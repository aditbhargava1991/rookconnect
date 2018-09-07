<!-- history -->
<div class="accordion-block-details padded" id="history">
    <div class="accordion-block-details-heading"><h4>History</h4></div>
    <div class="row">
		<?php $changes = mysqli_query($dbc, "SELECT * FROM `sales_order_history` WHERE `sales_order_id`='$sotid'");
		if(mysqli_num_rows($changes) > 0) {
            $odd_even = 0; ?>
			<div id="no-more-tables" class="col-sm-12">
				<table class="table table-bordered">
					<tr class="hidden-sm hidden-xs">
						<th>Changed</th>
						<th>History</th>
					</tr>
					<?php while($history = mysqli_fetch_assoc($changes)) { ?>
                        <?php $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
						<tr class="<?= $bg_class ?>">
							<td data-title="Changed"><?= get_contact($dbc, $history['contactid']) ?><br /><?= $history['date'] ?></td>
							<td data-title="History"><?= html_entity_decode($history['history']) ?></td>
						</tr>
                        <?php $odd_even++; ?>
					<?php } ?>
				</table>
			</div>
			<div class="clearfix"></div>
		<?php } ?>
    </div>
</div>