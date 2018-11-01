<script type="text/javascript">
$(document).on('change', 'select[name="estimatetype[]"]', function() { filter_categories(this); });
$(document).on('change', 'select[name="payment_terms"]', function() { if(this.value == 'CUSTOM') { $(this).next().first().hide(); $('input[name=payment_terms]').show().removeAttr('disabled').focus(); } });
$(document).on('change', 'select[name="payment_due"]', function() { if(this.value == 'CUSTOM') { $(this).closest('.col-sm-12').hide(); $(this).closest('.col-sm-12').nextAll('div').show(); $('input[name=payment_due]').removeAttr('disabled').focus(); } });
</script>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Business<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <select name="businessid" <?php echo $disable_business; ?> id="businessid" data-placeholder="Select a Business..." class="chosen-select-deselect form-control" width="380">
            <option></option>
            <option value='ADD NEW'>Add New Business</option>
            <?php
            $query = mysqli_query($dbc,"SELECT contactid, IFNULL(name,'') name, IFNULL(first_name,'') first_name, IFNULL(last_name,'') last_name, category FROM contacts WHERE category='Business' AND deleted=0 ORDER BY category");
			$results = sort_contacts_array(mysqli_fetch_all($query, MYSQLI_ASSOC));
            foreach($results as $id) {
                echo "<option ".($businessid == $id ? 'selected' : '')." value='". $id."'>".get_client($dbc, $id).'</option>';
            }
            ?>
        </select>
    </div>
</div>
<div class="form-group location_db" id="new_business_div" style="display:none;">
	<label for="site_name" class="col-sm-4 control-label">New Business:</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" name="new_business" value="">
	</div>
</div>

<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Contact:</label>
    <div class="col-sm-8">
        <select name="estimateclientid" <?php echo $disable_client; ?> id="estimateclientid" data-placeholder="Select a Contact..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <option value='ADD NEW'>Add New Contact</option>
            <?php
            $cat = '';
            $query = mysqli_query($dbc,"SELECT contactid, IFNULL(name,'') name, IFNULL(first_name,'') first_name, IFNULL(last_name,'') last_name, category FROM contacts WHERE businessid='$businessid' ORDER BY `category`");
			$category_group = [];
			while($row = mysqli_fetch_array($query)) {
				if($cat != $row['category'] && count($category_group) > 0) {
					echo '<optgroup label="'.$row['category'].'">';
					$category_group = sort_contacts_array($category_group);
					foreach($category_group as $id) {
						echo "<option ".($id == $clientid ? 'selected' : '')." value='$id'>".get_contact($dbc, $id).'</option>';
					}
					$cat = $row['category'];
					$category_group = [];
				}
				$category_group[] = $row;
			}
			if(count($category_group) > 0) {
				echo '<optgroup label="'.$row['category'].'">';
				$category_group = sort_contacts_array($category_group);
				foreach($category_group as $id) {
					echo "<option ".($id == $clientid ? 'selected' : '')." value='$id'>".get_contact($dbc, $id).'</option>';
				}
				$cat = $row['category'];
				$category_group = [];
			}
            ?>
        </select>
    </div>
</div>
<div class="form-group location_db" id="new_contact_div" style="display:none;">
	<label for="site_name" class="col-sm-4 control-label">New Contact:</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" name="new_contact_name" value="">
	</div>
</div>

<?php if(strpos($base_field_config, ','."Estimate Site".',') !== FALSE): ?>
	<div class="form-group location_db">
		<label for="site_name" class="col-sm-4 control-label">Customer Site Location:</label>
		<div class="col-sm-8">
			<select data-placeholder="Choose a Location..." id="siteselect" name="siteid" class="chosen-select-deselect form-control" width="380">
				<option value=""></option>
				<option value="ADD NEW">New Site Location</option>
				<?php
				$query = mysqli_query($dbc,"SELECT siteid, site_name FROM field_sites WHERE deleted=0 AND clientid='$businessid'");
				while($row = mysqli_fetch_array($query)) {
					echo "<option ".($siteid == $row['siteid'] ? 'selected' : '')." value='". $row['siteid']."'>".$row['site_name'].'</option>';
				}
				?>
			</select>
		</div>
	</div>
	<div class="form-group location_db" id="new_site_div" style="display:none;">
		<label for="site_name" class="col-sm-4 control-label">New Site Location:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="new_site_location" value="">
		</div>
	</div>
<?php endif; ?>

<?php if(strpos($base_field_config, ','."AFE Number".',') !== FALSE): ?>
	<div class="form-group afe_number">
		<label for="site_name" class="col-sm-4 control-label">Customer AFE #:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="afe_number" value="<?= $afe_number ?>">
		</div>
	</div>
<?php endif; ?>

