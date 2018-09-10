<?php include_once('../include.php');
$match_business = '';
if(!empty(MATCH_CONTACTS)) {
	$match_business = " AND `tickets`.`businessid` IN (".MATCH_CONTACTS.")";
} ?>

<script>
$(document).ready(function() {

	$('.selectall2').click(
		function() {
			if($('.selectall2').hasClass("deselectall")) {
				$(".selectall2").removeClass('deselectall');
				if($('.order_list_includer').prop('checked', true)) {
					$('.order_list_includer').click();
				}
				$(".selectall2").text('Select All');
				$('.selectall2').prop('title', 'This will select all rows on the current page.');
			} else {
				$(".selectall2").addClass('deselectall');
				if($('.order_list_includer').prop('checked', false)) {
					$('.order_list_includer').click();
				}
				$(".selectall2").text('Deselect All');
				$('.selectall2').prop('title', 'This will deselect all rows on the current page.');
			}

		});

	$('input.purchase_order_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=include_in_orders&type=po&name=inventory&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});

	$('input.sales_order_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=include_in_orders&type=so&name=inventory&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});

	$('input.order_list_includer').on('change', function() {
		var id = $(this).attr('id');
		var value = $(this).attr('value');
		if($(this).prop('checked') == true){
			var funct = 'add'
		} else { var funct = 'delete'; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "inventory_ajax_all.php?fill=include_in_orders&type=inventoryorderlist&name=inventory&value="+value+"&function="+funct+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){

		}
		});
	});

	$('input.point_of_sale_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=include_in_orders&type=pos&name=inventory&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});

	$('input.product_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = 0; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=include_in_orders&type=product&name=inventory&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
            location.reload();
		}
		});
	});

	$('.selectall').click(
        function() {
			if($('.selectall').hasClass("deselectall")) {
				$(".selectall").removeClass('deselectall');
				$('.display_website').prop('checked', false);
				$(".selectall").text('Select all');
				$('.selectall').prop('title', 'This will select all rows on the current page.');
			} else {
				$(".selectall").addClass('deselectall');
				$('.display_website').prop('checked', true);
				$(".selectall").text('Deselect all');
				$('.selectall').prop('title', 'This will deselect all rows on the current page.');
			}

		});


	$('input.display_website').on('change', function() {
		var invid = $(this).attr('id');

		if($(this).prop('checked') == true){
			var display = 'Yes';
		}
		if($(this).prop('checked') == false){
			var display = 'No';
		}

		$.ajax({
			type: "GET",
			url: "../ajax_all.php?fill=display_on_website&display="+display+"&invid="+invid,
			dataType: "html",
			success: function(response){
				console.log(response);
				location.reload();
			},
			cache: false
		});
	});

	/*$('.submit_selection').click(
		function() {
			var selected = new Array();
			var unSelected = new Array();

			$(".display_website").each(function () {
				if($(this).is(":checked")) {
					selected.push($(this).val());
					//console.log('checked '+selected);
				} else {
					unSelected.push($(this).val());
					//console.log('no checked '+unSelected);
				}
			});

			$.ajax({
				type: "GET",
				url: "../ajax_all.php?fill=display_on_website&selected="+selected+"&unselected="+unSelected,
				dataType: "html",
				success: function(response){
					//location.reload();
					console.log(response);
				}
			});
		}
	)*/

	$('.iframe_open').click(function() {
			var id = $(this).data('id');
			var title = $(this).parents('tr').children(':first').text();
		   $('#iframe_instead_of_window').attr('src', 'history.php?inventoryid='+id);
		   $('.iframe_title').text(title+' History');
		   $('.iframe_holder').show();
		   $('.hide_on_iframe').hide();
		   $('#iframe_instead_of_window').load(function() {
			   $(this).height(''+this.contentDocument.body.scrollHeight+'px');
		   });
	});

	$('.close_iframe').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});
	
	$('[data-inventory]').off('change').change(function() {
		var field = this;
		$.post('inventory_ajax.php?action=dashboard_update',
			{ name: this.name, value: this.value, id: $(this).data('inventory'), ticket: $(this).data('ticket') },
			function(response) {
				if($(field).is('[data-target]')) {
					$(field).closest('tr').find($(field).data('target')).html(response);
				}
			});
	});
});
$(document).on('change', 'select[name="search_category"]', function() { location = this.value; });
</script>
<style>
.selectbutton, .submit_selection { cursor:pointer; text-decoration:underline; }
@media (min-width: 801px) {
	.sel2 {	display:none; }
}
@media(min-width:768px) {
	.cate_fitter { max-width:300px; }
}
</style>
<?php 
$inventory_navigation_position = get_config($dbc, 'inventory_navigation_position');
$dropdownornot ='';
$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_category_dropdown'"));
if($get_config['configid'] > 0) {
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_category_dropdown'"));
	if($get_config['value'] == '1') {
		$dropdownornot = 'true';
	}
} ?>

