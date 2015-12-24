<?php
use \app\model\User\User;
use \app\model\User\Player;
use \app\helpers\Sessions;
use \app\helpers\Configuration as Cfg;
use \app\helpers\Hash;

use \app\model\Reservation\TrainingCourseConstants;
use \app\model\Reservation\DatetimeSpan;
use \app\model\Reservation\Reservation;
use \app\model\Reservation\Prereservation;
use \app\model\Reservation\Booking;

use \app\helpers\Calendar;

use \app\model\Messages\Logger;

use \app\model\Match\Game;


$app->group('/clanovi', function () use ($app, $authenticated_user) {

    $app->group('/statistike', function() use ($app, $authenticated_user){


        $app->get('/t1', $authenticated_user(), function() use ($app) {
//            $games_for_user = Game::getGamesByPlayer($app->auth_user->id);
//            $app->render('user/user.games.home.twig', array(
//                'user' => $app->auth_user,
//                'games' => $games_for_user,
//                'active_page' => "user.main",
//                'active_item' => "user.main.games"
//            ));


        })->name('user.stats.games.home');
    });

});
