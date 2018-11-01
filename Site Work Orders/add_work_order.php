<?php include('../include.php');
error_reporting(0);

if(isset($_POST['submit'])) {
	$workorderid = $_POST['submit'];
	$service_code = filter_var($_POST['service_code'],FILTER_SANITIZE_STRING);
	$status = $_POST['status'];
	$id_label = filter_var($_POST['id_label'],FILTER_SANITIZE_STRING);
	/*$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	if(!is_numeric($businessid) && $businessid != '') {
		$tile_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` LIKE '%_tabs' AND `value` LIKE '%Business%' ORDER BY `name` LIKE 'contacts3%' DESC, `name` LIKE 'contacts%' DESC, `name` LIKE 'clientinfo%' DESC, `name` LIKE 'contactsrolodex%' DESC"));
		$insert_tile = explode('_', $tile_name['name'])[0];
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `name`) VALUES ('$insert_tile', 'Business', '".encryptIt($businessid)."')");
		$businessid = mysqli_insert_id($dbc);
	}*/
	$siteid = filter_var($_POST['siteid'],FILTER_SANITIZE_STRING);
	if(!is_numeric($siteid) && $siteid != '') {
		$businessid = 0;
		$tile_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` LIKE '%_tabs' AND `value` LIKE '%Sites%' ORDER BY `name` LIKE 'contacts3%' DESC, `name` LIKE 'contacts%' DESC, `name` LIKE 'clientinfo%' DESC, `name` LIKE 'contactsrolodex%' DESC"));
		$insert_tile = explode('_', $tile_name['name'])[0];
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `businessid`, `site_name`) VALUES ('$insert_tile', 'Sites', '$businessid', '$siteid')");
		$siteid = mysqli_insert_id($dbc);
	} else {
		$businessid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `businessid` FROM `contacts` WHERE `contactid`='$siteid'"))['businessid'];
	}
	$contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
	if(!is_numeric($contactid) && $contactid != '') {
		$tile_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` LIKE '%_tabs' AND `value` LIKE '%Customers%' ORDER BY `name` LIKE 'contacts3%' DESC, `name` LIKE 'contacts%' DESC, `name` LIKE 'clientinfo%' DESC, `name` LIKE 'contactsrolodex%' DESC"));
		$insert_tile = explode('_', $tile_name['name'])[0];
		$first_name = explode(' ', $contactid)[0];
		$last_name = encryptIt(trim(str_replace($first_name, '', $contactid)));
		$first_name = encryptIt(trim($first_name));
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `businessid`, `siteid`, `first_name`, `last_name`) VALUES ('$insert_tile', 'Customers', '$businessid', '$siteid', '$first_name', '$last_name')");
		$contactid = mysqli_insert_id($dbc);
	}
	$staff_lead = filter_var($_POST['staff_lead'],FILTER_SANITIZE_STRING);
	$staff_crew_count = $_POST['staff_crew_count'];
	$staff_crew = [];
	$staff_positions = [];
	$staff_estimate_hours = [];
	$staff_estimate_days = [];
	for ($i = 0; $i < $staff_crew_count; $i++) {
		foreach ($_POST['staff_crew_'.$i] as $single_staff) {
			$staff_crew[] = empty($single_staff) ? '' : $single_staff;
			$staff_positions[] = empty($_POST['staff_positions_'.$i]) ? '' : $_POST['staff_positions_'.$i];
			$staff_estimate_hours[] = empty($_POST['staff_estimate_hours_'.$i]) ? '' : $_POST['staff_estimate_hours_'.$i];
			$staff_estimate_days[] = empty($_POST['staff_estimate_days_'.$i]) ? '' : $_POST['staff_estimate_days_'.$i];
		}
	}
	foreach($staff_crew as $key => $value) {
		if($value == '') {
			unset($staff_crew[$key]);
			unset($staff_positions[$key]);
			unset($staff_estimate_hours[$key]);
			unset($staff_estimate_days[$key]);
		}
	}
	$staff_crew = implode(',',$staff_crew);
	$staff_positions = implode(',',$staff_positions);
	$staff_estimate_hours = filter_var(implode(',',$staff_estimate_hours),FILTER_SANITIZE_STRING);
	$staff_estimate_days = filter_var(implode(',',$staff_estimate_days),FILTER_SANITIZE_STRING);
	foreach($_POST['service_cat'] as $key => $value) {
		if($value == '') {
			unset($_POST['service_cat'][$key]);
			unset($_POST['service_head'][$key]);
			unset($_POST['service_rates'][$key]);
		}
	}
	$service_cat = filter_var(implode('#*#',$_POST['service_cat']),FILTER_SANITIZE_STRING);
	$service_head = filter_var(implode('#*#',$_POST['service_head']),FILTER_SANITIZE_STRING);
	$service_rates = filter_var(implode('#*#',$_POST['service_rates']),FILTER_SANITIZE_STRING);
	foreach($_POST['equipment_id'] as $key => $value) {
		if($value == '') {
			unset($_POST['equipment_id'][$key]);
			unset($_POST['equipment_rate'][$key]);
			unset($_POST['equipment_status'][$key]);
		} else {
			mysqli_query($dbc, "UPDATE `equipment` SET `status`='".$_POST['equipment_status'][$key]."' WHERE `equipmentid`='$value'");
		}
	}
	$equipment_id = implode(',',$_POST['equipment_id']);
	$equipment_rate = implode(',',$_POST['equipment_rate']);
	$equipment_status = implode(',',$_POST['equipment_status']);
	foreach($_POST['material_id'] as $key => $value) {
		if($value == '') {
			unset($_POST['material_id'][$key]);
			unset($_POST['material_qty'][$key]);
		}
	}
	$material_id = implode('#*#',$_POST['material_id']);
	$material_qty = filter_var(implode(',',$_POST['material_qty']),FILTER_SANITIZE_STRING);
	$site_location = filter_var($_POST['site_location'],FILTER_SANITIZE_STRING);
	$site_description = filter_var(htmlentities($_POST['site_description']),FILTER_SANITIZE_STRING);
	$google_map_link = filter_var($_POST['google_map_link'],FILTER_SANITIZE_STRING);
	$work_start_date = filter_var($_POST['work_start_date'],FILTER_SANITIZE_STRING);
	$work_end_date = filter_var($_POST['work_end_date'],FILTER_SANITIZE_STRING);
	$work_start_time = filter_var(implode(',',$_POST['work_start_time']),FILTER_SANITIZE_STRING);
	$work_start_details = filter_var(implode(',',$_POST['work_start_details']),FILTER_SANITIZE_STRING);
	$po_list = [];
	foreach($_POST['po_list'] as $row => $poid) {
		if($_POST['attach_po_'.$poid] == 'attach') {
			$po_list[] = $poid;
			$markup = $_POST['mark_up'][$row];
			mysqli_query($dbc, "UPDATE `site_work_po` SET `mark_up`='$markup' WHERE `poid`='$poid'");
		}
	}
	$po_id = implode(',',$po_list);
	$comments = filter_var(htmlentities($_POST['comments']),FILTER_SANITIZE_STRING);
	$addendum_comments = filter_var(htmlentities($_POST['addendum_comments']),FILTER_SANITIZE_STRING);

	if($workorderid == 'NEW') {
		$work_order_sql = "INSERT INTO `site_work_orders` (`status`, `service_code`, `businessid`, `siteid`, `contactid`, `staff_lead`, `staff_crew`, `staff_positions`, `staff_estimate_hours`, `staff_estimate_days`, `service_cat`, `service_heading`, `service_rates`, `equipment_id`, `equipment_rate`, `equipment_status`, `material_id`, `material_qty`, `site_location`, `site_description`, `google_map_link`, `work_start_date`, `work_end_date`, `work_start_time`, `work_start_details`, `po_id`)
			VALUES ('$status', '$service_code', '$businessid', '$siteid', '$contactid', '$staff_lead', '$staff_crew', '$staff_positions', '$staff_estimate_hours', '$staff_estimate_days', '$service_cat', '$service_head', '$service_rates', '$equipment_id', '$equipment_rate', '$equipment_status', '$material_id', '$material_qty', '$site_location', '$site_description', '$google_map_link', '$work_start_date', '$work_end_date', '$work_start_time', '$work_start_details', '$po_id')";
	} else {
		$work_order_sql = "UPDATE `site_work_orders` SET `status`='$status', `service_code`='$service_code', `businessid`='$businessid', `siteid`='$siteid', `contactid`='$contactid', `id_label`='$id_label', `staff_lead`='$staff_lead', `staff_crew`='$staff_crew', `staff_positions`='$staff_positions', `staff_estimate_hours`='$staff_estimate_hours', `staff_estimate_days`='$staff_estimate_days', `service_cat`='$service_cat', `service_heading`='$service_head', `service_rates`='$service_rates', `equipment_id`='$equipment_id', `equipment_rate`='$equipment_rate', `equipment_status`='$equipment_status', `material_id`='$material_id', `material_qty`='$material_qty', `site_location`='$site_location', `site_description`='$site_description', `google_map_link`='$google_map_link', `work_start_date`='$work_start_date', `work_end_date`='$work_end_date', `work_start_time`='$work_start_time', `work_start_details`='$work_start_details', `po_id`='$po_id' WHERE `workorderid`='$workorderid'";
	}

	$result = mysqli_query($dbc, $work_order_sql);

	if($workorderid == 'NEW') {
		$workorderid = mysqli_insert_id($dbc);
		$label_id = $workorderid.'-'.(empty($service_code) ? '' : $service_code.'-').date('Y-m-d');
		mysqli_query($dbc, "UPDATE `site_work_orders` SET `id_label`='$label_id' WHERE `workorderid`='$workorderid'");
	}
	$status = mysqli_fetch_array(mysqli_query($dbc, "SELECT `status` FROM `site_work_orders` WHERE `workorderid`='$workorderid'"))['status'];

	$checklists = trim(filter_var($_POST['checklists'],FILTER_SANITIZE_STRING),',');
	$result = mysqli_query($dbc, "UPDATE `site_work_checklist` SET `workorderid`='$workorderid' WHERE `checklistid` IN ($checklists)");

	//Comment
	$note_heading = filter_var($_POST['note_heading'],FILTER_SANITIZE_STRING);
	$type = 'note';
	$comments = filter_var(htmlentities($_POST['comments']),FILTER_SANITIZE_STRING);
	if($comments != '') {
		$email_comment = $_POST['email_comment'];
		$query_insert_ca = "INSERT INTO `site_work_comment` (`workorderid`, `comment`, `created_date`, `created_by`, `type`, `note_heading`) VALUES ('$workorderid', '$comments', '$created_date', '$created_by', '$type', '$note_heading')";
		$result_insert_ca = mysqli_query($dbc, $query_insert_ca);

		if($_POST['send_email_on_comment'] == 'Yes') {
			$email = $_POST['comment_email_sender'];
			$email_name_result = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `email_address` = '".encryptIt($email)."' OR `office_email` = '".encryptIt($email)."'");
			if($email != '' && $email_name_id == mysqli_fetch_array($email_name_result)) {
				$from = [$email => get_contact($dbc, $email_name_id['contactid'])];
			} else if($email != '') {
				$from = [$email => get_contact($dbc, $_SESSION['contactid'])];
			} else {
				$from = '';
			}

			$subject = $_POST['comment_email_subject'];
			$message = str_replace(['[NOTE]','[WORKORDERID]'], [$_POST['comments'],$workorderid,get_client($dbc,$businessid),$heading,$status], $_POST['comment_email_body']).get_contact($dbc, $email_name_id['contactid'], 'email_address');
			$email = get_email($dbc, $email_comment);
			try {
				send_email($from, $email, '', '', $subject, $message, '');
			} catch(Exception $e) {
				echo "<script> alert('Unable to send email. Please try again later.'); console.log('".$e->getMessage()."'); </script>";
			}
		}
	}

	//Addendum
	$note_heading = filter_var($_POST['addendum_note_heading'],FILTER_SANITIZE_STRING);
	$type = 'addendum';
	$comments = filter_var(htmlentities($_POST['addendum_comments']),FILTER_SANITIZE_STRING);
	if($comments != '') {
		$email_comment = $_POST['addendum_email_comment'];
		$query_insert_ca = "INSERT INTO `site_work_comment` (`workorderid`, `comment`, `created_date`, `created_by`, `type`, `note_heading`) VALUES ('$workorderid', '$comments', '$created_date', '$created_by', '$type', '$note_heading')";
		$result_insert_ca = mysqli_query($dbc, $query_insert_ca);

		if($_POST['addendum_send_email_on_comment'] == 'Yes') {
			$email = $_POST['addendum_comment_email_sender'];
			$email_name_result = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `email_address` = '".encryptIt($email)."' OR `office_email` = '".encryptIt($email)."'");
			if($email != '' && $email_name_id == mysqli_fetch_array($email_name_result)) {
				$from = [$email => get_contact($dbc, $email_name_id['contactid'])];
			} else if($email != '') {
				$from = [$email => get_contact($dbc, $_SESSION['contactid'])];
			} else {
				$from = '';
			}

			$subject = $_POST['addendum_comment_email_subject'];
			$message = str_replace(['[ADDENDUM]','[WORKORDERID]'], [$_POST['addendum_comments'],$workorderid,get_client($dbc,$businessid),$heading,$status], $_POST['addendum_comment_email_body']).get_contact($dbc, $email_name_id['contactid'], 'email_address');
			$email = get_email($dbc, $email_comment);
			try {
				send_email($from, $email, '', '', $subject, $message, '');
			} catch(Exception $e) {
				echo "<script> alert('Unable to send email. Please try again later.'); console.log('".$e->getMessage()."'); </script>";
			}
		}
	}

    //Document
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        $document = $_FILES["upload_document"]["name"][$i];

        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;

        if($document != '') {
            $query_insert_client_doc = "INSERT INTO `site_work_document` (`workorderid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$workorderid', 'Support Document', '".filter_var($document, FILTER_SANITIZE_STRING)."', '$created_date', '$created_by')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }

	if($status == 'Pending') {
		echo "<script> window.location.replace('site_work_orders.php?tab=pending');</script>";
	} else {
		echo "<script> window.location.replace('site_work_orders.php?tab=active');</script>";
	}
}

