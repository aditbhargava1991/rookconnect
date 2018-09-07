<?php
/*
Dashboard
*/
if (isset($_POST['send_follow_up_email'])) {
    for($i = 0; $i < count($_POST['check_send_email']); $i++) {
        $check_send_email = explode('_', $_POST['check_send_email'][$i]);
        $staffid = $check_send_email[0];
        $safetyid = $check_send_email[1];
        $assignid = $check_send_email[2];

        $email = get_email($dbc, $staffid);

        $manual_type = get_manual($dbc, $safetyid, 'manual_type');

        $email_body = "Please login through the software and click on the link below. Sign in the signature box to confirm you understand and will adhere to this policy. If you have any questions or concerns, add them in the comment section. <br><br>";
        $email_body .= 'Safety Form: <a target="_blank" href="'.$_SERVER['SERVER_NAME'].'/Safety/add_manual.php?safetyid='.$safetyid.'&formid='.$assignid.'&action=view">Click Here</a><br>';

        $subject = 'Follow Up: Safety Form Assigned to you for Review';

        //Mail
        send_email('', $email, '', '', $subject, $email_body, '');
        //Mail
    }

    echo '<script type="text/javascript"> alert("Follow up Sent to staff"); window.location.replace("index.php?reports=view"); </script>';
}

if ( isset($_GET['send_rejected_email']) ) {
    $staffid  = ( isset($_GET['staffid']) ) ? preg_replace('/[^0-9]/', '', $_GET['staffid']) : '';
    $safetyid = ( isset($_GET['safetyid']) ) ? preg_replace('/[^0-9]/', '', $_GET['safetyid']) : '';
    $assignid = ( isset($_GET['assignid']) ) ? preg_replace('/[^0-9]/', '', $_GET['assignid']) : '';

    $email = get_email($dbc, $staffid);

    $manual_type = get_manual($dbc, $safetyid, 'manual_type');

    $email_body = "Safety form rejected. Please login through the software and click on the link below. Sign in the signature box to confirm you understand and will adhere to this policy. If you have any questions or concerns, add them in the comment section. <br><br>";
    $email_body .= 'Safety Form: <a target="_blank" href="'.$_SERVER['SERVER_NAME'].'/Safety/add_manual.php?safetyid='.$safetyid.'&formid='.$assignid.'&action=view">Click Here</a><br>';
    
    $subject = 'Rejected: Safety Form Assigned to you for Review';

    //Mail
    send_email('', $email, '', '', $subject, $email_body, '');
    //Mail

    echo '<script type="text/javascript"> alert("Rejected email sent to staff"); window.location.replace("index.php?reports=view"); </script>';
}

if((!empty($_GET['action'])) && ($_GET['action'] == 'send_followup_email')) {
    $safetyid = $_GET['safetyid'];
    $assignid = $_GET['assignid'];
    $staffid = $_GET['staffid'];

    //Mail
    $to = get_email($dbc, $staffid);
    $subject = 'Follow Up: Safety Form Assigned to you for Review';
    $message = '<html><body>';
    $message .= "Please login through the software and click on the link below. Sign in the signature box to confirm you understand and will adhere to this policy. If you have any questions or concerns, add them in the comment section. <br><br>";
    $message .= 'Safety Form: <a target="_blank" href="'.$_SERVER['SERVER_NAME'].'/Safety/add_manual.php?safetyid='.$safetyid.'&formid='.$assignid.'&action=view">Click Here</a><br>';
    $message .= '</body></html>';
    send_email('', $to, '', '', $subject, $message, '');

    //Mail
    echo '<script type="text/javascript"> alert("Follow up Sent to staff"); window.location.replace("index.php?reports=view"); </script>';
}

?>

<script type="text/javascript">
    function managerApproval(sel) {
        var id       = sel.id;
        var id_split = id.split('_');
        action       = id_split[0];
        safetyid     = id_split[1];
        staffid      = $('#'+id).data('staffid');
        assignid     = $('#'+id).data('assignid');
        
        $.ajax({
            type: "GET",
            url: "manual_ajax_all.php?fill=managerApproval&action="+action+"&safetyid="+safetyid,
            dataType: "html",
            success: function(response){
                if ( action=='approve' ) {
                    location.reload();
                } else if ( action=='reject' ) {
                    location.replace('index.php?reports=view&type=safety&send_rejected_email=yes&safetyid='+safetyid+'&staffid='+staffid+'&assignid='+assignid);
                }
            }
        });
    }
