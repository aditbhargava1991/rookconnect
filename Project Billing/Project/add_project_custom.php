<script>
$(document).ready(function() {
	//Custom
    var add_new_custom = 1;
    $('#deletecustom_0').hide();
    $('#add_row_custom').on( 'click', function () {
        $('#deletecustom_0').show();
        var clone = $('.additional_custom').clone();
        clone.find('.form-control').val('');

        clone.find('#customservice_0').attr('id', 'customservice_'+add_new_custom);
		clone.find('#customcategory_0').attr('id', 'customcategory_'+add_new_custom);
        clone.find('#customheading_0').attr('id', 'customheading_'+add_new_custom);
        clone.find('#customfrp_0').attr('id', 'customfrp_'+add_new_custom);
        clone.find('#customap_0').attr('id', 'customap_'+add_new_custom);
        clone.find('#customwp_0').attr('id', 'customwp_'+add_new_custom);
        clone.find('#customcomp_0').attr('id', 'customcomp_'+add_new_custom);
        clone.find('#customcp_0').attr('id', 'customcp_'+add_new_custom);
        clone.find('#custommsrp_0').attr('id', 'custommsrp_'+add_new_custom);
		clone.find('#customfinalprice_0').attr('id', 'customfinalprice_'+add_new_custom);
        clone.find('#customview_0').attr('id', 'customview_'+add_new_custom);
        clone.find('#customest_0').attr('id', 'customest_'+add_new_custom);

        clone.find('#custom_0').attr('id', 'custom_'+add_new_custom);
        clone.find('#deletecustom_0').attr('id', 'deletecustom_'+add_new_custom);
        $('#deletecustom_0').hide();

        clone.removeClass("additional_custom");
        $('#add_here_new_custom').append(clone);

        resetChosen($("#customservice_"+add_new_custom));
        resetChosen($("#customcategory_"+add_new_custom));
        resetChosen($("#customheading_"+add_new_custom));

        add_new_custom++;

        return false;
    });
});
//Custom
function selectCustomService(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=custom_service_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            alert('If you choose any custom then all data you inserted will gone.');
            $("#customcategory_"+arr[1]).html(response);
			$("#customcategory_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectCustomCat(sel) {
	var stage = encodeURIComponent(sel.value);
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=custom_cat_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#customheading_"+arr[1]).html(response);
			$("#customheading_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectCustomHeading(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=custom_head_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*');
            $("#customfrp_"+arr[1]).val(result[0]);
            $("#customap_"+arr[1]).val(result[1]);
            $("#customwp_"+arr[1]).val(result[2]);
            $("#customcomp_"+arr[1]).val(result[3]);
            $("#customcp_"+arr[1]).val(result[4]);
            $("#custommsrp_"+arr[1]).val(result[5]);
			$("#customfinalprice_"+arr[1]).val(result[6]);

            var projectid = $("#projectid").val();

            var package_id='';
            $('.package_head').each(function () {
                package_id += $(this).val()+',';
            });

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
	});
}
function countCustom() {
    var sum_fee = 0;
    $('[name="customprojectprice[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="custom_total"]').val(round2Fixed(sum_fee));
    $('[name="custom_summary"]').val(round2Fixed(sum_fee));

    var custom_budget = $('[name="custom_budget"]').val();
    if(custom_budget >= sum_fee) {
        $('[name="custom_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="custom_total"]').css("background-color", "#ff9999"); // Green
    }
}
</script>
<?php
$get_field_config_custom = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT custom FROM field_config"));
$field_config_custom = ','.$get_field_config_custom['custom'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <?php if (strpos($base_field_config, ','."Custom Service Type".',') !== FALSE) { ?>
            <label class="col-sm-2 text-center">Service Type</label>
            <?php } ?>
            <?php if (strpos($base_field_config, ','."Custom Category".',') !== FALSE) { ?>
            <label class="col-sm-2 text-center">Category</label>
            <?php } ?>
            <label class="col-sm-2 text-center">Heading</label>
            <?php if (strpos($field_config_custom, ','."Final Retail Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Final Retail Price</label>
            <?php } ?>
            <?php if (strpos($field_config_custom, ','."Admin Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Admin Price</label>
            <?php } ?>
            <?php if (strpos($field_config_custom, ','."Wholesale Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Wholesale Price</label>
            <?php } ?>
            <?php if (strpos($field_config_custom, ','."Commercial Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commercial Price</label>
            <?php } ?>
            <?php if (strpos($field_config_custom, ','."Client Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Client Price</label>
            <?php } ?>
            <?php if (strpos($field_config_custom, ','."MSRP".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">MSRP</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</label>
        </div>

        <?php if((!empty($_GET['cid'])) || (!empty($_GET['projectid']))) {
            $cid = '';
            $cval = '';
            if(!empty($_GET['cid'])) {
                $cid = $_GET['cid'];
            }

            if(!empty($_GET['projectid'])) {
                $custom = $get_contact['custom'];
                $each_cid = explode('**',$custom);
                $cid .= ',';
                foreach($each_cid as $id_all) {
                    if($id_all != '') {
                        $cid_all = explode('#',$id_all);
                        $cid .= $cid_all[0].',';
                        $cval .= $cid_all[1].',';
                    }
                }
            }

            $each_cid = explode(',',trim($cid,","));
            $each_cval = explode(',',trim($cval,","));
            $total_count = mb_substr_count($cid,',');

            $id_loop = 500;

            $final_total_custom = 0;
            for($cid_loop=0; $cid_loop<$total_count; $cid_loop++) {

                $customid = '';
                $cestval = 0;
                if(isset($each_cid[$cid_loop])) {
                    $customid = $each_cid[$cid_loop];
                }

                if(isset($each_cval[$cid_loop])) {
                    $cestval = $each_cval[$cid_loop];
                }
                $final_total_custom += $cestval;

                if($customid != '') {

                    $custom = explode('**', $get_rc['custom']);
                    $rc_price = 0;
                    foreach($custom as $pp){
                        if (strpos('#'.$pp, '#'.$customid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>
                <div class="form-group clearfix" id="<?php echo 'custom_'.$id_loop; ?>" >
                <?php if (strpos($base_field_config, ','."Custom Service Type".',') !== FALSE) { ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Service Type:</label>
                    <select onChange='selectCustomService(this)' data-placeholder="Choose a Type..." id="<?php echo 'customservice_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(service_type) FROM custom WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_custom($dbc, $customid, 'service_type') == $row['service_type']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['service_type']."'>".$row['service_type'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <?php if (strpos($base_field_config, ','."Custom Category".',') !== FALSE) { ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectCustomCat(this)' data-placeholder="Choose a Category..." id="<?php echo 'customcategory_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM custom WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_custom($dbc, $customid, 'category') == $row['category']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['category']."'>".$row['category'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select onChange='selectCustomHeading(this)' data-placeholder="Choose a Heading..." id="<?php echo 'customheading_'.$id_loop; ?>" name="customid[]" class="chosen-select-deselect form-control custom_head" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT customid, heading FROM custom WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_custom($dbc, $customid, 'heading') == $row['heading']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['customid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>
                </div>

                <?php if (strpos($field_config_custom, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="customfrp[]" value="<?php echo get_custom($dbc, $customid, 'final_retail_price');?>"  id="<?php echo 'customfrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_custom, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="customap[]" value="<?php echo get_custom($dbc, $customid, 'admin_price');?>"  id="<?php echo 'customap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_custom, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="customwp[]" value="<?php echo get_custom($dbc, $customid, 'wholesale_price');?>" id="<?php echo 'customwp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_custom, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="customcomp[]" value="<?php echo get_custom($dbc, $customid, 'commercial_price');?>" id="<?php echo 'customcomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_custom, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="customcp[]" value="<?php echo get_custom($dbc, $customid, 'client_price');?>" id="<?php echo 'customcp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_custom, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="custommsrp[]" value="<?php echo get_custom($dbc, $customid, 'msrp');?>" id="<?php echo 'custommsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="customfinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'customfinalprice_'.$id_loop; ?>"  type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="customprojectprice[]" value="<?php echo $cestval; ?>"  onchange="countCustom()" id="<?php echo 'customest_'.$id_loop; ?>"  type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'custom_','customheading_'); return false;" id="<?php echo 'deletecustom_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
        <?php  $id_loop++;
                }
            }
        } ?>

        <div class="additional_custom clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="custom_0">

                <?php if (strpos($base_field_config, ','."Custom Service Type".',') !== FALSE) { ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Service Type:</label>
                    <select onChange='selectCustomService(this)' data-placeholder="Choose a Type..." id="customservice_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(service_type) FROM custom WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['service_type']."'>".$row['service_type'].'</option>';

                        }
                        ?>
                    </select>
                </div>
                <?php } ?>
                <?php if (strpos($base_field_config, ','."Custom Category".',') !== FALSE) { ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectCustomCat(this)' data-placeholder="Choose a Custom..." id="customcategory_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT customid, category FROM custom WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select onChange='selectCustomHeading(this)' data-placeholder="Choose a Heading..." id="customheading_0" name="customid[]" class="chosen-select-deselect form-control custom_head" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT customid, heading FROM custom WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['customid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>
                </div>

                <?php if (strpos($field_config_custom, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="customfrp[]" readonly id="customfrp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_custom, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="customap[]" readonly id="customap_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_custom, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="customwp[]" readonly id="customwp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_custom, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="customcomp[]" id="customcomp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_custom, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="customcp[]" readonly id="customcp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_custom, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="custommsrp[]" readonly id="custommsrp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="customfinalprice[]" readonly id="customfinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="customprojectprice[]" id="customest_0" onchange="countCustom()" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'custom_','customheading_'); return false;" id="deletecustom_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_custom"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_custom" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="custom_budget" value="<?php echo $budget_price[2]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="custom_total" value="<?php echo $final_total_custom;?>" type="text" class="form-control">
    </div>
</div>
