<!-- Add/Edit Sales Order. Included in order.php -->

<input type="hidden" id="sotid" name="sotid" value="<?= $sotid ?>">
<input type="hidden" id="so_type" name="so_type" value="<?= $so_type ?>">

<div class="standard-body main-screen-white" style="padding-left: 0; padding-right: 0; border: none;">
    <div class="standard-body-title">
        <h3><?= ( !empty($_GET['sotid']) ) ? 'Edit' : 'Add'; ?> <?= SALES_ORDER_NOUN ?></h3>
    </div>

    <div class="standard-body-content"><?php

        include('details_customers.php');
        echo "<hr>";
        if (strpos($value_config, ',Sales Order Template,') !== FALSE) {
            $load_type = 'template';
            include('details_templates.php');
            echo "<hr>";
        }
        if (strpos($value_config, ',Copy Sales Order,') !== FALSE) {
            $load_type = 'sales_order';
            include('details_templates.php');
            echo "<hr>";
        }
        if (strpos($value_config, ',Sales Order Name,') !== FALSE) {
            include('details_sales_order_name.php');
            echo "<hr>";
        }
        if (strpos($value_config, ',Primary Staff,') !== FALSE || strpos($value_config, ',Assign Staff,')) {
            include('details_staff_information.php');
            echo "<hr>";
        }
        include('details_next_action.php');
        echo "<hr>";
        if (strpos($value_config, ',Logo,') !== FALSE) {
            include('details_logo.php');
    		echo "<hr>";
        }
        if (strpos($value_config, ',Custom Designs,') !== FALSE) {
            include('details_design.php');
            echo "<hr>";
        }
        include('details_category_functions.php');
        foreach ($cat_config as $contact_cat) {
            include('details_category_roster.php');
            echo "<hr>";
            include('details_category_order.php');
            echo "<hr>";
        }
        if(empty($cat_config)) {
            $no_cat = true;
            // include('details_category_order.php');
            include('details_order.php');
            echo "<hr>";
        }
        // include('details_security.php');
        // echo "<hr>";
        include('details_order_details.php');
    	echo "<hr>";
        if (strpos($value_config, ',Notes,') !== FALSE) {
            include('details_notes.php');
    		echo "<hr>";
        }
        include('details_history.php'); ?>
    </div>
</div><!-- .main-screen-white -->