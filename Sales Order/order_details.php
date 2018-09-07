<?php
/*
 * Add/Edit Sales Order
 * Acts as an index file
 */
error_reporting(0);
include ('../include.php');
$sotid = $_GET['sotid'];

if(isset($_POST['add_sales_order'])) {
	$history = '';
    $last_updated_by = $_SESSION['contactid'];

    //Custom Designs
    for($i = 0; $i < count($_FILES["custom_design"]['tmp_name']); $i++) {
        if(!empty($_FILES['custom_design']['name'][$i])) {
            $file = $_FILES['custom_design']['tmp_name'][$i];
            $file_name = $sotid.'_'.htmlspecialchars($_FILES['custom_design']['name'][$i], ENT_QUOTES);
            $design_name = $_POST['custom_design_name'][$i];

            move_uploaded_file($file,'download/'.$file_name);
            $query_insert = "INSERT INTO `sales_order_upload_temp` (`parentsotid`, `name`, `file`, `added_by`) VALUES ('$sotid', '$design_name', '$file_name', '".$_SESSION['contactid']."')";
            mysqli_query($dbc, $query_insert);
            $history .= 'Added Design: '.$design_name.'<br />';
        }
    }

    include('../Sales Order/save_order_details.php');
    $redirect_url = 'order_details.php?sotid='.$sotid;

    //Submit Sales Order
    if ($_POST['add_sales_order'] == 'Submit_Order') {
        include('../Sales Order/submit_order_details.php');
    }
	
	//Notes
	$html_note = $_POST['note_text'];
	if(strip_tags($html_note) != '') {
		$history .= "Added Note: $html_note<br />";
		$note = filter_var(htmlentities($html_note),FILTER_SANITIZE_STRING);
		$send_email = filter_var($_POST['note_email_to'],FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `sales_order_notes` (`sales_order_id`,`note`,`email_comment`,`created_by`) VALUES ('$sotid','$note','$send_email','".$_SESSION['contactid']."')");
		if($_POST['send_note_email'] == 'send' && $send_email > 0) {
			$to_name = get_contact($dbc, $send_email);
			$to = [$to_name=>get_email($dbc, $send_email)];
			$from = [$_POST['note_email_address']=>$_POST['note_email_name']];
			$subject = $_POST['note_email_subject'];
			$body = str_replace('[REFERENCE]',$html_note,$_POST['note_email_body']);
			try {
				send_email($from, $to, '', '', $subject, $body, '');
				echo "<script> alert('Your note has been emailed to $to_name.'); </script>";
			} catch (Exception $e) {
				echo '<script> alert("'.$e->getMessage().'"); </script>';
			}
		}
	}
	
	//History
	if($history != '') {
		$historyid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`id`) FROM `sales_order_history` WHERE `sales_order_id`='$sotid' AND `contactid`='".$_SESSION['contactid']."' AND `date` >= '".date('Y-m-d H:i:s',strtotime('-15min'))."'"))[0];
		if($historyid > 0) {
			mysqli_query($dbc, "UPDATE `sales_order_history` SET `history`=CONCAT(`history`,'".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."') WHERE `id`='$historyid'");
		} else {
			mysqli_query($dbc, "INSERT INTO `sales_order_history` (`sales_order_id`, `history`, `contactid`) VALUES ('$sotid', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '".$_SESSION['contactid']."')");
		}
	}

    echo '<script type="text/javascript"> window.location.replace("'.$redirect_url.'");';
    if($_POST['add_sales_order'] == 'Submit_Order') {
        echo 'window.open("generate_pdf.php?posid='.$posid.'", "_blank");';
    }
    echo '</script>';
}
?>

