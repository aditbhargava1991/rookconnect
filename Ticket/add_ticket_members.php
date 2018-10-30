<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Members</h3>') ?>
<?php $member_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN ('Members') AND `deleted`=0 AND `status`>0"));
$query = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `src_table`='Members' AND `deleted`=0 AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `tile_name`='".FOLDER_NAME."'".$query_daily);
$member = mysqli_fetch_assoc($query);
$i = 0;
do { ?>
	<div class="multi-block">
		<?php if($access_contacts === TRUE) { ?>
			<div class="form-group">
				<label class="col-sm-<?= strpos($value_config,',Contact Set Hours,') === FALSE ? '4' : '2' ?> control-label"><a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>Member:</label>
				<div class="col-sm-<?= strpos($value_config,',Contact Set Hours,') === FALSE ? '7' : '5' ?>">
					<select name="item_id" data-table="ticket_attached" data-id="<?= $member['id'] ?>" data-id-field="id" data-type="Members" data-type-field="src_table" class="chosen-select-deselect"><option></option>
						<?php foreach($member_list as $member_id) { ?>
							<option <?= $member_id['contactid'] == $member['item_id'] ? 'selected' : '' ?> value="<?= $member_id['contactid'] ?>"><?= $member_id['first_name'].' '.$member_id['last_name'] ?></option>
						<?php } ?>
					</select>
				</div>
				<?php foreach ($field_sort_order as $field_sort_field) { ?>
					<?php if(strpos($value_config,',Contact Set Hours,') !== FALSE && $field_sort_field == 'Contact Set Hours') { ?>
						<label class="col-sm-2 control-label">Billable Hours:</label>
						<div class="col-sm-2">
							<input type="text" name="hours_set" data-table="ticket_attached" data-id="<?= $member['id'] ?>" data-id-field="id" data-type="Members" data-type-field="src_table" class="form-control" value="<?= $member['hours_set'] ?>">
						</div>
					<?php } ?>
				<?php } ?>
				<div class="col-sm-1">
					<span class="show-on-mob pull-left" onclick="$(this).closest('div').find('img').first().click();">More details</span>
					<img class="inline-img pull-left black-color counterclockwise small" onclick="showMember(this);" src="../img/icons/dropdown-arrow.png">
					<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $member['id'] ?>" data-id-field="id" data-type="Members" data-type-field="src_table" value="0">
					<img class="inline-img pull-right" data-history-label="Member" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
					<img class="inline-img pull-right" data-history-label="Member" onclick="remMulti(this);" src="../img/remove.png">
				</div>
				<div class="clearfix"></div>
			</div>
		<?php } else if($member['item_id'] > 0) { ?>
			<div class="form-group">
				<label class="col-sm-<?= strpos($value_config,',Contact Set Hours,') === FALSE ? '4' : '2' ?> control-label"><?= ($member['item_id'] > 0) ? '<a target="_blank" href="../Members/contacts_inbox.php?edit='.$member['item_id'].'"><img class="inline-img no-toggle" src="../img/person.PNG" title="View Profile"></a>' : '' ?>Member:</label>
				<div class="col-sm-<?= strpos($value_config,',Contact Set Hours,') === FALSE ? '7' : '5' ?>">
					<?= get_contact($dbc, $member['item_id']) ?><input type="hidden" name="item_id" value="<?= $member['item_id'] ?>">
				</div>
				<?php foreach ($field_sort_order as $field_sort_field) { ?>
					<?php if(strpos($value_config,',Contact Set Hours,') !== FALSE && $field_sort_field == 'Contact Set Hours') { ?>
						<label class="col-sm-2 control-label">Billable Hours:</label>
						<div class="col-sm-2">
							<?= $member['hours_set'] ?>
						</div>
					<?php } ?>
				<?php } ?>
				<div class="col-sm-1">
					<img class="inline-img pull-left black-color counterclockwise small" onclick="showMember(this);" src="../img/icons/dropdown-arrow.png">
				</div>
				<div class="clearfix"></div>
			</div>
			<?php $pdf_contents[] = ['Member', get_contact($dbc, $member['item_id']).(strpos($value_config,',Contact Set Hours,') !== FALSE ? '<br>Billable Hours: '.$member['hours_set'] : '')]; ?>
		<?php } ?>
		<div id="panel_group_<?= ++$i ?>" class="iframe_div panel-group" style="display:none">
			<?php foreach ($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Members Profile,') !== FALSE && $field_sort_field == 'Members Profile') { ?>
					<div class="panel panel-default" data-tab="profile">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to view the Member's Profile."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#panel_group_<?= $i ?>" href="#collapse_profile_<?= $i ?>" onclick="memberPanels(this);">
								   Members Profile<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>
						<div id="collapse_profile_<?= $i ?>" class="panel-collapse collapse">
							<div class="panel-body no-pad">
								<iframe style="height: 0; width: 100%;" src="">Loading...</iframe>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Members Parental Guardian Family Contact,') !== FALSE && $field_sort_field == 'Members Parental Guardian Family Contact') { ?>
					<div class="panel panel-default" data-tab="guardians">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to view the Member's Parental/Guardian & Family Contact."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#panel_group_<?= $i ?>" href="#collapse_guardian_<?= $i ?>" onclick="memberPanels(this);">
								   Parental/Guardian & Family Contact<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>
						<div id="collapse_guardian_<?= $i ?>" class="panel-collapse collapse">
							<div class="panel-body no-pad">
								<iframe style="height: 0; width: 100%;" src="">Loading...</iframe>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Members Emergency Contact,') !== FALSE && $field_sort_field == 'Members Emergency Contact') { ?>
					<div class="panel panel-default" data-tab="emergency">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to view the Member's Emergency Contact."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#panel_group_<?= $i ?>" href="#collapse_emergency_<?= $i ?>" onclick="memberPanels(this);">
								   Emergency Contact<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>
						<div id="collapse_emergency_<?= $i ?>" class="panel-collapse collapse">
							<div class="panel-body no-pad">
								<iframe style="height: 0; width: 100%;" src="">Loading...</iframe>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Members Medical Details,') !== FALSE && $field_sort_field == 'Members Medical Details') { ?>
					<div class="panel panel-default" data-tab="medical">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to view the Member's Medical Details."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#panel_group_<?= $i ?>" href="#collapse_medical_<?= $i ?>" onclick="memberPanels(this);">
								   Medical Details<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>
						<div id="collapse_medical_<?= $i ?>" class="panel-collapse collapse">
							<div class="panel-body no-pad">
								<iframe style="height: 0; width: 100%;" src="">Loading...</iframe>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Members Key Methodologies,') !== FALSE && $field_sort_field == 'Members Key Methodologies') { ?>
					<div class="panel panel-default" data-tab="methodologies">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to view the Member's Key Methodologies."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#panel_group_<?= $i ?>" href="#collapse_method_<?= $i ?>" onclick="memberPanels(this);">
								   Key Methodologies<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>
						<div id="collapse_method_<?= $i ?>" class="panel-collapse collapse">
							<div class="panel-body no-pad">
								<iframe style="height: 0; width: 100%;" src="">Loading...</iframe>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Members Daily Log Notes,') !== FALSE && $field_sort_field == 'Members Daily Log Notes') { ?>
					<div class="panel panel-default" data-tab="log_notes">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to view the Member's Log Notes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#panel_group_<?= $i ?>" href="#collapse_notes_<?= $i ?>" onclick="memberPanels(this);">
								   Daily Log Notes<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>
						<div id="collapse_notes_<?= $i ?>" class="panel-collapse collapse">
							<div class="panel-body no-pad">
								<iframe style="height: 0; width: 100%;" src="">Loading...</iframe>
							</div>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
			<a href="" class="full-target" target="_blank">View Full Profile</a>
		</div>
	</div>
	<hr class="visible-xs">
<?php } while($member = mysqli_fetch_assoc($query)); ?>