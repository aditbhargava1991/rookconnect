<?php
/*
Add News Board
*/
include ('../include.php');
include ('../database_connection_htg.php');
error_reporting(0);
$rookconnect = get_software_name();

if(!empty($_GET['certuploadid'])) {
    $certuploadid = $_GET['certuploadid'];
    $query = mysqli_query($dbc,"DELETE FROM newsboard_uploads WHERE certuploadid='$certuploadid'");
    $newsboardid = $_GET['newsboardid'];

    echo '<script type="text/javascript"> window.location.replace("add_newsboard.php?newsboardid='.$newsboardid.'"); </script>';
}

if (isset($_POST['add_newsboard'])) {
    $contactid = $_SESSION['contactid'];

    if ( !empty($_POST['newsboard_type']) ) {
		$newsboard_type = filter_var($_POST['newsboard_type'],FILTER_SANITIZE_STRING);
	} else {
		$newsboard_type = 'Local';
	}

    $title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
    $issue_date = date('Y-m-d');
    $expiry_date = $_POST['expiry_date'];
    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
    $url = '';

    if ( empty($_POST['newsboardid']) ) {
        $software_url       = $_SERVER['SERVER_NAME'];
        $software_author    = '';
        $software_approval  = '';

        if($software_url == 'sea-alberta.rookconnect.com') {
            $software_author = 'SEA Alberta';
        } else if($software_url == 'sea-vancouver.rookconnect.com') {
            $software_author = 'SEA Vancouver';
        } else if($software_url == 'sea-saskatoon.rookconnect.com') {
            $software_author = 'SEA Saskatoon';
        } else if($software_url == 'sea-regina.rookconnect.com') {
            $software_author = 'SEA Regina';
        }

        $query_insert = "INSERT INTO `newsboard` (`contactid`, `newsboard_type`, `description`, `title`, `issue_date`, `expiry_date`, `cross_software`, `cross_software_approval`) VALUES ('$contactid', '$newsboard_type', '$description', '$title', '$issue_date', '$expiry_date', '$software_author', '$software_approval')";

        if ( $newsboard_type=='Softwarewide' ) {
            $result_insert_newsboard = mysqli_query($dbc_htg, $query_insert) or die(mysqli_error($dbc));
            $newsboardid = mysqli_insert_id($dbc_htg);
        } else {
            $result_insert_newsboard = mysqli_query($dbc, $query_insert) or die(mysqli_error($dbc));
            $newsboardid = mysqli_insert_id($dbc);
        }

        // Tag all the staff by default to the new post in a news board
        $boardid = $newsboard_board;
        $tag_newsboardid = $newsboardid;
        $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`,`email_address` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));
        foreach($staff_list as $staff) {
          $tag_staff_array[] = $staff['contactid'];
          $tag_staff_email_array[] = $staff['email_address'];
        }

        $tag_staff = implode(",", $tag_staff_array);
        $tag_staff_query = "INSERT INTO newsboard_tag(`boardid`, `newsboardid`, `staff`) VALUES('$boardid', '$tag_newsboardid', '$tag_staff')";
        mysqli_query($dbc, $tag_staff_query);
        $url = 'Added';

        //foreach($tag_staff_email_array as $tag_staff_email) {
        try {
            $sender = '';
            $cc_emails = '';
            $bcc_emails = '';
            $title = "test";
            $user = $tag_staff_email_array;
            $subject = "New post -> $title has been added to the News Board.";
            $body = "New Post with Name $title has been added to the news Board. <br>";
            $body .= 'For more details <a href="'.'https://' . $_SERVER['SERVER_NAME'] . '/News Board/index.php">click here</a>';
            send_email($sender, $user, $cc_emails, $bcc_emails, $subject, html_entity_decode($body), '');
        } catch (Exception $e) {
            $error .= "Unable to send email: ".$e->getMessage()."\n";
        }
        $url = 'Added';
    } else {
        $newsboardid = $_POST['newsboardid'];
        $query_update = "UPDATE `newsboard` SET `contactid`='$contactid', `newsboard_type`='$newsboard_type', `description`='$description', `title`='$title', `expiry_date`='$expiry_date' WHERE `newsboardid`='$newsboardid'";

        if ( $newsboard_type=='Softwarewide' ) {
            $result_update_vendor = mysqli_query($dbc_htg, $query_update);
        } else {
            $result_update_vendor = mysqli_query($dbc, $query_update);
        }
        $url = 'Updated';
    }

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        $document = htmlspecialchars($_FILES["upload_document"]["name"][$i], ENT_QUOTES);

        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;

        if($document != '') {
            $query_insert_client_doc = "INSERT INTO `newsboard_uploads` (`newsboardid`, `type`, `document_link`) VALUES ('$newsboardid', 'Document', '$document')";
            if ( $newsboard_type=='Softwarewide' ) {
                $result_insert_client_doc = mysqli_query($dbc_htg, $query_insert_client_doc);
            } else {
                $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
            }
        }
    }

    echo '<script type="text/javascript"> alert("News Board '.$url.'"); window.location.replace("newsboard.php"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
    $("#form1").submit(function( event ) {
        var newsboard_type = $("#newsboard_type").val();
        var title = $("input[name=title]").val();
        if (newsboard_type == '' || title == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });

    $('#add_row_doc').on( 'click', function () {
        var clone = $('.additional_doc').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_doc");
        $('#add_here_new_doc').append(clone);
        return false;
    });

    $.datepicker.setDefaults({
        onSelect: function(value) {
            if(this.id == 'expiry_date') {
                var date = new Date(value);
                date.setDate(date.getDate() - 30);
            }
        }
    });


});
</script>
</head>

<body>
<?php
include_once ('../navigation.php');
checkAuthorised('newsboard');
?>
<div class="container">
    <div class="row">
        <h1>News Board</h1>
        <div class="gap-left gap-top double-gap-bottom"><a href="newsboard.php" class="btn config-btn">Back to Dashboard</a></div>
        <form id="form1" name="form1" method="post"	action="add_newsboard.php" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT newsboard FROM field_config"));
            $value_config = ','.$get_field_config['newsboard'].',';
            $newsboard_type = '';
            $title = '';
            $expiry_date = '';
            $description = '';

            if(!empty($_GET['newsboardid'])) {
                $newsboardid = $_GET['newsboardid'];
                $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM newsboard WHERE newsboardid='$newsboardid'"));
                $contactid = $get_contact['contactid'];
                $newsboard_type = $get_contact['newsboard_type'];
                $title = $get_contact['title'];
                $expiry_date = $get_contact['expiry_date'];
                $description = $get_contact['description']; ?>
                <input type="hidden" id="newsboardid" name="newsboardid" value="<?php echo $newsboardid ?>" /><?php
            } ?>

            <?php if (strpos($value_config, ','."News Board Type".',') !== FALSE && ($rookconnect=='rook' || $rookconnect=='localhost') ) { ?>
                <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select the type from the dropdown menu. Add Softwarewide News Boards only from FFM Software."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>News Board Type<span class="hp-red">*</span>:</label>
                    <div class="col-sm-8">
                        <select id="newsboard_type" name="newsboard_type" class="chosen-select-deselect form-control" width="380">
                            <option value=""></option>
                            <?php
                                $selected_sw = $newsboard_type=='Softwarewide' ? 'selected="selected"' : '';
                                $selected_local = $newsboard_type!='Softwarewide' ? 'selected="selected"' : '';
                            ?>
                            <option <?= $selected_sw ?> value="Softwarewide">Softwarewide</option>
                            <option <?= $selected_local ?> value="Local">Local Software</option>
                        </select>
                    </div>
                </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Title".',') !== FALSE) { ?>
                <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="This is the title of the news item that will display on the New Board dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Title<span class="hp-red">*</span>:</label>
                    <div class="col-sm-8">
                        <input name="title" value="<?php echo $title; ?>" type="text" id="title" class="form-control">
                    </div>
                </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Uploader".',') !== FALSE) { ?>
                <div class="form-group">
                    <label for="additional_note" class="col-sm-4 control-label">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                        Header Image:
                    </label>
                    <div class="col-sm-8"><?php
                        if(!empty($_GET['newsboardid'])) {
                            $query_check_credentials = "SELECT * FROM newsboard_uploads WHERE newsboardid='$newsboardid' AND type = 'Document' ORDER BY certuploadid DESC";
                            $result = mysqli_query($dbc, $query_check_credentials);
                            $num_rows = mysqli_num_rows($result);
                            if($num_rows > 0) {
                                while($row = mysqli_fetch_array($result)) {
                                    $certuploadid = $row['certuploadid'];
                                    echo '<ul>';
                                    echo '<li><a href="download/'.$row['document_link'].'" target="_blank">'.$row['document_link'].'</a> - <a href="add_newsboard.php?certuploadid='.$certuploadid.'&newsboardid='.$newsboardid.'"> Delete</a></li>';
                                    echo '</ul>';
                                }
                            }
                        } ?>
                        <div class="enter_cost additional_doc clearfix">
                            <div class="clearfix"></div>
                            <div class="form-group clearfix">
                                <div class="">
                                    <input name="upload_document[]" type="file" data-filename-placement="inside" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <div id="add_here_new_doc"></div>

                        <div class="clearfix">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button style='display:none;' id="add_row_doc" class="btn brand-btn pull-left">Add Another Document</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Expiry Date".',') !== FALSE) { ?>
                <div class="form-group">
                    <label for="company_name" class="col-sm-4 control-label">Expiry Date:</label>
                    <div class="col-sm-8">
                        <input name="expiry_date" value="<?php echo $expiry_date; ?>" id="expiry_date" type="text" class="datepicker form-control" style="width:150px;" />
                    </div>
                </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Description".',') !== FALSE) { ?>
                <div class="form-group">
                    <label for="first_name[]" class="col-sm-4 control-label"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="This is where the body of your message will go."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Description:</label>
                    <div class="col-sm-8">
                        <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
                    </div>
                </div>
            <?php } ?>

            <div class="form-group">
                <p><span class="hp-red"><em>Required Fields *</em></span></p>
            </div>

            <div class="double-gap-bottom">
                <div class="pull-left">
                    <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="newsboard.php" class="btn brand-btn btn-lg">Back</a>
                </div>
                <div class="pull-right">
                    <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to submit your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <button type="submit" name="add_newsboard" value="Submit" class="btn brand-btn btn-lg">Submit</button>
                </div>
                <div class="clearfix"></div>
            </div>
        </form>
    </div>
</div>
<?php include ('../footer.php'); ?>
