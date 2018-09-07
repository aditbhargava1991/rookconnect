<style>
.popped-field {
	width: 90% !important;
}
</style>
<script>
$(function() {
    $( ".connectedChecklist" ).sortable({
		beforeStop: function(e, ui) { ui.helper.removeClass('popped-field'); },
		connectWith: ".connectedChecklist",
		delay: 500,
		handle: ".drag_handle",
		items: "li:not(.no-sort)",
		start: function(e, ui) {ui.helper.addClass('popped-field'); },
		update: function( event, ui ) {
			var id = ui.item.attr("id"); //Done
			var afterid = ui.item.prev().attr('id');
			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "equipment_ajax.php?fill=wo_checklist_priority&workorderid=<?= $workorderid ?>&id="+id+"&afterid="+afterid,
				dataType: "html",   //expect html to be returned
				success: function(response){ }
			});
		}
    }).disableSelection();
});

function addItem(textbox) {
	var text = textbox.value;
	$.ajax({    //create an ajax request to load_page.php
		type: "POST",
		url: "equipment_ajax.php?fill=wo_add_checklist",
		data: { new_item: text, workorderid: '<?= $workorderid ?>' },
		dataType: "html",   //expect html to be returned
		success: function(response){
			$(textbox).parents('li').before('<li id="'+response+'" class="ui-state-default" style="position:relative; width:calc(100% - 10px);"><span style="cursor:pointer; font-size: 1em;">'+
				'<input type="hidden" name="new_checklist_id[]" value="'+response+'">'+
				'<input type="checkbox" onclick="checklistChange(this);" value="'+response+'" style="height: 1.25em; width: 1.25em;">'+
				'<span class="pull-right" style="display:inline-block; width:calc(100% - 2em);" data-item="'+response+'">'+
				'<span style="display:inline-block; text-align:center; width:25%;" title="Flag This!" onclick="checklistFlag(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-flag-icon.png" style="height:1.5em;" onclick="return false;"></span>'+
				'<span style="display:inline-block; text-align:center; width:25%;" title="Attach File" onclick="checklistAttach(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>'+
				'<span style="display:inline-block; text-align:center; width:25%;" title="Reply" onclick="checklistReply(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;"></span>'+
				'<span style="display:inline-block; text-align:center; width:25%;" title="Archive Item" onclick="checklistArchive(this); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="return false;"></span>'+
				'</span><input type="text" name="reply_'+response+'" style="display:none;" class="form-control" /><input type="file" name="attach_'+response+'" style="display:none;" class="form-control" /><span class="display-field">'+
				text+'</span><img class="drag_handle pull-right" src="<?= WEBSITE_URL ?>/img/icons/hold.png" style="bottom:5px; height:1.5em; position:absolute; right:5px; width:1.5em;"></span></li>');
			$(textbox).val('');
		}
	});
}

function checklistChange(sel) {
	checked = 0;
    if($(sel).is(':checked')){
        var checked = 1;
    }
    $.ajax({
        type: "GET",
        url: "equipment_ajax.php?fill=wo_checked&id="+sel.value+"&checked="+checked,
        dataType: "html",
        success: function(response){
        }
    });
}

