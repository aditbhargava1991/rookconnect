<input type="hidden" name="set_gf" id="set_gf" />
<input type="hidden" id="paid_notpaid" name="paid_notpaid" value="<?= $paid ?>" />
<input type="hidden" name="set_promotion" id="set_promotion" value="<?= $promotionid > 0 ? get_promotion($dbc, $promotionid, 'cost') : '' ?>" />
<input type="hidden" name="mva_claim_price" value="<?= $mva_claim_price ?>" />

<input type="hidden" id="invoiceid" name="invoiceid" value="<?= $invoiceid ?>" />
<input type="hidden" id="patientid" name="patientid" value="<?= $get_invoice['patientid'] ?>" />
<input type="hidden" id="therapistsid" name="therapistsid" value="<?= $get_invoice['therapistsid'] ?>" />
<input type="hidden" id="injuryid" name="injuryid" value="<?= $injuryid ?>" />

<?php if(!empty($_GET['from'])) {
    echo '<input type="hidden" name="invoicefrom" value="'.$_GET['from'].'" />';
    echo '<input type="hidden" name="invoicefrom_start" value="'.$_GET['report_from'].'" />';
    echo '<input type="hidden" name="invoicefrom_end" value="'.$_GET['report_to'].'" />';
}
if(!empty($_GET['search_user'])) {
    echo '<input type="hidden" name="search_user" value="'.$_GET['search_user'].'" />';
}
if(!empty($_GET['search_invoice'])) {
    echo '<input type="hidden" name="search_invoice" value="'.$_GET['search_invoice'].'" />';
} ?>
<script>
$(document).ready(function() {
	<?php if ( isset($_GET['inv_mode']) && $_GET['inv_mode'] == 'adjust' ) { ?>
		$('.adjust_block_pricing').hide();
	<?php } ?>
});
function view_profile(img) {
    var id = $(img).closest('.form-group').find('select').val();
    if(id > 0) {
        overlayIFrameSlider('../Contacts/contacts_inbox.php?edit='+id,'auto',true,true);
    } else {
        alert('Please select <?= CONTACTS_NOUN ?> before viewing the profile.');
    }
}
</script>

