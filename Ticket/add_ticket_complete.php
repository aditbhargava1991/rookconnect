<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Complete '.TICKET_NOUN.'</h3>') ?>
<?php if($access_complete === TRUE && strpos($value_config,',Complete Hide Signature,') === FALSE) { ?>
	<div class="form-group">
		<label class="control-label col-sm-4">Staff:</label>
		<div class="col-sm-8">
			<select name="sign_off_id" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect"><option></option>
				<?php foreach($staff_list as $staff) { ?>
					<option <?= ($staff['contactid'] == $get_ticket['sign_off_id']) || (strpos($value_config,','."Complete Default Session User".',') !== FALSE && empty($get_ticket['sign_off_id']) && $staff['contactid'] == $_SESSION['contactid']) ? 'selected' : '' ?> value="<?= $staff['contactid'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-4">Signature:</label>
		<div class="col-sm-8">
			<?php if(file_exists('download/sign_off_'.$ticketid.'_'.$get_ticket['sign_off_id'].'.png')) { ?>
				<img src="download/sign_off_<?= $ticketid ?>_<?= $get_ticket['sign_off_id'] ?>.png" height="150">
			<?php } else {
				$output_name = 'sign_off_signature';
				$sign_output_options = 'data-table="tickets" data-id="'.$ticketid.'" data-id-field="ticketid"';
				include('../phpsign/sign_multiple.php'); ?>
				<?php if(strpos($value_config, ','."Complete Hide Sign & Complete".',') === FALSE) { ?>
					<button data-history-label="Complete <?= TICKET_NOUN ?>" class="<?= strpos($value_config, ',Complete Do Not Require Notes,') !== FALSE ? 'force_sign_off_click' : 'sign_off_click' ?> btn brand-btn pull-right">Sign &amp; Complete</button>
				<?php } ?>
				<?php if(config_visible_function($dbc, 'ticket') > 0 && strpos($value_config, ','."Complete Sign & Force Complete".',') !== FALSE) { ?>
					<br /><div class="clearfix"></div>
					<button data-history-label="Force Complete <?= TICKET_NOUN ?>" class="force_sign_off_click btn brand-btn pull-right">Sign &amp; Force Complete</button>
					<span class="popover-examples list-inline pull-right" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="This will complete the <?= TICKET_NOUN ?> even if there are notes that have not been entered."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<?php } ?>
				<input type="hidden" name="complete_force" value="0">
			<?php } ?>
		</div>
	</div>
<?php } else if(file_exists('download/sign_off_'.$ticketid.'_'.$get_ticket['sign_off_id'].'.png')) { ?>
	<div class="clearfix"></div>
	<div class="form-group">
		<label class="control-label col-sm-4">Signature:</label>
		<div class="col-sm-8">
			<?= get_contact($dbc, $get_ticket['sign_off_id']) ?><br />
			<img src="download/sign_off_<?= $ticketid ?>_<?= $get_ticket['sign_off_id'] ?>.png" height="150">
		</div>
	</div>
	<?php $pdf_contents[] = ['Signature', get_contact($dbc, $get_ticket['sign_off_id']).'<br /><img src="download/sign_off_'.$ticketid.'_'.$get_ticket['sign_off_id'].'.png" height="150">', 'img']; ?>
	<?php $notes = mysqli_query($dbc, "SELECT `ticket_comment`.*, `tickets`.`ticket_type` FROM ticket_comment LEFT JOIN `tickets` ON `ticket_comment`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_comment`.ticketid='$ticketid' AND `ticket_comment`.type='completion_notes' AND `ticket_comment`.`deleted`=0 ORDER BY ticketcommid DESC");
	if(mysqli_num_rows($notes) > 0) {
		if($generate_pdf) {
			ob_clean();
		}
		$odd_even = 0;
        while($row = mysqli_fetch_array($notes)) {
			$bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg';
            echo '<div class="note_block '.$bg_class.'">';
				echo profile_id($dbc, $row['created_by']);
				echo '<div class="pull-right" style="width: calc(100% - 3.5em);">'.html_entity_decode($row['comment'].$row['note']);
				echo "<em>Check In All used by ".get_contact($dbc, $row['created_by'])." at ".$row['note_date'].$row['created_date']."</em>";
			echo '</div><div class="clearfix"></div></div>';
            $odd_even++;
		}
		if($generate_pdf) {
			$pdf_contents[] = ['', ob_get_contents()];
		}
	}
} else if(strpos($value_config,',Complete Hide Signature,') === FALSE) {
	echo "<h3>".TICKET_NOUN." is not complete.</h3>";
	$pdf_contents[] = ['', TICKET_NOUN.' is not complete.'];
}
if(strpos($value_config,',Complete Main Approval,') !== FALSE) { ?>
	<div class="clearfix"></div>
	<?php if($get_ticket['main_approval'] > 0) {
		if(!file_exists('download/main_approval_'.$ticketid.'.png')) {
			if(!file_exists('download')) {
				mkdir('download',0777,true);
			}
			include_once('../phpsign/signature-to-image.php');
			$signature = sigJsonToImage(html_entity_decode($get_ticket['main_approval_signed']));
			imagepng($signature, 'download/main_approval_'.$ticketid.'.png');
		} ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Signature:</label>
			<div class="col-sm-8">
				<?= get_contact($dbc, $get_ticket['main_approval']) ?><br />
				<img src="download/main_approval_<?= $ticketid ?>.png" height="150">
			</div>
		</div>
		<?php $pdf_contents[] = ['Supervisor Approval', get_contact($dbc, $get_ticket['main_approval']).'<br /><img src="download/main_approval_'.$ticketid.'.png" height="150">', 'img']; ?>
	<?Php } else if($tile_security['approval'] > 0) { ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Signature:</label>
			<div class="col-sm-8">
				<?php $output_name = 'main_approval_signed';
				$sign_output_options = 'data-table="tickets" data-id="'.$ticketid.'" data-id-field="ticketid"';
				include('../phpsign/sign_multiple.php'); ?>
				<button class="btn brand-btn pull-right">Supervisor Approval</button>
			</div>
		</div>
	<?php }
}
if(strpos($value_config,',Complete Office Approval,') !== FALSE) { ?>
	<div class="clearfix"></div>
	<?php $complete_status = get_config($dbc, 'auto_archive_complete_tickets');
	if($get_ticket['final_approval'] > 0) {
		if(!file_exists('download/final_approval_'.$ticketid.'.png')) {
			if(!file_exists('download')) {
				mkdir('download',0777,true);
			}
			include_once('../phpsign/signature-to-image.php');
			$signature = sigJsonToImage(html_entity_decode($get_ticket['final_approval_signed']));
			imagepng($signature, 'download/final_approval_'.$ticketid.'.png');
		} ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Signature:</label>
			<div class="col-sm-8">
				<?= get_contact($dbc, $get_ticket['final_approval']) ?><br />
				<img src="download/final_approval_<?= $ticketid ?>.png" height="150">
			</div>
		</div>
		<?php $pdf_contents[] = ['Office Approval', get_contact($dbc, $get_ticket['final_approval']).'<br /><img src="download/final_approval_'.$ticketid.'.png" height="150">', 'img']; ?>
	<?Php } else if($tile_security['approval'] > 0 && $tile_security['config'] > 0) { ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Signature:</label>
			<div class="col-sm-8">
				<?php $output_name = 'final_approval_signed';
				$sign_output_options = 'data-table="tickets" data-id="'.$ticketid.'" data-id-field="ticketid"';
				include('../phpsign/sign_multiple.php'); ?>
				<button class="btn brand-btn pull-right">Office Approval</button>
			</div>
		</div>
	<?php }
}
$submitted_status = get_config($dbc, 'ticket_approval_status');
if(strpos($value_config,',Complete Submit Approval,') !== FALSE && $get_ticket['status'] != $submitted_status) { ?>
	<button class="btn brand-btn pull-right" onclick="submitApproval('<?= get_config($dbc, 'ticket_email_approval') ?>','<?= $submitted_status ?>'); return false;">Submit for Approval</button>
<?php } ?>