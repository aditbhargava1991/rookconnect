<?php
if (isset($_POST['submit'])) {
    $inventory_tabs = filter_var(implode('#*#',array_filter($_POST['inventory_tabs'])),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='inventory_tabs'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$inventory_tabs' WHERE name='inventory_tabs'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('inventory_tabs', '$inventory_tabs')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $inventory_navigation_position = filter_var($_POST['inventory_navigation_position'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='inventory_navigation_position'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$inventory_navigation_position' WHERE name='inventory_navigation_position'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('inventory_navigation_position', '$inventory_navigation_position')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}
?>

<div class="gap-top">
    <?php $inventory_tab_list = explode('#*#', get_config($dbc, 'inventory_tabs')); ?>
    <script>
    function add_inventory_tab() {
        var clone = $('.inv-tabs .form-group').last().clone();
        clone.find('input').val('');
        $('.inv-tabs').append(clone);
        $('.inv-tabs input').last().focus();
    }
    function rem_inventory_tab(link) {
        $(link).closest('.form-group').remove();
    }
    </script>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="These tabs sort your inventory by Tab, so please make sure the tab names match your inventory's tab names."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Add Tabs:</label>
        <div class="col-sm-8 inv-tabs">
            <?php foreach($inventory_tab_list as $inventory_tab) { ?>
                <div class="form-group">
                    <div class="col-sm-10"><input name="inventory_tabs[]" type="text" value="<?= $inventory_tab ?>" class="form-control"/></div>
                    <div class="col-sm-2"><img src="<?= WEBSITE_URL ?>/img/plus.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right" onclick="add_inventory_tab();">
                        <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right" onclick="rem_inventory_tab(this);"></div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Instead of tabs, have a drop down menu that will sort your inventory by their respective tabs."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Or Use a Drop Down Menu:</label>
        <div class="col-sm-8">
        <?php
        $checked = '';
        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_category_dropdown'"));
        if($get_config['configid'] > 0) {
            $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_category_dropdown'"));
            if($get_config['value'] == '1') {
                $checked = 'checked';
            }
        }
        ?>
          <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='' class='show_category_dropdown' value='1'>
        </div>
    </div>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Check this box to make Default as Select All."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Check this box to make Default as Select All:</label>
        <div class="col-sm-8">
            <?php
            $checked = '';
            $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='inventory_default_select_all'"));
            if($get_config['configid'] > 0) {
                $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='inventory_default_select_all'"));
                if($get_config['value'] == '1') {
                    $checked = 'checked';
                }
            }
            ?>
          <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='' class='inventory_default_select_all' value='1'>
        </div>
    </div>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Choose navigation positioning."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Navigation Position:</label>
        <div class="col-sm-8">
            <?php $inventory_navigation_position = get_config($dbc, 'inventory_navigation_position'); ?>
            <label><input type="radio" name="inventory_navigation_position" value="" <?= empty($inventory_navigation_position) ? 'checked' : '' ?>>Side</label>&nbsp;&nbsp;
            <label><input type="radio" name="inventory_navigation_position" value="top" <?= $inventory_navigation_position == 'top' ? 'checked' : '' ?>>Top</label>
        </div>
    </div>

    <div class="clearfix"></div>
</div>