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



    $app->get('/nasumicno-biranje-ekipa-simulacija', $authenticated_user(), function() use ($app) {

        $app->render('user/random_sort_players_sim.twig', array(
            'user' => $app->auth_user,
//            'my_str' => $my_str,
            'users' => User::getUsers()
        ));


    })->name('user.randomize-selection-simulation.home');

    $app->post("/nasumicno-biranje-ekipa", $authenticated_user(), function() use ($app) {
        if($app->request->isAjax()) {

            try {
                $body = $app->request->getBody();
                $json_data_received = json_decode($body, true);
//                echo "Evo me dolazim"; exit();
                $user_ids = $json_data_received['selected-users'];


                $validation_result = User::validateRandomUsersSplit($user_ids);
                if(!($validation_result["validated"])) {
                    // valudation failed
                    if(isset($validation_result["errors"])) {
                        $app->flashNow('errors',  $validation_result["errors"]);
                        $app->render('templates/partials/user_messages.twig', array());
                    }
                } else {
                    $users = $validation_result["users"];
                    $random_selection = User::splitUsersRandom($users);

                    $sorted_users = $random_selection["sorted_users"];
                    $team_ids = $random_selection["teams_ids"];
                    $teams = $random_selection["teams"];

                    $app->render('user/stats/sorted_rankings/by_pct_of_points_random_selection.twig', array(
                        'users' => $sorted_users,
                        'teams_ids' => $team_ids,
                        'teams' => $teams
                    ));
                }

                exit();

            } catch (Exception $e) {
                echo json_encode(array(
                    "error" => "Greska:" . $e->getMessage()
                ));
            }
            exit();
        }

    })->name('user.randomize-selection-simulation.post');


});
?>
