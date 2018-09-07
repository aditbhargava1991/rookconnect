<?php
/*
 * Tasks Main Index File
 * Included Files:
 *   - tasks_dashboard.php
 *   - add_task.php
 *   - add_taskboard.php
 *   - tab_reporting.php
 *   - task_milestones.php
 *   - tab_summary.php
 *   - tasks_search.php
 */
error_reporting(0);
include_once('../include.php');

/* //Auto archive old Tasks
*** Moved to /cronjobs/clear_completed_tasks.php ***
$task_statuses = explode(',',get_config($dbc, 'task_status'));
$status_complete = $task_statuses[count($task_statuses) - 1];
$tasklist_auto_archive = get_config($dbc, 'tasklist_auto_archive');
if($tasklist_auto_archive == 1) {
    $tasklist_auto_archive_days = get_config($dbc, 'tasklist_auto_archive_days');
    if($tasklist_auto_archive_days > 0) {
        $today_date = date('Y-m-d', strtotime(date('Y-m-d').' - '.$tasklist_auto_archive_days.' days'));
        $old_tasks = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `status` = '$status_complete' AND `status_date` <= '$today_date' AND `status_date` != '0000-00-00' AND `deleted` = 0"),MYSQLI_ASSOC);
        foreach ($old_tasks as $old_task) {
            mysqli_query($dbc, "UPDATE `tasklist` SET `deleted` = 1 WHERE `tasklistid` = '".$old_task['tasklistid']."'");
        }
    }
} */
?>
<style type="text/css">
    .ui-state-disabled { pointer-events:none !important; }
    footer { position:relative; z-index:100; }
</style>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').is(':visible')) {
			var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
			if(available_height > 200) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
                $('.main-screen .standard-dashboard-body-content').outerHeight(available_height - $('.standard-dashboard-body-title').height());
			}
            var sidebar_height = $('.tile-sidebar').outerHeight(true);
            $('.has-main-screen .main-screen').css('min-height', sidebar_height);
		}
	}).resize();

    $('.panel-heading').click(loadPanel);

	$('.sidebar a').click(function(event) {
        if(!event.isDefaultPrevented() && $(this).attr('target') != '_blank' && this.href != '' && this.href != undefined && $(this).attr('href')!='javascript:void(0);') {
            loadingOverlayShow('.has-main-screen', $('.has-main-screen').height());
		}
	});

    $('.search_list').keypress(function(e) {
        if(e.which==13) {
            var term = $(this).val();
            window.location.replace('../Tasks/index.php?category=All&tab=Search&term='+term);
        }
    });

    $('.search_list_mobile').keypress(function(e) {
        if(e.which==13) {
            var term = $(this).val();
            $.ajax({
                url: '../Tasks/tasks_search.php?term='+term,
                method: 'GET',
                response: 'html',
                success: function(response) {
                    $('#search_results_mobile').html(response);
                    $('.panel').hide();
                }
            });
        }
    });
});

function loadPanel() {
    $('#accordions .panel-heading:not(.higher_level_heading)').closest('.panel').find('.panel-body').html('Loading...');
    if(!$(this).hasClass('higher_level_heading')) {
        var panel = $(this).closest('.panel').find('.panel-body');
        $(panel).html('Loading...');
        $.ajax({
            url: $(panel).data('file'),
            method: 'POST',
            response: 'html',
            success: function(response) {
                $(panel).html(response);
            }
        });
    }
}

function popUpClosed() {
    window.location.reload();
}
</script>
</head>

<body>
<?php
    include_once ('../navigation.php');
checkAuthorised('tasks');
    $contactid = $_SESSION['contactid'];
    $category = trim($_GET['category']);
    $url_tab = trim($_GET['tab']);
?>

