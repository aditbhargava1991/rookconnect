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
<?php if(empty($_GET['mobile_cat'])) { ?>
    <div class='scale-to-fill has-main-screen'>
        <div class='main-screen form-horizontal'>
            <h3 class="gap">Summary</h3>
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
                            $block .= "<div class='form-group no-margin double-pad-top'>";
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
                            $block .= "<div class='form-group no-margin double-pad-top'>";
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
                            $block .= "<div class='form-group no-margin double-pad-top'>";
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
                            $block .= "<div class='form-group no-margin double-pad-top'>";
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
                            $block .= "<div class='form-group no-margin double-pad-top'>";
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
                            $block .= "<div class='form-group no-margin double-pad-top'>";
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
                            $block .= "<div class='form-group no-margin double-pad-top'>";
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
                            $block .= "<div class='form-group no-margin double-pad-top'>";
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
                            $block .= "<div class='form-group no-margin double-pad-top'><label class='col-sm-4'>".$staff['full_name'].":</label><div class='col-sm-8 text-center' style='background-color: #AAA; padding: 0 0 0 0;line-height:1.85em;'><div style='background-color: #6DCFF6; line-height: 1.5em; width:".($staff['complete'] / $staff['total'] * 100)."%;'>&nbsp;</div><div style='margin: -1.75em 1em 0;'><b>".round($staff['complete'] / $staff['total'] * 100,3)."% Completed</b></div></div></div>";
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
<?php } ?>
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