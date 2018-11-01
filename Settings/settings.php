<?php
// Software Settings Configuration

include ('../include.php');
error_reporting(0);
$tab = (isset($_GET['tab']) ? $_GET['tab'] : 'style');
?>
<script>
$(document).ready(function() {
	$('#settings_div .panel-heading').click(loadPanel);
    if($(window).width() >= 768) {
		$(window).resize(function() {
			var view_height = $(window).height() - ($('.scale-to-fill.has-main-screen').offset().top + $('#footer:visible').outerHeight());
			$('.tile-sidebar,.main-screen.standard-body').height(view_height);
		}).resize();
	}
});
function loadPanel() {
	body = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: $(body).data('url'),
		response: 'html',
		success: function(response) {
			$(body).html(response);
		}
	});
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('software_config');
?>
<div class="container" id="settings_div">
	<div class="row">
	<div class="main-screen">

            <!-- Tile Header -->
            <div class="tile-header standard-header">
                <div class="scale-to-fill">
                    <h1 class="gap-left"><a href="settings.php" class="default-color">Settings</a></h1>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->
		
		<div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
			
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_style">
							Styling<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_style" class="panel-collapse collapse">
					<div class="panel-body" data-url="style_config.php">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_format">
							Formatting<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_format" class="panel-collapse collapse">
					<div class="panel-body" data-url="software_format.php">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_menu_format">
							Menu Format<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_menu_format" class="panel-collapse collapse">
					<div class="panel-body" data-url="menu_settings.php">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_tile_sort">
							Tile Sort Order<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_tile_sort" class="panel-collapse collapse">
					<div class="panel-body" data-url="tile_order.php">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_dashboard">
							My Dashboards<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_dashboard" class="panel-collapse collapse">
					<div class="panel-body" data-url="dashboards.php">
						Loading...
					</div>
				</div>
			</div>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'dashboard')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_mydashboard">
								Dashboards<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_mydashboard" class="panel-collapse collapse">
						<div class="panel-body" data-url="dashboards.php?tab=dashboard">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'identity')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_softwareid">
								Software Identity<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_softwareid" class="panel-collapse collapse">
						<div class="panel-body" data-url="identity.php">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'login')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_software_login">
								Software Login Page <span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_software_login" class="panel-collapse collapse">
						<div class="panel-body" data-url="login_style.php">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'social')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_socialmedia">
							Social Media Links <span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_socialmedia" class="panel-collapse collapse">
						<div class="panel-body" data-url="social_media.php">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'favicon')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_urlfavicon">
							URL Favicon <span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_urlfavicon" class="panel-collapse collapse">
						<div class="panel-body" data-url="../Admin Settings/favicon_settings.php">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'logo')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_logo">
								Logo
							 <span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_logo" class="panel-collapse collapse">
						<div class="panel-body" data-url="logo.php">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'contact_sort')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_displaypref">
							Display Preferences <span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_displaypref" class="panel-collapse collapse">
						<div class="panel-body" data-url="contacts_sort_order.php">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'font')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_font_settings">
								Font Settings <span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_font_settings" class="panel-collapse collapse">
						<div class="panel-body" data-url="font_settings.php">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'data_use')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_data_usage">
								Data Usage <span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_data_usage" class="panel-collapse collapse">
						<div class="panel-body" data-url="data_usage.php">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'notes')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_notes">
								Notes <span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_notes" class="panel-collapse collapse">
						<div class="panel-body" data-url="notes.php">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'ticket_slider')) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_slider">
								Slider <span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_slider" class="panel-collapse collapse">
						<div class="panel-body" data-url="ticket_slider.php">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_notifications">
							Notifications <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_notifications" class="panel-collapse collapse">
					<div class="panel-body" data-url="notifications.php">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_dash_notifications">
							Desktop Notifications <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_dash_notifications" class="panel-collapse collapse">
					<div class="panel-body" data-url="desktop_notifications.php">
						Loading...
					</div>
				</div>
			</div>

		</div>

		<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
			<ul>
			<li <?= ($tab == 'style' ? 'class="active" ' : '') ?>><a href='settings.php?tab=style'>Styling</a></li>
			<li <?= ($tab == 'format' ? 'class="active" ' : '') ?>><a href='settings.php?tab=format'>Formatting</a></li>
			<li <?= ($tab == 'menus' ? 'class="active" ' : '') ?>><a href='settings.php?tab=menus'>Menu Formatting</a></li>
			<li <?= ($tab == 'tile_order' ? 'class="active" ' : '') ?>><a href='settings.php?tab=tile_order'>Tile Sort Order</a></li>
			<li <?= ($tab == 'my_dashboard' ? 'class="active" ' : '') ?>><a href='settings.php?tab=my_dashboard'>My Dashboards</a></li>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'dashboard')) { ?><li <?= ($tab == 'dashboard' ? 'class="active" ' : '') ?>><a href='settings.php?tab=dashboard'>Dashboards</a></li><?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'identity')) { ?><li <?= ($tab == 'identity' ? 'class="active" ' : '') ?>><a href='settings.php?tab=identity'>Software Identity</a></li><?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'login')) { ?><li <?= ($tab == 'login' ? 'class="active" ' : '') ?>><a href='settings.php?tab=login'>Software Login Page</a></li><?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'social')) { ?><li <?= ($tab == 'social' ? 'class="active" ' : '') ?>><a href='settings.php?tab=social'>Social Media Links</a></li><?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'favicon')) { ?><li <?= ($tab == 'favicon' ? 'class="active" ' : ''); ?>><a href='settings.php?tab=favicon'>URL Favicon</a></li><?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'logo')) { ?><li <?= ($tab == 'logo' ? 'class="active" ' : ''); ?>><a href='settings.php?tab=logo'>Logo</a></li><?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'contact_sort')) { ?><li <?= ($tab == 'contacts_sort_order' ? 'class="active" ' : '') ?>><a href='settings.php?tab=contacts_sort_order'> Display Preferences</a></li><?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'font')) { ?><li <?= ($tab == 'font_settings' ? 'class="active" ' : ''); ?>><a href='settings.php?tab=font_settings'> Font Settings</a></li><?php } ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'data_use')) { ?><li <?= ($tab == 'data_use' ? 'class="active" ' : ''); ?><a href='settings.php?tab=data_use'>Data Usage</a></li><?php } ?>
			<?php //if(check_subtab_persmission($dbc, 'software_config', ROLE, 'notes')) { ?><!--<li <?= ($tab == 'notes' ? 'class="active" ' : ''); ?>><a href='settings.php?tab=notes'>Notes</a></li> --><?php //} ?>
			<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'ticket_slider')) { ?><li <?= ($tab == 'ticket_slider' ? 'class="active" ' : ''); ?>><a href='settings.php?tab=ticket_slider'><?= TICKET_NOUN ?> Slider</a></li><?php } ?>
			<li <?= ($tab == 'notifications' ? 'class="active" ' : '') ?>><a href='settings.php?tab=notifications'>Notifications</a></li>
			<?php if(ROLE == 'Manager'){ ?><li <?= ($tab == 'desktop_notifications' ? 'class="active" ' : '') ?>><a href='settings.php?tab=desktop_notifications'>Desktop Notifications</a></li><?php } ?>
			<?php $enb = get_config($dbc, 'desktop_notification_enabled'); ?>
			<?php if(ROLE != 'Manager'){ ?><li <?= ($tab == 'desktop_notifications_user' && $enb == '1' ? 'class="active" ' : '') ?>><a href='settings.php?tab=desktop_notifications_user'>Desktop Notifications</a></li><?php } ?>
		</div>
		
		<div class="scale-to-fill has-main-screen hide-titles-mob" style="margin-bottom:-20px;">
			<div class="main-screen standard-body form-horizontal">
				<div class="standard-body-title">
					<h3><?php switch($tab) {
						case 'style': echo 'Software Styling Dashboard'; break;
						case 'format': echo 'Software Formatting Dashboard'; break;
						case 'menus': echo 'Menu Formatting Dashboard'; break;
						case 'tile_order': echo 'Tile Sort Order Dashboard'; break;
						case 'my_dashboard': echo 'My Dashboards'; break;
						case 'dashboard': echo 'Create Dashboards'; break;
						case 'identity': echo 'Software Identity Dashboard'; break;
						case 'login': echo 'Software Login Page'; break;
						case 'social': echo 'Social Media Links Dashboard'; break;
						case 'favicon': echo 'Software Favicon Dashboard'; break;
						case 'logo': echo 'Software Logo Dashboard'; break;
						case 'contacts_sort_order': echo 'Display Preferences'; break;
						case 'font_settings': echo 'Font Settings'; break;
						case 'data_use': echo 'Data Usage Reporting'; break;
						case 'notes': echo 'Notes'; break;
						case 'ticket_slider': echo TICKET_NOUN.' Slider View'; break;
						case 'notifications': echo 'Notifications'; break;
						case 'desktop_notifications': echo 'Desktop Notifications'; break;
						case 'desktop_notifications_user': echo 'Desktop Notifications'; break;
					} ?></h3>
				</div>

				<div class="standard-body-content" style="padding:0.5em;">
					<?php switch($tab) {
						case 'style': include('style_config.php'); break;
						case 'format': include('software_format.php'); break;
						case 'menus': include('menu_settings.php'); break;
						case 'tile_order': include('tile_order.php'); break;
						case 'my_dashboard':
						case 'dashboard': include('dashboards.php'); break;
						case 'identity': include('identity.php'); break;
						case 'login': include('login_style.php'); break;
						case 'social': include('social_media.php'); break;
						case 'favicon': include('../Admin Settings/favicon_settings.php'); break;
						case 'logo': include('logo.php'); break;
						case 'contacts_sort_order': include('contacts_sort_order.php'); break;
						case 'font_settings': include('font_settings.php'); break;
						case 'data_use': include('data_usage.php'); break;
						case 'notes': include('notes.php'); break;
						case 'ticket_slider': include('ticket_slider.php'); break;
						case 'notifications': include('notifications.php'); break;
						case 'desktop_notifications': include('desktop_notifications.php'); break;
						case 'desktop_notifications_user': include('desktop_notifications_user.php'); break;
					} ?>
				</div>
			</div>
		</div>
	</div>
</div></div>

<?php include ('../footer.php'); ?>
