<?php
/*
Add Vendor
*/
error_reporting(0);
if($field_option == 'Medication Details') { ?>
	<script type="text/javascript">
	$(document).ready(function() {
		$("#medication_type").change(function() {
			if($("#medication_type option:selected").text() == 'New Medication') {
					$( "#new_medication" ).show();
			} else {
				$( "#new_medication" ).hide();
			}
		});

		$("#category").change(function() {
			if($("#category option:selected").text() == 'New Category') {
					$( "#new_category" ).show();
			} else {
				$( "#new_category" ).hide();
			}
		});

		$('#add_row_doc').on( 'click', function () {
			var clone = $('.additional_doc').clone();
			clone.find('.form-control').val('');
			clone.removeClass("additional_doc");
			$('#add_here_new_doc').append(clone);
			return false;
		});

		$('#add_row_link').on( 'click', function () {
			var clone = $('.additional_link').clone();
			clone.find('.form-control').val('');
			clone.removeClass("additional_link");
			$('#add_here_new_link').append(clone);
			return false;
		});

	});
	function deleteMedicationUpload(list, meduploadid) {
		$.ajax({
			method: "POST",
			url: "../Contacts/contacts_ajax.php?action=delete_medication_upload",
			data: { meduploadid: meduploadid },
			success: function(response) {
				$(list).closest('li').remove();
			}
		});
	}
	function addMeds() {
		destroyInputs($('.meds_group'));
		var last_med = $('.meds_group').last();
		var clone = last_med.clone();
		clone.find('input,select,textarea').val('');
		clone.find('[data-row-id]').data('row-id','');
		last_med.after(clone);
		initInputs('.meds_group');
		$('.meds_group [data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
	}
	function remMeds(img) {
		if($('.meds_group').length == 1) {
			addMeds();
		}
		$(img).closest('.meds_group').find('[name=deleted]').val(1).change();
		$(img).closest('.meds_group').remove();
	}
	</script>

	<?php $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT medication FROM field_config"));
	$value_config = ','.$get_field_config['medication'].',';
	$clientid = $_GET['contactid'];
	$default_category = get_config($dbc, 'medication_category_default');
	$medications = mysqli_query($dbc,"SELECT * FROM medication WHERE clientid='$contactid'");
	$get_med = mysqli_fetch_assoc($medications);
	$label = $contact['category'];
	if($label == 'Members') {
		$label = 'Member';
	}
	do {
		$medicationid = $get_med['medicationid'];
		$medicationcontactid = $get_med['contactid'];

		$administration_times = $get_med['administration_times'];
		$side_effects = $get_med['side_effects'];
		$delivery_method = $get_med['delivery_method'];

		$medication_type = $get_med['medication_type'];
		$category = !empty($default_category) ? $default_category : '';
		$medication_code = $get_med['medication_code'];
		$heading = $get_med['heading'];
		$cost = $get_med['cost'];
		$description = $get_med['description'];
		$dosage = $get_med['dosage'];
		$quote_description = $get_med['quote_description'];
		$invoice_description = $get_med['invoice_description'];
		$ticket_description = $get_med['ticket_description'];
		$name = $get_med['name'];
		$title = $get_med['title'];
		$fee = $get_med['fee'];

		$final_retail_price = $get_med['final_retail_price'];
		$admin_price = $get_med['admin_price'];
		$wholesale_price = $get_med['wholesale_price'];
		$commercial_price = $get_med['commercial_price'];
		$client_price = $get_med['client_price'];
		$minimum_billable = $get_med['minimum_billable'];
		$estimated_hours = $get_med['estimated_hours'];
		$actual_hours = $get_med['actual_hours'];
		$msrp = $get_med['msrp'];

		$unit_price = $get_med['unit_price'];
		$unit_cost = $get_med['unit_cost'];
		$rent_price = $get_med['rent_price'];
		$rental_days = $get_med['rental_days'];
		$rental_weeks = $get_med['rental_weeks'];
		$rental_months = $get_med['rental_months'];
		$rental_years = $get_med['rental_years'];
		$reminder_alert = $get_med['reminder_alert'];
		$daily = $get_med['daily'];
		$weekly = $get_med['weekly'];
		$monthly = $get_med['monthly'];
		$annually = $get_med['annually'];
		$total_days = $get_med['total_days'];
		$total_hours = $get_med['total_hours'];
		$total_km = $get_med['total_km'];
		$total_miles = $get_med['total_miles'];

		$start_date = $get_med['start_date'];
		$end_date = $get_med['end_date'];
		$reminder_date = $get_med['reminder_date']; ?>
		<input type="hidden" id="medicationid" name="medicationid" value="<?php echo $medicationid; ?>" />
		<input type="hidden" id="clientid" name="clientid" value="<?php echo $clientid; ?>" />
		<input type="hidden" id="submit_type" name="submit_type" value="medications" />

		<div class="meds_group">
			<h5>Medication</h5>
			<?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label">Staff:</label>
			  <div class="col-sm-8">
				<select data-placeholder="Choose a Staff Member..." data-field="contactid" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="medicationcontactid" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <?php
					$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." order by first_name");
					while($row = mysqli_fetch_array($query)) {
						if ($medicationcontactid == $row['contactid']) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
					}
				  ?>
				</select>
			  </div>
			</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Client".',') !== FALSE) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?= $label ?>:</label>
			  <div class="col-sm-8">
				<select disabled="true" data-placeholder="Choose a <?= $label ?>..." data-field="clientid" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="clientid" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <?php
					$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category='".$_GET['category']."' order by first_name");
					while($row = mysqli_fetch_array($query)) {
						if ($contactid == $row['contactid']) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
					}
				  ?>
				</select>
			  </div>
			</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Medication Type".',') !== FALSE) { ?>
		   <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Medication Type<span class="hp-red">*</span>:</label>
			<div class="col-sm-8">
				<select id="medication_type" data-field="medication_type" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="medication_type" class="chosen-select-deselect form-control" width="380">
					<option value=''></option>
					<?php
					$types = array('Prescribed', 'Over the Counter', 'PRN');
					if(!empty(get_config($dbc, 'medication_medtype_custom'))) {
						$types = explode(',', get_config($dbc, 'medication_medtype_custom'));
					}
					foreach($types as $type) {
						if ($medication_type == $type) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option ".$selected." value='". $type."'>".$type.'</option>';

					}
					//echo "<option value = 'Other'>New Medication</option>";
					?>
				</select>
			</div>
		  </div>

		   <!--div class="form-group" id="new_medication" style="display: none;">
			<label for="travel_task" class="col-sm-4 control-label">New Medication Type:
			</label>
			<div class="col-sm-8">
				<input data-contactid-field="clientid" name="new_medication" type="text" class="form-control" />
			</div>
		  </div-->
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>

		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Category<span class="hp-red">*</span>:</label>
			<div class="col-sm-8">
			  <input data-contactid-field="clientid" name="med_category" value="<?php echo $category; ?>" type="text" id="name" class="form-control">
			</div>
		  </div>

		  <?php /* ?>
		   <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Category<span class="hp-red">*</span>:</label>
			<div class="col-sm-8">
				<select id="category" data-contactid-field="clientid" name="category" class="chosen-select-deselect form-control" width="380">
					<option value=''></option>
					<?php
					$query = mysqli_query($dbc,"SELECT distinct(category) FROM medication order by category");
					while($row = mysqli_fetch_array($query)) {
						if ($category == $row['category']) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option ".$selected." value='". $row['category']."'>".$row['category'].'</option>';

					}
					echo "<option value = 'Other'>New Category</option>";
					?>
				</select>
			</div>
		  </div>

		   <div class="form-group" id="new_category" style="display: none;">
			<label for="travel_task" class="col-sm-4 control-label">New Category:
			</label>
			<div class="col-sm-8">
				<input data-contactid-field="clientid" name="new_category" type="text" class="form-control" />
			</div>
		  </div>
		  <?php */ ?>

		  <?php } ?>

		   <?php if (strpos($value_config, ','."Title".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label"><?= (!empty(get_config($dbc, 'medication_title_custom')) ? get_config($dbc, 'medication_title_custom') : 'Title') ?><span class="hp-red">*</span>:</label>
			<div class="col-sm-8">
			  <input data-field="title" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="title" value="<?php echo $title; ?>" type="text" id="title" class="form-control">
			</div>
		  </div>
		  <?php } ?>

		<?php if (strpos($value_config, ','."Delivery Method".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="delivery_method" class="col-sm-4 control-label">Delivery Method:</label>
			<div class="col-sm-8">
			  <textarea data-field="delivery_method" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="delivery_method" type="text" class="form-control"><?php echo $delivery_method; ?></textarea>
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."Side Effects".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="side_effects" class="col-sm-4 control-label">Side Effects:</label>
			<div class="col-sm-8">
			  <textarea data-field="side_effects" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="side_effects" type="text" class="form-control"><?php echo $side_effects; ?></textarea>
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."Administration Times".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="administration_times" class="col-sm-4 control-label">Administration Times:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="checkbox" onclick="if(this.checked) { $(this.closest('.form-group').find('[name=administration_times]').val('As Needed').change(); }"> PRN <span class="popover-examples list-inline"><a href="" data-toggle="tooltip" data-placement="top" title="As Needed, not used at a specific time."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span></label>
			  <input data-field="administration_times" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="administration_times" value="<?php echo $administration_times; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>

			<?php if (strpos($value_config, ','."Uploader".',') !== FALSE) {
			?>

			<div class="form-group">
				<label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
						<span class="popover-examples list-inline">&nbsp;
						<a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
				</label>
				<div class="col-sm-8">
					<?php
					if(!empty($medicationid)) {
						$query_check_credentials = "SELECT * FROM medication_uploads WHERE medicationid='$medicationid' AND type = 'Document' ORDER BY meduploadid DESC";
						$result = mysqli_query($dbc, $query_check_credentials);
						$num_rows = mysqli_num_rows($result);
						if($num_rows > 0) {
							while($row = mysqli_fetch_array($result)) {
								$meduploadid = $row['meduploadid'];
								echo '<ul>';
								echo '<li><a href="download/medications/'.$row['document_link'].'" target="_blank">'.$row['document_link'].'</a> - <a onclick="deleteMedicationUpload(this,'.$meduploadid.'); return false;" href=""> Delete</a></li>';
								echo '</ul>';
							}
						}
					}
					?>
					<div class="enter_cost additional_doc clearfix">
						<div class="clearfix"></div>

						<div class="form-group clearfix">
							<div class="col-sm-5">
								<input data-field="upload_document" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
							</div>
						</div>

					</div>

					<div id="add_here_new_doc"></div>

					<div class="form-group triple-gapped clearfix">
						<div class="col-sm-offset-4 col-sm-8">
							<button id="add_row_doc" class="btn brand-btn pull-left">Add Another Document</button>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Link".',') !== FALSE) {
			?>
			<div class="form-group">
				<label for="additional_note" class="col-sm-4 control-label">Link(s):<br><em>(e.g. - https://www.google.com)</em>
				</label>
				<div class="col-sm-8">
					<?php
					if(!empty($_GET['medicationid'])) {
						$query_check_credentials = "SELECT * FROM medication_uploads WHERE medicationid='$medicationid' AND type = 'Link' ORDER BY meduploadid DESC";
						$result = mysqli_query($dbc, $query_check_credentials);
						$num_rows = mysqli_num_rows($result);
						if($num_rows > 0) {
							$link_no = 1;
							while($row = mysqli_fetch_array($result)) {
								$meduploadid = $row['meduploadid'];
								echo '<ul>';
								echo '<li><a target="_blank" href=\''.$row['document_link'].'\'">Link '.$link_no.'</a> - <a href="add_medication.php?meduploadid='.$meduploadid.'&medicationid='.$medicationid.'"> Delete</a></li>';
								echo '</ul>';
								$link_no++;
							}
						}
					}
					?>
					<div class="enter_cost additional_link clearfix">
						<div class="clearfix"></div>

						<div class="form-group clearfix">
							<div class="col-sm-5">
								<input data-field="support_link" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="support_link[]" type="text" class="form-control">
							</div>
						</div>

					</div>

					<div id="add_here_new_link"></div>

					<div class="form-group triple-gapped clearfix">
						<div class="col-sm-offset-4 col-sm-8">
							<button id="add_row_link" class="btn brand-btn pull-left">Add More Links</button>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

			   <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Heading:</label>
				<div class="col-sm-8">
				  <input data-field="heading" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="heading" value="<?php echo $heading; ?>" type="text" id="name" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			   <?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Name<span class="hp-red">*</span>:</label>
				<div class="col-sm-8">
				  <input data-field="name" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="name" value="<?php echo $name; ?>" type="text" id="name" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Medication Code".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Medication Code:</label>
				<div class="col-sm-8">
				  <input data-field="medication_code" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="medication_code" value="<?php echo $medication_code; ?>" type="text" id="name" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Description".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="first_name[]" class="col-sm-4 control-label">Description:</label>
				<div class="col-sm-8">
				  <textarea data-field="description" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Dosage".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Dosage:</label>
				<div class="col-sm-8">
				  <input data-field="dosage" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="dosage" value="<?php echo $dosage; ?>" type="text" id="name" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="first_name[]" class="col-sm-4 control-label"></label>
				<div class="col-sm-8">
				  <input type="checkbox" value="1" data-contactid-field="clientid" name="same_desc">Check this if Quote Description is same as Description.
				</div>
			  </div>

			  <div class="form-group">
				<label for="first_name[]" class="col-sm-4 control-label">Quote Description:</label>
				<div class="col-sm-8">
				  <textarea data-field="quote_description" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="quote_description" rows="5" cols="50" class="form-control"><?php echo $quote_description; ?></textarea>
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Invoice Description:</label>
				<div class="col-sm-8">
				  <textarea data-field="invoice_description" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="invoice_description" rows="5" cols="50" class="form-control"><?php echo $invoice_description; ?></textarea>
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label"><?= TICKET_NOUN ?> Description:</label>
				<div class="col-sm-8">
				  <textarea data-field="ticket_description" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="ticket_description" rows="5" cols="50" class="form-control"><?php echo $ticket_description; ?></textarea>
				</div>
			  </div>
			  <?php } ?>

			   <?php if (strpos($value_config, ','."Fee".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Fee<span class="hp-red">*</span>:</label>
				<div class="col-sm-8">
				  <input data-field="fee" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="fee" value="<?php echo $fee; ?>" type="text" id="name" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Cost:</label>
				<div class="col-sm-8">
				  <input data-field="cost" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="cost" value="<?php echo $cost; ?>" type="text" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Final Retail Price:</label>
				<div class="col-sm-8">
				  <input data-field="final_retail_price" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="final_retail_price" value="<?php echo $final_retail_price; ?>" type="text" class="form-control">
				</div>
			  </div>
			  <?php } ?>

				<?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Admin Price:</label>
				<div class="col-sm-8">
				  <input data-field="admin_price" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="admin_price" value="<?php echo $admin_price; ?>" type="text" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Wholesale Price:</label>
				<div class="col-sm-8">
				  <input data-field="wholesale_price" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="wholesale_price" value="<?php echo $wholesale_price; ?>" type="text" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Commercial Price:</label>
				<div class="col-sm-8">
				  <input data-field="commercial_price" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="commercial_price" value="<?php echo $commercial_price; ?>" type="text" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label"><?= $label ?> Price:</label>
				<div class="col-sm-8">
				  <input data-field="client_price" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="client_price" value="<?php echo $client_price; ?>" type="text" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Minimum Billable:</label>
				<div class="col-sm-8">
				  <input data-field="minimum_billable" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="minimum_billable" value="<?php echo $minimum_billable; ?>" type="text" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Estimated Hours:</label>
				<div class="col-sm-8">
				  <input data-field="estimated_hours" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="estimated_hours" value="<?php echo $estimated_hours; ?>" type="text" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Actual Hours:</label>
				<div class="col-sm-8">
				  <input data-field="actual_hours" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="actual_hours" value="<?php echo $actual_hours; ?>" type="text" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			  <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) { ?>
			  <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">MSRP:</label>
				<div class="col-sm-8">
				  <input data-field="msrp" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="msrp" value="<?php echo $msrp; ?>" type="text" class="form-control">
				</div>
			  </div>
			  <?php } ?>

			<?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { ?>
			<div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Unit Price:</label>
			<div class="col-sm-8">
			  <input data-field="unit_price" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="unit_price" value="<?php echo $unit_price; ?>" type="text" class="form-control">
			</div>
			</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Unit Cost".',') !== FALSE) { ?>
			<div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Unit Cost:</label>
			<div class="col-sm-8">
			  <input data-field="unit_cost" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="unit_cost" value="<?php echo $unit_cost; ?>" type="text" class="form-control">
			</div>
			</div>
			<?php } ?>

		  <?php if (strpos($value_config, ','."Rent Price".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Rent Price:</label>
			<div class="col-sm-8">
			  <input data-field="rent_price" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="rent_price" value="<?php echo $rent_price; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>


		  <?php if (strpos($value_config, ','."Rental Days".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Rental Days:</label>
			<div class="col-sm-8">
			  <input data-field="rental_days" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="rental_days" value="<?php echo $rental_days; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Rental Weeks:</label>
			<div class="col-sm-8">
			  <input data-field="rental_weeks" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="rental_weeks" value="<?php echo $rental_weeks; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."Rental Months".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Rental Months:</label>
			<div class="col-sm-8">
			  <input data-field="rental_months" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="rental_months" value="<?php echo $rental_months; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."Rental Years".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Rental Years:</label>
			<div class="col-sm-8">
			  <input data-field="rental_years" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="rental_years" value="<?php echo $rental_years; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Reminder/Alert:</label>
			<div class="col-sm-8">
			  <input data-field="reminder_alert" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="reminder_alert" value="<?php echo $reminder_alert; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>


		  <?php if (strpos($value_config, ','."Daily".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Daily:</label>
			<div class="col-sm-8">
			  <input data-field="daily" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="daily" value="<?php echo $daily; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."Weekly".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Weekly:</label>
			<div class="col-sm-8">
			  <input data-field="weekly" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="weekly" value="<?php echo $weekly; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."Monthly".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Monthly:</label>
			<div class="col-sm-8">
			  <input data-field="monthly" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="monthly" value="<?php echo $monthly; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."Annually".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Annually:</label>
			<div class="col-sm-8">
			  <input data-field="annually" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="annually" value="<?php echo $annually; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."#Of Days".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">#Of Days:</label>
			<div class="col-sm-8">
			  <input data-field="total_days" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="total_days" value="<?php echo $total_days; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."#Of Hours".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">#Of Hours:</label>
			<div class="col-sm-8">
			  <input data-field="total_hours" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="total_hours" value="<?php echo $total_hours; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>

		  <?php if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">#Of Kilometers:</label>
			<div class="col-sm-8">
			  <input data-field="total_km" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="total_km" value="<?php echo $total_km; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."#Of Miles".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">#Of Miles:</label>
			<div class="col-sm-8">
			  <input data-field="total_miles" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="total_miles" value="<?php echo $total_miles; ?>" type="text" class="form-control">
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."Start Date".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Start Date:</label>
			<div class="col-sm-8">
			  <input data-field="start_date" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="start_date" value="<?php echo $start_date; ?>" type="text" class="datepicker form-control">
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."End Date".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">End Date:</label>
			<div class="col-sm-8">
			  <input data-field="end_date" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="end_date" value="<?php echo $end_date; ?>" type="text" class="datepicker form-control">
			</div>
		  </div>
		  <?php } ?>
		  <?php if (strpos($value_config, ','."Reminder Date".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Reminder Date:
				<span class="popover-examples list-inline">&nbsp;
					<a data-toggle="tooltip" data-placement="top" title="" data-original-title="An email will be sent out on this date as a reminder that a medication requires attention."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a>
				</span>
			</label>
			<div class="col-sm-8">
			  <input data-field="reminder_date" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" data-contactid-field="clientid" name="reminder_date" value="<?php echo $reminder_date; ?>" type="text" class="datepicker form-control">
			</div>
		  </div>
		  <?php } ?>
		  <input type="hidden" name="deleted" data-table="medication" data-row-field="medicationid" data-row-id="<?= $medicationid ?>" value="<?= $get_med['deleted'] ?>">
		  <img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addMeds();">
		  <img class="inline-img pull-right" src="../img/remove.png" onclick="remMeds(this);">
	  </div>
	<?php } while($get_med = mysqli_fetch_assoc($medications)); ?>
	<h4>Medication Administration</h4>
	<div id="no-more-tables">
	  <table class="table table-bordered">
		<tr class="hidden-sm hidden-xs">
			<th>Medication</th>
			<th>Dosage</th>
			<th>Time</th>
			<th>Date</th>
			<th>Administered</th>
			<th>Witnessed</th>
		</tr>
		<?php $administration = mysqli_query($dbc, "SELECT `tickets`.`to_do_date` date, `ticket_attached`.`position` meds, `ticket_attached`.`description` dosage, `ticket_attached`.`shift_start` time, `ticket_attached`.`sign_name`, `ticket_attached`.`witness_name` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `item_id`='$contactid' AND `ticket_attached`.`src_table`='medication' AND `arrived`=1 AND `tickets`.`deleted`=0 AND `ticket_attached`.`deleted`=0");
		while($med_admin = mysqli_fetch_assoc($administration)) { ?>
			<tr>
				<td data-title="Medication"><?= $med_admin['meds'] ?></td>
				<td data-title="Dosage"><?= $med_admin['dosage'] ?></td>
				<td data-title="Time"><?= $med_admin['time'] ?></td>
				<td data-title="Date"><?= $med_admin['date'] ?></td>
				<td data-title="Administered"><?= $med_admin['sign_name'] ?></td>
				<td data-title="Witnessed"><?= $med_admin['witness_name'] ?></td>
			</tr>
		<?php } ?>
	  </table>
	</div>
<?php } ?>