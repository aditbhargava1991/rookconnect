<!-- Sales Lead Details / Add/Edit Sales Lead -->
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
    include_once('../Sales/config.php'); ?>
    <input type="hidden" id="salesid" name="salesid" value="<?= $salesid ?>" />

    <div class="main-screen-white standard-body" style="padding-left: 0; padding-right: 0; border: none;">
        <div class="standard-body-title" style="<?= empty($flag_colour) ? '' : 'background-color:#'.$flag_colour.';' ?>position:absolute;z-index:1;width:100%;" data-id="<?= $salesid ?>">
            <h3><?= ( !empty($salesid) ) ? 'Edit' : 'Add'; ?> <?= SALES_NOUN ?> #<?= $salesid ?>
                <div class="pull-right"><?php include('quick_actions.php'); ?></div>
                <span class="flag-label" data-colour="<?= $flag_colour ?>"><?= $flag_label ?></span>
            </h3>
        </div>

        <div class="standard-body-content"><?php

            if (strpos($value_config, ',Staff Information,') !== false) {
				echo "<div>";
                include('details_staff_info.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',Next Action,') !== false) {
				echo "<div>";
                include('details_next_action.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',Lead Information,') !== false) {
				echo "<div>";
                include('details_lead_info.php');
				echo "</div><hr>";
                echo "<div>";
                include('details_contacts.php');
                echo "</div>";
                echo "<div>";
                include('details_business.php');
                echo "</div><hr>";
            }
            if (strpos($value_config, ',Service,') !== false) {
				echo "<div>";
                include('details_services.php');
                echo "</div><hr>";
            }
            if (strpos($value_config, ',Products,') !== false) {
				echo "<div>";
                include('details_products.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',Reference Documents,') !== false) {
				echo "<div>";
                include('details_ref_docs.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',Marketing Material,') !== false) {
				echo "<div>";
                include('details_marketing.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',Information Gathering,') !== false) {
				echo "<div>";
                include('details_info_gathering.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',Estimate,') !== false) {
				echo "<div>";
                include('details_estimate.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',Lead Notes,') !== false) {
				echo "<div>";
                include('details_lead_notes.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',Tasks,') !== false) {
				echo "<div>";
                include('details_tasks.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',Deliverable,') !== false) {
				echo "<div>";
                include('details_deliverable.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',Time,') !== false) {
				echo "<div>";
                include('details_time.php');
				echo "</div><hr>";
            }
            if (strpos($value_config, ',History,') !== false) {
				echo "<div>";
                include('details_history.php');
                echo "</div>";
            } ?>

            <div class="pull-right gap-top gap-right gap-bottom">
                <a href="index.php" class=""><img class="no-toggle theme-color-icon" src="<?php echo WEBSITE_URL;?>/img/icons/cancel.png" alt="Save" width="36" title="" data-original-title="Save"></a>
                <button type="submit" name="add_sales" value="save_only" style="border: none;background: none;"><img class="no-toggle theme-color-icon" src="<?php echo WEBSITE_URL;?>/img/icons/save.png" alt="Save" width="36" title="" data-original-title="Save"></button>
                <button type="submit" name="add_sales" value="Submit" class="btn brand-btn">Submit</button>
            </div>
        </div><!-- .preview-block-container -->
    </div><!-- .main-screen-white -->

    <div class="clearfix"></div>

</form>