<!-- Hide this if WASHTECH is using ESTIMATES -->
<?php if(!isset($washtech_software_checker)) { ?>


<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right"><?= rtrim(ESTIMATE_TILE, 's') ?> Type<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <select name="estimatetype[]" multiple <?php echo $disable_type; ?> id="estimatetype" data-placeholder="Select a Type..." class="chosen-select-deselect form-control" width="380">
			<?php $rate_cats = [];
			$query = mysqli_query($dbc,"SELECT companyrcid, IFNULL(`rate_categories`,'') rate_categories FROM company_rate_card WHERE `rate_card_name` != '' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY rate_categories");
			while($row = mysqli_fetch_array($query)) {
				echo "<option data-type='rate_category' ".(in_array($row['rate_categories'], $estimatetype) || in_array(preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($row['rate_categories']))), $estimatetype) ? 'selected' : '')." value='". $row['rate_categories']."'>".$row['rate_categories'].'</option>';
				$rate_cats[] = $row['rate_categories'];
			}

			$project_types = explode(',',get_config($dbc,'project_tabs'));
			foreach($project_types as $type) {
				if(!in_array($type, $rate_cats)) {
					$type_val = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($type)));
					echo "<option ".((in_array($type, $estimatetype) || in_array($type_val, $estimatetype)) ? 'selected' : '')." value='$type_val'>$type</option>";
				}
			} ?>
            <!--<option <?php if ($estimatetype == "Client") { echo " selected"; } ?> value='Client'>Client</option>
            <?php if(tile_visible($dbc, 'sred') == 1) { ?>
            <option <?php if ($estimatetype == "SRED") { echo " selected"; } ?> value='SRED'>SR&ED</option>
            <?php } ?>
            <?php if(tile_visible($dbc, 'internal') == 1) { ?>
            <option <?php if ($estimatetype == "Internal") { echo " selected"; } ?> value='Internal'>Internal</option>
            <?php } ?>
            <?php if(tile_visible($dbc, 'rd') == 1) { ?>
            <option <?php if ($estimatetype == "RD") { echo " selected"; } ?> value='RD'>R&D</option>
            <?php } ?>
            <?php if(tile_visible($dbc, 'business_development') == 1) { ?>
            <option <?php if ($estimatetype == "Business Development") { echo " selected"; } ?>
            value='Business Development'>Business Development</option>
            <?php } ?>
            <?php if(tile_visible($dbc, 'process_development') == 1) { ?>
            <option <?php if ($estimatetype == "Process Development") { echo " selected"; } ?> value='Process Development'>Process Development</option>
            <?php } ?>
            <?php if(tile_visible($dbc, 'addendum') == 1) { ?>
            <option <?php if ($estimatetype == "Addendum") { echo " selected"; } ?> value='Addendum'>Addendum</option>
            <?php } ?>
            <?php if(tile_visible($dbc, 'addition') == 1) { ?>
            <option <?php if ($estimatetype == "Addition") { echo " selected"; } ?> value='Addition'>Addition</option>
            <?php } ?>
            <?php if(tile_visible($dbc, 'marketing') == 1) { ?>
            <option <?php if ($estimatetype == "Marketing") { echo " selected"; } ?> value='Marketing'>Marketing</option>
            <?php } ?>
            <?php if(tile_visible($dbc, 'manufacturing') == 1) { ?>
            <option <?php if ($estimatetype == "Manufacturing") { echo " selected"; } ?> value='Manufacturing'>Manufacturing</option>
            <?php } ?>
            <?php if(tile_visible($dbc, 'assembly') == 1) { ?>
            <option <?php if ($estimatetype == "Assembly") { echo " selected"; } ?> value='Assembly'>Assembly</option>
            <?php } ?>-->
        </select>
    </div>
</div>
<?php } ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right"><?php echo $short_name; ?><span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <input name="estimate_name" value="<?php echo $estimate_name; ?>" id="estimate_name" type="text" class="form-control"></p>
    </div>
</div>

<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Payment Terms<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
		<select name="payment_terms" class="form-control chosen-select-deselect">
			<option></option>
			<?php $quote_payment_term = explode('#*#', get_config($dbc, 'quote_payment_term'));
			foreach($quote_payment_term as $terms) {
				echo "<option ".($payment_terms == $terms ? 'selected' : '')." value='$terms'>$terms</option>";
			}
			if(!in_array($payment_terms, $quote_payment_term)) {
				echo "<option selected value='$payment_terms'>$payment_terms</option>";
			} ?>
			<option value="CUSTOM">Custom Terms</option>
		</select>
        <input name="payment_terms" disabled value="" type="text" class="form-control" style="display:none;"></p>
    </div>
</div>

<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Payment Due<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
		<div class="col-sm-12">
			<select name="payment_due" class="form-control chosen-select-deselect">
				<option></option>
				<?php $quote_due_period = explode('#*#', get_config($dbc, 'quote_due_period'));
				foreach($quote_due_period as $period) {
					echo "<option ".($payment_due == $period ? 'selected' : '')." value='$period'>$period</option>";
				}
				if(!in_array($payment_due, $quote_due_period)) {
					echo "<option selected value='$payment_due'>$payment_due</option>";
				} ?>
				<option value="CUSTOM">Custom Due</option>
			</select>
		</div>
        <div class="col-sm-11" style="display:none;"><input name="payment_due" disabled value="" type="text" class="form-control"></div>
		<div class="col-sm-1" style="display:none;"><a href="" onclick="$('select[name=payment_due]').val('').trigger('change.select2'); $(this).closest('.col-sm-8').find('div[class^=col-sm]').hide(); $(this).closest('.col-sm-8').find('.col-sm-12').show(); $('input[name=payment_due]').prop('disabled','disabled'); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a></div></p>
    </div>
</div>
