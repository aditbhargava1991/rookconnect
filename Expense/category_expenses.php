<head>
  <link href="../jquery-filestyle/src/jquery-filestyle.min.js">
</head>
<body>
  <script src="../jquery-filestyle/src/jquery.js"></script>
  <script src="../jquery-filestyle/src/jquery-filestyle.min.js"></script>
</body>
<?php /* Expense Output */
if(isset($_POST['submit'])) {
	foreach($_POST['expenseid'] as $key => $id) {
		$expense_values = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `expense` WHERE `expenseid`='$id'"));
		$staff = isset($_POST['search_staff']) ? $_POST['search_staff'] : $expense_values['staff'];
		$contact = isset($_POST['contact']) ?$_POST['contact'][$key] : $expense_values['contact'];
		$expense_tab = (!empty($_POST['expense_for'][$key]) ? $_POST['expense_for'][$key] : $_GET['tab']);
		$category = isset($_POST['category']) ?$_POST['category'][$key] : $expense_values['category'];
		$heading = isset($_POST['heading']) ?$_POST['heading'][$key] : $expense_values['heading'];
		$description = isset($_POST['description']) ?$_POST['description'][$key] : $expense_values['description'];
		$country = isset($_POST['country']) ?$_POST['country'][$key] : $expense_values['country'];
		$province = isset($_POST['province']) ?$_POST['province'][$key] : $expense_values['province'];
		$currency = isset($_POST['currency']) ?$_POST['currency'][$key] : $expense_values['currency'];
		if($currency == '') {
			$currency = 'CAD/CAD';
		}
		$exchange_rate = isset($_POST['exchange_rate']) ?$_POST['exchange_rate'][$key] : $expense_values['exchange_rate'];
		if($exchange_rate == 0) {
			$exchange_rate = 1;
		}
		$date = isset($_POST['ex_date']) ?$_POST['ex_date'][$key] : $expense_values['ex_date'];
		$work_order = isset($_POST['work_order']) ?$_POST['work_order'][$key] : $expense_values['work_order'];
		$type = isset($_POST['type']) ?$_POST['type'][$key] : $expense_values['type'];
		$day_expense = isset($_POST['day_expense']) ?$_POST['day_expense'][$key] : $expense_values['day_expense'];
		$amount = isset($_POST['amount']) ?$_POST['amount'][$key] : $expense_values['amount'];
		$cat_amt = isset($_POST['cat_amt']) ?$_POST['cat_amt'][$key] : $expense_values['cat_amt'];
		$tips = isset($_POST['tips']) ?$_POST['tips'][$key] : $expense_values['tips'];
		$third_tax = isset($_POST['hst']) ?$_POST['hst'][$key] : $expense_values['hst'];
		$local_tax = isset($_POST['pst']) ?$_POST['pst'][$key] : $expense_values['pst'];
		$tax = isset($_POST['gst']) ?$_POST['gst'][$key] : $expense_values['gst'];
		$total = isset($_POST['total']) ?$_POST['total'][$key] : $expense_values['total'];
		$budget = isset($_POST['balance']) ?$_POST['balance'][$key] : $expense_values['balance'];
		$reimburse = isset($_POST['reimburse']) ? $_POST['reimburse'][$key] : ($id == '' ? 1 : $expense_values['reimburse']);
		$comments = $_POST['comments'][$key];
		if($comments != '') {
			$comments .= " (Comment added by ".get_contact($dbc,$_SESSION['contactid'])." on ".date('Y-m-d h:i:s').")";
		}
		if($contact != '' || $description != '' || $date != '' || $work_order != '' || $type != '' || $day_expense != '' || ($amount != '' && $cat_amt == 0) || $tips != '' || $third_tax != '' || $local_tax != '' || $tax != '' || $total != '' || $budget != '') {
			if($id == '') {
				$sql_expense = "INSERT INTO `expense` (`expense_for`, `category`, `contact`, `staff`, `title`, `description`, `country`, `province`, `currency`, `exchange_rate`, `ex_date`, `work_order`, `type`, `day_expense`, `amount`, `tips`, `balance`, `pst`, `gst`, `hst`, `total`, `reimburse`, `comments`)
					VALUES ('$expense_tab', '$category', '$contact', '$staff', '$heading', '$description', '$country', '$province', '$currency', '$exchange_rate', '$date', '$work_order', '$type', '$day_expense', '$amount', '$tips', '$budget', '$local_tax', '$tax', '$third_tax', '$total', '$reimburse', '$comments')";
          $before_change = "";
          $history = "Expense entry has been added. <br />";
      	  add_update_history($dbc, 'expenses_history', $history, '', $before_change);
			} else {
				$sql_expense = "UPDATE `expense` SET `category`='$category', `contact`='$contact', `title`='$heading', `description`='$description', `country`='$country', `province`='$province', `currency`='$currency', `exchange_rate`='$exchange_rate', `ex_date`='$date', `work_order`='$work_order', `type`='$type', `day_expense`='$day_expense', `amount`='$amount', `tips`='$tips', `balance`='$budget', `pst`='$local_tax', `gst`='$tax', `hst`='$third_tax', `total`='$total', `reimburse`='$reimburse' WHERE `expenseid`='$id'";
        $before_change = "";
        $history = "Expense entry has been updated. <br />";
    	  add_update_history($dbc, 'expenses_history', $history, '', $before_change);
			}
			mysqli_query($dbc, $sql_expense);

			if($id == '') {
				$id = mysqli_insert_id($dbc);
			}
			$_POST['expenseid'][$key] = $id;
			if(!empty($_FILES['ex_file']['name'][$key])) {
				$temp_var = htmlspecialchars(preg_replace('/[^A-Za-z0-9\.]/','_',$_FILES['ex_file']['name'][$key]), ENT_QUOTES);
				$receipt = 'receipt_'.$id.'_'.$temp_var;
				$tmp_receipt = $_FILES['ex_file']['tmp_name'][$key];
				move_uploaded_file($tmp_receipt, "download/" . $receipt) ;
				$sql_receipt = mysqli_query($dbc, "UPDATE `expense` SET `ex_file`='$receipt' WHERE `expenseid`='$id'");
			}
		}
	}
	require_once('../phpsign/signature-to-image.php');
    $sign = $_POST['output'];
    $img = sigJsonToImage($sign);
	$sign_file = 'sign_'.date('Y-m-d-h-i').'_'.$_SESSION['contactid'].'.png';
	if($_POST['submit'] != 'export') {
		echo "<script> window.location.replace(''); </script>";
	}
	if($_POST['submit'] == 'approval') {
		imagepng($img, 'download/'.$sign_file);
		foreach($_POST['expenseid'] as $key => $value) {
			if($value != '') {
        $before_change = capture_before_change($dbc, 'expense', 'status', 'expenseid', $value);
        $before_change .= capture_before_change($dbc, 'expense', 'submit_by', 'expenseid', $value);
        $before_change .= capture_before_change($dbc, 'expense', 'submit_date', 'expenseid', $value);
        $before_change .= capture_before_change($dbc, 'expense', 'submit_sign', 'expenseid', $value);
				$submit_sql = "UPDATE `expense` SET `status`='Submitted', `submit_by`='".$_SESSION['contactid']."', `submit_date`='".date('Y-m-d')."', `submit_sign`='$sign_file' WHERE `expenseid`='$value'";
				mysqli_query($dbc,$submit_sql);
        $history = capture_after_change('status', 'Submitted');
        $history .= capture_after_change('submit_by', $_SESSION['contactid']);
        $history .= capture_after_change('submit_date', date('Y-m-d'));
        $history .= capture_after_change('submit_sign', $sign_file);
    	  add_update_history($dbc, 'expenses_history', $history, '', $before_change);
			}
		}
	} else if($_POST['submit'] == 'payable') {
		imagepng($img, 'download/'.$sign_file);
		foreach($_POST['expenseid'] as $key => $value) {
			if($value != '') {

        $before_change = capture_before_change($dbc, 'expense', 'status', 'expenseid', $value);
        $before_change .= capture_before_change($dbc, 'expense', 'approval_by', 'expenseid', $value);
        $before_change .= capture_before_change($dbc, 'expense', 'approval_date', 'expenseid', $value);
        $before_change .= capture_before_change($dbc, 'expense', 'approval_sign', 'expenseid', $value);
				$submit_sql = "UPDATE `expense` SET `status`='Approved', `approval_by`='".$_SESSION['contactid']."', `approval_date`='".date('Y-m-d')."', `approval_sign`='$sign_file' WHERE `expenseid`='$value'";
				mysqli_query($dbc,$submit_sql);
        $history = capture_after_change('status', 'Approved');
        $history .= capture_after_change('approval_by', $_SESSION['contactid']);
        $history .= capture_after_change('approval_date', date('Y-m-d'));
        $history .= capture_after_change('approval_sign', $sign_file);
    	  add_update_history($dbc, 'expenses_history', $history, '', $before_change);
			}
		}
	} else if($_POST['submit'] == 'paid') {
		imagepng($img, 'download/'.$sign_file);
		foreach($_POST['expenseid'] as $key => $value) {
			if($value != '') {
        $before_change = capture_before_change($dbc, 'expense', 'status', 'expenseid', $value);
        $before_change .= capture_before_change($dbc, 'expense', 'paid_by', 'expenseid', $value);
        $before_change .= capture_before_change($dbc, 'expense', 'paid_date', 'expenseid', $value);
        $before_change .= capture_before_change($dbc, 'expense', 'paid_sign', 'expenseid', $value);
				$submit_sql = "UPDATE `expense` SET `status`='Paid', `paid_by`='".$_SESSION['contactid']."', `paid_date`='".date('Y-m-d')."', `paid_sign`='$sign_file' WHERE `expenseid`='$value'";
				mysqli_query($dbc,$submit_sql);
        $history = capture_after_change('status', 'Paid');
        $history .= capture_after_change('paid_by', $_SESSION['contactid']);
        $history .= capture_after_change('paid_date', date('Y-m-d'));
        $history .= capture_after_change('paid_sign', $sign_file);
    	  add_update_history($dbc, 'expenses_history', $history, '', $before_change);
			}
		}
	} else if($_POST['submit'] == 'export') {
		require_once('../tcpdf/tcpdf.php');
		ob_clean();

		$get_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT `pdf_logo`, `pdf_header` FROM `field_config_expense`"));
		$head_logo = get_config($dbc, 'expense_logo');
		$pdf_header = get_config($dbc, 'expense_header');
		$pdf_footer = get_config($dbc, 'expense_footer');
		$staff_name = get_contact($dbc, $staff);
		$display_month = date('F Y', strtotime($search_month));
		DEFINE('HEADER_LOGO', $head_logo);
		DEFINE('HEADER_TEXT', html_entity_decode($pdf_header));
		DEFINE('FOOTER_TEXT', $pdf_footer == '' ? "<em>Expense Report for ".$display_month." for ".$staff_name." as of ".date('Y-m-d')."</em>" : html_entity_decode($pdf_footer));

		class MYPDF extends TCPDF {

			//Page header
			public function Header() {
				if($front_client_info != '') {
					if ($this->PageNo() > 1) {
						if(HEADER_LOGO != '') {
							$image_file = 'download/'.HEADER_LOGO;
							$this->Image($image_file, 10, 5, 25, '', '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);
						}

						if(HEADER_TEXT != '') {
							$this->setCellHeightRatio(0.7);
							$this->SetFont('helvetica', '', 10);
							$header_text = '<p style="text-align:right;">'.HEADER_TEXT.'</p>';
							$this->writeHTMLCell(0, 0, 0 , 5, $header_text, 0, 0, false, (HEADER_LOGO == '' ? 'C' : 'R'), true);
						}
					}
				} else {
					if(HEADER_LOGO != '') {
						$image_file = 'download/'.HEADER_LOGO;
						$this->Image($image_file, 10, 5, 25, '', '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);
					}

					if(HEADER_TEXT != '') {
						$this->setCellHeightRatio(0.7);
						$this->SetFont('helvetica', '', 10);
						$header_text = '<p style="text-align:right;">'.HEADER_TEXT.'</p>';
						$this->writeHTMLCell(0, 0, 0 , 5, $header_text, 0, 0, false, (HEADER_LOGO == '' ? 'C' : 'R'), true);
					}
				}
			}

			// Page footer
			public function Footer() {
				// Position at 15 mm from bottom
				$this->SetY(-15);
				$this->SetFont('helvetica', 'I', 8);
				$footer_text = '<p style="text-align:right;">'.$this->getAliasNumPage().'</p>';
				$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);

				$this->SetY(-15);
				$this->setCellHeightRatio(0.7);
				$this->SetFont('helvetica', '', 8);
				$footer_text = FOOTER_TEXT;
				$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "C", true);
			}
		}

		$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$margin_height = ($head_logo == '' && $pdf_header == '' ? 15 : 30);
		$pdf->SetMargins(PDF_MARGIN_LEFT, $margin_height, PDF_MARGIN_RIGHT);
		$pdf->AddPage();

		$pdf->SetFont('helvetica', '', 14);
		$pdf->Write(0, 'Expense Report for '.$display_month.' for '.$staff_name, '', 0, 'C', true, 0, false, false, 0);
		$pdf->Ln();

		$html = '<table border="1" cellpadding=2 cellspacing=0 style="width:100%">';
		$html .= "<tr>";
		$colspan = $totalspan = $tempcol = 0;
		$html .= '<th align="center">Heading</th>';
		$tempcol++;
		$colspan++;
		$config_sql = "SELECT expense, expense_dashboard, exchange_buffer, gst_name, gst_amt, pst_name, pst_amt, hst_name, hst_amt, expense_types, expense_rows FROM field_config_expense WHERE `tab`='$current_tab' UNION SELECT
			'Flight,Hotel,Breakfast,Lunch,Dinner,Beverages,Transportation,Entertainment,Gas,Misc',
			'Description,Date,Receipt,Type,Day Expense,Amount,Tax,Total', '0', 'GST', '5', 'PST', '0', 'HST', '0', 'Meals,Tip', 1";
		$get_expense_config = mysqli_fetch_assoc(mysqli_query($dbc,$config_sql));
		$value_config = ','.$get_expense_config['expense'].',';
		$db_config = ','.$get_expense_config['expense_dashboard'].',';
		$db_config_arr = explode(',',trim($db_config,','));
		$tips_and_tax = [];
		if(strpos($db_config,'Tips') !== FALSE) {
			$tips_and_tax[] = 'Tips';
		}
		if(strpos($db_config,'Tax') !== FALSE) {
			$tips_and_tax[] = 'Tax';
		}
		$detail = '';
		if(strpos($db_config,'Exchange') !== FALSE) {
			$tips_and_tax[] = 'Currency Conversion';
			$detail = ' After Currency Conversion';
		}
		foreach($db_config_arr as $field) {
			if($field == 'Contact') {
				$html .= '<th align="center">Expense Contact</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Expense For') {
				$html .= '<th align="center">Expense Tab</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Description') {
				$html .= '<th align="center">Description</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Country') {
				$html .= '<th align="center">Country of Expense</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Province') {
				$html .= '<th align="center">Province of Expense</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Exchange') {
				$html .= '<th align="center">Exchange Currency</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Date') {
				$html .= '<th align="center">Expense Date</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Work Order') {
				$html .= '<th align="center">Work Order</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Receipt') {
				$html .= '<th align="center">Receipt</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Type') {
				$html .= '<th align="center">Expense Type</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Day Expense') {
				$html .= '<th align="center">Day Expense</th>';
				$tempcol++;
				$colspan++;
			} else if($field == 'Amount') {
				$html .= '<th align="center">Amount'.(count($tips_and_tax) > 0 ? " (excluding ".implode(' & ', $tips_and_tax).")" : '').'</th>';
				$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
				$colspan++;
			} else if($field == 'Tips') {
				$html .= '<th align="center">Tips</th>';
				$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
				$colspan++;
			} else if($field == 'Third Tax') {
				$html .= '<th align="center">'.($hst_name == '' ? 'Third Tax' : $hst_name).'</th>';
				$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
				$colspan++;
			} else if($field == 'Local Tax') {
				$html .= '<th align="center">'.($pst_name == '' ? 'Additional Tax' : $pst_name).'</th>';
				$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
				$colspan++;
			} else if($field == 'Tax') {
				$html .= '<th align="center">'.($gst_name == '' ? 'Tax' : $gst_name).'</th>';
				$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
				$colspan++;
			} else if($field == 'Total') {
				$html .= '<th align="center">Total'.$detail.'</th>';
				$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
				$colspan++;
			} else if($field == 'Budget') {
				$html .= '<th align="center">Budget</th>';
				$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
				$colspan++;
			} else if($field == 'Reimburse') {
				$html .= '<th align="center">Reimburse</th>';
				$tempcol++;
				$colspan++;
			}
		}
		$colspan++;
		$html .= '<th align="center">Expense Status</th>';
		$html .= "</tr>";

		$total_amount = 0;
		$total_tips = 0;
		$total_hst = 0;
		$total_pst = 0;
		$total_gst = 0;
		$total_total = 0;
		$total_balance = 0;

		$categories_sql = "SELECT * FROM (SELECT CONCAT('EC ',`ec`,': ',`category`) `ec_code`, `category`, CONCAT('GL ',`gl`,': ',`heading`) `gl_code`, `heading` FROM `expense_categories` WHERE ('current_month'='$current_tab' || 'manager'='$current_tab' || 'payables'='$current_tab' || `expense_tab`='$current_tab') ORDER BY `ec`, `gl`) `categories`
			UNION SELECT 'Uncategorized', '', 'Misc', '' FROM (SELECT COUNT(*) numrows FROM `expense_categories` WHERE `expense_tab`='$current_tab') cat_count WHERE numrows = 0";
		$category_query = mysqli_query($dbc, $categories_sql);
		$category_group = '';
		while($cat_row = mysqli_fetch_array($category_query)) {
			// $query_expenses and $min_rows should be set by the tab page
			// $min_open is a setting
			$category_value = $cat_row['category'];
			$heading_value = $cat_row['heading'];
			$result = mysqli_query($dbc, $query_expenses." AND `category`='$category_value' AND `title`='$heading_value'");
			$row_count = mysqli_num_rows($result);
			$html .= "<tbody>";
			if($category_group != $cat_row['ec_code']) {
				$category_group = $cat_row['ec_code'];
				$html .= '<tr><td colspan="'.$colspan.'" align="center"><h3>'.$category_group.' Expenses</h3></td></tr>';
			}

			for($i = 0; $i < $row_count; $i++) {
				$id = $contact = $expense_tab = $category = $heading = $description = $country = $province = $currency = $date = $work_order = $receipt = $type = $day_expense = $amount = $tips = $third_tax = $local_tax = $tax = $total = $budget = $status = $comments = '';
				$reimburse = $exchange = 1;
				if($row = mysqli_fetch_array($result)) {
					$id = $row['expenseid'];
					$contact = $row['contact'];
					$expense_tab = $row['expense_for'];
					$category = $row['category'];
					$heading = $row['title'];
					$description = strip_tags(html_entity_decode($row['description']));
					$country = $row['country'];
					$province = $row['province'];
					$exchange = $row['exchange_rate'];
					$currency = $row['currency'];
					$date = $row['ex_date'];
					$work_order = $row['work_order'];
					$receipt = $row['ex_file'];
					$type = $row['type'];
					$day_expense = $row['day_expense'];
					$amount = $row['amount'];
					$tips = $row['tips'];
					$third_tax = $row['hst'];
					$local_tax = $row['pst'];
					$tax = $row['gst'];
					$total = $row['total'];
					$budget = $row['balance'];
					$status = $row['status'];
					$reimburse = $row['reimburse'];
					$comments = $row['comments'];
				}

				$html .= "<tr>";
				$html .= '<td align="center">'.$cat_row['gl_code'].'</td>';
				foreach($db_config_arr as $field) {
					$html .= '<td align="center">';
					if($field == 'Contact') {
						$html .= $contact;
					} else if($field == 'Expense For') {
						$html .= ucwords($expense_tab);
					} else if($field == 'Description') {
						$html .= $description;
					} else if($field == 'Country') {
						$html .= $country;
					} else if($field == 'Province') {
						$html .= $province;
					} else if($field == 'Exchange') {
						$html .= $currency.' @ '.$exchange;
					} else if($field == 'Date') {
						$html .= $date;
					} else if($field == 'Work Order') {
						$html .= $work_order;
					} else if($field == 'Receipt') {
						$html .= ($receipt != '' ? '<a href="'.WEBSITE_URL.'/Expense/download/'.$receipt.'" target="_blank">View</a>' : 'No Receipt');
					} else if($field == 'Type') {
						$html .= $type.'</td>';
					} else if($field == 'Day Expense') {
						$html .= '$' . $day_expense;
					} else if($field == 'Amount') {
						$html .= '$' . $amount;
					} else if($field == 'Tips') {
						$html .= '$' . $tips;
					} else if($field == 'Third Tax') {
						$html .= '$' . $third_tax;
					} else if($field == 'Local Tax') {
						$html .= '$' . $local_tax;
					} else if($field == 'Tax') {
						$html .= '$' . $tax;
					} else if($field == 'Total') {
						$html .= '$' . $total;
					} else if($field == 'Budget') {
						$html .= '$' . $budget;
					} else if($field == 'Reimburse') {
						$html .= ($reimburse ? 'To be reimbursed' : 'Not for reimbursement');
					}
					$html .= '</td>';
				}
				$html .= '<td>'.($status == '' ? 'Saved' : $status).'</td>';
				$html .= "</tr>";
				$total_amount += (float)$amount;
				$total_tips += (float)$tips;
				$total_hst += (float)$third_tax;
				$total_pst += (float)$local_tax;
				$total_gst += (float)$tax;
				$total_total += (float)$total;
				$total_balance += (float)$budget;
			}
			$html .= "</tbody>";
		}

		$html .= '<tr><td colspan="'.$totalspan.'"><b>Total</b></td>';
		echo '<pre>';
		print_r($db_config_arr);
		exit;
		foreach($db_config_arr as $i => $field) {
			if($field == 'Amount') {
				$html .= '<td><b>$' . number_format($total_amount, 2, '.', '') . '</b></td>';
			} else if($field == 'Tips') {
				$html .= '<td><b>$' . number_format($total_tips, 2, '.', '') . '</b></td>';
			} else if($field == 'Third Tax') {
				$html .= '<td><b>$' . number_format($total_hst, 2, '.', '') . '</b></td>';
			} else if($field == 'Local Tax') {
				$html .= '<td><b>$' . number_format($total_pst, 2, '.', '') . '</b></td>';
			} else if($field == 'Tax') {
				$html .= '<td><b>$' . number_format($total_gst, 2, '.', '') . '</b></td>';
			} else if($field == 'Total') {
				$html .= '<td><b>$' . number_format($total_total, 2, '.', '') . '</b></td>';
			} else if($field == 'Budget') {
				$html .= '<td><b>$' . number_format($total_balance, 2, '.', '') . '</b></td>';
			} else if($i >= $totalspan) {
				$html .= "<td></td>";
			}
		}
		$html .= "<td></td></tr></table>";

		$category_query = mysqli_query($dbc, $categories_sql);
		$html .= "<h3>Summary by Categories</h3>";
		$html .= '<table border="1" cellpadding=2 cellspacing=0 style="width:67%"><tr><th>Category & Heading</th><th>Expense Amount</th><th>Tax</th><th>Total</th></tr>';
		$final_amt = $final_tax = $final_total = 0;
		while($cat_row = mysqli_fetch_array($category_query)) {
			// $query_expenses and $min_rows should be set by the tab page
			// $min_open is a setting
			$category_value = $cat_row['category'];
			$heading_value = $cat_row['heading'];
			$html .= "<tr><td>".$cat_row['ec_code'].': '.$cat_row['gl_code']."</td>";
			$result = mysqli_query($dbc, $query_expenses." AND `category`='$category_value' AND `title`='$heading_value'");
			$cat_amt = $cat_tax = $cat_total = 0;
			while($row = mysqli_fetch_array($result)) {
				$cat_amt += $row['amount'];
				$cat_tax += $row['pst'] + $row['gst'];
				$cat_total += $row['total'];
			}
			$final_amt += $cat_amt;
			$final_tax += $cat_tax;
			$final_total += $cat_total;
			$html .= "<td>$".number_format($cat_amt, 2, '.', '')."</td><td>$".number_format($cat_tax, 2, '.', '')."</td><td>$".number_format($cat_total, 2, '.', '')."</td></tr>";
		}
		$html .= "<tr><td><b>Totals</b></td><td><b>$".number_format($final_amt, 2, '.', '')."</b></td><td><b>$".number_format($final_tax, 2, '.', '')."</b></td><td><b>$".number_format($final_total, 2, '.', '')."</b></td></tr>";
		$html .= "</table>";

		$pdf->SetFont('helvetica', '', 8);
		$pdf->setCellHeightRatio(1.75);
		$pdf->writeHTML($html, true, false, true, false, '');

		if (!file_exists('download/reports')) {
			mkdir('download/reports', 0777, true);
		}
		$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
		$pdf_name = 'download/reports/expenses_'.preg_replace('/[^a-z]/','_',strtolower($staff_name)).'_'.date('Y_m_d_h_i').'.pdf';
		$pdf->Output($pdf_name, 'F');

		echo '<script type="text/javascript"> window.location.replace("'.$pdf_name.'"); </script>';
	}
}

