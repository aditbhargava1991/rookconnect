<?php include_once('../include.php');
$contactid = $_GET['edit'];
$match_list = mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE CONCAT(',',`support_contact`,',') LIKE '%,$contactid,%' AND `deleted` = 0");
if(mysqli_num_rows($match_list) > 0) { ?>
	<table class="table table-bordered">
		<tr class="hidden-sm hidden-xs">
			<th>Staff</th>
			<th>Contacts</th>
			<th>Timeline</th>
			<th>Follow Up</th>
			<th>End Date</th>
			<th>Status</th>
			<th>Function</th>
		</tr>
		<?php while($row = mysqli_fetch_array($match_list)) {
            if(!empty($row['end_date']) && $curr_date > $row['end_date'] && $row['status'] != 'Suspend') {
                $row['status'] = 'Suspend';
                $history = 'Match passed end date and was automatically suspended on '.date('Y-m-d H:i:s').'<br>';
                $query_update_status = "UPDATE match_contact SET status = 'Suspend', history = CONCAT(IFNULL(history, ''), '$history') WHERE matchid = ". $row['matchid'];
                $result_update_status = mysqli_query($dbc, $query_update_status);
            }

            $style = '';
            if($row['status'] == 'Active') {
                $style = 'style="color: green;"';
            }
            if($row['status'] == 'Suspend') {
                $style = 'style="color: red;"';
            } ?>
			<tr <?= $style ?>>
				<td data-title="Staff">
					<?php $staff_contacts_arr = explode(',', $row['staff_contact']);
                    $staff_contacts = [];
                    foreach($staff_contacts_arr as $value){
                        array_push($staff_contacts, !empty(get_client($dbc, $value)) ? get_client($dbc, $value) : get_contact($dbc, $value));
                    }
                    echo implode(', ', $staff_contacts); ?>
				</td>
				<td data-title="Contacts">
					<?php $support_contacts_arr = explode(',', $row['support_contact']);
                    $support_contacts = [];
                    foreach($support_contacts_arr as $value){
                        array_push($support_contacts, !empty(get_client($dbc, $value)) ? get_client($dbc, $value) : get_contact($dbc, $value));
                    }
                    echo implode(', ', $support_contacts); ?>
				</td>
				<td data-title="Timeline"><?= $row['match_date'] ?></td>
				<td data-title="Follow Up"><?= $row['follow_up_date'] ?></td>
				<td data-title="End Date"><?= $row['end_date'] ?></td>
				<td data-title="Status">
					<?php if($row['status'] == 'Active') {
                        $match_status = 'Active | <a href="" data-matchid="'.$row['matchid'].'" data-status="Suspend" onclick="update_match_status(this); return false;">Suspend</a>';
                    } else if($row['status'] == 'Suspend') {
                        $match_status = '<a href="" data-matchid="'.$row['matchid'].'" data-status="Active" onclick="update_match_status(this); return false;">Active</a> | Suspend';
                    } else {
                        $match_status = '<a href="" data-matchid="'.$row['matchid'].'" data-status="Suspend" onclick="update_match_status(this); return false;">Active</a> | <a href="" data-matchid="'.$row['matchid'].'" data-status="Active" onclick="update_match_status(this); return false;">Suspend</a>';
                    }
                    echo $match_status; ?>
                </td>
				<td data-title="Function"><a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Match/edit_match.php?from_tile=contacts&edit=<?= $row['matchid'] ?>', 'auto', false, true); return false;">Edit</a> | <a href="" data-matchid="<?= $row['matchid'] ?>" onclick="delete_match(this); return false;">Archive</a></td>
			</tr>
		<?php } ?>
	</table>
<?php } else {
	echo 'No Matches Found.';
} ?>