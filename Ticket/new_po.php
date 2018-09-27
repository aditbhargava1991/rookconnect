<?php
/*
Field Purhase Order
*/
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['purchase_order'])) {
	$type = $_POST['type'];
	$vendorid = $_POST['vendorid'];
    $siteid = filter_var($_POST['siteid'],FILTER_SANITIZE_STRING);
	if(!is_numeric($siteid) && $siteid != '') {
		$businessid = 0;
		$tile_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` LIKE '%_tabs' AND `value` LIKE '%Sites%' ORDER BY `name` LIKE 'contacts3%' DESC, `name` LIKE 'contacts%' DESC, `name` LIKE 'clientinfo%' DESC, `name` LIKE 'contactsrolodex%' DESC"));
		$insert_tile = explode('_', $tile_name['name'])[0];
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `businessid`, `site_name`) VALUES ('$insert_tile', 'Sites', '$businessid', '$siteid')");
		$siteid = mysqli_insert_id($dbc);
	} else {
		$businessid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `businessid` FROM `contacts` WHERE `contactid`='$siteid'"))['businessid'];
	}
	$contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
	if(!is_numeric($contactid) && $contactid != '') {
		$tile_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` LIKE '%_tabs' AND `value` LIKE '%Customers%' ORDER BY `name` LIKE 'contacts3%' DESC, `name` LIKE 'contacts%' DESC, `name` LIKE 'clientinfo%' DESC, `name` LIKE 'contactsrolodex%' DESC"));
		$insert_tile = explode('_', $tile_name['name'])[0];
		$first_name = explode(' ', $contactid)[0];
		$last_name = encryptIt(trim(str_replace($first_name, '', $contactid)));
		$first_name = encryptIt(trim($first_name));
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `businessid`, `siteid`, `first_name`, `last_name`) VALUES ('$insert_tile', 'Customers', '$businessid', '$siteid', '$first_name', '$last_name')");
		$contactid = mysqli_insert_id($dbc);
	}
	$bill_to = $_POST['bill_to'];
    $issue_date = $_POST['issue_date'];
    $revision = $_POST['revision'];
    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);

	$qty = implode('#*#',$_POST['qty']);
	$descript = implode('#*#',$_POST['descript']);
	$grade = implode('#*#',$_POST['grade']);
	$tag = implode('#*#',$_POST['tag']);
	$detail = implode('#*#',$_POST['detail']);
	$unit_price = implode('#*#',$_POST['unit_price']);
	$unit_total = implode('#*#',$_POST['unit_total']);

    $total_price = filter_var($_POST['total_price'],FILTER_SANITIZE_STRING);
	$tax = filter_var($_POST['tax'],FILTER_SANITIZE_STRING);
    $final_total = filter_var($_POST['total_cost'],FILTER_SANITIZE_STRING);
    $created_by = get_contact($dbc, $_SESSION['contactid']);

	if(empty($_POST['poid'])) {
		$history = "Purchase Order created by ".$created_by." at ".date('Y-m-d h:i:s');
		$query_insert_po = "INSERT INTO `workorder_purchase_orders` (`type`, `bill_to`, `vendorid`, `issue_date`, `description`, `qty`, `descript`, `grade`, `tag`, `detail`, `unit_price`, `unit_total`, `total_price`, `tax`, `final_total`, `history`) VALUES ('$type', '$bill_to', '$vendorid', '$issue_date', '$description', '$qty', '$descript', '$grade', '$tag', '$detail', '$unit_price', '$unit_total', '$total_price', '$tax', '$final_total', '$history')";
	    $result_insert_po = mysqli_query($dbc, $query_insert_po);
        $poid = mysqli_insert_id($dbc);
        $url = 'Added';
	} else {
		$history = "Purchase Order updated by ".$created_by." at ".date('Y-m-d h:i:s');
		$poid = $_POST['poid'];
		$query_update_site = "UPDATE `workorder_purchase_orders` SET `type` = '$type', `bill_to` = '$bill_to', `issue_date` = '$issue_date', `description`= '$description', `qty` = '$qty', `vendorid` = '$vendorid', `descript` = '$descript', `grade` = '$grade', `tag` = '$tag', `detail` = '$detail', `unit_price` = '$unit_price', `unit_total` = '$unit_total', `description` = '$description', `total_price` = '$total_price', `tax` = '$tax', `final_total`='$final_total', `revision` = '$revision', `history`=CONCAT(IFNULL(CONCAT(`history`,'<br />\n'),''),'$history') WHERE `poid` = '$poid'";
		$result_update_site	= mysqli_query($dbc, $query_update_site);
        $url = 'Updated';
	}

    $vendname = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid='$vendorid'"));
    $vend_name = decryptIt($vendname['name']);
    $contact = decryptIt($vendname['first_name']).' '.decryptIt($vendname['last_name']);
    $cell_phone = decryptIt($vendname['cell_phone']);

	$total_count = count($_POST['qty']);
    $po_html = '';
	for($emp_loop=0; $emp_loop<=$total_count; $emp_loop++) {
		if($_POST['qty'][$emp_loop] != '') {
			$po_html .= '<tr>
            <td style="border-right: 1px solid grey; border-top:1px solid grey;">' . ($emp_loop+1).'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey;">' . $_POST['qty'][$emp_loop].'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey;">' . $_POST['descript'][$emp_loop] .'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey; ">'. $_POST['tag'][$emp_loop].'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey; ">'. $_POST['detail'][$emp_loop].'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey; ">$'. $_POST['unit_price'][$emp_loop].'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey; ">$'. $_POST['unit_total'][$emp_loop].'</td>
            </tr>';
		}
	}

    DEFINE('PO_LOGO', get_config($dbc, 'site_work_po_logo'));
	DEFINE('PO_HEADER_TEXT', html_entity_decode(get_config($dbc, 'site_work_po_address')));

	class MYPDF extends TCPDF {

		public function Header() {
			$image_file = 'download/'.PO_LOGO;
			$this->SetFont('helvetica', '', 15);
			$footer_text = 'WORK ORDER';
			$this->writeHTMLCell(0, 0, -140, 10, $footer_text, 0, 0, false, "L", true);
			$this->SetFont('helvetica', '', 9);
			$footer_text = '<br><strong>Do not pay from this work order<br>Date Work Performed:</strong> ' .date('Y-m-d').'<br>';
			$this->writeHTMLCell(0, 0, 10, 20, $footer_text, 0, 0, false, "L", true);
			$this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
            $footer_text = '<p style="text-align:right;">'.PO_HEADER_TEXT.'</p>';
			$this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', '', 9);
			$footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
		}
	}

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 9);
    $pdf->setCellHeightRatio(1.2);
	$html = '<table border="1">
				<tr>
                    <td rowspan="4"><img src="download/'.PO_LOGO.'" width="482" height="190" border="0" alt=""></td>
                    <td>Purchase Order</td>
                    <td>Effective Date</td>
                    <td>Procedure ID</td>
                </tr>
                <tr>
                    <td>'.$po_number.'</td>
                    <td>'.$issue_date.'</td>
                    <td></td>
                </tr>
                <tr>
                    <td rowspan="2">Vendor: '.$vend_name.'</td>
                    <td>Contact: '.$contact.'</td>
                    <td>Revision: '.$revision.'</td>
                </tr>
                <tr>
                    <td>Phone: '.$cell_phone.'
                    <td>0 Controlled</td>
                </tr>
            </table>';

    $pdf->setCellHeightRatio(0.3);
	$html .= '<p style="text-align:right;">'.PO_HEADER_TEXT.'<br><br></p>';
    $pdf->setCellHeightRatio(1.2);
	$html .= '<br><br>General Details: '.html_entity_decode($description).'<br><br><br>';

    if($po_html != '') {
			$html .='
			<table  style="text-align:left; border:1px solid black;">
				<tr>
                <th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">ITEM</th>
                <th style="border-right: 1px solid grey; text-align:center; width:10%;font-weight:bold;">QTY</th>
                <th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">ITEM DETAIL</th>
                <th style="border-right: 1px solid grey; text-align:center; width:10%;font-weight:bold;">TAG</th>
                <th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">DETAIL</th>
                <th style="border-right: 1px solid grey; text-align:center; width:10%;font-weight:bold;">EACH</th>
                <th style="border-right: 1px solid grey; text-align:center; width:10%;font-weight:bold;">Line Total</th>
                </tr>';

                $html .= $po_html;

			    $html .='
                <tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="6">&nbsp;</td><td style="border-top:1px solid grey;font-weight:bold;">&nbsp;</td></tr>
                <tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="6">Sub Total</td><td style="border-top:1px solid grey;font-weight:bold;">$'.$_POST['cost'].'</td></tr>
                <tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="6">Sales Tax</td><td style="border-top:1px solid grey;font-weight:bold;">'.'5%'.'</td></tr>
                <tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="6">Total</td><td style="border-top:1px solid grey;font-weight:bold;">$'.$_POST['total_cost'].'</td></tr>

			</table><br /><br />';

            $html .= 'Ordered By: '.$created_by;
    }

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('download/ticket_po_'.$poid.'.pdf', 'F');

    echo '<script> if(window.self === window.top) {
		window.location.reload("workorder.php");
	} else {
		window.top.new_po_added("'.$poid.'", "'.$final_total.'");
	} </script>';

    mysqli_close($dbc);//Close the DB Connection

}
$edit_config = ',billable,type,vendor,date,item_qty,item_desc,item_grade,item_tag,item_detail,item_price,item_cost,description,cost,tax,total,';
?>
<script type="text/javascript">

