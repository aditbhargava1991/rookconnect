<?php include('../include.php');
$edit_access = vuaed_visible_function($dbc, 'match');
$config_access = config_visible_function($dbc, 'match');
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
	setTimeout(function() { $(window).resize(); }, 100);
});
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('match');
?>

<div class="container">
    <div class='iframe_holder' style='display:none;'>
        <img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
        <span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
        <iframe id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
    </div>
	<div class="row hide_on_iframe">
		<div class="main-screen">
			<div class="tile-header standard-header">
                <div class="pull-right settings-block"><?php
	                if($config_access == 1) {
	                    echo "<div class='pull-right gap-left'><a href='?settings=match'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>";
	                }
                    if($edit_access == 1) {
						echo "<div class='pull-right gap-left'><a href='?edit=' class='new-btn'><button class='btn brand-btn'>New Match</button></a></div>";
                    } ?>
                </div>
                <div class="scale-to-fill">
					<h1 class="gap-left"><a href="?">Match</a></h1>
				</div>
                <div class="clearfix"></div>
			</div>

			<div class="clearfix"></div>
			<?php if(isset($_GET['edit'])) {
				include('edit_match.php');
			} else if(isset($_GET['settings']) && $config_access > 0) {
				include('field_config.php');
			} else {
				include('match_dashboard.php');
			} ?>
		</div>
	</div>
</div>

<?php include_once('../footer.php'); ?>