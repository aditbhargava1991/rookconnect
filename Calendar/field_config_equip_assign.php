<?php
if (isset($_POST['add_equip_assign'])) {
	$equipment_category = filter_var(implode(',',$_POST['equipment_category']), FILTER_SANITIZE_STRING);
    $client_type = filter_var($_POST['client_type'], FILTER_SANITIZE_STRING);
    $contact_category = filter_var(implode(',', $_POST['contact_category']),FILTER_SANITIZE_STRING);
    $contractor_category = filter_var(implode(',', $_POST['contractor_category']),FILTER_SANITIZE_STRING);
    $position_enabled = filter_var($_POST['position_enabled']);
    $enabled_fields = implode(',', $_POST['enabled_fields']);

    mysqli_query($dbc, "INSERT INTO `field_config_equip_assign` (`equipment_category`, `client_type`, `contact_category`,`contractor_category`,`position_enabled`,`enabled_fields`) SELECT '','','','','','' FROM (SELECT COUNT(*) rows FROM `field_config_equip_assign`) num WHERE num.rows=0");
    mysqli_query($dbc, "UPDATE `field_config_equip_assign` SET `equipment_category` = '$equipment_category', `client_type` = '$client_type', `contact_category` = '$contact_category', `contractor_category` = '$contractor_category', `position_enabled` = '$position_enabled', `enabled_fields` = '$enabled_fields'");
    
    set_config($dbc, 'equip_options', implode(',', $_POST['equip_options']));
}
?>
<script type="text/javascript">
function addContactCategory() {
    var block = $('div.contact-category').last();
    destroyInputs('div.contact-category');
    clone = block.clone();

    clone.find('.form-control').val('');

    block.after(clone);
    initInputs('div.contact-category');
}
function deleteContactCategory(button) {
    if($('div.contact-category').length <= 1) {
        addContactCategory();
    }
    $(button).closest('div.contact-category').remove();
}
function addContractorCategory() {
    var block = $('div.contractor-category').last();
    destroyInputs('div.contractor-category');
    clone = block.clone();

    clone.find('.form-control').val('');

    block.after(clone);
    initInputs('div.contractor-category');
}
function deleteContractorCategory(button) {
    if($('div.contractor-category').length <= 1) {
        addContractorCategory();
    }
    $(button).closest('div.contractor-category').remove();
}
</script>
<?php
$equipment_category = '';
$client_type = '';
$contact_category = '';
$contractor_category = '';
$position_enabled = '';
$enabled_fields = '';
$equip_options = '';

