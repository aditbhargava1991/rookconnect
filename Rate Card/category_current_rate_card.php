<div class='main_frame' id='no-more-tables'>
	<?php
	$db_config = get_config($dbc, 'cat_db_rate_fields');
	if(str_replace(',','',$db_config) == '') {
		$db_config = ",heading,uom,price,";
	}
	// Pagination Configuration
	$rowsPerPage = 25;
	$pageNum = 1;
	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	$sql = "SELECT * FROM `company_rate_card` WHERE `deleted` = 0 AND `tile_name` LIKE 'Equipment' AND `item_id`=0 AND `description` IN (SELECT `type` FROM `equipment` WHERE `deleted`=0) ORDER BY `description` LIMIT $offset, $rowsPerPage";
	$count_sql = "SELECT COUNT(`companyrcid`) numrows FROM `company_rate_card` WHERE `deleted` = 0 AND `tile_name` LIKE 'Equipment' AND `item_id`=0 AND `description` IN (SELECT `type` FROM `equipment` WHERE `deleted`=0)";
	$edit_security = vuaed_visible_function($dbc, 'rate_card');
	$result = mysqli_query($dbc, $sql);
	echo mysqli_error($dbc);
	if(mysqli_num_rows($result) > 0):
		echo display_pagination($dbc, $count_sql, $pageNum, $rowsPerPage);
		
		// Table Headers ?>
		<table class="table table-bordered table-striped">
            <thead>
                <tr class="hidden-xs hidden-sm">
                    <th>Rate Card</th>
                    <th>Category</th>
                    <?php if(strpos($db_config, ',start_end_dates,') !== FALSE) { ?>
                        <th>Start Date</th>
                    <?php } ?>
                    <?php if(strpos($db_config, ',start_end_dates,') !== FALSE) { ?>
                        <th>End Date</th>
                    <?php } ?>
                    <?php if(strpos($db_config, ',alert_date,') !== FALSE) { ?>
                        <th>Alert Date</th>
                    <?php } ?>
                    <?php if(strpos($db_config, ',alert_staff,') !== FALSE) { ?>
                        <th>Alert Staff</th>
                    <?php } ?>
                    <?php if(strpos($db_config, ',created_by,') !== FALSE) { ?>
                        <th>Created By</th>
                    <?php } ?>
                    <?php if(strpos($db_config, ',uom,') !== FALSE) { ?>
                        <th>UOM</th>
                    <?php } ?>
                    <?php if(strpos($db_config, ',cost,') !== FALSE) { ?>
                        <th>Cost</th>
                    <?php } ?>
                    <?php if(strpos($db_config, ',margin,') !== FALSE) { ?>
                        <th>Profit %</th>
                    <?php } ?>
                    <?php if(strpos($db_config, ',profit,') !== FALSE) { ?>
                        <th>Profit $</th>
                    <?php } ?>
                    <?php if(strpos($db_config, ',unit_price,') !== FALSE) { ?>
                        <th>Price</th>
                    <?php } ?>
                    <?php if($edit_security == 1) { ?>
                        <th>History</th>
                        <th>Function</th>
                    <?php } ?>
                </tr>
            </thead>
		<?php // Table Rows
		while($row = mysqli_fetch_array($result)) {
			echo '<tr>'; ?>
				<td data-title="Rate Card"><?= $row['rate_card_name'] ?></td>
				<td data-title="Category"><?= $row['description'] ?></td>
                <?php if(strpos($db_config, ',start_end_dates,') !== FALSE) { ?>
                    <td data-title="Start Date"><?= $row['start_date'] ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',start_end_dates,') !== FALSE) { ?>
                    <td data-title="End Date"><?= $row['end_date'] ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',alert_date,') !== FALSE) { ?>
                    <td data-title="Alert Date"><?= $row['alert_date'] ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',alert_staff,') !== FALSE) { ?>
                    <td data-title="Alert Staff">
                        <?php $staff_list = [];
                        foreach(explode(',',$row['alert_staff']) as $staffid) {
                            if($staffid > 0) {
                                $staff_list[] = get_contact($dbc, $staffid);
                            }
                        }
                        echo implode(', ',$staff_list); ?>
                    </td>
                <?php } ?>
                <?php if(strpos($db_config, ',created_by,') !== FALSE) { ?>
                    <td data-title="Created By"><?= get_contact($dbc,$row['created_by']) ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',uom,') !== FALSE) { ?>
                    <td data-title="UOM"><?= $row['uom'] ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',cost,') !== FALSE) { ?>
                    <td data-title="Cost"><?= $row['cost'] ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',margin,') !== FALSE) { ?>
                    <td data-title="Profit %"><?= $row['margin'] ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',profit,') !== FALSE) { ?>
                    <td data-title="Profit $"><?= $row['profit'] ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',unit_price,') !== FALSE) { ?>
                    <td data-title="Price"><?= $row['cust_price'] ?></td>
                <?php } ?>
                <?php if($edit_security == 1 && $row['companyrcid'] > 0) { ?>
                    <td data-title="History" data-id="<?= $row['companyrcid'] ?>" class="history-link"><a>View Changes</a></td>
					<td data-title="Function"><a href="?type=category&card=category&status=add&id=<?= $row['companyrcid'] ?>">Edit</a> | <a href="" data-id="<?= $row['companyrcid'] ?>" onclick="deleteRateCard(this); return false;">Delete</a></td>
                <?php } else if($edit_security == 1) { ?>
                    <td data-title="History"></td>
                    <td data-title="Function"><a href="?type=category&card=category&status=add&task=<?= $row['id'] ?>">Create</a></td>
                <?php } ?>
			<?php echo '</tr>';
		} ?>
		</table>
		
		<?php // End Pagination
		echo display_pagination($dbc, $count_sql, $pageNum, $rowsPerPage);
	else:
		echo "<h2>No Active Equipment Category Rate Cards Found.</h2>";
	endif; ?>
</div>
<div class='iframe_holder' id='history_frame' style='display:none;'>
	<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframe' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
	<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'>Rate Card History</span>
	<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="0; border:0;" src=""></iframe>
</div>
<script>
$(document).ready(function() {
	$('.history-link').click(iframe_history);
	$('.close_iframe').click(iframe_history_close);

	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		this.style.height = (this.contentWindow.document.body.offsetHeight + 80) + 'px';
	});
});
function iframe_history() {
	var id = $(this).data('id');
	$('#iframe_instead_of_window').attr('src', 'rate_card.php?card=category&status=history&id='+id);
	$('#history_frame').show();
	$('.main_frame').hide();
	$('#iframe_instead_of_window').on('load', function() {
		$(this).height($(this).get(0).contentWindow.document.body.scrollHeight);
	});
}
function iframe_history_close() {
	$('#history_frame').hide();
	$('.main_frame').show();
}
</script>
<style type='text/css'>
	.history-link {
		cursor: pointer;
	}
</style>