// Variables
$config_sql = "SELECT expense, expense_dashboard, exchange_buffer, gst_name, gst_amt, pst_name, pst_amt, hst_name, hst_amt, expense_types, expense_rows FROM field_config_expense WHERE `tab`='$current_tab' UNION SELECT
	'Flight,Hotel,Breakfast,Lunch,Dinner,Beverages,Transportation,Entertainment,Gas,Misc',
	'Description,Date,Receipt,Type,Day Expense,Amount,Tax,Total', '0', 'GST', '5', 'PST', '0', 'HST', '0', 'Meals,Tip', 1";
$get_expense_config = mysqli_fetch_assoc(mysqli_query($dbc,$config_sql));
$value_config = ','.$get_expense_config['expense'].',';
$db_config = ','.$get_expense_config['expense_dashboard'].',';
$gst_name = trim($get_expense_config['gst_name'],',');
$pst_name = trim($get_expense_config['pst_name'],',');
$hst_name = trim($get_expense_config['hst_name'],',');
$gst_amt = trim($get_expense_config['gst_amt'],',');
$pst_amt = trim($get_expense_config['pst_amt'],',');
$hst_amt = trim($get_expense_config['hst_amt'],',');
$expense_types = trim(','.$get_expense_config['expense_types'].',',',');
$min_open = (($current_tab == 'manager' || $current_tab == 'payables') ? 0 : $get_expense_config['expense_rows']);
$default_country = get_config($dbc, 'default_country');
$default_province = get_config($dbc, 'default_province');
$province_list = explode('#*#',get_config($dbc, 'expense_provinces'));

