<?php
/*
 * Add New Contact Slide-In
 */
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
error_reporting(0);
$purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
$category = $purchaser_config[0]; ?>
</head>

<body>
<div class="container"><?php
    if ( isset($_POST['submit']) ) {
        $businessid = '';
        $businessid = $_POST['businessid'];
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
        $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
        $phone = $_POST['phone'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
        $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
        $province = filter_var($_POST['province'], FILTER_SANITIZE_STRING);
        $postal_code = filter_var($_POST['postal_code'], FILTER_SANITIZE_STRING);
        
        $name = encryptIt($name);
        $first_name = encryptIt($first_name);
        $last_name = encryptIt($last_name);
        $phone = encryptIt($phone);
        $email = encryptIt($email);
        
        mysqli_query($dbc, "INSERT INTO contacts (category, businessid, name, first_name, last_name, office_phone, email_address, mailing_address, business_address, city, postal_code, zip_code, province, state) VALUES ('$category', '$businessid', '$name', '$first_name', '$last_name', '$phone', '$email', '$address', '$address', '$city', '$postal_code', '$postal_code', '$province', '$province')");
        $contactid = mysqli_insert_id($dbc);
        mysqli_query($dbc, "UPDATE `match_contact` SET `support_contact`=CONCAT(IFNULL(CONCAT(`support_contact`,','),''),'$contactid') WHERE CONCAT(',',`staff_contact`,',') LIKE '%,".$_SESSION['contactid'].",%' AND `deleted` = 0 AND `match_date` <= '$today_date' AND (IFNULL(`tile_list`,'')='' OR `tile_list` LIKE '%".FOLDER_NAME."%')"); ?>
        
        <script>
        $(window.top.document).find('select[name=patientid]').append('<option value="<?= $contactid ?>"><?= $_POST['first_name'].' '.$_POST['last_name'] ?></option>').val(<?= $contactid ?>).change();
        </script>
        echo '<script>window.top.location.href="create_invoice.php?contactid='.$contactid.'&type='.$_POST['type'].'";</script>';
        <?php // echo '<script>window.top.location.href="create_invoice.php?contactid='.$contactid.'&type='.$_POST['type'].'";</script>';
    }
    $field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='$category' AND `subtab` = '**no_subtab**'"))[0] . ',' . mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='$category' AND `subtab` = 'additions'"))[0]); ?>
	
    <h3>Add <?= $category ?></h3>
    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        <input type="hidden" name="type" value="<?= $_GET['type'] ?>">
        <?php if(in_array('Business', $field_config)) { ?>
            <div class="row">
                <div class="col-sm-4"><?= BUSINESS_CAT ?></div>
                <div class="col-sm-8">
                    <select name="businessid" id="businessid" data-placeholder="Select a Business..." class="form-control chosen-select-deselect">
                        <option></option><?php
                        $query = mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE category='Business' AND deleted=0 ORDER BY category");
                        while ( $row=mysqli_fetch_array($query) ) {
                            echo '<option value="'. $row['contactid'] .'">'. decryptIt($row['name']) .'</option>';
                        } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <?php if(in_array('Name', $field_config)) { ?>
            <div class="row business">
                <div class="col-sm-4"><?= $category ?> Name</div>
                <div class="col-sm-8"><input type="text" name="name" value="" class="form-control"/></div>
            </div>
        <?php } ?>
        <?php if(in_array('First Name', $field_config)) { ?>
            <div class="row">
                <div class="col-sm-4">First Name</div>
                <div class="col-sm-8"><input type="text" name="first_name" value="" class="form-control" /></div>
            </div>
        <?php } ?>
        <?php if(in_array('Last Name', $field_config)) { ?>
            <div class="row">
                <div class="col-sm-4">Last Name</div>
                <div class="col-sm-8"><input type="text" name="last_name" value="" class="form-control" /></div>
            </div>
        <?php } ?>
        <?php if(in_array_any(['Office Phone','Profile Office Phone'], $field_config)) { ?>
            <div class="row">
                <div class="col-sm-4">Phone Number</div>
                <div class="col-sm-8"><input type="tel" name="phone" value="" class="form-control" /></div>
            </div>
        <?php } ?>
        <?php if(in_array_any(['Email Address','Profile Email Address'], $field_config)) { ?>
            <div class="row">
                <div class="col-sm-4">Email Address</div>
                <div class="col-sm-8"><input type="email" name="email" value="" class="form-control" /></div>
            </div>
        <?php } ?>
        <?php if(in_array_any(['Mailing Full Address','Full Address','Business Full Address'], $field_config)) { ?>
            <div class="row">
                <div class="col-sm-4">Address</div>
                <div class="col-sm-8"><input type="text" name="address" value="" class="form-control" /></div>
            </div>
        <?php } ?>
        <?php if(in_array('City', $field_config)) { ?>
            <div class="row">
                <div class="col-sm-4">City</div>
                <div class="col-sm-8"><input type="text" name="city" value="" class="form-control" /></div>
            </div>
        <?php } ?>
        <?php if(in_array_any(['Province','State'], $field_config)) { ?>
            <div class="row">
                <div class="col-sm-4">Province/State</div>
                <div class="col-sm-8"><input type="text" name="province" value="" class="form-control" /></div>
            </div>
        <?php } ?>
        <?php if(in_array_any(['Postal Code','Zip Code'], $field_config)) { ?>
            <div class="row">
                <div class="col-sm-4">Postal/ZIP Code</div>
                <div class="col-sm-8"><input type="text" name="postal_code" value="" class="form-control" /></div>
            </div>
        <?php } ?>
        <div class="row">
            <button name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
            <a href="" class="btn brand-btn pull-right">Cancel</a>
        </div>
    </form>
</div><!-- .container -->

<script>
    $(document).ready(function(){
        $('.business').hide();
        $('#businessid').change(function() {
            if ($(this).val()=='NEW') {
                $('.business').show();
            } else {
                $('.business').hide();
            }
        });
    });
</script>
<?php include ('../footer.php'); ?>