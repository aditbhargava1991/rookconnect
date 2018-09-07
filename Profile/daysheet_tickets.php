<!-- Daysheet My Tickets-->
<script>
var ajax_loads = [];
$(document).ready(function() {
    setActions();
    highlightHigherLevels();
    $('.search_list').keyup(function() {
        if(current_ticket_search_key != this.value.toLowerCase()) {
            loadTickets();
        }
    });
    $('[data-type] a').not('.cursor-hand').click(function() {
        var tab = $(this).closest('[data-type]').data('type');
        loadNote(tab);
        $('[data-type]').not('[data-type="'+tab+'"]').find('.active.blue').removeClass('active').removeClass('blue');
        highlightHigherLevels();
    });
});
var ticket_list = [];
var current_ticket_search_key = '';
var search_option_id = 0;
function highlightHigherLevels() {
    $('.cursor-hand').removeClass('active blue');
    $('.sidebar-higher-level.highest-level').each(function() {
        var active_li = false;
        $(this).find('.sidebar-higher-level').each(function() {
            if($(this).find('li.active').length > 0) {
                $(this).find('.cursor-hand').addClass('active blue');
                active_li = true;
            }
        });
        if(active_li) {
            $(this).find('.cursor-hand').first().addClass('active blue');
        }
    });
}
function remForm(form, ticket, rev, div) {
    if(confirm('Are you sure you want to remove this form?')) {
        $(div).remove();
        $.post('ticket_ajax_all.php?action=removePdfForm',{formid:form,ticket:ticket,revision:rev});
    }
}
function setActions() {
    $('.archive-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item');
        $.ajax({
            url: 'ticket_ajax_all.php?action=archive',
            method: 'POST',
            data: { ticketid: item.data('id') }
        });
        item.hide();
    });
    $('.manual-flag-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item');
        item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').show();
        item.find('[name=flag_cancel]').off('click').click(function() {
            item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').hide();
            return false;
        });
        item.find('[name=flag_off]').off('click').click(function() {
            item.find('[name=colour]').val('FFFFFF');
            item.find('[name=label]').val('');
            item.find('[name=flag_start]').val('');
            item.find('[name=flag_end]').val('');
            item.find('[name=flag_it]').click();
            return false;
        });
        item.find('[name=flag_it]').off('click').click(function() {
            $.ajax({
                url: 'ticket_ajax_all.php?action=quick_actions',
                method: 'POST',
                data: {
                    field: 'manual_flag_colour',
                    value: item.find('[name=colour]').val(),
                    table: item.data('table'),
                    label: item.find('[name=label]').val(),
                    start: item.find('[name=flag_start]').val(),
                    end: item.find('[name=flag_end]').val(),
                    id: item.data('id'),
                    id_field: item.data('id-field')
                }
            });
            item.find('.flag_field_labels,[name=label],[name=colour],[name=flag_it],[name=flag_cancel],[name=flag_off],[name=flag_start],[name=flag_end]').hide();
            item.data('colour',item.find('[name=colour]').val());
            item.css('background-color','#'+item.find('[name=colour]').val());
            item.find('.flag-label').text(item.find('[name=label]').val());
            return false;
        });
    });
    $('.flag-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item');
        $.ajax({
            url: '../Ticket/ticket_ajax_all.php?action=quick_actions',
            method: 'POST',
            data: {
                field: 'flag_colour',
                value: item.data('colour'),
                table: item.data('table'),
                id: item.data('id'),
                id_field: item.data('id-field')
            },
            success: function(response) {
                item.data('colour',response.substr(0,6));
                item.css('background-color','#'+response.substr(0,6));
                item.find('.flag-label').html(response.substr(6));
            }
        });
    });
    $('.attach-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item');
        item.find('[type=file]').off('change').change(function() {
            var fileData = new FormData();
            fileData.append('file',$(this)[0].files[0]);
            fileData.append('field','document');
            fileData.append('table','ticket_document');
            fileData.append('folder','download');
            fileData.append('id',item.data('id'));
            fileData.append('id_field','ticketid');
            $.ajax({
                contentType: false,
                processData: false,
                method: "POST",
                url: "ticket_ajax_all.php?action=quick_actions",
                data: fileData
            });
        }).click();
    });
    $('.reply-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=tickets&id='+item.data('id'), 'auto', false, true);
    });

    $('.emailpdf-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item');
        item.find('[name=emailpdf]').off('change').off('blur').show().focus().blur(function() {
            $(this).off('blur');
            $.ajax({
                url: '../Ticket/ticket_ajax_all.php?action=quick_actions',
                method: 'POST',
                data: {
                        id: item.data('id'),
                        id_field: item.data('id-field'),
                        table: item.data('table'),
                        field: 'emailpdf',
                        value: this.value,
                    },
                success: function(response) {
                            alert('PDF Sent');
                }
                });
            $(this).hide().val('');
        }).keyup(function(e) {
            if(e.which == 13) {
                $(this).blur();
            } else if(e.which == 27) {
                $(this).off('blur').hide();
            }
        });
    });

    $('.reminder-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item');
        item.find('[name=reminder]').change(function() {
            var reminder = $(this).val();
            var select = item.find('.select_users');
            select.find('.cancel_button').off('click').click(function() {
                select.find('select option:selected').removeAttr('selected');
                select.find('select').trigger('change.select2');
                select.hide();
                return false;
            });
            select.find('.submit_button').off('click').click(function() {
                if(select.find('select').val() != '' && confirm('Are you sure you want to schedule reminders for the selected user(s)?')) {
                    var users = [];
                    select.find('select option:selected').each(function() {
                        users.push(this.value);
                        $(this).removeAttr('selected');
                    });
                    $.ajax({
                        method: 'POST',
                        url: '../Ticket/ticket_ajax_all.php?action=quick_actions',
                        data: {
                            id: item.data('id'),
                            id_field: item.data('id-field'),
                            table: item.data('table'),
                            field: 'reminder',
                            value: reminder,
                            users: users,
                            ref_id: item.data('id'),
                            ref_id_field: item.data('id-field')
                        },
                        success: function(result) {
                            select.hide();
                            select.find('select').trigger('change.select2');
                            item.find('h4').append(result);
                        }
                    });
                }
                return false;
            });
            select.show();
        }).focus();
    });
    $('.alert-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item');
        var select = item.find('.select_users');
        $(this).data('users').split(',').forEach(function(user) {
            if(user > 0) {
                select.find('option[value='+user+']').attr('selected',true);
            }
        });
        select.find('.cancel_button').off('click').click(function() {
            select.find('option:selected').removeAttr('selected');
            select.find('select').trigger('change.select2');
            select.hide();
            return false;
        });
        select.find('.submit_button').off('click').click(function() {
            if(select.find('select').val() != '' && confirm('Are you sure you want to activate alerts for the selected user(s)?')) {
                var users = [];
                select.find('select option:selected').each(function() {
                    users.push(this.value);
                    $(this).removeAttr('selected');
                });
                $.ajax({
                    method: 'POST',
                    url: '../Ticket/ticket_ajax_all.php?action=quick_actions',
                    data: {
                        id: item.data('id'),
                        id_field: item.data('id-field'),
                        table: item.data('table'),
                        field: 'alert',
                        value: users
                    },
                    success: function(result) {
                        select.hide();
                        item.find('h4').append(result);
                    }
                });
            }
            return false;
        });
        select.find('select').trigger('change.select2');
        select.show();
    });
    $('.email-icon').off('click').click(function() {
        var item = $(this).closest('.dashboard-item');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=tickets&id='+item.data('id'), 'auto', false, true);
    });
}
function setStatus(select) {
    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "../Ticket/ticket_ajax_all.php?fill=update_ticket_status&ticketid="+$(select).data('id')+'&status='+select.value,
        dataType: "html",   //expect html to be returned
        success: function(response){
            if(status == 'Archive') {
                $(sel).closest('tr').hide();
            }
        }
    });
}

