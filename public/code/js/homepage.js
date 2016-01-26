(function() {

    var
        $imgContainer,
        $homepageContent,
        scenes;


    $imgContainer = null;
    $homepageContent = null;


    scenes = {
        preLoad: function() {
            $homepageContent.velocity({
                opacity: 0
            }, 0);
        }
    };


    function init() {
        $imgContainer = $('.main-img-holder');
        $homepageContent = $('.home-container .homepage-cont-holder');
        scenes.preLoad();
        var imgSrc = 'public/graphics/background.jpg';

        $('<img/>').attr('src', imgSrc).load(function() {
            $(this).remove(); // prevent memory leaks
            $imgContainer.addClass("active");
            $imgContainer.velocity({
                opacity: 1
            }, 0);
            anim.fadeInDir($homepageContent, 800, 400, 'block', 0, 80, 0, 'spring');

        });

    }

    $(document).ready(function() {
        return init();
    });

}).call();