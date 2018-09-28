<?php
/*
 * Add Task Board
 * Called from index.php
 */
include ('../include.php');
checkAuthorised('tasks');
error_reporting(0);

if (isset($_POST['add_tab'])) {
    $board_security = filter_var($_POST['board_security'],FILTER_SANITIZE_STRING);
    $board_security = ($board_security=='Shared') ? 'Company' : $board_security;

    if($board_security == 'Private') {
        $company_staff_sharing = ','.$_SESSION['contactid'].',';
    } else {
	    $company_staff_sharing = ','.$_SESSION['contactid'].','.implode(',',$_POST['company_staff_sharing']).',';
    }
    /* if ( empty($company_staff_sharing) ) {
        $company_staff_sharing = ','.$_SESSION['contactid'].',';
    } */
    $businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	$contactid = implode(',',$_POST['contactid']);
    $first_contact = current(explode(',', $contactid));

    if($board_security == 'Client') {
        $contact_cat = mysqli_fetch_array(mysqli_query($dbc, "SELECT category, name, first_name, last_name FROM contacts WHERE contactid='$first_contact'"));
        $board_name = $contact_cat['category'] .': '. ( !empty($contact_cat['name']) ? decryptIt($contact_cat['name']) .': ' : '' ) . ( !empty($contact_cat['first_name']) ? decryptIt($contact_cat['first_name']).' ' : '' ) .  ( !empty($contact_cat['last_name']) ? decryptIt($contact_cat['last_name']) : '' );
    } else {
        $board_name = filter_var($_POST['board_name'],FILTER_SANITIZE_STRING);
    }

    $task_path = filter_var($_POST['task_path'],FILTER_SANITIZE_STRING);
    $milestone_timeline = filter_var($_POST['milestone_timeline'],FILTER_SANITIZE_STRING);
    $software_url = filter_var($_POST['software_url'],FILTER_SANITIZE_STRING);
    $new_taskboardid = '';

    if($board_security == 'Community') {
        $ffm_rook_db = @mysqli_connect('localhost', 'ffm_rook_user', 'mIghtyLion!542', 'ffm_rook_db');

        if(empty($_POST['taskboardid'])) {
            $query_insert_config = "INSERT INTO `task_board` (`board_name`, `board_security`, `company_staff_sharing`, `businessid`, `contactid`, `task_path`, `milestone_timeline`, `software_url`) VALUES ('$board_name', '$board_security', '$company_staff_sharing', '$businessid', '$contactid', '$task_path', '$milestone_timeline', '$software_url')";
            $result_insert_config = mysqli_query($ffm_rook_db, $query_insert_config);
            $new_taskboardid = mysqli_insert_id($dbc);
        } else {
            $taskboardid = $_POST['taskboardid'];
            $query_update_vendor = "UPDATE `task_board` SET `board_name` = '$board_name', `board_security` = '$board_security',`company_staff_sharing` = '$company_staff_sharing', `businessid` = '$businessid', `contactid` = '$contactid', `task_path` = '$task_path', `milestone_timeline` = '$milestone_timeline', `software_url` = '$software_url' WHERE `taskboardid` = '$taskboardid'";
            $result_update_vendor = mysqli_query($ffm_rook_db, $query_update_vendor);
        }
    } else {
        if($board_name != '') {
            if(empty($_POST['taskboardid'])) {
                $query_insert_config = "INSERT INTO `task_board` (`board_name`, `board_security`, `company_staff_sharing`, `businessid`, `contactid`, `task_path`, `milestone_timeline`, `software_url`) VALUES ('$board_name', '$board_security', '$company_staff_sharing', '$businessid', '$contactid', '$task_path', '$milestone_timeline', '$software_url')";
                $result_insert_config = mysqli_query($dbc, $query_insert_config);
            } else {
                $taskboardid = $_POST['taskboardid'];
                $query_update_vendor = "UPDATE `task_board` SET `board_name` = '$board_name', `board_security` = '$board_security',`company_staff_sharing` = '$company_staff_sharing', `businessid` = '$businessid', `contactid` = '$contactid', `task_path` = '$task_path', `milestone_timeline` = '$milestone_timeline', `software_url` = '$software_url' WHERE `taskboardid` = '$taskboardid'";
                $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
            }
        }
    }
	foreach($_POST['path_id'] as $key => $path_id) {
		$name = $_POST['path_name'];
		$milestone_list = [];
		$timeline_list = [];
		$count = count($_POST['milestone_'.$path_id]);
		for($i = 0; $i < $count; $i++) {
			if($_POST['milestone_'.$path_id][$i] != '' || $_POST['timeline_'.$path_id][$i] != '') {
				$milestone_list[] = $_POST['milestone_'.$path_id][$i];
				$timeline_list[] = $_POST['timeline_'.$path_id][$i];
			}
		}
		$milestones = implode('#*#',$milestone_list);
		$timelines = implode('#*#',$timeline_list);
		if($name != '' && $milestones != '' && $timelines != '') {
			$query = "UPDATE `project_path_milestone` SET `milestone`='$milestones', `timeline`='$timelines' WHERE `project_path_milestone`='$path_id'";
			if($path_id == '') {
				$query = "INSERT INTO `project_path_milestone` (`project_path`, `milestone`, `timeline`) VALUES ('$name', '$milestones', '$timelines')";
			}
			mysqli_query($dbc, $query);
		}
	}

    $taskboardid = ( !empty($new_taskboardid) ) ? $new_taskboardid : $taskboardid;

    echo '<script type="text/javascript">window.location.replace("?category='.$taskboardid.'&tab='.$board_security.'");</script>';
}
else if(isset($_GET['deleteid']) && $_GET['deleteid'] != '') {
	$id = $_GET['deleteid'];
    $date_of_archival = date('Y-m-d');
	$query = "UPDATE `task_board` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `taskboardid`='$id'";
	mysqli_query($dbc, $query);
	echo "<script>alert('Task board deleted successfully!');</script>";
}
?>

