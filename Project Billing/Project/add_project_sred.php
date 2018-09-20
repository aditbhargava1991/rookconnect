<script>
$(document).ready(function() {
	//Services
    var add_new_sred = 1;
    $('#deletesred_0').hide();
    $('#add_row_sred').on( 'click', function () {
        $('#deletesred_0').show();
        var clone = $('.additional_sred').clone();
        clone.find('.form-control').val('');

        clone.find('#sredservice_0').attr('id', 'sredservice_'+add_new_sred);
		clone.find('#sredcategory_0').attr('id', 'sredcategory_'+add_new_sred);
        clone.find('#sredheading_0').attr('id', 'sredheading_'+add_new_sred);
        clone.find('#sredfrp_0').attr('id', 'sredfrp_'+add_new_sred);
        clone.find('#sredap_0').attr('id', 'sredap_'+add_new_sred);
        clone.find('#sredwp_0').attr('id', 'sredwp_'+add_new_sred);
        clone.find('#sredcomp_0').attr('id', 'sredcomp_'+add_new_sred);
        clone.find('#sredcp_0').attr('id', 'sredcp_'+add_new_sred);
        clone.find('#sredmsrp_0').attr('id', 'sredmsrp_'+add_new_sred);
		clone.find('#sredfinalprice_0').attr('id', 'sredfinalprice_'+add_new_sred);
		clone.find('#sredprojectprice_0').attr('id', 'sredprojectprice_'+add_new_sred);
		clone.find('#sredprojectqty_0').attr('id', 'sredprojectqty_'+add_new_sred);
		clone.find('#sredprojecttotal_0').attr('id', 'sredprojecttotal_'+add_new_sred);

        clone.find('#sred_0').attr('id', 'sred_'+add_new_sred);
        clone.find('#deletesred_0').attr('id', 'deletesred_'+add_new_sred);
        $('#deletesred_0').hide();

        clone.removeClass("additional_sred");
        $('#add_here_new_sred').append(clone);

        resetChosen($("#sredservice_"+add_new_sred));
        resetChosen($("#sredcategory_"+add_new_sred));
        resetChosen($("#sredheading_"+add_new_sred));

        add_new_sred++;

        return false;
    });
});
//sred
function selectSrEd(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=sred_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#sredcategory_"+arr[1]).html(response);
			$("#sredcategory_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectSrEdCat(sel) {
	var stage = encodeURIComponent(sel.value);
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=sred_cat_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#sredheading_"+arr[1]).html(response);
			$("#sredheading_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectSrEdHeading(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=sred_head_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*');
            $("#sredfrp_"+arr[1]).val(result[0]);
            $("#sredap_"+arr[1]).val(result[1]);
            $("#sredwp_"+arr[1]).val(result[2]);
            $("#sredcomp_"+arr[1]).val(result[3]);
            $("#sredcp_"+arr[1]).val(result[4]);
            $("#sredmsrp_"+arr[1]).val(result[5]);
			$("#sredfinalprice_"+arr[1]).val(result[6]);

		}
	});
}
function countSrEd(txb) {
    if(txb != 'delete') {
        var get_id = txb.id;

        var split_id = get_id.split('_');

        document.getElementById('sredprojecttotal_'+split_id[1]).value = parseFloat($('#sredprojectprice_'+split_id[1]).val() * $('#sredprojectqty_'+split_id[1]).val());
    }

    var sum_fee = 0;
    $('[name="sredprojecttotal[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="sred_total"]').val(round2Fixed(sum_fee));
    $('[name="sred_summary"]').val(round2Fixed(sum_fee));

    var sred_budget = $('[name="sred_budget"]').val();
    if(sred_budget >= sum_fee) {
        $('[name="sred_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="sred_total"]').css("background-color", "#ff9999"); // Green
    }
}

