<?php
/*
Field Purhase Order
*/
include ('../include.php');
checkAuthorised('field_job');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['purchase_order'])) {
	$jobid = $_POST['jobid'];
	$po_number = $_POST['po_number'];
	$type = ( !empty($_POST['type']) ) ? $_POST['type'] : '';
	$bill_to = ( !empty($_POST['bill_to']) ) ? $_POST['bill_to'] : '';
	$third_invoice_no = $_POST['third_invoice_no'];
	$vendorid = $_POST['vendorid'];
    $issue_date = $_POST['issue_date'];
    $revision = $_POST['revision'];
    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);

	$qty = implode('#*#',$_POST['qty']);
	$desc = filter_var(implode('#*#',$_POST['desc']),FILTER_SANITIZE_STRING);
	$grade = filter_var(implode('#*#',$_POST['grade']),FILTER_SANITIZE_STRING);
	$tag = filter_var(implode('#*#',$_POST['tag']),FILTER_SANITIZE_STRING);
	$detail = filter_var(implode('#*#',$_POST['detail']),FILTER_SANITIZE_STRING);
	$price_per_unit = implode('#*#',$_POST['price_per_unit']);
	$each_cost = implode('#*#',$_POST['each_cost']);

    $cost = filter_var($_POST['cost'],FILTER_SANITIZE_STRING);
	$mark_up = filter_var($_POST['mark_up'],FILTER_SANITIZE_STRING);
	$total_cost = filter_var($_POST['total_cost'],FILTER_SANITIZE_STRING);
    $created_by = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

	if(empty($_POST['fieldpoid'])) {
		$query_insert_po = "INSERT INTO `field_po` (`jobid`, `po_number`, `type`, `bill_to`, `vendorid`, `third_invoice_no`, `issue_date`, `description`, `qty`, `desc`, `grade`, `tag`, `detail`, `price_per_unit`, `each_cost`, `cost`, `mark_up`, `total_cost`, `created_by`) VALUES ('$jobid', '$po_number', '$type', '$bill_to', '$vendorid', '$third_invoice_no', '$issue_date', '$description', '$qty', '$desc', '$grade', '$tag', '$detail', '$price_per_unit', '$each_cost', '$cost', '$mark_up', '$total_cost', '$created_by')";
	    $result_insert_po = mysqli_query($dbc, $query_insert_po);
        $fieldpoid = mysqli_insert_id($dbc);
        $url = 'Added';
			$before_change = '';
	    $history = "field_po entry has been added. <br />";
	    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
	} else {
		$fieldpoid = $_POST['fieldpoid'];
		$query_update_site = "UPDATE `field_po` SET `po_number`='$po_number', `type`='$type', `bill_to`='$bill_to', `issue_date`='$issue_date', `description`='$description', `qty`='$qty', `vendorid`='$vendorid', `third_invoice_no`='$third_invoice_no', `desc`='$desc', `grade`='$grade', `tag`='$tag', `detail`='$detail', `price_per_unit`='$price_per_unit', `each_cost`='$each_cost', `description`='$description', `cost`='$cost', `mark_up`='$mark_up', `total_cost`='$total_cost', `revision`='$revision', `edited_by`='$created_by' WHERE `fieldpoid`='$fieldpoid'";
		$result_update_site	= mysqli_query($dbc, $query_update_site);
        $url = 'Updated';
		$before_change = '';
    $history = "field_po entry has been updated for fieldpoid -> $fieldpoid. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
	}
	$invoice_list = '';
	foreach($_POST['vendor_invoice'] as $invoice) {
		$invoice_list .= '##FFM##'.$invoice;
	}
	foreach($_FILES['vendor_upload']['name'] as $i => $file) {
		if(!file_exists('download/field_invoice')) {
			mkdir('download/field_invoice',0777,true);
		}
		$file = htmlspecialchars($file, ENT_QUOTES);
		move_uploaded_file($_FILES['vendor_upload']['tmp_name'][$i],'download/field_invoice/'.$file);
		$invoice_list .= '##FFM##'.$file;
	}

	$before_change = capture_before_change($dbc, 'field_po', 'vendor_invoice', 'fieldpoid', $fieldpoid);

	mysqli_query($dbc, "UPDATE `field_po` SET `vendor_invoice`='$invoice_list' WHERE `fieldpoid`='$fieldpoid'");

	$history = capture_after_change('vendor_invoice', $invoice_list);
	add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

    $vendname = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$vendorid'"));
    $vend_name = decryptIt($vendname['name']);
    $contact = decryptIt($vendname['first_name']).' '.decryptIt($vendname['last_name']);
    $cell_phone = decryptIt($vendname['cell_phone']);

	$total_count = count($_POST['qty']);
    $po_html = '';
    $emp_loop=0;

    for($emp_loop=0; $emp_loop<=$total_count; $emp_loop++) {
		if( !empty($_POST['qty'][$emp_loop]) ) {
			$po_html .= '<tr>
            <td style="border-right: 1px solid grey; border-top:1px solid grey;">' . ($emp_loop+1).'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey;">' . $_POST['qty'][$emp_loop].'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey;">' . $_POST['desc'][$emp_loop] .'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey; ">'. $_POST['tag'][$emp_loop].'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey; ">'. $_POST['detail'][$emp_loop].'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey; ">$'. $_POST['price_per_unit'][$emp_loop].'</td>
            <td style="border-right: 1px solid grey; border-top:1px solid grey; ">$'. $_POST['each_cost'][$emp_loop].'</td>
            </tr>';
		}
	}

    DEFINE('PO_LOGO', get_config($dbc, 'field_jobs_po_logo'));
	DEFINE('PO_HEADER_TEXT', html_entity_decode(get_config($dbc, 'field_jobs_po_address')));

	class MYPDF extends TCPDF {

		public function Header() {
			$image_file = 'download/'.PO_LOGO;
			$this->SetFont('helvetica', '', 15);
			$footer_text = 'WORK TICKET';
			$this->writeHTMLCell(0, 0, -140, 10, $footer_text, 0, 0, false, "L", true);
			$this->SetFont('helvetica', '', 9);
			$footer_text = '<br><strong>Do not pay from this ticket<br>Date Work Performed:</strong> ' .date('Y-m-d').'<br>';
			$this->writeHTMLCell(0, 0, 10, 20, $footer_text, 0, 0, false, "L", true);
			$this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
            $footer_text = '<p style="text-align:right;">'.PO_HEADER_TEXT.'</p>';
			//$footer_text = '<p style="text-align:right;">Box 2052, Sundre, AB, T0M 1X0<br>Phone: 403-638-4030<br>Fax: 403-638-4001<br>Email: info@highlandprojects.com<br><br></p>';
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

	//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	//$pdf->setFooterData(array(0,64,0), array(0,64,128));

	//$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
	//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
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
                    <td rowspan="2">Vendor : '.$vend_name.'</td>
                    <td>Contact : '.$contact.'</td>
                    <td>Revision : '.$revision.'</td>
                </tr>
                <tr>
                    <td>Phone : '.$cell_phone.'
                    <td>0 Controlled</td>
                </tr>
            </table>';

    $pdf->setCellHeightRatio(0.3);
	$html .= '<p style="text-align:right;">'.PO_HEADER_TEXT.'<br><br></p>';
    $pdf->setCellHeightRatio(1.2);
	$html .= '<br><br>General Details : '.html_entity_decode($description).'<br><br><br>';

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

			    $html .='<tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="6">Ordered By : '.$created_by.'</td><td style="border-top:1px solid grey;font-weight:bold;">&nbsp;</td></tr>
                <tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="6">&nbsp;</td><td style="border-top:1px solid grey;font-weight:bold;">&nbsp;</td></tr>
                <tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="6">Sub Total</td><td style="border-top:1px solid grey;font-weight:bold;">$'.$_POST['cost'].'</td></tr>
                <tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="6">Sales Tax</td><td style="border-top:1px solid grey;font-weight:bold;">'.'5%'.'</td></tr>
                <tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="6">Total</td><td style="border-top:1px solid grey;font-weight:bold;">$'.$_POST['total_cost'].'</td></tr>

			</table>
			';
    }

	$pdf->writeHTML($html, true, false, true, false, '');

    $filename = $basename = "field_po_".$fieldpoid.".pdf";
    $j = 0;
    while(file_exists('download/'.$filename)) {
        $filename = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
    }
    if(file_exists('download/'.$basename)) {
        rename('download/'.$basename, 'download/'.$filename);
    }

	$pdf->Output('download/field_po_'.$fieldpoid.'.pdf', 'F');

    echo '<script type="text/javascript"> alert("Field PO Submitted."); window.location.replace("field_po.php"); </script>';

    //mysqli_close($dbc);//Close the DB Connection

}
$edit_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `field_list` FROM `field_config_field_jobs` WHERE `tab`='po'"));
$edit_config = $edit_result['field_list'];
if(str_replace(',','',$edit_config) == '') {
	$edit_config = ',job,po,billable,type,vendor,date,item_qty,item_desc,item_grade,item_tag,item_detail,item_price,item_cost,description,cost,tax,total,';
}
?>
<script type="text/javascript">

