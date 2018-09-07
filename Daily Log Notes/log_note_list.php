<?php include_once('../include.php');
checkAuthorised('daily_log_notes');
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	ob_clean();
}
include_once('../Daily Log Notes/config.php');
$search_client = '';
$search_from_date = '';
$search_to_date = '';
$clause = '`deleted`=0';
if(!empty($_GET['display_contact'])) {
	$display_contact = $_GET['display_contact'];
}
if(!empty($_GET['min_display'])) {
	$min_display = $_GET['min_display'];
}
if(!empty($_GET['from_url'])) {
	$from_url = urldecode($_GET['from_url']);
}
if(isset($display_contact)) {
	$search_client = $display_contact;
	$clause .= " AND `client_id`='$search_client'";
}
if(!empty($_GET['search_client'])) {
	$search_client = $_GET['search_client'];
	$clause .= " AND `client_id`='$search_client'";
}
if(!empty($_GET['search_from_date'])) {
	$search_from_date = $_GET['search_from_date'];
	$clause .= " AND `note_date` >= '$search_from_date'";
}
if(!empty($_GET['search_to_date'])) {
	$search_to_date = $_GET['search_to_date'];
	$clause .= " AND `note_date` <= '$search_to_date'";
}
if(!empty($_POST['search_client'])) {
	$search_client = $_POST['search_client'];
	$clause .= " AND `client_id`='$search_client'";
}
if(!empty($_POST['search_from_date'])) {
	$search_from_date = $_POST['search_from_date'];
	$clause .= " AND `note_date` >= '$search_from_date'";
}
if(!empty($_POST['search_to_date'])) {
	$search_to_date = $_POST['search_to_date'];
	$clause .= " AND `note_date` <= '$search_to_date'";
} ?>
<script>
$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
});
function search_notes() {
	$.ajax({
		url: '../Daily Log Notes/log_note_list.php',
		method: 'GET',
		data: {
			display_contact: '<?= $display_contact ?>',
			min_display: '<?= $min_display ?>',
			from_url: '<?= urlencode($from_url) ?>',
			search_from_date: $('[name=search_from_date]').val(),
			search_to_date: $('[name=search_to_date]').val()
		},
		success: function(response) {
			$('.daily_log_note_div').html(response);
		}
	});
}
function show_add() {
	$('[name=add_note]').show().focus();
	$('[name=add_note]').blur(function() {
		var note = $(this).val();
		$(this).hide().val('');
		if(note != '') {
			$.ajax({
				method: 'POST',
				url: '../Daily Log Notes/log_note_ajax.php?fill=add_note',
				data: { client: '<?= $search_client ?>', user: '<?= $_SESSION['contactid'] ?>', notes: note },
				complete: function(result) {
					$('[name=add_note]').closest('li').after(result.responseText);
				}
			});
		}
	});
}
function choose_user(target, id, date) {
	var title	= 'Choose a User';
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
						url: '../Daily Log Notes/log_note_ajax.php?fill=alert',
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'email') {
					$.ajax({
						method: 'POST',
						url: '../Daily Log Notes/log_note_ajax.php?fill=email',
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'reminder') {
					$.ajax({
						method: 'POST',
						url: '../Daily Log Notes/log_note_ajax.php?fill=reminder',
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
function send_alert(note) {
	note_id = $(note).parents('span').data('note');
	choose_user('alert', note_id);
}
function send_email(note) {
	note_id = $(note).parents('span').data('note');
	overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=daily_log_notes&id='+note_id, 'auto', false, true);
}
function send_reminder(note) {
	note_id = $(note).parents('span').data('note');
	$('[name=reminder_'+note_id+']').show().focus();
	$('[name=reminder_'+note_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reminder_'+note_id+']').change(function() {
		$(this).hide();
		var date = $(this).val().trim();
		$(this).val('');
		if(date != '') {
			choose_user('reminder', note_id, date);
		}
	});
}
function send_reply(note) {
	note_id = $(note).parents('span').data('note');
	$('[name=reply_'+note_id+']').show().focus();
	$('[name=reply_'+note_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reply_'+note_id+']').blur(function() {
		$(this).hide();
		var reply = $(this).val().trim();
		$(this).val('');
		if(reply != '') {
			var today = new Date();
			var save_reply = reply + "<br />\n<small><em>(Reply added by <?php echo decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?> at "+today.toLocaleString()+")</em></small>";
			$.ajax({
				method: 'POST',
				url: '../Daily Log Notes/log_note_ajax.php?fill=reply',
				data: { id: note_id, reply: save_reply },
				complete: function(result) {
					window.location.reload();
				}
			})
		}
	});
}
function attach_file(note) {
	note_id = $(note).parents('span').data('note');
	var file_id = 'attach_'+note_id;
	$('[name='+file_id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$('[name='+file_id+']')[0].files[0]);
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "../Daily Log Notes/log_note_ajax.php?fill=upload&id="+note_id,
			data: fileData,
			complete: function(result) {
				console.log(result.responseText);
				window.location.reload();
			}
		});
	});
	$('[name='+file_id+']').click();
}
function archive(note) {
	note_id = $(note).parents('span').data('note');
	if(confirm("Are you sure you want to archive this item?")) {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "../Daily Log Notes/log_note_ajax.php?fill=delete&id="+note_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$(note).parents('li').remove();
				console.log(response);
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
<div class="row hide_on_iframe">
	<?php if($search_client != '') {
		$result = mysqli_query($dbc, 'SELECT * FROM client_daily_log_notes WHERE '.$clause.' ORDER BY `note_date` DESC');
		$num_rows = mysqli_num_rows($result);
		if(empty($search_from_date) && empty($search_to_date) && $num_rows > $min_display && !($_GET['max'] > 0)) {
			$search_from_date = date('Y-m-d', strtotime('-7 days'));
			$search_to_date = date('Y-m-d');
			$clause .= " AND `note_date` >= '$search_from_date' AND `note_date` <= '$search_to_date 23:59:59'";
			$result = mysqli_query($dbc, 'SELECT * FROM client_daily_log_notes WHERE '.$clause.' ORDER BY `note_date` DESC');
		}
		echo '<!--SELECT * FROM client_daily_log_notes WHERE '.$clause.' ORDER BY `note_date` DESC-->';
	} ?>
	<form id="form1" name="form1" method="POST" enctype="multipart/form-data" class="form-horizontal" role="form">
		<?php if(!isset($display_contact) && get_config($dbc, 'log_note_tabs') == 'dropdown') { ?>
			<div class="form-group col-sm-5">
				<label for="search_client" class="col-sm-4 control-label">Search By <?= !empty($_GET['tab']) ? $_GET['tab'] : 'Contact' ?>:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select <?= !empty($_GET['tab']) ? $_GET['tab'] : 'Contact' ?>" name="search_client" class="chosen-select-deselect form-control">
						<option value=""></option>
						<?php $category = !empty($_GET['tab']) ? "AND `category`='".filter_var($_GET['tab'],FILTER_SANITIZE_STRING)."'" : " AND `category` IN ('".implode("','",explode(',',get_config($dbc, 'log_note_categories')))."')";
						$client_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `deleted`=0 AND `status`>0 $category"),MYSQLI_ASSOC));
						foreach($client_list as $row_id) {
							?><option <?php if ($row_id == $search_client) { echo " selected"; } ?> value='<?php echo  $row_id; ?>' ><?php echo get_contact($dbc, $row_id); ?></option>
						<?php } ?>
					</select>
			  </div>
		  </div>
		<?php }
		if(!isset($display_contact) || $num_rows > $min_display) { ?>
			<div class="form-group col-sm-5">
				<label for="search_from_date" class="col-sm-4 control-label">Search From Date:</label>
				<div class="col-sm-8">
					<input name="search_from_date" value="<?php echo $search_from_date; ?>" type="text" class="form-control datepicker">
				</div>
			</div>
			<div class="form-group col-sm-5">
				<label for="search_to_date" class="col-sm-4 control-label">Search To Date:</label>
				<div class="col-sm-8">
					<input name="search_to_date" value="<?php echo $search_to_date; ?>" type="text" class="form-control datepicker">
				</div>
			</div>

			<div class="col-sm-2 pull-right">
				<?php if(!isset($display_contact)) { ?>
					<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
					<button type="button" onclick="window.location=''" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
				<?php } else { ?>
					<button class="btn brand-btn mobile-block" onclick="search_notes(); return false;">Search</button>
				<?php } ?>
			</div>
		<?php } ?>
		<div class="clearfix"></div>
	</form>
	
	<?php if($search_client == '') {
		echo "<h3>Select a client to view their log notes.</h3>";
	} else {
		if(vuaed_visible_function_log_notes($dbc) && $_GET['mode'] != 'read') {
			echo '<button class="btn brand-btn mobile-block pull-right" onclick="show_add(); return false;">Add Daily Log Notes</button><div class="clearfix"></div>';
		}
		
		echo "<ul class='connectedChecklist full-width margin-vertical'>";
		echo "<li class='ui-state-default ui-state-disabled no-sort' style='cursor:pointer; font-size: 30px;'>".get_contact($dbc, $search_client)."</li>";
		echo "<li class='no-sort no-pad no-margin'><input type='text' name='add_note' value='' class='form-control' placeholder='Add Daily Log Note' style='display:none;'></li>";

		$i = 0;
		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_array( $result ))
			{
				if($i++ < $_GET['max'] || empty($_GET['max'])) {
					echo '<li class="ui-state-default">';
					echo '<span style="cursor:pointer;">';
					echo '<span class="pull-right" style="display:inline-block; width:100%;" data-note="'.$row['note_id'].'">';
					echo '<span style="display:inline-block; text-align:center; width:16%;" title="Send Alert" onclick="send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="display:inline-block; text-align:center; width:16%;" title="Send Email" onclick="send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="display:inline-block; text-align:center; width:16%;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="display:inline-block; text-align:center; width:16%;" title="Attach File" onclick="attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="display:inline-block; text-align:center; width:16%;" title="Reply" onclick="send_reply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="display:inline-block; text-align:center; width:16%;" title="Archive Item" onclick="archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '</span>';
					echo '<input type="text" name="reply_'.$row['note_id'].'" style="display:none; margin-top: 2em;" class="form-control" />';
					echo '<input type="text" name="reminder_'.$row['note_id'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
					echo '<input type="file" name="attach_'.$row['note_id'].'" style="display:none;" class="form-control" /><div class="clearfix"></div>';
					echo profile_id($dbc, $row['created_by']).'<div class="pull-right" style="width:calc(100% - 3.5em);">'.html_entity_decode($row['note']);
					foreach(explode('#*#', $row['documents']) as $document) {
						if($document != '') {
							echo 'Attachment: <a href="download/'.$document.'">'.$document.'</a><br />';
						}
					}
					echo '<br /><em class="small">Note created by '.get_contact($dbc, $row['created_by']).' at '.$row['note_date'].'</em></div></li>';
				}
			}
		} else {
			echo "<li class='no-sort'>No Notes Found</li>";
		}
		echo "</ul>";

		if(vuaed_visible_function_log_notes($dbc) && $_GET['mode'] != 'read') {
			echo '<button class="btn brand-btn mobile-block pull-right" onclick="show_add(); return false;">Add Daily Log Notes</button><div class="clearfix"></div>';
		}
	} ?>
</div>