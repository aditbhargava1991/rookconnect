<script type="text/javascript">
function completeStopStatus(btn, stop_id) {
	$.ajax({
		async: false,
		url: '../Ticket/ticket_ajax_all.php?action=customer_sign_off_complete_status',
		method: 'POST',
		data: { id: stop_id },
		success: function(response) {
			if(response != '') {
				<?php if(IFRAME_PAGE && strpos($_SERVER['SCRIPT_NAME'],'edit_ticket_tab') !== FALSE) { ?>
					<?php if(strpos($value_config, ','."Customer Stop Status".',') === FALSE) { ?>
						window.parent.$('select[name="status"][data-table="ticket_schedule"][data-id="'+stop_id+'"]').val(response).trigger('change.select2');
					<?php } ?>
					window.parent.$('[name="complete"][data-table="ticket_schedule"][data-id="'+stop_id+'"]').prop('checked', true);
				<?php } else { ?>
					<?php if(strpos($value_config, ','."Customer Stop Status".',') === FALSE) { ?>
						$('select[name="status"][data-table="ticket_schedule"][data-id="'+stop_id+'"]').val(response).trigger('change.select2');
					<?php } ?>
					$('[name="complete"][data-table="ticket_schedule"][data-id="'+stop_id+'"]').prop('checked', true);
				<?php } ?>
			}
		}
	});
}
</script>
<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Customer Notes</h3>') : '' ?>
<?php if($ticketid > 0) {
	$dbc->query("INSERT INTO `ticket_attached` (`ticketid`, `src_table`, `line_id`) SELECT `ticketid`, 'customer_approve', `id` FROM `ticket_schedule` WHERE `deleted`=0 AND `ticketid`='$ticketid' AND `type` NOT IN ('warehouse') AND `id` NOT IN (SELECT `line_id` FROM `ticket_attached` WHERE `deleted`=0 AND `ticketid`='$ticketid' AND `src_table`='customer_approve')");
    $dbc->query("UPDATE `ticket_attached` LEFT JOIN `ticket_schedule` ON `ticket_attached`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_attached`.`line_id`=`ticket_schedule`.`id` AND `ticket_attached`.`src_table`='customer_approve' SET `ticket_attached`.`deleted`=`ticket_schedule`.`deleted` WHERE `ticket_attached`.`ticketid`='$ticketid'");
}
$customer_approvals = $dbc->query("SELECT `ticket_attached`.*, IFNULL(`ticket_schedule`.`location_name`,`tickets`.`pickup_name`) `location_name`, `ticket_schedule`.`client_name`, `ticket_schedule`.`id` `stop` FROM `ticket_attached` LEFT JOIN `ticket_schedule` ON `ticket_attached`.`line_id`=`ticket_schedule`.`id` AND `ticket_schedule`.`deleted`=0 LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` AND `tickets`.`ticketid` > 0 WHERE `src_table`='customer_approve' AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0");
while($customer_approval = $customer_approvals->fetch_assoc()) {
	if(!($_GET['stop'] > 0) || $_GET['stop'] == $customer_approval['stop']) {
		echo '<div id="tab_section_ticket_customer_notes_'.$customer_approval['stop'].'" class="customer_notes tab-section">';
            echo "<h4>".$customer_approval['location_name'].' '.$customer_approval['client_name']."</h4>";
			foreach($field_sort_order as $field_sort_field) {
				if($customer_approval['completed'] == 0 && (strpos($value_config,',Customer Slider,') === FALSE || (IFRAME_PAGE && strpos($_SERVER['SCRIPT_NAME'],'edit_ticket_tab') !== FALSE) || $access_any > 0)) {
					if(strpos($value_config, ','."Customer Slider".',') !== FALSE && $field_sort_field == 'Customer Slider') {
						if(IFRAME_PAGE && strpos($_SERVER['SCRIPT_NAME'],'edit_ticket_tab') !== FALSE) { ?>
							<a href="../blank_loading_page.php" class="pull-right"><img class="slider-close" src="../img/icons/cancel.png"></a>
						<?php } else { ?>
							<button class="btn brand-btn pull-right" onclick="overlayIFrameSlider('../Ticket/edit_ticket_tab.php?tab=ticket_customer_notes&tile_name=<?= $_GET['tile_name'] ?>&ticketid=<?= $ticketid ?>&stop=<?= $customer_approval['stop'] ?>', '95%', true, true); return false;">Get Customer Feedback</button>
							<div class="clearfix"></div>
						<?php } ?>
					<?php }
					if(strpos($value_config, ','."Customer Stop Status".',') !== FALSE && $field_sort_field == 'Customer Stop Status') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Status:</label>
							<div class="col-sm-8">
								<?php $attached_stop = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `id` = '".$customer_approval['line_id']."'")); ?>
								<select data-placeholder="Select a Status..." name="status" data-table="ticket_schedule" data-id="<?= $attached_stop['id'] ?>" data-id-field="id" id="status" class="chosen-select-deselect"><option/>
									<?php foreach(explode(',',get_config($dbc, 'ticket_status')) as $cat_tab) {
										echo "<option ".($attached_stop['status'] == $cat_tab ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
									} ?>
								</select>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Contacted".',') !== FALSE && $field_sort_field == 'Customer Contacted') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer Contacted:</label>
							<div class="col-sm-8">
								<label class="form-checkbox"><input type="checkbox" name="checked_in" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" value="<?= empty($customer_approval['checked_in']) ? date('H:i:s') : $customer_approval['checked_in'] ?>" <?= empty($customer_approval['checked_in']) ? '' : 'checked' ?> onclick="var time = new Date(); $(this).val(time.getHours()+':'+time.getMinutes()+':'+time.getSeconds()).change();"> Contacted <?= empty($customer_approval['checked_in']) ? '' : 'at '.date('h:i a',strtotime($customer_approval['checked_in'])) ?></label>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Reset Details".',') !== FALSE && $field_sort_field == 'Customer Reset Details') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Who Reset and Why:</label>
							<div class="col-sm-12">
								<textarea name="location_from" class="full-width no_tools" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id"><?= html_entity_decode($customer_approval['location_from']) ?></textarea>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php } else if(strpos($value_config, ','."Customer Driver Notes".',') !== FALSE && $field_sort_field == 'Customer Driver Notes') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Driver Notes:</label>
							<div class="col-sm-12">
								<textarea name="location_from" class="full-width no_tools" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id"><?= html_entity_decode($customer_approval['location_from']) ?></textarea>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Property Photo".',') !== FALSE && $field_sort_field == 'Customer Property Photo') { ?>
						<div class="form-group">
							<div class="col-sm-12">
								<label class="col-sm-4 control-label">Photo of Property:</label>
								<div class="col-sm-8">
									<!-- <img class="inline-img" src="../img/camera.png" onclick="$(this).next('input').click();"> -->
									<input type="file" name="location_to" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" class="reload_customer_images">
									<div class="uploaded_image gap-top" <?= !file_exists('download/'.$customer_approval['location_to']) || empty($customer_approval['location_to']) ? 'style="display:none;"' : '' ?>>
										<?= file_exists('download/'.$customer_approval['location_to']) ? '<a href="download/'.$customer_approval['location_to'].'" target="_blank"><img src="download/'.$customer_approval['location_to'].'"  style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '' ?>
									</div>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Property Damage".',') !== FALSE && $field_sort_field == 'Customer Property Damage') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">My Property Is Damage Free:</label>
							<div class="col-sm-8">
								<label class="form-checkbox"><input type="radio" name="status__<?= $customer_approval['id'] ?>" value="1" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" <?= $customer_approval['status'] == 1 ? 'checked' : '' ?> onchange="if(this.checked) { $(this).closest('.form-group').find('.notes').hide(); }">Yes</label>
								<label class="form-checkbox"><input type="radio" name="status__<?= $customer_approval['id'] ?>" value="2" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" <?= $customer_approval['status'] == 2 ? 'checked' : '' ?> onchange="if(this.checked) { $(this).closest('.form-group').find('.notes').show(); }">No</label>
							</div>
							<div class="col-sm-12 notes" <?= $customer_approval['status'] == 2 ? '' : 'style="display:none;"' ?>>
								<label class="col-sm-4 control-label">Please Provide Details:</label>
								<textarea name="description" class="full-width noMceEditor" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id"><?= html_entity_decode($customer_approval['description']) ?></textarea>
								<label class="col-sm-4 control-label">Upload an Image:</label>
								<div class="col-sm-8">
									<!-- <img class="inline-img" src="../img/camera.png" onclick="$(this).next('input').click();"> -->
									<input type="file" name="weight_units" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" class="reload_customer_images">
									<div class="uploaded_image gap-top" <?= !file_exists('download/'.$customer_approval['weight_units']) || empty($customer_approval['weight_units']) ? 'style="display:none;"' : '' ?>>
										<?= file_exists('download/'.$customer_approval['weight_units']) ? '<a href="download/'.$customer_approval['weight_units'].'" target="_blank"><img src="download/'.$customer_approval['weight_units'].'"  style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '' ?>
									</div>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Product Damage Package".',') !== FALSE && $field_sort_field == 'Customer Product Damage Package') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">My Product Is Damage Free (Packaging Is Free of Damage If Left In Box):</label>
							<div class="col-sm-8">
								<label class="form-checkbox"><input type="radio" name="product__<?= $customer_approval['id'] ?>" value="1" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" <?= $customer_approval['product'] == 1 ? 'checked' : '' ?> onchange="if(this.checked) { $(this).closest('.form-group').find('.notes').hide(); }">Yes</label>
								<label class="form-checkbox"><input type="radio" name="product__<?= $customer_approval['id'] ?>" value="2" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" <?= $customer_approval['product'] == 2 ? 'checked' : '' ?> onchange="if(this.checked) { $(this).closest('.form-group').find('.notes').show(); }">No</label>
							</div>
							<div class="col-sm-12 notes" <?= $customer_approval['product'] == 2 ? '' : 'style="display:none;"' ?>>
								<label class="col-sm-4 control-label">Please Provide Details:</label>
								<textarea name="notes" class="full-width noMceEditor" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id"><?= html_entity_decode($customer_approval['notes']) ?></textarea>
								<label class="col-sm-4 control-label">Upload a Full Image:</label>
								<div class="col-sm-8">
									<!-- <img class="inline-img" src="../img/camera.png" onclick="$(this).next('input').click();"> -->
									<input type="file" name="dimension_units" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" class="reload_customer_images">
									<div class="uploaded_image gap-top" <?= !file_exists('download/'.$customer_approval['dimension_units']) || empty($customer_approval['dimension_units']) ? 'style="display:none;"' : '' ?>>
										<?= file_exists('download/'.$customer_approval['dimension_units']) ? '<a href="download/'.$customer_approval['dimension_units'].'" target="_blank"><img src="download/'.$customer_approval['dimension_units'].'" style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '' ?>
									</div>
								</div>
								<label class="col-sm-4 control-label">Upload a Close Up Image of Damage:</label>
								<div class="col-sm-8">
									<input type="file" name="dimensions" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" class="reload_customer_images">
									<div class="uploaded_image gap-top" <?= !file_exists('download/'.$customer_approval['dimensions']) || empty($customer_approval['dimensions']) ? 'style="display:none;"' : '' ?>>
										<?= file_exists('download/'.$customer_approval['dimensions']) ? '<a href="download/'.$customer_approval['dimensions'].'" target="_blank"><img src="download/'.$customer_approval['dimensions'].'" style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '' ?>
									</div>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php } else if(strpos($value_config, ','."Customer Product Damage".',') !== FALSE && $field_sort_field == 'Customer Product Damage') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">My Product Is Damage Free:</label>
							<div class="col-sm-8">
								<label class="form-checkbox"><input type="radio" name="product__<?= $customer_approval['id'] ?>" value="1" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" <?= $customer_approval['product'] == 1 ? 'checked' : '' ?> onchange="if(this.checked) { $(this).closest('.form-group').find('.notes').hide(); }">Yes</label>
								<label class="form-checkbox"><input type="radio" name="product__<?= $customer_approval['id'] ?>" value="2" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" <?= $customer_approval['product'] == 2 ? 'checked' : '' ?> onchange="if(this.checked) { $(this).closest('.form-group').find('.notes').show(); }">No</label>
							</div>
							<div class="col-sm-12 notes" <?= $customer_approval['product'] == 2 ? '' : 'style="display:none;"' ?>>
								<label class="col-sm-4 control-label">Please Provide Details:</label>
								<textarea name="notes" class="full-width noMceEditor" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id"><?= html_entity_decode($customer_approval['notes']) ?></textarea>
								<label class="col-sm-4 control-label">Upload a Full Image:</label>
								<div class="col-sm-8">
									<!-- <img class="inline-img" src="../img/camera.png" onclick="$(this).next('input').click();"> -->
									<input type="file" name="dimension_units" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" class="reload_customer_images">
									<div class="uploaded_image gap-top" <?= !file_exists('download/'.$customer_approval['dimension_units']) || empty($customer_approval['dimension_units']) ? 'style="display:none;"' : '' ?>>
										<?= file_exists('download/'.$customer_approval['dimension_units']) ? '<a href="download/'.$customer_approval['dimension_units'].'" target="_blank"><img src="download/'.$customer_approval['dimension_units'].'" style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '' ?>
									</div>
								</div>
								<label class="col-sm-4 control-label">Upload a Close Up Image of Damage:</label>
								<div class="col-sm-8">
									<input type="file" name="dimensions" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" class="reload_customer_images">
									<div class="uploaded_image gap-top" <?= !file_exists('download/'.$customer_approval['dimensions']) || empty($customer_approval['dimensions']) ? 'style="display:none;"' : '' ?>>
										<?= file_exists('download/'.$customer_approval['dimensions']) ? '<a href="download/'.$customer_approval['dimensions'].'" target="_blank"><img src="download/'.$customer_approval['dimensions'].'" style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '' ?>
									</div>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Rate".',') !== FALSE && $field_sort_field == 'Customer Rate') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">How would you rate our team?</label>
							<div class="col-sm-8">
								<div class="star-ratings">
									<input type="radio" class="star-input" <?= $customer_approval['rate'] == 5 ? 'checked' : '' ?> name="rate" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_rate-5" value="5"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_rate-5"></label>
									<input type="radio" class="star-input" <?= $customer_approval['rate'] == 4 ? 'checked' : '' ?> name="rate" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_rate-4" value="4"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_rate-4"></label>
									<input type="radio" class="star-input" <?= $customer_approval['rate'] == 3 ? 'checked' : '' ?> name="rate" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_rate-3" value="3"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_rate-3"></label>
									<input type="radio" class="star-input" <?= $customer_approval['rate'] == 2 ? 'checked' : '' ?> name="rate" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_rate-2" value="2"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_rate-2"></label>
									<input type="radio" class="star-input" <?= $customer_approval['rate'] == 1 ? 'checked' : '' ?> name="rate" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_rate-1" value="1"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_rate-1"></label>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php } else if(strpos($value_config, ','."Customer Delivery Rate".',') !== FALSE && $field_sort_field == 'Customer Delivery Rate') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">How Would You Rate Our Delivery Team?</label>
							<div class="col-sm-8">
								<div class="star-ratings">
									<input type="radio" class="star-input" <?= $customer_approval['rate'] == 5 ? 'checked' : '' ?> name="rate" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_rate-5" value="5"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_rate-5"></label>
									<input type="radio" class="star-input" <?= $customer_approval['rate'] == 4 ? 'checked' : '' ?> name="rate" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_rate-4" value="4"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_rate-4"></label>
									<input type="radio" class="star-input" <?= $customer_approval['rate'] == 3 ? 'checked' : '' ?> name="rate" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_rate-3" value="3"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_rate-3"></label>
									<input type="radio" class="star-input" <?= $customer_approval['rate'] == 2 ? 'checked' : '' ?> name="rate" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_rate-2" value="2"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_rate-2"></label>
									<input type="radio" class="star-input" <?= $customer_approval['rate'] == 1 ? 'checked' : '' ?> name="rate" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_rate-1" value="1"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_rate-1"></label>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Recommend".',') !== FALSE && $field_sort_field == 'Customer Recommend') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Would you recommend us?</label>
							<div class="col-sm-8">
								<label class="form-checkbox"><input type="radio" name="contact_info__<?= $customer_approval['id'] ?>" value="1" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" <?= $customer_approval['contact_info'] == 1 ? 'checked' : '' ?>>Yes</label>
								<label class="form-checkbox"><input type="radio" name="contact_info__<?= $customer_approval['id'] ?>" value="2" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" <?= $customer_approval['contact_info'] == 2 ? 'checked' : '' ?>>No</label>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php } else if(strpos($value_config, ','."Customer Recommend Likely".',') !== FALSE && $field_sort_field == 'Customer Recommend Likely') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">How Likely Are You To Recommend Our Delivery Service?</label>
							<div class="col-sm-8">
								<div class="star-ratings">
									<input type="radio" class="star-input" <?= $customer_approval['contact_info'] == 5 ? 'checked' : '' ?> name="contact_info" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_contact_info-5" value="5"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_contact_info-5"></label>
									<input type="radio" class="star-input" <?= $customer_approval['contact_info'] == 4 ? 'checked' : '' ?> name="contact_info" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_contact_info-4" value="4"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_contact_info-4"></label>
									<input type="radio" class="star-input" <?= $customer_approval['contact_info'] == 3 ? 'checked' : '' ?> name="contact_info" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_contact_info-3" value="3"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_contact_info-3"></label>
									<input type="radio" class="star-input" <?= $customer_approval['contact_info'] == 2 ? 'checked' : '' ?> name="contact_info" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_contact_info-2" value="2"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_contact_info-2"></label>
									<input type="radio" class="star-input" <?= $customer_approval['contact_info'] == 1 ? 'checked' : '' ?> name="contact_info" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" id="<?=$customer_approval['id'] ?>_contact_info-1" value="1"><label class="star-rating-label" for="<?=$customer_approval['id'] ?>_contact_info-1"></label>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Add Details".',') !== FALSE && $field_sort_field == 'Customer Add Details') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Any Additional Comments:</label>
							<div class="col-sm-12">
								<textarea name="weight" class="full-width noMceEditor" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id"><?= html_entity_decode($customer_approval['weight']) ?></textarea>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Sign".',') !== FALSE && $field_sort_field == 'Customer Sign') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Signature:</label>
							<div class="col-sm-8">
								<?php $output_name = 'signature';
								$sign_output_options = 'data-table="ticket_attached" data-id="'.$customer_approval['id'].'" data-id-field="id"';
								include('../phpsign/sign_multiple.php'); ?>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Complete".',') !== FALSE && $field_sort_field == 'Customer Complete') { ?>
						<div class="form-group">
							<input type="hidden" name="completed" data-table="ticket_attached" data-id="<?= $customer_approval['id'] ?>" data-id-field="id" data-back-url="<?= $back_url ?>" <?= IFRAME_PAGE && strpos($_SERVER['SCRIPT_NAME'],'edit_ticket_tab') !== FALSE ? 'data-iframe="1"' : '' ?> <?= strpos($value_config, ','."Customer Complete Exits Ticket".',') !== FALSE ? 'data-exit-ticket="1"' : '' ?> class="no_time">
							<?php if(strpos($value_config,',Checkin Delivery,') !== FALSE) {
								$checkout_id = $dbc->query("SELECT `id` FROM `ticket_attached` WHERE `src_table`='Delivery' AND `line_id`='".$customer_approval['line_id']."' AND `deleted`=0")->fetch_assoc(); ?>
								<input type="hidden" name="completed" data-table="ticket_attached" data-id="<?= $checkout_id['id'] ?>" data-id-field="id" class="no_time">
							<?php } ?>
							<button class="btn brand-btn pull-right" data-history-label="Customer Completed" onclick="<?= strpos($value_config,',Finish Button Hide,') !== FALSE ? "$(this).hide();" : "$(this).attr('disabled',true).text('Completed!');" ?>$(this).closest('.customer_notes').find('[name=signature]').change();$(this).closest('.customer_notes').find('input,select,textarea').attr('readonly',true).click(function() { return false; });<?= strpos($value_config, ','."Customer Sign Off Complete Status".',') !== FALSE ? "completeStopStatus(this, '".$customer_approval['stop']."');" : '' ?> $(this).closest('.form-group').find('[name=completed]').val(1).change(); return false;">Complete</button>
						</div>
						<div class="clearfix"></div>
					<?php }
				} else {
					if(strpos($value_config, ','."Customer Slider".',') !== FALSE && $field_sort_field == 'Customer Slider' && !($customer_approval['completed'] > 0)) { ?>
						<button class="btn brand-btn pull-right" onclick="overlayIFrameSlider('../Ticket/edit_ticket_tab.php?tab=ticket_customer_notes&tile_name=<?= $_GET['tile_name'] ?>&ticketid=<?= $ticketid ?>&stop=<?= $customer_approval['stop'] ?>', '95%', true, true); return false;">Get Customer Feedback</button>
						<div class="clearfix"></div>
					<?php }
					if(strpos($value_config, ','."Customer Contacted".',') !== FALSE && $field_sort_field == 'Customer Contacted') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Customer Contacted:</label>
							<div class="col-sm-8">
								<?= $customer_approval['checked_in'] ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['Customer Contacted', $customer_approval['checked_in']]; ?>
					<?php }
					if(strpos($value_config, ','."Customer Reset Details".',') !== FALSE && $field_sort_field == 'Customer Reset Details') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Who Reset and Why:</label>
							<div class="col-sm-8">
								<?= html_entity_decode($customer_approval['location_from']) ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['Who Reset and Why', html_entity_decode($customer_approval['location_from'])]; ?>
					<?php } else if(strpos($value_config, ','."Customer Driver Notes".',') !== FALSE && $field_sort_field == 'Customer Driver Notes') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Driver Notes:</label>
							<div class="col-sm-8">
								<?= html_entity_decode($customer_approval['location_from']) ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['Driver Notes', html_entity_decode($customer_approval['location_from'])]; ?>
					<?php }
					if(strpos($value_config, ','."Customer Property Photo".',') !== FALSE && $field_sort_field == 'Customer Property Photo') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Photo of Property:</label>
							<div class="col-sm-8">
								<?= ($customer_approval['location_to'] != '' && file_exists('download/'.$customer_approval['location_to']) ? '<a href="download/'.$customer_approval['location_to'].'" target="_blank"><img src="download/'.$customer_approval['location_to'].'"  style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '') ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['Photo of Property', ($customer_approval['location_to'] != '' && file_exists('download/'.$customer_approval['location_to']) ? '<img src="download/'.$customer_approval['location_to'].'">' : '')]; ?>
					<?php }
					if(strpos($value_config, ','."Customer Property Damage".',') !== FALSE && $field_sort_field == 'Customer Property Damage') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">My Property Is Damage Free:</label>
							<div class="col-sm-8">
								<?= $customer_approval['status'] == 1 ? 'Yes' : 'No:<br />'.html_entity_decode($customer_approval['description']).($customer_approval['weight_units'] != '' && file_exists('download/'.$customer_approval['weight_units']) ? '<a href="download/'.$customer_approval['weight_units'].'" target="_blank"><img src="download/'.$customer_approval['weight_units'].'"  style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '') ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['My Property Is Damage Free', $customer_approval['status'] == 1 ? 'Yes' : 'No:<br />'.html_entity_decode($customer_approval['description']).($customer_approval['weight_units'] != '' && file_exists('download/'.$customer_approval['weight_units']) ? '<img src="download/'.$customer_approval['weight_units'].'">' : '')]; ?>
					<?php }
					if(strpos($value_config, ','."Customer Product Damage Package".',') !== FALSE && $field_sort_field == 'Customer Product Damage Package') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">My Product Is Damage Free (Packaging Is Free of Damage If Left In Box):</label>
							<div class="col-sm-8">
								<?= $customer_approval['product'] == 1 ? 'Yes' : 'No:<br />'.html_entity_decode($customer_approval['notes']).($customer_approval['dimension_units'] != '' && file_exists('download/'.$customer_approval['dimension_units']) ? '<a href="download/'.$customer_approval['dimension_units'].'" target="_blank"><img src="download/'.$customer_approval['dimension_units'].'" style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '').($customer_approval['dimensions'] != '' && file_exists('download/'.$customer_approval['dimensions']) ? '<a href="download/'.$customer_approval['dimensions'].'" target="_blank"><img src="download/'.$customer_approval['dimensions'].'" style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '') ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['My Product Is Damage Free', $customer_approval['product'] == 1 ? 'Yes' : 'No:<br />'.html_entity_decode($customer_approval['notes']).($customer_approval['dimension_units'] != '' && file_exists('download/'.$customer_approval['dimension_units']) ? '<img src="download/'.$customer_approval['dimension_units'].'">' : '').($customer_approval['dimensions'] != '' && file_exists('download/'.$customer_approval['dimensions']) ? '<img src="download/'.$customer_approval['dimensions'].'">' : '')]; ?>
					<?php }
					if(strpos($value_config, ','."Customer Product Damage".',') !== FALSE && $field_sort_field == 'Customer Product Damage') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">My Product Is Damage Free:</label>
							<div class="col-sm-8">
								<?= $customer_approval['product'] == 1 ? 'Yes' : 'No:<br />'.html_entity_decode($customer_approval['notes']).($customer_approval['dimension_units'] != '' && file_exists('download/'.$customer_approval['dimension_units']) ? '<a href="download/'.$customer_approval['dimension_units'].'" target="_blank"><img src="download/'.$customer_approval['dimension_units'].'" style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '').($customer_approval['dimensions'] != '' && file_exists('download/'.$customer_approval['dimensions']) ? '<a href="download/'.$customer_approval['dimensions'].'" target="_blank"><img src="download/'.$customer_approval['dimensions'].'" style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>' : '') ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['My Product Is Damage Free', $customer_approval['product'] == 1 ? 'Yes' : 'No:<br />'.html_entity_decode($customer_approval['notes']).($customer_approval['dimension_units'] != '' && file_exists('download/'.$customer_approval['dimension_units']) ? '<img src="download/'.$customer_approval['dimension_units'].'">' : '').($customer_approval['dimensions'] != '' && file_exists('download/'.$customer_approval['dimensions']) ? '<img src="download/'.$customer_approval['dimensions'].'">' : '')]; ?>
					<?php }
					if(strpos($value_config, ','."Customer Rate".',') !== FALSE && $field_sort_field == 'Customer Rate') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">How would you rate our team?</label>
							<div class="col-sm-8">
								<?= number_format($customer_approval['rate'],0) ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['How would you rate our team?', number_format($customer_approval['rate'],0)]; ?>
					<?php } else if(strpos($value_config, ','."Customer Delivery Rate".',') !== FALSE && $field_sort_field == 'Customer Delivery Rate') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">How Would You Rate Our Delivery Team?</label>
							<div class="col-sm-8">
								<?= number_format($customer_approval['rate'],0) ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['How would you rate our team?', number_format($customer_approval['rate'],0)]; ?>
					<?php }
					if(strpos($value_config, ','."Customer Recommend".',') !== FALSE && $field_sort_field == 'Customer Recommend') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Would you recommend us?</label>
							<div class="col-sm-8">
								<?= $customer_approval['contact_info'] == 1 ? 'Yes' : 'No' ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['Would you recommend us?', $customer_approval['contact_info'] == 1 ? 'Yes' : 'No']; ?>
					<?php } else if(strpos($value_config, ','."Customer Recommend Likely".',') !== FALSE && $field_sort_field == 'Customer Recommend Likely') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">How Likely Are You To Recommend Our Delivery Service?</label>
							<div class="col-sm-8">
								<?= number_format($customer_approval['contact_info'],0) ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['Would you recommend us?', number_format($customer_approval['contact_info'],0)]; ?>
					<?php }
					if(strpos($value_config, ','."Customer Add Details".',') !== FALSE && $field_sort_field == 'Customer Add Details' && !empty(strip_tags(html_entity_decode($customer_approval['weight'])))) { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Additional Comments:</label>
							<div class="col-sm-8">
								<?= html_entity_decode($customer_approval['weight']) ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['Additional Comments', html_entity_decode($customer_approval['weight'])]; ?>
					<?php }
					if(strpos($value_config, ','."Customer Sign".',') !== FALSE && $field_sort_field == 'Customer Sign') { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Signature:</label>
							<div class="col-sm-8">
								<?php if($customer_approval['signature'] != '') {
									if(!file_exists('export/customer_sign_'.$customer_approval['id'].'.png')) {
										if(!file_exists('export')) {
											mkdir('export',0777,true);
										}
										include_once('../phpsign/signature-to-image.php');
										$signature = sigJsonToImage(html_entity_decode($customer_approval['signature']));
										imagepng($signature, 'export/customer_sign_'.$customer_approval['id'].'.png');
									} ?>
									<img src="export/customer_sign_<?= $customer_approval['id'] ?>.png">
									<?php $pdf_contents[] = ['Signature', '<img src="export/customer_sign_'.$customer_approval['id'].'.png">', 'img'];
								} ?>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php }
				} ?>
		<?php }
		echo '</div>';
	}
} ?>