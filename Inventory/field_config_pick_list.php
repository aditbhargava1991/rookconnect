<?php
if (isset($_POST['submit'])) {
    set_config($dbc, 'pick_list_filters', implode(',',$_POST['pick_list_filters']));
    set_config($dbc, 'pick_list_mandatory', implode(',',$_POST['pick_list_mandatory']));
}
?>

<div class="gap-top">
    <div class="form-group">
        <label class="col-sm-4 control-label">Pick List Inventory Filters:</label>
        <div class="col-sm-8">
			<?php $filters = explode(',',get_config($dbc, 'pick_list_filters')); ?>
            <label class="form-checkbox"><input name="pick_list_filters[]" value="category" <?= in_array('category',$filters) ? 'checked' : '' ?> type="checkbox"> Tab</label>
            <label class="form-checkbox"><input name="pick_list_filters[]" value="ticket_po" <?= in_array('ticket_po',$filters) ? 'checked' : '' ?> type="checkbox"> Purchase Order # (From <?= TICKET_NOUN ?>)</label>
            <label class="form-checkbox"><input name="pick_list_filters[]" value="po_line" <?= in_array('po_line',$filters) ? 'checked' : '' ?> type="checkbox"> PO Line Item # (From <?= TICKET_NOUN ?>)</label>
            <label class="form-checkbox"><input name="pick_list_filters[]" value="ticket" <?= in_array('ticket',$filters) ? 'checked' : '' ?> type="checkbox"> <?= TICKET_NOUN ?> Label</label>
            <label class="form-checkbox"><input name="pick_list_filters[]" value="ticket_customer_order" <?= in_array('ticket_customer_order',$filters) ? 'checked' : '' ?> type="checkbox"> Customer Order # (From <?= TICKET_NOUN ?>)</label>
            <label class="form-checkbox"><input name="pick_list_filters[]" value="detail_customer_order" <?= in_array('detail_customer_order',$filters) ? 'checked' : '' ?> type="checkbox"> Customer Order # (From <?= TICKET_NOUN ?> Line Item)</label>
            <label class="form-checkbox"><input name="pick_list_filters[]" value="pallet" <?= in_array('pallet',$filters) ? 'checked' : '' ?> type="checkbox"> Pallet #</label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Pick List Inventory Options:</label>
        <div class="col-sm-8">
			<?php $filters = explode(',',get_config($dbc, 'pick_list_filters')); ?>
            <label class="form-checkbox"><input name="pick_list_filters[]" value="display_all" <?= in_array('display_all',$filters) ? 'checked' : '' ?> type="checkbox"> Display All Button</label>
            <label class="form-checkbox"><input name="pick_list_filters[]" value="fill_max" <?= in_array('fill_max',$filters) ? 'checked' : '' ?> type="checkbox"> Fill Max Button</label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Pick List Mandatory Fields:</label>
        <div class="col-sm-8">
            <?php $mandatory = explode(',',get_config($dbc, 'pick_list_mandatory')); ?>
            <label class="form-checkbox"><input name="pick_list_mandatory[]" value="list_name" <?= in_array('list_name',$mandatory) ? 'checked' : '' ?> type="checkbox"> List Name</label>
            <label class="form-checkbox"><input name="pick_list_mandatory[]" value="business" <?= in_array('business',$mandatory) ? 'checked' : '' ?> type="checkbox"> Business</label>
        </div>
    </div>
</div>