<script type="text/javascript">
$(document).ready(function() {

	$("#project_path").change(function() {
		var project_path = $("#project_path").val();
		$.ajax({
			type: "GET",
			url: "task_ajax_all.php?fill=project_path_milestone&project_path="+project_path,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#milestone_timeline').html(response);
				$("#milestone_timeline").trigger("change.select2");
			}
		});
	});

    var taskboard = $("#taskboard").val();
    if(taskboard == 'Private') {
        $( "#businessid_show" ).hide();
        $( "#contactid_show" ).hide();
        $( "#company_staff_sharing" ).hide();
        $('.task-board-name').show();
    } else if(taskboard == 'Shared') {
        $( "#company_staff_sharing" ).show();
        $( "#businessid_show" ).hide();
        $( "#contactid_show" ).hide();
        $('.task-board-name').show();
    } else if(taskboard == 'Community') {
        $( "#businessid_show" ).show();
        $( "#contactid_show" ).show();
        $( "#company_staff_sharing" ).hide();
        $('.task-board-name').show();
    } else if(taskboard == 'Client') {
        $( "#businessid_show" ).show();
        $( "#contactid_show" ).show();
        $( "#company_staff_sharing" ).hide();
        $('.task-board-name').hide();
    }

    $("#board_security").change(function() {
        if ( $('#board_security option:selected').text()=='Private' ) {
            $('#company_staff_sharing').hide();
            $('#businessid_show').hide();
            $('#contactid_show').hide();
            $('.task-board-name').show();
        } else if ( $('#board_security option:selected').val()=='Client' ) {
            $('#company_staff_sharing').hide();
            $('#businessid_show').show();
            $('#contactid_show').show();
            $('.task-board-name').hide();
        } else if ( $('#board_security option:selected').val()=='Company' ) {
            $('#company_staff_sharing').show();
            $('#businessid_show').hide();
            $('#contactid_show').hide();
            $('.task-board-name').show();
        } else if ( $('#board_security option:selected').text()=='Community' ) {
            $( "#businessid_show" ).show();
            $( "#contactid_show" ).show();
            $( "#company_staff_sharing" ).hide();
            $('.task-board-name').show();
        } else {
            $('#businessid_show').hide();
            $('#contactid_show').hide();
            $('#company_staff_sharing').hide();
            $('.task-board-name').show();
        }

        /* if($("#board_security option:selected").val() == 'Client') {
            $( "#businessid_show").show();
            $( "#contactid_show").show();
            $( "#company_staff_sharing" ).hide();
            $('.task-board-name').hide();
        } */
    });

    $('.edit_btn').on('click', function(){
        var url = $(this).attr('href');
        window.parent.$('[name=edit_board]').off('load');
        window.parent.$('[name=edit_board]').attr('src', url);
    });

    $("#businessid").change(function() {
		var businessid = this.value;
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "task_ajax_all.php?fill=fillcontact&businessid="+businessid,
            dataType: "html",   //expect html to be returned
            success: function(response){
                var arr = response.split('*FFM*');
				$('#contactid').html(arr[0]);
				$("#contactid").trigger("change.select2");
            }
        });
	});
});
$(document).on('change', 'select[name="path_name"]', function() { changeLevel(this); });
function deleteLine(btn) {
	$(btn).parents('[name=path_line]').remove();
}
function addLine(src) {
	var clone=$('[name='+src+']').clone();
	clone.css('display','');
	clone.attr('name','path_line');
	$('[name='+src+']').before(clone);
	clone.find('[name^=milestone]').focus();
}
function changeLevel(sel) {
    var security_level = sel.value;
    //alert(security_level);
    $(".all_path").hide();
    $("#path_"+security_level).show();
}
</script>

