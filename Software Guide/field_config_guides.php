<?php
/*
 * Field Config - Guides
 */

$guideid = preg_replace('/[^0-9]/', '', $_GET['guide']);
if ( !empty($guideid) ) {
    $additional_guide = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `additional_guide` FROM `local_software_guide` WHERE `guideid`='$guideid' AND `deleted`=0"))['additional_guide'];
    $guide = mysqli_fetch_assoc(mysqli_query($dbc_htg, "SELECT `tile`, `subtab` FROM `how_to_guide` WHERE `guideid`='$guideid'"));
} else {
    $additional_guide = '';
} ?>

<div class="standard-body-title">
    <?php if ( !empty($guide) ) { ?>
        <h3><?= $guide['tile'] . ': '. $guide['subtab'] ?></h3>
    <?php } ?>
</div>

<div class="standard-body-content" style="padding:1em;">
    <form method="post" action="">
        <input type="hidden" name="guideid" value="<?= $guideid ?>" />
        <div class="row">
            <div class="col-sm-12 gap-top"><label>Additional Software Guide:</label></div>
            <div class="col-sm-12"><textarea name="additional_guide"><?= html_entity_decode($additional_guide) ?></textarea></div>
            <div class="col-sm-12 gap-top">
                <div class="row">
                    <div class="col-sm-6"><a class="cursor-hand delete-additional-guide"><img src="../img/icons/ROOK-trash-icon.png" width="30" alt="Delete" /></a></div>
                    <div class="col-sm-6 text-right"><input type="submit" name="submit_guide" value="Submit" class="btn brand-btn" /></div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </form>
</div>