
<script>
$(document).ready(function() {
	//Services
    var add_new_s = 1;
    $('#deleteservices_0').hide();
    $('#add_row_s').on( 'click', function () {
        $('#deleteservices_0').show();
        var clone = $('.additional_s').clone();
        clone.find('.form-control').val('');

        clone.find('#sservice_0').attr('id', 'sservice_'+add_new_s);
		clone.find('#scategory_0').attr('id', 'scategory_'+add_new_s);
        clone.find('#sheading_0').attr('id', 'sheading_'+add_new_s);
        clone.find('#sfrp_0').attr('id', 'sfrp_'+add_new_s);
        clone.find('#sap_0').attr('id', 'sap_'+add_new_s);
        clone.find('#swp_0').attr('id', 'swp_'+add_new_s);
        clone.find('#scomp_0').attr('id', 'scomp_'+add_new_s);
        clone.find('#scp_0').attr('id', 'scp_'+add_new_s);
        clone.find('#smsrp_0').attr('id', 'smsrp_'+add_new_s);
        clone.find('#smb_0').attr('id', 'smb_'+add_new_s);
        clone.find('#seh_0').attr('id', 'seh_'+add_new_s);
        clone.find('#sah_0').attr('id', 'sah_'+add_new_s);

		clone.find('#sfinalprice_0').attr('id', 'sfinalprice_'+add_new_s);
		clone.find('#sprojectprice_0').attr('id', 'sprojectprice_'+add_new_s);
		clone.find('#sprojectqty_0').attr('id', 'sprojectqty_'+add_new_s);
		clone.find('#sprojecttotal_0').attr('id', 'sprojecttotal_'+add_new_s);

        clone.find('#services_0').attr('id', 'services_'+add_new_s);
        clone.find('#deleteservices_0').attr('id', 'deleteservices_'+add_new_s);
        $('#deleteservices_0').hide();

        clone.removeClass("additional_s");
        $('#add_here_new_s').append(clone);

        resetChosen($("#sservice_"+add_new_s));
        resetChosen($("#scategory_"+add_new_s));
        resetChosen($("#sheading_"+add_new_s));

        add_new_s++;

        return false;
    });
});
//Services
function selectServiceService(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=s_service_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#scategory_"+arr[1]).html(response);
			$("#scategory_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectServiceCat(sel) {
	var stage = encodeURIComponent(sel.value);
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=s_cat_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#sheading_"+arr[1]).html(response);
			$("#sheading_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectServiceHeading(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=s_head_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*');
            $("#sfrp_"+arr[1]).val(result[0]);
            $("#sap_"+arr[1]).val(result[1]);
            $("#swp_"+arr[1]).val(result[2]);
            $("#scomp_"+arr[1]).val(result[3]);
            $("#scp_"+arr[1]).val(result[4]);
            $("#smsrp_"+arr[1]).val(result[5]);
			$("#sfinalprice_"+arr[1]).val(result[6]);
			$("#smb_"+arr[1]).val(result[7]);
			$("#seh_"+arr[1]).val(result[8]);
			$("#sah_"+arr[1]).val(result[9]);
		}
	});
}
function countService(txb) {
    if(txb != 'delete') {
        var get_id = txb.id;

        var split_id = get_id.split('_');

        document.getElementById('sprojecttotal_'+split_id[1]).value = parseFloat($('#sprojectprice_'+split_id[1]).val() * $('#sprojectqty_'+split_id[1]).val());
    }

    var sum_fee = 0;
    $('[name="sprojecttotal[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="service_total"]').val(round2Fixed(sum_fee));
    $('[name="service_summary"]').val(round2Fixed(sum_fee));

    var service_budget = $('[name="service_budget"]').val();
    if(service_budget >= sum_fee) {
        $('[name="service_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="service_total"]').css("background-color", "#ff9999"); // Green
    }
}

