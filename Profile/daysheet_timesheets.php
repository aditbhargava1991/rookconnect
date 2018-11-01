<!-- Daysheet My Time Sheets -->
<?php
include('../include.php');
include_once('../Calendar/calendar_functions_inc.php');
include '../Timesheet/config.php';
$value = $config['settings']['Choose Fields for Time Sheets Dashboard'];
?>
<script type="text/javascript" src="../Timesheet/timesheet.js"></script>
<script type="text/javascript">
function viewTicket(a) {
    var ticketid = $(a).data('ticketid');
    overlayIFrameSlider('<?= WEBSITE_URL ?>/Ticket/edit_tickets.php?edit='+ticketid+'&calendar_view=true','auto',false,true, $('#timesheet_div').outerHeight());
}
function addSignature(chk) {
    var td = $(chk).closest('td');
    var contactid = $('[name="staff_id"]').val();
    var date = chk.value;
    $('[name="time_cards_signature"]').closest('.sigPad').find('a[href="#clear"]').click();
    $(chk).prop('checked', false);
    $('#dialog-signature').dialog({
        resizable: false,
        height: "auto",
        width: ($(window).width() <= 500 ? $(window).width() : 500),
        modal: true,
        buttons: {
            "Submit": function() {
                $(this).dialog('close');
                $(td).html('Generating signature...');
                var signature = $('[name="time_cards_signature"]').val();
                $.ajax({
                    url: '../Timesheet/time_cards_ajax.php?action=add_signature',
                    method: 'POST',
                    data: { contactid: contactid, date: date, signature: signature },
                    success: function(response) {
                        var img = '<img src="../Timesheet/download/'+response+'" style="height: 50%; width: auto;">';
                        $(td).html(img);
                    }
                });
            },
            Cancel: function() {
                $(this).dialog('close');
            }
        }
    });
}
</script>

<div id="timesheet_div">
    <div id="dialog-pdf-options" title="Select PDF Fields" style="display: none;">
        <?php echo get_pdf_options($dbc); ?>
    </div>
    <div id="dialog-signature" title="Signature Box" style="display: none;">
        <?php $output_name = 'time_cards_signature';
        include('../phpsign/sign_multiple.php'); ?>
    </div>
    <div class="row timesheet_div">
        <input type="hidden" name="timesheet_time_format" value="<?= get_config($dbc, 'timesheet_time_format') ?>">
        <?php $_GET['tab'] = '';
        include('../Timesheet/time_cards_content.php');
        $_GET['tab'] = 'timesheets'; ?>
    </div>
</div>