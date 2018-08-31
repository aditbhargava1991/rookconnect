<!-- Daysheet My Sales -->
<?php
	$rowsPerPage = $_GET['pagerows'] > 0 ? $_GET['pagerows'] : 25;
	$_GET['page'] = $_GET['page'] ?: 1;
	$offset = ($_GET['page'] > 0 ? $_GET['page'] - 1 : 0) * $rowsPerPage;
    $sales_query = "SELECT * FROM `sales` WHERE `deleted`=0 AND CONCAT(',',IFNULL(`primary_staff`,''),',',IFNULL(`share_lead`,''),',') LIKE  '%".$_SESSION['contactid']."%' ORDER BY `created_date` DESC LIMIT $offset, $rowsPerPage";
    $sales_result = mysqli_query($dbc, $sales_query);
	if($sales_result->num_rows == 0 && $_GET['page'] > 1) {
		$_GET['page'] = 1;
		$offset = 0;
		$sales_query = "SELECT * FROM `sales` WHERE `deleted`=0 AND CONCAT(',',IFNULL(`primary_staff`,''),',',IFNULL(`share_lead`,''),',') LIKE  '%".$_SESSION['contactid']."%' ORDER BY `created_date` DESC LIMIT $offset, $rowsPerPage";
		$sales_result = mysqli_query($dbc, $sales_query);
	}
    $num_rows = mysqli_num_rows($sales_result);
?>
    <div class="col-xs-12">
        <div class="weekly-div" style="overflow-y: hidden;">
            <?php if($num_rows > 0) {
				display_pagination($dbc, "SELECT COUNT(*) `numrows` FROM `sales` WHERE `deleted`=0 AND CONCAT(',',IFNULL(`primary_staff`,''),',',IFNULL(`share_lead`,''),',') LIKE  '%".$_SESSION['contactid']."%'", $_GET['page'], $rowsPerPage, true, 25);
                echo '<ul class="option-list">';
                while($row = mysqli_fetch_array( $sales_result )) { ?>
                    <div class="horizontal-block">
                        <div class="horizontal-block-header">
                            <span class="flag-label"><?= $flag_label ?></span>
                            <h4 class="col-md-6"><a href="../Sales/sale.php?p=preview&id=<?= $row['salesid'] ?>" onclick="overlayIFrameSlider(this.href+'&iframe_slider=1','auto',true,true); return false;">Sales Lead <?= $row['salesid']; ?> <img class="inline-img" src="../img/icons/ROOK-edit-icon.png"></a></h4>
                            <div class="col-md-6"></div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="horizontal-block-details">
                            <div class="col-xs-12 col-md-4">
                                <div class="col-xs-6 default-color">Business:</div>
                                <div class="col-xs-6"><?= get_client($dbc, $row['businessid']); ?></div>
                                <div class="clearfix"></div>
                                <div class="col-xs-6 default-color">Contact:</div>
                                <div class="col-xs-6"><?= get_contact($dbc, $row['contactid']); ?></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="col-xs-6 default-color">Lead Status:</div>
                                <div class="col-xs-6"><?= $row['status'] ?></div>
                                <div class="clearfix"></div>
                                <div class="col-xs-6 default-color">Next Action:</div>
                                <div class="col-xs-6"><?= $row['next_action'] ?></div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="col-xs-6 default-color">Total Due:</div>
                                <div class="col-xs-6">$<?= ( $row['lead_value'] > 0 ) ? number_format($row['lead_value']) : '0.00'; ?></div>
                                <div class="clearfix"></div>
                                <div class="col-xs-6 default-color">Date:</div>
                                <div class="col-xs-6"><?= ( $row['new_reminder']!='0000-00-00' || !empty($row['new_reminder']) ) ? $row['new_reminder'] : 'YYYY-MM-DD'; ?></div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
					<?php echo '<div class="clearfix"></div><hr></div>';
                }
                echo '</ul>';
				display_pagination($dbc, "SELECT COUNT(*) `numrows` FROM `sales` WHERE `deleted`=0 AND CONCAT(',',IFNULL(`primary_staff`,''),',',IFNULL(`share_lead`,''),',') LIKE  '%".$_SESSION['contactid']."%'", $_GET['page'], $rowsPerPage, true, 25);
            } else {
                echo "<h2>No Record Found.</h2>";
            } ?>
        </div>
    </div>