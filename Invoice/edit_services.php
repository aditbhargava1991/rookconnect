<div id="inv_services">
    <h4 class="col-sm-12">Services
    <span class="popover-examples list-inline">
        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the service."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
    </span><img src="../img/icons/ROOK-add-icon.png" class="no-toggle cursor-hand adjust_block" title="Add Service" width="21" onclick="add_service_row();" /></h4>
    <?php if(in_array('service_cat',$field_config) && in_array('service_qty',$field_config)) {
        $col_cat = 3;
        $col_head = 3;
        $col_qty = 2;
        $col_fee = 2;
    } else if(in_array('service_cat',$field_config) && !in_array('service_qty',$field_config)) {
        $col_cat = 4;
        $col_head = 4;
        $col_qty = 0;
        $col_fee = 2;
    } else if(!in_array('service_cat',$field_config) && in_array('service_qty',$field_config)) {
        $col_cat = 0;
        $col_head = 6;
        $col_qty = 2;
        $col_fee = 2;
    } else if(in_array('unbilled_tickets',$field_config)) {
        $col_cat = 4;
        $col_head = 4;
        $col_qty = 0;
        $col_fee = 2;
    } else {
        $col_cat = 4;
        $col_head = 4;
        $col_qty = 0;
        $col_fee = 2;
    } ?>
    <div class="form-group service_option" <?= (in_array('services',$field_config) ? '' : 'style="display:none;"') ?>>
        <div class="col-sm-12">
            <div class="form-group clearfix hide-titles-mob service_labels" style="<?= ( empty(rtrim($serviceid, ',')) && $_GET['inv_mode'] == 'adjust' ) ? 'display:none;' : '' ?>">
                <label class="col-sm-<?= $col_cat > 0 ? $col_cat : '0 hidden' ?> text-center">Category</label>
                <label class="col-sm-<?= $col_head ?> text-center">Service Name</label>
                <label class="col-sm-<?= $col_qty > 0 ? $col_qty : '0 hidden' ?> text-center">Qty</label>
                <label class="col-sm-<?= $col_fee > 0 ? $col_fee : '0 hidden' ?> text-center">Fee</label>
            </div>

            <?php
            if(!empty($_GET['invoiceid'])) {

            if($serviceid != '') {
                $each_serviceid = explode(',',$serviceid);
                $each_serviceticketid = explode(',',$service_ticketid);
                $each_fee = explode(',',$fee);
                $total_count = mb_substr_count($serviceid,',');
                $id_loop = 500;

                for($client_loop=0; $client_loop<=$total_count; $client_loop++) {
                    if($each_serviceid[$client_loop] != '' && !($each_serviceticketid[$client_loop] > 0)) {
                        $serviceid = $each_serviceid[$client_loop];

                        $fee = $each_fee[$client_loop];
                        $qty = 1;
                        $service_line = $dbc->query("SELECT * FROM `invoice_lines` WHERE `invoiceid`='$invoiceid' AND `category`='service' AND `item_id`='$serviceid' AND `sub_total`='$fee'");
                        if($service_line->num_rows > 0) {
                            $service_line = $service_line->fetch_assoc();
                            $fee = $service_line['unit_price'] * $discount_percent;
                            $qty = round($service_line['quantity'],4);
                        } ?>

                    <div class="form-group clearfix">
                        <div class="col-sm-<?= $col_cat > 0 ? $col_cat : '0 hidden' ?>"><label class="show-on-mob">Service Category:</label>
                            <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                <?= get_all_from_service($dbc, $serviceid, 'category') ?>
                            <?php } else { ?>
                                <select data-placeholder="Select a Category..." id="<?php echo 'category_'.$id_loop; ?>" class="chosen-select-deselect form-control service_category_onchange" width="380">
                                    <option value=""></option>
                                    <?php
                                    if($app_type == '') {
                                        $query = mysqli_query($dbc,"SELECT category, GROUP_CONCAT(DISTINCT(appointment_type)) appointment_type FROM services WHERE deleted=0 GROUP BY `category`");
                                    } else {
                                        $query = mysqli_query($dbc,"SELECT category, GROUP_CONCAT(DISTINCT(appointment_type)) appointment_type FROM services WHERE deleted=0 AND (appointment_type = '' OR appointment_type='$type') GROUP BY `category`");
                                    }
                                    while($row = mysqli_fetch_array($query)) {
                                        if (get_all_from_service($dbc, $serviceid, 'category') == $row['category']) {
                                            $selected = 'selected="selected"';
                                        } else {
                                            $selected = '';
                                        }
                                        echo "<option data-appt-type=',".$row['appointment_type'].",' ".$selected." value='". $row['category']."'>".$row['category'].'</option>';
                                    }
                                    ?>
                                </select>
                            <?php } ?>
                        </div> <!-- Quantity -->

                        <div class="col-sm-<?= $col_head ?>"><label class="show-on-mob">Service Name:</label>
                            <input type="hidden" name="service_ticketid[]" value="">
                            <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                <input type="hidden" id="<?php echo 'serviceid_'.$id_loop; ?>" name="init_serviceid[]" class="serviceid" value="<?= $serviceid ?>"><?= get_all_from_service($dbc, $serviceid, 'heading') ?>
                                <input type="hidden" name="servicelabel" value="<?= get_all_from_service($dbc, $serviceid, 'category') ?>: <?= get_all_from_service($dbc, $serviceid, 'heading') ?>">
                                <input name="init_gst_exempt[]" id="<?php echo 'gstexempt_'.$id_loop; ?>"  type="hidden" value="<?php echo get_all_from_service($dbc, $serviceid, 'gst_exempt'); ?>" class="form-control gstexempt" />
                                <input name="init_service_row_id[]" type="hidden" value="<?= $client_loop ?>" class="insurer_row_id" />
                            <?php } else { ?>
                                <select id="<?php echo 'serviceid_'.$id_loop; ?>" data-placeholder="Select a Service..." name="serviceid[]" class="chosen-select-deselect form-control serviceid" width="380">
                                    <option value=""></option>
                                    <?php $db_category = get_all_from_service($dbc, $serviceid, 'category');
                                    if($app_type == '') {
                                        $query = mysqli_query($dbc,"SELECT s.serviceid, s.heading, r.cust_price service_rate, s.appointment_type, r.editable FROM services s,  company_rate_card r WHERE s.category='$db_category' AND s.serviceid = r.item_id AND r.tile_name LIKE 'Services' AND '$invoice_date' >= r.start_date AND ('$invoice_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')");
                                    } else {
                                        $query = mysqli_query($dbc,"SELECT s.serviceid, s.heading, r.cust_price `service_rate`, s.appointment_type, r.editable FROM services s,  company_rate_card r WHERE (s.appointment_type = '' OR s.appointment_type='$type') AND s.serviceid = r.item_id AND `tile_name` LIKE 'Services' AND '$invoice_date' >= r.start_date AND ('$invoice_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')");
                                    }
                                    $fee_editable = false;
                                    while($row = mysqli_fetch_array($query)) {
                                        if ($serviceid == $row['serviceid']) {
                                            $selected = 'selected="selected"';
                                            if($row['editable'] > 0) {
                                                $fee_editable = true;
                                            }
                                        } else {
                                            $selected = '';
                                        }
                                        echo "<option data-editable='".$row['editable']."' data-appt-type=',".$row['appointment_type'].",' ".$selected." value='". $row['serviceid']."'>".$row['heading'].'</option>';
                                    }
                                    ?>
                                </select>
                            <?php } ?>
                        </div>

                        <div class="col-sm-<?= $col_qty > 0 ? $col_qty : '0 hidden' ?>"><label class="show-on-mob">Quantity:</label>
                            <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                <input name="init_qty[]" id="<?php echo 'srv_qty_'.$id_loop; ?>"  type="hidden" value="<?php echo $qty; ?>" class="qty" onchange="setTotalPrice();" />
                                <?= $qty ?>
                            <?php } else { ?>
                                <input name="srv_qty[]" id="<?php echo 'fee_'.$id_loop; ?>"  type="number" step="any" value="<?php echo $qty; ?>" class="form-control qty" onchange="setTotalPrice();" />
                            <?php } ?>
                        </div>

                        <div class="col-sm-<?= $col_fee > 0 ? $col_fee : '0 hidden' ?>"><label class="show-on-mob">Total Fee:</label>
                            <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                <input name="init_fee[]" id="<?php echo 'fee_'.$id_loop; ?>"  type="hidden" value="<?php echo $fee; ?>" class="fee" />
                                <?= number_format($fee,2) ?>
                            <?php } else { ?>
                                <input name="fee[]" <?= $fee_editable ? '' : 'readonly' ?> id="<?php echo 'fee_'.$id_loop; ?>"  type="number" step="any" value="<?php echo $fee; ?>" class="form-control fee" />
                            <?php } ?>
                            <input name="gst_exempt[]" id="<?php echo 'gstexempt_'.$id_loop; ?>"  type="hidden" value="<?php echo get_all_from_service($dbc, $serviceid, 'gst_exempt'); ?>" class="form-control gstexempt" />
                            <input name="service_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                        </div>

                        <div class="col-sm-2">
                            <!-- Hidden on Returns/Adjustment
                            <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand adjust_block" onclick="rem_service_row(this);">
                            <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand adjust_block" onclick="add_service_row();">
                            -->
                            <label class="return_block"><input type="checkbox" name="servicerow_refund[]" value="<?= $client_loop ?>" onchange="countTotalPrice()"> Refund</label>
                        </div>

                        <div class="col-sm-12 pay-div"></div>
                    </div>
                        <?php
                        $id_loop++;
                    }
                }
            }
            ?>

            <?php } ?>

            <div class="additional_service form-group clearfix" style="<?= $_GET['inv_mode'] == 'adjust' ? 'display:none;' : '' ?>">

                <div class="col-sm-<?= $col_cat > 0 ? $col_cat : '0 hidden' ?>"><label class="show-on-mob">Service Category:</label>
                    <select data-placeholder="Select a Category..." id="category_0" class="chosen-select-deselect form-control service_category_onchange" width="380">
                        <option value=""></option>
                        <?php
                        if((!empty($_GET['invoiceid'])) && ($type != '')) {
                            $query = mysqli_query($dbc,"SELECT category, GROUP_CONCAT(DISTINCT(appointment_type)) appointment_type FROM services WHERE deleted=0 AND (appointment_type = '' OR appointment_type='$type') GROUP BY `category`");
                        } else {
                            $query = mysqli_query($dbc,"SELECT category, GROUP_CONCAT(DISTINCT(appointment_type)) appointment_type FROM services WHERE deleted=0 GROUP BY `category`");
                        }
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option data-appt-type=',".$row['appointment_type'].",' value='". $row['category']."'>".$row['category'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-<?= $col_head ?>"><label class="show-on-mob">Service Name:</label>
                    <input type="hidden" name="service_ticketid[]" value="">
                    <select id="serviceid_0" data-placeholder="Select a Service..." name="serviceid[]" class="chosen-select-deselect form-control serviceid" width="380">
                        <option value=""></option>
                    </select>
                </div>
                <div class="col-sm-<?= $col_qty > 0 ? $col_qty : '0 hidden' ?>"><label class="show-on-mob">Quantity:</label>
                    <input name="srv_qty[]" id="srv_qty_0" type="number" step="any" min=0 value=1 class="form-control qty" onchange="setTotalPrice();" />
                </div>
                <div class="col-sm-<?= $col_fee > 0 ? $col_fee : '0 hidden' ?>"><label class="show-on-mob">Total Fee:</label>
                    <input name="fee[]" readonly id="fee_0" type="number" step="any" value=0 class="form-control fee" />
                    <input name="gst_exempt[]" id="gstexempt_0"  type="hidden" value="0" class="form-control gstexempt" />
                    <input name="service_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                </div>

                <div class="col-sm-2">
                    <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_service_row(this);">
                    <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_service_row();">
                </div>

                <div class="col-sm-12 pay-div"></div>
            </div>

            <div id="add_here_new_service"></div>

        </div>
    </div>
</div>