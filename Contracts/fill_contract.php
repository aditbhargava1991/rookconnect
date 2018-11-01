<?php
/*
Add Vendor
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);

$text = '';
if($_GET['view'] == 'blank') {
	$contractid = $_GET['contractid'];
	$contract = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contracts` WHERE `contractid`='$contractid'"));
	$settings = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contracts`"));
	$pdf_name = preg_replace('/([^a-z]+)/','_',strtolower($contract['contract_name']))."_blank.pdf";
	$fields = explode('#*#', $contract['field_list']);
	$values = explode('#*#', $contract['default_list']);
	$text = html_entity_decode($contract['contract_text']);
	foreach($fields as $key => $field) {
		if($values[$key] == 'SIGNATURE') {
			$text = str_replace('[['.$field.']]', '<img src="blank_signature.png" width="190" height="80" border="0" />', $text);
		} else {
			$text = str_replace('[['.$field.']]', '<input type="text" name="field_'.$field.'" value="" size="20">', $text);
		}
	}

	// Fall through to generate the PDF
}

if (isset($_POST['fill_contract'])) {
	$contractid = $_GET['contractid'];
	$contract = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contracts` WHERE `contractid`='$contractid'"));
	$settings = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contracts`"));
	$businessid = $_POST['businessid'];
	$contactid = implode(',',$_POST['contactid']);
	$pdf_name = preg_replace('/[^a-z]/','_',strtolower($contract['contract_name']))."_".$contactid."_".date('Y_m_d').".pdf";
	$fields = explode('#*#', $_POST['fields']);
	$values = $_POST['values'];
	$staffid = $_SESSION['contactid'];
	$assignid = $_GET['assignid'];

	// Mark completed in contracts_staff and save contract in contract_completed
	if(empty($assignid)) {
		$sql_staff = "INSERT INTO `contracts_staff` (`contractid`, `recipient`, `contactid`, `businessid`, `done`, `due_date`) VALUES ('$contractid', '".get_email($dbc, $staffid)."', '$contactid', '$businessid', '1', '".date('Y-m-d')."')";
		$result = mysqli_query($dbc, $sql_staff);
		$assignid = mysqli_insert_id($dbc);
	} else {
		$sql_staff = "UPDATE `contracts_staff` SET `done`='1', `today_date`=CURRENT_TIMESTAMP WHERE `contractstaffid`='$assignid'";
		$result = mysqli_query($dbc, $sql_staff);
	}
	$sql_contract = "INSERT INTO `contracts_completed` (`contractstaffid`, `contractid`, `contactid`, `businessid`, `staffid`, `contract_fields`, `contract_values`, `contract_file`)
		VALUES ('$assignid', '$contractid', '$contactid', '$businessid', '$staffid', '".implode('#*#',$fields)."', '".implode('#*#',$values)."', '$pdf_name')";
	$result = mysqli_query($dbc, $sql_contract);

	// Generate PDF with Logo, Header, Footer, Contents
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	$text = html_entity_decode($contract['contract_text']);
	foreach($fields as $key => $field) {
		if($values[$key] == 'SIGNATURE') {
			$img = sigJsonToImage($_POST[$field.'_SIGN']);
			imagepng($img, 'download/sign_'.$field.'.png');
			$values[$key] = '<img src="download/sign_'.$field.'.png" width="190" height="80" border="0" />';
		}
		$text = str_replace('[['.$field.']]', $values[$key], $text);
	}

	// Fall through to generate the PDF
}

// If there is text to generate into a contract, create and display the PDF
if($text != '') {
    DEFINE('HEADER_LOGO', $settings['header_logo']);
    DEFINE('FOOTER_LOGO', $settings['footer_logo']);
	DEFINE('HEADER_TEXT', html_entity_decode($settings['header_text']));
    DEFINE('FOOTER_TEXT', html_entity_decode($settings['footer_text']));

    class MYPDF extends TCPDF {

        //Page header
        public function Header() {
			if(HEADER_LOGO != '') {
				$image_file = 'download/'.HEADER_LOGO;
				$this->Image($image_file, 10, 5, 0, 25, '', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
			}

			if(HEADER_TEXT != '') {
				$this->setCellHeightRatio(0.7);
				$this->SetFont('helvetica', '', 8);
				$header_text = '<p style="text-align:right;">'.HEADER_TEXT.'</p>';
				$this->writeHTMLCell(0, 0, 0 , 5, $header_text, 0, 0, false, "R", true);
			}
        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $footer_text = '<p style="text-align:right;">'.$this->getAliasNumPage().'</p>';
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);

            if(FOOTER_TEXT != '') {
                $this->SetY(-30);
                $this->setCellHeightRatio(0.7);
                $this->SetFont('helvetica', '', 8);
                $footer_text = FOOTER_TEXT;
                $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "C", true);
            }

            if(FOOTER_LOGO != '') {
                //$this->SetY(-30);
                $image_file = 'download/'.FOOTER_LOGO;
                $this->Image($image_file, 11, 275, 100, '', '', '', '', false, 300, 'C', false, false, 0, false, false, false);
            }
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetMargins(PDF_MARGIN_LEFT, (HEADER_LOGO != '' ? 35 : 20), PDF_MARGIN_RIGHT);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 8);
	$pdf->setCellHeightRatio(1);
	$pdf->writeHTML($text, true, false, true, false, '');

	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	$pdf->Output('download/'.$pdf_name, 'F');

	echo "<script> window.location.replace('download/$pdf_name'); </script>";
} ?>
<script type="text/javascript">
$(document).ready(function() {
	$('[name="contactid[]"]').change(function() {
		window.location.replace(window.location.search+'&contactid='+$(this).val());
	});
});
$(document).on('change', 'select[name="businessid"]', function() { loadContacts(this.value); });

function loadContacts(business) {
	$.ajax({
		url: 'contracts_ajax.php?fill=business_contacts&businessid='+business,
		method: 'GET',
		complete: function(result) {
			$('[name="contactid[]"]').empty().html(result.responseText).trigger('change.select2');
		}
	});
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('contracts'); ?>
<div class="container">
  <div class="row">

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
		<?php $contractid = $_GET['contractid'];
		$contractstaffid = $_GET['assignid'];
		$completedid = $_GET['completedcontractid'];

		$contactid = '';
		$staffid = '';
		$contract_fields = [];
		$contract_values = [];

		if(!empty($contractstaffid)) {
			$assigned = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contracts_staff` WHERE `contractstaffid`='$contractstaffid'"));
			$staffid = $assigned['staffid'];
			$contactid = $assigned['contactid'];
		}
		$customer = get_client($dbc, $contactid);
		if($customer == '') {
			$customer = get_contact($dbc, $contactid);
		}
		$contract = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contracts` WHERE `contractid`='$contractid'"));
		$contract_fields = explode('#*#',$contract['field_list']);
		$contract_values = explode('#*#',$contract['default_list']);
		if(!empty($completedid)) {
			$contract_complete = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contract_completed` WHERE `completedcontractid`='$completedcontractid'"));
			$contract_values = explode('#*#', $contract_complete['contract_values']);
		}
		if(!empty($_GET['contactid'])) {
			$contactid = explode(',',$_GET['contactid']);
			foreach($contactid as $id) {
				$businessid = get_contact($dbc, $id, 'businessid');
				if($customer != '') {
					$customer = get_client($dbc, $id);
				}
				if($customer == '') {
					$customer = get_contact($dbc, $id);
				}
			}
		} ?>

        <h1><?= $contract['contract_name'] ?></h1>

		<div class="gap-top triple-gap-bottom"><a href="contracts.php?tab=<?= $contract['category'] ?>" class="btn config-btn">Back to Dashboard</a></div>

		<div style='background-color: rgba(200,200,200,.9); padding:10px; color:black; width:100%; margin:auto; border:5px outset grey; border-radius:15px;'>
			<?php echo html_entity_decode($contract['contract_text']); ?>
		</div>
		<br />

        <div class="panel-group" id="accordion2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_cust" ><?= $contract['category'] ?> Information<span class="glyphicon glyphicon-plus"></span></a>
                    </h4>
                </div>

                <div id="collapse_cust" class="panel-collapse collapse">
                    <div class="panel-body">
						<?php if($contract['category'] == 'Customer') { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Customer:</label>
								<div class="col-sm-8">
									<select data-placeholder="Select a Customer" name="businessid" class="chosen-select-deselect form-control" width="380">
										<option value=""></option>
										<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, status FROM contacts WHERE deleted=0 AND category='Business'"),MYSQLI_ASSOC));
										foreach($query as $id) { ?>
											<option <?php echo ($businessid == $id ? 'selected' : ''); ?> value='<?= $id ?>' ><?= get_client($dbc, $id) ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Contact Name:</label>
								<div class="col-sm-8">
									<select data-placeholder="Select Contact(s)" name="contactid[]" multiple class="chosen-select-deselect form-control" width="380">
									  <?php
										$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT DISTINCT `contactid`, `name`, `first_name`, `last_name`, `category` FROM `contacts` WHERE `deleted`=0 AND `businessid`='$businessid'"),MYSQLI_ASSOC));
										$category = '';
										foreach($query as $id) {
											$contact = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name`, `category` FROM `contacts` WHERE `contactid`='$id'"));
											if($category != $contact['category']) {
												$category = $contact['category'];
												echo "<optgroup label='$category'>\n";
											}
											$name = get_client($dbc, $id);
											if($name == '') {
												$name = get_contact($dbc, $id);
											} ?>
											<option <?php echo (in_array($id, $contactid) ? 'selected' : ''); ?> value='<?php echo $id; ?>' ><?php echo $name; ?></option>
										<?php }
									  ?>
									</select>
								</div>
							</div>
						<?php } else { ?>
							<?php $cat_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) count FROM `contacts` WHERE `category`='".$contract['category']."'"))['count']; ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Assign to <?= ($cat_count > 0 ? $contract['category'] : 'Contact') ?>:</label>
								<div class="col-sm-8">
									<select data-placeholder="Select <?= ($cat_count > 0 ? $contract['category'] : 'Contact(s)') ?>" name="contactid[]" multiple class="chosen-select-deselect form-control" width="380">
									  <?php
										$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT DISTINCT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0".($cat_count > 0 ? " AND `category`='".$contract['category']."'" : "")),MYSQLI_ASSOC));
										foreach($query as $id) {
											$name = get_client($dbc, $id);
											if($name == '') {
												$name = get_contact($dbc, $id);
											} ?>
											<option <?php echo (in_array($id, $contactid) ? 'selected' : ''); ?> value='<?php echo $id; ?>' ><?php echo $name; ?></option>
										<?php }
									  ?>
									</select>
								</div>
							</div>
						<?php } ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_abi" >Contract Fields<span class="glyphicon glyphicon-plus"></span></a>
                    </h4>
                </div>

                <div id="collapse_abi" class="panel-collapse collapse">
                    <div class="panel-body">
						<input type="hidden" name="fields" value="<?= implode('#*#', $contract_fields) ?>">
						<?php foreach($contract_fields as $row => $field) {
							echo "<label class='col-sm-4 control-label'>$field:</label>";
							$default = $contract_values[$row];
							if($default == 'TODAY') {
								$default = date('Y-m-d');
							} else if($default == 'CUSTOMER') {
								$default = $customer;
							} ?>
							<div class="col-sm-8">
								<?php if($default == 'SIGNATURE') {
									$output_name = $field.'_SIGN';
									include('../phpsign/sign_multiple.php');
								} ?>
								<input type="<?= ($default == 'SIGNATURE' ? 'hidden' : 'text') ?>" name="values[]" value="<?= $default ?>" class="form-control">
							</div>
						<?php } ?>
                    </div>
                </div>
            </div>
        </div>

            <div class="form-group">
				<p><span class="hp-red"><em>Required Fields *</em></span></p>
            </div>

            <div class="form-group">
                <div class="col-sm-6">
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="contracts.php?tab=<?= $contract['category'] ?>" class="btn brand-btn btn-lg">Back</a>
					<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
                </div>
				<div class="col-sm-6">
					<button type="submit" name="fill_contract" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
					<span class="popover-examples list-inline pull-right" style="margin:15px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save your contract."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</div>
            </div>

			</div>



    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
