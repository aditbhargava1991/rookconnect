<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Staff Log Notes</h3>') ?>
<div class="col-md-12">
	<?php
	if($generate_pdf) {
		ob_clean();
	}
	if(!empty($_GET['ticketid']) || !empty($_GET['edit'])) {
        $query_check_credentials = "SELECT * FROM client_daily_log_notes WHERE ticketid='$ticketid' ORDER BY `note_date` DESC";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered'>
            <tr class='hidden-xs hidden-sm'>
            <th>Note</th>
            <th>References</th>
            <th>Date</th>
            <th>Added By</th>
            </tr>";
            while($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                echo '<td data-title="Note">'.html_entity_decode($row['note']).'</td>';
                echo '<td data-title="References">'.get_contact($dbc, $row['client_id']).'</td>';
                echo '<td data-title="Date">'.$row['note_date'].'</td>';
                echo '<td data-title="Added By">'.get_contact($dbc, $row['created_by']).'</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else if($access_any == FALSE) {
			echo "<h3>No Notes Found.</h3>";
		}
    }
    if($generate_pdf) {
    	$pdf_contents[] = ['', ob_get_contents()];
    }
    ?>
</div>
<?php if(!($strict_view > 0)) { ?>
<div class="col-md-12 multi-block email-block">
	<div class="form-group">
	  <label for="site_name" class="col-sm-4 control-label">References:</label>
	  <div class="col-sm-8">
		<select data-placeholder="Select Client..." name="client_id" data-table="client_daily_log_notes" data-id="" data-id-field="note_id" class="chosen-select-deselect form-control email_recipient" width="380">
		  <option value=""></option>
		  <?php
			$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Clients' AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
			foreach($query as $id) {
				echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
			}
		  ?>
		</select>
	  </div>
	</div>

  <div class="form-group">
	<label for="site_name" class="col-sm-4 control-label">Note:</label>
	<div class="col-sm-12">
	  <textarea name="note" data-table="client_daily_log_notes" data-id="" data-id-field="note_id" rows="4" cols="50" class="form-control" ></textarea>
	</div>
  </div>
	<button class="btn brand-btn pull-right" data-history-label="Note" data-history-label="Add Log Note" onclick="addMulti(this); return false;">Add Note</button>
	<div class="clearfix"></div>
</div>
<?php } ?>