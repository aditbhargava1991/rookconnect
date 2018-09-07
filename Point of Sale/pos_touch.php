<?php
/*
 * Point of Sale - Touch Screen
*/

include ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
?>

<style type="text/css">
/* Hide HTML5 Up and Down arrows. */
input[type="number"]::-webkit-outer-spin-button, input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] { -moz-appearance: textfield; }
.button { cursor:pointer; }
</style>

<script>
	$(document).ready(function(){
		$('#discount_percent_block').hide();
		$('#discount_value_block').hide();
		$('#calc_done').hide();
		$('#credit_done').hide();

		$('#select_customer_block').fadeIn();
		$('#select_customer').addClass('active_tab');
		$('#create_customer_block').hide();

		$('#discount_percent_block').fadeIn();
		$('#disc_percent').addClass('active_tab');
		$('#discount_value_block').hide();

		$('#email_select_customer_block').fadeIn();
		$('#email_select_customer').addClass('active_tab');
		$('#email_create_customer_block').hide();
		$('#email_only_block').hide();

		$('#back').click(function() {
			history.back(1);
		});

		$(".number").click(function () {
			var number = $(this).data('number');
			$("#amount_tentered, #discount_value").val(function() {
				return this.value + number;
			});
		});

		$('#visa, #master, #amex').click(function(event) {
			$('#visa, #master, #amex').removeClass('active_tab');
			$(this).addClass('active_tab');

			var href = '?complete=yes&payment_type=';
			$('#credit_url').attr('href', href + event.target.id);
			$('#credit_done').show();
		});
	});


	/* ----- Sticky sidebar ----- */
	$(window).scroll(function() {
		if ($(this).scrollTop() > 260) {
			$('#sidebar').addClass("sticky");
			$('#main').addClass("main-fix");
		}
		else {
			$('#sidebar').removeClass("sticky");
			$('#main').removeClass("main-fix");
		}
	});


	/* ----- Customer Select ----- */
	// Existing customer or new customer selection
	function customerSelection(sel) {
		var id = sel.id;

		if ( id=='select_customer' ) {
			$('#select_customer_block').fadeIn();
			$('#select_customer').addClass('active_tab');
			$('#create_customer').removeClass('active_tab');
			$('#create_customer_block').hide();
		}
		if ( id=='create_customer' ) {
			$('#create_customer_block').fadeIn();
			$('#create_customer').addClass('active_tab');
			$('#select_customer').removeClass('active_tab');
			$('#select_customer_block').hide();
		}
	}

	// Existing customer selected
	function customerSelected() {
		custid = $('#customerid').val();

		$.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=posTouchCustomerSelected&custid="+custid,
			dataType: "html",
			success: function(response){
				window.location.replace("pos_touch.php?classification=services");
			}
		});
	}


	/* ----- Calculate Balance for Cash Payments ----- */
	function calculateChange() {
		var amt_tentered	= parseFloat( $('#amount_tentered').val() );
		var amt_due			= parseFloat( $('#amount_due').val() );

		if ( amt_due > amt_tentered ) {
			alert('Please make sure the amount tendered can cover the amount due.');
		} else {
			var amt_change = amt_tentered - amt_due;
			$('#amount_change').val(amt_change.toFixed(2));
			$('#calc_done').fadeIn('slow');
		}
	}


	/* ----- Change Order Item Quantity ----- */
	function changeQuantity(sel) {
		var quantity = $('#quantity').val();
		var id = sel.id;
		if (id=='add') {
			quantity++;
			$('#quantity').val(quantity);
		}
		if (id=='remove' && quantity>=2) {
			quantity--;
			$('#quantity').val(quantity);
		}
	}


	/* ----- Discount ----- */
	// Select Discount Type
	function selectDiscountType(sel) {
		var id = sel.id;

		if ( id=='disc_percent' ) {
			$('#discount_percent_block').fadeIn();
			$('#discount_value_block').hide();
		} else {
			$('#discount_value_block').fadeIn();
			$('#discount_percent_block').hide();
		}
	}

	// Add A Discount To The Order
	function addDiscount(sel) {
		var checktotal	= $('#h_checktotal').val();
		var orderid		= $('#h_orderid').val();
		var id			= sel.id;
		var arr			= id.split('_');
		var discount	= arr[1];
		var gst			= $('#h_gst_rate').val();
		var pst			= $('#h_pst_rate').val();
		var disc_type	= '%';

		// Discount percentage
		if (id=='discount_ok') {
			disc_type	= '%';
			discount	= $('#discount').val();
		}
		// Discount value
		if (id=='add_discount_value') {
			disc_type	= '$';
			discount	= $('#discount_value').val();
		}

		if (checktotal>0) {
			$.ajax({
				type: "GET",
				url: "pos_touch_ajax_all.php?fill=posTouchAddDiscount&orderid="+orderid+"&disc_type="+disc_type+"&discount="+discount+"&gst="+gst+"&pst="+pst,
				dataType: "html",
				success: function(response){
					window.location.replace("pos_touch.php");
				}
			});
		} else {
			alert('Please add at least one item before adding a discount.');
		}
	}

  // Add A Gift Card discount To The Order
	function addGF(sel) {
		var checktotal	= $('#h_checktotal').val();
		var orderid		= $('#h_orderid').val();
		var gf_number			= $('#gf').val();
		var gst			= $('#h_gst_rate').val();
		var pst			= $('#h_pst_rate').val();

		if (checktotal>0) {
			$.ajax({
				type: "GET",
				url: "pos_touch_ajax_all.php?fill=posTouchAddGF&orderid="+orderid+"&gf_number="+gf_number+"&gst="+gst+"&pst="+pst,
				dataType: "html",
				success: function(response){
          if(response == 'na') {
            alert('Gift Card Number applied is either not valid or already been used.');
            return false;
          }
					window.location.replace("pos_touch.php");
				}
			});
		} else {
			alert('Please add at least one item before adding a discount.');
		}
	}

	// Add A Discount % Manually
	function changeDiscount(sel) {
		var discount = $('#discount').val();
		var id = sel.id;
		if (id=='increase') {
			discount++;
			$('#discount').val(discount);
		}
		if (id=='decrease' && discount>=2) {
			discount--;
			$('#discount').val(discount);
		}
	}

	// Remove The Discount From The Order
	function removeDiscount() {
		// Remove icon is shown only when there is a discount added
		var orderid	= $('#h_orderid').val();
		var gst		= $('#h_gst_rate').val();
		var pst		= $('#h_pst_rate').val();

		$.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=posTouchRemoveDiscount&orderid="+orderid+"&gst="+gst+"&pst="+pst,
			dataType: "html",
			success: function(response){
				location.reload();
			}
		});
	}


	/* ----- Coupon ----- */
	// Check if there is an order to add the coupon
	function checkValidCouponClick() {
		var orderid	= $('#h_orderid').val();

		if ( orderid=='' || typeof orderid==='undefined' ) {
			alert('Please create an order first.');
		} else {
			window.location.replace('pos_touch.php?coupon=yes');
		}
	}

	// Add A Coupon To The Order
	function addCoupon(sel) {
		var checktotal	= $('#h_checktotal').val();
		var orderid		= $('#h_orderid').val();
		var id			= sel.id;
		var arr			= id.split('_');
		var couponid	= arr[1];
		var gst			= $('#h_gst_rate').val();
		var pst			= $('#h_pst_rate').val();
		var inv_pricing	= $('#h_inv_pricing').val();

		if (checktotal>0) {
			$.ajax({
				type: "GET",
				url: "pos_touch_ajax_all.php?fill=posTouchAddCoupon&orderid="+orderid+"&couponid="+couponid+"&gst="+gst+"&pst="+pst,
				dataType: "html",
				success: function(response){
					window.location.replace("pos_touch.php?classification=inventory&inv_pricing="+inv_pricing);
				}
			});
		} else {
			alert('Please add at least one item before adding a coupon.');
		}
	}

	// Remove Coupon From The Order
	function removeCoupon(sel) {
		// Remove icon is shown only when there is a coupon added
		var orderid		= $('#h_orderid').val();
		var id			= sel.id;
		var arr			= id.split('_');
		var couponid	= arr[1];
		var gst			= $('#h_gst_rate').val();
		var pst			= $('#h_pst_rate').val();

		$.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=posTouchRemoveCoupon&orderid="+orderid+"&couponid="+couponid+"&gst="+gst+"&pst="+pst,
			dataType: "html",
			success: function(response){
				location.reload();
			}
		});
	}


	/* ----- Email Receipt ----- */
	// Check if the order is paid before emailing the receipt
	function checkValidEmailReceiptClick() {
		var orderid	= $('#h_orderid').val();
		var posid	= $('#h_posid').val();

		if ( orderid=='' || typeof orderid==='undefined' ) {
			alert('Please create an order first.');
		} else if ( posid=='' || typeof posid==='undefined' ) {
			alert('Please pay for the order before emailing the receipt.');
			window.location.replace('pos_touch.php?pay=yes&pay_type=cash');
		} else {
			window.location.replace('pos_touch.php?email_reciept=yes&posid='+posid);
		}
	}

	// Select email receipt sub tabs
	function emailReceiptSelection(sel) {
		var id = sel.id;

		if ( id=='email_select_customer' ) {
			$('#email_select_customer_block').fadeIn();
			$('#email_select_customer').addClass('active_tab');
			$('#email_create_customer').removeClass('active_tab');
			$('#email_only').removeClass('active_tab');
			$('#email_create_customer_block').hide();
			$('#email_only_block').hide();
		}
		if ( id=='email_create_customer' ) {
			$('#email_create_customer_block').fadeIn();
			$('#email_create_customer').addClass('active_tab');
			$('#email_select_customer').removeClass('active_tab');
			$('#email_only').removeClass('active_tab');
			$('#email_select_customer_block').hide();
			$('#email_only_block').hide();
		}
		if ( id=='email_only' ) {
			$('#email_only_block').fadeIn();
			$('#email_only').addClass('active_tab');
			$('#email_select_customer').removeClass('active_tab');
			$('#email_create_customer').removeClass('active_tab');
			$('#email_select_customer_block').hide();
			$('#email_create_customer_block').hide();
		}
		if ( id=='email_dont' ) {
			window.location.replace('pos_touch.php');
		}
	}

	// Email the receipt
	function emailReceipt(sel) {
		var id			= sel.id;
		var posid		= $('#h_posid').val();
		var attachment	= $('#h_attachment').val();
		var to_email	= '';
		var to_client	= '';
		var query_var	= '';

		if ( posid=='' || typeof posid==='undefined' ) {
			alert('Please pay for the order before emailing the receipt.');
			window.location.replace('pos_touch.php?pay=yes&pay_type=cash');
		}

		if ( id=='email_only_button' ) {
			to_email = $('#to_email').val();

			if ( to_email=='' || typeof to_email==='undefined' ) {
				alert('Please enter a valid email address to email the receipt to.');
			} else {
				query_var = '&to_email='+to_email;
			}

		}

		if ( id=='email_existing_button' ) {
			to_client = $('#customerid').val();

			if ( to_client=='' || typeof to_client==='undefined' ) {
				alert('Please select a customer to email the receipt to.');
			} else {
				query_var = '&to_client='+to_client;
			}

		}

		if ( id=='email_selected_button' ) {
			to_client = $('#h_custid').val();

			if ( to_client=='' || typeof to_client==='undefined' ) {
				alert('Please select a customer to email the receipt to.');
			} else {
				query_var = '&to_client='+to_client;
			}

		}

		if ( query_var != '' ) {
			$.ajax({
				type: "GET",
				url: "pos_touch_ajax_all.php?fill=posTouchEmailReceipt&attachment="+attachment+query_var,
				dataType: "html",
				success: function(response){
					alert('Receipt emailed to the customer.');
					window.location.replace("pos_touch.php");
				}
			});
		}
	}


	/* ----- Get Inventory from Barcode Scanning ----- */
    function barcodeScanner(sel) {
        var code = sel.value;
        var pricing = $(sel).data('pricing');

        $.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=barcodeScanner&code="+code,
			dataType: "html",
			success: function(response){
				result = response.split('*#*');
                window.location.replace("pos_touch.php?classification=inventory&inv_pricing="+pricing+"&inv_category="+result[1]+"&invid="+result[0]);
			}
		});
    }


	/* ----- Add An Inventory Item To The Order ----- */
	function addInventory(sel) {
		var id			= sel.id;
		var arr			= id.split('_');
		var invid		= arr[1];
		var inv_pricing	= $('#h_inv_pricing').val();
		var quantity	= $('#quantity').val();
		var orderid		= $('#h_orderid').val();
		var gst			= $('#h_gst_rate').val();
		var pst			= $('#h_pst_rate').val();

		if ( orderid == '' || typeof orderid === 'undefined' ) {
			orderid = 0;
		}

		$.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=posTouchAddInventoryPrice&orderid="+orderid+"&invid="+invid+"&inv_pricing="+inv_pricing+"&quantity="+quantity+"&gst="+gst+"&pst="+pst,
			dataType: "html",
			success: function(response){
				window.location.replace("pos_touch.php?classification=inventory&inv_pricing="+inv_pricing);
			}
		});
	}


	/* ----- Add A Product To The Order ----- */
	function addProduct(sel) {
		var id			= sel.id;
		var arr			= id.split('_');
		var prodid		= arr[1];
		var name		= $('#h_type').val() + ' ' + $('#h_product').val();
		var quantity	= $('#quantity').val();
		var orderid		= $('#h_orderid').val();
		var gst			= $('#h_gst_rate').val();
		var pst			= $('#h_pst_rate').val();

		if ( orderid == '' || typeof orderid === 'undefined' ) {
			orderid = 0;
		}

		$.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=posTouchAddPrice&orderid="+orderid+"&name="+name+"&prodid="+prodid+"&quantity="+quantity+"&gst="+gst+"&pst="+pst,
			dataType: "html",
			success: function(response){
				window.location.replace("pos_touch.php?classification=products");
			}
		});
	}


	/* ----- Get Product from Barcode Scanning ----- */
    function barcodeScannerProducts(sel) {
        var code = sel.value;

        $.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=barcodeScannerProducts&code="+code,
			dataType: "html",
			success: function(response){
				result = response.split('*#*');
                window.location.replace("pos_touch.php?classification=products&product="+response);
			}
		});
    }


	/* ----- Add A Service To The Order ----- */
	function addService(sel) {
		var id			= sel.id;
		var arr			= id.split('_');
		var servid		= arr[1];
		var name		= $('#h_service').val();
        var price       = $('#serv_price_edited').val();
		var quantity	= $('#quantity').val();
		var orderid		= $('#h_orderid').val();
        var staffid     = $('#h_staffid').val();
		var gst			= $('#h_gst_rate').val();
		var pst			= $('#h_pst_rate').val();

		if ( orderid == '' || typeof orderid === 'undefined' ) {
			orderid = 0;
		}
        if ( price == '' || typeof price === 'undefined' ) {
			price = 0;
		}

		$.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=posTouchAddServicePrice&orderid="+orderid+"&staffid="+staffid+"&name="+name+"&servid="+servid+"&quantity="+quantity+"&gst="+gst+"&pst="+pst+"&price="+price,
			dataType: "html",
			success: function(response){
                window.location.replace("pos_touch.php?classification=services");
			}
		});
	}

	/* ----- Remove An Item From The Order ----- */
	function removeProduct(sel) {
		var orderlistid	= sel.id;
		var gst			= $('#h_gst_rate').val();
		var pst			= $('#h_pst_rate').val();

		$.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=posTouchRemoveProduct&orderlistid="+orderlistid+"&gst="+gst+"&pst="+pst,
			dataType: "html",
			success: function(response){
				console.log(response);
				window.location.reload();
			}
		});
	}


	/* ----- Hold Order ----- */
	function holdOrder() {
		var checktotal	= $('#h_checktotal').val();
		var orderid		= $('#h_orderid').val();

		if (checktotal>0) {
			var comments = prompt("Enter comments for the order to be held", "");

			$.ajax({
				type: "GET",
				url: "pos_touch_ajax_all.php?fill=posTouchHoldOrder&orderid="+orderid+"&comments="+comments,
				dataType: "html",
				success: function(response){
					window.location.replace("pos_touch.php?classification=held");
				}
			});
		} else {
			alert('Please add at least one item to hold the order.');
		}
	}


	/* ----- Service Held Order ----- */
	function serviceHeldOrder(sel) {
		var id			= sel.id;
		var arr			= id.split('_');
		var orderid		= arr[1];

		$.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=posTouchServiceHeldOrder&orderid="+orderid,
			dataType: "html",
			success: function(response){
				window.location.replace("pos_touch.php?classification=held");
			}
		});
	}


	/* ----- Cancel Held Order ----- */
	function cancelHeldOrder(sel) {
		var id			= sel.id;
		var arr			= id.split('_');
		var orderid		= arr[1];

		$.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=posTouchCancelHeldOrder&orderid="+orderid,
			dataType: "html",
			success: function(response){
				window.location.replace("pos_touch.php?classification=held");
			}
		});
	}


	/* ----- Cancel Order ----- */
	function cancelOrder() {
		$.ajax({
			type: "GET",
			url: "pos_touch_ajax_all.php?fill=posTouchCancelOrder",
			dataType: "html",
			success: function(response){
				window.location.replace("pos_touch.php");
			}
		});
	}
