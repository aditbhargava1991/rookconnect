<?php 
    include_once('../include.php');
    error_reporting(0);
    if($_GET['edit'] > 0) {
        $projectid = $_GET['edit'];
        $project = mysqli_query($dbc, "SELECT projection_service_heading, projection_service_price, projection_product_heading, projection_product_price, projection_task_heading, projection_task_price, projection_inventory_heading, projection_inventory_price, projection_admin_heading, projection_admin_price FROM `project` WHERE `projectid`='$projectid' AND deleted = 0");
        if(mysqli_num_rows($project) > 0) {
?>
            <table class="table table-bordered">
                <tr class="hidden-sm hidden-xs">
                    <th></th>
                    <th>Heading</th>
                    <th>Price</th>
                </tr>
                <?php $rows = mysqli_fetch_array($project); ?>
                    <tr>
                        <td data-title="Projection Type">Service</td>
                        <td data-title="Projection Service Heading"><?= $row['projection_service_heading'] ?></td>
                        <td data-title="Projection Service Price"><?= number_format($row['projection_service_price'],2) ?></td>
                    </tr>
                    <tr>
                        <td data-title="Projection Type">Product</td>
                        <td data-title="Projection Product Heading"><?= $row['projection_product_heading'] ?></td>
                        <td data-title="Projection Product Price"><?= number_format($row['projection_product_price'],2) ?></td>
                    </tr>
                    <tr>
                        <td data-title="Projection Type">Task</td>
                        <td data-title="Projection Task Heading"><?= $row['projection_task_heading'] ?></td>
                        <td data-title="Projection Task Price"><?= number_format($row['projection_task_price'],2) ?></td>
                    </tr>
                    <tr>
                        <td data-title="Projection Type">Inventory</td>
                        <td data-title="Projection Inventory Heading"><?= $row['projection_inventory_heading'] ?></td>
                        <td data-title="Projection Inventory Price"><?= number_format($row['projection_inventory_price'],2) ?></td>
                    </tr>
                    <tr>
                        <td data-title="Projection Type">Admin</td>
                        <td data-title="Projection Admin Heading"><?= $row['projection_admin_heading'] ?></td>
                        <td data-title="Projection Admin Price"><?= number_format($row['projection_admin_price'],2) ?></td>
                    </tr>
            </table>
    <?php
        } else {
            echo "<h2>Please add Projection Details in order to see a Summary of the Project Projections.</h2>";
        }
    } 
    ?>