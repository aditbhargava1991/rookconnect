<style>
.today-btn {
  color: #fafafa;
  background: green;
  border: 2px solid #fafafa; }
.ui-disabled  { pointer-events: none !important; }
</style>
<script type="text/javascript" src="calendar.js"></script>
<script>
$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
});
function handleClick(sel) {

    var stagee = sel.value;
	var contactide = $('.contacterid').val();
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "calendar_ajax_all.php?fill=list_view&contactid="+contactide+"&value="+stagee+'&offline='+offline_mode,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});

}
function ticket_choose_user(target, id, date) {
	var title	= 'Select a User';
	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		var height = $(this).contents().find('option').length * $(this).contents().find('select').height();
		$(this).contents().find('select').data({id: id});
		this.style.height = (height + this.contentWindow.document.body.offsetHeight + 180) + 'px';
		$(this).contents().find('.btn').off();
		$(this).contents().find('.btn').click(function() {
			if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to send the '+target+' to the selected user?')) {
				if(target == 'alert') {
					$.ajax({
						method: 'POST',
						url: 'calendar_ajax_all.php?fill=ticketsendalert'+'&offline='+offline_mode,
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'email') {
					$.ajax({
						method: 'POST',
						url: 'calendar_ajax_all.php?fill=ticketsendemail'+'&offline='+offline_mode,
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'reminder') {
					$.ajax({
						method: 'POST',
						url: 'calendar_ajax_all.php?fill=ticketsendreminder'+'&offline='+offline_mode,
						data: { id: id, schedule: date, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				$(this).closest('body').find('select').val('');
				$('.close_iframer').click();
			}
			else if($(this).closest('body').find('select').val() == '') {
				$('.close_iframer').click();
			}
		});
	});
	$('#iframe_instead_of_window').attr('src', '<?php echo WEBSITE_URL; ?>/Staff/select_staff.php?target='+target);
	$('.iframe_title').text(title);
	$('.iframe_holder').show();
	$('.hide_on_iframe').hide();
}
function ticket_send_alert(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	ticket_choose_user('alert', ticket_id);
}
function ticket_send_email(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	ticket_choose_user('email', ticket_id);
}
function ticket_send_reminder(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=ticket_reminder_'+ticket_id+']').show().focus();
	$('[name=ticket_reminder_'+ticket_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=ticket_reminder_'+ticket_id+']').blur(function() {
		$(this).hide();
	});
	$('[name=ticket_reminder_'+ticket_id+']').change(function() {
		var date = $(this).val().trim();
		$(this).val('');
		if(date != '') {
			ticket_choose_user('reminder', ticket_id, date);
		}
	});
}
function ticket_add_note(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=ticket_reply_'+ticket_id+']').show().focus();
	$('[name=ticket_reply_'+ticket_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=ticket_reply_'+ticket_id+']').blur(function() {
		$(this).hide();
		var note = $(this).val().trim();
		$(this).val('');
		if(note != '') {
			$.ajax({
				method: 'POST',
				url: 'calendar_ajax_all.php?fill=ticketsendnote'+'&offline='+offline_mode,
				data: { id: ticket_id, note: note },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
}
function ticket_add_time(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=ticket_time_'+ticket_id+']').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: 'calendar_ajax_all.php?fill=ticketquicktime'+'&offline='+offline_mode,
				data: { id: ticket_id, time: time+':00' },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
	$('[name=ticket_time_'+ticket_id+']').timepicker('show');
}
function ticket_attach_file(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	var file_id = 'ticket_attach_'+ticket_id;
	$('[name='+file_id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$(ticket).parents('li').find('[name='+file_id+']')[0].files[0]);
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "calendar_ajax_all.php?fill=ticketsendupload&id="+ticket_id+'&offline='+offline_mode,
			data: fileData,
			complete: function(result) { console.log(result.responseText); }
		});
	});
	$(ticket).parents('li').find('[name='+file_id+']').click();
}
function ticket_flag_item(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$.ajax({
		method: "POST",
		url: "calendar_ajax_all.php?fill=ticketflag"+'&offline='+offline_mode,
		data: { id: ticket_id },
		complete: function(result) {
			console.log(result.responseText);
			$(ticket).closest('.pull-right').siblings('a').css('color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}
function ticket_archive(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	if(confirm("Are you sure you want to archive this ticket?")) {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "calendar_ajax_all.php?fill=ticketquickarchive&id="+ticket_id+'&offline='+offline_mode,
			dataType: "html",   //expect html to be returned
			success: function(response){
				console.log(response.responseText);
				$(ticket).parents('li').hide();
			}
		});
	}
}
</script>

<div class="iframe_holder" style="display:none;">
	<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
	<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
	<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
</div>
<div class="hide_on_iframe ticket-calendar" style="padding: 1em;">

	<form name="form_sites" method="post" action="" class="form-inline" role="form">
	<a href='?type=ticket&view=daily'><button type="button" class="btn brand-btn mobile-block" >Day</button></a>
	<a href='?type=ticket&view=weekly'><button type="button" class="btn brand-btn mobile-block active_tab" >Week</button></a>
	<a href='?type=ticket&view=monthly'><button type="button" class="btn brand-btn mobile-block" >Month</button></a>
	<a href='?type=ticket&view=custom'><button type="button" class="btn brand-btn mobile-block" >Custom</button></a>
	<a href='?type=ticket&view=30day'><button type="button" class="btn brand-btn mobile-block" >30 Days</button></a>
	<!-- <a href="field_config_calendar.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a> -->

	 <?php
		$contactid = $_SESSION['contactid'];
		$search_month = date('F');
		$search_year = date('Y');
		$search_user = $contactid;
		$search_client = '';
		$search_ticket = '';
		$srarch_status = '';
		if(isset($_POST['search_user_submit'])) {
			$search_user = $_POST['search_user'];
			$search_client = $_POST['search_client'];
			$search_ticket = $_POST['search_ticket'];
			$search_status = $_POST['search_status'];
		}
		if (isset($_POST['display_all_inventory'])) {
			$search_month = date('F');
			$search_year = date('Y');
			$search_user = '';
			$search_client = '';
			$search_ticket = '';
			$search_status = '';
		}
	?>
	<br><br>
	<span style='padding:5px; font-weight:bold;'>Normal View: </span><input onclick="handleClick(this);" type='radio' style='width:20px; height:20px;' <?php if($list_view !== 1) { echo 'checked'; } ?> name='horizo_vert' class='horizo_vert' value=''>
	<span style='padding:5px; font-weight:bold;'>List View: </span><input onclick="handleClick(this);" <?php if($list_view == 1) { echo 'checked'; } ?> type='radio' style='width:20px; height:20px;' name='horizo_vert' class='horizo_vert' value='1'><br><br>

	<div class="form-group">
		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
			<label for="site_name" class="control-label">Search By Staff:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<select data-placeholder="Select Staff" name="search_user" class="chosen-select-deselect form-control" width="380">
				<option value=""></option>
				<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status = 1 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY),MYSQLI_ASSOC));
				foreach($query as $id) { ?>
					<option <?php if ($id == $search_user) { echo " selected"; } ?> value='<?php echo  $id; ?>' ><?php echo get_contact($dbc, $id); ?></option>
				<?php } ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
			<label for="site_name" class="control-label">Search By <?= BUSINESS_CAT ?>:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<select data-placeholder="Select a Business" name="search_client" class="chosen-select-deselect form-control" width="380">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT DISTINCT(c.name), t.businessid FROM contacts c, tickets t WHERE t.businessid=c.contactid AND c.category='Business' order by name");
				while($row = mysqli_fetch_array($query)) {
					?><option <?php if ($row['businessid'] == $search_client) { echo " selected"; } ?> value='<?php echo  $row['businessid']; ?>' ><?php echo decryptIt($row['name']); ?></option>
			<?php	} ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
			<label for="site_name" class="control-label">Search By <?= TICKET_NOUN ?>#:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<input type="text" name="search_ticket" value="<?php echo $search_ticket; ?>" class="form-control">
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
			<label for="site_name" class="control-label">Search By Status:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<select data-placeholder="Select a Status" name="search_status" class="chosen-select-deselect form-control input-sm">
				<option value=""></option>
				<?php
				$tabs = get_config($dbc, 'ticket_status');
				$each_tab = explode(',', $tabs);
				foreach ($each_tab as $cat_tab) {
					if ($search_status == $cat_tab) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
				}
				?>
			</select>
		</div>
	</div>

	<br><br>
	<div class="form-group">
		<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
		<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
	</div>

