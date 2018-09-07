<?php
/*
Dashboard
*/
include ('../include.php');
error_reporting(0);

$cat_page = $_GET[ 'category' ];
if ( $cat_page == 'Agenda' ) {
	$cat_page = 'agenda.php';
} else if ($cat_page == 'Meeting') {
	$cat_page = 'meeting.php';
} else {
	$cat_page = 'how_to_guide.php';
}

if (isset($_POST['submit'])) {
    $default_business = filter_var($_POST['default_business'],FILTER_SANITIZE_STRING);
    $default_contact = filter_var($_POST['default_contact'],FILTER_SANITIZE_STRING);
    $subcommittee_types = filter_var($_POST['subcommittee_types'],FILTER_SANITIZE_STRING);
    $agenda_meeting = implode(',',$_POST['agenda_meeting']);
	$email_from = filter_var($_POST['email_from'],FILTER_SANITIZE_STRING);
	$email_subject = filter_var($_POST['email_subject'],FILTER_SANITIZE_STRING);
	$email_body = filter_var(htmlentities($_POST['email_body']),FILTER_SANITIZE_STRING);

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config_agendas_meetings"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_or_insert = "UPDATE `field_config_agendas_meetings` SET field_config='$agenda_meeting', `email_from`='$email_from', `email_subject`='$email_subject', `email_body`='$email_body', `default_business`='$default_business', `default_contact`='$default_contact', `subcommittee_types`='$subcommittee_types' WHERE `fieldconfigid` = 1";
    } else {
        $query_update_or_insert = "INSERT INTO `field_config_agendas_meetings` (`field_config`, `email_from`, `email_subject`, `email_body`, `default_business`, `default_contact`, `subcommittee_types`)
			VALUES ('$agenda_meeting', '$email_from', '$email_subject', '$email_body', '$default_business', '$default_contact', '$subcommittee_types')";
    }
    $result = mysqli_query($dbc, $query_update_or_insert);


	$email_logo = htmlspecialchars($_FILES['email_logo']['name'], ENT_QUOTES);
	if($email_logo != '') {
		move_uploaded_file($_FILES["email_logo"]["tmp_name"], "download/" . $_FILES["email_logo"]["name"]) ;
		$query_update = "UPDATE `field_config_agendas_meetings` SET `email_logo`='$email_logo' WHERE `fieldconfigid` = 1";
		$result = mysqli_query($dbc, $query_update);
	}

    echo '<script> window.location.replace("field_config_agenda_meeting.php?category='.$_GET['category'].'"); </script>';

}
?>
</head>
<body>

<?php include ('../navigation.php');
checkAuthorised('agenda_meeting'); ?>

<div class="container">
<div class="row">
<h1>Agendas &amp; Meetings</h1>
<div class="gap-top double-gap-bottom"><a href="<?= $cat_page; ?>" class="btn config-btn">Back to Dashboard</a></div>
<a href="am_inbox.php?settings=pdf&style_settings=design_styleA" class="btn brand-btn">PDF Design</a>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<?php
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_agendas_meetings"));
$value_config = ','.$get_field_config['field_config'].',';
?>