</script><?php

if ( isset ( $_POST['submit_new_cust'] ) ) {
	// New customer to email
	$customer_name	= encryptIt($_POST['customer_name']);
	$customer_phone	= encryptIt($_POST['cusphone']);
	$email			= encryptIt($_POST['email']);
	$cust_category	= get_config($dbc, 'pos_new_customer');
	$cust_category	= ( !empty($cust_category) ) ? $cust_category : 'Customer';
	$reference		= $_POST['reference'];

	$query_insert	= "INSERT INTO `contacts` (`name`, `category`, `office_phone`, `email_address`, `referred_by`) VALUES ('$customer_name', '$cust_category', '$customer_phone', '$email', '$reference')";
	$results_insert	= mysqli_query($dbc, $query_insert);
	$contactid		= mysqli_insert_id($dbc);
}

if ( isset ( $_POST['add_new_cust'] ) ) {
	// New customer to create the POS
	$customer_name	= encryptIt($_POST['customer_name']);
	$customer_phone	= encryptIt($_POST['cusphone']);
	$email			= encryptIt($_POST['email']);
	$cust_category	= get_config($dbc, 'pos_new_customer');
	$cust_category	= ( !empty($cust_category) ) ? $cust_category : 'Customer';
	$reference		= $_POST['reference'];

	$query_insert	= "INSERT INTO `contacts` (`name`, `category`, `office_phone`, `email_address`, `referred_by`) VALUES ('$customer_name', '$cust_category', '$customer_phone', '$email', '$reference')";
	$results_insert	= mysqli_query($dbc, $query_insert);
	$contactid		= mysqli_insert_id($dbc);

	// Start a new temporary POS entry
	echo '
		<script>
			$.ajax({
				type: "GET",
				url: "pos_touch_ajax_all.php?fill=posTouchCustomerSelected&custid="+' . $contactid . ',
				dataType: "html",
				success: function(response){
					window.location.replace("pos_touch.php?classification=inventory");
				}
			});
		</script>';
} ?>
</head>

