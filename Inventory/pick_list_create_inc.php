<?php include_once('../include.php');
$match_business = '';
if(!empty(MATCH_CONTACTS)) {
	$match_business = " AND `tickets`.`businessid` IN (".MATCH_CONTACTS.")";
}
if($_POST['save'] == 'save' || !empty($_POST['get_all'])) {
	$picklistid = $_POST['picklistid'];
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	$completed = filter_var($_POST['completed'],FILTER_SANITIZE_STRING);
	$deleted = filter_var($_POST['archived'],FILTER_SANITIZE_STRING);
	if($picklistid > 0) {
		$dbc->query("UPDATE `pick_lists` SET `name`='$name', `businessid`='$businessid', `projectid`='$projectid', `completed`='$completed', `deleted`='$deleted' WHERE `id`='$picklistid'");
	} else {
		$dbc->query("INSERT INTO `pick_lists` (`name`,`businessid`,`projectid`,`created_by`,`completed`,`deleted`) VALUES ('$name','$businessid','$projectid','{$_SESSION['contactid']}','$completed','$deleted')");
		$picklistid = $dbc->insert_id;
	}
	$errors = [];
	foreach($_POST['inventoryid'] as $i => $inventory) {
		if($inventory > 0) {
			$available = $dbc->query("SELECT `inventory`.`quantity` - CAST(`inventory`.`assigned_qty` AS SIGNED INT) `available`, `name`, `product_name` FROM `inventory` WHERE `inventoryid`='$inventory'")->fetch_assoc();
			$id = filter_var($_POST['id'][$i],FILTER_SANITIZE_STRING);
			$quantity = filter_var($_POST['quantity'][$i],FILTER_SANITIZE_STRING);
			$deleted = filter_var($_POST['deleted'][$i],FILTER_SANITIZE_STRING);
			if($id > 0) {
				$old_qty = $dbc->query("SELECT `quantity` FROM `pick_list_items` WHERE `id`='$id'")->fetch_array()[0];
				$qty_diff = $quantity - $old_qty;
				if($qty_diff > $available['available']) {
					$errors[] = "Only {$available['available']} available for ".trim($available['product_name'].' '.$available['name']).". Quantity has been reduced.";
					$quantity -= ($qty_diff - $available['available']);
					$qty_diff = $available['available'];
				}
				$dbc->query("UPDATE `pick_list_items` SET `inventoryid`='$inventory', `quantity`='$quantity', `deleted`='$deleted' WHERE `id`='$id'");

				$before_change = capture_before_change($dbc, 'inventory', 'assigned_qty', 'inventoryid', $inventory);
				$dbc->query("UPDATE `inventory` SET `assigned_qty`=(`assigned_qty` + $qty_diff) WHERE `inventoryid`='$inventory'");
				$history = capture_after_change('assigned_qty', $qty_diff);
				add_update_history($dbc, 'inventory_history', $history, '', $before_change);

				$dbc->query("INSERT INTO `inventory_change_log` (`inventoryid`,`contactid`,`location_of_change`,`date_time`,`change_comment`) SELECT `inventoryid`,'{$_SESSION['contactid']}','Pick List: $name',NOW(),'Assigned Quantity updated by $qty_diff' FROM `inventory` WHERE `inventoryid`='$inventory'");
			} else {
				if($quantity > $available['available']) {
					$errors[] = "Only {$available['available']} available for ".trim($available['product_name'].' '.$available['name']).". Quantity has been reduced.";
					$quantity = $available['available'];
				}
				$dbc->query("INSERT INTO `pick_list_items` (`pick_list`,`inventoryid`,`quantity`,`deleted`) VALUES ('$picklistid','$inventory','$quantity','$deleted')");
				$before_change = capture_before_change($dbc, 'inventory', 'assigned_qty', 'inventoryid', $inventory);
				$dbc->query("UPDATE `inventory` SET `assigned_qty`=(`assigned_qty` + $quantity) WHERE `inventoryid`='$inventory'");
				$history = capture_after_change('assigned_qty', $quantity);
				add_update_history($dbc, 'inventory_history', $history, '', $before_change);
				
				$dbc->query("INSERT INTO `inventory_change_log` (`inventoryid`,`contactid`,`location_of_change`,`date_time`,`change_comment`) SELECT `inventoryid`,'{$_SESSION['contactid']}','Pick List: $name',NOW(),'$quantity Assigned to Pick List' FROM `inventory` WHERE `inventoryid`='$inventory'");
			}
		}
	}
	if(count($errors) > 0) {
		echo '<script> alert("The following inventory had issues:\n'.implode("\n",$errors).'")</script>';
	}
	if(!empty($_POST['get_all'])) { ?>
		<script>
		$(document).ready(function() {
			overlayIFrameSlider('pick_list_display_all.php?id=<?= $picklistid ?>&filters=<?= $_POST['get_all'] ?>','auto',true,true);
		});
		</script>
	<?php }
	$_GET['edit'] = $picklistid;
} else if($_GET['list'] > 0 && !empty($_GET['pdf'])) {
	$pdf_style = filter_var($_GET['pdf'],FILTER_SANITIZE_STRING);
	$pick_list = $dbc->query("SELECT * FROM `pick_lists` WHERE `id`='{$_GET['list']}'")->fetch_assoc();
	$filename = "pick_list_".$_GET['list'].".pdf";
	$html = "List Name: ".$pick_list['name']."<br />";
	$html .= BUSINESS_CAT.": ".get_contact($dbc, $pick_list['businessid'],'name')."<br />";
	$html .= PROJECT_NOUN.": ".get_project_label($dbc, $dbc->query("SELECT * FROM `project` WHERE `projectid`='{$pick_list['projectid']}'")->fetch_assoc())."<br />";
	$html .= "Completed: ".($pick_list['completed'] > 0 ? 'Yes' : 'No');
	if($pick_list['signature'] != '' && !file_exists('download/signature_'.$pick_list['id'].'.png')) {
		if (!file_exists('download')) {
			mkdir('download', 0777, true);
		}
		include_once('../phpsign/signature-to-image.php');
		$signature = sigJsonToImage(html_entity_decode($pick_list['signature']));
		imagepng($signature, 'download/signature_'.$pick_list['id'].'.png');
	}
	if($pick_list['signature'] != '') {
		$html .= '<br /><img src="download/signature_'.$pick_list['id'].'.png">';
	}
	$html .= "<br />";
	$html .= "Archived: ".($pick_list['deleted'] > 0 ? 'Yes' : 'No')."<br />";
	$pick_list_filters = array_filter(explode(',',get_config($dbc, 'pick_list_filters')));
	$html .= '<table style="width:100%;">
		<tr>
			'.(in_array('category',$pick_list_filters) ? '<th>Tab</th>' : '').'
			'.(in_array('ticket_po',$pick_list_filters) ? '<th>Purchase Order #</th>' : '').'
			'.(in_array('ticket',$pick_list_filters) ? '<th>'.TICKET_NOUN.'</th>' : '').'
			'.(in_array('ticket_customer_order',$pick_list_filters) ? '<th>Customer Order #</th>' : '').'
			'.(in_array('detail_customer_order',$pick_list_filters) ? '<th>Customer Order #</th>' : '').'
			'.(in_array('pallet',$pick_list_filters) ? '<th>Pallet #</th>' : '').'
			<th>'.INVENTORY_NOUN.'</th>
			<th>Available</th>
			<th>Quantity</th>
			<th>Filled</th>
		</tr>';
		$items = $dbc->query("SELECT `pick_list_items`.*, `inventory`.`product_name`, `inventory`.`name`, `inventory`.`quantity` - `inventory`.`assigned_qty` `available`, `inventory`.`category`, `inventory`.`pallet`, `tickets`.`ticketid`, `tickets`.`purchase_order`, `tickets`.`customer_order_num`, `tickets`.`position` FROM `pick_list_items` LEFT JOIN `inventory` ON `pick_list_items`.`inventoryid`=`inventory`.`inventoryid` LEFT JOIN (SELECT `tickets`.`ticketid`, `ticket_label`, `item_id`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `purchase_order`, `customer_order_num`, `tickets`.`businessid`, `ticket_attached`.`position` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`deleted`=0 AND `tickets`.`deleted`=0 AND `ticket_attached`.`src_table` IN ('inventory','inventory_detailed')) `tickets` ON `inventory`.`inventoryid`=`tickets`.`item_id` WHERE `pick_list_items`.`pick_list`='{$_GET['list']}' AND `pick_list_items`.`deleted`=0 AND `pick_list_items`.`pick_list` > 0 $match_business");
		while($item = $items->fetch_assoc()) {
			$html .= '<tr>
				'.(in_array('category',$pick_list_filters) ? '<td>'.$item['category'].'</td>' : '').'
				'.(in_array('ticket_po',$pick_list_filters) ? '<td>'.implode('<br />',array_filter(explode('#*#',$item['purchase_order']))).'</td>' : '').'
				'.(in_array('ticket',$pick_list_filters) ? '<td>'.$item['ticket_label'].'</td>' : '').'
				'.(in_array('ticket_customer_order',$pick_list_filters) ? '<td>'.$item['customer_order_num'].'</td>' : '').'
				'.(in_array('detail_customer_order',$pick_list_filters) ? '<td>'.$item['position'].'</td>' : '').'
				'.(in_array('pallet',$pick_list_filters) ? '<td>'.$item['pallet'].'</td>' : '').'
				<td>'.trim($item['product_name'].' '.$item['name']).'</td>
				<td>'.($item['available'] + $item['quantity']).'</td>
				<td>'.$item['quantity'].'</td>
				<td>'.$item['filled'].'</td>
			</tr>';
		}
	$html .= '</table>';

	$select_pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "select * from inventory_pdf_setting where style = '$pdf_style'"));
	if(!empty($select_pdf_settings)) {
	    $file_name = $select_pdf_settings['file_name'];

	    $font_heading_size = $select_pdf_settings['font_size'];
	    $font_heading_type = $select_pdf_settings['font_type'];
	    $font_heading = $select_pdf_settings['font'];

	    $font_main_heading_size = $select_pdf_settings['font_size'];
	    $font_main_heading_type = $select_pdf_settings['font_type'];
	    $font_main_heading = $select_pdf_settings['font'];

	    $font_main_body_size = $select_pdf_settings['font_size'];
	    $font_main_body_type = $select_pdf_settings['font_type'];
	    $font_main_body = $select_pdf_settings['font'];

	    $font_footer_size = $select_pdf_settings['font_size'];
	    $font_footer_type = $select_pdf_settings['font_type'];
	    $font_footer = $select_pdf_settings['font'];

	    $pdf_header_logo = $select_pdf_settings['pdf_logo'];

	    $pdf_size = $select_pdf_settings['pdf_size'];
	    $page_ori = $select_pdf_settings['page_ori'];
	    $units = $select_pdf_settings['units'];
	    $margin_left = $select_pdf_settings['left_margin'];
	    $margin_right = $select_pdf_settings['right_margin'];
	    $margin_top = $select_pdf_settings['top_margin'];
	    $margin_header = $select_pdf_settings['header_margin'];
	    $margin_bottom = $select_pdf_settings['bottom_margin'];

	    $heading_color = $select_pdf_settings['pdf_color'];
	    $main_body_color = $select_pdf_settings['pdf_color'];
	    $main_heading_color = $select_pdf_settings['pdf_color'];
	    $footer_color = $select_pdf_settings['pdf_color'];
	}

	$select_header_pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "select * from inventory_pdf_setting where style = '$pdf_style' AND setting_type = 'header'"));
	if(!empty($select_header_pdf_settings)) {
		$file_name = $select_header_pdf_settings['file_name'];

		$font_heading_size = $select_header_pdf_settings['font_size'];
		$font_heading_type = $select_header_pdf_settings['font_type'];
		$font_heading = $select_header_pdf_settings['font'];

		$pdf_header_logo = $select_header_pdf_settings['pdf_logo'];
		$pdf_header_logo_align = $select_header_pdf_settings['alignment'];

		$heading_color = $select_header_pdf_settings['pdf_color'];
		$header_text = $select_header_pdf_settings['text'];
	}

	$select_footer_pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "select * from inventory_pdf_setting where style = '$pdf_style' AND setting_type = 'footer'"));
	if(!empty($select_footer_pdf_settings)) {
		$file_name = $select_footer_pdf_settings['file_name'];

		$font_footer_size = $select_footer_pdf_settings['font_size'];
		$font_footer_type = $select_footer_pdf_settings['font_type'];
		$font_footer = $select_footer_pdf_settings['font'];

		$pdf_footer_logo = $select_footer_pdf_settings['pdf_logo'];
		$pdf_footer_logo_align = $select_header_pdf_settings['alignment'];

		$footer_color = $select_footer_pdf_settings['pdf_color'];
		$footer_text = $select_footer_pdf_settings['text'];
	}

	$select_main_pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "select * from inventory_pdf_setting where style = '$pdf_style' AND setting_type = 'main'"));
	if(!empty($select_main_pdf_settings)) {

		$font_main_heading_size = $select_main_pdf_settings['font_size'];
		$font_main_heading_type = $select_main_pdf_settings['font_type'];
		$font_main_heading = $select_main_pdf_settings['font'];

		$font_main_body_size = $select_main_pdf_settings['font_body_size'];
		$font_main_body_type = $select_main_pdf_settings['font_body_type'];
		$font_main_body = $select_main_pdf_settings['font_body'];

		$main_body_color = $select_main_pdf_settings['pdf_body_color'];
		$main_heading_color = $select_main_pdf_settings['pdf_color'];
	}

	$color = empty($color) ? "#000000" : $color;
	$units = empty($units) ? "8" : $units;
	$page_ori = empty($page_ori) ? "Portrait" : $page_ori;

	$font_heading_size = empty($font_heading_size) ? "8" : $font_heading_size;
	$font_heading_type = empty($font_heading_type) ? "" : $font_heading_type;
	$font_heading = empty($font_heading) ? "times" : $font_heading;

	$font_main_heading_size = empty($font_main_heading_size) ? "8" : $font_main_heading_size;
	$font_main_heading_type = empty($font_main_heading_type) ? "" : $font_main_heading_type;
	$font_main_heading = empty($font_main_heading) ? "times" : $font_main_heading;

	$font_main_body_size = empty($font_main_body_size) ? "8" : $font_main_body_size;
	$font_main_body_type = empty($font_main_body_type) ? "" : $font_main_body_type;
	$font_main_body = empty($font_main_body) ? "times" : $font_main_body;

	$font_footer_size = empty($font_footer_size) ? "8" : $font_footer_size;
	$font_footer_type = empty($font_footer_type) ? "" : $font_footer_type;
	$font_footer = empty($font_footer) ? "times" : $font_footer;

	$pdf_header_logo = empty($pdf_header_logo) ? "" : $pdf_header_logo;
	$pdf_footer_logo = empty($pdf_footer_logo) ? "" : $pdf_footer_logo;
	$pdf_header_logo_align = empty($pdf_header_logo_align) ? "C" : $pdf_header_logo_align;
	$pdf_footer_logo_align = empty($pdf_footer_logo_align) ? "C" : $pdf_footer_logo_align;

	$margin_left = empty($margin_left) ? "10" : $margin_left;
	$margin_right = empty($margin_right) ? "10" : $margin_right;
	$margin_top = empty($margin_top) ? (!empty($pdf_header_logo) ? "30" : "10") : $margin_top;
	$margin_header = empty($margin_header) ? "10" : $margin_header;
	$margin_bottom = empty($margin_bottom) ? "10" : $margin_bottom;

	$heading_color = empty($heading_color) ? "#000000" : $heading_color;
	$main_body_color = empty($main_body_color) ? "#000000" : $main_body_color;
	$main_heading_color = empty($main_heading_color) ? "#000000" : $main_heading_color;
	$footer_color = empty($footer_color) ? "#000000" : $footer_color;

	DEFINE('HEADER_LOGO', $pdf_header_logo);
	DEFINE('FOOTER_LOGO', $pdf_footer_logo);
	DEFINE('HEADER_TEXT', html_entity_decode($header_text));
	DEFINE('FOOTER_TEXT', html_entity_decode($footer_text));
	DEFINE('HEADER_FONT', $font_heading);
	DEFINE('FOOTER_FONT', $font_footer);
	DEFINE('HEADER_FONT_TYPE', $font_heading_type);
	DEFINE('FOOTER_FONT_TYPE', $font_footer_type);
	DEFINE('HEADER_FONT_SIZE', $font_heading_size);
	DEFINE('FOOTER_FONT_SIZE', $font_footer_size);
	DEFINE('HEADER_LOGO_ALIGN', $pdf_header_logo_align);
	DEFINE('FOOTER_LOGO_ALIGN', $pdf_footer_logo_align);
	DEFINE('HEADER_COLOR', $heading_color);
	DEFINE('FOOTER_COLOR', $footer_color);

	include_once('../tcpdf/tcpdf.php');
	class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			$this->setFont('times', '', 8);
			if(HEADER_LOGO != '') {
				$image_file = 'download/'.HEADER_LOGO;
				$this->Image($image_file, 10, 5, 0, 25, '', '', 'T', false, 300, HEADER_LOGO_ALIGN, false, false, 0, false, false, false);
			}

			if(HEADER_TEXT != '') {
				$this->setCellHeightRatio(0.7);
				$font_style = "font-family: ".HEADER_FONT."; font-style: ".HEADER_FONT_TYPE."; font-size: ".HEADER_FONT_SIZE."; color: ".HEADER_COLOR.";";

				$header_align = (HEADER_LOGO_ALIGN == "L" ? "R" : "L");
				if ($header_align == "L") {
					$align_style = 'text-align: left;';
				} else {
					$align_style = 'text-align: right;';
				}
				$header_text = '<p style="'.$font_style.$align_style.'">'.HEADER_TEXT.'</p>';
				$this->writeHTMLCell(0, 0, 5 , 5, $header_text, 0, 0, false, true, $header_align, true);
			}
		}

		// Page footer
		public function Footer() {
			$font_style = "font-family: ".FOOTER_FONT."; font-style: ".FOOTER_FONT_TYPE."; font-size: ".FOOTER_FONT_SIZE."; color: ".FOOTER_COLOR.";";

			$footer_align = (FOOTER_LOGO_ALIGN == "L" ? "R" : "L");
			if ($footer_align == "L") {
				$align_style = 'text-align: left;';
			} else {
				$align_style = 'text-align: right;';
			}

			// Position at 15 mm from bottom
			$this->SetY(-10);
			$this->SetFont('times', '', 8);
			$footer_text = '<p style="'.$align_style.'">'.$this->getAliasNumPage().'</p>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, true, $footer_align, true);

			if(FOOTER_TEXT != '') {
				$this->SetY(-15);
				$this->setCellHeightRatio(0.7);
				$footer_text = '<p style="'.$font_style.$align_style.'">'.FOOTER_TEXT.'</p>';
				$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, true, $footer_align, true);
			}

			if(FOOTER_LOGO != '') {
				$this->SetY(-30);
				$image_file = 'download/'.FOOTER_LOGO;
				$this->Image($image_file, 11, 275, 25, '', '', '', '', false, 300, FOOTER_LOGO_ALIGN, false, false, 0, false, false, false);
			}
		}
	}

	$pdf = new MYPDF($page_ori, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetMargins($margin_left, $margin_top, $margin_right);
	$pdf->AddPage();
	$pdf->setCellHeightRatio(1);
	$pdf->writeHTML(utf8_encode($html), true, false, true, false, '');
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	$pdf->Output('download/'.$filename, 'F');
	echo "<script>window.open('download/".$filename."');</script>";
	echo "<script>window.location.replace('?edit=".$_GET['list']."');</script>";
}
$pdf_styles = mysqli_fetch_all(mysqli_query($dbc, "SELECT `style` FROM `inventory_pdf_setting` GROUP BY `style` ORDER BY `style`"),MYSQLI_ASSOC); ?>
<script type="text/javascript" src="inventory.js"></script>
<script>
$(document).ready(function() {
	$('[name=projectid]').change(function() {
		if($(this).find('option:selected').data('business') > 0) {
			$('[name=businessid]').val($(this).find('option:selected').data('business')).trigger('change.select2');
		}
	});
	$('[name=businessid]').change(function() {
		var business = this.value;
		$('[name=projectid] option').each(function() {
			if(!(business > 0) || $(this).data('business') == business || !($(this).data('business') > 0)) {
				$(this).show();
			} else {
				$(this).hide();
			}
		}).trigger('change.select2');
	});
	$('[name^=filter_]').change(populate_inventory);

	// Populate the Inventory Lists
	$('[name^=inventoryid]').each(function() {
		var sel = this;
		var val = sel.value;
		var qty = $(sel).find('option:selected').data('quantity');
		$(sel).empty();
		$(sel).append('<option />');
		inventory_list.forEach(function(inv) {
			$(sel).append('<option '+(val == inv.inventoryid ? 'selected' : '')+' value="'+inv.inventoryid+'" data-quantity="'+(val == inv.inventoryid ? qty : inv.available)+'">'+inv.name+'</option>');
		});
		$(sel).trigger('change.select2');
	});

	// Check mandatory fields
	$('#pick_list_form').submit(checkMandatoryFields);
});
var inventory_list = [];
function addRow() {
	destroyInputs();
	var clone = $('.inv_list .form-group:visible').last().clone();
	clone.find('input,select').val('');
	clone.find('.available,.filled').html('');
	$('.inv_list .form-group').last().after(clone);
	initInputs();
	$('[name^=filter_]').off('change',populate_inventory).change(populate_inventory);
}
function remRow(item) {
	if($('.inv_list .form-group:visible').length == 1) {
		addRow();
	}

	$(item).closest('.form-group').hide().find('[name="deleted[]"]').val(1);
}
function getAll(button) {
	var group = $(button).closest('.form-group');
	var options = {
		category: group.find('[name=filter_category]').val(),
		po: group.find('[name=filter_po]').val(),
		po_line: group.find('[name=filter_po_line]').val(),
		ticket: group.find('[name=filter_ticket]').val(),
		customer_order: group.find('[name=filter_customer_order]').val(),
		detail_customer_order: group.find('[name=filter_detail_customer_order]').val(),
		pallet: group.find('[name=filter_pallet]').val()
	};
	button.value = JSON.stringify(options);
}
function populate_inventory() {
	var group = $(this).closest('.form-group');
	/*if(this.name == 'filter_ticket') {
		group.find('[name=filter_po]').val($(this).find('option:selected').data('po'));
		group.find('[name=filter_po]').trigger('change.select2');
	} else if(this.name == 'filter_po') {
		var po_num = this.value;
		group.find('[name=filter_ticket] option').each(function() {
			if(po_num == '' || $(this).data('po') == po_num) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		group.find('[name=filter_ticket]').trigger('change.select2');
	}*/
	$.post('inventory_ajax.php?action=category_list',{
		category: group.find('[name=filter_category]').val(),
		po: group.find('[name=filter_po]').val(),
		po_line: group.find('[name=filter_po_line]').val(),
		ticket: group.find('[name=filter_ticket]').val(),
		customer_order: group.find('[name=filter_customer_order]').val(),
		detail_customer_order: group.find('[name=filter_detail_customer_order]').val(),
		pallet: group.find('[name=filter_pallet]').val()
	}, function(response) {
		response = response.split('#*#');
		group.find('[name="inventoryid[]"]').empty().html(response[0]).trigger('change.select2');
		group.find('[name="filter_category"]').empty().html(response[1]).trigger('change.select2');
		group.find('[name="filter_po"]').empty().html(response[2]).trigger('change.select2');
		group.find('[name="filter_po_line"]').empty().html(response[3]).trigger('change.select2');
		group.find('[name="filter_ticket"]').empty().html(response[4]).trigger('change.select2');
		group.find('[name="filter_customer_order"]').empty().html(response[5]).trigger('change.select2');
		group.find('[name="filter_detail_customer_order"]').empty().html(response[6]).trigger('change.select2');
		group.find('[name="filter_pallet"]').empty().html(response[7]).trigger('change.select2');
	});
}
function selectInventory(select) {
	$(select).closest('.form-group').find('.available').html(0);
	$(select).closest('.form-group').find('[name^=quantity]').prop('max',0);
	$(select).closest('.form-group').find('.available').html($(select).find('option:selected').data('quantity'));
	$(select).closest('.form-group').find('[name^=quantity]').prop('max',$(select).find('option:selected').data('quantity'));
}
function checkMandatoryFields() {
	var unfilled_fields = [];
	var form = $('#pick_list_form');
	$(form).find('.mandatory_field').each(function() {
		if($(this).val() == '') {
			unfilled_fields.push($(this).data('field-label'));
		}
	});
	if(unfilled_fields.length > 0) {
		var alert_msg = 'The following mandatory fields are not filled in:';
		unfilled_fields.forEach(function(label) {
			alert_msg += '\n'+label;
		});
		alert(alert_msg);
		return false;
	} else {
		return true;
	}
}
function exportPdf() {
	var picklistid = $('[name="picklistid"]').val();
	<?php if(count($pdf_styles) > 1) { ?>
		$('#dialog_pdf_style').dialog({
			resizable: false,
			height: 'auto',
			width: ($(window).width() <= 500 ? $(window).width() : 500),
			modal: true,
			buttons: {
				<?php foreach ($pdf_styles as $style) { ?>
					"PDF <?= substr($style['style'],-1) ?>": function() {
						window.location.href = "?list="+picklistid+"&pdf=<?= $style['style'] ?>";
						$(this).dialog('close');
					},
				<?php } ?>
		        Cancel: function() {
		        	$(this).dialog('close');
		        }
			}
		});
	<?php } else { ?>
		window.location.href = "?list="+picklistid+"&pdf=<?= $pdf_styles[0]['style'] ?>";
	<?php } ?>
}
</script>
<?php $list_name = 'New List';
$picklistid = 0;
if($_GET['edit'] > 0) {
	$picklistid = $_GET['edit'];
	$pick_list = $dbc->query("SELECT * FROM `pick_lists` WHERE `id`='$picklistid'")->fetch_assoc();
	$list_name = $pick_list['name'];
}
$mandatory_fields = explode(',',get_config($dbc, 'pick_list_mandatory')); ?>
<div id="dialog_pdf_style" title="Select a PDF Style" style="display:none;">
	Choose a PDF Style.
