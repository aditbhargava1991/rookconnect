<?php
/*
Dashboard
*/
?>
<?php if($_POST['submit_tab']): ?>
	<?php
	$tabNames = explode(',', $_POST['estimate_tab_name']);
	foreach($tabNames as $tabName) {
		$tab .= "'" . $tabName . "',";
	}

	$tab = rtrim($tab, ",");
	$get_current_tabs = mysqli_fetch_all(mysqli_query($dbc,"DELETE FROM `estimate_tab` WHERE `estimate_tab` NOT IN (" . $tab . ")"));
	foreach($tabNames as $tabName) {
		$get_tabs = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `estimate_tab` FROM `estimate_tab` WHERE `estimate_tab` = '" . $tabName . "'"));
		if(empty($get_tabs)) {
			$query_insert_tab = "INSERT INTO `estimate_tab` (`estimate_tab`) VALUES ('" . $tabName . "')";
			mysqli_query($dbc, $query_insert_tab);
		}
	}

	$before_change = '';
	$history = "Estimates tab entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);

	?>
<?php endif; ?>
<div class="pad-left">
	<a href='?tab=add'><button type="button" class="btn brand-btn mobile-block <?php echo $_GET['tab'] == 'add' ? 'active_tab' : ''; ?>" >Add <?= ESTIMATE_TILE ?> Tabs</button></a>
	<?php $get_tabs = mysqli_query($dbc,"select estimate_tab, estimate_tab_id from estimate_tab");
	while($row = mysqli_fetch_assoc($get_tabs)) {
		$tab_list[] = $row['estimate_tab']; ?>
		<a href='?tab=<?php echo $row['estimate_tab_id']; ?>' class='btn brand-btn mobile-block <?php echo $_GET['tab'] == $row['estimate_tab_id'] ? 'active_tab' : ''; ?>'>Configure <?php echo $row['estimate_tab']; ?> Tab</a>
	<?php } ?>
</div>
<br><br>
<?php if($_GET['tab'] == 'add'): ?>
<form action='' method='POST'>
	<div class="form-group">
		<label for="first_name" class="col-sm-1 control-label text-left">Tabs:</label>
		<div class="col-sm-8">
			<input name="estimate_tab_name" id="estimate_tab_name" value='<?php echo implode(',', $tab_list); ?>' type="text" class="form-control"><p></p>
		</div>
	</div>

	<div class="clearfix double-gap-bottom"></div>

    <div class="col-sm-6">
        <a href="estimate.php" class="btn config-btn btn-lg">Back</a>
	</div>
	<div class="col-sm-6">
        <button	type="submit" name="submit_tab"	value="submit_tab" class="btn config-btn btn-lg	pull-right">Submit</button>
    </div>
</form>
<?php else: ?>
	<?php include('tab_estimate_config.php') ?>
<?php endif; ?>
