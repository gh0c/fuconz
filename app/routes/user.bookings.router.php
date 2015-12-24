<?php
use \app\model\User\User;
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

$app->group('/clanovi', function () use ($app, $authenticated_user) {

    $app->group('/rezervacija-termina', function() use ($app, $authenticated_user){

        $app->post('/promjeni-mjesec-kalendara', $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {

                $body = $app->request->getBody();
                $json_data_received = json_decode($body, true);

                $p_selected_date = $json_data_received['date'];
                $p_pre_selected_spans = $json_data_received['selected-spans'];

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
            }

        })->name('user.booking.change-month.post');



        $app->post('/promjeni-datum-kalendara', $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {

                $body = $app->request->getBody();
                $json_data_received = json_decode($body, true);

                $p_selected_date = $json_data_received['date'];
                $p_pre_selected_spans = $json_data_received['selected-spans'];
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
                $app->response->headers->set('Content-Type', 'application/json');

                $app->render('calendar/multi.wrapper.twig', array(
                    'user' => $app->auth_user,
                    'active_page' => 'user.reservations',
                    'course_constants' => $course_constants,
                    'calendar' => $calendar,
                    'reservation' => $reservation,
                    'prereservation' => $prereservation,
                    'booking' => $booking,
                    'datetimes' => $datetime_span,
                    'pre_selected_spans' => $pre_selected_spans,
                    'calendar_class_desktop' => 'bookings',
                    'calendar_class_tablet' => 'bookings',
                    'calendar_class_mobile' => 'bookings',
                    'cal_wrapper_desktop' => 'calendar/months.ultra.horizontal.twig',
                    'cal_wrapper_tablet' => 'calendar/months.standard.twig',
                    'cal_wrapper_mobile' => 'calendar/months.ultra.vertical.twig',
                    'url_change_view_desktop' => $app->urlFor('user.booking.change-date-offset.post'),
                    'url_change_view_tablet' => $app->urlFor('user.booking.change-date-offset.post'),
                    'url_change_view_mobile' => $app->urlFor('user.booking.change-date-offset.post'),
                    'day_selector' => 'user/bookings/calendar/day.selector.bookings.ultra.horizontal.twig',

                ));
            }

        })->name('user.booking.change-date-offset.post');



        $app->get('/', $authenticated_user(), function () use ($app) {
            $calendar = new Calendar();
            $course_constants = new TrainingCourseConstants();
            $course_constants::set_default_values();
            //
//            $course_constants::set_custom_values("2015-12-28");
            //
            //
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
                'booking' => $booking,
                'calendar_class_desktop' => 'bookings',
                'calendar_class_tablet' => 'bookings',
                'calendar_class_mobile' => 'bookings',
                'cal_wrapper_desktop' => 'calendar/months.ultra.horizontal.twig',
                'cal_wrapper_tablet' => 'calendar/months.standard.twig',
                'cal_wrapper_mobile' => 'calendar/months.ultra.vertical.twig',
                'url_change_view_desktop' => $app->urlFor('user.booking.change-date-offset.post'),
                'url_change_view_tablet' => $app->urlFor('user.booking.change-date-offset.post'),
                'url_change_view_mobile' => $app->urlFor('user.booking.change-date-offset.post'),
                'day_selector' => 'user/bookings/calendar/day.selector.bookings.ultra.horizontal.twig',

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
                        $app->flash('available_datetime_spans_description_labels_midi', $check_result["available_datetime_spans_description_labels_midi"]);
                        $app->flash('available_datetime_spans_availibility_labels', $check_result["available_datetime_spans_availibility_labels"]);
                        $app->flash('full_datetime_spans', $check_result["full_datetime_spans"]);
                        $app->flash('full_datetime_spans_description_labels', $check_result["full_datetime_spans_description_labels"]);
                        $app->flash('full_datetime_spans_description_labels_midi', $check_result["full_datetime_spans_description_labels_midi"]);
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
                                $app->flash('available_datetime_spans_description_labels_midi', $check_result["available_datetime_spans_description_labels_midi"]);
                                $app->flash('available_datetime_spans_availibility_labels', $check_result["available_datetime_spans_availibility_labels"]);
                                $app->flash('full_datetime_spans', $check_result["full_datetime_spans"]);
                                $app->flash('full_datetime_spans_description_labels', $check_result["full_datetime_spans_description_labels"]);
                                $app->flash('full_datetime_spans_description_labels_midi', $check_result["full_datetime_spans_description_labels_midi"]);
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
                            $app->flash('available_datetime_spans_description_labels_midi', array_merge(
                                $check_result_reservations["available_datetime_spans_description_labels_midi"],
                                $check_result_prereservations["available_datetime_spans_description_labels_midi"]
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
                            $app->flash('full_datetime_spans_description_labels_midi', array_merge(
                                $check_result_reservations["full_datetime_spans_description_labels_midi"],
                                $check_result_prereservations["full_datetime_spans_description_labels_midi"]
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
                            $app->flash('available_datetime_spans_description_labels_midi', array_merge(
                                $check_result_reservations["available_datetime_spans_description_labels_midi"],
                                $check_result_prereservations["available_datetime_spans_description_labels_midi"]
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
                            $app->flash('full_datetime_spans_description_labels_midi', array_merge(
                                $check_result_reservations["full_datetime_spans_description_labels_midi"],
                                $check_result_prereservations["full_datetime_spans_description_labels_midi"]
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
                            $app->flash('available_datetime_spans_description_labels_midi', array_merge(
                                $check_result_reservations["available_datetime_spans_description_labels_midi"],
                                $check_result_prereservations["available_datetime_spans_description_labels_midi"]
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
                            $app->flash('full_datetime_spans_description_labels_midi', array_merge(
                                $check_result_reservations["full_datetime_spans_description_labels_midi"],
                                $check_result_prereservations["full_datetime_spans_description_labels_midi"]
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



        $app->post('/otkazivanje', $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {

                $body = $app->request->getBody();
                $json_data_received = json_decode($body, true);

                $p_booking_id = $json_data_received['booking-id'];
                $p_booking_type = $json_data_received['booking-type'];

                if(!isset($p_booking_id) || !isset($p_booking_type) || !in_array($p_booking_type, array("reservation", "pre_reservation"))) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        "error" =>"Neispravni podaci za otkazivanje rezervacije!"
                    ));
                    exit();
                }
                if ($p_booking_type == "pre_reservation") {
                    $pre_reservation = Prereservation::getById($p_booking_id);
                    if(!$pre_reservation || $pre_reservation->user->id != $app->auth_user->id) {
                        header('Content-Type: application/json');
                        echo json_encode(array(
                            "error" =>"Nije pronađena tvoja predbilježba sa unesenim ID-em!"
                        ));
                        exit();
                    } else {
                        $pre_reservation->delete();
                        Logger::logPrereservationCancelation($app->auth_user, $pre_reservation);
                        header('Content-Type: application/json');
                        echo json_encode(array(
                            "success" => true
                        ));
                        exit();
                    }
                } else if ($p_booking_type == "reservation") {
                    $reservation = Reservation::getById($p_booking_id);
                    if(!$reservation || $reservation->user->id != $app->auth_user->id) {
                        header('Content-Type: application/json');
                        echo json_encode(array(
                            "error" =>"Nije pronađena tvoja prijava sa unesenim ID-em!"
                        ));
                        exit();
                    } else {
                        $existing_prereservations = Prereservation::getUnactivatedByDatetimeSpan(
                            $reservation->datetime_span->id, "created_at ASC");
                        if(count($existing_prereservations) > 0) {
                            $reservation->delete();
                            $pre_reservation = $existing_prereservations[0];
                            $pre_reservation->setFlagActivated(1);

                            Reservation::createNew($reservation->training_course_id, $reservation->datetime_span->id,
                                $pre_reservation->user->id, 1, 1);
                            Logger::logReservationCancelationWithTriggeredPrereservationActivation($app->auth_user, $reservation, $pre_reservation);
                            header('Content-Type: application/json');
                            echo json_encode(array(
                                "success" => true
                            ));
                            exit();
                        } else {
                            $reservation->delete();
                            Logger::logClassicReservationCancelation($app->auth_user, $reservation);
                            header('Content-Type: application/json');
                            echo json_encode(array(
                                "success" => true
                            ));
                            exit();
                        }
//
                    }
                }


            }

        })->name('user.booking.cancel.post');

    });


    $app->get('/detalji-prijava/:span_id', $authenticated_user(), function ($span_id) use ($app) {
        $datetime_span = DatetimeSpan::getById($span_id);
        if (!$datetime_span) {
            $app->flash('errors',  "Ne postoji traženi termin.");
            $app->redirect($app->urlFor('user.bookings.home'));
        } else {
            $bookings = Booking::getByDatetimeSpan($span_id);
//            $logs = ActionLog::getActionLogsForDatetimeSpan($datetime_span->id);

//
            $app->render('user/bookings/user.bookings.span.details.twig', array(
                'user' => $app->auth_user,
                'active_page' => "user.main",
                'active_item' => "user.main.bookings",
                'bookings' => $bookings,
                'span' => $datetime_span,
//                'logs' => $logs,
                'text_help' => new \app\helpers\Text()
            ));
        }

    })->name('user.bookings.span.details');
});




?>
