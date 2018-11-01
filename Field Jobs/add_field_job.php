<?php
/*
Add	Job
*/
include ('../include.php');
checkAuthorised('field_job');
error_reporting(0);

if (isset($_POST['submit'])) {

	$job_number = $_POST['job_number'];
	$contactid = $_POST['contactid'];
	$ratecardid = $_POST['ratecardid'];
    $clientid = $_POST['clientid'];

	$description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
	$foremanid = ','.implode(',',$_POST['foremanid']).',';
	$afe_number = filter_var($_POST['afe_number'],FILTER_SANITIZE_STRING);

    $additional_info = filter_var($_POST['additional_info'],FILTER_SANITIZE_STRING);
    $siteid= $_POST['siteid'];
	if($siteid=='NEW SITE') {
		$site_name = $_POST['new_site_location'];
		$result = mysqli_query($dbc, "INSERT INTO `field_sites` (`site_name`, `clientid`) VALUES ('$site_name', '$clientid')");
		$siteid = mysqli_insert_id($dbc);
		$before_change = '';
    $history = "field_sites entry has been added. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
	}
	$job_date= $_POST['job_date'];
    $same_address = $_POST['same_address'];
    $created_by = $_SESSION['contactid'];

	if(empty($_POST['jobid'])) {
		$query_insert_site = "INSERT INTO `field_jobs` (`job_number`, `clientid`, `contactid`, `ratecardid`, `foremanid`, `afe_number`, `additional_info`, `siteid`, `description`, `job_date`) VALUES	('$job_number', '$clientid', '$contactid', '$ratecardid', '$foremanid', '$afe_number', '$additional_info', '$siteid', '$description', '$job_date')";
		$result_insert_site	= mysqli_query($dbc, $query_insert_site);
        $url = 'Added';
		$before_change = '';
    $history = "field_jobs entry has been added. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
	} else {
		$jobid = $_POST['jobid'];
		$query_update_site = "UPDATE `field_jobs` SET `job_date` = '$job_date', `job_number` = '$job_number', `clientid` = '$clientid', `contactid` = '$contactid', `ratecardid`	= '$ratecardid', `foremanid` = '$foremanid', `afe_number` = '$afe_number', `additional_info` = '$additional_info', `siteid` = '$siteid', `description` =	'$description' WHERE	`jobid` = '$jobid'";
		$result_update_site	= mysqli_query($dbc, $query_update_site);
        $url = 'Updated';
		$before_change = '';
    $history = "field_jobs entry has been updated for fsid -> $fsid. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
	}
    echo '<script type="text/javascript"> window.location.replace("field_jobs.php"); </script>';

	//header('Location: field_jobs.php');

   // mysqli_close($dbc); //Close the DB Connection
}
$edit_result = mysqli_fetch_array(mysqli_query($dbc, "select field_list from field_config_field_jobs where tab='jobs'"));
$edit_config = $edit_result['field_list'];
if(str_replace(',','',$edit_config) == '') {
	$edit_config = ',date,job,contact,rate,foreman,afe,additional,location,overview,';
}
?>

</head>
<script type="text/javascript">
$(document).ready(function() {

    $("#form1").submit(function( event ) {
        var job_date = $("#job_date").val();
        var contactid = $("#contactid").val();
        var ratecardid = $("#ratecardid").val();
        var job_number = $("input[name=job_number]").val();
        var afe_number = $("input[name=afe_number]").val();

        if (job_date == '' || contactid == '' || job_number == '' || afe_number == '') {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });

    $("#clientid").change(function() {
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "field_job_ajax_all.php?from=job&name="+this.value,
            dataType: "html",   //expect html to be returned
            success: function(response){
                $('#contactid').html(response);
                $("#contactid").trigger("change.select2");
            }
        });

        $.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "field_job_ajax_all.php?from=site_job&name="+this.value,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#siteselect').html(response);
				$("#siteselect").trigger("change.select2");
			}
		});

        $.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "field_job_ajax_all.php?from=site_rc&name="+this.value,
			dataType: "html",   //expect html to be returned
			success: function(response){
                $("#ratecardid").html(response);
			    $("#ratecardid").trigger("change.select2");
			}
		});
	});

	$('#siteselect').change(function() {
		if(this.value == 'NEW SITE') {
			$('#new_site_div').show();
		} else {
			$('#new_site_div').hide();
			$('[name=new_site_location]').val('');
		}
	});
});
$(document).on('change', 'select[name="contactid"]', function() { selectContact(this); });
function jobDate() {
	var job_date = document.getElementById("job_date").value;
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "field_job_ajax_all.php?from=jobdate&name="+job_date,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $('#job_number').val(response);
		}
	});
}
function selectContact(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "field_job_ajax_all.php?from=job_contact&name="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*FFM*');
            //$("#ratecardid").html(result[0]);
			//$("#ratecardid").trigger("change.select2");

            //$("#siteselect").html(result[1]);
			//$("#siteselect").trigger("change.select2");

		}
	});
}
</script>

