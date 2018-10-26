<?php include_once('config.php');
$manifestid = filter_var($_GET['manifestid'],FILTER_SANITIZE_STRING);
$manifest_fields = explode(',',get_config($dbc, 'ticket_manifest_fields'));
$ticket_filter = '';
if(in_array_starts('type ',$manifest_fields)) {
	$type_filters = [];
	foreach($manifest_fields as $config_field) {
		$config_field = explode(' ',$config_field);
		if($config_field[0] == 'type' && count($config_field) == 2 && in_array($config_field[1],$ticket_conf_list)) {
			$type_filters[] = $config_field[1];
		}
	}
	$ticket_filter = " AND `tickets`.`ticket_type` IN ('".implode("','",$type_filters)."')";
} else if(isset($_GET['tile_name']) || isset($_GET['tile_group'])) {
    $ticket_filter = " AND `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."')";
}
$manifest = $dbc->query("SELECT * FROM `ticket_manifests` WHERE `id`='$manifestid'")->fetch_assoc();
$siteid = $manifest['siteid'];
$manifest_label = ($siteid > 0 ? strtoupper(get_contact($dbc, $siteid)) : 'UNASSIGNED');
if(!file_exists('manifest')) {
	mkdir('manifest', 0777, true);
}
if(isset($_POST['update'])) {
	include_once('../tcpdf/tcpdf.php');
	DEFINE('TICKET_FOOTER',get_config($dbc, 'ticket_pdf_footer'));
	$history = '';
	$revision = $manifest['revision'] + 1;
	$line_items = filter_var(implode(',',$_POST['include']),FILTER_SANITIZE_STRING);
	$sum_qty = 0;
	$total_weight = 0;
	$manual_qty = [];
	foreach($_POST['include'] as $i => $line_id) {
		$prior_qty = $_POST['prior_qty'][$i];
		$qty = 1;
		foreach($_POST['line_rows'] as $line_row) {
			if($line_row == $line_id) {
				$qty = round($_POST['qty'][$i],3);
			}
		}
		$manual_qty[] = $qty;
		$sum_qty += $qty;
		if($qty != $prior_qty) {
			$diff = $qty - $prior_qty;
			$row = $dbc->query("SELECT `inventoryid`,`quantity` FROM `ticket_attached` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` WHERE `ticket_attached`.`src_table`='inventory' AND `ticket_attached`.`id`='$line_row'")->fetch_assoc();
			$history .= $row['inventoryid'].' quantity updated to '.$qty.' on manifest, ';
			
			$old_qty = $row['qty'];
			$new_qty = $old_qty - $diff;
			$dbc->query("UPDATE `inventory` SET `quantity`='$new_qty' WHERE `inventoryid`='{$row['inventoryid']}'");
			$dbc->query("INSERT INTO `inventory_change_log` (`inventoryid`,`contactid`,`old_inventory`,`changed_quantity`,`new_inventory`,`date_time`,`location_of_change`,`change_comment`) VALUES ('{$row['inventoryid']}','{$_SESSION['contactid']}','$old_qty','{$manual_qty[$i]}','$new_qty','".date('Y-m-d h:i:s')."','Manifest $manifestid updated: $qty (previously $prior_qty) assigned to Manifest')");
		}
	}
	$history .= 'by '.get_contact($dbc, $_SESSION['contactid']);
	$qtys = implode(',',$manual_qty);
	$signature = filter_var($_POST['signature'],FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `ticket_manifests` SET `date`='".date('Y-m-d')."',`line_items`='$line_items',`qty`='$qtys',`contactid`='".$_SESSION['contactid']."',`signature`='$signature',`revision`='$revision',`history`='$history' WHERE `id`='$manifestid'");
	if(!empty($signature)) {
		include_once('../phpsign/signature-to-image.php');
		$signature = sigJsonToImage(html_entity_decode($signature));
		imagepng($signature, 'manifest/signature_'.$manifestid.'_'.$revision.'.png');
	}
	$manifest_ref = date('y').'-'.str_pad($manifestid,4,0,STR_PAD_LEFT);
	$manifest_date = strtoupper(date('F d/y'));
	$logo = get_config($dbc, 'ticket_pdf_logo');
	$row_colour_1 = get_config($dbc, 'report_row_colour_1');
	$row_colour_2 = get_config($dbc, 'report_row_colour_2');
	$lines = [];
	if(in_array('pdf_collapse',$manifest_fields)) {
		$columns = ['po'=>0,'vendor'=>0,'line'=>0,'qty'=>0,'manual_qty'=>0,'site'=>0,'notes'=>0];
	} else {
		$columns = ['po'=>1,'vendor'=>1,'line'=>1,'qty'=>1,'manual_qty'=>1,'site'=>1,'notes'=>1];
	}
	if(in_array('mark_unused',$manifest_fields)) {
		foreach($_POST['mark_unused'] as $line_id) {
			if($line_id > 0) {
				mysqli_query($dbc, "UPDATE `ticket_attached` SET `used` = 0 WHERE `id` = '$line_id'");
			}
		}
	}
	foreach($_POST['include'] as $i => $line_id) {
		if($line_id > 0) {
			$row = $dbc->query("SELECT `tickets`.`ticket_label`,IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_num`,`origin`.`vendor`,`ticket_attached`.`po_line`,`ticket_attached`.`notes`,`inventory`.`inventoryid`,IFNULL(`inventory`.`quantity`,`ticket_attached`.`qty`) `qty`,IFNULL(`ticket_attached`.`siteid`,`tickets`.`siteid`) `siteid`,IFNULL(`ticket_attached`.`weight`,'') `weight`,IFNULL(`ticket_attached`.`weight_units`,'') `weight_units` FROM `ticket_attached` LEFT JOIN `inventory` ON `ticket_attached`.`src_table`='inventory' AND `ticket_attached`.`item_id`=`inventory`.`inventoryid` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` LEFT JOIN `ticket_schedule` `origin` ON `tickets`.`ticketid`=`origin`.`ticketid` AND `origin`.`type`='origin' WHERE `ticket_attached`.`id`='$line_id'")->fetch_assoc();

			$weight = [];
			$weights = [];
			$inv_weight_units = explode('#*#',$row['weight_units']);
			foreach(explode('#*#',$row['weight']) as $id => $inv_weight) {
				if(in_array('weight convert kg to lb',$manifest_fields) && $inv_weight_units[$id] == 'kg') {
					$inv_weight = number_format(($inv_weight*2.20462262185),2);
					$inv_weight_units[$id] = 'lbs';
				}
				$weight[] = $inv_weight.' '.$inv_weight_units[$id];
				$weights[$inv_weight_units[$id]] += $inv_weight;
				if(in_array('total weight lb',$manifest_fields)) {
					if($inv_weight_units[$id] == 'kg') {
						$total_weight += ($inv_weight*2.20462262185);
					} else {
						$total_weight += $inv_weight;
					}
				}
			}
			$weight = implode('<br />', $weight);
		} else {
			$row = '';
			$weight = '';
			$weights = [];
		}
		if(!empty($row)) {
			if(in_array('group pieces po',$manifest_fields)) {
				$po_num_key = implode('<br />',array_filter(explode('#*#',$row['po_num'])));
				$lines[$po_num_key]['file'][] = $row['ticket_label'];
				$lines[$po_num_key]['po'] = $po_num_key;
				$lines[$po_num_key]['vendor'][] = $row['vendor'] > 0 ? get_contact($dbc, $row['vendor'],'name_company') : '';
				$lines[$po_num_key]['line'][] = (empty($row['po_line']) ? 'N/A' : $row['po_line']);
				if($row['qty'] > 0) {
					$lines[$po_num_key]['qty'] += $row['qty'];
				}
				if($manual_qty[$i] > 0) {
					$lines[$po_num_key]['manual_qty'] += $manual_qty[$i];
				}
				$lines[$po_num_key]['site'][] = ($row['siteid'] == $siteid ? $manifest_label : ($row['siteid'] > 0 ? get_contact($dbc, $row['siteid']) : 'UNASSIGNED'));
				$lines[$po_num_key]['notes'][] = $row['notes'];
				foreach($weights as $weight_unit => $weight_num) {
					if(!empty($weight_unit) || $weight_num > 0) {
						$lines[$po_num_key]['weight'][$weight_unit] += $weight_num;
					}
				}
			} else {
				$lines[] = ['file'=>$row['ticket_label'], 'po'=>implode('<br />',array_filter(explode('#*#',$row['po_num']))), 'vendor'=>($row['vendor'] > 0 ? get_contact($dbc, $row['vendor'],'name_company') : ''), 'line'=>(empty($row['po_line']) ? 'N/A' : $row['po_line']), 'qty'=>($row['qty'] > 0 ? round($row['qty'],3) : ''), 'manual_qty'=>$manual_qty[$i], 'site'=>($row['siteid'] == $siteid ? $manifest_label : ($row['siteid'] > 0 ? get_contact($dbc, $row['siteid']) : 'UNASSIGNED')), 'notes'=>$row['notes'], 'weight'=>$weight];	
			}

			if(in_array('pdf_collapse',$manifest_fields)) {
				$columns['po'] += (!empty($row['po_num']) ? 1 : 0);
				$columns['vendor'] += ($row['vendor'] > 0 ? 1 : 0);
				$columns['line'] += (!empty($row['po_line']) ? 1 : 0);
				$columns['qty'] += ($row['qty'] > 0 ? 1 : 0);
				$columns['manual_qty'] += ($manual_qty[$i] > 0 ? 1 : 0);
				$columns['site'] += ($row['siteid'] > 0 ? 1 : 0);
				$columns['notes'] += (!empty($row['notes']) ? 1 : 0);
				$columns['weight'] += (!empty($weight) ? 1 : 0);
			}
		}
	}
	$col_count = (in_array('file',$manifest_fields) ? 1 : 0) + (in_array('po',$manifest_fields) && $columns['po'] > 0 ? 1 : 0) + (in_array('vendor',$manifest_fields) && $columns['vendor'] > 0 ? 1 : 0) + (in_array('line',$manifest_fields) && $columns['line'] > 0 ? 1 : 0) + ((in_array('qty',$manifest_fields) || in_array('group pieces',$manifest_fields)) && $columns['qty'] > 0 ? 1 : 0) + (in_array('manual qty',$manifest_fields) && $columns['manual_qty'] > 0 ? 1 : 0) + (in_array('site',$manifest_fields) && $columns['site'] > 0 ? 1 : 0) + (in_array('notes',$manifest_fields) && $columns['notes'] > 0 ? 1 : 0) + (!in_array('group pieces',$manifest_fields) ? 1 : 0) + (in_array('weight',$manifest_fields) && $columns['weight'] > 0 ? 1 : 0);
	$html = '<table style="width:100%;border:none;">
		<tr>
			'.(file_exists('download/'.$logo) ? '<td style="width: 120px;"><img src="download/'.$logo.'" style="margin-right:20px;margin-bottom:20px;width:100px;"><br />&nbsp;</td>' : '').'
			<td><br /><br /><b><i><u>'.$manifest_label.'</b></i></u><br /><br /><b>Date:</b><br /><b>File Ref:</b></td>
			<td><br /><br /><br /><br /><b>'.$manifest_date.'</b><br /><b>'.$manifest_ref.'</b></td>
		</tr>
	</table>
	<br />
	<table style="width:100%;">
		<tr>
			'.(in_array('file',$manifest_fields) ? '<th style="border:1px solid black; text-align:center;">FILE #</th>' : '').'
			'.(in_array('po',$manifest_fields) && $columns['po'] > 0 ? '<th style="border:1px solid black; text-align:center;">PO</th>' : '').'
			'.(in_array('vendor',$manifest_fields) && $columns['vendor'] > 0 ? '<th style="border:1px solid black; text-align:center;">VENDOR / SHIPPER</th>' : '').'
			'.(in_array('line',$manifest_fields) && $columns['line'] > 0 ? '<th style="border:1px solid black; text-align:center;">LINE ITEM #</th>' : '').'
			'.((in_array('qty',$manifest_fields) || in_array('group pieces',$manifest_fields)) && $columns['qty'] > 0 ? '<th style="border:1px solid black; text-align:center;">LAND TRAN PIECE COUNT</th>' : '').'
			'.(in_array('manual qty',$manifest_fields) && $columns['manual_qty'] > 0 ? '<th style="border:1px solid black; text-align:center;">LAND TRAN PIECE COUNT</th>' : '').'
			'.(in_array('weight',$manifest_fields) && $columns['weight'] > 0 ? '<th style="border:1px solid black; text-align:center;">WEIGHT</th>' : '').'
			'.(in_array('site',$manifest_fields) && $columns['site'] > 0 ? '<th style="border:1px solid black; text-align:center;">SITE</th>' : '').'
			'.(in_array('notes',$manifest_fields) && $columns['notes'] > 0 ? '<th style="border:1px solid black; text-align:center;">NOTES</th>' : '').'
		</tr>
		<tr style="background-color:'.$row_colour_1.'"><td style="font-size:5px;" colspan="'.$col_count.'">&nbsp;</td></tr>';
		$site_notes = '';
		if($siteid > 0) {
			$site_notes = html_entity_decode($dbc->query("SELECT `notes` FROM `contacts_description` WHERE `contactid`='$siteid'")->fetch_assoc()['notes']);
		}
		$i = 0;
		foreach($lines as $key => $row) {
			if(in_array('group pieces po',$manifest_fields)) {
				$row['file'] = implode('<br />', array_unique(array_filter($row['file'])));
				$row['vendor'] = implode('<br />', array_unique(array_filter($row['vendor'])));
				$row['line'] = implode('<br />', array_unique(array_filter($row['line'])));
				$row['qty'] = ($row['qty'] > 0 ? round($row['qty'],3) : '');
				$row['manual_qty'] = ($row['manual_qty'] > 0 ? round($row['manual_qty'],3) : '');
				$row['site'] = implode('<br />', array_unique(array_filter($row['site'])));
				$row['notes'] = implode('<br />', array_unique(array_filter($row['notes'])));
				$row_weight = [];
				foreach($row['weight'] as $weight_unit => $weight_num) {
					$row_weight[] = $weight_num.' '.$weight_unit;
				}
				$row['weight'] = implode('<br />', $row_weight);
			}
			$html .= '<tr style="background-color:'.($i % 2 == 0 ? $row_colour_1 : $row_colour_2).'">
				'.(in_array('file',$manifest_fields) ? '<td data-title="FILE #" style="text-align:center;">'.$row['file'].'</td>' : '').'
				'.(in_array('po',$manifest_fields) && $columns['po'] > 0 ? '<td data-title="PO" style="text-align:center;">'.$row['po'].'</td>' : '').'
				'.(in_array('vendor',$manifest_fields) && $columns['vendor'] > 0 ? '<td data-title="VENDOR / SHIPPER" style="text-align:center;">'.$row['vendor'].'</td>' : '').'
				'.(in_array('line',$manifest_fields) && $columns['line'] > 0 ? '<td data-title="LINE ITEM #" style="text-align:center;">'.$row['line'].'</td>' : '').'
				'.((in_array('qty',$manifest_fields) || in_array('group pieces',$manifest_fields)) && $columns['qty'] > 0 ? '<td data-title="LAND TRAN PIECE COUNT" style="text-align:center;">'.$row['qty'].'</td>' : '').'
				'.(in_array('manual qty',$manifest_fields) && $columns['manual_qty'] > 0 ? '<td data-title="LAND TRAN PIECE COUNT" style="text-align:center;">'.$row['manual_qty'].'</td>' : '').'
				'.(in_array('weight',$manifest_fields) && $columns['weight'] > 0 ? '<td data-title="WEIGHT" style="text-align:center;">'.$row['weight'].'</td>' : '').'
				'.(in_array('site',$manifest_fields) && $columns['site'] > 0 ? '<td data-title="SITE" style="text-align:center;">'.$row['site'].'</td>' : '').'
				'.(in_array('notes',$manifest_fields) && $columns['notes'] > 0 ? '<td data-title="NOTES" style="text-align:center;">'.html_entity_decode($row['notes']).'</td>' : '').'
			</tr>
			<tr style="background-color:'.($i % 2 == 0 ? $row_colour_1 : $row_colour_2).'"><td style="font-size:5px;" colspan="'.$col_count.'">&nbsp;</td></tr>';
			$i++;
		}
		$html .= '<tr style="border-top: 1px solid black;">
			'.(in_array('file',$manifest_fields) ? '<td style="border-top: 1px solid black;"></td>' : '').'
			'.(in_array('po',$manifest_fields) && $columns['po'] > 0 ? '<td style="border-top: 1px solid black;"></td>' : '').'
			'.(in_array('vendor',$manifest_fields) && $columns['vendor'] > 0 ? '<td style="border-top: 1px solid black;"></td>' : '').'
			'.(in_array('line',$manifest_fields) && $columns['line'] > 0 ? '<td style="border-top: 1px solid black;"></td>' : '').'
			'.((in_array('qty',$manifest_fields) || in_array('group pieces',$manifest_fields)) && $columns['qty'] > 0 ? '<td data-title="TOTAL LAND TRAN PIECE COUNT" style="text-align:center; border-top: 1px solid black;">'.$sum_qty. ' TOTAL PIECES</td>' : '').'
			'.(in_array('manual qty',$manifest_fields) && $columns['manual_qty'] > 0 ? '<td data-title="TOTAL LAND TRAN PIECE COUNT" style="text-align:center; border-top: 1px solid black;">'.$sum_qty.' TOTAL PIECES</td>' : '').'
			'.(in_array('weight',$manifest_fields) && $columns['weight'] > 0 ? '<td style="text-align:center; border-top: 1px solid black;">'.(in_array('total weight lb',$manifest_fields) ? $total_weight.' lbs' : '').'</td>' : '').'
			'.(in_array('site',$manifest_fields) && $columns['site'] > 0 ? '<td style="border-top: 1px solid black;"></td>' : '').'
			'.(in_array('notes',$manifest_fields) && $columns['notes'] > 0 ? '<td style="border-top: 1px solid black;"></td>' : '').'
		</tr>';
		$stamp_img = get_config($dbc, 'stamp_upload');
		$html .= '<tr>
			<td colspan="'.$col_count.'">
				<br /><br /><br />
				'.((in_array('stamp_sign',$manifest_fields) && !empty($stamp_img) && file_exists('download/'.$stamp_img)) ? '<img style="width:150px" src="download/'.$stamp_img.'">' : (empty($signature) ? '<br /><br /><br /><br /></td></tr><tr><td colspan="'.($col_count - 2).'"></td><td colspan="2" style="border-top:1px solid black;text-align:right;">Signature' : ('<img style="width:150px;border-bottom:1px solider black;" src="manifest/signature_'.$manifestid.'_'.$revision.'.png"><br />
				Signed: '.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name'])))).'
			</td>
		</tr>
	</table>';

	class MYPDF extends TCPDF {
		public function Header() {
		}
		public function Footer() {
			// Position at 15 mm from bottom
			// $this->SetY(-15);
			// $this->SetFont('helvetica', '', 9);
			// $footer_text = '<p style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().'</p>';
			// $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
			$this->SetY(-25);
			$this->SetFont('helvetica', '', 9);
			$this->writeHTMLCell(0, 0, '', '', html_entity_decode(TICKET_FOOTER), 0, 0, false, "C", true);
		}
	}
	$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 9);
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('manifest/manifest_'.$manifestid.'_'.$revision.'.pdf', 'F');
	echo "<script>
	window.top.open('manifest/manifest_".$manifestid."_".$revision.".pdf', '_blank');
	window.location.replace('?tile_name=".$_GET['tile_name']."&tab=manifest&site=recent');
	</script>";
	// echo $html;
} ?>
<script>
$(document).ready(function() {
	$('select[name=siteid],input[name=notes]').change(saveField);
});
function saveFieldMethod(field) {
	var value = '';
	if(field.name == 'siteid') {
		value_list = [];
		$(field).find('option:selected').each(function() {
			value_list.push(this.value);
		});
		value = value_list.join(',');
	} else {
		value = field.value;
	}
	var id_list = [$(field).data('id')];
	if(field.name == 'notes') {
		id_list = $(field).data('multi-id').toString().split(',');
	}
	id_list.forEach(function(id) {
		$.ajax({
			url: 'ticket_ajax_all.php?action=update_fields',
			method: 'POST',
			data: {
				table: $(field).data('table'),
				field: field.name,
				value: value,
				id: id,
				id_field: $(field).data('id-field'),
				ticketid: $(field).data('id')
			},
			success: function(response) {
				doneSaving();
			}
		});
	});
}
</script>
<?php $site_notes = '';
if($siteid > 0) {
	$site_notes = html_entity_decode($dbc->query("SELECT `notes` FROM `contacts_description` WHERE `contactid`='$siteid'")->fetch_assoc()['notes']);
}
$site_name = get_contact($dbc, $siteid, 'name_company');
$line_item = explode(',',$manifest['line_items']);
$type = $line_item[0] > 0 ? $dbc->query("SELECT `projecttype` FROM `project` LEFT JOIN `tickets` ON `project`.`projectid`=`tickets`.`projectid` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` WHERE `ticket_attached`.`id`='".$line_item[0]."'")->fetch_assoc()['projecttype'] : '';
$type_label = '';
foreach(explode(',',get_config($dbc, 'project_tabs')) as $type_name) {
	if(config_safe_str($type_name) == $type) {
		$type_label = $type_name;
	}
}
$qty = explode(',',$manifest['qty']);
$col_count = 2; ?>
<form class="form-horizontal" action="" method="POST">
	<button class="btn brand-btn pull-right" name="update" value="update" type="submit">Update Manifest</button>
	<h4 class="scale-to-fill"><p><?= SITES_CAT ?>: <?= get_contact($dbc, $manifest['siteid']) ?></p><?= empty($type) ? '' : '<p>Tab: '.$type_label.'</p>' ?></h4>
	<div class="clearfix"></div>
	<table class="table table-bordered">
		<tr class="hidden-sm hidden-xs">
			<?php if(in_array('file',$manifest_fields)) { $col_count++; ?><th><?= empty($ticket_noun) ? TICKET_NOUN : $ticket_noun ?></th><?php } ?>
			<?php if(in_array('site',$manifest_fields)) { ?><th><?= SITES_CAT ?></th><?php } ?>
			<?php if(in_array('po',$manifest_fields)) { $col_count++; ?><th>PO</th><?php } ?>
			<?php if(in_array('line',$manifest_fields)) { $col_count++; ?><th>Line Item</th><?php } ?>
			<?php if(in_array('vendor',$manifest_fields)) { $col_count++; ?><th>Vendor / Shipper</th><?php } ?>
			<?php if(in_array('manual qty',$manifest_fields)) { $col_count++; ?><th>Qty</th><?php } ?>
			<?php if(in_array('group pieces',$manifest_fields)) { $col_count++; ?><th>Qty</th><?php } ?>
			<?php if(in_array('weight',$manifest_fields)) { $col_count++; ?><th>Weight</th><?php } ?>
			<?php if(in_array('notes',$manifest_fields)) { $col_count++; ?><th>Notes</th><?php } ?>
			<?php if(!in_array('group pieces',$manifest_fields)) { ?><th>Include</th><?php } ?>
		</tr>
		<?php foreach($line_item as $i => $item) {
			$ticket = $dbc->query("SELECT `tickets`.`ticketid`, `tickets`.`ticket_label`, IF(`ticket_attached`.`siteid` IN ('0','',',,') OR `ticket_attached`.`siteid` IS NULL,IF(`piece`.`siteid` IN ('0','',',,') OR `piece`.`siteid` IS NULL,`tickets`.`siteid`,`piece`.`siteid`),`ticket_attached`.`siteid`) `siteid`, `ticket_attached`.`id`, `ticket_attached`.`notes`, IFNULL(`inventory`.`quantity`,`ticket_attached`.`qty`) `qty`, ".(in_array('group pieces po',$manifest_fields) ? "IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_num`" : "GROUP_CONCAT(DISTINCT IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) SEPARATOR '#*#') `po_num`").", `ticket_attached`.`po_line`, `ticket_schedule`.`vendor`, GROUP_CONCAT(`ticket_attached`.`id` SEPARATOR ',') `piece_id`, GROUP_CONCAT(`ticket_attached`.`piece_type` SEPARATOR '#*#') `piece_types` FROM `tickets` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` LEFT JOIN `ticket_attached` `piece` ON `ticket_attached`.`line_id`=`piece`.`id` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`type`='origin' WHERE `ticket_attached`.`id`='$item'")->fetch_assoc();?>
			<tr>
				<?php if(in_array('file',$manifest_fields)) { ?><td data-title="<?= empty($ticket_noun) ? TICKET_NOUN : $ticket_noun ?>"><?php if($tile_security['edit'] > 0) { ?><a href="index.php?<?= $current_tile ?>edit=<?= $ticket['ticketid'] ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,true); return false;"><?= get_ticket_label($dbc, $ticket) ?></a><?php } else { echo get_ticket_label($dbc, $ticket); } ?></td><?php } ?>
				<?php if(in_array('site',$manifest_fields)) { ?><td data-title="<?= SITES_CAT ?>"><a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Contacts/contacts_inbox.php?fields=all_fields&edit=<?= $siteid ?>', '75%', true, true); return false;"><?= $manifest_label ?></a></td><?php } ?>
				<?php if(in_array('po',$manifest_fields)) { ?><td data-title="PO">
					<?php foreach(explode('#*#',$ticket['po_num']) as $po_num) { ?>
						<a href="line_item_views.php?po=<?= $po_num ?>" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><?= $po_num ?></a><br />
					<?php } ?>
				</td><?php } ?>
				<?php if(in_array('line',$manifest_fields)) { ?><td data-title="Line Item"><?= empty($ticket['po_line']) ? 'N/A' : $ticket['po_line'] ?></td><?php } ?>
				<?php if(in_array('vendor',$manifest_fields)) { ?><td data-title="Vendor / Shipper"><?= $ticket['vendor'] > 0 ? '<a href="../Contacts/contacts_inbox.php?fields=all_fields&edit='.$ticket['vendor'].'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;">'.get_contact($dbc, $ticket['vendor'], 'name_company').'</a>' : '<a href="?edit='.$ticket['ticketid'].'" onclick="overlayIFrameSlider(\'edit_ticket_tab.php?ticketid='.$ticket['ticketid'].'&tab=ticket_transport_origin\',\'auto\',true); return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img"></a>' ?></td><?php } ?>
				<?php if(in_array('manual qty',$manifest_fields)) { ?><td data-title="Qty"><input type="number" placeholder="Available: <?= round($ticket['qty']+$qty[$i],3) ?>" name="qty[]" class="form-control" min="0" max="<?= $ticket['qty']+$qty[$i] > 0 ? $ticket['qty']+$qty[$i] : 0 ?>" value="<?= $qty[$i] ?>"></td><?php } ?>
				<?php if(in_array('group pieces',$manifest_fields)) { ?><td data-title="Qty"><img class="inline-img" src="../img/icons/ROOK-add-icon.png" onclick="$(this).closest('td').find('div').toggle();">
						<div style="display:inline-block;max-width:5em;"><input type="number" min="0" value="<?= count(explode(',',$ticket['piece_id'])) ?>" class="form-control" readonly></div>
						<div style="display:none;">
							<?php foreach(explode(',',$ticket['piece_id']) as $i => $piece) { ?>
								<label class="form-checkbox"><input type="checkbox" name="include[]" value="<?= $piece ?>" checked onchange="$(this).closest('td').find('[type=number]').val($(this).closest('td').find(':checked').length); if($(this).is(':checked')) { $(this).closest('td').find('[name=\'mark_unused[]\']').prop('disabled',true); } else { $(this).closest('td').find('[name=\'mark_unused[]\']').prop('disabled',false); }">Piece #<?= $dbc->query("SELECT COUNT(*) FROM `ticket_attached` LEFT JOIN `ticket_attached` `pieces` ON `ticket_attached`.`ticketid`=`pieces`.`ticketid` WHERE `ticket_attached`.`id`='$piece' AND `pieces`.`deleted`=0 AND (`pieces`.`po_num` < `ticket_attached`.`po_num` OR (`ticket_attached`.`po_num`=`pieces`.`po_num` AND (`pieces`.`po_line` < `ticket_attached`.`po_line` OR (`ticket_attached`.`po_line` = `pieces`.`po_line` AND `pieces`.`id` < `ticket_attached`.`id`))))")->fetch_assoc()['count']+1 ?>: <?= explode('#*#',$ticket['piece_types'])[$i] ?></label>
							<?php } ?>
							<input type="hidden" name="mark_unused[]" value="<?= $piece ?>" disabled>
						</div>
					</td><?php } ?>
				<?php if(in_array('weight',$manifest_fields)) { ?><td data-title="Weight">
					<?php foreach(explode(',',$ticket['piece_id']) as $i => $piece) {
						$piece_weights = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `weight`, `weight_units` FROM `ticket_attached` WHERE `id` = '$piece'"));
						$weight = [];
						$inv_weight_units = explode('#*#',$piece_weights['weight_units']);
						foreach(explode('#*#',$piece_weights['weight']) as $id => $inv_weight) {
							if(in_array('weight convert kg to lb',$manifest_fields) && $inv_weight_units[$id] == 'kg') {
								$inv_weight = number_format(($inv_weight*2.20462262185),2);
								$inv_weight_units[$id] = 'lbs';
							}
							$weight[] = $inv_weight.' '.$inv_weight_units[$id];
						}
						$weight = implode(', ', $weight); ?>
						Piece #<?= $dbc->query("SELECT COUNT(*) FROM `ticket_attached` LEFT JOIN `ticket_attached` `pieces` ON `ticket_attached`.`ticketid`=`pieces`.`ticketid` WHERE `ticket_attached`.`id`='$piece' AND `pieces`.`deleted`=0 AND (`pieces`.`po_num` < `ticket_attached`.`po_num` OR (`ticket_attached`.`po_num`=`pieces`.`po_num` AND (`pieces`.`po_line` < `ticket_attached`.`po_line` OR (`ticket_attached`.`po_line` = `pieces`.`po_line` AND `pieces`.`id` < `ticket_attached`.`id`))))")->fetch_assoc()['count']+1 ?>: <?= explode('#*#',$ticket['piece_types'])[$i].' - '.$weight ?><br />
					<?php } ?>
				</td><?php } ?>
				<?php if(in_array('notes',$manifest_fields)) { ?><td data-title="Notes"><?= $site_notes ?><input type="text" name="notes" data-table="ticket_attached" data-id="<?= $ticket['id'] ?>" data-multi-id="<?= $ticket['piece_id'] ?>" data-id-field="id" class="form-control" value="<?= strip_tags(html_entity_decode($ticket['notes'])) ?>"></td><?php } ?>
				<?php if(!in_array('group pieces',$manifest_fields)) { ?><td data-title="Include">
					<label class="form-checkbox any-width"><input type="checkbox" checked name="include[]" value="<?= $ticket['id'] ?>">Include</label>
					<input type="hidden" name="line_rows[]" value="<?= $ticket['id'] ?>">
					<input type="hidden" name="prior_qty[]" value="<?= $ticket['qty'] ?>">
				</td><?php } ?>
			</tr>
		<?php }
		$filter_inv = in_array('hide qty',$manifest_fields) ? 'AND IFNULL(`inventory`.`quantity`,`ticket_attached`.`qty`-`ticket_attached`.`used`) > 0' : '';
		$filter_proj = in_array('sort_project',$manifest_fields) && !empty($type) ? "AND `tickets`.`projectid` IN (SELECT `projectid` FROM `project` WHERE `projecttype`='".filter_var($type,FILTER_SANITIZE_STRING)."')" : '';
		$ticket_sql = "SELECT `tickets`.`ticketid`, `tickets`.`ticket_label`, IF(`ticket_attached`.`siteid` IN ('0','',',,') OR `ticket_attached`.`siteid` IS NULL,IF(`piece`.`siteid` IN ('0','',',,') OR `piece`.`siteid` IS NULL,`tickets`.`siteid`,`piece`.`siteid`),`ticket_attached`.`siteid`) `siteid`, `ticket_attached`.`id`, `ticket_attached`.`notes`, IFNULL(`inventory`.`quantity`,`ticket_attached`.`qty`) `qty`, ".(in_array('group pieces po',$manifest_fields) ? "IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_num`" : "GROUP_CONCAT(DISTINCT IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) SEPARATOR '#*#') `po_num`").", `ticket_attached`.`po_line`, MAX(`ticket_schedule`.`vendor`) `vendor`, GROUP_CONCAT(`ticket_attached`.`id` SEPARATOR ',') `piece_id`, GROUP_CONCAT(`ticket_attached`.`piece_type` SEPARATOR '#*#') `piece_types` FROM `tickets` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` AND `ticket_attached`.`src_table`='inventory' LEFT JOIN `ticket_attached` `piece` ON `ticket_attached`.`line_id`=`piece`.`id` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`type`='origin' WHERE `tickets`.`deleted`=0 AND `ticket_attached`.`deleted`=0 AND `tickets`.`status` != 'Archive' AND `ticket_attached`.`src_table` IN ('inventory','inventory_general') AND CONCAT(',',IF(`ticket_attached`.`siteid` IN ('0','',',,') OR `ticket_attached`.`siteid` IS NULL,IF(`piece`.`siteid` IN ('0','',',,') OR `piece`.`siteid` IS NULL,IF(`tickets`.`siteid` IN ('0','',',,') OR `tickets`.`siteid` IS NULL, 'na',`tickets`.`siteid`),`piece`.`siteid`),`ticket_attached`.`siteid`),',top_25,') LIKE '%,$siteid,%' AND `ticket_attached`.`id` NOT IN ('".implode("','",$line_item)."') $filter_inv $ticket_filter $filter_proj $search GROUP BY ".(in_array('group pieces',$manifest_fields) ? "`tickets`.`ticketid`,`ticket_attached`.`item_id`" : "`ticket_attached`.`id`").(in_array('group pieces po',$manifest_fields) ? ", IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`)" : '')." ORDER BY ".(in_array('ticket_sort',$manifest_fields) ? "`tickets`.`ticketid` DESC," : '')." LPAD(`ticket_attached`.`po_num`,100,0), LPAD(`ticket_attached`.`po_line`,100,0), `tickets`.`ticketid`, `ticket_attached`.`id`";
		$new_lines = $dbc->query($ticket_sql);
		if($new_lines->num_rows > 0) { ?>
			<tr>
				<th colspan="<?= $col_count ?>"><label>Add New Items to Manifest <input type="checkbox" onchange="$('tr.new_row').toggle();"></label></th>
			</tr>
			<?php while($ticket = $new_lines->fetch_assoc()) { ?>
				<tr class="new_row" style="display:none;">
					<?php if(in_array('file',$manifest_fields)) { ?><td data-title="<?= empty($ticket_noun) ? TICKET_NOUN : $ticket_noun ?>"><?php if($tile_security['edit'] > 0) { ?><a href="index.php?<?= $current_tile ?>edit=<?= $ticket['ticketid'] ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,true); return false;"><?= get_ticket_label($dbc, $ticket) ?></a><?php } else { echo get_ticket_label($dbc, $ticket); } ?></td><?php } ?>
					<?php if(in_array('site',$manifest_fields)) { ?><td data-title="<?= SITES_CAT ?>"><a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Contacts/contacts_inbox.php?fields=all_fields&edit=<?= $siteid ?>', '75%', true, true); return false;"><?= $manifest_label ?></a></td><?php } ?>
					<?php if(in_array('po',$manifest_fields)) { ?><td data-title="PO"><a href="line_item_views.php?po=<?= $ticket['po_num'] ?>" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><?= $ticket['po_num'] ?></a></td><?php } ?>
					<?php if(in_array('line',$manifest_fields)) { ?><td data-title="Line Item"><?= empty($ticket['po_line']) ? 'N/A' : $ticket['po_line'] ?></td><?php } ?>
					<?php if(in_array('vendor',$manifest_fields)) { ?><td data-title="Vendor / Shipper"><?= $ticket['vendor'] > 0 ? '<a href="../Contacts/contacts_inbox.php?fields=all_fields&edit='.$ticket['vendor'].'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;">'.get_contact($dbc, $ticket['vendor'], 'name_company').'</a>' : '<a href="?edit='.$ticket['ticketid'].'" onclick="overlayIFrameSlider(\'edit_ticket_tab.php?ticketid='.$ticket['ticketid'].'&tab=ticket_transport_origin\',\'auto\',true); return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img"></a>' ?></td><?php } ?>
					<?php if(in_array('manual qty',$manifest_fields)) { ?><td data-title="Qty"><input type="number" placeholder="Available: <?= round($ticket['qty']+$qty[$i],3) ?>" name="qty[]" class="form-control" min="0" max="<?= $ticket['qty']+$qty[$i] > 0 ? $ticket['qty']+$qty[$i] : 0 ?>" value="<?= $qty[$i] ?>"></td><?php } ?>
					<?php if(in_array('group pieces',$manifest_fields)) { ?><td data-title="Qty"><img class="inline-img" src="../img/icons/ROOK-add-icon.png" onclick="$(this).closest('td').find('div').toggle();">
							<div style="display:inline-block;max-width:5em;"><input type="number" min="0" value="0" class="form-control" readonly></div>
							<div style="display:none;">
								<?php foreach(explode(',',$ticket['piece_id']) as $i => $piece) { ?>
									<label class="form-checkbox"><input type="checkbox" name="include[]" value="<?= $piece ?>" onchange="$(this).closest('td').find('[type=number]').val($(this).closest('td').find(':checked').length);">Piece #<?= $dbc->query("SELECT COUNT(*) count FROM `ticket_attached` LEFT JOIN `ticket_attached` `pieces` ON `ticket_attached`.`ticketid`=`pieces`.`ticketid` WHERE `ticket_attached`.`id`='$piece' AND `pieces`.`deleted`=0 AND `pieces`.`id` < `ticket_attached`.`id`")->fetch_assoc()['count']+1 ?>: <?= explode('#*#',$ticket['piece_types'])[$i] ?></label>
								<?php } ?>
							</div>
						</td><?php } ?>
					<?php if(in_array('weight',$manifest_fields)) { ?><td data-title="Weight">
						<?php foreach(explode(',',$ticket['piece_id']) as $i => $piece) {
							$piece_weights = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `weight`, `weight_units` FROM `ticket_attached` WHERE `id` = '$piece'"));
							$weight = [];
							$inv_weight_units = explode('#*#',$piece_weights['weight_units']);
							foreach(explode('#*#',$piece_weights['weight']) as $id => $inv_weight) {
								if(in_array('weight convert kg to lb',$manifest_fields) && $inv_weight_units[$id] == 'kg') {
									$inv_weight = number_format(($inv_weight*2.20462262185),2);
									$inv_weight_units[$id] = 'lbs';
								}
								$weight[] = $inv_weight.' '.$inv_weight_units[$id];
							}
							$weight = implode(', ', $weight); ?>
							Piece #<?= $dbc->query("SELECT COUNT(*) count FROM `ticket_attached` LEFT JOIN `ticket_attached` `pieces` ON `ticket_attached`.`ticketid`=`pieces`.`ticketid` WHERE `ticket_attached`.`id`='$piece' AND `pieces`.`deleted`=0 AND `pieces`.`id` < `ticket_attached`.`id`")->fetch_assoc()['count']+1 ?>: <?= explode('#*#',$ticket['piece_types'])[$i].' - '.$weight ?><br />
						<?php } ?>
					</td><?php } ?>
					<?php if(in_array('notes',$manifest_fields)) { ?><td data-title="Notes"><?= $site_notes ?><input type="text" name="notes" data-table="ticket_attached" data-id="<?= $ticket['id'] ?>" data-multi-id="<?= $ticket['piece_id'] ?>" data-id-field="id" class="form-control" value="<?= strip_tags(html_entity_decode($ticket['notes'])) ?>"></td><?php } ?>
					<?php if(!in_array('group pieces',$manifest_fields)) { ?><td data-title="Include">
						<label class="form-checkbox any-width"><input type="checkbox" name="include[]" checked value="<?= $ticket['id'] ?>">Include</label>
						<input type="hidden" name="line_rows[]" value="<?= $ticket['id'] ?>">
						<input type="hidden" name="prior_qty[]" value="<?= $ticket['qty'] ?>">
					</td><?php } ?>
				</tr>
			<?php }
		} ?>
	</table>
	<div class="form-group">
		<?php $stamp_img = get_config($dbc, 'stamp_upload');
		if(in_array('stamp_sign',$manifest_fields) && !empty($stamp_img) && file_exists('download/'.$stamp_img)) { ?>
			<label class="col-sm-4">Stamp:</label>
			<div class="col-sm-8">
				<img src="download/<?= $stamp_img ?>" style="height:150px;">
			</div>
		<?php } else { ?>
			<label class="col-sm-4">Signature:</label>
			<div class="col-sm-8">
				<?php $output_name = 'signature';
				include_once('../phpsign/sign_multiple.php'); ?>
			</div>
		<?php } ?>
	</div>
	<button class="btn brand-btn pull-right" name="update" value="update" type="submit">Update Manifest</button>
</form>