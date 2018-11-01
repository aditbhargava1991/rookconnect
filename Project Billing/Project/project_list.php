<?php
if((!empty($_GET['projectid'])) && (!empty($_GET['type']))) {
    $projectid = $_GET['projectid'];
    $type = $_GET['type'];
    $approved_date = date('Y-m-d');
    $project_name = get_project($dbc, $projectid, 'project_name');
    $contactid = $_SESSION['contactid'];

    if($type == 'reject') {
        echo insert_day_overview($dbc, $contactid, 'Project', date('Y-m-d'), '', 'Rejected Project '.$project_name);
        $date_of_archival = date('Y-m-d');

        $query_update_report = "UPDATE `project` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `projectid` = '$projectid'";
        $result_update_report = mysqli_query($dbc, $query_update_report);
        $message = 'Project Rejected and Removed.';
		$url = 'project.php?type=Pending';
    } else {
        echo insert_day_overview($dbc, $contactid, 'Project', date('Y-m-d'), '', 'Approved Project '.$project_name);

        $history = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Approved on '.date('Y-m-d H:i:s');
		$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
		mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', '$history', '$projectid')");
		$projecttype = mysqli_fetch_array(mysqli_query($dbc, "SELECT `projecttype` FROM `project` WHERE `projectid`='$projectid'"))['projecttype'];
        $message = 'Project Approved';
		$url = 'project.php?type='.$projecttype;
    }

    echo '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';
}

