<?php include_once ('../include.php');
checkAuthorised('staff');
include_once ('../tcpdf/tcpdf.php'); ?>
</head>
<script type="text/javascript" src="staff.js"></script>
<body>
<?php include_once ('../navigation.php');
if(isset($_POST['submit'])) {
	$id = intval($_POST['submit']);
	$name = mysqli_real_escape_string($dbc, $_POST['name']);
	$description = htmlentities($_POST['description']);
	$user = get_contact($dbc, $_SESSION['contactid']);
	$time = date('Y-m-d H:i:s');

	if($id == 0) {
		$sql = "INSERT INTO positions (name, description, history) VALUES ('$name','$description','Position added by $user at $time.<br />\n')";
	}
	else {
		$sql = "UPDATE positions SET name='$name', description='$description', history=concat(ifnull(history,''),'Updated by $user at $time.<br />\n') where position_id='$id'";
	}
	mysqli_query($dbc, $sql);

	$position_id = $id;
	include('../Staff/save_position_rate_card.php');

	echo "<script>window.location = 'staff.php?tab=positions';</script>";
}
if(!empty($_GET['target'])) {
	if($_GET['target'] == 'pdf') {
		ob_clean();

		class MYPDF extends TCPDF {
			public function Header() {

				$this->SetFont('helvetica', '', 30);
				$this->MultiCell(180, 0, 'Positions', 0, 'C', 0, 0, 15, 15);
			}

			// Page footer
			public function Footer() {
				// Position at 15 mm from bottom
				$this->SetY(-15);
				$this->SetFont('helvetica', 'I', 8);
				$footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
				$this->Cell(90, 0, 'Date Printed: '.date('Y-m-d'), 0, 0, 'L');
				$this->Cell(90, 0, $footer_text, 0, 0, 'R');
			}
		}

		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
		$pdf->setFooterData(array(0,64,0), array(0,64,128));
		$pdf->SetMargins(PDF_MARGIN_LEFT, 45, PDF_MARGIN_RIGHT);

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 14);

		$query_check_credentials = "SELECT * FROM `positions` WHERE `deleted`=0 ORDER BY `name`";
		$result = mysqli_query($dbc, $query_check_credentials);
		$num_rows = mysqli_num_rows($result);
		while($row = mysqli_fetch_array($result)) {
			$html .= 'Position Name: <b>'.$row['name'].'</b><br /><table width="100%"><tr><td border="1">'.html_entity_decode($row['description']).'</td></tr></table><br /><br />';
		}

		$pdf->writeHTML($html, true, false, true, false, '');

		$pdf->Output('download/position_list_'.date('Y_m_d').'.pdf', 'F');

		echo '<script type="text/javascript">window.location.replace("download/position_list_'.date('Y_m_d').'.pdf");</script>';
	}
}
if(isset($_GET['id'])) {
	$id = intval($_GET['id']);
	if(isset($_GET['delete'])) {
		$user = mysqli_fetch_array(mysqli_query($dbc, "select concat(first_name,' ',last_name) name from contacts where contactid='{$_SESSION['contactid']}'"));
		$time = date('Y-m-d H:i:s');
        $date_of_archival = date('Y-m-d');
		$sql = "update positions set deleted=1, `date_of_archival` = '$date_of_archival', history=concat(ifnull(history,''),'Deleted by {$user['name']} at $time.') where position_id='$id'";
		$result = mysqli_query($dbc, $sql);
		$result = mysqli_fetch_array(mysqli_query($dbc, "select name from positions where position_id='$id'"));
		echo "<script>alert('The {$result['name']} position has been deleted'); window.location = 'staff.php?tab=positions';</script>";
	} else {
		$sql = "select * from positions where position_id='$id'";
		$result = mysqli_fetch_array(mysqli_query($dbc, $sql));
		$name = $result['name'];
		$description = $result['description'];
	}
}
else {
	$id = '';
	$name = '';
	$description = '';
}
$value_config = ','.get_config($dbc, 'positions_field_config').',';
?>
<div class="container">
	<div class="row">
		<!-- <div id="no-more-tables" class="main-screen contacts-list"> -->
		<div class="main-screen">
            <!-- Tile Header -->
            <div class="tile-header standard-header">
                <div class="col-xs-12 col-sm-4">
                    <h1>
                        <span class="pull-left" style="margin-top: -5px;"><a href="staff.php?tab=active" class="default-color">Staff</a></span>
                        <span class="clearfix"></span>
                    </h1>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

			<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

                <!-- Sidebar -->
                <div class="standard-collapsible hide-titles-mob tile-sidebar set-section-height">
                	<ul class="sidebar">
                		<li class="active">Position Details</li>
                	</ul>
                </div><!-- .tile-sidebar -->

				<!-- Main Screen -->
                <div class="scale-to-fill has-main-screen tile-content set-section-height">
            		<div class="main-screen-details main-screen override-main-screen <?= !in_array($_GET['tab'], ['active','suspended','probation']) ? 'standard-body' : '' ?>" style="height: inherit;">
						<div class='standard-body-title' style="<?= in_array($_GET['tab'], ['active','suspended','probation']) ? 'border-bottom: none;' : '' ?>">
							<h3><?php echo ($id == '' ? 'Add' : 'Edit'); ?> Position</h3>
						</div>
						<div class='standard-dashboard-body-content pad-left pad-right'>
							<h4><?php echo ($id == '' ? 'Add' : 'Edit'); ?> Position</h4>
							<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Position Name: </label>
							<div class='col-sm-8'><input class='form-control' type='text' name='name' value='<?php echo $name; ?>'></div></div>
							<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Description: </label>
							<div class='col-sm-8'><textarea class='form-control' name='description'><?php echo html_entity_decode($description); ?></textarea></div></div>
							<?php if (strpos($value_config, ','."Rate Card".',') !== FALSE) {
								include('../Staff/edit_position_rate_card.php');
							} ?>
						</div>
						<button type='submit' name='submit' value='<?php echo $id; ?>' class="btn brand-btn pull-right">Submit</button>
						<a href='staff.php?tab=positions' class="btn brand-btn pull-right">Back</a>
					</div>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include_once ('../footer.php'); ?>