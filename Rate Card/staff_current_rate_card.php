<script>
$(document).ready(function() {
	var selectCat = $('[name=filter_category]').val();
	var categories = new Array();
	$('td[data-title=Category]').each(function() {
		var cat = $(this).text();
		if(categories.find(function(val) { return val == cat; }) == undefined) {
			categories.push(cat);
		}
	});
	categories.sort();
	$('[name=filter_category]').empty();
	$('[name=filter_category]').append("<option value=''>Display All</option>");
	for(var cat in categories) {
		var selected = '';
		if(categories[cat] == selectCat) {
			selected = ' selected';
		}
		$('[name=filter_category]').append("<option"+selected+" value='"+categories[cat]+"'>"+categories[cat]+"</option>");
	}
	$('[name=filter_category]').change(filter_rates);
	$('[name=filter_category]').trigger('change.select2').change();
	
	$('#rate_table .form-control').change(function() {
		if(this.name == 'staff_id' && this.value == 'ALL_STAFF') {
			$(this).find('option').filter(function() { return this.value != ''; }).prop('selected','selected');
			$(this).find('option[value=ALL_STAFF]').removeAttr('selected');
			$(this).trigger('change.select2').change();
			return false;
		}
		var id = $(this).closest('tr').find('input[name=rate_id]').val();
		$.ajax({
			type: "POST",
			url: "ratecard_ajax_all.php?fill=staff_rate_update",
			data: { rate_id: id, field: this.name, value: $(this).val() },
			success: function(response) {
				console.log(response);
			}
		});
	});
});

function filter_rates() {
	var cat = $('[name=filter_category]').val();
	$('#rate_table tr').each(function() {
		if($(this).find('th').length > 0 || ($(this).find('td[data-title=Category]').text() == cat) || cat == '') {
			$(this).show();
			return;
		}
		$(this).hide();
	});
}
</script>
<div class="form-group">
	<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
		<label for="filter_category" class="control-label">Filter By Rate Category:</label>
	</div>
	<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
		<select name="filter_category" class="chosen-select-deselect form-control" width="380">
			<option value="">Display All</option>
		</select>
	</div>
