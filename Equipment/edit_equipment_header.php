<?php include_once('../include.php');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs')); ?>

<div class="blue tile-navbar">
    <?php if($overview > 0 && $_GET['edit'] > 0) { ?>
        <a href="?edit=<?= $_GET['edit'] ?>"><span class="block-clear <?= empty($_GET['subtab']) ? 'active' : '' ?>">Overview</span></a>
    <?php } ?>
	<a href="?edit=<?= $_GET['edit'] ?>&subtab=edit"><span class="block-clear <?= (!($overview > 0 || $_GET['edit'] > 0) && empty($_GET['subtab'])) || $_GET['subtab'] == 'edit' ? 'active' : '' ?>">Equipment</span></a>
	<?php if($_GET['edit'] > 0) { ?>
		<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
			<a href="?edit=<?= $_GET['edit'] ?>&subtab=inspections"><span class="block-clear <?= $_GET['subtab'] == 'inspections' ? 'active' : '' ?>">Inspections</span></a>
		<?php } ?>
		<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_order') === TRUE ) { ?>
			<a href="?edit=<?= $_GET['edit'] ?>&subtab=work_orders"><span class="block-clear <?= $_GET['subtab'] == 'work_orders' ? 'active' : '' ?>">Work Orders</span></a>
		<?php } ?>
		<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
			<a href="?edit=<?= $_GET['edit'] ?>&subtab=service"><span class="block-clear <?= $_GET['subtab'] == 'service' ? 'active' : '' ?>">Service Schedule</span></a>
		<?php } ?>
		<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
			<a href="?edit=<?= $_GET['edit'] ?>&subtab=expenses"><span class="block-clear <?= $_GET['subtab'] == 'expenses' ? 'active' : '' ?>">Expenses</span></a>
		<?php } ?>
		<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
			<a href="?edit=<?= $_GET['edit'] ?>&subtab=balance"><span class="block-clear <?= $_GET['subtab'] == 'balance' ? 'active' : '' ?>">Balance Sheet</span></a>
		<?php } ?>
		<?php if ( in_array('Equipment Assignment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'eqipment', ROLE, 'equip_assign') === TRUE ) { ?>
			<a href="?edit=<?= $_GET['edit'] ?>&subtab=equip_assign"><span class="block-clear <?= $_GET['subtab'] == 'equip_assign' ? 'active' : '' ?>">Equipment Assignment</span></a>
		<?php } ?>
	<?php } ?>
</div>