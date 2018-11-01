
<style>
.col-sm-2, .col-sm-1, .col-sm-4 {
	padding-left:5px !important;
	padding-right:5px !important;
	word-break: break-word;
}
.chosen-container {
	width:100% !important;
    min-width: 1px !important;
}

@media(max-width:767px) {
.hide-titles-mob {
	display:none;
}
.show-on-mob {
	display:inline-block;
}
}
@media(min-width:768px) {
.show-on-mob {
	display:none;
}	
	
}
</style>
<script>
$(document).ready(function() {
    var add_new_package = 1;
    $('#deletepackage_0').hide();
    $('#add_row_package').on( 'click', function () {
        $('#deletepackage_0').show();
        var clone = $('.additional_package').clone();
        clone.find('.form-control').val('');

        clone.find('#packageservice_0').attr('id', 'packageservice_'+add_new_package);
		clone.find('#packagecategory_0').attr('id', 'packagecategory_'+add_new_package);
        clone.find('#packageheading_0').attr('id', 'packageheading_'+add_new_package);
        clone.find('#packagefrp_0').attr('id', 'packagefrp_'+add_new_package);
        clone.find('#packageap_0').attr('id', 'packageap_'+add_new_package);
        clone.find('#packagewp_0').attr('id', 'packagewp_'+add_new_package);
        clone.find('#packagecomp_0').attr('id', 'packagecomp_'+add_new_package);
        clone.find('#packagecp_0').attr('id', 'packagecp_'+add_new_package);
        clone.find('#packagemsrp_0').attr('id', 'packagemsrp_'+add_new_package);
		clone.find('#packagefinalprice_0').attr('id', 'packagefinalprice_'+add_new_package);
        clone.find('#packageview_0').attr('id', 'packageview_'+add_new_package);
        clone.find('#packageest_0').attr('id', 'packageest_'+add_new_package);

        clone.find('#package_0').attr('id', 'package_'+add_new_package);
        clone.find('#deletepackage_0').attr('id', 'deletepackage_'+add_new_package);
        $('#deletepackage_0').hide();

        clone.removeClass("additional_package");
        $('#add_here_new_package').append(clone);

        resetChosen($("#packageservice_"+add_new_package));

        resetChosen($("#packagecategory_"+add_new_package));

        resetChosen($("#packageheading_"+add_new_package));

        add_new_package++;

        return false;
    });
});

//Packages & Promotions
function selectPackageService(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=package_service_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            alert('If you choose any package then all data you inserted will gone.');
            $("#packagecategory_"+arr[1]).html(response);
			$("#packagecategory_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectPackageCat(sel) {
	var stage = encodeURIComponent(sel.value);
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=package_cat_config&value="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#packageheading_"+arr[1]).html(response);
			$("#packageheading_"+arr[1]).trigger("change.select2");
		}
	});
}

