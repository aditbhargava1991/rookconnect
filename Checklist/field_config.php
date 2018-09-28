<?php
/*
Configuration - Choose which functionality you want for your software. Config email subject and body part for each functionality. Config Email Send Before days/month for patient treatment/booking confirmation and reminder.
*/
include ('../include.php');
error_reporting(0);
checkAuthorised('checklist');

$from=$_SERVER['HTTP_REFERER'];

if (isset($_POST['submit'])) {
    //Logo
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    $logo = htmlspecialchars($_FILES["logo"]["name"], ENT_QUOTES);
    $header = filter_var(htmlentities($_POST['pdf_header']),FILTER_SANITIZE_STRING);
	$colours = implode(',',$_POST['flag_colours']);
	$flag_names = implode('#*#',$_POST['flag_name']);

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM field_config_checklist"));
    if($get_field_config['configid'] > 0) {
		if($logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $logo;
		}
		move_uploaded_file($_FILES["logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `field_config_checklist` SET `pdf_logo` = '$logo_update', `pdf_header` = '$header', `flag_colours` = '$colours', `flag_names` = '$flag_names' WHERE `configid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
		move_uploaded_file($_FILES["logo"]["tmp_name"], "download/" . $_FILES["logo"]["name"]) ;
        $query_insert_config = "INSERT INTO `field_config_checklist` (`pdf_logo`, `pdf_header`, `flag_colours`, `flag_names`) VALUES ('$logo', '$header', '$colours', '$flag_names')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Logo
	
	//Quick Actions
	$quick_action_icons = filter_var(implode(',',$_POST['quick_action_icons']),FILTER_SANITIZE_STRING);
	set_config($dbc, 'quick_action_icons', $quick_action_icons);
	//Quick Actions
	
	//Tab Config
	$tab_config = implode(',',$_POST['tab_config']);
	$result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'checklist_tabs_" . $_SESSION['contactid'] . "' FROM (SELECT COUNT(*) numrows FROM `general_configuration` WHERE `name`='checklist_tabs_" . $_SESSION['contactid'] . "') current_config WHERE numrows=0");
	$sql_update = "UPDATE `general_configuration` SET `value`='$tab_config' WHERE `name`='checklist_tabs_" . $_SESSION['contactid'] . "'";
	$result = mysqli_query($dbc, $sql_update);
	//Tab Config

    echo '<script type="text/javascript"> window.location.replace("'.$_POST['referer'].'"); </script>';
}
?>
</head>
<body>

<?php include ('../navigation.php');
$get_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_checklist`"));
$logo = $get_config['pdf_logo'];
$pdf_header = $get_config['pdf_header'];
$flag_colours = $get_config['flag_colours'];
$flag_names = explode('#*#', $get_config['flag_names']);
$tab_config = get_config($dbc, 'checklist_tabs_' . $_SESSION['contactid']); ?>

<div class="container">
<div class="row">
<h1>Checklist Settings</h1>
<a href="<?php echo $from; ?>" class="btn brand-btn">Back to Dashboard</a>
<br />
<br />
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<input type="hidden" name="referer" value="<?php echo $from; ?>" />
    <div class="panel-group" id="accordion2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tabs" >
                        Checklist Tabs<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_tabs" class="panel-collapse collapse">
                <div class="panel-body">
					<script>
					$(document).ready(function() {
						$('[name="tab_main[]"]').change(function() {
							if(!this.checked) {
								$('[name="tab_config[]"][value^='+this.value+'_]').removeAttr('checked');
							}
							else {
								$('[name="tab_config[]"][value^='+this.value+'_]').attr('checked','checked');
							}
						});
					});
					</script>
					<h4>Select the checklist categories you wish to use.</h4>
                    <div class="form-group">
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'favourites') !== FALSE ? 'checked' : '') ?> value="favourites" style="height:1.5em; width:1.5em;"> Favourite Checklists</label>
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'private') !== FALSE ? 'checked' : '') ?> value="private" style="height:1.5em; width:1.5em;"> Private Checklists</label>
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'shared') !== FALSE ? 'checked' : '') ?> value="shared" style="height:1.5em; width:1.5em;"> Shared Checklists</label>
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'project') !== FALSE ? 'checked' : '') ?> value="project" style="height:1.5em; width:1.5em;"> Project Checklists</label>
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'company') !== FALSE ? 'checked' : '') ?> value="company" style="height:1.5em; width:1.5em;"> Company Checklists</label>
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'ongoing') !== FALSE ? 'checked' : '') ?> value="ongoing" style="height:1.5em; width:1.5em;"> Ongoing Checklists</label>
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'daily') !== FALSE ? 'checked' : '') ?> value="daily" style="height:1.5em; width:1.5em;"> Daily Checklists</label>
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'weekly') !== FALSE ? 'checked' : '') ?> value="weekly" style="height:1.5em; width:1.5em;"> Weekly Checklists</label>
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'monthly') !== FALSE ? 'checked' : '') ?> value="monthly" style="height:1.5em; width:1.5em;"> Monthly Checklists</label>
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'inventory') !== FALSE ? 'checked' : '') ?> value="inventory" style="height:1.5em; width:1.5em;"> Inventory Checklists</label>
                        <label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'equipment') !== FALSE ? 'checked' : '') ?> value="equipment" style="height:1.5em; width:1.5em;"> Equipment Checklists</label>
						<label class="form-checkbox"><input type="checkbox" name="tab_config[]" <?= (strpos($tab_config,'reporting') !== FALSE ? 'checked' : '') ?> value="reporting" style="height:1.5em; width:1.5em;"> Reporting</label>
                    </div>
                </div>
            </div>
        </div>
		
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
                        Logo and Header for PDF Export<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_field" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="form-group">
                    <label for="file[]" class="col-sm-4 control-label">Logo<span class="popover-examples list-inline">&nbsp;
                    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
                    </span>:</label>
                    <div class="col-sm-8">
                    <?php if($logo != '' && file_exists('download/'.$logo)) {
                        echo '<a href="download/'.$logo.'" target="_blank">View</a>';
                        ?>
                        <input type="hidden" name="logo_file" value="<?php echo $logo; ?>" />
                        <input name="logo" type="file" data-filename-placement="inside" class="form-control" />
                      <?php } else { ?>
                      <input name="logo" type="file" data-filename-placement="inside" class="form-control" />
                      <?php } ?>
                    </div>
                    </div>
                    <div class="form-group">
                        <label for="office_country" class="col-sm-4 control-label">Header Info:<br><em>(e.g. - company address, phone, email, etc.)</em></label>
                        <div class="col-sm-8">
                            <textarea name="pdf_header" rows="3" cols="50" class="form-control"><?php echo $pdf_header; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_actions" >
                        Quick Action Settings<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_actions" class="panel-collapse collapse">
                <div class="panel-body">
					<div class="form-group">
						<label class="col-sm-4 control-label">Enable Quick Action Icons</label>
						<div class="col-sm-8">
							<?php $quick_action_icons = explode(',',get_config($dbc, 'quick_action_icons')); ?>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('edit',$quick_action_icons) ? 'checked' : '' ?> value="edit"> <img class="inline-img" src="../img/icons/ROOK-edit-icon.png"> Edit</label>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('sync',$quick_action_icons) ? 'checked' : '' ?> value="sync"> <img class="inline-img" src="../img/icons/ROOK-sync-icon.png"> Sync External Path</label>
							<label class="form-checkbox" onClick="$('[name^=quick_action_icons][value=flag_manual]').removeAttr('checked');" ><input type="checkbox" name="quick_action_icons[]" <?= !in_array('flag_manual',$quick_action_icons) && in_array('flag',$quick_action_icons) ? 'checked' : '' ?> value="flag"> <img class="inline-img" src="../img/icons/ROOK-flag-icon.png"> Flag</label>
							<label onClick="$('[name^=quick_action_icons][value=flag]').removeAttr('checked');" class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('flag_manual',$quick_action_icons) ? 'checked' : '' ?> value="flag_manual"> <img class="inline-img" src="../img/icons/ROOK-flag-icon.png"> Manually Flag with Label</label>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('reply',$quick_action_icons) ? 'checked' : '' ?> value="reply"> <img class="inline-img" src="../img/icons/ROOK-reply-icon.png"> Reply</label>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('attach',$quick_action_icons) ? 'checked' : '' ?> value="attach"> <img class="inline-img" src="../img/icons/ROOK-attachment-icon.png"> Attach</label>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('alert',$quick_action_icons) ? 'checked' : '' ?> value="alert"> <img class="inline-img" src="../img/icons/ROOK-alert-icon.png"> Alerts</label>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('email',$quick_action_icons) ? 'checked' : '' ?> value="email"> <img class="inline-img" src="../img/icons/ROOK-email-icon.png"> Email</label>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('reminder',$quick_action_icons) ? 'checked' : '' ?> value="reminder"> <img class="inline-img" src="../img/icons/ROOK-reminder-icon.png"> Reminders</label>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('time',$quick_action_icons) ? 'checked' : '' ?> value="time"> <img class="inline-img" src="../img/icons/ROOK-timer-icon.png"> Add Time</label>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('timer',$quick_action_icons) ? 'checked' : '' ?> value="timer"> <img class="inline-img" src="../img/icons/ROOK-timer2-icon.png"> Track Time</label>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('archive',$quick_action_icons) ? 'checked' : '' ?> value="archive"> <img class="inline-img" src="../img/icons/ROOK-trash-icon.png"> Archive</label>
							<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('hide_all',$quick_action_icons) ? 'checked' : '' ?> value="hide_all" onclick="$('[name^=quick_action_icons]').not('[value=hide_all]').removeAttr('checked');"> Disable All</label>
						</div>
					</div>
                    <div class="form-group">
						<label for="file[]" class="col-sm-4 control-label">Flag Colours to Use<span class="popover-examples list-inline">&nbsp;
						<a  data-toggle="tooltip" data-placement="top" title="The selected colours will be cycled through when you flag an entry."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
						</span>:</label>
						<div class="col-sm-8">
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FF6060') !== false ? 'checked' : ''); ?> value="FF6060" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FF6060; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[0]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'DEBAA6') !== false ? 'checked' : ''); ?> value="DEBAA6" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #DEBAA6; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[1]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FFAEC9') !== false ? 'checked' : ''); ?> value="FFAEC9" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FFAEC9; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[2]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FFC90E') !== false ? 'checked' : ''); ?> value="FFC90E" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FFC90E; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[3]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'EFE4B0') !== false ? 'checked' : ''); ?> value="EFE4B0" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #EFE4B0; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[4]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'B5E61D') !== false ? 'checked' : ''); ?> value="B5E61D" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #B5E61D; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[5]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, '99D9EA') !== false ? 'checked' : ''); ?> value="99D9EA" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #99D9EA; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[6]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'D0E1F7') !== false ? 'checked' : ''); ?> value="D0E1F7" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #D0E1F7; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[7]; ?>" class="form-control"></div><div class="clearfix"></div>
							<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'C8BFE7') !== false ? 'checked' : ''); ?> value="C8BFE7" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
							<div style="border: 1px solid black; border-radius: 0.25em; background-color: #C8BFE7; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
							<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[8]; ?>" class="form-control"></div><div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-6">
			<a href="<?php echo $from; ?>" class="btn brand-btn btn-lg">Back</a>
			<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
		</div>
		<div class="col-sm-6">
			<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
		</div>
	</div>
</form>
</div>
</div>
<?php include('../footer.php'); ?>