<?php
/*
Payment/Invoice Listing
*/
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
include_once('../tcpdf/tcpdf.php');

$purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
$purchaser_label = count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0];
$payer_config = explode(',',get_config($dbc, 'invoice_payer_contact'));
$payer_label = count($payer_config) > 1 ? 'Third Party' : $payer_config[0];

if (isset($_POST['printpdf'])) {
    include_once ('print_unpaid_invoice.php');
}
?>
<script type="text/javascript" src="../Invoice/invoice.js"></script>
<script type="text/javascript">
    function view_unpaid_third_party()
    {
        $('.view_unpaid_third_party').toggleClass('hidden');
    }
</script>
<div class="standard-body-title hide-titles-mob">
    <h3 class="pull-left">Unpaid Insurer Invoices</h3>
    <div class="pull-right">
        <img src="../img/icons/ROOK-3dot-icon.png" class="no-toggle cursor-hand offset-top-15 double-gap-right" title="" width="25" data-original-title="Show/Hide Unpaid Insurer Invoices" onclick="view_unpaid_third_party()"> </div>
    <div class="clearfix"></div>
</div>

<div class="standard-body-content padded-desktop ">
    <form name="invoice" method="post" action="" class="form-inline " role="form" style="overflow-x:visible;overflow-y:visible;">

    <div class="notice double-gap-bottom popover-examples view_unpaid_third_party hidden">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11"><span class="notice-name">NOTE:</span>
        Generate Unpaid <?= $payer_label ?> reports from here. Select the <?= $purchaser_label ?> name from the drop down menu, then select the <?= $payer_label ?> from the drop down menu to display all unpaid <?= $payer_label ?> invoices related to that <?= $purchaser_label ?> and <?= $payer_label ?>.</div>
        <div class="clearfix"></div>
    </div>

        <?php
        if(isset($_POST['search_user_submit'])) {
            $search_user = $_POST['search_user'];
        } else {
            $search_user = '';
        }
        if (isset($_POST['display_all_inventory'])) {
            $search_user = '';
        }
        ?>
        <div class="row view_unpaid_third_party hidden">
            <div class="col-xs-12">
                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-4"><label class="control-label"><?= $purchaser_label ?>:</label></div>
                    <div class="col-sm-8">
                        <select data-placeholder="Select a <?= $purchaser_label ?>" name="search_user" id="search_user" class="chosen-select-deselect form-control" width="380">
                            <option value=""></option>
                            <?php
                            $query = mysqli_query($dbc,"SELECT distinct(patientid) FROM invoice WHERE paid = 'Waiting on Insurer' AND final_price IS NOT NULL AND serviceid IS NOT NULL AND serviceid != ','");
                            while($row = mysqli_fetch_array($query)) { ?>
                                <option <?php if ($row['patientid'] == $search_user) { echo " selected"; } ?> value='<?php echo  $row['patientid']; ?>' ><?php echo get_contact($dbc, $row['patientid']); ?></option><?php
                            } ?>
                        </select>
                        <input type="hidden" name="patientpdf" value="<?php echo $search_user; ?>" />
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-4"><label class="control-label"><?= $payer_label ?>:</label></div>
                    <div class="col-sm-8">
                        <select data-placeholder="Select an <?= $payer_label ?>" id="insurerid" name="insurerid" class="chosen-select-deselect form-control" width="380">
                            <option value=""></option><?php
                            $insurerid = get_all_form_contact($dbc, $search_user, 'insurerid');
                            $parts = explode(',', $insurerid);
                            echo '<option value=""></option>';
                            foreach ($parts as $key) {
                                if($key != '') {
                                    echo "<option value='". $key."'>".get_all_form_contact($dbc, $key, 'name').'</option>';
                                }
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 text-right offset-top-5">
                <button type="submit" name="search_user_submit" id="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                <button type="submit" name="printpdf" id="printpdf" value="Print Report" class="btn brand-btn pull-right">Print</button>
            </div>
        </div>

        <?php
        if($search_user != '') {
            $query_check_credentials = "SELECT `invoice`.* FROM invoice LEFT JOIN `invoice` isrc ON `invoice`.`invoiceid_src`=isrc.`invoiceid` WHERE `invoice`.patientid='$search_user' AND `invoice`.paid IN ('Waiting on Insurer','No') AND `invoice`.final_price IS NOT NULL AND `invoice`.serviceid IS NOT NULL AND `invoice`.serviceid != ',' ORDER BY IFNULL(isrc.`service_date`,`invoice`.`service_date`), `invoice`.`invoiceid`";

            $result = mysqli_query($dbc, $query_check_credentials);

            $serviceid = 0;
            $j = 0;
            $final_total = 0;
            $gst_sub = 0;
            $gst_grand = 0;
            $grand_total = 0;
            $sub_total = 0;
            $invoice_service = ',';
            $total_qty = 0;
            while($row = mysqli_fetch_array($result))
            {
                if($serviceid != $row['serviceid']) {
                    if($j != 0) {
                        echo "<tr>";
                        echo "<td colspan='4'>Sub Total</td><td>".$total_qty."</td><td>$".number_format((float)$sub_total, 2, '.', '')."</td>";
                        echo "<td>$".number_format((float)$gst_sub, 2, '.', '')."</td><td>$".number_format((float)$final_total, 2, '.', '')."</td><td></td>";
                        echo "</tr>";
                        echo '</table>';

                        $final_total = 0;
                        $sub_total = 0;
                        $gst_sub = 0;
                        $total_qty = 0;
                    }
                    echo "<br><br><table border='1' cellpadding='10' class='table' style='margin:0px; width:100%;'>";
                    echo "<tr>"; ?>
                    <th style='width:4%;'>Invoice#</th>
                    <th style='width:10%;'>Date</th>
                    <th style='width:35%;'>Service : Fee</th>
                    <th style='width:20%;'>Professional</th>
                    <th style='width:3%;'>Qty</th>
                    <th style='width:7%;'>Sub Total</th>
                    <th style='width:5%;'>GST</th>
                    <th style='width:10%;'>Invoice Total</th>
                    <th style='width:5%;'><span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the checkbox at the end of each row to select the item(s) you wish to print, then click the Print button."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span></th>
                    </tr><?php
                    $serviceid = $row['serviceid'];
                }

                $patientid = $row['patientid'];

                echo '<tr>';
                echo '<td>#' . $row['invoiceid'] .($row['invoiceid_src'] > 0 ? '<br />'.$row['invoice_type'].' for Invoice #'.$row['invoiceid_src'] : ''). '</td>';
                echo '<td>' . $row['service_date'] . '</td>';

                echo '<td>';
                $parts = explode(',', $row['serviceid']);
                $parts_ff = explode(',', $row['fee']);
                $m = 0;
                $total_service = 0;
                foreach ($parts as $key) {
                    if($key != '') {
                        echo get_all_from_service($dbc, $key, 'service_code').' : '.get_all_from_service($dbc, $key, 'heading'). ' : $'.$parts_ff[$m].'<br>';
                        $gst_exempt = get_all_from_service($dbc, $key, 'gst_exempt');
                        $total_service += $parts_ff[$m];
                    }
                    $m++;
                }
                echo '</td>';

                echo '<td>' . get_contact($dbc, $row['therapistsid']).'('.get_all_form_contact($dbc, $row['therapistsid'], 'license').')'. '</td>';
                echo '<td>1</td>';

                echo '<td>$' . $total_service . '</td>';

                if($gst_exempt == 1) {
                    //echo '<td>$0</td>';
                } else {
                    //echo '<td>$'.number_format((float)($total_service*0.05), 2, '.', '').'</td>';
                    $gst_sub += ($total_service*0.05);
                }
                echo '<td></td>';

                /*if($row['final_price'] != $row['total_price']) {
                    echo '<td>$'.($row['final_price']-$row['total_price']).'</td>';
                } else {
                    echo '<td>-</td>';
                }
                */

//                 echo '<td>$' . ($row['final_price']) . '</td>';
                //echo '<td>$' . number_format((float)($total_service+$gst_sub), 2, '.', '') .
                //'</td>';
                echo '<td></td>';

                echo '<td><input type="checkbox" style="width: 20px; height: 20px;" value="'.$row['invoiceid'].'" name="invoice_checked[]"></td>';

                //$final_total += $row['final_price'];
                $final_total += ($total_service+$gst_sub);
                $sub_total += $total_service;
                //$gst_sub += ($row['final_price']-$row['total_price']);
                $grand_total += $row['final_price'];
                $total_qty++;

                echo "</tr>";
                $j++;
                $invoice_service .= $row['invoiceid'].',';
            }

            $query_check_credentials = "SELECT * FROM invoice WHERE patientid='$search_user' AND paid = 'Waiting on Insurer' AND final_price IS NOT NULL AND inventoryid IS NOT NULL AND inventoryid != ',' ORDER BY inventoryid";

            $result = mysqli_query($dbc, $query_check_credentials);

            $inventoryid = 0;
            $m = 0;
            while($row = mysqli_fetch_array($result))
            {
                if($inventoryid != $row['inventoryid']) {
                    if($m == 0) {
                        echo "<tr>";
                        echo "<td colspan='4'>Sub Total</td><td>".$total_qty."</td><td>$".number_format((float)$sub_total, 2, '.', '')."</td>";
                        echo "<td>$".number_format((float)$gst_sub, 2, '.', '')."</td><td>$".number_format((float)$final_total, 2, '.', '')."</td><td></td>";
                        echo "</tr>";
                        echo '</table>';
                    }
                    echo "<br><br><table border='1' cellpadding='10' class='table' style='margin:0px; width:100%;'>";
                    ?><tr>
                    <th style='width:4%;'>Invoice#</th>
                    <th style='width:10%;'>Date</th>
                    <th style='width:35%;'>Inventory : Sell Price</th>
                    <th style='width:20%;'>Inventory</th>
                    <th style='width:3%;'>Qty</th>
                    <th style='width:7%;'>Sub Total</th>
                    <th style='width:5%;'>GST</th>
                    <th style='width:10%;'>Invoice Total</th>
                    <th style='width:5%;'><span class="popover-examples list-inline">
                            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the checkbox at the end of each row to select the item(s) you wish to print, then click the Print button."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                        </span></th></th>
                    </tr><?php
                    $inventoryid = $row['inventoryid'];
                }

                $patientid = $row['patientid'];

                echo '<tr>';
                echo '<td>' . $row['invoiceid'] .($row['invoiceid_src'] > 0 ? ' ('.$row['invoice_type'].' for #'.$row['invoiceid_src'].')' : ''). '</td>';
                echo '<td>' . $row['service_date'] . '</td>';

                echo '<td>';
                $parts1 = explode(',', $row['inventoryid']);
                $invtype = explode(',', $row['invtype']);
                $sell_price = explode(',', $row['sell_price']);
                $k = 0;
                $total_inv = 0;
                foreach ($parts1 as $key1) {
                    if($key1 != '') {
                        echo $invtype[$k].' : '.get_all_from_inventory($dbc, $key1 , 'name'). ' : $'.$sell_price[$k].'<br>';

                        $total_inv += $sell_price[$k];
                    }
                    $k++;
                }

                echo '</td>';
                echo '<td>Nose Creek</td>';
                echo '<td>1</td>';
                echo '<td>$' . number_format((float)$total_inv, 2, '.', '') . '</td>';

                echo '<td>$'.number_format((float)($total_inv*0.05), 2, '.', '').'</td>';

                //echo '<td>$' . ($row['final_price']) . '</td>';
                echo '<td>$' . number_format((float)($total_inv + ($total_inv*0.05)), 2, '.', '') . '</td>';

                echo '<td>';
                if (strpos($invoice_service, ','.$row['invoiceid'].',') !== FALSE) {
                    echo '-';
                } else {
                    echo '<input type="checkbox" style="width: 20px; height: 20px;" value="'.$row['invoiceid'].'" name="invoice_checked[]">';
                    $grand_total += $row['final_price'];
                }
                echo '</td>';

                echo "</tr>";
                $m++;
            }
            if($m == 0) {
                echo "<tr>";
                echo "<td colspan='4'>Sub Total</td><td>".$total_qty."</td><td>$".number_format((float)$sub_total, 2, '.', '')."</td>";
                echo "<td>$".number_format((float)$gst_sub, 2, '.', '')."</td><td>$".number_format((float)$final_total, 2, '.', '')."</td><td></td>";
                echo "</tr>";
                echo '</table>';
            }
            echo "<table border='2' cellpadding='10' class='table' style='margin:0px;'>";
            echo "<tr>";
            echo "<th>Grand Total : $".number_format((float)$grand_total, 2, '.', '')."</th>";
            echo "</tr>";
            echo '</table>';
        }
        ?>
        
    </form>
</div>