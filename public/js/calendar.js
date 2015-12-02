/**
 * Created by gh0c on 29.10.15..
 */

//$(document).ready(function(){

    $(document).on("click", ".available-for-booking", function() {
        if(!haltFormSubmitting()) {
            return false;
        }
        $("#user-course-booking").addClass("disabled-button");

        var b_date = $(this).data('date');
        var b_start_time = $(this).data('start-time');
        var b_course_id = $(this).data('course-id');
        var b_description = $(this).data('description');
        var b_availability = $(this).data('availability');

        if($("#slot-" + b_date + "-" + b_start_time + "-" + b_course_id).hasClass("yes-selected")) {
            console.log("-no add");
            enableFormSubmiting();
            $("#remove-" + b_date + "-" + b_start_time + "-" + b_course_id).click();
            return;
        }

        $("#day-" + b_date).addClass("selected-day");
        $("#slot-" + b_date + "-" + b_start_time + "-" + b_course_id).removeClass("not-selected");
        $("#slot-" + b_date + "-" + b_start_time + "-" + b_course_id).addClass("yes-selected");

        var icon_cont = $("#slot-" + b_date + "-" + b_start_time + "-" + b_course_id).find(".legend-icon-cont.not-selected");
        icon_cont.removeClass("not-selected");
        icon_cont.addClass("yes-selected");

        var divCloned = $( ".selected-span-cont.blueprint" ).last().clone();
        divCloned.addClass("mix");
        divCloned.removeClass("blueprint");

        divCloned.find("input.description-label").val(b_description);
        divCloned.find("input.availability-label").val(b_availability);


        removeSelectionIcon = document.createElement("i");
        removeSelectionIcon.className = "fa fa-fw fa-times-circle remove-selection";
        removeSelectionIcon.title = "Izbriši odabir";
        removeSelectionIcon.id = "remove-" + b_date + "-" + b_start_time + "-" + b_course_id;

        divCloned.append(removeSelectionIcon);

        divCloned.find(".remove-selection").attr('data-date', b_date);
        divCloned.find(".remove-selection").attr('data-start-time', b_start_time);
        divCloned.find(".remove-selection").attr('data-course-id', b_course_id);
        divCloned.find(".remove-selection").attr('data-description', b_description);

        divCloned.hide().appendTo('#selected-spans:last').slideDown(700, function() {
            divCloned.attr("data-myorder", b_date.replace(/-/g, "") + b_start_time.replace("-",""));

            var selectedSpanInput = divCloned.find(".selected-spans-input");
            if(selectedSpanInput.val() != "-") {
                console.log("Evo ove situacije");
//            var oldSelectedSpanInput = $("#terminSelector" + odabraniTermin.value);
//            if (stariSelector)
//            {
//                if(stariSelector != selector)
//                {
//                    stariSelector.className = "terminSelector";
//                }
//
//            }
            }
            selectedSpanInput.val(b_date + " " + b_start_time + " " + b_course_id);

            var selectedSpans = $("#selected-spans input[name='selected-spans\\[\\]']")
                .map(function(){return $(this).val();}).get();

            $("#selected-spans").mixItUp('sort', 'myorder:asc', function() {
                $("#user-course-booking").removeClass("disabled-button");
                enableFormSubmiting();
            });

        });

    });



    $(document).on("click", "#selected-spans .selected-span-cont .remove-selection", function() {
        if(!haltFormSubmitting()) {
            return false;
        }
        $("#user-course-booking").addClass("disabled-button");

        var b_date = $(this).data('date');
        var b_start_time = $(this).data('start-time');
        var b_course_id = $(this).data('course-id');
        var b_description = $(this).data('description');

        if($("#slot-" + b_date + "-" + b_start_time + "-" + b_course_id).length > 0) {
            // removing selected slot from actual month
            var slot = $("#slot-" + b_date + "-" + b_start_time + "-" + b_course_id);
            slot.addClass("not-selected");
            slot.removeClass("yes-selected");
            if(slot.find(".legend-icon-cont.yes-selected").length > 0) {
                var icon_cont = slot.find(".legend-icon-cont.yes-selected");
                icon_cont.removeClass("yes-selected");
                icon_cont.addClass("not-selected");
                if(!($("#day-" + b_date).find(".legend-icon-cont.yes-selected").length > 0)) {
                    $("#day-" + b_date).removeClass("selected-day");
                }
                var selected_spans_input = $('#selected-spans .selected-span-cont .selected-spans-input').filter(function() {
                    return this.value == b_date + " " + b_start_time + " " + b_course_id;
                });
                selected_spans_input.parent().slideUp(800, function(){
                    $(this).remove();
                    var selectedSpans = $("#selected-spans input[name='selected-spans\\[\\]']")
                        .map(function(){return $(this).val();}).get();
                    $("#selected-spans").mixItUp('sort', 'myorder:asc', function() {
                        $("#user-course-booking").removeClass("disabled-button");
                        enableFormSubmiting();
                    });
                });

            }
        } else {
            // removing slot selected in some other month view
            if($("#day-" + b_date).length > 0) {
                var selected_spans_input = $('#selected-spans .selected-span-cont .selected-spans-input').filter(function() {
                    return this.value == b_date + " " + b_start_time + " " + b_course_id;
                });
                selected_spans_input.parent().slideUp(
                    "slow", function() {
                        $(this).remove();
                        $("#user-course-booking").removeClass("disabled-button");
                        if(!($("#day-" + b_date).find(".legend-icon-cont.yes-selected").length > 0)) {
                            $("#day-" + b_date).removeClass("selected-day");
                        }
                        var selectedSpans = $("#selected-spans input[name='selected-spans\\[\\]']")
                            .map(function(){return $(this).val();}).get();
                        $("#selected-spans").mixItUp('sort', 'myorder:asc', function() {
                            $("#user-course-booking").removeClass("disabled-button");
                            enableFormSubmiting();
                        });
                    });

            } else {
                var selected_spans_input = $('#selected-spans .selected-span-cont .selected-spans-input').filter(function() {
                    return this.value == b_date + " " + b_start_time + " " + b_course_id;
                });
                selected_spans_input.parent().slideUp("slow", function() {
                    $(this).remove();
                    $("#selected-spans").mixItUp('sort', 'myorder:asc', function() {
                        $("#user-course-booking").removeClass("disabled-button");
                        enableFormSubmiting();
                    });
                });
            }
        }

    });


