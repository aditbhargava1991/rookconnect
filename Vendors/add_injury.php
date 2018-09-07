<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0);

if (isset($_POST['submit'])) {
    //Injury
    $contactid = $_POST['contactid'];
    $serviceid = $_POST['serviceid'];
    $injury_therapistsid = $_POST['injury_therapistsid'];
    $injury_name = filter_var($_POST['injury_name'],FILTER_SANITIZE_STRING);
    $injury_date = filter_var($_POST['injury_date'],FILTER_SANITIZE_STRING);
    $injury_type = filter_var($_POST['injury_type'],FILTER_SANITIZE_STRING);

    $insurer = filter_var($_POST['insurer'],FILTER_SANITIZE_STRING);
    $adjusterid = $_POST['adjusterid'];
    $today_date = date('Y-m-d');
    $treatment_plan = $_POST['treatment_plan'];

    move_uploaded_file($_FILES["injury_reg_form"]["tmp_name"], "Download/".$_FILES["injury_reg_form"]["name"]) ;

    $injury_reg_form = htmlspecialchars($_FILES["injury_reg_form"]["name"], ENT_QUOTES);

    if($_FILES["injury_other_form"]["name"] != '') {
        $injury_other_form = '#$#'.implode('#$#', $_FILES["injury_other_form"]["name"]);
    } else {
        $injury_other_form = '';
    }

    for($i = 0; $i < count($_FILES['injury_other_form']['name']); $i++) {
        move_uploaded_file($_FILES["injury_other_form"]["tmp_name"][$i], "Download/".$_FILES["injury_other_form"]["name"][$i]) ;
    }

    if(empty($_POST['injuryid'])) {
        $query_insert_injury = "INSERT INTO `patient_injury` (`contactid`, `injury_therapistsid`, `injury_name`, `injury_type`, `insurer`, `adjusterid`, `injury_date`, `injury_reg_form`, `injury_other_form`, `today_date`, `treatment_plan`) VALUES ('$contactid', '$injury_therapistsid', '$injury_name', '$injury_type', '$insurer', '$adjusterid', '$injury_date', '$injury_reg_form', '$injury_other_form', '$today_date', '$treatment_plan')";
        $result_insert_injury = mysqli_query($dbc, $query_insert_injury);
        $before_change = '';
  			$history = "New Patient injury added. <br />";
  			add_update_history($dbc, 'vendorpl_history', $history, '', $before_change);

    } else {
        $injuryid = $_POST['injuryid'];
        $query_update_staff = "UPDATE `patient_injury` SET `treatment_plan` = '$treatment_plan', `injury_therapistsid` = '$injury_therapistsid', `injury_name` = '$injury_name', `injury_type` = '$injury_type', `insurer` = '$insurer', `adjusterid` = '$adjusterid', `injury_date` = '$injury_date', `injury_reg_form` = CONCAT(injury_reg_form,'$injury_reg_form'), `injury_other_form` = CONCAT(injury_other_form,'$injury_other_form') WHERE `injuryid` = '$injuryid'";
        $result_update_staff = mysqli_query($dbc, $query_update_staff);
        $before_change = '';
  			$history = "Patient injury is been updated. <br />";
  			add_update_history($dbc, 'vendorpl_history', $history, '', $before_change);
    }

    //Injury

    echo '<script type="text/javascript"> window.location.replace("add_contacts.php?category=Patient&contactid='.$contactid.'"); </script>';

    mysqli_close($dbc); //Close the DB Connection
}

