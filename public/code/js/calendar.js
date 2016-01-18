/**
 * Created by gh0c on 29.10.15..
 */


$(document).on("click", ".available-for-booking", function() {
    if(!haltFormSubmitting()) {
        return false;
    } else {
        $("#user-course-booking").addClass("disabled-button");

        var b_date = $(this).data('date');
        var b_start_time = $(this).data('start-time');
        var b_course_id = $(this).data('course-id');
        var b_description = $(this).data('description');
        var b_descriptionMidi = $(this).data('description-midi');

        var b_availability = $(this).data('availability');

        var $clickedSlot = $(".slot-" + b_date + "-" + b_start_time + "-" + b_course_id);

        if($clickedSlot.hasClass("yes-selected")) {
            enableFormSubmiting();
            $("#remove-" + b_date + "-" + b_start_time + "-" + b_course_id).click();
            return true;
        } else {

            $(".day-" + b_date).addClass("selected-day");
            $clickedSlot.removeClass("not-selected");
            $clickedSlot.addClass("yes-selected");

            var $iconCont = $clickedSlot.find(".legend-icon-cont > .not-selected");
            $iconCont.removeClass("not-selected");
            $iconCont.addClass("yes-selected");

            var $divCloned = $( ".selected-span-cont.blueprint" ).last().clone();
            $divCloned.addClass("mix");
            $divCloned.removeClass("blueprint");
            $divCloned.removeClass("display-none");

            $divCloned.find("input.description-label.std").val(b_description);
            $divCloned.find("input.description-label.midi").val(b_descriptionMidi);

            $divCloned.find("input.availability-label").val(b_availability);


            var removeSelectionIcon = document.createElement("i");
            removeSelectionIcon.className = "fa fa-fw fa-times-circle remove-selection";
            removeSelectionIcon.title = "Izbriši odabir";
            removeSelectionIcon.id = "remove-" + b_date + "-" + b_start_time + "-" + b_course_id;

            $divCloned.prepend(removeSelectionIcon);

            $divCloned.find(".remove-selection").attr('data-date', b_date);
            $divCloned.find(".remove-selection").attr('data-start-time', b_start_time);
            $divCloned.find(".remove-selection").attr('data-course-id', b_course_id);

            $divCloned.hide().appendTo('#selected-spans:last').slideDown(700, function() {
                $divCloned.attr("data-myorder", b_date.replace(/-/g, "") + b_start_time.replace("-",""));

                var $selectedSpanInput = $divCloned.find(".selected-spans-input");
                if($selectedSpanInput.val() != "-") {
                    console.log("Special case of booking selection"); // special case
                }
                $selectedSpanInput.val(b_date + " " + b_start_time + " " + b_course_id);


                $("#selected-spans").mixItUp('sort', 'myorder:asc', function() {
                    $("#user-course-booking").removeClass("disabled-button");
                    enableFormSubmiting();
                });

            });
            return true;
        }

    }

});


