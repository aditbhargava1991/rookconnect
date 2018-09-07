<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>'.$communication_type.' Communication</h3>') : '' ?>
<a class="no-toggle" href="" title="Add <?= $communication_type ?> Communication" onclick="addCommunication('<?= $communication_type ?>','<?= $communication_method ?>'); return false;"><img class="inline-img" src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" /></a>
<div class="clearfix"></div>

<div class="col-sm-12">
	<div class="ticket_communication" id="no-more-tables" data-type="<?= $communication_type ?>" data-method="<?= $communication_method ?>"><?php include('add_ticket_view_communication.php'); ?></div>
</div>