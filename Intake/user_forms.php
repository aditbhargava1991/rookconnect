<?php $return_url = '?';
if(!empty($_GET['frefid'])) {
    $return_url = hex2bin($_GET['frefid']);
}
if(!empty($_POST['complete_form'])) {
    include_once('../tcpdf/tcpdf.php');
    require_once('../phpsign/signature-to-image.php');

    $intakeformid = $_POST['intakeformid'];
    $form_id = $_POST['form_id'];
    $assign_id = $_POST['assign_id'];
    $user_id = (empty($_SESSION['contactid']) ? 0 : $_SESSION['contactid']);
    $result = mysqli_query($dbc, "SELECT * FROM `user_form_assign` WHERE `form_id`='$form_id' AND '$assign_id' IN (`assign_id`,'') AND `completed_date` IS NULL");

    $intakeid = $_POST['intakeid'];
    if(!empty($intakeid)) {
        $pdf_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid` = '$intakeid'"))['pdf_id'];
    } else {
        $pdf_result = mysqli_query($dbc, "INSERT INTO `user_form_pdf` (`form_id`, `user_id`) VALUES ('$form_id', '$user_id')");
        $pdf_id = mysqli_insert_id($dbc);
    }
    if(mysqli_num_rows($result)) {
        $assign_id = mysqli_fetch_array($result)['assign_id'];
        mysqli_query($dbc, "UPDATE `user_form_assign` SET `completed_date`=CURRENT_TIMESTAMP, `pdf_id`='$pdf_id' WHERE `assign_id`='$assign_id'");
    } else {
        mysqli_query($dbc, "INSERT INTO `user_form_assign` (`form_id`, `user_id`, `completed_date`, `pdf_id`) VALUES ('$form_id', '$user_id', CURRENT_TIMESTAMP, '$pdf_id')");
        $assign_id = mysqli_insert_id($dbc);
    }
    $form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id`='$form_id'"));
    $pdf_name = preg_replace('/([^a-z])/', '', strtolower($form['name'])).'_'.$assign_id.'.pdf';
    mysqli_query($dbc, "UPDATE `user_form_pdf` SET `generated_file`='$pdf_name' WHERE `pdf_id`='$pdf_id'");

    include('../Form Builder/generate_form_pdf.php');
    $contactinfo_field = mysqli_fetch_array(mysqli_query($dbc, "SELECT `intake_field` FROM `user_forms` WHERE `form_id` = '$form_id'"))['intake_field'];
    $contactinfo_fieldname = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` FROM `user_form_fields` WHERE `field_id` = '$contactinfo_field'"))['name'];
    if(!empty($contactinfo_field)) {
        //Name
        $first_name_field = mysqli_fetch_array(mysqli_query($dbc, "SELECT `field_id` FROM `user_form_fields` WHERE `type` = 'OPTION' AND `name` = '$contactinfo_fieldname' AND `form_id` = '$form_id' AND `deleted` = 0 AND `source_conditions` = 'first_name'"))['field_id'];
        $last_name_field = mysqli_fetch_array(mysqli_query($dbc, "SELECT `field_id` FROM `user_form_fields` WHERE `type` = 'OPTION' AND `name` = '$contactinfo_fieldname' AND `form_id` = '$form_id' AND `deleted` = 0 AND `source_conditions` = 'last_name'"))['field_id'];
        $name = $_POST[$contactinfo_fieldname][$first_name_field].' '.$_POST[$contactinfo_fieldname][$last_name_field];

        //Email
        $email_field = mysqli_fetch_array(mysqli_query($dbc, "SELECT `field_id` FROM `user_form_fields` WHERE `type` = 'OPTION' AND `name` = '$contactinfo_fieldname' AND `form_id` = '$form_id' AND `deleted` = 0 AND `source_conditions` = 'email_address'"))['field_id'];
        $email = $_POST[$contactinfo_fieldname][$email_field];

        //Phone
        $phone = '';
        $home_phone_field = mysqli_fetch_array(mysqli_query($dbc, "SELECT `field_id` FROM `user_form_fields` WHERE `type` = 'OPTION' AND `name` = '$contactinfo_fieldname' AND `form_id` = '$form_id' AND `deleted` = 0 AND `source_conditions` = 'home_phone'"))['field_id'];
        if(!empty($_POST[$contactinfo_fieldname][$home_phone_field])) {
            $phone .= 'H: '.$_POST[$contactinfo_fieldname][$home_phone_field].'<br>';
        }
        $cell_phone_field = mysqli_fetch_array(mysqli_query($dbc, "SELECT `field_id` FROM `user_form_fields` WHERE `type` = 'OPTION' AND `name` = '$contactinfo_fieldname' AND `form_id` = '$form_id' AND `deleted` = 0 AND `source_conditions` = 'cell_phone'"))['field_id'];
        if(!empty($_POST[$contactinfo_fieldname][$cell_phone_field])) {
            $phone .= 'C: '.$_POST[$contactinfo_fieldname][$cell_phone_field].'<br>';
        }
        $office_phone_field = mysqli_fetch_array(mysqli_query($dbc, "SELECT `field_id` FROM `user_form_fields` WHERE `type` = 'OPTION' AND `name` = '$contactinfo_fieldname' AND `form_id` = '$form_id' AND `deleted` = 0 AND `source_conditions` = 'office_phone'"))['field_id'];
        if(!empty($_POST[$contactinfo_fieldname][$office_phone_field])) {
            $phone .= 'O: '.$_POST[$contactinfo_fieldname][$office_phone_field];
        }
    }

    $today_date = date('Y-m-d');
    if(empty($intakeid)) {
        $new_intake = true;
        $query_insert_upload = "INSERT INTO `intake` (`intakeformid`, `pdf_id`, `name`, `email`, `phone`, `intake_file`, `received_date`) VALUES ('$intakeformid', '$pdf_id', '$name', '$email', '$phone', 'download/$pdf_name', '$today_date')";
        $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        $intakeid = mysqli_insert_id($dbc);
    } else {
        $new_intake = false;
        $query_update_upload = "UPDATE `intake` SET `pdf_id` = '$pdf_id', `name` = '$name', `email` = '$email', `phone` = '$phone', `intake_file` = 'download/$pdf_name', `received_date` = '$today_date' WHERE `intakeid` = '$intakeid'";
        $result_update_upload = mysqli_query($dbc, $query_update_upload);
    }

    $pdf->writeHTML(utf8_encode('<form action="" method="POST">'.$pdf_text.'</form>'), true, false, true, false, '');

    include('../Form Builder/generate_form_pdf_page.php');
    mysqli_query($dbc, "UPDATE `intake` SET `ticket_description` = '".htmlentities($ticket_description)."' WHERE `intakeid` = '$intakeid'");

    if(!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    $pdf->Output('download/'.$pdf_name, 'F');
    
    if(empty($_SESSION['contactid'])) {
        $url = $_SERVER['REQUEST_URI'].'&complete=true';
    } else if(!empty($_POST['projectid'])) {
        $url = '../Project/projects.php?edit='.$_POST['projectid'];
    } else if(!empty($_POST['salesid'])) {
        $url = '../Sales/sale.php?p=salespath&id='.$_POST['salesid'];
    } else {
        $url = '../Intake/intake.php?tab=softwareforms&type='.$intakeformid;
    }

    if(!empty($_POST['projectid']) || !empty($_POST['salesid'])) {
        if($new_intake) {
            $assigned_date_query = ", `assigned_date` = '".date('Y-m-d')."'";
        }

        if(!empty($_POST['projectid'])) {
            $projectid = $_POST['projectid'];
            $project_milestone = $_POST['project_milestone'];
            mysqli_query($dbc, "UPDATE `intake` SET `projectid` = '$projectid', `project_milestone` = '$project_milestone' $assigned_date_query WHERE `intakeid` = '$intakeid'");  
        } else if(!empty($_POST['salesid'])) {
            $salesid = $_POST['salesid'];
            $sales_milestone = $_POST['sales_milestone'];
            mysqli_query($dbc, "UPDATE `intake` SET `salesid` = '$salesid', `sales_milestone` = '$sales_milestone' $assigned_date_query WHERE `intakeid` = '$intakeid'");
            
            include('../Intake/attach_services_sales.php');
        }

        $intake_contactid = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid` = '$intakeid'"))['contactid'];
        if(empty($intake_contactid)) {
            $user_form_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `user_form_id` FROM `intake_forms` WHERE `intakeformid` = '$intakeformid'"))['user_form_id'];
            $user_field_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT `intake_field` FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['intake_field'];
            $user_form_field = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `field_id` = '$user_field_id'"));
            $src_table = $user_form_field['source_table'];
            $contact_type = $user_form_field['source_conditions']; ?>
            <script type="text/javascript">
                $(function() {
                    $('.dialog_attachcontact').dialog({
                        resizable: true,
                        height: "auto",
                        width: ($(window).width() <= 600 ? $(window).width() : 600),
                        modal: true,
                        buttons: {
                            'New Contact': function() {
                                attachContactIframe('create', 'Create a New Contact');
                                $(this).dialog('close');
                            },
                            'Existing Contact': function() {
                                attachContactIframe('assign', 'Assign to an Existing Contact');
                                $(this).dialog('close');
                            },
                            'Do Not Attach': function() {
                                $(this).dialog('close');
                                window.location.replace('<?= $url ?>');
                            }
                        }
                    });

                    $('.close_iframer').click(function(){
                        $('.iframe_holder').hide();
                        $('.hide_on_iframe').show();
                    });

                    $('iframe').load(function() {
                        this.contentWindow.document.body.style.overflow = 'hidden';
                        this.contentWindow.document.body.style.minHeight = '0';
                        this.contentWindow.document.body.style.paddingBottom = '15em';
                        this.style.height = (this.contentWindow.document.body.offsetHeight + 180) + 'px';
                    });
                });
                window.onpopstate = function() {
                    $('.iframe_holder').hide();
                    $('.hide_on_iframe').show();
                }
                function attachContactIframe(action, title) {
                    var subtitle = "Choose a Contact category";
                    var contact_type = "<?= $contact_type ?>";
                    var src_table = "<?= $src_table ?>";
                    var intakeid = "<?= $intakeid ?>";
                    var projectid = "<?= $projectid ?>";
                    var salesid = "<?= $salesid ?>";
                    $('#iframe_instead_of_window').attr('src', 'get_contact_categories_software.php?subtitle='+subtitle+'&action='+action+'&contact_type='+contact_type+'&src_table='+src_table+'&intakeid='+intakeid+'&from_projectid='+projectid+"&from_salesid="+salesid);
                    $('.iframe_title').text(title);
                    $('.iframe_holder').show();
                    $('.hide_on_iframe').hide();
                }
            </script>
        <?php } else {
            echo "<script> window.location.replace('$url'); </script>";
        }
    } else {
        echo "<script> window.location.replace('$url'); </script>";
    }
} else { ?>
    <?php if($user_form_layout == 'Sidebar') { ?>
        <h1 style="margin-top: 0; padding-top: 0;"><a href="intake.php?tab=softwareforms">Intake</a></h1>
    <?php } else { ?>
        <h1><?= $intake_form['form_name'] ?></h1>
        <?php if(!empty($_SESSION['contactid'])) { ?>
            <div class="gap-top double-gap-bottom"><a href="intake.php?tab=softwareforms" class="btn config-btn">Back to Dashboard</a></div>
        <?php } ?>
    <?php } ?>
    
    <form name="assign_form" method="post" action="" class="form-horizontal" role="form" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0; border-top: 1px solid #E1E1E1;"' : '' ?>>
        <input type="hidden" name="intakeformid" value="<?= $intakeformid ?>">
        <input type="hidden" name="salesid" value="<?= $_GET['salesid'] ?>">
        <input type="hidden" name="sales_milestone" value="<?= $_GET['sales_milestone'] ?>">
        <input type="hidden" name="projectid" value="<?= $_GET['projectid'] ?>">
        <input type="hidden" name="project_milestone" value="<?= $_GET['project_milestone'] ?>">
        <?php $form_id = $user_form_id;
        $default_collapse = 'in';
        if(isset($_GET['intakeid'])) {
            $intakeid = $_GET['intakeid'];
            $get_intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid` = '$intakeid'"));
            $pdf_id = $get_intake['pdf_id'];
            echo '<input type="hidden" name="intakeid" value="'.$intakeid.'">';
        }
        if($user_form_layout == 'Sidebar') {
            include('user_forms_sidebar.php');
        }
        include('../Form Builder/generate_form_contents.php'); ?>

        <div class="form-group">
            <p><span class="hp-red"><em>Required Fields *</em></span></p>
        </div>

        <button class="btn brand-btn pull-right gap-bottom" name="complete_form" value="complete_form" onclick="return checkMandatoryFields();">Submit</button>
    </form>
    <script>
    // $(document).ready(function () {
    //     $('[name="complete_form"]').click(function() {
    //         return checkMandatoryFields();
    //     });
    // });
    </script>
    <?php if($user_form_layout == 'Sidebar') { ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>