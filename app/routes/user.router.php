<?php
use \app\model\User\RegisteredUser;
use app\helpers\Sessions;
use app\helpers\Configuration as Cfg;
use app\model\Content\ImagesHandler;
use app\helpers\Hash;

$user_model = new RegisteredUser();

$app->group('/clanovi', function () use ($app, $user_model, $authenticated_user) {

    $app->get('/', function () use ($app, $user_model) {
        $app->redirect($app->urlFor('user.profile.home'));

    })->name('registered-users.home');


    $app->get('/pocetna', $authenticated_user(), function () use ($app) {

        $app->render('user/user.home.twig', array(
            'user' => $app->auth_user));
    })->name('user.home');


    $app->get('/rezervacija-termina', $authenticated_user(), function () use ($app) {

        $app->render('user/bookings/user.book_training_course.twig', array(
            'user' => $app->auth_user,
            'active_page' => 'user.reservations'));
    })->name('user.book-training-course');


    $app->post('/rezervacija-termina', $authenticated_user(), function () use ($app) {

        echo "<br>Submit termina";
        echo "<br><br>";
        var_dump($_POST);
    })->name('user.book-training-course.post');


});
?>
