<?php include('../include.php');
ob_clean();

if(isset($_GET['action']) && $_GET['action'] == 'archive') {
	$id = $_POST['form'];
    $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `user_forms` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `form_id`='$id'");
}

if(isset($_GET['fill']) && $_GET['fill'] == 'retrieve_ref') {
    $form_id = $_GET['form_id'];
    $ref_source = $_GET['ref_source'];
    $ref_value = $_GET['ref_value'];
    $ref_name = explode('field_', $_GET['ref_name'], 2)[1];
    $field = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$form_id' AND `name` = '$ref_name' AND `type` NOT IN ('REFERENCE','OPTION') AND `deleted` = 0"));
    switch($ref_value) {
        case 'contact_name':
            $new_value = get_contact($dbc, $ref_source);
            break;
        case 'full_address':
            $new_value = get_address($dbc, $ref_source);
            break;
        case 'street':
            $new_value = get_contact($dbc, $ref_source, 'business_address');
            break;
        default:
            $contact = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` LEFT JOIN `contacts_upload` ON `contacts`.`contactid`=`contacts_upload`.`contactid` WHERE `contacts`.`contactid`='$ref_source'"));
            if(isEncrypted($ref_value)) {
                $new_value = decryptIt($contact[$ref_value]);
            } else {
                $new_value = $contact[$ref_value];
            }
            break;
    }

    //Replace 0000-00-00 date with empty string
    if($new_value == '0000-00-00') {
        $new_value = '';
    }

    //Verify if it is a date or not to see if we need to update format
    $is_date = DateTime::createFromFormat('Y-m-d',$new_value);
    if($is_date) {
        if($field['styling'] == '/') {
            $new_value = str_replace('-','/',$new_value);
        }
    }
    echo $new_value;
}

if(isset($_GET['fill']) && $_GET['fill'] == 'form_name') {
    $formid = filter_var($_POST['formid'],FILTER_SANITIZE_STRING);
    $form_name = filter_var($_POST['form_name'],FILTER_SANITIZE_STRING);
    $is_template = filter_var($_POST['is_template'],FILTER_SANITIZE_STRING);
    $num_rows = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_forms` WHERE `form_id` = '$formid' AND `form_id` > 0"))['num_rows'];

    if($num_rows > 0) {
        mysqli_query($dbc, "UPDATE `user_forms` SET `name` = '$form_name' WHERE `form_id` = '$formid'");
    } else {
        mysqli_query($dbc, "INSERT INTO `user_forms` (`name`, `is_template`) VALUES ('$form_name', '$is_template')");
        $formid = mysqli_insert_id($dbc);
        echo $formid;
    }
}

if(isset($_GET['fill']) && $_GET['fill'] == 'insert_field') {
    $formid = filter_var($_POST['formid'],FILTER_SANITIZE_STRING);
    $field_type = filter_var($_POST['field_type'],FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'],FILTER_SANITIZE_STRING);

    mysqli_query($dbc, "INSERT INTO `user_form_fields` (`form_id`, `type`) VALUES ('$formid', '$field_type')");
    $field_id = mysqli_insert_id($dbc);
    mysqli_query($dbc, "UPDATE `user_form_fields` SET `name` = 'field_".$field_id."' WHERE `field_id` = '$field_id'");

    if($field_type == 'HR') {
        $block_html = '<img src="../img/remove.png" class="inline-img" onclick="deleteField(this);" style="cursor: pointer;">&nbsp;Horizontal Row&nbsp;<img class="drag-handle" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" style="float: right; width: 2em;"">*#*'.$field_id;
    } else {
        $block_html = '<img src="../img/remove.png" class="inline-img" onclick="deleteField(this);" style="cursor: pointer;">&nbsp;<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Form Builder/edit_form_field_details.php?field_id='.$field_id.'\', \'auto\', true, true, $(\'.main-screen\').height()); return false;">'.$description.': </a>&nbsp;<img class="drag-handle" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" style="float: right; width: 2em;"">*#*'.$field_id;
    }
    echo $block_html;
}

if(isset($_GET['fill']) && $_GET['fill'] == 'update_field') {
    $formid = filter_var($_POST['formid'],FILTER_SANITIZE_STRING);
    $field_id = filter_var($_POST['field_id'],FILTER_SANITIZE_STRING);
    $field_type = filter_var($_POST['field_type'],FILTER_SANITIZE_STRING);
    $label = filter_var(htmlentities($_POST['label']),FILTER_SANITIZE_STRING);
    $sublabel = filter_var(htmlentities($_POST['sublabel']),FILTER_SANITIZE_STRING);
    $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
    $field_default = filter_var($_POST['field_default'],FILTER_SANITIZE_STRING);
    if($field_type == 'DATE') {
        $styling = filter_var($_POST['date_format'],FILTER_SANITIZE_STRING);
    } else if($field_type == 'TEXTBLOCK') {
        $styling = filter_var($_POST['textblock_format'],FILTER_SANITIZE_STRING);
    } else if($field_type == 'CHECKBOX' || $field_type == 'RADIO') {
        $styling = filter_var($_POST['checkboxradio_format'],FILTER_SANITIZE_STRING);
    } else {
        $styling = filter_var($_POST['table_styling'],FILTER_SANITIZE_STRING);
    }
    $content = filter_var(htmlentities($_POST['content']),FILTER_SANITIZE_STRING);
    if($field_type == 'SLIDER') {
        $slider_min = filter_var($_POST['slider_min'],FILTER_SANITIZE_STRING);
        $slider_max = filter_var($_POST['slider_max'],FILTER_SANITIZE_STRING);
        $slider_increment = filter_var($_POST['slider_increment'],FILTER_SANITIZE_STRING);
        $content = $slider_min.','.$slider_max.','.$slider_increment;
    } else if($field_type == 'SLIDER_TOTAL') {
        $slider_fields = json_decode($_POST['slider_fields']);
        $content = implode(',',$slider_fields);
    }
    $mandatory = filter_var($_POST['mandatory'],FILTER_SANITIZE_STRING);
    $pdf_align = $_POST['pdf_align'];
    $pdf_label = $_POST['pdf_label'];
    $pdf_checkbox = $_POST['pdf_checkbox'];
    if(!empty($pdf_checkbox)) {
        $styling .= ','.$pdf_checkbox;
    }
    $pdf_checkbox_size = $_POST['pdf_checkbox_size'];
    if(!empty($pdf_checkbox_size)) {
        $styling .= ','.$pdf_checkbox_size;
    }

    if($field_type == 'SERVICES') {
        $source_conditions = filter_var($_POST['services_hide_external'],FILTER_SANITIZE_STRING);
    } else if($field_type == 'REFERENCE' || $field_type == 'TEXTBOXREF' || $field_type == 'TEXTBOX' || $field_type == 'DATE') {
        $references = filter_var($_POST['ref_source_table'],FILTER_SANITIZE_STRING);
        $source_conditions = filter_var($_POST['ref_source_conditions'],FILTER_SANITIZE_STRING);
    } else if($field_type == 'CONTACTINFO') {
        $source_table = filter_var($_POST['contact_tile_name'],FILTER_SANITIZE_STRING);
        $source_conditions = filter_var($_POST['contact_category'],FILTER_SANITIZE_STRING);
    } else {
        $source_table = filter_var($_POST['field_source_table'],FILTER_SANITIZE_STRING);
        $source_conditions = filter_var($_POST['field_source_conditions'],FILTER_SANITIZE_STRING);
        if($field_type == 'SELECT' || $field_type == 'SELECT_CUS') {
            if($source_conditions == 'SELECT_CUS') {
                mysqli_query($dbc, "UPDATE `user_form_fields` SET `type` = 'SELECT_CUS' WHERE `field_id` = '$field_id'");
            } else {
                mysqli_query($dbc, "UPDATE `user_form_fields` SET `type` = 'SELECT' WHERE `field_id` = '$field_id'");
            }
        }
    }
    $old_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` FROM `user_form_fields` WHERE `field_id` = '$field_id'"))['name'];
    if($name != $old_name) {
        $name_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`name`) as num_rows FROM `user_form_fields` WHERE `form_id` = '$formid' AND `deleted` = 0 AND `name` = '$name' AND `type` != 'OPTION'"))['num_rows'];
    }
    if($name_exists > 0) {
        $name = $old_name;
        echo 'name_exists*#*Name already exists in this Form. Reverting back to previous name.*#*'.$old_name;
        $echoed_already = true;
    } else if(empty(trim($name))) {
        $name = $old_name;
        echo 'name_exists*#*Name cannot be blank. Reverting back to previous name.*#*'.$old_name;
        $echoed_already = true;
    }
    mysqli_query($dbc, "UPDATE `user_form_fields` SET `name` = '$name', `label` = '$label', `sublabel` = '$sublabel', `default` = '$field_default', `styling` = '$styling', `content` = '$content', `mandatory` = '$mandatory', `references` = '$references', `source_table` = '$source_table', `source_conditions` = '$source_conditions', `pdf_align` = '$pdf_align', `pdf_label` = '$pdf_label' WHERE `field_id` = '$field_id' AND `type` != 'OPTION'");

    //Update option name fields to the new name if it was changed
    mysqli_query($dbc, "UPDATE `user_form_fields` SET `name` = '$name' WHERE `form_id` = '$formid' AND `type` = 'OPTION' AND `name` = '$old_name'");

    //Update page details names to new name if it was changed
    if($name != $old_name) {
        $page_details_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT ufpd.* FROM `user_form_page_detail` ufpd LEFT JOIN `user_form_page` ufp ON ufpd.`page_id` = ufpd.`page_id` WHERE ufpd.`deleted` = 0 AND ufp.`deleted` = 0 AND ufp.`form_id` = '$formid' AND ufpd.`field_name` LIKE '$old_name%'"),MYSQLI_ASSOC);
        foreach ($page_details_sql as $page_detail) {
            $field_name = '';
            if($page_detail['field_name'] == $old_name) {
                $field_name = $name;
            } else if(explode('[',$page_details['field_name'])[0] == $old_name) {
                $field_name = str_replace($old_name.'[', $name.'[', $page_detail['field_name']);
            }
            if(!empty($field_name)) {
                mysqli_query($dbc, "UPDATE `user_form_page_detail` SET `field_name` = '$field_name'WHERE `page_detail_id` = '".$page_detail['page_detail_id']."'");
            }
        }
    }


    if($field_type == 'SERVICES') {
        //Services Fields
        $services = $_POST['services'];
        if(!is_array($services)) {
            $services = [$services];
        }
        $fields_keep = [];
        $sort_order = 0;
        foreach ($services as $service) {
            if($service > 0) {
                $field_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `type` = 'OPTION' AND `name` = '$name' AND `source_conditions` = '$service'"));
                if(!empty($field_exists)) {
                    $field_id = $field_exists['field_id'];
                    mysqli_query($dbc, "UPDATE `user_form_fields` SET `source_conditions` = '$service', `sort_order` = '$sort_order', `deleted` = 0 WHERE `field_id` = '$field_id'");
                } else {
                    mysqli_query($dbc, "INSERT INTO `user_form_fields` (`form_id`, `name`, `type`, `source_conditions`, `sort_order`) VALUES ('$formid', '$name', 'OPTION', '$service', '$sort_order')");
                    $field_id = mysqli_insert_id($dbc);
                }
                $fields_keep[] = $field_id;
                $sort_order++;
            }
        }
        $date_of_archival = date('Y-m-d');
        mysqli_query($dbc, "UPDATE `user_form_fields` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `form_id` = '$formid' AND `type` = 'OPTION' AND `name` = '$name' AND `field_id` NOT IN ($fields_keep)");
    } else if($field_type == 'TABLEADV') {
        //Table Advanced
        $option_row_fields = json_decode($_POST['option_row_fields']);
        $option_row_ids = [];
        $sort_order = 0;
        foreach ($option_row_fields as $option_row_field) {
            $option_row_field = json_decode(json_encode($option_row_field), true);
            $option_row_id = $option_row_field['option_row_id'];
            $option_row = htmlentities(implode('*#*', $option_row_field['option_row']));
            if(!empty($option_row_id)) {
                mysqli_query($dbc, "UPDATE `user_form_fields` SET `label` = '$option_row', `sort_order` = '$sort_order' WHERE `field_id` = '$option_row_id'");
            } else {
                mysqli_query($dbc, "INSERT INTO `user_form_fields` (`form_id`, `name`, `label`, `type`, `sort_order`) VALUES ('$formid', '$name', '$option_row', 'OPTION', '$sort_order')");
                $option_row_id = mysqli_insert_id($dbc);
            }
            $sort_order++;
            $option_row_ids[] = $option_row_id;
        }
        $option_row_ids_keep = "'".implode("','", $option_row_ids)."'";
         $date_of_archival = date('Y-m-d');
       mysqli_query($dbc, "UPDATE `user_form_fields` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `form_id` = '$formid' AND `type` = 'OPTION' AND `name` = '$name' AND `field_id` NOT IN ($option_row_ids_keep)");
        if(!empty($option_row_ids) && !$echoed_already) {
            echo 'tableadv_ids*#*'.implode('*#*', $option_row_ids);
        }
    } else if($field_type == 'CONTACTINFO') {
        //Contact Info Fields
        $contact_fields = json_decode($_POST['contact_fields']);
        $fields_keep = [];
        $sort_order = 0;
        foreach ($contact_fields as $contact_field) {
            $contact_field = json_decode(json_encode($contact_field), true);
            $field_name = $contact_field['field_name'];
            $field_label = $contact_field['field_label'];
            $field_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `type` = 'OPTION' AND `name` = '$name' AND `source_conditions` = '$field_name'"));
            if(!empty($field_exists)) {
                $field_id = $field_exists['field_id'];
                mysqli_query($dbc, "UPDATE `user_form_fields` SET `label` = '$field_label', `source_conditions` = '$field_name', `sort_order` = '$sort_order', `deleted` = 0 WHERE `field_id` = '$field_id'");
            } else {
                mysqli_query($dbc, "INSERT INTO `user_form_fields` (`form_id`, `name`, `label`, `type`, `source_conditions`, `sort_order`) VALUES ('$formid', '$name', '$field_label', 'OPTION', '$field_name', '$sort_order')");
                $field_id = mysqli_insert_id($dbc);
            }
            $fields_keep[] = $field_id;
            $sort_order++;
        }
        $fields_keep = "'".implode("','", $fields_keep)."'";
         $date_of_archival = date('Y-m-d');
       mysqli_query($dbc, "UPDATE `user_form_fields` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `form_id` = '$formid' AND `type` = 'OPTION' AND `name` = '$name' AND `field_id` NOT IN ($fields_keep)");
    } else {
        //Options Fields
        $option_fields = json_decode($_POST['option_fields']);
        $options_keep = [];
        $sort_order = 0;
        foreach ($option_fields as $option_field) {
            $option_field = json_decode(json_encode($option_field), true);
            $option_id = $option_field['option_id'];
            $option_label = $option_field['option_label'];
            $option_totaled = $option_field['option_totaled'];
            $option_input = $option_field['option_input'];
            if(!empty($option_id) && !empty($option_label)) {
                mysqli_query($dbc, "UPDATE `user_form_fields` SET `label` = '$option_label', `totaled` = '$option_totaled', `source_conditions` = '$option_input', `sort_order` = '$sort_order' WHERE `field_id` = '$option_id'");
                $sort_order++;
                $options_keep[] = $option_id;
            } else if(!empty($option_label) && empty($option_id)) {
                mysqli_query($dbc, "INSERT INTO `user_form_fields` (`form_id`, `name`, `label`, `type`, `totaled`, `source_conditions`, `sort_order`) VALUES ('$formid', '$name', '$option_label', 'OPTION', '$option_totaled', '$option_input', '$sort_order')");
                $sort_order++;
                $option_id = mysqli_insert_id($dbc);
                echo $option_id;
                $options_keep[] = $option_id;
            }
        }
        $options_keep = "'".implode("','", $options_keep)."'";
        $date_of_archival = date('Y-m-d');
        mysqli_query($dbc, "UPDATE `user_form_fields` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `form_id` = '$formid' AND `type` = 'OPTION' AND `name` = '$name' AND `field_id` NOT IN ($options_keep)");
    }

    //Replace form contents names if name changed
    $user_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id` = '$formid'"));
    $contents = html_entity_decode($user_form['contents']);
    $contents = str_replace('[['.$old_name.']]', '[['.$name.']]', $contents);
    $contents = str_replace('[['.$old_name.'[', '[['.$name.'[', $contents);
    $contents = htmlentities($contents);
    $header = html_entity_decode($user_form['header']);
    $header = str_replace('[['.$old_name.']]', '[['.$name.']]', $header);
    $header = str_replace('[['.$old_name.'[', '[['.$name.'[', $header);
    $header = htmlentities($header);
    $footer = html_entity_decode($user_form['footer']);
    $footer = str_replace('[['.$old_name.']]', '[['.$name.']]', $footer);
    $footer = str_replace('[['.$old_name.'[', '[['.$name.'[', $footer);
    $footer = htmlentities($footer);
    mysqli_query($dbc, "UPDATE `user_forms` SET `contents` = '$contents', `header` = '$header', `footer` = '$footer' WHERE `form_id` = '$formid'");
}

if(isset($_GET['fill']) && $_GET['fill'] == 'delete_field'){
    $field_id = filter_var($_POST['field_id'],FILTER_SANITIZE_STRING);
    $field = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `field_id` = '$field_id'"));
    $form_id = $field['form_id'];
    $name = $field['name'];
        $date_of_archival = date('Y-m-d');

    mysqli_query($dbc, "UPDATE `user_form_fields` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `field_id` = '$field_id'");
    mysqli_query($dbc, "UPDATE `user_form_fields` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `form_id` = '$form_id' AND `type` = 'OPTION' and `name` = '$name'");
}

if(isset($_GET['fill']) && $_GET['fill'] == 'sort_fields') {
    $field_id = filter_var($_POST['formid'],FILTER_SANITIZE_STRING);
    $all_fields = json_decode($_POST['field_order']);
    foreach ($all_fields as $order => $field_id) {
        mysqli_query($dbc, "UPDATE `user_form_fields` SET `sort_order` = '$order' WHERE `field_id` = '$field_id'");
    }
}

if(isset($_GET['fill']) && $_GET['fill'] == 'update_styling') {
    $formid = filter_var($_POST['formid'],FILTER_SANITIZE_STRING);

    $header = filter_var(htmlentities($_POST['header']),FILTER_SANITIZE_STRING);
    $header_align = filter_var($_POST['header_align'],FILTER_SANITIZE_STRING);
    $header_font = filter_var($_POST['header_font'],FILTER_SANITIZE_STRING);
    $header_size = filter_var($_POST['header_size'],FILTER_SANITIZE_STRING);
    $header_color = filter_var($_POST['header_color'],FILTER_SANITIZE_STRING);
    $header_styling = filter_var(implode(',',json_decode($_POST['header_styling'])),FILTER_SANITIZE_STRING);
    $header_skip_first_page = filter_var($_POST['header_skip_first_page'],FILTER_SANITIZE_STRING);

    $footer = filter_var(htmlentities($_POST['footer']),FILTER_SANITIZE_STRING);
    $footer_align = filter_var($_POST['footer_align'],FILTER_SANITIZE_STRING);
    $footer_font = filter_var($_POST['footer_font'],FILTER_SANITIZE_STRING);
    $footer_size = filter_var($_POST['footer_size'],FILTER_SANITIZE_STRING);
    $footer_color = filter_var($_POST['footer_color'],FILTER_SANITIZE_STRING);
    $footer_styling = filter_var(implode(',',json_decode($_POST['footer_styling'])),FILTER_SANITIZE_STRING);

    $section_heading_font = filter_var($_POST['section_heading_font'],FILTER_SANITIZE_STRING);
    $section_heading_size = filter_var($_POST['section_heading_size'],FILTER_SANITIZE_STRING);
    $section_heading_color = filter_var($_POST['section_heading_color'],FILTER_SANITIZE_STRING);
    $section_heading_styling = filter_var(implode(',',json_decode($_POST['section_heading_styling'])),FILTER_SANITIZE_STRING);

    $body_heading_font = filter_var($_POST['body_heading_font'],FILTER_SANITIZE_STRING);
    $body_heading_size = filter_var($_POST['body_heading_size'],FILTER_SANITIZE_STRING);
    $body_heading_color = filter_var($_POST['body_heading_color'],FILTER_SANITIZE_STRING);
    $body_heading_styling = filter_var(implode(',',json_decode($_POST['body_heading_styling'])),FILTER_SANITIZE_STRING);

    $body_font = filter_var($_POST['body_font'],FILTER_SANITIZE_STRING);
    $body_size = filter_var($_POST['body_size'],FILTER_SANITIZE_STRING);
    $body_color = filter_var($_POST['body_color'],FILTER_SANITIZE_STRING);
    $body_styling = filter_var(implode(',',json_decode($_POST['body_styling'])),FILTER_SANITIZE_STRING);

    $page_format = filter_var($_POST['page_format'],FILTER_SANITIZE_STRING);

    $advanced_styling = filter_var($_POST['advanced_styling'],FILTER_SANITIZE_STRING);
    $page_by_page = filter_var($_POST['page_by_page'],FILTER_SANITIZE_STRING);
    $hide_labels = filter_var($_POST['hide_labels'],FILTER_SANITIZE_STRING);
    $contents = filter_var(htmlentities($_POST['contents']),FILTER_SANITIZE_STRING);
    $display_form = filter_var($_POST['display_form'],FILTER_SANITIZE_STRING);

    mysqli_query($dbc, "UPDATE `user_forms` SET `header` = '$header', `header_align` = '$header_align', `header_font` = '$header_font', `header_size` = '$header_size', `header_color` = '$header_color', `header_skip_first_page` = '$header_skip_first_page', `footer` = '$footer', `footer_align` = '$footer_align', `footer_font` = '$footer_font', `footer_size` = '$footer_size', `footer_color` = '$footer_color', `section_heading_font` = '$section_heading_font', `section_heading_size` = '$section_heading_size', `section_heading_color` = '$section_heading_color', `body_heading_font` = '$body_heading_font', `body_heading_size` = '$body_heading_size', `body_heading_color` = '$body_heading_color', `font` = '$body_font', `body_size` = '$body_size', `body_color` = '$body_color', `page_format` = '$page_format', `advanced_styling` = '$advanced_styling', `page_by_page` = '$page_by_page', `hide_labels` = '$hide_labels', `contents` = '$contents', `display_form` = '$display_form', `header_styling` = '$header_styling', `footer_styling` = '$footer_styling', `section_heading_styling` = '$section_heading_styling', `body_heading_styling` = '$body_heading_styling', `body_styling` = '$body_styling' WHERE `form_id` = '$formid'");

    if(!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    if(!empty($_FILES['header_logo']['name'])) {
        $header_logo = $basename = preg_replace('/[^a-z0-9.]*/','',strtolower($_FILES['header_logo']['name']));
        $j = 0;
        while(file_exists('download/'.$header_logo)) {
            $header_logo = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
        }
        move_uploaded_file($_FILES['header_logo']['tmp_name'], 'download/'.$header_logo);
        mysqli_query($dbc, "UPDATE `user_forms` SET `header_logo` = '$header_logo' WHERE `form_id`='$formid'");
        echo "header_logo*#*download/".$header_logo;
    }

    if(!empty($_FILES['footer_logo']['name'])) {
        $footer_logo = $basename = preg_replace('/[^a-z0-9.]*/','',strtolower($_FILES['footer_logo']['name']));
        $j = 0;
        while(file_exists('download/'.$footer_logo)) {
            $footer_logo = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
        }
        move_uploaded_file($_FILES['footer_logo']['tmp_name'], 'download/'.$footer_logo);
        mysqli_query($dbc, "UPDATE `user_forms` SET `footer_logo` = '$footer_logo' WHERE `form_id`='$formid'");
        echo "footer_logo*#*download/".$footer_logo;
    }
}

if(isset($_GET['fill']) && $_GET['fill'] == 'update_config') {
    $formid = filter_var($_POST['formid'],FILTER_SANITIZE_STRING);
    $intake_field = filter_var($_POST['intake_field'],FILTER_SANITIZE_STRING);
    $assigned_tiles = filter_var(implode(',', json_decode($_POST['assigned_tiles'])),FILTER_SANITIZE_STRING);
    $attached_contacts = filter_var(implode(',', json_decode($_POST['attached_contacts'])),FILTER_SANITIZE_STRING);
    $attached_contact_categories = filter_var(implode(',', json_decode($_POST['attached_contact_categories'])),FILTER_SANITIZE_STRING);
    $attached_contact_default_field = filter_var($_POST['attached_contact_default_field'],FILTER_SANITIZE_STRING);
    $subtab = filter_var($_POST['subtab'],FILTER_SANITIZE_STRING);
    $form_layout = filter_var($_POST['form_layout'],FILTER_SANITIZE_STRING);

    mysqli_query($dbc, "UPDATE `user_forms` SET `assigned_tile` = '$assigned_tiles', `attached_contacts` = '$attached_contacts', `attached_contact_categories` = '$attached_contact_categories', `intake_field` = '$intake_field', `subtab` = '$subtab', `form_layout` = '$form_layout', `attached_contact_default_field` = '$attached_contact_default_field' WHERE `form_id` = '$formid'");
}

if(isset($_GET['fill']) && $_GET['fill'] == 'delete_logo') {
    $formid = $_POST['formid'];
    $type = $_POST['type'];
    if($type == 'config') {
    $logo = $_POST['logo'] == 'header' ? 'default_head_logo' : 'default_foot_logo';
        mysqli_query($dbc, "UPDATE `field_config_user_forms` SET `$logo` = ''");
    } else {
        $logo = $_POST['logo'] == 'header' ? 'header_logo' : 'footer_logo';
        mysqli_query($dbc, "UPDATE `user_forms` SET `$logo` = '' WHERE `form_id` = '$formid'");
        echo "UPDATE `user_forms` SET `$logo` = '' WHERE `form_id` = '$formid'";
    }
}

if(isset($_GET['fill']) && $_GET['fill'] == 'copy_form') {
    $formid = $_POST['form'];
    $form_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` FROM `user_forms` WHERE `form_id` = '$formid'"))['name'].' Copy';

    mysqli_query($dbc, "INSERT INTO `user_forms` (`name`,`contents`,`header`,`header_logo`,`header_align`,`header_font`,`header_size`,`header_color`,`footer`,`footer_logo`,`footer_align`,`footer_font`,`footer_size`,`footer_color`,`body_heading_font`,`body_heading_size`,`body_heading_color`,`font`,`body_size`,`body_color`,`section_heading_font`,`section_heading_size`,`section_heading_color`,`display_form`,`assigned_tile`,`advanced_styling`,`subtab`,`is_template`,`intake_field`,`page_by_page`,`hide_labels`,`header_styling`,`footer_styling`,`section_heading_styling`,`body_heading_styling`,`body_styling`) SELECT '$form_name',`contents`,`header`,`header_logo`,`header_align`,`header_font`,`header_size`,`header_color`,`footer`,`footer_logo`,`footer_align`,`footer_font`,`footer_size`,`footer_color`,`body_heading_font`,`body_heading_size`,`body_heading_color`,`font`,`body_size`,`body_color`,`section_heading_font`,`section_heading_size`,`section_heading_color`,`display_form`,`assigned_tile`,`advanced_styling`,`subtab`,`is_template`,`intake_field`,`page_by_page`,`hide_labels`,`header_styling`,`footer_styling`,`section_heading_styling`,`body_heading_styling`,`body_styling` FROM `user_forms` WHERE `form_id` = '$formid'");
    $new_formid = mysqli_insert_id($dbc);

    $form_fields = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `deleted` = 0");
    while ($row = mysqli_fetch_array($form_fields)) {
        $field_id = $row['field_id'];
        mysqli_query($dbc, "INSERT INTO `user_form_fields` (`form_id`,`name`,`label`,`sublabel`,`type`,`default`,`references`,`totaled`,`source_table`,`source_conditions`,`sort_order`,`content`,`styling`,`mandatory`) SELECT '$new_formid',`name`,`label`,`sublabel`,`type`,`default`,`references`,`totaled`,`source_table`,`source_conditions`,`sort_order`,`content`,`styling`,`mandatory` FROM `user_form_fields` WHERE `field_id` = '$field_id'");
    }

    $form_pages = mysqli_query($dbc, "SELECT * FROM `user_form_page` WHERE `form_id` = '$formid' AND `deleted` = 0");
    while ($row = mysqli_fetch_array($form_pages)) {
        $page_id = $row['page_id'];
        mysqli_query($dbc, "INSERT INTO `user_form_page` (`form_id`,`page`,`img`) SELECT '$new_formid',`page`,`img` FROM `user_form_page` WHERE `page_id` = '$page_id'");
        $new_page_id = mysqli_insert_id($dbc);
        $form_page_details = mysqli_query($dbc, "SELECT * FROM `user_form_page_detail` WHERE `page_id` = '$page_id' AND `deleted` = 0");
        while ($row2 = mysqli_fetch_array($form_page_details)) {
            $page_detail_id = $row2['page_detail_id'];
            mysqli_query($dbc, "INSERT INTO `user_form_page_detail` (`page_id`,`field_name`,`field_label`,`top`,`left`,`width`,`height`,`white_space`) SELECT '$new_page_id',`field_name`,`field_label`,`top`,`left`,`width`,`height`,`white_space` FROM `user_form_page_detail` WHERE `page_detail_id` = '$page_detail_id'");
        }
    }
    echo $new_formid;
}

if(isset($_GET['fill']) && $_GET['fill'] == 'update_page_detail') {
    $page_id = $_POST['page_id'];
    $page_detail_id = $_POST['page_detail_id'];
    $field_name = filter_var($_POST['field_name'],FILTER_SANITIZE_STRING);
    $field_label = filter_var($_POST['field_label'],FILTER_SANITIZE_STRING);
    $top = explode('px',$_POST['top'])[0];
    $left = explode('px',$_POST['left'])[0];
    $width = explode('px',$_POST['width'])[0];
    $height = explode('px',$_POST['height'])[0];
    $white_space = $_POST['white_space'];

    if(empty($page_detail_id)) {
        mysqli_query($dbc, "INSERT INTO `user_form_page_detail` (`page_id`, `field_name`, `field_label`, `top`, `left`, `width`, `height`, `white_space`) VALUES ('$page_id', '$field_name', '$field_label', '$top', '$left', '$width', '$height', '$white_space')");
        $page_id = mysqli_insert_id($dbc);
        echo $page_id;
    } else {
        mysqli_query($dbc, "UPDATE `user_form_page_detail` SET `top` = '$top', `left` = '$left', `width` = '$width', `height` = '$height' WHERE `page_detail_id` = '$page_detail_id'");
    }
}

if(isset($_GET['fill']) && $_GET['fill'] == 'delete_page_detail') {
    $page_detail_id = $_POST['page_detail_id'];
         $date_of_archival = date('Y-m-d');
   mysqli_query($dbc, "UPDATE `user_form_page_detail` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `page_detail_id` = '$page_detail_id'");
}

if(isset($_GET['fill']) && $_GET['fill'] == 'delete_page') {
    $page_id = $_POST['page_id'];
    $form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_page` WHERE `page_id` = '$page_id'"))['form_id'];
        $date_of_archival = date('Y-m-d');
    mysqli_query($dbc, "UPDATE `user_form_page` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `page_id` = '$page_id'");

    $pages = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_page` WHERE `form_id` = '$form_id' AND `deleted` = 0 ORDER BY `page`"),MYSQLI_ASSOC);
    $page_i = 1;
    foreach ($pages as $page) {
        mysqli_query($dbc, "UPDATE `user_form_page` SET `page` = '$page_i' WHERE `page_id` = '".$page['page_id']."'");
        $page_i++;
    }
}

if(isset($_GET['fill']) && $_GET['fill'] == 'sort_pages') {
    $form_id = $_POST['formid'];
    $page_i = 1;
    foreach ($_POST['pages'] as $page) {
        mysqli_query($dbc, "UPDATE `user_form_page` SET `page` = '$page_i' WHERE `page_id` = '".$page['page_id']."' AND `form_id` = '$form_id'");
        $page_i++;
    }
}