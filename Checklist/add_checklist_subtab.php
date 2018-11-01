<?php
include ('../include.php');
error_reporting(0);

if (isset($_POST['submit'])) {
    if ($_POST['action'] == 'edit') {
        $subtab_name = $_POST['subtab'];
        $subtab_shared = ','.implode(',',$_POST['subtab_shared']).',';
        $subtabid = $_POST['subtabid'];

        $before_change = capture_before_change($dbc, 'checklist_subtab', 'name', 'subtabid', $subtabid);
        $before_change .= capture_before_change($dbc, 'checklist_subtab', 'shared', 'subtabid', $subtabid);
        $query_update_subtab = "UPDATE `checklist_subtab` SET `name` = '$subtab_name', `shared` = '$subtab_shared' WHERE `subtabid` = '$subtabid'";
        $result_update_subtab = mysqli_query($dbc, $query_update_subtab);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist Sub Tab <b>'.$subtab_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '', '$subtab_name', '', '', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

        $start_word = strpos($report, "Updated");
        $end_word = strpos($report, " on");
        $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
        add_update_history($dbc, 'checklist_history', $history, '', $before_change);


    } else {
        $subtab_name = $_POST['new_subtab'];
        $subtab_shared = ','.implode(',',$_POST['subtab_shared']).',';
        $created_by = $_SESSION['contactid'];

        $query_insert_subtab = "INSERT INTO `checklist_subtab` (`name`, `shared`, `created_by`) VALUES ('$subtab_name', '$subtab_shared', '$created_by')";
        $result_insert_subtab = mysqli_query($dbc, $query_insert_subtab);

        $subtabid = mysqli_insert_id($dbc);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added Checklist Sub Tab <b>'.$subtab_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '', '$subtab_name', '', '', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

        $before_change = '';
        $start_word = strpos($report, "Updated");
        $end_word = strpos($report, " on");
        $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
        add_update_history($dbc, 'checklist_history', $history, '', $before_change);

        $update_tab_config = ",".$subtabid."_ongoing,".$subtabid."_daily,".$subtabid."_weekly,".$subtabid."_monthly";
        foreach ($_POST['subtab_shared'] as $tabs_config_row) {
            $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'checklist_tabs_" . $tabs_config_row . "' FROM (SELECT COUNT(*) numrows FROM `general_configuration` WHERE `name`='checklist_tabs_" . $tabs_config_row . "') current_config WHERE numrows=0");
            $query_tab_config = "UPDATE `general_configuration` SET `value` = CONCAT(`value`, '$update_tab_config') WHERE `name` = 'checklist_tabs_" . $tabs_config_row . "'";
            $result_tab_config = mysqli_query($dbc, $query_tab_config);
        }
        $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'checklist_tabs_" . $_SESSION['contactid'] . "' FROM (SELECT COUNT(*) numrows FROM `general_configuration` WHERE `name`='checklist_tabs_" . $_SESSION['contactid'] . "') current_config WHERE numrows=0");
        $query_tab_config = "UPDATE `general_configuration` SET `value` = CONCAT(`value`, '$update_tab_config') WHERE `name` = 'checklist_tabs_" . $_SESSION['contactid'] . "'";
        $result_tab_config = mysqli_query($dbc, $query_tab_config);
    }

    echo '<script type="text/javascript"> window.location.replace("checklist.php?subtabid='.$subtabid.'"); </script>';
}

?>

<script type="text/javascript">
$(document).on('change.select2', 'select[name="subtab_shared[]"]', function() { changeSubTabShared(this); });
function changeSubTabShared(sel) {
    if($(sel).find('option[value="ALL"]').is(':selected')) {
        $(sel).find('option').attr('selected','selected');
        $(sel).find('option[value="ALL"]').removeAttr('selected');
        $(sel).trigger('change.select2');
    }
}
</script>

</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('checklist');
$tab_list = get_config($dbc, 'checklist_tabs_' . $_SESSION['contactid']); ?>
<div class="container">
    <div class="row">

    <h1><?php echo !empty($_GET['subtabid']) ? 'Edit' : 'Add'; ?> Checklist Sub Tab</h1>
    <div class="gap-top double-gap-bottom"><a href="checklist.php" class="btn config-btn">Back to Dashboard</a></div>

    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
    if(!empty($_GET['subtabid'])) {
        $subtabid = $_GET['subtabid'];
        $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_subtab` WHERE `subtabid` = '$subtabid'"));
        $subtab_name = $get_subtab['name'];
        $subtab_shared = $get_subtab['shared'];

        echo '<input type="hidden" name="action" value="edit" />';
        echo '<input type="hidden" name="subtabid" value="'.$subtabid.'" />';
        echo '
            <div class="form-group clearfix subtab">
                <label for="new_subtab" class="col-sm-4 control-label text-right">
                    Sub Tab:
                </label>
                <div class="col-sm-8">
                    <input type="text" name="subtab" class="form-control" width="380" value="'.$subtab_name.'"" />
                </div>
            </div>';
    } else {
        echo '<input type="hidden" name="action" value="add" />';
        echo '
            <div class="form-group clearfix new_subtab">
                <label for="new_subtab" class="col-sm-4 control-label text-right">
                    New Sub Tab:
                </label>
                <div class="col-sm-8">
                    <input type="text" name="new_subtab" class="form-control" width="380" />
                </div>
            </div>';
    }
    ?>

    <div class="form-group clearfix">
        <label for="subtab_shared" class="col-sm-4 control-label text-right">
            <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Choose the contacts that you would like to share this Sub Tab with."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            Sub Tab Shared Contacts:
        </label>
        <div class="col-sm-8">
            <select name="subtab_shared[]" multiple data-placeholder="Select Shared Contacts..." class="chosen-select-deselect form-control">
                <option value='ALL'>Share with Everyone</option>
                <?php
                $cat = '';
                $query1 = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status>0 ORDER BY category"), MYSQLI_ASSOC));
                foreach($query1 as $row1) {
                    echo "<option ".((strpos($subtab_shared, ','.$row1.',') !== false) ? 'selected' : '')." value='". $row1."'>".get_contact($dbc, $row1).'</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group clearfix">
        <div class="col-sm-6">
            <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="If you click this, the current Sub Tab will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a href="checklist.php" class="btn brand-btn btn-lg">Back</a>
        </div>
        <div class="col-sm-6">
            <button name="submit" value="submit" class="btn brand-btn btn-lg pull-right">Submit</button>
            <span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save the Checklist Sub Tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        </div>
    </div>

    </form>

</div>
</div>
<?php include ('../footer.php'); ?>
