<?php $s_employeeid = $_SESSION['contactid'];
if (isset($_POST['upload_doc_for_po_invoice'])) {
    $poid = $_POST['upload_doc_for_po_invoice'];
	$file_count = count($_FILES["file_".$poid]["name"]);
	for($i = 0; $i < $file_count; $i++) {
		if (!file_exists('download/po_invoice')) {
			mkdir('download/po_invoice', 0777, true);
		}
		$docs = $_FILES["file_".$poid]["name"][$i];

		$third_invoice_no = $_POST['thirdinvoiceno_'.$poid];

		move_uploaded_file($_FILES["file_".$poid]["tmp_name"][$i], "download/po_invoice/".$_FILES["file_".$poid]["name"][$i]);

		if($docs != '') {
			$docs = '##FFM##'.$docs;
			$query_update_bid = "UPDATE `site_work_po` SET `invoice`=CONCAT(IFNULL(`invoice`, ''), '$docs') WHERE `poid` = '$poid'";
			$result_update_bid = mysqli_query($dbc, $query_update_bid);
		}
		if($third_invoice_no != '') {
			$query_update_bid = "UPDATE `site_work_po` SET `invoice_number` = '$third_invoice_no' WHERE `poid` = '$poid'";
			$result_update_bid = mysqli_query($dbc, $query_update_bid);
		}
	}
} ?>
<form name="form_jobs" enctype="multipart/form-data" method="post" action="" class="form-inline" role="form">

    <div id="no-more-tables">
        <?php
		if($edit_access == 1) {
			echo '<a href="add_po.php" class="btn brand-btn pull-right">Create PO</a>';
		}
        // Display Pager
        $rowsPerPage = ITEMS_PER_PAGE;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        $po_name_search = '';
        if (isset($_POST['search_po_submit'])) {
            $po_name_search = $_POST['search_po'];
        }
        if (isset($_POST['display_all_po'])) {
            $po_name_search = '';
        }

		$query_check_credentials = "SELECT * FROM `site_work_po` WHERE `deleted`=0 ORDER BY `poid` DESC LIMIT $offset, $rowsPerPage";
		$query   = "SELECT COUNT(*) AS numrows FROM `site_work_po` WHERE `deleted`=0";

        if($po_name_search == '') {
            echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $query, $pageNum, $rowsPerPage).'</h1>';
        }
        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead><tr class='hidden-xs hidden-sm'>
                <th>PO #</th>
                <th>Vendor</th>
                <th>PO</th>
                <th>3rd Party Invoice# / Upload Invoice</th>
                <th>Status</th>";
                if($edit_access == 1) {
                    echo "<th>Function</th>";
                }
                echo "</tr></thead>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }
        while($row = mysqli_fetch_array( $result ))
        {
            echo '<tr>';
			echo '<td data-title="PO#">' . $row['poid']. '</td>';
			echo '<td data-title="Vendor">' . get_client($dbc, $row['vendorid']). '</td>';
            $name_of_file = 'download/work_order_po_'.$row['poid'].'.pdf';
            echo '<td data-title="PDF"><a href='.$name_of_file.' target="_blank">View</a></td>';

			echo '<td data-title="Invoice">';
			$invoice = $row['invoice'];

            if($row['invoice_number'] == '') {
                echo '<input name="thirdinvoiceno_'.$row['poid'].'" value="'.$row['invoice_number'].'" type="text" class="form-control" style="width: 20%;" />';
            } else {
                echo '#'.$row['invoice_number'];
            }

            if($invoice != '') {
                $vin = explode('##FFM##', $invoice);
                foreach($vin as $vinc => $venin) {
                    if($venin != '') {
                        echo '<br> - <a href="download/po_invoice/'.$venin.'" target="_blank">'.$venin.'</a> | ';
                        echo '<a href=\'../delete_restore.php?action=delete&subtab=site_work_order_po&site_poid='.$row['poid'].'&vinc='.$vinc.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                    }
                }
            }

            echo '&nbsp;&nbsp;<input accept="image/*" name="file_'.$row['poid'].'[]" type="file" data-filename-placement="inside" class="form-control" multiple>';
            echo '<button type="submit" name="upload_doc_for_po_invoice" value="'.$row['poid'].'" class="btn brand-btn">Submit</button>';
			echo '</td>';

			$work_order = mysqli_query($dbc, "SELECT * FROM `site_work_orders` WHERE CONCAT(',',`po_id`,',') LIKE ',".$row['poid'].",'");
			$workorderid = mysqli_fetch_array($work_order)['workorderid'];
            echo '<td data-title="Status">'.(mysqli_num_rows($work_order) > 0 ? 'Attached to '.($edit_access == 1 ? '<a href="add_work_order.php?workorderid='.$workorderid.'">' : '').'Work Order #'.$workorderid.($edit_access == 1 ? '</a>' : '') : '').'</td>';

			echo '<td data-title="Complete">';
            if($edit_access == 1) {
				echo '<a href=\'add_po.php?poid='.$row['poid'].'\'>Edit</a> | ';
				echo '<a href=\'../delete_restore.php?action=delete&subtab=site_work_order_po&site_poid='.$row['poid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
            }
			echo '</td>';

            echo "</tr>";
        }

        echo '</table></div>';
		if($edit_access == 1) {
			echo '<a href="add_po.php" class="btn brand-btn pull-right">Create PO</a>';
		}
        if($po_name_search == '') {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        }
        ?>

</form>