</div>
<div class='main_frame' id='no-more-tables'>
	<?php
	$db_conf = get_config($dbc, 'staff_db_rate_fields');
	if(str_replace(',','',$db_conf) == '') {
		$db_conf = ",card,annual,history,function,";
	}
	$count_sql = "SELECT COUNT(`companyrcid`) numrows FROM `company_rate_card` WHERE `deleted` = 0 AND `tile_name` LIKE 'Staff'";
	$rowsPerPage = 25;
	$pageNum = 1;
	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	if($offset > mysqli_fetch_array(mysqli_query($dbc, $count_sql))['numrows']) {
		$offset = 0;
		$pageNum = 1;
	}
	$sql = "SELECT * FROM `company_rate_card` WHERE `deleted` = 0 AND `tile_name` LIKE 'Staff' ORDER BY `description` LIMIT $offset, $rowsPerPage";
	$result = mysqli_query($dbc, $sql);
	
	if(mysqli_num_rows($result) > 0):
		// Pagination Configuration
		echo display_pagination($dbc, $count_sql, $pageNum, $rowsPerPage);
		
		// Table Headers ?>
		<table id="rate_table" class="table table-bordered table-striped">
            <thead>
			<tr class="hidden-xs hidden-sm">
				<?php if(strpos($db_conf,'card') !== false): ?>
					<th style="max-width: 50%; width:40em;">Staff</th><?php endif; ?>
				<?php if(strpos($db_conf,'category') !== false): ?>
					<th>Category</th><?php endif; ?>
				<?php if(strpos($db_conf,'start_end_dates') !== false): ?>
					<th>Start Date</th><th>End Date</th><?php endif; ?>
				<?php if(strpos($db_conf,'alert_date') !== false): ?>
					<th>Alert Date</th><?php endif; ?>
				<?php if(strpos($db_conf,'alert_staff') !== false): ?>
					<th>Alert Staff</th><?php endif; ?>
				<?php if(strpos($db_conf,'created_by') !== false): ?>
					<th>Created By</th><?php endif; ?>
				<?php if(strpos($db_conf,'annual') !== false): ?>
					<th>Annual</th><?php endif; ?>
				<?php if(strpos($db_conf,'monthly') !== false): ?>
					<th>Monthly</th><?php endif; ?>
				<?php if(strpos($db_conf,'semi_month') !== false): ?>
					<th>Semi-Monthly</th><?php endif; ?>
				<?php if(strpos($db_conf,'weekly') !== false): ?>
					<th>Weekly</th><?php endif; ?>
				<?php if(strpos($db_conf,'daily') !== false): ?>
					<th>Daily</th><?php endif; ?>
				<?php if(strpos($db_conf,'hourly') !== false): ?>
					<th>Hourly</th><?php endif; ?>
				<?php if(strpos($db_conf,'hourly_work') !== false): ?>
					<th>Hourly (Work)</th><?php endif; ?>
				<?php if(strpos($db_conf,'hourly_travel') !== false): ?>
					<th>Hourly (Travel)</th><?php endif; ?>
				<?php if(strpos($db_conf,'field_day_actual') !== false): ?>
					<th>Field Day (Cost)</th><?php endif; ?>
				<?php if(strpos($db_conf,'field_day_bill') !== false): ?>
					<th>Field Day (Billable)</th><?php endif; ?>
				<?php if(strpos($db_conf,'uom') !== false): ?>
					<th>UoM</th><?php endif; ?>
				<?php if(strpos($db_conf,'cost') !== false): ?>
					<th>Cost</th><?php endif; ?>
				<?php if(strpos($db_conf,'price_admin') !== false): ?>
					<th>Admin Price</th><?php endif; ?>
				<?php if(strpos($db_conf,'price_wholesale') !== false): ?>
					<th>Wholesale Price</th><?php endif; ?>
				<?php if(strpos($db_conf,'price_commercial') !== false): ?>
					<th>Commercial Price</th><?php endif; ?>
				<?php if(strpos($db_conf,'price_client') !== false): ?>
					<th>Client Price</th><?php endif; ?>
				<?php if(strpos($db_conf,'minimum') !== false): ?>
					<th>Minimum Billable</th><?php endif; ?>
				<?php if(strpos($db_conf,'unit_price') !== false): ?>
					<th>Unit Price</th><?php endif; ?>
				<?php if(strpos($db_conf,'unit_cost') !== false): ?>
					<th>Unit Cost</th><?php endif; ?>
				<?php if(strpos($db_conf,'rent_price') !== false): ?>
					<th>Rent Price</th><?php endif; ?>
				<?php if(strpos($db_conf,'rent_days') !== false): ?>
					<th>Rental Days</th><?php endif; ?>
				<?php if(strpos($db_conf,'rent_weeks') !== false): ?>
					<th>Rental Weeks</th><?php endif; ?>
				<?php if(strpos($db_conf,'rent_months') !== false): ?>
					<th>Rental Months</th><?php endif; ?>
				<?php if(strpos($db_conf,'rent_years') !== false): ?>
					<th>Rental Years</th><?php endif; ?>
				<?php if(strpos($db_conf,'num_days') !== false): ?>
					<th>Number of Days</th><?php endif; ?>
				<?php if(strpos($db_conf,'num_hours') !== false): ?>
					<th>Number of Hours</th><?php endif; ?>
				<?php if(strpos($db_conf,'num_kms') !== false): ?>
					<th>Number of KMs</th><?php endif; ?>
				<?php if(strpos($db_conf,'num_miles') !== false): ?>
					<th>Number of Miles</th><?php endif; ?>
				<?php if(strpos($db_conf,'fee') !== false): ?>
					<th>Fee</th><?php endif; ?>
				<?php if(strpos($db_conf,'hours_estimated') !== false): ?>
					<th>Estimated Hours</th><?php endif; ?>
				<?php if(strpos($db_conf,'hours_actual') !== false): ?>
					<th>Actual Hours</th><?php endif; ?>
				<?php if(strpos($db_conf,'service_code') !== false): ?>
					<th>Service Code</th><?php endif; ?>
				<?php if(strpos($db_conf,'description') !== false): ?>
					<th>Description</th><?php endif; ?>
				<?php if(strpos($db_conf,'work_desc') !== false): ?>
					<th>Work</th><?php endif; ?>
				<?php if(strpos($db_conf,'history') !== false): ?>
					<th>History</th><?php endif; ?>
				<?php if(strpos($db_conf,'function') !== false): ?>
					<th style="text-align:center;">Functions</th><?php endif; ?>
			</tr>
            </thead>
		<?php // Table Rows
		$staff_sql = "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0";
		$staff_list = sort_contacts_query(mysqli_query($dbc, $staff_sql));
		while($row = mysqli_fetch_array($result)) {
			echo '<tr><input type="hidden" name="rate_id" value="'.$row['rate_id'].'">';
			if(strpos($db_conf,'card') !== false):
				$staff_ids = array_filter(explode(',',$row['item_id'].','.$row['description']));
				echo '<td data-title="Staff"><select multiple name="description" class="chosen-select-deselect form-control">';
				echo '<option></option><option '.(in_array('ALL', $staff_ids) ? 'selected' : '').' value="ALL">Select All Staff</option>';
				foreach($staff_list as $staff) {
					echo '<option '.(in_array($staff['contactid'], $staff_ids) ? 'selected' : '').' value="'.$staff['contactid'].'">'.$staff['full_name'].'</option>';
				}
				echo '</select></td>';
			endif;
			if(strpos($db_conf,'category') !== false):
				echo '<td data-title="Category">' . $row['category'] . '</td>';
			endif;
			if(strpos($db_conf,'start_end_dates') !== false):
				echo '<td data-title="Start Date">' . $row['start_date'] . '</td>';
				echo '<td data-title="End Date">' . $row['end_date'] . '</td>';
			endif;
			if(strpos($db_conf,'alert_date') !== false):
				echo '<td data-title="Alert Date">' . $row['alert_date'] . '</td>';
			endif;
			if(strpos($db_conf,'alert_staff') !== false):
				echo '<td data-title="Alert Staff">';
				$alert_staff_list = [];
				foreach(explode(',',$row['alert_staff']) as $staffid) {
					if($staffid > 0) {
						$alert_staff_list[] = get_contact($dbc, $staffid);
					}
				}
				echo implode(', ',$alert_staff_list);
				echo '</td>';
			endif;
			if(strpos($db_conf,'created_by') !== false):
				echo '<td data-title="Created By">' . get_contact($dbc, $row['created_by']) . '</td>';
			endif;
			if(strpos($db_conf,'annual') !== false):
				echo '<td data-title="Annual Rate"><input name="annual" type="number" class="form-control" step="any" min=0 value="' . $row['annual'] . '"></td>';
			endif;
			if(strpos($db_conf,'monthly') !== false):
				echo '<td data-title="Monthly Rate"><input name="monthly" type="number" class="form-control" step="any" min=0 value="' . $row['monthly'] . '"></td>';
			endif;
			if(strpos($db_conf,'semi_month') !== false):
				echo '<td data-title="Semi-Monthly Rate"><input name="semi_month" type="number" class="form-control" step="any" min=0 value="' . $row['semi_month'] . '"></td>';
			endif;
			if(strpos($db_conf,'weekly') !== false):
				echo '<td data-title="Weekly Rate"><input name="weekly" type="number" class="form-control" step="any" min=0 value="' . $row['weekly'] . '"></td>';
			endif;
			if(strpos($db_conf,'daily') !== false):
				echo '<td data-title="Daily Rate"><input name="daily" type="number" class="form-control" step="any" min=0 value="' . $row['daily'] . '"></td>';
			endif;
			if(strpos($db_conf,'hourly') !== false):
				echo '<td data-title="Hourly Rate"><input name="hourly" type="number" class="form-control" step="any" min=0 value="' . $row['hourly'] . '"></td>';
			endif;
			if(strpos($db_conf,'hourly_work') !== false):
				echo '<td data-title="Hourly Rate (Work)"><input name="hourly_work" type="number" class="form-control" step="any" min=0 value="' . $row['hourly_work'] . '"></td>';
			endif;
			if(strpos($db_conf,'hourly_travel') !== false):
				echo '<td data-title="Hourly Rate (Travel)"><input name="hourly_travel" type="number" class="form-control" step="any" min=0 value="' . $row['hourly_travel'] . '"></td>';
			endif;
			if(strpos($db_conf,'field_day_actual') !== false):
				echo '<td data-title="Field Day Rate (Actual Cost)">' . $row['field_day_actual'] . '</td>';
			endif;
			if(strpos($db_conf,'field_day_bill') !== false):
				echo '<td data-title="Field Day Rate (Billable Rate)">' . $row['field_day_bill'] . '</td>';
			endif;
			if(strpos($db_conf,'uom') !== false):
				echo '<td data-title="Unit of Measure">' . $row['uom'] . '</td>';
			endif;
			if(strpos($db_conf,'cost') !== false):
				echo '<td data-title="Cost">' . $row['cost'] . '</td>';
			endif;
			if(strpos($db_conf,'price_admin') !== false):
				echo '<td data-title="Admin Price">' . $row['price_admin'] . '</td>';
			endif;
			if(strpos($db_conf,'price_wholesale') !== false):
				echo '<td data-title="Wholesale Price">' . $row['price_wholesale'] . '</td>';
			endif;
			if(strpos($db_conf,'price_commercial') !== false):
				echo '<td data-title="Commercial Price">' . $row['price_commercial'] . '</td>';
			endif;
			if(strpos($db_conf,'price_client') !== false):
				echo '<td data-title="Client Price">' . $row['price_client'] . '</td>';
			endif;
			if(strpos($db_conf,'minimum') !== false):
				echo '<td data-title="Minimum Billable">' . $row['minimum'] . '</td>';
			endif;
			if(strpos($db_conf,'unit_price') !== false):
				echo '<td data-title="Unit Price">' . $row['unit_price'] . '</td>';
			endif;
			if(strpos($db_conf,'unit_cost') !== false):
				echo '<td data-title="Unit Cost">' . $row['unit_cost'] . '</td>';
			endif;
			if(strpos($db_conf,'rent_price') !== false):
				echo '<td data-title="Rent Price">' . $row['rent_price'] . '</td>';
			endif;
			if(strpos($db_conf,'rent_days') !== false):
				echo '<td data-title="Rental Days">' . $row['rent_days'] . '</td>';
			endif;
			if(strpos($db_conf,'rent_weeks') !== false):
				echo '<td data-title="Rental Weeks">' . $row['rent_weeks'] . '</td>';
			endif;
			if(strpos($db_conf,'rent_months') !== false):
				echo '<td data-title="Rental Months">' . $row['rent_months'] . '</td>';
			endif;
			if(strpos($db_conf,'rent_years') !== false):
				echo '<td data-title="Rental Years">' . $row['rent_years'] . '</td>';
			endif;
			if(strpos($db_conf,'num_days') !== false):
				echo '<td data-title="Number of Days">' . $row['num_days'] . '</td>';
			endif;
			if(strpos($db_conf,'num_hours') !== false):
				echo '<td data-title="Number of Hours">' . $row['num_hours'] . '</td>';
			endif;
			if(strpos($db_conf,'num_kms') !== false):
				echo '<td data-title="Number of Kilometers">' . $row['num_kms'] . '</td>';
			endif;
			if(strpos($db_conf,'num_miles') !== false):
				echo '<td data-title="Number of Miles">' . $row['num_miles'] . '</td>';
			endif;
			if(strpos($db_conf,'fee') !== false):
				echo '<td data-title="Fee">' . $row['fee'] . '</td>';
			endif;
			if(strpos($db_conf,'hours_estimated') !== false):
				echo '<td data-title="Estimated Hours">' . $row['hours_estimated'] . '</td>';
			endif;
			if(strpos($db_conf,'hours_actual') !== false):
				echo '<td data-title="Actual Hours">' . $row['hours_actual'] . '</td>';
			endif;
			if(strpos($db_conf,'service_code') !== false):
				echo '<td data-title="Service Code">' . $row['service_code'] . '</td>';
			endif;
			if(strpos($db_conf,'description') !== false):
				echo '<td data-title="Rate Description">' . $row['description'] . '</td>';
			endif;
			if(strpos($db_conf,'work_desc') !== false):
				echo '<td data-title="Description of Work">' . $row['work_desc'] . '</td>';
			endif;
			if(strpos($db_conf,'history') !== false):
				echo '<td data-id="'.$row['companyrcid'].'" data-title="History" class="history-link"><a>View Changes</a></td>';
			endif;
			if(strpos($db_conf,'function') !== false):
				echo '<td data-title="Functions" style="text-align:center;">';
				if(vuaed_visible_function($dbc, 'rate_card')) { ?>
					<a href="?card=staff&type=staff&status=add&id=<?php echo $row['companyrcid']; ?>">Edit</a>
					| <a onclick="return confirm('Are you sure you want to delete this rate card?');" href="../delete_restore.php?action=delete&staff_rate_id=<?php echo $row['companyrcid']; ?>">Delete</a>
				<?php } else {
					echo '<a href="?card=staff&type=staff&status=show&id='.$row['companyrcid'].'">View</a>';
				}
			endif;
			echo '</tr>';
		} ?>
		</table>
		
		<?php // End Pagination
		echo display_pagination($dbc, $count_sql, $pageNum, $rowsPerPage);
	else:
		echo "<h2>No Active Staff Rate Cards Found.</h2>";
	endif; ?>
</div>
<div class='iframe_holder' id='history_frame' style='display:none;'>
	<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframe' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
	<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'>Rate Card History</span>
	<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="0; border:0;" src=""></iframe>
</div>
<script>
$(document).ready(function() {
	$('.history-link').click(iframe_history);
	$('.close_iframe').click(iframe_history_close);

	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		this.style.height = (this.contentWindow.document.body.offsetHeight + 80) + 'px';
	});
});
function iframe_history() {
	var id = $(this).data('id');
	$('#iframe_instead_of_window').attr('src', 'rate_card.php?card=staff&status=history&id='+id);
	$('#history_frame').show();
	$('.main_frame').hide();
	$('#iframe_instead_of_window').on('load', function() {
		$(this).height($(this).get(0).contentWindow.document.body.scrollHeight);
	});
}
function iframe_history_close() {
	$('#history_frame').hide();
	$('.main_frame').show();
}
</script>
<style type='text/css'>
	.history-link {
		cursor: pointer;
	}
</style>