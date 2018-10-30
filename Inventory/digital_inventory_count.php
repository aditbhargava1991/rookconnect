<?php
/*
Inventory Listing
*/
error_reporting(0);

include ('../database_connection.php');
include ('../function.php');
include ('../global.php');
include ('../header.php');
include_once('../tcpdf/tcpdf.php');

if(vuaed_visible_function($dbc, 'inventory') !== 1) {
	echo '<script>alert("You do not have access to this page, please talk to your software administrator to gain access.");</script>';
	header('Location: inventory.php?category=Top');
	die();
}

if(isset($_POST['update_current_inv'])) {
	$category = $_POST['category_getter'];
	if($category == '' || $category == NULL || $category == '3456780123456971230' || $category == '3456780123456971232' ) {
		$sql = mysqli_query($dbc, 'UPDATE inventory set quantity = digital_count_qty WHERE deleted = 0 AND digital_count_qty != ""');
		$sql = mysqli_query($dbc, 'UPDATE inventory set digital_count_qty = "", digital_count_qty_multiple = "" WHERE deleted = 0 AND digital_count_qty != ""');
	} else {
		$sql = mysqli_query($dbc, 'UPDATE inventory set quantity = digital_count_qty WHERE deleted = 0 AND digital_count_qty != "" AND category = "'.$category.'"') or die(mysqli_error($dbc));
		$sql = mysqli_query($dbc, 'UPDATE inventory set digital_count_qty = "", digital_count_qty_multiple = "" WHERE category = "'.$category.'"');
	}
	header('Location: digital_inventory_count.php?category='.$category.'');
}

if(isset($_POST['reset_ai'])) {
	$category = $_POST['category_getter'];
	if($category == '' || $category == NULL || $category == '3456780123456971230' || $category == '3456780123456971232' ) {
		$sql = mysqli_query($dbc, 'UPDATE inventory set digital_count_qty = "", digital_count_qty_multiple = ""');
	} else {
		$sql = mysqli_query($dbc, 'UPDATE inventory set digital_count_qty = "", digital_count_qty_multiple = "" WHERE category = "'.$category.'"');
	}
    header('Location: digital_inventory_count.php?category='.$category.'');
}

