<?php $edit_access = vuaed_visible_function($dbc, 'calendar_rook');
$calendar_hide_left_time = get_config($dbc, 'calendar_hide_left_time');
if($calendar_hide_left_time == 1) {
	$hide_time = '; display: none;';
	$td_height = 'height: 29px;';
} ?>
<style>
.popped-field {
	border: 1px solid rgb(221, 221, 221);
	font-size: 1.2em;
	max-width: 15em;
	padding: 1em;
}
.highlightCell {
	background-color: rgba(0,0,0,0.2) !important;
}
a.shift {
	z-index: -1;
}
td {
	z-index: 0;
	<?= $td_height ?>
}
td:empty {
	z-index: 0;
}
</style>
<script type="text/javascript">
var offline_mode = '<?= $_GET['offline'] ?>';
$(document).ready(function() {
    $(window).resize(function() {
    	resizeBlocks();
    }).resize();
    $('a').on('click', function() {
    	resizeBlocks();
    });
    $('.collapsible').on('click', function() {
    	resizeBlocks();
    });
});
function resizeBlocks() {
	$('.used-block').each(function() {
		$(this).closest('td').css('z-index', 1);
		var rows = $(this).data('blocks');
		var parent = $(this).closest('td');
		var header = 0;
		if (parent.prev().is('thead:visible')) {
			header = $(this).closest('table').find('thead tr').first()[0].clientHeight;
		}
		$(this).css('top', header);
		$(this).css('left', '0');
		$(this).css('margin', '0');
		$(this).css('padding', '0.2em');
		if($(this).hasClass('combined_blocks')) {
			$(this).css('padding', '0');
		}
		$(this).height((parent.innerHeight() * parseInt(rows)) - header);
		$(this).height('calc(' + $(this).height() + 'px - 2px + ' + rows + 'px)');
		$(this).closest('td').find('.ui-resizable-e').height((parent.innerHeight() * parseInt(rows)) - header);
		// $(this).width(parent.innerWidth());
		// $(this).width('calc(' + $(this).width() + 'px)');
	});
}
</script>
<table class="table table_bordered <?= $appointment_calendar ?>" style="overflow-x: auto;">
	<?php $region_list = explode(',',get_config($dbc, '%_region', true));
	$region_colours = explode(',',get_config($dbc, '%_region_colour', true));
	$calendar_ticket_card_fields = explode(',',get_config($dbc, 'calendar_ticket_card_fields')); ?>
	<?php foreach($calendar_table[0][0] as $calendar_row => $calendar_times) {
		if($calendar_row === 'warnings' || $calendar_row === 'reminders' || $calendar_row === 'notes') {
			echo '<tr data-rowtype="'.$calendar_row.'" class="calendar_notes_row">';
		} else if($calendar_row === 'ticket_summary') {
			echo '<tr data-rowtype="'.$calendar_row.'" class="ticket_summary_row">';
		} else {
			echo ($calendar_row === 'title' ? "<thead><tr style='position: absolute; z-index: 9;'>" : '<tr data-rowtype="'.$calendar_row.'">');
		}
		foreach($calendar_table as $current_day => $calendar_col_date) {
			foreach($calendar_col_date as $contact_id => $calendar_col) {
				if(!empty($equipassign_data[$current_day][$contact_id])) {
					$equipassignid_data = "data-equipassign='".$equipassign_data[$current_day][$contact_id]."'";
				} else {
					$equipassignid_data = "";
				}
				if($calendar_row === 'title') {
					echo "<th data-contact='$contact_id' $equipassignid_data data-date='".$current_day."' data-row='title' style='";
					if($equipassign_data[$current_day][$contact_id] > 0) {
						$equipassign_region = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `region` FROM `equipment_assignment` WHERE `equipment_assignmentid`='".$equipassign_data[$current_day][$contact_id]."'"))['region'];
						foreach($region_list as $region_line => $region_name) {
							if($equipassign_region == $region_name) {
								echo "background: ".$region_colours[$region_line].";color: #000;";
							}
						}
					}
					echo ($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$hide_time."padding:0;'><div class='resizer' style='min-width:100%; max-width:100%; padding:0.5em;'>";
					echo ($current_day == 0 ? $calendar_col['title'] : ($_GET['view'] == 'daily' ? $calendar_col[$calendar_row] : date('l, F d', strtotime($current_day)).'<br>'.$calendar_col[$calendar_row]))."</div></th>";
				} else if ($contact_id > 0 && ($calendar_row === 'shifts' || $calendar_row === 'warnings' || $calendar_row === 'reminders' || $calendar_row === 'notes' || $calendar_row === 'ticket_summary' || $calendar_row === 'title')) {
					echo "<td data-rowtype='".$calendar_row."' data-date='".$current_day."' $equipassignid_data data-calendartype='".$_GET['type']."' data-calendarmode='".$_GET['mode']."' data-contact='$contact_id' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$hide_time."'><div class='calendar_notes' style='overflow-y: hidden;'>".$calendar_col[$calendar_row].'</div>';
					if($calendar_row == 'notes' && $contact_id != 0) {
						echo '<div class="calendar_notes_btn" style="text-align: right; position: relative;">'.($edit_access == 1 ? '<a class="edit_calendar_notes" href=""><sub>EDIT</sub></a>' : '').'</div>';
						echo '<div class="calendar_notes_edit" style="display:none;"><textarea style="resize: vertical;" class="noMceEditor form-control">'.html_entity_decode($calendar_col[$calendar_row]).'</textarea></div>';
					}
					echo '<a class="expand-div-link" href="" onclick="expandDiv(this); return false;"><div style="font-size: 1.5em; text-align: center;">...</div></a>';
				} else if ($contact_id > 0 && $calendar_row === 'ticket_summary') {
					echo "<td data-rowtype='ticket_summary' data-date='".$current_day."' data-contact='$contact_id' data-draggable='0' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$hide_time."'>".$calendar_col[$calendar_row]."</td>";
				} else {
					echo "<td data-region='".$calendar_table[$current_day][$contact_id]['region']."' data-date='".$current_day."' data-calendartype='".$_GET['type']."' data-contact='$contact_id' $equipassignid_data data-time='$calendar_row' data-duration='".($day_period * 60)."' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$hide_time.$is_shift."'>";
					if ($calendar_col[$calendar_row] != $calendar_col[$calendar_row - 1]) {
						echo $calendar_col[$calendar_row];
					}
					echo "</td>";
				}
				if($calendar_col[$calendar_row] != '' && $contact_id > 0 && $calendar_row != 'title' && $calendar_row != 'notes') {
				}
			}
		}
		echo "</tr>".($calendar_row === 'title' ? "</thead>" : '');
	} ?>
</table>