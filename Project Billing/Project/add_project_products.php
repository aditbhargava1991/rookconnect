
<script>
$(document).ready(function() {
	//Products
    var add_new_p = 1;
    $('#deleteproducts_0').hide();
    $('#add_row_p').on( 'click', function () {
        $('#deleteproducts_0').show();
        var clone = $('.additional_p').clone();
        clone.find('.form-control').val('');

        clone.find('#pproduct_0').attr('id', 'pproduct_'+add_new_p);
		clone.find('#pcategory_0').attr('id', 'pcategory_'+add_new_p);
        clone.find('#pheading_0').attr('id', 'pheading_'+add_new_p);
        clone.find('#pfrp_0').attr('id', 'pfrp_'+add_new_p);
        clone.find('#pap_0').attr('id', 'pap_'+add_new_p);
        clone.find('#pwp_0').attr('id', 'pwp_'+add_new_p);
        clone.find('#pcomp_0').attr('id', 'pcomp_'+add_new_p);
        clone.find('#pcp_0').attr('id', 'pcp_'+add_new_p);
        clone.find('#pmsrp_0').attr('id', 'pmsrp_'+add_new_p);
        clone.find('#pmb_0').attr('id', 'pmb_'+add_new_p);
        clone.find('#peh_0').attr('id', 'peh_'+add_new_p);
        clone.find('#pah_0').attr('id', 'pah_'+add_new_p);

		clone.find('#pfinalprice_0').attr('id', 'pfinalprice_'+add_new_p);
		clone.find('#pprojectprice_0').attr('id', 'pprojectprice_'+add_new_p);
		clone.find('#pprojectqty_0').attr('id', 'pprojectqty_'+add_new_p);
		clone.find('#pprojecttotal_0').attr('id', 'pprojecttotal_'+add_new_p);

        clone.find('#products_0').attr('id', 'products_'+add_new_p);
        clone.find('#deleteproducts_0').attr('id', 'deleteproducts_'+add_new_p);
        $('#deleteproducts_0').hide();

        clone.removeClass("additional_p");
        $('#add_here_new_p').append(clone);

        resetChosen($("#pproduct_"+add_new_p));
        resetChosen($("#pcategory_"+add_new_p));
        resetChosen($("#pheading_"+add_new_p));

        add_new_p++;

        return false;
    });
});
//Products
function selectProductProduct(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=p_product_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#pcategory_"+arr[1]).html(response);
			$("#pcategory_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectProductCat(sel) {
	var stage = encodeURIComponent(sel.value);
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=p_cat_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#pheading_"+arr[1]).html(response);
			$("#pheading_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectProductHeading(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=p_head_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*');
            $("#pfrp_"+arr[1]).val(result[0]);
            $("#pap_"+arr[1]).val(result[1]);
            $("#pwp_"+arr[1]).val(result[2]);
            $("#pcomp_"+arr[1]).val(result[3]);
            $("#pcp_"+arr[1]).val(result[4]);
            $("#pmsrp_"+arr[1]).val(result[5]);
			$("#pfinalprice_"+arr[1]).val(result[6]);
			$("#pmb_"+arr[1]).val(result[7]);
			$("#peh_"+arr[1]).val(result[8]);
			$("#pah_"+arr[1]).val(result[9]);
		}
	});
}
function countProduct(txb) {
    if(txb != 'delete') {
        var get_id = txb.id;

        var split_id = get_id.split('_');

        document.getElementById('pprojecttotal_'+split_id[1]).value = parseFloat($('#pprojectprice_'+split_id[1]).val() * $('#pprojectqty_'+split_id[1]).val());
    }

    var sum_fee = 0;
    $('[name="pprojecttotal[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="product_total"]').val(round2Fixed(sum_fee));
    $('[name="product_summary"]').val(round2Fixed(sum_fee));

    var product_budget = $('[name="product_budget"]').val();
    if(product_budget >= sum_fee) {
        $('[name="product_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="product_total"]').css("background-color", "#ff9999"); // Green
    }
}

