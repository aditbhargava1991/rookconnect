<?php include_once('../include.php');
include_once('../Sales/config.php');
if(empty($salesid)) {
	$salesid = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
} ?>
<script>
var addTime = function() {
	$('[name=time_add]').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: '../Sales/sales_ajax_all.php?action=lead_time',
				data: { id: '<?= $salesid ?>', time: time+':00' },
				success: function() {
					reload_time();
				}
			});
		}
	});
	$('[name=time_add]').timepicker('show');
}
var toggleTimeTracking = function() {
	if($('[name=time_track]').is(':visible')) {
		$('[name=time_track]').closest('.col-sm-4').hide();
		$('.start_stop span').text('Start');
		$('[name=time_track].timer').timer('stop');
        var timer_value = $('[name=time_track].timer').val();
		$('[name=time_track].timer').timer('remove');
		if ( timer_value != '' ) {
			$.ajax({
				method: 'POST',
				url: '../Sales/sales_ajax_all.php?action=lead_time',
				data: { id: '<?= $salesid ?>', time: timer_value },
				success: function() {
					reload_time();
				}
			});
        }
	} else {
		$('[name=time_track]').closest('.col-sm-4').show();
		$('.start_stop span').text('Stop');
        $('[name=time_track].timer').timer({
            editable: false
        });
	}
}
var reload_time = function() {
	$.get('../Sales/details_time.php?id=<?= $salesid ?>', function(response) {
		$('#time').parents('div').first().html(response);
	});
}
</script>
<!-- Time Tracking -->
<div class="accordion-block-details padded" id="time">
    <div class="accordion-block-details-heading"><h4>Time Tracking</h4></div>

	<div id="no-more-tables">
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>User</th>
				<th>Date</th>
				<th>Time</th>
			</tr>
			<?php $total_time = 0;
            $time_tracked = $dbc->query("SELECT * FROM `time_cards` WHERE `deleted`=0 AND `salesid`='$salesid' AND `salesid` > 0");
			while($time_row = $time_tracked->fetch_assoc()) { ?>
				<tr>
					<td data-title="User"><?= get_contact($dbc, $time_row['staff']) ?></td>
					<td data-title="Date"><?= $time_row['date'] ?></td>
					<td data-title="Time"><?= time_decimal2time($time_row['total_hrs']) ?></td>
				</tr>
                <?php $total_time += $time_row['total_hrs'];
            } ?>
            <tr>
                <td colspan="2">Total</td>
                <td data-title="Total"><?= time_decimal2time($total_time) ?></td>
            </tr>
		</table>
	</div>

    <div class="row set-row-height">
        <div class="col-xs-12">
			<a href="" onclick="addTime(); return false;" class=""><img class="inline-img no-toggle" src="../img/icons/ROOK-timer-icon.png" title="Add Time"></a>
			<input type="text" class="timepicker" style="width:0;height:0;border:0;" name="time_add" value="">
			<a href="" onclick="toggleTimeTracking(); return false;" class="start_stop"><img class="inline-img no-toggle" src="../img/icons/ROOK-timer2-icon.png" title="Start Tracking Time"></a>
			<div class="col-sm-4" style="display:none;"><input type="text" class="form-control timer" name="time_track"></div>
        </div>
        <div class="clearfix double-gap-bottom"></div>
    </div>

</div><!-- .accordion-block-details -->
