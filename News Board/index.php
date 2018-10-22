<?php
/*
 * Newsboad Main Page
 */

error_reporting(0);
include('../include.php');
include('../database_connection_htg.php');
$rookconnect = get_software_name();
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
                    url: 'news_ajax_all.php?fill=delete_newsitem&newsboardid='+newsboardid+'&newsboard_type='+newsboard_type,
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

    $(document).on('click', '.panel-heading.mobile_tag_load', loadPanelData);
    $(document).on('click', '.panel-body .nb-block a', loadPanelItem);

    function loadPanel() {
        var panel = $(this).closest('.panel').find('.panel-body');
        var board = panel.data('board');
        var type = panel.data('type');
        panel.html('');

        $.ajax({
            url: 'news_ajax_all.php?fill=load_panel&board='+board+'&type='+type,
            method: 'GET',
            response: 'html',
            success: function(response) {
                panel.html(response);
            }
        });
    }
    function loadPanelData() {
        var panel = $(this).closest('.panel').find('.panel-body');
        var board = panel.data('board');
        var tag = panel.data('tag');
        var type = panel.data('type');
        panel.html('');

        $.ajax({
            url: 'news_ajax_all.php?fill=load_panel_data&board='+board+'&tag='+tag+'&type='+type+'&mobile=yes',
            method: 'GET',
            response: 'html',
            success: function(response) {
                panel.html(response);
                panel.find('.standard-body-title').css('cssText', 'margin-top:0 !important; padding-top:0 !important;');
            }
        });
    }
    function loadPanelItem() {
        var panel = $(this).closest('.panel').find('.panel-body');
        var urlParams = new URLSearchParams($(this).attr('href'));
        var board = urlParams.get('board');
        var tag = urlParams.get('tag');
        var news = urlParams.get('news');
        var type = urlParams.get('type');
        panel.html('');

        $.ajax({
            url: 'news_ajax_all.php?fill=load_panel_item&board='+board+'&tag='+tag+'&news='+news+'&type='+type+'&mobile=yes',
            method: 'GET',
            response: 'html',
            success: function(response) {
                panel.html(response);
                panel.find('.standard-body-title').css('cssText', 'margin-top:0 !important; padding-top:0 !important;');
                //panel.closest('.panel').find('.mobile_tag_load').addClass('active');
                //panel.closest('.panel').find('.panel-title span').removeClass('glyphicon-plus').addClass('glyphicon-minus');
            }
        });

        return false;
    }
</script>
</head>

