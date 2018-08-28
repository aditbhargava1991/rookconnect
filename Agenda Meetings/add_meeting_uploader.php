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
function removeDoc(button) {
    $(button).closest('.form-group').remove();
}
</script>
<div class="col-md-12">

    <?php
    if(!empty($_GET['agendameetingid'])) {
        $query_check_credentials = "SELECT * FROM agenda_meeting_upload WHERE meetingid='$agendameetingid'";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            while($row = mysqli_fetch_array($result)) {
                $amuploadid = $row['amuploadid'];
                echo '<ul>';
                echo '<li><a href="download/'.$row['upload_agenda_document'].'" target="_blank">'.$row['upload_agenda_document'].'</a> - <a href="add_agenda.php?amuploadid='.$amuploadid.'&agendameetingid='.$agendameetingid.'"> Delete</a></li>';
                echo '</ul>';
            }
        }
    }
    ?>

    <?php
    if(!empty($_GET['ticketid'])) {
        $query_check_credentials = "SELECT * FROM ticket_document WHERE ticketid='$ticketid' ORDER BY ticketdocid DESC";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered'>
            <tr class='hidden-xs hidden-sm'>
            <th>Type</th>
            <th>Document/Link</th>
            <th>Date</th>
            <th>Uploaded By</th>
            </tr>";
            while($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                $by = $row['created_by'];
                echo '<td data-title="Schedule">'.$row['type'].'</td>';
                if($row['document'] != '') {
                    echo '<td data-title="Schedule"><a href="download/'.$row['document'].'" target="_blank">'.$row['document'].'</a></td>';
                } else {
                    echo '<td data-title="Schedule"><a target="_blank" href=\''.$row['link'].'\'">Link</a></td>';
                }
                echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
                echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
                //echo '<td data-title="Schedule"><a href=\'delete_restore.php?action=delete&ticketdocid='.$row['ticketdocid'].'&ticketid='.$row['ticketid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
    ?>

    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
                <span class="popover-examples list-inline">&nbsp;
                <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
        </label>
        <div class="col-sm-8">
            <div class="enter_cost additional_doc clearfix">
                <div class="clearfix"></div>
                <div class="form-group clearfix">
                    <div class="col-xs-10">
                        <input name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                    </div>
                    <div class="col-xs-2">
                        <a id="add_row_doc" class="cursor-hand inline-img pull-left"><img src="../img/icons/ROOK-add-icon.png" /></a>
                        <a class="cursor-hand inline-img pull-left" onclick="removeDoc(this);"><img src="../img/remove.png" /></a>
                    </div>
                </div>
            </div>

            <div id="add_here_new_doc"></div>
        </div>
    </div>

</div>