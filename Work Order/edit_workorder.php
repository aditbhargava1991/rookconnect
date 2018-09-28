<?php include_once('../include.php');
error_reporting(0);
if (isset($_POST['submit_workorder'])) {
    $region = filter_var($_POST['region'],FILTER_SANITIZE_STRING);
    $businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
    $heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'],FILTER_SANITIZE_STRING);
    $city = filter_var($_POST['city'],FILTER_SANITIZE_STRING);
    $postal_code = filter_var($_POST['postal_code'],FILTER_SANITIZE_STRING);
    $pickup_address = filter_var($_POST['pickup_address'],FILTER_SANITIZE_STRING);
    $pickup_city = filter_var($_POST['pickup_city'],FILTER_SANITIZE_STRING);
    $pickup_postal_code = filter_var($_POST['pickup_postal_code'],FILTER_SANITIZE_STRING);
    $dropoff_address = filter_var($_POST['dropoff_address'],FILTER_SANITIZE_STRING);
    $dropoff_city = filter_var($_POST['dropoff_city'],FILTER_SANITIZE_STRING);
    $dropoff_postal_code = filter_var($_POST['dropoff_postal_code'],FILTER_SANITIZE_STRING);
    $workorder_type = filter_var($_POST['workorder_type'],FILTER_SANITIZE_STRING);
    if ($workorder_type == 'NEW_TYPE') {
        $workorder_type = filter_var($_POST['workorder_type_new'],FILTER_SANITIZE_STRING);
    }
    $to_do_date = filter_var($_POST['to_do_date'],FILTER_SANITIZE_STRING);
    $to_do_time = filter_var($_POST['to_do_time'],FILTER_SANITIZE_STRING);
    $distance = filter_var($_POST['distance'],FILTER_SANITIZE_STRING);
    $num_items = filter_var($_POST['num_items'],FILTER_SANITIZE_STRING);
    $item_description = filter_var(htmlentities($_POST['item_description']),FILTER_SANITIZE_STRING);
    $exchange_product = filter_var($_POST['exchange_product'],FILTER_SANITIZE_STRING);
    $return_address = filter_var($_POST['return_address'],FILTER_SANITIZE_STRING);
    $return_city = filter_var($_POST['return_city'],FILTER_SANITIZE_STRING);
    $return_postal_code = filter_var($_POST['return_postal_code'],FILTER_SANITIZE_STRING);
    $oversized_item =filter_var($_POST['oversized_item'],FILTER_SANITIZE_STRING);
    $measure_width = filter_var($_POST['measure_width'],FILTER_SANITIZE_STRING);
    $measure_height = filter_var($_POST['measure_height'],FILTER_SANITIZE_STRING);
    $measure_depth = filter_var($_POST['measure_depth'],FILTER_SANITIZE_STRING);
    $assign_work = filter_var(htmlentities($_POST['assign_work']),FILTER_SANITIZE_STRING);
    $assembly_required = filter_var($_POST['assembly_required'],FILTER_SANITIZE_STRING);
    $max_time = $_POST['max_time_hour'].':'.$_POST['max_time_minute'].':00';
    $contactid = ','.implode(',', $_POST['contactid']).',';
    $assign_teamid = filter_var($_POST['assign_teamid'],FILTER_SANITIZE_STRING);
    $assign_equip_assignid = filter_var($_POST['assign_equip_assignid'],FILTER_SANITIZE_STRING);

    $created_date = date('Y-m-d');
    $created_by = $_SESSION['contactid'];

    if(empty($_POST['workorderid'])) {
        echo $query = "INSERT INTO `workorder` (`region`, `businessid`, `heading`, `address`, `city`, `postal_code`, `pickup_address`, `pickup_city`, `pickup_postal_code`, `dropoff_address`, `dropoff_city`, `dropoff_postal_code`, `workorder_type`, `to_do_date`, `to_do_time`, `distance`, `num_items`, `item_description`, `exchange_product`, `return_address`, `return_city`, `return_postal_code`, `oversized_item`, `measure_width`, `measure_height`, `measure_depth`, `assign_work`, `assembly_required`, `max_time`, `contactid`, `assign_teamid`, `assign_equip_assignid`) VALUES ('$region', '$businessid', '$heading', '$address', '$city', '$postal_code', '$pickup_address', '$pickup_city', '$pickup_postal_code', '$dropoff_address', '$dropoff_city', '$dropoff_postal_code', '$workorder_type', '$to_do_date', '$to_do_time', '$distance', '$num_items', '$item_description', '$exchange_product', '$return_address', '$return_city', '$return_postal_code', '$oversized_item', '$measure_width', '$measure_height', '$measure_depth', '$assign_work', '$assembly_required', '$max_time', '$contactid', '$assign_teamid', '$assign_equip_assignid')";
        $result = mysqli_query($dbc, $query);
        $workorderid = mysqli_insert_id($dbc);
    } else {
        $workorderid = $_POST['workorderid'];
        $query = "UPDATE `workorder` SET `region` = '$region', `businessid` = '$businessid', `heading` = '$heading', `address` = '$address', `city` = '$city', `postal_code` = '$postal_code', `pickup_address` = '$pickup_address', `pickup_city` = '$pickup_city', `pickup_postal_code` = '$pickup_postal_code', `dropoff_address` = '$dropoff_address', `dropoff_city` = '$dropoff_city', `dropoff_postal_code` = '$dropoff_postal_code', `workorder_type` = '$workorder_type', `to_do_date` = '$to_do_date', `to_do_time` = '$to_do_time', `distance` = '$distance', `num_items` = '$num_items', `item_description` = '$item_description', `exchange_product` = '$exchange_product', `return_address` = '$return_address', `return_city` = '$return_city', `return_postal_code` = '$return_postal_code', `oversized_item` = '$oversized_item', `measure_width` = '$measure_width', `measure_height` = '$measure_height', `measure_depth` = '$measure_depth', `assign_work` = '$assign_work', `assembly_required` = '$assembly_required', `max_time` = '$max_time', `contactid` = '$contactid', `assign_teamid` = '$assign_teamid', `assign_equip_assignid` = '$assign_equip_assignid' WHERE `workorderid` = '$workorderid'";
        $result = mysqli_query($dbc, $query);
    }

    //Deliverables
    if (!empty($_POST['add_deliverable'])) {
        $status = $_POST['status'];
        $deliverable_date = $_POST['deliverable_date'];
        $query = "INSERT INTO `workorder_deliverables` (`workorderid`, `status`, `contactid`, `created_date`, `created_by`) VALUES ('$workorderid', '$status', '$contactid', '$created_date', '$created_by')";
        $result = mysqli_query($dbc, $query);

        $query = "UPDATE `workorder` SET `status` = '$status', `deliverable_date` = '$deliverable_date' WHERE `workorderid` = '$workorderid'";
        $result = mysqli_query($dbc, $query);

        $sender = (!empty($_POST['deliverable_email_sender']) ? $_POST['deliverable_email_sender'] : '');
        $subject = $_POST['deliverable_email_subject'];
        $email_body = str_replace(['[WORKORDERID]'], [$workorderid], $_POST['deliverable_email_body']);
        $email_contacts = explode(',', $contactid);
        foreach ($email_contacts as $email_contact) {
            $to = mysqli_fetch_array(mysqli_query($dbc, "SELECT `email_address` FROM `contacts` WHERE `contactid` = '$email_contact'"))['email_address'];
            if (!empty($to)) {
                $to = decryptIt($to);
                try {
                    send_email($sender, $to, '', '', $subject, $email_body, '');
                } catch(Exception $e) {
                    echo "<script>alert('Unable to send email. Please try again later.');</script>";
                }
            }
        }
    }

    //Notes
    if (!empty($_POST['add_note'])) {
        $type = 'note';
        $note_heading = filter_var($_POST['note_heading'],FILTER_SANITIZE_STRING);
        $workorder_comment = htmlentities($_POST['workorder_comment']);
        $t_comment = filter_var($workorder_comment,FILTER_SANITIZE_STRING);
        $sender = (!empty($_POST['note_email_sender']) ? $_POST['note_email_sender'] : '');
        $subject = $_POST['note_email_subject'];
        $email_body = str_replace(['[NOTE]', '[WORKORDERID]'], [$_POST['workorder_comment'], $workorderid], $_POST['note_email_body']);

        if ($t_comment != '') {
            $email_comment = $_POST['note_email_staff'];
            $query = "INSERT INTO `workorder_comment` (`workorderid`, `comment`, `email_comment`, `created_date`, `created_by`, `type`, `note_heading`) VALUES ('$workorderid', '$t_comment', '$email_comment', '$created_date', '$created_by', '$type', '$note_heading')";
            $result = mysqli_query($dbc, $query);

            $to = mysqli_fetch_array(mysqli_query($dbc, "SELECT `email_address` FROM `contacts` WHERE `contactid` = '$email_comment'"))['email_address'];
            if (!empty($_POST['note_send_email']) && !empty($to)) {
                $to = decryptIt($to);
                try {
                    send_email($sender, $to, '', '', $subject, $email_body, '');
                } catch(Exception $e) {
                    echo "<script>alert('Unable to send email. Please try again later.');</script>";
                }
            }
        }
    }

    //Documents
    if (!file_exists(WEBSITE_URL.'/Work Order/download')) {
        mkdir(WEBSIE_URL.'/Work Order/download', 0777, true);
    }
    for($i = 0; $i < count($_FILES['support_doc']['name']); $i++) {
        $document = htmlspecialchars($_FILES['support_doc']['name'][$i], ENT_QUOTES);

        move_uploaded_file($_FILES['support_doc']['tmp_name'][$i], WEBSITE_URL.'/Work Order/download/'.$_FILES['support_doc']['name'][$i]);

        if($document != '') {
            $query = "INSERT INTO `workorder_document` (`workorderid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$workorderid', 'Support Document', '$document', '$created_date', '$created_by')";
            $result = mysqli_query($dbc, $query);
        }
    }
    for($i = 0; $i < count($_POST['support_link']); $i++) {
        $link = $_POST['support_link'][$i];

        if($link != '') {
            $query = "INSERT INTO `workorder_document` (`workorderid`, `type`, `link`, `created_date`, `created_by`) VALUES ('$workorderid', 'Support Link', '$link', '$created_date', '$created_by')";
            $result = mysqli_query($dbc, $query);
        }
    }
    for($i = 0; $i < count($_FILES['review_doc']['name']); $i++) {
        $document = htmlspecialchars($_FILES['review_doc']['name'][$i], ENT_QUOTES);

        move_uploaded_file($_FILES['review_doc']['tmp_name'][$i], WEBSITE_URL.'/Work Order/download/'.$_FILES['review_doc']['name'][$i]);

        if($document != '') {
            $query = "INSERT INTO `workorder_document` (`workorderid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$workorderid', 'Review Document', '$document', '$created_date', '$created_by')";
            $result = mysqli_query($dbc, $query);
        }
    }
    for($i = 0; $i < count($_POST['review_link']); $i++) {
        $link = $_POST['review_link'][$i];

        if($link != '') {
            $query = "INSERT INTO `workorder_document` (`workorderid`, `type`, `link`, `created_date`, `created_by`) VALUES ('$workorderid', 'Review Link', '$link', '$created_date', '$created_by')";
            $result = mysqli_query($dbc, $query);
        }
    }
}

