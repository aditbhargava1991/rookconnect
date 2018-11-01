<?php
/*
 * Add News
 * Called From: index.php, news_item.php
 * Function: Add or edit a single news item
 */
include ('../include.php');
include ('../database_connection_htg.php');
error_reporting(0);
$rookconnect = get_software_name();

if (isset($_POST['add_news'])) {
    $contactid = $_SESSION['contactid'];

    if ( !empty($_POST['newsboard_type']) ) {
		$newsboard_type = filter_var($_POST['newsboard_type'],FILTER_SANITIZE_STRING);
	} else {
		$newsboard_type = 'Local';
	}

    if ( isset($_POST['new_newsboard_board']) && !empty($_POST['new_newsboard_board']) ) {
        $new_newsboard_board = filter_var($_POST['new_newsboard_board'],FILTER_SANITIZE_STRING);
        mysqli_query($dbc, "INSERT INTO `newsboard_boards` (`board_name`, `shared_staff`) VALUES ('$new_newsboard_board', ',$contactid,')");
        $newsboard_board = mysqli_insert_id($dbc);
    } else {
        $newsboard_board = filter_var($_POST['newsboard_board'],FILTER_SANITIZE_STRING);
    }

    foreach ( $_POST['tags'] as $tag ) {
        $tags_arr[] = str_replace(',', ' ', filter_var($tag, FILTER_SANITIZE_STRING));
    }
    $tags = implode(',', $tags_arr);

    $title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
    $issue_date = date('Y-m-d');
    $expiry_date = ( !empty($_POST['expiry_date']) && $_POST['expiry_date'] != '0000-00-00' ) ? date('Y-m-d', strtotime($_POST['expiry_date'])) : $_POST['expiry_date'];
    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
    $url = '';

    if ( empty($_POST['newsboardid']) ) {
        $query_insert = "INSERT INTO `newsboard` (`contactid`, `boardid`, `newsboard_type`, `tags`, `title`, `issue_date`, `expiry_date`, `description`) VALUES ('$contactid', '$newsboard_board', '$newsboard_type', '$tags', '$title', '$issue_date', '$expiry_date', '$description')";

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
        //}
    } else {
        $newsboardid = $_POST['newsboardid'];
        $query_update = "UPDATE `newsboard` SET `contactid`='$contactid', `boardid`='$newsboard_board', `newsboard_type`='$newsboard_type', `tags`='$tags', `title`='$title', `expiry_date`='$expiry_date', `description`='$description' WHERE `newsboardid`='$newsboardid'";

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

        $.datepicker.setDefaults({
            onSelect: function(value) {
                if(this.id == 'expiry_date') {
                    var date = new Date(value);
                    date.setDate(date.getDate() - 30);
                }
            }
        });

        $('#newsboard_board').on('change', function() {
            var boardid = $('#newsboard_board option:selected').val();
            if ( boardid == 'NEW' ) {
                $('input[name="new_newsboard_board"]').show();
            } else {
                $('input[name="new_newsboard_board"]').hide();
            }
        });

        $('.delete_image').on('click', function() {
            var ans = confirm('Are you sure you want to delete this image?');
            if ( ans == true ) {
                var certuploadid = $(this).data('id');
                var newsboard_type = $(this).data('type');
                $.ajax({
                    type: 'GET',
                    url: 'news_ajax_all.php?fill=delete_image&certuploadid='+certuploadid+'&newsboard_type='+newsboard_type,
                    success: function(response) {
                        alert('Image deleted successfully.');
                    }
                });
            }
        });
    });

    $(document).on('change', '#newsboard_type', changeBoardType);
    $(document).on('change', '#newsboard_board', changeBoard);

    function addTag(button) {
        var block = $(button).closest('.tags_class');
        clone = block.clone();
        clone.find('.form-control').val('');
        block.after(clone);
    }
    function removeTag(button) {
        if($('div.tags_class').length <= 1) {
            $(button).closest('.tags_class').find('.form-control').val('');
        } else {
            $(button).closest('.tags_class').remove();
        }
    }
    function closeSlider() {
        window.location.href="../blank_loading_page.php";
    }
    function changeBoardType() {
        $('#newsboard_board').val('');
        $('#newsboard_board').trigger('change');
        var newsboard_type = $('#newsboard_type option:selected').val();
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: 'news_ajax_all.php?fill=get_boards&newsboard_type='+newsboard_type,
            success: function(response) {
                $('#newsboard_board').html(response);
                $('#newsboard_board').trigger('change');
            }
        });
    }
    function changeBoard() {
        var boardid = $('#newsboard_board option:selected').val();
        var newsboard_type = $('#newsboard_type option:selected').val();
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: 'news_ajax_all.php?fill=get_tags&boardid='+boardid+'&newsboard_type='+newsboard_type,
            success: function(response) {
                $('.tags_container').html(response);
            }
        });
    }
</script>
</head>

