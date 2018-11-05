<?php include_once('../include.php');
include_once('../Sales/config.php');
if(empty($salesid)) {
	$salesid = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
} ?>
<!-- Lead Notes -->
<script type="text/javascript">
$(document).ready(function() {
    init_page();
});
var reload_notes = function() {
	$.get('details_lead_notes.php?id=<?= $salesid ?>', function(response) {
		$('#leadnotes').parents('div').first().html(response);
	});
}
</script>
<div class="accordion-block-details padded" id="leadnotes">
    <div class="accordion-block-details-heading"><h4>Lead Notes</h4></div>

    <div class="row">
        <div class="col-xs-12"><?php
            $result = mysqli_query($dbc, "SELECT * FROM `sales_notes` WHERE `salesid`='{$salesid}' AND `salesid` > 0 AND `deleted`=0 ORDER BY `salesnoteid` DESC");
            if($result->num_rows > 0) {
                $odd_even = 0;
                echo '
                    <br />
                    <table class="table table-bordered">
                        <tr class="hidden-xs hidden-sm">
                            <th>Note</th>
                            <th>Date</th>
                            <th>Assign To</th>
                            <th>Added By</th>
                            <th style="width: 6em;"></th>
                        </tr>';

                while($row = mysqli_fetch_array($result)) {
                    $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg';
                    echo '<tr class="'.$bg_class.'">';
                        $by = $row['created_by'];
                        $to = $row['email_comment'];
                        echo '<td data-title="Note">'. html_entity_decode($row['comment']) .'</td>';
                        echo '<td data-title="Date">'. $row['created_date'] .'</td>';
                        echo '<td data-title="Assign To">'. get_staff($dbc, $to) .'</td>';
                        echo '<td data-title="Added By">'. get_staff($dbc, $by) .'</td>';
                        echo '<td data-title="Function">
                            <input type="hidden" data-table="sales_notes" data-id="'.$row['salesnoteid'].'" name="deleted">
                            <img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="rem_doc(this);">
                            <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="add_note();">
                        </td>';
                    echo '</tr>';
                    $odd_even++;
                }

                echo '</table><br /><br />';
            } else { ?>
                <a href="" onclick="add_note(); return false;" class=""><img class="inline-img theme-color-icon" data-history-label="note" src="../img/icons/ROOK-add-icon.png"></a>
            <?php } ?>
        </div>
    </div>

</div><!-- .accordion-block-details -->