</script>
<?php
$get_field_config_sred = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT sred FROM field_config"));
$field_config_sred = ','.$get_field_config_sred['sred'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <?php if (strpos($base_field_config, ','."SRED SRED Type".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Service Type</label>
            <?php } ?>
            <?php if (strpos($base_field_config, ','."SRED Category".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Category</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Heading</label>
            <?php if (strpos($field_config_sred, ','."Final Retail Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Final Retail Price</label>
            <?php } ?>
            <?php if (strpos($field_config_sred, ','."Admin Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Admin Price</label>
            <?php } ?>
            <?php if (strpos($field_config_sred, ','."Wholesale Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Wholesale Price</label>
            <?php } ?>
            <?php if (strpos($field_config_sred, ','."Commercial Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commercial Price</label>
            <?php } ?>
            <?php if (strpos($field_config_sred, ','."Client Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Client Price</label>
            <?php } ?>
            <?php if (strpos($field_config_sred, ','."MSRP".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">MSRP</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</label>
            <label class="col-sm-1 text-center">Quantity</label>
            <label class="col-sm-1 text-center">Total</label>
        </div>

       <?php
        $get_sred = '';
        if(!empty($_GET['pid'])) {
            $pid = $_GET['pid'];
            $each_pid = explode(',',$pid);

            foreach($each_pid as $key_pid) {
                $each_item =	rtrim(get_package($dbc, $key_pid, 'assign_sred'),'**#**');
                $get_sred  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['promoid'])) {
            $promoid = $_GET['promoid'];
            $each_promoid = explode(',',$promoid);

            foreach($each_promoid as $key_promoid) {
                $each_item =	rtrim(get_promotion($dbc, $key_promoid, 'assign_sred'),'**#**');
                $get_sred  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['cid'])) {
            $cid = $_GET['cid'];
            $each_cid = explode(',',$cid);

            foreach($each_cid as $key_cid) {
                $each_item =	rtrim(get_custom($dbc, $key_cid, 'assign_sred'),'**#**');
                $get_sred  .= '**'.$each_item;
            }
        }

        if(!empty($_GET['projectid'])) {
            $sred = $get_contact['sred'];
            $each_sredid = explode('**',$sred);
            foreach($each_sredid as $id_all) {
                if($id_all != '') {
                    $sredid_all = explode('#',$id_all);
                    $get_sred .= '**'.$sredid_all[0].'#'.$sredid_all[2].'#'.$sredid_all[1];
                }
            }
        }
        $final_total_sred = 0;
        ?>

        <?php if(!empty($get_sred)) {
            $each_assign_inventory = explode('**',$get_sred);
            $total_count = mb_substr_count($get_sred,'**');
            $id_loop = 500;

            for($inventory_loop=0; $inventory_loop<=$total_count; $inventory_loop++) {
                $each_item = explode('#',$each_assign_inventory[$inventory_loop]);
                $sredid = '';
                $qty = '';
                $est = '';
                if(isset($each_item[0])) {
                    $sredid = $each_item[0];
                }
                if(isset($each_item[1])) {
                    $qty = $each_item[1];
                }
                if(isset($each_item[2])) {
                    $est = $each_item[2];
                }
                $total = $qty*$est;
                $final_total_sred += $total;
                if($sredid != '') {

                    $sred = explode('**', $get_rc['sred']);
                    $rc_price = 0;
                    foreach($sred as $pp){
                        if (strpos('#'.$pp, '#'.$sredid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>

            <div class="form-group clearfix" id="<?php echo 'sred_'.$id_loop; ?>" >
                <?php if (strpos($base_field_config, ','."SRED SRED Type".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Service Type:</label>
                    <select onChange='selectSrEd(this)' data-placeholder="Choose a Type..." id="<?php echo 'sredservice_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(sred_type) FROM sred WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_sred($dbc, $sredid, 'sred_type') == $row['sred_type']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['sred_type']."'>".$row['sred_type'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>
                <?php if (strpos($base_field_config, ','."SRED Category".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectSrEdCat(this)' data-placeholder="Choose a Category..." id="<?php echo 'sredcategory_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM sred WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_sred($dbc, $sredid, 'category') == $row['category']) {
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
                    <select onChange='selectSrEdHeading(this)' data-placeholder="Choose a Heading..." id="<?php echo 'sredheading_'.$id_loop; ?>" name="sredid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT sredid, heading FROM sred WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if ($sredid == $row['sredid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['sredid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>

                    <!-- <input name="sheading[]" readonly id="<?php echo 'sheading_'.$id_loop; ?>" type="text" class="form-control" /> -->
                </div>

                <?php if (strpos($field_config_sred, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="sredfrp[]" value="<?php echo get_sred($dbc, $sredid, 'final_retail_price');?>" id="<?php echo 'sredfrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_sred, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="sredap[]" value="<?php echo get_sred($dbc, $sredid, 'admin_price');?>" id="<?php echo 'sredap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_sred, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="sredwp[]" value="<?php echo get_sred($dbc, $sredid, 'wholesale_price');?>" id="<?php echo 'sredwp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_sred, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Contractor:</label>
                    <input name="sredcomp[]" value="<?php echo get_sred($dbc, $sredid, 'commercial_price');?>" id="<?php echo 'sredcomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_sred, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="sredcp[]" value="<?php echo get_sred($dbc, $sredid, 'client_price');?>" id="<?php echo 'sredcp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_sred, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="sredmsrp[]" value="<?php echo get_sred($dbc, $sredid, 'msrp');?>" id="<?php echo 'sredmsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="sredfinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'sredfinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="sredprojectprice[]" id="<?php echo 'sredprojectprice_'.$id_loop; ?>" onchange="countSrEd(this)" value="<?php echo $est; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
                    <input name="sredprojectqty[]" id="<?php echo 'sredprojectqty_'.$id_loop; ?>" onchange="countSrEd(this)" value="<?php echo $qty; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="sredprojecttotal[]" value="<?php echo $total; ?>" id="<?php echo 'sredprojecttotal_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'sred_','sredheading_'); return false;" id="<?php echo 'deletesred_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
            <?php  $id_loop++;
                    }
                }
            } ?>

        <div class="additional_sred clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="sred_0">
                <?php if (strpos($base_field_config, ','."SRED SRED Type".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Service Type:</label>
                    <select onChange='selectSrEd(this)' data-placeholder="Choose a Type..." id="sredservice_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(sred_type) FROM sred WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['sred_type']."'>".$row['sred_type'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <?php if (strpos($base_field_config, ','."SRED Category".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectSrEdCat(this)' data-placeholder="Choose a Category..." id="sredcategory_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM sred WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';

                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select onChange='selectSrEdHeading(this)' data-placeholder="Choose a Heading..." id="sredheading_0" name="sredid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT sredid, heading FROM sred WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['sredid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>

                    <!-- <input name="sheading[]" readonly id="sheading_0" type="text" class="form-control" /> -->
                </div>

                <?php if (strpos($field_config_sred, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="sredfrp[]" id="sredfrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_sred, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="sredap[]" id="sredap_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_sred, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="sredwp[]" id="sredwp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_sred, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="sredcomp[]" id="sredcomp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_sred, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="sredcp[]" id="sredcp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_sred, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="sredmsrp[]" id="sredmsrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="sredfinalprice[]" readonly id="sredfinalprice_0" type="text" class="form-control" />
                </div>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="sredprojectprice[]" id='sredprojectprice_0' onchange="countSrEd(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
                    <input name="sredprojectqty[]" id='sredprojectqty_0' onchange="countSrEd(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="sredprojecttotal[]" id='sredprojecttotal_0' type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'sred_','sredheading_'); return false;" id="deletesred_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_sred"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_sred" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="sred_budget" value="<?php echo $budget_price[15]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="sred_total" value="<?php echo $final_total_sred;?>" type="text" class="form-control">
    </div>
</div>