<?php /* Tile Sort Order */
include_once('../include.php'); ?>
<div style="display:none;">
<?php include_once('../tiles.php'); ?>
</div>
<script type="text/javascript" src="tile_order.js"></script>
<style type='text/css'>
.display-field {
  display: inline-block;
  text-indent: 2px;
  vertical-align: top;
  width: calc(100% - 60px - 3em);
}
.popped-field {
	width: calc(100% + 1em);
}
.popped-field .display-field {
	color: black;
	font-size: 1.2em;
	width: auto;
}
</style>
<script>
$(document).ready(function() {
	maxHeight = 0;
	$('.ui-sortable').each(function() {
		maxHeight = $(this).height();
	});
	maxWidth = Math.max.apply( null, $( '.ui-sortable' ).map( function () {
		return $( this ).outerWidth( true );
	}).get() );
	
	$(".connectedChecklist").width(maxWidth).height(maxHeight);
	
	$( '.connectedChecklist' ).each(function () {
		this.style.setProperty( 'width', maxWidth, 'important' );
		if($(this).is('[data-id=all]')) {
			$(this).attr('style', 'width:'+maxWidth+'px !important');
		} else {
			this.style.setProperty( 'height', maxHeight, 'important' );
			$(this).attr('style', 'height:'+maxHeight+'px !important; width:'+maxWidth+'px !important');
			$(this).attr('style', 'height:'+maxHeight+'px !important;');
		}
	});
});
$(document).on('change', 'select.set_all_tiles_onchange', function() { set_all_tiles(this); });
$(document).on('change', 'select[name="security_levels[]"]', function() { set_default_levels(this); });
$(document).on('change', 'select[name="restrict_levels[]"]', function() { set_restrict_levels(this); });

