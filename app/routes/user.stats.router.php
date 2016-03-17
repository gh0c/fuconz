<?php
use \app\model\User\User;



use \app\model\Match\Game;


$app->group('/clanovi', function () use ($app, $authenticated_user) {

    $app->group('/statistike', function() use ($app, $authenticated_user){


        $app->get('/pocetna', $authenticated_user(), function() use ($app) {
//            $games_for_user = Game::getGamesByPlayer($app->auth_user->id);
            $num_of_games = 5;
            $games_for_user = Game::getGamesByPlayer($app->auth_user->id, $num_of_games,
                "datetime_span.datetime_span_start DESC, id DESC");

            $app->render('user/stats/home.twig', array(
                'user' => $app->auth_user,
                'games' => $games_for_user,
                'num_of_games' => $num_of_games,
                'active_page' => "user.main",
                'active_item' => "user.main.stats"
            ));



        })->name('user.stats.home');

        $app->group('/sortiranje', function() use ($app, $authenticated_user){
            $app->post('/broj-utakmica', $authenticated_user(), function() use ($app) {
                if($app->request->isAjax()) {
                    $users = User::getUsersByNumberOfAppearances();

                    $app->render('user/stats/sorted_rankings/by_num_of_appearances.twig', array(
                        'users' => $users
                    ));
                    exit();
                }
            })->name('user.stats.sort.number-of-appearances.post');
        });
        $app->group('/sortiranje', function() use ($app, $authenticated_user){
            $app->post('/broj-pobjeda', $authenticated_user(), function() use ($app) {
                if($app->request->isAjax()) {
                    $users = User::getUsersByNumberOfWins();

                    $app->render('user/stats/sorted_rankings/by_num_of_wins.twig', array(
                        'users' => $users
                    ));
                    exit();
                }
            })->name('user.stats.sort.number-of-wins.post');
        });
        $app->group('/sortiranje', function() use ($app, $authenticated_user){
            $app->post('/broj-bodova', $authenticated_user(), function() use ($app) {
                if($app->request->isAjax()) {
                    $users = User::getUsersByNumberOfPoints();

                    $app->render('user/stats/sorted_rankings/by_num_of_points.twig', array(
                        'users' => $users
                    ));
                    exit();
                }
            })->name('user.stats.sort.number-of-points.post');
        });
        $app->group('/sortiranje', function() use ($app, $authenticated_user){
            $app->post('/postotak-bodova', $authenticated_user(), function() use ($app) {
                if($app->request->isAjax()) {
                    $users = User::getUsersByPercentageOfPoints();

                    $app->render('user/stats/sorted_rankings/by_pct_of_points.twig', array(
                        'users' => $users
                    ));
                    exit();
                }
            })->name('user.stats.sort.percentage-of-points.post');
        });
        $app->group('/sortiranje', function() use ($app, $authenticated_user){
            $app->post('/forma', $authenticated_user(), function() use ($app) {
                if($app->request->isAjax()) {
                    $num_of_games = 6;
                    $users = User::getUsersByNumberOfRecentPoints($num_of_games);
                    $games_for_users = array();
                    foreach($users as $user) {
                        $games_for_user = Game::getGamesByPlayer($user->id, $num_of_games,
                            "datetime_span.datetime_span_start DESC, id DESC");
                        $games_for_users[] = $games_for_user;
                    }

                    $app->render('user/stats/sorted_rankings/by_num_of_recent_points.twig', array(
                        'users' => $users,
                        'games_for_users' => $games_for_users,
                        'num_of_games' => $num_of_games
                    ));
                    exit();
                }
            })->name('user.stats.sort.number-of-recent-points.post');
        });
    });

});
