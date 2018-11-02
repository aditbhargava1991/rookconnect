<?php include_once('../include.php');
if(!isset($tile_name)) {
	$rate_id = filter_var($_GET['rate_id'],FILTER_SANITIZE_STRING);
	$tile_name = filter_var($_GET['tile'],FILTER_SANITIZE_STRING);
	$cat_name = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
	$type_name = filter_var($_GET['type'],FILTER_SANITIZE_STRING);
	$rate_name = $dbc->query("SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='$rate_id'")->fetch_assoc()['rate_card_name'];
	$field_config = get_config($dbc,'company_rate_fields');
	$field_order = get_config($dbc, 'estimate_field_order');
	if($field_order == '') {
		$field_order = 'Type#*#Heading#*#Description#*#Item Type#*#Daily#*#Hourly#*#Customer Price#*#Dollarsaving#*#Percentsaving#*#UOM#*#Quantity#*#Cost#*#Margin#*#Profit#*#Estimate Price#*#Total';
	}
	$field_order = explode('#*#',$field_order);
	foreach(explode('#*#','Type#*#Heading#*#Description#*#Item Type#*#Daily#*#Hourly#*#Customer Price#*#Dollarsaving#*#Percentsaving#*#UOM#*#Quantity#*#Cost#*#Margin#*#Profit#*#Estimate Price#*#Total') as $field) {
		if(!in_array_starts($field, $field_order)) {
			$field_order[] = $field;
		}
	}
	if(str_replace(',','',$field_config) == '') {
		$field_config = ',tile,type,heading,description,uom,cost,estimate,customer,quantity,total,profit,margin,';
	}
}
if(!empty($ref_card) && $tile_name == 'Equipment' && $cat_name == 'category_rates') {
	$result = mysqli_query($dbc, "SELECT * FROM (SELECT `company_rate_card`.`companyrcid`, IFNULL(`company_rate_card`.`rate_card_types`,`src_card`.`rate_card_types`) `rate_card_types`, IFNULL(`company_rate_card`.`item_id`,`src_card`.`item_id`) `item_id`, IFNULL(`company_rate_card`.`heading`,`src_card`.`heading`) `heading`, IFNULL(`company_rate_card`.`description`,`src_card`.`description`) `description`, IFNULL(`company_rate_card`.`daily`,`src_card`.`daily`) `daily`, IFNULL(`company_rate_card`.`hourly`,`src_card`.`hourly`) `hourly`, IFNULL(`company_rate_card`.`uom`,`src_card`.`uom`) `uom`, IFNULL(`company_rate_card`.`cost`,`src_card`.`cost`) `cost`, IFNULL(`company_rate_card`.`cust_price`,`src_card`.`cust_price`) `cust_price`, IFNULL(`company_rate_card`.`profit`,`src_card`.`profit`) `profit`, IFNULL(`company_rate_card`.`margin`,`src_card`.`margin`) `margin`, IFNULL(`company_rate_card`.`sort_order`,`src_card`.`sort_order`) `sort_order`, `src_card`.`profit` `main_profit`, `src_card`.`margin` `main_margin`, `src_card`.`cost` `main_cost`, `src_card`.`cust_price` `main_cust_price`, `src_card`.`hourly` `main_hourly`, `src_card`.`daily` `main_daily` FROM `company_rate_card` LEFT JOIN (SELECT `category` FROM `equipment` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category`) `equipment` ON `company_rate_card`.`description`=`equipment`.`category` LEFT JOIN `company_rate_card` `src_card` ON `src_card`.`tile_name`=`company_rate_card`.`tile_name` AND `src_card`.`heading`=`company_rate_card`.`heading` AND `src_card`.`description`=`company_rate_card`.`description` AND `src_card`.`rate_card_types`=`company_rate_card`.`rate_card_types` AND `src_card`.`item_id`=`company_rate_card`.`item_id` AND `src_card`.`rate_card_name`='$ref_card' WHERE `equipment`.`category` IS NOT NULL AND `company_rate_card`.`tile_name`='$tile_name' AND `company_rate_card`.`deleted`=0 AND IFNULL(`src_card`.`deleted`,0)=0 AND `company_rate_card`.`rate_card_name`='$rate_name' UNION SELECT `company_rate_card`.`companyrcid`, IFNULL(`company_rate_card`.`rate_card_types`,`src_card`.`rate_card_types`) `rate_card_types`, IFNULL(`company_rate_card`.`item_id`,`src_card`.`item_id`) `item_id`, IFNULL(`company_rate_card`.`heading`,`src_card`.`heading`) `heading`, IFNULL(`company_rate_card`.`description`,`src_card`.`description`) `description`, IFNULL(`company_rate_card`.`daily`,`src_card`.`daily`) `daily`, IFNULL(`company_rate_card`.`hourly`,`src_card`.`hourly`) `hourly`, IFNULL(`company_rate_card`.`uom`,`src_card`.`uom`) `uom`, IFNULL(`company_rate_card`.`cost`,`src_card`.`cost`) `cost`, IFNULL(`company_rate_card`.`cust_price`,`src_card`.`cust_price`) `cust_price`, IFNULL(`company_rate_card`.`profit`,`src_card`.`profit`) `profit`, IFNULL(`company_rate_card`.`margin`,`src_card`.`margin`) `margin`, IFNULL(`company_rate_card`.`sort_order`,`src_card`.`sort_order`) `sort_order`, `src_card`.`profit` `main_profit`, `src_card`.`margin` `main_margin`, `src_card`.`cost` `main_cost`, `src_card`.`cust_price` `main_cust_price`, `src_card`.`hourly` `main_hourly`, `src_card`.`daily` `main_daily` FROM `company_rate_card` `src_card` LEFT JOIN (SELECT `category` FROM `equipment` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category`) `equipment` ON `src_card`.`description`=`equipment`.`category` LEFT JOIN `company_rate_card` ON `src_card`.`tile_name`=`company_rate_card`.`tile_name` AND `src_card`.`heading`=`company_rate_card`.`heading` AND `src_card`.`description`=`company_rate_card`.`description` AND `src_card`.`item_id`=`company_rate_card`.`item_id`  AND `company_rate_card`.`rate_card_name`='$rate_name' WHERE `equipment`.`category` IS NOT NULL AND `src_card`.`deleted`=0 AND `src_card`.`rate_card_name`='$ref_card' AND `src_card`.`tile_name`='$tile_name' AND `company_rate_card`.`companyrcid` IS NULL) `rates` ORDER BY IF(`rates`.`sort_order` > 0, `rates`.`sort_order`, 100), `rates`.`heading`, `rates`.`description`");
} else if(!empty($ref_card) && $tile_name == 'Equipment' && $cat_name == 'type_rates') {
	$result = mysqli_query($dbc, "SELECT * FROM (SELECT `company_rate_card`.`companyrcid`, IFNULL(`company_rate_card`.`rate_card_types`,`src_card`.`rate_card_types`) `rate_card_types`, IFNULL(`company_rate_card`.`item_id`,`src_card`.`item_id`) `item_id`, IFNULL(`company_rate_card`.`heading`,`src_card`.`heading`) `heading`, IFNULL(`company_rate_card`.`description`,`src_card`.`description`) `description`, IFNULL(`company_rate_card`.`daily`,`src_card`.`daily`) `daily`, IFNULL(`company_rate_card`.`hourly`,`src_card`.`hourly`) `hourly`, IFNULL(`company_rate_card`.`uom`,`src_card`.`uom`) `uom`, IFNULL(`company_rate_card`.`cost`,`src_card`.`cost`) `cost`, IFNULL(`company_rate_card`.`cust_price`,`src_card`.`cust_price`) `cust_price`, IFNULL(`company_rate_card`.`profit`,`src_card`.`profit`) `profit`, IFNULL(`company_rate_card`.`margin`,`src_card`.`margin`) `margin`, IFNULL(`company_rate_card`.`sort_order`,`src_card`.`sort_order`) `sort_order`, `src_card`.`profit` `main_profit`, `src_card`.`margin` `main_margin`, `src_card`.`cost` `main_cost`, `src_card`.`cust_price` `main_cust_price`, `src_card`.`hourly` `main_hourly`, `src_card`.`daily` `main_daily` FROM `company_rate_card` LEFT JOIN (SELECT `type` FROM `equipment` WHERE `deleted`=0 GROUP BY `type`) `equipment` ON `company_rate_card`.`description`=`equipment`.`type` LEFT JOIN `company_rate_card` `src_card` ON `src_card`.`tile_name`=`company_rate_card`.`tile_name` AND `src_card`.`heading`=`company_rate_card`.`heading` AND `src_card`.`description`=`company_rate_card`.`description` AND `src_card`.`rate_card_types`=`company_rate_card`.`rate_card_types` AND `src_card`.`item_id`=`company_rate_card`.`item_id` AND `src_card`.`rate_card_name`='$ref_card' WHERE `equipment`.`type` IS NOT NULL AND `company_rate_card`.`tile_name`='$tile_name' AND `company_rate_card`.`deleted`=0 AND IFNULL(`src_card`.`deleted`,0)=0 AND `company_rate_card`.`rate_card_name`='$rate_name' UNION SELECT `company_rate_card`.`companyrcid`, IFNULL(`company_rate_card`.`rate_card_types`,`src_card`.`rate_card_types`) `rate_card_types`, IFNULL(`company_rate_card`.`item_id`,`src_card`.`item_id`) `item_id`, IFNULL(`company_rate_card`.`heading`,`src_card`.`heading`) `heading`, IFNULL(`company_rate_card`.`description`,`src_card`.`description`) `description`, IFNULL(`company_rate_card`.`daily`,`src_card`.`daily`) `daily`, IFNULL(`company_rate_card`.`hourly`,`src_card`.`hourly`) `hourly`, IFNULL(`company_rate_card`.`uom`,`src_card`.`uom`) `uom`, IFNULL(`company_rate_card`.`cost`,`src_card`.`cost`) `cost`, IFNULL(`company_rate_card`.`cust_price`,`src_card`.`cust_price`) `cust_price`, IFNULL(`company_rate_card`.`profit`,`src_card`.`profit`) `profit`, IFNULL(`company_rate_card`.`margin`,`src_card`.`margin`) `margin`, IFNULL(`company_rate_card`.`sort_order`,`src_card`.`sort_order`) `sort_order`, `src_card`.`profit` `main_profit`, `src_card`.`margin` `main_margin`, `src_card`.`cost` `main_cost`, `src_card`.`cust_price` `main_cust_price`, `src_card`.`hourly` `main_hourly`, `src_card`.`daily` `main_daily` FROM `company_rate_card` `src_card` LEFT JOIN (SELECT `type` FROM `equipment` WHERE `deleted`=0 GROUP BY `type`) `equipment` ON `src_card`.`description`=`equipment`.`type` LEFT JOIN `company_rate_card` ON `src_card`.`tile_name`=`company_rate_card`.`tile_name` AND `src_card`.`heading`=`company_rate_card`.`heading` AND `src_card`.`description`=`company_rate_card`.`description` AND `src_card`.`item_id`=`company_rate_card`.`item_id`  AND `company_rate_card`.`rate_card_name`='$rate_name' WHERE `equipment`.`type` IS NOT NULL AND `src_card`.`deleted`=0 AND `src_card`.`rate_card_name`='$ref_card' AND `src_card`.`tile_name`='$tile_name' AND `company_rate_card`.`companyrcid` IS NULL) `rates` ORDER BY IF(`rates`.`sort_order` > 0, `rates`.`sort_order`, 100), `rates`.`heading`, `rates`.`description`");
} else if(!empty($ref_card) && $tile_name == 'Equipment') {
	$result = mysqli_query($dbc, "SELECT * FROM (SELECT `company_rate_card`.`companyrcid`, IFNULL(`company_rate_card`.`rate_card_types`,`src_card`.`rate_card_types`) `rate_card_types`, IFNULL(`company_rate_card`.`item_id`,`src_card`.`item_id`) `item_id`, IFNULL(`company_rate_card`.`heading`,`src_card`.`heading`) `heading`, IFNULL(`company_rate_card`.`description`,`src_card`.`description`) `description`, IFNULL(`company_rate_card`.`daily`,`src_card`.`daily`) `daily`, IFNULL(`company_rate_card`.`hourly`,`src_card`.`hourly`) `hourly`, IFNULL(`company_rate_card`.`uom`,`src_card`.`uom`) `uom`, IFNULL(`company_rate_card`.`cost`,`src_card`.`cost`) `cost`, IFNULL(`company_rate_card`.`cust_price`,`src_card`.`cust_price`) `cust_price`, IFNULL(`company_rate_card`.`profit`,`src_card`.`profit`) `profit`, IFNULL(`company_rate_card`.`margin`,`src_card`.`margin`) `margin`, IFNULL(`company_rate_card`.`sort_order`,`src_card`.`sort_order`) `sort_order`, `src_card`.`profit` `main_profit`, `src_card`.`margin` `main_margin`, `src_card`.`cost` `main_cost`, `src_card`.`cust_price` `main_cust_price`, `src_card`.`hourly` `main_hourly`, `src_card`.`daily` `main_daily` FROM `company_rate_card` LEFT JOIN `equipment` ON `company_rate_card`.`item_id`=`equipment`.`equipmentid` LEFT JOIN `company_rate_card` `src_card` ON `src_card`.`tile_name`=`company_rate_card`.`tile_name` AND `src_card`.`heading`=`company_rate_card`.`heading` AND `src_card`.`description`=`company_rate_card`.`description` AND `src_card`.`rate_card_types`=`company_rate_card`.`rate_card_types` AND `src_card`.`item_id`=`company_rate_card`.`item_id` AND `src_card`.`rate_card_name`='$ref_card' WHERE `equipment`.`category`='$cat_name' AND `company_rate_card`.`tile_name`='$tile_name' AND `company_rate_card`.`deleted`=0 AND IFNULL(`src_card`.`deleted`,0)=0 AND `company_rate_card`.`rate_card_name`='$rate_name' UNION SELECT `company_rate_card`.`companyrcid`, IFNULL(`company_rate_card`.`rate_card_types`,`src_card`.`rate_card_types`) `rate_card_types`, IFNULL(`company_rate_card`.`item_id`,`src_card`.`item_id`) `item_id`, IFNULL(`company_rate_card`.`heading`,`src_card`.`heading`) `heading`, IFNULL(`company_rate_card`.`description`,`src_card`.`description`) `description`, IFNULL(`company_rate_card`.`daily`,`src_card`.`daily`) `daily`, IFNULL(`company_rate_card`.`hourly`,`src_card`.`hourly`) `hourly`, IFNULL(`company_rate_card`.`uom`,`src_card`.`uom`) `uom`, IFNULL(`company_rate_card`.`cost`,`src_card`.`cost`) `cost`, IFNULL(`company_rate_card`.`cust_price`,`src_card`.`cust_price`) `cust_price`, IFNULL(`company_rate_card`.`profit`,`src_card`.`profit`) `profit`, IFNULL(`company_rate_card`.`margin`,`src_card`.`margin`) `margin`, IFNULL(`company_rate_card`.`sort_order`,`src_card`.`sort_order`) `sort_order`, `src_card`.`profit` `main_profit`, `src_card`.`margin` `main_margin`, `src_card`.`cost` `main_cost`, `src_card`.`cust_price` `main_cust_price`, `src_card`.`hourly` `main_hourly`, `src_card`.`daily` `main_daily` FROM `company_rate_card` `src_card` LEFT JOIN `company_rate_card` ON `src_card`.`tile_name`=`company_rate_card`.`tile_name` AND `src_card`.`heading`=`company_rate_card`.`heading` AND `src_card`.`description`=`company_rate_card`.`description` AND `src_card`.`item_id`=`company_rate_card`.`item_id`  AND `company_rate_card`.`rate_card_name`='$rate_name' WHERE `src_card`.`deleted`=0 AND `src_card`.`rate_card_name`='$ref_card' AND `src_card`.`tile_name`='$tile_name' AND `company_rate_card`.`companyrcid` IS NULL) `rates` ORDER BY IF(`rates`.`sort_order` > 0, `rates`.`sort_order`, 100), `rates`.`heading`, `rates`.`description`");
} else if(!empty($ref_card) && $tile_name == 'Services') {
	$result = mysqli_query($dbc, "SELECT * FROM (SELECT `company_rate_card`.`companyrcid`, IFNULL(`company_rate_card`.`rate_card_types`,`src_card`.`rate_card_types`) `rate_card_types`, IFNULL(`company_rate_card`.`item_id`,`src_card`.`item_id`) `item_id`, IFNULL(`company_rate_card`.`heading`,`src_card`.`heading`) `heading`, IFNULL(`company_rate_card`.`description`,`src_card`.`description`) `description`, IFNULL(`company_rate_card`.`daily`,`src_card`.`daily`) `daily`, IFNULL(`company_rate_card`.`hourly`,`src_card`.`hourly`) `hourly`, IFNULL(`company_rate_card`.`uom`,`src_card`.`uom`) `uom`, IFNULL(`company_rate_card`.`cost`,`src_card`.`cost`) `cost`, IFNULL(`company_rate_card`.`cust_price`,`src_card`.`cust_price`) `cust_price`, IFNULL(`company_rate_card`.`profit`,`src_card`.`profit`) `profit`, IFNULL(`company_rate_card`.`margin`,`src_card`.`margin`) `margin`, IFNULL(`company_rate_card`.`sort_order`,`src_card`.`sort_order`) `sort_order`, `src_card`.`profit` `main_profit`, `src_card`.`margin` `main_margin`, `src_card`.`cost` `main_cost`, `src_card`.`cust_price` `main_cust_price`, `src_card`.`hourly` `main_hourly`, `src_card`.`daily` `main_daily` FROM `company_rate_card` LEFT JOIN `services` ON `company_rate_card`.`item_id`=`services`.`serviceid` LEFT JOIN `company_rate_card` `src_card` ON `src_card`.`tile_name`=`company_rate_card`.`tile_name` AND `src_card`.`heading`=`company_rate_card`.`heading` AND `src_card`.`description`=`company_rate_card`.`description` AND `src_card`.`rate_card_types`=`company_rate_card`.`rate_card_types` AND `src_card`.`item_id`=`company_rate_card`.`item_id` AND `src_card`.`rate_card_name`='$ref_card' WHERE `services`.`category`='$cat_name' AND `company_rate_card`.`tile_name`='$tile_name' AND `company_rate_card`.`deleted`=0 AND IFNULL(`src_card`.`deleted`,0)=0 AND `company_rate_card`.`rate_card_name`='$rate_name' UNION SELECT `company_rate_card`.`companyrcid`, IFNULL(`company_rate_card`.`rate_card_types`,`src_card`.`rate_card_types`) `rate_card_types`, IFNULL(`company_rate_card`.`item_id`,`src_card`.`item_id`) `item_id`, IFNULL(`company_rate_card`.`heading`,`src_card`.`heading`) `heading`, IFNULL(`company_rate_card`.`description`,`src_card`.`description`) `description`, IFNULL(`company_rate_card`.`daily`,`src_card`.`daily`) `daily`, IFNULL(`company_rate_card`.`hourly`,`src_card`.`hourly`) `hourly`, IFNULL(`company_rate_card`.`uom`,`src_card`.`uom`) `uom`, IFNULL(`company_rate_card`.`cost`,`src_card`.`cost`) `cost`, IFNULL(`company_rate_card`.`cust_price`,`src_card`.`cust_price`) `cust_price`, IFNULL(`company_rate_card`.`profit`,`src_card`.`profit`) `profit`, IFNULL(`company_rate_card`.`margin`,`src_card`.`margin`) `margin`, IFNULL(`company_rate_card`.`sort_order`,`src_card`.`sort_order`) `sort_order`, `src_card`.`profit` `main_profit`, `src_card`.`margin` `main_margin`, `src_card`.`cost` `main_cost`, `src_card`.`cust_price` `main_cust_price`, `src_card`.`hourly` `main_hourly`, `src_card`.`daily` `main_daily` FROM `company_rate_card` `src_card` LEFT JOIN `company_rate_card` ON `src_card`.`tile_name`=`company_rate_card`.`tile_name` AND `src_card`.`heading`=`company_rate_card`.`heading` AND `src_card`.`description`=`company_rate_card`.`description` AND `src_card`.`item_id`=`company_rate_card`.`item_id`  AND `company_rate_card`.`rate_card_name`='$rate_name' WHERE `src_card`.`deleted`=0 AND `src_card`.`rate_card_name`='$ref_card' AND `src_card`.`tile_name`='$tile_name' AND `company_rate_card`.`companyrcid` IS NULL) `rates` ORDER BY IF(`rates`.`sort_order` > 0, `rates`.`sort_order`, 100), `rates`.`heading`, `rates`.`description`");
} else if(!empty($ref_card)) {
	$result = mysqli_query($dbc, "SELECT * FROM (SELECT `company_rate_card`.`companyrcid`, IFNULL(`company_rate_card`.`rate_card_types`,`src_card`.`rate_card_types`) `rate_card_types`, IFNULL(`company_rate_card`.`item_id`,`src_card`.`item_id`) `item_id`, IFNULL(`company_rate_card`.`heading`,`src_card`.`heading`) `heading`, IFNULL(`company_rate_card`.`description`,`src_card`.`description`) `description`, IFNULL(`company_rate_card`.`daily`,`src_card`.`daily`) `daily`, IFNULL(`company_rate_card`.`hourly`,`src_card`.`hourly`) `hourly`, IFNULL(`company_rate_card`.`uom`,`src_card`.`uom`) `uom`, IFNULL(`company_rate_card`.`cost`,`src_card`.`cost`) `cost`, IFNULL(`company_rate_card`.`cust_price`,`src_card`.`cust_price`) `cust_price`, IFNULL(`company_rate_card`.`profit`,`src_card`.`profit`) `profit`, IFNULL(`company_rate_card`.`margin`,`src_card`.`margin`) `margin`, IFNULL(`company_rate_card`.`sort_order`,`src_card`.`sort_order`) `sort_order`, `src_card`.`profit` `main_profit`, `src_card`.`margin` `main_margin`, `src_card`.`cost` `main_cost`, `src_card`.`cust_price` `main_cust_price`, `src_card`.`hourly` `main_hourly`, `src_card`.`daily` `main_daily` FROM `company_rate_card` LEFT JOIN `company_rate_card` `src_card` ON `src_card`.`tile_name`=`company_rate_card`.`tile_name` AND `src_card`.`heading`=`company_rate_card`.`heading` AND `src_card`.`description`=`company_rate_card`.`description` AND `src_card`.`rate_card_types`=`company_rate_card`.`rate_card_types` AND `src_card`.`item_id`=`company_rate_card`.`item_id` AND `src_card`.`rate_card_name`='$ref_card' WHERE `company_rate_card`.`tile_name`='$tile_name' AND `company_rate_card`.`deleted`=0 AND IFNULL(`src_card`.`deleted`,0)=0 AND `company_rate_card`.`rate_card_name`='$rate_name' UNION SELECT `company_rate_card`.`companyrcid`, IFNULL(`company_rate_card`.`rate_card_types`,`src_card`.`rate_card_types`) `rate_card_types`, IFNULL(`company_rate_card`.`item_id`,`src_card`.`item_id`) `item_id`, IFNULL(`company_rate_card`.`heading`,`src_card`.`heading`) `heading`, IFNULL(`company_rate_card`.`description`,`src_card`.`description`) `description`, IFNULL(`company_rate_card`.`daily`,`src_card`.`daily`) `daily`, IFNULL(`company_rate_card`.`hourly`,`src_card`.`hourly`) `hourly`, IFNULL(`company_rate_card`.`uom`,`src_card`.`uom`) `uom`, IFNULL(`company_rate_card`.`cost`,`src_card`.`cost`) `cost`, IFNULL(`company_rate_card`.`cust_price`,`src_card`.`cust_price`) `cust_price`, IFNULL(`company_rate_card`.`profit`,`src_card`.`profit`) `profit`, IFNULL(`company_rate_card`.`margin`,`src_card`.`margin`) `margin`, IFNULL(`company_rate_card`.`sort_order`,`src_card`.`sort_order`) `sort_order`, `src_card`.`profit` `main_profit`, `src_card`.`margin` `main_margin`, `src_card`.`cost` `main_cost`, `src_card`.`cust_price` `main_cust_price`, `src_card`.`hourly` `main_hourly`, `src_card`.`daily` `main_daily` FROM `company_rate_card` `src_card` LEFT JOIN `company_rate_card` ON `src_card`.`tile_name`=`company_rate_card`.`tile_name` AND `src_card`.`heading`=`company_rate_card`.`heading` AND `src_card`.`description`=`company_rate_card`.`description` AND `src_card`.`item_id`=`company_rate_card`.`item_id`  AND `company_rate_card`.`rate_card_name`='$rate_name' WHERE `src_card`.`deleted`=0 AND `src_card`.`rate_card_name`='$ref_card' AND `src_card`.`tile_name`='$tile_name' AND `company_rate_card`.`companyrcid` IS NULL) `rates` ORDER BY IF(`rates`.`sort_order` > 0, `rates`.`sort_order`, 100), `rates`.`heading`, `rates`.`description`");
} else if($tile_name == 'Equipment' && $cat_name == 'category_rates') {
	$result = mysqli_query($dbc, "SELECT `company_rate_card`.* FROM `company_rate_card` LEFT JOIN `equipment` ON `company_rate_card`.`description`=`equipment`.`category` WHERE `rate_card_name`='$rate_name' AND `company_rate_card`.`deleted`=0 AND `tile_name`='$tile_name' AND `equipment`.`category` IS NOT NULL GROUP BY `company_rate_card`.`companyrcid` ORDER BY `tile_name`, `rate_card_types`, IF(`sort_order` > 0, `company_rate_card`.`sort_order`, 100), `company_rate_card`.`heading`, `company_rate_card`.`description`");
} else if($tile_name == 'Equipment' && $cat_name == 'type_rates') {
	$result = mysqli_query($dbc, "SELECT `company_rate_card`.* FROM `company_rate_card` LEFT JOIN `equipment` ON `company_rate_card`.`description`=`equipment`.`type` WHERE `rate_card_name`='$rate_name' AND `company_rate_card`.`deleted`=0 AND `tile_name`='$tile_name' AND `equipment`.`type` IS NOT NULL GROUP BY `company_rate_card`.`companyrcid` ORDER BY `tile_name`, `rate_card_types`, IF(`sort_order` > 0, `company_rate_card`.`sort_order`, 100), `company_rate_card`.`heading`, `company_rate_card`.`description`");
} else if($tile_name == 'Equipment') {
	$result = mysqli_query($dbc, "SELECT `company_rate_card`.* FROM `company_rate_card` LEFT JOIN `equipment` ON `company_rate_card`.`item_id`=`equipment`.`equipmentid` WHERE `rate_card_name`='$rate_name' AND `company_rate_card`.`deleted`=0 AND `equipment`.`category`='$cat_name' AND `tile_name`='$tile_name' ORDER BY `tile_name`, `rate_card_types`, IF(`company_rate_card`.`sort_order` > 0, `company_rate_card`.`sort_order`, 100), `company_rate_card`.`heading`, `company_rate_card`.`description`");
} else if($tile_name == 'Services') {
	$result = mysqli_query($dbc, "SELECT `company_rate_card`.* FROM `company_rate_card` LEFT JOIN `services` ON `company_rate_card`.`item_id`=`services`.`serviceid` WHERE `rate_card_name`='$rate_name' AND `company_rate_card`.`deleted`=0 AND `services`.`category`='$cat_name' AND `tile_name`='$tile_name' ORDER BY `tile_name`, `rate_card_types`, IF(`company_rate_card`.`sort_order` > 0, `company_rate_card`.`sort_order`, 100), `company_rate_card`.`heading`, `company_rate_card`.`description`");
} else {
	$result = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE `rate_card_name`='$rate_name' AND `deleted`=0 AND `tile_name`='$tile_name' ORDER BY `tile_name`, `rate_card_types`, IF(`sort_order` > 0, `sort_order`, 100), `heading`, `description`");
}
$row_count = mysqli_num_rows($result);
$row = mysqli_fetch_array($result); ?>
<div class="tile_group">
	<!--<div class="col-sm-8 pull-right">
		<button class="add_rate btn brand-btn mobile-block pull-right">Add <?= $tile_name ?> Item</button>
	</div>-->
	<div id="bd_accordion" class="panel-group">
		<div id="no-more-tables">
			<table width="100%" id="rate_table" class="table table-bordered table-striped">
                <thead>
				<tr class="hidden-xs hidden-sm">
					<?php foreach($field_order as $field_data) {
						$data = explode('***',$field_data);
						$data[1] = ($data[1] == '' ? $data[0] : $data[1]);
						switch($data[0]) {
							case 'Type':
								if(strpos($field_config,',type,') !== FALSE) {
									?><th style="text-align:center;">Type</th><?php
								}
								break;
							case 'Heading':
								if(strpos($field_config,',heading,') !== FALSE && $tile_name != 'Expenses') {
									?><th style="text-align:center;">Heading</th><?php
								}
								break;
							case 'Description':
								if(strpos($field_config,',description,') !== FALSE) {
									?><th style="text-align:center;">Description</th><?php
								}
								break;
							case 'Item Type':
								if(strpos($field_config,',itemtype,') !== FALSE) {
									?><th style="text-align:center;">Item Type</th><?php
								}
								break;
							case 'Daily':
								if(strpos($field_config,',daily,') !== FALSE) {
									?><th style="text-align:center;">Daily</th><?php 
								}
								break;
							case 'Hourly':
								if(strpos($field_config,',hourly,') !== FALSE) {
									?><th style="text-align:center;">Hourly</th><?php
								}
								break;
							case 'UOM':
								if(strpos($field_config,',uom,') !== FALSE) {
									?><th style="text-align:center;">
										<span class="popover-examples list-inline tooltip-navigation"><a style="top:0;" class="info_i_sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Unit of Measure"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> UOM
									</th><?php
								}
								break;
							case 'Cost':
								if(strpos($field_config,',cost,') !== FALSE) {
									?><th style="text-align:center;">Cost</th><?php
								}
								break;
							case 'Estimate Price':
								if(strpos($field_config,',estimate,') !== FALSE) {
									?><th style="text-align:center;">Estimate Price</th><?php
								}
								break;
							case 'Customer Price':
								if(strpos($field_config,',customer,') !== FALSE) {
									?><th style="text-align:center;"><?= $tile_name == 'Expenses' ? 'Rate' : 'Customer Price' ?></th><?php
								}
								break;
							case 'Dollarsaving':
								if(strpos($field_config,',customer,') !== FALSE) {
									?><th style="text-align:center;">
										<span class="popover-examples list-inline tooltip-navigation"><a style="top:0;" class="info_i_sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="This will display the savings relative to the rate card selected as the Primary Rate Card"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>$ Savings
									</th><?php
								}
								break;
							case 'Percentsaving':
								if(strpos($field_config,',customer,') !== FALSE) {
									?><th style="text-align:center;">
										<span class="popover-examples list-inline tooltip-navigation"><a style="top:0;" class="info_i_sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="This will display the savings relative to the rate card selected as the Primary Rate Card"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>% Savings
									</th><?php
								}
								break;
							case 'Quantity':
								if(strpos($field_config,',quantity,') !== FALSE) {
									?><th style="text-align:center;">Quantity</th><?php
								}
								break;
							case 'Total':
								if(strpos($field_config,',total,') !== FALSE) {
									?><th style="text-align:center;">Total</th><?php
								}
								break;
							case 'Profit':
								if(strpos($field_config,',profit,') !== FALSE) {
									?><th style="text-align:center;">$ Profit</th><?php
								}
								break;
							case 'Margin':
								if(strpos($field_config,',margin,') !== FALSE) {
									?><th style="text-align:center;">% Margin</th><?php
								}
								break;
						}
					}
					if(strpos($field_config,',sort_order,') !== FALSE) {
						?><th style="text-align:center;"><span class="popover-examples list-inline tooltip-navigation"><a style="top:0;" class="info_i_sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Items without a sort order will be sorted alphabetically after the items that have a sort order."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Sort Order</th><?php
					} ?>
					<th style="text-align:center; width:7.5em;">Function</label>
				</tr>
                </thead>

				<?php do {
					$rate_id = $row['companyrcid'];
					$rate_type = $row['rate_card_types'];
					$rate_heading = $row['heading'];
					$rate_item = $row['item_id'];
					$rate_desc = $row['description'];
					$rate_daily = $row['daily'];
					$rate_hourly = $row['hourly'];
					$rate_uom = $row['uom'];
					$rate_cost = $row['cost'];
					$rate_cust = $row['cust_price'];
					$rate_profit = $row['profit'];
					$rate_margin = $row['margin'];
					$rate_total = $rate_profit / ($rate_margin / 100);
					$rate_quant = ($rate_total - $rate_profit) / $rate_cost;
					$rate_est = $rate_total / $rate_quant;
					$rate_sort = $row['sort_order'];
					$rate_deleted = $row['deleted'];
					$col_count = 0;
					$main_rate = ($row['main_profit'] / ($row['main_margin'] / 100)) / ((($row['main_profit'] / ($row['main_margin'] / 100)) - $row['main_profit']) / $row['main_cost']);
					$main_rate_field = 'rate_est';
					$main_rate_field_name = 'estimateprice';
					if($rate_cust > 0) {
						$main_rate = $row['main_cust_price'];
						$main_rate_field = 'rate_cust';
						$main_rate_field_name = 'cust_price';
					} else if($rate_hourly > 0) {
						$main_rate = $row['main_hourly'];
						$main_rate_field = 'rate_hourly';
						$main_rate_field_name = 'hourly';
					} else if($rate_daily > 0) {
						$main_rate = $row['main_daily'];
						$main_rate_field = 'rate_daily';
						$main_rate_field_name = 'daily';
					} ?>
					<tr class="<?php echo ($i == 0 ? 'additional_positionprod' : ''); ?>" <?php if($rate_deleted == 1) { echo 'style="display:none;"'; } ?>>
						<input type="hidden" name="entry_id[]" value="<?php echo ($rate_id != '' ? $rate_id : 'NEW_'); ?>">
						<input type="hidden" name="deleted[]" value="<?php echo $rate_deleted; ?>">
						<input type="hidden" name="tile_name[]" value="<?php echo $tile_name; ?>">

						<?php foreach($field_order as $field_data) {
							$data = explode('***',$field_data);
							$data[1] = ($data[1] == '' ? $data[0] : $data[1]);
							switch($data[0]) {
								case 'Type':
									if(strpos($field_config,',type,') !== FALSE) { $col_count++; ?><td data-title="Type">
										<select id="type_0" data-placeholder="Choose a Type..." name="rate_card_types[]" class="chosen-select-deselect form-control type" width="380">
										  <option value=""></option>
										  <?php
											$tabs = get_config($dbc, 'rate_card_types');
											$each_tab = explode(',', $tabs);
											foreach ($each_tab as $cat_tab) {
												if (trim($rate_type) == $cat_tab) {
													$selected = 'selected="selected"';
												} else {
													$selected = '';
												}
												echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
											}
										  ?>
										</select>
										</td><?php
									}
									break;
								case 'Heading':
									if(strpos($field_config,',heading,') !== FALSE && $tile_name != 'Expenses') {
										$col_count++; ?><td data-title="Heading">
											<input data-placeholder="Choose a Product..." name="heading[]" type="text" class="form-control prodprice1" value="<?php echo $rate_heading; ?>" />
										</td><?php 
									}
									break;
								case 'Description':
									if(strpos($field_config,',description,') !== FALSE) {
										$col_count++; ?><td data-title="Description">
											<input data-placeholder="Choose a Product..." name="description[]" type="text" class="form-control prodprice1" value="<?php echo $rate_desc; ?>" />
										</td><?php 
									}
									break;
								case 'Item Type':
									if(strpos($field_config,',itemtype,') !== FALSE) {
										$col_count++; ?><td data-title="Item Type">
											<select name="description[]" id="item_0" data-placeholder="Select Descriptions" class="chosen-select-deselect form-control item"><option></option>
												<?php if($tile_name == 'Position') { ?>
													<option value='Subsistence Pay'>Subsistence Pay</option>
												<?php } ?>
												<?php if($rate_desc != '') {
													echo "<option selected value='$rate_desc'>$rate_desc</option>";
												} else if($rate_desc == '' && $rate_item > 0 && $tile_name == 'Position') {
													$rate_desc = get_field_value('name','positions','position_id',$rate_item);
													if($rate_desc == '') {
														echo "<option selected value='$rate_item'>$rate_item</option>";
													} else {
														echo "<option selected value='$rate_desc'>$rate_desc</option>";
													}
												} else if($rate_desc == '' && $rate_item > 0) {
													echo "<option selected value='$rate_item'>$rate_item</option>";
												} ?>
											</select>
										</td><?php
									}
									break;
								case 'Item ID':
									if(strpos($field_config,',item_id,') !== FALSE) {
										$col_count++; ?><td data-title="Item Type">
											<select name="item_id[]" id="itemid_0" data-placeholder="Select Item" class="chosen-select-deselect form-control item"><option></option>
												<?php if($tile_name == 'Position') { ?>
													<option <?= $rate_desc == 'Subsistence Pay' ? 'selected' : '' ?> value='Subsistence Pay'>Subsistence Pay</option>
												<?php } ?>
												<?php if($rate_item > 0) {
													echo "<option selected value='$rate_item'>$rate_item</option>";
												} ?>
											</select>
										</td><?php
									}
									break;
								case 'Daily':
									if(strpos($field_config,',daily,') !== FALSE) {
										$col_count++; ?><td data-title="Daily">
											<input data-placeholder="Choose a Product..." name="daily[]" type="text" class="form-control prodprice1" value="<?php echo $rate_daily; ?>" />
										</td><?php 
									}
									break;
								case 'Hourly':
									if(strpos($field_config,',hourly,') !== FALSE) {
										$col_count++; ?><td data-title="Hourly">
											<input data-placeholder="Choose a Product..." name="hourly[]" type="text" class="form-control prodprice1" value="<?php echo $rate_hourly; ?>" />
										</td><?php
									}
									break;
								case 'UOM':
									if(strpos($field_config,',uom,') !== FALSE) {
										$col_count++; ?><td data-title="UoM">
											<input data-placeholder="Choose a Product..." name="uom[]" type="text" class="form-control prodprice1" value="<?php echo $rate_uom; ?>" />
										</td><?php
									}
									break;
								case 'Cost':
									if(strpos($field_config,',cost,') !== FALSE) {
										$col_count++; ?><td data-title="Cost">
											<input data-placeholder="Choose a Product..." id="cost_<?php echo $i; ?>" name="cost[]" type="text" class="form-control prodprice1" value="<?php echo $rate_cost; ?>" />
										</td><?php
									}
									break;
								case 'Estimate Price':
									if(strpos($field_config,',estimate,') !== FALSE) {
										$col_count++; ?><td data-title="Estimate Price">
											<input data-placeholder="Choose a Product..." id="estimateprice_<?php echo $i; ?>" name="estimateprice[]" onblur='changePriceTotal(this)' type="text" class="form-control prodprice1" value="<?php echo round($rate_est); ?>" />
										</td><?php
									}
									break;
								case 'Customer Price':
									if(strpos($field_config,',customer,') !== FALSE) {
										$col_count++; ?><td data-title="<?= $tile_name == 'Expenses' ? 'Rate' : 'Customer Price' ?>">
											<input id="custprice_<?php echo $i; ?>" name="cust_price[]" type="text" class="form-control prodprice1" value="<?php echo round($rate_cust); ?>" />
										</td><?php
									}
									break;
								case 'Dollarsaving':
									if(strpos($field_config,',dollarsaving,') !== FALSE) {
										$col_count++; ?><td data-title="$ Savings">
											<input data-placeholder="Savings From Standard Rate" id="dollarsaving_<?php echo $i; ?>" name="dollarsaving[]" type="text" class="form-control dollarsaving" data-main-rate="<?= $main_rate ?>" data-rate-field="<?= $main_rate_field_name ?>" value="<?= $main_rate > 0 ? number_format($main_rate - $$main_rate_field,2) : '' ?>" />
										</td><?php
									}
									break;
								case 'Percentsaving':
									if(strpos($field_config,',percentsaving,') !== FALSE) {
										$col_count++; ?><td data-title="% Savings">
											<input data-placeholder="Savings From Standard Rate" id="percentsaving_<?php echo $i; ?>" name="percentsaving[]" type="text" class="form-control percentsaving" data-main-rate="<?= $main_rate ?>" data-rate-field="<?= $main_rate_field_name ?>" value="<?= $main_rate > 0 ? round((1 - ($$main_rate_field / $main_rate)) * 100,2) : '' ?>" />
										</td><?php
									}
									break;
								case 'Quantity':
									if(strpos($field_config,',quantity,') !== FALSE) {
										$col_count++; ?><td data-title="Quantity">
											<input data-placeholder="Choose a Product..." id="quantity_<?php echo $i; ?>" name="quantity[]" onblur='changePriceTotal(this)' type="text" class="form-control prodprice1" value="<?php echo round($rate_quant); ?>" />
										</td><?php
									}
									break;
								case 'Total':
									if(strpos($field_config,',total,') !== FALSE) {
										$col_count++; ?><td data-title="Total">
											<input data-placeholder="Choose a Product..." id="total_<?php echo $i; ?>" name="total[]" type="text" class="form-control prodprice1" value="<?php echo round($rate_total,2); ?>" />
										</td><?php
									}
									break;
								case 'Profit':
									if(strpos($field_config,',profit,') !== FALSE) {
										$col_count++; ?><td data-title="$ Profit">
											<input data-placeholder="Choose a Product..." id="profit_<?php echo $i; ?>" name="profit[]" type="text" class="form-control prodprice1" value="<?php echo round($rate_profit,2); ?>" />
										</td><?php
									}
									break;
								case 'Margin':
									if(strpos($field_config,',margin,') !== FALSE) {
										$col_count++; ?><td data-title="% Margin">
											<input data-placeholder="Choose a Product..." id="margin_<?php echo $i; ?>" name="margin[]" type="text" class="form-control prodprice1" value="<?php echo round($rate_margin,2); ?>" />
										</td><?php
									}
									break;
							}
						}
						if(strpos($field_config,',sort_order,') !== FALSE) {
							$col_count++; ?><td data-title="Sort Order">
								<select data-placeholder="Select a Position..." id="sort_order_<?php echo $i; ?>" name="sort_order[]" class="chosen-select-deselect">
									<?php for($j = 0; $j < 100; $j++) {
										echo "<option ".($j == $rate_sort ? 'selected' : '')." value='$j'>".($j > 0 ? $j : '-')."</option>";
									} ?>
								</select>
							</td><?php
						} ?>
						<td style="text-align:center;">
							<?php $col_count++;
							if(strpos($field_config,',breakdown,') !== FALSE) { ?>
								<button type="button" onclick="addBreakdownRow(this); return false;" class="btn brand-btn">Add Breakdown Detail</button>
							<?php }
							if(strpos($field_config,',sort_order,') === FALSE) { ?>
								<img src="../img/icons/drag_handle.png" class="cursor-hand handle inline-img pull-right">
								<input type="hidden" id="sort_order_<?php echo $i; ?>" name="sort_order[]" value="<?= $rate_sort ?>">
							<?php } ?>
							<span onclick="remove_row(this);"><a href="" onclick="return false;"><img src="../img/remove.png" class="inline-img"></a></span>
							<span class="add_rate"><a href="" onclick="return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img"></a></span>
						</td>
					</tr>
					<?php $breakdown_result = mysqli_query($dbc, "SELECT * FROM `rate_card_breakdown` WHERE `rate_card_id`='$rate_id' AND `rate_card_type`='".$_GET['card']."' AND `deleted`=0"); ?>
					<tr class="breakdown_accordion">
						<td style="<?= mysqli_num_rows($breakdown_result) > 0 ? "" : "display:none;" ?>" colspan="<?= $col_count ?>">
							<div class="panel panel-default" style="<?= mysqli_num_rows($breakdown_result) > 0 ? "" : "display:none;" ?>">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#bd_accordion" href="#collapse_<?= $i ?>" >
											Breakdown Details<span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_<?= $i ?>" class="panel-collapse collapse">
									<div class="panel-body">
										<table class="table table-bordered table-striped" style="background-color: transparent;">
                                            <thead>
                                                <tr class="hidden-sm hidden-xs">
                                                    <th>Description</th>
                                                    <th>Quantity</th>
                                                    <th>UoM</th>
                                                    <th>Unit Cost</th>
                                                    <th>Subtotal</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
											<?php while($breakdown = mysqli_fetch_array($breakdown_result)) { ?>
												<tr><input type='hidden' name='bd_<?= $rate_id ?>[]' value='<?= $breakdown['rcbid'] ?>'>
													<td data-title="Description"><input type="text" name="bd_description_<?= $rate_id ?>[]" value="<?= $breakdown['description'] ?>" onchange="syncDetails(this);" placeholder="Description" class="form-control"></td>
													<td data-title="Quantity"><input type="number" onchange="calcBreakdowns();" name="bd_quantity_<?= $rate_id ?>[]" value="<?= $breakdown['quantity'] ?>" placeholder="Quantity" class="form-control" min="0" step="any"></td>
													<td data-title="UoM"><input type="text" name="bd_uom_<?= $rate_id ?>[]" value="<?= $breakdown['uom'] ?>" placeholder="UoM" class="form-control"></td>
													<td data-title="Unit Cost"><input type="number" onchange="syncDetails(this);" name="bd_cost_<?= $rate_id ?>[]" value="<?= $breakdown['cost'] ?>" placeholder="Unit Cost" class="form-control" min="0" step="any"></td>
													<td data-title="Subtotal"><input type="number" onchange="calcBreakdowns();" name="bd_total_<?= $rate_id ?>[]" data-id="<?= $rate_id ?>" value="<?= $breakdown['total'] ?>" placeholder="Subtotal" class="form-control" min="0" step="any"></td>
													<td style="text-align:center;"><a href="" onclick="removeDetail(this); return false;">Delete</a></td>
												</tr>
											<?php } ?>
										</table>
									</div>
								</div>
							</div>
						</td>
					</tr>
					<?php if(!empty($_GET['id'])) {
					}
				} while($row = mysqli_fetch_array($result)); ?>
			</table>
		</div>
	</div>
	<!--<div class="col-sm-8 pull-right">
		<button class="add_rate btn brand-btn mobile-block pull-right">Add <?= $tile_name ?> Item</button>
	</div>-->
</div>
<div class="clearfix triple-gap-bottom"></div>