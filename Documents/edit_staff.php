<?php include_once('../include.php');
if(!empty($_GET['tile_name'])) {
    checkAuthorised(false,false,'documents_all_'.$_GET['tile_name']);
} else {
    checkAuthorised('documents_all');
}
include_once('document_settings.php');

if(!empty($_GET['certuploadid'])) {
    $certuploadid = $_GET['certuploadid'];
    $query = mysqli_query($dbc,"DELETE FROM staff_documents_uploads WHERE certuploadid='$certuploadid'");
    $staff_documentsid = $_GET['edit'];

    echo '<script type="text/javascript"> window.location.replace("?tile_name='.$tile_name.'&tab='.$_GET['tab'].'&edit='.$staff_documentsid.'"); </script>';
}

if (isset($_POST['add_staff_documents'])) {
    $contactid =$_POST['contactid'];
    if($_POST['new_staff_documents'] != '') {
        $staff_documents_type = filter_var($_POST['new_staff_documents'],FILTER_SANITIZE_STRING);
    } else {
        $staff_documents_type = filter_var($_POST['staff_documents_type'],FILTER_SANITIZE_STRING);
    }

    if($_POST['new_category'] != '') {
        $category = filter_var($_POST['new_category'],FILTER_SANITIZE_STRING);
    } else {
        $category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
    }
    $invoice_description = filter_var(htmlentities($_POST['invoice_description']),FILTER_SANITIZE_STRING);
    $ticket_description = filter_var(htmlentities($_POST['ticket_description']),FILTER_SANITIZE_STRING);
    $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
    $fee = filter_var($_POST['fee'],FILTER_SANITIZE_STRING);
    $title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);

    $staff_documents_code = filter_var($_POST['staff_documents_code'],FILTER_SANITIZE_STRING);
    //$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
    $heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
    $cost = filter_var($_POST['cost'],FILTER_SANITIZE_STRING);
    //$description = filter_var($_POST['description'],FILTER_SANITIZE_STRING);

    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);

    if($_POST['same_desc'] == 1) {
        $quote_description = $description;
    } else {
        $quote_description = filter_var(htmlentities($_POST['quote_description']),FILTER_SANITIZE_STRING);
    }
    $final_retail_price = filter_var($_POST['final_retail_price'],FILTER_SANITIZE_STRING);
    $admin_price = filter_var($_POST['admin_price'],FILTER_SANITIZE_STRING);
    $wholesale_price = filter_var($_POST['wholesale_price'],FILTER_SANITIZE_STRING);
    $commercial_price = filter_var($_POST['commercial_price'],FILTER_SANITIZE_STRING);
    $staff_price = filter_var($_POST['staff_price'],FILTER_SANITIZE_STRING);
    $minimum_billable = filter_var($_POST['minimum_billable'],FILTER_SANITIZE_STRING);
    $estimated_hours = filter_var($_POST['estimated_hours'],FILTER_SANITIZE_STRING);
    $actual_hours = filter_var($_POST['actual_hours'],FILTER_SANITIZE_STRING);
    $msrp = filter_var($_POST['msrp'],FILTER_SANITIZE_STRING);

    $unit_price = filter_var($_POST['unit_price'],FILTER_SANITIZE_STRING);
    $unit_cost = filter_var($_POST['unit_cost'],FILTER_SANITIZE_STRING);
    $rent_price = filter_var($_POST['rent_price'],FILTER_SANITIZE_STRING);
    $rental_days = filter_var($_POST['rental_days'],FILTER_SANITIZE_STRING);
    $rental_weeks = filter_var($_POST['rental_weeks'],FILTER_SANITIZE_STRING);
    $rental_months = filter_var($_POST['rental_months'],FILTER_SANITIZE_STRING);
    $rental_years = filter_var($_POST['rental_years'],FILTER_SANITIZE_STRING);
    $reminder_alert = filter_var($_POST['reminder_alert'],FILTER_SANITIZE_STRING);
    $daily = filter_var($_POST['daily'],FILTER_SANITIZE_STRING);
    $weekly = filter_var($_POST['weekly'],FILTER_SANITIZE_STRING);
    $monthly = filter_var($_POST['monthly'],FILTER_SANITIZE_STRING);
    $annually = filter_var($_POST['annually'],FILTER_SANITIZE_STRING);
    $total_days = filter_var($_POST['total_days'],FILTER_SANITIZE_STRING);
    $total_hours = filter_var($_POST['total_hours'],FILTER_SANITIZE_STRING);
    $total_km = filter_var($_POST['total_km'],FILTER_SANITIZE_STRING);
    $total_miles = filter_var($_POST['total_miles'],FILTER_SANITIZE_STRING);
    $upload_date = date('Y-m-d');

    if(empty($_POST['staff_documentsid'])) {
        $query_insert_vendor = "INSERT INTO `staff_documents` (`contactid`, `staff_documents_type`, `category`, `staff_documents_code`, `heading`, `cost`, `description`, `quote_description`, `invoice_description`, `ticket_description`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `staff_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `name`, `title`,  `fee`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `upload_date`) VALUES ('$contactid', '$staff_documents_type', '$category', '$staff_documents_code', '$heading', '$cost', '$description', '$quote_description', '$invoice_description', '$ticket_description', '$final_retail_price', '$admin_price', '$wholesale_price', '$commercial_price', '$staff_price', '$minimum_billable', '$estimated_hours', '$actual_hours', '$msrp', '$name', '$title', '$fee', '$unit_price', '$unit_cost', '$rent_price', '$rental_days', '$rental_weeks', '$rental_months', '$rental_years', '$reminder_alert', '$daily', '$weekly', '$monthly', '$annually', '$total_days', '$total_hours', '$total_km', '$total_miles', '$upload_date')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $staff_documentsid = mysqli_insert_id($dbc);
        $url = 'Added';
    } else {
        $staff_documentsid = $_POST['staff_documentsid'];
        $query_update_vendor = "UPDATE `staff_documents` SET `contactid` = '$contactid', `staff_documents_type` = '$staff_documents_type', `category` = '$category',`staff_documents_code` = '$staff_documents_code', `heading` = '$heading', `cost` = '$cost', `description` = '$description', `quote_description` = '$quote_description', `invoice_description` = '$invoice_description', `ticket_description` = '$ticket_description', `final_retail_price` = '$final_retail_price', `admin_price` = '$admin_price', `wholesale_price` = '$wholesale_price', `commercial_price` = '$commercial_price', `staff_price` = '$staff_price', `minimum_billable` = '$minimum_billable', `estimated_hours` = '$estimated_hours', `actual_hours` = '$actual_hours', `msrp` = '$msrp', `name` = '$name', `title` = '$title', `fee` = '$fee', `unit_price` = '$unit_price', `unit_cost` = '$unit_cost', `rent_price` = '$rent_price', `rental_days` = '$rental_days', `rental_weeks` = '$rental_weeks', `rental_months` = '$rental_months', `rental_years` = '$rental_years', `reminder_alert` = '$reminder_alert', `daily` = '$daily', `weekly` = '$weekly', `monthly` = '$monthly', `annually` = '$annually', `total_days` = '$total_days', `total_hours` = '$total_hours', `total_km` = '$total_km', `total_miles` = '$total_miles', `upload_date` = '$upload_date' WHERE `staff_documentsid` = '$staff_documentsid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
    }

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        $basename = $document = str_replace(',','',htmlspecialchars($_FILES["upload_document"]["name"][$i], ENT_QUOTES));
        $document_link_name = filter_var($_POST["document_link_name"][$i],FILTER_SANITIZE_STRING);

        if($document != '') {
            $j = 0;
            while (file_exists('download/' . $document)) {
                $document = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$j.')$1', $basename);
            }

            move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$document) ;

            $query_insert_staff_doc = "INSERT INTO `staff_documents_uploads` (`staff_documentsid`, `type`, `document_link`, `document_link_name`) VALUES ('$staff_documentsid', 'Document', '$document', '$document_link_name')";
            $result_insert_staff_doc = mysqli_query($dbc, $query_insert_staff_doc);
        }
    }

    for($i = 0; $i < count($_POST['support_link']); $i++) {
        $support_link = $_POST['support_link'][$i];
        $support_link_name = filter_var($_POST["support_link_name"][$i],FILTER_SANITIZE_STRING);

        if($support_link != '') {
            $query_insert_staff_doc = "INSERT INTO `staff_documents_uploads` (`staff_documentsid`, `type`, `document_link`, `document_link_name`) VALUES ('$staff_documentsid', 'Link', '$support_link', '$support_link_name')";
            $result_insert_staff_doc = mysqli_query($dbc, $query_insert_staff_doc);
        }
    }

    echo '<script type="text/javascript"> window.location.replace("?tile_name='.$tile_name.'&tab='.$_GET['tab'].'"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
}

