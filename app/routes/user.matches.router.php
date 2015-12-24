<?php

use \app\model\Match\Game;


$app->group('/clanovi', function () use ($app, $authenticated_user) {

    $app->group('/susreti', function() use ($app, $authenticated_user){


        $app->get('/utakmice', $authenticated_user(), function() use ($app) {
            $games_for_user = Game::getGamesByPlayer($app->auth_user->id);
            $app->render('user/user.games.home.twig', array(
                'user' => $app->auth_user,
                'games' => $games_for_user,
                'active_page' => "user.main",
                'active_item' => "user.main.games"
            ));

        })->name('user.matches.games.home');

        $app->get('/utakmica/:game_id', $authenticated_user(), function($game_id) use ($app) {
            $game = Game::getById($game_id);
            if($game) {
                $app->render('user/matches/game.twig', array(
                    'user' => $app->auth_user,
                    'active_page' => "user.main",
                    'active_item' => "user.main.games",
                    'game' => $game,
                    'game_id' => $game->id,
                    "teams" => array(
                        "teamOne" => $game->players_team_one,
                        "teamTwo" => $game->players_team_two
                    )
                ));
            }

        })->name('user.matches.game');
    });
});

