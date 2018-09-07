<?php
	/*
	 * Software Guide Settings
	 */

	include ('../include.php');
    include ('check_security.php');
	error_reporting(0);

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		$howtoguide = implode ( ',', $_POST['howtoguide'] );
        $notes = implode ( ',', $_POST['notes'] );
		$get_field_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT COUNT(`fieldconfigid`) AS `fieldconfigid` FROM `field_config`" ) );

		if ( $get_field_config['fieldconfigid'] > 0 ) {
			$query_update	= "UPDATE `field_config` SET `how_to_guide_dashboard`='{$howtoguide}', `notes_dashboard`='{$notes}' WHERE `fieldconfigid`=1";
			$result_update	= mysqli_query ( $dbc, $query_update );
		} else {
			$query_insert	= "INSERT INTO `field_config` (`how_to_guide_dashboard`, `notes_dashboard`) VALUES ('{$howtoguide}', '{$notes}') WHERE `fieldconfigid`=1";
			$result_insert	= mysqli_query ( $dbc, $query_insert );
		}

		if(isset($_GET['maintype'])) {
			$submit_url = WEBSITE_URL . '/Manuals/manual.php?maintype=htg';
			echo '<script type="text/javascript"> window.location.replace('.$submit_url.'); </script>';
		}
		else {
			echo '<script type="text/javascript">window.location.replace("guides_dashboard.php");</script>';
		}
	}
?>
</head>

<body>
	<?php
		include ('../navigation.php');
		checkAuthorised('how_to_guide');
	?>

	<div class="container">
		<div class="row">
			<h1>Software Guide</h1>
			<?php if(isset($_GET['maintype'])): ?>
				<div class="pad-left gap-top double-gap-bottom"><a href="<?php echo WEBSITE_URL ?>/Manuals/manual.php?maintype=htg" class="btn config-btn">Back to Dashboard</a></div>
			<?php else: ?>
				<div class="pad-left gap-top double-gap-bottom"><a href="guides_dashboard.php" class="btn config-btn">Back to Dashboard</a></div>
			<?php endif; ?>
			<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
				<div class="panel-group" id="accordion2">

					<!-- Software Guide -->
                    <div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove fields that will appear on Software Guide dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
									Choose Fields for Software Guide Dashboard<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>
						<div id="collapse_field" class="panel-collapse collapse">
							<div class="panel-body" id="no-more-tables"><?php
								$get_field_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `how_to_guide_dashboard` FROM `field_config`" ) );
								$value_config = ',' . $get_field_config['how_to_guide_dashboard'] . ','; ?>
								<div class="row">
                                    <div class="col-sm-3"><input type="checkbox" <?php if (strpos($value_config, ',Tile,') !== FALSE) { echo " checked"; } ?> value="Tile" name="howtoguide[]" />&nbsp;&nbsp;Tile</div>
                                    <div class="col-sm-3"><input type="checkbox" <?php if (strpos($value_config, ',Subtab,') !== FALSE) { echo " checked"; } ?> value="Subtab" name="howtoguide[]">&nbsp;&nbsp;Sub Tab</div>
                                    <div class="col-sm-3"><input type="checkbox" <?php if (strpos($value_config, ',Order,') !== FALSE) { echo " checked"; } ?> value="Order" name="howtoguide[]">&nbsp;&nbsp;Order</div>
                                    <div class="col-sm-3"><input type="checkbox" <?php if (strpos($value_config, ',Description,') !== FALSE) { echo " checked"; } ?> value="Description" name="howtoguide[]">&nbsp;&nbsp;Description</div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3"><input type="checkbox" <?php if (strpos($value_config, ',Image,') !== FALSE) { echo " checked"; } ?> value="Image" name="howtoguide[]">&nbsp;&nbsp;Image</div>
                                    <div class="col-sm-3"><input type="checkbox" <?php if (strpos($value_config, ',Function,') !== FALSE) { echo " checked"; } ?> value="Function" name="howtoguide[]">&nbsp;&nbsp;Function</div>
                                    <div class="clearfix"></div>
                                </div>
							</div><!-- .panel-body -->
						</div><!-- .panel-collapse -->
					</div><!-- .panel .panel-default -->

					<!-- Notes -->
                    <div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove fields that will appear on Notes dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_notes" >
									Choose Fields for Notes Dashboard<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>
						<div id="collapse_notes" class="panel-collapse collapse">
							<div class="panel-body" id="no-more-tables"><?php
								$get_field_config_notes = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `notes_dashboard` FROM `field_config`" ) );
								$value_config = ',' . $get_field_config_notes['notes_dashboard'] . ','; ?>
								<div class="row">
                                    <div class="col-sm-3"><input type="checkbox" <?php if (strpos($value_config, ',Tile,') !== FALSE) { echo " checked"; } ?> value="Tile" name="notes[]" />&nbsp;&nbsp;Tile</div>
                                    <div class="col-sm-3"><input type="checkbox" <?php if (strpos($value_config, ',Subtab,') !== FALSE) { echo " checked"; } ?> value="Subtab" name="notes[]">&nbsp;&nbsp;Sub Tab</div>
                                    <div class="col-sm-3"><input type="checkbox" <?php if (strpos($value_config, ',Description,') !== FALSE) { echo " checked"; } ?> value="Description" name="notes[]">&nbsp;&nbsp;Description</div>
                                    <div class="col-sm-3"><input type="checkbox" <?php if (strpos($value_config, ',Function,') !== FALSE) { echo " checked"; } ?> value="Function" name="notes[]">&nbsp;&nbsp;Function</div>
                                    <div class="clearfix"></div>
                                </div>
							</div><!-- .panel-body -->
						</div><!-- .panel-collapse -->
					</div><!-- .panel .panel-default -->

				</div><!-- .panel-group -->

				<div class="form-group">
					<div class="col-sm-6">
						<?php if(isset($_GET['maintype'])): ?>
							<a href="<?php echo WEBSITE_URL ?>/Manuals/manual.php?maintype=htg" class="btn config-btn btn-lg">Back</a>
						<?php else: ?>
							<a href="guides_dashboard.php" class="btn config-btn btn-lg">Back</a>
						<?php endif; ?>
					</div>
					<div class="col-sm-6">
						<button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
					</div>
				</div>

			</form>

		</div><!-- .row -->
	</div><!-- .container -->

<?php include ('../footer.php'); ?>