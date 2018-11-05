<?php include_once('../include.php');
include_once('../Sales/config.php');
if(empty($salesid)) {
	$salesid = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
} ?>
<!-- Reference Documents -->
<script type="text/javascript">
$(document).ready(function() {
    init_page();
});
function addRowDoc() {
    var clone = $('.additional_doc').clone();
    clone.find('.form-control').val('');
    clone.removeClass("additional_doc");
    $('.additional_doc').last().after(clone);
    return false;
}
function addRowLink() {
    var clone = $('.additional_link').clone();
    clone.find('.form-control').val('');
    clone.removeClass("additional_link");
    $('.additional_link').last().after(clone);
    return false;
}
var reload_ref_docs = function() {
	$.get('details_ref_docs.php?id=<?= $salesid ?>', function(response) {
		$('#refdocs').parents('div').first().html(response);
	});
}
</script>

<div class="accordion-block-details padded" id="refdocs">
    <div class="accordion-block-details-heading"><h4>Reference Documents</h4></div>

    <div class="row"><?php
        if ( !empty($salesid) ) {
            $result = mysqli_query($dbc, "SELECT * FROM `sales_document` WHERE `salesid`='{$salesid}' AND (`document_type`='Reference Documents' OR IFNULL(`document_type`,'')='') AND `deleted`=0 AND `salesid` > 0 ORDER BY `salesdocid` DESC");
            if ( $result->num_rows > 0 ) {
                echo '
                    <table class="table table-bordered">
                        <tr class="hidden-xs hidden-sm">
                            <th>Document/Link</th>
                            <th>Date</th>
                            <th>Uploaded By</th>
                            <th></th>
                        </tr>';

                while ( $row=mysqli_fetch_array($result) ) {
                    echo '<tr>';
                        $by = $row['created_by'];
                        $label = (empty($row['label']) ? $row['document'].$row['link'] : $row['label']);
                        echo '<td data-title="Document"><a href="'.(!empty($row['document']) ? 'download/'.$row['document'] : $row['link']).'" target="_blank">'.$label.'</a>
                            <input type="text" class="form-control" data-table="sales_document" data-id="'.$row['salesdocid'].'" name="label" value="'.$label.'" onblur="$(this).hide(); $(this).closest(\'td\').find(\'a\').text(this.value).show(); $(this).closest(\'td\').find(\'img\').show();" style="display:none;">
                            <img src="../img/icons/ROOK-edit-icon.png" class="inline-img cursor-hand no-toggle" onclick="$(this).closest(\'td\').find(\'a,img\').hide();$(this).closest(\'td\').find(\'[name=label]\').show().focus();" title="Edit">
                        </td>';
                        echo '<td data-title="Date">'.$row['created_date'].'</td>';
                        echo '<td data-title="Uploaded By">'.get_staff($dbc, $by).'</td>';
                        echo '<td data-title="Function">
                            <input type="hidden" data-table="sales_document" data-id="'.$row['salesdocid'].'" name="deleted">
                            <img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="rem_doc(this);">
                            <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="add_doc(this);">
                            <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-email-icon.png" onclick="email_doc(this);">
                        </td>';
                    echo '</tr>';
                }

                echo '</table><br /><br />';
            }
        } ?>
    </div><!-- .row -->

    <div class="row add_doc" style="<?= $result->num_rows > 0 ? 'display:none;' : '' ?>">
        <div class="col-xs-12 col-sm-4">
            <span class="popover-examples list-inline"><a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span>
            <b>Upload Document(s):</b>
        </div>

        <div class="col-xs-12 col-sm-5">
            <input name="document" multiple data-table="sales_document" data-id="" data-after="reload_ref_docs" data-type="Reference Documents" type="file" data-filename-placement="inside" class="form-control" />
        </div>
				<?php $document_uploaded = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT document from sales_document WHERE document_type = 'Reference Documents' ORDER BY salesdocid DESC LIMIT 1"))['document']; ?>
				<a target="_blank" href="<?php echo $base_url . "/Sales/download/" . $document_uploaded; ?>">Reference document</a>
        <div class="clearfix"></div>

        <div class="col-xs-12 col-sm-4">
            <span class="popover-examples list-inline"><a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span>
            <b>Add Link:</b> <em>(eg. https://www.google.com)</em>
        </div>

        <div class="col-xs-12 col-sm-5">
            <input data-table="sales_document" data-id="" data-after="reload_ref_docs" name="link" type="text" placeholder="Link" class="form-control" />
        </div>
				<?php $link_uploaded = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT link from sales_document WHERE link != '' ORDER BY salesdocid DESC LIMIT 1"))['link']; ?>
				<a target="_blank" href="<?php echo $link_uploaded; ?>">Reference Link</a>
        <div class="clearfix"></div>
    </div><!-- .row -->

</div><!-- .accordion-block-details -->
