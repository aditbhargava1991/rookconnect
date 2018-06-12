<div class="tab-container mobile-100-container">
	<?php if ( check_subtab_persmission($dbc, 'email_communication', ROLE, 'internal') === TRUE ) { ?>
		<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Contains all internal email communication, including follow up by/date and status, which can be edited from here."><img src="../img/info.png" width="20"></a></span>
		<a href="?type=email_comm&client_projectid=<?php echo $projectid; ?>&category=Internal&from_url=<?php echo urlencode($_GET['from_url']); ?>"><button type="button" class="mobile-100 btn brand-btn mobile-block <?php echo ((empty($_GET['category']) || $_GET['category'] == 'Internal') ? 'active_tab' : ''); ?>">Internal</button></a>&nbsp;&nbsp;
		</div>
	<?php } ?>
	<?php if ( check_subtab_persmission($dbc, 'email_communication', ROLE, 'external') === TRUE ) { ?>
		<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Contains all external email communication, including follow up by/date and status, which can be edited from here."><img src="../img/info.png" width="20"></a></span>
		<a href="?type=email_comm&client_projectid=<?php echo $projectid; ?>&category=External&from_url=<?php echo urlencode($_GET['from_url']); ?>"><button type="button" class="mobile-100 btn brand-btn mobile-block <?php echo ((!empty($_GET['category']) && $_GET['category'] == 'External') ? 'active_tab' : ''); ?>">External</button></a>&nbsp;&nbsp;
		</div>
	<?php } ?>
	<?php if ( check_subtab_persmission($dbc, 'email_communication', ROLE, 'log') === TRUE ) { ?>
		<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="All email communication tied to this project is logged here."><img src="../img/info.png" width="20"></a></span>
		<a href="?type=email_comm&client_projectid=<?php echo $projectid; ?>&category=Log&from_url=<?php echo urlencode($_GET['from_url']); ?>"><button type="button" class="btn mobile-100 brand-btn mobile-block <?php echo ((!empty($_GET['category']) && $_GET['category'] == 'Log') ? 'active_tab' : ''); ?>">Log</button></a>&nbsp;&nbsp;
		</div><br /><br />
	<?php } ?>
</div>

<?php if($_GET['category'] == 'Log') {
	include('../Email Communication/log_display.php');
} else {
	$_GET['type'] = (empty($_GET['category']) ? 'Internal' : $_GET['category']);
	include('../Email Communication/email_list.php');
} ?>