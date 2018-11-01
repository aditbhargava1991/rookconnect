<?php
error_reporting(0);
?>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#form1").submit(function( event ) {
        //var projectclientid = $("#projectclientid").val();
        var goal_heading = $("input[name=goal_heading]").val();
        var goal_set_for = $("#goal_set_for").val();
		var goal_setter = $("#goal_setter").val();
		var goal_timeline = $("#goal_timeline").val();
        var goal = $("#goal").val();
		var reminder = $("#reminder").val();
		var start_date = $("#start_date").val();
		var end_date = $("#finish_date").val();

        if (goal_set_for == '' || goal_heading == '' || goal_setter == '' || goal_timeline == '' || goal == '' || reminder == '' || start_date == '' || end_date == '') {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });
});
</script>
<script type='text/javascript'>
$(document).ready(function() {

	$("#business").change(function() {
	if($("#business option:selected").text() == 'New Business') {
			$( "#new_business" ).show();
	} else {
		$( "#new_business" ).hide();
	}

	var businessid = this.value;

	/*$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "call_log_ajax_all.php?fill=assigncontact&businessid="+businessid,
		dataType: "html",   //expect html to be returned
		success: function(response){
			var arr = response.split('**#**');
			$('#call_log_contact').html(arr[0]);
			$("#call_log_contact").trigger("change.select2");
		}
	});*/

});

	//Objectives
	var add_new_p_misc = 1;
	$('#deleteproductsmisc_0').hide();
	$('#add_row_p_misc').on( 'click', function () {

		$('#deleteproductsmisc_0').show();
        var clone_misc = $('.additional_p_misc').clone();
        clone_misc.find('.form-control').val('');
		clone_misc.find('#objectives_0').attr('id', 'objectives_'+add_new_p_misc);
		clone_misc.find('#actions_0_0').attr('id', 'actions_'+add_new_p_misc+'_0');
		clone_misc.find('#actions_'+add_new_p_misc+'_0').attr('name', 'actions_'+add_new_p_misc+'[]');
		clone_misc.find('#add_row_action_0').attr('id', 'add_row_action_'+add_new_p_misc);
		clone_misc.find('#productsmisc_0').attr('id', 'productsmisc_'+add_new_p_misc);
		clone_misc.find('#additional_action_0').attr('id', 'additional_action_'+add_new_p_misc).empty();
		$('#additional_action_'+add_new_p_misc).empty();
        clone_misc.find('#deleteproductsmisc_0').attr('id', 'deleteproductsmisc_'+add_new_p_misc);
        $('#deleteproductsmisc_0').hide();

        clone_misc.removeClass("additional_p_misc");

        $('#add_here_new_p_misc').append(clone_misc);

        add_new_p_misc++;

        return false;
    });
});

var add_new_p_misc = 1;
//$('#deleteproductsmisc_0').hide();
function addAction(objective)
{
	var typeId = objective.id;
	var arr = typeId.split('_');
	$('#deleteproductsmisc_0').show();
	$('.additional_action').show();
	var clone_misc = $('.additional_action').clone();
	clone_misc.find('.form-control').val('');
	clone_misc.find('#action_0').attr('id', 'action_'+add_new_p_misc);
	clone_misc.find('#action_0_0').attr('id', 'action_'+arr[3]+'_'+add_new_p_misc);
	//clone_misc.find('#action_'+arr[3]).attr('id', 'action_'+add_new_p_misc);
	var count = 0;

	while($('#actions_'+arr[3]+'_'+count).length)
	{
		count++;
	}
	//alert('#actions_'+arr[3]+'_'+count + ' ==== > ' + $('#actions_'+arr[3]+'_'+count).val());
	//count += 1;
	clone_misc.find('#actions_0_0').attr('id', 'actions_'+arr[3]+'_'+count);
	clone_misc.find('#actions_'+arr[3]+'_'+count).attr('name', 'actions_'+arr[3]+'[]');
	clone_misc.find('#deleteproductsmisc_0').attr('id', 'deleteproductsmisc_'+add_new_p_misc);
	$('#deleteproductsmisc_0').hide();

	clone_misc.removeClass("additional_action");
	$('.additional_action').hide();

	$('#additional_action_' + arr[3]).append(clone_misc);

	add_new_p_misc++;

	return false;
}