// ...............................................
// NAVIGATION
// ...............................................


$(document).on("click", ".calendar-header.widget-header .month-changer", function(){
    if(!haltFormSubmitting()) {
        return false;
    }

    $("#booking-calendar-std").addClass("disabled-button");
    $("#user-course-booking").addClass("disabled-button");

    var divForGifCont = document.createElement("div");
    divForGifCont.className = "loading-gif-div";
    $("#booking-calendar-std > *").css("opacity", ".5");
    $(".calendar-overlay").append(divForGifCont);

    var iForGifCont = document.createElement("i");
    iForGifCont.className = "fa fa-spinner fa-spin";
    divForGifCont.appendChild(iForGifCont);

    var url = $(this).data("link");
    var date = $(this).data("date");

    var csrfKeyName = $(this).data("csrf-key-name");
    var csrfToken = $("input[name=csrf-token]").val();

    var selectedSpans = $("#selected-spans input[name='selected-spans\\[\\]']")
        .map(function(){return $(this).val();}).get();

    var params = {};
    params["date"] = date;
    params[csrfKeyName] = csrfToken;
    params["selected-spans"] = selectedSpans;

    var request = $.ajax({
        url: url,
        type: "POST",
        data: JSON.stringify(params),
        dataType: "html",
        contentType: "application/json; charset=utf-8"

    });

    request.done(function( reply ) {
        console.log("Req ended call");
        try {
            var json_o = jQuery.parseJSON(reply);
            if(json_o.error != null) {
                expandInfoPanel("");
                errorStatus("Greška! " + json_o.error);
                alert(json_o.error);
                $("#booking-calendar-std > *").css("opacity", "initial");
                $(".loading-gif-div").remove();
                $("#user-course-booking").removeClass("disabled-button");
                $("#booking-calendar-std").removeClass("disabled-button");
                enableFormSubmiting();
                return;

            }
        } catch (err) {
        }
        $("#booking-calendar-std > *").css("opacity", "initial");
        $(".loading-gif-div").remove();
        $("#user-course-booking").removeClass("disabled-button");
        $("#booking-calendar-std").removeClass("disabled-button");

        enableFormSubmiting();

        $( "#booking-calendar-std" ).html( reply );
    });
    request.fail(function(jqXHr, textStatus, errorThrown){
        console.log("ERROR!");
        console.log(jqXHr);
        console.log(textStatus);
        console.log(errorThrown);
    });
});



