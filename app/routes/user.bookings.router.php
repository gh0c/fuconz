<?php
use \app\model\User\User;
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

$app->group('/clanovi', function () use ($app, $authenticated_user) {

    $app->group('/rezervacija-termina', function() use ($app, $authenticated_user){

        $app->post('/promjeni-mjesec-kalendara', $authenticated_user(), function() use ($app) {
            $p_selected_date = $app->request->post('date');
            $p_pre_selected_spans = $app->request->post('selected-spans');
            $pre_selected_spans = array();
            if(isset($p_pre_selected_spans)) {
                foreach($p_pre_selected_spans as $span){
                    $datetime = explode(" ", $span);
                    $pre_selected_spans[$datetime[0]][] = $datetime[1];
                }
            }

            $calendar = new Calendar();
            $course_constants = new TrainingCourseConstants();
            $course_constants::set_custom_values($p_selected_date);
            $datetime_span = new DatetimeSpan();
            $reservation = new Reservation();
            $prereservation = new Prereservation();
            $booking = new Booking();

            $app->render('user/bookings/calendar/months_navigation.twig', array(
                'user' => $app->auth_user,
                'active_page' => 'user.reservations',
                'course_constants' => $course_constants,
                'calendar' => $calendar,
                'datetimes' => $datetime_span,
                'reservation' => $reservation,
                'prereservation' => $prereservation,
                'booking' => $booking,
                'pre_selected_spans' => $pre_selected_spans
            ));

        })->name('user.booking.change-month.post');



        $app->post('/promjeni-datum-kalendara', $authenticated_user(), function() use ($app) {
            $p_selected_date = $app->request->post('date');
            $p_pre_selected_spans = $app->request->post('selected-spans');
            $pre_selected_spans = array();

            if(isset($p_pre_selected_spans)) {
                foreach($p_pre_selected_spans as $span){
                    $datetime = explode(" ", $span);
                    $pre_selected_spans[$datetime[0]][] = $datetime[1];
                }
            }

            $calendar = new Calendar();
            $course_constants = new TrainingCourseConstants();
            $course_constants::set_custom_values($p_selected_date);
            $datetime_span = new DatetimeSpan();
            $reservation = new Reservation();
            $prereservation = new Prereservation();
            $booking = new Booking();

            $app->render('user/bookings/calendar/months_navigation.twig', array(
                'user' => $app->auth_user,
                'active_page' => 'user.reservations',
                'course_constants' => $course_constants,
                'calendar' => $calendar,
                'reservation' => $reservation,
                'prereservation' => $prereservation,
                'booking' => $booking,
                'datetimes' => $datetime_span,
                'pre_selected_spans' => $pre_selected_spans
            ));

        })->name('user.booking.change-date-offset.post');



        $app->get('/', $authenticated_user(), function () use ($app) {
            $calendar = new Calendar();
            $course_constants = new TrainingCourseConstants();
            $course_constants::set_default_values();
            $datetime_span = new DatetimeSpan();
            $reservation = new Reservation();
            $prereservation = new Prereservation();
            $booking = new Booking();
            $app->render('user/bookings/user.book_training_course.twig', array(
                'user' => $app->auth_user,
                'active_page' => 'user.reservations',
                'course_constants' => $course_constants,
                'calendar' => $calendar,
                'datetimes' => $datetime_span,
                'reservation' => $reservation,
                'prereservation' => $prereservation,
                'booking' => $booking
            ));
        })->name('user.book-training-course');


        $app->post('/', $authenticated_user(), function () use ($app) {

            $p_selected_spans = $app->request->post('selected-spans');
            array_pop($p_selected_spans);
            $validation_result = Reservation::validateNewSetOfReservations($p_selected_spans, $app->auth_user);

            if(!($validation_result["validated"])) {
                // valudation failed
                if(isset($validation_result["errors"])) {
                    $app->flash('errors',  $validation_result["errors"]);
                }
                $app->redirect($app->urlFor('user.book-training-course'));
            } else {
                // Validation of input data successful

                $check_result = Reservation::checkAvailabilityBookings($p_selected_spans);
                if(!($check_result["validated"])) {
                    // valudation failed
                    if(isset($check_result["errors"])) {
                        $app->flash('errors',  $check_result["errors"]);
                    }
                    $app->redirect($app->urlFor('user.book-training-course'));
                } else {
                    if(!$check_result["prereservations"]) {
                        // no need for pre-reservations, just finally insert those reservations

                        if(Reservation::createNewSet($p_selected_spans, $app->auth_user)) {
                            $success_msg = Logger::logClassicBooking($app->auth_user, $p_selected_spans);

                            $app->flash("success", $success_msg);
                            $app->redirect($app->urlFor('user.book-training-course'));

                        } else {
                            $app->flash('errors', "Greška kod unosa u bazu.\nPokušajte ponovno");
                            $app->redirect($app->urlFor('user.book-training-course'));
                        }

                    } else {
                        $app->flash('errors', "Broj postojećih rezervacija za neke termine jednak je broju slobodnih mjesta:\n".
                            $check_result["prereservations_status_label"]);
                        $app->flash('available_datetime_spans', $check_result["available_datetime_spans"]);
                        $app->flash('available_datetime_spans_description_labels', $check_result["available_datetime_spans_description_labels"]);
                        $app->flash('available_datetime_spans_availibility_labels', $check_result["available_datetime_spans_availibility_labels"]);
                        $app->flash('full_datetime_spans', $check_result["full_datetime_spans"]);
                        $app->flash('full_datetime_spans_description_labels', $check_result["full_datetime_spans_description_labels"]);
                        $app->flash('full_datetime_spans_availibility_labels', $check_result["full_datetime_spans_availibility_labels"]);

                        $app->flash('redirected', true);

                        $app->redirect($app->urlFor('user.pre-book-training-course'));
                    }
                }
            }

        })->name('user.book-training-course.post');

    });


    $app->group('/predbiljezba-termina', function() use ($app, $authenticated_user){
        $app->get('/', $authenticated_user(), function () use ($app) {
            $flash = $app->view()->getData('flash');
            $redirected = $flash['redirected'];
            if(!$redirected) {
                $app->redirect($app->urlFor('user.book-training-course'));
            } else {
                $calendar = new Calendar();
                $course_constants = new TrainingCourseConstants();
                $course_constants::set_default_values();
                $datetime_span = new DatetimeSpan();
                $reservation = new Reservation();
                $prereservation = new Prereservation();
                $booking = new Booking();

                $app->render('user/bookings/user.pre_book_training_course.twig', array(
                    'user' => $app->auth_user,
                    'active_page' => 'user.reservations',
                    'course_constants' => $course_constants,
                    'calendar' => $calendar,
                    'reservation' => $reservation,
                    'prereservation' => $prereservation,
                    'booking' => $booking,
                    'datetimes' => $datetime_span));
            }
        })->name('user.pre-book-training-course');


        $app->post('/', $authenticated_user(), function () use ($app) {
            $p_booking_selector = (int)$app->request->post('booking-selection');
            if(!isset($p_booking_selector) || !($p_booking_selector == 1 || $p_booking_selector == 2) ) {
                $app->flash('errors', "Imali ste neispravan odabir opcije rezervacije/predbilježbe.\nPonovite rezervaciju!");
                $app->redirect($app->urlFor('user.book-training-course'));
            } else {
                $p_selected_available_spans = $app->request->post('selected-available-spans');
                $p_selected_available_spans = ($p_selected_available_spans)?:array();
                $p_selected_full_spans = $app->request->post('selected-full-spans');
                $p_selected_full_spans = ($p_selected_full_spans)?:array();

                if($p_booking_selector == 2) {
                    $validation_result = Reservation::validateNewSetOfReservations($p_selected_available_spans, $app->auth_user);
                    if(!($validation_result["validated"])) {
                        // valudation failed
                        if(isset($validation_result["errors"])) {
                            $app->flash('errors',  $validation_result["errors"]);
                        }
                        $app->redirect($app->urlFor('user.book-training-course'));
                    } else {
                        // Validation of input data successful

                        $check_result = Reservation::checkAvailabilityBookings($p_selected_available_spans);
                        if(!($check_result["validated"])) {
                            // valudation failed
                            if(isset($check_result["errors"])) {
                                $app->flash('errors',  $check_result["errors"]);
                            }
                            $app->redirect($app->urlFor('user.book-training-course'));
                        } else {
                            if(!$check_result["prereservations"]) {
                                // no need for pre-reservations, just insert those reservations finally
                                if(Reservation::createNewSet($p_selected_available_spans, $app->auth_user)) {
                                    $success_msg = Logger::logClassicBooking($app->auth_user, $p_selected_available_spans);

                                    $app->flash("success", $success_msg);
                                    $app->redirect($app->urlFor('user.book-training-course'));
                                } else {
                                    $app->flash('errors', "Greška kod unosa u bazu.\nPokušajte ponovno");
                                    $app->redirect($app->urlFor('user.book-training-course'));
                                }


                            } else {
                                $app->flash('errors', "U međuvremenu je došlo do novih promjena popunjenosti termina!\n\n" .
                                    "Broj postojećih rezervacija za neke termine jednak je broju slobodnih mjesta:\n".
                                    $check_result["prereservations_status_label"]);
                                $app->flash('available_datetime_spans', $check_result["available_datetime_spans"]);
                                $app->flash('available_datetime_spans_description_labels', $check_result["available_datetime_spans_description_labels"]);
                                $app->flash('available_datetime_spans_availibility_labels', $check_result["available_datetime_spans_availibility_labels"]);
                                $app->flash('full_datetime_spans', $check_result["full_datetime_spans"]);
                                $app->flash('full_datetime_spans_description_labels', $check_result["full_datetime_spans_description_labels"]);
                                $app->flash('full_datetime_spans_availibility_labels', $check_result["full_datetime_spans_availibility_labels"]);

                                $app->flash('redirected', true);

                                $app->redirect($app->urlFor('user.pre-book-training-course'));
                            }
                        }
                    }
                } else if($p_booking_selector == 1) {

                    $validation_result_reservations = Reservation::validateNewSetOfReservations(array_merge(
                        $p_selected_available_spans, $p_selected_full_spans), $app->auth_user);

                    if(!($validation_result_reservations["validated"])) {
                        // validation failed (reservations)
                        if(isset($validation_result_reservations["errors"])) {
                            $app->flash('errors', $validation_result_reservations["errors"]);
                        }
                        $app->redirect($app->urlFor('user.book-training-course'));
                    }  else {
                        // Validation of input data successful

                        $check_result_reservations = Reservation::checkAvailabilityBookings($p_selected_available_spans);
                        $check_result_prereservations = Prereservation::checkAvailabilityBookings($p_selected_full_spans);

                        if(!$check_result_reservations["prereservations"] && !$check_result_prereservations["reservations"]) {
                            if(Reservation::createNewSet($p_selected_available_spans, $app->auth_user) &&
                                Prereservation::createNewSet($p_selected_full_spans, $app->auth_user)) {
                                $success_msg = Logger::logBookingWithPrereservation($app->auth_user, $p_selected_available_spans, $p_selected_full_spans);

                                $app->flash("success", $success_msg);
                                $app->redirect($app->urlFor('user.book-training-course'));
                            } else {
                                $app->flash('errors', "Greška kod unosa u bazu.\nPokušajte ponovno");
                                echo "sto papira!";exit();
                                $app->redirect($app->urlFor('user.book-training-course'));
                            }
                        } else if($check_result_reservations["prereservations"] && !$check_result_prereservations["reservations"]){
                            $app->flash('errors', "U međuvremenu je došlo do novih promjena popunjenosti termina!\n\n" .
                                "Broj postojećih rezervacija za neke termine jednak je broju slobodnih mjesta:\n".
                                $check_result_reservations["prereservations_status_label"]);
                            $app->flash('available_datetime_spans', array_merge(
                                $check_result_reservations["available_datetime_spans"],
                                $check_result_prereservations["available_datetime_spans"]
                            ));
                            $app->flash('available_datetime_spans_description_labels', array_merge(
                                $check_result_reservations["available_datetime_spans_description_labels"],
                                $check_result_prereservations["available_datetime_spans_description_labels"]
                            ));
                            $app->flash('available_datetime_spans_availibility_labels', array_merge(
                                $check_result_reservations["available_datetime_spans_availibility_labels"],
                                $check_result_prereservations["available_datetime_spans_availibility_labels"]
                            ));
                            $app->flash('full_datetime_spans', array_merge(
                                    $check_result_reservations["full_datetime_spans"],
                                    $check_result_prereservations["full_datetime_spans"])
                            );
                            $app->flash('full_datetime_spans_description_labels', array_merge(
                                $check_result_reservations["full_datetime_spans_description_labels"],
                                $check_result_prereservations["full_datetime_spans_description_labels"]
                            ));
                            $app->flash('full_datetime_spans_availibility_labels', array_merge(
                                $check_result_reservations["full_datetime_spans_availibility_labels"],
                                $check_result_prereservations["full_datetime_spans_availibility_labels"]
                            ));

                            $app->flash('redirected', true);

                            $app->redirect($app->urlFor('user.pre-book-training-course'));

                        } else if(!$check_result_reservations["prereservations"] && $check_result_prereservations["reservations"]){

                            $app->flash('errors', "U međuvremenu je došlo do novih promjena popunjenosti termina!\n\n" .
                                "Broj postojećih rezervacija za neke termine manji je od broja slobodnih mjesta:\n".
                                $check_result_prereservations["reservations_status_label"]);

                            $app->flash('available_datetime_spans', array_merge(
                                $check_result_reservations["available_datetime_spans"],
                                $check_result_prereservations["available_datetime_spans"]
                            ));
                            $app->flash('available_datetime_spans_description_labels', array_merge(
                                $check_result_reservations["available_datetime_spans_description_labels"],
                                $check_result_prereservations["available_datetime_spans_description_labels"]
                            ));
                            $app->flash('available_datetime_spans_availibility_labels', array_merge(
                                $check_result_reservations["available_datetime_spans_availibility_labels"],
                                $check_result_prereservations["available_datetime_spans_availibility_labels"]
                            ));
                            $app->flash('full_datetime_spans', array_merge(
                                $check_result_reservations["full_datetime_spans"],
                                $check_result_prereservations["full_datetime_spans"]
                            ));
                            $app->flash('full_datetime_spans_description_labels', array_merge(
                                $check_result_reservations["full_datetime_spans_description_labels"],
                                $check_result_prereservations["full_datetime_spans_description_labels"]
                            ));
                            $app->flash('full_datetime_spans_availibility_labels', array_merge(
                                $check_result_reservations["full_datetime_spans_availibility_labels"],
                                $check_result_prereservations["full_datetime_spans_availibility_labels"]
                            ));
                            $app->flash('redirected', true);

                            $app->redirect($app->urlFor('user.pre-book-training-course'));

                        } else if($check_result_reservations["prereservations"] && $check_result_prereservations["reservations"]){
                            $app->flash('errors', "U međuvremenu je došlo do novih promjena popunjenosti termina!\n\n" .
                                "Broj postojećih rezervacija za neke termine jednak je broju slobodnih mjesta:\n".
                                $check_result_reservations["prereservations_status_label"] . "\n" .
                                "Broj postojećih rezervacija za neke termine manji je od broja slobodnih mjesta:\n".
                                $check_result_prereservations["reservations_status_label"]);
                            $app->flash('available_datetime_spans', array_merge(
                                $check_result_reservations["available_datetime_spans"],
                                $check_result_prereservations["available_datetime_spans"]
                            ));
                            $app->flash('available_datetime_spans_description_labels', array_merge(
                                $check_result_reservations["available_datetime_spans_description_labels"],
                                $check_result_prereservations["available_datetime_spans_description_labels"]
                            ));
                            $app->flash('available_datetime_spans_availibility_labels', array_merge(
                                $check_result_reservations["available_datetime_spans_availibility_labels"],
                                $check_result_prereservations["available_datetime_spans_availibility_labels"]
                            ));
                            $app->flash('full_datetime_spans', array_merge(
                                $check_result_reservations["full_datetime_spans"],
                                $check_result_prereservations["full_datetime_spans"])
                            );
                            $app->flash('full_datetime_spans_description_labels', array_merge(
                                $check_result_reservations["full_datetime_spans_description_labels"],
                                $check_result_prereservations["full_datetime_spans_description_labels"]
                            ));
                            $app->flash('full_datetime_spans_availibility_labels', array_merge(
                                $check_result_reservations["full_datetime_spans_availibility_labels"],
                                $check_result_prereservations["full_datetime_spans_availibility_labels"]
                            ));

                            $app->flash('redirected', true);

                            $app->redirect($app->urlFor('user.pre-book-training-course'));
                        }
                    }

                }

            }

        })->name('user.pre-book-training-course.post');

    });




});
?>
