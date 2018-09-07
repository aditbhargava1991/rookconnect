<?php
$settings = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_user_forms`"));

$header_text = !empty($form['header']) ? $form['header'] : $settings['default_header'];
if(!empty($settings['req_header'])) {
    $header_text = $settings['req_header'].'<br />'.$header_text;
}
$header_text = html_entity_decode($header_text);
preg_match_all('/\[\[(.*?)]]/', $header_text, $header_fields);
foreach ($header_fields[1] as $header_field) {
    $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($header_field))];
    if(empty($value)) {
        $value = ' ';
    }
    $header_text = str_replace('[['.$header_field.']]', $value, $header_text);
}   

$header_logo = !empty($form['header_logo']) ? $form['header_logo'] : $settings['default_head_logo'];
$header_align = !empty($form['header_align']) ? $form['header_align'] : $settings['default_head_align'];
$header_font = !empty($form['header_font']) ? $form['header_font'] : $settings['default_head_font'];
$header_size = !empty($form['header_size']) ? $form['header_size'] : $settings['default_head_size'];
$header_color = !empty($form['header_color']) ? $form['header_color'] : $settings['default_head_color'];
$header_styling = !empty($form['header_styling']) ? $form['header_styling'] : $settings['default_head_styling'];
$header_skip_first_page = $form['header_skip_first_page'];

$footer_text = !empty($form['footer']) ? $form['footer'] : $settings['default_footer'];
if(!empty($settings['req_footer'])) {
    $footer_text = $settings['req_footer'].'<br />'.$footer_text;
}
$footer_text = html_entity_decode($footer_text);
preg_match_all('/\[\[(.*?)]]/', $footer_text, $footer_fields);
foreach ($footer_fields[1] as $footer_field) {
    $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($footer_field))];
    if(empty($value)) {
        $value = ' ';
    }
    $footer_text = str_replace('[['.$footer_field.']]', $value, $footer_text);
}

$footer_logo = !empty($form['footer_logo']) ? $form['footer_logo'] : $settings['default_foot_logo'];
$footer_align = !empty($form['footer_align']) ? $form['footer_align'] : $settings['default_foot_align'];
$footer_font = !empty($form['footer_font']) ? $form['footer_font'] : $settings['default_foot_font'];
$footer_size = !empty($form['footer_size']) ? $form['footer_size'] : $settings['default_foot_size'];
$footer_color = !empty($form['footer_color']) ? $form['footer_color'] : $settings['default_foot_color'];
$footer_styling = !empty($form['footer_styling']) ? $form['footer_styling'] : $settings['default_foot_styling'];

$section_heading_font = !empty($form['section_heading_font']) ? $form['section_heading_font'] : $settings['default_section_heading_font'];
$section_heading_size = !empty($form['section_heading_size']) ? $form['section_heading_size'] : $settings['default_section_heading_size'];
$section_heading_color = !empty($form['section_heading_color']) ? $form['section_heading_color'] : $settings['default_section_heading_color'];
$section_heading_styling = !empty($form['section_heading_styling']) ? $form['section_heading_styling'] : $settings['default_section_heading_styling'];

$body_heading_font = !empty($form['body_heading_font']) ? $form['body_heading_font'] : $settings['default_body_heading_font'];
$body_heading_size = !empty($form['body_heading_size']) ? $form['body_heading_size'] : $settings['default_body_heading_size'];
$body_heading_color = !empty($form['body_heading_color']) ? $form['body_heading_color'] : $settings['default_body_heading_color'];
$body_heading_styling = !empty($form['body_heading_styling']) ? $form['body_heading_styling'] : $settings['default_body_heading_styling'];

$body_font = !empty($form['font']) ? $form['font'] : $settings['default_font'];
$body_size = !empty($form['body_size']) ? $form['body_size'] : $settings['default_body_size'];
$body_color = !empty($form['body_color']) ? $form['body_color'] : $settings['default_body_color'];
$body_styling = !empty($form['body_styling']) ? $form['body_styling'] : $settings['default_body_styling'];

$page_format = !empty($form['page_format']) ? $form['page_format'] : $settings['default_page_format'];

$advanced_styling = !empty($form['advanced_styling']) ? $form['advanced_styling'] : '0';
$page_by_page = !empty($form['page_by_page']) ? $form['page_by_page'] : '0';
$hide_labels = !empty($form['hide_labels']) ? $form['hide_labels'] : '0';
$page_settings = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_page` WHERE `form_id` = '$form_id' AND `deleted` = 0 ORDER BY `page`"),MYSQLI_ASSOC);

DEFINE(FORM_HEADER_TEXT, $header_text);
DEFINE(FORM_HEADER_LOGO, $header_logo);
DEFINE(FORM_HEADER_ALIGN, $header_align);
DEFINE(FORM_HEADER_FONT, $header_font);
DEFINE(FORM_HEADER_SIZE, $header_size);
DEFINE(FORM_HEADER_COLOR, $header_color);
DEFINE(FORM_HEADER_STYLING, $header_styling);
DEFINE(FORM_HEADER_SKIP_FIRST_PAGE, $header_skip_first_page);

DEFINE(FORM_FOOTER_TEXT, $footer_text);
DEFINE(FORM_FOOTER_LOGO, $footer_logo);
DEFINE(FORM_FOOTER_ALIGN, $footer_align);
DEFINE(FORM_FOOTER_FONT, $footer_font);
DEFINE(FORM_FOOTER_SIZE, $footer_size);
DEFINE(FORM_FOOTER_STYLING, $footer_styling);
DEFINE(FORM_FOOTER_COLOR, $footer_color);

DEFINE(FORM_PAGE_FORMAT, $page_format);
DEFINE(FORM_ADVANCED_STYLING, $advanced_styling);
DEFINE(FORM_PAGE_BY_PAGE, $page_by_page);
DEFINE(FORM_PAGE_SETTINGS, serialize($page_settings));

