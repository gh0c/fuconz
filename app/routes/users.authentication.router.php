<?php

use app\helpers\Sessions;
use app\helpers\Configuration as Cfg;
use app\model\User\RegisteredUser;
use app\helpers\Auth;
use app\helpers\Mailer;
use app\helpers\Hash;

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

    if(!isset($p_username) || $p_username === "" || !isset($p_password) || $p_password === "")
    {
        $app->flash('errors',  "Nedostaju podaci za prijavu! \n" .
            "Unos korisničkog imena i lozinke su obavezni za prijavu.");
        $app->redirect($app->urlFor('user.login'));
    }

    $user = RegisteredUser::getUserByUsername($p_username);
    if (!$user) {
        $app->flash('errors', "Neispravo korisničko ime i/ili lozinka.");
        $app->redirect($app->urlFor('user.login'));
    }
    else {

        $success = Hash::passwordCheck($p_password . $user->getPasswordSalt(), $user->getPassword());

        if($success) {

            if(!$user->activated()) {
                $app->flash('errors', "Korisnik još nije aktiviran. \n");
                $app->flash('statuses', "U općenitom slučaju, prilikom registracije na unesenu e-mail adresu poslan je link za aktivaciju.");
                $app->redirect($app->urlFor('user.login'));
            } else {
                $user = Auth::doUserLogin($user);
                $app->flash('success', "Uspješna prijava!");

                if (isset($p_remember_me) && $p_remember_me === "on") {
                    Auth::setUserRememberMe($user);
                    $app->flash('statuses', "Koristeći ovaj preglednik, sustav će Vas zapamtiti.");
                }
                $app->redirect($app->urlFor('user.home'));
            }


        } else {
            $app->flash('errors', "Neispravo korisničko ime i/ili lozinka.");
            $app->redirect($app->urlFor('user.login'));
        }
    }

})->name('user.login.post');



