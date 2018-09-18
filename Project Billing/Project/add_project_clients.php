<script>
$(document).ready(function() {
	//Clients
	var add_new_cl = 1;
    $('#deleteclients_0').hide();
    $('#add_row_cl').on( 'click', function () {
        $('#deleteclients_0').show();
        var clone = $('.additional_cl').clone();
        clone.find('.form-control').val('');

        clone.find('#clclientid_0').attr('id', 'clclientid_'+add_new_cl);
		clone.find('#clclientperson_0').attr('id', 'clclientperson_'+add_new_cl);
        clone.find('#clrp_0').attr('id', 'clrp_'+add_new_cl);
        clone.find('#clap_0').attr('id', 'clap_'+add_new_cl);
        clone.find('#clwp_0').attr('id', 'clwp_'+add_new_cl);
        clone.find('#clcomp_0').attr('id', 'clcomp_'+add_new_cl);
        clone.find('#clcp_0').attr('id', 'clcp_'+add_new_cl);
        clone.find('#clmsrp_0').attr('id', 'clmsrp_'+add_new_cl);
		clone.find('#clfinalprice_0').attr('id', 'clfinalprice_'+add_new_cl);
        clone.find('#clientest_0').attr('id', 'clientest_'+add_new_cl);

        clone.find('#clients_0').attr('id', 'clients_'+add_new_cl);
        clone.find('#deleteclients_0').attr('id', 'deleteclients_'+add_new_cl);
        $('#deleteclients_0').hide();

        clone.removeClass("additional_cl");
        $('#add_here_new_cl').append(clone);

        resetChosen($("#clclientid_"+add_new_cl));
        resetChosen($("#clclientperson_"+add_new_cl));

        add_new_cl++;

        return false;
    });
});
//Clients
function selectClientName(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	var ratecardid = $("#hidden_ratecardid").val();

	$.ajax({
		type: "GET",
		url: "project_ajax_all.php?fill=cl_config&value="+stage+"&ratecardid="+ratecardid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('*');
            $("#clrp_"+arr[1]).val(result[0]);
            $("#clap_"+arr[1]).val(result[1]);
            $("#clwp_"+arr[1]).val(result[2]);
            $("#clcomp_"+arr[1]).val(result[3]);
            $("#clcp_"+arr[1]).val(result[4]);
            $("#clmsrp_"+arr[1]).val(result[5]);
			$("#clfinalprice_"+arr[1]).val(result[6]);

            $("#clclientid_"+arr[1]).html(result[7]);
			$("#clclientid_"+arr[1]).trigger("change.select2");
            $("#clclientperson_"+arr[1]).html(result[8]);
			$("#clclientperson_"+arr[1]).trigger("change.select2");
		}
	});
}
function countClient() {
    var sum_fee = 0;
    $('[name="clprojectprice[]"]').each(function () {
        sum_fee += +$(this).val() || 0;
    });

    $('[name="client_total"]').val(round2Fixed(sum_fee));
    $('[name="client_summary"]').val(round2Fixed(sum_fee));

    var client_budget = $('[name="client_budget"]').val();
    if(client_budget >= sum_fee) {
        $('[name="client_total"]').css("background-color", "#9CBA7F"); // Red
    } else {
        $('[name="client_total"]').css("background-color", "#ff9999"); // Green
    }
}
</script>
<?php
$get_field_config_clients = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT clients FROM field_config_contact"));
$field_config_clients = ','.$get_field_config_clients['clients'].',';
?>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group clearfix hide-titles-mob">
            <label class="col-sm-2 text-center">Client</label>
            <label class="col-sm-2 text-center">Contact Person</label>
            <?php if (strpos($field_config_clients, ','."Final Retail Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Final Retail Price</label>
            <?php } ?>
            <?php if (strpos($field_config_clients, ','."Admin Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Admin Price</label>
            <?php } ?>
            <?php if (strpos($field_config_clients, ','."Wholesale Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Wholesale Price</label>
            <?php } ?>
            <?php if (strpos($field_config_clients, ','."Commercial Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commercial Price</label>
            <?php } ?>
            <?php if (strpos($field_config_clients, ','."Client Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Client Price</label>
            <?php } ?>
            <?php if (strpos($field_config_clients, ','."MSRP".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">MSRP</label>
            <?php } ?>
            <label class="col-sm-1 text-center">Rate Card Price</label>
            <label class="col-sm-1 text-center"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</label>
        </div>

        <?php
        $get_client = '';
        if(!empty($_GET['pid'])) {
            $pid = $_GET['pid'];
            $each_pid = explode(',',$pid);

            foreach($each_pid as $key_pid) {
                $each_item =	rtrim(get_package($dbc, $key_pid, 'assign_clients'),'**#**');
                $get_client  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['promoid'])) {
            $promoid = $_GET['promoid'];
            $each_promoid = explode(',',$promoid);

            foreach($each_promoid as $key_promoid) {
                $each_item =	rtrim(get_promotion($dbc, $key_promoid, 'assign_clients'),'**#**');
                $get_client  .= '**'.$each_item;
            }
        }
        if(!empty($_GET['cid'])) {
            $cid = $_GET['cid'];
            $each_cid = explode(',',$cid);

            foreach($each_cid as $key_cid) {
                $each_item =	rtrim(get_custom($dbc, $key_cid, 'assign_clients'),'**#**');
                $get_client  .= '**'.$each_item;
            }
        }

        if(!empty($_GET['projectid'])) {
            $client = $get_contact['client'];
            $each_data = explode('**',$client);
            foreach($each_data as $id_all) {
                if($id_all != '') {
                    $data_all = explode('#',$id_all);
                    $get_client .= '**'.$data_all[0].'#'.$data_all[1];
                }
            }
        }
        $final_total_clients = 0;
        ?>

        <?php if(!empty($get_client)) {
            $each_assign_inventory = explode('**',$get_client);
            $total_count = mb_substr_count($get_client,'**');
            $id_loop = 500;
            for($inventory_loop=0; $inventory_loop<=$total_count; $inventory_loop++) {

                $each_item = explode('#',$each_assign_inventory[$inventory_loop]);
                $contactid = '';
                $est = '';
                if(isset($each_item[0])) {
                    $contactid = $each_item[0];
                }
                if(isset($each_item[1])) {
                    $est = $each_item[1];
                }
                $final_total_clients += $est;
                if($contactid != '') {
                    $client = explode('**', $get_rc['client']);
                    $rc_price = 0;
                    foreach($client as $pp){
                        if (strpos('#'.$pp, '#'.$contactid.'#') !== false) {
                            $rate_card_price = explode('#', $pp);
                            $rc_price = $rate_card_price[1];
                        }
                    }
            ?>
            <div class="form-group clearfix" id="<?php echo 'clients_'.$id_loop; ?>" >
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Client:</label>
                    <select onChange='selectClientName(this)' data-placeholder="Choose a Client..." id="<?php echo 'clclientid_'.$id_loop; ?>" name="clientid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Client' AND deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if ($contactid == $row['contactid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Contact Person:</label>
                    <select onChange='selectClientName(this)' data-placeholder="Choose a Client..." id="<?php echo 'clclientperson_'.$id_loop; ?>" name="clientperson[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Client' AND deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if ($contactid == $row['contactid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php if (strpos($field_config_clients, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="clrp[]" value="<?php echo get_contact($dbc, $contactid, 'final_retail_price');?>" id="<?php echo 'clrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_clients, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="clap[]" value="<?php echo get_contact($dbc, $contactid, 'admin_price');?>" id="<?php echo 'clap_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_clients, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="clwp[]" value="<?php echo get_contact($dbc, $contactid, 'wholesale_price');?>" id="<?php echo 'clwp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_clients, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="clcomp[]" value="<?php echo get_contact($dbc, $contactid, 'commercial_price');?>" id="<?php echo 'clcomp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_clients, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="clcp[]" value="<?php echo get_contact($dbc, $contactid, 'client_price');?>" id="<?php echo 'clcp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_clients, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="clmsrp[]" value="<?php echo get_contact($dbc, $contactid, 'msrp');?>" id="<?php echo 'clmsrp_'.$id_loop; ?>" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="clfinalprice[]" value="<?php echo $rc_price; ?>" readonly id="<?php echo 'clfinalprice_'.$id_loop; ?>" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="clprojectprice[]" id="<?php echo 'clientest_'.$id_loop; ?>"  value="<?php echo $est; ?>" onchange="countClient()" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'clients_','clclientid_'); return false;" id="<?php echo 'deleteclients_'.$id_loop; ?>" class="btn brand-btn">Delete</a>
                </div>
            </div>
            <?php  $id_loop++;
                    }
                }
            } ?>

        <div class="additional_cl clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix" id="clients_0">
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Client:</label>
                    <select onChange='selectClientName(this)' data-placeholder="Choose a Client..." id="clclientid_0" name="clientid[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Client' AND deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Contact Person:</label>
                    <select onChange='selectClientName(this)' data-placeholder="Choose a Contact..." id="clclientperson_0" name="clientperson[]" class="chosen-select-deselect form-control equipmentid" width="380">
                        <option value=''></option>
						<?php
							$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Client' AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
							foreach($query as $id) {
								$selected = '';
								echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
							}
						  ?>
                    </select>
                </div>
                <?php if (strpos($field_config_clients, ','."Final Retail Price".',') !== FALSE) { ?>
                <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Final Retail Price:</label>
                    <input name="clrp[]" id="clrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_clients, ','."Admin Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Admin Price:</label>
                    <input name="clap[]" id="clap_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_clients, ','."Wholesale Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Wholesale Price:</label>
                    <input name="clwp[]" id="clwp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_clients, ','."Commercial Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Commercial Price:</label>
                    <input name="clcomp[]" id="clcomp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_clients, ','."Client Price".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Client Price:</label>
                    <input name="clcp[]" id="clcp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <?php if (strpos($field_config_clients, ','."MSRP".',') !== FALSE) { ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">MSRP:</label>
                    <input name="clmsrp[]" id="clmsrp_0" readonly type="text" class="form-control" />
                </div>
                <?php } ?>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Rate Card Price:</label>
                    <input name="clfinalprice[]" readonly id="clfinalprice_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" ><label for="company_name" class="col-sm-4 show-on-mob control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price:</label>
                    <input name="clprojectprice[]" onchange="countClient()" id="clientest_0" type="text" class="form-control" />
                </div>
                <div class="col-sm-1" >
                    <a href="#" onclick="deleteProject(this,'clients_','clclientid_'); return false;" id="deleteclients_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

        </div>

        <div id="add_here_new_cl"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_cl" class="btn brand-btn pull-left">Add Row</button>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Budget:</label>
    <div class="col-sm-8">
      <input name="client_budget" value="<?php echo $budget_price[6]; ?>" type="text" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Total Applied:</label>
    <div class="col-sm-8">
      <input name="client_total" value="<?php echo $final_total_clients;?>" type="text" class="form-control">
    </div>
</div>
