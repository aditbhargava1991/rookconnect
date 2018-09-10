<?php include_once ('../include.php');
$strict_view = strictview_visible_function($dbc, 'inventory');
$tile_security = get_security($dbc, 'inventory');
if($strict_view > 0) {
    $tile_security['edit'] = 0;
    $tile_security['config'] = 0;
}

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && $_GET['from_type'] != 'mobile' ) {
		$new_category		= $_POST['new_category'];
		$new_type			= $_POST['new_type'];
		$new_name			= filter_var($_POST['new_name'],FILTER_SANITIZE_STRING);
		$bill_of_material	= implode ( ',', $_POST['old_inventoryid'] );
		$old_inventoryids	= explode( ',', $bill_of_material );
		$sell_price			= ( !empty ( $_POST['newprice1'] ) ) ? trim ( $_POST['newprice1'] ): 0.00;
		$final_retail_price	= ( !empty ( $_POST['newprice2'] ) ) ? trim ( $_POST['newprice2'] ) : 0.00;
		$unit_price			= ( !empty ( $_POST['newprice3'] ) ) ? trim ( $_POST['newprice3'] ) : 0.00;
		$wholesale_price	= ( !empty ( $_POST['newprice4'] ) ) ? trim ( $_POST['newprice4'] ) : 0.00;
		$commercial_price	= ( !empty ( $_POST['newprice5'] ) ) ? trim ( $_POST['newprice5'] ) : 0.00;
		$client_price		= ( !empty ( $_POST['newprice6'] ) ) ? trim ( $_POST['newprice6'] ) : 0.00;
		$preferred_price	= ( !empty ( $_POST['newprice7'] ) ) ? trim ( $_POST['newprice7'] ) : 0.00;
		$admin_price		= ( !empty ( $_POST['newprice8'] ) ) ? trim ( $_POST['newprice8'] ) : 0.00;
		$web_price			= ( !empty ( $_POST['newprice9'] ) ) ? trim ( $_POST['newprice9'] ) : 0.00;
		$commission_price	= ( !empty ( $_POST['newprice10'] ) ) ? trim ( $_POST['newprice10'] ) : 0.00;

		$query_insert_inventory = "INSERT INTO `inventory` (`bill_of_material`, `category`, `product_type`, `name`, `sell_price`, `final_retail_price`, `unit_price`, `wholesale_price`, `commercial_price`, `client_price`, `preferred_price`, `admin_price`, `web_price`, `commission_price`) VALUES ('$bill_of_material', '$new_category', '$new_type', '$new_name', '$sell_price', '$final_retail_price', '$unit_price', '$wholesale_price', '$commercial_price', '$client_price', '$preferred_price', '$admin_price', '$web_price', '$commission_price')";
		$result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
		
		$new_inventoryid	= mysqli_insert_id($dbc);
		$dater				= date('Y/m/d h:i:s a', time());
		$contactid			= $_SESSION['contactid'];
		
		$result = mysqli_query ( $dbc, "SELECT * FROM `contacts` WHERE `contactid`='$contactid'" );
		
		while ( $row = mysqli_fetch_assoc ( $result ) ) {
			$name = decryptIt ( $row['first_name'] ) . ' ' . decryptIt ( $row['last_name'] ) . ' (ID: ' . $row['contactid'] . ')';
		}
		
		$query_insert_inventory = "INSERT INTO `bill_of_material_log` (`pieces_of_inventoryid`, `inventoryid`, `date_time`, `contact`, `type`, `deleted`) VALUES ('$bill_of_material', '$new_inventoryid', '$dater', '$name', 'Add', '0')";
		$result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
		
		/* Create a Consumable Product
		 * Reduce the Inventory quantity at the time of POS creation
		 */
		$result = mysqli_query ( $dbc, "INSERT INTO `products` (`product_type`, `category`, `heading`, `sell_price`, `final_retail_price`, `unit_price`, `wholesale_price`, `commercial_price`, `client_price`, `preferred_price`, `admin_price`, `web_price`, `commission_price`, `include_in_pos`, `inventoryid` ) VALUES ('$new_type', '$new_category', '$new_name', '$sell_price', '$final_retail_price', '$unit_price', '$wholesale_price', '$commercial_price', '$client_price', '$preferred_price', '$admin_price', '$web_price', '$commission_price', 1, '$new_inventoryid' )" );

		$url = "inventory.php?category=".$new_category;
		echo '<script type="text/javascript">window.location.replace("'.$url.'");</script>';
	}
