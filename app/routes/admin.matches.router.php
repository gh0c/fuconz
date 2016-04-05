<?php
use \app\model\User\User;
use \app\model\Match\Game;
use app\helpers\Configuration as Cfg;
use app\model\Admin\Admin;
use \app\model\Reservation\DatetimeSpan;
use \app\model\Reservation\TrainingCourseConstants;
use \app\helpers\Calendar;

$app->group('/admin/susreti', function () use ($app, $authenticated_admin) {

    $app->get('/', $authenticated_admin(), function () use ($app) {
        $app->render('admin/matches/admin.matches.home.twig', array(
            'auth_admin' => $app->auth_admin,
            'active_page' => "matches",
            'active_item' => "matches.home"));
    })->name('admin.matches.home');


    $app->get('/utakmice', $authenticated_admin(), function () use ($app) {
        $all_games = Game::getGames();

        $app->render('admin/matches/admin.matches.games.all.twig', array(
            'games' => $all_games,
            'auth_admin' => $app->auth_admin,
            'active_page' => "matches",
            'active_item' => "matches.games"));
    })->name('admin.matches.games.all');


    $app->get('/utakmice/dodavanje', $authenticated_admin(), function () use ($app) {
        $all_games = Game::getGames();

        $calendar = new Calendar();
        $course_constants = new TrainingCourseConstants();
        $course_constants::set_default_values();
        $datetime_span = new DatetimeSpan();
        $game = new Game();

        $app->render('admin/matches/admin.matches.games.add.twig', array(
            'games' => $all_games,
            'auth_admin' => $app->auth_admin,
            'active_page' => "matches",
            'active_item' => "matches.games.add",
            'datetimes' => $datetime_span,
            'game' => $game,
            'course_constants' => $course_constants,
            'calendar' => $calendar,
            'calendar_class' => 'adding-games',
            'wrapper' => 'calendar/months.standard.twig',
            'day_selector' => 'admin/matches/calendar/day.selector.games.add.twig',
            'url_change_view' => $app->urlFor('admin.matches.games.add.change-month.post')
        ));
    })->name('admin.matches.games.add');


    $app->post('/utakmice/dodavanje/promjeni-mjesec-kalendara', $authenticated_admin(), function() use ($app) {
        if($app->request->isAjax()) {
            $body = $app->request->getBody();
            $json_data_received = json_decode($body, true);

            $p_selected_date = $json_data_received['date'];

            $all_games = Game::getGames();

            $calendar = new Calendar();
            $course_constants = new TrainingCourseConstants();
            $course_constants::set_custom_values($p_selected_date);
            $datetime_span = new DatetimeSpan();
            $game = new Game();

            $app->render('calendar/months.standard.twig', array(
                'games' => $all_games,
                'auth_admin' => $app->auth_admin,
                'active_page' => "matches",
                'active_item' => "matches.games.add",
                'datetimes' => $datetime_span,
                'game' => $game,
                'course_constants' => $course_constants,
                'calendar' => $calendar,
                'calendar_class' => 'adding-games',
                'wrapper' => 'calendar/months.standard.twig',
                'day_selector' => 'admin/matches/calendar/day.selector.games.add.twig',
                'url_change_view' => $app->urlFor('admin.matches.games.add.change-month.post')
            ));
        }

    })->name('admin.matches.games.add.change-month.post');


    $app->get('/utakmice/nova/:span_id', $authenticated_admin(), function ($span_id) use ($app) {
//        $all_courses = TrainingCourse::getCourses();
        $datetime_span = DatetimeSpan::getById($span_id);
        if (!$datetime_span) {
            echo "nema termina!";
        } else {
            $app->render('admin/matches/admin.matches.game.twig', array(
                'auth_admin' => $app->auth_admin,
                'active_page' => "matches",
                'active_item' => "matches.games.add",
                'datetime_span_id' => $span_id,
                'datetime_span_label' => $datetime_span->descriptionString(),
                'users' => User::getUsers()
            ));
        }

    })->name('admin.matches.game.new');


    $app->post('/utakmice/nova', $authenticated_admin(), function () use ($app) {
        $p_title = $app->request->post('title');

        $p_field = $app->request->post('field');
        $p_res_team_one = (int)$app->request->post('res-team-one');
        $p_res_team_two = (int)$app->request->post('res-team-two');
        $p_winner = (int)$app->request->post('winner-selection');
        $p_datetime_span_id = (int)$app->request->post('span-id');

        $p_players_team_one = $app->request->post('players-team-one');
        $p_players_team_two = $app->request->post('players-team-two');

        $p_after_et = $app->request->post('after-et');


        $validation_result = Game::validateNew($p_title, $p_datetime_span_id, $p_winner, $p_res_team_one, $p_res_team_two,
            $p_players_team_one, $p_players_team_two, $p_field);

        if(!($validation_result["validated"])) {
            // valudation failed
            if(isset($validation_result["errors"])) {
                $app->flash('admin_errors',  $validation_result["errors"]);
            }
            if(isset($p_datetime_span_id)) {
                $app->redirect($app->urlFor('admin.matches.game.new', array("span_id" => $p_datetime_span_id)));
            } else {
                $app->redirect($app->urlFor('admin.matches.games.all'));
            }
        } else {
            // Validation of input data successful
            $players = array("team-one" => $p_players_team_one, "team-two" => $p_players_team_two);
            $status = Game::createNew($p_title, $p_res_team_one, $p_res_team_two, $p_winner, $p_datetime_span_id,
                $players, $p_field, (isset($p_after_et) && $p_after_et === "on"));
            if ($status["success"] == true) {
//                Logger::logNewUserRegistration($new_user);
                $app->flash('admin_success', "Uspješan unos utakmice!");
                $app->redirect($app->urlFor('admin.matches.games.all'));
            } else {
                if(isset($status["err"])) {
                    $app->flash('admin_errors', "Greška kod unosa u bazu.\n" . $status["err"] . "\nPokušajte ponovno");
                } else {
                    $app->flash('admin_statuses', "Utakmica je krnje unesena, deseila se neka greška...");
                }
                if(isset($p_datetime_span_id)) {
                    $app->redirect($app->urlFor('admin.matches.game.new', array("span_id" => $p_datetime_span_id)));
                } else {
                    $app->redirect($app->urlFor('admin.matches.games.all'));
                }
            }
        }

    })->name('admin.matches.game.new.post');




    $app->get('/utakmice/izbrisi/:game_id', $authenticated_admin(), function ($game_id) use ($app) {
        if(!($game = Game::getById((int)$game_id))) {
            $app->flash('admin_errors',  "Ne postoji tražena utakmica.");
            $app->redirect($app->urlFor('admin.matches.games.all'));
        } else {
            if($game->delete()) {
                $app->flash('admin_success', "Uspješno brisanje utakmice: {$game->title}");
                $app->redirect($app->urlFor('admin.matches.games.all'));
            } else {
                $app->flash('admin_errors',  "Utakmica nije uspješno izbrisana. Pokušajte ponovo.");
                $app->redirect($app->urlFor('admin.matches.games.all'));
            }
        }
    })->name('admin.matches.delete.game');


    $app->get('/utakmice/uredi/:game_id', $authenticated_admin(), function ($game_id) use ($app) {
        $game = Game::getById($game_id);
        if(!$game) {
            $app->flash('admin_errors',  "Utakmica sa identifikatorom {$game_id} nije pronađena");
            $app->redirect($app->urlFor('admin.matches.games.all'));
        } else {
            $datetime_span = DatetimeSpan::getById($game->datetime_span_id);
            $app->render('admin/matches/admin.matches.game.edit.twig', array(
                'auth_admin' => $app->auth_admin,
                'active_page' => "matches",
                'active_item' => "matches.games",
                'game' => $game,
                'datetime_span_id' => $datetime_span->id,
                'datetime_span_label' => $datetime_span->descriptionString(),
                'users' => User::getUsers()
            ));
        }

    })->name('admin.matches.game.edit');


    $app->post('/utakmice/uredi', $authenticated_admin(), function () use ($app) {

        $p_id = $app->request->post('id');
        if(!isset($p_id) || $p_id === "" || !$game = Game::getById($p_id)) {
            $app->flash('admin_errors',  "Utakmica sa identifikatorom {$p_id} nije pronađena");
            $app->redirect($app->urlFor('admin.matches.games.all'));
        } else {
            $p_title = $app->request->post('title');

            $p_field = $app->request->post('field');
            $p_res_team_one = (int)$app->request->post('res-team-one');
            $p_res_team_two = (int)$app->request->post('res-team-two');
            $p_winner = (int)$app->request->post('winner-selection');
            $p_datetime_span_id = (int)$app->request->post('span-id');

            $p_players_team_one = $app->request->post('players-team-one');
            $p_players_team_two = $app->request->post('players-team-two');

            $p_after_et = $app->request->post('after-et');


            $validation_result = Game::validateEdit($p_title, $p_datetime_span_id, $p_winner, $p_res_team_one, $p_res_team_two,
                $p_players_team_one, $p_players_team_two, $p_field);
            var_dump($validation_result);

            if(!($validation_result["validated"])) {
                // valudation failed
                if(isset($validation_result["errors"])) {
                    $app->flash('admin_errors',  $validation_result["errors"]);
                }
                $app->redirect($app->urlFor('admin.matches.game.edit', array("game_id" => $p_id)));
            } else {
                // Validation of input data successful
                $players = array("team-one" => $p_players_team_one, "team-two" => $p_players_team_two);
                $status = $game->edit($p_title, $p_res_team_one, $p_res_team_two, $p_winner, $p_datetime_span_id,
                    $players, $p_field, (isset($p_after_et) && $p_after_et === "on"));
                if ($status["success"] == true) {
//                Logger::logNewUserRegistration($new_user);
                    $app->flash('admin_success', "Uspješne izmjene utakmice!");
                    $app->redirect($app->urlFor('admin.matches.games.all'));
                } else {
                    if(isset($status["err"])) {
                        $app->flash('admin_errors', "Greška kod unosa u bazu.\n" . $status["err"] . "\nPokušajte ponovno");
                    } else {
                        $app->flash('admin_statuses', "Utakmica je krnje unesena, deseila se neka greška...");
                    }
                    $app->redirect($app->urlFor('admin.matches.game.edit', array("game_id" => $p_id)));

                }
            }
        }


    })->name('admin.matches.game.edit.post');

//
//
//    $app->get('/rezervacije-clanova', $authenticated_admin(), function () use ($app) {
//        $all_users = User::getUsers();
//
//        $app->render('admin/reservations/admin.reservations.user_reservations.all.twig', array(
//            'users' => $all_users,
//            'auth_admin' => $app->auth_admin,
//            'active_page' => "reservations",
//            'active_item' => "reservations.user-reservations"));
//    })->name('admin.reservations.user-reservations.all');

});
?>