include_once ('../navigation.php');
checkAuthorised('site_work_orders');

$workorderid = '';
$status = 'Pending';
$service_code = '';
$id_label = '';
$businessid = '';
$siteid = '';
$contactid = '';
$staff_lead = '';
$staff_crew = ',,';
$staff_positions = ',,';
$staff_estimate_hours = ',,';
$staff_estimate_days = ',,';
$service_cat = '#*#';
$service_head = '#*#';
$service_rates = '#*#';
$equipment_id = ',';
$equipment_rate = ',';
$equipment_status = ',';
$material_id = '#*#';
$material_qty = ',';
$site_location = '';
$site_description = '';
$google_map_link = '';
$work_start_date = '';
$work_end_date = '';
$work_start_time = '';
$work_start_details = '';
$po_id = '';
$comments = '';
if(!empty($_GET['workorderid'])) {
	$workorderid = filter_var($_GET['workorderid'],FILTER_SANITIZE_STRING);
	$work_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `site_work_orders` WHERE `workorderid`='$workorderid'"));
	$status = $work_order['status'];
	$service_code = $work_order['service_code'];
	$id_label = $work_order['id_label'];
	$businessid = $work_order['businessid'];
	$siteid = $work_order['siteid'];
	$contactid = $work_order['contactid'];
	$staff_lead = $work_order['staff_lead'];
	$staff_crew = $work_order['staff_crew'];
	$staff_positions = $work_order['staff_positions'];
	$staff_estimate_hours = $work_order['staff_estimate_hours'];
	$staff_estimate_days = $work_order['staff_estimate_days'];
	$service_cat = $work_order['service_cat'];
	$service_head = $work_order['service_heading'];
	$service_rates = $work_order['service_rates'];
	$equipment_id = $work_order['equipment_id'];
	$equipment_rate = $work_order['equipment_rate'];
	$equipment_status = $work_order['equipment_status'];
	$material_id = $work_order['material_id'];
	$material_qty = $work_order['material_qty'];
	$site_location = $work_order['site_location'];
	$site_description = $work_order['site_description'];
	$google_map_link = $work_order['google_map_link'];
	$work_start_date = $work_order['work_start_date'];
	$work_end_date = $work_order['work_end_date'];
	$work_start_time = $work_order['work_start_time'];
	$work_start_details = $work_order['work_start_details'];
	$po_id = $work_order['po_id'];
	$comments = $work_order['comments'];
}
else if(!empty($_GET['src_id'])) {
	$src_id = filter_var($_GET['src_id'],FILTER_SANITIZE_STRING);
	$work_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `site_work_orders` WHERE `workorderid`='$src_id'"));
	$service_code = $work_order['service_code'];
	$businessid = $work_order['businessid'];
	$siteid = $work_order['siteid'];
	$contactid = $work_order['contactid'];
	$staff_lead = $work_order['staff_lead'];
	$staff_crew = $work_order['staff_crew'];
	$staff_positions = $work_order['staff_positions'];
	$staff_estimate_hours = $work_order['staff_estimate_hours'];
	$staff_estimate_days = $work_order['staff_estimate_days'];
	$service_cat = $work_order['service_cat'];
	$service_head = $work_order['service_heading'];
	$service_rates = $work_order['service_rates'];
	$equipment_id = $work_order['equipment_id'];
	$equipment_rate = $work_order['equipment_rate'];
	$equipment_status = $work_order['equipment_status'];
	$site_location = $work_order['site_location'];
	$site_description = $work_order['site_description'];
	$google_map_link = $work_order['google_map_link'];
} ?>
<script>
$(document).ready(function() {
	//$('[name=businessid]').first().change(function() { business_select(this.value); });
	//business_select('<?= $businessid ?>');
	site_select('<?= $siteid ?>');
	<?php
		$accordion_list = ['who' => 'Who', 'staff' => 'Staff & Crew', 'services' => 'Services', 'equip' => 'Equipment', 'material' => 'Materials', 'where' => 'Where', 'when' => 'When', 'docs' => 'Support Documents', 'checklist' => 'Site Checklist', 'pos' => 'Purchase Orders', 'comments' => 'Comments', 'safety' => 'Safety Checklist', 'addendum' => 'Addendum'];
		$contact_position = mysqli_fetch_array(mysqli_query($dbc, "SELECT `position` FROM `contacts` WHERE `contactid` = '".$_SESSION['contactid']."'"))['position'];

		foreach ($accordion_list as $accordion => $accordion_title) {
			$permission = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` = 'swo_readonly_".$accordion."'"));
			if (!empty($permission['value'])) {
				$positions = ','.$permission['value'].',';
				if (strpos($positions, ','.$contact_position.',') !== FALSE) {
					$accordion_id = 'collapse_'.$accordion;
					echo '$("#'.$accordion_id.'").find("div.panel-body").addClass("disabled-div");';
				}
			}
		}
	?>
});
$(document).on('change', 'select[name="siteid"]', function() { site_select($(this).val()); });
$(document).on('change', 'select[name="contactid"]', function() { set_new_who(); });
$(document).on('change', 'select[name="service_cat[]"]', function() { category_filter(this); });
$(document).on('change', 'select[name="service_head[]"]', function() { select_service(this); });
$(document).on('change', 'select[name="equip_cat_value[]"]', function() { equip_filter(this); });
$(document).on('change', 'select[name="equipment_id[]"]', function() { equip_filter(this); });
$(document).on('change', 'select[name="equip_make_value[]"]', function() { equip_filter(this); });
$(document).on('change', 'select[name="equip_model_value[]"]', function() { equip_filter(this); });
$(document).on('change', 'select[name="material_category[]"]', function() { is_custom_material(this); });

function markup(markup, total) {
	var rate = markup.value;
	var total = (total * (1 + rate / 100)).toFixed(2);
	$(markup).closest('tr').find('input[name=marked_up_total]').val(total);
}

/*function business_select(id) {
	if(id != '') {
		$.ajax({
			data: { business: id, site: $('[name=siteid]').data('value'), contact: $('[name=contactid]').data('value') },
			method: 'POST',
			url: 'site_work_orders_ajax.php?fill=businessid',
			success: function(result) {
				var arr = result.split('#*#');
				$('[name=siteid]').empty().html(arr[0]).trigger('change.select2');
				$('[name=contactid]').empty().html(arr[1]).trigger('change.select2');
				set_new_who();
			}
		});
	}
	else {
		$('[name=siteid]').empty().html('<option>Please select a business first</option>').trigger('change.select2');
		$('[name=contactid]').empty().html('<option>Please select a site first</option>').trigger('change.select2');
		set_new_who();
	}
}*/
function site_select(id) {
	if(id != '') {
		$.ajax({
			data: { business: $('select[name=siteid] option:selected').data('business'), site: id, contact: $('[name=contactid]').data('value') },
			method: 'POST',
			url: 'site_work_orders_ajax.php?fill=siteid',
			success: function(result) {
				$('[name=contactid]').empty().html(result).trigger('change.select2');
				var location = $('[name=siteid] option:selected').data('name');
				if($('[name=site_location]').val() == '') {
					$('[name=site_location]').val(location);
				}
				var description = $('[name=siteid] option:selected').data('location');
				if($('[name=site_description]').val() == '') {
					$('[name=site_description]').val(description);
				}
				var google = $('[name=siteid] option:selected').data('google');
				if($('[name=google_map_link]').val() == '') {
					$('[name=google_map_link]').val(google);
				}
				$('[name=businessid]').val($('[name=siteid] option:selected').data('business'));
				set_new_who();
			}
		});
	}
	else {
		$('[name=contactid]').empty().html('<option>Please select a site first</option>').trigger('change.select2');
		set_new_who();
	}
}
function set_new_who() {
	/*var business = $('[name=businessid]').first().val();
	if(business == 'NEW') {
		$('[name=businessid]').last().removeAttr('disabled').show();
	} else {
		$('[name=businessid]').last().attr('disabled','disabled').val('').hide();
	}*/
	var site = $('[name=siteid]').first().val();
	if(site == 'NEW') {
		$('[name=siteid]').last().removeAttr('disabled').show();
	} else {
		$('[name=siteid]').last().attr('disabled','disabled').val('').hide();
	}
	var contact = $('[name=contactid]').first().val();
	if(contact == 'NEW') {
		$('[name=contactid]').last().removeAttr('disabled').show();
	} else {
		$('[name=contactid]').last().attr('disabled','disabled').val('').hide();
	}
}
function category_filter(cat) {
	var val = cat.value;
	if(val == 'custom') {
		$(cat).closest('.form-group').find('input[name^=service_]').removeAttr('disabled').closest('div').show();
		$(cat).closest('.form-group').find('select').attr('disabled','disabled').closest('div').hide();
	} else {
		$(cat).closest('.form-group').find('[name="service_head[]"] option').each(function() {
			if(val == '' || $(this).data('category') == val || this.value == 'custom') {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		$(cat).closest('.form-group').find('[name="service_head[]"]').trigger('change.select2');
	}
}
function select_service(heading) {
	if(heading.value == 'custom') {
		$(heading).closest('.form-group').find('input[name="service_head[]"]').removeAttr('disabled').closest('div').show();
		$(heading).closest('div').attr('disabled','disabled').closest('div').hide();

		var rate = $(heading).closest('.form-group').find('select[name="service_rates[]"]');
		$(rate).closest('.form-group').find('input[name="service_rates[]"]').removeAttr('disabled').closest('div').show();
		$(rate).closest('div').attr('disabled','disabled').closest('div').hide();
	} else {
		$(heading).closest('.form-group').find('[name="service_cat[]"]').val($(heading).find('option:selected').data('category')).trigger('change.select2');
		service_rate($(heading).closest('.form-group').find('select[name="service_rates[]"]'));
	}
}
function service_rate(rate) {
	$.ajax({
		method: 'POST',
		response: 'html',
		url: 'site_work_orders_ajax.php?fill=service_rates',
		data: { service: $(rate).closest('.form-group').find('[name="service_head[]"]').val(), category: $(rate).closest('.form-group').find('[name="service_cat[]"]').val(), date: $('[name=work_start_date]').val() },
		success: function(response) {
			$(rate).empty().html(response).trigger('change.select2');
		}
	});
}
function equip_filter(row) {
	row = $(row).closest('.form-group');
	var unit = $(row).closest('.form-group').find('[name="equipment_id[]"]');
	if(unit.val() != '') {
		unit = unit.find('option:selected');
		$(row).find('[name="equip_cat_value[]"]').val(unit.data('category')).trigger('change.select2');
		$(row).find('[name="equip_type_value[]"]').val(unit.data('type')).trigger('change.select2');
		$(row).find('[name="equip_make_value[]"]').val(unit.data('make')).trigger('change.select2');
		$(row).find('[name="equip_model_value[]"]').val(unit.data('model')).trigger('change.select2');
	} else {
		var cat = $(row).find('[name="equip_cat_value[]"]').val();
		var type = $(row).find('[name="equip_type_value[]"]').val();
		if(cat != '' && cat != undefined) {
			unit.find('option').each(function() {
				if($(this).data('category') != cat && $(this).val() != '') {
					$(this).hide();
				} else {
					$(this).show();
				}
			});
		}
		if(type != '' && type != undefined) {
			unit.find('option').each(function() {
				if($(this).data('type') != type && $(this).val() != '') {
					$(this).hide();
				} else {
					$(this).show();
				}
			});
		}
		unit.trigger('change.select2');
	}
}
function addCrew() {
	var staff_crew_count = $('#staff_crew_count').val();
	var staff_crew_curr = staff_crew_count - 1;
	var clone = $('.form-group.crew').last().clone();
	clone.find('.form-control').val('');
	resetChosen(clone.find("select"));
	clone.find('[name="staff_crew_' + staff_crew_curr + '[]"]').attr("name", "staff_crew_" + staff_crew_count + "[]");
	clone.find('[name="staff_positions_' + staff_crew_curr + '"]').attr("name", "staff_positions_" + staff_crew_count);
	clone.find('[name="staff_estimate_hours_' + staff_crew_curr + '"]').attr("name", "staff_estimate_hours_" + staff_crew_count);
	clone.find('[name="staff_estimate_days_' + staff_crew_curr + '"]').attr("name", "staff_estimate_days_" + staff_crew_count);
	$('#staff_crew_count').val(parseInt(staff_crew_count) + 1);
	$('#crew_btn').before(clone);
}
function addService() {
	var src = $('.form-group.service').last();
	var clone = src.clone();
	clone.find('.form-control').val('');
	clone.find('input[name^=service_]').attr('disabled','disabled').closest('div').hide();
	clone.find('select').removeAttr('disabled').closest('div').show();
	clone.find('option').show();
	resetChosen(clone.find("select"));
	$('#service_btn').before(clone);
}
function addEquip() {
	var clone = $('.form-group.equip').last().clone();
	clone.find('.form-control').val('');
	resetChosen(clone.find("select"));
	$('#equip_btn').before(clone);
}
function addMaterial() {
	var clone = $('.form-group.material').last().clone();
	clone.find('.form-control').val('');
	resetChosen(clone.find("select"));
	$('#material_btn').before(clone);
}
function addTime(btn) {
	var group = $(btn).closest('.form-group');
	var clone = group.clone();
	clone.find('input').val('').removeClass('hasDatepicker').removeAttr('id');
	group.after(clone);
	$(btn).remove();
	group.find('.col-sm-7').removeClass('col-sm-7').addClass('col-sm-8');
	$('.datetimepicker').timepicker({
		controlType: 'select',
		oneLine: true,
		timeFormat: 'hh:mm tt'
	});
}
</script>

<style>
.disabled-div {
	pointer-events: none;
}
</style>

<div class="container">
  <div class="row">

		<h1><?php echo (!empty($_GET['workorderid']) ? 'Edit Work Order #'.$id_label : 'Add A New Work Order') ?></h1>

		<div class="form-group">
			<div class="col-sm-6">
				<a href="site_work_orders.php?tab=<?= ($status == 'Pending' ? 'pending' : 'active') ?>" class="btn brand-btn btn-lg">Back to Dashboard</a>
			</div>
			<div class="clearfix"></div>
		</div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<div class="panel-group" id="accordion2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_who" >
							Who: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_who" class="panel-collapse collapse">
					<div class="panel-body">
						<!--<div class="form-group"><label class="col-sm-4 control-label">Business:</label>
							<div class="col-sm-8"><select name="businessid" class="form-control chosen-select-deselect" onchange="business_select(this.value);"><option></option><option value="NEW">New Business</option>
							<?php $business_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `category`='Business' AND `deleted`=0 AND `status`=1"),MYSQLI_ASSOC));
							foreach($business_list as $id) {
								echo "<option ".($businessid == $id ? 'selected' : '')." value='$id'>".get_client($dbc, $id)."</option>";
							} ?></select><input disabled type="text" name="businessid" value="" style="display:none;" class="form-controlsite_selectdiv></div>-->
						<input type="hidden" name="businessid" value="<?= $businessid ?>">
						<div class="form-group"><label class="col-sm-4 control-label">Site:</label>
							<div class="col-sm-8"><select name="siteid" data-value="<?= $siteid ?>" class="form-control chosen-select-deselect"><option></option>
								<?php $site_list = mysqli_query($dbc, "SELECT `contactid`, `site_name`, `google_maps_address`, `lsd`, `businessid` FROM `contacts` WHERE `category`='Sites' AND `deleted`=0 AND `status`=1 ORDER BY `site_name`");
								while($site_row = mysqli_fetch_array($site_list)) {
									echo "<option data-business='".$site_row['businessid']."' data-name='".$site_row['site_name']."' data-google='".$site_row['google_maps_address']."' data-location='".$site_row['lsd']."' ".($site_row['contactid'] == $siteid ? 'selected' : '')." value='".$site_row['contactid']."'>".$site_row['site_name']."</option>";
								} ?></select>
								<input disabled type="text" name="siteid" value="" style="dispaly:none;" class="form-control"></div></div>
						<div class="form-group"><label class="col-sm-4 control-label">Contact:</label>
							<div class="col-sm-8"><select name="contactid" data-value="<?= $contactid ?>" class="form-control chosen-select-deselect"></select>
								<input disabled type="text" name="contactid" value="" style="dispaly:none;" class="form-control"></div></div>
						<div class="form-group"><label class="col-sm-4 control-label">Service Code:</label>
							<div class="col-sm-8"><select name="service_code" class="form-control chosen-select-deselect"><option></option>
								<option <?= ($service_code == 'EM' ? 'selected' : '') ?> value="EM">EM</option>
								<option <?= ($service_code == 'Con' ? 'selected' : '') ?> value="Con">Con</option>
								<option <?= ($service_code == 'Snow' ? 'selected' : '') ?> value="Snow">Snow</option>
								<option <?= ($service_code == 'SWLP' ? 'selected' : '') ?> value="SWLP">SWLP</option>
								<option <?= ($service_code == 'Shop' ? 'selected' : '') ?> value="Shop">Shop</option>
							</select></div></div>
						<?php if($status != 'Pending') { ?>
							<div class="form-group"><label class="col-sm-4 control-label">Work Order Status:</label>
								<div class="col-sm-8"><select name="status" class="form-control chosen-select-deselect"><option></option>
									<option <?= ($status == 'Pending' ? 'selected' : '') ?> value="Pending">Revert to Pending</option>
									<option <?= ($status == 'Approved' ? 'selected' : '') ?> value="Approved">Approved</option>
									<option <?= ($status == 'Ongoing' ? 'selected' : '') ?> value="Ongoing">Ongoing</option>
									<option <?= ($status == 'Finished' ? 'selected' : '') ?> value="Finished">Finished</option>
									<option <?= ($status == 'Archived' ? 'selected' : '') ?> value="Archived">Archived</option>
								</select></div></div>
						<?php } else { ?>
							<input type="hidden" name="status" value="Pending">
						<?php }
						if($workorderid > 0) { ?>
							<div class="form-group"><label class="col-sm-4 control-label">Work Order Label:</label>
								<div class="col-sm-8">
									<input type="text" name="id_label" value="<?= $id_label ?>" class="form-control"></div></div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff" >
							Staff & Crew: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_staff" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group"><label class="col-sm-4 control-label">Company Team Lead:</label>
							<div class="col-sm-8"><select name="staff_lead" class="form-control chosen-select-deselect"><option></option>
								<?php $manual_leads = get_config($dbc, 'site_work_order_leads');
								$manual_leads = ($manual_leads == '' ? 0 : $manual_leads);
								$staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category`='Staff' AND (`position`='Team Lead' OR `role` LIKE '%teamlead%' OR `contactid` IN ($manual_leads)) AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND (`first_name` != '' OR `last_name` != '')"),MYSQLI_ASSOC));
								foreach($staff_list as $id) {
									echo "<option ".($id == $staff_lead ? 'selected' : '')." value='$id'>".get_contact($dbc, $id)."</option>";
								} ?>
								</select></div></div>
						<div class="form-group hide-titles-mob">
							<div class="col-sm-3 text-center">Staff</div>
							<div class="col-sm-3 text-center">Position</div>
							<div class="col-sm-2 text-center">Estimated Hours</div>
							<div class="col-sm-2 text-center">Estimated Days</div>
						</div>
						<?php $staff_crew = explode(',',$staff_crew);
						$staff_positions = explode(',',$staff_positions);
						$staff_estimate_hours = explode(',',$staff_estimate_hours);
						$staff_estimate_days = explode(',',$staff_estimate_days);
						$staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category`='Staff' AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
						$staff_crew_count = 0;
						foreach($staff_crew as $j => $crew) { ?>
							<div class="form-group crew">
								<div class="col-sm-3"><label class="show-on-mob">Staff:</label>
									<select multiple name="staff_crew_<?php echo $staff_crew_count; ?>[]" class="form-control chosen-select-deselect">
										<?php foreach($staff_list as $id) {
											echo "<option ".($id == $crew ? 'selected' : '')." value='$id'>".get_contact($dbc, $id)."</option>";
										} ?></select></div>
								<div class="col-sm-3"><label class="show-on-mob">Position:</label>
									<select name="staff_positions_<?php echo $staff_crew_count; ?>" class="form-control chosen-select-deselect"><option></option>
										<?php $position_list = mysqli_query($dbc, "SELECT `position_id`, `name` FROM `positions` WHERE `deleted`=0 ORDER BY `name`");
										while($row = mysqli_fetch_array($position_list)) {
											echo "<option ".($row['position_id'] == $staff_positions[$j] ? 'selected' : '')." value='".$row['position_id']."'>".$row['name']."</option>";
										} ?></select></div>
								<div class="col-sm-2"><label class="show-on-mob">Estimated Hours:</label>
									<input type="number" class="form-control" name="staff_estimate_hours_<?php echo $staff_crew_count; ?>" value="<?php echo $staff_estimate_hours[$j]; ?>" min="0" step="any"></div>
								<div class="col-sm-2"><label class="show-on-mob">Estimated Days:</label>
									<input type="number" class="form-control" name="staff_estimate_days_<?php echo $staff_crew_count; ?>" value="<?php echo $staff_estimate_days[$j]; ?>" min="0" step="any"></div>
								<div class="col-sm-1"><button class="btn brand-btn" onclick="$(this).closest('.form-group').remove();">Delete</button></div>
							</div>
						<?php
						$staff_crew_count++;
						} ?>
						<button class="btn brand-btn pull-right" id="crew_btn" onclick="addCrew(); return false;">Add Crew</button>
						<input type="hidden" id="staff_crew_count" name="staff_crew_count" value="<?php echo count($staff_crew); ?>">
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_services" >
							Services: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_services" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group hide-titles-mob">
							<div class="col-sm-4 text-center">Category</div>
							<div class="col-sm-4 text-center">Heading</div>
							<div class="col-sm-3 text-center">Rate</div>
						</div>
						<?php $service_cat = explode('#*#',$service_cat);
						$service_head = explode('#*#',$service_head);
						$service_rates = explode('#*#',$service_rates);
						foreach($service_cat as $j => $cat) { ?>
							<div class="form-group service">
								<div class="col-sm-4"><label class="show-on-mob">Category:</label>
									<select name="service_cat[]" class="form-control chosen-select-deselect"><option></option>
										<option value="custom">Add Custom</option>
										<option <?= (!empty($cat) ? 'selected' : '') ?> value='<?php echo $cat; ?>'><?php echo $cat; ?></option>
										<?php $service_categories = mysqli_query($dbc, "SELECT DISTINCT `category` FROM `services` ORDER BY `category`");
										$custom_cat = true;
										while($row = mysqli_fetch_array($service_categories)) {
											if($cat == $row['category']) {
												$custom_cat = false;
											}
											echo "<option ".($cat == $row['category'] ? 'selected' : '')." value='".$row['category']."'>".$row['category']."</option>";
										}
										if($custom_cat) {
											echo "<option selected value='$cat'>$cat</option>";
										} ?></select></div>
								<div class="col-sm-4" style="display:none;"><label class="show-on-mob">Service Category:</label>
									<input disabled name="service_cat[]" type="text" class="form-control" placeholder="Service Category"></div>
								<div class="col-sm-4"><label class="show-on-mob">Heading:</label>
									<select name="service_head[]" class="form-control chosen-select-deselect"><option></option>
										<option value="custom">Add Custom</option>
										<?php $service_headings = mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` ORDER BY `category`, `heading`");
										$service_custom = true;
										while($row = mysqli_fetch_array($service_headings)) {
											if($service_head[$j] == $row['heading']) {
												$service_custom = false;
											}
											echo "<option ".($service_head[$j] == $row['heading'] ? 'selected' : '')." data-category='".$row['category']."' value='".$row['heading']."'>".$row['heading']."</option>";
										}
										if($service_custom) {
											echo "<option selected data-category='".$cat."' value='".$service_head[$j]."'>".$service_head[$j]."</option>";
										} ?></select></div>
								<div class="col-sm-4" style="display:none;"><label class="show-on-mob">Service Heading:</label>
									<input disabled name="service_head[]" type="text" class="form-control" placeholder="Service Heading"></div>
								<div class="col-sm-3"><label class="show-on-mob">Service Rate:</label>
									<select name="service_rates[]" class="chosen-select-deselect form-control" data-placeholder="Select a Rate">
										<option selected value="<?= $service_rates[$j] ?>"><?= $service_rates[$j] ?></option>
									</select></div>
								<div class="col-sm-3" style="display:none;"><label class="show-on-mob">Service Rate:</label>
									<input disabled name="service_rates[]" type="number" min="0" step="any" class="form-control" placeholder="Service Rate"></div>
								<div class="col-sm-1"><button class="btn brand-btn" onclick="$(this).closest('.form-group').remove();">Delete</button></div>
							</div>
							<?php if($service_rates[$j] == '') { ?>
								<script>
								$(document).ready(function() {
									service_rate($('select[name="service_rates[]"]').get(<?= $j ?>));
								});
								</script>
							<?php }
						} ?>
						<button class="btn brand-btn pull-right" id="service_btn" onclick="addService(); return false;">Add Service</button>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equip" >
							Equipment: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equip" class="panel-collapse collapse">
					<div class="panel-body">
						<?php $equip_fields = ','.mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`equipment`) FROM `field_config_equipment` WHERE `accordion` IS NOT NULL"))[0].','; ?>
						<div class="form-group hide-titles-mob">
							<?php if(strpos($equip_fields, ',Category,') !== FALSE) { ?>
								<div class="col-sm-2 text-center">Category</div>
							<?php } ?>
							<!--<?php if(strpos($equip_fields, ',Type,') !== FALSE) { ?>
								<div class="col-sm-2 text-center">Type</div>
							<?php } ?>-->
							<?php if(strpos($equip_fields, ',Unit #,') !== FALSE) { ?>
								<div class="col-sm-2 text-center">Unit #</div>
							<?php } ?>
							<?php if(strpos($equip_fields, ',Make,') !== FALSE) { ?>
								<div class="col-sm-1 text-center">Make</div>
							<?php } ?>
							<?php if(strpos($equip_fields, ',Model,') !== FALSE) { ?>
								<div class="col-sm-1 text-center">Model</div>
							<?php } ?>
							<?php if(strpos($equip_fields, ',Category,') === FALSE && strpos($equip_fields, ',Type,') === FALSE && strpos($equip_fields, ',Unit #,') === FALSE && strpos($equip_fields, ',Make,') === FALSE && strpos($equip_fields, ',Model,') === FALSE) { ?>
								<div class="col-sm-8 text-center">Equipment</div>
							<?php } ?>
							<div class="col-sm-1 text-center">Rate</div>
							<div class="col-sm-2 text-center">Status</div>
						</div>
						<div class="clearfix"></div>
						<?php $equipment_id = explode(',',$equipment_id);
						$equipment_rate = explode(',',$equipment_rate);
						$equipment_status = explode(',',$equipment_status);
						foreach($equipment_id as $j => $id) {
							$equip_status = mysqli_fetch_array(mysqli_query($dbc, "SELECT `status` FROM `equipment` WHERE `equipmentid`='$id' AND `status` != '' UNION SELECT '".$equipment_status[$j]."'"))['status']; ?>
							<div class="form-group equip">
								<?php if(strpos($equip_fields, ',Category,') !== FALSE) { ?>
									<div class="col-sm-2"><label class="show-on-mob">Category:</label>
										<select name="equip_cat_value[]" class="form-control chosen-select-deselect">
											<option></option>
											<?php $equip_list = mysqli_query($dbc, "SELECT `category`, `type`, `unit_number`, `make`, `model`, `label`, `equipmentid` FROM `equipment` ORDER BY `category`, `type`, `unit_number`, `make`, `model`, `equipmentid`");
											$category = '';
											while($row = mysqli_fetch_array($equip_list)) {
												if($category != $row['category']) {
													echo "<option ".($row['equipmentid'] == $id ? 'selected' : '')." value='".$row['category']."'>".$row['category']."</option>";
												}
												$category = $row['category'];
											} ?></select></div>
								<?php } ?>
								<!--<?php if(strpos($equip_fields, ',Type,') !== FALSE) { ?>
									<div class="col-sm-2"><label class="show-on-mob">Type:</label>
										<select name="equip_type_value[]" class="form-control chosen-select-deselect" onchange="equip_filter(this);">
											<option></option>
											<?php mysqli_data_seek($equip_list, 0);
											$type = '';
											while($row = mysqli_fetch_array($equip_list)) {
												if($type != $row['type']) {
													echo "<option ".($row['equipmentid'] == $id ? 'selected' : '')." value='".$row['type']."'>".$row['type']."</option>";
												}
												$type = $row['type'];
											} ?></select></div>
								<?php } ?>-->
								<?php if(strpos($equip_fields, ',Unit #,') !== FALSE) { ?>
									<div class="col-sm-2"><label class="show-on-mob">Unit #:</label>
										<select name="equipment_id[]" class="form-control chosen-select-deselect">
											<option></option>
											<?php mysqli_data_seek($equip_list, 0);
											while($row = mysqli_fetch_array($equip_list)) {
												echo "<option data-category='".$row['category']."' data-type='".$row['type']."' data-make='".$row['make']."' data-model='".$row['model']."' ".($row['equipmentid'] == $id ? 'selected' : '')." value='".$row['equipmentid']."'>".$row['unit_number'].(!empty($row['label']) ? ': '.$row['label'] : '')."</option>";
											} ?></select></div>
								<?php } ?>
								<?php if(strpos($equip_fields, ',Make,') !== FALSE) { ?>
									<div class="col-sm-1"><label class="show-on-mob">Make:</label>
										<select name="equip_make_value[]" class="form-control chosen-select-deselect">
											<option></option>
											<?php mysqli_data_seek($equip_list, 0);
											$make = '';
											while($row = mysqli_fetch_array($equip_list)) {
												if($make != $row['make']) {
													echo "<option ".($row['equipmentid'] == $id ? 'selected' : '')." value='".$row['make']."'>".$row['make']."</option>";
												}
												$make = $row['make'];
											} ?></select></div>
								<?php } ?>
								<?php if(strpos($equip_fields, ',Model,') !== FALSE) { ?>
									<div class="col-sm-1"><label class="show-on-mob">Model:</label>
										<select name="equip_model_value[]" class="form-control chosen-select-deselect">
											<option></option>
											<?php mysqli_data_seek($equip_list, 0);
											$model = '';
											while($row = mysqli_fetch_array($equip_list)) {
												if($model != $row['model']) {
													echo "<option ".($row['equipmentid'] == $id ? 'selected' : '')." value='".$row['model']."'>".$row['model']."</option>";
												}
												$model = $row['model'];
											} ?></select></div>
								<?php } ?>
								<?php if(strpos($equip_fields, ',Category,') === FALSE && strpos($equip_fields, ',Type,') === FALSE && strpos($equip_fields, ',Unit #,') === FALSE && strpos($equip_fields, ',Make,') === FALSE && strpos($equip_fields, ',Model,') === FALSE) { ?>
									<div class="col-sm-8"><label class="show-on-mob">Equipment:</label>
										<select name="equipment_id[]" class="form-control chosen-select-deselect">
											<option></option>
											<?php mysqli_data_seek($equip_list, 0);
											while($row = mysqli_fetch_array($equip_list)) {
												echo "<option value='".$row['equipmentid']."'>".$row['category'].' '.$row['type'].' '.$row['make'].' '.$row['model'].' '.$row['unit_number'].(!empty($row['label']) ? ': '.$row['label'] : '')."</option>";
											} ?></select></div>
								<?php } ?>
								<div class="col-sm-1"><label class="show-on-mob">Rate:</label>
									<input type="number" name="equipment_rate[]" class="form-control" value="<?= $equipment_rate[$j] ?>" min="0" step="any"></div>
								<div class="col-sm-2"><label class="show-on-mob">Status:</label>
									<select name="equipment_status[]" class="form-control chosen-select-deselect">
										<option></option>
										<option value='Active' <?= ($equip_status=='Active' ? 'selected="selected"' : '') ?> >Active</option>
										<option value='In Service' <?= ($equip_status=='In Service' || $equip_status == 'In Repair' ? 'selected="selected"' : '') ?> >In Service</option>
										<option value='Service Required' <?= ($equip_status=='Service Required' ? 'selected="selected"' : '') ?> >Service Required</option>
										<option value='On Site' <?= ($equip_status=='On Site' ? 'selected="selected"' : '') ?> >On Site</option>
										<option value='Inactive' <?= ($equip_status=='Inactive' ? 'selected="selected"' : '' )?> >Inactive</option>
										<option value='Sold' <?= ($equip_status=='Sold' ? 'selected="selected"' : '')?> >Sold</option>
										</select></div>
								<div class="col-sm-1"><button class="btn brand-btn" onclick="$(this).closest('.form-group').remove();">Delete</button></div>
							</div>
						<?php } ?>
						<button class="btn brand-btn pull-right" id="equip_btn" onclick="addEquip(); return false;">Add Equipment</button>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_material" >
							Materials: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_material" class="panel-collapse collapse">
					<div class="panel-body">
						<script>
						function is_custom_material(src) {
							if(src.value == 'CUSTOM') {
								var row = $(src).closest('.material');
								row.find('.col-sm-4').hide().find('select').attr('disabled','disabled');
								row.find('.col-sm-8').show().find('input').removeAttr('disabled');
							}
						}
						</script>
						<div class="form-group hide-titles-mob">
							<div class="col-sm-4 text-center">Category</div>
							<div class="col-sm-4 text-center">Type</div>
							<div class="col-sm-3 text-center">Quantity</div>
						</div>
						<?php $material_id = explode('#*#',$material_id);
						$material_qty = explode(',',$material_qty);
						foreach($material_id as $j => $material) {
							$cust_mat = true;
							if($material == '' || is_numeric($material)) {
								$cust_mat = false;
							} ?>
							<div class="form-group material">
								<div class="col-sm-8" <?= ($cust_mat ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Custom Material:</label>
									<input type="text" name="material_id[]" class="form-control" <?= ($cust_mat ? '' : 'disabled') ?> value="<?= $material ?>" placeholder="Enter Material"></div>
								<div class="col-sm-4" <?= ($cust_mat ? 'style="display:none;"' : '') ?>><label class="show-on-mob">Category:</label>
									<select name="material_category[]" class="form-control chosen-select-deselect" <?= ($cust_mat ? 'disabled' : '') ?>><option></option><option value="CUSTOM">Custom Material</option>
										<?php $material_list = mysqli_query($dbc, "SELECT `materialid`, `category`, `name` FROM `material` ORDER BY `name`");
										$category = '';
										while($row = mysqli_fetch_array($material_list)) {
											if($category != $row['category'] || $row['materialid'] == $material) {
												echo "<option ".($row['materialid'] == $material ? 'selected' : '')." value='".$row['materialid']."'>".$row['category']."</option>";
											}
											$category = $row['category'];
										} ?></select></div>
								<div class="col-sm-4" <?= ($cust_mat ? 'style="display:none;"' : '') ?>><label class="show-on-mob">Type:</label>
									<select name="material_id[]" class="form-control chosen-select-deselect" <?= ($cust_mat ? 'disabled' : '') ?>><option></option>
										<?php mysqli_data_seek($material_list, 0);
										while($row = mysqli_fetch_array($material_list)) {
											echo "<option ".($row['materialid'] == $material ? 'selected' : '')." value='".$row['materialid']."'>".$row['name']."</option>";
										} ?></select></div>
								<div class="col-sm-3"><label class="show-on-mob">Quantity:</label>
									<input type="number" class="form-control" name="material_qty[]" value="<?php echo $material_qty[$j]; ?>" min="0"></div>
								<div class="col-sm-1"><button class="btn brand-btn" onclick="$(this).closest('.form-group').remove();">Delete</button></div>
							</div>
						<?php } ?>
						<button class="btn brand-btn pull-right" id="material_btn" onclick="addMaterial(); return false;">Add Materials</button>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_where" >
							Where: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_where" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group"><label class="col-sm-4 control-label">SL:</label>
							<div class="col-sm-8"><input name="site_location" class="form-control" value="<?= $site_location ?>"></div></div>
						<div class="form-group"><label class="col-sm-4 control-label">LSD:</label>
							<div class="col-sm-8"><input name="site_description" class="form-control" value="<?= $site_description ?>"></div></div>
						<div class="form-group"><label class="col-sm-4 control-label">Google Maps:</label>
							<div class="col-sm-8"><input name="google_map_link" class="form-control" value="<?= $google_map_link ?>"></div></div>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_when" >
							When: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_when" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group"><label class="col-sm-4 control-label">Start Date:</label>
							<div class="col-sm-8"><input name="work_start_date" class="form-control datepicker" value="<?= $work_start_date ?>"></div></div>
						<div class="form-group"><label class="col-sm-4 control-label">End Date:</label>
							<div class="col-sm-8"><input name="work_end_date" class="form-control datepicker" value="<?= $work_end_date ?>"></div></div>
						<?php $work_start_arr = explode(',',$work_start_time);
						$work_details_arr = explode(',',$work_start_details);
						foreach($work_start_arr as $j => $start_work) { ?>
							<div class="form-group"><label class="col-sm-4 control-label">Start Time:</label>
								<?php if($j == count($work_start_arr) - 1) { ?>
									<div class="col-sm-7"><div class="col-sm-4"><input name="work_start_time[]" class="form-control datetimepicker" value="<?= $start_work ?>"></div>
										<div class="col-sm-2">Details</div><div class="col-sm-6"><input name="work_start_details[]" class="form-control" value="<?= $work_details_arr[$j] ?>"></div></div>
									<button class="btn brand-btn" onclick="addTime(this); return false;">Add Time</button>
								<?php } else { ?>
									<div class="col-sm-8"><div class="col-sm-4"><input name="work_start_time[]" class="form-control datetimepicker" value="<?= $start_work ?>"></div>
										<div class="col-sm-2">Details</div><div class="col-sm-6"><input name="work_start_details[]" class="form-control" value="<?= $work_details_arr[$j] ?>"></div></div>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_docs" >
							Support Documents: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_docs" class="panel-collapse collapse">
					<div class="panel-body">
					<?php
					if(!empty($_GET['workorderid'])) {
						$query_check_credentials = "SELECT * FROM site_work_document WHERE workorderid='$workorderid' ORDER BY documentid DESC";
						$result = mysqli_query($dbc, $query_check_credentials);
						$num_rows = mysqli_num_rows($result);
						if($num_rows > 0) {
							echo "<table class='table table-bordered'>
							<tr class='hidden-xs hidden-sm'>
							<th>Type</th>
							<th>Document/Link</th>
							<th>Date</th>
							<th>Uploaded By</th>
							<th>Delete</th>
							</tr>";
							while($row = mysqli_fetch_array($result)) {
								echo '<tr>';
								$by = $row['created_by'];
								echo '<td data-title="Schedule">'.$row['type'].'</td>';
								if($row['document'] != '') {
									echo '<td data-title="Schedule"><a href="download/'.$row['document'].'" target="_blank">'.$row['document'].'</a></td>';
								} else {
									echo '<td data-title="Schedule"><a target="_blank" href=\''.$row['link'].'\'">Link</a></td>';
								}
								echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
								echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
								echo '<td data-title="Schedule"><a href=\'../delete_restore.php?action=delete&ticketdocid='.$row['ticketdocid'].'&ticketid='.$row['ticketid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
								echo '</tr>';
							}
							echo '</table>';
						}
					}
					?>
						<div class="form-group">
							<label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
									<span class="popover-examples list-inline">&nbsp;
									<a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
									</span>
							</label>
							<div class="col-sm-8">

								<div class="enter_cost additional_doc clearfix">
									<div class="clearfix"></div>

									<div class="form-group clearfix">
										<div class="col-sm-5">
											<input name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
										</div>
									</div>

								</div>

								<div id="add_here_new_doc"></div>

								<div class="form-group triple-gapped clearfix">
									<div class="col-sm-offset-4 col-sm-8">
										<button id="add_row_doc" class="btn brand-btn pull-left">Add Another Document</button>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="additional_note" class="col-sm-4 control-label">Link(s):<br><em>(e.g. - https://www.google.com)</em>
							</label>
							<div class="col-sm-8">

								<div class="enter_cost additional_link clearfix">
									<div class="clearfix"></div>

									<div class="form-group clearfix">
										<div class="col-sm-5">
											<input name="support_link[]" type="text" class="form-control">
										</div>
									</div>

								</div>

								<div id="add_here_new_link"></div>

								<div class="form-group triple-gapped clearfix">
									<div class="col-sm-offset-4 col-sm-8">
										<button id="add_row_link" class="btn brand-btn pull-left">Add Another Link</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_checklist" >
							Site Checklist: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_checklist" class="panel-collapse collapse">
					<div class="panel-body">
						<?php include('add_work_order_checklist.php'); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pos" >
							Purchase Orders: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_pos" class="panel-collapse collapse">
					<div class="panel-body">
						<h3>Attach Purchase Orders</h3>
						<?php include('add_work_order_po.php'); ?>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_comments" >
							Comments: <span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_comments" class="panel-collapse collapse">
					<div class="panel-body"><div class="col-md-12">
					   <?php if($workorderid != 'NEW') {
							$query_check_credentials = "SELECT * FROM site_work_comment WHERE workorderid='$workorderid' AND type='note' ORDER BY commentid DESC";
							$result = mysqli_query($dbc, $query_check_credentials);
							$num_rows = mysqli_num_rows($result);
							if($num_rows > 0) {
								echo "<table class='table table-bordered'>
								<tr class='hidden-xs hidden-sm'>
								<th>Note</th>
								<th>Assigned To</th>
								<th>Date</th>
								<th>Added By</th>
								</tr>";
								while($row = mysqli_fetch_array($result)) {
									echo '<tr>';
									$by = $row['created_by'];
									$to = $row['email_comment'];
									echo '<td data-title="Note">'.html_entity_decode($row['comment']).'</td>';
									echo '<td data-title="Assigned To">'.get_staff($dbc, $to).'</td>';
									echo '<td data-title="Date">'.$row['created_date'].'</td>';
									echo '<td data-title="Added By">'.get_staff($dbc, $by).'</td>';
									echo '</tr>';
								}
								echo '</table>';
							}
						} ?>
					  <div class="form-group">
						<label for="site_name" class="col-sm-4 control-label">Note:</label>
						<div class="col-sm-8">
						  <textarea name="comments" rows="4" cols="50" class="form-control" ></textarea>
						</div>
					  </div>

						<div class="form-group">
						  <label for="site_name" class="col-sm-4 control-label">Send Email:</label>
						  <div class="col-sm-8">
							<input type="checkbox" value="Yes" name="send_email_on_comment" onclick="comment_check_send_email(this);">
						  </div>
						</div>

						<div class="form-group">
						  <label for="site_name" class="col-sm-4 control-label">Assign/Email To:</label>
						  <div class="col-sm-8">
							<select data-placeholder="Choose a Staff Member..." name="email_comment" class="chosen-select-deselect form-control" width="380">
							  <option value=""></option>
							  <?php
								$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category='Staff' order by first_name");
								while($row = mysqli_fetch_array($query)) {
									echo "<option value='". $row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
								}
							  ?>
							</select>
						  </div>
						</div><?php
						$sender = get_contact($dbc, $_SESSION['contactid'], 'email_address');
						$subject = 'Note added on Site Work Order for you to Review';
						$body = 'The following note has been added on a site work order for you:<br>[NOTE]<br><br>
								Please click the Site Work Order link below to view all information.<br>
								<a target="_blank" href="'.WEBSITE_URL.'/Site Work Orders/add_work_order.php?workorderid=[WORKORDERID]">Work Order #[WORKORDERID]</a><br>';
						?>
						<script>
						function comment_check_send_email(checked) {
							if(checked.checked) {
								$('#comment_email_send_div').show();
							} else {
								$('#comment_email_send_div').hide();
							}
						}
						</script>
						<div id="comment_email_send_div" style="display:none;">
							<div class="form-group">
								<label class="col-sm-4 control-label">Sending Email Address:</label>
								<div class="col-sm-8">
									<input type="text" name="comment_email_sender" class="form-control" value="<?php echo $sender; ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email Subject:</label>
								<div class="col-sm-8">
									<input type="text" name="comment_email_subject" class="form-control" value="<?php echo $subject; ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email Body:</label>
								<div class="col-sm-8">
									<textarea name="comment_email_body" class="form-control"><?php echo $body; ?></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_safety" >
						Safety Checklist: <span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_safety" class="panel-collapse collapse">
				<div class="panel-body">
					<?php include('add_work_order_safety_checklist.php'); ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_addendum" >
						Addendum: <span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_addendum" class="panel-collapse collapse">
				<div class="panel-body">
				   <?php if($workorderid != 'NEW') {
						$query_check_credentials = "SELECT * FROM site_work_comment WHERE workorderid='$workorderid' AND type='addendum' ORDER BY commentid DESC";
						$result = mysqli_query($dbc, $query_check_credentials);
						$num_rows = mysqli_num_rows($result);
						if($num_rows > 0) {
							echo "<table class='table table-bordered'>
							<tr class='hidden-xs hidden-sm'>
							<th>Addendum</th>
							<th>Assigned To</th>
							<th>Date</th>
							<th>Added By</th>
							</tr>";
							while($row = mysqli_fetch_array($result)) {
								echo '<tr>';
								$by = $row['created_by'];
								$to = $row['email_comment'];
								echo '<td data-title="Addendum">'.html_entity_decode($row['comment']).'</td>';
								echo '<td data-title="Assigned To">'.get_staff($dbc, $to).'</td>';
								echo '<td data-title="Date">'.$row['created_date'].'</td>';
								echo '<td data-title="Added By">'.get_staff($dbc, $by).'</td>';
								echo '</tr>';
							}
							echo '</table>';
						}
					} ?>
				  <div class="form-group">
					<label for="site_name" class="col-sm-4 control-label">Addendum:</label>
					<div class="col-sm-8">
					  <textarea name="addendum_comments" rows="4" cols="50" class="form-control" ></textarea>
					</div>
				  </div>

					<div class="form-group">
					  <label for="site_name" class="col-sm-4 control-label">Send Email:</label>
					  <div class="col-sm-8">
						<input type="checkbox" value="Yes" name="addendum_send_email_on_comment" onclick="comment_check_send_email(this);">
					  </div>
					</div>

					<div class="form-group">
					  <label for="site_name" class="col-sm-4 control-label">Assign/Email To:</label>
					  <div class="col-sm-8">
						<select data-placeholder="Choose a Staff Member..." name="addendum_email_comment" class="chosen-select-deselect form-control" width="380">
						  <option value=""></option>
						  <?php
							$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Staff' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
							foreach($query as $id) {
								$selected = '';
								//$selected = $vendorid == $id ? 'selected = "selected"' : '';
								echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
							}
						  ?>
						</select>
					  </div>
					</div><?php
					$sender = get_contact($dbc, $_SESSION['contactid'], 'email_address');
					$subject = 'Addendum added on Site Work Order for you to Review';
					$body = 'The following addendum has been added on a site work order for you:<br>[ADDENDUM]<br><br>
							Please click the Site Work Order link below to view all information.<br>
							<a target="_blank" href="'.WEBSITE_URL.'/Site Work Orders/add_work_order.php?workorderid=[WORKORDERID]">Work Order #[WORKORDERID]</a><br>';
					?>
					<script>
					function comment_check_send_email(checked) {
						if(checked.checked) {
							$('#comment_email_send_div').show();
						} else {
							$('#comment_email_send_div').hide();
						}
					}
					</script>
					<div id="comment_email_send_div" style="display:none;">
						<div class="form-group">
							<label class="col-sm-4 control-label">Sending Email Address:</label>
							<div class="col-sm-8">
								<input type="text" name="addendum_comment_email_sender" class="form-control" value="<?php echo $sender; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Subject:</label>
							<div class="col-sm-8">
								<input type="text" name="addendum_comment_email_subject" class="form-control" value="<?php echo $subject; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Body:</label>
							<div class="col-sm-8">
								<textarea name="addendum_comment_email_body" class="form-control"><?php echo $body; ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>

		<div class="form-group double-gap-top">
			<p><span class="brand-color"><em>Required Fields *</em></span></p>
		</div>

		<div class="form-group">
			<div class="col-sm-6">
				<a href="site_work_orders.php?tab=<?= ($status == 'pending' ? 'pending' : 'active') ?>" class="btn brand-btn btn-lg">Back</a>
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="submit"	value="<?= (empty($workorderid) ? 'NEW' : $workorderid) ?>" class="btn brand-btn btn-lg pull-right">Submit</button>
			</div>
			<div class="clearfix"></div>
		</div>

		</form>

	</div>
</div>

<?php include ('../footer.php'); ?>