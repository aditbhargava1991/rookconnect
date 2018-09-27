<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('incident_report');
error_reporting(0); ?>
<script>
$(document).ready(function(){
    $("#selectall").change(function(){
      $("input[name='incident_report[]']").prop('checked', $(this).prop("checked"));
      $("input[name='incident_report_dashboard[]']").prop('checked', $(this).prop("checked"));
    });
});
</script>
</head>
<body>

<?php include ('../navigation.php');
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_incident_report WHERE row_type=''"));
$main_type = $_GET['category'];
foreach(str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $full_type) {
	if($main_type == preg_replace('/[^a-z]/','',strtolower($full_type))) {
		$main_type = $full_type;
	}
}

$sec_level_list = get_security_levels($dbc);
if(empty($_GET['tab'])) {
	$_GET['tab'] = 'general';
}
if($_GET['tab'] == 'general') {
	$field_title = 'General Settings';
} else if($_GET['tab'] == 'followup') {
	$field_title = 'Follow Up Settings';
} else if($_GET['tab'] == 'quick_action') {
    $field_title = 'Quick Action Icons';
} else if($_GET['tab'] == 'summary') {
    $field_title = 'Summary Access';
} else if($_GET['tab'] == 'admin') {
    $field_title = 'Administration';
} ?>

<div class="container">
    <div class="row">
        <div class="main-screen">
            <div class="tile-header">
                <?php include('../Incident Report/tile_header.php'); ?>
            </div>

            <div class="tile-container" style="height: 100%;">

                <div class="collapsible tile-sidebar set-section-height">
                    <ul class="sidebar">
                        <a href="incident_report.php"><li>Back to Dashboard</li></a>
                        <a href="field_config.php?tab=general"><li <?= $_GET['tab'] == 'general' ? 'class="active"' : '' ?>>General Settings</li></a>
                        <a href="field_config.php?tab=summary"><li <?= $_GET['tab'] == 'summary' ? 'class="active"' : '' ?>>Summary Access</li></a>
                        <a href="field_config.php?tab=admin"><li <?= $_GET['tab'] == 'admin' ? 'class="active"' : '' ?>>Administration</li></a>
                        <a href="field_config.php?tab=followup"><li <?= $_GET['tab'] == 'followup' ? 'class="active"' : '' ?>>Follow Up Settings</li></a>
                        <a href="field_config.php?tab=quick_action"><li <?= $_GET['tab'] == 'quick_action' ? 'class="active"' : '' ?>>Quick Action Icons</li>
						<?php foreach(str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $in_type) {
							$current_type = preg_replace('/[^a-z]/','',strtolower($in_type));
							if($_GET['type'] == $current_type) {
								$field_title = $in_type;
							} ?>
	                        <a href="field_config.php?tab=fields&type=<?= $current_type ?>"><li <?= $_GET['type'] == $current_type && $_GET['tab'] == 'fields' ? 'class="active"' : '' ?>><?= $in_type ?></li></a>
                        <?php } ?>
                    </ul>
                </div>

                <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
                    <div class="scale-to-fill">
                        <div class="main-screen-white double-gap-top">
                            <div class="preview-block-container">
                                <div class="preview-block">
                                    <div class="preview-block-header"><h4><?= $field_title ?></h4></div>
                                </div>
                                <?php if($_GET['tab'] == 'fields') {
                                	include('field_config_fields.php');
                                } else if($_GET['tab'] == 'followup') {
                                	include('field_config_followup.php');
                                } else if($_GET['tab'] == 'quick_action') {
                                    include('field_config_quick_action.php');
                                } else if($_GET['tab'] == 'summary') {
                                    include('field_config_summary.php');
                                } else if($_GET['tab'] == 'admin') {
                                    include('field_config_admin.php');
                                } else {
                                	include('field_config_general.php');
                                } ?>
                            </div>
                        </div>
                    </div>

                    <div class="pull-right gap-top gap-right gap-bottom">
                        <a href="incident_report.php" class="btn brand-btn">Cancel</a>
                        <button type="submit" id="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
                    </div>
                </form>

                <div class="clearfix"></div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>