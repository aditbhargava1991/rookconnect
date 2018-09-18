<script>
$(document).ready(function() {
	//Inventory
    var add_new_in = 1;
    $('#deleteinventory_0').hide();
    $('#add_row_in').on( 'click', function () {
        $('#deleteinventory_0').show();
        var clone = $('.additional_in').clone();
        clone.find('.form-control').val('');

        clone.find('#ininventorycat_0').attr('id', 'ininventorycat_'+add_new_in);
		clone.find('#ininventorycode_0').attr('id', 'ininventorycode_'+add_new_in);
		clone.find('#ininventorypn_0').attr('id', 'ininventorypn_'+add_new_in);
		clone.find('#ininventoryname_0').attr('id', 'ininventoryname_'+add_new_in);
        clone.find('#inrp_0').attr('id', 'inrp_'+add_new_in);
        clone.find('#inap_0').attr('id', 'inap_'+add_new_in);
        clone.find('#inwp_0').attr('id', 'inwp_'+add_new_in);
        clone.find('#incomp_0').attr('id', 'incomp_'+add_new_in);
        clone.find('#incp_0').attr('id', 'incp_'+add_new_in);
        clone.find('#inmsrp_0').attr('id', 'inmsrp_'+add_new_in);
		clone.find('#infinalprice_0').attr('id', 'infinalprice_'+add_new_in);
		clone.find('#inprojectprice_0').attr('id', 'inprojectprice_'+add_new_in);
		clone.find('#inprojectqty_0').attr('id', 'inprojectqty_'+add_new_in);
		clone.find('#inprojecttotal_0').attr('id', 'inprojecttotal_'+add_new_in);

        clone.find('#inventory_0').attr('id', 'inventory_'+add_new_in);
        clone.find('#deleteinventory_0').attr('id', 'deleteinventory_'+add_new_in);
        $('#deleteinventory_0').hide();

        clone.removeClass("additional_in");
        $('#add_here_new_in').append(clone);

        resetChosen($("#ininventorycat_"+add_new_in));
        resetChosen($("#ininventorycode_"+add_new_in));
        resetChosen($("#ininventorypn_"+add_new_in));
        resetChosen($("#ininventoryname_"+add_new_in));

        add_new_in++;

        return false;
    });

});
//Inventory
function selectInventoryCategory(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=in_cat_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#ininventoryname_"+arr[1]).html(response);
			$("#ininventoryname_"+arr[1]).trigger("change.select2");
		}
	});
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=in_cat_config_partno&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#ininventorypart_"+arr[1]).html(response);
			$("#ininventorypart_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectInventoryCodePartName(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=in_code_part_name_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*FFM*');
            $("#inrp_"+arr[1]).val(result[0]);
            $("#inap_"+arr[1]).val(result[1]);
            $("#inwp_"+arr[1]).val(result[2]);
            $("#incomp_"+arr[1]).val(result[3]);
            $("#incp_"+arr[1]).val(result[4]);
            $("#inmsrp_"+arr[1]).val(result[5]);
			$("#infinalprice_"+arr[1]).val(result[6]);
		}
	});
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=in_code_part_name_config_number&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#ininventorypart_"+arr[1]).html(response);
			$("#ininventorypart_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectInventoryCodePartNo(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=in_code_part_no_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*FFM*');
            $("#inrp_"+arr[1]).val(result[0]);
            $("#inap_"+arr[1]).val(result[1]);
            $("#inwp_"+arr[1]).val(result[2]);
            $("#incomp_"+arr[1]).val(result[3]);
            $("#incp_"+arr[1]).val(result[4]);
            $("#inmsrp_"+arr[1]).val(result[5]);
			$("#infinalprice_"+arr[1]).val(result[6]);
		}
	});
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=in_code_part_no_config_name&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#ininventoryname_"+arr[1]).html(response);
			$("#ininventoryname_"+arr[1]).trigger("change.select2");
		}
	});
}
function countInventory(txb) {
    if(txb != 'delete') {
        var get_id = txb.id;

        var split_id = get_id.split('_');

        document.getElementById('inprojecttotal_'+split_id[1]).value = parseFloat($('#inprojectprice_'+split_id[1]).val() * $('#inprojectqty_'+split_id[1]).val());
    }

    var sum_fee = 0;
    $('[name="inprojecttotal[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="inventory_total"]').val(round2Fixed(sum_fee));
    $('[name="inventory_summary"]').val(round2Fixed(sum_fee));

    var inventory_budget = $('[name="inventory_budget"]').val();
    if(inventory_budget >= sum_fee) {
        $('[name="inventory_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="inventory_total"]').css("background-color", "#ff9999"); // Green
    }
}
</script>
<?php
$get_field_config_inventory = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(inventory_dashboard SEPARATOR ',') AS inventory FROM field_config_inventory"));
$field_config_inventory = ','.$get_field_config_inventory['inventory'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <?php if (strpos($base_field_config, ','."Inventory Category".',') !== FALSE) { ?>
            <label class="col-sm-2 text-center">Category</label>
            <?php } ?>
            <label class="col-sm-2 text-center">Product Name</label>
			<?php 
			if (strpos($base_field_config, ','."Inventory Part Number".',') !== FALSE) { ?>
			<label class="col-sm-2 text-center">Part Number</label>
			<?php } ?>
            <?php if (strpos($field_config_inventory, ','."Final Retail Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Final Retail Price</label>
            <?php } ?>
            <?php if (strpos($field_config_inventory, ','."Admin Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Admin Price</label>
            <?php } ?>
            <?php if (strpos($field_config_inventory, ','."Wholesale Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Wholesale Price</label>
            <?php } ?>
            <?php if (strpos($field_config_inventory, ','."Commercial Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commercial Price</label>
            <?php } ?>
            <?php if (strpos($field_config_inventory, ','."Client Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Client Price</label>
            <?php } ?>
            <?php if (strpos($field_config_inventory, ','."MSRP".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">MSRP</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</label>
            <label class="col-sm-1 text-center">Quantity</label>
            <label class="col-sm-1 text-center">Total</label>
        </div>

        <?php
        $get_inventory = '';
        if(!empty($_GET['pid'])) {
            $pid = $_GET['pid'];
            $each_pid = explode(',',$pid);

            foreach($each_pid as $key_pid) {
                $each_item =	rtrim(get_package($dbc, $key_pid, 'assign_inventory'),'**#**');
                $get_inventory  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['promoid'])) {
            $promoid = $_GET['promoid'];
            $each_promoid = explode(',',$promoid);

            foreach($each_promoid as $key_promoid) {
                $each_item =	rtrim(get_promotion($dbc, $key_promoid, 'assign_inventory'),'**#**');
                $get_inventory  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['cid'])) {
            $cid = $_GET['cid'];
            $each_cid = explode(',',$cid);

            foreach($each_cid as $key_cid) {
                $each_item =	rtrim(get_custom($dbc, $key_cid, 'assign_inventory'),'**#**');
                $get_inventory  .= '**'.$each_item;
            }
        }

        if(!empty($_GET['projectid'])) {
            $inventory = $get_contact['inventory'];
            $each_data = explode('**',$inventory);
            foreach($each_data as $id_all) {
                if($id_all != '') {
                    $data_all = explode('#',$id_all);
                    $get_inventory .= '**'.$data_all[0].'#'.$data_all[2].'#'.$data_all[1];
                }
            }
        }
        $final_total_inventory = 0;
        ?>

        <?php if(!empty($get_inventory)) {
            $each_assign_inventory = explode('**',$get_inventory);
            $total_count = mb_substr_count($get_inventory,'**');
            $id_loop = 500;
            for($inventory_loop=0; $inventory_loop<=$total_count; $inventory_loop++) {

                $each_item = explode('#',$each_assign_inventory[$inventory_loop]);
                $inventoryid = '';
                $qty = '';
                $est = '';
                if(isset($each_item[0])) {
                    $inventoryid = $each_item[0];
                }
                if(isset($each_item[1])) {
                    $qty = $each_item[1];
                }
                if(isset($each_item[2])) {
                    $est = $each_item[2];
                }
                $total = $qty*$est;
                $final_total_inventory += $total;

                if($inventoryid != '') {
                    $inventory = explode('**', $get_rc['inventory']);
                    $rc_price = 0;
                    foreach($inventory as $pp){
                        if (strpos('#'.$pp, '#'.$inventoryid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>

            <div class="form-group clearfix" id="<?php echo 'inventory_'.$id_loop; ?>" >
                <?php if (strpos($base_field_config, ','."Inventory Category".',') !== FALSE) { ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectInventoryCategory(this)' data-placeholder="Choose a Category..." id="<?php echo 'ininventorycat_'.$id_loop; ?>" class="chosen-select-deselect form-control inventoryid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT category FROM inventory");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_inventory($dbc, $inventoryid, 'category') == $row['category']) {
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
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Product Name:</label>
                <select onChange='selectInventoryCodePartName(this)' data-placeholder="Choose a Unit Number..." id="<?php echo 'ininventoryname_'.$id_loop; ?>" name="inventoryid[]" class="chosen-select-deselect form-control inventoryid" width="380">
                    <option value=''></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory");
                    while($row = mysqli_fetch_array($query)) {
                        if ($inventoryid == $row['inventoryid']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $row['inventoryid']."'>".decryptIt($row['name']).'</option>';
                    }
                    ?>
                </select>
                </div>
				<?php if (strpos($base_field_config, ','."Inventory Part Number".',') !== FALSE) { ?>
					<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Part Number:</label>
                    <select onChange='selectInventoryCodePartNo(this)' data-placeholder="Choose a Part Number..." id="<?php echo 'ininventorypart_'.$id_loop; ?>" class="chosen-select-deselect form-control inventoryid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM inventory WHERE inventoryid = '$inventoryid' order by inventoryid");
                        while($row = mysqli_fetch_array($query)) {
                            if ($inventoryid == $row['inventoryid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['inventoryid']."'>".$row['part_no'].'</option>';
                        }
                        ?>
                    </select>
                    </div>
					 <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="inrp[]" value="<?php echo get_inventory($dbc, $inventoryid, 'final_retail_price');?>" id="<?php echo 'inrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="inap[]" value="<?php echo get_inventory($dbc, $inventoryid, 'admin_price');?>" id="<?php echo 'inap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="inwp[]" value="<?php echo get_inventory($dbc, $inventoryid, 'wholesale_price');?>" id="<?php echo 'inwp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="incomp[]" value="<?php echo get_inventory($dbc, $inventoryid, 'commercial_price');?>" id="<?php echo 'incomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="incp[]" value="<?php echo get_inventory($dbc, $inventoryid, 'client_price');?>" id="<?php echo 'incp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="inmsrp[]" value="<?php echo get_inventory($dbc, $inventoryid, 'msrp');?>" id="<?php echo 'inmsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="infinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'infinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="inprojectprice[]" value="<?php echo $est; ?>" id="<?php echo 'inprojectprice_'.$id_loop; ?>" onchange="countInventory(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
                    <input name="inprojectqty[]" id="<?php echo 'inprojectqty_'.$id_loop; ?>" onchange="countInventory(this)" value="<?php echo $qty; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="inprojecttotal[]" value="<?php echo $total; ?>" id="<?php echo 'inprojecttotal_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'inventory_','ininventoryname_'); return false;" id="<?php echo 'deleteinventory_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
            <?php  $id_loop++;
                    }
                }
            } ?>

        <div class="additional_in clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="inventory_0">
                <?php if (strpos($base_field_config, ','."Inventory Category".',') !== FALSE) { ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectInventoryCategory(this)' data-placeholder="Choose a Category..." id="ininventorycat_0" class="chosen-select-deselect form-control inventoryid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM inventory");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Product Name:</label>
                    <select onChange='selectInventoryCodePartName(this)' data-placeholder="Choose a Unit Number..." id="ininventoryname_0" name="inventoryid[]" class="chosen-select-deselect form-control inventoryid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['inventoryid']."'>".decryptIt($row['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
				<?php if (strpos($base_field_config, ','."Inventory Part Number".',') !== FALSE) { ?>
					<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Part Number:</label>
                    <select onChange='selectInventoryCodePartNo(this)' data-placeholder="Choose a Unit Number..." id="ininventorypart_0" class="chosen-select-deselect form-control inventoryid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM inventory order by part_no");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['inventoryid']."'>".$row['part_no'].'</option>';
                        }
                        ?>
                    </select>
                    </div>
				<?php } ?>
                <?php if (strpos($field_config_inventory, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="inrp[]" id="inrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="inap[]" id="inap_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="inwp[]" id="inwp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="incomp[]" id="incomp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="incp[]" id="incp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_inventory, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="inmsrp[]" id="inmsrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="infinalprice[]" readonly id="infinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="inprojectprice[]" id='inprojectprice_0' onchange="countInventory(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
                    <input name="inprojectqty[]" id='inprojectqty_0' onchange="countInventory(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="inprojecttotal[]" id='inprojecttotal_0' type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'inventory_','ininventoryname_'); return false;" id="deleteinventory_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_in"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_in" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="inventory_budget" value="<?php echo $budget_price[9]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="inventory_total" value="<?php echo $final_total_inventory;?>" type="text" class="form-control">
    </div>
</div>