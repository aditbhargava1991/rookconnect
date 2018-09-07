<?php include('../include.php');
checkAuthorised('services');

switch($_GET['tab']) {
	case 'general':
		$_GET['tab'] = 'general';
		$page_title = 'General Settings';
		$include_file = 'field_config_general.php';
		break;
	case 'dashboard':
		$_GET['tab'] = 'dashboard';
		$page_title = 'Dashboard Fields';
		$include_file = 'field_config_dashboard.php';
		break;
	case 'fields':
	default:
		$_GET['tab'] = 'fields';
		$page_title = 'Services Fields';
		$include_file = 'field_config_fields.php';
		break;
} ?>

<script type="text/javascript">
$(document).ready(function(){
    /* if($(window).width() > 767) {
        resizeScreen();
        $(window).resize(function() {
            resizeScreen();
        });
    } */
    $(window).resize(function() {
		var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.tile-sidebar:visible').offset().top;
        if(available_height > 300) {
            $('#services_div .tile-sidebar, #services_div .scale-to-fill').height(available_height);
            $('#services_div .main-screen-white').height(available_height - 10);
        }
	}).resize();
});

/* function resizeScreen() {
    var view_height = $(window).height() > 500 ? $(window).height() : 500;
    $('#services_div .tile-sidebar, #services_div .scale-to-fill').height($('#services_div').height() - $('#services_div .tile-header').height() + 15);
} */
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div id="services_div" class="container">
    <div class="row">
        <div class="main-screen"><?php
            include('tile_header.php'); ?>

            <div class="tile-container">
                <div class="standard-collapsible tile-sidebar tile-sidebar-noleftpad hide-on-mobile">
                    <ul>
                        <a href="index.php"><li>Back to Dashboard</li></a>
                        <a href="field_config.php?tab=general"><li <?= $_GET['tab'] == 'general' ? 'class="active"' : '' ?>>General Settings</li></a>
                        <a href="field_config.php?tab=fields"><li <?= $_GET['tab'] == 'fields' ? 'class="active"' : '' ?>>Services Fields</li></a>
                        <a href="field_config.php?tab=dashboard"><li <?= $_GET['tab'] == 'dashboard' ? 'class="active"' : '' ?>>Dashboard Fields</li></a>
                    </ul>
                </div>

                <div class="scale-to-fill" style="background-color: #fff">
                    <div class="main-screen-white standard-body" style="padding-left: 0; padding-right: 0; border: none;">
                        <div class="standard-body-title">
                            <h3><?= $page_title ?></h3>
                        </div>
                        <div class="standard-body-content pad-10">
                            <?php include($include_file); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include ('../footer.php'); ?>