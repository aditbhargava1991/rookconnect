<?php $return_url = '?';
if(!empty($_GET['frefid'])) {
    $return_url = hex2bin($_GET['frefid']);
}
if (isset($_POST['add_ir']) || isset($_POST['save_ir'])) {
    include_once('../tcpdf/tcpdf.php');
    require_once('../phpsign/signature-to-image.php');
    
    $form_id = $_POST['user_form_id'];
    $user_id = (empty($_SESSION['contactid']) ? 0 : $_SESSION['contactid']);
    $pdf_result = mysqli_query($dbc, "INSERT INTO `user_form_pdf` (`form_id`, `user_id`) VALUES ('$form_id', '$user_id')");
    $pdf_id = mysqli_insert_id($dbc);

    $form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id`='$form_id'"));
        
    if($keep_revisions == 1 && $revision_number != '') {
        unlink('download/incident_report_'.$incidentreportid.'_'.$revision_number.'.pdf');
        $pdf_name = 'incident_report_'.$incidentreportid.'_'.$revision_number.'.pdf';
    } else {
        unlink('download/incident_report_'.$incidentreportid.'.pdf');
        $pdf_name = 'incident_report_'.$incidentreportid.'.pdf';
    }
    mysqli_query($dbc, "UPDATE `user_form_pdf` SET `generated_file`='$pdf_name' WHERE `pdf_id`='$pdf_id'");

    if(empty($date_of_report) && empty($date_of_happening)) {
        $date_of_report = date('Y-m-d');
        mysqli_query($dbc, "UPDATE `incident_report` SET `date_of_report` = '$date_of_report' WHERE `incidentreportid` = '$incidentreportid'");
    }
    if(empty($completed_by) && empty($contactid)) {
        $completed_by = $_SESSION['contactid'];
        mysqli_query($dbc, "UPDATE `incident_report` SET `completed_by` = '$completed_by' WHERE `incidentreportid` = '$incidentreportid'");
    }
    mysqli_query($dbc, "UPDATE `incident_report` SET `pdf_id` = '$pdf_id' WHERE `incidentreportid` = '$incidentreportid'");

    include('../Form Builder/generate_form_pdf.php');

    $pdf->writeHTML(utf8_encode('<form action="" method="POST">'.$pdf_text.'</form>'), true, false, true, false, '');

    include('../Form Builder/generate_form_pdf_page.php');
    
    if(!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    $pdf->Output('download/'.$pdf_name, 'F');
    
    echo "<script> window.open('download/$pdf_name', '_blank'); </script>";
} else {
    $form_id = $user_form_id;
    $default_collapse = 'in';
    if(!empty($_GET['incidentreportid'])) {
        $incidentreportid = $_GET['incidentreportid'];
        $pdf_id = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `pdf_id` FROM incident_report WHERE incidentreportid='$incidentreportid'"))['pdf_id'];
        echo '<input type="hidden" name="inc_rep_pdf_id" value="'.$pdf_id.'">';
    }
    include('../Form Builder/generate_form_contents.php'); ?>

    <div class="form-group pull-right">
        <?php if(!IFRAME_PAGE) { ?>
            <a href="<?= $from; ?>" class="btn brand-btn">Back</a>
        <?php } else { ?>
            <a href="../blank_loading_page.php" class="btn brand-btn">Cancel</a>
        <?php } ?>
        <button type="submit" name="save_ir" id="save_ir" value="Submit" class="btn brand-btn" onclick="storePanel();">Save</button>
        <button type="submit" name="add_ir" id="add_ir" value="Submit" class="btn brand-btn">Submit</button>
    </div>
<?php } ?>

<div class="form-group">
    <p><span class="hp-red"><em>Required Fields *</em></span></p>
</div>
<script>
$(document).ready(function () {
    $('[name="save_ir"],[name="add_ir"]').click(function() {
        return checkMandatoryFields();
    });
});
</script>