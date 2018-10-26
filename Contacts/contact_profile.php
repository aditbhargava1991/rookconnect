<?php include_once('../include.php'); ?>
<?php if($_GET['edit'] > 0) {
	$contactid = $_GET['edit'];
} else if($_GET['contactid'] > 0) {
	$contactid = $_GET['contactid'];
} else {
	$contactid = 0;
}
if($_GET['summary'] == 'true') {
    $summary_only = true;
}
$contact = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` LEFT JOIN `contacts_upload` ON `contacts`.`contactid`=`contacts_upload`.`contactid` WHERE `contacts`.`contactid`='$contactid'"));
$current_type = ($contactid > 0 ? get_contact($dbc, $contactid, 'category') : ($_GET['category'] != '' ? $_GET['category'] : explode(',',get_config($dbc,$folder_name.'_tabs'))[0]));
$field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE '".FOLDER_NAME."' IN (`tile_name`,'invoice','posadvanced') AND `tab`='$current_type' AND `subtab` = '**no_subtab**'"))[0] . ',' . mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".FOLDER_NAME."' AND `tab`='$current_type' AND `subtab` = 'additions'"))[0]);
$id_card_fields = get_config($dbc, config_safe_str($contact['category']).'_id_card_fields');
if($id_card_fields == '') {
    $id_card_fields = $field_config;
} else {
    if(strtolower($contact['category']) != 'staff') {
        $id_card_fields = array_merge($field_config, explode(',',$id_card_fields));
    } else {
        $id_card_fields = explode(',',$id_card_fields);
    }
}
if(in_array_any(['Star Rating'], $id_card_fields)) {
    $rating = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`ticket_schedule`.`id`) `num_rows`, `ticket_schedule`.`id`, `ticket_attached`.`rate`, AVG(`ticket_attached`.`rate`) `avg_rating` FROM `ticket_schedule` LEFT JOIN `equipment_assignment` ON `equipment_assignment`.`equipmentid` = `ticket_schedule`.`equipmentid` LEFT JOIN `equipment_assignment_staff` ON `equipment_assignment_staff`.`equipment_assignmentid` = `equipment_assignment`.`equipment_assignmentid` LEFT JOIN `ticket_attached` ON `ticket_attached`.`src_table`='customer_approve' AND `ticket_attached`.`line_id`=`ticket_schedule`.`id` WHERE `ticket_schedule`.`to_do_date` BETWEEN `equipment_assignment`.`start_date` AND IFNULL(NULLIF(`equipment_assignment`.`end_date`,'0000-00-00'),'9999-12-31') AND CONCAT(',',`equipment_assignment`.`hide_days`,',') NOT LIKE CONCAT('%,',`ticket_schedule`.`to_do_date`,',%') AND `equipment_assignment_staff`.`contactid` = '".$contact['contactid']."' AND `equipment_assignment_staff`.`deleted` = 0 AND `ticket_schedule`.`deleted` = 0 AND `equipment_assignment`.`deleted` = 0 AND `ticket_attached`.`rate` != 0"));
    $rating_html = '';
    for($i = 0; $i < 5; $i++) {
        if($rating['avg_rating'] >= 1) {
            $rating_html .= '<img class="inline-img" src="../img/icons/star.png">';
        } else if($rating['avg_rating'] >= 0.5) {
            $rating_html .= '<img class="inline-img" src="../img/icons/star_half.png">';
        } else {
            $rating_html .= '<img class="inline-img" src="../img/icons/star_empty.png">';
        }
        $rating['avg_rating'] -= 1;
    }
    $rating_html = '<div class="small">'.$rating_html.'<br />'.$rating['num_rows'].' Deliveries Completed</div>';
}
?>
<div class="col-sm-12">
	<?php if(!($summary_only === true)) {
        if($contact['category'] != 'Staff' && vuaed_visible_function($dbc, $security_folder) > 0) { ?><button onclick="copyContact(this); return false;" class="btn brand-btn pull-right gap-top">Copy Contact</button><button onclick="<?= IFRAME_PAGE ? "$('#profile_accordions').show(); $('.iframe_edit').show(); $('#view_profile').hide();" : " edit_profile();" ?> return false;" class="btn brand-btn pull-right gap-top">Edit Contact</button><?= IFRAME_PAGE ? '<a href="" onclick="openFullView(); return false;" class="btn brand-btn pull-right gap-top">Open Full Window</a>' : '' ?><?php }
        else if(FOLDER_NAME != 'profile' && !isset($_GET['view_only']) && vuaed_visible_function($dbc, 'staff') > 0) { ?><a href='?contactid=<?php echo $contactid; ?>&subtab=staff_information' class="hide-on-mobile btn brand-btn pull-right gap-top">Edit Staff</a><?php }
        else if(FOLDER_NAME != 'profile' && !isset($_GET['view_only']) && !(vuaed_visible_function($dbc, 'staff') > 0) && $contactid == $_SESSION['contactid']) { ?><a href='<?= WEBSITE_URL ?>/Profile/my_profile.php?edit_contact=true&from_staff_tile=true' class="hide-on-mobile btn brand-btn pull-right gap-top">Edit My Profile</a><?php } ?>
        <h3 class="gap-left"><?php if($contact['contactimage'] != '' && file_exists('download/'.$contact['contactimage']) && $contact['category'] != 'Staff') {
                echo '<img class="id-circle" src="download/'.$contact['contactimage'].'">';
                $contact_url = '';
            } else if($contact['category'] == 'Staff') {
                $field_config = '';
                $config_query = mysqli_query($dbc,"SELECT contacts FROM field_config_contacts WHERE tab='Staff' AND `accordion` IS NOT NULL AND `order` IS NOT NULL ORDER BY `subtab`, `order`");
                while($config_row = mysqli_fetch_assoc($config_query)) {
                    $field_config .= ','.$config_row['contacts'].',';
                }
                $field_config = explode(',',$field_config);
                if($contact['contactimage'] != '' && (file_exists('../Staff/download/'.$contact['contactimage']) || file_exists('../Profile/download/'.$contact['contactimage']))) {
                    if(file_exists('../Staff/download/'.$contact['contactimage'])) {
                        $contact_url = '../Staff/';
                    } else {
                        $contact_url = '../Profile/';
                    }
                    echo '<img class="id-circle no-toggle pull-left" src="'.$contact_url.'download/'.$contact['contactimage'].'" title="'.decryptIt($contact['name']).decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).'">';
                } else {
                    profile_id($dbc, $contactid, true, 'pull-left');
                }
            } ?>
            <?= get_client($dbc, $contactid).' '.get_contact($dbc, $contactid) ?><?= $rating_html ?></h3><div class="clearfix"></div>
            <style>
            .dashboard-profile-icon { height:auto; margin-right:8px; width:20px; }
            .dashboard-social-icon { height:auto; margin-right:3px; width:25px; }
            </style>
            <script>
            function edit_profile() {
                if($('.panel-heading:contains("View Profile")').is(':visible')) {
                    $('.panel:contains("View Profile")').nextAll('.panel').find('.panel-heading a').click();
                } else {
                    $('#view_profile').hide();
                    $('#view_history').hide();
                    $('#view_checklist').hide();
                    $('#edit_profile').show();
                }
                scrollScreen();
            }
            function view_profile() {
                if($('.panel-heading:contains("View Profile")').not(':visible')) {
                    $('#view_profile').show();
                    $('#edit_profile').hide();
                    $('#view_checklist').hide();
                    $('#view_history').hide();
                    $('.active.blue').removeClass('active').removeClass('blue');
                    $('[href=#view_profile] li').addClass('active blue');
                }
                scrollScreen();
            }
            function view_checklist() {
                if($('.panel-heading:contains("Checklists")').not(':visible')) {
                    $('#view_checklist').show();
                    $('#view_profile').hide();
                    $('#edit_profile').hide();
                    $('#view_history').hide();
                    $('.active.blue').removeClass('active').removeClass('blue');
                    $('[href=#view_checklist] li').addClass('active blue');
                }
                scrollScreen();
            }
            function statusChange(link) {
                var change_status = $(link).data('status') == "0" ? 'Activate' : 'Deactivate';
                if(confirm("Are you sure you want to "+change_status+" this contact?")) {
                    $(link).text($(link).data('status') == "0" ? 'Deactivate' : 'Activate').data('status',$(link).data('status') == "0" ? '1' : '0');
                    $.ajax({
                        url: '../Contacts/contacts_ajax.php?action=status_change&contactid='+$(link).data('contactid')+'&new_status='+$(link).data('status'),
                        method: 'POST'
                    });
                }
            }
            function copyContact(btn) {
                $('#dialog_copy_contact').dialog({
                    resizable: false,
                    height: "auto",
                    width: ($(window).width() <= 500 ? $(window).width() : 500),
                    modal: true,
                    buttons: {
                        "Copy Contact": function() {
                            var contactid = $('[name="contactid"]').val();
                            var category = $('[name="copy_contact_category"]').val();
                            $.ajax({
                                url: '../Contacts/contacts_ajax.php?action=copy_contact',
                                method: 'POST',
                                data: { contactid: contactid, category: category },
                                success: function(response) {
                                    window.location.href = "?edit="+response;
                                }
                            });
                            $(this).dialog('close');
                        },
                        Cancel: function() {
                            $(this).dialog('close');
                        }
                    }
                });
            }
            </script>
    <?php } ?>

    <?php if(!($contactid > 0)) {
        echo '<h3>No '.CONTACTS_NOUN.' Selected</h3>';
    } else if(in_array_starts('POS ',$id_card_fields)) { ?>
        <!-- POS Summary -->
        <?php if(in_array('POS Invoices', $id_card_fields)) {
            $inv_count = $dbc->query("SELECT COUNT(*) `count` FROM `invoice` WHERE `patientid`='$contactid' AND `status` NOT IN ('Void') AND `deleted`=0")->fetch_assoc(); ?>
            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 gap-top">
                <div class="summary-block">
                    <span class="text-lg"><?= ($inv_count['count'] > 0) ? '<a target="_top" href="../POSAdvanced/index.php?tab=all&contactid='.$contactid.'&type=&search_from=0000-00-00&search_to='.date('Y-m-t').'&search_invoice_submit=Search">'.$inv_count['count'].'</a>' : 0; ?></span><br />
                    Total<br />Invoices
                </div>
            </div>
        <?php } ?>
        <?php if(in_array('POS Paid', $id_card_fields)) {
            $inv_total = $dbc->query("SELECT SUM(`p`.`patient_price`) `paid` FROM `invoice_patient` `p` LEFT JOIN `invoice` `inv` ON (`inv`.`invoiceid`=`p`.`invoiceid`) WHERE (IFNULL(`p`.`paid`,'') NOT IN ('On Account','No','') AND `p`.`paid` NOT LIKE 'Net %') AND `inv`.`status` NOT IN ('Void') AND `inv`.`patientid`='$contactid'")->fetch_assoc()['paid']; ?>
            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 gap-top">
                <div class="summary-block">
                    <span class="text-lg"><?= '$'.floor($inv_total).'.<sup>'.sprintf("%02d",($inv_total * 100 % 100)).'</sup>' ?></span><br />
                    Total Paid<br />To Date
                </div>
            </div>
        <?php } ?>
        <?php if(in_array('POS A/R', $id_card_fields)) {
            $patient_ar = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT SUM(`p`.`patient_price`) `patient_ar` FROM `invoice_patient` `p` LEFT JOIN `invoice` `inv` ON (`inv`.`invoiceid`=`p`.`invoiceid`) WHERE (IFNULL(`p`.`paid`,'') IN ('On Account','No','') OR `p`.`paid` LIKE 'Net %') AND `inv`.`status` NOT IN ('Void') AND `inv`.`patientid`='$contactid'" ) )['patient_ar']; ?>
            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 gap-top">
                <div class="summary-block">
                    <span class="text-lg"><?= '$'.($patient_ar > 0 ? '<a target="_top" href="../POSAdvanced/index.php?tab=contact_ar&patientid='.$contactid.'&from=0000-00-00&until='.date('Y-m-d').'">' : '').floor($patient_ar).'.<sup>'.sprintf("%02d",($patient_ar * 100 % 100)).'</sup>'.($patient_ar > 0 ? '</a>' : '') ?></span><br />
                    A/R<br />&nbsp;
                </div>
            </div>
        <?php } ?>
        <?php if(in_array('POS Credit', $id_card_fields)) {
            $patient = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `amount_credit` FROM `contacts` WHERE `contactid`='$contactid'" ) );
            $patient_ar = empty($patient['amount_credit']) ? 0 : $patient['amount_credit']; ?>
            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 gap-top">
                <div class="summary-block">
                    <span class="text-lg"><?= '$'.floor($patient_ar).'.<sup>'.sprintf("%02d",($patient_ar * 100 % 100)).'</sup>' ?></span><br />
                    Credit<br />On Account
                </div>
            </div>
        <?php } ?>
        <?php if(in_array('POS Balance', $id_card_fields)) {
            $patient = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `amount_owing` FROM `contacts` WHERE `contactid`='$contactid'" ) );
            $patient_ar = empty($patient['amount_owing']) ? 0 : $patient['amount_owing']; ?>
            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 gap-top">
                <div class="summary-block">
                    <span class="text-lg"><?= '$'.floor($patient_ar).'.<sup>'.sprintf("%02d",($patient_ar * 100 % 100)).'</sup>' ?></span><br />
                    Account<br />Balance
                </div>
            </div>
        <?php } ?>
        <?php if(in_array('POS Last Date', $id_card_fields)) {
            $inv_count = $dbc->query("SELECT MAX(`invoice_date`) `date` FROM `invoice` WHERE `patientid`='$contactid' AND `status` NOT IN ('Void') AND `deleted`=0")->fetch_assoc(); ?>
            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2 gap-top">
                <div class="summary-block">
                    <span class="text-lg"><?= !empty($inv_count['date'] > 0) ? '<a target="_top" href="../POSAdvanced/index.php?tab=all&contactid='.$contactid.'&type=&search_from=0000-00-00&search_to='.date('Y-m-d').'&search_invoice_submit=Search">'.$inv_count['date'].'</a>' : 'N/A'; ?></span><br />
                    Date Last<br />Invoiced
                </div>
            </div>
        <?php } ?>
        <div class="clearfix"></div>
        <!-- POS Summary -->
    <?php } else if($summary_only === true) {
        echo '<h3>No '.CONTACTS_NOUN.' Summary Found</h3>';
    } ?>

    <?php if(!($summary_only === true)) { ?>
        <div class="col-sm-6">
            <ul class="chained-list col-sm-6 small">
                <?php if($contact['contactimage'] != '' && file_exists($contact_url.'download/'.$contact['contactimage'])) { ?><li style="text-align: center;"><img src="<?= $contact_url ?>download/<?= $contact['contactimage'] ?>" style="max-width: 200px; max-height: 200px;"></li><?php } ?>
                <?php if((in_array_any(['Sales Lead'], $id_card_fields) || in_array($contact['category'],['Sales Lead','Sales Leads'])) && in_array($contact['category'],explode(',',get_config($dbc, 'lead_all_contact_cat').',Sales Lead,Sales Leads,'))) {
                    $sales_lead_id = $dbc->query("SELECT `salesid` FROM `sales` WHERE `deleted`=0 AND CONCAT(',',`contactid`,',') LIKE '%,".$contactid.",%'")->fetch_assoc()['salesid'];
                    if($sales_lead_id > 0) { ?>
                        <li><img src="../img/person.PNG" class="dashboard-profile-icon" title="Sales Lead"><a href="../Sales/sale.php?p=details&id=<?= $sales_lead_id ?>" onclick="overlayIFrameSlider(this.href,'80%',true,true); return false;">Sales Lead #<?= $sales_lead_id ?></a></li>
                    <?php } ?>
                <?php } ?>
                <?php if(in_array_any(['Employee Number','Employee ID','Employee #'], $id_card_fields)) { ?><li><img src="../img/id-card.png" class="dashboard-profile-icon" title="ID Number"><?= $contactid ?></li><?php } ?>
                <?php if(in_array_any(['First Name','Last Name','Profile First Name','Profile Last Name'], $id_card_fields)) { ?><li><img src="../img/person.PNG" class="dashboard-profile-icon" title="Full Name"><?= decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']) ?></li><?php } ?>
                <?php if(in_array_any(['Position'], $id_card_fields)) {
                    $position_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `positions` WHERE `position_id` = '{$contact['position']}'"))['name']; ?><li><img src="../img/job.png" class="dashboard-profile-icon" title="Position"><?= !empty($position_name) ? $position_name : $contact['position'] ?></li><?php } ?>
                <?php if(in_array_any(['Home Phone','Profile Home Phone'], $id_card_fields)) { ?><li><a href="tel:<?= decryptIt($contact['home_phone']) ?>"><img src="../img/home_phone.PNG" class="dashboard-profile-icon" title="Home Phone"><?= decryptIt($contact['home_phone']) ?></a></li><?php } ?>
                <?php if(in_array_any(['Office Phone','Profile Office Phone'], $id_card_fields)) { ?><li><a href="tel:<?= decryptIt($contact['office_phone']) ?>"><img src="../img/office_phone.PNG" class="dashboard-profile-icon" title="Office Phone"><?= decryptIt($contact['office_phone']) ?></a></li><?php } ?>
                <?php if(in_array_any(['Cell Phone','Profile Cell Phone'], $id_card_fields)) { ?><li><a href="tel:<?= decryptIt($contact['cell_phone']) ?>"><img src="../img/cell_phone.PNG" class="dashboard-profile-icon" title="Cell Phone"><?= decryptIt($contact['cell_phone']) ?></a></li><?php } ?>
                <?php if(in_array_any(['Email Address','Profile Email Address'], $id_card_fields)) { ?><li><a href="mailto:<?= decryptIt($contact['email_address']) ?>"><img src="../img/email.PNG" class="dashboard-profile-icon" title="Email Address"><?= decryptIt($contact['email_address']) ?></a></li><?php } ?>
                <?php if(in_array_any(['Second Email Address'], $id_card_fields)) { ?><li><a href="mailto:<?= decryptIt($contact['second_email_address']) ?>"><img src="../img/email.PNG" class="dashboard-profile-icon" title="Second Email Address"><?= decryptIt($contact['second_email_address']) ?></a></li><?php } ?>
                <?php if(in_array_any(['Company Email Address','Profile Company Email Address'], $id_card_fields)) { ?><li><a href="mailto:<?= decryptIt($contact['office_email']) ?>"><img src="../img/email.PNG" class="dashboard-profile-icon" title="Company Email Address"><?= decryptIt($contact['office_email']) ?></a></li><?php } ?>
                <?php if(in_array_any(['Start Date'], $id_card_fields)) { ?><li><img src="../img/calendar.png" class="dashboard-profile-icon" title="Start Date"><?= $contact['start_date'] ?>
                    <?php if (FOLDER_NAME=='profile' && ($contact['start_date'] != '0000-00-00' || empty($contact['start_date']))) {
                        //Check if today is the work anniversary. If so, display it.
                        if ( date('m-d')==substr($contact['start_date'],5,5) ) {
                            $start_date = new DateTime($contact['start_date']);
                            $today = new DateTime(date('Y-m-d'));
                            $diff = $today->diff($start_date);
                            echo ' | ' . $diff->y . ' years';
                        }
                    } ?></li>
                <?php } ?>
                <?php if(in_array_any(['Contract End Date'], $id_card_fields)) { ?><li><img src="../img/calendar.png" class="dashboard-profile-icon" title="Contract End Date"><?= $contact['contract_end_date'] ?></li>
                <?php } ?>
                <li><img src="../img/setting.PNG" class="dashboard-profile-icon" title="Status">
                <?php if(vuaed_visible_function($dbc, $contact['category'] == 'staff' ? 'staff' : $security_folder) > 0) { ?><?= $contact['status'] > 0 ?
                    'Active | <a href="" onclick="statusChange(this); return false;" data-status="1" data-contactid="'.$contact['contactid'].'">Deactivate</a>' :
                    'Inactive | <a href="" onclick="statusChange(this); return false;" data-status="0" data-contactid="'.$contact['contactid'].'">Activate</a>' ?><?php
                } else {
                    echo $contact['status'] > 0 ? 'Active' : 'Inactive';
                } ?></li>
            </ul>
        </div>
        <div class="col-sm-6">
            <ul class="chained-list col-sm-6 small">
                <?php if(in_array_any(['Business','Program Business'], $id_card_fields)) { ?><li><img src="../img/business.PNG" class="dashboard-profile-icon" title="Business"><?= get_client($dbc, $contact['businessid']) ?></li><?php } ?>
                <?php if(in_array_any(['Name'], $id_card_fields)) { ?><li><img src="../img/business.PNG" class="dashboard-profile-icon" title="<?= $contact['category'] ?> Name"><?= decryptIt($contact['name']) ?></li><?php } ?>
                <?php if(in_array_any(['Location','Profile Location'], $id_card_fields)) { ?><li><img src="../img/address.PNG" class="dashboard-profile-icon" title="Location"><?= ($contact['con_location']) ?></li><?php } ?>
                <?php if(in_array_any(['Business Address'], $id_card_fields)) { ?><li><a class="show-on-mob" href="maps:<?= decryptIt($contact['business_street']) ?>"><img src="../img/address.PNG" title="Business Address" class="inline-img dashboard-profile-icon"><?= decryptIt($contact['business_street']) ?></a><a class="hide-on-mobile" href="https://maps.google.com/maps/place/<?= decryptIt($contact['business_street']) ?>"><img src="../img/address.PNG" title="Business Address" class="inline-img dashboard-profile-icon"><?= decryptIt($contact['business_street']) ?></a></li><?php } ?>
                <?php if(in_array_any(['Mailing Address'], $id_card_fields)) { ?><li><a class="show-on-mob" href="maps:<?= ($contact['mailing_address']) ?>"><img src="../img/address.PNG" title="Mailing Address" class="inline-img dashboard-profile-icon"><?= ($contact['mailing_address']) ?></a><a class="hide-on-mobile" href="https://maps.google.com/maps/place/<?= ($contact['mailing_address']) ?>"><img src="../img/address.PNG" title="Mailing Address" class="inline-img dashboard-profile-icon"><?= ($contact['mailing_address']) ?></a></li><?php } ?>
                <?php if(in_array_any(['Address'], $id_card_fields)) { ?><li><a class="show-on-mob" href="maps:<?= ($contact['address']) ?>"><img src="../img/address.PNG" title="Address" class="inline-img dashboard-profile-icon"><?= ($contact['address']) ?></a><a class="hide-on-mobile" href="https://maps.google.com/maps/place/<?= ($contact['address']) ?>"><img src="../img/address.PNG" title="Address" class="inline-img dashboard-profile-icon"><?= ($contact['address']) ?></a></li><?php } ?>
                <?php if(in_array_any(['Birth Date','Date of Birth','Profile Date of Birth'], $id_card_fields)) { ?><li><img src="../img/birthday.png" title="Birth Date" class="inline-img dashboard-profile-icon"><?= $contact['birth_date'] ?><?= ( $contact['birth_date']=='0000-00-00' || empty($contact['birth_date']) ) ? '' : ' Age: '.date_diff(date_create($contact['birth_date']), date_create('now'))->y ?></li><?php } ?>
                <?php if(in_array_any(['Guardians First Name'], $id_card_fields)) { ?>
                    <?php $guardian_count = count(explode('*#*', $contact['guardians_first_name']));
                    for ($counter = 0; $counter < $guardian_count && !empty($contact['guardians_first_name']); $counter++) { ?>
                        <li><img src="../img/person.PNG" title="Guardian <?= ($counter+1) ?>" class="inline-img dashboard-profile-icon"><?= explode('*#*', $contact['guardians_first_name'])[$counter].' '.explode('*#*', $contact['guardians_last_name'])[$counter] ?></li>
                        <?php if(in_array_any(['Guardians Work Phone','Guardians Home Phone','Guardians Cell Phone'], $id_card_fields)) { ?>
                            <li><img src="../img/home_phone.PNG" title="Guardian <?= ($counter+1) ?> Phone Number" class="dashboard-profile-icon"><?= !empty(explode('*#*', $contact['guardians_home_phone'])[$counter]) ? explode('*#*', 'H: '.$contact['guardians_home_phone'])[$counter].'&nbsp;&nbsp;' : '' ?><?= !empty(explode('*#*', $contact['guardians_work_phone'])[$counter]) ? explode('*#*', 'O: '.$contact['guardians_work_phone'])[$counter].'&nbsp;&nbsp;' : '' ?><?= !empty(explode('*#*', $contact['guardians_cell_phone'])[$counter]) ? explode('*#*', 'C: '.$contact['guardians_cell_phone'])[$counter].'&nbsp;&nbsp;' : '' ?></li>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                <?php if(in_array_any(['LinkedIn','Profile LinkedIn'], $id_card_fields)) { ?><li><a href="<?= $contact['linkedin'] ?>"><img src="../img/icons/social/linkedin.png" class="dashboard-social-icon" title="LinkedIn" /> LinkedIn</a></li><?php } ?>
                <?php if(in_array_any(['Facebook','Profile Facebook'], $id_card_fields)) { ?><li><a href="<?= $contact['facebook'] ?>"><img src="../img/icons/social/facebook.png" class="dashboard-social-icon" title="Facebook" /> Facebook</a></li><?php } ?>
                <?php if(in_array_any(['Twitter','Profile Twitter'], $id_card_fields)) { ?><li><a href="<?= $contact['twitter'] ?>"><img src="../img/icons/social/twitter.png" class="dashboard-social-icon" title="Twitter" /> Twitter</a></li><?php } ?>
                <?php if(in_array_any(['Google+','Profile Google+'], $id_card_fields)) { ?><li><a href="<?= $contact['google_plus'] ?>"><img src="../img/icons/social/google+.png" class="dashboard-social-icon" title="Google+" /> Google+</a></li><?php } ?>
                <?php if(in_array_any(['Instagram','Profile Instagram'], $id_card_fields)) { ?><li><a href="<?= $contact['instagram'] ?>"><img src="../img/icons/social/instagram.png" class="dashboard-social-icon" title="Instagram" /> Instagram</a></li><?php } ?>
                <?php if(in_array_any(['Pinterest','Profile Pinterest'], $id_card_fields)) { ?><li><a href="<?= $contact['pinterest'] ?>"><img src="../img/icons/social/pinterest.png" class="dashboard-social-icon" title="Pinterest" /> Pinterest</a></li><?php } ?>
                <?php if(in_array_any(['YouTube','Profile YouTube'], $id_card_fields)) { ?><li><a href="<?= $contact['youtube'] ?>"><img src="../img/icons/social/youtube.png" class="dashboard-social-icon" title="Youtube" /> YouTube</a></li><?php } ?>
                <?php if(in_array_any(['Blog','Profile Blog'], $id_card_fields)) { ?><li><a href="<?= $contact['blog'] ?>"><img src="../img/icons/social/rss.png" class="dashboard-social-icon" title="Blog" /> Blog</a></li><?php } ?>
                <?php if(in_array_any(['Ticket Service Total Hours'], $id_card_fields)) {
                    $ticket_type_times = [];
                    $ticket_tabs = array_filter(explode(',',get_config($dbc, 'ticket_tabs')));
                    foreach($ticket_tabs as $ticket_tab) {
                        $total_service_time = 0;
                        $tickets = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE CONCAT(',',`clientid`,',') LIKE '%,".$contact['contactid'].",%' AND `deleted` = 0 AND `ticket_type` = '".config_safe_str($ticket_tab)."'");
                        while($ticket = mysqli_fetch_assoc($tickets)) {
                            foreach(array_filter(explode(',',$ticket['service_total_time'])) as $service_time) {
                                $time_arr = explode(' ',$service_time);
                                for($time_i = 0; $time_i < count($time_arr); $time_i = $time_i+2) {
                                    if($time_arr[$time_i+1] == 'Hr') {
                                        $total_service_time += (intval($time_arr[$time_i])*60);
                                    } else if($time_arr[$time_i+1] == 'Min') {
                                        $total_service_time += intval($time_arr[$time_i]);
                                    }
                                }
                            }
                        }
                        $ticket_type_times[] = $ticket_tab.' Hours: '.time_decimal2time($total_service_time/60);
                    } ?><li><div class="col-xs-2" style="max-width:35px;"><img src="../img/icons/clock-button.png" title="<?= TICKET_NOUN ?> Service Total Hours" class="inline-img"></div><div class="col-xs-10"><?= implode('<br />',$ticket_type_times) ?></div><div class="clearfix"></div></li>
                <?php } ?>
                <?php if($contact['category'] == 'Staff') {
                    $business_card_template = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `business_card_template` WHERE `contact_category` = '".$contact['category']."'")); ?>
                    <li>&nbsp;<img src="../img/pdf.png" style="height:1.2em;" title="PDF" />
                        <?PHP if(!empty($business_card_template['template'])) { ?>
                            <a href="../Staff/business_card_templates/<?= $business_card_template['template'] ?>_pdf.php?contactid=<?= $contactid ?>">Business Card PDF</a> |
                        <?php } ?>
                        <a href="../Staff/id_card_pdf.php?contactid=<?= $contactid ?>">ID Card PDF</a>
                    </li>
                <?PHP } ?>
            </ul>
        </div>
    <?php } ?>
</div>