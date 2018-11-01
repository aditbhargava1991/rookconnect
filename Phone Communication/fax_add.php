<h1>Fax Communication</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="<?php echo $back_url; ?>" class="btn config-btn">Back to Dashboard</a></div>

<?php if(!empty($_GET['type'])) {
	echo '<input type="hidden" id="comm_type" name="comm_type" value="'.$communication_type.'" />';
}

$clientid = '';
$businessid = '';

if(!empty($_GET['bid'])) {
	$businessid = $_GET['bid'];
}

/*
if(!empty($_GET['clientid'])) {
	$clientid = $_GET['clientid'];
	$businessid = get_contact($dbc, $clientid, 'businessid');
}
*/
if(!empty($_GET['projectid'])) {
	$projectid = $_GET['projectid'];
	$businessid = get_project($dbc, $projectid, 'businessid');
	$clientid = get_project($dbc, $projectid, 'clientid');
}

$followup_by = '';
$followup_date = '';
$doc = '';
$contactid = $_SESSION['contactid'];
if(!empty($_GET['phone_communicationid'])) {

	$phone_communicationid = $_GET['phone_communicationid'];
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM phone_communication WHERE phone_communicationid='$phone_communicationid'"));

	$businessid = $get_ticket['businessid'];

	$contactid = $get_ticket['contactid'];
	if($businessid == '') {
		$businessid = get_contact($dbc, $contactid, 'businessid');
	}

	$projectid = $get_ticket['projectid'];
	$followup_by = $get_ticket['follow_up_by'];
	$doc = $get_ticket['doc'];
	$followup_date = $get_ticket['follow_up_date'];

	$comments = $get_ticket['comment'];
?>
<input type="hidden" id="phone_communicationid" name="phone_communicationid" value="<?php echo $phone_communicationid ?>" />
<?php   }      ?>

