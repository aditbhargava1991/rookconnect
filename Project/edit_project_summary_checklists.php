<?php 
    include_once('../include.php');
    error_reporting(0);
    if($_GET['edit'] > 0) {
        $projectid = $_GET['edit'];
        $checklists = mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `projectid`='$projectid' AND deleted = 0");
        if(mysqli_num_rows($checklists) > 0) {
?>
            <table class="table table-bordered">
                <tr class="hidden-sm hidden-xs">
                    <th>Checklist Type</th>
                    <th>Checklist Name</th>
                </tr>
                <?php while($rows = mysqli_fetch_array($checklists)) { ?>
                    <tr>
                        <td data-title="Checklist Type"><?= $row['checklist_type'] ?></td>
                        <td data-title="Checklist Name"><?= $row['checklist_name'] ?></td>
                    </tr>
                <?php } ?>
            </table>
    <?php
        } else {
            echo "<h2>Please add Checklists Details in order to see a Summary of the Project Checklists.</h2>";
        }
    } 
    ?>