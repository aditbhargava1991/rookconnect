<?php include_once('../include.php');
$table = filter_var($_GET['table'],FILTER_SANITIZE_STRING);
$id = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
$date = filter_var($_GET['date'],FILTER_SANITIZE_STRING); ?>
<script>
function approve() {
	$.post('../Project/projects_ajax.php?action=approvals', {
		field: 'approvals',
		table: '<?= $table ?>',
		signature: $('[name=approval]').val(),
		contactid: '<?= $_SESSION['contactid'] ?>',
		status: 1,
		id: '<?= $id ?>',
		date: '<?= $date ?>',
		invoice: '<?= $_GET['invoice'] ?>'
	}, function(response) { window.location.reload(); });
}
</script>
<div class="form-horizontal col-sm-12">
	<h2>Approve <?= $table == 'tickets' ? get_ticket_label($dbc, $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$id'")->fetch_assoc()) : 'Task #'.$id ?></h2>
	<div class="form-group">
		<label class="col-sm-4">Signature:</label>
		<div class="col-sm-8">
			<?php $output_name = 'approval';
			include('../phpsign/sign_multiple.php'); ?>
		</div>
	</div>
	<a href="" class="btn brand-btn pull-left">Cancel</a>
	<a class="btn brand-btn cursor-hand pull-right" onclick="approve();">Approve</a>
</div>