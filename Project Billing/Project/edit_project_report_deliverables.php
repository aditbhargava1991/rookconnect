<?php include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$projecttype = filter_var($_GET['projecttype'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
	$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'")); ?>
<script>
$(document).ready(function() {
	$('[data-table]').change(function() {
		$.post('projects_ajax.php?action=deliverable_date', {
			table: $(this).data('table'),
			field: this.name,
			id: $(this).data('id'),
			value: this.value
		});
	});
});
function selectAll(action) {
	$('button.selector').toggle();
	if(action == 'select') {
		$('.sort_table [name=include]').prop('checked',true);
	} else {
		$('.sort_table [name=include]').removeAttr('checked');
	}
}
</script>
<div id="no-more-tables">
	<?php if($security['edit'] > 0) { ?>
		<label class="form-checkbox"><input type="checkbox" name="includeDetails"> Include <?= TICKET_NOUN ?> Details</label>
		<button class="btn brand-btn pull-right" onclick="getDeliverables('email'); return false;">Send in Email</button>
		<button class="btn brand-btn pull-right" onclick="getDeliverables('pdf'); return false;">Download PDF<img class="inline-img smaller" src="../img/pdf.png"></button>
		<button class="btn brand-btn pull-right selector" onclick="selectAll('select'); return false;" style="display:none;">Select All</button>
		<button class="btn brand-btn pull-right selector" onclick="selectAll(); return false;">Select None</button>
	<?php } ?>
	<?php $sql = "SELECT * FROM (SELECT `tasklist`.`tasklistid` `id`, `tasklist`.`heading`, `tasklist`.`businessid`, `tasklist`.`task_tododate` `assigned_date`, `tasklist`.`deleted`, 'tasklist' `table`, MAX(if(`deliverable`.`output_type`='email',`deliverable`.`datetime`,'')) `email_time`, MAX(if(`deliverable`.`output_type`='pdf',`deliverable`.`datetime`,'')) `pdf_time` FROM `tasklist` LEFT JOIN `project_deliverables_output` `deliverable` ON `deliverable`.`tasklistid`=`tasklist`.`tasklistid` WHERE `projectid`='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` NOT IN ('Archive','Done') GROUP BY `tasklist`.`tasklistid` UNION
		SELECT `tickets`.`ticketid` `id`, `tickets`.`heading`, `tickets`.`businessid`, `tickets`.`to_do_date` `assigned_date`, `tickets`.`deleted`, 'tickets' `table`, MAX(if(`deliverable`.`output_type`='email',`deliverable`.`datetime`,'')) `email_time`, MAX(if(`deliverable`.`output_type`='pdf',`deliverable`.`datetime`,'')) `pdf_time` FROM `tickets` LEFT JOIN `project_deliverables_output` `deliverable` ON `deliverable`.`ticketid`=`tickets`.`ticketid` WHERE `projectid`='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` NOT IN ('Archive','Archived','Done') GROUP BY `tickets`.`ticketid`) deliverables ORDER BY `assigned_date` ASC, `table`, `id` DESC";
	$lines = mysqli_query($dbc, $sql);
	if(mysqli_num_rows($lines) > 0) { ?>
		<div class="form-horizontal col-sm-12" data-tab-name="<?= $head_id ?>">
			<div class="form-group">
				<div class="sort_table">
					<table class="table table-bordered">
						<tr class="hidden-sm hidden-xs">
							<th><?= BUSINESS_CAT ?></th>
							<th><?= TICKET_NOUN ?>/Task</th>
							<th>Estimated Development Date</th>
							<th>Estimated Internal QA Date</th>
							<th>Estimated Customer QA Date</th>
							<?php if($security['edit'] > 0) { ?>
								<th>Include in Email/PDF</th>
							<?php } ?>
						</tr>
						<?php while($line = mysqli_fetch_array($lines)) {
							$label = '';
							$url = '';
							if($line['table'] == 'tasklist') {
								$label = 'Task #'.$line['id'].': '.$line['heading'];
								$url = WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$task['tasklistid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']);
							} else {
								$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='".$line['id']."'"));
								$label = get_ticket_label($dbc, $ticket);
								$url = WEBSITE_URL.'/Ticket/index.php?edit='.$line['id'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']);
							} ?>
							<tr>
								<td data-title="<?= BUSINESS_CAT ?>"><?= get_contact($dbc, $line['businessid'], 'name') ?></td>
								<td data-title="<?= $line['table'] == 'tasklist' ? 'Task' : TICKET_NOUN ?>"><a href="<?= $url ?>" onclick="overlayIFrameSlider(this.href'&calendar_view=true', 'auto', true, true); return false;"><?= $label ?></a></td>
								<td data-title="Estimated Development Date" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><input type="text" name="<?= $line['table'] == 'tasklist' ? 'task_tododate' : 'to_do_date' ?>" data-table="<?= $line['table'] ?>" data-id="<?= $line['id'] ?>" value="<?= $line['assigned_date'] ?>" class="datepicker form-control"></td>
								<td data-title="Estimated Internal QA Date" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><?= ($line['table'] == 'tasklist' ? '' : '<input type="text" data-table="tickets" data-id="'.$line['id'].'" name="internal_qa_date" value="'.$ticket['internal_qa_date'].'" class="datepicker form-control">') ?></td>
								<td data-title="Estimated Customer QA Date" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><?= ($line['table'] == 'tasklist' ? '' : '<input type="text" data-table="tickets" data-id="'.$line['id'].'" name="deliverable_date" value="'.$ticket['deliverable_date'].'" class="datepicker form-control">') ?></td>
								<?php if($security['edit'] > 0) { ?>
									<td data-title="Function">
										<label class="form-checkbox small"><input type="checkbox" <?= $line['email_time'] == '' ? 'checked' : '' ?> name="include" value="<?= $line['table'].'|'.$line['id'] ?>">Include</label>
										<?= $line['email_time'] != '' ? '<br />Sent by email on '.date('Y-m-d',strtotime($line['email_time'])) : '' ?>
									</td>
								<?php } ?>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
		<?php if($security['edit'] > 0) { ?>
			<button class="btn brand-btn pull-right" onclick="getDeliverables('email'); return false;">Send in Email</button>
			<button class="btn brand-btn pull-right" onclick="getDeliverables('pdf'); return false;">Download PDF<img class="inline-img smaller" src="../img/pdf.png"></button>
		<?php } ?>
		<div class="clearfix"></div>
	<?php } ?>
</div>
<div class="email_options" style="display:none;">
	<h4>Send Deliverables in Email</h4>
	<form method="POST" action="deliverable_email.php">
		<input type="hidden" name="projectid" value="<?= $projectid ?>">
		<textarea name="deliver_list"></textarea>
		<div class="form-group">
			<label class="col-sm-4 control-label">Send Email To Address:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="deliver_to">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Email Sender's Address:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="deliver_from" value="<?= decryptIt($_SESSION[STAFF_EMAIL_FIELD]) ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Email Sender's Name:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="deliver_from_name" value="<?= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']) ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Email Subject:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="deliver_subject" value="Deliverables for <?= get_project_label($dbc, $project) ?> as of <?= date('Y-m-d') ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Comments:<br /><em>These comments will be added at the end of the email being sent.</em></label>
			<div class="col-sm-8">
				<textarea name="deliver_comment"></textarea>
			</div>
		</div>
		<input type="hidden" name="output_list">
		<button class="btn brand-btn pull-right" type="submit">Send Email</button>
	</form>
</div>
<div class="pdf_options" style="display:none;">
	<h4>Download Deliverables in PDF</h4>
	<form method="POST" action="deliverable_pdf.php">
		<input type="hidden" name="businessid" value="<?= $project['businessid'] ?>">
		<textarea name="deliver_list"></textarea>
		<div class="form-group">
			<label class="col-sm-4 control-label">Comments:<br /><em>These comments will be added at the bottom of the resulting PDF.</em></label>
			<div class="col-sm-8">
				<textarea name="deliver_comment"></textarea>
			</div>
		</div>
		<input type="hidden" name="output_list">
		<button class="btn brand-btn pull-right" type="submit">Download<img class="inline-img smaller" src="../img/pdf.png"></button>
	</form>
</div>