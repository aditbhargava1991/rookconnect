<?php
/*
Inventory Listing
*/
include ('../include.php');
error_reporting(0);
$security = get_security($dbc, 'checklist'); ?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.has-main-screen .main-screen').outerHeight($(window).height() - $('.has-main-screen').offset().top - $('footer').outerHeight());
		$('.sidebar').outerHeight($(window).height() - $('.sidebar ul').offset().top - $('footer').outerHeight());
	}).resize();
});

</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('checklist');
?>



<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

        <div class="col-sm-10">
			<h1>Checklist Dashboard</h1>
		</div>

            <div class="ffmbtnpic dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12" data-search-term="admin settings">
                <a href="checklist.php">Checklist</a>
            </div>
            <div class="clearfix"></div>
            <div class="clearfix"></div>
            <?php
                    $get_checklist = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(checklistid) AS checklistid FROM checklist WHERE checklist_tile=1"));

                    $subtab_list = mysqli_query($dbc, "SELECT checklistid, checklist_type, checklist_name FROM `checklist` WHERE (`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `assign_staff`=',ALL,') AND `deleted`=0 AND checklist_tile=1");
                    echo "<ul class='option-list'>";
                    while($checklist = mysqli_fetch_array($subtab_list)) {
            ?>
            <div class="ffmbtnpic dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12" data-search-term="admin settings">
                <a href="<?php echo 'checklist.php?subtabid='.$checklist['checklist_type'].'&view='.$checklist['checklistid']; ?>"><?php echo $checklist['checklist_name']; ?></a>
            </div>
            <?php } ?>

		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>
