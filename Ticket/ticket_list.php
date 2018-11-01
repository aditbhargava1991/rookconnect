<script>
	$(document).ready(function() {
		$('[name="search_user"],[name="search_client"],[name="search_project_type"],[name="status_searcher"],[name="search_ticket"]').change(function() {
			submitForm();
		});
		$('[name="status[]"]').change(function() {
			changePOSStatus(this);
		});
	});
    function submitForm(thisForm) {
        if (!$('input[name="search_user_submit"]').length) {
            var input = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "search_user_submit").val("1");
            $('[name=form1]').append($(input));
        }

        $('[name=form1]').submit();
    }
</script>
<?php // Dashboard
$ticket_status_list = explode(',',get_config($dbc, 'ticket_status'));
$project_types = [];
foreach(explode(',',get_config($dbc, 'project_tabs')) as $type_name) {
	$project_types[preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($type_name)))] = $type_name;
} ?>
<script src="../js/bootstrap.min.js"></script>

<script type="text/javascript">
function changePOSStatus(sel) {
	var status = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "<?php echo WEBSITE_URL; ?>/Ticket/ticket_ajax_all.php?fill=update_ticket_status&ticketid="+arr[1]+'&status='+status,
		dataType: "html",   //expect html to be returned
		success: function(response){
			if(status == 'Archive') {
				$(sel).closest('tr').hide();
			}
		}
	});
}

</script>
<div class='iframe_holder' style='display:none;'>
	<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
	<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
	<iframe id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
</div>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

	<?php $search_user = '';
	$search_user_id = '';
	$status_searcher = '';
	$search_client = '';
	$search_ticket = '';
	$search_ticket_id = '';
	$search_project_type = '';
	$search_project_type_name = '';
	$search_type = "AND '".filter_var($ticket_type)."' IN (`ticket_type`, '')";
	if(isset($_POST['search_user_submit'])) {
		$search_user = $_POST['search_user'];
		if($search_user == 'unassigned') {
			$search_user = "AND (REPLACE(IFNULL(t.contactid,''),',','') = '' AND REPLACE(IFNULL(internal_qa_contactid,''),',','') = '' AND REPLACE(IFNULL(deliverable_contactid,''),',','') = '')";
			$search_user_id = $_POST['search_user'];
		} else if($search_user !== '' && $search_user !== NULL) {
			$search_user_id = filter_var($_POST['search_user'],FILTER_SANITIZE_STRING);
			$search_user = "AND (t.contactid LIKE '%," . $search_user_id . ",%' OR internal_qa_contactid LIKE '%," . $search_user_id . ",%' OR deliverable_contactid LIKE '%," . $search_user_id . ",%' OR '".$search_user_id."'='')";
		} else {
			$search_user = '';
			$search_user_id = '';
		}
		$archivedornot = ' AND t.status != \'Archive\'';
		$status_searcher = $_POST['status_searcher'];
		if($status_searcher !== '' && $status_searcher !== NULL) {
			$status_searcher_id = $_POST['status_searcher'];
			if($status_searcher == 'Archive') {
				$archivedornot = '';
			}
			$status_searcher = "AND t.status='".$status_searcher."'";
		}
		$search_client = $_POST['search_client'];
		if($search_client !== '' && $search_client !== NULL) {
			 $search_client_id = $_POST['search_client'];
			$search_client = "AND '".$search_client."' IN (t.`businessid`, t.`clientid`)";
		}
		$search_project_type_name = filter_var($_POST['search_project_type'],FILTER_SANITIZE_STRING);
		if($search_project_type_name !== '') {
			$search_project_type = "AND t.`projectid` IN (SELECT `projectid` FROM `project` WHERE `projecttype`='$search_project_type_name')";
		}
		$search_ticket_id = $_POST['search_ticket'];
		if($search_ticket_id !== '' && $search_ticket_id !== NULL) {
			$search_ticket_id = $_POST['search_ticket'];
		}
	} else if($_SESSION['category'] == 'Staff' && (in_array('Staff',$db_config) || in_array('Deliverable Date',$db_config))) {
		$search_user_id = $contactid;
		$search_user = "AND (t.contactid LIKE '%," . $contactid . ",%' OR internal_qa_contactid LIKE '%," . $contactid . ",%' OR deliverable_contactid LIKE '%," . $contactid . ",%' OR '".$contactid."'='')";
	} else if($_SESSION['category'] == BUSINESS_CAT && in_array('Business',$db_config)) {
		$search_client_id = $contactid;
		$search_client = "AND '".$contactid."' IN (t.`businessid`, t.`clientid`)";
	}
	if (isset($_POST['display_all_inventory']) && (in_array('Staff',$db_config) || in_array('Deliverable Date',$db_config))) {
		$search_user_id = $contactid;
		$search_user = "AND (t.contactid LIKE '%," . $contactid . ",%' OR internal_qa_contactid LIKE '%," . $contactid . ",%' OR deliverable_contactid LIKE '%," . $contactid . ",%' OR '".$contactid."'='')";
	}
