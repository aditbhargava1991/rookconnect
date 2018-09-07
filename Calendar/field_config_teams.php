<?php
if (isset($_POST['add_teams'])) {
    $contact_category = filter_var(implode(',', $_POST['contact_category']),FILTER_SANITIZE_STRING);
    $position_enabled = filter_var($_POST['position_enabled'],FILTER_SANITIZE_STRING);
    $team_fields = implode(',', $_POST['team_fields']);

    mysqli_query($dbc, "INSERT INTO `field_config_teams` (`contact_category`,`position_enabled`,`team_fields`) SELECT '','','' FROM (SELECT COUNT(*) rows FROM `field_config_teams`) num WHERE num.rows=0");
    mysqli_query($dbc, "UPDATE `field_config_teams` SET `contact_category` = '$contact_category', `position_enabled` = '$position_enabled', `team_fields` = '$team_fields'");
}
?>
<script type="text/javascript">
function addContactCategory() {
    var block = $('div.contact-category').last();
    clone = block.clone();

    clone.find('.form-control').val('');
    resetChosen(clone.find('select'));

    block.after(clone);
}
function deleteContactCategory(button) {
    if($('div.contact-category').length <= 1) {
        addContactCategory();
    }
    $(button).closest('div.contact-category').remove();
}
</script>
<?php
$contact_category = '';
$position_enabled = '';
$team_fields = '';

$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_teams`"));
if (!empty($get_field_config)) {
    $contact_category = explode(',', $get_field_config['contact_category']);
    $position_enabled = $get_field_config['position_enabled'];
    $team_fields = ','.$get_field_config['team_fields'].',';
}
?>
<h3>Teams</h3>
<div class="panel-group" id="accordion2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_contacts">Contact Settings</a>
            </h4>
        </div>
        <div id="collapse_contacts" class="panel-collapse collapse">
            <div class="panel-body">
            <?php for ($i = 0; $i < count($contact_category); $i++) { ?>
                <div class="form-group contact-category">
                    <label for="contact_category" class="col-sm-4 control-label">Contact Category:</label>
                    <div class="col-sm-7">
                        <select data-placeholder="Select Category" name="contact_category[]" class="chosen-select-deselect form-control">
                            <option value="no_cat">No Category</option>
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
                <div class="form-group">
                    <label for="position_enabled" class="col-sm-4 control-label">Include Position Dropdown:</label>
                    <div class="col-sm-8">
                        <input type="checkbox" name="position_enabled" value="1" <?= ($position_enabled == 1 ? 'checked' : '') ?>>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field">Field Settings</a>
            </h4>
        </div>
        <div id="collapse_field" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="region" class="col-sm-4 control-label">Team Name:</label>
                    <div class="col-sm-8">
                        <input type="checkbox" name="team_fields[]" value="team_name" <?= (strpos($team_fields, ',team_name,') !== FALSE ? 'checked' : '') ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="region" class="col-sm-4 control-label">Region:</label>
                    <div class="col-sm-8">
                        <input type="checkbox" name="team_fields[]" value="region" <?= (strpos($team_fields, ',region,') !== FALSE ? 'checked' : '') ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="location" class="col-sm-4 control-label">Location:</label>
                    <div class="col-sm-8">
                        <input type="checkbox" name="team_fields[]" value="location" <?= (strpos($team_fields, ',location,') !== FALSE ? 'checked' : '') ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="classification" class="col-sm-4 control-label">Classification:</label>
                    <div class="col-sm-8">
                        <input type="checkbox" name="team_fields[]" value="classification" <?= (strpos($team_fields, ',classification,') !== FALSE ? 'checked' : '') ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="start_date" class="col-sm-4 control-label">Start Date:</label>
                    <div class="col-sm-8">
                        <input type="checkbox" name="team_fields[]" value="start_date" <?= (strpos($team_fields, ',start_date,') !== FALSE ? 'checked' : '') ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="end_date" class="col-sm-4 control-label">End Date:</label>
                    <div class="col-sm-8">
                        <input type="checkbox" name="team_fields[]" value="end_date" <?= (strpos($team_fields, ',end_date,') !== FALSE ? 'checked' : '') ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes" class="col-sm-4 control-label">Notes:</label>
                    <div class="col-sm-8">
                        <input type="checkbox" name="team_fields[]" value="notes" <?= (strpos($team_fields, ',notes,') !== FALSE ? 'checked' : '') ?>>
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
		<button	type="submit" name="add_teams" value="add_teams" class="btn brand-btn btn-lg pull-right">Submit</button>
	</div>
</div>