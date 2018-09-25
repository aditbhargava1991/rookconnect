<?php if (strpos($value_config, ','."From Email".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label">Sending Name:<br /><i>(this is the name attached to the Sending Email.)</i></label>
    <div class="col-sm-8">
        <input type="text" name="from_name" value="<?= $get_ticket['from_name'] == '' ? get_contact($dbc, $_SESSION['contactid']) : $get_ticket['from_name'] ?>" class="form-control">
    </div>
</div>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label">Sending Email:</label>
    <div class="col-sm-8">
        <input type="text" name="from_email" value="<?= $get_ticket['from_email'] == '' ? get_email($dbc, $_SESSION['contactid']) : $get_ticket['from_email'] ?>" class="form-control">
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."To Contact".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label">Business Contact To Email:</label>
    <div class="col-sm-8">
        <!--
        <select name="businesscontact_to_emailid[]" multiple id="estimateclientid" data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" width="380">
            <?php
            $cat = '';
            $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE ('$businessid' IN (`businessid`,'') OR `contactid`='$clientid') ORDER BY category");
            while($row = mysqli_fetch_array($query)) {
                if($cat != $row['category']) {
                    echo '<optgroup label="'.$row['category'].'">';
                    $cat = $row['category'];
                }
                $email_address = get_email($dbc, $row['contactid']);
                if(trim($email_address) != '') {
                    ?>
                    <option <?php if (strpos(','.$businesscontact_to_emailid.',', ','.$email_address.',') !== FALSE || $_GET['cid'] == $row['contactid']) {
                    echo " selected"; } ?> value="<?php echo $email_address; ?>"><?php echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' : '.$email_address; ?></option>
                <?php }
            }
            ?>
        </select>
        -->
        <?php foreach(explode(',',trim($to_contact,',')) as $line_contactid) { ?>
            <div class="to_contact">
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-xs-10 no-pad-left">
                        <select data-placeholder="Select a Contact..." name="businesscontact_to_emailid[]" class="chosen-select-deselect form-control" width="380">
                            <option value=""></option>
                            <?php
                                $cat = '';
                                $query = mysqli_query($dbc, "SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE businessid='$businessid' ORDER BY category");
                                while($row = mysqli_fetch_array($query)) {
                                    if($cat != $row['category']) {
                                        echo '<optgroup label="'.$row['category'].'">';
                                        $cat = $row['category'];
                                    }
                                    $email_address = get_email($dbc, $row['contactid']);
                                    if(trim($email_address) != '') { ?>
                                        <option <?= strpos(','.$businesscontact_to_emailid.',', ','.$email_address.',') !== false ? ' selected' : ''; ?> value="<?= $email_address; ?>"><?= decryptIt($row['first_name']).' '.decryptIt($row['last_name']).': '.$email_address; ?></option><?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-2">
                        <img class="inline-img pull-right cursor-hand" onclick="removeStaff(this);" src="../img/remove.png" />
                        <img class="inline-img pull-right cursor-hand" onclick="addStaff(this);" src="../img/icons/ROOK-add-icon.png" />
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."CC Contact".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label">Business Contact CC Email:</label>
    <div class="col-sm-8">
        <!--
        <select name="businesscontact_cc_emailid[]" multiple id="estimateclientid" data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" width="380">
            <?php
            $cat = '';
            $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE ('$businessid' IN (`businessid`,'') OR `contactid`='$clientid') ORDER BY category");
            while($row = mysqli_fetch_array($query)) {
                if($cat != $row['category']) {
                    echo '<optgroup label="'.$row['category'].'">';
                    $cat = $row['category'];
                }
                $email_address = get_email($dbc, $row['contactid']);
                if(trim($email_address) != '') {
                    ?>
                    <option <?php if (strpos(','.$businesscontact_cc_emailid.',', ','.$email_address.',') !== FALSE) {
                    echo " selected"; } ?> value="<?php echo $email_address; ?>"><?php echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' : '.$email_address; ?></option>
                <?php }
            }
            ?>
        </select>
        -->
        <?php foreach(explode(',',trim($cc_contact,',')) as $line_contactid) { ?>
            <div class="cc_contact">
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-xs-10 no-pad-left">
                        <select data-placeholder="Select a Contact..." name="businesscontact_cc_emailid[]" class="chosen-select-deselect form-control" width="380">
                            <option value=""></option>
                            <?php
                                $cat = '';
                                $query = mysqli_query($dbc, "SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE businessid='$businessid' ORDER BY category");
                                while($row = mysqli_fetch_array($query)) {
                                    if($cat != $row['category']) {
                                        echo '<optgroup label="'.$row['category'].'">';
                                        $cat = $row['category'];
                                    }
                                    $email_address = get_email($dbc, $row['contactid']);
                                    if(trim($email_address) != '') { ?>
                                        <option <?= strpos(','.$businesscontact_cc_emailid.',', ','.$email_address.',') !== false ? ' selected' : ''; ?> value="<?= $email_address; ?>"><?= decryptIt($row['first_name']).' '.decryptIt($row['last_name']).': '.$email_address; ?></option><?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-2">
                        <img class="inline-img pull-right cursor-hand" onclick="removeStaff(this);" src="../img/remove.png" />
                        <img class="inline-img pull-right cursor-hand" onclick="addStaff(this);" src="../img/icons/ROOK-add-icon.png" />
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."To Staff".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label">Staff To Email:</label>
    <div class="col-sm-8">
        <!--
        <select name="companycontact_to_emailid[]" multiple <?php echo $disable_client; ?> data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" width="380">
            <?php
            $cat = '';
            $query1 = mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 ORDER BY category");
            while($row1 = mysqli_fetch_array($query1)) {
                $email_address = get_email($dbc, $row1['contactid']);
                if(trim($email_address) != '') {
                    ?>
                    <option <?php if (strpos(','.$companycontact_to_emailid.',', ','.$email_address.',') !== FALSE) {
                    echo " selected"; } ?> value="<?php echo $email_address; ?>"><?php echo decryptIt($row1['first_name']).' '.decryptIt($row1['last_name']).' : '.$email_address; ?></option>
                <?php }
            }
            ?>
        </select>
        -->
        <?php foreach(explode(',',trim($to_staff,',')) as $line_contactid) { ?>
            <div class="to_staff">
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-xs-10 no-pad-left">
                        <select data-placeholder="Select a Staff..." name="companycontact_to_emailid[]" class="chosen-select-deselect form-control" width="380">
                          <option value=""></option>
                          <?php $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
                            foreach($staff_query as $row) {
                                $email_address = get_email($dbc, $row['contactid']); ?>
                                <option <?= $line_contactid == $row['contactid'] ? ' selected' : ''; ?> value="<?= $email_address ?>"><?= $row['first_name'].' '.$row['last_name'] .': '. $email_address; ?></option>
                            <?php }
                          ?>
                        </select>
                    </div>
                    <div class="col-xs-2">
                        <img class="inline-img pull-right cursor-hand" onclick="removeStaff(this);" src="../img/remove.png" />
                        <img class="inline-img pull-right cursor-hand" onclick="addStaff(this);" src="../img/icons/ROOK-add-icon.png" />
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."CC Staff".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label">Staff CC Email:</label>
    <div class="col-sm-8">
        <!--
        <select name="companycontact_cc_emailid[]" multiple <?php echo $disable_client; ?> data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" width="380">
            <?php
            $cat = '';
            $query1 = mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 ORDER BY category");
            while($row1 = mysqli_fetch_array($query1)) {
                $email_address = get_email($dbc, $row1['contactid']);
                if(trim($email_address) != '') {
                    ?>
                    <option <?php if (strpos(','.$companycontact_cc_emailid.',', ','.$email_address.',') !== FALSE) {
                    echo " selected"; } ?> value="<?php echo $email_address; ?>"><?php echo decryptIt($row1['first_name']).' '.decryptIt($row1['last_name']).' : '.$email_address; ?></option>
                <?php }
            }
            ?>
        </select>
        -->
        <?php foreach(explode(',',trim($cc_staff,',')) as $line_contactid) { ?>
            <div class="cc_staff">
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-xs-10 no-pad-left">
                        <select data-placeholder="Select a Staff..." name="companycontact_cc_emailid[]" class="chosen-select-deselect form-control" width="380">
                          <option value=""></option>
                          <?php $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
                            foreach($staff_query as $row) {
                                $email_address = get_email($dbc, $row['contactid']); ?>
                                <option <?= $line_contactid == $row['contactid'] ? ' selected' : ''; ?> value="<?= $email_address ?>"><?= $row['first_name'].' '.$row['last_name'] .': '. $email_address; ?></option>
                            <?php }
                          ?>
                        </select>
                    </div>
                    <div class="col-xs-2">
                        <img class="inline-img pull-right cursor-hand" onclick="removeStaff(this);" src="../img/remove.png" />
                        <img class="inline-img pull-right cursor-hand" onclick="addStaff(this);" src="../img/icons/ROOK-add-icon.png" />
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Additional Email".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label">Additional Email(s):<br /><em>(separate emails by commas)</em></label>
    <div class="col-sm-8">
        <input type="text" name="new_emailid" value="<?php echo $new_emailid; ?>"  class="form-control">
    </div>
</div>
<?php } ?>