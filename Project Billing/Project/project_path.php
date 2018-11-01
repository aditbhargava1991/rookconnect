<?php
/*
Dashboard
*/
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
?>
<script type="text/javascript" src="project_path.js"></script>
<script>
$(document).ready(function() {

$('.iframe_open').click(function(){
		var id = $(this).attr('id');
	   $('#iframe_instead_of_window').attr('src', 'project_history.php?projectid='+id);
	   $('.iframe_title').text('Project History');
	   $('.iframe_holder').show();
	   $('.hide_on_iframe').hide();
});

$('.close_iframer').click(function(){
	var result = confirm("Are you sure you want to close this window?");
	if (result) {
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	}
});

});
</script>
<style>
.active_tab {
    color: #000000;background: #ffffff;background: -webkit-linear-gradient(top, #FFF, #9A9A9A);
    background: linear-gradient(to bottom, #FFF, #9A9A9A);
    border: 2px solid #FFFFFF;cursor:default;
}
.ui-state-disabled  { pointer-events: none !important; }
</style>
</head>
<body>

<?php include_once('../navigation.php'); ?>

<div class="container">
	<div class="row hide_on_iframe">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <?php include_once('project_header_tabs.php'); ?>

         <?php
            if(isset($_POST['search_user_submit'])) {
                $search_client = $_POST['search_client'];
            } else {
                $search_user = $contactid;
            }
			if (isset($_POST['display_all_inventory'])) {
				$search_user = $contactid;
			}
        ?>
        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Search By Project Path:</label>
		  <div class="col-sm-8">
              <select data-placeholder="Pick a Project Path" name="search_client" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
              <option value=""></option>
              <?php
                $query = mysqli_query($dbc,"SELECT project_path_milestone, project_path FROM project_path_milestone");
                while($row = mysqli_fetch_array($query)) {
                ?><option <?php if ($row['project_path_milestone'] == $search_client) { echo " selected"; } ?> value='<?php echo  $row['project_path_milestone']; ?>' ><?php echo $row['project_path']; ?></option>
            <?php	} ?>
            </select>

          <label for="site_name" class=" control-label"></label>
            <button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
		  </div>
		</div>
		<div id='no-more-tables'>
        <div class="scrum_tickets" id="scrum_tickets">
        <?php
        if($search_client != '') {
            $each_tab = explode('#*#', get_project_path_milestone($dbc, $search_client, 'milestone'));
            $timeline = explode('#*#', get_project_path_milestone($dbc, $search_client, 'timeline'));

            $i=0;
            foreach ($each_tab as $cat_tab) {
                $query_check_credentials = "SELECT * FROM project WHERE project_path='$search_client' AND milestone_timeline='$cat_tab' AND projecttype = '$type' ORDER BY project_path";
                $result = mysqli_query($dbc, $query_check_credentials);
                $status = $cat_tab;
                $status = str_replace("&","FFMEND",$status);
                $status = str_replace(" ","FFMSPACE",$status);
                $status = str_replace("#","FFMHASH",$status);
                echo '<ul id="sortable'.$i.'" class="connectedSortable '.$status.'"><li class="ui-state-default ui-state-disabled">'.$cat_tab.'<br>'.$timeline[$i].'</li><br>';

                while($row = mysqli_fetch_array( $result )) {
                    echo '<li id="'.$row['projectid'].'" class="ui-state-default"><a href="#"  onclick="window.open(\''.WEBSITE_URL.'/Project/add_project.php?type='.$row['projecttype'].'&projectid='.$row['projectid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;">'.limit_text($row['project_name'], 5 ). '</a></li>';

                    $projectid = $row['projectid'];
                    $result_ticket = mysqli_query($dbc, "SELECT * FROM tickets WHERE projectid='$projectid' AND status != 'Archive'");

                    while($row_ticket = mysqli_fetch_array( $result_ticket )) {
                        echo '<li style="background: white;" id="'.$row_ticket['ticketid'].'" class="ui-state-default ui-state-ticket"><a href="#"  onclick="window.open(\''.WEBSITE_URL.'/Ticket/index.php?edit='.$row_ticket['ticketid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;">'.limit_text($row_ticket['heading'], 5 ). '</a></li>';
                    }

                    echo '<li class=""><input onChange="addQuickTicket(this)" name="add_task" id="add_new_task '.$projectid.'" type="text" class="form-control" /></li>';
                }
                echo '</ul>';
                $i++;
            }
        }
        ?>
        </div>

        </form>
		</div>
	</div>
</div>

<?php include_once('../footer.php'); ?>
