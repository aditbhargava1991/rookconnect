<script>
$(document).ready(function() {
	//Services
    var add_new_l = 1;
    $('#deletelabour_0').hide();
    $('#add_row_l').on( 'click', function () {
        $('#deletelabour_0').show();
        var clone = $('.additional_l').clone();
        clone.find('.form-control').val('');

        clone.find('#labour_0').attr('id', 'labour_'+add_new_l);
        clone.find('#lheading_0').attr('id', 'lheading_'+add_new_l);
		clone.find('#lhr_0').attr('id', 'lhr_'+add_new_l);
		clone.find('#lfinalprice_0').attr('id', 'lfinalprice_'+add_new_l);
		clone.find('#lprojectprice_0').attr('id', 'lprojectprice_'+add_new_l);
		clone.find('#lprojectqty_0').attr('id', 'lprojectqty_'+add_new_l);
		clone.find('#lprojecttotal_0').attr('id', 'lprojecttotal_'+add_new_l);

        clone.find('#labourfull_0').attr('id', 'labourfull_'+add_new_l);
        clone.find('#deletelabour_0').attr('id', 'deletelabour_'+add_new_l);
        $('#deletelabour_0').hide();

        clone.removeClass("additional_l");
        $('#add_here_new_l').append(clone);

        resetChosen($("#labour_"+add_new_l));
        resetChosen($("#lheading_"+add_new_l));

        add_new_l++;

        return false;
    });
});

