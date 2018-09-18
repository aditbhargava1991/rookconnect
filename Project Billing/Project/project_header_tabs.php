<?php if(empty($_GET['nav']) || $_GET['nav'] != 'no_tabs') {

	echo "<div class='tab-container1 mobile-100-container'>";
	if(empty($_GET['from'])) { ?>
		<div class="pull-left tab nav-subtab">
			<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see projects that are not yet approved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
			if ( check_subtab_persmission($dbc, 'project', ROLE, 'pending') === TRUE ) { ?>
				<a href='project.php?tab=projects&type=Pending'><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo $active_pending; ?>"><?php echo 'Pending '.PROJECT_TILE; ?></button></a>&nbsp;&nbsp;<?php
			} else { ?>
				<button type="button" class="btn disabled-btn mobile-block mobile-100 <?php echo $pending; ?>">Pending Projects</button>&nbsp;&nbsp;<?php
			} ?>
		</div>
		<?php foreach($project_tabs as $key => $tab): ?>
			<div class="pull-left tab nav-subtab">
				<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see approved <?php echo $tab.' '.PROJECT_TILE; ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
				if ( check_subtab_persmission($dbc, 'project', ROLE, $project_vars[$key]) === TRUE ) { ?>
					<a href='project.php?tab=projects&type=<?php echo $project_vars[$key]; ?>'><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo ${'active_'.$project_vars[$key]}; ?>"><?php echo $tab.' '.PROJECT_TILE; ?></button></a>&nbsp;&nbsp;<?php
				} else { ?>
					<button type="button" class="btn disabled-btn mobile-block mobile-100 <?php echo $pending; ?>">Pending Projects</button>&nbsp;&nbsp;<?php
				} ?>
			</div>
		<?php endforeach; ?>
		</div>
		<br><br>
		<?php if($type != 'Pending') {
			$pp = get_project_path_milestone($dbc, 1, 'project_path');
			if($pp != '') {
				$main_project = '';
				$path_project = '';
				$gantt_project = '';
				if(strpos($_SERVER['REQUEST_URI'], 'project.php') !== FALSE) {
					$main_project = ' active_tab';
				}
				if(strpos($_SERVER['REQUEST_URI'], 'project_path.php') !== FALSE) {
					$path_project = ' active_tab';
				}
				if(strpos($_SERVER['REQUEST_URI'], 'project_gantt_chart.php') !== FALSE) {
					$gantt_project = ' active_tab';
				}
			}
		}
		echo '<div class="clearfix"></div><br><br>';
	}
} ?>