<script type="text/javascript">
$(document).ready(function() {
    $('#add_row_doc').on( 'click', function () {
        var clone = $('.additional_doc').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_doc");
        $('#add_here_new_doc').append(clone);
        return false;
    });

});
</script>
<div class="col-md-12">
    <?php if(!empty($_GET['email_communicationid'])) {
        $query_check_credentials = "SELECT * FROM email_communicationid_upload WHERE email_communicationid='$email_communicationid' ORDER BY emailcommuploadid DESC";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered'>
            <tr class='hidden-xs hidden-sm'>
            <th>Document</th>
            <th>Date</th>
            <th>Uploaded By</th>
            </tr>";
            while($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                $by = $row['created_by'];
                echo '<td data-title="Schedule"><a href="download/'.$row['document'].'" target="_blank">'.$row['document'].'</a></td>';
                echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
                echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    } ?>

    <?php foreach(explode('#*#',$_GET['attach_docs']) as $doc_name) {
        $doc_name = urldecode($doc_name);
        if(file_exists($doc_name)) { ?>
            <div class="form-group">File to Attach: <a href="<?= $doc_name ?>"><?= $doc_name ?></a></div>
            <input type="hidden" name="email_file[]" value="<?= $doc_name ?>">
        <?php }
    } ?>
    
    <div class="form-group">
        <label for="additional_note" class="col-sm-12">
            Upload Document(s):
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span>
        </label>
        <div class="col-sm-12">
            <div class="enter_cost additional_doc clearfix">
                <div class="clearfix"></div>

                <div class="form-group clearfix">
                    <div class="col-xs-10">
                        <input name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                    </div>
                    <div class="col-xs-2 offset-top-5">
                        <a id="add_row_doc"><img src="../img/icons/ROOK-add-icon.png" width="20" alt="Add New Document" title="Add New Document" class="cursor-hand" /></a>
                    </div>
                </div>
            </div>

            <div id="add_here_new_doc"></div>
        </div>
    </div>
</div>