<?php /* Expense Output */
if(isset($_POST['submit'])) {
	foreach($_POST['expenseid'] as $key => $id) {
		if($equipmentid == 'ALL') {
			$equipmentid = $_POST['equipmentid'][$key];
		}
		$expense_values = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `expense` WHERE `expenseid`='$id'"));
		$staff = isset($_POST['search_staff']) ? $_POST['search_staff'] : $expense_values['staff'];
		$category = isset($_POST['category']) ?$_POST['category'][$key] : $expense_values['category'];
		$description = isset($_POST['description']) ?$_POST['description'][$key] : $expense_values['description'];
		$country = isset($_POST['country']) ?$_POST['country'][$key] : $expense_values['country'];
		$province = isset($_POST['province']) ?$_POST['province'][$key] : $expense_values['province'];
		$date = isset($_POST['ex_date']) ?$_POST['ex_date'][$key] : $expense_values['ex_date'];
		$amount = isset($_POST['amount']) ?$_POST['amount'][$key] : $expense_values['amount'];
		$hst = isset($_POST['hst']) ?$_POST['hst'][$key] : $expense_values['hst'];
		$pst = isset($_POST['pst']) ?$_POST['pst'][$key] : $expense_values['pst'];
		$gst = isset($_POST['gst']) ?$_POST['gst'][$key] : $expense_values['gst'];
		$total = isset($_POST['total']) ?$_POST['total'][$key] : $expense_values['total'];
		if($description != '' || $date != '' || $amount != '' || $total != '') {
			if($id == '') {
				$sql_expense = "INSERT INTO `equipment_expenses` (`equipmentid`, `category`, `staff`, `description`, `country`, `province`, `ex_date`, `amount`, `pst`, `gst`, `hst`, `total`)
					VALUES ('$equipmentid', '$category', '$staff', '$description', '$country', '$province', '$date', '$amount', '$pst', '$gst', '$hst', '$total')";
					$before_change = '';
	        $history = "New equipment expense Added. <br />";
	        add_update_history($dbc, 'equipment_history', $history, '', $before_change);
			} else {
				$sql_expense = "UPDATE `equipment_expenses` SET `equipmentid`='$equipmentid', `category`='$category', `description`='$description', `country`='$country', `province`='$province', `ex_date`='$date', `amount`='$amount', `pst`='$pst', `gst`='$gst', `hst`='$hst', `total`='$total' WHERE `expenseid`='$id'";
			}//echo $id.'-'.$sql_expense.'<br />';
			mysqli_query($dbc, $sql_expense);
			if($id == '') {
				$id = mysqli_insert_id($dbc);
			}

			$before_change = '';
	    $history = "Equipment expenses Updated. <br />";
	    add_update_history($dbc, 'equipment_history', $history, '', $before_change);
			if($_FILES['ex_file']['name'][$key] != '') {
				if (!file_exists('download')) {
					mkdir('download', 0777, true);
				}
				$basename = $receipt = preg_replace('/[^A-Za-z0-9\.]/','_',$_FILES['ex_file']['name'][$key]);
				$i = 0;echo $receipt;
				while(file_exists('download/'.$receipt)) {
					$receipt = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basename);
				}
				move_uploaded_file($_FILES['ex_file']['tmp_name'][$key], 'download/'.$receipt);
				$before_change = capture_before_change($dbc, 'equipment_expenses', 'ex_file', 'expenseid', $id);
				mysqli_query($dbc, "UPDATE `equipment_expenses` SET `ex_file`='$receipt' WHERE `expenseid`='$id'");
			  $history = capture_after_change('ex_file', $receipt);
			  add_update_history($dbc, 'equipment_history', $history, '', $before_change);
			}
		}
	}

	//ob_clean();
	//header('Location: ?'.$_SERVER['QUERY_STRING']);
} ?>

<script>
$(document).ready(function() {
	$('[name="contact[]"]').each(function() { loadContacts(this); });
	$('tbody').each(function() { $(this).find('tr').not(':last').find('a:contains("Add Row")').closest('span').hide(); });
	$('.datepicker').datepicker('option', { defaultDate: new Date(<?= substr($search_month, 0, 4) ?>, <?= substr($search_month, 5, 2)-1; ?>, 1) });
});
$(document).on('change', 'select[name="province[]"]', function() { calcTotal(); });

