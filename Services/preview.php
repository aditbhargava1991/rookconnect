<!-- Service Preview --><?php
$serviceid = preg_replace('/[^0-9]/', '', $_GET['id']);
$result    = mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='{$serviceid}'"); ?>

<div class="standard-body full-height preview">
    <div class="standard-body-title hide-on-mobile" style="padding-top:0 !important;">
        <h3 class="double-pad-top pull-left">Service</h3>
        <a href="../Services/service.php?p=details&id=<?= $serviceid ?>" class="btn brand-btn pull-right gap-right" style="margin-top:35px;">Edit</a>
        <div class="clearfix"></div>
    </div>
    
    <div class="standard-body-content"><?php
        if ( $result->num_rows > 0 ) {
            while ( $row=mysqli_fetch_assoc($result) ) { ?>
                <div class="preview-block-details">
                    <div class="row padded">
                        <div class="col-sm-12 col-md-6">
                            <div class="row set-row-height">
                                <div class="col-xs-4 default-color">Service Type:</div>
                                <div class="col-xs-8"><?= $row['service_type']; ?></div>
                            </div>
                            <div class="row set-row-height">
                                <div class="col-xs-4 default-color">Category:</div>
                                <div class="col-xs-8"><?= $row['category']; ?></div>
                            </div>
                            <div class="row set-row-height">
                                <div class="col-xs-4 default-color">Heading:</div>
                                <div class="col-xs-8"><?= $row['heading']; ?></div>
                            </div>
                        </div>
                        <?php if($row['service_image'] != '' && file_exists('download/'.$row['service_image'])) { ?>
                            <div class="col-sm-12 col-md-6">
                                <img src="download/<?= $row['service_image'] ?>" style="max-width: 20em;">
                            </div>
                        <?php } ?>
                        <div class="col-sm-12 col-md-6">
                            <div class="col-xs-4 default-color">Description:</div>
                            <div class="col-xs-8"><?= !empty($row['description']) ? html_entity_decode($row['description']) : (!empty($row['ticket_description']) ? html_entity_decode($row['ticket_description']) : (!empty($row['quote_description']) ? html_entity_decode($row['quote_description']) : (!empty($row['invoice_description']) ? html_entity_decode($row['invoice_description']) : ('-')))); ?></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div><!-- .preview-block-details --><?php
            }

        } else { ?>
            <div class="preview-block-details">No records found.</div><?php
        } ?>
    </div><!-- .standard-body-content -->
</div><!-- .standard-body -->