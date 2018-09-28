<?php
mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) SELECT 'marsheet_fields', 'Route,Dosage,Instructions,Medication Notes,Notes' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name` = 'marsheet_fields') num WHERE num.rows = 0");
mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) SELECT 'marsheet_row_headings', 'AM,Snack,Lunch,Snack,Supper,Snack,Bedtime' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name` = 'marsheet_row_headings') num WHERE num.rows = 0");
$value_config = ','.get_config($dbc, "marsheet_fields").',';

$date = date('Y-m-d');
if(!empty($_GET['marsheet_date'])) {
	$date = date('Y-m-d', strtotime($_GET['marsheet_date']));
} else if(!empty($_POST['marsheet_date'])) {
	$date = date('Y-m-d', strtotime($_POST['marsheet_date']));
}
$month = date('m', strtotime($date));
$year = date('Y', strtotime($date));
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year); ?>
<script type="text/javascript">
$(window).on('load', function() {
	<?php if(isset($_GET['subtab']) && $_GET['subtab'] == 'MAR Sheet') { ?>
		$('#nav_mar_sheet').trigger('click');
	<?php } ?>
});
window.addMarSheetMedicationBool = false;
window.addMarSheetBool = false;
$(document).ready(function() {
});
function addMarSheet(addMarSheetBool) {
	if(!addMarSheetBool) {
		$('#dialog_marsheet').dialog({
            resizable: true,
            height: 500,
            width: ($(window).width() <= 600 ? $(window).width() : 600),
            modal: true,
            buttons: {
                "Add MAR Sheet": function() {
                	var month = $('[name="marsheet_month"]').val();
                	var year = $('[name="marsheet_year"]').val();
                	var medicationid = [];
                	$('[name="marsheet_medication[]"] option:selected').each(function() {
                		medicationid.push(this.value);
                	});
                	var new_medication = $('[name="new_marsheet_medication"]').val();
                	var contactid = $('[name="edit"]').val();
                	$.ajax({
                		url: '../Contacts/contacts_ajax.php?action=add_marsheet',
                		type: 'POST',
                		data: {
                			month: month,
                			year: year,
                			medicationid: medicationid,
                			new_medication: new_medication,
                			contactid: contactid
                		},
                		dataType: 'html',
                		success: function(response) {
                			// console.log(response);
		                	window.addMarSheetBool = true;
		                    $('[name="add_marsheet"]').trigger('click');
                		}
                	});
                    $(this).dialog('close');
                },
                Cancel: function() {
                    window.addMarSheetBool = false;
                    $(this).dialog('close');
                }
          }
        });
		return false;
	} else {
		$('[name="subtab"]').val('MAR Sheet');
		return true;
	}
}

