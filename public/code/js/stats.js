(function() {


    var statsCalls = {
        startLoading : function(url, params) {
            return $.ajax({
                url: url,
                type: "POST",
                data: JSON.stringify(params),
                dataType: "html",
                contentType: "application/json; charset=utf-8"
            })
        },
        startLoadingJSON : function(url, params) {
            return $.ajax({
                url: url,
                type: "POST",
                data: JSON.stringify(params),
                dataType: "json",
                contentType: "application/json; charset=utf-8"
            })
        },

        handleAjaxCall : function(url, params, callback) {

            $.when(statsCalls.startLoading(url, params))
                .done(function(reply) {

                    try {
                        var jsonReply;
                        if(typeof reply != 'object') {
                            jsonReply = jQuery.parseJSON(reply);
                        } else {
                            jsonReply = reply;
                        }
                        if(typeof jsonReply["error"] != "undefined" && jsonReply["error"] != null) {
                            console.log("Error!!");
                        } else {
                            callback(reply);

                        }
                    } catch (err) {
                        callback(reply);
                    }


                })
                .fail(function(a) {
                    console.log("Desila se greska pri asonkronom zahtjevu!");
                    console.log(a.responseText);

                });
        },

        handleAjaxCallWithJSON : function(url, params, callback) {

            $.when(statsCalls.startLoadingJSON(url, params))
                .done(function(reply) {

                    var jsonReply;
                    if(typeof reply != 'object') {
                        jsonReply = jQuery.parseJSON(reply);
                    } else {
                        jsonReply = reply;
                    }

                    if(typeof jsonReply["error"] != "undefined" && jsonReply["error"] != null) {
                        console.log("Error!!");
                    } else {
                        callback(jsonReply);

                    }
                })
                .fail(function(a) {
                    console.log("Desila se greska pri asonkronom zahtjevu!");
                    console.log(a.responseText);
                });
        }
    };

    var generalStatsParams;

    $(document).ready(function(){
        $('nav .sort .dropdown').click(function(){
            var navFilters = $("#nav-sort-filters");
            var filter = navFilters.find(".filter");
            var g = $(this).data("filter");

            if ($(this).hasClass("open")) {
                $(this).removeClass("open");
                navFilters.removeClass("open");
                navFilters.slideToggle("normal");
                setTimeout(function() {
                        filter.hide();
                    }
                    , 500)
            } else {
                $("nav .sort .dropdown").removeClass("open");
                $(this).addClass("open");
                if (navFilters.hasClass("open")) {
                    filter.hide();
                    navFilters.find(".filter-" + g).fadeIn();
                } else {
                    navFilters.addClass("open");
                    navFilters.find(".filter-" + g).fadeIn();
                    navFilters.slideToggle("fast")
                }
            }
        });



        generalStatsParams = {};
        generalStatsParams["csrf-token"] = $("input[name=csrf-token]").val();


//        $(".stats-cont .nav-sort-filters a")[4].click();

    });






    var appendGifs = (function() {
        var $containers = $(".statistics-container");
        var $overlays = $(".statistics-overlay");
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
        var $containers = $(".statistics-container");

        var gifs = $(".loading-gif-div");
        $containers.find("> *").css("opacity", "1");
        gifs.remove();
        $containers.removeClass("disabled-button");
    });


    $(document).on("click", ".stats-cont .nav-sort-filters a", function(){
        var $caller = $(this);
        $('nav .sort .dropdown').click();
        appendGifs();
        statsCalls.handleAjaxCall($caller.data("criteria-url"), generalStatsParams, function(reply) {
            $("#stats").html(reply);
            removeGifs();
            $(".stats-cont .nav-sort-filters a").removeClass("active");
            $caller.addClass("active");

        });


    });

}).call();