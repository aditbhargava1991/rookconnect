<?php
/*
Properties Listing
*/
include ('../include.php');
?>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('properties');
?>

<div class="container">
	<div class="row">

    <form name="form_sites" method="post" action="" class="form-inline" role="form">

        <h1 class="double-pad-bottom">Properties</h1>

        <div id="no-more-tables">
            <?php
            // Display Pager
            if(vuaed_visible_function($dbc, 'properties') == 1) {
                echo '<a href="add_property.php" class="btn brand-btn pull-right">Add Property</a>';
            }
            $query   = "SELECT COUNT(propertyid) AS numrows FROM properties";

            $rowsPerPage = ITEMS_PER_PAGE;
            $pageNum = 1;

            if(isset($_GET['page'])) {
                $pageNum = $_GET['page'];
            }

            $offset = ($pageNum - 1) * $rowsPerPage;
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            $query_check_credentials = "SELECT * FROM properties WHERE deleted = 0 LIMIT $offset, $rowsPerPage";

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead><tr class='hidden-xs hidden-sm'>";
                echo "<th>Property Name</th>
                    <th>Short Name</th>
                    <th>Type</th>
                    <th># of Units</th>
                    <th>Lot #</th>
                    <th>Function</th>
                    </tr></thead>";
            } else{
                echo "<h2>No Record Found.</h2>";
            }
            while($row = mysqli_fetch_array( $result ))
            {
                echo '<tr>';
                echo '<td data-title="Property Name">' . $row['property_name'] . '</td>';
                echo '<td data-title="Short Name">' . $row['short_name'] . '</td>';
                echo '<td data-title="Short Name">' . $row['type'] . '</td>';
                echo '<td data-title="Tax ID">' . $row['no_of_units'] . '</td>';
                echo '<td data-title="Tax ID">' . $row['lot_number'] . '</td>';
                echo '<td>';
                if(vuaed_visible_function($dbc, 'properties') == 1) {
                    echo '<a href=\'add_property.php?propertyid='.$row['propertyid'].'\'>Edit</a> | ';
                    echo '<a href=\'../delete_restore.php?action=delete&propertyid='.$row['propertyid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a>';
                }
                echo '</td>';

                echo "</tr>";
            }

            echo '</table>';
            if(vuaed_visible_function($dbc, 'properties') == 1) {
                echo '<a href="add_property.php" class="btn brand-btn pull-right">Add Property</a>';
            }

            // how many rows we have in database

            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            ?>
        </div>
    </form>

	</div>
</div>
<?php include ('../footer.php'); ?>