$action = 'view';
if ($_GET['action'] == 'edit') {
    $action = 'edit';
}

$workorderid = '';
$region = '';
$projectid = '';
$businessid = '';
$heading = '';
$address = '';
$city = '';
$postal_code = '';
$pickup_address = '';
$pickup_city = '';
$pickup_postal_code = '';
$dropoff_address = '';
$dropoff_city = '';
$dropoff_postal_code = '';
$workorder_type = '';
$to_do_date = '';
$to_do_time = '';
$distance = '';
$num_items = '';
$item_description = '';
$exchange_product = '';
$return_address = '';
$return_city = '';
$return_postal_code = '';
$oversized_item ='';
$measure_width = '';
$measure_height = '';
$measure_depth = '';
$assign_work = '';
$assembly_required = '';
$max_time = '';
$contactid = '';
$assign_teamid = '';
$assign_equip_assignid = '';
$status = '';
$deliverable_date = '';

if (!empty($_GET['workorderid'])) {
    $workorderid = $_GET['workorderid'];
    $get_wo = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `workorder` WHERE `workorderid` = '$workorderid'"));

    $region = $get_wo['region'];
    $projectid = $get_wo['projectid'];
    $businessid = $get_wo['businessid'];
    $heading = $get_wo['heading'];
    $address = $get_wo['address'];
    $city = $get_wo['city'];
    $postal_code = $get_wo['postal_code'];
    $pickup_address = $get_wo['pickup_address'];
    $pickup_city = $get_wo['pickup_city'];
    $pickup_postal_code = $get_wo['pickup_postal_code'];
    $dropoff_address = $get_wo['dropoff_address'];
    $dropoff_city = $get_wo['dropoff_city'];
    $dropoff_postal_code = $get_wo['dropoff_postal_code'];
    $workorder_type = $get_wo['workorder_type'];
    $to_do_date = $get_wo['to_do_date'];
    $to_do_time = $get_wo['to_do_time'];
    $distance = $get_wo['distance'];
    $num_items = $get_wo['num_items'];
    $item_description = $get_wo['item_description'];
    $exchange_product = $get_wo['exchange_product'];
    $return_address = $get_wo['return_address'];
    $return_city = $get_wo['return_city'];
    $return_postal_code = $get_wo['return_postal_code'];
    $oversized_item = $get_wo['oversized_item'];
    $measure_width = $get_wo['measure_width'];
    $measure_height = $get_wo['measure_height'];
    $measure_depth = $get_wo['measure_depth'];
    $assign_work = $get_wo['assign_work'];
    $assembly_required = $get_wo['assembly_required'];
    $max_time = explode(':', $get_wo['max_time']);
    $contactid = $get_wo['contactid'];
    $assign_teamid = $get_wo['assign_teamid'];
    $assign_equip_assignid = $get_wo['assign_equip_assignid'];
    $status = $get_wo['status'];
    $deliverable_date = $get_wo['deliverable_date'];
}

