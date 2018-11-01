<?php include_once('../include.php');
checkAuthorised('certificate');
$access = get_security($dbc, 'certificate'); ?>
<script>
function add_row_doc() {
	var link = $('.additional_doc').last();
	var clone = link.clone();
	clone.find('input').val('');
	link.after(clone);
	setSave();
}
function add_row_link() {
	var link = $('.additional_link').last();
	var clone = link.clone();
	clone.find('input').val('');
	link.after(clone);
	setSave();
}
function setSave() {
	$('.content-block input,.content-block select,.content-block textarea').off('focus',unsaved).focus(unsaved).off('blur',unsaved).blur(unsaved).off('change', saveField).change(saveField);
}
function saveFieldMethod(field) {
    if(field.value == "MANUAL") {
      $('input[name="'+field.name+'"]').closest('.form-group').show();
      $('input[name="'+field.name+'"]').focus();
	  doneSaving();
    } else if(field.type == 'file') {
      var file = new FormData();
      var file_data = field.files[0];
      file.append('file',field.files[0]);
      file.append('table',$(field).data('table'));
      file.append('attached',$(field).data('attach-id'));
      file.append('attach_field',$(field).data('attach-field'));
      $.ajax({
        url: 'certificate_ajax.php?action=add_file',
        method: 'POST',
        processData: false,
        contentType: false,
        data: file,
        success: function(response) {
          console.log(response);
          doneSaving();
        }
      });
    } else {
      var input = field;
      var table = $(field).data('table');
      var id = $(field).data('id');
      var id_field = $(field).data('id-field');
      var attached = $(field).data('attach-id');
      var attach_field = $(field).data('attach-field');
      if(field.name.substr(-2) == '[]' && field.type == 'select-multiple') {
        var field_name = field.name.split('[]')[0];
        var values = [];
        $('[name="'+field.name+'"] option:selected').each(function() {
          values.push(this.value);
        });
        var value = values.join(',');
      } else {
        var field_name = field.name;
        var value = field.value;
      }
      var new_value = $(field).data('new');
      $.ajax({
        url: 'certificate_ajax.php?action=update_field',
        method: 'POST',
        data: {
          table: table,
          field: field_name,
          id: id,
          id_field: id_field,
          attached: attached,
          attach_field: attach_field,
          value: value,
          new_value: new_value
        },
        success: function(response) {
          if(response > 0 && table == 'certificate') {
            $('[data-table="'+table+'"][data-id]').data('id',response);
			window.history.replaceState('',"Software", window.location.href.replace('edit=0','edit='+response));
          } else if(response > 0) {
            $(input).data('id',response);
          }
          doneSaving();
        }
      });
    }
}
$(document).ready(function() {
	setSave();
});
</script>
<?php $value_config = get_field_config($dbc, 'certificate');
$contactid = (!empty($_GET['staffid']) ? $_GET['staffid'] : '');
$jobid = '';
$projectid = '';
$client_projectid = '';
$certificate_type = '';
$category = '';
$certificate_code = '';
$heading = '';
$cost = '';
$description = '';
$quote_description = '';
$invoice_description = '';
$ticket_description = '';

$final_retail_price = '';
$name = '';
$fee = '';
$admin_price = '';
$wholesale_price = '';
$commercial_price = '';
$client_price = '';
$minimum_billable = '';
$estimated_hours = '';
$actual_hours = '';
$msrp = '';

$unit_price = '';
$unit_cost = '';
$rent_price = '';
$rental_days = '';
$rental_weeks = '';
$rental_months = '';
$rental_years = '';
$certificate_reminder = '';
$reminder_alert = '';
$daily = '';
$weekly = '';
$monthly = '';
$annually = '';
$total_days = '';
$total_hours = '';
$total_km = '';
$total_miles = '';
$title = '';

$issue_date = '';
$reminder_date = '';
$expiry_date = '';

