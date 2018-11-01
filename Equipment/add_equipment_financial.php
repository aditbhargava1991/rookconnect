<?php //Equipment Financial Information
if (strpos($value_config, ','."Purchase Date".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Date Purchased:</label>
    <div class="col-sm-8">
        <input name="purchase_date" value="<?= $purchase_date ?>" type="text" class="form-control datepicker">
    </div>
</div>
<?php }

if (strpos($value_config, ','."Purchase Amount".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Purchased Amount:</label>
    <div class="col-sm-8">
        <input name="purchase_amt" value="<?= $purchase_amt ?>" type="number" min="0" step="any" class="form-control">
    </div>
</div>
<?php }

if (strpos($value_config, ','."Purchase KM".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">KM on Date Purchased:</label>
    <div class="col-sm-8">
        <input name="purchase_km" value="<?= $purchase_km ?>" type="number" min="0" step="any" class="form-control">
    </div>
</div>
<?php }
if (strpos($value_config, ','."Sale Date".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Date of Sale:</label>
    <div class="col-sm-8">
        <input name="sale_date" value="<?= $sale_date ?>" type="text" class="form-control datepicker">
    </div>
</div>
<?php }

if (strpos($value_config, ','."Sale Amount".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Sold Amount:</label>
    <div class="col-sm-8">
        <input name="sale_amt" value="<?= $sale_amt ?>" type="number" min="0" step="any" class="form-control">
    </div>
</div>
<?php }

if (strpos($value_config, ','."Bill of Sale".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Bill of Sale:</label>
    <div class="col-sm-8">
		<?php if($bill_of_sale != '' && file_exists('download/'.$bill_of_sale)) {
			echo '<a href="download/'.$bill_of_sale.'" target="_blank">View</a> - <a href="" onclick="$(\'[name=bill_of_sale_current]\').val(\'\'); return false;"> Delete</a>'; ?>
			<input type="hidden" name="bill_of_sale_current" value="<?php echo $bill_of_sale; ?>" />
		<?php } ?>
		<input name="bill_of_sale" type="file" data-filename-placement="inside" class="form-control" />
    </div>
</div>
<?php }

if (strpos($value_config, ','."Expense History".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <h2>Expense History</h2>
	<?php $expense_access = 'read_only';
	$query_expenses = "SELECT * FROM equipment_expenses WHERE `equipmentid`='$equipmentid'";
	echo "<div id='no-more-tables'><table class='table table-bordered'>";
	echo "<tr class='hidden-xs hidden-sm'>";
	$colspan = $totalspan = $tempcol = 0;
	echo '<th>Heading</th>';
	$tempcol++;
	$colspan++;
	if($equipmentid == 'ALL') {
		echo '<th>Equipment</th>';
		$tempcol++;
		$colspan++;
		$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `deleted`=0"),MYSQLI_ASSOC);
	}
	$equipment_expense_fields = array_filter(explode(',',trim(get_config($dbc, 'equipment_expense_fields'),',')));
	foreach($equipment_expense_fields as $field) {
		echo '<th>';
		if($field == 'Description') {
			echo 'Description';
			$tempcol++;
			$colspan++;
		} else if($field == 'Country') {
			echo 'Country of Expense';
			$tempcol++;
			$colspan++;
		} else if($field == 'Province') {
			echo 'Province of Expense';
			$tempcol++;
			$colspan++;
		} else if($field == 'Date') {
			echo 'Expense Date';
			$tempcol++;
			$colspan++;
		} else if($field == 'Receipt') {
			echo 'Receipt';
			$tempcol++;
			$colspan++;
		} else if($field == 'Amount') {
			$tips_and_tax = [];
			if(strpos($equipment_expense_fields,'Tax') !== FALSE) {
				$tips_and_tax[] = 'Tax';
			}
			echo 'Amount'.(count($tips_and_tax) > 0 ? " Before ".implode(' & ', $tips_and_tax) : '');
			$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
			$colspan++;
		} else if($field == 'HST') {
			echo 'HST';
			$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
			$colspan++;
		} else if($field == 'PST') {
			echo 'PST';
			$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
			$colspan++;
		} else if($field == 'GST') {
			echo 'GST';
			$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
			$colspan++;
		} else if($field == 'Total') {
			echo 'Total';
			$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
			$colspan++;
		}
		echo '</th>';
	}
	echo '<th>Status</th></tr>';
	$total_amount = 0;
	$total_hst = 0;
	$total_pst = 0;
	$total_gst = 0;
	$total_total = 0;

	$categories = [ 'Oil Change',
		'Tire Rotation',
		'Tune Up',
		'Gas',
		'Flat Time',
		'Emergency Service',
		'Registration',
		'Insurance' ];

	foreach($categories as $i => $category_value) {
		$result = mysqli_query($dbc, $query_expenses." AND `category`='$category_value'");
		$row_count = mysqli_num_rows($result);
		
		echo "<tbody>";

		for($i = 0; $i < $row_count; $i++) {
			$id = $cur_equip = $category = $description = $country = $province = $date = $receipt = $amount = $hst = $pst = $gst = $total = $status = '';
			$reimburse = 1;
			if($row = mysqli_fetch_array($result)) {
				$id = $row['expenseid'];
				$cur_equip = $row['equipmentid'];
				$category = $row['category'];
				$description = strip_tags(html_entity_decode($row['description']));
				$country = $row['country'];
				$province = $row['province'];
				$date = $row['ex_date'];
				$receipt = $row['ex_file'];
				$amount = $row['amount'];
				$hst = $row['hst'];
				$pst = $row['pst'];
				$gst = $row['gst'];
				$total = $row['total'];
				$status = $row['status'];
			}
			
			echo "<tr>";
			if($status != 'Rejected') {
				echo "<input type='hidden' name='submitted_expenseid[]' value='".$id."'>";
				echo '<td data-title="Expense Heading">'.$category_value.'</td>';
				if($equipmentid == 'ALL') {
					$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='$cur_equip'"));
					echo '<td data-title="Equipment">'.$equipment['category'].' Unit #' .$equipment['unit_number']. '</td>';
				}
				foreach($equipment_expense_fields as $field) {
					if($field == 'Description') {
						echo '<td data-title="Description">' .$description. '</td>';
					} else if($field == 'Country') {
						echo '<td data-title="Country">' .$country. '</td>';
					} else if($field == 'Province') {
						echo '<td data-title="Province">' .$province. '</td>';
					} else if($field == 'Date') {
						echo '<td data-title="Expense Date">' .$date. '</td>';
					} else if($field == 'Receipt') {
						echo '<td data-title="Receipt">'.($receipt != '' ? '<a href="download/'.$receipt.'" target="_blank">View</a>' : '').'</td>';
					} else if($field == 'Amount') {
						echo '<td data-title="Amount"><input type="hidden" name="amount[]" value="'.$amount.'" disabled>' . $amount . '</td>';
					} else if($field == 'HST') {
						echo '<td data-title="'.$hst_name.'"><input type="hidden" name="hst[]" value="'.$hst.'" disabled>' . $hst . '</td>';
					} else if($field == 'PST') {
						echo '<td data-title="'.$pst_name.'"><input type="hidden" name="pst[]" value="'.$pst.'" disabled>' . $pst . '</td>';
					} else if($field == 'GST') {
						echo '<td data-title="'.$gst_name.'"><input type="hidden" name="gst[]" value="'.$gst.'" disabled>' . $gst . '</td>';
					} else if($field == 'Total') {
						echo '<td data-title="Total"><input type="hidden" name="total[]" value="'.$total.'" disabled>' . $total . '</td>';
					}
				}
				echo '<td data-title="Status">'.($status == '' ? 'Pending' : $status).'</td>';
			}
			echo "</tr>";
			if($status != 'Rejected') {
				$total_amount += (float)$amount;
				$total_hst += (float)$hst;
				$total_pst += (float)$pst;
				$total_gst += (float)$gst;
				$total_total += (float)$total;
			}
		}
		echo "</tbody>";
	}

	echo "<tr>";
	echo "<td colspan='$totalspan'><b>Total</b></td>";
	foreach($equipment_expense_fields as $i => $field) {
		if($field == 'Amount') {
			echo '<td data-name="total_amt" data-title="Amount"><b>$' . number_format($total_amount, 2, '.', '') . '</b></td>';
		} else if($field == 'HST') {
			echo '<td data-name="total_hst" data-title="HST"><b>$' . number_format($total_hst, 2, '.', '') . '</b></td>';
		} else if($field == 'PST') {
			echo '<td data-name="total_pst" data-title="PST"><b>$' . number_format($total_pst, 2, '.', '') . '</b></td>';
		} else if($field == 'GST') {
			echo '<td data-name="total_gst" data-title="GST"><b>$' . number_format($total_gst, 2, '.', '') . '</b></td>';
		} else if($field == 'Total') {
			echo '<td data-name="total_total" data-title="Total"><b>$' . number_format($total_total, 2, '.', '') . '</b></td>';
		} else if($i >= $totalspan) {
			echo "<td></td>";
		}
	} ?><td></td></tr>

	</table>
	</div>
</div>
<?php }

if (strpos($value_config, ','."Service History".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <h2>Service History</h2>
	<div id="no-more-tables">
		<?php $service_records = mysqli_query($dbc, "SELECT * FROM equipment_service_record WHERE `equipmentid`='$equipmentid'");
		$service_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='service_record' AND equipment_dashboard IS NOT NULL"))['equipment_dashboard'];
		if(mysqli_num_rows($service_records) > 0) {
			echo "<table class='table table-bordered'>";
			echo "<tr class='hidden-xs hidden-sm'>";
			if (strpos($service_config, ','."Service Date".',') !== FALSE) {
				echo '<th>Service Date</th>';
			}
			if (strpos($service_config, ','."Advised Service Date".',') !== FALSE) {
				echo '<th>Advised Service Date</th>';
			}
			if (strpos($service_config, ','."Service Type".',') !== FALSE) {
				echo '<th>Service Tab</th>';
			}
			if (strpos($service_config, ','."Inventory".',') !== FALSE) {
				echo '<th>Inventory</th>';
			}
			if (strpos($service_config, ','."Description of Job".',') !== FALSE) {
				echo '<th>Description of Job</th>';
			}
			if (strpos($service_config, ','."Service Record Mileage".',') !== FALSE) {
				echo '<th>Service Record Mileage</th>';
			}
			if (strpos($service_config, ','."Hours".',') !== FALSE) {
				echo '<th>Hours</th>';
			}
			if (strpos($service_config, ','."Completed".',') !== FALSE) {
				echo '<th>Completed</th>';
			}
			if (strpos($service_config, ','."Staff".',') !== FALSE) {
				echo '<th>Staff</th>';
			}
			if (strpos($service_config, ','."Vendor".',') !== FALSE) {
				echo '<th>Vendor</th>';
			}
			if (strpos($service_config, ','."Service Record Cost".',') !== FALSE) {
				echo '<th>Service Record Cost</th>';
			}
			echo "<th>Function</th>";
			echo "</tr>";
			while($service_row = mysqli_fetch_array( $service_records ))
			{
				echo '<tr>';
				$equipment = $service_row['unit_number'];
				$equipment .= ' : '.$service_row['serial_number'];
				$equipment .= ' : '.$service_row['type'];
				$equipment .= ' : '.$service_row['category'];
				$equipment .= ' : '.$service_row['make'];
				$equipment .= ' : '.$service_row['model'];
				$equipment .= ' : '.$service_row['year_purchased'];
				$equipment .= ' : '.$service_row['mileage'];

				if (strpos($service_config, ','."Service Date".',') !== FALSE) {
					echo '<td data-title="Srv. Date">' . $service_row['service_date'] . '</td>';
				}
				if (strpos($service_config, ','."Advised Service Date".',') !== FALSE) {
					echo '<td data-title="Advised Srv. Date">' . $service_row['advised_service_date'] . '</td>';
				}
				if (strpos($service_config, ','."Service Type".',') !== FALSE) {
					echo '<td data-title="Service Tab">' . $service_row['service_type'] . '</td>';
				}

				if (strpos($service_config, ','."Inventory".',') !== FALSE) {
					$inventoryid = $service_row['inventoryid'];
					$inventory = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventoryid, code, category, sub_category FROM inventory WHERE inventoryid='$inventoryid'"));
					if($inventoryid != '') {
						echo '<td data-title="Inventory Item">' . $inventory['code'].' : '.$inventory['category']. ' : '.$inventory['sub_category'] . '</td>';
					} else {
						echo '<td>-</td>';
					}
				}

				if (strpos($service_config, ','."Description of Job".',') !== FALSE) {
					echo '<td data-title="Job Desc">' . $service_row['description_of_job'] . '</td>';
				}
				if (strpos($service_config, ','."Service Record Mileage".',') !== FALSE) {
					echo '<td data-title="Srv Rec. Mileage">' . $service_row['service_record_mileage'] . '</td>';
				}
				if (strpos($service_config, ','."Hours".',') !== FALSE) {
					echo '<td data-title="Hours">' . $service_row['service_record_hours'] . '</td>';
				}
				if (strpos($service_config, ','."Completed".',') !== FALSE) {
					echo '<td data-title="Completed">' . $service_row['completed'] . '</td>';
				}

				if (strpos($service_config, ','."Staff".',') !== FALSE) {
					$contactid = $service_row['contactid'];
					$staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name FROM contacts WHERE contactid='$contactid'"));
					if($contactid != '') {
						echo '<td data-title="Staff">' . decryptIt($staff['first_name']).' '.decryptIt($staff['last_name']) . '</td>';
					} else {
						echo '<td>-</td>';
					}
				}
				if (strpos($service_config, ','."Vendor".',') !== FALSE) {
					$vendorid = $service_row['vendorid'];
					$vendor = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name FROM contacts WHERE contactid='$vendorid'"));
					if($vendorid != '') {
						echo '<td data-title="Vendor">' . decryptIt($vendor['name']) . '</td>';
					} else {
						echo '<td>-</td>';
					}
				}
				if (strpos($service_config, ','."Service Record Cost".',') !== FALSE) {
					echo '<td data-title="Srv. Cost">' . $service_row['cost'] . '</td>';
				}

				echo '<td data-title="Function">';
					echo '<a href=\'add_equipment_service_record.php?servicerecordid='.$service_row['servicerecordid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'>Edit</a>';
				echo '</td>';

				echo "</tr>";
			}

			echo '</table>';
		} else {
			echo "<h2>No Record Found.</h2>";
		} ?>
	</div>
</div>
<?php }

if (strpos($value_config, ','."View Purchase Amount".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Purchased Amount:</label>
    <div class="col-sm-8">
        $<?= number_format($purchase_amt,2) ?>
    </div>
</div>
<?php }

if (strpos($value_config, ','."Invoiced Amt".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Total Dollars Invoiced:</label>
    <div class="col-sm-8">
        $<?= number_format($invoiced_amt,2) ?>
    </div>
</div>
<?php }

if (strpos($value_config, ','."Expenses".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Total Expenses:</label>
    <div class="col-sm-8">
        $<?= number_format($expense_total,2) ?>
    </div>
</div>
<?php }

if (strpos($value_config, ','."View Sale Amount".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Sold Amount:</label>
    <div class="col-sm-8">
        $<?= number_format($sale_amt,2) ?>
    </div>
</div>
<?php }

if (strpos($value_config, ','."Profit Loss".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Profit &amp; Loss:</label>
    <div class="col-sm-8">
        $<?= number_format($profit_loss,2) ?>
    </div>
</div>
<?php }
