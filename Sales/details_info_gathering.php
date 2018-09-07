<?php include_once('../include.php');
include_once('../Sales/config.php');
if(empty($salesid)) {
	$salesid = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
} ?>
<!-- Information Gathering -->
<script type="text/javascript">
$(document).ready(function() {
    $('.add_row_infodoc').on( 'click', add_info_doc);
});
$(document).ready(function() {
    init_page();
});
function add_info_doc() {
    var clone = $('.additional_infodoc').clone();
    clone.find('.form-control').val('');
    clone.removeClass("additional_infodoc");
    $('#add_here_new_infodoc').append(clone);
    $('.add_row_infodoc').off('click',add_info_doc).on( 'click', add_info_doc);
    return false;
}
var reload_info = function() {
	$.get('details_info_gathering.php?id=<?= $salesid ?>', function(response) {
		$('#infogathering').parents('div').first().html(response);
	});
}
</script>

<div class="accordion-block-details padded" id="infogathering">
    <div class="accordion-block-details-heading"><h4>Information Gathering</h4></div>
    
    <div class="row">
        <div class="col-xs-12"><?php
            $result = mysqli_query($dbc, "SELECT * FROM `sales_document` WHERE `salesid`='{$salesid}' AND `document_type`='Information Gathering' AND `deleted`=0 AND `salesid` > 0 ORDER BY `salesdocid` DESC");
            if ( $result->num_rows > 0 ) {
                echo '
                    <br />
                    <table class="table table-bordered">
                        <tr class="hidden-xs hidden-sm">
                            <th>Document</th>
                            <th>Date</th>
                            <th>Uploaded By</th>
                            <th></th>
                        </tr>';
                
                while ( $row=mysqli_fetch_array($result) ) {
                    echo '<tr>';
                        $by = $row['created_by'];
                        $label = (empty($row['label']) ? $row['document'] : $row['label']);
                        echo '<td data-title="Document"><a href="download/'.$row['document'].'" target="_blank">'.$label.'</a>
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
            } ?>
        </div>
    </div><!-- .row -->
    
    <div class="row add_doc" style="<?= $result->num_rows > 0 ? 'display:none;' : '' ?>">
        <div class="col-xs-12 col-sm-4">
            <span class="popover-examples list-inline"><a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span>
            <b>Upload Document(s):</b>
        </div>
        
        <div class="col-xs-12 col-sm-5">
            <input name="document" multiple data-table="sales_document" data-id="" data-after="reload_info" data-type="Information Gathering" type="file" data-filename-placement="inside" class="form-control" />
        </div>
        <div class="clearfix"></div>
    </div><!-- .row -->
    
    <div class="row double-gap-top">
        <div class="col-sm-12 gap-md-left-15 set-row-height"><a href="<?= WEBSITE_URL; ?>/Information Gathering/infogathering.php?from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']); ?>" target="_blank">Click to View/Add Information Gathering</a></div>
        <div class="clearfix"></div>
    </div><!-- .row -->
</div>