$category_list = [];
$category_results = mysqli_query($dbc, "SELECT `expense_tab`, `category` FROM `expense_categories` WHERE `expense_tab`='$current_tab' OR 'current_month' = '$current_tab' GROUP BY `category`, `expense_tab`");
while($row = mysqli_fetch_array($category_results)) {
	$category_list[] = [$row['expense_tab'],$row['category']];
}

$heading_list = [];
$heading_results = mysqli_query($dbc, "SELECT `expense_tab`, `category`, `heading` FROM `expense_categories` WHERE `expense_tab`='$current_tab' OR 'current_month' = '$current_tab' GROUP BY `heading`, `category`, `expense_tab`");
while($row = mysqli_fetch_array($heading_results)) {
	$heading_list[] = [$row['expense_tab'],$row['category'],$row['heading']];
}

$contact_list = [];
$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE `category` NOT LIKE 'Cold Call%' AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
foreach($query as $contactid) {
	$name = trim(get_client($dbc, $contactid));
	if($name == '') {
		$name = trim(get_contact($dbc, $contactid));
	}
	$contact_list[] = [$contactid,$name];
}

$currency_data = file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'); ?>

<script>
var exchange_data = <?= $currency_data ?>;
var exchange_buffer = <?= $get_expense_config['exchange_buffer'] ?>;
$(document).ready(function() {
	$('[name="contact[]"]').each(function() { loadContacts(this); });
	$('tbody').each(function() { $(this).find('tr').not(':last').find('a:contains("Add Row")').hide(); });
	$('.datepicker').datepicker('option', { defaultDate: new Date(<?= substr($search_month, 0, 4) ?>, <?= substr($search_month, 5, 2)-1; ?>, 1) });
	$('form').submit(function() {
		var descriptions = true;
		$('tr').each(function() {
			var descript = $(this).find('[name="description[]"]');
			var amount = $(this).find('[name="amount[]"]');
			var cat_amt = $(this).find('[name="cat_amt[]"]');
			if(amount.val() > 0 && cat_amt == 0 && descript.val() == '') {
				descriptions = false;
				descript.focus();
			}
		});
		if(!descriptions) {
			alert('Please fill in the description of each expense.');
			return false;
		}
	});
});
$(document).on('change', 'select[name="expense_for[]"]', function() { loadCategories(this); });
$(document).on('change', 'select[name="province[]"]', function() { calcTotal(); });
$(document).on('change', 'select[name="currency[]"]', function() { calcTotal(); });
var contacts = <?= json_encode($contact_list) ?>;

