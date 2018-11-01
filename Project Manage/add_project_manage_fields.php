<script type="text/javascript">
$(document).ready(function() {

	$("#businessid").change(function() {
        var businessid = $("#businessid").val();
		$.ajax({
			type: "GET",
			url: "project_manage_ajax_all.php?fill=projectname&businessid="+businessid,
			dataType: "html",   //expect html to be returned
			success: function(response){
                var result = response.split('**##**');

				$('#contactid').html(result[0]);
				$("#contactid").trigger("change.select2");

				//$('#ticketid').html(response);
				//$("#ticketid").trigger("change.select2");
			}
		});
	});

	$("#project_path").change(function() {
		var project_path = $("#project_path").val();
		$.ajax({
			type: "GET",
			url: "project_manage_ajax_all.php?fill=project_path_milestone&project_path="+project_path,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#milestone_timeline').html(response);
				$("#milestone_timeline").trigger("change.select2");
			}
		});
	});

	$("#service_type").change(function() {
		var main_service = $("#service_type").find(":selected").text();
		var main_service1 = main_service.replace(/ /g,'');
		var main_service2 = main_service1.replace("&", "__");
		$.ajax({
			type: "GET",
			url: "project_manage_ajax_all.php?fill=ticketservice&service_type="+main_service2,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#service_category').html(response);
				$("#service_category").trigger("change.select2");
			}
		});
	});

    $("#service_category").change(function() {
		var main_service = $("#service_type").find(":selected").text();
		var main_service1 = main_service.replace(/ /g,'');
		var main_service2 = main_service1.replace("&", "__");

		var subservice = $("#service_category").find(":selected").text();
		var subservice1 = subservice.replace(/ /g,'');
		var subservice2 = subservice1.replace("&", "__");

		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "project_manage_ajax_all.php?fill=ticketheading&service_category="+subservice2+"&service_type="+main_service2,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#service_heading').html(response);
				$("#service_heading").trigger("change.select2");
			}
		});
	});

    $('#add_row_doc').on( 'click', function () {
        var clone = $('.additional_doc').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_doc");
        $('#add_here_new_doc').append(clone);
        return false;
    });

    $('#add_row_doc_review').on( 'click', function () {
        var clone = $('.additional_doc_review').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_doc_review");
        $('#add_here_new_doc_review').append(clone);
        return false;
    });

    $('#add_row_link').on( 'click', function () {
        var clone = $('.additional_link').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_link");
        $('#add_here_new_link').append(clone);
        return false;
    });

    $('#add_row_review_link').on( 'click', function () {
        var clone = $('.additional_review_link').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_review_link");
        $('#add_here_new_review_link').append(clone);
        return false;
    });
});

