<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0);
$type_get = '';
if(isset($_GET['type'])) {
	$type_get = $_GET['type'];
}
// END EXPORT
?>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('inventory');
?>
<div class="container">

  <div class="row add">
		<h1	class="triple-pad-bottom">Bill of Material History</h1>

		<div class="pad-left double-gap-bottom"><a href="bill_of_material.php" class="btn config-btn">Back to Dashboard</a></div>
		<?php
if ($type_get == 'log') {
?><br><!--<a href="#" class="btn brand-btn btn-lg" onclick="history.go(-1);return false;">Back</a>--><br><?php
    $query_check_credentials = "SELECT * FROM bill_of_material_log WHERE deleted = 0 ORDER BY date_time DESC LIMIT 1000";
	 $gettotalrows = "SELECT * FROM bill_of_material_log WHERE deleted = 0";
            $result = mysqli_query($dbc, $query_check_credentials);
			$xxres = mysqli_query($dbc, $gettotalrows);
            $num_rows = mysqli_num_rows($result);
			  $num_rowst = mysqli_num_rows($xxres);
            if($num_rows > 0) {
				echo "<br>Currently displaying the last $num_rows rows (out of a total of $num_rowst rows).<br><br>";
                echo "<table class='table table-bordered table-striped'>";
                    echo "<thead>";
                        echo "<tr class='hidden-xs hidden-sm'>";
                            echo '<th>Tab</th>';
                            echo '<th>Inventory Name</th>';
                            echo '<th>Description</th>';
                            echo '<th>Date/Time</th>';
                            echo '<th>Author</th>';
                        echo "</tr>";
                    echo "</thead>";
            } else {
                echo "<h2 class ='list_dashboard'>No Record Found.</h2>";
            }

            while($row = mysqli_fetch_array( $result ))
            {
                echo "<tr>";
						echo '<td data-title="Tab">' . $row['type'] . '</td>';
                        echo '<td data-title="Inventory">';
						$resultw = mysqli_query($dbc, "SELECT * FROM inventory WHERE inventoryid= '".$row['inventoryid']."'");
						$name = 'No name given';
						while($roww = mysqli_fetch_assoc($resultw)) {
							if($roww['name'] !== '' && $roww['name'] !== NULL) {
								$name = $roww['name'];
							} else if($roww['product_name'] !== '' && $roww['product_name'] !== NULL) {
								$name = $roww['product_name'];
							}
							echo '<span title="Category: '.$roww['category'].'">'.$name.' (ID: <a href="add_inventory.php?inventoryid='.$row['inventoryid'].'&bomhist=true">'.$row['inventoryid'].'</a>)</span>';
						}
						echo '</td>';
						echo '<td data-title="Description">';

					if($row['type'] == 'Add') {
						echo 'Created using the following inventory:<br><ul>';
						   $var=explode(',',$row['pieces_of_inventoryid']);
						   foreach($var as $invid)
							{
								$resultw = mysqli_query($dbc, "SELECT * FROM inventory WHERE inventoryid= '".$invid."'");
								$name = 'No name given';
								while($roww = mysqli_fetch_assoc($resultw)) {
									if($roww['name'] !== '' && $roww['name'] !== NULL) {
										$name = $roww['name'];
									} else if($roww['product_name'] !== '' && $roww['product_name'] !== NULL) {
										$name = $roww['product_name'];
									}
									echo '<li><span title="Category: '.$roww['category'].'">'.$name.' (ID: <a href="add_inventory.php?inventoryid='.$invid.'&bomhist=true">'.$invid.'</a>)</span></li>';
								}
							}
							echo '</ul>';
					} else {
						   echo 'Changed from using the following inventory:<br><ul>';
						   $var=explode(',',$row['old_pieces_of_inventoryid']);
						   foreach($var as $invid)
							{
								$resultw = mysqli_query($dbc, "SELECT * FROM inventory WHERE inventoryid= '".$invid."'");
								$name = 'No name given';
								while($roww = mysqli_fetch_assoc($resultw)) {
									if($roww['name'] !== '' && $roww['name'] !== NULL) {
										$name = $roww['name'];
									} else if($roww['product_name'] !== '' && $roww['product_name'] !== NULL) {
										$name = $roww['product_name'];
									}
									echo '<li><span title="Category: '.$roww['category'].'">'.$name.' (ID: <a href="add_inventory.php?inventoryid='.$invid.'&bomhist=true">'.$invid.'</a>)</span></li>';
								}
							}
							echo '</ul>To using the following inventory:<br><ul>';
							$var=explode(',',$row['pieces_of_inventoryid']);
						   foreach($var as $invid)
							{
								$resultw = mysqli_query($dbc, "SELECT * FROM inventory WHERE inventoryid= '".$invid."'");
								$name = 'No name given';
								while($roww = mysqli_fetch_assoc($resultw)) {
									if($roww['name'] !== '' && $roww['name'] !== NULL) {
										$name = $roww['name'];
									} else if($roww['product_name'] !== '' && $roww['product_name'] !== NULL) {
										$name = $roww['product_name'];
									}
									echo '<li><span title="Category: '.$roww['category'].'">'.$name.' (ID: <a href="add_inventory.php?inventoryid='.$invid.'&bomhist=true">'.$invid.'</a>)</span></li>';
								}
							}
							echo '</ul>';
					}
						echo'</td>';
						$time = substr($row['date_time'], strpos($row['date_time'], ' '));
						$time = date("g:i a", strtotime($time));
						$arr = explode(' ',trim($row['date_time']));
						echo '<td data-title="Date & Time">'.$arr[0].' at '.$time. '</td>';
						echo '<td data-title="Author">' . $row['contact'] . '</td>';
                echo "</tr>";
            }

            echo '</table></div>'; } ?>
					
  </div>
<?php include ('../footer.php'); ?>