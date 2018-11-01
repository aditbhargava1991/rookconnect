<?php
/*
Customer Listing
*/
include ('../include.php');
$admin_securitys = explode(',',get_config($dbc, 'incident_report_admin_security'));
$admin_staffs = explode(',',get_config($dbc, 'incident_report_admin_staff'));
$admin_access = false;
if(in_array($_SESSION['contactid'],$admin_staffs)) {
    $admin_access = true;
}
foreach(array_filter(explode(',', $_SESSION['role'])) as $session_role) {
    if(in_array($session_role, $admin_securitys)) {
        $admin_access = true;
    }
}
if(!$admin_access) {
    header('Location: incident_report.php');
}
?>
<script>
$(document).ready(function() {
    $(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.tile-sidebar').offset().top;
        if(available_height > 200) {
            $('tile-container, .tile-sidebar, .tile-sidebar ul.sidebar, .tile-content').height(available_height);
            $('.tile-content .main-screen-white').height(available_height - 11);
        }
    }).resize();
});
function setStatus(select) {
	$.post('incident_report_ajax.php?action=admin_status', { id: $(select).data('id'), status: select.value });
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('incident_report');
// $view_sql = (search_visible_function($dbc,'incident_report') > 0 ? '' : " AND (CONCAT(',',`contactid`,',',`clientid`,',') LIKE '%,{$_SESSION['contactid']},%' OR `completed_by` = '{$_SESSION['contactid']}')");
$current_type = $_GET['type'];
if(!empty($current_type)) {
    $type_query = " AND `type` = '$current_type'";
}
$project_tabs = get_config($dbc, 'project_tabs');
if($project_tabs == '') {
    $project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
}
$project_tabs = explode(',',$project_tabs);
$project_vars = [];
foreach($project_tabs as $item) {
    $project_vars[preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)))] = $item;
}
$page_status = filter_var($_GET['status'],FILTER_SANITIZE_STRING);
?>
<div class="container">
    <div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe name="inc_rep_iframe" src=""></iframe>
        </div>
    </div>
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
                    <div class="main-screen-white" style="height:calc(100vh - 20em); overflow-y: auto;">

                        <form name="form_sites" method="post" action="" class="form-inline" role="form">
                            <div id="no-more-tables">
                                <div class="preview-block">
                                    <div class="preview-block-header"><h4>Administration - <?= empty($current_type) ? 'All '.INC_REP_TILE : $current_type ?></h4></div>
                                </div>
                            <?php
                            $manager_approvals = get_config($dbc, 'incident_report_manager_approvals');
                            if($manager_approvals == 1) {
                                $manager_query = " AND (`manager_status` = 'Done' OR IFNULL(`status`,'') NOT IN ('', 'Pending'))";
                            }

                            /* Pagination Counting */
                            $rowsPerPage = 25;
                            $pageNum = 1;

                            if(isset($_GET['page'])) {
                                $pageNum = $_GET['page'];
                            }

                            $offset = ($pageNum - 1) * $rowsPerPage;

                            if(!empty($_POST['search_incident_reports'])) {
                                $query_check_credentials = "SELECT * FROM incident_report WHERE (IFNULL(`status`,'') = '$page_status' OR ('$page_status' = 'Pending' AND IFNULL(`status`,'') = '')) AND `deleted`=0 $view_sql $type_query $manager_query";
                            } else {
                                $query_check_credentials = "SELECT * FROM incident_report WHERE (IFNULL(`status`,'') = '$page_status' OR ('$page_status' = 'Pending' AND IFNULL(`status`,'') = '')) AND `deleted`=0 $view_sql $type_query $manager_query LIMIT $offset, $rowsPerPage";
                                $query = "SELECT count(*) as numrows FROM incident_report WHERE (IFNULL(`status`,'') = '$page_status' OR ('$page_status' = 'Pending' AND IFNULL(`status`,'') = '')) AND `deleted`=0 $view_sql $type_query $manager_query";
                            }

                            $result = mysqli_query($dbc, $query_check_credentials);

                            $num_rows = mysqli_num_rows($result);

                            $status_field = 'status';
                            $approved_by_field = 'approved_by';

                            include('../Incident Report/approvals.php');

                            ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
