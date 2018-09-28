<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Wait List</h3>') ?>
<?php $waitlist_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `cell_phone`, `category` FROM `contacts` WHERE `category` IN ('Members','Clients','Customer','Patient') AND `deleted`=0 AND `status`>0"));
$query = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `src_table`='Wait List' AND `deleted`=0 AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `tile_name`='".FOLDER_NAME."'");
$waitlist = mysqli_fetch_assoc($query);
do {
	if($access_waitlist === TRUE) { ?>
		<div class="multi-block">
			<div class="form-group">
				<label class="col-sm-4 control-label">Wait List:</label>
				<div class="col-sm-4">
					<select name="item_id" data-table="ticket_attached" data-id="<?= $waitlist['id'] ?>" data-id-field="id" data-type="Wait List" data-type-field="src_table" class="chosen-select-deselect" onchange="$(this).closest('.multi-block').find('.cell_phone').html('<a href=tel:'+$(this).find('option:selected').data('contact')+'>'+$(this).find('option:selected').data('contact')+'</a>');"><option></option>
						<?php foreach($waitlist_list as $waitlist_option) {
							$waitlist['cell_phone'] = ($waitlist_option['contactid'] == $waitlist['item_id'] ? $waitlist_option['cell_phone'] : $waitlist['cell_phone']);
							$waitlist['category'] = ($waitlist_option['contactid'] == $waitlist['item_id'] ? $waitlist_option['category'] : $waitlist['category']); ?>
							<option <?= $waitlist_option['contactid'] == $waitlist['item_id'] ? 'selected' : '' ?> data-contact="<?= ($waitlist_option['cell_phone'] != '' ? $waitlist_option['cell_phone'] : '') ?>" value="<?= $waitlist_option['contactid'] ?>"><?= $waitlist_option['first_name'].' '.$waitlist_option['last_name'] ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-3 cell_phone">
					<?= ($waitlist['cell_phone'] != '' ? '<a href="tel:">'.$waitlist['cell_phone'].'</a>' : '') ?>
				</div>
				<div class="col-sm-1">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $waitlist['id'] ?>" data-id-field="id" data-type="Wait List" data-type-field="src_table" value="0">
					<img class="inline-img pull-right" data-history-label="Wait List" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
					<img class="inline-img pull-right" data-history-label="Wait List" onclick="remMulti(this);" src="../img/remove.png">
					<?php if($waitlist['category'] == 'Members') { ?>
						<span class="show-on-mob pull-left" onclick="$(this).closest('div').find('img').last().click();">More details</span>
						<img class="inline-img pull-left black-color counterclockwise" onclick="$(this).toggleClass('counterclockwise').closest('.multi-block').find('.member_info').toggle();" src="../img/icons/dropdown-arrow.png"><?php } ?>
				</div>
				<div class="clearfix"></div>
			</div>
			<?php if($waitlist['category'] == 'Members') {
				?><div class="col-sm-12 member_info" style="display:none;">
					<div class="panel-group" id="waitlist<?= $waitlist['id'] ?>">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if(strpos($value_config,',Wait List Members Medications,') !== FALSE && $field_sort_field == 'Wait List Members Medications') { ?>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter Member's Medication details here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
											<a data-toggle="collapse" data-parent="#waitlist<?= $waitlist['id'] ?>" href="#collapse_medication_wait_<?=$waitlist['id'] ?>">
												Medications<span class="glyphicon glyphicon-plus"></span>
											</a>
										</h4>
									</div>

									<div id="collapse_medication_wait_<?=$waitlist['id'] ?>" class="panel-collapse collapse">
										<div class="panel-body">
											<div class="hide-titles-mob">
												<label class="col-sm-6">Medication</label>
												<label class="col-sm-6">Dosage</label>
											</div>
											<?php $medications = mysqli_query($dbc, "SELECT `medicationid`, `medication`.`title`, `medication`.`dosage`, '00:00:00' FROM `medication` WHERE `medication`.`deleted`=0 AND `medication`.`clientid`='{$waitlist['item_id']}'");
											$medication = mysqli_fetch_assoc($medications);
											do { ?>
												<div class="multi-block">
													<div class="col-sm-6">
														<label class="show-on-mob">Medication:</label>
														<input type="text" name="title" <?= $access_all_checkin == TRUE ? 'data-table="medication" data-id="'.$medication['medicationid'].'" data-id-field="medicationid"' : 'readonly' ?> class="form-control" value="<?= $medication['title'] ?>">
													</div>
													<div class="col-sm-6">
														<label class="show-on-mob">Dosage:</label>
														<input type="text" name="dosage" <?= $access_all_checkin == TRUE ? 'data-table="medication" data-id="'.$medication['medicationid'].'" data-id-field="medicationid"' : 'readonly' ?> class="form-control" value="<?= $medication['dosage'] ?>">
													</div>
												</div>
											<?php } while($medication = mysqli_fetch_assoc($medications)); ?>
										</div>
									</div>
								</div>
							<?php } ?>
							<?php if(strpos($value_config,',Wait List Members Guardians,') !== FALSE && $field_sort_field == 'Wait List Members Guardians') { ?>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter Member's Guardian information here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
											<a data-toggle="collapse" data-parent="#waitlist<?= $waitlist['id'] ?>" href="#collapse_guardian_wait_<?=$waitlist['id'] ?>">
												Guardian<span class="glyphicon glyphicon-plus"></span>
											</a>
										</h4>
									</div>

									<div id="collapse_guardian_wait_<?=$waitlist['id'] ?>" class="panel-collapse collapse">
										<div class="panel-body">
											<div class="hide-titles-mob">
												<label class="col-sm-6">Name</label>
												<label class="col-sm-6">Phone Number</label>
											</div>
											<?php $guardians = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `guardians_first_name`, `guardians_last_name`, `guardians_cell_phone`, `guardians_home_phone`, `guardians_work_phone` FROM `contacts_medical` WHERE `contactid`='{$checkin['item_id']}'"));
											foreach(explode('*#*',$guardians['guardians_first_name']) as $i => $first_name) {
												$last_name = explode('*#*',$guardians['guardians_last_name'])[$i];
												$cell = explode('*#*',$guardians['guardians_cell_phone'])[$i];
												$home = explode('*#*',$guardians['guardians_home_phone'])[$i];
												$work = explode('*#*',$guardians['guardians_work_phone'])[$i];
												$phone = $cell == '' ? ($home == '' ? $work : $home) : $cell;
												$name = $first_name.' '.$last_name; ?>
												<div class="multi-block">
													<div class="col-sm-6">
														<label class="show-on-mob">Name:</label>
														<input type="text" name="description" readonly class="form-control" value="<?= $guardian['description'] ?>">
													</div>
													<div class="col-sm-6">
														<label class="show-on-mob">Phone Number:</label>
														<input type="text" name="contact_info" readonly class="form-control" value="<?= $guardian['contact_info'] ?>">
													</div>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							<?php } ?>
							<?php if(strpos($value_config,',Wait List Members Emergency Contacts,') !== FALSE && $field_sort_field == 'Wait List Members Emergency Contacts') { ?>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter Member's Emergency Contact information here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
											<a data-toggle="collapse" data-parent="#waitlist<?= $waitlist['id'] ?>" href="#collapse_emerg_wait_<?=$waitlist['id'] ?>">
												Emergency Contacts<span class="glyphicon glyphicon-plus"></span>
											</a>
										</h4>
									</div>

									<div id="collapse_emerg_wait_<?=$waitlist['id'] ?>" class="panel-collapse collapse">
										<div class="panel-body">
											<div class="hide-titles-mob">
												<label class="col-sm-3">First Name</label>
												<label class="col-sm-3">Last Name</label>
												<label class="col-sm-3">Phone Number</label>
												<label class="col-sm-2">Relationship</label>
											</div>
											<?php $emerg_contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_medical` WHERE `contactid`='{$checkin['item_id']}'"));
											$emergency_first_name = explode('*#*',$emerg_contact['emergency_first_name']);
											$emergency_last_name = explode('*#*',$emerg_contact['emergency_last_name']);
											$emergency_contact_number = explode('*#*',$emerg_contact['emergency_contact_number']);
											$emergency_relationship = explode('*#*',$emerg_contact['emergency_relationship']);
											foreach($emergency_first_name as $i => $first_name) { ?>
												<div class="multi-block">
													<div class="col-sm-3">
														<label class="show-on-mob">First Name:</label>
														<input type="text" name="emergency_first_name" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $first_name ?>">
													</div>
													<div class="col-sm-3">
														<label class="show-on-mob">Last Name:</label>
														<input type="text" name="emergency_last_name" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $emergency_last_name[$i] ?>">
													</div>
													<div class="col-sm-3">
														<label class="show-on-mob">Contact Number:</label>
														<input type="text" name="emergency_contact_number" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $emergency_contact_number[$i] ?>">
													</div>
													<div class="col-sm-2">
														<label class="show-on-mob">Relationship:</label>
														<input type="text" name="emergency_relationship" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $emergency_relationship[$i] ?>">
													</div>
													<?php if($access_all_checkin == TRUE) { ?>
														<div class="col-sm-1">
															<input type="hidden" name="description" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" data-concat="," value="<?= $show_hide[$i] ?>">
															<input type="hidden" name="deleted" value="<?= $show_hide[$i] ?>" onchange="$(this).closest('div').find('[name=description]').val(this.value).change();">
															<button class="btn brand-btn pull-right" onclick="return false;">Save</button>
															<img class="inline-img pull-right" data-history-label="Emergency Contact" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
															<img class="inline-img pull-right" data-history-label="Emergency Contact" onclick="remMulti(this);" src="../img/remove.png">
														</div>
													<?php } ?>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							<?php } ?>
							<?php if(strpos($value_config,',Wait List Members Key Methodologies,') !== FALSE && $field_sort_field == 'Wait List Members Key Methodologies') { ?>
								<div class="panel panel-default" data-tab="methodologies">
									<div class="panel-heading">
										<h4 class="panel-title">
											<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to view the Member's Key Methodologies."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
											<a data-toggle="collapse" data-parent="#waitlist<?= $waitlist['id'] ?>" href="#collapse_waitlist_method_<?= $i ?>" onclick="memberPanels(this);">
											   Key Methodologies<span class="glyphicon glyphicon-plus"></span>
											</a>
										</h4>
									</div>
									<div id="collapse_waitlist_method_<?= $i ?>" class="panel-collapse collapse">
										<div class="panel-body no-pad">
											<iframe style="height: 0; width: 100%;" src="">Loading...</iframe>
										</div>
									</div>
								</div>
							<?php } ?>
							<?php if(strpos($value_config,',Wait List Members Daily Log Notes,') !== FALSE && $field_sort_field == 'Wait List Members Daily Log Notes') { ?>
									<div class="panel panel-default" data-tab="log_notes">
										<div class="panel-heading">
											<h4 class="panel-title">
												<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to view the Member's Log Notes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
												<a data-toggle="collapse" data-parent="#waitlist<?= $waitlist['id'] ?>" href="#collapse_waitlist_notes_<?= $i ?>" onclick="memberPanels(this);">
												   Daily Log Notes<span class="glyphicon glyphicon-plus"></span>
												</a>
											</h4>
										</div>
										<div id="collapse_waitlist_notes_<?= $i ?>" class="panel-collapse collapse">
											<div class="panel-body no-pad">
												<iframe style="height: 0; width: 100%;" src="">Loading...</iframe>
											</div>
										</div>
									</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div><?php
			} ?>
		</div>
	<?php } else if($waitlist['item_id'] > 0) {
		$waitlist_option = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `cell_phone` FROM `contacts` WHERE `contactid`='{$waitlist['item_id']}'")); ?>
		<div class="multi-block">
			<div class="form-group">
				<label class="col-sm-4 control-label">Wait List:</label>
				<div class="col-sm-4">
					<?= decryptIt($waitlist_option['first_name']).' '.decryptIt($waitlist_option['last_name']).($waitlist_option['cell_phone'] != '' ? ' - '.decryptIt($waitlist_option['cell_phone']) : '') ?>
				</div>
				<div class="col-sm-4">
					<?= ($waitlist_option['cell_phone'] != '' ? ' - '.decryptIt($waitlist_option['cell_phone']) : '') ?>
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<?php $pdf_contents[] = ['Wait List', decryptIt($waitlist_option['first_name']).' '.decryptIt($waitlist_option['last_name']).($waitlist_option['cell_phone'] != '' ? ' - '.decryptIt($waitlist_option['cell_phone']) : '').('<br>'.$waitlist_option['cell_phone'] != '' ? ' - '.decryptIt($waitlist_option['cell_phone']) : '')]; ?>
	<?php }
} while($waitlist = mysqli_fetch_assoc($query)); ?>