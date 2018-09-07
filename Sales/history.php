<?php include_once('../include.php');
$salesid = filter_var($_GET['id'],FILTER_SANITIZE_STRING); ?>
<div class="accordion-block-details padded" id="tasks">
    <div class="accordion-block-details-heading"><h4>History<a href="../blank_loading_page.php" class="pull-right"><img class="inline-img" src="../img/icons/cancel.png"></a></h4></div>

    <div class="row set-row-height">
        <div class="col-xs-12"><?php
            $result = mysqli_query($dbc, "SELECT * FROM `sales_history` WHERE `salesid`='$salesid'");
            if ( $result->num_rows > 0 ) { ?>
				<table class="table table-bordered">
					<tr class="hidden-sm hidden-xs">
						<th>User</th>
						<th>Date</th>
						<th>Description</th>
					</tr>
					<?php while ( $row=mysqli_fetch_assoc($result) ) { ?>
						<tr>
							<td data-title="User"><?= $row['updated_by'] ?></td>
							<td data-title="Date"><?= date('Y-m-d', strtotime($row['created_date'])) ?></td>
							<td data-title="Description"><?= html_entity_decode($row['history']) ?></td>
						</tr>
					<?php } ?>
				</table>
            <?php } else {
                echo 'No Record Found.';
            }
        ?>
        </div>
        <div class="clearfix double-gap-bottom"></div>
    </div>

</div><!-- .accordion-block-details -->
