<script>
function set_form_action() {
	$('form[name=form1]').attr('action','<?php echo WEBSITE_URL; ?>/Reports/report_compensation.php');
}
</script>
<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
	<input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

    <?php
    //$contactid = '';
    if (isset($_POST['search_email_submit'])) {
        $starttime = $_POST['starttime'];
        $endtime = $_POST['endtime'];
        $therapist = $_POST['therapist'];
        $equipment = $_POST['equipment'];
    }
    $frequency = get_field_value('frequency_type frequency_interval ratecardid', 'rate_card', 'clientid',$therapist);
    $rateid = $frequency['ratecardid'];
    if(empty($frequency['frequency_type']) && empty($frequency['frequency_interval'])) {
        $frequency = get_field_value('frequency_type frequency_interval ratecardid', 'rate_card', 'clientid',get_contact($dbc, $therapist, 'businessid'));
        if(!($rateid > 0)) {
            $rateid = $frequency['rateid'];
        }
    }
    if(empty($frequency['frequency_type']) && empty($frequency['frequency_interval'])) {
        $frequency = get_field_value('frequency_type frequency_interval ratecardid', 'rate_card', 'clientid',get_contact($dbc, $therapist, 'ref_contact'));
        if(!($rateid > 0)) {
            $rateid = $frequency['rateid'];
        }
    }

    if($starttime == 0000-00-00) {
        $starttime = date('Y-m-01');
    }

    if($endtime == 0000-00-00) {
        $endtime = date('Y-m-d');
    }
    if($endtime == date("Y-m-d") && !empty($frequency['frequency_type']) && !empty($frequency['frequency_interval'])) {
        $endtime = date("Y-m-d", strtotime($starttime." + ".($frequency['frequency_type'] == 'Quarter' ? $frequency['frequency_interval'] * 3 : $frequency['frequency_interval'])." ".($frequency['frequency_type'] == 'Quarter' ? 'Month' : $frequency['frequency_type'])));
    }

	$value_config = ','.get_config($dbc, 'reports_dashboard').',';
    ?>

	<center><div class="form-group">
		<div class="form-group col-sm-5">
			<label class="col-sm-4">From:</label>
			<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
		</div>
		<div class="form-group col-sm-5">
			<label class="col-sm-4">Until:</label>
			<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
		</div>
		<div class="form-group col-sm-5">
			<label class="col-sm-4"><?= $contact_type == 'staff' ? 'Staff' : VENDOR_TILE ?>:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select <?= $contact_type == 'staff' ? 'Staff' : VENDOR_TILE ?>..." name="therapist" class="chosen-select-deselect form-control">
					<option value=""></option>
					<?php if($contact_type == 'vendor') {
                        $vendor_tabs = explode(',',get_config($dbc, 'vendors_tabs'));
                        $query = sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name, `name` FROM contacts WHERE category NOT IN ('Staff') AND `category` IN ('".implode("','",$vendor_tabs)."') AND `status`>0 AND deleted=0"));
                    } else {
                        $query = sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name, `name` FROM contacts WHERE category IN ('Staff') AND ".STAFF_CATS_HIDE_QUERY." AND `status`=1 AND deleted=0 AND status=1"));
                    }
					foreach($query as $rowid) {
						echo "<option ".($rowid['contactid'] == $therapist ? 'selected' : '')." value='".$rowid['contactid']."'>".$rowid['full_name']."</option>";
					} ?>
				</select>
			</div>
		</div>
        <?php if($contact_type == 'vendor') { ?>
            <div class="form-group col-sm-5">
                <label class="col-sm-4">Equipment:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Select Equipment..." name="equipment" class="chosen-select-deselect form-control">
                        <option value=""></option>
                        <?php $equip_list = $dbc->query("SELECT `equipment`.`equipmentid`, `equipment`.`category`, `equipment`.`make`, `equipment`.`model`, `equipment`.`unit_number`, `equipment`.`label`, `equipment`.`ownership_status` FROM `equipment` LEFT JOIN `rate_card` ON `rate_card`.`equipment` LIKE CONCAT(`equipment`.`equipmentid`,'#%') OR `rate_card`.`equipment` LIKE CONCAT('%**',`equipment`.`equipmentid`,'#%') WHERE `equipment`.`deleted`=0 AND (`equipment`.`ownership_status` > 0 OR (`rate_card`.`deleted`=0 AND `rate_card`.`on_off`=1)) GROUP BY `equipment`.`equipmentid`");
                        while($rowid = $equip_list->fetch_assoc()) {
                            echo "<option ".($rowid['equipmentid'] == $equipment ? 'selected' : '')." value='".$rowid['equipmentid']."'>".implode(': ',array_filter([$rowid['category'],$rowid['make'],$rowid['model'],$rowid['unit_number'],$rowid['label']]))."</option>";
                        } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
	<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

	<input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
	<input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
	<input type="hidden" name="therapistpdf" value="<?php echo $therapist; ?>">
	<input type="hidden" name="equipmentpdf" value="<?php echo $equipment; ?>">

	<div class="pull-right">
		<?php if (strpos($value_config, ','."Compensation Print Appointment Reports".',') !== FALSE) { ?>
			<span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="This report is used for a detailed analysis, with customer and invoice numbers broken out by each service type. This report is only for internal use."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20" style="padding-bottom:5px;" /></a></span>
			<button type="submit" name="printapptpdf" value="Print Appointment Report" class="btn brand-btn" onclick="set_form_action();">Print Appointment Report</button>
		<?php } ?>

		<span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="This report is for staff to see their compensation structure schedule."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20" style="padding-bottom:5px;" /></a></span>
		<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn" onclick="set_form_action();">Print Report</button>
	</div>
	<br><br>

	<?php $stat_holidays = [];
    foreach(mysqli_fetch_all(mysqli_query($dbc, "SELECT `date` FROM `holidays` WHERE `paid`=1 AND `deleted`=0")) as $stat_day) {
        $stat_holidays[] = $stat_day[0];
    }
    $stat_holidays = implode(',', $stat_holidays);

    echo report_compensation($dbc, $starttime, $endtime, '', '', '', $therapist, $stat_holidays, $invoicetype, $equipment); ?>

	<input type="hidden" name="stat_holidays_pdf" value="<?php echo $stat_holidays; ?>">

</form>