<?php include_once('../include.php');
checkAuthorised('estimate');
if(!empty($_POST['submit'])) {
	$heading_id = $_POST['heading_id'];
	$product_pricing = $_POST['productpricing'];
	$sort_order = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT MAX(`sort_order`) `sort_order` FROM `estimate_template_lines` WHERE `heading_id` = '$heading_id' AND `deleted` = 0"))['sort_order'];
    foreach($_POST['inventoryid'] as $i => $inventoryid) {
        $quantity = $_POST['vpl_quantity'][$i];

        if($inventoryid > 0 && $quantity > 0) {
        	$sort_order++;
            $query_insert = "INSERT INTO `estimate_template_lines` (`heading_id`, `src_table`, `src_id`, `qty`, `sort_order`, `product_pricing`) VALUES ('$heading_id', 'vpl', '$inventoryid', '$quantity', '$sort_order', '$product_pricing')";
            mysqli_query($dbc, $query_insert);
						$before_change = '';
						$history = "Estimates template line entry has been added. <br />";
						add_update_history($dbc, 'estimates_history', $history, '', $before_change);
        }
    }
}
?>

<form class="col-sm-12 form-horizontal" action="" method="POST" enctype="multipart/form-data">
	<h2>Load Order Form</h2><a class="pull-right" href="../blank_loading_page.php"><img class="slider-close" src="../img/icons/cancel.png"></a><br />
	<input type="hidden" name="heading_id" value="<?= $_GET['heading_id'] ?>">
	<?php include('../Estimate/estimate_scope_add_vpl.php'); ?>
	<div class="clearfix pad-vertical"></div>
	<a class="btn brand-btn pull-left" href="../blank_loading_page.php">Cancel</a>
	<button class="btn brand-btn pull-right" type="Submit" name="submit" value="submit">Apply to Template</button>
</form>
