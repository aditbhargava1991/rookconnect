<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$report_fields = explode(',', get_config($dbc, 'report_operation_fields'));

if (isset($_GET['printpdf'])) {
    $starttimepdf = $_GET['starttimepdf'];
    $endtimepdf = $_GET['endtimepdf'];
    $createstartpdf = $_GET['createstartpdf'];
    $createendpdf = $_GET['createendpdf'];
    $businessidpdf = $_GET['businessidpdf'];
    $siteidpdf = json_decode($_GET['siteidpdf']);
    $ticketidpdf = $_GET['ticketidpdf'];
    $extra_ticketpdf = $_GET['extra_ticketpdf'];
    $projectidpdf = $_GET['projectidpdf'];
    $hide_staffpdf = $_GET['hide_staffpdf'];
    $hide_wopdf = $_GET['hide_wopdf'];
    $disable_staffpdf = $_GET['disable_staffpdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
			//$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
            if(REPORT_LOGO != '') {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = TICKET_NOUN.' Activity';
            $this->writeHTMLCell(0, 0, 0 , 15, $footer_text, 0, 0, false, "C", true);
		}

		// Page footer
		public function Footer() {
            $this->SetY(-24);
            $this->SetFont('helvetica', 'I', 9);
            $footer_text = '<span style="text-align:left;">'.REPORT_FOOTER.'</span>';
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);

			// Position at 15 mm from bottom
			$this->SetY(-15);
            $this->SetFont('helvetica', 'I', 9);
			$footer_text = '<span style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on '.date('Y-m-d H:i:s').'</span>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
    	}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	foreach(report_output($dbc, $starttimepdf, $endtimepdf, $createstartpdf, $createendpdf, $businessidpdf, $siteidpdf, $ticketidpdf, $extra_ticketpdf, $projectidpdf, $hide_staffpdf, $hide_wopdf, $disable_staffpdf, 'padding:3px; border:1px solid black;', '', '', 'print') as $html) {
		$pdf->AddPage('L','LETTER');
		$pdf->SetFont('helvetica','',9);
		$pdf->writeHTML($html, true, false, true, false, '');
	}
    $today_date = date('Y_m_d');
    ob_end_clean(); //Maybe required in some instances when outputting to browser
	$pdf->Output('activity_report_'.$today_date.'.pdf', 'I'); //I - won't save the PDF, F - save the PDF on server
    track_download($dbc, 'report_operation_ticket_tasks', 0, WEBSITE_URL.'/Reports/Download/activity_report_'.$today_date.'.pdf', 'Activity Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/activity_report_<?= $today_date ?>.pdf', 'fullscreen=yes');
	</script><?php
	$_GET['search_email_submit'] = 1;
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $createstart = $createstartpdf;
    $createend = $createendpdf;
    $businessid = $businessidpdf;
    $siteid = $siteidpdf;
    $ticketid = $ticketidpdf;
    $extra_ticket = $extra_ticketpdf;
    $projectid = $projectidpdf;
    $hide_staff = $hide_staffpdf;
    $hide_wo = $hide_wopdf;
    $disable_staff = $disable_staffpdf;
} ?>

