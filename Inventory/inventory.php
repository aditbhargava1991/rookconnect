<?php
/*
Inventory Listing
*/
include ('../include.php');
error_reporting(0);
if(isset($_GET['order_list'])) {
	$order_id = $_GET['order_list'];
	$get_driver = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM order_lists WHERE order_id='$order_id'"));
	$inventoryidorder = $get_driver['inventoryid'];
	$order_list = '&order_list='.$_GET['order_list'].'';
	if(isset($_GET['currentlist'])) {
		$currentlist = 'on';
	} else {
		echo $currentlist = '';
	}
} else {
	$order_list = '';
	$currentlist = '';
}

$rookconnect = get_software_name();
?>
<script type="text/javascript" src="inventory.js"></script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('inventory');
$inventory_navigation_position = get_config($dbc, 'inventory_navigation_position');
$dropdownornot ='';
$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_category_dropdown'"));
if($get_config['configid'] > 0) {
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_category_dropdown'"));
	if($get_config['value'] == '1') {
		$dropdownornot = 'true';
	}
}
$active_cat = 'All';
$set_check_value    = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM inventory_setting WHERE inventorysettingid = 1"))['value'].','; ?>
<div id="inventory_div" class="container">
	<div class='iframe_holder' style='display:none;'>
		<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframe' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
    </div>
	<div class="row hide_on_iframe">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<?php include('../Inventory/tile_header.php'); ?>
			</div>

			<div class="tile-container" style="height: 100%;">
            	<?php include('../Inventory/mobile_view.php'); ?>

				<?php if($inventory_navigation_position == 'top') {
					include('../Inventory/tile_nav_top.php');
				} ?>

	            <?php if($inventory_navigation_position != 'top') { ?>
		            <div class="standard-collapsible tile-sidebar set-section-height hide-titles-mob">
		            	<?php include('../Inventory/tile_sidebar.php'); ?>
		            </div>
	            <?php } ?>

	            <div class="scale-to-fill has-main-screen tile-content hide-titles-mob">
					<div class="main-screen standard-body">
						<div class="standard-body-title"><h3><?= empty($_GET['category']) && strpos($set_check_value, ',summary') !== FALSE ? 'Summary' : 'Inventory' ?></h3></div>
						<div class="standard-body-content pad-left pad-right">
							<?php if(empty($_GET['category']) && strpos($set_check_value, ',summary') !== FALSE) { ?>
								<?php if(strpos($set_check_value, ',summary category,') !== FALSE) { ?>
									<div class="col-sm-6">
										<div class="overview-block">
											<h4><?= INVENTORY_TILE ?> by Category</h4>
											<?php $inventory_list = $dbc->query("SELECT COUNT(*) count, SUM(`quantity`) sum, `category` FROM `inventory` WHERE `deleted`=0 GROUP BY `category`");
											while($inventory = $inventory_list->fetch_assoc()) { ?>
												<b><a href="inventory.php?category=<?= preg_replace('/[^a-z]/','',strtolower($inventory['category'])) ?>"><?= $inventory['category'] ?></a></b>: <?= INVENTORY_NOUN ?> Types: <?= $inventory['count'] ?>, Total Quantity: <?= $inventory['sum'] ?><br />
											<?php } ?>
										</div>
									</div>
								<?php } ?>
							<?php } else { ?>
					        	<!-- Notice -->
					            <div class="notice gap-bottom gap-top popover-examples">
					                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
					                <div class="col-sm-11"><span class="notice-name">NOTE:</span>
						            This tile monitors all physical inventory for resale, displaying the main overview of your inventory. These items are managed by the software with auto Min Bin reminders for when each inventory item reaches a designated level.</div>
					                <div class="clearfix"></div>
					            </div>
					            <?php include('../Inventory/inventory_inc.php'); ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<?php include ('../footer.php'); ?>
