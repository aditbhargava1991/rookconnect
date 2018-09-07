<?php
/*
Asset Listing
*/
include ('../include.php');

?>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('assets');
$asset_navigation_position = get_config($dbc, 'asset_navigation_position');
?>
<div class="container">
	<div class="row">
        <div class="main-screen">
            <div class="tile-header">
                <?php include('../Asset/tile_header.php'); ?>
            </div>

            <div class="tile-container" style="height: 100%;">
                <?php if($asset_navigation_position == 'top') {
                    include('../Asset/tile_nav_top.php');
                } ?>

                <!-- Notice -->
                <div class="notice gap-bottom gap-top popover-examples">
                    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                    <div class="col-sm-11"><span class="notice-name">NOTE:</span>
                    This tile monitors all of your assets.</div>
                    <div class="clearfix"></div>
                </div>

                <?php if($asset_navigation_position != 'top') { ?>
                    <div class="collapsible tile-sidebar set-section-height">
                        <?php include('../Asset/tile_sidebar.php'); ?>
                    </div>
                <?php } ?>

                <div class="scale-to-fill tile-content set-section-height">
                    <div class="main-screen-white" style="height:calc(100vh - 20em); overflow-y: auto;">
                		<form name="form_sites" method="post" action="" class="form-inline double-gap-top" role="form">

                        <?php if($asset_navigation_position == 'top') { ?>
                            <div class="pull-left tab gap-bottom"><a href="asset.php?category=Top"><button type="button" class="btn brand-btn <?= (empty($_GET['category']) || $_GET['category'] == 'Top') ? 'active_tab' : '' ?>">Last 25 Added</button></a></div>
                            <?php $tabs = get_config($dbc, 'asset_tabs');
                            $each_tab = explode(',', $tabs);
                            foreach ($each_tab as $cat_tab) { ?>
                                <div class="pull-left tab gap-bottom"><a href="asset.php?category=<?= $cat_tab ?>"><button type="button" class="btn brand-btn <?= $_GET['category'] == $cat_tab ? 'active_tab' : '' ?>"><?= $cat_tab ?></button></a></div>
                            <?php }
                        } ?>
                        <div class="clearfix"></div>

                        <div id="no-more-tables">
                			<?php
                			// Display Pager
                			$asset = '';
                			if (isset($_POST['search_asset_submit'])) {
                				$asset = $_POST['search_asset'];
                                if (isset($_POST['search_asset'])) {
                                    $asset = $_POST['search_asset'];
                                }
                                if ($_POST['search_category'] != '') {
                                    $asset = $_POST['search_category'];
                                }
                			}
                			if (isset($_POST['display_all_asset'])) {
                				$asset = '';
                			}
                			include('asset_table.php'); ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include ('../footer.php'); ?>