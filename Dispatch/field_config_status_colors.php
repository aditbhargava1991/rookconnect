<?php
/*
Dispatch Field Config - Tile Settings
*/
include ('../include.php');
checkAuthorised('dispatch'); ?>

<script type="text/javascript">
$(document).ready(function() {
    $('[name="status_color[]"]').change(save_colors);
});
function save_colors() {
    var color_block = $(this).closest('.color_block');
    var status = $(color_block).find('[name="ticket_status[]"]').val();
    var color = $(color_block).find('[name="status_color[]"]').val();
    $.ajax({
        url: '../Dispatch/ajax.php?action=ticket_status_color',
        method: 'POST',
        data: { status: status, color: color },
        success: function(response) {
            console.log(response);
        }
    });
}
function color_code_change(sel) {
    $(sel).closest('.color_block').find('.color_hex').val(sel.value).change();
}
</script>

<?php $ticket_statuses = explode(',', get_config($dbc, 'ticket_status'));
foreach ($ticket_statuses as $ticket_status) {
    $color_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color` WHERE `status` = '$ticket_status'")); ?>
    <div class="form-group color_block">
        <input type="hidden" name="ticket_status[]" value="<?= $ticket_status ?>">
        <label class="col-sm-4 control-label"><?= $ticket_status ?>:</label>
        <div class="col-sm-1">
            <input onchange="color_code_change(this);" class="form-control" type="color" name="status_color_picker[]" value="<?= !empty($color_config['color']) ? $color_config['color'] : '#aaaaaa' ?>">
        </div>
        <div class="col-sm-7">
            <input type="text" name="status_color[]" class="form-control color_hex" value="<?= !empty($color_config['color']) ? $color_config['color'] : '#aaaaaa' ?>">
        </div>
    </div>
<?php } ?>