DEFINE(FORM_HIDE_LABELS, $hide_labels);
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        $form_pages = unserialize(FORM_PAGE_SETTINGS);
        if(FORM_ADVANCED_STYLING != 1 && FORM_PAGE_BY_PAGE == 1) {
            foreach($form_pages as $form_page) {
                if($this->page == $form_page['page']) {
                    $img_file = $form_page['img'];
                    if(file_get_contents($img_file)) {
                        $this->Image($img_file, 0, 0, 216, 279, '', '', '', false, 300, '', false, false, 0);
                    }
                }
            }
        }

        if(FORM_HEADER_SKIP_FIRST_PAGE == 0 || $this->page > 1) {
            $logo_align = (FORM_HEADER_ALIGN == "L" ? "R" : "L");
            $header_align = FORM_HEADER_ALIGN;
            switch($header_align) {
                case 'L':
                    $align_style = 'text-align: left;';
                    break;
                case 'C':
                    $align_style = 'text-align: center;';
                    break;
                case 'R':
                    $align_style = 'text-align: right;';
            }
            $font_style = 'font-family: '.FORM_HEADER_FONT.'; font-size: '.FORM_HEADER_SIZE.'; color: '.FORM_HEADER_COLOR.'; '.$align_style;
            $font_styling = explode(',',FORM_HEADER_STYLING);
            if(in_array('Bold',$font_styling)) {
                $font_style .= '; font-weight: bold';
            }
            if(in_array('Italic',$font_styling)) {
                $font_style .= '; font-style: Italic';
            }
            if(in_array('Underline',$font_styling)) {
                $font_style .= '; text-decoration: underline';
            }
            $this->setFont('helvetica', '', 9);
            if(FORM_HEADER_LOGO != '') {
                $image_file = '../Form Builder/download/'.FORM_HEADER_LOGO;
                $this->Image($image_file, 10, 5, 0, 25, '', '', 'T', false, 300, $logo_align, false, false, 0, false, false, false);
            }

            if(FORM_HEADER_TEXT != '') {
                $this->setCellHeightRatio(0.7);
                $header_text = '<p style="'.$font_style.'">'.FORM_HEADER_TEXT.'</p>';
                $this->writeHTMLCell(0, 0, 7.5 , 5, $header_text, 0, 0, false, true, $header_align, true);
            }
        }
    }

    //Page footer
    public function Footer() {
        $page_align = (FORM_FOOTER_ALIGN == "R" ? "L" : "R");
        $footer_align = FORM_FOOTER_ALIGN;
        switch($footer_align) {
            case 'L':
                $align_style = 'text-align: left;';
                $page_align_style = 'text-align: right;';
                break;
            case 'C':
                $align_style = 'text-align: center;';
                $page_align_style = 'text-align: right;';
                break;
            case 'R':
                $align_style = 'text-align: right;';
                $page_align_style = 'text-align: left;';
        }
        $font_style = 'font-family: '.FORM_FOOTER_FONT.'; font-size: '.FORM_FOOTER_SIZE.'; color: '.FORM_FOOTER_COLOR.'; '.$align_style;
        $font_styling = explode(',',FORM_FOOTER_STYLING);
        if(in_array('Bold',$font_styling)) {
            $font_style .= '; font-weight: bold';
        }
        if(in_array('Italic',$font_styling)) {
            $font_style .= '; font-style: Italic';
        }
        if(in_array('Underline',$font_styling)) {
            $font_style .= '; text-decoration: underline';
        }

        // Position at 15 mm from bottom
        $this->SetY(-10);
        $this->SetFont('helvetica', '', 6);
        $page_format = str_replace(array('[[CURRENT_PAGE]]','[[TOTAL_PAGE]]'), array($this->getAliasNumPage(),$this->getAliasNbPages()), FORM_PAGE_FORMAT);
        $footer_text = '<p style="'.$page_align_style.'">'.$page_format.'</p>';
        $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, true, $page_align, true);

        if(FORM_FOOTER_TEXT != '') {
            $this->SetY(-20);
            $this->setCellHeightRatio(0.7);
            $footer_text = '<p style="'.$font_style.'">'.FORM_FOOTER_TEXT.'</p>';
            $this->writeHTMLCell(0, 0, '' , '', $footer_text, 0, 0, false, true, $footer_align, true);
        }

        if(FORM_FOOTER_LOGO != '') {
            $image_file = '../Form Builder/download/'.FORM_FOOTER_LOGO;
            $this->Image($image_file, 0, 255, 0, 15, '', '', 'T', false, 300, $page_align, false, false, 0, false, false, false);
        }
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
$pdf->SetMargins(7.5, (FORM_HEADER_LOGO != '' ? 35 : (!empty($header_text) ? 20 : 10)), 7.5);
$pdf->SetFooterMargin(FORM_FOOTER_LOGO != '' ? 25 : 25);
if($advanced_styling != 1 && $page_by_page == 1){
    $pdf->SetAutoPageBreak(FALSE, 0);
} else {
    $pdf->SetAutoPageBreak(TRUE, (FORM_FOOTER_LOGO != '' ? 25 : 25));
}
$pdf->AddPage();
$pdf->SetFont('helvetica', '', !empty($body_size) ? $body_size : 8);
if($advanced_styling != 1) {
    $pdf->setCellHeightRatio(1);
} else {
    $pdf->setCellHeightRatio(1);
}

