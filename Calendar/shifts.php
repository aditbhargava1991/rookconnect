<?php include_once('../include.php');
checkAuthorised('calendar_rook');
error_reporting(0);
if (isset($_POST['submit'])) {
    $contactid = filter_var($_POST['shift_contactid'],FILTER_SANITIZE_STRING);
    $security_level = filter_var($_POST['shift_security_level'],FILTER_SANITIZE_STRING);
    if(!empty($security_level)) {
        $contactid = 0;
    }
    $clientid = filter_var($_POST['shift_clientid'],FILTER_SANITIZE_STRING);
    $startdate = filter_var($_POST['shift_startdate'],FILTER_SANITIZE_STRING);
    $enddate = filter_var($_POST['shift_enddate'],FILTER_SANITIZE_STRING);
    $starttime = filter_var($_POST['shift_starttime'],FILTER_SANITIZE_STRING);
    $endtime = filter_var($_POST['shift_endtime'],FILTER_SANITIZE_STRING);
    $availability = filter_var($_POST['shift_availability'],FILTER_SANITIZE_STRING);
    if($availability == 'Available Anytime') {
        $starttime = '12:00 AM';
        $endtime = '11:59 PM';
    }
    if($_POST['shift_type_check'] == 'dayoff') {
        $dayoff_type = filter_var($_POST['shift_dayoff_type'],FILTER_SANITIZE_STRING);
        if(empty($dayoff_type)) {
            $dayoff_type = 'Day Off';
        }
    } else if(isset($_POST['break_check'])) {
        $break_starttime = filter_var($_POST['shift_break_starttime'],FILTER_SANITIZE_STRING);
        $break_endtime = filter_var($_POST['shift_break_endtime'],FILTER_SANITIZE_STRING);
    }
    if(isset($_POST['repeat_check'])) {
        $repeat_type = explode('_',$_POST['shift_repeat_type']);
        $repeat_type = filter_var($repeat_type[0],FILTER_SANITIZE_STRING);
        $repeat_interval = filter_var($_POST['shift_repeat_interval'],FILTER_SANITIZE_STRING);
        $repeat_days = filter_var(implode(',',$_POST['shift_repeat_days']),FILTER_SANITIZE_STRING);
    }
    $notes = filter_var(htmlentities($_POST['shift_notes']),FILTER_SANITIZE_STRING);
    $set_hours = filter_var($_POST['set_hours'],FILTER_SANITIZE_STRING);

    if (empty($_POST['shiftid'])) {
        $query = "INSERT INTO `contacts_shifts` (`contactid`, `security_level`, `clientid`, `startdate`, `enddate`, `starttime`, `endtime`, `availability`, `break_starttime`, `break_endtime`, `dayoff_type`, `repeat_type`, `repeat_interval`, `repeat_days`, `notes`, `set_hours`) VALUES ('$contactid', '$security_level', '$clientid', '$startdate', '$enddate', '$starttime', '$endtime', '$availability', '$break_starttime', '$break_endtime', '$dayoff_type', '$repeat_type', '$repeat_interval', '$repeat_days', '$notes', '$set_hours')";
        $result = mysqli_query($dbc, $query);
        $shiftid = mysqli_insert_id($dbc);
    } else {
        $shiftid = $_POST['shiftid'];
        if($_POST['recurring'] == 'yes') {
            $shift = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'"));
            $edit_type = $_POST['edit_type'];
            $shift_current_date = $_POST['shift_current_date'];
            switch ($edit_type) {
                case 'once':
                    $hide_days = $shift['hide_days'];
                    $hide_days .= ','.$start_date;
                    $hide_days = trim($hide_days, ',');
                    $sql = "UPDATE `contacts_shifts` SET `hide_days` = '$hide_days' WHERE `shiftid` = '$shiftid'";
                    mysqli_query($dbc, $sql);

                    $query = "INSERT INTO `contacts_shifts` (`contactid`, `security_level`, `clientid`, `startdate`, `enddate`, `starttime`, `endtime`, `availability`, `break_starttime`, `break_endtime`, `dayoff_type`, `notes`, `set_hours`) VALUES ('$contactid', '$security_level', '$clientid', '$shift_current_date', '$shift_current_date', '$starttime', '$endtime', '$availability', '$break_starttime', '$break_endtime', '$dayoff_type', '$notes', '$set_hours')";
                    $result = mysqli_query($dbc, $query);
                    $shiftid = mysqli_insert_id($dbc);
                    break;
                case 'following':
                    $end_date = date('Y-m-d', strtotime($shift_current_date.' - 1 day'));
                    $sql = "UPDATE `contacts_shifts` SET `enddate` = '$end_date' WHERE `shiftid` = '$shiftid'";
                    mysqli_query($dbc, $sql);

                    $query = "INSERT INTO `contacts_shifts` (`contactid`, `security_level`, `clientid`, `startdate`, `enddate`, `starttime`, `endtime`, `availability`, `break_starttime`, `break_endtime`, `dayoff_type`, `repeat_type`, `repeat_interval`, `repeat_days`, `notes`, `set_hours`) VALUES ('$contactid', '$security_level', '$clientid', '$shift_current_date', '$enddate', '$starttime', '$availability', '$endtime', '$break_starttime', '$break_endtime', '$dayoff_type', '$repeat_type', '$repeat_interval', '$repeat_days', '$notes', '$set_hours')";
                    $result = mysqli_query($dbc, $query);
                    $shiftid = mysqli_insert_id($dbc);
                    break;
                case 'all':
                default:
                    $query = "UPDATE `contacts_shifts` SET `contactid` = '$contactid', `security_level` = '$security_level', `clientid` = '$clientid', `startdate` = '$startdate', `enddate` = '$enddate', `starttime` = '$starttime', `endtime` = '$endtime', `availability` = '$availability', `break_starttime` = '$break_starttime', `break_endtime` = '$break_endtime', `dayoff_type` = '$dayoff_type', `repeat_type` = '$repeat_type', `repeat_interval` = '$repeat_interval', `repeat_days` = '$repeat_days', `notes` = '$notes' WHERE `shiftid` = '$shiftid'";
                    $result = mysqli_query($dbc, $query);
                    break;
            }
        } else {
            $query = "UPDATE `contacts_shifts` SET `contactid` = '$contactid', `security_level` = '$security_level', `clientid` = '$clientid', `startdate` = '$startdate', `enddate` = '$enddate', `starttime` = '$starttime', `endtime` = '$endtime', `availability` = '$availability', `break_starttime` = '$break_starttime', `break_endtime` = '$break_endtime', `dayoff_type` = '$dayoff_type', `repeat_type` = '$repeat_type', `repeat_interval` = '$repeat_interval', `repeat_days` = '$repeat_days', `notes` = '$notes' WHERE `shiftid` = '$shiftid'";
            $result = mysqli_query($dbc, $query);
        }
    }

    $query = $_GET;
    unset ($query['shiftid']);
    unset ($query['current_day']);
    if(isset($_GET['from_url'])) {
        echo '<script>window.location.replace("'.$_GET['from_url'].'");</script>';
    } else {
        echo '<script>window.location.replace("?'.http_build_query($query).'&shiftid='.$shiftid.'");</script>';
    }
} else if(isset($_POST['submit_import'])) {
    if(!empty($_FILES['shift_import_file']['tmp_name'])) {
        $file = $_FILES['shift_import_file']['tmp_name'];
        $handle = fopen($file, "r");
        $headers = fgetcsv($handle, 0, ",");

        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            $values = [];
            foreach($headers as $i => $col) {
                $values[filter_var($col,FILTER_SANITIZE_STRING)] = filter_var(htmlentities($row[$i]));
                if($col == 'contactid' && !empty($_POST['shift_import_staff'])) {
                    $values[filter_var($col,FILTER_SANITIZE_STRING)] = filter_var($_POST['shift_import_staff']);
                }
            }

            if(empty($values['shiftid'])) {
                mysqli_query($dbc, "INSERT INTO `contacts_shifts` VALUES ()");
                $values['shiftid'] = mysqli_insert_id($dbc);
            }
            $shiftid = $values['shiftid'];

            $updates = [];
            foreach($values as $field => $value) {
                $updates[] = "`$field`='$value'";
            }
            $sql = "UPDATE `contacts_shifts` SET ".implode(',',$updates)." WHERE `shiftid` = '$shiftid'";
            if(!mysqli_query($dbc, $sql)) {
                echo "Error on shiftid $shiftid: ".mysqli_error($dbc)."<br />\n";
                $error = true;
            }
        }
        fclose($handle);
        echo '<script type="text/javascript"> alert("'.($error ? 'Some rows had errors. Please review the notes and make any corrections to the data to upload the data.' : 'Successfully imported CSV file.').'"); </script>';
    }
}

