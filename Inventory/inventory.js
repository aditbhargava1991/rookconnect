$(document).ready(function() {
	/* if($(window).width() > 767) {
		resizeScreen();
		$(window).resize(function() {
			resizeScreen();
		});
	} */
    $(window).resize(function() {
        var remove = 0;
        if ( $('header:visible')==true ) {
            remove = 0;
        } else {
            remove = 62;
        }
        var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('#inventory_div').offset().top - remove;
        if(available_height > 200) {
            $('#inventory_div .scale-to-fill, #inventory_div .scale-to-fill .main-screen, #inventory_div .tile-sidebar, #inventory_div .sidebar').height(available_height + 21);
        }
    }).resize();
});
function resizeScreen() {
	//var height = $(window).height() - $('#inventory_div .scale-to-fill').offset().top - $('#footer').outerHeight();
    $('#inventory_div .scale-to-fill,#inventory_div .scale-to-fill .main-screen,#inventory_div .tile-sidebar, #inventory_div .sidebar').height($('#inventory_div').height() - $('.tile-header').height() + 15);
	if(height < 250) {
		//$('#inventory_div .scale-to-fill .main-screen,#inventory_div .tile-sidebar,#inventory_div .scale-to-fill.tile-content').height(height);
	}
}