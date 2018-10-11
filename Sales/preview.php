<!-- Sales Lead Preview --><?php
$salesid = preg_replace('/[^0-9]/', '', $_GET['id']);
$lead    = mysqli_query($dbc, "SELECT * FROM `sales` WHERE `sales`.`salesid`='{$salesid}'");
$get_config_won_status = get_config($dbc, 'lead_status_won');
$get_config_lost_status = get_config($dbc, 'lead_status_lost');
$get_config_retained = get_config($dbc, 'lead_status_retained');
$lead_convert_to = get_config($dbc, 'lead_convert_to'); ?>
<div class="main-screen-white standard-body gap-bottom">
    <div class="standard-body-title pad-left" style="position:absolute;z-index:1;width:100%;">
        <h3><?= SALES_NOUN ?> #<?= $salesid ?>
            <div class="pull-right">
                <?php if(!empty($get_config_won_status) && !empty($lead_convert_to)) { ?>
                    <a href="convert_sales_lead.php?leadid=<?= $salesid ?>&won_lead=true" class="btn brand-btn">Mark as <?= $get_config_won_status ?></a>
                <?php } ?>
                <?php if(!empty($get_config_retained) && !empty($lead_convert_to)) { ?>
                    <a href="convert_sales_lead.php?leadid=<?= $salesid ?>&won_lead=keep" class="btn brand-btn">Add to  <?= $lead_convert_to ?></a>
                <?php } ?>
                <?php if(!empty($get_config_lost_status) && !empty($lead_convert_to)) { ?>
                    <a href="convert_sales_lead.php?leadid=<?= $salesid ?>&won_lead=lost" class="btn brand-btn">Mark as <?= $get_config_lost_status ?></a>
                <?php } ?>
	            <?php if(IFRAME_PAGE) { ?>
	                <div class="pull-right"><input type="button" onclick="javascript:window.top.location = '<?= WEBSITE_URL; ?>/Sales/sale.php?p=details&id=<?=$salesid;?>&a=staffinfo';" value="Open Full Window" class="btn brand-btn btn-small" /></div>
	            <?php } else { ?>
	                <div class="pull-right"><input type="button" onclick="javascript:window.location.replace('<?= WEBSITE_URL; ?>/Sales/sale.php?p=details&id=<?=$salesid;?>&a=staffinfo');" value="Edit" class="btn brand-btn btn-small" /></div>
                    <div class="pull-right"><?php include('quick_actions.php'); ?></div>
                    <span class="flag-label" data-colour="<?= $flag_colour ?>"><?= $flag_label ?></span>
	            <?php } ?>
            </div>
            <div class="clearfix"></div>
        </h3>
    </div>

    <div class="standard-body-content"><?php
        if ( $lead->num_rows > 0 ) {
            while ( $row=mysqli_fetch_assoc($lead) ) { ?>
                <div class="preview-block-details padded">
                    <div class="col-xs-12 col-md-7 preview-block-details-left">
                        <div class="row">
                            <div class="col-xs-4 default-color">Business:</div>
                            <div class="col-xs-8"><?php
                                $business_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `contacts` WHERE `contactid`={$row['businessid']}"))['name']; ?>
                                <a href="../Contacts/contacts_inbox.php?fields=all_fields&edit=<?= $row['businessid'] ?>" class="no-toggle" title="<?= get_contact($dbc, $row['businessid'], 'name_company') ?>" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><?= decryptIt($business_name) ?><img src="../img/person.PNG" class="inline-img"></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 default-color">Contact(s):</div>
                            <div class="col-xs-8"><?php
                                $contacts = '';
                                foreach ( explode(',', $row['contactid']) as $contact ) {
                                    if ( get_contact($dbc, $contact) != '-' ) {
                                        $contacts .= '<a href="../Contacts/contacts_inbox.php?fields=all_fields&edit='.$contact.'" class="no-toggle" title="'.get_contact($dbc, $contact).'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;">'.get_contact($dbc, $contact) . '<img src="../img/person.PNG" class="inline-img"></a><br />';
                                    }
                                }
                                echo rtrim($contacts, ', '); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 default-color">Contact #:</div>
                            <div class="col-xs-8"><?php
                                $phone_num_formats = array('(', ')', '-', ' ', '.');
                                if ( !empty($row['primary_number']) ) {
                                    echo '<a href="tel:'. str_replace($phone_num_formats, '', $row['primary_number']).'">'. $row['primary_number'] .'</a> (Primary)<br />';
                                }
                                foreach ( explode(',', $row['contactid']) as $contact ) {
                                    $result = mysqli_query($dbc, "SELECT `first_name`, `office_phone` FROM `contacts` WHERE `contactid`='$contact'");
                                    while( $row_phone=mysqli_fetch_assoc($result) ) {
                                        if ( !empty($row_phone['office_phone']) ) {
                                            echo '<a href="tel:'. str_replace($phone_num_formats, '', decryptIt($row_phone['office_phone'])).'">'. decryptIt($row_phone['office_phone']) .'</a> ('. decryptIt($row_phone['first_name']) .')<br />';
                                        }
                                    }
                                } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 default-color">Email:</div>
                            <div class="col-xs-8"><?php
                                if ( !empty($row['email_address']) ) {
                                    echo '<a href="mailto:'.$row['email_address'].'">'. $row['email_address'] . '</a> (Primary)<br />';
                                }
                                foreach ( explode(',', $row['contactid']) as $contact ) {
                                    $result = mysqli_query($dbc, "SELECT `first_name`, `email_address` FROM `contacts` WHERE `contactid`='$contact'");
                                    while( $row_email=mysqli_fetch_assoc($result) ) {
                                        if ( !empty($row_email['email_address']) ) {
                                            echo '<a href="mailto:'.decryptIt($row_email['email_address']).'">'. decryptIt($row_email['email_address']) .'</a> ('. decryptIt($row_email['first_name']) .')<br />';
                                        }
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-4 col-md-offset-1 preview-block-details-right">
                        <div class="row">
                            <div class="col-xs-6 default-color">Lead Value($):</div>
                            <div class="col-xs-6"><?= $row['lead_value']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 default-color">Lead Status:</div>
                            <div class="col-xs-6"><?= $row['status']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 default-color">Next Action:</div>
                            <div class="col-xs-6"><?= $row['next_action']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 default-color">Next Action Date:</div>
                            <div class="col-xs-6"><?= $row['new_reminder']; ?></div>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-xs-12 preview-block-details-full">
                        <div class="col-xs-12 col-sm-2 col-md-2"><b>Notes:</b></div>
                        <div class="col-xs-12 col-sm-10 col-md-10"><?php
                            $comments = mysqli_query($dbc, "SELECT * FROM `sales_notes` WHERE `salesid`='{$salesid}' ORDER BY `salesnoteid` DESC");
                            if ( $comments->num_rows > 0 ) {
                                $odd_even = 0;
                                while ( $row=mysqli_fetch_assoc($comments) ) {
                                    $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg';
                                    echo '<div class="'.$bg_class.'">'. html_entity_decode($row['comment']) .'</div>';
                                    $odd_even++;
                                }
                            } else {
                                echo '-';
                            } ?>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-xs-12 preview-block-details-full">
                        <div class="col-xs-12 col-sm-2 col-md-2"><b>Reminders:</b></div>
                        <div class="col-xs-12 col-sm-10 col-md-10"><?php
                            $comments = mysqli_query($dbc, "SELECT contactid, reminder_date FROM `reminders` WHERE src_table = 'sales' AND `src_tableid`='{$salesid}' AND reminder_date >= CURDATE() ORDER BY `reminder_date`");
                            if ( $comments->num_rows > 0 ) {
                                echo '<ul>';
                                while ( $row=mysqli_fetch_assoc($comments) ) {
                                    echo '<li>'. $row['reminder_date'] . ' : '.get_multiple_contact($dbc, $row['contactid']) .'</li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '-';
                            } ?>
                        </div>
                    </div>

                    <?php
                    if ( !empty($row['serviceid']) ) { ?>
                        <div class="col-sm-12 triple-gap-top">
                            <div class="no-more-tables">
                                <table>
                                    <tr class="hidden-xs hidden-sm"><?php
                                        if (strpos($value_config, ',Services Service Type,') !== false) { echo '<th>Service</th>'; }
                                        if (strpos($value_config, ',Services Category,') !== false) { echo '<th>Category</th>'; }
                                        if (strpos($value_config, ',Services Heading,') !== false) { echo '<th>Heading</th>'; }
                                        echo '<th>Unit Price($)</th>'; ?>
                                    </tr><?php

                                    foreach ( explode(',', $row['serviceid']) as $each_serviceid ) {
                                        $service_row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `serviceid`, `service_type`, `category`, `heading`, `client_price` FROM `services` WHERE `serviceid`='{$each_serviceid}'"));
                                        echo '<tr>';
                                            if (strpos($value_config, ',Services Service Type,') !== false) { echo '<td data-title="Service">'. $service_row['service_type'] .'</td>'; }
                                            if (strpos($value_config, ',Services Category,') !== false) { echo '<td data-title="Category">'. $service_row['category'] .'</td>'; }
                                            if (strpos($value_config, ',Services Heading,') !== false) { echo '<td data-title="Heading">'. $service_row['heading'] .'</td>'; }
                                            echo '<td data-title="Unit Price">'. (!empty($service_row['client_price']) ? $service_row['client_price'] : '0.00') .'</td>';
                                        echo '</tr>';
                                    } ?>
                                </table>
                           </div>
                        </div>
                        <div class="clearfix"></div><?php
                    }

                    if ( !empty($row['productid']) ) { ?>
                        <div class="col-sm-12 triple-gap-top">
                            <div class="no-more-tables">
                                <table>
                                    <tr class="hidden-xs hidden-sm"><?php
                                        if (strpos($value_config, ',Products Product Type,') !== false) { echo '<th>Product</th>'; }
                                        if (strpos($value_config, ',Products Category,') !== false) { echo '<th>Category</th>'; }
                                        if (strpos($value_config, ',Products Heading,') !== false) { echo '<th>Heading</th>'; }
                                        echo '<th>Unit Price($)</th>'; ?>
                                    </tr><?php

                                    foreach ( explode(',', $row['productid']) as $each_productid ) {
                                        $product_row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `productid`, `product_type`, `category`, `heading`, `client_price` FROM `products` WHERE `productid`='{$each_productid}'"));
                                        echo '<tr>';
                                            if (strpos($value_config, ',Products Product Type,') !== false) { echo '<td data-title="Product">'. $product_row['service_type'] .'</td>'; }
                                            if (strpos($value_config, ',Products Category,') !== false) { echo '<td data-title="Category">'. $product_row['category'] .'</td>'; }
                                            if (strpos($value_config, ',Products Heading,') !== false) { echo '<td data-title="Heading">'. $product_row['heading'] .'</td>'; }
                                            echo '<td data-title="Unit Price">'. (!empty($product_row['client_price']) ? $product_row['client_price'] : '0.00') .'</td>';
                                        echo '</tr>';
                                    } ?>
                                </table>
                           </div>
                        </div>
                        <div class="clearfix"></div><?php
                    } ?>


                    <div class="clearfix"></div>

                    <div class="col-xs-12 preview-block-details-full">
                        <div class="col-xs-12 col-sm-2 col-md-2"><b>Attached files:</b></div>
                        <div class="col-xs-12 col-sm-10 col-md-10"><?php
                            $documents = mysqli_query($dbc, "SELECT document, salesdocid FROM `sales_document` WHERE salesid = '{$salesid}' AND `document` !='' ORDER BY `salesdocid` DESC");
                            if ( $documents->num_rows > 0 ) {
                                echo '<ul>';
                                while ( $row=mysqli_fetch_assoc($documents) ) {
                                    $file = $_SERVER["DOCUMENT_ROOT"].'/Sales/download/'.$row['document'];
                                    if(file_exists($file)){
                                        echo '<li>'. $row['document'] .'<a href="'.WEBSITE_URL.'/Sales/download/'.$row['document'].'" target="_blank" class="text-blue"> - View</a></li>';
                                    }
                                }
                                echo '</ul>';
                            } else {
                                echo '-';
                            } ?>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <div class="col-xs-12 preview-block-details-full">
                        <div class="col-xs-12 col-sm-2 col-md-2"><b>Time tracking:</b></div>
                        <div class="col-xs-12 col-sm-10 col-md-10">
                            <div id="no-more-tables">
                                <table class="table table-bordered">
                                    <tr class="hidden-sm hidden-xs">
                                        <th>User</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                    </tr>
                                    <?php $total_time = 0;
                                    $time_tracked = $dbc->query("SELECT * FROM `time_cards` WHERE `deleted`=0 AND `salesid`='$salesid' AND `salesid` > 0");
                                    while($time_row = $time_tracked->fetch_assoc()) { ?>
                                        <tr>
                                            <td data-title="User"><?= get_contact($dbc, $time_row['staff']) ?></td>
                                            <td data-title="Date"><?= $time_row['date'] ?></td>
                                            <td data-title="Time"><?= time_decimal2time($time_row['total_hrs']) ?></td>
                                        </tr>
                                        <?php $total_time += $time_row['total_hrs'];
                                    } ?>
                                    <tr>
                                        <td colspan="2">Total</td>
                                        <td data-title="Total"><?= time_decimal2time($total_time) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- .preview-block-details --><?php
            }

        } else { ?>
            <div class="preview-block-details">No records found.</div><?php
        } ?>
    </div><!-- .preview-block-container -->
</div><!-- .main-screen-white -->