<?php
echo '<br><br><div class="pull-right1" >';
echo '<img src="'.WEBSITE_URL.'/img/block/green.png" width="10" height="10" border="0" alt="">&nbsp;&nbsp;Today + Following Day&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<img src="'.WEBSITE_URL.'/img/block/orange.png" width="10" height="10" border="0" alt="">&nbsp;&nbsp;Last 2 Days&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<img src="'.WEBSITE_URL.'/img/block/red.png" width="10" height="10" border="0" alt="">&nbsp;&nbsp;Older Than 2 Previous Days<br>';

$ticket_status = get_config($dbc, 'ticket_status');
$each_tab = explode(',', $ticket_status);
$i=1;
$status_array = array();
foreach ($each_tab as $cat_tab) {
	if($cat_tab != 'Done' && $cat_tab != 'Archive') {
		echo '<img src="'.WEBSITE_URL.'/img/block/s'.$i.'.png" width="10" height="10" border="0" alt="">&nbsp;'.$cat_tab.'&nbsp;&nbsp;';
		$status_array[$cat_tab] = 's'.$i.'.png';
		$i++;
	}
}
echo '</div>';
?>

<?php
echo '<h2>'.$search_month.' '.$search_year.'</h2>';
echo draw_calendar($dbc, date("n", strtotime($search_month)),$search_year,$search_user,$search_client,$search_ticket,$search_status,$status_array);
?>

