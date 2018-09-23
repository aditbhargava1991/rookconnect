<?php
/*
 * Sales Lead Main Page
 */
error_reporting(0);
include ('../include.php');
include_once('../Sales/config.php');

// Form submission from details.php
if (isset($_POST['add_sales'])) {
    echo '<script type="text/javascript"> window.location.replace("index.php");</script>';
}

$salesid   = preg_replace('/[^0-9]/', '', $_GET['id']);
$lead = $dbc->query("SELECT `businessid`, `contactid`, `lead_created_by`, `created_date`, `status`, `status_date`, `next_action`, `new_reminder`, `lead_value`, `estimated_close_date` FROM `sales` WHERE `salesid`='$salesid'")->fetch_assoc(); ?>

<script src="edit.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('#sales_div .tile-sidebar').offset().top - 1;
        if(available_height > 200) {
            $('#sales_div .tile-sidebar').height(available_height);
            $('#sales_div .main-screen-white').height(available_height);
            $('.tile-content').height('auto');
            $('.tile-content').css('overflow-x','hidden');
            $('#sales_div .main-screen-white').height('auto');
            $('#sales_div .main-screen-white .standard-body-content').height(available_height - $('#sales_div .main-screen-white .standard-body-title').height());
            $('#sales_div .main-screen-white .standard-body-content').css('overflow-x','hidden');
            $('#sales_div .main-screen-white .standard-body-content').css('overflow-y','auto');
            $('#sales_div .main-screen-white .standard-body-content').css('margin-top',$('#sales_div .main-screen-white .standard-body-title').height()+'px');
        }
    }).resize();

    $('.main-screen-white').css('overflow-x','hidden');
    var $sections = $('.accordion-block-details');
    $('.standard-body-content').on('scroll', function(){
        var currentScroll = $('.main-screen .tile-container').offset().top + $('.standard-body-content').find('.preview-block-header').height();
        var $currentSection;
        $sections.each(function(){
            var divPosition = $(this).offset().top;
            if( divPosition - 1 < currentScroll ){
                $('.tile-sidebar li').removeClass('active');
                $('.tile-sidebar [href=#'+$(this).attr('id')+'] li').addClass('active');
            } else if(divPosition < currentScroll + $('.main-screen .tile-container').height()) {
                $('.tile-sidebar [href=#'+$(this).attr('id')+'] li').addClass('active');
            }
        });
    });

    $('#nav_salespath').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='salespath' ) {
            echo 'window.location.replace("?p=salespath&id='.$_GET['id'].'");';
        } ?>
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
        $(this).addClass('active');
    });

    $('#nav_staffinfo').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=staffinfo");';
        } ?>
        $('#nav_salespath, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
        $(this).addClass('active');
    });

    $('#nav_business').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=business");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    <?php foreach(explode(',',$lead['contactid']) as $contactid) {
        if($contactid > 0) { ?>
            $('#nav_contact_<?= $contactid ?>').click(function() {<?php
                if ( isset($_GET['p']) && $_GET['p']!='details' ) {
                    echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=contact_'.$contactid.'");';
                } ?>
                $('[id^=nav_]').removeClass('active');
                $(this).addClass('active');
            });
        <?php }
    } ?>

    $('#nav_leadinfo').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=leadinfo");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_services').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=services");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_products').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=products");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_refdocs').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=refdocs");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_marketing').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=marketing");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_infogathering').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=infogathering");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_estimate').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=estimate");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_quote').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=quote");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_nextaction').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=nextaction");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_leadnotes, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_leadnotes').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=leadnotes");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_tasks, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_tasks').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=tasks");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_time, #nav_history').removeClass('active');
    });

    $('#nav_time').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=time");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_history').removeClass('active');
    });

    $('#nav_history').click(function() {<?php
        if ( isset($_GET['p']) && $_GET['p']!='details' ) {
            echo 'window.location.replace("?p=details&id='.$_GET['id'].'&a=history");';
        } ?>
        $(this).addClass('active');
        $('#nav_salespath, #nav_staffinfo, #nav_business, #nav_leadinfo, #nav_services, #nav_products, #nav_refdocs, #nav_marketing, #nav_infogathering, #nav_estimate, #nav_quote, #nav_nextaction, #nav_leadnotes, #nav_tasks, #nav_time').removeClass('active');
    });

    <?php
        if ( $_GET['p'] == 'details' && (!isset($_GET['a']) || $_GET['a']=='staffinfo') ) { echo "$('#nav_staffinfo').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='leadinfo' ) { echo "$('#nav_leadinfo').trigger('click');"; }
        if ( isset($_GET['a']) && strpos($_GET['a'],'contact') !== FALSE ) { echo "$('#nav_".$_GET['a']."').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='business' ) { echo "$('#nav_business').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='services' ) { echo "$('#nav_services').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='products' ) { echo "$('#nav_products').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='refdocs' ) { echo "$('#nav_refdocs').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='marketing' ) { echo "$('#nav_marketing').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='infogathering' ) { echo "$('#nav_infogathering').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='estimate' ) { echo "$('#nav_estimate').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='quote' ) { echo "$('#nav_quote').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='nextaction' ) { echo "$('#nav_nextaction').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='leadnotes' ) { echo "$('#nav_leadnotes').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='tasks' ) { echo "$('#nav_tasks').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='time' ) { echo "$('#nav_time').trigger('click');"; }
        if ( isset($_GET['a']) && $_GET['a']=='history' ) { echo "$('#nav_history').trigger('click');"; }
    ?>

    $('form').submit(function(e){
        if ($('[name=status]').length==0) {
            e.preventDefault();
            alert('Please select the Lead Status.');
        }
    });
});
</script>
</head>

