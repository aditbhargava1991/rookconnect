<?php error_reporting(0);
include_once('../include.php'); ?>
<script>
$(document).ready(function() {
	$('[name$=classification],[name$=class_regions],[name=timezone]').change(save_fields);
});
function save_fields() {
	var options = [];
	$('[name=<?= FOLDER_NAME ?>_classification]').each(function() {
		this.value = this.value.replace(',','');
		options.push(this.value);
	});
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=general_config',
		method: 'POST',
		data: {
			name: '<?= FOLDER_NAME ?>_classification',
			value: options.join(',')
		}
	});
	var options = [];
	$('[name=<?= FOLDER_NAME ?>_class_regions]').each(function() {
		this.value = this.value.replace(',','');
		options.push(this.value);
	});
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=general_config',
		method: 'POST',
		data: {
			name: '<?= FOLDER_NAME ?>_class_regions',
			value: options.join(',')
		}
	});
	var options = [];
	$('[name=timezone]').each(function() {
		this.value = this.value.replace(',','');
		options.push(this.value);
	});
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=general_config',
		method: 'POST',
		data: {
			name: 'classification_time_offset',
			value: options.join(',')
		}
	});
	upload_logo();
}
function add_option() {
	destroyInputs($('.classification-group'));
	var row = $('.classification-group').last();
	var clone = row.clone();
	clone.find('input').val('');
	clone.find('select').val('');
	row.after(clone);
	initInputs('.classification-group');
	$('[name$=classification],[name$=class_regions]').off('change', save_fields).change(save_fields);
}
function remove_option(target) {
	if($('.classification-group').length <= 0) {
		add_option();
	}
	$(target).closest('.form-group').remove();
	save_fields();
}
function upload_logo(file) {
	var options = new FormData();
	var counter = 0;
	$('[name="class_logos"]').each(function() {
		if($(this).val() != '' && $(this).val() != undefined) {
			options.append(counter, $(this).val());
		} else if($(this).closest('.logo_block').find('[name="class_logos_upload"]')[0].files[0] != undefined) {
			options.append(counter, $(this).closest('.logo_block').find('[name="class_logos_upload"]')[0].files[0]);
		} else {
			options.append(counter, '');
		}
		counter++;
	});
	options.append('counter', counter);
	options.append('name', '<?= FOLDER_NAME ?>_class_logos');
	options.append('folder_name', '<?= FOLDER_URL ?>');
	$.ajax({
		processData: false,
		contentType: false,
		url: '../Contacts/contacts_ajax.php?action=classification_logos',
		method: 'POST',
		data: options,
		success: function(response) {
			$('[name="class_logos_upload"]').val('');
			if(response != '' && response != undefined) {
				$(file).closest('.logo_block').find('[name="class_logos"]').val(response);
				$(file).closest('.logo_block').find('a.logo_url').attr('href', response);
				$(file).closest('.logo_block').find('.logo_exists').show();
				$(file).closest('.logo_block').find('.logo_upload').hide();
			}
		}
	})
}
function delete_logo(link) {
	var logo_block = $(link).closest('.logo_block');
	$(logo_block).find('[name="class_logos"]').val('');
	$(logo_block).find('.logo_exists').hide();
	$(logo_block).find('.logo_upload').show();
	upload_logo();
}
</script>
<div class="standard-dashboard-body-title">
    <h3>Settings - Classifications:</h3>
