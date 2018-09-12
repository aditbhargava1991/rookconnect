<?php include_once('../include.php');
$strict_view = strictview_visible_function($dbc, 'inventory');
$tile_security = get_security($dbc, 'inventory');
if($strict_view > 0) {
    $tile_security['edit'] = 0;
    $tile_security['config'] = 0;
}

if (isset($_POST['submit'])) {
    $category = $_POST['new_category'];
    $bill_of_material = implode(',',$_POST['old_inventoryid']);
    $new_name = filter_var($_POST['new_name'],FILTER_SANITIZE_STRING);
    $sell_price = $_POST['newprice1'];
    $final_retail_price = $_POST['newprice2'];
    $unit_price = $_POST['newprice3'];
    $wholesale_price = $_POST['newprice4'];
    $commercial_price = $_POST['newprice5'];
    $client_price = $_POST['newprice6'];
    $preferred_price = $_POST['newprice7'];
    $admin_price = $_POST['newprice8'];
    $web_price = $_POST['newprice9'];
    $commission_price = $_POST['newprice10'];

    $query_insert_inventory = "INSERT INTO `inventory` (`bill_of_material`, `category`, `name`, `sell_price`, `final_retail_price`, `unit_price`, `wholesale_price`, `commercial_price`, `client_price`, `preferred_price`, `admin_price`, `web_price`, `commission_price`
    ) VALUES ('$bill_of_material', '$category', '$new_name', '$sell_price', '$final_retail_price', '$unit_price', '$wholesale_price', '$commercial_price', '$client_price', '$preferred_price', '$admin_price', '$web_price', '$commission_price')";
    $result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
	$new_inventoryid = mysqli_insert_id($dbc);
  $before_change = '';
  $history = "New inventory Added. <br />";
  add_update_history($dbc, 'inventory_history', $history, '', $before_change);
	$dater = date('Y/m/d h:i:s a', time());
	$contactid = $_SESSION['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
	while($row = mysqli_fetch_assoc($result)) {
		$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' (ID: '.$row['contactid'].')';
	}
	$query_insert_inventory = "INSERT INTO `bill_of_material_log` (`pieces_of_inventoryid`, `inventoryid`, `date_time`, `contact`, `type`, `deleted`
    ) VALUES ('$bill_of_material', '$new_inventoryid', '$dater', '$name', 'Add', '0')";
	$result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);

    $url = "inventory.php?category=".$category;
    echo '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';
} ?>
<script type="text/javascript">
$(document).ready(function() {
    var prodcount = 1;
	$('#add_position_buttonprod').on( 'click', function () {
        var clone = $('.additional_positionprod').clone();
        clone.find('.form-control').val('');

        clone.find('.prodname').attr('id', 'name_'+prodcount);
        clone.find('.prodcategory').attr('id', 'category_'+prodcount);
        clone.find('.prodprice1').attr('id', 'price1_'+prodcount);
        clone.find('.prodprice2').attr('id', 'price2_'+prodcount);
        clone.find('.prodprice3').attr('id', 'price3_'+prodcount);
        clone.find('.prodprice4').attr('id', 'price4_'+prodcount);
        clone.find('.prodprice5').attr('id', 'price5_'+prodcount);
        clone.find('.prodprice6').attr('id', 'price6_'+prodcount);
        clone.find('.prodprice7').attr('id', 'price7_'+prodcount);
        clone.find('.prodprice8').attr('id', 'price8_'+prodcount);
        clone.find('.prodprice9').attr('id', 'price9_'+prodcount);
        clone.find('.prodprice10').attr('id', 'price10_'+prodcount);

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_positionprod");
        $('#add_here_new_positionprod').append(clone);

		resetChosen($("#name_"+prodcount));
		resetChosen($("#category_"+prodcount));
        prodcount++;
        return false;
    });

	$('.iframe_open').click(function(){
	   $('.iframe_title').text('Bill of Material History');
	   $('.iframe_holder').show();
	   $('.hide_on_iframe').hide();
});

$('.close_iframer').click(function(){
	var result = confirm("Are you sure you want to close this window?");
	if (result) {
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	}
});

$(document).on('change', 'select[name="prodcategory[]"]', function() { prodselectCategory(this); });
$(document).on('change', 'select[name="old_inventoryid[]"]', function() { prodselectName(this); });

});
function prodselectCategory(sel) {
    var end = sel.value;
    var typeId = sel.id;
    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "inventory_ajax_all.php?fill=prodselectCategory&name="+end,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var arr = typeId.split('_');
            $("#name_"+arr[1]).html(response);
            $("#name_"+arr[1]).trigger("change.select2");
        }
    });
}