if((!empty($_GET['projectid'])) && (!empty($_GET['status']))) {
    $projectid = $_GET['projectid'];
    $status = $_GET['status'];
    $query_update_report = "UPDATE `project` SET `status` = '$status' WHERE `projectid` = '$projectid'";
    $result_update_report = mysqli_query($dbc, $query_update_report);

    if($status == 'Approve') {
        echo '<script type="text/javascript"> alert("Project Approved and Move to Project."); window.location.replace("project.php"); </script>';
    } else {
        echo '<script type="text/javascript"> alert("Project Denied and Removed from Project."); window.location.replace("project.php"); </script>';
    }
}
?>
<script type="text/javascript">
function selectStatus(sel) {
	var status = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
    var contactid = $("#session_contactid").val();
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "<?php echo WEBSITE_URL;?>/ajax_all.php?fill=project_status&projectid="+arr[1]+'&status='+status+'&contactid='+contactid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            if(status == 'Approve as Project') {
                alert("Project Approved.");
				window.location.reload();
            } else if(status == 'Go To Estimate') {
                alert("Project Moved to Estimate.");
				window.location.reload();
            } else if(status == 'Archive/Delete') {
                alert("Project Removed.");
				$(sel).closest('tr').remove();
            } else {
			    location.reload();
            }
		}
	});
}
function ETAQuoteDate(sel) {
	var action = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "<?php echo WEBSITE_URL;?>/ajax_all.php?fill=project_etaquote&id="+arr[1]+'&name='+action,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
function review_project(id) {
	var contactid = '<?php echo $_SESSION['contactid']; ?>';

	$.ajax({
		type: "GET",
		url: "<?php echo WEBSITE_URL; ?>/Project/project_ajax_all.php?fill=review_project&project="+id+"&contact="+contactid,
		success: function(response) {
			location.reload();
		}
	});
}

$(document).ready(function() {
	$('.iframe_open').click(function(){
			var id = $(this).attr('id');
		   $('#iframe_instead_of_window').attr('src', 'project_history.php?projectid='+id);
		   $('.iframe_title').text('Project History');
		   $('.iframe_holder').show();
		   $('.hide_on_iframe').hide();
	});

	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
});
</script>

<?php
$project_tabs = get_config($dbc, 'project_tabs');
mysqli_query($dbc, "UPDATE `project` SET `projecttype`=LOWER(REPLACE(REPLACE(`projecttype`,' ','_'),'&',''))");
$project_tabs = explode(',',$project_tabs);
$type = (empty($_GET['type']) ? config_safe_str($project_tabs[0]) : $_GET['type']);
$project_vars = [];
$active_pending = '';
$title = '';
if($type == 'Pending') {
	$active_pending = 'active_tab';
	$title = 'Pending';
}

foreach($project_tabs as $item) {
	$var_name = config_safe_str($item);
	$project_vars[] = $var_name;
	${'active_'.$var_name} = '';
	if($type == $var_name) {
		${'active_'.$var_name} = 'active_tab';
		$type = $var_name;
		$title = $item;
	}
}
?>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
<input type="hidden" id="session_contactid" value="<?php echo $_SESSION['contactid']; ?>" />

<h1 class="pull-left double-gap-bottom"><?php echo $title.' '.PROJECT_TILE; ?></h1><?php
if(config_visible_function($dbc, 'project') == 1) {
	echo '<div class="pull-right gap-top">';
		echo '<a href="field_config_project.php?type='.$type.'" class="mobile-block pull-right"><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
		echo '<div class="popover-examples list-inline pull-right" style="margin:10px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></div>';
	echo '</div>';
} ?>
<div class="clearfix gap-bottom"></div>

<?php include_once('project_header_tabs.php'); ?>

<?php
	$search_cust = '';
	if(isset($_POST['search_user_submit'])) {
		$search_cust = $_POST['search_cust'];
	} else {
		$search_user = $contactid;
	}
	if(isset($_POST['search_project'])) {
		$search_project = $_POST['search_project'];
	} else {
		$search_project = '';
	}
	if (isset($_POST['display_all_inventory'])) {
		$search_user = $contactid;
		$search_project = '';
	}
?>

<div class="search-group">
	<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="col-sm-4">
				<label for="site_name" class="control-label">
					<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see all customers that you have created a project for."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					Search By Customer:</label>
			</div>
			<div class="col-sm-8">
				<select data-placeholder="Select a Customer" name="search_cust" class="chosen-select-deselect form-control">
					<option></option>
					<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT c.`contactid`, c.`name` FROM contacts c, project p WHERE p.businessid=c.contactid AND ((p.projecttype='$type' AND p.status!='Pending') OR p.`status`='$type') AND p.deleted=0"), MYSQLI_ASSOC));
					foreach($query as $custid) { ?>
						<option <?php if ($custid == $search_cust) { echo " selected"; } ?> value='<?php echo  $custid; ?>' ><?php echo get_client($dbc, $custid); ?></option><?php
					} ?>
				</select>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="col-sm-4">
				<label for="site_name" class="control-label">
					<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see all projects that you have created."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					Search By Project Short Name:</label>
			</div>
			<div class="col-sm-8">
				<select data-placeholder="Select a Project Name" name="search_project" class="chosen-select-deselect form-control">
					<option value=""></option><?php
					$query = mysqli_query($dbc,"SELECT project_name FROM project p WHERE ".($_GET['type'] == 'Pending' ? '' : "p.projecttype='$type' AND")." `status`".($_GET['type'] == 'Pending' ? '=' : '!=')."'Pending' AND p.deleted=0 ORDER BY `project_name`");
					while($row = mysqli_fetch_array($query)) { ?>
						<option <?php if ($row['project_name'] == $search_project) { echo " selected"; } ?> value='<?php echo  $row['project_name']; ?>' ><?php echo $row['project_name']; ?></option><?php
					} ?>
				</select>
			</div>
		</div>
	</div>
	<div class="form-group col-lg-3 col-md-4 col-sm-12 col-xs-12">
		<div style="display:inline-block; padding: 0 0.5em;">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here after you have made your customer selection."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see all projects within this tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
		</div>
	</div><!-- .form-group -->
	<div class="clearfix"></div>
</div>

<div id='no-more-tables'>
<?php
if($type != 'Pending') {
	if(vuaed_visible_function($dbc, 'project') == 1) {
		echo '<br /><div class="pull-right">';
			echo '<a href="add_project.php?type='.$type.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn pull-right">Add '.PROJECT_TILE.'</a>';
			echo '<div class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new project."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></div>';
		echo '</div>';
	}
}

$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

if($search_cust != '') {
	$query_check_credentials = "SELECT p.*, c.name FROM project p, contacts c WHERE p.businessid = c.contactid AND p.`businessid`='$search_cust' AND p.`project_name` LIKE '%$search_project%' AND p.projecttype='$type' AND p.deleted=0 ORDER BY projectid DESC";
} else if($search_project != '') {
	$query_check_credentials = "SELECT p.*, c.name FROM project p, contacts c WHERE p.businessid = c.contactid AND p.`project_name` LIKE '%$search_project%' AND p.projecttype='$type' AND p.deleted=0 ORDER BY projectid DESC";
} else {
	if($_GET['type'] == 'Pending') {
		$query_check_credentials = "SELECT * FROM project WHERE deleted = 0 AND status='Pending' ORDER BY projectid DESC LIMIT $offset, $rowsPerPage";
		$query   = "SELECT COUNT(projectid) AS numrows FROM project WHERE deleted = 0 AND status='Pending'";
	} else {
		if(empty($_GET['from'])) {
			$query_check_credentials = "SELECT * FROM project WHERE deleted = 0 AND projecttype='$type' AND status!='Pending' ORDER BY projectid DESC LIMIT $offset, $rowsPerPage";
			$query   = "SELECT count(*) as numrows FROM project WHERE deleted = 0 AND projecttype='$type' AND status!='Pending' ORDER BY projectid DESC";
		} else {
			$query_check_credentials = "SELECT * FROM project WHERE deleted = 0 AND projecttype='$type' ORDER BY projectid DESC LIMIT $offset, $rowsPerPage";
			$query   = "SELECT count(*) as numrows FROM project WHERE deleted = 0 AND projecttype='$type' ORDER BY projectid DESC ";
		}
	}
}

$result = mysqli_query($dbc, $query_check_credentials);

$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {

	// Added Pagination //
	if($search_cust == '' && $search_project == '') {
	echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	}
	// Pagination Finish //

	echo '<table class="table table-bordered">';
	if(PROJECT_TILE == 'Projects') {
		$project_tile_title = 'Project';
	} else if(PROJECT_TILE == 'Jobs') {
		$project_tile_title = 'Job';
	} else {
		$project_tile_title = PROJECT_TILE;
	}
	echo '<tr class="hidden-xs hidden-sm">
		<th>'.$project_tile_title.' #</th>
		<th>Business<br>Contact</th>
		<th>'.$project_tile_title.' Name & Created Date</th>
		<th>'.$project_tile_title.' Value</th>
		<th>Notes</th>
		<th>Function</th>
		<th>Reviewed</th>
		<th>History</th>
		</tr>';
} else {
	echo "<h2>No Record Found.</h2>";
}

// Get Project Types
$project_tabs = get_config($dbc, 'project_tabs');
if($project_tabs == '') {
	$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
}
$project_tabs = explode(',',$project_tabs);
$project_vars = [];
foreach($project_tabs as $item) {
	$project_vars[] = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
}

while($row = mysqli_fetch_array( $result )) {
	$projectid = $row['projectid'];
	echo '<input type="hidden" name="projectid_dashboard" value="'.$row['projectid'].'" /><tr>';

	foreach($project_vars as $key => $type_name) {
		if($row['projecttype'] == $type_name) {
			echo '<td data-title="'.(PROJECT_TILE == 'Projects' ? 'Project' : (PROJECT_TILE == 'Jobs' ? 'Job' : PROJECT_TILE)).' #"><a href="review_project.php?type=project_path&projectid='.$projectid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">#'.$projectid.'<br>'.$project_tabs[$key].'</a></td>';
		}
	}

	$clientid = explode(',',$row['clientid']);
	$businessid = $row['businessid'];
	if($businessid ==  '' || $businessid ==  0) {
		$businessid = get_contact($dbc, $clientid, 'businessid');
	}
	$client_name = [];
	foreach($clientid as $client) {
		$client_name[] = get_contact($dbc, $client, "");
	}
	echo '<td data-title="Customer">' . get_contact($dbc, $businessid, 'name').'<br>'.implode('<br />', $client_name) . '</td>';

	echo '<td data-title="Name & Created Date">' . $row['project_name'] . '<br>'.$row['start_date'].'</td>';
	echo '<td data-title="Project Value">$' . $row['total_price'] . '</td>';

	echo '<td data-title="Notes"><a href=\'add_project.php?type='.$type.'&projectid='.$row['projectid'].'&note=add_view\'>Add/View</a></td>';

	echo '<td data-title="Function">';
	if(vuaed_visible_function($dbc, 'project') == 1) {
		echo '<a href=\'add_project.php?type='.$type.'&projectid='.$row['projectid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'>Edit</a> | ';
	}
	if($_GET['type'] == 'Pending') {
		echo '<a href=\'project.php?projectid='.$row['projectid'].'&type=reject\'>Reject</a> | <a href=\'project.php?projectid='.$row['projectid'].'&type=approve\'>Approve</a>';
	} else { ?>
	<div class="form-group">
		<select id="projectstatus_<?php echo $row['projectid']; ?>"  data-placeholder="Choose a Status..." name="status[]" class="chosen-select-deselect form-control input-sm">
		  <option value=""></option>
		  <option value="Pending" <?php if($row['status'] == "Pending") { echo " selected"; } ?> >Pending</option>
		  <option value="In Development" <?php if($row['status'] == "In Development")  { echo " selected"; } ?> >In Development</option>
		  <option value="Go to Estimate" <?php if($row['status'] == "Go to Estimate")  { echo " selected"; } ?> >Go to Estimate</option>
		  <option value="Approve as Project" <?php if($row['status'] == "Approve as Project")  { echo " selected"; } ?> >Approve as <?php echo (PROJECT_TILE == 'Projects' ? 'Project' : (PROJECT_TILE == 'Jobs' ? 'Job' : PROJECT_TILE)); ?></option>
		  <option value="Attach To A Project" <?php if($row['status'] == "Attach To A Project")  { echo " selected"; } ?> >Attach to a <?php echo (PROJECT_TILE == 'Projects' ? 'Project' : (PROJECT_TILE == 'Jobs' ? 'Job' : PROJECT_TILE)); ?></option>
		  <option value="Archive/Delete" <?php if($row['status'] == "Archive/Delete")  { echo " selected"; } ?> >Archive/Delete</option>
		</select>
	</div>
	<?php }
	echo '</td>';

	echo '<td data-title="Review Information">';
		echo '<button class="btn brand-btn" onclick="review_project('.$row['projectid'].'); return false;">Mark Reviewed</button><br />';
		echo (empty($row['review_date']) ? 'Never Reviewed' : 'Reviewed '.date('Y-m-d', strtotime($row['review_date'])).' by '.get_contact($dbc,$row['reviewer_id']));
	echo "</td>";

	echo '<td data-title="History">';
	echo '<span class="iframe_open" id="'.$row['projectid'].'" style="cursor:pointer">View All</span></td>';
	echo "</tr>";
}

echo '</table>';

// Added Pagination //
if($search_cust == '' && $search_project == '') {
echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
}
// Pagination Finish //

if($type != 'Pending') {
	if(vuaed_visible_function($dbc, 'project') == 1) {
		echo '<div class="pull-right">';
			echo '<a href="add_project.php?type='.$type.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn pull-right">Add '.PROJECT_TILE.'</a>';
			echo '<div class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new project."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></div>';
		echo '</div>';
		echo '<div class="clearfix"></div>';
	}
}
?>

</form>
</div>
