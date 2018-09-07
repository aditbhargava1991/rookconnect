<?php
/*
Customer Listing
*/
include ('../include.php');
?>
<script type="text/javascript">
$(document).ready(function() {
	$('input.purchase_order_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=include_in_orders&type=po&name=product&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
            location.reload();
		}
		});
	});

	$('input.inventory_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = 0; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=include_in_orders&type=inventory&name=product&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
            location.reload();
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
		url: "../ajax_all.php?fill=include_in_orders&type=so&name=product&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
            location.reload();
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
		url: "../ajax_all.php?fill=include_in_orders&type=pos&name=product&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
            location.reload();
		}
		});
	});
});
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('products');
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

		<div class="col-sm-10"><h1>Products Dashboard</h1></div>
		<div class="col-sm-2 double-gap-top">
			<?php
				if(config_visible_function($dbc, 'products') == 1) {
					echo '<a href="field_config_products.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
					echo '<span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
				}
			?>
        </div>
		<div class="clearfix"></div>

		<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
			<div class="col-sm-11"><span class="notice-name">NOTE:</span>
			In this section your business can properly outline all product headings and descriptions for quotes, ticketing systems, work orders, etc. Assigning your businesses products by Type, Category and Heading will enable reporting per product. Price points for products are added in the rate card section and may or may not be visible here. Products added here will display in the rate card; products added to the rate card may or may not be visible here.</div>
			<div class="clearfix"></div>
		</div>

        <form name="form_sites" method="post" action="" class="form-inline" role="form">

            <center>
            <div class="form-group">
                <label for="site_name" class="col-sm-5 control-label">Search By Any:</label>
                <div class="col-sm-6">
                <?php if(isset($_POST['search_vendor_submit'])) { ?>
                    <input type="text" name="search_vendor" class="form-control" value="<?php echo $_POST['search_vendor']?>">
                <?php } else { ?>
                    <input type="text" name="search_vendor" class="form-control">
                <?php } ?>
                </div>
            </div>

            &nbsp;<button type="submit" name="search_vendor_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
            <button type="submit" name="display_all_vendor" value="Display All" class="btn brand-btn mobile-block">Display All</button>
            </center>

                <?php
					if(vuaed_visible_function($dbc, 'products') == 1) {
						echo '<a href="add_products.php" class="btn brand-btn mobile-block pull-right double-gap-top">Add Product</a>'; ?>
						<span class="popover-examples double-gap-top pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Product."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
						echo '<a href="add_inventory_multiple.php" class="btn brand-btn mobile-block pull-right double-gap-top">Add Multiple Products</a>'; ?>
						<span class="popover-examples double-gap-top pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="Click here to add more than one Product at a time by exporting a list."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
					}
                ?>

            <div id="no-more-tables">

            <?php
            //Search
            $vendor = '';
            if (isset($_POST['search_vendor_submit'])) {
                if (isset($_POST['search_vendor'])) {
                    $vendor = $_POST['search_vendor'];
                }
            }
            if (isset($_POST['display_all_vendor'])) {
                $vendor = '';
            }

			include('product_table.php');
            // Pagination Finish //
            if(vuaed_visible_function($dbc, 'products') == 1) {
            echo '<a href="add_products.php" class="btn brand-btn mobile-block pull-right">Add Product</a>'; ?>
			<span class="popover-examples pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Product."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
            }

            ?>
        </form>

        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
