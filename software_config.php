<?php
/*
Customer Listing
*/
include ('include.php');
?>
<script type="text/javascript">
    function tileConfig(sel) {
        var type = sel.type;
        var name = sel.name;
        var tile_value = sel.value;
        var final_value = '*';
		var contactid = $('.contacterid').val();

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
			url: "ajax_all.php?fill=tile_config&name="+name+"&value="+final_value+"&turnoff="+turnoff+"&turnOn="+turnOn+"&contactid="+contactid,
			dataType: "html",   //expect html to be returned
			success: function(response){
			}
		});
    }
$(document).ready(function() {
	$('.iframe_open').click(function(){
		var tile = $(this).data('option');
		var title = $(this).parents('tr').children(':first').text();
		$('#iframe_instead_of_window').attr('src', 'tile_history.php?tile_name='+tile+'&title='+title);
		$('.iframe_title').text('Tile Status History');
		$('.iframe_holder').show();
		$('.hide_on_iframe').hide();
	});

	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});

	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		this.style.height = (this.contentWindow.document.body.offsetHeight + 80) + 'px';
	});
});
</script>
</head>
<body>
<?php include_once ('navigation.php');
checkAuthorised();
?>

<div class="container triple-pad-bottom">
	<div class='iframe_holder' style='display:none;'>
		<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
    </div>
    <div class="row hide_on_iframe">
		<div class="col-md-12">

        <?php include('Settings/settings_navigation.php'); ?>
		<br><br>
		<?php if(config_visible_function($dbc, 'software_config') == 1) {
			/*href="config_settings.php?type=software_config"*/
				echo '<a class="mobile-block pull-right " onClick="alert(\'Coming soon!\');"><img style="width: 50px;" title="Tile Settings" src="img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right" style="margin:-5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile."><img src="img/info.png" width="20"></a></span><br><br>';
			} ?>
		<div id="">
        <?php

        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM admin_tile_config WHERE tileconfigid=1"));

        $software_config = $get_config['software_config'];
        $profile = $get_config['profile'];
        $security = $get_config['security'];
        $contacts = $get_config['contacts'];
        $documents = $get_config['documents'];
        $infogathering = $get_config['infogathering'];
        $hr = $get_config['hr'];
        $package = $get_config['package'];
        $promotion = $get_config['promotion'];
        $services = $get_config['services'];
        $passwords = $get_config['passwords'];
        $sred = $get_config['sred'];
        $labour = $get_config['labour'];
        $material = $get_config['material'];
        $inventory = $get_config['inventory'];
        $assets = $get_config['assets'];
        $equipment = $get_config['equipment'];
        $custom = $get_config['custom'];
        $pos = $get_config['pos'];
        $incident_report = $get_config['incident_report'];
        $policy_procedure = $get_config['policy_procedure'];
        $ops_manual = $get_config['ops_manual'];
        $emp_handbook = $get_config['emp_handbook'];
        $how_to_guide = $get_config['how_to_guide'];
        $safety = $get_config['safety'];
        $rate_card = $get_config['rate_card'];
        $estimate = $get_config['estimate'];
        $quote = $get_config['quote'];
		$cost_estimate = $get_config['cost_estimate'];
        $project = $get_config['project'];
        $project_workflow = $get_config['project_workflow'];
        $ticket = $get_config['ticket'];
        $field_job = $get_config['field_job'];
		$shop_work_orders = $get_config['shop_work_orders'];
        $report = $get_config['report'];
        $field_ticket_estimates = $get_config['field_ticket_estimates'];
        $driving_log = $get_config['driving_log'];
        $expense = $get_config['expense'];
        $marketing = $get_config['marketing'];

        $internal = $get_config['internal'];
        $rd = $get_config['rd'];
        $business_development = $get_config['business_development'];
        $process_development = $get_config['process_development'];
        $addendum = $get_config['addendum'];
        $addition = $get_config['addition'];
        $manufacturing = $get_config['manufacturing'];
        $assembly = $get_config['assembly'];

        $work_order = $get_config['work_order'];
        $daysheet = $get_config['daysheet'];
        $punch_card = $get_config['punch_card'];

        $certificate = $get_config['certificate'];
        $marketing_material = $get_config['marketing_material'];
        $internal_documents = $get_config['internal_documents'];
        $client_documents = $get_config['client_documents'];
        $products = $get_config['products'];
        $tasks = $get_config['tasks'];
        $tasks_updated = $get_config['tasks_updated'];
        $agenda_meeting = $get_config['agenda_meeting'];
        $sales = $get_config['sales'];
        $gantt_chart = $get_config['gantt_chart'];
        $communication = $get_config['communication'];
        $purchase_order = $get_config['purchase_order'];
        $orientation = $get_config['orientation'];
		$sales_order = $get_config['sales_order'];
		$vpl = $get_config['vpl'];
        $helpdesk = $get_config['helpdesk'];
        $time_tracking = $get_config['time_tracking'];
        $newsboard = $get_config['newsboard'];
		$ffmsupport = $get_config['ffmsupport'];
		$archiveddata = $get_config['archiveddata'];
        $email_communication = $get_config['email_communication'];
        $scrum = $get_config['scrum'];
        $charts = $get_config['charts'];
        $daily_log_notes = $get_config['daily_log_notes'];
        $timesheet = $get_config['timesheet'];
		$staff = $get_config['staff'];
        $checklist = $get_config['checklist'];
        $calllog = $get_config['calllog'];
		$budget = $get_config['budget'];
		$gao = $get_config['gao'];

        $routine = $get_config['routine'];
        $day_program = $get_config['day_program'];
        $match = $get_config['match'];
        $fund_development = $get_config['fund_development'];

        $medication = $get_config['medication'];
        $individual_support_plan = $get_config['individual_support_plan'];
        $social_story = $get_config['social_story'];

        ?>
		<div class="notice double-gap-bottom popover-examples">
		<div class="col-sm-1 notice-icon"><img src="img/info.png" class="wiggle-me" width="25px"></div>
		<div class="col-sm-16"><span class="notice-name">NOTE:</span>
		Turning on/off a tile in this section will turn it off for every user on the software. If you would like to enable/disable tiles for specific users, please go to the "Set Security Privileges" section.</div>
		<div class="clearfix"></div>
	</div>
        <table class='table table-bordered'>
            <tr class='hidden-sm '>
                <th>Available Software Tiles & Functionality</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="img/info.png" width="20"></a>
                </span>
                Turn On Tile</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="img/info.png" width="20"></a>
                </span>
                Turn Off Tile</th>
                <th>History</th>
                <th>Status</th>
            </tr>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Software Settings:</div></th></tr>
			<?php if (strpos($archiveddata, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Archived Data</td>
                <?php echo tile_config_function($dbc,'archiveddata'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($ffmsupport, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">FFM Support</td>
                <?php echo tile_config_function($dbc,'ffmsupport'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($helpdesk, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Help Desk</td>
                <?php echo tile_config_function($dbc,'helpdesk'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($security, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Security</td>
                <?php echo tile_config_function($dbc,'security'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($software_config, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Settings</td>
                <?php echo tile_config_function($dbc,'software_config'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($software_config, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Staff</td>
                <?php echo tile_config_function($dbc,'staff'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Common Practice:</div></th></tr>
			<?php if (strpos($agenda_meeting, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Agendas & Meetings</td>
                <?php echo tile_config_function($dbc,'agenda_meeting'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($checklist, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Checklist</td>
                <?php echo tile_config_function($dbc,'checklist'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($client_documents, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Client Documents</td>
                <?php echo tile_config_function($dbc,'client_documents'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($contacts, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Contacts</td>
                <?php echo tile_config_function($dbc,'contacts'); ?>
            </tr>
            <?php } ?>

			<?php if (strpos($documents, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Documents</td>
                <?php echo tile_config_function($dbc,'documents'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($internal_documents, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Internal Documents</td>
                <?php echo tile_config_function($dbc,'internal_documents'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($passwords, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Passwords</td>
                <?php echo tile_config_function($dbc,'passwords'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($profile, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Profile</td>
                <?php echo tile_config_function($dbc,'profile'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Human Resources:</div></th></tr>
			<?php if (strpos($certificate, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Certificates</td>
                <?php echo tile_config_function($dbc,'certificate'); ?>
            </tr>
            <?php } ?>
			 <?php if (strpos($emp_handbook, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Employee Handbook</td>
                <?php echo tile_config_function($dbc,'emp_handbook'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($how_to_guide, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">How to Guide</td>
                <?php echo tile_config_function($dbc,'how_to_guide'); ?>
            </tr>
            <?php } ?>
			 <?php if (strpos($hr, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">HR</td>
                <?php echo tile_config_function($dbc,'hr'); ?>
            </tr>
            <?php } ?>
			 <?php if (strpos($incident_report, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= INC_REP_TILE ?></td>
                <?php echo tile_config_function($dbc,'incident_report'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($ops_manual, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Operations Manual</td>
                <?php echo tile_config_function($dbc,'ops_manual'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($orientation, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Orientation</td>
                <?php echo tile_config_function($dbc,'orientation'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($policy_procedure, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Policies &amp; Procedures</td>
                <?php echo tile_config_function($dbc,'policy_procedure'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Sales:</div></th></tr>
			<?php if (strpos($calllog, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Cold Call</td>
                <?php echo tile_config_function($dbc,'calllog'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($budget, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Budget</td>
                <?php echo tile_config_function($dbc,'budget'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($budget, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Goals & Objectives</td>
                <?php echo tile_config_function($dbc,'gao'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($infogathering, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Information Gathering</td>
                <?php echo tile_config_function($dbc,'infogathering'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($marketing_material, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Marketing Materials</td>
                <?php echo tile_config_function($dbc,'marketing_material'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($sales, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Sales</td>
                <?php echo tile_config_function($dbc,'sales'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($sales_order, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= SALES_ORDER_TILE ?></td>
                <?php echo tile_config_function($dbc,'sales_order'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Inventory Management:</div></th></tr>
			<?php if (strpos($assets, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Assets</td>
                <?php echo tile_config_function($dbc,'assets'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($equipment, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Equipment</td>
                <?php echo tile_config_function($dbc,'equipment'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($inventory, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Inventory</td>
                <?php echo tile_config_function($dbc,'inventory'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($material, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Materials</td>
                <?php echo tile_config_function($dbc,'material'); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Collaborative:</div></th></tr>

			<?php if (strpos($communication, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Communication</td>
                <?php echo tile_config_function($dbc,'communication'); ?>
            </tr>
            <?php } ?>

			<?php if (strpos($email_communication, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Email Communication</td>
                <?php echo tile_config_function($dbc,'email_communication'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($newsboard, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">News Board</td>
                <?php echo tile_config_function($dbc,'newsboard'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($scrum, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Scrum</td>
                <?php echo tile_config_function($dbc,'scrum'); ?>
            </tr>
            <?php } ?>

			<?php if (strpos($tasks, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= TASK_TILE ?></td>
                <?php echo tile_config_function($dbc,'tasks'); ?>
            </tr>
            <?php } ?>

<!--
			<?php if (strpos($tasks_updated, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Tasks (Updated)</td>
                <?php echo tile_config_function($dbc,'tasks_updated'); ?>
            </tr>
            <?php } ?>
-->
            <tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'><?= ESTIMATE_TILE ?>/Quotes:</div></th></tr>
			<?php if (strpos($estimate, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= ESTIMATE_TILE ?></td>
                <?php echo tile_config_function($dbc,'estimate'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($quote, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Quotes</td>
                <?php echo tile_config_function($dbc,'quote'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($cost_estimate, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Cost Estimates</td>
                <?php echo tile_config_function($dbc,'cost_estimate'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($field_ticket_estimates, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Field Ticket Estimates</td>
                <?php echo tile_config_function($dbc,'field_ticket_estimates'); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Safety:</div></th></tr>
			<?php if (strpos($driving_log, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Driving Log</td>
                <?php echo tile_config_function($dbc,'driving_log'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($safety, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Safety</td>
                <?php echo tile_config_function($dbc,'safety'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Project Add Ons:</div></th></tr>
			<?php if (strpos($addendum, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Addendum</td>
                <?php echo tile_config_function($dbc,'addendum'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($addition, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Addition</td>
                <?php echo tile_config_function($dbc,'addition'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Operations:</div></th></tr>
			<?php if (strpos($field_job, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Field Jobs</td>
                <?php echo tile_config_function($dbc,'field_job'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($project, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?></td>
                <?php echo tile_config_function($dbc,'project'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($project_workflow, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Project Workflow</td>
                <?php echo tile_config_function($dbc,'project_workflow'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($shop_work_orders, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Shop Work Orders</td>
                <?php echo tile_config_function($dbc,'shop_work_orders'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Project/Job Details:</div></th></tr>
			<?php if (strpos($custom, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Custom</td>
                <?php echo tile_config_function($dbc,'custom'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($labour, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Labour</td>
                <?php echo tile_config_function($dbc,'labour'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($package, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Packages</td>
                <?php echo tile_config_function($dbc,'package'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($products, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Products</td>
                <?php echo tile_config_function($dbc,'products'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($promotion, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Promotions</td>
                <?php echo tile_config_function($dbc,'promotion'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($rate_card, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Rate Cards</td>
                <?php echo tile_config_function($dbc,'rate_card'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($services, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Services</td>
                <?php echo tile_config_function($dbc,'services'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Project/Job Type:</div></th></tr>
			<?php if (strpos($assembly, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Assembly</td>
                <?php echo tile_config_function($dbc,'assembly'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($business_development, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Business Development</td>
                <?php echo tile_config_function($dbc,'business_development'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($internal, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Internal</td>
                <?php echo tile_config_function($dbc,'internal'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($manufacturing, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Manufacturing</td>
                <?php echo tile_config_function($dbc,'manufacturing'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($marketing, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Marketing</td>
                 <?php echo tile_config_function($dbc,'marketing'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($process_development, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Process Development</td>
                <?php echo tile_config_function($dbc,'process_development'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($rd, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">R&D</td>
                <?php echo tile_config_function($dbc,'rd'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($sred, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">SR&ED</td>
                <?php echo tile_config_function($dbc,'sred'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Project/Job Tracking:</div></th></tr>
			<?php if (strpos($daysheet, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Day Sheet</td>
                <?php echo tile_config_function($dbc,'daysheet'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($ticket, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= TICKET_TILE ?></td>
                <?php echo tile_config_function($dbc,'ticket'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($punch_card, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Time Clock</td>
                <?php echo tile_config_function($dbc,'punch_card'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($timesheet, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Timesheet</td>
                <?php echo tile_config_function($dbc,'timesheet'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($time_tracking, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Time Tracking</td>
                <?php echo tile_config_function($dbc,'time_tracking'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($work_order, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Work Orders</td>
                <?php echo tile_config_function($dbc,'work_order'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Accounting:</div></th></tr>
			<?php if (strpos($expense, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Expenses</td>
                 <?php echo tile_config_function($dbc,'expense'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($pos, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= POS_ADVANCE_TILE ?></td>
                <?php echo tile_config_function($dbc,'pos'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($purchase_order, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Purchase Orders</td>
                <?php echo tile_config_function($dbc,'purchase_order'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($vpl, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Vendor Price List</td>
                <?php echo tile_config_function($dbc,'vpl'); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Reporting:</div></th></tr>
			<?php if (strpos($gantt_chart, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Gantt Chart</td>
                 <?php echo tile_config_function($dbc,'gantt_chart'); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($report, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Reports</td>
                <?php echo tile_config_function($dbc,'report'); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='5'><div style='text-align:left;width:100%;font-size:20px;'>Medical:</div></th></tr>

			<?php if (strpos($charts, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Charts</td>
                <?php echo tile_config_function($dbc,'charts'); ?>
            </tr>
            <?php } ?>

            <?php if (strpos($daily_log_notes, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Daily Log Notes">Daily Log Notes</td>
                <?php echo tile_config_function($dbc,'daily_log_notes'); ?>
            </tr>
            <?php } ?>

			<?php if (strpos($routine, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Routine</td>
                <?php echo tile_config_function($dbc,'routine'); ?>
            </tr>
            <?php } ?>

			<?php if (strpos($day_program, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Day Program</td>
                <?php echo tile_config_function($dbc,'day_program'); ?>
            </tr>
            <?php } ?>

			<?php if (strpos($match, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Match</td>
                <?php echo tile_config_function($dbc,'match'); ?>
            </tr>
            <?php } ?>

			<?php if (strpos($fund_development, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Fund Development</td>
                <?php echo tile_config_function($dbc,'fund_development'); ?>
            </tr>
            <?php } ?>

			<?php if (strpos($medication, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Medication</td>
                <?php echo tile_config_function($dbc,'medication'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($individual_support_plan, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Individual Service Plan</td>
                <?php echo tile_config_function($dbc,'individual_support_plan'); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($social_story, 'turn_on') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Social Story</td>
                <?php echo tile_config_function($dbc,'social_story'); ?>
            </tr>
            <?php } ?>

        </table>
<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>
		</div>
        </div>
    </div>
</div>
<?php include ('footer.php'); ?>