$enabled_fields = ','.get_config($dbc, 'appt_wo_fields').',';
?>
<style>
label.super-label {
    font-size: 0.8em;
}
.disabled-input {
    pointer-events: none;
}
.opacity-change {
    opacity: 0.6;
}
</style>
<script type="text/javascript">
$(document).ready(function () {
    <?php if ($action == 'view') { ?>
        $(".main-screen").find('.super-label').addClass("disabled-input");
        $(".main-screen").find(".form-input").addClass("opacity-change");
        $(".main-screen").find("input").css("background-color", "#DDD");
    <?php } ?>
});
function changeDeliveryType(delivery_type) {
    if ($(delivery_type).val() == 'NEW_TYPE') {
        $('[name="workorder_type_new"]').removeAttr("style");
    } else {
        $('[name="workorder_type_new"]').hide();
    }
}
function editWorkOrder() {
    $(".main-screen").find(".super-label").removeClass("disabled-input");
    $(".main-screen").find(".form-input").removeClass("opacity-change");
    $(".main-screen").find("input").css("background-color", "#FFF");
    $(".summary_edit").show();
    $(".summary_view").hide();
    $("#edit_btn").hide();
    $("#submit_btn").show();
    $("#cancel_btn").val("edit");
    $("#cancel_btn").text("Back");
}
function cancelWorkOrder() {
    if ($("#cancel_btn").val() == 'edit') {
        $(".main-screen").find('.super-label').addClass("disabled-input");
        $(".main-screen").find(".form-input").addClass("opacity-change");
        $(".main-screen").find("input").css("background-color", "#DDD");
        $(".summary_edit").hide();
        $(".summary_view").show();
        $("#edit_btn").show();
        $("#submit_btn").hide();
        $("#cancel_btn").val("cancel");
        $("#cancel_btn").text("Cancel");
    } else {
        window.location.reload();
    }
}
function addAnotherDoc() {
    var clone = $('.additional_doc').clone();
    clone.find('.form-control').val('');
    clone.removeClass('additional_doc');
    $('#add_here_new_doc').append(clone);
}
function addAnotherDocReview() {
    var clone = $('.additional_doc_review').clone();
    clone.find('.form-control').val('');
    clone.removeClass('additional_doc_review');
    $('#add_here_new_doc_review').append(clone);
}
function addAnotherLink() {
    var clone = $('.additional_link').clone();
    clone.find('.form-control').val('');
    clone.removeClass('additional_link');
    $('#add_here_new_link').append(clone);
}
function addAnotherLinkReview() {
    var clone = $('.additional_link_review').clone();
    clone.find('.form-control').val('');
    clone.removeClass('additional_link_review');
    $('#add_here_new_link_review').append(clone);
}
function addDeliverable(checkbox) {
    if ($(checkbox).is(":checked")) {
        $("#deliverables").show();
    } else {
        $("#deliverables").hide();
    }
}
function addNote(checkbox) {
    if ($(checkbox).is(":checked")) {
        $("#notes").show();
    } else {
        $("#notes").hide();
    }
}
function noteSendEmail(checkbox) {
    if ($(checkbox).is(":checked")) {
        $("#notes_email").show();
    } else {
        $("#notes_email").hide();
    }
}
</script>

