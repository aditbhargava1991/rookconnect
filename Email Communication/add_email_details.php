<script type="text/javascript">
$(document).ready(function() {
});
</script>

<div class="col-md-12">

       <?php if (strpos($value_config, ','."Subject".',') !== FALSE) { ?>
       <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Subject:</label>
        <div class="col-sm-8">
          <input name="subject" value="<?php echo $subject; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Body".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label">Body:</label>
        <div class="col-sm-8">
            <textarea name="email_body" rows="5" cols="50" class="form-control"><?php echo html_entity_decode(htmlspecialchars_decode($email_body)); ?></textarea>
            <?php if($ticketid > 0 && $communication_type == 'External') {
                $ticket_options = $dbc->query("SELECT GROUP_CONCAT(`fields` SEPARATOR ',') `field_config` FROM (SELECT `value` `fields` FROM `general_configuration` LEFT JOIN `tickets` ON `general_configuration`.`name` LIKE CONCAT('ticket_fields_',`tickets`.`ticket_type`) WHERE `ticketid`='$ticketid' UNION SELECT `tickets` `fields` FROM `field_config`) `options`")->fetch_assoc();
                if(strpos(','.$ticket_options['field_config'].',',',External Response,') !== FALSE) { ?>
                    <em>The following will be added to the end of your email:</em>
                    <p><a href="<?= WEBSITE_URL ?>/external_response.php?r=<?= urlencode(encryptIt(json_encode(['ticketid'=>$ticketid]))) ?>" target="_blank">You can reply to this message by clicking here.</a></p>
                <?php }
            } else if($ticketid > 0 && $communication_type == 'Internal') { ?>
                <em>The following will be added to the end of your email:</em>
                <p><a href="<?= WEBSITE_URL ?>/Ticket/index.php?edit=<?= $ticketid ?>" target="_blank">Click here</a> to view the <?= TICKET_NOUN ?></p>
            <?php } ?>
        </div>
      </div>
      <?php } ?>
</div>