<body>
<?php
include_once ('../navigation.php');
checkAuthorised('newsboard');
$newsboardid = isset($_GET['news']) ? preg_replace('/[^0-9]/', '', $_GET['news']) : '';
?>
<div class="container">
    <div class="row">
        <div class="offset-bottom-15">
            <h3 class="inline gap-left"><?= !empty($newsboardid) ? 'Edit' : 'Add' ?> News Item</h3>
            <div class="pull-right gap-right offset-top-15"><a class="cursor-hand" onclick="closeSlider();"><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img"></a></div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT newsboard FROM field_config"));
            $value_config = ','.$get_field_config['newsboard'].',';
            $newsboard_type = '';
            $title = '';
            $expiry_date = '';
            $description = '';

            if ( !empty($newsboardid) ) {
                $query = "SELECT * FROM newsboard WHERE newsboardid='$newsboardid'";
                if ( $_GET['type'] == 'sw' ) {
                    $news = mysqli_fetch_assoc(mysqli_query($dbc_htg, $query));
                } else {
                    $news = mysqli_fetch_assoc(mysqli_query($dbc, $query));
                }
                $contactid = $news['contactid'];
                $boardid = $news['boardid'];
                $newsboard_type = $news['newsboard_type'];
                $tags = $news['tags'];
                $title = $news['title'];
                $issue_date = $news['issue_date'];
                $expiry_date = $news['expiry_date'];
                $description = $news['description']; ?>
                <input type="hidden" id="newsboardid" name="newsboardid" value="<?= $newsboardid ?>" /><?php
            } ?>

            <?php if ( $rookconnect=='rook' || $rookconnect=='localhost' ) { ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label">
                            News Board Type<span class="red">*</span>:
                            <span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select the type from the dropdown menu. Add Softwarewide News Boards only from FFM Software."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                        </label>
                        <select id="newsboard_type" name="newsboard_type" class="chosen-select-deselect form-control" required>
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

            <div class="form-group">
                <div class="col-sm-12">
                    <label class="control-label">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select the News Board this news item should go under."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                        News Board<span class="red">*</span>:
                    </label>
                    <select id="newsboard_board" name="newsboard_board" class="chosen-select-deselect form-control" required>
                        <option value=""></option>
                        <option value="NEW">Add New News Board</option>
                        <?php
                            /* if ( $rookconnect=='rook' || $rookconnect=='localhost' ) {
                                echo '<option value="FFM">FFM Board</option>';
                            } */
                        ?>
                        <?php
                            $query = mysqli_query($dbc, "SELECT * FROM `newsboard_boards` WHERE `deleted`=0 ORDER BY `board_name`");
                            while ( $boards = mysqli_fetch_assoc($query) ) {
                                $selected = ( $boardid == $boards['boardid'] ) ? 'selected="selected"' : '';
                                echo '<option value="'. $boards['boardid'] .'" '. $selected .'>'. $boards['board_name'] .'</option>';
                            }
                        ?>
                    </select>
                    <input type="text" name="new_newsboard_board" class="form-control" style="display:none;" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12 tags_container">
                    <label for="company_name" class="control-label"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="These are the Sub Tabs this news item should go under. Add or remove as necessary."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Sub Tabs<span class="red">*</span>:</label>
                    <?php
                        foreach ( explode(',', $tags) as $tag ) {
                            echo '<div class="tags_class">';
                                echo '<div class="col-xs-10 no-pad-left"><input name="tags[]" value="'. $tag .'" type="text" class="form-control" /></div>';
                                echo '<div class="col-xs-2">';
                                    echo '<img class="inline-img pull-right cursor-hand" onclick="removeTag(this);" src="../img/remove.png" />';
                                    echo '<img class="inline-img pull-right cursor-hand" onclick="addTag(this);" src="../img/icons/ROOK-add-icon.png" />';
                                echo '</div>';
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="company_name" class="control-label"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="This is the title of the news item that will display on the New Board dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Title<span class="red">*</span>:</label>
                    <input name="title" value="<?= $title; ?>" type="text" id="title" class="form-control" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="additional_note" class="control-label">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                        Header Image:
                    </label>
                    <?php
                    if(!empty($newsboardid)) {
                        $query_check_credentials = "SELECT * FROM newsboard_uploads WHERE newsboardid='$newsboardid' AND type = 'Document' ORDER BY certuploadid DESC";
                        $result = mysqli_query($dbc, $query_check_credentials);
                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                            while($row = mysqli_fetch_array($result)) {
                                $certuploadid = $row['certuploadid'];
                                echo '<ul>';
                                    echo '<li><a href="download/'.$row['document_link'].'" target="_blank">'.$row['document_link'].'</a> <img src="../img/remove.png" alt="Delete Image" class="inline-img cursor-hand delete_image" data-id="'.$certuploadid.'" data-type="'.$newsboard_type.'" /></li>';
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
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="company_name" class="control-label">Expiry Date:</label>
                    <input name="expiry_date" value="<?= $expiry_date; ?>" id="expiry_date" type="text" class="datepicker form-control" style="width:150px;" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <label for="first_name[]" class="control-label"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="This is where the body of your message will go."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Description:</label>
                    <textarea name="description" rows="5" cols="50" class="form-control"><?= html_entity_decode($description); ?></textarea>
                </div>
            </div>

            <div class="double-gap-bottom">
                <button type="submit" name="add_news" value="Submit" class="btn brand-btn pull-right">Submit</button>
                <a class="btn brand-btn pull-right cursor-hand" onclick="closeSlider();">Cancel</a>
                <div class="clearfix"></div>
            </div>
        </form>
    </div>
</div>
<?php include ('../footer.php'); ?>
