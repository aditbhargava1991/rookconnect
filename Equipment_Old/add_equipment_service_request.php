<?php
/*
Add	Equipment Request
*/
include ('../include.php');
error_reporting(0);

if (isset($_POST['submit'])) {
    $equipmentid = $_POST['equipmentid'];
    $defect =	$_POST['defect'];
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

    $query_insert_equipment = "INSERT INTO `equipment_service_request` (`equipmentid`, `defect`, `comment`) VALUES	('$equipmentid', '$defect', '$comment')";
    $result_insert_equipment = mysqli_query($dbc, $query_insert_equipment);


    echo '<script type="text/javascript"> window.location.replace("service_request.php?category=Top"); </script>';

   // mysqli_close($dbc); //Close the DB Connection
}

?>
<script>
$( document ).ready(function() {
	$(".servicerecordid").prop('required',true);
$('input:radio[name="defect"]').change(
    function(){
        if ($(this).is(':checked') && $(this).val() == 'Need Not Be Corrected') {
            $(".servicerecordid").prop('required',false);
		    $(".servrec").hide();
        }

		if ($(this).is(':checked') && $(this).val() == 'Corrected') {
            $(".servicerecordid").prop('required',true);
			$(".servrec").show();
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

		<h1>Add A	New	Service Request</h1>

		<div class="pad-left gap-top double-gap-bottom"><a href="service_request.php?category=Top" class="btn config-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="add_equipment_service_request.php" enctype="multipart/form-data" class="form-horizontal" role="form">

		<input type="hidden" name="reportid" value="<?php echo $_GET['reportid']?> ">
            <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='service_request'"));
                $value_config = ','.$get_field_config['equipment'].',';
            ?>

		    <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { ?>
			<script>
			$(document).ready(function() {
				$('[name=equipmentid]').change();
			});
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
			    <label for="phone_number" class="col-sm-4 control-label">Category:</label>
			    <div class="col-sm-8">
                    <select data-placeholder="Select Category..."  name="category" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <?php
                            $query = mysqli_query($dbc,"SELECT category FROM equipment $access_query_where GROUP BY category ORDER BY category");
                            while($row = mysqli_fetch_array($query)) {
                                echo "<option value='". $row['category']."'>".$row['category'].'</option>';
                            }
                        ?>
                    </select>
                </div>
		    </div>
            <div class="form-group">
			    <label for="phone_number" class="col-sm-4 control-label">Equipment:</label>
			    <div class="col-sm-8">
                    <select data-placeholder="Select Equipment..."  name="equipmentid" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <?php
                            $query = mysqli_query($dbc,"SELECT equipmentid, unit_number, type, make, model, category FROM equipment $access_query_where order by unit_number");
                            while($row = mysqli_fetch_array($query)) {
                                if ($_GET['eqid'] == $row['equipmentid']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".($_GET['eqid'] == $row['equipmentid'] ? 'selected' : '')." data-category='".$row['category']."' value='". $row['equipmentid']."'>".$row['unit_number'].':'.$row['model'].':'.$row['type'].'</option>';
                            }
                        ?>
                    </select>
                </div>
		    </div>
            <?php } ?>

		    <?php if (strpos($value_config, ','."Service Record".',') !== FALSE) { ?>
            <!--
            <div class="form-group servrec">
			    <label for="phone_number"  class="col-sm-4 control-label">Service Record:</label>
			    <div class="col-sm-8">
                    <?php
                    $recordid = $_GET['recordid'];
                    ?>
                    <input name="servicerecordid" type="text"  value="<?php echo $recordid; ?>" class="form-control" />
                </div>
		    </div>
            -->
            <?php } ?>

			<?php if (strpos($value_config, ','."Defects".',') !== FALSE) { ?>
            <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Defects:</label>
				<div class="col-sm-8">
				  <input type="radio" checked="checked" name="defect" value="Corrected">Corrected
				  <input type="radio" name="defect" value="Need Not Be Corrected">Need Not Be Corrected
				</div>
			</div>
            <?php } ?>

		  <?php if (strpos($value_config, ','."Comment".',') !== FALSE) { ?>
          <div class="form-group">
			<label for="phone_number" class="col-sm-4 control-label">Comment:</label>
			<div class="col-sm-8">
			  <textarea name="comment" rows="5" cols="50" class="form-control"></textarea>
			</div>
		  </div>
          <?php } ?>

            <div class="form-group">
				<p><span class="brand-color"><em>Required	Fields *</em></span></p>
            </div>

		  <div class="form-group">
			<div class="col-sm-6">
				<a href="equipment.php?filter=Top"	class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	subm_but pull-right">Submit</button>
			</div>
		  </div>

        

		</form>

	</div>
  </div>

<?php include ('../footer.php'); ?>
