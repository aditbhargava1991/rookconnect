<?php
/*
 * Sale Tile Main Page
 */
error_reporting(0);
include ('../include.php');
?>
<script type="text/javascript">
    $(document).ready(function(){
        if($(window).width() > 767) {
            //resizeScreen();
            $(window).resize(function() {
                $('.double-scroller div').width($('.main-screen-white:visible').get(0).scrollWidth);
                $('.double-scroller').off('scroll',doubleScroll).scroll(doubleScroll);
                $('.dashboard-container').off('scroll',setDoubleScroll).scroll(setDoubleScroll);
                //resizeScreen();
            });
        }
        $('.dashboard-container').css('height', 'calc(100% - '+$('.double-scroller').height()+'px)');

        $(window).resize(function() {
            var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.tile-sidebar').offset().top - 19;
            if(available_height > 200) {
                $('#sales_div .tile-sidebar, #sales_div .tile-content').height(available_height);
            }
        }).resize();
    });

	function doubleScroll() {
		$('.main-screen-white:visible').scrollLeft(this.scrollLeft).scroll();
	}
	function setDoubleScroll() {
		$('.double-scroller').scrollLeft(this.scrollLeft);
	}

    function searchLeads(string) {
		$('[data-searchable]').hide();
		$('[data-searchable*="'+(string == '' ? ' ' : string)+'" i]').show();
	}
    function changeLeadStatus(sel) {
        var sid     = sel.id;
        var arr     = sid.split('_');
        var salesid = arr[1];
        var status  = sel.value;

        $.ajax({
            type: "GET",
            url: "sales_ajax_all.php?fill=changeLeadStatus&salesid="+salesid+"&status="+status,
            dataType: "html",
            success: function(response){
                window.location.reload();
            }
        });
    }

    function changeLeadNextAction(sel) {
        var sid        = sel.id;
        var arr        = sid.split('_');
        var salesid    = arr[1];
        var nextaction = sel.value;

        $.ajax({
            type: "GET",
            url: "sales_ajax_all.php?fill=changeLeadNextAction&salesid="+salesid+"&nextaction="+nextaction,
            dataType: "html",
            success: function(response){}
        });
    }

    function changeLeadFollowUpDate(sel) {
        var sid          = sel.id;
        var arr          = sid.split('_');
        var salesid      = arr[1];
        var followupdate = sel.value;

        $.ajax({
            type: "GET",
            url: "sales_ajax_all.php?fill=changeLeadFollowUpDate&salesid="+salesid+"&followupdate="+followupdate,
            dataType: "html",
            success: function(response){}
        });
    }

    function resizeScreen() {
        var view_height = $(window).height() > 500 ? $(window).height() : 500;

        if ( $('header .container').is(':visible')==true ) {
            view_height = $('header').height() + $('#nav').height() + $('footer').height();
        } else {
            view_height = 0;
        }

        //$('#sales_div .scale-to-fill,#sales_div .scale-to-fill .main-screen,#sales_div .tile-sidebar').height($('#sales_div .tile-container').height());
        $('#sales_div .tile-sidebar, #sales_div .tile-content').height($(window).height() - view_height - $('#sales_div .tile-header').height() - 21);
    }
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
    checkAuthorised('sales');
    $approvals = approval_visible_function($dbc, 'sales');
    $config_access = config_visible_function($dbc, 'sales');
    $statuses      = get_config($dbc, 'sales_lead_status');
    $next_actions  = get_config($dbc, 'sales_next_action');
    $dashboard     = preg_replace('/[^0-9]/', '', $_GET['dashboard']);

    if ( !empty($dashboard) ) {
        $query_mod = " AND (`primary_staff`='{$dashboard}' OR CONCAT(',',`share_lead`,',') LIKE '%,{$dashboard},%')";
    } else {
        $query_mod = '';
    }
?>