<body>
<?php
    include_once ('../navigation.php');
    checkAuthorised('newsboard');
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
                        if ( vuaed_visible_function($dbc, 'newsboard') == 1 ) { ?>
                            <a href="field_config_newsboard.php" class="mobile-block pull-right gap-right offset-left-5"><img style="width:30px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>
                            <button class="pull-right btn brand-btn offset-left-5" onclick="overlayIFrameSlider('add_board.php', 'auto', true, false);">Add News Board</button>
                            <button class="pull-right btn brand-btn" onclick="overlayIFrameSlider('add_news.php', 'auto', true, false);">Add News Post</button><?php
                        } ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

            <?php // Mobile view. Show on mobile. Hide on desktop. ?>
            <div id="newsboard_mobile" class="sidebar show-on-mob panel-group block-panels col-xs-12 form-horizontal"><?php
                $query = "SELECT * FROM `newsboard_boards` WHERE `deleted`=0";
                $boards = mysqli_query($dbc_htg, $query);
                if ( $boards->num_rows > 0 ) {
                    while ( $board = mysqli_fetch_assoc($boards) ) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading mobile_load">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#newsboard_mobile" href="#collapse_sw_<?= $board['boardid'] ?>">
                                        <?= $board['board_name'] ?><span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse_sw_<?= $board['boardid'] ?>" class="panel-collapse collapse">
                                <div id="board_sw_<?= $board['boardid'] ?>" class="panel-body" data-board="<?= $board['boardid'] ?>" data-type="sw" style="margin:-1px; padding:0;">
                                    Loading...
                                </div>
                            </div>
                        </div><?php
                    }
                }
                $boards = mysqli_query($dbc, $query);
                if ( $boards->num_rows > 0 ) {
                    while ( $board = mysqli_fetch_assoc($boards) ) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading mobile_load">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#newsboard_mobile" href="#collapse_<?= $board['boardid'] ?>">
                                        <?= $board['board_name'] ?><span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse_<?= $board['boardid'] ?>" class="panel-collapse collapse">
                                <div id="board_<?= $board['boardid'] ?>" class="panel-body" data-board="<?= $board['boardid'] ?>" data-type="" style="margin:-1px; padding:0;">
                                    Loading...
                                </div>
                            </div>
                        </div><?php
                    }
                } ?>
            </div><!-- #newsboard_mobile -->

            <?php // Desktop view. Desktop sidebar. ?>
            <div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
                <ul id="newsboard_desktop" class="panel-group">
                    <li class="standard-sidebar-searchbox"><input type="text" name="search" class="search-text form-control" placeholder="Search News Board" /></li><?php
                    // Softwarewide News Boards
                    $boards = mysqli_query($dbc_htg, "SELECT * FROM `newsboard_boards` WHERE `deleted`=0");
                    if ( $boards->num_rows > 0 ) {
                        while ( $board = mysqli_fetch_assoc($boards) ) {
                            $board_collapse = strtolower(str_replace(' ', '_', $board['board_name']));
                            echo '<li class="sidebar-higher-level">';
                                echo '<a class="'.(($_GET['board'] == $board['boardid'] && $_GET['type'] == 'sw') ? 'active' : 'collapsed').' cursor-hand" data-parent="#newsboard_desktop" data-toggle="collapse" data-target="#collapse_'. $board_collapse .'">'. $board['board_name'] . '<span class="arrow"></span></a>';
                                $tags = mysqli_query($dbc_htg, "SELECT `tags` FROM `newsboard` WHERE `boardid`='{$board['boardid']}'");
                                if ( $tags->num_rows > 0 ) {
                                    echo '<ul id="collapse_'. $board_collapse .'" class="collapse '. (($_GET['board'] == $board['boardid'] && $_GET['type'] == 'sw') ? 'in' : '') .'">';
                                        $url_tag = filter_var($_GET['tag'], FILTER_SANITIZE_STRING);
                                        while ( $row = mysqli_fetch_assoc($tags) ) {
                                            foreach ( explode(',', $row['tags']) as $tag ) {
                                                echo '<li class="'.(($url_tag == $tag && $_GET['type'] == 'sw') ? 'active' : '').'"><a href="?board='.$board['boardid'].'&tag='.$tag.'&type=sw">'. $tag .'</a></li>';
                                            }
                                        }
                                    echo '</ul>';
                                }
                            echo '</li>';
                        }
                    }
                    // Local News Boards
                    $boards = mysqli_query($dbc, "SELECT * FROM `newsboard_boards` WHERE `deleted`=0");
                    if ( $boards->num_rows > 0 ) {
                        while ( $board = mysqli_fetch_assoc($boards) ) {
                            $board_collapse = strtolower(str_replace(' ', '_', $board['board_name']));
                            echo '<li class="sidebar-higher-level">';
                                echo '<a class="'.(($_GET['board'] == $board['boardid'] && ($_GET['type'] == '' || $_GET['type'] == 'local')) ? 'active' : 'collapsed').' cursor-hand" data-parent="#newsboard_desktop" data-toggle="collapse" data-target="#collapse_'. $board_collapse .'">'. $board['board_name'] . '<span class="arrow"></span></a>';
                                $tags = mysqli_query($dbc, "SELECT `tags` FROM `newsboard` WHERE `boardid`='{$board['boardid']}'");
                                if ( $tags->num_rows > 0 ) {
                                    echo '<ul id="collapse_'. $board_collapse .'" class="collapse '. (($_GET['board'] == $board['boardid'] && ($_GET['type'] == '' || $_GET['type'] == 'local')) ? 'in' : '') .'">';
                                        $url_tag = filter_var($_GET['tag'], FILTER_SANITIZE_STRING);
                                        while ( $row = mysqli_fetch_assoc($tags) ) {
                                            foreach ( explode(',', $row['tags']) as $tag ) {
                                                echo '<li class="'.($url_tag == $tag ? 'active' : '').'"><a href="?board='.$board['boardid'].'&tag='.$tag.'">'. $tag .'</a></li>';
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

            <?php // Desktop view. Desktop content. ?>
            <div class="scale-to-fill has-main-screen hide-titles-mob" style="margin-bottom:-20px;">
                <div class="main-screen standard-body form-horizontal"><?php
                    if ( isset($_GET['s']) ) {
                        include('news_search.php');
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
