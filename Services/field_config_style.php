<?php
/*
PDF Style
*/
include ('../include.php');
checkAuthorised('services');
error_reporting(0);
?>
<head>
	<link id="jquiCSS" rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" type="text/css" media="all">
	<link href="css/demo.css" rel="stylesheet" /> 
	<link href="css/evol-colorpicker.min.css" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/css?family=Abril+Fatface|Encode+Sans|Indie+Flower|Karla|Lora|Merriweather|Montserrat|Nunito|Raleway|Roboto|Saira+Extra+Condensed|Slabo+27px" rel="stylesheet">

	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js" type="text/javascript"></script> -->
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script src="js/evol-colorpicker.min.js" type="text/javascript"></script>
</head>
<script>

$(document).ready(function(){

	// Change theme
    $('.css').on('click', function(evt){
        var theme=this.innerHTML.toLowerCase().replace(' ', '-'),
            url='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/'+theme+'/jquery-ui.css';
        $('#jquiCSS').attr('href', url);
        $('.css').removeClass('sel');
        $(this).addClass('sel');
    });
	
	// Events demo
	function setColor(evt, color){
        if(color){
            $('#cpEvent').css('background-color', color);
        }
	}

	$('#cpBinding').colorpicker({
		color:'#ebf1dd',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	})
		.on('change.color', setColor)
		.on('mouseover.color', setColor);
	
	
	// Methods demo
	$('#getVal').on('click', function(){
		alert("getval");
		alert('Selected color = "' + $('#cp1').colorpicker("val") + '"');
	});
	$('#setVal').on('click', function(){
		alert("setval");
		$('#cp1').colorpicker("val",'#31859b');
	});
	$('#enable').on('click', function(){
		alert("enable");
		$('#cp1').colorpicker("enable");
	});
	$('#disable').on('click', function(){
		$('#cp1').colorpicker("disable");
	});
	$('#clear').on('click', function(){
		$('#cp1').colorpicker("clear");
	});
	$('#destroy1').on('click', function(){
		$('#cp1').colorpicker("destroy");
	});
	// Methods demo 2 (inline colorpicker)
	$('#getVal2').on('click', function(){
		alert('Selected color = "' + $('#cpInline').colorpicker("val") + '"');
	});
	$('#setVal2').on('click', function(){
		$('#cpInline').colorpicker("val", '#31859b');
	});
	$('#enable2').on('click', function(){
		$('#cpInline').colorpicker("enable");
	});
	$('#disable2').on('click', function(){
		$('#cpInline').colorpicker("disable");
	});
	$('#destroy2').on('click', function(){
		$('#cpInline').colorpicker("destroy");
	});
	
	// Instanciate colorpickers
	$('#cp1').colorpicker({
		color:'#ff9800',
		initialHistory: ['#ff0000','#000000','red', 'purple']
	})
	$('#cpBinding').colorpicker({
		color:'#ebf1dd'
	})
    $('#cpInline').colorpicker({color:'#92cddc'});
    $('#cpInline2').colorpicker({color:'#92cddc'});

	// Custom theme palette
	$('#customTheme').colorpicker({
		color: '#f44336',
		customTheme: ['#f44336','#ff9800','#ffc107','#4caf50','#00bcd4','#3f51b5','#9c27b0', 'white', 'black']
	});
    $('#cpButton').colorpicker({showOn:'button'});
    $('#cpFocus').colorpicker({showOn:'focus'});
    $('#cpBoth').colorpicker();
    $('#cpOther').colorpicker({showOn:'none'});

	$('#show').on('click', function(evt){
		evt.stopImmediatePropagation();
		$('#cpOther').colorpicker("showPalette");
	});
	
	// With transparent color
	$('#transColor').colorpicker({
		transparentColor: true
	});

	// With hidden button
	$('#hideButton').colorpicker({
		hideButton: true
	});

	// No color indicator
	$('#noIndColor').colorpicker({
		displayIndicator: false
	});

	// French colorpicker
	$('#frenchColor').colorpicker({
		strings: "Couleurs de themes,Couleurs de base,Plus de couleurs,Moins de couleurs,Palette,Historique,Pas encore d'historique."
	});

	// Fix links
	$('a[href="#"]').attr('href', 'javascript:void(0)');

	/* if($(window).width() > 767) {
        resizeScreen();
        $(window).resize(function() {
            resizeScreen();
        });
    } */
    
    $(window).resize(function() {
		var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
        if(available_height > 300) {
            $('.tile-sidebar, .tile-sidebar .sidebar, .tile-content').height(available_height);
        }
	}).resize();
});

