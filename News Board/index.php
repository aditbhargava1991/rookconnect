<?php
/*
 * Newsboad Main Page
 */
 
error_reporting(0);
include_once('../include.php');
include_once ('database_connection_htg.php');
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
        
        $('.archive_newsitem').on('click', function() {
            var ans = confirm('Are you sure you want to delete this news item?');
            if ( ans == true ) {
                var newsboardid = $(this).data('id');
                var newsboard_type = $(this).data('type');
                $.ajax({
                    type: 'GET',
                    url: 'news_ajax_all.php?fill=delete_newsitem&newsboardid='+newsboardid,
                    success: function(response) {
                        alert('News item deleted successfully.');
                        window.location.replace('index.php');
                    }
                });
            }
        });
        
        $('.archive_board').on('click', function() {
            var boardid = '<?= preg_replace('/[^0-9]/', '', $_GET['board']); ?>';
            if ( boardid == '' || typeof boardid == 'undefined' ) {
                alert('Please go to a news board to use this option.');
            } else {
                var ans = confirm('Are you sure you want to archive the News Board? This will remove the board and all the news items in the board.');
                if ( ans == true ) {
                    $.ajax({
                        type: 'GET',
                        url: 'news_ajax_all.php?fill=archive_board&boardid='+boardid,
                        success: function(response) {
                            alert('News board and all the news items in the board archived succesfully.');
                            window.location.replace('index.php');
                        }
                    });
                }
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
                    <div class="pull-left"><h1><a href="index.php" class="default-color">News Board</a></h1></div>
                    <div class="pull-right gap-top"><?php
                        if ( config_visible_function ( $dbc, 'software_guide' ) == 1 ) {
                            echo '<a href="field_config.php" class="mobile-block pull-right gap-right offset-left-5"><img style="width:30px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
                        } ?>
                        <button class="pull-right btn brand-btn offset-left-5" onclick="overlayIFrameSlider('add_board.php', 'auto', true, true);">Add News Board</button>
                        <button class="pull-right btn brand-btn" onclick="overlayIFrameSlider('add_news.php', 'auto', true, true);">Add News</button>
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
                <ul id="newsboard_desktop" class="panel-group">
                    <li class="standard-sidebar-searchbox"><input type="text" class="search-text form-control" placeholder="Search News Board" /></li><?php
                    $boards = mysqli_query($dbc, "SELECT * FROM `newsboard_boards` WHERE `deleted`=0");
                    if ( $boards->num_rows > 0 ) {
                        while ( $board = mysqli_fetch_assoc($boards) ) {
                            $board_collapse = strtolower(str_replace(' ', '_', $board['board_name']));
                            echo '<li class="sidebar-higher-level">';
                                echo '<a class="'.($_GET['board'] == $board['boardid'] ? 'active' : 'collapsed').' cursor-hand" data-parent="#newsboard_desktop" data-toggle="collapse" data-target="#collapse_'. $board_collapse .'">'. $board['board_name'] . '<span class="arrow"></span></a>';
                                $tags = mysqli_query($dbc, "SELECT `tags` FROM `newsboard` WHERE `boardid`='{$board['boardid']}'");
                                if ( $tags->num_rows > 0 ) {
                                    echo '<ul id="collapse_'. $board_collapse .'" class="collapse '. ($_GET['board'] == $board['boardid'] ? 'in' : '') .'">';
                                        $url_tag = filter_var($_GET['tag'], FILTER_SANITIZE_STRING);
                                        while ( $row = mysqli_fetch_assoc($tags) ) {
                                            foreach ( explode(',', $row['tags']) as $tag ) {
                                                echo '<li class="'.( $url_tag == $tag ? 'active' : '' ).'"><a href="?board='.$board['boardid'].'&tag='.$tag.'">'. $tag .'</a></li>';
                                            }
                                        }
                                    echo '</ul>';
                                }
                            echo '</li>';
                        }
                    } else {
                        echo '<li>No news boards available</li>';
                    } ?>
                </ul>
            </div><!-- .sidebar -->

            <?php //Desktop view. Desktop content. ?>
            <div class="scale-to-fill has-main-screen hide-titles-mob" style="margin-bottom:-20px;">
                <div class="main-screen standard-body form-horizontal">
                    <?php
                    $search_term = filter_var($_GET['s'], FILTER_SANITIZE_STRING);
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
                    
                    } elseif ( isset($_GET['news']) ) {
                        include('news_item.php');
                    } else {
                        include('news_list.php');
                    } ?>
                </div>
            </div><!-- .has-main-screen -->
            
		</div><!-- .main-screen -->
	</div><!-- .row -->
</div><!-- .container -->

<div class="clearfix"></div>

<?php include('../footer.php'); ?>