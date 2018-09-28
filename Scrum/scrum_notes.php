<?php if(empty($_GET['date'])) {
	$scrum_list = $dbc->query("SELECT * FROM `daysheet_notepad` WHERE `contactid`=0 AND `date` != '' ORDER BY `date` DESC");
	if($scrum_list->num_rows > 0) { ?>
		<table class="table table-bordered table-striped">
            <thead>
                <tr class="hidden-sm hidden-xs">
                    <th>Entry</th>
                    <th>Staff</th>
                    <th>Date Created</th>
                </tr>
            </thead>
			<?php while($notes = $scrum_list->fetch_assoc()) {
				$note = strip_tags(html_entity_decode($notes['notes']));
				$offset = strrpos($note,' ',150); ?>
				<tr>
					<td data-title="Entry"><a href="?tab=notes&date=<?= $notes['date'] ?>"><?= substr($note, 0, $offset ?: 150) ?></a></td>
					<td data-title="Staff"><?php $staff_list = [];
					foreach(array_filter(explode(',',$notes['assigned'])) as $staff) {
						$staff_list[] = get_contact($dbc, $staff);
					}
					echo implode(', ',$staff_list); ?></td>
					<td data-title="Date Created"><a href="?tab=notes&date=<?= $notes['date'] ?>"><?= $notes['date'].' '.$notes['start_time'].(!empty($notes['start_time']) && !empty($notes['end_time']) ? ' - ' : '').$notes['end_time'] ?></a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<h3>No Notes Found</h3>';
	}
} else  {
	$date = date('Y-m-d', strtotime($_GET['date']));
	$scrum_notes = $dbc->query("SELECT * FROM `daysheet_notepad` WHERE `contactid`=0 AND `date`='$date' AND `date` != ''")->fetch_assoc(); ?>
	<script>
	var saveTimer = '';
	var loadTimer = setInterval(get_scrum_notes, 5000);
	var start_time = <?= $scrum_notes['timer_start'] > 0 ? time() - $scrum_notes['timer_start'] : 0 ?>;
	$(document).ready(function() {
		$('[name=scrum_notes]').off('keyup',saveField).keyup(saveField);
		$('[name=assigned]').change(function() {
			var id_list = [];
			$(this).find('option:selected').each(function() {
				if(this.value == 'ALL_STAFF') {
					$(this).removeAttr('selected');
					$('[name=assigned] option').filter(function() { return this.value > 0; }).prop('selected',true);
					$('[name=assigned]').change();
				} else if(this.value > 0) {
					id_list.push(this.value);
				}
			});
			$.post('scrum_ajax_all.php?action=scrum_staff_save', { staff: id_list.join(','), date: '<?= $date ?>' });
		});
		if(start_time > 0) {
			$('[name=timer]').timer({
				seconds: start_time //Specify start time in seconds
			});
			$('.btn.timer').toggle();
		}
		$('.btn.timer.start').click(function() {
			$('[name=timer]').timer({
				seconds: 0,
				editable: false
			});
			$('.btn.timer').toggle();
			$.post('scrum_ajax_all.php?action=scrum_timer_start', { date: '<?= $date ?>' },function(response) {console.log(response);});
		});
		$('.btn.timer.stop').click(function() {
			$('[name=timer]').timer('remove');
			$('.btn.timer').toggle();
			$.get('scrum_ajax_all.php?action=scrum_timer_stop',function(response) {console.log(response);});
		});
	});
	function get_scrum_notes() {
		$.post('scrum_ajax_all.php?action=scrum_notes_load', {
			date: '<?= $date ?>'
		}, function(response) {
			if(tinyMCE.get($('[name=scrum_notes]').attr('id')).getContent() != response) {
				var pos = tinyMCE.activeEditor.selection.getBookmark(2,true);
				var content = tinyMCE.get($('[name=scrum_notes]').attr('id')).getContent();
				tinyMCE.get($('[name=scrum_notes]').attr('id')).setContent(response);
				tinyMCE.activeEditor.selection.moveToBookmark(pos);
			}
		});
	}
	function saveFieldMethod(field) {
		clearTimeout(saveTimer);
		clearInterval(loadTimer);
		saveTimer = setTimeout(function() {
			$.post('scrum_ajax_all.php?action=scrum_notes_save', {
				date: '<?= $date ?>',
				notes: $('[name=scrum_notes]').val()
			}, function() {
				doneSaving();
				clearInterval(loadTimer);
				loadTimer = setInterval(get_scrum_notes, 5000);
			});
		}, 3000);
	}
	</script>
	<div class="form-horizontal">
		<div class="form-group">
			<label class="col-sm-4 control-label">Participants:</label>
			<div class="col-sm-8">
				<select class="chosen-select-deselect" multiple data-placeholder="Select Staff" name="assigned">
					<option value="ALL_STAFF">Select All</option>
					<?php foreach(sort_contacts_query($dbc->query("SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."")) as $staff) { ?>
						<option <?= in_array($staff['contactid'],explode(',',$scrum_notes['assigned'])) ? 'selected' : '' ?> value="<?= $staff['contactid'] ?>"><?= $staff['full_name'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Track Time:</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="timer" value="<?= $scrum_notes['timer_start'] > 0 ? time() - $scrum_notes['timer_start'] : '' ?>">
			</div>
			<div class="col-sm-2">
				<button class="btn brand-btn timer start col-sm-12">Start</button>
				<button class="btn brand-btn timer stop col-sm-12" style="display:none;">Stop</button>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-12">Scrum Notes <a href="?scrum_tab=notes&date=<?= date('Y-m-d', strtotime($date.'-1day')) ?>"><img class="inline-img-height smaller" src="<?= WEBSITE_URL ?>/img/icons/back-arrow.png"></a>
				<a href="" onclick="$('.scrum_date').focus(); return false;"><?= $date ?><img class="inline-img smaller" src="<?= WEBSITE_URL ?>/img/calendar.png"></a>
				<input type="text" style="border:0;width:0;" class="scrum_date datepicker" onchange="window.location.replace('?scrum_tab=notes&date='+this.value);" value="<?= $date ?>">
				<a href="?scrum_tab=notes&date=<?= date('Y-m-d', strtotime($date.'+1day')) ?>"><img class="inline-img-height smaller" src="<?= WEBSITE_URL ?>/img/icons/next-arrow.png"></a>
			</label>
			<div class="col-sm-12">
				<textarea name="scrum_notes" style="height:30vh;"><?= html_entity_decode($scrum_notes['notes'],ENT_QUOTES) ?></textarea>
			</div>
		</div>
		<a href="" onclick="window.location.replace('?scrum_tab=notes'); return false;" class="pull-right btn brand-btn">Save</a>
	</div>
	</div></div>
<?php } ?>