<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');
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
<?php $equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
include_once ('../Equipment/region_location_access.php');
?>

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
						Select Tab:</label>
				</div>
				<div class="col-sm-8">
					<select data-placeholder="Select Tab" name="search_cat" class="chosen-select-deselect form-control">
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
		echo '<a href="?edit_checklist=1" class="btn brand-btn mobile-block gap-bottom pull-right double-gap-top">Add Checklist</a>';
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