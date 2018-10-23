<?php
/*
 * This should be called from everywhere there is a quick action to add notes/comments/replies
 * Accept the Tile name in a $_GET['tile']
 */
 
include_once('include.php');
include_once('database_connection_htg.php');
checkAuthorised();
$html = '';

if(isset($_POST['submit'])) {
	$contactid = $_SESSION['contactid'];
	$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
	$id = preg_replace('/[^0-9]/', '', $_POST['id']);
	$note = filter_var(htmlentities($_POST['note'],FILTER_SANITIZE_STRING));
    $date = date('Y-m-d');
    $datetime = date('Y-d-m, g:i:s A');
    $software_name = '';
	$error = '';
    
    switch ($tile) {
        case 'tasks':
            $query = "INSERT INTO `task_comments` (`tasklistid`, `created_by`, `created_date`, `comment`) VALUES ('$id', '$contactid', '$date', '$note')";
            break;
        
        case 'tickets':
            /* $comment_type reflects Ticket/edit_ticket_tab.php */
            $value_config = get_field_config($dbc, 'tickets');
            if (strpos($value_config, ','."Client Log".',') !== FALSE) {
                $comment_type = 'client_log';
            } elseif (strpos($value_config, ','."Addendum".',') !== FALSE) {
                $comment_type = 'addendum';
            } elseif (strpos($value_config, ','."Debrief".',') !== FALSE) {
                $comment_type = 'debrief';
            } elseif (strpos($value_config, ','."Member Log Notes".',') !== FALSE) {
                $comment_type = 'member_note';
            } else {
                $comment_type = 'note';
            }
            $query = "INSERT INTO `ticket_comment` (`ticketid`, `comment`, `created_by`, `created_date`, `type`) VALUES ('$id', '$note', '$contactid', '$date', '$comment_type')";
            break;
        
        case 'projects';
            $query = "INSERT INTO `project_comment` (`projectid`, `comment`, `created_date`, `created_by`, `type`) VALUES ('$id', '$note', '$date', '$contactid', 'project_note')";
            break;
        
        case 'checklists':
            $note = '<p>'. $note . ' (Reply added by '. decryptIt($_SESSION['first_name']) .' '. decryptIt($_SESSION['last_name']) .' [PROFILE '. $_SESSION['contactid'] .'] at '.$datetime .')';
            $query = "UPDATE `checklist_name` SET `checklist`=CONCAT(`checklist`,'$note') WHERE `checklistnameid`='$id'";
            $checklistnameid = $id;
            break;
        
        case 'sales':
            $query = "INSERT INTO `sales_notes` (`salesid`, `comment`, `created_date`, `created_by`) VALUES('$id', '$note', '$date', '$contactid')";
            $salesid = $id;
            break;
        
        case 'newsboard':
            $software_name = filter_var($_POST['software_name'], FILTER_SANITIZE_STRING);
            if ( !empty($software_name) ) {
                $dbc = $dbc_htg;
                $query = "INSERT INTO `newsboard_comments` (`newsboardid`, `contactid`, `software_name`, `created_date`, `comment`) VALUES('$id', '$contactid', '$software_name', '$date', '$note')";
            } else {
                $query = "INSERT INTO `newsboard_comments` (`newsboardid`, `contactid`, `created_date`, `comment`) VALUES('$id', '$contactid', '$date', '$note')";
            }
            break;

        case 'planner':
            $note_exists = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `daysheet_notepad` WHERE `contactid` = '$id' AND `date` = '$date'"));
            if(!empty($note_exists)) {
                $query = "UPDATE `daysheet_notepad` SET `notes` = CONCAT(`notes`,'".htmlentities('<br />')."','$note') WHERE `contactid` = '$id' AND `date` = '$date'";
            } else {
                $query = "INSERT INTO `daysheet_notepad` (`contactid`, `notes`, `date`) VALUES ('$id', '$note', '$date')";
            }
            break;

        case 'incident_report_approvals':
            $query = "INSERT INTO `incident_report_comment` (`type`, `incidentreportid`, `comment`, `created_date`, `created_by`, `seen_by`) VALUES ('approval_note', '$id', '$note', '$date', '$contactid', ',$contactid')";
            break;
        
        default:
            break;
    }
	
    if ( !empty($query) ) {
        if ( mysqli_query($dbc, $query) ) {
            if ( $tile == 'tickets' || $tile == 'projects' ) {
                echo '<script type="text/javascript">alert("Note added successfully");</script>';
            } else {
                echo '<script type="text/javascript">alert("Note added successfully"); window.parent.location.reload(true);</script>';
            }
            
            if ( $checklistnameid ) {
                $before_change = capture_before_change($dbc, 'checklist_name', 'checklist', 'checklistnameid', $checklistnameid);
                $item_name = explode('&lt;p&gt;', mysql_fetch_array(mysqli_query($dbc, "SELECT `checklist` FROM `checklist_name` WHERE `checklistnameid`='$checklistnameid'"))['checklist']);
                $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Replied to Checklist Item <b>'.$item_name[0].'</b> on '. $date;
                $start_word = strpos($report, "Updated");
                $end_word = strpos($report, " on");
                $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
                add_update_history($dbc, 'checklist_history', $history, '', $before_change);
            }
            
            if ( $salesid ) {
                add_update_history($dbc, 'sales_history', 'Sales note added.', '', '', $salesid);
            }
            
            if ( $tile == 'newsboard' && !empty($software_name) ) {
                $subject = 'New Comment Added - '. $software_name;
                $body = '<h3>'.$subject.'</h3>
                Date: '. $created_date .'<br />
                Comment: '. $note .'<br />';
                $error = '';
                $ffm_recepient = mysqli_fetch_assoc(mysqli_query($dbc_htg, "SELECT `comment_reply_recepient_email` FROM `newsboard_config`"))['comment_reply_recepient_email'];
                
                if ( empty($ffm_recepient) ) {
                    $ffm_recepient = 'info@rookconnect.com';
                }
                
                try {
                    send_email('', $ffm_recepient, '', '', $subject, html_entity_decode($body), '');
                } catch (Exception $e) {
                    $error .= "Unable to send email: ".$e->getMessage()."\n";
                }
            }
        } else {
            echo '<script type="text/javascript">alert("An error occured. Please try again later. '. mysqli_error($dbc) .'");</script>';
        }
    }
	
}

