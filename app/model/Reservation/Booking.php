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



}