function loadContacts(dropdown) {
	var contact = $(dropdown);
	contact.empty();
	contact.append('<option></option>');
	$(contacts).each(function() {
		contact.append('<option value="'+this[0]+'">'+this[1]+'</option>');
	});
	contact.val(contact.data('value'));
	contact.trigger('change.select2');
}

function calcTotal() {
	var total_amt = 0;
	var total_tips = 0;
	var total_hst = 0;
	var total_gst = 0;
	var total_pst = 0;
	var total_budget = 0;
	var total_total = 0;

	$('[name="currency[]"]').each(function () {
		var id = $(this).find('option:selected').data('currency');
		var date = $(this).closest('tr').find('[name="ex_date[]"]').val();
		var rate = 1;
		if(id != undefined && id != '') {
			if(date == '') {
				date = '<?= date('Y-m-d') ?>';
			}
			var rates = $(exchange_data.observations).filter(function() { return this.d == date; });
			if(rates.length > 0) {
				rate = rates[0][id].v + exchange_buffer;
			} else {
				rate = $(this).closest('tr').find('[name="exchange_rate[]"]').val();
			}
		}
		$(this).closest('tr').find('[name="exchange_rate[]"]').val(rate);
	});
	$('[name="amount[]"]').each(function() {
		var amt = +$(this).val() || 0;
		var exchange = +$(this).closest('tr').find('[name="exchange_rate[]"]').val() || 1;
		if(amt > 0) {
			$(this).val(amt.toFixed(2));
		}
		total_amt += amt * exchange;
	});
	$('[name="tips[]"]').each(function() {
		var tips = +$(this).val() || 0;
		if(tips > 0) {
			$(this).val(tips.toFixed(2));
		}
		total_tips += tips;
	});
	$('[name="gst[]"]').each(function() {
		var rate = <?= ($gst_amt > 0 ? $gst_amt : 0) ?>;
		var amt = $(this).closest('tr').find('[name="amount[]"]').val();
		var province = $(this).closest('tr').find('[name="province[]"] option:selected');
		if(province.val() != '') {
			rate = province.data('gst');
		}
		if(amt != '' && amt != undefined) {
			$(this).val((Math.round(amt * rate) / 100).toFixed(2));
		}
		total_gst += +$(this).val();
	});
	$('[name="pst[]"]').each(function() {
		var rate = <?= ($pst_amt > 0 ? $pst_amt : 0) ?>;
		var amt = $(this).closest('tr').find('[name="amount[]"]').val();
		var province = $(this).closest('tr').find('[name="province[]"] option:selected');
		if(province.val() != '') {
			rate = province.data('pst');
		}
		if(amt != '' && amt != undefined) {
			$(this).val((Math.round(amt * rate) / 100).toFixed(2));
		}
		total_pst += +$(this).val();
	});
	$('[name="hst[]"]').each(function() {
		var rate = <?= ($hst_amt > 0 ? $hst_amt : 0) ?>;
		var amt = $(this).closest('tr').find('[name="amount[]"]').val();
		var province = $(this).closest('tr').find('[name="province[]"] option:selected');
		if(province.val() != '') {
			rate = province.data('hst');
		}
		if(amt != '' && amt != undefined) {
			$(this).val((Math.round(amt * rate) / 100).toFixed(2));
		}
		total_hst += +$(this).val();
	});
	$('[name="budget[]"]').each(function() {
		total_budget += +$(this).val() || 0;
	});
	$('[name="total[]"]').each(function() {
		var gst = +$(this).closest('tr').find('[name="gst[]"]').val() || 0;
		var pst = +$(this).closest('tr').find('[name="pst[]"]').val() || 0;
		var hst = +$(this).closest('tr').find('[name="hst[]"]').val() || 0;
		var tips = +$(this).closest('tr').find('[name="tips[]"]').val() || 0;
		var amt = $(this).closest('tr').find('[name="amount[]"]').val();
		var exchange = +$(this).closest('tr').find('[name="exchange_rate[]"]').val() || 1;
		var total_amt = 0;
		if(amt != '' && amt != undefined) {
			total_amt = (+amt) + tips + hst + pst + gst;
			$(this).val((total_amt * exchange).toFixed(2));
		}
		total_total += +$(this).val();
	});
	$('td[data-name="total_amt"]').html("<b>$"+total_amt.toFixed(2)+"</b>");
	$('td[data-name="total_tips"]').html("<b>$"+total_tips.toFixed(2)+"</b>");
	$('td[data-name="total_gst"]').html("<b>$"+total_gst.toFixed(2)+"</b>");
	$('td[data-name="total_pst"]').html("<b>$"+total_pst.toFixed(2)+"</b>");
	$('td[data-name="total_hst"]').html("<b>$"+total_hst.toFixed(2)+"</b>");
	$('td[data-name="total_total"]').html("<b>$"+total_total.toFixed(2)+"</b>");
	$('td[data-name="total_budget"]').html("<b>$"+total_budget.toFixed(2)+"</b>");
}