$(document).ready(function() {

    $("#form1").submit(function( event ) {
        var jobid = $("#jobid").val();

        if (jobid === null) {
        } else {
            var cost = $("input[name=cost]").val();
            var total_cost = $("input[name=total_cost]").val();

            if(cost == '0.00') {
                alert("Please fill up Cost.");
                return false;
            }
            if(total_cost == '0.00' || total_cost == '0' || total_cost == '' ) {
                alert("Please fill up Total Cost.");
                return false;
            }
        }

    });

    $("#cost").keyup(function() {
		var cost = parseFloat($('#cost').val());
		var mark_up = parseFloat($('#mark_up').val());
		var gst = parseFloat(cost*mark_up)/100;
        var cost_gst = parseFloat(cost+gst);
		document.getElementById('total_cost').value = round2Fixed(cost_gst);
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

		//$('div').attr('name', 'newName');
        clone.removeClass("additional_row");
        $('#add_here_new_data').append(clone);
      }
	  inc_material++;
        return false;
    });

});
$(document).on('change', 'select[name="jobid"]', function() { changeJob(this); });

    function changeJob(txb) {
        window.location = 'add_field_po.php?jobid='+txb.value;
    }

	function multiplyCost(txb) {
        var get_id = txb.id;
        var split_id = get_id.split('_');
		var qty = $('#qty_'+split_id[1]).val();
		if(qty == 0) {
			qty = 1;
		}
		var price = $('#up_'+split_id[1]).val();
		if(qty > 0 && price > 0) {
			var amount = parseFloat(qty * price);
			document.getElementById('amount_'+split_id[1]).value = round2Fixed(amount);
		}
        materialStock();
	}

	function materialStock() {
        var sum = 0;
        $('input[name="each_cost[]"]').each(function(){
            if(!isNaN(this.value) && this.value.length!=0) {
                sum += parseFloat($(this).val());
            }
        });
        document.getElementById('cost').value = sum;
		var cost = parseFloat($('#cost').val());
		var mark_up = parseFloat($('#mark_up').val());
		var gst = parseFloat(cost*0.05);
        var cost_gst = parseFloat(cost+gst);
		document.getElementById('total_cost').value = round2Fixed(cost_gst);
	}

	function totalCost() {
		var cost = parseFloat($('#cost').val());
		var mark_up = parseFloat($('#mark_up').val());
		var gst = parseFloat(cost*0.05);
        var cost_gst = parseFloat(cost+gst);
		document.getElementById('total_cost').value = round2Fixed(cost_gst);
	}

	function numericFilter(txb) {
	   txb.value = txb.value.replace(/[^\0-9]/ig, "");
	}
