<?php
	/*
	 Inventory Log
	*/

	include ('../include.php');
	error_reporting(0);

	$type_get = '';

	if ( isset ( $_GET['type'] ) ) {
		$type_get = $_GET['type'];
	}
				//if ( $type_get == 'InventoryLog') {
					$query_check_credentials	= "SELECT * FROM inventory_change_log WHERE deleted = 0 ORDER BY date_time DESC LIMIT 500";
					$gettotalrows				= "SELECT * FROM inventory_change_log WHERE deleted = 0";
					$result		= mysqli_query( $dbc, $query_check_credentials );
					$xxres		= mysqli_query( $dbc, $gettotalrows );
					$num_rows	= mysqli_num_rows( $result );
					$num_rowst	= mysqli_num_rows( $xxres );

					if ( $num_rows > 0 ) {
						echo "<br />Currently displaying the last $num_rows rows (out of a total of $num_rowst rows).<br /><br />";
						echo "<table class='table table-bordered'>";
						echo "<tr class=''>";
							echo '<th>Inventory Name</th>';
							echo '<th>Old Quantity</th>';
							echo '<th>Changed Quantity</th>';
							echo '<th>New Quantity</th>';
							echo '<th>Location</th>';
							echo '<th>User</th>';
							echo '<th>Date/Time</th>';
						echo "</tr>";

					} else {
						echo "<h2 class ='list_dashboard'>No Record Found.</h2>";
					}

					while ( $row = mysqli_fetch_array( $result ) ) {
						echo "<tr>";
							echo '<td data-title="Inventory">';
								$resultw	= mysqli_query ( $dbc, "SELECT * FROM inventory WHERE inventoryid= '" . $row['inventoryid'] . "'");
								$name		= 'No name given';

								while ( $roww = mysqli_fetch_assoc($resultw) ) {
									if ( $roww['name'] !== '' && $roww['name'] !== NULL ) {
										$name = $roww['name'];
									} else if( $roww['product_name'] !== '' && $roww['product_name'] !== NULL ) {
										$name = $roww['product_name'];
									}

									echo '<span title="Category: ' . $roww['category'] . '">' . $name . ' (ID: <a target="_Blank" href="../Inventory/add_inventory.php?inventoryid=' . $row['inventoryid'] . '">' . $row['inventoryid'] . '</a>)</span>';
								}
							echo '</td>';

							echo '<td data-title="Old">'		. $row['old_inventory']		. '</td>';
							echo '<td data-title="Changed">'	. $row['changed_quantity']	. '</td>';
							echo '<td data-title="New">'	. $row['new_inventory']		. '</td>';
							/*
							echo '<td data-title="Description">Created using the following inventory:<br><ul>';
								$var = explode ( ',', $row['pieces_of_inventoryid'] );
								foreach ( $var as $invid ) {
									$resultw = mysqli_query($dbc, "SELECT * FROM inventory WHERE inventoryid= '".$invid."'");
									$name = 'No name given';
									while ( $roww = mysqli_fetch_assoc ( $resultw ) ) {
										if($roww['name'] !== '' && $roww['name'] !== NULL) {
											$name = $roww['name'];
										} else if ( $roww['product_name'] !== '' && $roww['product_name'] !== NULL ) {
											$name = $roww['product_name'];
										}

										echo '<li><span title="Category: '.$roww['category'].'">'.$name.' (ID: <a href="add_inventory.php?inventoryid='.$invid.'&bomhist=true">'.$invid.'</a>)</span></li>';
									}
								}
							echo'</td>';
							*/
							echo '<td data-title="Location">'.$row['location_of_change'].'</td>';
							echo '<td data-title="User">';
								if($row['contactid'] !== '' && $row['contactid'] !== NULL) {
								$resultu	= mysqli_query ( $dbc, "SELECT `contactid`, `first_name`, `last_name` FROM contacts WHERE contactid= '" . $row['contactid'] . "'");
									while ( $rowu = mysqli_fetch_assoc ( $resultu ) ) {
										$name = decryptIt($rowu['first_name']) . ' ' . decryptIt($rowu['last_name']);
									}
								} else { $name = 'Cross Software'; }

								echo $name;
							echo '</td>';

							$time	= substr ( $row['date_time'], strpos ( $row['date_time'], ' ') );
							$time	= date ("g:i a", strtotime($time) );
							$arr	= explode (' ', trim ( $row['date_time'] ) );
							echo '<td data-title="Date & Time">' . $arr[0] . ' at ' . $time . '</td>';
						echo "</tr>";
					}

					echo '</table>';
?>
<script>
$('document').ready(function() {
    var tables = $('table');

    tables.map(function(idx, table) {
        var rows = $(table).find('tbody > tr');
        rows.map(function(idx, row){
            if(idx%2 == 0) {
                $(row).css('background-color', '#e6e6e6');
            }
        })
    })
})
</script>