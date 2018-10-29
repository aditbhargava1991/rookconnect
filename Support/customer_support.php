<?php include_once('config.php'); ?>
<?php if(!IFRAME_PAGE) { ?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.hide-titles-mob .standard-body-title').is(':visible')) {
			var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.tile-container').offset().top;
			if(available_height > 200) {
				$('.main-screen .has-main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.tile-sidebar').outerHeight(available_height).css('overflow-y','hidden');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
			}
		}
	}).resize();
	$('#mobile .panel-heading').off('click',loadPanel).click(loadPanel);
});
function loadPanel(panel_head, url) {
    if(panel_head.target != undefined) {
        panel_head = panel_head.target;
    }
	$(panel_head).off('click',loadPanel);
	var panel = $(panel_head).closest('.panel').find('.panel-body');
	panel.html('Loading...');
    if(url == undefined || url == '') {
        url = panel.data('file-name');
    }
	panel.load(url);
}
</script>
<?php } ?>
</head>
<body>
<?php include_once ('../navigation.php'); ?>

<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="ticket_iframe" src=""></iframe>
		</div>
	</div>
    <div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header" style="<?= IFRAME_PAGE ? 'display:none;' : '' ?>">
                <div class="pull-right settings-block hide-titles-mob"><?php
                    if($security['config'] > 0) {
                        echo "<div class='pull-right gap-left'><a href='?tab=field_config_comm'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>";
                    } ?>
                </div>
                <div class="scale-to-fill">
					<h1><a href="?">Customer Support</a></h1>
				</div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->
			
			<div class="standard-collapsible tile-sidebar set-section-height hide-titles-mob">
				<ul class="sidebar">
					<?php if(in_array($current_tab,['field_config_comm','field_config_tabs'])) { ?>
						<a href='?tab=field_config_comm'><li <?= $current_tab == 'field_config_comm' ? 'class="active"' : '' ?>>Communication</li></a>
						<a href='?tab=field_config_tabs'><li <?= $current_tab == 'field_config_tabs' ? 'class="active"' : '' ?>>Active Tabs</li></a>
					<?php } else {
                        foreach($tab_list as $tab_id) {
                            switch($tab_id) {
                                case 'services': ?>
                                    <a href='?tab=services'><li <?= $current_tab == 'services' ? 'class="active"' : '' ?>>FFM Services</li></a>
                                    <?php break;
                                case 'scrum': ?>
                                    <a href='?tab=scrum'><li <?= $current_tab == 'scrum' ? 'class="active"' : '' ?>>Scrum Board</li></a>
                                    <?php break;
                                case 'new': ?>
                                    <a href='?tab=requests&type=new'><li <?= $current_tab == 'requests' && $request_tab == 'new' ? 'class="active"' : '' ?>>New Request</li></a>
                                    <?php break;
                                case 'feedback': ?>
                                    <?php $count_row = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT SUM(IF(`support_type`='feedback' AND `deleted`=0, 1, 0)) `count` FROM `support` WHERE `businessid`='$user' OR '$user_category'  IN (".STAFF_CATS.")")); ?>
                                    <a href='?tab=requests&type=feedback'><li <?= $current_tab == 'requests' && $request_tab == 'feedback' ? 'class="active"' : '' ?>>Feedback & Ideas<span class="pull-right"><?= $count_row['count'] ?></span></li></a>
                                    <?php break;
                                case 'documents': ?>
                                    <a href='?tab=documents'><li <?= $current_tab == 'documents' ? 'class="active"' : '' ?>>Customer Documents</li></a>
                                    <?php break;
                                case 'meetings': ?>
                                    <a href='?tab=meetings'><li <?= $current_tab == 'meetings' ? 'class="active"' : '' ?>>Agendas & Meetings</li></a>
                                    <?php break;
                                case 'customer': ?>
                                    <a href='?tab=customer'><li <?= $current_tab == 'customer' ? 'class="active"' : '' ?>>Information Requests</li></a>
                                    <?php break;
                                case 'closed': ?>
                                    <?php $date = date('Y-m-d',strtotime('-2month'));
                                    $count_row = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT SUM(IF(`deleted`=1 AND `archived_date` > '$date', 1, 0)) `count` FROM `support` WHERE `businessid`='$user' OR '$user_category'  IN (".STAFF_CATS.")")); ?>
                                    <a href='?tab=requests&type=closed'><li <?= $current_tab == 'requests' && $request_tab == 'closed' ? 'class="active"' : '' ?>>Closed Requests<span class="pull-right"><?= $count_row['count'] ?></span></li></a>
                                    <?php break;
                                default:
                                    foreach($ticket_types as $type) {
                                        if(config_safe_str($type) == $tab_id) {
                                            $count_row = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT SUM(IF(`support_type`='".config_safe_str($type)."' AND `deleted`=0, 1, 0)) `count` FROM `support` WHERE `businessid`='$user' OR '$user_category'  IN (".STAFF_CATS.")")); ?>
                                            <a href='?tab=requests&type=<?= config_safe_str($type) ?>'><li <?= $current_tab == 'requests' && $request_tab == config_safe_str($type) ? 'class="active"' : '' ?>><?= $type ?><span class="pull-right"><?= $count_row['count'] ?></span></li></a>
                                        <?php }
                                    }
                                    break;
                            }
                        }
                    } ?>
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="tile-container" style="height: 100%;">
	            <div class="scale-to-fill has-main-screen tile-content hide-titles-mob">
					<div class="standard-body-title"><h3>
                        <?php if($_GET['tab'] == 'requests' && $_GET['type'] != 'new') { ?>
                            <img src="../img/icons/ROOK-3dot-icon.png" class="no-toggle cursor-hand offset-left-5 theme-color-icon pull-right" title="" width="25" data-title="Show/Hide Search" onclick="$('.search-group').toggle();">
                        <?php } ?>
                        <?php if($_GET['tab'] == 'requests' && $_GET['type'] != 'new' && $_GET['type'] != 'closed') { ?>
                            <a href="?tab=requests&type=new&new_type=<?=  $request_tab ?>&source=tab" class="btn brand-btn pull-right" style="margin-top:-0.3em;">New <?= $request_tab_name ?></a>
                        <?php } ?>
                        <?php switch($current_tab) {
						case 'field_config_comm':
                            echo "Communication Settings"; break;
						case 'field_config_tabs':
                            echo "Tab Settings"; break;
						case 'services':
                            echo "FFM Services"; break;
						case 'requests':
                            echo $request_tab_name; break;
						case 'meetings':
                            echo "Agendas & Meetings Dashboard"; break;
						case 'documents':
                            echo "Customer Documents Dashboard"; break;
						case 'scrum':
                            echo "Scrum Board"; break;
					} ?>
                    <?= !empty($user_name) ? '<br /><em><small>User: '.$user_name.'</small></em>' : '' ?>
                    </h3></div>
                    <div class="standard-body-content">
                        <?php if(!$dbc_support) { ?>
                            <div class="notice double-gap-bottom">
                                <img src="<?= WEBSITE_URL; ?>/img/error.png" class="wiggle-me" style="width:3em;">
                                <div style="float:right; width:calc(100% - 4em);"><span class="notice-name">Error:</span>
                                The software is unable to connect to the database. No support requests can be logged. We are working to resolve this error as quickly as possible. Your patience is appreciated.</div>
                                <div class="clearfix"></div>
                                <!--ERROR: #<?= mysqli_connect_errno() ?> - <?= mysqli_connect_error() ?>-->
                            </div>
                        <?php } else { ?>
                            <div id="no-more-tables">
                                <div class="form-horizontal col-sm-12"><?php include($current_tab.'.php'); ?></div>
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                    </div>
				</div>
	            <div class="show-on-mob full-width">
                    <div id="mobile" class="panel-group block-panels sidebar">
                        <?php if(!$dbc_support) { ?>
                            <div class="notice double-gap-bottom">
                                <img src="<?= WEBSITE_URL; ?>/img/error.png" class="wiggle-me" style="width:3em;">
                                <div style="float:right; width:calc(100% - 4em);"><span class="notice-name">Error:</span>
                                The software is unable to connect to the database. No support requests can be logged. We are working to resolve this error as quickly as possible. Your patience is appreciated.</div>
                                <div class="clearfix"></div>
                                <!--ERROR: #<?= mysqli_connect_errno() ?> - <?= mysqli_connect_error() ?>-->
                            </div>
                        <?php } else if($current_tab == 'requests' && $request_tab == 'new' && empty($_POST['new_request'])) { ?>
                            <div id="no-more-tables">
                                <div class="form-horizontal col-sm-12"><?php include($current_tab.'.php'); ?></div>
                            </div>
                        <?php } else {
                            foreach($tab_list as $tab_id) {
                                switch($tab_id) {
                                    case 'services': ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading mobile_load">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#mobile"href="#collapse_services">
                                                        FFM Services<span class="glyphicon glyphicon-plus"></span>
                                                    </a>
                                                </h4>
                                            </div>

                                            <div id="collapse_services" class="panel-collapse collapse">
                                                <div class="panel-body" data-file-name="services.php">
                                                    Loading...
                                                </div>
                                            </div>
                                        </div>
                                        <?php break;
                                    case 'new': ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading mobile_load">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#mobile"href="#collapse_new_request">
                                                        New Request<span class="glyphicon glyphicon-plus"></span>
                                                    </a>
                                                </h4>
                                            </div>

                                            <div id="collapse_new_request" class="panel-collapse collapse">
                                                <div class="panel-body" data-file-name="requests.php?type=new">
                                                    Loading...
                                                </div>
                                            </div>
                                        </div>
                                        <?php break;
                                    case 'feedback': ?>
                                        <?php $count_row = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT SUM(IF(`support_type`='feedback' AND `deleted`=0, 1, 0)) `count` FROM `support` WHERE `businessid`='$user' OR '$user_category'  IN (".STAFF_CATS.")")); ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading mobile_load">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#mobile"href="#collapse_feedback">
                                                        Feedback & Ideas - <?= $count_row['count'] ?><span class="glyphicon glyphicon-plus"></span>
                                                    </a>
                                                </h4>
                                            </div>

                                            <div id="collapse_feedback" class="panel-collapse collapse">
                                                <div class="panel-body" data-file-name="requests.php?type=feedback">
                                                    Loading...
                                                </div>
                                            </div>
                                        </div>
                                        <?php break;
                                    case 'documents': ?>
                                        <a href='?tab=documents'><li <?= $current_tab == 'documents' ? 'class="active"' : '' ?>>Customer Documents</li></a>
                                        <?php break;
                                    case 'meetings': ?>
                                        <a href='?tab=meetings'><li <?= $current_tab == 'meetings' ? 'class="active"' : '' ?>>Agendas & Meetings</li></a>
                                        <?php break;
                                    case 'customer': ?>
                                        <a href='?tab=customer'><li <?= $current_tab == 'customer' ? 'class="active"' : '' ?>>Information Requests</li></a>
                                        <?php break;
                                    case 'closed': ?>
                                        <?php $date = date('Y-m-d',strtotime('-2month'));
                                        $count_row = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT SUM(IF(`deleted`=1 AND `archived_date` > '$date', 1, 0)) `count` FROM `support` WHERE `businessid`='$user' OR '$user_category'  IN (".STAFF_CATS.")")); ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading mobile_load">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#mobile"href="#collapse_closed">
                                                        Closed Requests - <?= $count_row['count'] ?><span class="glyphicon glyphicon-plus"></span>
                                                    </a>
                                                </h4>
                                            </div>

                                            <div id="collapse_closed" class="panel-collapse collapse">
                                                <div class="panel-body" data-file-name="requests.php?type=closed">
                                                    Loading...
                                                </div>
                                            </div>
                                        </div>
                                        <?php break;
                                    default:
                                        foreach($ticket_types as $type) {
                                            if(config_safe_str($type) == $tab_id) {
                                                $count_row = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT SUM(IF(`support_type`='".config_safe_str($type)."' AND `deleted`=0, 1, 0)) `count` FROM `support` WHERE `businessid`='$user' OR '$user_category'  IN (".STAFF_CATS.")")); ?>
                                                <div class="panel panel-default">
                                                    <div class="panel-heading mobile_load">
                                                        <h4 class="panel-title">
                                                            <a data-toggle="collapse" data-parent="#mobile"href="#collapse_<?= config_safe_str($type) ?>">
                                                                <?= $type ?> - <?= $count_row['count'] ?><span class="glyphicon glyphicon-plus"></span>
                                                            </a>
                                                        </h4>
                                                    </div>

                                                    <div id="collapse_<?= config_safe_str($type) ?>" class="panel-collapse collapse">
                                                        <div class="panel-body" data-file-name="requests.php?type=<?= config_safe_str($type) ?>">
                                                            Loading...
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                        }
                                        break;
                                }
                            } ?>
                        <?php } ?>
                        <div class="clearfix"></div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('../footer.php');
