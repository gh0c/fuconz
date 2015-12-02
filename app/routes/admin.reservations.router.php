<?php
use \app\model\User\User;
use \app\model\Reservation\TrainingCourse;

use \app\model\Reservation\Booking;
use \app\model\Reservation\DatetimeSpan;
use \app\model\Reservation\TrainingCourseConstants;
use \app\helpers\Calendar;

use \app\model\Messages\ActionLog;

$app->group('/admin/rezervacije', function () use ($app, $authenticated_admin) {

    $app->get('/', function () use ($app) {
        $app->redirect($app->urlFor('admin.reservations.home'));
    })->name('admin.reservations');


    $app->get('/pocetna', $authenticated_admin(), function () use ($app) {

        $calendar = new Calendar();
        $course_constants = new TrainingCourseConstants();
        $course_constants::set_default_values();
        $datetime_span = new DatetimeSpan();

        $app->render('admin/reservations/admin.reservations.home.twig', array(
            'auth_admin' => $app->auth_admin,
            'active_page' => "reservations",
            'active_item' => "reservations.home",
            'datetimes' => $datetime_span,
            'course_constants' => $course_constants,
            'calendar' => $calendar,
            'calendar_class' => 'reservations-home',
            'wrapper' => 'calendar/months.standard.twig',
            'day_selector' => 'admin/reservations/calendar/day.selector.reservations.view.twig',
            'url_change_view' => $app->urlFor('admin.reservations.home.change-month.post')
        ));

    })->name('admin.reservations.home');



    $app->post('/pocetna/promjeni-mjesec-kalendara', $authenticated_admin(), function() use ($app) {
        if($app->request->isAjax()) {
            $body = $app->request->getBody();
            $json_data_received = json_decode($body, true);

            $p_selected_date = $json_data_received['date'];

            $calendar = new Calendar();
            $course_constants = new TrainingCourseConstants();
            $course_constants::set_custom_values($p_selected_date);
            $datetime_span = new DatetimeSpan();

            $app->render('calendar/months.standard.twig', array(
                'auth_admin' => $app->auth_admin,
                'active_page' => "reservations",
                'active_item' => "reservations.home",
                'datetimes' => $datetime_span,
                'course_constants' => $course_constants,
                'calendar' => $calendar,
                'calendar_class' => 'reservations-home',
                'wrapper' => 'calendar/months.standard.twig',
                'day_selector' => 'admin/reservations/calendar/day.selector.reservations.view.twig',
                'url_change_view' => $app->urlFor('admin.reservations.home.change-month.post')
            ));
        }

    })->name('admin.reservations.home.change-month.post');



    $app->get('/rezervacijski-termini', $authenticated_admin(), function () use ($app) {
        $all_courses = TrainingCourse::getCourses();

        $app->render('admin/reservations/admin.reservations.training_courses.all.twig', array(
            'training_courses' => $all_courses,
            'auth_admin' => $app->auth_admin,
            'active_page' => "reservations",
            'active_item' => "reservations.training-courses"));
    })->name('admin.reservations.training-courses.all');


    $app->get('/rezervacijski-termini/novi', $authenticated_admin(), function () use ($app) {
        $all_courses = TrainingCourse::getCourses();

        $app->render('admin/reservations/admin.reservations.training_course.twig', array(
            'training_courses' => $all_courses,
            'auth_admin' => $app->auth_admin,
            'active_page' => "reservations",
            'active_item' => "reservations.training-courses"));
    })->name('admin.reservations.training_course.new');


    $app->post('/rezervacijski-termini/novi', $authenticated_admin(), function () use ($app) {
        $all_courses = null; //User::getUsers();

        $p_title = $app->request->post('title');
        $p_date_from = $app->request->post('date-from');
        $p_date_until = $app->request->post('date-until');
        $p_start_time = $app->request->post('start-time');
        $p_end_time = $app->request->post('end-time');
        $p_repeating = $app->request->post('repeating');
        $p_repeating_interval = $app->request->post('repeating-interval');
        $p_repeating_frequency = $app->request->post('repeating-frequency');
        $p_capacity = $app->request->post('capacity');
        $p_min_reservations = $app->request->post('min-reservations');
        $p_reservation_time = $app->request->post('reservation-time') * 60 * 60;

        $validation_result = TrainingCourse::validateNew($p_title, $p_start_time, $p_end_time,
            $p_date_from, 1, $p_capacity, $p_min_reservations, $p_repeating, $p_repeating_interval,
            $p_repeating_frequency, $p_date_until, $p_reservation_time );

        if(!($validation_result["validated"])) {
            // validation failed
            $app->flash('admin_errors', "Greške!\n" . $validation_result["errors"]);

            if(isset($validation_result["errors"])) {
                $app->flash('admin_errors', "Greške!\n" . $validation_result["errors"]);
            }
            $app->redirect($app->urlFor('admin.reservations.training_course.new'));
        } else {
            // Validation of input data successful
            $status = TrainingCourse::createNew($p_title, $p_start_time, $p_end_time, date("Y-m-d", strtotime($p_date_from) ),
                1, $app->auth_admin->id, (int)$p_capacity, (int)$p_min_reservations, (isset($p_repeating) && $p_repeating === "on"),
                $p_repeating_interval, (int)$p_repeating_frequency, date("Y-m-d", strtotime($p_date_until) ), (int)$p_reservation_time);

            if ($status["success"]) {
                $app->flash('admin_success', "Uspješan unos!");
                $app->redirect($app->urlFor('admin.reservations.training-courses.all'));
            } else {
                $app->flash('admin_errors', "Greška kod unosa u bazu.\n" . $status["err"] . "\nPokušajte ponovno");
                $app->redirect($app->urlFor('admin.reservations.training_course.new'));
            }
        }

    })->name('admin.reservations.training_course.new.post');


    $app->get('/izbrisi/:course_id', $authenticated_admin(), function ($course_id) use ($app) {
        if(!($course = TrainingCourse::getCourseById((int)$course_id))) {
            $app->flash('admin_errors',  "Ne postoji traženi rezervacijski termin.");
            $app->redirect($app->urlFor('admin.reservations.training-courses.all'));
        } else {
            if($course->delete()) {
                $app->flash('admin_success', "Uspješno brisanje rezervacijskog termina: {$course->title}");
                $app->redirect($app->urlFor('admin.reservations.training-courses.all'));
            } else {
                $app->flash('admin_errors',  "Rezervacijski termin nije uspješno izbrisan. Pokušajte ponovo.");
                $app->redirect($app->urlFor('admin.reservations.training-courses.all'));
            }
        }
    })->name('admin.reservations.delete.training_course');


    $app->get('/rezervacije-clanova', $authenticated_admin(), function () use ($app) {
        $all_users = User::getUsers();

        $app->render('admin/reservations/admin.reservations.user_reservations.all.twig', array(
            'users' => $all_users,
            'auth_admin' => $app->auth_admin,
            'active_page' => "reservations",
            'active_item' => "reservations.user-reservations"));
    })->name('admin.reservations.user-reservations.all');


    $app->get('/detalji-rezervacijskog-termina/:span_id', $authenticated_admin(), function ($span_id) use ($app) {
        $datetime_span = DatetimeSpan::getById($span_id);
        if (!$datetime_span) {
            $app->flash('admin_errors',  "Ne postoji traženi termin.");
            $app->redirect($app->urlFor('admin.reservations.home'));
        } else {
            $bookings = Booking::getByDatetimeSpan($span_id);
            $logs = ActionLog::getActionLogsForDatetimeSpan($datetime_span->id);
//        var_dump($bookings);
//        foreach($bookings as $booking) {
//            var_dump($booking->user->username);
//            var_dump($booking->created_at);
//            if($booking->type == "reservation") {
//                var_dump("Rezervacija: poništena:");
//                var_dump($booking->canceled);
//            } else {
//                var_dump("Predbiljezba: aktivirana:");
//                var_dump($booking->activated);
//            }
//        }
//        echo "</pre>";
//
            $app->render('admin/reservations/admin.reservations.span.details.twig', array(
                'auth_admin' => $app->auth_admin,
                'active_page' => "reservations",
                'active_item' => "reservations.user-reservations",
                'bookings' => $bookings,
                'span' => $datetime_span,
                'logs' => $logs,
                'text_help' => new \app\helpers\Text()
            ));
        }

    })->name('admin.reservations.span.details');


});
?>
