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

        if($repeating) {
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

}


?>