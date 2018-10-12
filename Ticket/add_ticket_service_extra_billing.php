<?php $display_none = '';
$num_extra_billing = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`ticketcommid`) `num_rows` FROM `ticket_comment` WHERE `ticketid` = '$ticketid' AND `deleted` = 0 AND `type` = 'service_extra_billing'"))['num_rows'];
if(strpos($value_config, ',Service Extra Billing Display Only If Exists,') !== FALSE) {
	if(!($num_extra_billing > 0)) {
		$display_none = 'style="display:none;"';
	}
} ?>

<div class="service_extra_billing" <?= $display_none ?>>
	<h3><?= (!empty($renamed_accordion) ? $renamed_accordion : 'Service Extra Billing') ?></h3>
	<?php if(strpos($value_config,',Service Extra Billing Add Option,') !== FALSE) { ?>
		 <a class="no-toggle" href="" title="Add <?= (!empty($renamed_accordion) ? $renamed_accordion : 'Service Extra Billing') ?>" onclick="addNote('service_extra_billing',this); return false;"><img class="inline-img" data-history-label="Service Extra Billing" src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" /></a>
	<?php } ?>
	<?php $extra_billings = mysqli_query($dbc, "SELECT * FROM `ticket_comment` WHERE `ticketid` = '$ticketid' AND '$ticketid' > 0 AND `type` = 'service_extra_billing' AND `deleted` = 0 ORDER BY `ticketcommid` DESC");
	echo '<div class="col-sm-12">
		<div class="note_block extra_billing">';
		if($num_extra_billing > 0) {
			$odd_even = 0;
            while($row = mysqli_fetch_assoc($extra_billings)) {
				$bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg';
                echo '<div class="'.$bg_class.'">';
                    echo profile_id($dbc, $row['created_by']);
                    echo '<div class="pull-right" style="width: calc(100% - 3.5em);">'.html_entity_decode($row['comment']);
                    echo "<em>Added by ".get_contact($dbc, $row['created_by'])." at ".$row['created_date'].'</em></div>';
                    echo '<div class="clearfix"></div>';
                echo '</div>';

				$pdf_content = '<div class="'.$bg_class.'">';
                    $pdf_content .= html_entity_decode($row['comment']);
                    $pdf_content .= "<em>Added by ".get_contact($dbc, $row['created_by'])." at ".$row['created_date'].'</em>';
                $pdf_content = '</div>';
				$pdf_contents[] = ['Extra Billing', $pdf_content];

                $odd_even++;
			}
		} else {
			echo '<h4>No Extra Billing Found.</h4>';
		}
		echo '</div>
	</div><div class="clearfix"></div>'; ?>
</div>