<?php
/*
 * Tasks Search
 * Called from index.php
 */
?>

<div class="standard-dashboard-body-content">
<?php
    include ('../include.php');
    checkAuthorised('tasks');
    $contactid = $_SESSION['contactid'];
    $term = filter_var($_GET['term'], FILTER_SANITIZE_STRING);

    /* Pagination Counting */
    $rowsPerPage = 10;
    $pageNum = 1;

    if(isset($_GET['page'])) {
        $pageNum = $_GET['page'];
    }

    $offset = ($pageNum - 1) * $rowsPerPage;

    //$result = mysqli_query($dbc, "SELECT tl.*, tb.* FROM tasklist tl LEFT JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.deleted='0' AND tb.deleted='0' AND (tl.contactid='$contactid' OR tb.company_staff_sharing LIKE '%,". $contactid .",%') AND (tl.tasklistid LIKE '%$term%' OR tl.heading LIKE '%$term%') LIMIT $offset, $rowsPerPage");

    $result = mysqli_query($dbc, "SELECT tl.* FROM tasklist tl WHERE (tl.tasklistid LIKE '%$term%' OR tl.heading LIKE '%$term%') LIMIT $offset, $rowsPerPage");

    $query = "SELECT COUNT(*) AS numrows FROM tasklist tl WHERE (tl.tasklistid LIKE '%$term%' OR tl.heading LIKE '%$term%')";

    if ( $result->num_rows>0 ) {
        echo '<div class="'.($pageNum==1 ? '' : 'gap-left').'">'; echo display_pagination($dbc, $query, $pageNum, $rowsPerPage); echo '</div>';
        while ( $row=mysqli_fetch_assoc($result) ) {
            echo '<div class="dashboard-item">';

            $slider_layout = !empty(get_config($dbc, 'tasks_slider_layout')) ? get_config($dbc, 'tasks_slider_layout') : 'accordion';

            if($slider_layout == 'accordion') {
                    echo '<h4><a href="" onclick="overlayIFrameSlider(\'../Tasks_Updated/add_task.php?type='.$row['status'].'&tasklistid='.$row['tasklistid'].'\', \'50%\', false, false, $(\'.iframe_overlay\').closest(\'.container\').outerHeight() + 20); return false;">Task #'. $row['tasklistid'] .': '. $row['heading'] .'</a></h4>';
            } else {
                echo '<h4><a href="../Tasks_Updated/add_task_full_view.php?type='.$row['status'].'&tasklistid='.$row['tasklistid'].'">Task #'. $row['tasklistid'] .': '. $row['heading'] .'</a></h4>';
            }

            echo '</div>';
        }
        echo '<div class="'.($pageNum==1 ? '' : 'gap-left').'">'; echo display_pagination($dbc, $query, $pageNum, $rowsPerPage); echo '</div>';

    } else {
        echo '<h3>No Records Found.</h3>';
    }
?>
</div>