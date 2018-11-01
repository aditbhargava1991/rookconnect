<div id="inv_packages">
    <h4 class="col-sm-12">Packages
    <span class="popover-examples list-inline">
        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select any packages here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
    </span><img src="../img/icons/ROOK-add-icon.png" class="no-toggle cursor-hand adjust_block" title="Add Package" width="21" onclick="add_package_row();" /></h4>
    <div class="form-group package_option" <?= (in_array('packages',$field_config) ? '' : 'style="display:none;"') ?>>
        <div class="col-sm-12">
            <div class="form-group clearfix hide-titles-mob package_labels" style="<?= ( empty(rtrim($packageid, ',')) && $_GET['inv_mode'] == 'adjust' ) ? 'display:none;' : '' ?>">
                <label class="col-sm-4 text-center">Category</label>
                <label class="col-sm-4 text-center">Package Name</label>
                <label class="col-sm-2 text-center">Fee</label>
                <label class="col-sm-2 text-center"></label>
            </div>

            <?php $each_package = array_filter(explode(',', $packageid));
            $each_package[] = '';
            $each_package_cost = array_filter(explode(',', $package_cost));
            foreach($each_package as $loop => $package) {
                $package_cost = $each_package_cost[$loop] * $discount_percent;
                $package_cat = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category` FROM `package` WHERE `packageid`='$package'"))['category']; ?>
                <div class="additional_package form-group clearfix" style="<?= $_GET['inv_mode'] == 'adjust' ? 'display:none;' : '' ?>">
                    <div class="col-sm-4"><label class="show-on-mob">Package Category:</label>
                        <?php if($_GET['inv_mode'] == 'adjust' && $package > 0) {
                            echo $package_cat;
                        } else { ?>
                            <select data-placeholder="Select Category..." id="<?php echo 'packagecat_'.$loop; ?>" name="packagecat[]" class="chosen-select-deselect form-control packagecat">
                                <option value=""></option>
                                <?php $query = mysqli_query($dbc,"SELECT `category` FROM `package` WHERE deleted=0 GROUP BY `category` ORDER BY `category`");
                                while($row = mysqli_fetch_array($query)) {
                                    echo "<option ".($package_cat == $row['category'] ? 'selected' : '')." value='". $row['category']."'>".$row['category'].'</option>';
                                } ?>
                            </select>
                        <?php } ?>
                    </div>
                    <div class="col-sm-4"><label class="show-on-mob">Package Name:</label>
                        <?php if($_GET['inv_mode'] == 'adjust' && $package > 0) {
                            $package_heading = get_field_value('heading','package','packageid',$package);
                            echo $package_heading;
                            ?><input type="hidden" name="package_label" value="<?= $package_cat.': '.$package_heading ?>">
                            <input type="hidden" name="init_packageid[]" value="<?= $package ?>"><?php
                        } else { ?>
                            <select data-placeholder="Select Package..." id="<?php echo 'packageid_'.$loop; ?>" name="packageid[]" class="chosen-select-deselect form-control packageid">
                                <option value=""></option>
                                <?php $query = mysqli_query($dbc,"SELECT `packageid`, `heading`, `category`, `cost` FROM `package` WHERE deleted=0 ORDER BY `heading`");
                                while($row = mysqli_fetch_array($query)) {
                                    echo "<option ".($package == $row['packageid'] ? 'selected' : '')." data-cat='".$row['category']."' data-cost='".$row['cost']."' value='". $row['packageid']."'>".$row['heading'].'</option>';
                                } ?>
                            </select>
                        <?php } ?>
                    </div>
                    <div class="col-sm-2"><label class="show-on-mob">Fee:</label>
                        <?php if($_GET['inv_mode'] == 'adjust') { ?>
                            <input name="package_cost[]" id="<?php echo 'package_cost_'.$loop; ?>" onchange="countTotalPrice()" value="<?php echo $package_cost + 0; ?>" type="hidden" class="package_cost" />
                            <?= number_format($package_cost + 0,2) ?>
                        <?php } else { ?>
                            <input name="package_cost[]" id="<?php echo 'package_cost_'.$loop; ?>" onchange="countTotalPrice()" value="<?php echo $package_cost + 0; ?>" type="number" step="any" readonly class="form-control package_cost" />
                        <?php } ?>
                        <input name="package_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                        <input name="package_gst_exempt[]" type="hidden" value="0" />
                    </div>
                    <div class="col-sm-2">
                        <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand adjust_block" onclick="rem_package_row(this);">
                        <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand adjust_block" onclick="add_package_row();">
                        <?php if($_GET['inv_mode'] == 'adjust' && $package > 0) { ?>
                            <label class="return_block"><input type="checkbox" name="packagerow_refund[]" value="<?= $insurer_row_id ?>" onchange="countTotalPrice()"> Refund</label>
                        <?php } ?>
                    </div>
                    <div class="col-sm-12 pay-div"></div>
                </div>
            <?php } ?>
            <div id="add_here_new_package"></div>
        </div>
    </div>
</div>