if(!empty($_GET['edit'])) {

	$certificateid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
	$get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM certificate WHERE certificateid='$certificateid'"));
	$contactid = $get_contact['contactid'];
	$jobid = $get_contact['jobid'];
	$projectid = $get_contact['projectid'];
	$client_projectid = $get_contact['client_projectid'];
	$certificate_type = $get_contact['certificate_type'];
	$category = $get_contact['category'];
	$certificate_code = $get_contact['certificate_code'];
	$heading = $get_contact['heading'];
	$cost = $get_contact['cost'];
	$description = $get_contact['description'];
	$quote_description = $get_contact['quote_description'];
	$invoice_description = $get_contact['invoice_description'];
	$ticket_description = $get_contact['ticket_description'];
	$name = $get_contact['name'];
	$title = $get_contact['title'];
	$fee = $get_contact['fee'];

	$issue_date = $get_contact['issue_date'];
	$reminder_date = $get_contact['reminder_date'];
	$expiry_date = $get_contact['expiry_date'];

	$final_retail_price = $get_contact['final_retail_price'];
	$admin_price = $get_contact['admin_price'];
	$wholesale_price = $get_contact['wholesale_price'];
	$commercial_price = $get_contact['commercial_price'];
	$client_price = $get_contact['client_price'];
	$minimum_billable = $get_contact['minimum_billable'];
	$estimated_hours = $get_contact['estimated_hours'];
	$actual_hours = $get_contact['actual_hours'];
	$msrp = $get_contact['msrp'];

	$unit_price = $get_contact['unit_price'];
	$unit_cost = $get_contact['unit_cost'];
	$rent_price = $get_contact['rent_price'];
	$rental_days = $get_contact['rental_days'];
	$rental_weeks = $get_contact['rental_weeks'];
	$rental_months = $get_contact['rental_months'];
	$rental_years = $get_contact['rental_years'];
	$certificate_reminder = $get_contact['certificate_reminder'];
	$reminder_alert = $get_contact['reminder_alert'];
	$daily = $get_contact['daily'];
	$weekly = $get_contact['weekly'];
	$monthly = $get_contact['monthly'];
	$annually = $get_contact['annually'];
	$total_days = $get_contact['total_days'];
	$total_hours = $get_contact['total_hours'];
	$total_km = $get_contact['total_km'];
	$total_miles = $get_contact['total_miles']; ?>
	<input type="hidden" id="certificateid" name="certificateid" value="<?= $certificateid ?>" />
<?php } else if(isset($_GET['clientprojectid'])) {
	$client_projectid = $_GET['clientprojectid'];
} else if(isset($_GET['projectid'])) {
	$projectid = $_GET['projectid'];
} else if(isset($_GET['jobid'])) {
	$jobid = $_GET['jobid'];
} ?>
<div class="content-block main-screen-white">
	<h3>Certificate Information</h3>
        <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
        <div class="form-group">
          <label for="site_name" class="col-sm-4 control-label">Staff:</label>
          <div class="col-sm-8">
            <select data-placeholder="Select a Staff Member..." name="contactid" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="chosen-select-deselect form-control">
              <option value=""></option>
              <?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND status = 1"),MYSQLI_ASSOC));
				foreach($query as $id) {
					echo "<option ".($contactid == $id ? 'selected' : '')." value='". $id."'>".get_contact($dbc, $id).'</option>';
				} ?>
            </select>
          </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Projects".',') !== FALSE) { ?>
        <div class="form-group">
          <label for="site_name" class="col-sm-4 control-label">Project:</label>
          <div class="col-sm-8">
            <select data-placeholder="Select a Project..." name="projectid" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="chosen-select-deselect form-control">
              <option value=""></option>
              <?php $query = mysqli_query($dbc,"SELECT `projectid`, `project_name` FROM `project` WHERE `deleted`=0");
				while($row = mysqli_fetch_array($query)) {
					echo "<option ".($projectid == $row['projectid'] ? 'selected' : '')." value='". $row['projectid']."'>Project #".$row['projectid'].': '.$row['project_name'].'</option>';
				} ?>
            </select>
          </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Project".',') !== FALSE) { ?>
        <div class="form-group">
          <label for="site_name" class="col-sm-4 control-label">Client Project:</label>
          <div class="col-sm-8">
            <select data-placeholder="Select a Client Project..." name="client_projectid" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="chosen-select-deselect form-control">
              <option value=""></option>
              <?php $query = mysqli_query($dbc,"SELECT `projectid`, `project_name` FROM `client_project` WHERE `deleted`=0");
				while($row = mysqli_fetch_array($query)) {
					echo "<option ".($client_projectid == $row['projectid'] ? 'selected' : '')." value='". $row['projectid']."'>Project #".$row['projectid'].': '.$row['project_name'].'</option>';
				} ?>
            </select>
          </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Jobs".',') !== FALSE) { ?>
        <div class="form-group">
          <label for="site_name" class="col-sm-4 control-label">Job:</label>
          <div class="col-sm-8">
            <select data-placeholder="Select a Job..." name="jobid" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="chosen-select-deselect form-control">
              <option value=""></option>
              <?php $query = mysqli_query($dbc,"SELECT `projectid`, `project_name` FROM `jobs` WHERE `deleted`=0");
				while($row = mysqli_fetch_array($query)) {
					echo "<option ".($jobid == $row['projectid'] ? 'selected' : '')." value='". $row['projectid']."'>Project #".$row['projectid'].': '.$row['project_name'].'</option>';
				} ?>
            </select>
          </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Certificate Type".',') !== FALSE) { ?>
       <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Certificate Type<span class="hp-red">*</span>:</label>
        <div class="col-sm-8">
            <select id="certificate_type" name="certificate_type" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="chosen-select-deselect form-control">
                <option></option>
                <?php $certificate_types = array_filter(explode('#*#',get_config($dbc, 'certificate_types')));
                foreach($certificate_types as $row) {
                  echo '<option value="'.$row.'" '.($row == $certificate_type ? 'selected' : '' ).'>'.$row.'</option>';
                }
                echo "<option value = 'MANUAL'>New Certificate Type</option>";
                ?>
            </select>
        </div>
      </div>

       <div class="form-group" id="new_certificate" style="display: none;">
        <label for="travel_task" class="col-sm-4 control-label">New Certificate Type:
        </label>
        <div class="col-sm-8">
            <input name="certificate_type" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" data-new="1" class="form-control" />
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
       <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Category<span class="hp-red">*</span>:</label>
        <div class="col-sm-8">
            <select id="category" name="category" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="chosen-select-deselect form-control">
                <option value=''></option>
                <?php $certificate_categories = array_filter(explode('#*#',get_config($dbc, 'certificate_categories')));
                foreach($certificate_categories as $row) {
                  echo '<option value="'.$row.'" '.($row == $category ? 'selected' : '' ).'>'.$row.'</option>';
                }
                echo "<option value = 'MANUAL'>New Category</option>";
                ?>
            </select>
        </div>
      </div>

       <div class="form-group" id="new_category" style="display: none;">
        <label for="travel_task" class="col-sm-4 control-label">New Category:
        </label>
        <div class="col-sm-8">
            <input name="category" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" data-new="1" class="form-control" />
        </div>
      </div>

      <?php } ?>

       <?php if (strpos($value_config, ','."Title".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Title<span class="hp-red">*</span>:</label>
        <div class="col-sm-8">
          <input name="title" value="<?php echo $title; ?>" type="text" id="title" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

       <?php if (strpos($value_config, ','."Issue Date".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Issue Date:</label>
        <div class="col-sm-8">
          <input name="issue_date" value="<?php echo $issue_date; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="datepicker form-control">
        </div>
      </div>
      <?php } ?>

       <?php if (strpos($value_config, ','."Expiry Date".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Expiry Date:</label>
        <div class="col-sm-8">
          <input name="expiry_date" value="<?php echo $expiry_date; ?>" id="expiry_date" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="datepicker form-control">
        </div>
      </div>
      <?php } ?>

       <?php if (strpos($value_config, ','."Reminder Date".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Reminder Date:</label>
        <div class="col-sm-8">
          <input name="reminder_date" id="reminder_date" value="<?php echo $reminder_date; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="datepicker form-control">
        </div>
      </div>
      <?php } ?>
      <?php if (strpos($value_config, ','."Certificate Reminder Email".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Certificate Reminder Email:</label>
        <div class="col-sm-8">
          <select name="certificate_reminder[]" multiple data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control chosen-select-deselect">
        <?php $staff_result = sort_contacts_query(mysqli_query($dbc, "select first_name, last_name, name, contactid from contacts where category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
        foreach($staff_result as $row) {
          if($row['full_name'] != '-' && !empty($row['full_name'])) {
            echo "<option ".(strpos(','.$certificate_reminder.',', ','.$row['contactid'].',') !== FALSE ? "selected " : "")."value='{$row['contactid']}'>".$row['full_name']."</option>";
          }
        }
        ?>
        </select>
        </div>
      </div>
      <?php } ?>

    <?php if (strpos($value_config, ','."Uploader".',') !== FALSE) {
    ?>

    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span>
			Upload Document(s):
        </label>
        <div class="col-sm-8">
            <?php
            if($certificateid > 0) {
                $query_check_credentials = "SELECT * FROM certificate_uploads WHERE certificateid='$certificateid' AND type = 'Document' AND `deleted`=0 ORDER BY certuploadid DESC";
                $result = mysqli_query($dbc, $query_check_credentials);
                $num_rows = mysqli_num_rows($result);
                if($num_rows > 0) {
					echo '<ul>';
                    while($row = mysqli_fetch_array($result)) {
                        $certuploadid = $row['certuploadid'];
                        echo '<li><a href="download/'.$row['document_link'].'" target="_blank">'.$row['document_link'].'</a> - <a href="archive_document" onclick="$(this).closest(\'li\').hide().find(\'input\').val(1).change();return false;">Archive</a>
							<input type="hidden" name="deleted" data-table="certificate_uploads" data-id="'.$row['certuploadid'].'" data-id-field="certuploadid" value="0"></li>';
                    }
					echo '</ul>';
                }
            }
            ?>
            <div class="enter_cost additional_doc clearfix">
                <div class="form-group clearfix col-sm-11">
					<input name="document_link" type="file" data-filename-placement="inside" data-table="certificate_uploads" data-id="" data-id-field="certuploadid" data-attach-id="<?= $certificateid ?>" data-attach-field="certificateid" class="form-control" />
                </div>
				<div class="col-sm-1"><img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_row_doc();"></div>
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
            if($certificateid > 0) {
                $query_check_credentials = "SELECT * FROM certificate_uploads WHERE certificateid='$certificateid' AND type = 'Link' AND `deleted`=0 ORDER BY certuploadid DESC";
                $result = mysqli_query($dbc, $query_check_credentials);
                $num_rows = mysqli_num_rows($result);
                if($num_rows > 0) {
                    $link_no = 1;
					echo '<ul>';
                    while($row = mysqli_fetch_array($result)) {
                        $certuploadid = $row['certuploadid'];
                        echo '<li><a target="_blank" href=\''.$row['document_link'].'\'">'.$row['document_link'].'</a> - <a href="archive_link" onclick="$(this).closest(\'li\').hide().find(\'input\').val(1).change();return false;">Archive</a>
							<input type="hidden" name="deleted" data-table="certificate_uploads" data-id="'.$row['certuploadid'].'" data-id-field="certuploadid" value="0"></li>';
                    }
					echo '</ul>';
                }
            }
            ?>
            <div class="enter_cost additional_link clearfix">
                <div class="form-group clearfix col-sm-11">
					<input name="document_link" type="text" data-table="certificate_uploads" data-id="" data-id-field="certuploadid" data-attach-id="<?= $certificateid ?>" data-attach-field="certificateid" class="form-control">
                </div>
				<div class="col-sm-1"><img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_row_link();"></div>
            </div>
        </div>
    </div>
    <?php } ?>

       <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Heading:</label>
        <div class="col-sm-8">
          <input name="heading" value="<?php echo $heading; ?>" type="text" id="name" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

       <?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Name<span class="hp-red">*</span>:</label>
        <div class="col-sm-8">
          <input name="name" value="<?php echo $name; ?>" type="text" id="name" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Certificate Code".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Certificate Code:</label>
        <div class="col-sm-8">
          <input name="certificate_code" value="<?php echo $certificate_code; ?>" type="text" id="name" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Description".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label">Description:</label>
        <div class="col-sm-8">
          <textarea name="description" rows="5" cols="50" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control"><?php echo $description; ?></textarea>
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label"></label>
        <div class="col-sm-8">
          <label class="form-checkbox"><input type="checkbox" value="1" name="same_desc">Check this if Quote Description is same as Description.</label>
        </div>
      </div>

      <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label">Quote Description:</label>
        <div class="col-sm-8">
          <textarea name="quote_description" rows="5" cols="50" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control"><?php echo $quote_description; ?></textarea>
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Invoice Description:</label>
        <div class="col-sm-8">
          <textarea name="invoice_description" rows="5" cols="50" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control"><?php echo $invoice_description; ?></textarea>
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label"><?= TICKET_NOUN ?> Description:</label>
        <div class="col-sm-8">
          <textarea name="ticket_description" rows="5" cols="50" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control"><?php echo $ticket_description; ?></textarea>
        </div>
      </div>
      <?php } ?>

       <?php if (strpos($value_config, ','."Fee".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Fee<span class="hp-red">*</span>:</label>
        <div class="col-sm-8">
          <input name="fee" value="<?php echo $fee; ?>" type="text" id="name" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Cost:</label>
        <div class="col-sm-8">
          <input name="cost" value="<?php echo $cost; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Final Retail Price:</label>
        <div class="col-sm-8">
          <input name="final_retail_price" value="<?php echo $final_retail_price; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

        <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Admin Price:</label>
        <div class="col-sm-8">
          <input name="admin_price" value="<?php echo $admin_price; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Wholesale Price:</label>
        <div class="col-sm-8">
          <input name="wholesale_price" value="<?php echo $wholesale_price; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Commercial Price:</label>
        <div class="col-sm-8">
          <input name="commercial_price" value="<?php echo $commercial_price; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Client Price:</label>
        <div class="col-sm-8">
          <input name="client_price" value="<?php echo $client_price; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Minimum Billable:</label>
        <div class="col-sm-8">
          <input name="minimum_billable" value="<?php echo $minimum_billable; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Estimated Hours:</label>
        <div class="col-sm-8">
          <input name="estimated_hours" value="<?php echo $estimated_hours; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Actual Hours:</label>
        <div class="col-sm-8">
          <input name="actual_hours" value="<?php echo $actual_hours; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">MSRP:</label>
        <div class="col-sm-8">
          <input name="msrp" value="<?php echo $msrp; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
        </div>
      </div>
      <?php } ?>

    <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Unit Price:</label>
    <div class="col-sm-8">
      <input name="unit_price" value="<?php echo $unit_price; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
    </div>
    <?php } ?>

    <?php if (strpos($value_config, ','."Unit Cost".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Unit Cost:</label>
    <div class="col-sm-8">
      <input name="unit_cost" value="<?php echo $unit_cost; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
    </div>
    <?php } ?>

  <?php if (strpos($value_config, ','."Rent Price".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Rent Price:</label>
    <div class="col-sm-8">
      <input name="rent_price" value="<?php echo $rent_price; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>


  <?php if (strpos($value_config, ','."Rental Days".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Rental Days:</label>
    <div class="col-sm-8">
      <input name="rental_days" value="<?php echo $rental_days; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Rental Weeks:</label>
    <div class="col-sm-8">
      <input name="rental_weeks" value="<?php echo $rental_weeks; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."Rental Months".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Rental Months:</label>
    <div class="col-sm-8">
      <input name="rental_months" value="<?php echo $rental_months; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."Rental Years".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Rental Years:</label>
    <div class="col-sm-8">
      <input name="rental_years" value="<?php echo $rental_years; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Reminder/Alert:</label>
    <div class="col-sm-8">
      <input name="reminder_alert" value="<?php echo $reminder_alert; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>


  <?php if (strpos($value_config, ','."Daily".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Daily:</label>
    <div class="col-sm-8">
      <input name="daily" value="<?php echo $daily; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Weekly".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Weekly:</label>
    <div class="col-sm-8">
      <input name="weekly" value="<?php echo $weekly; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."Monthly".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Monthly:</label>
    <div class="col-sm-8">
      <input name="monthly" value="<?php echo $monthly; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."Annually".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Annually:</label>
    <div class="col-sm-8">
      <input name="annually" value="<?php echo $annually; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."#Of Days".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">#Of Days:</label>
    <div class="col-sm-8">
      <input name="total_days" value="<?php echo $total_days; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."#Of Hours".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">#Of Hours:</label>
    <div class="col-sm-8">
      <input name="total_hours" value="<?php echo $total_hours; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">#Of Kilometers:</label>
    <div class="col-sm-8">
      <input name="total_km" value="<?php echo $total_km; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."#Of Miles".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">#Of Miles:</label>
    <div class="col-sm-8">
      <input name="total_miles" value="<?php echo $total_miles; ?>" type="text" data-table="certificate" data-id="<?= $certificateid ?>" data-id-field="certificateid" class="form-control">
    </div>
  </div>
  <?php } ?>
  <div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<a href="?" class="btn brand-btn pull-right">Submit</a>