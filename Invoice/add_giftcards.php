<?php
/*
 * Add Coupon for POS
 */
include ('../include.php');
error_reporting(1);

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    //$created_for			= filter_var ( htmlentities ( $_POST['created_for'] ), FILTER_SANITIZE_STRING );
    $description	= filter_var ( htmlentities ( $_POST['description'] ), FILTER_SANITIZE_STRING );
    $value	= filter_var ( $_POST['value'], FILTER_SANITIZE_STRING );
    $issue_date		= filter_var ( $_POST['issue_date'], FILTER_SANITIZE_STRING );
    $expiry_date	= filter_var ( $_POST['expiry_date'], FILTER_SANITIZE_STRING );
    $created_by = $_SESSION['contactid'];
    $giftcard_number = filter_var ( $_POST['gift_card_number'], FILTER_SANITIZE_STRING );

    if ( empty ( $_POST['giftcardid'] ) ) {
        $query_insert = "INSERT INTO `pos_giftcards` (`created_by`, `description`, `value`, `issue_date`, `giftcard_number`, `expiry_date`) VALUES ('$created_by', '$description', '$value', '$issue_date', '$giftcard_number', '$expiry_date')";
        $result_insert = mysqli_query($dbc, $query_insert);
        $giftcardid = mysqli_insert_id($dbc);

	} else {
        $giftcardid = $_POST['giftcardid'];
        $query_update = "UPDATE `pos_giftcards` SET `created_by`='$created_by', `description`='$description', `value`='$value', `issue_date`='$issue_date', `giftcard_number`='$giftcard_number', `expiry_date`='$expiry_date' WHERE `posgiftcardsid` = '$giftcardid'";
        $result_update = mysqli_query($dbc, $query_update);
    }

    echo '<script type="text/javascript">window.location.replace("giftcards.php");</script>';
}

?>
<script type="text/javascript">
	$(document).ready(function() {
		$("#form1").submit(function( event ) {
			var created_for			= $("#created_for").val();
			var value	= $("#value").val();
			var issue_date		= $("#issue_date").val();
			if ( created_for=='' || value=='' || issue_date=='' || expiry_date=='') {
				alert("Please make sure you have filled in all of the required fields.");
				return false;
			}
		});
	});
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
?>
<div class="container">
	<div class="row">

		<div class="col-sm-10"><h1><?= POS_ADVANCE_TILE ?> Gift Card</h1></div>
		<div class="col-sm-2 double-gap-top"><?php
			if ( config_visible_function($dbc, 'pos') == 1 ) {
				echo '<a href="field_config_invoice.php" class="mobile-block pull-right"><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
			} ?>
		</div>

		<div class="clearfix gap-bottom"></div>

		<div class="gap-left double-gap-bottom"><a href="giftcards.php" class="btn config-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal"><?php
			$giftcard_number = '';
			$title			= '';
			$description	= '';
			$discount_type	= '';
			$discount		= '';
			$start_date		= '';
			$expiry_date	= '';

			if ( !empty ( $_GET['giftcardid'] ) ) {
				$giftcardid = $_GET['giftcardid'];
				$row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT * FROM `pos_giftcards` WHERE `posgiftcardsid`='$giftcardid'" ) );

				$giftcard_number = $row['giftcard_number'];
				$created_for			= $row['created_for'];
				$description	= $row['description'];
				$value	= $row['value'];
				$issue_date		= $row['issue_date'];
				$expiry_date	= $row['expiry_date']; ?>
				<input type="hidden" id="giftcardid" name="giftcardid" value="<?php echo $giftcardid ?>" /><?php
			} ?>
			<!--<div class="form-group">
				<label for="cer" class="col-sm-4 control-label"><span class="hp-red">*</span>Created For:</label>
				<div class="col-sm-8">
          <select data-placeholder="Select a Contact..." name="created_for" id="created_for" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
                <?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0"),MYSQLI_ASSOC));
                foreach($query as $rowid) {
                  echo "<option ".($rowid == $created_for ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
                } ?>
          </select>
        </div>
			</div>-->
      <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label"><span class="hp-red">*</span> Gift Card Number:</label>
				<div class="col-sm-8"><input type="text" name="gift_card_number" id="gift_card_number" class="form-control" value="<?php echo ( !empty($giftcard_number) ) ? $giftcard_number : ''; ?>"></div>
			</div>
			<div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Description:</label>
				<div class="col-sm-8"><textarea name="description" id="description" class="form-control"><?php echo ( !empty($description) ) ? $description : ''; ?></textarea></div>
			</div>
			<div class="form-group">
				<label for="giftcard_value" class="col-sm-4 control-label"><span class="hp-red">*</span> Gift Card Value:</label>
				<div class="col-sm-8"><input name="value" id="value" value="<?php echo "$" . ( !empty($value) ) ? $value : ''; ?>" type="text" class="form-control"></div>
			</div>
			<div class="form-group">
				<label for="issue_date" class="col-sm-4 control-label"><span class="hp-red">*</span> Issue Date:</label>
				<div class="col-sm-8"><input name="issue_date" id="issue_date" value="<?php echo ( !empty($issue_date) ) ? $issue_date : ''; ?>" type="text" class="form-control datepicker"></div>
			</div>
			<?php if (strpos($value_config, ',Expiry Date GF,') !== FALSE ) { ?>
				<div class="form-group">
					<label for="expiry_date" class="col-sm-4 control-label">Expiry Date:</label>
					<div class="col-sm-8"><input name="expiry_date" id="expiry_date" value="<?php echo ( !empty($expiry_date) ) ? $expiry_date : ''; ?>" type="text" class="form-control datepicker"></div>
				</div>
			<?php } ?>
			<div class="clearfix"></div>
			<div class="form-group">
				<p><span class="hp-red"><em>Required Fields *</em></span></p>
			</div>
			<div class="form-group">
				<div class="col-sm-6">
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking here will not save your entry."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a href="giftcards.php" class="btn brand-btn btn-lg">Back</a>
					<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button type="submit" name="add_coupon" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
					<span class="popover-examples list-inline pull-right" style="margin:15px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add this entry."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</div>
				<div class="clearfix"></div>
			</div>
		</form>
	</div>
</div>
<?php include ('../footer.php'); ?>
