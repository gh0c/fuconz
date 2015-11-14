<?php

namespace app\model\Reservation;

use app\helpers\Calendar;
use MatthiasMullie\Minify\Exception;


// Configuration Class
class TrainingCourseConstants {
    static $confArray;

    function __construct( ) {
        self::write("today", date("Y-m-d"));
        list($y, $m, $d) = explode("-", self::read("today"));
        self::write("today.year", $y);
        self::write("today.month", $m);
        self::write("today.day", $d);
    }

    public static function read($name) {
        if (array_key_exists($name, self::$confArray)) {
            return self::$confArray[$name];
        } else {
            return null;
        }
    }

    public static function write($name, $value) {
        self::$confArray[$name] = $value;
    }


    public static function set_default_values() {

        $selected_date = self::read("today");

        self::write("selected.date", $selected_date);
        self::write("selected.year", self::read("today.year"));
        self::write("selected.month", self::read("today.month"));
        self::write("selected.day", self::read("today.day"));

        self::write("date", $selected_date);

        self::write("day.before",
            Calendar::shift_day_month_year($selected_date, -1, 0, 0));
        list($y, $m, $d) = explode("-", self::read("day.before"));
        self::write("day.before.day", $d);
        self::write("day.before.month", $m);
        self::write("day.before.year", $y);

        self::write("day.after",
            Calendar::shift_day_month_year($selected_date, 1, 0, 0));
        list($y, $m, $d) = explode("-", self::read("day.after"));
        self::write("day.after.day", $d);
        self::write("day.after.month", $m);
        self::write("day.after.year", $y);

        self::write("month.before",
            Calendar::shift_day_month_year($selected_date, 0, -1, 0));
        list($y, $m, $d) = explode("-", self::read("month.before"));
        self::write("month.before.day", $d);
        self::write("month.before.month", $m);
        self::write("month.before.year", $y);

        self::write("month.after",
            Calendar::shift_day_month_year($selected_date, 0, 1, 0));
        list($y, $m, $d) = explode("-", self::read("month.after"));
        self::write("month.after.day", $d);
        self::write("month.after.month", $m);
        self::write("month.after.year", $y);

        self::write("year.before",
            Calendar::shift_day_month_year($selected_date, 0, 0, -1));
        list($y, $m, $d) = explode("-", self::read("year.before"));
        self::write("year.before.day", $d);
        self::write("year.before.month", $m);
        self::write("year.before.year", $y);

        self::write("year.after",
            Calendar::shift_day_month_year($selected_date, 0, 0, 1));
        list($y, $m, $d) = explode("-", self::read("year.after"));
        self::write("year.after.day", $d);
        self::write("year.after.month", $m);
        self::write("year.after.year", $y);

    }



    public static function set_custom_values($date) {
        list($y, $m, $d) = explode("-", $date);

        self::write("selected.date", $date);
        self::write("selected.year", $y);
        self::write("selected.month", $m);
        self::write("selected.day", $d);

        self::write("date", $date);

        self::write("day.before",
            Calendar::shift_day_month_year($date, -1, 0, 0));
        list($y, $m, $d) = explode("-", self::read("day.before"));
        self::write("day.before.day", $d);
        self::write("day.before.month", $m);
        self::write("day.before.year", $y);

        self::write("day.after",
            Calendar::shift_day_month_year($date, 1, 0, 0));
        list($y, $m, $d) = explode("-", self::read("day.after"));
        self::write("day.after.day", $d);
        self::write("day.after.month", $m);
        self::write("day.after.year", $y);

        self::write("month.before",
            Calendar::shift_day_month_year($date, 0, -1, 0));
        list($y, $m, $d) = explode("-", self::read("month.before"));
        self::write("month.before.day", $d);
        self::write("month.before.month", $m);
        self::write("month.before.year", $y);

        self::write("month.after",
            Calendar::shift_day_month_year($date, 0, 1, 0));
        list($y, $m, $d) = explode("-", self::read("month.after"));
        self::write("month.after.day", $d);
        self::write("month.after.month", $m);
        self::write("month.after.year", $y);

        self::write("year.before",
            Calendar::shift_day_month_year($date, 0, 0, -1));
        list($y, $m, $d) = explode("-", self::read("year.before"));
        self::write("year.before.day", $d);
        self::write("year.before.month", $m);
        self::write("year.before.year", $y);

        self::write("year.after",
            Calendar::shift_day_month_year($date, 0, 0, 1));
        list($y, $m, $d) = explode("-", self::read("year.after"));
        self::write("year.after.day", $d);
        self::write("year.after.month", $m);
        self::write("year.after.year", $y);
    }


    public static function set_day_offset($offset) {
        $date_with_offset = Calendar::shift_day_month_year(self::read("selected.date"), $offset, 0, 0);
        self::write(sprintf("day.offset.%+d", $offset), $date_with_offset);
        list($y, $m, $d) = explode("-", $date_with_offset);
        self::write(sprintf("day.offset.%+d.day", $offset), $d);
        self::write(sprintf("day.offset.%+d.month", $offset), $m);
        self::write(sprintf("day.offset.%+d.year", $offset), $y);
    }


    public static function get_day_offset($offset, $key = "") {

        if(self::read(sprintf("day.offset.%+d", $offset))) {
            return self::read(sprintf("day.offset.%+d%s", $offset, $key));
        } else {
            self::set_day_offset($offset);
            return self::read(sprintf("day.offset.%+d%s", $offset, $key));
        }
    }




}