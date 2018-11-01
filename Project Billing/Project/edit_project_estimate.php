<div id="head_estimates">
	<?php $estimate_list = mysqli_query($dbc, "SELECT `estimateid` FROM `estimates` WHERE `projectid`='$projectid' AND '$projectid' > 0 AND `deleted`=0");
	if(mysqli_num_rows($estimate_list) > 0) {
		echo "<h3>".ESTIMATE_TILE."</h3>";
		while($estimateid = mysqli_fetch_assoc($estimate_list)['estimateid']) {
			include('../Estimate/estimates_overview.php');
		}
	} else {
		echo "<h3>No Estimates Attached to this ".PROJECT_NOUN.".</h3>";
	} ?>
	<div class="clearfix"></div>
</div>