$(document).on("click", ".calendar-header.widget-header .offset-changer", function() {

    if(!haltFormSubmitting()) {
        return false;
    }

    $("#booking-calendar-ultra").addClass("disabled-button");
    $("#user-course-booking").addClass("disabled-button");

    var divForGifCont = document.createElement("div");
    divForGifCont.className = "loading-gif-div";
    $("#booking-calendar-ultra > *").css("opacity", ".5");
    $(".calendar-overlay").append(divForGifCont);

    var iForGifCont = document.createElement("i");
    iForGifCont.className = "fa fa-spinner fa-spin";
    divForGifCont.appendChild(iForGifCont);


    var url = $(this).data("link");
    var b_date = $(this).data("date");
    var b_direction = $(this).data("direction");
    var b_offset = $(this).data("direction-offset");

    var csrfKeyName = $(this).data("csrf-key-name");
    var csrfToken = $("input[name=" + csrfKeyName + "]").val();

    var selectedSpans = $("#selected-spans input[name='selected-spans\\[\\]']")
        .map(function(){return $(this).val();}).get();

    var columnWidth = parseFloat($(".tr .cell").first().css("margin-left").replace(/[^-\d\.]/g, '')) +
        parseFloat($(".tr .cell").first().css("margin-right").replace(/[^-\d\.]/g, '')) +
        parseFloat($(".tr .cell")[0].getBoundingClientRect().width);

    if (b_direction === "plus") {
        $(".pr ul").animate({
            left: '-=' + columnWidth*b_offset + 'px'
        }, 900, "linear", function() {
        });
    } else if (b_direction === "minus") {
        $(".pr ul").animate({
            left: '+=' + columnWidth*b_offset + 'px'
        }, 900, "linear", function() {
        });
    }

    var params = {};
    params["date"] = b_date;
    params[csrfKeyName] = csrfToken;
    params["selected-spans"] = selectedSpans;

    console.log("Req started");

    var request = $.ajax({
        url: url,
        type: "POST",
        data: JSON.stringify(params),
        dataType: "html",
        contentType: "application/json; charset=utf-8"
    });



    request.done(function( reply ) {
        console.log("Req ended call");
        try {
            var json_o = jQuery.parseJSON(reply);
            if(json_o.error != null) {
                expandInfoPanel("");
                errorStatus("Greška! " + json_o.error);
                alert(json_o.error);
                $("#booking-calendar-ultra > *").css("opacity", "initial");
                $(".loading-gif-div").remove();
                $("#user-course-booking").removeClass("disabled-button");
                $("#booking-calendar-ultra").removeClass("disabled-button");
                enableFormSubmiting();
                return;

            }
        } catch (err) {
        }

        $("#booking-calendar-ultra > *").css("opacity", "initial");
        $(".loading-gif-div").remove();
        $("#user-course-booking").removeClass("disabled-button");
        $("#booking-calendar-ultra").removeClass("disabled-button");
        enableFormSubmiting();

        $( "#booking-calendar-ultra" ).html( reply );
    });
    request.fail(function(jqXHr, textStatus, errorThrown){
        console.log("ERROR!");
        console.log(jqXHr);
        console.log(textStatus);
        console.log(errorThrown);
    });
});
//});



$(document).on("click", ".calendar-widget-holder .standard-month-changer", function(){
    if(!haltFormSubmitting()) {
        return false;
    }
    console.log(".");
    var calendarContainer = $(this).closest(".calendar-container");
    var calendarOverlay = $(this).closest(".calendar-overlay");
    calendarContainer.addClass("disabled-button");

    var divForGifCont = document.createElement("div");
    divForGifCont.className = "loading-gif-div";
    calendarContainer.find("> *").css("opacity", ".5");
    calendarOverlay.append(divForGifCont);
    var iForGifCont = document.createElement("i");
    iForGifCont.className = "fa fa-spinner fa-spin";
    divForGifCont.appendChild(iForGifCont);


    var url = $(this).data("link");
    var date = $(this).data("date");
    var csrfToken = $("input[name=csrf-token]").val();

    var params = {};
    params["date"] = date;
    params["csrf-token"] = csrfToken;

    var request = $.ajax({
        url: url,
        type: "POST",
        data: JSON.stringify(params),
        dataType: "html",
        contentType: "application/json; charset=utf-8"
    });

    request.done(function( reply ) {
        console.log("Req ended call");
        try {
            var json_o = jQuery.parseJSON(reply);
            if(json_o.error != null) {
                alert(json_o.error);
                calendarContainer.find("> *").css("opacity", "initial");
                $(".loading-gif-div").remove();
                calendarContainer.removeClass("disabled-button");
                enableFormSubmiting();
                return;
            }
        } catch (err) {
        }

        calendarContainer.html( reply );

        calendarContainer.find("> *").css("opacity", "initial");
        $(".loading-gif-div").remove();
        calendarContainer.removeClass("disabled-button");
        enableFormSubmiting();

    });
    request.fail(function(jqXHr, textStatus, errorThrown){
        console.log(jqXHr);
        console.log(textStatus);
        console.log(errorThrown);
        calendarContainer.find("> *").css("opacity", "initial");
        $(".loading-gif-div").remove();
        calendarContainer.removeClass("disabled-button");
        enableFormSubmiting();
    });
});