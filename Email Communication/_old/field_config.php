<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('email_communication');
error_reporting(0);
?>
<script type="text/javascript">
$(document).ready(function() {
});
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1>Email Communication</h1>
<!--<div class="pad-left gap-top double-gap-bottom"><a href="email_communication.php?maintye=comm&type=Internal" class="btn config-btn">Back to Dashboard</a></div>-->
<div class="pad-left gap-top double-gap-bottom"><a href="index.php" class="btn config-btn">Back to Dashboard</a></div>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<div class="panel-group" id="accordion2">
		<?php include('field_config_communication.php'); ?>
	</div>

    <div class="form-group">
        <div class="col-sm-6">
            <!-- <a href="email_communication.php?type=Internal" class="btn config-btn btn-lg">Back</a> -->
            <a href="index.php" class="btn config-btn btn-lg">Back</a>
        </div>
        <div class="col-sm-6">
            <button	type="submit" name="service_record_btn" value="service_record_btn" class="btn config-btn btn-lg	pull-right">Submit</button>
        </div>
    </div>

</form>
</div>
</div>

<?php if (isset($_POST['service_record_btn'])) {
    echo "<script>window.location.replace('email_communication.php?type=Internal');</script>";
} ?>
<?php include ('../footer.php'); ?>