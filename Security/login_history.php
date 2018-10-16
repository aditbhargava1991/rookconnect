<?php // Tile Configuration History
include ('../include.php');
checkAuthorised('security');
error_reporting(0);
?>
</head>
<body style="min-height:0px;">
	<div class="container">
		<div class="row">
			<?php $id = filter_var($_GET['user_id'],FILTER_SANITIZE_STRING);
			$title = ($id > 0 ? get_contact($dbc, $id) : 'All Users');
			$start_date = date('Y-m-d', strtotime($_POST['start_date'] ?: '-7days'));
			$end_date = date('Y-m-d', strtotime($_POST['end_date'] ?: 'today')); ?>
			<h1>Login History - <?= $title ?><a href='login_history_pdf.php?id=<?= $id ?>&start=<?= $start_date ?>&end=<?= $end_date ?>' class='pull-right text-sm'>PDF <img class='inline-img' src='../img/pdf.png'></a></h1>
			<form class="form-horizontal form-group" action="" method="POST">
				<div class="col-sm-5">
					<label class="col-sm-4 control-label">Start Date:</label>
					<div class="col-sm-8">
						<input type="text" name="start_date" class="form-control datepicker" value="<?= $start_date ?>">
					</div>
				</div>
				<div class="col-sm-5">
					<label class="col-sm-4 control-label">End Date:</label>
					<div class="col-sm-8">
						<input type="text" name="end_date" class="form-control datepicker" value="<?= $end_date ?>">
					</div>
				</div>
				<div class="col-sm-2">
					<button class="btn brand-btn" type="submit" name="submit">Search</button>
				</div>
			</form>
			<p>
			<?php $history = mysqli_query($dbc, "SELECT `user_name`, `login_at`, `login_ip`, `success` FROM `login_history` WHERE '$id' IN (`contactid`,'ALL') AND `login_at` BETWEEN '$start_date' AND '$end_date 23:59:59' ORDER BY `login_at` DESC");
			while($row = mysqli_fetch_array($history)) {
				echo $row['user_name']." ".($row['success'] ? 'successfully logged in' : 'unsuccessfuly attempted to log in')." at ".convert_timestamp_mysql($dbc, $row['login_at'], true).($row['login_ip'] != '' ? " (from IP address ".$row['login_ip'].")" : '').".<br />\n";
			} ?>
			</p>
		</div>
	</div>
</body>