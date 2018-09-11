<div id="inv_tickets">
    <h3><?= TICKET_TILE ?>
    <span class="popover-examples list-inline">
        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Add items from unbilled <?= TICKET_TILE ?> here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
    </span></h3>
    <div class="form-group ticket_option" <?= (in_array('unbilled_tickets',$field_config) ? '' : 'style="display:none;"') ?>>
        <div class="col-sm-12"><?php
            $db_config = explode(',',get_field_config($dbc, 'tickets_dashboard'));
            $tickets = $dbc->query("SELECT `tickets`.* FROM `tickets` LEFT JOIN `invoice` ON CONCAT(',',`invoice`.`ticketid`,',') LIKE CONCAT('%,',`tickets`.`ticketid`,',%') WHERE (`invoice`.`invoiceid` IS NULL OR `invoice`.`invoiceid` = '".$invoiceid."') ".($_GET['contactid'] > 0 ? "AND (CONCAT(',',IFNULL(`tickets`.`businessid`,''),',',IFNULL(`tickets`.`clientid`,''),',') LIKE '%,".filter_var($_GET['contactid'],FILTER_SANITIZE_STRING).",%' OR (IFNULL(`tickets`.`businessid`,0)=0 AND IFNULL(NULLIF(NULLIF(`tickets`.`clientid`,'0'),',,'),'')=''))" : "")." AND `tickets`.`deleted`=0 ".(in_array('Administration',$db_config) ?"AND IFNULL(`approvals`,'') != ''" : ''))->fetch_all(MYSQLI_ASSOC); ?>

            <?php foreach(explode(',', $get_invoice['ticketid']) as $invoice_ticketid) { ?>
                <div class="invoice_ticket form-group">
                    <label for="additional_note" class="col-sm-3 control-label">Unbilled <?= TICKET_TILE ?>:</label>
                    <div class="col-sm-8">
                        <select name="ticketid[]" data-placeholder="Select a <?= TICKET_NOUN ?>" class="chosen-select-deselect">
                            <option></option>
                            <?php foreach($tickets as $ticket) {
                                if($ticket['ticketid'] > 0) {
                                    echo '<option value="'.$ticket['ticketid'].'" '.($invoice_ticketid == $ticket['ticketid'] ? 'selected' : '').'>'.get_ticket_label($dbc, $ticket).'</option>';
                                }
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-1 adjust_block">
                        <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_ticket_row(this);">
                        <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_ticket_row();">
                    </div>
                    <div class="ticket_details">
                        <!-- Loaded from JavaScript -->
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>