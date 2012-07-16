function setUpPopUps() {
    $('a.popup_link').off('click');
    $('a.popup_close').off('click');
    $('a.popup_link').click(function(e) {
        e.preventDefault(); 
        $(".popup").stop(true, true).fadeOut();

        var div = $(this).attr('href');
        $("#" + div).stop(true, true).fadeIn();
    });
    $('div.popup').append('<a class="popup_close">Close</a>');
    $('a.popup_close').click(function(e) {
        $(this).parent().stop(true, true).fadeOut();
    });
}

$(document).ready(function() {
    setUpPopUps();
});