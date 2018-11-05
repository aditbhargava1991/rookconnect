<?php include_once ('../include.php');
include_once('../Dispatch/dashboard_functions.php');
include_once('../Dispatch/config.php');
checkAuthorised('dispatch');

$ticket_statuses = explode(',', get_config($dbc, 'ticket_status'));

$dispatch_icon_legend = '';
if(in_array('camera',$dispatch_tile_ticket_card_fields)) {
    $dispatch_icon_legend .= '<label><img src="../img/icons/ROOK-camera-icon.png" style="width: 1em; height: 1em;"> Camera</img></label><br />';
}
if(in_array('signature',$dispatch_tile_ticket_card_fields)) {
    $dispatch_icon_legend .= '<label><img src="../img/icons/ROOK-signature-icon.png" style="width: 1em; height: 1em;"> Signature</img></label><br />';
}
if(in_array('star_rating',$dispatch_tile_ticket_card_fields)) {
    $dispatch_icon_legend .= '<label><img src="../img/icons/ROOK-star-icon.png" style="width: 1em; height: 1em;"> Star Rating</img></label><br />';
}
if(in_array('customer_notes_hover',$dispatch_tile_ticket_card_fields)) {
    $dispatch_icon_legend .= '<label><img src="../img/icons/ROOK-reply-icon.png" style="width: 1em; height: 1em;"> Notes</img></label><br />';
}
if(!empty($dispatch_icon_legend)) {
    $dispatch_icon_legend = '<b>Icon Legend:</b><br>'.$dispatch_icon_legend;
}

