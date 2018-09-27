<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>'.CONTACTS_NOUN.' Notes</h3>') : '' ?>
<?php foreach($field_sort_order as $field_sort_field) { ?>
	<?php if ( strpos($value_config, ',Attached Business Notes,') !== false && $field_sort_field == 'Attached Business Notes' ) { ?>
		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label"><?= BUSINESS_CAT ?> Notes:</label>
		  <div class="col-sm-8">
			<?php $comments = '<p>'.html_entity_decode(implode('</p><p>',array_filter($dbc->query("SELECT `contacts_description`.`comments`, `contacts`.`description`, `contacts_description`.`general_comments`, `contacts_description`.`notes`, `contacts_description`.`service_notes` FROM `contacts` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` WHERE `contacts`.`contactid`='{$get_ticket['businessid']}'")->fetch_assoc()))).'</p>';
			if(strip_tags($comments) == '') {
				$comments = 'No Notes Found.';
			}
			echo $comments; ?>
		  </div>
		</div>
		<?php $pdf_contents[] = [BUSINESS_CAT.' Notes', $comments]; ?>
	<?php } ?>
	<?php if ( strpos($value_config, ',Attached Contact Notes,') !== false && $field_sort_field == 'Attached Contact Notes' ) { ?>
		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label"><?= CONTACTS_NOUN ?> Notes:</label>
		  <div class="col-sm-8">
			<?php $comments = '';
			foreach(array_filter(explode(',',$get_ticket['clientid'])) as $clientid_int) {
				if($clientid_int > 0) {
					$comments .= '<p>'.html_entity_decode(implode('</p><p>',array_filter($dbc->query("SELECT `contacts_description`.`comments`, `contacts`.`description`, `contacts_description`.`general_comments`, `contacts_description`.`notes`, `contacts_description`.`service_notes` FROM `contacts` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` WHERE `contacts`.`contactid`='{$get_ticket['clientid']}'")->fetch_assoc()))).'</p>';
				}
			}
			if(strip_tags($comments) == '') {
				$comments = 'No Notes Found.';
			}
			echo $comments; ?>
		  </div>
		</div>
		<?php $pdf_contents[] = [BUSINESS_CAT.' Notes', $comments]; ?>
	<?php } ?>
	<?php if ( strpos($value_config, ',Attached Contact Notes Add Note,') !== FALSE && $field_sort_field == 'Attached Contact Notes Add Note' && ($access_all > 0 || strpos($value_config, ',Attached Contact Notes Anyone Can Add,') !== FALSE) && !($strict_view > 0)) { ?>
		<a class="no-toggle" href="" title="Add a Note" onclick="addContactNote(this, '<?= $get_ticket['clientid'] ?>', <?= strpos($value_config, ',Attached Contact Notes Anyone Can Add,') !== FALSE ? 1 : 0 ?>); return false;"><img class="inline-img" data-history-label="<?= CONTACTS_NOUN ?> Note" src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" /></a>
		<div class="clearfix"></div>
	<?php } ?>
<?php } ?>
