<?php // CDS Sleep Country Macro
include_once ('include.php');
error_reporting(0);

if(isset($_POST['upload_file']) && !empty($_FILES['csv_file']['tmp_name'])) {
	$file_name = $_FILES['csv_file']['tmp_name'];
	$handle = fopen($file_name, 'r');
	$default_status = get_config($dbc, 'ticket_default_status');
	$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	$business = $dbc->query("SELECT * FROM `contacts` WHERE `contactid`='$businessid'")->fetch_assoc();
	$business_name = decryptIt($business['name']);
	$region = $business['region'];
	$classification = $business['classification'];
	$date = '';
	$ticket_type = filter_var($_POST['ticket_type'],FILTER_SANITIZE_STRING);

	$current_order = '';
	$ticket_list = [];
	$to_do_start_time = date('H:i',strtotime($to_do_start_time));
	while ($csv = fgetcsv($handle)) {
		if(count(array_filter($csv)) < 5) {
			continue;
		}
		$salesorderid = filter_var($csv[0],FILTER_SANITIZE_STRING);
		$order_number = filter_var($csv[2],FILTER_SANITIZE_STRING);
		$client_name = filter_var($csv[3],FILTER_SANITIZE_STRING);
		$address = filter_var($csv[4],FILTER_SANITIZE_STRING);
		$city = filter_var($csv[5],FILTER_SANITIZE_STRING);
		$to_do_date = date('Y-m-d',strtotime($csv[8]));
		$details = filter_var($csv[13],FILTER_SANITIZE_STRING);
		$est_time = filter_var($csv[14],FILTER_SANITIZE_STRING);
		$date = empty($to_do_date) ? $date : $to_do_date;
		if($salesorderid == $current_order) {
			$to_do_start_time = date('H:i',strtotime($to_do_start_time.' + 30 minutes'));
			echo "<!--Same Ticket...";
		} else {
			$current_order = $salesorderid;
			$to_do_start_time = '08:00';
			echo "<!--INSERT INTO `tickets` (`ticket_type`,`businessid`,`region`,`classification`, `salesorderid`, `created_by`, `ticket_label`, `ticket_label_date`, `heading`) VALUES ('$ticket_type','$businessid','$region','$classification','$salesorderid','".$_SESSION['contactid']."','$business_name - $salesorderid',NOW(),'$business_name - $salesorderid')";
			$dbc->query("INSERT INTO `tickets` (`ticket_type`,`businessid`,`region`,`classification`, `salesorderid`, `created_by`, `ticket_label`, `ticket_label_date`, `heading`) VALUES ('$ticket_type','$businessid','$region','$classification','$salesorderid','".$_SESSION['contactid']."','$business_name - $salesorderid',NOW(),'$business_name - $salesorderid')");
			$ticketid = $dbc->insert_id;
			$ticket_list[] = $ticketid;
			$dbc->query("INSERT INTO `ticket_history` (`ticketid`,`userid`,`src`,`description`) VALUES ($ticketid,".$_SESSION['contactid'].",'optimizer','Sleep Country macro imported ".TICKET_NOUN." $ticketid')");
		}
		$start_available = $to_do_start_time;
		$end_available = date('H:i',strtotime($to_do_start_time.' + 3 hours'));
		echo "INSERT INTO `ticket_schedule` (`ticketid`,`order_number`,`client_name`,`address`,`city`,`to_do_date`,`to_do_start_time`,`details`,`cust_est`,`start_available`,`end_available`,`type`,`status`) VALUES ('$ticketid','$order_number','$client_name','$address','$city','$to_do_date','$to_do_start_time','$details','$est_time','$start_available','$end_available','Drop Off','$default_status')-->";
		$dbc->query("INSERT INTO `ticket_schedule` (`ticketid`,`order_number`,`client_name`,`address`,`city`,`to_do_date`,`to_do_start_time`,`details`,`cust_est`,`start_available`,`end_available`,`type`,`status`) VALUES ('$ticketid','$order_number','$client_name','$address','$city','$to_do_date','$to_do_start_time','$details','$est_time','$start_available','$end_available','Drop Off','$default_status')");
	}
	fclose($handle); ?>
	<script>
	alert('The CSV has been imported!');
	function get_details() {
		equip_scroll = $('.equip_list').scrollTop();
		$('.equip_list').html('<h4>Loading Equipment...</h4>').load(encodeURI('assign_equipment_list.php?date=<?= $date ?>&region=<?= $region ?>&classification=<?= $classification ?>&staff_only=1'));
		$('.ticket_list').html('<h4>Loading <?= TICKET_TILE ?>...</h4>').load(encodeURI('assign_imported_tickets.php?unassign_type=<?= $ticket_type ?>&ids='+$('.ticket_list').data('ids')), function() { setTicketAssign(); });
	}
	var ticketid = '';
	var equipment = '';
	function setTicketAssign() {
		$( ".ticket_list" ).sortable({
			beforeStop: function(e, ticket) {
				var block = $('.block-item.equipment.active').first();
				if(block.length > 0) {
					equipment = block.data('id');
					ticketid = ticket.item.data('ticketid');
					$('[name=day_start_time]').focus();
					$('.ui-datepicker-close').click(function() {
						$('.ui-datepicker-close').off('click');
						var day = this.value;
						this.value = '';
						$.post('optimize_ajax.php?action=assign_ticket_deliveries', {
							equipment: equipment,
							ticket: ticketid,
							start: $('[name=day_start_time]').val(),
							increment: '30 minutes'
						}, function(response) {
							$('.ticket_list').data('ids',$('.ticket_list').data('ids').filter(function(str) { return str != ticketid; }));
							get_details();
							$('[name=day_start_time]').val('');
							initInputs();
							ticketid = '';
							equipment = '';
						});
					});
				}
			},
			delay: 0,
			handle: ".drag-handle",
			items: "span.block-item.ticket",
			sort: function(e, ticket) {
				block = $(document.elementsFromPoint(e.clientX, e.clientY)).filter('.block-item.equipment').not('.ui-sortable-helper').first();
				$('.block-item.equipment.active').removeClass('active');
				block.addClass('active');
			},
			start: function(e, ticket) {
				ticket.helper.css('width','18em');
			}
		});
	}
	$(document).ready(function() {
		get_details();
	});
	</script>
	<input type="text" style="height:0;width:0;border:0; padding:0;" class="datetimepicker" name="day_start_time">
	<h4 class="no-gap"><?= !empty($date) ? 'Date: '.$date.' ' : ''?><?= !empty($region) ? 'Region: '.$region.' ' : ''?><?= !empty($classification) ? 'Classification: '.$classification.' ' : ''?></h4>
	<div class="assign_list_box" style="height: 20em;position:relative;width:calc(100% - 2px);">
		<div class="equip_list" style="display:inline-block; height:calc(100% - 7em); width:20%; float:left; overflow-y:auto;"></div>
		<div class="ticket_list" data-ids="<?= json_encode($ticket_list) ?>" style="display:inline-block; height:calc(100% - 7em); width:80%; float:right; overflow-y:auto;"></div>
	</div>
<?php } else { ?>
	<h1>Sleep Country Macro</h1>
	<form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
		<ol>
			<li>Select the business to which the deliveries will be attached.</li>
			<li>Upload your CSV file using the File Uploader.</li>
			<li>Press the Submit button to run the macro and import the file.</li>
			<br>
			<p>
				<select class="chosen-select-deselect" data-placeholder="Select <?= BUSINESS_CAT ?>" name="businessid"><option />
					<?php foreach(sort_contacts_query($dbc->query("SELECT `name`, `contactid` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `deleted`=0 AND `status` > 0")) as $business) { ?>
						<option value="<?= $business['contactid'] ?>"><?= $business['name'] ?></option>
					<?php } ?>
				</select>
				<input type="file" name="csv_file" class="form-control">
				<input type="hidden" name="ticket_type" value="<?php foreach($macro_list as $macro) {
					if($macro[0] == $_GET['macro']) {
						echo $macro[1];
					}
				} ?>">
				<input type="submit" name="upload_file" value="Submit" class="btn brand-btn">
			</p>
		</ol>
	</form>
<?php } ?>