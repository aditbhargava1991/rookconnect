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
<div class="col-md-12">
    <?php
    if(!empty($_GET['salesid'])) {
        $query_check_credentials = "SELECT * FROM sales_document WHERE salesid='$salesid' ORDER BY salesdocid DESC";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered'>
            <tr class='hidden-xs hidden-sm'>
            <th>Document/Link</th>
            <th>Date</th>
            <th>Uploaded By</th>
            </tr>";
            while($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                $by = $row['created_by'];
                if($row['document'] != '') {
                    echo '<td data-title="Schedule"><a href="download/'.$row['document'].'" target="_blank">'.(empty($row['label']) ? $row['document'] : $row['label']).'</a></td>';
                } else {
                    echo '<td data-title="Schedule"><a target="_blank" href=\''.$row['link'].'\'">'.(empty($row['label']) ? 'Link' : $row['label']).'</a></td>';
                }
                echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
                echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
                //echo '<td data-title="Schedule"><a href=\'delete_restore.php?action=delete&ticketdocid='.$row['ticketdocid'].'&salesid='.$row['salesid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
    ?>

    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
        </label>
        <div class="col-sm-8">
			<div class="col-sm-7 text-center">Document Label</div>
			<div class="col-sm-5 text-center">Document</div>

            <div class="enter_cost additional_doc clearfix">
                <div class="clearfix"></div>

                <div class="form-group clearfix">
                    <div class="col-sm-7">
                        <input name="document_label[]" type="text" placeholder="Reference Document Label" class="form-control" />
                    </div>
                    <div class="col-sm-5">
                        <input name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
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

    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">Link(s):<br><em>(e.g. - https://www.google.com)</em>
        </label>
        <div class="col-sm-8">
			<div class="col-sm-7 text-center">Link Label</div>
			<div class="col-sm-5 text-center">Link</div>

            <div class="enter_cost additional_link clearfix">
                <div class="clearfix"></div>

                <div class="form-group clearfix">
                    <div class="col-sm-7">
                        <input name="link_label[]" type="text" placeholder="Link Label" class="form-control" />
                    </div>
                    <div class="col-sm-5">
                        <input name="support_link[]" type="text" placeholder="Link" class="form-control">
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

</div>