//Services
function selectLabour(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=labour_type_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#lheading_"+arr[1]).html(response);
			$("#lheading_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectLabourHeading(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=l_head_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*');
            $("#lhr_"+arr[1]).val(result[0]);
			$("#lfinalprice_"+arr[1]).val(result[1]);
		}
	});
}
function countLabour(txb) {
    if(txb != 'delete') {
        var get_id = txb.id;

        var split_id = get_id.split('_');

        document.getElementById('lprojecttotal_'+split_id[1]).value = parseFloat($('#lprojectprice_'+split_id[1]).val() * $('#lprojectqty_'+split_id[1]).val());
    }

    var sum_fee = 0;
    $('[name="lprojecttotal[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="labour_total"]').val(round2Fixed(sum_fee));
    $('[name="labour_summary"]').val(round2Fixed(sum_fee));

    var labour_budget = $('[name="labour_budget"]').val();
    if(labour_budget >= sum_fee) {
        $('[name="labour_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="labour_total"]').css("background-color", "#ff9999"); // Green
    }
}
</script>
<?php
$get_field_config_labour = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT labour FROM field_config"));
$field_config_labour = ','.$get_field_config_labour['labour'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <?php if (strpos($base_field_config, ','."Labour Type".',') !== FALSE) { ?>
            <label class="col-sm-2 text-center">Labour Type</label>
            <?php } ?>
            <label class="col-sm-2 text-center">Heading</label>
            <?php if (strpos($field_config_labour, ','."Hourly Rate".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Hourly Rate</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</label>
            <label class="col-sm-1 text-center">Hours</label>
            <label class="col-sm-1 text-center">Total</label>
        </div>

       <?php
        $get_labour = '';
        if(!empty($_GET['pid'])) {
            $pid = $_GET['pid'];
            $each_pid = explode(',',$pid);

            foreach($each_pid as $key_pid) {
                $each_item =	rtrim(get_package($dbc, $key_pid, 'assign_labour'),'**#**');
                $get_labour  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['promoid'])) {
            $promoid = $_GET['promoid'];
            $each_promoid = explode(',',$promoid);

            foreach($each_promoid as $key_promoid) {
                $each_item =	rtrim(get_promotion($dbc, $key_promoid, 'assign_labour'),'**#**');
                $get_labour  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['cid'])) {
            $cid = $_GET['cid'];
            $each_cid = explode(',',$cid);

            foreach($each_cid as $key_cid) {
                $each_item =	rtrim(get_custom($dbc, $key_cid, 'assign_labour'),'**#**');
                $get_labour  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['projectid'])) {
            $labour = $get_contact['labour'];
            $each_data = explode('**',$labour);
            foreach($each_data as $id_all) {
                if($id_all != '') {
                    $data_all = explode('#',$id_all);
                    $get_labour .= '**'.$data_all[0].'#'.$data_all[2].'#'.$data_all[1];
                }
            }
        }
        $final_total_labour = 0;
        ?>

        <?php if(!empty($get_labour)) {
            $each_assign_inventory = explode('**',$get_labour);
            $total_count = mb_substr_count($get_labour,'**');
            $id_loop = 500;
            for($inventory_loop=0; $inventory_loop<=$total_count; $inventory_loop++) {

                $each_item = explode('#',$each_assign_inventory[$inventory_loop]);
                $labourid = '';
                $qty = '';
                $est = '';
                if(isset($each_item[0])) {
                    $labourid = $each_item[0];
                }
                if(isset($each_item[1])) {
                    $qty = $each_item[1];
                }
                if(isset($each_item[2])) {
                    $est = $each_item[2];
                }
                $total = $qty*$est;
                $final_total_labour += $total;

                if($labourid != '') {
                    $labour = explode('**', $get_rc['labour']);
                    $rc_price = 0;
                    foreach($labour as $pp){
                        if (strpos('#'.$pp, '#'.$labourid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>

            <div class="form-group clearfix" id="<?php echo 'labourfull_'.$id_loop; ?>">
                <?php if (strpos($base_field_config, ','."Labour Type".',') !== FALSE) { ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Labour Type:</label>
                    <select onChange='selectLabour(this)' data-placeholder="Choose a Labour Type..." id="<?php echo 'labour_'.$id_loop; ?>" class="chosen-select-deselect form-control labourid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(labour_type) FROM labour WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_labour($dbc, $labourid, 'labour_type') == $row['labour_type']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['labour_type']."'>".$row['labour_type'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select onChange='selectLabourHeading(this)' data-placeholder="Choose a Heading..." id="<?php echo 'lheading_'.$id_loop; ?>" name="labourid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT labourid, heading FROM labour WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if ($labourid == $row['labourid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['labourid']."'>".$row['heading'].'</option>';
                        }
                        ?>
                    </select>
                </div>

                <?php if (strpos($field_config_labour, ','."Hourly Rate".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Hourly Rate:</label>
                    <input name="lhr[]" value="<?php echo get_labour($dbc, $labourid, 'hourly_rate');?>" id="<?php echo 'lhr_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="lfinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'lfinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="lprojectprice[]" value="<?php echo $est; ?>" id="<?php echo 'lprojectprice_'.$id_loop; ?>" onchange="countLabour(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Hours:</label>
                    <input name="lprojectqty[]" value="<?php echo $qty; ?>" id="<?php echo 'lprojectqty_'.$id_loop; ?>"  onchange="countLabour(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="lprojecttotal[]" value="<?php echo $total; ?>" id="<?php echo 'lprojecttotal_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'labourfull_','lheading_'); return false;" id="<?php echo 'deletelabour_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
            <?php  $id_loop++;
                    }
                }
            } ?>

        <div class="additional_l clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="labourfull_0">
                <?php if (strpos($base_field_config, ','."Labour Type".',') !== FALSE) { ?>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Labour Type:</label>
                    <select onChange='selectLabour(this)' data-placeholder="Choose a Labour Type..." id="labour_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(labour_type) FROM labour WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['labour_type']."'>".$row['labour_type'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select onChange='selectLabourHeading(this)' data-placeholder="Choose a Heading..." id="lheading_0" name="labourid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT labourid, heading FROM labour WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['labourid']."'>".$row['heading'].'</option>';
                        }
                        ?>
                    </select>
                </div>

                <?php if (strpos($field_config_labour, ','."Hourly Rate".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Hourly Rate:</label>
                    <input name="lhr[]" id="lhr_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="lfinalprice[]" readonly id="lfinalprice_0" type="text" class="form-control" />
                </div>

                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="lprojectprice[]" id='lprojectprice_0' onchange="countLabour(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Hours:</label>
                    <input name="lprojectqty[]" id='lprojectqty_0' onchange="countLabour(this)" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
                    <input name="lprojecttotal[]" id='lprojecttotal_0' type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'labourfull_','lheading_'); return false;" id="deletelabour_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_l"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_l" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="labour_budget" value="<?php echo $budget_price[13]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="labour_total" value="<?php echo $final_total_labour;?>" type="text" class="form-control">
    </div>
</div>