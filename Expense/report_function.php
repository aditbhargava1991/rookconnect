<?php /* Expenses Report */
function report_expense($dbc, $starttime, $endtime, $category, $table_style, $table_row_style, $grand_total_style) {
    $result = mysqli_query($dbc, "SELECT * FROM expense WHERE (DATE(ex_date) >= '".$starttime."' AND DATE(ex_date) <= '".$endtime."') AND `deleted`=0 AND '$category' IN (`category`,'')");
	$config_sql = "SELECT expense, expense_dashboard, exchange_buffer, gst_name, gst_amt, pst_name, pst_amt, expense_types FROM field_config_expense WHERE `tab`='$current_tab' UNION SELECT
		'Contact,Staff,Category,Heading,Description,Date,Receipt,Type,Day Expense,Amount,Tax,Total,Budget,Signature,Flight,Hotel,Breakfast,Lunch,Dinner,Beverages,Transportation,Entertainment,Gas,Misc',
		'Contact,Category,Heading,Description,Date,Receipt,Type,Day Expense,Amount,Tax,Total,Budget', '0', 'GST', '5', 'PST', '0', 'Meals,Tip'";
	$get_expense_config = mysqli_fetch_assoc(mysqli_query($dbc,$config_sql));
	$value_config = ','.$get_expense_config['expense'].',';
	$db_config = ','.$get_expense_config['expense_dashboard'].',';
	$gst_name = trim($get_expense_config['gst_name'],',');
	$pst_name = trim($get_expense_config['pst_name'],',');
	$gst_amt = trim($get_expense_config['gst_amt'],',');
	$pst_amt = trim($get_expense_config['pst_amt'],',');
	$expense_types = trim(','.$get_expense_config['expense_types'].',',',');

    $report_data = '<div id="no-more-tables"><table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'" class="hidden-xs hidden-sm">';
    if (strpos($value_config, ','."Category".',') !== FALSE) {
        $report_data .= '<th>Expense For</th>';
    }
    if (strpos($value_config, ','."Date".',') !== FALSE) {
        $report_data .= '<th>Expense Date</th>';
    }
    if (strpos($value_config, ','."Staff".',') !== FALSE) {
        $report_data .= '<th>Staff</th>';
    }

    if (strpos($value_config, ','."Heading".',') !== FALSE) {
        $report_data .= '<th>Expense Heading</th>';
    }
    $report_data .= '<th>Type</th>';
    if (strpos($value_config, ','."Receipt".',') !== FALSE) {
        $report_data .= '<th>Receipt</th>';
    }
    if (strpos($value_config, ','."Description".',') !== FALSE) {
        $report_data .= '<th>Description</th>';
    }
    if (strpos($value_config, ','."Day Expense".',') !== FALSE) {
        $report_data .= '<th>Day Expense</th>';
    }

    if (strpos($value_config, ','."Amount".',') !== FALSE) {
        $report_data .= '<th>Amount</th>';
    }
    if (strpos($value_config, ','."Local Tax".',') !== FALSE) {
        $report_data .= '<th>'.$pst_name.'</th>';
    }
    if (strpos($value_config, ','."Tax".',') !== FALSE) {
        $report_data .= '<th>'.$gst_name.'</th>';
    }
    if (strpos($value_config, ','."Total".',') !== FALSE) {
        $report_data .= '<th>Total</th>';
    }
    if (strpos($value_config, ','."Budget".',') !== FALSE) {
        $report_data .= '<th>Budget</th>';
    }
	$report_data .= '<th>Status</th>';

    $num_rows = mysqli_num_rows($result);
    $report_data .= "</tr>";
            $amount = 0;
            $gst = 0;
            $total = 0;
            $balance = 0;
    while($row = mysqli_fetch_array( $result )) {
        $report_data .= '<tr nobr="true">';

        if (strpos($value_config, ','."Category".',') !== FALSE) {
            $report_data .= '<td data-title="Expense For">' . ucwords($row['expense_for']).'<br>'.$row['contact'] . '</td>';
        }
        if (strpos($value_config, ','."Date".',') !== FALSE) {
            $report_data .= '<td data-title="Expense Date">' . $row['ex_date'] . '</td>';
        }
        if (strpos($value_config, ','."Staff".',') !== FALSE) {
            $report_data .= '<td data-title="Staff">' . get_contact($dbc,$row['staff']) . '</td>';
        }
        if (strpos($value_config, ','."Heading".',') !== FALSE) {
            $report_data .= '<td data-title="Expense Heading">' . $row['title'] . '</td>';
        }
        $report_data .= '<td data-title="Type">' . $row['type'] . '</td>';
        if (strpos($value_config, ','."Receipt".',') !== FALSE) {
            $report_data .= '<td data-title="Receipt"><a href="../Expense/download/'.trim($row['ex_file'],'download/').'" target="_blank">' . $row['ex_file'] . '</a></td>';
        }
        if (strpos($value_config, ','."Description".',') !== FALSE) {
            $report_data .= '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
        }
        if (strpos($value_config, ','."Day Expense".',') !== FALSE) {
            $report_data .= '<td data-title="Day Expense">' . $row['day_expense'] . '</td>';
        }
        if (strpos($value_config, ','."Amount".',') !== FALSE) {
            $report_data .= '<td data-title="Amount">' . $row['amount'] . '</td>';
        }
        if (strpos($value_config, ','."Local Tax".',') !== FALSE) {
            $report_data .= '<td data-title="'.$pst_name.'">' . $row['gst'] . '</td>';
        }
        if (strpos($value_config, ','."Tax".',') !== FALSE) {
            $report_data .= '<td data-title="'.$gst_name.'">' . $row['gst'] . '</td>';
        }
        if (strpos($value_config, ','."Total".',') !== FALSE) {
            $report_data .= '<td data-title="Amount">' . $row['total'] . '</td>';
        }
        if (strpos($value_config, ','."Budget".',') !== FALSE) {
            $report_data .= '<td data-title="Budget">' . $row['balance'] . '</td>';
        }
		$report_data .= '<td data-title="Status">'.$row['status'].'</td>';
        $report_data .= "</tr>";
        $amount += $row['amount'];
        $gst += $row['gst'];
        $total += $row['total'];
        $balance += $row['balance'];
    }

    if($num_rows > 0) {
		$colspan = 0;
    $report_data .= '<tr nobr="true">';
    if (strpos($value_config, ','."Category".',') !== FALSE) {
        $colspan++;
    }
    if (strpos($value_config, ','."Date".',') !== FALSE) {
        $colspan++;
    }
    if (strpos($value_config, ','."Staff".',') !== FALSE) {
        $colspan++;
    }
    if (strpos($value_config, ','."Heading".',') !== FALSE) {
        $colspan++;
    }
    $colspan++;
    if (strpos($value_config, ','."Receipt".',') !== FALSE) {
        $colspan++;
    }
    if (strpos($value_config, ','."Description".',') !== FALSE) {
        $colspan++;
    }
    if (strpos($value_config, ','."Day Expense".',') !== FALSE) {
        $colspan++;
    }
	$report_data .= '<td colspan="'.$colspan.'"><b>Total</b></td>';
    if (strpos($value_config, ','."Amount".',') !== FALSE) {
    $report_data .= '<td data-title="Amount"><b>$' . number_format((float)$amount, 2, '.', '') . '</b></td>';
    }
    if (strpos($value_config, ','."Local Tax".',') !== FALSE) {
    $report_data .= '<td data-title="'.$pst_name.'"><b>$' . number_format((float)$gst, 2, '.', '') . '</b></td>';
    }
    if (strpos($value_config, ','."Tax".',') !== FALSE) {
    $report_data .= '<td data-title="'.$gst_name.'"><b>$' . number_format((float)$gst, 2, '.', '') . '</b></td>';
    }
    if (strpos($value_config, ','."Amount".',') !== FALSE) {
    $report_data .= '<td data-title="Total"><b>$' . number_format((float)$total, 2, '.', '') . '</b></td>';
    }
    if (strpos($value_config, ','."Budget".',') !== FALSE) {
    $report_data .= '<td data-title="Budget"><b>$' . number_format((float)$balance, 2, '.', '') . '</b></td>';
    }
	$report_data .= '<td></td>';
    $report_data .= "</tr>";
    }

    $report_data .= '</table></div>';

    return $report_data;
}