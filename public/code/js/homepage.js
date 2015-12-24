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

//    anim = {
//        fadeInDir: function($el, dur, delay, displayStyle, deltaX, deltaY, deltaZ, easing, opacity, zIndex) {
//            if (deltaX == null) {
//                deltaX = 0;
//            }
//            if (deltaY == null) {
//                deltaY = 0;
//            }
//            if (deltaZ == null) {
//                deltaZ = 0;
//            }
//            if (easing == null) {
//                easing = null;
//            }
//            if (opacity == null) {
//                opacity = 0;
//            }
//            $el.css('display', displayStyle);
//            if (zIndex != null) {
//                $el.css('z-index', zIndex);
//            }
//            $el.velocity({
//                translateX: '-=' + deltaX,
//                translateY: '-=' + deltaY,
//                translateZ: '-=' + deltaZ
//            }, 0);
//            return $el.velocity({
//                opacity: 1,
//                translateX: '+=' + deltaX,
//                translateY: '+=' + deltaY,
//                translateZ: '+=' + deltaZ
//            }, {
//                easing: easing,
//                delay: delay,
//                duration: dur
//            });
//        }
//    };

    function init() {
        $imgContainer = $('.main-img-holder');
        $homepageContent = $('.home-container .homepage-cont-holder');
        scenes.preLoad();
        var imgSrc = 'public/graphics/background.jpg';
//        $imgContainer.prepend( $('<img src="public/graphics/background.jpg"/>') );
//        $imgContainer.imagesLoaded()
//            .done( onDone )
//            .always(onAlways);

        $('<img/>').attr('src', imgSrc).load(function() {
            $(this).remove(); // prevent memory leaks as @benweet suggested
//            $imgContainer.css('background-image', 'url(' + imgSrc + ')');
            $imgContainer.addClass("active");
            $imgContainer.velocity({
                opacity: 1
            }, 0);
            anim.fadeInDir($homepageContent, 800, 400, 'block', 0, 80, 0, 'spring');

        });

//        function onDone() {
//
//        }
//        function onAlways() {
//        }
    }

    $(document).ready(function() {
        console.log("Init");
        return init();
    });

}).call(this);