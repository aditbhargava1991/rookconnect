<script>
$(document).ready(function() {
    var add_new_promotion = 1;
    $('#deletepromotion_0').hide();
    $('#add_row_promotion').on( 'click', function () {
        $('#deletepromotion_0').show();
        var clone = $('.additional_promotion').clone();
        clone.find('.form-control').val('');

        clone.find('#promotionservice_0').attr('id', 'promotionservice_'+add_new_promotion);
		clone.find('#promotioncategory_0').attr('id', 'promotioncategory_'+add_new_promotion);
        clone.find('#promotionheading_0').attr('id', 'promotionheading_'+add_new_promotion);
        clone.find('#promotionfrp_0').attr('id', 'promotionfrp_'+add_new_promotion);
        clone.find('#promotionap_0').attr('id', 'promotionap_'+add_new_promotion);
        clone.find('#promotionwp_0').attr('id', 'promotionwp_'+add_new_promotion);
        clone.find('#promotioncomp_0').attr('id', 'promotioncomp_'+add_new_promotion);
        clone.find('#promotioncp_0').attr('id', 'promotioncp_'+add_new_promotion);
        clone.find('#promotionmsrp_0').attr('id', 'promotionmsrp_'+add_new_promotion);
		clone.find('#promotionfinalprice_0').attr('id', 'promotionfinalprice_'+add_new_promotion);
        clone.find('#promotionview_0').attr('id', 'promotionview_'+add_new_promotion);
        clone.find('#promotionest_0').attr('id', 'promotionest_'+add_new_promotion);

        clone.find('#promotion_0').attr('id', 'promotion_'+add_new_promotion);
        clone.find('#deletepromotion_0').attr('id', 'deletepromotion_'+add_new_promotion);
        $('#deletepromotion_0').hide();

        clone.removeClass("additional_promotion");
        $('#add_here_new_promotion').append(clone);

        resetChosen($("#promotionservice_"+add_new_promotion));

        resetChosen($("#promotioncategory_"+add_new_promotion));
        resetChosen($("#promotionheading_"+add_new_promotion));

        add_new_promotion++;

        return false;
    });
});