$(document).ready(function() {
    site_select('<?= $siteid ?>');

    $("#form1").submit(function( event ) {
		var cost = $("input[name=cost]").val();
		var total_cost = $("input[name=total_cost]").val();

		if(cost == '0.00') {
			alert("Please add Cost.");
			return false;
		}
		if(total_cost == '0.00' || total_cost == '0' || total_cost == '' ) {
			alert("Please add Total Cost.");
			return false;
		}
    });

    $("#cost").keyup(function() {
		var cost = parseFloat($('#cost').val());
		var tax = parseFloat($('#tax').val());
		var tax = parseFloat(cost*tax)/100;
        var cost_tax = parseFloat(cost+tax);
		document.getElementById('total_cost').value = round2Fixed(cost_tax);
    });

	var inc_material = 2;
    $('.hide_additional').hide();
    $('#add_new_row').on( 'click', function () {
		if ($('.hide_additional').is(":hidden")) {
			$('.hide_additional').show();
		} else {
			var clone = $('.additional_row').clone();
			clone.find('.form-control').val('');
			clone.find('#qty_1').attr('id', 'qty_'+inc_material);
			clone.find('#up_1').attr('id', 'up_'+inc_material);
			clone.find('#amount_1').attr('id', 'amount_'+inc_material);
			clone.removeClass("additional_row");
			$('#add_here_new_data').append(clone);
			$('[name="qty[]"]').last().focus();
			initSelectOnChanges();
		}

		if(window.self !== window.top) {
			$(window.top.document).find('#iframe_new_po').height('calc('+document.body.scrollHeight+'px + 6em)');
		}
		inc_material++;
        return false;
    });

	if(window.self !== window.top) {
		setTimeout(function() {
			$(window.top.document).find('#iframe_new_po').height('calc('+document.body.scrollHeight+'px + 6em)');
		}, 500);
	}
});

	function multiplyCost(txb) {
        var get_id = txb.id;
        var split_id = get_id.split('_');
        var amount = parseFloat($('#qty_'+split_id[1]).val() * $('#up_'+split_id[1]).val());
        document.getElementById('amount_'+split_id[1]).value = round2Fixed(amount);
        materialStock();
	}

	function materialStock() {
        var sum = 0;
        $('input[name="unit_total[]"]').each(function(){
            if(!isNaN(this.value) && this.value.length!=0) {
                sum += parseFloat($(this).val());
            }
        });
        document.getElementById('cost').value = sum;
		var cost = parseFloat($('#cost').val());
		var tax = parseFloat($('#tax').val());
		var tax = parseFloat(cost*0.05);
        var cost_tax = parseFloat(cost+tax);
		document.getElementById('total_cost').value = round2Fixed(cost_tax);
	}

	function totalCost() {
		var cost = parseFloat($('#cost').val());
		var tax = parseFloat($('#tax').val());
		var tax = parseFloat(cost*0.05);
        var cost_tax = parseFloat(cost+tax);
		document.getElementById('total_cost').value = round2Fixed(cost_tax);
	}

	function numericFilter(txb) {
	   txb.value = txb.value.replace(/[^\0-9]/ig, "");
	}

    function site_select(id) {
        if(id != '') {
            $.ajax({
                data: { business: $('select[name=siteid] option:selected').data('business'), site: id, contact: $('[name=contactid]').data('value') },
                method: 'POST',
                url: 'site_work_orders_ajax.php?fill=siteid',
                success: function(result) {
                    $('[name=contactid]').empty().html(result).trigger('change.select2');
                    var location = $('[name=siteid] option:selected').data('name');
                    if($('[name=site_location]').val() == '') {
                        $('[name=site_location]').val(location);
                    }
                    var description = $('[name=siteid] option:selected').data('location');
                    if($('[name=site_description]').val() == '') {
                        $('[name=site_description]').val(description);
                    }
                    var google = $('[name=siteid] option:selected').data('google');
                    if($('[name=google_map_link]').val() == '') {
                        $('[name=google_map_link]').val(google);
                    }
                    $('[name=businessid]').val($('[name=siteid] option:selected').data('business'));
                    set_new_who();
                }
            });
        }
        else {
            $('[name=contactid]').empty().html('<option>Please select a site first</option>').trigger('change.select2');
            set_new_who();
        }
    }

    function set_new_who() {
        var site = $('[name=siteid]').first().val();
        if(site == 'NEW') {
            $('[name=siteid]').last().show();
        } else {
            $('[name=siteid]').last().val('').hide();
        }
        var contact = $('[name=contactid]').first().val();
        if(contact == 'NEW') {
            $('[name=contactid]').last().show();
        } else {
            $('[name=contactid]').last().val('').hide();
        }
    }