function resizeScreen() {
    $('#services_div .tile-sidebar, #services_div .tile-sidebar .sidebar, #services_div .tile-content').height($('#services_div').height() - $('#services_div .tile-header').height() + 15);
    $('#services_div .main-screen-white').height($('#services_div .tile-content').height() - 10);
}
</script>

<script>
var contact_type = '';
$(document).ready(function() {
	$('.panel-heading').click(loadPanel);
});
function loadPanel() {
	$('.panel-body').html('Loading...');
	body = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: '../Contacts/'+$(body).data('file'),
		data: { folder: '<?= FOLDER_NAME ?>', type: contact_type },
		method: 'POST',
		response: 'html',
		success: function(response) {
			$(body).html(response);
		}
	});
}
</script>
<?php
if(empty($_GET['style_settings'])) {
	$_GET['style_settings'] = 'design_styleA';
}
?>
<body>
<?php include_once ('../navigation.php');

?>

<div id="services_div" class="container">
    <div class="row hide_on_iframe">
        <div class="main-screen"><?php
            include('tile_header.php'); ?>

            <div class="tile-container" style="height: 100%;">

            	<div class="collapsible tile-sidebar set-section-height">
					<ul class='sidebar'>
						<h4 style="padding-left: 0.5em;">Design Style</h4>
							<a href="?style_settings=design_styleA"><li class="<?= empty($_GET['style_settings']) || $_GET['style_settings'] == 'design_styleA' ? 'active' : '' ?>">Design Style A</li></a>
							<a href="?style_settings=design_styleB"><li class="<?= $_GET['style_settings'] == 'design_styleB' ? 'active' : '' ?>">Design Style B</li></a>
							<a href="?style_settings=design_styleC"><li class="<?= $_GET['style_settings'] == 'design_styleC' ? 'active' : '' ?>">Design Style C</li></a>
						<?php if(!isset($_GET['preview'])): ?>
							<h4 style="padding-left: 0.5em;">Design Settings</h4>
								<a href="?settings=pdf_setting&style_settings=<?php echo $_GET['style_settings']; ?>"><li class="<?= $_GET['settings'] == 'pdf_setting' ? 'active' : '' ?>">PDF Settings</li></a>
								<a href="?settings=pdf_header_setting&style_settings=<?php echo $_GET['style_settings']; ?>"><li class="<?= $_GET['settings'] == 'pdf_header_setting' ? 'active' : '' ?>">Header</li></a>
								<a href="?settings=pdf_main_setting&style_settings=<?php echo $_GET['style_settings']; ?>"><li class="<?= $_GET['settings'] == 'pdf_main_setting' ? 'active' : '' ?>">Main Content</li></a>
								<a href="?settings=pdf_footer_setting&style_settings=<?php echo $_GET['style_settings']; ?>"><li class="<?= $_GET['settings'] == 'pdf_footer_setting' ? 'active' : '' ?>">Footer</li></a>
						<?php endif; ?>
					</ul>
				</div>
			</div>

        	<div class="fill-to-gap tile-content set-section-height" style="position:relative; top: 1em; padding: 0; overflow-y: auto;">
        		<div class="main-screen-details">
        			<div class="sidebar" style="padding: 1em; margin: 0 auto; overflow-y: auto;">
						<?php 
							switch($_GET['settings']) {
								case 'pdf_setting':
									include('field_config_pdf_setting.php');
									break;
								case 'pdf_header_setting':
									include('field_config_pdf_header_setting.php');
									break;
								case 'pdf_main_setting':
									include('field_config_pdf_main_setting.php');
									break;
								case 'pdf_footer_setting':
									include('field_config_pdf_footer_setting.php');
									break;
							}
						?>
						<?php
							if(!isset($_GET['settings'])) {
								if($_GET['style_settings'] == 'design_styleA') 
									include('field_design_styleA.php');
								if($_GET['style_settings'] == 'design_styleB') 
									include('field_design_styleB.php');
								if($_GET['style_settings'] == 'design_styleC') 
									include('field_design_styleC.php');
							}
						?>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>