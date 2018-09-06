<?php /* Field Configuration for Site Work Orders */
include ('../include.php');
checkAuthorised('site_work_orders');
error_reporting(0);

if (isset($_POST['submit'])) {
	// Site Team Leads
	$staff_leads = implode(',',$_POST['team_leads']);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'site_work_order_leads' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='site_work_order_leads') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$staff_leads' WHERE `name`='site_work_order_leads'");

	// Driving Log Equipment Category
	$site_log_equip_cat = implode(',',$_POST['site_log_equip_cat']);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'site_log_equip_cat' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='site_log_equip_cat') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$site_log_equip_cat' WHERE `name`='site_log_equip_cat'");

	// Maximum Timer Time
	$max_timer = explode(':',$_POST['max_timer']);
	$max_timer = ($max_timer[0] * 3600) + ($max_timer[1] * 60);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'max_timer' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='max_timer') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$max_timer' WHERE `name`='max_timer'");

	// General Flag Colours
	$colours = [];
	foreach($_POST['flag_colours'] as $colour) {
		$colours[] = $colour.'*#*'.$_POST['flag_name_'.$colour];
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'general_flag_colours' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='general_flag_colours') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode('#*#',$colours)."' WHERE `name`='general_flag_colours'");

	$task_groups = [];
	foreach($_POST as $key => $value) {
		if(substr($key,0,10) == 'taskgroup_') {
			$task_groups[substr($key,10)] = implode('*#*',$value);
		}
	}
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'site_work_order_tasks' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='site_work_order_tasks') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".implode('#*#',$task_groups)."' WHERE `name`='site_work_order_tasks'");

	// Site Work Order Access
	$site_work_order_staff_groups = implode(',', $_POST['site_work_order_staff_groups']);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'site_work_order_staff_groups' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='site_work_order_staff_groups') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$site_work_order_staff_groups' WHERE `name`='site_work_order_staff_groups'");

	// Site Work Order Accordion Read-Only Settings
	$accordion_list = ['who' => 'Who', 'staff' => 'Staff & Crew', 'services' => 'Services', 'equip' => 'Equipment', 'material' => 'Materials', 'where' => 'Where', 'when' => 'When', 'docs' => 'Support Documents', 'checklist' => 'Site Checklist', 'pos' => 'Purchase Orders', 'comments' => 'Comments', 'safety' => 'Safety Checklist', 'addendum' => 'Addendum'];

	foreach ($accordion_list as $accordion => $accordion_title) {
		$swo_readonly = implode(',', $_POST['swo_readonly_'.$accordion]);
		$swo_readonly = trim($swo_readonly, ',');
		mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'swo_readonly_".$accordion."' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='swo_readonly_".$accordion."') num WHERE num.rows=0");
		mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$swo_readonly."' WHERE `name`='swo_readonly_".$accordion."'");
	}

	// Temporary Worker Orientation Form Settings
	$temp_worker_form_enabled = $_POST['temp_worker_form_enabled'];
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'swo_temp_worker_form' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='swo_temp_worker_form') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$temp_worker_form_enabled' WHERE `name`='swo_temp_worker_form'");

	// Display All Work Orders Including Ones Without Work Orders
	$swo_display_all_sites = $_POST['swo_display_all_sites'];
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'swo_display_all_sites' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='swo_display_all_sites') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$swo_display_all_sites' WHERE `name`='swo_display_all_sites'");

	// Include Staff In Equipment Transfer Tile
	$equipment_transfer_staff = $_POST['equipment_transfer_staff'];
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_transfer_staff' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_transfer_staff') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_transfer_staff' WHERE `name`='equipment_transfer_staff'");

    echo '<script type="text/javascript"> window.location.replace("site_work_orders.php?tab='.$_GET['tab'].'"); </script>';
}

// Variables
?>
</head>
<body>

<?php include ('../navigation.php');
$staff_leads = explode(',', get_config($dbc, 'site_work_order_leads'));
$site_work_order_staff_groups = get_config($dbc, 'site_work_order_staff_groups'); ?>