<div class="container">
	<div class="iframe_overlay" style="display:none; margin-top:-20px; padding-bottom:20px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="edit_board" src=""></iframe>
		</div>
	</div>

    <div class="iframe_holder" style="display:none;">
		<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
		<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
		<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
	</div>

    <div class="row hide_on_iframe">
		<div class="main-screen">
			<div class="tile-header">
                <div class="pull-right settings-block">
                    <div class="pull-right gap-left"><a href="field_config_project_manage.php?category=how_to"><img src="<?= WEBSITE_URL ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30" /></a></div>
                    <div class="pull-right gap-left"><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_taskboard.php?security=<?=$url_tab?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><button class="btn brand-btn hide-titles-mob">Add Task Board</button></a></div>
                    <div class="pull-right gap-left"><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks/add_task.php?category=<?=$_GET['category']?>&tab=<?=$_GET['tab']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><button class="btn brand-btn hide-titles-mob">Add Task</button></a></div>
                    <img class="no-toggle statusIcon pull-right no-margin inline-img" title="" src="" />
                </div>
                <div class="scale-to-fill"><h1 class="gap-left"><a href="index.php">Tasks</a></h1></div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

			<div class="clearfix"></div>

            <!-- Mobile View -->
            <div id="accordions" class="sidebar show-on-mob panel-group block-panels col-xs-12 form-horizontal">
                <div class="double-gap-bottom gap-right"><input class="form-control search_list_mobile" placeholder="Search Tasks" type="text" /></div>
                <div id="search_results_mobile"></div>
                <div class="panel panel-default" style="border-top:1px solid #ccc !important;">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordions" href="#collapse_summary">
                                Summary<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_summary" class="panel-collapse collapse">
                        <div class="panel-body" data-file="tab_summary.php?category=All&tab=Summary">
                            Loading...
                        </div>
                    </div>
                </div><?php //$get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(tl.tasklistid) `task_count`, SUM(IF(IFNULL(`updated_date`,`created_date`) > IFNULL(`ts`.`seen_date`,'0000-00-00'),1,0)) `unseen` FROM tasklist tl JOIN task_board tb ON (tb.taskboardid=tl.task_board) LEFT JOIN taskboard_seen ts ON ts.`contactid`='{$_SESSION['contactid']}' AND ts.`taskboardid`=0 WHERE (tl.contactid IN ({$_SESSION['contactid']}) OR (tb.board_security='Company' AND tb.company_staff_sharing LIKE '%,{$_SESSION['contactid']},%')) AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.deleted=0 AND tb.deleted=0 ORDER BY tl.task_tododate")); ?>
                <?php
                if (check_subtab_persmission($dbc, 'tasks', ROLE, 'my') === true) {
                    $result_mytasks = mysqli_query($dbc, "SELECT `task_board`.`taskboardid`, `board_name`, `board_security`, IFNULL(`seen_date`,'0000-00-00') `seen` FROM `task_board` LEFT JOIN `taskboard_seen` ON `task_board`.`taskboardid`=`taskboard_seen`.`taskboardid` AND `taskboard_seen`.`contactid`='{$_SESSION['contactid']}' WHERE `board_security`='Private' AND `company_staff_sharing` LIKE '%,". $contactid .",%' AND `deleted`=0");
                    if ( $result_mytasks->num_rows > 0 ) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading higher_level_heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordions" href="#collapse_private">
                                        Private Tasks<span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>

                            <div id="collapse_private" class="panel-collapse collapse">
                                <div class="panel-body" style="padding: 0; margin: -1px;" id="collapse_private_body">
                                    <?php while ( $row_mytasks=mysqli_fetch_assoc($result_mytasks) ) {
                                        $result_alert = mysqli_query($dbc, "SELECT taskboardid FROM task_board WHERE board_security='Private' AND company_staff_sharing LIKE '%," . $contactid . ",%' AND `deleted`=0");
                                        $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row_mytasks['seen']}',1,0)) as `unseen` FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.task_board='{$row_mytasks['taskboardid']}' AND tb.board_security='Private' AND tl.task_milestone_timeline<>'' AND tl.contactid IN (". $_SESSION['contactid'] .") AND tl.deleted=0 AND tb.deleted=0"));
                                        $task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0; ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#collapse_private_body" href="#collapse_<?= $row_mytasks['taskboardid'] ?>" class="double-pad-left">
                                                        <?= $row_mytasks['board_name'] ?><span class="pull-right"><?= $get_count['task_count'].($_GET['category']!=$row_mytasks['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '') ?></span><span class="glyphicon glyphicon-plus"></span>
                                                    </a>
                                                </h4>
                                            </div>

                                            <div id="collapse_<?= $row_mytasks['taskboardid'] ?>" class="panel-collapse collapse">
                                                <div class="panel-body" data-file="tasks_dashboard.php?category=<?= $row_mytasks['taskboardid'] ?>&tab=<?= $row_mytasks['board_security'] ?>">
                                                    Loading...
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordions" href="#collapse_private">
                                        Private Tasks<span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>

                            <div id="collapse_private" class="panel-collapse collapse">
                                <div class="panel-body" data-file="tasks_dashboard.php?category=All&tab=Private">
                                    Loading...
                                </div>
                            </div>
                        </div>
                    <?php }
                }

                $get_field_task_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `task_dashboard_tile` FROM `task_dashboard`"));
                $tasks_name = explode(',', $get_field_task_config['task_dashboard_tile']);
                $tasks_name = array_filter($tasks_name);
                if ( $_GET['category'] != 'All' ) {
                    $taskboardid = preg_replace('/[^0-9]/', '', $_GET['category']);
                    $result_taskboardid = mysqli_query($dbc, "SELECT `board_security` FROM `task_board` WHERE `taskboardid`='$taskboardid'");
                    if ( $result_security->num_rows > 0 ) {
                        $row = mysqli_fetch_assoc($dbc, $result_security);
                        $board = $row['board_security'];
                    }
                }
                foreach($tasks_name as $task_name) {
                    $task_file_path = str_replace(" ", "_", strtolower($task_name));
                    $info = '';
                    $security = '';
                    $tab = '';

                    switch($task_file_path) {
                        case 'company_tasks':
                            $info = "Click here to see shared tasks.";
                            $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'company') !== false ) ? 1 : 0;
                            $security = 'Company';
                            $tab = 'Company';
                            break;
                        case 'project_tasks':
                            $info = "Click here to view all project tasks.";
                            $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'project') !== false ) ? 1 : 0;
                            $security = 'path';
                            $tab = 'path';
                            break;
                        case 'client_tasks':
                            $info = "Click here to view all contacts related tasks.";
                            $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'client') !== false ) ? 1 : 0;
                            $security = 'Client';
                            $tab = 'Client';
                            break;
                        case 'reporting':
                            $info = "Click here to see task reporting.";
                            $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'reporting') !== false ) ? 1 : 0;
                            $tab = 'Reporting';
                            break;
                        default:
                            $info = "Click here to see all tasks.";
                            break;
                    }

                    if ( $display==1 ) {
                        if ( $security != '' ) {
                            $result = mysqli_query($dbc, "SELECT `task_board`.`taskboardid`, `board_name`, `board_security`, IFNULL(`seen_date`,'0000-00-00') `seen` FROM `task_board` LEFT JOIN `taskboard_seen` ON `task_board`.`taskboardid`=`taskboard_seen`.`taskboardid` AND `taskboard_seen`.`contactid`='{$_SESSION['contactid']}' WHERE `board_security`='". $security ."' AND `company_staff_sharing` LIKE '%,". $contactid .",%' AND `deleted`=0");
                            if ( $result->num_rows > 0 ) {
                                if ( $task_name=='Company Tasks' ) {
                                    $task_name = 'Shared Tasks';
                                }
                                if ( $task_name=='Client Tasks' ) {
                                    $task_name = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' Tasks' : CONTACTS_TILE.' Tasks';
                                }
                                $collapse_taskboard = config_safe_str($task_name); ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading higher_level_heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordions" href="#collapse_<?= $collapse_taskboard ?>">
                                                <?= $task_name ?><span class="glyphicon glyphicon-plus"></span>
                                            </a>
                                        </h4>
                                    </div>

                                    <div id="collapse_<?= $collapse_taskboard ?>" class="panel-collapse collapse">
                                        <div class="panel-body" style="padding: 0; margin: -1px;" id="collapse_<?= $collapse_taskboard ?>_body">
                                            <?php while ( $row=mysqli_fetch_assoc($result) ) {
                                                $result_alert = mysqli_query($dbc, "SELECT taskboardid FROM task_board WHERE board_security='$security' AND company_staff_sharing LIKE '%," . $contactid . ",%' AND `deleted`=0");
                                                $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row['seen']}',1,0)) as `unseen` FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.task_board='{$row['taskboardid']}' AND tb.board_security='$tab' AND tl.task_milestone_timeline<>'' AND tl.deleted=0 AND tb.deleted=0"));
                                                $task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0; ?>
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a data-toggle="collapse" data-parent="#collapse_<?= $collapse_taskboard ?>_body" href="#collapse_<?= $row['taskboardid'] ?>" class="double-pad-left">
                                                                <?= $row['board_name'] ?><span class="pull-right"><?= $get_count['task_count'].($_GET['category']!=$row['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '') ?></span><span class="glyphicon glyphicon-plus"></span>
                                                            </a>
                                                        </h4>
                                                    </div>

                                                    <div id="collapse_<?= $row['taskboardid'] ?>" class="panel-collapse collapse">
                                                        <div class="panel-body" data-file="tasks_dashboard.php?category=<?= $row['taskboardid'] ?>&tab=<?= $tab ?>">
                                                            Loading...
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } else {
                                if ( $task_name=='Client Tasks' ) {
                                    $task_name = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' Tasks' : CONTACTS_TILE.' Tasks';
                                }
                                $collapse_taskboard = config_safe_str($task_name); ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordions" href="#collapse_<?= $collapse_taskboard ?>">
                                                <?= $task_name ?><span class="glyphicon glyphicon-plus"></span>
                                            </a>
                                        </h4>
                                    </div>

                                    <div id="collapse_<?= $collapse_taskboard ?>" class="panel-collapse collapse">
                                        <div class="panel-body" data-file="tasks_dashboard.php?category=All&tab=<?= $tab ?>">
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        }
                    }
                } ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordions" href="#collapse_reporting">
                                Reporting<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_reporting" class="panel-collapse collapse">
                        <div class="panel-body" data-file="tab_reporting.php">
                            Loading...
                        </div>
                    </div>
                </div>
            </div><!-- #accordions -->

            <!--<div class="collapsible hide-titles-mob sidebar tile-sidebar sidebar-override inherit-height double-gap-top">-->
            <div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
                <ul><?php
                    echo '<li class="standard-sidebar-searchbox"><input class="form-control search_list" placeholder="Search Tasks" type="text" /></li>';
                    echo '<li class="sidebar-higher-level highest-level"><a href="?category=All&tab=Summary" class="cursor-hand '.($_GET['tab']=='Summary' ? 'active blue' : '').'">Summary</a></li>';

                    if (check_subtab_persmission($dbc, 'tasks', ROLE, 'my') === true) {
                        $result_mytasks = mysqli_query($dbc, "SELECT `task_board`.`taskboardid`, `board_name`, `board_security`, IFNULL(`seen_date`,'0000-00-00') `seen` FROM `task_board` LEFT JOIN `taskboard_seen` ON `task_board`.`taskboardid`=`taskboard_seen`.`taskboardid` AND `taskboard_seen`.`contactid`='{$_SESSION['contactid']}' WHERE `board_security`='Private' AND `company_staff_sharing` LIKE '%,". $contactid .",%' AND `deleted`=0");
                        if ( $result_mytasks->num_rows > 0 ) {
                            echo '<li class="sidebar-higher-level highest-level"><a class="'.(trim($_GET['tab']) == 'Private' ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#my_tasks" href="javascript:void(0);">Private Tasks<span class="arrow"></span></a>';
                                echo '<ul id="my_tasks" class="collapse '.(trim($_GET['tab']) == 'Private' ? 'in' : '').'">';
                                    while ( $row_mytasks=mysqli_fetch_assoc($result_mytasks) ) {
                                        $result_alert = mysqli_query($dbc, "SELECT taskboardid FROM task_board WHERE board_security='Private' AND company_staff_sharing LIKE '%," . $contactid . ",%' AND `deleted`=0");

                                        $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row_mytasks['seen']}',1,0)) as `unseen` FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.task_board='{$row_mytasks['taskboardid']}' AND tb.board_security='Private' AND tl.task_milestone_timeline<>'' AND tl.contactid IN (". $_SESSION['contactid'] .") AND tl.deleted=0 AND tb.deleted=0"));
                                        $task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0;

                                        echo '<a href="?category='. $row_mytasks['taskboardid'] .'&tab='. $row_mytasks['board_security'] .'">
                                        <li class="'.($_GET['category']==$row_mytasks['taskboardid'] ? 'active' : '').'">'. $row_mytasks['board_name'] .'<span class="pull-right pad-right">'. $get_count['task_count'] .($_GET['category']!=$row_mytasks['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '').'</li></a>';
                                    }
                                echo '</ul>';
                            echo '</li>';
                        } else {
                            echo '<li class="sidebar-higher-level highest-level"><a class="cursor-hand '.($_GET['tab']==$tab ? 'active blue' : '').'" href="?category=All&tab=Private">Private Tasks</a></li>';
                        }
                    } else {
                        echo '<li>Private Tasks</li>';
                    }

                    $get_field_task_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `task_dashboard_tile` FROM `task_dashboard`"));
                    $tasks_name = explode(',', $get_field_task_config['task_dashboard_tile']);
                    $tasks_name = array_filter($tasks_name);
                    if ( $_GET['category'] != 'All' ) {
                        $taskboardid = preg_replace('/[^0-9]/', '', $_GET['category']);
                        $result_taskboardid = mysqli_query($dbc, "SELECT `board_security` FROM `task_board` WHERE `taskboardid`='$taskboardid'");
                        if ( $result_security->num_rows > 0 ) {
                            $row = mysqli_fetch_assoc($dbc, $result_security);
                            $board = $row['board_security'];
                        }
                    }

                    foreach($tasks_name as $task_name) {
                        $task_file_path = str_replace(" ", "_", strtolower($task_name));
                        $info = '';
                        $security = '';
                        $tab = '';

                        switch($task_file_path) {
                            case 'company_tasks':
                                $info = "Click here to see shared tasks.";
                                $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'company') !== false ) ? 1 : 0;
                                $security = 'Company';
                                $tab = 'Company';
                                break;
                            case 'project_tasks':
                                $info = "Click here to view all project tasks.";
                                $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'project') !== false ) ? 1 : 0;
                                $security = 'path';
                                $tab = 'path';
                                break;
                            case 'client_tasks':
                                $info = "Click here to view all contacts related tasks.";
                                $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'client') !== false ) ? 1 : 0;
                                $security = 'Client';
                                $tab = 'Client';
                                break;
                            case 'sales_tasks':
                                $info = "Click here to view all ".SALES_TILE." tasks.";
                                $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'sales') !== false ) ? 1 : 0;
								$security = 'sales';
                                $tab = 'sales';
                                break;
                            case 'reporting':
                                $info = "Click here to see task reporting.";
                                $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'reporting') !== false ) ? 1 : 0;
                                $tab = 'Reporting';
                                break;
                            default:
                                $info = "Click here to see all tasks.";
                                break;
                        }

                        if ( $display==1 ) {
                            if ( $security == 'sales' ) {
								echo '<li class="sidebar-higher-level highest-level"><a class="'.(trim($_GET['tab']) == $tab ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#board_'.$tab.'" href="javascript:void(0);">'. SALES_TILE .' Tasks<span class="arrow"></span></a>';
									echo '<ul id="board_'.$tab.'" class="collapse '.(trim($_GET['tab']) == $tab ? 'in' : '').'">';
										$result = sort_contacts_query($dbc->query("SELECT `sales`.`salesid`, `contacts`.`first_name`, `contacts`.`last_name`, `bus`.`name`, IFNULL(`taskboard_seen`.`seen_date`,'0000-00-00') `seen` FROM `sales` LEFT JOIN `contacts` ON `sales`.`contactid`=`contacts`.`contactid` LEFT JOIN `contacts` `bus` ON `sales`.`businessid`=`bus`.`contactid` LEFT JOIN `taskboard_seen` ON `taskboard_seen`.`taskboardid`=`sales`.`salesid` AND `taskboard_seen`.`tab`='sales' WHERE `sales`.`deleted`=0"));
										foreach($result as $row) {
											$get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row['seen']}',1,0)) as `unseen` FROM tasklist tl WHERE tl.salesid='{$row['salesid']}' AND tl.deleted=0"));
											$task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0;

											echo '<a href="?category='. $row['salesid'] .'&tab='.$tab.'"><li class="'.($_GET['category']==$row['salesid'] && $_GET['tab'] == $tab ? 'active' : '').'">'.$row['name'].($row['name'] != '' && $row['first_name'].$row['last_name'] != '' ? ': ' : '').$row['first_name'].' '.$row['last_name'].'<span class="pull-right pad-right">'. $get_count['task_count'] .($_GET['category']!=$row['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '').'</span></li></a>';
										}
									echo '</ul>';
								echo '</li>';

                            } else if ( $security == 'Company' ) {
                                $result = mysqli_query($dbc, "SELECT `task_board`.`taskboardid`, `board_name`, `board_security`, `company_staff_sharing`, IFNULL(`seen_date`,'0000-00-00') `seen` FROM `task_board` LEFT JOIN `taskboard_seen` ON `task_board`.`taskboardid`=`taskboard_seen`.`taskboardid` AND `taskboard_seen`.`contactid`='{$_SESSION['contactid']}' AND IFNULL(`taskboard_seen`.`tab`,'$tab') = '$tab' WHERE `board_security`='". $security ."' AND `company_staff_sharing` LIKE '%,". $contactid .",%' AND `deleted`=0");

                                if ( $result->num_rows > 0 ) {
                                    if ( $task_name=='Company Tasks' ) {
                                        $task_name = 'Shared Tasks';
                                    }
                                    $shared_task_boards = '';
                                    $shared_task_staff = '';

                                    while ( $row=mysqli_fetch_assoc($result) ) {
                                        $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row['seen']}',1,0)) as `unseen` FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.task_board='{$row['taskboardid']}' AND tb.board_security='$tab' AND tl.task_milestone_timeline<>'' AND tl.deleted=0 AND tb.deleted=0"));

                                        $task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0;

                                        $shared_task_boards .= '<a href="?category='. $row['taskboardid'] .'&tab='.$tab.'&subtab=board"><li class="'.($_GET['category']==$row['taskboardid'] ? 'active' : '').'">'. $row['board_name'] .'<span class="pull-right pad-right">'. $get_count['task_count'] .($_GET['category']!=$row['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '').'</span></li></a>';

                                        $company_staff_sharing = '';
                                        foreach ( array_filter(explode(',', $row['company_staff_sharing'])) as $staffid ) {
                                            $company_staff_sharing .= get_staff($dbc, $staffid) .', ';
                                        }
                                        $company_staff_sharing = rtrim($company_staff_sharing, ', ');

                                        $shared_task_staff .= '<a href="?category='. $row['taskboardid'] .'&tab='.$tab.'&subtab=staff"><li class="'.($_GET['category']==$row['taskboardid'] ? 'active' : '').'"><div class="pull-left" style="max-width:85%;">'. $company_staff_sharing .'</div><div class="pull-right pad-right">'. $get_count['task_count'] .($_GET['category']!=$row['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</div>)' : '').'</span></li></a><div class="clearfix"></div>';
                                    }

                                    echo '<li class="sidebar-higher-level highest-level"><a class="'.(trim($_GET['tab']) == $tab ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#board_'.$tab.'" href="javascript:void(0);">'. $task_name .'<span class="arrow"></span></a>';



                                        echo '<ul id="board_'.$tab.'" class="collapse '.(trim($_GET['tab']) == $tab ? 'in' : '').'">';

                                            echo '<li class="sidebar-higher-level"><a class="'.(trim($_GET['tab'])==$tab && trim($_GET['subtab'])=='board' ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#shared_boards">Task Boards <span class="arrow"></span></a>';
                                                echo '<ul id="shared_boards" class="'.(trim($_GET['tab'])==$tab && trim($_GET['subtab'])=='board' && $_GET['category']!='' ? 'collapsed active' : 'collapse').'">';
                                                    echo $shared_task_boards;
                                                echo '</ul>';
                                            echo '</li>';

                                            echo '<li class="sidebar-higher-level"><a class="'.(trim($_GET['tab'])==$tab && trim($_GET['subtab'])=='staff' ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#shared_staff">Staff <span class="arrow"></span></a>';
                                                echo '<ul id="shared_staff" class="'.(trim($_GET['tab'])==$tab && trim($_GET['subtab'])=='staff' ? 'collapsed active' : 'collapse').'">';
                                                    echo $shared_task_staff;
                                                echo '</ul>';
                                            echo '</li>';

                                        echo '</ul>';
                                    echo '</li>';
                                }

                            } else if($security == 'path') {
                                echo '<li class="sidebar-higher-level highest-level"><a class="'.(trim($_GET['tab']) == $tab ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#board1_'.$tab.'" href="javascript:void(0);">'. $task_name .'<span class="arrow"></span></a>';

                                echo '<ul id="board1_'.$tab.'" class="collapse '.(trim($_GET['tab']) == $tab ? 'in' : '').'">';

                                //$result = mysqli_query($dbc, "SELECT projectid, project_name, project_path FROM project WHERE project_name != '' AND project_path > 0 AND projectid IN(SELECT projectid FROM tasklist WHERE deleted = 0 AND projectid>0)");

                                $result = mysqli_query($dbc, "SELECT DISTINCT(t.projectid), p.project_name, p.project_path FROM project p, tasklist t WHERE p.project_name != '' AND p.project_path > 0 AND p.projectid = t.projectid AND t.deleted = 0 AND p.deleted = 0 AND t.projectid>0 AND p.status != 'Archive' AND t.heading != ''");

                                while ( $row=mysqli_fetch_assoc($result) ) {
                                    $projectid = $row['projectid'];

                                    echo '<li class="sidebar-higher-level"><a class="'.(trim($_GET['tab'])==$tab && trim($_GET['edit'])==$projectid ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#shared_boards_'.$projectid.'">'.$row['project_name'].' <span class="arrow"></span></a>';

                                    $project_path = $row['project_path'];

                                    echo '<ul id="shared_boards_'.$projectid.'" class="collapse '.(trim($_GET['tab'])==$tab && trim($_GET['edit'])==$projectid ? 'in' : '').'">';

                                    foreach(explode(',',$row['project_path']) as $projectpathid) {
                                        $main_path = get_field_value('project_path','project_path_milestone','project_path_milestone',$projectpathid);

                                        echo '<a href="?category='. $projectid .'&tab=path&pathid=I|'.$projectpathid.'&edit='.$projectid.'">';

                                        $ex_projectpathid = explode('|',$_GET['pathid']);
                                        echo '<li data-target="#board923_'.$project_path.'" class="sidebar-lower-level  '.(($ex_projectpathid[1]==$projectpathid) && (trim($_GET['edit'])==$projectid) ? 'active' : 'collapsed').'" style="padding-left: 50px;">'. $main_path;

                                        echo '</li></a>';

                                    }
                                    echo '</ul>';
                                    echo '</li>';
                                }
                                    echo '</ul>';
                                    echo '</li>';

                            }



                            else if ( $security != '' ) {
                                $result = mysqli_query($dbc, "SELECT `task_board`.`taskboardid`, `board_name`, `board_security`, IFNULL(`seen_date`,'0000-00-00') `seen` FROM `task_board` LEFT JOIN `taskboard_seen` ON `task_board`.`taskboardid`=`taskboard_seen`.`taskboardid` AND `taskboard_seen`.`contactid`='{$_SESSION['contactid']}' AND IFNULL(`taskboard_seen`.`tab`,'$tab') = '$tab' WHERE `board_security`='". $security ."' AND `company_staff_sharing` LIKE '%,". $contactid .",%' AND `deleted`=0");
                                if ( $result->num_rows > 0 ) {

                                    if ( $task_name=='Company Tasks' ) {
                                        $task_name = 'Shared Tasks';
                                    }
                                    if ( $task_name=='Client Tasks' ) {
                                        $task_name = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' Tasks' : CONTACTS_TILE.' Tasks';
                                    }

                                    echo '<li class="sidebar-higher-level highest-level"><a class="'.(trim($_GET['tab']) == $tab ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#board_'.$tab.'" href="javascript:void(0);">'. $task_name .'<span class="arrow"></span></a>';
                                        echo '<ul id="board_'.$tab.'" class="collapse '.(trim($_GET['tab']) == $tab ? 'in' : '').'">';
                                            while ( $row=mysqli_fetch_assoc($result) ) {
                                                /* $result_alert = mysqli_query($dbc, "SELECT taskboardid FROM task_board WHERE board_security='$security' AND company_staff_sharing LIKE '%," . $contactid . ",%' AND `deleted`=0");

                                                while ( $row_alert=mysqli_fetch_array($result_alert) ) {
                                                    $tid = $row_alert['taskboardid'];
                                                    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(tasklistid) AS total_unread FROM tasklist WHERE task_board = '$tid' AND (DATE(`archived_date`) >= (DATE(NOW() - INTERVAL 3 DAY)) OR archived_date IS NULL OR archived_date = '0000-00-00') AND (task_tododate IS NULL OR task_tododate = '0000-00-00' OR (task_tododate< DATE(NOW()) AND status != 'Done')) AND `deleted`=0"));
                                                    $alert = '';
                                                    if($get_config['total_unread'] > 0) {
                                                        $alert = '<img class="pull-right" src="'.WEBSITE_URL.'/img/alert.png" border="0" alt="" />&nbsp;&nbsp;';
                                                    }
                                                } */

                                                $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row['seen']}',1,0)) as `unseen` FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.task_board='{$row['taskboardid']}' AND tb.board_security='$tab' AND tl.task_milestone_timeline<>'' AND tl.deleted=0 AND tb.deleted=0"));
                                                $task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0;

                                                //echo '<a href="?category='. $row['taskboardid'] .'&tab='.$tab.'"><li class="'.($_GET['category']==$row['taskboardid'] ? 'active' : '').'">'. $alert . $row['board_name'] .'<span class="pull-right">'. $get_count['task_count'] .'</span></li></a>';
                                                echo '<a href="?category='. $row['taskboardid'] .'&tab='.$tab.'"><li class="'.($_GET['category']==$row['taskboardid'] ? 'active' : '').'">'. $row['board_name'] .'<span class="pull-right pad-right">'. $get_count['task_count'] .($_GET['category']!=$row['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '').'</span></li></a>';
                                            }
                                        echo '</ul>';
                                    echo '</li>';
                                }  else {
                                    if ( $task_name=='Client Tasks' ) {
                                        $task_name = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' Tasks' : CONTACTS_TILE.' Tasks';
                                    }
                                    echo '<li class="sidebar-higher-level highest-level"><a class="'.($_GET['tab']==$tab ? 'cursor-hand active blue' : '').'" href="?category=All&tab='. $tab .'">'. $task_name .'</a></li>';
                                }
                            }
                        } else {
                            //echo '<li>'. $task_name .'</li>';
                        }

                    }

                    echo '<li class="sidebar-higher-level highest-level"><a class="cursor-hand '.($_GET['tab']==$tab ? 'active blue' : '').'" href="?category=All&tab=Reporting">Reporting</a></li>'; ?>
                </ul>
            </div><!-- .sidebar -->

            <div class="main-content-screen scale-to-fill has-main-screen hide-titles-mob">
                <div class="loading_overlay" style="display:none;"><div class="loading_wheel"></div></div>
                <div class="main-screen standard-dashboard-body override-main-screen form-horizontal">

                    <div class="standard-dashboard-body-title"><?php
                        $url_cat = filter_var($_GET['category'], FILTER_VALIDATE_INT);
                        $url_tab = filter_var($_GET['tab'], FILTER_SANITIZE_STRING);
                        $term = filter_var($_GET['term'], FILTER_SANITIZE_STRING);
                        $title = '';
                        $url_milestone = isset($_GET['milestone']) ? trim($_GET['milestone']) : '';
                        $board_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT board_name, company_staff_sharing FROM task_board WHERE taskboardid='$url_cat'"));
                        if ( $url_tab == 'Summary' ) {
                            $title = 'Summary';
                            $notes_subtab = 'tasks_summary';
                        } elseif ( $url_tab == 'Private' ) {
                            $title = 'Private Tasks';
                            $notes_subtab = 'tasks_private';
                        } elseif ( $url_tab == 'Company' ) {
                            $title = 'Shared Tasks';
                            $notes_subtab = 'tasks_company';
                        } elseif ( $url_tab == 'path' ) {
                            $title = (substr(PROJECT_TILE, -1)=='s' && substr(PROJECT_TILE, -2) !='ss') ? rtrim(PROJECT_TILE, 's').' Tasks' : PROJECT_TILE.' Tasks';
                            $notes_subtab = 'tasks_project';
                        } elseif ( $url_tab == 'Client' ) {
                            $title = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' Tasks' : CONTACTS_TILE.' Tasks';
                            $notes_subtab = 'tasks_client';
                        } elseif ( $url_tab == 'Reporting' ) {
                            $title = 'Reporting';
                            $notes_subtab = 'tasks_reporting';
                        } elseif ( $url_tab == 'Search' ) {
                            $title = 'Search';
                        } else {
                            $title = 'My Tasks';
                        }

                        if ( $url_tab == 'path' ) {

                        } else {

                            echo '<div class="row">';
                                echo '<div class="col-sm-6"><h3>'. ($title=='Search' ? $title .': '. $term : $title .': '. $board_name['board_name']) .'</h3></div>';
                                echo '<div class="col-sm-6 text-right">';
                                    if ( $url_tab!='Search' && $url_tab!='Summary' && $url_tab!='Reporting' ) {
                                        echo '<div class="gap-top gap-right" style="font-size:1.5em;">';
                                            if ( $board_name['company_staff_sharing'] ) {
                                                foreach ( array_filter(explode(',', $board_name['company_staff_sharing'])) as $staffid ) {
                                                    profile_id($dbc, $staffid);
                                                }
                                            } else {
                                                profile_id($dbc, $board_name['contactid']);
                                            }
                                        echo '</div>';
                                    }
                                echo '</div>';
                            echo '</div>';

                        }

                        if ( !empty($notes_subtab) ) {
                            $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='$notes_subtab'"));
                            $note = $notes['note'];

                            if ( !empty($note) ) { ?>
                                <div class="notice double-gap-bottom popover-examples">
                                    <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                                    <div class="col-sm-11">
                                        <span class="notice-name">NOTE:</span>
                                        <?= $note; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div><?php
                            }
                        } ?>
                    </div><!-- .standard-dashboard-body-title -->

                    <div class="standard-dashboard-body-content"><?php
                        if ( $url_tab=='Search' ) {
                            include('tasks_search.php');
                        } else { ?>
                            <div class="dashboard-item"><?php
                                if(!empty($_GET['pathid'])) {
                                    include('../Project/edit_project_path.php');
                                } else if ( $_GET['category'] != 'All' && empty($url_milestone) ) {
                                    include('tasks_dashboard.php');
                                } elseif ( $url_tab=='Reporting' ) {
                                    include('tab_reporting.php');
                                } elseif ( $url_milestone!='' ) {
                                    include('task_milestones.php');
                                } elseif ( $url_tab=='Summary' ) {
                                    include('tab_summary.php');
                                } elseif ( $url_tab=='Client' ) {
                                    include('tasks_dashboard.php');
                                } else {
                                    echo '<h4 class="gap-left">Select or create a Task Board.</h4>';
                                } ?>
                                <div class="clearfix"></div>
                            </div><?php
                        } ?>
                    </div><!-- .standard-dashboard-body-content -->

                </div><!-- .main-screen -->
            </div><!-- .has-main-screen -->

		</div><!-- .main-screen -->
	</div><!-- .row -->
<div class="clearfix"></div>
</div><!-- .container -->

<div class="clearfix"></div>

<?php include('../footer.php'); ?>
