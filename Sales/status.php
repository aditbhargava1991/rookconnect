<!-- Sales Status --><?php error_reporting(0);
include_once('../include.php');
checkAuthorised('sales');
$statuses = get_config($dbc, 'sales_lead_status');
$next_actions = get_config($dbc, 'sales_next_action');
$approvals = approval_visible_function($dbc, 'sales');
$project_security = get_security($dbc, 'project');
$estimates_active = tile_enabled($dbc, 'estimate')['user_enabled'];
$flag_colours = explode(',', get_config($dbc, "ticket_colour_flags"));
$flag_labels = explode('#*#', get_config($dbc, "ticket_colour_flag_names"));
$staff_list = sort_contacts_query($dbc->query("SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
$filter = "`deleted` = 0 ";
if(isset($_GET['s'])) {
	$filter .= " AND `status`='".filter_var($_GET['s'],FILTER_SANITIZE_STRING)."'";
    $page_title = $_GET['s'];
} else if(isset($_GET['r'])) {
	$filter .= " AND `region`='".filter_var($_GET['r'],FILTER_SANITIZE_STRING)."'";
    $page_title = $_GET['r'];
} else if(isset($_GET['l'])) {
	$filter .= " AND `location`='".filter_var($_GET['l'],FILTER_SANITIZE_STRING)."'";
    $page_title = $_GET['l'];
} else if(isset($_GET['c'])) {
	$filter .= " AND `classification`='".filter_var($_GET['c'],FILTER_SANITIZE_STRING)."'";
    $page_title = $_GET['c'];
} else if(isset($_GET['contactid'])) {
    $filter .= " AND (CONCAT(',',`share_lead`,',') LIKE '%,".filter_var($_GET['contactid'],FILTER_SANITIZE_STRING).",%' OR `primary_staff` = '".filter_var($_GET['contactid'],FILTER_SANITIZE_STRING)."')";
    $page_title = get_contact($dbc, $_GET['contactid']);
} ?>
<script type="text/javascript">
$(document).on('change', 'select[name="status"]', function() { changeLeadStatus(this); });
$(document).on('change', 'select[name="next_action"]', function() { changeLeadNextAction(this); });
</script>
<?php
$leads  = mysqli_query($dbc, "SELECT * FROM `sales` WHERE ".$filter.$query_mod);
    echo '<div class="main-screen-white horizontal-scroll standard-dashboard-body" style="border: none; background: none;">';
    echo '<div class="standard-dashboard-body-title"><h3>'.$page_title.'</h3></div>';
if ( $leads->num_rows > 0 ) {
    $i = 1;
    while ( $row=mysqli_fetch_assoc($leads) ) {
		$flag_colour = $flag_label = '';
		if(!empty($row['flag_label'])) {
			$flag_colour = $row['flag_colour'];
			$flag_label = $row['flag_label'];
		} else if(!empty($row['flag_colour'])) {
			$flag_colour = $row['flag_colour'];
            $flag_label_row = array_search($row['flag_colour'], $flag_colours);
            if($flag_label_row !== FALSE) {
                $flag_label = $flag_labels[$flag_label_row];
            }
		} ?>
        <div class="main-screen-white silver-border gap-bottom info-block-detail" data-id="<?= $row['salesid'] ?>" style="height:auto; <?= empty($flag_colour) ? '' : 'background-color: #'.$flag_colour ?>" data-searchable="<?= get_client($dbc, $row['businessid']); ?> <?= get_contact($dbc, $row['contactid']); ?>">
            <div class="col-xs-12 gap-top horizontal-block-container">
                <div class="horizontal-block">
                    <div class="horizontal-block-header">
						<span class="flag-label"><?= $flag_label ?></span>
                        <h4 class="col-md-6"><a href="sale.php?p=preview&id=<?= $row['salesid'] ?>">Sales Lead <?= $i; ?> <img class="inline-img" src="../img/icons/ROOK-edit-icon.png"></a></h4>
                        <div class="col-md-6"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="horizontal-block-details">
                        <div class="col-xs-12 col-md-4">
                            <div class="col-xs-6 default-color">Business:</div>
                            <div class="col-xs-6"><?= get_client($dbc, $row['businessid']); ?></div>
                            <div class="clearfix"></div>
                            <div class="col-xs-6 default-color">Contact:</div>
                            <div class="col-xs-6"><?= get_contact($dbc, $row['contactid']); ?></div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="col-xs-6 default-color">Lead Status:</div>
                            <div class="col-xs-6">
								<?php if($approvals > 0 || $_GET['s'] != 'Pending') { ?>
									<select id="status_<?= $row['salesid']; ?>" data-placeholder="Select a Status" name="status" class="form-control chosen-select-deselect">
										<option value=""></option><?php
										foreach ( explode(',', $statuses) as $status_list ) {
											$selected = ($status_list==$_GET['s']) ? 'selected="selected"' : '';
											echo '<option '. $selected .' value="'. $status_list .'">'. $status_list .'</li>';
										} ?>
									</select>
								<?php } else {
									echo $_GET['s'];
								} ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-xs-6 default-color">Next Action:</div>
                            <div class="col-xs-6">
                                <select id="action_<?= $row['salesid']; ?>" data-placeholder="Select Next Action" name="next_action" class="form-control chosen-select-deselect">
                                    <option value=""></option><?php
                                    foreach ( explode(',', $next_actions) as $next_action ) {
                                        $selected = ($next_action==$row['next_action']) ? 'selected="selected"' : '';
                                        echo '<option '. $selected .' value="'. $next_action .'">'. $next_action .'</li>';
                                    } ?>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="col-xs-6 default-color">Total Due:</div>
                            <div class="col-xs-6">$<?= ( $row['lead_value'] > 0 ) ? number_format($row['lead_value']) : '0.00'; ?></div>
                            <div class="clearfix"></div>
                            <div class="col-xs-6 default-color">Date:</div>
                            <div class="col-xs-6"><input onchange="changeLeadFollowUpDate(this)" type="text" id="date_<?= $row['salesid']; ?>" name="date" class="datepicker form-control" value="<?= ( $row['new_reminder']!='0000-00-00' || !empty($row['new_reminder']) ) ? $row['new_reminder'] : 'YYYY-MM-DD'; ?>" /></div>
                            <div class="clearfix"></div>
                        </div>

                    </div>
                    <div class="clearfix"></div>
                    <?php include('quick_actions.php'); ?>
                </div>
            </div><!-- .horizontal-block-container -->
            <div class="clearfix"></div>
        </div><!-- .main-screen-white --><?php
                
        $i++;
    } ?><?php

} else { ?>
    <div class="standard-dashboard-body-content">
        <h4>No Records Found.</h4>
    </div><?php
} ?>
</div>
<div class="clearfix"></div>

<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'status.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>