$shiftid = '';
if ($_GET['shiftid'] == 'NEW') {
    $shiftid = '';
    $shift_heading = 'New Staff Shift';
} else if ($_GET['shiftid'] == 'IMPORT') {
    $shiftid = $_GET['shiftid'];
    $shift_heading = 'Import Staff Shifts';
} else {
    $shiftid = $_GET['shiftid'];
    $shift_heading = 'Edit Staff Shift';
}

$dayoff_types = '';
$enabled_fields = '';
$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"));
if (!empty($get_field_config)) {
    $dayoff_types = explode(',', $get_field_config['dayoff_types']);
    $enabled_fields = ','.$get_field_config['enabled_fields'].',';
    $contact_category = $get_field_config['contact_category'];
}

$contactid = '';
if(isset($_GET['shift_staffid'])) {
    $contactid = $_GET['shift_staffid'];
}
$security_level = '';
$clientid = '';
if(isset($_GET['shift_clientid'])) {
    $clientid = $_GET['shift_clientid'];
}
$startdate = '';
if(isset($_GET['shift_startdate'])) {
    $startdate = $_GET['shift_startdate'];
}
$enddate = '';
if(isset($_GET['shift_enddate'])) {
    $enddate = $_GET['shift_enddate'];
}
$starttime = '';
if(isset($_GET['shift_starttime'])) {
    $starttime = urldecode($_GET['shift_starttime']);
}
$endtime = '';
if(isset($_GET['shift_endtime'])) {
    $endtime = urldecode($_GET['shift_endtime']);
}
$availability = '';
$break_starttime = '';
$break_endtime = '';
$dayoff_type = '';
$repeat_type = '';
$repeat_interval = '';
$repeat_days = '';
$notes = '';
$hours_type = 'Regular Hrs.';
$set_hours = 0;
if(isset($_GET['set_hours'])) {
    $set_hours = $_GET['set_hours'];
}
if (!empty($shiftid)) {
    $get_shifts = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'"));

    $contactid = $get_shifts['contactid'];
    $security_level = $get_shifts['security_level'];
    $clientid = $get_shifts['clientid'];
    $startdate = $get_shifts['startdate'];
    $enddate = $get_shifts['enddate'];
    $starttime = $get_shifts['starttime'];
    $endtime = $get_shifts['endtime'];
    $availability = $get_shifts['availability'];
    $break_starttime = $get_shifts['break_starttime'];
    $break_endtime = $get_shifts['break_endtime'];
    $dayoff_type = $get_shifts['dayoff_type'];
    $repeat_type = $get_shifts['repeat_type'];
    $repeat_interval = $get_shifts['repeat_interval'];
    $repeat_days = ','.$get_shifts['repeat_days'].',';
    $hours_type = $get_shifts['hours_type'];
    $notes = $get_shifts['notes'];
    if (!empty($_GET['current_day'])) {
        $shift_current_date = date('Y-m-d', strtotime($_GET['current_day']));
        $recurring = date('Y-m-d', strtotime($startdate)) == date('Y-m-d', strtotime($enddate)) ? 'no' : 'yes';
    }
    $set_hours = $get_shifts['set_hours'];
}
?>
<script type="text/javascript">
$(document).ready(function() {
    window.submitForm = false;
    window.checkedConflicts = false;

    $('[name="submit"]').on('click', function() {
        if($('[name="old_startdate"]').val() == $('[name="shift_startdate"]').val() && $('[name="old_enddate"]').val() == $('[name="shift_enddate"]').val()) {
            return shiftEditDialog(window.submitForm);
        } else if(!checkedConflicts) {
            var conflicts = checkShiftConflicts();
            conflicts.success(function(response) {
                if(response == 1) {
                    if(confirm('There are conflicting Shifts. Are you sure you want to continue?')) {
                        window.checkedConflicts = true;
                        $('[name="submit"]').trigger('click');
                    } else {
                        window.submitForm=false;
                    }
                } else {
                    window.checkedConflicts = true;
                    $('[name="submit"]').trigger('click');
                }
            });
            return false;
        } else {
            return true;
        }
    });

    function shiftEditDialog(submitForm) {
        if(!submitForm && $('[name="recurring"]').val() == 'yes') {
            $( "#dialog-confirm" ).dialog({
                resizable: false,
                height: "auto",
                width: ($(window).width() <= 600 ? $(window).width() : 600),
                modal: true,
                buttons: {
                    "Only this shift": function() {
                        $('[name="edit_type"]').val('once');
                        window.submitForm=true;
                        $(this).dialog('close');
                        $('[name="submit"]').trigger('click');
                    },
                    "Following shifts": function() {
                        $('[name="edit_type"]').val('following');
                        window.submitForm=true;
                        $(this).dialog('close');
                        $('[name="submit"]').trigger('click');
                    },
                    "All shifts": function() {
                        $('[name="edit_type"]').val('all');
                        window.submitForm=true;
                        $(this).dialog('close');
                        $('[name="submit"]').trigger('click');
                    },
                    Cancel: function() {
                        window.submitForm=false;
                        $(this).dialog('close');
                    }
              }
            });
            return false;
        } else if(!checkedConflicts) {
            var conflicts = checkShiftConflicts();
            conflicts.success(function(response) {
                if(response == 1) {
                    if(confirm('There are conflicting Shifts. Are you sure you want to continue?')) {
                        window.checkedConflicts = true;
                        $('[name="submit"]').trigger('click');
                    } else {
                        window.submitForm=false;
                    }
                } else {
                    window.checkedConflicts = true;
                    $('[name="submit"]').trigger('click');
                }
            });
            return false;
        } else {
            return true;
        }
    }

    function checkShiftConflicts() {
        var shiftid = $('[name="shiftid"]').val();
        var contactid = $('[name="shift_contactid"]').val();
        var startdate = $('[name="shift_startdate"]').val();
        var enddate = $('[name="shift_enddate"]').val();
        var starttime = $('[name="shift_starttime"]').val();
        var endtime = $('[name="shift_endtime"]').val();
        var repeat_type = $('[name="shift_repeat_type"]').val();
        var repeat_days = $('[name="shift_repeat_days"]').val();
        var repeat_interval = $('[name="shift_repeat_interval"]').val();
        var conflicts = 0;
        if($('[name="recurring"]').val() == 'yes' && $('[name="edit_type"]').val() == 'once') {
            enddate = $('[name="shift_startdate"]').val();
        }
        return $.ajax({
            url: '../Calendar/calendar_ajax_all.php?fill=check_shift_conflicts',
            method: 'POST',
            data: { shiftid: shiftid, contactid: contactid, startdate: startdate, enddate: enddate, starttime: starttime, endtime: endtime, repeat_type, repeat_days, repeat_interval: repeat_interval }
        });
    }
});
$(document).on('change', 'select[name="shift_repeat_type"]', function() { changeDaysOfWeek(this); });
$(document).on('change', 'select[name="shift_availability"]', function() { changeShiftAvailability(); });
function shiftChange(shiftid) {
    <?php
        $query = $_GET;
        unset ($query['shiftid']);
    ?>
    if ($(shiftid).val() == 'NEW') {
        location.replace("?<?= http_build_query($query) ?>&shiftid=NEW");
    } else {
        location.replace("?<?= http_build_query($query) ?>&shiftid=" + $(shiftid).val());
    }
}
function shiftTypeChange(chk) {
    var type = chk.value;
    if(type == 'shift') {
        if($(chk).is(':checked')) {
            $('#shift_type_dayoff').prop('checked', false);
            $('#break_div').show();
            $('#dayoff_type_div').hide();
        } else {
            $('#break_div').hide();
            $('#dayoff_type_div').show();
        }
    } else {
        if($(chk).is(':checked')) {
            $('#shift_type_shift').prop('checked', false);
            $('#break_div').hide();
            $('#dayoff_type_div').show();
        } else {
            $('#break_div').show();
            $('#dayoff_type_div').hide();
        }
    }
    if($('[name="shift_type_check"]:checked').length == 0) {
        $('#shift_type_shift').prop('checked', true);
        $('#break_div').show();
        $('#dayoff_type_div').hide();
    }
}
function dayOffCheck(dayOffCheckbox) {
    if ($(dayOffCheckbox).is(":checked")) {
        $('[name="shift_dayoff_type"]').removeAttr("disabled");
    } else {
        $('[name="shift_dayoff_type"]').attr("disabled", "disabled");
    }
    $('[name="shift_dayoff_type"]').trigger("change.select2");
}
function enableBreaks(chk) {
    if ($(chk).is(':checked')) {
        $('.break_div').show();
    } else {
        $('.break_div').hide();
    }
}
function enableRepeats(chk) {
    if ($(chk).is(':checked')) {
        $('.repeat_div').show();
    } else {
        $('.repeat_div').hide();
    }
}
function changeDaysOfWeek(sel) {
    var days_of_week = '';
    var repeat_type = sel.value;
    $('.repeat_days').show();
    switch (repeat_type) {
        case 'weekly':
            return;
        case 'weekly_weekday':
            days_of_week = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
            break;
        case 'weekly_mwf':
            days_of_week = ['Monday','Wednesday','Friday'];
            break;
        case 'weekly_tt':
            days_of_week = ['Tuesday','Thursday'];
            break;
        case 'monthly':
        case 'daily':
            $('.repeat_days').hide();
            days_of_week = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            break;
    }
    $('[name="shift_repeat_days[]"]').each(function() {
        if (days_of_week.indexOf($(this).val()) != -1) {
            $(this).prop('checked',true);
        } else {
            $(this).prop('checked',false);
        }
    });
}
function deleteShift() {
    if($('[name="recurring"]').val() == 'yes' && $('[name="shift_current_date"]').val() != '') {
        $( "#dialog-confirm-delete" ).dialog({
            resizable: false,
            height: "auto",
            width: ($(window).width() <= 500 ? $(window).width() : 500),
            modal: true,
            buttons: {
                "Only this shift": function() {
                    ajaxDeleteShift('once', $('[name="shift_current_date"]').val());
                },
                "All recurring shifts": function() {
                    ajaxDeleteShift('all', '');
                },
                Cancel: function() {
                    $(this).dialog('close');
                }
          }
        });
    } else {
        $( "#dialog-confirm-delete2" ).dialog({
            resizable: false,
            height: "auto",
            width: ($(window).width() <= 500 ? $(window).width() : 500),
            modal: true,
            buttons: {
                "Confirm": function() {
                    ajaxDeleteShift('all', '');
                },
                Cancel: function() {
                    $(this).dialog('close');
                }
          }
        });
    }
}
function ajaxDeleteShift(shifts, current_day) {
    var shiftid = $('[name="shiftid"]').val();
    $.ajax({
        type: 'GET',
        url: '../Calendar/calendar_ajax_all.php?fill=delete_shift&shiftid='+shiftid+'&shifts='+shifts+'&current_day='+current_day,
        dataType: 'html',
        success: function(response) {
            window.location.href = "?<?php unset($page_query['shiftid']); echo http_build_query($page_query); $page_query['shiftid'] = $shiftid; ?>";
        }
    });
}
function exportShifts() {
    $.ajax({
        type: 'GET',
        url: '../Calendar/calendar_ajax_all.php?fill=export_shifts',
        dataType: 'html',
        success: function(response) {
            window.open(response, '_blank');
        }
    });
}
function downloadShiftCsv() {
    $.ajax({
        type: 'GET',
        url: '../Calendar/calendar_ajax_all.php?fill=export_shifts&empty=true',
        dataType: 'html',
        success: function(response) {
            window.open(response, '_blank');
        }
    });
}
function changeShiftAvailability() {
    var availability = $('[name="shift_availability"]').val();
    if(availability == 'Available Anytime') {
        $('[name="shift_starttime"]').val('12:00 AM');
        $('[name="shift_endtime"]').val('11:59 PM')
        $('[name="shift_starttime"]').prop('readonly', true);
        $('[name="shift_endtime"]').prop('readonly', true);
        $('[name="shift_starttime"]').css('pointer-events', 'none');
        $('[name="shift_endtime"]').css('pointer-events', 'none');
    } else {
        $('[name="shift_starttime"]').prop('readonly', false);
        $('[name="shift_endtime"]').prop('readonly', false);
        $('[name="shift_starttime"]').css('pointer-events', '');
        $('[name="shift_endtime"]').css('pointer-events', '');
    }
}
function createShiftFor(input) {
    if(input.value == 'security_level') {
        $('.security_level_div').show();
        $('.staff_div').hide();
    } else {
        $('.security_level_div').hide();
        $('.staff_div').show();
    }
}
</script>

