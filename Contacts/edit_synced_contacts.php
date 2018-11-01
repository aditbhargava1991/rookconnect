<?php include_once('../include.php'); ?>

<script type="text/javascript">
$(document).on('change', 'select[name="synced_category"]', function() { filterSyncedCategory(this); });
$(document).on('change', 'select[name="synced_contact"]', function() { saveSyncedContact(this); });
function filterSyncedCategory(sel) {
    var block = $(sel).closest('.synced_div');
    var category = $(sel).find('option:selected').val();

    if(category != undefined && category != '') {
        $(block).find('[name="synced_contact"] option').hide();
        $(block).find('[name="synced_contact"] option[data-category="'+category+'"]').show();
        $(block).find('[name="synced_contact"] option[value="ADD_NEW"]').show();
    } else {
        $(block).find('[name="synced_contact"] option').show();
    }
    $(block).find('[name="synced_contact"]').trigger('change.select2');
}
function saveSyncedContact(sel) {
    var contactid = $('[name="contactid"]').val();
    var block = $(sel).closest('.synced_div');
    var contactsyncid = $(block).data('id');
    var synced_contactid = $(sel).find('option:selected').val();

    if(synced_contactid == 'ADD_NEW') {
        overlayIFrameSlider('../Contacts/contacts_inbox.php?fields=all_fields&edit=new&category='+$(block).find('[name="synced_category"] option:selected').val(), '75%', true, true);
        iframe_contactid = 0;
        var iframe_check = setInterval(function() {
            if(!$('.iframe_overlay iframe').is(':visible')) {
                if(iframe_contactid > 0) {
                    $.post('../Contacts/contacts_ajax.php?action=get_contact_list', { contactid: iframe_contactid }, function(response) {
                        $(sel).html(response);
                        $(sel).trigger('change.select2');
                        $(sel).val(iframe_contactid).change();
                    });
                }
                clearInterval(iframe_check);
            } else if(!(iframe_contactid > 0)) {
                iframe_contactid = $($('.iframe_overlay iframe').get(0).contentDocument).find('[name=contactid]').val();
            }
        }, 500);
    } else {
        $.ajax({
            url: '../Contacts/contacts_ajax.php?action=save_synced_contact',
            method: 'POST',
            data: { contactid: contactid, contactsyncid: contactsyncid, synced_contactid: synced_contactid },
            success: function(response) {
                if(response > 0) {
                    $(block).data('id', response);
                }
            }
        });
    }
}
function addSyncedContact() {
    destroyInputs('.synced_div');
    var block = $('.synced_div').last();
    var clone = $(block).clone();

    clone.data('id', '');
    clone.find('select').val('');

    $(block).after(clone);

    initInputs('.synced_div');
}
function removeSyncedContact(img) {
    if($('.synced_div').length <= 1) {
        addSyncedContact();
    }

    var contactsyncid = $(img).closest('.synced_div').data('id');
    $.ajax({
        url: '../Contacts/contacts_ajax.php?action=delete_synced_contact',
        method: 'POST',
        data: { contactsyncid: contactsyncid },
        success: function(response) {
            $(img).closest('.synced_div').remove();
        }
    });
}
function viewSyncedContact(img) {
    var block = $(img).closest('.synced_div');
    var synced_contactid = $(block).find('[name="synced_contact"] option:selected').val();
    if(synced_contactid > 0) {
        overlayIFrameSlider('<?= WEBSITE_URL ?>/<?= ucfirst(FOLDER_NAME) ?>/contacts_inbox.php?edit='+synced_contactid, 'auto', false, true);
    } else {
        alert('No Contact Selected');
    }
}
</script>

<h4>Synced Contacts</h4>
<?php 
$contact_categories = array_filter(explode(',', get_config($dbc, FOLDER_NAME.'_tabs')));
$contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND `tile_name` = '".FOLDER_NAME."' AND `category` != 'Staff'"));

$contactid = $_GET['edit'];
$synced_contacts = mysqli_query($dbc, "SELECT `synced_contactid`,`category` FROM (SELECT `contacts_sync`.`synced_contactid`, `contacts`.`category` FROM `contacts_sync` LEFT JOIN `contacts` ON `contacts_sync`.`synced_contactid` = `contacts`.`contactid` WHERE `contacts_sync`.`contactid` = '$contactid' AND `contacts_sync`.`deleted` = 0 AND `contacts`.`deleted` = 0 UNION SELECT `contacts_sync`.`contactid` `synced_contactid`, `contacts`.`category` FROM `contacts_sync` LEFT JOIN `contacts` ON `contacts_sync`.`contactid` = `contacts`.`contactid` WHERE `contacts_sync`.`synced_contactid` = '$contactid' AND `contacts_sync`.`deleted` = 0 AND `contacts`.`deleted` = 0) `contacts_sync_tables` ORDER BY `category`");
$synced_contact = mysqli_fetch_assoc($synced_contacts);

do {
    $synced_contactid = $synced_contact['synced_contactid'];
    $synced_category = $synced_contact['category']; ?>
    <div class="form-horizontal synced_div" data-id="<?= $synced_contact['contactsyncid'] ?>">
        <div class="form-group">
            <label class="col-sm-4 control-label">Category:</label>
            <div class="col-sm-8">
                <select name="synced_category" class="chosen-select-deselect">
                    <option></option>
                    <?php foreach($contact_categories as $contact_category) {
                        echo '<option value="'.$contact_category.'" '.($synced_category == $contact_category ? 'selected' : '').'>'.$contact_category.'</option>';
                    } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Contact:</label>
            <div class="col-sm-8">
                <select name="synced_contact" class="chosen-select-deselect">
                    <option></option>
                    <option value="ADD_NEW">Add New Contact</option>
                    <?php foreach($contact_list as $contact) {
                        echo '<option data-category="'.$contact['category'].'" value="'.$contact['contactid'].'" '.($synced_contactid == $contact['contactid'] ? 'selected' : '').' '.($contact['category'] != $synced_category && !empty($synced_category) ? 'style="display:none;"' : '').'>'.$contact['full_name'].'</option>';
                    } ?>
                </select>
            </div>
        </div>
        <div class="form-group pull-right">
            <a href="" onclick="addSyncedContact(); return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img no-toggle" title="Add Contact"></a>
            <a href="" onclick="removeSyncedContact(this); return false;"><img src="../img/remove.png" class="inline-img no-toggle" title="Remove Contact"></a>
            <a href="" onclick="viewSyncedContact(this); return false;"><img src="../img/icons/eyeball.png" class="inline-img no-toggle" title="View Contact"></a>
        </div>
        <div class="clearfix"></div>
    </div>
<?php } while($synced_contact = mysqli_fetch_assoc($synced_contacts)); 