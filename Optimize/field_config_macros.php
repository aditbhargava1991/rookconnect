<?php $file_list = array_filter(scandir('macros'), function($filename) { return strpos($filename,'.php') !== FALSE; });
if(count($file_list) == 0) {
	echo '<h3>No Macros Found.</h3>';
} else {
	$ticket_tabs = explode(',',get_config($dbc,'ticket_tabs')); ?>
	<script>
	$(document).ready(function() {
		setSave();
	});
	function setSave() {
		$('.form-group input,.form-group select').off('change',saveMacros).change(saveMacros);
	}
	function saveMacros() {
		var list = [];
        var bus_list = [];
		$('.form-group').each(function() {
			list.push($(this).find('input').val()+'|'+$(this).find('[name=file]').val()+'|'+$(this).find('[name=type]').val());
            cur_bus = [];
            $(this).find('[name=business]').each(function() {
                cur_bus.push(this.value);
            });
            bus_list.push(cur_bus.join('|'));
		});
		$.post('optimize_ajax.php?action=add_macro', { value: list, businesses: bus_list });
	}
	function addRow() {
		var block = $('.form-group').last();
		destroyInputs();
		var clone = block.clone();
		clone.find('input,select').val('');
		block.after(clone);
		initInputs();
		setSave();
	}
	function remRow(img) {
		if($('.form-group').length == 1) {
			addRow();
		}
		$(img).closest('.form-group').remove();
		saveMacros();
	}
	function addBus(img) {
		var block = $(img).closest('.form-group').find('.bus_option').last();
		destroyInputs();
		var clone = block.clone();
		clone.find('input,select').val('');
		block.after(clone);
		initInputs();
		setSave();
	}
	function remBus(img) {
		if($(img).closest('.form-group').find('.bus_option').length == 1) {
			addBus(img);
		}
		$(img).closest('.bus_option').remove();
		saveMacros();
	}
	</script>
	<?php $business_list = sort_contacts_query($dbc->query("SELECT `name`,`first_name`,`last_name`,`contactid` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `deleted`=0 AND `status` > 0"));
    $class_list = array_filter(explode(',',get_config($dbc, '%_classification', true, ',')));
    $macro_list[] = '';
    $bus_list[] = [''];
	foreach($macro_list as $label => $file) { ?>
		<div class="form-group">
			<div>
				<label class="col-sm-4">Macro Name:</label>
                <div class="col-sm-7"><input type="text" class="form-control" name="label" value="<?= $file != '' ? $label : '' ?>"></div>
                <div class="col-sm-1">
                    <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addRow()">
                    <img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="remRow(this)">
                </div>
			</div>
			<div>
				<label class="col-sm-4">File Name:</label>
                <div class="col-sm-8">
                    <select class="chosen-select-deselect" name="file" data-placeholder="Select File..."><option />
                        <?php foreach($file_list as $file_name) { ?>
                            <option <?= config_safe_str($file_name) == $file[0] ? 'selected' : '' ?> value="<?= $file_name ?>"><?= $file_name ?></option>
                        <?php } ?>
                    </select>
                </div>
			</div>
            <div class="clearfix"></div>
            <div>
                <label class="col-sm-4"><?= TICKET_NOUN ?> Type:</label>
                <div class="col-sm-8">
                    <select class="chosen-select-deselect" name="type" data-placeholder="Select Type..."><option />
                        <?php foreach($ticket_tabs as $ticket_type) { ?>
                            <option <?= config_safe_str($ticket_type) == $file[1] ? 'selected' : '' ?> value="<?= config_safe_str($ticket_type) ?>"><?= $ticket_type ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php foreach($bus_list[$label] as $bus_opt) { ?>
                <div class="bus_option">
                    <label class="col-sm-4"><?= BUSINESS_CAT ?> to Include:</label>
                    <div class="col-sm-7">
                        <select class="chosen-select-deselect" name="business" data-placeholder="Select <?= BUSINESS_CAT ?>..."><option />
                            <option <?= empty($bus_opt) || $bus_opt == 'ALL' ? 'selected' : '' ?> value="ALL">All <?= BUSINESS_CAT ?></option>
                            <?php foreach($class_list as $bus_class) { ?>
                                <option <?= config_safe_str($bus_class) == $bus_opt ? 'selected' : '' ?> value="<?= config_safe_str($bus_class) ?>">Classification: <?= $bus_class ?></option>
                            <?php } ?>
                            <?php foreach($business_list as $bus_name) { ?>
                                <option <?= $bus_name['contactid'] == $bus_opt ? 'selected' : '' ?> value="<?= $bus_name['contactid'] ?>"><?= $bus_name['full_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addBus(this)">
                        <img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="remBus(this)">
                    </div>
                </div>
            <?php } ?>
            <div class="clearfix"></div>
            <hr>
		</div>
	<?php }
} ?>