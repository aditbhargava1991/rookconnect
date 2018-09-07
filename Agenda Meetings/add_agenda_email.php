This Agenda will be emailed to the selected contacts when you Save or Approve the Agenda.
<?php if (strpos($value_config, ','."Email to all Contact Attendees".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right"><?php echo (strpos($value_config, ','."Business".',') === FALSE ? 'Contacts' : BUSINESS_CAT.' Contacts'); ?>:</label>
    <div class="col-sm-8">
        <select name="agenda_email_business[]" multiple data-placeholder="Select Contacts" class="chosen-select-deselect form-control" width="380">
            <?php
            $cat = '';
			$cat_list = [];
			$this_list = [];
            $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE IFNULL(businessid,0) IN ('$businessid', '', '0') AND `deleted`=0 AND `status`=1 AND `category` NOT IN ('".BUSINESS_CAT."',".STAFF_CATS.",'Sites') ORDER BY category");
            while($row = mysqli_fetch_array($query)) {
                if($cat != $row['category']) {
					$cat_list[$cat] = sort_contacts_array($this_list);
                    $cat = $row['category'];
					$this_list = [];
                }
                if($row['email_address'] != '') {
                    $this_list[] = [ 'contactid' => $row['contactid'], 'last_name' => $row['last_name'], 'first_name' => $row['first_name'] ];
                }
            }
			$cat_list[$cat] = sort_contacts_array($this_list);
			foreach($cat_list as $cat => $id_list) {
				echo '<optgroup label="'.$cat.'">';
				foreach($id_list as $id) {
					//$email = get_email($dbc, $id);
                    $email = explode(',',get_multiple_email($dbc, $id));
                    foreach($email as $each_email) {
                        if($each_email != '') {
                            echo "<option data-id='".$id."' value='". $each_email."'>".get_contact($dbc, $id).' : '.$each_email.'</option>';
                        }
                    }
				}
			} ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Email to all Company Attendees".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Company Contact:</label>
    <div class="col-sm-8">
        <select name="agenda_email_company[]" multiple data-placeholder="Select Staff" class="chosen-select-deselect form-control" width="380">
            <?php $query1 = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
			foreach($query1 as $id) {
				$email = explode(',',get_multiple_email($dbc, $id));
				foreach($email as $each_email) {
                    if($each_email != '') {
                        echo "<option data-id='".$id."' value='". $each_email."'>".get_contact($dbc, $id).' : '.$each_email.'</option>';
                    }
                }
			} ?>
        </select>
    </div>
</div>

<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Additional Email(s):</label>
    <div class="col-sm-8">
        <input type="text" name="agenda_additional_email" class="form-control">
    </div>
</div>
<?php } ?>


<?php
$sender = get_contact($dbc, $_SESSION['contactid'], 'email_address');
if($get_field_config['email_subject'] == '') {
	$subject = 'Agenda of Meeting'.($date_of_meeting != '' ? ' on '.$date_of_meeting : '');
} else {
	$subject = str_replace(['[Business]','[Date]','[Start]','[End]','[Location]'],
		[$business, $date_of_meeting, $time_of_meeting, $end_time_of_meeting, $location],
		$get_field_config['email_subject']);
}
?>
<div class="form-group">
	<label class="col-sm-4 control-label">Sending Email Name:</label>
	<div class="col-sm-8">
		<input type="text" name="email_name" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Sending Email Address:</label>
	<div class="col-sm-8">
		<input type="text" name="email_sender" class="form-control" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Email Subject:</label>
	<div class="col-sm-8">
		<input type="text" name="email_subject" class="form-control" value="<?php echo $subject; ?>">
	</div>
</div>

<div class="form-group clearfix">
	<button class="btn brand-btn pull-right" onclick="choose_all_emails();return false;">Select All Attendees</button>
</div>
<script>
function choose_all_emails() {
	$('[name="businesscontactid[]"] option:selected,[name="companycontactid[]"] option:selected').each(function() {
		var id = $(this).val();
		$('[name="agenda_email_business[]"] option[data-id='+id+']').attr('selected',true);
		$('[name="agenda_email_business[]"]').trigger('change.select2');
		$('[name="agenda_email_company[]"] option[data-id='+id+']').attr('selected',true);
		$('[name="agenda_email_company[]"]').trigger('change.select2');
	});
}
</script>