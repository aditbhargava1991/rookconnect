<?php
$search_user = $_SESSION['contactid'];    
include('../Notification/get_notifications.php');

if($num_rows_past > 0) {
    $alert_url = WEBSITE_URL.'/Daysheet/daysheet.php?side_content=past_due';
    $alert_img = WEBSITE_URL.'/img/alert.png';
} else if($num_rows > 0) {
    $alert_url = WEBSITE_URL.'/Daysheet/daysheet.php';
    $alert_img = WEBSITE_URL.'/img/alert-green.png';
} else {
    $alert_url = WEBSITE_URL.'/Daysheet/daysheet.php';
    $alert_img = WEBSITE_URL.'/img/alert-grey.png';
}

if($noti_count > 0 && $num_rows_past == 0) {
    $alert_url = WEBSITE_URL.'/Daysheet/daysheet.php?side_content=notifications';
}
?>

<?php
$alert_icon_show = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `alert_icon` FROM `user_settings` WHERE `contactid` = '".$search_user."'"))['alert_icon'];
if(empty($alert_icon_show)) { ?>
    <a href="<?= $alert_url ?>" class="alert-button planner-icon"><img src="<?= $alert_img ?>" alt="Planner" class="no-toggle" title="Planner" data-placement="bottom" />
        <?php if($noti_count > 0) { ?>
            <span class="planner-icon-notifications no-toggle" title="Notifications" data-placement="bottom"><?= $noti_count > 99 ? 99 : $noti_count ?></span>
        <?php } ?>
    </a>
<?php } ?>