</script>
<?php
$get_field_config_product = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT products_dashboard FROM field_config"));
$field_config_product = ','.$get_field_config_product['products_dashboard'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <?php if (strpos($base_field_config, ','."Products Product Type".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Product Type</label>
            <?php } ?>
            <?php if (strpos($base_field_config, ','."Products Category".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Category</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Heading</label>
            <?php if (strpos($field_config_product, ','."Final Retail Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Final Retail Price</label>
            <?php } ?>
            <?php if (strpos($field_config_product, ','."Admin Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Admin Price</label>
            <?php } ?>
            <?php if (strpos($field_config_product, ','."Wholesale Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Wholesale Price</label>
            <?php } ?>
            <?php if (strpos($field_config_product, ','."Commercial Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commercial Price</label>
            <?php } ?>
            <?php if (strpos($field_config_product, ','."Client Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Client Price</label>
            <?php } ?>
            <?php if (strpos($field_config_product, ','."MSRP".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">MSRP</label>
            <?php } ?>
            <?php if (strpos($field_config_product, ','."Minimum Billable".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Minimum Billable Hours</label>
            <?php } ?>
            <?php if (strpos($field_config_product, ','."Estimated Hours".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Estimated Hours</label>
            <?php } ?>
            <?php if (strpos($field_config_product, ','."Actual Hours".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Actual Hours</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center">Hourly Rate</label>
            <label class="col-sm-1 text-center">Actual Hours</label>
			<label class="col-sm-1 text-center">Total</label>
        </div>

       <?php
        $get_products = '';
        if(!empty($_GET['pid'])) {
            $pid = $_GET['pid'];
            $each_pid = explode(',',$pid);

            foreach($each_pid as $key_pid) {
                $each_item =	rtrim(get_package($dbc, $key_pid, 'assign_products'),'**#**');
                $get_products  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['promoid'])) {
            $promoid = $_GET['promoid'];
            $each_promoid = explode(',',$promoid);

            foreach($each_promoid as $key_promoid) {
                $each_item =	rtrim(get_promotion($dbc, $key_promoid, 'assign_products'),'**#**');
                $get_products  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['cid'])) {
            $cid = $_GET['cid'];
            $each_cid = explode(',',$cid);

            foreach($each_cid as $key_cid) {
                $each_item =	rtrim(get_custom($dbc, $key_cid, 'assign_products'),'**#**');
                $get_products  .= '**'.$each_item;
            }
        }

        if(!empty($_GET['projectid'])) {
            $products = $get_contact['products'];
            $each_productsid = explode('**',$products);
            foreach($each_productsid as $id_all) {
                if($id_all != '') {
                    $productsid_all = explode('#',$id_all);
                    $get_products .= '**'.$productsid_all[0].'#'.$productsid_all[2].'#'.$productsid_all[1];
                }
            }
        }
        $final_total_products = 0;
        ?>

        <?php if(!empty($get_products)) {
            $each_assign_inventory = explode('**',$get_products);
            $total_count = mb_substr_count($get_products,'**');
            $id_loop = 500;

            for($inventory_loop=0; $inventory_loop<=$total_count; $inventory_loop++) {
                $each_item = explode('#',$each_assign_inventory[$inventory_loop]);
                $productid = '';
                $qty = '';
                $est = '';
                if(isset($each_item[0])) {
                    $productid = $each_item[0];
                }
                if(isset($each_item[1])) {
                    $qty = $each_item[1];
                }
                if(isset($each_item[2])) {
                    $est = $each_item[2];
                }
                $total = $qty*$est;
                $final_total_products += $total;
                if($productid != '') {

                    $products = explode('**', $get_rc['products']);
                    $rc_price = 0;
                    foreach($products as $pp){
                        if (strpos('#'.$pp, '#'.$productid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>

            <div class="form-group clearfix" id="<?php echo 'products_'.$id_loop; ?>" >
                <?php if (strpos($base_field_config, ','."Products Product Type".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Product Type:</label>
                    <select onChange='selectProductProduct(this)' data-placeholder="Choose a Type..." id="<?php echo 'pproduct_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(product_type) FROM products WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_products($dbc, $productid, 'product_type') == $row['product_type']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['product_type']."'>".$row['product_type'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>
                <?php if (strpos($base_field_config, ','."Products Category".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectProductCat(this)' data-placeholder="Choose a Category..." id="<?php echo 'pcategory_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM products WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_products($dbc, $productid, 'category') == $row['category']) {
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
                    <select onChange='selectProductHeading(this)' data-placeholder="Choose a Heading..." id="<?php echo 'pheading_'.$id_loop; ?>" name="productid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT productid, heading FROM products WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if ($productid == $row['productid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['productid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>

                    <!-- <input name="pheading[]" readonly id="<?php echo 'sheading_'.$id_loop; ?>" type="text" class="form-control" /> -->
                </div>

                <?php if (strpos($field_config_product, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="pfrp[]" value="<?php echo get_products($dbc, $productid, 'final_retail_price');?>" id="<?php echo 'pfrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="pap[]" value="<?php echo get_products($dbc, $productid, 'admin_price');?>" id="<?php echo 'pap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="pwp[]" value="<?php echo get_products($dbc, $productid, 'wholesale_price');?>" id="<?php echo 'pwp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="pcomp[]" value="<?php echo get_products($dbc, $productid, 'commercial_price');?>" id="<?php echo 'pcomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="pcp[]" value="<?php echo get_products($dbc, $productid, 'client_price');?>" id="<?php echo 'pcp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="pmsrp[]" value="<?php echo get_products($dbc, $productid, 'msrp');?>" id="<?php echo 'pmsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
               <?php if (strpos($field_config_product, ','."Minimum Billable".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Minimum Billable:</label>
                        <input name="pmb[]" value="<?php echo get_products($dbc, $productid, 'minimum_billable');?>" id="<?php echo 'pmb_'.$id_loop; ?>" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Estimated Hours".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Estimated Hours:</label>
                        <input name="peh[]" value="<?php echo get_products($dbc, $productid, 'estimated_hours');?>" id="<?php echo 'peh_'.$id_loop; ?>" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Actual Hours".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Actual Hours:</label>
                        <input name="pah[]" value="<?php echo get_products($dbc, $productid, 'actual_hours');?>" id="<?php echo 'pah_'.$id_loop; ?>" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="pfinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'pfinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php echo get_config($dbc, 'project_product_qty_cost'); ?>:</label>
                    <input name="pprojectprice[]" id="<?php echo 'pprojectprice_'.$id_loop; ?>" onchange="countProduct(this)" value="<?php echo $est; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php echo get_config($dbc, 'project_product_price_or_hours'); ?>:</label>
                    <input name="pprojectqty[]" id="<?php echo 'pprojectqty_'.$id_loop; ?>" onchange="countProduct(this)" value="<?php echo $qty; ?>" type="text" class="form-control" />
                </div>
				<div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="pprojecttotal[]" value="<?php echo $total; ?>" id="<?php echo 'pprojecttotal_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'products_','pheading_'); return false;" id="<?php echo 'deleteproducts_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
            <?php  $id_loop++;
                    }
                }
            } ?>

        <div class="additional_p clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="products_0">
                <?php if (strpos($base_field_config, ','."Products Product Type".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Product Type:</label>
                    <select onChange='selectProductProduct(this)' data-placeholder="Choose a Type..." id="pproduct_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(product_type) FROM products WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['product_type']."'>".$row['product_type'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <?php if (strpos($base_field_config, ','."Products Category".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectProductCat(this)' data-placeholder="Choose a Category..." id="pcategory_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM products WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';

                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select onChange='selectProductHeading(this)' data-placeholder="Choose a Heading..." id="pheading_0" name="productid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT productid, heading FROM products WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['productid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>

                    <!-- <input name="pheading[]" readonly id="pheading_0" type="text" class="form-control" /> -->
                </div>

                <?php if (strpos($field_config_product, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="pfrp[]" id="pfrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="pap[]" id="pap_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="pwp[]" id="pwp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="pcomp[]" id="pcomp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="pcp[]" id="pcp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="pmsrp[]" id="pmsrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>

                <?php if (strpos($field_config_product, ','."Minimum Billable".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Minimum Billable:</label>
                        <input name="pmb[]" id="pmb_0" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Estimated Hours".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Estimated Hours:</label>
                        <input name="peh[]" id="peh_0" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>
                <?php if (strpos($field_config_product, ','."Actual Hours".',') !== FALSE) { ?>
                    <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Actual Hours:</label>
                        <input name="pah[]" id="pah_0" readonly type="text" class="form-control" />
                    </div>
                <?php } ?>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="pfinalprice[]" readonly id="pfinalprice_0" type="text" class="form-control" />
                </div>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php echo get_config($dbc, 'project_product_qty_cost'); ?>:</label>
                    <input name="pprojectprice[]" id='pprojectprice_0' onchange="countProduct(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php echo get_config($dbc, 'project_product_price_or_hours'); ?>:</label>
                    <input name="pprojectqty[]" id='pprojectqty_0' onchange="countProduct(this)" type="text" class="form-control" />
                </div>
				<div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="pprojecttotal[]" id='pprojecttotal_0' type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'products_','pheading_'); return false;" id="deleteproducts_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_p"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_p" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="product_budget" value="<?php echo $budget_price[3]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="product_total" value="<?php echo $final_total_products;?>" type="text" class="form-control">
    </div>
</div>