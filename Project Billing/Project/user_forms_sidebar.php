<?php
$form_id = $user_form_id;
include('../Form Builder/generate_form_contents_sidebar.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
	if($(window).width() > 767) {
		resizeScreen();
		$(window).resize(function() {
			resizeScreen();
		});
	}
	resetActive();
	$('#user_form_mainscreen').scroll(function() {
		resetActive();
	});
	$('[data-tab-target]').click(function() {
		$('#user_form_mainscreen').scrollTop($('#'+$(this).data('tab-target')).offset().top + $('#user_form_mainscreen').scrollTop() - $('#user_form_mainscreen').offset().top);
		return false;
	});
});
function resetActive() {
	var screenTop = $('#user_form_mainscreen').offset().top + 10;
	var screenHeight = $('#user_form_mainscreen').innerHeight();
	$('.active.blue').removeClass('active blue');
	$('.tab-section').filter(function() { return $(this).offset().top + this.clientHeight > screenTop && $(this).offset().top < screenTop + screenHeight; }).each(function() {
		$('[data-tab-target='+$(this).attr('id').replace('tab_section_','')+']').find('li').addClass('active blue');
	});
}
function resizeScreen() {
	var view_height = $(window).height() > 800 ? $(window).height() : 800;
	$('#user_form_sidebar,#user_form_mainscreen').height(view_height - $('#user_form_sidebar').offset().top - $('#footer').outerHeight());	
}
</script>