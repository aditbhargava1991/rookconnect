<?php include_once('../include.php');
$salesid = filter_var($_GET['salesid'],FILTER_SANITIZE_STRING);
if(isset($_POST['submit'])) {
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
    $assign_to = filter_var(implode(',',$_POST['assign']),FILTER_SANITIZE_STRING);
    $user = $_SESSION['contactid'];
    $dbc->query("INSERT INTO `sales_notes` (`salesid`,`note_heading`,`comment`,`email_comment`,`created_date`,`created_by`) VALUES ('$salesid','General','$comment','$assign_to',DATE(NOW()),'$user')"); ?>
    <script>
    try {
        $(window.top.document).find('iframe[src*=Sales]').get(0).contentWindow.reload_notes();
    } catch(err) { }
    window.parent.reload_notes();
    </script>
    <?php add_update_history($dbc, 'sales_history', "Note added. <br />", '', '', $salesid);

    if($_POST['submit'] == 'email') {
        $sender = filter_var($_POST['sender_address'],FILTER_SANITIZE_STRING);
        $sender_name = filter_var($_POST['sender_name'],FILTER_SANITIZE_STRING);
        $subject = $_POST['subject'];
        $sales = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `sales` WHERE `salesid`='$salesid'"));
        $body = str_replace(['[REFERENCE]','[SALESID]','[LEAD]','[STATUS]'], [html_entity_decode($comment),$salesid,get_contact($dbc, ($sales['contactid'] > 0 ? $sales['contactid'] : $sales['businessid']), 'name_company'),$sales['status']],$_POST['body']);
        foreach(array_filter($_POST['assign']) as $address) {
            $address = get_email($dbc, filter_var($address,FILTER_SANITIZE_STRING));
            try {
                send_email([$sender=>$sender_name], $address, '', '', $subject, $body, '');
            } catch(Exception $e) { echo "Unable to send e-mail: ".$e->getMessage(); }
        }
        $role_address = [];
        foreach(array_filter($_POST['assign_role']) as $role) {
            $role_contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE CONCAT(',',`role`,',') LIKE '%,$role,%' AND `deleted` = 1 AND `status` > 0");
            while($row = mysqli_fetch_assoc($role_contacts)) {
                if(!empty(get_email($dbc, $row['contactid']))) {
                    $role_address[$row['contactid']] = get_email($dbc, $row['contactid']);
                }
            }
        }
        foreach(array_filter($role_address) as $address) {
            try {
                send_email([$sender=>$sender_name], $address, '', '', $subject, $body, '');
            } catch(Exception $e) { echo "Unable to send e-mail: ".$e->getMessage(); }
        }
    }
} ?>
<form class="col-sm-12 form-horizontal" action="" method="POST" enctype="multipart/form-data">
    <a class="pull-right" href="../blank_loading_page.php"><img class="slider-close" src="../img/icons/cancel.png"></a>
    <h2>Add Note</h2>

    <div class="form-group">
        <label for="site_name" class="col-sm-4 control-label">Note:</label>
        <div class="col-sm-12">
            <textarea name="comment" rows="4" cols="50" class="form-control"></textarea>
        </div>
    </div>

    <div class="form-group">
        <label for="site_name" class="col-sm-4 control-label">Send Email:</label>
        <div class="col-sm-8">
            <input type="hidden" name="send_email_on_comment" value="">
            <input type="checkbox" value="Yes" name="check_send_email" onclick="ticket_comment_check_send_email(this);">
        </div>
    </div>

    <div class="form-group">
      <label for="site_name" class="col-sm-4 control-label">Assign<?= (strpos($value_config,',Send Emails,') !== FALSE ? "/Email" : "") ?> To:</label>
      <?php $comment_category = ($comment_type == 'member_note' ? "NOT IN ('Business',".STAFF_CATS.",'Sites')" : " IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""); ?>
      <?php $category = ($comment_type == 'member_note' ? $category : "Staff"); ?>
      <div class="col-sm-8">
        <select data-placeholder="Select <?= $category ?>..." name="assign[]" multiple class="chosen-select-deselect form-control">
            <?php $query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category $comment_category AND CONCAT(IFNULL(`first_name`,''),IFNULL(`last_name`,'')) != '' AND deleted=0 AND `status`>0"));
            foreach($query as $contact) {
                echo "<option value='". $contact['contactid']."'>".$contact['first_name'].' '.$contact['last_name'].'</option>';
            } ?>
        </select>
      </div>
    </div>

    <?php $subject = 'Note added on '.SALES_NOUN.' for you to Review';
    $body = 'The following note has been added on a '.SALES_NOUN.' for you:<br>[REFERENCE]<br />
            Status: [STATUS]<br>
            Please click the '.SALES_NOUN.' link below to view all information.<br>
            <a target="_blank" href="'.WEBSITE_URL.'/Sales/sale.php?id=[SALESID]">'.SALES_NOUN.' #[SALESID]</a><br>'; ?>
    <script>
    function ticket_comment_check_send_email(checked) {
        if(checked.checked) {
            $('[name="send_email_on_comment"]').val('Yes');
            $('.ticket_comment_email_send_div').show();
            $('[name=submit][value=add]').hide();
        } else {
            $('[name="send_email_on_comment"]').val('');
            $('.ticket_comment_email_send_div').hide();
            $('[name=submit][value=add]').show();
        }
    }
    </script>
    <div class="ticket_comment_email_send_div email_div" style="display:none;">
        <div class="form-group">
            <label class="col-sm-4 control-label">Email Sender's Name:</label>
            <div class="col-sm-8">
                <input type="text" name="sender_name" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Email Sender's Address:</label>
            <div class="col-sm-8">
                <input type="text" name="sender_address" class="form-control" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Email Subject:</label>
            <div class="col-sm-8">
                <input type="text" name="subject" class="form-control" value="<?php echo $subject; ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Email Body:</label>
            <div class="col-sm-12">
                <textarea name="body" class="form-control"><?php echo $body; ?></textarea>
            </div>
        </div>
        <button class="btn brand-btn pull-right" name="submit" value="email">Send Email</button>
    </div>
    <a class="btn brand-btn pull-left" href="../blank_loading_page.php">Cancel</a>
    <button class="btn brand-btn pull-right" name="submit" value="add">Add Note</button>
    <div class="clearfix"></div>
</form>