function selectPackageHeading(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=package_head_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*');
            $("#packagefrp_"+arr[1]).val(result[0]);
            $("#packageap_"+arr[1]).val(result[1]);
            $("#packagewp_"+arr[1]).val(result[2]);
            $("#packagecomp_"+arr[1]).val(result[3]);
            $("#packagecp_"+arr[1]).val(result[4]);
            $("#packagemsrp_"+arr[1]).val(result[5]);
			$("#packagefinalprice_"+arr[1]).val(result[6]);

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

function countPackage() {
    var sum_fee = 0;
    $('[name="packageprojectprice[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="package_total"]').val(round2Fixed(sum_fee));
    $('[name="package_summary"]').val(round2Fixed(sum_fee));

    var package_budget = $('[name="package_budget"]').val();
    if(package_budget >= sum_fee) {
        $('[name="package_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="package_total"]').css("background-color", "#ff9999"); // Green
    }

}
</script>

<?php
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT package FROM field_config"));
$field_config = ','.$get_field_config['package'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <?php if (strpos($base_field_config, ','."Package Service Type".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Service Type</label>
            <?php } ?>
            <?php if (strpos($base_field_config, ','."Package Category".',') !== FALSE) { ?>
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

        <?php if((!empty($_GET['pid'])) || (!empty($_GET['projectid']))) {
            $pid = '';
            $pval = '';
            if(!empty($_GET['pid'])) {
                $pid = $_GET['pid'];
            }

            if(!empty($_GET['projectid'])) {
                $package = $get_contact['package'];
                $each_pid = explode('**',$package);
                $pid .= ',';
                foreach($each_pid as $id_all) {
                    if($id_all != '') {
                        $pid_all = explode('#',$id_all);
                        $pid .= $pid_all[0].',';
                        $pval .= $pid_all[1].',';
                    }
                }
            }

            $each_pid = explode(',',trim($pid,","));
            $each_pval = explode(',',trim($pval,","));
            $total_count = mb_substr_count($pid,',');

            $id_loop = 500;

            $final_total_package = 0;
            for($pid_loop=0; $pid_loop<$total_count; $pid_loop++) {

                $packageid = '';
                $pestval = 0;
                if(isset($each_pid[$pid_loop])) {
                    $packageid = $each_pid[$pid_loop];
                }
                if(isset($each_pval[$pid_loop])) {
                    $pestval = $each_pval[$pid_loop];
                }
                $final_total_package += $pestval;

                if($packageid != '') {
                    $package = explode('**', $get_rc['package']);
                    $rc_price = 0;
                    foreach($package as $pp) {
                        if (strpos('#'.$pp, '#'.$packageid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>
                <div class="form-group clearfix" id="<?php echo 'package_'.$id_loop; ?>" >
                <?php if (strpos($base_field_config, ','."Package Service Type".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Service Type:</label>
                    <select onChange='selectPackageService(this)' data-placeholder="Choose a Type..." id="<?php echo 'packageservice_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(service_type) FROM package WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_package($dbc, $packageid, 'service_type') == $row['service_type']) {
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

                <?php if (strpos($base_field_config, ','."Package Category".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectPackageCat(this)' data-placeholder="Choose a Category..." id="<?php echo 'packagecategory_'.$id_loop; ?>" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM package WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_package($dbc, $packageid, 'category') == $row['category']) {
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
                    <select onChange='selectPackageHeading(this)' data-placeholder="Choose a Heading..." id="<?php echo 'packageheading_'.$id_loop; ?>" name="packageid[]" class="chosen-select-deselect form-control package_head" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT packageid, heading FROM package WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if (get_package($dbc, $packageid, 'heading') == $row['heading']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['packageid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>
                </div>

                <?php if (strpos($field_config, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="packagefrp[]" value="<?php echo get_package($dbc, $packageid, 'final_retail_price');?>"  id="<?php echo 'packagefrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="packageap[]" value="<?php echo get_package($dbc, $packageid, 'admin_price');?>"  id="<?php echo 'packageap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="packagewp[]" value="<?php echo get_package($dbc, $packageid, 'wholesale_price');?>" id="<?php echo 'packagewp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="packagecomp[]" value="<?php echo get_package($dbc, $packageid, 'commercial_price');?>" id="<?php echo 'packagecomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="packagecp[]" value="<?php echo get_package($dbc, $packageid, 'client_price');?>" id="<?php echo 'packagecp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="packagemsrp[]" value="<?php echo get_package($dbc, $packageid, 'msrp');?>" id="<?php echo 'packagemsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="packagefinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'packagefinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="packageprojectprice[]"  value="<?php echo $pestval; ?>"  onchange="countPackage()" type="text" id="<?php echo 'packageest_'.$id_loop; ?>" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'package_','packageheading_'); return false;" id="<?php echo 'deletepackage_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
        <?php  $id_loop++;
                }
            }
        } ?>

        <div class="additional_package clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="package_0">

                <?php if (strpos($base_field_config, ','."Package Service Type".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Service Type:</label>
                    <select onChange='selectPackageService(this)' data-placeholder="Choose a Type..." id="packageservice_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(service_type) FROM package WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['service_type']."'>".$row['service_type'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <?php if (strpos($base_field_config, ','."Package Category".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select onChange='selectPackageCat(this)' data-placeholder="Choose a Category..." id="packagecategory_0" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(category) FROM package WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';

                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select onChange='selectPackageHeading(this)' data-placeholder="Choose a Heading..." id="packageheading_0" name="packageid[]" class="chosen-select-deselect form-control package_head" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT packageid, heading FROM package WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['packageid']."'>".$row['heading'].'</option>';

                        }
                        ?>
                    </select>
                </div>

                <?php if (strpos($field_config, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="packagefrp[]" readonly id="packagefrp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="packageap[]" readonly id="packageap_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="packagewp[]" readonly id="packagewp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="packagecomp[]" id="packagecomp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="packagecp[]" readonly id="packagecp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="packagemsrp[]" readonly id="packagemsrp_0" type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="packagefinalprice[]" readonly id="packagefinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="packageprojectprice[]" id="packageest_0" onchange="countPackage()" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'package_','packageheading_'); return false;" id="deletepackage_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_package"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_package" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="package_budget" value="<?php echo $budget_price[0]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="package_total" value="<?php echo $final_total_package;?>" type="text" class="form-control">
    </div>
</div>