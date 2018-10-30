<?php $manualid = filter_var($_GET['manual_edit'], FILTER_SANITIZE_STRING);
if(isset($_POST['submit'])) {
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$favourite = filter_var($_POST['favourite'],FILTER_SANITIZE_STRING);
	$heading_number = filter_var($_POST['heading_number'],FILTER_SANITIZE_STRING);
	$heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
	$sub_heading_number = filter_var($_POST['sub_heading_number'],FILTER_SANITIZE_STRING);
	$sub_heading = filter_var($_POST['sub_heading'],FILTER_SANITIZE_STRING);
	$third_heading_number = filter_var($_POST['third_heading_number'],FILTER_SANITIZE_STRING);
	$third_heading = filter_var($_POST['third_heading'],FILTER_SANITIZE_STRING);
	$description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
	$assign_staff = filter_var(implode(',',$_POST['assign_staff']),FILTER_SANITIZE_STRING);
	$deadline = filter_var($_POST['deadline'],FILTER_SANITIZE_STRING);
	$email_subject = filter_var($_POST['email_subject'],FILTER_SANITIZE_STRING);
	$email_message = filter_var(htmlentities($_POST['email_message']),FILTER_SANITIZE_STRING);
	$recurring_due_date = filter_var($_POST['recurring_due_date'],FILTER_SANITIZE_STRING);
	$recurring_due_date_interval = filter_var($_POST['recurring_due_date_interval'],FILTER_SANITIZE_STRING);
	$recurring_due_date_type = filter_var($_POST['recurring_due_date_type'],FILTER_SANITIZE_STRING);
	$recurring_due_date_reminder = filter_var($_POST['recurring_due_date_reminder'],FILTER_SANITIZE_STRING);
	$recurring_due_date_email = filter_var($_POST['recurring_due_date_email'],FILTER_SANITIZE_STRING);

	if($manualid > 0) {
		$before_change = '';
		mysqli_query($dbc, "UPDATE `manuals` SET `category`='$category', `favourite`='$favourite', `heading_number`='$heading_number', `heading`='$heading', `sub_heading_number`='$sub_heading_number', `sub_heading`='$sub_heading', `third_heading_number`='$third_heading_number', `third_heading`='$third_heading', `description`='$description', `assign_staff`='$assign_staff', `deadline`='$deadline', `email_subject`='$email_subject', `email_message`='$email_message', `recurring_due_date` = '$recurring_due_date', `recurring_due_date_interval` = '$recurring_due_date_interval', `recurring_due_date_type` = '$recurring_due_date_type', `recurring_due_date_reminder` = '$recurring_due_date_reminder', `recurring_due_date_email` = '$recurring_due_date_email' WHERE `manualtypeid`='$manualid'");
		$history = "Manual Updated. <br />";
		add_update_history($dbc, 'hr_history', $history, '', $before_change);
	} else {
		mysqli_query($dbc, "INSERT INTO `manuals` (`category`, `favourite`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `description`, `assign_staff`, `deadline`, `email_subject`, `email_message`, `recurring_due_date`, `recurring_due_date_interval`, `recurring_due_date_type`, `recurring_due_date_reminder`, `recurring_due_date_email`)
			VALUES ('$category', '$favourite', '$heading_number', '$heading', '$sub_heading_number', '$sub_heading', '$third_heading_number', '$third_heading', '$description', ',$assign_staff,', '$deadline', '$email_subject', '$email_message', '$recurring_due_date', '$recurring_due_date_interval', '$recurring_due_date_type', '$recurring_due_date_reminder', '$recurring_due_date_email')");
		$manualid = mysqli_insert_id($dbc);
		$before_change = '';
		$history = "Manuals entry added. <br />";
		add_update_history($dbc, 'hr_history', $history, '', $before_change);
	}
	foreach($_FILES['hr_document']['name'] as $i => $file) {
		if($file != '') {
			$filename = file_safe_str($file);
			if(!file_exists('download')) {
				mkdir('download');
			}
			move_uploaded_file($_FILES['hr_document']['tmp_name'][$i],'download/'.$filename);
			mysqli_query($dbc, "INSERT INTO `manuals_upload` (`manualtypeid`,`type`,`upload`) VALUES ('$manualid','document','$filename')");
			$before_change = '';
			$history = "Manuals upload entry added. <br />";
			add_update_history($dbc, 'hr_history', $history, '', $before_change);
		}
	}
	foreach($_POST['hr_link'] as $i => $link) {
		if($link != '') {
			$link = filter_var($link,FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `manuals_upload` (`manualtypeid`,`type`,`upload`) VALUES ('$manualid','link','$link')");
			$before_change = '';
			$history = "Manuals upload entry added. <br />";
			add_update_history($dbc, 'hr_history', $history, '', $before_change);
		}
	}
	foreach($_FILES['hr_video']['name'] as $i => $file) {
		if($file != '') {
			$filename = file_safe_str($file);
			if(!file_exists('download')) {
				mkdir('download');
			}
			move_uploaded_file($_FILES['hr_video']['tmp_name'][$i],'download/'.$filename);
			mysqli_query($dbc, "INSERT INTO `manuals_upload` (`manualtypeid`,`type`,`upload`) VALUES ('$manualid','video','$filename')");
			$before_change = '';
			$history = "Manuals upload entry added. <br />";
			add_update_history($dbc, 'hr_history', $history, '', $before_change);
		}
	}
	foreach($_POST['assign_staff'] as $staff) {
		if($staff > 0) {
			mysqli_query($dbc, "INSERT INTO `manuals_staff` (`manualtypeid`, `staffid`) SELECT '$manualid', '$staff' FROM (SELECT COUNT(*) `rows` FROM `manuals_staff` WHERE `manualtypeid`='$manualid' AND `staffid`='$staff' AND `done`=0 AND `today_date` IS NULL) `num` WHERE `num`.`rows`=0");
			$before_change = '';
			$history = "Manuals upload entry added. <br />";
			add_update_history($dbc, 'hr_history', $history, '', $before_change);
		}
	}
	if($_POST['submit'] == 'email') {
		$heading = $third_heading != '' ? $third_heading_number.' '.$third_heading : ($sub_heading != '' ? $sub_heading_number.' '.$sub_heading : $heading_number.' '.$heading);
		$subject = str_replace(['[CATEGORY]','[HEADING]'],[$category,$heading],$email_subject);
		$body = html_entity_decode(str_replace(['[CATEGORY]','[HEADING]'],[$category,$heading],$email_message).'<p>Click <a href="'.WEBSITE_URL.'/HR/index.php?manual='.$manualid.'">here</a> to review the manual.</p>');
		foreach($_POST['assign_staff'] as $staff) {
			if($staff > 0) {
				$to = get_email($dbc, $staff);
				try {
					send_email('', $to, '', '', $subject, $body, '');
				} catch(Exception $e) { }
			}
		}
	}
	$back_url = '?tile_name='.$tile.'&tab='.$_POST['submit_url'];
	echo "<script> window.location.replace('".$back_url."'); </script>";
} ?>
<script>
    $(document).ready(function () {
            $('#deselect_all').click(function () {
                $('#assign_staff').val('').trigger("change");
            });
    });

    function changeCategory(category) {
        $.ajax({
            url: 'hr_ajax.php?action=set_category',
            method: 'POST',
            data: {
                category: category
            },
            success: function(response) {
                $('[name=heading_number]').html(response).trigger('change.select2');
            }
        });
    }
    function changeSection(section) {
        $.ajax({
            url: 'hr_ajax.php?action=set_manual_section',
            method: 'POST',
            data: {
                section: section,
                category: $('[name=category]').val()
            },
            success: function(response) {
                response = response.split('#*#');
                $('[name=heading]').val(response[0]);
                $('[name=sub_heading_number]').html(response[1]).trigger('change.select2');
            }
        });
    }
    function changeSubSection(subsection) {
        $.ajax({
            url: 'hr_ajax.php?action=set_manual_subsection',
            method: 'POST',
            data: {
                subsection: subsection,
                category: $('[name=category]').val()
            },
            success: function(response) {
                response = response.split('#*#');
                $('[name=sub_heading]').val(response[0]);
                $('[name=third_heading_number]').html(response[1]).trigger('change.select2');
            }
        });
    }
    function displayRecurringDueDate(chk) {
        if($(chk).is(':checked')) {
            $(chk).closest('.recurring_block').find('.recurring_due_date').show();
        } else {
            $(chk).closest('.recurring_block').find('.recurring_due_date').hide();
        }
    }
</script>
<?php $field_config = explode(',',get_config($dbc, 'hr_fields'));
$get_manual = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `manuals` WHERE `manualtypeid`='$manualid'"));
$fields = explode(',',$get_manual['fields']); ?>
<div class='scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen form-horizontal'>
		<form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
            <?php
            if(!empty($_GET['tab'])) {
                echo '<input type="hidden" name="submit_url" value="'.$_GET['tab'].'" />';
            }
            ?>
            <div class="standard-body-title">
                <h3><?= $manualid > 0 ? 'Edit' : 'Create' ?> Manual</h3>
            </div>
            <div class="standard-body-content pad-10">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Category:</label>
                    <div class="col-sm-8">
                        <select name="category" data-placeholder="Select a Category" class="chosen-select-deselect" onchange="changeCategory(this.value);"><option></option>
                            <?php foreach($categories as $cat_id => $category) {
                                if($cat_id != 'favourites') { ?>
                                    <option <?= $category == $get_manual['category'] ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Section:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Select Section" name="heading_number" class="chosen-select-deselect" <?= in_array('Sub Section Heading',$field_config) ? 'onchange="changeSection(this.value);"' : '' ?>><option></option>
                            <?php $heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading_number` FROM (SELECT `heading_number` FROM `hr` UNION SELECT `heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`heading_number`, 100, 0) IN (SELECT MAX(LPAD(`heading_number`, 100, 0)) FROM (SELECT `heading_number` FROM `hr` WHERE `deleted`=0 AND `category`='".$get_manual['category']."' UNION SELECT `heading_number` FROM `manuals` WHERE `deleted`=0 AND `category`='".$get_manual['category']."') `numbers`) GROUP BY `heading_number`"))['heading_number'] + 5;
                            for($i = 1; $i <= $heading_count; $i++) {
                                $heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `hr` WHERE `heading_number`='$i' AND `category`='".$get_manual['category']."' AND `deleted`=0 UNION SELECT `heading` FROM `manuals` WHERE `heading_number`='$i' AND `category`='".$get_manual['category']."' AND `deleted`=0"))['heading']; ?>
                                <option <?= $get_manual['heading_number'] == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i.' '.$heading ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Heading:</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="heading" value="<?= $get_manual['heading'] ?>">
                    </div>
                </div>
                <?php if(in_array('Sub Section Heading',$field_config)) {
                    $heading_number = $get_manual['heading_number'] == '' ? '1' : $get_manual['heading_number']; ?>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Sub Section:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Select Section" name="sub_heading_number" class="chosen-select-deselect" <?= in_array('Third Tier Heading',$field_config) ? 'onchange="changeSubSection(this.value);"' : '' ?>><option></option>
                                <?php $heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading_number`, `sub_heading_number` FROM (SELECT `heading_number`, `sub_heading_number` FROM `hr` UNION SELECT `heading_number`, `sub_heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`sub_heading_number`, 100, 0) IN (SELECT MAX(LPAD(`sub_heading_number`, 100, 0)) FROM (SELECT `sub_heading_number` FROM `hr` WHERE `deleted`=0 AND `category`='".$get_manual['category']."' AND `heading_number`='$heading_number' UNION SELECT `sub_heading_number` FROM `manuals` WHERE `deleted`=0 AND `category`='".$get_manual['category']."' AND `heading_number`='$heading_number') `numbers`) GROUP BY `sub_heading_number`"));
                                $heading_count = substr($heading_count['sub_heading_number'],strlen($heading_count['heading_number']) + 1) + 5;
                                for($i = 1; $i <= $heading_count; $i++) {
                                    $heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `sub_heading` FROM `hr` WHERE `sub_heading_number`='".$heading_number.'.'.$i."' AND `category`='".$get_manual['category']."' AND `deleted`=0 UNION SELECT `sub_heading` FROM `manuals` WHERE `sub_heading_number`='".$heading_number.'.'.$i."' AND `category`='".$get_manual['category']."' AND `deleted`=0"))['sub_heading']; ?>
                                    <option <?= $get_manual['sub_heading_number'] === $heading_number.'.'.$i ? 'selected' : '' ?> value="<?= $heading_number.'.'.$i ?>"><?= $heading_number.'.'.$i.' '.$heading ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Heading:</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="sub_heading" value="<?= $get_manual['sub_heading'] ?>">
                        </div>
                    </div>
                <?php } ?>
                <?php if(in_array('Third Tier Heading',$field_config)) {
                    $heading_number = $get_manual['sub_heading_number'] == '' ? '1.1' : $get_manual['sub_heading_number'];  ?>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Third Tier Section:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Select Section" name="third_heading_number" class="chosen-select-deselect"><option></option>
                                <?php $heading_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `third_heading_number`, `sub_heading_number` FROM (SELECT `third_heading_number`, `sub_heading_number` FROM `hr` UNION SELECT `third_heading_number`, `sub_heading_number` FROM `manuals`) `hr_manuals` WHERE LPAD(`third_heading_number`, 100, 0) IN (SELECT MAX(LPAD(`third_heading_number`, 100, 0)) FROM (SELECT `third_heading_number` FROM `hr` WHERE `deleted`=0 AND `category`='".$get_manual['category']."' AND `heading_number`='$heading_number' UNION SELECT `third_heading_number` FROM `manuals` WHERE `deleted`=0 AND `category`='".$get_manual['category']."' AND `sub_heading_number`='$heading_number') `numbers`) GROUP BY `third_heading_number`"));
                                $heading_count = substr($heading_count['third_heading_number'],strlen($heading_count['sub_heading_number']) + 1) + 5;
                                for($i = 1; $i <= $heading_count; $i++) {
                                    $heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `third_heading` FROM `hr` WHERE `third_heading_number`='".$heading_number.'.'.$i."' AND `category`='".$get_manual['category']."' AND `deleted`=0 UNION SELECT `third_heading` FROM `manuals` WHERE `third_heading_number`='".$heading_number.'.'.$i."' AND `category`='".$get_manual['category']."' AND `deleted`=0"))['third_heading']; ?>
                                    <option <?= $get_manual['third_heading_number'] === $heading_number.'.'.$i ? 'selected' : '' ?> value="<?= $heading_number.'.'.$i ?>"><?= $heading_number.'.'.$i.' '.$heading ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Heading:</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="third_heading" value="<?= $get_manual['third_heading'] ?>">
                        </div>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label class="col-sm-4 control-label">Detail:</label>
                    <div class="col-sm-8">
                        <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $get_manual['description']; ?></textarea>
                    </div>
                </div>

                <?php $uploads = mysqli_query($dbc, "SELECT `uploadid`, `upload`,`type` FROM `manuals_upload` WHERE `manualtypeid`='$manualid'");
                if(mysqli_num_rows($uploads) > 0) {
                    echo "<ul>";
                    while($upload = mysqli_fetch_assoc($uploads)) { ?>
                        <li><?php switch($upload['type']) {
                            case 'document':
                                echo 'Document: ';
                                echo '<a href="download/'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a> - <a href="add_manual.php?action=delete&manual_uploadid='.$upload['uploadid'].'&manualtypeid='.$manualid.'&type=document" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                                break;
                            case 'link':
                                echo 'Link: ';
                                echo '<a href="'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a> - <a href="add_manual.php?action=delete&manual_uploadid='.$upload['uploadid'].'&manualtypeid='.$manualid.'&type=link" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                                break;
                            case 'video':
                                echo 'Video: ';
                                echo '<a href="download/'.$upload['upload'].'" target="_blank">'.$upload['upload'].'</a> - <a href="add_manual.php?action=delete&manual_uploadid='.$upload['uploadid'].'&manualtypeid='.$manualid.'&type=video" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                                break;
                        } ?></li>
                    <?php }
                    echo "</ul>";
                } ?>
                <?php if (in_array('Document',$field_config)) { ?>
                    <div class="form-group doc_group">
                        <label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span> Add Document(s):</label>
                        <div class="col-sm-7">
                            <input type="file" name="hr_document[]" class="form-control">
                        </div>
                        <div class="col-sm-1">
                            <img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="$('.doc_group').last().after($(this).closest('.doc_group').clone()); $('.doc_group').last().find('input').val('').focus();">
                        </div>
                    </div>
                <?php } ?>

                <?php if (in_array('Link',$field_config)) { ?>
                    <div class="form-group link_group">
                        <label class="col-sm-4 control-label">Add Link(s):<br><em>(e.g. - https://www.google.com)</em></label>
                        <div class="col-sm-7">
                            <input type="text" name="hr_link[]" class="form-control">
                        </div>
                        <div class="col-sm-1">
                            <img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="$('.link_group').last().after($(this).closest('.link_group').clone()); $('.link_group').last().find('input').val('').focus();">
                        </div>
                    </div>
                <?php } ?>

                <?php if (in_array('Videos',$field_config)) { ?>
                    <div class="form-group video_group">
                        <label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span> Add Video(s):</label>
                        <div class="col-sm-7">
                            <input type="file" name="hr_video[]" class="form-control">
                        </div>
                        <div class="col-sm-1">
                            <img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="$('.video_group').last().after($(this).closest('.video_group').clone()); $('.video_group').last().find('input').val('').focus();">
                        </div>
                    </div>
                <?php } ?>

                <?php if (in_array('Staff',$field_config)) { ?>
                    <script>
                    function add_staff(img) {
                        var block = $(img).closest('.form-group');
                        destroyInputs();
                        var clone = block.clone();
                        clone.find('select').val('');
                        block.after(clone);
                        initInputs();
                        initTooltips();
                    }
                    function rem_staff(img) {
                        if($('[name="assign_staff[]"]').length <= 1) {
                            add_staff(img);
                        }
                        $(img).closest('.form-group').remove();
                        initTooltips();
                    }
                    </script>
                    <?php $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status > 0"));
                    foreach(empty(trim($get_manual['assign_staff'],',')) ? array_column($staff_list,'contactid') : array_unique(explode(',',$get_manual['assign_staff'])) as $distinct_staffid) { ?>
                        <div class="form-group clearfix completion_date">
                            <div class="pull-right" style="width:5em;">
                                <img src="../img/icons/ROOK-add-icon.png" class="inline-img cursor-hand no-toggle" title="Assign Additional Staff" onclick="add_staff(this);">
                                <img src="../img/remove.png" class="inline-img cursor-hand no-toggle" title="Remove Staff" onclick="rem_staff(this);">
                            </div>
                            <div class="scale-to-fill">
                                <label class="col-sm-4 control-label text-right">Assign Staff:</label>
                                <div class="col-sm-8"><!--<?= "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status > 0" ?>-->
                                    <select id = "assign_staff" name="assign_staff[]" data-placeholder="Choose a Staff Member..." class="chosen-select-deselect form-control">
                                        <?php foreach($staff_list as $row) { ?>
                                            <option <?= ($row['contactid'] == $distinct_staffid) ? 'selected' : '' ?> value="<?= $row['contactid']; ?>"><?= $row['full_name']; ?></option><?php
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

                <?php if (in_array('Review Deadline',$field_config)) { ?>
                    <div class="form-group clearfix">
                        <label class="col-sm-4 control-label text-right">Review Deadline:</label>
                        <div class="col-sm-8">
                            <input name="deadline" type="text" class="form-control datepicker" value="<?= $get_manual['deadline'] ?>"></p>
                        </div>
                    </div>
                <?php } ?>

                <?php if (in_array('Recurring Due Dates',$field_config)) { ?>
                    <h4>Recurring Due Dates</h4>
                    <div class="recurring_block">
                        <div class="form-group clearfix">
                            <label class="col-sm-4 control-label text-right">Recurring Due Dates:</label>
                            <div class="col-sm-8">
                                <label class="form-checkbox"><input type="checkbox" name="recurring_due_date" value="1" <?= !empty($get_manual['recurring_due_date']) ? 'checked' : '' ?> onchange="displayRecurringDueDate(this);"> Enable</label>
                            </div>
                        </div>
                        <div class="recurring_due_date" <?= !empty($get_manual['recurring_due_date']) ? '' : 'style="display:none;"' ?>>
                            <div class="form-group clearfix">
                                <label class="col-sm-4 control-label text-right">Recurring Due Date Interval:</label>
                                <div class="col-sm-8">
                                    <input type="number" name="recurring_due_date_interval" value="<?= $get_manual['recurring_due_date_interval'] ?>" <?= !empty($get_manual['recurring_due_date']) ? 'min="1"' : '' ?> class="form-control">
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="col-sm-4 control-label text-right">Recurring Due Date Type:</label>
                                <div class="col-sm-8">
                                    <select name="recurring_due_date_type" class="chosen-select-deselect form-control">
                                        <option></option>
                                        <option value="days" <?= $get_manual['recurring_due_date_type'] == 'days' ? 'selected' : ''?>>Days</option>
                                        <option value="weeks" <?= $get_manual['recurring_due_date_type'] == 'weeks' ? 'selected' : ''?>>Weeks</option>
                                        <option value="months" <?= $get_manual['recurring_due_date_type'] == 'months' ? 'selected' : ''?>>Months</option>
                                        <option value="years" <?= $get_manual['recurring_due_date_type'] == 'years' ? 'selected' : ''?>>Years</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="col-sm-4 control-label text-right">Create Reminder On Recurring Date:</label>
                                <div class="col-sm-8">
                                    <label class="form-checkbox"><input type="checkbox" name="recurring_due_date_reminder" value="1" <?= !empty($get_manual['recurring_due_date_reminder']) ? 'checked' : '' ?>> Enable</label>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="col-sm-4 control-label text-right">Send Email On Recurring Date:</label>
                                <div class="col-sm-8">
                                    <label class="form-checkbox"><input type="checkbox" name="recurring_due_date_email" value="1" <?= !empty($get_manual['recurring_due_date_email']) ? 'checked' : '' ?>> Enable</label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Configure Email -->
                <?php if (in_array('Configure Email',$field_config)) { ?>
                    <h4>Email on Assignment of Manual</h4>
                    <div class="form-group clearfix">
                        <label for="email_subject" class="col-sm-4 control-label text-right">Email Subject:</label>
                        <div class="col-sm-8"><input class="form-control" name="email_subject" type="text" value="<?= $get_manual['email_subject'] ?>"></div>
                    </div>
                    <div class="form-group clearfix">
                        <label for="email_message" class="col-sm-4 control-label text-right">Email Message:</label>
                        <div class="col-sm-8"><textarea name="email_message"><?= html_entity_decode($get_manual['email_message']) ?></textarea></div>
                    </div>
                <?php } ?>
                <button class="pull-right btn brand-btn" name="submit" value="">Submit</button>
                <button class="pull-right btn brand-btn" name="submit" value="email">Send Email</button>
                <div class="clearfix"></div>
            </div>
		</form>
	</div>
</div>