<body>
<?php
    if($_GET['iframe_slider'] != 1) {
        include_once ('../navigation.php');
    }
    checkAuthorised('sales');
?>

<div id="sales_div" class="container">
    <div class="iframe_overlay" style="display:none;">
        <div class="iframe">
            <iframe src=""></iframe>
        </div>
    </div>

    <div class="row">
        <div class="main-screen"><?php
            if($_GET['iframe_slider'] != 1 && !IFRAME_PAGE) {
                include('tile_header.php');
            }

            $page      = preg_replace('/\PL/u', '', $_GET['p']); ?>

            <!--<div class="tile-bar">
                <ul>
                    <li class="<?= ( $page=='details' ) ? 'active' : ''; ?>"><a href="?p=details&id=<?=$salesid;?>">Details</a></li>
                    <li class="<?= ( $page=='template' ) ? 'active' : ''; ?>"><a href="?p=template">Template</a></li>
                    <li class="<?= ( $page=='design' ) ? 'active' : ''; ?>"><a href="?p=design">Design</a></li>
                    <li class="<?= ( $page=='preview' ) ? 'active' : ''; ?>"><a href="?p=preview&id=<?=$salesid;?>">Preview</a></li>
                </ul>
            </div> .tile-bar -->

            <div class="tile-container">
                <!-- Quick Reports -->
                <div class="col-xs-12 collapsible-horizontal collapsed" id="summary-div">
                    <div class="col-xs-12 col-sm-4 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= date('Y-m-d',strtotime($lead['created_date'])) ?></div>
                            <div>Lead Created</div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= $lead['status'] ?></div>
                            <div>Since <?= $lead['status_date'] ?></div>
                        </div>
                    </div>
                    <?php if(empty($lead['next_action']) || empty($lead['new_reminder'])) { ?>
                        <div class="col-xs-12 col-sm-4 col-md-3 gap-top">
                            <div class="summary-block text-red">
                                <div class="text-lg">No Action</div>
                                <div>Scheduled</div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="col-xs-12 col-sm-4 col-md-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= $lead['next_action'] ?></div>
                                <div>Scheduled for <?= $lead['new_reminder'] ?></div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-xs-12 col-sm-4 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg">$<?= number_format($lead['lead_value'],2) ?></div>
                            <div>Lead Value</div>
                        </div>
                    </div>
                    <?php if(empty($lead['estimated_close_date'])) { ?>
                        <div class="col-xs-12 col-sm-4 col-md-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= $lead['estimated_close_date'] ?></div>
                                <div>Estimated Close Date</div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Sidebar -->
                <div class="standard-collapsible tile-sidebar tile-sidebar-noleftpad hide-on-mobile" <?= $_GET['iframe_slider'] == 1 || IFRAME_PAGE ? 'style="display:none;"' : '' ?>>
                    <ul><?php
                        $get_field_config_tabs_order   = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `value` FROM `general_configuration` where `name` = 'sales_sub_tabs_order'" ) );
                        $tab_order = stripslashes(html_entity_decode($get_field_config_tabs_order['value']));
                        $tab_order = json_decode($tab_order, true);
                        foreach ($tab_order as $key => $value) {

                            if (strpos($value_config, $value['condition']) !== false) { ?>
                                <a href="<?php echo $value['ahref'];?>"><li class="collapsed cursor-hand <?= $_GET['p'] == 'salespath' ? 'active' : '' ?>" data-toggle="collapse" data-target="<?php echo $value['target'];?>" id="<?php echo $value['id'];?>"><?php echo $value['label'];?></li></a>

                                <?php
                            }elseif((strpos($value_config, ',Lead Information,') !== false && $value['condition']=='nav_contact')) {
                                foreach(explode(',',$lead['contactid']) as $contactid) {
                                    if($contactid > 0) { ?>
                                        <a href="<?php echo $value['ahref'].$contactid;?>"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="<?php echo $value['target'].$contactid;?>" id="<?php echo $value['id'].$contactid;?>" data-condition=""><?php echo $value['label'];?></li></a>
                                    <?php }
                                }
                            }
                            elseif((strpos($value_config, ',Lead Information,') !== false && $value['condition']=='nav_business')) {
                                ?>
                                    <a href="<?php echo $value['ahref'];?>"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="<?php echo $value['target'];?>" id="<?php echo $value['id'];?>" data-condition=""><?php echo $value['label'];?></li></a>
                                <?php
                            }
                        }
                         ?>
                        <li><?= SALES_NOUN ?> Created by<br /><?= $salesid > 0 ? $lead['lead_created_by'] : get_contact($dbc, $_SESSION['contactid']) ?><br /> on <?= $salesid > 0 ? $lead['created_date'] : date('Y-m-d') ?></li>
                    </ul>
                </div><!-- .tile-sidebar -->

                <!-- Main Screen -->
                <div class="scale-to-fill has-main-screen tile-content set-section-height"><?php
                    if ( $page=='preview' || empty($page) ) {
                        include('preview.php');
                    } elseif ( $page=='salespath' ) {
                        include('salespath.php');
                    } elseif ( $page=='details' ) {
                        include('details.php');
                    } else {
                        include('details.php');
                    } ?>
                </div><!-- .tile-content -->

                <div class="clearfix"></div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php if($_GET['iframe_slider'] != 1) {
    include ('../footer.php');
} ?>