</div>
<?php if(!empty($pdf_styles)) { ?>
	<a class="pull-right gap-right" href="" onclick="exportPdf(this); return false;"><img src="../img/pdf.png"></a>
	<div class="clearfix"></div>
<?php } ?>
<?php if($picklistid > 0) { ?>
	<small><em>Created by <?= get_contact($dbc, $pick_list['created_by']) ?> on <?= date('Y-m-d',strtotime($pick_list['created_date'])) ?></em></small><?php } ?>
<form class="form-horizontal" method="POST" action="" id="pick_list_form">
	<input type="hidden" name="picklistid" value="<?= $picklistid ?>">
	<div class="form-group">
		<label class="col-sm-4 control-label">List Name<?= in_array('list_name',$mandatory_fields) ? '<span class="text-red">*</span>' : '' ?>:</label>
		<div class="col-sm-8">
			<?php if($inv_security['edit'] > 0) { ?>
				<input type="text" name="name" data-field-label="List Name" value="<?= $pick_list['name'] ?>" class="form-control <?= in_array('list_name',$mandatory_fields) ? 'mandatory_field' : '' ?>">
			<?php } else {
				echo $pick_list['name'];
			} ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?= BUSINESS_CAT ?><?= in_array('business',$mandatory_fields) ? '<span class="text-red">*</span>' : '' ?>:</label>
		<div class="col-sm-8">
			<?php if($inv_security['edit'] > 0) { ?>
				<select name="businessid" data-field-label="<?= BUSINESS_CAT ?>" class="chosen-select-deselect <?= in_array('business',$mandatory_fields) ? 'mandatory_field' : '' ?>" data-placeholder="Select <?= BUSINESS_CAT ?>"><option />
					<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `name` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `category`='".BUSINESS_CAT."'")) as $business) { ?>
						<option <?= $pick_list['businessid'] == $business['contactid'] ? 'selected' : '' ?> value="<?= $business['contactid'] ?>"><?= $business['name'] ?></option>
					<?php } ?>
				</select>
			<?php } else {
				echo get_contact($dbc, $pick_list['businessid'], 'name');
			} ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?= PROJECT_NOUN ?>:</label>
		<div class="col-sm-8">
			<?php if($inv_security['edit'] > 0) { ?>
				<select name="projectid" class="chosen-select-deselect" data-placeholder="Select <?= PROJECT_NOUN ?>"><option />
					<?php $project_list = $dbc->query("SELECT projectid, projecttype, project_name, businessid, clientid, status FROM project WHERE deleted=0 AND (status NOT IN ('Archive') OR `projectid`='$projectid') order by `projectid` DESC");
					while($project = $project_list->fetch_assoc()) { ?>
						<option <?= $pick_list['projectid'] == $project['projectid'] ? 'selected' : '' ?> data-business="<?= $project['businessid'] ?>" value="<?= $project['projectid'] ?>"><?= get_project_label($dbc, $project) ?></option>
					<?php } ?>
				</select>
			<?php } else {
				echo get_project_label($dbc, $dbc->query("SELECT * FROM `project` WHERE `projectid`='{$pick_list['projectid']}'")->fetch_assoc());
			} ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Completed:</label>
		<div class="col-sm-8">
			<?php if($inv_security['edit'] > 0) { ?>
				<label class="form-checkbox"><input type="radio" name="completed" value="0" <?= $pick_list['completed'] == 0 ? 'checked' : '' ?>> No</label>
				<label class="form-checkbox"><input type="radio" name="completed" value="1" <?= $pick_list['completed'] == 1 ? 'checked' : '' ?>> Yes</label>
			<?php } else {
				echo $pick_list['completed'] > 0 ? 'Yes' : 'No';
			} ?>
			<?php if($pick_list['completed'] == 1) { ?>
				<div class="col-sm-12">
					Completed: <?= get_contact($dbc, $pick_list['completed_by']) ?><br />
					<?php if($pick_list['signature'] != '' && !file_exists('download/signature_'.$pick_list['id'].'.png')) {
						if (!file_exists('download')) {
							mkdir('download', 0777, true);
						}
						include_once('../phpsign/signature-to-image.php');
						$signature = sigJsonToImage(html_entity_decode($pick_list['signature']));
						imagepng($signature, 'download/signature_'.$pick_list['id'].'.png');
					}
					if($pick_list['signature'] != '') { ?>
						<img src="download/signature_<?= $pick_list['id'] ?>.png">
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Archived:</label>
		<div class="col-sm-8">
			<?php if($inv_security['edit'] > 0) { ?>
				<label class="form-checkbox"><input type="radio" name="archived" value="0" <?= $pick_list['deleted'] == 0 ? 'checked' : '' ?>> No</label>
				<label class="form-checkbox"><input type="radio" name="archived" value="1" <?= $pick_list['deleted'] == 1 ? 'checked' : '' ?>> Yes</label>
			<?php } else {
				echo $pick_list['deleted'] > 0 ? 'Yes' : 'No';
			} ?>
		</div>
	</div>
	<div class="form-group">
		<?php $pick_list_filters = array_filter(explode(',',get_config($dbc, 'pick_list_filters')));
		$filter_cols = floor(12 / (count(array_filter($pick_list_filters,function($val) { return !in_array($val, ['display_all','fill_max']); })) + 1)); ?>
		<h4><?= INVENTORY_NOUN ?> List</h4>
		<div class="col-sm-12 inv_list">
			<div class="hide-titles-mob">
				<div class="col-sm-<?= in_array('display_all',$pick_list_filters) ? 7 : 8 ?>">
					<?php if(in_array('category',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>"><?= INVENTORY_NOUN ?> Tab</div>
					<?php } ?>
					<?php if(in_array('ticket_po',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>">Purchase Order #</div>
					<?php } ?>
					<?php if(in_array('po_line',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>">Line Item #</div>
					<?php } ?>
					<?php if(in_array('ticket',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>"><?= TICKET_NOUN ?></div>
					<?php } ?>
					<?php if(in_array('ticket_customer_order',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>">Customer Order #</div>
					<?php } ?>
					<?php if(in_array('detail_customer_order',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>">Customer Order #</div>
					<?php } ?>
					<?php if(in_array('pallet',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>">Pallet #</div>
					<?php } ?>
					<div class="col-sm-<?= 12 - ($filter_cols * (count(array_filter($pick_list_filters,function($val) { return !in_array($val, ['display_all','fill_max']); })))) ?>"><?= INVENTORY_NOUN ?></div>
					<script>
					<?php $inv_list = $dbc->query("SELECT `inventory`.`inventoryid`, `inventory`.`product_name`, `inventory`.`name`, `inventory`.`category`, `inventory`.`pallet`, `inventory`.`quantity` - CAST(`inventory`.`assigned_qty` AS SIGNED INT) `available`, `tickets`.`ticketid`, `tickets`.`ticket_label`, `tickets`.`purchase_order`, `tickets`.`customer_order_num`, `tickets`.`po_line`, `tickets`.`position` FROM `inventory` LEFT JOIN (SELECT `tickets`.`ticketid`, `ticket_label`, `item_id`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `purchase_order`, `customer_order_num`, `tickets`.`businessid`, `ticket_attached`.`po_line`, `ticket_attached`.`position` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`deleted`=0 AND `tickets`.`deleted`=0 AND `ticket_attached`.`src_table` IN ('inventory','inventory_detailed')) `tickets` ON `inventory`.`inventoryid`=`tickets`.`item_id` WHERE `inventory`.`deleted`=0 AND (IFNULL(`product_name`,'') != '' OR  IFNULL(`name`,'') != '') $match_business ORDER BY `inventory`.`category`, `inventory`.`product_name`, `inventory`.`name`");
					$inv_enc_list = [];
					while($inv_item = $inv_list->fetch_assoc()) {
						$inv_enc_list[] = array_map('utf8_encode',$inv_item);
					}?>
					inventory_list = <?= json_encode($inv_enc_list) ?>;
					</script>
				</div>
				<div class="col-sm-1">Available</div>
				<div class="col-sm-1">Quantity</div>
				<div class="col-sm-1">Filled</div>
			</div>
			<?php $items = $dbc->query("SELECT `pick_list_items`.*, `inventory`.`quantity` - `inventory`.`assigned_qty` `available`, `inventory`.`category`, `inventory`.`pallet`, `tickets`.`ticketid`, `tickets`.`purchase_order`, `tickets`.`customer_order_num`, `tickets`.`po_line`, `tickets`.`position` FROM `pick_list_items` LEFT JOIN `inventory` ON `pick_list_items`.`inventoryid`=`inventory`.`inventoryid` LEFT JOIN (SELECT `tickets`.`ticketid`, `ticket_label`, `item_id`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `purchase_order`, `customer_order_num`, `tickets`.`businessid`, `ticket_attached`.`po_line`, `ticket_attached`.`position` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`deleted`=0 AND `tickets`.`deleted`=0 AND `ticket_attached`.`src_table` IN ('inventory','inventory_detailed')) `tickets` ON `inventory`.`inventoryid`=`tickets`.`item_id` WHERE `pick_list_items`.`pick_list`='$picklistid' AND `pick_list_items`.`deleted`=0 AND `pick_list_items`.`pick_list` > 0 $match_business ORDER BY `tickets`.`ticketid`, LPAD(`tickets`.`po_line`,100,0), `inventory`.`category`, `inventory`.`name`, `inventory`.`product_name`");
			$item = $items->fetch_assoc();
			do { ?>
				<div class="form-group">
					<div class="col-sm-<?= in_array('display_all',$pick_list_filters) ? 7 : 8 ?>">
						<?php if(in_array('category',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob"><?= INVENTORY_NOUN ?> Tab: </span>
								<?php if($inv_security['edit'] > 0) { ?>
									<select name="filter_category" class="chosen-select-deselect" data-placeholder="Select Tab"><option />
										<?php if($item['category'] != '') { ?>
											<option selected value="<?= $item['category'] ?>" ><?= $item['category'] ?></option>
										<?php } ?>
										<?php foreach($each_tab as $category) { ?>
											<option <?= $category == $item['category'] ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
										<?php } ?>
									</select>
								<?php } else {
									echo $item['category'];
								} ?>
							</div>
						<?php } ?>
						<?php if(in_array('ticket_po',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob">Purchase Order #: </span>
								<?php if($inv_security['edit'] > 0) { ?>
									<select name="filter_po" class="chosen-select-deselect" data-placeholder="Select Purchase Order #"><option />
										<?php if($item['purchase_order'] != '') { ?>
											<option selected value="<?= $item['purchase_order'] ?>" ><?= $item['purchase_order'] ?></option>
										<?php } ?>
										<?php if(!isset($purchase_order_list)) {
											$purchase_order_list = [];
											$po_options = $dbc->query("SELECT `po_num` `purchase_order` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='inventory' AND `item_id` > 0 GROUP BY `po_num` ORDER BY `purchase_order`");
											while($po_option = $po_options->fetch_array()) {
												foreach(explode('#*#',$po_option[0]) as $po_number) {
													$purchase_order_list[] = $po_number;
												}
											}
											$purchase_order_list = array_filter(array_unique($purchase_order_list));
											sort($purchase_order_list);
										}
										foreach($purchase_order_list as $purchase_order) { ?>
											<option <?= strpos('#*#'.$item['purchase_order'].'#*#',"#*#$purchase_order#*#") !== FALSE ? 'selected' : '' ?> value="<?= $purchase_order ?>"><?= $purchase_order ?></option>
										<?php } ?>
									</select>
								<?php } else {
									echo implode('<br />',array_filter(explode('#*#',$item['purchase_order'])));
								} ?>
							</div>
						<?php } ?>
						<?php if(in_array('po_line',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob">Line Item #: </span>
								<?php if($inv_security['edit'] > 0) { ?>
									<select name="filter_po_line" class="chosen-select-deselect" data-placeholder="Select PO Line Item #"><option />
										<?php if(!isset($po_line_list)) {
											$po_line_list = $dbc->query("SELECT `ticket_attached`.`po_line` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `tickets`.`deleted`=0 AND `item_id` > 0 AND `ticket_attached`.`deleted`=0 AND IFNULL(`ticket_attached`.`po_line`,'') != '' GROUP BY `ticket_attached`.`po_line` ORDER BY `ticket_attached`.`po_line`")->fetch_all(MYSQLI_ASSOC);
										}
										foreach($po_line_list as $po_line) { ?>
											<option <?= $po_line['po_line'] == $item['po_line'] ? 'selected' : '' ?> value="<?= $po_line['po_line'] ?>"><?= $po_line['po_line'] ?></option>
										<?php } ?>
									</select>
								<?php } else {
									echo $item['po_line'];
								} ?>
							</div>
						<?php } ?>
						<?php if(in_array('ticket',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob"><?= TICKET_NOUN ?>: </span>
								<?php if($inv_security['edit'] > 0) { ?>
									<select name="filter_ticket" class="chosen-select-deselect" data-placeholder="Select <?= TICKET_NOUN ?>"><option />
										<?php if($item['ticket_label'] != '') { ?>
											<option selected value="<?= $item['ticketid'] ?>" ><?= $item['ticket_label'] ?></option>
										<?php } ?>
										<?php if(!isset($ticket_list)) {
											$ticket_list = $dbc->query("SELECT `tickets`.`ticketid`, `ticket_label`, CONCAT(GROUP_CONCAT(IFNULL(`ticket_attached`.`po_num`,'') SEPARATOR '#*#'),'#*#',IFNULL(`tickets`.`purchase_order`,'')) `purchase_order` FROM `tickets` LEFT JOIN `ticket_attached` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Archive','Archived') AND IFNULL(`ticket_label`,'') != '' AND `ticket_attached`.`src_table`='inventory' AND `ticket_attached`.`item_id` > 0 GROUP BY `ticket_label` ORDER BY `ticket_label`")->fetch_all(MYSQLI_ASSOC);
										}
										foreach($ticket_list as $ticket) { ?>
											<option data-po="<?= $ticket['purchase_order'] ?>" <?= $ticket['ticketid'] == $item['ticketid'] ? 'selected' : '' ?> value="<?= $ticket['ticketid'] ?>"><?= $ticket['ticket_label'] ?></option>
										<?php } ?>
									</select>
								<?php } else {
									echo get_ticket_label($dbc, $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='{$item['ticketid']}'")->fetch_assoc());
								} ?>
							</div>
						<?php } ?>
						<?php if(in_array('ticket_customer_order',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob">Customer Order #: </span>
								<?php if($inv_security['edit'] > 0) { ?>
									<select name="filter_customer_order" class="chosen-select-deselect" data-placeholder="Select Customer Order #"><option />
										<?php if($item['customer_order_num'] != '') { ?>
											<option selected value="<?= $item['customer_order_num'] ?>" ><?= $item['customer_order_num'] ?></option>
										<?php } ?>
										<?php if(!isset($customer_order_list)) {
											$customer_order_list = [];
											$ticket_co_options = $dbc->query("SELECT `customer_order_num` FROM `tickets` WHERE `deleted`=0 AND IFNULL(`customer_order_num`,'') != '' GROUP BY `customer_order_num` ORDER BY `customer_order_num`");
											while($co_option = $ticket_co_options->fetch_array()) {
												foreach(explode('#*#',$co_option[0]) as $co_number) {
													$customer_order_list[] = $co_number;
												}
											}
											$customer_order_list = array_filter(array_unique($customer_order_list));
											sort($customer_order_list);
										}
										foreach($customer_order_list as $customer_order) { ?>
											<option <?= strpos('#*#'.$item['customer_order_num'].'#*#',"#*#$customer_order#*#") !== FALSE ? 'selected' : '' ?> value="<?= $customer_order ?>"><?= $customer_order ?></option>
										<?php } ?>
									</select>
								<?php } else {
									echo implode('<br />',array_filter(explode('#*#',$item['customer_order_num'])));
								} ?>
							</div>
						<?php } ?>
						<?php if(in_array('detail_customer_order',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob">Customer Order #: </span>
								<?php if($inv_security['edit'] > 0) { ?>
									<select name="filter_detail_customer_order" class="chosen-select-deselect" data-placeholder="Select Customer Order #"><option />
										<?php if($item['position'] != '') { ?>
											<option selected value="<?= $item['position'] ?>" ><?= $item['position'] ?></option>
										<?php } ?>
										<?php if(!isset($detail_customer_order_list)) {
											$detail_customer_order_list = [];
											$co_options = $dbc->query("SELECT `position` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='inventory' AND `item_id` > 0 AND IFNULL(`position`,'') != '' GROUP BY `position` ORDER BY `position`");
											while($co_option = $co_options->fetch_array()) {
												foreach(explode('#*#',$co_option[0]) as $co_number) {
													$detail_customer_order_list[] = $co_number;
												}
											}
											$detail_customer_order_list = array_filter(array_unique($detail_customer_order_list));
											sort($detail_customer_order_list);
										}
										foreach($detail_customer_order_list as $customer_order) { ?>
											<option <?= strpos('#*#'.$item['position'].'#*#',"#*#$customer_order#*#") !== FALSE ? 'selected' : '' ?> value="<?= $customer_order ?>"><?= $customer_order ?></option>
										<?php } ?>
									</select>
								<?php } else {
									echo implode('<br />',array_filter(explode('#*#',$item['position'])));
								} ?>
							</div>
						<?php } ?>
						<?php if(in_array('pallet',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob">Pallet #: </span>
								<?php if($inv_security['edit'] > 0) { ?>
									<select name="filter_pallet" class="chosen-select-deselect" data-placeholder="Select Pallet #"><option />
										<?php if($item['pallet'] != '') { ?>
											<option selected value="<?= $item['pallet'] ?>" ><?= $item['pallet'] ?></option>
										<?php } ?>
										<?php if(!isset($pallet_list)) {
											$pallet_list = $dbc->query("SELECT `pallet` FROM `inventory` WHERE `deleted`=0 AND IFNULL(`pallet`,'') != '' GROUP BY `pallet` ORDER BY `pallet`")->fetch_all(MYSQLI_ASSOC);
										}
										foreach($pallet_list as $pallet) { ?>
											<option <?= $pallet['pallet'] == $item['pallet'] ? 'selected' : '' ?> value="<?= $pallet['pallet'] ?>"><?= $pallet['pallet'] ?></option>
										<?php } ?>
									</select>
								<?php } else {
									echo $item['pallet'];
								} ?>
							</div>
						<?php } ?>
						<div class="col-sm-<?= 12 - ($filter_cols * (count(array_filter($pick_list_filters,function($val) { return !in_array($val, ['display_all','fill_max']); })))) ?>">
							<span class="show-on-mob"><?= INVENTORY_NOUN ?>: </span>
							<?php if($inv_security['edit'] > 0) { ?>
								<select name="inventoryid[]" class="chosen-select-deselect" data-placeholder="Select <?= INVENTORY_NOUN ?>" onchange="selectInventory(this);"><option />
									<?php if($item['inventoryid'] > 0) { ?>
										<option selected value="<?= $item['inventoryid'] ?>" data-quantity="<?= $item['available'] + $item['quantity'] ?>"><?= $item['product_name'].' '.$item['name'] ?></option>
									<?php } ?>
								</select>
							<?php } else {
								echo $item['product_name'].' '.$item['name'];
							} ?>
						</div>
					</div>
					<div class="col-sm-1"><span class="show-on-mob">Available: </span><span class="available"><?= $item['available'] + $item['quantity'] ?></span></div>
					<div class="col-sm-1"><span class="show-on-mob">Quantity: </span><?php if($inv_security['edit'] > 0) { ?><input type="number" min=0 step="any" max=<?= $item['available'] + $item['quantity'] ?> name="quantity[]" value="<?= $item['quantity'] ?>" class="form-control"><?php } else { echo $item['quantity']; } ?></div>
					<div class="col-sm-1"><span class="show-on-mob">Filled: </span><span class="filled"><?= $item['filled'] ?></span></div>
					<div class="col-sm-<?= in_array('display_all',$pick_list_filters) ? 2 : 1 ?>">
						<?php if($inv_security['edit'] > 0) { ?>
							<input type="hidden" name="id[]" value="<?= $item['id'] ?>"><input type="hidden" name="deleted[]" value="0">
							<img class="inline-img cursor-hand" src="../img/remove.png" onclick="remRow(this);"><img class="inline-img cursor-hand" src="../img/icons/ROOK-add-icon.png" onclick="addRow();">
							<?php if(in_array('display_all',$pick_list_filters)) { ?><button class="btn brand-btn" type="submit" name="get_all" onclick="getAll(this);">Display All</button><?php } ?>
						<?php } ?>
					</div>
				</div>
			<?php } while($item = $items->fetch_assoc()); ?>
		</div>
	</div>
	<?php if($inv_security['edit'] > 0) { ?>
		<button class="btn brand-btn pull-right" name="save" value="save">Save</button>
	<?php } ?>
</form>