<div class="panel-group" id="accordion2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
                    Choose Fields for Agendas & Meetings<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_field" class="panel-collapse collapse in">
            <div class="panel-body">

			<div id='no-more-tables'>
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;<?= BUSINESS_CAT ?>
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Contact
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Date of Meeting".',') !== FALSE) { echo " checked"; } ?> value="Date of Meeting" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Date of Meeting
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Time of Meeting".',') !== FALSE) { echo " checked"; } ?> value="Time of Meeting" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Time of Meeting
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."End Time of Meeting".',') !== FALSE) { echo " checked"; } ?> value="End Time of Meeting" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;End Time of Meeting
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Location".',') !== FALSE) { echo " checked"; } ?> value="Location" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Location
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Meeting Requested by".',') !== FALSE) { echo " checked"; } ?> value="Meeting Requested by" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Meeting Requested by
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Meeting Objective".',') !== FALSE) { echo " checked"; } ?> value="Meeting Objective" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Meeting Objective
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Documents".',') !== FALSE) { echo " checked"; } ?> value="Documents" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Documents
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Items to Bring".',') !== FALSE) { echo " checked"; } ?> value="Items to Bring" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Items to Bring
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Company Attendees".',') !== FALSE) { echo " checked"; } ?> value="Company Attendees" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Staff Members
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Contact Attendees".',') !== FALSE) { echo " checked"; } ?> value="Contact Attendees" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Contact Attendees
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Add New Contact".',') !== FALSE) { echo " checked"; } ?> value="Add New Contact" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Add New Contact
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Project
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Agenda Topic".',') !== FALSE) { echo " checked"; } ?> value="Agenda Topic" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Agenda Topic
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Meeting Topic".',') !== FALSE) { echo " checked"; } ?> value="Meeting Topic" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Meeting Topic
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Service".',') !== FALSE) { echo " checked"; } ?> value="Service" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Service
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Agenda Notes".',') !== FALSE) { echo " checked"; } ?> value="Agenda Notes" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Agenda Notes
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tickets Waiting for QA".',') !== FALSE) { echo " checked"; } ?> value="Tickets Waiting for QA" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;<?= TICKET_TILE ?> Waiting for QA
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Email to all Company Attendees".',') !== FALSE) { echo " checked"; } ?> value="Email to all Company Attendees" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Email to all Staff Members
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Email to all Contact Attendees".',') !== FALSE) { echo " checked"; } ?> value="Email to all Contact Attendees" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Email to all Contact Attendees
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Meeting Notes".',') !== FALSE) { echo " checked"; } ?> value="Meeting Notes" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Meeting Notes
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Client Deliverables".',') !== FALSE) { echo " checked"; } ?> value="Client Deliverables" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Client Deliverables
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Company Deliverables".',') !== FALSE) { echo " checked"; } ?> value="Company Deliverables" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Company Deliverables
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Add Ticket".',') !== FALSE) { echo " checked"; } ?> value="Add Ticket" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Add <?= TICKET_NOUN ?>
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Add Task".',') !== FALSE) { echo " checked"; } ?> value="Add Task" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Add Task
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Time Tracking".',') !== FALSE) { echo " checked"; } ?> value="Time Tracking" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Time Tracking
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Sub Committee".',') !== FALSE) { echo " checked"; } ?> value="Sub Committee" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Sub-Committee
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="agenda_meeting[]">&nbsp;&nbsp;Heading
                        </td>
                    </tr>
                    <tr>

                    </tr>
                </table>
			  </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label">Default <?= BUSINESS_CAT ?>:</label>
                    <div class="col-sm-8">
                        <select name="default_business" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='".BUSINESS_CAT."' AND deleted=0"),MYSQLI_ASSOC));
                            foreach($query as $id) {
                                echo "<option ".($get_field_config['default_business'] == $id ? 'selected' : '')." value='". $id."'>".get_client($dbc, $id).'</option>';
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label">Default Contact:</label>
                    <div class="col-sm-8">
                        <select name="default_contact" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php
                            $cat = '';
                            $cat_list = [];
                            $this_list = [];
                            $query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE `deleted`=0 AND `status`=1 ORDER BY category");
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
                                    echo "<option ".($get_field_config['default_contact'] == $id ? 'selected' : '')." value='".$id."'>".decryptIt($names['name']).($names['name'].$names['first_name'].$names['last_name'] != '' ? ': ' : '').decryptIt($names['first_name'])." ".decryptIt($names['last_name']).'</option>';
                                }
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label">Sub-Committee Types:<br><em>Enter Sub-Committee types separated by a comma.</em></label>
                    <div class="col-sm-8">
                        <input type="text" name="subcommittee_types" class="form-control" value="<?= $get_field_config['subcommittee_types'] ?>">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_email" >
                    Configure Email for Agendas &amp; Meetings<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_email" class="panel-collapse collapse">
            <div class="panel-body">
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Logo:</label>
					<div class="col-sm-8">
						<input type="file" accept="image/*" class="form-control" name="email_logo">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Sending Email Address:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" value="<?php echo $get_field_config['email_from']; ?>" name="email_from">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Subject:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" value="<?php echo $get_field_config['email_subject']; ?>" name="email_subject">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Body:
						<p style="font-size:small; font-style:italic;">Use the following tags in the body and subject of the Email:<br />
						Business Names: [Business]<br />
						Date of Meeting: [Date]<br />
						Start Time of Meeting: [Start]<br />
						End Time of Meeting: [End]<br />
						Location: [Location]</p></label>
					<div class="col-sm-8">
						<textarea name="email_body"><?php echo $get_field_config['email_body']; ?></textarea>
						<p>Your body will be followed by the Meeting Details which are standard. Empty lines are omitted. An example based on your configuration is shown below.</p>
						<?php $business = get_client($dbc, $businessid);

						if($get_field_config['email_subject'] == '') {
							$subject = 'Meeting Note for Meeting'.($date_of_meeting != '' ? ' on '.$date_of_meeting : '');
						} else {
							$subject = $get_field_config['email_subject'];
						}
						$custom_body = html_entity_decode($get_field_config['email_body']);

						$email_body .= "<table width='100%' border='0'>";
						$email_body .= "<tr><td colspan='2'>".$custom_body."</td></tr>";

						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">'.BUSINESS_CAT.' :</td><td>Business Name</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Contact(s) :</td><td>Contact Names</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Staff Members :</td><td>Company Attendee Names</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Date of Meeting :</td><td>Date</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Time of Meeting :</td><td>Start Time - End Time</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Location :</td><td>Location</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Meeting Objective :</td><td>Meeting Objective</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Project :</td><td>Project Names</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Service(s) :</td><td>Service(s)</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Meeting Topic(s) :</td><td>Meeting Topic</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Meeting Note :</td><td>Meeting Notes</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Client Deliverables :</td><td>Client Deliverables</td></tr>';
						$email_body .= '<tr><td style="font-weight:bold; vertical-align:top; width:12em;">Company Deliverables :</td><td>Company Deliverables</td></tr>';

						$email_body .= ($get_field_config['email_logo'] != '' ? '<tr><td colspan="2"><img src="'.WEBSITE_URL.'/Agenda Meetings/download/'.$get_field_config['email_logo'].'" width="200" /></td>' : '');
						$email_body .= "</table>";
						echo $email_body; ?>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-6">
		<span class="popover-examples" style="margin:15px 0 0 0;"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href="agenda.php" class="btn brand-btn btn-lg">Back</a>
	</div>
	<div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
		<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
    </div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>