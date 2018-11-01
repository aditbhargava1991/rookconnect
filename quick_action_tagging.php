<?php
/*
 * This should be called from everywhere there is a quick action to add reminders
 * Accept the Tile name in a $_GET['tile']
 */

include_once('include.php');
checkAuthorised();
$html = '';

$id = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
if(isset($_POST['submit'])) {
	$contactid = $_SESSION['contactid'];
	$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
    $date = date('Y-m-d');

    switch ($tile) {
        case 'incident_report':
            $staffids = [];
            foreach($_POST['tagged_staff'] as $tagged_staff) {
                if($tagged_staff > 0) {
                    $staffids[] = $tagged_staff;
                    $already_tagged = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_tagging` WHERE `src_table` = 'incident_report' AND `item_id` = '$id' AND `contactid` = '$tagged_staff' AND `deleted` = 0"));
                    if(empty($already_tagged)) {
                        mysqli_query($dbc, "INSERT INTO `contacts_tagging` (`contactid`, `src_table`, `item_id`, `last_updated_date`) VALUES ('$tagged_staff', 'incident_report', '$id', '$date')");
                    }
                }
            }
            if(!empty($staffids)) {
                mysqli_query($dbc, "UPDATE `contacts_tagging` SET `deleted` = 1 WHERE `src_table` = 'incident_report' AND `item_id` = '$id' AND `contactid` NOT IN (".implode(',',$staffids).")");
            } else {
                mysqli_query($dbc, "UPDATE `contacts_tagging` SET `deleted` = 1 WHERE `src_table` = 'incident_report' AND `item_id` = '$id'");
            }
            break;

        default:
            break;
    }
} ?>

<script type="text/javascript">
function add_tag() {
    destroyInputs('.tagged_row');
    var block = $('.tagged_row').last();
    var clone = $(block).clone();
    clone.find('select').val('');
    $(block).after(clone);
    initInputs('.tagged_row');
}
function remove_tag(img) {
    if($('.tagged_row').length <= 1) {
        add_tag();
    }
    $(img).closest('.tagged_row').remove();
}
</script>

<?php $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1")); ?>
<div class="container">
	<div class="row">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        	<h3 class="inline">Tag Staff</h3>
            <div class="pull-right gap-top"><a href=""><img src="../img/icons/cancel.png" alt="Close" title="Close" class="inline-img" /></a></div>
            <div class="clearfix"></div>
            <hr />

            <input type="hidden" name="tile" value="<?= $_GET['tile'] ?>" />

            <div id="no-more-tables">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="hidden-xs hidden-sm">
                            <th>Tagged Staff</th>
                            <th>Function</th>
                        </tr>
                    </thead>
                    <?php $sql = mysqli_query($dbc, "SELECT * FROM `contacts_tagging` WHERE `src_table` = '".$_GET['tile']."' AND `item_id` = '".$_GET['id']."' AND '".$_GET['id']."' > 0 AND `deleted` = 0");
                    $row = mysqli_fetch_assoc($sql);
                    do { ?>
                        <tr class="tagged_row">
                            <td data-title="Tagged Staff">
                                <select name="tagged_staff[]" data-placeholder="Select a Staff..." class="chosen-select-deselect">
                                    <option></option>
                                    <?php foreach($staff_list as $staff) {
                                        echo '<option value="'.$staff['contactid'].'" '.($row['contactid'] == $staff['contactid'] ? 'selected' : '').'>'.$staff['full_name'].'</option>';
                                    } ?><
                                </select>
                            </td>
                            <td data-title="Function">
                                <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_tag();">
                                <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_tag(this);">
                            </td>
                        </tr>
                    <?php } while($row = mysqli_fetch_assoc($sql)); ?>
                </table>
            </div>

        	<div class="form-group pull-right">
        		<a href="" class="btn brand-btn">Back</a>
        		<button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
        	</div>
        </form>
    </div>
</div>