$rookconnect = get_software_name();
/*
if(isset($_POST['variance_report'])) {

	// {{{{{{ **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**##
	$datetime = date('l jS \of F Y h:i:s A');
	$fname = $_SESSION['first_name'];
	$category = $_POST['category_getter'];
	$lname = $_SESSION['last_name'];
	$query_insert_vr = "INSERT INTO `variance_reports` (`created_date`, `user`) VALUES ('$datetime', '".$fname." ".$lname."')";

 		$results_are_in = mysqli_query($dbc, $query_insert_vr);
        $vrid = mysqli_insert_id($dbc);

		 $query_ins_vr = "UPDATE `variance_reports` SET `pdf_link` = 'var_report_".$vrid.".pdf' WHERE vrid = '".$vrid."'";
		 $results_in2 = mysqli_query($dbc, $query_ins_vr);
		// PDF

		class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			// Logo
			//$image_file = 'img/SmartEnergyAlternates-Logo-Stacked-400px.jpg';
			//$this->Image($image_file, 10, 10, 51, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			// Set font
			//$this->SetFont('helvetica', 'B', 20);
			// Title
			//$this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-25);
			// Set font
			//$this->SetFont('helvetica', 'I', 8);
			// Page number
			$footer_text = '';
			//$footer_text = '<b>Smart Energy Alternates</b>&nbsp;&nbsp;&nbsp;Address, Calgary, Alberta, Postal C, Phone: 403-111-1111'.'<br><br>Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages();

			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
		}
	}

// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();


	$html = 'Report by: '.$fname.' '.$lname.'<br>Date: '.$datetime.'<br><br><center><div style="margin-top:10px; text-align:center;"><h1>Variance Report</h1></div></center>
<div style="font-size:10px;">


		<table style="padding:3px; text-align:center;" border="1px" class="table table-bordered">
	<tr style="padding:3px;  text-align:Center" >
		<th colspan="8" style="background-color:grey; color:black;">Customer Information</th>
	</tr>
	<tr style="padding:3px;  text-align:Center; background-color:white; color:black;" >
		<td>Category</td>
		<td>Name</td>
		<td>Shipping $</td>
		<td>Exchange $</td>
		<td>Canadian CPU</td>
		<td>Expected Inventory</td>
		<td>Actual Inventory</td>
		<td>Variance</td>
	</tr>';
		$resultg = mysqli_query($dbc, 'SELECT * FROM inventory WHERE deleted = 0 AND category = "'.$category.'"');

	 while($row = mysqli_fetch_array( $resultg ))

            {
				$vari = ($row['digital_count_qty'] - $row['quantity']);

                    if($vari == 0) {
					$styler = '';
					}

                    if($vari > 0) {
					$styler = 'background-color: rgb(147, 254, 147);';
					}

					if($vari < 0) {
					$styler = 'background-color:rgb(255, 147, 143);';
					}
				$html.= '<tr style="background-color:lightgrey; '.$styler.' color:black;">';
				$html.= '<td>' . $row['category'] . '</td>';
                $html.= '<td>' . $row['name'] . '</td>';
                $html.= '<td>$' . $row['shipping_cash'] . '</td>';
				$html.= '<td>$' . $row['exchange_cash'] . '</td>';
				$html.= '<td>$' . $row['cdn_cpu'] . '</td>';
				$html.= '<td>'.$row['quantity'].'</td>';
				$html.= '<td>'.$row['digital_count_qty'].'</td>';
				$html.= '<td>'.$vari.'</td>';
				$html.= '</tr>';
			}
	$html.= '
	</table>
	';

	if (!file_exists('PDF/vr')) {
		mkdir('PDF/vr', 0777, true);
	}

	$pdf->writeHTML($html, true, false, true, false, '');
	?><?php
	$pdf->Output('PDF/vr/var_report_'.$vrid.'.pdf', 'F');
	//header("Location: seaPDF/vr/invoice_".$invoiceid.".pdf");

		// Send PDF

    $query = mysqli_query($dbc, "SELECT staffid FROM privileges WHERE inventory LIKE '%D%'");

    while($row = mysqli_fetch_array( $query )) {
      $staffid = $row['staffid'];

            $query1 = mysqli_query($dbc, "SELECT email_address FROM staff WHERE staffid = '$staffid'");

            while($row1 = mysqli_fetch_array( $query1 )) {
                $emails .= "".$row1['email_address'].", ";
            }
    }
    // vv use this once software is live vv
        //$to_ffm = ''.$emails.'';
    // ^^ use this once software is live instead of line below (v) ^^
		$to_ffm = 'kelseynealon@freshfocusmedia.com';
		$subject_ffm = 'Variance Report - Submitted by '.$fname.' '.$lname.'';

		$message_ffm = 'Please find the submitted Variance Report below.';

		$file = 'seaPDF/vr/var_report_'.$vrid.'.pdf';
		$filename = basename($file);
		$file_size = filesize($file);
		$content = chunk_split(base64_encode(file_get_contents($file)));
		$uid = md5(uniqid(time()));
		$headers_ffm = "From: info@freshfocusmedia.com/\r\n"
		  ."MIME-Version: 1.0\r\n"
		  ."Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n"
		  ."This is a multi-part message in MIME format.\r\n"
		  ."--".$uid."\r\n"
		  ."Content-type:text/html; charset=iso-8859-1\r\n"
		  ."Content-Transfer-Encoding: 7bit\r\n\r\n"
		  .$message_ffm."\r\n\r\n"
		  ."--".$uid."\r\n"
		  ."Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"
		  ."Content-Transfer-Encoding: base64\r\n"
		  ."Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n"
		  .$content."\r\n\r\n"
		  ."--".$uid."--";
		  mail($to_ffm, $subject_ffm, $message_ffm, $headers_ffm);

		// Send PDF
    // PDF

//  }}}}}} **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**## **##**##**##**##**##**##**## THIS CURRENTLY IS NOT WORKING **##**##**##**##**##**##**##
}
*/
if(isset($_POST['export_inv_admin'])) {
	$today_date = date('Y-m-d');
	$FileName = 'exports/'."export_".$today_date.".csv";
	$file = fopen($FileName,"w");
	$category = $_POST['category_getter'];
	if($category == '' || $category == NULL || $category == '3456780123456971230' || $category == '3456780123456971232' ) {
		$sql = mysqli_query($dbc, "SELECT  Category, Name, USD_Invoice AS 'USD Invoice', Shipping_Cash AS 'Shipping $', Exchange_Cash AS 'Exchange $', Cdn_CPU AS 'Cdn CPU', quantity AS 'Expected Inventory', digital_count_qty AS 'Actual Inventory' FROM inventory WHERE deleted = 0 ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name");
	} else {
		$sql = mysqli_query($dbc, "SELECT  Category, Name, USD_Invoice AS 'USD Invoice', Shipping_Cash AS 'Shipping $', Exchange_Cash AS 'Exchange $', Cdn_CPU AS 'Cdn CPU', quantity AS 'Expected Inventory', digital_count_qty AS 'Actual Inventory' FROM inventory WHERE category = '".$category."' AND deleted = 0 ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name");
	}
	//$sql = mysqli_query($dbc, 'SELECT first_name, last_name, email_address FROM member');
	$row = mysqli_fetch_assoc($sql);

	// Save headings alon
		$HeadingsArray=array();
		foreach($row as $name => $value){
			$HeadingsArray[]=$name;
		}
		fputcsv($file,$HeadingsArray);

	// Save all records without headings
		while($row = mysqli_fetch_assoc($sql)){
		$valuesArray=array();
			foreach($row as $name => $value){
			$valuesArray[]=$value;
			}
		fputcsv($file,$valuesArray);
		}
		fclose($file);

	header("Location: $FileName");
}

