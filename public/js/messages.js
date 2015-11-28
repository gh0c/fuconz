

$(document).on("click", ".std-item.message.has-not-been-read .status-holder .has-been-read-status-changer.active", function() {


    var changer = $(this);
    changer.parent().addClass("disabled-button");
    changer.find("span.selector").removeClass("fa-square-o");
    changer.find("span.selector").addClass("fa-check-square-o");

    var url = changer.data("link");
    var b_msgId = changer.data("msg-id");
    var b_msgReceiverType = changer.data("msg-receiver-type");

    var csrfToken = $("input[name=csrf-token]").val();

    var params = {};
    params["msg-id"] = b_msgId;
    params["csrf-token"] = csrfToken;
    params["msg-receiver"] = b_msgReceiverType;
    params["flag"] = 1;

    console.log("Req started");

    var request = $.ajax({
        url: url,
        type: "POST",
        data: JSON.stringify(params),
        dataType: "json",
        contentType: "application/json; charset=utf-8"
    });

    request.done(function( reply ) {
        console.log("Req ended call");

        try {
            if(reply.error != null) {
                alert(reply.error);
                changer.parent().removeClass("disabled-button");
                changer.find("span.selector").removeClass("fa-check-square-o");
                changer.find("span.selector").addClass("fa-square-o");
                return;
            } else if (reply.newNumberOfUnread == null) {
                alert("Neka greška kod postavljanja pročitanosti poruke!");
                changer.parent().removeClass("disabled-button");
                changer.find("span.selector").removeClass("fa-check-square-o");
                changer.find("span.selector").addClass("fa-square-o");
                return;
            } else {
                changer.removeClass("active");
                changer.attr("title", "Upravo pročitano!");
                changer.find("span.exclamation-alarm").remove();
                var item = changer.closest(".item.message");
                item.removeClass("has-not-been-read");
                item.addClass("has-been-read");
                changer.parent().removeClass("disabled-button");
                updateMessagesLabels(reply.newNumberOfUnread);
                if (Intercom.supported) {
                    var intercom = Intercom.getInstance();
                    intercom.emit('numOfMessagesChange', {numberOfUnread: reply.newNumberOfUnread});
                    intercom.on('numOfMessagesChange', function(data) {
                        updateMessagesLabels(data.numberOfUnread);
                    });

                }

            }

        } catch (err) {        }
    });
    request.fail(function(jqXHr, textStatus, errorThrown){
        console.log("ERROR!");
        console.log(jqXHr);
        console.log(textStatus);
        console.log(errorThrown);
        changer.parent().removeClass("disabled-button");

    });

});




$(document).on("click", ".std-item.profile-home-message.has-not-been-read .status-holder .has-been-read-status-changer.active", function() {


    var changer = $(this);
    changer.parent().addClass("disabled-button");
    changer.find("span.selector").removeClass("fa-square-o");
    changer.find("span.selector").addClass("fa-check-square-o");

    var url = changer.data("url");
    var b_msgId = changer.data("msg-id");
    var b_msgReceiverType = changer.data("msg-receiver-type");

    var csrfToken = $("input[name=csrf-token]").val();

    var params = {};
    params["msg-id"] = b_msgId;
    params["csrf-token"] = csrfToken;
    params["msg-receiver"] = b_msgReceiverType;
    params["flag"] = 1;

    console.log("Req started");
    console.log(url);


    var request = $.ajax({
        url: url,
        type: "POST",
        data: JSON.stringify(params),
        dataType: "json",
        contentType: "application/json; charset=utf-8"
    });

    request.done(function( reply ) {
        console.log("Req ended call");

        try {
            if(reply.error != null) {
                alert(reply.error);
                changer.parent().removeClass("disabled-button");
                changer.find("span.selector").removeClass("fa-check-square-o");
                changer.find("span.selector").addClass("fa-square-o");
                return;
            } else if (reply.newNumberOfUnread == null) {
                alert("Neka greška kod postavljanja pročitanosti poruke!");
                changer.parent().removeClass("disabled-button");
                changer.find("span.selector").removeClass("fa-check-square-o");
                changer.find("span.selector").addClass("fa-square-o");
                return;
            } else {
                var item = changer.closest(".item.profile-home-message");
                item.removeClass("has-not-been-read");
                item.addClass("has-been-read");
                changer.removeClass("active");
                changer.attr("title", "Upravo pročitano!");

                changer.parent().removeClass("disabled-button");
                item.slideUp(700, function(){
                    $(this).remove();
                });

                updateMessagesLabels(reply.newNumberOfUnread);
                if (Intercom.supported) {
                    var intercom = Intercom.getInstance();
                    intercom.emit('numOfMessagesChange', {numberOfUnread: reply.newNumberOfUnread});
                    intercom.on('numOfMessagesChange', function(data) {
                        updateMessagesLabels(data.numberOfUnread);
                    });
                }
            }

        } catch (err) {        }
    });
    request.fail(function(jqXHr, textStatus, errorThrown){
        console.log("ERROR!");
        console.log(jqXHr);
        console.log(textStatus);
        console.log(errorThrown);
        changer.parent().removeClass("disabled-button");

    });

});
