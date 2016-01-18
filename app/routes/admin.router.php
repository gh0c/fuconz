<?php
use \app\model\User\User;
use app\helpers\Sessions;
use app\helpers\Configuration as Cfg;
use app\helpers\Hash;
use app\model\Admin\Admin;
use \app\model\Messages\Message;
use \app\model\Messages\Logger;


$app->group('/admin', function () use ($app, $authenticated_admin) {

    $app->get('/', function () use ($app) {
        $app->redirect($app->urlFor('admin.home'));

    })->name('administrator.home');


    $app->get('/pocetna', $authenticated_admin(), function () use ($app) {

        $app->render('admin/admin.home.twig', array(
            'auth_admin' => $app->auth_admin,
            'active_page' => "admin",
            'active_item' => "admin.home"));
    })->name('admin.home');


    $app->post('/ucitaj-poruke', $authenticated_admin(), function() use ($app) {
        if($app->request->isAjax()) {

            $body = $app->request->getBody();
            $json_data_received = json_decode($body, true);

            $unread_messages = Message::getUnreadMessagesForReceiver($app->auth_admin->id, "admin");
            $app->render('admin/admin.home.unread_messages.twig', array(
                'auth_admin' => $app->auth_admin,
                'unread_messages' => $unread_messages
            ));
            exit();
        }
    })->name('admin.home.load.messages.post');


    $app->post('/load-reservations', $authenticated_admin(), function() use ($app) {
        if($app->request->isAjax()) {

            $body = $app->request->getBody();
            $json_data_received = json_decode($body, true);

            $unread_messages = Message::getUnreadMessagesForReceiver($app->auth_user->id, "user");
            $app->render('user/profile/home/unread_messages.twig', array(
                'user' => $app->auth_user,
                'unread_messages' => $unread_messages
            ));
            exit();
        }
    })->name('admin.home.load.reservations.post');


    $app->get('/poruke/', $authenticated_admin(), function() use ($app) {
        $messages_for_admin = Message::getMessagesInRangeForReceiver($app->auth_admin->id, "admin");
        $app->render('admin/admin.messages.home.twig', array(
            'admin' => $app->auth_admin,
            'messages' => $messages_for_admin,
            'active_page' => "admin",
            'active_item' => "admin.messages"
        ));
    })->name('admin.messages.home');


    $app->post('/poruke/change-read-status', $authenticated_admin(), function() use ($app) {
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
                if (!$message || !isset($message->id) || $message->receiver_id != $app->auth_admin->id
                    || $message->receiver_type != $p_msg_receiver) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        "error" => "Nije pronađena poruka unesenog identifikatora (:{$p_msg_id})vezana za admina!"
                    ));
                    exit();
                } else {
                    $message->setHasBeenRead($p_flag);

                    header('Content-Type: application/json');
                    echo json_encode(array(
                        "newNumberOfUnread" => Message::numberOfUnreadForReceiver($app->auth_admin->id, "admin")
                    ));
                    exit();
                }
            }
        }
    })->name('admin.home.messages.change-read-status.post');


    $app->get('/slanje-masovne-poruke/', $authenticated_admin(), function() use ($app) {
        $app->render('admin/communication/broadcast.message.new.twig', array(
            'admin' => $app->auth_admin,
            'active_page' => "admin",
            'active_item' => "admin.broadcast-message"
        ));
    })->name('admin.messages.broadcast.new');

    $app->post('/slanje-masovne-poruke/', $authenticated_admin(), function() use ($app) {
        $p_message = $app->request->post("message-body");
        $sent_msg = Logger::logAdminBroadcastMessage($p_message);

        $app->flash('admin_success', "Uspješno je poslana poruka svim članovima!");
        $app->flash('sent_msg', $sent_msg);

        $app->redirect($app->urlFor('admin.messages.broadcast.new'));

    })->name('admin.messages.broadcast.new.post');
});
?>
