<?php
/*
Add Invoice
*/
include ('../include.php');
error_reporting(0);

if (isset($_POST['submit_pay'])) {
    $all_invoiceid = $_POST['invoiceid'];
    $payment_type = $_POST['payment_type'];

    $var=explode(',',$all_invoiceid);
    foreach($var as $invoiceid) {
        $before_change = capture_before_change($dbc, 'point_of_sell', 'payment_type', 'posid', $invoiceid);
        $before_change .= capture_before_change($dbc, 'point_of_sell', 'status', 'posid', $invoiceid);

        $query_invoice = "UPDATE `point_of_sell` SET `payment_type` = '$payment_type', `status` = 'Completed' WHERE `posid` = '$invoiceid'";
        $result_invoice = mysqli_query($dbc, $query_invoice);

        $history = capture_after_change('payment_type', $payment_type);
        $history .= capture_after_change('status', 'Completed');
        add_update_history($dbc, 'pos_history', $history, '', $before_change);
    }
    echo '<script type="text/javascript"> window.location.replace("unpaid_invoice.php"); </script>';
}

?>

</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('pos');
?>
<div class="container">
  <div class="row">

	    <h1 class="triple-pad-bottom">Pay Invoice</h1>

  		<form id="form_pay_invoice" name="form_pay_invoice" method="post" action="pay_invoice.php" enctype="multipart/form-data" class="form-horizontal" role="form">

		<input type="hidden" name="invoiceid" value=<?php echo $_GET['invoiceid']; ?> >

		<?php
		$tags = explode(',',$_GET['invoiceid']);
		$total_count = 0;
		foreach($tags as $invoiceid) {
			$get_inventory =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT total_price FROM point_of_sell WHERE posid='$invoiceid'"));
			$total_count += $get_inventory['total_price'];
		}
		?>

          <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Payment Total:</label>
            <div class="col-sm-8">
              <?php echo '$'.$total_count; ?>
            </div>
          </div>

              <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">Payment Type<span class="brand-color">*</span>:</label>
                <div class="col-sm-8">
                  <select id="payment_type" name="payment_type" data-placeholder="Choose a Type..." class="chosen-select-deselect form-control" width="380">
                      <option value=""></option>
                      <?php
                        $tabs = get_config($dbc, 'invoice_payment_types');
                        $each_tab = explode(',', $tabs);
                        foreach ($each_tab as $cat_tab) {
                            if ( $invtype == $cat_tab || strpos ( $cat_tab, $payment_type ) !== FALSE ) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                        }
                      ?>
                  </select>
                </div>
              </div>

          <div class="form-group">
            <div class="col-sm-4 clearfix">
                <a href="unpaid_invoice.php" class="btn brand-btn pull-right">Back</a>
            </div>
            <div class="col-sm-8">
                <button type="submit" name="submit_pay" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
            </div>
          </div>

		</form>

    </div>
  </div>
<?php include ('../footer.php'); ?>