$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"));
if (!empty($get_field_config)) {
    $equipment_category = $get_field_config['equipment_category'];
    $client_type = $get_field_config['client_type'];
    $contact_category = explode(',', $get_field_config['contact_category']);
    $contractor_category = explode(',', $get_field_config['contractor_category']);
    $position_enabled = $get_field_config['position_enabled'];
    $enabled_fields = ','.$get_field_config['enabled_fields'].',';
    $equip_options = ','.get_config($dbc,'equip_options').',';
}
?>
<h3>Equipment</h3>
<div class="panel-group" id="accordion2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equip">Equipment Assignment Settings</a>
            </h4>
        </div>
        <div id="collapse_equip" class="panel-collapse collapse">
            <div class="panel-body">
            	<div class="form-group">
	            	<label for="equipment_category" class="col-sm-4 control-label">Equipment Category:</label>
	            	<div class="col-sm-8">
		            	<select data-placeholder="Select Equipment" name="equipment_category[]" multiple class="chosen-select-deselect form-control">
		            		<option></option>
		            		<?php
		            		$equip_categories = get_config($dbc, 'equipment_tabs');
		            		$equip_categories = explode(',', $equip_categories);
		            		asort($equip_categories);
		            		foreach($equip_categories as $equip_category) {
		            			echo '<option value="'.$equip_category.'"'.(strpos(','.$equipment_category.',', ','.$equip_category.',') !== FALSE ? ' selected' : '').'>'.$equip_category.'</option>';
		            		}
		            		?>
		            	</select>
	            	</div>
            	</div>
                <div class="form-group">
                    <label for="client_type" class="col-sm-4 control-label">Client Type:</label>
                    <div class="col-sm-8">
                        <select name="client_type" data-placeholder="Select Client Type" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php
                            $query = "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`";
                            $result = mysqli_query($dbc, $query);
                            while ($row = mysqli_fetch_array($result)) {
                                echo '<option value="'.$row['category'].'"'.($row['category'] == $client_type ? ' selected' : '').'>'.$row['category'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="position_enabled" class="col-sm-4 control-label">Include Position Dropdown:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="position_enabled" value="1" <?= ($position_enabled == 1 ? 'checked' : '') ?>></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_contact_cat">Equipment Assignment Contact Category Settings</a>
            </h4>
        </div>
        <div id="collapse_contact_cat" class="panel-collapse collapse">
            <div class="panel-body">
                <?php for ($i = 0; $i < count($contact_category); $i++) { ?>
                    <div class="form-group contact-category">
                        <label for="contact_category" class="col-sm-4 control-label">Contact Category:</label>
                        <div class="col-sm-7">
                            <select data-placeholder="Select Category" name="contact_category[]" class="chosen-select-deselect form-control">
                                <option></option>
                                <?php
                                $query = "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1";
                                $result = mysqli_query($dbc, $query);
                                while ($row = mysqli_fetch_array($result)) {
                                    echo '<option value="'.$row['category'].'"'.($row['category'] == $contact_category[$i] ? ' selected' : '').'>'.$row['category'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-1 pull-right">
                            <img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addContactCategory();">
                            <img src="../img/remove.png" class="inline-img pull-right" onclick="deleteContactCategory(this);">
                        </div>
                    </div>
                <?php } ?>
                <?php for ($i = 0; $i < count($contractor_category); $i++) { ?>
                    <div class="form-group contractor-category">
                        <label for="contractor_category" class="col-sm-4 control-label">Contractor Category:</label>
                        <div class="col-sm-7">
                            <select data-placeholder="Select Category" name="contractor_category[]" class="chosen-select-deselect form-control">
                                <option></option>
                                <?php
                                $query = "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 AND `tile_name` = 'vendors' ORDER BY `category`";
                                $result = mysqli_query($dbc, $query);
                                while ($row = mysqli_fetch_array($result)) {
                                    echo '<option value="'.$row['category'].'"'.($row['category'] == $contractor_category[$i] ? ' selected' : '').'>'.$row['category'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-1 pull-right">
                            <img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addContractorCategory();">
                            <img src="../img/remove.png" class="inline-img pull-right" onclick="deleteContractorCategory(this);">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field">Equipment Assignment Field Settings</a>
            </h4>
        </div>
        <div id="collapse_field" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="region" class="col-sm-4 control-label">Team:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="team" <?= (strpos($enabled_fields, ',team,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="region" class="col-sm-4 control-label">Region:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="region" <?= (strpos($enabled_fields, ',region,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="location" class="col-sm-4 control-label">Location:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="location" <?= (strpos($enabled_fields, ',location,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="classification" class="col-sm-4 control-label">Classification:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="classification" <?= (strpos($enabled_fields, ',classification,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="start_date" class="col-sm-4 control-label">Start Date:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="start_date" <?= (strpos($enabled_fields, ',start_date,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="end_date" class="col-sm-4 control-label">End Date:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="end_date" <?= (strpos($enabled_fields, ',end_date,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes" class="col-sm-4 control-label">Notes:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="notes" <?= (strpos($enabled_fields, ',notes,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_display">Equipment Display Options</a>
            </h4>
        </div>
        <div id="collapse_display" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="region" class="col-sm-4 control-label">Sort by Region:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="equip_options[]" value="region_sort" <?= (strpos($equip_options, ',region_sort,') !== FALSE ? 'checked' : '') ?>></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group clearfix">
	<div class="col-sm-6">
		<a href="calendars.php" class="btn brand-btn btn-lg">Back</a>
	</div>
	<div class="col-sm-6">
		<button	type="submit" name="add_equip_assign" value="add_equip_assign" class="btn brand-btn btn-lg pull-right">Submit</button>
	</div>
</div>