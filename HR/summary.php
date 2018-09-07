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
<div class='scale-to-fill has-main-screen'>
	<div class='main-screen form-horizontal'>
		<h3>Summary</h3>
		<?php $total_height = 0;
		$blocks = [];
		if(in_array('individual_fave',$hr_summary)) {
			$height = 74;
			$query = $dbc->query("SELECT * FROM (SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE CONCAT(',',`favourite`,',') LIKE '%,".$_SESSION['contactid'].",%' AND `deleted`=0 UNION
				SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE CONCAT(',',`favourite`,',') LIKE '%,".$_SESSION['contactid'].",%' AND `deleted`=0) `items`
				ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)");
				$block = '<div class="overview-block">
					<h4>Favourites</h4>';
				if(mysqli_num_rows($query) > 0) {
					while($form = mysqli_fetch_assoc($query)) {
						$height += 18;
						if($form['listing_type'] == 'hr') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
						} else if($form['listing_type'] == 'manual') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
						}
						$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));
						$block .= "<div class='form'>";
						$block .= '<img class="pull-left inline-img neg-25-margin-vertical" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
						$block .= "<a href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>";
						if($form['sub_heading_number'] != '') {
							$block .= $form['heading_number'].' '.$form['heading'].' - ';
						}
						if($form['third_heading_number'] != '') {
							$block .= $form['sub_heading_number'].' '.$form['sub_heading'].' - ';
						}
						$block .= "$form_name</a>";
						$block .= '<span data-type="'.$form['listing_type'].'" data-id="'.$form['id'].'" class="pull-right neg-25-margin-vertical pad-horizontal" onclick="markFavourite(this);"><img class="inline-img fave" src="../img/blank_favourite.png" style="'.(strpos(','.$form['favourite'].',',','.$_SESSION['contactid'].',') !== false ? 'display:none;' : '').'"><img class="inline-img fave" src="../img/full_favourite.png" style="'.(strpos(','.$form['favourite'].',',','.$_SESSION['contactid'].',') !== false ? '' : 'display:none;').'"></span></div>';
					}
				} else {
					$height += 24;
					$block .= '<h5>No Pinned Forms Found</h5>';
				}
			$block .= '</div>';
			$blocks[] = [$height,$block];
			$total_height += $height;
		}
		if(in_array('individual_pin',$hr_summary)) {
			$height = 74;
			$query = $dbc->query("SELECT * FROM (SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE (CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%') AND `deleted`=0 UNION
				SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE (CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%') AND `deleted`=0) `items`
				ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)");
				$block = '<div class="overview-block">
					<h4>Pinned Forms</h4>';
				if(mysqli_num_rows($query) > 0) {
					while($form = mysqli_fetch_assoc($query)) {
						$height += 18;
						if($form['listing_type'] == 'hr') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
						} else if($form['listing_type'] == 'manual') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
						}
						$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));
						$block .= "<div class='form'>";
						$block .= '<img class="pull-left inline-img neg-25-margin-vertical" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
						$block .= "<a href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>";
						if($form['sub_heading_number'] != '') {
							$block .= $form['heading_number'].' '.$form['heading'].' - ';
						}
						if($form['third_heading_number'] != '') {
							$block .= $form['sub_heading_number'].' '.$form['sub_heading'].' - ';
						}
						$block .= "$form_name</a></div>";
					}
				} else {
					$height += 24;
					$block .= '<h5>No Pinned Forms Found</h5>';
				}
			$block .= '</div>';
			$blocks[] = [$height,$block];
			$total_height += $height;
		}
		if(in_array('individual',$hr_summary)) {
			$height = 74;
			$query = $dbc->query("SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE `deleted`=0 AND `hrid` IN (SELECT `hrid` FROM `hr_staff` WHERE `staffid`='".$_SESSION['contactid']."' AND `done`=0) ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)");
				$block = '<div class="overview-block">
					<h4>Incomplete Forms</h4>';
				if(mysqli_num_rows($query) > 0) {
					while($form = mysqli_fetch_assoc($query)) {
						$height += 18;
						if($form['listing_type'] == 'hr') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
						} else if($form['listing_type'] == 'manual') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
						}
						$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));
						$block .= "<div class='form'>";
						$block .= '<img class="pull-left inline-img neg-25-margin-vertical" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
						$block .= "<a href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>";
						if($form['sub_heading_number'] != '') {
							$block .= $form['heading_number'].' '.$form['heading'].' - ';
						}
						if($form['third_heading_number'] != '') {
							$block .= $form['sub_heading_number'].' '.$form['sub_heading'].' - ';
						}
						$block .= "$form_name</a></div>";
					}
				} else {
					$height += 24;
					$block .= '<h5>No Incomplete Forms Found</h5>';
				}
			$block .= '</div>';
			$blocks[] = [$height,$block];
			$total_height += $height;
			
			$height = 74;
			$query = $dbc->query("SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE `deleted`=0 AND `hrid` IN (SELECT `hrid` FROM `hr_staff` WHERE `staffid`='".$_SESSION['contactid']."' AND `done`=1 ORDER BY `hrstaffid` DESC LIMIT 0,5) ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)");
				$block = '<div class="overview-block">
					<h4>My Recent Forms</h4>';
				if(mysqli_num_rows($query) > 0) {
					while($form = mysqli_fetch_assoc($query)) {
						$height += 18;
						if($form['listing_type'] == 'hr') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
						} else if($form['listing_type'] == 'manual') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
						}
						$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));
						$block .= "<div class='form'>";
						$block .= '<img class="pull-left inline-img neg-25-margin-vertical" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
						$block .= "<a href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>";
						if($form['sub_heading_number'] != '') {
							$block .= $form['heading_number'].' '.$form['heading'].' - ';
						}
						if($form['third_heading_number'] != '') {
							$block .= $form['sub_heading_number'].' '.$form['sub_heading'].' - ';
						}
						$block .= "$form_name</a></div>";
					}
				} else {
					$height += 24;
					$block .= '<h5>No Recent Forms Found</h5>';
				}
			$block .= '</div>';
			$blocks[] = [$height,$block];
			$total_height += $height;
			
			$height = 74;
			$query = $dbc->query("SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE `deleted`=0 AND `manualtypeid` IN (SELECT `manualtypeid` FROM `manuals_staff` WHERE `staffid`='".$_SESSION['contactid']."' AND `done`=0) ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)");
				$block = '<div class="overview-block">
					<h4>Incomplete Manuals</h4>';
				if(mysqli_num_rows($query) > 0) {
					while($form = mysqli_fetch_assoc($query)) {
						$height += 18;
						if($form['listing_type'] == 'hr') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
						} else if($form['listing_type'] == 'manual') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
						}
						$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));
						$block .= "<div class='form'>";
						$block .= '<img class="pull-left inline-img neg-25-margin-vertical" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
						$block .= "<a href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>";
						if($form['sub_heading_number'] != '') {
							$block .= $form['heading_number'].' '.$form['heading'].' - ';
						}
						if($form['third_heading_number'] != '') {
							$block .= $form['sub_heading_number'].' '.$form['sub_heading'].' - ';
						}
						$block .= "$form_name</a></div>";
					}
				} else {
					$height += 24;
					$block .= '<h5>No Incomplete Manuals Found</h5>';
				}
			$block .= '</div>';
			$blocks[] = [$height,$block];
			$total_height += $height;
			
			$height = 74;
			$query = $dbc->query("SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE `deleted`=0 AND `manualtypeid` IN (SELECT `manualtypeid` FROM `manuals_staff` WHERE `staffid`='".$_SESSION['contactid']."' AND `done`=1 ORDER BY `manualstaffid` DESC LIMIT 0,5) ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)");
				$block = '<div class="overview-block">
					<h4>My Recent Manuals</h4>';
				if(mysqli_num_rows($query) > 0) {
					while($form = mysqli_fetch_assoc($query)) {
						$height += 18;
						if($form['listing_type'] == 'hr') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
						} else if($form['listing_type'] == 'manual') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
						}
						$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));
						$block .= "<div class='form'>";
						$block .= '<img class="pull-left inline-img neg-25-margin-vertical" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
						$block .= "<a href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>";
						if($form['sub_heading_number'] != '') {
							$block .= $form['heading_number'].' '.$form['heading'].' - ';
						}
						if($form['third_heading_number'] != '') {
							$block .= $form['sub_heading_number'].' '.$form['sub_heading'].' - ';
						}
						$block .= "$form_name</a></div>";
					}
				} else {
					$height += 24;
					$block .= '<h5>No Recent Manuals Found</h5>';
				}
			$block .= '</div>';
			$blocks[] = [$height,$block];
			$total_height += $height;
		}
		if(in_array('admin_recent',$hr_summary)) {
			$height = 74;
			$query = $dbc->query("SELECT 'hr' `listing_type`, `hrid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `hr` WHERE `deleted`=0 AND `hrid` IN (SELECT `hrid` FROM `hr_staff` WHERE `done`=1 ORDER BY `hrstaffid` DESC LIMIT 0,10) ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)");
				$block = '<div class="overview-block">
					<h4>All Recently Completed Forms</h4>';
				if(mysqli_num_rows($query) > 0) {
					while($form = mysqli_fetch_assoc($query)) {
						$height += 18;
						if($form['listing_type'] == 'hr') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
						} else if($form['listing_type'] == 'manual') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
						}
						$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));
						$block .= "<div class='form'>";
						$block .= '<img class="pull-left inline-img neg-25-margin-vertical" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
						$block .= "<a href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>";
						if($form['sub_heading_number'] != '') {
							$block .= $form['heading_number'].' '.$form['heading'].' - ';
						}
						if($form['third_heading_number'] != '') {
							$block .= $form['sub_heading_number'].' '.$form['sub_heading'].' - ';
						}
						$block .= "$form_name</a></div>";
					}
				} else {
					$height += 24;
					$block .= '<h5>No Recent Forms Found</h5>';
				}
			$block .= '</div>';
			$blocks[] = [$height,$block];
			$total_height += $height;
			
			$height = 74;
			$query = $dbc->query("SELECT 'manual' `listing_type`, `manualtypeid` `id`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading`, `favourite`, `pinned`, IF(CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%',1,0) `pin`, `deadline` FROM `manuals` WHERE `deleted`=0 AND `manualtypeid` IN (SELECT `manualtypeid` FROM `manuals_staff` WHERE `done`=1 ORDER BY `manualstaffid` DESC LIMIT 0,10) ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)");
				$block = '<div class="overview-block">
					<h4>All Recently Completed Manuals</h4>';
				if(mysqli_num_rows($query) > 0) {
					while($form = mysqli_fetch_assoc($query)) {
						$height += 18;
						if($form['listing_type'] == 'hr') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `hr_staff` WHERE `hrid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `hrstaffid` DESC"))['done'];
						} else if($form['listing_type'] == 'manual') {
							$assigned = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `done` FROM `manuals_staff` WHERE `manualtypeid`='".$form['id']."' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `manualstaffid` DESC"))['done'];
						}
						$form_name = ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading']));
						$block .= "<div class='form'>";
						$block .= '<img class="pull-left inline-img neg-25-margin-vertical" src="../img/'.($assigned == '1' ? 'checkmark.png' : 'error.png').'">';
						$block .= "<a href='?tile_name=".$tile."&".$form['listing_type']."=".$form['id']."' onclick='overlayIFrameSlider(this.href,\"auto\",true,true); return false;'>";
						if($form['sub_heading_number'] != '') {
							$block .= $form['heading_number'].' '.$form['heading'].' - ';
						}
						if($form['third_heading_number'] != '') {
							$block .= $form['sub_heading_number'].' '.$form['sub_heading'].' - ';
						}
						$block .= "$form_name</a></div>";
					}
				} else {
					$height += 24;
					$block .= '<h5>No Recent Manuals Found</h5>';
				}
			$block .= '</div>';
			$blocks[] = [$height,$block];
			$total_height += $height;
		}
		if(in_array('admin_progress',$hr_summary)) {
			$height = 74;
			$staff_list = sort_contacts_query($dbc->query("SELECT `contacts`.`contactid`,`contacts`.`name`,`contacts`.`first_name`,`contacts`.`last_name`,`contacts`.`category`,SUM(`forms`.`completed`) `complete`, COUNT(*) `total` FROM (SELECT CONCAT('hr',`hr`.`hrid`) `id`, MIN(`hr_staff`.`done`) `completed`, `hr_staff`.`staffid` FROM `hr_staff` LEFT JOIN `hr` ON `hr_staff`.`hrid`=`hr`.`hrid` WHERE `hr`.`deleted`=0 GROUP BY `hr_staff`.`staffid`, `hr_staff`.`hrid` UNION SELECT CONCAT('manual',`manuals`.`manualtypeid`) `id`, MIN(`manuals_staff`.`done`) `completed`, `manuals_staff`.`staffid` FROM `manuals_staff` LEFT JOIN `manuals` ON `manuals_staff`.`manualtypeid`=`manuals`.`manualtypeid` WHERE `manuals`.`deleted`=0 GROUP BY `manuals_staff`.`staffid`, `manuals_staff`.`manualtypeid`) `forms` LEFT JOIN `contacts` ON `forms`.`staffid`=`contacts`.`contactid` WHERE `contacts`.`deleted`=0 AND `contacts`.`status` > 0 GROUP BY `contacts`.`contactid`"));
				$block = '<div class="overview-block">
					<h4>Individual Progress</h4>';
				if(count($staff_list) > 0) {
					foreach($staff_list as $staff) {
						$height += 38;
						$block .= "<div class='form-group'><label class='col-sm-4'>".$staff['full_name'].":</label><div class='col-sm-8 text-center' style='background-color: #AAA; padding: 0 0 0 0;line-height:1.85em;'><div style='background-color: #6DCFF6; line-height: 1.5em; width:".($staff['complete'] / $staff['total'] * 100)."%;'>&nbsp;</div><div style='margin: -1.75em 1em 0;'><b>".round($staff['complete'] / $staff['total'] * 100,3)."% Completed</b></div></div></div>";
					}
				} else {
					$height += 24;
					$block .= '<h5>No Individuals Found</h5>';
				}
			$block .= '</div>';
			$blocks[] = [$height,$block];
			$total_height += $height;
		}
		echo '<div class="col-sm-6">';
		$height = 0;
		$split = false;
		foreach($blocks as $i => $block) {
			if(!$split && $i > 0 && ($height > ($total_height / 2) || $i == count($blocks) - 1)) {
				echo '</div><div class="col-sm-6">';
				$split = true;
			}
			echo $block[1];
			$height += $block[0];
		}
		echo '</div>'; ?>
	</div>
</div>