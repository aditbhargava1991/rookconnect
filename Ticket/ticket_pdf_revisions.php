<?php include('../include.php');
include('config.php');

$ticketid = $_GET['ticketid'];
$form = $_GET['form'];
$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
$form_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_pdf` WHERE `id` = '$form'")); ?>

<script type="text/javascript">
function remForm(form, ticket, rev, div) {
	if(confirm('Are you sure you want to remove this form?')) {
		$(div).remove();
		$.post('ticket_ajax_all.php?action=removePdfForm',{formid:form,ticket:ticket,revision:rev});
	}
}
</script>
<div class="container">
	<div class="row">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        	<h3 class="inline"><?= $form_config['pdf_name'] ?> - <?= get_ticket_label($dbc, $ticket) ?></h3>
            <div class="pull-right gap-top"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
            <div class="clearfix"></div>
        	<hr />

			<?php $form_query = mysqli_query($dbc, "SELECT `ticket_pdf_field_values`.`revision`, `revisions`.`last_revision` FROM `ticket_pdf_field_values` LEFT JOIN (SELECT `ticketid`, `pdf_type`, MAX(`revision`) `last_revision` FROM `ticket_pdf_field_values` WHERE `deleted`=0 GROUP BY `ticketid`, `pdf_type`) `revisions` ON `ticket_pdf_field_values`.`ticketid`=`revisions`.`ticketid` AND `ticket_pdf_field_values`.`pdf_type`=`revisions`.`pdf_type` WHERE `ticket_pdf_field_values`.`pdf_type` = '$form' AND `ticket_pdf_field_values`.`ticketid` = '$ticketid' AND `ticket_pdf_field_values`.`deleted` = 0 GROUP BY `ticket_pdf_field_values`.`revision` ORDER BY `ticket_pdf_field_values`.`revision` DESC");
			while($row = mysqli_fetch_assoc($form_query)) { ?>
				<div class="dashboard-item form-horizontal" style="border: 1px solid #ACA9A9;" >
					<h3>
						<a target="_blank" href="../Ticket/ticket_pdf_custom.php?<?= $current_tile ?>ticketid=<?= $ticketid ?>&form=<?= $form ?>&revision=<?= $row['revision'] ?>"><?= get_ticket_label($dbc, $ticket) ?> Revision #<?= $row['revision'] ?> of <?= $row['last_revision'] ?></a>
						<?php if($tile_security['edit'] > 0) { ?>
							<div class="pull-right">
								<a target="_blank" href="../Ticket/index.php?<?= $current_tile ?>custom_form=<?= $form ?>&revision=<?= $row['revision'] ?>&ticketid=<?= $ticketid ?>&pdf_mode=edit&revision_mode=edit" class="small pad-10"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-edit-icon.png" class="inline-img theme-color-icon no-toggle" title="Edit Revision"></a>
								<a target="_blank" href="../Ticket/index.php?<?= $current_tile ?>custom_form=<?= $form ?>&revision=<?= $row['revision'] ?>&ticketid=<?= $ticketid ?>&pdf_mode=edit&revision_mode=new" class="small pad-10"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img theme-color-icon no-toggle" title="Create Revision"></a>
								<?php if($tile_security['config'] > 0) { ?>
									<a href="" onclick="remForm('<?= $form ?>', '<?= $ticketid ?>', '<?= $row['revision'] ?>', $(this).closest('.dashboard-item')); return false;" class="small pad-10"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png" class="inline-img no-toggle" title="Archive Revision"></a>
								<?php } ?>
							</div><div class="clearfix"></div>
						<?php } ?>
					</h3>
				</div>
			<?php } ?>
		</form>
	</div>
</div>