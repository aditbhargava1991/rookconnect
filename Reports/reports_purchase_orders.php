<?php error_reporting(0);
/*
EIS
*/
include ('../include.php');
checkAuthorised('report');
$actual_link = WEBSITE_URL.$_SERVER['PHP_SELF'];
?>
<style>
@media (max-width: 767px) {
.until {
	margin-top:10px;
}
</style>
</head>
<body>

<?php include_once ('../navigation.php');
checkAuthorised();
?>
<script type="text/javascript">
$(document).on('change', 'select[name="choose_table"]', function() { location = this.value; });
</script>
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
        <form name="form_sites" method="post" action="" class="form-inline" role="form">
        <div style="background-color:rgba(142,142,142,0.50); border-radius:10px; border:1px solid white; padding:10px;" >
            <h2><?php
                if($_GET['type'] == 'accpay') {
                    echo "Accounts Payable";
                }
                ?></h2>
        	<!-- start time -->

            <center><div class="form-group col-sm-12">
				<div class="form-group col-sm-5">
                    <div class="col-sm-4"><label for="search_email">Report Type:</label></div>
                    <div class="col-sm-8">
                        <select name="choose_table" id="dynamic_select" class="chosen-select-deselect form-control" data-placeholder="Choose a Report">
                            <option <?php if($_GET['type'] == 'accpay') { echo "selected='selected'"; } ?> value="<?php echo $actual_link; ?>?type=accpay&report=<?= $_GET['report'] ?>">Accounts Payable</option>
                        </select>
                    </div></div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
				<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
				<button type="submit" name="display_all_email" value="Display All" class="btn brand-btn mobile-block">Display All</button>
			</div><div class="clearfix"></div></center>

           <br>
            <div class="visible-xs-block visible-sm-block clearfix">&nbsp;</div>
		</div>
		</form>

        <div class="no-more-tables">

        <?php

        if ($_GET['type'] == 'accpay' || !isset($_GET['type'])) {

            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            }
            if (isset($_POST['display_all_email'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            }

            if($starttime == 0000-00-00) {
                $starttime = '0001-01-01';
            }

            if($endtime == 0000-00-00) {
                $endtime = '2222-02-02';
            }

			// Pagination
			$rowsPerPage = 25;
			$pageNum = 1;
			if(isset($_GET['page'])) {
				$pageNum = $_GET['page'];
			}
			$offset = ($pageNum - 1) * $rowsPerPage;
			$limit = "LIMIT $offset, $rowsPerPage";
			
            if($endtime !== '2222-02-02' || $starttime !== '0001-01-01') {
				$query_check_credentials = "SELECT * FROM purchase_orders WHERE deleted = 0 AND invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."' ORDER BY invoice_date DESC $limit";
				$query_num = "SELECT COUNT(*) numrows FROM purchase_orders WHERE deleted = 0 AND invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."'";

                // Display what times they have set
                if($starttime !== '0001-01-01' && $endtime !== '2222-02-02') {
                echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying results from ".$starttime." until ".$endtime.".</span><br><br>";
                } else if ($starttime !== '0001-01-01') {
                    echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying all results starting from ".$starttime.".</span>";
                } else if ($endtime !== '2222-02-02') {
                    echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying all results from the beginning of time until ".$endtime.".</span>";
                }

            } else {
				$query_check_credentials = "SELECT * FROM purchase_orders WHERE deleted = 0 AND invoice_date >= '0000-00-00' AND invoice_date <= '2222-02-02' ORDER BY invoice_date DESC $limit";
				$query_num = "SELECT COUNT(*) numrows FROM purchase_orders WHERE deleted = 0 AND invoice_date >= '0000-00-00' AND invoice_date <= '2222-02-02'";
            }
            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
				echo display_pagination($dbc, $query_num, $pageNum, $rowsPerPage);
                echo "<table class='table table-bordered'>";
                echo "<tr class=''>
                <th>PO #</th>
                <th>Created Date</th>
                <th>Total Cost</th>
                <th>Total Paid</th>
                <th>Total Remaining</th>
				<th>View/Edit</th>
                ";
                echo "</tr>";
            } else{
                echo "<h2>No Record Found.</h2>";
            }
			$total_total_total_cost = 0;
			$total_total_paid = 0;
			$total_total_rem = 0;
            while($row = mysqli_fetch_array( $result ))
            {
				$gst = $row['gst'];
				$gst_paid = $row['gst_paid'];
				$gst_rem = $gst - $gst_paid;
				$pst = $row['pst'];
				$pst_paid = $row['pst_paid'];
				$pst_rem = $pst - $pst_paid;
				$delivery = $row['delivery'];
				$delivery_paid = $row['delivery_paid'];
				$delivery_rem = $delivery - $delivery_paid;
				$assembly = $row['assembly'];
				$assembly_paid = $row['assembly_paid'];
				$assembly_rem = $assembly - $assembly_paid;
                echo '<tr>';
                echo '<td data-title="PO #">'.$row['posid'].'</td>';
				echo '<td data-title="Created Date">'.$row['invoice_date'].'</td>';
                echo '<td data-title="Total Cost">';
					$total_total_cost = 0;
					$total_paid = 0;
					$query_check_credentiaxls = "SELECT * FROM purchase_orders_product WHERE posid = '".$row['posid']."'";
					$result_getter = mysqli_query($dbc, $query_check_credentiaxls);
					while($roxw = mysqli_fetch_array( $result_getter )) {
						if($roxw['quantity'] == NULL || $roxw['quantity'] == '') {
							$qty = 1;
						} else {
							$qty = $roxw['quantity'];
						}
						$total_cost = ($qty*$roxw['price']);
						$total_paid += $roxw['total_paid'];
						$total_total_cost += $total_cost;
					}
					$total_total_cost = $total_total_cost+$gst+$pst+$delivery+$assembly;
					$total_paid = $total_paid+$pst_paid+$gst_paid+$delivery_paid+$assembly_paid;
					$total_remaining = ($total_total_cost - $total_paid);
					$total_total_rem += $total_remaining;
					if($total_remaining > 0) {
						$total_remaining = '<span style="color:red; font-weight:bold;">$'.number_format($total_remaining, 2).'</span>';
					} else {
						$total_remaining = '<img src="../img/checkmark.png" width="25px" class="wiggle-me"> $'.number_format($total_remaining, 2);
					}
					echo '$'.number_format($total_total_cost, 2).'</td>';				
				echo '<td data-title="Paid">$'.number_format($total_paid, 2).'</td>';
				echo '<td data-title="Remaining">'.$total_remaining.'</td>';
				echo '<td data-title="View/Edit"><a href="../Purchase Order/receive_pay.php?posid='.$row['posid'].'&type=pay">Go to Accounts Payable</a></td>';
                echo "</tr>";
				$total_total_paid += $total_paid;
				$total_total_total_cost += $total_total_cost;
            }
            echo '</table>';
			if($num_rows > 0) {
				echo '<h3>Total Summary</h3>';
				echo '<table class="table table-bordered"><tr><th>Total Orders</th><th>Total Cost</th><th>Total Paid</th><th>Total Remaining</th></tr>';
				echo '<tr><td>'.$num_rows.'</td><td>$'.number_format($total_total_total_cost, 2).'</td><td>$'.number_format($total_total_paid, 2).'</td><td>$'.number_format($total_total_rem, 2).'</td></tr>';
				echo '</table>';
				echo display_pagination($dbc, $query_num, $pageNum, $rowsPerPage);
			}
        }
            ?>
			</div>