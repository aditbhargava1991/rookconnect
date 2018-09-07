<?php $style = 'a';
$file_name = 'estimate';
$font_size = 9;
$font_type = 'regular';
$font = 'Encode Sans, sans-serif';
$pdf_logo = '';
$footer_logo = '';
$pdf_size = 9;
$page_ori = 'portrait';
$units = 9;
$left_margin = 9;
$right_margin = 9;
$top_margin = 9;
$header_margin = 9;
$bottom_margin = 9;
$pdf_color = '#000000';
$text = '';
$header_font_colour = '#000000';
$header_font = 'Encode Sans, sans-serif';
$header_font_size = 9;
$header_font_type = 'regular';
$footer_text = '';
$footer_font_colour = '#000000';
$footer_font = 'Encode Sans, sans-serif';
$footer_alignment = 'right';
$footer_font_size = 9;
$footer_font_type = 'regular';
$alignment = 'right';
$font_body = 'Encode Sans, sans-serif';
$font_body_size = 9;
$font_body_type = 'regular';
$pdf_body_color = '#000000';
$heading_color = '#000000';
$page_numbers = 'bottom_main';
if(!empty($settings)) {
	$file_name = empty($settings['file_name']) ? 'estimate' : $settings['file_name'];
	$font_size = empty($settings['font_size']) ? '9' : $settings['font_size'];
	$font_type = empty($settings['font_type']) ? 'regular' : $settings['font_type'];
	$font = empty($settings['font']) ? 'Encode Sans, sans-serif' : $settings['font'];
	$pdf_logo = empty($settings['pdf_logo']) ? '' : $settings['pdf_logo'];
	$footer_logo = empty($settings['footer_logo']) ? '' : $settings['footer_logo'];
	$pdf_size = empty($settings['pdf_size']) ? '9' : $settings['pdf_size'];
	$page_ori = empty($settings['page_ori']) ? 'portrait' : $settings['page_ori'];
	$units = empty($settings['units']) ? '9' : $settings['units'];
	$left_margin = empty($settings['left_margin']) ? '9' : $settings['left_margin'];
	$right_margin = empty($settings['right_margin']) ? '9' : $settings['right_margin'];
	$top_margin = empty($settings['top_margin']) ? '9' : $settings['top_margin'];
	$header_margin = empty($settings['header_margin']) ? '9' : $settings['header_margin'];
	$bottom_margin = empty($settings['bottom_margin']) ? '9' : $settings['bottom_margin'];
	$pdf_color = empty($settings['pdf_color']) ? '#000000' : $settings['pdf_color'];
	$text = empty($settings['text']) ? '' : $settings['text'];
	$header_font_colour = empty($settings['header_font_colour']) ? '#000000' : $settings['header_font_colour'];
	$header_font = empty($settings['header_font']) ? 'Encode Sans, sans-serif' : $settings['header_font'];
	$header_font_size = empty($settings['header_font_size']) ? '9' : $settings['header_font_size'];
	$header_font_type = empty($settings['header_font_type']) ? 'regular' : $settings['header_font_type'];
	$footer_text = empty($settings['footer_text']) ? '' : $settings['footer_text'];
	$footer_font_colour = empty($settings['footer_font_colour']) ? '#000000' : $settings['footer_font_colour'];
	$footer_font = empty($settings['footer_font']) ? 'Encode Sans, sans-serif' : $settings['footer_font'];
	$footer_alignment = empty($settings['footer_alignment']) ? 'right' : $settings['footer_alignment'];
	$footer_font_size = empty($settings['footer_font_size']) ? '9' : $settings['footer_font_size'];
	$footer_font_type = empty($settings['footer_font_type']) ? 'regular' : $settings['footer_font_type'];
	$alignment = empty($settings['alignment']) ? 'right' : $settings['alignment'];
	$font_body = empty($settings['font_body']) ? 'Encode Sans, sans-serif' : $settings['font_body'];
	$font_body_size = empty($settings['font_body_size']) ? '9' : $settings['font_body_size'];
	$font_body_type = empty($settings['font_body_type']) ? 'regular' : $settings['font_body_type'];
	$pdf_body_color = empty($settings['pdf_body_color']) ? '#000000' : $settings['pdf_body_color'];
	$heading_color = empty($settings['heading_color']) ? '#000000' : $settings['heading_color'];
	$page_numbers = empty($settings['page_numbers']) ? 'bottom_main' : $settings['page_numbers'];
}
$us_exchange = json_decode(file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'), TRUE);
$us_rate = $us_exchange['observations'][count($us_exchange['observations']) - 1]['FXUSDCAD']['v'];
$us_rate_no_auto = get_config($dbc, 'disable_us_auto_convert');
$heading_counter_color = '#FFFFFF';
$val = 0;
for($i = 1; $i < 6; $i+=2) {
	$val += hexdec(substr($pdf_color,$i,2));
}
if($val > 576) {
	$heading_counter_color = '#000000';
}
if($estimateid > 0) {
	$file_name .= '_'.$estimateid.'.pdf';
} else {
	$file_name .= '_[ID].pdf';
}

if($estimateid > 0) {
	$estimate_cover = mysqli_query($dbc, "SELECT `businessid`, `created_date`, `expiry_date` FROM `estimates` WHERE `estimateid`='$estimateid'");
} else {
	$estimate_cover = mysqli_query($dbc, "SELECT 0 `businessid`, '".date('Y-m-d')."' `created_date`, '".date('Y-m-d',strtotime('+1month'))."' `expiry_date`");
}
$estimate_cover = mysqli_fetch_assoc($estimate_cover);
$cover_page = '<table style="height:90%; width:100%;">
	<tr>
		'.($page_numbers == 'top_left_all' ? '<td style="text-align: left; width: 10px;">1</td>' : '').'
		<td style="text-align:'.($settings['cover_alignment'] == 'centre' ? 'center' : $settings['cover_alignment']).';">'.(in_array($settings['cover_alignment'], ['left','centre','right']) && $settings['cover_logo'] != '' ? '<img src="download/'.$settings['cover_logo'].'" style="max-width:100%;width:'.($settings['cover_logo_height'] > 0 ? $settings['cover_logo_height'] : 20).'%;"/>' : '').'</td>
		'.($page_numbers == 'top_cover' ? '<td style="text-align: right; vertical-align: top; width: 10px;">1</td>' : '').'
	</tr>
	<tr>
		'.($page_numbers == 'top_left_all' ? '<td></td>' : '').'
		<td style="color:'.$settings['cover_font_colour'].';font-size:'.$settings['cover_font_size'].'px;'.($settings['cover_font_type'] == 'bold' || $settings['cover_font_type'] == 'bold_italic' ? 'font-weight:900;' : '').($settings['cover_font_type'] == 'italic' || $settings['cover_font_type'] == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$settings['font'].';margin-top:95px;">
			<div style="'.($settings['cover_text_alignment'] == 'top' ? 'height:90%;' : '').'">
				'.(in_array($settings['cover_alignment'], ['mid_left','mid_centre','mid_right']) && $settings['cover_logo'] != '' ? '<div style="text-align:'.str_replace(['mid_left','mid_centre','mid_right'], ['left','center','right'], $settings['cover_alignment']).'; width: 100%;"><img src="download/'.$settings['cover_logo'].'" style="max-width:100%;width:'.($settings['cover_logo_height'] > 0 ? $settings['cover_logo_height'] : 20).'%;"/></div>' : '').'
				'.str_replace(['[RECIPIENT]','[CREATED]','[EXPIRY]'],[($estimate_cover['businessid'] > 0 ? get_client($dbc,$estimate_cover['businessid']) : 'N/A'),$estimate_cover['created_date'],$estimate_cover['expiry_date']],html_entity_decode($settings['cover_text'])).'</div>
		</td>
		'.($page_numbers == 'top_cover' ? '<td></td>' : '').'
	</tr>
</table>';

$pages_content = '<table style="height:80%;width:100%;">
	<tr>
		<td style="text-align:'.($settings['pages_alignment'] == 'centre' ? 'center' : $settings['pages_alignment']).';"><img src="download/'.$settings['pages_logo'].'" style="max-width:100%;width:'.($settings['pages_logo_width'] > 0 ? $settings['pages_logo_width'] : 20).'%;"/></td>
	</tr>
	<tr>
		<td style="color:'.$settings['pages_font_size'].';font-size:'.$settings['pages_font_size'].'px;'.($settings['pages_font_type'] == 'bold' || $settings['pages_font_type'] == 'bold_italic' ? 'font-weight:900;' : '').($settings['pages_font_type'] == 'italic' || $settings['pages_font_type'] == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$settings['font'].';margin-top:95px;">
			'.html_entity_decode($settings['pages_text']).'
		</td>
	</tr>
</table>';

$toc_content = '';/*'<table style="height:80%;width:100%;">
	<tr>
		<td style="color:'.$settings['pages_font_size'].';font-size:'.$settings['pages_font_size'].'px;'.($settings['pages_font_type'] == 'bold' || $settings['pages_font_type'] == 'bold_italic' ? 'font-weight:900;' : '').($settings['pages_font_type'] == 'italic' || $settings['pages_font_type'] == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$settings['font'].';margin-top:95px;">';
$toc_content .= '<h4>Estimate Scope</h4>';
$toc_content .= '</td>
	</tr>
</table>';*/

$header_html = '<table style="height:10%;width:100%;">
	<tr>
		'.($page_numbers == 'top_left_all' || $page_numbers == 'top_left_mn' ? '<td style="text-align: left; width: 10px;">[PAGE #]</td>' : '').'
		'.($alignment != 'centre' && $alignment != 'right' && $pdf_logo != '' ? '<td width="16%"><img src="download/'.$pdf_logo.'" style="max-width:100%;width:'.($pdf_logo_width > 0 ? $pdf_logo_width : 10).'%;"/></td>' : '').'
		<td style="color:'.$header_font_colour.';font-size:'.$header_font_size.'px;'.($header_font_type == 'bold' || $header_font_type == 'bold_italic' ? 'font-weight:900;' : '').($header_font_type == 'italic' || $header_font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$header_font.';margin-top:95px;text-align:'.($alignment == 'left' ? 'right' : 'left').';width:'.($alignment == 'centre' ? '42%' : '84%').';">'.$text.'</td>
		'.(($alignment == 'centre' || $alignment == 'right') && $pdf_logo != '' ? '<td width="16%"><img src="download/'.$pdf_logo.'" style="max-width:100%;width:'.($pdf_logo_width > 0 ? $pdf_logo_width : 10).'%;"/></td>' : '').'
		'.($alignment == 'centre' && $pdf_logo != '' ? '<td width="42%"></td>' : '').'
		'.($page_numbers == 'top_cover' || $page_numbers == 'top_main' ? '<td style="text-align: right; vertical-align: top; width: 10px;">[PAGE #]</td>' : '').'
	</tr>
</table>';
$html = '<div style="height:80%;"><table cellspacing="'.$left_margin.'px" style="border:5px solid '.$pdf_color.';" width="100%">
	<tr>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$heading_color.';font-size:'.$font_size.'px;'.($font_type == 'bold' || $font_type == 'bold_italic' ? 'font-weight:900;' : '').($font_type == 'italic' || $font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font.';width:10%;vertical-align:top;">Cost Estimate #'.$estimateid.':</td>
		<td rowspan="2" style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';width:25%;vertical-align:top;">
			'.(!empty($estimate['clientid']) ? get_contact($dbc, $estimate['clientid']).'<br>' : '').'
            '.get_client($dbc, $estimate['businessid']).'<br>
			'.get_address($dbc, $estimate['businessid']).'
		</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$heading_color.';font-size:'.$font_size.'px;'.($font_type == 'bold' || $font_type == 'bold_italic' ? 'font-weight:900;' : '').($font_type == 'italic' || $font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font.';width:10%;vertical-align:top;">Date:</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';width:25%;vertical-align:top;">'.$estimate['created_date'].'</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$heading_color.';font-size:'.$font_size.'px;'.($font_type == 'bold' || $font_type == 'bold_italic' ? 'font-weight:900;' : '').($font_type == 'italic' || $font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font.';width:10%;vertical-align:top;">Expiration:</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';width:20%;vertical-align:top;">'.$estimate['expiry_date'].'</td>
	</tr>
	<tr>
		<td></td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$heading_color.';font-size:'.$font_size.'px;'.($font_type == 'bold' || $font_type == 'bold_italic' ? 'font-weight:900;' : '').($font_type == 'italic' || $font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font.';vertical-align:top;">Sales Person:</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;">';
foreach(explode(',',$estimate['assign_staffid']) as $staff) {
	if($staff > 0) {
		$html .= get_contact($dbc, $staff).'<br />';
	}
}
// Get the list of headings to use in the Scope Table
$heading_order = explode('#*#', get_config($dbc, 'estimate_field_order'));
if(in_array('Scope Detail',$config) && !in_array_starts('Detail',$heading_order)) {
	$heading_order[] = 'Detail***Scope Detail';
}
if(in_array('Scope Billing',$config) && !in_array_starts('Billing Frequency',$heading_order)) {
	$heading_order[] = 'Billing Frequency***Billing Frequency';
}
$col_width = 0;
foreach($heading_order as $order_info) {
	$order_info = explode('***',$order_info);
	// Count each column in use, and count description as three times wider
	switch($order_info[0]) {
		case 'Description':
			$col_width += 2;
		case 'UOM':
		case 'Quantity':
		case 'Estimate Price':
		case 'Total':
			$col_width += 1;
			break;
		case 'Total Multiple':
			if($estimate['quote_multiple'] > 1) {
				$col_width += 1;
			}
			break;
	}
}
$col_width = 1 / $col_width * 100;
$html .= '</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$heading_color.';font-size:'.$font_size.'px;'.($font_type == 'bold' || $font_type == 'bold_italic' ? 'font-weight:900;' : '').($font_type == 'italic' || $font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font.';vertical-align:top;">'.PROJECT_NOUN.' #:</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;">'.$estimate['projectid'].'</td>
	</tr>
	<tr>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$heading_color.';font-size:'.$font_size.'px;'.($font_type == 'bold' || $font_type == 'bold_italic' ? 'font-weight:900;' : '').($font_type == 'italic' || $font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font.';vertical-align:top;">AFE#:</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;">'.$estimate['afe_number'].'</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$heading_color.';font-size:'.$font_size.'px;'.($font_type == 'bold' || $font_type == 'bold_italic' ? 'font-weight:900;' : '').($font_type == 'italic' || $font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font.';vertical-align:top;">Payment Terms:</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;">'.$estimate['payment_terms'].'</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$heading_color.';font-size:'.$font_size.'px;'.($font_type == 'bold' || $font_type == 'bold_italic' ? 'font-weight:900;' : '').($font_type == 'italic' || $font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font.';vertical-align:top;">Due Period:</td>
		<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;">'.$estimate['payment_due'].'</td>
	</tr>
</table>
<table style="border: solid 1px black;width:100%;text-align:center;">
	<tr style="border-bottom: solid 1px black;color:'.$heading_counter_color.';background-color:'.$pdf_color.';font-size:'.$font_size.'px;'.($font_type == 'bold' || $font_type == 'bold_italic' ? 'font-weight:900;' : '').($font_type == 'italic' || $font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font.';">';
		foreach($heading_order as $order_info) {
			$order_info = explode('***',$order_info);
			switch($order_info[0]) {
				case 'Description':
					$html .= '<td style="border-right: 1px solid black;padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;width:'.($col_width * 3).'%">'.(empty($order_info[1]) ? $order_info[0] : $order_info[1]).'</td>';
					break;
				case 'UOM':
				case 'Quantity':
				case 'Estimate Price':
				case 'Total':
					$html .= '<td style="border-right: 1px solid black;padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;width:'.$col_width.'%">'.(empty($order_info[1]) ? $order_info[0] : $order_info[1]).'</td>';
					break;
				case 'Total Multiple':
					if($estimate['quote_multiple'] > 1) {
						$html .= '<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;width:'.$col_width.'%">'.str_replace('[COUNT]',$estimate['quote_multiple'],$order_info[1]).'</td>';
					}
					break;
			}
		}
		$html .= '</tr>';
if($estimateid > 0) {
	$headings = mysqli_query($dbc, "SELECT `heading`, `rate_card` FROM `estimate_scope` WHERE `estimateid`='".$estimateid."' AND `qty` != 0 AND `deleted`=0 GROUP BY `heading`, `rate_card` ORDER BY `rate_card`, MIN(`sort_order`)");
} else {
	$headings = mysqli_query($dbc, "SELECT 'Estimate Scope' `heading`, '' `rate_card`");
}
while($heading = mysqli_fetch_assoc($headings)) {
	$rates = explode(':',$heading['rate_card']);
	$rate_name = '';
	if($rates[0] == 'SCOPE') {
		$rate_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `rate_card_estimate_scopes` WHERE `id`='".$rates[1]."'"))['rate_card_name'];
	}
	$html .= '<tr style="background-color:'.$pdf_sub_color.';color:'.$pdf_sub_text_color.';font-size:'.$font_size.'px;'.($font_type == 'bold' || $font_type == 'bold_italic' ? 'font-weight:900;' : '').($font_type == 'italic' || $font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font.';">
			<td colspan="6" style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;text-align:center;">'.($rate_name == '' ? '' : $rate_name.' - ').$heading['heading'].'</td>
		</tr>';
	if($estimateid > 0) {
		$lines = mysqli_query($dbc, "SELECT * FROM `estimate_scope` WHERE `deleted`=0 AND `estimateid`='".$estimateid."' AND `qty` != 0 AND `heading`='".$heading['heading']."' ORDER BY `sort_order`");
	} else {
		$lines = mysqli_query($dbc, "SELECT 'miscellaneous' `src_table`, 'Line Item' `description`, 5 `qty`, 'each' `uom`, 200 `price`, 1000 `retail`, 5000 `multiple`");
	}
	while($line = mysqli_fetch_assoc($lines)) {
		$html .= '<tr style="border:1px solid #000000;">';
		foreach($heading_order as $order_info) {
			$order_info = explode('***',$order_info);
			switch($order_info[0]) {
				case 'Description':
					$html .= '<td style="border-bottom: 1px solid black;border-right: 1px solid black;padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;">';
					if($line['src_table'] == 'equipment' && $line['src_id'] > 0) {
						$html .= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `equipmentid`='{$line['src_id']}'"))['label'];
					} else if($line['src_table'] == 'inventory' && $line['src_id'] > 0) {
						$html .= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) label FROM `inventory` WHERE `inventoryid`='{$line['src_id']}'"))['label'];
					} else if($line['src_table'] == 'labour' && $line['src_id'] > 0) {
						$html .= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`labour_type`,''),' ',IFNULL(`category`,''),' ',IFNULL(`heading`,''),' ',IFNULL(`name`,'')) label FROM `labour` WHERE `labourid`='{$line['src_id']}'"))['label'];
					} else if($line['src_table'] == 'material' && $line['src_id'] > 0) {
						$html .= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`sub_category`,''),' ',IFNULL(`name`,'')) label FROM `material` WHERE `materialid`='{$line['src_id']}'"))['label'];
					} else if($line['src_table'] == 'position' && $line['src_id'] > 0) {
						$html .= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` label FROM `positions` WHERE `position_id`='{$line['src_id']}'"))['label'];
					} else if($line['src_table'] == 'products' && $line['src_id'] > 0) {
						$html .= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `products` WHERE `productid`='{$line['src_id']}'"))['label'];
					} else if($line['src_table'] == 'services' && $line['src_id'] > 0) {
						$html .= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `services` WHERE `serviceid`='{$line['src_id']}'"))['label'];
					} else if($line['src_table'] == 'vpl' && $line['src_id'] > 0) {
						$html .= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`product_name`,'')) label FROM `vendor_price_list` WHERE `inventoryid`='{$line['src_id']}'"))['label'];
					} else if($line['src_table'] != 'miscellaneous' && $line['src_id'] > 0) {
						$html .= get_contact($dbc, $line['src_id']);
					} else {
						$html .= $line['description'];
					}
					$html .= '</td>';
					break;
				case 'UOM':
					$html .= '<td style="border-bottom: 1px solid black;border-right: 1px solid black;padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;">'.($line['src_table'] != 'notes' ? $line['uom'] : '').'</td>';
					break;
				case 'Quantity':
					$html .= '<td style="border-bottom: 1px solid black;border-right: 1px solid black;padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;">'.($line['src_table'] != 'notes' ? round($line['qty'],2) : '').'</td>';
					break;
				case 'Estimate Price':
					$html .= '<td style="border-bottom: 1px solid black;border-right: 1px solid black;padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;text-align:right;">'.($line['src_table'] != 'notes' ? '$'.number_format($line['price'],2).($us_rate_no_auto == 'true' && $line['pricing'] == 'usd_cpu' ? ' USD' : '') : '').'</td>';
					break;
				case 'Total':
					$html .= '<td style="border-bottom: 1px solid black;border-right: 1px solid black;padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;text-align:right;">'.($line['src_table'] != 'notes' ? '$'.number_format($line['retail'],2).($us_rate_no_auto == 'true' && $line['pricing'] == 'usd_cpu' ? ' USD' : '') : '').'</td>';
					break;
				case 'Total Multiple':
					if($estimate['quote_multiple'] > 1) {
						$html .= '<td style="padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;">'.($line['src_table'] != 'notes' ? '$'.number_format($line['multiple'],2).($us_rate_no_auto == 'true' && $line['pricing'] == 'usd_cpu' ? ' USD' : '') : '').'</td>';
					}
					break;
			}
		}
		$html .= '</tr>';
	}
}
$html .= '<tr>
		<td colspan="6" style="text-align:left;padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.$font_body_size.'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;">'.html_entity_decode(get_config($dbc, 'quote_sign_notes')).'</td>
	</tr>
	<tr>
		<td colspan="6" style="text-align:left;padding:'.$top_margin.'px '.$right_margin.'px '.$bottom_margin.'px '.$left_margin.'px;color:'.$pdf_body_color.';font-size:'.($font_body_size-3).'px;'.($font_body_type == 'bold' || $font_body_type == 'bold_italic' ? 'font-weight:900;' : '').($font_body_type == 'italic' || $font_body_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$font_body.';vertical-align:top;">'.html_entity_decode(get_config($dbc, 'quote_terms')).'</td>
	</tr>
</table>';
$notes = mysqli_query($dbc, "SELECT * FROM `estimate_notes` WHERE `estimateid`='$estimateid' AND `heading` NOT IN ('Note','Follow Up Completed')");
while($note = mysqli_fetch_assoc($notes)) {
	$html .= '<h4>'.$note['heading'].'</h4>';
	$html .= html_entity_decode($note['notes']);
}
$html .= '</div>';
$footer_html = '<table style="height:10%;border-collapse: separate; border-spacing:'.$top_margin.'px '.$left_margin.'px;width:100%;">
	<tr>
		'.($page_numbers == 'bot_left_all' || $page_numbers == 'bot_left_mn' ? '<td style="text-align: right; width: 10px;">[PAGE #]</td>' : '').'
		'.($footer_alignment != 'centre' && $footer_alignment != 'right' && $footer_logo != '' ? '<td width="16%"><img src="download/'.$footer_logo.'" style="max-width:100%;height:75px;"/></td>' : '').'
		<td style="color:'.$footer_font_colour.';font-size:'.$footer_font_size.'px;'.($footer_font_type == 'bold' || $footer_font_type == 'bold_italic' ? 'font-weight:900;' : '').($footer_font_type == 'italic' || $footer_font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$footer_font.';margin-top:95px;text-align:'.($footer_alignment == 'left' ? 'right' : 'left').';width:'.($footer_alignment == 'centre' ? '42%' : '84%').';">'.$footer_text.'</td>
		'.(($footer_alignment == 'centre' || $footer_alignment == 'right') && $footer_logo != '' ? '<td width="16%"><img src="download/'.$footer_logo.'" style="max-width:100%;height:75px;"/></td>' : '').'
		'.($footer_alignment == 'centre' && $footer_logo != '' ? '<td width="42%"></td>' : '').'
		'.($page_numbers == 'bottom_cover' || $page_numbers == 'bottom_main' ? '<td style="text-align: right; vertical-align: top; width: 10px;">[PAGE #]</td>' : '').'
	</tr>
</table>';
$cover_footer = '<table style="border-collapse: separate; border-spacing:'.$top_margin.'px '.$left_margin.'px;width:100%; height: 10%;">
	<tr>
		'.($settings['cover_alignment'] == 'bot_left' && $settings['cover_logo'] != '' ? '<td style="height:'.($settings['cover_logo_height'] == '' ? '150' : $settings['cover_logo_height']*30).'px;text-align:right;"><img src="download/'.$settings['cover_logo'].'" style="max-width:130px;max-height:100%;"/></td>' : '').'
		<td style="color:'.$footer_font_colour.';font-size:'.$footer_font_size.'px;'.($footer_font_type == 'bold' || $footer_font_type == 'bold_italic' ? 'font-weight:900;' : '').($footer_font_type == 'italic' || $footer_font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$footer_font.';margin-top:95px;text-align:left; width:100%;">'.($page_numbers == 'bot_left_all' ? '1' : '').'</td>
		'.($settings['cover_alignment'] == 'bot_centre' && $settings['cover_logo'] != '' ? '<td style="height:'.($settings['cover_logo_height'] == '' ? '150' : $settings['cover_logo_height']*30).'px;text-align:right;width:55%;"><img src="download/'.$settings['cover_logo'].'" style="max-width:130px;max-height:100%;"/></td>' : '').'
		<td style="color:'.$footer_font_colour.';font-size:'.$footer_font_size.'px;'.($footer_font_type == 'bold' || $footer_font_type == 'bold_italic' ? 'font-weight:900;' : '').($footer_font_type == 'italic' || $footer_font_type == 'bold_italic' ? 'font-style:italic;' : '').'font-family:'.$footer_font.';margin-top:95px;text-align:right;width:100%;">'.($page_numbers == 'bottom_cover' ? '1' : '').'</td>
		'.($settings['cover_alignment'] == 'bot_right' && $settings['cover_logo'] != '' ? '<td style="height:'.($settings['cover_logo_height'] == '' ? '150' : $settings['cover_logo_height']*30).'px;"><img src="download/'.$settings['cover_logo'].'" style="max-width:130px;max-height:100%;"/></td>' : '').'
	</tr>
</table>';