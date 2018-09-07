<?php
/*
Archived Data Listing
*/
include ('database_connection.php');
include ('function.php');

?>
<script src="<?php echo WEBSITE_URL;?>/js/jquery.cookie.js"></script>

</head>
<body>
<?php include_once ('navigation.php');

?>
<div class="container">
    <div class="row">

		<div class="tabs">
		    <ul id="myTab" class="tab-links nav nav-pills">
		        <li class="active"><a href="#tab1">Staff</a></li>
                <li><a href="#tab2"><?= VENDOR_TILE ?></a></li>
				<li><a href="#tab3">Patients</a></li>
				<li><a href="#tab4">Inventory</a></li>
				<li><a href="#tab5">Service Code</a></li>
			</ul>
		</div>

        <div class="tab-content">

             <!-- Archived Staff -->
            <div id="tab1" class="tab-pane active triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div class="table-responsive">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM contacts WHERE WHERE category='Staff' AND deleted = 1");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table'>";
                        echo "<tr>
                                <th>License#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone#</th>
                                <th>Credential</th>
                                <th>Scheduled Hours</th>
                                <th>Restore</th>
                                </tr>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td>' . $row['license'] . '</td>';
                            echo '<td>' . decryptIt($row['first_name']).' '.decryptIt($row['last_name']) . '</td>';
                            echo '<td>' . $row['email_address'] . '</td>';
                            echo '<td>' . $row['phone_number'] . '</td>';
                            echo '<td>' . $row['credential'] . '</td>';
                            echo '<td>' . $row['scheduled_hours'] . '</td>';
                            echo '<td><a href=\'delete_restore.php?action=restore&contactid='.$row['contactid'].'\' onclick="return confirm(\'Are you sure?\')">Restore</a></td>';
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Staff -->

             <!-- Archived vendors -->
            <div id="tab2" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div id="no-more-tables">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM contacts WHERE category='Vendor' AND deleted = 1");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table class='table table-bordered'>";
                        echo "<tr class='hidden-xs hidden-sm'>
                                <th>".VENDOR_TILE."</th>
                                <th>Contact Number</th>
                                <th>Products</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Restore</th>
                                </tr>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td>'.$row['vendor'].'</td>';
                            echo '<td>' . $row['office_phone'] . '</td>';
                            echo '<td>' . $row['products'] . '</td>';
                            echo '<td>' . $row['business_street'] . '</td>';
                            echo '<td>' . $row['email_address'] . '</td>';
                            echo '<td data-title="Restore"><a href=\'delete_restore.php?action=restore&contactid='.$row['contactid'].'\' onclick="return confirm(\'Are you sure?\')">Restore</a></td>';
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived vendors -->

             <!-- Archived patients -->
            <div id="tab3" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div class="table-responsive">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM contacts WHERE category='Patient' AND deleted = 1");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table'>";
                        echo "<tr>
                                <th>Name</th>
                                <th>Home Phone</th>
                                <th>Email</th>
                                <th>DOB</th>
                                <th>Account Balance</th>
                                <th>Remaining Invoice</th>
                                <th>Restore</th>
                                </tr>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td>' . decryptIt($row['first_name']).' '.decryptIt($row['last_name']). '</td>';
                            echo '<td>' . $row['home_phone'] . '</td>';
                            echo '<td>' . $row['alt_phone'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>' . $row['birth_date'] . '</td>';
                            echo '<td>$' . round($row['account_balance'], 2). '</td>';
                            echo '<td><a href=\'delete_restore.php?action=restore&patientid='.$row['patientid'].'\' onclick="return confirm(\'Are you sure?\')">Restore</a></td>';
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived patients -->

             <!-- Archived Inventory -->
            <div id="tab4" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div class="table-responsive">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM inventory WHERE deleted = 1");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table'>";
                        echo "<tr>
								<th>Code</th>
                                <th>Description</th>
                                <th>Stock Amount</th>
                                <th>Restore</th>
                                </tr>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
                            echo '<td>'.$row['code'].'</td>';
                            echo '<td>' . $row['description'] . '</td>';
                             echo '<td>' . $row['max_bin'] . '</td>';
                            echo '<td><a href=\'delete_restore.php?action=restore&inventoryid='.$row['inventoryid'].'\' onclick="return confirm(\'Are you sure?\')">Restore</a></td>';
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Inventory -->

             <!-- Archived Service Code -->
            <div id="tab5" class="tab-pane triple-gap-top">
                <form name="form_sites" method="post" action="" class="form-inline" role="form">
                    <div class="table-responsive">
                        <?php
                        $result = mysqli_query($dbc, "SELECT * FROM services WHERE deleted = 1");

                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                        echo "<table border='2' cellpadding='10' class='table'>";
                        echo "<tr>
								<th>Code#</th>
								<th>Description</th>
								<th>Fee</th>
                                <th>Restore</th>
                                </tr>";
                        } else{
                            echo "<h2>No Record Found.</h2>";
                        }
                        while($row = mysqli_fetch_array( $result ))
                        {
                            echo '<tr>';
							echo '<td>' . $row['service_code'] . '</td>';
							echo '<td>' . $row['description'] . '</td>';
							echo '<td>' . $row['fee'] . '</td>';
                            echo '<td><a href=\'delete_restore.php?action=restore&serviceid='.$row['serviceid'].'\' onclick="return confirm(\'Are you sure?\')">Restore</a></td>';
                            echo "</tr>";
                        }

                        echo '</table>';
                        ?>
                    </div>
                </form>
            </div>
            <!-- Archived Service Code -->


        </div>

    </div>

</div>

<script>
	$('#myTab a').click(function (e) {
	  e.preventDefault()
	  $(this).tab('show')
	})

    $('#myTab a').on('shown.bs.tab', function(e){
      //save the latest tab using a cookie:
      $.cookie('last_tab', $(e.target).attr('href'));
    });

    //activate latest tab, if it exists:
    var lastTab = $.cookie('last_tab');
    if (lastTab) {
        $('ul.nav-pills').children().removeClass('active');
        $('a[href='+ lastTab +']').parents('li:first').addClass('active');
        $('div.tab-content').children().removeClass('active');
        $(lastTab).addClass('active');
    }
</script>
<?php include ('footer.php'); ?>