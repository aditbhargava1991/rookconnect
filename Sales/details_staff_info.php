<!-- Staff Information -->
<?php include_once('../Sales/config.php');
$limit_staff_cat = array_filter(explode(',',get_config($dbc, 'sales_limit_staff_cat')));
$cat_query = '';
if(!empty($limit_staff_cat)) {
    $cat_query = [];
    foreach($limit_staff_cat as $staff_cat) {
        $cat_query[] = "CONCAT(',',`staff_category`,',') LIKE ('%,".$staff_cat.",%')";
    }
    $cat_query = " AND (".implode(' OR ', $cat_query).")";
} ?>
<div class="accordion-block-details padded" id="staffinfo">
    <div class="accordion-block-details-heading"><h4>Staff Information</h4></div>
    <div class="row set-row-height">
        <div class="col-xs-12 col-sm-4 gap-md-left-15">Primary Staff:</div>
        <div class="col-xs-12 col-sm-5">
            <select data-placeholder="Select a Staff Member..." data-table="sales" name="primary_staff" class="chosen-select-deselect form-control">
                <option value=""></option><?php
                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `sales_lead_is_active`=1 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0".$cat_query), MYSQLI_ASSOC));
                foreach($query as $id) { ?>
                    <option <?= ($id==$primary_staff) ? 'selected' : ''; ?> value="<?= $id; ?>"><?= get_contact($dbc, $id); ?></option><?php
                } ?>
            </select>
        </div>
            <div class="col-sm-1">
                <img class="inline-img cursor-hand pull-left no-toggle" title="View this staff's profile" src="../img/person.PNG" onclick="load_profile(this,'Staff/staff_edit.php?view_only=id_card&contactid=');">
            </div>
        <div class="clearfix"></div>
    </div>
    <?php foreach(explode(',',$share_lead) as $share_lead_id) { ?>
        <div class="row set-row-height">
            <div class="col-xs-12 col-sm-4 gap-md-left-15">Share Lead:</div>
            <div class="col-xs-12 col-sm-5">
                <select data-placeholder="Select a Staff Member..." data-table="sales" data-concat="," name="share_lead" class="chosen-select-deselect form-control">
                    <option value=""></option><?php
                    $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `sales_lead_is_active`=1 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0".$cat_query), MYSQLI_ASSOC));
                    foreach($query as $id) { ?>
                        <option <?= $share_lead_id == $id ? 'selected' : ''; ?> value="<?= $id; ?>"><?= get_contact($dbc, $id); ?></option><?php
                    } ?>
                </select>
            </div>
            <div class="col-sm-1">
                <img class="inline-img cursor-hand pull-left no-toggle" title="View this staff's profile" src="../img/person.PNG" onclick="load_profile(this,'Staff/staff_edit.php?view_only=id_card&contactid=');">
                <img class="inline-img cursor-hand pull-right no-toggle" title="Remove this staff from sharing this Sales Lead" src="../img/remove.png" onclick="rem_row(this);">
                <img class="inline-img cursor-hand pull-right no-toggle" title="Add another staff to this Sales Lead" src="../img/icons/ROOK-add-icon.png" onclick="add_row(this);">
            </div>
            <div class="clearfix"></div>
        </div>
    <?php } ?>
    <div class="clearfix"></div>
</div>