<div class="container">
<div class="row">
<h1>Site Work Order Settings</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="site_work_orders.php?tab=<?= $_GET['tab'] ?>" class="btn brand-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>-->

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<div class="panel-group" id="accordion2">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_config" >
					Company Team Leads<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_config" class="panel-collapse collapse">
			<div class="panel-body">
				<label class="col-sm-4 control-label">Team Leads</label>
				<div class="col-sm-8">
					<select name="team_leads[]" multiple class="chosen-select-deselect form-control" data-placeholder="Select Team Leads">
						<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category`='Staff' AND `deleted`=0 AND `status`=1"), MYSQLI_ASSOC));
						foreach($staff_list as $id) {
							echo "<option ".(in_array($id, $staff_leads) ? 'selected' : '')." value='$id'>".get_contact($dbc, $id)."</option>";
						} ?>
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_driving_log" >
					Driving Log Settings<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_driving_log" class="panel-collapse collapse">
			<div class="panel-body">
				<label class="col-sm-4 control-label">Driving Log Equipment Category</label>
				<div class="col-sm-8">
					<select name="site_log_equip_cat[]" multiple class="chosen-select-deselect form-control" data-placeholder="Select Equipment Category">
						<?php $cat_list = explode(',', get_config($dbc, 'equipment_tabs'));
						$site_log_equip_cat = explode(',',get_config($dbc, 'site_log_equip_cat'));
						foreach($cat_list as $equip_cat) {
							echo "<option ".(in_array($equip_cat,$site_log_equip_cat) ? 'selected' : '')." value='$equip_cat'>$equip_cat</option>";
						} ?>
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tasks" >
					Site Work Order <?= TASK_TILE ?><span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_tasks" class="panel-collapse collapse">
			<div class="panel-body">
				<div class="col-sm-12"><div class="form-group"><label class="col-sm-4 control-label">Who can view Site Work Orders: </label><div class="col-sm-8">
					<select multiple data-placeholder="Select Staff Category" name="site_work_order_staff_groups[]" class="chosen-select-deselect form-control">
						<?php $query = mysqli_query($dbc, "SELECT DISTINCT `position` FROM `contacts` WHERE `category` = 'Staff' ORDER BY `position`");
						while ($row = mysqli_fetch_array($query)) {
							echo "<option ".(strpos(','.$site_work_order_staff_groups.',', ','.$row['position'].',') !== FALSE ? 'selected' : '')." value='".$row['position']."'>".$row['position']."</option>";
						} ?>
					</select>
				</div></div></div>
				<?php $task_groups = explode("#*#", get_config($dbc, 'site_work_order_tasks'));
				foreach($task_groups as $key => $group) {
					$list = explode('*#*', $group);
					echo "<div class='col-sm-12'><div class='form-group'><label class='col-sm-4 control-label'>Group Name: </label><div class='col-sm-8'><input type='text' name='taskgroup_".$key."[]' value='".$list[0]."' class='form-control'></div></div>";
					unset($list[0]);
					if(count($list) == 0) {
						$list[] = '';
					}
					foreach($list as $task) {
						echo "<div class='form-group'><label class='col-sm-4 control-label'>Task: </label><div class='col-sm-8'><input type='text' name='taskgroup_".$key."[]' value='".$task."' class='form-control'></div></div>";
					}
					echo "<button class='btn brand-btn pull-right' onclick='add_task(this); return false;'>Add Task</button>";
					echo "</div>";
				} ?>
				<button onclick="add_group(); return false;" class="btn brand-btn pull-right">Add Group</button>
				<script>
				function add_task(btn) {
					var textbox = $(btn).closest('.col-sm-12').find('.form-group').last().clone();
					textbox.find('input').val('');
					$(btn).before(textbox);
					$(btn).closest('.col-sm-12').find('input').last().focus();
				}
				function add_group() {
					var last = $('[name^=taskgroup]').last();
					var max = parseInt(last.attr('name').split('_')[1].replace('[]',''));
					var div = last.parents('.col-sm-12');
					var clone = div.clone();
					clone.find('.form-group').not(':last').not(':first').remove();
					clone.find('input').val('');
					clone.find('input').attr('name','taskgroup_'+(max + 1)+'[]');
					div.after(clone);
				}
				</script>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_readonly" >
					Site Work Order Accordion Read-Only Settings<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_readonly" class="panel-collapse collapse">
			<div class="panel-body">
				<div class="col-sm-12"><div class="form-group"><div class="col-sm-offset-4 col-sm-8" style="">
					<label class="control-label" style="color: #ffffff;">Select staff positions that you would like to have read-only access to for Site Work Order accordions.</label>
				</div></div></div>
			<?php
				$accordion_list = ['who' => 'Who', 'staff' => 'Staff & Crew', 'services' => 'Services', 'equip' => 'Equipment', 'material' => 'Materials', 'where' => 'Where', 'when' => 'When', 'docs' => 'Support Documents', 'checklist' => 'Site Checklist', 'pos' => 'Purchase Orders', 'comments' => 'Comments', 'safety' => 'Safety Checklist', 'addendum' => 'Addendum'];
				foreach ($accordion_list as $accordion => $accordion_title) {
					$permission = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` = 'swo_readonly_".$accordion."'"));
					$positions = ','.$permission['value'].',';
			?>
				<div class="col-sm-12"><div class="form-group"><label class="col-sm-4 control-label"><?php echo $accordion_title; ?>: </label><div class="col-sm-8">
					<select multiple data-placeholder="Select Staff Positions" name="swo_readonly_<?php echo $accordion; ?>[]" class="chosen-select-deselect form-control">
						<?php
							$query = mysqli_query($dbc, "SELECT DISTINCT `position` FROM `contacts` WHERE `category` = 'Staff' AND `deleted` = 0 AND `status` = 1");
							while ($row = mysqli_fetch_array($query)) {
								echo '<option value="'.$row['position'].'"'.(strpos($positions, ','.$row['position'].',') !== FALSE ? ' selected' : '').'>'.$row['position'].'</option>';
							}
						?>
					</select>
				</div></div></div>
				<?php
				}
			?>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_timer" >
					Timer Max Duration<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_timer" class="panel-collapse collapse">
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-4 control-label">Maximum Timer Duration</label>
					<div class="col-sm-8">
						<?php $max_timer = get_config($dbc, 'max_timer');
						$hours = floor($max_timer / 3600);
						$minutes = floor($max_timer % 3600 / 60); ?>
						<input type="text" class="form-control timepicker" value="<?= sprintf('%02d',$hours).':'.sprintf('%02d',$minutes) ?>" name="max_timer">
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
					<label for="file[]" class="col-sm-4 control-label">Flag Colours to Use<span class="popover-examples list-inline">&nbsp;
					<a  data-toggle="tooltip" data-placement="top" title="The selected colours will be cycled through when you flag an entry. These are software-wide settings."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
					</span>:</label>
					<div class="col-sm-8">
						<?php $flag_list = explode('#*#',get_config($dbc, 'general_flag_colours'));
						$flag_colours = [];
						$flag_names = [];
						foreach($flag_list as $list) {
							$list_arr = explode('*#*',$list);
							$flag_colours[] = $list_arr[0];
							$flag_names[] = $list_arr[1];
						} ?>
						<label class="col-sm-4" style="padding: 0 0.5em;"><input type="checkbox" <?= (in_array('FB0D0D', $flag_colours) ? 'checked' : '') ?> value="FB0D0D" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
						<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FB0D0D; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
						<div class="col-sm-8"><input type="text" name="flag_name_FB0D0D" value="<?= (in_array('FB0D0D', $flag_colours) ? $flag_names[array_search('FB0D0D',$flag_colours)] : '') ?>" class="form-control"></div>
						<label class="col-sm-4" style="padding: 0 0.5em;"><input type="checkbox" <?= (in_array('B97A57', $flag_colours) ? 'checked' : '') ?> value="B97A57" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
						<div style="border: 1px solid black; border-radius: 0.25em; background-color: #B97A57; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
						<div class="col-sm-8"><input type="text" name="flag_name_B97A57" value="<?= (in_array('B97A57', $flag_colours) ? $flag_names[array_search('B97A57',$flag_colours)] : '') ?>" class="form-control"></div>
						<label class="col-sm-4" style="padding: 0 0.5em;"><input type="checkbox" <?= (in_array('FFAEC9', $flag_colours) ? 'checked' : '') ?> value="FFAEC9" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
						<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FFAEC9; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
						<div class="col-sm-8"><input type="text" name="flag_name_FFAEC9" value="<?= (in_array('FFAEC9', $flag_colours) ? $flag_names[array_search('FFAEC9',$flag_colours)] : '') ?>" class="form-control"></div>
						<label class="col-sm-4" style="padding: 0 0.5em;"><input type="checkbox" <?= (in_array('FFC90E', $flag_colours) ? 'checked' : '') ?> value="FFC90E" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
						<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FFC90E; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
						<div class="col-sm-8"><input type="text" name="flag_name_FFC90E" value="<?= (in_array('FFC90E', $flag_colours) ? $flag_names[array_search('FFC90E',$flag_colours)] : '') ?>" class="form-control"></div>
						<label class="col-sm-4" style="padding: 0 0.5em;"><input type="checkbox" <?= (in_array('EFE4B0', $flag_colours) ? 'checked' : '') ?> value="EFE4B0" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
						<div style="border: 1px solid black; border-radius: 0.25em; background-color: #EFE4B0; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
						<div class="col-sm-8"><input type="text" name="flag_name_EFE4B0" value="<?= (in_array('EFE4B0', $flag_colours) ? $flag_names[array_search('EFE4B0',$flag_colours)] : '') ?>" class="form-control"></div>
						<label class="col-sm-4" style="padding: 0 0.5em;"><input type="checkbox" <?= (in_array('B5E61D', $flag_colours) ? 'checked' : '') ?> value="B5E61D" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
						<div style="border: 1px solid black; border-radius: 0.25em; background-color: #B5E61D; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
						<div class="col-sm-8"><input type="text" name="flag_name_B5E61D" value="<?= (in_array('B5E61D', $flag_colours) ? $flag_names[array_search('B5E61D',$flag_colours)] : '') ?>" class="form-control"></div>
						<label class="col-sm-4" style="padding: 0 0.5em;"><input type="checkbox" <?= (in_array('99D9EA', $flag_colours) ? 'checked' : '') ?> value="99D9EA" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
						<div style="border: 1px solid black; border-radius: 0.25em; background-color: #99D9EA; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
						<div class="col-sm-8"><input type="text" name="flag_name_99D9EA" value="<?= (in_array('99D9EA', $flag_colours) ? $flag_names[array_search('99D9EA',$flag_colours)] : '') ?>" class="form-control"></div>
						<label class="col-sm-4" style="padding: 0 0.5em;"><input type="checkbox" <?= (in_array('7092BE', $flag_colours) ? 'checked' : '') ?> value="7092BE" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
						<div style="border: 1px solid black; border-radius: 0.25em; background-color: #7092BE; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
						<div class="col-sm-8"><input type="text" name="flag_name_7092BE" value="<?= (in_array('7092BE', $flag_colours) ? $flag_names[array_search('7092BE',$flag_colours)] : '') ?>" class="form-control"></div>
						<label class="col-sm-4" style="padding: 0 0.5em;"><input type="checkbox" <?= (in_array('C8BFE7', $flag_colours) ? 'checked' : '') ?> value="C8BFE7" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
						<div style="border: 1px solid black; border-radius: 0.25em; background-color: #C8BFE7; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
						<div class="col-sm-8"><input type="text" name="flag_name_C8BFE7" value="<?= (in_array('C8BFE7', $flag_colours) ? $flag_names[array_search('C8BFE7',$flag_colours)] : '') ?>" class="form-control"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	$temp_worker_form_enabled = mysqli_fetch_array(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name` = 'swo_temp_worker_form'"))['value'];
	$temp_worker_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as `num_rows`, `hrid` FROM `hr` WHERE `form` = 'Temporary Worker Orientation'"));
	if ($temp_worker_form['num_rows'] > 0) { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_temp_worker_form" >
					Temporary Worker Orientation Form Settings<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_temp_worker_form" class="panel-collapse collapse">
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-4 control-label">Display Temporary Worker Orientation Form</label>
					<div class="col-sm-8">
						<input type="checkbox" name="temp_worker_form_enabled" <?= ($temp_worker_form_enabled == 1 ? 'checked' : ''); ?> value="1">
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_signin" >
					Sign In Tab Settings<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_signin" class="panel-collapse collapse">
			<div class="panel-body">
				<div class="form-group">
					<?php $swo_display_all_sites = get_config($dbc, 'swo_display_all_sites'); ?>
					<label class="col-sm-4 control-label">Display All Sites Including Ones Without Work Orders</label>
					<div class="col-sm-8">
						<input type="checkbox" name="swo_display_all_sites" <?= ($swo_display_all_sites == 1 ? 'checked' : ''); ?> value="1">
					</div>
				</div>
				<div class="form-group">
					<?php $equipment_transfer_staff = get_config($dbc, 'equipment_transfer_staff'); ?>
					<label class="col-sm-4 control-label">Include Staff In Equipment Transfer Tile</label>
					<div class="col-sm-8">
						<input type="checkbox" name="equipment_transfer_staff" <?= ($equipment_transfer_staff == 1 ? 'checked' : ''); ?> value="1">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
    <div class="col-sm-6">
        <a href="site_work_orders.php?tab=<?= $_GET['tab'] ?>" class="btn brand-btn btn-lg">Back</a>
		<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
    </div>
    <div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
    </div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>