<?php
use \app\model\User\RegisteredUser;
use app\helpers\Sessions;
use app\helpers\Configuration as Cfg;
use app\model\Content\ImagesHandler;
use app\helpers\Hash;
use app\model\Admin\Admin;



$app->group('/admin/clanovi', function () use ($app, $authenticated_admin) {

    $app->get('/', function () use ($app) {
        $app->redirect($app->urlFor('admin.users.home'));
    })->name('admin.users');


    $app->get('/pocetna', $authenticated_admin(), function () use ($app) {
        $app->render('admin/users/admin.users.home.twig', array(
            'auth_admin' => $app->auth_admin,
            'active_page' => "users",
            'active_item' => "users.home"));
    })->name('admin.users.home');


    $app->get('/svi-clanovi', $authenticated_admin(), function () use ($app) {
        $all_users = RegisteredUser::getUsers();

        $app->render('admin/users/admin.users.all.twig', array(
            'users' => $all_users,
            'auth_admin' => $app->auth_admin,
            'active_page' => "users",
            'active_item' => "users.all"));
    })->name('admin.users.all');


    $app->get('/aktivni-clanovi', $authenticated_admin(), function () use ($app) {
        $all_users = RegisteredUser::getActiveUsers();

        $app->render('admin/users/admin.users.all.active.twig', array(
            'users' => $all_users,
            'auth_admin' => $app->auth_admin,
            'active_page' => "users",
            'active_item' => "users.all.active"));
    })->name('admin.users.all.active');


    $app->get('/clan/:user_id', $authenticated_admin(), function ($user_id) use ($app) {
        var_dump((int)$user_id);
        exit();
    })->name('admin.users.user.home');

    $app->get('/uredi/:user_id', $authenticated_admin(), function ($user_id) use ($app) {
        var_dump((int)$user_id);
        exit();
    })->name('admin.users.edit.user');

    $app->get('/aktivnost/:user_id', $authenticated_admin(), function ($user_id) use ($app) {
        var_dump((int)$user_id);
        exit();
    })->name('admin.users.user.activity');

    $app->get('/izbrisi/:user_id', $authenticated_admin(), function ($user_id) use ($app) {
        if(!($user = RegisteredUser::getUserById((int)$user_id))) {
            $app->flash('errors',  "Ne postoji traženi član za brisanje.");
            $app->redirect($app->urlFor('admin.users.home'));
        } else {
            if($user->delete()) {
                $app->flash('success', "Uspješno brisanje člana: {$user->username}");
                $app->redirect($app->urlFor('admin.users.home'));
            } else {
                $app->flash('errors',  "Član nije uspješno izbrisan. Pokušajte ponovo.");
                $app->redirect($app->urlFor('admin.users.home'));
            }
        }
    })->name('admin.users.delete.user');

});
?>
