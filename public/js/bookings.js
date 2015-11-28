

$(document).on("click", "#hot-bookings .std-item.profile-hot-booking.booking-allowed.active .td.cancelation .icon-holder", function() {


    var booking = $(this);
    booking.addClass("disabled-button");
    var selection =  confirm(booking.data("confirmation-text"));

    if (selection) {
        var url = booking.data("url");

        booking.find(".cancelation .icon").removeClass("fa-ban");
        booking.find(".cancelation .icon").addClass("fa-circle-o");

        var b_booking_id = booking.data("booking-id");
        var b_booking_type = booking.data("booking-type");

        var csrfToken = $("input[name=csrf-token]").val();

        var params = {};
        params["booking-id"] = b_booking_id;
        params["csrf-token"] = csrfToken;
        params["booking-type"] = b_booking_type;
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
                    booking.removeClass("disabled-button");
                    booking.find(".cancelation .icon").removeClass("fa-circle-o");
                    booking.find(".cancelation .icon").addClass("fa-ban");
                    return;
                } else if (reply.success != null && reply.success == true) {
                    booking.attr("title", "Upravo otkazano!");
                    booking.find(".cancelation .icon").removeClass("fa-circle-o");
                    booking.find(".cancelation .icon").addClass("fa-times-circle");
                    var item = booking.closest(".item.profile-hot-booking");
                    item.removeClass("active");
                    item.addClass("inactive");

                    item.slideUp(700, function(){
                        $(this).remove();
                    });

                }

            } catch (err) {        }
        });
        request.fail(function(jqXHr, textStatus, errorThrown){
            console.log("ERROR!");
            console.log(jqXHr);
            console.log(textStatus);
            console.log(errorThrown);
            booking.removeClass("disabled-button");
            booking.find(".cancelation .icon").removeClass("fa-circle-o");
            booking.find(".cancelation .icon").addClass("fa-ban");

        });
    } else {
        booking.removeClass("disabled-button");

    }



});




$(document).on("click", ".std-item.profile-home-message.has-not-been-read .status-holder .has-been-read-status-changer.active", function() {


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
    var item = changer.closest(".item.profile-home-message");
    item.removeClass("has-not-been-read");
    item.addClass("has-been-read");

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
