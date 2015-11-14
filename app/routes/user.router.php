<?php
use \app\model\User\User;
use app\helpers\Sessions;
use app\helpers\Configuration as Cfg;
use app\model\Content\ImagesHandler;
use app\helpers\Hash;

$user_model = new User();

$app->group('/clanovi', function () use ($app, $user_model, $authenticated_user) {

    $app->get('/', function () use ($app, $user_model) {
        $app->redirect($app->urlFor('user.profile.home'));

    })->name('registered-users.home');


    $app->get('/pocetna', $authenticated_user(), function () use ($app) {

        $app->redirect($app->urlFor('user.profile.home'));
    })->name('user.home');




});
?>
