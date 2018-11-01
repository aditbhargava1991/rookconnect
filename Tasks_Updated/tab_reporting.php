<?php
    include ('../include.php');
checkAuthorised('tasks');
?>
<div class="standard-dashboard-body-content">
<div class="dashboard-item">

<div class="container"><?php
    $table_row_style = '';
    $table_style = '';
    $rowsPerPage = 10;
    $pageNum = 1;

    if(isset($_GET['page'])) {
        $pageNum = $_GET['page'];
    }

    $offset = ($pageNum - 1) * $rowsPerPage;

    $board_name = '';
    $staff = '';
    $startdate = date('Y-m-01');
    $enddate = date('Y-m-d');
    $query_mod_board = '';
    $query_mod_staff = '';

    if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
        $board_name = $_POST['board_name'];
        $staff = $_POST['staff'];
        $startdate = $_POST['startdate'];
        $enddate = !empty($_POST['enddate']) ? $_POST['enddate'] : date('Y-m-d');

        if ( !empty($board_name) ) {
            $query_mod_board = " AND task_board='$board_name'";
        }
        if ( !empty($staff) ) {
            $query_mod_staff = " AND contactid IN($staff)";
        }
        if ( !empty($startdate) ) {
            $query_mod_date = " AND (created_date BETWEEN '$startdate' AND '$enddate')";
        }

    }

    $query_mod_date = " AND (created_date BETWEEN '$startdate' AND '$enddate')";

    $result = mysqli_query($dbc,"SELECT * FROM tasklist WHERE deleted=0". $query_mod_board . $query_mod_staff . $query_mod_date ." ORDER BY tasklistid DESC LIMIT $offset, $rowsPerPage");
    $query = "SELECT COUNT(tasklistid) AS numrows FROM tasklist WHERE deleted=0". $query_mod_board . $query_mod_staff . $query_mod_date;

    $num_rows = mysqli_num_rows($result);

    echo '<div id="no-more-tables">'; ?>

        <form action="" method="post" class="form-horizontal">
            <div class="form-group gap-top">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><label class="control-label"><?= TASK_NOUN ?> Board:</label></div>
                        <div class="col-sm-6">
                            <select name="board_name" class="chosen-select-deselect" data-placeholder="Select <?= TASK_NOUN ?> Board...">
                                <option></option><?php
                                $board_result = mysqli_query($dbc, "SELECT taskboardid, board_name FROM task_board WHERE deleted=0 ORDER BY board_name");
                                if ( $board_result->num_rows > 0 ) {
                                    while ( $board_row=mysqli_fetch_assoc($board_result) ) {
                                        $selected = ($board_name==$board_row['taskboardid']) ? 'selected' : '';
                                        echo '<option '. $selected .' value="'. $board_row['taskboardid'] .'">'. $board_row['board_name'] .'</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-4"><label class="control-label">Staff:</label></div>
                        <div class="col-sm-6">
                            <select name="staff" class="chosen-select-deselect" data-placeholder="Select Staff...">
                                <option></option><?php
                                $staff_result = sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND status>0 AND deleted=0 AND (first_name<>'' OR last_name<>'')"));
                                foreach ($staff_result as $staff_row) {
                                    $selected = ($staff==$staff_row['contactid']) ? 'selected' : '';
                                    echo '<option '. $selected .' value="'. $staff_row['contactid'] .'">'. $staff_row['first_name'] .' '. $staff_row['last_name'] .'</option>';
                                }
                                /* if ( $staff_result->num_rows > 0 ) {
                                    while ( $staff_row=mysqli_fetch_assoc($staff_result) ) {
                                        $selected = ($staff==$staff_row['contactid']) ? 'selected' : '';
                                        echo '<option '. $selected .' value="'. $staff_row['contactid'] .'">'. decryptIt($staff_row['first_name']) .' '. decryptIt($staff_row['last_name']) .'</option>';
                                    }
                                } */ ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row gap-top">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><label class="control-label">From:</label></div>
                        <div class="col-sm-6">
                            <input type="text" name="startdate" value="<?= $startdate ?>" class="datepicker form-control" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-4"><label class="control-label">To:</label></div>
                        <div class="col-sm-6">
                            <input type="text" name="enddate" value="<?= $enddate ?>" class="datepicker form-control" />
                        </div>
                        <div class="col-sm-2"><input type="submit" name="submit" value="Submit" class="btn brand-btn" /></div>
                    </div>
                </div>
            </div>
        </form><?php

        if($num_rows > 0) {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            echo "<table class='table table-bordered'>";
            echo "<tr class='hidden-xs hidden-sm'>";

            echo '<th>Staff Name</th>';
            echo '<th>Date Created</th>';
            echo '<th>Task</th>';
            echo '<th>Task Start Date</th>';
            echo '<th>Task Status</th>';

            echo "</tr>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }

        while($row = mysqli_fetch_array( $result )) {
            echo "<tr>";
            $staffid = $row['contactid'];
            $staff = mysqli_fetch_array(mysqli_query($dbc,"SELECT first_name,last_name FROM contacts WHERE contactid='$staffid'"));
            if(($staff['first_name'] != null && $staff['first_name'] !='') || ($staff['last_name'] != null && $staff['last_name'] != ''))
                echo '<td data-title="Staff">'.decryptIt($staff['first_name']).' '.decryptIt($staff['last_name']).'</td>';
            else
                echo '<td data-title="Staff"></td>';

            if($row['created_date'] != null && $row['created_date'] != '')
                echo '<td data-title="Date Created">'.$row['created_date'].'</td>';
            else
                echo '<td data-title="Date Created"></td>';

            if($row['heading'] != null && $row['heading'] != '')
                echo '<td data-title="Task">'.$row['heading'].'</td>';
            else
                echo '<td data-title="Task"></td>';

            if($row['task_tododate'] != null && $row['task_tododate'] != '')
                echo '<td data-title="Start Date">'.$row['task_tododate'].'</td>';
            else
                echo '<td data-title="Start Date"></td>';

            if($row['task_milestone_timeline'] != null && $row['task_milestone_timeline'] != '')
                echo '<td data-title="Status">'.$row['task_milestone_timeline'].'</td>';
            else if($row['status'] != null && $row['status'] != '')
                echo '<td data-title="Status">'.$row['status'].'</td>';
            else
                echo '<td data-title="Status"></td>';
            echo "</tr>";
        }

        echo '</table>';

        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

    echo '</div><!-- #no-more-tables -->';
    ?>
</div><!-- .container -->

</div>
</div>