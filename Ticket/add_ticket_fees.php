<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Fees</h3>') : '' ?>
<?php foreach($field_sort_order as $field_sort_field) {
	if($field_sort_field == 'FFMCUSTOM Fees' || (!$custom_accordion && $field_sort_field == 'Fees')) { ?>
		<div class="hide-titles-mob">
			<label class="col-sm-4 text-center">Fee Name</label>
			<label class="col-sm-4 text-center">Details</label>
			<label class="col-sm-3 text-center">Amount</label>
		</div>
		<?php foreach(explode(',',$get_ticket['fee_name']) as $i => $fee_name) { ?>
			<div class="multi-block">
				<?php if($access_all > 0) { ?>
					<div class="col-sm-4">
						<label class="control-label show-on-mob">Fee Name:</label>
						<input type="text" name="fee_name" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," value="<?= $fee_name ?>" class="form-control">
					</div>
					<div class="col-sm-4">
						<label class="control-label show-on-mob">Fee Details:</label>
						<input type="text" name="fee_details" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," value="<?= explode(',',$get_ticket['fee_details'])[$i] ?>" class="form-control">
					</div>
					<div class="col-sm-3">
						<label class="control-label show-on-mob">Fee Amount:</label>
						<input type="number" min=0 step="0.01" name="fee_amt" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," value="<?= explode(',',$get_ticket['fee_amt'])[$i] ?>" class="form-control">
					</div>
					<div class="col-sm-1">
						<img class="inline-img pull-right" data-history-label="Fee" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
						<img class="inline-img pull-right" data-history-label="Fee" onclick="remMulti(this);" src="../img/remove.png">
					</div>
					<div class="clearfix"></div>
				<?php } else { ?>
					<div class="col-sm-4">
						<label class="control-label show-on-mob">Fee Name:</label>
						<?= $fee_name ?>
					</div>
					<?php $pdf_contents[] = ['Fee Name', $fee_name]; ?>
					<div class="col-sm-4">
						<label class="control-label show-on-mob">Fee Details:</label>
						<?= explode(',',$get_ticket['fee_details'])[$i] ?>
					</div>
					<?php $pdf_contents[] = ['Fee Details', explode(',',$get_ticket['fee_details'])[$i]]; ?>
					<div class="col-sm-3">
						<label class="control-label show-on-mob">Fee Amount:</label>
						<?= explode(',',$get_ticket['fee_amt'])[$i] ?>
					</div>
					<?php $pdf_contents[] = ['Fee Amount', explode(',',$get_ticket['fee_amt'])[$i]]; ?>
					<div class="clearfix"></div>
				<?php } ?>
			</div>
		<?php } ?>
	<?php }
} ?>