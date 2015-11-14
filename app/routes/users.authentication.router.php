<?php

use app\helpers\Sessions;
use app\helpers\Configuration as Cfg;
use app\model\User\User;
use app\helpers\Auth;
use app\helpers\Mailer;
use app\helpers\Hash;
use \app\model\Messages\Logger;

$app->get('/clanovi/prijava', $guest_user(), function () use ($app) {
    $app->render('authentication/user_login.twig', array(
        ));
})->name('user.login');


$app->get('/clanovi/odjava', function () use ($app) {
    Auth::doUserLogout();

    $app->flash('success', "Uspješno ste odjavljeni!");
    $app->redirect($app->urlFor('home'));
})->name('user.logout');



$app->post('/clanovi/prijava', $guest_user(), function () use ($app) {
    $p_username = $app->request->post('username');
    $p_password = $app->request->post('password');
    $p_remember_me = $app->request->post('remember-me');

    $validation_result = User::validateUserLogin($p_username, $p_password);

    if(!($validation_result["validated"])) {
        // Valudation failed
        if(isset($validation_result["errors"])) {
            $app->flash('errors',  $validation_result["errors"]);
        }
        $app->redirect($app->urlFor('user.login'));
    } else {
        // Validation of input data successful
        $user = Auth::doUserLogin($validation_result["user"]);
        $app->flash('success', "Uspješna prijava!");

        if (isset($p_remember_me) && $p_remember_me === "on") {
            Auth::setUserRememberMe($user);
            $app->flash('statuses', "Koristeći ovaj preglednik, sustav će Vas zapamtiti.");
        }
        Logger::logClassicUserLogin($user);
        $app->redirect($app->urlFor('user.profile.home'));
    }

})->name('user.login.post');



