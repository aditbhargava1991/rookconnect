<?php include_once('../include.php');
checkAuthorised('estimate');
$estimateid = 0;
if($_GET['estimateid'] > 0) {
	$estimateid = $_GET['estimateid'];
}
$scope_name = '';
$scope_names = $dbc->query("SELECT `scope_name` FROM `estimate_scope` WHERE `deleted`=0 AND `estimateid`='$estimateid' AND IFNULL(`scope_name`,'') != '' GROUP BY `scope_name` UNION SELECT 'Scope 1' `scope_name`");
while($scope_name_row = $scope_names->fetch_array()[0]) {
	if(config_safe_str($scope_name_row) == $_GET['scope']) {
		$scope_name = $scope_name_row;
	}
}
$heading = '';
$headings = $dbc->query("SELECT `heading` FROM `estimate_scope` WHERE `deleted`=0 AND `estimateid`='$estimateid' AND `scope_name`='$scope_name' AND IFNULL(`heading`,'') != '' GROUP BY `heading` UNION SELECT 'Heading 1' `heading`");
while($heading_row = $headings->fetch_array()[0]) {
	if((empty($_GET['heading']) && $heading_row == 'Heading 1' && empty($heading)) || config_safe_str($heading_row) == $_GET['heading'] || $heading_row == $_GET['heading']) {
		$heading = $heading_row;
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
	$scope_name = filter_var($_POST['scope_name'],FILTER_SANITIZE_STRING);
	$heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
    $date_of_archival = date('Y-m-d');
	$dbc->query("UPDATE `estimate_scope` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `estimateid`='$estimateid' AND `scope_name`='$scope_name' AND `heading`='$heading' AND IFNULL(`src_table`,'')=''");
	$before_change = '';
	$history = "Estimates scope entry has been updated for scope name $scope_name. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);

	if($_GET['type'] == 'vpl') {
		$pricing = filter_var($_POST['productpricing'],FILTER_SANITIZE_STRING);
		$exchange_rate = 1;
		if($pricing == 'usd_cpu') {
			$exchange_rate_list = json_decode(file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'), TRUE);
			$exchange_rate = $exchange_rate_list['observations'][count($exchange_rate_list['observations']) - 1]['FXUSDCAD']['v'];
		}
        foreach($_POST['inventoryid'] as $i => $value) {
        	$cost = $_POST['vpl_price'][$i];
        	$qty = $_POST['vpl_quantity'][$i];
			$retail = $cost * $exchange_rate * $qty;
        	if($value > 0 && $qty > 0) {
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`price`,`pricing`,`qty`,`retail`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','vpl','$value','$cost','".($pricing == 'usd_cpu' ? 0 : $cost)."','$pricing','$qty','$retail','$i')");
				$before_change = '';
				$history = "Estimates scope entry has been added. <br />";
				add_update_history($dbc, 'estimates_history', $history, '', $before_change);
			}
		}
	} else if($_GET['type'] == 'inventory') {
        foreach($_POST['inventoryid'] as $i => $value) {
        	$cost = $_POST['cost'][$i];
        	$price = $_POST['price'][$i];
        	$qty = $_POST['qty'][$i];
			$retail = $price * $qty;
        	if($value > 0 && $qty > 0) {
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`price`,`qty`,`retail`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','inventory','$value','$cost','$price','$qty','$retail','$i')");
			}
		}
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	} else if($_GET['type'] == 'services') {
        foreach($_POST['serviceid'] as $i => $value) {
        	$cost = $_POST['cost'][$i];
        	$price = $_POST['price'][$i];
        	$qty = $_POST['qty'][$i];
			$retail = $price * $qty;
        	if($value > 0 && $qty > 0) {
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`price`,`qty`,`retail`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','services','$value','$cost','$price','$qty','$retail','$i')");
			}
		}
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	} else if($_GET['type'] == 'staff') {
        foreach($_POST['staff_id'] as $i => $value) {
        	$cost = $_POST['cost'][$i];
        	$price = $_POST['price'][$i];
        	$qty = $_POST['qty'][$i];
			$retail = $price * $qty;
        	if($value > 0 && $qty > 0) {
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`price`,`qty`,`retail`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','staff','$value','$cost','$price','$qty','$retail','$i')");
			}
		}
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	} else if($_GET['type'] == 'products') {
        foreach($_POST['product_id'] as $i => $value) {
        	$cost = $_POST['cost'][$i];
        	$price = $_POST['price'][$i];
        	$qty = $_POST['qty'][$i];
			$retail = $price * $qty;
        	if($value > 0 && $qty > 0) {
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`price`,`qty`,`retail`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','products','$value','$cost','$price','$qty','$retail','$i')");
			}
		}
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	} else if($_GET['type'] == 'labour') {
        foreach($_POST['labour_id'] as $i => $value) {
        	$cost = $_POST['cost'][$i];
        	$price = $_POST['price'][$i];
        	$qty = $_POST['qty'][$i];
			$retail = $price * $qty;
        	if($value > 0 && $qty > 0) {
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`price`,`qty`,`retail`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','labour','$value','$cost','$price','$qty','$retail','$i')");
			}
		}
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	} else if($_GET['type'] == 'material') {
        foreach($_POST['material_id'] as $i => $value) {
        	$cost = $_POST['cost'][$i];
        	$price = $_POST['price'][$i];
        	$qty = $_POST['qty'][$i];
			$retail = $price * $qty;
        	if($value > 0 && $qty > 0) {
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`price`,`qty`,`retail`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','material','$value','$cost','$price','$qty','$retail','$i')");
			}
		}
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	} else if($_GET['type'] == 'position') {
        foreach($_POST['position_id'] as $i => $value) {
        	$cost = $_POST['cost'][$i];
        	$price = $_POST['price'][$i];
        	$qty = $_POST['qty'][$i];
			$retail = $price * $qty;
        	if($value > 0 && $qty > 0) {
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`price`,`qty`,`retail`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','position','$value','$cost','$price','$qty','$retail','$i')");
			}
		}
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	} else if($_GET['type'] == 'equipment') {
        foreach($_POST['equipment_id'] as $i => $value) {
        	$cost = $_POST['cost'][$i];
        	$price = $_POST['price'][$i];
        	$qty = $_POST['qty'][$i];
			$retail = $price * $qty;
        	if($value > 0 && $qty > 0) {
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`price`,`qty`,`retail`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','equipment','$value','$cost','$price','$qty','$retail','$i')");
			}
		}
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	} else if($_GET['type'] == 'clients') {
        foreach($_POST['client_id'] as $i => $value) {
        	$cost = $_POST['cost'][$i];
        	$price = $_POST['price'][$i];
        	$qty = $_POST['qty'][$i];
			$retail = $price * $qty;
        	if($value > 0 && $qty > 0) {
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`price`,`qty`,`retail`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','clients','$value','$cost','$price','$qty','$retail','$i')");
			}
		}
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	} else {
		$note = 0;
		$misc = 0;
		$type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);

		foreach($_POST['item'] as $i => $row) {
			if($type == 'notes') {
				$value = filter_var(htmlentities($row),FILTER_SANITIZE_STRING);
				$id = $_POST['id'][$i];
				if($id > 0) {
					$before_change = capture_before_change($dbc, 'estimate_scope', 'estimateid', 'id', $id);
					$before_change .= capture_before_change($dbc, 'estimate_scope', 'scope_name', 'id', $id);
					$before_change .= capture_before_change($dbc, 'estimate_scope', 'heading', 'id', $id);
					$before_change .= capture_before_change($dbc, 'estimate_scope', 'description', 'id', $id);
					$before_change .= capture_before_change($dbc, 'estimate_scope', 'rate_car', 'id', $id);
					$dbc->query("UPDATE `estimate_scope` SET `estimateid`='$estimateid', `scope_name`='$scope_name', `heading`='$heading',`description`='$value',`rate_card`='$rate_card' WHERE `id`='$id'");
					$history = capture_after_change('estimateid', $estimateid);
					$history .= capture_after_change('scope_name', $scope_name);
					$history .= capture_after_change('heading', $heading);
					$history .= capture_after_change('description', $value);
					$history .= capture_after_change('rate_card', $rate_card);
					add_update_history($dbc, 'estimates_history', $history, '', $before_change);

				} else {
					$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`description`,`rate_card`) VALUES ('$estimateid', '$scope_name','$heading','$type','$value','$current_rate')");
					$before_change = '';
					$history = "Estimates scope entry has been added. <br />";
					add_update_history($dbc, 'estimates_history', $history, '', $before_change);
				}
			} else if($type == 'miscellaneous') {
				$value = filter_var($row,FILTER_SANITIZE_STRING);
				$cost = filter_var($_POST['cost'][$i],FILTER_SANITIZE_STRING);
				$qty = filter_var($_POST['qty'][$i],FILTER_SANITIZE_STRING);
				$price = filter_var($_POST['price'][$i],FILTER_SANITIZE_STRING);
				$id = $_POST['id'][$i];
				if($id > 0) {
					$dbc->query("UDPATE `estimate_scope` SET `estimateid`='$estimateid', `scope_name`='$scope_name', `heading`='$heading',`description`='$description',`cost`='$cost',`qty`='$qty',`price`='$price',`rate_card`='$rate_card' WHERE `id`='$id'");
				} else {
					$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`description`,`cost`,`qty`,`price`,`rate_card`) VALUES ('$estimateid', '$scope_name','$heading','$type','$value','$cost','$qty','$price','$current_rate')");
					$before_change = '';
					$history = "Estimates scope entry has been added. <br />";
					add_update_history($dbc, 'estimates_history', $history, '', $before_change);
				}
			} else {
				$value = filter_var($row,FILTER_SANITIZE_STRING);
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
					}
					$general = mysqli_fetch_array($general);
					$cost = $general['cost'];
				} else {
					$cost = 0;
				}
				$dbc->query("INSERT INTO `estimate_scope` (`estimateid`, `scope_name`, `heading`,`src_table`,`src_id`,`cost`,`rate_card`,`sort_order`) VALUES ('$estimateid', '$scope_name','$heading','$type','$value','$cost','$current_rate','$i')");
				$before_change = '';
				$history = "Estimates scope entry has been added. <br />";
				add_update_history($dbc, 'estimates_history', $history, '', $before_change);
			}
		}
	}
	echo "<script>window.location.replace('../blank_loading_page.php');</script>";
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
	<input type="hidden" name="scope_name" value="<?= $scope_name ?>">
	<input type="hidden" name="heading" value="<?= $heading ?>">
	<h2>Add Scope Details</h2><a class="pull-right" href="../blank_loading_page.php"><img class="slider-close" src="../img/icons/cancel.png"></a><br />
    <div class="form-group">
        <label class="col-sm-4">Scope Name:</label>
        <div class="col-sm-8">
            <?= $scope_name ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4">Heading:</label>
        <div class="col-sm-8">
            <?= $heading ?>
        </div>
    </div>

	<input type="hidden" name="estimateid" value="<?= $estimateid ?>">
	<input type="hidden" name="scope_name" value="<?= $scope_name ?>">
	<input type="hidden" name="heading" value="<?= $heading ?>">
	<input type="hidden" name="type" value="<?= $$_GET['type'] ?>">

	<?php $heading_order = explode('#*#', get_config($dbc, 'estimate_field_order'));
	if(in_array('Scope Detail',$config) && !in_array_starts('Detail',$heading_order)) {
		$heading_order[] = 'Detail***Scope Detail';
	}
	if(in_array('Scope Billing',$config) && !in_array_starts('Billing Frequency',$heading_order)) {
		$heading_order[] = 'Billing Frequency***Billing Frequency';
	}
	$value_config = explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM `field_config_estimate`"))[0]); ?>

    <div class="form-group">
        <label class="col-sm-4">Detail:</label>
        <div class="col-sm-8">
            <select class="chosen-select-deselect" name="type" data-placeholder="Select Type" onchange="window.location.replace('?estimateid=<?= $_GET['estimateid'] ?>&scope=<?= $_GET['scope'] ?>&heading=<?= $_GET['heading'] ?>&type='+this.value);"><option />
				<?php foreach(array_reverse($tiles,true) as $label => $name) {
					if(in_array('Scope Item '.$name,$value_config) || !in_array_starts('Scope Item ',$value_config)) {
						echo '<option '.($_GET['type'] == $name ? 'selected' : '').' value="'.$name.'">'.$label.'</option>';
					}
				} ?>
			</select>
        </div>
    </div>

	<?php switch($_GET['type']) {
		case 'vpl':
			include('estimate_scope_add_vpl.php');
			break;
		case 'notes':
			$notes = $dbc->query("SELECT `description`, `id` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `scope_name`='$scope_name' AND `heading`='$heading' AND `deleted`=0 AND `src_table`='notes'");
			while($note = $notes->fetch_assoc()) { ?>
				<div class="form-group">
					<label class="col-sm-12">Note:</label>
					<input type="hidden" name="id[]" value="<?= $note['id'] ?>">
					<div class="col-sm-12"><textarea name="item[]"><?= html_entity_decode($note['description']) ?></textarea></div>
				</div>
			<?php } ?>
			<div class="form-group">
				<label class="col-sm-12">Note:</label>
				<input type="hidden" name="id[]" value="">
				<div class="col-sm-12"><textarea name="item[]"></textarea></div>
			</div>
			<?php break;
		case 'miscellaneous':
			$misc_items = $dbc->query("SELECT `description`, `id` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `scope_name`='$scope_name' AND `heading`='$heading' AND `deleted`=0 AND `src_table`='miscellaneous'");
			while($misc = $misc_items->fetch_assoc()) { ?>
				<div class="form-group">
					<input type="hidden" name="id[]" value="<?= $misc['id'] ?>">
					<div class="form-group">
						<label class="col-sm-4">Item:</label>
						<div class="col-sm-8"><input type="text" name="item[]" value="<?= html_entity_decode($misc['description']) ?>" class="form-control"></div>
					</div>
					<div class="form-group">
						<label class="col-sm-4">Cost:</label>
						<div class="col-sm-8"><input type="number" name="cost[]" value="<?= html_entity_decode($misc['cost']) ?>" class="form-control"></div>
					</div>
					<div class="form-group">
						<label class="col-sm-4">Quantity:</label>
						<div class="col-sm-8"><input type="number" name="qty[]" value="<?= html_entity_decode($misc['qty']) ?>" class="form-control"></div>
					</div>
					<div class="form-group">
						<label class="col-sm-4">Price:</label>
						<div class="col-sm-8"><input type="number" name="price[]" value="<?= html_entity_decode($misc['price']) ?>" class="form-control"></div>
					</div>
				</div>
			<?php } ?>
			<div class="form-group">
				<input type="hidden" name="id[]" value="<?= $misc['id'] ?>">
				<div class="form-group">
					<label class="col-sm-4">Item:</label>
					<div class="col-sm-8"><input type="text" name="item[]" value="" class="form-control"></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4">Cost:</label>
					<div class="col-sm-8"><input type="number" name="cost[]" value="" class="form-control"></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4">Quantity:</label>
					<div class="col-sm-8"><input type="number" name="qty[]" value="" class="form-control"></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4">Price:</label>
					<div class="col-sm-8"><input type="number" name="price[]" value="" class="form-control"></div>
				</div>
			</div>
			<?php break;
		case 'inventory':
			include('estimate_scope_add_inventory.php');
			break;
		case 'services':
			include('estimate_scope_add_services.php');
			break;
		case 'labour':
			include('estimate_scope_add_labour.php');
			break;
		case 'material':
			include('estimate_scope_add_material.php');
			break;
		case 'products':
			include('estimate_scope_add_products.php');
			break;
		case 'staff':
			include('estimate_scope_add_staff.php');
			break;
		case 'equipment':
			include('estimate_scope_add_equipment.php');
			break;
		case 'position':
			include('estimate_scope_add_position.php');
			break;
		case 'clients':
			include('estimate_scope_add_clients.php');
			break;
	} ?>
	<div class="clearfix pad-vertical"></div>
	<a class="btn brand-btn pull-left" href="../blank_loading_page.php">Cancel</a>
	<button class="btn brand-btn pull-right" type="Submit" name="submit" value="submit">Apply to Scope</button>
</form>
