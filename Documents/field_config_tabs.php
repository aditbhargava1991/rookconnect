<?php include_once('../include.php');
if(!empty($_GET['tile_name'])) {
	checkAuthorised(false,false,'documents_all_'.$_GET['tile_name']);
} else {
	checkAuthorised('documents_all');
}
include_once('document_settings.php');

if(isset($_POST['submit_tabs'])) {
	$documents_all_dashboard_layout = filter_var($_POST['documents_all_dashboard_layout'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'documents_all_dashboard_layout', $documents_all_dashboard_layout);
	
	$documents_all_tabs = implode(',',$_POST['documents_all_tabs']);
	set_config($dbc, 'documents_all_tabs', $documents_all_tabs);

	$documents_all_tiles = implode(',',$_POST['documents_all_tiles']);
	set_config($dbc, 'documents_all_tiles', $documents_all_tiles);

	$documents_all_custom_tabs = implode(',',$_POST['documents_all_custom_tabs']);
	set_config($dbc, 'documents_all_custom_tabs', $documents_all_custom_tabs);

    echo '<script type="text/javascript"> window.location.replace("?tile_name='.$tile_name.'&settings=tabs"); </script>';
}
?>

<script type="text/javascript">
function updateTabName(input) {
	var custom_div = $(input).closest('.custom_tab_div');
	$(custom_div).find('[name="documents_all_tabs[]"]').val(input.value);
	$(custom_div).find('[name="documents_all_tiles[]"]').val(input.value);
}
function addCustomDiv() {
	var block = $('.custom_tab_div').last();
	var clone = block.clone();

	clone.find('input').val('').prop('checked', false);
	block.after(clone);
}
function removeCustomDiv(img) {
	if($('.custom_tab_div').length <= 1) {
		addCustomDiv();
	}

	$(img).closest('.custom_tab_div').remove();
}
</script>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php
	$documents_all_tabs = ','.get_config($dbc, 'documents_all_tabs').',';
	$documents_all_tiles = ','.get_config($dbc, 'documents_all_tiles').',';
	$documents_all_custom_tabs = explode(',',get_config($dbc, 'documents_all_custom_tabs'));
	?>
    <div class="notice gap-bottom gap-top popover-examples">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11"><span class="notice-name">NOTE:</span>
        Tabs enabled as a Tile will have it's own separate Security settings in the Security tile. Tabs enabled in Documents will use Subtab security settings.</div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group">
    	<label class="col-sm-4 control-label" style="text-align: right;">Dashboard Layout:</label>
    	<div class="col-sm-8">
    		<label class="form-checkbox"><input type="radio" name="documents_all_dashboard_layout" value="" <?= $dashboard_layout != 'tile' ? 'checked' : '' ?>> Sidebar Navigation</label>
    		<label class="form-checkbox"><input type="radio" name="documents_all_dashboard_layout" value="tile" <?= $dashboard_layout == 'tile' ? 'checked' : '' ?>> Tabs as Tiles</label>
    	</div>
    </div>
	<div class="form-group">
		<label class="col-sm-4 control-label" style="text-align: right;">Enabled Tabs:</label>
		<div class="col-sm-8">
			<div class="col-sm-4 hide-titles-mob">Tab Name</div>
			<div class="col-sm-4 hide-titles-mob">Enable in Documents</div>
			<div class="col-sm-4 hide-titles-mob">Enable as Tile</div>
			<div class="clearfix"></div>
			<div class="block-group">
				<div class="col-sm-4">Client Documents</div>
				<div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" name="documents_all_tabs[]" value="Client Documents" <?= strpos($documents_all_tabs, ',Client Documents,') !== FALSE ? 'checked' : '' ?>> Enable <span class="show-on-mob">in Documents</span></label></div>
				<div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" name="documents_all_tiles[]" value="Client Documents" <?= strpos($documents_all_tiles, ',Client Documents,') !== FALSE ? 'checked' : '' ?>> Enable <span class="show-on-mob">as Tile</span></label></div>
				<div class="clearfix"></div>

				<div class="col-sm-4">Staff Documents</div>
				<div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" name="documents_all_tabs[]" value="Staff Documents" <?= strpos($documents_all_tabs, ',Staff Documents,') !== FALSE ? 'checked' : '' ?>> Enable <span class="show-on-mob">in Documents</span></label></div>
				<div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" name="documents_all_tiles[]" value="Staff Documents" <?= strpos($documents_all_tiles, ',Staff Documents,') !== FALSE ? 'checked' : '' ?>> Enable <span class="show-on-mob">as Tile</span></label></div>
				<div class="clearfix"></div>

				<div class="col-sm-4">Internal Documents</div>
				<div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" name="documents_all_tabs[]" value="Internal Documents" <?= strpos($documents_all_tabs, ',Internal Documents,') !== FALSE ? 'checked' : '' ?>> Enable <span class="show-on-mob">in Documents</span></label></div>
				<div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" name="documents_all_tiles[]" value="Internal Documents" <?= strpos($documents_all_tiles, ',Internal Documents,') !== FALSE ? 'checked' : '' ?>> Enable <span class="show-on-mob">as Tile</span></label></div>
				<div class="clearfix"></div>

				<div class="col-sm-4">Marketing Materials</div>
				<div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" name="documents_all_tabs[]" value="Marketing Material" <?= strpos($documents_all_tabs, ',Marketing Material,') !== FALSE ? 'checked' : '' ?>> Enable <span class="show-on-mob">in Documents</span></label></div>
				<div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" name="documents_all_tiles[]" value="Marketing Material" <?= strpos($documents_all_tiles, ',Marketing Material,') !== FALSE ? 'checked' : '' ?>> Enable <span class="show-on-mob">as Tile</span></label></div>
				<div class="clearfix"></div>

				<?php foreach($documents_all_custom_tabs as $custom_tab) { ?>
					<div class="custom_tab_div">
						<div class="col-sm-4"><input type="text" name="documents_all_custom_tabs[]" value="<?= $custom_tab ?>" class="form-control" onchange="updateTabName(this);"></div>
						<div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" name="documents_all_tabs[]" value="<?= $custom_tab ?>" <?= strpos($documents_all_tabs, ','.$custom_tab.',') !== FALSE && !empty($custom_tab) ? 'checked' : '' ?>> Enable <span class="show-on-mob">in Documents</span></label></div>
						<div class="col-sm-3"><label class="form-checkbox"><input type="checkbox" name="documents_all_tiles[]" value="<?= $custom_tab ?>" <?= strpos($documents_all_tiles, ','.$custom_tab.',') !== FALSE && !empty($custom_tab) ? 'checked' : '' ?>> Enable <span class="show-on-mob">as Tile</span></label></div>
						<div class="col-sm-1">
							<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addCustomDiv();">
							<img src="../img/remove.png" class="inline-img pull-right" onclick="removeCustomDiv(this);">
						</div>
						<div class="clearfix"></div>
					</div>
				<?php } ?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="form-group">
	    <div class="col-sm-6">
	        <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your Staff Document settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	        <a href="?tile_name=<?= $tab_name ?>" class="btn brand-btn btn-lg">Back</a>
			<!--<a href="#" class="btn config-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
		</div>
		<div class="col-sm-6">
	        <button	type="submit" name="submit_tabs" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
			<span class="popover-examples list-inline pull-right" style="margin:15px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click this to finalize your Staff Document settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
	    </div>
	</div>
</form>