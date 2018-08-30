<?php
/*
 * Newsboad Main Page
 */
 
error_reporting(0);
include_once('../include.php');
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
                    <div class="col-xs-10"><h1><a href="index.php" class="default-color">News Board</a></h1></div>
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
                        $url_boardid = preg_replace('/[^0-9]/', '', $_GET['board']);                        
                        $board = mysqli_query($dbc, "SELECT `board_name`, `shared_staff` FROM `newsboard_boards` WHERE `boardid`='$url_boardid' AND `deleted`=0");
                        if ( $board->num_rows > 0 ) {
                            while ( $row = mysqli_fetch_assoc($board) ) {
                                echo '<div class="standard-body-title">';
                                    echo '<h3>';
                                        echo $row['board_name'];
                                        foreach(array_filter(array_unique(explode(',', $row['shared_staff']))) as $shared_staff) {
                                            if ( $shared_staff == 'ALL' || empty($shared_staff) ) {
                                                echo '<div class="pull-left id-circle" style="background-color:#6DCFF6">All</div>';
                                            } else {
                                                profile_id($dbc, $shared_staff);
                                            }
                                        }
                                    echo '</h3>';
                                echo '</div>';
                            }
                        }
                        
                        $url_tag = filter_var($_GET['tag'], FILTER_SANITIZE_STRING);
                        if ( !empty($url_tag) ) {
                            $newsboards = mysqli_query($dbc, "SELECT `n`.`newsboardid`, `n`.`title`, `n`.`description`, `img`.`document_link` FROM `newsboard` `n` LEFT JOIN `newsboard_uploads` `img` ON (`n`.`newsboardid` = `img`.`newsboardid`) WHERE FIND_IN_SET ('$url_tag', `n`.`tags`) AND `n`.`boardid`='$url_boardid' AND `n`.`deleted`=0");
                            if ( $newsboards->num_rows > 0 ) {
                                echo '<div class="standard-body-content padded">';
                                    while ( $row = mysqli_fetch_assoc($newsboards) ) {
                                        echo '<div class="col-sm-3 nb-block">';
                                            echo '<h4><a href="index.php?news='.$row['newsboardid'].'">'. $row['title'] .'</a></h4>';
                                            if ( !empty($row['document_link']) ) {
                                                echo '<a href="index.php?news='.$row['newsboardid'].'"><img src="download/'.$row['document_link'].'" /></a>';
                                            }
                                        echo '</div>';
                                    }
                                echo '</div>';
                            }
                        }
                    } ?>
                </div>
            </div><!-- .has-main-screen -->
            
		</div><!-- .main-screen -->
	</div><!-- .row -->
</div><!-- .container -->

<div class="clearfix"></div>

<?php include('../footer.php'); ?>