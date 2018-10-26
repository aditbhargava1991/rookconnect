<?php /* Tile Sort Order */ ?>
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
}
</style>
<?php $tile_string = '';
foreach($user_tile_list as $tile) {
	if(is_array($tile)) {
		$tile = $tile[0].'#*#'.$tile[1];
	}
	$tile_string .= '*#*'.$tile;
} ?>
<script>
$(document).ready(function() {
	$.ajax({ //create an ajax request to load_page.php
		type: "POST",
		url: "settings_ajax.php?fill=tile_save",
		data: { tile_list: '<?php echo $tile_string; ?>' },
		dataType: "html",   //expect html to be returned
		success: function(response){
			console.log(response);
		}
	});

	$('.ui-sortable').each(function() {
		maxHeight = $(this).height();
	});
	maxWidth = Math.max.apply( null, $( '.ui-sortable' ).map( function () {
		return $( this ).outerWidth( true );
	}).get() );

	$(".tileSort").width(maxWidth).height(maxHeight);

	$( '.tileSort' ).each(function () {
		this.style.setProperty( 'height', maxHeight, 'important' );
		this.style.setProperty( 'width', maxWidth, 'important' );

		$(this).attr('style', 'height:'+maxHeight+'px !important; width:'+maxWidth+'px !important');
		$(this).attr('style', 'height:'+maxHeight+'px !important;');
	});
});

function choose_tile_color(sel) {
	var tile_id = sel.id;
    var tilecolor = sel.value;
	var tilecolor = tilecolor.replace("#", "");

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "settings_ajax.php?fill=tile_highlight_color&tile_id="+tile_id+'&tilecolor='+tilecolor,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
</script>

<form name="form_tile_order" method="post" action="" class="form-inline" role="form"><?php

    $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_tile_sort_order'"));
    $note = $notes['note'];

    if ( !empty($note) ) { ?>
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11">
                <span class="notice-name">NOTE:</span>
                <?= $note; ?>
            </div>
            <div class="clearfix"></div>
        </div><?php
    } ?>

	<div style="max-width: 100%; width: 50em;">

		<?php
		echo '<ul id="tile_sort" class="tileSort connectedChecklist">
		<li class="ui-state-default ui-state-disabled no-sort" style="cursor:pointer; font-size: 2em;">Tile Sort Order</li>';

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
                $tile_color = get_config($dbc, 'color_'.$tile_value);
				echo '<li class="ui-state-default" id="'.$tile_value.'"><span style="cursor:pointer; font-size: 1em;">'.$tile_info['name'].'<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" style="height:30px; width:30px;" /></span><input type="color" onchange="choose_tile_color(this); return false;" class="color_picker pull-right" id="color_'.$tile_value.'"" name="color_'.$tile_value.'" style="margin-right: 20px;" value = "#'.$tile_color.'" /></li>';
			}
		}

		echo '</ul>';
	?>
	</div>

</form>
