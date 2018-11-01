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
    $attachment = 'download/'.$name_of_file;

    send_email('', $to, '', '', $subject, $body, $attachment);

    echo '<script type="text/javascript"> alert("Invoice Successfully Sent to Patient."); window.location.replace("payment_invoice.php"); </script>';

	//header('Location: payment_invoice.php');
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

});
</script>
</head>
<body>
<?php include_once ('../navigation.php');

?>
<div class="container triple-pad-bottom">
    <div class="row">
        <h2><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?>
        <?php
            echo '<a href="field_config_invoice.php" class="btn mobile-block pull-right"><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        ?>
        </h2>

        <button type="button" class="btn brand-btn mobile-block active_tab" >Unpaid Invoice</button>
        <a href='invoice.php'><button type="button" class="btn brand-btn mobile-block" >Paid Invoice</button></a>
        <a href='unpaid_insurer_invoice.php'><button type="button" class="btn brand-btn mobile-block" >Unpaid Insurer Invoice</button></a>
        <a href='cashout.php'><button type="button" class="btn brand-btn mobile-block" >Cashout</button></a>

        <form name="invoice" method="post" action="payment_invoice.php" class="form-inline" role="form">

            <div class="table-responsive">
                <span class="pull-right"><img src="../img/green.png" width="32" height="32" border="0" alt=""> Invoice Paid &nbsp;&nbsp;
                <img src="../img/red.png" width="32" height="32" border="0" alt=""> Invoice Not Paid
                <img src="../img/blue.png" width="32" height="32" border="0" alt=""> Invoice Pending</span>
            <br><br>

            <?php
            echo '<a href="create_invoice.php" class="btn brand-btn pull-right">Sell</a>';
            // Display Pager

            if(!empty($_GET['patientid'])) {
                $patientid = $_GET['patientid'];
                $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND patientid='$patientid' AND paid = 'No'";
                echo '<input type="hidden" name="patientid_for_invoice"  class="patientid_for_invoice" value="patient_'.$patientid.'">';
            } else {
                //$query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND (DATE(invoice_date) = DATE(NOW()) OR paid = 'No') ORDER BY paid ASC,payment_type ASC,final_price DESC";
                $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND paid = 'No' ORDER BY invoiceid DESC";
            }

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
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
                <th>Generate</th>
                <th>Patient Invoice</th>
                <th>Edit</th>
                <th>Insurer Invoice</th>
                </tr>";
            } else {
                echo "<h2>No Record Found.</h2>";
            }

            while($row = mysqli_fetch_array( $result ))
            {
                $back = '';
                if($row['invoice_date'] == date('Y-m-d') && $row['paid'] == 'Yes') {
                    $back = 'style="background-color: rgba(0,255,0,0.4);"';
                }
                if($row['final_price'] == '') {
                    $back = 'style="background-color: rgba(0,0,255,0.4);"';
                }
                if($row['final_price'] != '' && $row['paid'] == 'No') {
                    $back = 'style="background-color: rgba(255,0,0,0.4);"';
                }
                $patientid = $row['patientid'];

                echo '<tr '.$back.'>';
                if(!empty($_GET['patientid'])) {
                    echo '<td><input type="checkbox" name="invoice[]" value="'.$row['invoiceid'].'" class="privileges_view_'.$patientid.'" ></td>';
                }

                echo '<td>' . $row['invoiceid'] . '</td>';
                echo '<td>' . $row['invoice_date'] . '</td>';

                echo '<td>'.get_contact($dbc, $row['patientid']). '</td>';
				//<a href="#"  onclick=" window.open(\''.WEBSITE_URL.'/Contact/add_contact.php?contactid='.$row['patientid'].'\', \'newwindow\', \'width=900, height=900\'); return false;">'.get_contact($dbc, $row['patientid']). '</a>
                echo '<td>' . $row['service_date'] . '</td>';

                $serviceid = $row['serviceid'];
                echo '<td>'. get_all_from_service($dbc, $serviceid, 'service_code').' : '.get_all_from_service($dbc, $serviceid, 'heading') . '</td>';
                echo '<td>' . $row['paid'] . '</td>';
                echo '<td>$' . ($row['final_price']) . '</td>';

                if($row['final_price'] == '') {
                    echo '<td><a href=\'create_invoice.php?invoiceid='.$row['invoiceid'].'&patientid='.$row['patientid'].'\' >Generate</a></td>';
                } else {
                    echo '<td>-</td>';
                }

                if($row['final_price'] != '') {
                    if($row['paid'] == 'Yes') {
                    $name_of_file = 'download/invoice_'.$row['invoiceid'].'.pdf';
                    $md5 = md5_file($name_of_file);
                    if($md5 == $row['invoice_md5']) {
                        echo '<td><a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a> | <a href=\'payment_invoice.php?action=email&invoiceid='.$row['invoiceid'].'&patientid='.$patientid.'\' >Email</a></td>';
                    } else {
                        echo '<td>(Error : File Change)</td>';
                    }
                    } else {
                        echo '<td>-</td>';
                    }
                } else {
                    echo '<td>-</td>';
                }

                if($row['paid'] == 'No' && $row['final_price'] != '') {
                    echo '<td><a href=\'create_invoice.php?invoiceid='.$row['invoiceid'].'&patientid='.$row['patientid'].'\' >Edit</a></td>';
                } else {
                    echo '<td>-</td>';
                }

                /*
                if($row['final_price'] != '') {
                    if($row['paid'] == 'No') {
                        echo '<td><a href=\'create_invoice.php?action=pay&from=invoice&invoiceid='.$row['invoiceid'].'\' >Pay</a></td>';
                    } else {
                        echo '<td>-</td>';
                    }
                } else {
                    echo '<td>-</td>';
                }
                */

                if($row['final_price'] != '' && $row['insurerid'] != 0 && $row['paid'] == 'Yes') {
                    $name_of_file = 'download/insuranceinvoice_'.$row['invoiceid'].'.pdf';
                    echo '<td><a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a></td>';
                } else {
                    echo '<td>-</td>';
                }

                /*echo '<td>';
                if (strpos(PAYMENT_INVOICE_PRIVILEGES,'D') !== false) {
                    echo '<a href=\'delete_restore.php?action=delete&invoiceid='.$row['invoiceid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                }
                echo '</td>';
                */

                echo "</tr>";
            }

            echo '</table></div>';
            echo '<a href="create_invoice.php" class="btn brand-btn pull-right">Sell</a>';
            ?>


        

        </form>

        <a href="<?php echo WEBSITE_URL;?>/home.php" class="btn brand-btn">Back</a>

	</div>

</div>
<?php include ('../footer.php'); ?>