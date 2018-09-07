<!-- Tile Sidebar -->
<?php
$search_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`marketing_material_type`) FROM `marketing_material` WHERE `deleted` = 0 ORDER BY `marketing_material_type`"),MYSQLI_ASSOC);
$search_categories = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `marketing_material` WHERE `deleted` = 0 ORDER BY `category`"),MYSQLI_ASSOC);
?>
<script type="text/javascript">
function submitButton(search_type, search_category) {
    $('[name="search_type"]').val(search_type);
    $('[name="search_category').val(search_category);
    $('[name="search_user_submit"]').click();
}
</script>
<input type="hidden" name="search_type" value="<?= $_POST['search_type'] ?>">
<input type="hidden" name="search_category" value="<?= $_POST['search_category'] ?>">
<ul class="sidebar">
    <li class="standard-sidebar-searchbox">
        <form action="" method="POST">
        	<input name="search_vendor" type="text" value="<?= $_POST['search_vendor'] ?>" class="form-control search_vendor" placeholder="Search Material">
        	<input type="submit" name="search_user_submit" style="display: none;">
        </form>
    </li>
<h4 style="padding-left: 0.5em;">Type</h4>
    <?php foreach($search_types as $row) { ?>
    <a href="" onclick="submitButton('<?= $row['marketing_material_type'] ?>', '<?= $_POST['search_category'] ?>'); return false;"><li <?= ($search_type == $row['marketing_material_type'] ? 'class="active"' : '') ?>><?= $row['marketing_material_type'] ?></li></a>
    <?php } ?>
<h4 style="padding-left: 0.5em;">Category</h4>
    <?php foreach($search_categories as $row) { ?>
    <a href="" onclick="submitButton('<?= $_POST['search_type'] ?>', '<?= $row['category'] ?>'); return false;"><li <?= ($search_category == $row['category'] ? 'class="active"' : '') ?>><?= $row['category'] ?></li></a>
    <?php } ?>
</ul>