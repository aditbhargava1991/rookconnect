<?php
/*
 * Service Add/Edit Page
 */
error_reporting(0);
include ('../include.php'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        if($(window).width() <= 767 && '<?= $_GET['p'] ?>' == 'details') {
            window.location.href = '?p=details_mob&id=<?= $_GET['id'] ?>';
        }
        $("#form1").submit(function( event ) {
            var service_type = $("#service_type").val();
            var category = $("input[name=category]").val();
            var heading = $("input[name=heading]").val();
            if (category == '' || heading == '' ) {
                alert("Please make sure you have filled in all of the required fields.");
                return false;
            }
        });

        $("#service_type").change(function() {
            if($("#service_type option:selected").text() == 'New Service') {
                $( "#new_service" ).show();
            } else {
                $( "#new_service" ).hide();
            }
        });

        $("#category").change(function() {
            if($("#category option:selected").text() == 'New Category') {
                $( "#new_category" ).show();
            } else {
                $( "#new_category" ).hide();
            }
        });

        /* if($(window).width() > 767) {
            resizeScreen();
            $(window).resize(function() {
                resizeScreen();
            });
        } */

        var hash = window.location.hash.substr(1);
        if (hash != '') {
            $('#nav_'+hash).click();
        } else {
            $('#nav_customers').click();
        }

        $('.tile-sidebar a:not(.cursor-hand)').click(function() {
            $('.tile-sidebar li').removeClass('active');
            $(this).closest('li').addClass('active');
        });

        var $sections = $('.accordion-block-details');
        var $subsections = $('.accordion-block-details-sub');
        $('.has-main-screen').on('scroll', function(){
            var currentScroll = $('.main-screen .tile-container').offset().top + $('.main-screen').find('.standard-body-title').height();
            var $currentSection;
            $('.tile-sidebar .active').removeClass('active');
            $sections.each(function(){
                var top = $('.has-main-screen').offset().top;
                var bottom = $('.has-main-screen').outerHeight() + top;
                if($(this).offset().top < bottom - 15 && $(this).offset().top + $(this).outerHeight() > top + 10) {
                    var id = this.id;
                    $('.tile-sidebar [href$=#'+id+']').find('li:not(.sidebar-higher-level)').addClass('active');
                }
            });
        }).scroll();

        var current_tab = [];
        $('[data-tab-name]:visible').each(function() {
            if(this.getBoundingClientRect().top < $('.main-screen .main-screen').offset().top + $('.main-screen .main-screen').height() &&
                this.getBoundingClientRect().bottom > $('.main-screen .main-screen').offset().top) {
                current_tab.push($(this).data('tab-name'));
            }
        });

        $(window).resize(function() {
            $('.main-screen').css('padding-bottom',0);
            if($('.main-screen .main-screen').is(':visible') && $('.sidebar').is(':visible')) {
                var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
                if(available_height > 300) {
                    $('.sidebar').outerHeight(available_height).css('overflow-y','auto');
                    var sidebar_height = $('.tile-sidebar').outerHeight(true);
                    $('.has-main-screen .main-screen').css('height','auto');
                    $('.has-main-screen').outerHeight(sidebar_height).css('overflow-y','auto');
                }
            } else {
                $('.main-screen .main-screen').css('height','auto');
            }
        }).resize();
        
        $('.tile-sidebar a').click(function() {
            $('.has-main-screen').scroll();
        });
    });

    function resizeScreen() {
        var view_height = $(window).height() > 800 ? $(window).height() : 800;
        //$('.scale-to-fill .main-screen, .tile-sidebar').height($('.tile-container').height());
        $('#services_div .tile-sidebar, #services_div .tile-content').height($('#services_div').height() - $('#services_div .tile-header').height() + 15);
        $('.standard-body .standard-body-content').css('height', 'auto');
        $('.standard-body.preview .standard-body-content').height($('#services_div .tile-content').height() - $('.standard-body .standard-body-title').height() + 20);
    }
</script>
</head>

<body><?php
include_once ('../navigation.php');
checkAuthorised('services');

