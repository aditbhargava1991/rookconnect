<!-- Next Actions -->
<?php include_once('../Sales/config.php'); ?>
<?php $get_config_won_status = get_config($dbc, 'lead_status_won');
$get_config_lost_status = get_config($dbc, 'lead_status_lost');
$get_config_retained = get_config($dbc, 'lead_status_retained');
$lead_convert_to = get_config($dbc, 'lead_convert_to'); ?>
<div class="accordion-block-details padded" id="nextaction">
    <div class="accordion-block-details-heading"><h4>Next Actions</h4></div>
    
    <div class="row set-row-height">
        <div class="col-xs-12 col-sm-4 gap-md-left-15">Next Action:</div>
        <div class="col-xs-12 col-sm-5">
            <select data-placeholder="Choose a Next Action..." data-table="sales" name="next_action" class="chosen-select-deselect form-control">
                <option value=""></option><?php
				$tabs = get_config($dbc, 'sales_next_action');
				
				foreach ( explode(',', $tabs) as $cat_tab ) {
					$selected = ( $next_action == $cat_tab ) ? 'selected="selected"' : '';
					echo '<option ' . $selected . ' value="' . $cat_tab . '">' . $cat_tab . '</option>';
				} ?>
            </select>
        </div>
        <div class="clearfix"></div>
    </div>
    
    <div class="row set-row-height">
        <div class="col-xs-12 col-sm-4 gap-md-left-15">New Reminder:</div>
        <div class="col-xs-12 col-sm-5"><input data-table="sales" name="new_reminder" value="<?= $new_reminder; ?>" type="text" class="datepicker form-control" /></div>
        <div class="clearfix"></div>
    </div>
    
    <!-- Lead Status -->
    <div class="row set-row-height">
        <div class="col-xs-12 col-sm-4 gap-md-left-15">Lead Status:</div>
        <div class="col-xs-12 col-sm-5">
            <select data-placeholder="Choose a Status..." data-table="sales" name="status" class="chosen-select-deselect form-control">
                <!-- <option value=""></option> --><?php
                $tabs = get_config ( $dbc, 'sales_lead_status' );
                foreach ( explode ( ',', $tabs ) as $cat_tab ) {
                    if(empty($status)) {
                        $status = !empty(get_config($dbc, 'lead_status_default')) ? get_config($dbc, 'lead_status_default') : $cat_tab;
                    }
                    $selected = ( $status == $cat_tab ) ? 'selected="selected"' : '';
                    echo '<option '. $selected .' value="'. $cat_tab .'">'. $cat_tab .'</option>';
                } ?>
                <option <?= ($status == 'Customers' ? 'selected ' : ''); ?>value="Customers"><?= $lead_convert_to ?></option>
            </select>
        </div>
        <div class="clearfix"></div>
    </div>
    
    <?php if(!empty($get_config_won_status) && !empty($lead_convert_to)) { ?>
        <a href="convert_sales_lead.php?leadid=<?= $salesid ?>&won_lead=true" class="btn brand-btn">Mark as <?= $get_config_won_status ?></a>
    <?php } ?>
    <?php if(!empty($get_config_retained) && !empty($lead_convert_to)) { ?>
        <a href="convert_sales_lead.php?leadid=<?= $salesid ?>&won_lead=keep" class="btn brand-btn">Add to  <?= $lead_convert_to ?></a>
    <?php } ?>
    <?php if(!empty($get_config_lost_status) && !empty($lead_convert_to)) { ?>
        <a href="convert_sales_lead.php?leadid=<?= $salesid ?>&won_lead=lost" class="btn brand-btn">Mark as <?= $get_config_lost_status ?></a>
    <?php } ?>
</div><!-- .accordion-block-details -->