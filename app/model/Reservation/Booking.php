<?php

namespace app\model\Reservation;

class Booking
{

    public function getByUserAndDatetimeSpan($user_id, $datetime_span_id)
    {
        if($reservation = Reservation::getByUserAndDatetimeSpan($user_id, $datetime_span_id)) {
            return $reservation;
        }
        if($prereservation  = Prereservation::getByUserAndDatetimeSpan($user_id, $datetime_span_id)) {
            return $prereservation;
        }
        return null;
    }


    public static function getByDatetimeSpan($datetime_span_id)
    {
        $reservations = Reservation::getByDatetimeSpan($datetime_span_id);
        $prereservations = Prereservation::getUnactivatedByDatetimeSpan($datetime_span_id);
        $all_bookings = array_merge($reservations, $prereservations);
        usort($all_bookings, array('app\model\Reservation\Booking', "sortByCreatedAt"));
        return $all_bookings;
    }

    public static function existsForUser($userId)
    {
        if (Reservation::existsForUser($userId) || Prereservation::existsForUser($userId))
            return true;
        else
            return false;
    }


    private static function sortByDatetimeSpanStart($a, $b)
    {
        if(strtotime($b->datetime_span->span_start) != strtotime($a->datetime_span->span_start)) {
            return strtotime($b->datetime_span->span_start) - strtotime($a->datetime_span->span_start);
        }
        else {
            if($b->type == "prereservation")
                return -1;
            return 1;
        }
    }

    private static function sortByCreatedAt($a, $b)
    {
        if(strtotime($b->created_at) != strtotime($a->created_at)) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        }
        else {
            if($b->type == "prereservation")
                return -1;
            return 1;
        }
    }


    public static function getByUser($user_id, $order_by = "datetime_span.datetime_span_start")
    {
        $reservations = Reservation::getByUser($user_id, $order_by);
        $prereservations = Prereservation::getUnactivatedByUser($user_id, $order_by);
        $all_bookings = array_merge($reservations, $prereservations);
        usort($all_bookings, array('app\model\Reservation\Booking', "sortByDatetimeSpanStart"));
        return $all_bookings;
    }

    public static function getByUserSorted($user_id, $order_by = "datetime_span.datetime_span_start")
    {
        $ended = array();
        $booking_not_allowed = array();
        $booking_allowed = array();

        $reservations = Reservation::getByUser($user_id, $order_by);
        $prereservations = Prereservation::getUnactivatedByUser($user_id, $order_by);
        $all_bookings = array_merge($reservations, $prereservations);

        foreach ($all_bookings as $booking ) {
            if($booking->datetime_span->span_start < date("Y-m-d H:i:s")) {
                $ended[] = $booking;
            } else if (!( (strtotime($booking->datetime_span->span_start) - $booking->datetime_span->training_course->reservation_time )
                > date("U"))) {
                $booking_not_allowed[] = $booking;
            } else {
                $booking_allowed[] = $booking;
            }
        }
        usort($ended, array('app\model\Reservation\Booking', "sortByDatetimeSpanStart"));
        usort($booking_not_allowed, array('app\model\Reservation\Booking', "sortByDatetimeSpanStart"));
        usort($booking_allowed, array('app\model\Reservation\Booking', "sortByDatetimeSpanStart"));

        return array($booking_allowed, $booking_not_allowed, $ended);
    }


    public static function getByUserLimitedSorted($user_id, $limit_allowed = 1000, $limit_ended = 1000,
                                                  $limit_not_allowed = 1000, $total = 1000, $order_by = "datetime_span.datetime_span_start")
    {
        $ended = array();
        $booking_not_allowed = array();
        $booking_allowed = array();

        $reservations = Reservation::getByUser($user_id, $order_by);
        $pre_reservations = Prereservation::getUnactivatedByUser($user_id, $order_by);
        $all_bookings = array_merge($reservations, $pre_reservations);

        foreach ($all_bookings as $booking ) {
            if($booking->datetime_span->span_start < date("Y-m-d H:i:s")) {
                $ended[] = $booking;
            } else if (!( (strtotime($booking->datetime_span->span_start) - $booking->datetime_span->training_course->reservation_time )
                > date("U"))) {
                $booking_not_allowed[] = $booking;
            } else {
                $booking_allowed[] = $booking;
            }
        }

//        echo sprintf("We originally got %d not allowed, %d allowed and %d ended bookings<br>",
//            sizeof($booking_not_allowed), sizeof($booking_allowed), sizeof($ended));

        usort($booking_not_allowed, array('app\model\Reservation\Booking', "sortByDatetimeSpanStart"));

        $booking_not_allowed = array_slice($booking_not_allowed, 0, $limit_not_allowed);

        $o_not_allowed_size = sizeof($booking_not_allowed);
        if($o_not_allowed_size >= $total) {
            $ended = array();
            $booking_allowed = array();
        } else {
            usort($booking_allowed, array('app\model\Reservation\Booking', "sortByDatetimeSpanStart"));
            $limit_allowed = (($total - $o_not_allowed_size) > $limit_allowed) ? $limit_allowed : ($total - $o_not_allowed_size);
            $booking_allowed = array_slice($booking_allowed, 0, $limit_allowed);
            $o_allowed_size = sizeof($booking_allowed);

            if($o_not_allowed_size + $o_allowed_size >= $total) {
                $ended = array();
            } else {
                usort($ended, array('app\model\Reservation\Booking', "sortByDatetimeSpanStart"));
                $limit_ended = (($total - $limit_ended - $o_allowed_size) > $limit_ended) ? $limit_ended : ($total - $o_not_allowed_size - $o_allowed_size);
                $ended = array_slice($ended, 0, $limit_ended);
                $o_ended_size = sizeof($ended);

            }
        }


        return array($booking_allowed, $booking_not_allowed, $ended);
    }



}