$(document).ready(function() {
    if($(window).width() >= 768) {
        $(window).resize(resizeScreen).resize();
    } else {
        $('div.set-section-height').removeClass('set-section-height');
    }
});
function resizeScreen() {
    // $('body>.container').css('margin-bottom','-5em');
    $('body>.container .main-screen').css('padding-bottom','0');
    $('.main-screen .set-section-height').outerHeight($('#footer').offset().top - $('.main-screen .set-section-height').offset().top);
    $('ul.sidebar').outerHeight($('#footer').offset().top - $('.main-screen .set-section-height').offset().top);
    $('div.sidebar').not('.weekly').outerHeight($('#footer').offset().top - $('.main-screen-details').offset().top);
    if($('div.sidebar.weekly').length > 0) {
        $('div.sidebar.weekly').outerHeight($('#footer').offset().top - $('div.sidebar.weekly').offset().top);
    }
    if($('[name="daysheet_notepad"]').length > 0) {
        $('[name="daysheet_notepad"]').outerHeight($('#footer').offset().top - $('div.sidebar.weekly').offset().top - 170);
    }
}
function setStatus(ticketid, stopid, status) {
	$.post('../Profile/profile_ajax.php?action=setStatus', { ticketid: ticketid, stopid: stopid, status: status});
}
function googleMapsLink(span) {
    var url = JSON.parse($(span).data('href'));
    window.open(url, '_blank');
}
function untagMyself(a, id) {
    if(id > 0 && confirm('Are you sure you want to untag yourself?')) {
        $.ajax({
            url: '../Profile/profile_ajax.php?action=contacts_untag'+'&id='+id,
            method: 'GET',
            success: function(response) {
                $(a).closest('li,.block-group-daysheet').remove();
            }
        });
    }
}