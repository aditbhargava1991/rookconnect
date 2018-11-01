<?php
include ('../include.php');
include ('../database_connection_htg.php');
error_reporting(0);


if($_GET['fill'] == 'cross_software_approval') {
	$id = $_GET['status'];
	$dbc_conn = $_GET['dbc'];
	$dbc_cross = ${'dbc_cross_'.$dbc_conn};
	if(isset($_GET['disapprove'])) {
		$message = $_GET['name'];
		mysqli_query($dbc_cross,"UPDATE `newsboard` SET cross_software_approval = 'disapproved' WHERE newsboardid='$id'") or die(mysqli_error($dbc_cross));
	} else {
		mysqli_query($dbc_cross,"UPDATE `newsboard` SET cross_software_approval = '1' WHERE newsboardid='$id'") or die(mysqli_error($dbc_cross));
	}
}

if($_GET['fill'] == 'comment_reply') {
    $newsboardid = $_POST['newsboardid'];
    $contactid = $_POST['contactid'];
    $software_name = $_POST['software_name'];
    $title = $_POST['title'];
    $created_date = date('Y-m-d');
    $comment = $_POST['comment'];

    if ( !empty($software_name) ) {
        //Softwarewide
        $query = "INSERT INTO `newsboard_comments` (`newsboardid`, `contactid`, `software_name`, `created_date`, `comment`) VALUES('$newsboardid', '$contactid', '$software_name', '$created_date', '$comment')";
        mysqli_query($dbc_htg, $query);

        $subject = 'New Comment Added - '. $software_name;
        $body = '<h3>'.$subject.'</h3>
        Title: '. $title .'<br />
        Date: '. $created_date .'<br />
        Comment: '. $comment .'<br />';
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

				// Tag all the staff by default to the new post in a news board
				$newsboard_select = mysqli_fetch_assoc(mysqli_query($dbc, "select boardid from newsboard where newsboardid = $newsboardid"));
        $boardid = $newsboard_select['boardid'];
        $tag_newsboardid = $newsboardid;
        $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`,`email_address` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));
        foreach($staff_list as $staff) {
          $tag_staff_array[] = $staff['contactid'];
          $tag_staff_email_array[] = $staff['email_address'];
        }

				//foreach($tag_staff_email_array as $tag_staff_email) {
        try {
            $sender = '';
            $cc_emails = '';
            $bcc_emails = '';
            $title = "test";
            $user = $tag_staff_email_array;
            $subject = "New post -> $title has been added to the News Board.";
            $body = "New Post with Name $title has been added to the news Board. <br>";
            $body .= 'For more details <a href="'.'https://' . $_SERVER['SERVER_NAME'] . '/News Board/index.php">click here</a>';
            send_email($sender, $user, $cc_emails, $bcc_emails, $subject, html_entity_decode($body), '');
        } catch (Exception $e) {
            $error .= "Unable to send email: ".$e->getMessage()."\n";
        }
        //}
    } else {
        //Local
        $query = "INSERT INTO `newsboard_comments` (`newsboardid`, `contactid`, `created_date`, `comment`) VALUES('$newsboardid', '$contactid', '$created_date', '$comment')";
        mysqli_query($dbc, $query);

				// Tag all the staff by default to the new post in a news board
        $boardid = $newsboard_board;
        $tag_newsboardid = $newsboardid;
        $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`,`email_address` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));
        foreach($staff_list as $staff) {
          $tag_staff_array[] = $staff['contactid'];
          $tag_staff_email_array[] = $staff['email_address'];
        }

				//foreach($tag_staff_email_array as $tag_staff_email) {
        try {
            $sender = '';
            $cc_emails = '';
            $bcc_emails = '';
            $title = "test";
            $user = $tag_staff_email_array;
            $subject = "New post -> $title has been added to the News Board.";
            $body = "New Post with Name $title has been added to the news Board. <br>";
            $body .= 'For more details <a href="'.'https://' . $_SERVER['SERVER_NAME'] . '/News Board/index.php">click here</a>';
            send_email($sender, $user, $cc_emails, $bcc_emails, $subject, html_entity_decode($body), '');
        } catch (Exception $e) {
            $error .= "Unable to send email: ".$e->getMessage()."\n";
        }
        //}
    }
}