<div id="sales_div" class="container">
    <div class="iframe_overlay" style="display:none;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe src=""></iframe>
        </div>
    </div>
    <div class="row">
		<div class="main-screen"><?php
            include('tile_header.php'); ?>

            <div class="tile-container">

                <!-- Notice --><?php
                $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE `tile`='sales' AND subtab='sales_sales'"));
                $note = $notes['note'];

                if ( !empty($note) && 1 == 0 ) { ?>
                    <div class="notice double-gap-bottom popover-examples">
                        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                        <div class="col-sm-11">
                            <span class="notice-name">NOTE:</span>
                            <?= $note; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div><?php
                } ?>

                <!-- Sales Stats --><?php
                $lead_status_won = get_config($dbc, 'lead_status_won');
                $lead_status_retained = get_config($dbc, 'lead_status_retained');
                $lead_status_lost = get_config($dbc, 'lead_status_lost');

                $oppotunities = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(*) `count`, SUM(`lead_value`) `value` FROM `sales` WHERE IFNULL(`status`,'') NOT IN ('$lead_status_won','$lead_status_lost','$lead_status_retained','') AND (IFNULL(NULLIF(`status_date`,''),`created_date`) BETWEEN '". date('Y-m-01') ."' AND '". date('Y-m-d') ."')" . $query_mod) );
                $closed = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(*) `count`, SUM(`lead_value`) `value` FROM `sales` WHERE `status` IN ('$lead_status_won','$lead_status_retained') AND (`status_date` BETWEEN '". date('Y-m-01') ."' AND '". date('Y-m-d') ."')" . $query_mod) );
                $tasks_total = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `tasklist` `t` LEFT JOIN `sales` `s` ON `t`.`salesid`=`s`.`salesid` OR (`t`.`clientid` > 0 AND CONCAT(',',`s`.`contactid`,',') LIKE CONCAT('%,',`t`.`clientid`,',%')) WHERE (`t`.`clientid`>0 AND `t`.`clientid` IN (`s`.`contactid`)) AND (`s`.`created_date` BETWEEN '". date('Y-m-01') ."' AND '". date('Y-m-d') ."')") );
                $estimates_total = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(`e`.`estimateid`) `count` FROM `estimate` `e`, `sales` `s` WHERE `e`.`clientid` IN (`s`.`contactid`) AND (`s`.`created_date` BETWEEN '". date('Y-m-01') ."' AND '". date('Y-m-d') ."')") ); ?>

                <div class="col-xs-12 collapsible-horizontal collapsed" id="summary-div">
                    <div class="col-xs-6 col-sm-4 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= ( $oppotunities['count'] > 0 ) ? $oppotunities['count'] : 0; ?></div>
                            <div>Total Open <?= SALES_TILE ?> in <?= date('F') ?></div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg">$<?= ( $oppotunities['value'] > 0 ) ? number_format($oppotunities['value'], 2) : '0.00'; ?></div>
                            <div>Value of Open <?= SALES_TILE ?> in <?= date('F') ?></div>
                        </div>
                    </div>
                    <?php foreach(explode(',',get_config($dbc, 'sales_quick_reports')) as $status) {
                        $sales_leads = mysqli_fetch_assoc( mysqli_query($dbc, "SELECT COUNT(*) `count`, SUM(`lead_value`) `value` FROM `sales` WHERE `status` IN ('$status') AND (`status_date` BETWEEN '". date('Y-m-01') ."' AND '". date('Y-m-d') ."')" . $query_mod) ); ?>
                        <div class="col-xs-6 col-sm-4 col-md-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $sales_leads['count'] > 0 ) ? $sales_leads['count'] : 0; ?></div>
                                <div>Total <?= $status ?> <?= SALES_TILE ?> in <?= date('F') ?></div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-4 col-md-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg">$<?= ( $sales_leads['value'] > 0 ) ? number_format($sales_leads['value'], 2) : '0.00'; ?></div>
                                <div>Value of <?= $status ?> <?= SALES_TILE ?> in <?= date('F') ?></div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-xs-6 col-sm-4 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= ( $closed['count'] > 0 ) ? $closed['count'] : 0; ?></div>
                            <div>Successful <?= SALES_TILE ?> in <?= date('F') ?></div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg">$<?= ( $closed['value'] > 0 ) ? number_format($closed['value'], 2) : '0.00'; ?></div>
                            <div>Value of Successful <?= SALES_TILE ?> in <?= date('F') ?></div>
                        </div>
						<!--<img class="pull-right inline-img" src="../img/icons/ROOK-minus-icon.png" onclick="$('#summary-div').hide();">-->
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= ( $tasks_total['count'] > 0 ) ? $tasks_total['count'] : 0; ?></div>
                            <div>Total Tasks in <?= date('F') ?></div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-3 gap-top">
                        <div class="summary-block">
                            <div class="text-lg"><?= ( $estimates_total['count'] > 0 ) ? $estimates_total['count'] : 0; ?></div>
                            <div>Total Estimates in <?= date('F') ?></div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <?php $page = preg_replace('/\PL/u', '', $_GET['p']); ?>

                <!-- Sidebar -->
                <div class="standard-collapsible tile-sidebar hide-titles-mob overflow-y">
                    <ul id="dashboard_sidebar">
						<li class="standard-sidebar-searchbox search-box"><input type="text" class="search-text form-control" placeholder="Search <?= SALES_TILE ?> Leads" onkeyup="searchLeads(this.value);"></li>
                        <li class="<?= ( $page=='dashboard' || empty($page) ) ? 'active' : '' ?>"><a href="index.php">Dashboard</a></li>
                        <li class="sidebar-higher-level"><a class="<?= in_array($_GET['s'],explode(',',$statuses)) ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#dashboard_sidebar" data-target="#collapse_status">Status<span class="arrow"></span></a>
							<ul id="collapse_status" class="panel-collapse collapse <?= !empty($_GET['s']) ? 'in' : '' ?>"><?php
								// Get Lead Statuses added in Settings->Lead Status accordion
								foreach ( explode(',', $statuses) as $status ) {
									$row_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `sales` WHERE `status`='$status' AND `deleted` = 0 " . $query_mod))['count'];
									echo '<li class="'.($_GET['s'] == $status ? 'active' : '').'"><a href="?p=filter&s='. $status .'">'. $status .'<span class="pull-right pad-right">'.$row_count.'</span></a></li>';
								} ?>
							</ul>
						</li>
                        <li class="sidebar-higher-level"><a class="<?= !empty($_GET['contactid']) ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#dashboard_sidebar" data-target="#collapse_staff">Staff<span class="arrow"></span></a>
                            <ul id="collapse_staff" class="panel-collapse collapse <?= !empty($_GET['contactid']) ? 'in' : '' ?>"><?php
                                // Get Staff
                                $staff_list = array_filter(explode(',',get_config($dbc, 'sales_dashboard_users')));
                                foreach($staff_list as $staffid) {
                                    $row_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `sales` WHERE (CONCAT(',',`share_lead`,',') LIKE '%,$staffid,%' OR `primary_staff` = '$staffid') AND `deleted` = 0"))['count'];
                                    echo '<li class="'.($_GET['contactid'] == $staffid ? 'active' : '').'"><a href="?p=filter&contactid='. $staffid .'">'. get_contact($dbc, $staffid) .'<span class="pull-right pad-right">'.$row_count.'</span></a></li>';
                                } ?>
                            </ul>
                        </li>
						<?php $regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
						if(count($regions) > 0) { ?>
							<li class="sidebar-higher-level"><a class="<?= in_array($_GET['r'],$locations) ? '' : 'collapsed' ?> <?= isset($_GET['r']) ? 'active' : '' ?> cursor-hand" data-toggle="collapse" data-parent="#dashboard_sidebar" data-target="#collapse_region">Region<span class="arrow"></span></a>
								<ul id="collapse_region" class="panel-collapse collapse <?= !empty($_GET['r']) ? 'in' : '' ?>"><?php
									// Get Lead Statuses added in Settings->Lead Status accordion
									foreach ( $regions as $region ) {
										$row_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `sales` WHERE `region`='$region' AND `deleted` = 0 " . $query_mod))['count'];
										echo '<li class="'.($_GET['r'] == $region ? 'active' : '').'"><a href="?p=filter&r='. $region .'">'. $region .'<span class="pull-right pad-right">'.$row_count.'</span></a></li>';
									} ?>
								</ul>
							</li>
						<?php } ?>
						<?php $locations =  array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
						if(count($locations) > 0) { ?>
							<li class="sidebar-higher-level"><a class="<?= in_array($_GET['l'],$locations) ? '' : 'collapsed' ?> <?= isset($_GET['l']) ? 'active' : '' ?> cursor-hand" data-toggle="collapse" data-parent="#dashboard_sidebar" data-target="#collapse_location">Location<span class="arrow"></span></a>
								<ul id="collapse_location" class="panel-collapse collapse <?= !empty($_GET['l']) ? 'in' : '' ?>"><?php
									// Get Lead Statuses added in Settings->Lead Status accordion
									foreach ( $locations as $location ) {
										$row_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `sales` WHERE `location`='$location' AND `deleted` = 0 " . $query_mod))['count'];
										echo '<li class="'.($_GET['l'] == $location ? 'active' : '').'"><a href="?p=filter&l='. $location .'">'. $location .'<span class="pull-right pad-right">'.$row_count.'</span></a></li>';
									} ?>
								</ul>
							</li>
						<?php } ?>
						<?php $classifications = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_classification'"))[0])));
						if(count($classifications) > 0) { ?>
							<li class="sidebar-higher-level"><a class="<?= in_array($_GET['c'],$classifications) ? '' : 'collapsed' ?> <?= isset($_GET['c']) ? 'active' : '' ?> cursor-hand" data-toggle="collapse" data-parent="#dashboard_sidebar" data-target="#collapse_classification">Classification<span class="arrow"></span></a>
								<ul id="collapse_classification" class="panel-collapse collapse <?= !empty($_GET['c']) ? 'in' : '' ?>"><?php
									// Get Lead Statuses added in Settings->Lead Status accordion
									foreach ( $classifications as $classification ) {
										$row_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `sales` WHERE `classification`='$classification' AND `deleted` = 0 " . $query_mod))['count'];
										echo '<li class="'.($_GET['c'] == $classification ? 'active' : '').'"><a href="?p=filter&c='. $classification .'">'. $classification .'<span class="pull-right pad-right">'.$row_count.'</span></a></li>';
									} ?>
								</ul>
							</li>
						<?php } ?>
                        <?php if ( check_subtab_persmission($dbc, 'sales', ROLE, 'reports') === TRUE ) { ?>
                            <li><a href="reports.php">Reports<img class="inline-img pull-right no-pad no-margin" src="../img/icons/pie-chart.png"></a></li>
                        <?php } ?>
                    </ul>
                </div><!-- .tile-sidebar -->

                <!-- Main Screen -->
                <div class="double-scroller"><div></div></div>
                <div class="scale-to-fill tile-content hide-titles-mob set-section-height"><?php
                    if ( $page=='filter' ) {
                        include('status.php');
                    } else {
                        include('dashboard.php');
                    } ?>
                </div>
                <div class="col-xs-12 show-on-mob"><?php
					include('status_mobile.php');
				?></div><!-- .tile-content -->

                <div class="clearfix"></div>
            </div><!-- .tile-container -->

            <div class="clearfix"></div>

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>
