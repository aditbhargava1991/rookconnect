<script>
$(document).ready(function() {
	//Vendor
    var add_new_v = 1;
    $('#deletevendor_0').hide();
    $('#add_row_v').on( 'click', function () {
        $('#deletevendor_0').show();
        var clone = $('.additional_v').clone();
        clone.find('.form-control').val('');

        clone.find('#prvendorid_0').attr('id', 'prvendorid_'+add_new_v);
        clone.find('#prcategory_0').attr('id', 'prcategory_'+add_new_v);
        clone.find('#prpricelistid_0').attr('id', 'prpricelistid_'+add_new_v);
        clone.find('#prproduct_0').attr('id', 'prproduct_'+add_new_v);
		clone.find('#vunitprice_0').attr('id', 'vunitprice_'+add_new_v);
        clone.find('#vfinalprice_0').attr('id', 'vfinalprice_'+add_new_v);
		clone.find('#vprojectprice_0').attr('id', 'vprojectprice_'+add_new_v);
		clone.find('#vprojectqty_0').attr('id', 'vprojectqty_'+add_new_v);
		clone.find('#vprojecttotal_0').attr('id', 'vprojecttotal_'+add_new_v);

        clone.find('#vendor_0').attr('id', 'vendor_'+add_new_v);
        clone.find('#deletevendor_0').attr('id', 'deletevendor_'+add_new_v);
        $('#deletevendor_0').hide();

        clone.removeClass("additional_v");
        $('#add_here_new_v').append(clone);

        resetChosen($("#prvendorid_"+add_new_v));
        resetChosen($("#prcategory_"+add_new_v));
        resetChosen($("#prpricelistid_"+add_new_v));
        resetChosen($("#prproduct_"+add_new_v));

        add_new_v++;

        return false;
    });
});

