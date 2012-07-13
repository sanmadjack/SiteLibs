var tooltip_disable = false;
function setUpToolTips() {
    
    $('.has_tooltip').hover(function() {
      $(this).children(".tooltip").stop(true, true).fadeIn();
    }, function() {
      $(this).children(".tooltip").stop(true, true).fadeOut();
    });
//    $('.has_tooltip').mouseover(function() {
  //      jQuery(this).children(".tooltip").stop(true);
    //    jQuery(this).children(".tooltip").fadeIn();
//    });
  //  $('.has_tooltip').mouseout(function() {
    //        jQuery(this).children(".tooltip").fadeOut();
    //});
}
