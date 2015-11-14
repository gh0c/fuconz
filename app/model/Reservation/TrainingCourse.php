<?php
namespace app\model\Reservation;

use app\helpers\Calendar;
use app\helpers\Configuration;
use app\model\Database\DatabaseConnection;
use \PDO;
use \PDOException;


class TrainingCourse {
    public $id = null;

    public $title = null;

    public $admin_id = null;

    public $created_at;
    public $updated_at;

    public $repeating_from = null;
    public $repeating_until = null;
    public $start_time = null;
    public $end_time = null;
    public $repeating = null;
    public $repeating_interval = null;
    public $repeating_frequency = null;

    public $description = null;

    public $capacity = null;
    public $min_reservations = null;
    public $reservation_time = 86400;
    public $training_type_id = null;



    function __construct($input_data = array()) {
        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];
        if ( isset( $input_data['title'] ) )
            $this->title = $input_data['title'];

        if ( isset( $input_data['start_time'] ) )
            $this->start_time = $input_data['start_time'];
        if ( isset( $input_data['end_time'] ) )
            $this->end_time = $input_data['end_time'];
        if ( isset( $input_data['repeating_from'] ) )
            $this->repeating_from = $input_data['repeating_from'] ;
        if ( isset( $input_data['$repeating_until'] ) )
            $this->$repeating_until = $input_data['$repeating_until'] ;

        if ( isset( $input_data['created_at'] ) )
            $this->created_at = $input_data['created_at'];
        if ( isset( $input_data['updated_at'] ) )
            $this->updated_at = $input_data['updated_at'];

        if ( isset( $input_data['repeating_interval'] ) ) {
            $this->repeating_interval = $input_data['repeating_interval'];
            if($this->repeating_interval == Configuration::read('repeating.interval.none')) {
                $this->repeating = false;
            } else {
                $this->repeating = true;
            }
        }
        if ( isset( $input_data['repeating_frequency'] ) )
            $this->repeating_frequency = $input_data['repeating_frequency'];

        if ( isset( $input_data['admin_id'] ) )
            $this->admin_id = (int)$input_data['admin_id'];


        if ( isset( $input_data['description'] ) )
            $this->description = $input_data['description'];
        if ( isset( $input_data['training_type_id'] ) )
            $this->training_type_id = (int)$input_data['training_type_id'];

