<?php
/*
Dashboard
*/
include_once('../include.php');
error_reporting(0);

if (isset($_POST['submit_general'])) {
    //Project Navigation Tabs
    $project_nav_tabs = implode(',',$_POST['project_nav_tabs']);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='project_nav_tabs'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$project_nav_tabs' WHERE name='project_nav_tabs'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('project_nav_tabs', '$project_nav_tabs')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Project Navigation Tabs

    //Tile Name
    $project_tile_name = $_POST['project_tile_name'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='project_tile_name'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$project_tile_name' WHERE name='project_tile_name'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('project_tile_name', '$project_tile_name')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Tile Name

    //Next Step after Project
    $next_step_after_project = implode('/',$_POST['next_step_after_project']);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='next_step_after_project'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$next_step_after_project' WHERE name='next_step_after_project'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('next_step_after_project', '$next_step_after_project')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Next Step after Project

    //project_service_price_or_hours
    $project_service_price_or_hours = $_POST['project_service_price_or_hours'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='project_service_price_or_hours'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$project_service_price_or_hours' WHERE name='project_service_price_or_hours'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('project_service_price_or_hours', '$project_service_price_or_hours')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $project_service_qty_cost = $_POST['project_service_qty_cost'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='project_service_qty_cost'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$project_service_qty_cost' WHERE name='project_service_qty_cost'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('project_service_qty_cost', '$project_service_qty_cost')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //project_service_qty_cost

	//Project/Estimate Types
	$old_types = preg_replace('/[^a-z_,]/','',str_replace(' ','_',strtolower(get_config($dbc, 'project_tabs'))));
	$project_tabs = filter_var($_POST['project_tabs'],FILTER_SANITIZE_STRING);
	$new_types = preg_replace('/[^a-z_,]/','',str_replace(' ','_',strtolower($project_tabs)));
	$project_tab_list = explode(',',$new_types);
	//Hide new subtabs for roles other than the current by default
	$value = '*turn_off**#*'.date('Y-m-d');
	foreach($project_tab_list as $tab_name) {
		if(strpos(','.$old_types.',',','.$tab_name.',') === FALSE && $tab_name != '') {
			foreach(get_security_levels($dbc) as $level) {
				$sql = "INSERT INTO `subtab_config` (`tile`, `security_level`, `subtab`, `status`) SELECT 'project', '$level', '$tab_name', '$value' FROM (SELECT COUNT(*) num FROM `subtab_config` WHERE `tile`='project' AND `security_level`='$level' AND `subtab`='$tab_name') rows WHERE rows.num=0";
				$result = mysqli_query($dbc, $sql);
			}
		}
	}
	//Remove subtab security for deleted Project Types
	$sql = "DELETE FROM `subtab_config` WHERE `tile`='project' AND `subtab` NOT IN ('".str_replace(',',"','",$new_types)."')";
	$result = mysqli_query($dbc, $sql);
	$project_tabs_result = mysqli_query($dbc,"INSERT INTO `general_configuration` (`name`, `value`) SELECT 'project_tabs', '' FROM (SELECT COUNT(*) `rows` FROM `general_configuration` WHERE `name`='project_tabs') CONFIG WHERE `rows`=0");
	$query_project_tabs = "UPDATE `general_configuration` SET `value`='$project_tabs' WHERE `name`='project_tabs'";
	$result_project_tabs = mysqli_query($dbc,$query_project_tabs);
	//Project/Estimate Types

    //Project Type Tiles
    $project_type_tiles = $_POST['project_type_tiles'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='project_type_tiles'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$project_type_tiles' WHERE name='project_type_tiles'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('project_type_tiles', '$project_type_tiles')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Project Type Tiles

    echo '<script type="text/javascript"> window.location.replace("field_config_project.php?type=Pending"); </script>';
}

if (isset($_POST['submit_all'])) {
    //Fields
    $type = $_POST['project_type'];
    $config_fields = implode(',',$_POST['config_fields']);

    if (strpos(','.$config_fields.',',','.'Package Heading,Promotion Heading,Custom Heading,Material Code,Services Heading,Products Heading,SRED Heading,Staff Contact Person,Contractor Contact Person,Clients Client Name,Clients Contact Person,Vendor Pricelist Vendor,Vendor Pricelist Price List,Vendor Pricelist Category,Vendor Pricelist Product,Customer Customer Name,Customer Contact Person,Inventory Product Name,Equipment Unit/Serial Number,Labour Heading,Expenses Type,Expenses Category,Other Detail'.',') === false) {
        $config_fields = 'Package Heading,Promotion Heading,Custom Heading,Material Code,Services Heading,Products Heading,SRED Heading,Staff Contact Person,Contractor Contact Person,Clients Client Name,Clients Contact Person,Vendor Pricelist Vendor,Vendor Pricelist Price List,Vendor Pricelist Category,Vendor Pricelist Product,Customer Customer Name,Customer Contact Person,Inventory Product Name,Equipment Unit/Serial Number,Labour Heading,Expenses Type,Expenses Category,Other Detail,'.$config_fields;
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigprojectid) AS fieldconfigprojectid FROM field_config_project WHERE type='$type'"));
    if($get_field_config['fieldconfigprojectid'] > 0) {
        $query_update_employee = "UPDATE `field_config_project` SET config_fields = '$config_fields' WHERE `type` = '$type'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config_project` (`type`, `config_fields`) VALUES ('$type', '$config_fields')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Fields

    if($type == 'SRED') {
        $type = 'SRED';
    }
    if($type == 'RD') {
        $type = 'RD';
    }
    echo '<script type="text/javascript"> window.location.replace("field_config_project.php?type='.$type.'"); </script>';
}
?>
<script>
$(document).ready(function(){
    $("#selectall").change(function(){
      $("input[name='config_fields[]']").prop('checked', $(this).prop("checked"));
    });
});
</script>
</head>
<body>

<?php include_once('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1><?php echo PROJECT_TILE; ?></h1>
<div class="pad-left gap-top"><a href="project.php?tab=projects&type=<?php echo $_GET['type']; ?>" class="btn brand-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>-->
<br>

<?php $project_tabs = get_config($dbc, 'project_tabs');
mysqli_query($dbc, "UPDATE `field_config_project` SET `type`=LOWER(REPLACE(REPLACE(`type`,' ','_'),'&',''))");

$project_tabs = explode(',',$project_tabs);
$project_vars = [];
$type = (empty($_GET['type']) ? 'Pending' : $_GET['type']);
$active_general = '';
if($type == 'Pending') {
    $active_general = 'active_tab';
}

foreach($project_tabs as $item) {
	$var_name = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
	$project_vars[] = $var_name;
	${'active_'.$var_name} = '';
	if($type == $var_name) {
		${'active_'.$var_name} = 'active_tab';
		$type = $var_name;
	}
} ?>

<div class="pad-left gap-top mobile-100-container">
	<span class="nav-subtab no-popover">
		<a href='field_config_project.php?type=Pending'><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo $active_general; ?>">General</button></a>&nbsp;&nbsp;
	</span>
	<span class="nav-subtab">
		<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create a new <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Path and Milestone."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href='field_config_project_path_milestone.php'><button type="button" class="btn brand-btn mobile-block mobile-100"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Template</button></a>&nbsp;&nbsp;
	</span>
	<?php foreach($project_tabs as $key => $project_tab) { ?>
		<span class="nav-subtab no-popover">
			<a href='field_config_project.php?type=<?php echo $project_vars[$key]; ?>'><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ${'active_'.$project_vars[$key]}; ?>"><?php echo $project_tab; ?></button></a>&nbsp;&nbsp;
		</span>
	<?php }	?>
</div>

<br>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<?php
echo '<input type="hidden" name="project_type" value="'.$type.'" />';

$config_sql = "SELECT `config_fields` FROM field_config_project WHERE type='$type' UNION
	SELECT `config_fields`  FROM `field_config_project` WHERE `fieldconfigprojectid` IN (SELECT MAX(`fieldconfigprojectid`) FROM `field_config_project` WHERE `type` IN ('".preg_replace('/[^a-z_,\']/','',str_replace(' ','_',str_replace(',',"','",strtolower(get_config($dbc,'project_tabs')))))."'))";
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,$config_sql));
$value_config = ','.$get_field_config['config_fields'].',';
?>
<div class="panel-group" id="accordion2">

    <?php if($type == 'Pending') { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_nav_tabs" >
                    <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Tabs<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_nav_tabs" class="panel-collapse collapse">
            <div class="panel-body">
				<?php $nav_tabs = get_config($dbc, 'project_nav_tabs'); ?>
				<label><input type="checkbox" name="project_nav_tabs[]" <?php echo (strpos(','.$nav_tabs.',', ',projects,') !== false ? 'checked' : ''); ?> value="projects" style="height: 1.5em; margin: 0.5em; width: 1.5em;"> <?php echo PROJECT_TILE; ?></label>
				<label><input type="checkbox" name="project_nav_tabs[]" <?php echo (strpos(','.$nav_tabs.',', ',scrum,') !== false ? 'checked' : ''); ?> value="scrum" style="height: 1.5em; margin: 0.5em; width: 1.5em;"> SCRUM</label>
				<label><input type="checkbox" name="project_nav_tabs[]" <?php echo (strpos(','.$nav_tabs.',', ',tickets,') !== false ? 'checked' : ''); ?> value="tickets" style="height: 1.5em; margin: 0.5em; width: 1.5em;"> <?= TICKET_NOUN ?></label>
				<label><input type="checkbox" name="project_nav_tabs[]" <?php echo (strpos(','.$nav_tabs.',', ',daysheet,') !== false ? 'checked' : ''); ?> value="daysheet" style="height: 1.5em; margin: 0.5em; width: 1.5em;"> Day Sheet</label>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tilename" >
                    <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Tile Name Change<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_tilename" class="panel-collapse collapse">
            <div class="panel-body">
                <?php
                $project_tile_name = get_config($dbc, 'project_tile_name');
                if($project_tile_name == '') {
                    $project_tile_name = 'Projects';
                }
                ?>

                  <div class="form-group">
                    <label for="fax_number"	class="col-sm-4	control-label">Change Name:</label>
                    <div class="col-sm-8">
                        <input name="project_tile_name" type="text" value = "<?php echo $project_tile_name; ?>" class="form-control">
                    </div>
                  </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_next" >
                    Next Step After <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?><span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_next" class="panel-collapse collapse">
            <div class="panel-body">
                <?php
                $next_step_after_project = get_config($dbc, 'next_step_after_project');
                ?>

                <input type="checkbox" <?php if (strpos($next_step_after_project, "Ticket") !== FALSE) { echo " checked"; } ?> value="Ticket" style="height: 20px; width: 20px;" name="next_step_after_project[]">&nbsp;&nbsp;<?= TICKET_NOUN ?>&nbsp;&nbsp;

                <input type="checkbox" <?php if (strpos($next_step_after_project, "Work Order") !== FALSE) { echo " checked"; } ?> value="Work Order" style="height: 20px; width: 20px;" name="next_step_after_project[]">&nbsp;&nbsp;Work Order&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_next12" >
                    Services Accordion Field Names<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_next12" class="panel-collapse collapse">
            <div class="panel-body">

                <?php
                $project_service_price_or_hours = get_config($dbc, 'project_service_price_or_hours');
                if($project_service_price_or_hours == '') {
                    $project_service_price_or_hours = 'Actual Hours';
                }
                ?>

                <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label">Change Name Price/Hours:</label>
                <div class="col-sm-8">
                    <input name="project_service_price_or_hours" type="text" value = "<?php echo $project_service_price_or_hours; ?>" class="form-control">
                </div>
                </div>

                <?php
                $project_service_qty_cost = get_config($dbc, 'project_service_qty_cost');
                if($project_service_qty_cost == '') {
                    $project_service_qty_cost = 'Hourly Rate';
                }
                ?>

                <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label">Change Name Quantity/Cost:</label>
                <div class="col-sm-8">
                    <input name="project_service_qty_cost" type="text" value = "<?php echo $project_service_qty_cost; ?>" class="form-control">
                </div>
                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_project_types" >
                    Add <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Types<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_project_types" class="panel-collapse collapse">
            <div class="panel-body">
				Add Project/<?= ESTIMATE_TILE ?> Types separated by a comma in the order you want them on the dashboard:<br />
				<small>Note that removing a type will remove it from the dashboard, and you will not be able to access those projects.</small><br />
				<br />
				<input name="project_tabs" type="text" value="<?php echo implode(',',$project_tabs); ?>" class="form-control"/><br />
				<input type="hidden" name="project_type_tiles" value="HIDE">
				<label><input name="project_type_tiles" type="checkbox" value="" <?php echo (get_config($dbc, 'project_type_tiles') == 'HIDE' ? '' : 'checked'); ?>> Include Project Types on Menus</label>
            </div>
        </div>
    </div>

    <?php } else { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_EstimateDetails" >
                    <?= ESTIMATE_TILE ?> Details<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_EstimateDetails" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Estimate Details Estimate Information".',') !== FALSE) { echo " checked"; } ?> value="Estimate Details Estimate Information" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Estimate Information&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Estimate Details Templates".',') !== FALSE) { echo " checked"; } ?> value="Estimate Details Templates" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Templates&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Estimate Details Notes".',') !== FALSE) { echo " checked"; } ?> value="Estimate Details Notes" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Notes&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Estimate Details Details".',') !== FALSE) { echo " checked"; } ?> value="Estimate Details Details" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Details&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Estimate Details Support Documents".',') !== FALSE) { echo " checked"; } ?> value="Estimate Details Support Documents" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Support Documents&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Estimate Details Dates".',') !== FALSE) { echo " checked"; } ?> value="Estimate Details Dates" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Dates&nbsp;&nbsp;

            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Information" >
                    Project Information<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Information" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Information Contact Region".',') !== FALSE) { echo " checked"; } ?> value="Information Contact Region" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Region&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Information Contact Location".',') !== FALSE) { echo " checked"; } ?> value="Information Contact Location" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Location&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Information Contact Classification".',') !== FALSE) { echo " checked"; } ?> value="Information Contact Classification" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Classification&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Information Business".',') !== FALSE) { echo " checked"; } ?> value="Information Business" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Business&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Information Contact".',') !== FALSE) { echo " checked"; } ?> value="Information Contact" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Information Scope Template".',') !== FALSE) { echo " checked"; } ?> value="Information Scope Template" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Scope Template&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Information Rate Card".',') !== FALSE) { echo " checked"; } ?> value="Information Rate Card" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Rate Card&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Information Project Type".',') !== FALSE) { echo " checked"; } ?> value="Information Project Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Project Type&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Information Project Short Name".',') !== FALSE) { echo " checked"; } ?> value="Information Project Short Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Project Short Name&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Information AFE Num".',') !== FALSE) { echo " checked"; } ?> value="Information AFE Num" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;AFE#&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Path" >
                    Project Path<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Path" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Path Path".',') !== FALSE) { echo " checked"; } ?> value="Path Path" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Path&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Path Milestone Timeline".',') !== FALSE) { echo " checked"; } ?> value="Path Milestone Timeline" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Milestone &amp; Timeline&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Path Add New Template".',') !== FALSE) { echo " checked"; } ?> value="Path Add New Template" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Add New Template&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Detail" >
                    Details<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Detail" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Details Issue".',') !== FALSE) { echo " checked"; } ?> value="Details Issue" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Issue&nbsp;&nbsp;

                <input type="checkbox" <?php if (strpos($value_config, ','."Details Problem".',') !== FALSE) { echo " checked"; } ?> value="Details Problem" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Problem&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details GAP".',') !== FALSE) { echo " checked"; } ?> value="Details GAP" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;GAP&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Technical Uncertainty".',') !== FALSE) { echo " checked"; } ?> value="Details Technical Uncertainty" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Technical Uncertainty&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Base Knowledge".',') !== FALSE) { echo " checked"; } ?> value="Details Base Knowledge" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Base Knowledge&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Do".',') !== FALSE) { echo " checked"; } ?> value="Details Do" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Do&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Already Known".',') !== FALSE) { echo " checked"; } ?> value="Details Already Known" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Already Known&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Sources".',') !== FALSE) { echo " checked"; } ?> value="Details Sources" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Sources&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Current Designs".',') !== FALSE) { echo " checked"; } ?> value="Details Current Designs" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Current Designs&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Known Techniques".',') !== FALSE) { echo " checked"; } ?> value="Details Known Techniques" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Known Techniques&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Review Needed".',') !== FALSE) { echo " checked"; } ?> value="Details Review Needed" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Review Needed&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Looking to Achieve".',') !== FALSE) { echo " checked"; } ?> value="Details Looking to Achieve" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Looking to Achieve&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Plan".',') !== FALSE) { echo " checked"; } ?> value="Details Plan" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Plan&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Next Steps".',') !== FALSE) { echo " checked"; } ?> value="Details Next Steps" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Next Steps&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Learnt".',') !== FALSE) { echo " checked"; } ?> value="Details Learnt" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Learned&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Discovered".',') !== FALSE) { echo " checked"; } ?> value="Details Discovered" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Discovered&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Tech Advancements".',') !== FALSE) { echo " checked"; } ?> value="Details Tech Advancements" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Tech Advancements&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Work".',') !== FALSE) { echo " checked"; } ?> value="Details Work" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Work&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Adjustments Needed".',') !== FALSE) { echo " checked"; } ?> value="Details Adjustments Needed" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Adjustments Needed&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Future Designs".',') !== FALSE) { echo " checked"; } ?> value="Details Future Designs" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Future Designs&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Targets".',') !== FALSE) { echo " checked"; } ?> value="Details Targets" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Targets&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Audience".',') !== FALSE) { echo " checked"; } ?> value="Details Audience" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Audience&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Strategy".',') !== FALSE) { echo " checked"; } ?> value="Details Strategy" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Strategy&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Desired Outcome".',') !== FALSE) { echo " checked"; } ?> value="Details Desired Outcome" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Desired Outcome&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Actual Outcome".',') !== FALSE) { echo " checked"; } ?> value="Details Actual Outcome" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Actual Outcome&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Check".',') !== FALSE) { echo " checked"; } ?> value="Details Check" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Check&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Objective".',') !== FALSE) { echo " checked"; } ?> value="Details Objective" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Objective&nbsp;&nbsp;

                <input type="checkbox" <?php if (strpos($value_config, ','."Project Detail".',') !== FALSE) { echo " checked"; } ?> value="Project Detail" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Project Detail&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details2 Adjust".',') !== FALSE) { echo " checked"; } ?> value="Details2 Adjust" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Adjust&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Work Order Num".',') !== FALSE) { echo " checked"; } ?> value="Details Work Order Num" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Work Order#&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Procedure ID".',') !== FALSE) { echo " checked"; } ?> value="Details Procedure ID" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Procedure ID&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Quote Num".',') !== FALSE) { echo " checked"; } ?> value="Details Quote Num" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Quote#&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details DWG Num".',') !== FALSE) { echo " checked"; } ?> value="Details DWG Num" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;DWG#&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Quantity".',') !== FALSE) { echo " checked"; } ?> value="Details Quantity" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Quantity&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details SN".',') !== FALSE) { echo " checked"; } ?> value="Details SN" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;S/N&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Customer PO AFE Num".',') !== FALSE) { echo " checked"; } ?> value="Details Customer PO AFE Num" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Customer PO/AFE#&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Total Project Budget".',') !== FALSE) { echo " checked"; } ?> value="Details Total Project Budget" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Total Project Budget&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Documents" >
                    Documents<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Documents" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Documents Documents".',') !== FALSE) { echo " checked"; } ?> value="Documents Documents" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Documents&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Documents Links".',') !== FALSE) { echo " checked"; } ?> value="Documents Links" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Links&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Dates" >
                    Dates<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Dates" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Dates Project Created Date".',') !== FALSE) { echo " checked"; } ?> value="Dates Project Created Date" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Project Created Date&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Dates Project Start Date".',') !== FALSE) { echo " checked"; } ?> value="Dates Project Start Date" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Project Start Date&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Dates Estimate Completion Date".',') !== FALSE) { echo " checked"; } ?> value="Dates Estimate Completion Date" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Estimate Completion Date&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Dates Effective Date".',') !== FALSE) { echo " checked"; } ?> value="Dates Effective Date" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Effective Date&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Dates Time Clock Start Date".',') !== FALSE) { echo " checked"; } ?> value="Dates Time Clock Start Date" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Time Clock Start Date&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Package" >
                    Package<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Package" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Package".',') !== FALSE) { echo " checked"; } ?> value="Package" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Package
                <br><br>

                <input type="checkbox" <?php if (strpos($value_config, ','."Package Service Type".',') !== FALSE) { echo " checked"; } ?> value="Package Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Package Category".',') !== FALSE) { echo " checked"; } ?> value="Package Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Package Heading".',') !== FALSE) { echo " checked"; } ?> value="Package Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Promotion" >
                    Promotion<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Promotion" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Promotion".',') !== FALSE) { echo " checked"; } ?> value="Promotion" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Promotion
                <br><br>

                <input type="checkbox" <?php if (strpos($value_config, ','."Promotion Service Type".',') !== FALSE) { echo " checked"; } ?> value="Promotion Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Promotion Category".',') !== FALSE) { echo " checked"; } ?> value="Promotion Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Promotion Heading".',') !== FALSE) { echo " checked"; } ?> value="Promotion Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Custom" >
                    Custom<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Custom" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Custom".',') !== FALSE) { echo " checked"; } ?> value="Custom" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Custom
                <br><br>

                <input type="checkbox" <?php if (strpos($value_config, ','."Custom Service Type".',') !== FALSE) { echo " checked"; } ?> value="Custom Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Custom Category".',') !== FALSE) { echo " checked"; } ?> value="Custom Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Custom Heading".',') !== FALSE) { echo " checked"; } ?> value="Custom Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Material" >
                    Material<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Material" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Material".',') !== FALSE) { echo " checked"; } ?> value="Material" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Material
                <br><br>

                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Material Code".',') !== FALSE) { echo " checked"; } ?> value="Material Code" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Code&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Services" >
                    Services<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Services" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Services".',') !== FALSE) { echo " checked"; } ?> value="Services" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Services
                <br><br>

                <input type="checkbox" <?php if (strpos($value_config, ','."Services Service Type".',') !== FALSE) { echo " checked"; } ?> value="Services Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Services Category".',') !== FALSE) { echo " checked"; } ?> value="Services Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Services Heading".',') !== FALSE) { echo " checked"; } ?> value="Services Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Products" >
                    Products<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Products" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Products".',') !== FALSE) { echo " checked"; } ?> value="Products" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Products
                <br><br>

                <input type="checkbox" <?php if (strpos($value_config, ','."Products Product Type".',') !== FALSE) { echo " checked"; } ?> value="Products Product Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product Type&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Products Category".',') !== FALSE) { echo " checked"; } ?> value="Products Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Products Heading".',') !== FALSE) { echo " checked"; } ?> value="Products Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sred" >
                    SR&ED<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_sred" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."SRED".',') !== FALSE) { echo " checked"; } ?> value="SRED" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;SR&ED
                <br><br>

                <input type="checkbox" <?php if (strpos($value_config, ','."SRED SRED Type".',') !== FALSE) { echo " checked"; } ?> value="SRED SRED Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;SR&ED Type&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."SRED Category".',') !== FALSE) { echo " checked"; } ?> value="SRED Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."SRED Heading".',') !== FALSE) { echo " checked"; } ?> value="SRED Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Staff" >
                    Staff<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Staff" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Staff
                <br><br>

                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Staff Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Staff Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Contractor" >
                    Contractor<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Contractor" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Contractor".',') !== FALSE) { echo " checked"; } ?> value="Contractor" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contractor
                <br><br>

                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Contractor Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Contractor Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Clients" >
                    Clients<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Clients" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Clients".',') !== FALSE) { echo " checked"; } ?> value="Clients" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Clients
                <br><br>

                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Clients Client Name".',') !== FALSE) { echo " checked"; } ?> value="Clients Client Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Client Name&nbsp;&nbsp;

                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Clients Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Clients Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pl" >
                    Vendor Pricelist<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_pl" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Vendor Pricelist".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Vendor Pricelist
                <br><br>

                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Vendor Pricelist Vendor".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Vendor" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Vendor&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Vendor Pricelist Price List".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Price List" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Price List&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Vendor Pricelist Category".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Vendor Pricelist Product".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Product" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Customer" >
                    Customer<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Customer" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Customer
                <br><br>

                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Customer Customer Name".',') !== FALSE) { echo " checked"; } ?> value="Customer Customer Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Customer Name&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Customer Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Customer Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Inventory" >
                    Inventory<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Inventory" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Inventory".',') !== FALSE) { echo " checked"; } ?> value="Inventory" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Inventory
                <br><br>

                <input type="checkbox" <?php if (strpos($value_config, ','."Inventory Category".',') !== FALSE) { echo " checked"; } ?> value="Inventory Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
				<input type="checkbox" <?php if (strpos($value_config, ','."Inventory Part Number".',') !== FALSE) { echo " checked"; } ?> value="Inventory Part Number" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Part Number&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Inventory Product Name".',') !== FALSE) { echo " checked"; } ?> value="Inventory Product Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product Name&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Equipment" >
                    Equipment<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Equipment" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Equipment
                <br><br>

                <input type="checkbox" <?php if (strpos($value_config, ','."Equipment Category".',') !== FALSE) { echo " checked"; } ?> value="Equipment Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Equipment Unit/Serial Number".',') !== FALSE) { echo " checked"; } ?> value="Equipment Unit/Serial Number" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Unit/Serial Number&nbsp;&nbsp;
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Labour" >
                    Labour<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Labour" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Labour".',') !== FALSE) { echo " checked"; } ?> value="Labour" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Labour
                <br><br>

                <input type="checkbox" <?php if (strpos($value_config, ','."Labour Type".',') !== FALSE) { echo " checked"; } ?> value="Labour Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Type&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Labour Heading".',') !== FALSE) { echo " checked"; } ?> value="Labour Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Expenses" >
                    Expenses<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Expenses" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Expenses".',') !== FALSE) { echo " checked"; } ?> value="Expenses" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Expenses
                <br><br>

                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Expenses Type".',') !== FALSE) { echo " checked"; } ?> value="Expenses Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Type&nbsp;&nbsp;
                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Expenses Category".',') !== FALSE) { echo " checked"; } ?> value="Expenses Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Other" >
                    Other<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Other" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Other".',') !== FALSE) { echo " checked"; } ?> value="Other" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Other
                <br><br>

                <input disabled type="checkbox" <?php if (strpos($value_config, ','."Other Detail".',') !== FALSE) { echo " checked"; } ?> value="Other Detail" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Detail&nbsp;&nbsp;
            </div>
        </div>
    </div>

    <?php } ?>

</div>

<?php if($type == 'Pending') { ?>
<div class="form-group">
    <div class="col-sm-6">
        <a href="project.php?tab=projects&type=<?php echo $_GET['type']; ?>" class="btn brand-btn btn-lg">Back</a>
		<!--<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>-->
    </div>
    <div class="col-sm-6">
        <button	type="submit" name="submit_general"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
    </div>
</div>
<?php } else { ?>
<div class="form-group">
    <div class="col-sm-6">
        <a href="project.php?tab=projects&type=<?php echo $_GET['type']; ?>" class="btn brand-btn btn-lg">Back</a>
		<!--<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>-->
    </div>
    <div class="col-sm-6">
        <button	type="submit" name="submit_all"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
    </div>
</div>
<?php } ?>

</form>
</div>
</div>

<?php include_once('../footer.php'); ?>
