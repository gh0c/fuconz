<?php
use \app\model\User\User;
use app\helpers\Configuration as Cfg;
use \app\model\Reservation\Booking;

$user_model = new User();

$app->group('/clanovi', function () use ($app, $user_model, $authenticated_user) {

    $app->get('/', function () use ($app, $user_model) {
        $app->redirect($app->urlFor('user.profile.home'));

    })->name('registered-users.home');


    $app->get('/pocetna', $authenticated_user(), function () use ($app) {

        $app->redirect($app->urlFor('user.profile.home'));
    })->name('user.home');


    $app->get('/rezervacije', $authenticated_user(), function() use ($app) {
        list($b_allowed, $b_not_allowed, $b_ended) = Booking::getByUserSorted($app->auth_user->id);
        $app->render('user/user.bookings.home.twig', array(
            'user' => $app->auth_user,
            'active_page' => "user.main",
            'active_item' => "user.main.bookings",
            'bookings_exists' => Booking::existsForUser($app->auth_user->id),
            'bookings' => Booking::getByUser($app->auth_user->id),
            'bookings_allowed' => $b_allowed,
            'bookings_not_allowed' => $b_not_allowed,
            'bookings_ended' => $b_ended
        ));
    })->name('user.bookings.home');



});
?>
