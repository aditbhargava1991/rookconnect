
    <div class="row">
        <label for="first_name" class="col-sm-3" style="<?= $style_strikethrough ?>">To Do Date:</label>
        <div class="col-sm-8" style="<?= $style_strikethrough ?>">
            <input style="<?= $style_strikethrough ?>" name="task_tododate" onchange="mark_task_date(this);" value="<?php echo $item['task_tododate']; ?>" type="text" class="datepicker form-control" id="todo_<?php echo $item['tasklistid']; ?>">
        </div>
    </div>

    <div class="row">
        <label for="site_name" class="col-sm-3" style="<?= $style_strikethrough ?>">Staff:</label>

        <div class="col-sm-9" style="<?= $style_strikethrough ?>">
            <div class="row" style="<?= $style_strikethrough ?>"><?php
                $task_contactids = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid` FROM `tasklist` WHERE tasklistid='{$item['tasklistid']}' AND `deleted`=0"))['contactid'];
                foreach(explode(',',trim($task_contactids,',')) as $task_contactid) { ?>
                    <div id="taskid_<?= $item['tasklistid'] ?>" class="add_staff">
                        <div class="clearfix"></div>
                        <div class="col-xs-9 no-pad-left">

                            <select style="<?= $style_strikethrough ?>" onchange="mark_task_staff(this);" data-placeholder="Select a Staff" name="task_userid[]" data-table="tasklist" data-field="contactid" class="chosen-select-deselect form-control" id="staff_<?= $item['tasklistid'] ?>">
                                <option value=""></option><?php
                                $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
                                foreach($staff_list as $staff_id) {
                                    $selected = ($task_contactid == $staff_id['contactid']) ? 'selected="selected"' : '' ?>
                                    <option <?= $selected ?> value="<?= $staff_id['contactid']; ?>"><?= $staff_id['first_name'].' '.$staff_id['last_name']; ?></option><?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-xs-3">
                            <img class="inline-img pull-right cursor-hand" data-taskid="<?= $item['tasklistid'] ?>" onclick="removeStaff(this);" src="../img/remove.png" />
                            <img class="inline-img pull-right cursor-hand" data-taskid="<?= $item['tasklistid'] ?>" onclick="addStaff(this);" src="../img/icons/ROOK-add-icon.png" />
                        </div>
                    </div><?php
                } ?>
            </div>

        </div>
    </div>

    <div class="row" style="<?= $style_strikethrough ?>">
        <label for="first_name" class="col-sm-3" style="<?= $style_strikethrough ?>">Status:</label>
        <div class="col-sm-9" style="<?= $style_strikethrough ?>">

            <select style="<?= $style_strikethrough ?>" onchange="task_status(this);" data-placeholder="Select a Status..." name="status" data-table="tasklist" data-field="status" class="<?php echo (strpos($task_mandatory_fields, ',Status,') !== FALSE ? 'required' : ''); ?> chosen-select-deselect form-control" id="status_<?php echo $item['tasklistid']; ?>">
                <option value=""></option>
              <?php
                $tabs = get_config($dbc, 'ticket_status');
                $each_tab = explode(',', $tabs);
                if($item['status'] == '') {
                    $item['status'] = get_config($dbc, 'task_default_status');
                }
                $selected_cat_tab = $cat_tab;
                foreach ($each_tab as $selected_cat_tab) {
                    if ($item['status'] == $selected_cat_tab) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $selected_cat_tab."'>".$selected_cat_tab.'</option>';
                }
              ?>
            </select>
        </div>
    </div>

