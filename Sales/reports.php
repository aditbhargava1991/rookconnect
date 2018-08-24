<?php
/*
 * Sales Tile Reports Main Page
 */
error_reporting(0);
include ('../include.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
        $(window).resize(function() {
            var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.tile-sidebar').offset().top;
            if(available_height > 200) {
                $('#sales_div .tile-sidebar, #sales_div .tile-content').height(available_height);
            }
        }).resize();
    });
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
    checkAuthorised('sales');
    $config_access = config_visible_function($dbc, 'sales');
    $dashboard     = preg_replace('/[^0-9]/', '', $_GET['dashboard']);

    if ( !empty($dashboard) ) {
        $query_mod = " AND (`primary_staff`='{$dashboard}' OR `share_lead`='{$dashboard}')";
    } else {
        $query_mod = '';
    }
?>

<div id="sales_div" class="container">
    <div class="row">
		<div class="main-screen"><?php
            include('tile_header.php'); ?>

            <div class="tile-container">

                <!-- Notice --><?php
                $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `note` FROM `notes_setting` WHERE `tile`='sales' AND `subtab`='sales_reports'"));
                $note = $notes['note'];
                if ( !empty($note) && 1 == 0 ) { ?>
                    <div class="notice gap-bottom double-gap-top popover-examples hidden-xs">
                        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                        <div class="col-sm-11"><span class="notice-name">NOTE: </span>
                            <?php echo $note; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div><?php
                } ?>

                <?php $page = ($_GET['p']) ? preg_replace('/\PL/u', '', $_GET['p']) : 'summary'; ?>

                <!-- Sidebar -->
                <div class="standard-collapsible tile-sidebar hide-titles-mob overflow-y">
                    <ul>
                        <a href="index.php"><li>Back to Dashboard</li></a>
                        <a href="?p=summary"><li class="<?= ( $page=='summary' || empty($page) ) ? 'active' : '' ?>">Monthly Summary Report</li></a>
                        <a href="?p=leadsource"><li class="<?= ( $page=='leadsource' || empty($page) ) ? 'active' : '' ?>">Lead Source Report</li></a>
                        <a href="?p=nextaction"><li class="<?= ( $page=='nextaction' || empty($page) ) ? 'active' : '' ?>">Next Action Report</li></a>
                        <a href="?p=leadspipeline"><li class="<?= ( $page=='leadspipeline' || empty($page) ) ? 'active' : '' ?>">Leads Added To Pipeline</li></a>
                        <a href="?p=wonlost"><li class="<?= ( $page=='wonlost' || empty($page) ) ? 'active' : '' ?>">Total Won/Lost</li></a>
                    </ul>
                </div><!-- .tile-sidebar -->

                <!-- Main Screen -->
                <div class="scale-to-fill tile-content hide-titles-mob">
                    <div class="main-screen-white standard-body" style="padding-left: 0; padding-right: 0; border: none;"><?php
                        if ( $page=='summary' ) {
                            $page_title = 'Monthly Summary Report';
                            $include_file = 'report_summary.php';
                        } elseif ( $page=='leadsource' ) {
                            $page_title = 'Lead Source Report';
                            $include_file = 'report_leadsource.php';
                        } elseif ( $page=='nextaction' ) {
                            $page_title = 'Next Action Report';
                            $include_file = 'report_nextaction.php';
                        } elseif ( $page=='leadspipeline' ) {
                            $page_title = 'Leads Added To Pipeline';
                            $include_file = 'report_leadspipeline.php';
                        } elseif ( $page=='wonlost' ) {
                            $page_title = 'Total Won/Lost';
                            $include_file = 'report_wonlost.php';
                        } else {
                            $page_title = 'Monthly Summary Report';
                            $include_file = 'report_summary.php';
                        } ?>
                        <div class="standard-body-title">
                            <h3><?= $page_title ?></h3>
                        </div>
                        <div class="standard-body-content pad-10">
                            <?php include($include_file); ?>
                        </div>
                    </div><!-- .main-screen-white -->
                </div><!-- .tile-content -->
                <div class="col-xs-12 show-on-mob"><?php
                    include('reports_mobile.php'); ?>
                </div>

                <div class="clearfix"></div>
            </div><!-- .tile-container -->

            <div class="clearfix"></div>

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>