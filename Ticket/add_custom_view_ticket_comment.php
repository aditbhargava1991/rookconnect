<h3><?= get_config($dbc, 'ticket_custom_notes_heading') ?></h3>
<?php if(!$generate_pdf) {
	$custom_comment_types = explode('#*#',get_config($dbc, 'ticket_custom_notes_type'))
}
foreach($custom_comment_types as $comment_type) {
	echo "<h4>$comment_type</h4>";
	$comment_type = config_safe_str($comment_type);
	include('add_view_ticket_comment.php');
	echo '<div class="clearfix"></div>';
} ?>