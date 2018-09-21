    <div class="form-group">
        <label for="first_name" class="col-sm-4">To Do Date:</label>
        <div class="col-sm-7">
            <input name="task_tododate" onchange="mark_task_date(this);" value="<?php echo $item['task_tododate']; ?>" type="text" class="datepicker form-control" id="todo_<?php echo $item['tasklistid']; ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="site_name" class="col-sm-4">Assign Staff:</label>
                <div class="col-sm-8">
                    <select multiple onchange="mark_task_staff(this);" data-placeholder="Select User" name="task_userid" data-table="tasklist" data-field="contactid" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380" id="todo_<?php echo $item['tasklistid']; ?>">
                        <?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
                        foreach($staff_list as $staff_id) { ?>
                             <!-- <option <?= ($staff_id == $_SESSION['contactid'] ? "selected" : '') ?> value='<?=  $staff_id; ?>' ><?= get_contact($dbc, $staff_id) ?></option> -->
                            <option <?= (strpos(','.$item['contactid'].',', ','.$staff_id.',') !== false) ? ' selected' : ''; ?> value="<?= $staff_id; ?>"><?= get_contact($dbc, $staff_id); ?></option>
                        <?php } ?>
                    </select>
                </div>

    </div>

