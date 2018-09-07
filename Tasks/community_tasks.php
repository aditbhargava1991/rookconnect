<?php
/*
Inventory Listing
*/
include ('../include.php');

$detect = new Mobile_Detect;
?>
<script type="text/javascript" src="tasks.js"></script>
<style type='text/css'>
.ui-state-disabled  { pointer-events: none !important; }
</style>
</head>
<body>
<?php
$contactide = $_SESSION['contactid'];
$get_table_orient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts WHERE contactid='$contactide'"));
$check_table_orient = $get_table_orient['horizontal_communication'];
?>
<script>
setTimeout(function() {

var maxWidth = Math.max.apply( null, $( '.ui-sortable' ).map( function () {
    return $( this ).outerWidth( true );
}).get() );


    var maxHeight = -1;

    $('.ui-sortable').each(function() {
      maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();

    });

$(function() {
  $(".connectedSortable").width(maxWidth).height(maxHeight);
});
$( '.connectedSortable' ).each(function () {
    this.style.setProperty( 'height', maxHeight, 'important' );
	this.style.setProperty( 'width', maxWidth, 'important' );

	<?php if($check_table_orient == 1) { ?>
		$(this).attr('style', 'height:'+maxHeight+'px !important; width:'+maxWidth+'px !important');
	<?php } else { ?>
		$(this).attr('style', 'height:'+maxHeight+'px !important;');
	<?PHP } ?>
});

}, 200);

