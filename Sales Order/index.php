<?php
/*
 * Sales Orders Tile Main Page
 * Main index page
 */
error_reporting(0);
include ('../include.php');
include_once('field_config_defaults.php');

//Auto archive old Sales Orders
$auto_archive_days = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_so`"))['auto_archive_days'];
$auto_archive_days = $auto_archive_days > 0 ? $auto_archive_days : 30;
$today_date = date('Y-m-d', strtotime(date('Y-m-d').' - '.$auto_archive_days.' days'));
$old_sales_orders = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `status` = 'Complete' AND `status_date` <= '$today_date' AND `status_date` != '0000-00-00'"),MYSQLI_ASSOC);
foreach ($old_sales_orders as $old_sales_order) {
    mysqli_query($dbc, "UPDATE `sales_order_temp` SET `status` = 'Archive' WHERE `sotid` = '".$old_sales_order['sotid']."'");
}
$old_sales_orders = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE `status` = 'Complete' AND `status_date` <= '$today_date' AND `status_date` != '0000-00-00'"),MYSQLI_ASSOC);
foreach ($old_sales_orders as $old_sales_order) {
    mysqli_query($dbc, "UPDATE `sales_order` SET `status` = 'Archive' WHERE `sotid` = '".$old_sales_order['posid']."'");
}

if(isset($_POST['copy_order'])) {
    $sales_order = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE `posid` = '".$_GET['id']."'"));
    $sot = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '".$sales_order['sotid']."'"));
    $sales_order_type = $sot['sales_order_type'];
    $sales_order_name = $sot['name'];
    $primary_staff = $sot['primary_staff'];
    $assign_staff = $sot['assign_staff'];
    $customerid = $sot['customerid'];
    $classification = $sot['classification'];
    $business_contact = $sot['business_contact'];
    $status = explode(',',get_config($dbc, "sales_order_statuses"))[0];
    $next_action = '';
    $next_action_date = '';
    $logo = $sot['logo'];
    $security_option = $sot['security_option'];
    $discount_type = $sot['discount_type'];
    $discount_value = $sot['discount_value'];
    $delivery_type = $sot['delivery_type'];
    $assembly_amount = $sot['assembly_amount'];
    $delivery_address = $sot['delivery_address'];
    $contractorid = $sot['contractorid'];
    $frequency = $sot['frequency'];
    $frequency_type = $sot['frequency_type'];
    $comment = $sot['comment'];
    $templateid = $sot['templateid'];
    $copied_sotid = $sot['copied_sotid'];

    mysqli_query($dbc, "INSERT INTO `sales_order_temp` (`name`, `sales_order_type`, `primary_staff`, `assign_staff`, `customerid`, `classification`, `business_contact`, `logo`, `security_option`, `discount_type`, `discount_value`, `delivery_type`, `assembly_amount`, `delivery_address`, `contractorid`, `comment`, `status`, `next_action`, `next_action_date`, `frequency`, `frequency_type`, `templateid`, `copied_sotid`) VALUES ('$sales_order_name', '$sales_order_type', '$primary_staff', '$assign_staff', '$customerid', '$classification', '$business_contact', '$logo', '$security_option', '$discount_type', '$discount_value', '$delivery_type', '$assembly_amount', '$delivery_address', '$contractorid', '$comment', '$status', '$next_action', '$next_action_date', '$frequency', '$frequency_type', '$templateid', '$copied_sotid')");
    $sotid = mysqli_insert_id($dbc);

    $sotp_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order_product_temp` WHERE `parentsotid` = '".$sot['sotid']."'"),MYSQLI_ASSOC);
    foreach ($sotp_list as $sotp) {
        $contactid = $_SESSION['contactid'];
        $pricing = $sotp['pricing'];
        $item_type = $sotp['item_type'];
        $item_type_id = $sotp['item_type_id'];
        $item_category = $sotp['item_category'];
        $item_name = $sotp['item_name'];
        $item_price = $sotp['item_price'];
        $contact_category = $sotp['contact_category'];
        $heading_name = $sotp['heading_name'];
        $mandatory_quantity = $sotp['mandatory_quantity'];
        $templateid = $sotp['templateid'];
        $copied_sotid = $sotp['copied_sotid'];
        $heading_sortorder = $sotp['heading_sortorder'];
        $sortorder = $sotp['sortorder'];

        mysqli_query($dbc, "INSERT INTO `sales_order_product_temp` (`contactid`, `pricing`, `item_type`, `item_type_id`, `item_category`, `item_name`, `item_price`, `quantity`, `contact_category`, `parentsotid`, `heading_name`, `mandatory_quantity`, `templateid`, `copied_sotid`, `heading_sortorder`, `sortorder`, `time_estimate`) VALUES ('$contactid', '$pricing', '$item_type', '$item_type_id', '$item_category', '$item_name', '$item_price', '$quantity', '$contact_category', '$sotid', '$heading_name', '$mandatory_quantity', '$templateid', '$copied_sotid', '$heading_sortorder', '$sortorder', '$time_estimate')");
    }

    $get_uploads = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order_upload_temp` WHERE `parentsotid` = '".$sot['sotid']."'"),MYSQLI_ASSOC);
    foreach ($get_uploads as $get_upload) {
        $name = $get_upload['name'];
        $file = $get_upload['file'];
        $added_by = $get_upload['added_by'];

        mysqli_query($dbc, "INSERT INTO `sales_order_upload_temp` (`parentsotid`, `name`, `file`, `added_by`) VALUES ('$sotid', '$name', '$file', '$added_by')");
    }

    echo '<script type="text/javascript">window.location.href = "order.php?p=details&sotid='.$sotid.'";</script>';
}
?>
<script type="text/javascript">
    $(document).ready(function() {
        if($(window).width() > 767) {
            //resizeScreen();
            $(window).resize(function() {
                //resizeScreen();
            });
        }
        
        $(window).resize(function() {
            var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('#sales_order_div .tile-container').offset().top - 2;
            if(available_height > 200) {
                $('#sales_order_div .tile-sidebar, #sales_order_div .tile-content').height(available_height);
            }
        }).resize();
    });
	function searchLeads(string) {
		$('[data-searchable]').hide();
		$('[data-searchable*="'+(string == '' ? ' ' : string)+'" i]').show();
	}
    
    function changeStatus(sel) {
        var type    = $(sel).closest('.sales-order-info').data('type');
        var id      = sel.id;
        var arr     = id.split('_');
        var soid    = arr[1];
        var status  = sel.value;
        
        $.ajax({
            type: "GET",
            url: "ajax.php?fill=changeStatus&soid="+soid+"&status="+status+"&type="+type,
            dataType: "html",
            success: function(response){
                window.location.reload();
            }
        });
    }
    
    function changeNextAction(sel) {
        var type       = $(sel).closest('.sales-order-info').data('type');
        var id         = sel.id;
        var arr        = id.split('_');
        var soid       = arr[1];
        var nextaction = sel.value;
        
        $.ajax({
            type: "GET",
            url: "ajax.php?fill=changeNextAction&soid="+soid+"&nextaction="+nextaction+"&type="+type,
            dataType: "html",
            success: function(response){}
        });
    }
    
    function changeNextActionDate(sel) {
        var type           = $(sel).closest('.sales-order-info').data('type');
        var id             = sel.id;
        var arr            = id.split('_');
        var soid           = arr[1];
        var nextActionDate = sel.value;
        
        $.ajax({
            type: "GET",
            url: "ajax.php?fill=changeNextActionDate&soid="+soid+"&nextActionDate="+nextActionDate+"&type="+type,
            dataType: "html",
            success: function(response){}
        });
    }

    function resizeScreen() {
        var view_height = $(window).height() > 800 ? $(window).height() : 800;
        $('#sales_order_div .scale-to-fill,#sales_order_div .scale-to-fill .main-screen,#sales_order_div .tile-sidebar').height($('#sales_order_div').height() - $('.tile-header').height() + 15);
    }
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
checkAuthorised('sales_order');
    $config_access = config_visible_function($dbc, 'sales_order');
    $statuses      = (!empty(get_config($dbc, 'sales_order_statuses'))) ? get_config($dbc, 'sales_order_statuses') : 'Opportunity,With Client,Fulfillment';
    if(strpos(','.$statuses.',', ',Complete,') === FALSE) {
        $statuses .= ',Complete';
    }
    $next_actions  = (!empty(get_config($dbc, 'sales_order_next_actions'))) ? get_config($dbc, 'sales_order_next_actions') : 'Phone Call,Email';
    $dashboard     = preg_replace('/[^0-9]/', '', $_GET['dashboard']);
    
    if ( !empty($dashboard) ) {
        $query_mod = " AND `created_by`='{$dashboard}'";
    } else {
        $query_mod = '';
    }

    $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_so`"));
    $value_config = ','.$field_config['dashboard_fields'].',';

    $security_query = " AND (`primary_staff` = '".$_SESSION['contactid']."' OR CONCAT(',',`assign_staff`,',') LIKE '%,".$_SESSION['contactid'].",%')";

    $security_access = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_security` WHERE CONCAT(',','".ROLE."',',') LIKE CONCAT('%,',`security_level`,',%')"),MYSQLI_ASSOC), 'access');
    if(in_array('ALL',$security_access) || !in_array('Assigned Only',$security_access)) {
        $security_query = "";
    }
?>

<div id="sales_order_div" class="container">
    <div class="iframe_overlay" style="display:none;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="row">
		<div class="main-screen"><?php
            include('tile_header.php'); ?>
            
            <div class="tile-container" style="height: 100%;">
                
                <!-- Stats --><?php
                $oppotunities = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(*) `count`, SUM(`total_price`) `value` FROM `sales_order` WHERE `deleted`=0".$security_query . $query_mod) );
                $closed = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(*) `count`, SUM(`total_price`) `value` FROM `sales_order` WHERE `deleted`=0 AND `status` IN ('won', 'Complete')".$security_query . $query_mod) ); ?>
                
                <div class="col-xs-12 collapsible-horizontal collapsed" id="summary-div">
                    <div class="col-xs-12 col-sm-6 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= ( $oppotunities['count'] > 0 ) ? $oppotunities['count'] : 0; ?></div>
                            <div>Total <?= SALES_ORDER_TILE ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg">$<?= ( $oppotunities['value'] > 0 ) ? number_format($oppotunities['value'], 2) : '0.00'; ?></div>
                            <div>Total Value of <?= SALES_ORDER_TILE ?></div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= ( $closed['count'] > 0 ) ? $closed['count'] : 0; ?></div>
                            <div>Closed Successfully</div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg">$<?= ( $closed['value'] > 0 ) ? number_format($closed['value'], 2) : '0.00'; ?></div>
                            <div>Total Value of Closed</div>
                        </div>
						<!-- <img class="pull-right inline-img" src="../img/icons/ROOK-minus-icon.png" onclick="$('#summary-div').hide();"> -->
                    </div>
                </div>
                
                <div class="clearfix"></div>
                
                <?php $page = preg_replace('/\PL/u', '', $_GET['p']); ?>
                
                <!-- Sidebar -->
                <div class="standard-collapsible tile-sidebar hide-titles-mob">
                    <ul>
						<li class="standard-sidebar-searchbox search-box"><input type="text" class="search-text form-control" placeholder="Search <?= SALES_ORDER_TILE ?>" onkeyup="searchLeads(this.value);"></li>
                        <li class="<?= ( $page=='dashboard' || empty($page) ) ? 'active' : '' ?>"><a href="index.php" style="display: block;">Dashboard</a></li>
                        <li class="standard-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_forms">All <?= SALES_ORDER_NOUN ?> Forms<span class="arrow"></span></a>
                            <ul id="collapse_forms" class="collapse"><?php
                            // Get Sales Order Forms that are not deleted
                            $sales_order_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `deleted` = 0 AND `status` != 'Archive' $security_query ORDER BY `sotid` DESC"),MYSQLI_ASSOC);
                            foreach ($sales_order_forms as $form) {
                                $cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts` WHERE `sales_order_type` = '".$form['sales_order_type']));
                                $form_name = $form['name'];
                                if(empty($form_name)) {
                                    $form_name = SALES_ORDER_NOUN.' Form #'.$form['sotid'];
                                }
                                echo '<li><a href="index.php?p=preview&sotid='.$form['sotid'].'">'.$form_name.'</a></li>';
                            } ?>
                            </ul>
                        </li>
                        <?php $sales_order_types = get_config($dbc, 'sales_order_types');
                        if(!empty($sales_order_types)) {
                            foreach(explode(',', $sales_order_types) as $sales_order_type) { ?>
                                <li class="sidebar-higher-level"><a class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_<?= config_safe_str($sales_order_type) ?>"><?= $sales_order_type ?><span class="arrow"></span></a>
                                    <ul id="collapse_<?= config_safe_str($sales_order_type) ?>" class="collapse"><?php
                                    // Get Sales Order Forms that are not deleted
                                    $sales_order_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `deleted` = 0 AND `status` != 'Archive' AND `sales_order_type` = '$sales_order_type' $security_query ORDER BY `sotid` DESC"),MYSQLI_ASSOC);
                                    foreach ($sales_order_forms as $form) {
                                        $cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts` WHERE `sales_order_type` = '".$form['sales_order_type']));
                                        $form_name = $form['name'];
                                        if(empty($form_name)) {
                                            $form_name = SALES_ORDER_NOUN.' Form #'.$form['sotid'];
                                        }
                                        echo '<li><a href="index.php?p=preview&sotid='.$form['sotid'].'">'.$form_name.'</a></li>';
                                    } ?>
                                    </ul>
                                </li>
                            <?php }
                        } ?>
                        <li class="sidebar-higher-level"><a class="<?= isset($_GET['s']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#collapse_status">Order Status<span class="arrow"></span></a>
							<ul id="collapse_status" class="collapse"><?php
								// Get Statuses added in Settings->Sales Order Statuses accordion
								foreach ( explode(',', $statuses) as $status ) {
									if ( trim($_GET['s']==$status) ) { ?>
										<script>
											$(document).ready(function() {
												$('#collapse_status').collapse('show');
											});
										</script><?php
									}
									echo '<li class="'.($_GET['s'] == $status ? 'active' : '').'"><a href="?p=filter&s='. $status .'">'. $status .'</a></li>';
								} ?>
							</ul>
						</li>
						<?php $regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
						if(count($regions) > 0) { ?>
							<li class="sidebar-higher-level"><a class="<?= isset($_GET['r']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#collapse_region">Region<span class="arrow"></span></a>
								<ul id="collapse_region" class="collapse"><?php
									foreach ( $regions as $region ) {
										if ( trim($_GET['r'] == $region) ) { ?>
											<script>
												$(document).ready(function() {
													$('#collapse_region').collapse('show');
												});
											</script><?php
										}
										echo '<li class="'.($_GET['r'] == $region ? 'active' : '').'"><a href="?p=filter&r='. $region .'">'. $region .'</a></li>';
									} ?>
								</ul>
							</li>
						<?php } ?>
						<?php $locations =  array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
						if(count($locations) > 0) { ?>
							<li class="sidebar-higher-level"><a class="<?= isset($_GET['l']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#collapse_location">Location<span class="arrow"></span></a>
								<ul id="collapse_location" class="collapse"><?php
									foreach ( $locations as $location ) {
										if ( trim($_GET['l'] == $location) ) { ?>
											<script>
												$(document).ready(function() {
													$('#collapse_location').collapse('show');
												});
											</script><?php
										}
										echo '<li class="'.($_GET['l'] == $location ? 'active' : '').'"><a href="?p=filter&l='. $location .'">'. $location .'</a></li>';
									} ?>
								</ul>
							</li>
						<?php } ?>
						<?php $classifications = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_classification'"))[0])));
						if(count($classifications) > 0) { ?>
							<li class="sidebar-higher-level"><a class="<?= isset($_GET['c']) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#collapse_classification">Classification<span class="arrow"></span></a>
								<ul id="collapse_classification" class="collapse"><?php
									foreach ( $classifications as $classification ) {
										if ( trim($_GET['c'] == $classification) ) { ?>
											<script>
												$(document).ready(function() {
													$('#collapse_classification').collapse('show');
												});
											</script><?php
										}
										echo '<li class="'.($_GET['c'] == $classification ? 'active' : '').'"><a href="?p=filter&c='. $classification .'">'. $classification .'</a></li>';
									} ?>
								</ul>
							</li>
						<?php } ?>
                    </ul>
                </div><!-- .tile-sidebar -->
                
                <!-- Main Screen -->
                <div class="scale-to-fill has-main-screen tile-content hide-titles-mob set-section-height"><?php
                    if ( $page=='filter' ) {
                        include('status.php');
                    } elseif ( $page=='preview' ) {
                        include('preview.php');
                    } else {
                        include('dashboard.php');
                    } ?>
                </div>
                <div class="col-xs-12 show-on-mob"><?php
					include('status_mobile.php');
				?></div><!-- .tile-content -->
                
                <div class="clearfix"></div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>