<div id="inv_services">
    <h3>Services</h3>
    <div class="form-group service_option" <?= (in_array('services',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="additional_note" class="col-sm-2 control-label">
        <span class="popover-examples list-inline">
            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the service."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
        </span>
        Services:</label>
        <div class="col-sm-7">
            <div class="form-group clearfix hide-titles-mob">
                <label class="col-sm-4 text-center">Category</label>
                <label class="col-sm-5 text-center">Service Name</label>
                <label class="col-sm-2 text-center">Fee</label>
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
                        ?>

                    <div class="form-group clearfix">
                        <div class="col-sm-4"><label class="show-on-mob">Service Category:</label>
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
                        </div> <!-- Quantity -->

                        <div class="col-sm-5"><label class="show-on-mob">Service Name:</label>
                            <input type="hidden" name="service_ticketid[]" value="">
                            <select id="<?php echo 'serviceid_'.$id_loop; ?>" data-placeholder="Select a Service..." name="serviceid[]" class="chosen-select-deselect form-control serviceid" width="380">
                                <option value=""></option>
                                <?php
                                //$query = mysqli_query($dbc,"SELECT serviceid, category, service_type, fee FROM services WHERE deleted=0 AND (appointment_type = '' OR appointment_type='$type')");
                                $db_category = get_all_from_service($dbc, $serviceid, 'category');
                                if($app_type == '') {
                                    //$query = mysqli_query($dbc,"SELECT serviceid, category, heading, fee FROM services WHERE deleted=0 AND category='$db_category'");

                                    $query = mysqli_query($dbc,"SELECT s.serviceid, s.heading, r.cust_price service_rate, s.appointment_type, r.editable FROM services s,  company_rate_card r WHERE s.category='$db_category' AND s.serviceid = r.item_id AND r.tile_name LIKE 'Services' AND '$invoice_date' >= r.start_date AND ('$invoice_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')");
                                } else {
                                    //$query = mysqli_query($dbc,"SELECT serviceid, category, heading, fee FROM services WHERE deleted=0 AND (appointment_type = '' OR appointment_type='$type')");

                                    $query = mysqli_query($dbc,"SELECT s.serviceid, s.heading, r.cust_price `service_rate`, s.appointment_type, r.editable FROM services s,  company_rate_card r WHERE (s.appointment_type = '' OR s.appointment_type='$type') AND s.serviceid = r.item_id AND `tile_name` LIKE 'Services' AND '$invoice_date' >= r.start_date AND ('$invoice_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')");
                                }
                                $fee_editable = false;
                                //$query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE deleted=0");
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
                        </div>

                        <div class="col-sm-2"><label class="show-on-mob">Total Fee:</label>
                            <input name="fee[]" <?= $fee_editable ? '' : 'readonly' ?> id="<?php echo 'fee_'.$id_loop; ?>"  type="number" step="any" value="<?php echo $fee; ?>" class="form-control fee" />
                            <input name="gst_exempt[]" id="<?php echo 'gstexempt_'.$id_loop; ?>"  type="hidden" value="<?php echo get_all_from_service($dbc, $serviceid, 'gst_exempt'); ?>" class="form-control gstexempt" />
                            <input name="service_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                        </div>

                        <div class="col-sm-1">
                            <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_service_row(this);">
                            <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_service_row();">
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

            <div class="additional_service form-group clearfix">

                <div class="col-sm-4"><label class="show-on-mob">Service Category:</label>
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
                <div class="col-sm-5"><label class="show-on-mob">Service Name:</label>
                    <input type="hidden" name="service_ticketid[]" value="">
                    <select id="serviceid_0" data-placeholder="Select a Service..." name="serviceid[]" class="chosen-select-deselect form-control serviceid" width="380">
                        <option value=""></option>
                        <?php
                        /*
                        $query = mysqli_query($dbc,"SELECT serviceid, category, heading, fee FROM services WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['serviceid']."'>".$row['category'].' : '.$row['heading']. ' : '.$row['fee'].'</option>';
                        }
                        */
                        ?>
                    </select>
                </div>
                <div class="col-sm-2"><label class="show-on-mob">Total Fee:</label>
                    <input name="fee[]" readonly id="fee_0" type="number" step="any" value=0 class="form-control fee" />
                    <input name="gst_exempt[]" id="gstexempt_0"  type="hidden" value="0" class="form-control gstexempt" />
                    <input name="service_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                </div>

                <div class="col-sm-1">
                    <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_service_row(this);">
                    <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_service_row();">
                </div>

                <div class="col-sm-12 pay-div"></div>
            </div>

            <div id="add_here_new_service"></div>

            <!--<div class="form-group triple-gapped clearfix">
                <div class="col-sm-offset-4 col-sm-8">
                    <button id="add_row_service" class="btn brand-btn pull-left">Add Service</button>
                </div>
            </div>-->

        </div>
    </div>
</div>