function removeLine(row) {
	var id = row.find('[name="expenseid[]"]').val();
	if(id != '') {
		$.ajax({
			type: "POST",
			url: "<?php echo WEBSITE_URL; ?>/Expense/expense_ajax.php?action=delete",
			data: { expenseid: id, source: '<?php echo $current_tab; ?>' },
			success: function(result) {
				console.log(result);
			}
		});
	}
	row.remove();
	$('tbody').find('a:contains("Add Row")').show();
	$('tbody').each(function() { $(this).find('tr').not(':last').find('a:contains("Add Row")').hide(); });
}
function approveLine(row) {
	var id = row.find('[name="expenseid[]"]').val();
	if(id != '') {
		$.ajax({
			type: "POST",
			url: "<?php echo WEBSITE_URL; ?>/Expense/expense_ajax.php?action=approve",
			data: { expenseid: id, source: '<?php echo $current_tab; ?>' },
			success: function(result) {
				console.log(result);
				window.location.reload();
			}
		});
	}
}
function payLine(row) {
	var id = row.find('[name="expenseid[]"]').val();
	if(id != '') {
		$.ajax({
			type: "POST",
			url: "<?php echo WEBSITE_URL; ?>/Expense/expense_ajax.php?action=pay",
			data: { expenseid: id, source: '<?php echo $current_tab; ?>' },
			success: function(result) {
				console.log(result);
				window.location.reload();
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
	$('tbody').each(function() { $(this).find('tr').not(':last').find('a:contains("Add Row")').hide(); });
	last.nextAll('tr').find('.form-control').first().focus();
	return false;
}
function addComment(row) {
	$('table tr').not(':first').hide();
	$(row).show();
	$('table').after('Comment: <input type="text" value="" name="temp_comment" class="form-control">');
	$('[name=temp_comment]').focus();
	$('[name=temp_comment]').blur(function() {
		var comment = this.value;
		$(this).remove();
		$('table tr').show();
		if(comment != '') {
			storeComment(row, comment);
		}
	});
}
function storeComment(row, comment) {
	var comments = row.find('[name="comments[]"],[name="submitted_comments[]"]').val();
	comments = ('' == comments ? comment : comments + "<br />" + comment);
	var id = row.find('[name="expenseid[]"],[name="submitted_expenseid[]"]').val();
	if(id != '') {
		$.ajax({
			type: "POST",
			url: "<?php echo WEBSITE_URL; ?>/Expense/expense_ajax.php?action=comment",
			data: { expenseid: id, comments: comments },
			success: function(result) {
				console.log(result);
			}
		});
	} else {
		row.find('[name="comments[]"]').val(comments);
	}
}
</script>

<?php
$currency_data = json_decode($currency_data, TRUE);
echo '<div class="form-group pull-right">';
if(strpos($tab_config,',manager,') !== FALSE && $current_tab != 'manager' && $current_tab != 'payables') {
	echo '<button type="submit" name="submit" value="approval" class="btn brand-btn mobile-block mobile-100 ">Submit All Expenses for Approval</button>';
} else if($current_tab == 'manager') {
	echo '<button type="submit" name="submit" value="payable" class="btn brand-btn mobile-block mobile-100 ">Approve All Expenses as Payables</button>';
} else if($current_tab == 'payables') {
	echo '<button type="submit" name="submit" value="paid" class="btn brand-btn mobile-block mobile-100 ">Mark All Expenses as Paid</button>';
} else {
	echo '<button type="submit" name="submit" value="payable" class="btn brand-btn mobile-block mobile-100 ">Submit All Expenses as Payables</button>';
}
echo '<button type="submit" name="submit" value="save" class="btn brand-btn mobile-block mobile-100 ">Save Expenses</button>';
echo '<button type="submit" name="submit" value="export" class="btn brand-btn mobile-block mobile-100 ">Save and Export to PDF <img src="../img/pdf.png"></button>';
echo '</div>';
echo '<div class="clearfix"></div>';

echo "<table class='table table-bordered'>";
echo "<tr class='hidden-xs hidden-sm'>";
$colspan = $totalspan = $tempcol = 0;
echo '<th>Heading</th>';
$tempcol++;
$colspan++;
$db_config_arr = array_filter(explode(',',trim($db_config,',')));
$tips_and_tax = [];
if(strpos($db_config,'Tips') !== FALSE) {
	$tips_and_tax[] = 'Tips';
}
if(strpos($db_config,'Tax') !== FALSE) {
	$tips_and_tax[] = 'Tax';
}
$detail = '';
if(strpos($db_config,'Exchange') !== FALSE) {
	$detail = ' After Currency Conversion';
	$tips_and_tax[] = 'Currency Conversion';
}
foreach($db_config_arr as $field) {
	if($field == 'Contact') {
		echo '<th>Expense Contact</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Expense For') {
		echo '<th>Expense Tab</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Description') {
		echo '<th style="width: 18em; max-width: 100%;">Description</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Country') {
		echo '<th>Country of Expense</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Province') {
		echo '<th>Province of Expense</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Exchange') {
		echo '<th>Exchange Currency</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Date') {
		echo '<th>Expense Date</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Work Order') {
		echo '<th>Work Order #</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Receipt') {
		echo '<th>Receipt</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Type') {
		echo '<th>Expense Type</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Day Expense') {
		echo '<th>Day Expense</th>';
		$tempcol++;
		$colspan++;
	} else if($field == 'Amount') {
		echo '<th>Amount'.(count($tips_and_tax) > 0 ? " Before ".implode(' & ', $tips_and_tax) : '').'</th>';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'Tips') {
		echo '<th>Tips</th>';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'Third Tax') {
		echo '<th>'.($hst_name == '' ? 'Third Tax' : $hst_name).'</th>';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'Local Tax') {
		echo '<th>'.($pst_name == '' ? 'Additional Tax' : $pst_name).'</th>';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'Tax') {
		echo '<th>'.($gst_name == '' ? 'Tax' : $gst_name).'</th>';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'Total') {
		echo '<th>Total'.$detail.'</th>';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'Budget') {
		echo '<th>Budget</th>';
		$totalspan = ($totalspan > 0 ? $totalspan : $tempcol);
		$colspan++;
	} else if($field == 'Reimburse') {
		echo '<th>Reimburse</th>';
		$tempcol++;
		$colspan++;
	}
}
echo '<th>Function</th>';
$colspan++;
echo "</tr>";

$total_amount = 0;
$total_tips = 0;
$total_hst = 0;
$total_pst = 0;
$total_gst = 0;
$total_total = 0;
$total_balance = 0;

$categories_sql = "SELECT * FROM (SELECT CONCAT('EC ',`ec`,': ',`category`) `ec_code`, `category`, CONCAT('GL ',`gl`,': ',`heading`) `gl_code`, `heading`, `amount` FROM `expense_categories` WHERE ('current_month'='$current_tab' || 'manager'='$current_tab' || 'payables'='$current_tab' || `expense_tab`='$current_tab') ORDER BY `ec`, `gl`) `categories`
	UNION SELECT 'Uncategorized', '', 'Misc', '', 0 FROM (SELECT COUNT(*) numrows FROM `expense_categories` WHERE `expense_tab`='$current_tab') cat_count WHERE numrows = 0";
$category_query = mysqli_query($dbc, $categories_sql);
$category_group = '';
while($cat_row = mysqli_fetch_array($category_query)) {
	// $query_expenses and $min_rows should be set by the tab page
	// $min_open is a setting
	$category_value = $cat_row['category'];
	$heading_value = $cat_row['heading'];
	$cat_amount = $cat_row['amount'];
	$result = mysqli_query($dbc, $query_expenses." AND `category`='$category_value' AND `title`='$heading_value'");
	$row_count = mysqli_num_rows($result);
	$row_count += $min_open;
	if($row_count < $min_rows) {
		$row_count = $min_rows;
	}
	echo "<tbody>";
	if($category_group != $cat_row['ec_code']) {
		$category_group = $cat_row['ec_code'];
		echo "<tr><td colspan='$colspan'><h3>".$category_group."</h3></td></tr>";
	}

	for($i = 0; $i < $row_count; $i++) {
		$id = $contact = $expense_tab = $category = $heading = $description = $country = $province = $currency = $date = $work_order = $receipt = $type = $day_expense = $amount = $tips = $third_tax = $local_tax = $tax = $total = $budget = $status = $comments = '';
		$reimburse = $exchange = 1;
		if($row = mysqli_fetch_array($result)) {
			$id = $row['expenseid'];
			$contact = $row['contact'];
			$expense_tab = $row['expense_for'];
			$category = $row['category'];
			$heading = $row['title'];
			$description = strip_tags(html_entity_decode($row['description']));
			$country = $row['country'];
			$province = $row['province'];
			$currency = $row['currency'];
			$exchange = $row['exchange_rate'];
			$date = $row['ex_date'];
			$work_order = $row['work_order'];
			$receipt = $row['ex_file'];
			$type = $row['type'];
			$day_expense = $row['day_expense'];
			$amount = $row['amount'];
			$tips = $row['tips'];
			$third_tax = $row['hst'];
			$local_tax = $row['pst'];
			$tax = $row['gst'];
			$total = $row['total'];
			$budget = $row['balance'];
			$status = $row['status'];
			$reimburse = $row['reimburse'];
			$comments = $row['comments'];
		} else {
			$country = $default_country;
			$province = $default_province;
		}
		if($cat_amount > 0) {
			$amount = $cat_amount;
			$province = 'N/A';
		}

		echo "<tr>";
		if($status == '' || ($status == 'Submitted' && $current_tab == 'manager') || ($status == 'Approved' && $current_tab == 'payables')) {
			echo "<input type='hidden' name='expenseid[]' value='".$id."'>";
			echo "<input type='hidden' name='comments[]' value='".$comments."'>";
			echo '<td data-title="'.$category_group.'">';
			echo '<input type="hidden" name="exchange_rate[]" value="'.$exchange.'">';
			echo '<input type="hidden" name="category[]" value="'.$cat_row['category'].'">';
			echo '<input type="hidden" name="heading[]" value="'.$cat_row['heading'].'">'.$cat_row['gl_code'].'</td>';

			foreach($db_config_arr as $field) {
				if($field == 'Contact') {
					echo '<td data-title="Expense Contact"><select data-placeholder="Choose a Contact..." name="contact[]" data-value="'.$contact.'" class="chosen-select-deselect form-control"></select></td>';
				} else if($field == 'Expense For') {
					echo '<td data-title="Expense Tab"><select data-placeholder="Choose an Expense For..." name="expense_for[]" class="chosen-select-deselect form-control" width="380">
						  <option value=""></option>
						  <option '.($expense_tab == 'business'?'selected':'').' value="business">Business Expense</option>
						  <option '.($expense_tab == 'customers'?'selected':'').' value="customers">Customer Expense</option>
						  <option '.($expense_tab == 'clients'?'selected':'').' value="clients">Client Expense</option>
						  <option '.($expense_tab == 'staff'?'selected':'').' value="staff">Staff Expense</option>
						  <option '.($expense_tab == 'sales'?'selected':'').' value="sales">Sales Expense</option>
						</select></td>';
				} else if($field == 'Description') {
					echo '<td data-title="Description"><input type="text" name="description[]" value="' .$description. '" class="form-control"></td>';
				} else if($field == 'Country') {
					echo '<td data-title="Country"><input type="text" name="country[]" value="' .$country. '" class="form-control"></td>';
				} else if($field == 'Province') {
					echo '<td data-title="Province"><select name="province[]" class="chosen-select-deselect">'.($cat_amount == 0 ? '<option></option>' : '');
					echo '<option '.($province == '--' ? 'selected' : '').' data-gst="0" data-pst="0" data-hst="0" value="--">N/A</option>';
					if($cat_amount == 0) {
						foreach($province_list as $province_data) {
							$data = explode('*',$province_data);
							echo "<option data-gst='".$data[1]."' data-pst='".$data[2]."' data-hst='".$data[3]."' ".($province == $data[0] ? 'selected' : '');
							echo " value='".$data[0]."'>".$data[0]."</option>";
						}
					}
					echo '</select></td>';
				} else if($field == 'Exchange') {
					echo '<td data-title="Exchange Currency"><select name="currency[]" class="chosen-select-deselect"><option></option>';
					foreach($currency_data['seriesDetail'] as $id => $info) {
						echo "<option data-currency='".$id."' ".($currency == $info['label'] ? 'selected' : '');
						echo " value='".$info['label']."'>".$info['label']."</option>";
					}
					echo '</select></td>';
				} else if($field == 'Date') {
					echo '<td data-title="Expense Date"><input type="text" name="ex_date[]" value="' .$date. '" class="form-control datepicker" onchange="calcTotal();"></td>';
				} else if($field == 'Work Order') {
					echo '<td data-title="Work Order #"><input type="text" name="work_order[]" value="' .$work_order. '" class="form-control"></td>';
				} else if($field == 'Receipt') {
					echo '<td data-title="Receipt">'.($receipt != '' ? '<a href="download/'.$receipt.'" target="_blank">View</a>' : '');
					?>
						<input type="file" class="jfilestyle" name="ex_file[]" data-input="false">
					</td>
				<?php
				} else if($field == 'Type') {
					echo '<td data-title="Type"><select data-placeholder="Select a Type" name="type[]" class="chosen-select-deselect form-control" width="380">
						<option value=""></option>';
						if (strpos($value_config, ','."Flight".',') !== FALSE) {
							echo '<option '.($type == 'Flight'?'selected':'').' value="Flight">Flight</option>';
						}
						if (strpos($value_config, ','."Hotel".',') !== FALSE) { ?>
							<option <?php if($type == 'Hotel') { echo 'selected'; } ?> value="Hotel">Hotel</option>
						<?php }
						if (strpos($value_config, ','."Breakfast".',') !== FALSE) { ?>
							<option <?php if($type == 'Breakfast') { echo 'selected'; } ?> value="Breakfast">Breakfast</option>
						<?php }
						if (strpos($value_config, ','."Lunch".',') !== FALSE) { ?>
							<option <?php if($type == 'Lunch') { echo 'selected'; } ?> value="Lunch">Lunch</option>
						<?php }
						if (strpos($value_config, ','."Dinner".',') !== FALSE) { ?>
							<option <?php if($type == 'Dinner') { echo 'selected'; } ?> value="Dinner">Dinner</option>
						<?php }
						if (strpos($value_config, ','."Beverages".',') !== FALSE) { ?>
							<option <?php if($type == 'Drink') { echo 'selected'; } ?> value="Drink">Beverages</option>
						<?php }
						if (strpos($value_config, ','."Transportation".',') !== FALSE) { ?>
							<option <?php if($type == 'Transportation') { echo 'selected'; } ?> value="Transportation">Transportation</option>
						<?php }
						if (strpos($value_config, ','."Entertainment".',') !== FALSE) { ?>
							<option <?php if($type == 'Entertainment') { echo 'selected'; } ?> value="Entertainment">Entertainment</option>
						<?php }
						if (strpos($value_config, ','."Gas".',') !== FALSE) { ?>
							<option <?php if($type == 'Gas') { echo 'selected'; } ?> value="Gas">Gas</option>
						<?php }
						if (strpos($value_config, ','."Misc".',') !== FALSE) { ?>
							<option <?php if($type == 'Misc') { echo 'selected'; } ?> value="Misc">Misc</option>
						<?php }
						$w5 = explode(',', $expense_types);
						foreach($w5 as $key=>$val) {
							echo '<option '.($val == $type ? 'selected' : '').' value="'.$val.'">'.$val.'</option>';
						}
					echo '</select></td>';
				} else if($field == 'Day Expense') {
					echo '<td data-title="Day Expense"><input type="text" name="day_expense[]" value="' . $day_expense . '" class="form-control"></td>';
				} else if($field == 'Amount') {
					echo '<td data-title="Amount'.(count($tips_and_tax) > 0 ? " Before ".implode(' & ', $tips_and_tax) : '').'"><input '.($cat_amount > 0 ? 'readonly' : '').' type="text" name="amount[]" onchange="calcTotal();" value="' . $amount . '" class="form-control">'.($cat_amount > 0 ? '<input type="hidden" name="cat_amt[]" value="' . $cat_amount . '">' : '').'</td>';
				} else if($field == 'Tips') {
					echo '<td data-title="Tips"><input type="text" name="tips[]" onchange="calcTotal();" value="' . $tips . '" class="form-control"></td>';
				} else if($field == 'Third Tax') {
					echo '<td data-title="'.$hst_name.'"><input type="text" readonly name="hst[]" onchange="calcTotal();" value="' . $third_tax . '" class="form-control"></td>';
				} else if($field == 'Local Tax') {
					echo '<td data-title="'.$pst_name.'"><input type="text" readonly name="pst[]" onchange="calcTotal();" value="' . $local_tax . '" class="form-control"></td>';
				} else if($field == 'Tax') {
					echo '<td data-title="'.$gst_name.'"><input type="text" readonly name="gst[]" onchange="calcTotal();" value="' . $tax . '" class="form-control"></td>';
				} else if($field == 'Total') {
					echo '<td data-title="Total'.$detail.'"><input type="text" readonly name="total[]" onchange="calcTotal();" value="' . $total . '" class="form-control"></td>';
				} else if($field == 'Budget') {
					echo '<td data-title="Budget"><input type="text" name="budget[]" value="' . $budget . '" class="form-control"></td>';
				} else if($field == 'Reimburse') {
					echo '<td data-title="Reimburse"><input '.($reimburse ? '' : 'checked').' type="checkbox" name="reimburse[]" value="0" class="form-control" style="display:none;"><label style="width:100%;">';
					echo '<input '.($reimburse ? 'checked' : '').' type="checkbox" name="reimburse[]" value="1" class="form-control" style="display: inline-block; height: 1.5em; width: 1.5em;" onchange="$(this).closest(\'td\').find(\'input\').first().attr(\'checked\',!this.checked);"> To be reimbursed</label></td>';
				}
			}
			echo '<td data-title="Function">';
				echo '<a href="" onclick="addRow(this); return false;" style="width:100%;">Add Row</a> | ';
				if($current_tab != 'manager' && $current_tab != 'payables' && vuaed_visible_function($dbc, 'expense')) {
					if($id > 0) {
						echo '<a href="add_expense.php?expenseid='.$id.'">Edit</a> | ';
					}
					echo '<a href="" onclick="if(confirm(\'Are you sure?\')) { removeLine($(this).closest(\'tr\')); } return false;">Archive</a> | ';
				}
				if($status == 'Submitted' && $current_tab == 'manager' && vuaed_visible_function($dbc, 'expense')) {
					echo '<a href="" onclick="approveLine($(this).closest(\'tr\')); return false;">Approve</a> | ';
					echo '<a href="" onclick="if(confirm(\'Are you sure?\')) { removeLine($(this).closest(\'tr\')); } return false;">Reject</a> | ';
					echo '<a href="add_expense.php?expenseid='.$id.'">Edit</a> | ';
				}
				if($status == 'Approved' && $current_tab == 'payables' && vuaed_visible_function($dbc, 'expense')) {
					echo '<a href="" onclick="payLine($(this).closest(\'tr\')); return false;">Mark as Paid</a> | ';
					echo '<a href="" onclick="if(confirm(\'Are you sure?\')) { removeLine($(this).closest(\'tr\')); } return false;">Reject</a> | ';
					echo '<a href="add_expense.php?expenseid='.$id.'">Edit</a> | ';
				}
				echo '<a href="Add Comment" onclick="addComment($(this).closest(\'tr\')); return false;">Comment</a>';
			echo '</td>';
		} else if($status != 'Rejected') {
			echo "<input type='hidden' name='submitted_expenseid[]' value='".$id."'>";
			echo "<input type='hidden' name='submitted_comments[]' value='".$comments."'>";
			echo '<input type="hidden" name="exchange_rate[]" value="'.$exchange.'">';
			echo '<td data-title="Expense Heading">'.$cat_row['gl_code'].'</td>';
			foreach($db_config_arr as $field) {
				if($field == 'Contact') {
					echo '<td data-title="Expense Contact">' .$contact. '</td>';
				} else if($field == 'Expense For') {
					echo '<td data-title="Expense Tab">' .ucwords($expense_tab). '</td>';
				} else if($field == 'Description') {
					echo '<td data-title="Description">' .$description. '</td>';
				} else if($field == 'Country') {
					echo '<td data-title="Country">' .$country. '</td>';
				} else if($field == 'Province') {
					echo '<td data-title="Province">' .$province. '</td>';
				} else if($field == 'Date') {
					echo '<td data-title="Expense Date">' .$date. '</td>';
				} else if($field == 'Work Order') {
					echo '<td data-title="Work Order #">' .$work_order. '</td>';
				} else if($field == 'Receipt') {
					echo '<td data-title="Receipt">'.($receipt != '' ? '<a href="download/'.$receipt.'" target="_blank">View</a>' : '').'</td>';
				} else if($field == 'Type') {
					echo '<td data-title="Type">'.$type.'</td>';
				} else if($field == 'Day Expense') {
					echo '<td data-title="Day Expense">' . $day_expense . '</td>';
				} else if($field == 'Amount') {
					echo '<td data-title="Amount"><input type="hidden" name="amount[]" value="'.$amount.'" disabled>' . $amount . '</td>';
				} else if($field == 'Tips') {
					echo '<td data-title="Tips"><input type="hidden" name="tips[]" value="'.$tips.'" disabled>' . $tips . '</td>';
				} else if($field == 'Third Tax') {
					echo '<td data-title="'.$hst_name.'"><input type="hidden" name="hst[]" value="'.$third_tax.'" disabled>' . $third_tax . '</td>';
				} else if($field == 'Local Tax') {
					echo '<td data-title="'.$pst_name.'"><input type="hidden" name="pst[]" value="'.$local_tax.'" disabled>' . $local_tax . '</td>';
				} else if($field == 'Tax') {
					echo '<td data-title="'.$gst_name.'"><input type="hidden" name="gst[]" value="'.$tax.'" disabled>' . $tax . '</td>';
				} else if($field == 'Total') {
					echo '<td data-title="Total"><input type="hidden" name="total[]" value="'.$total.'" disabled>' . $total . '</td>';
				} else if($field == 'Budget') {
					echo '<td data-title="Budget"><input type="hidden" name="budget[]" value="'.$budget.'" disabled>' . $budget . '</td>';
				} else if($field == 'Reimburse') {
					echo '<td data-title="Reimburse">' . ($reimburse ? 'To be reimbursed' : 'Not for reimbursement') . '</td>';
				}
			}
			echo '<td>'.$status.' | <a href="" onclick="addRow(this); return false;" style="width:100%;">Add Row</a>';
			if(vuaed_visible_function($dbc, 'expense') == 1) {
				echo ' | <a href="Add Comment" onclick="addComment($(this).closest(\'tr\')); return false;">Comment</a></td>';
			}
			echo '</td>';
		}
		echo "</tr>";
		$total_amount += (float)$amount;
		$total_tips += (float)$tips;
		$total_hst += (float)$third_tax;
		$total_pst += (float)$local_tax;
		$total_gst += (float)$tax;
		$total_total += (float)$total;
		$total_balance += (float)$budget;
	}
	echo "</tbody>";
}

echo "<tr>";
echo "<td colspan='$totalspan'><b>Total</b></td>";
foreach($db_config_arr as $i => $field) {
	if($field == 'Amount') {
		echo '<td data-name="total_amt" data-title="Amount"><b>$' . number_format($total_amount, 2, '.', '') . '</b></td>';
	} else if($field == 'Tips') {
		echo '<td data-name="total_tips" data-title="Tips"><b>$' . number_format($total_tips, 2, '.', '') . '</b></td>';
	} else if($field == 'Third Tax') {
		echo '<td data-name="total_hst" data-title="Tax"><b>$' . number_format($total_hst, 2, '.', '') . '</b></td>';
	} else if($field == 'Local Tax') {
		echo '<td data-name="total_pst" data-title="Tax"><b>$' . number_format($total_pst, 2, '.', '') . '</b></td>';
	} else if($field == 'Tax') {
		echo '<td data-name="total_gst" data-title="Tax"><b>$' . number_format($total_gst, 2, '.', '') . '</b></td>';
	} else if($field == 'Total') {
		echo '<td data-name="total_total" data-title="Total"><b>$' . number_format($total_total, 2, '.', '') . '</b></td>';
	} else if($field == 'Budget') {
		echo '<td data-name="total_budget" data-title="Budget"><b>$' . number_format($total_balance, 2, '.', '') . '</b></td>';
	} else if($i >= $totalspan) {
		echo "<td></td>";
	}
}
echo "<td></td></tr>";

echo '</table>';

echo '<div class="form-group col-sm-12">';
	echo '<label for="phpsign[]" class="col-sm-6 control-label">';
		if($current_tab == 'manager') {
			echo 'Management Approval:';
		} else if($current_tab == 'payables') {
			echo 'Paid Out:';
		} else {
			echo 'I AGREE TO THE FOLLOWING:<br />';
			echo 'ALL AMOUNTS INCLUDED ON THIS REPORT FOR REIMBURSEMENT WERE INCURRED FOR BUSINESS PURPOSES ONLY; AND ';
			echo 'I HAVE ATTACHED ALL DETAILED RECEIPTS TO SUPPORT MY REIMBURSEMENT CLAIM.<br />';
			echo 'Employee Signature:';
		}
	echo '</label>';
	echo '<div class="col-sm-6">';
		include ('../phpsign/sign.php');
	echo '</div>';
echo '</div><div class="clearfix"></div>';

echo '<div class="form-group pull-right">';
if(strpos($tab_config,',manager,') !== FALSE && $current_tab != 'manager' && $current_tab != 'payables') {
	echo '<button type="submit" name="submit" value="approval" class="btn brand-btn">Submit All Expenses for Approval</button>';
} else if($current_tab == 'manager') {
	echo '<button type="submit" name="submit" value="payable" class="btn brand-btn">Approve All Expenses as Payables</button>';
} else if($current_tab == 'payables') {
	echo '<button type="submit" name="submit" value="paid" class="btn brand-btn">Mark All Expenses as Paid</button>';
} else {
	echo '<button type="submit" name="submit" value="payable" class="btn brand-btn">Submit All Expenses as Payables</button>';
}
echo '<button type="submit" name="submit" value="save" class="btn brand-btn mobile-100-pull-right">Save Expenses</button>';
echo '<button type="submit" name="submit" value="export" class="btn brand-btn mobile-block mobile-100 ">Save and Export to PDF <img src="../img/pdf.png"></button>';
//echo '<button type="button" class="btn brand-btn mobile-100-pull-right" onclick="addRow();">Add Row</button>';
echo '</div>';
if($current_tab == 'manager' || $current_tab == 'payables') {
	$category_query = mysqli_query($dbc, $categories_sql);
	echo "<div class='clearfix'></div><h3>Summary - For Use by Accounting Staff</h3>";
	echo "<table class='table table-bordered'><tr class='hidden-xs hidden-sm'><th>Category & Heading</th><th>Expense Amount</th><th>Tax</th><th>Total</th></tr>";
	$final_amt = $final_tax = $final_total = 0;
	while($cat_row = mysqli_fetch_array($category_query)) {
		// $query_expenses and $min_rows should be set by the tab page
		// $min_open is a setting
		$category_value = $cat_row['category'];
		$heading_value = $cat_row['heading'];
		echo "<tr><td data-title='Category & Heading'>".$cat_row['ec_code'].': '.$cat_row['gl_code']."</td>";
		$result = mysqli_query($dbc, $query_expenses." AND `category`='$category_value' AND `title`='$heading_value'");
		$cat_amt = $cat_tax = $cat_total = 0;
		while($row = mysqli_fetch_array($result)) {
			$cat_amt += $row['amount'];
			$cat_tax += $row['pst'] + $row['gst'];
			$cat_total += $row['total'];
		}
		$final_amt += $cat_amt;
		$final_tax += $cat_tax;
		$final_total += $cat_total;
		echo "<td data-title='Amount'>$".number_format($cat_amt, 2, '.', '')."</td><td data-title='Tax'>$".number_format($cat_tax, 2, '.', '')."</td><td data-title='Total'>$".number_format($cat_total, 2, '.', '')."</td></tr>";
	}
	echo "<tr><td data-title=''><b>Totals</b></td><td data-title='Amount'><b>$".number_format($final_amt, 2, '.', '')."</b></td><td data-title='Tax'><b>$".number_format($final_tax, 2, '.', '')."</b></td><td data-title='Total'><b>$".number_format($final_total, 2, '.', '')."</b></td></tr>";
	echo "</table>";
}
?>
<style>
.element {
  display: inline-flex;
  align-items: center;
}
i.fa-camera {
  margin: 10px;
  cursor: pointer;
  font-size: 30px;
}
i:hover {
  opacity: 0.6;
}
?>
</style>
<script type='text/javascript'>
	$(":file").jfilestyle({input: false});
	var tImg = "<img src='../img/camera.png' width='50' height='50'/>";
	$(".focus-jfilestyle:contains('Choose file')").html(function (_, html) {
		 return html.replace(/Choose file/g , tImg )
	});
</script>
