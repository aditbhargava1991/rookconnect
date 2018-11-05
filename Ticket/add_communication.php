<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>'.$communication_type.' Communication</h3>') : '' ?>

<?php if($communication_type == 'External') { ?>
    <h4>Tagged for Communication</h4>
    <?php $tags = explode(',',$get_ticket['communication_tags']);
    $contact_list = sort_contacts_query($dbc->query("SELECT `contactid`,`category`,`name`,`last_name`,`first_name`,`email_address` FROM `contacts` WHERE `contactid` IN ('".implode("','",$tags)."') OR (`status` > 0 AND `deleted`=0 AND (`contactid` IN ('".implode("','",array_filter(explode(',',$get_ticket['businessid'].','.$get_ticket['clientid'])))."') OR `category`='Staff' OR `businessid`='".$get_ticket['businessid']."'))"));
    foreach($tags as $tag_contact) { ?>
        <div class="form-group multi-block" data-type="communication_tags">
            <label class="col-sm-4"><?= CONTACTS_NOUN ?>:</label>
            <div class="col-sm-7">
                <select name="communication_tags" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," class="chosen-select-deselect" data-placeholder="Select <?= CONTACTS_NOUN ?>"><option />
                    <?php foreach($contact_list as $contact) { ?>
                        <option <?= $contact['contactid'] == $tag_contact ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['full_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-1">
                <img class="pull-right inline-img cursor-hand no-toggle" src="../img/remove.png" data-history-label="Communication Tag" title="Remove <?= CONTACTS_NOUN ?>" data-remove="1" data-history-label="Communication" onclick="remMulti(this);">
                <img class="pull-right inline-img cursor-hand no-toggle" src="../img/icons/ROOK-add-icon.png" data-history-label="Communication Tag" title="Add <?= CONTACTS_NOUN ?>" data-history-label="Communication" onclick="addMulti(this);">
            </div>
        </div>
    <?php }
} ?>

<a class="no-toggle" href="" title="Add <?= $communication_type ?> Communication" onclick="addCommunication('<?= $communication_type ?>','<?= $communication_method ?>'); return false;"><img class="inline-img" data-history-label="Communication" src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" /></a>
<div class="clearfix"></div>
<div class="col-sm-12">
	<div class="ticket_communication" id="no-more-tables" data-type="<?= $communication_type ?>" data-method="<?= $communication_method ?>"><?php include('add_ticket_view_communication.php'); ?></div>
</div>