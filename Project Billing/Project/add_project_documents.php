<?php if($_GET['type'] == 'docs' && !empty($_POST['submit'])) {
	foreach($_FILES['document']['name'] as $i => $basename) {
		$label = filter_var($_POST['document_label'][$i],FILTER_SANITIZE_STRING);
		if($basename != '') {
			if (!file_exists('download')) {
				mkdir('download', 0777, true);
			}
			$basename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$basename);
			$j = 0;
			while(file_exists('download/'.$filename)) {
				$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$j.')$1', $basename);
			}
			if(!move_uploaded_file($_FILES['document']['tmp_name'][$i], 'download/'.$filename)) {
				echo "Error Saving Attachment: ".$filename."\n";
			} else {
				$query_insert_upload = "INSERT INTO `project_document` (`projectid`, `upload`, `label`) VALUES ('$projectid', '$filename', '$label')";
				$result_insert_upload = mysqli_query($dbc, $query_insert_upload);
			}
		}
	}
	foreach($_POST['support_link'] as $i => $link) {
		$link = filter_var($link, FILTER_SANITIZE_STRING);
		$label = filter_var($_POST['support_link_label'][$i],FILTER_SANITIZE_STRING);
		if($link != '') {
            $query_insert_client_doc = "INSERT INTO `project_document` (`projectid`, `link`, `label`) VALUES ('$projectid', '$link', '$label')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
		}
	}
} ?>
<script type="text/javascript">
$(document).ready(function() {
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

});
</script>

<?php if (strpos($value_config, ','."Documents Documents".',') !== FALSE) { ?>
<div class="form-group">
    <label for="additional_note" class="col-sm-4 control-label">Document(s)
    </label>
    <div class="col-sm-8">
        <?php
        if(!empty($_GET['projectid'])) {
            $projectid = $_GET['projectid'];

            $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM project_document WHERE projectid='$projectid' AND deleted = 0 AND upload IS NOT NULL"));

            if($get_doc['total_id'] > 0) {
                $result = mysqli_query($dbc, "SELECT upload, uploadid, `label` FROM project_document WHERE  projectid='$projectid' AND deleted = 0 AND upload IS NOT NULL");

                echo '<ul>';
                $i=0;
                while($row = mysqli_fetch_array($result)) {
                    $document = $row['upload'];
                    if($document != '') {
                        echo '<li><a href="download/'.$document.'" target="_blank">'.($row['label'] == '' ? $document : $row['label']).'</a>';
                        if($_GET['type'] != 'docs') {
                            echo ' - <a href="'.WEBSITE_URL.'/delete_restore.php?action=delete&uploadid='.$row['uploadid'].'&projectid='.$projectid.'&type='.$type.'" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                        }
                        echo '</li>';
                    }
                }
                echo '</ul>';
            }
        } ?>
		<div class="form-group clearfix">
			<label class="col-sm-5 text-center">Document</label>
			<label class="col-sm-7 text-center">Label</label>
		</div>
        <div class="enter_cost additional_doc clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix">
                <div class="col-sm-5">
                    <input name="document[]" type="file" data-filename-placement="inside" class="form-control" />
                </div>
                <div class="col-sm-7">
                    <input name="document_label[]" type="text" placeholder="Document Label" class="form-control">
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

<?php if (strpos($value_config, ','."Documents Links".',') !== FALSE) { ?>
<div class="form-group">
    <label for="additional_note" class="col-sm-4 control-label">Link(s):<br><em>(e.g. - https://www.google.com)</em>
    </label>
    <div class="col-sm-8">

        <?php
        if(!empty($_GET['projectid'])) {
            $projectid = $_GET['projectid'];

            $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM project_document WHERE projectid='$projectid' AND deleted = 0 AND link IS NOT NULL"));

            if($get_doc['total_id'] > 0) {
                $result = mysqli_query($dbc, "SELECT link, uploadid, label FROM project_document WHERE  projectid='$projectid' AND deleted = 0 AND link IS NOT NULL");

                echo '<ul>';
                $i=1;
                while($row = mysqli_fetch_array($result)) {
                    $link = $row['link'];
                    if($link != '') {
						if($row['label'] == '') {
							$link_name = explode('.', $row['link']);
							$www = explode('://',$link_name[0]);
							if($www[1] == 'www')
								$link_names = $link_name[1];
							elseif($www[0] == 'http')
								$link_names = $www[1];
							else
								$link_names = $row['link'];
						} else {
							$link_names = $row['label'];
						}
                        echo '<li><a target="_blank" href=\''.$row['link'].'\'">'.$link_names.'</a>';
                        if($_GET['type'] != 'docs') {
                            echo '- <a href="'.WEBSITE_URL.'/delete_restore.php?action=delete&uploadid='.$row['uploadid'].'&projectid='.$projectid.'&type='.$type.'" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                        }
                        echo '</li>';
                        $i++;
                    }
                }
                echo '</ul>';
            }
		} ?>
		<div class="form-group clearfix">
			<label class="col-sm-5 text-center">Link</label>
			<label class="col-sm-7 text-center">Label</label>
		</div>
        <div class="enter_cost additional_link clearfix">
            <div class="clearfix"></div>

            <div class="form-group clearfix">
                <div class="col-sm-5">
                    <input name="support_link[]" type="text" class="form-control">
                </div>
                <div class="col-sm-7">
                    <input name="support_link_label[]" type="text" placeholder="Link Label" class="form-control">
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
<?php if($_GET['type'] == 'docs') { ?>
	<div class="col-sm-12">
		<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
		<span class="popover-examples list-inline pull-right" style="margin:12px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click this to save the documents or links to the Project."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	</div>
<?php } ?>