<div id="no-more-tables">
	<?php
	// Display Pager
	$inventory = '';
	if (isset($_POST['search_inventory_submit'])) {
		$inventory = $_POST['search_inventory'];
        if (isset($_POST['search_inventory'])) {
            $inventory = $_POST['search_inventory'];
        }
     //   if ($_POST['search_category'] != '') {
       //     $inventory = $_POST['search_category'];
       // }
	}
	if (isset($_POST['display_all_inventory'])) {
		$inventory = '';
	}
	$rowsPerPage = ITEMS_PER_PAGE;
	$pageNum = 1;

	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}

	$offset = ($pageNum - 1) * $rowsPerPage;
	$warehouse = filter_var($_GET['warehouse'],FILTER_SANITIZE_STRING);
	$ticket_search = '';
	if(isset($_GET['search_inventory'])) {
		$tickets = $dbc->query("SELECT * FROM `tickets` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`src_table`='inventory_general' WHERE `tickets`.`deleted`=0 AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_attached` WHERE `src_table` IN ('inventory','inventory_general')) AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `warehouse_location`='$warehouse')");
		$ticket_ids = [];
		while($ticket = $tickets->fetch_assoc()) {
			if(strpos(get_ticket_label($dbc, $ticket).$ticket['piece_type'], $_GET['search_inventory']) !== FALSE) {
				$ticket_ids[] = $ticket['ticketid'];
			}
		}
		$ticket_search = " AND `tickets`.`ticketid` IN (".implode(',',$ticket_ids).")";
	}

	if($_GET['ticket_status'] != '') {
		$ticket_search .= " AND `status`='".filter_var($_GET['ticket_status'],FILTER_SANITIZE_STRING)."'";
	}
	if($_GET['projecttype'] != '') {
		$ticket_search .= " AND `projectid` IN (SELECT `projectid` FROM `project` WHERE `projecttype`='".filter_var($_GET['projecttype'],FILTER_SANITIZE_STRING)."')";
	}
	if($_GET['businessid'] != '') {
		$ticket_search .= " AND `tickets`.`businessid`='".filter_var($_GET['businessid'],FILTER_SANITIZE_STRING)."'";
	}
	if($_GET['clientid'] != '') {
		$ticket_search .= " AND `tickets`.`clientid`='".filter_var($_GET['clientid'],FILTER_SANITIZE_STRING)."'";
	}
	if($_GET['po'] != '') {
		$ticket_search .= " AND IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`)='".filter_var($_GET['po'],FILTER_SANITIZE_STRING)."'";
	}
	$ticket_list = mysqli_query($dbc, "SELECT `tickets`.*, `ticket_attached`.`id`, CONCAT('#',COUNT(`prior_piece`.`id`) + 1,' ',`ticket_attached`.`piece_type`) `inv_label`, `ticket_attached`.`weight` `inv_weight`, `ticket_attached`.`weight_units` `inv_units` FROM `tickets` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`src_table`='inventory_general' LEFT JOIN `ticket_attached` `prior_piece` ON `tickets`.`ticketid`=`prior_piece`.`ticketid` AND `prior_piece`.`deleted`=0 AND `prior_piece`.`src_table`='inventory_general' AND `prior_piece`.`id` < `ticket_attached`.`id` WHERE `tickets`.`deleted`=0 AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `warehouse_location`='$warehouse') AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_attached` WHERE `src_table` IN ('inventory_general')) $ticket_search $match_contact GROUP BY `tickets`.`ticketid`, `ticket_attached`.`id` ORDER BY `ticketid` DESC LIMIT $offset, $rowsPerPage");
	$ticket_count = "SELECT COUNT(*) numrows FROM `tickets` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`src_table`='inventory_general' WHERE `tickets`.`deleted`=0 AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `warehouse_location`='$warehouse') AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_attached` WHERE `src_table` IN ('inventory_general')) $ticket_search $match_contact";
	if($ticket_list->num_rows > 0) {
		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `inventory_dashboard` FROM `field_config_inventory` WHERE `inventory_dashboard` IS NOT NULL ORDER BY IF(`tab`='Uncategorized','',IF(`tab`='','Z',`tab`))"));
		$value_config = ','.$get_field_config['inventory_dashboard'].',';
		$column_count = count(array_filter(explode(',',$value_config))) + 1;
		display_pagination($dbc, $ticket_count, $pageNum, $rowsPerPage);
		$current_ticketid = 0;
		while($ticket = $ticket_list->fetch_assoc()) {
			echo "<div class='dashboard-item' style='border: 1px solid #ddd;'>
				<h3>".trim(get_ticket_label($dbc, $ticket)).": ";
				$weight = 0;
				foreach(explode('#*#',$ticket['inv_weight']) as $inv_weight) {
					$weight += $inv_weight;
				}
			echo ($weight > 0 ? $weight.' '.explode('#*#',$ticket['inv_units'])[0]." - " : '').$ticket['inv_label']."<label class='pull-right smaller'><input type='checkbox' onchange=\"$(this).closest('.dashboard-item').find('[type=checkbox]').not(':checked').prop('checked',true).change();\"> Received All</label></h3>";
			$ticket_inv = mysqli_query($dbc, "SELECT * FROM `ticket_attached` LEFT JOIN `inventory` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` WHERE `ticket_attached`.`src_table`='inventory' AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`ticketid`='{$ticket['ticketid']}' AND `line_id` IN (".($current_ticketid == $ticket['ticketid'] ? '' : '0,')."{$ticket['id']})");
			echo "<table class='table table-bordered'>";
				echo "<tr class='hidden-xs hidden-sm'>";
					if (isset($_GET['order_list'])) {
						echo '<th>Include in Order List';
						echo "<div class='selectall2 selectbutton' title='This will select all PDFs on the current page.' style='text-decoration:underline;cursor:pointer;'>Select All</div>";
						echo '</th>';
					}
					if (strpos($value_config, ','."Part #".',') !== FALSE) {
						echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="The Part # for an inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> Part #</th>';
					}
					if (strpos($value_config, ','."ID #".',') !== FALSE) {
						echo '<th>ID #</th>';
					}
					if (strpos($value_config, ','."Item SKU".',') !== FALSE) {
						echo '<th>Item SKU</th>';
					}
					if (strpos($value_config, ','."Code".',') !== FALSE) {
						echo '<th>Code</th>';
					}
					if (strpos($value_config, ','."Description".',') !== FALSE) {
						echo '<th>Description</th>';
					}
					if (strpos($value_config, ','."Category".',') !== FALSE) {
						echo '<th>Tab</th>';
					}
					if (strpos($value_config, ','."Subcategory".',') !== FALSE) {
						echo '<th>Subtab</th>';
					}
					if (strpos($value_config, ','."Name".',') !== FALSE) {
						echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="The set name of the inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> Name</th>';
					}
					if (strpos($value_config, ','."Product Name".',') !== FALSE) {
						echo '<th>Product Name</th>';
					}
					if (strpos($value_config, ','."Type".',') !== FALSE) {
						echo '<th>Tab</th>';
					}
					if (strpos($value_config, ','."Color".',') !== FALSE) {
						echo '<th>Color</th>';
					}

					/* Remove Kristi's (SEA) access to Product Costs */
					if ( $rookconnect == 'sea' && isset ( $_SESSION['user_name'] ) && $_SESSION['user_name'] == 'kristi' ) {
						// Show nothing
					} else {
						if (strpos($value_config, ','."Cost".',') !== FALSE) {
							echo '<th>Cost</th>';
						}
						if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
							echo '<th>CDN Cost Per Unit</th>';
						}
						if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
							echo '<th>USD Cost Per Unit</th>';
						}
						if (strpos($value_config, ','."Average Cost".',') !== FALSE) {
							echo '<th>Average Cost</th>';
						}
						if (strpos($value_config, ','."Purchase Cost".',') !== FALSE) {
							echo '<th>Purchase Cost</th>';
						}
						if (strpos($value_config, ','."USD Invoice".',') !== FALSE) {
							echo '<th>USD Invoice</th>';
						}
					}

					if (strpos($value_config, ','."COGS".',') !== FALSE) {
						echo '<th>COGS GL Code</th>';
					}
					if (strpos($value_config, ','."Vendor".',') !== FALSE) {
						echo '<th>Vendor</th>';
					}
					if (strpos($value_config, ','."Date Of Purchase".',') !== FALSE) {
						echo '<th>Date Of Purchase</th>';
					}
					if (strpos($value_config, ','."Shipping Rate".',') !== FALSE) {
						echo '<th>Shipping Rate</th>';
					}
					if (strpos($value_config, ','."Shipping Cash".',') !== FALSE) {
						echo '<th>Shipping Cash</th>';
					}
					if (strpos($value_config, ','."Freight Charge".',') !== FALSE) {
						echo '<th>Freight Charge</th>';
					}
					if (strpos($value_config, ','."Exchange Rate".',') !== FALSE) {
						echo '<th>Exchange Rate</th>';
					}
					if (strpos($value_config, ','."Exchange $".',') !== FALSE) {
						echo '<th>Exchange $</th>';
					}

					if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) {
						echo '<th>Drum Unit Cost</th>';
					}

					if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) {
						echo '<th>Drum Unit Price</th>';
					}
					if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) {
						echo '<th>Tote Unit Cost</th>';
					}
					if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) {
						echo '<th>Tote Unit Price</th>';
					}
					if (strpos($value_config, ','."WCB Price".',') !== FALSE) {
						echo '<th>WCB Price</th>';
					}
					if (strpos($value_config, ','."Sell Price".',') !== FALSE) {
						echo '<th>Sell Price</th>';
					}
					if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
						echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="The final price that the inventory item will be sold for."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> Final Retail Price</th>';
					}
					if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) {
						echo '<th>Wholesale Price</th>';
					}
					if (strpos($value_config, ','."Commercial Price".',') !== FALSE) {
						echo '<th>Commercial Price</th>';
					}
					if (strpos($value_config, ','."Client Price".',') !== FALSE) {
						echo '<th>Client Price</th>';

					}
					if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) {
						echo '<th>Purchase Order Price</th>';
					}
					if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) {
						echo '<th>'.SALES_ORDER_NOUN.' Price</th>';
					}
					if (strpos($value_config, ','."Suggested Retail Price".',') !== FALSE) {
						echo '<th>Suggested Retail Price</th>';
					}
					if (strpos($value_config, ','."Rush Price".',') !== FALSE) {
						echo '<th>Rush Price</th>';
					}
					if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) {
						echo '<th>Include in '.SALES_ORDER_TILE.'</th>';
					}
					if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) {
						echo '<th>Include in Point of Sale</th>';
					}
					if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) {
						echo '<th>Include in Purchase Orders</th>';
					}
					if (strpos($value_config, ','."Include in Product".',') !== FALSE) {
						echo '<th>Include in Product</th>';
					}
					if (strpos($value_config, ','."Preferred Price".',') !== FALSE) {
						echo '<th>Preferred Price</th>';
					}
					if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
						echo '<th>Admin Price</th>';
					}
					if (strpos($value_config, ','."Web Price".',') !== FALSE) {
						echo '<th>Web Price</th>';
					}
					if (strpos($value_config, ','."Commission Price".',') !== FALSE) {
						echo '<th>Commission Price</th>';
					}
					if (strpos($value_config, ','."MSRP".',') !== FALSE) {
						echo '<th>MSRP</th>';
					}

					if (strpos($value_config, ','."Unit Price".',') !== FALSE) {
						echo '<th>Unit Price</th>';
					}
					if (strpos($value_config, ','."Unit Cost".',') !== FALSE) {
						echo '<th>Unit Cost</th>';
					}
					if (strpos($value_config, ','."Rent Price".',') !== FALSE) {
						echo '<th>Rent Price</th>';
					}
					if (strpos($value_config, ','."Rental Days".',') !== FALSE) {
						echo '<th>Rental Days</th>';
					}
					if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) {
						echo '<th>Rental Weeks</th>';
					}
					if (strpos($value_config, ','."Rental Months".',') !== FALSE) {
						echo '<th>Rental Months</th>';
					}
					if (strpos($value_config, ','."Rental Years".',') !== FALSE) {
						echo '<th>Rental Years</th>';
					}
					if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) {
						echo '<th>Reminder/Alert</th>';
					}
					if (strpos($value_config, ','."Daily".',') !== FALSE) {
						echo '<th>Daily</th>';
					}
					if (strpos($value_config, ','."Weekly".',') !== FALSE) {
						echo '<th>Weekly</th>';
					}
					if (strpos($value_config, ','."Monthly".',') !== FALSE) {
						echo '<th>Monthly</th>';
					}
					if (strpos($value_config, ','."Annually".',') !== FALSE) {
						echo '<th>Annually</th>';
					}
					if (strpos($value_config, ','."#Of Days".',') !== FALSE) {
						echo '<th>#Of Days</th>';
					}
					if (strpos($value_config, ','."#Of Hours".',') !== FALSE) {
						echo '<th>#Of Hours</th>';
					}
					if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) {
						echo '<th>#Of Kilometers</th>';
					}
					if (strpos($value_config, ','."#Of Miles".',') !== FALSE) {
						echo '<th>#Of Miles</th>';
					}
					if (strpos($value_config, ','."Markup By $".',') !== FALSE) {
						echo '<th>Markup By $</th>';
					}
					if (strpos($value_config, ','."Markup By %".',') !== FALSE) {
						echo '<th>Markup By %</th>';
					}

					if (strpos($value_config, ','."GL Revenue".',') !== FALSE) {
						echo '<th>GL Revenue</th>';
					}
					if (strpos($value_config, ','."GL Assets".',') !== FALSE) {
						echo '<th>GL Assets</th>';
					}

					if (strpos($value_config, ','."Current Stock".',') !== FALSE) {
						echo '<th>Current Stock</th>';
					}
					if (strpos($value_config, ','."Current Inventory".',') !== FALSE) {
						echo '<th>Current Inventory</th>';
					}
					if (strpos($value_config, ','."Quantity".',') !== FALSE) {
						echo '<th>Quantity</th>';
					}
					if (strpos($value_config, ','."Expected".',') !== FALSE) {
						echo '<th>Expected</th>';
					}
					if (strpos($value_config, ','."Received".',') !== FALSE) {
						echo '<th>Received</th>';
					}
					if (strpos($value_config, ','."Discrepancy".',') !== FALSE) {
						echo '<th>Discrepancy</th>';
					}
					if (strpos($value_config, ','."Variance".',') !== FALSE) {
						echo '<th>GL Code</th>';
					}
					if (strpos($value_config, ','."Write-offs".',') !== FALSE) {
						echo '<th>Write-offs</th>';
					}
					if (strpos($value_config, ','."Buying Units".',') !== FALSE) {
						echo '<th>Buying Units</th>';
					}
					if (strpos($value_config, ','."Selling Units".',') !== FALSE) {
						echo '<th>Selling Units</th>';
					}
					if (strpos($value_config, ','."Stocking Units".',') !== FALSE) {
						echo '<th>Stocking Units</th>';
					}
					if (strpos($value_config, ','."Min Amount".',') !== FALSE) {
						echo '<th>Min Amount</th>';
					}
					if (strpos($value_config, ','."Max Amount".',') !== FALSE) {
						echo '<th>Max Amount</th>';
					}
					if (strpos($value_config, ','."Location".',') !== FALSE) {
						echo '<th>Location</th>';
					}
					if (strpos($value_config, ','."LSD".',') !== FALSE) {
						echo '<th>LSD</th>';
					}
					if (strpos($value_config, ','."Size".',') !== FALSE) {
						echo '<th>Size</th>';
					}
					if (strpos($value_config, ','."Weight".',') !== FALSE) {
						echo '<th>Weight</th>';
					}
					if (strpos($value_config, ','."Min Max".',') !== FALSE) {
						echo '<th>Min Max</th>';
					}
					if (strpos($value_config, ','."Min Bin".',') !== FALSE) {
						echo '<th>Min Bin</th>';
					}
					if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
						echo '<th>Estimated Hours</th>';
					}
					if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
						echo '<th>Actual Hours</th>';
					}
					if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
						echo '<th>Minimum Billable</th>';
					}
					if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
						echo '<th>Quote Description</th>';
					}
					if (strpos($value_config, ','."Status".',') !== FALSE) {
						echo '<th>Status</th>';
					}
					if (strpos($value_config, ','."Display On Website".',') !== FALSE) {
						echo '<th style="text-align:center">Display On Website<!--<br><div class="selectall selectbutton" title="This will select all PDFs on the current page.">Select All</div><div class="submit_selection">[ submit ]</div>--></th>';
					}
					if (strpos($value_config, ','."Featured On Website".',') !== FALSE) {
						echo '<th>Featured</th>';
					}
					if (strpos($value_config, ','."New Item".',') !== FALSE) {
						echo '<th>New</th>';
					}
					if (strpos($value_config, ','."Item On Sale".',') !== FALSE) {
						echo '<th>On Sale</th>';
					}
					if (strpos($value_config, ','."Item On Clearance".',') !== FALSE) {
						echo '<th>Clearance</th>';
					}
					if (strpos($value_config, ','."Notes".',') !== FALSE) {
						echo '<th>Notes</th>';
					}
					if (strpos($value_config, ','."Comments".',') !== FALSE) {
						echo '<th>Comments</th>';
					}
					if (strpos($value_config, ','."History".',') !== FALSE) {
						echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Inventory History for each inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> History</th>';
					}
					echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Edit or Archive an inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> Function</th>';
					echo "</tr>";
					while($row = $ticket_inv->fetch_assoc()) {
						$color = '';
						if($row['status'] == 'In inventory') {
							$color = 'style="color: white;"';
						}
						if($row['status'] == 'In transit from vendor') {
							$color = 'style="color: red;"';
						}
						if($row['status'] == 'In transit between yards') {
							$color = 'style="color: blue;"';
						}
						if($row['status'] == 'Not confirmed in yard by inventory check') {
							$color = 'style="color: yellow;"';
						}
						if($row['status'] == 'Assigned to job') {
							$color = 'style="color: green;"';
						}
						if($row['status'] == 'In transit and assigned') {
							$color = 'style="color: purple;"';
						}

						echo '<tr '.$color.'>';
						if (isset($_GET['order_list'])) {
							echo '<td data-title="Include in Order List">';
							$HiddenProducts = explode(',',$inventoryidorder);
							if (in_array($row['inventoryid'], $HiddenProducts)) {
							  $checked = 'Checked';
							} else {
							  $checked = '';
							}
							?><input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='order_list_includer' value='<?PHP echo $_GET['order_list']; ?>'><br>
							<?php
							echo '</td>';
						}
						if (strpos($value_config, ','."Part #".',') !== FALSE) {
							echo '<td data-title="Part #">' . $row['part_no'] . '</td>';
						}
						if (strpos($value_config, ','."ID #".',') !== FALSE) {
							echo '<td data-title="ID #">' . $row['id_number'] . '</td>';
						}
						if (strpos($value_config, ','."Item SKU".',') !== FALSE) {
							echo '<td data-title="Item SKU">' . $row['item_sku'] . '</td>';
						}
						if (strpos($value_config, ','."Code".',') !== FALSE) {
							echo '<td data-title="Code">' . $row['code'] . '</td>';
						}
						if (strpos($value_config, ','."Description".',') !== FALSE) {
							echo '<td data-title="Desc.">' . $row['part_no'] . '</td>';
						}
						if (strpos($value_config, ','."Category".',') !== FALSE) {
							echo '<td data-title="Tab">' . $row['category'] . '</td>';
						}
						if (strpos($value_config, ','."Subcategory".',') !== FALSE) {
							echo '<td data-title="Sub Tab">' . $row['sub_category'] . '</td>';
						}
						if (strpos($value_config, ','."Name".',') !== FALSE) {
							echo '<td data-title="Name">' . $row['name'] . '</td>';
						}
						if (strpos($value_config, ','."Product Name".',') !== FALSE) {
							echo '<td data-title="Prod. Name">' . $row['product_name'] . '</td>';
						}
						if (strpos($value_config, ','."Type".',') !== FALSE) {
							echo '<td data-title="Tab">' . $row['type'] . '</td>';
						}
						if (strpos($value_config, ','."Color".',') !== FALSE) {
							echo '<td data-title="Color">' . $row['color'] . '</td>';
						}

						/* Remove Kristi's (SEA) access to Product Costs */
						if ( $rookconnect == 'sea' && isset ( $_SESSION['user_name'] ) && $_SESSION['user_name'] == 'kristi' ) {
							// Show nothing
						} else {
							if (strpos($value_config, ','."Cost".',') !== FALSE) {
								echo '<td data-title="Cost">' . $row['cost'] . '</td>';
							}
							if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
							   echo '<td data-title="CAD/Unit">' . $row['cdn_cpu'] . '</td>';
							}
							if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
								echo '<td data-title="USD/Unit">' . $row['usd_cpu'] . '</td>';
							}
							if (strpos($value_config, ','."Average Cost".',') !== FALSE) {
								echo '<td data-title="Avg. Cost">' . ($row['average_cost'] > 0 ? $row['average_cost'] : ($row['cost'] > 0 ? $row['cost'] : ($row['unit_cost'] > 0 ? $row['unit_cost'] : $row['purchase_cost']))) . '</td>';
							}
							if (strpos($value_config, ','."Purchase Cost".',') !== FALSE) {
								echo '<td data-title="Purchase Cost">' . $row['purchase_cost'] . '</td>';
							}
							if (strpos($value_config, ','."USD Invoice".',') !== FALSE) {
								echo '<td data-title="USD Invoice">' . $row['usd_invoice'] . '</td>';
							}
						}

						if (strpos($value_config, ','."COGS".',') !== FALSE) {
							echo '<td data-title="COGS">' . $row['cogs_total'] . '</td>';
						}
						if (strpos($value_config, ','."Vendor".',') !== FALSE) {
							$vendorid = $row['vendorid'];
							$get_vendor = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT  name FROM contacts WHERE contactid='$vendorid'"));
							echo '<td data-title="Vendor">' . $get_vendor['name'] . '</td>';
						}
						if (strpos($value_config, ','."Date Of Purchase".',') !== FALSE) {
							echo '<td data-title="Purchase Date">' . $row['date_of_purchase'] . '</td>';
						}
						if (strpos($value_config, ','."Shipping Rate".',') !== FALSE) {
							echo '<td data-title="Ship Rate">' . $row['shipping_rate'] . '</td>';
						}
						if (strpos($value_config, ','."Shipping Cash".',') !== FALSE) {
							echo '<td data-title="Ship Cash">' . $row['shipping_cash'] . '</td>';
						}
						if (strpos($value_config, ','."Freight Charge".',') !== FALSE) {
							echo '<td data-title="Freight">' . $row['freight_charge'] . '</td>';
						}
						if (strpos($value_config, ','."Exchange Rate".',') !== FALSE) {
							echo '<td data-title="Exchange Rate">' . $row['exchange_rate'] . '</td>';
						}
						if (strpos($value_config, ','."Exchange $".',') !== FALSE) {
							echo '<td data-title="Exchange $">' . $row['exchange_cash'] . '</td>';
						}
						if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) {
							echo '<td data-title="Drum Unit Cost">' . $row['drum_unit_cost'] . '</td>';
						}

						if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) {
							echo '<td data-title="Drum Unit Price">' . $row['drum_unit_price'] . '</td>';
						}
						if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) {
							echo '<td data-title="Tote Unit Cost">' . $row['tote_unit_cost'] . '</td>';
						}
						if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) {
							echo '<td data-title="Tote Unit Price">' . $row['tote_unit_price'] . '</td>';
						}
						if (strpos($value_config, ','."WCB Price".',') !== FALSE) {
							echo '<td data-title="WCB Price">' . $row['wcb_price'] . '</td>';
						}
						if (strpos($value_config, ','."Sell Price".',') !== FALSE) {
							echo '<td data-title="Sell Price">' . $row['sell_price'] . '</td>';
						}
						if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
							echo '<td data-title="Retail">' . $row['final_retail_price'] . '</td>';
						}
						if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) {
							echo '<td data-title="Wholesale">' . $row['wholesale_price'] . '</td>';
						}
						if (strpos($value_config, ','."Commercial Price".',') !== FALSE) {
							echo '<td data-title="Comm. Price">' . $row['commercial_price'] . '</td>';
						}
						if (strpos($value_config, ','."Client Price".',') !== FALSE) {
							echo '<td data-title="Client Price">' . $row['client_price'] . '</td>';
						}
						if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) {
							 echo '<td data-title="Client Price">' . $row['purchase_order_price'] . '</td>';
						}
						if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) {
							 echo '<td data-title="Client Price">' . $row['sales_order_price'] . '</td>';
						}
						if (strpos($value_config, ','."Suggested Retail Price".',') !== FALSE) {
							echo '<td data-title="Suggested Retail Price">' . $row['suggested_retail_price'] . '</td>';
						}
						if (strpos($value_config, ','."Rush Price".',') !== FALSE) {
							echo '<td data-title="Rush Price">' . $row['rush_price'] . '</td>';
						}
						if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) {
							echo '<td data-title="Include in '.SALES_ORDER_TILE.'">';
							?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_so'] !== '' && $row['include_in_so'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='sales_order_includer' value='1'><br>
							<?php
							echo '</td>';
						}
						if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) {
							echo '<td data-title="Include in P.O.S.">';
							?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_pos'] !== '' && $row['include_in_pos'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='point_of_sale_includer' value='1'><br>
							<?php
							echo '</td>';
						}
						if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) {
							echo '<td data-title="Include in Purchase Orders">';
							?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_po'] !== '' && $row['include_in_po'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='purchase_order_includer' value='1'><br>
							<?php
							echo '</td>';
						}

						if (strpos($value_config, ','."Include in Product".',') !== FALSE) {
							echo '<td data-title="Include in Product">';
							if($row['productid'] == '') {
							?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_product'] !== '' && $row['include_in_product'] !== NULL && $row['include_in_product'] == 1) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='product_includer' value='1'><br>
							<?php
							} else {
								echo 'Already Included';
							}
							echo '</td>';
						}

						if (strpos($value_config, ','."Preferred Price".',') !== FALSE) {
							echo '<td data-title="Pref. Price">' . $row['preferred_price'] . '</td>';
						}
						if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
							echo '<td data-title="Admin Price">' . $row['admin_price'] . '</td>';
						}
						if (strpos($value_config, ','."Web Price".',') !== FALSE) {
							echo '<td data-title="Web Price">' . $row['web_price'] . '</td>';
						}
						if (strpos($value_config, ','."Commission Price".',') !== FALSE) {
							echo '<td data-title="Commission">' . $row['commission_price'] . '</td>';
						}
						if (strpos($value_config, ','."MSRP".',') !== FALSE) {
							echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
						}
						if (strpos($value_config, ','."Unit Price".',') !== FALSE) {
							echo '<td data-title="Unit Price">' . $row['unit_price'] . '</td>';
						}
						if (strpos($value_config, ','."Unit Cost".',') !== FALSE) {
							echo '<td data-title="Unit Cost">' . $row['unit_cost'] . '</td>';
						}
						if (strpos($value_config, ','."Rent Price".',') !== FALSE) {
							echo '<td data-title="Rent Price">' . $row['rent_price'] . '</td>';
						}
						if (strpos($value_config, ','."Rental Days".',') !== FALSE) {
							echo '<td data-title="Rent Days">' . $row['rental_days'] . '</td>';
						}
						if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) {
							echo '<td data-title="Rent Weeks">' . $row['rental_weeks'] . '</td>';
						}
						if (strpos($value_config, ','."Rental Months".',') !== FALSE) {
							echo '<td data-title="Rent Months">' . $row['rental_months'] . '</td>';
						}
						if (strpos($value_config, ','."Rental Years".',') !== FALSE) {
							echo '<td data-title="Rent Years">' . $row['rental_years'] . '</td>';
						}
						if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) {
							echo '<td data-title="Reminder">' . $row['reminder_alert'] . '</td>';
						}
						if (strpos($value_config, ','."Daily".',') !== FALSE) {
							echo '<td data-title="Daily">' . $row['daily'] . '</td>';
						}
						if (strpos($value_config, ','."Weekly".',') !== FALSE) {
							echo '<td data-title="Weekly">' . $row['weekly'] . '</td>';
						}
						if (strpos($value_config, ','."Monthly".',') !== FALSE) {
							echo '<td data-title="Monthly">' . $row['monthly'] . '</td>';
						}
						if (strpos($value_config, ','."Annually".',') !== FALSE) {
							echo '<td data-title="Annual">' . $row['annually'] . '</td>';
						}
						if (strpos($value_config, ','."#Of Days".',') !== FALSE) {
							echo '<td data-title="# Days">' . $row['total_days'] . '</td>';
						}
						if (strpos($value_config, ','."#Of Hours".',') !== FALSE) {
							echo '<td data-title="# Hours">' . $row['total_hours'] . '</td>';
						}
						if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) {
							echo '<td data-title="# Kilometers">' . $row['total_km'] . '</td>';
						}
						if (strpos($value_config, ','."#Of Miles".',') !== FALSE) {
							echo '<td data-title="# Miles">' . $row['total_miles'] . '</td>';
						}
						if (strpos($value_config, ','."Markup By $".',') !== FALSE) {
							echo '<td data-title="Markup $">' . $row['markup'] . '</td>';
						}
						if (strpos($value_config, ','."Markup By %".',') !== FALSE) {
							echo '<td data-title="Markup %">' . $row['markup_perc'] . '</td>';
						}

						if (strpos($value_config, ','."GL Revenue".',') !== FALSE) {
							echo '<td data-title="GL Revenue">' . $row['revenue'] . '</td>';
						}
						if (strpos($value_config, ','."GL Assets".',') !== FALSE) {
						   echo '<td data-title="GL Assets">' . $row['asset'] . '</td>';
						}

						if (strpos($value_config, ','."Current Stock".',') !== FALSE) {
							echo '<td data-title="Stock">' . $row['current_stock'] . '</td>';
						}
						if (strpos($value_config, ','."Current Inventory".',') !== FALSE) {
							echo '<td data-title="Inventory">' . $row['current_inventory'] . '</td>';
						}
						if (strpos($value_config, ','."Quantity".',') !== FALSE) {
							echo '<td data-title="Quantity">' . $row['quantity'] . '</td>';
						}
						if (strpos($value_config, ','."Expected".',') !== FALSE) {
							echo '<td data-title="Expected">' . $row['expected_inventory'] . '</td>';
						}
						if (strpos($value_config, ','."Received".',') !== FALSE) {
							echo '<td data-title="Received"><input type="number" min=0 step="any" value="' . $row['quantity'] . '" data-inventory="'.$row['inventoryid'].'" data-target=\'[data-title="Discrepancy"]\' data-ticket="'.$ticket['ticketid'].'" name="quantity" class="form-control"></td>';
						}
						if (strpos($value_config, ','."Discrepancy".',') !== FALSE) {
							echo '<td data-title="Discrepancy">' . ($row['quantity'] - $row['expected_inventory']) . '</td>';
						}
						if (strpos($value_config, ','."Variance".',') !== FALSE) {
							echo '<td data-title="Variance">' . $row['inv_variance'] . '</td>';
						}
						if (strpos($value_config, ','."Write-offs".',') !== FALSE) {
							echo '<td data-title="Write Offs">' . $row['write_offs'] . '</td>';
						}

						if (strpos($value_config, ','."Buying Units".',') !== FALSE) {
							echo '<td data-title="Buying Units">' . $row['buying_units'] . '</td>';
						}
						if (strpos($value_config, ','."Selling Units".',') !== FALSE) {
							echo '<td data-title="Selling Units">' . $row['selling_units'] . '</td>';
						}
						if (strpos($value_config, ','."Stocking Units".',') !== FALSE) {
							echo '<td data-title="Stocking Units">' . $row['stocking_units'] . '</td>';
						}
						if (strpos($value_config, ','."Min Amount".',') !== FALSE) {
							echo '<td data-title="Min Amount">' . $row['min_amount'] . '</td>';
						}
						if (strpos($value_config, ','."Max Amount".',') !== FALSE) {
							echo '<td data-title="Max Amount">' . $row['max_amount'] . '</td>';
						}

						if (strpos($value_config, ','."Location".',') !== FALSE) {
							echo '<td data-title="Location">' . $row['location'] . '</td>';
						}
						if (strpos($value_config, ','."LSD".',') !== FALSE) {
							echo '<td data-title="LSD">' . $row['lsd'] . '</td>';
						}
						if (strpos($value_config, ','."Size".',') !== FALSE) {
							echo '<td data-title="Size">' . $row['size'] . '</td>';
						}
						if (strpos($value_config, ','."Weight".',') !== FALSE) {
							echo '<td data-title="Weight">' . $row['weight'] . '</td>';
						}
						if (strpos($value_config, ','."Min Max".',') !== FALSE) {
							echo '<td data-title="Min / Max">' . $row['min_max'] . '</td>';
						}
						if (strpos($value_config, ','."Min Bin".',') !== FALSE) {
							echo '<td data-title="Min Bin">' . $row['min_bin'] . '</td>';
						}
						if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
							echo '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
						}
						if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
							echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
						}
						if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
							echo '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
						}
						if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
							echo '<td data-title="Quote Desc.">' . html_entity_decode($row['quote_description']) . '</td>';
						}
						if (strpos($value_config, ','."Status".',') !== FALSE) {
							echo '<td data-title="Status">' . $row['status'] . '</td>';
						}
						if (strpos($value_config, ','."Display On Website".',') !== FALSE) {
							//echo '<td data-title="On Website">' . $row['display_website'] . '</td>';
							/*$intv_id				= $row['inventoryid'];
							$get_website_config		= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT display_website FROM inventory WHERE inventoryid='$intv_id'"));
							$display_website_config	= ',' . $get_website_config['display_website'] . ',';
							$checked				= ( strpos ( $display_website_config, ',' . "Yes" . ',' ) !== FALSE) ? $checked=" checked" : $checked="";

							echo '<td data-title="On Website" align="center"><input style="height:20px; width:20px;" type="checkbox" name="display_website[]" class="display_website" value="' . $intv_id . '"' . $checked . '></td>';*/ ?>
							<td data-title="On Website" align="center"><input type='checkbox' style='width:20px; height:20px;' <?php echo ( $row['display_website'] == 'Yes' && $row['display_website'] !== NULL ) ? "checked='checked'" : ""; ?> id='<?php echo $row['inventoryid']; ?>'  name='' class='display_website' value='<?php //echo ( $row['display_website'] == 'Yes' && $row['display_website'] !== NULL ) ? "Yes" : "No"; ?>'></td><?php
						}
						if (strpos($value_config, ','."Featured On Website".',') !== FALSE) {
							$featured_item = ( $row['featured']=='0' ) ? 'No' : 'Yes';
							echo '<td data-title="New">' . $featured_item . '</td>';
						}
						if (strpos($value_config, ','."New Item".',') !== FALSE) {
							$new_item = ( $row['new']=='0' ) ? 'No' : 'Yes';
							echo '<td data-title="New">' . $new_item . '</td>';
						}
						if (strpos($value_config, ','."Item On Sale".',') !== FALSE) {
							$on_sale = ( $row['sale']=='0' ) ? 'No' : 'Yes';
							echo '<td data-title="On Sale">' . $on_sale . '</td>';
						}
						if (strpos($value_config, ','."Item On Clearance".',') !== FALSE) {
							$on_clearance = ( $row['clearance']=='0' ) ? 'No' : 'Yes';
							echo '<td data-title="Clearance">' . $on_clearance . '</td>';
						}
						if (strpos($value_config, ','."Notes".',') !== FALSE) {
							echo '<td data-title="Notes">' . $row['note'] . '</td>';
						}
						if (strpos($value_config, ','."Comments".',') !== FALSE) {
							echo '<td data-title="Comments">' . $row['comment'] . '</td>';
						}

						if (strpos($value_config, ','."History".',') !== FALSE) {
							echo '<td data-title="History"><a class="iframe_open" href="#" data-id="'.$row['inventoryid'].'">View</a></td>';
						}
						echo '<td data-title="Function">';
						if(vuaed_visible_function($dbc, 'inventory') == 1) {
							echo '<a href=\'add_inventory.php?inventoryid='.$row['inventoryid'].'\'>Edit</a> | ';
							echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&inventoryid='.$row['inventoryid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
						}
						if(strpos($value_config, 'Expected') !== FALSE) {
							echo '<label class="form-checkbox"><input type="checkbox" '.($row['expected_inventory'] == $row['quantity'] ? 'checked onclick="return false;"' : ' onchange="$(this).closest(\'tr\').find(\'[name=quantity]\').val(\''.$row['expected_inventory'].'\').change();"').'> Received Expected</label>';
						}
						echo '</td>';

						echo "</tr>";
					}
				echo "</table>
				<button class='btn brand-btn pull-right' onclick=\"overlayIFrameSlider('../Ticket/add_notes_and_documents.php?edit=".$ticket['ticketid']."', '75%', true, true); return false;\">Add Notes and Documents</button>
				<div class='clearfix'></div>
			</div>";
			$current_ticketid = $ticket['ticketid'];
		}
	} else if($_GET['warehouse'] != '') {
		echo '<h3>No results found</h3>';
	} else {
		echo 'Please select a '.TICKET_NOUN.' status.';
	} ?>
	</div>
</div>