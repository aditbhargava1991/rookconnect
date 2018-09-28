<?php include_once ('../include.php');
include_once('../Dispatch/config.php');
checkAuthorised('dispatch');
?>
<script type="text/javascript" src="../Dispatch/dashboard.js"></script>

<div class="scale-to-fill has-main-screen" style="padding: 0;">
    <div class="main-screen standard-body form-horizontal">

        <div class="standard-body-content">
            <div class="menu-bar" style="position: fixed; right: 20px; z-index: 1; top: 122px; display: block;">
                <div class="menu-content" style="display: none;"></div>
                <img src="../img/icons/ROOK-3dot-icon.png" width="30" class="no-toggle cursor-hand pull-right menu_button offset-right-10 theme-color-icon" title="" data-original-title="Search Filters" onclick="show_search_fields();">
            </div>
            <div class="search-fields" style="padding: 1em; display: none;">
                <h4>Search Filters</h4>
                <?php $search_count = 1; ?>
                <div class="col-sm-6">
                    <label class="col-sm-4">Date:</label>
                    <div class="col-sm-8">
                        <input type="text" name="search_date" value="<?= date('Y-m-d') ?>" class="form-control datepicker">
                    </div>
                </div>
                <?php if(in_array('region',$search_fields)) { ?>
                    <div class="col-sm-6">
                        <label class="col-sm-4">Region:</label>
                        <div class="col-sm-8">
                            <select name="search_region" class="chosen-select-deselect">
                                <option></option>
                                <?php foreach($allowed_regions as $region) {
                                    echo '<option value="'.$region.'">'.$region.'</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <?php $search_count++;
                    if($search_count % 2 == 0) {
                        echo '<div class="clearfix"></div>';
                    }
                } ?>
                <?php if(in_array('location',$search_fields)) { ?>
                    <div class="col-sm-6">
                        <label class="col-sm-4">Location:</label>
                        <div class="col-sm-8">
                            <select name="search_location" class="chosen-select-deselect">
                                <option></option>
                                <?php foreach($allowed_locations as $location) {
                                    echo '<option value="'.$location.'">'.$location.'</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <?php $search_count++;
                    if($search_count % 2 == 0) {
                        echo '<div class="clearfix"></div>';
                    }
                } ?>
                <?php if(in_array('region',$search_fields)) { ?>
                    <div class="col-sm-6">
                        <label class="col-sm-4">Classification:</label>
                        <div class="col-sm-8">
                            <select name="search_classification" class="chosen-select-deselect">
                                <option></option>
                                <?php foreach($allowed_classifications as $classification) {
                                    echo '<option value="'.$classification.'">'.$classification.'</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <?php $search_count++;
                    if($search_count % 2 == 0) {
                        echo '<div class="clearfix"></div>';
                    }
                } ?>
                <?php if(in_array('business',$search_fields) && !$is_customer) { ?>
                    <div class="col-sm-6">
                        <label class="col-sm-4"><?= BUSINESS_CAT ?>:</label>
                        <div class="col-sm-8">
                            <select name="search_business" class="chosen-select-deselect">
                                <option></option>
                                <?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `name`, `region`, `con_locations`, `classification` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `deleted`=0")) as $row) { ?>
                                    <option value="<?= $row['contactid'] ?>"><?= $row['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php $search_count++;
                    if($search_count % 2 == 0) {
                        echo '<div class="clearfix"></div>';
                    }
                } ?>
                <div class="clearfix"></div>
            </div>
            <div class="dispatch-body">
                <div class="dispatch-equipment-summary" style="padding: 1em;"></div>
                <div class="dashboard-equipment-buttons-group" style="padding: 1em;">
                    <h4 style="margin: 0; padding: 0.5em 0;"><?= count($equipment_categories) == 1 ? $equipment_categories[0] : 'Equipment' ?></h4>
                    <div class="dispatch-equipment-buttons"></div>
                    <label class="form-checkbox"><input type="checkbox" onclick="select_all_buttons(this);" checked> Select All</label>
                </div>
                <div class="double-scroller"><div></div></div>
                <div class="dispatch-equipment-list"></div>
            </div>
        </div>
    </div>
</div>