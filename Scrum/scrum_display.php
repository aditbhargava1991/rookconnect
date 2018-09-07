<script type="text/javascript" src="<?php echo WEBSITE_URL; ?>/Scrum/scrum.js"></script>
<style type='text/css'>
.ui-state-disabled  { pointer-events: none !important; }
</style>
<?php
$contactide = $_SESSION['contactid'];
$get_table_orient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT horizontal_communication FROM contacts WHERE contactid='$contactide'"));
$check_table_orient = $get_table_orient['horizontal_communication'];

$link = '?';
if(!empty($_GET['tab'])) {
	$link = '?tab='.$_GET['tab'].'&';
}
$scrum_tab = (empty($_GET['tab']) ? 'notes' : $_GET['tab']);

$title = "Scrum Board";
switch($scrum_tab) {
	case 'search': $title = 'Search Scrum Notes<a href="?tab=notes&date=today" class="btn brand-btn pull-right">Add Notes</a>'; break;
	case 'notes': $title = 'Scrum Notes<a href="?tab=notes&date=today" class="btn brand-btn pull-right">Add Notes</a>'; break;
	case 'personal': $title = TICKET_TILE; break;
	case 'company': $title = "Company ".TICKET_TILE; break;
}
?>
<script>
$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
});
$(document).on('change', 'select[name="search_client"]', function() { submitForm(); });
$(document).on('change', 'select[name="search_customer"]', function() { submitForm(); });
$(document).on('change', 'select[name="search_user"]', function() { submitForm(); });

function choose_user(target, id, date) {
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
						url: 'scrum_ajax_all.php?fill=sendalert',
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'email') {
					$.ajax({
						method: 'POST',
						url: 'scrum_ajax_all.php?fill=sendemail',
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'reminder') {
					$.ajax({
						method: 'POST',
						url: 'scrum_ajax_all.php?fill=sendreminder',
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
function send_alert(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	choose_user('alert', ticket_id);
}
function send_email(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=tickets&id='+ticket_id, 'auto', false, true, $('#scrum_div').height());
}
function send_reminder(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=reminder_'+ticket_id+']').show().focus();
	$('[name=reminder_'+ticket_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reminder_'+ticket_id+']').change(function() {
		$(this).hide();
		var date = $(this).val().trim();
		$(this).val('');
		if(date != '') {
			choose_user('reminder', ticket_id, date);
		}
	});
}
function send_reply(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=reply_'+ticket_id+']').show().focus();
	$('[name=reply_'+ticket_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reply_'+ticket_id+']').blur(function() {
		$(this).hide();
		var note = $(this).val().trim();
		$(this).val('');
		if(note != '') {
			$.ajax({
				method: 'POST',
				url: 'scrum_ajax_all.php?fill=sendnote',
				data: { id: ticket_id, note: note },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
}
function add_time(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
    ticket_class = $(ticket).parents('span').data('class');
	$('[name=ticket_time_'+ticket_id+']').show();
	$('[name=ticket_time_'+ticket_id+']').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$('[name=ticket_time_'+ticket_id+']').hide();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: 'scrum_ajax_all.php?fill=quicktime',
				data: { id: ticket_id, time: time+':00', tile: ticket_class },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
	$('[name=ticket_time_'+ticket_id+']').timepicker('show');
}
function attach_file(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	var file_id = 'attach_'+ticket_id;
	$('[name='+file_id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$(ticket).parents('li').find('[name='+file_id+']')[0].files[0]);
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "scrum_ajax_all.php?fill=sendupload&id="+ticket_id,
			data: fileData,
			complete: function(result) { console.log(result.responseText); }
		});
	});
	$(ticket).parents('li').find('[name='+file_id+']').click();
}
function flag_item(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$.ajax({
		method: "POST",
		url: "scrum_ajax_all.php?fill=ticketflag",
		data: { id: ticket_id },
		complete: function(result) {
			console.log(result.responseText);
			$(ticket).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}
function archive(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	if(confirm("Are you sure you want to archive this ticket?")) {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "scrum_ajax_all.php?fill=quickarchive&id="+ticket_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				console.log(response.responseText);
				$(ticket).parents('li').hide();
			}
		});
	}
}
</script>
<div class="standard-body-title pad-top">
    <h1 class="single-pad-bottom"><?php echo $title; ?></h1>
