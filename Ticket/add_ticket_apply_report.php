<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Chemicals</h3>') ?>
<?php $apply_reports = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 AND `src_table`='apply_report'");
$apply_report = $apply_reports->fetch_assoc();
do { ?>
	<?php if($access_all > 0) { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Equipment,') !== FALSE && $field_sort_field == 'Equipment') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Equipment Description:</label>
						<div class="col-sm-8">
							<input type="text" name="position" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['position'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Wind Speed,') !== FALSE && $field_sort_field == 'Wind Speed') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Wind Speed:</label>
						<div class="col-sm-8">
							<input type="text" name="weight" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['weight'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Temperature,') !== FALSE && $field_sort_field == 'Temperature') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Temperature:</label>
						<div class="col-sm-8">
							<input type="text" name="dimensions" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['dimensions'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Humidity,') !== FALSE && $field_sort_field == 'Humidity') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Humidity:</label>
						<div class="col-sm-8">
							<input type="text" name="description" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['description'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Wind Direction,') !== FALSE && $field_sort_field == 'Wind Direction') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Wind Direction:</label>
						<div class="col-sm-8">
							<input type="text" name="weight_units" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['weight_units'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Soil,') !== FALSE && $field_sort_field == 'Soil') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Soil:</label>
						<div class="col-sm-8">
							<input type="text" name="notes" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['notes'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
			<?php }
			if($access_all > 0) { ?>
				<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" value="0">
				<div class="clearfix"></div>
			<?php } ?>
		</div>
	<?php } else { ?>
		<?php foreach($field_sort_order as $field_sort_field) { ?>
			<?php if(strpos($value_config,',Equipment,') !== FALSE && $field_sort_field == 'Equipment') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Equipment Description:</label>
					<div class="col-sm-8">
						<?= $apply_report['position'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Equipment Description', $apply_report['position']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Wind Speed,') !== FALSE && $field_sort_field == 'Wind Speed') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Wind Speed:</label>
					<div class="col-sm-8">
						<?= $apply_report['weight'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Wind Speed', $apply_report['weight']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Temperature,') !== FALSE && $field_sort_field == 'Temperature') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Temperature:</label>
					<div class="col-sm-8">
						<?= $apply_report['dimensions'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Temperature', $apply_report['dimensions']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Humidity,') !== FALSE && $field_sort_field == 'Humidity') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Humidity:</label>
					<div class="col-sm-8">
						<?= $apply_report['description'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Humidity', $apply_report['description']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Wind Direction,') !== FALSE && $field_sort_field == 'Wind Direction') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Wind Direction:</label>
					<div class="col-sm-8">
						<?= $apply_report['weight_units'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Wind Direction', $apply_report['weight_units']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Soil,') !== FALSE && $field_sort_field == 'Soil') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Soil:</label>
					<div class="col-sm-8">
						<?= $apply_report['notes'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Soil', $apply_report['notes']]; ?>
			<?php } ?>
		<?php } ?>
	<?php }
} while($apply_report = $apply_reports->fetch_assoc()); ?>