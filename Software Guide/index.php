<?php
/*
 * Software Guide
 * This is what the software users will refer when they want to see how a Tile or Sub Tab works
 */

error_reporting(0);
include_once('../include.php');
include ('../database_connection_htg.php');
?>
<script>
    $(document).ready(function() {
        $(window).resize(function() {
            $('.main-screen').css('padding-bottom',0);
            if($('.main-screen .main-screen').is(':visible')) {
                var available_height = window.innerHeight - $(footer).outerHeight() - $('.sidebar:visible').offset().top;
                if(available_height > 200) {
                    $('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
                    $('.sidebar').outerHeight(available_height).css('overflow-y','auto');
                    $('.search-results').outerHeight(available_height).css('overflow-y','auto');
                }
                var sidebar_height = $('.tile-sidebar').outerHeight(true);
            }
        }).resize();

        $('.panel-heading.mobile_load').click(loadPanel);

        $('.search-text').keypress(function(e) {
            if (e.which==13) {
                var search = this.value;
                window.location.replace('index.php?s='+search);
            }
        });
    });

    function loadPanel() {
        var panel = $(this).closest('.panel').find('.panel-body');
        var guide = panel.data('guide');
        panel.html('');

        $.ajax({
            url: 'guide_ajax_all.php?fill=load_panel&guide='+guide,
            method: 'GET',
            response: 'html',
            success: function(response) {
                panel.html(response);
            }
        });
    }
</script>
</head>

<body>
<?php
    include_once ('../navigation.php');
    checkAuthorised('software_guide');
?>
<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe src=""></iframe>
		</div>
	</div>
	<div class="row">
		<div class="main-screen">

            <!-- Tile Header -->
            <div class="tile-header standard-header">
                <div class="row">
                    <div class="col-xs-10"><h1><a href="index.php" class="default-color">Software Guide</a></h1></div>
                    <div class="col-xs-2 gap-top"><?php
                        if ( config_visible_function ( $dbc, 'software_guide' ) == 1 ) {
                            echo '<a href="field_config.php" class="mobile-block pull-right gap-right"><img style="width:30px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
                        } ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

            <?php //Mobile view. Show on mobile. Hide on desktop. ?>
            <div id="guide_accordions" class="sidebar show-on-mob panel-group block-panels col-xs-12 form-horizontal"><?php
                $tiles = mysqli_query($dbc_htg, "SELECT `tile` FROM `how_to_guide` GROUP BY `tile` ORDER BY `tile`");
                if ( $tiles->num_rows > 0 ) {
                    foreach ( $tiles as $tile ) {
                        $guide = mysqli_query($dbc_htg, "SELECT `guideid`, `tile`, `subtab` FROM `how_to_guide` WHERE `tile`='{$tile['tile']}' AND `deleted`=0 ORDER BY `sort_order`");
                        if ( $guide->num_rows > 0 ) {
                            while ( $row=mysqli_fetch_assoc($guide) ) {
                                $tab_id = $row['guideid']; ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading mobile_load">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#guide_accordions" href="#collapse_<?= $tab_id ?>">
                                                <?= $row['tile'] . ': ' . $row['subtab']; ?><span class="glyphicon glyphicon-plus"></span>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_<?= $tab_id ?>" class="panel-collapse collapse">
                                        <div class="panel-body" data-guide="<?= $row['guideid'] ?>">
                                            Loading...
                                        </div>
                                    </div>
                                </div><?php
                            }
                        }
                        $local_guide = mysqli_query($dbc, "SELECT `additional_guide` FROM `local_software_guide` WHERE `guideid`='$guideid'");
                        if ( $local_guide->num_rows > 0 ) {
                            while ( $row=mysqli_fetch_assoc($local_guide) ) {
                                echo '<div style="padding:1em;">'. html_entity_decode($row['additional_guide']) .'</div>';
                            }
                        }
                    }
                } ?>
            </div><!-- #guide_accordions -->

            <?php //Desktop view. Desktop sidebar. ?>
            <div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
                <ul>
                    <li class="standard-sidebar-searchbox"><input type="text" class="search-text form-control" placeholder="Search Software Guide" /></li><?php
                    $tiles = mysqli_query($dbc_htg, "SELECT `tile` FROM `how_to_guide` GROUP BY `tile` ORDER BY `tile`");
                    if ( $tiles->num_rows > 0 ) {
                        foreach ( $tiles as $tile ) {
                            $tile_collapse = strtolower(str_replace(' ', '_', $tile['tile']));
                            $guide = mysqli_query($dbc_htg, "SELECT `guideid`, `tile`, `subtab` FROM `how_to_guide` WHERE `tile`='{$tile['tile']}' AND `deleted`=0 ORDER BY `sort_order`");
                            echo '<li class="sidebar-higher-level"><a class="'.($_GET['tile'] == $tile['tile'] ? 'active' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#collapse_'. $tile_collapse .'">'. $tile['tile'] . '<span class="arrow"></span></a>';
                            if ( $guide->num_rows > 0 ) {
                                echo '<ul id="collapse_'. $tile_collapse .'" class="collapse">';
                                    $url_tile = trim(filter_var($_GET['tile'], FILTER_SANITIZE_STRING ));
                                    $url_guideid = preg_replace('/[^0-9]/', '', $_GET['guide']);
                                    while ( $row=mysqli_fetch_assoc($guide) ) {
                                        if ( $url_tile==$row['tile'] ) { ?>
                                            <script>
                                                $(document).ready(function() {
                                                    $('#collapse_<?= $tile_collapse ?>').collapse('show');
                                                });
                                            </script><?php
                                        }
                                        echo '<li class="'.( $url_guideid==$row['guideid'] ? 'active' : '' ).'"><a href="?guide='.$row['guideid'].'&tile='.$row['tile'].'">'. $row['subtab'] .'</a></li>';
                                    }
                                echo '</ul>';
                            }
                            echo '</li>';
                        }
                    } else {
                        echo '<li>No software guides available</li>';
                    } ?>
                </ul>
            </div><!-- .sidebar -->

            <?php //Desktop view. Desktop content. ?>
            <div class="scale-to-fill has-main-screen hide-titles-mob" style="margin-bottom:-20px;">
                <div class="main-screen standard-body form-horizontal">
                    <?php $search_term = filter_var($_GET['s'], FILTER_SANITIZE_STRING);
                    if ( !empty($search_term) ) {
                        echo '<div class="standard-body-title">';
                        echo '<h3>Search: '. $search_term .' </h3>';
                        echo '</div>';

                        echo '<div class="standard-body-content" style="padding: 1em;">';
                        $search = mysqli_query($dbc_htg, "SELECT `guideid`, `tile`, `subtab` FROM `how_to_guide` WHERE `tile` LIKE '$search_term%' OR `subtab` LIKE '%$search_term%' OR `description` LIKE '%$search_term%' AND `deleted`=0");
                        if ( $search->num_rows > 0 ) {
                            echo '<ul>';
                                while ( $row=mysqli_fetch_assoc($search) ) {
                                    echo '<li><a href="?guide='.$row['guideid'].'&tile='.$row['tile'].'">'. $row['tile'] . ': ' . $row['subtab'] .'</a></li>';
                                }
                            echo '</ul>';
                        } else {
                            echo 'Nothing found for ' . $search_term;
                        }
                        echo '</div>';

                    } else {
                        $guideid = preg_replace('/[^0-9]/', '', $_GET['guide']);
                        if ( !empty($guideid) ) {
                            $guide = mysqli_query($dbc_htg, "SELECT `tile`, `subtab`, `description`, `image_full_path` FROM `how_to_guide` WHERE `guideid`='$guideid' AND `deleted`=0");
                            if ( $guide->num_rows > 0 ) {
                                while ( $row=mysqli_fetch_assoc($guide) ) {
                                    echo '<div class="standard-body-title">';
                                    echo '<h3>'. $row['tile'] . ': '. $row['subtab'] .'</h3>';
                                    echo '</div>';

                                    echo '<div class="standard-body-content" style="padding: 1em;">';
                                        echo html_entity_decode($row['description']);
                                        echo "<img src='".$row['image_full_path']."' width='100%' title='Software Guide Image'>";
                                    echo '</div>';
                                }
                            }
                            $local_guide = mysqli_query($dbc, "SELECT `additional_guide` FROM `local_software_guide` WHERE `guideid`='$guideid'");
                            if ( $local_guide->num_rows > 0 ) {
                                while ( $row=mysqli_fetch_assoc($local_guide) ) {
                                    echo '<div style="padding:1em;">'. html_entity_decode($row['additional_guide']) .'</div>';
                                }
                            }

                            if ( $guide->num_rows == 0 && $local_guide->num_rows == 0 ) {
                                echo '<div class="standard-body-content" style="padding: 1em;">';
                                    echo '<h4>Requested software guide is not available at this time. Please check back later for updates.</h4>';
                                echo '</div>';
                            }

                        } else {
                            $index_tiles = mysqli_query($dbc_htg, "SELECT `tile` FROM `how_to_guide` GROUP BY `tile` ORDER BY `tile`");
                            echo '<div class="standard-body-title">';
                            echo '<h3>Index</h3>';
                            echo '</div>';

                            echo '<div class="standard-body-content" style="padding: 1em;">';
                            echo '<ul class="guide-index">';
                                if ( $index_tiles->num_rows > 0 ) {
                                    foreach ( $index_tiles as $index_tile ) {
                                        $index_subtabs = mysqli_query($dbc_htg, "SELECT `guideid`, `tile`, `subtab` FROM `how_to_guide` WHERE `tile`='{$index_tile['tile']}' AND `deleted`=0 ORDER BY `sort_order`");
                                        echo '<li>Tile: '. $index_tile['tile'];
                                        if ( $index_subtabs->num_rows > 0 ) {
                                            echo '<ul>';
                                                while ( $row_subtab=mysqli_fetch_assoc($index_subtabs) ) {
                                                    echo '<li><a href="?guide='.$row_subtab['guideid'].'&tile='.$row_subtab['tile'].'">'. $row_subtab['subtab'] .'</a></li>';
                                                }
                                            echo '</ul>';
                                        }
                                        echo '</li>';
                                    }
                                }
                            echo '</ul>';
                            echo '</div>';
                        }
                    }?>
                </div>
            </div><!-- .has-main-screen -->

		</div><!-- .main-screen -->
	</div><!-- .row -->
</div><!-- .container -->

<div class="clearfix"></div>

<?php include('../footer.php'); ?>