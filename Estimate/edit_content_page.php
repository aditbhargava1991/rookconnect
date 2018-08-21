<?php include_once('../include.php');
checkAuthorised('estimate');
$id = $_GET['id'];
if(isset($_POST['submit'])) {
	$content = filter_var(htmlentities($_POST['content']),FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `estimate_content_page` SET `content`='$content' WHERE `id`='$id'");
	$before_change = '';
	$history = "Estimates content page entry has been updated for estimate id $estimateid. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
}
if($id > 0) {
	$page = $dbc->query("SELECT `content` FROM `estimate_content_page` WHERE `id`='$id'")->fetch_assoc(); ?>
	<form class="form-horizontal" method="POST" action="">
		<a class="pull-right" href="../blank_loading_page.php"><img class="slider-close" src="../img/icons/cancel.png"></a><br />
		<div class="col-sm-12">
			<h3>Content</h3>
			<textarea name="content"><?= html_entity_decode($page['content']) ?></textarea>
			<button class="btn brand-btn pull-right" name="submit" type="submit" value="submit">Save</button>
			<a class="btn brand-btn pull-left" href="../blank_loading_page.php">Cancel</a>
		</div>
	</form>
<?php } else {
	echo '<h4>Please select a content page to edit.</h4>';
}
