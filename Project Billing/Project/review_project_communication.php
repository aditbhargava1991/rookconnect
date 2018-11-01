<?php
if($_GET['type'] == 'email_comm') {
	$ec = 'active_tab';
	$current_tab = 'Email Communication';
}
if($_GET['type'] == 'phone_comm') {
	$pc = 'active_tab';
	$current_tab = 'Phone Communication';
}
?>
<a href='review_project.php?maintype=comm&type=email_comm&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $ec; ?>" >Email Communication</button></a>&nbsp;&nbsp;
<a href='review_project.php?maintype=comm&type=phone_comm&projectid=<?php echo $projectid; ?>&from_url=<?php echo urlencode($_GET['from_url']); ?>'><button type="button" class="btn brand-btn mobile-block mobile-100  <?php echo $pc; ?>" >Phone Communication</button></a>&nbsp;&nbsp;
<br><br>
<?php
if($_GET['type'] == 'email_comm') {
	include ('review_project_email_communication.php');
}
if($_GET['type'] == 'phone_comm') {
	include ('review_project_phone_communication.php');
}
?>