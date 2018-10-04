<?php $estimateid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
if(!($estimateid > 0)) {
	mysqli_query($dbc, "INSERT INTO `estimate` (`created_date`,`created_by`) VALUES ('".date('Y-m-d')."','".$_SESSION['contactid']."')");
	$estimateid = mysqli_insert_id($dbc);
	$before_change = '';
	$history = "Estimates entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	echo "<script> window.location.replace('?edit=$estimateid'); </script>";
	$_GET['edit'] = $estimateid;
} else {
	include('estimates_transfer.php');
} ?>
<div class="blue tile-navbar">
	<a href="?edit=<?= $_GET['edit'] ?>"><span class="block-clear <?= $_GET['tab'] == '' ? 'active' : '' ?>">Details</span></a><?php

    $query = mysqli_query($dbc, "SELECT IFNULL(`scope_name`,'') FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY IFNULL(`scope_name`,'') ORDER BY MIN(`sort_order`)");
    $scope_count = $query->num_rows;

    if ( $scope_count > 0 ) {
        while($row = mysqli_fetch_array($query)) {
            $scope_list[config_safe_str($row[0])] = $row[0];
        }
    } else {
        $scope_list['scope'] = 'Scope 1';
    }

    if ( $scope_count > 1 ) {
        foreach($scope_list as $scope_id => $scope) { ?>
            <a href="?edit=<?= $_GET['edit'] ?>&tab=scope&status=<?= $scope_id ?>"><span class="block-clear <?= ($_GET['tab'] == 'scope' && $_GET['status'] == $scope_id) ? 'active' : '' ?>"><?= $scope ?></span></a><?php
        }
    } ?>

	<!-- <a href="?edit=<?= $_GET['edit'] ?>&tab=scope"><span class="block-clear <?= $_GET['tab'] == 'scope' ? 'active' : '' ?>">Scope</span></a> -->
	<a href="?edit=<?= $_GET['edit'] ?>&tab=analysis&financials=<?= $_GET['edit'] ?>"><span class="block-clear <?= $_GET['tab'] == 'analysis' ? 'active' : '' ?>">Cost Analysis</span></a>
    <a href="?edit=<?= $_GET['edit'] ?>&tab=options"><span class="block-clear <?= $_GET['tab'] == 'options' ? 'active' : '' ?>">Design</span></a>
	<a href="?edit=<?= $_GET['edit'] ?>&tab=preview"><span class="block-clear <?= $_GET['tab'] == 'preview' ? 'active' : '' ?>">Preview</span></a>
	<div class="clearfix"></div>
</div>
<?php switch($_GET['tab']) {
	case 'scope': include('estimate_scope.php'); break;
	case 'preview': include('estimate_preview.php'); break;
    case 'analysis': include('estimate_financials.php'); break;
    case 'options': include('estimate_options.php'); break;
	default: include('estimate_details.php'); break;
} ?>
