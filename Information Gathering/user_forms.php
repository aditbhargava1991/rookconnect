<?php $return_url = '?';
if(!empty($_GET['frefid'])) {
    $return_url = hex2bin($_GET['frefid']);
}
if(!empty($_POST['manual_btn'])) {
    include_once('../tcpdf/tcpdf.php');
    require_once('../phpsign/signature-to-image.php');
    
    $form_id = $_POST['form_id'];
    $assign_id = $_POST['assign_id'];
    $user_id = (empty($_SESSION['contactid']) ? 0 : $_SESSION['contactid']);
    $result = mysqli_query($dbc, "SELECT * FROM `user_form_assign` WHERE `form_id`='$form_id' AND '$assign_id' IN (`assign_id`,'') AND `completed_date` IS NULL");

    $infopdfid = $_POST['infopdfid'];
    if(empty($pdf_id)) {
        $pdf_result = mysqli_query($dbc, "INSERT INTO `user_form_pdf` (`form_id`, `user_id`) VALUES ('$form_id', '$user_id')");
        $pdf_id = mysqli_insert_id($dbc);
    } else {
        $infopdf = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `infogathering_pdf` WHERE `infopdfid` = '$infopdfid'"));
        $pdf_id = $infopdf['fieldlevelriskid'];
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

    $pdf->writeHTML(utf8_encode('<form action="" method="POST">'.$pdf_text.'</form>'), true, false, true, false, '');

    include('../Form Builder/generate_form_pdf_page.php');
    
    if(!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    $pdf->Output('download/'.$pdf_name, 'F');

    $today_date = date('Y-m-d');
    $projectid = $_POST['projectid'];
    $businessid = $_POST['businessid'];
    $created_by = get_contact($dbc, $user_id);
    $company = get_client($dbc, $businessid);

    if(!empty($_POST['infopdfid'])) {
        $infopdfid = $_POST['infopdfid'];
        $query_update_site = "UPDATE `infogathering_pdf` SET `fieldlevelriskid` = '$pdf_id', `pdf_path` = 'download/".$pdf_name."', `today_date` = '$today_date', `created_by` = '$created_by', `company` = '$company', `projectid` = '$projectid', `businessid` = '$businessid' WHERE `infopdfid` = '$infopdfid'";
        $result_insert_site = mysqli_query($dbc, $query_update_site);
    } else {
        $query_insert_site = "INSERT INTO `infogathering_pdf` (`infogatheringid`, `fieldlevelriskid`, `pdf_path`, `today_date`, `created_by`, `company`, `projectid`, `businessid`, `staffid`, `salesid`) VALUES ('$infogatheringid', '$pdf_id', 'download/".$pdf_name."', '$today_date', '$created_by', '$company', '$projectid', '$businessid', '$user_id', '".$_POST['salesid']."')";
        $result_insert_site = mysqli_query($dbc, $query_insert_site);
        $infopdfid = mysqli_insert_id($dbc);
    }
    
    echo '<script type="text/javascript">
    window.location.replace("manual_reporting.php?type=infogathering");
    window.open("download/'.$pdf_name.'", "_blank"); </script>';

} else {
    $form_id = $user_form_id;
    $pdf_id = $_GET['formid'];
    $default_collapse = 'in';
    include('../Form Builder/generate_form_contents.php');
} ?>

<div class="form-group">
    <p><span class="hp-red"><em>Required Fields *</em></span></p>
</div>
<script>
$(document).ready(function () {
    $('[name="manual_btn"]').click(function() {
        return checkMandatoryFields();
    });
});
</script>