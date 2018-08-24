<script>
$(document).ready(function() {
	$('.has-dashboard').sortable({
		connectWith: '.dashboard-list',
		handle: '.est_handle',
		items: '.dashboard-item',
		sort: function(event) {
			var end_distance = window.innerWidth - event.clientX;
			var start_distance = event.clientX - $('.has-dashboard').offset().left;
			clearInterval(keep_scrolling);
			if(end_distance < 20) {
				keep_scrolling = setInterval(function() { $('.has-dashboard').scrollLeft($('.has-dashboard').scrollLeft() + 10); }, 10);
			} else if(start_distance < 20) {
				keep_scrolling = setInterval(function() { $('.has-dashboard').scrollLeft($('.has-dashboard').scrollLeft() - 10); }, 10);
			}
		},
		update: function(event, li) {
			$.ajax({
				url: 'estimates_ajax.php?action=estimate_fields',
				method: 'POST',
				data: {
					id: li.item.data('id'),
					id_field: 'estimateid',
					table: 'estimate',
					field: 'status',
					value: li.item.closest('ul').data('status')
				},
				success: function(response) {
					window.location.reload();
				}
			});
		}
	});
	$('[data-table]').change(saveField);
	$(window).resize(function() {
	}).resize();
    
	$('input[name=completed]').change(saveField);
});
var keep_scrolling = '';
function saveField() {
	if($(this).data('table') != '') { 
		var field = this;alert($(this).data('estimate'));
		$.ajax({
			url: 'estimates_ajax.php?action=estimate_fields',
			method: 'POST',
			data: {
				id: $(this).data('id'),
				id_field: $(this).data('identifier'),
				table: $(this).data('table'),
				field: this.name,
				value: this.value,
				estimate: $(this).data('estimate')
			},
			success: function(response) {
				if(field.name == 'status') {
					window.location.reload();
				} else if(response > 0 && $(field).data('table') != 'estimate_notes') {
					$(field).closest('.dashboard-item').find('[data-table='+$(field).data('table')+']').data('id',response);
				}
				console.log(response);
			}
		});
	}
}
function doubleScroll() {
	$('.has-dashboard').scrollLeft(this.scrollLeft).scroll();
}
function setDoubleScroll() {
	$('.double-scroller').scrollLeft(this.scrollLeft);
	if(this.scrollLeft < 25) {
		$('.left_jump').hide();
	} else {
		$('.left_jump').show();
	}
	if(this.scrollLeft > this.scrollWidth - this.clientWidth - 25) {
		$('.right_jump').hide();
	} else {
		$('.right_jump').show();
	}
}
function clearEstimates() {
	$.ajax({
		url: 'estimates_ajax.php?action=clearEstimates'
	});
	window.location.reload();
}
</script>
<div class="double-scroller"><div></div></div>
<form class="has-dashboard main-screen-white form-horizontal" style="padding:0.5em;overflow-y:hidden;">
	<div>
		<?php $dashboard = filter_var($_GET['dashboard'], FILTER_SANITIZE_STRING);
		if(empty($dashboard)) {
			$dashboard = $_SESSION['contactid'];
		}
		$statuses = $set_status = [];
		foreach($status as $status_name) {
			$statuses[$status_name] = preg_replace('/[^a-z]/','',strtolower($status_name));
			$set_status[] = "'".$statuses[$status_name]."'";
		}
		$sqlstatuses = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) estimates FROM `estimate` WHERE `status` NOT IN ('',".implode(',',$set_status).") AND `deleted`=0".($dashboard > 0 ? " AND CONCAT(',',IFNULL(`assign_staffid`,''),',',IFNULL(`created_by`,''),',') LIKE '%,$dashboard,%'" : "")))['estimates'];
		if($sqlstatuses > 0) {
			$statuses['Uncategorized'] = "misc";
		}
		$approvals = approval_visible_function($dbc, 'estimate');
		foreach($statuses as $status_name => $status_id) {
			$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) total, SUM(`total_retail`) value FROM `estimate` LEFT JOIN (SELECT `estimateid`, SUM(`retail`) `total_retail` FROM `estimate_scope` WHERE `deleted`=0 GROUP BY `estimateid`) `prices` ON `estimate`.`estimateid`=`prices`.`estimateid` WHERE `deleted`=0 AND (`status`!='$closed_status' OR `status_date` >= '$closed_date') AND (`status`='$status_id' OR ('$status_id'='misc' AND `status` NOT IN ('',".implode(',',$set_status).")))".($dashboard > 0 ? " AND CONCAT(',',IFNULL(`assign_staffid`,''),',',IFNULL(`created_by`,''),',') LIKE '%,$dashboard,%'" : ""))); ?>
			<div class="dashboard-list" data-status="<?= $status_id ?>">
				<div class="info-block-header"><a href="?status=<?= $status_id ?>"><h4><?= $status_name ?><?php if($closed_status == $status_id) { ?><img class="inline-img small pull-right no-toggle" data-original-title="Clear Completed Estimates" src="../img/clear-checklist.png" onclick="clearEstimates(); return false;"><?php } ?></h4>
				<div class="small"><span class="pull-left"><?= $summary['total'] ?></span><span class="pull-right">$<?= number_format($summary['value'],2) ?></span></div></a><div class="clearfix"></div></div>
				<ul class="dashboard-list" data-status="<?= $status_id ?>">
					<?php $estimates = mysqli_query($dbc, "SELECT `estimate`.`estimateid`, `estimate_name`, `businessid`, `clientid`, `total_price`, `status`, CURDATE() - `created_date` status_days, `estimatetype`, `projectid`, `add_to_project`, `prices`.`total_retail` FROM `estimate` LEFT JOIN (SELECT `estimateid`, SUM(`retail`) `total_retail` FROM `estimate_scope` WHERE `deleted`=0 GROUP BY `estimateid`) `prices` ON `estimate`.`estimateid`=`prices`.`estimateid` LEFT JOIN (SELECT MIN(`due_date`) due_date, `estimateid` FROM `estimate_actions` WHERE `deleted`=0 GROUP BY `estimateid`) actions ON `estimate`.`estimateid`=actions.`estimateid` WHERE (`status`='$status_id' OR ('$status_id'='misc' AND `status` NOT IN ('',".implode(',',$set_status)."))) AND (`status`!='$closed_status' OR `status_date` >= '$closed_date') AND `deleted`=0".($dashboard > 0 ? " AND CONCAT(',',IFNULL(`assign_staffid`,''),',',IFNULL(`created_by`,''),',') LIKE '%,$dashboard,%'" : "")." ORDER BY actions.`due_date`, `expiry_date` LIMIT 0,".get_config($dbc, "estimate_dashboard_length"));
					while($estimate = mysqli_fetch_array($estimates)) {
						$action = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate_actions` WHERE `estimateid`='{$estimate['estimateid']}' AND `deleted`=0 ORDER BY `due_date` ASC")); ?>
						<li class="dashboard-item textwrap <?= $action['due_date'] == date('Y-m-d') ? 'blue-border' : ($action['due_date'] < date('Y-m-d') ? 'red-border' : '') ?>" data-id="<?= $estimate['estimateid'] ?>">
							<a href="?view=<?= $estimate['estimateid'] ?>"><h4><span class="text-blue"><?= ($estimate['estimate_name'] != '' ? $estimate['estimate_name'] : '[UNTITLED '.$estimate['estimatetype'].']') ?><img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></span><span class="pull-right">$<?= number_format($estimate['total_retail'],2) ?></span></h4></a>
							<img class="pull-right est_handle inline-img no-toggle" src="../img/icons/drag_handle.png" title="Drag">
							<?= get_client($dbc, $estimate['businessid']) ?> <?= get_contact($dbc, $estimate['clientid']) ?> | <?= $estimate['status_days'] ?> days
							<div class="form-group double-gap-top">
								<label class="col-sm-4">Status:</label>
								<div class="col-sm-8">
									<?php if($approvals > 0 || ($estimate['status'] != 'Saved' && $estimate['status'] != 'Pending')) { ?>
										<select name="status" class="chosen-select-deselect" data-table="estimate" data-identifier="estimateid" data-id="<?= $estimate['estimateid'] ?>"><option></option>
											<?php $selected = false;
											foreach($status as $select_status) {
												$select_id = preg_replace('/[^a-z]/','',strtolower($select_status));
												if($status_id == $select_id) {
													$selected = true;
												} ?>
												<option <?= $select_id == $status_id ? 'selected' : '' ?> value="<?= $select_id ?>"><?= $select_status ?></option>
											<?php }
											if(!$selected) { ?>
												<option selected value="<?= $estimate['status'] ?>"><?= $estimate['status'] ?></option>
											<?php } ?>
											<option value="archived">Archive</option>
										</select>
									<?php } else {
										echo '<input number="status" data-table="estimate" data-identifier="estimateid" data-id="'.$estimate['estimateid'].'" type="hidden">';
										echo $estimate['status'];
									} ?>
								</div>
							</div><?php
                            $action_list = mysqli_query($dbc, "SELECT * FROM `estimate_actions` WHERE `estimateid`='{$estimate['estimateid']}' AND `deleted`=0 AND `completed`=0 ORDER BY `due_date` ASC");
                            while($action_item = mysqli_fetch_array($action_list)) { ?>
                                <hr />
                                <div class="form-group">
                                    <label class="col-sm-4">Next Action:</label>
                                    <div class="col-sm-8">
                                        <select name="action" data-table="estimate_actions" data-identifier="id" data-id="<?= $action_item['id'] ?>" data-estimate="<?= $estimate['estimateid'] ?>" class="chosen-select-deselect">
                                            <option></option>
                                            <option <?= $action_item['action'] == 'phone' ? 'selected' : '' ?> value="phone">Phone Call</option>
                                            <option <?= $action_item['action'] == 'email' ? 'selected' : '' ?> value="email">Email</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">Follow Up:</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="due_date" class="form-control datepicker" value="<?= $action_item['due_date'] ?>" data-table="estimate_actions" data-identifier="id" data-id="<?= $action_item['id'] ?>" data-estimate="<?= $estimate['estimateid'] ?>">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-sm-4">Mark Completed:</label>
                                    <div class="col-sm-8">
                                        <input type="checkbox" name="check_completed" class="form-checkbox" value="1" onchange="$(this).closest('div').find('[name=completed]').focus();" style="margin-left:0;" />
                                        <input type="text" name="completed" class="checkbox-text form-control" data-table="estimate_actions" data-id="<?= $action_item['id'] ?>" data-identifier="id" data-estimate="<?= $estimate['estimateid'] ?>" data-id-field="id" placeholder="Follow Up Details" onblur="if(this.value == '') { $(this).closest('.form-group').find('[name=check_completed]').removeAttr('checked'); alert('You must provide details about the follow up.'); }">
                                    </div>
                                </div><?php
                            } ?>
							<div class="clearfix"></div>
							<input type="text" class="form-control" name="notes" value="" style="display:none;" data-table="estimate_notes" data-identifier="id" data-id="" data-estimate="<?= $estimate['estimateid'] ?>" onblur="$(this).val('').hide();">
							<div class="action-icons">
								<?php if($estimate['projectid'] > 0) { ?>
									<a href="../Project/projects.php?edit=<?= $estimate['projectid'] ?>"><img src="../img/icons/create_project.png" class="inline-img no-toggle" title="View <?= PROJECT_NOUN.' #'.$estimate['projectid'] ?>"></a>
								<?php } else { ?>
									<a href="convert_to_project.php?estimate=<?= $estimate['estimateid'] ?>" onclick="overlayIFrame('convert_select_project.php?estimateid=<?= $estimate['estimateid'] ?>');return false;"><img src="../img/icons/create_project.png" class="inline-img no-toggle" title="<?= $estimate['add_to_project'] > 0 ? 'Attach to '.PROJECT_NOUN.' #'.$estimate['add_to_project'] : 'Create '.PROJECT_NOUN.' from '.ESTIMATE_TILE.'.' ?>"></a>
								<?php } ?>
								<a href="?financials=<?= $estimate['estimateid'] ?>"><img src="../img/icons/financials.png" class="inline-img no-toggle" title="View <?= ESTIMATE_TILE ?> Financial Summary."></a>
								<a href="Add Note" onclick="$(this).closest('.dashboard-item').find('[name=notes]').show().focus(); return false;"><img src="../img/notepad-icon-blue.png" class="inline-img no-toggle" title="Add Note to <?= ESTIMATE_TILE ?>."></a>
							<a href="Archive" onclick="$(this).closest('.dashboard-item').find('[name=status]').val('archived').trigger('change.select2').change(); return false;"><img src="../img/icons/ROOK-trash-icon.png" class="inline-img no-toggle" title="Archive the <?= ESTIMATE_TILE ?>."></a>
							</div>
						</li>
					<?php } ?>
					<?= $summary['total'] == 0 ? '<li class="dashboard-item">No '.$status_name.' '.ESTIMATE_TILE.'.</li>' : '' ?>
				</ul>
			</div>
		<?php } ?>
	</div>
</form>