?>
<?php if(1==1) { //$tile_security['search'] == 1) { ?>
	<div class="col-sm-12">
		<div class="form-group">
		<?php if(in_array('Staff',$db_config) || in_array('Deliverable Date',$db_config)) { ?>
			<div class="col-sm-5">
			  <label for="site_name" class="control-label col-sm-4">
				<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu for the user whom is attached to the desired <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Select a User:
			  </label>
			  <div class="col-sm-8">
				  <select data-placeholder="Select Staff" name="search_user" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
					foreach($query as $id) {
						$selected = '';
						$selected = $id == $search_user_id ? 'selected = "selected"' : '';
						echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
					}
				  ?>
				  <option <?= $search_user_id == 'unassigned' ? 'selected' : '' ?> value="unassigned">Unassigned</option>
				</select>
			  </div>
			</div>
		<?php } ?>
		<?php if(in_array('Business',$db_config) && $_SESSION['category'] == 'Staff') { ?>
		  <div class="col-sm-5">
	          <label for="site_name" class="col-sm-4 control-label">
	            <span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu for the Business that is attached to the desired <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	            Select a Business:
	          </label>
	          <div class="col-sm-8">
	              <select data-placeholder="Select a Business" name="search_client" class="chosen-select-deselect form-control">
	              <option value=""></option>
	              <?php
	                $query = sort_contacts_query(mysqli_query($dbc,"SELECT `name`, `contactid` FROM contacts WHERE `contactid` IN (SELECT `businessid` FROM `tickets` WHERE `deleted`=0)"));
	                foreach($query as $row) {
	                    ?><option <?= $row['contactid'] == $search_client_id ? 'selected' : '' ?> value='<?= $row['contactid'] ?>' ><?= $row['name'] ?></option>
	                <?php } ?>
	            </select>
	          </div>
		  </div>
		<?php } else if(in_array('Contact',$db_config) && $_SESSION['category'] == 'Staff') { ?>
		  <div class="col-sm-5">
	          <label for="site_name" class="col-sm-4 control-label">
	            <span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu for the Contact that is attached to the desired <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	            Select a Contact:
	          </label>
	          <div class="col-sm-8">
	              <select data-placeholder="Select a Contact" name="search_client" class="chosen-select-deselect form-control">
	              <option value=""></option>
	              <?php
	                $query = sort_contacts_query(mysqli_query($dbc,"SELECT `first_name`, `last_name`, `contactid` FROM contacts WHERE `contactid` IN (SELECT `clientid` FROM `tickets` WHERE `deleted`=0)"));
	                foreach($query as $row) {
	                    ?><option <?= $row['contactid'] == $search_client_id ? 'selected' : '' ?> value='<?= $row['contactid'] ?>' ><?= $row['first_name'].' '.$row['last_name'] ?></option>
	                <?php } ?>
	            </select>
	          </div>
		  </div>
		<?php } else { ?>
			<input type="hidden" name="search_client" value="<?= $search_client_id ?>">
		<?php } ?>
		<?php if(in_array('Project',$db_config)) { ?>
			<div class="col-sm-5">
				<label for="site_name" class="control-label col-sm-4">
					<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu for the tab of <?= PROJECT_NOUN ?> that is attached to <?= TICKET_TILE ?> you want to see."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					Select <?= in_array(substr(PROJECT_NOUN,0,1),['a','e','i','o','u','A','E','I','O','U']) ? 'an' : 'a' ?> <?= PROJECT_NOUN ?> Tab:
				</label>
				<div class="col-sm-8">
					<select data-placeholder="Select a Tab" name="search_project_type" id="" class="chosen-select-deselect form-control input-sm">
					  <option value=""></option>
					  <?php foreach($project_types as $cat_tab_value => $cat_tab) {
							echo "<option ".($cat_tab_value == $search_project_type_name ? 'selected' : '')." value='".$cat_tab_value."'>".$cat_tab.'</option>';
						} ?>
					</select>
				</div>
			</div>
		<?php } ?>
		<?php //if(in_array('Status',$db_config)) { ?>
			<div class="col-sm-5">
				<label for="site_name" class="control-label col-sm-4">
					<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu for the Status that is attached to the desired <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					Select a Status:
				</label>
				<div class="col-sm-8">
					<select data-placeholder="Select a Status" name="status_searcher" id="" class="chosen-select-deselect form-control input-sm">
					  <option value=""></option>
					  <?php foreach ($ticket_status_list as $cat_tab) {
							$count_query = mysqli_fetch_assoc(mysqli_query($dbc, "select count(*) as cat_count from tickets where status = '$cat_tab'"));
							echo "<option ".($status_searcher_id == $cat_tab ? 'selected' : '')." value='".$cat_tab."'>".$cat_tab.' ('.$count_query['cat_count'].')</option>';
						} ?>
					</select>
				</div>
			</div>
		<?php //} ?>

	    <div class="col-sm-5">
	      <label for="site_name" class="control-label col-sm-4">
	        <span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Choose from the drop down menu for the specific <?= TICKET_NOUN ?> Number that is attached to the desired <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	        Search By <?= TICKET_NOUN ?> #:
	      </label>
	      <div class="col-sm-8">
	         <input type="text" name="search_ticket" value="<?php echo $search_ticket_id; ?>" class="form-control">
	        </select>
	      </div>
	  	</div>

	  	<div class="col-sm-2 pull-right">
			<span class="popover-examples list-inline" style="margin:-5px 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Refreshes the page to display your <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block mobile-100">Display All</button>
		</div>
    </div>
</div>
<?php } ?>

