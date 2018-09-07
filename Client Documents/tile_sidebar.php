<!-- Tile Sidebar -->
<?php
$search_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT distinct(client_documents_type) FROM client_documents WHERE deleted=0 order by client_documents_type"),MYSQLI_ASSOC);
$search_categories = mysqli_fetch_all(mysqli_query($dbc, "SELECT distinct(category) FROM client_documents WHERE deleted=0 order by category"),MYSQLI_ASSOC);
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
        	<input name="search_vendor" type="text" value="<?= $_POST['search_vendor'] ?>" class="form-control search_vendor" placeholder="Search Document">
        	<input type="submit" name="search_user_submit" style="display: none;">
        </form>
    </li>

<h4 style="padding-left: 0.5em;">Type</h4>
    <?php foreach($search_types as $row) { ?>
    <a href="" onclick="submitButton('<?= $row['client_documents_type'] ?>', '<?= $_POST['search_category'] ?>'); return false;"><li <?= ($search_type == $row['client_documents_type'] ? 'class="active"' : '') ?>><?= $row['client_documents_type'] ?></li></a>
    <?php } ?>
<h4 style="padding-left: 0.5em;">Category</h4>
    <?php foreach($search_categories as $row) { ?>
    <a href="" onclick="submitButton('<?= $_POST['search_type'] ?>', '<?= $row['category'] ?>'); return false;"><li <?= ($search_category == $row['category'] ? 'class="active"' : '') ?>><?= $row['category'] ?></li></a>
    <?php } ?>
</ul>