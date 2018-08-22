<?php if(empty($current_url)) {
	$action_url = WEBSITE_URL.'/Field Jobs/field_payroll.php';
} ?>
<script>
function form_action_set() {
	$('form').attr('action','<?php echo $action_url; ?>');
}
</script>
<div class="col-md-12">

<h1 class="triple-pad-bottom">Payroll Reporting</h1>
<?php if (strpos($tab_config,',sites,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'sites' ) === true) { ?>
	<a href='field_sites.php'><button type="button" class="btn brand-btn mobile-block" >Sites</button></a>
<?php } ?>
<?php if (strpos($tab_config,',jobs,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'jobs' ) === true) { ?>
	<a href='field_jobs.php'><button type="button" class="btn brand-btn mobile-block" >Jobs</button></a>
<?php } ?>
<?php if (strpos($tab_config,',foreman,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'foreman_sheet' ) === true) { ?>
	<a href='field_foreman_sheet.php'><button type="button" class="btn brand-btn mobile-block" >Foreman Sheet</button></a>
<?php } ?>
<?php if (strpos($tab_config,',po,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'po' ) === true) { ?>
	<a href='field_po.php'><button type="button" class="btn brand-btn mobile-block" >PO</button></a>
<?php } ?>
<?php if (strpos($tab_config,',work,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'work_ticket' ) === true) { ?>
	<a href='field_work_ticket.php'><button type="button" class="btn brand-btn mobile-block" >Work Ticket</button></a>
<?php } ?>
<?php if (strpos($tab_config,',invoice,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'invoices' ) === true) { ?>
	<a href='field_invoice.php?paytype=Unpaid'><button type="button" class="btn brand-btn mobile-block" >Outstanding Invoices</button></a>
	<a href='field_invoice.php?paytype=Paid'><button type="button" class="btn brand-btn mobile-block" >Paid Invoices</button></a>
<?php } ?>
<?php if (strpos($tab_config,',payroll,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'payroll' ) === true) { ?>
	<a href='field_payroll.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Payroll</button></a>
<?php } ?>

<form id="form4" name="form4" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
	<button	type="submit" name="payroll_pdf" value="Submit1" class="btn brand-btn btn-lg	pull-right" style="" onclick="form_action_set();">Export CSV</button>
	<button	type="submit" name="payroll_simple" value="Submit1" class="btn brand-btn btn-lg	pull-right" style="" onclick="form_action_set();">Export Simplified CSV</button>
	<?php
	$s_start_date = date('Y-m-d');
	$s_end_date = date('Y-m-d');
	if(!empty($_POST['s_start_date'])) {
		$s_start_date = $_POST['s_start_date'];
	}
	if(!empty($_POST['s_end_date'])) {
		$s_end_date = $_POST['s_end_date'];
	}
	?>
	<div class="form-group">
		<label for="site_name" class="col-sm-1 control-label">Start Date:</label>
		<div class="col-sm-2">
			<input name="s_start_date" type="text" class="datepicker" value="<?php echo $s_start_date; ?>">
		</div>

		<label for="first_name" class="col-sm-1 control-label">End Date:</label>
		<div class="col-sm-2">
			<input name="s_end_date" type="text" class="datepicker" value="<?php echo $s_end_date; ?>">
		</div>

		<button type="submit" name="reporting_client" value="Submit" class="btn brand-btn mobile-block">Submit</button>
	</div>

	<?php
		if(isset($_POST['reporting_client'])) {
			$s_start_date = $_POST['s_start_date'];
			$s_end_date = $_POST['s_end_date'];

			$head_date = '';
			$display_date = $s_start_date;
			$head_reg_ot = '';

			$now = strtotime($s_end_date);
			$your_date = strtotime($s_start_date);
			$datediff = $now - $your_date;
			$total_days = floor($datediff/(60*60*24));

			echo "<table class='table table-bordered table-striped'>";
			echo "<thead><tr class='hidden-xs hidden-sm'><th>&nbsp;</th>";
			for($i=0;$i<=$total_days;$i++) {
				echo "<th>".$display_date."</th>";
				$display_date = date('Y-m-d',strtotime($display_date . "+1 days"));
			}
			echo "</tr></thead>";
			echo "<tr class='hidden-xs hidden-sm'><th>Name</th>";
			for($i=0;$i<=$total_days;$i++) {
				echo "<th>Reg-OT-Sub-Travel</th>";
			}
			echo "<th>Position</th>";
			echo "<th>Total Reg</th>";
			echo "<th>Total OT</th>";
			echo "<th>Total Sub</th>";
			echo "<th>Total Travel</th>";
			echo "</tr>";
			//AND p.created_date >= $s_start_date AND p.created_date <= $s_end_date
			$head = "SELECT e.*, p.* FROM contacts e, field_payroll p WHERE e.contactid = p.contactid GROUP BY e.contactid, p.positionid ORDER BY e.last_name";

			$result = mysqli_query($dbc, $head);

			while($row = mysqli_fetch_array( $result )) {
				$total_reg = 0;
				$total_ot = 0;
				$total_sub = 0;
				$total_travel = 0;
				$display_date = $_POST['s_start_date'];

				$employeeid = $row['contactid'];
				$positionid = $row['positionid'];

				$each_td = '';
				for($i=0;$i<=$total_days;$i++) {
					$my = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(reg) AS TOTAL_REG, SUM(ot) AS TOTAL_OT, SUM(sub) AS TOTAL_SUB, SUM(travel) AS TOTAL_TRAVEL FROM field_payroll WHERE contactid = '$employeeid' AND positionid = '$positionid' AND created_date = '$display_date'"));

					if($my['TOTAL_REG'] == '' && $my['TOTAL_OT'] == '' && $my['TOTAL_SUB'] == '') {
						$each_td .= '<td>-</td>';
					} else {
						$each_td .= '<td>' . $my['TOTAL_REG'].' - '.$my['TOTAL_OT'].' - $'.$my['TOTAL_SUB'].' - '.$my['TOTAL_TRAVEL'].'</td>';
					}
					$display_date = date('Y-m-d',strtotime($display_date . "+1 days"));
					$total_reg += $my['TOTAL_REG'];
					$total_ot += $my['TOTAL_OT'];
					$total_sub += $my['TOTAL_SUB'];
					$total_travel += $my['TOTAL_TRAVEL'];
				}

				if($total_reg != 0 || $total_ot != 0 || $total_sub != 0 || $total_travel != 0) {
					echo '<tr>';
					echo "<td>".get_staff($dbc, $employeeid)."</td>";
					echo $each_td;
					echo '<td>' . '</td>';
					echo '<td>' . $total_reg. '</td>';
					echo '<td>' . $total_ot. '</td>';
					echo '<td>' . $total_sub. '</td>';
					echo '<td>' . $total_travel. '</td>';
					echo '</tr>';
				}
			}
			echo "</table>";
//

		} else {
			$query_check_credentials = "SELECT *, SUM(reg) AS reg_hours, SUM(ot) AS ot_hours, SUM(sub) AS sub_hours, SUM(travel) AS travel_hours FROM field_payroll WHERE DATE(created_date) = CURDATE() GROUP BY created_date, contactid, positionid";

			$result = mysqli_query($dbc, $query_check_credentials);
			echo "<table class='table table-bordered table-striped'>";
			echo "<thead><tr class='hidden-xs hidden-sm'>
			<th></th>
			<th>".date('Y-m-d')."</th>
			</tr>";
			echo "<tr class='hidden-xs hidden-sm'>
			<th>Name</th>
			<th>Reg-OT-Sub</th>
			<th>Position</th>
			<th>Total Reg</th>
			<th>Total OT</th>
			<th>Total Sub</th>
			<th>Total Travel</th>
			</tr></thead>";

			$employeeid = 0;
			$positions = '';
			$hour_list = [0,0,0];
			$reg_hours = '';
			$ot_hours = '';
			$sub_hours = '';
			$travel_hours = '';

			while($row = mysqli_fetch_array( $result )) {
				if($employeeid != $row['contactid'] && $employeeid != 0) {
					echo "<tr>";
					echo '<td data-title="Development Link">' . get_staff($dbc, $employeeid).'</td>';
					echo '<td data-title="Development Link">' . $hour_list[0].' - '.$hour_list[1].' - '.$hour_list[2].'</td>';
					echo '<td data-title="Development Link">' . $positions . '</td>';
					echo '<td data-title="Development Link">' . $reg_hours. '</td>';
					echo '<td data-title="Development Link">' . $ot_hours. '</td>';
					echo '<td data-title="Development Link">' . $sub_hours. '</td>';
					echo '<td data-title="Development Link">' . $travel_hours. '</td>';

					echo "</tr>";
					$positions = '';
					$hour_list = [0,0,0];
					$reg_hours = '';
					$ot_hours = '';
					$sub_hours = '';
					$travel_hours = '';
				}
				$employeeid = $row['contactid'];
				$positionid = $row['positionid'];

				$positions .= get_positions($dbc, $positionid, 'name').':<br />';
				$hour_list[0] += $row['reg_hours'];
				$hour_list[1] += $row['ot_hours'];
				$hour_list[2] += $row['sub_hours'];
				$reg_hours .= $row['reg_hours'].'<br />';
				$ot_hours .= $row['ot_hours'].'<br />';
				$sub_hours .= $row['sub_hours'].'<br />';
				$travel_hours .= $row['travel_hours'].'<br />';
			}
			echo "<tr>";
			echo '<td data-title="Development Link">' . get_staff($dbc, $employeeid).'</td>';
			echo '<td data-title="Development Link">' . $hour_list[0].' - '.$hour_list[1].' - '.$hour_list[2].'</td>';
			echo '<td data-title="Development Link">' . $positions . '</td>';
			echo '<td data-title="Development Link">' . $reg_hours. '</td>';
			echo '<td data-title="Development Link">' . $ot_hours. '</td>';
			echo '<td data-title="Development Link">' . $sub_hours. '</td>';
			echo '<td data-title="Development Link">' . $travel_hours. '</td>';

			echo "</tr>";
			echo '</table>';

		}

	?>

</form>

</div>
		<!--<a href="/home.php" class="btn brand-btn mobile-block">Back</a>-->

</div>