<div class="clearfix"></div>
<?php

/* Pagination Counting */
$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;
$ticket_sort = 'ticketid DESC';
switch(get_config($dbc, 'ticket_sorting')) {
	case 'oldest': $ticket_sort = 'ticketid ASC'; break;
	case 'project': $ticket_sort = 'project_name ASC'; break;
}

if(!empty($_GET['pid'])) {
	$projectid = $_GET['pid'];
	$query_check_credentials = "SELECT t.*, c.name FROM tickets t LEFT JOIN contacts c ON t.businessid = c.contactid LEFT JOIN `project` ON t.projectid=`project`.`projectid` WHERE t.projectid = '$projectid' $search_type AND t.status != 'Archive' AND t.deleted = 0 ORDER BY $ticket_sort LIMIT $offset, $rowsPerPage";
	$query = "SELECT count(t.ticketid) as numrows FROM tickets t LEFT JOIN contacts c ON t.businessid = c.contactid WHERE t.projectid = '$projectid' $search_type AND t.status != 'Archive' AND t.deleted = 0";
} else {
	if(isset($_POST['search_user_submit'])) {
		if($search_ticket_id != '') {
			$searchable_list = mysqli_query($dbc, "SELECT t.*, c.name FROM tickets t LEFT JOIN contacts c ON t.businessid = c.contactid LEFT JOIN `project` ON t.projectid=`project`.`projectid` WHERE t.deleted = 0 $search_type");
			$ticketids = [0];
			while($searchable = mysqli_fetch_assoc($searchable_list)) {
				if(strpos(get_ticket_label($dbc, $searchable), $search_ticket_id) !== FALSE) {
					$ticketids[] = $searchable['ticketid'];
				}
			}
			$query_check_credentials = "SELECT t.*, c.name FROM tickets t LEFT JOIN contacts c ON t.businessid = c.contactid LEFT JOIN `project` ON t.projectid=`project`.`projectid` WHERE t.`ticketid` IN (".implode(',',$ticketids).") ORDER BY $ticket_sort LIMIT $offset, $rowsPerPage";
			$query = "SELECT count(t.ticketid) FROM tickets t LEFT JOIN contacts c ON t.businessid = c.contactid WHERE t.`ticketid` IN (".implode(',',$ticketids).")";
		} else {
            if($status_searcher != '') {
			    $query_check_credentials = "SELECT t.*, c.name FROM tickets t LEFT JOIN contacts c ON t.businessid = c.contactid LEFT JOIN `project` ON t.projectid=`project`.`projectid` WHERE t.deleted = 0 $search_type AND (1=1 ".$search_user." ".$search_client." ".$status_searcher." ".$search_project_type.") $archivedornot ORDER BY $ticket_sort";
            } else if($search_client != '') {
			    $query_check_credentials = "SELECT t.*, c.name FROM tickets t LEFT JOIN contacts c ON t.businessid = c.contactid LEFT JOIN `project` ON t.projectid=`project`.`projectid` WHERE t.deleted = 0 $search_type AND (1=1 ".$search_user." ".$search_client." ".$status_searcher." ".$search_project_type.") $archivedornot ORDER BY $ticket_sort";
            } else {
			    $query_check_credentials = "SELECT t.*, c.name FROM tickets t LEFT JOIN contacts c ON t.businessid = c.contactid LEFT JOIN `project` ON t.projectid=`project`.`projectid` WHERE t.deleted = 0 $search_type AND (1=1 ".$search_user." ".$search_client." ".$status_searcher." ".$search_project_type.") $archivedornot ORDER BY $ticket_sort LIMIT $offset, $rowsPerPage";
			    $query = "SELECT count(t.ticketid) FROM tickets t LEFT JOIN contacts c ON t.businessid = c.contactid WHERE t.deleted = 0 $search_type AND (1=1 ".$search_user." ".$search_client." ".$status_searcher." ".$search_project_type.") $archivedornot";
            }
		}
	} else {
		$query_check_credentials = "SELECT t.*, c.name FROM tickets t LEFT JOIN contacts c ON t.businessid=c.contactid LEFT JOIN `project` ON t.projectid=`project`.`projectid` WHERE t.status != 'Archive' AND t.deleted = 0 $search_type ".$search_user." ORDER BY $ticket_sort LIMIT $offset, $rowsPerPage";
		$query = "SELECT count(c.name) as numrows FROM tickets t LEFT JOIN contacts c ON t.businessid=c.contactid WHERE t.status != 'Archive' AND t.deleted = 0 $search_type ".$search_user;
	}
}

