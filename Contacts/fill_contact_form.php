<?php include_once('../include.php');
if(!empty($_POST['submit_form'])) {
    include_once('../tcpdf/tcpdf.php');
    require_once('../phpsign/signature-to-image.php');
    
    $form_id = $_POST['form_id'];
    $user_id = (empty($_SESSION['contactid']) ? 0 : $_SESSION['contactid']);
    $attached_contactid = $_POST['attached_contactid'];
    $pdf_id = $_POST['pdf_id'];
    if(!($pdf_id > 0)) {
        $pdf_result = mysqli_query($dbc, "INSERT INTO `user_form_pdf` (`form_id`, `user_id`, `attached_contactid`) VALUES ('$form_id', '$user_id', '$attached_contactid')");
        $pdf_id = mysqli_insert_id($dbc);
    } else {
        mysqli_query($dbc, "UPDATE `user_form_pdf` SET `user_id` = '$user_id', `date_created` = CURRENT_TIMESTAMP WHERE `pdf_id` = '$pdf_id'");
    }

    $form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id`='$form_id'"));
    $pdf_name = preg_replace('/([^a-z])/', '', strtolower($form['name'])).'_'.$pdf_id.'.pdf';
    mysqli_query($dbc, "UPDATE `user_form_pdf` SET `generated_file`='$pdf_name' WHERE `pdf_id`='$pdf_id'");
    include('../Form Builder/generate_form_pdf.php');

    $pdf->writeHTML(utf8_encode('<form action="" method="POST">'.$pdf_text.'</form>'), true, false, true, false, '');

    include('../Form Builder/generate_form_pdf_page.php');
    
    if(!file_exists('../Contacts/download')) {
        mkdir('../Contacts/download', 0777, true);
    }
    $pdf->Output('../Contacts/download/'.$pdf_name, 'F');
    
    echo "<script> window.parent.location.reload(); window.open('../Contacts/download/$pdf_name', '_blank'); </script>";
}
$attached_contactid = $_GET['contactid'];
$form_id = $_GET['form_id'];
$pdf_id = $_GET['pdf_id'];
if($pdf_id > 0) {
    $form_pdf = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `user_form_pdf` WHERE `pdf_id` = '$pdf_id'"));
    $form_id = $form_pdf['form_id'];
    $attached_contactid = $form_pdf['attached_contactid'];
}
$user_form = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id` = '$form_id'"));
?>
<script>
$(document).ready(function () {
    $('[name="submit_form"]').click(function() {
        return checkMandatoryFields();
    });
});
</script>
<form class="form-horizontal" action="" method="POST">
	<div class="pad-left pad-right">
		<input type="hidden" name="attached_contactid" value="<?= $attached_contactid ?>">
        <input type="hidden" name="pdf_id" value="<?= $pdf_id ?>">
		<input type="hidden" name="form_id" value="<?= $form_id ?>">
		<h2><?= $user_form['name'] ?> - <?= !empty(get_client($dbc, $attached_contactid)) ? get_client($dbc, $attached_contactid) : get_contact($dbc, $attached_contactid) ?></h2>
		<?php $default_collapse = 'in';
		include('../Form Builder/generate_form_contents.php'); ?>
		<div class="form-group">
		    <p><span class="hp-red"><em>Required Fields *</em></span></p>
		</div>
		<div class="form-group pull-right">
			<a href="?" class="btn brand-btn">Back</a>
			<button name="submit_form" value="submit_form" class="btn brand-btn">Submit</button>
		</div>
	</div>
</form>