//Vendor
function selectVendor(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=vendor_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#prpricelistid_"+arr[1]).html(response);
			$("#prpricelistid_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectPricelist(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=vpricelist_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#prcategory_"+arr[1]).html(response);
			$("#prcategory_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectCategory(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var pricelist = $("#prpricelistid_"+arr[1]).val();
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=vcat_config&value="+stage+"&pricelist="+pricelist,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#prproduct_"+arr[1]).html(response);
			$("#prproduct_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectProduct(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var pricelist = $("#prpricelistid_"+arr[1]).val();
    var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=vproduct_config&value="+stage+"&pricelist="+pricelist+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*');
            $("#vunitprice_"+arr[1]).val(result[0]);
            $("#vfinalprice_"+arr[1]).val(result[1]);
		}
	});
}
function countVendor(txb) {
    if(txb != 'delete') {
        var get_id = txb.id;

        var split_id = get_id.split('_');

        document.getElementById('vprojecttotal_'+split_id[1]).value = parseFloat($('#vprojectprice_'+split_id[1]).val() * $('#vprojectqty_'+split_id[1]).val());
    }

    var sum_fee = 0;
    $('[name="vprojecttotal[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="vendor_total"]').val(round2Fixed(sum_fee));
    $('[name="vendorpl_summary"]').val(round2Fixed(sum_fee));

    var vendor_budget = $('[name="vendor_budget"]').val();
    if(vendor_budget >= sum_fee) {
        $('[name="vendor_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="vendor_total"]').css("background-color", "#ff9999"); // Green
    }
}
</script>
<?php
$get_field_config_vendors = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT vendors FROM field_config"));
$field_config_vendors = ','.$get_field_config_vendors['vendors'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <label class="col-sm-2 text-center">Vendor</label>
            <label class="col-sm-2 text-center">Price List</label>
            <label class="col-sm-2 text-center">Category</label>
            <label class="col-sm-1 text-center">Product</label>
            <label class="col-sm-1 text-center">Canadian $ Cost Per Unit</label>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</label>
            <label class="col-sm-1 text-center">Hours</label>
            <label class="col-sm-1 text-center">Total</label>
        </div>

        <?php
        $get_vendor_pricelist = '';
        if(!empty($_GET['pid'])) {
            $pid = $_GET['pid'];
            $each_pid = explode(',',$pid);

            foreach($each_pid as $key_pid) {
                $each_item =	rtrim(get_package($dbc, $key_pid, 'assign_vendor'),'**#**');
                $get_vendor_pricelist  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['promoid'])) {
            $promoid = $_GET['promoid'];
            $each_promoid = explode(',',$promoid);

            foreach($each_promoid as $key_promoid) {
                $each_item =	rtrim(get_promotion($dbc, $key_promoid, 'assign_vendor'),'**#**');
                $get_vendor_pricelist  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['cid'])) {
            $cid = $_GET['cid'];
            $each_cid = explode(',',$cid);

            foreach($each_cid as $key_cid) {
                $each_item =	rtrim(get_custom($dbc, $key_cid, 'assign_vendor'),'**#**');
                $get_vendor_pricelist  .= '**'.$each_item;
            }
        }

        if(!empty($_GET['projectid'])) {
            $vendor = $get_contact['vendor'];
            $each_data = explode('**',$vendor);
            foreach($each_data as $id_all) {
                if($id_all != '') {
                    $data_all = explode('#',$id_all);
                    $get_vendor_pricelist .= '**'.$data_all[0].'#'.$data_all[2].'#'.$data_all[1];
                }
            }
        }
        $final_total_vendor = 0;
        ?>

        <?php if(!empty($get_vendor_pricelist)) {
            $each_assign_inventory = explode('**',$get_vendor_pricelist);
            $total_count = mb_substr_count($get_vendor_pricelist,'**');
            $id_loop = 500;
            for($inventory_loop=0; $inventory_loop<=$total_count; $inventory_loop++) {

                $each_item = explode('#',$each_assign_inventory[$inventory_loop]);
                $pricelistid = '';
                $qty = '';
                $est = '';
                if(isset($each_item[0])) {
                    $pricelistid = $each_item[0];
                }
                if(isset($each_item[1])) {
                    $qty = $each_item[1];
                }
                if(isset($each_item[2])) {
                    $est = $each_item[2];
                }
                $total = $qty*$est;
                $final_total_vendor += $total;

                if($pricelistid != '') {
                    $vendor = explode('**', $get_rc['vendor']);
                    $rc_price = 0;
                    foreach($vendor as $pp){
                        if (strpos('#'.$pp, '#'.$pricelistid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>

            <div class="form-group clearfix" id="<?php echo 'vendor_'.$id_loop; ?>" >
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Vendor:</label>
                    <select onChange='selectVendor(this)' data-placeholder="Choose a Vendor..." id="<?php echo 'prvendorid_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Vendor' AND deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_vendor_pricelist($dbc, $pricelistid, 'vendorid') == $row['contactid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Price List:</label>
                    <select onChange='selectPricelist(this)' data-placeholder="Choose a Price List Item..." id="<?php echo 'prpricelistid_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT pricelistid, pricelist_name FROM vendor_pricelist WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_vendor_pricelist($dbc, $pricelistid, 'pricelist_name') == $row['pricelist_name']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['pricelist_name']."'>".$row['pricelist_name'].'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectCategory(this)' data-placeholder="Choose a Category..." id="<?php echo 'prcategory_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT pricelistid, category FROM vendor_pricelist WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_vendor_pricelist($dbc, $pricelistid, 'category') == $row['category']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['category']."'>".$row['category'].'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Product:</label>
                    <select onChange='selectProduct(this)' data-placeholder="Choose a Product..." id="<?php echo 'prproduct_'.$id_loop; ?>" name="vendorperson[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT pricelistid, name FROM vendor_pricelist WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if ($pricelistid == $row['pricelistid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['pricelistid']."'>".decryptIt($row['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Canadian $ Cost Per Unit:</label>
                    <input name="vcdn_cpu[]" value="<?php echo get_vendor_pricelist($dbc, $pricelistid, 'cdn_cpu');?>" id="<?php echo 'vunitprice_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="vfinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'vfinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="vprojectprice[]" value="<?php echo $est; ?>" id="<?php echo 'vprojectprice_'.$id_loop; ?>" onchange="countVendor(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Hours:</label>
                    <input name="vprojectqty[]" id="<?php echo 'vprojectqty_'.$id_loop; ?>" onchange="countVendor(this)" value="<?php echo $qty; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="vprojecttotal[]" value="<?php echo $total; ?>" id="<?php echo 'vprojecttotal_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'vendor_','prproduct_'); return false;" id="<?php echo 'deletevendor_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
            <?php  $id_loop++;
                    }
                }
            } ?>

        <div class="additional_v clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="vendor_0">
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Vendor:</label>
                    <select onChange='selectVendor(this)' data-placeholder="Choose a Vendor..." id="prvendorid_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
						<?php
							$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Vendor' AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
							foreach($query as $id) {
								$selected = '';
								echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
							}
						  ?>
                    </select>
                </div>

                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Price List:</label>
                    <select onChange='selectPricelist(this)' data-placeholder="Choose a Price List Item..." id="prpricelistid_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT pricelistid, pricelist_name FROM vendor_pricelist WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['pricelist_name']."'>".$row['pricelist_name'].'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectCategory(this)' data-placeholder="Choose a Category..." id="prcategory_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT pricelistid, category FROM vendor_pricelist WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Product:</label>
                    <select onChange='selectProduct(this)' data-placeholder="Choose a Product..." id="prproduct_0" name="vendorperson[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT pricelistid, name FROM vendor_pricelist WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['pricelistid']."'>".decryptIt($row['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Canadian $ Cost Per Unit:</label>
                    <input name="vcdn_cpu[]" id="vunitprice_0" readonly type="text" class="form-control" />
                </div>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="vfinalprice[]" readonly id="vfinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="vprojectprice[]" id='vprojectprice_0' onchange="countVendor(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Hours:</label>
                    <input name="vprojectqty[]" id='vprojectqty_0' onchange="countVendor(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="vprojecttotal[]" id='vprojecttotal_0' type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'vendor_','prproduct_'); return false;" id="deletevendor_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_v"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_v" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="vendor_budget" value="<?php echo $budget_price[7]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="vendor_total" value="<?php echo $final_total_vendor;?>" type="text" class="form-control">
    </div>
</div>
