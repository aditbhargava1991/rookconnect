<?php include_once('../include.php');
checkAuthorised('equipment');

$security = get_security($dbc, 'equipment');
?>

<div id="equip_div" class="container">
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<div class="pull-right settings-block">