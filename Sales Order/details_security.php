<!-- Security -->
<div class="accordion-block-details padded" id="security">
    <div class="accordion-block-details-heading"><h4>Security Option</h4></div>
    <div class="row">
        <div class="col-sm-12 gap-md-left-15 set-row-height">Lead Created by: <?= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?></div>
    </div>
    <div class="row set-row-height">
        <div class="col-xs-12 col-sm-4 gap-md-left-15">Primary Staff:</div>
        <div class="col-xs-12 col-sm-5">
            <select data-placeholder="Select a Staff Member..." name="primary_staff" class="chosen-select-deselect form-control">
                <option value=""></option><?php
                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0"), MYSQLI_ASSOC));
                foreach($query as $id) { ?>
                    <option <?= ($id==$primary_staff) ? 'selected' : ''; ?> value="<?= $id; ?>"><?= get_contact($dbc, $id); ?></option><?php
                } ?>
            </select>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row set-row-height">
        <div class="col-xs-12 col-sm-4 gap-md-left-15">Share Lead:</div>
        <div class="col-xs-12 col-sm-5">
            <select data-placeholder="Select a Staff Member..." multiple name="share_lead[]" class="chosen-select-deselect form-control">
                <?php
                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0"), MYSQLI_ASSOC));
                foreach($query as $id) { ?>
                    <option <?= (strpos(','.$share_lead.',', ','.$id.',') !== false) ? 'selected' : ''; ?> value="<?= $id; ?>"><?= get_contact($dbc, $id); ?></option><?php
                } ?>
            </select>
        </div>
        <div class="clearfix"></div>
    </div>
</div>