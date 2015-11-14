<?php
use \app\model\User\User;
use app\helpers\Sessions;
use app\helpers\Configuration as Cfg;
use app\model\Content\ImagesHandler;
use app\helpers\Hash;
use app\model\Admin\Admin;



$app->group('/admin', function () use ($app, $authenticated_admin) {

    $app->get('/', function () use ($app) {
        $app->redirect($app->urlFor('admin.home'));

    })->name('administrator.home');


    $app->get('/pocetna', $authenticated_admin(), function () use ($app) {

        $app->render('admin/admin.home.twig', array(
            'auth_admin' => $app->auth_admin,
            'active_page' => "admin",
            'active_item' => "admin.pocetna"));
    })->name('admin.home');


});
?>
