<?php include_once('../include.php');
if(empty($field_option)) {
	$field_option = $_GET['field_option'];
}

$contactid = $_GET['edit'];
$rate_card = mysqli_query($dbc, "SELECT * FROM `rate_card` WHERE `clientid` ='$contactid' AND `deleted` = 0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc();
$service_fields = explode(',',get_field_config($dbc, 'services'));

$total_hours = 0;
$total_value = 0;

$load_template = $_GET['load_template'];
$template_items = [];
$loaded_templates = mysqli_fetch_array(mysqli_query($dbc, "SELECT `service_templates` FROM `contacts` WHERE `contactid` = '$contactid'"))['service_templates'];
foreach(explode(',',$loaded_templates) as $loaded_template) {
	if($loaded_template > 0) {
		$loaded_template = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services_service_templates` WHERE `templateid` = '$loaded_template'"));
		if(!empty($loaded_template)) {
			if($loaded_template['templateid'] == $load_template) {
				$template_items = explode(',', $loaded_template['serviceid']);
			} ?>
			<label class="form-checkbox"><input type="checkbox" name="select_service_template" data-templateid="<?= $loaded_template['templateid'] ?>" onchange="select_template_items(this);" <?= $load_template == $loaded_template['templateid'] ? 'checked' : '' ?>> <?= $loaded_template['name'] ?></label>
		<?php }
	}
} ?>

<?php if($field_option == 'Customer Rate Card Totalled Group Cat Type') {
	$rate_services = [];
	foreach(explode('**',$rate_card['services']) as $service_i => $service) {
		$service_line = explode('#',$service);
		if($service_line[0] > 0) {
			$service = $dbc->query("SELECT * FROM `services` WHERE `serviceid`='{$service_line[0]}'")->fetch_assoc();
			$rate_services[$service['category'].'#*#'.$service['service_type']][] = [$service, $service_line];
		}
	} ?>
	<div class="panel-group collapse-others standard-body-content" id="service_group_panel">
		<?php $panel_i = 0;
		foreach($rate_services as $cattype => $services) {
			$category = explode('#*#',$cattype)[0];
			$service_type = explode('#*#',$cattype)[1];
			$service_table_display = false; ?>
			<div class="panel panel-default cattype_block">
				<div class="panel-heading no_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#service_group_panel" href="#collapse_service_group_<?= $panel_i ?>">
							<?= $category.(!empty($service_type) ? ': '.$service_type : '') ?> - Services<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_service_group_<?= $panel_i ?>" class="panel-collapse collapse">
					<div class="panel-body white-background">
						<?php if(in_array('Category',$service_fields)) {
							$service_table_display = !empty($category) ? true : false; ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Category:</label>
								<div class="col-sm-8">
									<select name="service_category_group" data-placeholder="Select a Category" class="chosen-select-deselect form-control"><option></option>
										<?php $service_categories = $dbc->query("SELECT `category` FROM `services` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
										while($row_cat = $service_categories->fetch_assoc()['category']) { ?>
											<option <?= $row_cat == $category ? 'selected' : '' ?> value="<?= $row_cat ?>"><?= $row_cat ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php } ?>
						<?php if(in_array('Service Type',$service_fields)) {
							$service_table_display = (!empty($category) && !empty($service_type)) ? true : false; ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Service Type:</label>
								<div class="col-sm-8">
									<select name="service_type_group" data-placeholder="Select a Service Type" class="chosen-select-deselect form-control"><option></option>
										<?php $service_categories = $dbc->query("SELECT `category`, `service_type` FROM `services` WHERE `deleted`=0 GROUP BY CONCAT(`category`,`service_type`) ORDER BY `service_type`");
										while($service_row = $service_categories->fetch_assoc()) { ?>
											<option data-category="<?= $service_row['category'] ?>" value="<?= $service_row['service_type'] ?>" <?= !empty($category) && $category != $service_row['category'] ? 'style="display:none;"' : '' ?> <?= $category == $service_row['category'] && $service_type == $service_row['service_type'] ? 'selected' : '' ?>><?= $service_row['service_type'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php } ?>
						<?php if(in_array('Contacts # of Rooms',$service_fields)) {
							$num_rooms = mysqli_fetch_array(mysqli_query($dbc, "SELECT `num_rooms` FROM `contacts_services` WHERE `contactid` = '$contactid' AND `serviceid` = '{$services[0][0]['serviceid']}'"))['num_rooms']; ?>
							<div class="form-group">
								<label class="col-sm-4 control-label"># of Rooms:</label>
								<div class="col-sm-8">
									<select onchange="" name="service_num_rooms[]" data-placeholder="Select Number of Rooms" class="chosen-select-deselect">
										<?php for($room_i = 1; $room_i <= 10; $room_i++) { ?>
											<option value="<?= $room_i ?>" <?= $room_i == $num_rooms ? 'selected' : '' ?>><?= $room_i ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php } ?>
						<div class="service_group" <?= $service_table_display ? '' : 'style="display:none;"' ?>>
							<?php include('../Contacts/edit_addition_customer_rate_service_group.php'); ?>
						</div>
						<div class="service_group_hide" <?= !$service_table_display ? '' : 'style="display:none;"' ?>>
							Please select a Category and Service Type to display Services.
						</div>
					</div>
				</div>
			</div>
			<?php $panel_i++;
		} ?>
	</div>
	<input type="hidden" name="service_panel_i" value="<?= $panel_i ?>">
	<div class="form-group">
		<label class="control-label col-sm-4">Total Price:</label>
		<div class="col-sm-8">
			<input type="text" readonly value="<?= number_format($total_value,2) ?>" class="form-control">
		</div>
	</div>
	<?php if(in_array('Estimated Hours',$service_fields)) { ?>
		<script type="text/javascript">
		function edit_total_estimated_hours(a) {
			$('[name="total_estimated_hours"]').prop('readonly',false).css('background-color','').removeClass('readonly-block').focus();
			$('.edit_estimated_hours_btn').hide();
			$('.submit_estimated_hours_btn').show();
		}
		function update_total_estimated_hours(a, ratecardid) {
			var total_estimated_hours = $('[name="total_estimated_hours"]').val();
			$.ajax({
				url: '../Contacts/contacts_ajax.php?action=update_total_estimated_hours&ratecardid='+ratecardid+'&hours='+total_estimated_hours,
				method: 'GET',
				success: function(response) {
					$('[name="total_estimated_hours"]').prop('readonly',true).css('background-color','#eeeeee').addClass('readonly-block');
					$('.edit_estimated_hours_btn').show();
					$('.submit_estimated_hours_btn').hide();
				}
			});
		}
		</script>
		<div class="form-group">
			<label class="control-label col-sm-4">Total Estimated Hours:</label>
			<div class="col-sm-8">
				<input type="text" name="total_estimated_hours" readonly value="<?= time_decimal2time(($rate_card['total_estimated_hours'] > 0 ? $rate_card['total_estimated_hours'] : $total_hours)) ?>" class="form-control timepicker-5 readonly-block" style="background-color: #eeeeee;">
				<a href="?" onclick="edit_total_estimated_hours(this); return false;" class="btn brand-btn pull-right edit_estimated_hours_btn">Edit Estimated Hours</a>
				<a href="?" onclick="update_total_estimated_hours(this, '<?= $rate_card['ratecardid'] ?>'); return false;" class="btn brand-btn pull-right submit_estimated_hours_btn" style="display:none;">Submit Estimated Hours</a>
			</div>
		</div>
	<?php } ?>
<?php } else { ?>
	<table class="table table-bordered customer_rate_services" id="customer_rate_services">
		<tr class="hidden-sm hidden-xs">
			<?php if(in_array('Category',$service_fields)) {
				$columns++; ?>
				<th style="width:10%;">Category</th>
			<?php } ?>
			<?php if(in_array('Service Type',$service_fields)) {
				$columns++;  ?>
				<th style="width:10%;">Type</th>
			<?php } ?>
			<th style="width:10%;">Heading</th>
			<?php if(in_array('Contacts # of Rooms',$service_fields)) {
				$columns++;  ?>
				<th style="width:7%;"># of Rooms</th>
			<?php } ?>
			<th style="width:10%;">Rate Card Price</th>
			<?php if(in_array('Estimated Hours',$service_fields)) {
				$columns++;  ?>
				<th style="width:10%;">Time Estimate</th>
			<?php } ?>
			<th>Comments</th>
			<?php if(in_array('Service Create Ticket',$service_fields)) {
				$columns++;  ?>
				<th style="width:10%;">Function</th>
			<?php } ?>
			<th style="width: 10%;"></th>
		</tr>
		<?php 
		foreach(explode('**',$rate_card['services']) as $service_i => $service) {
			$service_line = explode('#',$service); ?>
			<tr <?= ($load_template > 0 && $service_line[0] > 0 && !in_array($service_line[0], $template_items)) ? 'style="display:none;"' : '' ?>>
			<?php 
			$service_time_estimate = '';
			if($service_line[0] > 0) {
				$service = $dbc->query("SELECT * FROM `services` WHERE `serviceid`='{$service_line[0]}'")->fetch_assoc();
				$service_time_estimate = $service['estimated_hours']; ?>
				<?php if(in_array('Category',$service_fields)) { ?>
					<td data-title="Category"><?= $service['category'] ?></td>
				<?php } ?>
				<?php if(in_array('Service Type',$service_fields)) { ?>
					<td data-title="Type"><?= $service['service_type'] ?></td>
				<?php } ?>
				<td data-title="Heading"><?= $service['heading'] ?><input type="hidden" name="heading" value="<?= $service['serviceid'] ?>"></td>
			<?php } else if($service != '##') { ?>
				<?php if(in_array('Category',$service_fields)) { ?>
					<td data-title="Category">
						<select onchange="rate_service_category_updated(this);" data-placeholder="Select Category" class="chosen-select-deselect service_category"><option />
							<?php $service_categories = $dbc->query("SELECT `category` FROM `services` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
							while($row_cat = $service_categories->fetch_assoc()['category']) { ?>
								<option value="<?= $row_cat ?>"><?= $row_cat ?></option>
							<?php } ?>
						</select>
						<!-- <span class="pull-right"><input type="checkbox" onclick="add_all_services(this);"> Add All</span> -->
					</td>
				<?php } ?>
				<?php if(in_array('Service Type',$service_fields)) { ?>
					<td data-title="Type">
						<select onchange="rate_service_type_updated(this);" name="type" data-placeholder="Select Type" class="chosen-select-deselect service_type"><option />
							<?php if(!in_array('Category',$service_fields)) {
								$service_categories = $dbc->query("SELECT `service_type` FROM `services` WHERE `deleted`=0 GROUP BY `service_type` ORDER BY `service_type`");
								while($service_row = $service_categories->fetch_assoc()) { ?>
									<option value="<?= $service_row['service_type'] ?>"><?= $service_row['service_type'] ?></option>
								<?php }
							} ?>
						</select>
						<!-- <span class="pull-right"><input type="checkbox" onclick="add_all_services(this);"> Add All</span> -->
					</td>
				<?php } ?>
				<td data-title="Heading">
					<select onchange="rate_service_updated(this);" name="heading" data-placeholder="Select Service" class="chosen-select-deselect"><option />
						<?php if(!in_array('Category',$service_fields) && !in_array('Service Type',$service_fields)) {
							$service_categories = $dbc->query("SELECT `serviceid`,`heading`,`category`,`service_type` FROM `services` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
							while($service_row = $service_categories->fetch_assoc()) { ?>
								<option data-category="<?= $service_row['category'] ?>" data-service-type="<?= $service_row['service_type'] ?>" value="<?= $service_row['serviceid'] ?>"><?= $service_row['heading'] ?></option>
							<?php }
						} ?>
					</select>
				</td>
			<?php }
			if($service != '##') { ?>
				<?php if(in_array('Contacts # of Rooms',$service_fields)) {
					$num_rooms = mysqli_fetch_array(mysqli_query($dbc, "SELECT `num_rooms` FROM `contacts_services` WHERE `contactid` = '$contactid' AND `serviceid` = '{$service_line[0]}'"))['num_rooms'];
					$num_rooms = empty($num_rooms) ? '1' : $num_rooms; ?>
					<td data-title="# of Rooms">
						<select name="service_num_rooms[]" data-placeholder="Select Number of Rooms" class="chosen-select-deselect">
							<?php for($room_i = 1; $room_i <= 10; $room_i++) { ?>
								<option value="<?= $room_i ?>" <?= $room_i == $num_rooms ? 'selected' : '' ?>><?= $room_i ?></option>
							<?php } ?>
						</select>
					</td>
				<?php } ?>
				<td data-title="Rate Card Price"><input type="number" name="services[]" data-contactid-field="clientid" data-row-id="<?= $rate_card['ratecardid'] ?>" data-row-field="ratecardid" value="<?= $service_line[1] ?>" data-field="services" data-prepend="<?= $service_line[0] ?>#" data-append="#<?= $service_line[2] ?>" data-append-last="**" data-table="rate_card" data-delimiter="**" class="form-control"></td>
				<?php if(in_array('Estimated Hours',$service_fields)) { ?>
					<td data-title="Time Estimate"><input type="text" name="service_time_estimate" class="form-control" disabled readonly value="<?= $service_time_estimate ?>"></td>
				<?php } ?>
				<td data-title="Comments"><input type="text" name="service_comments[]" data-contactid-field="clientid" data-row-id="<?= $rate_card['ratecardid'] ?>" data-row-field="ratecardid" value="<?= explode('#*#',$rate_card['service_comments'])[$service_i] ?>" data-field="service_comments" data-table="rate_card" data-delimiter="#*#" class="form-control"></td>
				<?php if(in_array('Service Create Ticket',$service_fields)) { ?>
					<td data-title="Function">
						<label class="control-checkbox any-width"><input type="checkbox" name="service_create" value="<?= $service_line[0] ?>" <?= $service_line[0] > 0 && in_array($service_line[0], $template_items) ? 'checked' : '' ?>>Include on <?= TICKET_NOUN ?></label>
						<button class="btn brand-btn" name="service_create" value="<?= $service_line[0] ?>" onclick="overlayIFrameSlider('../Ticket/index.php?calendar_view=true&edit=0&<?= $contact['category'] == BUSINESS_CAT ? 'bid' : 'clientid' ?>=<?= $contactid ?>&from_type=customer_rate_services&serviceid='+this.value, 'auto', true, true); return false;">Create <?= TICKET_NOUN ?></button>
					</td>
				<?php } ?>
				<td data-title="" align="center">
	                <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addDelimitedRow($('.customer_rate_services'));">
	                <img src="../img/remove.png" class="inline-img pull-right remove_btn" onclick="rate_service_remove(this);">
				</td>
			</tr>
			<?php }
			$service_time_estimate = time_time2decimal($service_time_estimate);
			$total_hours += $service_time_estimate;
			if($service_time_estimate == 0) {
				$service_time_estimate = 1;
			}
			$total_value += $service_line[1]; ?>
		<?php }
		if($field_option == "Customer Rate Card Totalled") { ?>
			<tr>
				<?php if(in_array('Category',$service_fields)) { ?>
					<td style="width:10%;"></td>
				<?php } ?>
				<?php if(in_array('Service Type',$service_fields)) { ?>
					<td style="width:10%;"></td>
				<?php } ?>
				<td style="width:10%;"></td>
				<?php if(in_array('Contacts # of Rooms',$service_fields)) { ?>
					<td style="width:7%;"></td>
				<?php } ?>
				<td style="width:10%;">$<?= number_format($total_value,2) ?></td>
				<?php if(in_array('Estimated Hours',$service_fields)) { ?>
					<td style="width:10%;"><?= time_decimal2time($total_hours) ?></td>
				<?php } ?>
				<td></td>
				<?php if(in_array('Service Create Ticket',$service_fields)) { ?>
					<td style="width:10%;"></td>
				<?php } ?>
				<td style="width: 10%;"></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>