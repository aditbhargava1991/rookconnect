<!-- Daysheet My Communications -->
<?php
	$rowsPerPage = $_GET['pagerows'] > 0 ? $_GET['pagerows'] : 25;
	$_GET['page'] = $_GET['page'] ?: 1;
	$offset = ($_GET['page'] > 0 ? $_GET['page'] - 1 : 0) * $rowsPerPage;
    $comm_query = "SELECT * FROM `email_communication` WHERE `deleted`=0 AND `created_by`='".$_SESSION['contactid']."' ORDER BY `today_date` DESC LIMIT $offset, $rowsPerPage";
    $comm_result = mysqli_query($dbc, $comm_query);
	if($comm_result->num_rows == 0 && $_GET['page'] > 1) {
		$_GET['page'] = 1;
		$offset = 0;
		$comm_query = "SELECT * FROM `email_communication` WHERE `deleted`=0 AND `created_by`='".$_SESSION['contactid']."' ORDER BY `today_date` DESC LIMIT $offset, $rowsPerPage";
		$comm_result = mysqli_query($dbc, $comm_query);
	}
    $num_rows = mysqli_num_rows($comm_result);
?>
    <div class="col-xs-12">
        <div class="weekly-div" style="overflow-y: hidden;">
            <?php if($num_rows > 0) {
				display_pagination($dbc, "SELECT COUNT(*) `numrows` FROM `email_communication` WHERE `deleted`=0 AND `created_by`='".$_SESSION['contactid']."'", $_GET['page'], $rowsPerPage, true, 25);
                echo '<ul class="option-list">';
                while($row = mysqli_fetch_array( $comm_result )) {
					echo '<div class="note_block">';
					echo 'Date: '.$row['today_date'].'<br />';
					if($row['businessid'] > 0) {
						echo BUSINESS_CAT.': <a href="../Contacts/contacts_inbox.php?edit='.$row['businessid'].'" onclick="overlayIFrameSlider(this.href+\'&fields=all_fields\',\'auto\',true,true); return false;">'.get_contact($dbc, $row['businessid'], 'name_company').'</a><br />';
					}
					$individuals = [];
					foreach(array_filter(explode(',',$row['contactid'])) as $row_contactid) {
						$individuals[] = '<a href="../Contacts/contacts_inbox.php?edit='.$row_contactid.'" onclick="overlayIFrameSlider(this.href+\'&fields=all_fields\',\'auto\',true,true); return false;">'.get_contact($dbc, $row_contactid, 'name_company').'</a>';
					}
					if(count($individuals) > 0) {
						echo 'Individuals: '.implode(', ',$individuals).'<br />';
					}
					echo profile_id($dbc, $row['created_by'],false);
					echo '<div class="pull-right" style="width: calc(100% - 3.5em);">';
					echo '<p><b>From: '.$row['from_name'].' &lt;'.$row['from_email'].'&gt;</b><br />';
					echo '<b>To: '.implode('; ',array_filter(explode(',',$row['to_staff'].','.$row['to_contact'].','.$row['new_emailid']))).'</b><br />';
					echo '<b>CC: '.implode('; ',array_filter(explode(',',$row['cc_staff'].','.$row['cc_contact']))).'</b>';
					echo '<b>Subject: '.$row['subject'].'</b></p>';
					echo html_entity_decode($row['email_body']);
					echo '</div><div class="clearfix"></div><hr></div>';
                }
                echo '</ul>';
				display_pagination($dbc, "SELECT COUNT(*) `numrows` FROM `email_communication` WHERE `deleted`=0 AND `created_by`='".$_SESSION['contactid']."'", $_GET['page'], $rowsPerPage, true, 25);
            } else {
                echo "<h2>No Record Found.</h2>";
            } ?>
        </div>
    </div>