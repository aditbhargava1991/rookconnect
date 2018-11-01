<?php $return_url = '?';
if(!empty($_GET['frefid'])) {
    $return_url = hex2bin($_GET['frefid']);
}
if(!empty($_POST['add_manual'])) {
    include_once('../tcpdf/tcpdf.php');
    require_once('../phpsign/signature-to-image.php');
    
    $form_id = $_POST['form_id'];
    $assign_id = $_POST['assign_id'];
    $user_id = (empty($_SESSION['contactid']) ? 0 : $_SESSION['contactid']);
    $result = mysqli_query($dbc, "SELECT * FROM `user_form_assign` WHERE `form_id`='$form_id' AND '$assign_id' IN (`assign_id`,'') AND `completed_date` IS NULL");
    $pdf_result = mysqli_query($dbc, "INSERT INTO `user_form_pdf` (`form_id`, `user_id`) VALUES ('$form_id', '$user_id')");
    $pdf_id = mysqli_insert_id($dbc);
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
    $project_form_id = $_POST['project_form_id'];
    $projectform = $_POST['projectform'];
    if($projectform > 0) {
        $query_insert_site = "UPDATE `project_form` SET `pdf_id` = '$pdf_id', `pdf_path` = 'download/".$pdf_name."', `today_date` = '$today_date' WHERE `id` = '$projectform'";
        $result_insert_site = mysqli_query($dbc, $query_insert_site);
    } else {
        $query_insert_site = "INSERT INTO `project_form` (`project_form_id`, `projectid`, `pdf_id`, `pdf_path`, `today_date`) VALUES ('$project_form_id', '$projectid', '$pdf_id', 'download/".$pdf_name."', '$today_date')";
        $result_insert_site = mysqli_query($dbc, $query_insert_site);
    }
    
	$redirect = '?edit='.$projectid.'&tab=user_forms&project_form_id='.$project_form_id;
    echo '<script> window.location.replace("'.$redirect.'"); window.open("download/'.$pdf_name.'", "_blank"); </script>';
} else {
    $form_id = $user_form_id;
    $pdf_id = $get_form['pdf_id'];
    $default_collapse = 'in';
    include('../Form Builder/generate_form_contents.php');
} ?>
<script>
$(document).ready(function () {
    $('[name="manual_btn"]').click(function() {
        return checkMandatoryFields();
    });
});
</script>