</script>

<?php include_once ('../navigation.php'); ?>

<div class="scale-to-fill has-main-screen">

	<form name="form_sites" method="post" action="" class="form-horizontal main-screen pad-top pad-horizontal" role="form">
        <?php
        $contactid = '';
        $category = '';
        $heading = '';
        $status = '';
        $s_start_date = '';
        $s_end_date = '';
		$formid = '';

        if(!empty($_POST['contactid'])) {
            $contactid = $_POST['contactid'];
        }
        if(!empty($_POST['category'])) {
            $category = $_POST['category'];
        }
        if(!empty($_POST['heading'])) {
            $heading = $_POST['heading'];
        }
        if(!empty($_POST['formid'])) {
            $formid = $_POST['formid'];
        }
        if(!empty($_POST['status'])) {
            $status = $_POST['status'];
        }
        if(!empty($_POST['s_start_date'])) {
            $s_start_date = $_POST['s_start_date'];
        }
        if(!empty($_POST['s_end_date'])) {
            $s_end_date = $_POST['s_end_date'];
        }
        if (isset($_POST['display_all_asset'])) {
            $contactid = '';
            $category = '';
            $heading = '';
            $status = '';
			$formid = '';
            $s_start_date = '';
            $s_end_date = '';
        }
        ?>

		<div class="form-group col-md-6 col-sm-12">
        <div class="col-sm-4">
          <label for="ship_country" class="control-label">
			<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Staff:
		  </label>
        </div>
          <div class="col-sm-8">
                <select data-placeholder="Select Staff..." name="contactid" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
						<?php
							$query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"));
							foreach($query as $staff) {
								echo "<option ".($staff['contactid'] == $contactid ? 'selected = "selected"' : '')." value='". $staff['contactid']."'>".$staff['full_name'].'</option>';
							}
						?>
                </select>

          </div>
		</div>

		<div class="form-group col-md-6 col-sm-12">
        <div class="col-sm-4">
          <label for="ship_zip" class="control-label">
			<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Topic:
		  </label>
        </div>
          <div class="col-sm-8">
                <select data-placeholder="Select Tab..." name="category" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $query = mysqli_query($dbc,"SELECT distinct(tab) FROM safety WHERE deleted=0 order by tab");
                    while($row = mysqli_fetch_array($query)) {
                        if ($category == $row['tab']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        ?>
                        <option <?php echo $selected; ?> value='<?php echo $row['tab']; ?>' ><?php echo $row['tab']; ?></option>
                    <?php }
                  ?>
                </select>
          </div>
		</div>

		<div class="form-group col-md-6 col-sm-12">
        <div class="col-sm-4">
          <label for="ship_zip" class="control-label">
			<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Heading:
		  </label>
        </div>
          <div class="col-sm-8">
                <select data-placeholder="Select Heading..." name="heading" class="chosen-select-deselect form-control" width="350">
                  <option value=""></option>
                  <?php
                    $query = mysqli_query($dbc,"SELECT distinct(heading) FROM safety WHERE deleted=0 order by heading");
                    while($row = mysqli_fetch_array($query)) {
                        if ($heading == $row['heading']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        ?>
                        <option <?php echo $selected; ?> value='<?php echo $row['heading']; ?>' ><?php echo $row['heading']; ?></option>
                    <?php }
                  ?>
                </select>
          </div>
		</div>

		<div class="form-group col-md-6 col-sm-12">
        <div class="col-sm-4">
          <label for="ship_zip" class="control-label">
			<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Form:
		  </label>
        </div>
          <div class="col-sm-8">
                <select data-placeholder="Select Form..." name="formid" class="chosen-select-deselect form-control" width="350">
                  <option value=""></option>
                  <?php
                    $query = mysqli_query($dbc,"SELECT `safetyid`, `tab`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading` FROM safety WHERE deleted=0 order by `tab`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)");
                    while($row = mysqli_fetch_array($query)) { ?>
                        <option <?= $formid == $row['safetyid'] ? 'selected' : '' ?> value='<?php echo $row['safetyid']; ?>' ><?= $row['tab'].': '.($row['third_heading_number'] != '' ? $row['third_heading_number'].' '.$row['third_heading'] : ($row['sub_heading_number'] != '' ? $row['sub_heading_number'].' '.$row['sub_heading'] : $row['heading_number'].' '.$row['heading'])) ?></option>
                    <?php } ?>
                </select>
          </div>
		</div>

		<div class="form-group col-md-6 col-sm-12">
        <div class="col-sm-4">
          <label for="ship_zip" class="control-label">
			<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Status:
		  </label>
        </div>
          <div class="col-sm-8">
                <select data-placeholder="Select Status..." name="status" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <option <?php if ($status=='Deadline Past') echo 'selected="selected"';?> value="Deadline Past">Deadline Passed</option>
                  <option <?php if ($status=='Deadline Today') echo 'selected="selected"';?> value="Deadline Today">Deadline Today</option>
                </select>
          </div>
		</div>

		<div class="form-group col-md-6 col-sm-12">
        <div class="col-sm-4">
            <label for="site_name" class="control-label" style="padding-top:5px;">
				<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down calendar."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Start Date:
			</label>
        </div>
            <div class="col-sm-8">
                <input name="s_start_date" type="text" class="datepicker form-control" value="<?php echo $s_start_date; ?>">
            </div>
		</div>

		<div class="form-group col-md-6 col-sm-12">
        <div class="col-sm-4">
            <label for="first_name" class="control-label" style="padding-top:5px;">
				<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down calendar."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				End Date:
			</label>
        </div>
            <div class="col-sm-8">
                <input name="s_end_date" type="text" class="datepicker form-control" value="<?php echo $s_end_date; ?>">
            </div>
		</div>

        <?php if($type == 'safety') { ?>
		<div class="form-group col-md-6 col-sm-12">
          <label for="ship_zip" class="col-sm-4 control-label"><?= PROJECT_NOUN ?>#:</label>
          <div class="col-sm-8">
                <select data-placeholder="Select <?= PROJECT_NOUN ?>..." name="sectionid" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $query = mysqli_query($dbc,"SELECT distinct(sectionid) FROM bid_section WHERE deleted=0 order by sectionid");
                    while($row = mysqli_fetch_array($query)) {
                        if ($sectionid == $row['sectionid']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        ?>
                        <option <?php echo $selected; ?> value='<?php echo $row['sectionid']; ?>' ><?php echo $row['sectionid']; ?></option>
                    <?php }
                  ?>
                </select>
          </div>
        </div>
        <?php } ?>

		<div class="form-group">
			<div class="col-sm-12">
				<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to submit your search fields."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<button type="submit" name="reporting_client" value="Submit" class="btn brand-btn mobile-block">Search</button>

				<span class="popover-examples list-inline" style="margin:0 2px 0 12px;"><a data-toggle="tooltip" data-placement="top" title="Refreshes the page to display all Safety Reports."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<button type="submit" name="display_all_asset" value="Display All" class="btn brand-btn mobile-block">Display All</button>
			</div>
        </div>

        <br><br>

        <span class="pull-right">
            <img src="<?php echo WEBSITE_URL;?>/img/block/red.png" width="23" height="23" border="0" alt=""> Deadline Passed
            <img src="<?php echo WEBSITE_URL;?>/img/block/green.png" width="23" height="23" border="0" alt=""> Deadline Today
        </span><br><br>
        <?php

        if(isset($_POST['reporting_client'])) {
            $contactid = $_POST['contactid'];
            $category = $_POST['category'];
            $heading = $_POST['heading'];
            $formid = $_POST['formid'];
            $status = $_POST['status'];
            $s_start_date = $_POST['s_start_date'];
            $s_end_date = $_POST['s_end_date'];

            $query_check_credentials = "SELECT m.*, ms.`safetyid`, ms.`fieldlevelriskid`, ms.`assign_staff`, ms.`staffcheck`, ms.`done`, ms.`manager_approval`, '' `staffid`, '' `today_date`, 'form' `safety_type` FROM safety_attendance ms, safety m WHERE m.deleted=0 AND m.safetyid = ms.safetyid AND (m.tab='$category' OR m.heading='$heading' OR '$formid'=`m`.`safetyid`) UNION SELECT m.*, ms.`safetyid`, '' `fieldlevelriskid`, '' `assign_staff`, '' `staffcheck`, ms.`done`, '' `manager_approval`, ms.`staffid`, ms.`today_date`, 'manual' `safety_type` FROM safety_staff ms, safety m WHERE m.deleted=0 AND m.safetyid = ms.safetyid AND (m.tab='$category' OR m.heading='$heading' OR '$formid'=`m`.`safetyid`)";
            if($status == 'Deadline Past') {
                $query_check_credentials = "SELECT m.*, ms.`safetyid`, ms.`fieldlevelriskid`, ms.`assign_staff`, ms.`staffcheck`, ms.`done`, ms.`manager_approval`, '' `staffid`, '' `today_date`, 'form' `safety_type` FROM safety_attendance ms, safety m WHERE m.deleted=0 AND m.safetyid = ms.safetyid AND ms.done=0 AND DATE(NOW()) > DATE(m.deadline) AND '$formid' IN (`m`.`safetyid`,'') UNION SELECT m.*, ms.`safetyid`, '' `fieldlevelriskid`, '' `assign_staff`, '' `staffcheck`, ms.`done`, '' `manager_approval`, ms.`staffid`, ms.`today_date`, 'manual' `safety_type` FROM safety_staff ms, safety m WHERE m.deleted=0 AND m.safetyid = ms.safetyid AND ms.done=0 AND DATE(NOW()) > DATE(m.deadline) AND '$formid' IN (`m`.`safetyid`,'')";
            }
            if($status == 'Deadline Today') {
                $query_check_credentials = "SELECT m.*, ms.`safetyid`, ms.`fieldlevelriskid`, ms.`assign_staff`, ms.`staffcheck`, ms.`done`, ms.`manager_approval`, '' `staffid`, '' `today_date`, 'form' `safety_type` FROM safety_attendance ms, safety m WHERE m.deleted=0 AND m.safetyid = ms.safetyid AND ms.done=0 AND DATE(NOW()) = DATE(m.deadline) AND '$formid' IN (`m`.`safetyid`,'') UNION SELECT m.*, ms.`safetyid`, '' `fieldlevelriskid`, '' `assign_staff`, '' `staffcheck`, ms.`done`, '' `manager_approval`, ms.`staffid`, ms.`today_date`, 'manual' `safety_type` FROM safety_staff ms, safety m WHERE m.deleted=0 AND m.safetyid = ms.safetyid AND ms.done=0 AND DATE(NOW()) = DATE(m.deadline) AND '$formid' IN (`m`.`safetyid`,'')";
            }
            if($s_start_date != '' && $s_end_date != '') {
                $query_check_credentials = "SELECT m.*, ms.`safetyid`, ms.`fieldlevelriskid`, ms.`assign_staff`, ms.`staffcheck`, ms.`done`, ms.`manager_approval`, '' `staffid`, '' `today_date`, 'form' `safety_type` FROM safety_attendance ms, safety m WHERE m.deleted=0 AND m.safetyid = ms.safetyid AND m.deadline >= '$s_start_date' AND m.deadline <= '$s_end_date' AND '$formid' IN (`m`.`safetyid`,'') UNION SELECT m.*, ms.`safetyid`, '' `fieldlevelriskid`, '' `assign_staff`, '' `staffcheck`, ms.`done`, '' `manager_approval`, ms.`staffid`, ms.`today_date`, 'manual' `safety_type` FROM safety_staff ms, safety m WHERE m.deleted=0 AND m.safetyid = ms.safetyid AND m.deadline >= '$s_start_date' AND m.deadline <= '$s_end_date' AND '$formid' IN (`m`.`safetyid`,'')";
            }
        } else if(empty($_GET['action'])) {
            $query_check_credentials = "SELECT m.*, ms.`safetyid`, ms.`fieldlevelriskid`, ms.`assign_staff`, ms.`staffcheck`, ms.`done`, ms.`manager_approval`, '' `staffid`, '' `today_date`, 'form' `safety_type` FROM safety_attendance ms, safety m WHERE m.deleted=0 AND m.safetyid = ms.safetyid UNION SELECT m.*, ms.`safetyid`, '' `fieldlevelriskid`, '' `assign_staff`, '' `staffcheck`, ms.`done`, '' `manager_approval`, ms.`staffid`, ms.`today_date`, 'manual' `safety_type` FROM safety_staff ms, safety m WHERE m.deleted=0 AND m.safetyid = ms.safetyid";
            
            $include_incident_reports = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_incident_report` WHERE `row_type` = ''"))['safety_report'];
            if ($include_incident_reports == 1) {
                $query_incident_reports = "SELECT * FROM `incident_report` WHERE `safetyid` IS NULL";
            }
        }

        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered'>";
            echo '<tr class="hidden-xs hidden-sm">
                <th>Staff</th>
                <th>Topic (Sub Tab)</th>
                <th>Heading</th>
                <th>Sub Section Heading</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Signed Off&nbsp;&nbsp;<button type="submit" name="send_follow_up_email" value="Submit" class="btn brand-btn">Send</button></th>';
                if ( ROLE=='admin' || ROLE=='super' || ROLE==',admin,' || ROLE==',super,' ) {
                    echo '<th>Manager Approval</th>';
                }
                echo '</tr>';
        } else {
            echo "<h2>No Record Found.</h2>";
        }
		$staff_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""),MYSQLI_ASSOC);
		foreach($staff_list as $i => $arr) {
			$staff_list[$i]['full_name'] = decryptIt($arr['first_name']).' '.decryptIt($arr['last_name']);
		}

        while($row = mysqli_fetch_array( $result ))
        {
            $safetyid = $row['safetyid'];
            $fieldlevelriskid = $row['fieldlevelriskid'];
            $done = $row['done'];
            $staffname = $row['assign_staff'];
			$staffid = 0;
            if($row['safety_type'] == 'manual') {
                $staffid = $row['staffid'];
            } else {
    			foreach($staff_list as $arr) {
    				if($staffid == 0 && $arr['full_name'] == $staffname) {
    					$staffid = $arr['contactid'];
    				}
    			}
            }
            $today = date('Y-m-d');
            $color = '';
            $signed_off = $row['today_date'];

            if($row['done'] == 0) {
                $color = 'style="background-color: lightgreen;"';
            }

            echo "<tr>";
            echo '<td data-title="Contact Person">' . ($row['safety_type'] == 'manual' ? get_contact($dbc, $row['staffid']) : $row['assign_staff']) . '</td>';
            echo '<td data-title="Code">' . $row['category'] . '</td>';
            echo '<td data-title="Code">' . $row['heading_number'].' '.$row['heading'] . '</td>';
            echo '<td data-title="Code">' . $row['sub_heading_number'].' '.$row['sub_heading'] . '</td>';
            echo '<td data-title="Code">' . $row['deadline'] . '</td>';

            echo '<td data-title="Code">';
            if(($today > $deadline) && ($row['done'] == 0)) {
                echo '<img src="'.WEBSITE_URL.'/img/block/red.png" width="22" height="22" border="0" alt=""> '.$row['today_date'];
            }
            if(($today == $deadline) && ($row['done'] == 0)) {
                echo '<img src="'.WEBSITE_URL.'/img/block/green.png" width="22" height="22" border="0" alt=""> '.$row['today_date'];
            }
            if($row['done'] == 1) {
                echo '<img src="'.WEBSITE_URL.'/img/checkmark.png" width="22" height="22" border="0" alt="">';
            }
            echo '</td>';

            if($row['done'] == 1) {

                if($row['safety_type'] == 'manual') {
                    $heading_number = $row['heading_number'];
                    $sub_heading_number = $row['sub_heading_number'];
                    $heading = $row['heading'];
                    $sub_heading = $row['sub_heading'];
                    $third_heading_number = $row['third_heading_number'];
                    $third_heading = $row['third_heading'];
                    $pdf_name = '';
                    if ($third_heading != '') {
                        $pdf_html .= "<h3>$third_heading_number - $third_heading</h3>";
                        $pdf_name = config_safe_str($third_heading_number.'-'.$third_heading);
                    } else if ($sub_heading != '') {
                        $pdf_html .= "<h3>$sub_heading_number - $sub_heading</h3>";
                        $pdf_name = config_safe_str($sub_heading_number.'-'.$sub_heading);
                    } else if ($heading != '') {
                        $pdf_html .= "<h3>$heading_number - $heading</h3>";
                        $pdf_name = config_safe_str($heading_number.'-'.$heading);
                    }
                    $pdf_path = WEBSITE_URL.'/Safety/download/'.$pdf_name.'_'.$row['staffid'].'_'.$row['today_date'].'.pdf';
                } else {
                    $pdf_path = safety_pdf($dbc, $safetyid, $fieldlevelriskid);
                }

                $pdf = '<a target="_blank" href="'.$pdf_path.'"><img src="'.WEBSITE_URL.'/img/pdf.png" width="'.$img_width.'" height="'.$img_height.'" border="0" alt=""></a>';
                echo '<td data-title="Code">'.$row['today_date'] .'&nbsp;'.$pdf.'</td>';
            } else {
                echo '<td data-title="Code">';
                echo '<a href="index.php?reports=view&staffid='.$staffid.'&assignid='.$fieldlevelriskid.'&safetyid='.$row['safetyid'].'&action=send_followup_email">Send</a>';
                echo '&nbsp;&nbsp;<input name="check_send_email[]" type="checkbox" value="'.$staffid.'_'.$row['safetyid'].'_'.$fieldlevelriskid.'" class="form-control check_send_email" style="width:25px;"/></td>';
            }
            
            if ( ROLE=='admin' || ROLE=='super' || ROLE==',admin,' || ROLE==',super,' ) {
                echo '<td data-title="Manager Approval">';
                    if ( $row['manager_approval']=='0' ) {
                        $approve = '<a href="javascript:void(0);" id="approve_'. $row['safetyattid'] .'" onclick="managerApproval(this);">Approve</a>';
                        $reject  = '<a href="javascript:void(0);" id="reject_'. $row['safetyattid'] .'" data-staffid="'. $staffid .'" data-assignid="'. $fieldlevelriskid .'" onclick="managerApproval(this);">Reject</a>';
                    } elseif ( $row['manager_approval']=='1' ) {
                        $approve = 'Approved';
                        $reject  = '<a href="javascript:void(0);" id="reject_'. $row['safetyattid'] .'" data-staffid="'. $staffid .'" data-assignid="'. $fieldlevelriskid .'" onclick="managerApproval(this);">Reject</a>';
                    } elseif ( $row['manager_approval']=='2' ) {
                        $approve = '<a href="javascript:void(0);" id="approve_'. $row['safetyattid'] .'" onclick="managerApproval(this);">Approve</a>';
                        $reject  = 'Rejected';
                    }
                    echo $approve . ' | ' . $reject;
                echo '</td>';
            }

            echo "</tr>";
        }

        $result_incident = mysqli_query($dbc, $query_incident_reports);

        while ($row = mysqli_fetch_array($result_incident)) {
            echo "<tr>";
            echo '<td data-title="Contact Person">' . get_contact($dbc, $row['contactid']) . '</td>';
            echo '<td data-title="Code">N/A</td>';
            echo '<td data-title="Code">N/A</td>';
            echo '<td data-title="Code">' . $row['type'] . '</td>';
            echo '<td data-title="Code"></td>';
            echo '<td data-title="Code"><img src="'.WEBSITE_URL.'/img/checkmark.png" width="22" height="22" border="0" alt=""></td>';

            $pdf_path = '../Incident Report/download/incident_report_'.$row['incidentreportid'].'.pdf';
            $pdf = '<a target="_blank" href="'.$pdf_path.'"><img src="'.WEBSITE_URL.'/img/pdf.png" width="'.$img_width.'" height="'.$img_height.'" border="0" alt=""></a>';
            echo '<td data-title="Code">'.$row['today_date'] .'&nbsp;'.$pdf.'</td>';

            echo "</tr>";
        }

        echo '</table>';
        ?>


	</div>

        
	</form>
	</div>
	</div>

</div>

<?php include ('../footer.php');

function safety_pdf($dbc, $safetyid, $fieldlevelriskid) {
    $form = get_safety($dbc, $safetyid, 'form');
    $user_form_id = get_safety($dbc, $safetyid, 'user_form_id');

    if($user_form_id > 0) {
        $user_pdf = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_pdf` WHERE `pdf_id` = '$fieldlevelriskid'"));
        $pdf_path = 'download/'.$user_pdf['generated_file'];
        return $pdf_path;
    } else {
        if($form == 'Field Level Hazard Assessment') {
            $pdf_path = 'field_level_hazard_assessment/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'Hydrera Site Specific Pre Job Safety Meeting Hazard Assessment') {
            $pdf_path = 'hydrera_site_specificpre_job/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'Weekly Safety Meeting') {
            $pdf_path = 'weekly_safety_meeting/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'Tailgate Safety Meeting') {
            $pdf_path = 'tailgate_safety_meeting/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'Toolbox Safety Meeting') {
            $pdf_path = 'toolbox_safety_meeting/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'Daily Equipment Inspection Checklist') {
            $pdf_path = 'daily_equipment_inspection_checklist/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'AVS Hazard Identification') {
            $pdf_path = 'avs_hazard_identification/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'AVS Near Miss') {
            $pdf_path = 'near_miss_report/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'Incident Investigation Report') {
            $pdf_path = 'incident_investigation_report/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'Follow Up Incident Report') {
            $pdf_path = 'follow_up_incident_report/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Pre Job Hazard Assessment') {
            $pdf_path = 'pre_job_hazard_assessment/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Monthly Site Safety Inspections') {
            $pdf_path = 'monthly_site_safety_inspection/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Monthly Office Safety Inspections') {
            $pdf_path = 'monthly_office_safety_inspection/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Monthly Health and Safety Summary') {
            $pdf_path = 'monthly_health_and_safety_summary/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Trailer Inspection Checklist') {
            $pdf_path = 'trailer_inspection_checklist/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Misconduct Form') {
            $pdf_path = 'employee_misconduct_form/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Site Inspection Hazard Assessment') {
            $pdf_path = 'site_inspection_hazard_assessment/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }

        if($form == 'Weekly Planned Inspection Checklist') {
            $pdf_path = 'weekly_planned_inspection_checklist/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Equipment Inspection Checklist') {
            $pdf_path = 'equipment_inspection_checklist/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Equipment Training Record') {
            $pdf_path = 'employee_equipment_training_record/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Vehicle Inspection Checklist') {
            $pdf_path = 'vehicle_inspection_checklist/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Safety Meeting Minutes') {
            $pdf_path = 'safety_meeting_minutes/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Vehicle Damage Report') {
            $pdf_path = 'vehicle_damage_report/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Fall Protection Plan') {
            $pdf_path = 'fall_protection_plan/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Spill Incident Report') {
            $pdf_path = 'spill_incident_report/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'General Site Safety Inspection') {
            $pdf_path = 'general_site_safety_inspection/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Confined Space Entry Permit') {
            $pdf_path = 'confined_space_entry_permit/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Lanyards Inspection Checklist Log') {
            $pdf_path = 'lanyards_inspection_checklist_log/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'On The Job Training Record') {
            $pdf_path = 'on_the_job_training_record/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'General Office Safety Inspection') {
            $pdf_path = 'general_office_safety_inspection/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Full Body Harness Inspection Checklist Log') {
            $pdf_path = 'full_body_harness_inspection_checklist_log/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Confined Space Pre Entry Checklist') {
            $pdf_path = 'confined_space_entry_pre_entry_checklist/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Confined Space Entry Log') {
            $pdf_path = 'confined_space_entry_log/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Emergency Response Transportation Plan') {
            $pdf_path = 'emergency_response_transportation_plan/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Hazard Id Report') {
            $pdf_path = 'hazard_id_report/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Dangerous Goods Shipping Document') {
            $pdf_path = 'dangerous_goods_shipping_document/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Safe Work Permit') {
            $pdf_path = 'safe_work_permit/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Journey Management - Trip Tracking Form') {
            $pdf_path = 'journey_management_trip_tracking/download/hazard_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Motor Vehicle Accident Form') {
            $pdf_path = '../Incident Report/download/incident_report_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
    }
}
?>