function calcTotal() {
	var total_amt = 0;
	var total_hst = 0;
	var total_gst = 0;
	var total_pst = 0;
	var total_total = 0;
	$('[name="amount[]"]').each(function() {
		var amt = +$(this).val() || 0;
		if(amt > 0) {
			$(this).val(amt.toFixed(2));
		}
		total_amt += amt;
	});
	$('[name="gst[]"]').each(function() {
		var rate = 5;
		var amt = $(this).closest('tr').find('[name="amount[]"]').val();
		var province = $(this).closest('tr').find('[name="province[]"] option:selected');
		if(province.val() != '' && province.val() != undefined) {
			rate = province.data('gst');
		}
		if(amt != '' && amt != undefined) {
			$(this).val((Math.round(amt * rate) / 100).toFixed(2));
		}
		total_gst += +$(this).val();
	});
	$('[name="pst[]"]').each(function() {
		var rate = 0;
		var amt = $(this).closest('tr').find('[name="amount[]"]').val();
		var province = $(this).closest('tr').find('[name="province[]"] option:selected');
		if(province.val() != '' && province.val() != undefined) {
			rate = province.data('pst');
		}
		if(amt != '' && amt != undefined) {
			$(this).val((Math.round(amt * rate) / 100).toFixed(2));
		}
		total_pst += +$(this).val();
	});
	$('[name="hst[]"]').each(function() {
		var rate = 0;
		var amt = $(this).closest('tr').find('[name="amount[]"]').val();
		var province = $(this).closest('tr').find('[name="province[]"] option:selected');
		if(province.val() != '' && province.val() != undefined) {
			rate = province.data('hst');
		}
		if(amt != '' && amt != undefined) {
			$(this).val((Math.round(amt * rate) / 100).toFixed(2));
		}
		total_hst += +$(this).val();
	});
	$('[name="total[]"]').each(function() {
		var gst = +$(this).closest('tr').find('[name="gst[]"]').val() || 0;
		var pst = +$(this).closest('tr').find('[name="pst[]"]').val() || 0;
		var hst = +$(this).closest('tr').find('[name="hst[]"]').val() || 0;
		var amt = $(this).closest('tr').find('[name="amount[]"]').val();
		if(amt != '' && amt != undefined) {
			$(this).val(((+amt) + hst + pst + gst).toFixed(2));
		}
		total_total += +$(this).val();
	});
	$('td[data-name="total_amt"]').html("<b>$"+total_amt.toFixed(2)+"</b>");
	$('td[data-name="total_gst"]').html("<b>$"+total_gst.toFixed(2)+"</b>");
	$('td[data-name="total_pst"]').html("<b>$"+total_pst.toFixed(2)+"</b>");
	$('td[data-name="total_hst"]').html("<b>$"+total_hst.toFixed(2)+"</b>");
	$('td[data-name="total_total"]').html("<b>$"+total_total.toFixed(2)+"</b>");
}

function removeLine(row) {
	var id = row.find('[name="expenseid[]"]').val();
	if(id != '') {
		$.ajax({
			type: "POST",
			url: "equipment_ajax.php?fill=expense_delete",
			data: { expenseid: id },
			success: function(result) {
				console.log(result);
			}
		});
	}
	row.remove();
	$('tbody').find('a:contains("Add Row")').closest('span').show();
	$('tbody').each(function() { $(this).find('tr').not(':last').find('a:contains("Add Row")').closest('span').hide(); });
}
function approveLine(row) {
	var id = row.find('[name="expenseid[]"]').val();
	if(id != '') {
		$.ajax({
			type: "POST",
			url: "equipment_ajax.php?fill=expense_approve",
			data: { expenseid: id },
			success: function(result) {
				console.log(result);
				window.location.replace('');
			}
		});
	}
}
function payLine(row) {
	var id = row.find('[name="expenseid[]"]').val();
	if(id != '') {
		$.ajax({
			type: "POST",
			url: "equipment_ajax.php?fill=expense_pay",
			data: { expenseid: id },
			success: function(result) {
				console.log(result);
				window.location.replace('');
			}
		});
	}
}
function rejectLine(row) {
	var id = row.find('[name="expenseid[]"]').val();
	if(id != '') {
		$.ajax({
			type: "POST",
			url: "equipment_ajax.php?fill=expense_reject",
			data: { expenseid: id },
			success: function(result) {
				console.log(result);
				window.location.replace('');
			}
		});
	}
}
function addRow(button) {
	var last = $(button).closest('tbody').find('tr:last');
	var clone = last.clone();
	clone.find('.form-control').val('');
	resetChosen(clone.find("select[class^=chosen]"));
	clone.find('.datepicker').each(function() {
		$(this).removeAttr('id').removeClass('hasDatepicker');
		$('.datepicker').datepicker({dateFormat: 'yy-mm-dd', changeYear: true, changeMonth: true, yearRange: '1920:2025'});
	});
	last.after(clone);
	$('tbody').each(function() { $(this).find('tr').not(':last').find('a:contains("Add Row")').closest('span').hide(); });
	last.nextAll('tr').find('.form-control').first().focus();
	return false;
}
</script>

