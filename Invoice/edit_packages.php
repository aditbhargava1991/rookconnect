<div id="inv_packages">
    <h3>Packages
    <span class="popover-examples list-inline">
        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select any packages here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
    </span></h3>
    <div class="form-group package_option" <?= (in_array('packages',$field_config) ? '' : 'style="display:none;"') ?>>
        <div class="col-sm-12">
            <div class="form-group clearfix hide-titles-mob">
                <label class="col-sm-4 text-center">Category</label>
                <label class="col-sm-5 text-center">Package Name</label>
                <label class="col-sm-2 text-center">Fee</label>
                <label class="col-sm-1 text-center"></label>
            </div>

            <?php $each_package = array_filter(explode(',', $packageid));
            $each_package[] = '';
            $each_package_cost = array_filter(explode(',', $package_cost));
            foreach($each_package as $loop => $package) {
                $package_cost = $each_package_cost[$loop];
                $package_cat = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category` FROM `package` WHERE `packageid`='$package'"))['category']; ?>
                <div class="additional_package form-group clearfix <?= $package > 0 ? '' : 'adjust_block' ?>">
                    <div class="col-sm-4"><label class="show-on-mob">Package Category:</label>
                        <select data-placeholder="Select Category..." id="<?php echo 'packagecat_'.$loop; ?>" name="packagecat[]" class="chosen-select-deselect form-control packagecat">
                            <option value=""></option>
                            <?php $query = mysqli_query($dbc,"SELECT `category` FROM `package` WHERE deleted=0 GROUP BY `category` ORDER BY `category`");
                            while($row = mysqli_fetch_array($query)) {
                                echo "<option ".($package_cat == $row['category'] ? 'selected' : '')." value='". $row['category']."'>".$row['category'].'</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-5"><label class="show-on-mob">Package Name:</label>
                        <select data-placeholder="Select Package..." id="<?php echo 'packageid_'.$loop; ?>" name="packageid[]" class="chosen-select-deselect form-control packageid">
                            <option value=""></option>
                            <?php $query = mysqli_query($dbc,"SELECT `packageid`, `heading`, `category`, `cost` FROM `package` WHERE deleted=0 ORDER BY `heading`");
                            while($row = mysqli_fetch_array($query)) {
                                echo "<option ".($package == $row['packageid'] ? 'selected' : '')." data-cat='".$row['category']."' data-cost='".$row['cost']."' value='". $row['packageid']."'>".$row['heading'].'</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-2"><label class="show-on-mob">Fee:</label>
                        <input name="package_cost[]" id="<?php echo 'package_cost_'.$loop; ?>" onchange="countTotalPrice()" value="<?php echo $package_cost + 0; ?>" type="number" step="any" readonly class="form-control package_cost" />
                        <input name="package_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                        <input name="package_gst_exempt[]" type="hidden" value="0" />
                    </div>
                    <div class="col-sm-1 adjust_block">
                        <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_package_row(this);">
                        <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_package_row();">
                    </div>
                    <div class="col-sm-12 pay-div"></div>
                </div>
            <?php } ?>
            <div id="add_here_new_package"></div>
        </div>
    </div>
</div>