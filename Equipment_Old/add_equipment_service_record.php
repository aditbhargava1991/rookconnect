<?php
/*
Add	Equipment
*/
include ('../include.php');
error_reporting(0);
$from_url = "service_record.php?category=Top";
if(!empty($_GET['from_url'])) {
	$from_url = $_GET['from_url'];
}

if (isset($_POST['submit'])) {

    $equipmentid = $_POST['equipmentid'];
	$inventoryid = $_POST['inventoryid'];
    $service_date = $_POST['service_date'];
    $advised_service_date = $_POST['advised_service_date'];
    $description_of_job = filter_var($_POST['description_of_job'],FILTER_SANITIZE_STRING);
    $service_record_mileage = filter_var($_POST['service_record_mileage'],FILTER_SANITIZE_STRING);
    $service_record_hours = filter_var($_POST['service_record_hours'],FILTER_SANITIZE_STRING);
    $completed =	$_POST['completed'];
    $contactid = $_POST['contactid'];
    $service_type = $_POST['service_type'];
	$vendorid = ','.implode(',',$_POST['vendorid']).',';
    $cost =	filter_var($_POST['cost'],FILTER_SANITIZE_STRING);

    $service_record_kilometers = filter_var($_POST['service_record_kilometers'],FILTER_SANITIZE_STRING);
	$rec_next_service_mileage = filter_var($_POST['rec_next_service_mileage'],FILTER_SANITIZE_STRING);
	$doc_name = htmlspecialchars($_FILES["file"]["name"], ENT_QUOTES);

	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}

    if(empty($_POST['servicerecordid'])) {
		move_uploaded_file($_FILES["file"]["tmp_name"], "download/" . $_FILES["file"]["name"]) ;

        $query_insert_equipment = "INSERT INTO `equipment_service_record` (`reportid`, `equipmentid`, `inventoryid`, `service_date`, `advised_service_date`, `description_of_job`, `service_record_mileage`, `service_record_hours`, `completed`, `contactid`, `service_type`, `vendorid`, `cost`, `service_record_kilometers`, `rec_next_service_mileage`, `file`) VALUES	('$reportid', '$equipmentid', '$inventoryid', '$service_date', '$advised_service_date', '$description_of_job', '$service_record_mileage', '$service_record_hours', '$completed', '$contactid', '$service_type', '$vendorid', '$cost', '$service_record_kilometers', '$rec_next_service_mileage', '$doc_name')";
        $result_insert_equipment	= mysqli_query($dbc, $query_insert_equipment);
        $url = 'Added';
    } else {
        $servicerecordid = $_POST['servicerecordid'];
		if($doc_name == '') {
			$photo_update = $_POST['photo_file'];

		} else {
			$photo_update = $doc_name;
		}
		move_uploaded_file($_FILES["file"]["tmp_name"], "download/" . $photo_update);
        $query_update_equipment = "UPDATE `equipment_service_record` SET `equipmentid` = '$equipmentid', `inventoryid` = '$inventoryid', `service_date` = '$service_date', `advised_service_date` = '$advised_service_date', `description_of_job` = '$description_of_job', `service_record_mileage` = '$service_record_mileage', `service_record_hours` = '$service_record_hours', `completed` = '$completed', `contactid` = '$contactid', `service_type` = '$service_type', `vendorid` = '$vendorid', `cost` = '$cost', `service_record_kilometers` = '$service_record_kilometers', `rec_next_service_mileage` = '$rec_next_service_mileage', `file` = '$photo_update' WHERE	`servicerecordid` = '$servicerecordid'";
        $result_update_equipment	= mysqli_query($dbc, $query_update_equipment);
        $url = 'Updated';
    }

    echo '<script type="text/javascript"> window.location.replace(""); </script>';

   // mysqli_close($dbc); //Close the DB Connection
}

