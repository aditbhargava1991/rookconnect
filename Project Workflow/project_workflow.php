<?php
/*
Customer Listing
*/
include ('../include.php');

if (isset($_POST['add_workflow'])) {

    $tile_name = filter_var($_POST['tile_name'],FILTER_SANITIZE_STRING);
    $project_path = implode(',',$_POST['project_path']);

    if(empty($_POST['project_workflow_id'])) {
        $query_insert_vendor = "INSERT INTO `project_workflow` (`tile_name`, `project_path`) VALUES ('$tile_name', '$project_path')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $url = 'Added';
    } else {
        $project_workflow_id = $_POST['project_workflow_id'];
        $query_update_vendor = "UPDATE `project_workflow` SET `tile_name` = '$tile_name', `project_path` = '$project_path' WHERE `project_workflow_id` = '$project_workflow_id'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
    }

    echo '<script type="text/javascript"> window.location.replace("project_workflow.php?type=active"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
}
if(isset($_GET['action'])=='delete' && isset($_GET['tile']))
{
	$tile=$_GET['tile'];
	$projectid=$_GET['projectid'];
	$query_delete = "DELETE  t3, t4, t5 FROM
	  project_manage as t3
	  INNER JOIN  project_manage_detail as t4 on t3.projectmanageid=t4.projectmanageid
	  INNER JOIN  project_manage_budget as t5 on t3.projectmanageid=t5.projectmanageid
	  WHERE BINARY t3.tile='$tile'";

	$result_delete1 = mysqli_query($dbc,"DELETE FROM project_workflow WHERE project_workflow_id='$projectid'");
	$result_delete2 = mysqli_query($dbc,"DELETE FROM field_config_project_manage WHERE tile='$tile'");
	$result_delete = mysqli_query($dbc,$query_delete);

	echo '<script type="text/javascript"> alert("'.$tile.' Tile Delete Successfully"); window.location.replace("project_workflow.php?type=active"); </script>';
}
?>
<script type="text/javascript">
function tileConfig(sel) {
    var type = sel.type;
    var name = sel.name;
    var tile_value = sel.value;
    var final_value = '*';

    if($("#"+name+"_turn_on").is(":checked")) {
        final_value += 'turn_on*';
    }
    if($("#"+name+"_turn_off").is(":checked")) {
        final_value += 'turn_off*';
    }

    var isTurnOff = $("#"+name+"_turn_off").is(':checked');
    if(isTurnOff) {
       var turnoff = name;
    } else {
        var turnoff = '';
    }

    var isTurnOn = $("#"+name+"_turn_on").is(':checked');
    if(isTurnOn) {
       var turnOn = name;
    } else {
        var turnOn = '';
    }

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "../ajax_all.php?fill=tile_config&name="+name+"&value="+final_value+"&turnoff="+turnoff+"&turnOn="+turnOn,
        dataType: "html",   //expect html to be returned
        success: function(response){
            //location.reload();
        }
    });
}

