<?php
/*
Add Vendor
*/
include ('../include.php');
error_reporting(0);
$from_url = (!empty($_GET['from_url']) ? $_GET['from_url'] : 'day_program.php');

if(!empty($_GET['meduploadid'])) {
    $meduploadid = $_GET['meduploadid'];
    $query = mysqli_query($dbc,"DELETE FROM medication_uploads WHERE meduploadid='$meduploadid'");
    $dayprogramid = $_GET['dayprogramid'];

    echo '<script type="text/javascript"> window.location.replace("add_medication.php?dayprogramid='.$dayprogramid.'&from_url='.$from_url.'"); </script>';
}

if (isset($_POST['add_medication'])) {

    $support_contact_category = $_POST['support_contact_category'];
    $support_contact = $_POST['support_contact'];
    $timer = $_POST['timer'];
	$date = $_POST['timer_date'];
    $planned_activity = implode(',',$_POST['planned_activity']);
    $day_obj = filter_var(htmlentities($_POST['day_obj']),FILTER_SANITIZE_STRING);
    $completed_activity = implode(',',$_POST['completed_activity']);
    $day_notes = filter_var(htmlentities($_POST['day_notes']),FILTER_SANITIZE_STRING);
    $eme_contact = implode(',',$_POST['eme_contact']);

    if(empty($_POST['dayprogramid'])) {
        $query_insert_or_update = "INSERT INTO `day_program` (`support_contact_category`, `support_contact`, `timer`, `date`, `planned_activity`, `day_obj`, `completed_activity`, `day_notes`, `eme_contact`) VALUES ('$support_contact_category', '$support_contact', '$timer', '$date', '$planned_activity', '$day_obj', '$completed_activity', '$day_notes', '$eme_contact')";
        $url = 'Added';
    } else {
        $dayprogramid = $_POST['dayprogramid'];
        $query_insert_or_update = "UPDATE `day_program` SET `support_contact_category` = '$support_contact_category', `support_contact` = '$support_contact', `timer` = '$timer', `date`='$date', `planned_activity` = '$planned_activity', `day_obj` = '$day_obj', `completed_activity` = '$completed_activity', `day_notes` = '$day_notes', `eme_contact` = '$eme_contact' WHERE `dayprogramid` = '$dayprogramid'";
        $url = 'Updated';
    }
	$result = mysqli_query($dbc, $query_insert_or_update);

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    echo '<script type="text/javascript"> window.location.replace("'.$from_url.'"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
}

