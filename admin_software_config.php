<?php
/*
Customer Listing
*/
error_reporting(0);
include ('include.php');

	// Kick off from the page if you are not an Admin or Super Admin
	$contacterid = $_SESSION['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contacterid'");
	while($row = mysqli_fetch_assoc($result)) {
		$role = $row['role'];
		$level_url = $role;
	}
	if(stripos(','.$role.',',',super,') === false && stripos(','.$role.',',',admin,') === false) {
		header('location: home.php');
		die();
	}

if (isset($_POST['add_staff_email'])) {
	$staff_email_field = filter_var($_POST['staff_email_field'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_email_field', $staff_email_field);
}
if (isset($_POST['add_general'])) {

    $company_name = filter_var($_POST['company_name'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='company_name'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$company_name' WHERE name='company_name'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('company_name', '$company_name')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}
if (isset($_POST['add_social_media'])) {

    $facebook_link = filter_var($_POST['facebook'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='facebook_link'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$facebook_link' WHERE name='facebook_link'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('facebook_link', '$facebook_link')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$google_link = filter_var($_POST['googleplus'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='google_link'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$google_link' WHERE name='google_link'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('google_link', '$google_link')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$twitter_link = filter_var($_POST['twitter'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='twitter_link'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$twitter_link' WHERE name='twitter_link'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('twitter_link', '$twitter_link')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$linkedin_link = filter_var($_POST['linkedin'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='linkedin_link'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$linkedin_link' WHERE name='linkedin_link'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('linkedin_link', '$linkedin_link')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}
if (isset($_POST['add_style'])) {
       //Task Status
	$loginstyle = $_POST['loginstyle'];
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='login_style'"));
	if($get_config['configid'] > 0) {
		$query_update_employee = "UPDATE `general_configuration` SET value = '$loginstyle' WHERE name='login_style'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('login_style', '$loginstyle')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

    echo '<script type="text/javascript"> window.location.replace("admin_software_config.php?software_style"); </script>';
}
if (isset($_POST['add_default_login'])) {

    $default_login = filter_var($_POST['default_login'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='default_login'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$default_login' WHERE name='default_login'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('default_login', '$default_login')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

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
			url: "ajax_all.php?fill=admin_tile_config&name="+name+"&value="+final_value+"&turnoff="+turnoff+"&turnOn="+turnOn,
			dataType: "html",   //expect html to be returned
			success: function(response) {
				response = response.split('#*#');
				//console.log(response[0]);
				$(sel).closest('tr').find('td[data-title=Status]').html(response[1]);
				if(response[2] == '1') {
					$(sel).closest('tr').find('input[value=turn_on]').attr('checked','checked');
				} else {
					$(sel).closest('tr').find('input[value=turn_off]').attr('checked','checked');
				}
			}
		});
    }
$(document).ready(function() {
	$('.live-search-box2').focus();
    $('.live-search-list2 tr').each(function(){
        var text = $(this).text() + ' ' + $(this).prevAll().andSelf().find('th').last().text();
        text = text.replace(/ Show in Security Tile/g, '');
        searchtext = $(this).find("td:first-child").text();
        $(this).attr('data-search-term', searchtext.toLowerCase());
    });

    $('.live-search-box2').on('keyup', function(){
        var searchTerm = $(this).val().toLowerCase();

        $('.live-search-list2 tr').each(function(){
            if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
                $(this).show();
                if (!$(this).hasClass('dont-hide')) {
                    $(this).attr("class","search-found");
                }
            } else if(!$(this).hasClass('dont-hide')) {
                $(this).hide();
                $(this).attr("class","search-not-found");
            }
        });

        $('div .panel-default').each(function(){
            if ($(this).find('.search-found').length > 0) {
                $(this).show();
            } else {
                $(this).hide();
            }
        })
    });

	$('.iframe_open').click(function(){
			var tile = $(this).data('option');
			var title = $(this).parents('tr').children(':first').text();
		   $('#iframe_instead_of_window').attr('src', 'tile_history.php?user=admin&tile_name='+tile+'&title='+title);
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

	$('[name="tile_enable_section[]"]').change(function() {
		var list = '';
		$('[name="tile_enable_section[]"]:checked').each(function() {
			list = (list == '' ? '' : list + ',') + $(this).val();
		});
		$.ajax({
			type: "POST",
			url: "ajax_all.php?fill=tile_enable_section",
			data: { value: list },
			dataType: "html",   //expect html to be returned
			success: function(response) {
				console.log(response);
			}
		});
	});
});
</script>
</head>
<body>
<?php include_once ('navigation.php');
checkAuthorised();
?>

<div class="container">
	<div class='iframe_holder' style='display:none;'>
		<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
    </div>
    <div class="row hide_on_iframe">
		<div class="col-md-12">
		<div><?php
		$active_tab_email = '';
		$active_tab_config = '';
		$active_tab_sync = '';
        $active_tab_reset_demo = '';
		$active_tab_initiate = '';
		$active_tab_set = '';
		$active_tab = '';
		$active_tab_style = '';
		$active_tab_sm = '';
		$active_tab_fav = '';
		$active_tab_login = '';
		$active_tab_sync_inventory = '';
		$title = '';
		if(isset($_GET['email_configuration'])) {
			$active_tab_email = 'active_tab';
			$title = 'Staff Email Configuration';
		} else if(isset($_GET['config_differences'])) {
			$active_tab_config = 'active_tab';
			$title = 'Live vs Demo Configurations';
		} else if(isset($_GET['data_sync'])) {
			$active_tab_sync = 'active_tab';
			$title = 'Live to Demo Data Sync';
		} else if(isset($_GET['reset_demo'])) {
			$active_tab_reset_demo = 'active_tab';
			$title = 'Reset Demo To Live';
		} else if(isset($_GET['initiate_software'])) {
			$active_tab_initiate = 'active_tab';
			$title = 'Initiate Software Pack';
		} else if(isset($_GET['software_settings'])) {
			$active_tab_set = 'active_tab';
			$title = 'Software Identity Settings';
		} else if(isset($_GET['software_style'])) {
			$active_tab_style = 'active_tab';
			$title = 'Login Style Settings';
		} else if(isset($_GET['software_social_media'])) {
			$active_tab_sm = 'active_tab';
			$title = 'Social Media Settings';
		} else if(isset($_GET['favicon'])) {
			$active_tab_fav = 'active_tab';
			$title = 'Favicon Settings';
		} else if(isset($_GET['login_page'])) {
			$active_tab_login = 'active_tab';
			$title = 'Default Login Page';
		} else if(isset($_GET['sync_inventory'])) {
			$active_tab_sync_inventory = 'active_tab';
			$title = 'Sync Inventory Between Software';
		} else {
			$active_tab = 'active_tab';
			$title = 'Software Functionality Settings';
		}

		if($title !== '') {
			echo "<h1 name=pageName>$title</h1>";
		} ?>

		<div class="double-gap-top tab-container mobile-100-container triple-gap-bottom"><?php
			if(stripos(','.$role.',',',super,') !== false) {
				echo '
					<div class="pull-left tab">
						<span class="popover-examples no-gap-pad">
							<a data-toggle="tooltip" data-placement="top" title="This sets tile abilities for every user."><img src="img/info.png" width="20"></a>
						</span>
						<a href="admin_software_config.php"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab . '">Software Functionality</button></a>
					</div>';
			}

			echo '
				<div class="pull-left tab">
					<span class="popover-examples no-gap-pad">
						<a data-toggle="tooltip" data-placement="top" title="Select and initialize the software formula you wish to utilize for your business."><img src="img/info.png" width="20"></a>
					</span>
					<a href="admin_software_config.php?initiate_software"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_initiate . '">Initiate Software Pack</button></a>
				</div>';

			echo '
				<div class="pull-left tab">
					<span class="popover-examples no-gap-pad">
						<a data-toggle="tooltip" data-placement="top" title="Add your company\'s name to the software."><img src="img/info.png" width="20"></a>
					</span>
					<a href="admin_software_config.php?software_settings"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_set . '">Software Identity</button></a>
				</div>';

			echo '
				<div class="pull-left tab">
					<span class="popover-examples no-gap-pad">
						<a data-toggle="tooltip" data-placement="top" title="Set your login style."><img src="img/info.png" width="20"></a>
					</span>
					<a href="admin_software_config.php?software_style"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_style . '">Login Style</button></a>
				</div>';

			echo '
				<div class="pull-left tab">
					<span class="popover-examples no-gap-pad">
						<a data-toggle="tooltip" data-placement="top" title="Link your company\'s social media channels."><img src="img/info.png" width="20"></a>
					</span>
					<a href="admin_software_config.php?software_social_media"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_sm . '">Social Media</button></a>
				</div>';

			echo '
				<div class="pull-left tab">
					<span class="popover-examples no-gap-pad">
						<a data-toggle="tooltip" data-placement="top" title="The small image that appears beside the text in the current tab."><img src="img/info.png" width="20"></a>
					</span>
					<a href="admin_software_config.php?favicon"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_fav . '">Favicon</button></a>
				</div>';

			echo '
				<div class="pull-left tab">
					<span class="popover-examples no-gap-pad">
						<a data-toggle="tooltip" data-placement="top" title="Change your software\'s default login page."><img src="img/info.png" width="20"></a>
					</span>
					<a href="admin_software_config.php?login_page"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_login . '">Default Login Page</button></a>
				</div>';

			echo '
				<div class="pull-left tab">
					<span class="popover-examples no-gap-pad">
						<a data-toggle="tooltip" data-placement="top" title="Configure which email to use when emailing Staff from the software."><img src="img/info.png" width="20"></a>
					</span>
					<a href="admin_software_config.php?email_configuration"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_email . '">Staff Email Configuration</button></a>
				</div>';

			if(stripos(','.$role.',',',super,') !== false) {
				echo '
					<div class="pull-left tab">
						<span class="popover-examples no-gap-pad">
							<a data-toggle="tooltip" data-placement="top" title="View your software\'s Live Configurations vs Demo Configurations."><img src="img/info.png" width="20"></a>
						</span>
						<a href="admin_software_config.php?config_differences"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_config . '">Live vs Demo Configurations</button></a>
					</div>';

				echo '
					<div class="pull-left tab">
						<span class="popover-examples no-gap-pad">
							<a data-toggle="tooltip" data-placement="top" title="View your software\'s Live Configurations vs Demo Configurations."><img src="img/info.png" width="20"></a>
						</span>
						<a href="admin_software_config.php?data_sync"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_sync . '">Live to Demo Data Sync</button></a>
					</div>';

				echo '
					<div class="pull-left tab">
						<span class="popover-examples no-gap-pad">
							<a data-toggle="tooltip" data-placement="top" title="Reset your Demo software\'s data and configuration to a copy of your Live software."><img src="img/info.png" width="20"></a>
						</span>
						<a href="admin_software_config.php?reset_demo"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_reset_demo . '">Reset Demo To Live</button></a>
					</div>';

				echo '
					<div class="pull-left tab">
						<span class="popover-examples no-gap-pad">
							<a data-toggle="tooltip" data-placement="top" title="Sync Inventory between different software so the Inventory on these software are in sync."><img src="img/info.png" width="20"></a>
						</span>
						<a href="admin_software_config.php?sync_inventory"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_tab_sync_inventory . '">Sync Inventory</button></a>
					</div>';
			} ?>

			<div class="clearfix"></div>

		</div>

	</div>
		<?php if (empty($_GET)) {
			if(stripos(','.$role.',',',super,') === false) {
				header('location: admin_software_config.php?software_settings');
				die();
			}   ?>

		<div id="">
        <?php
        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM admin_tile_config"));

        $software_config = $get_config['software_config'];
        $profile = $get_config['profile'];
        $security = $get_config['security'];
        $contacts = $get_config['contacts'];
        $contacts3 = $get_config['contacts3'];
        $contacts_rolodex = $get_config['contacts_rolodex'];
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
        $invoicing = $get_config['invoicing'];
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
        $report = $get_config['report'];
        $field_ticket_estimates = $get_config['field_ticket_estimates'];
        $driving_log = $get_config['driving_log'];
        $expense = $get_config['expense'];
        $payables = $get_config['payables'];
        $billing = $get_config['billing'];
        $marketing = $get_config['marketing'];
		$manual = $get_config['manual'];

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
        $payroll = $get_config['payroll'];

        $certificate = $get_config['certificate'];
        $marketing_material = $get_config['marketing_material'];
        $internal_documents = $get_config['internal_documents'];
        $client_documents = $get_config['client_documents'];
        $contracts = $get_config['contracts'];
        $products = $get_config['products'];
        $tasks = $get_config['tasks'];
        $tasks_updated = $get_config['tasks_updated'];
        $agenda_meeting = $get_config['agenda_meeting'];
        $sales = $get_config['sales'];
        $gantt_chart = $get_config['gantt_chart'];
        $communication = $get_config['communication'];
        $communication_schedule = $get_config['communication_schedule'];
        $purchase_order = $get_config['purchase_order'];
        $orientation = $get_config['orientation'];
		$sales_order = $get_config['sales_order'];
		$vpl = $get_config['vpl'];
        $helpdesk = $get_config['helpdesk'];
        $time_tracking = $get_config['time_tracking'];
        $newsboard = $get_config['newsboard'];
		$calendar_rook = $get_config['calendar_rook'];
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
		$profit_loss = $get_config['profit_loss'];
		$gao = $get_config['gao'];

		// Clinic Ace
		$appointment_calendar = $get_config['appointment_calendar'];
		$booking = $get_config['booking'];
		$check_in = $get_config['check_in'];
		$reactivation = $get_config['reactivation'];
		$check_out = $get_config['check_out'];
		$treatment_charts = $get_config['treatment_charts'];
		$accounts_receivables = $get_config['accounts_receivables'];
		$therapist = $get_config['therapist'];
		$treatment = $get_config['treatment'];
		$exercise_library = $get_config['exercise_library'];
		$confirmation = $get_config['confirmation'];
		$confirm = $get_config['confirm'];
		$goals_compensation = $get_config['goals_compensation'];
		$crm = $get_config['crm'];
		$policies = $get_config['policies'];
		$employee_handbook = $get_config['employee_handbook'];

        $routine = $get_config['routine'];
        $day_program = $get_config['day_program'];
        $match = $get_config['match'];
        $fund_development = $get_config['fund_development'];

        $client_documentation = $get_config['client_documentation'];
        $medication = $get_config['medication'];
        $individual_support_plan = $get_config['individual_support_plan'];
        $social_story = $get_config['social_story'];

		$intake = $get_config['intake'];
        $how_to_checklist = $get_config['how_to_checklist'];
        $drop_off_analysis = $get_config['drop_off_analysis'];
        $injury = $get_config['injury'];
		$jobs = $get_config['jobs'];
        $interactive_calendar = $get_config['interactive_calendar'];
        $properties = $get_config['properties'];
		$client_projects = $get_config['client_projects'];
		$preformance_review = $get_config['preformance_review'];
		$training_quiz = $get_config['training_quiz'];

		$website = $get_config['website'];

        $section_display = ','.get_config($dbc, 'tile_enable_section').',';
        ?>
		<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="img/info.png" class="wiggle-me" width="25px"></div>
			<div class="col-sm-16"><span class="notice-name">NOTE:</span> Please note, turning on/off from the table below will turn on/off functionality for all accounts accessing the software. If you would like to enable/disable access to Tiles for specific users or user groups, please go to the "Set Security Privileges" section in the Software Security tile.</div>
		</div>
        <center><input type='text' name='x' class=' form-control live-search-box2' placeholder='Search for a tile...' style='max-width:300px; margin-bottom:20px;'></center>
		<!-- Added in each accordion

		<table class='table table-bordered live-search-list2'>
            <tr class='hidden-sm dont-hide'>
                <th>Available Software Tiles &amp; Functionality</th>
                <th><span class="popover-examples list-inline">&nbsp;
					<a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
					</span>Turn Tile On</th>
                <th><span class="popover-examples list-inline">&nbsp;
					<a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
					</span>Turn Tile Off</th>
                <th>History</th>
                <th>Function Status</th>
            </tr>
		</table> -->

			<!-- Software Settings -->
			<div class="panel-group" id="accordion2">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field1" >
								Software Settings
							</a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="software_settings" <?= (strpos($section_display, ',software_settings,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
						</h4>
					</div>

					<div id="collapse_field1" class="panel-collapse collapse in">
						<div class="panel-body">
							<div id='no-more-tables'>
								<table border='2' cellpadding='10' class='table live-search-list2'>
									<tr class='hidden-sm dont-hide'>
										<th>Available Software Tiles &amp; Functionality</th>
										<th><span class="popover-examples list-inline">&nbsp;
											<a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
											</span>Turn Tile On</th>
										<th><span class="popover-examples list-inline">&nbsp;
											<a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
											</span>Turn Tile Off</th>
										<th>History</th>
										<th>Function Status</th>
									</tr>
									<tr>
										<td data-title="Comment">Archived Data</td>
										<?php echo tile_config_function($dbc, 'archiveddata', 'admin'); ?>
									</tr>
									<tr>
										<td data-title="Comment">Customer Support</td>
										<?php echo tile_config_function($dbc, 'customer_support', 'admin'); ?>
									</tr>
                                    <tr>
										<td data-title="Comment">Demo</td>
										<?php echo tile_config_function($dbc, 'demo', 'admin'); ?>
									</tr>
									<tr>
										<td data-title="Comment">FFM Support</td>
										<?php echo tile_config_function($dbc, 'ffmsupport', 'admin'); ?>
									</tr>
									<tr>
										<td data-title="Comment">Passwords</td>
										<?php echo tile_config_function($dbc, 'passwords', 'admin'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Comment">Profile</td>
                                        <?php echo tile_config_function($dbc, 'profile', 'admin'); ?>
                                    </tr>
									<tr>
										<td data-title="Comment">Security</td>
										<?php echo tile_config_function($dbc, 'security', 'admin'); ?>
									</tr>
									<tr>
										<td data-title="Comment">Software Settings</td>
										<?php echo tile_config_function($dbc, 'software_config', 'admin'); ?>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
            <!-- Human Resources -->
            <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field2" >
                                Human Resources
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="human_resources" <?= (strpos($section_display, ',human_resources,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field2" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">All Software Guide</td>
                                        <?php echo tile_config_function($dbc, 'how_to_guide', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Certificates</td>
                                        <?php echo tile_config_function($dbc, 'certificate', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Employee Handbook</td>
                                        <?php echo tile_config_function($dbc, 'emp_handbook', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Goals & Objectives</td>
                                        <?php echo tile_config_function($dbc, 'gao', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">How To Checklist</td>
                                        <?php echo tile_config_function($dbc, 'how_to_checklist', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">HR</td>
                                        <?php echo tile_config_function($dbc, 'hr', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Manuals</td>
                                        <?php echo tile_config_function($dbc, 'manual', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Operations Manual</td>
                                        <?php echo tile_config_function($dbc, 'ops_manual', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Orientation</td>
                                        <?php echo tile_config_function($dbc, 'orientation', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Performance Reviews</td>
                                        <?php echo tile_config_function($dbc, 'preformance_review', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Policies & Procedures</td>
                                        <?php echo tile_config_function($dbc, 'policy_procedure', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Safety Manual</td>
                                        <?php echo tile_config_function($dbc, 'safety_manual', 'admin'); ?>
                                    </tr>
                                        <td data-title="Comment">Software Guide</td>
                                        <?php echo tile_config_function($dbc, 'software_guide', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Staff</td>
                                        <?php echo tile_config_function($dbc, 'staff', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Training & Quizzes</td>
                                        <?php echo tile_config_function($dbc, 'training_quiz', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
				<!-- Profiles -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field3" >
								Profiles
							</a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="profiles" <?= (strpos($section_display, ',profiles,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
						</h4>
					</div>

					<div id="collapse_field3" class="panel-collapse collapse">
						<div class="panel-body">
							<div id='no-more-tables'>
								<table border='2' cellpadding='10' class='table live-search-list2'>
									<tr class='hidden-sm dont-hide'>
										<th>Available Software Tiles &amp; Functionality</th>
										<th><span class="popover-examples list-inline">&nbsp;
											<a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
											</span>Turn Tile On</th>
										<th><span class="popover-examples list-inline">&nbsp;
											<a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
											</span>Turn Tile Off</th>
										<th>History</th>
										<th>Function Status</th>
									</tr>
									<tr>
						                <td data-title="Comment">Client Information</td>
						                <?php echo tile_config_function($dbc, 'client_info', 'admin'); ?>
						            </tr>
									<tr>
										<td data-title="Comment">Contacts</td>
										<?php echo tile_config_function($dbc, 'contacts', 'admin'); ?>
									</tr>
									<tr>
										<td data-title="Comment">Contacts (Updated)</td>
										<?php echo tile_config_function($dbc, 'contacts_inbox', 'admin'); ?>
									</tr>

									<tr>
										<td data-title="Comment">Contacts 3</td>
										<?php echo tile_config_function($dbc, 'contacts3', 'admin'); ?>
									</tr>
									<tr>
										<td data-title="Comment">Contacts Rolodex</td>
										<?php echo tile_config_function($dbc, 'contacts_rolodex', 'admin'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Fund Development</td>
                                        <?php echo tile_config_function($dbc, 'fund_development', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Members">Members</td>
                                        <?php echo tile_config_function($dbc, 'members', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="PT Day Sheet">PT Day Sheet</td>
                                        <?php echo tile_config_function($dbc, 'therapist', 'admin'); ?>
                                    </tr>
								</table>
							</div>
						</div>
					</div>
				</div>
                <!-- Accounting -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field4" >
                                Accounting
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="accounting" <?= (strpos($section_display, ',accounting,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field4" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Accounts Receivable">Accounts Receivable</td>
                                        <?php echo tile_config_function($dbc, 'accounts_receivables', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Budget</td>
                                        <?php echo tile_config_function($dbc, 'budget', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Expenses</td>
                                        <?php echo tile_config_function($dbc, 'expense', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Goals & Objectives">Goals & Compensation</td>
                                        <?php echo tile_config_function($dbc, 'goals_compensation', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Payables</td>
                                        <?php echo tile_config_function($dbc, 'payables', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Payroll</td>
                                        <?php echo tile_config_function($dbc, 'payroll', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Profit & Loss</td>
                                        <?php echo tile_config_function($dbc, 'profit_loss', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Project Billing & Invoices</td>
                                        <?php echo tile_config_function($dbc, 'billing', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Purchase Orders</td>
                                        <?php echo tile_config_function($dbc, 'purchase_order', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Reports</td>
                                        <?php echo tile_config_function($dbc, 'report', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Vendor Price List</td>
                                        <?php echo tile_config_function($dbc, 'vpl', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Time Tracking -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field5" >
                                Time Tracking
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="time_tracking" <?= (strpos($section_display, ',time_tracking,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field5" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Day Sheet">Planner</td>
                                        <?php echo tile_config_function($dbc, 'daysheet', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Sign In</td>
                                        <?php echo tile_config_function($dbc, 'sign_in_time', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?= START_DAY ?></td>
                                        <?php echo tile_config_function($dbc, 'start_day_button', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Time Clock</td>
                                        <?php echo tile_config_function($dbc, 'punch_card', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Timesheet">Time Sheets</td>
                                        <?php echo tile_config_function($dbc, 'timesheet', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Time Tracking</td>
                                        <?php echo tile_config_function($dbc, 'time_tracking', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Inventory Management -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field6" >
                                Inventory Management
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="inventory_management" <?= (strpos($section_display, ',inventory_management,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field6" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Assets</td>
                                        <?php echo tile_config_function($dbc, 'assets', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Inventory</td>
                                        <?php echo tile_config_function($dbc, 'inventory', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Materials</td>
                                        <?php echo tile_config_function($dbc, 'material', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?= VENDOR_TILE ?></td>
                                        <?php echo tile_config_function($dbc, 'vendors', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Inventory Management -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field7" >
                                Equipment
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="equipment" <?= (strpos($section_display, ',equipment,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field7" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Equipment</td>
                                        <?php echo tile_config_function($dbc, 'equipment', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Collaborative -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field8" >
                                Collaborative Workflow
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="collaborative_workflow" <?= (strpos($section_display, ',collaborative_workflow,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field8" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Agendas & Meetings</td>
                                        <?php echo tile_config_function($dbc, 'agenda_meeting', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Appointment Calendar">Appointment Calendar</td>
                                        <?php echo tile_config_function($dbc, 'appointment_calendar', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Booking">Booking</td>
                                        <?php echo tile_config_function($dbc, 'booking', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Calendar</td>
                                        <?php echo tile_config_function($dbc, 'calendar_rook', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Checklists</td>
                                        <?php echo tile_config_function($dbc, 'checklist', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Interactive Calendar</td>
                                        <?php echo tile_config_function($dbc, 'interactive_calendar', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">News Board</td>
                                        <?php echo tile_config_function($dbc, 'newsboard', 'admin'); ?>
                                    </tr>

                                    <tr>
                                        <td data-title="Comment">Tasks</td>
                                        <?php echo tile_config_function($dbc, 'tasks', 'admin'); ?>
                                    </tr>

                                    <!--<tr>
                                        <td data-title="Comment">Tasks (Updated)</td>
                                        <?php echo tile_config_function($dbc, 'tasks_updated', 'admin'); ?>
                                    </tr>
                                    -->
                                    <tr>
                                        <td data-title="Comment">Trip Optimizer</td>
                                        <?php echo tile_config_function($dbc, 'optimize', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Digital Forms -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field9" >
                                Digital Forms
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="digital_forms" <?= (strpos($section_display, ',digital_forms,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field9" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Client Documentation</td>
                                        <?php echo tile_config_function($dbc, 'client_documentation', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Contracts</td>
                                        <?php echo tile_config_function($dbc, 'contracts', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Daily Log Notes</td>
                                        <?php echo tile_config_function($dbc, 'daily_log_notes', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Day Program</td>
                                        <?php echo tile_config_function($dbc, 'day_program', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Documents">Documents</td>
                                        <?php echo tile_config_function($dbc, 'documents_all', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Exercise Library">Exercise Library</td>
                                        <?php echo tile_config_function($dbc, 'exercise_library', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Exercise Library">Form Builder</td>
                                        <?php echo tile_config_function($dbc, 'form_builder', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Individual Service Plan (ISP)</td>
                                        <?php echo tile_config_function($dbc, 'individual_support_plan', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Charts">Charts</td>
                                        <?php echo tile_config_function($dbc, 'charts', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Medications</td>
                                        <?php echo tile_config_function($dbc, 'medication', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Routine Creator</td>
                                        <?php echo tile_config_function($dbc, 'routine', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Social Story</td>
                                        <?php echo tile_config_function($dbc, 'social_story', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Treatment Charts">Treatment Charts</td>
                                        <?php echo tile_config_function($dbc, 'treatment_charts', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Estimates / Quotes -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field10" >
                                <?= ESTIMATE_TILE ?> / Quoting
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="estimates" <?= (strpos($section_display, ',estimates,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field10" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Cost Estimates</td>
                                        <?php echo tile_config_function($dbc, 'cost_estimate', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?= ESTIMATE_TILE ?></td>
                                        <?php echo tile_config_function($dbc, 'estimate', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Field Ticket Estimates</td>
                                        <?php echo tile_config_function($dbc, 'field_ticket_estimates', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Quotes</td>
                                        <?php echo tile_config_function($dbc, 'quote', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
				<!-- Sales -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field11" >
								Sales
							</a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="sales" <?= (strpos($section_display, ',sales,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
						</h4>
					</div>

					<div id="collapse_field11" class="panel-collapse collapse">
						<div class="panel-body">
							<div id='no-more-tables'>
								<table border='2' cellpadding='10' class='table live-search-list2'>
									<tr class='hidden-sm dont-hide'>
										<th>Available Software Tiles &amp; Functionality</th>
										<th><span class="popover-examples list-inline">&nbsp;
											<a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
											</span>Turn Tile On</th>
										<th><span class="popover-examples list-inline">&nbsp;
											<a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
											</span>Turn Tile Off</th>
										<th>History</th>
										<th>Function Status</th>
									</tr>
									<tr>
										<td data-title="Comment">Cold Call</td>
										<?php echo tile_config_function($dbc, 'calllog', 'admin'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Drop Off Analysis">Drop Off Analysis</td>
                                        <?php echo tile_config_function($dbc, 'drop_off_analysis', 'admin'); ?>
                                    </tr>
									<tr>
										<td data-title="Comment">Information Gathering</td>
										<?php echo tile_config_function($dbc, 'infogathering', 'admin'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Comment">Intake Form</td>
                                        <?php echo tile_config_function($dbc, 'intake', 'admin'); ?>
                                    </tr>
									<tr>
										<td data-title="Comment">Sales</td>
										<?php echo tile_config_function($dbc, 'sales', 'admin'); ?>
									</tr>
									<tr>
										<td data-title="Comment"><?= SALES_ORDER_TILE ?></td>
										<?php echo tile_config_function($dbc, 'sales_order', 'admin'); ?>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
                <!-- Project Management -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field12" >
                                <?php echo PROJECT_TILE; ?> Management
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="project_management" <?= (strpos($section_display, ',project_management,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field12" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Addendum <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'addendum', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Addition <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'addition', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Assembly <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'assembly', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Business Development <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'business_development', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Client Projects</td>
                                        <?php echo tile_config_function($dbc, 'client_projects', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Communication</td>
                                        <?php echo tile_config_function($dbc, 'communication_schedule', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Communication Tasks</td>
                                        <?php echo tile_config_function($dbc, 'communication', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Dispatch</td>
                                        <?php echo tile_config_function($dbc, 'dispatch', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Email Communication</td>
                                        <?php echo tile_config_function($dbc, 'email_communication', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Field Jobs</td>
                                        <?php echo tile_config_function($dbc, 'field_job', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Gantt Chart</td>
                                        <?php echo tile_config_function($dbc, 'gantt_chart', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Injury">Injury</td>
                                        <?php echo tile_config_function($dbc, 'injury', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Internal <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'internal', 'admin'); ?>
                                    </tr>
                                    <!--
                                    <tr>
                                        <td data-title="Comment">Jobs</td>
                                        <?php //echo tile_config_function($dbc, 'jobs', 'admin'); ?>
                                    </tr>
                                    -->
                                    <tr>
                                        <td data-title="Comment">Manufacturing <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'manufacturing', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Marketing <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'marketing', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Phone Communication</td>
                                        <?php echo tile_config_function($dbc, 'phone_communication', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Process Development <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'process_development', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Products</td>
                                        <?php echo tile_config_function($dbc, 'products', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?></td>
                                        <?php echo tile_config_function($dbc, 'project', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Project Workflow</td>
                                        <?php echo tile_config_function($dbc, 'project_workflow', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Properties</td>
                                        <?php echo tile_config_function($dbc, 'properties', $properties); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">R&D <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'rd', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Scrum</td>
                                        <?php echo tile_config_function($dbc, 'scrum', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Services</td>
                                        <?php echo tile_config_function($dbc, 'services', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Site Work Orders</td>
                                        <?php echo tile_config_function($dbc, 'site_work_orders', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Shop Work Orders</td>
                                        <?php echo tile_config_function($dbc, 'shop_work_orders', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">SR&ED <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'sred', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?= TICKET_TILE ?></td>
                                        <?php echo tile_config_function($dbc, 'ticket', 'admin'); ?>
                                    </tr>
                                    <!--
                                    <tr>
                                        <td data-title="Comment">Work Orders</td>
                                        <?php //echo tile_config_function($dbc, 'work_order', 'admin'); ?>
                                    </tr>
                                    -->
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
				<!-- Safety -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field13" >
								Safety
							</a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="safety" <?= (strpos($section_display, ',safety,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
						</h4>
					</div>

					<div id="collapse_field13" class="panel-collapse collapse">
						<div class="panel-body">
							<div id='no-more-tables'>
								<table border='2' cellpadding='10' class='table live-search-list2'>
									<tr class='hidden-sm dont-hide'>
										<th>Available Software Tiles &amp; Functionality</th>
										<th><span class="popover-examples list-inline">&nbsp;
											<a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
											</span>Turn Tile On</th>
										<th><span class="popover-examples list-inline">&nbsp;
											<a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
											</span>Turn Tile Off</th>
										<th>History</th>
										<th>Function Status</th>
									</tr>
									<tr>
										<td data-title="Comment">Driving Log</td>
										<?php echo tile_config_function($dbc, 'driving_log', 'admin'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Comment"><?= INC_REP_TILE ?></td>
                                        <?php echo tile_config_function($dbc, 'incident_report', 'admin'); ?>
                                    </tr>
									<tr>
										<td data-title="Comment">Safety</td>
										<?php echo tile_config_function($dbc, 'safety', 'admin'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Match</td>
                                        <?php echo tile_config_function($dbc, 'match', 'admin'); ?>
                                    </tr>
								</table>
							</div>
						</div>
					</div>
				</div>
                <!-- Point of Sale -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field14" >
                                Point of Sale
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="point_of_sale" <?= (strpos($section_display, ',point_of_sale,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field14" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Check In">Check In</td>
                                        <?php echo tile_config_function($dbc, 'check_in', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Check Out">Check Out</td>
                                        <?php echo tile_config_function($dbc, 'check_out', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Custom</td>
                                        <?php echo tile_config_function($dbc, 'custom', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Invoicing</td>
                                        <?php echo tile_config_function($dbc, 'invoicing', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Labour</td>
                                        <?php echo tile_config_function($dbc, 'labour', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Packages</td>
                                        <?php echo tile_config_function($dbc, 'package', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?= POS_ADVANCE_TILE ?></td>
                                        <?php echo tile_config_function($dbc, 'posadvanced', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Promotions & Coupons</td>
                                        <?php echo tile_config_function($dbc, 'promotion', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Rate Cards</td>
                                        <?php echo tile_config_function($dbc, 'rate_card', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Service Queue</td>
                                        <?php echo tile_config_function($dbc, 'service_queue', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Customer Relationship Management -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field15" >
                                Customer Relationship Management
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="crm" <?= (strpos($section_display, ',crm,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field15" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Confirmation">Notifications</td>
                                        <?php echo tile_config_function($dbc, 'confirmation', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Confirm">Confirmation</td>
                                        <?php echo tile_config_function($dbc, 'confirm', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="CRM">CRM</td>
                                        <?php echo tile_config_function($dbc, 'crm', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Reactivation">Follow Up</td>
                                        <?php echo tile_config_function($dbc, 'reactivation', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Helpdesk</td>
                                        <?php echo tile_config_function($dbc, 'helpdesk', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Website</td>
                                        <?php echo tile_config_function($dbc, 'website', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Analytics -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field16" >
                                Analytics
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="analytics" <?= (strpos($section_display, ',analytics,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field16" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Reports</td>
                                        <?php echo tile_config_function($dbc, 'report', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Communication -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field17" >
                                Communication
                            </a>
							<span style="font-size: 0.4em; margin: 0 3em;"><label><input type="checkbox" name="tile_enable_section[]" style="height:1.5em; width:1.5em;" value="communication" <?= (strpos($section_display, ',communication,') !== FALSE ? 'checked' : '') ?>> Show in Security Tile</label></span><span class="glyphicon glyphicon-plus"></span>
                        </h4>
                    </div>

                    <div id="collapse_field17" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Emoji Comm</td>
                                        <?php echo tile_config_function($dbc, 'non_verbal_communication', 'admin'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


		<!-- Initiate Software Pack -->
		<?php } else if ( isset ( $_GET['initiate_software'] ) ) { ?>
            <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Select and initialize the software formula you wish to utilize for your business. While all software functionality, features and headings are configurable throughout the settings of the software, the initial software pack you select will determine which preset settings you start your software with. You can only have one formula active at a time.</div>
            <div class="clearfix"></div>
            </div>

			<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="img/info.png" class="wiggle-me" width="25px"></div>
			<div class="col-sm-16"><span class="notice-name">ALERT:</span>
				Please be advised that changing your software pack will remove all current data and settings that you have configured. It's advised to use this feature only to start or reset your software, and access to this section should be limited.</div>
				<div class="clearfix"></div>
			</div>


		<!-- Software Identity Settings -->
		<?php } else if(isset($_GET['software_settings'])) { ?>
			<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="img/info.png" class="wiggle-me" width="25px"></div>
			<div class="col-sm-16"><span class="notice-name">NOTE:</span>
				Welcome to the first step in operational efficiency for your business! Many of our software functions provide the ability to allow third parties, clients, customers, contractors, etc. the power to collaborate with you in real time. All of these functions require that your software have its own personal identity. This identity will not be searchable online; but will be used as a unique ID when communicating with other individuals or software platforms.</div>
				<div class="clearfix"></div>
			</div>

			<form id="form1" name="form1" method="post"	action="admin_software_config.php?software_settings" enctype="multipart/form-data" class="form-horizontal" role="form">
				<div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">Your Company's Name:</label>
					<div class="col-sm-8">
					  <input name="company_name" placeholder="Fresh Focus Media Inc." type="text" value="<?php echo get_config($dbc, 'company_name'); ?>" class="form-control"/>
					</div>
				</div>
				<br><br>
				<div class="form-group">
					<!--<div class="col-sm-4 clearfix">
						<a href="admin_software_config.php" class="btn config-btn pull-right">Back</a>
						<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>--
					</div>-->
					<div class="col-sm-12">
						<button	type="submit" name="add_general" value="add_general" class="btn config-btn btn-lg pull-right">Submit</button>
					</div>
				</div>
			</form>


		<!-- Login Style Settings -->
		<?php } else if(isset($_GET['software_style'])) { ?>
			<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="img/info.png" class="wiggle-me" width="25px"></div>
			<div class="col-sm-16"><span class="notice-name">NOTE:</span>
				All software platforms are completely customizable, and your login screen is no exception. Please select from the drop down menu the styling you’d like to apply to your software platform.</div>
				<div class="clearfix"></div>
			</div>

			<form id="form1" name="form1" method="post"	action="admin_software_config.php?software_settings" enctype="multipart/form-data" class="form-horizontal" role="form">
				<div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">Login Page Style:</label>
					<div class="col-sm-8">
						<select data-placeholder="Choose style" name="loginstyle" class="chosen-select-deselect form-control inventoryid" width="380">
							<option value=''></option><?php

							//Get style
							$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='login_style'"));
							if($get_config['configid'] > 0) {
								$get_style = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM general_configuration WHERE name='login_style'"));
								$get_style_file = $get_style['value'];
							} else {
								$get_style_file = '';
							} ?>

							<option <?php if($get_style_file == '') { echo "selected"; } ?> value="">Default</option>
							<option <?php if($get_style_file == 'btb') { echo "selected"; } ?> value="btb">Break the Barrier</option>
							<option <?php if($get_style_file == 'washt') { echo "selected"; } ?> value="washt">Black</option>
							<option <?php if($get_style_file == 'blackorange') { echo "selected"; } ?> value="blackorange">Black & Orange</option>
							<option <?php if($get_style_file == 'blackpurple') { echo "selected"; } ?> value="blackpurple">Black & Purple</option>
							<option <?php if($get_style_file == 'blackred') { echo "selected"; } ?> value="blackpurple">Black & Red</option>
							<option <?php if($get_style_file == 'blackneon') { echo "selected"; } ?> value="blackneon">Black Neon (Blue)</option>
							<option <?php if($get_style_file == 'blackneonred') { echo "selected"; } ?> value="blackneonred">Black Neon (Red)</option>
							<option <?php if($get_style_file == 'turq') { echo "selected"; } ?> value="turq">Black & Turquoise</option>
							<option <?php if($get_style_file == 'blueorange') { echo "selected"; } ?> value="blueorange">Blue & Orange</option>
							<option <?php if($get_style_file == 'chrome') { echo "selected"; } ?> value="chrome">Chrome</option>
							<option <?php if($get_style_file == 'cosmos') { echo "selected"; } ?> value="cosmos">Cosmic</option>
							<option <?php if($get_style_file == 'purp') { echo "selected"; } ?> value="purp">Cotton Candy</option>
							<option <?php if($get_style_file == 'bgw') { echo "selected"; } ?> value="bgw">Clinic Ace</option>
							<option <?php if($get_style_file == 'ffm') { echo "selected"; } ?> value="ffm">Fresh Focus Media</option>
							<option <?php if($get_style_file == 'ffmwhite') { echo "selected"; } ?> value="ffmwhite">FFM White</option>
							<option <?php if($get_style_file == 'flowers') { echo "selected"; } ?> value="flowers">Flowers</option>
							<option <?php if($get_style_file == 'silver') { echo "selected"; } ?> value="silver">Green & Grey</option>
							<option <?php if($get_style_file == 'garden') { echo "selected"; } ?>  value="garden">Garden</option>
							<option <?php if($get_style_file == 'green') { echo "selected"; } ?>  value="green">Green</option>
							<option <?php if($get_style_file == 'leo') { echo "selected"; } ?> value="polka">Leopard Print</option>
							<option <?php if($get_style_file == 'navy') { echo "selected"; } ?> value="navy">Navy</option>
							<option <?php if($get_style_file == 'orangeblue') { echo "selected"; } ?> value="orangeblue">Orange & Blue</option>
							<option <?php if($get_style_file == 'polka') { echo "selected"; } ?> value="">Polka Dots</option>
							<option <?php if($get_style_file == 'swr') { echo "selected"; } ?> value="swr">Precision Workflow (White)</option>
							<option <?php if($get_style_file == 'bwr') { echo "selected"; } ?> value="bwr">Precision Workflow (Black)</option>
							<option <?php if($get_style_file == 'redsilver') { echo "selected"; } ?> value="redsilver">Red &amp; Silver</option>
							<option <?php if($get_style_file == 'blw') { echo "selected"; } ?> value="blw">ROOK Connect</option>
							<option <?php if($get_style_file == 'happy') { echo "selected"; } ?> value="happy">Smiley Faces</option>
							<option <?php if($get_style_file == 'transport') { echo "selected"; } ?> value="transport">Transport</option>
						</select>
					</div>
				</div>
				<br /><br />
				<div class="form-group">
					<!--<div class="col-sm-4 clearfix">
						<a href="admin_software_config.php" class="btn config-btn pull-right">Back</a>
						<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>--
					</div>-->
					<div class="col-sm-12">
						<button	type="submit" name="add_style" value="add_style" class="btn config-btn btn-lg	pull-right">Submit</button>
					</div>
				</div>
			</form>


		<!-- Social Media Settings -->
		<?php } else if(isset($_GET['software_social_media'])) { ?>
			<div class="notice double-gap-bottom popover-examples">
				<div class="col-sm-1 notice-icon"><img src="img/info.png" class="wiggle-me" width="25px"></div>
				<div class="col-sm-11"><span class="notice-name">TIP:</span>
				Copy and paste the URL of your desired social media page in the appropriate text box below. Leaving a text box blank will hide the social media buttons on your software.</div>
				<div class="clearfix"></div>
			</div>

			<form id="form1" name="form1" method="post"	action="admin_software_config.php?software_social_media" enctype="multipart/form-data" class="form-horizontal" role="form">
				<div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">Facebook:</label>
					<div class="col-sm-8">
					  <input name="facebook" type="text" placeholder="https://www.facebook.com/FreshFocusMediaYYC" value="<?php echo get_config($dbc, 'facebook_link'); ?>" class="form-control"/>
					</div>
				</div>
				<div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">Google Plus:</label>
					<div class="col-sm-8">
					  <input name="googleplus" type="text" placeholder="https://plus.google.com/+Freshfocusmediayyc/posts" value="<?php echo get_config($dbc, 'google_link'); ?>" class="form-control"/>
					</div>
				</div>
				<div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">LinkedIn:</label>
					<div class="col-sm-8">
					  <input name="linkedin" type="text" placeholder="https://www.linkedin.com/company/fresh-focus-media" value="<?php echo get_config($dbc, 'linkedin_link'); ?>" class="form-control"/>
					</div>
				</div>
				<div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">Twitter:</label>
					<div class="col-sm-8">
					  <input name="twitter" type="text" placeholder="https://twitter.com/freshfocusmedia" value="<?php echo get_config($dbc, 'twitter_link'); ?>" class="form-control"/>
					</div>
				</div>
				<br /><br />
				<div class="form-group">
					<!--<div class="col-sm-4 clearfix">
						<a href="contacts.php?category=Business&filter=Top" class="btn config-btn pull-right">Back</a>--
						<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>
					</div>-->
					<div class="col-sm-12">
						<button	type="submit" name="add_social_media" value="add_social_media" class="btn config-btn btn-lg	pull-right">Submit</button>
					</div>
				</div>
			</form>


		<!-- Favicon Settings -->
		<?php } else if(isset($_GET['favicon'])) {

			include('Admin Settings/favicon_settings.php');

		} else if(isset($_GET['login_page'])) { ?>

			<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="img/info.png" class="wiggle-me" width="25px"></div>
			<div class="col-sm-16"><span class="notice-name">NOTE:</span>
				Choose the default page to display when a user first logs in to the software.</div>
				<div class="clearfix"></div>
			</div>

			<form id="form1" name="form1" method="post"	action="admin_software_config.php?login_page" enctype="multipart/form-data" class="form-horizontal" role="form">
				<div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">Default Login Page:</label>
					<div class="col-sm-8">
						<select name="default_login" class="chosen-select-deselect form-control inventoryid" width="380">
							<?php $default_login = get_config($dbc, 'default_login'); ?>
							<option value="Default">Default</option>
							<option <?= $default_login == 'News Board' ? 'selected' : '' ?> value="News Board">News Board</option>
							<option <?= $default_login == 'Calendar' ? 'selected' : '' ?> value="Calendar">Calendar</option>
							<option <?= $default_login == 'Day Sheet' ? 'selected' : '' ?> value="Day Sheet">Planner</option>
						</select>
					</div>
				</div>
				<br /><br />
				<div class="form-group">
					<!--<div class="col-sm-4 clearfix">
						<a href="admin_software_config.php" class="btn config-btn pull-right">Back</a>
						<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>--
					</div>-->
					<div class="col-sm-12">
						<button	type="submit" name="add_default_login" value="add_default_login" class="btn config-btn btn-lg	pull-right">Submit</button>
					</div>
				</div>
			</form>


		<?php } else if(isset($_GET['config_differences'])) {
			include('live_demo_configurations.php');
		} else if(isset($_GET['data_sync'])) {
			include('live_demo_data.php');
		} else if(isset($_GET['reset_demo'])) {
			include('reset_demo_to_live.php');
		} else if(isset($_GET['email_configuration'])) {
			include('staff_email_configuration.php');
		} else if(isset($_GET['sync_inventory'])) {
			include('Admin Settings/sync_inventory.php');
		} ?>
        </div>
    </div>
</div>

<?php include ('footer.php'); ?>
