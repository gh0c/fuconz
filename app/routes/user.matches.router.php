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
    $user1 = Player::getPlayerById(1);
    $user2 = Player::getPlayerById(3);
    $user3 = Player::getPlayerById(16);
    $user4 = Player::getPlayerById(15);
    $user6 = Player::getPlayerById(5);
    $user5 = Player::getPlayerById(7);
    $user7 = Player::getPlayerById(4);
    $u8 = Player::getPlayerById(13);
    $u9 = Player::getPlayerById(12);
    $u10 = Player::getPlayerById(9);

    $U01 = new Player(array("use_fb_avatar" => 1, "fb_id" => "1119556452", "username" => "Gan", "id", "999"));
    $U02 = new Player(array("use_fb_avatar" => 1, "fb_id" => "1157876830", "username" => "Ivo", "id", "999"));
    $U03 = new Player(array("use_fb_avatar" => 1, "fb_id" => "100002493183362", "username" => "Doms", "id", "999"));
    $U04 = new Player(array("use_fb_avatar" => 1, "fb_id" => "1113270916", "username" => "Šoc", "id", "999"));
    $U05 = new Player(array("use_fb_avatar" => 1, "fb_id" => "1518848921", "username" => "Gec", "id", "999"));

    $U06 = new Player(array("use_fb_avatar" => 1, "fb_id" => "100003852825258", "username" => "Gogo", "id", "999"));
    $U07 = new Player(array("use_fb_avatar" => 1, "fb_id" => "1080084243", "username" => "Pixi", "id", "999"));

    $U08 = new Player(array("username" => "Pixi5", "id", "999"));
    $U09 = new Player(array("username" => "Neno", "id", "999"));
    $U10 = Player::getUserByUsername("Gan");

    $app->render('game.test.2.twig', array(
        'user' => $app->auth_user,
        'game_id' => 2,
        "number_of_players_in_a_team" => 5,
        "teams" => array(
            "teamOne" => array($U03, $U09, $U04, $U02, $U01),
            "teamTwo" => array( $U08, $U04, $U05, $U10, $user1)
        )
    ));
})->name('game.cool.test');

$app->post('/game-load', function() use ($app) {
    if($app->request->isAjax()) {
        $body = $app->request->getBody();
        $json_data_received = json_decode($body, true);


        $user1 = Player::getPlayerById(1);
        $users[] = $user1;

        $user2 = Player::getPlayerById(3);
        $users[] = $user2;

        $user3 = Player::getPlayerById(16);
        $users[] = $user3;

        $user4 = Player::getPlayerById(15);
        $users[] = $user4;
        $user6 = Player::getPlayerById(5);
        $users[] = $user6;

        $user5 = Player::getPlayerById(7);
        $users[] = $user5;

        $user7 = Player::getPlayerById(4);
        $users[] = $user7;
        $u8 = Player::getPlayerById(13);
        $u9 = Player::getPlayerById(12);
        $u10 = Player::getPlayerById(9);


        $app->response->headers->set('Content-Type', 'application/json;charset=utf-8');
//        echo json_encode(array("stuff" => "bla", "game" => 5)); exit();
        echo json_encode(array(
            "game" => array(
            "number_of_players_in_a_team" => 5,
            "team_one_players" => array($user1, $user2, $user3, $user5, $user6),
            "team_two_players" => array($user4, $user7, $u8, $u9, $u10))

        ));
        exit();


    }
})->name('game.test.load.post');


$app->get('/game-load', function() use ($app) {
    echo "Zip";
    echo "0";
        $user1 = Player::getPlayerById(1);
        $users[] = $user1;
    echo "1";

        $user2 = Player::getPlayerById(3);
        $users[] = $user2;
    echo "2";

        $user3 = Player::getPlayerById(16);
        $users[] = $user3;
    echo "3";

        $user4 = Player::getPlayerById(15);
        $users[] = $user4;    echo "4";

    $user6 = Player::getPlayerById(5);
        $users[] = $user6;    echo "5";


    $user5 = Player::getPlayerById(7);
        $users[] = $user5;    echo "6";


    $user7 = Player::getPlayerById(4);
        $users[] = $user7;    echo "7";

    $u8 = Player::getPlayerById(13);
    echo "8";

    $u9 = Player::getPlayerById(12);
    echo "9";

    $u10 = Player::getPlayerById(9);
    echo "10";

        echo "ja";

    var_dump($users);

//        header('Content-Type: application/json');
//        echo json_encode(array(
//            "game" => array(
//                "number_of_players_in_a_team" => 5,
//                "team_one_players" => array($user1, $user2, $user3, $user5, $user6),
//                "team_two_players" => array($user4, $user7, $u8, $u9, $u10))
//
//        ));
        exit();




})->name('game.test.load');
