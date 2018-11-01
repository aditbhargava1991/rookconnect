<?php
/*
Dashboard
*/
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['submit'])) {
    $projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
    $qd = htmlentities($_POST['project_data']);
    $project_data = filter_var($qd,FILTER_SANITIZE_STRING);

    $query_update_report = "UPDATE `project` SET `project_data` = '$project_data' WHERE `projectid` = '$projectid'";
    $result_update_report = mysqli_query($dbc, $query_update_report);

    echo '<script type="text/javascript"> window.location.replace("project.php"); </script>';
}
?>
</head>
<body>

<?php include_once('../navigation.php'); ?>

<div class="container">
	<div class="row">
        <form id="form1" name="form1" method="post" action="edit_project.php" enctype="multipart/form-data" class="form-horizontal" role="form">

            <?php
            $projectid = $_GET['projectid'];
            $type = $_GET['type'];
			$project_all = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT *  FROM project WHERE projectid='$projectid'"));

            ?>
            <input type="hidden" name="projectid" id="projectid" value="<?php echo $projectid; ?>">

            <?php if($type == 'summary') { ?>
            <div class="form-group">
                <label for="additional_note" class="col-sm-4 control-label">Project:</label>
                <div class="col-sm-8">
                    <textarea name="project_data" rows="15" cols="50" class="form-control"><?php echo $project_all['project_data']; ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-4 clearfix">
                    <!--<a href="project.php?type=Pending" class="btn brand-btn pull-right">Back</a>-->
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-8">
                    <!-- <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button> -->
                </div>
            </div>
            <?php } ?>

            <?php if($type == 'profit_loss') {
               ?>
                <!--<a href="project.php?type=Pending" class="btn brand-btn pull-right">Back</a>-->
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
                <table class="table table-bordered">
                    <tr class="hidden-xs hidden-sm">
                    <th>Type</th>
                    <th>Name</th>
                    <th>Cost</th>
                    <th>Project Price</th>
                    <th>Profit/Loss</th>
                    </tr>
                    <?php echo $project_all['review_profit_loss']; ?>
                </table>
                <!--<a href="project.php?type=Pending" class="btn brand-btn pull-right">Back</a>-->
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
            <?php } ?>

            <?php if($type == 'budget') { ?>
              <!--<a href="project.php?type=Pending" class="btn brand-btn pull-right">Back</a>-->
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
              <?php
               $budget_price = explode('*#*', $project_all['budget_price']);
               echo '<h3>Total Budget : $'.$budget_price[16].'</h3><br>';
               echo '<h3>Total Project : $'.$project_all['total_price'].'</h3><br>';
               ?>
                <table class="table table-bordered">
                    <tr class="hidden-xs hidden-sm">
                    <th>Type</th>
                    <th>Budget Price</th>
                    <th>Project Price</th>
                    </tr>
                    <?php echo $project_all['review_budget']; ?>
                </table>
                <!--<a href="project.php?type=Pending" class="btn brand-btn pull-right">Back</a>-->
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
            <?php } ?>
        </form>

	</div>
</div>

<?php include_once('../footer.php'); ?>
