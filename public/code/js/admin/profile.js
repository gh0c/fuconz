(function() {

    var
        $loading,
        $unreadMessages,

        events,
        loader,
        init,
        scenes,


    $loading = null;
    $unreadMessages = null;



    scenes = {
        preLoad: function() {
            $unreadMessages.velocity({
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

        anim.fadeInDir($loading, 500, 0, 'block', 0, 20);

        var messagesUrl = $("#messages-loader-url").val();
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
                scenes.endLoading();

            });
        });


        scenes.preLoad();

        startMessagesLoading();
    };


    $(document).ready(function() {
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