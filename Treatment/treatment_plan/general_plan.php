<?php
/*
NEW PATIENT HISTORY FORM
*/
if (isset($_POST['manual_btn'])) {
	$patientid = $_POST['patientid'];
    $injuryid = $_POST['injuryid'];
    $therapistsid = get_all_from_injury($dbc, $injuryid, 'injury_therapistsid');
    $treatment_plan = htmlentities($_POST['treatment_plan']);
    $treatment_plan = filter_var($treatment_plan,FILTER_SANITIZE_STRING);

	//$treatment_plan = filter_var($_POST['treatment_plan'],FILTER_SANITIZE_STRING);
    $send_note = $_POST['send_note'];
    $updated_at = date('Y-m-d');
    if(empty($_POST['treatmentplanid'])) {
		$query_insert_form = "INSERT INTO `treatment_plan` (`patientid`, `therapistsid`, `injuryid`, `treatment_plan`, `updated_at`) VALUES ('$patientid', '$therapistsid', '$injuryid', '$treatment_plan', '$updated_at')";
		$result_insert_form = mysqli_query($dbc, $query_insert_form);
        $url = 'Added';
    } else {
        $treatmentplanid = $_POST['treatmentplanid'];
        $query_update_inventory = "UPDATE `treatment_plan` SET `treatment_plan` = '$treatment_plan', `updated_at` = '$updated_at' WHERE `treatmentplanid` = '$treatmentplanid'";
        $result_update_inventory	= mysqli_query($dbc, $query_update_inventory);
        $url = 'Updated';
    }

    if($send_note == 1) {
        $to = get_config($dbc, 'treatment_frontend_email');
        $patient = get_contact($dbc, $patientid);
        $subject = 'Clinic Ace Treatment Plan for Patient : '.$patient.'';
        send_email('', $to, '', '', $subject, $_POST['treatment_plan'], '');
    }

    echo '<script type="text/javascript"> window.location.replace("index.php?tab='.$tab_name.'&subtab='.$category.'"); </script>';

    mysqli_close($dbc); //Close the DB Connection
}

?>
<script type="text/javascript">
$(document).ready(function() {

    $("#form1").submit(function( event ) {
        var patientid = $("#patientid").val();
        var injury = $("#injury").val();
        //var therapistsid = $("#therapistsid").val();
        if (patientid == '' || injury == '') {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });

	$("#patientid").change(function() {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "<?php echo WEBSITE_URL;?>/ajax_all.php?fill=patient&patientid="+this.value,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#injury').html(response);
				$("#injury").trigger("change.select2");
                tinymce.get('notes').getBody().innerHTML = '';
                tinymce.get('subjective').getBody().innerHTML = '';
                tinymce.get('objective').getBody().innerHTML = '';
                tinymce.get('assessment').getBody().innerHTML = '';
                tinymce.get('plan').getBody().innerHTML = '';
			}
		});

	});

});
$(document).on('change', 'select[name="patientid"]', function() { changePatient(this); });

function changePatient(sel) {
    var proValue = sel.value;
    var proId = sel.id;
    var arr = proId.split('_');

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "<?php echo WEBSITE_URL;?>/ajax_all.php?fill=treatment&patientid="+proValue,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('#*#');
            $("#patient_email").val(result[0]);
            $("#injuryid").html(result[1]);
			$("#injuryid").trigger("change.select2");
        }
    });
}
</script>
<?php

$patientid = '';
$therapistsid = '';
$injuryid = '';
$treatment_plan = '';

if(!empty($_GET['treatmentplanid']))	{
	$treatmentplanid = $_GET['treatmentplanid'];
	$get_treatment =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	treatment_plan WHERE	treatmentplanid='$treatmentplanid'"));
	$patientid = $get_treatment['patientid'];
	$therapistsid = $get_treatment['therapistsid'];
	$injuryid = $get_treatment['injuryid'];
	$treatment_plan = $get_treatment['treatment_plan'];

?>
<input type="hidden" id="treatmentplanid"	name="treatmentplanid" value="<?php echo $treatmentplanid ?>" />
<?php	}	   ?>

<div class="panel-group" id="accordion">

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapse_info" >
					Information<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_info" class="panel-collapse collapse">
			<div class="panel-body">

			  <?php if(empty($_GET['treatmentplanid'])) { ?>

			  <div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Patient<span class="empire-red">*</span>:</label>
				<div class="col-sm-8">
					<select id="patientid" data-placeholder="Choose a Patient..." name="patientid" class="chosen-select-deselect form-control" width="380">
						<option value=""></option>
						<?php
						$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Patient' AND status>0 AND deleted=0");
						while($row = mysqli_fetch_array($query)) {
							if ($patientid == $row['contactid']) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}
							echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
						}
						?>
					</select>
				</div>
			  </div>

			  <div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Injury:</label>
				<div class="col-sm-8">
					<select id="injuryid" data-placeholder="Choose a Injury..." name="injuryid" class="chosen-select-deselect form-control" width="380">
						<option value=""></option>
					</select>
				</div>
			  </div>

			  <?php } else {
			  ?>
			  <div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Patient:</label>
				<div class="col-sm-8">
					<?php echo get_contact($dbc, $patientid); ?>
				</div>
			  </div>

			  <div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Injury:</label>
				<div class="col-sm-8">
					<?php echo get_all_from_injury($dbc, $injuryid, 'injury_name').' - '.                  get_all_from_injury($dbc, $injuryid, 'injury_type').' : '.
						get_all_from_injury($dbc, $injuryid, 'injury_date'); ?>
				</div>
			  </div>

			  <?php } ?>

				<!--
				  <div class="form-group">
					<label for="site_name" class="col-sm-4 control-label">Therapists<span class="empire-red">*</span>:</label>
					<div class="col-sm-8">
						<select id="therapistsid" data-placeholder="Choose a Therapists..." name="therapistsid" class="chosen-select-deselect form-control" width="380">
							<option value=""></option>
							<?php
							$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND deleted=0");
							while($row = mysqli_fetch_array($query)) {
								if ($therapistsid == $row['contactid']) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}
								echo "<option ".$selected." value='". $row['contactid']."'>".$row['first_name'].' '.$row['last_name'].'</option>';
							}
							?>
						</select>
					</div>
				  </div>
				  -->

			</div>

		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapse_2" >
					Treatment Plan<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_2" class="panel-collapse collapse">
			<div class="panel-body">

			  <div class="form-group">
				<label for="fax_number"	class="col-sm-4	control-label">Treatment Plan:</label>
				<div class="col-sm-8">
					<textarea name="treatment_plan" rows="5" cols="50" class="form-control"><?php echo $treatment_plan; ?></textarea>
				</div>
			  </div>

			  <div class="form-group">
				<div class="col-sm-offset-4 col-sm-8">
				  <div class="checkbox">
					<label>
					  <input type="checkbox" value="1" name="send_note">Send Notes to Front Desk
					</label>
				  </div>
				</div>
			  </div>

			</div>

		</div>
	</div>

</div>

 <div class="form-group">
	<div class="col-sm-4">
		<p><span class="empire-red pull-right"><em>Required Fields *</em></span></p>
	</div>
	<div class="col-sm-8"></div>
</div>