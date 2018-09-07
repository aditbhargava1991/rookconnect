<?php
/*
Equipment Listing
*/
include ('../include.php');
error_reporting(0);

if (isset($_POST['export_pdf'])) {
    $checklistid = $_POST['export_pdf'];
	include('checklist_pdf.php');
} ?>
<style type='text/css'>
.display-field {
  display: inline-block;
  text-indent: 2px;
  vertical-align: top;
  width: calc(100% - 2.5em);
}
.popped-field {
	width: calc(100% + 1em);
}
.popped-field .display-field {
	color: black;
	font-size: 1.2em;
}
</style>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('equipment');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
include_once ('../Equipment/region_location_access.php');
?>
<div class="container">
	<div class="iframe_holder" style="display:none;">
		<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
		<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
		<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
	</div>
	<div class="row hide_on_iframe">

        <div class="col-sm-10"><h1 class="single-pad-bottom">Equipment: Checklist</h1></div>
		<div class="col-sm-2 double-gap-top">
			<?php
			if(config_visible_function($dbc, 'equipment') == 1) {
				echo '<a href="field_config_equipment.php?type=tab" class="mobile-block pull-right "><img style="width:45px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
                echo '<span class="popover-examples pull-right" style="margin:10px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the Settings within this tile. Any changes will appear on your dashboard."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
			} ?>
		</div>
		<div class="clearfix double-gap-bottom"></div>

		<div class="tab-container">
			<?php if ( in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Active Equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="equipment.php?category=Top&status=Active"><button type="button" class="btn brand-btn mobile-block">Active Equipment</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit Inactive Equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="equipment.php?category=Top&status=Inactive"><button type="button" class="btn brand-btn mobile-block">Inactive Equipment</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View all past and scheduled equipment inspections."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="inspections.php"><button type="button" class="btn brand-btn mobile-block">Inspections</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Assign',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'assign') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Assigned equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="assign_equipment.php"><button type="button" class="btn brand-btn mobile-block">Assigned Equipment</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_orders') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View the status of all Work Orders."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="work_orders.php"><button type="button" class="btn brand-btn mobile-block">Work Orders</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View all Equipment Expenses."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="expenses.php"><button type="button" class="btn brand-btn mobile-block">Expenses</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Balance Sheets relating to a specific item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="balance.php"><button type="button" class="btn brand-btn mobile-block">Balance Sheets</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View the scheduled service dates for equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="service_schedules.php"><button type="button" class="btn brand-btn mobile-block">Service Schedules</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Requests',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'requests') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and add Services Requests for equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="service_request.php?category=Top"><button type="button" class="btn brand-btn mobile-block">Service Requests</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Records',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'records') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and add Service Records for equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="service_record.php?category=Top"><button type="button" class="btn brand-btn mobile-block">Service Records</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Checklists',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'checklist') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View, add and edit Equipment Checklists."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="equipment_checklist.php"><button type="button" class="btn brand-btn mobile-block active_tab">Checklists</button></a>
                </div>
			<?php } ?>
            <div class="clearfix"></div>
		</div>

		<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
			<div class="col-sm-11"><span class="notice-name">NOTE:</span>
			A checklist is defined for either an equipment category, a type of equipment, or a specific piece of equipment. Once you have selected a piece of equipment, any checklists that match the piece of equipment will be displayed.</div>
			<div class="clearfix"></div>
		</div>

		<form name="form_sites" method="post" action="" class="form-inline" role="form">

			<script>
			function filterCategory(cat) {
				if(cat != '') {
					$('[name=search_equip] optgroup').each(function() {
						$(this).prop('label','');
					});
				} else {
					$('[name=search_equip] optgroup').each(function() {
						$(this).prop('label',$(this).data('category'));
					});
				}
				$('[name=search_equip] option').each(function() {
					if(cat == '' || $(this).data('category') == cat) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
				$('[name=search_equip]').trigger('change.select2');
			}
			</script>
			<div class="search-group">
				<div class="form-group col-lg-10 col-md-10 col-sm-12 col-xs-12">
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="col-sm-4">
							<label for="site_name" class="control-label">
								<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see all equipment for which you have records."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								Select Category:</label>
						</div>
						<div class="col-sm-8">
							<select data-placeholder="Select Category" name="search_cat" class="chosen-select-deselect form-control">
								<option></option>
								<?php $search_cat = (!empty($_POST['search_cat']) ? $_POST['search_cat'] : '');
								$query = mysqli_query($dbc,"SELECT `category`, COUNT(*) FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `category` ORDER BY `category`");
								while($equipment = mysqli_fetch_array($query)) { ?>
									<option <?= ($equipment['category'] == $search_cat ? " selected" : '') ?> value='<?=  $equipment['category'] ?>' ><?= $equipment['category'] ?></option>
								<?php }
								echo ($category == '' ? '' : '</optgroup>'); ?>
							</select>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="col-sm-4">
							<label for="site_name" class="control-label">
								<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see all equipment for which you have records."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								Select Equipment:</label>
						</div>
						<div class="col-sm-8">
							<select data-placeholder="Select Equipment" name="search_equip" class="chosen-select-deselect form-control">
								<option></option>
								<?php $search_equip = (!empty($_POST['search_equip']) ? $_POST['search_equip'] : '');
								$query = mysqli_query($dbc,"SELECT `equipmentid`, `category`, `type`, `unit_number` FROM `equipment` WHERE `deleted`=0 $access_query ORDER BY `category`, `unit_number`, `type`");
								$category = '';
								while($equipment = mysqli_fetch_array($query)) {
									if($equipment['category'] != $category) {
										$category = $equipment['category'];
										echo ($category == '' ? '' : '</optgroup>').'<optgroup data-category="'.$category.'" label="'.$category.'">';
									} ?>
									<option <?= ($equipment['equipmentid'] == $search_equip ? " selected" : '') ?> data-category="<?= $equipment['category'] ?>" value='<?=  $equipment['equipmentid'] ?>' >Unit #<?= $equipment['unit_number'].': '.$equipment['type'] ?></option><?php
								}
								echo ($category == '' ? '' : '</optgroup>'); ?>
							</select>
						</div>
					</div>
				</div>
				<div class="form-group col-lg-2 col-md-3 col-sm-12 col-xs-12">
					<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
				</div><!-- .form-group -->
				<div class="clearfix"></div>
			</div>

            <?php if(vuaed_visible_function($dbc, 'equipment') == 1) {
				echo '<a href="add_checklist.php" class="btn brand-btn mobile-block gap-bottom pull-right double-gap-top">Add Checklist</a>';
			}
			if($search_equip != '') {
				$checklists = mysqli_query($dbc, "SELECT * FROM `equipment` equip LEFT JOIN `item_checklist` list ON list.`checklist_item`='equipment' AND list.`deleted`=0 AND equip.`equipmentid`=list.`item_id` WHERE `equipmentid`='$search_equip'");
				while($checklist = mysqli_fetch_array($checklists)) {
					include('../Checklist/item_checklist_view.php');
				}
			} else {
				echo "<h3>Select equipment to view checklists.</h3>";
			} ?>
		</form>
	</div>
</div>

<?php include ('../footer.php'); ?>