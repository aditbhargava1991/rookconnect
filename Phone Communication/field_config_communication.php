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
	
    set_config($dbc, 'fax_communication', implode(',',$_POST['fax_communication']));
    set_config($dbc, 'fax_communication_db', implode(',',$_POST['fax_communication_dashboard']));
    set_config($dbc, 'fax_service', $_POST['fax_service']);
    set_config($dbc, 'fax_service_acct', $_POST['fax_service_acct']);
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
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field_fax" >
				Choose Fields for Fax<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_field_fax" class="panel-collapse collapse">
		<div class="panel-body" id="no-more-tables">
			<?php $value_config = ','.get_config($dbc,'fax_communication').','; ?>

			<table border='2' cellpadding='10' class='table'>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="fax_communication[]">&nbsp;&nbsp;Business
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="fax_communication[]">&nbsp;&nbsp;Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="fax_communication[]">&nbsp;&nbsp;Project
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Subject".',') !== FALSE) { echo " checked"; } ?> value="Subject" style="height: 20px; width: 20px;" name="fax_communication[]">&nbsp;&nbsp;Subject
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Body".',') !== FALSE) { echo " checked"; } ?> value="Body" style="height: 20px; width: 20px;" name="fax_communication[]">&nbsp;&nbsp;Body
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."File".',') !== FALSE) { echo " checked"; } ?> value="File" style="height: 20px; width: 20px;" name="fax_communication[]">&nbsp;&nbsp;File to Send
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Manual Number".',') !== FALSE) { echo " checked"; } ?> value="Manual Number" style="height: 20px; width: 20px;" name="fax_communication[]">&nbsp;&nbsp;Manual Number
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up By".',') !== FALSE) { echo " checked"; } ?> value="Follow Up By" style="height: 20px; width: 20px;" name="fax_communication[]">&nbsp;&nbsp;Follow Up By
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Date" style="height: 20px; width: 20px;" name="fax_communication[]">&nbsp;&nbsp;Follow Up Date
					</td>
				</tr>
			</table>
			<div class="form-group">
				<?php $fax_service = get_config($dbc, 'fax_service'); ?>
				<label class="col-sm-4">Fax Service:<span class="popover-examples"><a data-toggle="tooltip"  data-original-title="Before setting a service, please create an account to use with the service."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span></label>
				<div class="col-sm-8">
					<select class="chosen-select-deselect" name="fax_service" data-placeholder="Select a Service"><option />
						<option <?= $fax_service == '@rcfax.com' ? 'selected' : '' ?> value="@rcfax.com">RingCentral</option>
						<option <?= $fax_service == '@nextivafax.com' ? 'selected' : '' ?> value="@nextivafax.com">Nextiva</option>
						<option <?= $fax_service == '@metrofax.com' ? 'selected' : '' ?> value="@metrofax.com">MetroFax</option>
						<option <?= $fax_service == '@myfax.com' ? 'selected' : '' ?> value="@myfax.com">MyFax</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<?php $fax_service_acct = get_config($dbc, 'fax_service_acct'); ?>
				<label class="col-sm-4">Fax Account:<span class="popover-examples"><a data-toggle="tooltip"  data-original-title="This is the email address on your account, from which the emails must be sent."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span></label>
				<div class="col-sm-8">
					<input type="text" name="fax_service_acct" value="<?= $fax_service_acct ?>" class="form-control">
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field_faxdb" >
				Choose Fields for Fax Dashboard<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_field_faxdb" class="panel-collapse collapse">
		<div class="panel-body" id="no-more-tables">
			<?php $value_config = ','.get_config($dbc,'fax_communication_db').','; ?>

			<table border='2' cellpadding='10' class='table'>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;Business
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;Contact
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;Project
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Subject".',') !== FALSE) { echo " checked"; } ?> value="Subject" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;Subject
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Body".',') !== FALSE) { echo " checked"; } ?> value="Body" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;Body
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."File".',') !== FALSE) { echo " checked"; } ?> value="File" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;File
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Manual Number".',') !== FALSE) { echo " checked"; } ?> value="Manual Number" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;Manual Number
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Fax#".',') !== FALSE) { echo " checked"; } ?> value="Fax#" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;Fax #
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Sent Info".',') !== FALSE) { echo " checked"; } ?> value="Sent Info" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;Sent
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up By".',') !== FALSE) { echo " checked"; } ?> value="Follow Up By" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;Follow Up By
					</td>
					<td>
						<input type="checkbox" <?php if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Date" style="height: 20px; width: 20px;" name="fax_communication_dashboard[]">&nbsp;&nbsp;Follow Up Date
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