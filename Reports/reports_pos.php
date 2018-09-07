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
<script type="text/javascript">
$(document).on('change', 'select[name="choose_table"]', function() { location = this.value; });
</script>
        <form name="form_sites" method="post" action="" class="form-inline" role="form">
        <div style="background-color:rgba(142,142,142,0.50); border-radius:10px; border:1px solid white; padding:10px;" >
            <h2><?php
                if($_GET['table'] == 'balancesheet') {
                    echo "Balance Sheet";
                }
                if($_GET['table'] == 'productmovement') {
                    echo "Product Movement Summary";
                }
                if($_GET['table'] == 'sales') {
                    echo "Sales"; }
                ?></h2>

			<!-- start time -->

			<div class="form-group col-sm-5">
				<label for="search_email" class="col-sm-4">Report Type:</label>
				<div class="col-sm-8">
					<select name="choose_table" id="dynamic_select" class="chosen-select-deselect form-control" data-placeholder="Choose a Report">
						<option <?php if($_GET['table'] == 'productmovement') { echo "selected='selected'"; } ?> value="<?php echo $actual_link; ?>?type=operations&table=productmovement&report=<?= $_GET['report'] ?>">Product Movement Summary</option>
						<option <?php if($_GET['table'] == 'sales') { echo "selected='selected'"; } ?> value="<?php echo $actual_link; ?>?type=operations&table=sales&report=<?= $_GET['report'] ?>">Sales</option>
						<!--<option <?php //if($_GET['table'] == 'pos_excempt') { echo "selected='selected'"; } ?> value="<?php //echo $actual_link; ?>?table=pos_excempt">POS Excempt Invoices</option>-->
					</select>
				</div></div>

            <div class="form-group col-sm-5">
				<label for="site_name" class="col-sm-4 control-label">From:</label>
				<div class="col-sm-8">
					<input name="starttime" type="text"  class="datepicker form-control" value="<?php echo $starttime; ?>"></p>
				</div>
            </div>

              <!-- end time -->
             <div class="form-group col-sm-5 until">
            <label for="site_name" class="col-sm-4 control-label">Until:</label>
            <div class="col-sm-8">
                <input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></p>
            </div></div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
            <button type="submit" name="display_all_email" value="Display All" class="btn brand-btn mobile-block">Display All</button>
              			<div class="clearfix"></div>
					</div>
		</form>

        <div class="no-more-tables">

        <?php

        if ($_GET['table'] == 'productmovement' || !isset($_GET['table'])) {

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

            if($endtime !== '2222-02-02' || $starttime !== '0001-01-01') {
                //$query_check_credentials = "SELECT invoice_date, inventoryid, SUM(amount_out), SUM(amount_in) FROM report_product_movement WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND deleted = 0 AND ( inventoryid IN(SELECT inventoryid FROM inventory WHERE deleted = 0 AND (category LIKE '%" . $email . "%' OR name LIKE '%" . $email . "%'))) GROUP BY inventoryid";
				$query_check_credentials = "SELECT p.posid AS posid, p.invoice_date, pp.inventoryid AS inventoryid, SUM(pp.quantity) AS quantity FROM point_of_sell p, point_of_sell_product pp WHERE (p.deleted = 0 AND pp.posid=p.posid) AND (p.invoice_date >= '".$starttime."' AND p.invoice_date <= '".$endtime."') GROUP BY pp.inventoryid ORDER BY p.invoice_date";

                // Display what times they have set
                if($starttime !== '0001-01-01' && $endtime !== '2222-02-02') {
                echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying results from ".$starttime." until ".$endtime.".</span><br><br>";
                } else if ($starttime !== '0001-01-01') {
                    echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying all results starting from ".$starttime.".</span>";
                } else if ($endtime !== '2222-02-02') {
                    echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying all results from the beginning of time until ".$endtime.".</span>";
                }

            } else {
				$query_check_credentials = "SELECT p.posid AS posid, p.invoice_date, pp.inventoryid AS inventoryid, SUM(pp.quantity) AS quantity FROM point_of_sell p, point_of_sell_product pp WHERE (p.deleted = 0 AND pp.posid=p.posid) AND (p.invoice_date >= '0000-00-00' AND p.invoice_date <= '2222-02-02') GROUP BY pp.inventoryid ORDER BY p.invoice_date";
                //$query_check_credentials = "SELECT invoice_date, inventoryid, SUM(quantity) FROM point_of_sell_product WHERE (invoice_date >= '0000-00-00' AND invoice_date <= '2222-02-02') AND deleted = 0 GROUP BY inventoryid";
            }
            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                echo "<table class='table table-bordered'>";
                echo "<tr class=''>
                <th>Category</th>
                <th>Name</th>
                <th>Amount Sold</th>
                <th>Current Inventory</th>
                <th>Stock on Hand</th>
                ";
                echo "</tr>";
            } else{
                echo "<h2>No Record Found.</h2>";
            }

            while($row = mysqli_fetch_array( $result ))
            {
				if($row['inventoryid'] !== '' && $row['inventoryid'] !== NULL && $row['quantity'] > 0) {
                echo '<tr>';
                echo '<td data-title="Category">
                ';
                $query = mysqli_query($dbc,"SELECT category, name FROM inventory WHERE inventoryid = '".$row['inventoryid']."'");
				
                while($cat_and_name = mysqli_fetch_array($query)) {
                    echo $cat_and_name['category'];
                    ?></td>
					<td data-title="Name"><?php
                    echo $cat_and_name['name'].'</td>';
                }
                ?></td><?php
                echo '<td data-title="Amount Out">'.$row['quantity'].'</td>';

                $query = mysqli_query($dbc,"SELECT quantity, final_retail_price FROM inventory WHERE inventoryid = '".$row['inventoryid']."'");
                while($qty_and_final_rprice = mysqli_fetch_array($query)) {
                    echo '<td data-title="Current Inventory">'.$qty_and_final_rprice['quantity'].'</td>';

                    echo '<td data-title="Stock on Hand">$';
                    $stock_on_hand = $qty_and_final_rprice['quantity'] * $qty_and_final_rprice['final_retail_price'];
                    echo $stock_on_hand;
                echo'</td>';
                }

                echo "</tr>";
				}

            }

            echo '</table>';
        }

		if ($_GET['table'] == 'sales') {

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

            if($endtime !== '2222-02-02' || $starttime !== '0001-01-01') {
             
				$mainx = "SELECT p.posid AS posid, p.total_before_tax AS total_before_tax, p.gst AS gst, p.pst AS pst, p.invoice_date, pp.inventoryid AS inventoryid, pp.quantity AS quantity, pp.type_category AS type_category, pp.posproductid AS pposid, pp.price AS price FROM point_of_sell p, point_of_sell_product pp WHERE (p.deleted = 0 AND pp.posid=p.posid) AND (p.invoice_date >= '$starttime' AND p.invoice_date <= '$endtime') ORDER BY pp.type_category, pp.inventoryid, pp.posid";
				$total_shipping = "SELECT delivery, assembly FROM point_of_sell WHERE deleted = 0 AND invoice_date >= '$starttime' AND invoice_date <= '$endtime'";

				// Display what times they have set
				if($starttime !== '0001-01-01' && $endtime !== '2222-02-02') {
				echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying results from ".$starttime." until ".$endtime.".</span><br><br>";
				} else if ($starttime !== '0001-01-01') {
					echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying all results starting from ".$starttime.".</span>";
				} else if ($endtime !== '2222-02-02') {
					echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying all results from the beginning of time until ".$endtime.".</span>";
				}

            } else {
				$mainx = "SELECT p.posid AS posid, p.total_before_tax AS total_before_tax, p.gst AS gst, p.pst AS pst, p.invoice_date, pp.inventoryid AS inventoryid, pp.quantity AS quantity, pp.type_category AS type_category, pp.posproductid AS pposid, pp.price AS price FROM point_of_sell p, point_of_sell_product pp WHERE (p.deleted = 0 AND pp.posid=p.posid) AND (p.invoice_date >= '0000-00-00' AND p.invoice_date <= '2222-02-02') ORDER BY pp.type_category, pp.inventoryid, pp.posid";
				$total_shipping = "SELECT delivery, assembly FROM point_of_sell WHERE deleted = 0 AND invoice_date >= '0000-00-00' AND invoice_date <= '2222-02-02'";
            }
            $resultxx = mysqli_query($dbc, $mainx);
			$resultxxtwo = mysqli_query($dbc, $total_shipping);
			$i = 0;
			$gross_total = 0;
			$category_pst = 0;
			$category_gst = 0;
			$net_total = 0;
			$gross_total_total = 0;
			$total_gst = 0;
			$total_pst = 0;
			$num_rowsa = mysqli_num_rows($resultxx);

            $result2 = mysqli_query($dbc, $mainx);

            $num_rows2 = mysqli_num_rows($resultxx);
            if($num_rows2 > 0) {
                echo "<table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>
				<th>Type</th>
                <th>Category</th>
                <th>Gross Sales</th>
				<th>GST</th>
                <th>PST</th>
                <th>Net Sales<br><em>(discount not calculated)</em></th>
                ";
                echo "</tr>";
            } else{
            	//echo "<h2>No Record Found.</h2>";
            }
			$n = 0;
			if($num_rows2 > 0) {
				while($rowxx = mysqli_fetch_array( $resultxx ))
				{
					
					$type_category = $rowxx['type_category'];
					if($type_category == 'inventory' || $type_category == '' || $type_category == NULL) {
						$type_category = 'Inventory';
						if($type_category == NULL) {
							$type_category = NULL;
						}
						$table = 'inventory';
						$id = 'inventoryid';
					}
					if($type_category == 'service') {
						$type_category = 'Service';
						$table = 'services';
						$id = 'serviceid';
					}
					if($type_category == 'product') {
						$type_category = 'Product';
						$table = 'products';
						$id = 'productid';
					}
					if($type_category == 'vpl') {
						$type_category = 'Vendor Price List';
						$table = 'vendor_price_list';
						$id = 'inventoryid';
					}
					if($type_category == 'misc product') {
						$type_category = 'Miscellaneous Product';
					}
					
					if($rowxx['inventoryid'] !== '' && $rowxx['inventoryid'] !== NULL) {
						$query = mysqli_query($dbc,"SELECT category, name FROM ".$table." WHERE ".$id." = '".$rowxx['inventoryid']."'");
						while($cat_and_name = mysqli_fetch_array($query)) {
							$category = $cat_and_name['category'];
							if($category == '' || $category == NULL) {
								$category = 'No category found'; 
							}
						}
					} else {
						$category = 'Miscellaneous Product (No Category)';
					}
					
					$price = $rowxx['price'];
					$quantity = $rowxx['quantity'];
					// GET GST AND PST OF ITEM
					
					$total_before_tax = $rowxx['total_before_tax'];
					$gst = $rowxx['gst'];
					$pst = $rowxx['pst'];
					$gst_rate = $gst/$total_before_tax;
					$pst_rate = $pst/$total_before_tax;
					
					
					if($rowxx['inventoryid'] == '' || $rowxx['inventoryid'] == NULL) {
						$quantity = 1;
						$num_of_inv = mysqli_num_rows(mysqli_query($dbc, "SELECT p.*, pp.* FROM point_of_sell p, point_of_sell_product pp WHERE (p.deleted = 0 AND pp.posid=p.posid) AND (pp.inventoryid = '' OR pp.inventoryid IS NULL) AND type_category = '".$rowxx['type_category']."'"));
					} else {
						if($rowxx['type_category'] !== NULL && $rowxx['type_category'] !== '') {
							$num_of_inv = mysqli_num_rows(mysqli_query($dbc, "SELECT p.*, pp.* FROM point_of_sell_product pp, point_of_sell p WHERE (p.deleted = 0 AND pp.posid=p.posid) AND (pp.inventoryid = '".$rowxx['inventoryid']."' AND type_category = '".$rowxx['type_category']."')"));
						} else {
							$num_of_inv = mysqli_num_rows(mysqli_query($dbc, "SELECT p.*, pp.* FROM point_of_sell_product pp, point_of_sell p WHERE (p.deleted = 0 AND pp.posid=p.posid) AND (pp.inventoryid = '".$rowxx['inventoryid']."' AND type_category is null)"));
						}
					}
					if($num_of_inv_total > 0) {
						$num_of_inv = $num_of_inv_total;
					}
					$i++;
					$n++;
					$item_pst = $pst_rate*$price*$quantity;
					$item_gst = $gst_rate*$price*$quantity;
					$gross_total += $price*$quantity;
					$category_pst += $item_pst;
					$category_gst += $item_gst;
					$net_total += (($price*$quantity)-($item_gst+$item_pst));
					// UNCOMMENTING THIS MAY LEAD TO A BETTER UNDERSTANDING OF HOW THIS CODE WORKS (Sorry, I know the code is quite long!) // echo 'Q: '. $quantity.' | P: '.$price.' | '.$gross_total.' T < | POSID > '.$rowxx['posid'].' | PPOSID: <span style="color:blue;">'.$rowxx['pposid'].'</span> | Inv ID: '.$rowxx['inventoryid'].' | Num of Inv: '.$num_of_inv.' | I: '. $i.' | Cat: '.$category.' | GST RATE: '.$gst_rate.' | PST: '.$pst_rate.' | <br>';
					if($i == $num_of_inv) {
						$o = 0;
						$resultxz = mysqli_query($dbc, $mainx);
						while($rowxz = mysqli_fetch_array( $resultxz ))
						{
							$o++;
							
							if($o == ($n+1)) {
								$type_categoryz = $rowxz['type_category'];
								if($type_categoryz == 'inventory') {
									$type_categoryz = 'Inventory';
									$tablez = 'inventory';
									$idz = 'inventoryid';
								}
								if($type_categoryz == 'service') {
									$type_categoryz = 'Service';
									$tablez = 'services';
									$idz = 'serviceid';
								}
								if($type_categoryz == 'product') {
									$type_categoryz = 'Product';
									$tablez = 'products';
									$idz = 'productid';
								}
								if($type_categoryz == 'vpl') {
									$type_categoryz = 'Vendor Price List';
									$tablez = 'vendor_price_list';
									$idz = 'inventoryid';
								}
								if($type_categoryz == 'misc product') {
									$type_categoryz = 'Miscellaneous Product';
								}
								$category2 = '';
								if($rowxz['inventoryid'] !== '' && $rowxz['inventoryid'] !== NULL) {
									$query32 = mysqli_query($dbc,"SELECT category, name FROM ".$tablez." WHERE ".$idz." = '".$rowxz['inventoryid']."'");
									
									while($cat_and_name = mysqli_fetch_array($query32)) {
										$category2 = $cat_and_name['category'];
										if($category2 == '' || $category2 == NULL) {
											$category2 = 'No category found'; 
										}
									}
								}
								
								if($category2 == $category && $type_categoryz == $type_category) {
									$num_of_inv_next_row = mysqli_num_rows(mysqli_query($dbc, "SELECT p.*, pp.* FROM point_of_sell_product pp, point_of_sell p WHERE (p.deleted = 0 AND pp.posid=p.posid) AND (pp.inventoryid = '".$rowxz['inventoryid']."' AND type_category = '".$rowxz['type_category']."')"));
									$num_of_inv_total = $num_of_inv + $num_of_inv_next_row;
								} else {
									$num_of_inv_total = 0;
									$i = 0;
									$table='<tr>';
										$table .= '<td data-title="Type">'.$type_category.'</td>';
										$table .= '<td data-title="Category">'.$category.'</td>';
										$table .= '<td data-title="Gross Sales">$'.number_format($gross_total, 2).'</td>';
										$table .= '<td data-title="GST">$'.number_format($category_gst, 2).'</td>';
										$table .= '<td data-title="PST">$'.number_format($category_pst, 2).'</td>';
										$table .= '<td data-title="Net Sales">$'.number_format($net_total, 2).'</td>';
									$table .= '</tr>';
									echo $table;
									
									$gross_total_total += $gross_total;
									$total_gst += $category_gst;
									$total_pst += $category_pst;
									
									$gross_total = 0;
									$category_pst = 0;
									$category_gst = 0;
									$net_total = 0;
								}
							} 
						}
					}
				}
			}
            echo '</table>';
			
            $num_rows1 = mysqli_num_rows($resultxx);
            if($num_rows1 > 0) {
                echo "<table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>
                <th>Total Gross Sales</th>
                <th>Total GST</th>
                <th>Total PST</th>
                <th>Total Net Sales<br><em>(discount not calculated)</em></th>
                ";
                echo "</tr>";
            } else{
            	echo "<h2>No Record Found.</h2>";
            }
			 if($num_rows1 > 0) {
				echo '<tr>';
                echo '<td data-title="Total Gross Sales">$'.number_format($gross_total_total, 2).'</td>';
				echo '<td data-title="Total GST">$'.number_format($total_gst, 2).'</td>';
                echo '<td data-title="Total PST">$'.number_format($total_pst, 2).'</td>';
                echo '<td data-title="Total Net Sales">$'.number_format(($gross_total_total-($total_gst+$total_pst)), 2).'</td>';
            	echo "</tr>";
                //$total_final_discount = round($row1['TOTAL_DISCOUNT'], 2);
			}

            echo '</table>';
			
			$num_rows23 = mysqli_num_rows($resultxxtwo);
            if($num_rows23 > 0) {
                echo "<table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>
                <th>Total Shipping/Delivery</th>
                <th>Total Assembly</th>
                ";
                echo "</tr>";
            } else{
            	echo "<h2>No Record Found.</h2>";
            }
			 if($num_rows23 > 0) {
				 $assembly = 0;
				 $delivery = 0;
				 while($row = mysqli_fetch_array( $resultxxtwo ))
				{
					if(is_numeric($row['assembly'])) {
						$assembly += $row['assembly'];
					}
					if(is_numeric($row['delivery'])) {
						$delivery += $row['delivery'];
					}
				}
				echo '<tr>';
					echo '<td data-title="Total Shipping/Delivery">$'.number_format($delivery, 2).'</td>';
					echo '<td data-title="Total Assembly">$'.number_format($assembly, 2).'</td>';
				echo "</tr>";
			}

            echo '</table>';

		}

		if ($_GET['table'] == 'pos_excempt') {
			// This functionality has been disabled for the time being. 
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

            if($endtime !== '2222-02-02' || $starttime !== '0001-01-01') {
                $main1 = "SELECT * FROM report_pos_exempt WHERE (invoice_date >= '$starttime' AND invoice_date <= '$endtime')";

				// Display what times they have set
				if($starttime !== '0001-01-01' && $endtime !== '2222-02-02') {
				echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying results from ".$starttime." until ".$endtime.".</span><br><br>";
				} else if ($starttime !== '0001-01-01') {
					echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying all results starting from ".$starttime.".</span>";
				} else if ($endtime !== '2222-02-02') {
					echo "<span style='position:relative;margin-bottom:5px; top:-5px;'>Displaying all results from the beginning of time until ".$endtime.".</span>";
				}

            } else {
                $main1 = "SELECT * FROM report_pos_exempt WHERE (invoice_date >= '0000-00-00' AND invoice_date <= '2222-02-02')";
            }

            $result1 = mysqli_query($dbc, $main1);

            $num_rows1 = mysqli_num_rows($result1);
            if($num_rows1 > 0) {
                echo "<table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>
                <th>Invoice #</th>
                <th>Customer</th>
                <th>View</th>
                ";
                echo "</tr>";
            } else{
            	echo "<h2>No Record Found.</h2>";
            }

			while($row1 = mysqli_fetch_array( $result1 ))
            {
                $customer = $row1['customer'];
                $c1 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT client_name FROM clients WHERE clientid='$customer'"));

				echo '<tr>';
                echo '<td data-title="Invoice #">'.$row1['invoiceid'].'</td>';
                echo '<td data-title="Customer">'.$c1['client_name'].'</td>';
                echo '<td data-title="View"><a target="_blank" href="seaPDF/Quote/invoice_'.$row1['invoiceid'].'.pdf">PDF <img src="img/pdf.png" title="PDF"></a></td>';
			}

            echo '</table>';
        }
            ?>
	</div>