$result = mysqli_query($dbc, $query_check_credentials) or die(mysqli_error($dbc));

if(!$result) {
	echo "Search query is currently unavailable, please contact your server admin...";
}

$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
	// Added Pagination //
    if($status_searcher == '' && $search_client == '') {
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    }
	// Pagination Finish //

	include('ticket_table.php');

	// Added Pagination //
	if($status_searcher == '' && $search_client == '') {
	echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	}
	// Pagination Finish //
} else {
	if($tile_security['edit'] == 1) {
		echo '<div class="pull-right gap-bottom">';
			?><span class="popover-examples list-inline" style="margin:0 5px 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add <?= in_array(substr(TICKET_NOUN,0,1),['a','e','i','o','u','A','E','I','O','U']) ? 'an' : 'a' ?> <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
		echo '<a href="../Ticket/index.php?edit=0&type='.$ticket_type.'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']).'" class="btn brand-btn mobile-block">Add '.TICKET_NOUN.'</a>';
		echo '</div>';
	}
	echo "<h2>No Record Found.</h2>";
	if($tile_security['edit'] == 1) {
		echo '<div class="pull-right gap-bottom">';
			?><span class="popover-examples list-inline" style="margin:0 5px 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add <?= in_array(substr(TICKET_NOUN,0,1),['a','e','i','o','u']) ? 'an' : 'a' ?> <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
		echo '<a href="../Ticket/index.php?edit=0&type='.$ticket_type.'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']).'" class="btn brand-btn mobile-block">Add '.TICKET_NOUN.'</a>';
		echo '</div>';
	}
}
?>

<script type="text/javascript">
$(document).ready(function() {
	$('.iframe_open').click(function(){
		   var id = $(this).attr('id');
		   $('#iframe_instead_of_window').attr('src', 'ticket_history.php?ticketid='+id+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name));
		   $('.iframe_title').text('<?= TICKET_NOUN ?> History');
		   $('.iframe_holder').show();
		   $('.hide_on_iframe').hide();
	});

	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});

});
</script>
</form>