<div class="panel-group" id="accordion2">

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
					Information<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_info" class="panel-collapse collapse in">
			<div class="panel-body">
				<script type="text/javascript">
				$(document).ready(function() {
				});
				</script>

				<div class="col-md-12">

					<?php if(strpos($value_config,',Business,') !== FALSE) { ?>
						<div class="form-group clearfix completion_date">
							<label for="first_name" class="col-sm-4 control-label text-right">Business<span class="brand-color">*</span>:</label>
							<div class="col-sm-8">
								<select name="businessid" id="businessid" data-placeholder="Select an Option..." class="chosen-select-deselect form-control" width="380">
									<option value=''></option>
									<?php
									$query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Business' AND deleted=0 ORDER BY category");
									while($row = mysqli_fetch_array($query)) {
										if ($businessid== $row['contactid']) {
											$selected = 'selected="selected"';
										} else {
											$selected = '';
										}
										echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
									}
									?>
								</select>
							</div>
						</div>
					<?php } ?>

					<?php if(strpos($value_config,',Contact,') !== FALSE) { ?>
						<div class="form-group">
						  <label for="site_name" class="col-sm-4 control-label">Contact<span class="text-red">*</span>:</label>
						  <div class="col-sm-8">
							<select data-placeholder="Select a Contact..." id="contactid" name="contactid" class="chosen-select-deselect form-control" width="380">
								<option></option>
								<?php $contact_query = sort_contacts_query(mysqli_query($dbc, "SELECT contactid, name, first_name, last_name, fax, category FROM contacts WHERE businessid='$businessid' AND `deleted`=0 AND `status` > 0 AND `category`!='Sites'"));
								foreach($contact_query as $row) {
									$fax = $row['fax'];
									echo "<option ".($row['contactid'] == $contactid ? 'selected' : '')." value='". $row['contactid']."'>".$row['full_name'].' &lt;'.(empty($fax) ? 'No Fax Number' : $fax).'&gt;</option>';
								} ?>
							</select>
						  </div>
						</div>
					<?php } ?>

					<?php if(strpos($value_config,',Manual Number,') !== FALSE) { ?>
						<div class="form-group">
						  <label for="site_name" class="col-sm-4 control-label">Manual Recipient<span class="text-red">*</span>:</label>
						  <div class="col-sm-8">
							<input type="text" name="manual_fax" value="<?= $get_ticket['manual'] ?>" class="form-control">
						  </div>
						</div>
					<?php } ?>

					<?php if(strpos($value_config,',Project,') !== FALSE) { ?>
						<div class="form-group">
						  <label for="site_name" class="col-sm-4 control-label">Project Name<span class="text-red">*</span>:</label>
						  <div class="col-sm-8">
							<select data-placeholder="Select a Project..." name="projectid" id="projectid"  class="chosen-select-deselect form-control" width="380">
							  <option value=""></option>
							  <?php $project_tabs = get_config($dbc, 'project_tabs');
								if($project_tabs == '') {
									$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
								}
								$project_tabs = explode(',',$project_tabs);
								$project_vars = [];
								foreach($project_tabs as $item) {
									$project_vars[] = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
								}
								$query = mysqli_query($dbc,"SELECT * FROM (SELECT projectid, projecttype, project_name FROM project WHERE businessid='$businessid' UNION SELECT CONCAT('C',`projectid`), 'Client Project', `project_name` FROM `client_project` WHERE `clientid`='$businessid' AND `deleted`=0) PROJECTS order by project_name");
								while($row = mysqli_fetch_array($query)) {
									if(substr($row['projectid'],0,1)=='C') {
										echo "<option ".($projectid == $row['projectid'] ? 'selected' : '')." value='".$row['projectid']."'>Client Project: ".$row['project_name'].'</option>';
									}
									foreach($project_vars as $key => $type_name) {
										if($type_name == $row['projecttype']) {
											echo "<option ".($projectid == $row['projectid'] ? 'selected' : '')." value='".$row['projectid']."'>".$project_tabs[$key].': '.$row['project_name'].'</option>';
										}
									}
								}
							  ?>
							</select>
						  </div>
						</div>
					<?php } ?>

					<!--<div class="form-group clearfix completion_date">
						<label for="first_name" class="col-sm-4 control-label text-right">Date of Call<span class="brand-color">*</span>:</label>
						<div class="col-sm-8">
							<input type="text" name="doc" id="doc" value="<?php echo $followup_date; ?>" class="datepicker form-control">
						</div>
					</div>-->

					<div class="form-group">
						<div class="col-sm-4">
							<a href="<?php echo $back_url; ?>" class="btn brand-btn">Back</a>
						</div>
						<div class="col-sm-8">
							<button type="date" name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_td" >
					Fax Communication Details<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_td" class="panel-collapse collapse">
			<div class="panel-body">
				<?php if(strpos($value_config,',Subject,') !== FALSE || strpos($value_config,',Body,') !== FALSE) { ?>
					<h3>Cover Page Information</h3>
				<?php } ?>
				<?php if(strpos($value_config,',Subject,') !== FALSE) { ?>
					   <div class="form-group">
						<label for="call_date" class="col-sm-4 control-label">Subject:</label>
						<div class="col-sm-8">
						  <input type="text" name="doc" id="doc" value="<?php echo $doc; ?>" class="datepicker form-control">
						</div>
					  </div>
				<?php } ?>
				<?php if(strpos($value_config,',Body,') !== FALSE) { ?>
					   <div class="form-group">
						<label for="call_date" class="col-sm-4 control-label">Body:</label>
						<div class="col-sm-8">
						  <textarea name="comments" rows="5" cols="50" class="form-control"><?php echo $comments; ?></textarea>
						</div>
					  </div>
				<?php } ?>
				<?php if(strpos($value_config,',File,') !== FALSE) { ?>
					<h3>Fax</h3>
					   <div class="form-group">
						<label for="call_date" class="col-sm-4 control-label">File to Fax:</label>
						<div class="col-sm-8">
						  <input type="file" name="fax_file" accept="application/PDF" class="form-control"><?php echo $comments; ?></textarea>
						</div>
					  </div>
				<?php } ?>
				<div class="form-group">
					<div class="col-sm-4">
						<a href="<?php echo $back_url; ?>" class="btn brand-btn">Back</a>
					</div>
					<div class="col-sm-8">
						<button type="submit" name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_followup" >
					Follow Up<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_followup" class="panel-collapse collapse">
			<div class="panel-body">
				<div class="form-group clearfix completion_date">
					<label for="first_name" class="col-sm-4 control-label text-right">Follow Up By:</label>
					<div class="col-sm-8">
						<select name="followup_by"  id="followup_by" data-placeholder="Select a Staff..." class="chosen-select-deselect form-control" width="380">
							<option value=""></option>
							  <?php
								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
								foreach($query as $id) {
									$selected = '';
									$selected = $id == $followup_by ? 'selected = "selected"' : '';
									echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
								}
							  ?>
						</select>
					</div>
				</div>

				<div class="form-group clearfix completion_date">
					<label for="followup_date" class="col-sm-4 control-label text-right">Follow Up Date:</label>
					<div class="col-sm-8">
						<input type="text" name="followup_date" id="followup_date" value="<?php echo $followup_date; ?>" class="datepicker form-control">
					</div>
				</div>

			</div>
		</div>
	</div>

</div>

<div class="form-group">
	<p><span class="hp-red"><em>Required Fields *</em></span></p>
</div>

<div class="form-group">
	<div class="col-sm-6">
		<a href="<?php echo $back_url; ?>" class="btn brand-btn btn-lg">Back</a>
	</div>
	<div class="col-sm-6">
		<button type="submit" name="submit" value="submit" class="btn brand-btn btn-lg pull-right">Submit</button>
	</div>
</div>

<style>
	.chosen-container {
		width:100%;
	}
</style>
