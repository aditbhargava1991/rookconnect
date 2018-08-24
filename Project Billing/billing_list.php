<script>
$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
});

function view_history(id) {
	$('#iframe_instead_of_window').attr('src', '../Project/project_history.php?projectid='+id);
   $('.iframe_title').text('Project History');
   $('.iframe_holder').show();
   $('.hide_on_iframe').hide();
}

function archiveProject(id) {
	var contactid = '<?php echo $_SESSION['contactid']; ?>';
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "<?php echo WEBSITE_URL;?>/ajax_all.php?fill=project_status&projectid="+id+'&status=Archive/Delete&contactid='+contactid,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
</script>
<div class="container">
	<div class='iframe_holder' style='display:none;'>

		<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
	</div>
	<div class="row hide_on_iframe">
		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
		<input type="hidden" id="session_contactid" value="<?php echo $_SESSION['contactid']; ?>" />

		<?php echo preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower(explode(',',get_config('project_tabs'))[0])));
			if(isset($_POST['search_user_submit'])) {
				$search_client = $_POST['search_client'];
			} else {
				$search_user = $contactid;
			}
			if (isset($_POST['display_all_inventory'])) {
				$search_user = $contactid;
			}
		?>

		<div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">
				<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see all clients that you have created a project for."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Search By Client:
			</label>
            <div class="col-sm-8" style="width:auto">
				<select data-placeholder="Select a Client" name="search_client" class="chosen-select-deselect form-control" width="380">
					<option value=""></option><?php
					$query = mysqli_query($dbc,"SELECT DISTINCT(c.name) AS client_name FROM contacts c, project p WHERE p.businessid=c.contactid AND p.deleted=0");
					while($row = mysqli_fetch_array($query)) { ?>
						<option <?php if ($row['client_name'] == $search_client) { echo " selected"; } ?> value='<?php echo  $row['client_name']; ?>' ><?php echo decryptIt($row['client_name']); ?></option><?php
					} ?>
				</select>
            </div>
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here after you have made your client selection."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>

            <span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see all projects within this tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
		</div><!-- .form-group -->

		<div id='no-more-tables'>
		<?php
		$rowsPerPage = 25;
		$pageNum = 1;

		if(isset($_GET['page'])) {
			$pageNum = $_GET['page'];
		}

		$offset = ($pageNum - 1) * $rowsPerPage;

		if($search_client != '') {
			$query_check_credentials = "SELECT p.*, c.name FROM project p, contacts c WHERE p.businessid = c.contactid AND (c.name='$search_client') AND p.deleted=0 ORDER BY projectid DESC";
			$query = "SELECT COUNT(`projectid`) FROM project p, contacts c WHERE p.businessid = c.contactid AND (c.name='$search_client') AND p.deleted=0";
			//$query = "SELECT count(c.name) FROM project p, contacts c WHERE p.businessid = c.contactid AND (c.name='$search_client') AND p.projecttype='$type' AND p.deleted=0 ORDER BY projectid DESC";
		} else {
			if($_GET['type'] == 'Pending') {
				$query_check_credentials = "SELECT * FROM project WHERE deleted = 0 AND status='Pending' ORDER BY projectid DESC LIMIT $offset, $rowsPerPage";
				$query   = "SELECT COUNT(projectid) AS numrows FROM project WHERE deleted = 0 AND status='Pending'";
			} else {
				$query_check_credentials = "SELECT * FROM project WHERE deleted = 0 ORDER BY projectid DESC LIMIT $offset, $rowsPerPage";
				$query   = "SELECT count(*) as numrows FROM project WHERE deleted = 0";
			}
		}

		$result = mysqli_query($dbc, $query_check_credentials);

		$num_rows = mysqli_num_rows($result);
		if($num_rows > 0) {

			// Added Pagination //
			if($search_client == '') {
			echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
			}
			// Pagination Finish //

			echo '<table class="table table-bordered table-striped">';
			if(PROJECT_TILE == 'Projects') {
				$project_tile_title = 'Project';
			} else if(PROJECT_TILE == 'Jobs') {
				$project_tile_title = 'Job';
			} else {
				$project_tile_title = PROJECT_TILE;
			}
			echo '<thead><tr class="hidden-xs hidden-sm">
				<th>'.$project_tile_title.' #</th>
				<th>Business<br>Contact</th>
				<th>'.$project_tile_title.' Name & Created Date</th>
				<th>Billings</th>
				<th>Invoices</th>
				<th>Function</th>
				<th>History</th>
				</tr></thead>';

			// Get Project Types
			$project_tabs = get_config($dbc, 'project_tabs');
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
						echo '<td data-title="'.(PROJECT_TILE == 'Projects' ? 'Project' : (PROJECT_TILE == 'Jobs' ? 'Job' : PROJECT_TILE)).' #"><a href="'.WEBSITE_URL.'/Project/review_project.php?type=project_path&projectid='.$projectid.'&from='.$row['projecttype'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">#'.$projectid.'<br>'.$project_tabs[$key].''.($row['status'] == 'Pending' ? ' (Pending)' : '').'</a></td>';
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
				echo '<td data-title="Client">' . get_contact($dbc, $businessid, 'name').'<br>'.implode('<br />', $client_name) . '</td>';
				echo '<td data-title="Name & Created Date">' . $row['project_name'] . '<br>'.$row['start_date'].'</td>';
				echo '<td data-title="Billings"><a href="?tab=billing&subtab=project&projectid='.$projectid.'">View</a></td>';
				echo '<td data-title="Invoices"><a href="?tab=invoices&projectid='.$projectid.'">View</a></td>';
				echo '<td data-title="Function">'.(vuaed_visible_function($dbc, 'project') == 1 ? '<a href="../Project/add_project.php?type='.$row['projecttype'].'&projectid='.$projectid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Edit</a> | <a href="Archive Project" onclick="archiveProject('.$projectid.'); return false;">Archive</a>' : '').'</td>';
				echo '<td data-title="History"><a href="View History" onclick="view_history('.$projectid.'); return false;">View All</a></td>';
				echo "</tr>";
			}

			echo '</table>';

			// Added Pagination //
			if($search_client == '') {
			echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
			}
			// Pagination Finish //
		} else {
			echo "<h2>No Record Found.</h2>";
		}
		?>

		</form>
		</div>
	</div>
</div>