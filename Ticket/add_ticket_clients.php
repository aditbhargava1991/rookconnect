<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Clients</h3>') ?>
<?php $client_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN ('$client_accordion_category') AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
$query = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `src_table`='$client_accordion_category' AND `deleted`=0 AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `tile_name`='".FOLDER_NAME."'".$query_daily);
$client = mysqli_fetch_assoc($query);
do { ?>
	<div class="multi-block">
		<?php if($access_contacts === TRUE) { ?>
			<div class="form-group">
				<label class="col-sm-<?= strpos($value_config,',Contact Set Hours,') === FALSE ? '4' : '2' ?> control-label"><?= $client_accordion_category ?>:</label>
				<div class="col-sm-<?= strpos($value_config,',Contact Set Hours,') === FALSE ? '7' : '5' ?>">
					<select name="item_id" data-table="ticket_attached" data-id="<?= $client['id'] ?>" data-placeholder="Select <?= $client_accordion_category ?>" data-id-field="id" data-type="Clients" data-type-field="src_table" class="chosen-select-deselect"><option></option>
						<?php foreach($client_list as $client_id) { ?>
							<option <?= $client_id == $client['item_id'] ? 'selected' : '' ?> value="<?= $client_id ?>"><?= get_contact($dbc, $client_id) ?></option>
						<?php } ?>
					</select>
				</div>
					<?php if(strpos($value_config,',Contact Set Hours,') !== FALSE) { // && $field_sort_field == 'Contact Set Hours') { ?>
						<label class="col-sm-2 control-label">Billable Hours:</label>
						<div class="col-sm-2">
							<input type="text" name="hours_set" data-table="ticket_attached" data-id="<?= $client['id'] ?>" data-id-field="id" data-type="Members" data-type-field="src_table" class="form-control" value="<?= $client['hours_set'] ?>">
						</div>
					<?php } ?>
				<div class="col-sm-1">
					<img class="inline-img pull-left black-color counterclockwise small" onclick="showClient(this);" src="../img/icons/dropdown-arrow.png">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $client['id'] ?>" data-id-field="id" data-type="Clients" data-type-field="src_table" value="0">
					<img class="inline-img pull-right" data-history-label="<?= $client_accordion_category ?>" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
					<img class="inline-img pull-right" data-history-label="<?= $client_accordion_category ?>" onclick="remMulti(this);" src="../img/remove.png">
				</div>
				<div class="clearfix"></div>
			</div>
		<?php } else if($client['item_id'] > 0) { ?>
				<label class="col-sm-<?= strpos($value_config,',Contact Set Hours,') === FALSE ? '4' : '2' ?> control-label"><?= $client_accordion_category ?>:</label>
				<div class="col-sm-<?= strpos($value_config,',Contact Set Hours,') === FALSE ? '7' : '5' ?>">
					<?= get_contact($dbc, $client['item_id']) ?>
				</div>
				<?php foreach ($field_sort_order as $field_sort_field) { ?>
					<?php if(strpos($value_config,',Contact Set Hours,') !== FALSE && $field_sort_field == 'Contact Set Hours') { ?>
						<label class="col-sm-2 control-label">Billable Hours:</label>
						<div class="col-sm-2">
							<?= $client['hours_set'] ?>
						</div>
					<?php } ?>
				<?php } ?>
				<div class="col-sm-1">
					<img class="inline-img pull-left black-color counterclockwise small" onclick="showClient(this);" src="../img/icons/dropdown-arrow.png">
				</div>
				<div class="clearfix"></div>
			</div>
			<?php $pdf_contents[] = [$client_accordion_category, get_contact($dbc, $client['item_id']).(strpos($value_config,',Contact Set Hours,') !== FALSE ? '<br>Billable Hours: '.$client['hours_set'] : '')]; ?>
		<?php } ?>
		<div class="iframe_div" style="display:none">
			<span>Loading...</span>
			<iframe name="client_iframe" style="height: 0; width: 100%;" src=""></iframe>
		</div>
	</div>
<?php } while($client = mysqli_fetch_assoc($query)); ?>
