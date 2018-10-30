<?php
/*
Payment/Invoice Listing
*/
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
include_once('../tcpdf/tcpdf.php');

/*
if (isset($_POST['submit_pay'])) {
	$all_invoice = implode(',',$_POST['invoice']);
	header('Location: create_invoice.php?action=pay&from=patient&invoiceid='.$all_invoice);
}

if (isset($_POST['printpdf'])) {
    include_once ('print_unpaid_invoice.php');
}
*/
if((!empty($_GET['action'])) && ($_GET['action'] == 'email')) {

	$invoiceid = $_GET['invoiceid'];
	$patientid = $_GET['patientid'];

	$name_of_file = 'invoice_'.$invoiceid.'.pdf';

    $to = get_email($dbc, $patientid);
    $subject = 'Physiotherapy Invoice';
	$body = 'Please find attached your invoice from Physiotherapy';
    $attachment = 'Download/'.$name_of_file;

    send_email('', $to, '', '', $subject, $body, $attachment);

    echo '<script type="text/javascript"> alert("Invoice Successfully Sent to Patient."); window.location.replace("index.php?tab=today"); </script>';

	//header('Location: unpaid_invoice.php');
    // Send Email to Client
}
?>
<script src="<?php echo WEBSITE_URL;?>/js/jquery.cookie.js"></script>
<script type="text/javascript">
$(document).ready(function() {

    $('.all_view').click(function(event) {  //on click
		var arr = $('.patientid_for_invoice').val().split('_');
        if(this.checked) { // check select status
            $('.privileges_view_'+arr[1]).each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"
            });
        }else{
            $('.privileges_view_'+arr[1]).each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"
            });
        }
    });

	$('.iframe_open').click(function(){
			var id = $(this).attr('id');
			var arr = id.split('_');
		    $('#iframe_instead_of_window').attr('src', '<?php echo WEBSITE_URL; ?>/Contacts/add_contacts.php?category=Patient&contactid='+arr[0]);
		    $('.iframe_title').text('View Patient');
			$('.hide_on_iframe').hide(1000);
			$('.iframe_holder').show(1000);
	});
	$('.close_iframer').click(function(){
				$('.iframe_holder').hide(1000);
				$('.hide_on_iframe').show(1000);
				location.reload();
	});

});
</script>
</head>
<body>
<?php include_once ('../navigation.php');

