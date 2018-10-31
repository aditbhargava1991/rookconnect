<div id="inv_summary">
    <h4 class="col-sm-12">Summary</h4>
    <div class="form-group" <?= (in_array('promo',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">
        <span class="popover-examples list-inline">
            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Apply any promotions here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
        </span>
        Promotion:</label>
        <div class="col-sm-9">
            <select data-placeholder="Select a Promotion..." id="promotionid" name="promotionid" class="chosen-select-deselect form-control" width="380">
                <option value=""></option>
                <?php $query = mysqli_query($dbc,"SELECT promotionid, heading, cost FROM promotion WHERE deleted=0");
                while($row = mysqli_fetch_array($query)) {
                    if ($promotionid == $row['promotionid']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $row['promotionid']."'>".$row['heading'].': $'.number_format($row['cost'],2).'</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <?php if (in_array('discount',$field_config) && $_GET['inv_mode'] != 'adjust') { ?>
        <div class="form-group">
            <label for="giftcard" class="col-sm-3 control-label">Discount Type:</label>
            <div class="col-sm-9">
                <label><input type="radio" name="discount_type" value="%" />%</label>
                <label><input type="radio" name="discount_type" checked value="$" />$</label>
            </div>
        </div>
        <div class="form-group">
            <label for="giftcard" class="col-sm-3 control-label">Discount Value:</label>
            <div class="col-sm-9">
                <input name="discount_value" onchange="countTotalPrice()" id="discount_value" type="text" class="form-control" value="<?= $discount_value; ?>" />
            </div>
        </div>
    <?php } ?>

    <div class="form-group" <?= (in_array('pay_mode',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">Payment Method:</label>
        <div class="col-sm-9">

            <select data-placeholder="Select a Type..." name="paid" id="paid_status" class="chosen-select-deselect form-control" width="480">
                <option value=""></option>
                <option <?php if ($paid=='Yes') echo 'selected="selected"';?>  value="Yes">Patient Invoice : Patient is paying full amount on checkout.</option>
                <option <?php if ($paid=='Waiting on Insurer') echo 'selected="selected"';?> value="Waiting on Insurer">Waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> : Clinic is waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> to pay full amount.</option>
                <option <?php if ($paid=='No') echo 'selected="selected"';?>  value="No">Partially Paid : The invoice is being paid partially by patient and partially by <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?>.</option>
                <option <?php if ($paid=='On Account') echo 'selected="selected"';?> value="On Account">A/R On Account : Patient will pay invoice in future. Must choose Payment Type as Apply A/R to Account.</option>
                <option <?php if ($paid=='Credit On Account') echo 'selected="selected"';?> value="Credit On Account">Credit On Account : Patient is appyling credit to profile.</option>
            </select>
        </div>
    </div>
    <?php $value_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='invoice_tax'"))['value'];
    $invoice_tax = explode('*#*',$value_config);

    $total_count = mb_substr_count($value_config,'*#*');
    $tax_rate = 0;
    foreach($invoice_tax as $invoice_tax_line) {
        $invoice_tax_name_rate = explode('**',$invoice_tax_line);
        $tax_rate += floatval($invoice_tax_name_rate[1]);
    } ?>
    <input type="hidden" name="tax_rate" id="tax_rate" data-value="<?= $tax_rate ?>" value="<?= strtoupper(get_contact($dbc, $patientid, 'client_tax_exemption')) == 'YES' ? 0 : $tax_rate ?>" />
    <input name="total_price" value="<?php echo 0+$total_price; ?>" id="total_price" type="hidden" />
    <input name="final_price" value="<?php echo 0+$final_price; ?>" id="final_price" type="hidden" />

    <div class="form-group" <?= (in_array('tips',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">
            <span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the gratuity to be applied to the assigned staff."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
        Gratuity($):</label>
        <div class="col-sm-9">
          <input name="gratuity" onchange="countTotalPrice()" id="gratuity" type="text" class="form-control" value="<?= $gratuity ?>" />
        </div>
    </div>

    <div class="form-group" <?= (in_array('delivery',$field_config) && $_GET['inv_mode'] == 'adjust' ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">
            <span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the delivery method chosen by the <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
        Delivery Option:</label>
        <div class="col-sm-9">
          <select name="delivery_type" data-placeholder="Select a Delivery Option..." id="delivery_type" class="form-control chosen-select-deselect"><option></option>
            <option <?= ($delivery_type == 'Pick-Up' ? 'selected' : '') ?> value="Pick-Up">Pick-Up</option>
            <option <?= ($delivery_type == 'Company Delivery' ? 'selected' : '') ?> value="Company Delivery">Company Delivery</option>
            <option <?= ($delivery_type == 'Drop Ship' ? 'selected' : '') ?> value="Drop Ship">Drop Ship</option>
            <option <?= ($delivery_type == 'Shipping' ? 'selected' : '') ?> value="Shipping">Shipping</option>
            <option <?= ($delivery_type == 'Shipping on Customer Account' ? 'selected' : '') ?> value="Shipping on Customer Account">Shipping on Customer Account</option>
          </select>
        </div>
    </div>

    <div class="form-group confirm_delivery" <?= (($delivery_type == 'Drop Ship' || $delivery_type == 'Shipping' || $delivery_type == 'Company Delivery') && $_GET['inv_mode'] == 'adjust' ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">
            <span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Update the address for delivery. If it is wrong, you will need to update it on the <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> profile. You can also enter a one-time shipping address."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
        Confirm Delivery Address:</label>
        <div class="col-sm-9">
          <input name="delivery_address" onchange="countTotalPrice()" id="delivery_address" type="text" class="form-control" value="<?= $delivery_address ?>" />
        </div>
    </div>

    <div class="form-group deliver_contractor" <?= (($delivery_type == 'Drop Ship' || $delivery_type == 'Shipping') && $_GET['inv_mode'] == 'adjust' ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">
            <span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the contractor that will handle the delivery."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
        Delivery Contractor:</label>
        <div class="col-sm-9">
          <select name="contractorid" id="contractorid" class="form-control chosen-select-deselect"><option></option>
            <?php $contractors = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name`, `name` FROM `contacts` WHERE `category` LIKE 'Contractor%' AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
            foreach($contractors as $contractor) {
                $contractor = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `contactid`='$contractor'"));
                echo "<option ".($contractor['contactid'] == $contractorid ? 'selected' : '')." value='". $contractor['contactid']."'>".($contractor['name'] != '' ? decryptIt($contractor['name']) : decryptIt($contractor['first_name']).' '.decryptIt($contractor['last_name'])).'</option>';
            } ?>
          </select>
        </div>
    </div>

    <div class="form-group ship_amt" <?= (($delivery_type == '' || $delivery_type == 'Pick-Up') && $_GET['inv_mode'] == 'adjust' ? 'style="display:none;"' : '') ?>>
        <label for="site_name" class="col-sm-3 control-label">
            <span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Enter the cost of shipping."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
        Delivery/Shipping Amount:</label>
        <div class="col-sm-9">
          <input name="delivery" onchange="countTotalPrice()" id="delivery" type="text" class="form-control" value="<?= $delivery ?>" />
        </div>
    </div>

    <div class="form-group" <?= (in_array('ship_date',$field_config) && $_GET['inv_mode'] == 'adjust' ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">
            <span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Enter the date by which the order will ship."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
        Ship Date:</label>
        <div class="col-sm-9">
          <input name="ship_date" onchange="countTotalPrice()" id="ship_date" type="text" class="form-control datepicker" value="<?= $ship_date ?>" />
        </div>
    </div>

    <?php if (in_array('assembly',$field_config) && $_GET['inv_mode'] != 'adjust') { ?>
    <div class="form-group">
        <label for="giftcard" class="col-sm-3 control-label">
        Assembly:</label>
        <div class="col-sm-9">
            <input name="assembly" onchange="countTotalPrice()" id="assembly" type="text" class="form-control" value="<?= $assembly; ?>" />
        </div>
    </div>
    <?php } ?>

    <?php if (in_array('Customer Billing Status',$field_config) && $_GET['inv_mode'] != 'adjust') { ?>
    <div class="form-group">
        <label for="giftcard" class="col-sm-3 control-label">
        Customer Billing Status:</label>
        <div class="col-sm-9">
            <select class="chosen-select-deselect" name="customer_billing_status"><option></option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Declined">Declined</option>
            </select>
        </div>
    </div>
    <?php } ?>

    <div class="form-group" <?= (in_array('next_appt',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">
        <span class="popover-examples list-inline">
            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select to book the next appointment. If you click save, the appointment will be saved, and will not appear on this invoice when it is being edited."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
        </span>
        Next Appointment<span class="hp-red">*</span>:</label>
        <div class="col-sm-9">
          <label><input required name="next_appointment" type="radio" value="Yes" class="form next_appointment" /> Yes</label>
          <label><input required name="next_appointment" checked type="radio" value="No" class="form next_appointment" /> No</label>
            <a href="/mrbs/" target="_blank" class="next_appointment_fields pull-right btn brand-btn">Check Calendar</a><br><br>
        </div>
    </div>

    <input type="hidden" name="bookingid" value=<?php echo $bookingid; ?> >

    <span class="next_appointment_fields">
        <div class="form-group">
            <label for="first_name" class="col-sm-3 control-label"></label>
            <div class="col-sm-9 book-calendar">
                <div class="form-group clearfix">
                    <label class="col-sm-3">Start Appt Date & Time
                        <span class="popover-examples list-inline">
                            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the 15 minute interval labels to specify those times"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="25"></a>
                        </span>
                    </label>
                    <label class="col-sm-3">End Appt Date & Time
                        <span class="popover-examples list-inline">
                            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the 15 minute interval labels to specify those times"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="25"></a>
                        </span>
                    </label>
                    <label class="col-sm-5">Type
                    </label>
                </div>
                <div class="form-group clearfix book-validate-cal">
                    <div class="book_1">
                        <span class="col-sm-3">
                            <input name="block_appoint_date[]" id="appointdate_1" type="text" placeholder="Click for Datepicker" class="datetimepicker form-control"></p>
                        </span>
                        <span class="col-sm-3">
                            <input name="block_end_appoint_date[]" id="endappointdate_1" type="text" placeholder="Click for Datepicker" class="datetimepicker form-control"></p>
                        </span>
                        <span class="col-sm-5">
                            <select data-placeholder="Select a Type..." id="appointtype_1" name="appointtype[]" class="chosen-select-deselect form-control input-sm">
                                <option value=""></option>
                                <?php $appointment_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                                foreach ($appointment_types as $appointment_type) {
                                    echo '<option '.($type == $appointment_type['id'] ? 'selected' : '').' value="'.$appointment_type['id'].'">'.$appointment_type['name'].'</option>';
                                } ?>
                            </select>
                        </span>
                        <span class="col-sm-1">
                            <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="removeclass(this);" title="Remove this Row">
                            <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="addmore();" title="Add Additional Appointment">
                        </span><div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </span>

    <div class="form-group" <?= (in_array('survey',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">
        <span class="popover-examples list-inline">
            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the proper survey to send here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
        </span>
        Send Survey:</label>
        <div class="col-sm-9">
            <select data-placeholder="Select a Survey..." name="survey" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $query = mysqli_query($dbc,"SELECT surveyid, name, service FROM crm_feedback_survey_form WHERE deleted=0");
                while($row = mysqli_fetch_array($query)) {
                    echo "<option ".($get_invoice['survey'] == $row['surveyid'] ? 'selected' : '')." value='". $row['surveyid']."'>".$row['name'].' : '.$row['service'].'</option>';
                }
              ?>
            </select>
        </div>
    </div>

    <?php if (strpos(','.get_config($dbc, 'crm_dashboard').',', ',Recommendations,') !== FALSE) { ?>
        <div class="form-group" <?= (in_array('request_recommend',$field_config) ? '' : 'style="display:none;"') ?>>
            <label for="site_name" class="col-sm-3 control-label">
                <span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select whether or not to send the Recommendation email."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Request Recommendation Report:</label>
            <div class="col-sm-9">
                <label class="control-label"><input type="radio" name="request_recommendation" <?= ($get_invoice['request_recommend'] == 'send' ? 'checked' : '') ?> value="send"> Send</label>
                <label class="control-label"><input type="radio" name="request_recommendation" <?= ($get_invoice['request_recommend'] == 'no' ? 'checked' : '') ?> value="no"> Don't Send</label>
            </div>
        </div>
    <?php } ?>

       <div class="form-group" <?= (in_array('followup',$field_config) ? '' : 'style="display:none;"') ?>followup>
        <label for="site_name" class="col-sm-3 control-label">
        <span class="popover-examples list-inline">
            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the proper follow up email type."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
        </span>
        Send Follow Up Email After Assessment: </label>
        <div class="col-sm-9">
            <select data-placeholder="Select an Email Type..." name="follow_up_assessment_email" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <option <?= ($get_invoice['follow_up_email'] == 'Massage' ? 'selected' : '') ?> value="Massage">Massage Follow Up Email</option>
              <option <?= ($get_invoice['follow_up_email'] == 'Physiotherapy' ? 'selected' : '') ?> value="Physiotherapy">Physiotherapy Follow Up Email</option>
            </select>
        </div>
      </div>

    <?php if (in_array('giftcard',$field_config)) { ?>
        <div class="form-group">
            <label for="giftcard" class="col-sm-3 control-label">
            Gift Card:</label>
            <div class="col-sm-9">
                <input type="text" <?php echo ($return ? 'readonly' : ''); ?> name="gf_number" onblur="changeGF(this.value);" id="gf_number" value="<?= ($gf_number) ? $gf_number : ''; ?>" type="text" class="form-control" />
            </div>
        </div>
    <?php } ?>

    <?php if($_GET['inv_mode'] == 'adjust') {
        $previous_payments = 0; ?>
        <div class="form-group">
            <label for="additional_note" class="col-sm-3 control-label">Invoice Payment Information:</label>
            <div class="col-sm-9">
                <div class="form-group clearfix hide-titles-mob">
                    <label class="col-sm-4">Paid By</label>
                    <label class="col-sm-3">Payment Type</label>
                    <label class="col-sm-3">Payment Amount</label>
                    <label class="col-sm-2 return_block">Refund Amount</label>
                </div>

                <?php foreach($insurer_paid_who as $loop_check => $check_insurer) {
                    $check_amt = $insurer_paid_amt[$loop_check];
                    if($check_amt != 0) {
                        foreach($insurer_paid_who as $valid_check => $valid_insurer) {
                            $valid_amt = $insurer_paid_amt[$valid_check];
                            if($loop_check != $valid_check && $check_insurer == $valid_insurer) {
                                $insurer_paid_amt[$loop_check] += $valid_amt;
                                unset($insurer_paid_who[$valid_check]);
                                unset($insurer_paid_amt[$valid_check]);
                            }
                        }
                    } else {
                        unset($insurer_paid_who[$loop_check]);
                        unset($insurer_paid_amt[$loop_check]);
                    }
                }
                $insurer_paid_who = array_values($insurer_paid_who);
                $insurer_paid_amt = array_values($insurer_paid_amt);

                foreach($insurer_paid_who as $i => $ins_pay_id) {
                    if($insurer_paid_amt[$i] > 0) { ?>
                        <div class="form-group clearfix">
                            <div class="col-sm-4"><label class="col-sm-4 show-on-mob">Paid By:</label><?= get_client($dbc, $ins_pay_id) ?></div>
                            <div class="col-sm-3"><label class="col-sm-4 show-on-mob">Payment Type:</label><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Payment</div>
                            <div class="col-sm-3"><label class="col-sm-4 show-on-mob">Payment Amount:</label>$<?= number_format($insurer_paid_amt[$i],2) ?><input type="hidden" name="amount_previously_paid[]" value="<?= $insurer_paid_amt[$i] ?>"><input type="hidden" name="insurer_amt[]" value="<?= $insurer_paid_amt[$i] ?>"><input type="hidden" name="insurer_payer[]" value="<?= $ins_pay_id ?>"></div>
                        </div>
                        <?php $previous_payments += $insurer_paid_amt[$i] * 1;
                    }
                }

                foreach($patient_paid_type as $loop_check => $check_patient) {
                    $check_amt = $patient_paid_amt[$loop_check];
                    if($check_amt != 0) {
                        foreach($patient_paid_type as $valid_check => $valid_patient) {
                            $valid_amt = $patient_paid_amt[$valid_check];
                            if($loop_check != $valid_check && $check_patient == $valid_patient) {
                                $patient_paid_amt[$loop_check] += $valid_amt;
                                unset($patient_paid_type[$valid_check]);
                                unset($patient_paid_amt[$valid_check]);
                            }
                        }
                    } else {
                        unset($patient_paid_type[$loop_check]);
                        unset($patient_paid_amt[$loop_check]);
                    }
                }
                $patient_paid_type = array_values($patient_paid_type);
                $patient_paid_amt = array_values($patient_paid_amt);

                foreach($patient_paid_type as $i => $patient_pay_type) { ?>
                    <div class="form-group clearfix">
                        <div class="col-sm-4"><label class="col-sm-4 show-on-mob">Paid By:</label><?= $patient ?></div>
                        <div class="col-sm-3"><label class="col-sm-4 show-on-mob">Payment Type:</label><?= $patient_pay_type ?></div>
                        <div class="col-sm-3"><label class="col-sm-4 show-on-mob">Payment Amount:</label>$<?= number_format($patient_paid_amt[$i],2) ?><input type="hidden" name="amount_previously_paid[]" value="<?= $patient_paid_amt[$i] ?>"></div>
                        <div class="col-sm-2 return_block"><label class="col-sm-4 show-on-mob">Refund amount to <?= $patient_pay_type ?>:</label>
                            <input type="hidden" name="refund_to_type[]" value="<?= $patient_pay_type ?>"><input type="number" class="form-control" name="refund_type_amount[]" value="0" min="0" max="<?= $patient_paid_amt[$i] ?>" data-status="auto" onchange="adjustRefundAmt();" step="any"></div>
                    </div>
                    <?php $previous_payments += $patient_paid_amt[$i] * 1;
                } ?>
            </div>
        </div>
        <script>
        var previous_payment = '<?= $previous_payments ?>' * 1;
        </script>
    <?php } ?>
    <div class="form-group payment_option">
        <label for="additional_note" class="col-sm-3 control-label"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Payment:</label>
        <div class="col-sm-9">
            <label class="col-sm-12 control-checkbox"><input type="checkbox" name="add_credit" value="add_credit" onchange="allow_edit_amount();">
            <input type="hidden" name="credit_balance" value=0>Add balance as credit on <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Account</label>
            <div class="form-group clearfix hide-titles-mob">
                <label class="col-sm-5 text-center">Type</label>
                <label class="col-sm-5 text-center">Amount</label>
            </div>
            <div class="additional_payment form-group clearfix adjust_block">
                <div class="col-sm-5"><label class="show-on-mob">Payment Type:</label>
                  <select id="payment_type" name="payment_type[]" data-placeholder="Select a Type..." class="chosen-select-deselect form-control" width="380">
                        <option value=''></option>
                        <?php foreach(explode(',',get_config($dbc, 'invoice_payment_types')) as $available_pay_method) { ?>
                            <option value = '<?= $available_pay_method ?>'><?= $available_pay_method ?></option>
                        <?php } ?>
                        <?php if($account_balance != 0) { ?>
                        <option value = 'Patient Account' >Apply Credit to <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Account : $<?php echo $account_balance; ?></option>
                        <?php }
                        if(strpos(WEBSITE_URL,'clinicace') !== FALSE) { ?>
                            <option value = 'On Account'>Apply A/R to Account</option>
                        <?php } ?>
                  </select>
                </div>
                <div class="col-sm-5"><label class="show-on-mob">Payment Amount:</label>
                    <input name="payment_price[]" type="text" id="payment_price_0" class="form-control payment_price" onchange="countTotalPrice();" />
                </div>
                <div class="col-sm-2">
                    <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_patient_payment_row(this);">
                    <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_patient_payment_row();">
                </div>
            </div>

            <div id="add_here_new_payment"></div>
        </div>
    </div>
    
    <?php if(in_array('send_email',$field_config)) { ?>
        <div class="form-group">
            <label class="col-sm-3 pull-left">Send <?= POS_ADVANCE_NOUN ?> by Email:</label>
            <div class="inline pull-left" style="width:5em;">
                <label class="no-pad form-checkbox"><input type="checkbox" name="email_invoice" value="yes" onclick="$('.email_validate').toggle();"> Yes</label>
            </div>
            <div class="scale-to-fill email_validate" style="display:none;">
                <div class="col-sm-12"><input type="text" name="invoice_email_address" placeholder="Email Address" class="form-control" value="<?= $patient > 0 ? get_email($dbc, $patient) : '' ?>"></div>
            </div>
        </div>
    <?php } ?>

       <div class="form-group" <?= (in_array('comment',$field_config) ? '' : 'style="display:none;"') ?>followup>
        <label for="site_name" class="col-sm-3 control-label">Comment:</label>
        <div class="col-sm-9">
            <textarea name="comment" class="form-control"><?= $comment ?></textarea>
        </div>
      </div>

     <div class="form-group">
        <div class="col-sm-3">
            <p><span class="empire-red pull-right"><em>Required Fields *</em></span></p>
        </div>
        <div class="col-sm-6"></div>
    </div>

    <div class="control-div">
        <div class="form-group">
            <div class="col-sm-3 col-xs-4">
                <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking here will discard changes and return you to the <?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?> tile main dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="index.php?tab=today"><img width="30" class="override-theme-color-icon no-toggle" title="Cancel Invoice" src="../img/icons/ROOK-trash-icon.png"></a>
            </div>
            <div class="col-sm-9 col-xs-8">
                <button type="submit" name="submit_btn" onclick="return validateappo();" id="submit" value="<?= $_GET['inv_mode'] == 'adjust' ? 'Adjustment' : 'New' ?>" class="btn brand-btn pull-right">Submit</button>
                <span class="popover-examples list-inline pull-right" style="margin:5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to Submit the invoice after processing payment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <?php if($_GET['inv_mode'] != 'adjust') { ?>
                    <button type="submit" name="save_btn" onclick="return validateappo();" id="save" value="Saved" class="pull-right image-btn"><img src="../img/icons/save.png" alt="Save" width="30" class="override-theme-color-icon" /></button>
                    <span class="popover-examples list-inline pull-right" style="margin:5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to Save this invoice for when a client is checking out (you will need to complete the transaction later)."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
	$('.form-control').change(function() {
		<?php if($patient != '') { ?>
			$('.detail_patient_name').html('<?= $patient ?>');
			$('.detail_patient_injury').html('<?= $injury ?>');
			if($('[name=treatment_plan]').is(':visible')) {
				$('.detail_patient_treatment').html('<?= $treatment_plan ?>').closest('h4').show();
			} else {
				$('.detail_patient_treatment').closest('h4').hide();
			}
			$('.detail_staff_name ').html('<?= $staff ?>');
		<?php } else { ?>
			if($('.non_patient_fields').is(':visible')) {
				$('.detail_patient_name').html($('[name=first_name]').val() + ' ' + $('[name=last_name]').val());
				$('.detail_patient_injury').closest('h4').hide();
			} else {
				$('.detail_patient_name').html($('[name=patientid] option:selected').text());
                if($('select[name=patientid]').val() > 0) {
                    $('#header_summary').load('../Contacts/contact_profile.php?summary=true&contactid='+$('select[name=patientid]').val(),function() { $(this).append('<div class="clearfix"></div>'); });
                    $.post('invoice_ajax.php?action=get_tax_exempt', {
                        contactid: $('select[name=patientid]').val()
                    }, function(response) {
                        if(response.toUpperCase() == 'YES') {
                            $('[name=tax_rate]').val(0);
                        } else {
                            $('[name=tax_rate]').val($('[name=tax_rate]').data('value'));
                        }
                    });
                    if(this.name == 'invoice_email_address') {
                        $.post('invoice_ajax.php?action=set_email_address', { contactid: $('select[name=patientid]').val(), email: this.value }, function(response) {
                            console.log(response);
                        });
                    } else {
                        $.post('invoice_ajax.php?action=get_email_address', { contactid: $('select[name=patientid]').val() }, function(response) {
                            $('[name=invoice_email_address]').val(response);
                        });
                    }
                }
				if($('#injuryid_chosen').is(':visible')) {
					$('.detail_patient_injury').html($('[name=injuryid] option:selected').text() == '' ? 'Please Select' : $('[name=injuryid] option:selected').text()).closest('h4').show();
				}
			}
			if($('[name=treatment_plan]').is(':visible')) {
				$('.detail_patient_treatment').html($('[name=treatment_plan] option:selected').text()).closest('h4').show();
			} else {
				$('.detail_patient_treatment').closest('h4').hide();
			}
			$('.detail_staff_name ').html($('[name=therapistsid] option:selected').text() == '' ? 'N/A' : $('[name=therapistsid] option:selected').text());
		<?php } ?>
		$('.detail_promo_amt ').html($('[name=promotionid] option:selected').text() == '' ? 'N/A' : $('[name=promotionid] option:selected').text());
		if($('#paid_status').val() != '' && $('#paid_status').val() != 'Saved' && $('#paid_status').val() != 'Waiting on Insurer') {
			$('.detail_patient_amt').closest('h4').show();
		} else {
			$('.detail_patient_amt').closest('h4').hide();
		}
		if($('#paid_status').val() == 'No' || $('#paid_status').val() == 'Waiting on Insurer') {
			$('.detail_insurer_amt').closest('h4').show();
		} else {
			$('.detail_insurer_amt').closest('h4').hide();
		}
		$('[name="serviceid[]"]').each(function() {
			var label = $(this).find('option:selected').text();
			var fee = $(this).closest('.form-group').find('[name="fee[]"]').val();
		});
	});
	$('.form-control').first().change();
	<?php if($paid != '') {
		echo "pay_mode_selected('$paid');\n";
		if($paid == 'No' || $paid == 'Waiting on Insurer') {
			echo "var service_ins = '".$get_invoice['service_insurer']."';\n";
			echo "var inv_ins = '".$get_invoice['inventory_insurer']."';\n";
			echo "var package_ins = '".$get_invoice['package_insurer']."';\n";
		} else {
			echo "var service_ins = '0:0';\n";
			echo "var inv_ins = '0:0';\n";
			echo "var package_ins = '0:0';\n";
		}
	} else {
		echo "var service_ins = '0:0';\n";
		echo "var inv_ins = '0:0';\n";
		echo "var package_ins = '0:0';\n";
	} ?>

	var i = 1;
	$(service_ins.split(',')).each(function() {
		var j = 0;
		$(this.split('#*#')).each(function() {
			var info = this.split(':');
			var target = $('.service_option').find('.form-group').eq(i).find('.pay-div').eq(j);
			target.find('[name="insurerid[]"]').val(info[0]).trigger('change.select2');
			target.find('[name="insurer_payment_amt[]"]').val(info[1]);
			j++;
		});
		i++;
	});
	var i = 1;
	$(inv_ins.split(',')).each(function() {
		var j = 0;
		$(this.split('#*#')).each(function() {
			var info = this.split(':');
			var target = $('.product_option').find('.form-group').eq(i).find('.pay-div').eq(j);
			target.find('[name="insurerid[]"]').val(info[0]).trigger('change.select2');
			target.find('[name="insurer_payment_amt[]"]').val(info[1]);
			j++;
		});
		i++;
	});
	var i = 1;
	$(package_ins.split(',')).each(function() {
		var j = 0;
		$(this.split('#*#')).each(function() {
			var info = this.split(':');
			var target = $('.package_option').find('.form-group').eq(i).find('.pay-div').eq(j);
			target.find('[name="insurerid[]"]').val(info[0]).trigger('change.select2');
			target.find('[name="insurer_payment_amt[]"]').val(info[1]);
			j++;
		});
		i++;
	});
	countTotalPrice();
});
$(document).on('change', 'select[name="app_type"]', function() { changeApptType(this.value); });
$(document).on('change', 'select[name="pricing"]', function() {
    if ($('[name="pricing"] option:selected').val()=='admin_price') {
        $('[name="unit_price[]"]').attr('readonly', false);
    } else {
        $('[name="unit_price[]"]').attr('readonly', true);
    }
    updatePricing();
});
$(document).on('change', '[name="unit_price[]"]', function() {
    adminPrice(this);
});
$(document).on('change', 'select[name="linepricing[]"]', function() {
    var rowid = this.id.split('_')[1];
    if ($('#linepricing_'+rowid+' option:selected').val()=='admin_price') {
        $('#unitprice_'+rowid).attr('readonly', false);
    } else {
        $('#unitprice_'+rowid).attr('readonly', true);
    }
    updatePricing();
});
$(document).on('change', 'select[name="paid"]', function() { pay_mode_selected(this.value); });
$(document).on('change', 'select.service_category_onchange', function() { changeCategory(this); });
$(document).on('change', 'select[name="serviceid[]"]', function() { changeService(this); });
$(document).on('change', '[name="fee[]"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="inventorycat[]"]', function() { filterInventory(this); });
$(document).on('change', 'select[name="inventorypart[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="inventoryid[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="invtype[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="packagecat[]"]', function() { changePackage(this); });
$(document).on('change', 'select[name="packageid[]"]', function() { changePackage(this); });
$(document).on('change', 'select[name="promotionid"]', function() { changePromotion(this); });
$(document).on('change', 'select[name="delivery_type"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="contractorid"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="payment_type[]"]', function() { set_patient_payment_row(); });

function pay_mode_selected(paid) {
	if(paid == 'No' || paid == 'Waiting on Insurer') {
		if($('.pay-div').html() == '') {
			$('.pay-div').html('<div class="insurer_line"><label class="col-sm-3 control-label"><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Name:</label>'+
				'<div class="col-sm-4"><select name="insurerid[]" class="chosen-select-deselect form-control" width="380">'+
                    '<option value=""></option><?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category IN ('".implode("','",$payer_config)."') AND deleted=0 ORDER BY name"),MYSQLI_ASSOC));
					foreach($query as $row) {
						echo '<option value="'. $row.'">'.htmlentities(get_client($dbc, $row), ENT_QUOTES).'</option>';
					}
					?></select></div>'+
				'<label class="col-sm-3 control-label"><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Portion: <span class="popover-examples list-inline">'+
					'<a href="#job_file" data-toggle="tooltip" data-placement="top" title="The portion that the <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> will pay."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span></label>'+
				'<div class="col-sm-3"><input type="number" step="any" name="insurer_payment_amt[]" class="form-control" value="0" onchange="countTotalPrice();">'+
					'<input type="hidden" name="insurer_row_applied[]" value=""></div>'+
				'<div class="col-sm-3"><img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_insurer_row(this);">'+
					'<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_insurer_row(this);"></div></div>');
			$('[name="insurerid[]"]').select2({
                width: '100%'
            });
			$('.pay-div').each(function() {
				$(this).find('[name="insurer_row_applied[]"]').val($(this).closest('.form-group').find('.insurer_row_id').val());
			});
		}
		if(paid == 'Waiting on Insurer') {
			$('[name="serviceid[]"]').change();
			$('[name="quantity[]"]').change();
			$('[name="packageid[]"]').change();
			$('[name="misc_qty[]"]').change();
		}
	} else {
		$('.pay-div').empty();
	}
	$('[name=paid]').val(paid).trigger('change.select2');
	$('[name="payment_price[]"]').last().attr('readonly','readonly');
	countTotalPrice();
}

var clone = $('.book-validate-cal').clone();
clone.find('.datetimepicker').val('');
clone.find('.datetimepicker').each(function() {
$(this).removeAttr('id').removeClass('hasDatepicker');
	$('.datetimepicker').datetimepicker({
		controlType: 'select',
		changeMonth: true,
		changeYear: true,
		yearRange: '<?= date('Y') - 10 ?>:<?= date('Y') + 5 ?>',
		dateFormat: 'yy-mm-dd',
		timeFormat: "hh:mm tt",
		minuteGrid: 15,
		hourMin: 6,
		hourMax: 20,
		//minDate: 0
	});
});
function addmore()
{
	var classname = $('.book-calendar [class^=book_]').last().attr('class');
	var classes = classname.split("_");
	var value = parseInt(classes[1]) + 1;
	var currentclass = 'book_' + value;

	var insertstring = '<div class="'+ currentclass +'">'+
							'<span class="col-sm-3">'+
								'<input name="block_appoint_date[]" id="appointdate_'+value+'" type="text" placeholder="Click for Datepicker" class="datetimepicker form-control"></p>'+
							'</span>'+
							'<span class="col-sm-3">'+
								'<input name="block_end_appoint_date[]" id="endappointdate_'+value+'" type="text" placeholder="Click for Datepicker" class="datetimepicker form-control"></p>'+
							'</span>'+
							'<span class="col-sm-5">'+
								'<select data-placeholder="Select a Type..." id="appointtype_'+value+'" name="appointtype[]" class="chosen-select-deselect form-control input-sm"><option value=""></option>'+
								'<option value="A">Private-PT-Assessment</option>'+
							'<option value="B">Private-PT-Treatment</option>'+
							'<option value="C">MVC-IN-PT-Assessment</option>'+
							'<option value="D">MVC-IN-PT-Treatment</option>'+
							'<option value="F">MVC-OUT-PT-Assessment</option>'+
							'<option value="G">MVC-OUT-PT-Treatment</option>'+
							'<option value="H">WCB-PT-Assessment</option>'+
							'<option value="J">WCB-PT-Treatment</option>'+
							'<option value="K">Private-MT</option>'+
							'<option value="L">MVC-IN-MT</option>'+
							'<option value="M">MVC-OUT-MT</option>'+
							'<option value="N">AHS-PT-Assessment</option>'+
							'<option value="O">AHS-PT-Treatment</option>'+
							'<option value="S">Reassessment</option>'+
							'<option value="T">Post-Reassessment</option>'+
							'<option value="U">Private-MT-Assessment</option>'+
							'<option value="V">Orthotics</option>'+
							'</select></p>'+
							'</span>'+
							'<span class="col-sm-1">'+
							'<img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="removeclass(this);" title="Remove this Row">'+
							'<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="addmore();" title="Add Additional Appointment">'+
							'</span><div class="clearfix"></div>'+
						'</div>';
	jQuery(insertstring).insertAfter('.' + classname);
	resetChosen($('.'+currentclass).find('.chosen-select-deselect'));
	var clone = $('.book-validate-cal').clone();
	clone.find('.datetimepicker').val('');
	clone.find('.datetimepicker').each(function() {
		$(this).removeAttr('id').removeClass('hasDatepicker');
		$('.datetimepicker').datetimepicker({
			controlType: 'select',
			changeMonth: true,
			changeYear: true,
			yearRange: '<?= date('Y') - 10 ?>:<?= date('Y') + 5 ?>',
			dateFormat: 'yy-mm-dd',
			timeFormat: "hh:mm tt",
			minuteGrid: 15,
			hourMin: 7,
			hourMax: 19
		});
	});
}

function removeclass(remove)
{
	if($('[class^=book_]').length == 1) {
		addmore();
	}
	$(remove).closest('[class^=book_]').remove();
}

function validateappo()
{
	if(jQuery("input[name='next_appointment']:checked").val() == 'Yes' || jQuery("input[name='next_appointment']:checked").val() == 'yes')
	{
		var count = 0;
		//alert(jQuery(".book-validate-cal > div"));
		var therapiststemid = "<?php echo $get_invoice['therapistsid']; ?>";
		jQuery(".book-validate-cal").children().each(function(n, i) {
			if(typeof this.className !== 'object') {
				var classname = this.className;
				var splitclass = classname.split("_");
				var i = parseInt(splitclass[1]);
				var appdate = jQuery('#appointdate_' + i).val();
				var endappdate = jQuery('#endappointdate_' + i).val();
				$.ajax({
				  type: "GET",
				  url: "../Invoice/appointment_ajax.php",
				  data: 'appdate=' + appdate + '&endappdate=' + endappdate + '&therapistid=' + therapiststemid,
				  cache: false,
				  success: function(data){
					  if(data == 1) {
						  jQuery('#appointdate_' + i)
						  jQuery('#appointdate_' + i).addClass('borderClass');
						  jQuery('#endappointdate_' + i).addClass('borderClass');
						  count = 1;
					  }
					  else {
						  jQuery('#appointdate_' + i)
						  jQuery('#appointdate_' + i).removeClass('borderClass');
						  jQuery('#endappointdate_' + i).removeClass('borderClass');
					  }

				  },

				  error: function(data) {
					  alert("Something Wrong in Appointment");
				  },

				  async:false
				});
			}
		});

		if(count > 0) {
			alert("There are some clashes in Appointment dates marked with Red Border");
			return false;
		}

		return true;
	}
}

function billTicket(input) {
	var block = $(input).closest('label');
	if(input.checked) {
		block.find('[disabled]').removeAttr('disabled');
	} else {
		block.find('[type=hidden]').prop('disabled',true);
	}
	setTotalPrice();
}
</script>