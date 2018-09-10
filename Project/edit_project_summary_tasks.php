<?php 
    include_once('../include.php');
    error_reporting(0);
    if($_GET['edit'] > 0) {
        $projectid = $_GET['edit'];
        $tasks = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `projectid`='$projectid' AND deleted = 0");
        if(mysqli_num_rows($tasks) > 0) {
?>
            <table class="table table-bordered">
                <tr class="hidden-sm hidden-xs">
                    <th>Task</th>
                    <th>To do date</th>
                    <th>Status</th>
                    <th>Status Date</th>
                </tr>
                <?php while($rows = mysqli_fetch_array($tasks)) { ?>
                    <tr>
                        <td data-title="Task"><?= $row['task'] ?></td>
                        <td data-title="To do date"><?= $row['task_tododate'] ?></td>
                        <td data-title="Status"><?= $row['status'] ?></td>
                        <td data-title="Status"><?= $row['status_date'] ?></td>
                    </tr>
                <?php } ?>
            </table>
    <?php
        } else {
            echo "<h2>Please add Task Details in order to see a Summary of the Project Tasks.</h2>";
        }
    } 
    ?>