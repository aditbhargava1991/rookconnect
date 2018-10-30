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
		<?php $sql = "";
		if($tab == 'pinned') {
			$sql = "SELECT * FROM (SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE (CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%') AND `deleted`=0 UNION
				SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE (CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%') AND `deleted`=0) `items`
				ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)";
		} else if($tab == 'favourites') {
			$sql = "SELECT * FROM (SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE CONCAT(',',`favourite`,',') LIKE '%,".$_SESSION['contactid'].",%' AND `deleted`=0 UNION
				SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE CONCAT(',',`favourite`,',') LIKE '%,".$_SESSION['contactid'].",%' AND `deleted`=0) `items`
				ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)";
		} else {
			$sql = "SELECT * FROM (SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE `category`='$tab_cat' AND `deleted`=0 UNION
				SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE `category`='$tab_cat' AND `deleted`=0) `items`
				ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)";
		}
		$query = mysqli_query($dbc, $sql);
		$today = date('Y-m-d');
		$heading = $sub_heading = ''; ?>
        <div class="standard-body-title">
            <h3><?= $tab_cat ?></h3>
        </div>
		<div class='standard-body-content form-list hr'>
			<?php if(mysqli_num_rows($query) > 0) {
				while($form = mysqli_fetch_assoc($query)) {
					if($form['listing_type'] == 'hr') {
						$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
					} else if($form['listing_type'] == 'manual') {
						$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
					}
					if($heading != '#'.$form['heading_number'].' '.$form['heading'] && $form['sub_heading_number'] != '') {
						$heading = '#'.$form['heading_number'].' '.$form['heading'];
						echo "<div class='heading'>$heading</div>";
					}
					if($sub_heading != '#'.$form['sub_heading_number'].' '.$form['sub_heading'] && $form['third_heading_number'] != '') {
						$sub_heading = '#'.$form['sub_heading_number'].' '.$form['sub_heading'];
						echo "<div class='sub-heading'>$sub_heading</div>";
					}
					$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));
					echo "<div class='form'>";
					echo '<img class="pull-left inline-img no-margin" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
					echo "<a class='gap-left' href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>$form_name</a>";
					if($security['edit'] > 0) {
						echo '<a href="" onclick="return archive(\''.$form['listing_type'].'\',\''.$form['id'].'\');" class="pull-right"><img class="inline-img" src="../img/icons/ROOK-trash-icon.png"></a>';
						echo '<a href="?tile_name='.$tile.'&'.$form['listing_type'].'_edit='.$form['id'].'&back_url='.urlencode($_SERVER['REQUEST_URI']).'" class="pad-horizontal pull-right"><img class="inline-img" src="../img/icons/ROOK-edit-icon.png"></a>';
						echo '<span class="pull-right pad-horizontal" style="max-width:100%;"><img class="inline-img small cursor-hand no-toggle" title="Pin This" onclick="overlayIFrameSlider(\'pin_form.php?id='.$form['id'].'&type='.$form['listing_type'].'&pinned='.$form['pinned'].'\', \'auto\', true, false);" src="'.($form['pin'] > 0 ? '../img/pinned-filled.png' : '../img/pinned.png').'"></span>';
					} else if($form['pin'] > 0) {
						echo '<span class="pull-right pad-horizontal"><img class="inline-img small cursor-hand" src="../img/pinned-filled.png"></span>';
					}
					echo '<span data-type="'.$form['listing_type'].'" data-id="'.$form['id'].'" class="pull-right neg-25-margin-vertical pad-horizontal" onclick="markFavourite(this);"><img class="inline-img fave cursor-hand no-toggle" title="Add to Favourites" src="../img/blank_favourite.png" style="'.(strpos(','.$form['favourite'].',',','.$_SESSION['contactid'].',') !== false ? 'display:none;' : '').'"><img class="inline-img fave cursor-hand no-toggle" title="Remove from Favourites" src="../img/full_favourite.png" style="'.(strpos(','.$form['favourite'].',',','.$_SESSION['contactid'].',') !== false ? '' : 'display:none;').'"></span>';
					echo $today > $form['deadline'] ? ($assigned == '0' ? '<span class="text-red pull-right">Past Due</span>' : $assigned == '1' ? '' : '<span class="text-red pull-right">Review Needed</span>') : ($assigned == '' ? '<span class="text-blue pull-right">New</span>' : '');
					echo "<div class='clearfix'></div></div>";
				}
			} else { ?>
				<h3>No <?= $tab == 'pinned' ? 'Pinned' : ($tab == 'favourites' ? 'Favourite' : $tab_cat) ?> Found</h3>
			<?php } ?>
		</div>
	</div>
</div>