$(document).ready(function() {

});
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('project_workflow');
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

        <h1 class="">Project Workflow Dashboard
        </h1>
		<?php
			$active_tab='';
			$addedit_tab='';
			if($_GET['type']=='active'){
				$active_tab='active_tab';
			}else if($_GET['type']=='addedit'){
				$addedit_tab='active_tab';
			}
		?>
		<?php if ( check_subtab_persmission($dbc, 'project_workflow', ROLE, 'active') === TRUE ) { ?>
            <a href="project_workflow.php?type=active"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo $active_tab; ?>">Active Workflow</button></a>&nbsp;&nbsp;
        <?php } else { ?>
            <button type="button" class="btn disabled-btn mobile-block mobile-100">Active Workflow</button>&nbsp;&nbsp;
        <?php } ?>
        
		<?php if ( check_subtab_persmission($dbc, 'project_workflow', ROLE, 'add_edit') === TRUE ) { ?>
            <a href="project_workflow.php?type=addedit"><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo $addedit_tab; ?>">Add/Edit Workflow</button></a>&nbsp;&nbsp;
        <?php } else { ?>
            <button type="button" class="btn disabled-btn mobile-block mobile-100">Add/Edit Workflow</button>&nbsp;&nbsp;
        <?php } ?>
		<br><br>
        
		<?php
			if($_GET['type']=='active'){
		?>
			<table class='table table-bordered table-striped'>
                <thead>
                    <tr class='hidden-xs hidden-sm'>
                        <th>SR No.</th>
                        <th>Project Workflow Name</th>
                        <th>Tabs</th>
                        <th>Function</th>
                    </tr>
                </thead>
				<?php
					$project_workflow_query=mysqli_query($dbc,"SELECT * FROM project_workflow where deleted = 0 ORDER BY project_workflow_id DESC");
					while($project_workflow_row = mysqli_fetch_array($project_workflow_query)) {
						echo '<tr>';
						echo '<td>'.$project_workflow_row['project_workflow_id'].'</td>';
						echo '<td>'.$project_workflow_row['tile_name'].'</td>';
						echo '<td>'.$project_workflow_row['project_path'].'</td>';
						echo '<td><a href="project_workflow.php?type=addedit&projectworkflowid='.$project_workflow_row['project_workflow_id'].'">Edit</a> |';
						echo '<a href=\'../delete_restore.php?action=delete&subtab=po&tile='.$project_workflow_row['tile_name'].'&project_workflow_id='.$project_workflow_row['project_workflow_id'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
						echo '</tr>';
					}
				?>
			</table>
		<?php } ?>
		<?php
			if($_GET['type']=='addedit'){
		?>
		<form name="form_sites" method="post" action="" class="form-horizontal" role="form">
            <?php $package = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='package'"))[0];
            $promotion = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='promotion'"))[0];
            $services = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='services'"))[0];
            $labour = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='labour'"))[0];
            $material = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='material'"))[0];
            $inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='inventory'"))[0];
            $assets = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='assets'"))[0];
            $equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='equipment'"))[0];
            $custom = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='custom'"))[0];
            $rate_card = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='rate_card'"))[0];
            $products = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='products'"))[0];
		    $vpl = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(SUM(`admin_enabled`) > 0, 'turn_on', 'turn_off') on_or_off FROM `tile_security` WHERE `tile_name`='vpl'"))[0]; ?>
        <table class='table table-bordered table-striped'>
            <thead>
            <tr class='hidden-sm '>
                <th>What details should be available in projects?</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="../img/info.png" width="20"></a>
                </span>
                Turn On Tile</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="../img/info.png" width="20"></a>
                </span>
                Turn Off Tile</th>
            </tr>
            </thead>

            <?php if (strpos($assets, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Assets</td>
                <?php echo project_workflow_function($dbc,'assets'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($equipment, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Equipment</td>
                <?php echo project_workflow_function($dbc,'equipment'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($inventory, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Inventory</td>
                <?php echo project_workflow_function($dbc,'inventory'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($labour, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Labour</td>
                <?php echo project_workflow_function($dbc,'labour'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($material, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Material</td>
                <?php echo project_workflow_function($dbc,'material'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($package, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Packages</td>
                <?php echo project_workflow_function($dbc,'package'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($products, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Products</td>
                <?php echo project_workflow_function($dbc,'products'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($promotion, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Promotions</td>
                <?php echo project_workflow_function($dbc,'promotion'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($rate_card, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Rate Cards</td>
                <?php echo project_workflow_function($dbc,'rate_card'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($services, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Services</td>
                <?php echo project_workflow_function($dbc,'services'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($vpl, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Vendor Price List</td>
                <?php echo project_workflow_function($dbc,'vpl'); ?>
            </tr>
            <?php } ?>
        </table>
		<?php
			$projectworkflowid='';
			$project_workflow_name='';
			$project_workflow_field_config='';

			if(isset($_GET['projectworkflowid'])){
				$projectworkflowid=$_GET['projectworkflowid'];
				$get_project_workflow = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM project_workflow WHERE deleted = 0 and project_workflow_id=$projectworkflowid"));

				$project_workflow_field_config = ','.$get_project_workflow['project_path'].',';

				$project_workflow_name=$get_project_workflow['tile_name'];
			}
		?>
        <h4>
        <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Project Workflow Name:</label>
            <div class="col-sm-8">
              <input name="tile_name" type="text" value="<?php echo $project_workflow_name; ?>" class="form-control">
			  <input type="hidden" name="project_workflow_id" value="<?php echo $projectworkflowid; ?>">
            </div>
        </div>
        </h4>
        <h3>
        Select Project Path order of Operations:
        </h3>
        <h4>
        <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Preseeding Projects:</label>
            <div class="col-sm-8">
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Bids".',') !== FALSE) { echo " checked"; } ?> value="Bids" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Bids
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."RFP".',') !== FALSE) { echo " checked"; } ?> value="RFP" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;RFP
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Estimate".',') !== FALSE) { echo " checked"; } ?> value="Estimate" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Estimate
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Quote".',') !== FALSE) { echo " checked"; } ?> value="Quote" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Quote
            </div>
        </div>

        <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Project Tracking:</label>
            <div class="col-sm-8">
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Project
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Job".',') !== FALSE) { echo " checked"; } ?> value="Job" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Job
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Foreman Sheet".',') !== FALSE) { echo " checked"; } ?> value="Foreman Sheet" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Foreman Sheet
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Tickets".',') !== FALSE) { echo " checked"; } ?> value="Tickets" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;<?= TICKET_TILE ?>
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Work Tickets".',') !== FALSE) { echo " checked"; } ?> value="Work Tickets" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Work Tickets
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Work Order".',') !== FALSE) { echo " checked"; } ?> value="Work Order" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Work Order
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."	 Tracking".',') !== FALSE) { echo " checked"; } ?> value="Time Tracking" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Time Tracking
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Punch Card".',') !== FALSE) { echo " checked"; } ?> value="Punch Card" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Punch Card
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Project Path/Milestones".',') !== FALSE) { echo " checked"; } ?> value="Project Path/Milestones" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Project Path/Milestones
				<input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Pending Work Order".',') !== FALSE) { echo " checked"; } ?> value="Pending Work Order" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Pending Work Order
				<input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Shop Work Order".',') !== FALSE) { echo " checked"; } ?> value="Shop Work Order" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Shop Work Order
				<input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Shop Time Clock".',') !== FALSE) { echo " checked"; } ?> value="Shop Time Clock" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Shop Time Clock
				<!--<input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Shop Time Sheets".',') !== FALSE) { echo " checked"; } ?> value="Shop Time Sheets" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Shop Time Sheets-->
            </div>
        </div>

        <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Accounting:</label>
            <div class="col-sm-8">
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."POS".',') !== FALSE) { echo " checked"; } ?> value="POS" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;POS
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Invoicing".',') !== FALSE) { echo " checked"; } ?> value="Invoicing" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Invoicing
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Time Tracking".',') !== FALSE) { echo " checked"; } ?> value="Time Tracking" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Time Tracking
				<input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Shop Time Sheets".',') !== FALSE) { echo " checked"; } ?> value="Shop Time Sheets" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Shop Time Sheets
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Payroll".',') !== FALSE) { echo " checked"; } ?> value="Payroll" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Payroll
				<input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Accounts Payable".',') !== FALSE) { echo " checked"; } ?> value="Accounts Payable" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Accounts Payable
				<input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Billables".',') !== FALSE) { echo " checked"; } ?> value="Billables" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Billables
				<input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Time Sheets".',') !== FALSE) { echo " checked"; } ?> value="Time Sheets" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Time Sheets
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Preparation".',') !== FALSE) { echo " checked"; } ?> value="Preparation" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Preparation
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Cold Call Pipeline".',') !== FALSE) { echo " checked"; } ?> value="Cold Call Pipeline" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Cold Call Pipeline
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Schedule".',') !== FALSE) { echo " checked"; } ?> value="Schedule" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Schedule
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Lead Bank".',') !== FALSE) { echo " checked"; } ?> value="Lead Bank" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Lead Bank
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Goals".',') !== FALSE) { echo " checked"; } ?> value="Goals" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Goals
            </div>
        </div>

        <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Reporting:</label>
            <div class="col-sm-8">
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Reports".',') !== FALSE) { echo " checked"; } ?> value="Reports" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Reports
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Gantt Chart".',') !== FALSE) { echo " checked"; } ?> value="Gantt Chart" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Gantt Chart
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Progress Tracking".',') !== FALSE) { echo " checked"; } ?> value="Progress Tracking" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Progress Tracking
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Reporting".',') !== FALSE) { echo " checked"; } ?> value="Reporting" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Reporting
            </div>
        </div>

        <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Project Add Ons:</label>
            <div class="col-sm-8">
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Purchase Orders".',') !== FALSE) { echo " checked"; } ?> value="Purchase Orders" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Purchase Orders
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Additions".',') !== FALSE) { echo " checked"; } ?> value="Additions" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Additions
                <input type="checkbox" <?php if (strpos($project_workflow_field_config, ','."Addendums".',') !== FALSE) { echo " checked"; } ?> value="Addendums" style="height: 20px; width: 20px;" name="project_path[]">&nbsp;&nbsp;Addendums
            </div>
        </div>
        </h4>

        <div class="form-group">
            <div class="col-sm-6">
				<!--<a href="../home.php" class="btn brand-btn btn-lg pull-right">Back</a>
                <a href="services.php" class="btn brand-btn btn-lg pull-right">Back</a>
				<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button type="submit" name="add_workflow" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
			</div>
        </div>

        </form>
		<?php } ?>
        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
