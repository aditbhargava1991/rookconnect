<?php
	if ( isset ( $_GET['status'] ) ) {
		$status = trim ( $_GET['status'] );
		$status_query = ( $status == 'active' ) ? ">0" : "=0";

	} else {
		$status = 'active';
		$status_query = ">0";
	}
?>

<script>
	$(document).on('change', 'select[name="role[]"]', function() { changeRole(this); });
	$(document).on('change', 'select[name="regions[]"]', function() { changeRegions(this); });
	$(document).on('change', 'select[name="locations[]"]', function() { changeLocations(this); });
	$(document).on('change', 'select[name="classifications[]"]', function() { changeClassifications(this); });
	$(document).on('change', 'select[name="positions[]"]', function() { changePositions(this); });

	function changeRole(sel) {
		var role	= $(sel).val().join(',');
		var id		= sel.id;

		$.ajax({
			type: "GET",
			url: "security_ajax_all.php?fill=change_role&role="+role+"&id="+id,
			dataType: "html",
			success: function(response){
				location.reload();
			}
		});
	}

	function changeRegions(sel) {
		var regions	= $(sel).val().join(',');
		var id		= sel.id;
		$.ajax({
			type: "GET",
			url: "security_ajax_all.php?fill=change_regions&regions="+regions+"&id="+id,
			dataType: "html",
			success: function(response){
				location.reload();
			}
		});
	}

	function changeLocations(sel) {
		var locations	= $(sel).val().join(',');
		var id		= sel.id;

		$.ajax({
			type: "GET",
			url: "security_ajax_all.php?fill=change_locations&locations="+locations+"&id="+id,
			dataType: "html",
			success: function(response){
				location.reload();
			}
		});
	}

	function changeClassifications(sel) {
		var classifications	= $(sel).val().join(',');
		var id		= sel.id;

		$.ajax({
			type: "GET",
			url: "security_ajax_all.php?fill=change_classifications&classifications="+classifications+"&id="+id,
			dataType: "html",
			success: function(response){
				location.reload();
			}
		});
	}

	function changePositions(sel) {
		var positions	= $(sel).val().join(',');
		var id		= sel.id;

		$.ajax({
			type: "GET",
			url: "security_ajax_all.php?fill=change_positions&positions="+positions+"&id="+id,
			dataType: "html",
			success: function(response){
				location.reload();
			}
		});
	}
</script>

<div class="tab-container mobile-100-container">
	<a href="security.php?tab=assign&status=active"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ($status == 'active' ? 'active_tab' : ''); ?>">Active Users</button></a>
	<a href="security.php?tab=assign&status=suspended"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ($status == 'suspended' ? 'active_tab' : ''); ?>">Suspended Users</button></a>
</div>