<div class="main-screen full-width-screen">
<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <input type="hidden" name="workorderid" value="<?= $workorderid ?>">

    <div class="col-sm-12" style="background-color: rgb(58, 196, 242);">
        <h1 style="color: #fff; padding-top: 0.2em; padding-bottom: 0.2em; margin: 0;">WO: <?= $heading ?>
        <a href="" onclick="window.location.reload();" class="brand-btn pull-right" style="background-color: #fff; color: rgb(58, 196, 242); padding-right: 0.4em; padding-left: 0.4em; text-decoration: none;">X</a></h1>
    </div>

    <div class="col-sm-6">
        <h3>Summary</h3>

        <div style="height: 40em; overflow-y: auto; padding: 1em;">

            <div class="summary_edit"<?php if ($action != 'edit') { echo ' style="display:none;"'; } ?>>
                <?php if (strpos($enabled_fields, ',region,') !== FALSE) { ?>
                    <label class="super-label">Region:
                    <div class="form-input">
                        <select data-placeholder="Select Region" name="region" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php
                                $query = "SELECT * FROM `general_configuration` WHERE `name` LIKE '%_region'";
                                $result = mysqli_query($dbc, $query);
                                $region_list = '';
                                while ($row = mysqli_fetch_array($result)) {
                                    $region_list .= $row['value'] . ',';
                                }
                                $region_list = rtrim($region_list, ',');
                                $region_list = explode(',', $region_list);
                                asort($region_list);
                                foreach ($region_list as $single_region) {
                                    if ($region == $single_region) {
                                        $selected = 'selected="selected"';
                                    } else {
                                        $selected = '';
                                    }
                                    echo "<option ".$selected." value='". $single_region."'>".$single_region.'</option>';
                                }
                            ?>
                        </select>
                    </div></label>
                <?php } ?>

                <?php if (strpos($enabled_fields, ',project,') !== FALSE) { ?>
                    <label class="super-label">Project:
                    <div class="form-input">
                        <select data-placeholder="Select Project" name="projectid" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php
                                $query = "SELECT * FROM `project` WHERE `deleted` = 0 ORDER BY `project_name`";
                                $result = mysqli_query($dbc, $query);
                                while ($row = mysqli_fetch_array($result)) {
                                    echo '<option value="'.$row['projectid'].'"'.($row['projectid'] == $projectid ? ' selected' : '').'>'.$row['project_name'].'</option>';
                                }
                            ?>
                        </select>
                    </div></label>
                <?php } ?>

                <?php if (strpos($enabled_fields, ',customer,') !== FALSE) { ?>
                    <label class="super-label">Customer:
                    <div class="form-input">
                        <select data-placeholder="Select Customer" name="businessid" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php
                                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Business' AND `deleted` = 0 AND `status` = 1"),MYSQLI_ASSOC));
                                foreach ($query as $id) {
                                    echo '<option value="'.$id.'"'.($id == $businessid ? ' selected' : '').'>'.get_client($dbc, $id).'</option>';
                                }
                            ?>
                        </select>
                    </div></label>
                <?php } ?>

                <?php if (strpos($enabled_fields, ',location,') !== FALSE) { ?>
                    <label class="super-label">Address:
                    <div class="form-input">
                        <input type="text" name="address" value="<?= $address ?>" class="form-control">
                    </div></label>

                    <label class="super-label">City:
                    <div class="form-input">
                        <input type="text" name="city" value="<?= $city ?>" class="form-control">
                    </div></label>

                    <label class="super-label">Postal Code:
                    <div class="form-input">
                        <input type="text" name="postal_code" value="<?= $postal_code ?>" class="form-control">
                    </div></label>
                <?php } ?>

                <?php if (strpos($enabled_fields, ',heading,') !== FALSE) { ?>
                    <label class="super-label">Work Order No. :
                    <div class="form-input">
                        <input type="text" name="heading" value="<?= $heading ?>" class="form-control">
                    </div></label>
                <?php } ?>

                <?php if (strpos($enabled_fields, ',pickup_location,') !== FALSE) { ?>
                    <label class="super-label">Pick Up Address:
                    <div class="form-input">
                        <input type="text" name="pickup_address" value="<?= $pickup_address ?>" class="form-control">
                    </div></label>

                    <label class="super-label">Pick Up City:
                    <div class="form-input">
                        <input type="text" name="pickup_city" value="<?= $pickup_city ?>" class="form-control">
                    </div></label>

                    <label class="super-label">Pick Up Postal Code:
                    <div class="form-input">
                        <input type="text" name="pickup_postal_code" value="<?= $pickup_postal_code ?>" class="form-control">
                    </div></label>
                <?php } ?>

                <?php if (strpos($enabled_fields, ',dropoff_location,') !== FALSE) { ?>
                    <label class="super-label">Drop Off Address:
                    <div class="form-input">
                        <input type="text" name="dropoff_address" value="<?= $dropoff_address ?>" class="form-control">
                    </div></label>

                    <label class="super-label">Drop Off City:
                    <div class="form-input">
                        <input type="text" name="dropoff_city" value="<?= $dropoff_city ?>" class="form-control">
                    </div></label>

                    <label class="super-label">Drop Off Postal Code:
                    <div class="form-input">
                        <input type="text" name="dropoff_postal_code" value="<?= $dropoff_postal_code ?>" class="form-control">
                    </div></label>
                <?php } ?>
            </div>

            <div class="summary_view"<?php if ($action != 'view') { echo ' style="display:none;"'; } ?>>
                <?php if (strpos($enabled_fields, ',region,') !== FALSE) { ?>
                    <label class="control-label">REGION:&nbsp;</label><?= $region ?><br />
                <?php } ?>

                <?php if (strpos($enabled_fields, ',project,') !== FALSE) { ?>
                    <label class="control-label">PROJECT:&nbsp;</label><?= get_project($dbc, $projectid, 'project_name') ?><br />
                <?php } ?>

                <?php if (strpos($enabled_fields, ',customer,') !== FALSE) { ?>
                    <label class="control-label">CUSTOMER:&nbsp;</label><?= get_client($dbc, $businessid) ?><br />
                <?php } ?>

                <?php if (strpos($enabled_fields, ',location,') !== FALSE) { ?>
                    <label class="control-label">LOCATION:&nbsp;</label><?= $address.' '.$city.' '.$postal_code ?><br />
                <?php } ?>

                <?php if (strpos($enabled_fields, ',heading,') !== FALSE) { ?>
                    <label class="control-label">WORK ORDER NO. :&nbsp;</label><?= $heading ?><br />
                <?php } ?>

                <?php if (strpos($enabled_fields, ',pickup_location,') !== FALSE) { ?>
                    <h5>PICK UP LOCATION:</h5>
                    <?= $pickup_address.' '.$pickup_city.' '.$pickup_postal_code ?><br />
                <?php } ?>

                <?php if (strpos($enabled_fields, ',dropoff_location,') !== FALSE) { ?>
                    <h5>DROP OFF LOCATION:</h5>
                    <?= $dropoff_address.' '.$dropoff_city.' '.$dropoff_postal_code ?><br />
                <?php } ?>
            </div>

            <div style="padding: 1em;"></div>

            <?php if (strpos($enabled_fields, ',workorder_type,') !== FALSE) { ?>
                <label class="super-label">Delivery Type:
                <div class="form-input">
                    <select data-placeholder="Select Delivery Type" name="workorder_type" class="chosen-select-deselect form-control" onchange="changeDeliveryType(this)">
                        <option></option>
                        <option value="NEW_TYPE">New Delivery Type</option>
                        <?php
                            $query = "SELECT DISTINCT `workorder_type` FROM `workorder`";
                            $result = mysqli_query($dbc, $query);
                            while ($row = mysqli_fetch_array($result)) {
                                echo '<option value="'.$row['workorder_type'].'"'.($row['workorder_type'] == $workorder_type ? ' selected' : '').'>'.$row['workorder_type'].'</option>';
                            }
                        ?>
                    </select>
                </div></label>
                <div class="form-input">
                    <input type="text" name="workorder_type_new" class="form-control" style="display:none;">
                </div>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',to_do_date,') !== FALSE) { ?>
                <label class="super-label">Delivery Date:
                <div class="form-input">
                    <input type="text" name="to_do_date" value="<?= $to_do_date ?>" class="form-control datepicker">
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',to_do_time,') !== FALSE) { ?>
                <label class="super-label">Delivery Time:
                <div class="form-input">
                    <input type="text" name="to_do_time" value="<?= date('h:i a', strtotime($to_do_time)) ?>" class="form-control datetimepicker">
                </div></label>
            <?php } ?>

            <div class="summary_edit"<?php if ($action != 'edit') { echo ' style="display:none;"'; } ?>>
                <?php if (strpos($enabled_fields, ',documents,') !== FALSE) { ?>
                    <div style="padding: 1em;"></div>

                    <label class="super-label">Support Documents:
                    <div class="form-input">
                        <div class="additional_doc">
                            <input name="support_doc[]" multiple type="file" data-filename-placement="inside" class="form-control">
                        </div>
                        <div id="add_here_new_doc"></div>
                        <button id="add_row_doc" class="btn brand-btn pull-right" onclick="addAnotherDoc(); return false;">Add Another Document</button>
                    </div></label>

                    <label class="super-label">Support Links:
                    <div class="form-input">
                        <div class="additional_link">
                            <input name="support_link[]" type="text" class="form-control">
                        </div>
                        <div id="add_here_new_link"></div>
                        <button id="add_row_link" class="btn brand-btn pull-right" onclick="addAnotherLink(); return false;">Add Another Link</button>
                    </div></label>

                    <label class="super-label">Review Documents:
                    <div class="form-input">
                        <div class="additional_doc_review">
                            <input name="review_doc[]" multiple type="file" data-filename-placement="inside" class="form-control">
                        </div>
                        <div id="add_here_new_doc_review"></div>
                        <button id="add_row_doc_review" class="btn brand-btn pull-right" onclick="addAnotherDocReview(); return false;">Add Another Document</button>
                    </div></label>

                    <label class="super-label">Review Links:
                    <div class="form-input">
                        <div class="additional_link_review">
                            <input name="review_link[]" type="text" class="form-control">
                        </div>
                        <div id="add_here_new_link_review"></div>
                        <button id="add_row_link_review" class="btn brand-btn pull-right" onclick="addAnotherLinkReview(); return false;">Add Another Link</button>
                    </div></label>
                <?php } ?>
            </div>

            <div class="summary_view"<?php if ($action != 'view') { echo ' style="display:none;"'; } ?>>
                <?php if (strpos($enabled_fields, ',documents,') !== FALSE) { ?>
                    <?php
                        $query = "SELECT * FROM `workorder_document` WHERE `workorderid` = '$workorderid' ORDER BY `workorderdocid` DESC";
                        $result = mysqli_query($dbc, $query);
                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                    ?>
                        <h5>DOCUMENTS</h5>
                        <table class="table table-bordered">
                            <tr class="hidden-xs hidden-sm">
                                <th>Type</th>
                                <th>Document/Link</th>
                                <th>Date</th>
                                <th>Uploaded By</th>
                            </tr>
                        <?php while($row = mysqli_fetch_array($result)) { ?>
                            <tr>
                                <td><?= $row['type'] ?></td>
                                <td><?= ($row['document'] != '' ? '<a href="'.WEBSITE_URL.'/download/'.$row['document'].'" target="_blank">'.$row['document'].'</a>' : '<a href="'.$row['link'].'" target="_blank">'.$row['link'].'</a>') ?></td>
                                <td><?= $row['created_date'] ?></td>
                                <td><?= get_contact($dbc, $row['created_by']) ?></td>
                            </tr>
                        <?php } ?>
                        </table>
                    <?php } ?>
                <?php } ?>

                <?php if (strpos($enabled_fields, ',deliverables,') !== FALSE) { ?>
                    <?php
                        $query = "SELECT * FROM `workorder_deliverables` WHERE `workorderid` = '$workorderid' ORDER BY `deliverablesid` DESC";
                        $result = mysqli_query($dbc, $query);
                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                    ?>
                        <h5>DELIVERABLES</h5>
                        <table class="table table-bordered">
                            <tr class="hidden-xs hidden-sm">
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Date</th>
                                <th>Assigned By</th>
                            </tr>
                        <?php while($row = mysqli_fetch_array($result)) {
                            $assigned_to = explode(',', $row['contactid']);
                            $staff = '';
                            foreach ($assigned_to as $staffid) {
                                if (!empty($staffid)) {
                                    $staff .= get_contact($dbc, $staffid).', ';
                                }
                            }
                            $staff = rtrim($staff, ', ');
                        ?>
                            <tr>
                                <td><?= $row['status'] ?></td>
                                <td><?= $staff ?></td>
                                <td><?= $row['created_date'] ?></td>
                                <td><?= get_contact($dbc, $row['created_by']) ?></td>
                            </tr>
                        <?php } ?>
                        </table>
                    <?php } ?>
                <?php } ?>

                <?php if (strpos($enabled_fields, ',notes,') !== FALSE) { ?>
                    <?php
                        $query = "SELECT * FROM `workorder_comment` WHERE `workorderid` = '$workorderid' AND `type` = 'note' ORDER BY `workordercommid` DESC";
                        $result = mysqli_query($dbc, $query);
                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                    ?>
                        <h5>DELIVERABLES</h5>
                        <table class="table table-bordered">
                            <tr class="hidden-xs hidden-sm">
                                <th>Heading</th>
                                <th>Note</th>
                                <th>Assigned To</th>
                                <th>Date</th>
                                <th>Added By</th>
                            </tr>
                        <?php while($row = mysqli_fetch_array($result)) { ?>
                            <tr>
                                <td><?= $row['note_heading'] ?></td>
                                <td><?= html_entity_decode($row['comment']) ?></td>
                                <td><?= get_contact($dbc, $row['email_comment']) ?></td>
                                <td><?= $row['created_date'] ?></td>
                                <td><?= get_contact($dbc, $row['created_by']) ?></td>
                            </tr>
                        <?php } ?>
                        </table>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <h3>Delivery Summary</h3>

        <div style="height: 40em; overflow-y: auto; padding: 1em;">
            <?php if (strpos($enabled_fields, ',distance,') !== FALSE) { ?>
                <label class="super-label">Distance:
                <div class="form-input">
                    <input type="text" name="distance" value="<?= $distance ?>" class="form-control">
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',num_items,') !== FALSE) { ?>
                <label class="super-label">Number of Pieces:
                <div class="form-input">
                    <input type="number" name="num_items" value="<?= $num_items ?>" class="form-control">
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',item_description,') !== FALSE) { ?>
                <label class="super-label">Piece Description:
                <div class="form-input">
                    <textarea name="item_description" class="form-control"><?= html_entity_decode($item_description) ?></textarea>
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',exchange_product,') !== FALSE) { ?>
                <label class="super-label" style="padding-bottom: 1em;">Exchange Product: &nbsp;&nbsp;
                <div class="form-input" style="display: inline;">
                    <input type="checkbox" name="exchange_product" value="1" <?= ($exchange_product == 1 ? 'checked' : '') ?> style="position: relative; top: 0.4em; transform: scale(1.5)">
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',return_location,') !== FALSE) { ?>
                <label class="super-label">Return Address:
                <div class="form-input">
                    <input type="text" name="return_address" value="<?= $return_address ?>" class="form-control">
                </div></label>

                <label class="super-label">Return City:
                <div class="form-input">
                    <input type="text" name="return_city" value="<?= $return_city ?>" class="form-control">
                </div></label>

                <label class="super-label">Return Postal Code:
                <div class="form-input">
                    <input type="text" name="return_postal_code" value="<?= $return_postal_code ?>" class="form-control">
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',oversized_item,') !== FALSE) { ?>
                <label class="super-label" style="padding-bottom: 1em;">Oversized Item: &nbsp;&nbsp;
                <div class="form-input" style="display: inline;">
                    <input type="checkbox" name="oversized_item" value="1" <?= ($oversized_item == 1 ? 'checked' : '') ?> style="position: relative; top: 0.4em; transform: scale(1.5)">
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',measurement,') !== FALSE) { ?>
                <label class="super-label">Measurement:<br />
                <div class="form-input">
                    <input type="text" name="measure_width" value="<?= $measure_width ?>" class="form-control" style="width: 5em; display: inline;"> <span style="font-size: 2em; vertical-align: bottom;">W </span>
                    <input type="text" name="measure_height" value="<?= $measure_height ?>" class="form-control" style="width: 5em; display: inline;"> <span style="font-size: 2em; vertical-align: bottom;">H </span>
                    <input type="text" name="measure_depth" value="<?= $measure_depth ?>" class="form-control" style="width: 5em; display: inline;"> <span style="font-size: 2em; vertical-align: bottom;">D </span>
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',description,') !== FALSE) { ?>
                <label class="super-label">Description:
                <div class="form-input">
                    <textarea name="assign_work" class="form-control"><?= html_entity_decode($assign_work) ?></textarea>
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',assembly_required,') !== FALSE) { ?>
                <label class="super-label">Pieces Requiring Assembly:
                <div class="form-input">
                    <input type="number" name="assembly_required" value="<?= $assembly_required ?>" class="form-control">
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',estimated_time,') !== FALSE) { ?>
                <label class="super-label">Estimated Time:
                <div class="form-input">
                    <select style="width: 5em; display: inline;" name="max_time_hour" class="chosen-select-deselect1 form-control">
                        <?php
                        for($i = 0; $i < 40; $i++) {
                            if($i < 10) {
                                $i = '0'.$i;
                            }
                            echo '<option value="'.$i.'"'.($max_time[0] == $i ? ' selected' : '').'>'.$i.'</option>';
                        }
                        ?>
                    </select>
                    <span style="font-size: 1em; vertical-align: bottom;">Hours </span>
                    <select style="width: 5em; display: inline;" name="max_time_minute" class="chosen-select-deselect1 form-control">
                        <?php
                        for($i = 0; $i < 60; $i++) {
                            if($i < 10) {
                                $i = '0'.$i;
                            }
                            echo '<option value="'.$i.'"'.($max_time[1] == $i ? ' selected' : '').'>'.$i.'</option>';
                        }
                        ?>
                    </select>
                    <span style="font-size: 1em; vertical-align: bottom;">Minutes </span>
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',assign_staffid,') !== FALSE) { ?>
                <label class="super-label">Staff:
                <div class="form-input">
                    <select multiple data-placeholder="Select Staff" name="contactid[]" class="chosen-select-deselect form-control">
                        <?php
                            $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Staff' AND `deleted` = 0 AND `status` = 1"),MYSQLI_ASSOC));
                            foreach ($query as $id) {
                                echo '<option value="'.$id.'"'.(strpos(','.$contactid.',', ','.$id.',') !== FALSE ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
                            }
                        ?>
                    </select>
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',assign_teamid,') !== FALSE) { ?>
                <label class="super-label">Team:
                <div class="form-input">
                    <select data-placeholder="Select Team" name="assign_teamid" class="chosen-select-deselect form-control">
                        <option></option>
                        <?php
                            $query = "SELECT * FROM `teams` WHERE DATE(`end_date`) >= DATE(CURDATE()) AND `deleted` = 0";
                            $result = mysqli_query($dbc, $query);
                            while ($row = mysqli_fetch_array($result)) {
                                $team_name = '';
                                $team_contacts = trim($row['contactid'], ',');
                                $team_contacts = explode(',', $team_contacts);
                                foreach ($team_contacts as $team_contact) {
                                    $contacts = explode('*#*', $team_contact);
                                    foreach ($contacts as $single_contact) {
                                        $team_name .= get_contact($dbc, $single_contact) . ', ';
                                    }
                                }
                                $team_name = rtrim($team_name, ', ');
                                echo '<option value="'.$row['teamid'].'"'.($row['teamid'] == $assign_teamid ? ' selected' : '').'>'.$team_name.'</option>';
                            }
                        ?>
                    </select>
                </div></label>
            <?php } ?>

            <?php if (strpos($enabled_fields, ',assign_equip_assignid,') !== FALSE) { ?>
                <?php
                $get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"));
                if (!empty($get_field_config)) {
                    $equipment_category = $get_field_config['equipment_category']; ?>

                    <label class="super-label"><?= $equipment_category ?> Assignment:
                    <div class="form-input">
                        <select data-placeholder="Select <?= $equipment_category ?> Assignment" name="assign_equip_assignid" class="chosen-select-deselect">
                            <option></option>
                            <?php
                                $query = "SELECT ea.*, e.* FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE ea.`deleted` = 0 AND DATE(ea.`end_date`) >= DATE(CURDATE()) ORDER BY e.`unit_number`";
                                $result = mysqli_query($dbc, $query);
                                while ($row = mysqli_fetch_array($result)) {
                                    $equipment_name = $equipment_category . ' #' . $row['unit_number'];
                                    echo '<option value="'.$row['equipment_assignmentid'].'"'.($row['equipment_assignmentid'] == $assign_equip_assignid ? ' selected' : '').'>'.$equipment_name.'</option>';
                                }
                            ?>
                        </select>
                    </div></label>
                <?php } ?>
            <?php } ?>

            <div class="summary_edit"<?php if ($action != 'edit') { echo ' style="display:none;"'; } ?>>
                <?php if (strpos($enabled_fields, ',deliverables,') !== FALSE) { ?>
                    <label class="super-label" style="padding-bottom: 1em;">Add Deliverable: &nbsp;&nbsp;
                    <div class="form-input" style="display: inline;">
                        <input type="checkbox" name="add_deliverable" value="1" style="position: relative; top: 0.4em; transform: scale(1.5)" onclick="addDeliverable(this)">
                    </div></label>

                    <div id="deliverables" style="display: none;">
                        <label class="super-label">Status:
                        <div class="form-input">
                            <select data-placeholder="Select a Status" name="status" class="chosen-select-deselect form-control">
                                <option></option>
                                <?php
                                    $workorder_status = get_config($dbc, 'workorder_status');
                                    $workorder_status = explode(',', $workorder_status);
                                    foreach ($workorder_status as $single_status) {
                                        echo '<option value="'.$single_status.'"'.($single_status == $status ? ' selected' : '').'>'.$single_status.'</option>';
                                    }
                                ?>
                            </select>
                        </div></label>

                        <label class="super-label">Deliverable Date:
                        <div class="form-input">
                            <input name="deliverable_date" value="<?= $deliverable_date ?>" type="text" class="form-control datepicker">
                        </div></label>

                        <?php
                        $sender = get_contact($dbc, $_SESSION['contactid'], 'email_address');
                        $subject = 'FFM - Work Order Assigned To You';
                        $body = 'FFM - Work Order Assigned To You.<br/><br/>
                            <a target="_blank" href="'.WEBSITE_URL.'/Work Order/add_workorder.php?workorderid=[WORKORDERID]">Work Order #[WORKORDERID]</a><br/><br/><br/>
                            <img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">';
                        ?>

                        <label class="super-label">Sending Email Address:
                        <div class="form-input">
                            <input name="deliverable_email_sender" value="<?= $sender ?>" type="text" class="form-control">
                        </div></label>

                        <label class="super-label">Email Subject:
                        <div class="form-input">
                            <input name="deliverable_email_subject" value="<?= $subject ?>" type="text" class="form-control">
                        </div></label>

                        <label class="super-label">Email Body:
                        <div class="form-input">
                            <textarea name="deliverable_email_body" class="form-control"><?= $body ?></textarea>
                        </div></label>
                    </div>
                <?php } ?>

                <?php if (strpos($enabled_fields, ',notes,') !== FALSE) { ?>
                    <label class="super-label" style="padding-bottom: 1em;">Add Note: &nbsp;&nbsp;
                    <div class="form-input" style="display: inline;">
                        <input type="checkbox" name="add_note" value="1" style="position: relative; top: 0.4em; transform: scale(1.5)" onclick="addNote(this)">
                    </div></label>

                    <div id="notes" style="display: none;">
                        <label class="super-label">Note Heading:
                        <div class="form-input">
                            <input name="note_heading" type="text" class="form-control">
                        </div></label>

                        <label class="super-label">Note:
                        <div class="form-input">
                            <textarea name="workorder_comment" class="form-control"></textarea>
                        </div></label>

                        <label class="super-label">Assign/Email To:</label>
                        <div class="form-input">
                            <select data-placeholder="Select Staff" name="note_email_staff" class="chosen-select-deselect form-control">
                                <option></option>
                                <?php
                                    $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Staff' AND `deleted` = 0 AND `status` = 1"),MYSQLI_ASSOC));
                                    foreach ($query as $id) {
                                        echo '<option value="'.$id.'">'.get_contact($dbc, $id).'</option>';
                                    }
                                ?>
                            </select>
                        </div>

                        <label class="super-label" style="padding-bottom: 1em;">Send Email: &nbsp;&nbsp;
                        <div class="form-input" style="display: inline;">
                            <input type="checkbox" name="note_send_email" value="1" style="position: relative; top: 0.4em; transform: scale(1.5)" onclick="noteSendEmail(this)">
                        </div></label>

                        <?php
                        $sender = get_contact($dbc, $_SESSION['contactid'], 'email_address');
                        $subject = 'Note added on Work Order for you to review.';
                        $body = 'Note : [NOTE]<br><br>
                            Please click below Work Order link to view all information.<br>
                            Work Order : <a target="_blank" href="'.WEBSITE_URL.'/Work Order/add_workorder.php?workorderid=[WORKORDERID]">Click Here</a><br>';
                        ?>

                        <div id="notes_email" style="display: none;">
                            <label class="super-label">Sending Email Address:
                            <div class="form-input">
                                <input name="note_email_sender" value="<?= $sender ?>" type="text" class="form-control">
                            </div></label>

                            <label class="super-label">Email Subject:
                            <div class="form-input">
                                <input name="note_email_subject" value="<?= $subject ?>" type="text" class="form-control">
                            </div></label>

                            <label class="super-label">Email Body:
                            <div class="form-input">
                                <textarea name="note_email_body" class="form-control"><?= $body ?></textarea>
                            </div></label>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="pull-right" style="padding-top: 1em;">
        <button id="cancel_btn" class="btn brand-btn" value="cancel" onclick="cancelWorkOrder(); return false;">Cancel</a>
        <button type="submit" id="submit_btn" name="submit_workorder" value="submit_workorder" class="btn brand-btn" style="display:none;">Submit</button>
        <button id="edit_btn" class="btn brand-btn" onclick="editWorkOrder(); return false;">Edit</button>
    </div>
</form>

<div style="display:none;"><?php include('../footer.php'); ?></div>