<?php


namespace app\helpers;

use app\helpers\Configuration as Cfg;



/* Used helper calendar functions from booking calendar */

class Calendar
{
    public static function shift_day_month_year($date, $delta_days = 0,
        $delta_months = 0, $delta_years = 0)
    {
        // delta_years adjustment:
        // Use this to adjust for next and previous years.
        // Add the $delta_years to the current year and make the new date.

        if ($delta_years != 0) {
            // Split the date into its components.
            list($year, $month, $day) = explode("-", $date);
            // Careful to check for leap year effects!
            if ($month == 2 && $day == 29) {
                // Check the number of days in the month/year, with the day set to 1.
                $tmp_date = date("Y-m", mktime(1, 0, 0, $month, 1, $year + $delta_years));
                list($new_year, $new_month) = explode("-", $tmp_date);
                $days_in_month = number_of_days_in_month($new_year, $new_month);
                // Lower the day value if it exceeds the number of days in the new month/year.
                if ($days_in_month < $day) { $day = $days_in_month; }
                $date = $new_year . '-' . $month . '-' . $day;
            } else {
                $new_year = $year + $delta_years;
                $date = sprintf("%04d-%02d-%02d", $new_year, $month, $day);
            }
        }

        // delta_months adjustment:
        // Use this to adjust for next and previous months.
        // Note: This DOES NOT subtract 30 days!
        // Use $delta_days for that type of calculation.
        // Add the $delta_months to the current month and make the new date.

        if ($delta_months != 0) {
            // Split the date into its components.
            list($year, $month, $day) = explode("-", $date);
            // Calculate New Month and Year
            $new_year = $year;
            $new_month = $month + $delta_months;
            if ($delta_months < -840 || $delta_months > 840) { $new_month = $month; } // Bad Delta
            if ($delta_months > 0) { // Adding Months
                while ($new_month > 12) { // Adjust so $new_month is between 1 and 12.
                    $new_year++;
                    $new_month -= 12;
                }
            } elseif ($delta_months < 0) { // Subtracting Months
                while ($new_month < 1) { // Adjust so $new_month is between 1 and 12.
                    $new_year--;
                    $new_month += 12;
                }
            }
            // Careful to check for number of days in the new month!
            $days_in_month = self::number_of_days_in_month($new_year, $new_month);
            // Lower the day value if it exceeds the number of days in the new month/year.
            if ($days_in_month < $day) { $day = $days_in_month; }
            $date = sprintf("%04d-%02d-%02d", $new_year, $new_month, $day);
        }

        // delta_days adjustment:
        // Use this to adjust for next and previous days.
        // Add the $delta_days to the current day and make the new date.

        if ($delta_days != 0) {
            // Split the date into its components.
            list($year, $month, $day) = explode("-", $date);
            // Create New Date
            $date = date("Y-m-d", mktime(1, 0, 0, $month, $day, $year) + $delta_days*24*60*60);
        }

        // Check Valid Date, Use for TroubleShooting
        //list($year, $month, $day) = explode("-", $date);
        //$valid = checkdate($month, $day, $year);
        //if (!$valid)  return "Error, function add_delta_ymd: Could not process valid date!";

        return $date;
    }


