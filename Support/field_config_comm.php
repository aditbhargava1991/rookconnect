<?php include_once('config.php'); ?>
<script>
$(document).ready(function() {
	$('input').change(saveField);
});
function saveFieldMethod() {
	var all_recip = [];
	$('[name=support_recipients_all]').each(function() {
		if(this.value != '') {
			all_recip.push(this.value);
		}
	});
	var default_recip = [];
	$('[name=support_recipients_default]').each(function() {
		if(this.value != '') {
			default_recip.push(this.value);
		}
	});
	var support_types = [];
	var recip_list = [];
	var alerts = [];
	var notes = [];
	$('[name=support_type]').each(function() {
		support_types.push(this.value);
		var recips = [];
		$('[name=support_recipients_'+this.value+']').each(function() {
			if(this.value != '') {
				recips.push(this.value);
			}
		});
		recip_list.push(recips);
		notes.push($('[name=support_note_'+this.value+']').val());
		alerts.push($('[name=support_alert_'+this.value+']').val());
	});
	$.post('support_ajax.php?action=comm_settings', {
		all_recips: all_recip,
		default_recips: default_recip,
		types: support_types,
		recipients: recip_list,
		alerts: alerts,
		notes: notes
	}, function(response) {
        doneSaving();
    });
}
function addRecip(img) {
	var block = $(img).closest('.form-group');
	var clone = block.clone();
	clone.find('input').val('');
	block.after(clone);
	$('input').off('change',saveFields).change(saveFields);
	clone.find('input').first().focus();
}
function remRecip(img) {
	var block = $(img).closest('.form-group');
	var field = block.find('input').attr('name');
	if($('[name='+field+']').length <= 1) {
		addRecip(img);
	}
	block.remove();
	saveFields();
}
</script>
<?php array_unshift($ticket_types,'Feedback');
$all_emails = get_config($dbc, 'support_recipients_all');
$default_emails = get_config($dbc, 'support_recipients_default');
foreach(explode(';',$all_emails) as $email) { ?>
    <div class="form-group">
        <label class="col-sm-4">Recipients for All:<br /><em>Addresses that will receive notification of all requests.</em></label>
        <div class="col-sm-7">
            <input type="email" name="support_recipients_all" value="<?= $email ?>" class="form-control">
        </div>
        <div class="col-sm-1">
            <img class="inline-img cursor-hand pull-right" onclick="addRecip(this);" src="../img/icons/ROOK-add-icon.png">
            <img class="inline-img cursor-hand pull-right" onclick="remRecip(this);" src="../img/remove.png">
        </div>
    </div>
<?php } ?>
<hr>
<?php foreach(explode(';',$default_emails) as $email) { ?>
    <div class="form-group">
        <label class="col-sm-4">Default Recipients:<br /><em>Addresses that will receive notification of requests with no specified address.</em></label>
        <div class="col-sm-7">
            <input type="email" name="support_recipients_default" value="<?= $email ?>" class="form-control">
        </div>
        <div class="col-sm-1">
            <img class="inline-img cursor-hand pull-right" onclick="addRecip(this);" src="../img/icons/ROOK-add-icon.png">
            <img class="inline-img cursor-hand pull-right" onclick="remRecip(this);" src="../img/remove.png">
        </div>
    </div>
<?php }
foreach($ticket_types as $type) {
    $type_id = config_safe_str($type);
    $recipients = get_config($dbc, 'support_recipients_'.$type_id);
    $alert = get_config($dbc, 'support_alert_'.$type_id);
    $note = get_config($dbc, 'support_note_'.$type_id); ?>
    <hr>
    <h3><?= $type ?></h3>
    <input type="hidden" name="support_type" value="<?= $type_id ?>">
    <?php foreach(explode(';',$recipients) as $email) { ?>
        <div class="form-group">
            <label class="col-sm-4">Recipients:<br /><em>Addresses that will receive only this type of request.</em></label>
            <div class="col-sm-7">
                <input type="email" name="support_recipients_<?= $type_id ?>" value="<?= $email ?>" class="form-control">
            </div>
            <div class="col-sm-1">
                <img class="inline-img cursor-hand pull-right" onclick="addRecip(this);" src="../img/icons/ROOK-add-icon.png">
                <img class="inline-img cursor-hand pull-right" onclick="remRecip(this);" src="../img/remove.png">
            </div>
        </div>
    <?php } ?>
    <div class="form-group">
        <label class="col-sm-4">Support Note:<br /><em>Displayed at the top of the New Request page.</em></label>
        <div class="col-sm-8">
            <input type="text" name="support_note_<?= $type_id ?>" value="<?= $note ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4">Support Alert:<br /><em>Displayed before the support request can be created.</em></label>
        <div class="col-sm-8">
            <input type="text" name="support_alert_<?= $type_id ?>" value="<?= $alert ?>" class="form-control">
        </div>
    </div>
<?php }