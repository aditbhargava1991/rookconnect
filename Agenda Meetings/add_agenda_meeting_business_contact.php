<div id="business_group_clone" name="business_group">
	<?php if (strpos($value_config, ','."Business".',') !== FALSE) { ?>
		<div class="form-group clearfix completion_date">
			<label for="first_name" class="col-sm-4 control-label text-right"><?= BUSINESS_CAT ?><span class="brand-color">*</span>:</label>
			<div class="col-sm-8">
				<select name="businessid[]" multiple <?php echo $disable_business; ?> id="businessid" data-placeholder="Select an Option..." class="chosen-select-deselect form-control" width="380">
					<option value='New Business'>New Business</option>
					<?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='".BUSINESS_CAT."' AND deleted=0"),MYSQLI_ASSOC));
					foreach($query as $id) {
						echo "<option ".(strpos(','.$businessid.',', ','.$id.',') !== false ? 'selected' : '')." value='". $id."'>".get_client($dbc, $id).'</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group" style="display:none;">
			<label for="first_name" class="col-sm-4 control-label text-right">New <?= BUSINESS_CAT ?>:</label>
			<div class="col-sm-8">
				<input type="text" name="new_business_name" class="form-control">
			</div>
		</div>
		<script>
		$(document).ready(function() {
			$("[name='businessid[]']").change(function() {
				var bid = $(this);
				var cid = $('[name="businesscontactid[]"]');
				if(bid.find('[value="New Business"]:selected').val() == 'New Business') {
					$('[name="new_business_name"]').closest('.form-group').show();
					$('[name="new_contact_name"]').closest('.form-group').show();
				}

				$.ajax({
					url: "agenda_ajax.php?data=CONTACTS&bid="+bid.val()+"&cid="+cid.val(),
					success: function(response) {
						var block = bid.closest('[name=business_group]');
						var contacts = block.find('[name="businesscontactid[]"]');
						contacts.empty().append(response).trigger('change.select2');
					}
				});
				var cid = $('[name="meeting_requested_by"]');
				$.ajax({
					url: "agenda_ajax.php?data=REQUEST&bid="+bid.val()+"&cid="+cid.val(),
					success: function(response) {
						var contacts = $('[name="meeting_requested_by"]');
						contacts.empty().append(response).trigger('change.select2');
					}
				});
				var pid = $('[name="projectid[]"]');
				$.ajax({
					url: "agenda_ajax.php?data=PROJECTS&bid="+bid.val()+"&pid="+pid.val(),
					success: function(response) {
						pid.empty().append(response).trigger('change.select2');
					}
				});
				var agenda_email_id = $('[name="agenda_email_business[]"]');
				$.ajax({
					url: "agenda_ajax.php?data=EMAIL&bid="+bid.val()+"&eid="+agenda_email_id.val(),
					success: function(response) {
						agenda_email_id.empty().append(response).trigger('change.select2');
					}
				});
				var meeting_email_id = $('[name="businesscontactemailid[]"]');
				$.ajax({
					url: "agenda_ajax.php?data=EMAIL&bid="+bid.val()+"&eid="+meeting_email_id.val(),
					success: function(response) {
						meeting_email_id.empty().append(response).trigger('change.select2');
					}
				});
			});
		});
		</script>
	<?php } ?>

	<?php if (strpos($value_config, ','."Contact".',') !== FALSE) {
		$categories = get_config($dbc, 'contacts_tabs');
		if(empty($categories)) {
			$categories = get_config($dbc, 'contactsrolodex_tabs');
		}
		if(empty($categories)) {
			$categories = get_config($dbc, 'contacts3_tabs');
		}
		if(empty($categories)) {
			$categories = get_config($dbc, 'clientinfo_tabs');
		}

        if(tile_enabled($dbc, 'members')) {
                $contact_label = 'Members';
        } else {
            $contact_label = 'Contact';
            $cat_array = array_filter(explode(',',str_replace('Business','',$categories)));
            if(count($cat_array) == 1) {
                foreach($cat_array as $cat_label) {
                    $contact_label = $cat_label;
                }
                switch($contact_label) {
                    case 'Contacts': $contact_label = 'Contact'; break;
                    case 'Customers': $contact_label = 'Customer'; break;
                    case 'Clients': $contact_label = 'Client'; break;
                }
            }
        }
		?>
		<div class="form-group clearfix completion_date">
			<label for="first_name" class="col-sm-4 control-label text-right"><?= $contact_label ?><span class="brand-color">*</span>:</label>
			<div class="col-sm-8">
				<select name="businesscontactid[]" multiple <?php echo $disable_client; ?> id="estimateclientid" data-placeholder="Select a <?= $contact_label ?>..." class="chosen-select-deselect form-control" width="380">
					<option value='New <?= $contact_label ?>'>New <?= $contact_label ?></option>
					<?php
					$cat = '';
					$cat_list = [];
					$this_list = [];
					$query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE (',$businessid,') LIKE CONCAT('%,',IFNULL(`businessid`,0),',%') AND `deleted`=0 AND `status`=1 ORDER BY category");
					while($row = mysqli_fetch_array($query)) {
						if($cat != $row['category']) {
							$cat_list[$cat] = sort_contacts_array($this_list);
							$cat = $row['category'];
							$this_list = [];
						}
						$this_list[] = [ 'contactid' => $row['contactid'], 'name' => $row['name'], 'last_name' => $row['last_name'], 'first_name' => $row['first_name'] ];
					}
					$cat_list[$cat] = sort_contacts_array($this_list);
					$this_list = [];
					foreach($cat_list as $cat => $id_list) {
						echo '<optgroup label="'.$cat.'">';
						foreach($id_list as $id) {
							$names = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$id'"));
                            if(($names['name'] != '') || ($names['first_name'] != '' || $names['last_name'] != '')) {
							    echo "<option ".($businesscontactid == $id ? 'selected' : '')." value='".$id."'>".decryptIt($names['name']).($names['name'].$names['first_name'].$names['last_name'] != '' ? ' ' : '').decryptIt($names['first_name'])." ".decryptIt($names['last_name']).'</option>';
                            }
						}
					} ?>
				</select>
			</div>
		</div>
		<div id="new_contact_block" class="form-group" style="display:none;">
			<label for="first_name" class="col-sm-4 control-label text-right">New <?= $contact_label ?> Category:</label>
			<div class="col-sm-8">
				<select name="new-contact-category"  data-placeholder="Select a Category..." class="chosen-select-deselect form-control"><option></option></select>
			</div>
			<div id="new_contact_iframe" style="display:none">
				<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' onclick="close_new_contact_block();" class='close_iframe_new_contacts' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
				<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
				<iframe name="iframe_new_contacts" id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
			</div>
		</div>
		<script>
		$(document).ready(function() {
			$("[name='businesscontactid[]']").change(function() {
				var new_contact = $(this).find('option[value="New Contact"]:selected');
				if(new_contact.length > 0) {
					$('#new_contact_block').show();
					//$("[name='businesscontactid[]']").trigger('change.select2');
				}
			});
			$.ajax({
				url: '../Contacts/get_list.php',
				method: 'POST',
				data: { target: 'categories' },
				success: function(result) {
					var categories = JSON.parse(result);
					categories.forEach(function(cat) {
						var option = document.createElement("option");
						if("<?php echo $rookconnect; ?>" == "highland" && cat == 'Business') {
							option.text = "Customer";
						} else if("<?php echo $rookconnect; ?>" == "highland" && (cat == 'Customer' || cat == 'Customers')) {
							option.text = "Contact";
						} else {
							option.text = cat;
						}

						option.value = cat;
						if(cat != 'Business' && cat != 'Staff' && cat != '<?= $url_category ?>') {
							$('[name=new-contact-category]').append(option);
						}
					});
					$('[name=new-contact-category]').append("<option value='CANCEL'>Cancel New Contact</option>");
					$('[name=new-contact-category]').trigger('change.select2');

					$('[name=new-contact-category]').chosen().change(function() {
						var category = $(this).val();
						if(category == 'CANCEL') {
							$("[name='businesscontactid[]']").find('option[value="New Contact"]').removeAttr('selected');
							$("[name='businesscontactid[]']").trigger('change.select2');
							$('#new_contact_block').hide();
							$('[name=new-contact-category]').empty();
						} else {
							$('[name=iframe_new_contacts]').off('load');
							$('#new_contact_iframe').show();
							$('[name=iframe_new_contacts]').attr('src', '../Contacts/add_contacts.php?category='+category);
							$('[name=iframe_new_contacts]').load(function() {
								$('[name=iframe_new_contacts]').load(function() {
									$('[name=new-contact-category]').empty().val('').trigger('change.select2');
									$('#new_contact_iframe').hide();
									$('[name=iframe_new_contacts]').off('load');
									$('[name=iframe_new_contacts]').attr('src', '');
								});
							});
						}
					});
				}
			});
		});
		</script>
		<?php if (strpos($value_config, ','."Add New Contact".',') !== FALSE) { ?>
			<div class="form-group" style="display:none;">
				<label for="first_name" class="col-sm-4 control-label text-right">New Contact:</label>
				<div class="col-sm-8">
					<input type="text" name="new_contact_name" class="form-control">
				</div>
			</div>
		<?php } ?>
	<?php } ?>
</div>

<?php if (strpos($value_config, ','."Company Attendees".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
	<label for="first_name" class="col-sm-4 control-label text-right">Staff Members:</label>
	<div class="col-sm-8">
		<select name="companycontactid[]" multiple <?php echo $disable_client; ?> data-placeholder="Select an Option..." class="chosen-select-deselect form-control" width="380">
			<?php $query1 = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
			foreach($query1 as $id) {
                $get_timer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `timer` FROM `agenda_meeting_timer` WHERE `agendameetingid` = '$agendameetingid' AND created_by = '$id'"));
                $timer = '';
                $e_time = explode(':',$get_timer['timer']);
                echo $e_time[0];
                if($e_time[0] > 0) {
                    $timer .= $e_time[0].' hour ';
                }
                if($e_time[1] > 0) {
                    $timer .= $e_time[1].' minutes';
                }
                echo "<option ".(strpos(','.$companycontactid.',', ','.$id.',') !== FALSE ? 'selected' : '').' value="'.$id.'">'.get_contact($dbc, $id).' - '.$timer.'</option>';
			} ?>
		</select>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Heading".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Heading:</label>
    <div class="col-sm-8">
        <select name="heading" data-placeholder="Select a Heading..." class="heading chosen-select-deselect form-control" width="380">
            <option value=''></option>

                <?php
                $query = mysqli_fetch_all(mysqli_query($dbc,"SELECT DISTINCT(heading) FROM agenda_meeting"),MYSQLI_ASSOC);
                foreach($query as $id) {
                    if($id['heading'] != '') {
                        echo "<option ".(strpos(','.$heading.',', ','.$id['heading'].',') !== false ? 'selected' : '')." value='". $id['heading']."'>".$id['heading'].'</option>';
                    }
                }
                ?>
                <option value='Other'>Other <?php echo $heading; ?></option>
        </select>
    </div>
</div>

<div class="form-group clearfix other_heading">
    <label for="first_name" class="col-sm-4 control-label text-right">Other Heading:</label>
    <div class="col-sm-8">
        <input type="text" name="other_heading" value="<?php echo $other_heading; ?>"  class="form-control">
    </div>
</div>
<?php } ?>


<?php if (strpos($value_config, ','."Sub Committee".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
	<label for="first_name" class="col-sm-4 control-label text-right">Sub-Committee:</label>
	<div class="col-sm-8">
		<select name="subcommittee" data-placeholder="Select an Option..." class="chosen-select-deselect form-control" width="380">
			<option value=''></option>
			<?php foreach(array_filter(explode(',',$get_field_config['subcommittee_types'])) as $subcommittee_type) {
				echo "<option ".($subcommittee == $subcommittee_type ? 'selected' : '').' value="'.$subcommittee_type.'">'.$subcommittee_type.'</option>';
			} ?>
		</select>
	</div>
</div>
<?php } ?>