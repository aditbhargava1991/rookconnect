<!-- Daysheet Notifications -->
<?php
if($daysheet_styling == 'card') {
    $row_open = '<div class="block-group-daysheet">';
    $row_close = '</div>';
} else {
    $row_open = '<li>';
    $row_close = '</li>';
}
?>
<div class="col-xs-12">
    <div class="weekly-div" style="overflow-y: hidden;">
        <?php
        $noti_list = mysqli_query($dbc, "SELECT * FROM (SELECT * FROM `journal_notifications` WHERE `contactid` = '$contactid' AND `seen` = 0 AND `deleted` = 0 ORDER BY `id` DESC) as new_noti UNION SELECT * FROM (SELECT * FROM `journal_notifications` WHERE `contactid` = '$contactid' AND `seen` = 1 AND `deleted` = 0 ORDER BY `id` DESC LIMIT 25) as old_noti");
        include('../Profile/daysheet_notifications_inc.php');
        echo $noti_html;
        mysqli_query($dbc, "UPDATE `journal_notifications` SET `seen` = 1 WHERE `contactid` = '$contactid'");
        ?>
    </div>
</div>