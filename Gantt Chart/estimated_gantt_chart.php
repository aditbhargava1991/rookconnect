<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('gantt_chart');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
?>
<script>
    function submitForm(thisForm) {
        if (!$('input[name="search_user_submit"]').length) {
            var input = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "search_user_submit").val("1");
            $('[name=form1]').append($(input));
        }

        $('[name=form1]').submit();
    }
    $(document).on('change', 'select[name="search_client"]', function() { submitForm(); });
</script>
<script type="text/javascript">
function startDate(sel) {
	var action = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "estimated_gantt_chart_ajax_all.php?fill=gantt_startdate&id="+arr[1]+'&start_date='+action,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
function endDate(sel) {
	var action = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "estimated_gantt_chart_ajax_all.php?fill=gantt_enddate&id="+arr[1]+'&end_date='+action,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
</script>
<style>

</style>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
	<div class="row">
	    <h1>Gantt Chart Dashboard</h1>
		<div class="tab-container mobile-100-container padded">
			<?php if ( check_subtab_persmission($dbc, 'gantt_chart', ROLE, 'estimated') === TRUE ) { ?>
				<a href="estimated_gantt_chart.php"><button type="button" class="btn mobile-100 brand-btn mobile-block active_tab">Estimated</button></a>&nbsp;&nbsp;
			<?php } else { ?>
				<button type="button" class="btn mobile-100 disabled-btn mobile-block">Estimated</button>
			<?php } ?>

			<?php if ( check_subtab_persmission($dbc, 'gantt_chart', ROLE, 'chart') === TRUE ) { ?>
				<a href="draw_gantt_chart.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Gantt Chart</button></a>&nbsp;&nbsp;
			<?php } else { ?>
				<button type="button" class="btn mobile-100 disabled-btn mobile-block">Gantt Chart</button>
			<?php }

			if ( vuaed_visible_function($dbc, 'gantt_chart') == 1 ) {
				echo '<a href="add_estimated_gantt_chart.php" class="btn brand-btn mobile-block pull-right mobile-100-pull-right">Add Estimated Gantt Chart Information</a>';
			} ?>
		</div>

		<div class="clearfix"></div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        <?php
            $search_client = '';
            if(isset($_POST['search_user_submit'])) {
                $search_client = $_POST['search_client'];
            }
			if (isset($_POST['display_all_inventory'])) {
				$search_client = '';
			}
        ?>

        <div class="form-group">
			<label for="site_name" class="col-sm-2 control-label">Search By Business:</label>
			<div class="col-sm-8" style="width:auto">
				<select data-placeholder="Select a Business" name="search_client" class="chosen-select-deselect form-control">
					<option value=""></option><?php
					$query = mysqli_query($dbc,"SELECT c.contactid,c.name AS client_name FROM contacts c, estimated_gantt_chart t WHERE t.businessid=c.contactid");
					while($row = mysqli_fetch_array($query)) { ?>
						<option <?php if ($row['contactid'] == $search_client) { echo " selected"; } ?> value='<?php echo  $row['contactid']; ?>' ><?php echo decryptIt($row['client_name']); ?></option><?php
					} ?>
				</select>
			</div>
				<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
				<!--<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>-->
		</div>

		<div class="clearfix"></div>

        <?php

        /* Pagination Counting */
        $rowsPerPage = 25;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        if($search_client != '') {
            $query_check_credentials = "SELECT t.*, c.name FROM estimated_gantt_chart t, contacts c WHERE t.businessid = c.contactid AND t.businessid = '$search_client' ORDER BY estimatedganttchartid DESC LIMIT $offset, $rowsPerPage";
            $query = "SELECT count(c.name) as numrows FROM estimated_gantt_chart t, contacts c WHERE t.businessid = c.contactid AND t.businessid = '$search_client' ORDER BY estimatedganttchartid DESC";
        } else {
            $query_check_credentials = "SELECT t.*, c.name FROM estimated_gantt_chart t, contacts c WHERE t.businessid = c.contactid ORDER BY estimatedganttchartid DESC LIMIT $offset, $rowsPerPage";
            $query = "SELECT count(c.name) as numrows FROM estimated_gantt_chart t, contacts c WHERE t.businessid = c.contactid ORDER BY estimatedganttchartid DESC";
        }
        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            // Added Pagination //
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            // Pagination Finish //

            echo '<div id="no-more-tables"><table class="table table-bordered table-striped">';
            echo '<thead><tr class="hidden-xs hidden-sm">
                <th>Business</th>
                <th>Service</th>
                <th>Heading</th>
                <th>Phase</th>
                <th>Start Date</th>
                <th>End Date</th>
                </tr></thead>';
        } else {
            echo "<h2>No Record Found.</h2>";
        }
        while($row = mysqli_fetch_array( $result )) {
            echo '<tr>';
            $estimatedganttchartid = $row['estimatedganttchartid'];

            echo '<td data-title="Business">' . get_contact($dbc, $row['businessid'], 'name') . '</td>';

            echo '<td data-title="Service">' . $row['service_type'].'<br>'.$row['service'] .'<br>'.$row['sub_heading'] . '</td>';
            echo '<td data-title="Heading">' . $row['heading'] . '</td>';
            echo '<td data-title="Phase">' . $row['phase'] . '</td>';

            echo '<td data-title="Start Date"><input name="start_date" type="text" id="startdate_'.$row['estimatedganttchartid'].'"  onchange="startDate(this)" class="datepicker" value="'.$row['start_date'].'"></td>';

            echo '<td data-title="End Date"><input name="end_date" type="text" id="enddate_'.$row['estimatedganttchartid'].'"  onchange="endDate(this)" class="datepicker" value="'.$row['end_date'].'"></td>';

            echo "</tr>";
        }

        echo '</table></div>';

        // Added Pagination //
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        // Pagination Finish //
        ?>

        </form>
	</div>
</div>

<?php include ('../footer.php'); ?>
