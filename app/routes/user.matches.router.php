<?php
use \app\model\User\User;
use \app\model\User\Player;
use \app\helpers\Sessions;
use \app\helpers\Configuration as Cfg;
use \app\model\Content\ImagesHandler;
use \app\helpers\Hash;

use \app\model\Reservation\TrainingCourseConstants;
use \app\model\Reservation\DatetimeSpan;
use \app\model\Reservation\Reservation;
use \app\model\Reservation\Prereservation;
use \app\model\Reservation\Booking;

use \app\helpers\Calendar;

use \app\model\Messages\Logger;




$app->get('/game', function() use ($app) {

    $app->render('game.test.twig', array(
        'user' => $app->auth_user,
        'game_id' => 2
    ));
})->name('game.test');


$app->get('/game-cool', function() use ($app) {

    $U01 = new Player(array("use_fb_avatar" => 1, "fb_id" => "1119556452", "username" => "Gan", "id", "999"));
    $U02 = new Player(array( "username" => "Ivo", "id", "999"));
    $U03 = new Player(array("use_fb_avatar" => 1, "fb_id" => "100002493183362", "username" => "Doms", "id", "999"));
    $U04 = new Player(array("use_fb_avatar" => 1, "fb_id" => "1113270916", "username" => "Šoc", "id", "999"));
    $U05 = new Player(array("use_fb_avatar" => 1, "fb_id" => "1518848921", "username" => "Geconz", "id", "999"));

    $U06 = new Player(array("use_fb_avatar" => 1, "fb_id" => "100003852825258", "username" => "Gogo", "id", "999"));
    $U07 = new Player(array("use_fb_avatar" => 1, "fb_id" => "1080084243", "username" => "Pixi", "id", "999"));

    $U08 = new Player(array("username" => "Pixi5", "id", "999"));
    $U09 = new Player(array("username" => "Neno", "id", "999"));

    $U11 = new Player(array("use_fb_avatar" => 1, "fb_id" => "1059559471", "username" => "Pero", "id", "999"));


    $app->render('game.test.2.twig', array(
        'user' => $app->auth_user,
        'game_id' => 2,
        "number_of_players_in_a_team" => 5,
        "teams" => array(
            "teamOne" => array($U03, $U09, $U04, $U02, $U01),
            "teamTwo" => array( $U08, $U11, $U05, $U06, $U07)

        )
    ));
})->name('game.cool.test');

