<?php $manual = filter_var($_GET['manual'],FILTER_SANITIZE_STRING);
if(isset($_POST['submit'])) {
	include_once('../phpsign/signature-to-image.php');
	$staff = $_SESSION['contactid'];
	// Insert a row if it isn't already there
	$query_insert_row = "INSERT INTO `manuals_staff` (`manualtypeid`, `staffid`) SELECT '$manual', '$staff' FROM (SELECT COUNT(*) rows FROM `manuals_staff` WHERE `manualtypeid`='$manual' AND `staffid`='$staff') LOGTABLE WHERE rows=0";
	mysqli_query($dbc, $query_insert_row);
	$before_change = '';
	$history = "Manuals staff entry added. <br />";
	add_update_history($dbc, 'hr_history', $history, '', $before_change);
	$done = $_POST['validation'] != '' ? 1 : 0;

		$before_change = '';

		$query_update_ticket = "UPDATE `manuals_staff` SET `done` = '$done', `today_date` = '$today_date' WHERE `manualtypeid` = '$manual' AND staffid='$staff' AND done=0";
    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

		$history = "Manual Staff Updated. <br />";
		add_update_history($dbc, 'hr_history', $history, '', $before_change);

    //Update reminders to done
    mysqli_query($dbc, "UPDATE `reminders` SET `done` = $done WHERE `contactid` = '$staff' AND `src_table` = 'manuals' AND `src_tableid` = '$manual'");

	$manual_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `manuals_staff` WHERE `manualtypeid` = '$manual' AND `staffid` = '$staff' AND `done` = 1 ORDER BY `today_date` DESC"))['manualstaffid'];
	$comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
	$signature = sigJsonToImage($_POST['output']);
	imagepng($signature, 'download/sign_'.$manual_id.'.png');
	$_GET['manualid_pdf'] = $manual;
	include_once('manual_pdf.php');
	$to = get_config($dbc, 'manual_completed_email');
	if(!empty($to)) {
		$manual = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `heading`, `heading_number`, `sub_heading`, `sub_heading_number`, `third_heading`, `third_heading_number` FROM `manuals` WHERE `manualtypeid`='$manual'"));
		$heading = $manual['third_heading'] != '' ? $manual['third_heading_number'].' '.$manual['third_heading'] : ($manual['sub_heading'] != '' ? $manual['sub_heading_number'].' '.$manual['sub_heading'] : $manual['heading_number'].' '.$manual['heading']);
		$subject = str_replace(['[CATEGORY]','[HEADING]','[USER]','[COMMENT]'],[$manual['category'],$heading,get_contact($dbc, $staff),$comment],get_config($dbc, 'manual_subject_completed'));
		$body = html_entity_decode(str_replace(['[CATEGORY]','[HEADING]','[USER]','[COMMENT]'],[$manual['category'],$heading,get_contact($dbc, $staff),($comment == '' ? '' : 'Comment: '.$comment)],get_config($dbc, 'manual_body_completed')));
		try {
			send_email('', $to, '', '', $subject, $body, $pdf_path);
		} catch(Exception $e) { }
	}
	echo "<script> window.location.replace('?tile_name=".$tile."'); </script>";
}
$get_manual = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `manuals` WHERE `manualtypeid`='$manual'"));
$value_config = ','.get_config($dbc, 'hr_fields').','; ?>
<form name="manual" action="" method="POST">
	<div class='scale-to-fill has-main-screen no-pad'>
		<div class='main-screen form-horizontal'>
            <div class="standard-body-title">
                <h3>
                    <?= $get_manual['category'] != '' ? '<b>'.$get_manual['category'].'</b><br />' : '' ?>
                    <?= $get_manual['third_heading'] != '' ? $get_manual['third_heading_number'].' '.$get_manual['third_heading'] : ($get_manual['sub_heading'] != '' ? $get_manual['sub_heading_number'].' '.$get_manual['sub_heading'] : $get_manual['heading_number'].' '.$get_manual['heading']) ?>
                    <a href="../blank_loading_page.php" class="pull-right"><img src="../img/icons/cancel.png" class="inline-img no-toggle small" title="Cancel"></a>
                    <a href="?manualid_pdf=<?= $manual ?>" class="pull-right" target="_blank"><img class="inline-img no-toggle small" title="Download PDF" src="../img/pdf.png"></a><div class="clearfix"></div>
                </h3>
            </div>
			<div class="standard-body-content pad-horiz-2 pad-vertical">
                <div class="clearfix"></div>
				<div class="pull-right full-width"><?= html_entity_decode(get_config($dbc, "manual_header")) ?></div>
				<div class="form-group">
					<div class="col-sm-12">
						<?= html_entity_decode($get_manual['description']) ?>
					</div>
				</div>

				<?php $uploads = mysqli_query($dbc, "SELECT `uploadid`, `upload`,`type` FROM `manuals_upload` WHERE `manualtypeid`='$manual'");
				if(mysqli_num_rows($uploads) > 0) {
					echo '<div class="form-group">
						<div class="col-sm-12">
							<ul>';
								while($upload = mysqli_fetch_assoc($uploads)) { ?>
									<li><?php switch($upload['type']) {
										case 'document':
											echo 'Document: ';
											echo '<a href="download/'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a>'; // - <a href="add_manual.php?action=delete&uploadid='.$row['uploadid'].'&manual='.$manual.'&type=document" onclick="return confirm(\'Are you sure?\')">Delete</a>';
											break;
										case 'link':
											echo 'Link: ';
											echo '<a href="'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a>'; // - <a href="add_manual.php?action=delete&uploadid='.$row['uploadid'].'&manual='.$manual.'&type=link" onclick="return confirm(\'Are you sure?\')">Delete</a>';
											break;
										case 'video':
											echo 'Video: ';
											echo '<a href="download/'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a>'; // - <a href="add_manual.php?action=delete&uploadid='.$row['uploadid'].'&manual='.$manual.'&type=video" onclick="return confirm(\'Are you sure?\')">Delete</a>';
											break;
									} ?></li>
								<?php }
							echo "</div>
						</div>
					</ul>";
				} ?>

				<?php if (strpos($value_config, ','."Comments".',') !== FALSE) { ?>
					<div class="form-group">
						<label for="comment" class="col-sm-12">Comments:</label>
						<div class="col-sm-12">
							<textarea name="comment" rows="5" cols="50" class="form-control"></textarea>
						</div>
					</div>
				<?php } ?>

				<div class="form-group">
					<label for="comment" class="col-sm-12 form-checkbox any-width"><input type="checkbox" name="validation" value="CONFIRMED"> I confirm that I have read and agree to the above:</label>
				</div>

				<?php if (strpos($value_config, ','."Signature box".',') !== FALSE) { ?>
					<div class="form-group">
						<label class="col-sm-12">Signature:</label>
						<div class="col-sm-12">
							<?php $output_name = 'output';
							include ('../phpsign/sign_multiple.php'); ?>
						</div>
					</div>
				<?php } ?>
				<button name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
				<div class="clearfix"></div>
				<?= html_entity_decode(get_config($dbc, "manual_footer")) ?>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>
