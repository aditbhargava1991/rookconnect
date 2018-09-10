<?php 
    include_once('../include.php');
    error_reporting(0);
    if($_GET['edit'] > 0) {
        $projectid = $_GET['edit'];
        $tickets = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `projectid`='$projectid' AND deleted = 0");
        if(mysqli_num_rows($tickets) > 0) {
?>
            <table class="table table-bordered">
                <tr class="hidden-sm hidden-xs">
                    <th>Ticket Type</th>
                    <th>Fee Amount</th>
                    <th>Status</th>
                    <th>Status Date</th>
                </tr>
                <?php while($rows = mysqli_fetch_array($tickets)) { ?>
                    <tr>
                        <td data-title="Ticket Type"><?= $row['ticket_type'] ?></td>
                        <td data-title="Fee Amount"><?= number_format($row['fee_amt'],2) ?></td>
                        <td data-title="Status"><?= $row['status'] ?></td>
                        <td data-title="Status"><?= $row['status_date'] ?></td>
                    </tr>
                <?php } ?>
            </table>
    <?php
        } else {
            echo "<h2>Please add Ticket Details in order to see a Summary of the Project Tickets.</h2>";
        }
    } 
    ?>