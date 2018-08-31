<?php
/*
Customer Listing
*/
include ('../include.php');
?>

<script type="text/javascript">
$(document).on('change', 'select[name="contactid[]"]', function() { selectStaff(this); });
$(document).on('change', 'select[name="status[]"]', function() { selectStatus(this); });
function selectStaff(sel) {
	var contactid = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "helpdesk_ajax_all.php?fill=staff&supportid="+arr[1]+"&contactid="+contactid,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
function selectStatus(sel) {
	var status = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	if(status == 'Archived') {
		if (confirm('Are you sure you want to permanently delete this Support Request?')) {
			$.ajax({
				type: "GET",
				url: "helpdesk_ajax_all.php?fill=status&supportid="+arr[1]+"&status="+status,
				dataType: "html",   //expect html to be returned
				success: function(response){
					location.reload();
					if(status == 'Archived') {
						alert('Support request deleted.');
					}
				}
			});
		}
	} else {
		$.ajax({
				type: "GET",
				url: "helpdesk_ajax_all.php?fill=status&supportid="+arr[1]+"&status="+status,
				dataType: "html",   //expect html to be returned
				success: function(response){
					location.reload();
					if(status == 'Ticket') {
						window.location.replace("../Ticket/index.php?edit=0&supportid="+arr[1]);
					}
					if(status == 'Task') {
						window.open("../Tasks_Updated/add_task.php?supportid="+arr[1], "myWindowName", "width=800, height=600");
					}
					if(status == 'Not Priority') {
						alert('Moved to Not Priority Tab.');
					}
					if(status == 'Monday to be Scheduled') {
						alert('Moved to Monday to be Scheduled Tab.');
					}
				}
		});
	}
}
</script>

</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('helpdesk');
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

        <h1 class="">Help Desk Dashboard
        <?php
        if(config_visible_function($dbc, 'helpdesk') == 1) {
            echo '<a href="field_config_helpdesk.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        }
        ?>
        </h1>

        <span class="pull-right">
            <img src="<?php echo WEBSITE_URL;?>/img/block/red.png" width="23" height="23" border="0" alt="">&nbsp;Critical&nbsp;&nbsp;
            <img src="<?php echo WEBSITE_URL;?>/img/block/dark_yellow.png" width="23" height="23" border="0" alt="">&nbsp;Urgent&nbsp;&nbsp;
            <img src="<?php echo WEBSITE_URL;?>/img/block/blue.png" width="23" height="23" border="0" alt="">&nbsp;High&nbsp;&nbsp;
            <img src="<?php echo WEBSITE_URL;?>/img/block/green.png" width="23" height="23" border="0" alt="">&nbsp;Medium&nbsp;&nbsp;
            <img src="<?php echo WEBSITE_URL;?>/img/block/brown.png" width="23" height="23" border="0" alt="">&nbsp;Low&nbsp;&nbsp;
        </span><br><br>

        <form name="form_sites" method="post" action="" class="form-inline" role="form">
             <div id="no-more-tables">

            <?php
            if(!empty($_GET['category'])) {
                $category = $_GET['category'];
            } else {
                $category = 'Alert';
            }
            $tabs = get_config($dbc, 'helpdesk_type');
            $each_tab = explode(',', $tabs);

            $active_all = '';
            $active_monday = '';
            $active_nopri = '';
            $status = '';
            if($category == 'Alert') {
                $active_all = 'active_tab';
                $status = 'Alert';
            }
            if($category == 'Monday') {
                $active_monday = 'active_tab';
                $status = 'Monday to be Scheduled';
            }
            if($category == 'NoPriority') {
                $active_nopri = 'active_tab';
                $status = 'Not Priority';
            }
			echo '<div class="mobile-100-container">';
            echo "<a href='helpdesk.php?category=Alert'><button type='button' class='btn brand-btn mobile-block mobile-100 ".$active_all."' >Alert</button></a>&nbsp;&nbsp;";

            foreach ($each_tab as $cat_tab) {
                $active_daily = '';
                if($category == $cat_tab) {
                    $active_daily = 'active_tab';
                }
				if($cat_tab !== '') {
					echo "<a href='helpdesk.php?category=".$cat_tab."'><button type='button' class='btn brand-btn mobile-100 mobile-block ".$active_daily."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
				}
            }
            echo "<a href='helpdesk.php?category=Monday'><button type='button' class='btn brand-btn mobile-block mobile-100 ".$active_monday."' >Monday to be Scheduled</button></a>&nbsp;&nbsp;";
            echo "<a href='helpdesk.php?category=NoPriority'><button type='button' class='btn brand-btn mobile-block mobile-100 ".$active_nopri."' >Not Priority</button></a>&nbsp;&nbsp;";
			echo '</div>';
            echo '<br><br>';

            if(!empty($_GET['contactid'])) {
                $contactid = $_GET['contactid'];
                $query_check_credentials = "SELECT * FROM support WHERE deleted=0 and status = 'Support Request'  AND contactid = '$contactid' AND support_type='$category' ORDER BY supportid DESC";
            } else {

                if($category == 'Alert' || $category == 'Monday' || $category == 'NoPriority') {
                    $query_check_credentials = "SELECT * FROM support WHERE deleted=0 and status = '$status' ORDER BY supportid DESC";
                } else {
                    $query_check_credentials = "SELECT * FROM support WHERE deleted=0 and status = 'Support Request' AND support_type='$category' ORDER BY supportid DESC";
                }
            }

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                echo "<table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>";
                    echo '<th>ID#</th>';
                    echo '<th>Client Info</th>';
                    echo '<th>Type<br>Heading</th>';
                    echo '<th>Description</th>';
                    echo '<th>Document(s)</th>';
                    echo '<th>Assign Staff</th>';
                    echo '<th>Status</th>';
                    echo "</tr>";
            } else {
                echo "<h2>No Record Found.</h2>";
            }

            while($row = mysqli_fetch_array( $result ))
            {
                $color = '';
                if($row['priority'] == 'Critical') {
                    $color = 'style="color: red;"';
                }
                if($row['priority'] == 'Urgent') {
                    $color = 'style="color: yellow;"';
                }
                if($row['priority'] == 'High') {
                    $color = 'style="color: blue;"';
                }
                if($row['priority'] == 'Medium') {
                    $color = 'style="color: green;"';
                }
                if($row['priority'] == 'Low') {
                    $color = 'style="color: brown;"';
                }

				echo '<tr '.$color.'>';
                echo '<td data-title="Code">#' . $row['supportid'] . '</td>';
                echo '<td data-title="Code">' . $row['company_name'] . '<br>'
                . $row['name'].'<br>'.$row['email'].'<br>'.$row['contact_number'].'<br>'.$row['cc_email'] . '</td>';
                echo '<td data-title="Code">' . $row['support_type'] . '<br>' . $row['heading'] . '</td>';
				echo '<td width="">' . html_entity_decode( $row['message'] ) . '</td>';

                echo '<td>';
                if($row['document'] != '') {
                    $file_names = explode('*#*', $row['document']);
                    //echo '<ul>';
                    $i=0;
                    foreach($file_names as $file_name) {
                        if($file_name != '') {
                            echo '- <a href="../Ticket/download/'.$file_name.'" target="_blank">'.$file_name.'</a><br>';
                        }
                        $i++;
                    }
                    //echo '</ul>';
                } else {
                    echo '-';
                }
                echo '</td>';

                ?>
                <td data-title="Status">
                <select id="staff_<?php echo $row['supportid']; ?>"  data-placeholder="Choose a Staff..." name="contactid[]" class="chosen-select-deselect form-control input-sm">
                    <option value=''></option>
                    <?php
                    $query2 = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0");
                    while($row2 = mysqli_fetch_array($query2)) {
                        if ($row['contactid'] == $row2['contactid']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $row2['contactid']."'>".decryptIt($row2['first_name']).' '.decryptIt($row2['last_name']).'</option>';
                    }
                    ?>
                </select>
                </td>

                <td data-title="Status">
                <select id="status_<?php echo $row['supportid']; ?>"  data-placeholder="Choose an Option..." name="status[]" class="chosen-select-deselect form-control input-sm">
                    <option value=''></option>
                    <option <?php if ($row['status'] == 'Ticket') { echo " selected"; } ?> value='Ticket'><?= TICKET_NOUN ?></option>
                    <option <?php if ($row['status'] == 'Task') { echo " selected"; } ?> value='Task'>Task</option>
                    <option <?php if ($row['status'] == 'Support Request') { echo " selected"; } ?> value='Support Request'>Support Request</option>
                    <option <?php if ($row['status'] == 'Monday to be Scheduled') { echo " selected"; } ?> value='Monday to be Scheduled'>Monday to be Scheduled</option>
                    <option <?php if ($row['status'] == 'Not Priority') { echo " selected"; } ?> value='Not Priority'>Not Priority</option>
					<option <?php if ($row['status'] == 'Archived') { echo " selected"; } ?> value='Archived'>Archive</option>
                </select>
                </td>
                <?php
                echo "</tr>";
            }

            echo '</table></div>';
            ?>
        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>