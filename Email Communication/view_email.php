<?php
/*
 * View Internal/External Email
 * Called From: dashboard_email.php
 */
include ('../include.php');
error_reporting(0);
$communication_type = empty($_POST['comm_type']) ? (empty($_GET['type']) ? 'Internal' : ucfirst(filter_var($_GET['type'], FILTER_SANITIZE_STRING))) : $_POST['comm_type']; ?>
</head>

<body>
<?php
    include_once ('../navigation.php');
    checkAuthorised('email_communication');
?>
<div class="container">
    <div class="row">
        <h3 class="gap-left pull-left">Email</h3>
        <div class="pull-right offset-top-15"><?php
            $id = '';
            if (!empty($_GET['email_communicationid'])) {
                $id = preg_replace('/[^0-9]/', '', $_GET['email_communicationid']);
            }
            
            if ( !empty($id) ) { ?>
                <img src="../img/icons/ROOK-reminder-icon.png" alt="Add Reminder" title="Add Reminder" class="no-toggle cursor-hand" data-placement="bottom" width="25" onclick="overlayIFrameSlider('../quick_action_reminders.php?tile=email&id=<?= $id ?>', 'auto', false, true);" /><?php
            } ?>
            <a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img no-toggle" data-placement="bottom" width="25" /></a>
        </div>
        <div class="clearfix"></div>
        <hr />
        
        <?php
            if ( !empty($_GET['email_communicationid']) ) {
                $id = preg_replace('/[^0-9]/', '', $_GET['email_communicationid']);
                $email = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM email_communication WHERE email_communicationid='$id'"));
            }
        ?>
            
        <div class="padded">
            <div class="row">
                <label class="col-xs-4">Email Type:</label>
                <div class="col-xs-8"><?= $email['communication_type'] ?></div>
            </div>
            
            <div class="row gap-top">
                <label class="col-xs-4">From:</label>
                <div class="col-xs-8"><?= $email['from_name'] .' &lt'. $email['from_email'] .'&gt' ?></div>
            </div>
            
            <div class="row gap-top">
                <label class="col-xs-4">Contact(s):</label>
                <div class="col-xs-8"><?php
                    $all_contacts = $email['to_contact'] .','. $email['cc_contact'];
                    $contact_emails = '';
                    foreach ( explode(',', $all_contacts) as $contact_email ) {
                        $contact_emails .= $contact_email .', ';
                    }
                    echo rtrim($contact_emails, ', '); ?>
                </div>
            </div>
            
            <div class="row gap-top">
                <label class="col-xs-4">Staff:</label>
                <div class="col-xs-8"><?php
                    $all_staff = $email['to_staff'] .','. $email['to_staff'];
                    $staff_emails = '';
                    foreach ( explode(',', $all_staff) as $staff_email ) {
                        $staff_emails .= $staff_email .', ';
                    }
                    echo rtrim($staff_emails, ', '); ?>
                </div>
            </div>
            
            <div class="row gap-top">
                <label class="col-xs-4">Additional Emails:</label>
                <div class="col-xs-8"><?php
                    $additional_staff = $email['new_emailid'];
                    $additional_emails = '';
                    foreach ( explode(',', $additional_staff) as $additional_email ) {
                        $additional_emails .= $additional_email .', ';
                    }
                    echo rtrim($additional_emails, ', '); ?>
                </div>
            </div>
            
            <div class="row gap-top">
                <label class="col-xs-4">Subject:</label>
                <div class="col-xs-8"><?= html_entity_decode($email['subject']) ?></div>
            </div>
            
            <div class="row gap-top">
                <label class="col-xs-4">Email Body:</label>
                <div class="col-xs-8"><?= html_entity_decode($email['email_body']) ?></div>
            </div>
            
            <div class="row gap-top">
                <label class="col-xs-4">Attachment(s):</label>
                <div class="col-xs-8"><?php
                    if ( !empty($_GET['email_communicationid']) ) {
                        $id = preg_replace('/[^0-9]/', '', $_GET['email_communicationid']);
                        $attachments = mysqli_query($dbc, "SELECT * FROM `email_communicationid_upload` WHERE `email_communicationid`='$id' ORDER BY `emailcommuploadid` DESC");
                        if ( $attachments->num_rows > 0 ) { ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr class="hidden-xs hidden-sm">
                                        <th>Document</th>
                                        <th>Date</th>
                                        <th>Uploaded By</th>
                                    </tr>
                                </thead><?php
                                while ( $row = mysqli_fetch_array($attachments) ) { ?>
                                    <tr>
                                        <td data-title="Document"><a href="download/'<?= $row['document'] ?>" target="_blank"><?= $row['document'] ?></a></td>
                                        <td data-title="Date"><?= $row['created_date'] ?></td>
                                        <td data-title="Uploaded By"><?= get_staff($dbc, $row['created_by']) ?></td>
                                    </tr><?php
                                } ?>
                            </table><?php
                        } else {
                            echo '-';
                        }
                    } ?>
                </div>
            </div>
        </div><!-- .main-screen -->
        
    </div>
</div>
<?php include ('../footer.php'); ?>