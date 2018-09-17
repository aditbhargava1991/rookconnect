<?php
/*
Payment/Invoice Listing
*/
include_once ('../include.php');
include_once('../Ticket/config.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
?>
<script type="text/javascript" src="../Invoice/invoice.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').height() - $('.tile-header').height() - $('.standard-body-title').height() - 5;
        if(available_height > 200) {
            $('#invoice_div .standard-body').height(available_height);
        }
    }).resize();
});
</script>
<div class="standard-body-title">
    <h3>Unbilled <?= TICKET_TILE ?></h3>
</div>

<div class="standard-body-content">
    <div class="pad-10">
        <?php $_GET['status'] = 'unbilled';
        include('../Ticket/ticket_invoice.php'); ?>
    </div>
</div><!-- .standard-body-content -->