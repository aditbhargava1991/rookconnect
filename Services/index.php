<?php
/*
 * Services Tile Main Page
 */
error_reporting(0);
include ('../include.php');
$service_db_config = get_field_config($dbc, 'services_dashboard');
$service_field_config = get_field_config($dbc, 'services');
?>
<script type="text/javascript">
$(document).ready(function() {
	$('input.purchase_order_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({
            type: "GET",
            url: "../ajax_all.php?fill=include_in_orders&type=po&name=services&value="+value+"&status="+id,
            dataType: "html",
            success: function(response){}
		});
	});

	$('input.sales_order_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({
            type: "GET",
            url: "../ajax_all.php?fill=include_in_orders&type=so&name=services&value="+value+"&status="+id,
            dataType: "html",
            success: function(response){}
		});
	});

	$('input.point_of_sale_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({
            type: "GET",
            url: "../ajax_all.php?fill=include_in_orders&type=pos&name=services&value="+value+"&status="+id,
            dataType: "html",
            success: function(response){}
		});
	});

    $('#accordions .panel-heading').on('touchstart',loadPanel).click(loadPanel);

    /* if($(window).width() > 767) {
		resizeScreen();
		$(window).resize(function() {
			resizeScreen();
		});
	} */

    $(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').is(':visible') && $('.sidebar').is(':visible')) {
			var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
			if(available_height > 300) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
			}
            var sidebar_height = $('.tile-sidebar').outerHeight(true);
            $('.has-main-screen, .has-main-screen .main-screen').css('min-height', sidebar_height);
		} else {
			$('.main-screen .main-screen').css('height','auto');
		}
	}).resize();
});

function resizeScreen() {
    $('#services_div .tile-sidebar, #services_div .tile-content, #services_div .tile-content .main-screen').height($('#services_div').height() - $('#services_div .tile-header').height() + 15);
}

/* function loadPanel() {
    if(!$(this).find('.panel-collapse').hasClass('in')) {
        var panel = $(this).closest('.panel').find('.panel-body');
        var url_category = $(panel).data('category');
        $(panel).html('Loading...');
        $.ajax({
            url: '../Services/dashboard_inc.php?cat_mob='+url_category,
            method: 'POST',
            data: { url_category: url_category },
            response: 'html',
            success: function(response) {
                $(panel).html(response);
            }
        });
    }
} */

function loadPanel() {
    $('#accordions .panel-heading:not(.higher_level_heading)').closest('.panel').find('.panel-body').html('Loading...');
    if(!$(this).hasClass('higher_level_heading')) {
        var panel = $(this).closest('.panel').find('.panel-body');
        $(panel).html('Loading...');
        $.ajax({
            url: $(panel).data('file'),
            method: 'GET',
            response: 'html',
            success: function(response) {
                $(panel).html(response);
            }
        });
    }
}

function searchServices(string) {
    $('[data-searchable]').hide();
    $('[data-searchable*="'+(string == '' ? ' ' : string)+'" i]').show();
    if(string == '') {
    $('[data-searchable]').show();
    }
}
function loadSearchService(string) {
    window.location.href = '?cat=<?= $_GET['cat'] ?>&type=<?= $_GET['type'] ?>&key='+encodeURIComponent(string);
}
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
    checkAuthorised('services');
?>

