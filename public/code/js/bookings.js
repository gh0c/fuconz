

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




$(document).on("click", ".std-item.booking.booking-allowed.active .td.cancelation .cancelation-icon-holder", function() {

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
                    var item = booking.closest(".item.booking");
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