</div>
<div class="standard-dashboard-body-content full-height">
    <div class="dashboard-item dashboard-item2 full-height">
        <div class="form-horizontal block-group block-group-noborder">
            <div class="hide-titles-mobile">
                <div class="pull-right" style="width:5em;"><img src="../img/empty.png"></div>
                <div class="scale-to-fill">
                    <label class="col-sm-3">Classification<br /><em>Cannot contain commas</em></label>
                    <label class="col-sm-3">Region<br /><em>Optional: Set a classification to be only for one region</em></label>
                    <label class="col-sm-3">Logo</label>
                    <label class="col-sm-3">Time Zone</label>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php $regions = explode(',',get_config($dbc, '%_region', true));
            $classifications = explode(',',get_config($dbc, FOLDER_NAME.'_classification'));
            $class_regions = explode(',',get_config($dbc, FOLDER_NAME.'_class_regions'));
            $class_logos = explode('*#*',get_config($dbc, FOLDER_NAME.'_class_logos'));
            $class_times = explode(',',get_config($dbc, 'classification_time_offset'));
            foreach($classifications as $i => $classification) {
                $class_time = $class_times[$i]; ?>
                <div class="form-group classification-group">
                    <div class="pull-right" style="width: 5em;">
                        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option();">
                        <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this);">
                    </div>
                    <div class="scale-to-fill">
                        <div class="col-sm-3">
                            <label class="show-on-mob">Classification:<br /><em>Classification names cannot contain commas.</em></label>
                            <input name="<?= FOLDER_NAME ?>_classification" type="text" value="<?= $classification ?>" class="form-control"/>
                        </div>
                        <div class="col-sm-3">
                            <label class="show-on-mob">Region:<br /><em>Optional: You can set a classification to be only for one region.</em></label>
                            <select name="<?= FOLDER_NAME ?>_class_regions" data-placeholder="Select a Region (Optional)" value="<?= $class_regions[$i] ?>" class="chosen-select-deselect">
                                <option <?= $class_regions[$i] == 'ALL' || $class_regions[$i] == '' ? 'selected' : '' ?> value="ALL">All Regions</option>
                                <?php foreach($regions as $region) { ?>
                                    <option <?= $class_regions[$i] == $region ? 'selected' : '' ?> value="<?= $region ?>"><?= $region ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-3 logo_block">
                            <input type="hidden" name="class_logos" value="<?= $class_logos[$i] ?>">
                            <label class="show-on-mob">Logo:</label>
                            <div class="logo_exists" <?= empty($class_logos[$i]) ? 'style="display:none;"' : '' ?>>
                                <a href="<?= $class_logos[$i] ?>" target="_blank" class="logo_url">View</a> | <a href="" onclick="delete_logo(this); return false;">Delete</a>
                            </div>
                            <div class="logo_upload" <?= !empty($class_logos[$i]) ? 'style="display:none;"' : '' ?>>
                                <input type="file" name="class_logos_upload" class="form-control" onchange="upload_logo(this); save_fields();">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <select name="timezone" data-placeholder="Select Time Zone" class="chosen-select-deselect"><option />
                                <option <?= $class_time == 'Africa/Abidjan' ? 'selected' : '' ?> value="Africa/Abidjan">Africa/Abidjan</option>
                                <option <?= $class_time == 'Africa/Accra' ? 'selected' : '' ?> value="Africa/Accra">Africa/Accra</option>
                                <option <?= $class_time == 'Africa/Addis_Ababa' ? 'selected' : '' ?> value="Africa/Addis_Ababa">Africa/Addis_Ababa</option>
                                <option <?= $class_time == 'Africa/Algiers' ? 'selected' : '' ?> value="Africa/Algiers">Africa/Algiers</option>
                                <option <?= $class_time == 'Africa/Asmara' ? 'selected' : '' ?> value="Africa/Asmara">Africa/Asmara</option>
                                <option <?= $class_time == 'Africa/Bamako' ? 'selected' : '' ?> value="Africa/Bamako">Africa/Bamako</option>
                                <option <?= $class_time == 'Africa/Bangui' ? 'selected' : '' ?> value="Africa/Bangui">Africa/Bangui</option>
                                <option <?= $class_time == 'Africa/Banjul' ? 'selected' : '' ?> value="Africa/Banjul">Africa/Banjul</option>
                                <option <?= $class_time == 'Africa/Bissau' ? 'selected' : '' ?> value="Africa/Bissau">Africa/Bissau</option>
                                <option <?= $class_time == 'Africa/Blantyre' ? 'selected' : '' ?> value="Africa/Blantyre">Africa/Blantyre</option>
                                <option <?= $class_time == 'Africa/Brazzaville' ? 'selected' : '' ?> value="Africa/Brazzaville">Africa/Brazzaville</option>
                                <option <?= $class_time == 'Africa/Bujumbura' ? 'selected' : '' ?> value="Africa/Bujumbura">Africa/Bujumbura</option>
                                <option <?= $class_time == 'Africa/Cairo' ? 'selected' : '' ?> value="Africa/Cairo">Africa/Cairo</option>
                                <option <?= $class_time == 'Africa/Casablanca' ? 'selected' : '' ?> value="Africa/Casablanca">Africa/Casablanca</option>
                                <option <?= $class_time == 'Africa/Ceuta' ? 'selected' : '' ?> value="Africa/Ceuta">Africa/Ceuta</option>
                                <option <?= $class_time == 'Africa/Conakry' ? 'selected' : '' ?> value="Africa/Conakry">Africa/Conakry</option>
                                <option <?= $class_time == 'Africa/Dakar' ? 'selected' : '' ?> value="Africa/Dakar">Africa/Dakar</option>
                                <option <?= $class_time == 'Africa/Dar_es_Salaam' ? 'selected' : '' ?> value="Africa/Dar_es_Salaam">Africa/Dar_es_Salaam</option>
                                <option <?= $class_time == 'Africa/Djibouti' ? 'selected' : '' ?> value="Africa/Djibouti">Africa/Djibouti</option>
                                <option <?= $class_time == 'Africa/Douala' ? 'selected' : '' ?> value="Africa/Douala">Africa/Douala</option>
                                <option <?= $class_time == 'Africa/El_Aaiun' ? 'selected' : '' ?> value="Africa/El_Aaiun">Africa/El_Aaiun</option>
                                <option <?= $class_time == 'Africa/Freetown' ? 'selected' : '' ?> value="Africa/Freetown">Africa/Freetown</option>
                                <option <?= $class_time == 'Africa/Gaborone' ? 'selected' : '' ?> value="Africa/Gaborone">Africa/Gaborone</option>
                                <option <?= $class_time == 'Africa/Harare' ? 'selected' : '' ?> value="Africa/Harare">Africa/Harare</option>
                                <option <?= $class_time == 'Africa/Johannesburg' ? 'selected' : '' ?> value="Africa/Johannesburg">Africa/Johannesburg</option>
                                <option <?= $class_time == 'Africa/Juba' ? 'selected' : '' ?> value="Africa/Juba">Africa/Juba</option>
                                <option <?= $class_time == 'Africa/Kampala' ? 'selected' : '' ?> value="Africa/Kampala">Africa/Kampala</option>
                                <option <?= $class_time == 'Africa/Khartoum' ? 'selected' : '' ?> value="Africa/Khartoum">Africa/Khartoum</option>
                                <option <?= $class_time == 'Africa/Kigali' ? 'selected' : '' ?> value="Africa/Kigali">Africa/Kigali</option>
                                <option <?= $class_time == 'Africa/Kinshasa' ? 'selected' : '' ?> value="Africa/Kinshasa">Africa/Kinshasa</option>
                                <option <?= $class_time == 'Africa/Lagos' ? 'selected' : '' ?> value="Africa/Lagos">Africa/Lagos</option>
                                <option <?= $class_time == 'Africa/Libreville' ? 'selected' : '' ?> value="Africa/Libreville">Africa/Libreville</option>
                                <option <?= $class_time == 'Africa/Lome' ? 'selected' : '' ?> value="Africa/Lome">Africa/Lome</option>
                                <option <?= $class_time == 'Africa/Luanda' ? 'selected' : '' ?> value="Africa/Luanda">Africa/Luanda</option>
                                <option <?= $class_time == 'Africa/Lubumbashi' ? 'selected' : '' ?> value="Africa/Lubumbashi">Africa/Lubumbashi</option>
                                <option <?= $class_time == 'Africa/Lusaka' ? 'selected' : '' ?> value="Africa/Lusaka">Africa/Lusaka</option>
                                <option <?= $class_time == 'Africa/Malabo' ? 'selected' : '' ?> value="Africa/Malabo">Africa/Malabo</option>
                                <option <?= $class_time == 'Africa/Maputo' ? 'selected' : '' ?> value="Africa/Maputo">Africa/Maputo</option>
                                <option <?= $class_time == 'Africa/Maseru' ? 'selected' : '' ?> value="Africa/Maseru">Africa/Maseru</option>
                                <option <?= $class_time == 'Africa/Mbabane' ? 'selected' : '' ?> value="Africa/Mbabane">Africa/Mbabane</option>
                                <option <?= $class_time == 'Africa/Mogadishu' ? 'selected' : '' ?> value="Africa/Mogadishu">Africa/Mogadishu</option>
                                <option <?= $class_time == 'Africa/Monrovia' ? 'selected' : '' ?> value="Africa/Monrovia">Africa/Monrovia</option>
                                <option <?= $class_time == 'Africa/Nairobi' ? 'selected' : '' ?> value="Africa/Nairobi">Africa/Nairobi</option>
                                <option <?= $class_time == 'Africa/Ndjamena' ? 'selected' : '' ?> value="Africa/Ndjamena">Africa/Ndjamena</option>
                                <option <?= $class_time == 'Africa/Niamey' ? 'selected' : '' ?> value="Africa/Niamey">Africa/Niamey</option>
                                <option <?= $class_time == 'Africa/Nouakchott' ? 'selected' : '' ?> value="Africa/Nouakchott">Africa/Nouakchott</option>
                                <option <?= $class_time == 'Africa/Ouagadougou' ? 'selected' : '' ?> value="Africa/Ouagadougou">Africa/Ouagadougou</option>
                                <option <?= $class_time == 'Africa/Porto-Novo' ? 'selected' : '' ?> value="Africa/Porto-Novo">Africa/Porto-Novo</option>
                                <option <?= $class_time == 'Africa/Sao_Tome' ? 'selected' : '' ?> value="Africa/Sao_Tome">Africa/Sao_Tome</option>
                                <option <?= $class_time == 'Africa/Timbuktu' ? 'selected' : '' ?> value="Africa/Timbuktu">Africa/Timbuktu</option>
                                <option <?= $class_time == 'Africa/Tripoli' ? 'selected' : '' ?> value="Africa/Tripoli">Africa/Tripoli</option>
                                <option <?= $class_time == 'Africa/Tunis' ? 'selected' : '' ?> value="Africa/Tunis">Africa/Tunis</option>
                                <option <?= $class_time == 'Africa/Windhoek' ? 'selected' : '' ?> value="Africa/Windhoek">Africa/Windhoek</option>
                                <option <?= $class_time == 'America/Adak' ? 'selected' : '' ?> value="America/Adak">America/Adak</option>
                                <option <?= $class_time == 'America/Anchorage' ? 'selected' : '' ?> value="America/Anchorage">America/Anchorage</option>
                                <option <?= $class_time == 'America/Anguilla' ? 'selected' : '' ?> value="America/Anguilla">America/Anguilla</option>
                                <option <?= $class_time == 'America/Antigua' ? 'selected' : '' ?> value="America/Antigua">America/Antigua</option>
                                <option <?= $class_time == 'America/Araguaina' ? 'selected' : '' ?> value="America/Araguaina">America/Araguaina</option>
                                <option <?= $class_time == 'America/Argentina/Buenos_Aires' ? 'selected' : '' ?> value="America/Argentina/Buenos_Aires">America/Argentina/Buenos_Aires</option>
                                <option <?= $class_time == 'America/Argentina/Catamarca' ? 'selected' : '' ?> value="America/Argentina/Catamarca">America/Argentina/Catamarca</option>
                                <option <?= $class_time == 'America/Argentina/ComodRivadavia' ? 'selected' : '' ?> value="America/Argentina/ComodRivadavia">America/Argentina/ComodRivadavia</option>
                                <option <?= $class_time == 'America/Argentina/Cordoba' ? 'selected' : '' ?> value="America/Argentina/Cordoba">America/Argentina/Cordoba</option>
                                <option <?= $class_time == 'America/Argentina/Jujuy' ? 'selected' : '' ?> value="America/Argentina/Jujuy">America/Argentina/Jujuy</option>
                                <option <?= $class_time == 'America/Argentina/La_Rioja' ? 'selected' : '' ?> value="America/Argentina/La_Rioja">America/Argentina/La_Rioja</option>
                                <option <?= $class_time == 'America/Argentina/Mendoza' ? 'selected' : '' ?> value="America/Argentina/Mendoza">America/Argentina/Mendoza</option>
                                <option <?= $class_time == 'America/Argentina/Rio_Gallegos' ? 'selected' : '' ?> value="America/Argentina/Rio_Gallegos">America/Argentina/Rio_Gallegos</option>
                                <option <?= $class_time == 'America/Argentina/Salta' ? 'selected' : '' ?> value="America/Argentina/Salta">America/Argentina/Salta</option>
                                <option <?= $class_time == 'America/Argentina/San_Juan' ? 'selected' : '' ?> value="America/Argentina/San_Juan">America/Argentina/San_Juan</option>
                                <option <?= $class_time == 'America/Argentina/San_Luis' ? 'selected' : '' ?> value="America/Argentina/San_Luis">America/Argentina/San_Luis</option>
                                <option <?= $class_time == 'America/Argentina/Tucuman' ? 'selected' : '' ?> value="America/Argentina/Tucuman">America/Argentina/Tucuman</option>
                                <option <?= $class_time == 'America/Argentina/Ushuaia' ? 'selected' : '' ?> value="America/Argentina/Ushuaia">America/Argentina/Ushuaia</option>
                                <option <?= $class_time == 'America/Aruba' ? 'selected' : '' ?> value="America/Aruba">America/Aruba</option>
                                <option <?= $class_time == 'America/Asuncion' ? 'selected' : '' ?> value="America/Asuncion">America/Asuncion</option>
                                <option <?= $class_time == 'America/Atikokan' ? 'selected' : '' ?> value="America/Atikokan">America/Atikokan</option>
                                <option <?= $class_time == 'America/Atka' ? 'selected' : '' ?> value="America/Atka">America/Atka</option>
                                <option <?= $class_time == 'America/Bahia' ? 'selected' : '' ?> value="America/Bahia">America/Bahia</option>
                                <option <?= $class_time == 'America/Bahia_Banderas' ? 'selected' : '' ?> value="America/Bahia_Banderas">America/Bahia_Banderas</option>
                                <option <?= $class_time == 'America/Barbados' ? 'selected' : '' ?> value="America/Barbados">America/Barbados</option>
                                <option <?= $class_time == 'America/Belem' ? 'selected' : '' ?> value="America/Belem">America/Belem</option>
                                <option <?= $class_time == 'America/Belize' ? 'selected' : '' ?> value="America/Belize">America/Belize</option>
                                <option <?= $class_time == 'America/Blanc-Sablon' ? 'selected' : '' ?> value="America/Blanc-Sablon">America/Blanc-Sablon</option>
                                <option <?= $class_time == 'America/Boa_Vista' ? 'selected' : '' ?> value="America/Boa_Vista">America/Boa_Vista</option>
                                <option <?= $class_time == 'America/Bogota' ? 'selected' : '' ?> value="America/Bogota">America/Bogota</option>
                                <option <?= $class_time == 'America/Boise' ? 'selected' : '' ?> value="America/Boise">America/Boise</option>
                                <option <?= $class_time == 'America/Buenos_Aires' ? 'selected' : '' ?> value="America/Buenos_Aires">America/Buenos_Aires</option>
                                <option <?= $class_time == 'America/Cambridge_Bay' ? 'selected' : '' ?> value="America/Cambridge_Bay">America/Cambridge_Bay</option>
                                <option <?= $class_time == 'America/Campo_Grande' ? 'selected' : '' ?> value="America/Campo_Grande">America/Campo_Grande</option>
                                <option <?= $class_time == 'America/Cancun' ? 'selected' : '' ?> value="America/Cancun">America/Cancun</option>
                                <option <?= $class_time == 'America/Caracas' ? 'selected' : '' ?> value="America/Caracas">America/Caracas</option>
                                <option <?= $class_time == 'America/Catamarca' ? 'selected' : '' ?> value="America/Catamarca">America/Catamarca</option>
                                <option <?= $class_time == 'America/Cayenne' ? 'selected' : '' ?> value="America/Cayenne">America/Cayenne</option>
                                <option <?= $class_time == 'America/Cayman' ? 'selected' : '' ?> value="America/Cayman">America/Cayman</option>
                                <option <?= $class_time == 'America/Chicago' ? 'selected' : '' ?> value="America/Chicago">America/Chicago</option>
                                <option <?= $class_time == 'America/Chihuahua' ? 'selected' : '' ?> value="America/Chihuahua">America/Chihuahua</option>
                                <option <?= $class_time == 'America/Coral_Harbour' ? 'selected' : '' ?> value="America/Coral_Harbour">America/Coral_Harbour</option>
                                <option <?= $class_time == 'America/Cordoba' ? 'selected' : '' ?> value="America/Cordoba">America/Cordoba</option>
                                <option <?= $class_time == 'America/Costa_Rica' ? 'selected' : '' ?> value="America/Costa_Rica">America/Costa_Rica</option>
                                <option <?= $class_time == 'America/Creston' ? 'selected' : '' ?> value="America/Creston">America/Creston</option>
                                <option <?= $class_time == 'America/Cuiaba' ? 'selected' : '' ?> value="America/Cuiaba">America/Cuiaba</option>
                                <option <?= $class_time == 'America/Curacao' ? 'selected' : '' ?> value="America/Curacao">America/Curacao</option>
                                <option <?= $class_time == 'America/Danmarkshavn' ? 'selected' : '' ?> value="America/Danmarkshavn">America/Danmarkshavn</option>
                                <option <?= $class_time == 'America/Dawson' ? 'selected' : '' ?> value="America/Dawson">America/Dawson</option>
                                <option <?= $class_time == 'America/Dawson_Creek' ? 'selected' : '' ?> value="America/Dawson_Creek">America/Dawson_Creek</option>
                                <option <?= $class_time == 'America/Denver' ? 'selected' : '' ?> value="America/Denver">America/Denver</option>
                                <option <?= $class_time == 'America/Detroit' ? 'selected' : '' ?> value="America/Detroit">America/Detroit</option>
                                <option <?= $class_time == 'America/Dominica' ? 'selected' : '' ?> value="America/Dominica">America/Dominica</option>
                                <option <?= $class_time == 'America/Edmonton' ? 'selected' : '' ?> value="America/Edmonton">America/Edmonton</option>
                                <option <?= $class_time == 'America/Eirunepe' ? 'selected' : '' ?> value="America/Eirunepe">America/Eirunepe</option>
                                <option <?= $class_time == 'America/El_Salvador' ? 'selected' : '' ?> value="America/El_Salvador">America/El_Salvador</option>
                                <option <?= $class_time == 'America/Ensenada' ? 'selected' : '' ?> value="America/Ensenada">America/Ensenada</option>
                                <option <?= $class_time == 'America/Fort_Nelson' ? 'selected' : '' ?> value="America/Fort_Nelson">America/Fort_Nelson</option>
                                <option <?= $class_time == 'America/Fort_Wayne' ? 'selected' : '' ?> value="America/Fort_Wayne">America/Fort_Wayne</option>
                                <option <?= $class_time == 'America/Fortaleza' ? 'selected' : '' ?> value="America/Fortaleza">America/Fortaleza</option>
                                <option <?= $class_time == 'America/Glace_Bay' ? 'selected' : '' ?> value="America/Glace_Bay">America/Glace_Bay</option>
                                <option <?= $class_time == 'America/Godthab' ? 'selected' : '' ?> value="America/Godthab">America/Godthab</option>
                                <option <?= $class_time == 'America/Goose_Bay' ? 'selected' : '' ?> value="America/Goose_Bay">America/Goose_Bay</option>
                                <option <?= $class_time == 'America/Grand_Turk' ? 'selected' : '' ?> value="America/Grand_Turk">America/Grand_Turk</option>
                                <option <?= $class_time == 'America/Grenada' ? 'selected' : '' ?> value="America/Grenada">America/Grenada</option>
                                <option <?= $class_time == 'America/Guadeloupe' ? 'selected' : '' ?> value="America/Guadeloupe">America/Guadeloupe</option>
                                <option <?= $class_time == 'America/Guatemala' ? 'selected' : '' ?> value="America/Guatemala">America/Guatemala</option>
                                <option <?= $class_time == 'America/Guayaquil' ? 'selected' : '' ?> value="America/Guayaquil">America/Guayaquil</option>
                                <option <?= $class_time == 'America/Guyana' ? 'selected' : '' ?> value="America/Guyana">America/Guyana</option>
                                <option <?= $class_time == 'America/Halifax' ? 'selected' : '' ?> value="America/Halifax">America/Halifax</option>
                                <option <?= $class_time == 'America/Havana' ? 'selected' : '' ?> value="America/Havana">America/Havana</option>
                                <option <?= $class_time == 'America/Hermosillo' ? 'selected' : '' ?> value="America/Hermosillo">America/Hermosillo</option>
                                <option <?= $class_time == 'America/Indiana/Indianapolis' ? 'selected' : '' ?> value="America/Indiana/Indianapolis">America/Indiana/Indianapolis</option>
                                <option <?= $class_time == 'America/Indiana/Knox' ? 'selected' : '' ?> value="America/Indiana/Knox">America/Indiana/Knox</option>
                                <option <?= $class_time == 'America/Indiana/Marengo' ? 'selected' : '' ?> value="America/Indiana/Marengo">America/Indiana/Marengo</option>
                                <option <?= $class_time == 'America/Indiana/Petersburg' ? 'selected' : '' ?> value="America/Indiana/Petersburg">America/Indiana/Petersburg</option>
                                <option <?= $class_time == 'America/Indiana/Tell_City' ? 'selected' : '' ?> value="America/Indiana/Tell_City">America/Indiana/Tell_City</option>
                                <option <?= $class_time == 'America/Indiana/Vevay' ? 'selected' : '' ?> value="America/Indiana/Vevay">America/Indiana/Vevay</option>
                                <option <?= $class_time == 'America/Indiana/Vincennes' ? 'selected' : '' ?> value="America/Indiana/Vincennes">America/Indiana/Vincennes</option>
                                <option <?= $class_time == 'America/Indiana/Winamac' ? 'selected' : '' ?> value="America/Indiana/Winamac">America/Indiana/Winamac</option>
                                <option <?= $class_time == 'America/Indianapolis' ? 'selected' : '' ?> value="America/Indianapolis">America/Indianapolis</option>
                                <option <?= $class_time == 'America/Inuvik' ? 'selected' : '' ?> value="America/Inuvik">America/Inuvik</option>
                                <option <?= $class_time == 'America/Iqaluit' ? 'selected' : '' ?> value="America/Iqaluit">America/Iqaluit</option>
                                <option <?= $class_time == 'America/Jamaica' ? 'selected' : '' ?> value="America/Jamaica">America/Jamaica</option>
                                <option <?= $class_time == 'America/Jujuy' ? 'selected' : '' ?> value="America/Jujuy">America/Jujuy</option>
                                <option <?= $class_time == 'America/Juneau' ? 'selected' : '' ?> value="America/Juneau">America/Juneau</option>
                                <option <?= $class_time == 'America/Kentucky/Louisville' ? 'selected' : '' ?> value="America/Kentucky/Louisville">America/Kentucky/Louisville</option>
                                <option <?= $class_time == 'America/Kentucky/Monticello' ? 'selected' : '' ?> value="America/Kentucky/Monticello">America/Kentucky/Monticello</option>
                                <option <?= $class_time == 'America/Knox_IN' ? 'selected' : '' ?> value="America/Knox_IN">America/Knox_IN</option>
                                <option <?= $class_time == 'America/Kralendijk' ? 'selected' : '' ?> value="America/Kralendijk">America/Kralendijk</option>
                                <option <?= $class_time == 'America/La_Paz' ? 'selected' : '' ?> value="America/La_Paz">America/La_Paz</option>
                                <option <?= $class_time == 'America/Lima' ? 'selected' : '' ?> value="America/Lima">America/Lima</option>
                                <option <?= $class_time == 'America/Los_Angeles' ? 'selected' : '' ?> value="America/Los_Angeles">America/Los_Angeles</option>
                                <option <?= $class_time == 'America/Louisville' ? 'selected' : '' ?> value="America/Louisville">America/Louisville</option>
                                <option <?= $class_time == 'America/Lower_Princes' ? 'selected' : '' ?> value="America/Lower_Princes">America/Lower_Princes</option>
                                <option <?= $class_time == 'America/Maceio' ? 'selected' : '' ?> value="America/Maceio">America/Maceio</option>
                                <option <?= $class_time == 'America/Managua' ? 'selected' : '' ?> value="America/Managua">America/Managua</option>
                                <option <?= $class_time == 'America/Manaus' ? 'selected' : '' ?> value="America/Manaus">America/Manaus</option>
                                <option <?= $class_time == 'America/Marigot' ? 'selected' : '' ?> value="America/Marigot">America/Marigot</option>
                                <option <?= $class_time == 'America/Martinique' ? 'selected' : '' ?> value="America/Martinique">America/Martinique</option>
                                <option <?= $class_time == 'America/Matamoros' ? 'selected' : '' ?> value="America/Matamoros">America/Matamoros</option>
                                <option <?= $class_time == 'America/Mazatlan' ? 'selected' : '' ?> value="America/Mazatlan">America/Mazatlan</option>
                                <option <?= $class_time == 'America/Mendoza' ? 'selected' : '' ?> value="America/Mendoza">America/Mendoza</option>
                                <option <?= $class_time == 'America/Menominee' ? 'selected' : '' ?> value="America/Menominee">America/Menominee</option>
                                <option <?= $class_time == 'America/Merida' ? 'selected' : '' ?> value="America/Merida">America/Merida</option>
                                <option <?= $class_time == 'America/Metlakatla' ? 'selected' : '' ?> value="America/Metlakatla">America/Metlakatla</option>
                                <option <?= $class_time == 'America/Mexico_City' ? 'selected' : '' ?> value="America/Mexico_City">America/Mexico_City</option>
                                <option <?= $class_time == 'America/Miquelon' ? 'selected' : '' ?> value="America/Miquelon">America/Miquelon</option>
                                <option <?= $class_time == 'America/Moncton' ? 'selected' : '' ?> value="America/Moncton">America/Moncton</option>
                                <option <?= $class_time == 'America/Monterrey' ? 'selected' : '' ?> value="America/Monterrey">America/Monterrey</option>
                                <option <?= $class_time == 'America/Montevideo' ? 'selected' : '' ?> value="America/Montevideo">America/Montevideo</option>
                                <option <?= $class_time == 'America/Montreal' ? 'selected' : '' ?> value="America/Montreal">America/Montreal</option>
                                <option <?= $class_time == 'America/Montserrat' ? 'selected' : '' ?> value="America/Montserrat">America/Montserrat</option>
                                <option <?= $class_time == 'America/Nassau' ? 'selected' : '' ?> value="America/Nassau">America/Nassau</option>
                                <option <?= $class_time == 'America/New_York' ? 'selected' : '' ?> value="America/New_York">America/New_York</option>
                                <option <?= $class_time == 'America/Nipigon' ? 'selected' : '' ?> value="America/Nipigon">America/Nipigon</option>
                                <option <?= $class_time == 'America/Nome' ? 'selected' : '' ?> value="America/Nome">America/Nome</option>
                                <option <?= $class_time == 'America/Noronha' ? 'selected' : '' ?> value="America/Noronha">America/Noronha</option>
                                <option <?= $class_time == 'America/North_Dakota/Beulah' ? 'selected' : '' ?> value="America/North_Dakota/Beulah">America/North_Dakota/Beulah</option>
                                <option <?= $class_time == 'America/North_Dakota/Center' ? 'selected' : '' ?> value="America/North_Dakota/Center">America/North_Dakota/Center</option>
                                <option <?= $class_time == 'America/North_Dakota/New_Salem' ? 'selected' : '' ?> value="America/North_Dakota/New_Salem">America/North_Dakota/New_Salem</option>
                                <option <?= $class_time == 'America/Ojinaga' ? 'selected' : '' ?> value="America/Ojinaga">America/Ojinaga</option>
                                <option <?= $class_time == 'America/Panama' ? 'selected' : '' ?> value="America/Panama">America/Panama</option>
                                <option <?= $class_time == 'America/Pangnirtung' ? 'selected' : '' ?> value="America/Pangnirtung">America/Pangnirtung</option>
                                <option <?= $class_time == 'America/Paramaribo' ? 'selected' : '' ?> value="America/Paramaribo">America/Paramaribo</option>
                                <option <?= $class_time == 'America/Phoenix' ? 'selected' : '' ?> value="America/Phoenix">America/Phoenix</option>
                                <option <?= $class_time == 'America/Port_of_Spain' ? 'selected' : '' ?> value="America/Port_of_Spain">America/Port_of_Spain</option>
                                <option <?= $class_time == 'America/Port-au-Prince' ? 'selected' : '' ?> value="America/Port-au-Prince">America/Port-au-Prince</option>
                                <option <?= $class_time == 'America/Porto_Acre' ? 'selected' : '' ?> value="America/Porto_Acre">America/Porto_Acre</option>
                                <option <?= $class_time == 'America/Porto_Velho' ? 'selected' : '' ?> value="America/Porto_Velho">America/Porto_Velho</option>
                                <option <?= $class_time == 'America/Puerto_Rico' ? 'selected' : '' ?> value="America/Puerto_Rico">America/Puerto_Rico</option>
                                <option <?= $class_time == 'America/Punta_Arenas' ? 'selected' : '' ?> value="America/Punta_Arenas">America/Punta_Arenas</option>
                                <option <?= $class_time == 'America/Rainy_River' ? 'selected' : '' ?> value="America/Rainy_River">America/Rainy_River</option>
                                <option <?= $class_time == 'America/Rankin_Inlet' ? 'selected' : '' ?> value="America/Rankin_Inlet">America/Rankin_Inlet</option>
                                <option <?= $class_time == 'America/Recife' ? 'selected' : '' ?> value="America/Recife">America/Recife</option>
                                <option <?= $class_time == 'America/Regina' ? 'selected' : '' ?> value="America/Regina">America/Regina</option>
                                <option <?= $class_time == 'America/Resolute' ? 'selected' : '' ?> value="America/Resolute">America/Resolute</option>
                                <option <?= $class_time == 'America/Rio_Branco' ? 'selected' : '' ?> value="America/Rio_Branco">America/Rio_Branco</option>
                                <option <?= $class_time == 'America/Rosario' ? 'selected' : '' ?> value="America/Rosario">America/Rosario</option>
                                <option <?= $class_time == 'America/Santa_Isabel' ? 'selected' : '' ?> value="America/Santa_Isabel">America/Santa_Isabel</option>
                                <option <?= $class_time == 'America/Santarem' ? 'selected' : '' ?> value="America/Santarem">America/Santarem</option>
                                <option <?= $class_time == 'America/Santiago' ? 'selected' : '' ?> value="America/Santiago">America/Santiago</option>
                                <option <?= $class_time == 'America/Santo_Domingo' ? 'selected' : '' ?> value="America/Santo_Domingo">America/Santo_Domingo</option>
                                <option <?= $class_time == 'America/Sao_Paulo' ? 'selected' : '' ?> value="America/Sao_Paulo">America/Sao_Paulo</option>
                                <option <?= $class_time == 'America/Scoresbysund' ? 'selected' : '' ?> value="America/Scoresbysund">America/Scoresbysund</option>
                                <option <?= $class_time == 'America/Shiprock' ? 'selected' : '' ?> value="America/Shiprock">America/Shiprock</option>
                                <option <?= $class_time == 'America/Sitka' ? 'selected' : '' ?> value="America/Sitka">America/Sitka</option>
                                <option <?= $class_time == 'America/St_Barthelemy' ? 'selected' : '' ?> value="America/St_Barthelemy">America/St_Barthelemy</option>
                                <option <?= $class_time == 'America/St_Johns' ? 'selected' : '' ?> value="America/St_Johns">America/St_Johns</option>
                                <option <?= $class_time == 'America/St_Kitts' ? 'selected' : '' ?> value="America/St_Kitts">America/St_Kitts</option>
                                <option <?= $class_time == 'America/St_Lucia' ? 'selected' : '' ?> value="America/St_Lucia">America/St_Lucia</option>
                                <option <?= $class_time == 'America/St_Thomas' ? 'selected' : '' ?> value="America/St_Thomas">America/St_Thomas</option>
                                <option <?= $class_time == 'America/St_Vincent' ? 'selected' : '' ?> value="America/St_Vincent">America/St_Vincent</option>
                                <option <?= $class_time == 'America/Swift_Current' ? 'selected' : '' ?> value="America/Swift_Current">America/Swift_Current</option>
                                <option <?= $class_time == 'America/Tegucigalpa' ? 'selected' : '' ?> value="America/Tegucigalpa">America/Tegucigalpa</option>
                                <option <?= $class_time == 'America/Thule' ? 'selected' : '' ?> value="America/Thule">America/Thule</option>
                                <option <?= $class_time == 'America/Thunder_Bay' ? 'selected' : '' ?> value="America/Thunder_Bay">America/Thunder_Bay</option>
                                <option <?= $class_time == 'America/Tijuana' ? 'selected' : '' ?> value="America/Tijuana">America/Tijuana</option>
                                <option <?= $class_time == 'America/Toronto' ? 'selected' : '' ?> value="America/Toronto">America/Toronto</option>
                                <option <?= $class_time == 'America/Tortola' ? 'selected' : '' ?> value="America/Tortola">America/Tortola</option>
                                <option <?= $class_time == 'America/Vancouver' ? 'selected' : '' ?> value="America/Vancouver">America/Vancouver</option>
                                <option <?= $class_time == 'America/Virgin' ? 'selected' : '' ?> value="America/Virgin">America/Virgin</option>
                                <option <?= $class_time == 'America/Whitehorse' ? 'selected' : '' ?> value="America/Whitehorse">America/Whitehorse</option>
                                <option <?= $class_time == 'America/Winnipeg' ? 'selected' : '' ?> value="America/Winnipeg">America/Winnipeg</option>
                                <option <?= $class_time == 'America/Yakutat' ? 'selected' : '' ?> value="America/Yakutat">America/Yakutat</option>
                                <option <?= $class_time == 'America/Yellowknife' ? 'selected' : '' ?> value="America/Yellowknife">America/Yellowknife</option>
                                <option <?= $class_time == 'Antarctica/Casey' ? 'selected' : '' ?> value="Antarctica/Casey">Antarctica/Casey</option>
                                <option <?= $class_time == 'Antarctica/Davis' ? 'selected' : '' ?> value="Antarctica/Davis">Antarctica/Davis</option>
                                <option <?= $class_time == 'Antarctica/DumontDUrville' ? 'selected' : '' ?> value="Antarctica/DumontDUrville">Antarctica/DumontDUrville</option>
                                <option <?= $class_time == 'Antarctica/Macquarie' ? 'selected' : '' ?> value="Antarctica/Macquarie">Antarctica/Macquarie</option>
                                <option <?= $class_time == 'Antarctica/Mawson' ? 'selected' : '' ?> value="Antarctica/Mawson">Antarctica/Mawson</option>
                                <option <?= $class_time == 'Antarctica/McMurdo' ? 'selected' : '' ?> value="Antarctica/McMurdo">Antarctica/McMurdo</option>
                                <option <?= $class_time == 'Antarctica/Palmer' ? 'selected' : '' ?> value="Antarctica/Palmer">Antarctica/Palmer</option>
                                <option <?= $class_time == 'Antarctica/Rothera' ? 'selected' : '' ?> value="Antarctica/Rothera">Antarctica/Rothera</option>
                                <option <?= $class_time == 'Antarctica/South_Pole' ? 'selected' : '' ?> value="Antarctica/South_Pole">Antarctica/South_Pole</option>
                                <option <?= $class_time == 'Antarctica/Syowa' ? 'selected' : '' ?> value="Antarctica/Syowa">Antarctica/Syowa</option>
                                <option <?= $class_time == 'Antarctica/Troll' ? 'selected' : '' ?> value="Antarctica/Troll">Antarctica/Troll</option>
                                <option <?= $class_time == 'Antarctica/Vostok' ? 'selected' : '' ?> value="Antarctica/Vostok">Antarctica/Vostok</option>
                                <option <?= $class_time == 'Arctic/Longyearbyen' ? 'selected' : '' ?> value="Arctic/Longyearbyen">Arctic/Longyearbyen</option>
                                <option <?= $class_time == 'Asia/Aden' ? 'selected' : '' ?> value="Asia/Aden">Asia/Aden</option>
                                <option <?= $class_time == 'Asia/Almaty' ? 'selected' : '' ?> value="Asia/Almaty">Asia/Almaty</option>
                                <option <?= $class_time == 'Asia/Amman' ? 'selected' : '' ?> value="Asia/Amman">Asia/Amman</option>
                                <option <?= $class_time == 'Asia/Anadyr' ? 'selected' : '' ?> value="Asia/Anadyr">Asia/Anadyr</option>
                                <option <?= $class_time == 'Asia/Aqtau' ? 'selected' : '' ?> value="Asia/Aqtau">Asia/Aqtau</option>
                                <option <?= $class_time == 'Asia/Aqtobe' ? 'selected' : '' ?> value="Asia/Aqtobe">Asia/Aqtobe</option>
                                <option <?= $class_time == 'Asia/Ashgabat' ? 'selected' : '' ?> value="Asia/Ashgabat">Asia/Ashgabat</option>
                                <option <?= $class_time == 'Asia/Ashkhabad' ? 'selected' : '' ?> value="Asia/Ashkhabad">Asia/Ashkhabad</option>
                                <option <?= $class_time == 'Asia/Atyrau' ? 'selected' : '' ?> value="Asia/Atyrau">Asia/Atyrau</option>
                                <option <?= $class_time == 'Asia/Baghdad' ? 'selected' : '' ?> value="Asia/Baghdad">Asia/Baghdad</option>
                                <option <?= $class_time == 'Asia/Bahrain' ? 'selected' : '' ?> value="Asia/Bahrain">Asia/Bahrain</option>
                                <option <?= $class_time == 'Asia/Baku' ? 'selected' : '' ?> value="Asia/Baku">Asia/Baku</option>
                                <option <?= $class_time == 'Asia/Bangkok' ? 'selected' : '' ?> value="Asia/Bangkok">Asia/Bangkok</option>
                                <option <?= $class_time == 'Asia/Barnaul' ? 'selected' : '' ?> value="Asia/Barnaul">Asia/Barnaul</option>
                                <option <?= $class_time == 'Asia/Beirut' ? 'selected' : '' ?> value="Asia/Beirut">Asia/Beirut</option>
                                <option <?= $class_time == 'Asia/Bishkek' ? 'selected' : '' ?> value="Asia/Bishkek">Asia/Bishkek</option>
                                <option <?= $class_time == 'Asia/Brunei' ? 'selected' : '' ?> value="Asia/Brunei">Asia/Brunei</option>
                                <option <?= $class_time == 'Asia/Calcutta' ? 'selected' : '' ?> value="Asia/Calcutta">Asia/Calcutta</option>
                                <option <?= $class_time == 'Asia/Chita' ? 'selected' : '' ?> value="Asia/Chita">Asia/Chita</option>
                                <option <?= $class_time == 'Asia/Choibalsan' ? 'selected' : '' ?> value="Asia/Choibalsan">Asia/Choibalsan</option>
                                <option <?= $class_time == 'Asia/Chongqing' ? 'selected' : '' ?> value="Asia/Chongqing">Asia/Chongqing</option>
                                <option <?= $class_time == 'Asia/Chungking' ? 'selected' : '' ?> value="Asia/Chungking">Asia/Chungking</option>
                                <option <?= $class_time == 'Asia/Colombo' ? 'selected' : '' ?> value="Asia/Colombo">Asia/Colombo</option>
                                <option <?= $class_time == 'Asia/Dacca' ? 'selected' : '' ?> value="Asia/Dacca">Asia/Dacca</option>
                                <option <?= $class_time == 'Asia/Damascus' ? 'selected' : '' ?> value="Asia/Damascus">Asia/Damascus</option>
                                <option <?= $class_time == 'Asia/Dhaka' ? 'selected' : '' ?> value="Asia/Dhaka">Asia/Dhaka</option>
                                <option <?= $class_time == 'Asia/Dili' ? 'selected' : '' ?> value="Asia/Dili">Asia/Dili</option>
                                <option <?= $class_time == 'Asia/Dubai' ? 'selected' : '' ?> value="Asia/Dubai">Asia/Dubai</option>
                                <option <?= $class_time == 'Asia/Dushanbe' ? 'selected' : '' ?> value="Asia/Dushanbe">Asia/Dushanbe</option>
                                <option <?= $class_time == 'Asia/Famagusta' ? 'selected' : '' ?> value="Asia/Famagusta">Asia/Famagusta</option>
                                <option <?= $class_time == 'Asia/Gaza' ? 'selected' : '' ?> value="Asia/Gaza">Asia/Gaza</option>
                                <option <?= $class_time == 'Asia/Harbin' ? 'selected' : '' ?> value="Asia/Harbin">Asia/Harbin</option>
                                <option <?= $class_time == 'Asia/Hebron' ? 'selected' : '' ?> value="Asia/Hebron">Asia/Hebron</option>
                                <option <?= $class_time == 'Asia/Ho_Chi_Minh' ? 'selected' : '' ?> value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh</option>
                                <option <?= $class_time == 'Asia/Hong_Kong' ? 'selected' : '' ?> value="Asia/Hong_Kong">Asia/Hong_Kong</option>
                                <option <?= $class_time == 'Asia/Hovd' ? 'selected' : '' ?> value="Asia/Hovd">Asia/Hovd</option>
                                <option <?= $class_time == 'Asia/Irkutsk' ? 'selected' : '' ?> value="Asia/Irkutsk">Asia/Irkutsk</option>
                                <option <?= $class_time == 'Asia/Istanbul' ? 'selected' : '' ?> value="Asia/Istanbul">Asia/Istanbul</option>
                                <option <?= $class_time == 'Asia/Jakarta' ? 'selected' : '' ?> value="Asia/Jakarta">Asia/Jakarta</option>
                                <option <?= $class_time == 'Asia/Jayapura' ? 'selected' : '' ?> value="Asia/Jayapura">Asia/Jayapura</option>
                                <option <?= $class_time == 'Asia/Jerusalem' ? 'selected' : '' ?> value="Asia/Jerusalem">Asia/Jerusalem</option>
                                <option <?= $class_time == 'Asia/Kabul' ? 'selected' : '' ?> value="Asia/Kabul">Asia/Kabul</option>
                                <option <?= $class_time == 'Asia/Kamchatka' ? 'selected' : '' ?> value="Asia/Kamchatka">Asia/Kamchatka</option>
                                <option <?= $class_time == 'Asia/Karachi' ? 'selected' : '' ?> value="Asia/Karachi">Asia/Karachi</option>
                                <option <?= $class_time == 'Asia/Kashgar' ? 'selected' : '' ?> value="Asia/Kashgar">Asia/Kashgar</option>
                                <option <?= $class_time == 'Asia/Kathmandu' ? 'selected' : '' ?> value="Asia/Kathmandu">Asia/Kathmandu</option>
                                <option <?= $class_time == 'Asia/Katmandu' ? 'selected' : '' ?> value="Asia/Katmandu">Asia/Katmandu</option>
                                <option <?= $class_time == 'Asia/Khandyga' ? 'selected' : '' ?> value="Asia/Khandyga">Asia/Khandyga</option>
                                <option <?= $class_time == 'Asia/Kolkata' ? 'selected' : '' ?> value="Asia/Kolkata">Asia/Kolkata</option>
                                <option <?= $class_time == 'Asia/Krasnoyarsk' ? 'selected' : '' ?> value="Asia/Krasnoyarsk">Asia/Krasnoyarsk</option>
                                <option <?= $class_time == 'Asia/Kuala_Lumpur' ? 'selected' : '' ?> value="Asia/Kuala_Lumpur">Asia/Kuala_Lumpur</option>
                                <option <?= $class_time == 'Asia/Kuching' ? 'selected' : '' ?> value="Asia/Kuching">Asia/Kuching</option>
                                <option <?= $class_time == 'Asia/Kuwait' ? 'selected' : '' ?> value="Asia/Kuwait">Asia/Kuwait</option>
                                <option <?= $class_time == 'Asia/Macao' ? 'selected' : '' ?> value="Asia/Macao">Asia/Macao</option>
                                <option <?= $class_time == 'Asia/Macau' ? 'selected' : '' ?> value="Asia/Macau">Asia/Macau</option>
                                <option <?= $class_time == 'Asia/Magadan' ? 'selected' : '' ?> value="Asia/Magadan">Asia/Magadan</option>
                                <option <?= $class_time == 'Asia/Makassar' ? 'selected' : '' ?> value="Asia/Makassar">Asia/Makassar</option>
                                <option <?= $class_time == 'Asia/Manila' ? 'selected' : '' ?> value="Asia/Manila">Asia/Manila</option>
                                <option <?= $class_time == 'Asia/Muscat' ? 'selected' : '' ?> value="Asia/Muscat">Asia/Muscat</option>
                                <option <?= $class_time == 'Asia/Novokuznetsk' ? 'selected' : '' ?> value="Asia/Novokuznetsk">Asia/Novokuznetsk</option>
                                <option <?= $class_time == 'Asia/Novosibirsk' ? 'selected' : '' ?> value="Asia/Novosibirsk">Asia/Novosibirsk</option>
                                <option <?= $class_time == 'Asia/Omsk' ? 'selected' : '' ?> value="Asia/Omsk">Asia/Omsk</option>
                                <option <?= $class_time == 'Asia/Oral' ? 'selected' : '' ?> value="Asia/Oral">Asia/Oral</option>
                                <option <?= $class_time == 'Asia/Phnom_Penh' ? 'selected' : '' ?> value="Asia/Phnom_Penh">Asia/Phnom_Penh</option>
                                <option <?= $class_time == 'Asia/Pontianak' ? 'selected' : '' ?> value="Asia/Pontianak">Asia/Pontianak</option>
                                <option <?= $class_time == 'Asia/Pyongyang' ? 'selected' : '' ?> value="Asia/Pyongyang">Asia/Pyongyang</option>
                                <option <?= $class_time == 'Asia/Qatar' ? 'selected' : '' ?> value="Asia/Qatar">Asia/Qatar</option>
                                <option <?= $class_time == 'Asia/Qyzylorda' ? 'selected' : '' ?> value="Asia/Qyzylorda">Asia/Qyzylorda</option>
                                <option <?= $class_time == 'Asia/Rangoon' ? 'selected' : '' ?> value="Asia/Rangoon">Asia/Rangoon</option>
                                <option <?= $class_time == 'Asia/Riyadh' ? 'selected' : '' ?> value="Asia/Riyadh">Asia/Riyadh</option>
                                <option <?= $class_time == 'Asia/Saigon' ? 'selected' : '' ?> value="Asia/Saigon">Asia/Saigon</option>
                                <option <?= $class_time == 'Asia/Sakhalin' ? 'selected' : '' ?> value="Asia/Sakhalin">Asia/Sakhalin</option>
                                <option <?= $class_time == 'Asia/Samarkand' ? 'selected' : '' ?> value="Asia/Samarkand">Asia/Samarkand</option>
                                <option <?= $class_time == 'Asia/Seoul' ? 'selected' : '' ?> value="Asia/Seoul">Asia/Seoul</option>
                                <option <?= $class_time == 'Asia/Shanghai' ? 'selected' : '' ?> value="Asia/Shanghai">Asia/Shanghai</option>
                                <option <?= $class_time == 'Asia/Singapore' ? 'selected' : '' ?> value="Asia/Singapore">Asia/Singapore</option>
                                <option <?= $class_time == 'Asia/Srednekolymsk' ? 'selected' : '' ?> value="Asia/Srednekolymsk">Asia/Srednekolymsk</option>
                                <option <?= $class_time == 'Asia/Taipei' ? 'selected' : '' ?> value="Asia/Taipei">Asia/Taipei</option>
                                <option <?= $class_time == 'Asia/Tashkent' ? 'selected' : '' ?> value="Asia/Tashkent">Asia/Tashkent</option>
                                <option <?= $class_time == 'Asia/Tbilisi' ? 'selected' : '' ?> value="Asia/Tbilisi">Asia/Tbilisi</option>
                                <option <?= $class_time == 'Asia/Tehran' ? 'selected' : '' ?> value="Asia/Tehran">Asia/Tehran</option>
                                <option <?= $class_time == 'Asia/Tel_Aviv' ? 'selected' : '' ?> value="Asia/Tel_Aviv">Asia/Tel_Aviv</option>
                                <option <?= $class_time == 'Asia/Thimbu' ? 'selected' : '' ?> value="Asia/Thimbu">Asia/Thimbu</option>
                                <option <?= $class_time == 'Asia/Thimphu' ? 'selected' : '' ?> value="Asia/Thimphu">Asia/Thimphu</option>
                                <option <?= $class_time == 'Asia/Tokyo' ? 'selected' : '' ?> value="Asia/Tokyo">Asia/Tokyo</option>
                                <option <?= $class_time == 'Asia/Tomsk' ? 'selected' : '' ?> value="Asia/Tomsk">Asia/Tomsk</option>
                                <option <?= $class_time == 'Asia/Ujung_Pandang' ? 'selected' : '' ?> value="Asia/Ujung_Pandang">Asia/Ujung_Pandang</option>
                                <option <?= $class_time == 'Asia/Ulaanbaatar' ? 'selected' : '' ?> value="Asia/Ulaanbaatar">Asia/Ulaanbaatar</option>
                                <option <?= $class_time == 'Asia/Ulan_Bator' ? 'selected' : '' ?> value="Asia/Ulan_Bator">Asia/Ulan_Bator</option>
                                <option <?= $class_time == 'Asia/Urumqi' ? 'selected' : '' ?> value="Asia/Urumqi">Asia/Urumqi</option>
                                <option <?= $class_time == 'Asia/Ust-Nera' ? 'selected' : '' ?> value="Asia/Ust-Nera">Asia/Ust-Nera</option>
                                <option <?= $class_time == 'Asia/Vientiane' ? 'selected' : '' ?> value="Asia/Vientiane">Asia/Vientiane</option>
                                <option <?= $class_time == 'Asia/Vladivostok' ? 'selected' : '' ?> value="Asia/Vladivostok">Asia/Vladivostok</option>
                                <option <?= $class_time == 'Asia/Yakutsk' ? 'selected' : '' ?> value="Asia/Yakutsk">Asia/Yakutsk</option>
                                <option <?= $class_time == 'Asia/Yangon' ? 'selected' : '' ?> value="Asia/Yangon">Asia/Yangon</option>
                                <option <?= $class_time == 'Asia/Yekaterinburg' ? 'selected' : '' ?> value="Asia/Yekaterinburg">Asia/Yekaterinburg</option>
                                <option <?= $class_time == 'Asia/Yerevan' ? 'selected' : '' ?> value="Asia/Yerevan">Asia/Yerevan</option>
                                <option <?= $class_time == 'Atlantic/Azores' ? 'selected' : '' ?> value="Atlantic/Azores">Atlantic/Azores</option>
                                <option <?= $class_time == 'Atlantic/Bermuda' ? 'selected' : '' ?> value="Atlantic/Bermuda">Atlantic/Bermuda</option>
                                <option <?= $class_time == 'Atlantic/Canary' ? 'selected' : '' ?> value="Atlantic/Canary">Atlantic/Canary</option>
                                <option <?= $class_time == 'Atlantic/Cape_Verde' ? 'selected' : '' ?> value="Atlantic/Cape_Verde">Atlantic/Cape_Verde</option>
                                <option <?= $class_time == 'Atlantic/Faeroe' ? 'selected' : '' ?> value="Atlantic/Faeroe">Atlantic/Faeroe</option>
                                <option <?= $class_time == 'Atlantic/Faroe' ? 'selected' : '' ?> value="Atlantic/Faroe">Atlantic/Faroe</option>
                                <option <?= $class_time == 'Atlantic/Jan_Mayen' ? 'selected' : '' ?> value="Atlantic/Jan_Mayen">Atlantic/Jan_Mayen</option>
                                <option <?= $class_time == 'Atlantic/Madeira' ? 'selected' : '' ?> value="Atlantic/Madeira">Atlantic/Madeira</option>
                                <option <?= $class_time == 'Atlantic/Reykjavik' ? 'selected' : '' ?> value="Atlantic/Reykjavik">Atlantic/Reykjavik</option>
                                <option <?= $class_time == 'Atlantic/South_Georgia' ? 'selected' : '' ?> value="Atlantic/South_Georgia">Atlantic/South_Georgia</option>
                                <option <?= $class_time == 'Atlantic/St_Helena' ? 'selected' : '' ?> value="Atlantic/St_Helena">Atlantic/St_Helena</option>
                                <option <?= $class_time == 'Atlantic/Stanley' ? 'selected' : '' ?> value="Atlantic/Stanley">Atlantic/Stanley</option>
                                <option <?= $class_time == 'Australia/Adelaide' ? 'selected' : '' ?> value="Australia/Adelaide">Australia/Adelaide</option>
                                <option <?= $class_time == 'Australia/Brisbane' ? 'selected' : '' ?> value="Australia/Brisbane">Australia/Brisbane</option>
                                <option <?= $class_time == 'Australia/Broken_Hill' ? 'selected' : '' ?> value="Australia/Broken_Hill">Australia/Broken_Hill</option>
                                <option <?= $class_time == 'Australia/Canberra' ? 'selected' : '' ?> value="Australia/Canberra">Australia/Canberra</option>
                                <option <?= $class_time == 'Australia/Currie' ? 'selected' : '' ?> value="Australia/Currie">Australia/Currie</option>
                                <option <?= $class_time == 'Australia/Darwin' ? 'selected' : '' ?> value="Australia/Darwin">Australia/Darwin</option>
                                <option <?= $class_time == 'Australia/Eucla' ? 'selected' : '' ?> value="Australia/Eucla">Australia/Eucla</option>
                                <option <?= $class_time == 'Australia/Hobart' ? 'selected' : '' ?> value="Australia/Hobart">Australia/Hobart</option>
                                <option <?= $class_time == 'Australia/Lindeman' ? 'selected' : '' ?> value="Australia/Lindeman">Australia/Lindeman</option>
                                <option <?= $class_time == 'Australia/Lord_Howe' ? 'selected' : '' ?> value="Australia/Lord_Howe">Australia/Lord_Howe</option>
                                <option <?= $class_time == 'Australia/Melbourne' ? 'selected' : '' ?> value="Australia/Melbourne">Australia/Melbourne</option>
                                <option <?= $class_time == 'Australia/Perth' ? 'selected' : '' ?> value="Australia/Perth">Australia/Perth</option>
                                <option <?= $class_time == 'Australia/Sydney' ? 'selected' : '' ?> value="Australia/Sydney">Australia/Sydney</option>
                                <option <?= $class_time == 'Australia/Yancowinna' ? 'selected' : '' ?> value="Australia/Yancowinna">Australia/Yancowinna</option>
                                <option <?= $class_time == 'Etc/GMT' ? 'selected' : '' ?> value="Etc/GMT">Etc/GMT</option>
                                <option <?= $class_time == 'Etc/GMT+0' ? 'selected' : '' ?> value="Etc/GMT+0">Etc/GMT+0</option>
                                <option <?= $class_time == 'Etc/GMT+1' ? 'selected' : '' ?> value="Etc/GMT+1">Etc/GMT+1</option>
                                <option <?= $class_time == 'Etc/GMT+10' ? 'selected' : '' ?> value="Etc/GMT+10">Etc/GMT+10</option>
                                <option <?= $class_time == 'Etc/GMT+11' ? 'selected' : '' ?> value="Etc/GMT+11">Etc/GMT+11</option>
                                <option <?= $class_time == 'Etc/GMT+12' ? 'selected' : '' ?> value="Etc/GMT+12">Etc/GMT+12</option>
                                <option <?= $class_time == 'Etc/GMT+2' ? 'selected' : '' ?> value="Etc/GMT+2">Etc/GMT+2</option>
                                <option <?= $class_time == 'Etc/GMT+3' ? 'selected' : '' ?> value="Etc/GMT+3">Etc/GMT+3</option>
                                <option <?= $class_time == 'Etc/GMT+4' ? 'selected' : '' ?> value="Etc/GMT+4">Etc/GMT+4</option>
                                <option <?= $class_time == 'Etc/GMT+5' ? 'selected' : '' ?> value="Etc/GMT+5">Etc/GMT+5</option>
                                <option <?= $class_time == 'Etc/GMT+6' ? 'selected' : '' ?> value="Etc/GMT+6">Etc/GMT+6</option>
                                <option <?= $class_time == 'Etc/GMT+7' ? 'selected' : '' ?> value="Etc/GMT+7">Etc/GMT+7</option>
                                <option <?= $class_time == 'Etc/GMT+8' ? 'selected' : '' ?> value="Etc/GMT+8">Etc/GMT+8</option>
                                <option <?= $class_time == 'Etc/GMT+9' ? 'selected' : '' ?> value="Etc/GMT+9">Etc/GMT+9</option>
                                <option <?= $class_time == 'Etc/GMT0' ? 'selected' : '' ?> value="Etc/GMT0">Etc/GMT0</option>
                                <option <?= $class_time == 'Etc/GMT-0' ? 'selected' : '' ?> value="Etc/GMT-0">Etc/GMT-0</option>
                                <option <?= $class_time == 'Etc/GMT-1' ? 'selected' : '' ?> value="Etc/GMT-1">Etc/GMT-1</option>
                                <option <?= $class_time == 'Etc/GMT-10' ? 'selected' : '' ?> value="Etc/GMT-10">Etc/GMT-10</option>
                                <option <?= $class_time == 'Etc/GMT-11' ? 'selected' : '' ?> value="Etc/GMT-11">Etc/GMT-11</option>
                                <option <?= $class_time == 'Etc/GMT-12' ? 'selected' : '' ?> value="Etc/GMT-12">Etc/GMT-12</option>
                                <option <?= $class_time == 'Etc/GMT-13' ? 'selected' : '' ?> value="Etc/GMT-13">Etc/GMT-13</option>
                                <option <?= $class_time == 'Etc/GMT-14' ? 'selected' : '' ?> value="Etc/GMT-14">Etc/GMT-14</option>
                                <option <?= $class_time == 'Etc/GMT-2' ? 'selected' : '' ?> value="Etc/GMT-2">Etc/GMT-2</option>
                                <option <?= $class_time == 'Etc/GMT-3' ? 'selected' : '' ?> value="Etc/GMT-3">Etc/GMT-3</option>
                                <option <?= $class_time == 'Etc/GMT-4' ? 'selected' : '' ?> value="Etc/GMT-4">Etc/GMT-4</option>
                                <option <?= $class_time == 'Etc/GMT-5' ? 'selected' : '' ?> value="Etc/GMT-5">Etc/GMT-5</option>
                                <option <?= $class_time == 'Etc/GMT-6' ? 'selected' : '' ?> value="Etc/GMT-6">Etc/GMT-6</option>
                                <option <?= $class_time == 'Etc/GMT-7' ? 'selected' : '' ?> value="Etc/GMT-7">Etc/GMT-7</option>
                                <option <?= $class_time == 'Etc/GMT-8' ? 'selected' : '' ?> value="Etc/GMT-8">Etc/GMT-8</option>
                                <option <?= $class_time == 'Etc/GMT-9' ? 'selected' : '' ?> value="Etc/GMT-9">Etc/GMT-9</option>
                                <option <?= $class_time == 'Etc/UTC' ? 'selected' : '' ?> value="Etc/UTC">Etc/UTC</option>
                                <option <?= $class_time == 'Europe/Amsterdam' ? 'selected' : '' ?> value="Europe/Amsterdam">Europe/Amsterdam</option>
                                <option <?= $class_time == 'Europe/Andorra' ? 'selected' : '' ?> value="Europe/Andorra">Europe/Andorra</option>
                                <option <?= $class_time == 'Europe/Astrakhan' ? 'selected' : '' ?> value="Europe/Astrakhan">Europe/Astrakhan</option>
                                <option <?= $class_time == 'Europe/Athens' ? 'selected' : '' ?> value="Europe/Athens">Europe/Athens</option>
                                <option <?= $class_time == 'Europe/Belfast' ? 'selected' : '' ?> value="Europe/Belfast">Europe/Belfast</option>
                                <option <?= $class_time == 'Europe/Belgrade' ? 'selected' : '' ?> value="Europe/Belgrade">Europe/Belgrade</option>
                                <option <?= $class_time == 'Europe/Berlin' ? 'selected' : '' ?> value="Europe/Berlin">Europe/Berlin</option>
                                <option <?= $class_time == 'Europe/Bratislava' ? 'selected' : '' ?> value="Europe/Bratislava">Europe/Bratislava</option>
                                <option <?= $class_time == 'Europe/Brussels' ? 'selected' : '' ?> value="Europe/Brussels">Europe/Brussels</option>
                                <option <?= $class_time == 'Europe/Bucharest' ? 'selected' : '' ?> value="Europe/Bucharest">Europe/Bucharest</option>
                                <option <?= $class_time == 'Europe/Budapest' ? 'selected' : '' ?> value="Europe/Budapest">Europe/Budapest</option>
                                <option <?= $class_time == 'Europe/Busingen' ? 'selected' : '' ?> value="Europe/Busingen">Europe/Busingen</option>
                                <option <?= $class_time == 'Europe/Chisinau' ? 'selected' : '' ?> value="Europe/Chisinau">Europe/Chisinau</option>
                                <option <?= $class_time == 'Europe/Copenhagen' ? 'selected' : '' ?> value="Europe/Copenhagen">Europe/Copenhagen</option>
                                <option <?= $class_time == 'Europe/Dublin' ? 'selected' : '' ?> value="Europe/Dublin">Europe/Dublin</option>
                                <option <?= $class_time == 'Europe/Gibraltar' ? 'selected' : '' ?> value="Europe/Gibraltar">Europe/Gibraltar</option>
                                <option <?= $class_time == 'Europe/Guernsey' ? 'selected' : '' ?> value="Europe/Guernsey">Europe/Guernsey</option>
                                <option <?= $class_time == 'Europe/Helsinki' ? 'selected' : '' ?> value="Europe/Helsinki">Europe/Helsinki</option>
                                <option <?= $class_time == 'Europe/Isle_of_Man' ? 'selected' : '' ?> value="Europe/Isle_of_Man">Europe/Isle_of_Man</option>
                                <option <?= $class_time == 'Europe/Istanbul' ? 'selected' : '' ?> value="Europe/Istanbul">Europe/Istanbul</option>
                                <option <?= $class_time == 'Europe/Jersey' ? 'selected' : '' ?> value="Europe/Jersey">Europe/Jersey</option>
                                <option <?= $class_time == 'Europe/Kaliningrad' ? 'selected' : '' ?> value="Europe/Kaliningrad">Europe/Kaliningrad</option>
                                <option <?= $class_time == 'Europe/Kiev' ? 'selected' : '' ?> value="Europe/Kiev">Europe/Kiev</option>
                                <option <?= $class_time == 'Europe/Kirov' ? 'selected' : '' ?> value="Europe/Kirov">Europe/Kirov</option>
                                <option <?= $class_time == 'Europe/Lisbon' ? 'selected' : '' ?> value="Europe/Lisbon">Europe/Lisbon</option>
                                <option <?= $class_time == 'Europe/Ljubljana' ? 'selected' : '' ?> value="Europe/Ljubljana">Europe/Ljubljana</option>
                                <option <?= $class_time == 'Europe/London' ? 'selected' : '' ?> value="Europe/London">Europe/London</option>
                                <option <?= $class_time == 'Europe/Luxembourg' ? 'selected' : '' ?> value="Europe/Luxembourg">Europe/Luxembourg</option>
                                <option <?= $class_time == 'Europe/Madrid' ? 'selected' : '' ?> value="Europe/Madrid">Europe/Madrid</option>
                                <option <?= $class_time == 'Europe/Malta' ? 'selected' : '' ?> value="Europe/Malta">Europe/Malta</option>
                                <option <?= $class_time == 'Europe/Mariehamn' ? 'selected' : '' ?> value="Europe/Mariehamn">Europe/Mariehamn</option>
                                <option <?= $class_time == 'Europe/Minsk' ? 'selected' : '' ?> value="Europe/Minsk">Europe/Minsk</option>
                                <option <?= $class_time == 'Europe/Monaco' ? 'selected' : '' ?> value="Europe/Monaco">Europe/Monaco</option>
                                <option <?= $class_time == 'Europe/Moscow' ? 'selected' : '' ?> value="Europe/Moscow">Europe/Moscow</option>
                                <option <?= $class_time == 'Europe/Nicosia' ? 'selected' : '' ?> value="Europe/Nicosia">Europe/Nicosia</option>
                                <option <?= $class_time == 'Europe/Oslo' ? 'selected' : '' ?> value="Europe/Oslo">Europe/Oslo</option>
                                <option <?= $class_time == 'Europe/Paris' ? 'selected' : '' ?> value="Europe/Paris">Europe/Paris</option>
                                <option <?= $class_time == 'Europe/Podgorica' ? 'selected' : '' ?> value="Europe/Podgorica">Europe/Podgorica</option>
                                <option <?= $class_time == 'Europe/Prague' ? 'selected' : '' ?> value="Europe/Prague">Europe/Prague</option>
                                <option <?= $class_time == 'Europe/Riga' ? 'selected' : '' ?> value="Europe/Riga">Europe/Riga</option>
                                <option <?= $class_time == 'Europe/Rome' ? 'selected' : '' ?> value="Europe/Rome">Europe/Rome</option>
                                <option <?= $class_time == 'Europe/Samara' ? 'selected' : '' ?> value="Europe/Samara">Europe/Samara</option>
                                <option <?= $class_time == 'Europe/San_Marino' ? 'selected' : '' ?> value="Europe/San_Marino">Europe/San_Marino</option>
                                <option <?= $class_time == 'Europe/Sarajevo' ? 'selected' : '' ?> value="Europe/Sarajevo">Europe/Sarajevo</option>
                                <option <?= $class_time == 'Europe/Saratov' ? 'selected' : '' ?> value="Europe/Saratov">Europe/Saratov</option>
                                <option <?= $class_time == 'Europe/Simferopol' ? 'selected' : '' ?> value="Europe/Simferopol">Europe/Simferopol</option>
                                <option <?= $class_time == 'Europe/Skopje' ? 'selected' : '' ?> value="Europe/Skopje">Europe/Skopje</option>
                                <option <?= $class_time == 'Europe/Sofia' ? 'selected' : '' ?> value="Europe/Sofia">Europe/Sofia</option>
                                <option <?= $class_time == 'Europe/Stockholm' ? 'selected' : '' ?> value="Europe/Stockholm">Europe/Stockholm</option>
                                <option <?= $class_time == 'Europe/Tallinn' ? 'selected' : '' ?> value="Europe/Tallinn">Europe/Tallinn</option>
                                <option <?= $class_time == 'Europe/Tirane' ? 'selected' : '' ?> value="Europe/Tirane">Europe/Tirane</option>
                                <option <?= $class_time == 'Europe/Tiraspol' ? 'selected' : '' ?> value="Europe/Tiraspol">Europe/Tiraspol</option>
                                <option <?= $class_time == 'Europe/Ulyanovsk' ? 'selected' : '' ?> value="Europe/Ulyanovsk">Europe/Ulyanovsk</option>
                                <option <?= $class_time == 'Europe/Uzhgorod' ? 'selected' : '' ?> value="Europe/Uzhgorod">Europe/Uzhgorod</option>
                                <option <?= $class_time == 'Europe/Vaduz' ? 'selected' : '' ?> value="Europe/Vaduz">Europe/Vaduz</option>
                                <option <?= $class_time == 'Europe/Vatican' ? 'selected' : '' ?> value="Europe/Vatican">Europe/Vatican</option>
                                <option <?= $class_time == 'Europe/Vienna' ? 'selected' : '' ?> value="Europe/Vienna">Europe/Vienna</option>
                                <option <?= $class_time == 'Europe/Vilnius' ? 'selected' : '' ?> value="Europe/Vilnius">Europe/Vilnius</option>
                                <option <?= $class_time == 'Europe/Volgograd' ? 'selected' : '' ?> value="Europe/Volgograd">Europe/Volgograd</option>
                                <option <?= $class_time == 'Europe/Warsaw' ? 'selected' : '' ?> value="Europe/Warsaw">Europe/Warsaw</option>
                                <option <?= $class_time == 'Europe/Zagreb' ? 'selected' : '' ?> value="Europe/Zagreb">Europe/Zagreb</option>
                                <option <?= $class_time == 'Europe/Zaporozhye' ? 'selected' : '' ?> value="Europe/Zaporozhye">Europe/Zaporozhye</option>
                                <option <?= $class_time == 'Europe/Zurich' ? 'selected' : '' ?> value="Europe/Zurich">Europe/Zurich</option>
                                <option <?= $class_time == 'GMT' ? 'selected' : '' ?> value="GMT">GMT</option>
                                <option <?= $class_time == 'Indian/Antananarivo' ? 'selected' : '' ?> value="Indian/Antananarivo">Indian/Antananarivo</option>
                                <option <?= $class_time == 'Indian/Chagos' ? 'selected' : '' ?> value="Indian/Chagos">Indian/Chagos</option>
                                <option <?= $class_time == 'Indian/Christmas' ? 'selected' : '' ?> value="Indian/Christmas">Indian/Christmas</option>
                                <option <?= $class_time == 'Indian/Cocos' ? 'selected' : '' ?> value="Indian/Cocos">Indian/Cocos</option>
                                <option <?= $class_time == 'Indian/Comoro' ? 'selected' : '' ?> value="Indian/Comoro">Indian/Comoro</option>
                                <option <?= $class_time == 'Indian/Kerguelen' ? 'selected' : '' ?> value="Indian/Kerguelen">Indian/Kerguelen</option>
                                <option <?= $class_time == 'Indian/Mahe' ? 'selected' : '' ?> value="Indian/Mahe">Indian/Mahe</option>
                                <option <?= $class_time == 'Indian/Maldives' ? 'selected' : '' ?> value="Indian/Maldives">Indian/Maldives</option>
                                <option <?= $class_time == 'Indian/Mauritius' ? 'selected' : '' ?> value="Indian/Mauritius">Indian/Mauritius</option>
                                <option <?= $class_time == 'Indian/Mayotte' ? 'selected' : '' ?> value="Indian/Mayotte">Indian/Mayotte</option>
                                <option <?= $class_time == 'Indian/Reunion' ? 'selected' : '' ?> value="Indian/Reunion">Indian/Reunion</option>
                                <option <?= $class_time == 'Pacific/Apia' ? 'selected' : '' ?> value="Pacific/Apia">Pacific/Apia</option>
                                <option <?= $class_time == 'Pacific/Auckland' ? 'selected' : '' ?> value="Pacific/Auckland">Pacific/Auckland</option>
                                <option <?= $class_time == 'Pacific/Bougainville' ? 'selected' : '' ?> value="Pacific/Bougainville">Pacific/Bougainville</option>
                                <option <?= $class_time == 'Pacific/Chatham' ? 'selected' : '' ?> value="Pacific/Chatham">Pacific/Chatham</option>
                                <option <?= $class_time == 'Pacific/Chuuk' ? 'selected' : '' ?> value="Pacific/Chuuk">Pacific/Chuuk</option>
                                <option <?= $class_time == 'Pacific/Easter' ? 'selected' : '' ?> value="Pacific/Easter">Pacific/Easter</option>
                                <option <?= $class_time == 'Pacific/Efate' ? 'selected' : '' ?> value="Pacific/Efate">Pacific/Efate</option>
                                <option <?= $class_time == 'Pacific/Enderbury' ? 'selected' : '' ?> value="Pacific/Enderbury">Pacific/Enderbury</option>
                                <option <?= $class_time == 'Pacific/Fakaofo' ? 'selected' : '' ?> value="Pacific/Fakaofo">Pacific/Fakaofo</option>
                                <option <?= $class_time == 'Pacific/Fiji' ? 'selected' : '' ?> value="Pacific/Fiji">Pacific/Fiji</option>
                                <option <?= $class_time == 'Pacific/Funafuti' ? 'selected' : '' ?> value="Pacific/Funafuti">Pacific/Funafuti</option>
                                <option <?= $class_time == 'Pacific/Galapagos' ? 'selected' : '' ?> value="Pacific/Galapagos">Pacific/Galapagos</option>
                                <option <?= $class_time == 'Pacific/Gambier' ? 'selected' : '' ?> value="Pacific/Gambier">Pacific/Gambier</option>
                                <option <?= $class_time == 'Pacific/Guadalcanal' ? 'selected' : '' ?> value="Pacific/Guadalcanal">Pacific/Guadalcanal</option>
                                <option <?= $class_time == 'Pacific/Guam' ? 'selected' : '' ?> value="Pacific/Guam">Pacific/Guam</option>
                                <option <?= $class_time == 'Pacific/Honolulu' ? 'selected' : '' ?> value="Pacific/Honolulu">Pacific/Honolulu</option>
                                <option <?= $class_time == 'Pacific/Johnston' ? 'selected' : '' ?> value="Pacific/Johnston">Pacific/Johnston</option>
                                <option <?= $class_time == 'Pacific/Kiritimati' ? 'selected' : '' ?> value="Pacific/Kiritimati">Pacific/Kiritimati</option>
                                <option <?= $class_time == 'Pacific/Kosrae' ? 'selected' : '' ?> value="Pacific/Kosrae">Pacific/Kosrae</option>
                                <option <?= $class_time == 'Pacific/Kwajalein' ? 'selected' : '' ?> value="Pacific/Kwajalein">Pacific/Kwajalein</option>
                                <option <?= $class_time == 'Pacific/Majuro' ? 'selected' : '' ?> value="Pacific/Majuro">Pacific/Majuro</option>
                                <option <?= $class_time == 'Pacific/Marquesas' ? 'selected' : '' ?> value="Pacific/Marquesas">Pacific/Marquesas</option>
                                <option <?= $class_time == 'Pacific/Midway' ? 'selected' : '' ?> value="Pacific/Midway">Pacific/Midway</option>
                                <option <?= $class_time == 'Pacific/Nauru' ? 'selected' : '' ?> value="Pacific/Nauru">Pacific/Nauru</option>
                                <option <?= $class_time == 'Pacific/Niue' ? 'selected' : '' ?> value="Pacific/Niue">Pacific/Niue</option>
                                <option <?= $class_time == 'Pacific/Norfolk' ? 'selected' : '' ?> value="Pacific/Norfolk">Pacific/Norfolk</option>
                                <option <?= $class_time == 'Pacific/Noumea' ? 'selected' : '' ?> value="Pacific/Noumea">Pacific/Noumea</option>
                                <option <?= $class_time == 'Pacific/Pago_Pago' ? 'selected' : '' ?> value="Pacific/Pago_Pago">Pacific/Pago_Pago</option>
                                <option <?= $class_time == 'Pacific/Palau' ? 'selected' : '' ?> value="Pacific/Palau">Pacific/Palau</option>
                                <option <?= $class_time == 'Pacific/Pitcairn' ? 'selected' : '' ?> value="Pacific/Pitcairn">Pacific/Pitcairn</option>
                                <option <?= $class_time == 'Pacific/Pohnpei' ? 'selected' : '' ?> value="Pacific/Pohnpei">Pacific/Pohnpei</option>
                                <option <?= $class_time == 'Pacific/Ponape' ? 'selected' : '' ?> value="Pacific/Ponape">Pacific/Ponape</option>
                                <option <?= $class_time == 'Pacific/Port_Moresby' ? 'selected' : '' ?> value="Pacific/Port_Moresby">Pacific/Port_Moresby</option>
                                <option <?= $class_time == 'Pacific/Rarotonga' ? 'selected' : '' ?> value="Pacific/Rarotonga">Pacific/Rarotonga</option>
                                <option <?= $class_time == 'Pacific/Saipan' ? 'selected' : '' ?> value="Pacific/Saipan">Pacific/Saipan</option>
                                <option <?= $class_time == 'Pacific/Samoa' ? 'selected' : '' ?> value="Pacific/Samoa">Pacific/Samoa</option>
                                <option <?= $class_time == 'Pacific/Tahiti' ? 'selected' : '' ?> value="Pacific/Tahiti">Pacific/Tahiti</option>
                                <option <?= $class_time == 'Pacific/Tarawa' ? 'selected' : '' ?> value="Pacific/Tarawa">Pacific/Tarawa</option>
                                <option <?= $class_time == 'Pacific/Tongatapu' ? 'selected' : '' ?> value="Pacific/Tongatapu">Pacific/Tongatapu</option>
                                <option <?= $class_time == 'Pacific/Truk' ? 'selected' : '' ?> value="Pacific/Truk">Pacific/Truk</option>
                                <option <?= $class_time == 'Pacific/Wake' ? 'selected' : '' ?> value="Pacific/Wake">Pacific/Wake</option>
                                <option <?= $class_time == 'Pacific/Wallis' ? 'selected' : '' ?> value="Pacific/Wallis">Pacific/Wallis</option>
                                <option <?= $class_time == 'Pacific/Yap' ? 'selected' : '' ?> value="Pacific/Yap">Pacific/Yap</option>
                            </select>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div><!-- .dashboard-item -->
</div><!-- .standard-dashboard-body-content -->