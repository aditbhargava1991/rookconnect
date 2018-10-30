<?php include_once('../include.php');
$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
$form = filter_var($_POST['custom_form'], FILTER_SANITIZE_STRING);
if(isset($_POST['custom_form'])) {
	require_once('../phpsign/signature-to-image.php');
	$ticketid = filter_var($_GET['ticketid'], FILTER_SANITIZE_STRING);
	$revision = 1 + mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`revision`) `revision` FROM `ticket_pdf_field_values` WHERE `ticketid`='$ticketid' AND `pdf_type`='$form' AND `deleted`=0"))['revision'];
	if($_POST['revision_mode'] == 'edit') {
		$revision = $_POST['revision_number'];
		mysqli_query($dbc, "UPDATE `ticket_pdf_field_values` SET `deleted` = 1 WHERE `ticketid` = '$ticketid' AND `pdf_type` = '$form' AND `revision` = '$revision'");
	}
	foreach($_POST as $field => $value) {
		if(strpos($field, 'ffmsignature_') !== FALSE) { 
			$field = explode('ffmsignature_', $field)[1];
            imagepng(sigJsonToImage($value), 'download/sign_'.$form.'_'.$field.'_'.$ticketid.'_'.$revision.'.png');
			$value = filter_var($value, FILTER_SANITIZE_STRING);
			$dbc->query("INSERT INTO `ticket_pdf_field_values` (`ticketid`, `pdf_type`, `revision`, `field_name`, `field_value`) VALUES ('$ticketid', '$form', '$revision', '$field', '$value')");
		} else if($field != 'custom_form') {
			$field = filter_var($field, FILTER_SANITIZE_STRING);
			if(is_array($value)) {
				$value = implode(',',$value);
			}
			$value = filter_var(htmlentities($value), FILTER_SANITIZE_STRING);
			$dbc->query("INSERT INTO `ticket_pdf_field_values` (`ticketid`, `pdf_type`, `revision`, `field_name`, `field_value`) VALUES ('$ticketid', '$form', '$revision', '$field', '$value')");
		}
	}
	echo "<script> window.location.replace('ticket_pdf_custom.php?form=$form&ticketid=$ticketid&revision=$revision&revision_mode=".$_POST['revision_mode']."'); </script>";
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
				var data_text = $('<textarea />').html($(this).data('text')).text();
				text = text+data_text+"\n";
			});
			if($(input).data('limit-note') != undefined && $(input).data('limit-note') != '') {
				text = text+$(input).data('limit-note');
			}
			block.find('input,textarea').last().val(text);
		}
		function updateTicket(select, field, update_ticket = 1, table_name = 'ticket_schedule') {
			if(update_ticket == 1) {
				if(confirm("Click OK to update the <?= TICKET_NOUN ?> with this contact?")) {
					$.post('ticket_ajax_all.php?action=manual_update', {
						table_name: table_name,
						field_name: field.split('-')[1],
						value: select.value,
						ticketid: <?= $ticketid ?>,
						identifier: 'type',
						id: field.split('-')[0]
					}, function(response) { console.log(response); });
				}
			}
			$(select).nextAll('input,textarea').first().val($(select).find('option:selected').data('output')).change();
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
		echo '<input type="hidden" name="revision_mode" value="'.$_GET['revision_mode'].'">';
		echo '<input type="hidden" name="revision_number" value="'.$_GET['revision'].'">';

		while($page = $pdf_pages->fetch_assoc()['page']) {
			echo "<h3 class='pad-10'>Page ".$page."</h3>";
			echo "<!--SELECT `fields`.*, `values`.`field_value`, `values`.`revision` FROM `ticket_pdf_fields` `fields` LEFT JOIN `ticket_pdf_field_values` `values` ON `fields`.`pdf_type`=`values`.`pdf_type` AND `fields`.`field_name`=`values`.`field_name` AND `values`.`ticketid`='$ticketid' AND $revision IN (`values`.`revision`,999999999) AND `values`.`deleted`=0 LEFT JOIN `ticket_pdf_field_values` `older` ON `values`.`ticketid`=`older`.`ticketid` AND `values`.`pdf_type`=`older`.`pdf_type` AND `values`.`field_name`=`older`.`field_name` AND `values`.`id` < `older`.`id` AND `older`.`revision` <= $revision AND `older`.`deleted`=0 WHERE `older`.`id` IS NULL AND `fields`.`pdf_type`='{$form['id']}' AND `fields`.`page`='$page' AND `fields`.`input_class` NOT IN ('editLink','revisionField') AND `fields`.`deleted`=0 ORDER BY `fields`.`sort`,`fields`.`id`";
			$fields = $dbc->query("SELECT `fields`.*, `values`.`field_value`, `values`.`revision` FROM `ticket_pdf_fields` `fields` LEFT JOIN `ticket_pdf_field_values` `values` ON `fields`.`pdf_type`=`values`.`pdf_type` AND `fields`.`field_name`=`values`.`field_name` AND `values`.`ticketid`='$ticketid' AND $revision IN (`values`.`revision`,999999999) AND `values`.`deleted`=0 LEFT JOIN `ticket_pdf_field_values` `older` ON `values`.`ticketid`=`older`.`ticketid` AND `values`.`pdf_type`=`older`.`pdf_type` AND `values`.`field_name`=`older`.`field_name` AND `values`.`id` < `older`.`id` AND `older`.`revision` <= $revision AND `older`.`deleted`=0 WHERE `older`.`id` IS NULL AND `fields`.`pdf_type`='{$form['id']}' AND `fields`.`page`='$page' AND `fields`.`input_class` NOT IN ('editLink','revisionField') AND `fields`.`deleted`=0 ORDER BY `fields`.`sort`,`fields`.`id`");
			echo ' Origin: '.print_r($origin,true).' Destination: '.print_r($dest,true).' General: '.print_r($general,true).' Shipment: '.print_r($shipment,true)."-->";
			while($field = $fields->fetch_assoc()) {
				$options = explode(':',$field['options']);
				$initial_options = $options;
				$option_details = [];
				foreach($options as $key => $option) {
					$option_details[explode('-',$option)[0]] = $option;
					$options[$key] = explode('-',$option)[0];
				}
				$field_options = [];
				if(in_array('mandatory',$options)) {
					$field_options[] = 'required';
				} else if(in_array('read',$options)) {
					$field_options[] = 'readonly';
				}
				echo '<div class="form-group"><!--Field: '.print_r($field,true).'-->
					<label class="control-label col-sm-4">'.$field['field_label'].(in_array('required',$field_options) ? '<span class="text-red">*</span>' : '').'</label>
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
								case 'session_user':
									$session_user = array_shift(sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$_SESSION['contactid']."'")));
									foreach(explode('+',$values[1]) as $row => $field_line) {
										if($row > 0 && trim($value,"\n") == $value) {
											$value .= "\n";
										}
										foreach(explode(',',$field_line) as $field_detail) {
											if(array_key_exists($field_detail,$session_user)) {
												$value .= $session_user[$field_detail];
											} else {
												$value .= $field_detail.' ';
											}
										}
									}
									break;
								case 'get_ticket_label':
									$value = get_ticket_label($dbc, $get_ticket, null, null, $values[1]);
									break;
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
												} else if(explode('*#*',$field_detail[0])[0] == 'cube_size') {
													$size_details = explode('*#*',$field_detail[0]);
													$field_detail[0] = $size_details[0];
													$cube = 0;
													$cube_dim = '';
													$general_rows = mysqli_query($dbc, "SELECT `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units` FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='inventory_general' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
													while($general_line = $general_rows->fetch_assoc()) {
														$line_cube = 1;
														foreach(explode('x',$general_line['dimensions']) as $dim_i => $dim) {
															if($size_details[1] == 'cu_ft') {
																$dim = $dim / ($general_line['dimension_units'] == 'mm' ? 25.4 : ($general_line['dimension_units'] == 'cm' ? 2.54 : 1));
																$general_line['dimension_units'] = 'in';
															}
															if($dim_i < 3) {
																$line_cube *= $dim / ($general_line['dimension_units'] == 'in' ? 12 : ($general_line['dimension_units'] == 'mm' ? 1000 : ($general_line['dimension_units'] == 'cm' ? 100 : 1)));
															}
															$cube_dim = $cube_dim == '' ? ($general_line['dimension_units'] == 'in' ? 'cu ft' : ($general_line['dimension_units'] == 'mm' ? 'cu m' : ($general_line['dimension_units'] == 'cm' ? 'cu m' : 'cu '.$general_line['dimension_units']))) : $cube_dim;
														}
														$cube += $line_cube;
													}
													$value .= round($cube,2).' '.$cube_dim.' ';
												} else if(explode('*#*',$field_detail[0])[0] == 'dimensions_inch') {
													$size_details = explode('*#*',$field_detail[0]);
													$field_detail[0] = $size_details[0];
													$quantity_line = [];
													$length_line = [];
													$width_line = [];
													$height_line = [];
													$general_rows = mysqli_query($dbc, "SELECT SUM(`qty`) `qty`, `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units` FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='inventory_general' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily." GROUP BY CONCAT(IFNULL(`ticket_attached`.`dimensions`,''),IFNULL(`ticket_attached`.`dimension_units`,''))");
													while($general_line = $general_rows->fetch_assoc()) {
														$length_inch = 0;
														$width_inch = 0;
														$height_inch = 0;
														foreach(explode('x',$general_line['dimensions']) as $dim_i => $dim) {
															if($dim_i == 0) {
																$length_inch = $dim / ($general_line['dimension_units'] == 'mm' ? 25.4 : ($general_line['dimension_units'] == 'cm' ? 2.54 : 1));
															} else if($dim_i == 1) {
																$width_inch = $dim / ($general_line['dimension_units'] == 'mm' ? 25.4 : ($general_line['dimension_units'] == 'cm' ? 2.54 : 1));
															} else if($dim_i == 2) {
																$height_inch = $dim / ($general_line['dimension_units'] == 'mm' ? 25.4 : ($general_line['dimension_units'] == 'cm' ? 2.54 : 1));
															}
														}
														$length_line[] = round($length_inch,2);
														$width_line[] = round($width_inch,2);
														$height_line[] = round($height_inch,2);
														$quantity_line[] = $general_line['qty'];
													}
													if($size_details[1] == 'length_inch') {
														$value .= implode("\n",$length_line);
													} else if($size_details[1] == 'width_inch') {
														$value .= implode("\n",$width_line);
													} else if($size_details[1] == 'height_inch') {
														$value .= implode("\n",$height_line);
													} else if($size_details[1] == 'quantity') {
														$value .= implode("\n",$quantity_line);
													}
												} else if($field_detail[0] == 'piece_types_count') {
													$general_rows = mysqli_query($dbc, "SELECT `ticket_attached`.`piece_type`, SUM(`qty`) `qty` FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='inventory_general' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily." GROUP BY `ticket_attached`.`piece_type`");
													$piece_types = [];
													while($general_line = $general_rows->fetch_assoc()) {
														$piece_types[] = $general_line['qty'].' x '.$general_line['piece_type'];
													}
													$piece_types = implode("\n", $piece_types);
													$value .= $piece_types;
												} else if($field_detail[0] == 'piece_types_count_dim') {
													$general_rows = mysqli_query($dbc, "SELECT `ticket_attached`.`piece_type`, SUM(`qty`) `qty`, `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units` FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='inventory_general' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily." GROUP BY CONCAT(IFNULL(`ticket_attached`.`piece_type`,''),IFNULL(`ticket_attached`.`dimensions`,''),IFNULL(`ticket_attached`.`dimension_units`,''))");
													$piece_types = [];
													while($general_line = $general_rows->fetch_assoc()) {
														$length_inch = 0;
														$width_inch = 0;
														$height_inch = 0;
														foreach(explode('x',$general_line['dimensions']) as $dim_i => $dim) {
															if($dim_i == 0) {
																$length_inch = $dim / ($general_line['dimension_units'] == 'mm' ? 25.4 : ($general_line['dimension_units'] == 'cm' ? 2.54 : 1));
															} else if($dim_i == 1) {
																$width_inch = $dim / ($general_line['dimension_units'] == 'mm' ? 25.4 : ($general_line['dimension_units'] == 'cm' ? 2.54 : 1));
															} else if($dim_i == 2) {
																$height_inch = $dim / ($general_line['dimension_units'] == 'mm' ? 25.4 : ($general_line['dimension_units'] == 'cm' ? 2.54 : 1));
															}
														}
														$piece_types[] = $general_line['qty'].' x '.$length_inch."'x".$width_inch."'x".$height_inch."' ".$general_line['piece_type'];
													}
													$piece_types = implode("\n", $piece_types);
													$value .= $piece_types;
												} else if($field_detail[0] == 'piece_types') {
													$general_rows = mysqli_query($dbc, "SELECT DISTINCT `ticket_attached`.`piece_type` FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='inventory_general' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0 AND IFNULL(`piece_type`,'') != ''".$query_daily);
													$piece_types = [];
													while($general_line = $general_rows->fetch_assoc()) {
														$piece_types[] = $general_line['piece_type'];
													}
													$piece_types = implode(', ', $piece_types);
													$value .= $piece_types;
												} else if($field_detail[0] == 'total_length') {

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
									$limit = '';
									$offset = '';
									$limit_note = '';
									$limit_query = '';
									if(in_array('limit',$options)) {
										$limit_details = explode('-',$option_details['limit']);
										$limit = ($limit_details[1] >= 0 ? $limit_details[1] : 9999999);
										$offset = ($limit_details[2] >= 0 ? $limit_details[2] : 0);
										$limit_note = $limit_details[3];
										$limit_query = " LIMIT $limit OFFSET $offset";
									}
									$list_options = [];
									$include_label = [];
									$include_id = [];
									$details = explode('@',substr(implode(':',$values),12));
									$detail_sub = $details[1];
									$include_po = '';
									$include_po_confirm = '';
									$po_list = [];
									$general_po_nums = [];
									$i = ($offset > 0 ? $offset : 0);
									$general_rows = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`qty`, `ticket_attached`.`received`, `ticket_attached`.`used`, `ticket_attached`.`description`, `ticket_attached`.`status`, `ticket_attached`.`po_line`, `ticket_attached`.`piece_num`, `ticket_attached`.`piece_type`, `ticket_attached`.`used`, `ticket_attached`.`weight`, `ticket_attached`.`weight_units`, `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units`, `ticket_attached`.`discrepancy`, `ticket_attached`.`backorder`, `ticket_attached`.`position`, `ticket_attached`.`notes`, `ticket_attached`.`contact_info`, `ticket_attached`.`po_num` FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='inventory_general' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily.$limit_query);
									$general_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`ticket_attached`.`id`) `num_rows` FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='inventory_general' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily))['num_rows'];
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
										foreach(explode('#*#',$general_line['po_num']) as $general_po_num) {
											$general_po_nums[] = $general_po_num;
										}
									} while($general_line = $general_rows->fetch_assoc());
									$general_po_nums = implode('#*#', $general_po_nums);
									$require_value = '';
									if($include_po != '') {
										$require_value = $include_po.': '.implode(', ',array_unique(array_filter(explode('#*#',$get_ticket['purchase_order'].'#*#'.$general_po_nums))));
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
										$checked = explode(',',$dbc->query("SELECT `field_value` FROM `ticket_pdf_field_values` `values` WHERE `ticketid`='$ticketid' AND `pdf_type`='{$form['id']}' AND `field_name`='included_".$field['field_name']."' AND '$revision' IN (`values`.`revision`, '999999999') AND `deleted`=0")->fetch_assoc()['field_value']);
										foreach($list_options as $i => $option) {
											echo '<label class="form-checkbox"><input type="checkbox" name="included_'.$field['field_name'].'[]" data-limit-note="'.($general_count > $limit ? $limit_note : '').'" data-text="'.htmlentities($option).'" onchange="setText(this);" '.(in_array($include_id[$i],$checked) ? 'checked' : '').' value="'.$include_id[$i].'">'.$include_label[$i].'</label>';
										}
										echo '<input type="checkbox" name="included_'.$field['field_name'].'[]" data-text="'.htmlentities($require_value).'" checked style="display:none;">';
										$value = $require_value;
									} else {
										$value = implode("\n",$list_options)."\n".$require_value;
									}
									if(count($po_list) > 0) {
										$checked = explode(',',$dbc->query("SELECT `field_value` FROM `ticket_pdf_field_values` `values` WHERE `ticketid`='$ticketid' AND `pdf_type`='{$form['id']}' AND `field_name`='included_".$field['field_name']."' AND '$revision' IN (`values`.`revision`, '999999999') AND `deleted`=0")->fetch_assoc()['field_value']);
										foreach($po_list as $po_i => $option) {
											echo '<label class="form-checkbox"><input type="checkbox" name="included_'.$field['field_name'].'[]"  data-limit-note="'.($general_count > $limit ? $limit_note : '').'" data-text="'.htmlentities($option).'" onchange="setText(this);" value="'.$include_id[$po_i+$i].'">'.$option.'</label>';
										}
										$value .= $require_value;
									}
									if(in_array('limit',$options) && $general_count > $limit && !empty($limit_note)) {
										$value .= "\n".$limit_note;
										echo '<br /><b>'.$limit_note.'</b>';
									}
									break;
								case 'inventory-row':
									$inventory = mysqli_query($dbc, "SELECT `ticket_attached`.*, `inventory`.`name` FROM `ticket_attached` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` WHERE `src_table`='inventory' AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`ticketid`='$ticketid'");
									while($inv_row = $inventory->fetch_assoc()) {
										$value .= $inv_row['qty'].' X '.$inv_row['name'].' Approx Weight '.$inv_row['weight'].' '.$inv_row['weight_units'].' Dimensions '.explode('#*#',$inv_row['dimensions'])[0].' '.explode('x',explode('#*#',$inv_row['dimension_units'])[0])[0]."\n";
									}
									break;
								case 'inventory-row-detail':
									$inventory = mysqli_query($dbc, "SELECT `ticket_attached`.*, `inventory`.`name` FROM `ticket_attached` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` WHERE `src_table`='inventory' AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`ticketid`='$ticketid'");
									while($inv_row = $inventory->fetch_assoc()) {
										foreach(explode('+',$values[1]) as $row => $field_line) {
											if($row > 0 && trim($value,"\n") == $value) {
												$value .= "\n";
											}
											foreach(explode(',',$field_line) as $field_detail) {
												$field_detail = explode('-',$field_detail);
												if(!array_key_exists($field_detail[0],$inv_row)) {
													$value = trim($value).trim(str_replace(['FFMCOMMA','FFMCOLON','FFMDASH','FFMPLUS','FFMHASH','FFMSINQUOT'],[',',':','-','+','#',"'"],implode('-',$field_detail)),"'");
												} else {
													$value .= $inv_row[$field_detail[0]].' ';
												}
											}
										}
										$value .= "\n";
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
										if($field_detail_id == $get_ticket[$field_detail[0]]) {
											$field_detail_id = '';
										}
									}
									if($field_detail_id > 0) {
										$contact_info = array_shift(sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$field_detail_id."'")));

										foreach(explode('+',$field_detail[1]) as $row => $field_line) {
											if($row > 0 && trim($value,"\n") == $value) {
												$value .= "\n";
											}
											foreach(explode(',',$field_line) as $contact_detail) {
												if(!array_key_exists($contact_detail,$contact_info)) {
													$value = trim($value).trim(str_replace(['FFMCOMMA','FFMCOLON','FFMDASH','FFMPLUS','FFMHASH','FFMSINQUOT'],[',',':','-','+','#',"'"],implode('-',$contact_detail)),"'");
												} else {
													$value .= $contact_info[$contact_detail].' ';
												}
											}
										}
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
							<select class="contact_select chosen-select-deselect" data-placeholder="Select <?= $options[$contact_options+2] ?>" onchange="updateTicket(this, '<?= $initial_options[$contact_options+1] ?>', 1, <?= ($options[$contact_options+4] == 'tickets' ? "'tickets'" : "'ticket_schedule'") ?>)"><option />
								<?php foreach(sort_contacts_query($dbc->query("SELECT * FROM `contacts` WHERE `deleted`=0 AND `status` > 0".(empty($options[$contact_options+2]) ? '' :  " AND `category`='".$options[$contact_options+2]."'"))) as $contact) {
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
									<option <?= $contact['contactid'] == $field_id ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>" data-output="<?= trim($output) ?>"><?= $contact['name'].' '.$contact['first_name'].' '.$contact['last_name'] ?></option>
								<?php } ?>
							</select>
						<?php }
						$contact_reference = array_search('contactsreference',$options);
						if($contact_reference !== FALSE) {
							?>
							<script type="text/javascript">
							$(document).on('change', '[name="<?= $options[$contact_options+1] ?>"]', function() {
								var select_value = $(this).closest('.form-group').find('.contact_select').val()
								$('[name="<?= $field['field_name'] ?>"]').closest('.form-group').find('.hidden_select').val(select_value).change();
							});
							</script>
							<select class="hidden_select" data-placeholder="Select <?= $options[$contact_options+3] ?>" onchange="updateTicket(this, '<?= $options[$contact_options+2] ?>', 0)" style="display: none;"><option />
								<?php foreach(sort_contacts_query($dbc->query("SELECT * FROM `contacts` WHERE `category`='".$options[$contact_options+3]."' AND `deleted`=0 AND `status` > 0")) as $contact) {
									$output = '';
									foreach(explode('+',$options[$contact_options+4]) as $option_line) {
										foreach(explode(',',$option_line) as $option_field) {
											if($option_field == 'full_name') {
												$output .= $contact['name'].' '.$contact['first_name'].' '.$contact['last_name'].' ';
											} else {
												$output .= $contact[$option_field].' ';
											}
										}
										$output = trim($output)."\n";
									} ?>
									<option <?= $contact['contactid'] == $field_id ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>" data-output="<?= trim($output) ?>"><?= $contact['name'].' '.$contact['first_name'].' '.$contact['last_name'] ?></option>
								<?php } ?>
							</select>
						<?php }
						$onchange = '';
						if(!empty($field['field_value']) && $_GET['pdf_mode'] == 'edit') {
							$value = $field['field_value'];
						}
						if(in_array('sync',$options)) {
							$i = array_search('sync',$options) + 1;
							$onchange = 'onchange="$(\'[name='.$options[$i].']\').val(this.value).change();"';
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
						if($field['input_class'] == 'signature') {
							$output_name = 'ffmsignature_'.$field['field_name'];
							$output_value = $value;
							include('../phpsign/sign_multiple.php');
						} else if(in_array($values[0], ['checkbox','checkbox_residue','checkbox_other_products','checkbox_shipping_list'])) {
							echo '<label class="form-checkbox"><input type="checkbox" name="'.$field['field_name'].'" value="'.$value.'" '.$checkbox_checked.' onchange="updateChecked(this);"><input type="hidden" name="'.$field['field_name'].'" class="hidden_checkbox" value="" '.(empty($checkbox_checked) ? '' : 'disabled').'></label>';
						} else if($field['height'] > 7) {
							echo '<textarea class="form-control noMceEditor" '.implode(' ',$field_options).' rows="6" '.$onchange.' name="'.$field['field_name'].'">'.$value.'</textarea>';
						} else {
							echo '<input type="text" '.implode(' ',$field_options).' class="form-control '.$field['input_class'].'" '.$onchange.' name="'.$field['field_name'].'" value="'.$value.'">';
						}
					echo '</div>
				</div>';
			}
		}
		echo '<button name="custom_form" value="'.$form['id'].'" class="btn brand-btn pull-right" type="submit" onclick="if($(\'[required]\').filter(function() { return this.value == \'\'; }).length > 0) { alert(\'Please complete all required fields.\'); return false; } else { return confirm(\''.($_GET['revision_mode'] == 'edit' ? 'The Changes You Have Made Will Replace This Revisions Document. Click Okay if this is Correct.' : 'The Changes You Have Made Will Create a New Revision Document. Click Okay if this is Correct').'\'); }">Save</button>
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
					<?php $ticket_list = "SELECT * FROM `tickets` WHERE `deleted`=0 AND `status` != 'Archive' AND `ticket_type` IN ('".implode("','",$ticket_conf_list)."')".(empty($form['ticket_types']) || $form['ticket_types'] == 'ALL' ? '' : (" AND `ticket_type` IN ('".config_safe_str($form['ticket_types'])."')")).($_GET['projectid'] > 0 ? " AND `projectid`='{$_GET['projectid']}'" : '');
					$ticket_list = $dbc->query($ticket_list);
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
