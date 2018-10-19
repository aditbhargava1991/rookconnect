<?php include_once('../include.php');
$category_query = [];
foreach($categories as $cat_id => $label) {
    if(($tile == 'hr' || $tile == $cat_id) && check_subtab_persmission($dbc, 'hr', ROLE, $label)) {
        $category_query[] = $label;
    }
}
$category_query = " AND `category` IN ('".implode("','", $category_query)."')";
$sql = "SELECT * FROM (SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE `deleted`=0 $category_query UNION
    SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE `deleted`=0 $category_query) `items`
    ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)";
$hr_headings = [];
$hr_sub_headings = [];
$hr_third_headings = [];
$hr_forms = [];

$query = mysqli_query($dbc, $sql);
while($form = mysqli_fetch_assoc($query)) {
    $form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));

    if(!empty($form['heading_number']) && ($form_name != $form['heading_number'].' '.$form['heading'])) {
        $hr_headings[$form['heading_number'].$form['heading'].$form['category']] = [
            'category'=>$form['category'],
            'heading_number'=>$form['heading_number'],
            'heading'=>$form['heading']
        ];
    }
    if(!empty($form['sub_heading_number']) && ($form_name != $form['sub_heading_number'].' '.$form['sub_heading'])) {
        $hr_sub_headings[$form['sub_heading_number'].$form['sub_heading'].$form['heading_number'].$form['heading'].$form['category']] = [
            'category'=>$form['category'],
            'heading_number'=>$form['heading_number'],
            'heading'=>$form['heading'],
            'sub_heading_number'=>$form['sub_heading_number'],
            'sub_heading'=>$form['sub_heading']
        ];
    }
    if(!empty($form['third_heading_number']) && ($form_name != $form['third_heading_number'].' '.$form['third_heading'])) {
        $hr_third_headings[$form['third_heading_number'].$form['third_heading'].$form['sub_heading_number'].$form['sub_heading'].$form['heading_number'].$form['heading'].$form['category']] = [
            'category'=>$form['category'],
            'heading_number'=>$form['heading_number'],
            'heading'=>$form['heading'],
            'sub_heading_number'=>$form['sub_heading_number'],
            'sub_heading'=>$form['sub_heading'],
            'third_heading_number'=>$form['third_heading_number'],
            'third_heading'=>$form['third_heading']
        ];
    }
    if(!empty($form_name)) {
        $hr_forms[$form['listing_type'].'#*#'.$form['id']] = [
            'category'=>$form['category'],
            'heading_number'=>$form['heading_number'],
            'heading'=>$form['heading'],
            'sub_heading_number'=>$form['sub_heading_number'],
            'sub_heading'=>$form['sub_heading'],
            'third_heading_number'=>$form['third_heading_number'],
            'third_heading'=>$form['third_heading'],
            'form_name'=>$form_name
        ];
    }
}

