(function() {

    var
        $loading,
        $unreadMessages,
        $subHeading,

        anim,
        data,
        dom,
        events,
        loader,
        init,
        pos,
        scenes,


    $loading = null;
    $unreadMessages = null;

    data = {
        players: {
            teamOne: [],
            teamTwo: []
        },
        stubPlayers: {
            teamOne: [],
            teamTwo: []
        }
    };




    loader = {
        populateMatchData: function() {

        }
    };



    dom = {


        displayNone: function($el) {
            return $el.css('display', 'none');
        }
    };


    scenes = {
        preLoad: function() {
            $unreadMessages.velocity({
                opacity: 0
            }, 0);

            $('main').css("pointer-events", "auto");
            return $('main').velocity({
                opacity: 1
            }, 0);
        },
        loadIn: function(delay) {


        },
        startLoading: function() {

        },
        endLoading: function() {
            return $loading.slideUp(500);
        }


    };

    anim = {
        fadeInDir: function($el, dur, delay, displayStyle, deltaX, deltaY, deltaZ, easing, opacity, zIndex) {
            if (deltaX == null) {
                deltaX = 0;
            }
            if (deltaY == null) {
                deltaY = 0;
            }
            if (deltaZ == null) {
                deltaZ = 0;
            }
            if (easing == null) {
                easing = null;
            }
            if (opacity == null) {
                opacity = 0;
            }
            $el.css('display', displayStyle);
            if (zIndex != null) {
                $el.css('z-index', zIndex);
            }
            $el.velocity({
                translateX: '-=' + deltaX,
                translateY: '-=' + deltaY,
                translateZ: '-=' + deltaZ
            }, 0);
            return $el.velocity({
                opacity: 1,
                translateX: '+=' + deltaX,
                translateY: '+=' + deltaY,
                translateZ: '+=' + deltaZ
            }, {
                easing: easing,
                delay: delay,
                duration: dur
            });
        },
        fadeOutDir: function($el, dur, delay, displayStyle, deltaX, deltaY, deltaZ, easing, opacity, zIndex) {
            var display;
            if (deltaX == null) {
                deltaX = 0;
            }
            if (deltaY == null) {
                deltaY = 0;
            }
            if (deltaZ == null) {
                deltaZ = 0;
            }
            if (easing == null) {
                easing = null;
            }
            if (opacity == null) {
                opacity = 0;
            }
            if (!opacity) {
                display = 'none';
            } else {
                display = displayStyle;
            }
            if (zIndex != null) {
                $el.css('z-index', zIndex);
            }
            return $el.velocity({
                opacity: opacity,
                translateX: '+=' + deltaX,
                translateY: '+=' + deltaY,
                translateZ: '+=' + deltaZ
            }, {
                easing: easing,
                delay: delay,
                duration: dur
            }).velocity({
                opacity: opacity,
                translateX: '-=' + deltaX,
                translateY: '-=' + deltaY,
                translateZ: '-=' + deltaZ
            }, {
                duration: 0,
                display: display
            });
        }
    };


    init = function() {
        $loading = $('.loading-info-cont');
        $unreadMessages = $("#unread-messages");

        anim.fadeInDir($loading, 500, 0, 'block', 0, 20);

        var messagesUrl = $("#messages-loader-url").val();
        var reservationsUrl = $("#reservations-loader-url").val();
        var csrfToken = $("input[name=csrf-token]").val();

        var params = {};
        params["csrf-token"] = csrfToken;

        console.log("Req started");

        var request1 = $.ajax({
            url: messagesUrl,
            type: "POST",
            data: JSON.stringify(params),
            dataType: "html",
            contentType: "application/json; charset=utf-8"
        });



        request1.done(function( reply ) {
            console.log("Req ended call");
            try {
                var json_o = jQuery.parseJSON(reply);
                if(json_o.error != null) {
                    expandInfoPanel("");
                    errorStatus("Greška! " + json_o.error);
//                    alert(json_o.error);

                    return;

                }
            } catch (err) {
            }
            console.log("..rest of app");
            scenes.preLoad();
            $unreadMessages.html(reply);
            if ($unreadMessages.css("display") == "block") {
//                $(".warning-msg .fa.warning").remove();
//                statusText.parent().removeClass("warning-msg");
            }
            else  {
                $unreadMessages.slideDown(500, function() {
                    anim.fadeInDir($unreadMessages, 800, 400, 'inline-block', 0, 80, 0, 'spring');
                    $unreadMessages.promise().done( function() {
                        scenes.endLoading();
                    });

                });
            }
        });
        request1.fail(function(jqXHr, textStatus, errorThrown){
            console.log("ERROR!");
            console.log(jqXHr);
            console.log(textStatus);
            console.log(errorThrown);
        });
        return;
        return scenes.startLoading();

    };


    $(document).ready(function() {
        return init();
    });

}).call(this);