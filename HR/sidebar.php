<?php if(!IFRAME_PAGE) { ?>
	<ul class='sidebar hide-titles-mob collapsible <?= $tile == 'hr' ? '' : 'collapsed' ?>' style='padding-left: 15px;'>
		<?php if(get_config($dbc, 'hr_include_profile') == 1) {
			include('../Staff/field_list.php');
			$contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` LEFT JOIN `contacts_upload` ON `contacts`.`contactid`=`contacts_upload`.`contactid` WHERE `contacts`.`contactid`='".$_SESSION['contactid']."'"));
			$field_config_contacts = mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab` = 'Staff' AND `subtab` != 'hidden'");
			$completed_fields = 0;
			$all_fields = 0;
			$contact_fields = [];
			while($field_config_contact = mysqli_fetch_assoc($field_config_contacts)) {
				$contact_fields[] = $field_config_contact['contacts'];
			}
			$contact_fields = ','.implode(',', $contact_fields).',';
			$contact_tabs = ','.get_config($dbc, 'staff_field_subtabs').',';
			foreach($field_list as $staff_tab => $tab_list) {
				if(strpos($contact_tabs, ','.$staff_tab.',') !== FALSE) {
					foreach($tab_list as $staff_subtab => $subtab_list) {
						foreach($subtab_list as $field_key => $field_value) {
							$field_key = explode('#',$field_key)[0];
							if(strpos($contact_fields, ','.$field_value.',') !== FALSE && isset($contact[$field_key])) {
								$all_fields++;
								if(!empty(str_replace(['0000-00-00','0'],'',$contact[$field_key]))) {
									$completed_fields++;
								}
							}
						}
					}
				}
			}
			$percent_completed = round($completed_fields/$all_fields*100); ?>
			<a href="<?= WEBSITE_URL ?>/Profile/my_profile.php?edit_contact=true"><li class="">Profile (<?= $percent_completed ?>% Completed)</li></a>
		<?php } ?>
		<?php if(count($hr_summary) > 0) { ?>
			<a href="?tile_name=<?= $tile ?>&tab=summary"><li class="<?= 'summary' == $tab ? 'active blue' : '' ?>">Summary</li></a>
		<?php } ?>
		<?php foreach($categories as $cat_id => $label) {
			if($tab == $cat_id) {
				$tab_cat = $label;
			}
			if(($tile == 'hr' || $tile == $cat_id) && check_subtab_persmission($dbc, 'hr', ROLE, $label)) { ?>
				<a href="?tile_name=<?= $tile ?>&tab=<?= $cat_id ?>"><li class="<?= $cat_id == $tab ? 'active blue' : '' ?>"><?= $label ?></li></a>
			<?php }
		} ?>
		<?php $pr_fields = ','.get_config($dbc, 'performance_review_fields').',';
		if(strpos($pr_fields, ',Enable Performance Reviews,') !== FALSE) {
			if(tile_visible($dbc, 'preformance_review')) { ?>
				<a href="?performance_review=list"><li class="<?= $_GET['performance_review'] == 'list' ? 'active blue' : '' ?>">Performance Reviews</li></a>
				<?php if(isset($_GET['performance_review'])) { ?>
					<ul>
						<?php $pr_tab = $_GET['pr_tab'];
						$pr_positions = explode(',', get_config($dbc, 'performance_review_positions'));
						if(!empty(get_config($dbc, 'performance_review_positions'))) {
							foreach ($pr_positions as $pr_position) {
								if(check_subtab_persmission($dbc, 'preformance_review', ROLE, $pr_position)) { ?>
									<a href="?performance_review=list&pr_tab=<?= $pr_position ?>"><li class="<?= $pr_tab == $pr_position ? 'active blue' : '' ?>"><?= $pr_position ?></li></a>
								<?php }
							}
						} ?>
					</ul>
				<?php } ?>
			<?php }
		} ?>
	</ul>
<?php } ?>