function deleteEstimate(sel, hide, blank) {
	var typeId = sel.id;
	var arr = typeId.split('_');
	$("#"+hide+arr[1]).html('');
	$("#"+hide+arr[1]).hide();
	$("#"+blank+arr[1]).html('');
    $("#"+blank+arr[1]).hide();
}

function deleteActions(sel, hide) {
	var typeId = sel.id;
	var arr = typeId.split('_');
	$("#"+hide+arr[1]).html('');
	$("#"+hide+arr[1]).hide();
}


</script>
<?php
/*
Add Goals & Objectives
*/
include ('../include.php');
error_reporting(0);

?>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('gao');
?>
<?php
$note_add_view = '';
$info_view = '';
if(!empty($_GET['note'])) {
	$note_add_view = 'in';
} else {
	$info_view = 'in';
}
?>
<?php
if (isset($_POST['add_gao'])) {
	if($_POST['add_gao'] == 'Save') {
		$status = 0;
	}
	elseif($_POST['add_gao'] == 'Submit') {
		$status = 1;
	}

	$created_date = date('Y-m-d');
    //$created_by = $_SESSION['contactid'];

	$goal_heading = filter_var($_POST['goal_heading'],FILTER_SANITIZE_STRING);
    $goal_setter = $_POST['goal_setter'];

	if(is_array($_POST['goal_set_for'])) {
		$goal_set_for = implode(',', $_POST['goal_set_for']);
	}
	else {
		if($_POST['goal_set_for'] == 'all') {
			$goal_set_for = 0;
		}
		else {
			$goal_set_for = $_POST['goal_set_for'];
		}
	}

    $goal_timeline = $_POST['goal_timeline'];
    $start_date = filter_var($_POST['start_date'],FILTER_SANITIZE_STRING);
    $end_date = filter_var($_POST['end_date'],FILTER_SANITIZE_STRING);
	$reminder = filter_var($_POST['reminder'],FILTER_SANITIZE_STRING);
	$goal = filter_var($_POST['goal'],FILTER_SANITIZE_STRING);
	//$encryptData =
    //$wholeData = ;

    if(empty($_POST['goalid'])) {
		$type = $_GET['type'];
        $query_insert_vendor = "INSERT INTO `goals` (`goal_heading`, `goal_setter`, `goal_set_for`, `goal_timeline`, `start_date`,
		`end_date`, `reminder`, `goal`, `type`) VALUES
		('$goal_heading', '$goal_setter', '$goal_set_for', '$goal_timeline', '$start_date', '$end_date','$reminder', '$goal','$type')";
		$result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $goalid = mysqli_insert_id($dbc);
        $url = 'Added';
    } else {
        $goalid = $_POST['goalid'];
        $query_update_vendor = "UPDATE `goals` SET `goal_heading` = '$goal_heading', `goal_setter` = '$goal_setter', `goal_set_for` = '$goal_set_for',
		`goal_timeline` = '$goal_timeline', `start_date` = '$start_date', `end_date` = '$end_date', `reminder` = '$reminder', `goal` = '$goal' WHERE `goalid` = '$goalid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
    }

	$count = 0;
	mysqli_query($dbc, "delete from goal_objectives where goalid = $goalid");
	$objectives = $_POST['objectives'];
	$objectives = array_filter($objectives);
	$totalCount = count($objectives);

	for($i = 0; $i < $totalCount; $i++) {
		if(isset($objectives[$i])) {
			$actions = array();
			$var = "actions_$i";
			if(is_array($_POST[$var]))
				$actions = $_POST[$var];

			$actions = array_filter($actions);
			$mergedAction = array();
			foreach($actions as $action) {
				$mergedAction[] = $action;
			}


			$objective = $objectives[$i];
			$mergedActions = rtrim(implode(',', $mergedAction), ',');
			$query_insert_obj = "INSERT INTO `goal_objectives` (`goalid`,`objectives`,`actions`) VALUES ('$goalid','$objective','$mergedActions')";
			$result_insert_obj = mysqli_query($dbc, $query_insert_obj);
		}
		$count++;
	}

	$count = 500;
	$totalCount = $count + $totalCount;
	for($i = 500; $i < $totalCount; $i++) {
		if(isset($objectives[$i])) {
			$actions = array();
			$var = "actions_$i";
			if(is_array($_POST[$var]))
				$actions = $_POST[$var];

			$actions = array_filter($actions);
			$mergedAction = array();
			foreach($actions as $action) {
				$mergedAction[] = $action;
			}


			$objective = $objectives[$i];
			$mergedActions = rtrim(implode(',', $mergedAction), ',');
			$query_insert_obj = "INSERT INTO `goal_objectives` (`goalid`,`objectives`,`actions`) VALUES ('$goalid','$objective','$mergedActions')";
			$result_insert_obj = mysqli_query($dbc, $query_insert_obj);
		}
		$count++;
	}


    //Notes
    echo '<script type="text/javascript"> window.location.replace("gao.php?maintype='.$type.'"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
}
?>
<div class="container">
	<div class="row">
		<h1>Add Goals & Objectives</h1>
		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<?php

			$goal_heading = '';
			$goal_setter = '';
			$goal_set_for = '';
			$goal_timeline = '';
			$start_date = '';
			$end_date = '';
			$reminder = '';
			$goal = '';

			if(!empty($_GET['goalid'])) {

				$goalid = $_GET['goalid'];
				$get_gao = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM goals WHERE goalid='$goalid'"));

				$goal_heading = $get_gao['goal_heading'];
				$goal_setter = $get_gao['goal_setter'];
				$goal_set_for = $get_gao['goal_set_for'];
				$goal_timeline = $get_gao['goal_timeline'];
				$start_date = $get_gao['start_date'];
				$end_date = $get_gao['end_date'];
				$reminder = $get_gao['reminder'];
				$goal = $get_gao['goal'];

			?>
			<input type="hidden" id="goalid" name="goalid" value="<?php echo $goalid ?>" />
			<?php   }      ?>

			<div class="panel-group" id="accordion2">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
								General Information<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<?php $class = 'collapse'; ?>
					<?php if($_GET['note'])
							$class = 'collapse';
					?>

					<div id="collapse_info" class="panel-collapse <?php echo $class; ?>">
						<div class="panel-body">

							<div class="form-group">
								<label for="goal_heading" class="col-sm-4 control-label">Goal Heading:<span class="brand-color">*</span></label>
								<div class="col-sm-8">
								  <input name="goal_heading" value="<?php echo $goal_heading; ?>" type="text" class="form-control">
								</div>
							</div>
							<?php $search_client = '';?>
							<div class="form-group">
								<label for="goal_setter" class="col-sm-4 control-label">Goal Setter:<span class="brand-color">*</span></label>
								<div class="col-sm-8">
									<select data-placeholder="Select a Staff" name="goal_setter" class="chosen-select-deselect form-control">
									  <option value=""></option>
									  <?php
										$query = mysqli_query($dbc,"SELECT DISTINCT(c.name), c.contactid FROM contacts c");
										while($row = mysqli_fetch_array($query)) {
										?><option <?php if ($row['contactid'] == $goal_setter) { echo " selected"; } ?> value='<?php echo  $row['contactid']; ?>' ><?php echo decryptIt($row['name']); ?></option>
									<?php	} ?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="goal_set_for" class="col-sm-4 control-label">Goal Set For:<span class="brand-color">*</span></label>
								<div class="col-sm-8">

									  <?php if($_GET['type'] == 'department'): ?>
										<select data-placeholder="Select a Staff" multiple name="goal_set_for[]" class="chosen-select-deselect form-control">
										  <?php
											$query = mysqli_query($dbc,"SELECT DISTINCT(c.name), c.contactid FROM contacts c");
											while($row = mysqli_fetch_array($query)) {
											?><option <?php if ($row['contactid'] == $goal_set_for) { echo " selected"; } ?> value='<?php echo  $row['contactid']; ?>' ><?php echo decryptIt($row['name']); ?></option>
										  <?php	} ?>
										</select>
									  <?php else: ?>
										<select data-placeholder="Select a Staff" name="goal_set_for" class="chosen-select-deselect form-control">
										  <option value=""></option>
										  <option value='all'>All Contacts</option>
										  <?php
											$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
											foreach($query as $id) {
												$selected = '';
												$selected = $id == $goal_set_for ? 'selected = "selected"' : '';
												echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
											}
										  ?>
										</select>
									  <?php endif; ?>

								</div>
							</div>
							<?php $businessid = ''; ?>
							<div class="form-group">
								<label for="business" class="col-sm-4 control-label">Goal Timeline:<span class="brand-color">*</span></label>
								<div class="col-sm-8">
									<select data-placeholder="Choose a Timeline..." name="goal_timeline" id="goal_timeline" class="chosen-select-deselect form-control1" width="380">
									  <option value=""></option>
									  <?php
										$goalsTime = array('Daily','Weekly','Bi-Monthly','Monthly','Quarterly','Semi Annually','Yearly');
										foreach($goalsTime as $goalTime) {
											if ($goal_timeline == $goalTime) {
												$selected = 'selected="selected"';
											} else {
												$selected = '';
											}
											echo "<option ".$selected." value='".$goalTime."'>".$goalTime.'</option>';
										}
									  ?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>



				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_desc" >
								Dates<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_desc" class="panel-collapse collapse">
						<div class="panel-body">
						   <div class="form-group clearfix">
								<label for="first_name" class="col-sm-4 control-label text-right">Goal Start Date:<span class="brand-color">*</span></label>
								<div class="col-sm-8">
									<input name="start_date" value="<?php echo $start_date; ?>" type="text" class="datepicker"></p>
								</div>
							</div>
							<div class="form-group clearfix">
								<label for="first_name" class="col-sm-4 control-label text-right">Goal End Date:<span class="brand-color">*</span></label>
								<div class="col-sm-8">
									<input name="end_date" value="<?php echo $end_date; ?>" type="text" class="datepicker"></p>
								</div>
							</div>
							<div class="form-group clearfix">
								<label for="first_name" class="col-sm-4 control-label text-right">Reminder/Follow Up:<span class="brand-color">*</span></label>
								<div class="col-sm-8">
									<input name="reminder" value="<?php echo $reminder; ?>" type="text" class="datepicker"></p>
								</div>
							</div>
						 </div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_cost" >
								Goals<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>



					<div id="collapse_cost" class="panel-collapse collapse <?php echo $note_add_view; ?>">
						<div class="panel-body">
							<div class="form-group">

								<label for="goal" class="col-sm-2 control-label">
								<span class="popover-examples list-inline" style="margin-left:20px;"><a data-toggle="tooltip" Title='Goals are general guidelines that explain what you want to achieve.' data-placement="top" title=""><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a></span>The Goal:<span class="brand-color">*</span></label>
								<div class="col-sm-8">
								  <input name="goal" value="<?php echo $goal; ?>" type="text" class="form-control">
								</div>
							</div>
							<div class="form-group clearfix products_heading">
								<label class="col-sm-6 text-center"><span class="popover-examples list-inline" style="margin-left:20px;"><a data-toggle="tooltip" Title='Objectives outline and define strategies or implementation steps required to attain the identified goal.' data-placement="top" title=""><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Objective</label>
								<label class="col-sm-1 text-center"><span class="popover-examples list-inline" style="margin-left:20px;"><a data-toggle="tooltip" Title='Often each objective is associated with a series of actions required to achieve it.' data-placement="top" title=""><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Action</label>
							</div>

							<?php $id_loop = 500; ?>
							<?php
								$select_query = "select * from goal_objectives where goalid = $goalid";
								$select_result = mysqli_query($dbc, $select_query);
							?>
							<?php while ($row = mysqli_fetch_array($select_result)) { ?>
								<?php if($row['objectives'] != '' || $row['actions'] != '') { ?>
									<?php
									$tempactionse = array();
									$tempactionse = explode(',', $row['actions']); ?>
									<div class="form-group clearfix all_products" id="<?php echo 'productsmisc_'.$id_loop; ?>" >
										<div class="col-sm-2"></div>
										<div class="col-sm-4">
											<input name="objectives[<?php echo $id_loop; ?>]" value="<?php echo $row['objectives']; ?>" id="objectives_<?php echo $id_loop; ?>" type="text" class="form-control" />
										</div>
										<div class="col-sm-4 form-group triple-gapped clearfix">
											<input name="actions_<?php echo $id_loop; ?>[]" value="<?php echo $tempactionse[0]; ?>" id="actions_<?php echo $id_loop; ?>_0"  type="text" class="form-control" />
										</div>
										<div class="col-sm-3">
											<button id="add_row_action_<?php echo $id_loop; ?>" onclick="return addAction(this)" class="btn brand-btn pull-left">Add More Actions</button>
											<a href="#" onclick="deleteEstimate(this,'productsmisc_','additional_action_'); return false;" id="deleteproductsmisc_<?php echo $id_loop; ?>" class="btn brand-btn">Delete</a>
										</div>
										<div id='additional_action_<?php echo $id_loop; ?>' class="clearfix">
											<?php array_shift($tempactionse); ?>
											<?php $counting = 1; ?>
											<?php foreach($tempactionse as $tempaction): ?>
												<div id="action_<?php echo $id_loop; ?>_<?php echo $counting; ?>">
													<div class="col-sm-5"></div>
													<div class="col-sm-4">
														<input name="actions_<?php echo $id_loop; ?>[]" value="<?php echo $tempaction; ?>" id="actions_<?php echo $id_loop; ?>_<?php echo $counting; ?>"  type="text" class="form-control" />
													</div>
													<div class="form-group triple-gapped clearfix">
														<div class="col-sm-2" >
															<a href="#" onclick="deleteActions(this,'action_<?php echo $id_loop; ?>_'); return false;" id="deleteproductsmisc_<?php echo $counting; ?>" class="btn brand-btn">Delete</a>
														</div>
													</div>
												</div>
												<?php $counting++; ?>
											<?php endforeach; ?>
										</div>
									</div>
								<?php } ?>
							<?php $id_loop++; } ?>

							<div class="additional_p_misc clearfix">
								<div class="clearfix"></div>
								<div class="form-group all_products" id="productsmisc_0">
									<div class="col-sm-2"></div>
									<div class="col-sm-4">
										<input name="objectives[]" value="" id="objectives_0" type="text" class="form-control" />
									</div>
									<div class="col-sm-4 form-group triple-gapped clearfix">
										<input name="actions_0[]" value="" id="actions_0_0"  type="text" class="form-control" />
									</div>
									<div class="col-sm-2">
										<button id="add_row_action_0" onclick="return addAction(this)" class="btn brand-btn pull-left">Add More Actions</button>
										<a href="#" onclick="deleteEstimate(this,'productsmisc_','additional_action_'); return false;" id="deleteproductsmisc_0" class="btn brand-btn">Delete</a>
									</div>
									<div id='additional_action_0' class="clearfix"></div>
								</div>


							</div>
							<div class='additional_action clearfix' style="display:none">
								<div id="action_0">
									<div class="col-sm-5"></div>
									<div class="col-sm-4">
										<input name="actions_0[]" value="" id="actions_0_0"  type="text" class="form-control" />
									</div>
									<div class="form-group triple-gapped clearfix">
										<div class="col-sm-1" >
											<a href="#" onclick="deleteActions(this,'action_'); return false;" id="deleteproductsmisc_0" class="btn brand-btn">Delete</a>
										</div>
									</div>

								</div>
							</div>

							<div id="add_here_new_p_misc"></div>

							<div class="form-group triple-gapped clearfix">
								<div class="col-sm-offset-10">
									<button id="add_row_p_misc" class="btn brand-btn pull-left">Add More Objectives</button>
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
					<a href="gao.php?maintype=<?php echo $_GET['type']; ?>" class="btn brand-btn btn-lg">Back</a>
					<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button type="submit" name="add_gao" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
				</div>

			</div>

		</form>

  </div>
</div>
<?php include ('../footer.php'); ?>
