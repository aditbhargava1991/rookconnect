<?php error_reporting(0);
if(!isset($_GET['mobile_view'])) {
    include_once ('../include.php');
} else {
    include_once ('../database_connection.php');
    include_once ('../global.php');
    include_once ('../function.php');
    include_once ('../output_functions.php');
    include_once ('../email.php');
    include_once ('../user_font_settings.php');
}
checkAuthorised('staff');
$rookconnect = get_software_name();
if(!empty($_POST['subtab']) && $_POST['subtab'] != 'schedule') {
    $action_page = 'staff_edit.php?contactid='.$_GET['contactid'];
    if($_POST['subtab'] == 'certificates') {
        $action_page = 'certificate.php?contactid='.$_GET['contactid'];
    } else if($_POST['subtab'] == 'history') {
        $action_page = 'staff_history.php?contactid='.$_GET['contactid'];
    } else if($_POST['subtab'] == 'reminders') {
        $action_page = 'staff_reminder.php?contactid='.$_GET['contactid'];
    } else if($_POST['subtab'] == 'schedule') {
        $action_page = 'staff_schedule.php?contactid='.$_GET['contactid'];
    }

	?>
	<form action="<?php echo $action_page; ?>" method="post" id="change_page">
		<input type="hidden" name="subtab" value="<?php echo $_POST['subtab']; ?>">
	</form>
	<script type="text/javascript"> document.getElementById('change_page').submit(); </script>
<?php }
include_once('../Staff/staff_schedule_include.php');
?>