$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services` FROM `field_config`"));
$value_config = ','.$get_field_config['services'].',';

if (isset($_POST['add_service'])) {

	if($_POST['new_service'] != '') {
		$service_type = filter_var($_POST['new_service'],FILTER_SANITIZE_STRING);
	} else {
		$service_type = filter_var($_POST['service_type'],FILTER_SANITIZE_STRING);
	}

	if($_POST['new_category'] != '') {
		$category = filter_var($_POST['new_category'],FILTER_SANITIZE_STRING);
	} else {
		$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	}

    $invoice_description = filter_var(htmlentities($_POST['invoice_description']),FILTER_SANITIZE_STRING);
    $ticket_description = filter_var(htmlentities($_POST['ticket_description']),FILTER_SANITIZE_STRING);
    $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
    $fee = filter_var($_POST['fee'],FILTER_SANITIZE_STRING);

    $service_code = filter_var($_POST['service_code'],FILTER_SANITIZE_STRING);
    $quantity = filter_var($_POST['quantity'],FILTER_SANITIZE_STRING);
    $heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
    $cost = filter_var($_POST['cost'],FILTER_SANITIZE_STRING);

    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);

    if($_POST['same_desc'] == 1) {
        $quote_description = $description;
    } else {
        $quote_description = filter_var(htmlentities($_POST['quote_description']),FILTER_SANITIZE_STRING);
    }

    $final_retail_price = filter_var($_POST['final_retail_price'],FILTER_SANITIZE_STRING);
    $admin_price = filter_var($_POST['admin_price'],FILTER_SANITIZE_STRING);
    $wholesale_price = filter_var($_POST['wholesale_price'],FILTER_SANITIZE_STRING);
    $commercial_price = filter_var($_POST['commercial_price'],FILTER_SANITIZE_STRING);
    $client_price = filter_var($_POST['client_price'],FILTER_SANITIZE_STRING);
    $minimum_billable = filter_var($_POST['minimum_billable'],FILTER_SANITIZE_STRING);
	$purchase_order_price = filter_var($_POST['purchase_order_price'],FILTER_SANITIZE_STRING);
	$sales_order_price = filter_var($_POST['sales_order_price'],FILTER_SANITIZE_STRING);
    $hourly_rate = filter_var($_POST['hourly_rate'],FILTER_SANITIZE_STRING);
    $estimated_hours = filter_var($_POST['estimated_hours'],FILTER_SANITIZE_STRING);
    $actual_hours = filter_var($_POST['actual_hours'],FILTER_SANITIZE_STRING);
    $msrp = filter_var($_POST['msrp'],FILTER_SANITIZE_STRING);

    $unit_price = filter_var($_POST['unit_price'],FILTER_SANITIZE_STRING);
    $unit_cost = filter_var($_POST['unit_cost'],FILTER_SANITIZE_STRING);
    $rent_price = filter_var($_POST['rent_price'],FILTER_SANITIZE_STRING);
    $rental_days = filter_var($_POST['rental_days'],FILTER_SANITIZE_STRING);
    $rental_weeks = filter_var($_POST['rental_weeks'],FILTER_SANITIZE_STRING);
    $rental_months = filter_var($_POST['rental_months'],FILTER_SANITIZE_STRING);
    $rental_years = filter_var($_POST['rental_years'],FILTER_SANITIZE_STRING);
    $reminder_alert = filter_var($_POST['reminder_alert'],FILTER_SANITIZE_STRING);
    $daily = filter_var($_POST['daily'],FILTER_SANITIZE_STRING);
    $weekly = filter_var($_POST['weekly'],FILTER_SANITIZE_STRING);
    $monthly = filter_var($_POST['monthly'],FILTER_SANITIZE_STRING);
    $annually = filter_var($_POST['annually'],FILTER_SANITIZE_STRING);
    $total_days = filter_var($_POST['total_days'],FILTER_SANITIZE_STRING);
    $total_hours = filter_var($_POST['total_hours'],FILTER_SANITIZE_STRING);
    $total_km = filter_var($_POST['total_km'],FILTER_SANITIZE_STRING);
    $total_miles = filter_var($_POST['total_miles'],FILTER_SANITIZE_STRING);
	$include_in_pos = filter_var($_POST['include_in_pos'],FILTER_SANITIZE_STRING);
	$include_in_po = filter_var($_POST['include_in_po'],FILTER_SANITIZE_STRING);
	$include_in_so = filter_var($_POST['include_in_so'],FILTER_SANITIZE_STRING);
	$checklist = filter_var(implode('#*#',array_filter($_POST['checklist'])),FILTER_SANITIZE_STRING);

    $gst_exempt = $_POST['gst_exempt'];
    $appointment_type = filter_var($_POST['appointment_type'],FILTER_SANITIZE_STRING);

    if(empty($_POST['serviceid'])) {
        $query_insert = "INSERT INTO `services` (`service_type`, `category`, `service_code`, `heading`, `cost`, `description`, `quote_description`, `invoice_description`, `ticket_description`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `minimum_billable`, `hourly_rate`, `estimated_hours`, `actual_hours`, `msrp`, `name`, `fee`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles` , `include_in_so`,`include_in_po`,`include_in_pos`, `gst_exempt`, `appointment_type`, `quantity`, `checklist`) VALUES ('$service_type', '$category', '$service_code', '$heading', '$cost', '$description', '$quote_description', '$invoice_description', '$ticket_description', '$final_retail_price', '$admin_price', '$wholesale_price', '$commercial_price', '$client_price', '$purchase_order_price', '$sales_order_price', '$minimum_billable', '$hourly_rate', '$estimated_hours', '$actual_hours', '$msrp', '$name', '$fee', '$unit_price', '$unit_cost', '$rent_price', '$rental_days', '$rental_weeks', '$rental_months', '$rental_years', '$reminder_alert', '$daily', '$weekly', '$monthly', '$annually', '$total_days', '$total_hours', '$total_km', '$total_miles', '$include_in_so', '$include_in_po', '$include_in_pos', '$gst_exempt', '$appointment_type', '$quantity', '$checklist')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert);
		$serviceid = $dbc->insert_id;
        $url = 'Added';
        $before_change = "";
				$history = "Service entry has been added. <br />";
	  		add_update_history($dbc, 'service_history', $history, '', $before_change);
    } else {
        $serviceid = $_POST['serviceid'];
        $query_update = "UPDATE `services` SET `service_type` = '$service_type', `category` = '$category',`service_code` = '$service_code', `heading` = '$heading', `cost` = '$cost', `description` = '$description', `quote_description` = '$quote_description', `invoice_description` = '$invoice_description', `ticket_description` = '$ticket_description', `final_retail_price` = '$final_retail_price', `admin_price` = '$admin_price', `wholesale_price` = '$wholesale_price', `commercial_price` = '$commercial_price', `client_price` = '$client_price', `purchase_order_price` = '$purchase_order_price', `sales_order_price` = '$sales_order_price', `minimum_billable` = '$minimum_billable', `hourly_rate` = '$hourly_rate', `estimated_hours` = '$estimated_hours', `actual_hours` = '$actual_hours', `msrp` = '$msrp', `name` = '$name', `fee` = '$fee', `unit_price` = '$unit_price', `unit_cost` = '$unit_cost', `rent_price` = '$rent_price', `rental_days` = '$rental_days', `rental_weeks` = '$rental_weeks', `rental_months` = '$rental_months', `rental_years` = '$rental_years', `reminder_alert` = '$reminder_alert', `daily` = '$daily', `weekly` = '$weekly', `monthly` = '$monthly', `annually` = '$annually', `total_days` = '$total_days', `total_hours` = '$total_hours', `total_km` = '$total_km', `total_miles` = '$total_miles', `include_in_so` = '$include_in_so', `include_in_po` = '$include_in_po', `include_in_pos` = '$include_in_pos', `gst_exempt` = '$gst_exempt', `appointment_type` = '$appointment_type', `quantity` = '$quantity', `checklist`='$checklist' WHERE `serviceid` = '$serviceid'";
        $result_update_vendor = mysqli_query($dbc, $query_update);
        $url = 'Updated';
        $before_change = "";
				$history = "Service with service id $serviceid has been updated. <br />";
	  		add_update_history($dbc, 'service_history', $history, '', $before_change);
    }
	if($_FILES['service_image']['name'] != '') {
		if (!file_exists('download')) {
			mkdir('download', 0777, true);
		}
		$filename = file_safe_str($_FILES['service_image']['name']);
		move_uploaded_file($_FILES["service_image"]["tmp_name"], "download/".$filename);
    $before_change = capture_before_change($dbc, 'services', 'service_image', 'serviceid', $serviceid);
		$dbc->query("UPDATE `services` SET `service_image`='$filename' WHERE `serviceid`='$serviceid'");
    $history = capture_after_change('service_image', $filename);
    add_update_history($dbc, 'service_history', $history, '', $before_change);
	}

    if(strpos($value_config, ',Rate Card,') !== false && vuaed_visible_function($dbc, 'rate_card') > 0) {
        include('save_rate_card.php');
    }
    echo '<script type="text/javascript">window.location.replace("index.php?c='.bin2hex($category).'");</script>';
} ?>

