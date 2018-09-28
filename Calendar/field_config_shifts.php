<?php
if (isset($_POST['add_shifts'])) {
    $dayoff_types = filter_var(implode(',',$_POST['dayoff_types']), FILTER_SANITIZE_STRING);
    $dayoff_types_timesheet = filter_var(implode(',',$_POST['dayoff_types_timesheet']), FILTER_SANITIZE_STRING);
    $enabled_fields = implode(',', $_POST['enabled_fields']);
    $contact_category = filter_var($_POST['contact_category'],FILTER_SANITIZE_STRING);

    mysqli_query($dbc, "INSERT INTO `field_config_contacts_shifts` (`dayoff_types`, `dayoff_types_timesheet`, `enabled_fields`, `contact_category`) SELECT 'Stat Day,Sick Day,Vacation Day,Other Leave','','','' FROM (SELECT COUNT(*) rows FROM `field_config_contacts_shifts`) num WHERE num.rows=0");
    mysqli_query($dbc, "UPDATE `field_config_contacts_shifts` SET `dayoff_types` = '$dayoff_types', `dayoff_types_timesheet` = '$dayoff_types_timesheet', `enabled_fields` = '$enabled_fields', `contact_category` = '$contact_category'");

    $shift_conflicts_check_num = $_POST['conflicts_check_num'] > 0 ? $_POST['conflicts_check_num'] : 1;
    $shift_conflicts_check_type = in_array($_POST['conflicts_check_type'], ['months','weeks']) ? $_POST['conflicts_check_type'] : 'months';
    set_config($dbc, 'shift_conflicts_check_num', $shift_conflicts_check_num);
    set_config($dbc, 'shift_conflicts_check_type', $shift_conflicts_check_type);

    //PDF Styling
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    mysqli_query($dbc, "INSERT INTO `field_config_contacts_shifts_pdf` (`header_logo`) SELECT '' FROM (SELECT COUNT(*) rows FROM `field_config_contacts_shifts_pdf`) num WHERE num.rows=0");
    
    $header_logo_align = filter_var($_POST['header_logo_align'],FILTER_SANITIZE_STRING);
    $header_text = filter_var(htmlentities($_POST['header_text']),FILTER_SANITIZE_STRING);
    $header_align = filter_var($_POST['header_align'],FILTER_SANITIZE_STRING);
    $footer_logo_align = filter_var($_POST['footer_logo_align'],FILTER_SANITIZE_STRING);
    $footer_text = filter_var(htmlentities($_POST['footer_text']),FILTER_SANITIZE_STRING);
    $footer_align = filter_var($_POST['footer_align'],FILTER_SANITIZE_STRING);

    if(!empty($_FILES['header_logo']['name'])) {
        $header_logo = $basename = preg_replace('/[^a-z0-9.]*/','',strtolower($_FILES['header_logo']['name']));
        $j = 0;
        while(file_exists('download/'.$header_logo)) {
            $header_logo = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
        }
        move_uploaded_file($_FILES['header_logo']['tmp_name'], 'download/'.$header_logo);
        mysqli_query($dbc, "UPDATE `field_config_contacts_shifts_pdf` SET `header_logo` = '$header_logo'");
    }

    if(!empty($_FILES['footer_logo']['name'])) {
        $footer_logo = $basename = preg_replace('/[^a-z0-9.]*/','',strtolower($_FILES['footer_logo']['name']));
        $j = 0;
        while(file_exists('download/'.$footer_logo)) {
            $footer_logo = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
        }
        move_uploaded_file($_FILES['footer_logo']['tmp_name'], 'download/'.$footer_logo);
        mysqli_query($dbc, "UPDATE `field_config_contacts_shifts_pdf` SET `footer_logo` = '$footer_logo'");
    }

    mysqli_query($dbc, "UPDATE `field_config_contacts_shifts_pdf` SET `header_logo_align` = '$header_logo_align', `header_text` = '$header_text', `header_align` = '$header_align', `footer_logo_align` = '$footer_logo_align', `footer_text` = '$footer_text', `footer_align` = '$footer_align'");

    set_config($dbc, 'shift_calendar_quick_add', filter_var($_POST['shift_calendar_quick_add'],FILTER_SANITIZE_STRING));
    set_config($dbc, 'shift_hide_if_day_off', filter_var($_POST['shift_hide_if_day_off'],FILTER_SANITIZE_STRING));
}
?>
<script type="text/javascript">
$(document).on('change', 'select[name="contact_category"]', displayCalendarColorField);
function displayCalendarColorField() {
    var contact_category = $('[name="contact_category"]').val();
    if(contact_category != undefined && contact_category != '') {
        $('.client_calendar_color').find('.control-label').text('Use '+contact_category+' Calendar Color:');
        $('.client_calendar_color').show();
    } else {
        $('.client_calendar_color').hide();
    }
}
function displayDayOffTypes(dayOffCheckbox) {
    if ($(dayOffCheckbox).is(":checked")) {
        $('#dayoff_types').show();
    } else {
        $('#dayoff_types').hide();
    }
}
function displayConflictSettings(chk) {
    if($(chk).is(':checked')) {
        $('#conflicts_settings').show();
    } else {
        $('#conflicts_settings').hide();
    }
}
function colorCodeChange(sel) {
    $(sel).closest('.form-group').find('[name$="color"]').val(sel.value);
}
function deleteLogo(logo) {
    if(confirm('Are you sure you want to delete this logo?')) {
        var formid = $('#formid').val();
        $.ajax({
            url: '../Calendar/calendar_ajax_all.php?fill=shiftsDeleteLogo',
            type: 'POST',
            data: { logo: logo },
            success: function(response) {
                if(logo == 'header') {
                    $('.header_logo_url').html('');
                } else if(logo == 'footer') {
                    $('.footer_logo_url').html('');
                }
            }
        });
    }
}
</script>
<?php
$contact_category = '';
$dayoff_types = '';
$dayoff_types_timesheet = '';
$enabled_fields = '';