function deleteProject(sel, hide, blank) {
	var typeId = sel.id;
	var arr = typeId.split('_');

    var projectid = $("#projectid").val();

    if(arr[0] == 'deletepackage') {
        $("#packageest_"+arr[1]).val('0');
        countPackage();
    }
    if(arr[0] == 'deletepromotion') {
        $("#promotionest_"+arr[1]).val('0');
        countPromotion();
    }
    if(arr[0] == 'deletecustom') {
        $("#customest_"+arr[1]).val('0');
        countCustom();
    }

    if(arr[0] == 'deletematerial') {
        $("#mprojectprice_"+arr[1]).val('0');
        $("#mprojectqty_"+arr[1]).val('0');
        $("#mprojecttotal_"+arr[1]).val('0');
        countMaterial('delete');
    }

    if(arr[0] == 'deleteservices') {
        $("#sprojectprice_"+arr[1]).val('0');
        $("#sprojectqty_"+arr[1]).val('0');
        $("#sprojecttotal_"+arr[1]).val('0');
        countService('delete');
    }
    if(arr[0] == 'deleteproducts') {
        $("#pprojectprice_"+arr[1]).val('0');
        $("#pprojectqty_"+arr[1]).val('0');
        $("#pprojecttotal_"+arr[1]).val('0');
        countService('delete');
    }
    if(arr[0] == 'deletesred') {
        $("#sredprojectprice_"+arr[1]).val('0');
        $("#sredprojectqty_"+arr[1]).val('0');
        $("#sredprojecttotal_"+arr[1]).val('0');
        countSrEd('delete');
    }
    if(arr[0] == 'deletestaff') {
        $("#stprojectprice_"+arr[1]).val('0');
        $("#stprojectqty_"+arr[1]).val('0');
        $("#stprojecttotal_"+arr[1]).val('0');
        countStaff('delete');
    }
    if(arr[0] == 'deletecontractor') {
        $("#cntprojectprice_"+arr[1]).val('0');
        $("#cntprojectqty_"+arr[1]).val('0');
        $("#cntprojecttotal_"+arr[1]).val('0');
        countContractor('delete');
    }

    if(arr[0] == 'deleteclients') {
        $("#clientest_"+arr[1]).val('0');
        countClient();
    }

    if(arr[0] == 'deletevendor') {
        $("#vprojectprice_"+arr[1]).val('0');
        $("#vprojectqty_"+arr[1]).val('0');
        $("#vprojecttotal_"+arr[1]).val('0');
        countVendor('delete');
    }
    if(arr[0] == 'deletecustomer') {
        $("#customerest_"+arr[1]).val('0');
        countCustomer();
    }
    if(arr[0] == 'deleteinventory') {
        $("#inprojectprice_"+arr[1]).val('0');
        $("#inprojectqty_"+arr[1]).val('0');
        $("#inprojecttotal_"+arr[1]).val('0');
        countInventory('delete');
    }
    if(arr[0] == 'deleteequipment') {
        $("#eqprojectprice_"+arr[1]).val('0');
        $("#eqprojectqty_"+arr[1]).val('0');
        $("#eqprojecttotal_"+arr[1]).val('0');
        countEquipment('delete');
    }
    if(arr[0] == 'deletelabour') {
        $("#lprojectprice_"+arr[1]).val('0');
        $("#lprojectqty_"+arr[1]).val('0');
        $("#lprojecttotal_"+arr[1]).val('0');
        countLabour('delete');
    }

    if(projectid == 0) {
        if(arr[0] == 'deletepackage') {
            alert('If you Delete any Package then all data Related to this Package will gone.');
            var packageval = $("#"+blank+arr[1]).val();
            var param = getParameterByName('pid');

            var package_id = param.replace(packageval+",", "");
            var package_id = package_id.replace(",,", ",");

            var promotion_id='';
            $('.promotion_head').each(function () {
                promotion_id += $(this).val()+',';
            });

            var custom_id='';
            $('.custom_head').each(function () {
                custom_id += $(this).val()+',';
            });
            window.location = 'add_project.php?projectid='+projectid+'&pid='+package_id+'&promoid='+promotion_id+'&cid='+custom_id;
        }

        if(arr[0] == 'deletepromotion') {
            alert('If you Delete any Promotion then all data Related to this Promotion will gone.');
            var promoval = $("#"+blank+arr[1]).val();
            var param = getParameterByName('promoid');

            var promotion_id = param.replace(promoval+",", "");
            var promotion_id = promotion_id.replace(",,", ",");

            var package_id='';
            $('.package_head').each(function () {
                package_id += $(this).val()+',';
            });

            var custom_id='';
            $('.custom_head').each(function () {
                custom_id += $(this).val()+',';
            });
            window.location = 'add_project.php?projectid='+projectid+'&pid='+package_id+'&promoid='+promotion_id+'&cid='+custom_id;
        }

        if(arr[0] == 'deletecustom') {
            alert('If you Delete any Custom then all data Related to this Custom will gone.');
            var cusval = $("#"+blank+arr[1]).val();
            var param = getParameterByName('cid');

            var custom_id = param.replace(cusval+",", "");
            var custom_id = custom_id.replace(",,", ",");

            var package_id='';
            $('.package_head').each(function () {
                package_id += $(this).val()+',';
            });

            var promotion_id='';
            $('.promotion_head').each(function () {
                promotion_id += $(this).val()+',';
            });

            window.location = 'add_project.php?projectid='+projectid+'&pid='+package_id+'&promoid='+promotion_id+'&cid='+custom_id;
        }
    }

    $("#"+hide+arr[1]).hide();
    $("#"+blank+arr[1]).val('');


    return false;
}
</script>

