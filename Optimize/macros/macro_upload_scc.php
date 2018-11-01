<?php // CDS Sleep Country Macro
include_once ('include.php');
error_reporting(0);

if(isset($_POST['upload_file']) && !empty($_FILES['csv_file']['tmp_name'])) {
	$file_name = $_FILES['csv_file']['tmp_name'];
	$handle = fopen($file_name, 'r');
	$default_status = get_config($dbc, 'ticket_default_status');
	$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	$default_services = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services_service_templates` WHERE `deleted`=0 AND `contactid`='$businessid'"))['serviceid'];
    $service_est_time = 0;
    foreach(explode(',',$default_services) as $serviceid) {
        $est_hours = explode(':',$dbc->query("SELECT `estimated_hours` FROM `services` WHERE `serviceid`='$serviceid'")->fetch_assoc()['estimated_hours']);
        $service_est_time += $est_hours[0] * 1 + $est_hours[1] / 60 + $est_hours[2] / 3600;
    }
    $increment_time = get_config($dbc, 'scheduling_increments');
    $increment_time = ($increment_time > 0 ? $increment_time * 60 : 1800);
    $business = $dbc->query("SELECT * FROM `contacts` WHERE `contactid`='$businessid'")->fetch_assoc();
	$business_name = decryptIt($business['name']);
    $bus_address = '';
    $bus_city = '';
    $bus_postal_code = '';
    if(!empty($business['mailing_address'])) {
        $bus_address = $business['mailing_address'];
        $bus_city = $business['city'];
        $bus_postal_code = empty($business['postal_code']) ? $business['zip_code'] : $business['postal_code'];
    } else if(!empty($business['business_street'])) {
        $bus_address = decryptIt($business['business_street']);
        $bus_city = decryptIt($business['business_city']);
        $bus_postal_code = decryptIt($business['business_zip']);
    }
	$region = array_values(array_filter(explode(',',$business['region'])))[0];
	$classification = array_values(array_filter(explode(',',$business['classification'])))[0];
    
	$date = '';
	$ticket_type = filter_var($_POST['ticket_type'],FILTER_SANITIZE_STRING);
    $duration = strtotime(get_config($dbc, 'scheduling_increments').' minutes') - strtotime('0 minutes');
    if(!($duration > 0)) {
        $duration = 900;
    }

	$current_order = '';
	$ticket_list = [];
    $warehouse_start_time = get_config($dbc, 'ticket_warehouse_start_time');
    $to_do_start_time = $warehouse_start_time;
	while ($csv = fgetcsv($handle)) {
		if(count(array_filter($csv)) < 5) {
			continue;
		}
		$salesorderid = filter_var($csv[0],FILTER_SANITIZE_STRING);
        if($current_order != $salesorderid && $current_order != '' && $date != '' && $ticketid > 0) {
            $dbc->query("INSERT INTO `ticket_schedule` (`ticketid`,`type`,`to_do_date`,`to_do_start_time`,`to_do_end_time`,`client_name`,`address`,`city`,`postal_code`,`order_number`,`status`) VALUES ('$ticketid','warehouse','".$date."','".$to_do_start_time."','".$to_do_start_time."','".$business_name."','".$bus_address."','".$bus_city."','".$bus_postal_code."','".$current_order."','$default_status')");
        }
		$order_number = filter_var($csv[2],FILTER_SANITIZE_STRING);
		$client_name = filter_var($csv[3],FILTER_SANITIZE_STRING);
		$address = filter_var($csv[4],FILTER_SANITIZE_STRING);
		$city = filter_var($csv[5],FILTER_SANITIZE_STRING);
		$to_do_date = date('Y-m-d',strtotime($csv[8]));
		$details = filter_var($csv[13],FILTER_SANITIZE_STRING);
		$est_time = filter_var($csv[14],FILTER_SANITIZE_STRING);
		$date = empty($to_do_date) ? $date : $to_do_date;
		if($salesorderid == $current_order) {
            $to_do_start_time = $to_do_end_time;
            $to_do_end_time = date('H:i',strtotime($to_do_start_time) + (empty($est_time) ? 300 : strtotime($est_time) - strtotime('00:00')));

			echo "<!--Same Ticket...";
		} else {
			$current_order = $salesorderid;
			$to_do_start_time = date('H:i',strtotime($warehouse_start_time));

            $to_do_end_time = date('H:i',strtotime($to_do_start_time) + (empty($est_time) ? 300 : strtotime($est_time) - strtotime('00:00')));
			echo "<!--INSERT INTO `tickets` (`ticket_type`,`businessid`,`region`,`classification`, `salesorderid`, `created_by`, `ticket_label`, `ticket_label_date`, `heading`) VALUES ('$ticket_type','$businessid','$region','$classification','$salesorderid','".$_SESSION['contactid']."','$business_name - $salesorderid',NOW(),'$business_name - $salesorderid')";
			$dbc->query("INSERT INTO `tickets` (`ticket_type`,`businessid`,`region`,`classification`, `salesorderid`, `created_by`, `ticket_label`, `ticket_label_date`, `heading`) VALUES ('$ticket_type','$businessid','$region','$classification','$salesorderid','".$_SESSION['contactid']."','$business_name - $salesorderid',NOW(),'$business_name - $salesorderid')");

			$ticketid = $dbc->insert_id;
			$ticket_list[] = $ticketid;
			$dbc->query("INSERT INTO `ticket_history` (`ticketid`,`userid`,`src`,`description`) VALUES ($ticketid,".$_SESSION['contactid'].",'optimizer','Sleep Country macro imported ".TICKET_NOUN." $ticketid')");
            $dbc->query("INSERT INTO `ticket_schedule` (`ticketid`,`type`,`to_do_date`,`to_do_start_time`,`to_do_end_time`,`client_name`,`address`,`city`,`postal_code`,`order_number`,`status`) VALUES ('$ticketid','warehouse','".$date."','".$warehouse_start_time."','".$warehouse_start_time."','".$business_name."','".$bus_address."','".$bus_city."','".$bus_postal_code."','".$current_order."','$default_status')");
            $to_do_start_time = date('H:i', strtotime($to_do_end_time) + 300);
		}
        $to_do_end_time = date('H:i', strtotime($to_do_start_time) + ($service_est_time > 0 ? $service_est_time * 3600 : 1800));
		$start_available = $to_do_start_time;
		$end_available = date('H:i',strtotime($to_do_start_time.' + 3 hours'));
        $prior_attempts = $dbc->query("SELECT * FROM `ticket_schedule` WHERE CONCAT(`order_number`,'-') LIKE '$order_number-%' AND `client_name`='$client_name' AND `address`='$address' AND `city`='$city'")->num_rows;
        if($prior_attempts > 0) {
            $order_number = $order_number.'-'.($prior_attempts + 1);
        }

        $google_link = 'https://www.google.ca/maps/place/'.urlencode($address).','.urlencode($city);
		echo "INSERT INTO `ticket_schedule` (`ticketid`,`order_number`,`client_name`,`address`,`city`,`map_link`,`to_do_date`,`to_do_start_time`,`to_do_end_time`,`details`,`cust_est`,`est_time`,`start_available`,`end_available`,`serviceid`,`type`,`status`) VALUES ('$ticketid','$order_number','$client_name','$address','$city','$google_link','$to_do_date','$to_do_start_time','$to_do_end_time','$details','$est_time','$service_est_time','$start_available','$end_available','$default_services','".get_config($dbc, 'delivery_type_default')."','$default_status')-->";
		$dbc->query("INSERT INTO `ticket_schedule` (`ticketid`,`order_number`,`client_name`,`address`,`city`,`map_link`,`to_do_date`,`to_do_start_time`,`to_do_end_time`,`details`,`cust_est`,`est_time`,`start_available`,`end_available`,`serviceid`,`type`,`status`) VALUES ('$ticketid','$order_number','$client_name','$address','$city','$google_link','$to_do_date','$to_do_start_time','$to_do_end_time','$details','$est_time','$service_est_time','$start_available','$end_available','$default_services','".get_config($dbc, 'delivery_type_default')."','$default_status')");
	}
    if($current_order != $salesorderid && $current_order != '' && $date != '' && $ticketid > 0) {
        $dbc->query("INSERT INTO `ticket_schedule` (`ticketid`,`type`,`to_do_date`,`to_do_start_time`,`client_name`,`address`,`city`,`postal_code`,`order_number`,`status`) VALUES ('$ticketid','warehouse','".$date."','".$to_do_end_time."','".$business_name."','".$bus_address."','".$bus_city."','".$bus_postal_code."','".$current_order."','$default_status')");
    }
	fclose($handle); ?>
    <script>
	alert('The CSV has been imported!');
    </script>
<?php }
if((isset($_POST['upload_file']) && !empty($_FILES['csv_file']['tmp_name'])) || $_GET['access'] == 'prior') {
    if(empty($date) && !empty($_GET['date'])) {
        $date = $_GET['date'];
    }
    if(empty($ticket_type) && !empty($_GET['ticket_type'])) {
        $ticket_type = $_GET['ticket_type'];
    } ?>
    <script src="../Calendar/map_sorting.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= DIRECTIONS_KEY ?>"></script>
	<script>
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
						var day_start = $('[name=day_start_time]').val();
						$('[name=day_start_time]').val('00:00').change();
						$.ajax({
                            async: false,
                            url: 'optimize_ajax.php?action=assign_ticket_deliveries',
                            method: 'POST',
                            data: {
                                equipment: equipment,
                                ticket: ticketid,
                                start: day_start,
                                increment: '30 minutes'
                            },
                            success: function(date) {
                                sort_by_map(date, equipment, '', '', true, day_start);
                                $('.ticket_list').data('ids',$('.ticket_list').data('ids').filter(function(str) { return str != ticketid; }));
                                get_details();
                                $('[name=day_start_time]').val('00:00').change();
                                initInputs();
                                ticketid = '';
                                equipment = '';
                            }
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
    <?php $current_offset = '';
    foreach(array_filter(explode(',',$region)) as $cur_region) {
        foreach(explode(',',get_config($dbc, 'contacts_region')) as $i => $region_name) {
            if($region_name == $cur_region && $region_name != '') {
                $current_offset = explode(',',get_config($dbc, 'region_time_offset'))[$i];
            }
        }
    }
    foreach(array_filter(explode(',',$classification)) as $cur_class) {
        foreach(explode(',',get_config($dbc, 'contacts_classification')) as $i => $class_name) {
            if($class_name == $cur_class && $class_name != '') {
                $current_offset = explode(',',get_config($dbc, 'classification_time_offset'))[$i];
            }
        }
    }
    $current_offset = get_offset_from_zone($current_offset);
    $default_offset = get_offset_from_zone(''); ?>
     <?php // echo $date == date('Y-m-d') ? 'data-datetimepicker-mintime="'.date('H:i', time() - $default_offset + $current_offset).'"' : ''; ?>
	<input type="text" style="height:0;width:0;border:0; padding:0;" class="datetimepicker" name="day_start_time">
	<h4 class="no-gap"><?= !empty($date) ? 'Date: '.$date.' ' : ''?><?= !empty($region) ? 'Region: '.$region.' ' : ''?><?= !empty($classification) ? 'Classification: '.$classification.' ' : ''?></h4>
	<div class="assign_list_box" style="height: 20em;position:relative;width:calc(100% - 2px);">
		<div class="equip_list" style="display:inline-block; height:calc(100% - 7em); width:20%; float:left; overflow-y:auto;"></div>
		<div class="ticket_list" data-ids="<?= json_encode($ticket_list) ?>" style="display:inline-block; height:calc(100% - 7em); width:80%; float:right; overflow-y:auto;"></div>
	</div>
<?php } else { ?>
    <!--<a href="?tab=macros&access=prior&macro=<?= $_GET['macro'] ?>" class="btn brand-btn pull-right">See Prior Uploads</a>-->
	<h1>Sleep Country Macro</h1>
	<form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
		<ol>
			<li>Select the business to which the deliveries will be attached.</li>
			<li>Upload your CSV file using the File Uploader.</li>
			<li>Press the Submit button to run the macro and import the file.</li>
			<br>
			<p>
				<select class="chosen-select-deselect" data-placeholder="Select <?= BUSINESS_CAT ?>" name="businessid"><option />
					<?php foreach(sort_contacts_query($dbc->query("SELECT `name`, `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `deleted`=0 AND `status` > 0 AND (`classification` IN ('".implode("','",$cur_bus)."') OR `contactid` IN ('".implode("','",$cur_bus)."') OR '' IN ('".implode("','",$cur_bus)."') OR 'ALL' IN ('".implode("','",$cur_bus)."'))")) as $business) { ?>
						<option value="<?= $business['contactid'] ?>"><?= $business['full_name'] ?></option>
					<?php } ?>
				</select>
				<input type="file" name="csv_file" class="form-control">
				<input type="hidden" name="ticket_type" value="<?= $cur_macro[1] ?>">
				<input type="submit" name="upload_file" value="Submit" class="btn brand-btn">
			</p>
		</ol>
	</form>
<?php } ?>