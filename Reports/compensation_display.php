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
	<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

	<input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
	<input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
	<input type="hidden" name="therapistpdf" value="<?php echo $therapist; ?>">

	<div class="pull-right">
		<?php if (strpos($value_config, ','."Compensation Print Appointment Reports".',') !== FALSE) { ?>
			<span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="This report is used for a detailed analysis, with customer and invoice numbers broken out by each service type. This report is only for internal use."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20" style="padding-bottom:5px;" /></a></span>
			<button type="submit" name="printapptpdf" value="Print Appointment Report" class="btn brand-btn" onclick="set_form_action();">Print Appointment Report</button>
		<?php } ?>

		<span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="This report is for staff to see their compensation structure schedule."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20" style="padding-bottom:5px;" /></a></span>
		<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn" onclick="set_form_action();">Print Report</button>
	</div>
	<br><br>

	<?php
		//echo '<a href="report_compensation.php?compensation=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

		//$contractDateBegin = strtotime($starttime);
		//$contractDateEnd = strtotime($endtime);

		//$stat_start = '2016-08-02';
		//$stat_end = '2016-08-31';
		//$stat_end = '0000-00-00';
		//$stat_start = '0000-00-00';
        //
		//$total_stat_holiday = 0;
		//$stat_holiday = explode(',',get_config($dbc, 'stat_holiday'));
		//foreach($stat_holiday as $stat_day){
		//	$stat_date = strtotime($stat_day);
		//	if (($stat_date >= $contractDateBegin) && ($stat_date <= $contractDateEnd)) {
		//		$stat_end = date('Y-m-d', strtotime('-1 day', strtotime($stat_day)));
		//		$stat_start = date('Y-m-d', strtotime('-63 day', strtotime($stat_day)));
        //
		//		$total_stat_holiday++;
		//	}
		//}
		$stat_holidays = [];
		foreach(mysqli_fetch_all(mysqli_query($dbc, "SELECT `date` FROM `holidays` WHERE `paid`=1 AND `deleted`=0")) as $stat_day) {
			$stat_holidays[] = $stat_day[0];
		}
		$stat_holidays = implode(',', $stat_holidays);
		//$stat_holidays = get_config($dbc, 'stat_holiday');

		echo report_compensation($dbc, $starttime, $endtime, '', '', '', $therapist, $stat_holidays, $invoicetype);
	?>

	<input type="hidden" name="stat_holidays_pdf" value="<?php echo $stat_holidays; ?>">

</form>