$body_heading_css = '';
$body_heading_styling = explode(',',$body_heading_styling);
if(in_array('Bold', $body_heading_styling)) {
    $body_heading_css .= 'font-weight: bold;';
}
if(in_array('Italic', $body_heading_styling)) {
    $body_heading_css .= 'font-style: italic;';
}
if(in_array('Underline', $body_heading_styling)) {
    $body_heading_css .= 'text-decoration: underline;';
}
$section_heading_css = '';
$section_heading_styling = explode(',',$section_heading_styling);
if(in_array('Bold', $section_heading_styling)) {
    $section_heading_css .= 'font-weight: bold;';
}
if(in_array('Italic', $section_heading_styling)) {
    $section_heading_css .= 'font-style: italic;';
}
if(in_array('Underline', $section_heading_styling)) {
    $section_heading_css .= 'text-decoration: underline;';
}
$body_css = '';
$body_styling = explode(',',$body_styling);
if(in_array('Bold', $body_styling)) {
    $body_css .= 'font-weight: bold;';
}
if(in_array('Italic', $body_styling)) {
    $body_css .= 'font-style: italic;';
}
if(in_array('Underline', $body_styling)) {
    $body_css .= 'text-decoration: underline;';
}
if($advanced_styling != 1) {
    $html_css = "
        <style>
            h1,h2,h3,h4,h5 {
                font-family: $body_heading_font;
                color: $body_heading_color;
                $body_heading_css
            }
            body,p {
                font-family: $body_font;
                font-size: $body_size;
                color: $body_color;
                $body_styling
            }
            tr {
                font-family: $body_font;
                font-size: $body_size;
                color: $body_color;
                $body_styling
            }
            .body-heading {
                font-family: $body_heading_font;
                font-size: $body_heading_size;
                color: $body_heading_color;
                $body_heading_css
            }
            .section-heading {
                font-family: $section_heading_font;
                font-size: $section_heading_size;
                color: $section_heading_color;
                $section_heading_css
            }
            p {
                line-height: 120%;
            }
        </style>
    ";
    $pdf_text = $html_css;
} else {
    $html_css = "
        <style>
            body {
                font-family: $body_font;
            }
            tr {
                font-family: $body_font;
            }
        </style>
    ";
    $pdf_text = $html_css.html_entity_decode($form['contents']);
}
if(isset($_GET['performance_review']) && $advanced_styling != 1 && $page_by_page != 1) {
    $pr_html = '<h1 style="text-align: center;">'.get_contact($dbc, $_POST['pr_staff']).(!empty($_POST['pr_position']) ? ' - '.$_POST['pr_position'] : '').'</h1>';
    $pdf_text = $pr_html.$pdf_text;
}
if(isset($infogatheringid)) {
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_infogathering WHERE form='".$form['form_id']."'"));
    $form_config = explode(',',$get_field_config['fields']);

    $info_html = '';
    if(in_array_any(['Business','Project','Created By','Today Date'], $form_config)) {
        $info_html = '<table style="padding:3px; border:1px solid black;">';
        $info_html .= '<tr style="background-color: black; color: white;">';
        if(in_array('Today Date', $form_config)) {
            $info_html .= '<td>Date</td>';
        }
        if(in_array('Business', $form_config)) {
            $info_html .= '<td>Business</td>';
        }
        if(in_array('Project', $form_config)) {
            $info_html .= '<td>Project</td>';
        }
        if(in_array('Created By', $form_config)) {
            $info_html .= '<td>Created By</td>';
        }
        $info_html .= '</tr>';

        $info_html .= '<tr>';
        if(in_array('Today Date', $form_config)) {
            $info_html .= '<td>'.date('Y-m-d').'</td>';
        }
        if(in_array('Business', $form_config)) {
            $info_html .= '<td>'.get_client($dbc, $_POST['businessid']).'</td>';
        }
        if(in_array('Project', $form_config)) {
            $project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$_POST['projectid']."'"));
            $info_html .= '<td>'.get_project_label($dbc, $project).'</td>';
        }
        if(in_array('Created By', $form_config)) {
            $info_html .= '<td>'.get_contact($dbc, $_SESSION['contactid']).'</td>';
        }
        $info_html .= '</tr></table>';
    }
    $pdf_text = $info_html.$pdf_text;
}
$ticket_description = '';
$fields = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id`='$form_id' AND `type` NOT IN ('OPTION') AND `deleted`=0 ORDER BY `sort_order`");
$page_details = [];
while($field = mysqli_fetch_array($fields)) {
    $field_id = $field['field_id'];
    
    switch($field['type']) {
        case 'SLIDER':
            if($printable_pdf == 'true' && $advanced_styling != 1 && $page_by_page != 1) {
                $pdf_value = '';
                $slider_arr = explode(',', $field['content']);
                $slider_min = $slider_arr[0];
                $slider_max = $slider_arr[1];
                $pdf_value .= 'Min: '.$slider_arr[0].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                $pdf_value .= 'Max: '.$slider_arr[1].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                $pdf_value .= 'Value: __________';
                $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))];
                // $value = ltrim($value, '<p>');
                // $value = rtrim($value, '</p>');
                $value = str_replace('<p>','', $value);
                $value = str_replace('</p>','<br>',$value);
                if($preview_form == 'true') {
                    $value = 'PREVIEW';
                }
                if($advanced_styling != 1 && $page_by_page == 1) {
                    $page_details[$field['name']] = generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
                } else if($advanced_styling != 1) {
                    $pdf_text .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
                } else {
                    $pdf_text = str_replace('[['.$field['name'].']]', $value, $pdf_text);
                }
                $ticket_description .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            }
            break;
        case 'SELECT_CUS':
            if($printable_pdf == 'true' && $advanced_styling != 1 && $page_by_page != 1) {
                $pdf_value = '';
                $options = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `name`='".$field['name']."' AND `form_id`='$form_id' AND `type`='OPTION' AND `deleted`=0 ORDER BY `sort_order`");
                while ($option = mysqli_fetch_array($options)) {
                    $pdf_value .= '<img style="height: 8px; width: 8px;" src="../img/checkbox_unchecked.png"> '.$option['label'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
            } else{
                $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))];
                $values = [];
                if($field['content'] == 'multiple') {
                    foreach($value as $single_value) {
                        $values[] = $single_value;
                    }
                } else {
                    $values = [$value];
                }
                $pdf_value = implode(', ', $values);
                $value = implode('#*#',$values);
                if($preview_form == 'true') {
                    $value = 'PREVIEW';
                    $pdf_value = 'PREVIEW';
                }
                if($advanced_styling != 1 && $page_by_page == 1) {
                    $page_details[$field['name']] = generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else if($advanced_styling != 1) {
                    $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else {
                    $pdf_text = str_replace('[['.$field['name'].']]', $pdf_value, $pdf_text);
                }
                $ticket_description .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            }
            break;
        case 'SELECT':
            if($printable_pdf == 'true' && $advanced_styling != 1 && $page_by_page != 1) {
                $pdf_value = '';
                $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `category`='".$field['source_conditions']."' AND `deleted`=0 AND `status`>0 AND `show_hide_user`=1"),MYSQLI_ASSOC));
                foreach($contact_list as $contact_id) {
                    $contact = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name`, `nick_name` FROM `contacts` WHERE `contactid`='$contact_id'"));
                    $name = ($contact['name'] != '' ? decryptIt($contact['name']) : '');
                    if($contact['first_name'].$contact['last_name'].$contact['nick_name'] != '') {
                        $name .= ($name != '' ? ': ' : '').decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']);
                    }
                    $name .= ($contact['nick_name'] != '' ? '"'.$contact['nick_name'].'"' : '');
                    if(!empty($name)) {
                        $pdf_value .= '<img style="height: 8px; width: 8px;" src="../img/checkbox_unchecked.png"> '.$name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                }
                $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))];
                $values = [];
                if($field['content'] == 'multiple') {
                    foreach($value as $single_value) {
                        $values[] = $single_value;
                    }
                } else {
                    $values = [$value];
                }
                $pdf_values = [];
                foreach($values as $select_contactid) {
                    if($select_contactid > 0) {
                        $table = $field['source_table'];
                        $value_src = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `$table` WHERE `contactid`='$select_contactid'"));
                        $select_contactid = ($value_src['name'] != '' ? decryptIt($value_src['name']) : '');
                        if($value_src['first_name'].$value_src['last_name'].$value_src['nick_name'] != '') {
                            $select_contactid .= ($select_contactid != '' ? ': ' : '').decryptIt($value_src['first_name']).' '.decryptIt($value_src['last_name']);
                        }
                        $select_contactid .= ($value_src['nick_name'] != '' ? '"'.$value_src['nick_name'].'"' : '');
                        $pdf_values[] = $select_contactid;
                    }
                }
                $value = implode('#*#', $values);
                $pdf_value = implode(', ', $pdf_values);
                if($preview_form == 'true') {
                    $value = 'PREVIEW';
                    $pdf_value = 'PREVIEW';
                }
                if($advanced_styling != 1 && $page_by_page == 1) {
                    $page_details[$field['name']] = generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else if($advanced_styling != 1) {
                    $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else {
                    $pdf_text = str_replace('[['.$field['name'].']]', $pdf_value, $pdf_text);
                }
                $ticket_description .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            }
            break;
        case 'REFERENCE':
            $field_ref = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower(mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` FROM `user_form_fields` WHERE `field_id`='".$field['references']."' AND `deleted`=0 ORDER BY `sort_order`"))['name']))];
            switch($field['source_conditions']) {
                case 'contact_name':
                    $value = get_contact($dbc, $field_ref);
                    break;
                case 'full_address':
                    $value = get_address($dbc, $field_ref);
                    break;
                case 'street':
                    $value = get_contact($dbc, $field_ref, 'business_address');
                    break;
                default:
                    $value = get_contact($dbc, $field_ref, $field['source_conditions']);
                    break;
            }
            if($preview_form == 'true') {
                $value = 'PREVIEW';
            }
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling == 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $value, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            break;
        case 'RADIO':
            $option_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `type`='OPTION' AND `form_id`='$form_id' AND `name`='".$field['name']."' AND `deleted`=0 ORDER BY `sort_order`");
            $field_styling = $field['styling'];
            $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))];
            $pdf_value = '';
            while($option = mysqli_fetch_array($option_list)) {
                // $pdf_value .= '<input type="radio" name="'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'" value="'.$option['label'].'" '.($value == $option['label'] ? 'checked="checked"' : '').' /> '.$option['label'].' ';
                if ($value == $option['label']) {
                    if(strpos(','.$field_styling.',',',x,') !== FALSE) {
                        $img_src = 'radio_checked_x.png';
                    } else {
                        $img_src = 'radio_checked.png';
                    }
                    $checked = 1;
                } else {
                    $img_src = 'radio_unchecked.png';
                    $checked = 0;
                }

                if(strpos(','.$field_styling.',',',chk_med,') !== FALSE) {
                    $checkbox_style = 'height: 11px; width: 11px;';
                } else if(strpos(','.$field_styling.',',',chk_lrg,') !== FALSE) {
                    $checkbox_style = 'height: 14px; width: 14px;';
                } else {
                    $checkbox_style = 'height: 8px; width: 8px;';
                }

                if($preview_form != 'true') {
                    $field_id_option = $option['field_id'];
                    $field_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id_option'"))['num_rows'];
                    if ($field_exists > 0) {
                        mysqli_query($dbc, "UPDATE `user_form_data` SET `value` = '".filter_var(htmlentities($value), FILTER_SANITIZE_STRING)."', `checked` = '$checked' WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id_option'");
                    } else {
                        mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`, `checked`) VALUES ('$pdf_id', '$field_id_option', '".filter_var(htmlentities($value),FILTER_SANITIZE_STRING)."', '$checked')");
                    }
                }
                $pdf_value .= '<img style="'.$checkbox_style.'" src="../img/'.$img_src.'"> '.$option['label'].' '.(strpos(','.$field_styling.',', ',newline,') !== FALSE ? '<br>' : '');
            }
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling != 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $pdf_value, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            break;
        case 'CHECKBOX':
            $option_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `type`='OPTION' AND `form_id`='$form_id' AND `name`='".$field['name']."' AND `deleted`=0 ORDER BY `sort_order`");
            $field_styling = $field['styling'];
            $value = implode(',', $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))]);
            $pdf_value = '';
            $i = 0;
            while($option = mysqli_fetch_array($option_list)) {
                // $pdf_value .= '<input type="checkbox" name="'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).preg_replace('/[^a-z0-9_]/','',strtolower($option['label'])).'" value="'.$option['label'].'" '.(in_array($option['label'],$_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))]) ? 'checked="checked"' : '').'> '.$option['label'].' ';
                if (in_array($option['label'],$_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))])) {
                    if(strpos(','.$field_styling.',',',x,') !== FALSE) {
                        $img_src = 'checkbox_checked_x.png';
                    } else {
                        $img_src = 'checkbox_checked.png';
                    }
                    $checked = 1;
                } else {
                    $img_src = 'checkbox_unchecked.png';
                    $checked = 0;
                }

                if(strpos(','.$field_styling.',',',chk_med,') !== FALSE) {
                    $checkbox_style = 'height: 11px; width: 11px;';
                } else if(strpos(','.$field_styling.',',',chk_lrg,') !== FALSE) {
                    $checkbox_style = 'height: 14px; width: 14px;';
                } else {
                    $checkbox_style = 'height: 8px; width: 8px;';
                }

                $check_input = '';
                $check_input_pdf = '';
                if($option['source_conditions'] == 'input') {
                    $check_input = filter_var($_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_input'][$option['field_id']],FILTER_SANITIZE_STRING);
                    $check_input_pdf = '<u>'.$check_input;
                    for($i = strlen($check_input); $i < 50; $i++) {
                        $check_input_pdf .= '&nbsp;';
                    }
                    $check_input_pdf .= '</u>&nbsp;&nbsp;';
                    $value .= '*#*'.$check_input;
                }
                if($preview_form != 'true') {
                    $field_id_option = $option['field_id'];
                    $field_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id_option'"))['num_rows'];
                    if ($field_exists > 0) {
                        mysqli_query($dbc, "UPDATE `user_form_data` SET `value` = '".filter_var(htmlentities($value), FILTER_SANITIZE_STRING)."', `checked` = '$checked' WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id_option'");
                    } else {
                        mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`, `checked`) VALUES ('$pdf_id', '$field_id_option', '".filter_var(htmlentities($value),FILTER_SANITIZE_STRING)."', '$checked')");
                    }
                }
                $pdf_value .= '<img style="'.$checkbox_style.'" src="../img/'.$img_src.'"> '.$option['label'].' '.$check_input_pdf.(strpos(','.$field_styling.',', ',newline,') !== FALSE ? '<br>' : '');
                if($advanced_styling != 1 && $page_by_page == 1) {
                    $page_details[$field['name'].'['.$i.']'] = '<img style="'.$checkbox_style.'" src="../img/'.$img_src.'"> '.$option['label'].' '.$check_input_pdf;
                    $page_details[$field['name'].'['.$i.',chk]'] = '<img style="'.$checkbox_style.'" src="../img/'.$img_src.'">';
                }
                $i++;
            }
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling != 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $pdf_value, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            break;
        case 'CHECKINFO':
            $checked = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_checked'] == 1 ? 1 : 0;
            $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))];
            if($preview_form == 'true') {
                $value = 'PREVIEW';
            }
            $value_pdf = ($_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_checked'] == 1 ? 'Checked' : 'Not Checked').($_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))] != '' ? ': ' : '').$_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))];
            if($preview_form != 'true') {
                $field_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id'"))['num_rows'];
                if ($field_exists > 0) {
                    mysqli_query($dbc, "UPDATE `user_form_data` SET `checked` = '$checked' WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id'");
                } else {
                    mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `checked`) VALUES ('$pdf_id', '$field_id', '$checked')");
                }
            }
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $value_pdf, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling != 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $value_pdf, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $value_pdf, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $value_pdf, $field['pdf_align'], $field['pdf_label'], 'ticket');
            break;
        case 'SIGNONLY':
            $pdf_value = '';
            $value = '';
            if ($_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_SIGN'] != '' || $preview_form == 'true') {
                $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_SIGN'];
                if(!file_exists('signatures')) {
                    mkdir('signatures', 0777, true);
                }
                imagepng(sigJsonToImage($value), 'signatures/sign_'.$field_id.'_'.$assign_id.'.png');
                $pdf_value = '<table border="0"><tr><td><img src="signatures/sign_'.$field_id.'_'.$assign_id.'.png" height="30" border="0" alt=""></td></tr><tr><td>'.$pdf_value.'</td></tr></table>';
                $value = 'signatures/sign_'.$field_id.'_'.$assign_id.'.png';
                if($advanced_styling != 1 && $page_by_page == 1) {
                    $page_detail = mysqli_fetch_array(mysqli_query($dbc, "SELECT ufpd.* FROM `user_form_page_detail` ufpd LEFT JOIN `user_form_page` ufp ON ufpd.`page_id` = ufp.`page_id` WHERE ufp.`form_id` = '$form_id' AND ufp.`deleted` = 0 AND ufpd.`deleted` = 0 AND ufpd.`field_name` = '".$field['name']."'"));
                    if(!empty($page_detail)) {
                        $pdf_value = '<table border="0"><tr><td><img src="signatures/sign_'.$field_id.'_'.$assign_id.'.png" height="'.$page_detail['height'].'" width="'.$page_detail['width'].'" border="0" alt=""></td></tr><tr><td></td></tr></table>';
                    }
                    $page_details[$field['name']] = generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else if($advanced_styling != 1) {
                    $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else {
                    $pdf_text = str_replace('[['.$field['name'].']]', $pdf_value, $pdf_text);
                }
                $ticket_description .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            } else {
                $sign_data = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id' ORDER BY `data_id` DESC"), MYSQLI_ASSOC);
                $pdf_value = '<table border="0"><tr><td><img src="'.$sign_data[0]['value'].'" height="30" border="0" alt=""></td></tr><tr><td>';
                $pdf_value .= '</td></tr></table>';
                if($advanced_styling != 1 && $page_by_page == 1) {
                    $page_detail = mysqli_fetch_array(mysqli_query($dbc, "SELECT ufpd.* FROM `user_form_page_detail` ufpd LEFT JOIN `user_form_page` ufp ON ufpd.`page_id` = ufp.`page_id` WHERE ufp.`form_id` = '$form_id' AND ufp.`deleted` = 0 AND ufpd.`deleted` = 0 AND ufpd.`field_name` = '".$field['name']."'"));
                    if(!empty($page_detail)) {
                        $pdf_value = '<table border="0"><tr><td><img src="'.$sign_data[0]['value'].'" height="'.$page_detail['height'].'" width="'.$page_detail['width'].'" border="0" alt=""></td></tr><tr><td></td></tr></table>';
                    }
                    $page_details[$field['name']] = generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else if($advanced_styling != 1) {
                    $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else {
                    $pdf_text = str_replace('[['.$field['name'].']]', $pdf_value, $pdf_text);
                }
                $ticket_description .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            }
            break;
        case 'SIGN':
            $pdf_value = '';
            $value = '';
            if ($_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_SIGN'] != '' || $preview_form == 'true') {
                $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_DATE'];
                $pdf_value .= '<br />Date: '.$value;
                if($preview_form != 'true') {
                    mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`) VALUES ('$pdf_id', '".$field_id."_DATE', '".filter_var($value,FILTER_SANITIZE_STRING)."')");
                }
                $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_NAME'];
                $pdf_value .= '<br />Name: '.$value;
                if($preview_form != 'true') {
                    mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`) VALUES ('$pdf_id', '".$field_id."_NAME', '".filter_var($value,FILTER_SANITIZE_STRING)."')");
                }
                $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_SIGN'];
                if(!file_exists('signatures')) {
                    mkdir('signatures', 0777, true);
                }
                imagepng(sigJsonToImage($value), 'signatures/sign_'.$field_id.'_'.$assign_id.'.png');
                $pdf_value = '<table border="0"><tr><td><img src="signatures/sign_'.$field_id.'_'.$assign_id.'.png" height="30" border="0" alt="" style="border-bottom:0.5px solid black;"></td></tr><tr><td>'.$pdf_value.'</td></tr></table>';
                $value = 'signatures/sign_'.$field_id.'_'.$assign_id.'.png';
                if($advanced_styling != 1 && $page_by_page == 1) {
                    $page_details[$field['name']] = generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else if($advanced_styling != 1) {
                    $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else {
                    $pdf_text = str_replace('[['.$field['name'].']]', $pdf_value, $pdf_text);
                }
                $ticket_description .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            } else {
                $sign_data = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id' ORDER BY `data_id` DESC"), MYSQLI_ASSOC);
                $pdf_value = '<table border="0"><tr><td><img src="'.$sign_data[0]['value'].'" height="30" border="0" alt="" style="border-bottom:0.5px solid black;"></td></tr><tr><td>';
                $pdf_value .= 'Name: '.$sign_data[2]['value'];
                $pdf_value .= '<br />Date: '.$sign_data[1]['value'];
                $pdf_value .= '</td></tr></table>';
                if($advanced_styling != 1 && $page_by_page == 1) {
                    $page_details[$field['name']] = generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else if($advanced_styling != 1) {
                    $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
                } else {
                    $pdf_text = str_replace('[['.$field['name'].']]', $pdf_value, $pdf_text);
                }
                $ticket_description .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            }
            break;
        case 'MULTISIGN':
            $pdf_value = '';
            $sig_values = [];
            $sig_values_names = [];
            $sig_values_dates = [];
            $sig_names = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_NAME'];
            $sig_dates = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_DATE'];
            $signatures = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_SIGN'];
            if(!file_exists('signatures')) {
                mkdir('signatures', 0777, true);
            }
            $sign_data = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id' ORDER BY `data_id` DESC"), MYSQLI_ASSOC);
            $sign_imgs = explode('*#*', $sign_data[0]['value']);
            $sign_names = explode('*#*', $sign_data[1]['value']);
            $sign_dates = explode('*#*', $sign_data[2]['value']);
            $counter = 0;
            for ($i = 0; $i < count($sign_imgs) && $preview_form != 'true'; $i++) {
                if (!empty($sign_imgs[$i])) {
                    $sig_values[] = $sign_imgs[$i];
                    $sig_values_names[] = $sign_names[$i];
                    $sig_values_dates[] = $sign_dates[$i];
                    $pdf_value .= '<table border="0"><tr><td><img src="'.$sign_imgs[$i].'" height="30" border="0" alt="" style="border-bottom:0.5px solid black;"></td></tr><tr><td><br />Name: '.$sign_names[$i].'<br />Date: '.$sign_dates[$i].'</td></tr></table>';
                    $counter = $i;
                }
            }
            for ($i = 0; $i < count($signatures); $i++) {
                if (!empty($signatures[$i])) {
                    imagepng(sigJsonToImage($signatures[$i]), 'signatures/sign_'.$field_id.'_'.$assign_id.'_'.$i.'.png');
                    $sig_values_names[] = $sig_names[$i];
                    $sig_values_dates[] = $sig_dates[$i];
                    $sig_values[] = 'signatures/sign_'.$field_id.'_'.$assign_id.'_'.$i.'.png';
                    $pdf_value .= '<table border="0"><tr><td><img src="signatures/sign_'.$field_id.'_'.$assign_id.'_'.$i.'.png" height="30" border="0" alt="" style="border-bottom:0.5px solid black;"></td></tr><tr><td><br />Name: '.$sig_names[$i].'<br />Date: '.$sig_dates[$i].'</td></tr></table>';
                }
            }
            if($preview_form != 'true') {
                mysqli_query($dbc, "DELETE FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id'");
                $sig_dates = implode('*#*', $sig_values_dates);
                mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`) VALUES ('$pdf_id', '".$field_id."_DATE', '".filter_var($sig_dates,FILTER_SANITIZE_STRING)."')");
                $sig_names = implode('*#*', $sig_values_names);
                mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`) VALUES ('$pdf_id', '".$field_id."_NAME', '".filter_var($sig_names,FILTER_SANITIZE_STRING)."')");
            }
            $value = implode('*#*', $sig_values);
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling != 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $pdf_value, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $pdf_value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            break;
        case 'TABLE':
            $option_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `type`='OPTION' AND `form_id`='$form_id' AND `name`='".$field['name']."' AND `deleted`=0 ORDER BY `sort_order`");
            $totals = 0;
            $value = '<table border="1" cellspacing="0" cellpadding="2"><tr>';
            $table_list = [];
            $total_list = [];
            while($option = mysqli_fetch_array($option_list)) {
                $totals += $option['totaled'];
                $value .= '<th>'.$option['label'].'</th>';
                if($preview_form == 'true') {
                    $table_list[0][$option['field_id']] = 'PREVIEW';
                } else if($printable_pdf == 'true') {
                    $table_list[0][$option['field_id']] = '';
                    $table_list[1][$option['field_id']] = '';
                    $table_list[2][$option['field_id']] = '';
                    $table_list[3][$option['field_id']] = '';
                    $table_list[4][$option['field_id']] = '';
                }
                foreach($_POST['option_'.preg_replace('/[^a-z0-9_]/','',strtolower($option['label']))] as $row => $line_value) {
                    $table_list[$row][$option['field_id']] = ($option['totaled'] == 1 ? number_format($line_value,2) : $line_value);
                    $total_list[$option['field_id']] += ($option['totaled'] == 1 ? $line_value : 0);
                }
                $value_option = implode('*#*', $_POST['option_'.preg_replace('/[^a-z0-9_]/','',strtolower($option['label']))]);
                $value_option = trim($value_option, '*#*');
                $field_id_option = $option['field_id'];
                if($preview_form != 'true') {
                    $field_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id_option'"))['num_rows'];
                    if ($field_exists > 0) {
                        mysqli_query($dbc, "UPDATE `user_form_data` SET `value` = '$value_option' WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id_option'");
                    } else if ($value_option != '') {
                        mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`) VALUES ('$pdf_id', '$field_id_option', '$value_option')");
                    }
                }
            }
            $value .= '</tr>';
            foreach($table_list as $tr) {
                $value .= '<tr>';
                foreach($tr as $td) {
                    $value .= '<td>'.$td.'</td>';
                }
                $value .= '</tr>';
            }
            if($totals > 0) {
                $value .= '<tr>';
                foreach($total_list as $total) {
                    $value .= '<td>'.($total > 0 ? number_format($total,2) : '').'</td>';
                }
                $value .= '</tr>';
            }
            $value .= '</table>';
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling != 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $value, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            break;
        case 'TABLEADV':
            $option_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `type`='OPTION' AND `name`='".$field['name']."' AND `form_id`='$form_id' AND `deleted`=0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
            $table_styling = html_entity_decode($field['styling']);
            if(strpos($table_styling, '[[checkbox="x"]]') !== FALSE) {
                $checkbox_checked = 'checkbox_checked_x.png';
                $checkbox_unchecked = 'checkbox_unchecked.png';
                $checkbox_style = 'height: 8px; width: 8px;';
            } else if(strpos($table_styling, '[[checkbox="large_chk"]]') !== FALSE) {
                $checkbox_checked = 'checkbox_checked_lrg.png';
                $checkbox_unchecked = 'checkbox_unchecked_lrg.png';
                $checkbox_style = 'height: 15px; width: 15px;';
            } else {
                $checkbox_checked = 'checkbox_checked.png';
                $checkbox_unchecked = 'checkbox_unchecked.png';
                $checkbox_style = 'height: 8px; width: 8px;';
            }
            $table_styling = str_replace('[[checkbox="x"]]', '', $table_styling);
            $table_styling = str_replace('[[checkbox="large_chk"]]', '', $table_styling);
            $value = '<table cellspacing="0" cellpadding="2" '.$table_styling.'>';
            for ($i = 0; $i < count($option_list); $i++) {
                $table_row = explode('*#*', $option_list[$i]['label']);
                $field_id_option = $option_list[$i]['field_id'];
                $input_i = 0;
                $value .= '<tr style="page-break-inside: avoid;">';
                foreach ($table_row as $j => $single_cell) {
                    if (strpos($single_cell, '[[disable]]') === FALSE) {
                        $is_checkbox = false;
                        if(strpos($single_cell, '[[checkbox]]') !== FALSE) {
                            $is_checkbox = true;
                            $checkbox_value = $_POST['option_row_'.$field_id_option][$input_i];
                            $old_checkbox_checked = $checkbox_checked;
                            $old_checkbox_unchecked = $checkbox_unchecked;
                            $old_checkbox_style = $checkbox_style;
                            if(strpos($single_cell, '[[checkbox=&quot;x&quot;]]') !== FALSE) {
                                $checkbox_checked = 'checkbox_checked_x.png';
                                $checkbox_unchecked = 'checkbox_unchecked.png';
                                $checkbox_style = 'height: 8px; width: 8px;';
                            } else if(strpos($single_cell, '[[checkbox=&quot;large_chk&quot;]]') !== FALSE) {
                                $checkbox_checked = 'checkbox_checked_lrg.png';
                                $checkbox_unchecked = 'checkbox_unchecked_lrg.png';
                                $checkbox_style = 'height: 15px; width: 15px;';
                            }
                            $single_cell = str_replace('[[checkbox=&quot;x&quot;]]', '', $single_cell);
                            $single_cell = str_replace('[[checkbox=&quot;large_chk&quot;]]', '', $single_cell);
                            if($checkbox_value == 1) {
                                $img_src = $checkbox_checked;
                            } else {
                                $img_src = $checkbox_unchecked;
                            }
                            $single_cell = str_replace('[[checkbox]]', '<img style="'.$checkbox_style.'" src="../img/'.$img_src.'">', $single_cell);
                            $input_i++;
                            $checkbox_checked = $old_checkbox_checked;
                            $checkbox_unchecked = $old_checkbox_unchecked;
                            $checkbox_style = $old_checkbox_style;
                        }
                        if(strpos($single_cell, '[[bullet]]') !== FALSE) {
                            $add_bullet = '&bull; ';
                            $single_cell = str_replace('[[bullet]]', '', $single_cell);
                        } else {
                            $add_bullet = '';
                        }
                        $cell_values = explode('[[', $single_cell);
                        if (!empty($cell_values[1])) {
                            $value .= '<td'.(!empty($cell_values[1]) ? ' '.rtrim(html_entity_decode($cell_values[1]), ']]') : '').'>';
                        } else {
                            $value .= '<td style="border: 0.5px solid black;">';
                        }
                        if($preview_form == 'true') {
                            $page_details[$field['name'].'['.$i.','.$j.']'] = 'PREVIEW';
                        } else if (empty($cell_values[0]) || $cell_values[0][0] == '=' && !$is_checkbox) {
                            $value .= $add_bullet.$_POST['option_row_'.$field_id_option][$input_i];
                            $page_details[$field['name'].'['.$i.','.$j.']'] = $add_bullet.$_POST['option_row_'.$field_id_option][$input_i];
                            $input_i++;
                        } else {
                            $value .= html_entity_decode($cell_values[0]);
                            $page_details[$field['name'].'['.$i.','.$j.']'] = html_entity_decode($cell_values[0]);
                        }
                        $value .= '</td>';
                    }
                }
                $value .= '</tr>';
                $insert_value = filter_var(implode('*#*', $_POST['option_row_'.$field_id_option]),FILTER_SANITIZE_STRING);
                if($preview_form != 'true') {
                    $field_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id_option'"))['num_rows'];
                    if ($field_exists > 0) {
                        mysqli_query($dbc, "UPDATE `user_form_data` SET `value` = '$insert_value' WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id_option'");
                    } else {
                        mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`) VALUES ('$pdf_id', '$field_id_option', '$insert_value')");
                    }
                }
            }
            $value .= '</table>';
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling != 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $value, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            break;
        case 'TEXTBLOCK':
            $value = implode('*#*', $_POST[$field['name']]);
            $content = html_entity_decode($field['content']);
            // $content = ltrim($content, '<p>');
            // $content = rtrim($content, '</p>');
            $content = str_replace('<p>','', $content);
            $content = str_replace('</p>','<br>',$content);
            // $content = rtrim($content, '<br>');
            $text_content = explode('[[input]]', $content);
            $input_i = 0;
            $new_text = '';
            $textblock_format = $field['styling'];
            for($i = 0; $i < count($text_content); $i++) {
                if ($i == count($text_content) - 1) {
                    $new_text_value = $text_content[$i];
                } else {
                    if(!empty($_POST[$field['name']][$input_i])) {
                        $page_details[$field['name'].'['.$input_i.']'] = $_POST[$field['name']][$input_i];
                    } else if($preview_form == 'true') {
                        $page_details[$field['name'].'['.$input_i.']'] = 'PREVIEW';
                    }
                    if($textblock_format == 'nounderline') {
                        if($preview_form == 'true') {
                            $new_text_value = $text_content[$i].'PREVIEW';
                        } else if(empty($_POST[$field['name']][$input_i])) {
                            $new_text_value = $text_content[$i].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        } else {
                            $new_text_value = $text_content[$i].$_POST[$field['name']][$input_i];
                        }
                    } else {
                        if($preview_form == 'true') {
                            $new_text_value = $text_content[$i].'<u>PREVIEW</u>';
                        } else if(empty($_POST[$field['name']][$input_i])) {
                            $new_text_value = $text_content[$i].'<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';
                        } else {
                            $new_text_value = $text_content[$i].'<u>'.$_POST[$field['name']][$input_i].'</u>';
                        }
                    }
                    $input_i++;
                }
                $new_text .= $new_text_value;
            }
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $new_text, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling != 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $new_text, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $new_text, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $new_text, $field['pdf_align'], $field['pdf_label'], 'ticket');
            break;
        case 'DATE':
            $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))];
            if($preview_form == 'true') {
                if($field['styling'] == '/') {
                    $value = 'YYYY/MM/DD';
                } else {
                    $value = 'YYYY-MM-DD';
                }
            } else if(!empty($value)) {
                if($field['styling'] == '/') {
                    $value = date('Y/m/d', strtotime($value));
                } else {
                    $value = date('Y-m-d', strtotime($value));
                }
            }
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling != 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $value, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            break;
        case 'ACCORDION':
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = '<p class="section-heading" style="text-align: '.(empty($field['pdf_align']) ? 'left' : $field['pdf_align']).';">'.$field['label'].'</p>';
            } else if($advanced_styling != 1) {
                $pdf_text .= '<p class="section-heading" style="text-align: '.(empty($field['pdf_align']) ? 'left' : $field['pdf_align']).';">'.$field['label'].'</p>';
            } else {
                $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))];
                if($preview_form == 'true') {
                    $value = 'PREVIEW';
                }
                $pdf_text = str_replace('[['.$field['name'].']]', $value, $pdf_text);
            }
            $ticket_description .= '<h3>'.$field['label'].'</h3>';
            break;
        case 'CONTACTINFO':
            if($advanced_styling != 1) {
                $pdf_text_value = '<p class="body-heading">'.$field['label'].'</p>';
                $pdf_text_value .= '<table border="0">';
            }
            $ticket_description .= '<p><b>'.$field['label'].'</b></p>';
            $contact_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$form_id' AND `name` = '".$field['name']."' AND `type` = 'OPTION' AND `deleted` = 0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
            $tr_i = 0;
            foreach($contact_fields as $contact_field) {
                $value = $_POST[$field['name']][$contact_field['field_id']];
                if($preview_form == 'true') {
                    $value = 'PREVIEW';
                }
                if($advanced_styling != 1 && $page_by_page == 1) {
                    if(FORM_HIDE_LABELS == 1) {
                        $page_details[$field['name'].'['.$contact_field['source_conditions'].']'] = $value;
                    } else {
                        $page_details[$field['name'].'['.$contact_field['source_conditions'].']'] = $contact_field['label'].': '.$value;
                    }
                }
                if($advanced_styling != 1) {
                    if($tr_i == 0) {
                        $pdf_text_value .= '<tr>';
                    }
                    $pdf_text_value .= '<td width="50%">'.$contact_field['label'].': '.$value.'</td>';
                    $tr_i++;
                    if($tr_i == 2) {
                        $pdf_text_value .= '</tr>';
                        $tr_i = 0;
                    }
                } else {
                    $pdf_text = str_replace('[['.$field['name'].'['.$contact_field['source_conditions'].']]]', $value, $pdf_text);
                }
                $ticket_description .= '<b>'.$contact_field['label'].':</b> '.$value.'<br>';

                if($preview_form != 'true') {
                    $data_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$contact_field['field_id']."'"))['num_rows'];
                    if($data_exists > 0) {
                        mysqli_query($dbc, "UPDATE `user_form_data` SET `value` = '".filter_var(htmlentities($value), FILTER_SANITIZE_STRING)."' WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$contact_field['field_id']."'");
                    } else {
                        mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`) VALUES ('$pdf_id', '".$contact_field['field_id']."', '".filter_var(htmlentities($value),FILTER_SANITIZE_STRING)."')");
                    }
                }
            }
            if($advanced_styling != 1) {
                if($tr_i == 1) {
                    $pdf_text_value .= '<td width="50%"></td></tr>';
                }
                $pdf_text_value .= '</table>';
                if($advanced_styling != 1 && $page_by_page == 1) {
                    $page_details[$field['name']] = $pdf_text_value;
                } else {
                    $pdf_text .= $pdf_text_value;
                }
            }
            break;
        case 'FILE':
            $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_existing'];
            if($_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name'])).'_delete'] == 1) {
                $value = '';
            }
            if(!empty($_FILES['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))]['name'])) {
                $file_name = $basename = $_FILES['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))]['name'];
                $j = 0;
                while(file_exists('download/'.$file_name)) {
                    $file_name = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
                }
                $value = WEBSITE_URL.'/'.FOLDER_NAME.'/download/'.$file_name;
                move_uploaded_file($_FILES['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))]['tmp_name'], 'download/'.$file_name);
            }
            if($preview_form == 'true') {
                $value = 'PREVIEW';
            }
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling != 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $value, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label'], 'ticket');

            break;
        case 'SERVICES':
            if($advanced_styling != 1) {
                $value = '<p class="body-heading">'.$field['label'].'</p>';
            } else {
                $ticket_description .= '<p><b>'.$field['label'].'</b></p>';
            }
            $value .= '<table border="1" cellspacing="0" cellpadding="2"><tr>';
            $value .= '<th width="60%">Service</th>';
            $value .= '<th width="30%">Price</th>';
            $value .= '<th width="10%" align="center">Include</th>';
            $value .= '</tr>';
            $form_services = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id`='$form_id' AND `type`='OPTION' AND `name`='".$field['name']."' AND '".$field['type']."' IN ('SERVICES') AND `deleted`=0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
            foreach($form_services as $form_service) {
                $service = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '".$form_service['source_conditions']."'"));
                $price = number_format($_POST['field_'.$field['name']][$form_service['field_id']], 2, '.', '');
                $include = $_POST['field_'.$field['name'].'_add'][$form_service['field_id']];
                $value .= '<tr style="page-break-inside: avoid;">';
                $value .= '<td>'.$service['heading'].'</td>';
                $value .= '<td>$'.$price.'</td>';
                $value .= '<td align="center"><img src="'.($include == 1 ? '../img/checkbox_checked_x.png' : '../img/checkbox_unchecked.png').'" style="height: 8px; width: 8px;"></td>';
                $value .= '</tr>';

                if($preview_form != 'true') {
                    $data_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$form_service['field_id']."'"))['num_rows'];
                    if($data_exists > 0) {
                        mysqli_query($dbc, "UPDATE `user_form_data` SET `value` = '$price', `checked` = '$include' WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$form_service['field_id']."'");
                    } else {
                        mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`, `checked`) VALUES ('$pdf_id', '".$form_service['field_id']."', '$price', '$include')");
                    }
                }
            }
            $value .= '</table>';
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = $value;
            } else if($advanced_styling != 1) {
                $pdf_text .= $page_details[$field['name']] = $value;
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $value, $pdf_text);
            }
            $ticket_description .= $value;
            break;
        case 'TEXT':
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = '<p style="text-align: '.(empty($field['pdf_align']) ? 'left' : $field['pdf_align']).';">'.$field['label'].'</p>';
            } else if($advanced_styling != 1) {
                $pdf_text .= '<p style="text-align: '.(empty($field['pdf_align']) ? 'left' : $field['pdf_align']).';">'.$field['label'].'</p>';
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $field['label'], $pdf_text);
            }
            $ticket_description .= '<p>'.$field['label'].'</p>';
            break;
        default:
            $value = $_POST['field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']))];
            // $value = ltrim($value, '<p>');
            // $value = rtrim($value, '</p>');
            $value = str_replace('<p>','', $value);
            $value = str_replace('</p>','<br>',$value);
            if($preview_form == 'true') {
                $value = 'PREVIEW';
            }
            if($advanced_styling != 1 && $page_by_page == 1) {
                $page_details[$field['name']] = generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else if($advanced_styling != 1) {
                $pdf_text .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label']);
            } else {
                $pdf_text = str_replace('[['.$field['name'].']]', $value, $pdf_text);
            }
            $ticket_description .= generateSimpleStyling($field['label'], $value, $field['pdf_align'], $field['pdf_label'], 'ticket');
            break;
    }
    
    if($preview_form != 'true' && $field['type'] != 'CONTACTINFO') {
        $field_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id'"))['num_rows'];
        if ($field_exists > 0 && $field['type'] != 'SIGN' && $field['type'] != 'MULTISIGN') {
            mysqli_query($dbc, "UPDATE `user_form_data` SET `value` = '".filter_var(htmlentities($value), FILTER_SANITIZE_STRING)."' WHERE `pdf_id` = '$pdf_id' AND `field_id` = '$field_id'");
        } else {
            if (($field['type'] == 'SIGN' || $field['type'] == 'MULTISIGN') && $value == '') {

            } else {
                mysqli_query($dbc, "INSERT INTO `user_form_data` (`pdf_id`, `field_id`, `value`) VALUES ('$pdf_id', '$field_id', '".filter_var(htmlentities($value),FILTER_SANITIZE_STRING)."')");
            }
        }
    }
}
function generateSimpleStyling($heading, $text, $pdf_align, $pdf_label, $ticket = '') {
    if($ticket == 'ticket') {
        $pdf_text = '<p>';
        $pdf_text .= '<b>'.$heading.'</b>';
        $pdf_text .= '<br>'.$text;
        $pdf_text .= '</p>';
    } else {
        $pdf_text = '<p style="text-align: '.(empty($pdf_align) ? 'left' : $pdf_align).';">';

        if(!empty($heading) && FORM_HIDE_LABELS != 1) {
            $pdf_text .= '<span class="body-heading">'.$heading.'</span>';
        }
        if($pdf_label == 1) {
            $pdf_text .= ((!empty($heading) && FORM_HIDE_LABELS != 1) ? ': ' : '').$text;
        } else if($pdf_label == 2) {
            $pdf_text .= '<ul align="'.(empty($pdf_align) ? 'left' : $pdf_align).'"><li>'.$text.'</li></ul>';
        } else {
            $pdf_text .= ((!empty($heading) && FORM_HIDE_LABELS != 1) ? '<br>' : '').$text;
        }
        $pdf_text .= '</p>';
    }
    return $pdf_text;
}
?>