$(document).on("click", "#selected-spans .selected-span-cont .remove-selection", function() {
    if(!haltFormSubmitting()) {
        return false;
    } else {

        $("#user-course-booking").addClass("disabled-button");

        var b_date = $(this).data('date');
        var b_start_time = $(this).data('start-time');
        var b_course_id = $(this).data('course-id');

        var $clickedSlot = $(".slot-" + b_date + "-" + b_start_time + "-" + b_course_id);

        var $dayConts = $(".day-" + b_date);

        var $selectedSpanInputs = $('#selected-spans').find('.selected-span-cont .selected-spans-input');

        var selectedInputValues;

        if($clickedSlot.length > 0) {
            // removing selected slot from actual month
            $clickedSlot.addClass("not-selected");
            $clickedSlot.removeClass("yes-selected");

            var $slotIcons = $clickedSlot.find(".legend-icon-cont > .yes-selected");

            if($slotIcons.length > 0) {
                $slotIcons.removeClass("yes-selected");
                $slotIcons.addClass("not-selected");


                if(!($dayConts.find(".legend-icon-cont > .yes-selected").length > 0)) {
                    $dayConts.removeClass("selected-day");
                }

                selectedInputValues = $selectedSpanInputs.filter(function() {
                    return this.value == b_date + " " + b_start_time + " " + b_course_id;
                });
                selectedInputValues.parent().slideUp(800, function(){
                    $(this).remove();
                    $("#selected-spans").mixItUp('sort', 'myorder:asc', function() {
                        $("#user-course-booking").removeClass("disabled-button");
                        enableFormSubmiting();
                    });
                });

            } else {
                console.log("@x");
            }
        } else {
            // removing slot selected in some other month view
            if($dayConts.length > 0) {
                selectedInputValues = $selectedSpanInputs.filter(function() {
                    return this.value == b_date + " " + b_start_time + " " + b_course_id;
                });
                selectedInputValues.parent().slideUp(
                    "slow", function() {
                        $(this).remove();
                        $("#user-course-booking").removeClass("disabled-button");
                        if(!($dayConts.find(".legend-icon-cont > .yes-selected").length > 0)) {
                            $dayConts.removeClass("selected-day");
                        }
                        $("#selected-spans").mixItUp('sort', 'myorder:asc', function() {
                            $("#user-course-booking").removeClass("disabled-button");
                            enableFormSubmiting();
                        });
                    });

            } else {
                selectedInputValues = $selectedSpanInputs.filter(function() {
                    return this.value == b_date + " " + b_start_time + " " + b_course_id;
                });
                selectedInputValues.parent().slideUp("slow", function() {
                    $(this).remove();
                    $("#selected-spans").mixItUp('sort', 'myorder:asc', function() {
                        $("#user-course-booking").removeClass("disabled-button");
                        enableFormSubmiting();
                    });
                });
            }
        }
        return true;
    }

});




// ...............................................
// NAVIGATION
// ...............................................







var appendGifs = (function() {
    var $containers = $(".calendar-container");
    var $overlays = $(".calendar-overlay");
    $containers.addClass("disabled-button");

    var divForGifCont = document.createElement("div");
    divForGifCont.className = "loading-gif-div";
    $containers.find("> *").css("opacity", ".5");

    var iForGifCont = document.createElement("i");
    iForGifCont.className = "fa fa-spinner fa-spin";
    divForGifCont.appendChild(iForGifCont);

    $overlays.append(divForGifCont);
});

var removeGifs = (function(){
    var $containers = $(".calendar-container");

    var gifs = $(".loading-gif-div");
    $containers.find("> *").css("opacity", "1");
    gifs.remove();
    $containers.removeClass("disabled-button");
});


var disableForm = (function(form) {
    if (form.length > 0){
        form.addClass("disabled-button");
    }
    appendGifs();
});

var reEnableForm = (function(form) {
    removeGifs();
    if (form.length > 0){
        form.removeClass("disabled-button");
    }
    enableFormSubmiting();
});


var startAjaxReq = (function(url, params) {
    return $.ajax({
        url: url,
        type: "POST",
        data: JSON.stringify(params),
        dataType: "html",
        contentType: "application/json; charset=utf-8"
    });
});


var startLoading = (function(url, params, form, replyContainer) {
    $.when(startAjaxReq(url, params))
        .done(function(reply) {
            displayRequestReply(reply, url, form, replyContainer);
        })
        .fail(function(jqXHr, textStatus, errorThrown) {
            requestFail(jqXHr, textStatus, errorThrown);
            reEnableForm(form);
        });
});



var displayError = (function(reply) {
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
});


var requestFail = (function(jqXHr, textStatus, errorThrown) {
    console.log(" REQUEST ERROR!");
    console.log(jqXHr);
    console.log(textStatus);
    console.log(errorThrown);
    expandInfoPanel("");
    errorStatus("Greška! " + errorThrown);
});

var displayRequestReply = (function(reply, url, form, replyContainer) {
    if(displayError(reply)) {
        reEnableForm(form);
    } else {
        replyContainer.html( reply );
        reEnableForm(form);
    }

});



var collectParamsForDateChange = (function(caller) {
    var params = {};
    params["date"] = caller.data("date");
    params["csrf-token"] = $("input[name=csrf-token]").val();

    return params;
});



