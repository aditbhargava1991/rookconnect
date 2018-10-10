<?php
/*
Customer Listing
*/
include ('../include.php');
?>
<script type="text/javascript">
    $(document).ready(function() {
        $(window).resize(function() {
            var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.tile-sidebar').offset().top;
            if(available_height > 200) {
                $('tile-container, .tile-sidebar, .tile-sidebar ul.sidebar, .tile-content').height(available_height);
            }
        }).resize();
    });
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('incident_report');
$tile_summary = get_config($dbc, 'incident_report_summary_userid_'.$_SESSION['contactid']);
if(empty($tile_summary)) {
	$tile_summary = [];
	foreach(array_filter(explode(',',$_SESSION['role'])) as $session_role) {
		$tile_summary[] = get_config($dbc, 'incident_report_summary_seclevel_'.config_safe_str($session_role));
	}
	$tile_summary = implode(',', $tile_summary);
}
if(empty($tile_summary)) {
	$tile_summary = get_config($dbc, 'incident_report_summary');
}
$tile_summary = explode(',', $tile_summary);
$view_sql = (search_visible_function($dbc,'incident_report') > 0 ? '' : " AND (CONCAT(',',`contactid`,',',`clientid`,',') LIKE '%,{$_SESSION['contactid']},%' OR `completed_by` = '{$_SESSION['contactid']}')");

if(in_array('Staff Only',$tile_summary)) {
	$view_sql = " AND (CONCAT(',',`contactid`,',',`clientid`,',') LIKE '%,{$_SESSION['contactid']},%' OR `completed_by` = '{$_SESSION['contactid']}')";
}
?>

<div class="container">
    <div class='iframe_holder' style='display:none;'>
        <img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframe' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
        <span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
        <iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
    </div>
    <div class="row hide_on_iframe">
        <div class="main-screen">
            <div class="tile-header">
                <?php include('../Incident Report/tile_header.php'); ?>
            </div>

            <div class="tile-container" style="height: 100%;">
                <div class="collapsible tile-sidebar set-section-height hide-on-mobile">
                    <?php include('../Incident Report/tile_sidebar.php'); ?>
                </div>

                <div class="scale-to-fill tile-content set-section-height">
                    <div class="main-screen-white" style="height:100%; overflow-y: auto; background-color: inherit; border: none;">
						            <h3>Summary</h3>
                        <div class="row">
                            <?php if(in_array('Types',$tile_summary)) { ?>
                                <div class="col-sm-6">
                                    <div class="overview-block">
                                        <h4><?= INC_REP_TILE ?> by Type</h4>
                                        <?php foreach(str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $in_type) {
                                            $count = mysqli_fetch_array($dbc->query("SELECT COUNT(*) FROM `incident_report` WHERE `deleted`=0 AND `type`='$in_type'".$view_sql),MYSQLI_NUM)[0];
                                            echo '<a href="incident_report.php?type='.$in_type.'&search_from=&search_to=">'.$in_type.': '.$count.'</a><br />';
                                        } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if(in_array('Complete',$tile_summary)) { ?>
                                <div class="col-sm-6">
                                    <div class="overview-block">
                                        <h4>Completed <?= INC_REP_TILE ?></h4>
                                        <?php foreach(str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $in_type) {
                                            $count = mysqli_fetch_array($dbc->query("SELECT COUNT(*) FROM `incident_report` WHERE (status = 'Done') AND `deleted`=0 AND `type`='$in_type'".$view_sql),MYSQLI_NUM)[0];
                                            echo '<a href="admin.php?status=Done&type='.$in_type.'&search_from=&search_to=">'.$in_type.': '.$count.'</a><br />';
                                        } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