function checklistAttach(item) {
	id = $(item).parents('span').data('item');
	$('[name=attach_'+id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$('[name=attach_'+id+']')[0].files[0]);
		$('[name=attach_'+id+']').off('change').val('');
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "equipment_ajax.php?fill=wo_checklist_upload&id="+id,
			data: fileData,
			complete: function(result) { $(item).parents('li').find('br').last().after('<a href="download/'+result.responseText+'">'+result.responseText+'</a><br />'); }
		});
	});
	$('[name=attach_'+id+']').click();
}
function checklistReply(item) {
	id = $(item).parents('span').data('item');
	$('[name=reply_'+id+']').show().focus();
	$('[name=reply_'+id+']').blur(function() {
		var reply = $(this).val().trim();
		$(this).hide().val('');
		if(reply != '') {
			var today = new Date();
			var save_reply = reply + " (Reply added by <?php echo decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?> at "+today.toLocaleString()+")";
			$.ajax({
				method: 'POST',
				url: 'equipment_ajax.php?fill=wo_checklist_reply',
				data: { id: id, reply: save_reply },
				complete: function(result) { $(item).parents('li').find('.display-field').append('<p>'+save_reply+'</p>'); }
			})
		}
	});
}
function checklistArchive(item) {
	if(confirm("Are you sure you want to archive this item?")) {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "equipment_ajax.php?fill=wo_delete_checklist&id="+$(item).parents('span').data('item'),
			dataType: "html",   //expect html to be returned
			success: function(response){ $(item).closest('li').remove(); }
		});
	}
}
function checklistFlag(item) {
	$.ajax({
		method: "POST",
		url: "equipment_ajax.php?fill=wo_checklist_flag",
		data: { id: $(item).parents('span').data('item') },
		complete: function(result) {
			$(item).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}
</script>

<ul id="sortable" class="connectedChecklist">
	<?php $result = mysqli_query($dbc, "SELECT * FROM `equipment_wo_checklist` WHERE workorderid='$workorderid' AND deleted = 0 ORDER BY sort");
	while($row = mysqli_fetch_array( $result )) {
		echo '<li id="'.$row['checklistid'].'" class="ui-state-default" style="'.($row['flag_colour'] == '' ? '' : 'background-color: #'.$row['flag_colour'].';').' position:relative; width:calc(100% - 10px);">';
		echo '<span style="cursor:pointer; font-size: 1em;"><input type="checkbox" onclick="checklistChange(this);" value="'.$row['checklistid'].'" style="height: 1.25em; width: 1.25em;" name="checklistid[]">';
		echo '<span class="pull-right" style="display:inline-block; width:calc(100% - 2em);" data-item="'.$row['checklistid'].'">';
		echo '<span style="display:inline-block; text-align:center; width:25%;" title="Flag This!" onclick="checklistFlag(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;" onclick="return false;"></span>';
		echo '<span style="display:inline-block; text-align:center; width:25%;" title="Attach File" onclick="checklistAttach(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>';
		echo '<span style="display:inline-block; text-align:center; width:25%;" title="Reply" onclick="checklistReply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;"></span>';
		echo '<span style="display:inline-block; text-align:center; width:25%;" title="Archive Item" onclick="checklistArchive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="return false;"></span>';
		echo '</span>';
		echo '<input type="text" name="reply_'.$row['checklistid'].'" style="display:none;" class="form-control" />';
		echo '<input type="file" name="attach_'.$row['checklistid'].'" style="display:none;" class="form-control" />';
		echo '<span class="display-field">'.html_entity_decode($row['checklist']).'</span>';
		$documents = mysqli_query($dbc, "SELECT * FROM `equipment_wo_checklist_uploads` WHERE `checklistid`='".$row['checklistid']."'");
		while($doc = mysqli_fetch_array($documents)) {
			if($doc['type'] == 'Link') {
				echo '<a href="'.$doc['link'].'">'.$doc['link'].' (Link added by '.get_staff($dbc, $doc['created_by']).' on '.$doc['created_date'].')</a><br />';
			} else {
				echo '<a href="download/'.$doc['link'].'">'.$doc['link'].' (Uploaded by '.get_staff($dbc, $doc['created_by']).' on '.$doc['created_date'].')</a><br />';
			}
		}
		echo '<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="bottom:5px; height:1.5em; position:absolute; right:5px; width:1.5em;" /></span>';
		echo '</li>';
	} ?>
	<li class="new_task_box no-sort"><input onChange="addItem(this)" name="add_checklist" placeholder="Add New Checklist Item" type="text" class="form-control" /></li>
</ul>