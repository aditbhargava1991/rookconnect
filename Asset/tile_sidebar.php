<?php $current_tab = basename($_SERVER['PHP_SELF'], '.php'); ?>
<ul class="sidebar">
    <li class="standard-sidebar-searchbox">
        <form action="" method="POST">
        	<input name="search_asset" type="text" value="<?= $_POST['search_asset'] ?>" class="form-control search_asset" placeholder="Search Assets">
        	<input type="submit" value="search_asset_submit" name="search_asset_submit" style="display: none;">
        </form>
    </li>

	<a href='asset.php?category=Top'><li <?= $current_tab == 'asset' ? 'class="active"' : '' ?>>Assets</li></a>
	<ul style="margin: 0px;">
		<?php
	    $category = $_GET['category'];
	    $tabs = get_config($dbc, 'asset_tabs');
	    $each_tab = explode(',', $tabs);

	    $active_all = '';
	    if((empty($_GET['category']) || $_GET['category'] == 'Top') && $current_tab != 'order_list') {
	        $active_all = 'class="active"';
	    } ?>
	    <a href='asset.php?category=Top'><li <?= $active_all ?>>Last 25 Added</li></a>

	    <?php foreach ($each_tab as $cat_tab) {
	        $active_daily = '';
	        if((!empty($_GET['category'])) && ($_GET['category'] == $cat_tab)) {
	            $active_daily = 'class="active"';
	        }
			if($cat_tab !== '') { ?>
				<a href='asset.php?category=<?= $cat_tab ?>'><li <?= $active_daily ?>><?= $cat_tab ?></li></a>
			<?php }
	    } ?>
	</ul>
	<?php
	$asset_order_list = get_config($dbc, "asset_order_list");
	if($asset_order_list > 0) { ?>
		<a href='order_list.php'><li <?= $current_tab == 'order_list' ? 'class="active"' : '' ?>>Order Lists</li></a>
	<?php }
    //echo display_filter('asset.php');
	?>
</ul>