switch(filter_var($_GET['tile'], FILTER_SANITIZE_STRING)) {
    case 'tasks':
        $tile = 'tasks';
        $id = preg_replace('/[^0-9]/', '', $_GET['id']);
        $query = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `comment` FROM `task_comments` WHERE `tasklistid`='$id' AND `deleted`=0");
        if ( $query->num_rows > 0 ) {
            $html .= '<div class="form-group clearfix full-width">
                <div class="col-sm-12">';
                    while ( $row=mysqli_fetch_assoc($query) ) {
                        $html .= '<div class="note_block row">
                            <div class="col-xs-1">'. profile_id($dbc, $row['created_by'], false) .'</div>
                            <div class="col-xs-11">
                                <div>'. html_entity_decode($row['comment']) .'</div>
                                <div><em>Added by '. get_contact($dbc, $row['created_by']) .' on '. $row['created_date'] .'</em></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="margin-vertical" />';
                    }
                $html .= '</div>
                <div class="clearfix"></div>
            </div>';
        }
        break;
    
    case 'tickets':
        $tile = 'tickets';
        $id = preg_replace('/[^0-9]/', '', $_GET['id']);
        $query = mysqli_query($dbc, "SELECT `comment`, `created_date`, `created_by` FROM `ticket_comment` WHERE ticketid='$id' AND `deleted`=0");
        if ( $query->num_rows > 0 ) {
            $html .= '<div class="form-group clearfix full-width">
                <div class="col-sm-12">';
                    while ( $row=mysqli_fetch_assoc($query) ) {
                        $html .= '<div class="note_block row">
                            <div class="col-xs-1">'. profile_id($dbc, $row['created_by'], false) .'</div>
                            <div class="col-xs-11">
                                <div>'. html_entity_decode($row['comment']) .'</div>
                                <div><em>Added by '. get_contact($dbc, $row['created_by']) .' on '. $row['created_date'] .'</em></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="margin-vertical" />';
                    }
                $html .= '</div>
                <div class="clearfix"></div>
            </div>';
        }
        break;
	
    case 'projects':
        $tile = 'projects';
        $id = preg_replace('/[^0-9]/', '', $_GET['id']);
        $query = mysqli_query($dbc, "SELECT `comment`, `created_date`, `created_by` FROM `project_comment` WHERE `projectid`='$id'");
        if ( $query->num_rows > 0 ) {
            $html .= '<div class="form-group clearfix full-width">
                <div class="col-sm-12">';
                    while ( $row=mysqli_fetch_assoc($query) ) {
                        $html .= '<div class="note_block row">
                            <div class="col-xs-1">'. profile_id($dbc, $row['created_by'], false) .'</div>
                            <div class="col-xs-11">
                                <div>'. html_entity_decode($row['comment']) .'</div>
                                <div><em>Added by '. get_contact($dbc, $row['created_by']) .' on '. $row['created_date'] .'</em></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="margin-vertical" />';
                    }
                $html .= '</div>
                <div class="clearfix"></div>
            </div>';
        }
		break;
    
    case 'checklists':
        $tile = 'checklists';
        $id = preg_replace('/[^0-9]/', '', $_GET['id']);
        $query = mysqli_query($dbc, "SELECT `checklistnameid`, `checklist` FROM `checklist_name` WHERE `checklistnameid`='$id' AND `deleted`=0");
        if ( $query->num_rows > 0 ) {
            $html .= '<div class="form-group clearfix full-width">
                <div class="col-sm-12">';
                    while ( $row=mysqli_fetch_assoc($query) ) {
                        $html .= '<div class="note_block row">
                            <div class="col-xs-12">'. '#'.$row['checklistnameid'].': '.preg_replace_callback('/\[PROFILE ([0-9]+)\]/',profile_callback,html_entity_decode($row['checklist'])) .'</div>
                        </div>
                        <hr class="margin-vertical" />';
                    }
                $html .= '</div>
                <div class="clearfix"></div>
            </div>';
        }
        break;
        
    case 'sales':
        $tile = 'sales';
        $id = preg_replace('/[^0-9]/', '', $_GET['id']);
        $query = mysqli_query($dbc, "SELECT `comment`, `created_date`, `created_by` FROM `sales_notes` WHERE `salesid`='$id'");
        if ( $query->num_rows > 0 ) {
            $html .= '<div class="form-group clearfix full-width">
                <div class="col-sm-12">';
                    while ( $row=mysqli_fetch_assoc($query) ) {
                        $html .= '<div class="note_block row">
                            <div class="col-xs-1">'. profile_id($dbc, $row['created_by'], false) .'</div>
                            <div class="col-xs-11">
                                <div>'. html_entity_decode($row['comment']) .'</div>
                                <div><em>Added by '. get_contact($dbc, $row['created_by']) .' on '. $row['created_date'] .'</em></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="margin-vertical" />';
                    }
                $html .= '</div>
                <div class="clearfix"></div>
            </div>';
        }
        break;
        
    case 'newsboard':
        $tile = 'newsboard';
        $id = preg_replace('/[^0-9]/', '', $_GET['id']);
        $type = filter_var($_GET['type'],FILTER_SANITIZE_STRING);
        $dbc_news = ($type == 'sw') ? $dbc_htg : $dbc;
        $sw_query = '';
        $software_name = $_SERVER['SERVER_NAME'];
        if ( $type == 'sw' ) {
            $sw_query = "AND `software_name`='$software_name'";
            $html .= '<input type="hidden" name="software_name" value="'. $software_name .'" />';
        } else {
            $html .= '<input type="hidden" name="software_name" value="" />';
        }
        $query = mysqli_query($dbc_news, "SELECT `nbcommentid`, `contactid`, `created_date`, `comment` FROM `newsboard_comments` WHERE `newsboardid`='$id' AND `deleted`=0 $sw_query ORDER BY `nbcommentid` DESC");
        if ( $query->num_rows > 0 ) {
            $html .= '<div class="form-group clearfix full-width">
                <div class="col-sm-12">';
                    while ( $row = mysqli_fetch_assoc($query) ) {
                        $html .= '<div class="note_block row">
                            <div class="col-xs-1">'. profile_id($dbc, $row['contactid'], false) .'</div>
                            <div class="col-xs-11">
                                <div>'. html_entity_decode($row['comment']) .'</div>
                                <div><em>Added by '. get_contact($dbc, $row['contactid']) .' on '. $row['created_date'] .'</em></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="margin-vertical" />';
                    }
        }
        break;

    case 'planner':
        $tile = 'planner';
        $id = preg_replace('/[^0-9]/', '', $_GET['id']);
        $query = mysqli_query($dbc, "SELECT `notes` FROM `daysheet_notepad` WHERE `contactid` = '$id' AND `date` = '".date('Y-m-d')."'");
        if ( $query->num_rows > 0 ) {
            $html .= '<div class="form-group clearfix full-width">
                <h4>Notes - '.date('Y-m-d').'</h4>
                <div class="col-sm-12">';
                    while ( $row=mysqli_fetch_assoc($query) ) {
                        $html .= '<div class="note_block row">
                            <div class="col-xs-12">
                                <div>'. html_entity_decode($row['notes']) .'</div>
                            </div>
                            <div class="clearfix"></div>
                        </div>';
                    }
                $html .= '</div>
                <div class="clearfix"></div>
            </div>';
        }
        break;

    case 'incident_report_approvals':
        $tile = 'incident_report_approvals';
        $id = preg_replace('/[^0-9]/', '', $_GET['id']);
        $query = mysqli_query($dbc, "SELECT * FROM `incident_report_comment` WHERE `incidentreportid` = '$id' AND `deleted` = 0 AND `type` = 'approval_note'");
        if ( $query->num_rows > 0 ) {
            $html .= '<div class="form-group clearfix full-width">
                <div class="col-sm-12">';
                    while ( $row=mysqli_fetch_assoc($query) ) {
                        if(strpos(','.$row['seen_by'].',', ','.$_SESSION['contactid'].',') === FALSE) {
                            mysqli_query($dbc, "UPDATE `incident_report_comment` SET `seen_by` = CONCAT(IFNULL(`seen_by`,''),',".$_SESSION['contactid']."') WHERE `id` = '".$row['id']."'");
                        }
                        $html .= '<div class="note_block row">
                            <div class="col-xs-1">'. profile_id($dbc, $row['created_by'], false) .'</div>
                            <div class="col-xs-11">
                                <div>'. html_entity_decode($row['comment']) .'</div>
                                <div><em>Added by '. get_contact($dbc, $row['created_by']) .' on '. $row['created_date'] .'</em></div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="margin-vertical" />';
                    }
                $html .= '</div>
                <div class="clearfix"></div>
            </div>';
        }
        break;
    
    default:
        break;
} ?>

<div class="container">
	<div class="row">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        	<h3 class="inline">Add Note</h3>
            <div class="pull-right gap-top"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
            <div class="clearfix"></div>
            <hr />
            
            <input type="hidden" name="tile" value="<?= $tile ?>" />
            <input type="hidden" name="id" value="<?= $id ?>" />

        	<div class="form-group">
        		<div class="col-sm-12"><?= $html ?></div>
        	</div>
        	<div class="form-group">
        		<label class="col-sm-12">Note:</label>
        		<div class="col-sm-12"><textarea name="note" class="form-control noMceEditor"></textarea></div>
        	</div>
        	<div class="form-group pull-right">
        		<a href="" class="btn brand-btn">Back</a>
        		<button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
        	</div>
        </form>
    </div>
</div>