function setMilestoneTimeline(select) {
    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "../Ticket/ticket_ajax_all.php?fill=update_ticket_mt&ticketid="+$(select).data('id')+'&mt='+select.value,
        dataType: "html",   //expect html to be returned
        success: function(response){

        }
    });
}


function setTotalBudgetTime(input) {
    $.ajax({
        type: "POST",
        url: "../Ticket/ticket_ajax_all.php?action=update_ticket_total_budget_time",
        data: { ticketid: $(input).data('id'), time: $(input).val() },
        dataType: "html",
        success: function(response){
            if(response != '') {
                $(input).closest('.dashboard-item').find('.total_budget_time_icon').attr('title', response).show();
            } else {
                $(input).closest('.dashboard-item').find('.total_budget_time_icon').attr('title', response).hide();
            }
        }
    });
}
</script>
<?php
/* Start Pagination Counting */
$rowsPerPage = 10;
$pageNum = 1;

if(isset($_GET['page'])) {
    $pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

$contactid = $_SESSION['contactid'];
$search_start_date = date('Y-m-01');
$search_end_date = date('Y-m-t');

$_SERVER['QUERY_STRING'] = explode('&', $_SERVER['QUERY_STRING']);
foreach($_SERVER['QUERY_STRING'] as $key => $query_string) {
    if(strpos($query_string,'search_start_date') !== FALSE || strpos($query_string,'search_end_date') !== FALSE || strpos($query_string,'page') !== FALSE) {
        unset($_SERVER['QUERY_STRING'][$key]);
    }
}
if(!empty($_POST['search_start_date'])) {
    $_GET['search_start_date'] = $_POST['search_start_date'];
}
if(!empty($_POST['search_end_date'])) {
    $_GET['search_end_date'] = $_POST['search_end_date'];
}
if(!empty($_GET['search_start_date'])) {
    $search_start_date = $_GET['search_start_date'];
    $_SERVER['QUERY_STRING'][] = 'search_start_date='.$_GET['search_start_date'];
}
if(!empty($_GET['search_end_date'])) {
    $search_end_date = $_GET['search_end_date'];
    $_SERVER['QUERY_STRING'][] = 'search_end_date='.$_GET['search_end_date'];
}
$_SERVER['QUERY_STRING'] = implode('&', $_SERVER['QUERY_STRING']);

$equipment = [];
for($cur_day = $search_start_date; strtotime($cur_day) <= strtotime($search_end_date); $cur_day = date('Y-m-d', strtotime($cur_day.' + 1 day'))) {
    $equipment_ids = mysqli_query($dbc, "SELECT `equipmentid` FROM `equipment_assignment_staff` LEFT JOIN `equipment_assignment` ON `equipment_assignment_staff`.`equipment_assignmentid`=`equipment_assignment`.`equipment_assignmentid` WHERE `equipment_assignment_staff`.`deleted`=0 AND `equipment_assignment`.`deleted`=0 AND `equipment_assignment_staff`.`contactid`='$contactid' AND DATE(`equipment_assignment`.`start_date`) <= '$cur_day' AND DATE(`equipment_assignment`.`end_date`) >= '$cur_day' AND CONCAT(',',`hide_staff`,',') NOT LIKE '%,$contactid,%' AND CONCAT(',',`hide_days`,',') NOT LIKE '%,$cur_day,%'");
    while($equipment[] = mysqli_fetch_assoc($equipment_ids)['equipmentid']) { }
}
$equipment = implode(',',array_filter(array_unique($equipment)));
$equipment_query = '';
if($equipment != '') {
    $equipment_query = " OR `equipment` IN ($equipment)";
}

$tickets_list = "SELECT * FROM `tickets` WHERE `deleted` = 0 AND `status` NOT IN ('Archive','Done') AND ((`internal_qa_date` BETWEEN '$search_start_date' AND '$search_end_date' AND CONCAT(',',`internal_qa_contactid`,',') LIKE '%,$contactid,%') OR (`deliverable_date` BETWEEN '$search_start_date' AND '$search_end_date' AND CONCAT(',',`deliverable_contactid`,',' LIKE '%,$contactid,%') OR ((`to_do_date` BETWEEN '$search_start_date' AND '$search_end_date' OR IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) BETWEEN '$search_start_date' AND '$search_end_date') AND CONCAT(',',`contactid`,',') LIKE '%,$contactid,%') $equipment_query OR `ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `deleted` = 0 AND ((`to_do_date` BETWEEN '$search_start_date' AND '$search_end_date' OR IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) BETWEEN '$search_start_date' AND '$search_end_date') AND CONCAT(',',`contactid`,',') LIKE '%,$contactid,%') $equipment_query))) LIMIT $offset, $rowsPerPage";
$query = "SELECT COUNT(`ticketid`) as `numrows` FROM `tickets` WHERE `deleted` = 0 AND `status` NOT IN ('Archive','Done') AND ((`internal_qa_date` BETWEEN '$search_start_date' AND '$search_end_date' AND CONCAT(',',`internal_qa_contactid`,',') LIKE '%,$contactid,%') OR (`deliverable_date` BETWEEN '$search_start_date' AND '$search_end_date' AND CONCAT(',',`deliverable_contactid`,',' LIKE '%,$contactid,%') OR ((`to_do_date` BETWEEN '$search_start_date' AND '$search_end_date' OR IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) BETWEEN '$search_start_date' AND '$search_end_date') AND CONCAT(',',`contactid`,',') LIKE '%,$contactid,%') $equipment_query OR `ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `deleted` = 0 AND ((`to_do_date` BETWEEN '$search_start_date' AND '$search_end_date' OR IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) BETWEEN '$search_start_date' AND '$search_end_date') AND CONCAT(',',`contactid`,',') LIKE '%,$contactid,%') $equipment_query)))";

$result = mysqli_query($dbc, $tickets_list);

$num_rows = mysqli_num_rows($result);
?>
<div class="col-xs-12">
    <div class="weekly-div" style="overflow-y: hidden;">
        <form action="" method="POST">
            <div class="form-group">
                <label class="col-sm-2">Start Date:</label>
                <div class="col-sm-3"><input type="text" name="search_start_date" class="form-control datepicker" style="background-color: white;" value="<?= $search_start_date ?>"></div>
                <label class="col-sm-2">End Date:</label>
                <div class="col-sm-3"><input type="text" name="search_end_date" class="form-control datepicker" style="background-color: white;" value="<?= $search_end_date ?>"></div>
                <button type="submit" name="search_tickets" class="btn brand-btn">Submit</button>
            </div>
        </form>
        <?php if($num_rows > 0) {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            while($row = mysqli_fetch_array( $result )) {
                $_GET['ticketid'] = $row['ticketid'];
                $no_ob_clean = true;
                include('../Ticket/ticket_load.php');
            }
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        } else {
            echo "<h2>No Record Found.</h2>";
        } ?>
    </div>
</div>