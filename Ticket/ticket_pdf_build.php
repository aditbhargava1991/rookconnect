<?php include_once('../include.php');
$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
$form = filter_var($_POST['custom_form'], FILTER_SANITIZE_STRING);
if(isset($_POST['custom_form'])) {
	$ticketid = filter_var($_GET['ticketid'], FILTER_SANITIZE_STRING);
	$revision = 1 + mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`revision`) `revision` FROM `ticket_pdf_field_values` WHERE `ticketid`='$ticketid' AND `pdf_type`='$form'"))['revision'];
	foreach($_POST as $field => $value) {
		if($field != 'custom_form') {
			$field = filter_var($field, FILTER_SANITIZE_STRING);
			if(is_array($value)) {
				$value = implode(',',$value);
			}
			$value = filter_var(htmlentities($value), FILTER_SANITIZE_STRING);
			$dbc->query("INSERT INTO `ticket_pdf_field_values` (`ticketid`, `pdf_type`, `revision`, `field_name`, `field_value`) VALUES ('$ticketid', '$form', '$revision', '$field', '$value')");
		}
	}
	echo "<script> window.location.replace('ticket_pdf_custom.php?form=$form&ticketid=$ticketid&revision=$revision'); </script>";
} else if($ticketid > 0) {
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
	$ticket = get_ticket_label($dbc, $get_ticket);
	$revision = 999999999;
	if($_GET['revision'] > 0) {
		$revision = $_GET['revision'];
	}
	if(!empty($_GET['custom_form'])) { ?>
		<script>
		function setText(input) {
			var block = $(input).closest('.form-group');
			var text = '';
			block.find('[data-text]:checked').each(function() {
				text = text+$(this).data('text')+"\n";
			});
			block.find('input,textarea').last().val(text);
		}
		function updateTicket(select, field) {
			if(confirm("Click OK to update the <?= TICKET_NOUN ?> with this contact?")) {
				$.post('ticket_ajax_all.php?action=manual_update', {
					table_name: 'ticket_schedule',
					field_name: field.split('-')[1],
					value: select.value,
					ticketid: <?= $ticketid ?>,
					identifier: 'type',
					id: field.split('-')[0]
				});
			}
			$(select).nextAll('input,textarea').first().val($(select).find('option:selected').data('output'));
		}
		function updateChecked(input) {
			if($(input).is(':checked')) {
				$(input).closest('label').find('.hidden_checkbox').prop('disabled', true);
			} else {
				$(input).closest('label').find('.hidden_checkbox').prop('disabled', false);
			}
		}
		</script>
		<?php $form = $dbc->query("SELECT * FROM `ticket_pdf` WHERE `id`='".filter_var($_GET['custom_form'],FILTER_SANITIZE_STRING)."'")->fetch_assoc();
		$origin = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `type`='origin'"));
		$dest = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `type`='destination'"));
		$delivery = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `type`!='destination' AND `type`!='origin'"),MYSQLI_ASSOC);
		$general = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`qty`, `ticket_attached`.`received`, `ticket_attached`.`used`, `ticket_attached`.`description`, `ticket_attached`.`status`, `ticket_attached`.`po_line`, SUM(`ticket_attached`.`piece_num`) `piece_num`, `ticket_attached`.`piece_type`, `ticket_attached`.`used`, SUM(`ticket_attached`.`weight`) `weight`, `ticket_attached`.`weight_units`, `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units`, `ticket_attached`.`discrepancy`, `ticket_attached`.`backorder`, `ticket_attached`.`position`, `ticket_attached`.`notes`, `ticket_attached`.`contact_info`, `inventory`.`category`, `inventory`.`sub_category` FROM `ticket_attached` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` WHERE `ticket_attached`.`src_table`='inventory_general' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily));
		$shipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`qty`, `ticket_attached`.`weight`, `ticket_attached`.`weight_units`, `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units` FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='inventory_shipment' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily));
		$readings = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticket_attached`.* FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='readings' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily));
		$tank_readings = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticket_attached`.* FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='tank_readings' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily));
		$residues = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`qty`, `ticket_attached`.`volume`, `ticket_attached`.`description`, `ticket_attached`.`status` FROM `ticket_attached` WHERE `ticket_attached`.`description` != '' AND `ticket_attached`.`src_table`='residue' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
		$other_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`qty`, `ticket_attached`.`volume`, `ticket_attached`.`description`, `ticket_attached`.`status` FROM `ticket_attached` WHERE `ticket_attached`.`description` != '' AND `ticket_attached`.`src_table`='other_list' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
		$shipping_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`qty`, `ticket_attached`.`volume`, `ticket_attached`.`description`, `ticket_attached`.`status` FROM `ticket_attached` WHERE `ticket_attached`.`description` != '' AND `ticket_attached`.`src_table`='shipping_list' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);

		$pdf_pages = $dbc->query("SELECT `page` FROM `ticket_pdf_fields` WHERE `pdf_type`='{$form['id']}' AND `deleted`=0 GROUP BY `page`");
		echo '<form method="POST" action="" class="form-horizontal">';
		echo "<h2 class='pad-5'>".$form['pdf_name'].": $ticket</h2>";
		while($page = $pdf_pages->fetch_assoc()['page']) {
			echo "<h3 class='pad-10'>Page ".$page."</h3>";
			echo "<!--SELECT `fields`.*, `values`.`field_value`, `values`.`revision` FROM `ticket_pdf_fields` `fields` LEFT JOIN `ticket_pdf_field_values` `values` ON `fields`.`pdf_type`=`values`.`pdf_type` AND `fields`.`field_name`=`values`.`field_name` AND `values`.`ticketid`='$ticketid' AND $revision IN (`values`.`revision`,999999999) LEFT JOIN `ticket_pdf_field_values` `older` ON `values`.`ticketid`=`older`.`ticketid` AND `values`.`pdf_type`=`older`.`pdf_type` AND `values`.`field_name`=`older`.`field_name` AND `values`.`id` < `older`.`id` AND `older`.`revision` <= $revision WHERE `older`.`id` IS NULL AND `fields`.`pdf_type`='{$form['id']}' AND `fields`.`page`='$page' AND `fields`.`input_class` NOT IN ('editLink','revisionField') AND `fields`.`deleted`=0 ORDER BY `fields`.`sort`,`fields`.`id`";
			$fields = $dbc->query("SELECT `fields`.*, `values`.`field_value`, `values`.`revision` FROM `ticket_pdf_fields` `fields` LEFT JOIN `ticket_pdf_field_values` `values` ON `fields`.`pdf_type`=`values`.`pdf_type` AND `fields`.`field_name`=`values`.`field_name` AND `values`.`ticketid`='$ticketid' AND $revision IN (`values`.`revision`,999999999) LEFT JOIN `ticket_pdf_field_values` `older` ON `values`.`ticketid`=`older`.`ticketid` AND `values`.`pdf_type`=`older`.`pdf_type` AND `values`.`field_name`=`older`.`field_name` AND `values`.`id` < `older`.`id` AND `older`.`revision` <= $revision WHERE `older`.`id` IS NULL AND `fields`.`pdf_type`='{$form['id']}' AND `fields`.`page`='$page' AND `fields`.`input_class` NOT IN ('editLink','revisionField') AND `fields`.`deleted`=0 ORDER BY `fields`.`sort`,`fields`.`id`");
			echo ' Origin: '.print_r($origin,true).' Destination: '.print_r($dest,true).' General: '.print_r($general,true).' Shipment: '.print_r($shipment,true)."-->";
			while($field = $fields->fetch_assoc()) {
				$options = explode(':',$field['options']);
				echo '<div class="form-group"><!--Field: '.print_r($field,true).'-->
					<label class="control-label col-sm-4">'.$field['field_label'].'</label>
					<div class="col-sm-8">';
						$field_id = 0;
						$value = $field['default_value'];
						$values = explode(':',$value);
						if(count($values) > 1) {
							$defaults = explode('|',$values[1])[1];
							$values[1] = explode('|',$values[1])[0];
							$value = '';
							$onchange = '';
							switch($values[0]) {
								case 'ticket':
									$value = $get_ticket[$values[1]];
									break;
								case 'created_by':
									$field_id = $get_ticket['created_by'];
									$value = get_contact($dbc, $get_ticket['created_by'], ($values[1] == 'full_name' ? '' : $values[1]));
									break;
								case 'ticket_label':
									$value = substr(explode(' ',$ticket)[0],$values[1]);
									break;
								case 'delivery':
									if(is_array($delivery)) {
										$field_detail = explode('-',$values[1]);
										$delivery_type = $field_detail[0];
										$delivery_field = $field_detail[1];
										$value = '';
										foreach($delivery as $stop) {
											if($stop['type'] == $delivery_type) {
												$value = $stop[$delivery_field];
												break;
											}
										}
									} else {
										$value = '';
									}
									break;
								case 'origin':
									if(is_array($origin)) {
										foreach(explode('+',$values[1]) as $row => $field_line) {
											if($row > 0 && trim($value,"\n") == $value) {
												$value .= "\n";
											}
											foreach(explode(',',$field_line) as $field_detail) {
												$field_detail = explode('-',$field_detail);
												if(count($field_detail) > 1) {
													$field_id = $origin[$field_detail[0]];
													$value .= get_contact($dbc, $origin[$field_detail[0]], ($field_detail[1] == 'full_name' ? '' : $field_detail[1])).' ';
												} else if(!array_key_exists($field_detail[0],$origin)) {
													$value = trim($value).trim(str_replace(['FFMCOMMA','FFMCOLON','FFMDASH','FFMPLUS','FFMHASH','FFMSINQUOT'],[',',':','-','+','#',"'"],implode('-',$field_detail)),"'");
												} else {
													$value .= $origin[$field_detail[0]].' ';
												}
											}
										}
									} else {
										$value = '';
									}
									break;
								case 'destination':
									if(is_array($dest)) {
										foreach(explode('+',$values[1]) as $row => $field_line) {
											if($row > 0 && trim($value,"\n") == $value) {
												$value .= "\n";
											}
											foreach(explode(',',$field_line) as $field_detail) {
												$field_detail = explode('-',$field_detail);
												if(count($field_detail) > 1) {
													$field_id = $dest[$field_detail[0]];
													$value .= get_contact($dbc, $dest[$field_detail[0]], ($field_detail[1] == 'full_name' ? '' : $field_detail[1])).' ';
												} else if(!array_key_exists($field_detail[0],$dest)) {
													$value = trim($value).trim(str_replace(['FFMCOMMA','FFMCOLON','FFMDASH','FFMPLUS','FFMHASH','FFMSINQUOT'],[',',':','-','+','#',"'"],implode('-',$field_detail)),"'");
												} else {
													$value .= $dest[$field_detail[0]].' ';
												}
											}
										}
									} else {
										$value = '';
									}
									break;
								case 'general':
									if(is_array($general)) {
										foreach(explode('+',$values[1]) as $row => $field_line) {
											if($row > 0 && trim($value,"\n") == $value) {
												$value .= "\n";
											}
											foreach(explode(',',$field_line) as $field_detail) {
												$field_detail = explode('-',$field_detail);
												if(count($field_detail) > 1) {
													$field_id = $general[$field_detail[0]];
													$value .= get_contact($dbc, $general[$field_detail[0]], ($field_detail[1] == 'full_name' ? '' : $field_detail[1])).' ';
												} else if(!array_key_exists($field_detail[0],$general)) {
													$value = trim($value).trim(str_replace(['FFMCOMMA','FFMCOLON','FFMDASH','FFMPLUS','FFMHASH','FFMSINQUOT'],[',',':','-','+','#',"'"],implode('-',$field_detail)),"'");
												} else {
													$value .= $general[$field_detail[0]].' ';
												}
											}
										}
									} else {
										$value = '';
									}
									break;
								case 'shipment':
									if(is_array($shipment)) {
										foreach(explode('+',$values[1]) as $row => $field_line) {
											if($row > 0 && trim($value,"\n") == $value) {
												$value .= "\n";
											}
											foreach(explode(',',$field_line) as $field_detail) {
												$field_detail = explode('-',$field_detail);
												if(count($field_detail) > 1) {
													$field_id = $shipment[$field_detail[0]];
													$value .= get_contact($dbc, $shipment[$field_detail[0]], ($field_detail[1] == 'full_name' ? '' : $field_detail[1])).' ';
												} else if($field_detail[0] == 'cube_size') {
													$cube = 0;
													$cube_dim = '';
													$general_rows = mysqli_query($dbc, "SELECT `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units` FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='inventory_general' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
													while($general_line = $general_rows->fetch_assoc()) {
														$line_cube = 1;
														foreach(explode('x',$general_line['dimensions']) as $dim_i => $dim) {
															if($dim_i < 3) {
																$line_cube *= $dim / ($general_line['dimension_units'] == 'in' ? 12 : ($general_line['dimension_units'] == 'mm' ? 1000 : ($general_line['dimension_units'] == 'cm' ? 100 : 1)));
															}
															$cube_dim = $cube_dim == '' ? ($general_line['dimension_units'] == 'in' ? 'cu ft' : ($general_line['dimension_units'] == 'mm' ? 'cu m' : ($general_line['dimension_units'] == 'cm' ? 'cu m' : 'cu '.$general_line['dimension_units']))) : $cube_dim;
														}
														$cube += $line_cube;
													}
													$value .= round($cube,2).' '.$cube_dim.' ';
												} else if(!array_key_exists($field_detail[0],$shipment)) {
													$value = trim($value).trim(str_replace(['FFMCOMMA','FFMCOLON','FFMDASH','FFMPLUS','FFMHASH','FFMSINQUOT'],[',',':','-','+','#',"'"],implode('-',$field_detail)),"'");
												} else {
													$value .= $shipment[$field_detail[0]].' ';
												}
											}
										}
									} else {
										$value = '';
									}
									break;
								case 'general-row':
									$list_options = [];
									$include_label = [];
									$include_id = [];
									$details = explode('@',substr(implode(':',$values),12));
									$detail_sub = $details[1];
									$include_po = '';
									$include_po_confirm = '';
									$po_list = [];
									$i = 0;
									$general_rows = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`qty`, `ticket_attached`.`received`, `ticket_attached`.`used`, `ticket_attached`.`description`, `ticket_attached`.`status`, `ticket_attached`.`po_line`, `ticket_attached`.`piece_num`, `ticket_attached`.`piece_type`, `ticket_attached`.`used`, `ticket_attached`.`weight`, `ticket_attached`.`weight_units`, `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units`, `ticket_attached`.`discrepancy`, `ticket_attached`.`backorder`, `ticket_attached`.`position`, `ticket_attached`.`notes`, `ticket_attached`.`contact_info` FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='inventory_general' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
									$general_line = $general_rows->fetch_assoc();
									do {
										$value = '';
										foreach(explode('+',$details[0]) as $row => $field_line) {
											if($row > 0 && trim($value,"\n") == $value) {
												$value .= "\n";
											}
											foreach(explode(',',$field_line) as $field_detail) {
												if($field_detail == '#') {
													$value .= (++$i).' ';
												} else if(substr($field_detail,0,13) == 'WO_PO_CONFIRM') {
													$include_po_confirm = str_replace(['FFMCOMMA','FFMCOLON','FFMDASH','FFMPLUS','FFMHASH','FFMSINQUOT'],[',',':','-','+','#',"'"],substr($field_detail,13));
												} else if(substr($field_detail,0,5) == 'WO_PO') {
													$include_po = str_replace(['FFMCOMMA','FFMCOLON','FFMDASH','FFMPLUS','FFMHASH','FFMSINQUOT'],[',',':','-','+','#',"'"],substr($field_detail,5));
												} else if(!array_key_exists($field_detail,$general_line)) {
													$value = trim($value).trim(str_replace(['FFMCOMMA','FFMCOLON','FFMDASH','FFMPLUS','FFMHASH','FFMSINQUOT'],[',',':','-','+','#',"'"],$field_detail),"'");
												} else {
													$value .= $general_line[$field_detail].' ';
												}
											}
										}
										if($general_line['id'] > 0) {
											$include_label[] = $value;
											$include_id[] = $general_line['id'];
											if($detail_sub == 'inventory-row') {
												$inventory = mysqli_query($dbc, "SELECT `ticket_attached`.*, `inventory`.`name` FROM `ticket_attached` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` WHERE `src_table`='inventory' AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`line_id`='{$general_line['id']}'");
												while($inv_row = $inventory->fetch_assoc()) {
													$value .= "\n".$inv_row['qty'].' X '.$inv_row['name'].' Approx Weight '.$inv_row['weight'].' '.$inv_row['weight_units'].' Dimensions '.explode('#*#',$inv_row['dimensions'])[0].' '.explode('x',explode('#*#',$inv_row['dimension_units'])[0])[0];
												}
											}
											$list_options[] = $value;
										}
									} while($general_line = $general_rows->fetch_assoc());
									$require_value = '';
									if($include_po != '') {
										$require_value = $include_po.': '.implode(', ',array_filter(explode('#*#',$get_ticket['purchase_order'])));
									}
									if($include_po_confirm != '') {
										foreach(explode('#*#',$get_ticket['purchase_order']) as $po_row) {
											$po_list[] = $include_po_confirm.': '.$po_row;
										}
										$po_numbers = $dbc->query("SELECT `po_num` FROM `ticket_attached` WHERE `deleted`=0 AND `ticketid`='$ticketid' AND `src_table`='inventory' AND IFNULL(`po_num`,'') != ''");
										while($po_row = $po_numbers->fetch_assoc()) {
											$po_list[] = $include_po_confirm.': '.$po_row['po_num'];
										}
										$po_list = array_filter(array_unique($po_list));
										sort($po_list);
										foreach($po_list as $po_num) {
											$include_id[] = $po_num;
										}
									}
									$i = 0;
									if(in_array('confirm',$options)) {
										$checked = explode(',',$dbc->query("SELECT `field_value` FROM `ticket_pdf_field_values` `values` WHERE `ticketid`='$ticketid' AND `pdf_type`='{$form['id']}' AND `field_name`='included_".$field['field_name']."' AND '$revision' IN (`values`.`revision`, '999999999')")->fetch_assoc());
										foreach($list_options as $i => $option) {
											echo '<label class="form-checkbox"><input type="checkbox" name="included_'.$field['field_name'].'" data-text="'.htmlentities($option).'" onchange="setText(this);" '.(in_array($include_id[$i],$checked) ? 'checked' : '').' value="'.$include_id[$i].'">'.$include_label[$i].'</label>';
										}
										echo '<input type="checkbox" name="included_'.$field['field_name'].'" data-text="'.htmlentities($require_value).'" checked style="display:none;">';
										$value = $require_value;
									} else {
										$value = implode("\n",$list_options)."\n".$require_value;
									}
									if(count($po_list) > 0) {
										$checked = explode(',',$dbc->query("SELECT `field_value` FROM `ticket_pdf_field_values` `values` WHERE `ticketid`='$ticketid' AND `pdf_type`='{$form['id']}' AND `field_name`='included_".$field['field_name']."' AND '$revision' IN (`values`.`revision`, '999999999')")->fetch_assoc());
										foreach($po_list as $po_i => $option) {
											echo '<label class="form-checkbox"><input type="checkbox" name="included_'.$field['field_name'].'" data-text="'.htmlentities($option).'" onchange="setText(this);" value="'.$include_id[$po_i+$i].'">'.$option.'</label>';
										}
										$value .= $require_value;
									}
									break;
								case 'inventory-row':
									$inventory = mysqli_query($dbc, "SELECT `ticket_attached`.*, `inventory`.`name` FROM `ticket_attached` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` WHERE `src_table`='inventory' AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`ticketid`='$ticketid'");
									while($inv_row = $inventory->fetch_assoc()) {
										$value .= $inv_row['qty'].' X '.$inv_row['name'].' Approx Weight '.$inv_row['weight'].' '.$inv_row['weight_units'].' Dimensions '.explode('#*#',$inv_row['dimensions'])[0].' '.explode('x',explode('#*#',$inv_row['dimension_units'])[0])[0]."\n";
									}
									break;
								case 'contact-info':
									$field_detail = explode('-',$values[1]);
									$index = $field_detail[2];
									$index_separator = $field_detail[3];
									$field_detail_id = $get_ticket[$field_detail[0]];
									if($index != '' && !empty($index_separator)) {
										$field_i = 0;
										foreach(array_filter(explode($index_separator, $field_detail_id)) as $field_value) {
											if($field_i == $index) {
												$field_detail_id = $field_value;
												break;
											}
											$field_i++;
										}
									}
									if($field_detail_id > 0) {
										$value = get_contact($dbc, $field_detail_id, ($field_detail[1] == 'full_name' ? '' : $field_detail[1]));
									} else if ($field_detail_id != '') {
										$value = $field_detail_id;
									}
									break;
								case 'readings':
									$value = $readings[$values[1]];
									break;
								case 'tank-readings':
									$value = $readings[$values[1]];
									break;
								case 'checkbox':
									$field_detail = explode('-',$values[1]);
									$checkbox_checked = $get_ticket[$field_detail[0]] == $field_detail[1] ? 'checked' : '';
									$value = $field_detail[1];
									break;
								case 'checkbox_residue':
									$field_detail = explode('-',$values[1]);
									$checkbox_checked = 0;
									foreach($residues as $residue) {
										if($residue['description'] == $field_detail[0]) {
											$checkbox_checked = 1;
										}
									}
									$value = $field_detail[1];
									break;
								case 'checkbox_other_products':
									$field_detail = explode('-',$values[1]);
									$checkbox_checked = 0;
									foreach($other_list as $other) {
										if($other['description'] == $field_detail[0]) {
											$checkbox_checked = 1;
										}
									}
									$value = $field_detail[1];
									break;
								case 'other_products':
									$field_detail = explode('-',$values[1]);
									foreach($other_list as $other) {
										if($other['description'] == $field_detail[0]) {
											$value = $other[$field_detail[1]];
										}
									}
									break;
								case 'checkbox_shipping_list':
									$field_detail = explode('-',$values[1]);
									$checkbox_checked = 0;
									foreach($shipping_list as $shipping) {
										if($shipping['description'] == $field_detail[0]) {
											$checkbox_checked = 1;
										}
									}
									$value = $field_detail[1];
									break;
								case 'shipping_list':
									$field_detail = explode('-',$values[1]);
									foreach($shipping_list as $shipping) {
										if($shipping['description'] == $field_detail[0]) {
											$value = $shipping[$field_detail[1]];
										}
									}
									break;
								default:
									$value = implode(':',$values);
									break;
							}
							$value = $value ?: $defaults;
						} else if(array_key_exists($value,$get_ticket)) {
							$value = $get_ticket[$value];
						}
						$contact_option = array_search('contacts',$options);
						if($contact_option !== FALSE) {
							?>
							<select class="chosen-select-deselect" data-placeholder="Select <?= $options[$contact_options+2] ?>" onchange="updateTicket(this, '<?= $options[$contact_options+1] ?>')"><option />
								<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `name`, `first_name`, `last_name`, `ship_to_address`, `ship_city`, `ship_state`, `ship_zip`, `office_phone` FROM `contacts` WHERE `category`='".$options[$contact_options+2]."' AND `deleted`=0 AND `status` > 0")) as $contact) {
									$output = '';
									foreach(explode('+',$options[$contact_options+3]) as $option_line) {
										foreach(explode(',',$option_line) as $option_field) {
											if($option_field == 'full_name') {
												$output .= $contact['name'].' '.$contact['first_name'].' '.$contact['last_name'].' ';
											} else {
												$output .= $contact[$option_field].' ';
											}
										}
										$output = trim($output)."\n";
									} ?>
									<option <?= $contact['contactid'] == $field_id ? 'selected' : '' ?> value="<?= $contact['name'].' '.$contact['first_name'].' '.$contact['last_name'] ?>" data-output="<?= trim($output) ?>"><?= $contact['name'].' '.$contact['first_name'].' '.$contact['last_name'] ?></option>
								<?php } ?>
							</select>
						<?php }
						if(!empty($field['field_value']) && $_GET['pdf_mode'] == 'edit') {
							$value = $field['field_value'];
						}
						if(in_array('sync',$options)) {
							$i = array_search('sync',$options) + 1;
							$onchange = 'onchange="$(\'[name='.$options[$i].']\').val(this.value);"';
						}
						if(in_array('UPPER',$options)) {
							$value = strtoupper($value);
						} else if(in_array('lower',$options)) {
							$value = strtolower($value);
						} else if(in_array('Title',$options)) {
							$value = ucwords($value);
						} else if(in_array('phone',$options)) {
							$value = preg_replace('/[^0-9]/','',$str);
							switch(strlen($value)) {
								case 10:
									$value = substr($value,0,3).substr($value,3,3).substr($value,6,4);
									break;
								case 7:
									$value = substr($value,0,3).substr($value,3,4);
									break;
							}
						}
						if(in_array($values[0], ['checkbox','checkbox_residue','checkbox_other_products','checkbox_shipping_list'])) {
							echo '<label class="form-checkbox"><input type="checkbox" name="'.$field['field_name'].'" value="'.$value.'" '.$checkbox_checked.' onchange="updateChecked(this);"><input type="hidden" name="'.$field['field_name'].'" class="hidden_checkbox" value="" '.(empty($checkbox_checked) ? '' : 'disabled').'></label>';
						} else if($field['height'] > 7) {
							echo '<textarea class="form-control noMceEditor" rows="6" '.$onchange.' name="'.$field['field_name'].'">'.$value.'</textarea>';
						} else {
							echo '<input type="text" class="form-control '.$field['input_class'].'" '.$onchange.' name="'.$field['field_name'].'" value="'.$value.'">';
						}
					echo '</div>
				</div>';
			}
		}
		echo '<button name="custom_form" value="'.$form['id'].'" class="btn brand-btn pull-right" type="submit" onclick="return confirm(\'The Changes You Have Made Will Create a New Revision Document. Click Okay if this is Correct.\');">Save</button>
		<div class="clearfix"></div>
		</form>';
	}
} else { ?>
	<form method="GET" action="" class="form-horizontal">
		<?php $form = $dbc->query("SELECT * FROM `ticket_pdf` WHERE `id`='".filter_var($_GET['custom_form'],FILTER_SANITIZE_STRING)."'")->fetch_assoc(); ?>
		<h3>Create New <?= $form['pdf_name'] ?></h3>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?= TICKET_NOUN ?>:</label>
			<div class="col-sm-8">
				<select class="chosen-select-deselect" data-placeholder="Select a Ticket" name="ticketid"><option />
					<?php $ticket_list = $dbc->query("SELECT * FROM `tickets` WHERE `deleted`=0 AND `status` != 'Archive'".($_GET['projectid'] > 0 ? " AND `projectid`='{$_GET['projectid']}'" : ''));
					while($ticket = $ticket_list->fetch_assoc()) { ?>
						<option value="<?= $ticket['ticketid'] ?>"><?= get_ticket_label($dbc, $ticket) ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<button class="btn brand-btn pull-right" name="custom_form" value="<?= $_GET['custom_form'] ?>">Create Form</button>
		<div class="clearfix"></div>
	</form>
<?php }
