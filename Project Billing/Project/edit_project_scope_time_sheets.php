<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
    $security = get_security($dbc, $tile);
    $strict_view = strictview_visible_function($dbc, 'project');
    if($strict_view > 0) {
        $security['edit'] = 0;
        $security['config'] = 0;
    }
}
if(!isset($projectid)) {
    $projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
    foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
        if($tile == 'project' || $tile == config_safe_str($type_name)) {
            $project_tabs[config_safe_str($type_name)] = $type_name;
        }
    }
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project');
$time_sheets_count = 0; ?>
<!-- <h3>Time Sheets</h3> -->
<?php 
$value_config_ts = explode(',',get_field_config($dbc, 'time_cards'));
if(!in_array('reg_hrs',$value_config_ts) && !in_array('direct_hrs',$value_config_ts)) {
    $value_config_ts = array_merge($value_config_ts,['reg_hrs','extra_hrs','relief_hrs','sleep_hrs','sick_hrs','sick_used','stat_hrs','stat_used','vaca_hrs','vaca_used']);
}
$time_sheets = mysqli_query($dbc, "SELECT `time_cards_id`, `staff`, `date`, SUM(IF(`type_of_time`='Regular Hrs.',`total_hrs`,0)) REG_HRS, SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
    SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
    SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
    SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
    SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
    SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER,
    GROUP_CONCAT(`comment_box` SEPARATOR ', ') COMMENTS,
    SUM(`timer_tracked`) TRACKED_HRS,
    SUM(IF(`type_of_time`='Direct Hrs.',`total_hrs`,0)) DIRECT_HRS, SUM(IF(`type_of_time`='Indirect Hrs.',`total_hrs`,0)) INDIRECT_HRS FROM `time_cards` WHERE `staff` IN ({$project['clientid']}) AND `approv`='N' AND `deleted`=0 GROUP BY CONCAT(`staff`,`date`) ORDER BY `date`");
$time_sheets_count += mysqli_num_rows($time_sheets);
if($time_sheets_count > 0) { ?>
    <table class="table table-bordered">
        <tr class='hidden-xs hidden-sm'>
            <th style='text-align:center; vertical-align:bottom; width:8em;'><div>Client</div></th>
            <th style='text-align:center; vertical-align:bottom; width:8em;'><div>Date</div></th>
            <?php if(in_array('total_tracked_hrs',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Total Tracked<br />Hours</div></th><?php } ?>
            <?php if(in_array('reg_hrs',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Regular<br />Hours</div></th><?php } ?>
            <?php if(in_array('direct_hrs',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Direct<br />Hours</div></th><?php } ?>
            <?php if(in_array('indirect_hrs',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Indirect<br />Hours</div></th><?php } ?>
            <?php if(in_array('extra_hrs',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Extra<br />Hours</div></th><?php } ?>
            <?php if(in_array('relief_hrs',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Relief<br />Hours</div></th><?php } ?>
            <?php if(in_array('sleep_hrs',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Sleep<br />Hours</div></th><?php } ?>
            <?php if(in_array('sick_hrs',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Sick Time<br />Adjustment</div></th><?php } ?>
            <?php if(in_array('sick_used',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Sick Hrs.<br />Taken</div></th><?php } ?>
            <?php if(in_array('stat_hrs',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Stat<br />Hours</div></th><?php } ?>
            <?php if(in_array('stat_used',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Stat. Hrs.<br />Taken</div></th><?php } ?>
            <?php if(in_array('vaca_hrs',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Vacation<br />Hours</div></th><?php } ?>
            <?php if(in_array('vaca_used',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Vacation<br />Hrs. Taken</div></th><?php } ?>
            <th style='text-align:center; vertical-align:bottom;'><div>Comments</div></th>
            <?php if(in_array('signature',$value_config_ts)) { ?><th style='text-align:center; vertical-align:bottom; width:2em;'><div>Parent/Guardian Signature</div></th><?php } ?>
        </tr>
        <?php while ($row = mysqli_fetch_array($time_sheets)) { ?>
            <tr style="text-align: center;">
                <td data-title="Client"><a href="../Timesheet/time_cards.php?search_client=<?= $row['staff'] ?>"><?= (!empty(get_client($dbc, $row['staff'])) ? get_client($dbc, $row['staff']) : get_contact($dbc, $row['staff'])) ?></a></td>
                <td data-title="Date"><?= $row['date'] ?></td> 
                <?php if(in_array('total_tracked_hrs',$value_config_ts)) { ?><td data-title="Total Tracked Hours"><?= $row['TRACKED_HRS'] ?></td><?php } ?>
                <?php if(in_array('reg_hrs',$value_config_ts)) { ?><td data-title="Regular Hours"><?= $row['REG_HRS'] ?></td><?php } ?>
                <?php if(in_array('direct_hrs',$value_config_ts)) { ?><td data-title="Direct Hours"><?= $row['DIRECT_HRS'] ?></td><?php } ?>
                <?php if(in_array('indirect_hrs',$value_config_ts)) { ?><td data-title="Indirect Hours"><?= $row['INDIRECT_HRS'] ?></td><?php } ?>
                <?php if(in_array('extra_hrs',$value_config_ts)) { ?><td data-title="Extra Hours"><?= $row['EXTRA_HRS'] ?></td><?php } ?>
                <?php if(in_array('relief_hrs',$value_config_ts)) { ?><td data-title="Relief Hours"><?= $row['RELIEF_HRS'] ?></td><?php } ?>
                <?php if(in_array('sleep_hrs',$value_config_ts)) { ?><td data-title="Sleep Hours"><?= $row['SLEEP_HRS'] ?></td><?php } ?>
                <?php if(in_array('sick_hrs',$value_config_ts)) { ?><td data-title="Sick Time Adjustment"><?= $row['SICK_ADJ'] ?></td><?php } ?>
                <?php if(in_array('sick_used',$value_config_ts)) { ?><td data-title="Sick Hours Taken"><?= $row['SICK_HRS'] ?></td><?php } ?>
                <?php if(in_array('stat_hrs',$value_config_ts)) { ?><td data-title="Stat Hours"><?= $row['STAT_AVAIL'] ?></td><?php } ?>
                <?php if(in_array('stat_used',$value_config_ts)) { ?><td data-title="Stat Hours Taken"><?= $row['STAT_HRS'] ?></td><?php } ?>
                <?php if(in_array('vaca_hrs',$value_config_ts)) { ?><td data-title="Vacation Hours"><?= $row['VACA_AVAIL'] ?></td><?php } ?>
                <?php if(in_array('vaca_used',$value_config_ts)) { ?><td data-title="Vacation Hours Taken"><?= $row['VACA_HRS'] ?></td><?php } ?>
                <td data-title="Comments"><?= html_entity_decode($row['COMMENTS']) ?></td>
                <?php if(in_array('signature',$value_config_ts)) { ?><td data-title="Parent/Guardian Signature">
                    <?php $signature = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `time_cards_signature` WHERE `contactid` = '".$row['staff']."' AND `date` = '".$row['date']."'"))['signature'];
                        if(!empty($signature)) { ?>
                            <img src="../Timesheet/download/<?= $signature ?>" style="height: 50%; width: auto;">
                        <?php } ?>
                </td><?php } ?>
            </tr>
        <?php } ?>
    </table>
<?php } else {
    echo "<h2>No Time Sheets Found</h2>";
} ?>
<?php include('next_buttons.php'); ?>