<?php include_once ('../include.php');
include_once('../Dispatch/dashboard_functions.php');
include_once('../Dispatch/config.php');
checkAuthorised('dispatch');
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="../Dispatch/dashboard.js"></script>

<div id="camera_hover" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>
<div id="signature_hover" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>

<div id="accordion" class="tile-sidebar sidebar sidebar standard-collapsible">
    <ul>
        <?php if(in_array('region',$search_fields) && !$is_customer) { ?>
            <li class="sidebar-higher-level"><a class="cursor-hand collapsed" data-parent="#accordion" data-toggle="collapse" data-target="#collapse_region">Region<span class="arrow"></span></a>
                <ul class="collapse" id="collapse_region" style="overflow: hidden;">
                    <?php foreach($allowed_regions as $region) { ?>
                        <a href="" data-region="<?= $region ?>" data-activevalue="<?= $region ?>" onclick="filter_sidebar(this); return false;"><li><?= $region ?></li></a>
                    <?php } ?>
                </ul>
            </li>
            <li class="active_li sidebar-higher-level" data-accordion="collapse_region" style="display: none; padding-top: 0;">
                <ul class="collapse in" data-accordion="collapse_region">
                    <?php foreach($allowed_regions as $region) { ?>
                        <a data-activevalue="<?= $region ?>" class="active_li_item" style="cursor: pointer;"><li class="active blue"><?= $region ?></li></a>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if(in_array('location',$search_fields) && !$is_customer) { ?>
            <li class="sidebar-higher-level"><a class="cursor-hand collapsed" data-parent="#accordion" data-toggle="collapse" data-target="#collapse_location">Location<span class="arrow"></span></a>
                <ul class="collapse" id="collapse_location" style="overflow: hidden;">
                    <?php foreach($allowed_locations as $location) { ?>
                        <a href="" data-location="<?= $location ?>" data-activevalue="<?= $location ?>" onclick="filter_sidebar(this); return false;"><li><?= $location ?></li></a>
                    <?php } ?>
                </ul>
            </li>
            <li class="active_li sidebar-higher-level" data-accordion="collapse_location" style="display: none; padding-top: 0;">
                <ul class="collapse in" data-accordion="collapse_location">
                    <?php foreach($allowed_locations as $location) { ?>
                        <a data-activevalue="<?= $location ?>" class="active_li_item" style="cursor: pointer;"><li class="active blue"><?= $location ?></li></a>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if(in_array('classification',$search_fields) && !$is_customer) { ?>
            <li class="sidebar-higher-level"><a class="cursor-hand collapsed" data-parent="#accordion" data-toggle="collapse" data-target="#collapse_classification">Classification<span class="arrow"></span></a>
                <ul class="collapse" id="collapse_classification" style="overflow: hidden;">
                    <?php foreach($allowed_classifications as $i => $classification) { ?>
                        <a href="" data-region='<?= json_encode($classification_regions[$i]) ?>' data-classification="<?= $classification ?>" data-activevalue="<?= $classification ?>" onclick="filter_sidebar(this); return false;"><li><?= $classification ?></li></a>
                    <?php } ?>
                </ul>
            </li>
            <li class="active_li sidebar-higher-level" data-accordion="collapse_classification" style="display: none; padding-top: 0;">
                <ul class="collapse in" data-accordion="collapse_classification">
                    <?php foreach($allowed_classifications as $classification) { ?>
                        <a data-activevalue="<?= $classification ?>" class="active_li_item" style="cursor: pointer;"><li class="active blue"><?= $classification ?></li></a>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <li class="sidebar-higher-level"><a class="cursor-hand" data-parent="#accordion" data-toggle="collapse" data-target="#collapse_equipment"><?= $equipment_label ?><span class="arrow"></span></a>
            <ul class="collapse in dispatch-equipment-buttons" id="collapse_equipment" style="overflow: hidden;"></ul>
        </li>
        <li class="equip_active_li active_li sidebar-higher-level" data-accordion="collapse_equipment" style="display: none; padding-top: 0;">
            <ul class="collapse in" data-accordion="collapse_equipment"></ul>
        </li>
    </ul>
</div>

<div class="scale-to-fill has-main-screen" style="padding: 0;">
    <div class="main-screen standard-body form-horizontal">

        <div class="standard-body-title">
            <h3>Dispatch Schedule - <?= !empty($_GET['date']) ? $_GET['date'] : date('Y-m-d') ?></h3>
        </div>

        <div class="standard-body-content">
            <div class="menu-bar" style="position: fixed; right: 20px; z-index: 1; top: 125px; display: block;">
                <div class="menu-content" style="display: none;"></div>
                <img src="../img/icons/ROOK-3dot-icon.png" width="30" class="no-toggle cursor-hand pull-right menu_button offset-right-10 theme-color-icon" title="" data-original-title="Search <?= TICKET_TILE ?>" onclick="show_search_fields();">
            </div>
            <div class="search-fields" style="padding: 1em; display: none;">
                <h4>Search Filters</h4>
                <?php $search_count = 1; ?>
                <div class="col-sm-6">
                    <label class="col-sm-4">Date:</label>
                    <div class="col-sm-8">
                        <input type="text" name="search_date" value="<?= !empty($_GET['date']) ? $_GET['date'] : date('Y-m-d') ?>" class="form-control datepicker">
                    </div>
                </div>
                <?php if(in_array('region',$search_fields) && !$is_customer) { ?>
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
                <?php if(in_array('location',$search_fields) && !$is_customer) { ?>
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
                <?php if(in_array('classification',$search_fields) && !$is_customer) { ?>
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
                <div class="dispatch-summary" style="padding: 1em; display: none;">
                    <div class="dispatch-equipment-summary-title"></div>
                    <div class="dispatch-equipment-summary"></div>
                </div>
                <div class="clearfix"></div>
                <!-- <div class="dashboard-equipment-buttons-group" style="padding: 1em;">
                    <h4 style="margin: 0; padding: 0.5em 0;"><?= count($equipment_categories) == 1 ? $equipment_categories[0] : 'Equipment' ?></h4>
                    <div class="dispatch-equipment-buttons"></div>
                    <label class="form-checkbox"><input type="checkbox" onclick="select_all_buttons(this);"> Select All</label>
                </div> -->
                <div class="double-scroller"><div></div></div>
                <div class="dispatch-equipment-list"></div>
            </div>
        </div>
    </div>
</div>