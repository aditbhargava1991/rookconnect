<?php include_once('config.php'); ?>
<script>
$(document).ready(function() {
	$('[name=tab_list]').change(saveField);
	$('.form-group').sortable({
		items: 'label',
		update: function() {
            current_fields.push($('[name=tab_list]').get(0));
            saveField()
        }
	});
});
function saveFieldMethod() {
	var tab_list = [];
	$('[name=tab_list]:checked').each(function() {
		if(this.value != '') {
			tab_list.push(this.value);
		}
	});
	$.post('support_ajax.php?action=tab_settings', {
		tab_list: tab_list
	}, function(response) {
        doneSaving();
    });
}
</script>
<?php $tab_list = explode(',',get_config($dbc, 'cust_support_tab_list')); ?>
<div class="form-group">
    <label class="col-sm-4">Tabs to Display:</label>
    <div class="col-sm-8">
        <?php $tab_options = array_merge($tab_list,['services','scrum','new','feedback','closed','documents','meetings','customer']);
        foreach($ticket_types as $type_name) {
            $tab_options[] = config_safe_str($type_name);
        }
        foreach(array_unique($tab_options) as $tab_id) {
            switch($tab_id) {
                case 'services': ?>
                    <label class="form-checkbox"><input type="checkbox" name="tab_list" <?= in_array('services',$tab_list) ? 'checked' : '' ?> value="services" class="form-control"> Services</label>
                    <?php break;
                case 'scrum': ?>
                    <label class="form-checkbox"><input type="checkbox" name="tab_list" <?= in_array('scrum',$tab_list) ? 'checked' : '' ?> value="scrum" class="form-control"> Scrum Board</label>
                    <?php break;
                case 'new': ?>
                    <label class="form-checkbox"><input type="checkbox" name="tab_list" checked disabled value="new" class="form-control"> New Request</label>
                    <?php break;
                case 'feedback': ?>
                    <label class="form-checkbox"><input type="checkbox" name="tab_list" <?= in_array('feedback',$tab_list) ? 'checked' : '' ?> value="feedback" class="form-control"> Feedback &amp; Ideas</label>
                    <?php break;
                case 'closed': ?>
                    <label class="form-checkbox"><input type="checkbox" name="tab_list" <?= in_array('closed',$tab_list) ? 'checked' : '' ?> value="closed" class="form-control"> Closed Requests</label>
                    <?php break;
                case 'documents': ?>
                    <label class="form-checkbox"><input type="checkbox" name="tab_list" <?= in_array('documents',$tab_list) ? 'checked' : '' ?> disabled value="documents" class="form-control"> Customer Documents</label>
                    <?php break;
                case 'meetings': ?>
                    <label class="form-checkbox"><input type="checkbox" name="tab_list" <?= in_array('meetings',$tab_list) ? 'checked' : '' ?> disabled value="meetings" class="form-control"> Agendas & Meetings</label>
                    <?php break;
                case 'customer': ?>
                    <label class="form-checkbox"><input type="checkbox" name="tab_list" <?= in_array('customer',$tab_list) ? 'checked' : '' ?> disabled value="customer" class="form-control"> Information Requests</label>
                    <?php break;
                default:
                    foreach($ticket_types as $type) {
                        if(config_safe_str($type) == $tab_id) { ?>
                            <label class="form-checkbox"><input type="checkbox" name="tab_list" <?= in_array(config_safe_str($type),$tab_list) ? 'checked' : '' ?> value="<?= config_safe_str($type) ?>" class="form-control"> <?= $type ?></label>
                        <?php }
                    }
                    break;
            }
        } ?>
    </div>
</div>