<?php if (strpos($value_config, ','."Business".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Business<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <select name="businessid" id="businessid" data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Business' AND deleted=0 ORDER BY category");
            while($row = mysqli_fetch_array($query)) {
                if ($businessid== $row['contactid']) {
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
<?php } ?>

<?php if (strpos($value_config, ','."Contact".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Contact<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <select name="business_contactid" id="contactid" data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $cat = '';
            $query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE businessid='$businessid' order by category");
            while($row = mysqli_fetch_array($query)) {
                if ($contactid== $row['contactid']) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                if($cat != $row['category']) {
                    echo '<optgroup label="'.$row['category'].'">';
                    $cat = $row['category'];
                }
                echo "<option ".$selected." value='". $row['contactid']."'>".$row['name'],' '.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Rate Card".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Rate Card:</label>
    <div class="col-sm-8">
        <select name="ratecardid" id="ratecardid" data-placeholder="Choose a Rate Card..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $query = mysqli_query($dbc,"SELECT ratecardid, rate_card_name FROM rate_card WHERE on_off=1 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') order by rate_card_name");
            while($row = mysqli_fetch_array($query)) {
                if ($ratecardid == $row['ratecardid']) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='". $row['ratecardid']."'>".$row['rate_card_name'].'</option>';
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Short Name".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Short Name:</label>
    <div class="col-sm-8">
        <input name="short_name" value="<?php echo $short_name; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Piece Work".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Piece Work:</label>
    <div class="col-sm-8">
        <input name="piece_work" value="<?php echo $piece_work; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Add to Helpdesk".',') !== FALSE) { ?>
<div class="form-group">
    <label for="site_name" class="col-sm-4 control-label">Add to Helpdesk</label>
    <div class="col-sm-8">
        <input type="checkbox"  <?php if($add_to_helpdesk == '1') { echo 'checked'; } ?> value="1" name="add_to_helpdesk">
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Heading".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Heading:</label>
    <div class="col-sm-8">
        <input name="heading" value="<?php echo $heading; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Location".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Location:</label>
    <div class="col-sm-8">
        <input name="location" value="<?php echo $location; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Job number".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Job number:</label>
    <div class="col-sm-8">
        <input name="job_number" value="<?php echo $job_number; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."AFE number".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">AFE number:</label>
    <div class="col-sm-8">
        <input name="afe_number" value="<?php echo $afe_number; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Created Date".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Created Date:</label>
    <div class="col-sm-8">
        <input name="created_date" value="<?php echo $created_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Start Date".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Start Date:</label>
    <div class="col-sm-8">
        <input name="start_date" value="<?php echo $start_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Estimated Completion Date".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Estimated Completion Date:</label>
    <div class="col-sm-8">
        <input name="estimated_completion_date" value="<?php echo $estimated_completion_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Work performed".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Work performed:</label>
    <div class="col-sm-8">
        <input name="work_performed_date" value="<?php echo $work_performed_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Issue".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Issue:</label>
    <div class="col-sm-8">
        <textarea name="detail_issue" rows="5" cols="50" class="form-control"><?php echo $detail_issue; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Problem".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Problem:</label>
    <div class="col-sm-8">
        <textarea name="detail_problem" rows="5" cols="50" class="form-control"><?php echo $detail_problem; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."GAP".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">GAP:</label>
    <div class="col-sm-8">
        <textarea name="detail_gap" rows="5" cols="50" class="form-control"><?php echo $detail_gap; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Technical Uncertainty".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Technical Uncertainty:</label>
    <div class="col-sm-8">
        <textarea name="detail_technical_uncertainty" rows="5" cols="50" class="form-control"><?php echo $detail_technical_uncertainty; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Base Knowledge".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Base Knowledge:</label>
    <div class="col-sm-8">
        <textarea name="detail_base_knowledge" rows="5" cols="50" class="form-control"><?php echo $detail_base_knowledge; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Do".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Do:</label>
    <div class="col-sm-8">
        <textarea name="detail_do" rows="5" cols="50" class="form-control"><?php echo $detail_do; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Already Known".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Already Known:</label>
    <div class="col-sm-8">
        <textarea name="detail_already_known" rows="5" cols="50" class="form-control"><?php echo $detail_already_known; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Sources".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Sources:</label>
    <div class="col-sm-8">
        <textarea name="detail_sources" rows="5" cols="50" class="form-control"><?php echo $detail_sources; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Current Designs".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Current Designs:</label>
    <div class="col-sm-8">
        <textarea name="detail_current_designs" rows="5" cols="50" class="form-control"><?php echo $detail_current_designs; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Known Techniques".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Known Techniques:</label>
    <div class="col-sm-8">
        <textarea name="detail_known_techniques" rows="5" cols="50" class="form-control"><?php echo $detail_known_techniques; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Review Needed".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Review Needed:</label>
    <div class="col-sm-8">
        <textarea name="detail_review_needed" rows="5" cols="50" class="form-control"><?php echo $detail_review_needed; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Looking to Achieve".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Looking to Achieve:</label>
    <div class="col-sm-8">
        <textarea name="detail_looking_to_achieve" rows="5" cols="50" class="form-control"><?php echo $detail_looking_to_achieve; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Plan".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Plan:</label>
    <div class="col-sm-8">
        <textarea name="detail_plan" rows="5" cols="50" class="form-control"><?php echo $detail_plan; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Next Steps".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Next Steps:</label>
    <div class="col-sm-8">
        <textarea name="detail_next_steps" rows="5" cols="50" class="form-control"><?php echo $detail_next_steps; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Learnt".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Learned:</label>
    <div class="col-sm-8">
        <textarea name="detail_learnt" rows="5" cols="50" class="form-control"><?php echo $detail_learnt; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Discovered".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Discovered:</label>
    <div class="col-sm-8">
        <textarea name="detail_discovered" rows="5" cols="50" class="form-control"><?php echo $detail_discovered; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Tech Advancements".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Tech Advancements:</label>
    <div class="col-sm-8">
        <textarea name="detail_tech_advancements" rows="5" cols="50" class="form-control"><?php echo $detail_tech_advancements; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Work".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Work:</label>
    <div class="col-sm-8">
        <textarea name="detail_work" rows="5" cols="50" class="form-control"><?php echo $detail_work; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Adjustments Needed".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Adjustments Needed:</label>
    <div class="col-sm-8">
        <textarea name="detail_adjustments_needed" rows="5" cols="50" class="form-control"><?php echo $detail_adjustments_needed; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Future Designs".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Future Designs:</label>
    <div class="col-sm-8">
        <textarea name="detail_future_designs" rows="5" cols="50" class="form-control"><?php echo $detail_future_designs; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Objective".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Objective:</label>
    <div class="col-sm-8">
        <textarea name="detail_objective" rows="5" cols="50" class="form-control"><?php echo $detail_objective; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Targets".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Targets:</label>
    <div class="col-sm-8">
        <textarea name="detail_targets" rows="5" cols="50" class="form-control"><?php echo $detail_targets; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Audience".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Audience:</label>
    <div class="col-sm-8">
        <textarea name="detail_audience" rows="5" cols="50" class="form-control"><?php echo $detail_audience; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Strategy".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Strategy:</label>
    <div class="col-sm-8">
        <textarea name="detail_strategy" rows="5" cols="50" class="form-control"><?php echo $detail_strategy; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Desired Outcome".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Desired Outcome:</label>
    <div class="col-sm-8">
        <textarea name="detail_desired_outcome" rows="5" cols="50" class="form-control"><?php echo $detail_desired_outcome; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Actual Outcome".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Actual Outcome:</label>
    <div class="col-sm-8">
        <textarea name="detail_actual_outcome" rows="5" cols="50" class="form-control"><?php echo $detail_actual_outcome; ?></textarea>
    </div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Check".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Check:</label>
    <div class="col-sm-8">
        <textarea name="detail_check" rows="5" cols="50" class="form-control"><?php echo $detail_check; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Doing Start and End Date".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Doing Start Date:</label>
    <div class="col-sm-8">
        <input name="doing_start_date" value="<?php echo $doing_start_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>

<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Doing End Date:</label>
    <div class="col-sm-8">
        <input name="doing_end_date" value="<?php echo $doing_end_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Internal QA Date".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Internal QA Date:</label>
    <div class="col-sm-8">
        <input name="internal_qa_date" value="<?php echo $internal_qa_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Client QA/Deliverable Date".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Client QA/Deliverable Date:</label>
    <div class="col-sm-8">
        <input name="client_qa_date" value="<?php echo $client_qa_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."TO DO Date".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">TO DO Date:</label>
    <div class="col-sm-8">
        <input name="to_do_date" value="<?php echo $to_do_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Deliverable Date".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Deliverable Date:</label>
    <div class="col-sm-8">
        <input name="deliverable_date" value="<?php echo $deliverable_date; ?>" type="text" class="datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Estimated Time to Complete Work".',') !== FALSE) { ?>
<div class="form-group">
    <label for="site_name" class="col-sm-4 control-label">Estimated Time to Complete Work:</label>
    <div class="col-sm-8">
        <select style="width: 100px;" data-placeholder="Choose a Type..." name="estimated_time_to_complete_work_hour" class="chosen-select-deselect1 form-control" >
        <?php
        for($i=0;$i<200;$i++) {
            if($estimated_time_to_complete_work[0] == $i) {
                $selected = ' selected';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." value='". $i."'>".$i.'</option>';
        }
        ?>
        </select>Hour
        <select style="width: 100px;" data-placeholder="Choose a Type..." name="estimated_time_to_complete_work_minute" class="chosen-select-deselect1 form-control" >
        <?php
        for($i=00;$i<60;$i++) {
            if($i<10) {
                $i = '0'.$i;
            }
            if($estimated_time_to_complete_work[1] == $i) {
                $selected = ' selected';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." value='". $i."'>".$i.'</option>';
        }
        ?>
        </select>Minute
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Small Timer".',') !== FALSE) {
include ('add_ticket_timer.php');
} ?>

<?php if (strpos($value_config, ','."Big Box Timer".',') !== FALSE) {
include ('add_workorder_timer.php');
} ?>

<?php if (strpos($value_config, ','."Path".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Project Path:</label>
    <div class="col-sm-8">
        <select name="project_path" id="project_path" data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $query = mysqli_query($dbc,"SELECT project_path_milestone, project_path FROM project_path_milestone order by project_path");
            while($row = mysqli_fetch_array($query)) {
                if ($project_path== $row['project_path_milestone']) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='". $row['project_path_milestone']."'>".$row['project_path'].'</option>';
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Milestone & Timeline".',') !== FALSE) { ?>
<div class="form-group">
  <label for="site_name" class="col-sm-4 control-label">Milestone & Timeline:</label>
  <div class="col-sm-8">
    <select data-placeholder="Choose an Option..." name="milestone_timeline" id="milestone_timeline"  class="chosen-select-deselect form-control" width="580">
        <option value=""></option>
        <?php
        $each_tab = explode('#*#', get_project_path_milestone($dbc, $project_path, 'milestone'));
        $timeline = explode('#*#', get_project_path_milestone($dbc, $project_path, 'timeline'));
        $j=0;
        foreach ($each_tab as $cat_tab) {
            if ($milestone_timeline == $cat_tab) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.' : '.$timeline[$j].'</option>';
            $j++;
        }
      ?>
    </select>
  </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Service Type".',') !== FALSE) { ?>
		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Service Type<span class="text-red">*</span>:</label>
		  <div class="col-sm-8">
			<select data-placeholder="Choose a Type..." id="service_type" name="service_type" class="chosen-select-deselect form-control" width="380">
			  <option value=""></option>
			  <?php
				$query = mysqli_query($dbc,"SELECT distinct(service_type) FROM services WHERE deleted=0 order by service_type");
				while($row = mysqli_fetch_array($query)) {
                    if ($service_type== $row['service_type']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
					echo "<option ".$selected." value='". $row['service_type']."'>".$row['service_type'].'</option>';
				}
			  ?>
			</select>
		  </div>
		</div>
<?php } ?>

<?php if (strpos($value_config, ','."Service Category".',') !== FALSE) { ?>
    <div class="form-group">
      <label for="site_name" class="col-sm-4 control-label">Service Category<span class="text-red">*</span>:</label>
      <div class="col-sm-8">
        <select data-placeholder="Choose a Category..." name="service_category" id="service_category"  class="chosen-select-deselect form-control" width="580">
          <option value=""></option>
          <?php
            $query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE deleted=0 order category");
            while($row = mysqli_fetch_array($query)) {
                if ($service_category== $row['category']) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='". $row['category']."'>".$row['category'].'</option>';
            }
          ?>
        </select>
      </div>
    </div>
<?php } ?>

<?php if (strpos($value_config, ','."Service Heading".',') !== FALSE) { ?>
    <div class="form-group">
      <label for="site_name" class="col-sm-4 control-label">Service Heading<span class="text-red">*</span>:</label>
      <div class="col-sm-8">
        <select data-placeholder="Choose a Heading..." name="service_heading" id="service_heading"  class="chosen-select-deselect form-control" width="580">
          <option value=""></option>
          <?php
            $query = mysqli_query($dbc,"SELECT serviceid, heading FROM services WHERE deleted=0");
            while($row = mysqli_fetch_array($query)) {
                if ($service_heading== $row['serviceid']) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='". $row['serviceid']."'>".$row['heading'].'</option>';
            }
          ?>
        </select>
      </div>
    </div>
<?php } ?>

<?php if (strpos($value_config, ','."Support Documents".',') !== FALSE) {
    ?>
    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
        </label>
        <div class="col-sm-8">
            <?php
            $doc1 = mysqli_query($dbc, "SELECT * FROM project_manage_document_link WHERE projectmanageid='$projectmanageid' AND type='Support Document' ORDER BY doclinkid DESC");
            while($row_doc1 = mysqli_fetch_array($doc1)) {
                echo '-<a href="download/'.$row_doc1['document'].'" target="_blank">'.$row_doc1['document'].'</a><br>';
            }
            ?>
            <div class="enter_cost additional_doc clearfix">
                <div class="clearfix"></div>

                <div class="form-group clearfix">
                    <div class="col-sm-5">
                        <input name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                    </div>
                </div>

            </div>

            <div id="add_here_new_doc"></div>

            <div class="form-group triple-gapped clearfix">
                <div class="col-sm-offset-4 col-sm-8">
                    <button id="add_row_doc" class="btn brand-btn pull-left">Add Another Document</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (strpos($value_config, ','."Support Links".',') !== FALSE) { ?>
    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">Link(s):<br><em>(e.g. - https://www.google.com)</em>
        </label>
        <div class="col-sm-8">
            <?php
            $doc2 = mysqli_query($dbc, "SELECT * FROM project_manage_document_link WHERE projectmanageid='$projectmanageid' AND type='Support Link' ORDER BY doclinkid DESC");
            while($row_doc2 = mysqli_fetch_array($doc2)) {
                echo '-<a target="_blank" href=\''.$row_doc2['link'].'\'">Link</a><br>';
            }
            ?>
            <div class="enter_cost additional_link clearfix">
                <div class="clearfix"></div>

                <div class="form-group clearfix">
                    <div class="col-sm-5">
                        <input name="support_link[]" type="text" class="form-control">
                    </div>
                </div>

            </div>

            <div id="add_here_new_link"></div>

            <div class="form-group triple-gapped clearfix">
                <div class="col-sm-offset-4 col-sm-8">
                    <button id="add_row_link" class="btn brand-btn pull-left">Add Another Link</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (strpos($value_config, ','."Review Documents".',') !== FALSE) { ?>
    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
        </label>
        <div class="col-sm-8">
            <?php
                $doc3 = mysqli_query($dbc, "SELECT * FROM project_manage_document_link WHERE projectmanageid='$projectmanageid' AND type='Review Document' ORDER BY doclinkid DESC");
                while($row_doc3 = mysqli_fetch_array($doc3)) {
                    echo '-<a href="download/'.$row_doc3['document'].'" target="_blank">'.$row_doc3['document'].'</a><br>';
                }
            ?>
            <div class="enter_cost additional_doc_review clearfix">
                <div class="clearfix"></div>

                <div class="form-group clearfix">
                    <div class="col-sm-5">
                        <input name="review_upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                    </div>
                </div>

            </div>

            <div id="add_here_new_doc_review"></div>

            <div class="form-group triple-gapped clearfix">
                <div class="col-sm-offset-4 col-sm-8">
                    <button id="add_row_doc_review" class="btn brand-btn pull-left">Add Another Document</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (strpos($value_config, ','."Review Links".',') !== FALSE) { ?>
    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">Link(s):<br><em>(e.g. - https://www.google.com)</em>
        </label>
        <div class="col-sm-8">
            <?php
            $doc4 = mysqli_query($dbc, "SELECT * FROM project_manage_document_link WHERE projectmanageid='$projectmanageid' AND type='Review Link' ORDER BY doclinkid DESC");
            while($row_doc4 = mysqli_fetch_array($doc4)) {
                echo '-<a target="_blank" href=\''.$row_doc4['link'].'\'">Link</a><br>';
            }
            ?>
            <div class="enter_cost additional_review_link clearfix">
                <div class="clearfix"></div>

                <div class="form-group clearfix">
                    <div class="col-sm-5">
                        <input name="support_review_link[]" type="text" class="form-control">
                    </div>
                </div>

            </div>

            <div id="add_here_new_review_link"></div>

            <div class="form-group triple-gapped clearfix">
                <div class="col-sm-offset-4 col-sm-8">
                    <button id="add_row_review_link" class="btn brand-btn pull-left">Add Another Link</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (strpos($value_config, ','."Description".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Description:</label>
    <div class="col-sm-8">
        <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Notes".',') !== FALSE) { ?>
<div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Notes:</label>
    <div class="col-sm-8">
        <textarea name="notes" rows="5" cols="50" class="form-control"><?php echo $notes; ?></textarea>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Status".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label">Status:</label>
    <div class="col-sm-8">
        <input name="status" type="text" value="<?php echo $status; ?>" class="form-control">

        <!--<select data-placeholder="Choose a Heading..." name="heading" class="chosen-select-deselect form-control" width="380">
          <option value=""></option>
          <?php
            $tabs = get_config($dbc, 'ticket_heading');
            $each_tab = explode(',', $tabs);
            foreach ($each_tab as $cat_tab) {
                if ($heading == $cat_tab) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
            }
          ?>
        </select>
        -->
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Staff(Assign To)".',') !== FALSE) {
?>
<div class="form-group">
  <label for="site_name" class="col-sm-4 control-label">Staff(Assign To):</label>
  <div class="col-sm-8">
    <select data-placeholder="Choose a Staff Member..." multiple id="assign_to" name="assign_to[]" class="chosen-select-deselect form-control" width="380">
      <?php
        $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."");
        echo "<option value='Assign to All'>Assign to All</option>";
        while($row = mysqli_fetch_array($query)) { ?>
            <option <?php if (strpos(','.$assign_to.',', ','.$row['contactid'].',') !== false) { echo  'selected="selected"'; } ?> value='<?php echo $row['contactid'];?>'><?php echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']);?></option>
        <?php }
      ?>
    </select>
  </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Doing Assign To".',') !== FALSE) { ?>
<div class="form-group">
  <label for="site_name" class="col-sm-4 control-label">Doing Assign To:</label>
  <div class="col-sm-8">
    <select data-placeholder="Choose a Staff Member..." name="doing_assign_to" class="chosen-select-deselect form-control" width="380">
      <option value=""></option>
      <?php
        $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."");
        //echo "<option value='Assign to All'>Assign to All</option>";
        while($row = mysqli_fetch_array($query)) {
            if ($doing_assign_to== $row['contactid']) {
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

<?php if (strpos($value_config, ','."Internal QA Assign To".',') !== FALSE) { ?>
<div class="form-group">
  <label for="site_name" class="col-sm-4 control-label">Internal QA Assign To:</label>
  <div class="col-sm-8">
    <select data-placeholder="Choose a Staff Member..." name="internal_qa_assign_to" class="chosen-select-deselect form-control" width="380">
      <option value=""></option>
      <?php
        $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."");
        //echo "<option value='Assign to All'>Assign to All</option>";
        while($row = mysqli_fetch_array($query)) {
            if ($internal_qa_assign_to== $row['contactid']) {
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

<?php if (strpos($value_config, ','."Client QA/Deliverable Assign To".',') !== FALSE) { ?>
<div class="form-group">
  <label for="site_name" class="col-sm-4 control-label">Client QA/Deliverable Assign To:</label>
  <div class="col-sm-8">
    <select data-placeholder="Choose a Staff Member..." name="client_qa_assign_to" class="chosen-select-deselect form-control" width="380">
      <option value=""></option>
	  <?php
			$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
			foreach($query as $id) {
				$selected = '';
				$selected = $id == $client_qa_assign_to ? 'selected = "selected"' : '';
				echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
			}
		  ?>
    </select>
  </div>
</div>
<?php } ?>

<?php
    include ('add_project_budget.php');
?>

<?php if (strpos($value_config, ','."Package".',') !== FALSE) { ?>
    <h3>Package</h3>
    <?php
    include ('add_project_package.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Promotion".',') !== FALSE) { ?>
    <h3>promotion</h3>
    <?php
    include ('add_project_promotion.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Custom".',') !== FALSE) { ?>
    <h3>custom</h3>
    <?php
    include ('add_project_custom.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Material".',') !== FALSE) { ?>
    <h3>material</h3>
    <?php
    include ('add_project_material.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Services".',') !== FALSE) { ?>
    <h3>services</h3>
    <?php
    include ('add_project_services.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Products".',') !== FALSE) { ?>
    <h3>products</h3>
    <?php
    include ('add_project_products.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
    <h3>staff</h3>
    <?php
    include ('add_project_staff.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Contractor".',') !== FALSE) { ?>
    <h3>contractor</h3>
    <?php
    include ('add_project_contractor.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Clients".',') !== FALSE) { ?>
    <h3>clients</h3>
    <?php
    include ('add_project_clients.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Vendor Pricelist".',') !== FALSE) { ?>
    <h3>vendor</h3>
    <?php
    include ('add_project_vendor.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Customer".',') !== FALSE) { ?>
    <h3>customer</h3>
    <?php
    include ('add_project_customer.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Inventory".',') !== FALSE) { ?>
    <h3>inventory</h3>
    <?php
    include ('add_project_inventory.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { ?>
    <h3>equipment</h3>
    <?php
    include ('add_project_equipment.php');
    ?>
<?php } ?>

<?php if (strpos($value_config, ','."Labour".',') !== FALSE) { ?>
    <h3>labour</h3>
    <?php
    include ('add_project_labour.php');
    ?>
<?php } ?>