?>
<script type="text/javascript">
	$(document).ready(function() {
		var prodcount = 1;
		$('#add_position_buttonprod').on('click', function () {
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

	});
	
	$(document).on('change', 'select[name="prodcategory[]"]', function() { prodselectCategory(this); });
	$(document).on('change', 'select[name="old_inventoryid[]"]', function() { prodselectName(this); });
	
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
			}
		});
	}
</script>
	        <?php
				$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(inventory_dashboard SEPARATOR ',') AS all_inventory FROM field_config_inventory WHERE accordion IS NULL AND inventory IS NULL"));
				$value_config = ','.$get_field_config['all_inventory'].',';
			?>

			<form name="form_sites" method="post" action="bill_of_material_consumables.php" class="form-horizontal" role="form">
				
				<!------- List Inventory Items To Create A Bill of Material ------->
				<div class="form-group clearfix">
					<label class="col-sm-12 double-gap-top"><h3>Inventory Item(s)</h3></label>
				</div>
				<div class="form-group clearfix"><?php
					if ( strpos ( $value_config, ',Category,' ) !== FALSE ) { ?>
	                    <label class="col-sm-2">Tab</label><?php
					}
					if ( strpos ( $value_config, ',Name,' ) !== FALSE ) { ?>
	                    <label class="col-sm-3">Name</label><?php
					}
					if ( strpos ( $value_config, ',Average Cost,' ) !== FALSE ) { ?>
	                    <label class="col-sm-1 text-center">Average Cost</label><?php
					}
					if ( strpos ( $value_config, ',Unit Cost,' ) !== FALSE ) { ?>
	                    <label class="col-sm-1 text-center">Unit Cost</label><?php
					}
					if ( strpos ( $value_config, ',Cost,' ) !== FALSE ) { ?>
	                    <label class="col-sm-1 text-center">Cost</label><?php
					}
					if ( strpos ( $value_config, ',CDN Cost Per Unit,' ) !== FALSE ) { ?>
	                    <label class="col-sm-1 text-center">CDN Cost/Unit</label><?php
					}
					if ( strpos ( $value_config, ',USD Cost Per Unit,' ) !== FALSE ) { ?>
	                    <label class="col-sm-1 text-center">USD Cost/Unit</label><?php
					}
					if ( strpos ( $value_config, ',Drum Unit Cost,' ) !== FALSE ) { ?>
	                    <label class="col-sm-1 text-center">Drum Unit Cost</label><?php
					}
					if ( strpos ( $value_config, ',Tote Unit Cost,' ) !== FALSE ) { ?>
	                    <label class="col-sm-1 text-center">Tote Unit Cost</label><?php
					} ?>
	            </div>
				
				<div class="clearfix"></div>
	            
				<div class="additional_positionprod">
					<div class="form-group clearfix" id="prodservices_0" width="100%">
						<?php if ( strpos ( $value_config, ',Category,' ) !== FALSE ) { ?>
							<div class="col-sm-2 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
								<select data-placeholder="Choose a Tab..." id="category_0" name="prodcategory[]" class="chosen-select-deselect form-control prodcategory">
									<option value=""></option><?php
									$query = mysqli_query ( $dbc, "SELECT DISTINCT(`category`) FROM `inventory` WHERE `deleted`=0 ORDER BY `category`" );
									while ( $row = mysqli_fetch_array ( $query ) ) { ?>
										<option id="<?php echo $row['category']; ?>" value="<?php echo $row['category']; ?>"><?php echo $row['category']; ?></option><?php
									} ?>
								</select>
							</div>
						<?php } ?>
						
						<?php if ( strpos ( $value_config, ',Name,' ) !== FALSE ) { ?>
							<div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
								<select data-placeholder="Choose a Heading..." name="old_inventoryid[]" id="name_0" class="chosen-select-deselect form-control prodname" style="position:relative;">
									<option value=""></option><?php
									$query = mysqli_query ( $dbc, "SELECT `inventoryid`, `name` FROM `inventory` WHERE `deleted`=0 ORDER BY `name`" );
									while ( $row = mysqli_fetch_array ( $query ) ) { ?>
										<option value="<?php echo $row['inventoryid']; ?>"><?php echo $row['name']; ?></option><?php
									} ?>
								</select>
							</div>
						<?php }
						
						if ( strpos ( $value_config, ',Average Cost,' ) !== FALSE ) { ?>
							<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id="price1_0" type="text" class="form-control prodprice1" /></div><?php
						}
						if ( strpos ( $value_config, ',Unit Cost,' ) !== FALSE ) { ?>
							<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id="price2_0" type="text" class="form-control prodprice2" /></div><?php
						}
						if ( strpos ( $value_config, ',Cost,' ) !== FALSE ) { ?>
							<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id="price3_0" type="text" class="form-control prodprice3" /></div><?php
						}
						if ( strpos ( $value_config, ',CDN Cost Per Unit,' ) !== FALSE ) { ?>
							<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id="price4_0" type="text" class="form-control prodprice4" /></div><?php
						}
						if ( strpos ( $value_config, ',USD Cost Per Unit,' ) !== FALSE ) { ?>
							<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id="price5_0" type="text" class="form-control prodprice5" /></div><?php
						}
						if ( strpos ( $value_config, ',Drum Unit Cost,' ) !== FALSE ) { ?>
							<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id="price6_0" type="text" class="form-control prodprice6" /></div><?php
						}
						if ( strpos ( $value_config, ',Tote Unit Cost,' ) !== FALSE ) { ?>
							<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id="price7_0" type="text" class="form-control prodprice7" /></div><?php
						} ?>
					</div>
	            </div><!-- .additional_positionprod -->

	            <div id="add_here_new_positionprod"></div>
	            
	            <?php if($tile_security['edit'] > 0) { ?>
		            <div class="col-sm-12 triple-gap-bottom">
		                <button id="add_position_buttonprod" class="btn brand-btn mobile-block">Add</button>
		            </div>
		        <?php } ?>

				<label class="col-sm-5"><h4>Total</h4></label><?php
				if ( strpos ( $value_config, ',Average Cost,' ) !== FALSE ) { ?>
					<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id= "final_prodprice1" type="text" class="form-control" /></div><?php
				}
				if ( strpos ( $value_config, ',Unit Cost,' ) !== FALSE ) { ?>
					<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id= "final_prodprice2" type="text" class="form-control" /></div><?php
				}
				if ( strpos ( $value_config, ',Cost,' ) !== FALSE ) { ?>
					<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id= "final_prodprice3" type="text" class="form-control" /></div><?php
				}
				if ( strpos ( $value_config, ',CDN Cost Per Unit,' ) !== FALSE ) { ?>
					<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id= "final_prodprice4" type="text" class="form-control" /></div><?php
				}
				if ( strpos ( $value_config, ',USD Cost Per Unit,' ) !== FALSE ) { ?>
					<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id= "final_prodprice5" type="text" class="form-control" /></div><?php
				}
				if ( strpos ( $value_config, ',Drum Unit Cost,' ) !== FALSE ) { ?>
					<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id= "final_prodprice6" type="text" class="form-control" /></div><?php
				}
				if ( strpos ( $value_config, ',Tote Unit Cost,' ) !== FALSE ) { ?>
					<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>"><input id= "final_prodprice7" type="text" class="form-control" /></div><?php
				} ?>

	            <div class="clearfix double-gap-bottom"></div>
				
				
				<!------- Create A Bill of Material from selected Inventory Items ------->
				<div class="form-group clearfix double-gap-top">
					<label class="col-sm-12"><h3>Create A New Product</h3></label>
				</div>
				<div class="col-sm-12"><?php
					if ( strpos ( $value_config, ',Category,' ) !== FALSE ) { ?>
						<div class="col-sm-4 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center">Tab</label>
							<select data-placeholder="Choose a Tab..." name="new_category" class="chosen-select-deselect form-control">
								<option value=""></option><?php
								$query = mysqli_query ( $dbc, "SELECT DISTINCT(`category`) FROM `products` WHERE `deleted`=0 ORDER BY `category`" );
								while ( $row = mysqli_fetch_array ( $query ) ) { ?>
									<option id="<?php echo $row['category']; ?>" value="<?php echo $row['category']; ?>"><?php echo $row['category']; ?></option><?php
								} ?>
							</select>
						</div><?php
					}
					if ( strpos ( $value_config, ',Type,' ) !== FALSE ) { ?>
						<div class="col-sm-4 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center">Product Tab</label>
							<select data-placeholder="Choose a Tab..." name="new_type" class="chosen-select-deselect form-control">
								<option value=""></option><?php
								$query = mysqli_query ( $dbc, "SELECT DISTINCT(`product_type`) FROM `products` WHERE `deleted`=0 ORDER BY `product_type`" );
								while ( $row = mysqli_fetch_array ( $query ) ) { ?>
									<option id="<?php echo $row['product_type']; ?>" value="<?php echo $row['product_type']; ?>"><?php echo $row['product_type']; ?></option><?php
								} ?>
							</select>
						</div><?php
					}
					if ( strpos ( $value_config, ',Name,' ) !== FALSE ) { ?>
						<div class="col-sm-4 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center">Name</label>
							<input name="new_name" type="text" class="form-control" />
						</div><?php
					} ?>
				</div>
				<div class="col-sm-12"><?php
					if ( strpos ( $value_config, ',Sell Price,' ) !== FALSE ) { ?>
						<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center" style="min-height:50px;">Sell Price</label>
							<input name="newprice1" type="text" class="form-control" />
						</div><?php
					}
					if ( strpos ( $value_config, ',Final Retail Price,' ) !== FALSE ) { ?>
						<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center" style="min-height:50px;">Final Retail Price</label>
							<input name="newprice2" type="text" class="form-control" />
						</div><?php
					}
					if ( strpos ( $value_config, ',Unit Price,' ) !== FALSE ) { ?>
						<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center" style="min-height:50px;">Unit Price</label>
							<input name="newprice3" type="text" class="form-control" />
						</div><?php
					}
					if ( strpos ( $value_config, ',Wholesale Price,' ) !== FALSE ) { ?>
						<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center" style="min-height:50px;">Wholesale Price</label>
							<input name="newprice4" type="text" class="form-control" />
						</div><?php
					}
					if ( strpos ( $value_config, ',Commercial Price,' ) !== FALSE ) { ?>
						<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center" style="min-height:50px;">Commercial Price</label>
							<input name="newprice5" type="text" class="form-control" />
						</div><?php
					}
					if ( strpos ( $value_config, ',Client Price,' ) !== FALSE ) { ?>
						<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center" style="min-height:50px;">Client Price</label>
							<input name="newprice6" type="text" class="form-control" />
						</div><?php
					}
					if ( strpos ( $value_config, ',Preferred Price,' ) !== FALSE ) { ?>
						<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center" style="min-height:50px;">Preferred Price</label>
							<input name="newprice7" type="text" class="form-control" />
						</div><?php
					}
					if ( strpos ( $value_config, ',Admin Price,' ) !== FALSE ) { ?>
						<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center" style="min-height:50px;">Admin Price</label>
							<input name="newprice8" type="text" class="form-control" />
						</div><?php
					}
					if ( strpos ( $value_config, ',Web Price,' ) !== FALSE ) { ?>
						<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center" style="min-height:50px;">Web Price</label>
							<input name="newprice9" type="text" class="form-control" />
						</div><?php
					}
					if ( strpos ( $value_config, ',Commission Price,' ) !== FALSE ) { ?>
						<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> double-gap-bottom">
							<label class="text-center" style="min-height:50px;">Commission Price</label>
							<input name="newprice10" type="text" class="form-control" />
						</div><?php
					} ?>
	            </div>
	            <div class="form-group clearfix"><?php
					if ( strpos ( $value_config, ',Category,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Type,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Name,' ) !== FALSE ) { ?>
	                    <?php
					}
					if ( strpos ( $value_config, ',Sell Price,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Final Retail Price,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Unit Price,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Wholesale Price,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Commercial Price,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Client Price,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Preferred Price,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Admin Price,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Web Price,' ) !== FALSE ) { ?><?php
					}
					if ( strpos ( $value_config, ',Commission Price,' ) !== FALSE ) { ?><?php
					} ?>
	            </div>
				<?php if($tile_security['edit'] > 0) { ?>
		            <button type="submit" name="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
		        <?php } ?>
	        </form>
	        
		</div><!-- .row .hide_on_iframe -->
	</div><!-- .container -->
</form>