<script>
jQuery(document).ready(function($){
	$('.live-search-box').focus();
	$('.live-search-list tr').each(function(){
		var text = $(this).text() + ' ' + $(this).prevAll().andSelf().find('th').last().text();
		$(this).attr('data-search-term', text.toLowerCase());
	});

	$('.live-search-box').on('keyup', function(){
		var searchTerm = $(this).val().toLowerCase();

		$('.live-search-list tr').each(function(){
			if(searchTerm == '' && $(this).data('dashboard') == '' && !$(this).hasClass('dont-hide')) {
				$(this).hide();
			} else if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
				$(this).show();
			} else if(!$(this).hasClass('dont-hide')) {
				$(this).hide();
			}
		});
	});
	$('.live-search-box').keyup();
});

function securityConfig(sel) {
	var type = sel.type;
	var name = sel.name;
	var label = $(sel).closest('tr').find('[type=text]').val();
	if(label == undefined) {
		label = '';
	}
	var tile_value = sel.value;
	var id = sel.id;
	var final_value = '*';
	if($("#"+name+"_turn_on").is(":checked")) {
		final_value += 'turn_on*';
	}
	if($("#"+name+"_turn_off").is(":checked")) {
		final_value += 'turn_off*';
	}

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "/ajax_all.php?fill=security_level&name="+name+"&value="+final_value+"&label="+label,
		dataType: "html",   //expect html to be returned
		success: function(response){
			var url = window.location.href;
			var n = url.indexOf("#");
			if(n > 0) {
				var url = url.slice(0,n);
			}
		}
	});
}
function renameLevel(input) {
	label = input.value;
	name = $(input).closest('tr').find('[type=radio]').attr('name');
	if(name != '') {
		var final_value = '*';
		if($("#"+name+"_turn_on").is(":checked")) {
			final_value += 'turn_on*';
		}
		if($("#"+name+"_turn_off").is(":checked")) {
			final_value += 'turn_off*';
		}
		$.ajax({    //create an ajax request to ajax_all.php
			type: "GET",
			url: "/ajax_all.php?fill=security_level&name="+name+"&value="+final_value+"&label="+label,
			dataType: "html",   //expect html to be returned
			success: function(response){
				var url = window.location.href;
				var n = url.indexOf("#");
				if(n > 0) {
					var url = url.slice(0,n);
				}
			}
		});
	}
}