<script type="text/javascript">
$(document).ready(function() {
    $('a.printpdf').click(function() {
        $('[name=printpdf]').click();
    });
    <?php if ( isset($_GET['search_email_submit']) ) { ?>
        $('#search_div').hide();
    <?php } else { ?>
        $('#search_div').show();
    <?php } ?>
    
    $('.show_search').click(function(){
        $('#search_div').toggle();
    });
});
function bus_filter(select) {
	var bus = select.value;
	$('[name="siteid[]"] option').each(function() {
		if($(this).data('business') != bus && bus > 0) {
			$(this).removeAttr('selected').hide();
		} else {
			$(this).show();
		}
	});
	$('[name="siteid[]"]').trigger('change.select2');
}
</script>
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span> Displays a list of work done that will result in extra billing sorted by date and by site.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="get" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
        <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
        <input type="hidden" name="report" value="<?php echo $_GET['report']; ?>">
        <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_GET['search_email_submit'])) {
                $starttime = $_GET['starttime'];
                $endtime = $_GET['endtime'];
                $createstart = $_GET['createstart'];
                $createend = $_GET['createend'];
                $businessid = $_GET['businessid'];
                $siteid = $_GET['siteid'];
                $extra_ticket = $_GET['search_extra_ticket'];
                $ticketid = $_GET['ticketid'];
                $projectid = $_GET['projectid'];
                $hide_staff = $_GET['hide_staff'];
                $hide_wo = $_GET['hide_wo'];
                $disable_staff = implode('#',$_GET['disable_staff']);
            }
            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }
            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            if($createend == 0000-00-00 && (isset($_GET['createend']) || !isset($_GET['search_email_submit']))) {
                $createend = date('Y-m-d');
            } else if($createend == 0000-00-00) {
				$createend = '9999-12-30';
			}
            if(!($businessid > 0)) {
                $businessid = '%';
            }
            if(!($siteid > 0)) {
                $siteid = [];
            } ?>

            <div id="search_div">
				<div class="form-group col-sm-6">
					<label class="col-sm-4"><?= BUSINESS_CAT ?>:</label>
					<div class="col-sm-8">
						<select name="businessid" class="chosen-select-deselect" data-placeholder="Select <?= BUSINESS_CAT ?>" onchange="bus_filter(this);"><option />
							<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `name`, `last_name`, `first_name` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `category`='".BUSINESS_CAT."'")) as $business_row) { ?>
								<option <?= $business_row['contactid'] == $businessid ? 'selected' : '' ?> value="<?= $business_row['contactid'] ?>"><?= $business_row['full_name'] ?></option>
							<?php } ?>
						</select>
					</div>
                </div>
				<div class="form-group col-sm-6">
					<label class="col-sm-4"><?= SITES_CAT ?>:</label>
					<div class="col-sm-8">
						<select name="siteid[]" multiple class="chosen-select-deselect" data-placeholder="Select <?= SITES_CAT ?>"><option />
							<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `site_name`, `display_name`, `businessid` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `category`='".SITES_CAT."'")) as $site_row) { ?>
								<option data-business="<?= $site_row['businessid'] ?>" <?= in_array($site_row['contactid'], $siteid) ? 'selected' : '' ?> value="<?= $site_row['contactid'] ?>"><?= $site_row['full_name'] ?></option>
							<?php } ?>
						</select>
					</div>
                </div>
				<div class="form-group col-sm-6">
					<label class="col-sm-4"><?= TICKET_NOUN ?>:</label>
					<div class="col-sm-8">
						<select name="ticketid" class="chosen-select-deselect" data-placeholder="Select <?= TICKET_NOUN ?>"><option />
							<?php $ticket_list = $dbc->query("SELECT * FROM `tickets` WHERE `deleted`=0 ORDER BY `ticket_label`, `ticketid`");
							while($ticket_row = $ticket_list->fetch_assoc()) { ?>
								<option <?= $ticket_row['ticketid'] == $ticketid ? 'selected' : '' ?> value="<?= $ticket_row['ticketid'] ?>"><?= get_ticket_label($dbc, $ticket_row) ?></option>
							<?php } ?>
						</select>
					</div>
                </div>
				<div class="form-group col-sm-6">
					<label class="col-sm-4"><?= PROJECT_NOUN ?>:</label>
					<div class="col-sm-8">
						<select name="projectid" class="chosen-select-deselect" data-placeholder="Select <?= PROJECT_NOUN ?>"><option />
							<?php $project_list = $dbc->query("SELECT * FROM `project` WHERE `deleted`=0 ORDER BY `projectid`");
							while($project_row = $project_list->fetch_assoc()) { ?>
								<option <?= $project_row['projectid'] == $projectid ? 'selected' : '' ?> value="<?= $project_row['projectid'] ?>"><?= get_project_label($dbc, $project_row) ?></option>
							<?php } ?>
						</select>
					</div>
                </div>
				<?php if(in_array('filter_extra_billing',$report_fields)) { ?>
					<div class="form-group col-sm-6">
						<label for="search_extra_ticket" class="col-sm-4 control-label">Search By Extra Billing <?= TICKET_NOUN ?>:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select a <?= TICKET_NOUN ?> #" name="search_extra_ticket" class="chosen-select-deselect form-control">
								<option value=""></option>
								<?php
								$query = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `ticket_comment` WHERE `type`='service_extra_billing' AND `deleted`=0) ORDER BY `ticketid`");
								while($row = mysqli_fetch_array($query)) { ?>
									<option <?php if ($row['ticketid'] == $extra_ticket) { echo " selected"; } ?> value='<?= $row['ticketid'] ?>' ><?= get_ticket_label($dbc, $row) ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('filter_materials',$report_fields)) { ?>
					<div class="form-group col-sm-6">
						<label for="search_material" class="col-sm-4 control-label">Search By Materials:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select Material" name="search_material" class="chosen-select-deselect form-control">
								<option value=""></option>
								<?php
								$query = mysqli_query($dbc,"SELECT `description` FROM (SELECT `description` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='material' UNION SELECT `name` `description` FROM `material` WHERE `deleted`=0) `materials` WHERE IFNULL(`description`,'')!='' GROUP BY `description` ORDER BY `description`");
								while($row = mysqli_fetch_array($query)) { ?>
									<option <?php if ($row['description'] == $search_material) { echo " selected"; } ?> value='<?= $row['description'] ?>' ><?= $row['description'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('filter_staff_expenses',$report_fields)) { ?>
					<div class="form-group col-sm-6">
						<label for="search_expenses" class="col-sm-4 control-label">Only <?= TICKET_TILE ?> with Expenses:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select Option" name="search_material" class="chosen-select-deselect form-control">
								<option <?= $search_expenses == 'No' ? 'selected' : '' ?> value="No">Display All</option>
								<option <?= $search_expenses == 'Yes' ? 'selected' : '' ?> value="Yes">Only with Expenses</option>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('filter_ticket_notes',$report_fields)) { ?>
					<div class="form-group col-sm-6">
						<label for="search_notes" class="col-sm-4 control-label">Only <?= TICKET_TILE ?> with Notes:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select Option" name="search_material" class="chosen-select-deselect form-control">
								<option <?= $search_notes == 'No' ? 'selected' : '' ?> value="No">Display All</option>
								<option <?= $search_notes == 'Yes' ? 'selected' : '' ?> value="Yes">Only with Notes</option>
							</select>
						</div>
					</div>
				<?php } ?>
				<div class="form-group col-sm-6">
					<label class="col-sm-4">Date From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-6">
					<label class="col-sm-4">Date Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
				<?php if(in_array('ticket_activity_created_dates',$report_fields)) { ?>
					<div class="form-group col-sm-6">
						<label class="col-sm-4"><?= TICKET_NOUN ?> Created From:</label>
						<div class="col-sm-8"><input name="createstart" type="text" class="datepicker form-control" value="<?php echo $createstart; ?>"></div>
					</div>
					<div class="form-group col-sm-6">
						<label class="col-sm-4"><?= TICKET_NOUN ?> Created Until:</label>
						<div class="col-sm-8"><input name="createend" type="text" class="datepicker form-control" value="<?php echo $createend; ?>"></div>
					</div>
				<?php } ?>
				<div class="form-group">
					<label class="form-checkbox col-sm-4"><input name="hide_staff" type="checkbox" <?= $hide_staff == 'hide' ? 'checked' : '' ?> value="hide">Hide Staff on Report</label>
					<label class="form-checkbox col-sm-4"><input name="hide_wo" type="checkbox" <?= $hide_wo == 'hide' ? 'checked' : '' ?> value="hide">Hide <?= TICKET_NOUN ?> on Report</label>
					<?php if($disable_staff != '') { ?>
						<label class="form-checkbox col-sm-4 any-width"><input name="disable_staff[]" type="checkbox" checked value="<?= $disable_staff ?>">Keep Selected Staff Hidden from Report</label>
					<?php } ?>
				</div>
				<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block pull-right">Submit</button>
                <div class="clearfix"></div>
			</div>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="createstartpdf" value="<?php echo $createstart; ?>">
            <input type="hidden" name="createendpdf" value="<?php echo $createend; ?>">
            <input type="hidden" name="businessidpdf" value="<?php echo $businessid; ?>">
            <input type="hidden" name="siteidpdf" value='<?= json_encode($siteid) ?>'>
            <input type="hidden" name="ticketidpdf" value="<?php echo $ticketid; ?>">
            <input type="hidden" name="extra_ticketpdf" value="<?php echo $extra_ticket; ?>">
            <input type="hidden" name="projectidpdf" value="<?php echo $projectid; ?>">
            <input type="hidden" name="hide_staffpdf" value="<?php echo $hide_staff; ?>">
            <input type="hidden" name="hide_wopdf" value="<?php echo $hide_wo; ?>">
            <input type="hidden" name="disable_staffpdf" value="<?php echo $disable_staff; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right" style="visibility:hidden;">Print Report</button>
            <br><br>
			<div class="clearfix"></div>

            <?php if(isset($_GET['search_email_submit'])) {
                foreach(report_output($dbc, $starttime, $endtime, $createstart, $createend, $businessid, $siteid, $ticketid, $extra_ticket, $projectid, $hide_staff, $hide_wo, $disable_staff) as $page) {
					echo $page;
				}
				display_pagination($dbc,"SELECT '".$_SERVER['page_rows']."' `numrows`", $_GET['page'], 25);
            } else {
				echo '<h3>Please submit the search criteria.</h3>';
			} ?>
        </form>

<?php

function report_output($dbc, $starttime, $endtime, $createstart, $createend, $businessid, $siteid, $ticketid, $extra_ticket, $projectid, $hide_staff, $hide_wo, $disable_staff, $table_style, $table_row_style, $grand_total_style, $output_mode) {
	$report_fields = explode(',', get_config($dbc, 'report_operation_fields'));
	$report_pages = [];
	$starttime = filter_var($starttime,FILTER_SANITIZE_STRING);
	$endtime = filter_var($endtime,FILTER_SANITIZE_STRING);
	$createstart = date('Y-m-d',strtotime(filter_var($createstart,FILTER_SANITIZE_STRING)));
	$createend = date('Y-m-d',strtotime(filter_var($createend,FILTER_SANITIZE_STRING).' +1day'));
	$businessid = filter_var($businessid,FILTER_SANITIZE_STRING);
	$ticketid = filter_var($ticketid,FILTER_SANITIZE_STRING);
	$extra_ticket = filter_var($extra_ticket,FILTER_SANITIZE_STRING);
	$projectid = filter_var($projectid,FILTER_SANITIZE_STRING);
	$disable_staff = filter_var($disable_staff,FILTER_SANITIZE_STRING);
	$report_head = '<table width="100%" style="border:0 solid black;">
		<tr>
			<td style="padding:0.5em;width:15%;">Date Range:</td>
			<td style="padding:0.5em;width:35%;">'.date('M-j-Y',strtotime($starttime)).' to '.date('M-j-Y',strtotime($endtime)).'</td>
			<td style="padding:0.5em;width:15%;">'.($ticketid > 0 ? TICKET_NOUN.':' : '').'</td>
			<td style="padding:0.5em;width:35%;">'.($ticketid > 0 ? get_ticket_label($dbc, $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc()) : '').'</td>
		</tr>
	</table>';
	$site_list = [];
	if(count(array_filter($siteid)) > 0) {
		$site_list = $siteid;
	} else if(!is_array($siteid) && $siteid > 0) {
		$site_list[] = $siteid;
	} else {
		$site_ids = $dbc->query("SELECT `tickets`.`siteid` FROM `ticket_attached` `time` LEFT JOIN `tickets` ON `time`.`ticketid`=`tickets`.`ticketid` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `time`.`deleted`=0 AND `tickets`.`deleted`=0 AND IFNULL(`project`.`deleted`,0)=0 AND `time`.`src_table` LIKE 'Staff%' AND `time`.`date_stamp` BETWEEN '$starttime' AND '$endtime' AND `tickets`.`created_date` BETWEEN '$createstart' AND '$createend' AND '$ticketid' IN (`time`.`ticketid`,'') AND '$projectid' IN (`tickets`.`projectid`,'') AND CONCAT(',',IFNULL(`tickets`.`businessid`,''),',',IFNULL(`tickets`.`clientid`,''),',',IFNULL(`project`.`clientid`,''),',',IFNULL(`project`.`businessid`,''),',') LIKE '%,".($businessid ?: '%').",%' AND '#".($disable_staff ?: '')."#' NOT LIKE CONCAT('%#',`tickets`.`siteid`,'|',`time`.`date_stamp`,'|',`time`.`item_id`,'#%') AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_comment` WHERE `type`='service_extra_billing' AND `deleted`=0) GROUP BY `tickets`.`siteid`");
		if($site_ids->num_rows > 0) {
			while($siteid = $site_ids->fetch_assoc()) {
                foreach(explode(',',$siteid['siteid']) as $site_id) {
                    if($site_id > 0) {
                        $site_list[] = $site_id;
                    }
				}
			}
		}
	}
	$total_count = 0;
	$rowsPerPage = 25;
	if($output_mode == 'print') {
		$rowsPerPage = PHP_INT_MAX;
	}
	$pageNum = $_GET['page'];
	$offset = $pageNum * $rowsPerPage;
	$cur_row = 0;
	if(count($site_list) > 0) {
		foreach($site_list as $siteid) {
			$report_data = '';
			$siteid = filter_var($siteid,FILTER_SANITIZE_STRING);
			$site_head = $report_head;
			foreach(sort_contacts_query($dbc->query("SELECT `display_name`,`site_name`,`mailing_address`,`city`,`province`,`zip_code`,`ship_to_address`,`ship_city`,`ship_state`,`ship_zip`,`business_street`,`business_city`,`business_state`,`business_zip` FROM `contacts` WHERE `contactid`='$siteid'")) as $site) {
				$site_head .= '<table width="100%" style="border:1px solid black;">
					<tr>
						<td style="padding:0.5em;">'.$site['full_name'].'<br />
						'.trim(empty($site['mailing_address']) ? (empty($site['ship_to_address']) ? $site['business_street'].' '.$site['business_city'].', '.$site['business_state'].' '.$site['business_zip'] : $site['ship_to_address'].' '.$site['ship_city'].', '.$site['ship_state'].' '.$site['ship_zip']) : $site['mailing_address'].' '.$site['city'].', '.$site['province'].' '.$site['zip_code'],', ').'</td>
					</tr>
				</table>';
			}
			$date_range = $dbc->query("SELECT SUM(IFNULL(`time_cards`.`total_hrs`,0)) `hours`, `time_cards`.`date` `date_stamp` FROM `time_cards` LEFT JOIN `tickets` ON `tickets`.`ticketid` = `time_cards`.`ticketid` LEFT JOIN `ticket_attached` `time` ON `time`.`id` = `time_cards`.`ticket_attached_id` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `time_cards`.`ticketid` > 0 AND IFNULL(`time`.`deleted`,0)=0 AND `tickets`.`deleted`=0 AND `time_cards`.`deleted`=0 AND IFNULL(`project`.`deleted`,0)=0 AND `time_cards`.`date` BETWEEN '$starttime' AND '$endtime' AND `tickets`.`created_date` BETWEEN '$createstart' AND '$createend' AND '$ticketid' IN (`time_cards`.`ticketid`,'') AND '$projectid' IN (`tickets`.`projectid`,'') AND CONCAT(',',IFNULL(`tickets`.`siteid`,''),',',IFNULL(`project`.`siteid`,''),',') LIKE '%,$siteid,%' AND CONCAT(',',IFNULL(`tickets`.`businessid`,''),',',IFNULL(`tickets`.`clientid`,''),',',IFNULL(`project`.`clientid`,''),',',IFNULL(`project`.`businessid`,''),',') LIKE '%,".($businessid ?: '%').",%' AND '#".($disable_staff ?: '%')."#' NOT LIKE CONCAT('%#',`tickets`.`siteid`,'|',`time_cards`.`date`,'|',`time_cards`.`staff`,'#%') AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_comment` WHERE `type`='service_extra_billing' AND `deleted`=0) GROUP BY `time_cards`.`date`");
			if($date_range->num_rows > 0) {
				$site_head .= '<table border="0" class="table no-border" width="100%" style="'.$table_style.'">';
				$ticket_types = explode(',',get_config($dbc, 'ticket_tabs'));
				while($date = $date_range->fetch_assoc()) {
					$sum_hours = 0;
					$staff_list = [];
					$display_total = false;
					// Date Title
					$date_head = '<tr>
						<td colspan="2" style="background-color:#CCCCCC;border:0 solid black;">'.date('D-M-j-Y',strtotime($date['date_stamp'])).'</td>
						<td style="background-color:#CCCCCC;border:0 solid black;text-align:right;" colspan="3">Total Extra Billing Hours for day: '.number_format($date['hours'],2).'</td>
					</tr>';
					$details = $dbc->query("SELECT GROUP_CONCAT(`time_cards`.`staff`) `staff`, SUM(IFNULL(`time_cards`.`total_hrs`,0)) `hours`, TIME_FORMAT(MIN(IFNULL(STR_TO_DATE(`time_cards`.`start_time`, '%l:%i %p'),STR_TO_DATE(`time_cards`.`start_time`, '%H:%i'))),'%h:%i %p') `start`, TIME_FORMAT(MAX(IFNULL(STR_TO_DATE(`time_cards`.`end_time`, '%l:%i %p'),STR_TO_DATE(`time_cards`.`end_time`, '%H:%i'))),'%h:%i %p') `end`, GROUP_CONCAT(`time_cards`.`ticketid`) `tickets` FROM `time_cards` LEFT JOIN `tickets` ON `tickets`.`ticketid` = `time_cards`.`ticketid` LEFT JOIN `ticket_attached` `time` ON `time`.`id` = `time_cards`.`ticket_attached_id` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `time_cards`.`ticketid` > 0 AND IFNULL(`time`.`deleted`,0)=0 AND `tickets`.`deleted`=0 AND `time_cards`.`deleted`=0 AND IFNULL(`project`.`deleted`,0)=0 AND '$ticketid' IN (`time_cards`.`ticketid`,'') AND '$projectid' IN (`tickets`.`projectid`,'') AND CONCAT(',',IFNULL(`tickets`.`siteid`,''),',',IFNULL(`project`.`siteid`,''),',') LIKE '%,$siteid,%' AND CONCAT(',',IFNULL(`tickets`.`businessid`,''),',',IFNULL(`tickets`.`clientid`,''),',',IFNULL(`project`.`clientid`,''),',',IFNULL(`project`.`businessid`,''),',') LIKE '%,".($businessid ?: '%').",%' AND `time_cards`.`date`='{$date['date_stamp']}' AND '#".($disable_staff ?: '%')."#' NOT LIKE CONCAT('%#',`tickets`.`siteid`,'|',`time_cards`.`date`,'|',`time_cards`.`staff`,'#%') AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_comment` WHERE `type`='service_extra_billing' AND `deleted`=0) AND `time_cards`.`staff` > 0".(!in_array('ticket_activity_staff_group', $report_fields) ? " GROUP BY `time_cards`.`staff`" : " GROUP BY `time_cards`.`ticketid`"));
					while($detail = $details->fetch_assoc()) {
						$cur_row++;
						if($cur_row > $offset && $cur_row <= $offset + $rowsPerPage) {
							$display_total = true;
							$report_data .= $site_head.$date_head;
							$site_head = $date_head = '';
							//Tasks Details
							$types = [];
							$notes = [];
							$tickets = [];
							foreach(array_filter(array_unique(explode(',',$detail['tickets']))) as $ticket) {
								if($ticket > 0) {
									$note_list = $dbc->query("SELECT * FROM `ticket_comment` WHERE `ticketid`='$ticket' AND `deleted`=0 AND `created_date`='".date('D-M-j-Y',strtotime($date['date_stamp']))."' AND `created_by`='{$detail['item_id']}'");
									while($note = $note_list->fetch_assoc()) {
										$notes[] = html_entity_decode($note['comment']);
									}
									if(empty($tickets[$ticket])) {
										$tickets[$ticket] = '<a href="../Ticket/index.php?edit='.$ticket.'&date='.date('Y-m-d',strtotime($date['date_stamp'])).'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\',\'auto\',true,true); return false;">'.get_ticket_label($dbc, $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticket'")->fetch_assoc()).'</a>';
									}
									$type = get_field_value('ticket_type', 'tickets', 'ticketid', $ticket);
									if($type != '') {
										foreach($ticket_types as $type_name) {
											if(config_safe_str($type_name) == $type) {
												$types[] = $type_name;
											}
										}
									}
								}
							}
							$report_data .= '<tr>
								<td style="width:10%;">Task Name:</td>
								<td style="width:45%;">'.implode(', ',array_unique($types)).'</td>
								<td style="width:10%;">'.($hide_staff != 'hide' ? 'Staff:' : '').'</td>
								<td style="width:35%;" colspan="2">';
								foreach(array_filter(array_unique(explode(',',$detail['staff']))) as $staff) {
									$report_data .= '<label class="form-checkbox any-width">'.($hide_staff != 'hide' ? '<input type="checkbox" class="inline" style="display:none;" name="disable_staff[]" value="'.$siteid.'|'.$date['date_stamp'].'|'.$staff.'">'.get_contact($dbc, $staff) : '').'</label>';
									$staff_list[] = $staff;
								}
								$report_data .= '</td>
							</tr>';
							$report_data .= '<tr>
								<td style="width:10%;">Start Time:</td>
								<td style="width:45%;">'.$detail['start'].'</td>
								<td style="width:10%;">Total Hours:</td>
								<td style="width:35%;">'.number_format($detail['hours'],2).'</td>
							</tr>';
							$report_data .= '<tr>
								<td style="width:10%;">End Time:</td>
								<td style="width:45%;">'.$detail['end'].'</td>
								<td style="width:10%;">'.($hide_wo != 'hide' ? TICKET_NOUN : '').':</td>
								<td style="width:35%;" colspan="2" rowspan="2">'.($hide_wo != 'hide' ? implode(', ',$tickets) : '').'</td>
							</tr>';
							$report_data .= '<tr>
								<td style="width:10%;">Total Staff on Site:</td>
								<td style="width:45%;">'.(count(array_filter(array_unique(explode(',',$detail['staff']))))).'</td>
								<td style="width:10%;"></td>
							</tr>';
							$report_data .= '<tr>
								<td style="width:10%;">Notes:</td>
								<td colspan="2">'.implode(', ',$notes).'</td>
							</tr>';
							// Task Title
							$report_data .= '<tr>
								<td></td>
								<td colspan="2" style="background-color:#CCCCCC;border:0 solid black;">Service</td>
								<td style="background-color:#CCCCCC;border:0 solid black;" colspan="2">Total Hours</td>
							</tr>';
							// Tasks List
							$detail_task = $dbc->query("SELECT `time_cards`.`type_of_time`, SUM(IFNULL(`time_cards`.`total_hrs`,0)) `hours` FROM `time_cards` WHERE `time_cards`.`ticketid` > 0 AND `time_cards`.`date`='".$date['date_stamp']."' AND `time_cards`.`deleted`=0 AND `time_cards`.`staff` IN (".$detail['staff'].") AND `time_cards`.`ticketid` IN (".$detail['tickets'].") GROUP BY `time_cards`.`type_of_time`");
							while($task = $detail_task->fetch_assoc()) {
								$report_data .= '<tr>
									<td></td>
									<td colspan="2">'.$task['type_of_time'].'</td>
									<td colspan="2">'.number_format($task['hours'],2).'</td>
								</tr>';
								$sum_hours += $task['hours'];
							}
                            $material_list = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid` IN ('".implode("','", explode(',',$detail['tickets']))."') AND `date_stamp`='".$date['date_stamp']."' AND `src_table`='material' AND `deleted`=0 AND (`item_id` > 0 OR `description` != '')");
                            if($material_list->num_rows > 0) {
                                $report_data .= '<tr>
                                    <td></td>
                                    <td colspan="2" style="background-color:#CCCCCC;border:0 solid black;">Materials on '.TICKET_TILE.'</td>
                                    <td style="background-color:#CCCCCC;border:0 solid black;" colspan="2">Quantity</td>
                                </tr>';
                                // Tasks List
                                while($material = $material_list->fetch_assoc()) {
                                    $report_data .= '<tr>
                                        <td></td>
                                        <td colspan="2">'.($material['item_id'] > 0 ? get_field_value('name','material','materialid',$material['item_id']) : $material['description']).'</td>
                                        <td colspan="2">'.round($material['qty'],2).'</td>
                                    </tr>';
                                }
                            }
							$report_data .= '<tr><td colspan="5" style="border-bottom:1px solid black;"></td></tr>';
						}
					}
					if($display_total) {
						$report_data .= '<tr style="font-weight:bold;"><td style="text-align:left">Total Hours:</td><td style="text-align:right;">'.number_format($sum_hours,2).'</td><td style="text-align:left">Staff on Site:</td><td style="text-align:right;" colspan="2">'.count(array_unique(array_filter($staff_list))).'</td></tr><tr><td colspan="5"><hr></td></tr>';
					}
				}
				$report_data .= '</table>';
			} else {
				$report_data .= '<table border="0" class="table no-border" width="100%" style="'.$table_style.'"><tr><td style="border-bottom:1px solid black;"><h3>No Hours Found</h3></td></tr></table>';
			}
			$report_pages[] = $report_data;
		}
	} else {
		$report_pages[] = $report_head.'<h3> No Sites Selected</h3>';
	}
	$_SERVER['page_rows'] = $cur_row;
    return $report_pages;
}

?>
<script>
$('document').ready(function() {
    var tables = $('table');

    tables.map(function(idx, table) {
        var rows = $(table).find('tbody > tr');
        rows.map(function(idx, row){
            if(idx%2 == 0) {
                $(row).css('background-color', '#e6e6e6');
            }
        })
    })
})
</script>