function jump_to(i) {
	$('#scrum_tickets').scrollLeft(0);
	$('#scrum_tickets').scrollLeft($('#sortable'+i).position().left - 40);
}
$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
});
function choose_user(target, type, id) {
	var title	= 'Choose a User';
	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		var height = $(this).contents().find('option').length * $(this).contents().find('select').height();
		$(this).contents().find('select').data({type: type, id: id});
		this.style.height = (height + this.contentWindow.document.body.offsetHeight + 180) + 'px';
		$(this).contents().find('.btn').off();
		$(this).contents().find('.btn').click(function() {
			if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to send the '+target+' to the selected user?')) {
				if(target == 'alert') {
					$.ajax({
						method: 'POST',
						url: 'task_ajax_all.php?fill=taskalert',
						data: { id: id, type: type, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'email') {
					$.ajax({
						method: 'POST',
						url: 'task_ajax_all.php?fill=taskemail',
						data: { id: id, type: type, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'reminder') {
					$.ajax({
						method: 'POST',
						url: 'task_ajax_all.php?fill=taskreminder',
						data: { id: id, type: type, schedule: date, user: $(this).closest('body').find('select').val() },
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
function send_alert(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	choose_user('alert', type, task_id);
}
function send_email(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	choose_user('email', type, task_id);
}
function send_reminder(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	var name_id = (type == 'task board' ? 'board_' : '');
	$('[name=reminder_'+name_id+task_id+']').show().focus();
	$('[name=reminder_'+name_id+task_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reminder_'+name_id+task_id+']').change(function() {
		$(this).hide();
		var date = $(this).val().trim();
		$(this).val('');
		if(date != '') {
			choose_user('reminder', type, task_id, date);
		}
	});
}
function send_reply(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	$('[name=reply_'+task_id+']').show().focus();
	$('[name=reply_'+task_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reply_'+task_id+']').blur(function() {
		$(this).hide();
		var reply = $(this).val().trim();
		$(this).val('');
		if(reply != '') {
			var today = new Date();
			var save_reply = reply + " (Reply added by <?php echo decryptIt(decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']); ?> at "+today.toLocaleString()+")";
			$.ajax({
				method: 'POST',
				url: 'task_ajax_all.php?fill=taskreply',
				data: { taskid: task_id, reply: save_reply },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
}
function quick_add_time(task) {
	task_id = $(task).parents('span').data('task');
	$('[name=task_time_'+task_id+']').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: 'task_ajax_all.php?fill=task_quick_time',
				data: { id: task_id, time: time+':00' },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
	$('[name=task_time_'+task_id+']').timepicker('show');
}
function attach_file(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task_board';
		task_id = task_id.substring(5);
	}
	var file_id = 'attach_'+(type == 'task' ? '' : 'board_')+task_id;
	$('[name='+file_id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$('[name='+file_id+']')[0].files[0]);
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "task_ajax_all.php?fill=task_upload&type="+type+"&id="+task_id,
			data: fileData,
			complete: function(result) {
				console.log(result.responseText);
				window.location.reload();
				//alert('Your file has been uploaded.');
			}
		});
	});
	$('[name='+file_id+']').click();
}
function flag_item(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task_board';
		task_id = task_id.substring(5);
	}
	$.ajax({
		method: "POST",
		url: "task_ajax_all.php?fill=taskflag",
		data: { type: type, id: task_id },
		complete: function(result) {
			console.log(result.responseText);
			if(type == 'task') {
				$(task).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
			} else {
				$(task).closest('form').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
			}
		}
	});
}
function archive(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	if(type == 'task' && confirm("Are you sure you want to archive this task?")) {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "task_ajax_all.php?fill=delete_task&taskid="+task_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				window.location.reload();
				console.log(response.responseText);
			}
		});
	}
	else if(confirm("Are you sure you want to archive this task board?")) {
		window.location = "<?php echo WEBSITE_URL; ?>/Tasks/add_task_board.php?deleteid=" + task_id;
	}
}
</script>
<?php include_once ('../navigation.php');
checkAuthorised('tasks');
?>
<div class="container">
	<div class="iframe_holder" style="display:none;">
		<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
		<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
		<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
	</div>
	<div class="row hide_on_iframe">

    <h1 class="single-pad-bottom pull-left">Community Task Boards</h1>
    <?php
    if(config_visible_function($dbc, 'contact') == 1) {
        //echo '<a href="field_config_tasks.php?type=tab" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br>';
    }

    echo '<br><a href="field_config_project_manage.php?category=how_to" class="pull-right"><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
	echo '<span class="popover-examples list-inline"><a class="pull-right" style="margin:-5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to add/remove your task boards."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';

	echo '<div class="clearfix"></div>';

    if (strpos($_SERVER['REQUEST_URI'], '/tasks.php') !== false) {
        $active = 'active_tab';
    }
    else {
        $active = '';
    }

    echo '<div class="tab-container"><div class="pull-left tab"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to see your personal task board for the day."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
	echo "<a href='tasks.php?category=All'><button type='button' class='btn brand-btn mobile-block'>My Tasks</button></a></div>";

	$get_field_task_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT task_dashboard_tile FROM task_dashboard"));
    $tasks_name = explode(',' , $get_field_task_config['task_dashboard_tile']);
    $tasks_name = array_filter($tasks_name);
    foreach($tasks_name as $task_name) {
        $task_file_path = str_replace(" ","_",strtolower($task_name));
        if (strpos($_SERVER['REQUEST_URI'], $task_file_path) !== false) {
            $active = 'active_tab';
        }
        else {
            $active = '';
        }
		switch($task_file_path) {
			case 'company_tasks': $info = "Click here to see everyone in your company."; break;
			case 'community_tasks': $info = "Click here to see everyone in the ROOK community."; break;
			case 'business_tasks': $info = "Click here to view all business tasks."; break;
			case 'client_tasks': $info = "Click here to view all client tasks."; break;
			case 'reporting': $info = "Click here to see all task activity."; break;
			default: $info = "Unknown Tab"; break;
		}

        echo '<div class="pull-left tab"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="'.$info.'"><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
        echo "<a href='" . $task_file_path . ".php?category=All'><button type='button' class='btn brand-btn mobile-block ".$active."'>" . $task_name . "</button></a></div>";
    }

	echo '<div class="clearfix"></div>';

    //echo '<a class="btn brand-btn pull-right" href="#"  onclick="wwindow.open(\'add_tasklist.php\', \'newwindow\', \'width=1000, height=900\'); return false;">Add Task</a>';
	//echo '<h2><span class="iframe_open">Add Task</span></h2>';
    ?>


	<input type='hidden' value='<?php echo $contactide; ?>' class='contacterid'>
	<div class="col-sm-2">
		<span style='padding:5px; font-weight:bold;'>Vertical View: </span><input onclick="handleClick(this);" type='radio' style='width:20px; height:20px;' <?php if($check_table_orient !== 1) { echo 'checked'; } ?> name='horizo_vert' class='horizo_vert' value=''>
	</div>
	<div class="col-sm-3">
		<span style='padding:5px; font-weight:bold;'>Horizontal View (Mobile): </span><input onclick="handleClick(this);" <?php if($check_table_orient == 1) { echo 'checked'; } ?> type='radio' style='width:20px; height:20px;' name='horizo_vert' class='horizo_vert' value='1'>
	</div>
	<br><br>
	<div class="clearfix"></div>
        <?php
        $ffm_rook_db = @mysqli_connect('localhost', 'ffm_rook_user', 'mIghtyLion!542', 'ffm_rook_db');

        $category = $_GET['category'];
        $contactid = $_SESSION['contactid'];
		echo '<div class="mobile-100-container">';
        $current_url = $_SERVER['SERVER_NAME'];
        $result = mysqli_query($ffm_rook_db, "SELECT * FROM task_board WHERE board_security='Community' AND software_url='$current_url'");
        while($row = mysqli_fetch_array($result)) {
            $active_daily = '';
            if((!empty($_GET['category'])) && ($_GET['category'] == $row['taskboardid'])) {
                $active_daily = 'active_tab';
            }
            echo "<a href='community_tasks.php?category=".$row['taskboardid']."'><button type='button' class='mobile-100 btn brand-btn mobile-block ".$active_daily."' >".$row['board_name']."</button></a>&nbsp;&nbsp;";
        }

        ?>
        <br><br>
		<?php
		if($_GET['category'] != 'All') {
			$query_check_credentials = "SELECT * FROM task_board_document WHERE taskboardid='".$_GET['category']."' ORDER BY taskboarddocid DESC";
			$result = mysqli_query($dbc, $query_check_credentials);
			$num_rows = mysqli_num_rows($result);
			if($num_rows > 0) {
				echo "<table class='table table-bordered' style='width:100%;'>
				<tr class='hidden-xs hidden-sm'>
				<th>Document</th>
				<th>Date</th>
				<th>Uploaded By</th>
				</tr>";
				while($row = mysqli_fetch_array($result)) {
					echo '<tr>';
					$by = $row['created_by'];
					echo '<td data-title="Schedule"><a href="download/'.$row['document'].'" target="_blank">'.$row['document'].'</a></td>';
					echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
					echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
					//echo '<td data-title="Schedule"><a href=\'delete_restore.php?action=delete&ticketdocid='.$row['ticketdocid'].'&ticketid='.$row['ticketid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
					echo '</tr>';
				}
				echo '</table>';
			}
		}
		?>
		</div>
		<?php if($_GET['category'] !== 'All'):
		$task_flag = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM `task_board` WHERE `taskboardid`='{$_GET['category']}'"))['flag_colour']; ?>
		<form name="form_sites" method="post" action="" class="form-inline" role="form" <?php echo ($task_flag == '' ? '' : 'style="background-color: #'.$task_flag.';"'); ?>>
			<span class="pull-right" style="cursor: pointer;" data-task="BOARD<?php echo $_GET['category']; ?>">
				<span style="padding: 0.25em 0.5em;" title="Flag This!" onclick="flag_item(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-flag-icon.png" style="height:2em;"></span>
				<span style="padding: 0.25em 0.5em;" title="Send Alert" onclick="send_alert(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-alert-icon.png" style="height:2em;"></span>
				<span style="padding: 0.25em 0.5em;" title="Send Email" onclick="send_email(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-email-icon.png" style="height:2em;"></span>
				<span style="padding: 0.25em 0.5em;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-reminder-icon.png" style="height:2em;"></span>
				<span style="padding: 0.25em 0.5em;" title="Attach File" onclick="attach_file(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-attachment-icon.png" style="height:2em;"></span>
				<span style="padding: 0.25em 0.5em;" title="Archive Task Board" onclick="archive(this); return false;"><img src="<?php echo WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" style="height:2em;"></span>
				<br /><input type="text" name="reminder_board_<?php echo $_GET['category']; ?>" style="display:none; margin-top: 2em;" class="form-control datepicker" />
			</span>
			<input type="file" name="attach_board_<?php echo $_GET['category']; ?>" style="display:none;" />
		<?php endif; ?>
		<div class="clearfix"></div>
        <div class="scrum_tickets" id="scrum_tickets">
        <?php
        if($_GET['category'] != 'All') {
            $taskboardid = $_GET['category'];
            $get_custom =	mysqli_fetch_assoc(mysqli_query($ffm_rook_db,"SELECT task_path FROM task_board WHERE	taskboardid='$taskboardid'"));
            $task_path = $get_custom['task_path'];

            $get_custom2 =	mysqli_fetch_assoc(mysqli_query($ffm_rook_db,"SELECT milestone, timeline  FROM project_path_milestone WHERE	project_path_milestone='$task_path'"));
            $each_tab = explode('#*#', $get_custom2['milestone']);
            $timeline = explode('#*#', $get_custom2['timeline']);
            $i=0;
            foreach ($each_tab as $cat_tab) {
                $result = mysqli_query($ffm_rook_db, "SELECT * FROM tasklist WHERE task_path='$task_path' AND task_milestone_timeline='$cat_tab' ORDER BY task_path ASC, tasklistid DESC");
                $status = $cat_tab;
                $status = str_replace("&","FFMEND",$status);
                $status = str_replace(" ","FFMSPACE",$status);
                $status = str_replace("#","FFMHASH",$status);

			    $class_on = '';
			    if($check_table_orient == '1') {
					$class_on = 'horizontal-on';
					$class_on_2 = 'horizontal-on-title';
				} else {
					$class_on = '';
					$class_on_2 = '';
				}

				if($i > 0 && $detect->isMobile()) {
					echo '<img src="'.WEBSITE_URL.'/img/icons/rewind.png" style="width: 1.5em; cursor:pointer; margin-left:50px; margin-right: 10px;" onclick="jump_to('.($i-1).');">';
				}
                echo '<ul id="sortable'.$i.'" class="connectedSortable no-sort '.$status.' '.$class_on.'"><li class="ui-state-default ui-state-disabled '.$class_on_2.'">'.$cat_tab.'<br>'.$timeline[$i].'</li>';
                echo '<li class="new_task_box no-sort"><input onChange="changeEndAme(this)" name="add_task" id="add_new_task '.$status.' '.$task_path.'" type="text" class="form-control" /></li>';

                while($row = mysqli_fetch_array( $result )) {
                    //echo '<li id="'.$row['tasklistid'].'" class="ui-state-default '.$class_on.'"><a href="#"  onclick="wwindow.open(\''.WEBSITE_URL.'/Tasks/add_tasklist.php?type='.$row['status'].'&tasklistid='.$row['tasklistid'].'\', \'newwindow\', \'width=1000, height=900\'); return false;">'.limit_text($row['heading'], 5 ). '</a></li>';
					echo '<li id="'.$row['tasklistid'].'" class="ui-state-default '.$class_on.'">';

					echo '<span class="pull-right" style="width: 100%;" data-task="'.$row['tasklistid'].'">';
					echo '<span style="padding: 0 0.5em;" title="Flag This!" onclick="flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="padding: 0 0.5em;" title="Send Alert" onclick="send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="padding: 0 0.5em;" title="Send Email" onclick="send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="padding: 0 0.5em;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="padding: 0 0.5em;" title="Attach File" onclick="attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="padding: 0 0.5em;" title="Reply" onclick="send_reply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="display:inline-block; text-align:center; width:11%" title="Add Time" onclick="quick_add_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '<span style="padding: 0 0.5em;" title="Archive Task" onclick="archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="return false;"></span>';
					echo '</span>';
					echo '<br /><input type="text" name="reply_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
					echo '<input type="text" name="task_time_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />';
					echo '<br /><input type="text" name="reminder_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
					echo '<input type="file" name="attach_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';

					echo '<span class="iframe_open_add" id="'.$row['status'].'_'.$row['tasklistid'].'" style="cursor:pointer;">'.limit_text($row['heading'], 5 ). '<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="height:1.5em; width:1.5em;" /></span></li>';
                }
                echo '</ul>';
				if($i < count($each_tab) - 1 && $detect->isMobile()) {
					echo '<img src="'.WEBSITE_URL.'/img/icons/fast-forward.png" style="width: 1.5em; cursor:pointer; margin-right: 50px;" onclick="jump_to('.($i+1).');">';
				}
                $i++;
            }
        }
        ?>
        </div>

		</form>
	</div>
</div>

<?php include ('../footer.php'); ?>
