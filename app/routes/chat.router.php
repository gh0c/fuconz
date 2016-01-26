<?php
use \app\model\User\User;
use \app\model\Chat\ChatRoom;
use \app\model\Chat\ChatMessage;

$app->group('/komunikacija', function () use ($app, $authenticated_user) {

    $app->group('/chat', $authenticated_user(), function () use ($app, $authenticated_user) {

        $app->get('/1', $authenticated_user(), function () use ($app) {
            $users = User::getUsers(100, "id ASC");


            $sender = $users[5];
            $receivers = array($users[2], $users[9]);
            $status = ChatRoom::createNew("A jebemumiša di si bio dok je grmilo",
                $sender, $receivers);


            $sender = $users[1];
            $receivers = array($users[5]);
            $status = ChatRoom::createNew("Oii Oii Oii",
                $sender, $receivers);

            $sender = $users[5];
            $receivers = array($users[10]);
            $status = ChatRoom::createNew("Oii2 Oii2 Oii",
                $sender, $receivers);



            echo "Status success: " . $status["success"];
        });

        $app->get('/4', $authenticated_user(), function () use ($app) {
            $texts = array(
                "Moj text www.jimmy.com",
                "Moj text www.jimmy",
                "moj http://losina.jimmy.com/",
                "moj https://losina.jimmy.com/",
                "moj http://losina.jimmy.com",
                "moj http://losina.jimmy.co/",
                "moj losina.jimmy.com",
                "moj losina.jimmy.com/",

            );
            foreach($texts as $t) {
                $tagg = \app\helpers\Text::textTagUrls($t);
                echo "<pre>For text:<br>" . $t . "<br>Tagg:<br>" . $tagg . "<br></pre>";

            }
        });

        $app->get('/5', $authenticated_user(), function () use ($app) {
            $texts = array(
                "Lošina :D",
                "ajde:daj",
                "ajde:Daj",
                ":Dhaha",
                "ajde:D",
                "haha",
                ":-)",
                ":D",
                ":D ok :D:D",
                "::D",
                ":D"

            );

            foreach($texts as $t) {
                $tagg = \app\helpers\Text::toChatString($t);
                echo "<pre>For text:<br>" . $t .
                    "<br>Chat msg:<br>" . $tagg . "<br></pre>";

            }


        });





        $app->post("/ucitaj-sve-korisnike", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {
                $users = User::getActiveUsersWithoutMe($app->auth_user->id, 1000, "username ASC");
                $app->render('user/chat/active_users.twig', array(
                    'users' => $users
                ));
                exit();
            }
        })->name('user.chat.load.users.post');


        $app->post("/ucitaj-sve-korisnikove-razgovore", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {
                $chat_rooms = ChatRoom::getByUser($app->auth_user->id);
                $app->render('user/chat/my_chat_rooms.twig', array(
                    'chat_rooms' => $chat_rooms
                ));
                exit();
            }
        })->name('user.chat.load.chat-rooms.post');


        $app->post("/ucitaj-korisnike-i-razgovore", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {
                $users = User::getActiveUsersWithoutMe($app->auth_user->id, 1000, "username ASC");
                $chat_rooms = ChatRoom::getByUser($app->auth_user->id);
                $app->render('user/chat/my_chat_rooms_and_users.twig', array(
                    'chat_rooms' => $chat_rooms,
                    'users' => $users
                ));
                exit();
            }
        })->name('user.chat.load.chat-rooms-and-users.post');


//
//        $app->post("/ucitaj-razgovor", $authenticated_user(), function() use ($app) {
//            if($app->request->isAjax()) {
//
//                $body = $app->request->getBody();
//                $json_data_received = json_decode($body, true);
//
//                $chat_room_hash = $json_data_received['chat-room-hash'];
//
//                $chat_room = ChatRoom::getByHash($chat_room_hash);
//
//                if($chat_room) {
//                    $messages = ChatMessage::getMessagesForChatRoom($chat_room->id);
//                    $app->render('user/chat/chat_messages.twig', array(
//                        'chat_messages' => $messages,
//                        'chat_room' => $chat_room
//                    ));
//                } else {
//                    echo "<pre>Mane štani kak bi Mara rekel,.</pre>";
//
//                }
//                exit();
//            }
//
//        })->name('user.chat.load.chat-room-messages.post');

        $app->post("/ucitaj-sobu-razgovora", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {

                try {
                    $body = $app->request->getBody();
                    $json_data_received = json_decode($body, true);

                    $chat_room_hash = $json_data_received['chat-room-hash'];

                    $chat_room = ChatRoom::getByHash($chat_room_hash);

                    if($chat_room) {
                        $messages = ChatMessage::getMessagesForChatRoom($chat_room->id);
                        $app->render('user/chat/load_chat_room.twig', array(
                            'chat_messages' => $messages,
                            'chat_room' => $chat_room
                        ));
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(array(
                            "error" => "Ne postoji razgovor unesenog identifikatora"
                        ));
                    }
                } catch (Exception $e) {
                    echo json_encode(array(
                        "error" => "Greska:" . $e->getMessage()
                    ));
                }
                exit();
            }

        })->name('user.chat.load.chat-room.post');



