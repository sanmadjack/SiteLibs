function setUpToolTips() {    
    $('.has_tooltip').off('hover');
    $('.has_tooltip').hover(function() {
        
        var top = $(this).height();
        var width = $(this).children(".tooltip").width();
        var parent_left = $(this).offset().left;
        
        var doc_width = $(document).innerWidth() - 50;
        if(parent_left + width > doc_width) {
          $(this).children(".tooltip").css({ top: top, right: 15 });
            
        } else {
            
          $(this).children(".tooltip").css({ top: top, left: 35 });
            
        }
        
        
        
        
      $(this).children(".tooltip").stop(true, true).fadeIn();
    }, function() {
      $(this).children(".tooltip").stop(true, true).fadeOut();
    });
}

$(document).ready(function() {
    setUpToolTips();
});