<script type="text/javascript">
$(document).ready(function() {
    if($(window).width() > 767) {
        resizeScreen();
        $(window).resize(function() {
            resizeScreen();
        });
    }
    $('[name="add_sales_order"]').click(function() {
        var check_mandatory = '';
        $('.order_detail_contact').each(function() {
            var contact_name = $(this).data('contactname');
            $(this).find('table').each(function() {
                var quantity = parseInt($(this).data('quantity'));
                var heading = $(this).data('heading');
                if(quantity > 0) {
                    var selected_quantity = 0;
                    $(this).find('[name="item_quantity[]"]').each(function() {
                        selected_quantity += parseInt($(this).val());
                    });
                    if(selected_quantity != quantity) {
                        check_mandatory += contact_name+': Invalid Quantity for '+heading+"\n";
                    }
                }
            });
        });
        if(check_mandatory != '') {
            check_mandatory = "The following requirements are not met for this <?= SALES_ORDER_NOUN ?>:\n\n"+check_mandatory+"\nThis <?= SALES_ORDER_NOUN ?> will not be able to submitted until the requirements are met.";
        }
        if($(this).val() == 'Submit') {
            if(check_mandatory != '') {
                check_mandatory += " Are you sure you would like to continue?";
                if(confirm(check_mandatory)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else if($(this).val() == 'Submit_Order') {
            if(check_mandatory != '') {
                alert(check_mandatory);
                return false;
            } else {
                if (confirm('Are you sure you want to submit your order?')) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    });
});

function resizeScreen() {
    var view_height = $(window).height() > 800 ? $(window).height() : 800;
    $('#sales_order_div .scale-to-fill .main-screen-white').height(view_height - $('#sales_order_div .scale-to-fill').offset().top - $('#footer').outerHeight());
    $('#sales_order_div .scalable').height(view_height - $('#sales_order_div .scalable').offset().top - $('#footer').outerHeight());
    $('#sales_order_div .scalable .main-screen-white').height($('#sales_order_div .scalable').height() - $('#sales_order_div .scalable .buttons_div').height() - 50);
    $('#sales_order_div .tile-sidebar').height($('#sales_order_div .tile-container').height());
}

function deleteSalesOrderForm() {
    if (confirm('Are you sure you want to discard this <?= SALES_ORDER_NOUN ?>?')) {
        var sotid = $('#sotid').val();
        $.ajax({
            type: 'GET',
            url: 'ajax.php?fill=deleteSalesOrderForm&sotid='+sotid,
            dataType: 'html',
            success: function(response) {
                window.location.href = 'index.php';
            }
        });
    }
}

function loadOrderDetails(sel) {
    $('.sidebar_heading .cursor-hand').removeClass('active');
    $(sel).closest('.sidebar_heading').find('.cursor-hand').addClass('active');
    $('div .order_detail_contact').hide();
    $('div .order_detail_contact[data-contactid="'+$(sel).data('contactid')+'"]').show();
    $('#sidebar_nav li').removeClass('active');
    $(sel).closest('li').addClass('active');
    $('#order_details_header').text('Order Details - '+$(sel).text().trim());

    if ($(sel).data('contactid') == 'custom_designs') {
        $('#add_note').hide();
    } else {
        $('#add_note').show();
    }
}
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
checkAuthorised('sales_order');
    $sot = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '".$_GET['sotid']."'"));
    
    $so_type = $sot['sales_order_type'];
    $customerid = $sot['customerid'];
    $classification = $sot['classification'];
    $logo = $sot['logo'];
    if(empty($logo)) {
        $logo = get_config($dbc, 'sales_order_logo');
    }
    $security_option = $sot['security_option'];
    $discount_type = $sot['discount_type'];
    $discount_value = $sot['discount_value'];
    $delivery_type = $sot['delivery_type'];
    $delivery_address = $sot['delivery_address'];
    $contractorid = $sot['contractirid'];
    $delivery_amount = $sot['delivery_amount'];
    $assembly_amount = $sot['assembly_amount'];
    $payment_type = $sot['payment_type'];
    $deposit_paid = $sot['deposit_paid'];
    $comment = $sot['comment'];
    $ship_date = $sot['ship_date'];
    $due_date = $sot['due_date'];
    $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_so`"));
    $value_config = ','.$field_config['fields'].',';
    if(!empty($so_type)) {
        $value_config = get_config($dbc, 'so_'.config_safe_str($so_type).'_fields');
    }
    $active_tab = 'class="active"';
    $active_div = '';
    if(!empty($classification)) {
        $classification_query = " AND `classification` = '$classification'";
    } else {
        $classification_query = "";
    }
    $cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts` WHERE `sales_order_type` = '$so_type'"),MYSQLI_ASSOC);
    $contact_categories = [];
    foreach ($cat_config as $contact_cat) {
        $contact_categories[] = $contact_cat['contact_category'];
    }

?>

<div id="sales_order_div" class="container">
    
    <div class="row hide_on_iframe">
		<div class="main-screen"><?php
            include('tile_header.php');
            
            //$page   = preg_replace('/\PL/u', '', $_GET['p']);
            //$posid  = preg_replace('/[^0-9]/', '', $_GET['id']); ?>
            
            <div class="tile-container" style="height: 100%;">
                <!-- Sidebar -->
                <div class="standard-collapsible tile-sidebar tile-sidebar-noleftpad" style="overflow-y: hidden; background-color: #fff">
                    <ul id="sidebar_nav" style="overflow-y: auto; height: calc(100% - 150px);">
                        <li><a href="index.php">Dashboard</a></li>
                        <?php if (strpos($value_config, ',Custom Designs,') !== FALSE) { ?>
                            <li class="cursor-hand" data-toggle="collapse" data-target="#collapse_designs"><a href="" data-contactid="custom_designs" onclick="loadOrderDetails(this); return false;">Custom Designs</a></li>
                        <?php } ?>
                        <?php foreach ($contact_categories as $i => $contact_category) { ?>
                            <li class="sidebar_heading sidebar-higher-level"><a class="cursor-hand <?= $i == 0 ? 'active' : 'collapsed' ?>" data-toggle="collapse" data-target="#collapse_<?= $contact_category ?>"><?= $contact_category ?> Roster<span class="arrow"></span></a>
                                <ul id="collapse_<?= $contact_category ?>" class="collapse <?= $i == 0 ? 'in' : '' ?>" style="height: auto;"><?php
                                    $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `businessid` = '$customerid' AND `category` = '$contact_category' AND `deleted` = 0".$classification_query),MYSQLI_ASSOC));
                                    foreach ($contact_list as $id) {
                                        echo '<li '.$active_tab.'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="" data-contactid="'.$id.'" onclick="loadOrderDetails(this); return false;" id="'.$contact_category.'">'.get_contact($dbc, $id).'</a></li>';
                                        if (!empty($active_tab)) {
                                            $active_div = $id;
                                        }
                                        $active_tab = '';
                                    } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if(empty($contact_categories)) { ?>
                            <li class="active"><a id="no_cat" href="" data-contactid="<?= $customerid ?>" onclick="loadOrderDetails(this); return false;"><?= SALES_ORDER_NOUN ?> Details</a></li>
                        <?php } ?>
                    </ul>
                    <?php if (!empty($logo) && strpos($value_config, ',Logo,') !== FALSE) { ?>
                        <img src="download/<?= $logo ?>" class="pad-10" style="max-width: 150px; max-height: 150px; width: 100%; height: auto; margin-top: 1em; bottom: 0px; position: relative;">
                    <?php } ?>
                </div><!-- .tile-sidebar -->
                
                <!-- Main Screen -->
                <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

                    <!-- Preview Bar -->
                    <?php $detect = new Mobile_Detect;
                    $is_mobile = ( $detect->isMobile() ) ? true : false;
                    if(!$is_mobile) { ?>
                        <div class="scalable preview-bar hide-titles-mob" style="<?= $scale_style ?>"><?php
                            include('order_details_preview.php'); ?>
                            <div class="pull-right gap-top gap-right buttons_div">
                                <a href="index.php" class="btn brand-btn">Cancel</a>
                                <a href="<?= WEBSITE_URL ?>/Sales Order/order.php?p=details&sotid=<?= $sotid ?>" class="btn brand-btn">Edit <?= SALES_ORDER_NOUN ?></a>
                                <button type="submit" name="add_sales_order" value="Submit" class="btn brand-btn">Save</button>
                                <button type="submit" name="add_sales_order" value="Submit_Order" class="btn brand-btn">Submit Order</button>
                                <a href="#" onclick="deleteSalesOrderForm(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png" class="no-toggle" title="Archive"></a>
                            </div>
                        </div><!-- .preview-bar -->
                    <?php } ?>

                    <div class="scale-to-fill"><?php
                        include('order_details_content.php'); ?>
                    </div>

                    <?php if($is_mobile) { ?>
                        <!-- Preview Bar -->
                        <div class="col-xs-12 show-on-mob preview-bar"><?php
                            include('order_details_preview.php'); ?>
                            <div class="pull-right gap-top gap-right buttons_div">
                                <a href="index.php" class="btn brand-btn">Cancel</a>
                                <a href="<?= WEBSITE_URL ?>/Sales Order/order.php?p=details&sotid=<?= $sotid ?>" class="btn brand-btn">Edit <?= SALES_ORDER_NOUN ?></a>
                                <button type="submit" name="add_sales_order" value="Submit" class="btn brand-btn">Save</button>
                                <button type="submit" name="add_sales_order" value="Submit_Order" class="btn brand-btn">Submit Order</button>
                                <a href="#" onclick="deleteSalesOrderForm(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png" class="no-toggle" title="Archive"></a>
                            </div>
                        </div><!-- .preview-bar -->
                    <?php } ?>
                </form>
                
                <div class="clearfix"></div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>