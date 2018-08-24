<?php
/*
Dashboard
FFM
*/
include ('../include.php');
error_reporting(0);
checkAuthorised('calendar_rook');
?>
<!-- <link rel="stylesheet" href="calendar.css" type="text/css"> -->
<style>
.today-btn {
  color: #fafafa;
  background: green;
  border: 2px solid #fafafa; }
</style>
<script type="text/javascript" src="calendar.js"></script>
<script>
$( document ).ready(function() {
});
</script>
</head>
<body>

<?php include ('../navigation.php');
?>

<div class="container">
	<div class="row hide_on_iframe">

        <form name="form_sites" method="post" action="" class="form-inline" role="form">
        <?php
        $day = '';
        $week = '';
        $month = '';
        $custom = '';
        $type = $_GET['type'];

        if($type == 'day') {
            $day = ' active_tab';
        }
        if($type == 'week') {
            $week = ' active_tab';
        }
        if($type == 'month') {
            $month = ' active_tab';
        }
        if($type == 'custom') {
            $custom = ' active_tab';
        }
		if($type == '30days') {
            $30days = ' active_tab';
        }
        ?>
        <a href='calendar.php?type=day'><button type="button" class="btn brand-btn mobile-block <?php echo $day; ?>" >Day</button></a>
        <a href='calendar.php?type=week'><button type="button" class="btn brand-btn mobile-block <?php echo $week; ?>" >Week</button></a>
        <a href='calendar.php?type=month'><button type="button" class="btn brand-btn mobile-block <?php echo $month; ?>" >Month</button></a>
        <a href='calendar.php?type=custom'><button type="button" class="btn brand-btn mobile-block <?php echo $custom; ?>" >Custom</button></a>
		<a href='calendar.php?type=30days'><button type="button" class="btn brand-btn mobile-block <?php echo $30days; ?>" >30 Days</button></a>

        <?php
            $search_month = date('F');
            $search_year = date('Y');
            if(isset($_POST['search_user_submit'])) {
                $search_month = $_POST['search_month'];
                $search_year = $_POST['search_year'];
            }
			if (isset($_POST['display_all_inventory'])) {
				$search_month = date('F');
                $search_year = date('Y');
			}

            if($type == 'custom') {
        ?>

        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Month:</label>
		  <div class="col-sm-8">
              <select data-placeholder="Pick a Client" name="search_month" class="form-control" width="380">
                <option value=""></option>
                <option <?php if ($search_month == "January") { echo " selected"; } ?>  value="January">January</option>
                <option <?php if ($search_month == "February") { echo " selected"; } ?> value="February">February</option>
                <option <?php if ($search_month == "March") { echo " selected"; } ?> value="March">March</option>
                <option <?php if ($search_month == "April") { echo " selected"; } ?> value="April">April</option>
                <option <?php if ($search_month == "May") { echo " selected"; } ?> value="May">May</option>
                <option <?php if ($search_month == "June") { echo " selected"; } ?> value="June">June</option>
                <option <?php if ($search_month == "July") { echo " selected"; } ?> value="July">July</option>
                <option <?php if ($search_month == "August") { echo " selected"; } ?> value="August">August</option>
                <option <?php if ($search_month == "September") { echo " selected"; } ?> value="September">September</option>
                <option <?php if ($search_month == "October") { echo " selected"; } ?> value="October">October</option>
                <option <?php if ($search_month == "November") { echo " selected"; } ?> value="November">November</option>
                <option <?php if ($search_month == "December") { echo " selected"; } ?> value="December">December</option>
            </select>
		  </div>
		</div>
        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Year:</label>
		  <div class="col-sm-8">
              <select data-placeholder="Pick a Client" name="search_year" class="form-control" width="380">
              <option value=""></option>
              <option <?php if ($search_year == "2015") { echo " selected"; } ?>  value="2015">2015</option>
              <option <?php if ($search_year == "2016") { echo " selected"; } ?>  value="2016">2016</option>
              <option <?php if ($search_year == "2017") { echo " selected"; } ?>  value="2017">2017</option>
            </select>
		  </div>
		</div>

        <div class="form-group">
            <button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
			<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Current Month</button>
		</div>
        <?php } ?>
    <?php
    echo '<div class="pull-right" >';
    echo '<img src="'.WEBSITE_URL.'/img/block/green.png" width="10" height="10" border="0" alt="">&nbsp;Today+Coming Day';
    echo '<br><img src="'.WEBSITE_URL.'/img/block/orange.png" width="10" height="10" border="0" alt="">&nbsp;Last 2 days';
    echo '<br><img src="'.WEBSITE_URL.'/img/block/red.png" width="10" height="10" border="0" alt="">&nbsp;Older than last 2 days';
    echo '</div>';
    ?>

    <?php
    echo '<h2>'.$search_month.' '.$search_year.'</h2>';
    echo draw_calendar($dbc, date("n", strtotime($search_month)),$search_year);
    ?>

	</div>
</div>

<?php include ('../footer.php'); ?>

<?php
function draw_calendar($dbc, $month,$year){

	/* draw table */

    //class="calendar"

	$calendar = '<table cellpadding="0" cellspacing="0" class="table table-bordered">';

	/* table headings */
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np"> </td>';
		$days_in_this_week++;
	endfor;

    $sunday = date( 'Y-m-d', strtotime( 'sunday last week' ) );
    $monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
    $tuesday = date( 'Y-m-d', strtotime( 'tuesday this week' ) );
    $wednesday = date( 'Y-m-d', strtotime( 'wednesday this week' ) );
    $thursday = date( 'Y-m-d', strtotime( 'thursday this week' ) );
    $friday = date( 'Y-m-d', strtotime( 'friday this week' ) );
    $saturday = date( 'Y-m-d', strtotime( 'saturday this week' ) );

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):

        $today_date = $list_day.'-'.$month.'-'.$year;
        $new_today_date = date_format(date_create_from_format('j-n-Y', $today_date), 'Y-m-d');

            if($_GET['type'] == 'week' && ($new_today_date == $sunday || $new_today_date == $monday || $new_today_date == $tuesday || $new_today_date == $wednesday || $new_today_date == $thursday || $new_today_date == $friday || $new_today_date == $saturday)) {

            $calendar.= '<td class="calendar-day connectedSortable '.$new_today_date.'">';
            /* add in the day number */
            $class = '';
            if($new_today_date == date('Y-m-d')) {
                $class = 'today-btn';
            }
            $calendar.= '<div class="btn brand-btn pull-right '.$class.'">'.$list_day.'</div>';

            $result = mysqli_query($dbc,"SELECT * FROM contacts WHERE category IN (".STAFF_CATS.") AND IFNULL(`staff_category`,'') NOT IN (".STAFF_CATS_HIDE.") AND deleted=0");

            $old_staff = '';
            $i=0;
            while($row = mysqli_fetch_array( $result )) {
                $contactid = $row['contactid'];
                $staff = get_staff($dbc, $contactid);

                $tickets = mysqli_query($dbc,"SELECT * FROM tickets WHERE DATE(to_do_date) = '$new_today_date'  AND contactid LIKE '%," . $contactid . ",%'");

                $num_rows = mysqli_num_rows($tickets);
                if($num_rows > 0) {
                    $calendar .= $staff.'<br>';
                }

                while($row_tickets = mysqli_fetch_array( $tickets )) {
                    $date_color = 'block/green.png';
                    if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
                        $date_color = 'block/red.png';
                    }
                    if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
                        $date_color = 'block/orange.png';
                    }
                    $calendar .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row_tickets['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="ticket_'.$row_tickets['ticketid'].'">#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' ('.substr($row_tickets['max_time'], 0, 5).')'.'</a><br>';
					//$calendar .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<a class="" href="#"  id="ticket_'.$row_tickets['ticketid'].'" onclick="wwindow.open(\''.WEBSITE_URL.'/Ticket/add_tickets.php?ticketid='.$row_tickets['ticketid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;">#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' ('.substr($row_tickets['max_time'], 0, 5).')'.'</a><br>';
                }

                $tasklist = mysqli_query($dbc,"SELECT * FROM tasklist WHERE DATE(task_tododate) = '$new_today_date'  AND contactid = '$contactid'");

                $num_rows1 = mysqli_num_rows($tasklist);
                if($num_rows1 > 0 && $num_rows == 0) {
                    $calendar .= $staff.'<br>';
                }

                while($row_tasklist = mysqli_fetch_array( $tasklist )) {
                    $date_color = 'block/green.png';
                    if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
                        $date_color = 'block/red.png';
                    }
                    if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
                        $date_color = 'block/orange.png';
                    }
					$calendar .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<a href="'.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$row_tasklist['tasklistid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="task_'.$row_tasklist['tasklistid'].'">#'.$row_tasklist['tasklistid'].' : '.get_contact($dbc, $row_tasklist['businessid'], 'name').' ('.substr($row_tasklist['max_time'], 0, 5).')'.'</a><br>';
                  //  $calendar .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<a href="#"  id="task_'.$row_tasklist['tasklistid'].'" onclick="wwindow.open(\''.WEBSITE_URL.'/Tasks/add_task.php?tasklistid='.$row_tasklist['tasklistid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;">'.$row_tasklist['heading']. '</a><br>';
                }

                $site_wo = mysqli_query($dbc,"SELECT * FROM `site_work_orders` WHERE ((DATE(work_start_date) <= '$new_today_date' AND DATE(work_end_date) >= '$new_today_date') OR `active` LIKE '$new_today_date%') AND `status` NOT IN ('Pending', 'Archived')");
                $num_rows1 = mysqli_num_rows($site_wo);
                if($num_rows1+$num_rows > 0 && mysqli_num_rows($site_wo) == 0) {
                    $calendar .= $staff.'<br>';
                }
                while($row_site = mysqli_fetch_array( $site_wo )) {
                    $date_color = 'block/green.png';
                    if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
                        $date_color = 'block/red.png';
                    } else if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
                        $date_color = 'block/orange.png';
                    }
					$calendar .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<a href="'.WEBSITE_URL.'/Site Work Orders/view_work_order.php?workorderid='.$row_site['workorderid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="site_wo_'.$row_site['workorderid'].'">#'.$row_site['id_label'].'</a><br>';
                }

                $i++;
            }

            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
            $calendar.= str_repeat('<p> </p>',2);

            $calendar.= '</td>';
        }

		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			//$calendar.= '<td class="calendar-day-np"> </td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';

	/* all done, return result */
	return $calendar;
}
?>