if ( $_GET['fill'] == 'delete_image' ) {
    $certuploadid = preg_replace('/[^0-9]/', '', $_GET['certuploadid']);
    $newsboard_type = filter_var($_GET['newsboard_type'], FILTER_SANITIZE_STRING);
    if ( !empty($certuploadid) ) {
        $query = "DELETE FROM `newsboard_uploads` WHERE `certuploadid`='$certuploadid'";
        if ( $newsboard_type == 'Softwarewide' ) {
            mysqli_query($dbc_htg, $query);
        } else {
            mysqli_query($dbc, $query);
        }
    }
}

if ( $_GET['fill'] == 'get_boards' ) {
    $newsboard_type = filter_var($_GET['newsboard_type'], FILTER_SANITIZE_STRING);
    $query = "SELECT * FROM `newsboard_boards` WHERE `deleted`=0 ORDER BY `board_name`";
    $html = '';

    if ( $newsboard_type == 'Softwarewide' ) {
        $result = mysqli_query($dbc_htg, $query);
    } else {
        $result = mysqli_query($dbc, $query);
    }

    if ( $result->num_rows > 0 ) {
        $html .= '<option value=""></option>';
        $html .= '<option value="NEW">Add New News Board</option>';
        while ( $row = mysqli_fetch_assoc($result) ) {
            $html .= '<option value="'. $row['boardid'] .'">'. $row['board_name'] .'</option>';
        }
    } else {
        $html .= '<option value="NEW">Add New News Board</option>';
    }

    echo $html;
}

if ( $_GET['fill'] == 'get_tags' ) {
    $boardid = preg_replace('/[^0-9]/', '', $_GET['boardid']);
    $newsboard_type = filter_var($_GET['newsboard_type'], FILTER_SANITIZE_STRING);
    $tags = '';
    $html = '';

    $query = "SELECT `tags` FROM `newsboard` WHERE `boardid`='$boardid'";
    if ( $newsboard_type == 'Softwarewide' ) {
        $result = mysqli_query($dbc_htg, $query);
    } else {
        $result = mysqli_query($dbc, $query);
    }

    if ( $result->num_rows > 0 ) {
        while ( $row = mysqli_fetch_assoc($result) ) {
            $tags .= $row['tags'] .',';
        }
        $tags = rtrim($tags, ',');
    }

    foreach ( explode(',', $tags) as $tag ) {
        $html .= '<div class="tags_class">';
            $html .= '<div class="col-xs-10 no-pad-left"><input name="tags[]" value="'. $tag .'" type="text" class="form-control" /></div>';
            $html .= '<div class="col-xs-2">';
                $html .= '<img class="inline-img pull-right cursor-hand" onclick="removeTag(this);" src="../img/remove.png" />';
                $html .= '<img class="inline-img pull-right cursor-hand" onclick="addTag(this);" src="../img/icons/ROOK-add-icon.png" />';
            $html .= '</div>';
        $html .= '</div>';
    }

    echo $html;
}

if ( $_GET['fill'] == 'delete_newsitem' ) {
    $newsboardid = preg_replace('/[^0-9]/', '', $_GET['newsboardid']);
    $newsboard_type = filter_var($_GET['newsboard_type'], FILTER_SANITIZE_STRING);

    $query = "UPDATE `newsboard` SET `deleted`=1 WHERE `newsboardid`='$newsboardid'";
    if ( $newsboard_type == 'Softwarewide' ) {
        mysqli_query($dbc_htg, $query);
    } else {
        mysqli_query($dbc, $query);
    }
}

