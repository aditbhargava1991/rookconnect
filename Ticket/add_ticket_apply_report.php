<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Application Report</h3>') ?>
<?php $apply_reports = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 AND `src_table`='apply_report'");
$apply_report = $apply_reports->fetch_assoc();
do { ?>
	<?php if($access_all > 0) { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Application Equipment,') !== FALSE && $field_sort_field == 'Application Equipment') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Equipment Description:
                            <span class="incognito" style="display:none;"><img class="cursor-hand inline-img no-toggle no-colour <?= !in_array($field_sort_field,$incognito_fields) ? 'black-color' : 'red-color' ?>" src="../img/icons/ROOK-incognito-icon.png" title="Hide this Field on <?= POS_ADVANCE_TILE ?>" data-field="<?= $field_sort_field ?>" onclick="toggleIncognito(this);"><input type="hidden" name="incognito_fields" data-concat="," data-table="tickets" data-id-field="ticketid" data-id="<?= $ticketid ?>" value="<?= !in_array($field_sort_field,$incognito_fields) ? '' : $field_sort_field ?>"></span></label>
						<div class="col-sm-8">
							<input type="text" name="position" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['position'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Application Wind Speed,') !== FALSE && $field_sort_field == 'Application Wind Speed') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Wind Speed:
                            <span class="incognito" style="display:none;"><img class="cursor-hand inline-img no-toggle no-colour <?= !in_array($field_sort_field,$incognito_fields) ? 'black-color' : 'red-color' ?>" src="../img/icons/ROOK-incognito-icon.png" title="Hide this Field on <?= POS_ADVANCE_TILE ?>" data-field="<?= $field_sort_field ?>" onclick="toggleIncognito(this);"><input type="hidden" name="incognito_fields" data-concat="," data-table="tickets" data-id-field="ticketid" data-id="<?= $ticketid ?>" value="<?= !in_array($field_sort_field,$incognito_fields) ? '' : $field_sort_field ?>"></span></label>
						<div class="col-sm-8">
							<input type="text" name="weight" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['weight'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Application Temperature,') !== FALSE && $field_sort_field == 'Application Temperature') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Temperature:
                            <span class="incognito" style="display:none;"><img class="cursor-hand inline-img no-toggle no-colour <?= !in_array($field_sort_field,$incognito_fields) ? 'black-color' : 'red-color' ?>" src="../img/icons/ROOK-incognito-icon.png" title="Hide this Field on <?= POS_ADVANCE_TILE ?>" data-field="<?= $field_sort_field ?>" onclick="toggleIncognito(this);"><input type="hidden" name="incognito_fields" data-concat="," data-table="tickets" data-id-field="ticketid" data-id="<?= $ticketid ?>" value="<?= !in_array($field_sort_field,$incognito_fields) ? '' : $field_sort_field ?>"></span></label>
						<div class="col-sm-8">
							<input type="text" name="dimensions" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['dimensions'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Application Humidity,') !== FALSE && $field_sort_field == 'Application Humidity') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Humidity:
                            <span class="incognito" style="display:none;"><img class="cursor-hand inline-img no-toggle no-colour <?= !in_array($field_sort_field,$incognito_fields) ? 'black-color' : 'red-color' ?>" src="../img/icons/ROOK-incognito-icon.png" title="Hide this Field on <?= POS_ADVANCE_TILE ?>" data-field="<?= $field_sort_field ?>" onclick="toggleIncognito(this);"><input type="hidden" name="incognito_fields" data-concat="," data-table="tickets" data-id-field="ticketid" data-id="<?= $ticketid ?>" value="<?= !in_array($field_sort_field,$incognito_fields) ? '' : $field_sort_field ?>"></span></label>
						<div class="col-sm-8">
							<input type="text" name="description" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['description'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Application Wind Direction,') !== FALSE && $field_sort_field == 'Application Wind Direction') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Wind Direction:
                            <span class="incognito" style="display:none;"><img class="cursor-hand inline-img no-toggle no-colour <?= !in_array($field_sort_field,$incognito_fields) ? 'black-color' : 'red-color' ?>" src="../img/icons/ROOK-incognito-icon.png" title="Hide this Field on <?= POS_ADVANCE_TILE ?>" data-field="<?= $field_sort_field ?>" onclick="toggleIncognito(this);"><input type="hidden" name="incognito_fields" data-concat="," data-table="tickets" data-id-field="ticketid" data-id="<?= $ticketid ?>" value="<?= !in_array($field_sort_field,$incognito_fields) ? '' : $field_sort_field ?>"></span></label>
						<div class="col-sm-8">
							<input type="text" name="weight_units" data-table="ticket_attached" data-id="<?= $apply_report['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $apply_report['weight_units'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Application Soil,') !== FALSE && $field_sort_field == 'Application Soil') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Soil:
                            <span class="incognito" style="display:none;"><img class="cursor-hand inline-img no-toggle no-colour <?= !in_array($field_sort_field,$incognito_fields) ? 'black-color' : 'red-color' ?>" src="../img/icons/ROOK-incognito-icon.png" title="Hide this Field on <?= POS_ADVANCE_TILE ?>" data-field="<?= $field_sort_field ?>" onclick="toggleIncognito(this);"><input type="hidden" name="incognito_fields" data-concat="," data-table="tickets" data-id-field="ticketid" data-id="<?= $ticketid ?>" value="<?= !in_array($field_sort_field,$incognito_fields) ? '' : $field_sort_field ?>"></span></label>
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
			<?php if(strpos($value_config,',Application Equipment,') !== FALSE && $field_sort_field == 'Application Equipment') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Equipment Description:</label>
					<div class="col-sm-8">
						<?= $apply_report['position'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Equipment Description', $apply_report['position']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Application Wind Speed,') !== FALSE && $field_sort_field == 'Application Wind Speed') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Wind Speed:</label>
					<div class="col-sm-8">
						<?= $apply_report['weight'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Wind Speed', $apply_report['weight']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Application Temperature,') !== FALSE && $field_sort_field == 'Application Temperature') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Temperature:</label>
					<div class="col-sm-8">
						<?= $apply_report['dimensions'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Temperature', $apply_report['dimensions']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Application Humidity,') !== FALSE && $field_sort_field == 'Application Humidity') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Humidity:</label>
					<div class="col-sm-8">
						<?= $apply_report['description'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Humidity', $apply_report['description']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Application Wind Direction,') !== FALSE && $field_sort_field == 'Application Wind Direction') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Wind Direction:</label>
					<div class="col-sm-8">
						<?= $apply_report['weight_units'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Wind Direction', $apply_report['weight_units']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Application Soil,') !== FALSE && $field_sort_field == 'Application Soil') { ?>
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