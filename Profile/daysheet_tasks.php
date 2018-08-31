<!-- Daysheet My Tasks -->
<?php
$contactid = $_SESSION['contactid'];
$taskboards = [];

if (check_subtab_persmission($dbc, 'tasks', ROLE, 'my') === true) {
    $result = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `task_board` WHERE `board_security` = 'Private' AND CONCAT(',',`company_staff_sharing`,',') LIKE '%,$contactid,%' AND `deleted` = 0"),MYSQLI_ASSOC);
    foreach($result as $row) {
        $taskboards['Private Tasks'][] = ['url'=>'../Tasks_Updated/index.php?category='.$row['taskboardid'].'&tab=Private', 'label'=>$row['board_name']];
    }
}

$get_field_task_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `task_dashboard_tile` FROM `task_dashboard`"));
$tasks_name = explode(',', $get_field_task_config['task_dashboard_tile']);
$tasks_name = array_filter($tasks_name);
foreach($tasks_name as $task_name) {
    $task_file_path = str_replace(" ", "_", strtolower($task_name));
    $info = '';
    $security = '';
    $tab = '';

    switch($task_file_path) {
        case 'company_tasks':
            $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'company') !== false ) ? 1 : 0;
            $security = 'Company';
            $tab = 'Company';
            break;
        case 'project_tasks':
            $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'project') !== false ) ? 1 : 0;
            $security = 'path';
            $tab = 'path';
            break;
        case 'client_tasks':
            $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'client') !== false ) ? 1 : 0;
            $security = 'Client';
            $tab = 'Client';
            break;
        case 'sales_tasks':
            $display = ( check_subtab_persmission($dbc, 'tasks', ROLE, 'sales') !== false ) ? 1 : 0;
            $security = 'sales';
            $tab = 'sales';
            break;
    }

    if($display == 1) {
        if($security == 'sales') {
            $result = sort_contacts_query(mysqli_query($dbc, "SELECT `sales`.`salesid`, `contacts`.`first_name`, `contacts`.`last_name`, `bus`.`name` FROM `sales` LEFT JOIN `contacts` ON `sales`.`contactid`=`contacts`.`contactid` LEFT JOIN `contacts` `bus` ON `sales`.`businessid`=`bus`.`contactid` WHERE `sales`.`deleted` = 0 AND (`primary_staff` = '$contactid' OR CONCAT(',',`share_lead`,',') LIKE '$contactid')"));
            foreach($result as $row) {
                $taskboards[SALES_NOUN.' Tasks'][] = ['url'=>'../Tasks_Updated/index.php?category='.$row['salesid'].'&tab='.$tab, 'label'=>$row['name'].($row['name'] != '' && $row['first_name'].$row['last_name'] != '' ? ': ' : '').$row['first_name'].' '.$row['last_name']];
            }
        } else if($security == 'Company') {
            $result = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `task_board` WHERE `deleted` = 0 AND `board_security` = '$security' AND CONCAT(',',`company_staff_sharing`,',') LIKE '%,$contactid,%'"),MYSQLI_ASSOC);
            foreach($result as $row) {
                $taskboards[($task_name == 'Company Tasks' ? 'Shared Tasks' : $task_name)][] = ['url'=>'../Tasks_Updated/index.php?category='.$row['taskboardid'].'&tab='.$tab.'&subtab=board', 'label'=>$row['board_name']];

                $company_staff_sharing = '';
                foreach ( array_filter(explode(',', $row['company_staff_sharing'])) as $staffid ) {
                    $company_staff_sharing .= get_staff($dbc, $staffid) .', ';
                }
                $company_staff_sharing = rtrim($company_staff_sharing, ', ');

                $taskboards[($task_name == 'Company Tasks' ? 'Shared Tasks' : $task_name)][] = ['url'=>'../Tasks_Updated/index.php?category='.$row['taskboardid'].'&tab='.$tab.'&subtab=staff', 'label'=>$company_staff_sharing];
            }
        } else if($security == 'path') {
            $result = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(t.projectid), p.project_name, p.project_path FROM project p, tasklist t WHERE p.project_name != '' AND p.project_path > 0 AND p.projectid = t.projectid AND t.deleted = 0 AND p.deleted = 0 AND t.projectid>0 AND p.status != 'Archive' AND t.heading != '' AND p.project_lead = '$contactid'"),MYSQLI_ASSOC);
            foreach($result as $row) {
                $projectid = $row['projectid'];
                $project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '$projectid'"));
                foreach(explode(',', $row['project_path']) as $projectpathid) {
                    $main_path = get_field_value('project_path','project_path_milestone','project_path_milestone',$projectpathid);
                    $taskboards[PROJECT_NOUN.' Tasks'][] = ['url'=>'../Tasks_Updated/index.php?category='.$projectid.'&tab=path&pathid=I|'.$projectpathid.'&edit='.$projectid, 'label'=>get_project_label($dbc, $project).': '.$main_path];
                }
            }
        } else if($security != '') {
            $result = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `task_board` WHERE `board_security` = '$security' AND CONCAT(',',`company_staff_sharing`,',') LIKE '%,$contactid,%' AND `deleted` = 0"));
            foreach($result as $row) {
                if ( $task_name=='Company Tasks' ) {
                    $task_name = 'Shared Tasks';
                }
                if ( $task_name=='Client Tasks' ) {
                    $task_name = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's').' Tasks' : CONTACTS_TILE.' Tasks';
                }
                $taskboards[$task_name][] = ['url'=>'../Tasks_Updated/index.php?category='.$row['taskboardid'].'&tab='.$tab, 'label'=>$row['board_name']];
            }
        }
    }
}


$tasks_query = "SELECT DISTINCT(tb.taskboardid), tb.board_security, tb.company_staff_sharing, tb.board_name  FROM task_board tb, tasklist tl WHERE tb.taskboardid = tl.task_board AND tl.contactid = '$contactid' AND tl.deleted = 0 AND tb.deleted = 0";
    $tasks_result = mysqli_query($dbc, $tasks_query);
    $num_rows = mysqli_num_rows($tasks_result);
?>
<div class="col-xs-12">
    <div class="weekly-div" style="overflow-y: hidden;">
        <?php if(!empty($taskboards)) {
            foreach($taskboards as $board_type => $taskboard_list) {
                echo '<h3>'.$board_type.'</h3>';
                echo '<ul class="option-list">';
                foreach($taskboard_list as $taskboard) {
                    echo '<a href="'.$taskboard['url'].'" class="col-sm-6"><li style="width:calc(100% - 3em);">';
                    profile_id($dbc, $_SESSION['contactid']);
                    echo '<div style="display:inline; width:calc(100% - 3em);">'.$taskboard['label'].'</div><div class="clearfix"></div></li></a>';
                }
                echo '</ul><div class="clearfix"></div>';
            }
        } else {
            echo "<h2>No Record Found.</h2>";
        } ?>
    </div>
</div>