</script>
</head>
<body>

<?php include_once ('../navigation.php'); ?>

<div class="container">
	<div class="row">

        <h1 class="double-pad-bottom">Create Purchase Order</h1>
        <div class="pad-left double-gap-bottom"><a href="field_po.php" class="btn config-btn">Back to Dashboard</a></div>

        <form id="form1" action="" method="post" class="form-horizontal" role="form" enctype="multipart/form-data"><?php

            $jobid	= $_GET['jobid'];
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
            $mark_up = '';
            $total_cost = '';

            $get_job_number =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `job_number` FROM `field_jobs` WHERE `jobid`='$jobid'"));
			$po_number = '';
			if($jobid > 0) {
				$get_po =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `po_number` FROM `field_po` WHERE `jobid`='$jobid' AND `po_number` LIKE '%-%-%-%-%' ORDER BY `fieldpoid` DESC"));

				$pn = explode('-',$get_po['po_number']);

				$po = $pn[4]+1;
				$po_number = $get_job_number['job_number'].'-'.$po;

				if($po_number == -1) {
					$po_number = '';
				}
			}

            if(!empty($_GET['fieldpoid'])) {

                $fieldpoid = $_GET['fieldpoid'];
                $get_job =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	`field_po` WHERE `fieldpoid`='$fieldpoid'"));

                $jobid = $get_job['jobid'];
                $po_number = $get_job['po_number'];
                $type = $get_job['type'];
                $mark_up = $get_job['mark_up'];
                $bill_to = $get_job['bill_to'];
                $total_cost = $get_job['total_cost'];
                $vendorid = $get_job['vendorid'];
                $vendor_invoice = $get_job['vendor_invoice'];
                $third_invoice_no = $get_job['third_invoice_no'];
                $cost = $get_job['cost'];
                $description = $get_job['description'];

                $cost = $get_job['cost'];
                $qty = $get_job['qty'];
                $desc = $get_job['desc'];
                $grade = $get_job['grade'];
                $tag = $get_job['tag'];
                $detail = $get_job['detail'];
                $price_per_unit = $get_job['price_per_unit'];
                $each_cost = $get_job['each_cost'];
                $issue_date = $get_job['issue_date'];

                $cost = $get_job['cost'];
                $mark_up = $get_job['mark_up'];
                $total_cost = $get_job['total_cost'];
                $revision = $get_job['revision']+1; ?>

                <input type="hidden" id="fieldpoid"	name="fieldpoid" value="<?= $fieldpoid; ?>" /><?php
            } ?>

            <input type="hidden" id="jobid"	name="jobid" value="<?= $jobid; ?>" />
            <input type="hidden" id="revision" name="revision" value="<?= $revision; ?>" />

            <?php if(strpos($edit_config,',job,') !== false): ?>
                <?php if(empty($_GET['fieldpoid'])) { ?>
                    <div class="form-group">
                        <label for="site_name" class="col-sm-4 control-label">Job#:</label>
                        <div class="col-sm-8">
                            <select required id="jobid" data-placeholder="Choose a Job..." name="jobid" class="chosen-select-deselect form-control job_check" width="380">
                                <option value=""></option><?php
                                $query = mysqli_query($dbc,"SELECT `jobid`, `job_number` FROM `field_jobs` WHERE `deleted`=0");
                                while($row = mysqli_fetch_array($query)) {
                                    if ($jobid == $row['jobid']) {
                                        $selected = 'selected="selected"';
                                    } else {
                                        $selected = '';
                                    }
                                    echo "<option ".$selected." value='". $row['jobid']."'>".$row['job_number'].'</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                <?php } else { /* ?>
                    <div class="form-group">
                        <label for="site_name" class="col-sm-4 control-label">Job#:</label>
                        <div class="col-sm-8">
                            <?= $get_job_number['job_number'] ?>
                        </div>
                    </div>
				<?php */ } ?>
            <?php endif; ?>

            <?php if(strpos($edit_config,',po,') !== false): ?>
                <div class="form-group clearfix">
                    <label for="site_name" class="col-sm-4 control-label">PO#:</label>
                    <div class="col-sm-8"><input name="po_number" type="text" class="form-control" value="<?= $po_number; ?>"></div><!-- Quantity -->
                </div>
            <?php endif; ?>

            <?php if(strpos($edit_config,',billable,') !== false): ?>
                <div class="form-group">
                    <label for="file[]" class="col-sm-4 control-label">Billable/Non:</label>
                    <div class="col-sm-8">
                        <label class="pad-right"><input type="radio" <?php if ($bill_to == "Billable") { echo " checked"; } ?> name="bill_to" value="Billable">Billable</label>
                        <label class="pad-right"><input type="radio" <?php if ($bill_to == "Non-billable") { echo " checked"; } ?> name="bill_to" value="Non-billable">Non-billable</label>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(strpos($edit_config,',type,') !== false): ?>
                <div class="form-group">
                    <label for="file[]" class="col-sm-4 control-label">Type:</label>
                    <div class="col-sm-8">
                        <label class="pad-right"><input type="radio" <?php if ($type == "3rd Party") { echo " checked readonly"; } if ($type !== '') { echo "readonly";} if ($type == '') { echo "checked"; } ?> name="type" value="3rd Party">3rd Party</label>
                        <label class="pad-right"><input type="radio" <?php if ($type == "Other") { echo " checked readonly"; } if ($type !== '') { echo "readonly";} ?> name="type" value="Other">Other</label>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(strpos($edit_config,',vendor,') !== false): ?>
                <div class="form-group vendor">
                    <label for="fax_number"	class="col-sm-4	control-label">Vendor:</label>
                    <div class="col-sm-8">
                        <select <?php if ($type !== '') { echo "readonly";} ?>  data-placeholder="Choose a Vendor..." id="vendorid" name="vendorid" class="chosen-select-deselect form-control" width="380">
                            <option value=""></option><?php
                            $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Vendor' AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
                            foreach($query as $id) {
                                $selected = '';
                                $selected = $id == $vendorid ? 'selected = "selected"' : '';
                                echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
                            } ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(strpos($edit_config,',3rd party,') !== false): ?>
                <div class="form-group vendor">
                    <label for="fax_number"	class="col-sm-4	control-label">3rd Party Invoice #:</label>
                    <div class="col-sm-8">
						<input type="text" class="form-control" name="third_invoice_no" value="<?= $third_invoice_no ?>">
                    </div>
                </div>
			<?php else: ?>
				<input type="hidden" name="third_invoice_no" value="<?= $third_invoice_no ?>">
            <?php endif; ?>

            <?php if(strpos($edit_config,',invoice,') !== false): ?>
                <div class="form-group vendor">
                    <label for="fax_number"	class="col-sm-4	control-label">Upload Invoice:</label>
                    <div class="col-sm-8">
						<input accept="*" name="vendor_upload[]" type="file" data-filename-placement="inside" class="form-control" multiple>
						<?php $invoices = explode('##FFM##',$vendor_invoice);
						for($i = 1; $i < count($invoices); $i ++) { ?>
							<div class="form-group"><input type="hidden" name="vendor_invoice[]" value="<?= $invoices[$i] ?>"><?= file_exists('download/field_invoice/'.$invoices[$i]) ? '<a href="download/field_invoice/'.$invoices[$i].'" target="_blank">'.$invoices[$i].'</a>' : $invoices[$i] ?> | <span class="cursor-hand" onclick="$(this).closest('.form-group').remove();">Archive</span></div>
						<?php } ?>
                    </div>
                </div>
			<?php else:
				$invoices = explode('##FFM##',$vendor_invoice);
				for($i = 1; $i < count($invoices); $i ++) { ?>
					<input type="hidden" name="vendor_invoice[]" value="<?= $invoices[$i] ?>">
				<?php }
			endif; ?>

            <?php if(strpos($edit_config,',date,') !== false): ?>
                <div class="form-group vendor">
                    <label for="first_name" class="col-sm-4 control-label text-right">Date:</label>
                    <div class="col-sm-8"><input name="issue_date" type="text" value="<?php echo $issue_date;?>" class="datepicker"></div>
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
                </div><?php

                if(!empty($_GET['fieldpoid'])) {
                    $qut = explode('#*#',$qty);
                    $desc = explode('#*#',$desc);
                    $grade = explode('#*#',$grade);
                    $tag = explode('#*#',$tag);
                    $detail = explode('#*#',$detail);
                    $price_per_unit = explode('#*#',$price_per_unit);
                    $each_cost = explode('#*#',$each_cost);
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
                            } ?>

                            <div class="form-group clearfix">
                                <?php if(strpos($edit_config,',item_qty,') !== false): ?>
                                    <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
                                        <input name="qty[]" id="qty_<?= $emp_loop; ?>" type="text" maxlength="20" class="form-control qty" value="<?= $qt; ?>" onKeyUp="numericFilter(this); multiplyCost(this);">
                                    </div>
                                <?php endif; ?>
                                <?php if(strpos($edit_config,',item_desc,') !== false): ?>
                                    <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Description:</label>
                                        <input name="desc[]" value="<?= $de; ?>" type="text" class="form-control">
                                    </div>
                                <?php endif; ?>
                                <?php if(strpos($edit_config,',item_grade,') !== false): ?>
                                    <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Grade:</label>
                                        <input name="grade[]" value="<?= $gr; ?>" type="text" class="form-control">
                                    </div>
                                <?php endif; ?>
                                <?php if(strpos($edit_config,',item_tag,') !== false): ?>
                                    <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Tag:</label>
                                        <input name="tag[]" value="<?= $tg; ?>" type="text" class="form-control">
                                    </div>
                                <?php endif; ?>
                                <?php if(strpos($edit_config,',item_detail,') !== false): ?>
                                    <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Detail:</label>
                                        <input name="detail[]" value="<?= $dt; ?>" type="text" class="form-control">
                                    </div>
                                <?php endif; ?>
                                <?php if(strpos($edit_config,',item_price,') !== false): ?>
                                    <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Price Per Unit ($):</label>
                                        <input name="price_per_unit[]" value="<?= $ppu; ?>" id="up_<?= $emp_loop; ?>" type="text" class="form-control up" onKeyUp="multiplyCost(this);">
                                    </div>
                                <?php endif; ?>
                                <?php if(strpos($edit_config,',item_cost,') !== false): ?>
                                    <div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Cost ($):</label>
                                        <input name="each_cost[]" value="<?= $ec; ?>" id="amount_<?= $emp_loop; ?>" type="text" maxlength="20" onchange="materialStock();" class="form-control amount">
                                    </div>
                                <?php endif; ?>
                            </div><?php
                        }
                    }
				} ?>

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
								<input name="desc[]" type="text" class="form-control">
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
								<input name="price_per_unit[]" id="up_<?php echo ($emp_loop+1);?>" type="text" class="form-control up" onKeyUp="multiplyCost(this);">
							</div>
						<?php endif; ?>
						<?php if(strpos($edit_config,',item_cost,') !== false): ?>
							<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Cost ($):</label>
								<input name="each_cost[]" id="amount_<?php echo ($emp_loop+1);?>" type="text" maxlength="20" onchange="materialStock();" class="form-control amount">
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div id="add_here_new_data"></div>
				<button id="add_new_row" class="btn brand-btn">Add Another Item</button>
            <?php endif; ?>

            <?php if(strpos($edit_config,',description,') !== false): ?>
                <div class="form-group">
                    <label for="additional_note" class="col-sm-4 control-label">Description:</label>
                    <div class="col-sm-8">
                        <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(strpos($edit_config,',cost,') !== false): ?>
                <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Cost<span class="text-red">*</span>:</label>
                    <div class="col-sm-8">
                      <input name="cost" type="text" id="cost" value="<?php echo $cost; ?>" class="form-control">
                    </div>
                </div>
            <?php endif; ?>

            <?php if(strpos($edit_config,',tax,') !== false): ?>
                <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Sales Tax %:</label>
                    <div class="col-sm-8">
                      <input name="mark_up" type="text" id="mark_up" value = "<?php if($mark_up == '') { echo "5"; } else { echo $mark_up;}?>" onKeyUp="totalCost();" class="form-control">
                    </div>
                </div>
            <?php endif; ?>

            <?php if(strpos($edit_config,',total,') !== false): ?>
                <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Total Cost<span class="text-red">*</span>:</label>
                    <div class="col-sm-8">
                      <input name="total_cost" id="total_cost" value = "<?php if($total_cost !== '') { echo $total_cost; }?>" type="text" class="form-control">
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <div class="col-sm-4 clearfix">
                    <a href="field_po.php" class="btn brand-btn pull-right">Back</a>
                    <!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
                </div>
                <div class="col-sm-8">
                        <!-- <input type="submit" name="purchase_order" value="Save Changes" class="btn brand-btn btn-lg pull-right" style="margin-right:20px"/> -->
                        <input type="submit" name="purchase_order" value="Issue PO" class="btn brand-btn btn-lg pull-right" style="margin-right:20px"/>
                </div>
            </div>

        </form>
    </div>
</div>

<?php include ('../footer.php'); ?>
