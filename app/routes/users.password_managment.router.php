<?php

use app\helpers\Sessions;
use app\helpers\Configuration as Cfg;
use app\model\User\User;
use app\helpers\Auth;
use app\helpers\Mailer;
use app\helpers\Hash;

$app->group('/clanovi', function () use ($app, $guest_user, $authenticated_user) {


    $app->get('/zaboravljena-lozinka', $guest_user(), function () use ($app) {
        $app->render('authentication/user.password_recovery.twig', array(
        ));
    })->name('user.password_recovery');


    $app->post('/zaboravljena-lozinka', $guest_user(), function () use ($app) {
        $p_email = $app->request->post('email');
        if(!isset($p_email) || $p_email === "" ) {
            $app->flash('errors', "Nedostaju podaci za proces povratka lozinke! \n" .
                "Unos e-mail adrese obavezan je za prijavu.");
            $app->redirect($app->urlFor('user.login'));
        } else {
            $user = User::getUserByEmail($p_email);
            if (!$user) {
                $app->flash('errors', "Unesena e-mail adresa $p_email ne postoji u bazi registriranih članova.\n" .
                    "Ukoliko smatrate da je to pogreška, javite se na neki od navedenih kontakata.");
                $app->redirect($app->urlFor('user.password_recovery'));
            }
            else {
                if ($pass_reset = $user->activePassResetRequestExists()) {
                    $app->flash('statuses', "Imate aktivan zahtjev za za proces povratka zaboravljene lozinke!" .
                        "\nProvjerite e-mail ili pokušajte ponovno poslati zahtjev nakon " .
                        date("H:i (d.m.Y.)", $pass_reset->resetValidUntil()));
                    $app->redirect($app->urlFor('user.password_recovery'));

                }
                else {
                    $pass_reset = $user->createNewPasswordReset();

                    $url = Cfg::read('path.domain') . $app->urlFor('user.password_reset') .
                        "?user=" . urlencode($pass_reset->security) . "&reset-hash=" . urlencode($pass_reset->reset_hash);

                    if(Cfg::read('mail.send'))  {
                        $sent = Mailer::passwordRecovery(array(
                            'url' => $url,
                            'user' => $user,
                            'pass.reset' => $pass_reset
                        ));
                        if ($sent) {
                            $app->flash('statuses', "Poslan Vam je e-mail s poveznicom na formu za obnavljanje lozinke!");
                        }
                    }
                    $app->redirect($app->urlFor('user.login'));
                }
            }
        }

    })->name('user.password_recovery.post');


    $app->get('/obnova-lozinke', $guest_user(), function () use ($app) {
        $g_user = $app->request->get('user');
        $g_reset_hash = $app->request->get('reset-hash');

        if (!isset($g_reset_hash) || trim($g_reset_hash) == FALSE ||
            !isset($g_user) || trim($g_user) == FALSE) {
            $app->flash('errors', "Neispravan URL sa zahtjevom za reaktivacijom lozinke!");
            $app->redirect($app->urlFor('user.login'));
        }
        else {
            if(!$pass_reset = User::getPassResetByUserHash($g_user)) {
                $app->flash('errors', "Neispravan URL sa zahtjevom za reaktivacijom lozinke!");
                $app->redirect($app->urlFor('user.login'));
            }
            else if(!$user = User::getUserById($pass_reset->user_id)) {
                $app->flash('errors', "Nevažeći zahtjev za reaktivacijom lozinke!");
                $app->redirect($app->urlFor('user.login'));
            }
            else if(!$user->activePassResetRequestExists()) {
                $app->flash('errors', "Ne postoji aktivan zahtjev za reaktivacijom lozinke!");
                $app->redirect($app->urlFor('user.login'));
            }
            if (!Hash::hashCheck($pass_reset->reset_hash, Hash::hash($g_reset_hash))){
                $app->flash('errors', "Neispravan URL sa zahtjevom za reaktivacijom lozinke!");
                $app->redirect($app->urlFor('user.login'));
            }
            $app->flashNow('statuses', "Na korak ste od uspješne reaktivacije lozinke." .
                "\nUnesite e-mail adresu i novu lozinku");
            $app->render('authentication/user.password_reset.twig', array(
                'security' => $g_user,
                'hash' => $g_reset_hash
            ));
        }

    })->name('user.password_reset');


    $app->post('/obnova-lozinke', $guest_user(), function () use ($app) {
        $g_user = $app->request->get('user');
        $g_reset_hash = $app->request->get('reset-hash');
        $p_email = $app->request->post('email');
        $p_password_new = $app->request->post('new-password');
        $p_password_new_repeated = $app->request->post('new-password-repeated');

        if (!isset($g_reset_hash) || trim($g_reset_hash) == FALSE ||
            !isset($g_user) || trim($g_user) == FALSE) {
            $app->flash('errors', "Neispravan URL sa zahtjevom za reaktivacijom lozinke!");
            $app->redirect($app->urlFor('user.login'));
        }
        else {
            if(!$pass_reset = User::getPassResetByUserHash($g_user)) {
                $app->flash('errors', "Neispravan URL sa zahtjevom za reaktivacijom lozinke!:user");
                $app->redirect($app->urlFor('user.login'));
            } else if(!$user = User::getUserById($pass_reset->user_id)) {
                $app->flash('errors', "Nevažeći zahtjev za reaktivacijom lozinke!");
                $app->redirect($app->urlFor('user.login'));
            } else if(!$user->activePassResetRequestExists()) {
                $app->flash('errors', "Ne postoji aktivan zahtjev za reaktivacijom lozinke!");
                $app->redirect($app->urlFor('user.login'));
            } else if (!Hash::hashCheck($pass_reset->reset_hash, Hash::hash($g_reset_hash))){
                $app->flash('errors', "Neispravan URL sa zahtjevom za reaktivacijom lozinke!:hash");
                $app->redirect($app->urlFor('user.login'));
            } else if(!isset($p_email) || $p_email === "" ) {
                $app->flash('errors', "Nedostaju podaci za proces povratka lozinke! \n" .
                    "Unos e-mail adrese obavezan je za prijavu.");
                $app->render('authentication/user.password_reset.twig', array(
                    'security' => $g_user,
                    'hash' => $g_reset_hash
                ));
            } else if(!isset($p_password_new) || $p_password_new === "" ||
                !isset($p_password_new_repeated) || $p_password_new_repeated === "") {
                $app->flashNow('errors',  "Nedostaju podaci za reaktivaciju lozinke! \n" .
                    "Unos lozinke i njena potvrda su obavezni.");
                $app->render('authentication/user.password_reset.twig', array(
                    'security' => $g_user,
                    'hash' => $g_reset_hash
                ));
            } else if($p_password_new != $p_password_new_repeated) {
                $app->flashNow('errors',  "Nova lozinka i njena potvrda se ne poklapaju");
                $app->render('authentication/user.password_reset.twig', array(
                    'security' => $g_user,
                    'hash' => $g_reset_hash
                ));
            } else if($p_email != $user->email) {
                $app->flashNow('errors',  "Unesena e-mail adresa $p_email ne odgovara onoj " .
                    "za koju je poslan zahtjev za reaktivaciju lozinke!");
                $app->render('authentication/user.password_reset.twig', array(
                    'security' => $g_user,
                    'hash' => $g_reset_hash
                ));
            } else {
                $user->deletePasswordResetRequest();
                $user->updatePassword(md5($p_password_new . $user->getPasswordSalt()));
                $app->flash('success', "Udonis U did it!");

                $app->redirect($app->urlFor('user.login'));
            }
        }
    })->name('user.password_reset.post');


});