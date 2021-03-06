<script>
$(document).ready(function() {
	//Position
    $('.staff_pos .delete').last().hide();
    $('#add_row_pos').on( 'click', function () {
		$('.staff_pos .delete').show();
		destroyInputs($('#collapse_staffpos'));
		
		var clone = $('.staff_pos').last().clone();
		clone.find('select,input').val('');
		$('.staff_pos').last().after(clone);

		initInputs('#collapse_staffpos');
		$('.staff_pos .delete').show().last().hide();
        return false;
    });
});
function remRow(btn) {
	$(btn).closest('.staff_pos').remove();
	$('.staff_pos .delete').show().last().hide();
}
function setPosSavings() {
	$('#collapse_staffpos .form-group').find('[name="staff_pos_rate[]"]').each(function() {
		var current = this.value;
		var company = $(this).closest('.form-group').find('[name="primary_rate[]"]').val();
		$(this).closest('.form-group').find('[name="savings_dollar[]"]').val(round2Fixed(company - current));
		$(this).closest('.form-group').find('[name="savings_percent[]"]').val(round2Fixed(company > 0 ? (company - current) / company * 100 : 0));
	});
}
</script>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <label class="col-sm-3 text-center">Position</label>
            <label class="col-sm-1 text-center">UoM</label>
            <label class="col-sm-1 text-center">Rate Card Price</label>
			<?php if (strpos($base_field_config, ','."savings".',') !== FALSE) { ?>
				<label class="col-sm-1 text-center">Company Rate</label>
				<label class="col-sm-1 text-center">$ Savings</label>
				<label class="col-sm-1 text-center">% Savings</label>
			<?php } ?>
        </div>

		<?php $positions = explode('**',$staff_position);
		$position_list = $dbc->query("SELECT `positions`.`position_id`,`positions`.`name`,MAX(`company_rate_card`.`cust_price`) `rate` FROM `positions` LEFT JOIN `company_rate_card` ON (`positions`.`position_id`=`company_rate_card`.`item_id` OR (IFNULL(`company_rate_card`.`item_id`,0)=0 AND `positions`.`name`=`company_rate_card`.`description`)) AND `company_rate_card`.`deleted`=0 AND `company_rate_card`.`tile_name`='Position' AND `rate_card_name`='$ref_card' WHERE `positions`.`deleted`=0 GROUP BY `positions`.`position_id` ORDER BY `positions`.`name`")->fetch_all(MYSQLI_ASSOC);
		if($ref_card != '') {
			$positions = array_filter($positions);
			$position_id_list = [];
			foreach($positions as $position) {
				$position = explode('#',$position);
				if($position[0] > 0) {
					$position_id_list[] = $position[0];
				}
			}
			$ref_pos_list = $dbc->query("SELECT * FROM `company_rate_card` WHERE `deleted`=0 AND `rate_card_name`='$ref_card' AND `tile_name`='Services' AND `item_id` NOT IN ('".implode("','",$position_id_list)."')");
			while($ref_pos = $ref_pos_list->fetch_assoc()) {
				$positions[] = $ref_pos['item_id'];
			}
			$positions[] = 0;
		}
		foreach($positions as $position) {
			$position = explode('#',$position);
			$ratecardprice = $position[2] > 0 ? $position[2] : $position[1];
			$company_rate = 0; ?>
			<div class="form-group clearfix staff_pos">
				<div class="col-sm-3"><label class="show-on-mob">Position:</label>
					<select data-placeholder="Select Position" class="chosen-select-deselect" name="staff_pos[]"><option />
						<?php foreach($position_list as $position_row) { ?>
							<option <?= $position[0] == $position_row['position_id'] ? 'selected' : '' ?> data-rate="<?= $position_row['rate'] ?>" value="<?= $position_row['position_id'] ?>"><?= $position_row['name'] ?></option>
							<?php if($position[0] == $position_row['position_id']) {
								$company_rate = $position_row['rate'];
							}
						} ?>
					</select>
				</div>
				<div class="col-sm-1"><label class="show-on-mob">Unit of Measure:</label>
					<select data-placeholder="Select Position" class="chosen-select-deselect" name="staff_pos_unit[]"><option />
						<option <?= $position[2] > 0 ? '' : 'selected' ?> value="Hourly">Hourly</option>
						<option <?= $position[2] > 0 ? 'selected' : '' ?> value="Daily">Daily</option>
					</select>
				</div>
				<div class="col-sm-1"><label class="show-on-mob">Rate:</label>
					<input type="number" min=0 class="form-control" name="staff_pos_rate[]" onchange="setPosSavings();" value="<?= $ratecardprice ?>">
				</div>

                <?php if (strpos($base_field_config, ','."savings".',') !== FALSE) { ?>
                    <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Company Rate</label>
                        <input name="primary_rate[]" value="<?= $company_rate ?>" disabled type="text" class="form-control" />
                    </div>
                    <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">$ Savings</label>
                        <input name="savings_dollar[]" value="<?= number_format($company_rate - $ratecardprice,2) ?>" disabled type="text" class="form-control" />
                    </div>
                    <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">% Savings</label>
                        <input name="savings_percent[]" value="<?= number_format(($company_rate - $ratecardprice) / $company_rate * 100,2) ?>" disabled type="text" class="form-control" />
                    </div>
                <?php } ?>
				<div class="col-sm-1"><button class="delete btn brand-btn" onclick="remRow(this); return false;">Delete</button></div>
			</div>
		<?php } ?>
        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_pos" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>