<?php
$approval_access = approval_visible_function($dbc, 'equipment');
echo '<div class="form-group pull-right">';
    echo '<span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to Save all Expenses entered."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
    echo '<button type="submit" name="submit" value="save" class="btn brand-btn mobile-block mobile-100 ">Save Expenses</button>';
echo '</div>';
echo '<div class="clearfix"></div>';

echo "<table class='table table-bordered'>";
echo "<tr class='hidden-xs hidden-sm'>";
$colspan = $totalspan = $tempcol = 0;
echo '<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The expense Heading for this item of equipment."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Heading</th>';
$tempcol++;
$colspan++;
if($equipmentid == 'ALL') {
	echo '<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The item of Equipment this expense is associated with."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Equipment</th>';
	$tempcol++;
	$colspan++;
	$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `deleted`=0"),MYSQLI_ASSOC);
}
$equipment_expense_fields = array_filter(explode(',',trim(get_config($dbc, 'equipment_expense_fields'),',')));
foreach($equipment_expense_fields as $field) {
	echo '<th>';
	if($field == 'Description') {
		echo '<span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Description of this equipment expense."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Description';
		$tempcol++;
		$colspan++;
	} else if($field == 'Country') {
		echo '<span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Country in which this expense was incurred."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Country of Expense';
		$tempcol++;
		$colspan++;
	} else if($field == 'Province') {
		echo '<span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Province in which this expense was incurred."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Province of Expense';
		$tempcol++;
		$colspan++;
	} else if($field == 'Date') {
		echo '<span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Date on which the expense was incurred."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Expense Date';
		$tempcol++;
		$colspan++;
	} else if($field == 'Receipt') {
		echo '<span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Attach a photo or scan of the Receipt for this expense."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Receipt';
		$tempcol++;
		$colspan++;
	} else if($field == 'Amount') {
		$tips_and_tax = [];
		if(strpos($equipment_expense_fields,'Tax') !== FALSE) {
			$tips_and_tax[] = 'Tax';
		}
		echo '<span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Amount of the expense before applicable taxes."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Amount'.(count($tips_and_tax) > 0 ? " Before ".implode(' & ', $tips_and_tax) : '');
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'HST') {
		echo '<span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The amount of any applicable harmonized sales tax."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> HST';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'PST') {
		echo '<span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The amount of any applicable provincial sales tax."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> PST';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'GST') {
		echo '<span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Add an Info i: goods and services tax."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> GST';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'Total') {
		echo '<span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Total amount of the expense, including all taxes."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Total';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	}
	echo '</th>';
}
echo '<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Add another row to this expense or delete the expense completely."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Function</th>';
$colspan++;
echo "</tr>";

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
	$row_count += 1;

	echo "<tbody>";

	for($i = 0; $i < $row_count; $i++) {
		$id = $cur_equip = $category = $description = $country = $province = $date = $receipt = $amount = $hst = $pst = $gst = $total = $status = '';
		$country = 'Canada';
		$province = 'AB';
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
		if($status == '' || ($approval_access == 1 && $status == 'Approved')) {
			echo "<input type='hidden' name='expenseid[]' value='".$id."'>";
			echo '<td data-title="Expense Heading">';
			echo '<input type="hidden" name="category[]" value="'.$category_value.'">'.$category_value.'</td>';
			if($equipmentid == 'ALL') {
				echo '<td data-title="Equipment"><select name="equipmentid[]" class="chosen-select-deselect form-control"><option></option>';
				foreach($equip_list as $equip) {
					echo '<option '.($equip['equipmentid'] == $cur_equip ? 'selected' : '').' value="'.$equip['equipmentid'].'">'.$equip['category'].' Unit #'.$equip['unit_number'].'</option>';
				}
				echo '</select></td>';
			}

			foreach($equipment_expense_fields as $field) {
				if($field == 'Description') {
					echo '<td data-title="Description"><input type="text" name="description[]" value="' .$description. '" class="form-control"></td>';
				} else if($field == 'Country') {
					echo '<td data-title="Country"><input type="text" name="country[]" value="' .$country. '" class="form-control"></td>';
				} else if($field == 'Province') {
					echo '<td data-title="Province"><select name="province[]" class="chosen-select-deselect"><option></option>'.
						"<option data-gst='5' data-pst='0' data-hst='0' ".($province == 'AB' ? 'selected' : '')." value='AB'>AB</option>".
						"<option data-gst='5' data-pst='7' data-hst='0' ".($province == 'BC' ? 'selected' : '')." value='BC'>BC</option>".
						"<option data-gst='5' data-pst='8' data-hst='0' ".($province == 'MB' ? 'selected' : '')." value='MB'>MB</option>".
						"<option data-gst='0' data-pst='0' data-hst='15' ".($province == 'NB' ? 'selected' : '')." value='NB'>NB</option>".
						"<option data-gst='0' data-pst='0' data-hst='15' ".($province == 'NL' ? 'selected' : '')." value='NL'>NL</option>".
						"<option data-gst='5' data-pst='0' data-hst='0' ".($province == 'NT' ? 'selected' : '')." value='NT'>NT</option>".
						"<option data-gst='0' data-pst='0' data-hst='15' ".($province == 'NS' ? 'selected' : '')." value='NS'>NS</option>".
						"<option data-gst='5' data-pst='0' data-hst='0' ".($province == 'NU' ? 'selected' : '')." value='NU'>NU</option>".
						"<option data-gst='0' data-pst='0' data-hst='13' ".($province == 'ON' ? 'selected' : '')." value='ON'>ON</option>".
						"<option data-gst='0' data-pst='0' data-hst='15' ".($province == 'PE' ? 'selected' : '')." value='PE'>PE</option>".
						"<option data-gst='5' data-pst='9.975' data-hst='0' ".($province == 'QC' ? 'selected' : '')." value='QC'>QC</option>".
						"<option data-gst='5' data-pst='5' data-hst='0' ".($province == 'SK' ? 'selected' : '')." value='SK'>SK</option>".
						"<option data-gst='5' data-pst='0' data-hst='0' ".($province == 'YT' ? 'selected' : '')." value='YT'>YT</option>".
						"</select></td>";
				} else if($field == 'Date') {
					echo '<td data-title="Expense Date"><input type="text" name="ex_date[]" value="' .$date. '" class="form-control datepicker"></td>';
				} else if($field == 'Receipt') {
					echo '<td data-title="Receipt">'.($receipt != '' ? '<a href="download/'.$receipt.'" target="_blank">View</a>' : '').'<input type="file" name="ex_file[]" data-filename-placement="inside" class="form-control"></td>';
				} else if($field == 'Amount') {
					$tips_and_tax = [];
					if(strpos($db_config,'Tax') !== FALSE) {
						$tips_and_tax[] = 'Tax';
					}
					echo '<td data-title="Amount'.(count($tips_and_tax) > 0 ? " Before ".implode(' & ', $tips_and_tax) : '').'"><input type="text" name="amount[]" onchange="calcTotal();" value="' . $amount . '" class="form-control"></td>';
				} else if($field == 'HST') {
					echo '<td data-title="HST"><input type="text" name="hst[]" onchange="calcTotal();" value="' . $hst . '" class="form-control"></td>';
				} else if($field == 'PST') {
					echo '<td data-title="PST"><input type="text" name="pst[]" onchange="calcTotal();" value="' . $pst . '" class="form-control"></td>';
				} else if($field == 'GST') {
					echo '<td data-title="GST"><input type="text" name="gst[]" onchange="calcTotal();" value="' . $gst . '" class="form-control"></td>';
				} else if($field == 'Total') {
					echo '<td data-title="Total"><input type="text" name="total[]" onchange="calcTotal();" value="' . $total . '" class="form-control"></td>';
				}
			}
			echo '<td data-title="Function">';
				echo '<span><a href="" onclick="addRow(this); return false;" style="width:100%;">Add Row</a> | </span>';
				if($status == '' && $approval_access == 1 && $id > 0) {
					echo '<a href="" onclick="payLine($(this).closest(\'tr\')); return false;">Mark as Paid</a> | ';
				}
				if($status != 'Paid' && $approval_access == 1 && $id > 0) {
					echo '<a href="" onclick="if(confirm(\'Are you sure?\')) { rejectLine($(this).closest(\'tr\')); return false; } return false;">Reject</a> | ';
				}
				if($status == '' || $approval_access == 1) {
					echo '<a href="" onclick="if(confirm(\'Are you sure?\')) { removeLine($(this).closest(\'tr\')); } return false;">Delete</a>';
				}
			echo '</td>';
		} else if($status != 'Rejected') {
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
			echo '<td>'.$status.'</td>';
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
}
echo "<td></td></tr>";

echo '</table>'; ?>
		<div class="form-group pull-right">
            <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to Save all Expenses entered."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <button type="submit" name="submit" value="save" class="btn brand-btn mobile-100-pull-right">Save Expenses</button>
        </div>
	</div>
</div><div class="clearfix"></div>