</script>
</head>
<body>

<?php include_once ('../navigation.php');

?>

<div class="container">
	<div class="row">

    <h1 class="double-pad-bottom">Create Purchase Order</h1>
	<?php if(!IFRAME_PAGE) { ?>
		<div class="pad-left double-gap-bottom"><a href="site_work_orders.php?tab=po" class="btn brand-btn">Back to Dashboard</a></div>
	<?php } ?>

    <form id="form1" action="" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
	<?php

		$type = '';
		$bill_to = 'Billable';
		$vendorid = '';
		$description = '';
		$cost = '';
        $qty = '';
        $desc = '';
        $grade = '';
        $tag = '';
        $detail = '';
        $price_per_unit = '';
        $each_cost = '';
        $issue_date = '';
        $revision = 0;

        $cost = '';
        $tax = '';
        $total_cost = '';

		if(!empty($_GET['poid'])) {

			$poid = $_GET['poid'];
			$get_po = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `workorder_purchase_orders` WHERE `poid`='$poid'"));

			$po_number = $get_po['po_number'];
			$type = $get_po['type'];
			$tax = $get_po['tax'];
			$bill_to = $get_po['bill_to'];
			$vendorid = $get_po['vendorid'];
            $description = $get_po['description'];

            $qty = $get_po['qty'];
            $desc = $get_po['descript'];
            $grade = $get_po['grade'];
            $tag = $get_po['tag'];
            $detail = $get_po['detail'];
            $unit_price = $get_po['unit_price'];
            $unit_total = $get_po['unit_total'];
            $issue_date = $get_po['issue_date'];

            $total_price = $get_po['total_price'];
            $tax = $get_po['tax'];
            $final_total = $get_po['final_total'];
            $revision = $get_po['revision']+1;

		?>
		<input type="hidden" id="poid"	name="poid" value="<?php echo $poid ?>" />
		<?php } else if(!empty($_GET['workorderid'])) { ?>
			<input type="hidden" name="workorderid" value="<?= $_GET['workorderid'] ?>">
		<?php } ?>
		<input type="hidden" id="revision"	name="revision" value="<?php echo $revision ?>" />


		<?php if(strpos($edit_config,',billable,') !== false): ?>
		  <div class="form-group">
			<label for="file[]" class="col-sm-4 control-label">Billable/Non:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="radio" <?php if ($bill_to == "Billable") { echo " checked"; } ?> name="bill_to" value="Billable">Billable</label>
				<label class="form-checkbox"><input type="radio" <?php if ($bill_to == "Non-billable") { echo " checked"; } ?> name="bill_to" value="Non-billable">Non-billable</label>
			</div>
		  </div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',type,') !== false): ?>
		  <div class="form-group">
			<label for="file[]" class="col-sm-4 control-label">Tab:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="radio" <?php if ($type == "3rd Party") { echo " checked readonly"; } if ($type !== '') { echo "readonly";} if ($type == '') { echo "checked";} ?> name="type" value="3rd Party">3rd Party</label>
				<label class="form-checkbox"><input type="radio" <?php if ($type == "Other") { echo " checked readonly"; } if ($type !== '') { echo "readonly";} ?> name="type" value="Other">Other</label>
			</div>
		  </div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',vendor,') !== false): ?>
		  <div class="form-group vendor">
			<label for="fax_number"	class="col-sm-4	control-label">Vendor:</label>
			<div class="col-sm-8">
				<select <?php if ($type !== '') { echo "readonly";} ?>  data-placeholder="Choose a Vendor..." id="vendorid" name="vendorid" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Vendor' AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
					foreach($query as $id) {
						$selected = '';
						$selected = $vendorid == $id ? 'selected = "selected"' : '';
						echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
					}
				  ?>
				</select>
			</div>
		  </div>
		<?php endif; ?>

            <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label">Site:</label>
                <div class="col-sm-8">
                    <select name="siteid" data-value="<?= $siteid ?>" id="po_sideid" class="form-control chosen-select-deselect">
                        <option></option><?php
                        $site_list = mysqli_query($dbc, "SELECT `contactid`, `site_name`, `google_maps_address`, `lsd`, `businessid` FROM `contacts` WHERE `category`='Sites' AND `deleted`=0 AND `status`>0 ORDER BY `site_name`");
                        while($site_row = mysqli_fetch_array($site_list)) {
                            echo "<option data-business='".$site_row['businessid']."' data-name='".$site_row['site_name']."' data-google='".$site_row['google_maps_address']."' data-location='".$site_row['lsd']."' ".($site_row['contactid'] == $siteid ? 'selected' : '')." value='".$site_row['contactid']."'>".$site_row['site_name']."</option>";
                        } ?>
                    </select>
                    <input disabled type="text" name="siteid" value="" style="dispaly:none;" class="form-control" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">Contact:</label>
                <div class="col-sm-8">
                    <select name="contactid" id="po_contactid" data-value="<?= $contactid ?>" class="form-control chosen-select-deselect"></select>
                    <input disabled type="text" name="contactid" value="" style="dispaly:none;" class="form-control" />
                </div>
            </div>

		<?php if(strpos($edit_config,',date,') !== false): ?>
            <div class="form-group vendor">
                <label for="first_name" class="col-sm-4 control-label text-right">Date:</label>
                <div class="col-sm-8">
                    <input name="issue_date" type="text" value="<?php echo $issue_date;?>" class="datepicker"></p>
                </div>
            </div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',item_') !== false): ?>
			<div class="form-group clearfix hide-titles-mob">
				<?php if(strpos($edit_config,',item_qty,') !== false): ?>
					<label class="col-sm-1 text-center">Qty</label>
				<?php endif; ?>
				<?php if(strpos($edit_config,',item_desc,') !== false): ?>
					<label class="col-sm-2 text-center">Desc</label>
				<?php endif; ?>
				<?php if(strpos($edit_config,',item_grade,') !== false): ?>
					<label class="col-sm-2 text-center">Grade</label>
				<?php endif; ?>
				<?php if(strpos($edit_config,',item_tag,') !== false): ?>
					<label class="col-sm-1 text-center">Tag</label>
				<?php endif; ?>
				<?php if(strpos($edit_config,',item_detail,') !== false): ?>
					<label class="col-sm-2 text-center">Detail</label>
				<?php endif; ?>
				<?php if(strpos($edit_config,',item_price,') !== false): ?>
					<label class="col-sm-2 text-center">Price per unit($)</label>
				<?php endif; ?>
				<?php if(strpos($edit_config,',item_cost,') !== false): ?>
					<label class="col-sm-2 text-center">Cost($)</label>
				<?php endif; ?>
			</div>

			<?php
			if(empty($_GET['poid'])) {
				?>
			  <div class="additional_row">
				<div class="clearfix"></div>
				<div class="form-group clearfix">
					<?php if(strpos($edit_config,',item_qty,') !== false): ?>
						<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
							<input name="qty[]" id="qty_1" type="text" maxlength="20" class="form-control qty" onKeyUp="numericFilter(this); multiplyCost(this);">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_desc,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Description:</label>
							<input name="descript[]" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_grade,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Grade:</label>
							<input name="grade[]" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_tag,') !== false): ?>
						<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Tag:</label>
							<input name="tag[]" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_detail,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Detail:</label>
							<input name="detail[]" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_price,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Price Per Unit ($):</label>
							<input name="unit_price[]" id="up_1" type="text" class="form-control up" onKeyUp="multiplyCost(this);">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_cost,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Cost ($):</label>
							<input name="unit_total[]" id="amount_1" type="text" maxlength="20" class="form-control amount">
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div id="add_here_new_data"></div>
			<button id="add_new_row" class="btn brand-btn">Add Another Item</button>

			<?php
			} else {

			$qut = explode('#*#',$qty);
			$desc = explode('#*#',$desc);
			$grade = explode('#*#',$grade);
			$tag = explode('#*#',$tag);
			$detail = explode('#*#',$detail);
			$price_per_unit = explode('#*#',$unit_price);
			$each_cost = explode('#*#',$unit_total);
			$total_count = mb_substr_count($qty,'#*#');
			$no_ratecard = 0;
			$no_rate_position = '';
			for($emp_loop=0; $emp_loop<=$total_count; $emp_loop++) {
				if(($qut[$emp_loop]) != '') {
				$qt = '';
				$de = '';
				$gr = '';
				$tg = '';
				$dt = '';
				$ppu = '';
				$ec = '';
				$style = '';

				if(isset($qut[$emp_loop])) {
					$qt = $qut[$emp_loop];
				}
				if(isset($desc[$emp_loop])) {
					$de = $desc[$emp_loop];
				}
				if(isset($grade[$emp_loop])) {
					$gr = $grade[$emp_loop];
				}
				if(isset($tag[$emp_loop])) {
					$tg = $tag[$emp_loop];
				}
				if(isset($detail[$emp_loop])) {
					$dt = $detail[$emp_loop];
				}
				if(isset($price_per_unit[$emp_loop])) {
					$ppu = $price_per_unit[$emp_loop];
				}
				if(isset($each_cost[$emp_loop])) {
					$ec = $each_cost[$emp_loop];
				}

			?>
				<div class="form-group clearfix">
					<?php if(strpos($edit_config,',item_qty,') !== false): ?>
						<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
							<input name="qty[]" id="qty_<?php echo $emp_loop;?>" type="text" maxlength="20" class="form-control qty" value="<?php echo  $qt; ?>" onKeyUp="numericFilter(this); multiplyCost(this);">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_desc,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Description:</label>
							<input name="descript[]" value="<?php echo  $de; ?>" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_grade,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Grade:</label>
							<input name="grade[]" value="<?php echo  $gr; ?>" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_tag,') !== false): ?>
						<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Tag:</label>
							<input name="tag[]" value="<?php echo  $tg; ?>" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_detail,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Detail:</label>
							<input name="detail[]" value="<?php echo  $dt; ?>" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_price,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Price Per Unit ($):</label>
							<input name="unit_price[]" value="<?php echo  $ppu; ?>" id="up_<?php echo $emp_loop;?>" type="text" class="form-control up" onKeyUp="multiplyCost(this);">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_cost,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Cost ($):</label>
							<input name="unit_total[]" value="<?php echo  $ec; ?>" id="amount_<?php echo $emp_loop;?>" type="text" maxlength="20" class="form-control amount">
						</div>
					<?php endif; ?>

			   </div>

		<?php } } ?>

			<div class="additional_row">
				<div class="clearfix"></div>
				<div class="form-group clearfix">
					<?php if(strpos($edit_config,',item_qty,') !== false): ?>
						<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
							<input name="qty[]" id="qty_<?php echo ($emp_loop+1);?>" type="text" maxlength="20" class="form-control qty" onKeyUp="numericFilter(this); multiplyCost(this);">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_desc,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Description:</label>
							<input name="descript[]" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_grade,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Grade:</label>
							<input name="grade[]" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_tag,') !== false): ?>
						<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Tag:</label>
							<input name="tag[]" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_detail,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Detail:</label>
							<input name="detail[]" type="text" class="form-control">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_price,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Price Per Unit ($):</label>
							<input name="unit_price[]" id="up_<?php echo ($emp_loop+1);?>" type="text" class="form-control up" onKeyUp="multiplyCost(this);">
						</div>
					<?php endif; ?>
					<?php if(strpos($edit_config,',item_cost,') !== false): ?>
						<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Cost ($):</label>
							<input name="unit_total[]" id="amount_<?php echo ($emp_loop+1);?>" type="text" maxlength="20" class="form-control amount">
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div id="add_here_new_data"></div>
			<!-- <button id="add_new_row" class="btn brand-btn">Add</button> -->

						<?php }

						?>
	<?php endif; ?>
	<?php if(strpos($edit_config,',description,') !== false): ?>
		<div class="form-group">
			<label for="additional_note" class="col-sm-4 control-label">Description:</label>
			<div class="col-sm-12">
				<textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
			</div>
		</div>
	<?php endif; ?>

	<?php if(strpos($edit_config,',cost,') !== false): ?>
		<div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Cost<span class="text-red">*</span>:</label>
			<div class="col-sm-8">
			  <input name="total_price" type="text" id="cost" value="<?php echo $total_price; ?>" class="form-control">
			</div>
		</div>
	<?php endif; ?>

	<?php if(strpos($edit_config,',tax,') !== false): ?>
		<div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Sales Tax %:</label>
			<div class="col-sm-8">
			  <input name="tax" type="text" id="tax" value = "<?php if($tax == '') { echo "5"; } else { echo $tax;}?>" onKeyUp="totalCost();" class="form-control">
			</div>
		</div>
	<?php endif; ?>

	<?php if(strpos($edit_config,',total,') !== false): ?>
		<div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Total Cost<span class="text-red">*</span>:</label>
			<div class="col-sm-8">
			  <input name="total_cost" id="total_cost" value = "<?php if($final_total !== '') { echo $final_total; }?>" type="text" class="form-control">
			</div>
		</div>
	<?php endif; ?>

    <div class="form-group">
        <div class="col-sm-4 clearfix">
			<a href="workorder.php" class="btn brand-btn pull-right">Back</a>
        </div>
        <div class="col-sm-8">
                <input type="submit" name="purchase_order" value="Issue PO" class="btn brand-btn btn-lg pull-right" style="margin-right:20px"/>
        </div>
    </div>

    </form>
    </div>
</div>

<?php include_once('../footer.php'); ?>
