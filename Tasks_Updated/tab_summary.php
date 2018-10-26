<?php
/*
 * Summary Tab
 * Called from index.php
 */
?>

<?php include ('../include.php');
checkAuthorised('tasks'); ?>
<div class="standard-dashboard-body-content"><?php
    $get_tabs = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT task_dashboard_tile FROM task_dashboard")); ?>
    <div id="summary-div" class="col-xs-12"><?php
        foreach ( explode(',', $get_tabs['task_dashboard_tile']) as $enabled_tab ) {
            if ( $enabled_tab!=='Community Tasks' && $enabled_tab!=='Business Tasks' && $enabled_tab!=='Reporting' ) {

                if ( $enabled_tab=='Private Tasks' ) {
                    $enabled_tab = 'Private '.TASK_TILE;
                    $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.task_board=tb.taskboardid AND tb.board_security='Private' AND (tl.archived_date IS NULL OR tl.archived_date = '0000-00-00') AND (tl.created_by = ({$_SESSION['contactid']}) OR tl.contactid IN (". $_SESSION['contactid'] .")) AND tl.deleted=0 AND tb.deleted=0"));
                } else if ( $enabled_tab=='Shared Tasks' ) {
                    $enabled_tab = 'Shared '.TASK_TILE;
                    $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) task_count FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE (tb.board_security='Company' AND tb.company_staff_sharing LIKE '%,". $_SESSION['contactid'] .",%') AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND (tl.salesid IS NULL OR tl.salesid = 0) AND tl.task_path > 0 AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_tododate"));
                } elseif ( $enabled_tab=='Project Tasks' ) {
                    $enabled_tab = 'Project '.TASK_TILE;

                    $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) task_count FROM tasklist tl, project p WHERE tl.projectid = p.projectid AND p.deleted = 0 AND tl.projectid > 0 AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 ORDER BY tl.task_tododate"));

                    //$get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) task_count FROM tasklist tl, project p WHERE tl.projectid = p.projectid AND p.deleted = 0 AND tl.projectid > 0 AND (tl.created_by = ({$_SESSION['contactid']}) OR tl.contactid IN (". $_SESSION['contactid'] .")) AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 ORDER BY tl.task_tododate"));
                } elseif ( $enabled_tab=='Client Tasks' ) {
                    $enabled_tab = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' '.TASK_TILE : CONTACTS_TILE.' '.TASK_TILE;
                    $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) task_count FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE (tl.created_by = ({$_SESSION['contactid']}) OR tl.contactid IN (". $_SESSION['contactid'] .")) AND (tb.board_security='Client' AND tb.company_staff_sharing LIKE '%,". $_SESSION['contactid'] .",%') AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_tododate"));
                }  elseif ( $enabled_tab=='Sales Tasks') {
                    $enabled_tab = 'Sales '.TASK_TILE;
                    $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) task_count FROM tasklist tl WHERE tl.salesid > 0 AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tl.task_milestone_timeline = ''"));
                } else {
                    //$board_security = str_replace(' Tasks', '', $enabled_tab);
                   // $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.task_board=tb.taskboardid AND tb.board_security='$board_security' AND tl.deleted=0 AND tb.deleted=0"));
                }

                $task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0;

                echo '<div class="col-xs-12 col-sm-6 col-md-3 gap-top">';
                    echo '<div class="summary-block">';
                        echo '<div class="text-lg">'. $task_count .'</div>';
                        echo '<div>'. $enabled_tab .'</div>';
                    echo '</div>';
                echo '</div>';
            }
        } ?>

    </div><!-- #summary-div -->

    <?php
        echo '<div class="col-xs-12 gap-top">';
            echo '<div class="overview-block">';
                echo '<h4>Today\'s Created '.TASK_TILE.' : '.date('F jS, Y').'</h4>';

                $today_tickets = $dbc->query("SELECT tasklistid, heading, status FROM `tasklist` WHERE `deleted`=0 AND created_date = '".date('Y-m-d')."'");
                while($ticket = $today_tickets->fetch_assoc()) {
                    ?>

                    <?php
                    $slider_layout = !empty(get_config($dbc, 'tasks_slider_layout')) ? get_config($dbc, 'tasks_slider_layout') : 'accordion';

                    if($slider_layout == 'accordion') {
                    ?>
                    <p><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?type=<?=$ticket['status']?>&tasklistid=<?=$ticket['tasklistid']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">#<?php echo $ticket['tasklistid'].' - '.$ticket['heading'] ?></a></p>
                    <?php } else { ?>
                    <p><a href="../Tasks_Updated/add_task_full_view.php?type=<?=$ticket['status']?>&tasklistid=<?=$ticket['tasklistid']?>">#<?php echo $ticket['tasklistid'].' - '.$ticket['heading'] ?></a></p>
                    <?php } ?>

                    <?php
                    }
            echo '</div>';
        echo '</div>';

        echo '<div class="col-xs-12">';
            echo '<div class="overview-block">';
                echo '<h4>'.TASK_TILE.' that have not been completed today : '.date('F jS, Y').'</h4>';

                $today_tickets = $dbc->query("SELECT tasklistid, heading, status, work_time FROM `tasklist` WHERE `deleted`=0 AND task_tododate = '".date('Y-m-d')."' AND work_time = '00:00:00'");
                while($ticket = $today_tickets->fetch_assoc()) {

                    if($slider_layout == 'accordion') {
                    ?>
                    <p><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?type=<?=$ticket['status']?>&tasklistid=<?=$ticket['tasklistid']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">#<?php echo $ticket['tasklistid'].' - '.$ticket['heading'].' - '.$ticket['status'].' - '.$ticket['work_time'] ?></a></p>
                    <?php } else { ?>
                    <p><a href="../Tasks_Updated/add_task_full_view.php?type=<?=$ticket['status']?>&tasklistid=<?=$ticket['tasklistid']?>">#<?php echo $ticket['tasklistid'].' - '.$ticket['heading'].' - '.$ticket['status'].' - '.$ticket['work_time'] ?></a></p>
                    <?php } ?>

                    <?php
                    }
            echo '</div>';
        echo '</div>';

        echo '<div class="col-xs-12">';
            echo '<div class="overview-block">';
                echo '<h4>Time tracked/added to '.TASK_TILE.' today : '.date('F jS, Y').'</h4>';

                $today_tickets = $dbc->query("SELECT DISTINCT(t.tasklistid), t.heading, t.status, tt.work_time FROM `tasklist` t, tasklist_time tt WHERE t.deleted=0 AND tt.timer_date = '".date('Y-m-d')."' AND t.tasklistid = tt.tasklistid");
                while($ticket = $today_tickets->fetch_assoc()) {

                    if($slider_layout == 'accordion') {
                    ?>
                    <p><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?type=<?=$ticket['status']?>&tasklistid=<?=$ticket['tasklistid']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">#<?php echo $ticket['tasklistid'].' - '.$ticket['heading'].' - '.$ticket['status'].' - '.$ticket['work_time'] ?></a></p>
                    <?php } else { ?>
                    <p><a href="../Tasks_Updated/add_task_full_view.php?type=<?=$ticket['status']?>&tasklistid=<?=$ticket['tasklistid']?>">#<?php echo $ticket['tasklistid'].' - '.$ticket['heading'].' - '.$ticket['status'].' - '.$ticket['work_time'] ?></a></p>
                    <?php } ?>
                    <?php
                    }

                $today_tickets = $dbc->query("SELECT tasklistid, heading, status, work_time FROM `tasklist` WHERE deleted=0 AND task_tododate = '".date('Y-m-d')."' AND work_time != '00:00:00'");
                while($ticket = $today_tickets->fetch_assoc()) {
                    ?>
                        <p><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?type=<?=$ticket['status']?>&tasklistid=<?=$ticket['tasklistid']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">#<?php echo $ticket['tasklistid'].' - '.$ticket['heading'].' - '.$ticket['status'].' - '.$ticket['work_time'] ?></a></p>
                    <?php
                    }
            echo '</div>';
        echo '</div>';

        ?>

</div><!-- .standard-dashboard-body-content -->