<div id="services_div" class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="ticket_iframe" src=""></iframe>
		</div>
	</div>
    <div class="row">
		<div class="main-screen"><?php
            include('tile_header.php'); ?>

            <div class="tile-container" style="height: 100%;">

                <?php
                    $url_cat = hex2bin($_GET['cat']);
                    $url_type = hex2bin($_GET['type']);
                ?>

                <!-- Sidebar -->
                <div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
                    <ul>
                        <li class="standard-sidebar-searchbox search-box"><input type="text" class="search-text form-control" placeholder="Search Services" onkeyup="searchServices(this.value);" onchange="loadSearchService(this.value);" value="<?= $_GET['key'] ?>"></li><?php
												$row_cat_subtab = '';
												$row_cat = 'Summary';
												$active  = 'collapsed';
												if ( (!empty($url_cat)) && ($url_cat==$row_cat) && (!isset($_GET['currentlist'])) ) {
														$active = 'active';
												}

												echo '<li class="sidebar-higher-level highest-level"><a class="cursor-hand '. $active .'" data-toggle="collapse" data-target="#type_'.$row_cat_subtab.'" href="?summary=true"><span class="sidebar-text">'. $row_cat .'</span><span class="arrow"></span></a></li>';
												$query = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `services` WHERE `category`!='' AND `deleted`=0 ORDER BY `category`");
                        while ( $row=mysqli_fetch_assoc($query) ){
                            $active  = 'collapsed';
                            $row_cat = $row['category'];
                            $row_cat_subtab = str_replace ( ['&', '/', ',', ' ', '___', '__'], ['', '_', '', '_', '_', '_'], $row['category'] );

                            if ( (!empty($url_cat)) && ($url_cat==$row_cat) && (!isset($_GET['currentlist'])) ) {
                                $active = 'active';
                            }

                            if ( check_subtab_persmission($dbc, 'services', ROLE, $row_cat_subtab) === true ) {
                                if(strpos($service_field_config,',Service Type,') !== false) {
                                    echo '<li class="sidebar-higher-level highest-level"><a class="cursor-hand '. $active .'" data-toggle="collapse" data-target="#type_'.$row_cat_subtab.'" href="javascript:void(0);"><span class="sidebar-text">'. $row_cat .'</span><span class="arrow"></span></a>';
                                        echo '<ul id="type_'.$row_cat_subtab.'" class="collapse '.($url_cat==$row_cat ? 'in' : '').'">';
                                            $result = mysqli_query($dbc, "SELECT `service_type`, COUNT(*) `count` FROM `services` WHERE `category`='$row_cat' AND `deleted`=0 GROUP BY `service_type`");
                                            while($row = $result->fetch_assoc()) {
                                                echo '<a href="?cat='. bin2hex($row_cat) .'&type='. bin2hex($row['service_type']) .'"><li class="'.($url_type==$row['service_type'] && $url_cat == $row_cat ? 'active' : '').'"><div class="sidebar-text">'.(empty($row['service_type']) ? 'No Type' : $row['service_type']).'</div><div class="pull-right">'. $row['count'] .'</div></li></a>';
                                            }
                                        echo '</ul>';
                                    echo '</li>';
                                } else {
                                    $count = $dbc->query("SELECT COUNT(*) `rows` FROM `services` WHERE `category`='$row_cat' AND `deleted`=0")->fetch_assoc()['rows'];
                                    echo '<li class="sidebar-higher-level highest-level"><a class="cursor-hand '. $active .'" href="?cat='. bin2hex($row_cat) .'"><span class="sidebar-text">'. $row_cat .'</span><span class="pull-right">'. $count .'</span></a>';
                                }
                            } else {
                                echo '<li class="sidebar-higher-level highest-level">'. $row_cat .'</li>';
                            }
                        } ?>
                    </ul>
                </div><!-- .tile-sidebar -->

                <!-- Main Screen -->
                <div class="scale-to-fill tile-content has-main-screen hide-on-mobile">
										<?php if($_GET['summary'] == true): ?>
												<?php include('summary.php'); ?>
										<?php else: ?>
                    	<?php include('dashboard.php'); ?>
										<?php endif; ?>
                </div><!-- .tile-content -->

                <div class="clearfix"></div>
            </div><!-- .tile-container -->

            <!-- Mobile -->
            <div class="row show-on-mob" style="width: 100%; background-color: #fff;">
                <div id="accordions" class="sidebar show-on-mob panel-group block-panels gap-top gap-left" style="width:94%; padding: 0;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordions" href="#collapse_all_services">
                                    All Services<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>

                        <div id="collapse_all_services" class="panel-collapse collapse">
                            <div class="panel-body" data-file="dashboard_inc.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php

                    $query = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `services` WHERE `category`!='' AND `deleted`=0 ORDER BY `category`");
                    $service_counter = 0;

                    while ( $row=mysqli_fetch_assoc($query) ){
                        $active  = '';
                        $row_cat = $row['category'];
                        $row_cat_subtab = str_replace ( ['&', '/', ',', ' ', '___', '__'], ['', '_', '', '_', '_', '_'], $row['category'] );

                        if ( check_subtab_persmission($dbc, 'services', ROLE, $row_cat_subtab) === true ) { ?>
                            <div class="panel panel-default">
                                <div class="panel-heading higher_level_heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordions" href="#collapse_<?= $service_counter ?>">
                                            <?= $row_cat ?><span class="glyphicon glyphicon-plus"></span>
                                        </a>
                                    </h4>
                                </div>

                                <div id="collapse_<?= $service_counter ?>" class="panel-collapse collapse">
                                    <div class="panel-body" id="collapse_active_<?= $service_counter ?>" style="margin:-1px; padding:0;">
                                        <?php
                                            $result = mysqli_query($dbc, "SELECT `service_type`, COUNT(`service_type`) `count` FROM `services` WHERE `category`='$row_cat' AND `deleted`=0 GROUP BY `service_type`");
                                            foreach($result as $row) {
                                                $cat_lower = strtolower(str_replace(['&', '/', ',', ' ', '___', '__'], ['', '_', '', '_', '_', '_'], $row_cat));
                                                $type_lower = strtolower(str_replace(['&', '/', ',', ' ', '___', '__'], ['', '_', '', '_', '_', '_'], $row['service_type'])); ?>
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a data-toggle="collapse" data-parent="#collapse_active_<?= $service_counter ?>" href="#collapse_<?= $cat_lower ?>_<?= $type_lower ?>" class="double-pad-left">
                                                                <?= $row['service_type'] ?><span class="glyphicon glyphicon-plus"></span>
                                                            </a>
                                                        </h4>
                                                    </div>

                                                    <div id="collapse_<?= $cat_lower ?>_<?= $type_lower ?>" class="panel-collapse collapse">
                                                        <div class="panel-body" data-file="dashboard_inc.php?cat_mob=<?=bin2hex($row_cat)?>&type_mob=<?=bin2hex($row['service_type'])?>">
                                                            Loading...
                                                        </div>
                                                    </div>
                                                </div><?php
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div><?php

                            $service_counter++;
                        }
                    } ?>
                </div>
            </div><!-- .show-on-mob -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>