$(document).on("click", ".calendar-widget-holder.dynamic .standard-offset-changer.horizontal", function(){
    if(!haltFormSubmitting()) {
        return false;
    } else {
        var $multiCalendarsHolder = $(".multi-calendar-holder");
        var $myForm = $("#user-course-booking");

        disableForm($myForm);

        var b_direction = $(this).data("direction");
        var b_offset = $(this).data("direction-offset");


        var $cells = $(".horizontal .tr .cell");
        var columnWidth = parseFloat($cells.first().css("margin-left").replace(/[^-\d\.]/g, '')) +
            parseFloat($cells.first().css("margin-right").replace(/[^-\d\.]/g, '')) +
            parseFloat($cells[0].getBoundingClientRect().width);

        var $listContainer = $(".pr.horizontal ul");

        if (b_direction === "plus") {
            $listContainer.velocity({
                left: '-=' + columnWidth*b_offset + 'px'
            }, 900, "linear", function() {
            });
        } else if (b_direction === "minus") {
            $listContainer.velocity({
                left: '+=' + columnWidth*b_offset + 'px'
            }, 900, "linear", function() {
            });
        }

        /// -----
        var url = $(this).data("link");

        var $selectedSpanInputs = $("#selected-spans").find("input[name='selected-spans\\[\\]']");

        var myParams = {};
        myParams["selected-spans"] = $selectedSpanInputs
            .map(function(){return $(this).val();}).get();

        // Merge parameters into one dict
        var params = $.extend(collectParamsForDateChange($(this)), myParams);
        startLoading(url, params, $myForm, $multiCalendarsHolder);
        return true;
    }

});



$(document).on("click", ".calendar-widget-holder.dynamic .standard-offset-changer.vertical", function(){
    if(!haltFormSubmitting()) {
        return false;
    } else {
        var $multiCalendarsHolder = $(".multi-calendar-holder");
        var $myForm = $("#user-course-booking");

        disableForm($myForm);

        var b_direction = $(this).data("direction");
        var b_offset = $(this).data("direction-offset");

        var $cells = $(".vertical .tr .cell");
        var columnHeight = parseFloat($cells.first().css("margin-top").replace(/[^-\d\.]/g, '')) +
            parseFloat($cells.first().css("margin-bottom").replace(/[^-\d\.]/g, '')) +
            parseFloat($cells[0].getBoundingClientRect().height);
        var $listContainer = $(".pr.vertical ul");

        if (b_direction === "plus") {
            $listContainer.velocity({
                top: '-=' + columnHeight*b_offset + 'px'
            }, 900, "linear", function() {
            });
        } else if (b_direction === "minus") {
            $listContainer.velocity({
                top: '+=' + columnHeight*b_offset + 'px'
            }, 900, "linear", function() {
            });
        }

//        reEnableForm($myForm);
//        return;

        /// -----
        var url = $(this).data("link");

        var $selectedSpanInputs = $("#selected-spans").find("input[name='selected-spans\\[\\]']");

        var myParams = {};
        myParams["selected-spans"] = $selectedSpanInputs
            .map(function(){return $(this).val();}).get();

        // Merge parameters into one dict
        var params = $.extend(collectParamsForDateChange($(this)), myParams);
        startLoading(url, params, $myForm, $multiCalendarsHolder);
        return true;
    }

});



$(document).on("click", ".calendar-widget-holder.dynamic .standard-month-changer", function(){
    if(!haltFormSubmitting()) {
        return false;
    } else {
        var $multiCalendarsHolder = $(".multi-calendar-holder");
        var $myForm = $("#user-course-booking");

        disableForm($myForm);

        /// -----
        var url = $(this).data("link");

        var $selectedSpanInputs = $("#selected-spans").find("input[name='selected-spans\\[\\]']");

        var myParams = {};
        myParams["selected-spans"] = $selectedSpanInputs
            .map(function(){return $(this).val();}).get();

        // Merge parameters into one dict
        var params = $.extend(collectParamsForDateChange($(this)), myParams);
        startLoading(url, params, $myForm, $multiCalendarsHolder);
        return true;
    }

});







// standard calendar normally...
$(document).on("click", ".calendar-widget-holder:not(.dynamic) .standard-month-changer", function(){
    if(!haltFormSubmitting()) {
        return false;
    } else {
        appendGifs();

        var $calendarHolder = $(".calendar-container");

        var url = $(this).data("link");
        var params = collectParamsForDateChange($(this));

        startLoading(url, params, [], $calendarHolder);
        return true;
    }


});