<?php
    if($_GET['status'] == 'suspended') {
        $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `note` FROM `notes_setting` WHERE `tile`='security' AND `subtab`='security_assign_previleges_suspended'"));
        $note = $notes['note'];
    } else {
        $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `note` FROM `notes_setting` WHERE `tile`='security' AND `subtab`='security_assign_previleges_active'"));
        $note = $notes['note'];
    }

    if ( !empty($note) ) { ?>
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11">
                <span class="notice-name">NOTE:</span>
                <?= $note; ?>
            </div>
            <div class="clearfix"></div>
        </div><?php
    }

	// Pagination Config
	$rowsPerPage = 25;
	$pageNum = 1;

	if ( isset ( $_GET['page'] ) ) {
		$pageNum = $_GET['page'];
	}

	$offset = ($pageNum - 1) * $rowsPerPage;

	$sql		= "SELECT `contacts`.`contactid`, `contacts`.`name`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`category`, `contacts`.`user_name`, `contacts`.`role`, `region_access`, `location_access`, `classification_access`, `positions_allowed` FROM `contacts` LEFT JOIN `contacts_security` ON `contacts`.`contactid`=`contacts_security`.`contactid` WHERE (`contacts`.`category` IN (".STAFF_CATS.") OR IFNULL(`contacts`.`user_name`,'') != '') AND `contacts`.`status`{$status_query} AND `contacts`.`deleted`=0 LIMIT {$offset}, {$rowsPerPage}";
	$result		= mysqli_query($dbc, $sql);
	$count		= mysqli_num_rows ( $result );
	$sorted_list = sort_contacts_query($result);
	$sql_count	= "SELECT COUNT(*) `numrows` FROM `contacts` WHERE (`category` IN (".STAFF_CATS.") OR IFNULL(`user_name`,'') != '') AND `status`{$status_query} AND `deleted`=0";
	$on_security = get_security_levels($dbc);
	$region_list = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
	$location_list = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
	$classification_list = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_classification'"))[0])));
	$position_list = $dbc->query("SELECT `position_id`, `name`, `description` FROM `positions` WHERE `deleted`=0 AND IFNULL(`name`,'') != ''")->fetch_all(MYSQLI_ASSOC);

	if ( $count > 0 ) {
		// Display pagination
		echo display_pagination($dbc, $sql_count, $pageNum, $rowsPerPage); ?>

		<table class="table table-bordered table-striped">
			<thead>
                <tr class="hidden-xs hidden-sm">
                    <th style="width:25em;">Staff Name</th>
                    <th>Security Level</th>
                    <?= count($region_list) > 0 ? '<th>Allowed Regions</th>' : '' ?>
                    <?= count($location_list) > 0 ? '<th>Allowed Locations</th>' : '' ?>
                    <?= count($classification_list) > 0 ? '<th>Allowed Classifications</th>' : '' ?>
                    <?= count($position_list) > 0 ? '<th>Allowed Positions</th>' : '' ?>
                </tr>
            </thead><?php

			foreach ($sorted_list as $row) {
				$name = '';
				if ( empty($row['first_name']) && empty($row['last_name']) ) {
					$name = ( !empty($row['name']) ) ? $row['name'] : $row['user_name'];
				} else {
					$name = trim($row['first_name'].' '.$row['last_name']);
				}
				if ( empty($name) ) { $name = 'Not given'; }
				$region_access = array_filter(explode(',',$row['region_access']));
				$location_access = array_filter(explode(',',$row['location_access']));
				$classification_access = array_filter(explode(',',$row['classification_access']));
				$allowed_positions = array_filter(explode(',',$row['positions_allowed'])); ?>

				<tr>
					<td data-title="Staff Name"><?= $name.(!empty($row['user_name']) ? '<br />Username: '.$row['user_name'] : '').($row['category'] != 'Staff' ? '<br /> Category: '.$row['category'] : '') ?></td>
					<td data-title="Security Level">
						<select name="role[]" multiple id="<?= $row['contactid'] ?>" data-placeholder="Select a Security Level" class="role chosen-select-deselect form-control">
							<?php foreach($on_security as $category => $value) { ?>
								<option <?= $value == 'super' ? 'disabled' : '' ?> <?= strpos(','.$row['role'].',',",$value,") !== FALSE ? 'selected' : '' ?> value="<?= $value ?>"><?= $category ?></option>
							<?php } ?>
						</select>
					</td>
					<?php if(count($region_list) > 0) { ?>
						<td data-title="Allowed Regions">
							<select name="regions[]" multiple id="<?= $row['contactid'] ?>" data-placeholder="Select Allowed Regions" class="regions chosen-select-deselect form-control">
								<?php foreach($region_list as $value) { ?>
									<option <?= in_array($value, $region_access) ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
								<?php } ?>
							</select>
						</td>
					<?php } ?>
					<?php if(count($location_list) > 0) { ?>
						<td data-title="Allowed Locations">
							<select name="locations[]" multiple id="<?= $row['contactid'] ?>" data-placeholder="Select Allowed Locations" class="locations chosen-select-deselect form-control">
								<?php foreach($location_list as $value) { ?>
									<option <?= in_array($value, $location_access) ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
								<?php } ?>
							</select>
						</td>
					<?php } ?>
					<?php if(count($classification_list) > 0) { ?>
						<td data-title="Allowed Classifications">
							<select name="classifications[]" multiple id="<?= $row['contactid'] ?>" data-placeholder="Select Allowed Classifications" class="classifications chosen-select-deselect form-control">
								<?php foreach($classification_list as $value) { ?>
									<option <?= in_array($value, $classification_access) ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
								<?php } ?>
							</select>
						</td>
					<?php } ?>
					<?php if(count($position_list) > 0) { ?>
						<td data-title="Allowed Positions">
							<select name="positions[]" multiple id="<?= $row['contactid'] ?>" data-placeholder="Select Allowed Positions" class="positions chosen-select-deselect form-control">
								<?php foreach($position_list as $value) { ?>
									<option <?= in_array($value['position_id'], $allowed_positions) ? 'selected' : '' ?> value="<?= $value['position_id'] ?>"><?= $value['name'] ?></option>
								<?php } ?>
							</select>
						</td>
					<?php } ?>
				</tr><?php
			} ?>
		</table><?php

		// Display pagination
		echo display_pagination($dbc, $sql_count, $pageNum, $rowsPerPage);

	} else { ?>
		<h2>No Records Found.</h2><?php
	}
?>
