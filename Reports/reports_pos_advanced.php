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
                <div>
                    <h3 class="gap-left"><?php
                        if($_GET['table'] == 'balancesheet') {
                            echo "Balance Sheet";
                        } elseif($_GET['table'] == 'sales') {
                            echo "Sales";
                        } else {
                            echo "Product Movement Summary";
                        } ?>
                    </h3><br /><?php
                    
                    if (isset($_POST['search_email_submit'])) {
                        $starttime = $_POST['starttime'];
                        $endtime = $_POST['endtime'];
                    }
                    if (isset($_POST['display_all'])) {
                        $starttime = $_POST['starttime'];
                        $endtime = $_POST['endtime'];
                    }

                    if($starttime == 0000-00-00 || empty($starttime)) {
                        $starttime = date('Y-m-01');
                    }
                    if($endtime == 0000-00-00 || empty($endtime)) {
                        $endtime = date('Y-m-t');
                    } ?>

                    <div class="form-group col-sm-6">
                        <label for="search_email" class="col-sm-4">Report Type:</label>
                        <div class="col-sm-8">
                            <select name="choose_table" id="dynamic_select" class="chosen-select-deselect form-control" data-placeholder="Choose a Report">
                                <option <?php if($_GET['table'] == 'productmovement') { echo "selected='selected'"; } ?> value="<?php echo $actual_link; ?>?type=operations&table=productmovement&report=<?= $_GET['report'] ?>">Product Movement Summary</option>
                                <option <?php if($_GET['table'] == 'sales') { echo "selected='selected'"; } ?> value="<?php echo $actual_link; ?>?type=operations&table=sales&report=<?= $_GET['report'] ?>">Sales</option>
                                <!--<option <?php //if($_GET['table'] == 'pos_excempt') { echo "selected='selected'"; } ?> value="<?php //echo $actual_link; ?>?table=pos_excempt">POS Excempt Invoices</option>-->
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="site_name" class="col-sm-4 control-label">From:</label>
                        <div class="col-sm-8">
                            <input name="starttime" type="text"  class="datepicker form-control" value="<?= $starttime; ?>"></p>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 until">
                        <label for="site_name" class="col-sm-4 control-label">Until:</label>
                        <div class="col-sm-8">
                            <input name="endtime" type="text" class="datepicker form-control" value="<?= $endtime; ?>"></p>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 text-right">
                        <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
                        <button type="submit" name="display_all" value="Display All" class="btn brand-btn mobile-block">Display All</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>

            <div class="no-more-tables"><?php
                if ($_GET['table'] == 'productmovement' || !isset($_GET['table'])) {
                    if (isset($_POST['search_email_submit'])) {
                        $starttime = $_POST['starttime'];
                        $endtime = $_POST['endtime'];
                    }
                    if (isset($_POST['display_all'])) {
                        $starttime = $_POST['starttime'];
                        $endtime = $_POST['endtime'];
                    }

                    if($starttime == 0000-00-00 || empty($starttime)) {
                        $starttime = date('Y-m-01');
                    }
                    if($endtime == 0000-00-00 || empty($endtime)) {
                        $endtime = date('Y-m-t');
                    }

                    $query_check_credentials = "SELECT `i`.`category`, `i`.`name`, SUM(`il`.`quantity`) `quantity_sold`, `i`.`quantity`, `i`.`final_retail_price` FROM `invoice_lines` `il` LEFT JOIN `inventory` `i` ON (`il`.`item_id`=`i`.`inventoryid`) LEFT JOIN `invoice` `v` ON (`il`.`invoiceid`=`v`.`invoiceid`) WHERE `v`.`deleted`=0 AND `il`.`deleted`=0 AND (`v`.`invoice_date` BETWEEN '$starttime' AND '$endtime') GROUP BY `il`.`item_id` ORDER BY `i`.`category`, `v`.`invoice_date`";
                    $result = mysqli_query($dbc, $query_check_credentials);

                    $num_rows = mysqli_num_rows($result);
                    if($num_rows > 0) {
                        echo "<h4 class='double-gap-bottom'>Displaying results from ".$starttime." until ".$endtime."</h4>";
                        echo "<table class='table table-bordered'>";
                        echo "<tr class='hidden-xs hidden-sm'>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Amount Sold</th>
                            <th>Current Inventory</th>
                            <th>Stock on Hand (@ Final Retail Price)</th>
                        </tr>";
                    } else{
                        echo "<h4>No records found from ".$starttime." until ".$endtime."</h4>";
                    }

                    while($row = mysqli_fetch_array( $result )) { ?>
                        <tr>
                            <td data-title="Category"><?= $row['category'] ?></td>
                            <td data-title="Name"><?= $row['name'] ?></td>
                            <td data-title="Amount Sold" align="right"><?= number_format($row['quantity_sold'], 0) ?></td>
                            <td data-title="Current Inventory" align="right"><?= $row['quantity'] ?></td>
                            <td data-title="Stock on Hand" align="right">$<?= number_format($row['quantity'] * $row['final_retail_price'], 2) ?></td>
                        </tr><?php
                    }

                    echo '</table>';
                } //productmovement

                
                if ($_GET['table'] == 'sales') {
                    if (isset($_POST['search_email_submit'])) {
                        $starttime = $_POST['starttime'];
                        $endtime = $_POST['endtime'];
                    }
                    if (isset($_POST['display_all'])) {
                        $starttime = $_POST['starttime'];
                        $endtime = $_POST['endtime'];
                    }

                    if ( $starttime==0000-00-00 || empty($starttime) ) {
                        $starttime = date('Y-m-01');
                    }
                    if ( $endtime==0000-00-00 || empty($endtime) ) {
                        $endtime = date('Y-m-t');
                    }
                    
                    $query = "SELECT `il`.`category` `type`, `il`.`item_id`, SUM(`il`.`quantity` * `il`.`unit_price`) `gross_sales`, SUM(`il`.`gst`) `gst`, SUM(`il`.`pst`) `pst`, SUM(`v`.`delivery`) `delivery`, SUM(`v`.`assembly`) `assembly` FROM `invoice_lines` `il` LEFT JOIN `invoice` `v` ON (`il`.`invoiceid`=`v`.`invoiceid`) LEFT JOIN `inventory` `i` ON (`il`.`item_id`=`i`.`inventoryid`) WHERE `il`.`deleted`=0 AND `v`.`deleted`=0 AND (`v`.`invoice_date` BETWEEN '$starttime' AND '$endtime') GROUP BY `il`.`item_id` ORDER BY `il`.`category`, `il`.`item_id`";
                    $result = mysqli_query($dbc, $query);
                    
                    if($result->num_rows > 0) {
                        $total_gross_sales = 0;
                        $total_gst = 0;
                        $total_pst = 0;
                        $total_net_sales = 0;
                        $total_delivery = 0;
                        $total_assembly = 0;
                        
                        echo "<h4 class='double-gap-bottom'>Displaying results from ".$starttime." until ".$endtime."</h4>";
                        
                        echo '<table class="table table-bordered">';
                            echo '<tr class="hidden-xs hidden-sm">
                            <th>Type</th>
                            <th>Category</th>
                            <th>Gross Sales</th>
                            <th>GST</th>
                            <th>PST</th>
                            <th>Net Sales<br><em>(discount not calculated)</em></th>
                            </tr>';
                            
                            while ( $row=mysqli_fetch_assoc($result) ) {
                                $type = $row['type'];
                                if($type == 'inventory' || $type == '' || $type == NULL) {
                                    $type_category = 'Inventory';
                                    $table = 'inventory';
                                    $id = 'inventoryid';
                                }
                                if($type == 'service') {
                                    $type_category = 'Service';
                                    $table = 'services';
                                    $id = 'serviceid';
                                }
                                if($type == 'product') {
                                    $type_category = 'Product';
                                    $table = 'products';
                                    $id = 'productid';
                                }
                                if($type == 'vpl') {
                                    $type_category = 'Vendor Price List';
                                    $table = 'vendor_price_list';
                                    $id = 'inventoryid';
                                }
                                if($type == 'misc product') {
                                    $type_category = 'Miscellaneous Product';
                                }
                                
                                if( !empty($row['item_id']) ) {
                                    $query_cat = mysqli_query($dbc, "SELECT `category` FROM `".$table."` WHERE `".$id."`='".$row['item_id']."'");
                                    while($row_cat = mysqli_fetch_array($query_cat)) {
                                        $category = $row_cat['category'];
                                        if( empty($category) ) {
                                            $category = 'No category found'; 
                                        }
                                    }
                                } else {
                                    $category = 'Miscellaneous Product (No Category)';
                                } ?>
                                <tr>
                                    <td data-title="Type"><?= $type_category ?></td>
                                    <td data-title="Category"><?= $category ?></td>
                                    <td data-title="Gross Sales" align="right">$<?= number_format($row['gross_sales'], 2) ?></td>
                                    <td data-title="GST" align="right">$<?= number_format($row['gst'], 2) ?></td>
                                    <td data-title="PST" align="right">$<?= number_format($row['pst'], 2) ?></td>
                                    <td data-title="Net Sales" align="right">$<?= number_format($row['gross_sales'] - ($row['gst']+$row['pst']), 2) ?></td>
                                </tr><?php
                                
                                $total_gross_sales += $row['gross_sales'];
                                $total_gst += $row['gst'];
                                $total_pst += $row['pst'];
                                $total_net_sales += $row['gross_sales'] - $row['gst'] - $row['pst'];
                                $total_delivery =+ $row['delivery'];
                                $total_assembly += $row['assembly'];
                            }//while
                            
                        echo '</table>'; ?>
                        
                        <table class='table table-bordered'>
                            <tr class='hidden-xs hidden-sm'>
                                <th>Total Gross Sales</th>
                                <th>Total GST</th>
                                <th>Total PST</th>
                                <th>Total Net Sales<br><em>(discount not calculated)</em></th>
                            </tr>
                            <tr class='hidden-xs hidden-sm'>
                                <td data-title="Total Gross Sales"><?= number_format($total_gross_sales, 2) ?></td>
                                <td data-title="Total GST"><?= number_format($total_gst, 2) ?></td>
                                <td data-title="Total PST"><?= number_format($total_pst, 2) ?></td>
                                <td data-title="Total Net Sales"><?= number_format($total_net_sales, 2) ?></td>
                            </tr>
                        </table>
                        
                        <table class='table table-bordered'>
                            <tr class='hidden-xs hidden-sm'>
                                <th>Total Shipping/Delivery</th>
                                <th>Total Assembly</th>
                            </tr>
                            <tr class='hidden-xs hidden-sm'>
                                <td data-title="Total Shipping/Delivery"><?= number_format($total_delivery, 2) ?></td>
                                <td data-title="Total Assembly"><?= number_format($total_assembly, 2) ?></td>
                            </tr>
                        </table><?php
                    
                    } else{
                        echo "<h4>No Record Found.</h4>";
                    }

                }// if table==sales

                if ($_GET['table'] == 'pos_excempt') {
                    // This functionality has been disabled for the time being. 
                    if (isset($_POST['search_email_submit'])) {
                        $starttime = $_POST['starttime'];
                        $endtime = $_POST['endtime'];
                    }
                    if (isset($_POST['display_all'])) {
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
                } ?>
			</div><!-- .no-more-tables -->
