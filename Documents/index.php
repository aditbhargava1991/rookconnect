<?php
/*
Documents Tile
*/
include_once('../include.php');
error_reporting(0);
?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').not('.show-on-mob .main-screen').is(':visible')) {
			var available_height = window.innerHeight - $(footer).outerHeight() - $('.standard-body:visible').offset().top;
			if(available_height > 200) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
			}
		}
	}).resize();
});
</script>
</head>
<body>
<?php include_once ('../navigation.php');
if(!empty($_GET['tile_name'])) {
	checkAuthorised(false,false,'documents_all_'.$_GET['tile_name']);
} else {
	checkAuthorised('documents_all');
}
// $security = get_security($dbc, 'documents_all');
include_once('document_settings.php');
?>

<div class="container">
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header">
                <div class="pull-right settings-block"><?php
	                if($config_access == 1) {
	                    echo "<div class='pull-right gap-left'><a href='?tile_name=".$tile_name."&settings=".(!empty($tile_name) ? $tile_name : 'tabs')."'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>";
	                }
                    if($edit_access == 1 && ($dashboard_layout != 'tile' || !empty($_GET['tab']))) {
						echo "<div class='pull-right gap-left'><a href='?tile_name=".$tile_name."&tab=".$_GET['tab']."&edit=' class='new-btn'><button class='btn brand-btn hide-titles-mob'>New ".$tab_type."</button></a></div>";
                    } ?>
                </div>
                <div class="scale-to-fill">
					<h1 class="gap-left"><a href="?tile_name=<?= $tile_name ?>&tab=<?= $tab ?>">Documents</a></h1>
				</div>
                <div class="clearfix"></div>
			</div>

			<div class="clearfix"></div>
			<?php if(isset($_GET['send_material'])) {
				include('send_material.php');
			} else if(isset($_GET['edit'])) {
				include('edit_document.php');
			} else if(isset($_GET['settings']) && $config_access > 0) {
				include('field_config.php');
			} else {
				include('document_dashboard.php');
			} ?>
		</div>
	</div>
</div>

<?php include_once('../footer.php'); ?>