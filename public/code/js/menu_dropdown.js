$(document).ready(function(){
    $('nav.search .dropdown').click(function(){
        var navFilters = $("#nav-filters");
        var filter = navFilters.find(".filter");
        var g = $(this).data("filter");
        if ($(this).hasClass("open")) {
            $(this).removeClass("open");
            navFilters.removeClass("open");
            navFilters.slideToggle("normal");
            setTimeout(function() {
                    filter.hide();
            }
            , 500)
        } else {
            $("nav.search .dropdown").removeClass("open");
            $(this).addClass("open");
            if (navFilters.hasClass("open")) {
                filter.hide();
                navFilters.find(".filter-" + g).fadeIn();
            } else {
                navFilters.addClass("open");
                navFilters.find(".filter-" + g).fadeIn();
                navFilters.slideToggle("fast")
            }
        }
    });


});