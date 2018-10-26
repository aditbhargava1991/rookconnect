<script>
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
		url: "../ajax_all.php?fill=tile_config&name="+name+"&value="+final_value+"&turnoff="+turnoff+"&turnOn="+turnOn+"&contactid="+contactid,
		dataType: "html",   //expect html to be returned
		success: function(response){
			response = response.split('#*#');
			console.log(response[0]);
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
		$('#iframe_instead_of_window').attr('src', 'tile_history.php?tile_name='+tile+'&title='+title);
		$('.iframe_title').text('Tile Status History');
		$('.iframe_holder').show();
		$('.hide_on_iframe').hide();
		$('#iframe_instead_of_window').on('load', function() {
			$(this).height($(this).get(0).contentWindow.document.body.scrollHeight);
		});
	});

	$('.close_iframe').click(function(){
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
<div class='iframe_holder' style='display:none;'>
	<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframe' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
	<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
	<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
</div>
<div class="row hide_on_iframe">
	<div class="col-md-12">

	<div id="">
	<?php

	//$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM admin_tile_config WHERE tileconfigid=1"));
    //
	//$software_config = $get_config['software_config'];
	//$profile = $get_config['profile'];
	//$contacts = $get_config['contacts'];
	//$contacts3 = $get_config['contacts3'];
	//$documents = $get_config['documents'];
	//$hr = $get_config['hr'];
	//$package = $get_config['package'];
	//$promotion = $get_config['promotion'];
	//$services = $get_config['services'];
	//$passwords = $get_config['passwords'];
	//$sred = $get_config['sred'];
	//$labour = $get_config['labour'];
	//$material = $get_config['material'];
	//$inventory = $get_config['inventory'];
	//$assets = $get_config['assets'];
	//$equipment = $get_config['equipment'];
	//$custom = $get_config['custom'];
	//$invoicing = $get_config['invoicing'];
	//$pos = $get_config['pos'];
	//$service_queue = $get_config['service_queue'];
	//$incident_report = $get_config['incident_report'];
	//$policy_procedure = $get_config['policy_procedure'];
	//$ops_manual = $get_config['ops_manual'];
	//$emp_handbook = $get_config['emp_handbook'];
	//$how_to_guide = $get_config['how_to_guide'];
	//$safety = $get_config['safety'];
	//$rate_card = $get_config['rate_card'];
	//$estimate = $get_config['estimate'];
	//$quote = $get_config['quote'];
	//$cost_estimate = $get_config['cost_estimate'];
	//$project = $get_config['project'];
	//$project_workflow = $get_config['project_workflow'];
	//$ticket = $get_config['ticket'];
	//$field_job = $get_config['field_job'];
	//$report = $get_config['report'];
	//$driving_log = $get_config['driving_log'];
	//$expense = $get_config['expense'];
	//$payables = $get_config['payables'];
	//$billing = $get_config['billing'];
	//$marketing = $get_config['marketing'];
    //
	//$internal = $get_config['internal'];
	//$rd = $get_config['rd'];
	//$business_development = $get_config['business_development'];
	//$process_development = $get_config['process_development'];
	//$addendum = $get_config['addendum'];
	//$addition = $get_config['addition'];
	//$manufacturing = $get_config['manufacturing'];
	//$assembly = $get_config['assembly'];
	//$work_order = $get_config['work_order'];
	//$daysheet = $get_config['daysheet'];
	//$punch_card = $get_config['punch_card'];
	//$payroll = $get_config['payroll'];
    //
	//$certificate = $get_config['certificate'];
	//$marketing_material = $get_config['marketing_material'];
	//$internal_documents = $get_config['internal_documents'];
	//$client_documents = $get_config['client_documents'];
	//$contracts = $get_config['contracts'];
	//$agenda_meeting = $get_config['agenda_meeting'];
	//$infogathering = $get_config['infogathering'];
	//$products = $get_config['products'];
	//$tasks = $get_config['tasks'];
	//$sales = $get_config['sales'];
	//$gantt_chart = $get_config['gantt_chart'];
	//$communication = $get_config['communication'];
	//$communication_schedule = $get_config['communication_schedule'];
	//$purchase_order = $get_config['purchase_order'];
	//$orientation = $get_config['orientation'];
	//$sales_order = $get_config['sales_order'];
	//$vpl = $get_config['vpl'];
	//$helpdesk = $get_config['helpdesk'];
	//$time_tracking = $get_config['time_tracking'];
	//$newsboard = $get_config['newsboard'];
    //
	//$ffmsupport = $get_config['ffmsupport'];
	//$archiveddata = $get_config['archiveddata'];
	//$email_communication = $get_config['email_communication'];
	//$scrum = $get_config['scrum'];
    //
	//$charts = $get_config['charts'];
	//$field_ticket_estimates = $get_config['field_ticket_estimates'];
	//$checklist = $get_config['checklist'];
	//$calllog = $get_config['calllog'];
	//$budget = $get_config['budget'];
	//$profit_loss = $get_config['profit_loss'];
	//$gao = $get_config['gao'];
    //
	//// Clinic Ace
	//$appointment_calendar = $get_config['appointment_calendar'];
	//$booking = $get_config['booking'];
	//$check_in = $get_config['check_in'];
	//$reactivation = $get_config['reactivation'];
	//$check_out = $get_config['check_out'];
	//$treatment_charts = $get_config['treatment_charts'];
	//$accounts_receivables = $get_config['accounts_receivables'];
	//$therapist = $get_config['therapist'];
	//$treatment = $get_config['treatment'];
	//$exercise_library = $get_config['exercise_library'];
	//$confirmation = $get_config['confirmation'];
	//$goals_compensation = $get_config['goals_compensation'];
	//$crm = $get_config['crm'];
	//$policies = $get_config['policies'];
	//$employee_handbook = $get_config['employee_handbook'];
    //
	//$intake = $get_config['intake'];
	//$how_to_checklist = $get_config['how_to_checklist'];
    //$drop_off_analysis = $get_config['drop_off_analysis'];
    //$injury = $get_config['injury'];
    //
	//$jobs = $get_config['jobs'];
	//$interactive_calendar = $get_config['interactive_calendar'];
    $properties = $get_config['properties'];

	$section_display = ','.get_config($dbc, 'tile_enable_section').',';

	$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='security_software_functionality'"));
    $note = $notes['note'];

    if ( !empty($note) ) { ?>
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11">
                <span class="notice-name">NOTE:</span>
                <?= $note; ?>
            </div>
            <div class="clearfix"></div>
        </div><?php
    } ?>

	<center><input type='text' name='x' class=' form-control live-search-box2' placeholder='Search for a tile...' style='max-width:300px; margin-bottom:20px;'></center>
	<!-- Added in each Accordion -->
	<!--<table class='table table-bordered live-search-list2'>
		<tr class='hidden-sm dont-hide'>
			<th>Available Software Tiles & Functionality</th>
			<th><span class="popover-examples list-inline">
				<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
			</span>Turn On Tile</th>
			<th><span class="popover-examples list-inline">
				<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
			</span>Turn Off Tile</th>
			<th>History</th>
			<th>Status</th>
		</tr>
	</table>-->
	<!-- Software Settings -->
	<div class="panel-group" id="accordion2">
		<?php if(strpos($section_display,',software_settings,') !== FALSE): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field3" >
							Software Settings<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_field3" class="panel-collapse collapse in">
					<div class="panel-body">
						<div id='no-more-tables'>
							<table border='2' cellpadding='10' class='table table-bordered live-search-list2'>
								<tr class='hidden-sm dont-hide'>
									<th>Available Software Tiles & Functionality</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn On Tile</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn Off Tile</th>
									<th>History</th>
									<th>Status</th>
								</tr>
								<tr>
										<td data-title="Comment">Archived Data</td>
										<?php echo tile_config_function($dbc, 'archiveddata'); ?>
									</tr>
									<tr>
										<td data-title="Comment">Customer Support</td>
										<?php echo tile_config_function($dbc, 'customer_support'); ?>
									</tr>
                                    <tr>
										<td data-title="Comment">Demo</td>
										<?php echo tile_config_function($dbc, 'demo'); ?>
									</tr>
									<tr>
										<td data-title="Comment">FFM Support</td>
										<?php echo tile_config_function($dbc, 'ffmsupport'); ?>
									</tr>
									<tr>
										<td data-title="Comment">Passwords</td>
										<?php echo tile_config_function($dbc, 'passwords'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Comment">Profile</td>
                                        <?php echo tile_config_function($dbc, 'profile'); ?>
                                    </tr>
									<tr>
										<td data-title="Comment">Security</td>
										<?php echo tile_config_function($dbc, 'security'); ?>
									</tr>
									<tr>
										<td data-title="Comment">Software Settings</td>
										<?php echo tile_config_function($dbc, 'software_config'); ?>
									</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<!-- Human Resources -->
		<?php if(strpos($section_display,',human_resources,') !== FALSE): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field5" >
							Human Resources<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_field5" class="panel-collapse collapse">
					<div class="panel-body">
						<div id='no-more-tables'>
							<table border='2' cellpadding='10' class='table table-bordered live-search-list2'>
								<tr class='hidden-sm dont-hide'>
									<th>Available Software Tiles & Functionality</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn On Tile</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn Off Tile</th>
									<th>History</th>
									<th>Status</th>
								</tr>
                                <tr>
                                    <td data-title="Comment">All Software Guide</td>
                                    <?php echo tile_config_function($dbc, 'how_to_guide'); ?>
                                </tr>
								<tr>
                                    <td data-title="Comment">Certificates</td>
                                    <?php echo tile_config_function($dbc, 'certificate'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Employee Handbook</td>
                                    <?php echo tile_config_function($dbc, 'emp_handbook'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Goals & Objectives</td>
                                    <?php echo tile_config_function($dbc, 'gao'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">How To Checklist</td>
                                    <?php echo tile_config_function($dbc, 'how_to_checklist'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">HR</td>
                                    <?php echo tile_config_function($dbc, 'hr'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Manuals</td>
                                    <?php echo tile_config_function($dbc, 'manual'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Operations Manual</td>
                                    <?php echo tile_config_function($dbc, 'ops_manual'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Orientation</td>
                                    <?php echo tile_config_function($dbc, 'orientation'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Performance Reviews</td>
                                    <?php echo tile_config_function($dbc, 'preformance_review'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Policies & Procedures</td>
                                    <?php echo tile_config_function($dbc, 'policy_procedure'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Safety Manual</td>
                                    <?php echo tile_config_function($dbc, 'safety_manual'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Software Guide</td>
                                    <?php echo tile_config_function($dbc, 'software_guide'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Staff</td>
                                    <?php echo tile_config_function($dbc, 'staff'); ?>
                                </tr>
                                <tr>
                                    <td data-title="Comment">Training & Quizzes</td>
                                    <?php echo tile_config_function($dbc, 'training_quiz'); ?>
                                </tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<!-- Human Resources -->
		<!-- Profiles -->
		<?php if(strpos($section_display,',profiles,') !== FALSE): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field27" >
							Profiles<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_field27" class="panel-collapse collapse">
					<div class="panel-body">
						<div id='no-more-tables'>
							<table border='2' cellpadding='10' class='table table-bordered live-search-list2'>
								<tr class='hidden-sm dont-hide'>
									<th>Available Software Tiles & Functionality</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn On Tile</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn Off Tile</th>
									<th>History</th>
									<th>Status</th>
								</tr>
								<tr>
						                <td data-title="Comment">Client Information</td>
						                <?php echo tile_config_function($dbc, 'client_info'); ?>
						            </tr>
									<tr>
										<td data-title="Comment">Contacts</td>
										<?php echo tile_config_function($dbc, 'contacts'); ?>
									</tr>
									<tr>
										<td data-title="Comment">Contacts (Updated)</td>
										<?php echo tile_config_function($dbc, 'contacts_inbox'); ?>
									</tr>

									<tr>
										<td data-title="Comment">Contacts 3</td>
										<?php echo tile_config_function($dbc, 'contacts3'); ?>
									</tr>
									<tr>
										<td data-title="Comment">Contacts Rolodex</td>
										<?php echo tile_config_function($dbc, 'contacts_rolodex'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Fund Development</td>
                                        <?php echo tile_config_function($dbc, 'fund_development'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Members">Members</td>
                                        <?php echo tile_config_function($dbc, 'members'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="PT Day Sheet">PT Day Sheet</td>
                                        <?php echo tile_config_function($dbc, 'therapist'); ?>
                                    </tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<!-- Profiles -->
		<!-- Accounting -->
		<?php if(strpos($section_display,',accounting,') !== FALSE): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field36" >
							Accounting<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_field36" class="panel-collapse collapse">
					<div class="panel-body">
						<div id='no-more-tables'>
							<table border='2' cellpadding='10' class='table table-bordered live-search-list2'>
								<tr class='hidden-sm dont-hide'>
									<th>Available Software Tiles & Functionality</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn On Tile</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn Off Tile</th>
									<th>History</th>
									<th>Status</th>
								</tr>
								<tr>
                                        <td data-title="Accounts Receivable">Accounts Receivable</td>
                                        <?php echo tile_config_function($dbc, 'accounts_receivables'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Budget</td>
                                        <?php echo tile_config_function($dbc, 'budget'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Expenses</td>
                                        <?php echo tile_config_function($dbc, 'expense'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Goals & Objectives">Goals & Compensation</td>
                                        <?php echo tile_config_function($dbc, 'goals_compensation'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Payables</td>
                                        <?php echo tile_config_function($dbc, 'payables'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Payroll</td>
                                        <?php echo tile_config_function($dbc, 'payroll'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Profit & Loss</td>
                                        <?php echo tile_config_function($dbc, 'profit_loss'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Project Billing & Invoices</td>
                                        <?php echo tile_config_function($dbc, 'billing'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Purchase Orders</td>
                                        <?php echo tile_config_function($dbc, 'purchase_order'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Reports</td>
                                        <?php echo tile_config_function($dbc, 'report'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Vendor Price List</td>
                                        <?php echo tile_config_function($dbc, 'vpl'); ?>
                                    </tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<!-- Accouting -->
		<!-- Time Tracking -->
		<?php if(strpos($section_display,',time_tracking,') !== FALSE): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field34" >
							Time Tracking<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>


                    <div id="collapse_field34" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
										<tr>
                                        <td data-title="Planner">Planner</td>
                                        <?php echo tile_config_function($dbc, 'daysheet'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Sign In</td>
                                        <?php echo tile_config_function($dbc, 'sign_in_time'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?= START_DAY ?></td>
                                        <?php echo tile_config_function($dbc, 'start_day_button'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Time Clock</td>
                                        <?php echo tile_config_function($dbc, 'punch_card'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Timesheet">Time Sheets</td>
                                        <?php echo tile_config_function($dbc, 'timesheet'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Time Tracking</td>
                                        <?php echo tile_config_function($dbc, 'time_tracking'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
			<!-- Inventory Management -->
		<?php if(strpos($section_display,',inventory_management,') !== FALSE): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field19" >
							Inventory Management<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_field19" class="panel-collapse collapse">
					<div class="panel-body">
						<div id='no-more-tables'>
							<table border='2' cellpadding='10' class='table table-bordered live-search-list2'>
								<tr class='hidden-sm dont-hide'>
									<th>Available Software Tiles & Functionality</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn On Tile</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn Off Tile</th>
									<th>History</th>
									<th>Status</th>
								</tr>
								<tr>
                                        <td data-title="Comment">Assets</td>
                                        <?php echo tile_config_function($dbc, 'assets'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Inventory</td>
                                        <?php echo tile_config_function($dbc, 'inventory'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Materials</td>
                                        <?php echo tile_config_function($dbc, 'material'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?= VENDOR_TILE ?></td>
                                        <?php echo tile_config_function($dbc, 'vendors'); ?>
                                    </tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<!-- Equipment -->
		<?php if(strpos($section_display,',equipment,') !== FALSE): ?>
			<div class="panel panel-default">
                    <div class="panel-heading">
						<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field7" >
							Equipment<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
                    </div>

                    <div id="collapse_field7" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Equipment</td>
                                        <?php echo tile_config_function($dbc, 'equipment'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
		<?php endif; ?>
		<!-- Collaborative -->
		<?php if(strpos($section_display,',collaborative_workflow,') !== FALSE): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
						<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field8" >
							Collaborative Workflow<span class="glyphicon glyphicon-plus"></span>
						</a>
                    </div>

                    <div id="collapse_field8" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Agendas & Meetings</td>
                                        <?php echo tile_config_function($dbc, 'agenda_meeting'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Appointment Calendar">Appointment Calendar</td>
                                        <?php echo tile_config_function($dbc, 'appointment_calendar'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Booking">Booking</td>
                                        <?php echo tile_config_function($dbc, 'booking'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Calendar</td>
                                        <?php echo tile_config_function($dbc, 'calendar_rook'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Checklists</td>
                                        <?php echo tile_config_function($dbc, 'checklist'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Interactive Calendar</td>
                                        <?php echo tile_config_function($dbc, 'interactive_calendar'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">News Board</td>
                                        <?php echo tile_config_function($dbc, 'newsboard'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Tasks</td>
                                        <?php echo tile_config_function($dbc, 'tasks'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Tasks (Updated)</td>
                                        <?php echo tile_config_function($dbc, 'tasks_updated'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Trip Optimizer</td>
                                        <?php echo tile_config_function($dbc, 'optimize'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
		<?php endif; ?>
		<!-- Digital Forms -->
		<?php if(strpos($section_display,',digital_forms,') !== FALSE): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
						<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field37" >
							Digital Forms<span class="glyphicon glyphicon-plus"></span>
						</a>
                    </div>

                    <div id="collapse_field37" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Client Documentation</td>
                                        <?php echo tile_config_function($dbc, 'client_documentation'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Contracts</td>
                                        <?php echo tile_config_function($dbc, 'contracts'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Daily Log Notes</td>
                                        <?php echo tile_config_function($dbc, 'daily_log_notes'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Day Program</td>
                                        <?php echo tile_config_function($dbc, 'day_program'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Documents">Documents</td>
                                        <?php echo tile_config_function($dbc, 'documents_all'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Exercise Library">Exercise Library</td>
                                        <?php echo tile_config_function($dbc, 'exercise_library'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Exercise Library">Form Builder</td>
                                        <?php echo tile_config_function($dbc, 'form_builder'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Individual Service Plan (ISP)</td>
                                        <?php echo tile_config_function($dbc, 'individual_support_plan'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Charts">Charts</td>
                                        <?php echo tile_config_function($dbc, 'charts'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Medications</td>
                                        <?php echo tile_config_function($dbc, 'medication'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Routine Creator</td>
                                        <?php echo tile_config_function($dbc, 'routine'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Social Story</td>
                                        <?php echo tile_config_function($dbc, 'social_story'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Treatment Charts">Treatment Charts</td>
                                        <?php echo tile_config_function($dbc, 'treatment_charts'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
                <!-- Estimates / Quotes -->
			<?php if(strpos($section_display,',estimates,') !== FALSE): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field38" >
								<?= ESTIMATE_TILE ?> / Quoting<span class="glyphicon glyphicon-plus"></span>
							</a>
                        </h4>
                    </div>

                    <div id="collapse_field38" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Cost Estimates</td>
                                        <?php echo tile_config_function($dbc, 'cost_estimate'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?= ESTIMATE_TILE ?></td>
                                        <?php echo tile_config_function($dbc, 'estimate'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Field Ticket Estimates</td>
                                        <?php echo tile_config_function($dbc, 'field_ticket_estimates'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Quotes</td>
                                        <?php echo tile_config_function($dbc, 'quote'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
		<!-- Sales -->
		<?php if(strpos($section_display,',sales,') !== FALSE): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field6" >
							Sales<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_field6" class="panel-collapse collapse">
					<div class="panel-body">
						<div id='no-more-tables'>
							<table border='2' cellpadding='10' class='table table-bordered live-search-list2'>
								<tr class='hidden-sm dont-hide'>
									<th>Available Software Tiles & Functionality</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn On Tile</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn Off Tile</th>
									<th>History</th>
									<th>Status</th>
								</tr>
								<tr>
										<td data-title="Comment">Cold Call</td>
										<?php echo tile_config_function($dbc, 'calllog'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Drop Off Analysis">Drop Off Analysis</td>
                                        <?php echo tile_config_function($dbc, 'drop_off_analysis'); ?>
                                    </tr>
									<tr>
										<td data-title="Comment">Information Gathering</td>
										<?php echo tile_config_function($dbc, 'infogathering'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Comment">Intake Form</td>
                                        <?php echo tile_config_function($dbc, 'intake'); ?>
                                    </tr>
									<tr>
										<td data-title="Comment">Sales</td>
										<?php echo tile_config_function($dbc, 'sales'); ?>
									</tr>
									<tr>
										<td data-title="Comment"><?= SALES_ORDER_TILE ?></td>
										<?php echo tile_config_function($dbc, 'sales_order'); ?>
									</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<!-- Collaborative -->
		<?php if(strpos($section_display,',collaborative,') !== FALSE): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field7" >
							Collaborative<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_field7" class="panel-collapse collapse">
					<div class="panel-body">
						<div id='no-more-tables'>
							<table border='2' cellpadding='10' class='table table-bordered live-search-list2'>
								<tr class='hidden-sm dont-hide'>
									<th>Available Software Tiles & Functionality</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn On Tile</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn Off Tile</th>
									<th>History</th>
									<th>Status</th>
								</tr>
								<tr>
									<td data-title="Comment">Communication Tasks</td>
									<?php echo tile_config_function($dbc, 'communication'); ?>
								</tr>
								<tr>
									<td data-title="Comment">Communication</td>
									<?php echo tile_config_function($dbc, 'communication_schedule'); ?>
								</tr>
								<tr>
									<td data-title="Comment">Email Communication</td>
									<?php echo tile_config_function($dbc, 'email_communication'); ?>
								</tr>
								<tr>
									<td data-title="Comment">News Board</td>
									<?php echo tile_config_function($dbc, 'newsboard'); ?>
								</tr>
								<tr>
									<td data-title="Comment">Phone Communication</td>
									<?php echo tile_config_function($dbc, 'phone_communication'); ?>
								</tr>
								<tr>
									<td data-title="Comment">Scrum</td>
									<?php echo tile_config_function($dbc, 'scrum'); ?>
								</tr>
								<tr>
									<td data-title="Comment">Tasks</td>
									<?php echo tile_config_function($dbc, 'tasks'); ?>
								</tr>
								<tr>
									<td data-title="Comment">Tasks (Updated)</td>
									<?php echo tile_config_function($dbc, 'tasks_updated'); ?>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<!-- Estimates / Quotes -->
		<!-- Project Management -->
		<?php if(strpos($section_display,',project_management,') !== FALSE): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field39" >
								<?php echo PROJECT_TILE; ?> Management<span class="glyphicon glyphicon-plus"></span>
							</a>
                        </h4>
                    </div>

                    <div id="collapse_field39" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Addendum <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'addendum'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Addition <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'addition'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Assembly <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'assembly'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Business Development <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'business_development'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Client Projects</td>
                                        <?php echo tile_config_function($dbc, 'client_projects'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Communication</td>
                                        <?php echo tile_config_function($dbc, 'communication_schedule'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Communication Tasks</td>
                                        <?php echo tile_config_function($dbc, 'communication'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Dispatch</td>
                                        <?php echo tile_config_function($dbc, 'dispatch', 'admin'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Email Communication</td>
                                        <?php echo tile_config_function($dbc, 'email_communication'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Field Jobs</td>
                                        <?php echo tile_config_function($dbc, 'field_job'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Gantt Chart</td>
                                        <?php echo tile_config_function($dbc, 'gantt_chart'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Injury">Injury</td>
                                        <?php echo tile_config_function($dbc, 'injury'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Internal <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'internal'); ?>
                                    </tr>
                                    <!--
                                    <tr>
                                        <td data-title="Comment">Jobs</td>
                                        <?php //echo tile_config_function($dbc, 'jobs'); ?>
                                    </tr>
                                    -->
                                    <tr>
                                        <td data-title="Comment">Manufacturing <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'manufacturing'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Marketing <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'marketing'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Phone Communication</td>
                                        <?php echo tile_config_function($dbc, 'phone_communication'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Process Development <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'process_development'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Products</td>
                                        <?php echo tile_config_function($dbc, 'products'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?></td>
                                        <?php echo tile_config_function($dbc, 'project'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Project Workflow</td>
                                        <?php echo tile_config_function($dbc, 'project_workflow'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Properties</td>
                                        <?php echo tile_config_function($dbc, 'properties'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">R&D <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'rd'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Scrum</td>
                                        <?php echo tile_config_function($dbc, 'scrum'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Services</td>
                                        <?php echo tile_config_function($dbc, 'services'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Site Work Orders</td>
                                        <?php echo tile_config_function($dbc, 'site_work_orders'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Shop Work Orders</td>
                                        <?php echo tile_config_function($dbc, 'shop_work_orders'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">SR&ED <?php echo PROJECT_TILE; ?></td>
                                        <?php echo tile_config_function($dbc, 'sred'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?= TICKET_TILE ?></td>
                                        <?php echo tile_config_function($dbc, 'ticket'); ?>
                                    </tr>
                                    <!--
                                    <tr>
                                        <td data-title="Comment">Work Orders</td>
                                        <?php //echo tile_config_function($dbc, 'work_order'); ?>
                                    </tr>
                                    -->
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endif; ?>

		<!-- Safety -->
		<?php if(strpos($section_display,',safety,') !== FALSE): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field9" >
							Safety<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_field9" class="panel-collapse collapse">
					<div class="panel-body">
						<div id='no-more-tables'>
							<table border='2' cellpadding='10' class='table table-bordered live-search-list2'>
								<tr class='hidden-sm dont-hide'>
									<th>Available Software Tiles & Functionality</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn On Tile</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn Off Tile</th>
									<th>History</th>
									<th>Status</th>
								</tr>
								<tr>
										<td data-title="Comment">Driving Log</td>
										<?php echo tile_config_function($dbc, 'driving_log'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Comment"><?= INC_REP_TILE ?></td>
                                        <?php echo tile_config_function($dbc, 'incident_report'); ?>
                                    </tr>
									<tr>
										<td data-title="Comment">Safety</td>
										<?php echo tile_config_function($dbc, 'safety'); ?>
									</tr>
                                    <tr>
                                        <td data-title="Daily Log Notes">Match</td>
                                        <?php echo tile_config_function($dbc, 'match'); ?>
                                    </tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<!-- Project Management -->

		<?php if(strpos($section_display,',point_of_sale,') !== FALSE): ?>
		<!-- Point of Sale -->
                <div class="panel panel-default">
                    <div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field30" >
								Point of Sale<span class="glyphicon glyphicon-plus"></span>
							</a>
                        </h4>
                    </div>

                    <div id="collapse_field30" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Check In">Check In</td>
                                        <?php echo tile_config_function($dbc, 'check_in'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Check Out">Check Out</td>
                                        <?php echo tile_config_function($dbc, 'check_out'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Custom</td>
                                        <?php echo tile_config_function($dbc, 'custom'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Invoicing</td>
                                        <?php echo tile_config_function($dbc, 'invoicing'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Labour</td>
                                        <?php echo tile_config_function($dbc, 'labour'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Packages</td>
                                        <?php echo tile_config_function($dbc, 'package'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment"><?= POS_ADVANCE_TILE ?></td>
                                        <?php echo tile_config_function($dbc, 'posadvanced'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Promotions & Coupons</td>
                                        <?php echo tile_config_function($dbc, 'promotion'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Rate Cards</td>
                                        <?php echo tile_config_function($dbc, 'rate_card'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Service Queue</td>
                                        <?php echo tile_config_function($dbc, 'service_queue'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
                <!-- Customer Relationship Management -->
			<?php if(strpos($section_display,',crm,') !== FALSE): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field31" >
								Customer Relationship Management<span class="glyphicon glyphicon-plus"></span>
							</a>
                        </h4>
                    </div>

                    <div id="collapse_field31" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Confirmation">Notifications</td>
                                        <?php echo tile_config_function($dbc, 'confirmation'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Confirm">Confirmation</td>
                                        <?php echo tile_config_function($dbc, 'confirm'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="CRM">CRM</td>
                                        <?php echo tile_config_function($dbc, 'crm'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Reactivation">Follow Up</td>
                                        <?php echo tile_config_function($dbc, 'reactivation'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Helpdesk</td>
                                        <?php echo tile_config_function($dbc, 'helpdesk'); ?>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Website</td>
                                        <?php echo tile_config_function($dbc, 'website'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
                <!-- Analytics -->
			<?php if(strpos($section_display,',analytics,') !== FALSE): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field32" >
								Analytics<span class="glyphicon glyphicon-plus"></span>
							</a>
                        </h4>
                    </div>

                    <div id="collapse_field32" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div id='no-more-tables'>
                                <table border='2' cellpadding='10' class='table live-search-list2'>
                                    <tr class='hidden-sm dont-hide'>
                                        <th>Available Software Tiles &amp; Functionality</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click below to Activate the desired Tiles for your software. Activating the Tile does not configure the details for your desired functionality, you will need to configure functionality individually through the setting for that Tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile On</th>
                                        <th><span class="popover-examples list-inline">&nbsp;
                                            <a  data-toggle="tooltip" data-placement="top" title="Click here to Deactivate functionality for your software. Deactivating removes all access to that functionality in the software. Data tables may be lost if you're looking to limit access; please do so from the security tile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                                            </span>Turn Tile Off</th>
                                        <th>History</th>
                                        <th>Function Status</th>
                                    </tr>
                                    <tr>
                                        <td data-title="Comment">Reports</td>
                                        <?php echo tile_config_function($dbc, 'report'); ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
		<?php endif; ?>
		<!-- Communication -->
		<?php if(strpos($section_display,',communication,') !== FALSE): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field21" >
							Communication<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_field21" class="panel-collapse collapse">
					<div class="panel-body">
						<div id='no-more-tables'>
							<table border='2' cellpadding='10' class='table table-bordered live-search-list2'>
								<tr class='hidden-sm dont-hide'>
									<th>Available Software Tiles & Functionality</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning on a tile applies the tile to active view. The tile still must be configured, click the tile to configure."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn On Tile</th>
									<th><span class="popover-examples list-inline">
										<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Turning off a tile removes the tile from the active view. Tiles turned off are not deleted, merely removed from the active view."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
									</span>Turn Off Tile</th>
									<th>History</th>
									<th>Status</th>
								</tr>
								<tr>
									<td data-title="Comment">Non Verbal Communication</td>
									<?php echo tile_config_function($dbc, 'non_verbal_communication'); ?>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

	</div>
	<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>
	</div>
	</div>
</div>
