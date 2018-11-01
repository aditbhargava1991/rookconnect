<?php
if (isset($_POST['service_record_btn'])) {
    $internal_communication = implode(',',$_POST['internal_communication']);
    $internal_communication_dashboard = implode(',',$_POST['internal_communication_dashboard']);
    $external_communication = implode(',',$_POST['external_communication']);
    $external_communication_dashboard = implode(',',$_POST['external_communication_dashboard']);
    $log_communication_dashboard = implode(',',$_POST['log_communication_dashboard']);

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_employee = "UPDATE `field_config` SET internal_communication = '$internal_communication', internal_communication_dashboard = '$internal_communication_dashboard', external_communication = '$external_communication', external_communication_dashboard = '$external_communication_dashboard', log_communication_dashboard = '$log_communication_dashboard' WHERE `fieldconfigid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config` (`internal_communication`, `internal_communication_dashboard`, `external_communication`, `external_communication_dashboard`, `log_communication_dashboard`) VALUES ('$internal_communication', '$internal_communication_dashboard', '$external_communication', '$external_communication_dashboard', '$log_communication_dashboard')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
				Choose Fields for Internal<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_field" class="panel-collapse collapse">
		<div class="panel-body" id="no-more-tables">
			<?php
			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT internal_communication FROM field_config"));
			$value_config = ','.$get_field_config['internal_communication'].',';
			?>

			<table border='2' cellpadding='10' class='table'>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Business
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Project
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Subject".',') !== FALSE) { echo " checked"; } ?> value="Subject" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Subject
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Body".',') !== FALSE) { echo " checked"; } ?> value="Body" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Body
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Attachment".',') !== FALSE) { echo " checked"; } ?> value="Attachment" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Attachment
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."To Staff".',') !== FALSE) { echo " checked"; } ?> value="To Staff" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;To Staff
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."CC Staff".',') !== FALSE) { echo " checked"; } ?> value="CC Staff" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;CC Staff
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Additional Email".',') !== FALSE) { echo " checked"; } ?> value="Additional Email" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Additional Email
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up By".',') !== FALSE) { echo " checked"; } ?> value="Follow Up By" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Follow Up By
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Date" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Follow Up Date
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Communication Timer".',') !== FALSE) { echo " checked"; } ?> value="Communication Timer" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Timer
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."From Email".',') !== FALSE) { echo " checked"; } ?> value="From Email" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Sending Email
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="internal_communication[]">&nbsp;&nbsp;Type
					</td>
				</tr>

			</table>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field1" >
				Choose Fields for Internal Dashboard<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_field1" class="panel-collapse collapse">
		<div class="panel-body" id="no-more-tables">
			<?php
			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT internal_communication_dashboard FROM field_config"));
			$value_config = ','.$get_field_config['internal_communication_dashboard'].',';
			?>

			<table border='2' cellpadding='10' class='table'>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Business
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Project
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Subject".',') !== FALSE) { echo " checked"; } ?> value="Subject" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Subject
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Body".',') !== FALSE) { echo " checked"; } ?> value="Body" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Body
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Attachment".',') !== FALSE) { echo " checked"; } ?> value="Attachment" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Attachment
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."To Staff".',') !== FALSE) { echo " checked"; } ?> value="To Staff" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;To Staff
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."CC Staff".',') !== FALSE) { echo " checked"; } ?> value="CC Staff" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;CC Staff
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Additional Email".',') !== FALSE) { echo " checked"; } ?> value="Additional Email" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Additional Email
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Email#".',') !== FALSE) { echo " checked"; } ?> value="Email#" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Email#
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Email By".',') !== FALSE) { echo " checked"; } ?> value="Email By" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Staff
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Email Date".',') !== FALSE) { echo " checked"; } ?> value="Email Date" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Email Date
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up By".',') !== FALSE) { echo " checked"; } ?> value="Follow Up By" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Follow Up By
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Date" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Follow Up Date
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="internal_communication_dashboard[]">&nbsp;&nbsp;Type
					</td>
				</tr>

			</table>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field2" >
				Choose Fields for External<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_field2" class="panel-collapse collapse">
		<div class="panel-body" id="no-more-tables">
			<?php
			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT external_communication FROM field_config"));
			$value_config = ','.$get_field_config['external_communication'].',';
			?>

			<table border='2' cellpadding='10' class='table'>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Business
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Project
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Subject".',') !== FALSE) { echo " checked"; } ?> value="Subject" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Subject
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Body".',') !== FALSE) { echo " checked"; } ?> value="Body" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Body
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Attachment".',') !== FALSE) { echo " checked"; } ?> value="Attachment" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Attachment
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."To Contact".',') !== FALSE) { echo " checked"; } ?> value="To Contact" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;To Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."CC Contact".',') !== FALSE) { echo " checked"; } ?> value="CC Contact" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;CC Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."CC Staff".',') !== FALSE) { echo " checked"; } ?> value="CC Staff" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;CC Staff
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Additional Email".',') !== FALSE) { echo " checked"; } ?> value="Additional Email" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Additional Email
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up By".',') !== FALSE) { echo " checked"; } ?> value="Follow Up By" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Follow Up By
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Date" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Follow Up Date
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Communication Timer".',') !== FALSE) { echo " checked"; } ?> value="Communication Timer" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Timer
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."From Email".',') !== FALSE) { echo " checked"; } ?> value="From Email" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Sending Email
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="external_communication[]">&nbsp;&nbsp;Type
					</td>
				</tr>

			</table>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field3" >
				Choose Fields for External Dashboard<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_field3" class="panel-collapse collapse">
		<div class="panel-body" id="no-more-tables">
			<?php
			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT external_communication_dashboard FROM field_config"));
			$value_config = ','.$get_field_config['external_communication_dashboard'].',';
			?>

			<table border='2' cellpadding='10' class='table'>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Business
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Project
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Subject".',') !== FALSE) { echo " checked"; } ?> value="Subject" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Subject
					</td>

					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Body".',') !== FALSE) { echo " checked"; } ?> value="Body" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Body
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Attachment".',') !== FALSE) { echo " checked"; } ?> value="Attachment" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Attachment
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."To Contact".',') !== FALSE) { echo " checked"; } ?> value="To Contact" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;To Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."CC Contact".',') !== FALSE) { echo " checked"; } ?> value="CC Contact" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;CC Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."CC Staff".',') !== FALSE) { echo " checked"; } ?> value="CC Staff" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;CC Staff
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Additional Email".',') !== FALSE) { echo " checked"; } ?> value="Additional Email" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Additional Email
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Email#".',') !== FALSE) { echo " checked"; } ?> value="Email#" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Email#
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Email By".',') !== FALSE) { echo " checked"; } ?> value="Email By" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Staff
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Email Date".',') !== FALSE) { echo " checked"; } ?> value="Email Date" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Email Date
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up By".',') !== FALSE) { echo " checked"; } ?> value="Follow Up By" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Follow Up By
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Date" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Follow Up Date
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="external_communication_dashboard[]">&nbsp;&nbsp;Type
					</td>
				</tr>

			</table>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field4" >
				Choose Fields for Log Dashboard<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_field4" class="panel-collapse collapse">
		<div class="panel-body" id="no-more-tables">
			<?php
			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT log_communication_dashboard FROM field_config"));
			$value_config = ','.$get_field_config['log_communication_dashboard'].',';
			?>

			<table border='2' cellpadding='10' class='table'>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Business
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Project
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Subject".',') !== FALSE) { echo " checked"; } ?> value="Subject" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Subject
					</td>

					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Body".',') !== FALSE) { echo " checked"; } ?> value="Body" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Body
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Attachment".',') !== FALSE) { echo " checked"; } ?> value="Attachment" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Attachment
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Email To".',') !== FALSE) { echo " checked"; } ?> value="Email To" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Email To
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Email By".',') !== FALSE) { echo " checked"; } ?> value="Email By" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Staff
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Email Date".',') !== FALSE) { echo " checked"; } ?> value="Email Date" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Email Date
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Status
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Email#".',') !== FALSE) { echo " checked"; } ?> value="Email#" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Email#
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up By".',') !== FALSE) { echo " checked"; } ?> value="Follow Up By" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Follow Up By
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Date" style="height: 20px; width: 20px;" name="log_communication_dashboard[]">&nbsp;&nbsp;Follow Up Date
					</td>
				</tr>

			</table>
		</div>
	</div>
</div>