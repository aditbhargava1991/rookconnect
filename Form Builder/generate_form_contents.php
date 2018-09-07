<?php include_once('../Form Builder/contactinfo_fields.php');
$form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id`='$form_id'"));
$form_layout = !empty($form['form_layout']) ? $form['form_layout'] : 'Accordions';

$div_i = 0;

$attached_contact_default_field = '';
if($attached_contactid > 0 && !empty($form['attached_contact_default_field']) && empty($pdf_id)) {
    $attached_contact_default_field = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `field_id` = '".$form['attached_contact_default_field']."'"));
    $attached_contact_default_field = 'field_'.preg_replace('/[^a-z0-9_]/','',strtolower($attached_contact_default_field['name']));
}
?>
<script>
$(document).ready(function () {
    $('label.col-sm-4.control-label .field_label').each(function() {
        if ($(this).text().slice(-1) != ':' && $(this).text().trim() != '' && $(this).text().slice(-1) != '?') {
            $(this).text($(this).text() + ':');
        }
    });
    <?php if(!empty($attached_contact_default_field)) { ?>
        $('[name="<?= $attached_contact_default_field ?>"]').val('<?= $attached_contactid ?>').trigger('change.select2').change();
    <?php } ?>
});
$(document).on('change', 'select[name="heading_number"],.form_div select', function() { populateReferenceValues(this); });
function populateReferenceValues(link) {
    var form_id = $('[name="form_id"]').val();
    $('.form_div input[data-refsource="'+$(link).attr('name')+'"]').each(function() {
        var ref_source = $(link).val();
        var ref_value = $(this).data('refvalue');
        var ref_name = $(this).attr('name');
        if(ref_source != undefined && ref_source != '' && ref_value != undefined && ref_value != '') {
            $.ajax({
                type: "GET",
                url: "<?= WEBSITE_URL ?>/Form Builder/form_ajax.php?fill=retrieve_ref&ref_source="+ref_source+"&ref_value="+ref_value+'&ref_name='+ref_name+'&form_id='+form_id,
                dataType: "html",
                success: function(response) {
                    $('[name="'+ref_name+'"]').val(response);
                }
            });
        }
    });
}
function addRow(link) {
    var row = $(link).closest('table').find('tr').last().clone();
    row.find('input').val('');
    $(link).closest('table').append(row).find('tr').last().find('input').first().focus();
    return false;
}
function remRow(link) {
    if($(link).closest('table').find('tr').length == 2) {
        addRow(link);
    }
    $(link).closest('tr').remove();
    return false;
}
function addSignature(btn) {
    var clone = $(btn).closest('div.multisign').clone();
    clone.find('input').not('.datepicker').val('');
    $(btn).closest('div.multisign').after(clone);

    var options = {
      drawOnly : true,
      validateFields : false
    };
    $('.sigPad').signaturePad(options);
    $('#linear').signaturePad({drawOnly:true, lineTop:200});
    $('#smoothed').signaturePad({drawOnly:true, drawBezierCurves:true, lineTop:200});
    $('#smoothed-variableStrokeWidth').signaturePad({drawOnly:true, drawBezierCurves:true, variableStrokeWidth:true, lineTop:200});
}
function removeSignature(btn) {
    if($('div.multisign').length <= 1) {
        addSignature($(btn));
    }
    $(btn).closest('div.multisign').remove();
}
function setCheckboxValue(chkbox) {
    if($(chkbox).is(':checked')) {
        $(chkbox).next('.checkbox_value').val(1);
    } else {
        $(chkbox).next('.checkbox_value').val(0);
    }
}
function checkMandatoryFields() {
    var all_filled = true;
    $('.user_form_field[data-mandatory="1"]').each(function() {
        $(this).find('input.form-control').each(function() {
            if($(this).val() == '') {
                all_filled = false;;
            }
        });
        $(this).find('input.output').each(function() {
            if($(this).val() == '') {
                all_filled = false;;
            }
        });
        $(this).find('select.form-control').each(function() {
            if($(this).val() == '') {
                all_filled = false;;
            }
        });
    });
    if(!all_filled) {
        alert('There are one or more mandatory fields not filled in.');
    }
    return all_filled;
}
</script>

<div class="scale-to-fill form_div">
    <input type="hidden" name="form_id" value="<?= $form_id ?>">
    <input type="hidden" name="assign_id" value="<?= $_GET['assign_id'] ?>">

    <div class="<?= isset($_GET['performance_review']) || $form_layout == 'Sidebar' ? 'form-horizontal' : 'panel-group' ?>" id="accordion">
        <?php if ($form['display_form'] == 1) {
            $default_collapse = ''; ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_content" >
                        Form Content<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_content" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="form-group">
                    <?php echo html_entity_decode($form['contents']); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if(FOLDER_NAME == 'safety') { ?>
            <?php if($form_layout == 'Sidebar') { ?>
                <div id="user_form_div_safety_2" class="tab-section">
                    <h4>Information</h4>
                    <div class="form-group">
                        <label for="business_street" class="col-sm-4 control-label">Date</label>
                        <div class="col-sm-8">
                            <input type="text" name="safety_today_date" value="<?php echo $today_date; ?>" class="form-control" />
                        </div>
                    </div>
                </div>
                <hr>
            <?php } else { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
                                Information<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_info" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="business_street" class="col-sm-4 control-label">Date</label>
                                <div class="col-sm-8">
                                    <input type="text" name="safety_today_date" value="<?php echo $today_date; ?>" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else if($incident_report_form == 1) {
            if(!empty($report_info)) {
                if($form_layout == 'Sidebar') { ?>
                    <div id="user_form_div_increp_info" class="tab-section">
                        <h4>Information</h4>
                        <div class="form-group">
                            <?= html_entity_decode($report_info) ?>
                        </div>
                    </div>
                    <hr>
                <?php } else { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_report_info" >
                                    <?= INC_REP_NOUN ?> Information<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>

                        <div id="collapse_report_info" class="panel-collapse collapse">
                            <div class="panel-body">
                                <?= html_entity_decode($report_info) ?>
                            </div>
                        </div>
                    </div>
                <?php }
            }
            if($form_layout == 'Sidebar') { ?>
                <div id="user_form_div_increp_details" class="tab-section">
                    <h4><?= (strpos($value_config, ','."Type_DetailsLabel".',') !== FALSE ? 'Details of Staff/Member(s) Involved' : 'Type & Individuals') ?></h4>
                    <?php include('../Incident Report/add_incident_report_fields_details.php'); ?>
                </div>
                <hr>
            <?php } else { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_inc_rep_details" >
                                <?= (strpos($value_config, ','."Type_DetailsLabel".',') !== FALSE ? 'Details of Staff/Member(s) Involved' : 'Type & Individuals') ?><span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_inc_rep_details" class="panel-collapse collapse <?php echo $default_collapse; $default_collapse = ''; ?>">
                        <div class="panel-body">
                            <?php include('../Incident Report/add_incident_report_fields_details.php'); ?>
                        </div>
                    </div>
                </div>
            <?php }
        } ?>

        <?php $field_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id`='".$form_id."' AND `type` NOT IN ('REFERENCE','OPTION') AND `deleted`=0 ORDER BY `sort_order`");
        while($field = mysqli_fetch_array($field_list)) {
            $field_post = 'field_'.preg_replace('/[^a-z0-9_]/','',strtolower($field['name']));
            switch($field['default']) {
                case 'TIMESTAMP': $default = date('Y-m-d h:i a'); break;
                case 'SESSION_CONTACT': $default = ($field['type'] == 'SELECT' ? $_SESSION['contactid'] : get_contact($dbc, $_SESSION['contactid'])); break;
                default: $default = $field['default']; break;
            }
            $options = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `name`='".$field['name']."' AND `form_id`='$form_id' AND `type`='OPTION' AND `deleted`=0 ORDER BY `sort_order`");

            $field_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$field['field_id']."'"))['num_rows'];
            if ($field_exists > 0 && !empty($pdf_id)) {
                $field_existing = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$field['field_id']."'"));
                $default = $field_existing['value'];
            }
            
            if($field['sort_order'] == 0 && $field['type'] != 'ACCORDION') {
                if(isset($_GET['performance_review']) || $form_layout == 'Sidebar') { ?>
                    <div id="user_form_div_<?= $div_i++ ?>" class="tab-section">
                    <h4><?= $form['name'] ?></h4>
                <?php } else { ?>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse_fields" >
                                    Form Fields<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>

                        <div id="collapse_fields" class="panel-collapse collapse <?php echo $default_collapse; $default_collapse = ''; ?>">
                            <div class="panel-body">
                <?php } }

                if ($field['type'] == 'ACCORDION' && !isset($_GET['performance_review']) && $form_layout != 'Sidebar') {
                    if ($field['sort_order'] != 0) { ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php if(isset($_GET['performance_review']) || $form_layout == 'Sidebar') { ?>
        <div id="user_form_div_<?= $div_i++ ?>" class="tab-section">
        <h4><?= $field['label'] ?></h4>
        <?php } else { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo $field_post; ?>">
                        <?php echo $field['label']; ?><span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_<?php echo $field_post; ?>" class="panel-collapse collapse <?php echo $default_collapse; $default_collapse = ''; ?>">
                <div class="panel-body">
        <?php } ?>
                <?php } else if($field['type'] == 'ACCORDION' && (isset($_GET['performance_review']) || $form_layout == 'Sidebar')) {
                    if($field['sort_order'] != 0) { ?>
                        </div>
                        <hr>
                    <?php } ?>
                    <div id="user_form_div_<?= $div_i++ ?>" class="tab-section">
                    <h4><?= $field['label'] ?></h4>
                <?php } else { ?>
                    <?php if(isset($_GET['performance_review']) && !$staff_info_set) { ?>
                        <script type="text/javascript">
                        $(document).on('change', 'select[name="pr_position"]', function() { changePosition(this); });
                        function changePosition(sel) {
                            var position = sel.value;
                            $('select[name="pr_staff"]').find('option').hide();
                            $('select[name="pr_staff"]').find('option[data-position="'+position+'"],option[data-position=""]').show();
                            $('select[name="pr_staff"]').trigger('change.select2');
                        }
                        </script>
                        <?php $pr_positions = array_filter(explode(',', get_config($dbc, 'performance_review_positions')));
                        if(!empty($pr_positions)) { ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Staff Position:</label>
                                <div class="col-sm-8" style="padding-top: 7px;">
                                    <select name="pr_position" class="chosen-select-deselect">
                                        <option></option>
                                        <?php foreach ($pr_positions as $pr_position) {
                                            echo '<option value="'.$pr_position.'" '.($get_pr['position'] == $pr_position ? 'selected' : '').'>'.$pr_position.'</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Staff:</label>
                            <div class="col-sm-8" style="padding-top: 7px;">
                                <select name="pr_staff" class="chosen-select-deselect">
                                    <option></option>
                                    <?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
                                    foreach ($staff_list as $staff_id) { ?>
                                        <option value="<?= $staff_id ?>" <?= $get_pr['userid'] == $staff_id ? 'selected' : '' ?> data-position="<?= get_contact($dbc, $staff_id, 'position') ?>"><?= get_contact($dbc, $staff_id) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    <?php $staff_info_set = true; } ?>
                    <?php if($field['type'] == 'CONTACTINFO') { ?>
                        <div class="col-sm-8 col-sm-offset-4">
                            <h5><?= $field['label'] ?></h5>
                        </div>
                        <?php $contact_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$form_id' AND `name` = '".$field['name']."' AND `type` = 'OPTION' AND `deleted` = 0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
                        foreach($contact_fields as $contact_field) {
                            $data_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$contact_field['field_id']."'"));
                            $default = $data_exists['value']; ?>
                            <div class="form-group user_form_field" data-mandatory="<?= $field['mandatory'] ?>">
                                <label class="col-sm-4 control-label"><span class="field_label"><?= $contact_field['label'] ?><?= $field['mandatory'] == 1 ? '*' : '' ?></span><br><small><?= $field['sublabel'] ?></small></label>
                                <div class="col-sm-8">
                                    <?php switch($contactinfo_fields[$contact_field['source_conditions']]) {
                                        case 'date': ?>
                                            <input type="text" name="<?= $field['name'] ?>[<?= $contact_field['field_id'] ?>]" class="form-control datepicker" value="<?= $default ?>">
                                            <?php break;
                                        default: ?>
                                            <input type="text" name="<?= $field['name'] ?>[<?= $contact_field['field_id'] ?>]" class="form-control" value="<?= $default ?>">
                                            <?php break;
                                    } ?>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="form-group user_form_field" data-mandatory="<?= $field['mandatory'] ?>">
                            <label class="col-sm-4 control-label"><span class="field_label"><?php echo $field['type'] != 'TEXT' ? $field['label'] : ''; ?><?= $field['mandatory'] == 1 ? '*' : '' ?></span><br><small><?= $field['sublabel'] ?></small></label>
                            <div class="col-sm-8" style="padding-top: 7px;">
                                <?php switch($field['type']) {
                                    case 'TEXT':
                                        echo '<span class="pull-left">'.$field['label'].'</span>';
                                        break;
                                    case 'DATE':
                                        $ref_source = 'field_'.mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` FROM `user_form_fields` WHERE `field_id` = '".$field['references']."'"))['name'];
                                        $ref_value = $field['source_conditions'];
                                        echo '<input type="text" name="'.$field_post.'" class="form-control datepicker" value="'.substr($default,0,10).'" data-refsource="'.$ref_source.'" data-refvalue="'.$ref_value.'">';
                                        break;
                                    case 'DATETIME':
                                        echo '<input type="text" name="'.$field_post.'" class="form-control dateandtimepicker" value="'.substr($default,11).'">';
                                        break;
                                    case 'TIME':
                                        echo '<input type="text" name="'.$field_post.'" class="form-control timepicker" value="'.$default.'">';
                                        break;
                                    case 'SELECT':
                                        echo '<select '.($field['content'] == 'multiple' ? 'name="'.$field_post.'[]" multiple' : 'name="'.$field_post.'"').' class="form-control chosen-select-deselect select_ref"><option></option>';
                                        $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `category`='".$field['source_conditions']."' AND `deleted`=0 AND `status`>0 AND `show_hide_user`=1"),MYSQLI_ASSOC));
                                        foreach($contact_list as $contactid) {
                                            $contact = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name`, `nick_name` FROM `contacts` WHERE `contactid`='$contactid'"));
                                            $name = ($contact['name'] != '' ? decryptIt($contact['name']) : '');
                                            if($contact['first_name'].$contact['last_name'].$contact['nick_name'] != '') {
                                                $name .= ($name != '' ? ': ' : '').decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']);
                                            }
                                            $name .= ($contact['nick_name'] != '' ? '"'.$contact['nick_name'].'"' : '');
                                            echo '<option '.(strpos('#*#'.$default.'#*#', '#*#'.$contactid.'#*#') !== FALSE ? 'selected' : '').' value="'.$contactid.'">'.$name.'</option>';
                                        }
                                        echo '</select>';
                                        break;
                                    case 'SELECT_CUS':
                                        echo '<select '.($field['content'] == 'multiple' ? 'name="'.$field_post.'[]" multiple' : 'name="'.$field_post.'"').' class="form-control chosen-select-deselect"><option></option>';
                                        while ($option = mysqli_fetch_array($options)) {
                                            echo '<option '.(strpos('#*#'.$default.'#*#', '#*#'.$option['label'].'#*#') !== FALSE ? 'selected' : '').' value="'.$option['label'].'">'.$option['label'].'</option>';
                                        }
                                        echo '</select>';
                                    case 'RADIO':
                                        while($radio = mysqli_fetch_array($options)) {
                                            $option_data = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$radio['field_id']."'"));
                                            $checked = '';
                                            if ($option_data['checked'] == 1) {
                                                $checked = ' checked';
                                            }
                                            echo '<label class="form-checkbox"><input type="radio" name="'.$field_post.'" value="'.$radio['label'].'"'.$checked.'> '.$radio['label'].'</label>';
                                        }
                                        break;
                                    case 'CHECKBOX':
                                        while($check = mysqli_fetch_array($options)) {
                                            $option_data = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$check['field_id']."'"));
                                            $checked = '';
                                            if ($option_data['checked'] == 1) {
                                                $checked = ' checked';
                                            }
                                            if($check['source_conditions'] == 'input') {
                                                echo '<label class="form-checkbox" style="max-width: 50em;"><input type="checkbox" name="'.$field_post.'[]" value="'.$check['label'].'"'.$checked.'> '.$check['label'].'&nbsp;&nbsp;<input type="text" name="'.$field_post.'_input['.$check['field_id'].']" value="'.explode('*#*', $option_data['value'])[1].'" class="form-control inline"></label>';
                                            } else {
                                                echo '<label class="form-checkbox"><input type="checkbox" name="'.$field_post.'[]" value="'.$check['label'].'"'.$checked.'> '.$check['label'].'</label>';
                                            }
                                        }
                                        break;
                                    case 'CHECKINFO':
                                        $option_data = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$field['field_id']."'"));
                                        echo '<label class="form-checkbox col-sm-12" style="max-width:100%;"><div class="col-sm-1"><input type="checkbox" name="'.$field_post.'_checked" value="1"'.($option_data['checked'] == 1 ? ' checked' : '').'></div>';
                                        echo '<div class="col-sm-11"><input type="text" name="'.$field_post.'" value="'.$default.'" class="form-control"></div></label>';
                                        break;
                                    case 'SIGNONLY':
                                        $sign_data = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$field['field_id']."' ORDER BY `data_id` DESC"), MYSQLI_ASSOC);
                                        if ($sign_data[0]['value'] != '') {
                                            echo '<table border="0"><tr><td><img src="'.$sign_data[0]['value'].'" height="120" border="0" alt="" style="border-bottom:1px solid black;"></td></tr><tr><td>';
                                            echo '</td></tr></table>';
                                        } else {
                                            $output_name = $field_post.'_SIGN';
                                            include('../phpsign/sign_multiple.php');
                                        }
                                        break;
                                    case 'SIGN':
                                        $sign_data = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$field['field_id']."' ORDER BY `data_id` DESC"), MYSQLI_ASSOC);
                                        if ($sign_data[0]['value'] != '') {
                                            echo '<table border="0"><tr><td><img src="'.$sign_data[0]['value'].'" height="120" border="0" alt="" style="border-bottom:1px solid black;"></td></tr><tr><td>';
                                            echo 'Name: '.$sign_data[1]['value'];
                                            echo '<br />Date: '.$sign_data[2]['value'];
                                            echo '</td></tr></table>';
                                        } else {
                                            $output_name = $field_post.'_SIGN';
                                            include('../phpsign/sign_multiple.php');
                                            echo '<input type="text" placeholder="Insert Name Here" name="'.$field_post.'_NAME" value="'.$default.'" class="form-control">';
                                            echo '<input type="text" name="'.$field_post.'_DATE" value="'.date('Y-m-d').'" class="form-control datepicker">';
                                        }
                                        break;
                                    case 'MULTISIGN':
                                        $sign_data = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$field['field_id']."' ORDER BY `data_id` DESC"), MYSQLI_ASSOC);
                                        $sign_imgs = explode('*#*', $sign_data[0]['value']);
                                        $sign_names = explode('*#*', $sign_data[1]['value']);
                                        $sign_dates = explode('*#*', $sign_data[2]['value']);
                                        for ($i = 0; $i < count($sign_imgs); $i++) {
                                            if ($sign_imgs[$i] != '') {
                                                echo '<table border="0"><tr><td><img src="'.$sign_imgs[$i].'" height="120" border="0" alt="" style="border-bottom:1px solid black;"></td></tr><tr><td>';
                                                echo 'Name: '.$sign_names[$i];
                                                echo '<br />Date: '.$sign_dates[$i];
                                                echo '</td></tr></table>';
                                            }
                                        }
                                        echo '<div class="multisign">';
                                        $output_name = $field_post.'_SIGN[]';
                                        include('../phpsign/sign_multiple.php');
                                        echo '<input type="text" placeholder="Insert Name Here" name="'.$field_post.'_NAME[]" class="form-control">';
                                        echo '<input type="text" name="'.$field_post.'_DATE[]" value="'.date('Y-m-d').'" class="form-control datepicker">';
                                        echo '<div class="add_remove_buttons">';
                                        echo '<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addSignature(this);">';
                                        echo '<img src="../img/remove.png" class="inline-img pull-right" onclick="removeSignature(this);">';
                                        echo '</div>';
                                        echo '</div>';
                                        break;
                                    case 'TABLE':
                                        $body_tr = '';
                                        echo '<table class="table table-bordered"><tr class="hidden-sm hidden-xs">';
                                        $option_data = mysqli_query($dbc, "SELECT * FROM `user_form_data` d, `user_form_fields` f WHERE d.`field_id` = f.`field_id` AND `type` = 'OPTION' AND `pdf_id` = '$pdf_id' AND `name` = '".$field['name']."' ORDER BY `sort_order`");
                                        $table_data = [];
                                        while ($option = mysqli_fetch_array($option_data)) {
                                            $table_data[] = [explode('*#*', $option['value']), $option['label'], $option['totaled']];
                                        }
                                        for($i = 0; $i < count($table_data[0][0]); $i++) {
                                            $body_tr .= '<tr>';
                                            for ($j = 0; $j < count($table_data); $j++) {
                                                $label_id = 'option_'.preg_replace('/[^a-z0-9_]/','',strtolower($table_data[$j][1]));
                                                $body_tr .= '<td data-title="'.$table_data[$j][1].'"><input type="'.($table_data[$j][2] == 1 ? 'number" step="any' : 'text').'" name="'.$label_id.'[]" class="form-control" value="'.$table_data[$j][0][$i].'"></td>';
                                            }
                                            $body_tr .= '<td>';
                                            $body_tr .= '<a href="" onclick="return addRow(this);" class="pull-right"><img src="'.WEBSITE_URL.'/img/plus.png" style="width:1.5em;"></a>';
                                            $body_tr .= '<a href="" onclick="return remRow(this);" class="pull-right"><img src="'.WEBSITE_URL.'/img/remove.png" style="width:1.5em;"></a></td></tr>';
                                        }
                                        while($tr = mysqli_fetch_array($options)) {
                                            $label_id = 'option_'.preg_replace('/[^a-z0-9_]/','',strtolower($tr['label']));
                                            echo '<th>'.$tr['label'].'</th>';
                                            $body_tr .= '<td data-title="'.$tr['label'].'"><input type="'.($tr['totaled'] == 1 ? 'number" step="any' : 'text').'" name="'.$label_id.'[]" class="form-control"></td>';
                                        }
                                        echo '<th style="width:4.5em;"></th></tr>'.$body_tr.'<td>';
                                        echo '<a href="" onclick="return addRow(this);" class="pull-right"><img src="'.WEBSITE_URL.'/img/plus.png" style="width:1.5em;"></a>';
                                        echo '<a href="" onclick="return remRow(this);" class="pull-right" style="position: relative; left: -1em;"><img src="'.WEBSITE_URL.'/img/remove.png" style="width:1.5em;"></a></td></tr></table>';
                                        break;
                                    case 'TABLEADV': ?>
                                        <script type="text/javascript">
                                        $(document).ready(function () {
                                            var tableadv = $('#table_<?= $field['name'] ?>');
                                            $('table input').change(function() {
                                                $(tableadv).find('input').each(function() {
                                                    if($(this).data('calculation').charAt(0) == '=') {
                                                        var equation = $(this).data('calculation').substring(1);
                                                        var new_equation = '';
                                                        var cell = '';
                                                        for(var i = 0; i <= equation.length; i++) {
                                                            if(equation[i] == '*' || equation[i] == '/' || equation[i] == '+' || equation[i] == '-' || i == equation.length) {
                                                                var cell_arr = cell.split('.');
                                                                if(cell_arr.length > 1) {
                                                                    cell_table = $('#table_'+cell_arr[0]);
                                                                    cell = cell_arr[1];
                                                                } else {
                                                                    cell_table = tableadv;
                                                                    cell = cell_arr[0];
                                                                }
                                                                var cell_value = $(cell_table).find('td[data-cell="'+cell+'"],th[data-cell="'+cell+'"]');
                                                                if($(cell_value).find('input').length > 0) {
                                                                    cell_value = $(cell_value).find('input').val();
                                                                } else {
                                                                    cell_value = $(cell_value).text();
                                                                }
                                                                if(cell_value.toLowerCase() == 'y') {
                                                                    cell_value = 1;
                                                                } else {
                                                                    cell_value = parseFloat(cell_value.replace('$',''));
                                                                    if(!cell_value > 0) {
                                                                        cell_value = 0;
                                                                    }
                                                                }
                                                                if(i == equation.length) {
                                                                    new_equation += cell_value;
                                                                } else {
                                                                    new_equation += cell_value+equation[i];
                                                                }
                                                                cell = '';
                                                            } else {
                                                                cell = cell.concat(equation[i]);
                                                            }
                                                        }
                                                        $(this).val(eval(new_equation).toFixed(2));
                                                    }
                                                });
                                            });
                                        });
                                        </script>
                                        <?php
                                        $options = mysqli_fetch_all($options, MYSQLI_ASSOC);
                                        echo '<table id="table_'.$field['name'].'" class="table table-bordered"><tr>';
                                        $table_headers = explode('*#*', $options[0]['label']);
                                        $cell_row = 1;
                                        $cell_column = "a";
                                        foreach ($table_headers as $table_header) {
                                            if (strpos($table_header, '[[disable]]') === FALSE) {
                                                $cell_values = explode('[[', $table_header);
                                                echo '<th'.(!empty($cell_values[1]) ? ' '.rtrim(html_entity_decode($cell_values[1]), ']]') : '').' data-cell="'.$cell_column.$cell_row.'">';
                                                echo html_entity_decode($cell_values[0]).'</th>';
                                            }
                                            $cell_column = ++$cell_column;
                                        }
                                        $cell_row++;
                                        echo '</tr>';
                                        for ($i = 1; $i < count($options); $i++) {
                                            $table_rows = explode('*#*', $options[$i]['label']);
                                            $saved_values = mysqli_fetch_array(mysqli_query($dbc, "SELECT `value` FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '".$options[$i]['field_id']."'"))['value'];
                                            $saved_values = explode('*#*', $saved_values);
                                            $input_i = 0;
                                            $cell_column = "a";
                                            echo '<tr>';
                                            foreach ($table_rows as $single_cell) {
                                                if (strpos($single_cell, '[[disable]]') === FALSE) {
                                                    if (strpos($single_cell, '[[checkbox]]') !== FALSE) {
                                                        $single_cell = str_replace('[[checkbox]]', '<input type="checkbox" style="width: 20px; height: 20px; position: relative; left: calc(50% - 10px);" onclick="setCheckboxValue(this);"'.($saved_values[$input_i] == 1 ? 'checked="checked"' : '').'><input type="hidden" name="option_row_'.$options[$i]['field_id'].'[]" class="checkbox_value" value="'.($saved_values[$input_i] == 1 ? '1' : '0').'">', $single_cell);
                                                        $input_i++;
                                                    }
                                                    str_replace('[[bullet]]', '', $single_cell);
                                                    $cell_values = explode('[[', $single_cell);
                                                    echo '<td'.(!empty($cell_values[1]) ? ' '.rtrim(html_entity_decode($cell_values[1]), ']]') : '').' data-cell="'.$cell_column.$cell_row.'">';
    												$cell_fields = explode('|',$cell_values[0]);
    												if(count($cell_fields) > 1) {
    													if($cell_fields[0] == 'hours') {
    														$billables = json_decode(urldecode($_GET['lines']));
    														$hours = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(IF(`hours_set` > 0, `hours_set`, `hours_tracked`)) `hours` FROM `ticket_attached` WHERE `ticketid` IN (SELECT `billable_id` FROM `project_billable` WHERE `id` IN (".implode(',',$billables).") AND `billable_table`='tickets') AND `deleted`=0 AND `src_table` IN ('Staff','Staff_Tasks') AND `item_id` IN (SELECT `contactid` FROM `contacts` WHERE `category_contact`='".$cell_fields[1]."')"))['hours'];
    														echo '<input type="text" name="option_row_'.$options[$i]['field_id'].'[]" class="form-control" value="'.($saved_values[$input_i] > 0 ? $saved_values[$input_i] : $hours).'">';
    													}
    												} else if (empty($cell_values[0]) || $cell_values[0][0] == '=') {
                                                        echo '<input type="text" name="option_row_'.$options[$i]['field_id'].'[]" class="form-control" value="'.$saved_values[$input_i].'" data-calculation="'.strtolower($cell_values[0]).'">';
                                                        $input_i++;
                                                    } else {
                                                        echo html_entity_decode($cell_values[0]);
                                                    }
                                                    echo '</td>';
                                                }
                                                $cell_column = ++$cell_column;
                                            }
                                            $cell_row++;
                                            echo '</tr>';
                                        }
                                        echo '</table>';
                                        break;
                                    case 'TEXTBLOCK':
                                        $input_values = explode('*#*', $default);
                                        $text_content = explode('[[input]]', html_entity_decode($field['content']));
                                        $input_i = 0;
                                        $new_text = '';
                                        for($i = 0; $i < count($text_content); $i++) {
                                            if ($i == count($text_content) - 1) {
                                                $new_text .= $text_content[$i];
                                            } else {
                                                $new_text .= $text_content[$i].'<input type="text" name="'.$field['name'].'[]" class="form-control" value="'.$input_values[$input_i].'" style="width: 20em; display: inline;">';
                                                $input_i++;
                                            }
                                        }
                                        echo '<span class="pull-left">'.$new_text.'</span>';
                                        break;
                                    case 'TEXTAREA':
                                        echo '<textarea name="'.$field_post.'" class="form-control">'.html_entity_decode($default).'</textarea>';
                                        break;
                                    case 'TEXTBOXREF':
                                        $ref_source = 'field_'.mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` FROM `user_form_fields` WHERE `field_id` = '".$field['references']."'"))['name'];
                                        $ref_value = $field['source_conditions'];
                                        echo '<input type="text" name="'.$field_post.'" class="form-control" value="'.$default.'" data-refsource="'.$ref_source.'" data-refvalue="'.$ref_value.'">';
                                        break;
                                    case 'SLIDER':
                                        $slider_arr = explode(',', $field['content']);
                                        $slider_min = $slider_arr[0];
                                        $slider_max = $slider_arr[1];
                                        $slider_increment = $slider_arr[2]; ?>
                                        <script type="text/javascript">
                                        $(document).ready(function() {
                                            $('#<?= $field_post ?>').slider({
                                                value: <?= !empty($default) ? $default : $slider_min ?>,
                                                min: <?= $slider_min ?>,
                                                max: <?= $slider_max ?>,
                                                step: <?= $slider_increment ?>,
                                                create: function() {
                                                    $('#<?= $field_post ?>_handle').text($(this).slider('value'));
                                                },
                                                slide: function(event, ui) {
                                                    $('#<?= $field_post ?>_handle').text(ui.value);
                                                    $('[name="<?= $field_post ?>"]').val(ui.value).trigger('change');
                                                }
                                            });
                                        });
                                        </script>
                                        <?php
                                        echo '<input type="hidden" name="'.$field_post.'" value="'.(!empty($default) ? $default : $slider_min).'">';
                                        echo '<div id="'.$field_post.'" class="slider-custom"><div id="'.$field_post.'_handle" class="ui-slider-handle slider-custom-handle"></div></div><span class="pull-left" style="margin-left: 1.5em;">'.$slider_min.'</span><span class="pull-right" style="margin-right: 1.5em;">'.$slider_max.'</span>';
                                        break;
                                    case 'SLIDER_TOTAL':
                                        $slider_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `field_id` IN (".$field['content'].") AND `form_id` = '".$form_id."' AND `type` = 'SLIDER' AND `deleted` = 0"),MYSQLI_ASSOC);
                                        $slider_field_names = [];
                                        foreach ($slider_fields as $slider_field) {
                                            $slider_field_names[] = $slider_field['name'];
                                        }
                                        $slider_js_names = '[name="field_'.implode('"],[name="field_', $slider_field_names).'"]'; ?>
                                        <script type="text/javascript">
                                        $(document).ready(function() {
                                            $('<?= $slider_js_names ?>').change(function() {
                                                <?= $field_post ?>_total();
                                            });
                                            <?= $field_post ?>_total();
                                        });
                                        function <?= $field_post ?>_total() {
                                            var slider_total = 0;
                                            <?php foreach ($slider_field_names as $slider_field_name) { ?>
                                                var slider_value = parseInt($('[name="field_<?= $slider_field_name ?>"]').val());
                                                slider_total = slider_total + slider_value;
                                            <?php } ?>
                                            $('#<?= $field_post ?>').text(slider_total);
                                            $('[name="<?= $field_post ?>"]').val(slider_total);
                                        }
                                        </script>
                                        <?php
                                        echo '<input type="hidden" name="'.$field_post.'" value="'.$default.'" class="form-control">';
                                        echo '<span style="margin-left: 1.5em;" id="'.$field_post.'">0</span>';
                                        break;
                                    case 'SERVICES': ?>
                                        <table id="no-more-tables" class="table table-bordered">
                                            <tr class="hidden-xs">
                                                <th>Service</th>
                                                <th <?= !empty($_SESSION['contactid']) || strpos(','.$field['source_conditions'].',', ',hide_from_external,') === FALSE ? '' : 'style="display:none;"' ?>>Price</th>
                                                <th>Include</th>
                                            </tr>
                                            <?php $form_services = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id`='$form_id' AND `type`='OPTION' AND `name`='".$field['name']."' AND '".$field['type']."' IN ('SERVICES') AND `deleted`=0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
                                            foreach($form_services as $form_service) {
                                                $service = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '".$form_service['source_conditions']."'"));

                                                $rate_info = $dbc->query("SELECT `value` `price`, `checked` FROM `user_form_data` WHERE `pdf_id` = '$pdf_id' AND `field_id` = '{$form_service['field_id']}' UNION
                                                    SELECT `service_rate` `price`, '0' `checked` FROM `service_rate_card` WHERE `deleted`=0 AND `serviceid`='{$service['serviceid']}' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
                                                    SELECT `cust_price` `price`, '0' `checked` FROM `company_rate_card` WHERE LOWER(`tile_name`)='services' AND `item_id`='{$service['serviceid']}' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc();
                                                $service_checked = $rate_info['checked'];
                                                $rate_info = $rate_info['price'];
                                                $rate = 0.00;
                                                if(strpos($rate_info,'#') !== FALSE) {
                                                    foreach(explode('**',$rate_info) as $rate_line) {
                                                        $rate_line = explode('#',$rate_line);
                                                        if($rate_line[0] == $item[$table_id]) {
                                                            $rate = $rate_line[1];
                                                        }
                                                    }
                                                } else {
                                                    $rate = $rate_info;
                                                }
                                                if(empty($rate)) {
                                                    $rate = 0.00;
                                                } ?>
                                                <tr>
                                                    <td data-title="Service"><?= $service['heading'] ?></td>
                                                    <td data-title="Price" <?= !empty($_SESSION['contactid']) || strpos(','.$field['source_conditions'].',', ',hide_from_external,') === FALSE ? '' : 'style="display:none;"' ?>><input type="number" name="<?= $field_post ?>[<?= $form_service['field_id'] ?>]" value="<?= $rate ?>" min="0.00" step="0.01" class="form-control" <?= empty($_SESSION['contactid']) ? 'readonly' : '' ?>></td>
                                                    <td data-title="Include"><label class="form-checkbox"><input type="checkbox" name="<?= $field_post ?>_add[<?= $form_service['field_id'] ?>]" value="1" <?= $service_checked == 1 ? 'checked' : '' ?>></label></td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                        <?php break;
                                    case 'FILE': ?>
                                        <?php if(!empty($default)) { ?>
                                            <div class="existing_file">
                                                <a href="<?= $default ?>" target="_blank">View</a> | <a href="" onclick="$(this).closest('.existing_file').find('[name=<?= $field_post ?>_delete]').val(1); $(this).closest('.existing_file').hide(); return false;">Delete</a>
                                                <input type="hidden" name="<?= $field_post ?>_delete" value="0">
                                                <input type="hidden" name="<?= $field_post ?>_existing" value="<?= $default ?>">
                                            </div>
                                        <?php } ?>
                                        <input type="file" name="<?= $field_post ?>" data-filename-placement="inside" class="form-control" onchange="$(this).closest('user_form_field').find('[name=<?= $field_post ?>_delete]').val(0);">
                                        <?php break;
                                    case 'HR': ?>
                                        <hr />
                                        <?php break;
                                    default:
                                        $ref_source = 'field_'.mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` FROM `user_form_fields` WHERE `field_id` = '".$field['references']."'"))['name'];
                                        $ref_value = $field['source_conditions'];
                                        $ref_html = '';
                                        if(!empty($ref_source)) {
                                            $ref_html = 'data-refsource="'.$ref_source.'" data-refvalue="'.$ref_value.'"';
                                        }
                                        echo '<input type="text" name="'.$field_post.'" class="form-control" value="'.$default.'" '.$ref_html.'>';
                                        break;
                                } ?>
                            </div>
                        </div>
                <?php }}} ?>
    <?php if(!isset($_GET['performance_review']) && $form_layout != 'Sidebar') { ?>
            </div>
        </div>
    </div>
    <?php } else { ?>
    </div>
    <?php } ?>
    <?php if(!empty($_GET['formid']) && FOLDER_NAME == 'safety') {
        $sa = mysqli_query($dbc, "SELECT * FROM safety_attendance WHERE fieldlevelriskid = '$pdf_id' AND safetyid='$safetyid'");
        $sa_inc=  0;
        while($row_sa = mysqli_fetch_array( $sa )) {
            $assign_staff_sa = $row_sa['assign_staff'];
            $assign_staff_id = $row_sa['safetyattid'];
            $assign_staff_done = $row_sa['done'];
        ?>
        <?php if($form_layout == 'Sidebar') { ?>
            <div id="user_form_div_safety_sigs" class="tab-section">
            <h4><?= $assign_staff_sa ?></h4>
        <?php } else { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sa<?php echo $sa_inc;?>" >
                        <?php echo $assign_staff_sa; ?><span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_sa<?php echo $sa_inc;?>" class="panel-collapse collapse">
                <div class="panel-body">
        <?php } ?>
                    <?php
                    if($assign_staff_done == 0) { ?>

                    <?php if (strpos($assign_staff_sa, 'Extra') !== false) { ?>
                       <div class="form-group">
                        <label for="business_street" class="col-sm-4 control-label">Name:</label>
                        <div class="col-sm-8">
                            <input name="assign_staff_<?php echo $assign_staff_id;?>" type="text" class="form-control" />
                        </div>
                      </div>
                    <?php } ?>
                    <?php $output_name = 'sign_'.$assign_staff_id; ?>
                    <?php include ('../phpsign/sign_multiple.php'); ?>

                    <?php } ?>

        <?php if($form_layout == 'Sidebar') { ?>
            </div>
        <?php } else { ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php $sa_inc++;
        }
    } else if($incident_report_form == 1 && $get_field_config['pdf_notes'] != '') {
        if($form_layout == 'Sidebar') { ?>
            <hr>
            <div id="user_form_div_increp_notes" class="tab-section">
                <h4>Description</h4>
                <div class="form-group">
                    <?= html_entity_decode($get_field_config['pdf_notes']) ?>
                </div>
            </div>
            <hr>
        <?php } else { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_inc_rep_notes" >
                            Description<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_inc_rep_notes" class="panel-collapse collapse <?php echo $default_collapse; $default_collapse = ''; ?>">
                    <div class="panel-body">
                        <?= html_entity_decode($get_field_config['pdf_notes']) ?>
                    </div>
                </div>
            </div>
        <?php }
    } ?>
    </div>
</div>