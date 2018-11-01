<?php include_once('../include.php');
checkAuthorised('calendar_rook');
include_once('calendar_settings_inc.php');
include_once('calendar_functions_inc.php');
$search_month = date('F');
$search_year = date('Y');
if(!isset($_GET['date'])) {
	$_GET['date'] = date('Y-m-d');
}
if(isset($_GET['date'])) {
	$search_month = date('F', strtotime($_GET['date']));
	$search_year = date('Y', strtotime($_GET['date']));
}
$calendar_month = date("n", strtotime($search_month.' '.$search_year));
$calendar_year = $search_year;
$quick_add = get_config($dbc, 'shift_calendar_quick_add');
$edit_access = vuaed_visible_function($dbc, 'calendar_rook');
if($is_customer) {
	$edit_access = 0;
}
$ticket_view_access = tile_visible($dbc, 'ticket');

$page_query = $_GET;
$all_contacts = $_POST['all_contacts'];
$all_contacts_query = "'".implode("','", $all_contacts)."'"; ?>
<style>
.today-btn {
  color: #fafafa;
  background: green;
  border: 2px solid #fafafa; }
</style>
<script type="text/javascript" src="calendar.js"></script>
<script type="text/javascript">
	$('.calendar_view').on('scroll', function() {
		$('.calendar_view tr').first().css('top',$('.calendar_view').first().prop('scrollTop'));
	});
</script>
<?php
/* draw table */

if($_GET['view'] == 'daily') {
	$monthly_days = [date('l', strtotime($_GET['date']))];
}

$day_of_week_num = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

//class="calendar"
$column_width = (100 / count($monthly_days)).'%';

echo '<table class="table table_bordered calendar_table" style="background-color: #fff; margin-bottom: 0px; min-height: 100%; overflow-x: auto;">';

/* table headings */
if($monthly_start == 'Monday') {
	$headings = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
} else {
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
}
foreach ($headings as $key => $heading) {
	if(!in_array($heading, $monthly_days)) {
		unset($headings[$key]);
	}
}
echo '<thead><tr class="calendar-row" style="height: 1em; position: absolute; z-index: 9;"><th style="width:'.$column_width.'; min-width: 10em; border-left: 1px solid rgb(221, 221, 221);" class="calendar-day-head">'.implode('</th><th style="width:'.$column_width.'; min-width: 10em; border-left: 1px solid rgb(221, 221, 221);" class="calendar-day-head">',$headings).'</hd></tr></thead>';

/* days and weeks vars now ... */
$running_day = date('w',mktime(0,0,0,$calendar_month,1,$calendar_year));
$days_in_month = date('t',mktime(0,0,0,$calendar_month,1,$calendar_year));
if($monthly_numdays == 'week') {
	$days_added = 7;
} else if($monthly_numdays == 'month') {
	$days_added = date('Y-m-d', strtotime($calendar_year.'-'.$calendar_month.'-01 + 1 month'));
	$days_added = date('t', strtotime($days_added));
} else {
	$days_added = 0;
}
$days_in_this_week = 1;
$day_counter = 0;
$dates_array = array();

$list_day = 1;
$first_day_month = date('Y-m-01', strtotime($_GET['date']));
$last_day_month = date('Y-m-t', strtotime($_GET['date']));
if($_GET['view'] == 'weekly') {
	if($monthly_start == 'Monday' && date('l', strtotime($_GET['date'])) != 'Monday') {
		$list_day = date('j', strtotime('last Monday', strtotime($_GET['date'])));
		if(date('l', strtotime($_GET['date'])) == 'Sunday') {
			$days_in_month = date('j', strtotime($_GET['date']));
		} else {
			$days_in_month = date('j', strtotime('next Sunday', strtotime($_GET['date'])));
		}
	} else if ($monthly_start != 'Monday' && date('l', strtotime($_GET['date'])) != 'Sunday') {
		$list_day = date('j', strtotime('last Sunday', strtotime($_GET['date'])));
		$days_in_month = date('j', strtotime('next Saturday', strtotime($_GET['date'])));
		if(date('l', strtotime($_GET['date'])) == 'Saturday') {
			$days_in_month = date('j', strtotime($_GET['date']));
		} else {
			$days_in_month = date('j', strtotime('next Saturday', strtotime($_GET['date'])));
		}
	} else {
		$list_day = date('j', strtotime($_GET['date']));
		$days_in_month = date('j', strtotime($_GET['date'].' + 6 days'));
	}
	if($list_day > date('j', strtotime($_GET['date']))) {
		$list_day = $list_day - date('t', strtotime($first_day_month.' - 1 month'));
	}
	if($days_in_month < date('j', strtotime($_GET['date']))) {
		$days_in_month = $days_in_month + date('t', $_GET['date']);
	}
	$days_added = 0;
	$running_day = 0;
} else if($_GET['view'] == 'daily') {
	$list_day = date('j', strtotime($_GET['date']));
	$days_in_month = $list_day;
	$days_added = 0;
	$running_day = 0;
}

echo '<tbody>';
/* row for week one */
echo '<tr class="calendar-row">';

/* print "blank" days until the first of the current week */
for($x = 0; $x < $running_day; $x++):
	if(in_array($day_of_week_num[$x], $monthly_days)) {
		echo '<td style="width:'.$column_width.'; min-width: 10em; border-left: 1px solid rgb(221, 221, 221);" class="calendar-day-np"> </td>';
	}
	$days_in_this_week++;
endfor;
if($monthly_start == 'Monday') {
	$sunday = date( 'Y-m-d', strtotime( 'sunday this week' ) );
} else {
	$sunday = date( 'Y-m-d', strtotime( 'sunday last week' ) );
}
$monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
$tuesday = date( 'Y-m-d', strtotime( 'tuesday this week' ) );
$wednesday = date( 'Y-m-d', strtotime( 'wednesday this week' ) );
$thursday = date( 'Y-m-d', strtotime( 'thursday this week' ) );
$friday = date( 'Y-m-d', strtotime( 'friday this week' ) );
$saturday = date( 'Y-m-d', strtotime( 'saturday this week' ) );

$all_calendar_dates = [];
/* keep going with days.... */

for($list_day; $list_day <= $days_in_month + $days_added; $list_day++):
	if($list_day > date('j', $last_day_month)) {
		$today_date = date('Y-m-01', strtotime($first_day_month.' + '.($list_day - 1).' days'));
		$new_today_date = $today_date;
	    $list_day_label = date('j', strtotime($new_today_date));
	} else if($list_day <= 0) {
		$today_date = date('Y-m-d', strtotime($first_day_month.' '.($list_day - 1).' days'));
		$new_today_date = $today_date;
	    $list_day_label = date('j', strtotime($new_today_date));
	} else {
	    $today_date = $list_day.'-'.$calendar_month.'-'.$calendar_year;
	    if($list_day > $days_in_month) {
	    	$new_list_day = $list_day - $days_in_month;
	    	$new_calendar_month = $calendar_month + 1;
	    	$new_calendar_year = $calendar_year;
	    	if($new_calendar_month == 13) {
	    		$new_calendar_month = 01;
	    		$new_calendar_year = $calendar_year + 1;
	    	}
	    	$today_date = $new_list_day.'-'.$new_calendar_month.'-'.$new_calendar_year;
	    }
	    $new_today_date = date_format(date_create_from_format('j-n-Y', $today_date), 'Y-m-d');
	    $list_day_label = $list_day > $days_in_month ? $new_list_day : $list_day;
	}
    $day_of_week = date('l', strtotime($new_today_date));

    if(in_array($day_of_week, $monthly_days)) {
    	$all_calendar_dates[] = $new_today_date;
	    echo '<td style="width:'.$column_width.'; min-width: 10em; border-left: 1px solid rgb(221, 221, 221); position: relative; padding-bottom: 2em;" class="calendar-day '.($_GET['mode'] != 'staff_summary' && $_GET['mode'] != 'ticket_summary' && $_GET['mode'] != 'day_summary' ? 'calendarSortable' : '').' '.$new_today_date.'" data-itemtype="shift" data-date="'.$new_today_date.'">';
	    /* add in the day number */
	    $class = '';
	    if($new_today_date == date('Y-m-d')) {
	        $class = 'today-btn';
	    }
	    echo '<a href="?date_override=1&type='.$_GET['type'].'&view=daily&date='.$new_today_date.'&mode='.($_GET['mode'] == 'summary' ? 'schedule' : $_GET['mode']).'&region='.$_GET['region'].($_GET['type'] == 'schedule' && $_GET['mode'] == 'summary' ? '&retrieve_assigned=1' : '').'"><div class="btn brand-btn pull-right '.$class.'">'.$list_day_label.'</div></a>';

	    if($_GET['retrieve_all'] == 1) {
	    	if($_GET['type'] == 'schedule' && $_GET['mode'] == 'summary') {
	    		$column = '<br><br>';
	    		include('monthly_display_equip_summary.php');
	    		echo $column;
	    	} else if($_GET['type'] == 'ticket' && $_GET['mode'] == 'staff_summary') {
	    		$column = '<br><br>';
	    		include('monthly_display_tickets_staff_summary.php');
	    		echo $column;
	    	} else if($_GET['type'] == 'ticket' && $_GET['mode'] == 'ticket_summary') {
	    		$column = '<br><br>';
	    		include('monthly_display_tickets_ticket_summary.php');
	    		echo $column;
	    	} else if($_GET['type'] == 'uni' && $_GET['mode'] == 'staff_summary') {
	    		$column = '<br><br>';
	    		include('monthly_display_uni_staff_summary.php');
	    		echo $column;
	    	} else if($_GET['type'] == 'uni' && $_GET['mode'] == 'day_summary') {
	    		$column = '<br><br>';
	    		include('monthly_display_uni_day_summary.php');
	    		echo $column;
	    	}
	    }

	    /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
	    echo str_repeat('<p> </p>',2);

	    if($_GET['type'] == 'shift' && $quick_add == 1 && $edit_access > 0) {
    		echo '<span style="position: absolute; bottom: 0.25em; right: 0.25em;"><a href="" onclick="quickAddShift(this); return false" data-date="'.$new_today_date.'"><img src="'.WEBSITE_URL.'/img/icons/ROOK-add-icon.png" class="inline-img" title="Quick Add"></a></span>';
	    }

	    echo '</td>';
    }

	if($running_day == 6):
		echo '</tr>';
		if(($day_counter+1) != $days_in_month && $_GET['view'] != 'weekly' && $_GET['view'] != 'daily'):
			echo '<tr class="calendar-row">';
		endif;
		$running_day = -1;
		$days_in_this_week = 0;
	endif;
	$days_in_this_week++; $running_day++; $day_counter++;
endfor;

/* finish the rest of the days in the week */
if($days_in_this_week < 8 && $_GET['view'] != 'weekly' && $_GET['view'] != 'daily'):
	for($x = $days_in_this_week; $x < 8; $x++):
		if(in_array($day_of_week_num[$x - 1], $monthly_days)) {
			echo '<td style="width:'.$column_width.'; min-width: 10em; border-left: 1px solid rgb(221, 221, 221);" class="calendar-day-np"> </td>';
		}
	endfor;
endif;

/* final row */
echo '</tr>';
echo '</tbody>';

echo '<input type="hidden" id="calendar_dates_month" value=\''.json_encode($all_calendar_dates).'\'>';
/* end the table */
echo '</table>';

?>