if ( $_GET['fill'] == 'get_shared_staff' ) {
    $boardid = preg_replace('/[^0-9]/', '', $_GET['boardid']);
    $html = '';
    $start = '<div class="add_staff">
                        <div class="clearfix"></div>
                        <div class="col-xs-10 no-pad-left">
                            <select data-placeholder="Select a Staff..." id="contactid" name="contactid[]" class="chosen-select-deselect form-control">
                                <option value=""></option>';
    $end = '</select>
                </div>

                <div class="col-xs-2">
                    <img class="inline-img pull-right cursor-hand" onclick="removeStaff(this);" src="../img/remove.png" />
                    <img class="inline-img pull-right cursor-hand" onclick="addStaff(this);" src="../img/icons/ROOK-add-icon.png" />
                </div>
            </div>';

    $get_shared_staff = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `shared_staff` FROM `newsboard_boards` WHERE `boardid`='$boardid'"))['shared_staff'];
    if ( !empty($get_shared_staff) ) {
        foreach ( array_filter(explode(',', $get_shared_staff)) as $shared_staff ) {
            $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
            $html .= $start;
            foreach ( $staff_query as $staff ) {
                $html .= '<option '. ($staff['contactid'] == $shared_staff ? 'selected="selected"' : '') .' value="'. $staff['contactid'] .'">'. $staff['first_name'].' '.$staff['last_name'] .'</option>';
            }
            $html .= $end;
        }
    } else {
        $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
        $html .= $start;
        foreach ( $staff_query as $staff ) {
            $html .= '<option '. ($staff['contactid'] == $_SESSION['contactid'] ? 'selected="selected"' : '') .' value="'. $staff['contactid'] .'">'. $staff['first_name'].' '.$staff['last_name'] .'</option>';
        }
        $html .= $end;
    }

    echo $html;
}

if ( $_GET['fill'] == 'archive_board' ) {
    $boardid = preg_replace('/[^0-9]/', '', $_GET['boardid']);
    mysqli_query($dbc, "UPDATE `newsboard_boards` SET `deleted`=1 WHERE `boardid`='$boardid'");
    mysqli_query($dbc, "UPDATE `newsboard` SET `deleted`=1 WHERE `boardid`='$boardid'");
}

if ( $_GET['fill'] == 'load_panel' ) {
    $board = preg_replace('/[^0-9]/', '', $_GET['board']);
    $type = filter_var($_GET['type'], FILTER_SANITIZE_STRING);
    $dbc_news = ($type == 'sw') ? $dbc_htg : $dbc;
    $parent_id = ($type == 'sw') ? 'board_sw_' : 'board_';
    $tag_id = ($type == 'sw') ? 'tag_sw_' : 'tag_';
    $collapse_tag = ($type == 'sw') ? 'collapsetag_sw_' : 'collapsetag_';
    $html = '';

    $result = mysqli_query($dbc_news, "SELECT `tags` FROM `newsboard` WHERE `boardid`='$board' AND `deleted`=0");
    if ( $result->num_rows > 0 ) {
        while ( $row=mysqli_fetch_assoc($result) ) {
            foreach ( explode(',', $row['tags']) as $tag ) {
                $html .= '<div class="panel panel-default">';
                    $html .= '<div class="panel-heading mobile_tag_load">';
                        $html .= '<h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#'.$parent_id.$board.'" href="#'.$tag_id.strtolower(str_replace(' ', '', $tag)).'" class="double-pad-left">'.$tag.'<span class="glyphicon glyphicon-plus"></span></a>
                                </h4>';
                        $html .= '</div>';
                        $html .= '<div id="'.$tag_id.strtolower(str_replace(' ', '', $tag)).'" class="panel-collapse collapse">';
                            $html .= '<div id="'.$collapse_tag.$board.'" class="panel-body" data-board="'.$board.'" data-tag="'.$tag.'" data-type="'.$type.'" style="margin:-1px; padding:0;">
                                        Loading...
                                    </div>';
                        $html .= '</div>';
                    $html .= '</div>';
                $html .= '</div>';
            }
        }
    }

    echo $html;
}

if ( $_GET['fill'] == 'load_panel_data' ) {
    $html = include('news_list.php');
    echo $html;
}

if ( $_GET['fill'] == 'load_panel_item' ) {
    $html = include('news_item.php');
    echo $html;
}
if ( $_GET['fill'] == 'untag') {
	$boardid = $_GET['boardid'];
	$newsboardid = $_GET['newsboardid'];
	$contactid = $_GET['contactid'];
	$select_query = "SELECT staff from newsboard_tag where boardid=$boardid and newsboardid=$newsboardid";
	$select_newsboard = mysqli_fetch_assoc(mysqli_query($dbc, $select_query));
	if(!empty(array_filter($select_newsboard))) {
		$staff_array = explode(",", $select_newsboard['staff']);
		if(in_array($contactid, $staff_array)) {
			$position = array_search($contactid, $staff_array);
			unset($staff_array[$position]);
		}

		$update_staff = implode(",", $staff_array);
		$query = "UPDATE newsboard_tag set staff = '$update_staff' where boardid=$boardid and newsboardid=$newsboardid";
		mysqli_query($dbc, $query);
	}
}

?>