$ticket_status_legend = '<b>Status Color Code:</b><br>';
foreach ($ticket_statuses as $ticket_status) {
    $ticket_status_color_detail = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color` WHERE `status` = '$ticket_status'"))['color'];
    $ticket_status_legend .= '<label><div class="ticket-status-color" style="background-color: '.$ticket_status_color_detail.';"></div>'.$ticket_status.'</label><br />';
}
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="../Dispatch/dashboard.js"></script>
<script type="text/javascript" src="../Calendar/map_sorting.js"></script>

<input type="hidden" name="group_regions" value="<?= $group_regions ?>">
<input type="hidden" id="dispatch_auto_refresh" name="dispatch_auto_refresh" value="<?= $auto_refresh ?>">
<div id="camera_hover" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>
<div id="signature_hover" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>
<div id="star_rating_hover" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>
<div id="customer_notes_hover" class="block-button" style="position:absolute; z-index:9999; display:none;">Loading...</div>

<div id="accordion" class="tile-sidebar sidebar sidebar standard-collapsible">
    <ul>
        <?php /* $equipment_view_parent = "#accordion";
        if($summary_tab == 1) {
            $equipment_view_parent = "#collapse_equipment_view"; ?>
            <a id="summary_tab" href="" onclick="filter_sidebar(this); return false;">
                <li class="">Summary</li>
            </a>
            <a id="summary_tab" href="" onclick="filter_sidebar(this); return false;">
                <li class="">Truck Overview</li>
            </a>
            <li class="sidebar-higher-level highest_level"><a class="cursor-hand collapsed" data-parent="#accordion" data-toggle="collapse" data-target="#collapse_equipment_view"><?= $equipment_label ?> View<span class="arrow"></span></a>
                <ul class="collapse" id="collapse_equipment_view" style="overflow: hidden;">
        <?php } */ ?>

        <?php $equipment_view_parent = "#accordion";
        if($summary_tab == 1) {
            $equipment_view_parent = "#collapse_equipment_view"; ?>
            <a id="summary_tab" href="" onclick="filter_sidebar(this); return false;"><li class="active blue">Summary</li></a>

            <li class="sidebar-higher-level highest_level"><a class="cursor-hand collapsed" data-parent="#accordion" data-toggle="collapse" data-target="#collapse_equipment_view"><?= $equipment_label ?> View<span class="arrow"></span></a>
                <ul class="collapse" id="collapse_equipment_view" style="overflow: hidden;">
        <?php } ?>
        <?php if(in_array('region',$search_fields) && !$is_customer) { ?>
            <li class="sidebar-higher-level"><a class="cursor-hand collapsed" data-parent="<?= $equipment_view_parent ?>" data-toggle="collapse" data-target="#collapse_region">Region<span class="arrow"></span></a>
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
            <li class="sidebar-higher-level"><a class="cursor-hand collapsed" data-parent="<?= $equipment_view_parent ?>" data-toggle="collapse" data-target="#collapse_location">Location<span class="arrow"></span></a>
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
            <li class="sidebar-higher-level"><a class="cursor-hand collapsed" data-parent="<?= $equipment_view_parent ?>" data-toggle="collapse" data-target="#collapse_classification">Classification<span class="arrow"></span></a>
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
        <li class="sidebar-higher-level"><a class="cursor-hand <?= $summary_tab != 1 ? '' : 'collapsed' ?>" data-parent="<?= $equipment_view_parent ?>" data-toggle="collapse" data-target="#collapse_equipment"><?= $equipment_label ?><span class="arrow"></span></a>
            <ul class="collapse <?= $summary_tab != 1 ? 'in' : '' ?> dispatch-equipment-buttons" id="collapse_equipment" style="overflow: hidden;"></ul>
        </li>
        <li class="equip_active_li active_li sidebar-higher-level" data-accordion="collapse_equipment" style="display: none; padding-top: 0;">
            <ul class="collapse in" data-accordion="collapse_equipment"></ul>
        </li>
        <?php if($summary_tab == 1) { ?>
                </ul>
            </li>

        <a id="table_tab" href="" onclick="table_view(this); return false;"><li class="">Table View</li></a>

        <?php } ?>
    </ul>
</div>

<div class="scale-to-fill has-main-screen" style="padding: 0;">
    <div class="main-screen standard-body form-horizontal" style="float: left;">

        <div class="standard-body-title">
            <h3>Dispatch Schedule - <?= !empty($_GET['date']) ? $_GET['date'] : date('Y-m-d') ?></h3>
        </div>

        <div class="standard-body-content">
            <div class="menu-bar" style="position: fixed; right: 20px; z-index: 1; top: 125px; display: block;">
                <img src="../img/icons/ROOK-3dot-icon.png" width="30" class="no-toggle cursor-hand pull-right menu_button offset-right-10 theme-color-icon" title="" data-original-title="Search <?= TICKET_TILE ?>" onclick="show_search_fields();">
                <div class="block-button offset-right-10 dispatch-legend-block pull-right" style="position: relative;">
                    <div class="block-button dispatch-status-legend" style="display: none; width: 20em; position: absolute; top: 50%; right: 50%;"><?= $dispatch_icon_legend.$ticket_status_legend ?></div>
                    <img src="../img/legend-icon.png" class="dispatch-legend-img">
                </div>
                <a href="" onclick="retrieve_summary(this, 'VISIBLE'); return false;"><img class="dispatch-summary-icon inline-img pull-right btn-horizontal-collapse no-toggle gap-right visible_summary" src="../img/icons/pie-chart.png" title="" data-original-title="View Summary"></a>
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
                <div class="double-scroller"><div></div></div>
                <div class="dispatch-summary-tab-list" <?= $summary_tab != 1 ? 'style="display:none;"' : '' ?>></div>
                <div class="dispatch-summary-list-none" style="padding: 1em; <?= $summary_tab != 1 ? 'display:none;' : '' ?>">No <?= $equipment_label ?> Found</div>
                <div class="dispatch-equipment-list" <?= $truck_tab != 1 ? 'style="display:none;"' : '' ?>></div>
                <div class="dispatch-equipment-list-none" style="padding: 1em; <?= $truck_tab != 1 ? 'display:none;' : '' ?>">No <?= $equipment_label ?> Selected</div>

                <div class="dispatch-table-list" <?= $table_tab != 1 ? 'style="display:none;"' : '' ?>></div>

            </div>
        </div>
    </div>
    <div class="loading_overlay" style="display: none;"><div class="loading_wheel"></div></div>
</div>