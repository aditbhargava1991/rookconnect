<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$report_fields = explode(',', get_config($dbc, 'report_operation_fields')); ?>


		<?php $search_status = (!empty($_GET['wo_type']) ? filter_var($_GET['wo_type'],FILTER_SANITIZE_STRING) : 'Approved');
		$search_ticket = '';
		$search_extra_ticket = '';
		$search_expenses = 'No';
		$search_notes = 'No';
		$search_from = '';
		$search_until = '';
		
		if (isset($_POST['search_from'])) {
			$search_from = $_POST['search_from'];
		} else {
			$search_from = date('Y-m-01');
		}
		if (isset($_POST['search_until'])) {
			$search_until = $_POST['search_until'];
		} else {
			$search_until = date('Y-m-d');
		}
		if (isset($_POST['search_ticket'])) {
			$search_ticket = $_POST['search_ticket'];
		}
		if (isset($_POST['search_extra_ticket'])) {
			$search_extra_ticket = $_POST['search_extra_ticket'];
		}
		if (isset($_POST['search_expenses'])) {
			$search_expenses = $_POST['search_expenses'];
		}
		if (isset($_POST['search_notes'])) {
			$search_notes = $_POST['search_notes'];
		} ?>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
			<center><div class="form-group">
				<div class="col-sm-5">
					<label for="search_ticket" class="col-sm-4 control-label">Search By <?= TICKET_NOUN ?>:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a <?= TICKET_NOUN ?> #" name="search_ticket" class="chosen-select-deselect form-control">
							<option value=""></option>
							<?php
							$query = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 ORDER BY `ticketid`");
							while($row = mysqli_fetch_array($query)) { ?>
								<option <?php if ($row['ticketid'] == $search_ticket) { echo " selected"; } ?> value='<?= $row['ticketid'] ?>' ><?= get_ticket_label($dbc, $row) ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<?php if(in_array('filter_extra_billing',$report_fields)) { ?>
					<div class="col-sm-5">
						<label for="search_extra_ticket" class="col-sm-4 control-label">Search By Extra Billing <?= TICKET_NOUN ?>:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select a <?= TICKET_NOUN ?> #" name="search_extra_ticket" class="chosen-select-deselect form-control">
								<option value=""></option>
								<?php
								$query = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `ticket_comment` WHERE `type`='service_extra_billing' AND `deleted`=0) ORDER BY `ticketid`");
								while($row = mysqli_fetch_array($query)) { ?>
									<option <?php if ($row['ticketid'] == $search_extra_ticket) { echo " selected"; } ?> value='<?= $row['ticketid'] ?>' ><?= get_ticket_label($dbc, $row) ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('filter_staff_expenses',$report_fields)) { ?>
					<div class="col-sm-5">
						<label for="search_expenses" class="col-sm-4 control-label">Only <?= TICKET_TILE ?> with Expenses:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select Option" name="search_material" class="chosen-select-deselect form-control">
								<option <?= $search_expenses == 'No' ? 'selected' : '' ?> value="No">Display All</option>
								<option <?= $search_expenses == 'Yes' ? 'selected' : '' ?> value="Yes">Only with Expenses</option>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('filter_ticket_notes',$report_fields)) { ?>
					<div class="col-sm-5">
						<label for="search_notes" class="col-sm-4 control-label">Only <?= TICKET_TILE ?> with Notes:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select Option" name="search_material" class="chosen-select-deselect form-control">
								<option <?= $search_notes == 'No' ? 'selected' : '' ?> value="No">Display All</option>
								<option <?= $search_notes == 'Yes' ? 'selected' : '' ?> value="Yes">Only with Notes</option>
							</select>
						</div>
					</div>
				<?php } ?>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="search_from" type="text" class="datepicker form-control" value="<?php echo $search_from; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="search_until" type="text" class="datepicker form-control" value="<?php echo $search_until; ?>"></div>
				</div>
			<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div>
			<button type="button" onclick="window.location=''" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button></center>
        <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>

            <input type="hidden" name="report_status" value="<?php echo $search_status; ?>">
            <input type="hidden" name="report_ticket" value="<?php echo $search_ticket; ?>">
            <input type="hidden" name="report_extra_ticket" value="<?php echo $search_extra_ticket; ?>">
            <input type="hidden" name="report_expenses" value="<?php echo $search_expenses; ?>">
            <input type="hidden" name="report_notes" value="<?php echo $search_notes; ?>">
            <input type="hidden" name="report_from" value="<?php echo $search_from; ?>">
            <input type="hidden" name="report_until" value="<?php echo $search_until; ?>">
            <br><br>

            <?php
                echo work_orders($dbc, $search_status, $search_ticket, $search_extra_ticket, $search_expenses, $search_notes, $search_from, $search_until);
            ?>

        </form>

<?php
function work_orders($dbc, $status = 'Active', $ticket = '', $extra_ticket = '', $expenses = 'No', $notes = 'No', $from_date = '', $until_date = '', $table_style = '', $table_row_style = '', $grand_total_style = '') {
    $report_data = '';

	$sql = "SELECT `ticketid`, `date_stamp` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table` IN ('Staff','Staff_Tasks') AND `date_stamp` BETWEEN '$from_date' AND '$until_date' AND '$ticket' IN (`ticketid`,'') AND '$extra_ticket' IN (`ticketid`,'') AND `ticketid` NOT IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=1) ".($expenses == 'Yes' ? " AND `ticketid` IN (SELECT `work_order` FROM `expense` WHERE `deleted`=0) " : '').($notes == 'Yes' ? " AND `ticketid` IN (SELECT `ticketid` FROM `ticket_comment` WHERE `deleted`=0) " : '')." GROUP BY `ticketid`, `date_stamp` ORDER BY `date_stamp` ASC";
	$result = mysqli_query($dbc, $sql);
	
	if($result->num_rows == 0) {
		return "<h3>No ".TICKET_TILE." Found</h3>";
	}

    $pageNum = empty($_GET['page']) ? 1 : $_GET['page'];
    $rowsPerPage = empty($_GET['pagerows']) ? 25 : $_GET['pagerows'];
    display_pagination($dbc, "SELECT '".$result->num_rows."' `numrows`", $pageNum, $rowsPerPage, true, 25);
    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>'.TICKET_NOUN.' #</th>
			<th>Staff on Site</th>
			<th>Actual Time</th>';
    $report_data .=  "</tr>";

    $total_time = 0;
    $offset = ($pageNum - 1) * $rowsPerPage;
    $current_row = 0;
    while($time = mysqli_fetch_array( $result ))
    {
        $staff = [];
        $staff_names = [];
        $actual = [];
        $hours = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid`='{$time['ticketid']}' AND `date_stamp`='{$time['date_stamp']}' AND `deleted`=0 AND `src_table` IN ('Staff','Staff_Tasks')");
        while($hour = $hours->fetch_assoc()) {
            $staff[] = $hour['item_id'];
            $staff_names[] = get_contact($dbc, $hour['item_id']).' - '.$hour['position'];
            $actual[] = $hour['hours_tracked'];
            $total_time += $hour['hours_tracked'];
        }

        if($current_row >= $offset && $current_row < $offset + $rowsPerPage) {
            $report_data .= '<tr nobr="true">
                <td data-title="'.TICKET_NOUN.' #:">'.get_ticket_label($dbc, $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='{$time['ticketid']}'")->fetch_assoc()).' on '.$time['date_stamp'].'</td>
                <td data-title="Staff on Site:">'.implode('<br />',$staff_names).'</td>
                <td data-title="Actual Time:">'.implode('<br />',$actual).'</td></tr>';
        }
        $current_row++;
    }
    $report_data .= '<tr nobr="true" style="font-weight:bold;">
        <td colspan="2">Total Hours (from All Pages):</td>
        <td data-title="Total Hours (from All Pages)">'.$total_time.'</td></tr>';

    $report_data .= '</table>';

    return $report_data;
}
?>
<script>
$('document').ready(function() {
    var tables = $('table');

    tables.map(function(idx, table) {
        var rows = $(table).find('tbody > tr');
        rows.map(function(idx, row){
            if(idx%2 == 0) {
                $(row).css('background-color', '#e6e6e6');
            }
        })
    })
})
</script>