<?php $lock_date = get_config($dbc, 'staff_schedule_lock_date'); ?>

<h3 class="gap-left"><?= $shift_heading ?></h3>

<div class="block-group" style="height: calc(100% - 4.5em); overflow-y: auto;">
    <div id="dialog-confirm" title="Edit Recurring Shift" style="display: none;">
        Would you like to update only this Shift, all recurring Shifts, or following recurring Shifts?
    </div>
    <div id="dialog-confirm-delete" title="Delete Shift" style="display: none;">
        Would you like to delete all recurring Shifts or just the Shift for <?= $shift_current_date ?>?
    </div>
    <div id="dialog-confirm-delete2" title="Delete Shift" style="display: none;">
        Are you sure you would like to delete this Shift<?= $recurring == 'yes' ? 'and all recurirng Shifts' : '' ?>?
    </div>
    <form name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form" id="shiftform">
        <?php if($shiftid != 'IMPORT') { ?>
            <?php if($set_hours > 0) { ?>
                <div class="notice double-gap-bottom popover-examples">
                    <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                    <div class="col-sm-11">
                        <span class="notice-name">NOTE:</span>
                        You are adding Set Hours. This type of Scheduling will affect your Time Sheets and will overwrite any Regular Hours with these Set Hours. Leave End Date blank to keep Set Hours ongoing.
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php } ?>
            <input type="hidden" name="set_hours" value="<?= $set_hours ?>">
            <input type="hidden" name="shiftid" value="<?= $shiftid ?>">
            <input type="hidden" name="recurring" value="<?= $recurring ?>">
            <input type="hidden" name="edit_type" value="">
            <input type="hidden" name="old_startdate" value="<?= empty($startdate) || $startdate == '0000-00-00' ? '' : date('Y-m-d', strtotime($startdate)) ?>">
            <input type="hidden" name="old_enddate" value="<?= empty($enddate) || $enddate == '0000-00-00' ? '' : date('Y-m-d', strtotime($enddate)) ?>">

            <?php if(!empty($shift_current_date)) { ?>
            <label for="shift_current_date" class="super-label">Current Date:
            <input type="text" name="shift_current_date" class="form-control datepicker" value="<?= $shift_current_date ?>" style="pointer-events: none;" readonly>
            </label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',security_level,') !== FALSE || !empty($security_level)) { ?>
                <label for="security_level" class="super-label">Create Shift For:</label>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <div class="pull-left"><input type="radio" id="create_shift_for" name="create_shift_for" value="staff"<?= empty($security_level) ? ' checked' : '' ?> onclick="createShiftFor(this)" style="position: relative; top: 0.3em;"></div>
                            <label for="dayoff_type" class="form-label pull-left pad-left pad-top">Staff</label>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <div class="pull-left"><input type="radio" id="create_shift_for" name="create_shift_for" value="security_level"<?= !empty($security_level) ? ' checked' : '' ?> onclick="createShiftFor(this)" style="position: relative; top: 0.3em;"></div>
                            <label for="dayoff_type" class="form-label pull-left pad-left pad-top">Security Level</label>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <div class="security_level_div" <?= empty($security_level) ? 'style="display:none;"' : '' ?>>
                    <label for="contactid" class="super-label">Security Level:
                    <select data-placeholder="Select Security Level" name="shift_security_level" class="chosen-select-deselect">
                        <option></option>
                        <?php $on_security = get_security_levels($dbc);
                        foreach($on_security as $security_label => $security_value) {
                            echo '<option value="'.$security_value.'"'.($security_value == $security_level ? ' selected' : '').'>'.$security_label.'</option>';
                        }
                        ?>
                    </select></label>
                </div>
            <?php } ?>

            <div class="staff_div" <?= !empty($security_level) ? 'style="display:none;"' : '' ?>>
                <label for="contactid" class="super-label">Staff:
                <select data-placeholder="Select Staff" name="shift_contactid" class="chosen-select-deselect">
                    <option></option>
                    <?php
                        $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1"),MYSQLI_ASSOC));
                        foreach ($query as $id) {
                            echo '<option value="'.$id.'"'.($id == $contactid ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
                        }
                    ?>
                </select></label>
            </div>

            <?php if (!empty($contact_category)) { ?>
                <label for="clientid" class="super-label"><?= $contact_category ?>:
                <select data-palceholder="Select <?= $contact_category ?>" name="shift_clientid" class="chosen-select-deselect">
                    <option></option>
                    <?php
                        $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = '".$contact_category."' AND `deleted` = 0 AND `status` = 1"),MYSQLI_ASSOC));
                        foreach ($query as $id) {
                            echo '<option value="'.$id.'"'.($id == $clientid ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
                        }
                    ?>
                </select>
                </label>
            <?php } ?>
            <hr>

            <?php if (strpos($enabled_fields, ',dates,') !== FALSE) {
        		$lock_date = get_config($dbc, 'staff_schedule_lock_date'); ?>
                <div class="form-group">
                    <div class="pull-left" style="width:25px;"><img src="../img/month-overview-blue.png" alt="Date" width="18" /></div>
                    <label class="pull-left" style="width:50px;">Date:</label>
                    <div class="pull-left"><input type="text" placeholder="Start Date" name="shift_startdate" data-min-date="<?= $lock_date ?>" class="form-control datepicker" value="<?= empty($startdate) || $startdate == '0000-00-00' ? '' : date('Y-m-d', strtotime($startdate)) ?>" onchange="$('[name=shift_enddate]').val($(this).val());"></div>
                    <div class="pull-left pad-left pad-right"> - </div>
                    <div class="pull-left"><input type="text" placeholder="End Date" name="shift_enddate" data-min-date="<?= $lock_date ?>" class="form-control datepicker" value="<?= empty($enddate) || $enddate == '0000-00-00' ? '' : date('Y-m-d', strtotime($enddate)) ?>"></div>
                </div>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',availability,') !== FALSE) { ?>
                <label class="form-label pull-left">Availability</label>
                <label for="availability" class="super-label pull-left pad-left">Availability:
                <select name="shift_availability" data-placeholder="Select an Availability..." class="chosen-select-deselect form-control">
                    <option <?= empty($availability) || $availability == 'Available On Scheduled Hours' ? 'selected' : '' ?> value="Available On Scheduled Hours">Available On Scheduled Hours</option>
                    <option <?= $availability == 'Available Anytime' ? 'selected' : '' ?> value="Available Anytime">Available Anytime</option>
                    <option <?= $availability == 'Call Before Booking' ? 'selected' : '' ?> value="Call Before Booking">Call Before Booking</option>
                </select>
                </label>
                <div class="clearfix"></div>
                <hr class="offset-bottom-5">
            <?php } ?>

            <?php if (strpos($enabled_fields, ',time,') !== FALSE) { ?>
                <div class="form-group">
                    <div class="pull-left" style="width:25px;"><img src="../img/icons/ROOK-timer-icon.png" alt="Time" width="20" /></div>
                    <label class="pull-left" style="width:50px;">Time:</label>
                    <div class="pull-left"><input type="text" placeholder="Start Time" name="shift_starttime" class="form-control datetimepicker" value="<?= $availability == 'Available Anytime' ? '12:00 AM' : (empty($starttime) ? '' : date('h:i a', strtotime($starttime))) ?>" <?= $availability == 'Available Anytime' ? 'readonly' : '' ?>></div>
                    <div class="pull-left pad-left pad-right"> - </div>
                    <div class="pull-left"><input type="text" placeholder="End Time" name="shift_endtime" class="form-control datetimepicker" value="<?= $availability == 'Available Anytime' ? '11:59 PM' : (empty($endtime) ? '' : date('h:i a', strtotime($endtime))) ?>" <?= $availability == 'Available Anytime' ? 'readonly' : '' ?>></div>
                    <div class="clearfix"></div>
                    <hr class="offset-bottom-5">
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <div class="pull-left"><input type="checkbox" id="shift_type_shift" name="shift_type_check" value="shift"<?= empty($dayoff_type) ? ' checked' : '' ?> onclick="shiftTypeChange(this)" style="transform: scale(1.5); position: relative; top: 0.2em;"></div>
                        <label for="dayoff_type" class="form-label pull-left pad-left">Shift</label>
                        <div class="clearfix"></div>
                    </div>
                </div>
                
                <?php if (strpos($enabled_fields, ',dayoff_type,') !== FALSE) { ?>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <div class="pull-left"><input type="checkbox" id="shift_type_dayoff" name="shift_type_check" value="dayoff"<?= !empty($dayoff_type) ? ' checked' : '' ?> onclick="shiftTypeChange(this)"  style="transform: scale(1.5); position: relative; top: 0.2em;"></div>
                            <label for="dayoff_type" class="form-label pull-left pad-left">Day Off</label>
                            <div id="dayoff_type_div" class="pull-left pad-left" <?= (empty($dayoff_type) ? 'style="display:none;"' : '' ) ?>>
                                <select data-placeholder="Select Day Off Type" name="shift_dayoff_type" class="chosen-select-deselect">
                                    <option></option>
                                    <?php
                                        foreach ($dayoff_types as $type) {
                                            echo '<option value="'.$type.'"'.($type == $dayoff_type ? ' selected' : '').'>'.$type.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <?php } ?>
                <div class="clearfix"></div>
                <hr class="offset-top-5">
            </div>

            <?php if (strpos($enabled_fields, ',repeat_days,') !== FALSE) { ?>
                <div class="form-group">
                    <div class="pull-left offset-left-5"><input type="checkbox" name="repeat_check" value="1"<?= !empty($repeat_type) ? ' checked' : '' ?> onclick="enableRepeats(this);" style="transform: scale(1.5); position: relative; top: 0.2em;"></div>
                    <label class="form-label pull-left pad-left">Repeat</label>
                    <div class="repeat_div pull-left pad-left" <?= !empty($repeat_type) ? '' : 'style="display: none;"' ?>>
                        <div class="row">
                            <div class="col-xs-6">
                                <label for="repeat_type" class="super-label">Repeats:
                                    <select data-placeholder="Select Type" name="shift_repeat_type" class="chosen-select-deselect">
                                        <option <?= $repeat_type == 'daily' ? 'selected' : '' ?> value="daily">Daily</option>
                                        <option <?= $repeat_type == 'weekly' || $repeat_type == '' ? 'selected' : '' ?> value="weekly">Weekly</option>
                                        <option value="weekly_weekday">Every Weekday (Monday to Friday)</option>
                                        <option value="weekly_mwf">Every Monday, Wednesday, and Friday</option>
                                        <option value="weekly_tt">Every Tuesday and Thursday</option>
                                        <option <?= $repeat_type == 'monthly' ? 'selected' : '' ?> value="monthly">Monthly</option>
                                    </select>
                                </label>
                            </div>
                            <div class="pad-left col-xs-6">
                                <label for="repeat_interval" class="super-label">Repeat Interval:
                                    <select data-placeholder="Select Week Interval" name="shift_repeat_interval" class="chosen-select-deselect">
                                        <?php for ($shift_i = 1; $shift_i <= 30; $shift_i++) {
                                            echo '<option '.($repeat_interval == $shift_i ? 'selected' : '').' value="'.$shift_i.'">'.$shift_i.'</option>';
                                        } ?>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <div class="clearifx"></div>
                        <label for="repeat_days" class="super-label repeat_days" <?= $repeat_type == 'weekly' || $repeat_type == '' ? '' : 'style="display: none;"' ?>>Repeat Days:<br />
                            <?php
                                $days_of_week = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

                                foreach ($days_of_week as $day_of_week_shift) {
                                    echo '<label style="padding-right: 0.5em; "><input type="checkbox" name="shift_repeat_days[]" value="'.$day_of_week_shift.'" '.(strpos($repeat_days, ','.$day_of_week_shift.',') !== FALSE ? 'checked' : '').'>'.$day_of_week_shift.'</label>';
                                }
                            ?>
                        </label>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="offset-bottom-5">
                </div>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',hours_type,') !== FALSE) { ?>
                <div class="form-group">
                    <label for="hours_type" class="super-label">Shift Type:
                        <select data-placeholder="Select Type" name="hours_type" class="chosen-select-deselect">
                            <option <?= $hours_type == 'Regular Hrs.' || $hours_type == '' ? 'selected' : '' ?> value="daily">Regular</option>
                            <option <?= $hours_type == 'Extra Hrs.' ? 'selected' : '' ?> value="weekly">Extra</option>
                            <option <?= $hours_type == 'Relief Hrs.' ? 'selected' : '' ?> value="weekly">Relief</option>
                        </select>
                    </label>
                    <hr class="offset-bottom-5">
                </div>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',breaks,') !== FALSE) { ?>
                <div class="form-group">
                    <div id="break_div" <?= (!empty($dayoff_type) ? 'style="display:none;"' : '') ?>>
                        <div class="pull-left offset-left-5"><input type="checkbox" name="break_check" value="1"<?= !empty($break_starttime) || !empty($break_endtime) ? ' checked' : '' ?> onclick="enableBreaks(this);" style="transform: scale(1.5); position: relative; top: 0.2em;"></div>
                        <label class="form-label pull-left pad-left">Breaks</label>

                        <div class="break_div pull-left pad-left" style="<?= !empty($break_starttime) || !empty($break_endtime) ? '': 'display: none;' ?>">
                            <div class="pull-left" style="width:25px;"><img src="../img/icons/ROOK-timer-icon.png" alt="Time" width="20" /></div>
                            <div class="pull-left"><input type="text" placeholder="Start Time" name="shift_break_starttime" class="form-control datetimepicker" value="<?= $break_starttime ?>"></div>
                            <div class="pull-left pad-left pad-right"> - </div>
                            <div class="pull-left"><input type="text" placeholder="End Time" name="shift_break_endtime" class="form-control datetimepicker" value="<?= $break_endtime ?>"></div>
                        </div>
                        
                        <div class="clearfix"></div>
                        <hr class="offset-bottom-5">
                    </div>
                </div>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',notes,') !== FALSE) { ?>
                <label for="notes" class="super-label">Description:
                <textarea name="shift_notes" class="form-control noMceEditor"><?= html_entity_decode($notes) ?></textarea></label>
            <?php } ?>

            <div class="pull-right" style="padding-top: 1em;">
                <?php if($recurring == 'yes' && ($startdate >= $lock_date || $startdate == '')) { ?>
                    <button type="submit" name="submit" value="calendar_shifts" class="btn brand-btn">Submit</button>
                <?php } else if($startdate >= $lock_date || $startdate == '') { ?>
                    <button type="submit" name="submit" value="calendar_shifts" class="btn brand-btn">Submit</button>
                <?php } ?>
                <?php
                    unset($page_query['teamid']);
                    unset($page_query['subtab']);
                    unset($page_query['unbooked']);
                    unset($page_query['equipment_assignmentid']);
                    unset($page_query['shiftid']);
                    unset($page_query['action']);
                    unset($page_query['bookingid']);
                    unset($page_query['appoint_date']);
                    unset($page_query['end_appoint_date']);
                    unset($page_query['therapistsid']);
                    unset($page_query['equipmentid']);
                    unset($page_query['add_reminder']);
                ?>
                <a href="?<?= http_build_query($page_query) ?>" class="btn brand-btn mobile-anchor">Cancel</a>
                <?php if(!empty($shiftid) && $startdate >= $lock_date) { ?>
                    <a href="#" onclick="deleteShift(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png"></a>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="notice">Steps to Import Shifts:<br><Br>
                <b>1.</b> <a href="" onclick="downloadShiftCsv(); return false;">Click here to download the Import Template.</a><br>
                <span style='color:pink;'><img src='../img/warning.png' style='width:25px;'> NOTE</span>: Do not change any of the column titles in the first row, or else the edits may not go through properly. The software will determine what type of Import you are doing as long as the column titles are not changed.<br><span style='color:lightgreen'><b>Hint:</b></span> press CTRL+F on your keyboard to find the fields you would like to populate; this will help you locate them faster.<br><br>
                <b>2.</b> To add new shifts, leave the first column (shiftid) blank.<br><br>
                <b>3.</b> To specify a staff in the spreadsheet, enter the employee id in the second column (contactid). Otherwise, select a Staff when you are uploading the file.<br><br>
                <b>4.</b> Please use the following format for columns in your Spreadsheet so your shirts insert properly:<br>
                <ul>
                    <li><b>startdate</b>: Enter the date with the following format: YYYY-MM-DD (eg. 2017-12-01)</li>
                    <li><b>enddate</b>: Enter the date with the following format: YYYY-MM-DD (eg. 2017-12-31)</li>
                    <li><b>starttime</b>: Enter the time with the following format: HH:MM AM/PM (eg. 6:30 AM)</li>
                    <li><b>endtime</b>: Enter the date with the following format: HH:MM AM/PM (eg. 12:30 PM)</li>
                    <li><b>repeat_days</b>: Enter the full day of the week separated by commas. This will make it so if it hits one of these days between your Shift startdate and enddate, it will be counted as a shift. (eg. Monday,Tuesday,Wednesday,Thursday,Friday)</li>
                    <li><b>repeat_type</b>: Enter only one of the following options: daily, weekly, or monthly (eg. weekly)</li>
                    <li><b>repeat_interval</b>: Enter a number value for the how often you want the repeat_type to be repeated. For example, if your repeat_type is daily and you have an interval of 3, it will repeat every 3 days (eg. shift first day, no shift second day, no shift third day, shift fourth day, etc.). If your repeat_type is weekly and you have an interval or 2, it will repeat every 2 weeks (eg. shift first week, no shift second week, shift third week, etc.).</li>
                    <li><b>break_starttime</b>: Leave this blank if there are no breaks. Enter the time with the following format: HH:MM AM/PM (eg. 6:30 AM)</li>
                    <li><b>break_endtime</b>: Leave this blank if there are no breaks. Enter the date with the following format: HH:MM AM/PM (eg. 12:30 PM)</li>
                </ul>
                <b>5.</b> After you are done editing the data, save your Excel (CSV) file, upload the CSV file below, and hit submit.<br><br>
            </div>
            <div class="form-group">
                <label class="col-sm-4">File:</label>
                <div class="col-sm-8">
                    <input class="form-control" type="file" name="shift_import_file" /><br />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Staff:<br><i>(NOTE: All shifts imported will be attached to this Staff if a Staff is selected.)</i></label>
                <div class="col-sm-8">
                    <select name="shift_import_staff" class="chosen-select-deselect form-control">
                        <option></option>
                        <?php
                            $shift_contactid = $_GET['shift_contactid'];
                            $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1"),MYSQLI_ASSOC));
                            foreach ($query as $id) {
                                echo '<option value="'.$id.'"'.($id == $shift_contactid ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row double-padded pull-right">
                <button type="submit" name="submit_import" value="submit_import" class="btn brand-btn">Submit</button>
                <a href="?<?= http_build_query($page_query) ?>" class="btn brand-btn mobile-anchor">Cancel</a>
            </div>
        <?php } ?>
    </form>
</div>