</script>
<?php
$get_field_config_service = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT services_dashboard FROM field_config"));
$field_config_service = ','.$get_field_config_service['services_dashboard'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <?php if (strpos($base_field_config, ','."Services Service Type".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Service Type</label>
            <?php } ?>
            <?php if (strpos($base_field_config, ','."Services Category".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Category</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Heading</label>
            <?php if (strpos($field_config_service, ','."Final Retail Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Final Retail Price</label>
            <?php } ?>
            <?php if (strpos($field_config_service, ','."Admin Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Admin Price</label>
            <?php } ?>
            <?php if (strpos($field_config_service, ','."Wholesale Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Wholesale Price</label>
            <?php } ?>
            <?php if (strpos($field_config_service, ','."Commercial Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commercial Price</label>
            <?php } ?>
            <?php if (strpos($field_config_service, ','."Client Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Client Price</label>
            <?php } ?>
            <?php if (strpos($field_config_service, ','."MSRP".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">MSRP</label>
            <?php } ?>
            <?php if (strpos($field_config_service, ','."Minimum Billable".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Minimum Billable Hours</label>
            <?php } ?>
            <?php if (strpos($field_config_service, ','."Estimated Hours".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Estimated Hours</label>
            <?php } ?>
            <?php if (strpos($field_config_service, ','."Actual Hours".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Actual Hours</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?php echo get_config($dbc, 'project_service_qty_cost'); ?></label>
            <label class="col-sm-1 text-center"><?php echo get_config($dbc, 'project_service_price_or_hours'); ?></label>
			<label class="col-sm-1 text-center">Total</label>
        </div>

       <?php
        $get_services = '';
        if(!empty($_GET['pid'])) {
            $pid = $_GET['pid'];
            $each_pid = explode(',',$pid);

            foreach($each_pid as $key_pid) {
                $each_item =	rtrim(get_package($dbc, $key_pid, 'assign_services'),'**#**');
                $get_services  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['promoid'])) {
            $promoid = $_GET['promoid'];
            $each_promoid = explode(',',$promoid);

            foreach($each_promoid as $key_promoid) {
                $each_item =	rtrim(get_promotion($dbc, $key_promoid, 'assign_services'),'**#**');
                $get_services  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['cid'])) {
            $cid = $_GET['cid'];
            $each_cid = explode(',',$cid);

            foreach($each_cid as $key_cid) {
                $each_item =	rtrim(get_custom($dbc, $key_cid, 'assign_services'),'**#**');
                $get_services  .= '**'.$each_item;
            }
        }

        if(!empty($_GET['projectid'])) {
            $services = $get_contact['services'];
            $each_servicesid = explode('**',$services);
            foreach($each_servicesid as $id_all) {
                if($id_all != '') {
                    $servicesid_all = explode('#',$id_all);
                    $get_services .= '**'.$servicesid_all[0].'#'.$servicesid_all[2].'#'.$servicesid_all[1];
                }
            }
        }
        $final_total_services = 0;
        ?>

        <?php if(!empty($get_services)) {
            $each_assign_inventory = explode('**',$get_services);
            $total_count = mb_substr_count($get_services,'**');
            $id_loop = 500;

            for($inventory_loop=0; $inventory_loop<=$total_count; $inventory_loop++) {
                $each_item = explode('#',$each_assign_inventory[$inventory_loop]);
                $serviceid = '';
                $qty = '';
                $est = '';
                if(isset($each_item[0])) {
                    $serviceid = $each_item[0];
                }
                if(isset($each_item[1])) {
                    $qty = $each_item[1];
                }
                if(isset($each_item[2])) {
                    $est = $each_item[2];
                }
                $total = $qty*$est;
                $final_total_services += $total;
                if($serviceid != '') {

                    $services = explode('**', $get_rc['services']);
                    $rc_price = 0;
                    foreach($services as $pp){
                        if (strpos('#'.$pp, '#'.$serviceid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>

            <div class="form-group clearfix" id="<?php echo 'services_'.$id_loop; ?>" >
                <?php if (strpos($base_field_config, ','."Services Service Type".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Service Type:</label>
                    <select onChange='selectServiceService(this)' data-placeholder="Choose a Type..." id="<?php echo 'sservice_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(service_type) FROM services WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_services($dbc, $serviceid, 'service_type') == $row['service_type']) {
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
                <?php if (strpos($base_field_config, ','."Services Category".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectServiceCat(this)' data-placeholder="Choose a Category..." id="<?php echo 'scategory_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_services($dbc, $serviceid, 'category') == $row['category']) {
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
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select onChange='selectServiceHeading(this)' data-placeholder="Choose a Heading..." id="<?php echo 'sheading_'.$id_loop; ?>" name="serviceid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT serviceid, heading FROM services WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if ($serviceid == $row['serviceid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['serviceid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>

                    <!-- <input name="sheading[]" readonly id="<?php echo 'sheading_'.$id_loop; ?>" type="text" class="form-control" /> -->
                </div>

                <?php if (strpos($field_config_service, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="sfrp[]" value="<?php echo get_services($dbc, $serviceid, 'final_retail_price');?>" id="<?php echo 'sfrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="sap[]" value="<?php echo get_services($dbc, $serviceid, 'admin_price');?>" id="<?php echo 'sap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="swp[]" value="<?php echo get_services($dbc, $serviceid, 'wholesale_price');?>" id="<?php echo 'swp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="scomp[]" value="<?php echo get_services($dbc, $serviceid, 'commercial_price');?>" id="<?php echo 'scomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="scp[]" value="<?php echo get_services($dbc, $serviceid, 'client_price');?>" id="<?php echo 'scp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="smsrp[]" value="<?php echo get_services($dbc, $serviceid, 'msrp');?>" id="<?php echo 'smsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
               <?php if (strpos($field_config_service, ','."Minimum Billable".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Minimum Billable:</label>
                        <input name="smb[]" value="<?php echo get_services($dbc, $serviceid, 'minimum_billable');?>" id="<?php echo 'smb_'.$id_loop; ?>" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Estimated Hours".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Estimated Hours:</label>
                        <input name="seh[]" value="<?php echo get_services($dbc, $serviceid, 'estimated_hours');?>" id="<?php echo 'seh_'.$id_loop; ?>" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Actual Hours".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Actual Hours:</label>
                        <input name="sah[]" value="<?php echo get_services($dbc, $serviceid, 'actual_hours');?>" id="<?php echo 'sah_'.$id_loop; ?>" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="sfinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'sfinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php echo get_config($dbc, 'project_service_qty_cost'); ?>:</label>
                    <input name="sprojectprice[]" id="<?php echo 'sprojectprice_'.$id_loop; ?>" onchange="countService(this)" value="<?php echo $est; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php echo get_config($dbc, 'project_service_price_or_hours'); ?>:</label>
                    <input name="sprojectqty[]" id="<?php echo 'sprojectqty_'.$id_loop; ?>" onchange="countService(this)" value="<?php echo $qty; ?>" type="text" class="form-control" />
                </div>
				<div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="sprojecttotal[]" value="<?php echo $total; ?>" id="<?php echo 'sprojecttotal_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'services_','sheading_'); return false;" id="<?php echo 'deleteservices_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
            <?php  $id_loop++;
                    }
                }
            } ?>

        <div class="additional_s clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="services_0">
                <?php if (strpos($base_field_config, ','."Services Service Type".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Service Type:</label>
                    <select onChange='selectServiceService(this)' data-placeholder="Choose a Type..." id="sservice_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(service_type) FROM services WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['service_type']."'>".$row['service_type'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <?php if (strpos($base_field_config, ','."Services Category".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectServiceCat(this)' data-placeholder="Choose a Category..." id="scategory_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';

                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select onChange='selectServiceHeading(this)' data-placeholder="Choose a Heading..." id="sheading_0" name="serviceid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT serviceid, heading FROM services WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['serviceid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>

                    <!-- <input name="sheading[]" readonly id="sheading_0" type="text" class="form-control" /> -->
                </div>

                <?php if (strpos($field_config_service, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="sfrp[]" id="sfrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="sap[]" id="sap_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="swp[]" id="swp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="scomp[]" id="scomp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="scp[]" id="scp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="smsrp[]" id="smsrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>

                <?php if (strpos($field_config_service, ','."Minimum Billable".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Minimum Billable:</label>
                        <input name="smb[]" id="smb_0" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Estimated Hours".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Estimated Hours:</label>
                        <input name="seh[]" id="seh_0" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>
                <?php if (strpos($field_config_service, ','."Actual Hours".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Actual Hours:</label>
                        <input name="sah[]" id="sah_0" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="sfinalprice[]" readonly id="sfinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php echo get_config($dbc, 'project_service_qty_cost'); ?>:</label>
                    <input name="sprojectprice[]" id='sprojectprice_0' onchange="countService(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php echo get_config($dbc, 'project_service_price_or_hours'); ?>:</label>
                    <input name="sprojectqty[]" id='sprojectqty_0' onchange="countService(this)" type="text" class="form-control" />
                </div>
				<div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="sprojecttotal[]" id='sprojecttotal_0' type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'services_','sheading_'); return false;" id="deleteservices_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_s"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_s" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="service_budget" value="<?php echo $budget_price[3]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="service_total" value="<?php echo $final_total_services;?>" type="text" class="form-control">
    </div>
</div>