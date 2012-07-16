function setUpToolTips() {    
    $('.has_tooltip').off('hover');
    $('.has_tooltip').hover(function() {
      $(this).children(".tooltip").stop(true, true).fadeIn();
    }, function() {
      $(this).children(".tooltip").stop(true, true).fadeOut();
    });
}

$(document).ready(function() {
    setUpToolTips();
});
