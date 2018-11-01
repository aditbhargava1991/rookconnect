<!-- Sales Lead Preview --><?php
$soid  = preg_replace('/[^0-9]/', '', $_GET['id']);
$order = mysqli_query($dbc, "SELECT `so`.*, `c`.`office_phone`, `c`.`email_address` FROM `sales_order` AS `so` LEFT JOIN `contacts` AS `c` ON (`c`.`contactid`=`so`.`contactid`) WHERE `so`.`posid`='{$soid}'");
$order_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `sales_order` WHERE `posid` = '$soid'"))['name'];
$pdf_file = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_pdf` WHERE `type` = 'so' AND `soid` = '$soid'"))['file_name'];
if(!empty($_GET['sotid'])) {
    $soid = $_GET['sotid'];
    $order = mysqli_query($dbc, "SELECT `so`.*, `c`.`office_phone`, `c`.`email_address` FROM `sales_order_temp` AS `so` LEFT JOIN `contacts` AS `c` ON (`c`.`contactid`=`so`.`customerid`) WHERE `so`.`sotid`='{$soid}'");
    $order_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `sales_order_temp` WHERE `sotid` = '$soid'"))['name'];
    $so_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `sales_order_type` FROM `sales_order_temp` WHERE `sotid` = '$soid'"))['sales_order_type'];
    $pdf_file = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_pdf` WHERE `type` = 'sot' AND `soid` = '$soid'"))['file_name'];
    $cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts` WHERE `sales_order_type` = '$so_type'"),MYSQLI_ASSOC);
}
?>
<div class="main-screen standard-body gap-bottom" style="overflow-y:auto;">
    <div class="standard-body-title pad-left">
        <h3><?= !empty($order_name) ? $order_name : SALES_ORDER_NOUN.' '.$soid ?>
            <?php if(!empty($_GET['sotid'])) { ?>
                <a href="order.php?p=details&sotid=<?= $soid ?>" class="btn brand-btn pull-right">Edit</a>
                <?php if(!empty($cat_config)) { ?>
                    <a href="order_details.php?p=details&sotid=<?= $soid ?>" class="btn brand-btn pull-right">Order Details</a>
                <?php } ?>
            <?php } ?>
        </h3>
    </div>

    <div class="standard-body-content"><?php

        if ( $order->num_rows > 0 ) {
            while ( $row=mysqli_fetch_assoc($order) ) {
                $row['contactid'] = !empty($row['customerid']) ? $row['customerid'] : $row['contactid'];
                $sotid = $row['sotid'];
                $projectid = $row['projectid']; ?>
                <div class="preview-block-details padded">
                    <div class="col-xs-12 col-sm-6">
                        <div class="row">
                            <div class="col-xs-4 col-sm-3 default-color">Customer:</div>
                            <div class="col-xs-8 col-sm-9"><?= !empty(get_client($dbc, $row['contactid'])) ? get_client($dbc, $row['contactid']) : get_contact($dbc, $row['contactid']); ?></div>
                        </div>
                        <div class="row pad-top-5">
                            <div class="col-xs-4 col-sm-3 default-color">Classification:</div>
                            <div class="col-xs-8 col-sm-9"><?= !empty($row['classification']) ? $row['classification'] : '-' ?></div>
                        </div>
                        <div class="row pad-top-5">
                            <div class="col-xs-4 col-sm-3 default-color">Contact:</div>
                            <div class="col-xs-8 col-sm-9"><?php
                                foreach (explode(',',$row['business_contact']) as $contact) {
                                    echo get_contact($dbc, $contact).'<br>';
                                }
                            ?></div>
                        </div>
                        <div class="row pad-top-5">
                            <div class="col-xs-4 col-sm-3 default-color">Office #:</div>
                            <div class="col-xs-8 col-sm-9"><?= (!empty($row['office_phone'])) ? decryptIt($row['office_phone']) : '-'; ?></div>
                        </div>
                        <div class="row pad-top-5">
                            <div class="col-xs-4 col-sm-3 default-color">Email:</div>
                            <div class="col-xs-8 col-sm-9"><?= (!empty($row['email_address'])) ? decryptIt($row['email_address']) : '-'; ?></div>
                        </div>
                        <div class="row pad-top-5">
                            <div class="col-xs-4 col-sm-3 default-color">Invoice:</div>
                            <div class="col-xs-8 col-sm-9"><?= (file_exists('download/invoice_'.$row['posid'].'.pdf') ? '<a href="download/invoice_'.$row['posid'].'.pdf" target="_blank">View PDF</a>' : '-') ?></div>
                        </div>
                        <div class="row pad-top-5">
                            <div class="col-xs-4 col-sm-3 default-color">PDF:</div>
                            <div class="col-xs-8 col-sm-9"><?= (file_get_contents(WEBSITE_URL.'/Sales Order/download/'.$pdf_file) && !empty($pdf_file) ? '<a href="'.WEBSITE_URL.'/Sales Order/download/'.$pdf_file.'" target="_blank">View PDF</a>' : '-') ?></div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <div class="row">
                            <div class="col-xs-4 default-color">Order Value($):</div>
                            <div class="col-xs-8"><?= !empty($_GET['sotid']) ? 'N/A' : $row['total_price']; ?></div>
                        </div>
                        <div class="row pad-top-5">
                            <div class="col-xs-4 default-color">Order Status:</div>
                            <div class="col-xs-8"><?= $row['status']; ?></div>
                        </div>
                        <div class="row pad-top-5">
                            <div class="col-xs-4 default-color">Next Action:</div>
                            <div class="col-xs-8"><?= $row['next_action']; ?></div>
                        </div>
                        <div class="row pad-top-5">
                            <div class="col-xs-4 default-color">Next Action Date:</div>
                            <div class="col-xs-8"><?= $row['next_action_date']; ?></div>
                        </div>
                        <div class="row pad-top-5">
                            <div class="col-xs-4 default-color">Comments:</div>
                            <div class="col-xs-8"><?= html_entity_decode($row['comment']); ?></div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                </div><!-- .preview-block-details --><?php
            } ?>

            <div class="preview-block-details padded">
                <div class="col-xs-12"><?php

                    //Inventory & Misc Products
                    if(!empty($_GET['sotid'])) {
                        $result   = mysqli_query($dbc, "SELECT `sopt`.*, `sopd`.`quantity` FROM `sales_order_product_temp` `sopt` LEFT JOIN `sales_order_product_details_temp` `sopd` ON `sopt`.`sotid` = `sopd`.`parentsotid` WHERE `sopt`.`parentsotid`='$soid' AND `sopt`.`item_type`='inventory' AND `sopt`.`item_type_id` IS NOT NULL AND `sopd`.`quantity` > 0");
                    } else {
                        $result   = mysqli_query($dbc, "SELECT * FROM `sales_order_product` WHERE `posid`='$soid' AND `type_category`='inventory' AND `inventoryid` IS NOT NULL");
                    }
                    $num_rows = mysqli_num_rows($result);

                    if ($num_rows > 0) {
                        $titler = 'Inventory';
                    }

                    if($num_rows > 0) {
                        $html .= '
                            <h5>'. $titler .'</h5>
                            <table border="1px" style="padding:3px; border:1px solid black;">
                                <tr>
                                    <th>Part#</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>';

                                while ( $row=mysqli_fetch_assoc($result) ) {
                                    $inventoryid = !empty($row['item_type_id']) ? $row['item_type_id'] : $row['inventoryid'];

                                    if ( $inventoryid != '' ) {
                                        $row['price'] = !empty($row['item_price']) ? $row['item_price'] : $row['price'];
                                        $price = $row['price'];
                                        $quantity = $row['quantity'];
                                        $amount = $price*$quantity;

                                        $html .= '<tr>';
                                            $html .= '<td>'. get_inventory($dbc, $inventoryid, 'part_no') .'</td>';
                                            $html .= '<td>'. get_inventory($dbc, $inventoryid, 'name') .'</td>';
                                            $html .= '<td>'. $row['quantity'] .'</td>';
                                            $html .= '<td>$'. $row['price'] .'</td>';
                                            $html .= '<td style="text-align:right;">$'. number_format($amount,2) .'</td>';
                                        $html .= '</tr>';
                                    }
                                }
                        $html .= '</table>';
                    }

                    //Products
                    if(!empty($_GET['sotid'])) {
                        $result   = mysqli_query($dbc, "SELECT `sopt`.*, `sopd`.`quantity` FROM `sales_order_product_temp` `sopt` LEFT JOIN `sales_order_product_details_temp` `sopd` ON `sopt`.`sotid` = `sopd`.`parentsotid` WHERE `sopt`.`parentsotid`='$soid' AND `sopt`.`item_type`='product' AND `sopt`.`item_type_id` IS NOT NULL AND `sopd`.`quantity` > 0");
                    } else {
                        $result    = mysqli_query($dbc, "SELECT * FROM `sales_order_product` WHERE `posid`='$soid' AND `type_category`='product' AND `inventoryid` IS NOT NULL");
                    }
                    $num_rows3 = mysqli_num_rows($result);

                    if($num_rows3 > 0) {
                        if($num_rows > 0 || $num_rows2 > 0) { $html .= '<br>'; }

                        $html .= '
                            <h5>Products</h5>
                            <table border="1px" style="padding:3px; border:1px solid black;">
                                <tr>
                                    <th>Category</th>
                                    <th>Heading</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>';

                            while ( $row=mysqli_fetch_array($result) ) {
                                $inventoryid = !empty($row['item_type_id']) ? $row['item_type_id'] : $row['inventoryid'];

                                if($inventoryid != '') {
                                    $row['price'] = !empty($row['item_price']) ? $row['item_price'] : $row['price'];
                                    $price = $row['price'];
                                    $quantity = $row['quantity'];
                                    $amount = $price*$quantity;
                                    $html .= '<tr>';
                                        $html .= '<td>'. get_products($dbc, $inventoryid, 'category') .'</td>';
                                        $html .= '<td>'. get_products($dbc, $inventoryid, 'heading') .'</td>';
                                        $html .= '<td>'. $row['quantity'] .'</td>';
                                        $html .= '<td>$'. $row['price'] .'</td>';
                                        $html .= '<td style="text-align:right;">$'. number_format($amount,2) .'</td>';
                                    $html .= '</tr>';
                                }
                            }
                        $html .= '</table>';
                    }

                    //Services
                    if(!empty($_GET['sotid'])) {
                        $result   = mysqli_query($dbc, "SELECT `sopt`.*, `sopd`.`quantity` FROM `sales_order_product_temp` `sopt` LEFT JOIN `sales_order_product_details_temp` `sopd` ON `sopt`.`sotid` = `sopd`.`parentsotid` WHERE `sopt`.`parentsotid`='$soid' AND `sopt`.`item_type`='services' AND `sopt`.`item_type_id` IS NOT NULL AND `sopd`.`quantity` > 0");
                    } else {
                        $result    = mysqli_query($dbc, "SELECT * FROM `sales_order_product` WHERE `posid`='$soid' AND `type_category`='services' AND `inventoryid` IS NOT NULL");
                    }
                    $num_rows4 = mysqli_num_rows($result);

                    if($num_rows4 > 0) {
                        if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0) { $html .= '<br>'; }

                        $html .= '
                            <h5>Services</h5>
                            <table border="1px" style="padding:3px; border:1px solid black;">
                                <tr>
                                    <th>Category</th>
                                    <th>Heading</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>';

                            while($row = mysqli_fetch_array( $result )) {
                                $inventoryid = !empty($row['item_type_id']) ? $row['item_type_id'] : $row['inventoryid'];

                                if($inventoryid != '') {
                                    $row['price'] = !empty($row['item_price']) ? $row['item_price'] : $row['price'];
                                    $price = $row['price'];
                                    $quantity = $row['quantity'];
                                    $amount = $price*$quantity;

                                    $html .= '<tr>';
                                        $html .= '<td>'.get_services($dbc, $inventoryid, 'category').'</td>';
                                        $html .= '<td>'.get_services($dbc, $inventoryid, 'heading').'</td>';
                                        $html .= '<td>'. $row['quantity'] .'</td>';
                                        $html .= '<td>$'. $row['price'] .'</td>';
                                        $html .= '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
                                    $html .= '</tr>';
                                }
                            }

                        $html .= '</table>';
                    }

                    //VPL
                    if(!empty($_GET['sotid'])) {
                        $result   = mysqli_query($dbc, "SELECT `sopt`.*, `sopd`.`quantity` FROM `sales_order_product_temp` `sopt` LEFT JOIN `sales_order_product_details_temp` `sopd` ON `sopt`.`sotid` = `sopd`.`parentsotid` WHERE `sopt`.`parentsotid`='$soid' AND `sopt`.`item_type`='vendor' AND `sopt`.`item_type_id` IS NOT NULL AND `sopd`.`quantity` > 0");
                    } else {
                        $result    = mysqli_query($dbc, "SELECT * FROM `sales_order_product` WHERE `posid`='$soid' AND `type_category`='vendor' AND `inventoryid` IS NOT NULL");
                    }
                    $num_rows5 = mysqli_num_rows($result);

                    if($num_rows5 > 0) {
                        if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0) { $html .= '<br>'; }

                        $html .= '
                            <h5>Vendor Price List Items</h5>
                            <table border="1px" style="padding:3px; border:1px solid black;">
                                <tr>
                                    <th>Part#</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>';

                            while($row = mysqli_fetch_array( $result )) {
                                $inventoryid = !empty($row['item_type_id']) ? $row['item_type_id'] : $row['inventoryid'];

                                if($inventoryid != '') {
                                    $row['price'] = !empty($row['item_price']) ? $row['item_price'] : $row['price'];
                                    $price = $row['price'];
                                    $quantity = $row['quantity'];
                                    $amount = $price*$quantity;

                                    $html .= '<tr>';
                                        $html .= '<td>'. get_vpl($dbc, $inventoryid, 'part_no') .'</td>';
                                        $html .= '<td>'. get_vpl($dbc, $inventoryid, 'name') .'</td>';
                                        $html .= '<td>'. $row['quantity'] .'</td>';
                                        $html .= '<td>$'. $row['price'] .'</td>';
                                        $html .= '<td style="text-align:right;">$'. number_format($amount,2) .'</td>';
                                    $html .= '</tr>';
                                }
                            }

                        $html .= '</table>';
                    }

                    //Labour
                    if(!empty($_GET['sotid'])) {
                        $result   = mysqli_query($dbc, "SELECT `sopt`.*, `sopd`.`quantity` FROM `sales_order_product_temp` `sopt` LEFT JOIN `sales_order_product_details_temp` `sopd` ON `sopt`.`sotid` = `sopd`.`parentsotid` WHERE `sopt`.`parentsotid`='$soid' AND `sopt`.`item_type`='labour' AND `sopt`.`item_type_id` IS NOT NULL AND `sopd`.`quantity` > 0");
                    } else {
                        $result    = mysqli_query($dbc, "SELECT * FROM `sales_order_product` WHERE `posid`='$soid' AND `type_category`='labour' AND `inventoryid` IS NOT NULL");
                    }
                    $num_rows6 = mysqli_num_rows($result);

                    if($num_rows6 > 0) {
                        if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0 || $num_rows5 > 0) { $html .= '<br>'; }

                        $html .= '
                            <h5>Labour</h5>
                            <table border="1px" style="padding:3px; border:1px solid black;">
                                <tr>
                                    <th>Labour</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>';

                            while($row = mysqli_fetch_array( $result )) {
                                $inventoryid = !empty($row['item_type_id']) ? $row['item_type_id'] : $row['inventoryid'];

                                if($inventoryid != '') {
                                    $row['price'] = !empty($row['item_price']) ? $row['item_price'] : $row['price'];
                                    $price = $row['price'];
                                    $quantity = $row['quantity'];
                                    $amount = $price*$quantity;

                                    $html .= '<tr>';
                                        $html .= '<td>'. get_labour($dbc, $inventoryid, 'heading') .'</td>';
                                        $html .= '<td>'. $row['quantity'] .'</td>';
                                        $html .= '<td>$'. $row['price'] .'</td>';
                                        $html .= '<td style="text-align:right;">$'. number_format($amount,2) .'</td>';
                                    $html .= '</tr>';
                                }
                            }

                        $html .= '</table>';
                    }

                    //Equipment
                    if(!empty($_GET['sotid'])) {
                        $result   = mysqli_query($dbc, "SELECT `sopt`.*, `sopd`.`quantity` FROM `sales_order_product_temp` `sopt` LEFT JOIN `sales_order_product_details_temp` `sopd` ON `sopt`.`sotid` = `sopd`.`parentsotid` WHERE `sopt`.`parentsotid`='$soid' AND `sopt`.`item_type`='equipment' AND `sopt`.`item_type_id` IS NOT NULL AND `sopd`.`quantity` > 0");
                    } else {
                        $result    = mysqli_query($dbc, "SELECT * FROM `sales_order_product` WHERE `posid`='$soid' AND `type_category`='equipment' AND `inventoryid` IS NOT NULL");
                    }
                    $num_rows6 = mysqli_num_rows($result);

                    if($num_rows6 > 0) {
                        if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0 || $num_rows5 > 0) { $html .= '<br>'; }

                        $html .= '
                            <h5>Equipment</h5>
                            <table border="1px" style="padding:3px; border:1px solid black;">
                                <tr>
                                    <th>Equipment</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>';

                            while($row = mysqli_fetch_array( $result )) {
                                $inventoryid = !empty($row['item_type_id']) ? $row['item_type_id'] : $row['inventoryid'];

                                if($inventoryid != '') {
                                    $row['price'] = !empty($row['item_price']) ? $row['item_price'] : $row['price'];
                                    $price = $row['price'];
                                    $quantity = $row['quantity'];
                                    $amount = $price*$quantity;

                                    $html .= '<tr>';
                                        $html .= '<td>'. get_equipment_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$row['inventoryid']."'"))) .'</td>';
                                        $html .= '<td>'. $row['quantity'] .'</td>';
                                        $html .= '<td>$'. $row['price'] .'</td>';
                                        $html .= '<td style="text-align:right;">$'. number_format($amount,2) .'</td>';
                                    $html .= '</tr>';
                                }
                            }

                        $html .= '</table>';
                    }

                    $html .= '<br>';
                    echo $html; ?>
                </div>

                <script>
                function attach_to_project() {
                    var projectid = $('[name=attach_to_project]').val();
                    if(projectid > 0) {
                        window.location.href = '../Sales Order/convert_to_project.php?posid=<?= $soid ?>&projectid='+projectid;
                    } else {
                        if($('.select-project').is(':visible')) {
                            alert('Please Select a <?= PROJECT_NOUN ?>.');
                        } else {
                            $('.select-project').show();
                        }
                    }
                    $('#create_project').show();
                    $('.select-projecttype').hide();
                }
                function attach_to_projecttype() {
                    var projecttype = $('[name=attach_to_projecttype]').val();
                    if(projecttype != '' && projecttype != undefined) {
                        window.location.href = '../Sales Order/convert_to_project.php?posid=<?= $soid ?>&projecttype='+projecttype;
                    } else {
                        if($('.select-projecttype').is(':visible')) {
                            alert('Please Select a <?= PROJECT_NOUN ?> Type.');
                        } else {
                            $('.select-projecttype').show();
                        }
                    }
                    $('#attach_project').show();
                    $('.select-project').hide();
                }
                </script>
                <?php if(empty($_GET['sotid'])) { ?>
                    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
                        <?php if ( vuaed_visible_function ( $dbc, 'sales_order') == 1 ) {
                            if($projectid > 0) { ?>
                                <a href="../Project/projects.php?edit=<?= $projectid ?>" class="btn brand-btn"><?= PROJECT_NOUN.' #'.$projectid ?></a>
                            <?php } else { ?>
                                <a id="create_project" href="" onclick="attach_to_projecttype();return false;" class="btn brand-btn">Create <?= PROJECT_NOUN ?></a>
                                <a id="attach_project" href="" onclick="attach_to_project();return false;" class="btn brand-btn">Attach to <?= PROJECT_NOUN ?></a>
                            <?php }
                        } ?>
                        <?php if (!empty($sotid) && config_visible_function($dbc, 'sales_order') == 1) { ?>
                            <button type="submit" name="copy_order" class="btn brand-btn pull-right">Copy Order</button>
                        <?php } ?>
                    </form>
                <?php } ?>
                <?php if(!($projectid > 0)) { ?>
                    <div class="select-project" style="display:none;">
                        <select name="attach_to_project" data-placeholder="Select a <?= PROJECT_NOUN ?>" class="chosen-select-deselect"><option></option>
                            <?php $projects = mysqli_query($dbc, "SELECT `projectid`, `project_name` FROM `project` WHERE `deleted`=0");
                            while($project = mysqli_fetch_assoc($projects)) { ?>
                                <option value="<?= $project['projectid'] ?>"><?= get_project_label($dbc, $project) ?></option>
                            <?php } ?>
                        </select>
                        <button onclick="attach_to_project(); return false;" class="btn brand-btn pull-right">Confirm</button>
                        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                    </div>
                    <div class="select-projecttype" style="display:none;">
                        <select name="attach_to_projecttype" data-placeholder="Select a <?= PROJECT_NOUN ?> Type" class="chosen-select-deselect"><option></option>
                            <?php $project_tabs = get_config($dbc, 'project_tabs');
                            $project_tabs = explode(',',$project_tabs);
                            foreach($project_tabs as $item) {
                                $var_name = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
                                if($var_name == 'client' || check_subtab_persmission($dbc, 'project', ROLE, $var_name) == 1) {
                                    echo "<option value='$var_name'>$item</option>";
                                }
                            } ?>
                        </select>
                        <button onclick="attach_to_projecttype(); return false;" class="btn brand-btn pull-right">Confirm</button>
                        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                    </div>
                <?php } ?>
                <div class="clearfix"></div>
            </div><!-- .preview-block-details --><?php

        } else { ?>
            <div class="preview-block-details">No records found.</div><?php
        } ?>
    </div>
</div><!-- .main-screen-white -->