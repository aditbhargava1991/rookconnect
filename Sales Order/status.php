<!-- Sales Status --><?php

/* BEGIN For status_mobile.php */
error_reporting(0);
include_once('../include.php');
checkAuthorised('sales_order');
$statuses      = (!empty(get_config($dbc, 'sales_order_statuses'))) ? get_config($dbc, 'sales_order_statuses') : 'Opportunity,With Client,Fulfillment';
if(strpos(','.$statuses.',','Complete') === FALSE) {
    $statuses = trim($statuses,',').',Complete';
}
$next_actions  = (!empty(get_config($dbc, 'sales_order_next_actions'))) ? get_config($dbc, 'sales_order_next_actions') : 'Phone Call,Email';
/* END For status_mobile.php */ ?>

<script type="text/javascript">
$(document).on('change', 'select[name="status"]', function() { changeStatus(this); });
$(document).on('change', 'select[name="next_action"]', function() { changeNextAction(this); });
</script>

<?php
if(isset($_GET['s'])) {
    $page_title = $_GET['s'];
} else if(isset($_GET['r'])) {
    $page_title = $_GET['r'];
} else if(isset($_GET['l'])) {
    $page_title = $_GET['l'];
} else if(isset($_GET['c'])) {
    $page_title = $_GET['c'];
}
$filter = "1=1";
if(isset($_GET['s'])) {
	$filter .= " AND `status`='".filter_var($_GET['s'],FILTER_SANITIZE_STRING)."'";
} else if(isset($_GET['r'])) {
	$filter .= " AND `region`='".filter_var($_GET['r'],FILTER_SANITIZE_STRING)."'";
} else if(isset($_GET['l'])) {
	$filter .= " AND `location`='".filter_var($_GET['l'],FILTER_SANITIZE_STRING)."'";
} else if(isset($_GET['c'])) {
	$filter .= " AND `classification`='".filter_var($_GET['c'],FILTER_SANITIZE_STRING)."'";
}
$filter .= " AND `deleted` = 0 AND `status` != 'Archive'";
$sales_order_temp = mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE ".$filter.$query_mod);
$i = 1;
echo '<div class="main-screen-white horizontal-scroll standard-dashboard-body" style="border: none; background: none;">';
echo '<div class="standard-dashboard-body-title"><h3>'.$page_title.'</h3></div>';
if ( $sales_order_temp->num_rows > 0 ) {
    while ( $row=mysqli_fetch_assoc($sales_order_temp) ) {
        $cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts` WHERE `sales_order_type` = '".$row['sales_order_type'])); ?>
        <div class="sales-order-info main-screen-white silver-border gap-bottom" style="height:auto;" data-searchable="<?= get_client($dbc, $row['customerid']); ?> <?= get_contact($dbc, $row['customerid']); ?>" data-type="sot">
            <div class="col-xs-12 gap-top horizontal-block-container">
                <div class="horizontal-block">
                    <div class="horizontal-block-header">
                        <h4 class="col-md-6"><a href="index?p=preview&sotid=<?= $row['sotid'] ?>"><?= !empty($row['name']) ? $row['name'] : 'Sales Order Form #'.$row['sotid'] ?> <img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a></h4>
                        <div class="col-md-6"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="horizontal-block-details">
                        <div class="col-xs-12 col-md-4">
                            <div class="col-xs-6 col-sm-4 default-color">Customer:</div>
                            <div class="col-xs-6 col-sm-8"><?= !empty(get_client($dbc, $row['customerid'])) ? get_client($dbc, $row['customerid']) : get_contact($dbc, $row['customerid']); ?></div>
                            <div class="clearfix"></div>
                            
                            <?php if (strpos($value_config, ',Classification,') !== FALSE) { ?>
                                <div class="col-xs-6 col-sm-4 default-color">Classification:</div>
                                <div class="col-xs-6 col-sm-8"><?= !empty($row['classification']) ? $row['classification'] : '-' ?></div>
                                <div class="clearfix"></div>
                            <?php } ?>
                                
                            <?php if (strpos($value_config, ',Business Contact,') !== FALSE) { ?>
                                <div class="col-xs-6 col-sm-4 default-color">Contact:</div>
                                <div class="col-xs-6 col-sm-8"><?php
                                    foreach (explode(',',$row['business_contact']) as $contact) {
                                        echo get_contact($dbc, $contact).'<br>';
                                    } ?>
                                </div>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="col-xs-6 default-color">Order Status:</div>
                            <div class="col-xs-6">
                                <select id="status_<?= $row['sotid']; ?>" data-placeholder="Select a Status" name="status" class="form-control chosen-select-deselect">
                                    <option value=""></option><?php
                                    foreach ( explode(',', $statuses) as $status_list ) {
                                        $selected = ($status_list==$_GET['s']) ? 'selected="selected"' : '';
                                        echo '<option '. $selected .' value="'. $status_list .'">'. $status_list .'</li>';
                                    } ?>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                                
                            <?php if (strpos($value_config, ',Next Action,') !== FALSE) { ?>
                                <div class="col-xs-6 default-color">Next Action:</div>
                                <div class="col-xs-6">
                                    <select id="action_<?= $row['sotid']; ?>" data-placeholder="Select Next Action" name="next_action" class="form-control chosen-select-deselect">
                                        <option value=""></option><?php
                                        foreach ( explode(',', $next_actions) as $next_action ) {
                                            $selected = ($next_action==$row['next_action']) ? 'selected="selected"' : '';
                                            echo '<option '. $selected .' value="'. $next_action .'">'. $next_action .'</li>';
                                        } ?>
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="col-xs-6 default-color">Total Price:</div>
                            <div class="col-xs-6">N/A</div>
                            <div class="clearfix"></div>
                                
                            <?php if (strpos($value_config, ',Next Action Follow Up Date,') !== FALSE) { ?>
                                <div class="col-xs-6 default-color">Next Action Date:</div>
                                <div class="col-xs-6"><input onchange="changeNextActionDate(this)" type="text" id="date_<?= $row['posid']; ?>" name="date" class="datepicker form-control" value="<?= ( $row['next_action_date']!='0000-00-00' || !empty($row['next_action_date']) ) ? $row['next_action_date'] : 'YYYY-MM-DD'; ?>" /></div>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div><!-- .horizontal-block-container -->
            <div class="clearfix"></div>
        </div><!-- .main-screen-white --><?php
                
        $i++;
    } ?><?php
}
$sales_orders = mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE ".$filter.$query_mod);
if ( $sales_orders->num_rows > 0 ) {
    while ( $row=mysqli_fetch_assoc($sales_orders) ) { ?>
        <div class="sales-order-info main-screen-white silver-border gap-bottom" style="height:auto;" data-searchable="<?= get_client($dbc, $row['contactid']); ?> <?= get_contact($dbc, $row['contactid']); ?>" data-type="so">
            <div class="col-xs-12 gap-top horizontal-block-container">
                <div class="horizontal-block">
                    <div class="horizontal-block-header">
                        <h4 class="col-md-6"><a href="index.php?p=preview&id=<?= $row['posid'] ?>"><?= !empty($row['name']) ? $row['name'] : 'Sales Order #'.$row['posid'] ?> <img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a></h4>
                        <div class="col-md-6"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="horizontal-block-details">
                        <div class="col-xs-12 col-md-4">
                            <div class="col-xs-6 col-sm-4 default-color">Customer:</div>
                            <div class="col-xs-6 col-sm-8"><?= !empty(get_client($dbc, $row['contactid'])) ? get_client($dbc, $row['contactid']) : get_contact($dbc, $row['contactid']); ?></div>
                            <div class="clearfix"></div>
                                
                            <?php if (strpos($value_config, ',Classification,') !== FALSE) { ?>
                                <div class="col-xs-6 col-sm-4 default-color">Classification:</div>
                                <div class="col-xs-6 col-sm-8"><?= !empty($row['classification']) ? $row['classification'] : '-' ?></div>
                                <div class="clearfix"></div>
                            <?php } ?>
                                
                            <?php if (strpos($value_config, ',Business Contact,') !== FALSE) { ?>
                                <div class="col-xs-6 col-sm-4 default-color">Contact:</div>
                                <div class="col-xs-6 col-sm-8"><?php
                                    foreach (explode(',',$row['business_contact']) as $contact) {
                                        echo get_contact($dbc, $contact).'<br>';
                                    } ?>
                                </div>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="col-xs-6 default-color">Order Status:</div>
                            <div class="col-xs-6">
                                <select id="status_<?= $row['posid']; ?>" data-placeholder="Select a Status" name="status" class="form-control chosen-select-deselect">
                                    <option value=""></option><?php
                                    foreach ( explode(',', $statuses) as $status_list ) {
                                        $selected = ($status_list==$_GET['s']) ? 'selected="selected"' : '';
                                        echo '<option '. $selected .' value="'. $status_list .'">'. $status_list .'</li>';
                                    } ?>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                                
                            <?php if (strpos($value_config, ',Next Action,') !== FALSE) { ?>
                                <div class="col-xs-6 default-color">Next Action:</div>
                                <div class="col-xs-6">
                                    <select id="action_<?= $row['posid']; ?>" data-placeholder="Select Next Action" name="next_action" class="form-control chosen-select-deselect">
                                        <option value=""></option><?php
                                        foreach ( explode(',', $next_actions) as $next_action ) {
                                            $selected = ($next_action==$row['next_action']) ? 'selected="selected"' : '';
                                            echo '<option '. $selected .' value="'. $next_action .'">'. $next_action .'</li>';
                                        } ?>
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="col-xs-6 default-color">Total Price:</div>
                            <div class="col-xs-6">$<?= ( $row['total_price'] > 0 ) ? number_format($row['total_price'], 2) : '0.00'; ?></div>
                            <div class="clearfix"></div>
                                
                            <?php if (strpos($value_config, ',Next Action Follow Up Date,') !== FALSE) { ?>
                                <div class="col-xs-6 default-color">Next Action Date:</div>
                                <div class="col-xs-6"><input onchange="changeNextActionDate(this)" type="text" id="date_<?= $row['posid']; ?>" name="date" class="datepicker form-control" value="<?= ( $row['next_action_date']!='0000-00-00' || !empty($row['next_action_date']) ) ? $row['next_action_date'] : 'YYYY-MM-DD'; ?>" /></div>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div><!-- .horizontal-block-container -->
            <div class="clearfix"></div>
        </div><!-- .main-screen-white --><?php
                
        $i++;
    } ?><?php
}

if ($i == 1) { ?>
    <div class="standard-dashboard-body-content">
        <h4>No Records Found.</h4>
    </div><?php
} ?>
</div>
<div class="clearfix"></div>

<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'status.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>