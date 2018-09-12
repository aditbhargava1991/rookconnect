<?php
/*
Add Vendor
*/
include ('../include.php');
error_reporting(0);

if((!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
    $uploadid = $_GET['uploadid'];
    $query = mysqli_query($dbc,"DELETE FROM manuals_upload WHERE uploadid='$uploadid'");

    $type = $_GET['type'];
    $manualtypeid = $_GET['manualtypeid'];
    echo '<script type="text/javascript"> window.location.replace("add_manual.php?manualtypeid='.$manualtypeid.'&type='.$type.'"); </script>';
}

if (isset($_POST['view_manual'])) {
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

    $manualtypeid = $_POST['manualtypeid'];

    $type = $_POST['type'];

    if($comment != '') {
        if($type == 'policy_procedures') {
            $column = 'manual_policy_pro_email';
        }
        if($type == 'operations_manual') {
            $column = 'manual_operations_email';
        }
        if($type == 'emp_handbook') {
            $column = 'manual_emp_handbook_email';
        }
        if($type == 'guide') {
            $column = 'manual_guide_email';
        }
        if($type == 'safety') {
            $column = 'manual_safety_email';
        }

        $get_manual =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	manuals WHERE	manualtypeid='$manualtypeid'"));

        //Mail
        $to = get_config($dbc, $column);
        $user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
        $subject = 'Manual Read by '.$user;

        $message = "Category : ".$get_manual['category'].'<br>';
        $message .= "Heading/Section : ".$get_manual['heading'].'<br>';
        $message .= "Sub Heading : ".$get_manual['sub_heading'].'<br>';
        $message .= "Comment<br/><br/>".$_POST['comment'];
        send_email('', $to, '', '', $subject, $message, '');

        //Mail
    }

    $staffid = $_SESSION['contactid'];
    $today_date = date('Y-m-d H:i:s');
    $query_update_ticket = "UPDATE `manuals_staff` SET `done` = '1', `today_date` = '$today_date' WHERE `manualtypeid` = '$manualtypeid' AND staffid='$staffid' AND done=0";
    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

    echo '<script type="text/javascript"> window.location.replace("'.$type.'.php?contactid='.$_SESSION['contactid'].'"); </script>';
}

if (isset($_POST['add_manual'])) {
	if($_POST['new_category'] != '') {
		$category = filter_var($_POST['new_category'],FILTER_SANITIZE_STRING);
	} else {
		$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	}
	if($_POST['new_heading'] != '') {
		$heading = filter_var($_POST['new_heading'],FILTER_SANITIZE_STRING);
	} else {
		$heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
	}

	if($_POST['new_heading_number'] != '') {
		$heading_number = filter_var($_POST['new_heading_number'],FILTER_SANITIZE_STRING);
	} else {
		$heading_number = filter_var($_POST['heading_number'],FILTER_SANITIZE_STRING);
	}

    //$heading_number = filter_var($_POST['heading_number'],FILTER_SANITIZE_STRING);
    $sub_heading_number = filter_var($_POST['sub_heading_number'],FILTER_SANITIZE_STRING);
    $sub_heading = filter_var($_POST['sub_heading'],FILTER_SANITIZE_STRING);
    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
    $assign_staff = ','.implode(',',$_POST['assign_staff']).',';
    $deadline = filter_var($_POST['deadline'],FILTER_SANITIZE_STRING);
    $manual_type = $_POST['type'];

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    if(empty($_POST['manualtypeid'])) {
        $query_insert_vendor = "INSERT INTO `manuals` (`manual_type`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `description`, `assign_staff`, `deadline`) VALUES ('$manual_type', '$category', '$heading_number', '$heading', '$sub_heading_number', '$sub_heading', '$description', '$assign_staff', '$deadline')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $manualtypeid = mysqli_insert_id($dbc);

        $url = 'Added';
    } else {
        $manualtypeid = $_POST['manualtypeid'];
        $query_update_vendor = "UPDATE `manuals` SET `manual_type` = '$manual_type', `category` = '$category', `heading_number` = '$heading_number', `heading` = '$heading', `sub_heading_number` = '$sub_heading_number', `sub_heading` = '$sub_heading', `description` = '$description', `assign_staff` = '$assign_staff', `deadline` = '$deadline' WHERE `manualtypeid` = '$manualtypeid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
    }

    $assign_staff = $_POST['assign_staff'];
    for($i = 0; $i < count($_POST['assign_staff']); $i++) {
        if($assign_staff[$i] != '') {
            $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(manualstaffid) AS total_id FROM manuals_staff WHERE manualtypeid='$manualtypeid' AND staffid='$assign_staff[$i]' AND done=0"));
            if($get_staff['total_id'] == 0) {
                //Mail
                send_email([$_POST['email_address']=>$_POST['email_name']], get_email($dbc, $assign_staff[$i]), '', '', $_POST['email_subject'], $_POST['email_body'], '');
                //Mail

                $query_insert_upload = "INSERT INTO `manuals_staff` (`manualtypeid`, `staffid`) VALUES ('$manualtypeid', '$assign_staff[$i]')";
                $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
            }
        }
    }

    $document = htmlspecialchars($_FILES["document"]["name"], ENT_QUOTES);
    for($i = 0; $i < count($_FILES['document']['name']); $i++) {
        if($document[$i] != '') {
            move_uploaded_file($_FILES["document"]["tmp_name"][$i], "download/" . $_FILES["document"]["name"][$i]) ;
            $query_insert_upload = "INSERT INTO `manuals_upload` (`manualtypeid`, `type`, `upload`) VALUES ('$manualtypeid', 'document', '$document[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }
    }

    $link = $_POST['link'];
    for($i = 0; $i < count($_POST['link']); $i++) {
        if($link[$i] != '') {
            $query_insert_upload = "INSERT INTO `manuals_upload` (`manualtypeid`, `type`, `upload`) VALUES ('$manualtypeid', 'link', '$link[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }
    }

    $video = htmlspecialchars($_FILES["video"]["name"], ENT_QUOTES);
    for($i = 0; $i < count($_FILES['video']['name']); $i++) {
        if($video[$i] != '') {
            move_uploaded_file($_FILES["video"]["tmp_name"][$i], "download/" . $_FILES["video"]["name"][$i]) ;

            $query_insert_upload = "INSERT INTO `manuals_upload` (`manualtypeid`, `type`, `upload`) VALUES ('$manualtypeid', 'video', '$video[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }
    }

    echo '<script type="text/javascript"> window.location.replace("'.$manual_type.'.php?contactid='.$_SESSION['contactid'].'"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
}

?>
<script type="text/javascript">
$(document).ready(function() {

    $("#category").change(function() {
        if($("#category option:selected").text() == 'New Category') {
                $( "#new_category" ).show();
        } else {
            $( "#new_category" ).hide();
        }
    });

    $("#heading").change(function() {
        if($("#heading option:selected").text() == 'New Heading') {
                $( "#new_heading" ).show();
        } else {
            $( "#new_heading" ).hide();
        }
    });

    $("#heading_number").change(function() {
        if($("#heading_number option:selected").text() == 'New Heading Number') {
                $("#new_heading_number").show();
        } else {
            $( "#new_heading_number" ).hide();
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

    $('#add_row_videos').on( 'click', function () {
        var clone = $('.additional_videos').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_videos");
        $('#add_here_new_videos').append(clone);
        return false;
    });

} );

</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised();
?>
<div class="container">
  <div class="row">

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT policy_procedures, operations_manual, emp_handbook, guide, safety FROM field_config_manuals"));

        $manual_type = '';
        $type = '';
        if(!empty($_GET['type'])) {
            $type = $_GET['type'];
        }

        if($type == 'policy_procedures') {
            $manual_type = 'Policy & Procedures';
            $value_config = ','.$get_field_config['policy_procedures'].',';
        }
        if($type == 'operations_manual') {
            $manual_type = 'Operations Manual';
            $value_config = ','.$get_field_config['operations_manual'].',';
        }
        if($type == 'emp_handbook') {
            $manual_type = 'Employee Handbook';
            $value_config = ','.$get_field_config['emp_handbook'].',';
        }
        if($type == 'guide') {
            $manual_type = 'How to Guide';
            $value_config = ','.$get_field_config['guide'].',';
        }
        if($type == 'safety') {
            $manual_type = 'Safety';
            $value_config = ','.$get_field_config['safety'].',';
        }

        $category = '';
        $heading = '';
        $sub_heading = '';
        $description = '';
        $assign_staff = '';
        $deadline = '';
        $action = '';
        $heading_number = '';
        $sub_heading_number = '';

        if(!empty($_GET['manualtypeid'])) {

            $manualtypeid = $_GET['manualtypeid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM manuals WHERE manualtypeid='$manualtypeid'"));

            $heading_number = $get_contact['heading_number'];
            $sub_heading_number = $get_contact['sub_heading_number'];
            $category = $get_contact['category'];
            $heading = $get_contact['heading'];
            $sub_heading = $get_contact['sub_heading'];
            $description = $get_contact['description'];
            $assign_staff = $get_contact['assign_staff'];
            $deadline = $get_contact['deadline'];

            $action = $_GET['action'];
        ?>
        <input type="hidden" id="manualtypeid" name="manualtypeid" value="<?php echo $manualtypeid ?>" />
        <?php   }      ?>
        <input type="hidden" id="type" name="type" value="<?php echo $type; ?>" />

        <h1 class="triple-pad-bottom"><?php echo $manual_type ?></h1>

        <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
          <?php if($action == 'view') { ?>
           <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Category:</label>
            <div class="col-sm-8">
                <?php echo $category; ?>
            </div>
          </div>
          <?php } else { ?>
           <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Category:</label>
            <div class="col-sm-8">
                <select id="category" name="category" class="chosen-select-deselect form-control" width="380">
                    <option value=''></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT distinct(category) FROM manuals WHERE manual_type='$type' order by category");
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
            <label for="travel_task" class="col-sm-4 control-label">New Category Name:
            </label>
            <div class="col-sm-8">
                <input name="new_category" type="text" class="form-control" />
            </div>
          </div>
          <?php } ?>
      <?php } ?>

        <?php if (strpos($value_config, ','."Heading/Section Number".',') !== FALSE) { ?>

          <?php if($action == 'view') { ?>
           <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Heading/Section Number:</label>
            <div class="col-sm-8">
                <?php echo $heading_number; ?>
            </div>
          </div>
          <?php } else { ?>

          <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Heading/Section Number:</label>
            <div class="col-sm-8">
                <select id="heading_number" name="heading_number" class="chosen-select-deselect form-control" width="380">
                    <option value=''></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT distinct(heading_number) FROM manuals WHERE manual_type='$type' order by heading_number");
                    while($row = mysqli_fetch_array($query)) {
                        if ($heading_number == $row['heading_number']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $row['heading_number']."'>".$row['heading_number'].'</option>';
                    }
                    echo "<option value = 'Other'>New Heading Number</option>";
                    ?>
                </select>
            </div>
          </div>

           <div class="form-group" id="new_heading_number" style="display: none;">
            <label for="travel_task" class="col-sm-4 control-label">New Heading/Section Number:
            </label>
            <div class="col-sm-8">
                <input name="new_heading_number" type="text" class="form-control" />
            </div>
          </div>

        <?php } ?>
      <?php } ?>

        <?php if (strpos($value_config, ','."Heading/Section".',') !== FALSE) { ?>

          <?php if($action == 'view') { ?>
           <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Heading/Section:</label>
            <div class="col-sm-8">
                <?php echo $heading; ?>
            </div>
          </div>
          <?php } else { ?>

          <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Heading/Section:</label>
            <div class="col-sm-8">
                <select id="heading" name="heading" class="chosen-select-deselect form-control" width="380">
                    <option value=''></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT distinct(heading) FROM manuals WHERE manual_type='$type' order by heading");
                    while($row = mysqli_fetch_array($query)) {
                        if ($heading == $row['heading']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $row['heading']."'>".$row['heading'].'</option>';

                    }
                    echo "<option value = 'Other'>New Heading</option>";
                    ?>
                </select>
            </div>
          </div>

           <div class="form-group" id="new_heading" style="display: none;">
            <label for="travel_task" class="col-sm-4 control-label">New Heading/Section Name:
            </label>
            <div class="col-sm-8">
                <input name="new_heading" type="text" class="form-control" />
            </div>
          </div>

        <?php } ?>
      <?php } ?>

       <?php if (strpos($value_config, ','."Sub Heading Number".',') !== FALSE) { ?>
          <?php if($action == 'view') { ?>
           <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Sub Heading Number:</label>
            <div class="col-sm-8">
                <?php echo $sub_heading_number; ?>
            </div>
          </div>
          <?php } else { ?>

          <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Sub Heading Number:</label>
            <div class="col-sm-8">
              <input name="sub_heading_number" value="<?php echo $sub_heading_number; ?>" type="text" id="name" class="form-control">
            </div>
          </div>
        <?php } ?>
      <?php } ?>

       <?php if (strpos($value_config, ','."Sub Heading".',') !== FALSE) { ?>
          <?php if($action == 'view') { ?>
           <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Sub Heading:</label>
            <div class="col-sm-8">
                <?php echo $sub_heading; ?>
            </div>
          </div>
          <?php } else { ?>

          <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Sub Heading:</label>
            <div class="col-sm-8">
              <input name="sub_heading" value="<?php echo $sub_heading; ?>" type="text" id="name" class="form-control">
            </div>
          </div>
        <?php } ?>
      <?php } ?>

      <?php if (strpos($value_config, ','."Description".',') !== FALSE) { ?>

          <?php if($action == 'view') { ?>
           <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Description:</label>
            <div class="col-sm-8">
                <?php //echo html_entity_decode($description); ?>
                <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
            </div>
          </div>
          <?php } else { ?>

          <div class="form-group">
            <label for="first_name[]" class="col-sm-4 control-label">Description:</label>
            <div class="col-sm-8">
              <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
            </div>
          </div>
        <?php } ?>
      <?php } ?>

        <?php if (strpos($value_config, ','."Document".',') !== FALSE) { ?>

          <?php if($action == 'view') { ?>
           <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Document(s):</label>
            <div class="col-sm-8">
                <?php
                    $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM manuals_upload WHERE type='document' AND manualtypeid='$manualtypeid'"));

                    if((!empty($_GET['manualtypeid'])) && ($get_doc['total_id'] > 0)) {
                        $result = mysqli_query($dbc, "SELECT upload, uploadid FROM manuals_upload WHERE type='document' AND manualtypeid='$manualtypeid'");

                        echo '<ul>';
                        $i=0;
                        while($row = mysqli_fetch_array($result)) {
                            $document = $row['upload'];
                            if($document != '') {
                                echo '<li><a href="download/'.$document.'" target="_blank">'.$document.'</a></li>';
                            }
                        }
                        echo '</ul>';
                    }
                ?>
            </div>
          </div>
          <?php } else { ?>
            <div class="form-group">
                <label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
                        <span class="popover-examples list-inline">&nbsp;
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                        </span>
                </label>
                <div class="col-sm-8">

                <?php
                    if(!empty($_GET['manualtypeid'])) {
                    $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM manuals_upload WHERE type='document' AND manualtypeid='$manualtypeid'"));

                    if($get_doc['total_id'] > 0) {
                        $result = mysqli_query($dbc, "SELECT upload, uploadid FROM manuals_upload WHERE type='document' AND manualtypeid='$manualtypeid'");

                        echo '<ul>';
                        $i=0;
                        while($row = mysqli_fetch_array($result)) {
                            $document = $row['upload'];
                            if($document != '') {
                                echo '<li><a href="download/'.$document.'" target="_blank">'.$document.'</a> - <a href="add_manual.php?action=delete&uploadid='.$row['uploadid'].'&manualtypeid='.$manualtypeid.'&type='.$type.'" onclick="return confirm(\'Are you sure?\')">Delete</a></li>';
                            }
                        }
                        echo '</ul>';
                    }
                }
                ?>
                    <div class="enter_cost additional_doc clearfix">
                        <div class="clearfix"></div>

                        <div class="form-group clearfix">
                            <div class="col-sm-5">
                                <input name="document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                            </div>
                        </div>

                    </div>

                    <div id="add_here_new_doc"></div>

                    <div class="form-group triple-gapped clearfix">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button id="add_row_doc" class="btn brand-btn pull-left">Add Another Document</button>
                        </div>
                    </div>
                </div>
            </div>
          <?php } ?>
        <?php } ?>

        <?php if (strpos($value_config, ','."Link".',') !== FALSE) { ?>

          <?php if($action == 'view') { ?>
           <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Link(s):</label>
            <div class="col-sm-8">
                <?php
                    $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM manuals_upload WHERE type='link' AND manualtypeid='$manualtypeid'"));

                    if((!empty($_GET['manualtypeid'])) && ($get_doc['total_id'] > 0)) {
                        $result = mysqli_query($dbc, "SELECT upload, uploadid FROM manuals_upload WHERE type='link' AND manualtypeid='$manualtypeid'");

                        echo '<ul>';
                        $i=0;
                        while($row = mysqli_fetch_array($result)) {
                            $link = $row['upload'];
                            if($link != '') {
                                echo '<li><a href="'.$link.'" target="_blank">'.$link.'</a></li>';
                            }
                        }
                        echo '</ul>';
                    }
                ?>
            </div>
          </div>
          <?php } else { ?>

            <div class="form-group">
                <label for="additional_note" class="col-sm-4 control-label">Add Link(s):<br><em>(e.g. - https://www.google.com)</em>
                </label>
                <div class="col-sm-8">

                <?php
                    if(!empty($_GET['manualtypeid'])) {
                        $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM manuals_upload WHERE type='link' AND manualtypeid='$manualtypeid'"));

                        if($get_doc['total_id'] > 0) {
                            $result = mysqli_query($dbc, "SELECT upload, uploadid FROM manuals_upload WHERE type='link' AND manualtypeid='$manualtypeid'");

                            echo '<ul>';
                            $i=0;
                            while($row = mysqli_fetch_array($result)) {
                                $link = $row['upload'];
                                if($link != '') {
                                    echo '<li><a href="'.$link.'" target="_blank">'.$link.'</a> - <a href="add_manual.php?action=delete&uploadid='.$row['uploadid'].'&manualtypeid='.$manualtypeid.'&type='.$type.'" onclick="return confirm(\'Are you sure?\')">Delete</a></li>';
                                }
                            }
                            echo '</ul>';
                        }
                    }
                ?>

                    <div class="enter_cost additional_link clearfix">
                        <div class="clearfix"></div>

                        <div class="form-group clearfix">
                            <div class="col-sm-5">
                                <input name="link[]" type="text" class="form-control"/>
                            </div>
                        </div>

                    </div>

                    <div id="add_here_new_link"></div>

                    <div class="form-group triple-gapped clearfix">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button id="add_row_link" class="btn brand-btn pull-left">Add Another Link</button>
                        </div>
                    </div>
                </div>
            </div>
          <?php } ?>
        <?php } ?>

        <?php if (strpos($value_config, ','."Videos".',') !== FALSE) { ?>

          <?php if($action == 'view') { ?>
           <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Video(s):</label>
            <div class="col-sm-8">
            <?php
                $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM manuals_upload WHERE type='video' AND manualtypeid='$manualtypeid'"));

                if((!empty($_GET['manualtypeid'])) && ($get_doc['total_id'] > 0)) {
                    $result = mysqli_query($dbc, "SELECT upload, uploadid FROM manuals_upload WHERE type='video' AND manualtypeid='$manualtypeid'");

                    echo '<ul>';
                    $i=0;
                    while($row = mysqli_fetch_array($result)) {
                        $video = $row['upload'];
                        if($video != '') {
                            echo '<li><a href="download/'.$video.'" target="_blank">'.$video.'</a></li>';
                        }
                    }
                    echo '</ul>';
                }
            ?>
            </div>
          </div>
          <?php } else { ?>

            <div class="form-group">
                <label for="additional_note" class="col-sm-4 control-label">Upload Video(s):
                        <span class="popover-examples list-inline">&nbsp;
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                        </span>
                </label>
                <div class="col-sm-8">

                <?php
                    if(!empty($_GET['manualtypeid'])) {
                    $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM manuals_upload WHERE type='video' AND manualtypeid='$manualtypeid'"));

                    if($get_doc['total_id'] > 0) {
                        $result = mysqli_query($dbc, "SELECT upload, uploadid FROM manuals_upload WHERE type='video' AND manualtypeid='$manualtypeid'");

                        echo '<ul>';
                        $i=0;
                        while($row = mysqli_fetch_array($result)) {
                            $video = $row['upload'];
                            if($video != '') {
                                echo '<li><a href="download/'.$video.'" target="_blank">'.$video.'</a> - <a href="add_manual.php?action=delete&uploadid='.$row['uploadid'].'&manualtypeid='.$manualtypeid.'&type='.$type.'" onclick="return confirm(\'Are you sure?\')">Delete</a></li>';
                            }
                        }
                        echo '</ul>';
                    }
                }
                ?>
                    <div class="enter_cost additional_videos clearfix">
                        <div class="clearfix"></div>

                        <div class="form-group clearfix">
                            <div class="col-sm-5">
                                <input name="video[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                            </div>
                        </div>

                    </div>

                    <div id="add_here_new_videos"></div>

                    <div class="form-group triple-gapped clearfix">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button id="add_row_videos" class="btn brand-btn pull-left">Add Another Video</button>
                        </div>
                    </div>
                </div>
            </div>
          <?php } ?>
        <?php } ?>

        <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
        <?php if($action == 'view') { ?>

        <?php  } else { ?>
            <div class="form-group clearfix completion_date">
                <label for="first_name" class="col-sm-4 control-label text-right">Staff:</label>
                <div class="col-sm-8">
                    <select name="assign_staff[]" data-placeholder="Choose a Staff Member..." class="chosen-select-deselect form-control" multiple width="380">
						<?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND IFNULL(`staff_category`,'') NOT IN (".STAFF_CATS_HIDE.") AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
					foreach($query as $id) {
						$selected = '';
						$selected = strpos($assign_staff, ','.$id.',') !== false ? 'selected = "selected"' : '';
						echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
					}
				  ?>
                    </select>
                </div>
            </div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Sender Name</label>
				<div class="col-sm-8">
					<input type="text" name="email_sender_name" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Sender Address</label>
				<div class="col-sm-8">
					<input type="text" name="email_sender_address" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid'], 'email_address') ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Subject</label>
				<div class="col-sm-8">
					<input type="text" name="email_subject" class="form-control" value="Manual Assigned to you for Review">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Body</label>
				<div class="col-sm-8">
					<textarea name="email_body" class="form-control">
						Please login through the software and click on the link below. Sign in the signature box to confirm you understand and will adhere to this policy. If you have any questions or concerns, add them in the comment section. <br><br>
						Manual : <a target="_blank" href="<?= WEBSITE_URL ?>/Manuals/add_manual.php?manualtypeid=<?= $manualtypeid ?>&type=<?= $_GET['type'] ?>&action=view">Click Here</a><br>
					</textarea>
				</div>
			</div>
        <?php } ?>
        <?php } ?>

        <?php if (strpos($value_config, ','."Review Deadline".',') !== FALSE) { ?>
                <?php if($action == 'view') { ?>

        <?php  } else { ?>
            <div class="form-group clearfix">
                <label for="first_name" class="col-sm-4 control-label text-right">Review Deadline:</label>
                <div class="col-sm-8">
                    <input name="deadline" type="text" class="datepicker" value="<?php echo $deadline; ?>"></p>
                </div>
            </div>
            <?php } ?>
        <?php } ?>


      <?php if (strpos($value_config, ','."Comments".',') !== FALSE) { ?>
          <?php if($action == 'view') { ?>
          <div class="form-group">
            <label for="first_name[]" class="col-sm-4 control-label">Comments:</label>
            <div class="col-sm-8">
              <textarea name="comment" rows="5" cols="50" class="form-control"></textarea>
            </div>
          </div>
        <?php } ?>
      <?php } ?>

      <?php if (strpos($value_config, ','."Signature box".',') !== FALSE) { ?>
          <?php if($action == 'view') { ?>
          <div class="form-group">
            <label for="first_name[]" class="col-sm-4 control-label">Signature:</label>
            <div class="col-sm-8">
              <?php include ('../phpsign/sign.php'); ?>
            </div>
          </div>
        <?php } ?>
      <?php } ?>

        <?php if($action == 'view') { ?>
        <?php  } else { ?>
            <div class="form-group">
              <div class="col-sm-4">
                  <p><span class="hp-red pull-right"><em>Required Fields *</em></span></p>
              </div>
              <div class="col-sm-8"></div>
            </div>
        <?php } ?>

        <div class="form-group">
            <div class="col-sm-4 clearfix">
                <!--<a href="<?php //echo $type; ?>.php?contactid=<?php //echo $_SESSION['contactid']; ?>" class="btn brand-btn pull-right">Back</a>-->
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
            </div>
          <div class="col-sm-8">
            <?php if($action == 'view') { ?>
            <button type="submit" name="view_manual" value="view_manual" class="btn brand-btn btn-lg pull-right">Submit</button>
            <?php } else { ?>
            <button type="submit" name="add_manual" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
            <?php } ?>
		  </div>
        </div>



    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
