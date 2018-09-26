<script>
$(document).ready(function() {
	//Equipment
    var add_new_eq = 1;
    $('#deleteequipment_0').hide();
    $('#add_row_eq').on( 'click', function () {
        $('#deleteequipment_0').show();
        var clone = $('.additional_eq').clone();
        clone.find('.form-control').val('');

        clone.find('#eqequipmentcat_0').attr('id', 'eqequipmentcat_'+add_new_eq);
        clone.find('#eqequipmentun_0').attr('id', 'eqequipmentun_'+add_new_eq);

        clone.find('#eqmr_0').attr('id', 'eqmr_'+add_new_eq);
        clone.find('#eqsmr_0').attr('id', 'eqsmr_'+add_new_eq);
        clone.find('#eqdr_0').attr('id', 'eqdr_'+add_new_eq);
        clone.find('#eqhr_0').attr('id', 'eqhr_'+add_new_eq);
        clone.find('#eqhrt_0').attr('id', 'eqhrt_'+add_new_eq);
        clone.find('#eqfdb_0').attr('id', 'eqfdb_'+add_new_eq);
		clone.find('#eqfinalprice_0').attr('id', 'eqfinalprice_'+add_new_eq);
		clone.find('#eqprojectprice_0').attr('id', 'eqprojectprice_'+add_new_eq);
		clone.find('#eqprojectqty_0').attr('id', 'eqprojectqty_'+add_new_eq);
		clone.find('#eqprojecttotal_0').attr('id', 'eqprojecttotal_'+add_new_eq);

        clone.find('#equipment_0').attr('id', 'equipment_'+add_new_eq);
        clone.find('#deleteequipment_0').attr('id', 'deleteequipment_'+add_new_eq);
        $('#deleteequipment_0').hide();

        clone.removeClass("additional_eq");
        $('#add_here_new_eq').append(clone);

        resetChosen($("#eqequipmentcat_"+add_new_eq));
        resetChosen($("#eqequipmentun_"+add_new_eq));

        add_new_eq++;

        return false;
    });
});
//Equipment
function selectEquipmentCategory(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=eq_cat_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*FFM*');
            $("#eqequipmentun_"+arr[1]).html(result[0]);
			$("#eqequipmentun_"+arr[1]).trigger("change.select2");
		}
	});
}
function selectEquipmentUnSn(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=eq_un_sn_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*');
            $("#eqmr_"+arr[1]).val(result[0]);
            $("#eqsmr_"+arr[1]).val(result[1]);
            $("#eqdr_"+arr[1]).val(result[2]);
            $("#eqhr_"+arr[1]).val(result[3]);
            $("#eqhrt_"+arr[1]).val(result[4]);
            $("#eqfdc_"+arr[1]).val(result[5]);
            $("#eqfdb_"+arr[1]).val(result[6]);
			$("#eqfinalprice_"+arr[1]).val(result[7]);
        }
	});
}
function countEquipment(txb) {
    if(txb != 'delete') {
        var get_id = txb.id;

        var split_id = get_id.split('_');

        document.getElementById('eqprojecttotal_'+split_id[1]).value = parseFloat($('#eqprojectprice_'+split_id[1]).val() * $('#eqprojectqty_'+split_id[1]).val());
    }

    var sum_fee = 0;
    $('[name="eqprojecttotal[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="equ_total"]').val(round2Fixed(sum_fee));
    $('[name="equipment_summary"]').val(round2Fixed(sum_fee));

    var equ_budget = $('[name="equ_budget"]').val();
    if(equ_budget >= sum_fee) {
        $('[name="equ_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="equ_total"]').css("background-color", "#ff9999"); // Green
    }
}
</script>
<?php
$get_field_config_equipment = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config"));
$field_config_equipment = ','.$get_field_config_equipment['equipment'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <?php if (strpos($base_field_config, ','."Equipment Category".',') !== FALSE) { ?>
            <label class="col-sm-2 text-center">Category</label>
            <?php } ?>
            <label class="col-sm-2 text-center">Unit/<br>Serial Number</label>
            <?php if (strpos($field_config_equipment, ','."Monthly Rate".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Monthly Rate</label>
            <?php } ?>
            <?php if (strpos($field_config_equipment, ','."Semi Monthly Rate".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Semi Monthly Rate</label>
            <?php } ?>
            <?php if (strpos($field_config_equipment, ','."Daily Rate".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Daily Rate</label>
            <?php } ?>
            <?php if (strpos($field_config_equipment, ','."HR Rate Work".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">HR Rate Work</label>
            <?php } ?>
            <?php if (strpos($field_config_equipment, ','."HR Rate Travel".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">HR Rate Travel</label>
            <?php } ?>
            <?php if (strpos($field_config_equipment, ','."Field Day Cost".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Field Day Cost</label>
            <?php } ?>
            <?php if (strpos($field_config_equipment, ','."Field Day Cost".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Field Day Billable</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</label>
            <label class="col-sm-1 text-center">Quantity</label>
            <label class="col-sm-1 text-center">Total</label>
        </div>

       <?php
        $get_equipment_field = '';
        if(!empty($_GET['pid'])) {
            $pid = $_GET['pid'];
            $each_pid = explode(',',$pid);

            foreach($each_pid as $key_pid) {
                $each_item =	rtrim(get_package($dbc, $key_pid, 'assign_equipment'),'**#**');
                $get_equipment_field  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['promoid'])) {
            $promoid = $_GET['promoid'];
            $each_promoid = explode(',',$promoid);

            foreach($each_promoid as $key_promoid) {
                $each_item =	rtrim(get_promotion($dbc, $key_promoid, 'assign_equipment'),'**#**');
                $get_equipment_field  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['cid'])) {
            $cid = $_GET['cid'];
            $each_cid = explode(',',$cid);

            foreach($each_cid as $key_cid) {
                $each_item =	rtrim(get_custom($dbc, $key_cid, 'assign_equipment'),'**#**');
                $get_equipment_field  .= '**'.$each_item;
            }
        }

        if(!empty($_GET['projectid'])) {
            $equipment = $get_contact['equipment'];
            $each_data = explode('**',$equipment);
            foreach($each_data as $id_all) {
                if($id_all != '') {
                    $data_all = explode('#',$id_all);
                    $get_equipment_field .= '**'.$data_all[0].'#'.$data_all[2].'#'.$data_all[1];
                }
            }
        }
        $final_total_equipment = 0;
        ?>

        <?php if(!empty($get_equipment_field)) {
            $each_assign_equipment = explode('**',$get_equipment_field);
            $total_count = mb_substr_count($get_equipment_field,'**');
            $id_loop = 500;
            for($inventory_loop=0; $inventory_loop<=$total_count; $inventory_loop++) {

                $each_item = explode('#',$each_assign_equipment[$inventory_loop]);
                $equipmentid = '';
                $qty = '';
                $est = '';
                if(isset($each_item[0])) {
                    $equipmentid = $each_item[0];
                }
                if(isset($each_item[1])) {
                    $qty = $each_item[1];
                }
                if(isset($each_item[2])) {
                    $est = $each_item[2];
                }
                $total = $qty*$est;
                $final_total_equipment += $total;

                if($equipmentid != '') {

                    $equipment = explode('**', $get_rc['equipment']);
                    $rc_price = 0;
                    foreach($equipment as $pp){
                        if (strpos('#'.$pp, '#'.$equipmentid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>
            <div class="form-group clearfix" id="<?php echo 'equipment_'.$id_loop; ?>" >
                <?php if (strpos($base_field_config, ','."Equipment Category".',') !== FALSE) { ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectEquipmentCategory(this)' data-placeholder="Choose a Category..." id="<?php echo 'eqequipmentcat_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM equipment");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_equipment_field($dbc, $equipmentid, 'category') == $row['category']) {
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
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Unit/Serial Number:</label>
                    <select onChange='selectEquipmentUnSn(this)' data-placeholder="Choose a Number..." id="<?php echo 'eqequipmentun_'.$id_loop; ?>" name="equipmentid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT equipmentid, unit_number, serial_number  FROM equipment");
                        while($row = mysqli_fetch_array($query)) {
                            if ($equipmentid == $row['equipmentid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['equipmentid']."'>".$row['unit_number'].' : '.$row['serial_number'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php if (strpos($field_config_equipment, ','."Monthly Rate".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Monthly Rate:</label>
                    <input name="eqmr[]" value="<?php echo get_equipment_field($dbc, $equipmentid, 'monthly_rate');?>" id="<?php echo 'eqmr_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."Semi Monthly Rate".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Semi Monthly Rate:</label>
                    <input name="eqsmr[]" value="<?php echo get_equipment_field($dbc, $equipmentid, 'semi_monthly_rate');?>" id="<?php echo 'eqsmr_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."Daily Rate".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Daily Rate:</label>
                    <input name="eqdr[]" value="<?php echo get_equipment_field($dbc, $equipmentid, 'daily_rate');?>" id="<?php echo 'eqdr_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."HR Rate Work".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">HR Rate Work:</label>
                    <input name="eqhr[]" value="<?php echo get_equipment_field($dbc, $equipmentid, 'hr_rate_work');?>" id="<?php echo 'eqhr_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."HR Rate Travel".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">HR Rate Travel:</label>
                    <input name="eqhrt[]" value="<?php echo get_equipment_field($dbc, $equipmentid, 'hr_rate_travel');?>" id="<?php echo 'eqhrt_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."Field Day Cost".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Field Day Cost:</label>
                    <input name="eqfdc[]" value="<?php echo get_equipment_field($dbc, $equipmentid, 'field_day_cost');?>" id="<?php echo 'eqfdc_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."Field Day Billable".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Field Day Billable:</label>
                    <input name="eqfdb[]" value="<?php echo get_equipment_field($dbc, $equipmentid, 'field_day_billable');?>" id="<?php echo 'eqfdb_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="eqfinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'eqfinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="eqprojectprice[]" value="<?php echo $est; ?>" id="<?php echo 'eqprojectprice_'.$id_loop; ?>" onchange="countEquipment(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
                    <input name="eqprojectqty[]" id="<?php echo 'eqprojectqty_'.$id_loop; ?>" onchange="countEquipment(this)" value="<?php echo $qty; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="eqprojecttotal[]" value="<?php echo $total; ?>" id="<?php echo 'eqprojecttotal_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'equipment_','eqequipmentun_'); return false;" id="<?php echo 'deleteequipment_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
            <?php  $id_loop++;
                    }
                }
            } ?>

        <div class="additional_eq clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="equipment_0">
                <?php if (strpos($base_field_config, ','."Equipment Category".',') !== FALSE) { ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectEquipmentCategory(this)' data-placeholder="Choose a Category..." id="eqequipmentcat_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM equipment");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Unit/Serial Number:</label>
                    <select onChange='selectEquipmentUnSn(this)' data-placeholder="Choose a Number..." id="eqequipmentun_0" name="equipmentid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT equipmentid, unit_number, serial_number  FROM equipment");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['equipmentid']."'>".$row['unit_number'].' : '.$row['serial_number'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php if (strpos($field_config_equipment, ','."Monthly Rate".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Monthly Rate:</label>
                    <input name="eqmr[]" id="eqmr_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."Semi Monthly Rate".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Semi Monthly Rate:</label>
                    <input name="eqsmr[]" id="eqsmr_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."Daily Rate".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Daily Rate:</label>
                    <input name="eqdr[]" id="eqdr_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."HR Rate Work".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">HR Rate Work:</label>
                    <input name="eqhr[]" id="eqhr_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."HR Rate Travel".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">HR Rate Travel:</label>
                    <input name="eqhrt[]" id="eqhrt_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."Field Day Cost".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Field Day Cost:</label>
                    <input name="eqfdc[]" id="eqfdc_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_equipment, ','."Field Day Billable".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Field Day Billable:</label>
                    <input name="eqfdb[]" id="eqfdb_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="eqfinalprice[]" readonly id="eqfinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="eqprojectprice[]" id='eqprojectprice_0' onchange="countEquipment(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
                    <input name="eqprojectqty[]" id='eqprojectqty_0' onchange="countEquipment(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="eqprojecttotal[]" id='eqprojecttotal_0' type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'equipment_','eqequipmentun_'); return false;" id="deleteequipment_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_eq"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_eq" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="equ_budget" value="<?php echo $budget_price[10]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="equ_total" value="<?php echo $final_total_equipment;?>" type="text" class="form-control">
    </div>
</div>