?>
<script type="text/javascript">
$(document).ready(function() {
    $("#form1").submit(function( event ) {
        var staff_documents_type = $("#staff_documents_type").val();
        var category = $("input[name=category]").val();
        var title = $("input[name=title]").val();
        if (staff_documents_type == '' || category == '' || title == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });

    $("#staff_documents_type").change(function() {
        if($("#staff_documents_type option:selected").text() == 'New Staff Documents') {
                $( "#new_staff_documents" ).show();
        } else {
            $( "#new_staff_documents" ).hide();
        }
    });

    $("#category").change(function() {
        if($("#category option:selected").text() == 'New Category') {
                $( "#new_category" ).show();
        } else {
            $( "#new_category" ).hide();
        }
    });

    $('#add_row_doc').on( 'click', function () {
        var clone = $('.additional_doc').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_doc");
        $('#add_here_new_doc').append(clone);
        return false;
    });

    $('#add_row_link').on( 'click', function () {
        var clone = $('.additional_link').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_link");
        $('#add_here_new_link').append(clone);
        return false;
    });

});
</script>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<?php
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT staff_documents FROM field_config"));
    if(empty($get_field_config['staff_documents'])) {
        $get_field_config['staff_documents'] = 'Staff Documents Type,Category,Title,Uploader,Link';
    }
    $value_config = ','.$get_field_config['staff_documents'].',';

    $staff_documents_type = '';
    $category = '';
    $staff_documents_code = '';
    $heading = '';
    $cost = '';
    $description = '';
    $quote_description = '';
    $invoice_description = '';
    $ticket_description = '';

    $final_retail_price = '';
    $name = '';
    $fee = '';
    $admin_price = '';
    $wholesale_price = '';
    $commercial_price = '';
    $staff_price = '';
    $minimum_billable = '';
    $estimated_hours = '';
    $actual_hours = '';
    $msrp = '';
    $contactid = '';

    $unit_price = '';
    $unit_cost = '';
    $rent_price = '';
    $rental_days = '';
    $rental_weeks = '';
    $rental_months = '';
    $rental_years = '';
    $reminder_alert = '';
    $daily = '';
    $weekly = '';
    $monthly = '';
    $annually = '';
    $total_days = '';
    $total_hours = '';
    $total_km = '';
    $total_miles = '';
    $title = '';

    if(!empty($_GET['edit'])) {

        $staff_documentsid = $_GET['edit'];
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM staff_documents WHERE staff_documentsid='$staff_documentsid'"));
        $contactid = $get_contact['contactid'];
        $staff_documents_type = $get_contact['staff_documents_type'];
        $category = $get_contact['category'];
        $staff_documents_code = $get_contact['staff_documents_code'];
        $heading = $get_contact['heading'];
        $cost = $get_contact['cost'];
        $description = $get_contact['description'];
        $quote_description = $get_contact['quote_description'];
        $invoice_description = $get_contact['invoice_description'];
        $ticket_description = $get_contact['ticket_description'];
        $name = $get_contact['name'];
        $title = $get_contact['title'];
        $fee = $get_contact['fee'];

        $final_retail_price = $get_contact['final_retail_price'];
        $admin_price = $get_contact['admin_price'];
        $wholesale_price = $get_contact['wholesale_price'];
        $commercial_price = $get_contact['commercial_price'];
        $staff_price = $get_contact['staff_price'];
        $minimum_billable = $get_contact['minimum_billable'];
        $estimated_hours = $get_contact['estimated_hours'];
        $actual_hours = $get_contact['actual_hours'];
        $msrp = $get_contact['msrp'];

        $unit_price = $get_contact['unit_price'];
        $unit_cost = $get_contact['unit_cost'];
        $rent_price = $get_contact['rent_price'];
        $rental_days = $get_contact['rental_days'];
        $rental_weeks = $get_contact['rental_weeks'];
        $rental_months = $get_contact['rental_months'];
        $rental_years = $get_contact['rental_years'];
        $reminder_alert = $get_contact['reminder_alert'];
        $daily = $get_contact['daily'];
        $weekly = $get_contact['weekly'];
        $monthly = $get_contact['monthly'];
        $annually = $get_contact['annually'];
        $total_days = $get_contact['total_days'];
        $total_hours = $get_contact['total_hours'];
        $total_km = $get_contact['total_km'];
        $total_miles = $get_contact['total_miles'];
    ?>
    <input type="hidden" id="staff_documentsid" name="staff_documentsid" value="<?php echo $staff_documentsid ?>" />
    <?php   }      ?>

    <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
    <div class="form-group">
      <label for="site_name" class="col-sm-4 control-label">Staff:</label>
      <div class="col-sm-8">
        <select data-placeholder="Choose a Staff..." name="contactid" class="chosen-select-deselect form-control" width="380">
          <option value=""></option>
          <?php
                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""),MYSQLI_ASSOC));
                foreach($query as $id) {
                    $query_staff = mysqli_fetch_array(mysqli_query($dbc, "SELECT status, deleted FROM contacts WHERE contactid='$id'"));
                    $staff_status = '';
                    if ($query_staff['deleted'] == '1') {
                        $staff_status = ' (Archived)';
                    } else if ($query_staff['status'] == '0') {
                        $staff_status = ' (Suspended)';
                    }
                    $selected = '';
                    $selected = $id == $contactid ? 'selected = "selected"' : '';
                    echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).$staff_status.'</option>';
                }
            ?>
        </select>
      </div>
    </div>
    <?php } ?>

    <?php if (strpos($value_config, ','."Staff Documents Type".',') !== FALSE) { ?>
   <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Staff Document Type<span class="hp-red">*</span>:</label>
    <div class="col-sm-8">
        <select id="staff_documents_type" name="staff_documents_type" class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $query = mysqli_query($dbc,"SELECT distinct(staff_documents_type) FROM staff_documents order by staff_documents_type");
            while($row = mysqli_fetch_array($query)) {
                if ($staff_documents_type == $row['staff_documents_type']) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='". $row['staff_documents_type']."'>".$row['staff_documents_type'].'</option>';

            }
            echo "<option value = 'Other'>New Staff Documents</option>";
            ?>
        </select>
    </div>
  </div>

   <div class="form-group" id="new_staff_documents" style="display: none;">
    <label for="travel_task" class="col-sm-4 control-label">New Staff Document Type:
    </label>
    <div class="col-sm-8">
        <input name="new_staff_documents" type="text" class="form-control" />
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>

  <!-- <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Category<span class="hp-red">*</span>:</label>
    <div class="col-sm-8">
      <input name="category" value="<?php echo $category; ?>" type="text" id="name" class="form-control">
    </div>
  </div>
  -->

   <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Category<span class="hp-red">*</span>:</label>
    <div class="col-sm-8">
        <select id="category" name="category" class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $query = mysqli_query($dbc,"SELECT distinct(category) FROM staff_documents order by category");
            while($row = mysqli_fetch_array($query)) {
                if ($category == $row['category']) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='". $row['category']."'>".$row['category'].'</option>';

            }
            echo "<option value = 'Other'>New Category</option>";
            ?>
        </select>
    </div>
  </div>

   <div class="form-group" id="new_category" style="display: none;">
    <label for="travel_task" class="col-sm-4 control-label">New Category:
    </label>
    <div class="col-sm-8">
        <input name="new_category" type="text" class="form-control" />
    </div>
  </div>

  <?php } ?>

   <?php if (strpos($value_config, ','."Title".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Title<span class="hp-red">*</span>:</label>
    <div class="col-sm-8">
      <input name="title" value="<?php echo $title; ?>" type="text" id="title" class="form-control">
    </div>
  </div>
  <?php } ?>

<?php if (strpos($value_config, ','."Uploader".',') !== FALSE) {
?>

<div class="form-group">
    <label for="additional_note" class="col-sm-4 control-label">
        <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span>
        Upload Document(s):
    </label>
    <div class="col-sm-8">
        <?php
        if(!empty($_GET['edit'])) {
            $query_check_credentials = "SELECT * FROM staff_documents_uploads WHERE staff_documentsid='$staff_documentsid' AND type = 'Document' ORDER BY certuploadid DESC";
            $result = mysqli_query($dbc, $query_check_credentials);
            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                while($row = mysqli_fetch_array($result)) {
                    $certuploadid = $row['certuploadid'];
                    echo '<ul>';
                    if(file_get_contents('../Staff Documents/download/'.$row['document_link'])) {
                        $download_link = '../Staff Documents/download/'.$row['document_link'];
                    } else {
                        $download_link = 'download/'.$row['document_link'];
                    }
                    echo '<li><a href="'.$download_link.'" target="_blank">'.$row['document_link_name'].' : '.$row['document_link'].'</a> - <a href="?tile_name='.$tile_name.'&tab='.$_GET['tab'].'&certuploadid='.$certuploadid.'&edit='.$staff_documentsid.'"> Delete</a></li>';
                    echo '</ul>';
                }
            }
        }
        ?>

        <div class="form-group clearfix">
            <div class="col-sm-5">
                Attchment
            </div>
            <div class="col-sm-5">
                Name
            </div>
        </div>

        <div class="enter_cost additional_doc clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix">
                <div class="col-sm-5">
                    <input name="upload_document[]" type="file" data-filename-placement="inside" class="form-control" />
                </div>
                <div class="col-sm-5">
                    <input name="document_link_name[]" type="text" class="form-control" />
                </div>
            </div>

        </div>

        <div id="add_here_new_doc"></div>

        <div class="form-group triple-gapped clearfix">
            <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to add documents."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <button id="add_row_doc" class="btn brand-btn">Add Another Document</button>
        </div>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Link".',') !== FALSE) {
?>
<div class="form-group">
    <label for="additional_note" class="col-sm-4 control-label">Link(s):<br><em>(e.g. - http://www.google.com)</em>
    </label>
    <div class="col-sm-8">
        <?php
        if(!empty($_GET['edit'])) {
            $query_check_credentials = "SELECT * FROM staff_documents_uploads WHERE staff_documentsid='$staff_documentsid' AND type = 'Link' ORDER BY certuploadid DESC";
            $result = mysqli_query($dbc, $query_check_credentials);
            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                $link_no = 1;
                while($row = mysqli_fetch_array($result)) {
                    $certuploadid = $row['certuploadid'];
                    echo '<ul>';
                    echo '<li><a target="_blank" href=\''.$row['document_link'].'\'">'.$row['document_link_name'] .' : Link  '.$link_no.'</a> - <a href="?tile_name='.$tile_name.'&tab='.$_GET['tab'].'&certuploadid='.$certuploadid.'&edit='.$staff_documentsid.'"> Delete</a></li>';
                    echo '</ul>';
                    $link_no++;
                }
            }
        }
        ?>

        <div class="form-group clearfix">
            <div class="col-sm-5">
                Link
            </div>
            <div class="col-sm-5">
                Name
            </div>
        </div>
        <div class="enter_cost additional_link clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix">
                <div class="col-sm-5">
                    <input name="support_link[]" type="text" placeholder="Full URL including http:// or https://" class="form-control">
                </div>
                <div class="col-sm-5">
                    <input name="support_link_name[]" type="text" class="form-control">
                </div>
            </div>

        </div>

        <div id="add_here_new_link"></div>

        <div class="form-group triple-gapped clearfix">
            <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_link" class="btn brand-btn pull-left">Add More Links</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>

   <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Heading:</label>
    <div class="col-sm-8">
      <input name="heading" value="<?php echo $heading; ?>" type="text" id="name" class="form-control">
    </div>
  </div>
  <?php } ?>

   <?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Name<span class="hp-red">*</span>:</label>
    <div class="col-sm-8">
      <input name="name" value="<?php echo $name; ?>" type="text" id="name" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Staff Documents Code".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Staff Documents Code:</label>
    <div class="col-sm-8">
      <input name="staff_documents_code" value="<?php echo $staff_documents_code; ?>" type="text" id="name" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Description".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="first_name[]" class="col-sm-4 control-label">Description:</label>
    <div class="col-sm-8">
      <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="first_name[]" class="col-sm-4 control-label"></label>
    <div class="col-sm-8">
      <input type="checkbox" value="1" name="same_desc">Check this if Quote Description is same as Description.
    </div>
  </div>

  <div class="form-group">
    <label for="first_name[]" class="col-sm-4 control-label">Quote Description:</label>
    <div class="col-sm-8">
      <textarea name="quote_description" rows="5" cols="50" class="form-control"><?php echo $quote_description; ?></textarea>
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Invoice Description:</label>
    <div class="col-sm-8">
      <textarea name="invoice_description" rows="5" cols="50" class="form-control"><?php echo $invoice_description; ?></textarea>
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label"><?= TICKET_NOUN ?> Description:</label>
    <div class="col-sm-8">
      <textarea name="ticket_description" rows="5" cols="50" class="form-control"><?php echo $ticket_description; ?></textarea>
    </div>
  </div>
  <?php } ?>

   <?php if (strpos($value_config, ','."Fee".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Fee<span class="hp-red">*</span>:</label>
    <div class="col-sm-8">
      <input name="fee" value="<?php echo $fee; ?>" type="text" id="name" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Cost:</label>
    <div class="col-sm-8">
      <input name="cost" value="<?php echo $cost; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Final Retail Price:</label>
    <div class="col-sm-8">
      <input name="final_retail_price" value="<?php echo $final_retail_price; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

    <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Admin Price:</label>
    <div class="col-sm-8">
      <input name="admin_price" value="<?php echo $admin_price; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Wholesale Price:</label>
    <div class="col-sm-8">
      <input name="wholesale_price" value="<?php echo $wholesale_price; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Commercial Price:</label>
    <div class="col-sm-8">
      <input name="commercial_price" value="<?php echo $commercial_price; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Staff Price".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Staff Price:</label>
    <div class="col-sm-8">
      <input name="staff_price" value="<?php echo $staff_price; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Minimum Billable:</label>
    <div class="col-sm-8">
      <input name="minimum_billable" value="<?php echo $minimum_billable; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Estimated Hours:</label>
    <div class="col-sm-8">
      <input name="estimated_hours" value="<?php echo $estimated_hours; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Actual Hours:</label>
    <div class="col-sm-8">
      <input name="actual_hours" value="<?php echo $actual_hours; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">MSRP:</label>
    <div class="col-sm-8">
      <input name="msrp" value="<?php echo $msrp; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

<?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Unit Price:</label>
<div class="col-sm-8">
  <input name="unit_price" value="<?php echo $unit_price; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Unit Cost".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Unit Cost:</label>
<div class="col-sm-8">
  <input name="unit_cost" value="<?php echo $unit_cost; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Rent Price".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Rent Price:</label>
<div class="col-sm-8">
  <input name="rent_price" value="<?php echo $rent_price; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>


<?php if (strpos($value_config, ','."Rental Days".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Rental Days:</label>
<div class="col-sm-8">
  <input name="rental_days" value="<?php echo $rental_days; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Rental Weeks:</label>
<div class="col-sm-8">
  <input name="rental_weeks" value="<?php echo $rental_weeks; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Rental Months".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Rental Months:</label>
<div class="col-sm-8">
  <input name="rental_months" value="<?php echo $rental_months; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Rental Years".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Rental Years:</label>
<div class="col-sm-8">
  <input name="rental_years" value="<?php echo $rental_years; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Reminder/Alert:</label>
<div class="col-sm-8">
  <input name="reminder_alert" value="<?php echo $reminder_alert; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>


<?php if (strpos($value_config, ','."Daily".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Daily:</label>
<div class="col-sm-8">
  <input name="daily" value="<?php echo $daily; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Weekly".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Weekly:</label>
<div class="col-sm-8">
  <input name="weekly" value="<?php echo $weekly; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Monthly".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Monthly:</label>
<div class="col-sm-8">
  <input name="monthly" value="<?php echo $monthly; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Annually".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Annually:</label>
<div class="col-sm-8">
  <input name="annually" value="<?php echo $annually; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."#Of Days".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">#Of Days:</label>
<div class="col-sm-8">
  <input name="total_days" value="<?php echo $total_days; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."#Of Hours".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">#Of Hours:</label>
<div class="col-sm-8">
  <input name="total_hours" value="<?php echo $total_hours; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">#Of Kilometers:</label>
<div class="col-sm-8">
  <input name="total_km" value="<?php echo $total_km; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."#Of Miles".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">#Of Miles:</label>
<div class="col-sm-8">
  <input name="total_miles" value="<?php echo $total_miles; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

    <div class="form-group">
        <p><span class="hp-red"><em>Required Fields *</em></span></p>
    </div>

    <div class="form-group">
        <div class="col-sm-6">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking here will not save your entry."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a href="?tile_name=<?= $tile_name ?>&tab=<?= $_GET['tab'] ?>" class="btn brand-btn btn-lg">Back</a>
            <!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
        </div>
        <div class="col-sm-6">
            <button type="submit" name="add_staff_documents" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
            <span class="popover-examples list-inline pull-right" style="margin:15px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add this entry."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        </div>
    </div>



</form>