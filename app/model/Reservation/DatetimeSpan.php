<?php

namespace app\model\Reservation;

use \app\helpers\Calendar;
use \app\helpers\Configuration;
use app\model\Database\DatabaseConnection;
use \PDO;
use \PDOException;


class DatetimeSpan {

    public $id = null;

    public $span_start = null;
    public $span_end = null;

    public $day_of_week = null;
    public $course_id = null;

    public $training_course = null;

    public $start_time;
    public $start_time_hour;
    public $start_time_minutes;
    public $date;
    public $end_time;

    public $canceled = null;

    function __construct($input_data = array()) {

        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];

        if ( isset( $input_data['datetime_span_start'] ) ) {
            $this->span_start = $input_data['datetime_span_start'];
            $datetime = explode(" ", $this->span_start);
            $this->date = $datetime[0];
            $this->start_time = substr($datetime[1], 0, -3);
            $hours_minutes = explode(":", $this->start_time);
            $this->start_time_hour = $hours_minutes[0];
            $this->start_time_minutes = $hours_minutes[1];
        }

        if ( isset( $input_data['datetime_span_end'] ) ) {
            $this->span_end = $input_data['datetime_span_end'];
            $datetime = explode(" ", $this->span_end);
            $this->end_time = substr($datetime[1], 0, -3);
        }

        if ( isset( $input_data['canceled'] ) )
            $this->canceled = (int) $input_data['canceled'];
        if ( isset( $input_data['day_of_week'] ) )
            $this->day_of_week = (int) $input_data['day_of_week'];
        if ( isset( $input_data['training_course_id'] ) )
            $this->course_id = (int) $input_data['training_course_id'];
    }


    public static function overlapping_exists($date, $start_time, $end_time) {
        $dbh = DatabaseConnection::getInstance();
        $span_start = $date . " " . $start_time . ":00";
        $span_end = $date . " " . $end_time . ":00";

        $sql = "SELECT * FROM datetime_span WHERE datetime_span_start < :span_end AND
          datetime_span_end > :span_start";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':span_start', $span_start , PDO::PARAM_STR);
        $stmt->bindParam(':span_end', $span_end, PDO::PARAM_STR);

        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function return_overlappings($date, $start_time, $end_time) {
        $dbh = DatabaseConnection::getInstance();
        $span_start = $date . " " . $start_time . ":00";
        $span_end = $date . " " . $end_time . ":00";

        $sql = "SELECT * FROM datetime_span WHERE datetime_span_start < :span_end AND
          datetime_span_end > :span_start";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':span_start', $span_start , PDO::PARAM_STR);
        $stmt->bindParam(':span_end', $span_end, PDO::PARAM_STR);


        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $span = new DatetimeSpan($row);
                $list[] = $span;
            }
            return $list;
        }
        else {
            return null;
        }
    }


    public static function get_by_date($date) {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT * FROM datetime_span WHERE
          datetime_span_start BETWEEN :datetime_start AND :datetime_end ORDER BY datetime_span_start";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':datetime_start', $date . " 00:00:00", PDO::PARAM_STR);
        $stmt->bindValue(':datetime_end', $date . " 23:59:59", PDO::PARAM_STR);


        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $span = new DatetimeSpan($row);
                $span->training_course = TrainingCourse::getCourseById($span->course_id);
                $list[] = $span;
            }
            return $list;
        }
        else {
            return null;
        }
    }

    public static function get_by_datetime_and_course($datetime, $course_id) {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT * FROM datetime_span WHERE
          datetime_span_start = :datetime AND training_course_id = :course_id LIMIT 1";

        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':datetime', $datetime, PDO::PARAM_STR);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $span = new DatetimeSpan($stmt->fetch(PDO::FETCH_ASSOC));
            $span->training_course = TrainingCourse::getCourseById($span->course_id);
            return $span;
        }
        else {
            return null;
        }
    }


    public function description_string() {
        list($year, $month, $day) = explode("-", $this->date);

        $description = sprintf("%02s. %s %04s. (%s) od %s sati", $day, Calendar::cro_month_label_genitive($month),
            $year, Calendar::cro_weekday_label(date("w", strtotime($this->date))), $this->start_time);

        return $description;
    }
}