//        $app->post("/stvori-razgovor", $authenticated_user(), function() use ($app) {
//            if($app->request->isAjax()) {
//
//                $body = $app->request->getBody();
//                $json_data_received = json_decode($body, true);
//
//                $user_id = $json_data_received['user-id'];
//
//                $user = User::getUserById($user_id);
//
//                if($user) {
//                    $app->render('user/chat/new_chat_creation.twig', array(
//                        'user' => $user
//                    ));
//                }
//                exit();
//            }
//
//        })->name('user.chat.create.chat-room-messages.post');


        $app->post("/stvori-sobu-razgovora", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {


                try {
                    $body = $app->request->getBody();
                    $json_data_received = json_decode($body, true);

                    $user_id = $json_data_received['user-id'];

                    $user = User::getUserById($user_id);



                    if($user) {
                        $app->render('user/chat/load_chat_room_template.twig', array(
                            'user' => $user
                        ));
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(array(
                            "error" => "Ne postoji odabrani korisnik"
                        ));
                    }
                } catch (Exception $e) {
                    echo json_encode(array(
                        "error" => "Greska:" . $e->getMessage()
                    ));
                }
                exit();
            }

        })->name('user.chat.create.chat-room.post');



        $app->post("/iniciraj-novu-sobu-razgovora", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {
                try {
                    $body = $app->request->getBody();
                    $json_data_received = json_decode($body, true);

                    $user_id = $json_data_received['user-id'];
                    $message_body = $json_data_received['message-body'];

                    $message = htmlspecialchars($message_body);

                    $receiver = User::getUserById($user_id);


                    if($receiver && is_null($app->auth_user->chatConversationWith($receiver->id))) {
                        $status = ChatRoom::createNew($message,
                            $app->auth_user, array($receiver));

                        if($status["success"]) {
                            $chat_room = ChatRoom::getById($status["chat-room-id"]);

                            $messages = ChatMessage::getMessagesForChatRoom($chat_room->id);
                            $app->render('user/chat/chat_messages.twig', array(
                                'chat_messages' => $messages,
                                'chat_room' => $chat_room
                            ));

                        }  else {
                            header('Content-Type: application/json');
                            echo json_encode(array(
                                "error" => $status["err"]
                            ));
                        }

                    } else {
                        if(!is_null($app->auth_user->chatConversationWith($receiver->id))){
                            header('Content-Type: application/json');
                            echo json_encode(array(
                                "error" => "Već postoji razgovor s ovim korisnikom. Ručno osvježite stranicu ili malo pričekajte..."
                            ));
                        }
                    }
                } catch (Exception $e) {
                    echo json_encode(array(
                        "error" => "Greska:" . $e->getMessage()
                    ));
                }
                exit();
            }

        })->name('user.chat.initiate.new.post');


        $app->post("/dodaj-poruku-u-razgovor", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {
                try {
                    $body = $app->request->getBody();
                    $json_data_received = json_decode($body, true);

                    $chat_room_hash = $json_data_received['chat-room-hash'];
                    $message_body = $json_data_received['message-body'];

                    $message = htmlspecialchars($message_body);

                    $chat_room = ChatRoom::getByHash($chat_room_hash);

                    if($chat_room) {
                        $chat_messages = ChatMessage::getUsersUnreadMessagesForChatRoom(
                            $chat_room->id, $app->auth_user->id);
    //                    echo "<br>";var_dump($chat_messages);echo "<br>";
                        $status = $chat_room->addMessageTo($message, $app->auth_user);
                        if($status["success"]) {
                            $new_message = ChatMessage::getMessageById($status["msg-id"]);
                            $chat_messages [] = $new_message;
                            $app->render('user/chat/appended_new_message.twig', array(
                                'chat_messages' => $chat_messages
                            ));
                        }
                    }
                } catch (Exception $e) {
                    echo json_encode(array(
                        "error" => "Greska:" . $e->getMessage()
                    ));
                }
                exit();
            }

        })->name('user.chat.append.new.post');



        $app->post("/osvjezi-procitanost", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {

                header('Content-Type: application/json');
                try {

                    $body = $app->request->getBody();
                    $json_data_received = json_decode($body, true);

                    $chat_room_hash = $json_data_received['chat-room-hash'];
                    $last_message_in_view_id = $json_data_received['last-msg-id'];

                    $chat_room = ChatRoom::getByHash($chat_room_hash);

                    if($chat_room) {

                        if(!is_null($last_message_in_view_id)) {
                            $status = $chat_room->refreshUnreadStatusForUserWithMsg($app->auth_user->id, $last_message_in_view_id);
                        } else {
                            $status = $chat_room->refreshUnreadStatusForUser($app->auth_user->id);
                        }

                        if($status["success"]) {
                            $number_of_hot_chatrooms = ChatRoom::numberOfHotChatrooms($app->auth_user->id);
                            $i_have_new_unread = ($number_of_hot_chatrooms > 0) ? true : false;

                            $hot_chatrooms = array();
                            if ($i_have_new_unread) {
                                $hot_chatrooms = ChatRoom::getHotForUser($app->auth_user->id);
                            }
                            $chatrooms = array();

                            /* @var $chat_room ChatRoom */
                            foreach($hot_chatrooms as $chat_room) {
                                $chatrooms[] = array(
                                    "chatroom" => $chat_room,
                                    "number-of-unread-msgs" => $chat_room->numberOfUsersUnreadMessages($app->auth_user->id)
                                );
                            }
                            echo json_encode(array(
                                "i-have-unread-msgs" => $i_have_new_unread,
                                "number-of-hot-chatrooms" => $number_of_hot_chatrooms,
                                "hot-chatrooms" => $chatrooms
                            ));
                        } else {
                            header('Content-Type: application/json');
                            echo json_encode(array(
                                "error" => $status["err"]
                            ));
                        }
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(array(
                            "error" => "Ne postoji razgovor unesenog identifikatora"
                        ));
                    }
                } catch (Exception $e) {
                    echo json_encode(array(
                        "error" => "Greska:" . $e->getMessage()
                    ));
                }
                exit();

            }
        })->name('user.chat.refresh-status.post');


        $app->post("/provjera-statusa-razgovora", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {

                header('Content-Type: application/json');
                try {
                    $number_of_hot_chatrooms = ChatRoom::numberOfHotChatrooms($app->auth_user->id);
                    $i_have_new_unread = ($number_of_hot_chatrooms > 0) ? true : false;

                    $hot_chatrooms = array();
                    if ($i_have_new_unread) {
                        $hot_chatrooms = ChatRoom::getHotForUser($app->auth_user->id);
                    }
                    $chatrooms = array();

                    /* @var $chat_room ChatRoom */
                    foreach($hot_chatrooms as $chat_room) {
                        $chatrooms[] = array(
                            "chatroom" => $chat_room,
                            "number-of-unread-msgs" => $chat_room->numberOfUsersUnreadMessages($app->auth_user->id),
                            "last-message" => $chat_room->lastMessage()
                        );
                    }

                    echo json_encode(array(
                        "i-have-unread-msgs" => $i_have_new_unread,
                        "number-of-hot-chatrooms" => $number_of_hot_chatrooms,
                        "hot-chatrooms" => $chatrooms
                    ));
                } catch (Exception $e) {
                    echo json_encode(array(
                        "error" => "Greska:" . $e->getMessage()
                    ));
                }
                exit();
            }

        })->name('user.chat.check-status.post');




        $app->post("/inicijalna-provjera-statusa-razgovora", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {
                header('Content-Type: application/json');
                try {
                    $number_of_hot_chatrooms = ChatRoom::numberOfHotChatrooms($app->auth_user->id);
                    $i_have_new_unread = ($number_of_hot_chatrooms > 0) ? true : false;

                    $hot_chatrooms = array();
                    if ($i_have_new_unread) {
                        $hot_chatrooms = ChatRoom::getHotForUser($app->auth_user->id);
                    }
                    $chatrooms = array();

                    /* @var $chat_room ChatRoom */
                    foreach($hot_chatrooms as $chat_room) {
                        $chatrooms[] = array(
                            "chatroom" => $chat_room,
                            "number-of-unread-msgs" => $chat_room->numberOfUsersUnreadMessages($app->auth_user->id),
                            "last-message" => $chat_room->lastMessage()

                        );
                    }

                    echo json_encode(array(
                        "i-have-unread-msgs" => $i_have_new_unread,
                        "number-of-hot-chatrooms" => $number_of_hot_chatrooms,
                        "hot-chatrooms" => $chatrooms
                    ));
                } catch (Exception $e) {
                    echo json_encode(array(
                        "error" => "Greska:" . $e->getMessage()
                    ));
                }
                exit();
            }

        })->name('user.chat.check-initial-status.post');


        $app->post("/dohvati-neprocitane-poruke", $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {
                try {
                    $body = $app->request->getBody();
                    $json_data_received = json_decode($body, true);

                    $chat_room_hash = $json_data_received['chat-room-hash'];

                    $chat_room = ChatRoom::getByHash($chat_room_hash);

                    if($chat_room) {
//                        $status = $chat_room->addMessageTo($message, $app->auth_user);
//                        if($status["success"]) {
//                            $new_message = ChatMessage::getMessageById($status["msg-id"]);
//
//                            $app->render('user/chat/appended_new_message.twig', array(
//                                'message' => $new_message
//                            ));
//                        }
                        $messages = ChatMessage::getUsersUnreadMessagesForChatRoom($chat_room->id, $app->auth_user->id);
                        $app->render('user/chat/unread_chat_messages.twig', array(
                            'chat_messages' => $messages,
                            'chat_room' => $chat_room
                        ));
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(array(
                            "error" => "Ne postoji razgovor unesenog identifikatora"
                        ));
                    }
                } catch (Exception $e) {
                    echo json_encode(array(
                        "error" => "Greska:" . $e->getMessage()
                    ));
                }
                exit();

            }

        })->name('user.chat.fetch-new-messages.post');
    });

});
