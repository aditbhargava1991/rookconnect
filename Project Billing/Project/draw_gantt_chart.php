<?php
/*
Dashboard
*/
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
require('../gantti-master/lib/gantti.php');
error_reporting(0);
?>
<script type="text/javascript">
</script>
<style>

</style>
<script>
$(document).ready(function() {
});
</script>
</head>
<body>

<?php include_once('../navigation.php'); ?>

<div class="container">
	<div class="row hide_on_iframe">
	    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <a href='estimated_gantt_chart.php'><button type='button' class='btn brand-btn mobile-block' >Estimated</button></a>&nbsp;&nbsp;
        <a href='draw_gantt_chart.php'><button type='button' class='btn brand-btn mobile-block active_tab' >Gantt Chart</button></a>&nbsp;&nbsp;

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
		  <label for="site_name" class="col-sm-4 control-label">Search By Business:</label>
		  <div class="col-sm-8">
              <select data-placeholder="Pick a Client" name="search_client" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
              <option value=""></option>
              <?php
                $query = mysqli_query($dbc,"SELECT c.contactid,c.name AS client_name FROM contacts c, estimated_gantt_chart t WHERE t.businessid=c.contactid");
                while($row = mysqli_fetch_array($query)) {
                ?><option <?php if ($row['contactid'] == $search_client) { echo " selected"; } ?> value='<?php echo  $row['contactid']; ?>' ><?php echo $row['client_name']; ?></option>
            <?php	} ?>
            </select>
		  </div>
		</div>

        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label"></label>
		  <div class="col-sm-8">
            <button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
			<!-- <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button> -->
		  </div>
		</div>

        <?php
        echo '<img src="'.WEBSITE_URL.'/img/block/dark_yellow.png" width="10" height="10" border="0" alt="">&nbsp;Sales/Estimate/RFP&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/brown.png" width="10" height="10" border="0" alt="">&nbsp;Strategy Needed&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/lime.png" width="10" height="10" border="0" alt="">&nbsp;Last Minute Priority&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/olive.png" width="10" height="10" border="0" alt="">&nbsp;Information Gathering&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/orange.png" width="10" height="10" border="0" alt="">&nbsp;To Be Scheduled&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/pink.png" width="10" height="10" border="0" alt="">&nbsp;Scheduled/To Do&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/purple.png" width="10" height="10" border="0" alt="">&nbsp;Doing Today&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/00eeee.png" width="10" height="10" border="0" alt="">&nbsp;Internal QA&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/bf3eff.png" width="10" height="10" border="0" alt="">&nbsp;Customer QA&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/ee9572.png" width="10" height="10" border="0" alt="">&nbsp;Waiting On Customer&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/yellow.png" width="10" height="10" border="0" alt="">&nbsp;Done&nbsp;&nbsp;';
        echo '<img src="'.WEBSITE_URL.'/img/block/white.png" width="10" height="10" border="0" alt="">&nbsp;Archive&nbsp;&nbsp;';
        echo '<br><br>';

        $data = array();

        if($search_client != '') {
            $query_check_credentials = "SELECT t.*, c.name FROM estimated_gantt_chart t, contacts c WHERE t.businessid = c.contactid AND t.businessid = '$search_client' ORDER BY estimatedganttchartid DESC";
        }

        $result = mysqli_query($dbc, $query_check_credentials);
        while($row = mysqli_fetch_array( $result )) {
            $businessid = $row['businessid'];

            $get_tt = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT start_date,estimated_completed_date,completion_date FROM project WHERE businessid='$businessid'"));

            $data[] = array(
              'label' => 'Estimated',
              'start' => $row['start_date'],
              'end'   => $row['end_date'],
              'class' => 'important',
            );

            $data[] = array(
              'label' => 'Project',
              'start' => $get_tt['start_date'],
              'end'   => $get_tt['estimated_completed_date'],
              'class' => 'current',
            );

            $result_ticket = mysqli_query($dbc, "SELECT * FROM tickets WHERE businessid = '$businessid'");
            while($row_ticket = mysqli_fetch_array( $result_ticket )) {
                if($row_ticket['to_do_date'] != '0000-00-00') {
                    $class = 'current';
                    if($row_ticket['status'] == 'Sales/Estimate/RFP') {
                        $class = 'class1';
                    }
                    if($row_ticket['status'] == 'Strategy Needed') {
                        $class = 'class2';
                    }
                    if($row_ticket['status'] == 'Last Minute Priority') {
                        $class = 'class3';
                    }
                    if($row_ticket['status'] == 'Information Gathering') {
                        $class = 'class4';
                    }
                    if($row_ticket['status'] == 'To Be Scheduled') {
                        $class = 'class5';
                    }
                    if($row_ticket['status'] == 'Scheduled/To Do') {
                        $class = 'class6';
                    }
                    if($row_ticket['status'] == 'Doing Today') {
                        $class = 'class7';
                    }
                    if($row_ticket['status'] == 'Internal QA') {
                        $class = 'class8';
                    }
                    if($row_ticket['status'] == 'Customer QA') {
                        $class = 'class9';
                    }
                    if($row_ticket['status'] == 'Waiting On Customer') {
                        $class = 'class10';
                    }
                    if($row_ticket['status'] == 'Done') {
                        $class = 'class11';
                    }
                    if($row_ticket['status'] == 'Archive') {
                        $class = 'class12';
                    }

                    $contactid = get_multiple_contact($dbc, $row_ticket['contactid']);
                    $data[] = array(
						'label' => '<a id="'.$row_ticket['ticketid'].'" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row_ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" >#'.$row_ticket['ticketid'].'</a>',
                      'start' => $row_ticket['to_do_date'],
                      'end'   => date('Y-m-d', strtotime($row_ticket['to_do_end_date'] . ' +1 day')),
                      'class' => $class,
                    );
                }
            }

            $result_ticket = mysqli_query($dbc, "SELECT * FROM workorder WHERE clientid = '$businessid'");
            while($row_ticket = mysqli_fetch_array( $result_ticket )) {
                if($row_ticket['to_do_date'] != '0000-00-00') {
                    $class = 'current';
                    if($row_ticket['status'] == 'Sales/Estimate/RFP') {
                        $class = 'class1';
                    }
                    if($row_ticket['status'] == 'Strategy Needed') {
                        $class = 'class2';
                    }
                    if($row_ticket['status'] == 'Last Minute Priority') {
                        $class = 'class3';
                    }
                    if($row_ticket['status'] == 'Information Gathering') {
                        $class = 'class4';
                    }
                    if($row_ticket['status'] == 'To Be Scheduled') {
                        $class = 'class5';
                    }
                    if($row_ticket['status'] == 'Scheduled/To Do') {
                        $class = 'class6';
                    }
                    if($row_ticket['status'] == 'Doing Today') {
                        $class = 'class7';
                    }
                    if($row_ticket['status'] == 'Internal QA') {
                        $class = 'class8';
                    }
                    if($row_ticket['status'] == 'Customer QA') {
                        $class = 'class9';
                    }
                    if($row_ticket['status'] == 'Waiting On Customer') {
                        $class = 'class10';
                    }
                    if($row_ticket['status'] == 'Done') {
                        $class = 'class11';
                    }
                    if($row_ticket['status'] == 'Archive') {
                        $class = 'class12';
                    }

                    $contactid = get_multiple_contact($dbc, $row_ticket['contactid']);
                    $data[] = array(
                      'label' => '<a id="'.$row_ticket['workorderid'].'" href="'.WEBSITE_URL.'/Work Order/add_workorder.php?workorderid='.$row_ticket['workorderid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" >#'.$row_ticket['workorderid'].'</a>',
                      'start' => $row_ticket['to_do_date'],
                      'end'   => date('Y-m-d', strtotime($row_ticket['to_do_date'] . ' +1 day')),
                      'class' => $class,
                    );
                }
            }

        }

        $gantti = new Gantti($data, array(
          'title'      => get_contact($dbc, $businessid, 'name'),
          'cellwidth'  => 25,
          'cellheight' => 35,
          'today'      => true
        ));
        echo $gantti;

        ?>

        </form>
	</div>
</div>

<?php include_once('../footer.php'); ?>
