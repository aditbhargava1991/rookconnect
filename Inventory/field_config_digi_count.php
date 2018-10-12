<div class="gap-top">
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Digital inventory count functionality is ideal for users wanting to confirm that their actual quantity of inventory matches the quantity of inventory in their software."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Enable Digital Inventory Count:</label>
        <div class="col-sm-8">
        <?php
        $checked = '';
        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_digi_count'"));
        if($get_config['configid'] > 0) {
            $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_digi_count'"));
            if($get_config['value'] == '1') {
                $checked = 'checked';
            }
        }
        ?>
          <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='' class='show_digi_count' value='1'>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="When you go to Digital Inventory Count, data from this tab will load by default."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Default Tab:</label>
        <div class="col-sm-8">
            <select name="default_digi_count_tab" class="chosen-select-deselect form-control category_actual" width="380"><?php
                $sql = mysqli_query($dbc, "SELECT * FROM inventory WHERE deleted = 0 GROUP BY category ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name");
                $db_value = get_config($dbc, 'default_digi_count_tab'); ?>
                <option value="3456780123456971232" <?= $db_value == '3456780123456971232' ? 'selected="selected"' : '' ?>>Most Recently Added (25 Rows)</option>
                <option value="3456780123456971230" <?= $db_value == '3456780123456971230' ? 'selected="selected"' : '' ?>>All Tabs</option><?php
                while($row = mysqli_fetch_assoc($sql)){
                    $selected = $row['category'] == $db_value ? 'selected="selected"' : '';
                    echo '<option value="'.$row['category'].'" '.$selected.'>'.$row['category'].'</option>';
                } ?>
            </select>
        </div>
    </div>

    <div class="clearfix"></div>
</div>