<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('report');
?>
<script type="text/javascript">
$(document).ready(function() {
    if($(window).width() > 768) {
        $(window).resize(function() {
            var view_height = $(window).height() - ($('#reports_div .scale-to-fill.has-main-screen').offset().top + $('#footer:visible').outerHeight()) - 1;
            $('.tile-sidebar,.main-screen.standard-body').height(view_height);
        }).resize();
    }
    $('#mobile_tabs .panel-heading').click(loadPanel);
});
function loadPanel() {
    var panel = $(this).closest('.panel').find('.panel-body');
    panel.html('Loading...');
    $.ajax({
        url: panel.data('file-name'),
        method: 'POST',
        response: 'html',
        success: function(response) {
            panel.html(response);
        }
    });
}
</script>
</head>
<body>

<?php include ('../navigation.php');
switch($_GET['tab']) {
    case 'logo':
        $title = 'Logo for Reports';
        $filename = 'field_config_logo.php';
        break;
    case 'header_footer':
        $title = 'Reports Header & Footer';
        $filename = 'field_config_header_footer.php';
        break;
    case 'dashboard':
        $title = 'Dashboard Settings';
        $filename = 'field_config_dashboard.php';
        break;
    case 'default':
        $title = 'Default Report';
        $filename = 'field_config_default.php';
        break;
    case 'operations':
        $title = 'Operations Reports';
        $filename = 'field_config_operations.php';
        break;
    case 'compensation':
        $title = 'Compensation Reports';
        $filename = 'field_config_compensation.php';
        break;
    case 'dashboard_navigation':
        $title = 'Dashboard Navigations';
        $filename = 'field_config_navigation.php';
        break;
    case 'tabs':
    default:
        $_GET['tab'] = 'tabs';
        $title = 'Reports Tabs';
        $filename = 'field_config_tabs.php';
        break;
}
?>

<div class="container" id="reports_div">
    <div class="row">
        <div class="main-screen">
            <div class="tile-header standard-header">
                <div class="pull-right settings-block">
                    <?php if(config_visible_function($dbc, 'report') == 1) {
                        echo '<a href="field_config.php" class="mobile-block pull-right "><img style="width: 30px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
                    } ?>
                </div>
                <div class="scale-to-fill">
                    <h1 class="gap-left"><a href="report_tiles.php">Reports</a></h1>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_tabs">
                                Tabs<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_tabs" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="field_config_tabs.php">
                            Loading...
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_logo">
                                Logo for Reports<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_logo" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="field_config_logo.php">
                            Loading...
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_header_footer">
                                Reports Header & Footer<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_header_footer" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="field_config_header_footer.php">
                            Loading...
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_dashboard">
                                Dashboard Setting<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_dashboard" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="field_config_dashboard.php">
                            Loading...
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_default">
                                Default Report<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_default" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="field_config_default.php">
                            Loading...
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_operations">
                                Operations Reports<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_operations" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="field_config_operations.php">
                            Loading...
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_compensation">
                                Compensation Reports<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_compensation" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="field_config_compensation.php">
                            Loading...
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_compensation">
                                Dashboard Navigation<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_compensation" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="field_config_compensation.php">
                            Loading...
                        </div>
                    </div>
                </div>
            </div>

            <div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
                <ul>
                    <a href="?tab=logo"><li <?= $_GET['tab'] == 'logo' ? 'class="active"' : '' ?>>Logo for Reports</li></a>
                    <a href="?tab=header_footer"><li <?= $_GET['tab'] == 'header_footer' ? 'class="active"' : '' ?>>Reports Header & Footer</li></a>
                    <a href="?tab=tabs"><li <?= (empty($_GET['tab']) || $_GET['tab'] == 'tabs' ? 'class="active"' : '') ?>>Reports Tabs</li></a>
                    <a href="?tab=dashboard"><li <?= $_GET['tab'] == 'dashboard' ? 'class="active"' : '' ?>>Dashboard Setting</li></a>
                    <a href="?tab=default"><li <?= $_GET['tab'] == 'default' ? 'class="active"' : '' ?>>Default Report</li></a>
                    <a href="?tab=operations"><li <?= $_GET['tab'] == 'operations' ? 'class="active"' : '' ?>>Operations Reports</li></a>
                    <a href="?tab=compensation"><li <?= $_GET['tab'] == 'compensation' ? 'class="active"' : '' ?>>Compensation Reports</li></a>
                    <a href="?tab=dashboard_navigation"><li <?= $_GET['tab'] == 'dashboard_navigation' ? 'class="active"' : '' ?>>Dashboard Navigation</li></a>
                </ul>
            </div>

            <div class="scale-to-fill has-main-screen hide-titles-mob">
                <div class="main-screen standard-body form-horizontal">
                    <div class="standard-body-title">
                        <h3><?= $title ?></h3>
                    </div>

                    <div class="standard-body" style="padding: 0.5em;">
                        <?php include('../Reports/'.$filename); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../footer.php'); ?>
