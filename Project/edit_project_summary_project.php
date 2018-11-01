<?php
    include_once('../include.php');
    error_reporting(0);
    if($_GET['edit'] > 0) {
        $projectid = $_GET['edit'];
        $project = mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid' AND deleted = 0");
        if(mysqli_num_rows($project) > 0) {
?>
            <table class="table table-bordered">
                <tr class="hidden-sm hidden-xs">
                    <th>Project Type</th>
                    <th>Project Name</th>
                    <th>Total Price</th>
                    <th>Status</th>
                </tr>
                <?php while($rows = mysqli_fetch_array($project)) { ?>
                    <tr>
                        <td data-title="Project Type"><?= $rows['projecttype'] ?></td>
                        <td data-title="Project Name"><?= $rows['project_name'] ?></td>
                        <td data-title="Total Price"><?= number_format($rows['total_price'],2) ?></td>
                        <td data-title="Status"><?= $rows['status'] ?></td>
                    </tr>
                <?php } ?>
            </table>
    <?php
        } else {
            echo "<h2>Please add Project Details in order to see a Summary of the Project.</h2>";
        }
    }
    ?>