<?php
    error_reporting(0);

    include_once ('../database_connection.php');
    include_once ('../global.php');
    include_once ('../function.php');
    include_once ('../output_functions.php');
    include_once ('../email.php');
    include_once ('../user_font_settings.php');
    include_once ('../pagination.php');

    checkAuthorised('services');

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services_dashboard` FROM `field_config`"));
    $value_config = ','.$get_field_config['services_dashboard'].',';

    // Pagination Config
    $rowsPerPage = 10;
    $pageNum = 1;

    if ( isset($_GET['page']) ) {
        $pageNum = $_GET['page'];
    }
    $offset = ($pageNum - 1) * $rowsPerPage;
    
    $url_cat = isset($_GET['cat_mob']) && !empty($_GET['cat_mob']) ? trim(hex2bin($_GET['cat_mob'])) : $url_cat;
    $url_type = isset($_GET['type_mob']) && !empty($_GET['type_mob']) ? trim(hex2bin($_GET['type_mob'])) : $url_type;
    $url_key = filter_var($_GET['key'],FILTER_SANITIZE_STRING);
?>

<div class="standard-body-title hide-on-mobile">
    <h3><?= empty($url_cat) ? 'All Services' : $url_cat ?><?= !empty($url_type) ? ': '.$url_type : '' ?><?= !empty($url_key) ? ' - Searching for '.$url_key : '' ?></h3>
</div>
<div class="standard-dashboard-body-content pad-left pad-right">
    <!-- Notice -->
    <div class="notice gap-bottom double-gap-top popover-examples hide-on-mobile">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11"><span class="notice-name">NOTE:</span>
        In this section your business can outline all service headings and descriptions for quotes, ticketing systems, work orders, etc. Assigning your business services a Service Type, Category and Heading will enable reporting per service. Price points for services are added in the rate card section and may or may not be visible here. Services added here will display in the rate card; services added to the rate card may or may not be visible here.</div>
        <div class="clearfix"></div>
    </div>
    
    <?php
        $query_cat = !empty($url_cat) ? "AND `category`='".$url_cat."'" : "";
        $query_type = !empty($url_type) ? "AND `service_type`='".$url_type."'" : "";
        $query_key = !empty($url_key) ? "AND (`name` LIKE '%$url_key%' OR `heading` LIKE '%$url_key%')" : '';
        
        $result = mysqli_query($dbc, "SELECT * FROM `services` WHERE `deleted`=0 $query_cat $query_type $query_key ORDER BY `category`, `service_type`, `heading` LIMIT $offset, $rowsPerPage");
        $num_rows = "SELECT COUNT(*) `numrows` FROM `services` WHERE `deleted`=0 $query_cat $query_type $query_key ORDER BY `category`, `service_type`, `heading`";
        
		$rate_card_access = get_security($dbc, 'rate_card');
        if ( $result->num_rows>0) {
            echo '<div class="pagination_links">';
                echo display_pagination($dbc, $num_rows, $pageNum, $rowsPerPage);
            echo '</div>';
            
            while ( $row=mysqli_fetch_assoc($result) ) { ?>
                <div class="dashboard-item override-dashboard-item" data-searchable="<?= $row['category'].' '.$row['service_type'].' '.$row['heading'] ?>">
                    <div class="row">
                        <div class="col-sm-4">
                            <?php $db_service_image = get_config($dbc, 'services_default_image', false, ''); ?>
                            <img src="<?= !empty($row['service_image']) && file_exists('download/'.$row['service_image']) ? 'download/'.$row['service_image'] : (!empty($db_service_image) && file_exists('download/'.$db_service_image) ? 'download/'.$db_service_image : '../img/icons/empty.png') ?>" alt="<?= $row['heading'] ?>" style="max-width:100%; max-height: 10em;" />
                        </div>
                        <div class="col-sm-8">
                            <h4>
                                <?php if(empty($url_cat)) {
                                    echo $row['category'].(empty($row['service_type'].$url_type) ? '' : ': ');
                                } ?>
                                <?php if(empty($url_type)) {
                                    echo $row['service_type'];
                                } ?>
                            </h4>
                            <h5><a href="service.php?p=preview&id=<?=$row['serviceid']?>"><?= $row['heading'] ?></a></h5>
							<?php $rate = $dbc->query("SELECT `companyrcid`, `cust_price`, `uom` FROM `company_rate_card` WHERE `item_id`='{$row['serviceid']}' AND `tile_name` LIKE 'Services' AND `deleted`=0 AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW())")->fetch_assoc(); ?>
							<h5><?php if (strpos($service_db_config, ','."Rate Card Rate".',') !== FALSE && $rate_card_access['visible'] > 0) {
								echo 'Rate: '.($rate['companyrcid'] > 0 ? '$'.number_format($rate['cust_price'],2).' '.$rate['uom'] : 'N/A');
							}
							if (strpos($service_db_config, ','."Rate Card".',') !== FALSE && $rate_card_access['edit'] > 0) {
								echo ' | <a href="../Rate Card/ratecards.php?card=services&type=services&t='.$row['category'].'&status=add&'.($rate['companyrcid'] > 0 ? 'id='.$rate['companyrcid'] : 'service='.$row['serviceid']).'" onclick="overlayIFrameSlider(this.href); return false;">'.($rate['companyrcid'] > 0 ? 'View' : 'Create').' Rate Card</a>';
							} ?></h5>
                            <a href="service.php?p=details&id=<?=$row['serviceid']?>">Edit</a> | <a href="../delete_restore.php?action=delete&serviceid=<?=$row['serviceid']?>&cat=<?=$url_cat?>" onclick="return confirm(\'Are you sure you want to archive this service?\')">Archive</a>
                        </div>
                    </div>
                </div><?php
            }
            
            echo '<div class="pagination_links">';
                echo display_pagination($dbc, $num_rows, $pageNum, $rowsPerPage);
            echo '</div>';
        }
    ?>
</div>

<div class="clearfix"></div>