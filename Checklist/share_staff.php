<?php
/* Share with Staff
 * Called From: overlayIframe
 * Included In: view_checklist.php
 */
include ('../include.php');
error_reporting(0);
?>

<script>
function addStaff() {
    var block = $('div.add_staff').last();
    destroyInputs('.add_staff');
    clone = block.clone();
    clone.find('.form-control').val('');
    block.after(clone);
    initInputs('.add_staff');
}
function removeStaff(button) {
    if($('div.add_staff').length <= 1) {
        addStaff();
    }
    $(button).closest('div.add_staff').remove();
    $('div.add_staff').first().find('[name="contactid"]').change();
}
</script>
</head>

<body>
<?php
    include_once ('../navigation.php');
    checkAuthorised('checklist');
    $checklistid = preg_replace('/[^0-9]/', '', $_GET['edit']);
    $contactid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT assign_staff FROM checklist WHERE checklistid='$checklistid' AND deleted=0"))['assign_staff'];
    
    if ( isset($_POST['submit']) ) {
        $contactids = ','. implode(',', $_POST['contactid']) .',';
        mysqli_query($dbc, "UPDATE checklist SET assign_staff = '$contactids'");
        echo '<script>window.location.href="../blank_loading_page.php";</script>';
    }
?>
<div class="container">
    <div class="row">
        <h3 class="gap-left pull-left double-gap-bottom">Share with Staff</h3>
        <div class="pull-right offset-top-15"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
        <div class="clearfix"></div>

        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <?php foreach(explode(',',trim($contactid,',')) as $line_contactid) { ?>
                <div class="add_staff">
                    <div class="clearfix"></div>
                    <div class="col-xs-10">
                        <select data-placeholder="Select a Staff..." id="contactid" name="contactid[]" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," class="chosen-select-deselect form-control email_recipient" width="380">
                          <option value=""></option>
                          <?php $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
                            foreach($staff_query as $row) { ?>
                                <option <?php if ($line_contactid == $row['contactid']) {
                                echo " selected"; } ?> value="<?php echo $row['contactid']; ?>"><?php echo $row['first_name'].' '.$row['last_name']; ?></option>
                            <?php }
                          ?>
                        </select>
                    </div>

                    <div class="col-xs-2">
                        <img class="inline-img pull-right cursor-hand" onclick="removeStaff(this);" src="../img/remove.png" />
                        <img class="inline-img pull-right cursor-hand" onclick="addStaff(this);" src="../img/icons/ROOK-add-icon.png" />
                    </div>
                </div>
            <?php } ?>
            
            <div class="clearfix"></div>
            
            <div class="form-group double-gap-top">
                <input type="submit" name="submit" value="Submit" class="btn brand-btn pull-right" />
                <a href="" class="btn brand-btn pull-right">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include ('../footer.php'); ?>