<?php
/*
 * Daysheet
 */
error_reporting(0);
include ('../include.php');
if(empty($_GET['tab'])) {
    $_GET['tab'] = 'daysheet';
}

//Insert config settings if none exist
mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) SELECT 'daysheet_fields_config', 'Reminders,Tickets,Tasks,Checklists' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name` = 'daysheet_fields_config') num WHERE num.rows = 0");
mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) SELECT 'daysheet_button_config', 'My Projects,My Tickets,My Checklists,My Tasks,My Time Sheets' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name` = 'daysheet_button_config') num WHERE num.rows = 0");
mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) SELECT 'daysheet_weekly_config', '1,2,3,4,5,6,7' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name` = 'daysheet_weekly_config') num WHERE num.rows = 0");

//Configs
$daysheet_fields_config = get_user_settings()['daysheet_fields_config'];
if(empty($daysheet_fields_config)) {
    $daysheet_fields_config = explode(',', get_config($dbc, 'daysheet_fields_config'));
} else {
    $daysheet_fields_config = explode(',', $daysheet_fields_config);
}
$daysheet_weekly_config = get_user_settings()['daysheet_weekly_config'];
if(empty($daysheet_weekly_config)) {
    $daysheet_weekly_config = explode(',', get_config($dbc, 'daysheet_weekly_config'));
} else {
    $daysheet_weekly_config = explode(',', $daysheet_weekly_config);
}
$daysheet_button_config = get_user_settings()['daysheet_button_config'];
if(empty($daysheet_button_config)) {
    $daysheet_button_config = explode(',', get_config($dbc, 'daysheet_button_config'));
} else {
    $daysheet_button_config = explode(',', $daysheet_button_config);
}

$quick_actions = explode(',',get_config($dbc, 'daysheet_quick_action_icons'));
$quick_action_html = '';
$quick_action_html .= '<div class="action-icons pull-right gap-right">';
$quick_action_html .= (in_array('reply',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" class="inline-img reply-icon no-toggle" title="Add Note">' : '');
$quick_action_html .= (in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" class="inline-img email-icon no-toggle" title="Send Email">' : '');
$quick_action_html .= (in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon no-toggle" title="Schedule Reminder">' : '');
$quick_action_html .= '</div>';
?>
</head>
<style>
hr {
    margin: 0;
}
</style>
<script type="text/javascript" src="../Profile/profile.js"></script>
<script type="text/javascript">
$(document).ready(function(){        
    if($(window).width() >= 768) {
        $(window).resize(function() {
            var screen_height = $(window).height() > 500 ? $(window).height() : 500;
            $('.main-screen .set-section-height').outerHeight(screen_height - $('#footer:visible').height() - $('.main-screen .set-section-height').offset().top - 1);
            $('ul.sidebar').outerHeight(screen_height - $('#footer:visible').height() - $('.main-screen .set-section-height').offset().top - 1);
            $('div.sidebar').not('.weekly').outerHeight(screen_height - $('#footer:visible').height() - $('.main-screen-details').offset().top - 1);
            if($('div.sidebar.weekly').length > 0) {
                $('div.sidebar.weekly').outerHeight(screen_height - $('#footer:visible').height() - $('div.sidebar.weekly').offset().top - 1);
            }
            if($('[name="daysheet_notepad"]').length > 0) {
                $('[name="daysheet_notepad"]').outerHeight(screen_height - $('#footer:visible').height() - $('div.sidebar.weekly').offset().top - 170 - 1);
            }
        }).resize();
    } else {
        $(window).resize(function() {
            $('div.sidebar').css('height', '100%');
            $('div.tile-content').height($(window).height() - $('#footer:visible').height() - $('div.tile-content').offset().top - 1);
        }).resize();
    }
    $('.reply-icon').off('click').click(function() {
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=planner&id=<?= $_SESSION['contactid'] ?>', 'auto', false, true);
    });
    $('.reminder-icon').off('click').click(function() {
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=planner&id=<?= $_SESSION['contactid'] ?>', 'auto', false, true);
    });
    $('.email-icon').off('click').click(function() {
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=planner&id=<?= $_SESSION['contactid'] ?>', 'auto', false, true);
    });
});
</script>
<body>
<?php include_once ('../navigation.php');
checkAuthorised();
?>
<div id="daysheet_div" class="container">
    <div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe name="daysheet_iframe" src=""></iframe>
        </div>
    </div>
    <div class="iframe_holder" style="display:none;">
        <img src="<?= WEBSITE_URL ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
        <span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
        <iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
    </div>
    <div class="row hide_on_iframe">
        <div class="main-screen">
            <!-- Tile Header -->
            <div class="tile-header">
                <div class="col-xs-12 col-sm-4">
                    <h1>
                        <span class="pull-left" style="margin-top: -5px;"><a href="daysheet.php" class="default-color">Planner</a></span>
                        <span class="clearfix"></span>
                    </h1>
                </div>
                <div class="col-xs-12 col-sm-8 text-right settings-block">
                    <div class="pull-right gap-left top-settings">
	                    <?php if ( config_visible_function ( $dbc, 'profile' ) == 1 ) { ?>
                            <a href="?settings=config" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
                            <span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to change settings for the Planner."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
	                    } ?>
						<?php if(get_config($dbc, 'planner_end_day') == 'show') { ?>
							<a href="?end_day=end" class="btn brand-btn pull-right">End Day</a>
						<?php } ?>
                        <?= '<div class="pull-right hide-titles-mob">'.$quick_action_html.'</div>' ?>
                        <?= '<div class="clearfix"></div><div class="show-on-mob pull-right">'.$quick_action_html.'</div>' ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

            <div class="tile-container" style="height: 100%;">
                <?php if($_GET['tab'] != 'timesheets') { ?>
                    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
                <?php } ?>

                	<?php if($_GET['settings'] == 'config') { ?>
	                    <!-- Sidebar -->
	                    <div class="collapsible tile-sidebar set-section-height">
	                        <?php include('../Daysheet/tile_sidebar.php'); ?>
	                    </div><!-- .tile-sidebar -->
	                <?php } else { ?>
                        <!-- Sidebar -->
                        <div class="collapsible tile-sidebar set-section-height hide-on-mobile">
                            <?php include('../Daysheet/tile_sidebar.php'); ?>
                        </div><!-- .tile-sidebar -->
                    <?php } ?>

                    <!-- Main Screen -->
                    <div class="scale-to-fill tile-content set-section-height" style="padding: 0; overflow-y: auto;"><?php
                        if ($_GET['end_day'] == 'end') {
                            include('../Profile/daysheet_overview.php');
                        } else if ($_GET['settings'] == 'config') {
                            include('../Profile/field_config_daysheet.php');
                        } else if ($_GET['tab'] == 'journals' || $_GET['tab'] == 'notes' || $_GET['tab'] == 'scrum_notes') {
                            include('../Daysheet/journal.php');
                        } else {
                            include('../Profile/daysheet_main.php');
                        } ?>
                    </div><!-- .tile-content -->
                    
                    <div class="clearfix"></div>
                </div><!-- .tile-container -->
            <?php if($_GET['tab'] != 'timesheets') { ?>
                </form>
            <?php } ?>

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>