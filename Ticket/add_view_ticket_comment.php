<?php if($access_any > 0) { ?>
	<a class="pull-right no-toggle" href="" title="Add a Note" onclick="addNote('<?= $comment_type ?>',this); return false;"><img class="inline-img" src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" /></a>
	<div class="clearfix"></div>
<?php } ?>
<div class="col-sm-12">
	<div class="ticket_comments" id="no-more-tables" data-type="<?= $comment_type ?>"><?php include('add_ticket_view_notes.php'); ?></div>
</div>