<?php
/*
 * Add Coupon for POS
 */
include ('../include.php');
error_reporting(0);

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $title			= filter_var ( htmlentities ( $_POST['title'] ), FILTER_SANITIZE_STRING );
    $description	= filter_var ( htmlentities ( $_POST['description'] ), FILTER_SANITIZE_STRING );
    $discount_type	= filter_var ( $_POST['discount_type'], FILTER_SANITIZE_STRING );
    $discount		= filter_var ( $_POST['discount'], FILTER_SANITIZE_NUMBER_INT );
    $start_date		= filter_var ( $_POST['start_date'], FILTER_SANITIZE_STRING );
    $expiry_date	= filter_var ( $_POST['expiry_date'], FILTER_SANITIZE_STRING );

    if ( empty ( $_POST['couponid'] ) ) {
        $query_insert = "INSERT INTO `pos_touch_coupons` (`title`, `description`, `discount_type`, `discount`, `start_date`, `expiry_date`) VALUES ('$title', '$description', '$discount_type', '$discount', '$start_date', '$expiry_date')";
        $result_insert = mysqli_query($dbc, $query_insert);
        $couponid = mysqli_insert_id($dbc);

        $before_change = '';
        $history = "Point of Sale Coupon Added. <br />";
        add_update_history($dbc, 'pos_history', $history, '', $before_change);

	} else {
        $couponid = $_POST['couponid'];

        $before_change = '';

        $query_update = "UPDATE `pos_touch_coupons` SET `title`='$title', `description`='$description', `discount_type`='$discount_type', `discount`='$discount', `start_date`='$start_date', `expiry_date`='$expiry_date'";
        $result_update = mysqli_query($dbc, $query_update);

        $history = "Point of Sale Coupon entries updated. <br />";
        add_update_history($dbc, 'pos_history', $history, '', $before_change);
    }

    echo '<script type="text/javascript">window.location.replace("coupons.php");</script>';
}

?>
<script type="text/javascript">
	$(document).ready(function() {
		$("#form1").submit(function( event ) {
			var title			= $("#title").val();
			var discount_type	= $("#discount_type").val();
			var discount		= $("#discount").val();
			var start_date		= $("#start_date").val();
			var expiry_date		= $("#expiry_date").val();

			if ( title=='' || discount_type=='' || discount=='' || start_date=='' || expiry_date=='' ) {
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
	checkAuthorised('pos');
?>
<div class="container">
	<div class="row">

		<div class="col-sm-10"><h1><?= POS_ADVANCE_TILE ?> Coupon</h1></div>
		<div class="col-sm-2 double-gap-top"><?php
			if ( config_visible_function($dbc, 'pos') == 1 ) {
				echo '<a href="field_config_pos.php" class="mobile-block pull-right"><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
			} ?>
		</div>

		<div class="clearfix gap-bottom"></div>

		<div class="gap-left double-gap-bottom"><a href="coupons.php" class="btn config-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal"><?php
			$title			= '';
			$description	= '';
			$discount_type	= '';
			$discount		= '';
			$start_date		= '';
			$expiry_date	= '';

			if ( !empty ( $_GET['couponid'] ) ) {
				$couponid = $_GET['couponid'];
				$row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT * FROM `pos_touch_coupons` WHERE `couponid`='$couponid'" ) );

				$title			= $row['title'];
				$description	= $row['description'];
				$discount_type	= $row['discount_type'];
				$discount		= $row['discount'];
				$start_date		= $row['start_date'];
				$expiry_date	= $row['expiry_date']; ?>
				<input type="hidden" id="couponid" name="couponid" value="<?php echo $couponid ?>" /><?php
			} ?>

			<div class="form-group">
				<label for="company_name" class="col-sm-4 control-label"><span class="hp-red">*</span> Title:</label>
				<div class="col-sm-8"><input name="title" id="title" value="<?php echo ( !empty($title) ) ? $title : ''; ?>" type="text" class="form-control"></div>
			</div>
			<div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Description:</label>
				<div class="col-sm-8"><textarea name="description" id="description" class="form-control"><?php echo ( !empty($description) ) ? $description : ''; ?></textarea></div>
			</div>
			<div class="form-group">
				<label for="company_name" class="col-sm-4 control-label"><span class="hp-red">*</span> Discount Type:</label>
				<div class="col-sm-8">
					<select name="discount_type" id="discount_type" data-placeholder="Select Discount Type" class="chosen-select-deselect form-control">
						<option value=""></option>
						<option value="%">%</option>
						<option value="$">$</option><?php
						$result = mysqli_query ( $dbc, "SELECT DISTINCT(`discount_type`) FROM `pos_touch_coupons`" );
						while ( $row=mysqli_fetch_assoc($result) ) {
							if ( $discount_type==$row['discount_type'] ) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}
							echo '<option ' . $selected . ' value="' . $row['discount_type'] . '">' . $row['discount_type'] . '</option>';
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="company_name" class="col-sm-4 control-label"><span class="hp-red">*</span> Discount:</label>
				<div class="col-sm-8"><input name="discount" id="discount" value="<?php echo ( !empty($discount) ) ? $discount : ''; ?>" type="text" class="form-control"></div>
			</div>
			<div class="form-group">
				<label for="company_name" class="col-sm-4 control-label"><span class="hp-red">*</span> Start Date:</label>
				<div class="col-sm-8"><input name="start_date" id="start_date" value="<?php echo ( !empty($start_date) ) ? $start_date : ''; ?>" type="text" class="form-control datepicker"></div>
			</div>
			<div class="form-group">
				<label for="company_name" class="col-sm-4 control-label"><span class="hp-red">*</span> Expiry Date:</label>
				<div class="col-sm-8"><input name="expiry_date" id="expiry_date" value="<?php echo ( !empty($expiry_date) ) ? $expiry_date : ''; ?>" type="text" class="form-control datepicker"></div>
			</div>

			<div class="clearfix"></div>

			<div class="form-group">
				<p><span class="hp-red"><em>Required Fields *</em></span></p>
			</div>

			<div class="form-group">
				<div class="col-sm-6">
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking here will not save your entry."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a href="coupons.php" class="btn brand-btn btn-lg">Back</a>
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