function addMarSheetMedication(addMarSheetMedicationBool, btn) {
	if(!addMarSheetMedicationBool) {
		$('#dialog_marsheet_med').dialog({
            resizable: true,
            height: 300,
            width: ($(window).width() <= 600 ? $(window).width() : 600),
            modal: true,
            buttons: {
                "Add Medication": function() {
                	var marsheetid = $(btn).closest('.marsheet_block').find('[name="marsheetid"]').val();
                	var medicationid = $('[name="add_marsheet_medication"]').val();
                	var new_medication = $('[name="add_new_marsheet_medication"]').val();
                	$.ajax({
                		url: '../Contacts/contacts_ajax.php?action=add_marsheet_medication',
                		type: 'POST',
                		data: {
                			marsheetid: marsheetid,
                			medicationid: medicationid,
                			new_medication: new_medication
                		},
                		dataType: 'html',
                		success: function(response) {
                			// console.log(response);
		                	window.addMarSheetMedicationBool = true;
		                    $(btn).closest('.marsheet_block').find('.add_marsheet_medication').trigger('click');
                		}
                	});
                    $(this).dialog('close');
                },
                Cancel: function() {
                    window.addMarSheetMedicationBool = false;
                    $(this).dialog('close');
                }
          }
        });
		return false;
	} else {
		$('[name="subtab"]').val('MAR Sheet');
		window.addMarSheetBool = true;
        $('[name="add_marsheet"]').trigger('click');
		return true;
	}
}
$(document).on('change', 'select[name="marsheet_medication[]"]', function() { newMarSheetMedication(this); });
$(document).on('change', 'select[name="add_marsheet_medication"]', function() { newAddMarSheetMedication(this); });
function addMarSheetRow(btn) {
	var block = $(btn).closest('.marsheet_table_block').find('.marsheet_detail_row').last();
	var clone = block.clone();
	var marsheetid = $(btn).closest('.marsheet_block').find('[name="marsheetid"]').val();

	clone.find('.form-control').val('');
	clone.find('.medication_column').remove();
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=add_marsheet_row&marsheetid='+marsheetid,
		type: 'GET',
		dataType: 'html',
		success: function(response) {
			var marsheetrowid = response;
			clone.find('[name="marsheetrowid"]').val(response);
			clone.find('input').each(function() {
				$(this).attr('data-row-id', response);
			});
			clone.find('[data-field]').not('.tile-search').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
		}
	});
	block.after(clone);
	var table_block = $(btn).closest('.marsheet_table_block');
	if(table_block.find('.medication_column').length > 0) {
		table_block.find('.medication_column').prop('rowspan', table_block.find('.marsheet_detail_row').length);
	}
}
function deleteMarSheetRow(btn) {
	if(confirm('Are you sure you want to delete this row?')) {
		var table_block = $(btn).closest('.marsheet_table_block');
		var medication_column = '';
		var add_medication_column = false;
		if(table_block.find('.medication_column').length > 0) {
			add_medication_column = true;
			medication_column = table_block.find('.medication_column').clone();
			medication_column.find('[data-field]').not('.tile-search').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
			$('.add_marsheet_medication').click(function() {
				return addMarSheetMedication(window.addMarSheetMedicationBool, this);
			});
		}
		var block = $(btn).closest('.marsheet_detail_row');
		if(table_block.find('.marsheet_detail_row').length <= 1) {
			addMarSheetRow(btn);
		}
		var marsheetrowid = block.find('[name="marsheetrowid"]').val();
		$.ajax({
			url: '../Contacts/contacts_ajax.php?action=delete_marsheet_row&marsheetrowid='+marsheetrowid,
			type: 'GET',
			dataType: 'html',
			success: function(response) {
			}
		});
		block.remove();
		if(table_block.find('.medication_column').length > 0) {
			table_block.find('.medication_column').prop('rowspan', table_block.find('.marsheet_detail_row').length);
		} else if(add_medication_column) {
			table_block.find('.marsheet_detail_row').first().prepend(medication_column);
			table_block.find('.medication_column').prop('rowspan', table_block.find('.marsheet_detail_row').length);
		}
	}
}
function deleteMarSheet(btn) {
	if(confirm('Are you sure you want to delete this MAR Sheet?')) {
		var block = $(btn).closest('.marsheet_block');
		var marsheetid = block.find('[name="marsheetid"]').val();
		$.ajax({
			url: '../Contacts/contacts_ajax.php?action=delete_marsheet&marsheetid='+marsheetid,
			type: 'GET',
			dataType: 'html',
			success: function(response) {
				block.remove();
			}
		});
	}
}
function deleteMarsheetMedication(btn) {
	if(confirm('Are you sure you want to remove this Medication from this MAR Sheet?')) {
		var block = $(btn).closest('.marsheet_block');
		var marsheetid = block.find('[name="marsheetid"]').val();
		var med_block = $(btn).closest('.medication_block');
		var medicationid = $(btn).data('medicationid');
		$.ajax({
			url: '../Contacts/contacts_ajax.php?action=delete_marsheet_medication&marsheetid='+marsheetid+'&medicationid='+medicationid,
			type: 'GET',
			dataType: 'html',
			success: function(response) {
				med_block.remove();
			}
		});
	}
}
function newMarSheetMedication(sel) {
	if($(sel).find('option[value=NEW_MED]').is(':selected')) {
		$('[name="new_marsheet_medication"]').show();
	} else {
		$('[name="new_marsheet_medication"]').hide();
	}
}
function newAddMarSheetMedication(sel) {
	if($(sel).val() == 'NEW_MED') {
		$('[name="add_new_marsheet_medication"]').show();
	} else {
		$('[name="add_new_marsheet_medication"]').hide();
	}
}
function exportMarSheet() {
	var contactid = '<?= $_GET['edit']; ?>';
	var month = '<?= $month ?>';
	var year = '<?= $year ?>';
	var url = '../Medication/marsheet_pdf.php?contactid='+contactid+'&month='+month+'&year='+year;

	window.open(url, '_blank');
}
<?php if(FOLDER_NAME == 'medication') { ?>
	function syncUnsaved() {}
<?php } ?>
</script>