<body>
<?php include_once ('../navigation.php');

?>
<div class="container">
  <div class="row">

		<h1	class="triple-pad-bottom">Job</h1>
		<div class="pad-left double-gap-bottom"><a href="field_jobs.php" class="btn config-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<?php
		$job_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT job_number FROM field_jobs WHERE DATE(job_date) = CURDATE() ORDER BY jobid DESC LIMIT 1"));
        $jn = $job_result['job_number'];
        $jn_last = explode('-',$jn);
		$job_number = date('y-m-d').'-'.($jn_last[3]+1);
        $job_date = date('Y-m-d');
        $clientid = '';
		$contactid = '';
		$ratecardid = '';
		$foremanid = '';
		$afe_number = '';
        $additional_info = '';
		$siteid = '';
        $description = '';

		if(!empty($_GET['jobid'])) {

			$jobid = $_GET['jobid'];
			$get_job =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	field_jobs WHERE jobid='$jobid'"));
            $job_date = $get_job['job_date'];
			$job_number = $get_job['job_number'];
            $clientid = $get_job['clientid'];
            $description = $get_job['description'];
			$contactid =	$get_job['contactid'];
			$ratecardid	= $get_job['ratecardid'];
			$foremanid = $get_job['foremanid'];
			$afe_number =	$get_job['afe_number'];
            $additional_info = $get_job['additional_info'];
			$siteid =	$get_job['siteid'];
		?>
		<input type="hidden" id="jobid"	name="jobid" value="<?php echo $jobid ?>" />
		<?php	}	   ?>

		<?php if(strpos($edit_config,',date,') !== false): ?>
			<div class="form-group clearfix orientation_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Job Date<span class="text-red">*</span>:</label>
				<div class="col-sm-8">
					<input name="job_date" id="job_date" value="<?php echo $job_date; ?>" onchange="jobDate()" type="text" style="width: 90px;" class="datepicker">
				</div>
			</div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',job,') !== false): ?>
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Job#<span class="text-red">*</span>:</label>
			<div class="col-sm-8">
			  <input name="job_number" id="job_number" type="text" value="<?php echo $job_number; ?>" class="form-control" />
			</div>
		  </div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',contact,') !== false): ?>
			<div class="form-group customer_db">
			  <label for="site_name" class="col-sm-4 control-label">Customer<span class="text-red">*</span>:</label>
			  <div class="col-sm-8">
				<select data-placeholder="Choose a Customer..." id="clientid" name="clientid" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <?php
					$query = sort_contacts_query(mysqli_query($dbc,"SELECT distinct(name), contactid FROM contacts WHERE category='Client' OR category='Customer' OR category='Business' ORDER BY name"));
					foreach($query as $row) {
						if ($clientid == $row['contactid']) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option ".$selected." value='". $row['contactid']."'>".$row['name'].'</option>';
					}
				  ?>
				</select>
			  </div>
			</div>

            <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">Contact<span class="text-red">*</span>:</label>
                <div class="col-sm-8">
                    <select id="contactid" data-placeholder="Choose a Contact..." name="contactid" class="chosen-select-deselect form-control" width="380">
                    <?php
                        $query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name, name FROM contacts WHERE '$clientid' IN (`businessid`,'')"));
                        echo '<option value=""></option>';
						foreach($query as $row) {
                            if ($contactid == $row['contactid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='".$row['contactid']."'>".$row['name'].' '.$row['first_name'].' '.$row['last_name'].'</option>';
                        }
                    ?>
                    </select>
                </div>
            </div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',rate,') !== false): ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label">Rate Card<span class="text-red">*</span>:</label>
			  <div class="col-sm-8">
				<select data-placeholder="Choose a Rate Card..." name="ratecardid" id="ratecardid" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <?php
					$query = mysqli_query($dbc,"SELECT CONCAT('customer*',ratecardid) id, rate_card_name FROM rate_card WHERE deleted=0 AND on_off=1 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
						SELECT CONCAT('company*',MIN(`companyrcid`)) id, `rate_card_name` FROM `company_rate_card` WHERE DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `rate_card_name` HAVING MIN(`deleted`)=0 UNION
						SELECT 'position*' id, 'Rate Card by Position' rate_card_name FROM `position_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted` UNION
						SELECT 'staff*' id, 'Rate Card by Staff' rate_card_name FROM `staff_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted` UNION
						SELECT 'equipment*' id, 'Rate Card by Equipment' rate_card_name FROM `equipment_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted` UNION
						SELECT 'category*' id, 'Rate Card by Equipment Category' rate_card_name FROM `category_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted`");
					while($row = mysqli_fetch_array($query)) {
						if ($ratecardid == $row['id'] || 'customer*'.$ratecardid == $row['id']) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option ".$selected." value='". $row['id']."'>".$row['rate_card_name'].'</option>';
					}
				  ?>
				</select>
			  </div>
			</div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',foreman,') !== false): ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label">Foreman<span class="text-red">*</span>:</label>
			  <div class="col-sm-8">
				<select data-placeholder="Choose a Foreman..." name="foremanid[]" multiple class="chosen-select-deselect form-control" width="380">
				    <?php
						$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
						foreach($query as $id) {
							$selected = '';
							$selected = strpos(','.$foremanid.',', ','.$id.',') !== FALSE ? 'selected = "selected"' : '';
							echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
						}
					?>
				</select>
			  </div>
			</div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',afe,') !== false): ?>
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">AFE#<span class="text-red">*</span>:</label>
			<div class="col-sm-8">
			  <input name="afe_number" type="text" value="<?php echo $afe_number; ?>" class="form-control" />
			</div>
		  </div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',additional,') !== false): ?>
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Additional Info:</label>
			<div class="col-sm-8">
			  <input name="additional_info" type="text" value="<?php echo $additional_info; ?>" class="form-control" />
			</div>
		  </div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',location,') !== false): ?>
    		<div class="form-group location_db">
			  <label for="site_name" class="col-sm-4 control-label">Customer Site Location:</label>
			  <div class="col-sm-8">
				<select data-placeholder="Choose a Location..." id="siteselect" name="siteid" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <option value="NEW SITE">New Site Location</option>
				  <?php
					$query = mysqli_query($dbc,"SELECT siteid, site_name FROM field_sites WHERE deleted=0 AND clientid='$clientid'");
					while($row = mysqli_fetch_array($query)) {
						if ($siteid == $row['siteid']) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option ".$selected." value='". $row['siteid']."'>".$row['site_name'].'</option>';
					}
				  ?>
				</select>
			  </div>
			</div>
    		<div class="form-group location_db" id="new_site_div" style="display:none;">
			  <label for="site_name" class="col-sm-4 control-label">New Site Location:</label>
			  <div class="col-sm-8">
				<input type="text" class="form-control" name="new_site_location" value="">
			  </div>
			</div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',overview,') !== false): ?>
			<div class="form-group">
				<label for="additional_note" class="col-sm-4 control-label">Job Overview:</label>
				<div class="col-sm-8">
					<textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
				</div>
			</div>
		<?php endif; ?>

		<div class="form-group">
			<div class="col-sm-4">
				<p><span class="text-red pull-right"><em>Required	Fields *</em></span></p>
			</div>
			<div class="col-sm-8"></div>
		</div>

		  <div class="form-group">
			<div class="col-sm-4 clearfix">
				<a href="field_jobs.php" class="btn brand-btn pull-right">Back</a>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-8">
                <?php
                if(empty($_GET['jobid'])) { ?>
                    <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Create Job</button>
                <?php } else { ?>
                    <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Update Job</button>
                <?php } ?>
			</div>
		  </div>

		</form>
	</div>
  </div>

<?php include ('../footer.php'); ?>