?>
<script type="text/javascript">
$(document).ready(function() {
	$('.complete-timer-btn').click(complete_timer);

    $("#isp_quality").change(function() {
        if($( "#isp_quality option:selected" ).text() == 'Other') {
                $( "#isp_quality_name" ).show();
        } else {
            $( "#isp_quality_name" ).hide();
        }
    });

    $("#isp_sis").change(function() {
        if($( "#isp_sis option:selected" ).text() == 'Other') {
                $( "#isp_sis_name" ).show();
        } else {
            $( "#isp_sis_name" ).hide();
        }
    });

    $("#isp_goals").change(function() {
        if($( "#isp_goals option:selected" ).text() == 'Other') {
                $( "#isp_goals_name" ).show();
        } else {
            $( "#isp_goals_name" ).hide();
        }
    });

    $("#form1").submit(function( event ) {
        var medication_type = $("#medication_type").val();
        var category = $("input[name=category]").val();
        var title = $("input[name=title]").val();
        if (medication_type == '' || category == '' || title == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });
});

function selectContactCategory(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "../Individual Support Plan/isp_ajax_all.php?fill=contact_category&category="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#contact_"+arr[2]).html(response);
			$("#contact_"+arr[2]).trigger("change.select2");
		}
	});
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('day_program');
?>
<div class="container">
	<div class="row hide_on_iframe">

    <h1>Day Program</h1>
	<div class="pad-left gap-top double-gap-bottom"><a href="<?= $from_url ?>" class="btn config-btn">Back to Dashboard</a></div>

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT day_program FROM field_config"));
        $value_config = ','.$get_field_config['day_program'].',';

        $support_contact_category = '';
        $support_contact = '';
        $timer = '';
        $date = '';
		$timer_started = '';
        $planned_activity = '';
        $day_obj = '';
        $completed_activity = '';
        $day_notes = '';
        $eme_contact = '';

        if($_GET['acc'] == 'day_program') {
            $acc_day_program = ' in';
        }
        if($_GET['acc'] == 'isp_detail') {
            $acc_isp_detail = ' in';
        }
        if($_GET['acc'] == 'isp_notes') {
            $acc_isp_notes = ' in';
        }

        if(!empty($_GET['dayprogramid'])) {

            $dayprogramid = $_GET['dayprogramid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM day_program WHERE dayprogramid='$dayprogramid'"));

            $support_contact_category = $get_contact['support_contact_category'];
            $support_contact = $get_contact['support_contact'];
            $timer = $get_contact['timer'];
			$date = $get_contact['date'];
			$timer_started = $get_contact['timer_started'];
            $planned_activity = $get_contact['planned_activity'];
            $day_obj = $get_contact['day_obj'];
            $completed_activity = $get_contact['completed_activity'];
            $day_notes = $get_contact['day_notes'];
            $eme_contact = $get_contact['eme_contact'];
        ?>
        <input type="hidden" id="dayprogramid" name="dayprogramid" value="<?php echo $dayprogramid ?>" />
        <input type="hidden" id="login_contactid" name="login_contactid" value="<?php echo $_SESSION['contactid']; ?>" />
        <?php   }      ?>

    <div class="panel-group" id="accordion2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse1" >
                        Contact<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse1" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    echo contact_category_call($dbc, 'contact_category_0', 'support_contact_category', $support_contact_category); ?>

                    <?php echo contact_call($dbc, 'contact_0', 'support_contact', $support_contact, '',$support_contact_category); ?>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse2" >
                        Today's Schedule<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse2" class="panel-collapse collapse <?php echo $acc_day_program; ?>">
                <div class="panel-body">


                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse3" >
                        Start Day Accordion<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse3" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_daily_timer.php');
                    ?>

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label">Planned Activity:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Choose a Category..." multiple name="planned_activity[]" class="chosen-select-deselect form-control" width="380">
                              <option <?php if (strpos($planned_activity, "Brush") !== FALSE) { echo " selected"; } ?> value="Brush">Brush</option>
                              <option <?php if (strpos($planned_activity, "Dinner") !== FALSE) { echo " selected"; } ?> value="Dinner">Dinner</option>
                              <option <?php if (strpos($planned_activity, "Get Dressed") !== FALSE) { echo " selected"; } ?> value="Get Dressed">Get Dressed</option>
                              <option <?php if (strpos($planned_activity, "Lunch") !== FALSE) { echo " selected"; } ?> value="Lunch">Lunch</option>
                              <option <?php if (strpos($planned_activity, "Shower") !== FALSE) { echo " selected"; } ?> value="Shower">Shower</option>
                              <option <?php if (strpos($planned_activity, "Walking") !== FALSE) { echo " selected"; } ?> value="Walking">Walking</option>
                            </select>
                        </div>
                    </div>

                   <div class="form-group">
                    <label for="first_name[]" class="col-sm-4 control-label">Day Objectives:</label>
                    <div class="col-sm-8">
                      <textarea name="day_obj" rows="5" cols="50" class="form-control"><?php echo $day_obj; ?></textarea>
                    </div>
                  </div>

                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse35" >
                        End Day Accordion<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse35" class="panel-collapse collapse">
                <div class="panel-body">

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label">Completed Activity:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Choose a Category..." multiple name="completed_activity[]" class="chosen-select-deselect form-control" width="380">
                              <option <?php if (strpos($completed_activity, "Brush") !== FALSE) { echo " selected"; } ?> value="Brush">Brush</option>
                              <option <?php if (strpos($completed_activity, "Get Dressed") !== FALSE) { echo " selected"; } ?> value="Get Dressed">Get Dressed</option>
                              <option <?php if (strpos($completed_activity, "Lunch") !== FALSE) { echo " selected"; } ?> value="Lunch">Lunch</option>
                            </select>
                        </div>
                    </div>

                   <div class="form-group">
                    <label for="first_name[]" class="col-sm-4 control-label">Day Notes:</label>
                    <div class="col-sm-8">
                      <textarea name="day_notes" rows="5" cols="50" class="form-control"><?php echo $day_notes; ?></textarea>
                    </div>
                  </div>

				  <div class="col-sm-8 pull-right">
					<button class='btn complete-timer-btn brand-btn mobile-block' style='height: 150px; width: 400px; font-size: 60px;'>End</button>
				</div>

                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse4" >
                        Emergency Contacts<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse4" class="panel-collapse collapse">
                <div class="panel-body">

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label">Emergency Contacts:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Choose a Category..." name="eme_contact" class="chosen-select-deselect form-control" width="380">
                              <option value=""></option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse5" >
                        Expenses<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse5" class="panel-collapse collapse">
                <div class="panel-body">

                <?php
				echo '<a href="'.WEBSITE_URL.'/Expense/add_expense.php?from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" onclick="return confirm(\'Are you sure you want to leave this page? Unsaved changes may be lost!\');" id="'.$row['ticketid'].'" title="'.$row['heading'].'">Add Expenses</a>';
               // echo '<a href="#"  onclick="wwindow.open(\''.WEBSITE_URL.'/Expense/add_expense.php\', \'newwindow\', \'width=900, height=900\'); return false;">Add Expense</a></td>';
                ?>

                </div>
            </div>
        </div>

    </div>

        <div class="form-group">
			<p><span class="hp-red"><em>Required Fields *</em></span></p>
        </div>

        <div class="form-group">
            <div class="col-sm-6"><a href="<?= $from_url ?>" class="btn brand-btn btn-lg">Back</a></div>
			<div class="col-sm-6"><button type="submit" name="add_medication" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button></div>
			<div class="clearfix"></div>
        </div>



    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function contact_category_call($dbc, $select_id, $select_name, $contact_category_value) {
    ?>
    <script type="text/javascript">
    $(document).on('change', 'select[name="<?= $select_name ?>"]', function() { selectContactCategory(this); });
    </script>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact Category:</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Category..." id="<?php echo $select_id; ?>" name="<?php echo $select_name; ?>" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $tabs = get_config($dbc, 'contacts_tabs');
                $each_tab = explode(',', $tabs);
                foreach ($each_tab as $cat_tab) {
                    ?>
                    <option <?php if (strpos($contact_category_value, $cat_tab) !== FALSE) {
			        echo " selected"; } ?> value='<?php echo $cat_tab; ?>'><?php echo $cat_tab; ?></option>
                <?php }
              ?>
            </select>
        </div>
    </div>
<?php } ?>

<?php
function contact_call($dbc, $select_id, $select_name, $contact_value,$multiple, $from_contact) {
    ?>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact:</label>
        <div class="col-sm-8">
            <select <?php echo $multiple; ?> data-placeholder="Choose a Contact..." name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php if($contact_value != '') {

                $query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category = '$from_contact' order by first_name");
                while($row = mysqli_fetch_array($query)) {
                    if(decryptIt($row['name']) != '') { ?>
                        <option <?php if (strpos($contact_value, $row['contactid']) !== FALSE) {
			            echo " selected"; } ?> value='<?php echo $row['contactid']; ?>'><?php echo decryptIt($row['name']); ?></option>
                    <?php } else { ?>
                        <option <?php if (strpos($contact_value, $row['contactid']) !== FALSE) {
			            echo " selected"; } ?> value='<?php echo $row['contactid']; ?>'><?php echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']); ?></option>
                    <?php
                    }
                }
             } ?>
            </select>
        </div>
    </div>
<?php } ?>