<input type="hidden" name="contactid" value="<?= $_GET['edit'] ?>">
<div id="dialog_marsheet" title="Add MAR Sheet" style="display: none;">
	<div class="form-group">
		<label class="col-sm-4 control-label">Month:</label>
		<div class="col-sm-8">
			<select name="marsheet_month" class="chosen-select-deselect form-control">
				<?php for($cur_month = 1; $cur_month <= 12; $cur_month++) { ?>
					<option <?= (date('n', strtotime($date)) == $cur_month ? 'selected' : '') ?> value="<?= $cur_month ?>"><?= date('F', mktime(0, 0, 0, $cur_month, 10)) ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Year:</label>
		<div class="col-sm-8">
			<select name="marsheet_year" class="chosen-select-deselect form-control">
				<?php for($cur_year = intval(date('Y')) - 10; $cur_year <= intval(date('Y')); $cur_year++) { ?>
					<option <?= (date('Y', strtotime($date)) == $cur_year ? 'selected' : '') ?> value="<?= $cur_year ?>"><?= $cur_year ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Medication:</label>
		<div class="col-sm-8">
			<select name="marsheet_medication[]" multiple class="chosen-select-deselect form-control">
				<option value="NEW_MED">New Medication</option>
				<?php $medications = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `medication` WHERE `clientid` = '".$_GET['edit']."' AND `deleted` = 0"),MYSQLI_ASSOC);
					foreach ($medications as $medication) { ?>
						<option value="<?= $medication['medicationid'] ?>"><?= $medication['title'] ?></option>
					<?php } ?>
			</select>
			<input type="text" placeholder="New Medication Name" name="new_marsheet_medication" class="form-control" style="display: none;">
		</div>
	</div>
</div>
<div id="dialog_marsheet_med" title="Add Medication to MAR Sheet" style="display: none;">
	<div class="form-group">
		<label class="col-sm-4 control-label">Medication:</label>
		<div class="col-sm-8">
			<select name="add_marsheet_medication" class="chosen-select-deselect form-control">
				<option></option>
				<option value="NEW_MED">New Medication</option>
				<?php $medications = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `medication` WHERE `clientid` = '".$_GET['edit']."' AND `deleted` = 0"),MYSQLI_ASSOC);
					foreach ($medications as $medication) { ?>
						<option value="<?= $medication['medicationid'] ?>"><?= $medication['title'] ?></option>
					<?php } ?>
			</select>
			<input type="text" placeholder="New Medication Name" name="add_new_marsheet_medication" class="form-control" style="display: none;">
		</div>
	</div>
</div>

<div class="form-group">
	<?php if(FOLDER_NAME == 'medication') { ?>
	<div class="col-sm-5">
	    <label class="col-sm-6 control-label"><span class="pull-right">Client:</span></label>
	    <div class="col-sm-6">
	        <select name="search_clientid" class="form-control chosen-select-deselect">
	        	<option></option>
		        <?php
		            $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Clients' AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
		            foreach ($query as $id) { ?>
		                <option <?= ($id == $_GET['edit'] ? 'selected' : '') ?> value="<?= $id ?>"><?= get_contact($dbc, $id) ?></option>
		            <?php }
		        ?>
	        </select>
	    </div>
	</div>
	<?php } ?>
	<div class="col-sm-4 <?= (FOLDER_NAME == 'medication' ? 'col-sm-offset-1' : 'col-sm-offset-4') ?>">
		<label class="control-label">Date:</label>
		<input type="text" name="marsheet_date" value="<?= $date ?>" class="form-control inline datepicker">
		<button tyep="submit" name="load_marsheet_date" value="load_marsheet_date" onclick="$('[name=subtab]').val('MAR Sheet');" class="btn brand-btn mobile-block">Submit</button>
		<input type="hidden" name="edit" value="<?= $_GET['edit'] ?>">
		<input type="hidden" name="category" value="<?= $_GET['category'] ?>">
		<input type="hidden" name="subtab" value="MAR Sheet">
	</div>
	<div class="<?= (FOLDER_NAME == 'medication' ? 'col-sm-2' : 'col-sm-4') ?>">
		<button name="add_marsheet" value="add_marsheet" class="btn brand-btn mobile-block pull-right" onclick="return addMarSheet(window.addMarSheetBool);">Add MAR Sheet</button>
		<button name="export_marsheet" value="export_marsheet" onclick="exportMarSheet(); return false;" class="btn brand-btn mobile-block pull-right">Export to PDF</button>
	</div>
</div>
<div class="clearfix"></div>

<?php if(!empty($_GET['edit'])) {
	$marsheet_query = "SELECT * FROM `marsheet` WHERE `contactid` = '".$_GET['edit']."' AND `month` = '$month' AND `year` = '$year' AND `deleted` = 0 ORDER BY `marsheetid` ASC";
	$marsheet_result = mysqli_fetch_all(mysqli_query($dbc, $marsheet_query),MYSQLI_ASSOC);

	if(empty($marsheet_result)) {
		$latest_marsheets = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `marsheet` WHERE `contactid` = '".$_GET['edit']."' AND `deleted` = 0 AND `year` = (SELECT `year` FROM `marsheet` WHERE `contactid` = '".$_GET['edit']."' AND `deleted` = 0 ORDER BY `year` DESC, `month` DESC LIMIT 1) AND `month` = (SELECT `month` FROM `marsheet` WHERE `contactid` = '".$_GET['edit']."' AND `deleted` = 0 ORDER BY `year` DESC, `month` DESC LIMIT 1)"),MYSQLI_ASSOC);
		if(!empty($latest_marsheets)) {
			foreach($latest_marsheets as $latest_marsheet) {
				mysqli_query($dbc, "INSERT INTO `marsheet` (`contactid`, `medicationid`, `month`, `year`, `medication_notes`, `comment`) VALUES ('".$_GET['edit']."', '".$latest_marsheet['medicationid']."', '$month', '$year', '".$latest_marsheet['medication_notes']."', '".$latest_marsheet['comment']."')");
				$marsheetid = mysqli_insert_id($dbc);

			    $row_headings = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `marsheet_row` WHERE `marsheetid` = '".$latest_marsheet['marsheetid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
			    foreach ($row_headings as $row_heading) {
			    	mysqli_query($dbc, "INSERT INTO `marsheet_row` (`marsheetid`, `heading`) VALUES ('$marsheetid', '".$row_heading['heading']."')");
			    }
			}
		} else {
			mysqli_query($dbc, "INSERT INTO `marsheet` (`contactid`, `month`, `year`) VALUES ('".$_GET['edit']."', '$month', '$year')");
			$marsheetid = mysqli_insert_id($dbc);

		    $row_headings = explode(',',get_config($dbc, "marsheet_row_headings"));
		    foreach ($row_headings as $row_heading) {
		    	mysqli_query($dbc, "INSERT INTO `marsheet_row` (`marsheetid`, `heading`) VALUES ('$marsheetid', '$row_heading')");
		    }
		}
		$marsheet_query = "SELECT * FROM `marsheet` WHERE `contactid` = '".$_GET['edit']."' AND `month` = '$month' AND `year` = '$year' AND `deleted` = 0 ORDER BY `marsheetid` ASC";
		$marsheet_result = mysqli_fetch_all(mysqli_query($dbc, $marsheet_query),MYSQLI_ASSOC);
	}

	if(!empty($marsheet_result)) {
		foreach ($marsheet_result as $marsheet) {
			$medicationid = $marsheet['medicationid'];
			$marsheetid = $marsheet['marsheetid'];
			$marsheet_medication_notes = $marsheet['medication_notes'];
			$marsheet_comment = $marsheet['comment']; ?>

			<div class="marsheet_block">
				<img class="inline-img pull-left" src="../img/remove.png" onclick="deleteMarSheet(this);"> <h4>MAR Sheet - <?= get_contact($dbc, $_GET['edit']) ?></h4>
				<input type="hidden" name="marsheetid" value="<?= $marsheetid ?>">
				<?php if(strpos($value_config, ','."Inline View".',') === FALSE) { ?>
					<a class="btn brand-btn pull-right gap-bottom add_marsheet_medication" onclick="return addMarSheetMedication(window.addMarSheetMedicationBool, this);">Add Medication</a>
					<div class="clearfix"></div>
					<?php foreach(explode(',',$medicationid) as $medid) {
						$medication_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `medication` WHERE `medicationid` = '$medid'"))['title'];
						$marsheet_medication = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `marsheet_medication` WHERE `medicationid` = '$medid'"));
						if(empty($marsheet_medication)) {
							mysqli_query($dbc, "INSERT INTO `marsheet_medication` (`contactid`, `medicationid`) VALUES ('".$_GET['edit']."', '$medid')");
							$marsheet_medication = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `marsheet_medication` WHERE `medicationid` = '$medid'"));
						} ?>
						<div class="medication_block">
							<div class="form-group">
								<label for="medication_name" class="col-sm-4 control-label"><img class="inline-img" data-medicationid="<?= $medid ?>" src="../img/remove.png" onclick="deleteMarsheetMedication(this);"> Medication:</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" value="<?= $medication_name ?>" readonly>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php if (strpos($value_config, ','."Route".',') !== FALSE) { ?>
								<div class="form-group">
									<label for="route" class="col-sm-4 control-label">Route:</label>
									<div class="col-sm-8">
										<input type="text" name="marsheet_route" data-table="marsheet_medication" data-field="route" data-row-field="marsheetmedicationid" data-row-id="<?= $marsheet_medication['marsheetmedicationid'] ?>" class="form-control" value="<?= $marsheet_medication['route'] ?>">
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Dosage".',') !== FALSE) { ?>
								<div class="form-group">
									<label for="dosage" class="col-sm-4 control-label">Dosage:</label>
									<div class="col-sm-8">
										<input type="text" name="marsheet_route" data-table="marsheet_medication" data-field="dosage" data-row-field="marsheetmedicationid" data-row-id="<?= $marsheet_medication['marsheetmedicationid'] ?>" class="form-control" value="<?= $marsheet_medication['dosage'] ?>">
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Instructions".',') !== FALSE) { ?>
								<div class="form-group">
									<label for="instructions" class="col-sm-4 control-label">Instructions:</label>
									<div class="col-sm-8">
										<textarea name="marsheet_instructions" data-table="marsheet_medication" data-field="instructions" data-row-field="marsheetmedicationid" data-row-id="<?= $marsheet_medication['marsheetmedicationid'] ?>" class="form-control"><?= html_entity_decode($marsheet_medication['instructions']) ?></textarea>
									</div>
								</div>
							<?php } ?>
							<hr>
						</div>
					<?php } ?>
				<?php } ?>
					<?php if (strpos($value_config, ','."Medication Notes".',') !== FALSE) { ?>
						<div class="form-group">
							<label for="medication_notes" class="col-sm-4 control-label">Medication Notes (Allergies or Special Instructions):</label>
							<div class="col-sm-8">
								<textarea name="marsheet_medication_notes" data-table="marsheet" data-field="medication_notes" data-row-field="marsheetid" data-row-id="<?= $marsheetid ?>" class="form-control"><?= html_entity_decode($marsheet_medication_notes) ?></textarea>
							</div>
						</div>
					<?php } ?>
					<?php if (strpos($value_config, ','."Notes".',') !== FALSE) { ?>
						<div class="form-group">
							<label for="notes" class="col-sm-4 control-label">Notes:</label>
							<div class="col-sm-8">
								<textarea name="marsheet_comment" data-table="marsheet" data-field="comment" data-row-field="marsheetid" data-row-id="<?= $marsheetid ?>" class="form-control"><?= $marsheet_comment ?></textarea>
							</div>
						</div>
					<?php } ?>
				<div class="clearfix"></div><br />
				<div class="marsheet_table_block" style="overflow-x: auto;">
					<table class="table table-bordered" style="margin-bottom: 0;">
						<div class="marsheet_legend col-sm-8 col-sm-offset-4">
							<b>R = Refused, D = Destroyed, S = Sleeping, N = Nausea/Vomiting, O = Other</b>
						</div>
						<tr>
							<?php if (strpos($value_config, ','."Inline View".',') !== FALSE) { ?>
								<th style="min-width: 30em;">Medication</th>
							<?php } ?>
							<th><?= date('F Y') ?></th>
							<?php for($day_of_month = 1; $day_of_month <= $days_in_month; $day_of_month++) { ?>
								<th><?= $day_of_month ?></th>
							<?php } ?>
						</tr>

						<?php mysqli_query($dbc, "INSERT INTO `marsheet_row` (`marsheetid`) SELECT '$marsheetid' FROM (SELECT COUNT(*) rows FROM `marsheet_row` WHERE `marsheetid` = '$marsheetid' AND `deleted` = 0) num WHERE num.rows = 0");
						$marsheet_row_query = "SELECT * FROM `marsheet_row` WHERE `marsheetid` = '$marsheetid' AND `deleted` = 0 ORDER BY `marsheetrowid` ASC";
						$marsheet_row_result = mysqli_fetch_all(mysqli_query($dbc, $marsheet_row_query),MYSQLI_ASSOC);

						$inline_added = false;
						foreach ($marsheet_row_result as $marsheet_row) {
							$marsheetrowid = $marsheet_row['marsheetrowid'];
							$heading = $marsheet_row['heading']; ?>
							<tr class="marsheet_detail_row">
								<input type="hidden" name="marsheetrowid" value="<?= $marsheetrowid ?>">
								<?php if(!$inline_added && strpos($value_config, ','."Inline View".',') !== FALSE) {
									$inline_added = true; ?>
									<td class="medication_column" data-title="Medication" rowspan="<?= count($marsheet_row_result) ?>">
										<?php foreach(explode(',',$medicationid) as $med_i => $medid) {
											$medication_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `medication` WHERE `medicationid` = '$medid'"))['title'];
											$marsheet_medication = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `marsheet_medication` WHERE `medicationid` = '$medid'"));
											if(empty($marsheet_medication)) {
												mysqli_query($dbc, "INSERT INTO `marsheet_medication` (`contactid`, `medicationid`) VALUES ('".$_GET['edit']."', '$medid')");
												$marsheet_medication = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `marsheet_medication` WHERE `medicationid` = '$medid'"));
											} ?>
											<div class="medication_block">
												<div class="form-group">
													<label for="medication_name" class="col-sm-4 control-label"><img data-medicationid="<?= $medid ?>" class="inline-img" src="../img/remove.png" onclick="deleteMarsheetMedication(this);"> Medication:</label>
													<div class="col-sm-8">
														<input type="text" class="form-control" value="<?= $medication_name ?>" readonly>
													</div>
												</div>
												<div class="clearfix"></div>
												<?php if (strpos($value_config, ','."Route".',') !== FALSE) { ?>
													<div class="form-group">
														<label for="route" class="col-sm-4 control-label">Route:</label>
														<div class="col-sm-8">
															<input type="text" name="marsheet_route" data-table="marsheet_medication" data-field="route" data-row-field="marsheetmedicationid" data-row-id="<?= $marsheet_medication['marsheetmedicationid'] ?>" class="form-control" value="<?= $marsheet_medication['route'] ?>">
														</div>
													</div>
												<?php } ?>
												<?php if (strpos($value_config, ','."Dosage".',') !== FALSE) { ?>
													<div class="form-group">
														<label for="dosage" class="col-sm-4 control-label">Dosage:</label>
														<div class="col-sm-8">
															<input type="text" name="marsheet_route" data-table="marsheet_medication" data-field="dosage" data-row-field="marsheetmedicationid" data-row-id="<?= $marsheet_medication['marsheetmedicationid'] ?>" class="form-control" value="<?= $marsheet_medication['dosage'] ?>">
														</div>
													</div>
												<?php } ?>
												<?php if (strpos($value_config, ','."Instructions".',') !== FALSE) { ?>
													<div class="form-group">
														<label for="instructions" class="col-sm-4 control-label">Instructions:</label>
														<div class="col-sm-8">
															<textarea name="marsheet_instructions" data-table="marsheet_medication" data-field="instructions" data-row-field="marsheetmedicationid" data-row-id="<?= $marsheet_medication['marsheetmedicationid'] ?>" class="noMceEditor form-control"><?= strip_tags(html_entity_decode($marsheet_medication['instructions'])) ?></textarea>
														</div>
													</div>
												<?php } ?>
												<?php if($med_i < (count(explode(',',$medicationid)) - 1)) {
													echo '<hr>';
												} ?>
											</div>
										<?php } ?>
										<img class="inline-img pull-right add_marsheet_medication" title="Add Medication" src="../img/icons/ROOK-add-icon.png" onclick="return addMarSheetMedication(window.addMarSheetMedicationBool, this);">
									</td>
								<?php } ?>
								<td data-title="Heading" style="min-width: 20em;">
									<input type="text" name="marsheet_row" data-table="marsheet_row" data-field="heading" data-row-field="marsheetrowid" data-row-id="<?= $marsheetrowid ?>" data-no-contactid="true" class="form-control" value="<?= $heading ?>" style="display: inline; width: calc(100% - 2.0em);">
									<!-- <img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addMarSheetDetailRow();"> -->
									<img class="inline-img pull-right" src="../img/remove.png" onclick="deleteMarSheetRow(this);">
								</td>
								<?php for($day_of_month = 1; $day_of_month <= $days_in_month; $day_of_month++) { ?>
									<td data-title="<?= $day_of_month ?>" style="min-width: 60px;">
										<input type="text" name="marsheet_row_detail" data-table="marsheet_row" data-field="day_<?= $day_of_month ?>" data-row-field="marsheetrowid" data-row-id="<?= $marsheetrowid ?>" data-no-contactid="true" class="form-control" value="<?= $marsheet_row['day_'.$day_of_month] ?>">
									</td>
								<?php } ?>
							</tr>
						<?php } ?>
					</table>
					<img class="inline-img pull-left" title="Add Row" src="../img/icons/ROOK-add-icon.png" onclick="addMarSheetRow(this);">
				</div>
			</div>
		<?php }
	} else {
		echo '<h1>No MAR Sheet Found</h1>';
	}
} else {
	echo '<h1>No Client Selected</h1>';
} ?>