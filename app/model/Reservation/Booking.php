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


    public static function getByUser($user_id, $order_by = "datetime_span.datetime_span_start")
    {
        $reservations = Reservation::getByUser($user_id, $order_by);
        $prereservations = Prereservation::getByUser($user_id, $order_by);
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
        $prereservations = Prereservation::getByUser($user_id, $order_by);
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




}