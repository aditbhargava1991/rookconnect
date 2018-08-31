<div class='main_frame' id='no-more-tables'>
	<?php
	$db_conf = get_config($dbc, 'pos_db_rate_fields');
	if(str_replace(',','',$db_conf) == '') {
		$db_conf = ",card,annual,history,function,";
	}
	$sql = "SELECT r.*, IFNULL(p.`name`,'N/A') position_name FROM `company_rate_card` r LEFT JOIN `positions` p ON r.`item_id` = p.`position_id` OR (r.`item_id`=0 AND r.`description`=p.`name`) AND p.`deleted` = 0 WHERE r.`deleted` = 0 AND r.`tile_name` LIKE 'Position'";
	$count_sql = "SELECT COUNT(`companyrcid`) numrows FROM `company_rate_card` WHERE `deleted` = 0 AND `tile_name` LIKE 'Position'";
	$result = mysqli_query($dbc, $sql);
	
	if(mysqli_num_rows($result) > 0):
		// Pagination Configuration
		$rowsPerPage = 25;
		$pageNum = 1;
		if(isset($_GET['page'])) {
			$pageNum = $_GET['page'];
		}
		$offset = ($pageNum - 1) * $rowsPerPage;
		echo display_pagination($dbc, $count_sql, $pageNum, $rowsPerPage);
		
		// Table Headers ?>
		<table class="table table-bordered table-striped">
            <thead>
			<tr class="hidden-xs hidden-sm">
				<th>Rate Card</th>
				<?php if(strpos($db_conf,'card') !== false): ?>
					<th>Position</th><?php endif; ?>
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
				<?php if(strpos($db_conf,'history') !== false): ?>
					<th>History</th><?php endif; ?>
				<?php if(strpos($db_conf,'function') !== false): ?>
					<th>Functions</th><?php endif; ?>
			</tr>
            </thead>
		<?php // Table Rows
		while($row = mysqli_fetch_array($result)) {
			echo '<tr>
				<td data-title="Rate Card">'.(empty($row['rate_card_name']) ? 'Universal' : $row['rate_card_name']).'</td>';
			if(strpos($db_conf,'card') !== false):
				echo '<td data-title="Position">' . $row['position_name'] . '</td>';
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
				$staff_list = [];
				foreach(explode(',',$row['alert_staff']) as $staffid) {
					if($staffid > 0) {
						$staff_list[] = get_contact($dbc, $staffid);
					}
				}
				echo implode(', ',$staff_list);
				echo '</td>';
			endif;
			if(strpos($db_conf,'created_by') !== false):
				echo '<td data-title="Created By">' . get_contact($dbc, $row['created_by']) . '</td>';
			endif;
			if(strpos($db_conf,'annual') !== false):
				echo '<td data-title="Annual Rate">' . $row['annual'] . '</td>';
			endif;
			if(strpos($db_conf,'monthly') !== false):
				echo '<td data-title="Monthly Rate">' . $row['monthly'] . '</td>';
			endif;
			if(strpos($db_conf,'semi_month') !== false):
				echo '<td data-title="Semi-Monthly Rate">' . $row['semi_month'] . '</td>';
			endif;
			if(strpos($db_conf,'weekly') !== false):
				echo '<td data-title="Weekly Rate">' . $row['weekly'] . '</td>';
			endif;
			if(strpos($db_conf,'daily') !== false):
				echo '<td data-title="Daily Rate">' . $row['daily'] . '</td>';
			endif;
			if(strpos($db_conf,'hourly') !== false):
				echo '<td data-title="Hourly Rate">' . $row['hourly'] . '</td>';
			endif;
			if(strpos($db_conf,'hourly_work') !== false):
				echo '<td data-title="Hourly Rate (Work)">' . $row['hourly_work'] . '</td>';
			endif;
			if(strpos($db_conf,'hourly_travel') !== false):
				echo '<td data-title="Hourly Rate (Travel)">' . $row['hourly_travel'] . '</td>';
			endif;
			if(strpos($db_conf,'field_day_actual') !== false):
				echo '<td data-title="Field Day Rate (Actual Cost)">' . $row['field_day_actual'] . '</td>';
			endif;
			if(strpos($db_conf,'field_day_bill') !== false):
				echo '<td data-title="Field Day Rate (Billable Rate)">' . $row['field_day_bill'] . '</td>';
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
			if(strpos($db_conf,'history') !== false):
				echo '<td data-id="'.$row['companyrcid'].'" data-title="History" class="history-link"><a>View Changes</a></td>';
			endif;
			if(strpos($db_conf,'function') !== false):
				echo '<td data-title="Functions" style="text-align:center;">';
				if(vuaed_visible_function($dbc, 'rate_card')) { ?>
					<a href="?card=position&type=position&status=add&id=<?php echo $row['companyrcid']; ?>">Edit</a>
					| <a onclick="return confirm('Are you sure you want to delete this rate card?');" href="../delete_restore.php?action=delete&position_rate_id=<?php echo $row['companyrcid']; ?>">Delete</a>
				<?php } else {
					echo '<a href="?card=position&type=position&status=show&id='.$row['companyrcid'].'">View</a>';
				}
			endif;
			echo '</tr>';
		} ?>
		</table>
		
		<?php // End Pagination
		echo display_pagination($dbc, $count_sql, $pageNum, $rowsPerPage);
	else:
		echo "<h2>No Active Position Rate Cards Found.</h2>";
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
	$('#iframe_instead_of_window').attr('src', 'rate_card.php?card=position&status=history&id='+id);
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