?>
<script type="text/javascript">
    $(document).ready(function () {

        $("#form1").submit(function( event ) {
            var equipmentid = $("#equipmentid").val();
            if (equipmentid == '') {
                alert("Please make sure you have filled in all of the required fields.");
                return false;
            }
        });

        $("#make").change(function() {
            if($( "#make option:selected" ).text() == 'Other') {
                    $( "#make_name" ).show();
            } else {
                $( "#make_name" ).hide();
            }
        });
    });
    $(document).on('change', 'select[name="category"]', function() { filterCategory(this.value); });
    $(document).on('change', 'select[name="equipmentid"]', function() { $('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2').change(); });
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('equipment');
include_once ('../Equipment/region_location_access.php');
?>
<div class="container">
  <div class="row">

		<h1>Add A	New	Service Record</h1>

		<div class="pad-left gap-top double-gap-bottom"><a href="<?= $from_url ?>" class="btn config-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='service_record'"));
        $value_config = ','.$get_field_config['equipment'].',';

        $equipmentid = '';
		$category = '';
		$inventoryid = '';
        $service_date = '';
        $advised_service_date = '';
        $description_of_job = '';
        $service_record_mileage = '';
        $service_record_hours = '';
        $completed = '';
        $contactid = '';
        $service_type = '';
        $vendorid = '';
        $cost = '';
        $service_record_kilometers = '';
		$rec_next_service_mileage = '';
		$doc_name = '';

		if(!empty($_GET['servicerecordid'])) {

			$servicerecordid = $_GET['servicerecordid'];
			$get_equipment = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_service_record.*, equipment.category FROM equipment_service_record LEFT JOIN `equipment` ON `equipment_service_record`.`equipmentid`=`equipment`.`equipmentid` WHERE servicerecordid='$servicerecordid'"));

            $equipmentid = $get_equipment['equipmentid'];
			$category = $get_equipment['category'];
			$inventoryid = $get_equipment['inventoryid'];
            $service_date = $get_equipment['service_date'];
            $advised_service_date = $get_equipment['advised_service_date'];
            $description_of_job = $get_equipment['description_of_job'];
            $service_record_mileage = $get_equipment['service_record_mileage'];
            $service_record_hours = $get_equipment['service_record_hours'];
            $completed = $get_equipment['completed'];
            $contactid = $get_equipment['contactid'];
            $service_type = $get_equipment['service_type'];
            $vendorid = $get_equipment['vendorid'];
            $cost = $get_equipment['cost'];
            $service_record_kilometers = $get_equipment['service_record_kilometers'];
			$rec_next_service_mileage = $get_equipment['rec_next_service_mileage'];
			$doc_name = $get_equipment['file'];
		?>
        <input type="hidden" id="servicerecordid" name="servicerecordid" value="<?php echo $servicerecordid; ?>" />
		<?php	}	   ?>

            <?php if (strpos($value_config, ','."Service Date".',') !== FALSE) { ?>
            <div class="form-group clearfix completion_date">
                <label for="first_name" class="col-sm-4 control-label text-right">Service Date:</label>
                <div class="col-sm-8">
                    <input name="service_date" value="<?php echo $service_date; ?>" type="text" class="datepicker form-control"></p>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Advised Service Date".',') !== FALSE) { ?>
            <div class="form-group clearfix completion_date">
                <label for="first_name" class="col-sm-4 control-label text-right">Advised Service Date:</label>
                <div class="col-sm-8">
                    <input name="advised_service_date" value="<?php echo $advised_service_date; ?>" type="text" class="datepicker form-control"></p>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { ?>
			<script>
			function filterCategory(cat) {
				$('[name=equipmentid] option').each(function() {
					if(cat == '' || cat == $(this).data('category')) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
				$('[name=equipmentid]').trigger('change.select2');
			}
			</script>
		    <div class="form-group">
			    <label for="phone_number" class="col-sm-4 control-label">Category<span class="hp-red">*</span>:</label>
			    <div class="col-sm-8">
                    <select data-placeholder="Select Category..."  name="category" id="category" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <?php
                            $query = mysqli_query($dbc,"SELECT category FROM equipment $access_query_where GROUP BY category ORDER BY category");
                            while($row = mysqli_fetch_array($query)) {
                                echo "<option ".($category == $row['category'] ? 'selected' : '')." value='". $row['category']."'>".$row['category'].'</option>';
                            }
                        ?>
                    </select>
                </div>
		    </div>
		    <div class="form-group">
			    <label for="phone_number" class="col-sm-4 control-label">Equipment<span class="hp-red">*</span>:</label>
			    <div class="col-sm-8">
                    <select data-placeholder="Select Equipment..."  name="equipmentid" id="equipmentid" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <?php
                            $query = mysqli_query($dbc,"SELECT equipmentid, unit_number, type, make, model, category FROM equipment $access_query_where order by unit_number");
                            while($row = mysqli_fetch_array($query)) {
                                echo "<option ".($equipmentid == $row['equipmentid'] ? 'selected' : '')." data-category='".$row['category']."' value='". $row['equipmentid']."'>".$row['unit_number'].' : '.$row['model'].' : '.$row['type'].'</option>';
                            }
                        ?>
                    </select>
                </div>
		    </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Inventory".',') !== FALSE) { ?>
		    <div class="form-group">
			    <label for="phone_number" class="col-sm-4 control-label">Inventory:</label>
			    <div class="col-sm-8">
                    <select data-placeholder="Choose a Inventory Item..."  name="inventoryid" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <?php
                            $query = mysqli_query($dbc,"SELECT inventoryid, code, category, sub_category FROM inventory order by code");
                            while($row = mysqli_fetch_array($query)) {
                                if ($inventoryid == $row['inventoryid']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='". $row['inventoryid']."'>".$row['code'].' : '.$row['category']. ' : '.$row['sub_category'].'</option>';
                            }
                        ?>
                    </select>
                </div>
		    </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Description of Job".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="phone_number" class="col-sm-4 control-label">Description of Job:</label>
			<div class="col-sm-8">
			  <input name="description_of_job" type="text" value="<?php echo $description_of_job; ?>" class="form-control"/>
			</div>
		  </div>
          <?php } ?>

          <?php if (strpos($value_config, ','."Service Record Mileage".',') !== FALSE) { ?>
          <div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label">Service Record Mileage:</label>
			<div class="col-sm-8">
			  <input name="service_record_mileage" type="text" value="<?php	echo $service_record_mileage; ?>" class="form-control"/>
			</div>
		  </div>
          <?php } ?>

          <?php if (strpos($value_config, ','."Hours".',') !== FALSE) { ?>
          <div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label">Hours:</label>
			<div class="col-sm-8">
			  <input name="service_record_hours" type="text" value="<?php echo $service_record_hours; ?>" class="form-control"/>
			</div>
		  </div>
          <?php } ?>

          <?php if (strpos($value_config, ','."Completed".',') !== FALSE) { ?>
          <div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label"><input type="checkbox" <?php if ($completed == '1') { echo " checked"; } ?> name="completed" value=1></label>
			<div class="col-sm-8">
			  Completed
			</div>
		  </div>
          <?php } ?>

            <?php if (strpos($value_config, ','."Service Type".',') !== FALSE) { ?>
            <div class="form-group">
              <label for="site_name" class="col-sm-4 control-label">Service Type:</label>
              <div class="col-sm-8">
                <select data-placeholder="Choose a Type..." name="service_type" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <option value="Oil Change">Oil Change</option>
                  <option value="Tires">Tires</option>
                  <option value="CVIP Inspections">CVIP Inspections</option>
                  <option value="Fleet Registration">Fleet Registration</option>
                  <option value="Repairs / Maintenance">Repairs / Maintenance</option>
                </select>
              </div>
            </div>
            <?php } ?>

         <?php if (strpos($value_config, ','."Kilometers".',') !== FALSE) { ?>
         <div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label">Kilometers:</label>
			<div class="col-sm-8">
			  <input name="service_record_kilometers" type="text" value="<?php	echo $service_record_kilometers; ?>" class="form-control"/>
			</div>
		  </div>
          <?php } ?>

          <?php if (strpos($value_config, ','."Recommended Next Service Mileage".',') !== FALSE) { ?>
          <div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label">Recommended Next Service Mileage:</label>
			<div class="col-sm-8">
			  <input name="rec_next_service_mileage" type="text" value="<?php	echo $rec_next_service_mileage; ?>" class="form-control"/>
			</div>
		  </div>
          <?php } ?>

		  <?php if (strpos($value_config, ','."Receipt/Document".',') !== FALSE) { ?>
          <div class="form-group">
			<label for="file[]" class="col-sm-4 control-label">Receipt/Document:
                <span class="popover-examples list-inline">&nbsp;
                <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
            </label>
			<div class="col-sm-8">
			<?php if((!empty($_GET['servicerecordid'])) && ($doc_name != '')) {
				echo '<a href="download/'.$doc_name.'" target="_blank">View</a>';
				?>
				<input type="hidden" name="photo_file" value="<?php echo $doc_name; ?>" />
				<input name="file" type="file" id="file" data-filename-placement="inside" class="form-control" />
			  <?php } else { ?>
			  <input name="file" type="file" id="file" data-filename-placement="inside" class="form-control" />
			  <?php } ?>
			</div>
		  </div>
          <?php } ?>

            <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
            <div class="form-group">
              <label for="site_name" class="col-sm-4 control-label">Staff:</label>
              <div class="col-sm-8">
                <select data-placeholder="Choose a Staff Member..." name="contactid" id="employee_name" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." order by first_name");
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
            <?php } ?>

          <?php if (strpos($value_config, ','."Vendor".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label">Vendor:</label>
			<div class="col-sm-8">
                <select data-placeholder="Choose a Vendor..." id="vendorid" name="vendorid[]" class="chosen-select-deselect form-control" width="380" multiple>
                  <?php
                    $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE deleted=0 AND category='Vendor' order by name");
                    while($row = mysqli_fetch_array($query)) { ?>
						<option <?= (strpos(','.$vendorid.',', ','.$row['contactid'].',') !== FALSE ? "selected" : '') ?> value='<?= $row['contactid'] ?>' ><?= decryptIt($row['name']) ?></option>
					<?php } ?>
                </select>
			</div>
		  </div>
          <?php } ?>

          <?php if (strpos($value_config, ','."Service Record Cost".',') !== FALSE) { ?>
		  <div class="form-group">
			<label for="phone_number" class="col-sm-4 control-label">Service Record Cost:</label>
			<div class="col-sm-8">
			  <input name="cost" type="text" value="<?php echo $cost; ?>" class="form-control"/>
			</div>
		  </div>
          <?php } ?>

            <div class="form-group">
				<p><span class="brand-color"><em>Required	Fields *</em></span></p>
            </div>

		  <div class="form-group">
			<div class="col-sm-6">
				<a href="<?= $from_url ?>"	class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
			</div>
		  </div>



		</form>

	</div>
  </div>

<?php include ('..footer.php'); ?>
