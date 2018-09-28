<?php
	/*
	 * Service Queue for POS Orders that need preparation
	 */

	include ('../include.php');
	error_reporting(0);
	
	if ( isset($_GET['posid']) && $_GET['posid'] != '' ) {
		$posid	= preg_replace ( '/[^0-9]+/', '', $_GET['posid'] );
		$result	= mysqli_query ( $dbc, "DELETE FROM `service_queue` WHERE `posid`='$posid'" );
		echo '<script>window.location.replace("service_queue.php");</script>';
	}
?>
</head>

<body><?php
	include_once ('../navigation.php');
	checkAuthorised('service_queue'); ?>

	<div class="container triple-pad-bottom">		
		<div class="row">
		
			<div class="col-sm-12"><h1>Service Queue Dashboard</h1></div>
			
			<div class="clearfix"></div>

			<div class="gap-top double-gap-bottom clearfix">
				<div class="mobile-100-container"><a href="javascript:void(0);" onclick="javascript:window.location.replace('service_queue.php');" class="btn brand-btn mobile-block pull-right mobile-100-pull-right">Refresh</a></div>
			</div>
				
			<div id="no-more-tables"><?php
				/* Pagination Counting */
				$rowsPerPage = 25;
				$pageNum = 1;

				if ( isset($_GET['page']) ) {
					$pageNum = $_GET['page'];
				}

				$offset = ($pageNum - 1) * $rowsPerPage;
				
				$query_check_credentials = "SELECT * FROM `service_queue` WHERE `status` = 'Pending' LIMIT $offset, $rowsPerPage";
				$query = "SELECT COUNT(*) AS numrows FROM `service_queue` WHERE `status` = 'Pending'";

				$result		= mysqli_query($dbc, $query_check_credentials);
				$num_rows	= ($result) ? mysqli_num_rows($result) : 0;
				
				if ( $num_rows > 0 ) {
					
					echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

					echo '<table class="table table-bordered table-striped">';
                        echo '<thead>';
                            echo '<tr class="hidden-xs hidden-sm">';
                                echo '<th>Invoice #</th>';
                                echo '<th>Invoice Date</th>';
                                echo '<th>Status</th>';
                                echo '<th>Function</th>';
                            echo '</tr>';
                        echo '</thead>';

						while ( $row = mysqli_fetch_array($result) ) {
							echo '<tr>';
								echo '<td data-title="Invoice #">' . $row['posid'] . '</td>';
								echo '<td data-title="Invoice Date">' . $row['inv_date'] . '</td>';
								echo '<td data-title="Status">' . html_entity_decode ( $row['status'] ) . '</td>';
								echo '<td data-title="Function" nowrap>
										<a href="?posid=' . $row['posid'] . '"><button class="btn brand-btn">Serviced</button></a>
									</td>';
							echo '</tr>';
						}
					echo '</table>';
					
					echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
				
				} else {
					echo '<h2>No Records Found.</h2>';
				} ?>
			
			</div><!-- #no-more-tables -->

		</div><!-- .row -->
	</div><!-- .container -->
	
<?php include ('../footer.php'); ?>