        if ( isset( $input_data['capacity'] ) )
            $this->capacity = (int)$input_data['capacity'];
        if ( isset( $input_data['min_reservations'] ) )
            $this->min_reservations = (int)$input_data['min_reservations'];
        if ( isset( $input_data['reservation_time'] ) )
            $this->reservation_time = (int)$input_data['reservation_time'];
    }

    public function populate_attributes_from_input($data)
    {
        $this->__construct($data);
    }




    public static function createNew($title, $start_time, $end_time, $repeating_from, $training_type_id, $admin_id,
        $capacity, $min_reservations, $repeating = false, $repeating_interval = "none",
        $repeating_frequency = null, $repeating_until = null, $reservation_time = 86400, $description = null) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO training_course (
            title, start_time, end_time, repeating_from, repeating_until, admin_id, reservation_time,
            capacity, min_reservations, repeating_interval, repeating_frequency, updated_at, training_type_id, description)
            VALUES (:title, :start_time, :end_time, :repeating_from, :repeating_until, :admin_id, :reservation_time,
            :capacity, :min_reservations, :repeating_interval, :repeating_frequency, :updated_at, :training_type, :description)";

        $stmt = $dbh->prepare($sql);


        try {
            $dbh->beginTransaction();
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':start_time', $start_time, PDO::PARAM_STR);
            $stmt->bindParam(':end_time', $end_time, PDO::PARAM_STR);
            $stmt->bindParam(':repeating_from', $repeating_from, PDO::PARAM_STR);
            $stmt->bindParam(':repeating_until', $repeating_until, PDO::PARAM_STR);
            $stmt->bindParam(':repeating_interval', $repeating_interval, PDO::PARAM_STR);
            $stmt->bindParam(':repeating_frequency', $repeating_frequency, PDO::PARAM_STR);
            $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
            $stmt->bindParam(':training_type', $training_type_id, PDO::PARAM_INT);
            $stmt->bindParam(':reservation_time', $reservation_time, PDO::PARAM_INT);

            $stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);
            $stmt->bindParam(':min_reservations', $min_reservations, PDO::PARAM_INT);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);


            $stmt->execute();

            if ($stmt->rowCount() == 1) {

                $dates = Calendar::dates_span($repeating_from, $repeating, $repeating_until,
                    $repeating_interval, $repeating_frequency);
                $course_id = $dbh->lastInsertId();

                foreach($dates as $date) {
                    $day_of_week = date("w", strtotime($date));
                    $span_start = $date . " " . $start_time . ":00";
                    $span_end = $date . " " . $end_time . ":00";

                    $sql2 = "INSERT INTO datetime_span (training_course_id, datetime_span_start,
                        datetime_span_end, day_of_week) VALUES(:course_id, :span_start,
                        :span_end, :day_of_week)";
                    $stmt = $dbh->prepare($sql2);
                    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
                    $stmt->bindParam(':day_of_week', $day_of_week, PDO::PARAM_INT);
                    $stmt->bindParam(':span_start', $span_start , PDO::PARAM_STR);
                    $stmt->bindParam(':span_end', $span_end, PDO::PARAM_STR);
                    $stmt->execute();
                }
                $dbh->commit();

                return true;
            } else {
                return false;
            }

        } catch(\Exception $e) {
            $dbh->rollback();
        }

    }


    public static function validateNew($p_title, $p_start_time, $p_end_time, $p_date_from, $training_type_id,
                                       $p_capacity, $p_min_reservations, $p_repeating, $p_repeating_interval,
                                       $p_repeating_frequency, $p_date_until, $p_reservation_time) {
        $validation_result = array();
        $validation_result["validated"] = false;

        if(!isset($p_date_from) || $p_date_from === "" || !isset($p_start_time) || $p_start_time === ""
            || !isset($p_end_time) || $p_end_time === "" || !isset($p_title) || $p_title === "") {
            $validation_result["errors"] = "Nedostaju podaci za unos! \n" .
                "Unos vremena početka i kraja termina, datuma i naziva termina su obavezni";
            return $validation_result;
        } else if (!Calendar::validate_date($p_date_from, "d.m.Y.")) {
            $validation_result["errors"] = "Neispravan datum termina: {$p_date_from}";
            return $validation_result;
        } elseif (!isset($p_capacity) || !is_numeric($p_capacity) || (int)$p_capacity <= 0 ||
            !isset($p_min_reservations) || !is_numeric($p_min_reservations) || $p_min_reservations <= 0) {
            $validation_result["errors"] = "Nedostaju podaci o kapacitetu i minimalnom broju rezervacija. \n" .
                "Moraju biti pozitivan cijeli broj";
            return $validation_result;
        } elseif (!Calendar::validate_date($p_date_from . " " . $p_start_time, "d.m.Y. H:i") ||
            !Calendar::validate_date($p_date_from . " " . $p_end_time, "d.m.Y. H:i")) {
            $validation_result["errors"] = "Neispravan format satnice termina: {$p_start_time} - {$p_end_time}";
            return $validation_result;
        } elseif (strtotime($p_date_from . " " . $p_start_time) >= strtotime($p_date_from . " " . $p_end_time)) {
            $validation_result["errors"] = "Kraj termina nije nakon njegovog početka: {$p_start_time} - {$p_end_time}";
            return $validation_result;
        }elseif (!isset($p_reservation_time) || !is_numeric($p_reservation_time) || (int)$p_reservation_time <= 0) {
            $validation_result["errors"] = "Odabrana je neispravna vrijednost vremena za rezervaciju. To mora biti pozitivan cijeli broj.";
            return $validation_result;
        }
        if(isset($p_repeating) && $p_repeating === "on") {
            $repeating = true;
        } else {
            $repeating = false;
        }

        if($repeating) {
            if(!isset($p_date_until) || $p_date_until === "") {
                $validation_result["errors"] = "Odabrano je ponavljanje termina ali ne i datum do kad";
                return $validation_result;
            } elseif (!Calendar::validate_date($p_date_until, "d.m.Y.")) {
                $validation_result["errors"] = "Neispravan datum do kad se termin ponavlja: {$p_date_until}";
                return $validation_result;
            } elseif (strtotime($p_date_from . " " . $p_end_time) >= strtotime($p_date_until . " " . $p_start_time)) {
                $validation_result["errors"] = "Datum termina je prije datuma do kad se ponavlja.";
                return $validation_result;
            } elseif (!in_array($p_repeating_interval, array(
                Configuration::read("repeating.interval.day"), Configuration::read("repeating.interval.week"),
                Configuration::read("repeating.interval.month.1")))) {
                $validation_result["errors"] = "Uz odabrano ponavljanje termina odabrana je neispravna vrijednost".
                    " intervala ponavljanja: {$p_repeating_interval}.";
                return $validation_result;
            } elseif (!isset($p_repeating_frequency) || !is_numeric($p_repeating_frequency) || (int)$p_repeating_frequency <= 0) {
                $validation_result["errors"] = "Uz odabrano ponavljanje termina odabrana je neispravna vrijednost".
                    " frekvencije ponavljanja. \nMora biti pozitivan cijeli broj.";
                return $validation_result;
            }
        }

        $dates = Calendar::dates_span(date("Y-m-d", strtotime($p_date_from)), $repeating,
            date("Y-m-d", strtotime($p_date_until)), $p_repeating_interval, $p_repeating_frequency);
        foreach($dates as $date) {
            if($overlappings = DatetimeSpan::return_overlappings($date, $p_start_time, $p_end_time)) {
                if(!isset($validation_result["errors"])) {
                    $validation_result["errors"] = "";
                }
                $validation_result["errors"] .= "Za termin " . $p_start_time ." - " . $p_end_time . " " .
                    date("d.m.Y.", strtotime($date)) . " postoje preklapanja s postojećim terminima: \n";
                foreach($overlappings as $overlap) {
                    $validation_result["errors"] .= $overlap->start_time . " - " . $overlap->end_time ." " .
                        date("d.m.Y.", strtotime($overlap->date)) . "\n";
                }
            }
        }

        if(!isset($validation_result["errors"]) || $validation_result["errors"] === "")
            $validation_result["validated"] = true;
        return $validation_result;
    }



    public function delete() {
        $dbh = DatabaseConnection::getInstance();
        $sql = "DELETE FROM training_course WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $sql = "DELETE FROM datetime_span WHERE training_course_id = :training_course_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':training_course_id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }


    public static function getCourses($limit = 1000000, $order_by = "title ASC") {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM training_course ORDER BY :order LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':order', $order_by, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user = new TrainingCourse($row);
                $list[] = $user;
            }
            return $list;
        }
        else {
            return null;
        }
    }


    public static function getCourseById($course_id)    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM training_course WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $course_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $course = new TrainingCourse($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
        return $course;
    }
}