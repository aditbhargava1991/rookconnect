<?php
/*
 * Add/Edit Sales Order
 * Acts as an index file
 */
error_reporting(0);
include ('../include.php');
$sotid = $_GET['sotid'];
$so_type = $_GET['so_type'];

$security_access = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_security` WHERE CONCAT(',','".ROLE."',',') LIKE CONCAT('%,',`security_level`,',%')"),MYSQLI_ASSOC), 'access');
if(!in_array('ALL',$security_access) && in_array('Assigned Only',$security_access) && !empty($sotid)) {
    $has_access = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) num_rows FROM `sales_order_temp` WHERE `sotid` = '$sotid' AND  (`primary_staff` = '".$_SESSION['contactid']."' OR CONCAT(',',`assign_staff`,',') LIKE '%,".$_SESSION['contactid'].",%')"))['num_rows'];
    if(!($has_access > 0)) {
        echo '<script>alert("You do not have access to this '.SALES_ORDER_NOUN.'."); window.location.href = "'.WEBSITE_URL.'/Sales Order/index.php";</script>';
    }
}

$default_template = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_so`"))['default_template'];
if(!empty($so_type)) {
    $default_template = get_config($dbc, 'so_'.config_safe_str($so_type).'_default_template');
}
if(empty($_GET['sotid']) && $default_template > 0) {
    mysqli_query($dbc, "INSERT INTO `sales_order_temp` (`deleted`, `sales_order_type`, `templateid`) VALUES (1, '$so_type', '$default_template')");
    $sotid = mysqli_insert_id($dbc);

    $template_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `template_name` FROM `sales_order_template` WHERE `id` = '$default_template'"))['template_name'];
    $contactid = $_SESSION['contactid'];
    $sales_order_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '$sotid'"))['name'];
    if(empty($sales_order_name)) {
        $sales_order_name = SALES_ORDER_NOUN.' Form #'.$sotid;
    }

    mysqli_query($dbc, "DELETE FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid'");

    $template_products = mysqli_query($dbc, "SELECT * FROM `sales_order_template_product` WHERE `template_id` = '$default_template'");
    while ($row = mysqli_fetch_array($template_products)) {
        $item_type = $row['item_type'];
        $item_type_id = $row['item_type_id'];
        $item_category = $row['item_category'];
        $item_name = $row['item_name'];
        $item_price = number_format($row['item_price'], 2);
        $contact_category = $row['contact_category'];
        $heading_name = $row['heading_name'];
        $mandatory_quantity = $row['mandatory_quantity'];

        mysqli_query($dbc, "INSERT INTO `sales_order_product_temp` (`contactid`, `item_type`, `item_type_id`, `item_category`, `item_name`, `item_price`, `contact_category`, `heading_name`, `mandatory_quantity`, `parentsotid`, `templateid`) VALUES ('$contactid', '$item_type', '$item_type_id', '$item_category', '$item_name', '$item_price', '$contact_category', '$heading_name', '$mandatory_quantity','$sotid', '$default_template')");
    }
    $history = 'Loaded Template '.$template_name.' into '.$sales_order_name.'<br />';

    //History
    if($history != '') {
        $historyid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`id`) FROM `sales_order_history` WHERE `sales_order_id`='$sotid' AND `contactid`='".$_SESSION['contactid']."' AND `date` >= '".date('Y-m-d H:i:s',strtotime('-15min'))."'"))[0];
        if($historyid > 0) {
            mysqli_query($dbc, "UPDATE `sales_order_history` SET `history`=CONCAT(`history`,'".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."') WHERE `id`='$historyid'");
        } else {
            mysqli_query($dbc, "INSERT INTO `sales_order_history` (`sales_order_id`, `history`, `contactid`) VALUES ('$sotid', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '".$_SESSION['contactid']."')");
        }
    }

}

