<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Location Details</h3>') ?>
<?php $location_details_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`location_from`, `ticket_attached`.`notes`, `ticket_attached`.`location_to`, `ticket_attached`.`description`, `ticket_attached`.`volume` FROM `ticket_attached` WHERE `ticket_attached`.`description` != '' AND `ticket_attached`.`src_table`='location_details' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
$location_details = mysqli_fetch_assoc($location_details_list);
do {
	if($access_all > 0) { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Location Details From,') !== FALSE && $field_sort_field == 'Location Details From') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Location From:</label>
						<div class="col-sm-8">
							<input type="text" name="location_from" data-table="ticket_attached" data-id="<?= $location_details['id'] ?>" data-id-field="id" data-type="location_details" data-type-field="src_table" class="form-control" value="<?= $location_details['location_from'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Location Details From Notes,') !== FALSE && $field_sort_field == 'Location Details From Notes') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Location From Notes:</label>
						<div class="col-sm-8">
							<textarea name="notes" data-table="ticket_attached" data-id="<?= $location_details['id'] ?>" data-id-field="id" data-type="location_details" data-type-field="src_table" class="form-control"><?= html_entity_decode($location_details['notes']) ?></textarea>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Location Details To,') !== FALSE && $field_sort_field == 'Location Details To') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Location To:</label>
						<div class="col-sm-8">
							<input type="text" name="location_to" data-table="ticket_attached" data-id="<?= $location_details['id'] ?>" data-id-field="id" data-type="location_details" data-type-field="src_table" class="form-control" value="<?= $location_details['location_to'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Location Details To Notes,') !== FALSE && $field_sort_field == 'Location Details To Notes') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Location To Notes:</label>
						<div class="col-sm-8">
							<textarea name="description" data-table="ticket_attached" data-id="<?= $location_details['id'] ?>" data-id-field="id" data-type="location_details" data-type-field="src_table" class="form-control"><?= html_entity_decode($location_details['description']) ?></textarea>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Location Details Volume,') !== FALSE && $field_sort_field == 'Location Details Volume') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Volume:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="volume" data-table="ticket_attached" data-id="<?= $location_details['id'] ?>" data-id-field="id" data-type="location_details" data-type-field="src_table" class="form-control" value="<?= $location_details['volume'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
			<?php } ?>
			<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $location_details['id'] ?>" data-id-field="id" data-type="location_details" data-type-field="src_table" value="0">
			<img class="inline-img pull-right" data-history-label="Location Details" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
			<img class="inline-img pull-right" data-history-label="Location Details" onclick="remMulti(this);" src="../img/remove.png">
			<div class="clearfix"></div>
		</div>
	<?php } else if($location_details['location_from'] != '') { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Location Details From,') !== FALSE && $field_sort_field == 'Location Details From') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Location From:</label>
						<div class="col-sm-8">
							<?= $location_details['location_from'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Location From', $location_details['location_from']] ?>
				<?php } ?>
				<?php if(strpos($value_config,',Location Details From Notes,') !== FALSE && $field_sort_field == 'Location Details From Notes') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Location From Notes:</label>
						<div class="col-sm-8">
							<?= html_entity_decode($location_details['notes']) ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Location From Notes', html_entity_decode($location_details['notes'])] ?>
				<?php } ?>
				<?php if(strpos($value_config,',Location Details To,') !== FALSE && $field_sort_field == 'Location Details To') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Location To:</label>
						<div class="col-sm-8">
							<?= $location_details['location_to'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Location To', $location_details['location_to']] ?>
				<?php } ?>
				<?php if(strpos($value_config,',Location Details To Notes,') !== FALSE && $field_sort_field == 'Location Details To Notes') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Location To Notes:</label>
						<div class="col-sm-8">
							<?= html_entity_decode($location_details['description']) ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Location To Notes', html_entity_decode($location_details['description'])] ?>
				<?php } ?>
				<?php if(strpos($value_config,',Location Details Volume,') !== FALSE && $field_sort_field == 'Location Details Volume') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Volume:</label>
						<div class="col-sm-8">
							<?= $location_details['volume'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Volume', $location_details['volume']] ?>
				<?php } ?>
			<?php } ?>
		</div>
	<?php }
} while($location_details = mysqli_fetch_assoc($location_details_list)); ?>