<?php
// Security Configuration

include ('../include.php');
error_reporting(0);
$tab = (isset($_GET['tab']) ? $_GET['tab'] : 'tiles');
?>
<script>
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('security');
?>
<div class="container">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-10">
				<h1 class="single-pad-bottom"><?php switch($tab) {
					case 'tiles': echo 'Software Functionality'; break;
					case 'levels': echo 'Security Levels & Groups'; break;
					case 'privileges': echo 'Set Security Privileges'; break;
					case 'privileges_by_staf': echo 'Set Security Privileges'; break;
					case 'assign': echo 'User Privileges'; break;
					case 'contact_cat': echo 'Contact Category Default Levels'; break;
					case 'reporting': echo 'Reporting'; break;
				} ?> Dashboard</h1>
			</div>
			<!--<div class="col-sm-2 double-gap-top"><?php
				if(config_visible_function($dbc, 'software_config') == 1) {
					/*href="config_settings.php?type=software_config"*/
					echo '<a class="mobile-block pull-right " onClick="alert(\'Coming soon!\');"><img style="width: 50px;" title="Tile Settings" src="' . WEBSITE_URL . '/img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right" style="margin:15px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
				} ?>
			</div>-->
			<div class="clearfix double-gap-bottom"></div>
		</div>

		<div class="tab-container mobile-100-container">
			<?php if ( check_subtab_persmission($dbc, 'security', ROLE, 'tiles') === TRUE ) { ?>
                <a href="security.php?tab=tiles"><button type="button" class="btn brand-btn mobile-block mobile-100<?php echo ($tab == 'tiles' ? ' active_tab' : ''); ?>">Software Functionality</button></a>
            <?php } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Software Functionality</button>
            <?php } ?>

            <?php if ( check_subtab_persmission($dbc, 'security', ROLE, 'levels') === TRUE ) { ?>
                <a href='security.php?tab=levels'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo ($tab == 'levels' ? ' active_tab' : ''); ?>' >Security Levels &amp; Groups</button></a>
            <?php } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Security Levels &amp; Groups</button>
            <?php } ?>

            <?php if ( check_subtab_persmission($dbc, 'security', ROLE, 'privileges') === TRUE ) { ?>
                <a href='security.php?tab=privileges'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo ($tab == 'privileges' ? ' active_tab' : ''); ?>' >Set Security Privileges</button></a>
            <?php } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Set Security Privileges</button>
            <?php } ?>

						<?php if ( check_subtab_persmission($dbc, 'security', ROLE, 'privileges') === TRUE ) { ?>
                <a href='security.php?tab=privileges_by_staff'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo ($tab == 'privileges_by_staff' ? ' active_tab' : ''); ?>' >Set Security Privileges by Staff</button></a>
            <?php } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Set Security Privileges By Staff</button>
            <?php } ?>

            <?php if ( check_subtab_persmission($dbc, 'security', ROLE, 'assign') === TRUE ) { ?>
                <a href='security.php?tab=assign&status=active'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo ($tab == 'assign' ? ' active_tab' : ''); ?>' >User Privileges</button></a>
            <?php } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">User Privileges</button>
            <?php } ?>

            <?php if ( check_subtab_persmission($dbc, 'security', ROLE, 'contact_cat') === TRUE ) { ?>
                <a href='security.php?tab=contact_cat'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo ($tab == 'contact_cat' ? ' active_tab' : ''); ?>' >Contact Category Default Levels</button></a>
            <?php } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Contact Category Default Levels</button>
            <?php } ?>

            <?php if ( check_subtab_persmission($dbc, 'security', ROLE, 'password') === TRUE ) { ?>
                <a href='security.php?tab=password&status=active'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo ($tab == 'password' ? ' active_tab' : ''); ?>' >Password Reset</button></a>
            <?php } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Password Reset</button>
            <?php } ?>

            <?php if ( check_subtab_persmission($dbc, 'security', ROLE, 'reporting') === TRUE ) { ?>
                <a href='security.php?tab=reporting'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo ($tab == 'reporting' ? ' active_tab' : ''); ?>' >Reporting</button></a>
            <?php } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Reporting</button>
            <?php } ?>
		</div>

		<?php switch($tab) {
			case 'tiles': include('tile_enable.php'); break;
			case 'levels': include('security_levels.php'); break;
			case 'privileges': include('security_privileges.php'); break;
			case 'privileges_by_staff': include('security_privileges_by_staff.php'); break;
			case 'assign': include('assign_privileges.php'); break;
			case 'contact_cat': include('contact_category_levels.php'); break;
			case 'password': include('password_reset.php'); break;
			case 'reporting': include('security_reporting.php'); break;
		}
		?>
	</div>
</div>

<?php include ('../footer.php'); ?>