// Form submission from details.php
if (isset($_POST['add_sales_order'])) {
	$history = '';
    $sotid          = $_POST['sotid'];
    $businessid     = $_POST['businessid'];
    $so_type        = $_POST['so_type'];
    $sales_order_name = filter_var($_POST['sales_order_name'],FILTER_SANITIZE_STRING);
	$invoice_date   = date('Y-m-d');
    $created_by     = $_SESSION['contactid'];
    $m              = 0;
    $classification = filter_var($_POST['classification'],FILTER_SANITIZE_STRING);
    if (empty($sotid)) {
        mysqli_query($dbc, "INSERT INTO `sales_order_temp` VALUES ()");
        $sotid = mysqli_insert_id($dbc);
        $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_so`"));
        $value_config = ','.$field_config['fields'].',';
        if(!empty($so_type)) {
            $value_config = ','.get_config($dbc, 'so_'.config_safe_str($so_type).'_fields').',';
        }
        if(strpos($value_config, ',Generate Name Customer Sotid,') !== FALSE && $_POST['sales_order_name'] == (!empty(get_client($dbc, $businessid)) ? get_client($dbc, $businessid) : get_contact($dbc, $businessid)).' #') {
            $sales_order_name = !empty(get_client($dbc, $businessid)) ? get_client($dbc, $businessid) : get_contact($dbc, $businessid).' #'.$sotid;
        }
		$history .= 'Created new '.SALES_ORDER_NOUN.'<br />';
    } else {
        mysqli_query($dbc, "UPDATE `sales_order_temp` SET `deleted` = 0 WHERE `sotid` = '$sotid'");
    }
	$current_values = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid`='$sotid'"));

    //Staff Information
    $primary_staff = $_POST['primary_staff'];
    $assign_staff = implode(',',$_POST['assign_staff']);
    
    $business_region         = filter_var($_POST['business_region'],FILTER_SANITIZE_STRING);
    $business_location       = filter_var($_POST['business_location'],FILTER_SANITIZE_STRING);
    $business_classification = filter_var($_POST['business_classification'],FILTER_SANITIZE_STRING);
    //Business
    if($businessid != $current_values['customerid']) {
		$history .= 'Attached '.SALES_ORDER_NOUN.' to '.!empty(get_client($dbc, $businessid)) ? get_client($dbc, $businessid) : get_contact($dbc, $businessid).'<br />';
	}

    //Business Contacts
    $business_contacts = '';
    for($i = 0; $i < count($_POST['business_contact']); $i++) {
        $business_contact = $_POST['business_contact'][$i];
        if($business_contact > 0 && !in_array($business_contact,explode(',',$current_values['business_contact']))) {
			$history .= 'Attached '.SALES_ORDER_NOUN.' to '.get_contact($dbc, $business_contact).'<br />';
		}
        $business_contacts .= $business_contact.',';
    }
    $business_contacts = trim($business_contacts, ',');

    //Classification
    if($classification == 'new_classification') {
        $classification = $_POST['new_classification'];
        $classification_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `general_configuration` WHERE `name` LIKE '%_classification'"));
        if (!empty($classification_config['name'])) {
            $classification_config_val = rtrim($classification_config['value'], ',').','.$classification;
            mysqli_query($dbc, "UPDATE `general_configuration` SET `value` = '$classification_config_val' WHERE `name` = '".$classification_config['name']."'");
        } else {
            $classification_config_val = $classification;
            mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES (`contacts_classification`, '$classification_config_val')");
        }
        $history .= 'Attached '.SALES_ORDER_NOUN.' to New Classification: '.$_POST['new_classification'].'<br />';
    } else if($classification != $current_values['classification']) {
        $history .= 'Attached '.SALES_ORDER_NOUN.' to Classification '.$_POST['new_classification'].'<br />';
    }

    //Contact Categories
    $cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts`"),MYSQLI_ASSOC);
    foreach ($cat_config as $contact_cat) {
        $contact_category = $contact_cat['contact_category'];
        for ($i = 0; $i < count($_POST[$contact_category.'_first_name']) && $i < count($_POST[$contact_category.'_last_name']) && $i < count($_POST[$contact_category.'_email']) && $i < count($_POST[$contact_category.'_username']); $i++) {
            if($_POST[$contact_category.'_first_name'][$i].$_POST[$contact_category.'_last_name'][$i].$_POST[$contact_category.'_email'][$i].$_POST[$contact_category.'_username'][$i] != '') {
                $first_name = encryptIt($_POST[$contact_category.'_first_name'][$i]);
                $last_name = encryptIt($_POST[$contact_category.'_last_name'][$i]);
                $email_address = encryptIt($_POST[$contact_category.'_email'][$i]);
                $player_number = $_POST[$contact_category.'_number'][$i];
                $user_name = $_POST[$contact_category.'_username'][$i];
                $password = encryptIt($_POST[$contact_category.'_password'][$i]);
                if (isset($_POST[$contact_category.'_email_login'][$i])) {
                    $subject = "Login Credentials for ".SALES_ORDER_NOUN;
                    $message = "The following is your Username and Password:<br /><br />
                        Username: ".$user_name."<br />
                        Password: ".decryptIt($password)."<br /><br />
                        Click <a href='".WEBSITE_URL."/Sales Order/order.php?p=details&sotid=".$sotid."' target='_blank'>here</a> to log in.";
                    send_email('', decryptIt($email_address), '', '', $subject, $message, '');
                }

                $query_insert = "INSERT INTO `contacts` (`category`, `businessid`, `first_name`, `last_name`, `email_address`, `user_name`, `password`, `password_date`, `classification`, `player_number`) VALUES ('$contact_category', '$businessid', '$first_name', '$last_name', '$email_address', '$user_name', '$password', CURRENT_TIMESTAMP, '$classification', '$player_number')";
                mysqli_query($dbc, $query_insert);
                $history .= 'Added '.$contact_category.' '.$_POST[$contact_category.'_first_name'][$i].' '.$_POST[$contact_category.'_last_name'][$i].'<br />';
            }
        }
    }

    //Next Action
    $status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
    $next_action = filter_var($_POST['next_action'],FILTER_SANITIZE_STRING);
    $next_action_date = filter_var($_POST['next_action_date'],FILTER_SANITIZE_STRING);
    
    //Logo
    if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    $logo = htmlspecialchars($_FILES["logo"]["name"], ENT_QUOTES);
    if(!empty($logo)) {
        $logo_name = $sotid.'_'.$logo;
        move_uploaded_file($_FILES['logo']['tmp_name'],'download/'.$logo_name);   
		$history .= 'Updated Logo: '.$logo.'<br />';
    } else {
        $logo_name = $_POST['logo_file'];
    }

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

    //Sales Order Details
    if($_POST['has_details'] == 1) {
        include('../Sales Order/save_order_details.php');
    }

    //Order Details
    $inventory_pricing = filter_var($_POST['inventorypricing'],FILTER_SANITIZE_STRING);
	$history .= $inventory_pricing != $current_values['inventory_pricing'] ? 'Updated Inventory Pricing to '.$inventory_pricing.'<br />' : '';
    $vendor_pricing = filter_var($_POST['vendorpricing'],FILTER_SANITIZE_STRING);
	$history .= $vendor_pricing != $current_values['vendor_pricing'] ? 'Updated Vendor Pricing to '.$vendor_pricing.'<br />' : '';
    $inventory_pricing_team = filter_var($_POST['inventorypricing_team'],FILTER_SANITIZE_STRING);
	$history .= $inventory_pricing_team != $current_values['inventory_pricing_team'] ? 'Updated Team Inventory Pricing to '.$inventory_pricing_team.'<br />' : '';
    $vendor_pricing_team = filter_var($_POST['vendorpricing_team'],FILTER_SANITIZE_STRING);
	$history .= $vendor_pricing_team != $current_values['vendor_pricing_team'] ? 'Updated Team Vendor Pricing to '.$vendor_pricing_team.'<br />' : '';
    $discount_type = filter_var($_POST['discount_type'],FILTER_SANITIZE_STRING);
	$history .= $discount_type != $current_values['discount_type'] ? 'Updated Discount Type to '.$discount_type.'<br />' : '';
    $discount_value = filter_var($_POST['discount_value'],FILTER_SANITIZE_STRING);
	$history .= $discount_value != $current_values['discount_value'] ? 'Updated Discount Value to '.$discount_value.'<br />' : '';
    $delivery_type = filter_var($_POST['delivery_type'],FILTER_SANITIZE_STRING);
	$history .= $delivery_type != $current_values['delivery_type'] ? 'Updated Delivery Type to '.$delivery_type.'<br />' : '';
    $delivery_address = filter_var($_POST['delivery_address'],FILTER_SANITIZE_STRING);
	$history .= $delivery_address != $current_values['delivery_address'] ? 'Updated Delivery Address to '.$delivery_address.'<br />' : '';
    $contractorid = filter_var($_POST['contractorid'],FILTER_SANITIZE_STRING);
	$history .= $contractorid != $current_values['contractorid'] && (!empty($contractorid) || !empty($current_values['contractorid'])) ? 'Updated Delivery Contractor to '.get_client($dbc,$contractorid).'<br />' : '';
    if ($delivery_type != 'Company Delivery') {
        $delivery_amount = filter_var($_POST['delivery_amount'],FILTER_SANITIZE_STRING);
		$history .= $delivery_amount != $current_values['delivery_amount'] ? 'Updated Delivery Price to '.$delivery_amount.'<br />' : '';
    }
    $assembly_amount = filter_var($_POST['assembly_amount'],FILTER_SANITIZE_STRING);
	$history .= $assembly_amount != $current_values['assembly_amount'] ? 'Updated Assembly Price to '.$assembly_amount.'<br />' : '';
    $payment_type = filter_var($_POST['payment_type'],FILTER_SANITIZE_STRING);
	$history .= $payment_type != $current_values['payment_type'] ? 'Updated Payment Type to '.$payment_type.'<br />' : '';
    $deposit_paid = filter_var($_POST['deposit_paid'],FILTER_SANITIZE_STRING);
	$history .= $deposit_paid != $current_values['deposit_paid'] ? 'Updated Deposit Paid to '.$deposit_paid.'<br />' : '';
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
	$history .= $comment != $current_values['comment'] ? 'Added Comment: '.$comment.'<br />' : '';
    $ship_date = filter_var($_POST['ship_date'],FILTER_SANITIZE_STRING);
	$history .= $ship_date != $current_values['ship_date'] ? 'Updated Shipping Date to '.$ship_date.'<br />' : '';
    $due_date = filter_var($_POST['due_date'],FILTER_SANITIZE_STRING);
    $frequency = filter_var($_POST['frequency'],FILTER_SANITIZE_STRING);
    $frequency_type = filter_var($_POST['frequency_type'],FILTER_SANITIZE_STRING);
	$history .= $due_date != $current_values['due_date'] ? 'Updated Due Date to '.$due_date.'<br />' : '';

    $query_update = "UPDATE `sales_order_temp` SET `sales_order_type` = '$so_type', `name` = '$sales_order_name', `primary_staff` = '$primary_staff', `assign_staff` = '$assign_staff', `customerid` = '$businessid', `classification` = '$classification', `inventory_pricing` = '$inventory_pricing', `vendor_pricing` = '$vendor_pricing', `inventory_pricing_team` = '$inventory_pricing_team', `vendor_pricing_team` = '$vendor_pricing_team', `discount_type` = '$discount_type', `discount_value` = '$discount_value', `delivery_type` = '$delivery_type', `delivery_address` = '$delivery_address', `contractorid` = '$contractorid', `delivery_amount` = '$delivery_amount', `assembly_amount` = '$assembly_amount', `payment_type` = '$payment_type', `deposit_paid` = '$deposit_paid', `comment` = '$comment', `ship_date` = '$ship_date', `due_date` = '$due_date', `frequency` = '$frequency', `frequency_type` = '$frequency_type', `business_contact` = '$business_contacts', `logo` = '$logo_name', `status` = '$status', `next_action` = '$next_action', `next_action_date` = '$next_action_date' WHERE `sotid` = '$sotid'";
    mysqli_query($dbc, $query_update);
	
	//Notes
	$html_note = $_POST['note_text'];
	if(strip_tags($html_note) != '') {
		$history .= "Added Note: $html_note<br />";
		$note = filter_var(htmlentities($html_note),FILTER_SANITIZE_STRING);
		$send_email = filter_var($_POST['note_email_to'],FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `sales_order_notes` (`sales_order_id`,`note`,`email_comment`,`created_by`) VALUES ('$sotid','$note','$send_email','".$_SESSION['contactid']."')");
		if($_POST['send_note_email'] == 'send' && $send_email > 0) {
			$to = [get_contact($dbc, $send_email)=>get_email($dbc, $send_email)];
			$from = [$_POST['note_email_address']=>$_POST['note_email_name']];
			$subject = $_POST['note_email_subject'];
			$body = str_replace('[REFERENCE]',$html_note,$_POST['note_email_body']);
			try {
				send_email($from, $to, '', '', $subject, $body, '');
			} catch (Exception $e) {
				echo '<script> alert("'.$e->getMessage().'"); </script>';
			}
		}
	}

    //Save as Template
    if($_POST['add_sales_order'] == 'Save as Template') {
        if(empty($sales_order_name)) {
            $sales_order_name = SALES_ORDER_NOUN.' Form #'.$sotid;
        }
        $history .= 'Saved '.$sales_order_name.' as a Template<br />';

        $query_insert = mysqli_query($dbc, "INSERT INTO `sales_order_template` (`template_name`, `sales_order_type`) VALUES ('$sales_order_name', '$so_type')");
        $templateid = mysqli_insert_id($dbc);

        $product_list = mysqli_query($dbc, "SELECT * FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid'");
        while ($row = mysqli_fetch_array($product_list)) {
            $item_type = $row['item_type'];
            $item_type_id = $row['item_type_id'];
            $item_category = $row['item_category'];
            $item_name = $row['item_name'];
            $item_price = $row['item_price'];
            $time_estimate = $row['time_estimate'];
            $contact_category = $row['contact_category'];
            $heading_name = $row['heading_name'];
            $mandatory_quantity = $row['mandatory_quantity'];

            mysqli_query($dbc, "INSERT INTO `sales_order_template_product` (`item_type`, `item_type_id`, `item_category`, `item_name`, `item_price`, `contact_category`, `heading_name`, `mandatory_quantity`, `time_estimate`, `template_id`) VALUES ('$item_type', '$item_type_id', '$item_category', '$item_name', '$item_price', '$contact_category', '$heading_name', '$mandatory_quantity', '$time_estimate', '$templateid')");
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

    //Generate PDF
    if($_POST['add_sales_order'] == 'Generate PDF') {
        echo '<script type="text/javascript"> window.open("'.WEBSITE_URL.'/Sales Order/generate_pdf.php?sotid='.$sotid.'", "_blank"); </script>';
    }

    $redirect_url = 'index.php';
    if ($_POST['add_sales_order'] == 'Business Change' || $_POST['add_sales_order'] == 'Generate PDF') {
        $redirect_url = 'order.php?p=details&sotid='.$sotid;
    } else if ($_POST['add_sales_order'] == 'Order Details') {
        $redirect_url = 'order_details.php?sotid='.$sotid;
    } else if ($_POST['add_sales_order'] == 'Submit Order') {
        include('../Sales Order/submit_order_details.php');
    }
	
    echo '<script type="text/javascript"> window.location.replace("'.$redirect_url.'");</script>';
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
    var hash = window.location.hash.substr(1);
    if (hash != '') {
        $('#nav_'+hash).click();
    } else {
        $('#nav_customers').click();
    }

    //Submit Order
    $('[name="add_sales_order"][value="Submit Order"]').click(function() {
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
            alert(check_mandatory);
            return false;
        } else {
            if (confirm('Are you sure you want to submit your order?')) {
                return true;
            } else {
                return false;
            }
        }
    });

    //Save Template
    $('#save_as_template').click(function() {
        if(!confirm('Are you sure you want to save this <?= SALES_ORDER_NOUN ?> as a Template?')) {
            return false;
        }
    });

    //iFrame
    $('.iframe_open').click(function(){
        var sotid    = $('#sotid').val();
        var category = $(this).data('category');
        var title    = $(this).data('title');
        var pricing  = $(this).data('pricing');
        var contact_category = $(this).data('contact-category');
        $('#iframe_instead_of_window').attr('src', 'get_products.php?sotid='+sotid+'&category='+category+'&pricing='+pricing+'&contact_category='+contact_category);
        $('.iframe_title').text(title);
        $('.iframe_holder').show();
        $('.hide_on_iframe').hide();
        $('.iframe_holder iframe').outerHeight($('.iframe_holder').closest('.container').outerHeight());
    });

    $('.close_iframer').click(function(){
        $('.iframe_holder').hide();
        $('.hide_on_iframe').show();
        window.location.reload();
    });

    $('iframe').load(function() {
        this.contentWindow.document.body.style.overflow = 'scroll';
        this.contentWindow.document.body.style.minHeight = '0';
        this.contentWindow.document.body.style.paddingBottom = '15em';
        this.style.height = (this.contentWindow.document.body.offsetHeight + 10) + 'px';
    });

    $('.tile-sidebar a:not(.cursor-hand)').click(function() {
        $('.tile-sidebar li').removeClass('active');
        $(this).closest('li').addClass('active');
    });
    
    
    var $sections = $('.accordion-block-details');
    var $subsections = $('.accordion-block-details-sub');
    $('.main-screen-white').on('scroll', function(){
        var currentScroll = $('.main-screen .tile-container').offset().top + $('.main-screen-white').find('.preview-block-header').height();
        var $currentSection;
        $sections.each(function(){
            var divPosition = this.getBoundingClientRect().top;
            if( divPosition < currentScroll ){
                $currentSection = $(this);
            }
            var id = $currentSection.attr('id');
            $('.tile-sidebar .active:not(.sub_heading)').removeClass('active');
            $('.tile-sidebar [href=#'+id+']').find('li:not(.sidebar-higher-level)').addClass('active');
            $('.tile-sidebar a.cursor-hand[href=#'+id+']').addClass('active');
        });
        var $currentSection;
        $subsections.each(function() {
            var divPosition = this.getBoundingClientRect().top;
            if( divPosition < currentScroll ){
                $currentSection = $(this);
            }
            var id = $currentSection.attr('id');
            $('.tile-sidebar .active.sub_heading').removeClass('active');
            $('.tile-sidebar [href=#'+id+']').find('li.sub_heading').addClass('active');
        });
        $('.tile-sidebar a.cursor-hand').each(function() {
            if($(this).hasClass('active') && !($(this).closest('.sidebar_heading').find('ul').hasClass('in'))) {
                $(this).click();
            } else if(!($(this).hasClass('active')) && $(this).closest('.sidebar_heading').find('ul').hasClass('in')) {
                $(this).click();
            }
        });
    });

    var current_tab = [];
    $('[data-tab-name]:visible').each(function() {
        if(this.getBoundingClientRect().top < $('.main-screen .main-screen').offset().top + $('.main-screen .main-screen').height() &&
            this.getBoundingClientRect().bottom > $('.main-screen .main-screen').offset().top) {
            current_tab.push($(this).data('tab-name'));
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
        var so_type = $('#so_type').val();
        $.ajax({
            type: 'GET',
            url: 'ajax.php?fill=deleteSalesOrderForm&sotid='+sotid+'&so_type='+so_type,
            dataType: 'html',
            success: function(response) {
                window.location.href = 'index.php';
            }
        });
    }
}

window.onpopstate = function() {
    $('.iframe_holder').hide();
    $('.hide_on_iframe').show();
}
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
checkAuthorised('sales_order');
    $sales_order_name = '';
    $so_type = $_GET['so_type'];
    $primary_staff = $_SESSION['contactid'];
    $assign_staff = '';
    $customerid = '';
    $business_contact = '';
    $status = explode(',',$statuses)[0];
    $next_action = '';
    $next_action_date = '';
    $security_option = '';
    $inventory_pricing = '';
    $vendor_pricing = '';
    $inventory_pricing_team = '';
    $vendor_pricing_team = '';
    $discount_type = '';
    $discount_value = '';
    $delivery_type = '';
    $delivery_address = '';
    $contractorid = '';
    $delivery_amount = '';
    $assembly_amount = '';
    $payment_type = '';
    $deposit_paid = '';
    $comment = '';
    $ship_date = '';
    $due_date = '';
    $logo = '';

    if ( !empty($sotid) ) {
        $get_sot = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '$sotid'"));

        $sales_order_name = $get_sot['name'];
        $so_type = $get_sot['sales_order_type'];
        $primary_staff = $get_sot['primary_staff'];
        $assign_staff = $get_sot['assign_staff'];
        $customerid = $get_sot['customerid'];
        $classification = $get_sot['classification'];
        $business_contact = $get_sot['business_contact'];
        $status = $get_sot['status'];
        $next_action = $get_sot['next_action'];
        $next_action_date = $get_sot['next_action_date'];
        $security_option = $get_sot['security_option'];
        $inventory_pricing = $get_sot['inventory_pricing'];
        $vendor_pricing = $get_sot['vendor_pricing'];
        $inventory_pricing_team = $get_sot['inventory_pricing_team'];
        $vendor_pricing_team = $get_sot['vendor_pricing_team'];
        $discount_type = $get_sot['discount_type'];
        $discount_value = $get_sot['discount_value'];
        $delivery_type = $get_sot['delivery_type'];
        $delivery_address = $get_sot['delivery_address'];
        $contractorid = $get_sot['contractirid'];
        $delivery_amount = $get_sot['delivery_amount'];
        $assembly_amount = $get_sot['assembly_amount'];
        $payment_type = $get_sot['payment_type'];
        $deposit_paid = $get_sot['deposit_paid'];
        $comment = $get_sot['comment'];
        $ship_date = $get_sot['ship_date'];
        $due_date = $get_sot['due_date'];
        $frequency = $get_sot['frequency'];
        $frequency_type = $get_sot['frequency_type'];
        $logo = $get_sot['logo'];

        if(!empty($classification)) {
            $classification_query = " AND `classification` = '$classification'";
        } else {
            $classification_query = "";
        }
    }

    $statuses     = (!empty(get_config($dbc, 'sales_order_statuses'))) ? get_config($dbc, 'sales_order_statuses') : 'Opportunity,With Client,Fulfillment';
    $next_actions = (!empty(get_config($dbc, 'sales_order_next_actions'))) ? get_config($dbc, 'sales_order_next_actions') : 'Phone Call,Email';
    $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_so`"));
    $customer_cat = explode(',', $field_config['customer_category']);
    $customer_fields = ','.$field_config['customer_fields'].',';
    $value_config = ','.$field_config['fields'].',';
    if(!empty($so_type)) {
        $customer_cat = explode(',', get_config($dbc, 'so_'.config_safe_str($so_type).'_customer_category'));
        $customer_fields = ','.get_config($dbc, 'so_'.config_safe_str($so_type).'_customer_fields').',';
        $value_config = ','.get_config($dbc, 'so_'.config_safe_str($so_type).'_fields').',';
    }
    if(empty($customer_cat)) {
        $customer_cat = ['Business'];
    }
    if($customer_fields == ',,') {
        $customer_fields = ',Business Name,Region,Location,Classification,Phone Number,Email Address,';
    }
    $cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts` WHERE `sales_order_type` = '$so_type'"),MYSQLI_ASSOC);
?>

<?php if(strpos($value_config, ',Generate Name Customer Sotid,') !== FALSE) { ?>
<script type="text/javascript">
$(document).ready(function() {
    $('#save_order').click(function() {
        loadingOverlayShow('#sales_order_main');
    });
});
$(document).on('change', 'select[name="businessid"]', function() { updateSalesOrderName() });
function updateSalesOrderName() {
    var name = $('select[name="businessid"] option:selected').text()+' #'+$('#sotid').val();
    $('[name="sales_order_name"]').val(name);
}
</script>
<?php } ?>

<div id="sales_order_div" class="container">
    <div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe name="sales_order_iframe" src=""></iframe>
        </div>
    </div>

    <div class="iframe_holder" style="display:none;">
        <img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
        <span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
        <iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src="" scrolling="yes"></iframe>
    </div>
    
    <div class="row hide_on_iframe">
		<div class="main-screen" id="sales_order_main">
            <div class="loading_overlay" style="display: none; margin-top: -20px; padding-bottom: 20px;"><div class="loading_wheel"></div></div><?php
            include('tile_header.php');
            
            $page   = preg_replace('/\PL/u', '', $_GET['p']);
            $posid  = preg_replace('/[^0-9]/', '', $_GET['id']); ?>
            
            <div class="tile-container" style="height: 100%;">
                <!-- Sidebar -->
                <div class="standard-collapsible tile-sidebar tile-sidebar-noleftpad hide-on-mobile">
                    <ul style="overflow-y: auto; height: 100%;">
                        <li><a href="index.php">Dashboard</a></li>
                        <a href="#customers"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_customers" id="nav_customers">Customer</li></a>
                        <?php if (strpos($value_config, ',Sales Order Template,') !== FALSE) { ?>
                            <a href="#sales_order_template"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_sales_order_template" id="nav_sales_order_template"><?= SALES_ORDER_NOUN ?> Template</li></a>
                        <?php } ?>
                        <?php if (strpos($value_config, ',Copy Sales Order,') !== FALSE) { ?>
                            <a href="#sales_order_sales_order"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_sales_order_sales_order" id="nav_sales_order_sales_order">Copy <?= SALES_ORDER_NOUN ?></li></a>
                        <?php } ?>
                        <?php if (strpos($value_config, ',Sales Order Name,') !== FALSE) { ?>
                            <a href="#sales_order_name"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_sales_order_name" id="nav_sales_order_name"><?= SALES_ORDER_NOUN ?> Name</li></a>
                        <?php } ?>
                        <a href="#staff_information"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_staff_information" id="nav_staff_information">Staff Information</li></a>
                        <a href="#next_action"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_next_action" id="nav_next_action">Next Action</li></a>
                        <?php if (strpos($value_config, ',Logo,') !== FALSE) { ?>
                            <a href="#logo"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_logo" id="nav_logo">Logo</li></a>
                        <?php } ?>
                        <?php if (strpos($value_config, ',Custom Designs,') !== FALSE) { ?>
                            <a href="#upload_design"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_upload_design" id="nav_upload_design">Custom Designs</li></a>
                        <?php } ?>
                        <?php foreach ($cat_config as $contact_cat) { ?>
                            <a href="#<?= $contact_cat['contact_category'] ?>_roster"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_<?= $contact_cat['contact_category'] ?>_roster" id="nav_<?= $contact_cat['contact_category'] ?>_roster"><?= $contact_cat['contact_category'] ?> Roster</li></a>
                            <a href="#<?= $contact_cat['contact_category'] ?>_order"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_<?= $contact_cat['contact_category'] ?>_order" id="nav_<?= $contact_cat['contact_category'] ?>_order"><?= $contact_cat['contact_category'] ?> Order</li></a>
                        <?php } ?>
                        <?php if(empty($cat_config)) {
                            $heading_list = array_filter(array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `heading_name`, `mandatory_quantity`, IF(`heading_sortorder` = 0, NULL, `heading_sortorder`) `heading_sortorder` FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `contact_category` = '**no_cat**' GROUP BY `heading_name` ORDER BY `item_type` <> 'inventory', `item_type` <> 'vendor', `item_type` <> 'services', `heading_sortorder` IS NOT NULL, `heading_sortorder` ASC"),MYSQLI_ASSOC),'heading_name'));
                            if(!empty($heading_list)) { ?>
                                <li class="sidebar_heading sidebar-higher-level">
                                    <a href="#nocat_order" id="no_cat" data-contactid="<?= $customerid ?>" class="sidebar_heading_collapse collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_nocat_order"><?= SALES_ORDER_NOUN ?> Details<span class="arrow"></span></a>
                                    <ul id="collapse_nocat_order" class="collapse">
                                        <?php foreach($heading_list as $heading) { ?>
                                            <a href="#nocat_order_<?= config_safe_str($heading) ?>" data-target="#collapse_nocat_order_<?= config_safe_str($heading) ?>"><li class="sub_heading"><?= $heading ?></li></a>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } else { ?>
                                <a href="#nocat_order" id="no_cat" data-contactid="<?= $customerid ?>"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_nocat_order" id="nav_no_cat_roster"><?= SALES_ORDER_NOUN ?> Details</li></a>
                            <?php } ?>
                        <?php } ?>
                        <a href="#order_details"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_order_details" id="nav_order_details">Order Details</li></a>
                        <?php if (strpos($value_config, ',Notes,') !== FALSE) { ?>
                            <a href="#notes"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_order_notes" id="nav_order_notes">Notes</li></a>
                        <?php } ?>
                        <a href="#history"><li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_order_history" id="nav_order_history">History</li></a>
                        <!-- <li class="collapsed cursor-hand" data-toggle="collapse" data-target="#collapse_security" id="nav_security"><a href="#security">Security Option</a></li> -->
                    </ul>
                </div><!-- .tile-sidebar -->
                
                <!-- Main Screen --><?php
                if ( $page=='preview' || empty($page) ) { ?>
                    <div class="col-xs-12 col-sm-9 tile-content"><?php
                        include('preview.php'); ?>
                    </div><?php
                
                } else { ?>
                    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

                        <!-- Preview Bar -->
                        <?php $detect = new Mobile_Detect;
                        $is_mobile = ( $detect->isMobile() ) ? true : false;
                        if($_GET['iframe_slider'] == 1) {
                            $is_mobile = true;
                        }
                        if(!$is_mobile) { ?>
                            <div class="scalable preview-bar hide-titles-mob" style="<?= $scale_style ?>"><?php
                                include('details_preview.php'); ?>
                                <div class="pull-right gap-top gap-right buttons_div">
                                    <button type="submit" id="save_as_template" name="add_sales_order" value="Save as Template" class="btn brand-btn">Save as Template</button>
                                    <?php if(!empty($_GET['sotid'])) { ?>
                                        <button type="submit" name="add_sales_order" value="Generate PDF" class="btn brand-btn">Generate PDF</button>
                                    <?php } ?>
                                    <a href="index.php" class="btn brand-btn">Cancel</a>
                                    <button type="submit" name="add_sales_order" value="Submit" class="btn brand-btn">Save</button>
                                    <button type="submit" id="save_order" name="add_sales_order" value="Business Change" class="btn brand-btn" style="display: none;">Save</button>
                                    <!-- <button type="submit" name="add_sales_order" value="Submit_Order" onclick="return confirm('Are you sure you want to submit your order?');" class="btn brand-btn">Submit Order</button> -->
                                    <?php if(empty($cat_config)) { ?>
                                        <button type="submit" name="add_sales_order" value="Submit Order" class="btn brand-btn">Submit Order</button>
                                    <?php } else { ?>
                                        <button type="submit" name="add_sales_order" value="Order Details" class="btn brand-btn">Continue To Order Details</button>
                                    <?php } ?>
                                    <a href="#" onclick="deleteSalesOrderForm(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png" class="no-toggle" title="Archive"></a>
                                </div>
                            </div><!-- .preview-bar -->
                        <?php } ?>

                        <div class="scale-to-fill has-main-screen"><?php
                            include('details.php'); ?>
                        </div>

                        <?php if($is_mobile) { ?>
                            <!-- Preview Bar -->
                            <div class="col-xs-12 show-on-mob preview-bar"><?php
                                include('details_preview.php'); ?>
                                <div class="pull-right gap-top gap-right buttons_div">
                                    <button type="submit" id="save_as_template" name="add_sales_order" value="Save as Template" class="btn brand-btn">Save as Template</button>
                                    <?php if(!empty($_GET['sotid'])) { ?>
                                        <button type="submit" name="add_sales_order" value="Generate PDF" class="btn brand-btn">Generate PDF</button>
                                    <?php } ?>
                                    <a href="index.php" class="btn brand-btn">Cancel</a>
                                    <button type="submit" name="add_sales_order" value="Submit" class="btn brand-btn">Save</button>
                                    <button type="submit" id="save_order" name="add_sales_order" value="Business Change" class="btn brand-btn" style="display: none;">Save</button>
                                    <!-- <button type="submit" name="add_sales_order" value="Submit_Order" onclick="return confirm('Are you sure you want to submit your order?');" class="btn brand-btn">Submit Order</button> -->
                                    <?php if(empty($cat_config)) { ?>
                                        <button type="submit" name="add_sales_order" value="Submit Order" class="btn brand-btn">Submit Order</button>
                                    <?php } else { ?>
                                        <button type="submit" name="add_sales_order" value="Order Details" class="btn brand-btn">Continue To Order Details</button>
                                    <?php } ?>
                                    <a href="#" onclick="deleteSalesOrderForm(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png" class="no-toggle" title="Archive"></a>
                                </div>
                            </div><!-- .preview-bar -->
                        <?php } ?>

                    </form><?php
                } ?>
                
                <div class="clearfix"></div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>

<?php

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $password = '';
    for ($i = 0; $i < 8; $i++) {
        $rng = rand(0, strlen($alphabet));
        $password .= substr($alphabet, $rng, 1);
    }

    return $password;
}