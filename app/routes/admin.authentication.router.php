<?php

use app\helpers\Sessions;
use app\helpers\Configuration as Cfg;
use app\model\User\User;
use app\helpers\Auth;
use app\helpers\Mailer;
use app\helpers\Hash;
use app\model\Admin\Admin;

$app->get('/admin/prijava', $guest_admin(), function () use ($app) {
    $app->render('admin/authentication/admin_login.twig', array(
        ));
})->name('admin.login');


$app->get('/admin/odjava', function () use ($app) {
    Auth::doAdminLogout();

    $app->flash('admin_success', "Uspješno ste odjavljeni kao admin!");
    $app->redirect($app->urlFor('home'));
})->name('admin.logout');



$app->post('/admin/prijava', $guest_admin(), function () use ($app) {
    $p_username = $app->request->post('username');
    $p_password = $app->request->post('password');
    $p_remember_me = $app->request->post('remember-me');

    if(!isset($p_username) || $p_username === "" || !isset($p_password) || $p_password === "")
    {
        $app->flash('admin_errors',  "Nedostaju podaci za prijavu! \n" .
            "Unos korisničkog imena i lozinke su obavezni za prijavu.");
        $app->redirect($app->urlFor('admin.login'));
    }

    $admin = Admin::getAdminByUsername($p_username);
    if (!$admin) {
        $app->flash('admin_errors', "Neispravo korisničko ime");
        $app->redirect($app->urlFor('admin.login'));
    }
    else {

        $success = Hash::passwordCheck($p_password . $admin->getPasswordSalt(), $admin->getPassword());

        if($success) {

            $admin = Auth::doAdminLogin($admin);
            $app->flash('admin_success', "Uspješna prijava administratora!");

            if (isset($p_remember_me) && $p_remember_me === "on") {
                Auth::setAdminRememberMe($admin);
                $app->flash('admin_statuses', "Koristeći ovaj preglednik, sustav će Vas zapamtiti.");
            }
            $app->redirect($app->urlFor('admin.home'));

        } else {
            $app->flash('admin_errors', "Neispravo korisničko ime i/ili lozinka.");
            $app->redirect($app->urlFor('admin.login'));
        }
    }

})->name('admin.login.post');



