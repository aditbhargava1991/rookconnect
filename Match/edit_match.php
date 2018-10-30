<?php
/*
Add Vendor
*/
include ('../include.php');
checkAuthorised('match');
if (isset($_POST['add_match'])) {

    $support_contact_category = implode(',',$_POST['support_contact_category']);
    $support_contact = implode(',', $_POST['support_contact']);
    $staff_contact_category = $_POST['staff_contact_category'];
    $staff_contact = implode(',', $_POST['staff_contact']);
    $match_date = $_POST['match_date'];
    $follow_up_date = $_POST['follow_up_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $tile_list = filter_var(implode('#',$_POST['tile_list']),FILTER_SANITIZE_STRING);

    if(empty($_POST['matchid'])) {
        $history = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added on '.date('Y-m-d H:i:s').'<br>';

        $query_insert_vendor = "INSERT INTO `match_contact` (`support_contact_category`, `support_contact`, `staff_contact_category`, `staff_contact`, `tile_list`, `match_date`, `follow_up_date`, `end_date`, `status`, `history`) VALUES ('$support_contact_category', '$support_contact', '$staff_contact_category', '$staff_contact', '$tile_list', '$match_date', '$follow_up_date', '$end_date', '$status', '$history')";

        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $matchid = mysqli_insert_id($dbc);

        if($status == 'Archive') {
            $date_of_archival = date('Y-m-d');
        $query_update_vendor = "UPDATE `match_contact` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `matchid` = '$matchid'";
            $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        }

        $url = 'Added';
    } else {
        $matchid = $_POST['matchid'];
        $history = check_history($dbc, $matchid);
        $query_update_vendor = "UPDATE `match_contact` SET `support_contact_category` = '$support_contact_category', `support_contact` = '$support_contact', `staff_contact_category` = '$staff_contact_category', `staff_contact` = '$staff_contact', `tile_list` = '$tile_list', `match_date` = '$match_date', `follow_up_date` = '$follow_up_date', `end_date` = '$end_date', `status` = '$status', `history` = CONCAT(IFNULL(history, ''), '$history') WHERE `matchid` = '$matchid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);

        if($status == 'Archive') {
           $date_of_archival = date('Y-m-d');
         $query_update_vendor = "UPDATE `match_contact` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `matchid` = '$matchid'";
            $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        }
        $url = 'Updated';
    }

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    if($_POST['from_tile'] == 'contacts') {
        echo '<script type+"text/javascript"> window.parent.reload_match(); </script>';
    }
    echo '<script type="text/javascript"> window.location.replace("?"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
}

?>
<script type="text/javascript">
$(document).ready(function() {

    $("#isp_quality").change(function() {
        if($( "#isp_quality option:selected" ).text() == 'Other') {
                $( "#isp_quality_name" ).show();
        } else {
            $( "#isp_quality_name" ).hide();
        }
    });

    $("#isp_sis").change(function() {
        if($( "#isp_sis option:selected" ).text() == 'Other') {
                $( "#isp_sis_name" ).show();
        } else {
            $( "#isp_sis_name" ).hide();
        }
    });

    $("#isp_goals").change(function() {
        if($( "#isp_goals option:selected" ).text() == 'Other') {
                $( "#isp_goals_name" ).show();
        } else {
            $( "#isp_goals_name" ).hide();
        }
    });

    $("#form1").submit(function( event ) {
        var medication_type = $("#medication_type").val();
        var category = $("input[name=category]").val();
        var title = $("input[name=title]").val();
        if (medication_type == '' || category == '' || title == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });

    $('select[name="support_contact_category[]"]').change();
});
$(document).on('change', 'select[name="support_contact_category[]"]', function() { selectContactCategory(this); });

function selectContactCategory(sel) {
	var stage = [];
    $(sel).find('option:selected').each(function() {
        stage.push(this.value);
    });
    stage = stage.join(',');
	var typeId = sel.id;
	var arr = typeId.split('_');
    var selected_contacts = [];
    $("#contact_"+arr[2]+" option:selected").each(function() {
        selected_contacts.push($(this).val());
    });
    selected_contacts = selected_contacts.join(',');
	$.ajax({
		type: "POST",
		url: "isp_ajax_all.php?fill=contact_category&category="+stage,
        data: { selected_contacts: selected_contacts },
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#contact_"+arr[2]).html(response);
			$("#contact_"+arr[2]).trigger("change.select2");
		}
	});
}
</script>

<?php if(!IFRAME_PAGE) { ?>
    <div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
        <ul>
            <a href="?"><li>Back to Dashboard</li></a>
            <a href="?" onclick="return false;"><li class="active blue">Match Details</li></a>
        </ul>
    </div>
<?php } ?>

<div class="scale-to-fill has-main-screen">
    <div class="main-screen standard-body form-horizontal">
        <div class="standard-body-title">
            <h3><?= empty($_GET['edit']) ? 'Add' : 'Edit' ?> Match</h3>
        </div>

        <div class="standard-body-content" style="padding: 1em;">

            <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
                <input type="hidden" name="from_tile" value="<?= $_GET['from_tile'] ?>">
                <?php
                $support_contact_category = get_contact($dbc, $_GET['support_contact'], 'category');
                $support_contact = [$_GET['support_contact']];
                $staff_contact_category = '';
                $staff_contact = '';
                $match_date = '';
                $follow_up_date = '';
                $end_date = '';
                $status = '';
                $tile_list = [];

                if(!empty($_GET['edit'])) {
                    $matchid = $_GET['edit'];
                    $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM match_contact WHERE matchid='$matchid'"));

                    $support_contact_category = $get_contact['support_contact_category'];
                    $support_contact = explode(',', $get_contact['support_contact']);
                    $staff_contact_category = $get_contact['staff_contact_category'];
                    $staff_contact = explode(',', $get_contact['staff_contact']);
                    $match_date = $get_contact['match_date'];
                    $follow_up_date = $get_contact['follow_up_date'];
                    $end_date = $get_contact['end_date'];
                    $status = $get_contact['status'];
                    $tile_list = array_filter(explode('#',$get_contact['tile_list']));
                    ?>
                    <input type="hidden" id="matchid" name="matchid" value="<?php echo $matchid ?>" />
                <?php } ?>

                <div class="form-group clearfix">
                    <label class="col-sm-4 control-label">Staff / User:</label>
                    <div class="col-sm-8">
                        <select name="staff_contact[]" multiple data-placeholder="Select Staff..." class="chosen-select-deselect form-control">
                            <?php $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE ((`category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.") OR (`category` NOT IN (".STAFF_CATS.") AND IFNULL(`user_name`,'') != '')) AND `deleted`=0 AND `status`>0 AND `show_hide_user`=1"));
                            foreach($staff_list as $staffid) {
                                echo '<option value="'.$staffid['contactid'].'" '.(in_array($staffid['contactid'], $staff_contact) ? 'selected' : '').'>'.$staffid['full_name'].'</option>';
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="col-sm-4 control-label">Contact Category:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Select Category..." id="contact_category_0" name="support_contact_category[]" multiple class="chosen-select-deselect form-control" width="380">
                            <option value=""></option>
                            <?php
                            $each_tab = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`"),MYSQLI_ASSOC),'category');
                            foreach ($each_tab as $cat_tab) {
                                ?>
                                <option <?= strpos(','.$support_contact_category.',', ','.$cat_tab.',') !== FALSE ? 'selected' : '' ?> value="<?= $cat_tab ?>"><?= $cat_tab ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label for="fax_number" class="col-sm-4 control-label">Contact:</label>
                    <div class="col-sm-8">
                        <select multiple data-placeholder="Select Contact..." name="support_contact[]" id="contact_0" class="chosen-select-deselect form-control" width="380">
                            <?php if(!empty($support_contact)) {
                                $query = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN ('".implode("','",explode(',',$support_contact_category))."') AND `deleted`=0 AND `status`=1"));
                                foreach($query as $row) {
                                    echo '<option value="'.$row['contactid'].'" '.(in_array($row['contactid'], $support_contact) ? 'selected' : '').'>'.$row['full_name'].'</option>';
                                }
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label for="match_date" class="col-sm-4 control-label">Match Start Date:</label>
                    <div class="col-sm-8">
                        <input name="match_date" value="<?php echo $match_date; ?>" type="text" class="datepicker form-control">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label for="follow_up_date" class="col-sm-4 control-label">Match Follow Up Date:</label>
                    <div class="col-sm-8">
                        <input name="follow_up_date" value="<?php echo $follow_up_date; ?>" type="text" class="datepicker form-control">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label for="end_date" class="col-sm-4 control-label">Match End Date:</label>
                    <div class="col-sm-8">
                        <input name="end_date" value="<?php echo $end_date; ?>" type="text" class="datepicker form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="travel_task" class="col-sm-4 control-label">Status:</label>
                    <div class="col-sm-8">
                        <select id="status" name="status" class="chosen-select-deselect form-control" width="380">
                            <option value=''></option>
                            <option value='Suspend' <?= $status == 'Suspend' ? 'selected' : '' ?>>Suspend</option>
                            <option value='Active' <?= $status == 'Active' ? 'selected' : '' ?>>Active</option>
                            <option value='Archive' <?= $status == 'Archive' ? 'selected' : '' ?>>Archive</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-4 control-label">Match Tiles:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="tile_list[]" value="" <?= empty($tile_list) ? 'checked' : '' ?> onclick="if(this.checked) { $('[name^=tile_list]').not(this).removeAttr('checked'); }"> All Tiles</label>
                        <?php foreach($_SESSION['tile_list'] as $tile_data) {
                            if(in_array($tile_data['tile'],['posadvanced','check_out','accounts_receivables','project','ticket'])) { ?>
                                <label class="form-checkbox"><input type="checkbox" name="tile_list[]" value="<?= $tile_data['tile'] ?>" <?= in_array($tile_data['tile'],$tile_list) ? 'checked' : '' ?> onclick="if(this.checked) { $('[name^=tile_list]').first().removeAttr('checked'); }"> <?= $tile_data['key'] ?></label>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-4">
                        <p><span class="hp-red pull-left"><em>Required Fields *</em></span></p>
                    </div>
                    <div class="col-sm-8">
                        <button type="submit" name="add_match" value="Submit" class="btn brand-btn pull-right">Submit</button>
                        <a href="?" class="btn brand-btn pull-right">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include ('../footer.php'); ?>

<?php
function check_history($dbc, $matchid) {
    $match = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE `matchid` = '$matchid'"));

    $support_contact_category = $_POST['support_contact_category'];
    $support_contact = implode(',', $_POST['support_contact']);
    $staff_contact_category = $_POST['staff_contact_category'];
    $staff_contact = implode(',', $_POST['staff_contact']);
    $match_date = $_POST['match_date'];
    $follow_up_date = $_POST['follow_up_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $tile_list = implode('#',$_POST['tile_list']);

    $history = '';

    if ($match['staff_contact_category'] != $staff_contact_category) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Staff Category on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['staff_contact'] != $staff_contact) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Staff on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['support_contact_category'] != $support_contact_category) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Contact Category on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['support_contact'] != $support_contact) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Contact on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['match_date'] != $match_date) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Match Start Date on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['follow_up_date'] != $follow_up_date) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Match Follow Up Date on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['end_date'] != $end_date) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Match End Date on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['status'] != $status) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Status on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['tile_list'] != $tile_list) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Match Tiles on '.date('Y-m-d H:i:s').'<br>';
    }

    return $history;
}
?>