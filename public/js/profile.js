(function() {

    var
        $loading,
        $unreadMessages,
        $hotBookings,
        $hotGames,
        $infos,

        anim,
        data,
        dom,
        events,
        loader,
        init,
        pos,
        scenes;


    $loading = null;
    $unreadMessages = null;
    $hotBookings = null;
    $hotGames = null;
    $infos = null;


    loader = {
        populateMatchData: function() {

        }
    };



    dom = {
        displayNone: function($el) {
            return $el.css('display', 'none');
        }
    };


    events = {
        attachInfosExpand: function() {
            $infos.on('click', function(e) {
                var $el;
                e.preventDefault();
                $el = $(this);
//                var holder = $el.closest(".icon-holder");
                $el.siblings(".info-overlay").animate({width:'toggle'},350);
                $el.siblings(".info-overlay-right").animate({width:'toggle'},350);

            });
        }
    };


    scenes = {
        preLoad: function() {
            $unreadMessages.velocity({
                opacity: 0
            }, 0);
            $hotBookings.velocity({
                opacity: 0
            }, 0);
            $hotGames.velocity({
                opacity: 0
            }, 0);
            $('main').css("pointer-events", "auto");
            return $('main').velocity({
                opacity: 1
            }, 0);
        },
        loadIn: function(delay) {

            var delayInc;
            if (delay == null) {
                delay = 0;
            }
            $world.velocity({
                opacity: 1,
                translateY: 0,
                translateZ: 0
            }, {
                duration: 1000,
                delay: delay,
                easing: 'spring'
            });
            anim.fadeInDir($heading, 300, delay + 600, 'block', 0, 30);
            anim.fadeInDir($subHeading, 300, delay + 800, 'block', 0, 30);

            anim.fadeInDir($teamListStubTeamTwo, 300, delay + 800, 'block', 0, 30);
            anim.fadeInDir($teamListTeamOne, 300, delay + 800, 'block', 0, 30);

            anim.fadeInDir($switcher, 400, delay + 900, 'block', 0, 30);
            delay += 1200;
            delayInc = 30;

            anim.dropStubPlayers($playersStubTeamTwo, delay, delayInc);

            setTimeout (function() {
                return anim.dropPlayers($playersTeamOne, delay, delayInc);
            }, 1000);

        },
        startLoading: function() {
            var images, key, ref, val;
            images = [];
            ref = data.players.teamOne && data.players.teamTwo;
            for (key in ref) {
                val = ref[key];
                images.push(val.avatarImgUrl);
            }
            return dom.preloadImages(images);
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
        $hotBookings = $("#hot-bookings");
        $hotGames = $("#hot-games");

        anim.fadeInDir($loading, 500, 0, 'block', 0, 20);

        var messagesUrl = $("#messages-loader-url").val();
        var reservationsUrl = $("#reservations-loader-url").val();
        var gamesUrl = $("#games-loader-url").val();

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
                        console.log("Req2 started");
                        var request2 = $.ajax({
                            url: reservationsUrl,
                            type: "POST",
                            data: JSON.stringify(params),
                            dataType: "html",
                            contentType: "application/json; charset=utf-8"
                        });

                        request2.done(function( reply ) {
                            console.log("Req2 ended call");
                            try {
                                var json_o = jQuery.parseJSON(reply);
                                if(json_o.error != null) {
                                    expandInfoPanel("");
                                    errorStatus("Greška! " + json_o.error);
                                    return;
                                }
                            } catch (err) {
                            }

                            $hotBookings.html(reply);

                            if ($hotBookings.css("display") == "inline-block") {
                            }
                            else  {

                                $hotBookings.slideDown(500, function() {
                                    anim.fadeInDir($hotBookings, 800, 400, 'inline-block', 0, 80, 0, 'spring');
                                    $infos = $hotBookings.find(".icon-holder.info");

                                    $hotBookings.promise().done( function() {
                                        events.attachInfosExpand();

                                        console.log("Req3 started");
                                        var request3 = $.ajax({
                                            url: gamesUrl,
                                            type: "POST",
                                            data: JSON.stringify(params),
                                            dataType: "html",
                                            contentType: "application/json; charset=utf-8"
                                        });

                                        request3.done(function( reply ) {
                                            console.log("Req3 ended call");
                                            try {
                                                var json_o = jQuery.parseJSON(reply);
                                                if(json_o.error != null) {
                                                    expandInfoPanel("");
                                                    errorStatus("Greška! " + json_o.error);
                                                    return;
                                                }
                                            } catch (err) {
                                            }

                                            $hotGames.html(reply);

                                            if ($hotGames.css("display") == "inline-block") {
                                            }
                                            else  {

                                                $hotGames.slideDown(500, function() {
                                                    anim.fadeInDir($hotGames, 800, 400, 'inline-block', 0, 80, 0, 'spring');
                                                    $infos = $hotGames.find(".icon-holder.info");

                                                    $hotGames.promise().done( function() {
                                                        events.attachInfosExpand();
                                                        scenes.endLoading();
                                                    });
                                                });
                                            }
                                        });
                                        request3.fail(function(jqXHr, textStatus, errorThrown){
                                            console.log("ERROR!");
                                            console.log(jqXHr);
                                            console.log(textStatus);
                                            console.log(errorThrown);
                                        });
                                        return;
                                    });
                                });
                            }
                        });
                        request2.fail(function(jqXHr, textStatus, errorThrown){
                            console.log("ERROR!");
                            console.log(jqXHr);
                            console.log(textStatus);
                            console.log(errorThrown);
                        });
                        return;
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