<div id="inv_details">
    <h4 class="col-sm-12">Details</h4>
    <?php $invoice_types = array_filter(explode(',',get_config($dbc, 'invoice_types')));
    if(!empty($invoice_types)) { ?>
        <div class="form-group">
            <label class="col-sm-3 control-label">Invoice Type:</label>
            <div class="col-sm-9">
                <select name="type" class="chosen-select-deselect form-control">
                    <option></option>
                    <?php foreach($invoice_types as $invoice_type_dropdown) {
                        echo '<option value="'.config_safe_str($invoice_type_dropdown).'" '.($invoice_type == config_safe_str($invoice_type_dropdown) ? 'selected' : '').'>'.$invoice_type_dropdown.'</option>';

                    } ?>
                </select>
            </div>
        </div>
    <?php } ?>

    <?php if($_GET['inv_mode'] == 'adjust') { ?>
        <input type="hidden" name="invoice_mode" value="Adjustment">
    <?php } ?>
      
    <div class="form-group" <?= (in_array('invoice_date',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">Invoice Date:</label>
        <div class="col-sm-9">
            <input type="text" readonly value="<?= date('Y-m-d'); ?>" class="form-control">
        </div>
    </div>

    <?php if(!empty($_GET['invoiceid'])) { ?>
      <div class="form-group" <?= (in_array('customer',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>:</label>
        <div class="col-sm-9">
            <?php echo $patient; ?>
        </div>
      </div>
    <?php } else { ?>
        <div class="form-group" <?= (in_array('invoice_type',$field_config) ? '' : 'style="display:none;"') ?>>
            <label for="site_name" class="col-sm-3 control-label">Type:</label>
            <div class="col-sm-9">
              <div class="radio">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="All patients who have a profile in the software."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                <label class="pad-right">
                <input type="radio" checked name="invoice_type" class="patient_type" value="Patient"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?></label>
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Non-<?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>s making a purchase."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                <label class="pad-right"><input type="radio" name="invoice_type" value="Non Patient" class="patient_type">Non <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?></label>
              </div>
            </div>
        </div>

      <div class="form-group non_patient_fields">
        <label for="site_name" class="col-sm-3 control-label">First Name<span class="hp-red">*</span>:</label>
        <div class="col-sm-9">
          <input name="first_name" type="text" class="form-control" />
        </div>
      </div>
      <div class="form-group non_patient_fields">
        <label for="site_name" class="col-sm-3 control-label">Last Name<span class="hp-red">*</span>:</label>
        <div class="col-sm-9">
          <input name="last_name" type="text" class="form-control" />
        </div>
      </div>
      <div class="form-group non_patient_fields">
        <label for="site_name" class="col-sm-3 control-label">Email:</label>
        <div class="col-sm-9">
          <input name="email" type="text" class="form-control" />
        </div>
      </div>

        <div class="form-group patient patient_type_fields" <?= (in_array('customer',$field_config) ? '' : 'style="display:none;"') ?>>
            <label for="site_name" class="col-sm-3 control-label">
            <span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select a Customer's name."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
            <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?><span class="hp-red">*</span>:</label>
            <div class="col-sm-8">
                <select id="patientid" data-placeholder="Select <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>..." name="patientid" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <option value="NEW">Add New <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?></option>
                    <?php
                        $query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category IN ('".implode("','",$purchaser_config)."') AND status>0 AND deleted=0"));
                        foreach($query as $contact) {
                            $selected = '';
                            $selected = $contact['contactid'] == $_GET['contactid'] ? 'selected="selected"' : '';
                            echo "<option ".$selected." value='".$contact['contactid']."'>".$contact['full_name'].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="col-sm-1">
                <img src="../img/person.PNG" class="inline-img cursor-hand pull-right" onclick="view_profile(this);">
            </div>
        </div>
    <?php } ?>

    <div class="form-group patient  <?= (in_array('reference',$field_config) ? 'reference' : '" style="display:none;') ?>">
        <label for="site_name" class="col-sm-3 control-label">Reference:</label>
        <div class="col-sm-9"><input type="text" name="reference" class="form-control" /></div>
    </div>

    <div class="form-group patient  <?= (in_array('contract',$field_config) ? 'reference' : '" style="display:none;') ?>">
        <label for="site_name" class="col-sm-3 control-label">Contract:</label>
        <div class="col-sm-9"><input type="text" name="contract" class="form-control" value="<?= $get_invoice['contract'] ?>" /></div>
    </div>

    <div class="form-group patient  <?= (in_array('po_num',$field_config) ? 'reference' : '" style="display:none;') ?>">
        <label for="site_name" class="col-sm-3 control-label">PO #:</label>
        <div class="col-sm-9"><input type="text" name="po_num" class="form-control" value="<?= $get_invoice['po_num'] ?>" /></div>
    </div>

    <div class="form-group patient  <?= (in_array('area',$field_config) ? 'reference' : '" style="display:none;') ?>">
        <label for="site_name" class="col-sm-3 control-label">Area:</label>
        <div class="col-sm-9"><input type="text" name="area" class="form-control" value="<?= $get_invoice['area'] ?>" /></div>
    </div>

    <div class="form-group patient  <?= (in_array('injury',$field_config) ? 'patient_type_fields' : '" style="display:none;') ?>">
        <?php if($_GET['inv_mode'] == 'adjust') { ?>
            <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Invoiced Injury:</label>
                <div class="col-sm-9 control-label" style="text-align:left;"><?= $injury ?></div>
            </div>
        <?php } ?>
        <div class="adjust_block">
            <label for="site_name" class="col-sm-3 control-label">
            <span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the injury."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
            Injury<span class="hp-red">*</span>:</label>
            <div class="col-sm-9">
                <select id="injuryid" data-placeholder="Select an Injury..." name="injuryid" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <?php $pid = $_GET['contactid'];
                    $query = mysqli_query($dbc,"SELECT contactid, injuryid, injury_name, injury_date, injury_type, treatment_plan FROM patient_injury WHERE contactid='$pid' AND discharge_date IS NULL AND deleted=0");
                    while($row = mysqli_fetch_array($query)) {
                        $injuryid = $row['injuryid'];
                        $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_injury FROM booking WHERE injuryid='$injuryid'"));

                        $treatment_plan = get_all_from_injury($dbc, $injuryid, 'treatment_plan');
                        $final_treatment_done = '';
                        if($treatment_plan != '') {
                            $final_treatment_done = ' : '.($total_injury['total_injury']+1).'/'.$treatment_plan;
                        }

                        echo "<option ".($get_invoice['injuryid'] == $injuryid ? 'selected' : '')." value='". $row['injuryid']."'>".$row['injury_type'].' : '.$row['injury_name']. ' : '.$row['injury_date'].$final_treatment_done.'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group treatment_plan" <?= (in_array('treatment',$field_config) ? '' : 'style="display:none;"') ?>>
        <?php if($_GET['inv_mode'] == 'adjust') { ?>
            <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Invoiced Treatment Plan:</label>
                <div class="col-sm-9 control-label" style="text-align:left;"><?= $treatment_plan ?></div>
            </div>
        <?php } ?>
        <div class="adjust_block">
            <label for="site_name" class="col-sm-3 control-label">Treatment Plan:</label>
            <div class="col-sm-9">
              <select name="treatment_plan" data-placeholder="Select a Plan..." class="chosen-select-deselect form-control" width="380">
                    <option value=''></option>
                    <option <?php if ($treatment_plan == "3") { echo " selected"; } ?> value = '3'>3</option>
                    <option <?php if ($treatment_plan == "4") { echo " selected"; } ?> value = '4'>4</option>
                    <option <?php if ($treatment_plan == "5") { echo " selected"; } ?> value = '5'>5</option>
                    <option <?php if ($treatment_plan == "6") { echo " selected"; } ?> value = '6'>6</option>
                    <option <?php if ($treatment_plan == "7") { echo " selected"; } ?> value = '7'>7</option>
                    <option <?php if ($treatment_plan == "12") { echo " selected"; } ?>  value = '12'>12</option>
                    <option <?php if ($treatment_plan == "21") { echo " selected"; } ?> value = '21'>21</option>
              </select>
            </div>
        </div>
    </div>

    <?php $staff_label = 'Staff';
    $staff_list = [];
    if(in_array('vendor',$field_config)) {
        $staff_label = 'Vendor';
        $staff_list = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name, name FROM contacts WHERE category IN ('".implode("','",explode(',',get_config($dbc, 'vendors_tabs')))."') AND deleted=0 AND (`name` NOT LIKE '' OR `first_name` NOT LIKE '' OR `last_name` NOT LIKE '')"));
    } else {
        $staff_list = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0"));
    } ?>
    <div class="form-group" <?= (in_array('staff',$field_config) || in_array('vendor',$field_config) ? '' : 'style="display:none;"') ?>>
        <?php if($_GET['inv_mode'] == 'adjust') { ?>
            <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Invoiced <?= $staff_label ?>:</label>
                <div class="col-sm-9 control-label" style="text-align:left;"><?= $staff ?></div>
            </div>
        <?php } ?>
        <div class="adjust_block">
            <label for="site_name" class="col-sm-3 control-label">
            <span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the <?= $staff_label ?> providing services."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
            <?= $staff_label ?>:</label>
            <div class="col-sm-8">
                <select id="therapistsid" data-placeholder="Select <?= $staff_label ?>..." name="therapistsid" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <?php foreach($staff_list as $row) {
                        echo "<option ".($get_invoice['therapistsid'] == $row['contactid'] || (empty($get_invoice['therapistsid']) && $row['contactid'] == $_SESSION['contactid']) ? 'selected' : '')." value='". $row['contactid']."'>".$row['full_name'].'</option>';
                    } ?>
                </select>
            </div>
            <div class="col-sm-1">
                <img src="../img/person.PNG" class="inline-img cursor-hand pull-right" onclick="view_profile(this);">
            </div>
        </div>
    </div>

    <div class="form-group" <?= (in_array('appt_type',$field_config) ? '' : 'style="display:none;"') ?>>
        <?php if($_GET['inv_mode'] == 'adjust') { ?>
            <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Invoiced Appointment Type:</label>
                <div class="col-sm-9 control-label" style="text-align:left;"><?php echo $app_type; ?></div>
            </div>
        <?php } ?>
        <div class="adjust_block">
            <label for="site_name" class="col-sm-3 control-label">Appointment Type:</label>
            <div class="col-sm-9">
                <select name="app_type" class="chosen-select-deselect"><option></option>
                    <?php $appointment_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                    foreach ($appointment_types as $appointment_type) {
                        echo '<option '.($type == $appointment_type['id'] ? 'selected' : '').' value="'.$appointment_type['id'].'">'.$appointment_type['name'].'</option>';
                    } ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group" <?= (in_array('service_date',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">Service Date:</label>
        <div class="col-sm-9">
            <input type="text" name="service_date" <?= ($_GET['inv_mode'] == 'adjust') ? 'readonly' : '' ?> class="form-control <?= ($_GET['inv_mode'] == 'adjust') ? '' : 'datepicker' ?>" value="<?= $service_date ?>">
        </div>
    </div>

    <div class="form-group" <?= (in_array('pricing',$field_config) ? '' : 'style="display:none;"') ?>>
        <?php if($_GET['inv_mode'] == 'adjust') { ?>
            <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Invoiced Product Pricing:</label>
                <div class="col-sm-9">
                    <?= ucwords(str_replace('_',' ',$pricing)) ?>
					<span class="adjust_block"><img src="../img/icons/ROOK-add-icon.png" class="no-toggle cursor-hand inline-img" title="Add Pricing for Adjustment" onclick="$('.adjust_block_pricing').toggle();" /></span>
                </div>
				
            </div>
        <?php } ?>
        <div class="adjust_block_pricing">
            <label for="site_name" class="col-sm-3 control-label">Product Pricing:</label>
            <div class="col-sm-8">
                <select name="pricing" data-placeholder="Select Pricing" class="chosen-select-deselect"><option></option>
                    <?php if(in_array('price_admin', $field_config)) { ?><option <?= ($pricing == 'admin_price' ? 'selected' : '') ?> value="admin_price">Admin Price</option><?php } ?>
                    <?php if(in_array('price_client', $field_config)) { ?><option <?= ($pricing == 'client_price' ? 'selected' : '') ?> value="client_price">Client Price</option><?php } ?>
                    <?php if(in_array('price_commercial', $field_config)) { ?><option <?= ($pricing == 'commercial_price' ? 'selected' : '') ?> value="commercial_price">Commercial Price</option><?php } ?>
                    <?php if(in_array('price_distributor', $field_config)) { ?><option <?= ($pricing == 'distributor_price' ? 'selected' : '') ?> value="distributor_price">Distributor Price</option><?php } ?>
                    <?php if(in_array('price_retail', $field_config)) { ?><option <?= ($pricing == 'final_retail_price' || $pricing == '' ? 'selected' : '') ?> value="final_retail_price">Final Retail Price</option><?php } ?>
                    <?php if(in_array('price_preferred', $field_config)) { ?><option <?= ($pricing == 'preferred_price' ? 'selected' : '') ?> value="preferred_price">Preferred Price</option><?php } ?>
                    <?php if(in_array('price_po', $field_config)) { ?><option <?= ($pricing == 'purchase_order_price' ? 'selected' : '') ?> value="purchase_order_price">Purchase Order Price</option><?php } ?>
                    <?php if(in_array('price_sales', $field_config)) { ?><option <?= ($pricing == 'sales_order_price' ? 'selected' : '') ?> value="sales_order_price"><?= SALES_ORDER_NOUN ?> Price</option><?php } ?>
                    <?php if(in_array('price_web', $field_config)) { ?><option <?= ($pricing == 'web_price' ? 'selected' : '') ?> value="web_price">Web Price</option><?php } ?>
                    <?php if(in_array('price_wholesale', $field_config)) { ?><option <?= ($pricing == 'wholesale_price' ? 'selected' : '') ?> value="wholesale_price">Wholesale Price</option><?php } ?>
                </select>
            </div>
			<div class="col-sm-1">
				<label>
					<input type="checkbox" checked="checked" name="pricing_change" onchange="if (this.checked) { $('.price_edit').hide(); $('.pricing-div').hide(); } else { $('.price_edit').show(); }" />
					<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Use this pricing for all Inventory." class="cursor-hand" style="display:inline-block; float:right; margin-top:1px;"><img src="../img/info.png" width="20" /></a></span>
				</label>
			</div>
        </div>
    </div>

    <div class="form-group" <?= (in_array('pay_mode',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="site_name" class="col-sm-3 control-label">
        <span class="popover-examples list-inline">
            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select payment method (you must select one in order to move on)."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
        </span>
        Payment Method:</label>
        <div class="col-sm-9">
            <select data-placeholder="Select a Type..." name="paid" id="paid_status" class="chosen-select-deselect form-control" width="480">
                <option value=""></option>
                <!--<option <?php if ($paid=='Saved') echo 'selected="selected"';?>  value="Saved">Save Invoice</option>-->
                <option <?php if ($paid=='Yes') echo 'selected="selected"';?>  value="Yes">Patient Invoice : Patient is paying full amount on checkout.</option>
                <option <?php if ($paid=='Waiting on Insurer') echo 'selected="selected"';?> value="Waiting on Insurer">Waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> : Clinic is waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> to pay full amount.</option>
                <option <?php if ($paid=='No') echo 'selected="selected"';?>  value="No">Partially Paid : The invoice is being paid partially by patient and partially by <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?>.</option>
                <option <?php if ($paid=='On Account') echo 'selected="selected"';?> value="On Account">A/R On Account : Patient will pay invoice in future. Must choose Payment Type as Apply A/R to Account.</option>
                <option <?php if ($paid=='Credit On Account') echo 'selected="selected"';?> value="Credit On Account">Credit On Account : Patient is appyling credit to profile.</option>
            </select>
        </div>
    </div>
</div>