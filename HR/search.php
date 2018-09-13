<script>
function markFavourite(img) {
	$(img).find('.fave').toggle();
	$.ajax({
		url: 'hr_ajax.php?action=mark_favourite&user=<?= $_SESSION['contactid'] ?>&id='+$(img).data('id')+'&item='+$(img).data('type')
	});
}
function markPinned(img) {
	$(img).closest('.pull-right').find('.pinned').toggle().find('select').off('change',savePinned).change(savePinned);
	$(img).closest('.pull-right').css('width',$(img).find('.pinned').is(':visible') ? '50em' : '20em');
	$(window).resize();
}
function savePinned() {
	$.ajax({
		url: 'hr_ajax.php?action=mark_pinned',
		method: 'POST',
		data: {
			users: $(this).val(),
			id: $(this).data('id'),
			item: $(this).data('type')
		}
	});
	$(this).closest('.pinned').hide();
	$(this).closest('.pull-right').css('width','20em');
	$(window).resize();
}
function archive(type, id) {
	$.ajax({
		url: 'hr_ajax.php?action=archive',
		method: 'POST',
		data: {
			id: id,
			type: type
		},
		success: function(response) {
			// console.log(response);
			window.location.reload();
		}
	});
	return false;
}
</script>
<div class='scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen form-horizontal'>
		<?php $key = filter_var($_GET['key'],FILTER_SANITIZE_STRING);
        $sql = "SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE `deleted`=0 UNION
            SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE `deleted`=0";
        if(check_subtab_persmission($dbc, 'preformance_review', ROLE, $pr_position)) {
            $sql .= " UNION SELECT 'reviewid' `listing_type`, CONCAT(`reviewid`,'&performance_review=add&form_id=',`user_form_id`) `id`, 'Performance Review' `category`, '0' `heading_number`, `position` `heading`, '0' `sub_heading_number`, CONCAT(`today_date`,' Performance Review') `sub_heading`, '' `third_heading_number`, '' `third_heading`, '' `favourite`, '' `pinned`, 0 `pin`, '9999-12-31' `deadline` FROM `performance_review` LEFT JOIN `user_forms` ON `performance_review`.`user_form_id` = `user_forms`.`form_id` WHERE `performance_review`.`deleted` = 0";
        }
		$sql = "SELECT * FROM ($sql) `items` WHERE `heading` LIKE '%$key%' OR `sub_heading` LIKE '%$key%' OR `third_heading` LIKE '%$key%'
            ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)";
		$query = mysqli_query($dbc, $sql);
		$security_levels = get_security_levels($dbc);
		$today = date('Y-m-d');
		$category = $heading = $sub_heading = ''; ?>
		<div class='block-group form-list'>
			<?php if(mysqli_num_rows($query) > 0) {
				while($form = mysqli_fetch_assoc($query)) {
					if($form['listing_type'] == 'hr') {
						$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
					} else if($form['listing_type'] == 'manual') {
						$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
					}
					if($category != $form['category']) {
						$category = $form['category'];
						echo "<div class='heading'>$category</div>";
					}
					if($heading != ($form['heading_number'] > 0 ? $form['heading_number'].' ' : '').$form['heading'] && $form['sub_heading_number'] != '') {
						$heading = ($form['heading_number'] > 0 ? $form['heading_number'].' ' : '').$form['heading'];
						echo "<div class='sub-heading'>$heading</div>";
					}
					if($sub_heading != ($form['sub_heading_number'] > 0 ? $form['sub_heading_number'].' ' : '').$form['sub_heading'] && $form['third_heading_number'] != '') {
						$sub_heading = ($form['sub_heading_number'] > 0 ? $form['sub_heading_number'].' ' : '').$form['sub_heading'];
						echo "<div class='sub-heading' style='font-size: 1em'>$sub_heading</div>";
					}
					$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? ($form['sub_heading_number'] > 0 ? $form['sub_heading_number'].' ' : '').$form['sub_heading'] : ($form['heading_number'] > 0 ? $form['heading_number'].' ' : '').$form['heading']));
					echo "<div class='form'>";
					echo '<img class="pull-left inline-img neg-25-margin-vertical" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
					echo "<a href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>$form_name</a>";
					if($security['edit'] > 0) {
						echo '<a href="" onclick="return archive(\''.$form['listing_type'].'\',\''.$form['id'].'\');" class="pull-right"><img class="inline-img" src="../img/icons/ROOK-trash-icon.png"></a>';
						echo '<a href="?tile_name='.$tile.'&'.$form['listing_type'].'_edit='.$form['id'].'&back_url='.urlencode($_SERVER['REQUEST_URI']).'" class="pad-horizontal pull-right"><img class="inline-img" src="../img/icons/ROOK-edit-icon.png"></a>';
						echo '<span class="pull-right pad-horizontal" style="max-width:100%;"><img class="inline-img small" onclick="markPinned(this);" src="'.($form['pin'] > 0 ? '../img/pinned-filled.png' : '../img/pinned.png').'">';
						echo '<span class="pinned" style="display:none; width:20em; max-width:100%;"><select multiple data-placeholder="Select Users and Levels" data-id="'.$form['id'].'" data-type="'.$form['listing_type'].'" class="chosen-select-deselect"><option></option>';
						echo '<option '.(strpos(','.$form['pinned'].',', ',ALL,') !== FALSE ? 'selected' : '').' value="ALL">All Users</option>';
						foreach($security_levels as $level_label => $level_name) {
							echo '<option '.(strpos(','.$form['pinned'].',',','.$level_name.',') !== FALSE ? 'selected' : '').' value="'.$level_name.'">'.$level_label.'</option>';
						}
						foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1")) as $contact) {
							echo '<option '.(strpos(','.$form['pinned'].',', ','.$contact['contactid'].',') !== FALSE ? 'selected' : '').' value="'.$contact['contactid'].'">'.$contact['first_name'].' '.$contact['last_name'].'</option>';
						}
						echo '</select></span></span>';
					} else if($form['pin'] > 0) {
						echo '<span class="pull-right pad-horizontal"><img class="inline-img small" src="../img/pinned-filled.png"></span>';
					}
					echo '<span data-type="'.$form['listing_type'].'" data-id="'.$form['id'].'" class="pull-right neg-25-margin-vertical pad-horizontal" onclick="markFavourite(this);"><img class="inline-img fave" src="../img/blank_favourite.png" style="'.(strpos(','.$form['favourite'].',',','.$_SESSION['contactid'].',') !== false ? 'display:none;' : '').'"><img class="inline-img fave" src="../img/full_favourite.png" style="'.(strpos(','.$form['favourite'].',',','.$_SESSION['contactid'].',') !== false ? '' : 'display:none;').'"></span>';
					echo $today > $form['deadline'] ? ($assigned == '0' ? '<span class="text-red pull-right">Past Due</span>' : $assigned == '1' ? '' : '<span class="text-red pull-right">Review Needed</span>') : ($assigned == '' ? '<span class="text-blue pull-right">New</span>' : '');
					echo "<div class='clearfix'></div></div>";
				}
			} else { ?>
				<h3>No Search Results for "<?= $key ?>" Found<!--<?= $sql ?>--></h3>
			<?php } ?>
		</div>
	</div>