//Promotions & Promotions
function selectPromotionService(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=promotion_service_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            alert('If you choose any promotion then all data you inserted will gone.');
            $("#promotioncategory_"+arr[1]).html(response);
			$("#promotioncategory_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectPromotionCat(sel) {
	var stage = encodeURIComponent(sel.value);
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=promotion_cat_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#promotionheading_"+arr[1]).html(response);
			$("#promotionheading_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectPromotionHeading(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=promotion_head_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*');
            $("#promotionfrp_"+arr[1]).val(result[0]);
            $("#promotionap_"+arr[1]).val(result[1]);
            $("#promotionwp_"+arr[1]).val(result[2]);
            $("#promotioncomp_"+arr[1]).val(result[3]);
            $("#promotioncp_"+arr[1]).val(result[4]);
            $("#promotionmsrp_"+arr[1]).val(result[5]);
			$("#promotionfinalprice_"+arr[1]).val(result[6]);

            //var clientid = $("#projectclientid").val();
            //var ratecardid = $("#ratecardid").val();
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
function countPromotion() {
    var sum_fee = 0;
    $('[name="promotionprojectprice[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="promotion_total"]').val(round2Fixed(sum_fee));
    $('[name="promotion_summary"]').val(round2Fixed(sum_fee));

    var promotion_budget = $('[name="promotion_budget"]').val();
    if(promotion_budget >= sum_fee) {
        $('[name="promotion_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="promotion_total"]').css("background-color", "#ff9999"); // Green
    }
}
</script>

<?php
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT promotion FROM field_config"));
$field_config = ','.$get_field_config['promotion'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <?php if (strpos($base_field_config, ','."Promotion Service Type".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Service Type</label>
            <?php } ?>
            <?php if (strpos($base_field_config, ','."Promotion Category".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Category</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Heading</label>
            <?php if (strpos($field_config, ','."Final Retail Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Final Retail Price</label>
            <?php } ?>
            <?php if (strpos($field_config, ','."Admin Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Admin Price</label>
            <?php } ?>
            <?php if (strpos($field_config, ','."Wholesale Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Wholesale Price</label>
            <?php } ?>
            <?php if (strpos($field_config, ','."Commercial Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commercial Price</label>
            <?php } ?>
            <?php if (strpos($field_config, ','."Client Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Client Price</label>
            <?php } ?>
            <?php if (strpos($field_config, ','."MSRP".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">MSRP</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</label>
        </div>

        <?php if((!empty($_GET['promoid'])) || (!empty($_GET['projectid']))) {
            $promoid = '';
            $promoval = '';
            if(!empty($_GET['promoid'])) {
                $promoid = $_GET['promoid'];
            }

            if(!empty($_GET['projectid'])) {
                $promotion = $get_contact['promotion'];
                $each_promoid = explode('**',$promotion);
                $promoid .= ',';
                foreach($each_promoid as $id_all) {
                    if($id_all != '') {
                        $promoid_all = explode('#',$id_all);
                        $promoid .= $promoid_all[0].',';
                        $promoval .= $promoid_all[1].',';
                    }
                }
            }

            $each_promoid = explode(',',trim($promoid,","));
            $each_promoval = explode(',',trim($promoval,","));
            $total_count = mb_substr_count($promoid,',');

            $id_loop = 500;

            $final_total_promotion = 0;
            for($promoid_loop=0; $promoid_loop<$total_count; $promoid_loop++) {

                $promotionid = '';
                $promoestval = 0;
                if(isset($each_promoid[$promoid_loop])) {
                    $promotionid = $each_promoid[$promoid_loop];
                }

                if(isset($each_promoval[$promoid_loop])) {
                    $promoestval = $each_promoval[$promoid_loop];
                }
                $final_total_promotion += $promoestval;

                if($promotionid != '') {

                    $promotion = explode('**', $get_rc['promotion']);
                    $rc_price = 0;
                    foreach($promotion as $pp){
                        if (strpos('#'.$pp, '#'.$promotionid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>
                <div class="form-group clearfix" id="<?php echo 'promotion_'.$id_loop; ?>" >
                <?php if (strpos($base_field_config, ','."Promotion Service Type".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Service Type:</label>
                    <select onChange='selectPromotionService(this)' data-placeholder="Choose a Type..." id="<?php echo 'promotionservice_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(service_type) FROM promotion WHERE deleted=0 AND DATE(expiry_date) >= DATE(NOW())");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_promotion($dbc, $promotionid, 'service_type') == $row['service_type']) {
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

                <?php if (strpos($base_field_config, ','."Promotion Category".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectPromotionCat(this)' data-placeholder="Choose a Category..." id="<?php echo 'promotioncategory_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM promotion WHERE deleted=0 AND DATE(expiry_date) >= DATE(NOW())");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_promotion($dbc, $promotionid, 'category') == $row['category']) {
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
                    <select onChange='selectPromotionHeading(this)' data-placeholder="Choose a Heading..." id="<?php echo 'promotionheading_'.$id_loop; ?>" name="promotionid[]" class="chosen-select-deselect form-control promotion_head" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT promotionid, heading FROM promotion WHERE deleted=0 AND DATE(expiry_date) >= DATE(NOW())");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_promotion($dbc, $promotionid, 'heading') == $row['heading']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['promotionid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>
                </div>

                <?php if (strpos($field_config, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="promotionfrp[]" value="<?php echo get_promotion($dbc, $promotionid, 'final_retail_price');?>"  id="<?php echo 'promotionfrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="promotionap[]" value="<?php echo get_promotion($dbc, $promotionid, 'admin_price');?>"  id="<?php echo 'promotionap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="promotionwp[]" value="<?php echo get_promotion($dbc, $promotionid, 'wholesale_price');?>" id="<?php echo 'promotionwp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="promotioncomp[]" value="<?php echo get_promotion($dbc, $promotionid, 'commercial_price');?>" id="<?php echo 'promotioncomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="promotioncp[]" value="<?php echo get_promotion($dbc, $promotionid, 'client_price');?>" id="<?php echo 'promotioncp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="promotionmsrp[]" value="<?php echo get_promotion($dbc, $promotionid, 'msrp');?>" id="<?php echo 'promotionmsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="promotionfinalprice[]" value="<?php echo $rc_price; ?>" readonly id="promotionfinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="promotionprojectprice[]" value="<?php echo $promoestval; ?>" onchange="countPromotion()" id="<?php echo 'promotionest_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'promotion_','promotionheading_'); return false;" id="<?php echo 'deletepromotion_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
        <?php  $id_loop++;
                }
            }
        } ?>

        <div class="additional_promotion clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="promotion_0">

                <?php if (strpos($base_field_config, ','."Promotion Service Type".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Service Type:</label>
                    <select onChange='selectPromotionService(this)' data-placeholder="Choose a Type..." id="promotionservice_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(service_type) FROM promotion WHERE deleted=0 AND DATE(expiry_date) >= DATE(NOW())");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['service_type']."'>".$row['service_type'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <?php if (strpos($base_field_config, ','."Promotion Category".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectPromotionCat(this)' data-placeholder="Choose a Category..." id="promotioncategory_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM promotion WHERE deleted=0 AND DATE(expiry_date) >= DATE(NOW())");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';

                        }
                        ?>
                    </select>
                </div>
                <?php } ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select onChange='selectPromotionHeading(this)' data-placeholder="Choose a Heading..." id="promotionheading_0" name="promotionid[]" class="chosen-select-deselect form-control promotion_head" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT promotionid, heading FROM promotion WHERE deleted=0 AND DATE(expiry_date) >= DATE(NOW())");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['promotionid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>
                </div>

                <?php if (strpos($field_config, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="promotionfrp[]" readonly id="promotionfrp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="promotionap[]" readonly id="promotionap_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="promotionwp[]" readonly id="promotionwp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="promotioncomp[]" id="promotioncomp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="promotioncp[]" readonly id="promotioncp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="promotionmsrp[]" readonly id="promotionmsrp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="promotionfinalprice[]" readonly id="promotionfinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="promotionprojectprice[]" id="promotionest_0"  onchange="countPromotion()" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'promotion_','promotionheading_'); return false;" id="deletepromotion_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_promotion"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_promotion" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="promotion_budget" value="<?php echo $budget_price[1]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="promotion_total" value="<?php echo $final_total_promotion;?>" type="text" class="form-control">
    </div>
</div>