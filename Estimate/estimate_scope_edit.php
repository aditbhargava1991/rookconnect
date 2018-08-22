<?php include_once('../include.php');
checkAuthorised('estimate');
$estimateid = 0;
if($_GET['estimateid'] > 0) {
	$estimateid = $_GET['estimateid'];
}
$priorid = 0;
if($_GET['priorid'] > 0) {
	$priorid = $_GET['priorid'];
}
if(!empty($_GET['scope'])) {
	$scope_name = '';
	$scope_names = $dbc->query("SELECT `scope_name` FROM `estimate_scope` WHERE `deleted`=0 AND IFNULL(`scope_name`,'') != ''");
	while($scope_name_row = $scope_names->fetch_array()[0]) {
		if(config_safe_str($scope_name_row) == $_GET['scope']) {
			$scope_name = $scope_name_row;
		}
	}
}
$rates = [];
$query = mysqli_query($dbc, "SELECT `rate_card` FROM `estimate_scope` WHERE `estimateid`='$estimateid' GROUP BY `rate_card`");
if(mysqli_num_rows($query) > 0) {
	while($row = mysqli_fetch_array($query)) {
		$rates[bin2hex($row[0])] = explode(':',$row[0]);
	}
} else {
	$rates[''] = '';
}
$current_rate = (!empty($_GET['rate']) ? $_GET['rate'] : key($rates));
if(isset($_POST['submit'])) {
	print_r($_POST);
	$note = 0;
	$misc = 0;
	$scope_name = filter_var($_POST['scope_name'],FILTER_SANITIZE_STRING);

	foreach($_POST['item'] as $i => $row) {
		$row = explode('|',$row);
		$id = $row[0];
		$type = filter_var($row[1],FILTER_SANITIZE_STRING);
		$value = filter_var($row[2],FILTER_SANITIZE_STRING);
		$product_pricing = filter_var($row[3],FILTER_SANITIZE_STRING);
		if($type == 'notes') {
			$value = filter_var(htmlentities($_POST['note_item'][$note++]),FILTER_SANITIZE_STRING);
		} else if($type == 'miscellaneous') {
			$value = filter_var($_POST['misc_item'][$misc++],FILTER_SANITIZE_STRING);
		}
		$heading = filter_var($_POST['heading'][$i],FILTER_SANITIZE_STRING);
		if($id > 0) {
			$before_change = capture_before_change($dbc, 'estimate_scope', 'heading', 'id', $id);
			$dbc->query("UPDATE `estimate_scope` SET `heading`='$heading', `sort_order`='$i' WHERE `id`='$id'");
			$history = capture_after_change('heading', $heading);
			add_update_history($dbc, 'estimates_history', $history, '', $before_change);

		} else if(!empty($value)) {
			if($value > 0) {
				$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='$type' AND `tile_name`!='miscellaneous' AND `item_id`='$value' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') ORDER BY `rate_card_name` != '$rate_name'");
				if(mysqli_num_rows($general) == 0 && $type == 'clients') {
					$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='clients' AND `description`='".get_contact($dbc, $value)."' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
				} else if(mysqli_num_rows($general) == 0 && $type == 'equipment') {
					$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='equipment' AND `description` IN (SELECT `unit_number` FROM `equipment` WHERE `equipmentid`='$value') AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')
						UNION SELECT * FROM `equipment_rate_table` WHERE `deleted`=0 AND `equipment_id`='$value' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')
						UNION SELECT * FROM `category_rate_table` WHERE `deleted`=0 AND `category` IN (SELECT `category` FROM `equipment` WHERE `equipmentid`='$value') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
				} else if(mysqli_num_rows($general) == 0 && $type == 'inventory') {
					$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='inventory' AND `description` IN (SELECT `product_name` FROM `inventory` WHERE `inventoryid`='$value') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($general) == 0) {
						$general = mysqli_query($dbc, "SELECT '' `uom`, 1 `qty`, `$inv_cost_field` `cost`, '' `profit`, '' `margin`, '' `cust_price`, '' `retail_rate` FROM `inventory` WHERE `inventoryid`='$value'");
					}
				} else if(mysqli_num_rows($general) == 0 && $type == 'labour') {
					$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='labour' AND `description` IN (SELECT `heading` FROM `labour` WHERE `labourid`='$value') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
				} else if(mysqli_num_rows($general) == 0 && $type == 'material') {
					$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='material' AND `description` IN (SELECT `name` FROM `material` WHERE `materialid`='$value') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
				} else if(mysqli_num_rows($general) == 0 && $type == 'position') {
					$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='position' AND `description` IN (SELECT `name` FROM `positions` WHERE `position_id`='$value') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')
						UNION SELECT * FROM `position_rate_table` WHERE `position_id`='$value' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
				} else if(mysqli_num_rows($general) == 0 && $type == 'products') {
					$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='products' AND `description` IN (SELECT CONCAT(`category`,' ',`heading`) FROM `products` WHERE `productid`='$value') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
				} else if(mysqli_num_rows($general) == 0 && $type == 'services') {
					$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='services' AND `description` IN (SELECT CONCAT(`category`,' ',`heading`) FROM `services` WHERE `serviceid`='$value') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')
						UNION SELECT * FROM `service_rate_card` WHERE `serviceid`='$value' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
				} else if(mysqli_num_rows($general) == 0 && $type == 'staff') {
					$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='staff' AND `description`='".get_contact($dbc, $value)."' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
				} else if(mysqli_num_rows($general) == 0 && $type == 'vpl' && !empty($product_pricing)) {
					$general = mysqli_query($dbc, "SELECT `$product_pricing` `cost` FROM `vendor_price_list` WHERE `inventoryid` = '$value'");
				}
				$general = mysqli_fetch_array($general);
				if($product_pricing == 'usd_cpu') {
					$exchange_rate_list = json_decode(file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'), TRUE);
					$exchange_rate = $exchange_rate_list['observations'][count($exchange_rate_list['observations']) - 1]['FXUSDCAD']['v'];
					$general['cost'] = $general['cost'] * $exchange_rate;
				}
				$cost = $general['cost'];
			} else {
				$cost = 0;
			}
			$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`".(in_array($type,['notes','miscellaneous']) ? 'description' : 'src_id')."`,`cost`,`rate_card`,`sort_order`,`pricing`) VALUES ('$estimateid', '$scope_name','$heading','$type','$value','$cost','$current_rate','$i','$product_pricing')");
			$before_change = '';
			$history = "Estimates scope entry has been added. <br />";
			add_update_history($dbc, 'estimates_history', $history, '', $before_change);
		}
	}
	echo "<script>window.top.location.reload(); window.location.replace('../blank_loading_page.php');</script>";
}
include_once('../Rate Card/line_types.php'); ?>
<script>
$(document).ready(function() {
	setSort();
});
var tile_items = [];
var categories = [];
var item_types = [];
<?php $categories = [];
$item_types = [];
foreach($src_options as $option) {
	if(!isset($categories[$option['tile_name']])) {
		echo "item_types['".$option['tile_name']."'] = [];\n";
		echo "categories['".$option['tile_name']."'] = [];\n";
		echo "tile_items['".$option['tile_name']."'] = [];\n";
		$categories[$option['tile_name']] = [];
	}
	if($option['category'] != '') {
		if(!in_array($option['category'],$categories[$option['tile_name']])) {
			echo "categories['".$option['tile_name']."'].push('".$option['category']."');\n";
			$categories[$option['tile_name']][] = $option['category'];
		}
	}
	if($option['type'] != '') {
		if(!in_array($option['type'],$item_types[$option['tile_name']])) {
			echo "item_types['".$option['tile_name']."'].push(['".$option['type']."','".$option['category']."']);\n";
			$item_types[$option['tile_name']][] = $option['type'];
		}
	}
	$option_data = json_encode($option);
	if($option_data != '') {
		echo "tile_items['".$option['tile_name']."'][".$option['id']."] = ".$option_data.";\n";
	}
} ?>
function add_item(btn) {
	var type = $(btn).data('type');
	var block = $(btn).next();
	var object = '<div class="col-sm-12">';
	if(type == 'miscellaneous') {
		object = object+'<h4>Miscellaneous<img class="pull-right cursor-hand line-handle inline-img" src="../img/icons/drag_handle.png"></h4><input type="hidden" name="item[]" value="|miscellaneous|"><input type="text" class="form-control" name="misc_item[]">';
	} else if(type == 'notes') {
		object = object+'<h4>Notes<img class="pull-right cursor-hand line-handle inline-img" src="../img/icons/drag_handle.png"></h4><input type="hidden" name="item[]" value="|notes|"><textarea name="note_item[]"></textarea>';
	} else {
		if(type != 'position') {
			object = object+(type == 'services' ? '<div class="col-sm-4">' : '<div class="col-sm-5">')+'<select name="category" class="chosen-select-deselect form-control" name="category" data-type="'+type+'" onchange="fill_select(this);"><option></option></select></div>';
		}
		if(type == 'services') {
			object = object+'<div class="col-sm-3"><select name="item_type" class="chosen-select-deselect form-control" data-type="'+type+'" name="type" onchange="fill_select(this);"><option></option></select></div>';
		}
		object = object+(type != 'position' ? (type == 'services' ? '<div class="col-sm-4">' : '<div class="col-sm-6">') : '<div class="col-sm-11">')+'<select name="item[]" class="chosen-select-deselect form-control" data-type="'+type+'"><option></option></select></div><img class="pull-right cursor-hand line-handle inline-img" src="../img/icons/drag_handle.png">';
	}
	object = object+'<input type="hidden" name="heading[]" value="'+$('.heading_value').first().text()+'"><div class="clearfix"></div><hr /></div>';
	block.append(object);
	setSort();
	var object = block.find('.col-sm-12').last();
	var selects = object.find('select');
	if(selects.length > 0) {
		fill_select(selects.get(0));
	}
	object.find('input[type=text],select,textarea').first().focus();
	block.show();
}
function setSort() {
	$('.block-item').sortable({
		handle: '.line-handle',
		items: '.col-sm-12'
	});
	initInputs();
}
function fill_select(select) {
	var block = $(select).closest('.col-sm-12');
	if($(select).find('option[value]').length == 0 && select.name == 'category') {
		categories[$(select).data('type')].forEach(function(str) {
			$(select).append('<option value="'+str+'">'+str+'</option>');
		});
		$(select).trigger('change.select2');
	} else if($(select).find('option[value]').length == 0) {
		categories[$(select).data('type')].forEach(function(str) {
			$(select).append('<option value="'+str+'">'+str+'</option>');
		});
		$(select).trigger('change.select2');
	} else if(select.value != '' && select.name == 'category' && $(select).data('type') == 'services') {
		block.find('[name=item_type]').empty().append('<option />');
		item_types[$(select).data('type')].forEach(function(arr) {
			if(arr[1] == select.value) {
				block.find('[name=item_type]').append('<option value="'+arr[0]+'">'+arr[0]+'</option>');
			}
		});
		block.find('[name=item_type]').trigger('change.select2');
	} else if(select.value != '' && select.name == 'type') {
		block.find('[name=item_type]').empty().append('<option />');
		categories[$(select).data('type')].forEach(function(arr) {
			if(arr[1] == select.value) {
				block.find('[name=item_type]').append('<option value="'+arr[0]+'">'+arr[0]+'</option>');
			}
		});
		block.find('[name=item_type]').trigger('change.select2');
	} else if(select.value != '') {
		block.find('[name="item[]"]').empty().append('<option />');
		var cat = block.find('[name=category]').val();
		var type = block.find('[name=item_type]').val();
		tile_items[$(select).data('type')].forEach(function(obj) {
			if(obj.category == cat && (type == undefined || obj.type == type)) {
				block.find('[name="item[]"]').append('<option value="|'+$(select).data('type')+'|'+obj.id+'">'+obj.label+'</option>');
			}
		});
		block.find('[name="item[]"]').trigger('change.select2');
	}
}
function setHeading(input) {
	$(input).closest('h3').find('*').show().filter('span').text(input.value);
	$(input).hide();
	$(input).closest('h3').nextAll('.block-item').first().find('[name="heading[]"]').val(input.value);
}
$(document).on('change', 'select[name="prior_business"]', function() { filterPrior(); });
$(document).on('change', 'select[name="prior_contact"]', function() { filterPrior(); });
$(document).on('change', 'select[name="prior_site"]', function() { filterPrior(); });
function filterPrior() {
	$('[name=prior_estimate]').find('option').show();
	if($('[name=prior_business]').val() != '') {
		$('[name=prior_estimate]').find('option').filter('[data-businessid]:not([data-businessid='+$('[name=prior_business]').val()+'])').hide();
	}
	if($('[name=prior_contact]').val() != '') {
		$('[name=prior_estimate]').find('option').filter('[data-clientid]:not([data-clientid='+$('[name=prior_contact]').val()+'])').hide();
	}
	if($('[name=prior_site]').val() != '') {
		$('[name=prior_estimate]').find('option').filter('[data-siteid]:not([data-siteid='+$('[name=prior_site]').val()+'])').hide();
	}
	$('[name=prior_estimate]').trigger('change.select2');
}
function setIncluded(input) {
	var block = $(input).closest('.col-sm-12');
	if(input.checked) {
		block.find('[type=text],textarea,[type=hidden]').prop('disabled',false);
	} else {
		block.find('[type=text],textarea,[type=hidden]').prop('disabled',true);
	}
}
</script>
<form class="col-sm-12 form-horizontal" action="" method="POST" enctype="multipart/form-data">
	<h2><?= !empty($_GET['src']) ? 'Load Scope Details' : 'Edit Scope' ?></h2><a class="pull-right" href="../blank_loading_page.php"><img class="slider-close" src="../img/icons/cancel.png"></a><br />
	<?php if(!empty($_GET['src'])) { ?>
		<div class="form-group">
			<div class="col-sm-5">
				<div class="form-group">
					<label class="col-sm-4">Load Scope Template:</label>
					<div class="col-sm-8">
						<select name="scope_template" class="chosen-select-deselect" onchange="window.location.replace('?estimateid=<?= $_GET['estimateid'] ?>&scope=<?= $_GET['scope'] ?>&mode<?= $_GET['mode'] ?>=&src=<?= $_GET['src'] ?>&templateid='+this.value+'&rate=<?= $_GET['rate'] ?>');">
							<option></option>
							<?php $templates = mysqli_query($dbc, "SELECT `id`, `template_name` FROM `estimate_templates` WHERE `deleted` = 0 ORDER BY `template_name`");
							while($template = mysqli_fetch_array($templates)) { ?>
								<option <?= $_GET['templateid'] == $template['id'] ? 'selected' : '' ?> value="<?= $template['id'] ?>"><?= $template['template_name'] ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
			<div class="col-sm-1">OR</div>
			<div class="col-sm-6">
				<?php if(in_array('Business',$config)) { ?>
					<div class="form-group">
						<label class="col-sm-4">Business:</label>
						<div class="col-sm-8">
							<select name="prior_business" class="chosen-select-deselect">
								<option></option>
								<?php $prior_bus = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `contactid` IN (SELECT `businessid` FROM `estimate` WHERE `deleted`=0)"));
								foreach($prior_bus as $business) { ?>
									<option value="<?= $business['contactid'] ?>"><?= $business['name'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('Contact',$config)) { ?>
					<div class="form-group">
						<label class="col-sm-4">Contact:</label>
						<div class="col-sm-8">
							<select name="prior_contact" class="chosen-select-deselect">
								<option></option>
								<?php $prior_contacts = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid` IN (SELECT `clientid` FROM `estimate` WHERE `deleted`=0)"));
								foreach($prior_contacts as $contact) { ?>
									<option value="<?= $contact['contactid'] ?>"><?= $contact['first_name'].' '.$contact['last_name'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('Site',$config)) { ?>
					<div class="form-group">
						<label class="col-sm-4">Site:</label>
						<div class="col-sm-8">
							<select name="prior_site" class="chosen-select-deselect">
								<option></option>
								<?php $prior_sites = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `site_name` FROM `contacts` WHERE `contactid` IN (SELECT `siteid` FROM `estimate` WHERE `deleted`=0)"));
								foreach($prior_sites as $site) { ?>
									<option value="<?= $site['contactid'] ?>"><?= $site['site_name'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<div class="form-group">
					<label class="col-sm-4">Load <?= rtrim(ESTIMATE_TILE, 's') ?> Scope:</label>
					<div class="col-sm-8">
						<select name="prior_estimate" class="chosen-select-deselect" onchange="window.location.replace('?estimateid=<?= $_GET['estimateid'] ?>&scope=<?= $_GET['scope'] ?>&mode<?= $_GET['mode'] ?>=&src=<?= $_GET['src'] ?>&priorid='+this.value+'&rate=<?= $_GET['rate'] ?>');">
							<option></option>
							<?php $priors = mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `deleted`=0");
							while($prior = mysqli_fetch_array($priors)) { ?>
								<option <?= $priorid == $prior['estimateid'] ? 'selected' : '' ?> data-businessid="<?= $prior['businessid'] ?>" data-clientid="<?= $prior['clientid'] ?>" data-siteid="<?= $prior['siteid'] ?>" value="<?= $prior['estimateid'] ?>"><?= $prior['estimate_name'] ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
	<div class="form-group">
		<label class="col-sm-4">Rate Card:</label>
		<div class="col-sm-8">
			<select name="scope_rate" class="chosen-select-deselect" onchange="window.location.replace('?estimateid=<?= $_GET['estimateid'] ?>&scope=<?= $_GET['scope'] ?>&mode<?= $_GET['mode'] ?>=&src=<?= $_GET['src'] ?>&templateid=<?= $_GET['templateid'] ?>&priorid=<?= $_GET['priorid'] ?>&rate='+this.value);">
				<option></option>
				<?php $rate_list = mysqli_query($dbc, "SELECT MIN(`companyrcid`) id, CONCAT(`rate_card_name`,IF(`rate_card_types`!='',CONCAT(': ',`rate_card_types`),'')) rate_name FROM `company_rate_card` WHERE `deleted`=0 AND `rate_card_name`!='' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `rate_name` ORDER BY `rate_name`");
				while($rate = mysqli_fetch_array($rate_list)) { ?>
					<option <?= $current_rate == 'COMPANY:'.$rate['id'] ? 'selected' : '' ?> value="COMPANY:<?= $rate['id'] ?>">Company Rate: <?= $rate['rate_name'] ?></option>
				<?php } ?>
				<?php $rate_list = mysqli_query($dbc, "SELECT `rate_card_estimate_scopes`.`id`, `rate_card_estimate_scopes`.`template_id`, CONCAT(`estimate_templates`.`template_name`,': ',`rate_card_estimate_scopes`.`rate_card_name`) rate_name FROM `rate_card_estimate_scopes` LEFT JOIN `estimate_templates` ON `rate_card_estimate_scopes`.`template_id`=`estimate_templates`.`id` WHERE `rate_card_estimate_scopes`.`deleted`=0 AND `estimate_templates`.`deleted`=0");
				while($rate = mysqli_fetch_array($rate_list)) { ?>
					<option <?= $current_rate == 'SCOPE:'.$rate['id'] ? 'selected' : '' ?> data-template="<?= $rate['template_id'] ?>" value="SCOPE:<?= $rate['id'] ?>">Scope Rate: <?= $rate['rate_name'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>

	<?php $heading_order = explode('#*#', get_config($dbc, 'estimate_field_order'));
	if(in_array('Scope Detail',$config) && !in_array_starts('Detail',$heading_order)) {
		$heading_order[] = 'Detail***Scope Detail';
	}
	if(in_array('Scope Billing',$config) && !in_array_starts('Billing Frequency',$heading_order)) {
		$heading_order[] = 'Billing Frequency***Billing Frequency';
	}
	$value_config = explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM `field_config_estimate`"))[0]);
	$scope_name = '';
	$query = mysqli_query($dbc, "SELECT * FROM (SELECT `scope_name` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `src_table` != '' AND `deleted`=0 GROUP BY `scope_name` ORDER BY MIN(`sort_order`)) `scopes` UNION SELECT 'Scope 1' `scope_name`");
	while($row = mysqli_fetch_array($query)) {
		if((empty($_GET['scope']) && empty($scope_name)) || $_GET['scope'] == config_safe_str($row[0])) {
			$scope_name = $row[0];
		}
	} ?>

    <div class="form-group">
        <label class="col-sm-4">Scope Name:</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" name="scope_name" data-table="estimate" data-id-field="estimateid" value="<?= $scope_name ?>">
        </div>
    </div>

	<?php $_GET['rate'] = $current_rate;
	if($_GET['templateid'] > 0) {
		$query = mysqli_query($dbc, "SELECT `heading_name`, `id` FROM `estimate_template_headings` WHERE `template_id`='{$_GET['templateid']}' AND `deleted`=0 ORDER BY `sort_order`");
	} else if($_GET['priorid'] > 0) {
		$query = mysqli_query($dbc, "SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='{$_GET['priorid']}' AND `rate_card`='".implode(':',$rates[$current_rate])."' AND `src_table` != '' AND (`src_id` > 0 OR `description` != '') AND `scope_name`='$scope_name' AND `deleted`=0 GROUP BY `heading` ORDER BY MIN(`sort_order`)");
	} else {
		$query = mysqli_query($dbc, "SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `rate_card`='".implode(':',$rates[$current_rate])."' AND `src_table` != '' AND (`src_id` > 0 OR `description` != '') AND `scope_name`='$scope_name' AND `deleted`=0 GROUP BY `heading` ORDER BY MIN(`sort_order`)");
	}
	$heading_ids = [];
	if(mysqli_num_rows($query) > 0) {
		while($row = mysqli_fetch_array($query)) {
			$headings[config_safe_str($row[0])] = $row[0];
			$heading_ids[config_safe_str($row[0])] = $row[1];
		}
	} else {
		$headings['scope'] = 'Scope';
	}

	$i = 0;
	foreach($headings as $heading_str => $heading) {
		echo "<h3><span class='heading_name'>$heading</span>";
		echo '<img class="inline-img small" src="../img/icons/ROOK-edit-icon.png" onclick="$(this).closest(\'h3\').find(\'*\').hide().filter(\'input\').show().focus();">';
		echo '<input type="text" class="form-control" style="display:none;" name="heading_value" value="'.$heading.'" onblur="setHeading(this);"></h3>';
        echo '<div class="clearfix"></div>';
		if($_GET['templateid'] > 0) {
			$lines = mysqli_query($dbc, "SELECT * FROM `estimate_template_lines` WHERE `heading_id`='{$heading_ids[$heading_str]}' AND `deleted`=0 ORDER BY `sort_order`");
		} else if($_GET['priorid'] > 0) {
			$lines = mysqli_query($dbc, "SELECT * FROM `estimate_scope` WHERE `estimateid`='$priorid' AND `estimateid` > 0 AND `rate_card`='".implode(':',$rates[$current_rate])."' AND `heading`='$heading' AND `src_table` != '' AND (`src_id` > 0 OR `description` != '') AND `scope_name`='$scope_name' AND `deleted`=0 ORDER BY `sort_order`");
		} else {
			$lines = mysqli_query($dbc, "SELECT * FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `estimateid` > 0 AND `rate_card`='".implode(':',$rates[$current_rate])."' AND `heading`='$heading' AND `src_table` != '' AND (`src_id` > 0 OR `description` != '') AND `scope_name`='$scope_name' AND `deleted`=0 ORDER BY `sort_order`");
		}
		echo '<div class="col-sm-12 block-item gap-top gap-bottom">';
		while($line = mysqli_fetch_array($lines)) {
			echo '<div class="col-sm-12">';
			echo '<img class="pull-right cursor-hand line-handle inline-img" src="../img/icons/drag_handle.png">';
			if($_GET['templateid'] > 0 || $_GET['priorid'] > 0) {
				echo '<label class="form-checkbox pull-right any-width"><input type="checkbox" name="item[]" checked value="|'.$line['src_table'].'|'.$line['src_id'].'|'.$line['product_pricing'].'" onchange="setIncluded(this);">Include</label><input type="hidden" name="heading[]" value="'.$heading.'">';
			} else {
				echo '<input type="hidden" name="item[]" value="'.$line['id'].'|'.$line['src_table'].'|'.$line['src_id'].'"><input type="hidden" name="heading[]" value="'.$heading.'">';
			}
			$cost = $line['price'];
			if($line['src_table'] == 'miscellaneous') {
				echo '<h4>Miscellaneous</h4><input type="text" class="form-control" name="misc_item[]" value="'.$line['description'].'">';
			} else if($line['src_table'] == 'notes') {
				echo '<h4>Notes</h4><textarea name="note_item[]">'.$line['description'].'</textarea>';
			} else {
				if($line['src_table'] == 'vpl' && !empty($line['product_pricing'])) {
					$cost = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `".$line['product_pricing']."` FROM `vendor_price_list` WHERE `inventoryid` = '".$line['src_id']."'"))[$line['product_pricing']];
				}
				foreach($tiles as $label => $tile_name) {
					if($tile_name == $line['src_table']) {
						echo $label.': ';
					}
				}
				foreach($src_options as $option) {
					if($option['tile_name'] == $line['src_table'] && $option['id'] == $line['src_id']) {
						echo $option['label'];
						if(!($cost > 0)) {
							$cost = $line['price'];
						}
					}
				}
			}
			if(!($cost > 0)) {
				$cost = 0;
			}
			echo '<span class="pull-right">'.round($line['qty'],3).' @ $'.number_format($cost,2).($line['product_pricing'] == 'usd_cpu' ? ' USD' : '').'</span>';
			echo '<hr /></div>';
		}
		echo '</div>';
		$i++;
	} ?>
	<div class="clearfix pad-vertical"></div>
	<a class="btn brand-btn pull-left" href="../blank_loading_page.php">Cancel</a>
	<button class="btn brand-btn pull-right" type="Submit" name="submit" value="submit">Apply to Scope</button>
</form>
