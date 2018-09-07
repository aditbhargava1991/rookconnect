<?php include_once('../include.php');
checkAuthorised('form_builder');
$return_url = '?';
if(!empty($_GET['frefid'])) {
	$return_url = hex2bin($_GET['frefid']);
}
if(!empty($_POST['complete_form'])) {
	include_once('../tcpdf/tcpdf.php');
	require_once('../phpsign/signature-to-image.php');
	
	$preview_form = $_POST['preview_form'];
	$printable_pdf = $_POST['printable_pdf'];

	$form_id = $_POST['form_id'];
	$assign_id = $_POST['assign_id'];
	$user_id = (empty($_SESSION['contactid']) ? 0 : $_SESSION['contactid']);
	$result = mysqli_query($dbc, "SELECT * FROM `user_form_assign` WHERE `form_id`='$form_id' AND '$assign_id' IN (`assign_id`,'') AND `completed_date` IS NULL");

	if($preview_form != 'true' && $printable_pdf != 'true') {
		$pdf_result = mysqli_query($dbc, "INSERT INTO `user_form_pdf` (`form_id`, `user_id`) VALUES ('$form_id', '$user_id')");
		$pdf_id = mysqli_insert_id($dbc);
		if(mysqli_num_rows($result)) {
			$assign_id = mysqli_fetch_array($result)['assign_id'];
			mysqli_query($dbc, "UPDATE `user_form_assign` SET `completed_date`=CURRENT_TIMESTAMP, `pdf_id`='$pdf_id' WHERE `assign_id`='$assign_id'");
		} else {
			mysqli_query($dbc, "INSERT INTO `user_form_assign` (`form_id`, `user_id`, `completed_date`, `pdf_id`) VALUES ('$form_id', '$user_id', CURRENT_TIMESTAMP, '$pdf_id')");
			$assign_id = mysqli_insert_id($dbc);
		}
	}
	
	$form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id`='$form_id'"));
	if($printable_pdf == 'true') {
		$pdf_name = preg_replace('/([^a-z])/', '', strtolower($form['name'])).'_printable.pdf';
	} else if($preview_form != 'true') {
		$pdf_name = preg_replace('/([^a-z])/', '', strtolower($form['name'])).'_'.$assign_id.'.pdf';
		mysqli_query($dbc, "UPDATE `user_form_pdf` SET `generated_file`='$pdf_name' WHERE `pdf_id`='$pdf_id'");
	} else {
		$pdf_name = preg_replace('/([^a-z])/', '', strtolower($form['name'])).'_preview.pdf';
	}
	include('../Form Builder/generate_form_pdf.php');

	$pdf->writeHTML(utf8_encode('<form action="" method="POST">'.$pdf_text.'</form>'), true, false, true, false, '');

	include('../Form Builder/generate_form_pdf_page.php');
	
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	$pdf->Output('download/'.$pdf_name, 'F');

	if($printable_pdf == 'true') {
		ob_clean();
		echo 'download/'.$pdf_name;
	} else {
		echo "<script> window.location.replace('download/$pdf_name'); </script>";
	}
} else { ?>
	<form name="assign_form" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0; border-top: 1px solid #E1E1E1;"' : '' ?>>
		<?php $form_id = $_GET['id'];
		$default_collapse = 'in';
        if($user_form_layout == 'Sidebar') {
            include('user_forms_sidebar.php');
        }
	    include('../Form Builder/generate_form_contents.php'); ?>
		<div class="form-group">
			<p><span class="hp-red"><em>Required Fields *</em></span></p>
		</div>

		<button class="btn brand-btn pull-right" name="complete_form" value="complete_form" onclick="return checkMandatoryFields();">Submit</button>
		<a href="<?= $return_url ?>" class="btn brand-btn pull-left">Back</a>
		<?php if($user_form_layout == 'Sidebar') { ?>
			</div>
		</div>
		<?php } ?>
	</form>
<?php } ?>