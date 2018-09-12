<?php include_once('../include.php');
$strict_view = strictview_visible_function($dbc, 'inventory');
$tile_security = get_security($dbc, 'inventory');
if($strict_view > 0) {
    $tile_security['edit'] = 0;
    $tile_security['config'] = 0;
}
$match_business = '';
if(!empty(MATCH_CONTACTS)) {
	$match_business = " AND (`tickets`.`businessid` IN (".MATCH_CONTACTS.") OR `tickets`.`businessid` IS NULL)";
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
			var target = $(this).data('detail');
			var title = $(this).parents('tr').children(':first').text();
		   $('#iframe_instead_of_window').attr('src', 'history.php?detail='+target+'&inventoryid='+id);
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
function setPallet(item) {
	if(item.value == "ADD_NEW") {
		$(item).closest('span').hide().closest('td').find('input').show().focus();
	} else {
		$.post('inventory_ajax.php?action=dashboard_update',{ id: $(item).data('id'), name: 'pallet', value: item.value });
	}
}
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
	<form name="form_sites" method="GET" action="" class="form-inline gap-top" role="form">
		<?php

			if(isset($_GET['order_list']) || $total_inv > 0) {
				echo '<br>';
			}

			if($inventory_navigation_position == 'top') {
				$category = $_GET['category'];
				$tabs = get_config($dbc, 'inventory_tabs');
				$each_tab = explode('#*#', $tabs);

				$active_all = '';
				$active_bom = '';
				if(empty($_GET['category']) || $_GET['category'] == 'Top' && $currentlist != 'on') {
					$active_all = 'active_tab';
				}
				echo "<div class='pull-left tab gap-bottom'><a href='inventory.php?category=Top".$order_list."'><button type='button' class='btn brand-btn ".$active_all."' >Last 25 Added</button></a></div>";

				if($dropdownornot !== 'true') {
					foreach ($each_tab as $cat_tab) {
						$url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
						$active_daily = '';
						if((!empty($_GET['category'])) && ($_GET['category'] == $url_tab) && (!isset($_GET['currentlist']))) {
							$active_daily = 'active_tab';
							$active_cat = $cat_tab;
						}
						echo "<div class='pull-left tab'><a href='inventory.php?category=".$url_tab.$order_list."'><button type='button' class='btn brand-btn ".$active_daily."' >".$cat_tab."</button></a></div>";
					}
				} else { ?>
					<div class="form-group  mobile-100-container cate_fitter" style='width:80%; margin:auto;'>
						<select name="search_category" class="chosen-select-deselect form-control mobile-100-pull-right category_actual">
						  <option value="" selected>Select a Tab</option>
						  <?php
							if ($category == "dispall_31V2irt2u3e5S3s1f2ADe3_31") {
								$xx = 'selected="selected"';
							} else {
								$xx = '';
							}
							echo "<option ".$xx." value='inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31".$order_list."'>Display All</option>";
							$sql = mysqli_query($dbc, "SELECT * FROM inventory WHERE deleted = 0 GROUP BY category ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name");
							/*
							Show only Tabs : Disabled to show all categories instead.
							$tabs = html_entity_decode(get_config($dbc, 'inventory_tabs'));
							$each_tab = explode('#*#', $tabs);
							foreach ($each_tab as $cat_tab) {
								$url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
								$active_daily = '';
								if((!empty($_GET['category'])) && ($_GET['category'] == $cat_tab) && (!isset($_GET['currentlist']))) {
									$active_daily = 'selected';
								}
								echo "<option value='inventory.php?category=".$url_tab.$order_list."' ".$active_daily." >".$cat_tab."</option>";
							}*/
							while($row = mysqli_fetch_assoc($sql)){
								$active_daily = '';
								$cat_tab = $row['category'];
								$url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
								if((!empty($_GET['category'])) && ($_GET['category'] == $url_tab) && (!isset($_GET['currentlist']))) {
									$active_daily = 'selected';
									$active_cat = $cat_tab;
								}
								echo "<option ".$active_daily." value='inventory.php?category=".$url_tab.$order_list."'>".$cat_tab."</option>";
							}
						  ?>
						</select>
					</div>
				<?php }
			} ?>
			<div class="clearfix"></div>

			<?php if($dropdownornot == 'true' && $inventory_navigation_position != 'top') { ?>
				<div class="col-sm-2 col-xs-6">
					<label for="search_inventory" style="width:100%; text-align:right; padding-top:5px;">Tab:</label>
				</div>
				<div class="col-sm-4 col-xs-6" style="margin-bottom:10px !important;">
					<select data-placeholder="Select a Tab..." name="search_category" class="chosen-select-deselect form-control mobile-100-pull-right category_actual">
					  <option value="" selected>Select a Tab</option>
					  <?php
						if ($category == "dispall_31V2irt2u3e5S3s1f2ADe3_31") {
							$xx = 'selected="selected"';
						} else {
							$xx = '';
						}
						echo "<option ".$xx." value='inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31".$order_list."'>Display All</option>";
						$sql = mysqli_query($dbc, "SELECT * FROM inventory WHERE deleted = 0 GROUP BY category ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name");
						/*
						Show only Tabs : Disabled to show all categories instead.
						$tabs = html_entity_decode(get_config($dbc, 'inventory_tabs'));
						$each_tab = explode('#*#', $tabs);
						foreach ($each_tab as $cat_tab) {
							$url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
							$active_daily = '';
							if((!empty($_GET['category'])) && ($_GET['category'] == $cat_tab) && (!isset($_GET['currentlist']))) {
								$active_daily = 'selected';
							}
							echo "<option value='inventory.php?category=".$url_tab.$order_list."' ".$active_daily." >".$cat_tab."</option>";
						}*/
						while($row = mysqli_fetch_assoc($sql)){
							$active_daily = '';
							$cat_tab = $row['category'];
							$url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
							if((!empty($_GET['category'])) && ($_GET['category'] == $url_tab) && (!isset($_GET['currentlist']))) {
								$active_daily = 'selected';
							}
							echo "<option ".$active_daily." value='inventory.php?category=".$url_tab.$order_list."'>".$cat_tab."</option>";
						}
					  ?>
					</select>
				</div>
				<div class="clearfix"></div>
			<?php } ?>

			<?php if($_GET['no_search'] != 'true') { ?>
            <!--
				<div class='col-sm-2 col-xs-6'>
					<label for="search_category" style="width:100%; text-align:right; padding-top:5px;">Search <?= $active_cat ?>:</label>
				</div>
				<div class="col-sm-4 col-xs-6" style="margin-bottom:10px !important;">
					<input  type="hidden" name="category" value="<?= $_GET['category'] ?>" class="form-control" />
					<input  type="text" name="search_category" value="<?= $_GET['search_category'] ?>" class="form-control" />
				</div>

                <div class="tab-container">
					<div class="tab pull-left"><button type="submit" name="search_inventory_submit" value="Search" class="btn brand-btn mobile-block mobile-100">Search</button></div>
					<div class="tab pull-left"><button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block mobile-100">Display All</button></div>
				<?php
				 if (isset($_GET['order_list'])) {  ?>
					<div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 pull-sm-right pull-xs-right" style="padding-right:12px;">
						<div class='selectall selectbutton sel2' title='This will select all PDFs on the current page.' style='cursor:pointer;text-decoration:underline;'>Select All</div>
					</div>
			  <?php } ?>
				</div>
                -->
			<?php } ?>
	<div class="clearfix"></div>
</form>
<div <?= $_GET['no_search'] == 'true' ? '' : 'id="no-more-tables"' ?>>
	<?php include('inventory_table.php');
	if(vuaed_visible_function($dbc, 'inventory') == 1 && $_GET['no_search'] != 'true') {
		if($category != 'Top' && $category != 'dispall_31V2irt2u3e5S3s1f2ADe3_31') {
			echo '<a href="add_inventory.php?category='.$category.$order_list.'" class="btn brand-btn mobile-block gap-bottom pull-right">Add '.INVENTORY_NOUN.'</a>';
		}
	}
	//echo display_filter('inventory.php');

	?>
	</div>
</div>