?>
<div class="container triple-pad-bottom">
    <div class='iframe_holder' style='display:none;'>

		<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
    </div>
	<div class="row hide_on_iframe">
        <h2><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?>
        <?php
            echo '<a href="field_config_invoice.php" class="btn mobile-block pull-right"><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        ?>
        </h2>

        <a href='index.php?tab=today'><button type="button" class="btn brand-btn mobile-block" >Today's Invoices</button></a>
        <a href='generate_invoice.php'><button type="button" class="btn brand-btn mobile-block" >Generate Invoice</button></a>
        <a href='unpaid_invoice.php'><button type="button" class="btn brand-btn mobile-block" >Unpaid Invoice</button></a>
        <a href='waiting_on_insurer_invoice.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Waiting on Insurer Invoice</button></a>
        <a href='invoice.php'><button type="button" class="btn brand-btn mobile-block" >Paid Invoice</button></a>
        <a href='unpaid_insurer_invoice.php'><button type="button" class="btn brand-btn mobile-block" >Unpaid Insurer Invoice Report</button></a>
        <a href='cashout.php'><button type="button" class="btn brand-btn mobile-block" >Cashout</button></a>

        <form name="invoice" method="post" action="" class="form-inline" role="form">
           <?php
            if(isset($_POST['search_user_submit'])) {
                $search_user = $_POST['search_user'];
            } else {
                $search_user = '';
            }
			if (isset($_POST['display_all_inventory'])) {
				$search_user = '';
			}
            ?>
            <div class="form-group">
              <label for="site_name" class="col-sm-4 control-label">Search by Customer:</label>
              <div class="col-sm-8">
                  <select data-placeholder="Pick a User" name="search_user" id="search_user" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $query = mysqli_query($dbc,"SELECT distinct(patientid) FROM invoice WHERE paid = 'Waiting on Insurer'");
                    while($row = mysqli_fetch_array($query)) {
                    ?><option <?php if ($row['patientid'] == $search_user) { echo " selected"; } ?> value='<?php echo  $row['patientid']; ?>' ><?php echo get_contact($dbc, $row['patientid']); ?></option>
                <?php	}
                ?>
                </select>
              </div>
            </div>

            <input type="hidden" name="patientpdf" value="<?php echo $search_user; ?>">

            <div class="form-group">
                <button type="submit" name="search_user_submit" id="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
            </div>

            <div class="table-responsive">

            <?php
            echo '<a href="create_invoice.php" class="btn brand-btn pull-right">Sell</a>';
            // Display Pager

            /* Pagination Counting */
            $rowsPerPage = 25;
            $pageNum = 1;

            if(isset($_GET['page'])) {
                $pageNum = $_GET['page'];
            }

            $offset = ($pageNum - 1) * $rowsPerPage;

            if(!empty($_GET['patientid'])) {
                $patientid = $_GET['patientid'];
                $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND patientid='$patientid' AND paid = 'No' LIMIT $offset, $rowsPerPage";
                $query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 0 AND patientid='$patientid' AND paid = 'No'";
                echo '<input type="hidden" name="patientid_for_invoice"  class="patientid_for_invoice" value="patient_'.$patientid.'">';
            } else if($search_user != '') {
                $query_check_credentials = "SELECT * FROM invoice WHERE patientid='$search_user' AND paid = 'Waiting on Insurer' ORDER BY invoiceid DESC LIMIT $offset, $rowsPerPage";
                $query = "SELECT count(*) as numrows FROM invoice WHERE patientid='$search_user' AND paid = 'Waiting on Insurer' ORDER BY invoiceid DESC";
            } else {
                //$query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND (DATE(invoice_date) = DATE(NOW()) OR paid = 'No') ORDER BY paid ASC,payment_type ASC,final_price DESC";
                $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND paid = 'Waiting on Insurer' ORDER BY invoiceid DESC LIMIT $offset, $rowsPerPage ";
                $query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 0 AND paid = 'Waiting on Insurer' ORDER BY invoiceid";
            }

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                // Added Pagination //
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                echo "<table border='2' cellpadding='10' class='table'>";
                echo "<tr>";
                if(!empty($_GET['patientid'])) {
                    //echo "<th><input type='checkbox' class='all_view' name='all_view'  >&nbsp;All	<button type='submit' name='submit_pay' value='Submit' class='btn brand-btn '>Pay</button></th>";
                }
                echo "<th>Invoice#</th>
                <th>Invoice Date</th>
                <th>Patient</th>
                <th>Service Date</th>
                <th>Service</th>
                <th>Paid</th>
                <th>Total</th>
                <th>Edit</th>
                </tr>";
            } else {
                echo "<h2>No Record Found.</h2>";
            }

            while($row = mysqli_fetch_array( $result ))
            {
                $patientid = $row['patientid'];

                echo '<tr>';
                if(!empty($_GET['patientid'])) {
                    echo '<td><input type="checkbox" name="invoice[]" value="'.$row['invoiceid'].'" class="privileges_view_'.$patientid.'" ></td>';
                }

                echo '<td>' . $row['invoiceid'] . '</td>';
                echo '<td>' . $row['invoice_date'] . '</td>';

                if($row['patientid'] != 0) {
					echo '<td><a class="iframe_open" id="'.$row['patientid'].'">'.get_contact($dbc, $row['patientid']). '</a></td>';
					//echo '<td><a href="#"  onclick=" window.open(\''.WEBSITE_URL.'/Contact/add_contact.php?contactid='.$row['patientid'].'\', \'newwindow\', \'width=900, height=900\'); return false;">'.get_contact($dbc, $row['patientid']). '</a></td>';
                    //echo '<td>'.get_contact($dbc, $row['patientid']). '</td>';
                } else {
                    echo '<td>Non Patient</td>';
                }
                echo '<td>' . $row['service_date'] . '</td>';

                $serviceid = $row['serviceid'];
                echo '<td>'. get_all_from_service($dbc, $serviceid, 'service_code').' : '.get_all_from_service($dbc, $serviceid, 'heading') . '</td>';
                echo '<td>' . $row['paid'] . '</td>';
                echo '<td>$' . ($row['final_price']) . '</td>';

                if($row['paid'] != 'Yes' && $row['final_price'] != '') {
                    echo '<td><a href=\'create_invoice.php?invoiceid='.$row['invoiceid'].'&patientid='.$row['patientid'].'\' >Edit</a></td>';
                } else {
                    echo '<td>-</td>';
                }

                /*
                if($row['final_price'] != '') {
                    if($row['paid'] != 'Yes') {
                        echo '<td><a href=\'create_invoice.php?action=pay&from=invoice&invoiceid='.$row['invoiceid'].'\' >Pay</a></td>';
                    } else {
                        echo '<td>-</td>';
                    }
                } else {
                    echo '<td>-</td>';
                }
                */

                echo "</tr>";
            }

            echo '</table></div>';
            // Added Pagination //
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            echo '<a href="create_invoice.php" class="btn brand-btn pull-right">Sell</a>';
            ?>



        </form>

        <a href="<?php echo WEBSITE_URL;?>/home.php" class="btn brand-btn">Back</a>

	</div>

</div>
<?php include ('../footer.php'); ?>
