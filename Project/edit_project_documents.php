<div id="head_documents" class="form-horizontal col-sm-12" data-tab-name="documents">
    <?php include_once('../include.php');
    error_reporting(0);
    if(!isset($security)) {
        $security = get_security($dbc, $tile);
        $strict_view = strictview_visible_function($dbc, 'project');
        if($strict_view > 0) {
            $security['edit'] = 0;
            $security['config'] = 0;
        }
    }
    if(!isset($project)) {
        $projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
        $project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid' AND '$projectid' > 0"));
    }
    $documents = mysqli_query($dbc, "SELECT * FROM `project_document` WHERE `projectid`='$projectid' AND '$projectid' > 0 AND `deleted`=0 ORDER BY `category`") ?>
    <script>
    $(document).ready(function() {
        $('input[type=file]').change(uploadFile);
        <?php if ( !empty($salesid) ) { ?>
            var checkid = setInterval(function() {
                if($('[name=projectid]').val() > 0) {
                    clearInterval(checkid);
                    uploadSalesDocs();
                }
            }, 3000);
        <?php } ?>
    });
    $(document).on('change', 'select.category1', function() { checkValue(this); });
    $(document).on('change', 'select.category2', function() { checkValue(this); setCategory(this); });
    function uploadFile() {
        var uploaded = 0;
        var filecount = this.files.length;
        for(var i = 0; i < filecount; i++) {
            var file = new FormData();
            var file_data = this.files[i];
            file.append('file',this.files[i]);
            file.append('table','project_document');
            file.append('type',$('input[name=upload]').data('type'));
            file.append('project','<?= $projectid ?>');
            $.ajax({
                url: 'projects_ajax.php?action=project_uploads',
                method: 'POST',
                processData: false,
                contentType: false,
                data: file,
                xhr: function() {
                    var num_label = i;
                    var filename = this.data.get('file').name;
                    $('input[name=upload]').hide().after('<div style="background-color:#000;height:1.5em;padding:0;position:relative;width:100%;"><div style="background-color:#444;height:1.5em;left:0;position:absolute;top:0;" id="progress_'+num_label+'"></div><span id="label_'+num_label+'" style="color:#fff;left:0;position:absolute;text-align:center;top:0;width:100%;z-index:1;">'+filename+': 0%</span></div><div class="clearfix"></div>');
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(e){
                        var percentComplete = Math.round(e.loaded / e.total * 100);
                        $('#label_'+num_label).text(filename+': '+percentComplete+'%');
                        $('#progress_'+num_label).css('width',percentComplete+'%');
                        console.log(filename+': '+percentComplete+'%');
                    }, false);

                    return xhr;
                },
                success: function(response) {
                    if(++uploaded == filecount) {
                        reloadDocuments();
                    }
                }
            });
        }
    }
    function checkValue(select) {
        if(select.value == 'MANUAL') {
            $(select).next().hide().parent().find('input[name=category]').show().focus();
        }
    }
    function setCategory(input) {
        $('select[name=category]').prepend('<option value="'+input.value+'">'+input.value+'</option>').trigger('change.select2');
        $(input).closest('.add_doc').find('input').data('type',input.value).data('type-field','category');
    }
    function uploadSalesDocs() {
        var projectid = $('input[name=projectid]').val();
        var salesid = '<?=$salesid?>';
        $.ajax({
            url: 'projects_ajax.php?action=sales_docs_upload',
            method: 'POST',
            data: {
                projectid: projectid,
                salesid: salesid
            },
            success: function(response) {
                reloadDocuments();
            }
        });
    }
    function reloadDocuments() {
        $.get('../Project/edit_project_documents.php',{ projectid: '<?= $projectid ?>'}, function(response) {
            $('#head_documents').html($(response));
        });
    }
    var linkSaveInterval = null;
    function saveLink() {
        linkSaveInterval = setInterval(function() {
            if(saving_field == null && current_fields.length == 0) {
                clearInterval(linkSaveInterval);
                reloadDocuments();
            }
        }, 250);
    }
    </script>
	<h3><?= PROJECT_NOUN ?> Documents</h3>
	<div class="notice double-gap-top double-gap-bottom popover-examples">
		<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
		<div class="col-sm-11"><span class="notice-name">NOTE: </span>Add and view all documents and links added to this project.</div>
		<div class="clearfix"></div>
	</div>
	<div id="no_more_tables">
        <?php if(mysqli_num_rows($documents) > 0) { ?>
            <table class="table table-bordered">
				<tr class="hidden-sm hidden-xs">
					<th>Document</th>
					<th>Category</th>
					<th>Added By</th>
					<th></th>
				</tr>
			<?php while($document = mysqli_fetch_array($documents)) { ?>
				<tr>
					<td data-title="Document"><?php if($document['upload'] != '') { ?>
						<a href="download/<?= $document['upload'] ?>"><?= $document['label'] == '' ? $document['upload'] : $document['label'] ?></a>
					<?php } else if($document['link'] != '') { ?>
						<a href="<?= (strpos($document['link'],'http') === FALSE ? 'http://' : '').$document['link'] ?>"><?= $document['label'] == '' ? $document['link'] : $document['label'] ?></a>
					<?php } ?>
					<?php if($security['edit'] > 0) { ?>
						<input type="text" name="label" class="form-control" value="<?= empty($document['label']) ? (empty($document['upload']) ? $document['link'] : $document['upload']) : $document['label'] ?>" data-table="project_document" data-id-field="uploadid" data-id="<?= $document['uploadid'] ?>" style="display:none;" onblur="$(this).closest('td').find('a').show().first().text(this.value); $(this).hide();">
						<a href="" onclick="$(this).closest('td').find('a').hide(); $(this).closest('td').find('[name=label]').show().focus(); return false;"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img"></a>
					<?php } ?>
					</td>
					<td data-title="Category" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><select name="category" class="chosen-select-deselect form-control category1" data-placeholder="Select a Category" data-table="project_document" data-id-field="uploadid" data-id="<?= $document['uploadid'] ?>">
						<option></option>
						<option value="MANUAL">Add New</option>
						<?php $doc_cats = mysqli_query($dbc, "SELECT `category` FROM `project_document` WHERE `category` != '' GROUP BY `category` ORDER BY `category`");
						while($doc_cat = mysqli_fetch_array($doc_cats)[0]) { ?>
							<option <?= $document['category'] == $doc_cat ? 'selected' : '' ?> value="<?= $doc_cat ?>"><?= $doc_cat ?></option>
						<?php } ?>
					</select><input type="text" name="category" data-table="project_document" data-id-field="uploadid" data-id="<?= $document['uploadid'] ?>" placeholder="Category" class="form-control" style="display:none;" onchange="setCategory(this);"></td>
					<td data-title="Added By"><?= get_contact($dbc, $document['created_by']) ?></td>
					<td data-title="">
						<input type="hidden" name="deleted" value="1" data-table="project_document" data-id-field="uploadid" data-id="<?= $document['uploadid'] ?>">
						<a href="" onclick="$('.add_doc').show(); return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img"></a>
						<a href="" onclick="$(this).closest('td').find('[name=deleted]').change(); $(this).closest('tr').remove(); return false;"><img src="../img/remove.png" class="inline-img"></a>
                    </td>
				</tr>
			<?php } ?>
            </table>
        <?php } else { ?>
            <a href="" class="btn brand-btn pull-left" onclick="$('.add_doc').show(); $(this).hide(); return false;">Add Document or Link</a>
        <?php } ?>
	</div>
	<?php if($security['edit'] > 0) { ?>
		<div class="add_doc" style="display:none;">
            <div class="form-group">
                <label class="col-sm-4">Category:</label>
                <div class="col-sm-8">
                    <select name="category" class="chosen-select-deselect form-control category2" data-placeholder="Select a Category">
                        <option></option>
                        <option value="MANUAL">Add New</option>
                        <?php $doc_cats = mysqli_query($dbc, "SELECT `category` FROM `project_document` WHERE `category` != '' GROUP BY `category` ORDER BY `category`");
                        while($doc_cat = mysqli_fetch_array($doc_cats)[0]) { ?>
                            <option <?= $document['category'] == $doc_cat ? 'selected' : '' ?> value="<?= $doc_cat ?>"><?= $doc_cat ?></option>
                        <?php } ?>
                    </select><input type="text" name="category" class="form-control" style="display:none;" placeholder="Category" onchange="setCategory(this);">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Type:</label>
                <div class="col-sm-8">
                    <select class="chosen-select-deselect" onchange="$('.doc_field,.link_field').hide(); if(this.value == 'link') { $('.link_field').show(); } else if(this.value == 'doc') { $('.doc_field').show(); }"><option />
                        <option value="doc">Add Document</option>
                        <option value="link">Add Link</option>
                    </select>
                </div>
            </div>
            <div class="form-group doc_field" style="display:none;">
                <label class="col-sm-4">Document:</label>
                <div class="col-sm-8">
                    <input type="file" multiple name="upload" class="form-control">
                </div>
            </div>
            <div class="form-group link_field" style="display:none;">
                <label class="col-sm-4">Link:</label>
                <div class="col-sm-8">
                    <input type="text" name="link" class="form-control" data-table="project_document" data-id-field="uploadid" data-project="<?= $projectid ?>">
                </div>
                <a href="" class="btn brand-btn pull-right" onclick="saveLink(); return false;">Save Link</a>
            </div>
            <?php $row_sales_docs_query = mysqli_query($dbc, "SELECT salesdocid, document_type, document FROM sales_document WHERE salesid='$salesid' AND `salesid` > 0 AND `deleted`=0");
            if ($row_sales_docs_query->num_rows>0) {
                echo '<ul>';
                while ($row_sales_doc=mysqli_fetch_assoc($row_sales_docs_query)) { ?>
                    <li><a href="../Sales/download/<?=$row_sales_doc['document']?>" target="_blank"><?=$row_sales_doc['document_type']?></a></li><?php
                }
                echo '</ul>';
            } ?>
		</div>
		<div class="clearfix"></div>
	<?php } ?>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_project_documents.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>