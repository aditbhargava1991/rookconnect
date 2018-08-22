<div class="notice double-gap-bottom popover-examples">
    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11"><span class="notice-name">NOTE:</span>
    <?php if($_GET['category'] == 'inactive') { ?>
    In this section you can review all Inactive and Expired rates that your company has offered. Once a rate for a labour expires, it's logged here for your records and ongoing review.
    <?php } else { ?>
    The Rate Card tile is where you assign price points of all labour being offered by your business. In the Active Rate Card section you can review all current rates being offered by your business.<br>
    Click Edit to add end dates for current rates, and click Add Rate Card to add new price points for labour. In this section you can add multiple rates for each labour by selected start and end dates, ensuring of course that prices don't overlap. You must assign an effective start date for a rate in order to have an active rate for a labour.
    <?php } ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class='main_frame' id='no-more-tables'>
	<?php
    $rowsPerPage = 25;
    $pageNum = 1;
    if(isset($_GET['page'])) {
        $pageNum = $_GET['page'];
    }
    $offset = ($pageNum - 1) * $rowsPerPage;

    if(isset($_GET['t'])) {
        $scat = $_GET['t'];
        $scat_query = "AND `category` = '$scat'";
    } else {
        $scat = '';
        $scat_query = "";
    }

    $db_config = ',category,heading,start_date,end_date,price,'.get_config($dbc, 'task_db_rate_fields').',';
	$edit_security = vuaed_visible_function($dbc, 'rate_card');

    if($_GET['category'] == 'inactive') {
        $sql1 = "SELECT `r`.*, `t`.`category`, `t`.`description` `task_name` FROM `tile_rate_card` `r` LEFT JOIN `task_types` `t` ON `r`.`item_id` = `= `t`.`id` WHERE `r`.`deleted` = 0 AND DATE(`r`.`end_date`) != '0000-00-00' AND (DATE(`r`.`end_date`) <= DATE(NOW()) OR DATE(`r`.`start_date`) > DATE(NOW())) AND `r`.`tile_name` LIKE 'Tasks' $scat_query LIMIT $offset, $rowsPerPage";
        $count_sql = "SELECT COUNT(`r`.`companyrcid`) numrows FROM `company_rate_card` `r` LEFT JOIN `task_types` `t` ON `r`.`item_id` = `t`.`id` WHERE `r`.`deleted` = 0 AND DATE(`r`.`end_date`) != '0000-00-00' AND (DATE(`r`.`end_date`) <= DATE(NOW()) OR DATE(`r`.`start_date`) > DATE(NOW())) AND `r`.`tile_name` LIKE 'Tasks' $scat_query";
    } else {
        $sql1 = "SELECT `t`.`id`, `t`.`category`, `t`.`description` `task_name`, `r`.* FROM `task_types` `t` LEFT JOIN `company_rate_card` `r` ON `t`.`id`=`r`.`item_id` AND `r`.`tile_name` LIKE 'Tasks' WHERE `t`.`deleted`=0 AND IFNULL(`r`.`deleted`,0)=0 AND IFNULL(`start_date`,'0000-00-00') <= DATE(NOW()) AND IFNULL(NULLIF(`r`.`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) $scat_query LIMIT $offset, $rowsPerPage";
        $count_sql = "SELECT COUNT(*) numrows FROM `task_types` `t` LEFT JOIN `company_rate_card` `r` ON `t`.`id`=`r`.`item_id` AND `r`.`tile_name` LIKE 'Tasks' WHERE `t`.`deleted`=0 AND IFNULL(`r`.`deleted`,0)=0 AND IFNULL(`start_date`,'0000-00-00') <= DATE(NOW()) AND IFNULL(NULLIF(`r`.`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) $scat_query";
    }
	
	$result = mysqli_query($dbc, $sql1);
	if(mysqli_num_rows($result) > 0) {
		// Pagination Configuration
		echo display_pagination($dbc, $count_sql, $pageNum, $rowsPerPage);

		 ?>
		<table id="no-more-tables" class="table table-bordered table-striped">
            <thead>
			<tr class="hidden-xs hidden-sm">
				<th>Rate Card</th>
                <?php if(strpos($db_config, ',category,') !== FALSE) { ?>
                    <th>Category</th>
                <?php } ?>
                <?php if(strpos($db_config, ',heading,') !== FALSE) { ?>
                    <th>Heading</th>
                <?php } ?>
                <?php if(strpos($db_config, ',start_date,') !== FALSE) { ?>
                    <th>Start Date</th>
                <?php } ?>
                <?php if(strpos($db_config, ',end_date,') !== FALSE) { ?>
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
                <?php if(strpos($db_config, ',price,') !== FALSE) { ?>
                    <th>Price</th>
                <?php } ?>
                <?php if($edit_security == 1) { ?>
                    <th>History</th>
                    <th>Function</th>
                <?php } ?>
			</tr>
            </thead>
		<?php // Table Rows
		while($row = mysqli_fetch_array($result)) { ?>
            <tr>
				<td data-title="Rate Card"><?= $row['rate_card_name'] ?></td>
                <?php if(strpos($db_config, ',category,') !== FALSE) { ?>
                    <td data-title="Category"><?= $row['category'] ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',heading,') !== FALSE) { ?>
                    <td data-title="Heading"><?= $row['task_name'] ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',start_date,') !== FALSE) { ?>
                    <td data-title="Start Date"><?= $row['start_date'] ?></td>
                <?php } ?>
                <?php if(strpos($db_config, ',end_date,') !== FALSE) { ?>
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
                <?php if(strpos($db_config, ',price,') !== FALSE) { ?>
                    <td data-title="Price"><?= $row['cust_price'] ?></td>
                <?php } ?>
                <?php if($edit_security == 1 && $row['companyrcid'] > 0) { ?>
                    <td data-title="History" data-id="<?= $row['companyrcid'] ?>" class="history-link"><a>View Changes</a></td>
					<td data-title="Function"><a href="?type=tasks&card=tasks&status=add&t=<?= $_GET['t'] ?>&id=<?= $row['companyrcid'] ?>">Edit</a> | <a href="" data-id="<?= $row['companyrcid'] ?>" onclick="deleteRateCard(this); return false;">Delete</a></td>
                <?php } else if($edit_security == 1) { ?>
                    <td data-title="History"></td>
                    <td data-title="Function"><a href="?type=tasks&card=tasks&status=add&t=<?= $_GET['t'] ?>&task=<?= $row['id'] ?>">Create</a></td>
                <?php } ?>
            </tr>
		<?php } ?>
		</table>

		<?php // End Pagination
		echo display_pagination($dbc, $count_sql, $pageNum, $rowsPerPage);
	} else {
	    echo "<h2>No Record Found.</h2>";
	} ?>
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
	$('#iframe_instead_of_window').attr('src', 'rate_card.php?card=labour&status=history&id='+id);
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
function deleteRateCard(a) {
    var ratecardid = $(a).data('id');
    $.ajax({
        url: '../Rate Card/ratecard_ajax_all.php?fill=delete_rate_card',
        method: 'POST',
        data: { ratecardid: ratecardid },
        success: function(response) {
            $(a).closest('tr').remove();
        }
    });
}
</script>
<style type='text/css'>
	.history-link {
		cursor: pointer;
	}
</style>