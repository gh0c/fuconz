<?php

namespace app\model\Reservation;

use app\helpers\Calendar;


// Configuration Class
class TrainingCourseConstants {
    static $confArray;

    function __construct( ) {
        self::write("today", date("Y-m-d"));
        list($y, $m, $d) = explode("-", self::sead("today"));
        self::write("today.year", $y);
        self::write("today.month", $m);
        self::write("today.day", $d);
    }

    public static function read($name) {
        return self::$confArray[$name];
    }

    public static function write($name, $value) {
        self::$confArray[$name] = $value;
    }


    public static function set_default_values() {
        self::write("selected.date", self::read("today"));
        self::write("selected.year", self::read("today.year"));
        self::write("selected.month", self::read("today.mont"));
        self::write("selected.day", self::read("today.day"));

        self::write("date", self::read("selected.date"));

        self::write("day.before",
            Calendar::shift_day_month_year(self::read("selected.date"), -1, 0, 0));
        self::write("day.after",
            Calendar::shift_day_month_year(self::read("selected.date"), 1, 0, 0));
        self::write("month.before",
            Calendar::shift_day_month_year(self::read("selected.date"), 0, -1, 0));
        self::write("month.after",
            Calendar::shift_day_month_year(self::read("selected.date"), 0, 1, 0));
        self::write("year.before",
            Calendar::shift_day_month_year(self::read("selected.date"), 0, 0, -1));
        self::write("year.after",
            Calendar::shift_day_month_year(self::read("selected.date"), 0, 0, 1));

    }


}