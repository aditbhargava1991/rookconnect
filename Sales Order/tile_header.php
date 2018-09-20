<!-- Tile Header -->
<script type="text/javascript">
$(document).on('change', 'select[name="so_type_add"]', function() { newSalesOrder(this); });
function newSalesOrder(sel) {
    if(sel.value != '' && sel.value != undefined) {
        window.location.href = '<?= WEBSITE_URL ?>/Sales Order/order.php?p=details&so_type='+sel.value;
    }
}
function newSalesOrderDialog() {
    $('#dialog_new_so').dialog({
        resizable: false,
        height: "auto",
        width: ($(window).width() <= 500 ? $(window).width() : 500),
        modal: true,
        buttons: {
            Cancel: function() {
                $(this).dialog('close');
            }
        }
    });
}
</script>
<?php
    if (isset($_GET['sotid'])) {
        $sales_order_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `sales_order_temp` WHERE `sotid` ='".$_GET['sotid']."'"))['name'];
    }
?>
<?php $sales_order_types = get_config($dbc, 'sales_order_types');
if(!empty($sales_order_types)) { ?>
    <div id="dialog_new_so" title="Select a <?= SALES_ORDER_NOUN ?> Type" style="display: none;">
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= SALES_ORDER_NOUN ?> Type:</label>
            <div class="col-sm-8">
                <select name="so_type_add" data-placeholder="Select a Type" class="chosen-select-deselect form-control"><option></option>
                    <?php foreach(explode(',', $sales_order_types) as $sales_order_type) { ?>
                        <option value="<?= $sales_order_type ?>"><?= $sales_order_type ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
<?php } ?>
<div class="tile-header standard-header">
    <div class="col-xs-12 col-sm-6">
        <h1>
            <a href="index.php"><span class="pull-left"><?= SALES_ORDER_TILE ?><?= !empty($sales_order_name) ? ' - '.$sales_order_name : '' ?></span></a>
            <span class="pull-left" style="margin-top:-5px;"><?php
                $staff = mysqli_query($dbc, "SELECT `initials`, `first_name`, `last_name`, `calendar_color` FROM `contacts` WHERE `contactid`='{$_SESSION['contactid']}'");
                profile_id($dbc, $_SESSION['contactid']); ?>
            </span>
            <span class="clearfix"></span>
        </h1>
    </div>
    <div class="col-xs-12 col-sm-6 text-right settings-block"><?php
        if ( config_visible_function ( $dbc, 'sales_order' ) == 1 ) { ?>
            <div class="pull-right gap-left top-settings">
                <a href="field_config.php" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
                <span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div><?php
        }
        if ( vuaed_visible_function ( $dbc, 'sales_order') == 1 ) { ?>
            <div class="pull-right gap-left top-button-2">
                <a href="templates.php?templateid=new" class="btn brand-btn mobile-block pull-right">Templates</a>
                <span class="popover-examples list-inline pull-right" style="margin:7px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create or edit a <?= SALES_ORDER_NOUN ?> Template."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
            <div class="pull-right double-gap-left top-button-2">
                <?php if(!empty($sales_order_types)) { ?>
                    <a href="" onclick="newSalesOrderDialog(); return false;" class="btn brand-btn mobile-block pull-right">New <?= SALES_ORDER_NOUN ?></a>
                <?php } else { ?>
                    <a href="order.php?p=details" class="btn brand-btn mobile-block pull-right">New <?= SALES_ORDER_NOUN ?></a>
                <?php } ?>
                <span class="popover-examples list-inline pull-right" style="margin:7px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create a <?= SALES_ORDER_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div><?php
        }
        if ( !isset($_GET['edit']) && !isset($_GET['view']) ) { ?>
            <div class="pull-right top-dashboard">
                

                <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-Speedometer.png" height="30" class="pull-right gap-left cursor-hand no-toggle" title="View Dashboards" onclick="$('.dashboard_select').toggle();">
                <div class="dashboard_select pull-right" style="display: none;">
                    <select class="chosen-select-deselect" onchange="window.location.replace('?dashboard='+this.value);">
                        <option value="<?= $_SESSION['contactid'] ?>">My Dashboard</option><?php
                        if ( $config_access > 0 ) {
                            echo '<option '.($_GET['dashboard'] == 'company_dashboard' ? 'selected' : '').' value="company_dashboard">Company Dashboard</option>';
                            foreach(sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1"),MYSQLI_ASSOC)) as $contactid) {
                                if($contactid != $_SESSION['contactid']) {
                                    echo '<option '.($_GET['dashboard'] == $contactid ? 'selected' : '').' value="'.$contactid.'">'.get_contact($dbc, $contactid).'\'s Dashboard</option>';
                                }
                            }
                        } ?>
                    </select>
                </div>
            </div>
			<img class="inline-img pull-right btn-horizontal-collapse no-toggle" src="../img/icons/pie-chart.png" title="View Summary">
            <?php if( $_GET['p']=='preview' && $_GET['sotid']!=''){ ?>
                <div class="icons_div" data-id="<?= $_GET['sotid'] ?>">
                    <a href='Add Reminder' onclick='return false;'><img src='<?= WEBSITE_URL?>/img/icons/ROOK-reminder-icon.png' class='no-toggle reminder-icon' title='Schedule Reminder' style='width: 2.5em;margin-right: 1em;'></a>
                </div>
            <?php } ?>
		<?php } ?>
    </div>
    <div class="clearfix"></div>
</div><!-- .tile-header -->
<script>
$(document).ready(function() {
    $('.icons_div .reminder-icon').off('click').click(function() {
        var item = $(this).closest('.icons_div');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=sales_order&id='+item.data('id'), 'auto', false, true);
    });
})
</script>