</div>
<div class='scale-to-fill has-main-screen show-on-mob'>
	<div class='main-screen form-horizontal'>
		<div class='block-group form-list' style="width:95vw;">
			<?php foreach($categories as $cat_id => $tab_cat) {
				if($tab_cat != 'Pinned' && $tab_cat != 'Favourites' && ($tile == 'hr' || $tile == $cat_id) && check_subtab_persmission($dbc, 'hr', ROLE, $label)) { ?>
					<h2><a href="?tile_name=<?= $_GET['tile_name'] ?>&mobile_cat=<?= $cat_id ?>"><?= $tab_cat ?></a></h2>
					<?php if($_GET['mobile_cat'] == $cat_id) {
						$sql = "SELECT * FROM (SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE `category`='$tab_cat' AND `deleted`=0 UNION
							SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE `category`='$tab_cat' AND `deleted`=0) `items`
							ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)";
						$query = mysqli_query($dbc, $sql);
						$security_levels = get_security_levels($dbc);
						$today = date('Y-m-d');
						$heading = $sub_heading = '';
						if(mysqli_num_rows($query) > 0) {
							$category = '';
							while($form = mysqli_fetch_assoc($query)) {
								if($form['listing_type'] == 'hr') {
									$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
								} else if($form['listing_type'] == 'manual') {
									$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
								}
								if($heading != $form['heading_number'].' '.$form['heading'] && $form['sub_heading_number'] != '') {
									$heading = $form['heading_number'].' '.$form['heading'];
									echo "<div class='heading'>$heading</div>";
								}
								if($sub_heading != $form['sub_heading_number'].' '.$form['sub_heading'] && $form['third_heading_number'] != '') {
									$sub_heading = $form['sub_heading_number'].' '.$form['sub_heading'];
									echo "<div class='sub-heading'>$sub_heading</div>";
								}
								$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));
								echo "<div class='form'>";
								echo '<img class="pull-left inline-img neg-25-margin-vertical" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
								echo "<a href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>$form_name</a>";
								if($security['edit'] > 0) {
									echo '<a href="" onclick="return archive(\''.$form['listing_type'].'\',\''.$form['id'].'\');" class="pull-right">Archive</a>';
									echo '<a href="?tile_name='.$tile.'&'.$form['listing_type'].'_edit='.$form['id'].'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;" class="pad-horizontal pull-right">Edit</a>';
									echo '<span class="pull-right pad-horizontal" style="max-width:100%;"><img class="inline-img small" onclick="markPinned(this);" src="'.($form['pin'] > 0 ? '../img/pinned-filled.png' : '../img/pinned.png').'">';
									echo '<span class="pinned" style="display:none; width:20em; max-width:100%;"><select multiple data-placeholder="Select Users and Levels" data-id="'.$form['id'].'" data-type="'.$form['listing_type'].'" class="chosen-select-deselect"><option></option>';
									echo '<option '.(strpos(','.$form['pinned'].',', ',ALL,') !== FALSE ? 'selected' : '').' value="ALL">All Users</option>';
									foreach($security_levels as $level_label => $level_name) {
										echo '<option '.(strpos(','.$form['pinned'].',',','.$level_name.',') !== FALSE ? 'selected' : '').' value="'.$level_name.'">'.$level_label.'</option>';
									}
									foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1")) as $contact) {
										echo '<option '.(strpos(','.$form['pinned'].',', ','.$contact['contactid'].',') !== FALSE ? 'selected' : '').' value="'.$contact['contactid'].'">'.$contact['first_name'].' '.$contact['last_name'].'</option>';
									}
									echo '</select></span></span>';
								} else if($form['pin'] > 0) {
									echo '<span class="pull-right pad-horizontal"><img class="inline-img small" src="../img/pinned-filled.png"></span>';
								}
								echo '<span data-type="'.$form['listing_type'].'" data-id="'.$form['id'].'" class="pull-right neg-25-margin-vertical pad-horizontal" onclick="markFavourite(this);"><img class="inline-img fave" src="../img/blank_favourite.png" style="'.(strpos(','.$form['favourite'].',',','.$_SESSION['contactid'].',') !== false ? 'display:none;' : '').'"><img class="inline-img fave" src="../img/full_favourite.png" style="'.(strpos(','.$form['favourite'].',',','.$_SESSION['contactid'].',') !== false ? '' : 'display:none;').'"></span>';
								echo $today > $form['deadline'] ? ($assigned == '0' ? '<span class="text-red pull-right">Past Due</span>' : $assigned == '1' ? '' : '<span class="text-red pull-right">Review Needed</span>') : ($assigned == '' ? '<span class="text-blue pull-right">New</span>' : '');
								echo "<div class='clearfix'></div></div>";
							}
						} else { ?>
							<h3>No <?= $tab_cat == 'pinned' ? 'Pinned' : ($tab_cat == 'favourites' ? 'Favourite' : $tab_cat) ?> Found</h3>
						<?php }
					}
				}
			} ?>
		</div>
	</div>
</div>