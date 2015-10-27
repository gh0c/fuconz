$(document).ready(function(){
    $('nav.search .dropdown').click(function(){
//        var mOver= $(this);
//        var openMenu= $(this).children('div.one');
//        $(openMenu).css({
//            'visibility':'visible',
//            'height':'auto'
//        });
        var g = $(this).data("filter");
        if ($(this).hasClass("open")) {
            $(this).removeClass("open");
            $("#nav-filters").removeClass("open");
            $("#nav-filters").slideToggle("normal");
            setTimeout(function() {
                    $("#nav-filters .filter").hide()
                }
                , 500)
        } else {
            $("nav.search .dropdown").removeClass("open");
            $(this).addClass("open");
            if ($("#nav-filters").hasClass("open")) {
                $("#nav-filters .filter").hide();
                $("#nav-filters .filter-" + g).fadeIn()
            } else {
                $("#nav-filters").addClass("open");
                $("#nav-filters .filter-" + g).fadeIn();
                $("#nav-filters").slideToggle("fast")
            }
        }
    });
});