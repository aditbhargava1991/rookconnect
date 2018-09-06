<!-- Tasks --><?php include_once('../Sales/config.php'); ?>
<div class="accordion-block-details padded" id="tasks">
    <div class="accordion-block-details-heading"><h4>Tasks</h4></div>

    <div class="row set-row-height">
        <div class="col-xs-12"><?php
            $result = mysqli_query($dbc, "SELECT `t`.`tasklistid`, `t`.`heading` FROM `tasklist` `t`, `sales` `s` WHERE ((`t`.`clientid`>0 AND `t`.`clientid` IN (`s`.`contactid`)) OR `t`.`businessid`=`s`.`businessid`) AND `s`.`salesid`='$salesid' GROUP BY `t`.`tasklistid`");
            if ( $result->num_rows > 0 ) {
                while ( $row=mysqli_fetch_assoc($result) ) { ?>
                    <a href="" onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Tasks_Updated/add_task.php?tasklistid=<?= $row['tasklistid'] ?>', '50%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">Task #<?= $row['tasklistid'] ?> : <?= html_entity_decode($row['heading']) ?></a><br /><?php
                }
            } else {
                echo 'No Record Found.';
            }
        ?>
        </div>
        <div class="clearfix double-gap-bottom"></div>
    </div>


    <?php $tasks_only = true;
    include('salespath.php'); ?>

</div><!-- .accordion-block-details -->