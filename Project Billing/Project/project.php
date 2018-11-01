<?php
/*
Dashboard
*/
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
checkAuthorised();
$nav_tabs = array_filter(explode(',',get_config($dbc, 'project_nav_tabs')));
$current_tab = (empty($_GET['tab']) ? $nav_tabs[0] : $_GET['tab']);
?>

</head>
<body>
<?php include_once('../navigation.php'); ?>

<div class="container">
	<div class='iframe_holder' style='display:none;'>

		<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
    </div>
	<div class="row hide_on_iframe">
		<?php if((empty($_GET['nav']) || $_GET['nav'] != 'no_tabs') && count($nav_tabs) > 1): ?>
			<div class='mobile-100-container tab-container'>
				<?php if (in_array('projects', $nav_tabs) && check_subtab_persmission($dbc, 'project', ROLE, 'nav_projects') === TRUE ) { ?>
					<span class="nav-subtab">
						<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see the <?php echo PROJECT_TILE; ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						<a href='project.php?tab=projects'><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'projects' ? 'active_tab' : ''); ?>"><?php echo PROJECT_TILE; ?></button></a>
					</span><?php
				} ?>
				<?php if (in_array('scrum', $nav_tabs) && check_subtab_persmission($dbc, 'project', ROLE, 'nav_scrum') === TRUE ) { ?>
					<span class="nav-subtab">
						<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see the Scrum board."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						<a href='project.php?tab=scrum'><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'scrum' ? 'active_tab' : ''); ?>">Scrum</button></a>
					</span><?php
				} ?>
				<?php if (in_array('tickets', $nav_tabs) && check_subtab_persmission($dbc, 'project', ROLE, 'nav_tickets') === TRUE ) { ?>
					<span class="nav-subtab">
						<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						<a href='project.php?tab=tickets'><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'tickets' ? 'active_tab' : ''); ?>"><?= TICKET_TILE ?></button></a>
					</span><?php
				} ?>
				<?php if (in_array('daysheet', $nav_tabs) && check_subtab_persmission($dbc, 'project', ROLE, 'nav_daysheet') === TRUE ) { ?>
					<span class="nav-subtab">
						<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see the Day Sheet."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						<a href='project.php?tab=daysheet'><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ($current_tab == 'daysheet' ? 'active_tab' : ''); ?>">Day Sheet</button></a>
					</span><?php
				} ?>
			</div>
		<?php endif; ?>

		<?php switch($current_tab) {
			case 'projects' : include('project_list.php'); break;
			case 'scrum' : include('../Scrum/scrum_display.php'); break;
			case 'tickets' : ?>
				<h1><?= TICKET_TILE ?>
				<?php
					$contactid = $_SESSION['contactid'];
					if(config_visible_function($dbc, 'ticket') == 1) { ?>
						<div class="pull-right">
							<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							<a href="../Ticket/field_config_tickets.php?from_url=<?php echo WEBSITE_URL.$_SERVER['REQUEST_URI']; ?>" class="mobile-block"><img style="width:50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>
						</div><?php
					} ?></h1><?php
				include('../Ticket/ticket_list.php');
				break;
			case 'daysheet' : include('../Daysheet/daysheet_display.php'); break;
		} ?>
	</div>
</div>

<?php include_once('../footer.php'); ?>
