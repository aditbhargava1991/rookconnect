<?php if(($access_any > 0 || strpos($value_config, ',Notes Anyone Can Add,') !== FALSE) && !($strict_view > 0)) { ?>
	<a class="no-toggle" href="" title="Add a Note" onclick="addNote('<?= $comment_type ?>',this, <?= strpos($value_config, ',Notes Anyone Can Add,') !== FALSE ? 1 : 0 ?>); return false;"><img class="inline-img" data-history-label="<?= $comment_type ?>" src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" /></a>
	<div class="clearfix"></div>
<?php } ?>
<div class="col-sm-12">
	<div class="ticket_comments" id="no-more-tables" data-type="<?= $comment_type ?>"><?php include('add_ticket_view_notes.php'); ?></div>
</div>