</div>
<div class="standard-body-content">
    <?php $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='projects_scrum'"));

    $note = $notes['note'];
    if ( !empty($note) ) { ?>
        <div class="notice popover-examples">
            <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            <?= $note ?></div>
            <div class="clearfix"></div>
        </div><?php
    }

	if(!in_array($scrum_tab, ['notes','search'])) {
		$query_clause = '';
		$ticket_query = '';
		$task_query = '';
		$search_client = '';
		$search_customer = '';
		$search_user = '';
		if(!empty($_POST['search_client'])) {
			$search_client = $_POST['search_client'];
			$query_clause .= " AND `businessid`='".$search_client."'";
		}
		if(!empty($_POST['search_customer'])) {
			$search_customer = $_POST['search_client'];
			$query_clause .= " AND CONCAT(',',`clientid`,',') LIKE '%,".$search_customer.",%'";
		}
		if(!empty($_POST['search_user'])) {
			$search_user = $_POST['search_user'];
			$ticket_query .= " AND (CONCAT(',',`contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$search_user.",%')";
			$task_query .= " AND (CONCAT(',',`contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$search_user.",%')";
		} else if($scrum_tab == 'personal') {
			$search_user = $_SESSION['contactid'];
			$ticket_query .= " AND (CONCAT(',',`contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$search_user.",%')";
			$task_query .= " AND (CONCAT(',',`contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$search_user.",%')";
		} else if($scrum_tab == 'staff') {
			$search_user = filter_var($_GET['subtab'],FILTER_SANITIZE_STRING);
			$ticket_query .= " AND (CONCAT(',',`contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$search_user.",%')";
			$task_query .= " AND (CONCAT(',',`contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$search_user.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$search_user.",%')";
		}
		if($scrum_tab == 'project') {
			$search_project = filter_var($_GET['subtab'],FILTER_SANITIZE_STRING);
			$query_clause .= " AND `projectid`='$search_project'";
		}
		?>

		<form name="form_sites" method="post" action="" class="form-horizontal" role="form">

			<input type='hidden' value='<?php echo $contactide; ?>' class='contacterid'>
			<div class="search-group">
				<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="col-sm-4">
							<label for="site_name" class="control-label">Search By <?= BUSINESS_CAT ?>:</label>
						</div>
						<div class="col-sm-8">
							<select data-placeholder="Select a <?= BUSINESS_CAT ?>" name="search_client" class="chosen-select-deselect form-control">
								<option value=""></option>
								<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `contactid` IN (SELECT `businessid` FROM `tickets` WHERE `deleted`=0)"),MYSQLI_ASSOC));
								foreach($query as $rowid) { ?>
									<option <?php if ($rowid == $search_client) { echo " selected"; } ?> value='<?php echo  $rowid; ?>' ><?php echo get_client($dbc, $rowid); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="col-sm-4">
							<label for="site_name" class="control-label">Search By Customer:</label>
						</div>
						<div class="col-sm-8">
							<select data-placeholder="Select a Customer" name="search_customer" class="chosen-select-deselect form-control" width="380">
								<option value=""></option>
								<?php
								$query = mysqli_query($dbc,"SELECT contactid, businessid, name FROM `contacts` WHERE `contactid` IN (SELECT `contacts`.`contactid` FROM `tickets` LEFT JOIN `contacts` ON CONCAT(',',IFNULL(`tickets`.`clientid`,0),',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `tickets`.`deleted`=0)");
								while($row = mysqli_fetch_array($query)) {
									?><option <?php if ($row['contactid'] == $search_customer) { echo " selected"; } ?> value='<?php echo  $row['businessid']; ?>' ><?php echo decryptIt($row['name']); ?></option>
							<?php	} ?>
							</select>
						</div>
					</div>
					<?php if($scrum_tab != 'personal' && $scrum_tab != 'staff'): ?>
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div class="col-sm-4">
								<label for="site_name" class="control-label">Search By Staff:</label>
							</div>
							<div class="col-sm-8">
								<select data-placeholder="Select Staff" name="search_user" class="chosen-select-deselect form-control" width="380">
									<option value=""></option>
									<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` IN (SELECT `contacts`.`contactid` FROM `tickets` LEFT JOIN `contacts` ON CONCAT(',',IFNULL(`tickets`.`contactid`,0),',',IFNULL(`tickets`.`internal_qa_contactid`,0),',') LIKE CONCAT('%,',`contacts`.`contactid`,',%'))"),MYSQLI_ASSOC));
									foreach($query as $rowid) { ?>
										<option <?php if ($rowid == $search_user) { echo "selected"; } ?> value='<?php echo  $rowid; ?>' ><?php echo get_contact($dbc, $rowid); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="form-group col-lg-3 col-md-4 col-sm-12 col-xs-12">
					<div style="display:inline-block; padding: 0 0.5em;">
						<!-- <button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button> -->
						<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
					</div>
					<a href="<?= WEBSITE_URL ?>/Ticket/index.php?edit=0&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true); return false;" class="btn brand-btn pull-right" style="width:auto;"> Add <?= TICKET_NOUN ?></a>
				</div><!-- .form-group -->
				<div class="clearfix"></div>
			</div>
		</form>
		<div class="clearfix"></div>
    <?php }

	switch($scrum_tab) {
		case 'search': include('scrum_search.php'); break;
		case 'notes': include('scrum_notes.php'); break;
		case 'personal': include('scrum_personal.php'); break;
		case 'company': include('scrum_tickets.php'); break;
		default: include('scrum_tickets.php'); break;
	} ?>

</div>