?>
<script type="text/javascript">
    $(document).ready(function () {

     $("#form1").submit(function( event ) {
        var therapistid = $("#therapistid").val();
        var injury_name = $("#injury_name").val();
        var category = $("#category").val();
        //var therapistsid = $("#therapistsid").val();
        if (category == '' || injury_name =='') {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });
});
$(document).on('change', 'select[name="injury_type"]', function() { selectType(this); });

</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('vendors');
?>
<div class="container">
  <div class="row">

        <h1 class="triple-pad-bottom">Injury</h1>

		<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        <?php
        $contactid = $_GET['contactid'];
        $serviceid = '';
        $injury_therapistsid = '';
        $injury_name = '';
        $injury_date = '';
        $injury_type = '';
        $injury_reg_form = '';
        $injury_other_form = '';
        $insurer = '';
        $adjusterid = '';
        $treatment_plan = '';
        if(!empty($_GET['injuryid'])) {
            $injuryid = $_GET['injuryid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM patient_injury WHERE injuryid='$injuryid'"));
            $contactid = $get_contact['contactid'];
            $serviceid = $get_contact['serviceid'];
            $injury_therapistsid = $get_contact['injury_therapistsid'];
            $injury_name = $get_contact['injury_name'];
            $injury_date = $get_contact['injury_date'];
            $injury_type = $get_contact['injury_type'];
            $insurer = $get_contact['insurer'];
            $adjusterid = $get_contact['adjusterid'];
            $injury_reg_form = $get_contact['injury_reg_form'];
            $injury_other_form = $get_contact['injury_other_form'];
            $treatment_plan = $get_contact['treatment_plan'];
            echo '<input type="hidden" name="injuryid" value="'.$_GET['injuryid'].'" />';
        }
        ?>

         <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Staff:</label>
            <div class="col-sm-8">
                <select data-placeholder="Select a Staff..." name="injury_therapistsid" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Staff' AND `status`=1 AND deleted=0");
                    while($row = mysqli_fetch_array($query)) {
                        if ($injury_therapistsid == $row['contactid']) {
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
            <label for="site_name" class="col-sm-4 control-label">Patient<span class="empire-red">*</span>:</label>
            <div class="col-sm-8">
                <select id="contactid" data-placeholder="Select a Patient..." name="contactid" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Patient' AND status=1 AND deleted=0");
                    while($row = mysqli_fetch_array($query)) {
                        if ($contactid == $row['contactid']) {
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
              <label for="ship_zip" class="col-sm-4 control-label">Injury Name<span class="empire-red">*</span>:</label>
              <div class="col-sm-8">
                <select data-placeholder="Choose a Name..." name="injury_name" id="injury_name" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <option <?php if ($injury_name=='Thumb') echo 'selected="selected"';?> value="Thumb">Thumb</option>
                    <option <?php if ($injury_name=='Knee') echo 'selected="selected"';?> value="Knee">Knee</option>
                    <option <?php if ($injury_name=='Hand') echo 'selected="selected"';?> value="Hand">Hand</option>
                    <option <?php if ($injury_name=='Forearm') echo 'selected="selected"';?> value="Forearm">Forearm</option>
                    <option <?php if ($injury_name=='Arm') echo 'selected="selected"';?> value="Arm">Arm</option>
                    <option <?php if ($injury_name=='Shoulder') echo 'selected="selected"';?> value="Shoulder">Shoulder</option>
                    <option <?php if ($injury_name=='Back') echo 'selected="selected"';?> value="Back">Back</option>
                    <option <?php if ($injury_name=='Chest') echo 'selected="selected"';?> value="Chest">Chest</option>
                    <option <?php if ($injury_name=='Neck') echo 'selected="selected"';?> value="Neck">Neck</option>
                    <option <?php if ($injury_name=='Thigh') echo 'selected="selected"';?> value="Thigh">Thigh</option>
                    <option <?php if ($injury_name=='Elbow') echo 'selected="selected"';?> value="Elbow">Elbow</option>
                    <option <?php if ($injury_name=='Wrist') echo 'selected="selected"';?> value="Wrist">Wrist</option>
                    <option <?php if ($injury_name=='Hamstring') echo 'selected="selected"';?> value="Hamstring">Hamstring</option>
                    <option <?php if ($injury_name=='Calf') echo 'selected="selected"';?> value="Calf">Calf</option>
                    <option <?php if ($injury_name=='Heel') echo 'selected="selected"';?> value="Heel">Heel</option>
                    <option <?php if ($injury_name=='Ankle') echo 'selected="selected"';?> value="Ankle">Ankle</option>
                    <option <?php if ($injury_name=='Foot') echo 'selected="selected"';?> value="Foot">Foot</option>
                    <option <?php if ($injury_name=='Abdominals') echo 'selected="selected"';?> value="Abdominals">Abdominals</option>
                    <option <?php if ($injury_name=='Groin') echo 'selected="selected"';?> value="Groin">Groin</option>
                    <option <?php if ($injury_name=='Head') echo 'selected="selected"';?> value="Head">Head</option>
                    <option <?php if ($injury_name=='Hip') echo 'selected="selected"';?> value="Hip">Hip</option>
                    <option <?php if ($injury_name=='Jaw') echo 'selected="selected"';?> value="Jaw">Jaw</option>
                    <option <?php if ($injury_name=='Leg') echo 'selected="selected"';?> value="Leg">Leg</option>
                    <option <?php if ($injury_name=='Low back') echo 'selected="selected"';?> value="Low back">Low back</option>
                    <option <?php if ($injury_name=='Lumbar Spine') echo 'selected="selected"';?> value="Lumbar Spine">Lumbar Spine</option>
                    <option <?php if ($injury_name=='Massage') echo 'selected="selected"';?> value="Massage">Massage</option>
                    <option <?php if ($injury_name=='Rib') echo 'selected="selected"';?> value="Rib">Rib</option>
                    <option <?php if ($injury_name=='Tail bone') echo 'selected="selected"';?> value="Tail bone">Tail bone</option>
                    <option <?php if ($injury_name=='Thoracic Spine') echo 'selected="selected"';?> value="Thoracic Spine">Thoracic Spine</option>
                    <option <?php if ($injury_name=='Toe') echo 'selected="selected"';?> value="Toe">Toe</option>
                    <option <?php if ($injury_name=='Vertigo') echo 'selected="selected"';?> value="Vertigo">Vertigo</option>
                    <option <?php if ($injury_name=='Pelvis') echo 'selected="selected"';?> value="Pelvis">Pelvis</option>

                    <option <?php if ($injury_name=='Buttocks') echo 'selected="selected"';?> value="Buttocks">Buttocks</option>
                    <option <?php if ($injury_name=='Clavicle') echo 'selected="selected"';?> value="Clavicle">Clavicle</option>
                    <option <?php if ($injury_name=='Rib') echo 'selected="selected"';?> value="Rib">Rib</option>
                    <option <?php if ($injury_name=='Quad') echo 'selected="selected"';?> value="Quad">Quad</option>
                    <option <?php if ($injury_name=='Rotator Cuff') echo 'selected="selected"';?> value="Rotator Cuff">Rotator Cuff</option>
                    <option <?php if ($injury_name=='Scapula') echo 'selected="selected"';?> value="Scapula">Scapula</option>
                    <option <?php if ($injury_name=='Shin') echo 'selected="selected"';?> value="Shin">Shin</option>
                    <option <?php if ($injury_name=='Sl Joint') echo 'selected="selected"';?> value="Sl Joint">Sl Joint</option>
                    <option <?php if ($injury_name=='Thoracic') echo 'selected="selected"';?> value="Thoracic">Thoracic</option>
                    <option <?php if ($injury_name=='Achilles') echo 'selected="selected"';?> value="Achilles">Achilles</option>
                    <option <?php if ($injury_name=='ACL') echo 'selected="selected"';?> value="ACL">ACL</option>
                    <option <?php if ($injury_name=='Sciatic Nerve') echo 'selected="selected"';?> value="Sciatic Nerve">Sciatic Nerve</option>
                    <option <?php if ($injury_name=='Concussion') echo 'selected="selected"';?> value="Concussion">Concussion</option>
                    <option <?php if ($injury_name=='Glute') echo 'selected="selected"';?> value="Glute">Glute</option>
                    <option <?php if ($injury_name=='Groin') echo 'selected="selected"';?> value="Groin">Groin</option>
                    <option <?php if ($injury_name=='Hamstring') echo 'selected="selected"';?> value="Hamstring">Hamstring</option>
                    <option <?php if ($injury_name=='Humorous') echo 'selected="selected"';?> value="Humorous">Humorous</option>
                    <option <?php if ($injury_name=='Lumbar Spine') echo 'selected="selected"';?> value="Lumbar Spine">Lumbar Spine</option>
                    <option <?php if ($injury_name=='MCL') echo 'selected="selected"';?> value="MCL">MCL</option>
                    <option <?php if ($injury_name=='Concusion') echo 'selected="selected"';?> value="Concusion">Concusion</option>
                    <option <?php if ($injury_name=='Face') echo 'selected="selected"';?> value="Face">Face</option>
                    <option <?php if ($injury_name=='Foot/Feet') echo 'selected="selected"';?> value="Foot/Feet">Foot/Feet</option>
                    <option <?php if ($injury_name=='Full Body') echo 'selected="selected"';?> value="Full Body">Full Body</option>
                    <option <?php if ($injury_name=='Hand-Finger') echo 'selected="selected"';?> value="Hand-Finger">Hand-Finger</option>
                    <option <?php if ($injury_name=='Lower Back') echo 'selected="selected"';?> value="Lower Back">Lower Back</option>
                    <option <?php if ($injury_name=='Massage') echo 'selected="selected"';?> value="Massage">Massage</option>
                    <option <?php if ($injury_name=='Sciatica') echo 'selected="selected"';?> value="Sciatica">Sciatica</option>
                    <option <?php if ($injury_name=='Spine') echo 'selected="selected"';?> value="Spine">Spine</option>
                    <option <?php if ($injury_name=='Upperback') echo 'selected="selected"';?> value="Upperback">Upperback</option>
                    <option <?php if ($injury_name=='Clinic Forms') echo 'selected="selected"';?> value="Clinic Forms">Clinic Forms</option>
                </select>

              </div>
            </div>

          <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Injury Date:</label>
            <div class="col-sm-8">
              <input name="injury_date" type="text" class="datepicker" value="<?php echo $injury_date; ?>">
            </div>
          </div>

          <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Injury File Type<span class="empire-red">*</span>:</label>
            <div class="col-sm-8">
                <select id="category" name="injury_type" class="chosen-select-deselect form-control" width="380">
                    <option value=''></option>
                    <option <?php if ($injury_type=='AHS') echo 'selected="selected"';?> value='AHS'>AHS</option>
                    <option <?php if ($injury_type=='MVA/MVC - Out of Protocol') echo 'selected="selected"';?> value='MVA/MVC - Out of Protocol'>MVA/MVC - Out of Protocol</option>
                    <option <?php if ($injury_type=='MVA/MVC - In Protocol') echo 'selected="selected"';?> value='MVA/MVC - In Protocol'>MVA/MVC - In Protocol</option>
                    <option <?php if ($injury_type=='Orthotics') echo 'selected="selected"';?> value='Orthotics'>Orthotics</option>
                    <option <?php if ($injury_type=='Private Massage') echo 'selected="selected"';?> value='Private Massage'>Private Massage</option>
                    <option <?php if ($injury_type=='Private Physio') echo 'selected="selected"';?> value='Private Physio'>Private Physio</option>
                    <option <?php if ($injury_type=='WCB') echo 'selected="selected"';?> value='WCB'>WCB</option>
                </select>
            </div>
          </div>

           <div class="form-group treatment_plan">
            <label for="site_name" class="col-sm-4 control-label">Treatment Plan:</label>
            <div class="col-sm-8">
              <select name="treatment_plan" data-placeholder="Choose a Plan..." class="chosen-select-deselect form-control" width="380">
					<option value=''></option>
                    <?php
                    for($i = 1; $i <= 50; $i++) { ?>
                        <option <?php if ($treatment_plan == $i) { echo " selected"; } ?> value = '<?php echo $i; ?>'><?php echo $i; ?></option>
                    <?php } ?>
              </select>
            </div>
          </div>

          <!--
          <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Service:</label>
            <div class="col-sm-8">
                <select id="contactid" data-placeholder="Choose a Service..." name="serviceid" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT serviceid, service_code, service_type, fee FROM services WHERE deleted=0");
                    while($row = mysqli_fetch_array($query)) {
                        if ($serviceid == $row['serviceid']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $row['serviceid']."'>".$row['service_code'].' : $'.$row['fee'].'</option>';
                    }
                    ?>
                </select>
            </div>
          </div>

          <?php
          $style = 'style="display:none;"';
          if(strpos($injury_type, 'MVA') !== FALSE) {
            $style = 'style="display:block;"';
          }
          ?>
          <div class="form-group mva_type" <?php echo $style; ?>>
            <label for="position[]" class="col-sm-4 control-label">Insurer:</label>
            <div class="col-sm-8">
                <select onchange="selectInsurer(this)" data-placeholder="Choose a Insurer..." name="insurer" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Insurer' AND deleted=0 ORDER BY name");
                    while($row = mysqli_fetch_array($query)) {
                        if ($insurer == $row['name']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $row['name']."'>".$row['name'].'</option>';
                    }
                  ?>
                </select>
            </div>
          </div>

          <?php if(!empty($_GET['injuryid'])) { ?>
          <div class="form-group mva_type" <?php echo $style; ?>>
            <label for="position[]" class="col-sm-4 control-label">Adjuster:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Adjuster..." id="adjusterid" name="adjusterid" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE name='$insurer' AND category='Adjuster'");
                    while($row = mysqli_fetch_array($query)) {
                        if ($adjusterid == $row['contactid']) {
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
          <?php } else { ?>
          <div class="form-group mva_type" <?php echo $style; ?>>
            <label for="position[]" class="col-sm-4 control-label">Adjuster:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Adjuster..." id="adjusterid" name="adjusterid" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                </select>
            </div>
          </div>
          <?php } ?>

           <div class="form-group">
            <label for="file[]" class="col-sm-4 control-label">Registration Form:
            <span class="popover-examples list-inline">&nbsp;
            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Remove Single/Double Quote from file name"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
            </label>
            <div class="col-sm-8">
              <input name="injury_reg_form" type="file" id="file" data-filename-placement="inside" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label for="file[]" class="col-sm-4 control-label">Other Forms:<br><em>(Insurance, History, etc) </em>
            <span class="popover-examples list-inline">&nbsp;
            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Remove Single/Double Quote from file name"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
            </span>
            </label>
            <div class="col-sm-8">
              <input name="injury_other_form[]" multiple type="file" id="file" data-filename-placement="inside" class="form-control" />
            </div>
          </div>
          -->

         <div class="form-group">
            <div class="col-sm-4">
                <p><span class="empire-red pull-right"><em>Required Fields *</em></span></p>
            </div>
            <div class="col-sm-8"></div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 clearfix">
                <a href="add_contacts.php?category=Patient&contactid=<?php echo $contactid; ?>" class="btn brand-btn pull-right">Back</a>
            </div>
            <div class="col-sm-8">
                <button type="submit" name="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
            </div>
        </div>



        </form>

    </div>
  </div>

<?php include ('../footer.php'); ?>