function add_dashboard(name) {
	$.ajax({
		type: "POST",
		url: "settings_ajax.php?fill=dashboard_add",
		data: { dashboard: name, assigned: '<?= $_GET['tab'] == 'my_dashboard' ? "true" : 'false' ?>' },
		dataType: "html",
		success: function(response) {
			window.location.reload();
		}
	});
}
function rem_dashboard(id) {
	$.ajax({
		type: "POST",
		url: "settings_ajax.php?fill=dashboard_remove",
		data: { dashboard: id },
		dataType: "html",
		success: function(response) {
			window.location.reload();
		}
	});
}
function rename_dashboard(text) {
	$.ajax({
		type: "POST",
		url: "settings_ajax.php?fill=dashboard_rename",
		data: { name: text.value, dashboard: $(text).closest('ul').data('id') },
		dataType: "html",
		success: function(response) {
			console.log(response);
		}
	});
}
function set_default_dashboard() {
	$.ajax({
		type: "POST",
		url: "settings_ajax.php?fill=dashboard_default",
		data: { dashboard: $('[name=default_dashboard]:checked').val(), tile_list: '<?php foreach($user_tile_list as $tile) { echo '*#*'.(is_array($tile) ? $tile[0].'#*#'.$tile[1] : $tile); } ?>' },
		dataType: "html",
		success: function(response) {
			console.log(response);
		}
	});
}
function set_all_tiles(select) {
	var value = [];
	$(select).find('option:selected').each(function() {
		value.push(this.value);
	});
	$.ajax({
		type: "POST",
		url: "settings_ajax.php?fill=show_all_tiles_level",
		data: { levels: value.join(',') },
		dataType: "html",
		success: function(response) {
			console.log(response);
		}
	});
}
function set_default_levels(select) {
	var value = [];
	$(select).find('option:selected').each(function() {
		value.push(this.value);
	});
	$.ajax({
		type: "POST",
		url: "settings_ajax.php?fill=dashboard_default_levels",
		data: { dashboard: $(select).data('id'), levels: value.join(',') },
		dataType: "html",
		success: function(response) {
			console.log(response);
		}
	});
}
function set_restrict_levels(select) {
	var value = [];
	$(select).find('option:selected').each(function() {
		value.push(this.value);
	});
	$.ajax({
		type: "POST",
		url: "settings_ajax.php?fill=dashboard_restrict",
		data: { dashboard: $(select).data('id'), levels: value.join(',') },
		dataType: "html",
		success: function(response) {
			console.log(response);
		}
	});
}
</script>
<?php $default_dashboard = mysqli_fetch_array(mysqli_query($dbc, "SELECT `default_dashboard` FROM `contacts_tile_sort` WHERE `contactid`='".$_SESSION['contactid']."'"))['default_dashboard'];
$db_security = 'AND `assigned_users` IS NULL';
if($_GET['tab'] == 'my_dashboard') {
	$db_security = "AND (`assigned_users` IS NULL OR `assigned_users` ='".$_SESSION['contactid']."')";
}
$dashboards = mysqli_query($dbc, "SELECT * FROM `tile_dashboards` WHERE `deleted`=0 $db_security ORDER BY `name`");
$board_list = [];
while($board = mysqli_fetch_array($dashboards)) {
	$board_list[] = $board['name'];
}
mysqli_data_seek($dashboards, 0); ?>

<button class="btn brand-btn" name="add_board" onclick="add_dashboard('New Dashboard');">Add New Dashboard</button>
<?php if(!in_array('Admin', $board_list)) { ?><button class="btn brand-btn" name="add_board" onclick="add_dashboard('Admin');">Use Admin Dashboard</button><?php } ?>
<?php if(!in_array('HR', $board_list)) { ?><button class="btn brand-btn" name="add_board" onclick="add_dashboard('HR');">Use HR Dashboard</button><?php } ?>
<?php if(!in_array('Sales', $board_list)) { ?><button class="btn brand-btn" name="add_board" onclick="add_dashboard('Sales');">Use Sales Dashboard</button><?php } ?>
<div class="clearfix"></div><br /><?php

if( $_GET['tab']=='dashboard' ) {
    $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_dashboard'"));
} else {
    $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_my_dashboard'"));
}

if ( !empty($notes['note']) ) { ?>
    <div class="notice double-gap-bottom popover-examples">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11">
            <span class="notice-name">NOTE:</span>
            <?= $notes['note']; ?>
        </div>
        <div class="clearfix"></div>
    </div><?php
} ?>

<form name="form_tile_order" method="post" action="" class="form-inline" role="form">
	<div style="max-width: 100%; vertical-align: top; width: 22em; display: inline-block;">
		<?php
		echo '<ul id="tile_sort" class="dashboardTiles connectedChecklist" data-id="all">
		<li class="ui-state-default ui-state-disabled no-sort" style="cursor:pointer; font-size: 2em; overflow: visible;">All Tile List<br /><span class="smaller">';
		if($_GET['tab'] == 'dashboard') {
			echo '<b>Show All for Levels:</b> <select class="chosen-select-deselect set_all_tiles_onchange" multiple data-placeholder="Select Security Levels" name="show_all_tiles_level[]"><option></option>';
			$default_levels = explode(',',',super,'.get_config($dbc, 'show_all_tiles_level'));
			foreach(get_security_levels($dbc) as $label => $level) {
				echo '<option '.(in_array($level,$default_levels) ? 'selected' : '').' value="'.$level.'">'.$label.'</option>';
			}
			echo '</select>';
		} else {
			echo 'Drag these tiles into dashboards';
		}
		echo '</span></li>';

		foreach($user_tile_list as $tile) {
			if(!is_array($tile) && strpos($tile,'#*#') !== false) {
				$tile = explode('#*#',$tile);
			}
			$tile_info = tile_data($dbc,$tile);
			if($tile_info['name'] !== false) {
				$tile_value = $tile;
				if(is_array($tile)) {
					$tile_value = $tile[0].'#*#'.$tile[1];
				}
				echo '<li class="ui-state-default" data-board="all" data-id="'.$tile_value.'"><span style="cursor:pointer; font-size: 1em;">'.$tile_info['name'].'<img class="drag_handle pull-right small inline-img" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" /></span></li>';
			}
		}

		echo '</ul>';
	?>
	</div>
	
	<div style="display: inline-block; width: calc(100% - 22.5em);"><?php while($board = mysqli_fetch_array($dashboards)) {
		echo '<div style="max-width: 100%; width: 22em; display: inline-block; float: left;">
		<ul class="'.($_GET['tab'] == 'dashboard' || $board['assigned_users'] == $_SESSION['contactid'] ? 'dashboardTiles ' : '').' connectedChecklist" data-id="'.$board['dashboard_id'].'">
		<li class="ui-state-default ui-state-disabled no-sort" style="cursor:pointer; font-size: 2em; overflow: visible;">
			<div style="display: inline-block; width:calc(100% - 2em);"><input type="text" name="board_name" value="'.$board['name'].'" class="form-control" onchange="'.($_GET['tab'] != 'my_dashboard' || $board['assigned_users'] == $_SESSION['contactid'] ? 'rename_dashboard(this);' : '').'"></div>
			<img onclick="rem_dashboard('.$board['dashboard_id'].');" src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="cursor: pointer; height: 0.75em; width: 0.75em;" title="Remove Dashboard">';
		if($_GET['tab'] == 'dashboard') {
			echo '<label style="font-size: 0.5em;">Default Dashboard for: <select class="chosen-select-deselect" multiple data-placeholder="Select Security Levels" name="security_levels[]" data-id="'.$board['dashboard_id'].'"><option></option>';
			$default_levels = explode(',',$board['default_levels']);
			foreach(get_security_levels($dbc) as $label => $level) {
				echo '<option '.(in_array($level,$default_levels) ? 'selected' : '').' value="'.$level.'">'.$label.'</option>';
			}
			echo '</select></label>';
			echo '<label style="font-size: 0.5em;">Restrict to Dashboard for: <select class="chosen-select-deselect" multiple data-placeholder="Select Security Levels" name="restrict_levels[]" data-id="'.$board['dashboard_id'].'"><option></option>';
			$levels = explode(',',$board['restrict_levels']);
			foreach(get_security_levels($dbc) as $label => $level) {
				if($level != 'super') {
					echo '<option '.(in_array($level,$levels) ? 'selected' : '').' value="'.$level.'">'.$label.'</option>';
				}
			}
			echo '</select></label>';
		} else {
			echo '<label style="font-size: 0.5em;"><input type="radio" '.($default_dashboard == $board['dashboard_id'] ? 'checked' : '').' onchange="set_default_dashboard();" name="default_dashboard" value="'.$board['dashboard_id'].'" style="height: 0.75em; margin: 0; width: 0.75em;"> Use as my default dashboard</label>';
		}
		echo '</li>';
		foreach(explode('*#*', $board['tile_sort']) as $tile) {
			if(!is_array($tile) && strpos($tile,'#*#') !== false) {
				$tile = explode('#*#',$tile);
			}
			$tile_info = tile_data($dbc,$tile);
			if($tile_info['name'] !== false) {
				$tile_value = $tile;
				if(is_array($tile)) {
					$tile_value = $tile[0].'#*#'.$tile[1];
				}
				echo '<li class="ui-state-default" data-board="'.$board['dashboard_id'].'" data-id="'.$tile_value.'"><span style="cursor:pointer; font-size: 1em;">'.$tile_info['name'].($_GET['tab'] != 'my_dashboard' || $board['assigned_users'] == $_SESSION['contactid'] ? '<img class="drag_handle pull-right small inline-img" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" />' : '').'</span></li>';
			}
		}
		echo '</ul></div>';
	} ?></div>
	<br />&nbsp;
	<div class="clearfix"></div>
</form>