</div>
<?php
function draw_calendar($dbc, $month,$year,$search_user,$search_client,$search_ticket,$search_status,$status_array){

	/* draw table */

    //class="calendar"

	$calendar = '<table cellpadding="0" cellspacing="0" class="table table-bordered">';

	/* table headings */
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$calendar.= '<tr class="calendar-row"><td style="width:14.29%" class="calendar-day-head">'.implode('</td><td style="width:14.29%" class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

    $sunday = date( 'Y-m-d', strtotime( 'sunday last week' ) );
    $monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
    $tuesday = date( 'Y-m-d', strtotime( 'tuesday this week' ) );
    $wednesday = date( 'Y-m-d', strtotime( 'wednesday this week' ) );
    $thursday = date( 'Y-m-d', strtotime( 'thursday this week' ) );
    $friday = date( 'Y-m-d', strtotime( 'friday this week' ) );
    $saturday = date( 'Y-m-d', strtotime( 'saturday this week' ) );

	$today_in_month = date('d');
	/* print "blank" days until the first of the current week */
	if($today_in_month < 8) {
		for($x = 0; $x < $running_day; $x++):
			$calendar.= '<td style="width:14.29%" class="calendar-day connectedSortable">';
			$days_in_this_week++;
		endfor;
	}

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):

        $today_date = $list_day.'-'.$month.'-'.$year;
        $new_today_date = date_format(date_create_from_format('j-n-Y', $today_date), 'Y-m-d');

            if($new_today_date == $sunday || $new_today_date == $monday || $new_today_date == $tuesday || $new_today_date == $wednesday || $new_today_date == $thursday || $new_today_date == $friday || $new_today_date == $saturday) {

            $calendar.= '<td style="width:14.29%" class="calendar-day connectedSortable '.$new_today_date.'">';
            /* add in the day number */
            $class = '';
            if($new_today_date == date('Y-m-d')) {
                $class = 'today-btn';
            }
            $calendar.= '<div class="btn brand-btn ui-disabled pull-right '.$class.'">'.$list_day.'</div>';

            if($search_user != '') {
                $result = mysqli_query($dbc,"SELECT * FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND contactid ='$search_user' AND deleted=0");
            } else {
                $result = mysqli_query($dbc,"SELECT * FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0");
            }

            $old_staff = '';
            $i=0;
            while($row = mysqli_fetch_array( $result )) {
                $contactid = $row['contactid'];
                $staff = get_staff($dbc, $contactid);

                if($search_client != '') {
                    $tickets = mysqli_query($dbc,"SELECT * FROM tickets WHERE (internal_qa_date='$new_today_date' OR deliverable_date='$new_today_date' OR '$new_today_date' BETWEEN to_do_date AND to_do_end_date) AND (contactid LIKE '%," . $contactid . ",%' OR internal_qa_contactid LIKE '%," . $contactid . ",%' OR deliverable_contactid LIKE '%," . $contactid . ",%') AND businessid='$search_client' AND status NOT IN('Archive', 'Done')");
                } elseif($search_ticket != '') {
                    $tickets = mysqli_query($dbc,"SELECT * FROM tickets WHERE (internal_qa_date='$new_today_date' OR deliverable_date='$new_today_date' OR '$new_today_date' BETWEEN to_do_date AND to_do_end_date) AND (contactid LIKE '%," . $contactid . ",%' OR internal_qa_contactid LIKE '%," . $contactid . ",%' OR deliverable_contactid LIKE '%," . $contactid . ",%') AND ticketid='$search_ticket' AND status NOT IN('Archive', 'Done')");
                } elseif($search_status != '') {
                    $tickets = mysqli_query($dbc,"SELECT * FROM tickets WHERE (internal_qa_date='$new_today_date' OR deliverable_date='$new_today_date' OR '$new_today_date' BETWEEN to_do_date AND to_do_end_date) AND (contactid LIKE '%," . $contactid . ",%' OR internal_qa_contactid LIKE '%," . $contactid . ",%' OR deliverable_contactid LIKE '%," . $contactid . ",%') AND status='$search_status' AND status NOT IN('Archive', 'Done')");
                } else {
                    $tickets = mysqli_query($dbc,"SELECT * FROM tickets WHERE (internal_qa_date='$new_today_date' OR deliverable_date='$new_today_date' OR '$new_today_date' BETWEEN to_do_date AND to_do_end_date) AND (contactid LIKE '%," . $contactid . ",%' OR internal_qa_contactid LIKE '%," . $contactid . ",%' OR deliverable_contactid LIKE '%," . $contactid . ",%') AND status NOT IN('Archive', 'Done')");
                }

                /*
                if($search_client != '') {
                    $tickets = mysqli_query($dbc,"SELECT * FROM tickets WHERE (('$new_today_date' BETWEEN to_do_date AND to_do_end_date) OR (internal_qa_date = '$new_today_date') OR (deliverable_date = '$new_today_date')) AND contactid LIKE '%," . $contactid . ",%' AND businessid='$search_client' AND status NOT IN('Archive', 'Done')");
                } elseif($search_ticket != '') {
                    $tickets = mysqli_query($dbc,"SELECT * FROM tickets WHERE (('$new_today_date' BETWEEN to_do_date AND to_do_end_date) OR (internal_qa_date = '$new_today_date') OR (deliverable_date = '$new_today_date')) AND contactid LIKE '%," . $contactid . ",%' AND ticketid='$search_ticket' AND status NOT IN('Archive', 'Done')");
                } else {
                    $tickets = mysqli_query($dbc,"SELECT * FROM tickets WHERE (('$new_today_date' BETWEEN to_do_date AND to_do_end_date) OR (internal_qa_date = '$new_today_date') OR (deliverable_date = '$new_today_date')) AND contactid LIKE '%," . $contactid . ",%' AND status NOT IN('Archive', 'Done')");
                }
                */

                $num_rows = mysqli_num_rows($tickets);
                $j = 0;

                while($row_tickets = mysqli_fetch_array( $tickets )) {
                    if((($row_tickets['status'] == 'Internal QA') && ($new_today_date == $row_tickets['internal_qa_date']) && (strpos($row_tickets['internal_qa_contactid'], ','.$contactid.',') !== FALSE)) || (($row_tickets['status'] == 'Customer QA' || $row_tickets['status'] == 'Waiting On Customer') && ($new_today_date == $row_tickets['deliverable_date']) && (strpos($row_tickets['deliverable_contactid'], ','.$contactid.',') !== FALSE)) || (($row_tickets['status'] != 'Customer QA' && $row_tickets['status'] != 'Internal QA') && ($new_today_date >= $row_tickets['to_do_date'] && $new_today_date <= $row_tickets['to_do_end_date']) && (strpos($row_tickets['contactid'], ','.$contactid.',') !== FALSE))) {
                        if($j == 0) {
                            $calendar .= '<h4 class="ui-disabled">'.$staff.'</h4>';
                        }
                        $date_color = 'block/green.png';
                        if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
                            $date_color = 'block/red.png';
                        }
                        if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
                            $date_color = 'block/orange.png';
                        }

                        $status_color = '';
                        $status = $row_tickets['status'];
                        $status_color = 'block/'.$status_array[$status];
						$contactide = $_SESSION['contactid'];
						$get_table_orient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM user_settings WHERE contactid='$contactide'"));
						$list_view = $get_table_orient['calendar_list_view'];
						if($list_view == 1) {
                        //$calendar .= '<a class="" href="#" style="color:black; display:block; border-bottom:1px solid black; white-space: nowrap; width:200px; text-overflow: ellipsis; overflow:hidden; padding: 2px;  background-color: '.$row['calendar_color'].';" id="ticket_'.$row_tickets['ticketid'].'" onclick="wwindow.open(\''.WEBSITE_URL.'/Ticket/add_tickets.php?ticketid='.$row_tickets['ticketid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;" title="#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'"><img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt=""><img src="'.WEBSITE_URL.'/img/'.$status_color.'" width="10" height="10" border="0" alt="" style="margin-left:3px;">&nbsp;#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'</a>';
						$calendar .= '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row_tickets['ticketid'].'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'" style="color:black;  display:block; border-bottom:1px solid black; white-space: nowrap; width:200px; text-overflow: ellipsis; overflow:hidden; padding: 2px;  background-color: '.$row['calendar_color'].';" title="#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'"><img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt=""><img src="'.WEBSITE_URL.'/img/'.$status_color.'" width="10" height="10" border="0" alt="" style="margin-left:3px;">&nbsp;#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'</a>';
						} else {
						$calendar .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" style="width:1em;" border="0" alt="">&nbsp;<img src="'.WEBSITE_URL.'/img/'.$status_color.'" style="width:1em;" border="0" alt="">';
						$calendar .= '<span class="pull-right" style="display:inline-block; width:calc(100% - 2.5em);" data-ticket="'.$row_tickets['ticketid'].'">';
						$calendar .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Flag This!" onclick="ticket_flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1em;" onclick="return false;"></span>';
						$calendar .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Alert" onclick="ticket_send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1em;" onclick="return false;"></span>';
						$calendar .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Email" onclick="ticket_send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1em;" onclick="return false;"></span>';
						$calendar .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Schedule Reminder" onclick="ticket_send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1em;" onclick="return false;"></span>';
						$calendar .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Attach File" onclick="ticket_attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1em;" onclick="return false;"></span>';
						$calendar .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Add Note" onclick="ticket_add_note(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1em;" onclick="return false;"></span>';
						$calendar .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Add Time" onclick="ticket_add_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:1em;" onclick="return false;"></span>';
						$calendar .= '<span style="display:inline-block; text-align:center; width:12.5%;" title="Archive Item" onclick="ticket_archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1em;" onclick="return false;"></span>';
						$calendar .= '</span>';
						$calendar .= '<input type="text" name="ticket_reply_'.$row_tickets['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
						$calendar .= '<input type="text" name="ticket_time_'.$row_tickets['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />';
						$calendar .= '<input type="text" name="ticket_reminder_'.$row_tickets['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
						$calendar .= '<input type="file" name="ticket_attach_'.$row_tickets['ticketid'].'" style="display:none;" class="form-control" />';
						$calendar .= '<br /><a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row_tickets['ticketid'].'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'" style="display:block; padding: 5px;color:black; '.($row_tickets['flag_colour'] != '' ? 'color: #'.$row_tickets['flag_colour'].';' : '').'
                        border-radius: 10px; background-color: '.$row['calendar_color'].';" id="ticket_'.$row_tickets['ticketid'].'">'.TICKET_NOUN.' #'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'</a><br>';
						//$calendar .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<img src="'.WEBSITE_URL.'/img/'.$status_color.'" width="10" height="10" border="0" alt="">&nbsp;<a class="" href="#" style="display:block; padding: 5px;color:black;border-radius: 10px; background-color: '.$row['calendar_color'].';" id="ticket_'.$row_tickets['ticketid'].'" onclick="wwindow.open(\''.WEBSITE_URL.'/Ticket/add_tickets.php?ticketid='.$row_tickets['ticketid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;">#'.$row_tickets['ticketid'].' : '.get_contact($dbc, $row_tickets['businessid'], 'name').' : '.$row_tickets['heading'].' ('.substr($row_tickets['max_time'], 0, 5).')'.'</a><br>';
						}
                        $j++;
                    }
                }

                if ($search_client != '') {
                    $tasklist = mysqli_query($dbc,"SELECT * FROM tasklist WHERE DATE(task_tododate) = '$new_today_date'  AND contactid = '$contactid' AND status NOT IN('Archive', 'Done') AND businessid = '$search_client'");
                } else {
                    $tasklist = mysqli_query($dbc,"SELECT * FROM tasklist WHERE DATE(task_tododate) = '$new_today_date'  AND contactid = '$contactid' AND status NOT IN('Archive', 'Done')");
                }

                $num_rows1 = mysqli_num_rows($tasklist);
                if($num_rows1 > 0 && $num_rows == 0) {
                    $calendar .= $staff.'<br>';
                }

                while($row_tasklist = mysqli_fetch_array( $tasklist )) {
                    $date_color = 'block/green.png';
                    if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
                        $date_color = 'block/red.png';
                    }
                    if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
                        $date_color = 'block/orange.png';
                    }
                    $calendar .= '<a style="color:black;  display:block; border-bottom:1px solid black; white-space: nowrap; width:200px; text-overflow: ellipsis; overflow:hidden; padding: 2px;  background-color: '.$row['calendar_color'].';" href="'.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$row_tasklist['tasklistid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="task_'.$row_tasklist['tasklistid'].'" title="'.$row_tasklist['heading'].'"><img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;Task: '.$row_tasklist['heading']. '</span><br>';
                    //$calendar .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<a href="#"  id="task_'.$row_tasklist['tasklistid'].'" onclick="wwindow.open(\''.WEBSITE_URL.'/Tasks/add_task.php?tasklistid='.$row_tasklist['tasklistid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;">'.$row_tasklist['heading']. '</a><br>';
                }

                $site_wo = mysqli_query($dbc,"SELECT * FROM `site_work_orders` WHERE ((DATE(work_start_date) <= '$new_today_date' AND DATE(work_end_date) >= '$new_today_date') OR `active` LIKE '$new_today_date%') AND `status` NOT IN ('Pending', 'Archived')");
                if($num_rows1+$num_rows > 0 && mysqli_num_rows($site_wo) > 0) {
                    $calendar .= $staff.'<br>';
                }
                while($row_site = mysqli_fetch_array( $site_wo )) {
                    $date_color = 'block/green.png';
                    if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
                        $date_color = 'block/red.png';
                    } else if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
                        $date_color = 'block/orange.png';
                    }
					$calendar .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" width="10" height="10" border="0" alt="">&nbsp;<a href="'.WEBSITE_URL.'/Site Work Orders/view_work_order.php?workorderid='.$row_site['workorderid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="site_wo_'.$row_site['workorderid'].'">#'.$row_site['id_label'].'</a><br>';
                }

                $i++;
            }

            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
            $calendar.= str_repeat('<p> </p>',2);

            $calendar.= '</td>';
        }

		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';

	/* all done, return result */
	return $calendar;
}
?>