<div id="services_div" class="container">
    <div class="row">
		<div class="main-screen"><?php
            include('tile_header.php');
            $page      = preg_replace('/\PL/u', '', $_GET['p']);
            $serviceid = preg_replace('/[^0-9]/', '', $_GET['id']); ?>

            <!--
            <div class="tile-bar">
                <ul>
                    <li class="<?= ( $page=='details' ) ? 'active' : ''; ?>"><a href="?p=details&id=<?=$serviceid;?>">Details</a></li>
                    <li class="<?= ( $page=='preview' ) ? 'active' : ''; ?>"><a href="?p=preview&id=<?=$serviceid;?>">Preview</a></li>
                </ul>
            </div>-- .tile-bar -->

            <div class="tile-container">
                <?php if($page == 'preview') { ?>
                    <div class="show-on-mob full-width pad-left pad-right pad-bottom" style="border-bottom:1px solid #ACA9A9; margin-bottom:-5px;">
                        <h3 class="pull-left">Service</h3>
                        <a href="../Services/service.php?p=details&id=<?= $serviceid ?>" class="btn brand-btn pull-right" style="margin-top:15px;">Edit</a>
                        <div class="clearfix"></div>
                    </div>
                <?php } ?>
                <!-- Sidebar -->
                <div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
                    <ul>
                        <a href="index.php"><li class="collapsed cursor-hand">Back to Dashboard</li></a>
                        <a href="?p=details&id=<?= $serviceid ?>#serviceinfo"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_serviceinfo" id="nav_serviceinfo">Service Information</li></a><?php
                        if (strpos($value_config, ',Quantity,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#quantity"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_quantity" id="nav_quantity">Quantity</li></a><?php
                        }
                        if (strpos($value_config, ',Description,') !== false || strpos($value_config, ',Quote Description,') !== false || strpos($value_config, ',Invoice Description,') !== false || strpos($value_config, ',Ticket Description,') !== false || strpos($value_config, ',Service Image,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#descriptions"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_descriptions" id="nav_descriptions">Descriptions</li></a><?php
                        }
                        if (strpos($value_config, ',Checklist,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#checklist"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_checklist" id="nav_checklist">Checklist</li></a><?php
                        }
                        if (strpos($value_config, ',Fee,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#fee"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_fee" id="nav_fee">Fee</li></a><?php
                        }
                        if (strpos($value_config, ',Cost,') !== false || strpos($value_config, ',Unit Cost,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#costs"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_costs" id="nav_costs">Costs</li></a><?php
                        }
                        if (strpos($value_config, ',Final Retail Price,') !== false || strpos($value_config, ',Admin Price,') !== false || strpos($value_config, ',Wholesale Price,') !== false || strpos($value_config, ',Commercial Price,') !== false || strpos($value_config, ',Client Price,') !== false || strpos($value_config, ',Purchase Order Price,') !== false || strpos($value_config, ',Sales Order Price,') !== false || strpos($value_config, ',MSRP,') !== false || strpos($value_config, ',Unit Price,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#pricepoints"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_pricepoints" id="nav_pricepoints">Price Points</li></a><?php
                        }
                        if (strpos($value_config, ',Include in Sales Orders,') !== false || strpos($value_config, ',Include in Purchase Orders,') !== false || strpos($value_config, ',Include in P.O.S.,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#inclusions"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_inclusions" id="nav_inclusions">Inclusions</li></a><?php
                        }
                        if (strpos($value_config, ',Rent Price,') !== false || strpos($value_config, ',Rental Days,') !== false || strpos($value_config, ',Rental Weeks,') !== false || strpos($value_config, ',Rental Months,') !== false || strpos($value_config, ',Rental Years,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#rentalinfo"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_rentalinfo" id="nav_rentalinfo">Rental Information</li></a><?php
                        }
                        if (strpos($value_config, ',Reminder/Alert,') !== false || strpos($value_config, ',Daily,') !== false || strpos($value_config, ',Weekly,') !== false || strpos($value_config, ',Monthly,') !== false || strpos($value_config, ',Annually,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#reminderalert"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_reminderalert" id="nav_reminderalert">Reminder/Alert</li></a><?php
                        }
                        if (strpos($value_config, ',#Of Days,') !== false || strpos($value_config, ',#Of Hours,') !== false || strpos($value_config, ',#Of Kilometers,') !== false || strpos($value_config, ',#Of Miles,') !== false || strpos($value_config, ',Estimated Hours,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#unitinfo"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_unitinfo" id="nav_unitinfo">Unit Information</li></a><?php
                        }
                        if (strpos($value_config, ',GST exempt,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#gstexempt"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_gst_exempt" id="nav_gstexempt">GST Exempt</li></a><?php
                        }
                        if (strpos($value_config, ',Appointment Type,') !== false) { ?>
                            <a href="?p=details&id=<?= $serviceid ?>#appttype"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_appointment_type" id="nav_appttype">Appointment Type</li></a><?php
                        } ?>
                    </ul>
                </div><!-- .tile-sidebar -->

                <!-- Main Screen -->
                <div class="scale-to-fill tile-content has-main-screen" style="padding:0;">
                    <div class="main-screen override-main-screen no-overflow-x"><?php
                        if ( $page=='preview' || empty($page) ) {
                            include('preview.php');
                        } elseif ( $page=='detailsmob' ) {
                            include('details_mob.php');
                        } elseif ( $page=='details' ) {
                            include('details.php');
                        } else {
                            include('details.php');
                        } ?>
                    </div><!-- .main-screen -->
                </div><!-- .tile-content -->

                <div class="clearfix"></div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>
