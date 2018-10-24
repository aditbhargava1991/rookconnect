<?php
/*
 * This should be called from everywhere there is a quick action to add reminders
 * Accept the Tile name in a $_GET['tile']
 */

include_once('../include.php');
checkAuthorised();
$html = '';

//$id = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
if(isset($_GET['newsboardid']) && $_GET['newsboardid'] != '') {
	$newsboardid = $_GET['newsboardid'];
}
else {
	$newsboardid = 0;
}

$boardid = $_GET['boardid'];
if(isset($_POST['submit'])) {
	$staff = implode("," , $_POST['tag_staff']);
	$select_query = "SELECT newsboard_tagid, staff from newsboard_tag where boardid=$boardid and newsboardid=$newsboardid";
	$select_newsboard = mysqli_fetch_assoc(mysqli_query($dbc, $select_query));
	if(!empty(array_filter($select_newsboard))) {
		$staff_array = explode(",", $select_newsboard['staff']);
		$update_staff = implode(",", array_unique($_POST['tag_staff']));
		$query = "UPDATE newsboard_tag set staff = '$update_staff' where boardid=$boardid and newsboardid=$newsboardid";
	}
	else {
		$query = "INSERT INTO newsboard_tag(`boardid`, `newsboardid`, `staff`) VALUES('$boardid', '$newsboardid', '$staff')";
	}

	mysqli_query($dbc, $query);
} ?>
<div class="container">
	<div class="row">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
					<?php if(isset($_GET['newsboardid']) && $_GET['newsboardid'] != ''): ?>
        		<h3 class="inline">Tag Staff to Post</h3>
					<?php else: ?>
						<h3 class="inline">Tag Staff to Board</h3>
					<?php endif; ?>
            <div class="pull-right gap-top"><a href=""><img src="../img/icons/cancel.png" alt="Close" title="Close" class="inline-img" /></a></div>
            <div class="clearfix"></div>
            <div class="double-gap-top form-group">
                <label class="col-sm-4 control-label">Select Staff:</label>
                <div class="col-sm-8">
										<?php
											$select_query = "SELECT staff from newsboard_tag where boardid=$boardid and newsboardid=$newsboardid";
											$select_newsboard = explode(",", mysqli_fetch_assoc(mysqli_query($dbc, $select_query))['staff']);
										?>
                    <select name="tag_staff[]" multiple class="chosen-select-deselect form-control">
                        <?php $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));
                        foreach($staff_list as $staff) {
                            if(!empty($staff['full_name']) && $staff['full_name'] != '-') { ?>
                                <option <?php if(in_array($staff['contactid'], $select_newsboard)) { echo "selected"; } ?> value="<?= $staff['contactid']; ?>"><?= $staff['full_name'] ?></option>
                            <?php }
                        } ?>
                    </select>
                </div>
            </div>
						<div class="form-group pull-right">
	        		<a href="" class="btn brand-btn">Cancel</a>
	        		<button type="submit" name="submit" value="Tag_Submit" class="btn brand-btn">Submit</button>
	        	</div>
				</form>
    </div>
</div>