<body class="pos-touch">
<?php
	include_once ('../navigation.php');
checkAuthorised('pos');
?>
<div class="container triple-pad-bottom">
    <div class="row sticky-container">
		<?php
			// Check if it came from the Appointmet Calendar
            if ( isset($_GET['bookingid']) && !empty($_GET['bookingid']) ) {
                $bookingid = preg_replace('/[^0-9]/', '', $_GET['bookingid']);
                $get_booking = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `bookingid`, `patientid`, `therapistsid`, `serviceid` FROM `booking` WHERE `bookingid`='$bookingid'")); ?>
                <script>
                    custid    = '<?= $get_booking['patientid'] ?>';
                    staffid   = '<?= bin2hex($get_booking['therapistsid']) ?>';
                    serviceid = '<?= bin2hex($get_booking['serviceid']) ?>';
                    $.ajax({
                        type: "GET",
                        url: "pos_touch_ajax_all.php?fill=posTouchAppointment&custid="+custid+"&staffid="+staffid+"&serviceid="+serviceid,
                        dataType: "html",
                        success: function(response){
                            console.log(response);
                            window.location.replace("pos_touch.php?classification=services");
                        }
                    });
                </script><?php
            }

            $get_field_config	= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `pos` FROM `field_config`"));
			$value_config		= ',' . $get_field_config['pos'] . ',';

			// Get tax (GST/PST)
			$get_pos_tax	= get_config($dbc, 'pos_tax');
			$pdf_tax		= '';
			$gst_rate		= '';
			$pst_rate		= '';

			if ( $get_pos_tax != '' ) {
				$pos_tax		= explode ( '*#*', $get_pos_tax );
				$total_count	= mb_substr_count ( $get_pos_tax, '*#*' );

				for ( $eq_loop=0; $eq_loop<=$total_count; $eq_loop++ ) {
					$pos_tax_name_rate = explode ( '**', $pos_tax[$eq_loop] );

					if ( strcasecmp ( $pos_tax_name_rate[0], 'gst' ) == 0 ) {
						$gst_rate = $pos_tax_name_rate[1];
					}

					if ( strcasecmp ( $pos_tax_name_rate[0], 'pst') == 0 ) {
						if ( $pos_tax_name_rate[3] == 'Yes' && $client_tax_exemption == 'Yes' ) {
							$pst_rate = 0;
						} else {
							$pst_rate = $pos_tax_name_rate[1];
						}
					}
				}

				$pst_rate = ( $pst_rate == '' ) ? 0 : $pst_rate;
			} ?>

			<input type="hidden" id="h_gst_rate" name="h_gst_rate" value="<?php echo ( !empty($gst_rate) ) ? $gst_rate : '0'; ?>" />
			<input type="hidden" id="h_pst_rate" name="h_pst_rate" value="<?php echo ( !empty($pst_rate) ) ? $pst_rate : '0'; ?>" /><?php
		?>

		<!-- Order Total Sidebar -->
		<?php include('pos_touch_sidebar.php'); ?>


		<!-- Products / Main -->
		<div id="main" class="col-sm-8 double-pad-left">
			<h1 class="pull-left"><?= POS_ADVANCE_TILE ?></h1>
			<div class="pull-right double-gap-top">
				<?php
					if(config_visible_function($dbc, 'pos') == 1) {
						echo '<a href="field_config_pos.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
					}
				?>
			</div>
			<div class="clearfix double-gap-bottom"></div><?php

			/* On-screen display is based on these values */
			$classification = ( isset ( $_GET['classification'] ) ) ? trim( $_GET['classification'] ) : 'services';

			switch ($classification) {
				case 'inventory':
					$inventory_active = 'active_tab';
					break;
				case 'products':
					$products_active = 'active_tab';
					break;
				case 'services':
					$services_active = 'active_tab';
					break;
				case 'misc':
					$misc_active = 'active_tab';
					break;
				case 'held':
					$held_active = 'active_tab';
					break;
				default:
					$inventory_active	= '';
					$products_active	= '';
					$services_active	= '';
					$misc_active		= '';
					$held_active		= '';
					break;
			}

			// Inventory
            if ( get_software_name()=='calla' || get_software_name()=='localhost' ) {
                $inv_pricing = 'final_retail_price';
            } else {
                $inv_pricing = ( isset ( $_GET['inv_pricing'] ) ) ? trim( $_GET['inv_pricing'] ) : '';
            }
			$inv_category	= ( isset ( $_GET['inv_category'] ) ) ? trim( $_GET['inv_category'] ) : '';
			$invid			= ( isset ( $_GET['invid'] ) ) ? trim( preg_replace('/[^0-9]/', '', $_GET['invid']) ) : '';

			// Products
			$category	= ( isset ( $_GET['category'] ) ) ? trim( $_GET['category'] ) : '';
			$type		= ( isset ( $_GET['type'] ) ) ? trim( $_GET['type'] ) : '';
			$product	= ( isset ( $_GET['product'] ) ) ? trim( $_GET['product'] ) : '';

			// Services
			$serv_category	= ( isset ( $_GET['servcat'] ) ) ? trim( $_GET['servcat'] ) : '';
			$service	    = ( isset ( $_GET['service'] ) ) ? trim( $_GET['service'] ) : '';
            $servid         = ( isset ( $_GET['servid'] ) ) ? trim( preg_replace('/[^0-9]/', '', $_GET['servid']) ) : '';

			// Staff
			$serv_staff	= ( isset ( $_GET['servstaff'] ) ) ? trim( preg_replace('/[^0-9]/', '', $_GET['servstaff']) ) : '';

			// Check if we're on the email receipt dashboard
			$customerid	= ( isset ( $_GET['customerid'] ) ) ? trim( $_GET['customerid'] ) : '';
			if ( strpos ( $value_config, ',Customer,') !== FALSE ) {
				$customer = ( empty($orderid) && empty($customerid) ) ? TRUE : FALSE;
			} else {
				$customer = FALSE;
			}

			$discount		= ( isset ( $_GET['discount'] ) ) ? TRUE : FALSE;
			$coupon			= ( isset ( $_GET['coupon'] ) ) ? TRUE : FALSE;
      $gf			= ( isset ( $_GET['gf'] ) ) ? TRUE : FALSE;
			$email_reciept	= ( isset ( $_GET['email_reciept'] ) ) ? TRUE : FALSE;
			$pay			= ( isset ( $_GET['pay'] ) ) ? TRUE : FALSE;
			$pay_type		= ( isset ( $_GET['pay_type'] ) ) ? trim ( $_GET['pay_type'] ) : '';
			$complete		= ( isset ( $_GET['complete'] ) ) ? TRUE : FALSE; ?>

			<input type="hidden" name="type" id="h_type" value="<?php echo $type; ?>" />
			<input type="hidden" name="product" id="h_product" value="<?php echo $product; ?>" />
			<input type="hidden" name="service" id="h_service" value="<?php echo hex2bin($service); ?>" />
			<input type="hidden" name="staffid" id="h_staffid" value="<?php echo $serv_staff; ?>" />
			<input type="hidden" name="orderid" id="h_orderid" value="<?php echo ( isset ($_SESSION['orderid']) && !empty($_SESSION['orderid']) ) ? $_SESSION['orderid'] : ''; ?>" /><?php

			/* ----- Main POS Dashboard ----- */
			include('pos_touch_main.php');

			/* ----- Customer Selection Dashboard ----- */
			include('pos_touch_customer_selection.php');

			/* ----- Add Discount Dashboard ----- */
			include('pos_touch_discount.php');

      /* ----- Add Discount Dashboard ----- */
      include('pos_touch_gf.php');

			/* ----- Add Coupon Dashboard ----- */
			include('pos_touch_coupon.php');

			/* ----- Payment Dashboard ----- */
			include('pos_touch_payment.php');

			/* ----- Complete The Order ----- */
			include('pos_touch_complete.php');

			/* ----- Email Receipt Dashboard ----- */
			include('pos_touch_email_receipt.php'); ?>

		</div><!-- #main .col-sm-8 -->

		<div class="clearfix"></div>

	</div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>
