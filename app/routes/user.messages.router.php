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
            $messages_for_user = Message::getMessagesForReceiver($app->auth_user->id, "user");
            $app->render('user/user.messages.home.twig', array(
                'user' => $app->auth_user,
                'messages' => $messages_for_user,
                'active_page' => "user.main",
                'active_item' => "user.main.messages"
            ));

        })->name('user.messages.home');


        $app->post('/change-read-status', $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {

                $body = $app->request->getBody();
                $json_data_received = json_decode($body, true);

            }

        })->name('user.messages.change-read-status.post');



    });



});




?>
