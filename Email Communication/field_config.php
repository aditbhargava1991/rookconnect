<?php
    include ('../include.php');
    checkAuthorised('email_communication');
    error_reporting(0);
?>
<script>
    $(document).ready(function() {
        $('.panel-heading').click(loadPanel);
        $(window).resize(function() {
            var available_height = window.innerHeight - $(footer).outerHeight() - $('.main-screen .main-screen').offset().top;
            if(available_height > 200) {
                $('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
                $('.tile-sidebar').outerHeight(available_height).css('overflow-y','auto');
            }
        }).resize();
    });
    function loadPanel() {
        if(!$(this).hasClass('no_load')) {
            $('.panel-body').html('Loading...');
            body = $(this).closest('.panel').find('.panel-body');
            $.ajax({
                url: $(body).data('file'),
                method: 'POST',
                response: 'html',
                success: function(response) {
                    $(body).html(response);
                }
            });
        }
    }
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
    <div class="iframe_overlay" style="display:none;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe src=""></iframe>
        </div>
    </div>
    
    <div class="row">
        <div class="main-screen">
            <!-- Tile Header -->
            <div class="tile-header standard-header">
                <div class="pull-right settings-block"><?php
	                if(config_visible_function($dbc, 'email_communication') == 1) {
	                    echo '<div class="pull-right gap-left"><a href="field_config.php?settings=fields"><img src="'.WEBSITE_URL.'/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30" /></a></div>';
	                } ?>
                    <div class="pull-right">
                        <button class="btn brand-btn hide-titles-mob" onclick="overlayIFrameSlider('add_email.php?type=<?= $type ?>', 'auto', false, true);">New Email</button>
                        <a class="cursor-hand show-on-mob" onclick="overlayIFrameSlider('add_email.php?type=<?= $type ?>', 'auto', false, true);"><img src="../img/icons/ROOK-add-icon.png" style="height:2em;" /></a>
                    </div>
                </div>
                <div class="scale-to-fill">
					<h1 class="gap-left"><a href="index.php">Email Communication</a></h1>
				</div>
                <div class="clearfix"></div>
			</div><!-- .tile-header -->
            
            <div class="clearfix"></div>

            <div id="settings_accordions" class="sidebar show-on-mob panel-group block-panels col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_fields">
                                Fields<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_subtab_fields" class="panel-collapse collapse">
                        <div class="panel-body" data-file="field_config_fields.php">
                            Loading...
                        </div>
                    </div>
                </div><!-- .panel -->

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_dashboard">
                                Dashboard<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_subtab_dashboard" class="panel-collapse collapse">
                        <div class="panel-body" data-file="field_config_dashboard.php">
                            Loading...
                        </div>
                    </div>
                </div><!-- .panel -->

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#settings_accordions" href="#collapse_subtab_log">
                                Log<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_subtab_log" class="panel-collapse collapse">
                        <div class="panel-body" data-file="field_config_log.php">
                            Loading...
                        </div>
                    </div>
                </div><!-- .panel -->
            </div><!-- #settings_accordions -->

            <div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
                <ul>
                    <a href="index.php"><li>Back to Dashboard</li></a>
                    <a href="?settings=fields"><li class="<?= $_GET['settings'] == 'fields' ? 'active blue' : '' ?>">Fields</li></a>
                    <a href="?settings=dashboard"><li class="<?= $_GET['settings'] == 'dashboard' ? 'active blue' : '' ?>">Dashboard</li></a>
                    <a href="?settings=log"><li class="<?= $_GET['settings'] == 'log' ? 'active blue' : '' ?>">Log</li></a>
                </ul>
            </div><!-- .tile-sidebar -->

            <div class="has-main-screen scale-to-fill hide-titles-mob">
                <div class="main-screen standard-dashboard-body"><?php
                    switch($_GET['settings']) {
                        case 'dashboard':
                            include('field_config_dashboard.php');
                            break;
                        case 'log':
                            include('field_config_log.php');
                            break;
                        case 'fields':
                        default:
                            include('field_config_fields.php');
                            break;
                    } ?>
                </div><!-- .main-screen -->
            </div><!-- .has-main-screen -->
            
        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>