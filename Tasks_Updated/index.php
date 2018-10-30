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
            var note_height = '';
            var note_height_project = '';
            if ( $('.standard-dashboard-body-title .notice').is(':visible') ) {
                note_height = -10;
                note_height_project = 15;
            } else {
                note_height = 25;
                note_height_project = 50;
            }
			if(available_height > 200) {
				$('.main-screen .main-screen, .has-main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
                //$('.main-screen .standard-dashboard-body-content').outerHeight(available_height - $('.standard-dashboard-body-title').height());
                $('#scrum_tickets').outerHeight($('.has-main-screen .main-screen').outerHeight() - $('.standard-dashboard-body-title:visible').outerHeight() - $('.standard-body-title:visible').outerHeight() - $('.dashboard_heading').outerHeight() - $('footer:visible').outerHeight() + note_height);
                $('.has-dashboard.dashboard-container.ui-sortable').outerHeight($('.has-main-screen .main-screen').outerHeight() - $('.standard-dashboard-body-title:visible').outerHeight() - $('.standard-body-title').outerHeight() - $('footer:visible').outerHeight() + note_height_project);
                $('.scrollable_unit').outerHeight($('#scrum_tickets').outerHeight() - $('.info-block-header').outerHeight() - 25);
                $('.has-dashboard.dashboard-container.ui-sortable').css({'padding-bottom':'0', 'padding-top':'8px'});
                $('.has-dashboard .dashboard-list').css({'margin-bottom':'-10px', 'overflow-y':'hidden'});
                $('.has-dashboard .dashboard-list ul.dashboard-list').css('overflow-y','scroll');
			}
            //var sidebar_height = $('.tile-sidebar').outerHeight(true);
            //$('.has-main-screen .main-screen').css('min-height', sidebar_height);
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
            window.location.replace('../Tasks_Updated/index.php?category=All&tab=Search&term='+term);
        }
    });

    $('.search_list_mobile').keypress(function(e) {
        if(e.which==13) {
            var term = $(this).val();
            $.ajax({
                url: '../Tasks_Updated/tasks_search.php?term='+term,
                method: 'GET',
                response: 'html',
                success: function(response) {
                    $('#search_results_mobile').html(response);
                    $('.panel').hide();
                }
            });
        }
    });

    $('.tile-sidebar .highest-level .top-a').click(function() {
        $(this).each(function() {
            if ( $(this).data('parent') == '#desktop_accordions' ) {
                $('.tile-sidebar .highest-level .top-ul').removeClass('in');
            }
        }).off('click').click(function() {
            $(this).addClass('collapsed');
            $('.tile-sidebar .highest-level .top-ul').removeClass('in');
        });
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
                    <?php
                    $taskboardid = 0;
                    if(!empty($_GET['category']) && $_GET['category'] != 'All') {
                        $taskboardid = $_GET['category'];
                    }
                    ?>
                    <div class="pull-right gap-left"><a href="field_config_project_manage.php?category=how_to"><img src="<?= WEBSITE_URL ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30" /></a></div>
                    <div class="pull-right gap-left"><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_taskboard.php?security=<?=$url_tab?>&taskboardid=<?=$taskboardid?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><button class="btn brand-btn hide-titles-mob">Add <?= TASK_NOUN ?> Board</button></a></div>
                    <!-- <div class="pull-right gap-left"><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?category=<?=$_GET['category']?>&tab=<?=$_GET['tab']?>', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><button class="btn brand-btn hide-titles-mob">Add Task</button></a></div> -->

                    <?php
                    $slider_layout = !empty(get_config($dbc, 'tasks_slider_layout')) ? get_config($dbc, 'tasks_slider_layout') : 'accordion';

                    if($slider_layout == 'accordion') {
                    ?>
                    <div class="pull-right gap-left"><a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php', '50%', false, false, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;"><button class="btn brand-btn hide-titles-mob">Add <?= TASK_NOUN ?></button></a></div>
                    <?php } else { ?>
                    <div class="pull-right gap-left"><a href="../Tasks_Updated/add_task_full_view.php"><button class="btn brand-btn hide-titles-mob">Add <?= TASK_NOUN ?></button></a></div>
                    <?php } ?>

                    <img class="no-toggle statusIcon pull-right no-margin inline-img" title="" src="" />
                </div>
                <?php
                $note = '';
                $heading = $_GET['tab'];
                if($heading == 'Private') {
                    $heading = 'Private Tasks';
                    $note = "These are Task Boards specific to the user logged in. No Private Task Boards are visible to any other software user unless shared. If a Private Task Board is shared, it's no longer Private and moves to the Shared Task Board tab for future use.";
                }
                if($heading == 'Company') {
                    $heading = 'Shared Tasks';
                    $note = "Shared Task Boards are meant for collaboration. All shared parties profile icons are visible in the header for the Board. Shared Boards are only visible to the individuals they're shared with.";
                }
                if($heading == 'path') {
                    $heading = 'Project Tasks';
                    $note = 'Project Task Boards are connected to the Project Paths assigned in the Projects tile. Every Project Path that has Tasks assigned within them display under the Project Task Board sub tab. Clicking on a Project here should display the Project Path, showing only the Tasks within each Milestone. If a Task is added here, it will now be a part of the Project Path for that Project, and if a Task is added in the Project Path from the Project tile, then it would also display here. Project Path Milestones and Tasks are always in sync and make the same Task information available. Only Projects with Tasks in a Project Path will be available here. Only the staff added to the Project can see the Project Board.';
                }
                if($heading == 'Client') {
                    $heading = 'Contact Tasks';
                    $note = "Every contact added to the Contact tile can have a Task Board attached to it. If a user adds a Task to a Contact, only that user can see those Tasks here. Contacts without Tasks will not be visible until a Task is added. Any Tasks added or worked on in the Task tile will be synched to the contact and visible in the contact's profile.";
                }
                if($heading == 'sales') {
                    $heading = 'Sales Tasks';
                    $note = 'Each Sales Lead in the Sales tile has a Task Path automatically assigned. Any Sales Task Board with Tasks on the path will display under this tab. Tasks assigned from the Sales Task Board sub tab within Tasks coincide with Tasks from the Sales tile. Adding from either location will update both Task Boards seamlessly. If a user adds a Task to a Sales Lead, only that user can see those Tasks here. Other staff can go to the Sales Lead Path to see all the Tasks.';
                }
                if($heading == 'Summary') {
                    $heading = 'Summary';
                }
                ?>
                <div class="scale-to-fill"><h1 class="gap-left"><a href="index.php?category=All&tab=Summary"><?= TASK_TILE ?>: </a><?php echo $heading;?></h1></div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

			<div class="clearfix"></div>

            <?php
            if($note != '') { ?>
            <div class="notice gap-bottom gap-top popover-examples">
                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11"><span class="notice-name">NOTE:</span>
                <?php echo $note; ?></div>
                <div class="clearfix"></div>
            </div>
            <?php } ?>

            <!-- Mobile View Start-->
            <div id="accordions" class="sidebar show-on-mob panel-group block-panels col-xs-12 form-horizontal">
                <div class="double-gap-bottom gap-right"><input class="form-control search_list_mobile" placeholder="Search" type="text" /></div>
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
                </div>
                <?php
                if (check_subtab_persmission($dbc, 'tasks', ROLE, 'my') === true) {
                    $result_mytasks = mysqli_query($dbc, "SELECT `task_board`.`taskboardid`, `board_name`, `board_security`, IFNULL(`seen_date`,'0000-00-00') `seen` FROM `task_board` LEFT JOIN `taskboard_seen` ON `task_board`.`taskboardid`=`taskboard_seen`.`taskboardid` AND `taskboard_seen`.`contactid`='{$_SESSION['contactid']}' WHERE `board_security`='Private' AND `company_staff_sharing` LIKE '%,". $contactid .",%' AND `deleted`=0");
                    if ( $result_mytasks->num_rows > 0 ) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading higher_level_heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordions" href="#collapse_private">
                                        Private <?= TASK_TILE ?><span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>

                            <div id="collapse_private" class="panel-collapse collapse">
                                <div class="panel-body" style="padding: 0; margin: -1px;" id="collapse_private_body">
                                    <?php while ( $row_mytasks=mysqli_fetch_assoc($result_mytasks) ) {
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
                                        Private <?= TASK_TILE ?><span class="glyphicon glyphicon-plus"></span>
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
                                    $task_name = 'Shared '.TASK_TILE;
                                }
                                if ( $task_name=='Client Tasks' ) {
                                    $task_name = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' '.TASK_TILE : CONTACTS_TILE.' '.TASK_TILE;
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
            <!-- Mobile View Finish-->

            <!--<div class="collapsible hide-titles-mob sidebar tile-sidebar sidebar-override inherit-height double-gap-top">-->
            <div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
                <ul id="desktop_accordions" class="panel-group"><?php
                    echo '<li class="standard-sidebar-searchbox"><input class="form-control search_list" placeholder="Search" type="text" /></li>';
                    echo '<li class="sidebar-higher-level highest-level"><a href="?category=All&tab=Summary" class="top-a cursor-hand '.($_GET['tab']=='Summary' ? 'active blue' : '').'">Summary</a></li>';

                    if (check_subtab_persmission($dbc, 'tasks', ROLE, 'my') === true) {
                        $result_mytasks = mysqli_query($dbc, "SELECT DISTINCT(`task_board`.`taskboardid`), `board_name`, `board_security` FROM `task_board` WHERE `board_security`='Private' AND `company_staff_sharing` LIKE '%,". $contactid .",%' AND `deleted`=0");
                        if ( $result_mytasks->num_rows > 0 ) {
                            echo '<li class="sidebar-higher-level highest-level"><a class="top-a '.(trim($_GET['tab']) == 'Private' ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#my_tasks" data-parent="#desktop_accordions" href="javascript:void(0);">Private '.TASK_TILE.'<span class="arrow"></span></a>';
                                echo '<ul id="my_tasks" class="top-ul collapse '.(trim($_GET['tab']) == 'Private' ? 'in' : '').'">';
                                    while ( $row_mytasks=mysqli_fetch_assoc($result_mytasks) ) {

                                        $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row_mytasks['seen']}',1,0)) as `unseen` FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.task_board='{$row_mytasks['taskboardid']}' AND tb.board_security='Private' AND (tl.created_by = ({$_SESSION['contactid']}) OR tl.contactid IN (". $_SESSION['contactid'] .")) AND tl.deleted=0 AND tb.deleted=0 AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00')"));

                                        $task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0;

                                        echo '<a href="?category='. $row_mytasks['taskboardid'] .'&tab='. $row_mytasks['board_security'] .'">
                                        <li class="'.($_GET['category']==$row_mytasks['taskboardid'] ? 'active' : '').'">'. $row_mytasks['board_name'] .'<span class="pull-right pad-right">'. $get_count['task_count'] .($_GET['category']!=$row_mytasks['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '').'</li></a>';
                                    }
                                echo '</ul>';
                            echo '</li>';
                        } else {
                            echo '<li class="sidebar-higher-level highest-level"><a class="cursor-hand '.($_GET['tab']==$tab ? 'active blue' : '').'" href="?category=All&tab=Private">Private '.TASK_TILE.'</a></li>';
                        }
                    } else {
                        echo '<li>Private '.TASK_TILE.'</li>';
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
                            case 'shared_tasks': // Shared Task
                                $info = "Click here to see shared tasks.";
                                $display = ( check_subtab_persmission($dbc, 'tasks_updated', ROLE, 'company') !== false ) ? 1 : 0;
                                $security = 'Company';
                                $tab = 'Company';
                                break;
                            case 'project_tasks': // Project Tasks
                                $info = "Click here to view all project tasks.";
                                $display = ( check_subtab_persmission($dbc, 'tasks_updated', ROLE, 'project') !== false ) ? 1 : 0;
                                $security = 'path';
                                $tab = 'path';
                                break;
                            case 'client_tasks': // Contact Tasks
                                $info = "Click here to view all contacts related tasks.";
                                $display = ( check_subtab_persmission($dbc, 'tasks_updated', ROLE, 'client') !== false ) ? 1 : 0;
                                $security = 'Client';
                                $tab = 'Client';
                                break;
                            case 'sales_tasks': // Sales Task
                                $info = "Click here to view all ".SALES_TILE." tasks.";
                                $display = ( check_subtab_persmission($dbc, 'tasks_updated', ROLE, 'sales') !== false ) ? 1 : 0;
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
                            if ( $security == 'sales' ) { // Sales Task
								echo '<li class="sidebar-higher-level highest-level"><a class="top-a '.(trim($_GET['tab']) == $tab ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#board_'.$tab.'" data-parent="#desktop_accordions" href="javascript:void(0);">'. SALES_TILE .' '.TASK_TILE.'<span class="arrow"></span></a>';
									echo '<ul id="board_'.$tab.'" class="top-ul collapse '.(trim($_GET['tab']) == $tab ? 'in' : '').'">';
										$result = sort_contacts_query($dbc->query("SELECT `sales`.`salesid`, `contacts`.`first_name`, `contacts`.`last_name`, `bus`.`name`, IFNULL(`taskboard_seen`.`seen_date`,'0000-00-00') `seen` FROM `sales` LEFT JOIN `contacts` ON `sales`.`contactid`=`contacts`.`contactid` LEFT JOIN `contacts` `bus` ON `sales`.`businessid`=`bus`.`contactid` LEFT JOIN `taskboard_seen` ON `taskboard_seen`.`taskboardid`=`sales`.`salesid` AND `taskboard_seen`.`tab`='sales' WHERE `sales`.`deleted`=0"));
										foreach($result as $row) {
											$get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row['seen']}',1,0)) as `unseen` FROM tasklist tl WHERE tl.salesid='{$row['salesid']}' AND tl.deleted=0 AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND task_milestone_timeline = ''"));
											$task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0;

                                            if($row['name'] != '' || $row['first_name'] != '') {
											    echo '<a href="?category='. $row['salesid'] .'&tab='.$tab.'"><li class="'.($_GET['category']==$row['salesid'] && $_GET['tab'] == $tab ? 'active' : '').'">'.$row['name'].($row['name'] != '' && $row['first_name'].$row['last_name'] != '' ? ': ' : '').$row['first_name'].' '.$row['last_name'].'<span class="pull-right pad-right">'. $get_count['task_count'] .($_GET['category']!=$row['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '').'</span></li></a>';
                                            }
										}
									echo '</ul>';
								echo '</li>';

                            } else if ( $security == 'Company' ) { // Shared Task
                                $result = mysqli_query($dbc, "SELECT `task_board`.`taskboardid`, `board_name`, `board_security`, `company_staff_sharing`, IFNULL(`seen_date`,'0000-00-00') `seen` FROM `task_board` LEFT JOIN `taskboard_seen` ON `task_board`.`taskboardid`=`taskboard_seen`.`taskboardid` AND `taskboard_seen`.`contactid`='{$_SESSION['contactid']}' AND IFNULL(`taskboard_seen`.`tab`,'$tab') = '$tab' WHERE `board_security`='". $security ."' AND `company_staff_sharing` LIKE '%,". $contactid .",%' AND `deleted`=0");

                                // if ( $result->num_rows > 0 ) {
                                    if ( $task_name=='Company Tasks' ) {
                                        $task_name = 'Shared '.TASK_TILE;
                                    }
                                    $shared_task_boards = '';
                                    $shared_task_staff = '';

                                    while ( $row=mysqli_fetch_assoc($result) ) {
                                        $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row['seen']}',1,0)) as `unseen` FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.task_board='{$row['taskboardid']}' AND tb.board_security='$tab' AND tl.deleted=0 AND tb.deleted=0 AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00') AND tl.task_path >0"));

                                        $task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0;

                                        $shared_task_boards .= '<a href="?category='. $row['taskboardid'] .'&tab='.$tab.'&subtab=board"><li class="'.($_GET['category']==$row['taskboardid'] ? 'active' : '').'">'. $row['board_name'] .'<span class="pull-right pad-right">'. $get_count['task_count'] .($_GET['category']!=$row['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '').'</span></li></a>';

                                        $company_staff_sharing = '';
                                        foreach ( array_filter(explode(',', $row['company_staff_sharing'])) as $staffid ) {
                                            $company_staff_sharing .= get_staff($dbc, $staffid) .', ';
                                        }
                                        $company_staff_sharing = rtrim($company_staff_sharing, ', ');

                                        $shared_task_staff .= '<a href="?category='. $row['taskboardid'] .'&tab='.$tab.'&subtab=staff"><li class="'.($_GET['category']==$row['taskboardid'] ? 'active' : '').'"><div class="pull-left" style="max-width:85%;">'. $company_staff_sharing .'</div><div class="pull-right pad-right">'. $get_count['task_count'] .($_GET['category']!=$row['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</div>)' : '').'</span></li></a><div class="clearfix"></div>';
                                    }

                                    echo '<li class="sidebar-higher-level highest-level"><a class="top-a '.(trim($_GET['tab']) == $tab ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#board_'.$tab.'" data-parent="#desktop_accordions" href="javascript:void(0);">'. $task_name .'<span class="arrow"></span></a>';

                                        echo '<ul id="board_'.$tab.'" class="top-ul collapse '.(trim($_GET['tab']) == $tab ? 'in' : '').'">';

                                            echo '<li class="sidebar-higher-level"><a class="'.(trim($_GET['tab'])==$tab && trim($_GET['subtab'])=='board' ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#shared_boards">'.TASK_NOUN .' Boards <span class="arrow"></span></a>';
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
                                //}

                            } else if($security == 'path') { // Project Tasks
                                echo '<li class="sidebar-higher-level highest-level"><a class="top-a '.(trim($_GET['tab']) == $tab ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#board1_'.$tab.'" data-parent="#desktop_accordions" href="javascript:void(0);">'.PROJECT_NOUN. ' '.TASK_TILE.'<span class="arrow"></span></a>';


                                echo '<ul id="board1_'.$tab.'" class="top-ul collapse '.(trim($_GET['tab']) == $tab ? 'in' : '').'">';

                                //$result = mysqli_query($dbc, "SELECT projectid, project_name, project_path FROM project WHERE project_name != '' AND project_path > 0 AND projectid IN(SELECT projectid FROM tasklist WHERE deleted = 0 AND projectid>0)");

                                $result = mysqli_query($dbc, "SELECT DISTINCT(t.projectid), p.project_name, p.project_path FROM project p, tasklist t WHERE p.project_name != '' AND p.project_path > 0 AND p.projectid = t.projectid AND t.deleted = 0 AND p.deleted = 0 AND t.projectid>0 AND p.status != 'Archive' AND t.heading != ''");

                                while ( $row=mysqli_fetch_assoc($result) ) {
                                    $projectid = $row['projectid'];

								    $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row['seen']}',1,0)) as `unseen` FROM tasklist tl WHERE tl.projectid='$projectid' AND tl.deleted=0 AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00')"));

                                    $task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0;

                                    echo '<li class="sidebar-higher-level"><a class="'.(trim($_GET['tab'])==$tab && trim($_GET['edit'])==$projectid ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#shared_boards_'.$projectid.'">'.$row['project_name'].' <span class="arrow"></span></a>';

                                    $project_path = $row['project_path'];

                                    echo '<ul id="shared_boards_'.$projectid.'" class="collapse '.(trim($_GET['tab'])==$tab && trim($_GET['edit'])==$projectid ? 'in' : '').'">';

                                    foreach(explode(',',$row['project_path']) as $projectpathid) {
                                        $main_path = get_field_value('project_path','project_path_milestone','project_path_milestone',$projectpathid);

                                        echo '<a href="?category='. $projectid .'&tab=path&pathid=I|'.$projectpathid.'&edit='.$projectid.'">';

                                        $ex_projectpathid = explode('|',$_GET['pathid']);
                                        echo '<li data-target="#board923_'.$project_path.'" class="sidebar-lower-level  '.(($ex_projectpathid[1]==$projectpathid) && (trim($_GET['edit'])==$projectid) ? 'active' : 'collapsed').'">'. $main_path;

                                        echo '<span class="pull-right pad-right">'. $task_count .($_GET['category']!=$row['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '').'</span></li></a>';

                                    }
                                    echo '</ul>';
                                    echo '</li>';
                                }
                                    echo '</ul>';
                                    echo '</li>';

                            } else if ( $security != '' ) { // Contact Tasks
                                $result = mysqli_query($dbc, "SELECT `task_board`.`taskboardid`, `board_name`, `board_security`, IFNULL(`seen_date`,'0000-00-00') `seen` FROM `task_board` LEFT JOIN `taskboard_seen` ON `task_board`.`taskboardid`=`taskboard_seen`.`taskboardid` AND `taskboard_seen`.`contactid`='{$_SESSION['contactid']}' AND IFNULL(`taskboard_seen`.`tab`,'$tab') = '$tab' WHERE `board_security`='". $security ."' AND `company_staff_sharing` LIKE '%,". $contactid .",%' AND `deleted`=0");
                                if ( $result->num_rows > 0 ) {

                                    if ( $task_name=='Company Tasks' ) {
                                        $task_name = 'Shared '.TASK_TILE;
                                    }
                                    if ( $task_name=='Client Tasks' ) {
                                        $task_name = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' '.TASK_TILE : CONTACTS_TILE.' '.TASK_TILE;
                                    }

                                    echo '<li class="sidebar-higher-level highest-level"><a class="top-a '.(trim($_GET['tab']) == $tab ? 'active blue' : 'collapsed').' cursor-hand" data-toggle="collapse" data-target="#board_'.$tab.'" data-parent="#desktop_accordions" href="javascript:void(0);">'. $task_name .'<span class="arrow"></span></a>';
                                        echo '<ul id="board_'.$tab.'" class="top-ul collapse '.(trim($_GET['tab']) == $tab ? 'in' : '').'">';
                                            while ( $row=mysqli_fetch_assoc($result) ) {
                                                $get_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(tl.tasklistid) as task_count, SUM(IF(IFNULL(`updated_date`,`created_date`) > '{$row['seen']}',1,0)) as `unseen` FROM tasklist tl JOIN task_board tb ON (tl.task_board=tb.taskboardid) WHERE tl.task_board='{$row['taskboardid']}' AND tb.board_security='$tab' AND tl.deleted=0 AND tb.deleted=0 AND (tl.archived_date IS NULL OR tl.archived_date='0000-00-00')"));
                                                $task_count = ($get_count['task_count'] > 0) ? $get_count['task_count'] : 0;

                                                echo '<a href="?category='. $row['taskboardid'] .'&tab='.$tab.'"><li class="'.($_GET['category']==$row['taskboardid'] ? 'active' : '').'">'. $row['board_name'] .'<span class="pull-right pad-right">'. $get_count['task_count'] .($_GET['category']!=$row['taskboardid'] && $get_count['unseen'] > 0 ? ' (<span class="text-red no-toggle" title="There are '.$get_count['unseen'].' tasks that have been added or changed since you last viewed this board.">'.$get_count['unseen'].'</span>)' : '').'</span></li></a>';
                                            }
                                        echo '</ul>';
                                    echo '</li>';
                                }  else {
                                    if ( $task_name=='Client Tasks' ) {
                                        $task_name = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' '.TASK_TILE : CONTACTS_TILE.' '.TASK_TILE;
                                    }
                                    echo '<li class="sidebar-higher-level highest-level"><a class="top-a '.($_GET['tab']==$tab ? 'cursor-hand active blue' : '').'" href="?category=All&tab='. $tab .'">'. $task_name .'</a></li>';
                                }
                            }
                        }

                    }

                    echo '<li class="sidebar-higher-level highest-level"><a class="cursor-hand '.($_GET['tab']==$tab ? 'active blue' : '').'" href="?category=All&tab=Reporting">Reporting</a></li>'; ?>
                </ul>
            </div><!-- .sidebar -->

            <div class="main-content-screen scale-to-fill has-main-screen hide-titles-mob">
                <div class="loading_overlay" style="display:none;"><div class="loading_wheel"></div></div>
                <div class="main-screen standard-dashboard-body override-main-screen form-horizontal no-overflow">

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
                            $title = 'Private '.TASK_TILE;
                            $notes_subtab = 'tasks_private';
                        } elseif ( $url_tab == 'Company' ) {
                            $title = 'Shared '.TASK_TILE;
                            $notes_subtab = 'tasks_company';
                        } elseif ( $url_tab == 'path' ) {
                            $title = (substr(PROJECT_TILE, -1)=='s' && substr(PROJECT_TILE, -2) !='ss') ? rtrim(PROJECT_TILE, 's').' '.TASK_TILE : PROJECT_TILE.' '.TASK_TILE;
                            $notes_subtab = 'tasks_project';
                        } elseif ( $url_tab == 'Client' ) {
                            $title = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' '.TASK_TILE : CONTACTS_TILE.' '.TASK_TILE;
                            $notes_subtab = 'tasks_client';
                        } elseif ( $url_tab == 'Reporting' ) {
                            $title = 'Reporting';
                            $notes_subtab = 'tasks_reporting';
                        } elseif ( $url_tab == 'Search' ) {
                            $title = 'Search';
                        } else {
                            $title = 'My '.TASK_TILE;
                        }

                        /*if ( $url_tab == 'path' ) {

                        } else {

                            echo '<div class="row">';
                                //echo '<div class="col-sm-6"><h3>'. ($title=='Search' ? $title .': '. $term : $title .': '. $board_name['board_name']) .'</h3></div>';

                                 echo '<div class="col-sm-6"><h3>'. ($title=='Search' ? $title .': '. $term : $board_name['board_name']) .'</h3></div>';

                                echo '<div class="col-sm-6 text-right">';
                                    if ( $url_tab!='Search' && $url_tab!='Summary' && $url_tab!='Reporting' ) {
                                        echo '<div class="gap-top gap-right" style="font-size:1.5em;">'; ?>
                                          <img class="no-toggle" title="Overall Task Board History" style="margin-top:3px; cursor:pointer; height:1.8em;" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/task_history.php?board=company&taskboardid=<?=$taskboardid?>','auto',true,true);" src="../img/icons/eyeball.png">
                                          <?php
                                            if ( $board_name['company_staff_sharing'] ) {
                                                $c_ex = explode(',', $board_name['company_staff_sharing']);
                                                $c_unique = array_unique($c_ex);
                                                foreach ( array_filter($c_unique) as $staffid ) {
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
                        */

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

                    <?php if(!empty($_GET['pathid'])) {
                        $projectid = $_GET['edit'];
                        $projecttype = $project['projecttype'];
                        $project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
                        $fromTasks = 1;
                        $tab_config = array('Tasks');
                        $security['edit'] = 1;
                        include('../Project/edit_project_path.php');
                    } else { ?>
                        <!--<div class="standard-dashboard-body-content"> --><?php
                            if ( $url_tab=='Search' ) {
                                include('tasks_search.php');
                            } else { ?>
                                <!-- <div class="dashboard-item"> -->
                                <?php
                                    if ( $_GET['category'] != 'All' && empty($url_milestone) ) {
                                        include('tasks_dashboard.php'); // Private Task,
                                    } elseif ( $url_tab=='Reporting' ) {
                                        include('tab_reporting.php');
                                    } elseif ( $url_milestone!='' ) {
                                        //include('task_milestones.php');
                                    } elseif ( $url_tab=='Summary' ) { // Summary tab
                                        include('tab_summary.php');
                                    } elseif ( $url_tab=='Client' ) {
                                        include('tasks_dashboard.php'); // Contact Tab
                                    } else {
                                        echo '<h4 class="gap-left">Select or create a '.TASK_NOUN .' Board.</h4>';
                                    } ?>
                                    <div class="clearfix"></div>
                                <!--</div> --><?php
                            } ?>
                        <!--</div> --><!-- .standard-dashboard-body-content -->
                    <?php } ?>

                </div><!-- .main-screen -->
            </div><!-- .has-main-screen -->

		</div><!-- .main-screen -->
	</div><!-- .row -->
<div class="clearfix"></div>
</div><!-- .container -->

<div class="clearfix"></div>

<?php include('../footer.php'); ?>