function prodselectName(sel) {
    var end = sel.value;
    var typeId = sel.id;

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "inventory_ajax_all.php?fill=prodselectName&name="+end,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('*#*');
            var arr = typeId.split('_');
            $("#price1_"+arr[1]).val(result[0]);
            $("#price2_"+arr[1]).val(result[1]);
            $("#price3_"+arr[1]).val(result[2]);
            $("#price4_"+arr[1]).val(result[3]);
            $("#price5_"+arr[1]).val(result[4]);
            $("#price6_"+arr[1]).val(result[5]);
            $("#price7_"+arr[1]).val(result[6]);
            $("#price8_"+arr[1]).val(result[7]);
            $("#price9_"+arr[1]).val(result[8]);
            $("#price10_"+arr[1]).val(result[9]);

            var total1 = 0;
            $('.prodprice1').each(function () {
                total1 += +$(this).val() || 0;
            });
            $('#final_prodprice1').val(round2Fixed(total1));

            var total2 = 0;
            $('.prodprice2').each(function () {
                total2 += +$(this).val() || 0;
            });
            $('#final_prodprice2').val(round2Fixed(total2));

            var total3 = 0;
            $('.prodprice3').each(function () {
                total3 += +$(this).val() || 0;
            });
            $('#final_prodprice3').val(round2Fixed(total3));

            var total4 = 0;
            $('.prodprice4').each(function () {
                total4 += +$(this).val() || 0;
            });
            $('#final_prodprice4').val(round2Fixed(total4));

            var total5 = 0;
            $('.prodprice5').each(function () {
                total5 += +$(this).val() || 0;
            });
            $('#final_prodprice5').val(round2Fixed(total5));

            var total6 = 0;
            $('.prodprice6').each(function () {
                total6 += +$(this).val() || 0;
            });
            $('#final_prodprice6').val(round2Fixed(total6));

            var total7 = 0;
            $('.prodprice7').each(function () {
                total7 += +$(this).val() || 0;
            });
            $('#final_prodprice7').val(round2Fixed(total7));

            var total8 = 0;
            $('.prodprice8').each(function () {
                total8 += +$(this).val() || 0;
            });
            $('#final_prodprice8').val(round2Fixed(total8));

            var total9 = 0;
            $('.prodprice9').each(function () {
                total9 += +$(this).val() || 0;
            });
            $('#final_prodprice9').val(round2Fixed(total9));

            var total10 = 0;
            $('.prodprice10').each(function () {
                total10 += +$(this).val() || 0;
            });
            $('#final_prodprice10').val(round2Fixed(total10));
        }
    });
}
</script>
<form name="form_sites" method="post" action="bill_of_material.php" class="form-horizontal" role="form">

    <?php if(!($strict_view > 0)) { ?>
        <div class="col-sm-12 gap-top pull-right">
            <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View all Bill of Material history."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a href="bill_of_material_history.php?type=log"><button type="button" class="btn brand-btn">History</button></a>
        </div>
    <?php } ?>
    <?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(inventory_dashboard SEPARATOR ',') AS all_inventory FROM field_config_inventory WHERE accordion IS NULL AND inventory IS NULL"));
        $value_config = ','.$get_field_config['all_inventory'].',';
    ?>



   <div class="form-group clearfix">
	<label class="col-sm-1 text-center"><h4>Product(s)</h4></label>
	</div>
	  <div class="form-group clearfix">
        <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
            <label class="col-sm-3 text-center">Tab</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
            <label class="col-sm-3 text-center">Name</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Sell Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Sell Price</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Final Retail Price</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Unit Price</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Wholesale Price</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commercial Price</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Client Price</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Preferred Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Preferred Price</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Admin Price</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Web Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Web Price</label>
        <?php } ?>
        <?php if (strpos($value_config, ','."Commission Price".',') !== FALSE) { ?>
            <label class="col-sm-1 text-center">Commission Price</label>
        <?php } ?>
    </div>

    <div class="additional_positionprod">
    <div class="clearfix"></div>
    <div class="form-group clearfix" id="prodservices_0" width="100%">

        <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
        <div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <select data-placeholder="Choose a Tab..."  id="category_0" name="prodcategory[]" class="chosen-select-deselect form-control prodcategory">
                <option value=""></option>
                <?php
                $query = mysqli_query($dbc,"SELECT DISTINCT(category) FROM inventory WHERE deleted=0 order by category");
                while($row = mysqli_fetch_array($query)) {
                    ?><option id='<?php echo $row['category'];?>' value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
                }
                ?>
            </select>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
        <div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <select data-placeholder="Choose a Heading..." name="old_inventoryid[]" id="name_0" class="chosen-select-deselect form-control prodname" style="position:relative;">
                <option value=""></option>
                <?php
                $query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory WHERE deleted=0 order by name");
                while($row = mysqli_fetch_array($query)) {
                    ?><option value='<?php echo $row['inventoryid'];?>'><?php echo $row['name'];?></option><?php
                }
                ?>
            </select>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Sell Price".',') !== FALSE) { ?>
        <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <input data-placeholder="Choose a Product..." id= "price1_0" style="" type="text" class="form-control prodprice1" />
        </div>
        <?php } ?>
        <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
        <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <input data-placeholder="Choose a Product..." id= "price2_0" style="" type="text" class="form-control prodprice2" />
        </div>
        <?php } ?>
        <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { ?>
        <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <input data-placeholder="Choose a Product..." id= "price3_0" style="" type="text" class="form-control prodprice3" />
        </div>
        <?php } ?>
        <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
        <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <input data-placeholder="Choose a Product..." id= "price4_0" style="" type="text" class="form-control prodprice4" />
         </div>
        <?php } ?>
        <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
         <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
           <input data-placeholder="Choose a Product..." id= "price5_0" style="" type="text" class="form-control prodprice5" />
        </div>
        <?php } ?>
        <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
        <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <input data-placeholder="Choose a Product..." id= "price6_0" style="" type="text" class="form-control prodprice6" />
        </div>
        <?php } ?>
        <?php if (strpos($value_config, ','."Preferred Price".',') !== FALSE) { ?>
        <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <input data-placeholder="Choose a Product..." id= "price7_0" style="" type="text" class="form-control prodprice7" />
        </div>
        <?php } ?>
        <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
        <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <input data-placeholder="Choose a Product..." id= "price8_0" style="" type="text" class="form-control prodprice8" />
        </div>
        <?php } ?>
        <?php if (strpos($value_config, ','."Web Price".',') !== FALSE) { ?>
        <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <input data-placeholder="Choose a Product..." id= "price9_0" style="" type="text" class="form-control prodprice9" />
         </div>
        <?php } ?>
        <?php if (strpos($value_config, ','."Commission Price".',') !== FALSE) { ?>
        <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
            <input data-placeholder="Choose a Product..." id= "price10_0" style="" type="text" class="form-control prodprice10" />
        </div>
        <?php } ?>
    </div>

    </div>

    <div id="add_here_new_positionprod"></div>

    <?php if($tile_security['edit'] > 0) { ?>
        <div class="col-sm-8 col-sm-offset-4 triple-gap-bottom">
            <button id="add_position_buttonprod" class="btn brand-btn mobile-block">Add</button>
        </div>
    <?php } ?>

	<label class="col-sm-1 text-center"><h4>Total</h4></label>
    <div class="col-sm-5">
    </div>
    <?php if (strpos($value_config, ','."Sell Price".',') !== FALSE) { ?>
    <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
        <input data-placeholder="Choose a Product..." id= "final_prodprice1" style="" type="text" class="form-control" />
    </div>
    <?php } ?>
    <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
    <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
        <input data-placeholder="Choose a Product..." id= "final_prodprice2" style="" type="text" class="form-control" />
    </div>
    <?php } ?>
    <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { ?>
    <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
        <input data-placeholder="Choose a Product..." id= "final_prodprice3" style="" type="text" class="form-control" />
    </div>
    <?php } ?>
    <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
    <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
        <input data-placeholder="Choose a Product..." id= "final_prodprice4" style="" type="text" class="form-control" />
     </div>
    <?php } ?>
    <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
     <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
       <input data-placeholder="Choose a Product..." id= "final_prodprice5" style="" type="text" class="form-control" />
    </div>
    <?php } ?>
    <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
    <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
        <input data-placeholder="Choose a Product..." id= "final_prodprice6" style="" type="text" class="form-control" />
    </div>
    <?php } ?>
    <?php if (strpos($value_config, ','."Preferred Price".',') !== FALSE) { ?>
    <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
        <input data-placeholder="Choose a Product..." id= "final_prodprice7" style="" type="text" class="form-control" />
    </div>
    <?php } ?>
    <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
    <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
        <input data-placeholder="Choose a Product..." id= "final_prodprice8" style="" type="text" class="form-control" />
    </div>
    <?php } ?>
    <?php if (strpos($value_config, ','."Web Price".',') !== FALSE) { ?>
    <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
        <input data-placeholder="Choose a Product..." id= "final_prodprice9" style="" type="text" class="form-control" />
     </div>
    <?php } ?>
    <?php if (strpos($value_config, ','."Commission Price".',') !== FALSE) { ?>
    <div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
        <input data-placeholder="Choose a Product..." id= "final_prodprice10" style="" type="text" class="form-control" />
    </div>
    <?php } ?>

    <?php if($tile_security['edit'] > 0) { ?>
        <div class="form-group clearfix"></div>
        <div class="form-group clearfix">
    	<label class="col-sm-1 text-center"><h4>New Product</h4></label>
    	</div>
        <div class="form-group clearfix" width="100%">
            <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
            <div class="col-sm-3">
                <select data-placeholder="Choose a Tab..." name="new_category" class="chosen-select-deselect form-control">
                    <option value=""></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT DISTINCT(category) FROM inventory WHERE deleted=0 order by category");
                    while($row = mysqli_fetch_array($query)) {
                        ?><option id='<?php echo $row['category'];?>' value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
                    }
                    ?>
                </select>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
            <div class="col-sm-3">
                <input data-placeholder="Choose a Product..." name="new_name" style="" type="text" class="form-control" />
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Sell Price".',') !== FALSE) { ?>
            <div class="col-sm-1">
                <input data-placeholder="Choose a Product..." name="newprice1" type="text" class="form-control" />
            </div>
            <?php } ?>
            <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
            <div class="col-sm-1">
                <input data-placeholder="Choose a Product..." name="newprice2" type="text" class="form-control" />
            </div>
            <?php } ?>
            <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { ?>
            <div class="col-sm-1">
                <input data-placeholder="Choose a Product..." name="newprice3" type="text" class="form-control" />
            </div>
            <?php } ?>
            <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
            <div class="col-sm-1">
                <input data-placeholder="Choose a Product..." name="newprice4" type="text" class="form-control" />
             </div>
            <?php } ?>
            <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
             <div class="col-sm-1">
               <input data-placeholder="Choose a Product..." name="newprice5" type="text" class="form-control" />
            </div>
            <?php } ?>
            <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
            <div class="col-sm-1">
                <input data-placeholder="Choose a Product..." name="newprice6" type="text" class="form-control" />
            </div>
            <?php } ?>
            <?php if (strpos($value_config, ','."Preferred Price".',') !== FALSE) { ?>
            <div class="col-sm-1">
                <input data-placeholder="Choose a Product..." name="newprice7" type="text" class="form-control" />
            </div>
            <?php } ?>
            <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
            <div class="col-sm-1">
                <input data-placeholder="Choose a Product..." name="newprice8" type="text" class="form-control" />
            </div>
            <?php } ?>
            <?php if (strpos($value_config, ','."Web Price".',') !== FALSE) { ?>
            <div class="col-sm-1">
                <input data-placeholder="Choose a Product..." name="newprice9" type="text" class="form-control" />
             </div>
            <?php } ?>
            <?php if (strpos($value_config, ','."Commission Price".',') !== FALSE) { ?>
            <div class="col-sm-1">
                <input data-placeholder="Choose a Product..." name="newprice10" type="text" class="form-control" />
            </div>
            <?php } ?>
        </div>

        <button type="submit" name="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
    <?php } ?>

</form>