function load_history(level) {
	var title = $(this).parents('tr').children(':first').text();
	$('#iframe_instead_of_window').attr('src', 'level_history.php?level='+level+'&title='+title);
	$('.iframe_title').text('Security Level History');
	$('.iframe_holder').show();
	$('.hide_on_iframe').hide();
	$('#iframe_instead_of_window').on('load', function() {
		$(this).height($(this).get(0).contentWindow.document.body.scrollHeight);
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
}
function add_custom_row() {
	var row = $('tr').last();
	var new_row = row.clone();
	new_row.find('input').val('').removeAttr('checked').prop('name','');
	row.after(new_row);
}
</script>
<div class='iframe_holder' style='display:none;'>
	<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframe' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
	<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
	<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
</div>
<div class="row hide_on_iframe">
	<div class="col-md-12">

	<?php // $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM security_level WHERE securitylevelid=1"));
	$admin = $therapist = $executive_front_staff = $trainer = $accounting = $accmanager = $advocate = $assembler = $assist = $businessdevmanager = $businessdevcoo = $contractor = $chairman = $ced = $ceo = $cfd = $cfo = $coo = $cod = $client = $commsalesdirector = $customers = $controller = $daypass = $executive = $execassist = $exdirect = $fieldops = $fieldopmanager = $fieldshop = $fieldsup = $findirect = $fluidhaulingman = $foreman = $genmanager = $hrmanager = $humanres = $invmanager = $lead = $mainshop = $manfmanager = $manager = $marketing = $marketingdirector = $mrkmanager = $master = $officemanager = $office_admin = $operations = $opsmanager = $opconsult = $operationslead = $opcoord = $paintshop = $president = $prospect = $regionalmanager = $sales = $safety = $safetysup = $salesmarketingdirect = $salesdirector = $salesmanager = $shopforeman = $shopworker = $staff = $supervisor = $suppchainlogist = $supporter = $teamcolead = $teamlead = $teammember = $vicepres = $vpcorpdev = $vpsales = $waterspec = false;
	$security_levels = mysqli_query($dbc, "SELECT * FROM `security_level_names` WHERE `active` > 0 AND `identifier` NOT LIKE 'FFMCUST_%'");
	while($row = mysqli_fetch_assoc($security_levels)) {
		${$row['identifier']} = true;
		${$row['identifier'].'_history'} = $row['history'];

	} ?>

	<div class="row live-search-list"><?php
    $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='security_security_level_n_group'"));
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

	<center><input type='text' name='x' class='form-control live-search-box' placeholder='Search for a tile...' style='max-width:300px;margin-bottom:20px;'></center>

	<br><br>
	<table class='table table-bordered table-striped block-group'>
        <thead>
            <tr class='hidden-sm hidden-xs dont-hide'>
                <th>Activate Security Levels & Groups</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click here to Activate a security level of security group. Activation of a security level of group does not assign security privileges, security privileges need to be assigned in the Set Security Privileges of the software."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                </span>
                Activate</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click here to Deactivate a security level or group. Deactivating a security level that has accounts assigned to it will prevent them from accessing the software and automatically suspend their account."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a>
                </span>
                Deactivate</th>
                <th>History</th>
								<th>Order</th>
            </tr>
        </thead>
		<?php
		$current_role_array = array_filter(explode(",",$_SESSION['role']));
		$current_role = $current_role[1];
		$level_array = array('super','admin','therapist','executive_front_staff','trainer','accounting','accmanager','advocate','assembler','assist','businessdevmanager','businessdevcoo','contractor','chairman','ced','ceo','cfd','cfo','coo','cod','client','commsalesdirector','customers','controller','daypass','executive','execassist','exdirect','fieldops','fieldopmanager','fieldshop','fieldsup','findirect','fluidhaulingman','foreman','genmanager','hrmanager','humanres','invmanager','lead','mainshop','manfmanager','manager','marketing','marketingdirector','mrkmanager','master','officemanager','office_admin','operations','opsmanager','opconsult','operationslead','opcoord','paintshop','president','prospect','regionalmanager','sales','safety','safetysup','salesmarketingdirect','salesdirector','salesmanager','shopforeman','shopworker','staff','supervisor','suppchainlogist','supporter','teamcolead','teamlead','teammember','vicepres','vpcorpdev','vpsales','waterspec');
		 ?>
		 <?php
		 	$split = array_search($current_role, $level_array);
			$verify_array = array_slice($level_array, $split + 1); // second part
		  ?>
		<tbody>
		<?php  $security_level_array = mysqli_query($dbc, "select * from security_level_names order by custom_order ASC"); ?>
		<?php  $security_level_count_array = mysqli_fetch_assoc(mysqli_query($dbc, "select count(*) as level_count from security_level_names")); ?>
		<?php $counter = 1; ?>
		<?php while($row = mysqli_fetch_assoc($security_level_array)) { ?>
					<?php if(in_array($row['identifier'], $verify_array)): ?>
						<tr id='<?php echo $row['identifier']; ?>'>
							<td data-title="Comment"><?php echo $row['label']; ?></td>
							<?php echo security_level_function($row['identifier'], $$row['identifier'], ""); ?>
							<td width="5%">
								<?php if($counter == $security_level_count_array['level_count']): ?>
									<!--<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option();">-->
								<?php endif; ?>
								<img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
							</td>
						</tr>
					<?php endif; ?>
					<?php $counter++; ?>
		<?php } ?>
		<!--<?php if(in_array('therapist', $verify_array)): ?>
		<tr id="therapist">
			<td data-title="Comment">Therapist</td>
			<?php echo security_level_function('therapist', $therapist, $therapist_history); ?>
			<td width="5%">
				<img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
			</td>
		</tr>
	<?php endif; ?>
	<?php if(in_array('executive_front_staff', $verify_array)): ?>
		<tr id="executive_front_staff">
			<td data-title="Comment">Executive Front Staff</td>
			<?php echo security_level_function('executive_front_staff', $executive_front_staff, $executive_front_staff_history); ?>
			<td width="5%">
				<img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
			</td>
		</tr>
	<?php endif; ?>
	<?php if(in_array('trainer', $verify_array)): ?>
		<tr id="trainer">
			<td data-title="Comment">Trainer</td>
			<?php echo security_level_function('trainer', $trainer, $trainer_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('accounting', $verify_array)): ?>
		<tr id="accounting">
			<td data-title="Comment">Accounting</td>
			<?php echo security_level_function('accounting', $accounting, $accounting_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('accmanager', $verify_array)): ?>
		<tr id="accmanager">
			<td data-title="Comment">Accounting Manager</td>
			<?php echo security_level_function('accmanager', $accmanager, $accmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('advocate', $verify_array)): ?>
		<tr id="advocate">
			<td data-title="Comment">Advocate</td>
			<?php echo security_level_function('advocate', $advocate, $advocate_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('assembler', $verify_array)): ?>
		<tr id="assembler">
			<td data-title="Comment">Assembler</td>
			<?php echo security_level_function('assembler', $assembler, $assembler_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('assist', $verify_array)): ?>
		<tr id="assist">
			<td data-title="Comment">Assistant</td>
			<?php echo security_level_function('assist', $assist, $assist_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('businessdevmanager', $verify_array)): ?>
		<tr id="businessdevmanager">
			<td data-title="Comment">Business Development Manager</td>
			<?php echo security_level_function('businessdevmanager', $businessdevmanager, $businessdevmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('businessdevcoo', $verify_array)): ?>
		<tr id="businessdevcoo">
			<td data-title="Comment">Business Development Coordinator</td>
			<?php echo security_level_function('businessdevcoo', $businessdevcoo, $businessdevcoo_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('contractor', $verify_array)): ?>
		 <tr id="contractor">
			<td data-title="Comment">Contractor</td>
			<?php echo security_level_function('contractor', $contractor, $contractor_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('chairman', $verify_array)): ?>
		<tr id="chairman">
			<td data-title="Comment">Chairman</td>
			<?php echo security_level_function('chairman', $chairman, $chairman_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('ced', $verify_array)): ?>
		<tr id="ced">
			<td data-title="Comment">Chief Executive Director</td>
			<?php echo security_level_function('ced', $ced, $ced_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('ceo', $verify_array)): ?>
		<tr id="ceo">
			<td data-title="Comment">Chief Executive Officer</td>
			<?php echo security_level_function('ceo', $ceo, $ceo_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('cfd', $verify_array)): ?>
		<tr id="cfd">
			<td data-title="Comment">Chief Financial Director</td>
			<?php echo security_level_function('cfd', $cfd, $cfd_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('cfo', $verify_array)): ?>
		<tr id="cfo">
			<td data-title="Comment">Chief Financial Officer</td>
			<?php echo security_level_function('cfo', $cfo, $cfo_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('coo', $verify_array)): ?>
		<tr id="coo">
			<td data-title="Comment">Chief Operating Officer</td>
			<?php echo security_level_function('coo', $coo, $coo_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('cod', $verify_array)): ?>
		<tr id="cod">
			<td data-title="Comment">Chief Operations Director</td>
			<?php echo security_level_function('cod', $cod, $cod_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('client', $verify_array)): ?>
		<tr id="client">
			<td data-title="Comment">Client</td>
			<?php echo security_level_function('client', $client, $client_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('commsalesdirector', $verify_array)): ?>
		<tr id="commsalesdirector">
			<td data-title="Comment">Commercial Sales Director</td>
			<?php echo security_level_function('commsalesdirector', $commsalesdirector, $commsalesdirector_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('customers', $verify_array)): ?>
		<tr id="customers">
			<td data-title="Comment">Customers</td>
			<?php echo security_level_function('customers', $customers, $customers_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('controller', $verify_array)): ?>
		<tr id="controller">
			<td data-title="Comment">Controller</td>
			<?php echo security_level_function('controller', $controller, $controller_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('daypass', $verify_array)): ?>
		<tr id="daypass">
			<td data-title="Comment">Day Pass</td>
			<?php echo security_level_function('daypass', $daypass, $daypass_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('executive', $verify_array)): ?>
		<tr id="executive">
			<td data-title="Comment">Executive</td>
			<?php echo security_level_function('executive', $executive, $executive_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('execassist', $verify_array)): ?>
		<tr id="execassist">
			<td data-title="Comment">Executive Assistant</td>
			<?php echo security_level_function('execassist', $execassist, $execassist_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('exdirect', $verify_array)): ?>
		<tr id="exdirect">
			<td data-title="Comment">Executive Director</td>
			<?php echo security_level_function('exdirect', $exdirect, $exdirect_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('fieldops', $verify_array)): ?>
		<tr id="fieldops">
			<td data-title="Comment">Field Operations</td>
			<?php echo security_level_function('fieldops', $fieldops, $fieldops_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('fieldopmanager', $verify_array)): ?>
		<tr id="fieldopmanager">
			<td data-title="Comment">Field Operations Manager</td>
			<?php echo security_level_function('fieldopmanager', $fieldopmanager, $fieldopmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('fieldshop', $verify_array)): ?>
		<tr id="fieldshop">
			<td data-title="Comment">Field Shop</td>
			<?php echo security_level_function('fieldshop', $fieldshop, $fieldshop_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('fieldsup', $verify_array)): ?>
		<tr id="fieldsup">
			<td data-title="Comment">Field Supervisor</td>
			<?php echo security_level_function('fieldsup', $fieldsup, $fieldsup_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('findirect', $verify_array)): ?>
		<tr id="findirect">
			<td data-title="Comment">Financial Director</td>
			<?php echo security_level_function('findirect', $findirect, $findirect_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('fluidhaulingman', $verify_array)): ?>
		<tr id="fluidhaulingman">
			<td data-title="Comment">Fluid Hauling Manager</td>
			<?php echo security_level_function('fluidhaulingman', $fluidhaulingman, $fluidhaulingman_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('foreman', $verify_array)): ?>
		<tr id="foreman">
			<td data-title="Comment">Foreman</td>
			<?php echo security_level_function('foreman', $foreman, $foreman_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('genmanager', $verify_array)): ?>
		<tr id="genmanager">
			<td data-title="Comment">General Manager</td>
			<?php echo security_level_function('genmanager', $genmanager, $genmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('hrmanager', $verify_array)): ?>
		<tr id="hrmanager">
			<td data-title="Comment">HR Manager</td>
			<?php echo security_level_function('hrmanager', $hrmanager, $hrmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('humanres', $verify_array)): ?>
		<tr id="humanres">
			<td data-title="Comment">Human Resources</td>
			<?php echo security_level_function('humanres', $humanres, $humanres_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('invmanager', $verify_array)): ?>
		<tr id="invmanager">
			<td data-title="Comment">Inventory Manager</td>
			<?php echo security_level_function('invmanager', $invmanager, $invmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('lead', $verify_array)): ?>
		<tr id="lead">
			<td data-title="Comment">Lead</td>
			<?php echo security_level_function('lead', $lead, $lead_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('mainshop', $verify_array)): ?>
		<tr id="mainshop">
			<td data-title="Comment">Main Shop</td>
			<?php echo security_level_function('mainshop', $mainshop, $mainshop_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('manfmanager', $verify_array)): ?>
		<tr id="manfmanager">
			<td data-title="Comment">Manufacturing Manager</td>
			<?php echo security_level_function('manfmanager', $manfmanager, $manfmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('manager', $verify_array)): ?>
		<tr id="manager">
			<td data-title="Comment">Managers</td>
			<?php echo security_level_function('manager', $manager, $manager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('marketing', $verify_array)): ?>
		<tr id="marketing">
			<td data-title="Comment">Marketing</td>
			<?php echo security_level_function('marketing', $marketing, $marketing_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('marketingdirector', $verify_array)): ?>
		<tr id="marketingdirector">
			<td data-title="Comment">Marketing Director</td>
			<?php echo security_level_function('marketingdirector', $marketingdirector, $marketingdirector_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('mrkmanager', $verify_array)): ?>
		<tr id="mrkmanager">
			<td data-title="Comment">Marketing Manager</td>
			<?php echo security_level_function('mrkmanager', $mrkmanager, $mrkmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('master', $verify_array)): ?>
		<tr id="master">
			<td data-title="Comment">Master</td>
			<?php echo security_level_function('master', $master, $master_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('officemanager', $verify_array)): ?>
		<tr id="officemanager">
			<td data-title="Comment">Office Manager</td>
			<?php echo security_level_function('officemanager', $officemanager, $officemanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('office_admin', $verify_array)): ?>
		<tr id="office_admin">
			<td data-title="Comment">Office Admin</td>
			<?php echo security_level_function('office_admin', $office_admin, $office_admin_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('operations', $verify_array)): ?>
		<tr id="operations">
			<td data-title="Comment">Operations</td>
			<?php echo security_level_function('operations', $operations, $operations_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('opsmanager', $verify_array)): ?>
		<tr id="opsmanager">
			<td data-title="Comment">Operations Manager</td>
			<?php echo security_level_function('opsmanager', $opsmanager, $opsmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('opconsult', $verify_array)): ?>
		<tr id="opconsult">
			<td data-title="Comment">Operations Consultant</td>
			<?php echo security_level_function('opconsult', $opconsult, $opconsult_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('operationslead', $verify_array)): ?>
		<tr id="operationslead">
			<td data-title="Comment">Operations Lead</td>
			<?php echo security_level_function('operationslead', $operationslead, $operationslead_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('opcoord', $verify_array)): ?>
		<tr id="opcoord">
			<td data-title="Comment">Operations Coordinator</td>
			<?php echo security_level_function('opcoord', $opcoord, $opcoord_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('paintshop', $verify_array)): ?>
		<tr id="paintshop">
			<td data-title="Comment">Paint Shop</td>
			<?php echo security_level_function('paintshop', $paintshop, $paintshop_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('president', $verify_array)): ?>
		<tr id="president">
			<td data-title="Comment">President</td>
			<?php echo security_level_function('president', $president, $president_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('prospect', $verify_array)): ?>
		<tr id="prospect">
			<td data-title="Comment">Prospect</td>
			<?php echo security_level_function('prospect', $prospect, $prospect_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('regionalmanager', $verify_array)): ?>
		<tr id="regionalmanager">
			<td data-title="Comment">Regional Manager</td>
			<?php echo security_level_function('regionalmanager', $regionalmanager, $regionalmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('sales', $verify_array)): ?>
		<tr id="sales">
			<td data-title="Comment">Sales</td>
			<?php echo security_level_function('sales', $sales, $sales_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('safety', $verify_array)): ?>
		<tr id="safety">
			<td data-title="Comment">Safety</td>
			<?php echo security_level_function('safety', $safety, $safety_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('safetysup', $verify_array)): ?>
		<tr id="safetysup">
			<td data-title="Comment">Safety Supervisor</td>
			<?php echo security_level_function('safetysup', $safetysup, $safetysup_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('salesmarketingdirect', $verify_array)): ?>
		<tr id="salesmarketingdirect">
			<td data-title="Comment">Sales & Marketing Director</td>
			<?php echo security_level_function('salesmarketingdirect', $salesmarketingdirect, $salesmarketingdirect_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('salesdirector', $verify_array)): ?>
		<tr id="salesdirector">
			<td data-title="Comment">Sales Director</td>
			<?php echo security_level_function('salesdirector', $salesdirector, $salesdirector_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('salesmanager', $verify_array)): ?>
		 <tr id="salesmanager">
			<td data-title="Comment">Sales Manager</td>
			<?php echo security_level_function('salesmanager', $salesmanager, $salesmanager_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('shopforeman', $verify_array)): ?>
		<tr id="shopforeman">
			<td data-title="Comment">Shop Foreman</td>
			<?php echo security_level_function('shopforeman', $shopforeman, $shopforeman_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('shopworker', $verify_array)): ?>
		<tr id="shopworker">
			<td data-title="Comment">Shop Worker</td>
			<?php echo security_level_function('shopworker', $shopworker, $shopworker_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('staff', $verify_array)): ?>
		<tr id="staff">
			<td data-title="Comment">Staff</td>
			<?php echo security_level_function('staff', $staff, $staff_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('supervisor', $verify_array)): ?>
		<tr id="supervisor">
			<td data-title="Comment">Supervisor</td>
			<?php echo security_level_function('supervisor', $supervisor, $supervisor_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('suppchainlogist', $verify_array)): ?>
		<tr id="suppchainlogist">
			<td data-title="Comment">Supply Chain & Logistics</td>
			<?php echo security_level_function('suppchainlogist', $suppchainlogist, $suppchainlogist_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('supporter', $verify_array)): ?>
		<tr id="supporter">
			<td data-title="Comment">Supporter</td>
			<?php echo security_level_function('supporter', $supporter, $supporter_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('teamcolead', $verify_array)): ?>
		<tr id="teamcolead">
			<td data-title="Comment">Team Co-Lead</td>
			<?php echo security_level_function('teamcolead', $teamcolead, $teamcolead_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('teamlead', $verify_array)): ?>
		<tr id="teamlead">
			<td data-title="Comment">Team Lead</td>
			<?php echo security_level_function('teamlead', $teamlead, $teamlead_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('teammember', $verify_array)): ?>
		<tr id="teammember">
			<td data-title="Comment">Team Member</td>
			<?php echo security_level_function('teammember', $teammember, $teammember_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('vicepres', $verify_array)): ?>
		<tr id="vicepres">
			<td data-title="Comment">Vice-President</td>
			<?php echo security_level_function('vicepres', $vicepres, $vicepres_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('vpcorpdev', $verify_array)): ?>
		<tr id="vpcorpdev">
			<td data-title="Comment">VP Corporate Development</td>
			<?php echo security_level_function('vpcorpdev', $vpcorpdev, $vpcorpdev_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('vpsales', $verify_array)): ?>
		<tr id="vpsales">
			<td data-title="Comment">VP Sales</td>
			<?php echo security_level_function('vpsales', $vpsales, $vpsales_history); ?>
		<td width="5%">
  <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
</td> </tr>
	<?php endif; ?>
	<?php if(in_array('waterspec', $verify_array)): ?>
		<tr id="waterspec">
			<td data-title="Comment">Water Specialist</td>
			<?php echo security_level_function('waterspec', $waterspec, $waterspec_history); ?>
			<td width="5%">
				<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option();">
				<img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
			</td>
		</tr>
	<?php endif; ?>-->
		<?php $security_levels = mysqli_query($dbc, "SELECT * FROM `security_level_names` WHERE `identifier` LIKE 'FFMCUST_%'");
		$level = mysqli_fetch_assoc($security_levels);
		do { ?>
			<tr id="Admin">
				<td data-title="Comment"><input type="text" class="form-control" value="<?= $level['label'] ?>" onchange="renameLevel(this);"></td>
				<?php echo security_level_function($level['identifier'], $level['active'] == 1, $level['history']); ?>
			</tr>
		<?php } while($level = mysqli_fetch_assoc($security_levels)); ?>
		</tbody>
	</table>
	<script type="text/javascript">
		$("tbody").sortable({
            stop: function(e, ui) {
								var current = ui.item[0].id;
								var previous = ui.item[0].nextElementSibling.id;
        				var next = ui.item[0].previousElementSibling.id;
								$.ajax({    //create an ajax request to ajax_all.php
									type: "GET",
									url: "/ajax_all.php?fill=security_level_order&current="+current+"&previous="+previous+"&next="+next,
									dataType: "html",   //expect html to be returned
									success: function(response){
									}
								});
            }
        }).disableSelection();
	</script>
	<?php
	function security_level_function($field, $value, $history) { ?>
		<td data-title="Activate"><input type="radio" <?= $value ? "checked" : '' ?> onchange="securityConfig(this)" name="<?php echo $field;?>" value="turn_on" id="<?php echo $field;?>_turn_on" style="height:20px;width:20px;">
		</td>
		<td data-title="Deactivate"><input type="radio" <?= $value ? "" : 'checked' ?> onchange="securityConfig(this)" name="<?php echo $field;?>" value="turn_off" id="<?php echo $field;?>_turn_off" style="height:20px;width:20px;">
		</td>
		<td data-title="History"><?php if($history == "") {
			echo "-";
		} else {
			echo "<a onclick='load_history(\"$field\"); return false;' href=''>View All</a>";
		}
		if(strpos($field, 'FFMCUST_') !== FALSE) {
			echo '<img class="inline-img" src="../img/icons/ROOK-add-icon.png" onclick="add_custom_row();">';
		} ?></td>
	<?php } ?>
	</div>
	</div>
</div>
