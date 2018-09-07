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
			var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
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
checkAuthorised('labour');
?>

<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="labour_iframe" src=""></iframe>
		</div>
	</div>
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<?php include('../Labour/tile_header.php'); ?>
			</div>

			<div class="clearfix"></div>
			<?php if(isset($_GET['edit'])) {
				include('edit_labour.php');
			} else if(isset($_GET['settings']) && config_visible_function($dbc, 'labour') == 1) {
				include('field_config.php');
			} else {
				include('labour_dashboard.php');
			} ?>
		</div>
	</div>
</div>

<?php include_once('../footer.php'); ?>