    // Find the Number of Days in a Month
    // Month is between 1 and 12
    public static function number_of_days_in_month($year, $month)
    {
        $days_in_the_month = array (31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        if ($month != 2) {
            return $days_in_the_month[$month - 1];
        }
        // or Check for Leap Year (February)
        return (checkdate($month, 29, $year)) ? 29 : 28;
    }


    public static function validate_date($date, $format = 'Y-m-d H:i:s'){
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }


    public static function dates_span($start_date, $repeating = false, $end_date = null,
                                      $repeating_interval, $repeating_frequency) {
        $dates = array();
        if(!$repeating_interval) {
            $dates[] = $start_date;
        } else {
            $new_date = $start_date;
            $temp_end_date = strtotime($end_date);

            $count_of_days = 1;

            switch ( $repeating_interval ) {
                case Cfg::read('repeating.interval.day'):

                    do {
                        $dates[] = $new_date;
                        $new_date = self::shift_day_month_year($start_date, $count_of_days * $repeating_frequency);
                        $temp_current_date = strtotime($new_date);
                        $count_of_days++;

                    } while($temp_current_date <= $temp_end_date);

                    break;
                case Cfg::read('repeating.interval.week'):

                    do {
                        $dates[] = $new_date;
                        $new_date = self::shift_day_month_year($start_date,
                            $count_of_days * 7 * $repeating_frequency);
                        $temp_current_date = strtotime($new_date);
                        $count_of_days++;

                    } while($temp_current_date <= $temp_end_date);

                    break;
                case Cfg::read('repeating.interval.month.1'):

                    do {
                        $dates[] = $new_date;
                        $new_date = self::shift_day_month_year($start_date,
                            0, $count_of_days * $repeating_frequency);
                        $temp_current_date = strtotime($new_date);
                        $count_of_days++;

                    } while($temp_current_date <= $temp_end_date);

                    break;
                case Cfg::read('repeating.interval.month.2'):
                    // not necessary yet

                    break;
                default:
                    break;
            }

        }

        return $dates;
    }


    public static function weeks_in_a_month($date) {
        list($year, $month, $day) = explode("-", $date);
        $last_day = date("t", mktime(0, 0, 0, $month, 1, $year));

        $day = strtotime("{$year}-{$month}-{$last_day}");
        $week_count = date('W', $day) - date('W', strtotime(date('Y-m-01', $day))) + 1;
        return $week_count;
    }



    public static function weekday_of_first_in_month($date) {
        list($year, $month, $day) = explode("-", $date);
        return date("w", mktime(1, 0, 0, $month, 1, $year));
    }


    public static function days_in_a_month_number($date) {
        list($year, $month, $day) = explode("-", $date);
        return date("t", mktime(0, 0, 0, $month, 1, $year));
    }


    public static function cro_month_label($m) {
        switch((int)$m)
        {
            case 0: return "UNESI MJESEC IZMEDJU 1 i 12!";
            case 1: return "siječanj";
            case 2: return "veljača";
            case 3: return "ožujak";
            case 4: return "travanj";
            case 5: return "svibanj";
            case 6: return "lipanj";
            case 7: return "srpanj";
            case 8: return "kolovoz";
            case 9: return "rujan";
            case 10: return "listopad";
            case 11: return "studeni";
            case 12: return "prosinac";
            default: return "- unknown -";
        }
    }





    public static function cro_month_label_genitive($m) {
        switch((int)$m)
        {
            case 0: return "UNESI MJESEC IZMEDJU 1 i 12!";
            case 1: return "siječnja";
            case 2: return "veljače";
            case 3: return "ožujka";
            case 4: return "travnja";
            case 5: return "svibnja";
            case 6: return "lipnja";
            case 7: return "srpnja";
            case 8: return "kolovoza";
            case 9: return "rujna";
            case 10: return "listopada";
            case 11: return "studenog";
            case 12: return "prosinca";
            default: return "- unknown -";
        }
    }



    public static function cro_weekday_label($idx) {
        switch($idx) {
            case 0: return "nedjelja";
            case 1: return "ponedjeljak";
            case 2: return "utorak";
            case 3: return "srijeda";
            case 4: return "četvrtak";
            case 5: return "petak";
            case 6: return "subota";
            default: return "unknown-weekday($idx)";
        }
    }


    public static function cro_weekday_label_short($idx) {
        switch($idx) {
            case 0: return "ned";
            case 1: return "pon";
            case 2: return "uto";
            case 3: return "sri";
            case 4: return "čet";
            case 5: return "pet";
            case 6: return "sub";
            default: return "unknown-weekday($idx)";
        }
    }



    public static function advanced_month_label($date) {
        $label = "Default name";

        $first_day_in_view = self::shift_day_month_year($date, self::days_offset_left(), 0, 0);
        $last_day_in_view = self::shift_day_month_year($date, self::days_offset_right(), 0, 0);

        list($year_1st, $month_1st, $day_1st) = explode("-", $first_day_in_view);
        list($year_nth, $month_nth, $day_nth) = explode("-", $last_day_in_view);

        if($year_1st == $year_nth) {
            if ($month_1st == $month_nth) {
                $label = sprintf("%s %s.", self::cro_month_label($month_1st), $year_1st);
            } else {
                $label = sprintf("%s i %s %s.", self::cro_month_label($month_1st), self::cro_month_label($month_nth), $year_1st);
            }
        } else {
            $label = sprintf("%s %s. i %s %s.", self::cro_month_label($month_1st), $year_1st, self::cro_month_label($month_nth), $year_nth);
        }

        return $label;

    }

    public static function days_offset_left() {
        return Cfg::read("calendar.date.offset.day.left");
    }

    public static function days_offset_right() {
        return Cfg::read("calendar.date.offset.day.right");
    }

    public static function days_offset_std() {
        return Cfg::read("calendar.date.offset.day.std");
    }



    public static function months_break($date) {

        $first_day_in_view = self::shift_day_month_year($date, self::days_offset_left(), 0, 0);
        $last_day_in_view = self::shift_day_month_year($date, self::days_offset_right(), 0, 0);

        list($year_1st, $month_1st, $day_1st) = explode("-", $first_day_in_view);
        list($year_nth, $month_nth, $day_nth) = explode("-", $last_day_in_view);

        if($year_1st == $year_nth) {
            if ($month_1st == $month_nth) {
                return null;
            } else {
                $index = date("t", mktime(0, 0, 0, $month_1st, 1, $year_1st)) - $day_1st + 1;
            }
        } else {
            $index = date("t", mktime(0, 0, 0, $month_1st, 1, $year_1st)) - $day_1st + 1;
        }
        return $index;

    }
}


?>