?>
<script type="text/javascript" src="inventory.js"></script>
<script type="text/javascript">
$(document).ready(function() {
		$('.category_get').val($('.category_actual').val());
		$('#form321').attr('action', 'digital_inventory_count.php?category='+$('.category_actual').val());

		$('.category_actual').on('change', function() {
			$('#form321').attr('action', 'digital_inventory_count.php?category='+$('.category_actual').val());
		});

    $(".act_inv").focusout(function() {
		var thisval = parseFloat($(this).val());
		if(!isNaN(thisval)) {
			var act_inv = $(this).attr('id');
			var arr = act_inv.split('_');
			if(typeof arr[2] != 'undefined') {
				var input_value = parseFloat($('#actinv_'+arr[1]+'_'+arr[2]).val());
				var order = arr[2];
				var order_tru = arr[2];
			} else {
				var input_value = parseFloat($('#actinv_'+arr[1]).val());
				var order = '';
				var order_tru = 1;
			}
			var original_qty = parseFloat($('.quantity_'+arr[1]).text());
			var variance = parseInt(input_value-original_qty);
			//alert(variance +' '+original_qty+' '+input_value);

		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "inventory_ajax_all.php?fill=actual_inventory&order="+order+"&variance="+variance+"&edit=false&original_qty="+original_qty+"&name="+thisval+"&invid="+arr[1],
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('.variance_'+arr[1]).text(response);
				if(response > 0) {
					$('.variance_'+arr[1]).parent().css('background', 'rgba(37,178,37,.75)', 'important');
				}
				if(response < 0) {
					$('.variance_'+arr[1]).parent().css('background', 'rgba(255, 70, 70, 1)', 'important');
				}
				if(response == 0) {
					$('.variance_'+arr[1]).parent().css('background', 'inherit', 'important');
				}
			}

		});

		} else { console.log('isNaN'); }
	});

    //$('.act_inv').focusout();

	// Give QTY a location name ...
	$('.edit_this').on( 'click', function () {
	var editid = $(this).attr('id');
	var arr = editid.split('_');
	if(typeof arr[2] != 'undefined') {
		var input_value = $('#actinv_'+arr[1]+'_'+arr[2]).val();
		var location = $('#location_'+arr[1]+'_'+arr[2]).val();
		var order = arr[2]
	} else {
		var input_value = $('#actinv_'+arr[1]).val();
		var location = $('#location_'+arr[1]+'_1').val();
		var order = 1;
	}
	if(input_value == '' || isNaN(input_value)) {
		alert("Please add a quantity to this input before you give it a location.");
	} else {
		var locator = prompt("Please enter the location of where this inventory was counted:", location);
		var original_qty = parseFloat($('.quantity_'+arr[1]).text());
		if (locator != null && locator != '') {
			var stripped_locator = locator.replace(/[^\w\s]/gi, '');
			$('#location_'+arr[1]+'_'+order).val(stripped_locator);
			// save location into the database...
			$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "inventory_ajax_all.php?fill=actual_inventory&order="+order+"&edit=false&location=true&name_of_location="+stripped_locator+"&original_qty="+original_qty+"&name="+input_value+"&invid="+arr[1],
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('.variance_'+arr[1]).text(response);
			}

			});

		}
	}

    });

	$('.delete_this').on( 'click', function () {
		if (confirm('Are you sure you want to remove this input field?')) {
			var deleteid = $(this).attr('id');
			var arr = deleteid.split('_');
			$('#addthese_'+arr[1]+'_'+arr[2]).remove();
			//get total count of rest of inputs
			var count_tot = $('.totalofinputs_'+arr[1]).attr('id').split('_');
			var count_tot_new = parseInt(count_tot[1]);
			//change rest of inputs numbers to fill up the gap...
			var num_left = parseInt(arr[2]);
			for(var i = (num_left); i < count_tot_new; i++) {
				var new_i = parseInt(i);
				var old_i = parseInt(i)+1;
				$('#deletethis_'+arr[1]+'_'+old_i).attr('id', 'deletethis_'+arr[1]+'_'+new_i);
				$('#editthis_'+arr[1]+'_'+old_i).attr('id', 'editthis_'+arr[1]+'_'+new_i);
				$('#actinv_'+arr[1]+'_'+old_i).attr('id', 'actinv_'+arr[1]+'_'+new_i);
				$('#addthese_'+arr[1]+'_'+old_i).attr('id', 'addthese_'+arr[1]+'_'+new_i);
			}
			var new_total_count = parseInt(count_tot[1])-1;
			$('.totalofinputs_'+arr[1]).attr('id', 'totalofinputs_'+new_total_count);
			var original_qty = parseFloat($('.quantity_'+arr[1]).text());
			var variance = '';
			var thisval = '';
			var order = arr[2];
			// Update the database
			$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "inventory_ajax_all.php?fill=actual_inventory&order="+order+"&variance="+variance+"&edit=true&original_qty="+original_qty+"&name="+thisval+"&invid="+arr[1],
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('.variance_'+arr[1]).text(response);
				if(response > 0) {
					$('.variance_'+arr[1]).parent().css('background', 'rgba(37,178,37,.75)', 'important');
				}
				if(response < 0) {
					$('.variance_'+arr[1]).parent().css('background', 'rgba(255, 70, 70, 1)', 'important');
				}
				if(response == 0) {
					$('.variance_'+arr[1]).parent().css('background', 'inherit', 'important');
				}
			}

			});
		}

    });

	//BEGIN INVENTORY CODE
    $('.add_new_input').on( 'click', function () {
		var newinpid = $(this).attr('id');
		var arr = newinpid.split('_');

		var count_id = $('.totalofinputs_'+arr[1]).attr('id');
		var count = count_id.split('_')[1];
		count++;
        var clone = $('#addthese_'+arr[1]).clone(true);

		clone.find('#actinv_'+arr[1]).val('');
        clone.find('#deletethis_'+arr[1]).attr('id', 'deletethis_'+arr[1]+'_'+count);
		clone.find('#editthis_'+arr[1]).attr('id', 'editthis_'+arr[1]+'_'+count);
		clone.find('#actinv_'+arr[1]).attr('id', 'actinv_'+arr[1]+'_'+count);
		clone.find('.hide_this_object').removeAttr('class', 'hide_this_object');
		clone.removeAttr('id', 'addthese_'+arr[1]);
		clone.attr('id', 'addthese_'+arr[1]+'_'+count);

        $('#add_here_new_position_'+arr[1]).append(clone);


		$('.totalofinputs_'+arr[1]).attr('id', 'totalofinputs_'+count);
    });

});
</script>
<style>
@media(min-width:801px) {
	.no_mobile_style {
		padding: 0px !important;
		width: 200px;
		vertical-align:middle !important;
	}
}
.buttons_box {
	width:48%;margin:auto; background-color:rgba(244,244,244,0.5);  min-height:35px; vertical-align:top; border:1px grey solid; padding:2px; display:inline-block; text-align:center;
}
.hide_this_object {
	display:none;
}
.additional_position {
	border-bottom:1px solid grey;
	min-height:35px;
}
.cursor_pointer {
	cursor: pointer;
}
.act_inv {
	display:inline-block; width:49%; min-height:35px; outline-color:green; padding-left:10px; background:white; color: black; border:0px;
}
</style>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('inventory');
?>
<div class="container" id="inventory_div">
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<?php include('../Inventory/tile_header.php'); ?>
			</div>

			<div class="tile-container" style="height: 100%;">

				<div class="standard-collapsible tile-sidebar set-section-height hide-titles-mob">
					<ul class="sidebar">
						<li class="active">Inventory Count</li>
					</ul>
				</div>

		        <div class="scale-to-fill has-main-screen tile-content">
					<div class="main-screen standard-body">
						<div class="standard-body-title"><h3>Digital Inventory Count</h3></div>
						<div class="standard-body-content pad-left pad-right pad-top">
							<form name="form_clients" method="post" action="digital_inventory_count.php?category=<?php if(isset($_POST['search_client_submit'])) { echo $_POST['category_search']; } ?>" class="form-inline" id="form321" role="form">
								<div class='mobile-100-container'>
									<input type='hidden' class='category_get' value='' name='category_getter'>

									<button type="submit" style="position:relative; margin-top:3px !important" name="update_current_inv" value="Update Current Inventory" onclick="return confirm('Are you sure you want to update the current inventory for all products? \nNote: this will reset all values in the Actual Inventory columns.');" class="btn brand-btn pull-right upd_cur_inv mobile-100-pull-right">Update Current Inventory</button><?php /*
									<button type="submit" style=" position:relative;margin-top:3px !important" name="variance_report" value="Create Variance Report" class="btn brand-btn pull-right upd_cur_inv mobile-100-pull-right" onclick="return confirm('Are you sure you want to submit your current count?');">Create Variance Report</button> <?php */ ?>
									<button type="submit" style="position:relative;margin-top:3px !important" name="reset_ai" value="Reset Actual Inventory" onclick="return confirm('Are you sure you want to reset the count?');" class="btn brand-btn pull-right upd_cur_inv mobile-100-pull-right">Reset Count</button>
									<button type="submit" style=" position:relative;margin-top:3px !important;" name="export_inv_admin" value="Export Inventory" class="btn brand-btn pull-right mobile-100-pull-right">Export Inventory Count</button>

								</div>

								<br><br>

								<div  style='display:block; margin-bottom:5px;' class="single-pad-bottom">
				                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4">
								<label for="search_site" style='width:100%; text-align:right;'>Search:</label>
								</div>
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8" style='margin-bottom:10px;'>
				                    <?php if(isset($_POST['search_client_submit'])) { ?>
										<input type="text" name="search_client" value="<?php echo $_POST['search_client']?>" class="form-control">
									<?php } else { ?>
										<input type="text" name="search_client" class=" form-control">
									<?php } ?>
								</div>
								<?php
								$sql = mysqli_query($dbc, "SELECT * FROM inventory WHERE deleted = 0 GROUP BY category ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name");
                                $db_value = get_config($dbc, 'default_digi_count_tab');
                                $db_query_mod = ( $db_value != '3456780123456971230' && $db_value != '3456780123456971232' ) ? "AND category='$db_value'" : '' ?>

								<div class="col-lg-1 col-md-1 col-sm-1 col-xs-4">
								<label for="travel_task" class="" style='text-align:right;width:100%;'>Tab:</label>
								</div>
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8" style='margin-bottom:10px;'>
									<select name="category_search" class="chosen-select-deselect form-control category_actual" width="380"><?php
                                        $selected_recent = ($_POST['category_search'] == '3456780123456971232' || $_GET['category'] == '3456780123456971232' || $db_value == '3456780123456971232' || (empty($_POST['category_search']) && empty($_GET['category']) && empty($db_value))) ? 'selected' : '';
                                        $selected_all = ($_POST['category_search'] == '3456780123456971230' || $_GET['category'] == '3456780123456971230' || $db_value == '3456780123456971230') ? 'selected' : ''; ?>
                                        <option value="3456780123456971232" <?= $selected_recent ?>>Most Recently Added (25 Rows)</option>
                                        <option value="3456780123456971230" <?= $selected_all ?>>All Tabs</option>
                                        <?php
                                        while($row = mysqli_fetch_assoc($sql)){
                                            $selected = '';
                                            if(isset($_POST['search_client_submit'])) {
                                                if($row['category'] == $_POST['category_search']) {
                                                    $selected = 'selected';
                                                }
                                            } else if(isset($_GET['category'])) {
                                                if($row['category'] == $_GET['category']) {
                                                    $selected = 'selected';
                                                }
                                            } else {
                                                $selected = $row['category'] == $db_value ? 'selected="selected"' : '';
                                            }
                                            echo '<option value="'.$row['category'].'" '.$selected.'>'.$row['category'].'</option>';
                                        }
                                        ?>
									</select>
								</div>
								<div class="col-lg-5 col-md-4 col-sm-4 col-xs-offset-4 col-sm-offset-0 col-md-offset-0 col-lg-offset-0">
									<button  type="submit" name="search_client_submit" value="Search" class="btn brand-btn">Search</button>
									<button type="submit" name="display_all_client" value="Display All" class="btn brand-btn">Display All</button>
								</div>
							</div>
								<div class="clearfix"></div>
				            <?php
				            // Display Pager

				            $rowsPerPage = ITEMS_PER_PAGE;
				            $pageNum = 1;

				            if(isset($_GET['page'])) {
				            	$pageNum = $_GET['page'];
				            }

				            $offset = ($pageNum - 1) * $rowsPerPage;

				            $client_name = '';
							$category_search = '';
				            if (isset($_POST['search_client_submit'])) {
				                $client_name = $_POST['search_client'];
								if($_POST['search_client'] != '') {
									$client_name = " AND (name LIKE '%" . $client_name . "%' OR category LIKE '%" . $client_name . "%' OR usd_invoice LIKE '%" . $client_name . "%' OR shipping_cash LIKE '%" . $client_name . "%' OR exchange_cash LIKE '%" . $client_name . "%' OR cdn_cpu LIKE '%" . $client_name . "%' OR quantity LIKE '%" . $client_name . "%' OR digital_count_qty LIKE '%" . $client_name . "%')";
								}
								if($_POST['category_search'] != '') {
									$category_search = $_POST['category_search'];
									if($category_search != '3456780123456971230' && $category_search != '3456780123456971232' ) {
										$category_search = " AND category = '".$category_search."'";
									} else {
                                        $category_search = '';
                                    }
								}
							}
                            
                            if($client_name != '' || $category_search != '') {
                                $query_check_credentials = "SELECT  * FROM inventory WHERE deleted = 0 ".$client_name." ".$category_search." ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name";
                            } else if(isset($_GET['category']) && ($_GET['category'] != '' && $_GET['category'] != NULL && $_GET['category'] != '3456780123456971230'  && $_GET['category'] != '3456780123456971232')) {
                                $category = $_GET['category'];
                                $query_check_credentials = "SELECT * FROM inventory WHERE deleted = 0 AND category = '".$category."' ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name";
                            } else if(isset($_GET['category']) && $_GET['category'] == '3456780123456971230'){
                                $query_check_credentials = "SELECT * FROM inventory WHERE deleted = 0 ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name";
                            } else if ( empty($client_name) && empty($category_search) && !isset($_GET['category']) && !empty($db_value) ) {
                                $query_check_credentials = "SELECT * FROM inventory WHERE deleted = 0 ".$db_query_mod." ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name";
                            } else {
                                $query_check_credentials = "SELECT * FROM inventory WHERE deleted = 0 ORDER BY inventoryid DESC LIMIT 25";
                            }
                            
				            // how many rows we have in database
				            $query = "SELECT COUNT(clientid) AS numrows FROM clients";
				            if($client_name == '') {
				                //echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $query, $pageNum, $rowsPerPage).'</h1>';
				            }
				            $result = mysqli_query($dbc, $query_check_credentials);
				            $num_rows = mysqli_num_rows($result);
						    if($num_rows > 0) {
								echo "<form><div id='no-more-tables'><table border='2' cellpadding='10' class='table table-striped'>";
				                echo "<thead><tr class='hidden-xs hidden-sm'>
				                <th>Tab</th>
								<th>Name</th>
								<th>Shipping $</th>
								<th>Exchange $</th>";
								
								if ( $rookconnect == 'sea' && isset ( $_SESSION['user_name'] ) && $_SESSION['user_name'] == 'kristi' ) {
									//Don't show Canadian CPU
								} else {
									echo "<th>Canadian CPU</th>";
								}
								
								echo "<th>Expected Inventory</th>
				                <th>Actual Inventory</th>
				                <th>Variance</th>
								";
				                echo "</tr></thead>";


				            } else{
				            	echo "<h2>No Record Found.</h2>";
				            }

				            while($row = mysqli_fetch_array( $result ))
				            {
				            	echo "<tr>";
								if($row['shipping_cash'] == '' || $row['shipping_cash'] == NULL) {
									$shipping_cash = 0;
								} else { $shipping_cash = $row['shipping_cash']; }
								if($row['exchange_cash'] == '' || $row['exchange_cash'] == NULL) {
									$exchange_cash = 0;
								} else { $exchange_cash = $row['exchange_cash']; }
								if($row['cdn_cpu'] == '' || $row['cdn_cpu'] == NULL) {
									$cdn_cpu = 0;
								} else { $cdn_cpu = $row['cdn_cpu']; }
								echo '<td data-title="Tab">' . $row['category'] . '</td>';
				                echo '<td data-title="Name">' . $row['name'] . '</td>';
				                echo '<td data-title="Shipping $">$' . $shipping_cash . '</td>';
								echo '<td data-title="Exchange $">$' . $exchange_cash . '</td>';
								
								if ( $rookconnect == 'sea' && isset ( $_SESSION['user_name'] ) && $_SESSION['user_name'] == 'kristi' ) {
									//Don't show Canadian CPU
								} else {
									echo '<td data-title="Canadian CPU">$' . $cdn_cpu . '</td>';
								}
								
								echo '<td data-title="Expected Qty"><input type="hidden" class="exp_inv" value="'.$row['quantity'] . '">' . $row['quantity'] . '</td>';
					                  /* echo '<td style="padding:0px; width:100px"><div title="Click to Edit" class="act_inv1" style="cursor:pointer; background:rgb(128, 128, 128, 0.5); padding-left:10px;   line-height: 35px; width: 100px; height:35px; "></div><input style="display:none; width:100%; height:35px; outline-color:#be5; padding-left:10px; background:transparent; color: white; border:0px; " class="act_inv" value="'.$row['actual_inventory1'].'" type="text"><input type="hidden" class="invid" value="'.$row['inventoryid'].'"><span class="act_inv2" style="display:none;"></span></td>';
									   */
									   echo '
									   <td  data-title="Actual Qty" class="no_mobile_style">
									   ';
									   $other_inputs = '';
									   $first_qty = '';
									   $i = 1;
									   $queryer = "SELECT * FROM inventory WHERE inventoryid='".$row['inventoryid']."'";
										$resulter = mysqli_query($dbc,$queryer) or die(mysqli_error($dbc));
										$get_config = mysqli_fetch_assoc($resulter);
										$multiple_qty_counter = $get_config['digital_count_qty_multiple'];
										$digi_count = $get_config['digital_count_qty'];
										$qty_expect = $get_config['quantity'];
										if($qty_expect > 0 && $digi_count !== '' && $digi_count !== NULL) {
											$variancer = $digi_count - $qty_expect;
										} else {
											$variancer = $digi_count + $qty_expect;
										}
										$var=explode(',',$multiple_qty_counter);
										$total = count($var);
										if($total > 0) {
											$new_string = '';
											foreach($var as $qty) {
												if($i > 1) {

													$arr = explode("#$#", $qty);
													$first = $arr[0];
													$location = $arr[1];
													$other_inputs .= '
													<div class="additional_position" id="addthese_'.$row['inventoryid'].'_'.$i.'">
													   <input class="act_inv" value="'.$first.'" id="actinv_'.$row['inventoryid'].'_'.$i.'" type="text" name="actualinventory[]">
													   <input class="" value="'.$location.'" id="location_'.$row['inventoryid'].'_'.$i.'" type="hidden">
													   <div class="buttons_box">
														   <span class="">
															<img class="delete_this cursor_pointer " src="../img/icons/minus-1.png" id="deletethis_'.$row['inventoryid'].'_'.$i.'" width="25px" style="padding:2px">
														   </span>
														   <img class="edit_this wiggle-me cursor_pointer" src="../img/icons/map-location.png" id="editthis_'.$row['inventoryid'].'_'.$i.'" width="25px" style="padding:2px">
													   </div>
												   </div>
													';
												} else {
													$first_location = '';
													$arr = explode("#$#", $qty);
													if(isset($arr[0])) {
														$first_qty = $arr[0];
														if(isset($arr[1])) {
															$first_location = $arr[1];
														} else {
															$first_location = '';
														}
													} else {
														$first_qty = '';
														$first_location = '';
													}
												}
											   $i++;
											}
										}

									   echo '<div class="additional_position" id="addthese_'.$row['inventoryid'].'">
										   <input class="act_inv" value="'.$first_qty.'" id="actinv_'.$row['inventoryid'].'" type="text" name="actualinventory[]">
										   <input class="" value="'.$first_location.'" id="location_'.$row['inventoryid'].'_1" type="hidden">
										   <div class="buttons_box">
											   <span class="hide_this_object">
												<img class="delete_this cursor_pointer " src="../img/icons/minus-1.png" id="deletethis_'.$row['inventoryid'].'" width="25px" style="padding:2px">
											   </span>
											   <img class="edit_this wiggle-me cursor_pointer" src="../img/icons/map-location.png" id="editthis_'.$row['inventoryid'].'" width="25px" style="padding:2px">
										   </div>
									   </div>';

									   echo '<span id="add_here_new_position_'.$row['inventoryid'].'">'.$other_inputs;
									   $total_inpz = $i-1;
									   if($total_inpz == 0) {
										   $total_inpz = 1;
									   }
									   echo '</span>
									   <center><img class="add_new_input cursor_pointer" id="addnew_'.$row['inventoryid'].'" src="../img/icons/add-1.png" width="25px" style="padding:2px"></center>
									   <span style="display:none;" id="totalofinputs_'.$total_inpz.'" class="totalofinputs_'.$row['inventoryid'].'"></span>
									   <span style="display:none;" class="quantity_'.$row['inventoryid'].'">'.$row['quantity'] . '</span>
									   </td>';
										if($variancer > 0) {
											$style = 'style="background-color:rgba(37,178,37,.75);"';
										} else if($variancer < 0) {
											$style = 'style="background-color:rgba(255, 70, 70, 1);"';
										} else {
											$style = 'style="background-color:inherit;"';
										}
				                       echo '<td data-title="Variance" '.$style.'><span style="display:block;" class="variance_'.$row['inventoryid'].'">'.$variancer.'</span></td>';


				            	echo "</tr>";
				            }

				            echo '</table></div>';


				            if($client_name == '') {
				               // echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
				            }

				            ?>


				        </form>
					    </div>
				    </div>
			    </div>
			</div>
		</div>
	</div>
</div>
        <!-- <center><a href="#">Back to Top</a></center> -->

<?php include ('../footer.php');?>