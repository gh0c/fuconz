(function() {


    var chatFunctions = {
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
        chatOverlayWrap : function() {
            return $(".chat-overlay-wrap");
        },
        displayRequestReply : function(reply, url, container) {
    //        if(displayError(reply)) {return;}
            container.html(reply);

        },
        appendGifs : function() {
            var $chatOverlayWrap = chatFunctions.chatOverlayWrap(),
                $overlays = $(".chat-content-holder");

            $chatOverlayWrap.addClass("disabled-button");

            var divForGifCont = document.createElement("div");
            divForGifCont.className = "loading-gif-div";
            $chatOverlayWrap.find("> *").css("opacity", ".4");

            var iForGifCont = document.createElement("i");
            iForGifCont.className = "fa fa-spinner fa-spin";
            divForGifCont.appendChild(iForGifCont);

            $overlays.append(divForGifCont);
        },
        removeGifs : function(){
            var $chatOverlayWrap = chatFunctions.chatOverlayWrap(),
                $gifs = $(".loading-gif-div");

            $chatOverlayWrap.find("> *").css("opacity", "1");
            $gifs.remove();
            $chatOverlayWrap.removeClass("disabled-button");
        },
        displayChatRoom : function  ($chatRoomMessagesCont, allConversationHolders, thisConversationHolder,
            allRoomHolders, thisRoomHolder) {

            if ($chatRoomMessagesCont.hasClass("open")) {
                allConversationHolders.hide();
                allRoomHolders.hide();
                thisConversationHolder.fadeIn();
                thisRoomHolder.fadeIn();

            } else {
                $chatRoomMessagesCont.addClass("open");
                thisConversationHolder.fadeIn();
                thisRoomHolder.fadeIn();
            }
        },
        handleAjaxCallWithJSON : function(url, params, callback) {
            chatFunctions.changeReqStatus("act-req");

            $.when(chatFunctions.startLoadingJSON(url, params))
                .done(function(reply) {
                    chatFunctions.changeReqStatus("end-req");

                    var jsonReply;
                    if(typeof reply != 'object') {
                        jsonReply = jQuery.parseJSON(reply);
                    } else {
                        jsonReply = reply;
                    }

                    if(typeof jsonReply["error"] != "undefined" && jsonReply["error"] != null) {
                        console.log("Error!!");
                        chatProcessor.displayError(jsonReply["error"]);
                    } else {
                        callback(jsonReply);
                    }

                    chatFunctions.changeReqStatus("no-req");

                })
                .fail(function(a) {
                    chatFunctions.changeReqStatus("pend-req");
                    console.log("Desila se greska!");
                    chatProcessor.displayError(a.responseText);
                });
        },
        handleAjaxCall : function(url, params, callback) {
            chatFunctions.changeReqStatus("act-req");

            $.when(chatFunctions.startLoading(url, params))
                .done(function(reply) {
                    chatFunctions.changeReqStatus("end-req");

                    try {
                        var jsonReply;
                        if(typeof reply != 'object') {
                            jsonReply = jQuery.parseJSON(reply);
                        } else {
                            jsonReply = reply;
                        }
                        if(typeof jsonReply["error"] != "undefined" && jsonReply["error"] != null) {
                            console.log("Error!!");
                            chatProcessor.displayError(jsonReply["error"]);
                        } else {
                            callback(reply);
                        }
                    } catch (err) {
                        callback(reply);
                    }

                    chatFunctions.changeReqStatus("no-req");

                })
                .fail(function(a) {
                    chatFunctions.changeReqStatus("pend-req");
                    console.log("Desila se greska pri asonkronom zahtjevu!");
                    chatProcessor.displayError(a.responseText);
                });
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

//        myOrder : function() {
//            var $orderedChatRooms = $(".main-mix-chat");
//            if ($orderedChatRooms.length > 0) {
//                return parseInt($orderedChatRooms.last().attr("data-myorder")) + 1;
//            } else {
//                return 1;
//            }
//        },
        changeReqStatus : function(reqStatus) {
            var status = $(".status-info-holder .req-status");
            status.removeClass("no-req").removeClass("act-req").removeClass("pend-req").removeClass("canc-req").removeClass("end-req");
            status.addClass(reqStatus);
        },

        lightsHolder : function() {
            return chatFunctions.currentChatContainer().find(".conversation-container:visible .submit-message .lights-holder");
        },
        chatRoomsContainer : function() {
            var $section = $("#chat-nav-filters").find(".filter-chat-rooms");
            // chat rooms container
            return $section.find(".chat-rooms-container");
        },
        usersContainer : function() {
            var $section = $("#chat-nav-filters").find(".filter-users");
            // users container
            return $section.find(".users-container");
        },
        currentChatContainer : function() {
            var $section = $("#chat-nav-filters").find(".filter-current-chat");
            return $section.find(".current-chat-messages-container");
        },

//        reinitialiseChatRoomsMenu : function() {
//            var $chatRoomsCont = chatFunctions.chatRoomsContainer();
//
//            if($chatRoomsCont.hasClass("empty")) {
//                $chatRoomsCont.removeClass("empty");
//            }
//            $.when(chatFunctions.startLoading(populateChatRoomsUrl, generalParams)).done(function(reply) {
//                $chatRoomsCont.html(reply);
//            });
//        },
//        reinitialiseUsersMenu : function() {
//            var $usersCont = chatFunctions.usersContainer();
//
//            if($usersCont.hasClass("empty")) {
//                $usersCont.removeClass("empty");
//            }
//            $.when(chatFunctions.startLoading(populateUsersUrl, generalParams)).done(function(reply) {
//                $usersCont.html(reply);
//            });
//        },

        reinitialiseMenus : function() {
            //
            chatFunctions.handleAjaxCall(populateChatRoomsAndUsersUrl, generalParams, function(reply) {
                var $usersCont = chatFunctions.usersContainer(),
                    $chatRoomsCont = chatFunctions.chatRoomsContainer();
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


        creatingCardTemplate : function($container, number, backgroundClass, stackClass) {
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
            chatFunctions.creatingCardTemplate($container, number, "fa-square", "hot-card");
        }

//        createMessageCard : function($container, number) {
//            chatFunctions.creatingCardTemplate($container, number, "fa-circle", "hot-msg-card");
//        }
    };

    function randomBetween(minimum, maximum) {
//        var rn = Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
//        console.log("Rand between " + minimum.toString() + " and " + maximum.toString() + " turned out to be " + rn.toString());
//        return rn;
        return Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
    }

    function ChatRoom (chatRoomHash) {
        this.hash = chatRoomHash;
    }



    var chatProcessor = {
        data : {
            lastID 		        : 0,
            noActivity	        : 0,
            chatRooms           : {},
            initiallyCalled     : false,
            activityAfterInit   : false

        },

        activeChecking : false,


        init : function() {
            chatProcessor.data.initiallyCalled = true;
            chatFunctions.reinitialiseMenus();
            chatProcessor.initialCheck();

            (function getChatsTimeoutFunction(){
                chatProcessor.secondaryCheck(getChatsTimeoutFunction);
            })();
        },


        refreshReadStatusOfChatroom : function(hash) {
            if(hash in chatProcessor.data.chatRooms) {
                var myParams = {};
                myParams["chat-room-hash"] = hash;
                var params = $.extend(generalParams, myParams);

                chatFunctions.handleAjaxCallWithJSON(refreshChatStatusUrl, params, function(jsonReply) {
                    delete chatProcessor.data.chatRooms[hash];
                    if(Object.keys(chatProcessor.data.chatRooms).length < 1) {
                        chatFunctions.headerAlarmIcon("remove");
                    }
                    chatFunctions.reinitialiseMenus();

                    var $chatOverlayWrap = chatFunctions.chatOverlayWrap();
                    var $bottomMenuItemCont = $chatOverlayWrap.find(".chat-menu-bottom .no-dropdown.all-chat-rooms a.chat-opener");
                    chatFunctions.createCard($bottomMenuItemCont, jsonReply["number-of-hot-chatrooms"]);
                });
            }
        },

        initialCheck : function() {
            chatProcessor.activeChecking = true;

            chatFunctions.handleAjaxCallWithJSON(checkInitialChatStatusUrl, generalParams, function(jsonReply) {
                if (jsonReply["i-have-unread-msgs"] == true) {

                    // OK, INIT call is necessary so i'll leave this appending in JS instead of template rendering before ajax call
                    // add card icon with number of "hot" chatrooms to chat overlay bottom menu item
                    var $chatOverlayWrap = chatFunctions.chatOverlayWrap();
                    var $bottomMenuItemCont = $chatOverlayWrap.find(".chat-menu-bottom .no-dropdown.all-chat-rooms a.chat-opener");
                    chatFunctions.createCard($bottomMenuItemCont, jsonReply["number-of-hot-chatrooms"]);

                    jsonReply["hot-chatrooms"].forEach(function(hotChatroom) {
                        chatProcessor.data.chatRooms[hotChatroom["chatroom"]["hash"]] = {
                            chatRoom            : new ChatRoom(hotChatroom["chatroom"]["hash"]),
                            lastMessageId       : hotChatroom["last-message"]["id"],
                            fetchingNecessary   : false
                        };
                    });
                }
                chatProcessor.activeChecking = false;
            });
        },


        secondaryCheck : function(callback) {

            if (!chatProcessor.activeChecking) {

                chatProcessor.activeChecking = true;

                chatFunctions.handleAjaxCallWithJSON(checkChatStatusUrl, generalParams, function(jsonReply) {
                    var changesOccurred = false;

                    if (jsonReply["i-have-unread-msgs"] == true) {

                        var currHash;
                        jsonReply["hot-chatrooms"].forEach(function(hotChatroom) {
                            currHash = hotChatroom["chatroom"]["hash"];
                            if(currHash in chatProcessor.data.chatRooms) {
                                if(hotChatroom["last-message"]["id"] != chatProcessor.data.chatRooms[currHash].lastMessageId) {
                                    changesOccurred                                             = true;
                                    chatProcessor.data.chatRooms[currHash].lastMessageId        = hotChatroom["last-message"]["id"];
                                    chatProcessor.data.chatRooms[currHash].fetchingNecessary    = true;
                                }
                            } else {
                                changesOccurred = true;
                                chatProcessor.data.chatRooms[hotChatroom["chatroom"]["hash"]] = {
                                    chatRoom            : new ChatRoom(hotChatroom["chatroom"]["hash"]),
                                    lastMessageId       : hotChatroom["last-message"]["id"],
                                    fetchingNecessary   : true
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






        refreshChats : function(jsonReply) {
            chatProcessor.resetNoActivityCounter();

            var $chatOverlayWrap = chatFunctions.chatOverlayWrap();
            var $bottomMenuItemCont = $chatOverlayWrap.find(".chat-menu-bottom .no-dropdown.all-chat-rooms a.chat-opener");
            chatFunctions.createCard($bottomMenuItemCont, jsonReply["number-of-hot-chatrooms"]);


            chatFunctions.headerAlarmIcon("add");
            var sound = new Howl({
                urls: ['public/sound/fbChatSound.mp3']
            }).play();

            var $chatRoomMessagesCont = chatFunctions.currentChatContainer();

            if($chatRoomMessagesCont.hasClass("open")) {
                // first populate open chats with new activity, then reinit menus
                chatProcessor.fetchChats($chatRoomMessagesCont);
                chatFunctions.reinitialiseMenus();

            } else {
                // first reinitialise menus, then fetch new chats...

                chatFunctions.reinitialiseMenus();
                chatProcessor.fetchChats($chatRoomMessagesCont);
            }

            chatFunctions.reinitialiseMenus();

        },
        fetchChats : function($chatRoomMessagesCont) {
            for (var hashKey in chatProcessor.data.chatRooms) {
                if (chatProcessor.data.chatRooms.hasOwnProperty(hashKey)) {
                    if(chatProcessor.data.chatRooms[hashKey].fetchingNecessary == true) {
                        chatProcessor.getChat(hashKey, $chatRoomMessagesCont);
                        chatProcessor.data.chatRooms[hashKey].fetchingNecessary = false;
                    }
                }
            }
        },


        getChat : function(hash, $chatRoomMessagesCont){

            var $existingChatRoomMessages = $chatRoomMessagesCont.find(
                ".chat-conversation-holder[data-chat-conversation-hash*='" + hash + "']"
            );

            if ($existingChatRoomMessages.length > 0) {
                var myParams = {};
                myParams["chat-room-hash"] = hash;
                var params = $.extend(generalParams, myParams);

                chatFunctions.handleAjaxCall(fetchNewMessagesUrl, params, function(reply) {
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
                        $messagesContainer.animate({scrollTop: $messagesContainer[0].scrollHeight});
                    }, 25);
                });

            }
        },

        getCallbackDelay : function() {
            // Setting a timeout for the next request,
            // depending on the chat activity and some random sugar

//            var nextRequest = 1500;
//            if(chatProcessor.data.noActivity > 5){
//                nextRequest = 1000*randomBetween(2,5);
//            }
//            if(chatProcessor.data.noActivity > 15){
//                nextRequest = 1000*[3, 4, 5, 6][randomBetween(0,3)];
//            }
//            if(chatProcessor.data.noActivity > 25){
//                nextRequest = 1000*randomBetween(3,6);
//            }
//            if(chatProcessor.data.noActivity > 40){
//                nextRequest = 1000*[5, 7, 10][randomBetween(0,2)];
//            }
//            if(chatProcessor.data.noActivity > 75){
//                nextRequest = 1000*[5, 10, 15][randomBetween(0,2)];
//            }
//            if(chatProcessor.data.noActivity > 100){
//                nextRequest = 1000*[10, 20, 30][randomBetween(0,2)];
//            }
//            if(chatProcessor.data.noActivity > 150){
//                nextRequest = 1000*[10, 25, 50][randomBetween(0,2)];
//            }
            var nextRequest = 1500;
            if(chatProcessor.data.activityAfterInit ==  true) {
                if(chatProcessor.data.noActivity > 5){
                    nextRequest = 1000*randomBetween(2,5);
                }
                if(chatProcessor.data.noActivity > 10){
                    nextRequest = 1000*[3, 4, 5, 6][randomBetween(0,3)];
                }
                if(chatProcessor.data.noActivity > 20){
                    nextRequest = 1000*randomBetween(3,6);
                }
                if(chatProcessor.data.noActivity > 30){
                    nextRequest = 1000*[5, 7, 10][randomBetween(0,2)];
                }
                if(chatProcessor.data.noActivity > 50){
                    nextRequest = 1000*[5, 10, 15][randomBetween(0,2)];
                }
                if(chatProcessor.data.noActivity > 75){
                    nextRequest = 1000*[10, 20, 30][randomBetween(0,2)];
                }
                if(chatProcessor.data.noActivity > 100){
                    nextRequest = 1000*[10, 25, 50][randomBetween(0,2)];
                }
            } else {
                if(chatProcessor.data.noActivity > 5){
                    nextRequest = 1000*randomBetween(2,5);
                }
                if(chatProcessor.data.noActivity > 10){
                    nextRequest = 1000*[4, 6, 8][randomBetween(0,2)];
                }
                if(chatProcessor.data.noActivity > 15){
                    nextRequest = 1000*randomBetween(5,10);
                }
                if(chatProcessor.data.noActivity > 20){
                    nextRequest = 1000*[5, 10, 15][randomBetween(0,2)];
                }
                if(chatProcessor.data.noActivity > 30){
                    nextRequest = 1000*[10, 20, 50][randomBetween(0,2)];
                }
                if(chatProcessor.data.noActivity > 50){
                    nextRequest = 1000*[10, 40, 60][randomBetween(0,2)];
                }
                if(chatProcessor.data.noActivity > 100){
                    nextRequest = 1000*[20, 50, 100][randomBetween(0,2)];
                }
            }

            nextRequest = nextRequest*2;
            console.log("Next request in " + nextRequest.toString() + "secs");
            return nextRequest;
        },

        resetNoActivityCounter : function() {
            chatProcessor.data.noActivity = 0;
        },


        // This method displays an error message on the top of the page:

        displayError : function(msg){
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

            setTimeout(function(){
                $elem.click();
            },8000);

            var $existingErrorMessage = $cont.find(".chat-error-message");
            if($existingErrorMessage.length > 0) {
                $existingErrorMessage.fadeOut(function(){
                    $existingErrorMessage.remove();
                    $elem.hide().appendTo($cont).slideDown();
                });
            } else {
                $elem.hide().appendTo($cont).slideDown();
            }
        }

    };




    var scrollApi,
        generalParams,

        populateUsersUrl,
        populateChatRoomsUrl,
        populateChatRoomsAndUsersUrl,
        populateChatRoomMessagesUrl,
        populateChatRoomUrl,
        createChatRoomMessagesUrl,
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
        populateChatRoomMessagesUrl = $("#populate-chat-room-messages-url").val();
        populateChatRoomUrl = $("#populate-chat-room-url").val();
        createChatRoomMessagesUrl = $("#create-chat-room-messages-url").val();
        createChatRoomUrl = $("#create-chat-room-url").val();

        initiateChatRoomUrl = $("#initiate-chat-url").val();
        appendMessageToChatUrl = $("#append-message-to-chat-url").val();

        checkChatStatusUrl = $('#check-chat-status-url').val();
        checkInitialChatStatusUrl = $('#check-initial-chat-status-url').val();

        refreshChatStatusUrl = $('#refresh-chat-status-url').val();
        fetchNewMessagesUrl = $('#fetch-new-messages-url').val();

        scrollApi = $('.chat-content-holder').jScrollPane( {
    //            autoReinitialise: true
            verticalGutter: 0,
            horizontalGutter: 2
        }).data('jsp');
        
        chatProcessor.init();
//
//        $('.current-chat-messages-container').mixItUp({
//            animation: {
//                duration: 700
//            },
//            layout: {
//                display: 'block'
//            }
//        });
        function onResize() {
            scrollApi.reinitialise();
        }
        window.addEventListener("resize", onResize);



        $('nav.chat-menu-bottom .no-dropdown').click(function(){
            //scrollApi.reinitialise();
            var $chatNavFilters = $("#chat-nav-filters");
            var $filters = $chatNavFilters.find(".filter");

            var $descriptionsNavFilters = $(".section-desc-filters");
            var $filterDesc = $descriptionsNavFilters.find(".filter");

            var filterString = $(this).data("filter");

            // add filter-name as a class to the main holder
            var $chatContentHolder = $(".chat-content-holder");
            var classes = $chatContentHolder.attr("class").split(" ").filter(function(c) {
                return c.lastIndexOf('pane-', 0) !== 0;
            });
            $chatContentHolder.attr("class", $.trim(classes.join(" ")));
            $chatContentHolder.addClass("pane-" + filterString);


            if ($(this).hasClass("open")) {
                $(this).removeClass("open");
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
                $(this).addClass("open");
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
        });




        // users section - initalize if not yet initialised
        $('nav.chat-menu-bottom .no-dropdown.all-users').click(function(){
            // users container
            var $usersCont = chatFunctions.usersContainer();

            if($usersCont.hasClass("empty")) {
                $usersCont.removeClass("empty");
                chatFunctions.appendGifs();

                // ajax call to populate all users info
                $.when(chatFunctions.startLoading(populateUsersUrl, generalParams)).done(function(reply) {
                    chatFunctions.displayRequestReply(reply, populateUsersUrl, $usersCont);
                    chatFunctions.removeGifs();
                    scrollApi.reinitialise();
                });
            }
        });



        // chat rooms section - initalize if not yet initialised
        $('nav.chat-menu-bottom .no-dropdown.all-chat-rooms').click(function(){

            // chat rooms container
            var $chatRoomsCont = chatFunctions.chatRoomsContainer();

            if($chatRoomsCont.hasClass("empty")) {
                $chatRoomsCont.removeClass("empty");
                chatFunctions.appendGifs();

                // ajax call to populate all chat rooms info
                $.when(chatFunctions.startLoading(populateChatRoomsUrl, generalParams)).done(function(reply) {
                    chatFunctions.displayRequestReply(reply, populateChatRoomsUrl, $chatRoomsCont);
                    chatFunctions.removeGifs();
                    scrollApi.reinitialise();
                });
            }
        });

    });




    $(document).on("click", '.chat-nav-filters .current-chat-messages-container .submit-message .submitter.creator', function() {
        var $chatConvHolder = $(this).closest(".chat-conversation-holder");
        createChat($chatConvHolder);
    });

    $(document).on("keydown", '.chat-nav-filters .current-chat-messages-container .submit-message .textarea-submitter.creating', function(e) {
        var $chatConvHolder = $(this).closest(".chat-conversation-holder");
        if(e.which == 13 && e.shiftKey) {

        } else if (e.which == 13) {
            e.preventDefault(); //Stops enter from creating a new line
            createChat($chatConvHolder);
        }
    });


    function createChat($chatConversationHolder) {
        chatProcessor.resetNoActivityCounter();

        var userId = $chatConversationHolder.data("chat-cr-user-id");
        var $textarea = $chatConversationHolder.find(".textarea-submitter");
        var textMessageBody = $textarea.val();
        if(textMessageBody != null && textMessageBody !== "") {
            $textarea.attr("readonly", "true");
            chatFunctions.appendGifs();

            $chatConversationHolder.find(".textarea-submitter").val("");

            chatFunctions.changeReqStatus("pend-req");

            var myParams = {};
            myParams["user-id"] = userId;
            myParams["message-body"] = textMessageBody;
            var params = $.extend(generalParams, myParams);
            chatFunctions.changeReqStatus("act-req");

            $.when(chatFunctions.startLoading(initiateChatRoomUrl, params)).done(function(replyMessages) {
                chatFunctions.changeReqStatus("end-req");

                var $stub = $('<div />',{
                    html	: replyMessages,
                    class   : 'mix main-mix-chat conversation-container'
                });

                var $newChatMessages = $('#conv-msg-cr-' + userId);
                var $newChatRoom = $('#conv-info-cr-' + userId);

                var $oldHolder = $newChatMessages.find(".chat-conversation-holder");

                var hash = $stub.find(".chat-conversation-holder").data("chat-conversation-hash");
                $newChatMessages.attr("id", 'conv-msg-' + hash);
                $newChatRoom.attr("id", 'conv-info-' + hash);
                $oldHolder.attr("data-chat-conversation-hash", hash);
                $oldHolder.html($stub.find(".chat-conversation-holder").html());
                $newChatRoom.find(".chat-room-holder").attr("data-chat-room-hash", hash);

                chatFunctions.removeGifs();

                var $messagesContainer = $newChatMessages.find(".messages-container");
                setTimeout(function() {
                    $messagesContainer.animate({scrollTop: $messagesContainer[0].scrollHeight});
                    $chatConversationHolder.find(".textarea-submitter").focus();
                }, 25);

                chatFunctions.reinitialiseMenus();

                chatFunctions.changeReqStatus("no-req");

            });
        } else {
            chatProcessor.displayError("Upiši poruku prije slanja!");
        }
    }



    $(document).on("click", '.chat-nav-filters .current-chat-messages-container .submit-message .submitter.appender', function() {
        var $chatConvHolder = $(this).closest(".chat-conversation-holder");
        chatProcessor.refreshReadStatusOfChatroom($chatConvHolder.data("chat-conversation-hash"));

        appendMessageToChat($chatConvHolder);
    });

    $(document).on("keydown", '.chat-nav-filters .current-chat-messages-container .submit-message .textarea-submitter.appending', function(e) {
        var $chatConvHolder = $(this).closest(".chat-conversation-holder");
        chatProcessor.refreshReadStatusOfChatroom($chatConvHolder.data("chat-conversation-hash"));

        if(e.which == 13 && e.shiftKey) {

        } else if (e.which == 13) {
            e.preventDefault(); //Stops enter from creating a new line
            appendMessageToChat($chatConvHolder);
        }
    });

    $(document).on("click", '.chat-nav-filters .current-chat-messages-container .submit-message .textarea-submitter.appending, ' +
        '.chat-nav-filters .current-chat-messages-container .messages-container', function() {
        var $chatConvHolder = $(this).closest(".chat-conversation-holder");
        var hash = $chatConvHolder.data("chat-conversation-hash");
        if (typeof hash != "undefined" && hash != null) {
            chatProcessor.refreshReadStatusOfChatroom($chatConvHolder.data("chat-conversation-hash"));
        }
    });

    function appendMessageToChat($chatConversationHolder) {
        chatProcessor.resetNoActivityCounter();
//        chatProcessor.secondaryCheck();

        var chatRoomHash = $chatConversationHolder.data("chat-conversation-hash");
        var textMessageBody = $chatConversationHolder.find(".textarea-submitter").val();
        if(textMessageBody != null && textMessageBody !== "") {
            chatProcessor.activeChecking = true;

            $chatConversationHolder.find(".textarea-submitter").val("");


            var myParams = {};
            myParams["chat-room-hash"] = chatRoomHash;
            myParams["message-body"] = textMessageBody;
            var params = $.extend(generalParams, myParams);
            var $lights = $('<i>', {
                class : 'light l-1 no fa fa-fw fa-circle'
            });
            var lightsHolder = chatFunctions.lightsHolder();
            lightsHolder.append($lights);
            $lights.removeClass("no");
            $lights.addClass("act");


            chatFunctions.handleAjaxCall(appendMessageToChatUrl, params, function(replyMessages) {
                $lights.removeClass("act");
                $lights.addClass("pend");

                var $stub = $('<div />',{
                    html	: replyMessages
                });
                var $messagesContainer = $chatConversationHolder.find(".messages-container");

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
                $lights.removeClass("pend");
                $lights.addClass("end");
                setTimeout(function() {
                    $messagesContainer.animate({scrollTop: $messagesContainer[0].scrollHeight});
                    $chatConversationHolder.find(".textarea-submitter").focus();
                }, 25);

                // If until now this was chat room with unread messages, now it's definitely not
                // remove classes in user menu and active chatroom conversations menu
                chatFunctions.reinitialiseMenus();

                chatProcessor.activeChecking = false;
                setTimeout(function() {
                    $lights.removeClass("end");
                    $lights.addClass("no");
                    $lights.fadeOut(function(){
                        $lights.remove();
                    });

                }, 600);
            });

        } else {
            chatProcessor.displayError("Upiši poruku prije slanja!");
        }
    }



    $(document).on("click", '.chat-nav-filters .users-container a.user.chat-active.opener' +
        ', .chat-nav-filters .chat-rooms-container a.chat-room.opener', function() {
        var hash = $(this).data("chat-room-hash");

        chatProcessor.refreshReadStatusOfChatroom(hash);

        var $chatRoomMessagesCont = chatFunctions.currentChatContainer();

        var $sectionDescFilter = $("#section-desc-filters");
        var $descSection = $sectionDescFilter.find(".filter-current-chat");
        var $chatRoomCont = $descSection.find(".current-chat-room-container");


        var myParams = {};
        myParams["chat-room-hash"] = hash;
        var params = $.extend(generalParams, myParams);

        loadChat();

        function loadChat() {

            if($chatRoomMessagesCont.hasClass("empty") && $chatRoomCont.hasClass("empty")) {
                chatFunctions.appendGifs();

                $chatRoomCont.removeClass("empty");
                $chatRoomMessagesCont.removeClass("empty");

                // load chat room with ajax
                populateChat();

            } else {
                var $existingChatRoomMessages = $chatRoomMessagesCont.find(
                    ".chat-conversation-holder[data-chat-conversation-hash*='" + hash + "']"
                );
                var $existingChatRooms = $chatRoomCont.find(
                    ".chat-room-holder[data-chat-room-hash*='" + hash + "']"
                );

                if ($existingChatRoomMessages.length > 0) {

                    var $allConversationMessages = $chatRoomMessagesCont.find(".chat-conversation-holder");
                    var $allConversationRooms = $chatRoomCont.find(".chat-room-holder");

                    chatFunctions.displayChatRoom(
                        $chatRoomMessagesCont,
                        $allConversationMessages.closest(".conversation-container"),
                        $existingChatRoomMessages.closest(".conversation-container"),
                        $allConversationRooms,
                        $existingChatRooms
                    );
                    $('nav.chat-menu-bottom .no-dropdown.current-chat-room').click();

                    scrollApi.reinitialise();
                    setTimeout(function() {
                        $existingChatRoomMessages.find(".textarea-submitter").focus();
                    }, 0);

                } else {
                    chatFunctions.appendGifs();
                    // load chat room with ajax
                    populateChat();
                }
            }


            function populateChat() {
                $.when(chatFunctions.startLoading(populateChatRoomMessagesUrl, params), chatFunctions.startLoading(populateChatRoomUrl, params)).done(function(replyMessages, replyInfo) {

                    var $newChatMessages = $('<div />',{
                        id		: 'conv-msg-' + hash,
                        html	: replyMessages[0],
                        class   : 'mix main-mix-chat conversation-container'
                    });
                    $chatRoomMessagesCont.append($newChatMessages);


                    var $newChatInfo = $('<div />',{
                        id		: 'conv-info-' + hash,
                        html	: replyInfo[0]
                    });
                    $chatRoomCont.append($newChatInfo);


                    var $chatMessages = $newChatMessages.find(".chat-conversation-holder");
                    var $chatRoom = $newChatInfo.find(".chat-room-holder");

                    var $allConversationMessages = $chatRoomMessagesCont.find(".chat-conversation-holder");
                    var $allConversationRooms = $chatRoomCont.find(".chat-room-holder");

                    chatFunctions.displayChatRoom(
                        $chatRoomMessagesCont,
                        $allConversationMessages.closest(".conversation-container"),
                        $chatMessages.closest(".conversation-container"),
                        $allConversationRooms,
                        $chatRoom
                    );

                    $('nav.chat-menu-bottom .no-dropdown.current-chat-room').click();

                    chatFunctions.removeGifs();
                    scrollApi.reinitialise();
                    var $messagesContainer = $newChatMessages.find(".messages-container");
                    setTimeout(function() {
                        $messagesContainer.animate({scrollTop: $messagesContainer[0].scrollHeight});
                        $chatMessages.find(".textarea-submitter").focus();
                    }, 25);
                });
            }


        }

    });




    $(document).on("click", '.chat-nav-filters .users-container a.user.chat-active.creator', function() {
        var userId = $(this).data("user-id");

        var $chatNavFilters = $("#chat-nav-filters");
        var $section = $chatNavFilters.find(".filter-current-chat");
        var $chatRoomMessagesCont = $section.find(".current-chat-messages-container");

        var $sectionDescFilter = $("#section-desc-filters");
        var $descSection = $sectionDescFilter.find(".filter-current-chat");
        var $chatRoomCont = $descSection.find(".current-chat-room-container");


        var myParams = {};
        myParams["user-id"] = userId;
        var params = $.extend(generalParams, myParams);


        createChat();


        function createChat() {

            if($chatRoomMessagesCont.hasClass("empty") && $chatRoomCont.hasClass("empty")) {
                chatFunctions.appendGifs();

                $chatRoomCont.removeClass("empty");
                $chatRoomMessagesCont.removeClass("empty");

                // load empty chat room with ajax
                initialiseChat();

            } else {
                var $existingChatRoomMessages = $chatRoomMessagesCont.find(
                    ".chat-conversation-holder[data-chat-cr-user-id='" + userId + "']"
                );
                var $existingChatRooms = $chatRoomCont.find(
                    ".chat-room-holder[data-chat-cr-user-id='" + userId + "']"
                );

                if ($existingChatRoomMessages.length > 0) {

                    var $allConversationMessages = $chatRoomMessagesCont.find(".chat-conversation-holder");
                    var $allConversationRooms = $chatRoomCont.find(".chat-room-holder");

                    chatFunctions.displayChatRoom(
                        $chatRoomMessagesCont,
                        $allConversationMessages.closest(".conversation-container"),
                        $existingChatRoomMessages.closest(".conversation-container"),
                        $allConversationRooms,
                        $existingChatRooms
                    );
                    $('nav.chat-menu-bottom .no-dropdown.current-chat-room').click();

                    scrollApi.reinitialise();

                } else {
                    chatFunctions.appendGifs();
                    // load empty chat room with ajax
                    initialiseChat();
                }
            }



            function initialiseChat() {
                $.when(chatFunctions.startLoading(createChatRoomMessagesUrl, params),
                        chatFunctions.startLoading(createChatRoomUrl, params)).done(function(replyMessages, replyInfo) {

                    var $newChatMessages = $('<div />',{
                        id		: 'conv-msg-cr-' + userId,
                        html	: replyMessages[0],
                        class   : 'mix main-mix-chat conversation-container'
                    });

                    $chatRoomMessagesCont.append($newChatMessages);


                    var $newChatInfo = $('<div />',{
                        id		: 'conv-info-cr-' + userId,
                        html	: replyInfo[0]
                    });
                    $chatRoomCont.append($newChatInfo);


                    var $chatMessages = $newChatMessages.find(".chat-conversation-holder");
                    var $chatRoom = $newChatInfo.find(".chat-room-holder");

                    var $allConversationMessages = $chatRoomMessagesCont.find(".chat-conversation-holder");
                    var $allConversationRooms = $chatRoomCont.find(".chat-room-holder");

                    chatFunctions.displayChatRoom(
                        $chatRoomMessagesCont,
                        $allConversationMessages.closest(".conversation-container"),
                        $chatMessages.closest(".conversation-container"),
                        $allConversationRooms,
                        $chatRoom
                    );

                    $('nav.chat-menu-bottom .no-dropdown.current-chat-room').click();

                    chatFunctions.removeGifs();
                    scrollApi.reinitialise();

                    var $messagesContainer = $newChatMessages.find(".messages-container");
                    setTimeout(function() {
                        $messagesContainer.animate({scrollTop: $messagesContainer[0].scrollHeight});
                        $chatMessages.find(".textarea-submitter").focus();
                    }, 25);

                });
            }


        }
    });




}).call();