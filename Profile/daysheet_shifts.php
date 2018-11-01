<!-- Daysheet My Shifts -->
<?php
include_once ('../Calendar/calendar_functions_inc.php');

$start_date = date('Y-m-d');
$end_date = date('Y-m-d');
if(!empty($_POST['search_start_date'])) {
    $start_date = date('Y-m-d', strtotime($_POST['search_start_date']));
}
if(!empty($_POST['search_end_date'])) {
    $end_date = date('Y-m-d', strtotime($_POST['search_end_date']));
}

?>
<div class="col-xs-12">
    <div class="weekly-div" style="overflow-y: hidden;">
        <form class="form-horizontal" method="POST" action="">
            <center>
                <div class="form-group col-sm-5">
                    <label class="col-sm-4">Start Date:</label>
                    <div class="col-sm-8"><input type="text" name="search_start_date" value="<?= $start_date ?>" class="form-control datepicker"></div>
                </div>
                <div class="form-group col-sm-5">
                    <label class="col-sm-4">End Date:</label>
                    <div class="col-sm-8"><input type="text" name="search_end_date" value="<?= $end_date ?>" class="form-control datepicker"></div>
                </div>
                <button type="submit" name="search_shift_dates" value="Search" class="btn brand-btn mobile-block">Submit</button>
            </center>
            <div class="clearfix"></div>
            <?php for($cur_day = $start_date; strtotime($cur_day) <= strtotime($end_date); $cur_day = date('Y-m-d', strtotime($cur_day.' + 1 day'))) {
                $day_of_week = date('l', strtotime($cur_day));
                $shifts = checkShiftIntervals($dbc, $_SESSION['contactid'], $day_of_week, $cur_day, 'all'); ?>
                <div class="shift_day double-gap-left">
                    <h4 style="font-weight: normal;"><?= date('F d, Y', strtotime($cur_day)) ?></h4>
                    <ul>
                    <?php if(!empty($shifts)) {
                        $total_booked_time = 0;
                        foreach($shifts as $shift) {
                            echo '<li>';
                            if(!empty($shift['heading'])) {
                                echo $shift['heading'].'<br>';
                            }
                            if(!empty($shift['dayoff_type'])) {
                                echo 'Day Off: '.date('h:i a', strtotime($shift['starttime'])).' - '.date('h:i a', strtotime($shift['endtime'])).'<br>';
                                echo 'Day Off Type: '.$shift['dayoff_type'];
                            } else {
                                $total_booked_time += (strtotime($shift['endtime']) - strtotime($shift['starttime']));
                                echo 'Shift: '.date('h:i a', strtotime($shift['starttime'])).' - '.date('h:i a', strtotime($shift['endtime']));
                                if(!empty($shift['break_starttime']) && !empty($shift['break_endtime'])) {
                                    echo '<br>';
                                    echo 'Break: '.date('h:i a', strtotime($shift['break_starttime'])).' - '.date('h:i a', strtotime($shift['break_endtime']));
                                }
                                if(!empty($shift['clientid'])) {
                                    echo '<br>';
                                    echo get_contact($dbc, $shift['clientid'], 'category').': ';
                                    echo '<a href="'.WEBSITE_URL.'/'.ucfirst(get_contact($dbc, $shift['clientid'], 'tile_name')).'/contacts_inbox.php?edit='.$shift['clientid'].'">'.get_contact($dbc, $shift['clientid']).'</a>';
                                }
                            }
                            if(!empty($shift['notes'])) {
                                echo '<br>';
                                echo 'Notes: '.html_entity_decode($shift['notes']);
                            }
                            echo '</li>';
                        }
                        echo '<br>Total Booked Time: '.(sprintf('%02d', floor($total_booked_time / 3600)).':'.sprintf('%02d', floor($total_booked_time % 3600 / 60))).'';
                    } else {
                        echo 'No Shifts Found.';
                    } ?>
                    </ul>
                </div>
                <hr style="height: 1px; border: 0; border-top: 1px solid #ccc;">
            <?php } ?>
        </form>
    </div>
</div>