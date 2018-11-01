<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('estimate');
error_reporting(0);
$main_page = 'estimate.php';

if (isset($_POST['submit_tab_config'])) {
    $config_fields_dashboard = implode(',',$_POST['config_fields_dashboard']);
    $config_fields = implode(',',$_POST['config_fields']);

    if (strpos(','.$config_fields.',',','.'Package Heading,Promotion Heading,Custom Heading,Material Code,Services Heading,Products Heading,SRED Heading,Staff Contact Person,Contractor Contact Person,Clients Client Name,Clients Contact Person,Vendor Pricelist Vendor,Vendor Pricelist Price List,Vendor Pricelist Category,Vendor Pricelist Product,Customer Customer Name,Customer Contact Person,Inventory Product Name,Equipment Unit/Serial Number,Labour Heading,Expenses Type,Expenses Category,Other Detail'.',') === false) {
        $config_fields = 'Package Heading,Promotion Heading,Custom Heading,Material Code,Services Heading,Products Heading,SRED Heading,Staff Contact Person,Contractor Contact Person,Clients Client Name,Clients Contact Person,Vendor Pricelist Vendor,Vendor Pricelist Price List,Vendor Pricelist Category,Vendor Pricelist Product,Customer Customer Name,Customer Contact Person,Inventory Product Name,Equipment Unit/Serial Number,Labour Heading,Expenses Type,Expenses Category,Other Detail,'.$config_fields;
    }
	//Field Labels
	$labels = [];
	foreach($_POST['info_value'] as $key => $description) {
		$field = $_POST['info_name'][$key];
		$labels[] = $field.'#*#'.$description;
	}
	$label_config = implode(',',$labels);

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

	//Staff Groups
	$groups = [];
	$i = 0;
	foreach($_POST['group_name'] as $key => $value) {
		$group = [];
		$group[] = $value;
		for(; $i < count($_POST['group_staff']) && $_POST['group_staff'][$i] != ''; $i++) {
			$group[] = $_POST['group_staff'][$i];
		}
		$i++;
		$groups[] = implode(',',$group);
	}
	$staff_groups = implode('#*#',$groups);

	//Custom Accordions
	$accordions = [];
	foreach($_POST['accordion'] as $key => $value) {
		$accordions[$_POST['accordion_order'][$key]] = $value;
	}
	ksort($accordions);
	$accordion_config = implode('#*#',$accordions);

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigestimateid) AS fieldconfigestimateid FROM field_config_estimate"));
    if($get_field_config['fieldconfigestimateid'] > 0) {
        $query_update_employee = "UPDATE `field_config_estimate` SET config_fields = '$config_fields', config_fields_dashboard = '$config_fields_dashboard', `field_info` = '$label_config', `estimate_groups` = '$staff_groups', `custom_accordions` = '$accordion_config' WHERE `fieldconfigestimateid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config_estimate` (`config_fields`, `config_fields_dashboard`, `field_info`, `estimate_groups`, `custom_accordions`) VALUES ('$config_fields', '$config_fields_dashboard', '$label_config', '$staff_groups', '$accordion_config')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	//Field Labels and Order
	$labels = [];
	foreach($_POST['estimate_field_name'] as $key => $name) {
		$labels[] = $name.'***'.$_POST['estimate_field_label'][$key];
	}
	$label_config = implode('#*#',$labels);
	$result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimate_field_order' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimate_field_order') num WHERE num.rows=0");
	$result = mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$label_config' WHERE `name`='estimate_field_order'");

    //Tile Name
    $estimate_service_price_or_hours = $_POST['estimate_service_price_or_hours'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='estimate_service_price_or_hours'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$estimate_service_price_or_hours' WHERE name='estimate_service_price_or_hours'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('estimate_service_price_or_hours', '$estimate_service_price_or_hours')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Tile Name

    $estimate_service_qty_cost = $_POST['estimate_service_qty_cost'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='estimate_service_qty_cost'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$estimate_service_qty_cost' WHERE name='estimate_service_qty_cost'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('estimate_service_qty_cost', '$estimate_service_qty_cost')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"></script>';
}
?>
<script>
$(document).ready(function(){
    $("#selectall").change(function(){
      $("input[name='config_fields[]']").prop('checked', $(this).prop("checked"));
    });
	$('.add-staff-group').click(function() {
		var clone = $('#collapse_staff_collab .form-group:last').clone();
		clone.find('[name^=group_name]').val('');
		resetChosen(clone.find("[name^=group_staff]"));
		$('#collapse_staff_collab .form-group:last').after(clone);
		return false;
	});
	$('.add-accordion').click(function() {
		var clone = $('.custom-accordion:first').clone();
		clone.find('input').val('');
		resetChosen(clone.find("[class^=chosen]"));
		clone.find('.remove-accordion').click(remove_accordion);
		$(this).before(clone);
		var selects = $('[name="accordion_order[]"]');
		var max = parseInt(selects.find('option:last').val()) + 1;
		selects.append('<option value='+max+'>'+max+'</option>');
		selects.last().val(max);
		selects.trigger('change.select2');
		return false;
	});
	$('.remove-accordion').click(remove_accordion);
	$('[name="estimate_field_name[]"]').change(function() {
		if(this.checked) {
			$(this).closest('label').find('input[type=text]').removeAttr('disabled');
		} else {
			$(this).closest('label').find('input[type=text]').attr('disabled','disabled');
		}
	});
});
function remove_accordion() {
	$(this).parents('.custom-accordion').remove();
	return false;
}
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1><?= ESTIMATE_TILE ?></h1>
<div class="pad-left gap-top double-gap-bottom"><a href="<?php echo $main_page; ?>" class="btn brand-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>-->
<div class="pad-left">
	<?php $active_tab = ''; $tab_active_tab = ''; ?>
	<?php if($_GET['tab']): ?>
		<?php $tab_active_tab = 'active_tab'; ?>
	<?php else: ?>
		<?php $active_tab = 'active_tab'; ?>
	<?php endif; ?>
	<a href='field_config_estimate.php'><button type="button" class="btn brand-btn mobile-block <?php echo $active_tab; ?>" ><?= ESTIMATE_TILE ?> Config</button></a>
	<a href='field_config_quote.php'><button type="button" class="btn brand-btn mobile-block" >Quote Config</button></a>
	<a href='field_config_estimate.php?tab=add'><button type="button" class="btn brand-btn mobile-block <?php echo $tab_active_tab; ?>" ><?= ESTIMATE_TILE ?> Tab Config</button></a>
</div>
	<br>
<?php if($_GET['tab']): ?>
	<?php include('tab_config_estimate.php'); ?>
<?php else: ?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<input type="hidden" name="referer" value="<?php echo $main_page; ?>" />
<?php
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_estimate"));
$value_config = ','.$get_field_config['config_fields'].',';
$config_fields_dashboard = ','.$get_field_config['config_fields_dashboard'].',';

$info_config = explode(',',$get_field_config['field_info']);
$short_name = 'Short Name/LSD';
$expiry_length = '';
foreach($info_config as $str) {
	$info_arr = explode('#*#',$str);
	if($info_arr[0] == 'short_name') {
		$short_name = $info_arr[1];
	} else if($info_arr[0] == 'expiry_length') {
		$expiry_length = $info_arr[1];
	} else if($info_arr[0] == 'group_names') {
		$groups = explode('*',$info_arr[1]);
		foreach($groups as $group) {
			$staff_groups = explode(',',$group);
		}
	}
}
$staff_groups = [];
foreach(explode('#*#',$get_field_config['estimate_groups']) as $group) {
	$staff_groups[] = explode(',',$group);
}

$project_tabs = get_config($dbc, 'project_tabs');
?>
<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dash" >
                    <?= ESTIMATE_TILE ?> Dashboard Config<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_dash" class="panel-collapse collapse">
            <div class="panel-body">

                 <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."Estimate#".',') !== FALSE) { echo " checked"; } ?> value="Estimate#" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;<?= ESTIMATE_TILE ?>#&nbsp;&nbsp;

                <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;Business&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."Estimate Name".',') !== FALSE) { echo " checked"; } ?> value="Estimate Name" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;<?= rtrim(ESTIMATE_TILE, 's') ?> Name&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."Total Cost".',') !== FALSE) { echo " checked"; } ?> value="Total Cost" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;Total Cost&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."Notes".',') !== FALSE) { echo " checked"; } ?> value="Notes" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;Notes&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."Financial Summary".',') !== FALSE) { echo " checked"; } ?> value="Financial Summary" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;Financial Summary&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."Review Quote".',') !== FALSE) { echo " checked"; } ?> value="Review Quote" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;Review Quote&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."Preview Draft".',') !== FALSE) { echo " checked"; } ?> value="Preview Draft" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;Preview Draft&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;Status&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."History".',') !== FALSE) { echo " checked"; } ?> value="History" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;History&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($config_fields_dashboard, ','."Expiration Date".',') !== FALSE) { echo " checked"; } ?> value="Expiration Date" style="height: 20px; width: 20px;" name="config_fields_dashboard[]">&nbsp;&nbsp;Expiration Date&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_basic" >
                    <?= ESTIMATE_TILE ?> Configuration<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_basic" class="panel-collapse collapse">
            <div class="panel-body">
                <input type="checkbox" <?php if (strpos($value_config, ','."Estimate Site".',') !== FALSE) { echo " checked"; } ?> value="Estimate Site" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Customer Site Location&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."AFE Number".',') !== FALSE) { echo " checked"; } ?> value="AFE Number" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Customer AFE #&nbsp;&nbsp;
				<div class="form-group">
					<label class="col-sm-4 control-label text-right">Short Name Field Description:</label>
					<div class="col-sm-8"><input type="text" name="info_value[]" class="form-control" value="<?php echo $short_name; ?>"></div>
					<input type="hidden" name="info_name[]" value="short_name">
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label text-right">Default Expiry Length (in days):</label>
					<div class="col-sm-8"><input type="number" name="info_value[]" class="form-control" value="<?php echo $expiry_length; ?>"></div>
					<input type="hidden" name="info_name[]" value="expiry_length">
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label text-right">Add <?= ESTIMATE_TILE ?>/Project Types separated by a comma:</label>
					<div class="col-sm-8"><input name="project_tabs" type="text" value="<?php echo $project_tabs; ?>" class="form-control"/></div>
				</div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff_collab" >
                    Staff Collaboration Groups<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_staff_collab" class="panel-collapse collapse">
            <div class="panel-body">
				<div class="form-group">
					<label class="col-sm-4 control-label" style="text-align:center;">Staff Group</label>
					<label class="col-sm-8 control-label" style="text-align:center;">Group Members</label>
				</div>
				<?php foreach($staff_groups as $group) { ?>
					<div class="form-group">
						<div class="col-sm-4"><input type="text" name="group_name[]" class="form-control" value="<?php echo $group[0]; ?>"></div>
						<div class="col-sm-8">
							<select multiple name="group_staff[]" data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" width="380">
								<?php
								$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 order by first_name");
								while($row = mysqli_fetch_array($query)) {
									?>
									<option <?php if (in_array($row['contactid'],$group) === true) { echo " selected"; } ?> value="<?php echo $row['contactid']; ?>"><?php echo decryptIt($row['first_name']). ' '.decryptIt($row['last_name']); ?></option>
								<?php }
								?>
								<option value=""></option>
								  <?php
									$sorted_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, category, name, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND status>0"), MYSQLI_ASSOC));
									foreach($sorted_list as $id) { ?>
										<option value='<?php echo  $id; ?>' ><?php echo get_contact($dbc, $id); ?></option>
									<?php  } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<button class='btn brand-btn add-staff-group pull-right'>Add Collaboration Group</button>
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

                <input type="checkbox" <?php if (strpos($value_config, ','."Details Detail".',') !== FALSE) { echo " checked"; } ?> value="Details Detail" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Detail&nbsp;&nbsp;
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
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Already known".',') !== FALSE) { echo " checked"; } ?> value="Details Already known" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Already known&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Looking to Achieve".',') !== FALSE) { echo " checked"; } ?> value="Details Looking to Achieve" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Looking to Achieve&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Plan".',') !== FALSE) { echo " checked"; } ?> value="Details Plan" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Plan&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Next Steps".',') !== FALSE) { echo " checked"; } ?> value="Details Next Steps" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Next Steps&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Learnt".',') !== FALSE) { echo " checked"; } ?> value="Details Learned" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Learnt&nbsp;&nbsp;
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
                <input type="checkbox" <?php if (strpos($value_config, ','."Details Notes".',') !== FALSE) { echo " checked"; } ?> value="Details Notes" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Notes for Quote&nbsp;&nbsp;

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Budget" >
                    Budget<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_Budget" class="panel-collapse collapse">
            <div class="panel-body">

                <input type="checkbox" <?php if (strpos($value_config, ','."Budget".',') !== FALSE) { echo " checked"; } ?> value="Budget" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Budget

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

                <?php
                $estimate_service_price_or_hours = get_config($dbc, 'estimate_service_price_or_hours');
                if($estimate_service_price_or_hours == '') {
                    $estimate_service_price_or_hours = 'Estimated Hours';
                }
                ?>

              <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label">Change Name Price/Hours:</label>
                <div class="col-sm-8">
                    <input name="estimate_service_price_or_hours" type="text" value = "<?php echo $estimate_service_price_or_hours; ?>" class="form-control">
                </div>
              </div>

                <?php
                $estimate_service_qty_cost = get_config($dbc, 'estimate_service_qty_cost');
                if($estimate_service_qty_cost == '') {
                    $estimate_service_qty_cost = 'Cost Per Hours';
                }
                ?>

              <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label">Change Name Quantity/Cost:</label>
                <div class="col-sm-8">
                    <input name="estimate_service_qty_cost" type="text" value = "<?php echo $estimate_service_qty_cost; ?>" class="form-control">
                </div>
              </div>

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

                <input type="checkbox" <?php if (strpos($value_config, ','."Inventory UOM".',') !== FALSE) { echo " checked"; } ?> value="Inventory UOM" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;UOM&nbsp;&nbsp;

                <input type="checkbox" <?php if (strpos($value_config, ','."Inventory Category".',') !== FALSE) { echo " checked"; } ?> value="Inventory Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;

               <?php /* <input disabled type="checkbox" <?php if (strpos($value_config, ','."Inventory Product Name".',') !== FALSE) { echo " checked"; } ?> value="Inventory Product Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product Name&nbsp;&nbsp; */ ?>

			    <input disabled type="checkbox" <?php if (strpos($value_config, ','."Inventory Product Name".',') !== FALSE) { echo " checked"; } ?> value="Inventory Product Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product Name&nbsp;&nbsp;

				<input type="checkbox" <?php if (strpos($value_config, ','."Inventory Part No".',') !== FALSE) { echo " checked"; } ?> value="Inventory Part No" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Part Number&nbsp;&nbsp;

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

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_custom" >
                    Custom Accordions<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_custom" class="panel-collapse collapse">
            <div class="panel-body">
				<?php $accordion_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `custom_accordions`, `custom_accordion_labels` FROM `field_config_estimate`"));
				$accordions = explode('#*#',$accordion_result['custom_accordions']);
				$accordion_labels = explode('#*#',$accordion_label_result['custom_accordions']);
				$acc_count = count($accordions);
				foreach($accordions as $acc_order => $accordion):
					$acc_fields = explode(',',$accordion);
					$acc_name = $acc_fields[0];
					$acc_labels = explode('*#*',$accordion_labels[$acc_order]);
					unset($acc_fields[0]); ?>
					<div class="custom-accordion">
						<button class="btn brand-btn pull-right remove-accordion">Remove Accordion</button><div class="clearfix"></div>
						<label class="col-sm-4"></label><label class="col-sm-4" style="text-align:center;">Field Order</label><label class="col-sm-4" style="text-align:center;"><?= ESTIMATE_TILE ?> Label</label>
						<div class="form-group">
							<label for="accordion[]" class="col-sm-4 control-label">Accordion Name:</label>
							<div class="col-sm-8">
								<input name="accordion[]" type="text" value="<?php echo $acc_name; ?>" class="form-control">
							</div>
							<label for="accordion_order[]" class="col-sm-4 control-label">Accordion Order:</label>
							<div class="col-sm-8">
								<select name="accordion_order[]" class="chosen-select">
								<?php for($i = 1; $i <= $acc_count; $i++) :
									echo "<option ".($i == $acc_order+1 ? "selected " : "")."value='$i'>$i</option>";
								endfor; ?>
								</select>
							</div>
						</div>
					</div>
				<?php endforeach; ?>

                <button class="btn brand-btn pull-right add-accordion">Add Accordion</button>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field_order" >
                    Field Display Order<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_field_order" class="panel-collapse collapse">
            <div class="panel-body">
				<div id='no-more-tables'>
				<h3>Field Sort Order and Labels</h3>
				These settings will affect the Rate Card tile, as well as Cost Estimates and Estimates. Move the fields around to change the display order.
				<div class='sortable' style='border:solid 1px black;'>
					<style>
					#no-more-tables .sortable label {
						background-color: RGBA(255,255,255,0.2);
						margin: 0.5em;
						min-width: 25em;
						padding: 0.5em;
					}
					#no-more-tables .sortable label input[type=checkbox] {
						height: 1.5em;
						margin: 0.25em;
						width: 1.5em;
					}
					</style>
					<script>
					$(document).ready(function() {
						$('.sortable').sortable({
						  connectWith: '.sortable',
						  items: 'label'
						});
					});
					</script>
					<?php $estimate_field_order = get_config($dbc, 'estimate_field_order');
					if($estimate_field_order == '') {
						$estimate_field_order = $accordions[0];
						if($estimate_field_order == '') {
							$estimate_field_order = trim(str_replace([',itemtype,',',estimate,',',category,',',tile,',',breakdown,'], [',Item Type,',',Estimate Price,','','',''], get_config($dbc, 'company_rate_fields')),',');
							$estimate_field_order = explode(',', $estimate_field_order);
						} else {
							$estimate_field_order = explode(',', $estimate_field_order);
							unset($estimate_field_order[0]);
						}
					} else {
						$estimate_field_order = explode('#*#', $estimate_field_order);
					}
					$estimate_field_order = array_map('ucwords',$estimate_field_order);
					$defaults = 'Type,Heading,Description,Item Type,Daily,Hourly,Customer Price,UOM,Quantity,Cost,Margin,Profit,Estimate Price,Total,Total Multiple,';
					foreach($estimate_field_order as $value) {
						$defaults = trim(str_replace(','.explode('***',$value)[0].',',',',','.$defaults.','),',');
					}
					$estimate_field_arr = array_filter(array_unique(array_merge($estimate_field_order,explode(',',$defaults))));
					foreach($estimate_field_arr as $field_order) {
						$data = explode('***', $field_order);
						$field = $data[0];
						$label = $data[1];
						echo '<label><input type="checkbox" '.(in_array($field_order,$estimate_field_order) ? 'checked' : '').' value="'.$field.'" name="estimate_field_name[]">';
						echo $field; //.($field == 'Total Multiple' ? ' - Label appended with X [#]' : '');
						echo ': <input type="text" '.(in_array($field_order,$estimate_field_order) ? '' : 'disabled').' class="form-control" name="estimate_field_label[]" value="'.$label.'"></label>';
					} ?>
				</div>
            </div>
        </div>
    </div>
</div>

<br /><div class="clearfix"></div>
<div class="form-group">
    <div class="col-sm-6">
        <a href="<?php echo $main_page; ?>" class="btn brand-btn btn-lg">Back</a>
		<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
	</div>
	<div class="col-sm-6">
        <button	type="submit" name="submit_tab_config"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
    </div>
</div>

</form>
<?php endif; ?>
</div>
</div>
<script src="<?php echo WEBSITE_URL;?>/js/chosen.jquery.js" type="text/javascript"></script>
<script>
var config = {
	'.chosen-select'           : {},
	'.chosen-select-deselect'  : {allow_single_deselect:true},
	'.chosen-select-no-single' : {disable_search_threshold:10},
	'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
	'.chosen-select-width'     : {width:"95%"}
}
for (var selector in config) {
	$(selector).chosen(config[selector]);
}
</script>
<?php include ('../footer.php'); ?>