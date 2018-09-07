<?php
/*
Inventory Listing
*/
include ('../include.php');

?>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('material');
$material_navigation_position = get_config($dbc, 'material_navigation_position');
?>
<div class="container">
	<div class="row">
        <div class="main-screen">
            <div class="tile-header">
                <div class="col-xs-12 col-sm-4">
                    <h1>
                        <span class="pull-left" style="margin-top: -5px;"><a href="material.php" class="default-color">Materials</a></span>
                        <span class="clearfix"></span>
                    </h1>
                </div>
                <div class="col-xs-12 col-sm-8 text-right settings-block">
                    <?php
                    if(config_visible_function($dbc, 'material') == 1) { ?>
                        <div class="pull-right gap-left top-settings">
                            <a href="field_config.php" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
                            <span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                        </div><?php
                    }
                    if(vuaed_visible_function($dbc, 'material') == 1) {
                        echo '<div class="row gap-left gap-right">';
                            echo '<a href="add_material.php" class="btn brand-btn mobile-block gap-bottom pull-right">New Material</a>';
                            echo '<span class="popover-examples list-inline pull-right" style="margin:7px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new Material."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
                        echo '</div>';
                    } ?>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="tile-container" style="height: 100%;">
                <?php if($material_navigation_position == 'top') {
                    include('../Material/tile_nav_top.php');
                } ?>

                <!-- Notice -->
                <div class="notice gap-bottom gap-top popover-examples">
                    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                    <div class="col-sm-11"><span class="notice-name">NOTE:</span>
                    This tile monitors all of your materials.</div>
                    <div class="clearfix"></div>
                </div>

                <?php if($material_navigation_position != 'top') { ?>
                    <div class="collapsible tile-sidebar set-section-height">
                        <?php include('../Material/tile_sidebar.php'); ?>
                    </div>
                <?php } ?>

                <div class="scale-to-fill tile-content set-section-height">
                    <div class="main-screen-white" style="height:calc(100vh - 20em); overflow-y: auto;">
                		<form name="form_sites" method="post" action="" class="form-horizontal gap-top" role="form">

                        <?php if($material_navigation_position == 'top') { ?>
                            <div class="pull-left tab gap-bottom"><a href="material.php?filter=Top"><button type="button" class="btn brand-btn <?= $_GET['filter'] == 'Top' ? 'active_tab' : '' ?>">Last 25 Added</button></a></div>
                            <?php $cat_list = mysqli_query($dbc,"SELECT distinct(category) FROM material where deleted = 0");
                            if(mysqli_num_rows($cat_list) > 0) { ?>
                                <?php while($cat_tab = mysqli_fetch_array($cat_list)) {
                                    if(!empty($cat_tab['category'])) { ?>
                                        <div class="pull-left tab gap-bottom"><a href="material.php?category=<?= $cat_tab['category'] ?>"><button type="button" class="btn brand-btn <?= $cat_tab['category'] == $_GET['category'] ? 'active_tab' : '' ?>"><?= $cat_tab['category'] ?></button></a></div>
                                    <?php }
                                } ?>
                            <?php }
                        } ?>
                        <div class="clearfix"></div>

                        <?php
                            echo display_filter('material.php');
                        ?>

                		<div id="no-more-tables">
                			<?php
                			// Display Pager
                			$material = '';
                			if (isset($_POST['search_material_submit'])) {
                				$material = $_POST['search_material'];
                                if (isset($_POST['search_material'])) {
                                    $material = $_POST['search_material'];
                                }
                                // if ($_POST['search_category'] != '') {
                                //     $material = $_POST['search_category'];
                                // }
                			}
                			if (isset($_POST['display_all_material'])) {
                				$material = '';
                			}

                			include('materials_table.php');

                            echo display_filter('material.php');

                			?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>
