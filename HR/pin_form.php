<?php include_once('../include.php');
$pinned = array_unique(explode(',',trim($_GET['pinned'],','))); ?>
<script>
function add_user(img) {
    var block = $(img).closest('.form-group');
    destroyInputs();
    var clone = block.clone();
    clone.find('select').val('');
    block.after(clone);
    initInputs();
    initTooltips();
}
function rem_user(img) {
    if($('[name=user_list]').length <= 1) {
        add_user(img);
    }
    $(img).closest('.form-group').remove();
    initTooltips();
    savePinned();
}
function savePinned() {
    var users = [];
    $('[name=user_list]').each(function() {
        users.push(this.value);
    });
	$.ajax({
		url: 'hr_ajax.php?action=mark_pinned',
		method: 'POST',
		data: {
			users: users,
			id: $('[name=id]').val(),
			item: $('[name=type]').val()
		}
	});
	$(this).closest('.pinned').hide();
	$(this).closest('.pull-right').css('width','20em');
}
</script>
<input type="hidden" name="id" value="<?= $_GET['id'] ?>">
<input type="hidden" name="type" value="<?= $_GET['type'] ?>">
<div class="pad-10">
    <h3>Pinned Users<a href="../blank_loading_page.php" class="pull-right no-toggle" title="Close"><img src="../img/icons/cancel.png" class="inline-img"></a></h3>
    <div class="clearfix"></div>
    <?php foreach($pinned as $pin) { ?>
        <div class="form-group">
            <div class="pull-right" style="width:6em;">
                <img src="../img/icons/ROOK-add-icon.png" class="no-toggle cursor-hand inline-img" title="Add Users" onclick="add_user(this);">
                <img src="../img/remove.png" class="no-toggle cursor-hand inline-img" title="Remove Users" onclick="rem_user(this);">
            </div>
            <div class="scale-to-fill">
                <label class="col-sm-4">Pinned Users:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Select Users and Levels" name="user_list" class="chosen-select-deselect" onchange="savePinned();"><option></option>
                        <option <?= ($pin == 'ALL' ? 'selected' : '') ?> value="ALL">All Users</option>
                        <?php foreach(get_security_levels($dbc) as $level_label => $level_name) { ?>
                            <option <?= ($pin == $level_name ? 'selected' : '') ?> value="<?= $level_name ?>"><?= $level_label ?></option>
                        <?php } ?>
                        <?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1")) as $contact) { ?>
                            <option <?= ($pin == $contact['contactid'] ? 'selected' : '') ?> value="<?= $contact['contactid'] ?>"><?= $contact['full_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    <?php } ?>
    <a href="../blank_loading_page.php" class="pull-right no-toggle btn brand-btn" title="Close">Submit</a>
</div>