<?php
/*
 * Email Communication Tile
 * Included Files: dashboard.php
 */
include_once('../include.php');
error_reporting(0);
?>
<script>
    $(document).ready(function() {
        $(window).resize(function() {
            $('.main-screen').css('padding-bottom',0);
            if($('.main-screen .main-screen').not('.show-on-mob .main-screen').is(':visible')) {
                var available_height = window.innerHeight - $(footer).outerHeight() - $('.sidebar:visible').offset().top;
                if(available_height > 200) {
                    $('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
                    $('.sidebar').outerHeight(available_height).css('overflow-y','auto');
                    $('.search-results').outerHeight(available_height).css('overflow-y','auto');
                }
            }
        }).resize();
    });
</script>
</head>
<body>
<?php
    include_once ('../navigation.php');
	checkAuthorised('email_communication');
    include_once('document_settings.php');
    switch (filter_var($_GET['type'], FILTER_SANITIZE_STRING)) {
        case 'external':
            $type = 'external';
            $tab_title = 'External Communication';
            break;
        case 'log':
            $type = 'log';
            $tab_title = 'Communication Log';
            break;
        case 'internal':
        default:
            $type = 'internal';
            $tab_title = 'Internal Communication';
            break;
    }
?>

<div class="container">
    <div class="iframe_overlay" style="display:none;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe src=""></iframe>
        </div>
    </div>
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header">
                <div class="pull-right settings-block"><?php
	                if(config_visible_function($dbc, 'email_communication') == 1) {
	                    echo '<div class="pull-right gap-left"><a href="field_config.php?settings=fields"><img src="'.WEBSITE_URL.'/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30" /></a></div>';
	                } ?>
                    <div class="pull-right">
                        <button class="btn brand-btn hide-titles-mob" onclick="overlayIFrameSlider('add_email.php?type=<?= $type ?>', 'auto', false, true, 'auto', true);">New Email</button>
                        <a class="cursor-hand show-on-mob" onclick="overlayIFrameSlider('add_email.php?type=<?= $type ?>', 'auto', false, true);"><img src="../img/icons/ROOK-add-icon.png" style="height:2em;" /></a>
                    </div>
                </div>
                <div class="scale-to-fill">
					<h1 class="gap-left"><a href="index.php">Email Communication</a></h1>
				</div>
                <div class="clearfix"></div>
			</div>

			<div class="clearfix"></div>
            
            <?php include('dashboard.php'); ?>
		</div>
	</div>
</div>

<?php include_once('../footer.php'); ?>