<?php
use \app\model\User\User;
use \app\helpers\Sessions;
use \app\helpers\Configuration as Cfg;
use \app\helpers\Hash;

use \app\model\Messages\Message;

use \app\helpers\Calendar;

use \app\model\Messages\Logger;

$app->group('/clanovi', function () use ($app, $authenticated_user) {

    $app->group('/poruke', function() use ($app, $authenticated_user){

        $app->get('/', $authenticated_user(), function() use ($app) {
            $messages_for_user = Message::getMessagesInRangeForReceiver($app->auth_user->id, "user");
            $app->render('user/user.messages.home.twig', array(
                'user' => $app->auth_user,
                'messages' => $messages_for_user,
                'active_page' => "user.main",
                'active_item' => "user.main.messages"
            ));

        })->name('user.messages.home');


        $app->post('/postavi-procitanost', $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {

                $body = $app->request->getBody();
                $json_data_received = json_decode($body, true);

                $p_msg_id = $json_data_received["msg-id"];
                $p_msg_receiver = $json_data_received["msg-receiver"];
                $p_flag = (int)$json_data_received["flag"];

                if(!isset($p_msg_id) || !isset($p_msg_receiver) || !isset($p_flag) ||
                    $p_msg_id === "" || $p_msg_receiver === "" || !in_array($p_flag, array(0,1))) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        "error" => "Nepravilna manipulacija podacima koji se šalju za promjenu pročitanosti poruke!"
                    ));
                    exit();
                } else {
                    $message = Message::getMessageById($p_msg_id);
                    if (!$message || !isset($message->id) || $message->receiver_id != $app->auth_user->id
                        || $message->receiver_type != $p_msg_receiver) {
                        header('Content-Type: application/json');
                        echo json_encode(array(
                            "error" => "Nije pronađena poruka unesenog identifikatora (:{$p_msg_id})vezana za tvoj članski profil!"
                        ));
                        exit();
                    } else {
                        $message->setHasBeenRead($p_flag);

                        header('Content-Type: application/json');
                        echo json_encode(array(
                            "newNumberOfUnread" => Message::numberOfUnreadForReceiver($app->auth_user->id, "user")
                        ));
                        exit();
                    }
                }
            }
        })->name('user.messages.change-read-status.post');



    });



});




?>
