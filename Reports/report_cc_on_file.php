<?php
	/*
	 * Credit Card on File Report
	 */

	include ('../include.php');
	error_reporting(0);
checkAuthorised('report'); ?>

<?php
				echo '<h2>Contacts with Credit Card on File</h2>';

				$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `category`, `businessid`, `first_name`, `last_name`, `office_phone`, `email_address` FROM `contacts` WHERE `cc_on_file`=1 AND `deleted`=0 AND status=1"),MYSQLI_ASSOC));
				$num_rows		= count( $result );
				
				if ( $num_rows <= 0 ) {
					echo '<h2 class="list_dashboard">No Records Found.</h2>';
				
				} else {
					echo "<br /><table class='table table-bordered'>";
						echo "<tr class=''>";
							echo '<th>Business Name</th>';
							echo '<th>Category</th>';
							echo '<th>First Name</th>';
							echo '<th>Last Name</th>';
							echo '<th>Office Phone</th>';
							echo '<th>Email Address</th>';
							//echo '<th>Function</th>';
						echo "</tr>";

						foreach($result as $contactid) {
							$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `category`, `businessid`, `first_name`, `last_name`, `office_phone`, `email_address` FROM `contacts` WHERE `contactid`='$contactid'"));
							echo "<tr>";
								echo '<td data-title="Business Name">';
									$businessid	= $row[ 'businessid' ];
									$resultw	= mysqli_query ( $dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `contactid`='$businessid' AND `deleted`=0");
									
									while ( $roww = mysqli_fetch_assoc($resultw) ) {
										$name = ( $roww['name'] !== '' && $roww['name'] !== NULL ) ? $roww['name'] : '-';
									}
									
									echo $name;
								echo '</td>';
								
								echo '<td data-title="Category">'		. $row['category']		. '</td>';
								echo '<td data-title="First Name">'		. decryptIt($row['first_name'])	. '</td>';
								echo '<td data-title="Last Name">'		. decryptIt($row['last_name'])		. '</td>';
								echo '<td data-title="Office Phone">'	. $row['office_phone']	. '</td>';
								echo '<td data-title="Email Address">'	. decryptIt($row['email_address'])	.'</td>';
								//echo '<td data-title="Edit">'			. '<a href="/Contacts/add_contacts.php?category=' . $row['category'] . '&contactid=' . $row['contactid'] . '">Edit</a></td>';
							echo "</tr>";
						}
					echo '</table>';
				} ?>
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