if(!empty($_POST['submit_request'])) {
    $form_html = '<ul>';
    foreach($_POST['request_form'] as $request_form) {
        $request_form = $hr_forms[$request_form];
        if(!empty($request_form)) {
            $form_html .= '<li>';
            $form_html .= '<b>Category</b>: '.$request_form['category'].'<br />';
            if(!empty($request_form['heading_number']) && ($request_form['form_name'] != $request_form['heading_number'].' '.$request_form['heading'])) {
                $form_html .= '<b>Section</b>: '.$request_form['heading_number'].' '.$request_form['heading'].'<br />';
            }
            if(!empty($request_form['sub_heading_number']) && ($request_form['form_name'] != $request_form['sub_heading_number'].' '.$request_form['sub_heading'])) {
                $form_html .= '<b>Sub Section</b>: '.$request_form['sub_heading_number'].' '.$request_form['sub_heading'].'<br />';
            }
            if(!empty($request_form['third_heading_number']) && ($request_form['form_name'] != $request_form['third_heading_number'].' '.$request_form['third_heading'])) {
                $form_html .= '<b>Third Section</b>: '.$request_form['third_heading_number'].' '.$request_form['third_heading'].'<br />';
            }
            $form_html .= '<b>Form</b>: '.$request_form['form_name'];
            $form_html .= '</li><br />';
        }
    }
    $form_html .= '</ul>';

    $subject = "HR Update Request";
    $body = get_contact($dbc, $_SESSION['contactid']).' has requested an update on the following HR Forms/Manuals:<br />'.$form_html;
    
    $email_staff = [];
    foreach(array_filter(explode(',', get_config($dbc, 'hr_request_update_security'))) as $request_security) {
        $staff_ids = mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND CONCAT(',',`role`,',') LIKE '%,$request_security,%'"),MYSQLI_ASSOC);
        foreach($staff_ids as $staff_id) {
            $staff_id = $staff_id['contactid'];
            if($staff_id > 0) {
                $email_staff[$staff_id] = get_email($dbc, $staff_id);
            }
        }
    }
    foreach(explode(',', get_config($dbc, 'hr_request_update_staff')) as $staff_id) {
        if($staff_id > 0) {
            $email_staff[$staff_id] = get_email($dbc, $staff_id);
        }
    }
    $email_staff = array_filter(array_unique($email_staff));

    $error_message = '';
    foreach($email_staff as $staff_id => $staff_email) {
        $staff_name = get_contact($dbc, $staff_id);
        try {
            send_email('', $staff_email, '', '', $subject, $body, '');
        } catch(Exception $e) {
            $error_message .= "Unable to email ".$staff_name.". Please try again later.\n";
        }
    }
    if(!empty($error_message)) {
        echo '<script type="text/javascript"> alert(\''.$error_message.'\'); </script>';
    } else {
        echo '<script type="text/javascript"> alert(\'Successfully sent email.\'); </script>';
    }
}
?>
<script type="text/javascript">
$(document).on('change', '.hr_group select', function() { filterForms(this); });
$(document).on('submit', '[name="form_request"]', function() {
    var forms_found = false;
    $('[name="request_form[]"] option:selected').each(function() {
        if(this.value != undefined && this.value != '') {
            forms_found = true;
            return;
        }
    });
    if(!forms_found) {
        alert("No forms selected.");
        return false;   
    }
});
function filterForms(select) {
    var block = $(select).closest('.hr_group');
    var filter_query = '';
    $(block).find('.heading').hide();
    $(block).find('.sub_heading').hide();
    $(block).find('.third_heading').hide();
    $(block).find('.form_heading').hide();

    var category = $(block).find('[name="category"] option:selected').val();
    if(category != undefined && category != '') {
        filter_query += '[data-category="'+category+'"]';
        if($(block).find('[name="heading"] option'+filter_query).length > 0) {
            $(block).find('[name="heading"] option').hide();
            $(block).find('[name="heading"] option'+filter_query).show();
            $(block).find('.heading').show();
        } else {
            $(block).find('[name="heading"] option').prop('selected', false);
            $(block).find('.heading').hide();
        }

        var heading = $(block).find('[name="heading"] option:selected');
        if($(heading).val() != undefined && $(heading).val() != '') {
            var heading_number = $(heading).data('heading-number');
            var heading = $(heading).data('heading');
            filter_query += '[data-heading-number="'+heading_number+'"][data-heading="'+heading+'"]';
        }

        if($(block).find('[name="sub_heading"] option'+filter_query).length > 0) {
            $(block).find('[name="sub_heading"] option').hide();
            $(block).find('[name="sub_heading"] option'+filter_query).show();
            $(block).find('.sub_heading').show();
        } else {
            $(block).find('[name="sub_heading"] option').prop('selected', false);
            $(block).find('.sub_heading').hide();
        }

        var sub_heading = $(block).find('[name="sub_heading"] option:selected');
        if($(sub_heading).val() != undefined && $(sub_heading).val() != '') {
            var sub_heading_number = $(sub_heading).data('sub-heading-number');
            var sub_heading = $(sub_heading).data('sub-heading');
            filter_query += '[data-sub-heading-number="'+heading_number+'"][data-sub-heading="'+heading+'"]';
        }

        if($(block).find('[name="third_heading"] option'+filter_query).length > 0) {
            $(block).find('[name="third_heading"] option').hide();
            $(block).find('[name="third_heading"] option'+filter_query).show();
            $(block).find('.third_heading').show();
        } else {
            $(block).find('[name="third_heading"] option').prop('selected', false);
            $(block).find('.third_heading').hide();
        }

        var third_heading = $(block).find('[name="third_heading"] option:selected');
        if($(third_heading).val() != undefined && $(third_heading).val() != '') {
            var third_heading_number = $(third_heading).data('third-heading-number');
            var third_heading = $(third_heading).data('third-heading');
            filter_query += '[data-third-heading-number="'+heading_number+'"][data-third-heading="'+heading+'"]';
        }

        $('.form_heading').show();
        $(block).find('[name="request_form[]"] option').hide();
        $(block).find('[name="request_form[]"] option'+filter_query).show();
    }
    $(block).find('.heading').trigger('change.select2');
    $(block).find('.sub_heading').trigger('change.select2');
    $(block).find('.third_heading').trigger('change.select2');
    $(block).find('.form_heading').trigger('change.select2');
}
function addForm() {
    destroyInputs('.hr_group');
    var block = $('.hr_group').last();
    var clone = $(block).clone();
    $(clone).find('select').val('');
    $(clone).find('.heading').hide();
    $(clone).find('.sub_heading').hide();
    $(clone).find('.third_heading').hide();
    $(clone).find('.form_heading').hide();
    $(block).after(clone);
    initInputs('.hr_group');
}
function removeForm(img) {
    if($('.hr_group').length <= 1) {
        addForm();
    }
    $(img).closest('.hr_group').remove();
}
</script>

