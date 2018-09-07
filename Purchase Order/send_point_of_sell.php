<?php
error_reporting(0);
/*
Payment/Invoice Listing SEA
*/
include_once ('../include.php');
include_once('../tcpdf/tcpdf.php');

$back_url = '?';
if(!empty($_GET['from'])) {
	$back_url = urldecode($_GET['from']);
}

if (isset($_POST['submit'])) {
    $posid = $_POST['posid'];
    $subject = $_POST['subject'];
    $email_body = $_POST['email_body'];

    $attachment = '';
    $attachment = 'download/invoice_'.$posid.'.pdf';
    $send_email = $_POST['email'];

	$dbc->query("UPDATE `purchase_orders` SET `date_sent`=CONCAT(IFNULL(CONCAT(`date_sent`,'#*#'),''),DATE(NOW())), `sent_by`=CONCAT(IFNULL(CONCAT(`sent_by`,'#*#'),''),'Email') WHERE `posid`='$posid'");
		$before_change = "";
	  $history = "Purchase order email has been sent. <br />";
	  add_update_history($dbc, 'po_history', $history, '', $before_change);
    send_email('', $_POST['email'], '', '', $subject, $email_body, $attachment);

    echo '<script type="text/javascript"> window.location.replace("'.$back_url.'"); </script>';
} ?>
<h1>Send Purchase Order to Client</h1>
<a href="<?php echo $back_url; ?>" class="btn config-btn btn-lg">Back to Dashboard</a>
<form name="invoice_table" method="post" action="" class="form-horizontal" role="form">
	<h3>Choose Name to send Purchase Order</h3>
	<?php
	$posid = $_GET['posid'];

	$contactid = get_pos($dbc, $posid, 'contactid');

	$resultt = mysqli_query($dbc, "SELECT contactid, first_name, last_name, email_address FROM contacts WHERE `contactid`='$contactid'");

	while($roww = mysqli_fetch_array( $resultt )) {
		if($roww['email_address'] != '') {
			echo '<input type="checkbox" value="'.decryptIt($roww['email_address']).'" style="height: 20px; width: 20px;" name="email[]">';
		} else {
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		echo '&nbsp;&nbsp;'.decryptIt($roww['first_name']).' '.decryptIt($roww['last_name']).' - '.decryptIt($roww['email_address']);
		echo '<br>';
	}
	?>
	<input type="hidden" name="posid" id="posid" value="<?php echo $posid; ?>">
	<br><br>
	<div class="form-group">
	<label for="company_name" class="col-sm-4 control-label">Email Subject:</label>
	<div class="col-sm-8">
	  <input name="subject" value="P.O. Attached" type="text" class="form-control">
	</div>
	</div>

	<div class="form-group">
	<label for="company_name" class="col-sm-4 control-label">Email Body:</label>
	<div class="col-sm-8">
	  <textarea name="email_body" rows="5" cols="50" class="form-control"><?php echo 'Please find attachment for your Purchase Order.';?></textarea>
	</div>
	</div>

	<a href="<?php echo $back_url; ?>" class="btn brand-btn btn-lg	pull-left">Back</a>
	<button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>

</form>
