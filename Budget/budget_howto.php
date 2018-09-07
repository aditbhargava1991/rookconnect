<div class="hide-titles-mob">
	<br /><br />
	<?php $active_Workflow = '';
	$active_Keywords	= '';
	
	if ( empty ( $_GET['status'] ) ) {
		$active_Workflow = ' active_tab';
		$_GET['status'] = 'Workflow';
	}
	if ( $_GET['status'] == 'Workflow' ) {
		$active_Workflow = ' active_tab';
	}
	if ( $_GET['status'] == 'Keywords' ) {
		$active_Keywords = ' active_tab';
	}
	
	echo '<a href="budget.php?tab=howto&status=Workflow"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_Workflow . '">Workflow</button></a>&nbsp;&nbsp';
	echo '<a href="budget.php?tab=howto&status=Keywords"><button type="button" class="btn brand-btn mobile-block mobile-100 ' . $active_Keywords . '">Keywords</button></a>&nbsp;&nbsp'; ?>
	<br /><br />
</div>
<?php
	if ( $_GET['status'] == 'Workflow' ) {
		echo "Workflow Content Will be Updated Soon";
		//echo '<img src="download/ROOK-CallLog-Funnel.png" alt="Workflow" />';
	}
	if ( $_GET['status'] == 'Keywords' ) {
		echo "Keywords Content Will be Updated Soon";
		//echo '<img src="download/ROOK-CallLog-Definitions.png" alt="Keywords" />';
	}