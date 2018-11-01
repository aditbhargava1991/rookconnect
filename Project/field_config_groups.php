<?php error_reporting(0);
include_once('../include.php');

echo '<div class="pad-10">';
include('../Calendar/teams_list.php');
echo '</div>';

if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_groups.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>