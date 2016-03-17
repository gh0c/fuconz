/**
 * Created by gh0c on 20.01.16..
 */
(function() {
    function randomBetween(minimum, maximum) {
        return Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
    }



    var calls = {
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
            chatLayout.changeReqStatus("act-req");

            $.when(calls.startLoading(url, params))
                .done(function(reply) {
                    chatLayout.changeReqStatus("end-req");

                    try {
                        var jsonReply;
                        if(typeof reply != 'object') {
                            jsonReply = jQuery.parseJSON(reply);
                        } else {
                            jsonReply = reply;
                        }
                        if(typeof jsonReply["error"] != "undefined" && jsonReply["error"] != null) {
                            console.log("Error!!");
                            chatLayout.displayError(jsonReply["error"]);
                        } else {
                            callback(reply);
                            chatLayout.changeReqStatus("no-req");

                        }
                    } catch (err) {
                        callback(reply);
                        chatLayout.changeReqStatus("no-req");
                    }


                })
                .fail(function(a) {
                    chatLayout.changeReqStatus("canc-req");
                    console.log("Desila se greska pri asonkronom zahtjevu!");
                    console.log(a.responseText);

                    chatLayout.displayError(a.responseText);
                });
        },

        handleAjaxCallWithJSON : function(url, params, callback) {
            chatLayout.changeReqStatus("act-req");

            $.when(calls.startLoadingJSON(url, params))
                .done(function(reply) {
                    chatLayout.changeReqStatus("end-req");

                    var jsonReply;
                    if(typeof reply != 'object') {
                        jsonReply = jQuery.parseJSON(reply);
                    } else {
                        jsonReply = reply;
                    }

                    if(typeof jsonReply["error"] != "undefined" && jsonReply["error"] != null) {
                        console.log("Error!!");
                        chatLayout.displayError(jsonReply["error"]);
                    } else {
                        callback(jsonReply);
                        chatLayout.changeReqStatus("no-req");

                    }


                })
                .fail(function(a) {
                    chatLayout.changeReqStatus("canc-req");
                    console.log("Desila se greska pri asonkronom zahtjevu!");
                    console.log(a.responseText);
                    chatLayout.displayError(a.responseText);
                });
        }
    };



    function MsgLight($elem) {
        this.elem = $elem;
    }
    MsgLight.prototype.changeLight = function(lightClass) {
        if(!this.elem.hasClass(lightClass)) {
            this.elem.removeClass("no").removeClass("act").removeClass("pend").removeClass("canc").removeClass("end");
            this.elem.addClass(lightClass);
        }
    };
    MsgLight.prototype.removeLight = function() {
        this.elem.slideUp(500, function(){
            $(this).remove();
        });
    };


    var chatLayout = {
        changeReqStatus : function(reqStatus) {
            var $status = $(".status-info-holder .req-status");
            if(!$status.hasClass(reqStatus)) {
                $status.removeClass("no-req").removeClass("act-req").removeClass("pend-req").removeClass("canc-req").removeClass("end-req");
                $status.addClass(reqStatus);
            }
        },
        changeNoActivityStatus : function(actStatus) {
            var $status = $(".status-info-holder .req-status");
            if(!$status.hasClass(actStatus)) {
                $status.removeClass("a-1").removeClass("a-2").removeClass("a-3").removeClass("a-4")
                    .removeClass("a-5").removeClass("a-6").removeClass("a-7").removeClass("a-8");
                $status.addClass(actStatus);
            }
        },

        displayError : function(msg){
            chatLayout.removeGifs();
            var $cont = $('.status-info-holder .req-status-info'),
                $elem = $('<div>',{
                    class	: 'chat-error-message',
                    html	: '<span title = "' + msg + '"><i class = "fa fa-fw fa-ban"></i> ' + msg + '</span>'
                });

            $elem.click(function(){
                $(this).fadeOut(function(){
                    $(this).remove();
                });
            });

            // after 15 seconds close it automatically
            setTimeout(function(){
                $elem.click();
            },15000);

            var $existingErrorMessage = $cont.find(".chat-error-message");
            if($existingErrorMessage.length > 0) {
                $existingErrorMessage.fadeOut(function(){
                    $existingErrorMessage.remove();
                    $elem.hide().appendTo($cont).slideDown();
                });
            } else {
                $elem.hide().appendTo($cont).slideDown();
            }
        },


        chatRoomsContainer : function() {
            var $section = $("#chat-nav-filters").find(".filter-chat-rooms");
            return $section.find(".chat-rooms-container");
        },
        usersContainer : function() {
            var $section = $("#chat-nav-filters").find(".filter-users");
            return $section.find(".users-container");
        },
        currentChatContainer : function() {
            var $section = $("#chat-nav-filters").find(".filter-current-chat");
            return $section.find(".current-chat-messages-container");
        },

        chatOverlayWrap : function() {
            return $(".chat-overlay-wrap");
        },


        appendGifs : function() {
            var $chatOverlayWrap = chatLayout.chatOverlayWrap(),
                $overlays = $(".chat-content-holder");

            $chatOverlayWrap.addClass("disabled-button");
            $chatOverlayWrap.find("> *").css("opacity", ".4");

            var $divForGifCont = $("<div>", {
                    class : 'loading-gif-div'
                }),
                $iForGifCont = $("<i>", {
                    class : 'fa fa-spinner fa-spin'
                });

            $divForGifCont.append($iForGifCont);
            $overlays.append($divForGifCont);
        },
        removeGifs : function(){
            var $chatOverlayWrap = chatLayout.chatOverlayWrap(),
                $gifs = $(".loading-gif-div");

            $chatOverlayWrap.find("> *").css("opacity", "1");
            $gifs.remove();
            $chatOverlayWrap.removeClass("disabled-button");
        },


        createCardTemplate : function($container, number, backgroundClass, stackClass) {
            var $existingCard = $container.find(".fa-stack." + stackClass);
            if ($existingCard.length < 1) {
                if (number > 0) {
                    var $cardElementsArr = [
                            '<i class="fa fa-stack-2x card ', backgroundClass, '"></i>',
                            '<i class="fa fa-stack-1x number">', number, '</i>'
                        ],
                        $cardNumElem = $('<div>' , {
                            class : 'fa-stack ' + stackClass,
                            html : $cardElementsArr.join('')
                        });
                    $cardNumElem.hide().appendTo($container).slideDown();
                }
            } else {
                if (number > 0) {
                    $existingCard.find(".number").text(number);
                } else {
                    // remove card
                    $existingCard.fadeOut(function(){
                        $existingCard.remove();
                    });
                }
            }
        },

        createCard : function($container, number) {
            chatLayout.createCardTemplate($container, number, "fa-square", "hot-card");
        },


        lightsHolder : function() {
            var $holder = chatLayout.currentChatContainer()
                .find(".conversation-container:visible .submit-message .lights-holder");

            var $lights = $('<i>', {
                class : 'light l-1 no fa fa-spinner fa-spin'
            });
            $lights.hide().appendTo($holder).slideDown();

            return new MsgLight($lights);
        },

        headerAlarmIcon : function(action){
            var $cont = $("ul.menu li .chat-opener"),
                $existingIcon = $("#unread-chat-messages-header-alarm");

            if (action == "remove") {
                $existingIcon.fadeOut(function(){
                    $existingIcon.remove();
                });
            } else if (action == "add") {
                if ($existingIcon.length < 1) {
                    var $alarmIcon = $('<i>', {
                        class   : 'fa fa-file-text faa-horizontal animated alarm',
                        id      : 'unread-chat-messages-header-alarm'
                    });
                    $alarmIcon.hide().appendTo($cont).slideDown();
                }
            }
        },


        newMyOrder : function() {
            var $orderedChatRooms = $(".main-mix-chat[data-myorder]");
            if ($orderedChatRooms.length > 0) {
                var orders = $orderedChatRooms.map(function() {
                    return $(this).attr('data-myorder');
                }).get();//get all data values in an array

                var highest = Math.max.apply(Math, orders);
                return highest + 1;
            } else {
                return 1;
            }
        },


        bigEmoticonsContainer : function($chatConvHolder) {
            return $chatConvHolder.find(".messages-menu-filter-items .filter-big-emoticons .big-emoticons-container");
        },
        memeEmoticonsContainer : function($chatConvHolder) {
            return $chatConvHolder.find(".messages-menu-filter-items .filter-meme-emoticons .meme-emoticons-container");
        },


        openChatsMenuSlick : function() {
            return $('#section-desc-filters')
                .find('.current-chat-room-container .open-chats-slider .slider-holder');
        }
    };


    var chatView = {

        openChatRooms : {},

        openChatsSlickMenuInitialised : false,


        switchOpenFilter : function($clickedMenuBtn) {
            var $chatNavFilters = $("#chat-nav-filters");
            var $filters = $chatNavFilters.find(".filter");

            var $descriptionsNavFilters = $(".section-desc-filters");
            var $filterDesc = $descriptionsNavFilters.find(".filter");

            var filterString = $clickedMenuBtn.data("filter");

            // add filter-name as a class to the main holder
            var $chatContentHolder = $(".chat-content-holder");
            var classes = $chatContentHolder.attr("class").split(" ").filter(function(c) {
                return c.lastIndexOf('pane-', 0) !== 0;
            });
            $chatContentHolder.attr("class", $.trim(classes.join(" ")));
            $chatContentHolder.addClass("pane-" + filterString);


            if ($clickedMenuBtn.hasClass("open")) {
                $clickedMenuBtn.removeClass("open");
                $chatNavFilters.removeClass("open");
                $chatNavFilters.slideToggle("normal");

                $descriptionsNavFilters.removeClass("open");
                $descriptionsNavFilters.slideToggle("normal");
                setTimeout(function() {
                    $filters.hide();
                    $filterDesc.hide();
                    scrollApi.reinitialise();

                } , 500 );
            } else {
                $("nav.chat-menu-bottom .no-dropdown").removeClass("open");
                $clickedMenuBtn.addClass("open");
                if ($chatNavFilters.hasClass("open")) {
                    // hide all filters
                    $filters.hide();
                    $filterDesc.hide();
                    $chatNavFilters.find(".filter-" + filterString).fadeIn();
                    $descriptionsNavFilters.find(".filter-" + filterString).fadeIn();
                    scrollApi.reinitialise();

                } else {
                    $chatNavFilters.addClass("open");
                    $chatNavFilters.find(".filter-" + filterString).fadeIn(400, function() {
                        scrollApi.reinitialise();
                    });
                    $chatNavFilters.slideToggle("fast");

                    $descriptionsNavFilters.addClass("open");
                    $descriptionsNavFilters.find(".filter-" + filterString).fadeIn();
                    $descriptionsNavFilters.slideToggle("fast");
                }
            }
        },



        initSlickMenu : function($holder) {


            var openChatsMenuSlick = chatLayout.openChatsMenuSlick();
            openChatsMenuSlick.append($holder);

            openChatsMenuSlick.slick({
                slidesToShow: 3,
                slidesToScroll: 3,
                infinite: true,
                centerPadding: '50px',
                centerMode: true,
                swipeToSlide: true,
                responsive: [
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            infinite: true,
                            centerPadding: '30px',
                            centerMode: true,
                            swipeToSlide: true
                        }
                    }
                ]

            });

            chatView.openChatsSlickMenuInitialised = true;


        },


        addToSlickMenu : function($holder) {

            var openChatsMenuSlick = chatLayout.openChatsMenuSlick();

            if(!chatView.openChatsSlickMenuInitialised) {
                console.log("initialising");

                chatView.initSlickMenu($holder);
            } else {
                console.log("adding");
                openChatsMenuSlick.slick('slickAdd',$holder);
            }

        },




        initUserList : function() {
            // users container
            var $usersCont = chatLayout.usersContainer();
            if($usersCont.hasClass("empty")) {
                $usersCont.removeClass("empty");
                chatLayout.appendGifs();

                // ajax call to populate all users info
                calls.handleAjaxCall(populateUsersUrl, generalParams, function(reply) {
                    $usersCont.html(reply);
                    chatLayout.removeGifs();
                    scrollApi.reinitialise();
                });
            }
        },

        initChatRoomList : function() {
            // chat rooms container
            var $chatRoomsCont = chatLayout.chatRoomsContainer();
            if($chatRoomsCont.hasClass("empty")) {
                $chatRoomsCont.removeClass("empty");
                chatLayout.appendGifs();

                // ajax call to populate all users info
                calls.handleAjaxCall(populateChatRoomsUrl, generalParams, function(reply) {
                    $chatRoomsCont.html(reply);
                    chatLayout.removeGifs();
                    scrollApi.reinitialise();
                });
            }
        },

        reinitialiseMenus : function() {
            calls.handleAjaxCall(populateChatRoomsAndUsersUrl, generalParams, function(reply) {
                var $usersCont = chatLayout.usersContainer(),
                    $chatRoomsCont = chatLayout.chatRoomsContainer();
                if($usersCont.hasClass("empty")) {
                    $usersCont.removeClass("empty");
                }
                if($chatRoomsCont.hasClass("empty")) {
                    $chatRoomsCont.removeClass("empty");
                }

                var $stub = $('<div />',{
                    html	: reply
                });

                var $users      = $stub.find("#users-holder"),
                    $chatRooms  = $stub.find("#chatrooms-holder");
                $usersCont.html($users.html());
                $chatRoomsCont.html($chatRooms.html());
            });
        },

        displayChatRoom : function($currentChatContainer,
                                   $allConversationHolders,
                                   $thisConversationHolder,
                                   callback) {

            if ($currentChatContainer.hasClass("open")) {
                $allConversationHolders.hide();
            } else {
                $currentChatContainer.addClass("open");
            }
            if(typeof callback === "function") {
                $thisConversationHolder.fadeIn(400, callback());
            } else {
                $thisConversationHolder.fadeIn(400);
            }

        },


        openChatRoom : function(hash, dontChangeView) {
            chatProcessor.refreshReadStatusOfChatRoom(hash);

            var $currentChatContainer = chatLayout.currentChatContainer();

//            var $sectionDescFilter = $("#section-desc-filters");
//            var $descSection = $sectionDescFilter.find(".filter-current-chat");
//            var $chatRoomCont = $descSection.find(".current-chat-room-container");
//
            if($currentChatContainer.hasClass("empty")) {
                $currentChatContainer.removeClass("empty");
                chatLayout.appendGifs();

                chatFunctions.populateChat(hash);

            } else {
                var $existingChatRoomMessages = $currentChatContainer.find(
                    ".chat-conversation-holder[data-conv-hash*='" + hash + "']");
                if ($existingChatRoomMessages.length > 0) {

                    var $allConversationMessages = $currentChatContainer.find(".chat-conversation-holder");
//                    var $allConversationRooms = $chatRoomCont.find(".chat-room-holder");

                    chatView.displayChatRoom(
                        $currentChatContainer,
                        $allConversationMessages.closest(".conversation-container"),
                        $existingChatRoomMessages.closest(".conversation-container"),
                        function () {
                            setTimeout(function() {
                                if(hash in chatView.openChatRooms) {
                                    chatView.openChatRooms[hash]['messagesScroll'].reinitialise();

                                }
                            }, 150);
                            $existingChatRoomMessages.closest(".conversation-container")
                                .attr("data-myorder", chatLayout.newMyOrder());

                        }
                    );

                    if(typeof dontChangeView == 'undefined' || dontChangeView == false) {
                        $('nav.chat-menu-bottom .no-dropdown.current-chat-room').click();
                    }


                    scrollApi.reinitialise();
                    setTimeout(function() {
                        $existingChatRoomMessages.find(".textarea-submitter").focus();
                    }, 0);

                } else {
                    chatLayout.appendGifs();
                    chatFunctions.populateChat(hash);
                }
            }
        },

        openChatRoomTemplate : function(userId) {
            var $currentChatContainer = chatLayout.currentChatContainer();
            if($currentChatContainer.hasClass("empty")) {
                chatLayout.appendGifs();
                $currentChatContainer.removeClass("empty");
                chatFunctions.populateChatTemplate(userId);
            } else {
                var $existingChatRoomMessages = $currentChatContainer.find(
                    ".chat-conversation-holder[data-chat-temp-user-id='" + userId + "']");
                if ($existingChatRoomMessages.length > 0) {
                    var $allConversationMessages = $currentChatContainer.find(".chat-conversation-holder");
//                    var $allConversationRooms = $chatRoomCont.find(".chat-room-holder");

                    chatView.displayChatRoom(
                        $currentChatContainer,
                        $allConversationMessages.closest(".conversation-container"),
                        $existingChatRoomMessages.closest(".conversation-container"),
                        function() {
                            $existingChatRoomMessages.closest(".conversation-container").attr("data-myorder", chatLayout.newMyOrder());
                        }

                    );
                    $('nav.chat-menu-bottom .no-dropdown.current-chat-room').click();

                    scrollApi.reinitialise();
                    setTimeout(function() {
                        $existingChatRoomMessages.find(".textarea-submitter").focus();
                    }, 0);
                } else {
                    chatLayout.appendGifs();
                    chatFunctions.populateChatTemplate(userId);
                }
            }
        },


        closeChatRoom : function($clickedButton) {
            var $conversationCont = $clickedButton.closest(".conversation-container");
            var hash = $conversationCont.find(".chat-conversation-holder").data("conv-hash");

            if(hash in chatView.openChatRooms) {
                delete chatView.openChatRooms[hash];
            }
            if(hash in chatProcessor.data.chatRoomsToProcess) {
                delete chatProcessor.data.chatRoomsToProcess[hash];
            }
            $conversationCont.slideDown(500, function(){
                $(this).remove();
            });

            // if there is another chat room open, display the one with most recent activity
            var $orderedChatRooms = $(".main-mix-chat[data-myorder]");
            if ($orderedChatRooms.length > 0) {
                var orders = $orderedChatRooms.map(function() {
                    return $(this).attr('data-myorder');
                }).get();//get all data values in an array

                var highest = Math.max.apply(Math, orders);

                var $newChatRoomToDisplay = $orderedChatRooms.filter(function(){
                    return $(this).attr('data-myorder') == highest;
                });
                var newHash = $newChatRoomToDisplay
                    .find(".chat-conversation-holder")
                    .data("conv-hash");


                $newChatRoomToDisplay.fadeIn(400, function () {
                    setTimeout(function() {
                        if(newHash in chatView.openChatRooms) {
                            chatView.openChatRooms[newHash]['messagesScroll']
                                .reinitialise();
                        }
                    }, 150);
                });

            }
        },



        openMessagesMenu : function($clickedButton) {
            var $conversationHolder = $clickedButton.closest(".chat-conversation-holder");
            $conversationHolder.toggleClass("show-menu");
            $clickedButton.toggleClass("open");
        },

        switchOpenMessagesMenuItem : function($clickedMenuBtn) {
            var $conversationHolder = $clickedMenuBtn.closest(".chat-conversation-holder"),

                $messagesMenuFiltItems = $conversationHolder.find(".messages-menu-filter-items"),
                $filterItems = $messagesMenuFiltItems.find(".filter-item");

            var filterString = $clickedMenuBtn.data("filter"),
                hash = $conversationHolder.data("conv-hash");


            if ($clickedMenuBtn.hasClass("open")) {
                $clickedMenuBtn.removeClass("open");
                $messagesMenuFiltItems.removeClass("open");
                $messagesMenuFiltItems.slideToggle("normal");

                setTimeout(function() {
                    $filterItems.hide();
                    if(hash in chatView.openChatRooms) {
                        chatView.openChatRooms[hash]["messagesMenuScroll"].reinitialise();
                    }
                } , 500 );
            } else {
                $("nav.chat-messages-menu .no-dropdown").removeClass("open");
                $clickedMenuBtn.addClass("open");
                if ($messagesMenuFiltItems.hasClass("open")) {
                    // hide all filters
                    $filterItems.hide();
                    $messagesMenuFiltItems.find(".filter-" + filterString).fadeIn();
                    if(hash in chatView.openChatRooms) {
                        chatView.openChatRooms[hash]["messagesMenuScroll"].reinitialise();
                    }
                } else {
                    $messagesMenuFiltItems.addClass("open");
                    $messagesMenuFiltItems.find(".filter-" + filterString).fadeIn(400, function() {
                        if(hash in chatView.openChatRooms) {
                            chatView.openChatRooms[hash]["messagesMenuScroll"].reinitialise();
                        }
                    });
                    $messagesMenuFiltItems.slideToggle("fast");
                }
            }
        },

        initBigEmoticonsMenuList : function($clickedBtn) {
            // users container
            var $chatConvHolder = $clickedBtn.closest(".chat-conversation-holder"),
                $emoticonsCont = chatLayout.bigEmoticonsContainer($chatConvHolder);
            var hash = $chatConvHolder.data("conv-hash");
            if($emoticonsCont.hasClass("empty")) {
                $emoticonsCont.removeClass("empty");
                var $emoticonsHolder = $("#big-emoticons-holder");
                $emoticonsCont.html($emoticonsHolder.html());

                if(hash in chatView.openChatRooms) {
                    chatView.openChatRooms[hash]["messagesMenuScroll"].reinitialise();
                }
            }
        },
        initMemeEmoticonsMenuList : function($clickedBtn) {
            // users container
            var $chatConvHolder = $clickedBtn.closest(".chat-conversation-holder"),
                $emoticonsCont = chatLayout.memeEmoticonsContainer($chatConvHolder);
            var hash = $chatConvHolder.data("conv-hash");
            if($emoticonsCont.hasClass("empty")) {
                $emoticonsCont.removeClass("empty");
                var $emoticonsHolder = $("#meme-emoticons-holder");
                $emoticonsCont.html($emoticonsHolder.html());

                if(hash in chatView.openChatRooms) {
                    chatView.openChatRooms[hash]["messagesMenuScroll"].reinitialise();
                }
            }
        }
    };


    var chatFunctions = {
        populateChat : function(hash) {

            var myParams = {};
            myParams["chat-room-hash"] = hash;
            var params = $.extend(generalParams, myParams);

            calls.handleAjaxCall(populateChatRoomUrl, params, function(reply) {
                var $currentChatContainer = chatLayout.currentChatContainer();

                var $stub = $('<div>', {
                    html : reply
                });

                var $chatMessagesHtml = $stub.find(".f-chat-messages").html();


                var $newChatMessages = $('<div />',{
                    id		: 'conv-msg-' + hash,
                    html	: $chatMessagesHtml,
                    class   : 'mix main-mix-chat conversation-container'
                });
                $currentChatContainer.append($newChatMessages);

                var $chatMessages = $newChatMessages.find(".chat-conversation-holder"),
                    $allConversationMessages = $currentChatContainer.find(".chat-conversation-holder");

                var $messagesScrollPane =
                        $newChatMessages.find(".messages-scroll-pane").jScrollPane( {
                            verticalGutter: 0,
                            horizontalGutter: 1,
                            animateScroll: true,
                            verticalDragMinHeight: 80,
                            verticalDragMaxHeight: 350
                        }).data('jsp'),

                    $messagesMenuScrollPane =
                        $newChatMessages.find(".messages-menu-items-scroll-pane").jScrollPane( {
                            verticalGutter: 0,
                            horizontalGutter: 1,
                            animateScroll: true,
                            verticalDragMinHeight: 80,
                            verticalDragMaxHeight: 350
                        }).data('jsp');


                function onResize() {
                    $messagesScrollPane.reinitialise();
                    $messagesMenuScrollPane.reinitialise();
                }
                window.addEventListener("resize", onResize);

                chatView.openChatRooms[hash] = {
                    'messagesScroll' : $messagesScrollPane,
                    'messagesMenuScroll' : $messagesMenuScrollPane
                };





                chatView.displayChatRoom(
                    $currentChatContainer,
                    $allConversationMessages.closest(".conversation-container"),
                    $chatMessages.closest(".conversation-container"),
                    function () {
                        setTimeout(function() {
                            chatView.openChatRooms[hash]['messagesScroll'].reinitialise();
                            chatView.openChatRooms[hash]['messagesScroll'].scrollToBottom();
                            chatView.openChatRooms[hash]['messagesMenuScroll'].reinitialise();

                            var $chatRoomTitleHolder = $newChatMessages
                                .find(".chatroom-title-holder")
                                .clone();
                            chatView.addToSlickMenu($chatRoomTitleHolder);
                        }, 150);
                        $chatMessages.closest(".conversation-container")
                            .attr("data-myorder", chatLayout.newMyOrder());

                    }
                );

                $('nav.chat-menu-bottom .no-dropdown.current-chat-room').click();

                chatLayout.removeGifs();

                scrollApi.reinitialise();

                setTimeout(function() {
                    $chatMessages.find(".textarea-submitter").focus();
                }, 25);
            });

        },

        populateChatTemplate : function(userId) {

            var myParams = {};
            myParams["user-id"] = userId;
            var params = $.extend(generalParams, myParams);

            calls.handleAjaxCall(createChatRoomUrl, params, function(reply) {
                var $currentChatContainer = chatLayout.currentChatContainer();

                var $stub = $('<div>', {
                    html : reply
                });

                var $chatMessagesHtml = $stub.find(".f-chat-messages").html();


                var $newChatMessages = $('<div />',{
                    id		: 'conv-msg-cr-' + userId,
                    html	: $chatMessagesHtml,
                    class   : 'mix main-mix-chat conversation-container'
                });
                $newChatMessages.attr("data-myorder", chatLayout.newMyOrder());

                $currentChatContainer.append($newChatMessages);

                var $chatMessages = $newChatMessages.find(".chat-conversation-holder"),
                    $allConversationMessages = $currentChatContainer.find(".chat-conversation-holder");



                var $messagesScrollPane =
                        $newChatMessages.find(".messages-scroll-pane").jScrollPane( {
                            verticalGutter: 0,
                            horizontalGutter: 1,
                            animateScroll: true,
                            verticalDragMinHeight: 80,
                            verticalDragMaxHeight: 350
                        }).data('jsp'),

                    $messagesMenuScrollPane =
                        $newChatMessages.find(".messages-menu-items-scroll-pane").jScrollPane( {
                            verticalGutter: 0,
                            horizontalGutter: 1,
                            animateScroll: true,
                            verticalDragMinHeight: 80,
                            verticalDragMaxHeight: 350
                        }).data('jsp');


                function onResize() {
                    $messagesScrollPane.reinitialise();
                    $messagesMenuScrollPane.reinitialise();
                }
                window.addEventListener("resize", onResize);

//                chatView.openChatRooms[hash] = {
//                    'messagesScroll' : $messagesScrollPane,
//                    'messagesMenuScroll' : $messagesMenuScrollPane
//                };
//


                chatView.displayChatRoom(
                    $currentChatContainer,
                    $allConversationMessages.closest(".conversation-container"),
                    $chatMessages.closest(".conversation-container"),
                    function () {
                        setTimeout(function() {
                            $messagesScrollPane.reinitialise();
                            $messagesScrollPane.scrollToBottom();
                        }, 150);
                        $chatMessages.closest(".conversation-container").attr("data-myorder", chatLayout.newMyOrder());

                    }
                );

                $('nav.chat-menu-bottom .no-dropdown.current-chat-room').click();

                chatLayout.removeGifs();

                scrollApi.reinitialise();

                setTimeout(function() {
                    $chatMessages.find(".textarea-submitter").focus();
                }, 25);
            });

        },

        appendMessageToChat: function ($chatConversationHolder) {
            chatProcessor.resetNoActivityCounter();

            var chatRoomHash = $chatConversationHolder.data("conv-hash");
            var textMessageBody = $chatConversationHolder.find(".textarea-submitter").val();
            if(textMessageBody != null && textMessageBody !== "") {
                chatProcessor.activeChecking = true;

                $chatConversationHolder.find(".textarea-submitter").val("");


                var myParams = {};
                myParams["chat-room-hash"] = chatRoomHash;
                myParams["message-body"] = textMessageBody;
                var params = $.extend(generalParams, myParams);

                var msgLight = chatLayout.lightsHolder();
                msgLight.changeLight("act");


                calls.handleAjaxCall(appendMessageToChatUrl, params, function(reply) {
                    msgLight.changeLight("pend");

                    var $stub = $('<div />',{
                        html	: reply
                    });
                    var $messagesContainer = $chatConversationHolder.find(".messages-container");


                    $stub.find(".msg-m").each(function() {
                        var idt = $(this).attr("id");
                        if ($messagesContainer.find("#" + idt).length > 0) {
                            $(this).remove();
                        }
                    });

                    var $penultimateExistingMsg = $messagesContainer.find(".msg-m").last();
                    // housekeeping, if last message before this one was of the same author, we don't need another
                    // title with username nor avatar!
                    if($penultimateExistingMsg.data("author-id") == $stub.find(".msg-m").first().data("author-id")) {
                        var $penultimateMessageBody = $penultimateExistingMsg.find(".msg-s .msg-b-holder .message-body");
                        $penultimateMessageBody.removeClass("last-in-a-row");
                        $penultimateMessageBody.addClass("not-last-in-a-row");

                        var $penultimateMessageAvatar = $penultimateExistingMsg.find(".msg-s .av-holder .img-av-holder");
                        $penultimateMessageAvatar.remove();

                        $stub.find(".msg-m").first().find(".msg-sender").remove();
                    }

                    $messagesContainer.append($stub.html());
                    msgLight.changeLight("end");
                    setTimeout(function() {
                        chatView.openChatRooms[chatRoomHash]
                            ['messagesScroll'].reinitialise();
                        chatView.openChatRooms[chatRoomHash]
                            ['messagesScroll'].scrollToBottom();
                        $chatConversationHolder.find(".textarea-submitter").focus();
                    }, 25);

                    // If until now this was chat room with unread messages, now it's definitely not
                    // remove classes in user menu and active chatroom conversations menu
                    chatView.reinitialiseMenus();

                    chatProcessor.activeChecking = false;
                    setTimeout(function() {
                        msgLight.changeLight("no");
                        msgLight.removeLight();
                    }, 400);
                });

            } else {
                chatLayout.displayError("Upiši poruku prije slanja!");
            }
        },

        appendMessageToChatTemplate: function ($chatConversationHolder) {
            chatProcessor.resetNoActivityCounter();

            var userId = $chatConversationHolder.data("chat-temp-user-id");
            var textMessageBody = $chatConversationHolder.find(".textarea-submitter").val();
            if(textMessageBody != null && textMessageBody !== "") {
                chatProcessor.activeChecking = true;
                $chatConversationHolder.find(".textarea-submitter").attr("readonly", "true");
                $chatConversationHolder.find(".textarea-submitter").val("");

                var myParams = {};
                myParams["user-id"] = userId;
                myParams["message-body"] = textMessageBody;
                var params = $.extend(generalParams, myParams);

                var msgLight = chatLayout.lightsHolder();
                msgLight.changeLight("act");


                calls.handleAjaxCall(initiateChatRoomUrl, params, function(reply) {
                    msgLight.changeLight("pend");

                    var $stub = $('<div />',{
                        html	: reply,
                        class   : 'mix main-mix-chat conversation-container'
                    });


                    var $newChatMessages = $('#conv-msg-cr-' + userId);

                    var $oldHolder = $newChatMessages.find(".chat-conversation-holder");
                    var hash = $stub.find(".chat-conversation-holder").data("conv-hash");
                    $newChatMessages.attr("id", 'conv-msg-' + hash);

                    $oldHolder.attr("data-conv-hash", hash);
                    $oldHolder.html($stub.find(".chat-conversation-holder").html());



                    var $messagesScrollPane =
                            $newChatMessages.find(".messages-scroll-pane").jScrollPane( {
                                verticalGutter: 0,
                                horizontalGutter: 1,
                                animateScroll: true,
                                verticalDragMinHeight: 80,
                                verticalDragMaxHeight: 350
                            }).data('jsp'),

                        $messagesMenuScrollPane =
                            $newChatMessages.find(".messages-menu-items-scroll-pane").jScrollPane( {
                                verticalGutter: 0,
                                horizontalGutter: 1,
                                animateScroll: true,
                                verticalDragMinHeight: 80,
                                verticalDragMaxHeight: 350
                            }).data('jsp');


                    function onResize() {
                        $messagesScrollPane.reinitialise();
                        $messagesMenuScrollPane.reinitialise();
                    }
                    window.addEventListener("resize", onResize);

                    chatView.openChatRooms[hash] = {
                        'messagesScroll' : $messagesScrollPane,
                        'messagesMenuScroll' : $messagesMenuScrollPane
                    };

                    setTimeout(function() {
                        chatView.openChatRooms[hash]
                            ['messagesScroll'].reinitialise();
                        chatView.openChatRooms[hash]
                            ['messagesScroll'].scrollToBottom();
                        $chatConversationHolder.find(".textarea-submitter").focus();
                    }, 25);

                    chatView.reinitialiseMenus();

                    chatProcessor.activeChecking = false;
                });

            } else {
                chatLayout.displayError("Upiši poruku prije slanja!");
            }
        }



    };


    function ChatRoomToProcess (chatRoomHash) {
        this.hash = chatRoomHash;
    }



    var chatProcessor = {
        data : {
            noActivity : 0,
            chatRoomsToProcess : {},
            initCall : false,
            activityAfterInitCall : false
        },

        activeChecking : false,

        init : function() {
            chatProcessor.data.initCall = true;
            chatView.reinitialiseMenus();
            chatProcessor.initialCheck();

            (function getChatsTimeoutFunction(){
                chatProcessor.secondaryCheck(getChatsTimeoutFunction);
            })();
        },

        resetNoActivityCounter : function() {
            chatProcessor.data.noActivity = 0;
        },

        initialCheck : function() {
            chatProcessor.activeChecking = true;

            calls.handleAjaxCallWithJSON(checkInitialChatStatusUrl, generalParams, function(jsonReply) {
                if (jsonReply["i-have-unread-msgs"] == true) {

                    // add card icon with number of "hot" chatrooms to chat overlay bottom menu item
                    var $chatOverlayWrap = chatLayout.chatOverlayWrap();
                    var $bottomMenuItemCont = $chatOverlayWrap.find(".chat-menu-bottom .no-dropdown.all-chat-rooms a.chat-opener");
                    chatLayout.createCard($bottomMenuItemCont, jsonReply["number-of-hot-chatrooms"]);

                    jsonReply["hot-chatrooms"].forEach(function(hotChatroom) {
                        chatProcessor.data.chatRoomsToProcess[hotChatroom["chatroom"]["hash"]] = {
                            chatRoom : new ChatRoomToProcess(hotChatroom["chatroom"]["hash"]),
                            lastMessageId : hotChatroom["last-message"]["id"],
                            fetchingNecessary : false,
                            activeRefreshing : false
                        };
                    });
                }
                chatProcessor.activeChecking = false;
            });
        },

        secondaryCheck : function(callback) {

            if (!chatProcessor.activeChecking) {

                chatProcessor.activeChecking = true;

                calls.handleAjaxCallWithJSON(checkChatStatusUrl, generalParams, function(jsonReply) {
                    var changesOccurred = false;

                    if (jsonReply["i-have-unread-msgs"] == true) {

                        var currHash;
                        jsonReply["hot-chatrooms"].forEach(function(hotChatroom) {
                            currHash = hotChatroom["chatroom"]["hash"];
                            if(currHash in chatProcessor.data.chatRoomsToProcess) {
                                if(hotChatroom["last-message"]["id"] != chatProcessor.data.chatRoomsToProcess[currHash].lastMessageId) {
                                    changesOccurred = true;
                                    chatProcessor.data.chatRoomsToProcess[currHash].lastMessageId  = hotChatroom["last-message"]["id"];
                                    chatProcessor.data.chatRoomsToProcess[currHash].fetchingNecessary = true;
                                }
                            } else {
                                changesOccurred = true;
                                chatProcessor.data.chatRoomsToProcess[hotChatroom["chatroom"]["hash"]] = {
                                    chatRoom : new ChatRoomToProcess(hotChatroom["chatroom"]["hash"]),
                                    lastMessageId : hotChatroom["last-message"]["id"],
                                    fetchingNecessary : true,
                                    activeRefreshing : false
                                };
                            }
                        });

                    }

                    if (!changesOccurred) {
                        chatProcessor.data.noActivity++;
                    } else {
                        chatProcessor.data.activityAfterInit = true;
                        chatProcessor.refreshChats(jsonReply);
                    }

                    chatProcessor.activeChecking = false;

                });
            } else {
                // checking (or my message appending) in progress!
                chatProcessor.data.noActivity++;
            }
            setTimeout(callback, chatProcessor.getCallbackDelay());


        },



        refreshReadStatusOfChatRoom : function(hash) {
            if(hash in chatProcessor.data.chatRoomsToProcess &&
                chatProcessor.data.chatRoomsToProcess[hash].activeRefreshing != true) {
                chatProcessor.data.chatRoomsToProcess[hash].activeRefreshing = true;

                var $currentChatContainer = chatLayout.currentChatContainer(),
                    $existingChatRoomMessages = $currentChatContainer.find(
                        ".chat-conversation-holder[data-conv-hash*='" + hash + "']"),

                    $lastMessage = $existingChatRoomMessages.find(".messages-container .msg-m").last(),
                    msgId;

                if($lastMessage.length > 0) {
                    msgId = parseInt($lastMessage.attr("id").split("-").pop());
                } else {
                    msgId = null;
                }


                var myParams = {};
                myParams["chat-room-hash"] = hash;
                myParams["last-msg-id"] = msgId;

                var params = $.extend(generalParams, myParams);

                calls.handleAjaxCallWithJSON(refreshChatStatusUrl, params, function(jsonReply) {
                    delete chatProcessor.data.chatRoomsToProcess[hash];
                    if(Object.keys(chatProcessor.data.chatRoomsToProcess).length < 1) {
                        chatLayout.headerAlarmIcon("remove");
                    }
                    chatView.reinitialiseMenus();

                    var $chatOverlayWrap = chatLayout.chatOverlayWrap();
                    var $bottomMenuItemCont = $chatOverlayWrap.find(".chat-menu-bottom .no-dropdown.all-chat-rooms a.chat-opener");
                    chatLayout.createCard($bottomMenuItemCont, jsonReply["number-of-hot-chatrooms"]);
                });
            }
        },


        refreshChats : function(jsonReply) {
            chatProcessor.resetNoActivityCounter();

            var $chatOverlayWrap = chatLayout.chatOverlayWrap();
            var $bottomMenuItemCont = $chatOverlayWrap.find(".chat-menu-bottom .no-dropdown.all-chat-rooms a.chat-opener");
            chatLayout.createCard($bottomMenuItemCont, jsonReply["number-of-hot-chatrooms"]);


            chatLayout.headerAlarmIcon("add");
            var sound = new Howl({
                urls: ['public/sound/fbChatSound.mp3']
            }).play();

            var $chatRoomMessagesCont = chatLayout.currentChatContainer();

            if($chatRoomMessagesCont.hasClass("open")) {
                // first populate open chats with new activity, then reinit menus
                chatProcessor.fetchChats($chatRoomMessagesCont);
                chatView.reinitialiseMenus();

            } else {
                // first reinitialise menus, then fetch new chats...

                chatView.reinitialiseMenus();
                chatProcessor.fetchChats($chatRoomMessagesCont);
            }

        },

        fetchChats : function($chatRoomMessagesCont) {
            for (var hashKey in chatProcessor.data.chatRoomsToProcess) {
                if (chatProcessor.data.chatRoomsToProcess.hasOwnProperty(hashKey)) {
                    if(chatProcessor.data.chatRoomsToProcess[hashKey].fetchingNecessary == true) {
                        chatProcessor.getChat(hashKey, $chatRoomMessagesCont);
                        chatProcessor.data.chatRoomsToProcess[hashKey].fetchingNecessary = false;
                    }
                }
            }
        },

        getChat : function(hash, $chatRoomMessagesCont){

            var $existingChatRoomMessages = $chatRoomMessagesCont.find(
                ".chat-conversation-holder[data-conv-hash*='" + hash + "']");

            if ($existingChatRoomMessages.length > 0) {
                var myParams = {};
                myParams["chat-room-hash"] = hash;
                var params = $.extend(generalParams, myParams);

                calls.handleAjaxCall(fetchNewMessagesUrl, params, function(reply) {
                    var $stub = $('<div />',{
                        html	: reply
                    });

                    var $messagesContainer = $existingChatRoomMessages.find(".messages-container");

                    $stub.find(".msg-m").each(function() {
                        var idt = $(this).attr("id");
                        if ($messagesContainer.find("#" + idt).length > 0) {
                            $(this).remove();
                        }
                    });

                    var $penultimateExistingMsg = $messagesContainer.find(".msg-m").last();
//
                    // housekeeping, if last message before this one was of the same author, we don't need another
                    // title with username nor avatar!
                    if($penultimateExistingMsg.data("author-id") == $stub.find(".msg-m").first().data("author-id")) {
                        var $penultimateMessageBody = $penultimateExistingMsg.find(".msg-s .msg-b-holder .message-body");
                        $penultimateMessageBody.removeClass("last-in-a-row");
                        $penultimateMessageBody.addClass("not-last-in-a-row");

                        var $penultimateMessageAvatar = $penultimateExistingMsg.find(".msg-s .av-holder .img-av-holder");
                        $penultimateMessageAvatar.remove();

                        $stub.find(".msg-m").first().find(".msg-sender").remove();
                    }

                    $messagesContainer.append($stub.html());


                    setTimeout(function() {
                        chatView.openChatRooms[hash]
                            ['messagesScroll'].reinitialise();
                        chatView.openChatRooms[hash]
                            ['messagesScroll'].scrollToBottom();

                    }, 25);
                });

            }
        },

        getCallbackDelay : function() {
            // Setting a timeout for the next request,
            // depending on the chat activity and some random sugar


            var nextRequest = 3,
                noActivity  = "a-1";

            if(chatProcessor.data.activityAfterInit ==  true) {
                if(chatProcessor.data.noActivity > 5){
                    nextRequest = randomBetween(2,6);
                    noActivity  = "a-2";
                }
                if(chatProcessor.data.noActivity > 10){
                    nextRequest = randomBetween(3,9);
                    noActivity  = "a-3";
                }
                if(chatProcessor.data.noActivity > 20){
                    nextRequest = [3, 6, 9, 12][randomBetween(0,3)];
                    noActivity  = "a-4";
                }
                if(chatProcessor.data.noActivity > 30){
                    nextRequest = randomBetween(5,15);
                    noActivity  = "a-5";
                }
                if(chatProcessor.data.noActivity > 50){
                    nextRequest = [6, 12, 20][randomBetween(0,2)];
                    noActivity  = "a-6";
                }
                if(chatProcessor.data.noActivity > 75){
                    nextRequest = [10, 20, 30, 40][randomBetween(0,3)];
                    noActivity  = "a-7";
                }
                if(chatProcessor.data.noActivity > 100){
                    nextRequest = [15, 30, 45, 60][randomBetween(0,3)];
                    noActivity  = "a-8";
                }
            } else {
                if(chatProcessor.data.noActivity > 2){
                    nextRequest = randomBetween(5,10);
                    noActivity  = "a-2";
                }
                if(chatProcessor.data.noActivity > 5){
                    nextRequest = randomBetween(10,20);
                    noActivity  = "a-3";
                }
                if(chatProcessor.data.noActivity > 10){
                    nextRequest = randomBetween(15,35);
                    noActivity  = "a-4";
                }
                if(chatProcessor.data.noActivity > 20){
                    nextRequest = randomBetween(15,60);
                    noActivity  = "a-5";
                }
                if(chatProcessor.data.noActivity > 35){
                    nextRequest = randomBetween(20,80);
                    noActivity  = "a-6";
                }
                if(chatProcessor.data.noActivity > 50){
                    nextRequest = [25,50,75,100][randomBetween(0,3)];
                    noActivity  = "a-7";
                }
                if(chatProcessor.data.noActivity > 70){
                    nextRequest = [30,60,90,120][randomBetween(0,3)];
                    noActivity  = "a-8";
                }
            }
            chatLayout.changeNoActivityStatus(noActivity);
//            console.log("Next request in " + nextRequest.toString() + "secs");
            return nextRequest*1000;
        }
    };


    var scrollApi,
        generalParams,

        populateUsersUrl,
        populateChatRoomsUrl,
        populateChatRoomsAndUsersUrl,

        populateChatRoomUrl,
        createChatRoomUrl,

        initiateChatRoomUrl,
        appendMessageToChatUrl,

        checkChatStatusUrl,
        checkInitialChatStatusUrl,

        refreshChatStatusUrl,
        fetchNewMessagesUrl;



    $(document).ready(function(){


        generalParams = {};
        generalParams["csrf-token"] = $("input[name=csrf-token]").val();

        populateUsersUrl = $("#populate-users-url").val();
        populateChatRoomsUrl = $("#populate-chat-rooms-url").val();
        populateChatRoomsAndUsersUrl = $("#populate-chat-rooms-and-users-url").val();

        populateChatRoomUrl = $("#populate-chat-room-url").val();
        createChatRoomUrl = $("#create-chat-room-url").val();

        initiateChatRoomUrl = $("#initiate-chat-url").val();
        appendMessageToChatUrl = $("#append-message-to-chat-url").val();

        checkChatStatusUrl = $('#check-chat-status-url').val();
        checkInitialChatStatusUrl = $('#check-initial-chat-status-url').val();

        refreshChatStatusUrl = $('#refresh-chat-status-url').val();
        fetchNewMessagesUrl = $('#fetch-new-messages-url').val();

        scrollApi = $('.chat-content-holder').jScrollPane( {
            verticalGutter: 0,
            horizontalGutter: 1,
            animateScroll: true,
            verticalDragMinHeight: 50,
            verticalDragMaxHeight: 80
        }).data('jsp');

        function onResize() {
            scrollApi.reinitialise();
        }
        window.addEventListener("resize", onResize);

        chatProcessor.init();


        $('nav.chat-menu-bottom .no-dropdown').click(function(){
            chatView.switchOpenFilter($(this));
        });

        // users section - initalize if not yet initialised
        $('nav.chat-menu-bottom .no-dropdown.all-users').click(function(){
            chatView.initUserList();
        });
        // chat rooms section - initalize if not yet initialised
        $('nav.chat-menu-bottom .no-dropdown.all-chat-rooms').click(function(){
            chatView.initChatRoomList();
        });


        $(document).on("click", '.chat-nav-filters .users-container a.user.chat-active.opener' +
            ', .chat-nav-filters .chat-rooms-container a.chat-room.opener', function() {
            var hash = $(this).data("chat-room-hash");
            chatView.openChatRoom(hash);
        });

        $(document).on("click", '.chat-nav-filters .users-container a.user.chat-active.creator', function() {
            var userId = $(this).data("user-id");
            chatView.openChatRoomTemplate(userId);
        });



        $(document).on("click", '.chat-nav-filters .current-chat-messages-container .chatroom-menus-holder .close-chatroom', function() {
            chatView.closeChatRoom($(this));
        });

        $(document).on("click", '.chat-nav-filters .current-chat-messages-container .submit-message .submitter.appender', function() {
            var $chatConvHolder = $(this).closest(".chat-conversation-holder");
            chatProcessor.refreshReadStatusOfChatRoom($chatConvHolder.data("conv-hash"));
            chatFunctions.appendMessageToChat($chatConvHolder);
        });

        $(document).on("keydown", '.chat-nav-filters .current-chat-messages-container .submit-message .textarea-submitter.appending', function(e) {
            var $chatConvHolder = $(this).closest(".chat-conversation-holder");
            chatProcessor.refreshReadStatusOfChatRoom($chatConvHolder.data("conv-hash"));

            if(e.which == 13 && e.shiftKey) {

            } else if (e.which == 13) {
                e.preventDefault(); //Stops enter from creating a new line
                chatFunctions.appendMessageToChat($chatConvHolder);
            }
        });

        $(document).on("click", '.chat-nav-filters .current-chat-messages-container .submit-message .textarea-submitter.appending, ' +
            '.chat-nav-filters .current-chat-messages-container .messages-container', function() {
            var $chatConvHolder = $(this).closest(".chat-conversation-holder");
            var hash = $chatConvHolder.data("conv-hash");
            if (typeof hash != "undefined" && hash != null) {
                chatProcessor.refreshReadStatusOfChatRoom(hash);
            }
        });


        $(document).on("click", '.chat-nav-filters .current-chat-messages-container .submit-message .submitter.creator', function() {
            var $chatConvHolder = $(this).closest(".chat-conversation-holder");
            chatFunctions.appendMessageToChatTemplate($chatConvHolder);
        });

        $(document).on("keydown", '.chat-nav-filters .current-chat-messages-container .submit-message .textarea-submitter.creating', function(e) {
            var $chatConvHolder = $(this).closest(".chat-conversation-holder");
            if(e.which == 13 && e.shiftKey) {

            } else if (e.which == 13) {
                e.preventDefault(); //Stops enter from creating a new line
                chatFunctions.appendMessageToChatTemplate($chatConvHolder);
            }
        });


        $(document).on("click", '.chat-nav-filters .current-chat-messages-container .chatroom-menus-holder .open-messages-menu', function() {
            chatView.openMessagesMenu($(this));
        });

        $(document).on("click", 'nav.chat-messages-menu .no-dropdown', function() {
            chatView.switchOpenMessagesMenuItem($(this));
        });


        $(document).on("click", 'nav.chat-messages-menu .no-dropdown.big-emoticons', function() {
            chatView.initBigEmoticonsMenuList($(this));
        });
        $(document).on("click", 'nav.chat-messages-menu .no-dropdown.meme-emoticons', function() {
            chatView.initMemeEmoticonsMenuList($(this));
        });

        $(document).on("click", '.chat-conversation-holder .messages-menu-items-scroll-pane .emoticons-container .emoticon-link', function() {
            var $chatConvHolder = $(this).closest(".chat-conversation-holder");
            $chatConvHolder.find(".textarea-submitter").val($(this).data("emot-code"));
            $(".chat-nav-filters .current-chat-messages-container .chatroom-menus-holder .open-messages-menu").click();
            setTimeout(function() {
                $chatConvHolder.find(".submit-message .submitter.appender").click();
            }, 1000);
        });


        $(document).on("click", '.s-content .chatroom-title-holder.slick-slide', function() {
            console.log("Click now");


            var openChatsMenuSlick = chatLayout.openChatsMenuSlick();

            console.log("Curr idx: " + openChatsMenuSlick.slick("slickCurrentSlide"));
            var clickedSlideSlickIdx = $(this).data("slick-index");
            console.log("Slide clicked idx: " + $(this).data("slick-index"));
            openChatsMenuSlick.slick("slickGoTo", clickedSlideSlickIdx);

            var hash = $(this).data("conv-hash");
            chatView.openChatRoom(hash, true);

            console.log("New curr idx: " + openChatsMenuSlick.slick("slickCurrentSlide"));

        });

    });

}).call();