$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"));
if (!empty($get_field_config)) {
    $dayoff_types = $get_field_config['dayoff_types'];
    $dayoff_types_timesheet = $get_field_config['dayoff_types_timesheet'];
    $enabled_fields = ','.$get_field_config['enabled_fields'].',';
} else {
    mysqli_query($dbc, "INSERT INTO `field_config_contacts_shifts` (`dayoff_types`, `enabled_fields`) SELECT 'Stat Day,Sick Day,Vacation Day,Other Leave','' FROM (SELECT COUNT(*) rows FROM `field_config_contacts_shifts`) num WHERE num.rows=0");
    $get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"));
    $dayoff_types = $get_field_config['dayoff_types'];
    $dayoff_types_timesheet = $get_field_config['dayoff_types_timesheet'];
}
$contact_category = $get_field_config['contact_category'];
?>
<h3>Staff Shifts</h3>
<div class="panel-group" id="accordion2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_calendar">Calendar Settings</a>
            </h4>
        </div>
        <div id="collapse_calendar" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Monthly View Quick Add Icon:</label>
                    <div class="col-sm-8">
                        <?php $shift_calendar_quick_add = get_config($dbc, 'shift_calendar_quick_add'); ?>
                        <label class="form-checkbox"><input type="checkbox" name="shift_calendar_quick_add" value="1" <?= $shift_calendar_quick_add == 1 ? 'checked' : '' ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Hide Shifts If Staff Has Day Off:</label>
                    <div class="col-sm-8">
                        <?php $shift_hide_if_day_off = get_config($dbc, 'shift_hide_if_day_off'); ?>
                        <label class="form-checkbox"><input type="checkbox" name="shift_hide_if_day_off" value="1" <?= $shift_hide_if_day_off == 1 ? 'checked' : '' ?>></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field">Field Settings</a>
            </h4>
        </div>
        <div id="collapse_field" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="contact_type" class="col-sm-4 control-label">Contact Category:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Choose a Category..." name="contact_category" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php $category_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`"),MYSQLI_ASSOC);
                            foreach($category_list as $category) {
                                echo '<option '.($category['category'] == $contact_category ? 'selected' : '').' value="'.$category['category'].'">'.$category['category'].'</option>';
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group client_calendar_color" <?= empty($contact_category) ? 'style="display:none;"' : '' ?>>
                    <label class="col-sm-4 control-label">Use <?= $contact_category ?> Calendar Color:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="client_calendar_color" <?= (strpos($enabled_fields, ',client_calendar_color,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="time" class="col-sm-4 control-label">Security Level Defaults:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="security_level" <?= (strpos($enabled_fields, ',security_level,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="heading" class="col-sm-4 control-label">Heading:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="heading" <?= (strpos($enabled_fields, ',heading,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="time" class="col-sm-4 control-label">Time:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="time" <?= (strpos($enabled_fields, ',time,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="dates" class="col-sm-4 control-label">Dates:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="dates" <?= (strpos($enabled_fields, ',dates,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="time" class="col-sm-4 control-label">Availability:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="availability" <?= (strpos($enabled_fields, ',availability,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="breaks" class="col-sm-4 control-label">Breaks:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="breaks" <?= (strpos($enabled_fields, ',breaks,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="repeat_days" class="col-sm-4 control-label">Repeat Days:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="repeat_days" <?= (strpos($enabled_fields, ',repeat_days,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="dayoff_type" class="col-sm-4 control-label">Day Off:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="dayoff_type" <?= (strpos($enabled_fields, ',dayoff_type,') !== FALSE ? 'checked' : '') ?> onclick="displayDayOffTypes(this)"></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes" class="col-sm-4 control-label">Shift Types:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="hours_type" <?= (strpos($enabled_fields, ',hours_type,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes" class="col-sm-4 control-label">Description:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="notes" <?= (strpos($enabled_fields, ',notes,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="import_button" class="col-sm-4 control-label">Import Button:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="import_button" <?= (strpos($enabled_fields, ',import_button,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="export_button" class="col-sm-4 control-label">Export Button:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="export_button" <?= (strpos($enabled_fields, ',export_button,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="shifts_report" class="col-sm-4 control-label">Shifts Report:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="shifts_report" <?= (strpos($enabled_fields, ',shifts_report,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="dayoff_types" class="panel panel-default" <?php echo (strpos($enabled_fields, ',dayoff_type,') !== FALSE ? '' : 'style="display: none;"') ?>>
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dayoff">Day Off Types</a>
            </h4>
        </div>
        <div id="collapse_dayoff" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group" id="dayoff_types">
                    <label for="dayoff_type" class="col-sm-4 control-label"><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='Configure the different Day Off Types. Select a Time Sheet Hour to pair with the Day Off Type to automatically add those Days Off to Time Sheets.'><img src='<?= WEBSITE_URL ?>/img/info.png' width='20'></a></span> Day Off Types:</label>
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label class="col-sm-6 text-center">Dayoff Type</label>
                            <label class="col-sm-4 text-center">Time Sheet Hours</label>
                            <label class="col-sm-2"></label>
                        </div>
                        <?php $dayoff_types_timesheet = explode(',', $dayoff_types_timesheet);
                        foreach(explode(',', $dayoff_types) as $i => $dayoff_type) {
                            $dayoff_type_timesheet = $dayoff_types_timesheet[$i]; ?>
                            <div class="form-group dayoff_block">
                                <div class="col-sm-6">
                                    <input type="text" name="dayoff_types[]" value="<?= $dayoff_type ?>" class="form-control">
                                </div>
                                <div class="col-sm-4">
                                    <select name="dayoff_types_timesheet[]" class="chosen-select-deselect">
                                        <option></option>
                                        <option value="Sick Hrs.Taken" <?= $dayoff_type_timesheet == 'Sick Hrs.Taken' ? 'selected' : '' ?>>Sick Hours</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <img src="../img/remove.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="removeDayOffType(this);">
                                    <img src="../img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right black-color" onclick="addDayOffType();">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_conflicts">Conflict Settings</a>
            </h4>
        </div>
        <div id="collapse_conflicts" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="conflicts_button" class="col-sm-4 control-label">Display Conflicts Button:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="conflicts_button" <?= (strpos($enabled_fields, ',conflicts_button,') !== FALSE ? 'checked' : '') ?> onchange="displayConflictSettings(this);"></label>
                    </div>
                </div>
                <div id="conflicts_settings" class="form-group" <?= strpos($enabled_fields, ',conflicts_button,') !== FALSE ? '' : 'style="display;none;"' ?>>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Number of Days to Check:</label>
                        <div class="col-sm-2">
                            <?php $shift_conflicts_check_num = get_config($dbc, 'shift_conflicts_check_num'); ?>
                            <input type="number" min="1" name="conflicts_check_num" value="<?= $shift_conflicts_check_num > 0 ? $shift_conflicts_check_num : 1 ?>" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <?php $shift_conflicts_check_type = get_config($dbc, 'shift_conflicts_check_type');
                            if(empty($shift_conflicts_check_type)) {
                                $shift_conflicts_check_type = 'months';
                            } ?>
                            <select name="conflicts_check_type" class="chosen-select-deselect form-control">
                                <option value="months" <?= $shift_conflicts_check_type == 'months' ? 'selected' : '' ?> >Months</option>
                                <option value="weeks" <?= $shift_conflicts_check_type == 'weeks' ? 'selected' : '' ?>>Weeks</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="conflicts_highlight" class="col-sm-4 control-label">Highlight Conflicts in Red:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="conflicts_highlight" <?= (strpos($enabled_fields, ',conflicts_highlight,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="conflicts_warning" class="col-sm-4 control-label">Display Warning Icon for Conflicts:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="conflicts_warning" <?= (strpos($enabled_fields, ',conflicts_warning,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pdf">PDF Settings</a>
            </h4>
        </div>
        <div id="collapse_pdf" class="panel-collapse collapse">
            <div class="panel-body">
                <?php $pdf_settings = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts_pdf`"));

                $header_logo = !empty($pdf_settings['header_logo']) ? $pdf_settings['header_logo'] : '';
                $header_logo_align = !empty($pdf_settings['header_logo_align']) ? $pdf_settings['header_logo_align'] : 'R';
                $header_text = !empty($pdf_settings['header_text']) ? $pdf_settings['header_text'] : '';
                $header_align = !empty($pdf_settings['header_align']) ? $pdf_settings['header_align'] : 'L';

                $footer_logo = !empty($pdf_settings['footer_logo']) ? $pdf_settings['footer_logo'] : '';
                $footer_logo_align = !empty($pdf_settings['footer_logo_align']) ? $pdf_settings['footer_logo_align'] : 'L';
                $footer_text = !empty($pdf_settings['footer_text']) ? $pdf_settings['footer_text'] : '';
                $footer_align = !empty($pdf_settings['footer_align']) ? $pdf_settings['footer_align'] : 'C'; ?>

                <h3>Header Settings</h3>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Header Logo:</label>
                    <div class="col-sm-8">
                        <div class="header_logo_url">
                            <?php if(!empty($header_logo) && file_exists('download/'.$header_logo)) { ?>
                                <a href="download/<?= $header_logo ?>" target="_blank">View</a> | <a href="" onclick="deleteLogo('header'); return false;">Delete</a>
                            <?php } ?>
                        </div>
                        <input name="header_logo" type="file" data-filename-placement="inside" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Header Logo Align:</label>
                    <div class="col-sm-8">
                        <select name="header_logo_align" class="chosen-select-deselect form-control">
                            <option></option>
                            <option <?= $header_logo_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
                            <option <?= $header_logo_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
                            <option <?= $header_logo_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Header Text:</label>
                    <div class="col-sm-8">
                        <textarea name="header_text"><?= html_entity_decode($header_text) ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Header Text Align:</label>
                    <div class="col-sm-8">
                        <select name="header_align" class="chosen-select-deselect form-control">
                            <option></option>
                            <option <?= $header_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
                            <option <?= $header_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
                            <option <?= $header_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
                        </select>
                    </div>
                </div>
                <h3>Footer Settings</h3>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Footer Logo:</label>
                    <div class="col-sm-8">
                        <div class="footer_logo_url">
                            <?php if(!empty($footer_logo) && file_exists('download/'.$footer_logo)) { ?>
                                <a href="download/<?= $footer_logo ?>" target="_blank">View</a> | <a href="" onclick="deleteLogo('footer'); return false;">Delete</a>
                            <?php } ?>
                        </div>
                        <input name="footer_logo" type="file" data-filename-placement="inside" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Footer Logo Align:</label>
                    <div class="col-sm-8">
                        <select name="footer_logo_align" class="chosen-select-deselect form-control">
                            <option></option>
                            <option <?= $footer_logo_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
                            <option <?= $footer_logo_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
                            <option <?= $footer_logo_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Footer Text:</label>
                    <div class="col-sm-8">
                        <textarea name="footer_text"><?= html_entity_decode($footer_text) ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Footer Text Align:</label>
                    <div class="col-sm-8">
                        <select name="footer_align" class="chosen-select-deselect form-control">
                            <option></option>
                            <option <?= $footer_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
                            <option <?= $footer_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
                            <option <?= $footer_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group clearfix">
	<div class="col-sm-6">
		<a href="calendars.php" class="btn brand-btn btn-lg">Back</a>
	</div>
	<div class="col-sm-6">
		<button	type="submit" name="add_shifts" value="add_shifts" class="btn brand-btn btn-lg pull-right">Submit</button>
	</div>
</div>