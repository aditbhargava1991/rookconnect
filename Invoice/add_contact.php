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
$purchaser = count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0];?>
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
        
        if ( !empty($name) ) {
            $get_businessid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE name='$name'"));
            if ( !empty($get_businessid['contactid']) ) {
                $businessid = $get_businessid['contactid'];
            } else {
                mysqli_query($dbc, "INSERT INTO contacts (category, name) VALUES ('Business', '$name')");
                $businessid = mysqli_insert_id($dbc);
            }
        }
        
        mysqli_query($dbc, "INSERT INTO contacts (category, businessid, first_name, last_name, office_phone, email_address, mailing_address, business_address, city, postal_code, zip_code, province, state) VALUES ('{$purchaser_config[0]}', '$businessid', '$first_name', '$last_name', '$phone', '$email', '$address', '$address', '$city', '$postal_code', '$postal_code', '$province', '$province')");
        $contactid = mysqli_insert_id($dbc);
        
        echo '<script>window.top.location.href="add_invoice.php?contactid='.$contactid.'&type='.$_POST['type'].'";</script>';
    } ?>
	
    <h3>Add <?= $purchaser ?></h3>
    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        <input type="hidden" name="type" value="<?= $_GET['type'] ?>">
        <div class="row">
            <div class="col-sm-4">Business</div>
            <div class="col-sm-8">
                <select name="businessid" id="businessid" data-placeholder="Select a Business..." class="form-control chosen-select-deselect">
                    <option></option>
                    <option value="NEW">Add New</option><?php
                    $query = mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE category='Business' AND deleted=0 ORDER BY category");
                    while ( $row=mysqli_fetch_array($query) ) {
                        echo '<option value="'. $row['contactid'] .'">'. decryptIt($row['name']) .'</option>';
                    } ?>
                </select>
            </div>
        </div>
        <div class="row business">
            <div class="col-sm-4">Business Name</div>
            <div class="col-sm-8"><input type="text" name="name" value="" class="form-control"/></div>
        </div>
        <div class="row">
            <div class="col-sm-4">First Name</div>
            <div class="col-sm-8"><input type="text" name="first_name" value="" class="form-control" /></div>
        </div>
        <div class="row">
            <div class="col-sm-4">Last Name</div>
            <div class="col-sm-8"><input type="text" name="last_name" value="" class="form-control" /></div>
        </div>
        <div class="row">
            <div class="col-sm-4">Phone Number</div>
            <div class="col-sm-8"><input type="tel" name="phone" value="" class="form-control" /></div>
        </div>
        <div class="row">
            <div class="col-sm-4">Email Address</div>
            <div class="col-sm-8"><input type="email" name="email" value="" class="form-control" /></div>
        </div>
        <div class="row">
            <div class="col-sm-4">Address</div>
            <div class="col-sm-8"><input type="text" name="address" value="" class="form-control" /></div>
        </div>
        <div class="row">
            <div class="col-sm-4">City</div>
            <div class="col-sm-8"><input type="text" name="city" value="" class="form-control" /></div>
        </div>
        <div class="row">
            <div class="col-sm-4">Province/State</div>
            <div class="col-sm-8"><input type="text" name="province" value="" class="form-control" /></div>
        </div>
        <div class="row">
            <div class="col-sm-4">Postal/ZIP Code</div>
            <div class="col-sm-8"><input type="text" name="postal_code" value="" class="form-control" /></div>
        </div>
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