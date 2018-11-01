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
            $profit_loss = '';
            $budget = '';
            $type = $_GET['type'];
            $projectid = $_GET['projectid'];
            if($_GET['type'] == 'profit_loss') {
                $profit_loss = 'active_tab';
            }
            if($_GET['type'] == 'budget') {
                $budget = 'active_tab';
            }
        ?>
        <a href='edit_project.php?projectid=<?php echo $projectid;?>&type=profit_loss'><button type="button" class="btn brand-btn mobile-block <?php echo $profit_loss; ?>" >By Heading</button></a>&nbsp;&nbsp;
        <a href='edit_project.php?projectid=<?php echo $projectid;?>&type=budget'><button type="button" class="btn brand-btn mobile-block <?php echo $budget; ?>" >By Service</button></a>&nbsp;&nbsp;

            <?php
			$project_all = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT *  FROM project WHERE projectid='$projectid'"));
            ?>
            <input type="hidden" name="projectid" id="projectid" value="<?php echo $projectid; ?>">

            <?php if($type == 'summary') { ?>
            <div class="form-group">
                <label for="additional_note" class="col-sm-4 control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?>:</label>
                <div class="col-sm-8">
                    <textarea name="project_data" rows="15" cols="50" class="form-control"><?php echo $project_all['project_data']; ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-4 clearfix">
                    <a href="project.php?type=Client" class="btn brand-btn pull-right">Back</a>
					<!--<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>-->
                </div>
                <div class="col-sm-8">
                    <!-- <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button> -->
                </div>
            </div>
            <?php } ?>

            <?php if($type == 'profit_loss') {
               ?>
                <br>
                <a href="project.php?type=Client" class="btn brand-btn pull-right">Back</a>
				<!--<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>-->
				<div id='no-more-tables'>
                <table class="table table-bordered">
                    <tr class="hidden-xs hidden-sm">
                    <th>Type</th>
                    <th>Name</th>
                    <th>Cost</th>
                    <th>Price</th>
                    <th>Profit/Loss</th>
                    </tr>
                    <?php echo $project_all['review_profit_loss']; ?>
                    <tr>
                    <td data-title='Type'>Total <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</td>
                    <td data-title='Name'></td>
                    <td data-title='Cost'>$<?php echo $project_all['financial_cost']; ?></td>
                    <td data-title='Price'>$<?php echo $project_all['financial_price']; ?></td>
                    <td data-title='Profit/Loss'>$<?php echo $project_all['financial_plus_minus']; ?></td>
                    </tr>
                </table>
				</div>
                <a href="project.php?type=Client" class="btn brand-btn pull-right">Back</a>
            <?php } ?>

            <?php if($type == 'budget') { ?>
            <br>
              <a href="project.php?type=Client" class="btn brand-btn pull-right">Back</a>
              <?php
               $budget_price = explode('*#*', $project_all['budget_price']);
               ?><div id='no-more-tables'>
                <table class="table table-bordered">
                    <tr class="hidden-xs hidden-sm">
                    <th>Type</th>
                    <th>Budget Price</th>
                    <th><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</th>
                    </tr>
                    <?php echo $project_all['review_budget']; ?>
                    <tr>
                    <td data-title='Type'>Total</td>
                    <td data-title='Budget Price'>$<?php echo $budget_price[16]; ?></td>
                    <td data-title='<?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price'>$<?php echo $project_all['total_price']; ?></td>
                    </tr>
                </table></div>
                <a href="project.php?type=Client" class="btn brand-btn pull-right">Back</a>
            <?php } ?>
        </form>

	</div>
</div>

<?php include_once('../footer.php'); ?>
