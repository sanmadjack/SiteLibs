function setUpPopUps() {
    $('a.popup_link').off('click');
    $('a.popup_close').off('click');
    $('a.popup_link').click(function(e) {
        e.preventDefault(); 
    });
    $('a.popup_link').mousedown(function(e) {
        e.preventDefault(); 
        $(".popup").stop(true, true).fadeOut();

        var div = $(this).attr('href');
        $("#" + div).stop(true, true).fadeIn();
    });
    $('div.popup').html(function(index, oldhtml) {
        return '<a class="popup_close">Close</a><div class="popup_content">' + oldhtml + '</div>';
    });
        
    $('a.popup_close').mousedown(function(e) {
        $(this).parent().stop(true, true).fadeOut();
    });
}

$(document).ready(function() {
    setUpPopUps();
});
