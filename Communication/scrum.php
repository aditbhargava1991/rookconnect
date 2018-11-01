<?php
/*
Inventory Listing
*/
include ('../include.php');

?>
<style>

</style>
<script type="text/javascript" src="tasks.js"></script>
<link rel="stylesheet" href="tasks.css" type="text/css">
<script type="text/javascript">
$(document).ready(function () {
});
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('communication');
?>
<div class="container">
	<div class="row hide_on_iframe">

    <h1 class="single-pad-bottom"><?= TASK_TILE ?>
    <?php
    if(config_visible_function($dbc, 'communication') == 1) {
        echo '<a href="field_config_tasks.php?type=tab" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
    }

    $task_tab = explode(",",get_config($dbc, 'task_tab'));

    echo "<a href='tasks.php?category=".$task_tab[0]."'><button type='button' class='btn brand-btn mobile-block' >".TASK_TILE."</button></a>&nbsp;&nbsp;";
    echo "<a href='tickets.php'><button type='button' class='btn brand-btn mobile-block' >".TICKET_TILE."</button></a>&nbsp;&nbsp;";
    echo "<a href='scrum_tasks.php?category=".$task_tab[0]."'><button type='button' class='btn brand-btn mobile-block active_tab' >Scrum ".TASK_TILE."</button></a>&nbsp;&nbsp;";
    echo "<a href='scrum_tickets.php'><button type='button' class='btn brand-btn mobile-block' >Scrum ".TICKET_TILE."</button></a>&nbsp;&nbsp;";

    ?>
    </h1>

    <form name="form_sites" method="post" action="" class="form-inline" role="form">
        <?php

        $category = $_GET['category'];
        $tabs = get_config($dbc, 'task_tab');
        $each_tab = explode(',', $tabs);

        $active_all = '';
        if(empty($_GET['category']) || $_GET['category'] == 'Top') {
            $active_all = 'active_tab';
        }

        foreach ($each_tab as $cat_tab) {
            $board_assign = get_tasklist($dbc, 'Task', $cat_tab, 'board_assign');
            if (strpos($board_assign, ','.$_SESSION['contactid'].',') !== FALSE) {
                $active_daily = '';
                if((!empty($_GET['category'])) && ($_GET['category'] == $cat_tab)) {
                    $active_daily = 'active_tab';
                }
                echo "<a href='scrum.php?category=".$cat_tab."'><button type='button' class='btn brand-btn mobile-block ".$active_daily."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
            }
        }
        ?>
        <br><br>
        <div class="tasklist">
        <?php

        $tabs = get_config($dbc, 'ticket_status');
        $each_tab = explode(',', $tabs);
        $i=1;
        foreach ($each_tab as $cat_tab) {
            //$query_check_credentials = "SELECT * FROM tasklist WHERE contactid='$contactid' AND status!='Archived' ORDER BY tasklistid DESC";
            $query_check_credentials = "SELECT * FROM tasklist WHERE status='$cat_tab' AND category='$category' ORDER BY tasklistid DESC";
            $result = mysqli_query($dbc, $query_check_credentials);
            $status = $cat_tab;
            $status = str_replace("&","FFMEND",$status);
            $status = str_replace(" ","FFMSPACE",$status);
            $status = str_replace("#","FFMSPACE",$status);

              echo '<ul id="sortable'.$i.'" class="connectedSortable '.$status.'"><li class="ui-state-default ui-state-disabled">'.$cat_tab.'</li><br>';
              while($row = mysqli_fetch_array( $result )) {
                $contactid = $row['contactid'];
                $staff = get_staff($dbc, $contactid);
                //echo '<li id="'.$row['tasklistid'].'" class="ui-state-default"><a href="#"  onclick="wwindow.open(\''.WEBSITE_URL.'/Ticket/add_view_ticket_tasklist.php?tile=tasktile&tasklistid='.$row['tasklistid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;"><b>'.$staff.'</b><br>'.$row['heading']. '</a></li>';
				echo '<li id="'.$row['tasklistid'].'" class="ui-state-default '.$class_on.'"><a href="'.WEBSITE_URL.'/Ticket/add_view_ticket_tasklist.php?tile=tasktile&tasklistid='.$row['tasklistid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="'.$row['tasklistid'].'" title="'.$row['heading'].'"><b>'.$business.'</b><br>'.$staff.'<br><span  title="'.$row['heading']. '"><b>'.$staff.'</b><br>'.$row['heading']. '</a></li>';
              }
              echo '</ul>';
              //echo '<ul>';
              //echo '<li id="'.$row['tasklistid'].'" class="new_task_box"><input onChange="changeEndAme(this)" name="add_task" id="add_new_task '.$status.' '.$category.'" type="text" class="form-control add_new_task_from_here" /></li>';
               // echo '</ul>';
              $i++;
        }

        ?>
        </div>
		</form>
	</div>
</div>

<?php include ('../footer.php'); ?>