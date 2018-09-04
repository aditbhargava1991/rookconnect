<?php $current_tab = (!empty($_GET['day_tab']) ? $_GET['day_tab'] : '');
if($current_tab == '' && check_subtab_persmission($dbc, 'daysheet', ROLE, 'ticket') === TRUE) {
	$current_tab = 'ticket';
} else if($current_tab == '' && check_subtab_persmission($dbc, 'daysheet', ROLE, 'work_order') === TRUE) {
	$current_tab = 'work_order';
} else if($current_tab == '' && check_subtab_persmission($dbc, 'daysheet', ROLE, 'overview') === TRUE) {
	$current_tab = 'overview';
}
$link = '';
if(empty($_GET['tab'])) {
	$link = '?';
} else {
	$link = '?tab='.$_GET['tab'].'&';
}
$title = "Day Sheet";
switch($current_tab) {
	case 'ticket': $title = TICKET_TILE; break;
	case 'tasks': $title = "My Tasks"; break;
	case 'work_order': $title = "My Work Orders"; break;
	case 'overview': $title = "Day Overview"; break;
}
?>
<h1 class="double-pad-bottom"><?php echo $title; ?></h1>

<div class="gap-left tab-container1 mobile-100-container">
	<div class="pull-left tab">
		<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to see the list of <?= TICKET_TILE ?> for the current day."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<?php if ( check_subtab_persmission($dbc, 'daysheet', ROLE, 'ticket') === TRUE ) { ?>
			<a href="<?php echo $link; ?>day_tab=ticket"><button type="button" class="btn brand-btn mobile-block <?php echo ($current_tab == 'ticket' ? 'active_tab' : ''); ?> mobile-100"><?= TICKET_TILE ?></button></a>
		<?php } ?>
	</div>
	<div class="pull-left tab">
		<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to see the list of Tasks for the current day."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<?php if ( check_subtab_persmission($dbc, 'daysheet', ROLE, 'ticket') === TRUE ) { ?>
			<a href="<?php echo $link; ?>day_tab=tasks"><button type="button" class="btn brand-btn mobile-block <?php echo ($current_tab == 'tasks' ? 'active_tab' : ''); ?> mobile-100">My <?= TASK_TILE ?></button></a>
		<?php } ?>
	</div>
	<div class="pull-left tab">
		<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to view all Work Orders for the current day."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<?php if ( check_subtab_persmission($dbc, 'daysheet', ROLE, 'work_order') === TRUE ) { ?>
			<a href="<?php echo $link; ?>day_tab=work_order&contactid=<?php echo $_SESSION['contactid']; ?>"><button type="button" class="btn brand-btn mobile-block <?php echo ($current_tab == 'work_order' ? 'active_tab' : ''); ?> mobile-100">My Work Orders</button></a>
		<?php } ?>
	</div>
	<div class="pull-left tab">
		<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to check over your daily <?= TICKET_TILE ?>/Tasks."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<?php if ( check_subtab_persmission($dbc, 'daysheet', ROLE, 'overview') === TRUE ) { ?>
			<a href="<?php echo $link; ?>day_tab=overview"><button type="button" class="btn brand-btn mobile-block <?php echo ($current_tab == 'overview' ? 'active_tab' : ''); ?> mobile-100">Day Overview</button></a>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
</div>

<?php switch($current_tab) {
	case 'ticket': include('ticket_daysheet.php'); break;
	case 'tasks': include('task_daysheet.php'); break;
	case 'work_order': include('workorder_daysheet.php'); break;
	case 'overview': include('day_overview.php'); break;
} ?>