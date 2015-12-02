<?php

use \app\model\User\User;
use \app\helpers\Configuration as Cfg;
use \app\model\Reservation\Booking;
use \app\model\Reservation\TrainingCourse;
use \app\model\Reservation\DatetimeSpan;
use \app\model\Messages\Message;
use \app\model\Messages\Logger;
use \app\model\Reservation\Reservation;
use \app\model\Reservation\Prereservation;


$app->get('/cron-provjera-test', function() use ($app) {
    Message::createNew("admin", 1, "Cron on..");
});


$app->get('/cron-provjera', function() use ($app) {
    echo '<pre style = "font-family: \'Courier New\', Courier, monospace;">';
    echo "<br>" .
        " ------------------------------------------------------------<br>".
        "  ---------------------------------------------------------- <br>".
        "  -     ______       _______       ______     ____    __   - <br>".
        '  -   /   ___  \    |   __  \     /  __  \   |     \ |  |  - <br>'.
        '  -  |  /    \__|   |  |__\  |   |  |  |  |  |  |\  \|  |  - <br>' .
        '  -  |  |     __    |   __  <    |  |  |  |  |  | \     |  - <br>'.
        '  -  \  \____/  |   |  |  \  \   |  |__|  |  |  |  \    |  - <br>'.
        '  -   \_______ /    |__|   \__|   \______/   |__|   \___|  - <br>'.
        '  -                                                        - <br>'.
        ' ------------------------------------------------------------';
    echo "</pre>";

    $max_reservation_time = TrainingCourse::maxReservationTime();

    $courses_until = time() + $max_reservation_time;

    $spans = DatetimeSpan::getFromNowUntil($courses_until);
    foreach ($spans as $span) {
        if($span->training_course) {

            // Check if bookings for this span are still allowed:
            $course_booking_allowed = (strtotime($span->span_start) - $span->training_course->reservation_time) > date("U");

            if(!$course_booking_allowed) {
                // Bookings are not allowed anymore, check reservations (if they are not checked already)

                if($span->canceled == 0) {
                    // not confirmed nor canceled yet

                    $existing_reservations = Reservation::getByDatetimeSpan($span->id);
                    $number_of_reservations = sizeof($existing_reservations);
                    if($number_of_reservations < $span->training_course->min_reservations) {
                        $span->setFlagCanceled(1);
                        foreach($existing_reservations as $reservation) {
                            $reservation->setFlagCanceled(1);
                        }
                        Logger::logSpanCancelation($span, $existing_reservations);

                    } else {
                        $span->setFlagCanceled(2);
                        foreach($existing_reservations as $reservation) {
                            $reservation->setFlagCanceled(2);
                        }
                        $existing_pre_reservations = Prereservation::getUnactivatedByDatetimeSpan($span->id);
                        foreach($existing_pre_reservations as $pre_reservation) {
                            $pre_reservation->setFlagActivated(2);
                        }
                        Logger::logSpanConfirmation($span, $existing_reservations, $existing_pre_reservations);
                    }
                }
            }
        }
    }


})->name('cron-bookings-check');
?>