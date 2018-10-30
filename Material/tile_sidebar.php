<?php $current_tab = basename($_SERVER['PHP_SELF'], '.php');
$current_cat = $_GET['category']; ?>
<ul class="sidebar">
    <li class="standard-sidebar-searchbox">
        <form action="" method="POST">
        	<input name="search_material" type="text" value="<?= $_POST['search_material'] ?>" class="form-control search_material" placeholder="Search Materials">
        	<input type="submit" value="search_material_submit" name="search_material_submit" style="display: none;">
        </form>
    </li>

	<a href='material.php?filter=Top'><li <?= $current_tab == 'material' ? 'class="active"' : '' ?>>Materials</li></a>
	<?php $cat_list = mysqli_query($dbc,"SELECT distinct(category) FROM material where deleted = 0");
	if(mysqli_num_rows($cat_list) > 0) { ?>
		<ul style="margin: 0px;">
			<?php while($cat_tab = mysqli_fetch_array($cat_list)) {
				if(!empty($cat_tab['category'])) { ?>
					<a href='material.php?category=<?= $cat_tab['category'] ?>'><li <?= $current_cat == $cat_tab['category'] ? 'class="active"' : '' ?>><?= $cat_tab['category'] ?></li></a>
				<?php }
			} ?>
		</ul>
	<?php }
	$material_order_list = get_config($dbc, "material_order_list");
	if($material_order_list > 0) { ?>
		<a href='order_list.php'><li <?= $current_tab == 'order_list' ? 'class="active"' : '' ?>>Order Lists</li></a>
	<?php }
	?>
</ul>