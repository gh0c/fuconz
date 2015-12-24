(function() {

    var
        $loading,
        $unreadMessages,
        $hotBookings,
        $hotGames,
        $infos,

        events,
        init,
        scenes;


    $loading = null;
    $unreadMessages = null;
    $hotBookings = null;
    $hotGames = null;
    $infos = null;




    events = {
        attachInfosExpand: function(infos) {
            infos.on('click', function(e) {
                var $el;
                e.preventDefault();
                $el = $(this);
//                $el.siblings(".info-overlay").animate({width:'toggle'},350);
//                $el.siblings(".info-overlay-right").animate({width:'toggle'},350);

                var $overlayHolders = $el.closest(".td").siblings(".td");

                var $overlays = $overlayHolders.find(".info-overlay");
                var $overlaysRight = $overlayHolders.find(".info-overlay-right");

                if ($overlays.is(":visible")) {
                    $el.removeClass("clicked");
                    $overlays.velocity("slideUp", { duration: 350 });
                    $overlaysRight.velocity("slideUp", { duration: 350 });
                } else {
                    $el.addClass("clicked");
                    $overlays.velocity("slideDown", { duration: 350 });
                    $overlaysRight.velocity("slideDown", { duration: 350 });
                }
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
            var main = $('main');
            main.css("pointer-events", "auto");
            return main.velocity({
                opacity: 1
            }, 0);
        },

        endLoading: function() {
            return $loading.slideUp(500);
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


        var startLoading = (function(url) {
            console.log("Req started " + url.toString());
            return $.ajax({
                url: url,
                type: "POST",
                data: JSON.stringify(params),
                dataType: "html",
                contentType: "application/json; charset=utf-8"
            });
        });


        var displayRequestReply = (function(reply, url, container) {
            console.log("Req ended call " + url.toString());
            if(displayError(reply)) {return;}

            container.html(reply);

            container.slideDown(500, function() {
                anim.fadeInDir(container, 800, 400, 'inline-block', 0, 80, 0, 'spring');
            });
//
        });



        var startMessagesLoading = (function() {
            $.when(startLoading(messagesUrl)).done(function(reply) {
                displayRequestReply(reply, messagesUrl, $unreadMessages);
//                $unreadMessages.promise().done( function() {
//
//                });
            });
        });

        var startGamesLoading = (function() {
            $.when(startLoading(gamesUrl, $hotGames)).done(function(reply) {
                displayRequestReply(reply, gamesUrl, $hotGames);
                var infos = $hotGames.find(".icon-holder.info");
                events.attachInfosExpand(infos);
            });
        });

        var startBookingsLoading = (function() {
            $.when(startLoading(reservationsUrl, $hotBookings)).done(function(reply) {
                displayRequestReply(reply, reservationsUrl, $hotBookings);
                var infos = $hotBookings.find(".icon-holder.info");
                $hotBookings.promise().done( function() {
                    events.attachInfosExpand(infos);
                    scenes.endLoading();

                });
            });
        });

        scenes.preLoad();

        startMessagesLoading();
        startGamesLoading();
        startBookingsLoading();
    };


    $(document).ready(function() {
//        console.log("Ready");
        return init();
    });

}).call(this);



function displayError(reply) {
    try {
        var json_o = jQuery.parseJSON(reply);
        if(json_o.error != null) {
            expandInfoPanel("");
            errorStatus("Greška! " + json_o.error);
            return true;
        }
    } catch (err) {
    }
    return false;
}

function requestFail(jqXHr, textStatus, errorThrown) {
    console.log(" REQUEST ERROR!");
    console.log(jqXHr);
    console.log(textStatus);
    console.log(errorThrown);
}