<div class="scale-to-fill has-main-screen">
    <div class="main-screen form-horizontal">
        <div class="standard-body-title">
            <h3>Request an Update</h3>
        </div>
        <div class="standard-body-content" style="padding: 0.5em;">
            <form id="form1" name="form_request" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
                <div class="hr_group">
                    <div class="form-group">
                        <label class="col-sm-4">Category:</label>
                        <div class="col-sm-8">
                            <select name="category" class="chosen-select-deselect" data-placeholder="Select a Category...">
                                <option></option>
                                <?php foreach($categories as $cat_id => $label) {
                                    if(($tile == 'hr' || $tile == $cat_id) && check_subtab_persmission($dbc, 'hr', ROLE, $label) && $cat_id != 'favourites' && $cat_id != 'pinned') { ?>
                                        <option value="<?= $label ?>"><?= $label ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group heading" style="display: none;">
                        <label class="col-sm-4">Section:</label>
                        <div class="col-sm-8">
                            <select name="heading" class="chosen-select-deselect" data-placeholder="Select a Category...">
                                <option></option>
                                <?php foreach($hr_headings as $heading_key => $heading) { ?>
                                    <option data-category="<?= $heading['category'] ?>" data-heading-number="<?= $heading['heading_number'] ?>" data-heading="<?= $heading['heading'] ?>" value="<?= $heading_key ?>"><?= $heading['heading_number'].' '.$heading['heading'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group sub_heading" style="display: none;">
                        <label class="col-sm-4">Sub Section:</label>
                        <div class="col-sm-8">
                            <select name="sub_heading" class="chosen-select-deselect" data-placeholder="Select a Category...">
                                <option></option>
                                <?php foreach($hr_sub_headings as $heading_key => $heading) { ?>
                                    <option data-category="<?= $heading['category'] ?>" data-sub-heading-number="<?= $heading['sub_heading_number'] ?>" data-sub-heading="<?= $heading['sub_heading'] ?>" data-heading-number="<?= $heading['heading_number'] ?>" data-heading="<?= $heading['heading'] ?>" value="<?= $heading_key ?>"><?= $heading['sub_heading_number'].' '.$heading['sub_heading'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group third_heading" style="display: none;">
                        <label class="col-sm-4">Third Section:</label>
                        <div class="col-sm-8">
                            <select name="third_heading" class="chosen-select-deselect" data-placeholder="Select a Category...">
                                <option></option>
                                <?php foreach($hr_third_headings as $heading_key => $heading) { ?>
                                    <option data-category="<?= $heading['category'] ?>" data-third-heading-number="<?= $heading['third_heading_number'] ?>" data-third-heading="<?= $heading['third_heading'] ?>" data-sub-heading-number="<?= $heading['sub_heading_number'] ?>" data-sub-heading="<?= $heading['sub_heading'] ?>" data-heading-number="<?= $heading['heading_number'] ?>" data-heading="<?= $heading['heading'] ?>" value="<?= $heading_key ?>"><?= $heading['third_heading_number'].' '.$heading['third_heading'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group form_heading" style="display: none;">
                        <label class="col-sm-4">Form:</label>
                        <div class="col-sm-8">
                            <select name="request_form[]" class="chosen-select-deselect" data-placeholder="Select a Category...">
                                <option></option>
                                <?php foreach($hr_forms as $form_id => $heading) { ?>
                                    <option data-category="<?= $heading['category'] ?>" data-third-heading-number="<?= $heading['third_heading_number'] ?>" data-third-heading="<?= $heading['third_heading'] ?>" data-sub-heading-number="<?= $heading['sub_heading_number'] ?>" data-sub-heading="<?= $heading['sub_heading'] ?>" data-heading-number="<?= $heading['heading_number'] ?>" data-heading="<?= $heading['heading'] ?>" value="<?= $form_id ?>"><?= $heading['form_name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group pull-right">
                        <a href="" onclick="addForm(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img"></a>
                        <a href="" onclick="removeForm(this); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img"></a>
                    </div>
                    <div class="clearfix"></div>
                    <hr />
                </div>
                <div class="form-group pull-right">
                    <button type="submit" name="submit_request" value="Submit" class="btn brand-btn">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>