<div class="container">
	<div class="row"><?php
        $task_tab_back = explode(",",get_config($dbc, 'task_tab'));
        $back_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>

        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
            $board_name = '';
            $board_security = '';
            $company_staff_sharing = '';
            $businessid = '';
            $contactid = '';
            $task_path = '';
            $milestone_timeline = '';
            $software_url = '';
            if(!empty($_GET['security'])) {
                $board_security = $_GET['security'];
            }

            if(!empty($_GET['taskboardid'])) {
                $taskboardid = $_GET['taskboardid'];
                $get_board = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM task_board WHERE taskboardid='$taskboardid' AND `deleted`=0"));

                $board_name = $get_board['board_name'];
                $board_security = $get_board['board_security'];
                $company_staff_sharing = $get_board['company_staff_sharing'];
                $businessid = $get_board['businessid'];
                $contactid = $get_board['contactid'];
                $task_path = $get_board['task_path'];
                $milestone_timeline = $get_board['milestone_timeline'];
                $software_url = $get_board['software_url']; ?>
                <input type="hidden" id="taskboard" name="taskboard" value="<?php echo $board_security ?>" />
                <input type="hidden" id="taskboardid" name="taskboardid" value="<?php echo $taskboardid ?>" /><?php
            } ?>

            <div class="pull-right"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
            <div class="clearfix"></div>

            <h3>Add Task Board</h3>
            <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label">Task Board Type:</label>
                <div class="col-sm-8">
                    <select name="board_security" id="board_security" data-placeholder="Choose a Task Board Type..." class="chosen-select-deselect form-control" width="380">
                        <option></option><?php
                        $all_board_types = mysqli_fetch_array(mysqli_query($dbc, "SELECT task_dashboard_tile FROM task_dashboard"));
                        foreach(explode(',', 'Private,'.$all_board_types['task_dashboard_tile']) as $board_type) {
                            $board_type = str_replace(' Tasks', '', $board_type);
                            if ( $board_type=='Client' ) {
                                $board_name = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's') : CONTACTS_TILE;
                            } elseif ( $board_type=='Company' ) {
                                $board_name = 'Shared';
                            } else {
                                $board_name = $board_type;
                            }
                            if ( $board_type!='Community' && $board_type!='Business' && $board_type!='Reporting' ) { ?>
                                <option <?= trim($get_board['board_security'])==trim($board_type) ? 'selected' : '' ?> value="<?= $board_type ?>"><?= $board_name ?></option><?php
                            }
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group task-board-name">
                <label for="fax_number"	class="col-sm-4	control-label">Task Board Name:</label>
                <div class="col-sm-8">
                  <input name="board_name" value="<?= $get_board['board_name'] ?>" type="text" class="form-control"/>
                </div>
            </div>

            <div class="form-group" id="company_staff_sharing" style="display:none;">
                <label for="fax_number"	class="col-sm-4	control-label">Share With Staff:</label>
                <div class="col-sm-8">
                    <select multiple name="company_staff_sharing[]" data-placeholder="Choose Staff..." class="chosen-select-deselect form-control" width="380">
                      <?php
                        $query1 = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." order by first_name");
                        while($row1 = mysqli_fetch_array($query1)) {
                            ?>
                            <option <?php if (strpos(','.$company_staff_sharing.',', ','.$row1['contactid'].',') !== FALSE) { echo  'selected="selected"'; } ?> value='<?php echo $row1['contactid']; ?>' ><?php echo decryptIt($row1['first_name']).' '.decryptIt($row1['last_name']); ?></option>
                        <?php }
                      ?>
                    </select>
                </div>
            </div>

            <div class="form-group" id="businessid_show" style="display: none;">
                <label for="first_name" class="col-sm-4 control-label text-right">Business:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Choose a Business..." name="businessid" id="businessid" class="chosen-select-deselect form-control1" width="380">
                        <option></option><?php
                        $query = mysqli_query($dbc,"SELECT name, contactid FROM contacts WHERE name != '' AND deleted=0 ORDER BY name");
                        while($row = mysqli_fetch_array($query)) {
                            if ($get_board['businessid'] == $row['contactid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group" id="contactid_show" style="display: none;">
                <label for="first_name" class="col-sm-4 control-label text-right">Contact:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Choose a Client..." multiple id="contactid" name="contactid[]" class="chosen-select-deselect form-control1" width="380">
                        <?php
                        $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE businessid = '$businessid' order by first_name");
                        while($row = mysqli_fetch_array($query)) {
                            if ($get_board['contactid'] == $row['contactid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='".$row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">Path Name:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Select a Task Path..." name="task_path" class="chosen-select-deselect form-control" width="380">
                        <option></option><?php
                        $query = mysqli_query($dbc,"SELECT project_path_milestone, project_path FROM project_path_milestone order by project_path");
                        while($row = mysqli_fetch_array($query)) { ?>
                            <option <?php if ($row['project_path_milestone'] == $get_board['task_path']) { echo " selected"; } ?> value='<?php echo  $row['project_path_milestone']; ?>' ><?php echo $row['project_path']; ?></option><?php
                        } ?>
                    </select>
                </div>
            </div>

            <?php $query = "SELECT * FROM project_path_milestone";
            $results = mysqli_query($dbc, $query);
            while($row = mysqli_fetch_array($results)) {
                $path_name = $row['project_path'];
                $milestones = explode('#*#',$row['milestone']);
                $timelines = explode('#*#',$row['timeline']);
                $count = count($milestones);
                $path_id = $row['project_path_milestone'];
                ?>

                <input type='hidden' name='path_id[]' value='<?php echo $path_id; ?>'>
                <!--
                <div class='form-group'><label class='col-sm-4 control-label'><a href="#" onclick="$('[name=path_<?php echo $path_id; ?>]').toggle(); return false;" data-toggle="tooltip" data-placement="top" title="Click here to edit this path.">
                    <span class="popover-examples list-inline" style="margin:0 2px 0 0;"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></span>
                    Path Name:</a></label>
                    <div class='col-sm-8'><input name='path_name[]' type='text' class='form-control' value='<?php echo $path_name; ?>'></div>
                </div>
                -->
                <div name="path_<?php echo $path_id; ?>" id="path_<?php echo $path_id; ?>"  style="display:none;" class="all_path">
                    <div class="form-group clearfix">
                        <label class="col-sm-3 text-center">Milestone</label>
                        <label class="col-sm-5 text-center">Timeline</label>
                    </div>
                    <?php for($i = 0; $i < $count; $i++) {
                        if($milestones[$i] != '' || $timelines[$i] != '') { ?>
                            <div class="form-group clearfix" name="path_line">
                                <div class="col-sm-3">
                                    <input name="milestone_<?php echo $path_id; ?>[]" id="milestone_<?php echo $path_id; ?>" value = "<?php echo $milestones[$i]; ?>" type="text" class="form-control milestone">
                                </div>
                                <div class="col-sm-5">
                                    <input name="timeline_<?php echo $path_id; ?>[]" value = "<?php echo $timelines[$i]; ?>" type="text" class="form-control">
                                </div>
                                <div class="col-sm-1 m-top-mbl" >
                                    <a href="#" onclick="deleteLine(this); return false;"class="btn brand-btn">Delete</a>
                                </div>
                            </div>

                        <?php } ?>
                    <?php } ?>
                    <div class="form-group clearfix" name="add_path_<?php echo $path_id; ?>" style="display:none;">
                        <div class="col-sm-3">
                            <input name="milestone_<?php echo $path_id; ?>[]" id="milestone_<?php echo $path_id; ?>" value = "" type="text" class="form-control milestone">
                        </div>
                        <div class="col-sm-5">
                            <input name="timeline_<?php echo $path_id; ?>[]" value = "" type="text" class="form-control">
                        </div>
                        <div class="col-sm-1 m-top-mbl" >
                            <a href="#" onclick="deleteLine(this); return false;"class="btn brand-btn">Delete</a>
                        </div>
                    </div>
                    <button class="btn brand-btn" onclick="addLine('add_path_<?php echo $path_id; ?>');return false;">Add Milestone</button>
                </div>
            <?php } ?>
            <div name="new_path" style="display:none;">
                <input type='hidden' name='path_id[]' value=''>
                <div class='form-group'>
                    <span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to edit this path."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <label class='col-sm-4 control-label'><a href="#" onclick="$('[name=path_]').toggle(); return false;">Task Path:</a></label>
                    <div class='col-sm-8'><input name='path_name' type='text' class='form-control' value=''></div>
                </div>
                <div name="path_">
                    <div class="form-group clearfix">
                        <label class="col-sm-3 text-center">Milestone</label>
                        <label class="col-sm-5 text-center">Timeline</label>
                    </div>
                    <div class="form-group clearfix" name="add_path_" style="display:none;">
                        <div class="col-sm-3">
                            <input name="milestone_[]" id="milestone_" value="" type="text" class="form-control milestone">
                        </div>
                        <div class="col-sm-5">
                            <input name="timeline_[]" value="" type="text" class="form-control">
                        </div>
                        <div class="col-sm-1 m-top-mbl" >
                            <a href="#" onclick="deleteLine(this); return false;"class="btn brand-btn">Delete</a>
                        </div>
                    </div>
                    <button class="btn brand-btn" onclick="addLine('add_path_');return false;">Add Milestone</button>
                </div>
            </div>
            <button class="btn brand-btn pull-right" onclick="$('[name=new_path]').show();addLine('add_path_');$(this).hide();return false;">Add New Path</button>
            <!--<button class="btn brand-btn pull-right" onclick="$('[name=path_name]').change();$(this).hide();return false;">Edit Current Path</button>-->

            <div class="clearfix"></div>

            <div class="form-group pull-right double-gap-top">
                <a href="<?= $back_url ?>" class="btn brand-btn pull-left">Cancel</a>
                <button	type="submit" name="add_tab" value="add_tab" class="btn brand-btn pull-right">Submit</button>
                <div class="clearfix"></div>
            </div>
        </form>
    </div><!-- .row -->
</div><!-- .container -->
