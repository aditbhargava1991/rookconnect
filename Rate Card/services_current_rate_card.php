<div class="notice double-gap-bottom popover-examples">
    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11"><span class="notice-name">NOTE:</span>
    <?php if($_GET['category'] == 'inactive') { ?>
    In this section you can review all Inactive and Expired rates that your company has offered. Once a rate for a service expires, it's logged here for your records and ongoing review.
    <?php } else { ?>
    The Rate Card tile is where you assign price points of all services being offered by your business. In the Active Rate Card section you can review all current rates being offered by your business.<br>
    Click Edit to add end dates for current rates, and click Add Rate Card to add new price points for services. In this section you can add multiple rates for each service by selected start and end dates, ensuring of course that prices don't overlap. You must assign an effective start date for a rate in order to have an active rate for a service.
    <?php } ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class='main_frame' id='no-more-tables'>
	<?php
	$db_conf = ','.get_config($dbc, 'services_db_rate_fields').',';
    $rowsPerPage = 25;
    $pageNum = 1;
    if(isset($_GET['page'])) {
        $pageNum = $_GET['page'];
    }
    $offset = ($pageNum - 1) * $rowsPerPage;

    if(isset($_GET['t'])) {
        $scat = $_GET['t'];
    } else {
        $scat = '';
    }

    if($_GET['category'] == 'inactive') {
	    $sql1 = "SELECT src.*, s.`service_code`, s.`heading` FROM company_rate_card src LEFT JOIN services s ON src.item_id=s.serviceid WHERE src.deleted = 0 AND DATE(src.end_date) != '0000-00-00'  AND (DATE(src.end_date) < DATE(NOW()) OR DATE(src.start_date) > DATE(NOW())) AND s.category='$scat' ORDER BY `src`.`end_date`, s.`heading` LIMIT $offset, $rowsPerPage";
	    $count_sql = "SELECT COUNT(src.serviceratecardid) numrows FROM company_rate_card src LEFT JOIN services s ON src.item_id=s.serviceid WHERE src.deleted = 0 AND DATE(src.end_date) != '0000-00-00' AND (DATE(src.end_date) < DATE(NOW()) OR DATE(src.start_date) > DATE(NOW())) AND s.category='$scat'";
    } else {
	    $sql1 = "SELECT src.*, s.`serviceid`,s.`service_code`,s.`heading` FROM services s LEFT JOIN company_rate_card src ON s.serviceid = src.item_id WHERE s.`deleted`=0 AND IFNULL(src.deleted,0) = 0 AND DATE(NOW()) >= DATE(IFNULL(src.start_date,NOW())) AND (DATE(IFNULL(src.end_date,NOW())) >= DATE(NOW()) OR IFNULL(src.end_date,'0000-00-00') = '0000-00-00') AND s.category='$scat' ORDER BY s.`heading` LIMIT $offset, $rowsPerPage";
	    $count_sql = "SELECT COUNT(*) FROM services s LEFT JOIN company_rate_card src ON s.serviceid = src.item_id WHERE s.`deleted`=0 AND IFNULL(src.deleted,0) = 0 AND DATE(NOW()) >= DATE(IFNULL(src.start_date,NOW())) AND (DATE(IFNULL(src.end_date,NOW())) >= DATE(NOW()) OR IFNULL(src.end_date,'0000-00-00') = '0000-00-00') AND s.category='$scat'";
    }

	$result = mysqli_query($dbc, $sql1);
	if(mysqli_num_rows($result) > 0) {
		// Pagination Configuration
		echo display_pagination($dbc, $count_sql, $pageNum, $rowsPerPage);

		 ?>
		<table class="table table-bordered table-striped">
            <thead>
			<tr class="hidden-xs hidden-sm">
					<th>Code</th>
                    <th>Heading</th>
					<th>Effective Dates</th>
					<?php if(strpos($db_conf,'alert_date') !== false): ?>
						<th>Alert Date</th><?php endif; ?>
					<?php if(strpos($db_conf,'alert_staff') !== false): ?>
						<th>Alert Staff</th><?php endif; ?>
					<?php if(strpos($db_conf,'created_by') !== false): ?>
						<th>Created By</th><?php endif; ?>
				    <th>Rate</th>
                    <th>Admin Fee</th>
					<?php if(strpos($db_conf,'uom') !== false): ?>
						<th>UoM</th><?php endif; ?>
                    <th>History</th>
                    <th>Functions</th>
			</tr>
            </thead>
		<?php // Table Rows
		while($row = mysqli_fetch_array($result)) {
			echo '<tr>';
				echo '<td data-title="Service Code">' . $row['service_code'] . '</td>';
				echo '<td data-title="Service">' . $row['heading'] . '</td>';
				if($row['serviceratecardid'] > 0) {
					echo '<td data-title="Effective Dates">' . $row['start_date'].' : '. $row['end_date']. '</td>';
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
					echo '<td data-title="Rate">' . $row['service_rate'] . '</td>';
					echo '<td data-title="Admin Fee">' . $row['admin_fee'] . '</td>';
					if(strpos($db_conf,'uom') !== false):
						echo '<td data-title="Unit of Measure">' . $row['uom'] . '</td>';
					endif;
					echo '<td data-id="'.$row['companyrcid'].'" data-title="history" class="history-link"><a>View Changes</a></td>';
					echo '<td data-type="equipment" data-title="functions" style="text-align:center;"><a href="?card=services&type=services&t='.$_GET['t'].'&status=add&id='.$row['companyrcid'].'">Edit</a> | ';
					echo '<a href="../delete_restore.php?action=delete&serviceratecardid='.$row['companyrcid'].'">Delete</a></td>';
				} else {
					echo '<td data-title="Effective Dates"></td>';
					if(strpos($db_conf,'alert_date') !== false):
						echo '<td data-title="Alert Date"></td>';
					endif;
					if(strpos($db_conf,'alert_staff') !== false):
						echo '<td data-title="Alert Staff"></td>';
					endif;
					if(strpos($db_conf,'created_by') !== false):
						echo '<td data-title="Created By"></td>';
					endif;
					echo '<td data-title="Rate"></td>';
					echo '<td data-title="Admin Fee"></td>';
					if(strpos($db_conf,'uom') !== false):
						echo '<td data-title="Unit of Measure"></td>';
					endif;
					echo '<td data-title="history"></td>';
					echo '<td data-type="equipment" data-title="functions" style="text-align:center;"><a href="?card=services&type=services&t='.$_GET['t'].'&status=add&service='.$row['serviceid'].'">Create</a></td>';
				}
            echo '</tr>';
		} ?>
		</table>

		<?php // End Pagination
		echo display_pagination($dbc, $count_sql, $pageNum, $rowsPerPage);
	} else {
        if(empty($_GET['category'])) {
            echo "<h2>Please select rate card type as Active or Inactive.</h2>";
        } else if(empty($_GET['t'])) {
            echo "<h2>Please select service category.</h2>";
        } else {
		    echo "<h2>No Record Found.</h2>";
        }
	} ?>
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
	$('#iframe_instead_of_window').attr('src', 'rate_card.php?card=services&status=history&id='+id);
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