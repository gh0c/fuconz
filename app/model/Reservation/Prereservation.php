<?php

namespace app\model\Reservation;

use \app\helpers\Calendar;
use \app\helpers\Configuration;
use app\model\Database\DatabaseConnection;
use MatthiasMullie\Minify\Exception;
use \PDO;
use \PDOException;


class Prereservation {

    public $id = null;

    public $type = "prereservation";

    public $datetime_span = null;
    public $datetime_span_id = null;

    public $training_course_id = null;

    public $created_at = null;

    public $canceled = null;
    public $activated = null;


    public $user = null;
    public $user_id = null;


    function __construct($input_data = array()) {

        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];
        if ( isset( $input_data['canceled'] ) )
            $this->canceled = (int) $input_data['canceled'];
        if ( isset( $input_data['activated'] ) )
            $this->activated = (int) $input_data['activated'];

        if ( isset( $input_data['training_course_id'] ) )
            $this->course_id = (int) $input_data['training_course_id'];
        if ( isset( $input_data['datetime_span_id'] ) ) {
            $this->datetime_span_id = (int) $input_data['datetime_span_id'];
            $this->datetime_span = DatetimeSpan::getById($this->datetime_span_id);
        }
        if ( isset( $input_data['user_id'] ) )
            $this->user_id = (int) $input_data['user_id'];

        if ( isset( $input_data['created_at'] ) )
            $this->created_at = $input_data['created_at'];

    }


    public static function create_new($course, $datetime_span, $user, $training_type = 1) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO pre_reservation (user_id, datetime_span_id, training_course_id, training_type_id, created_at)
            VALUES (:user_id, :datetime_span_id, :training_course_id, :training_type_id, :created_at)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);

        $stmt->bindParam(':user_id', $user, PDO::PARAM_INT);
        $stmt->bindParam(':datetime_span_id', $datetime_span, PDO::PARAM_INT);
        $stmt->bindParam(':training_course_id', $course, PDO::PARAM_INT);
        $stmt->bindParam(':training_type_id', $training_type, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }


    public static function validateNewSetOfPrereservations($p_selected_spans, $user) {
        $validation_result = array();
        $validation_result["validated"] = false;

        if(!isset($p_selected_spans) || sizeof($p_selected_spans) < 1 ||
            (sizeof($p_selected_spans) == 1 && $p_selected_spans[0] == "-")) {
            $validation_result["errors"] = "Termini nisu niti odabrani";
            return $validation_result;
        } else {
            for ($i = 0; $i < sizeof($p_selected_spans); $i++) {
                $selected_span = $p_selected_spans[$i];
                if(!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}-\d{2} \d+$/',$selected_span)) {
                    $validation_result["errors"] = "Neki od termina nije u dozvoljenom formatu:\n" . $selected_span;
                    return $validation_result;
                } else {
                    $date_time_courseId = explode(" ", $selected_span);
                    $s_date = $date_time_courseId[0];
                    $s_time = $date_time_courseId[1];
                    $s_course_id = $date_time_courseId[2];

                    list($s_hours, $s_minutes) = explode("-", $s_time);

                    if(!Calendar::validate_date($s_date . " " . $s_hours . ":" . $s_minutes, "Y-m-d H:i")) {
                        $validation_result["errors"] = "Odabrano vrijeme nije ispravno:\n" . $s_date . " " . $s_time;
                        return $validation_result;
                    } else if (strtotime($s_date . " " . $s_hours . ":" . $s_minutes) < strtotime(date("Y-m-d H:i"))) {
                        $validation_result["errors"] = "Termin koji ste odabrali: " . $s_date . " " . $s_time . " je prošao:\n" .
                            "Ponovite rezervaciju!";
                        return $validation_result;

                    } else if (!($datetime_span = DatetimeSpan::getByDatetimeAndCourse($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                        $s_course_id))) {
                        $validation_result["errors"] = "Termin koji ste odabrali: " . $s_date . " " . $s_time . " nema " .
                            "odgovarajući zapis u bazi podataka uz pripadnu vrijednost identifikatora termina i ovog vremena...";
                        return $validation_result;
                    } else if (!$training_course = TrainingCourse::getCourseById($s_course_id)) {
                        $validation_result["errors"] = "Termin koji ste odabrali: " . $s_date . " " . $s_time . " nema " .
                            "odgovarajući zapis u bazi podataka uz pripadnu vrijednost identifikatora termina";
                        return $validation_result;
                    } else if (strtotime($s_date . " " . $s_hours . ":" . $s_minutes) < (strtotime(date("Y-m-d H:i")) + $training_course->reservation_time)) {
                        $validation_result["errors"] = "Terminu koji ste odabrali: " . $s_date . " " . $s_time . " je prošlo vrijeme za predbilježbu:\n" .
                            "Ponovite rezervaciju!";
                        return $validation_result;

                    } else {
                        if(self::alreadyExists($training_course->id, $datetime_span->id, $user->id)) {
                            $validation_result["errors"] = "Već imate identičnu predbilježbu: " . $datetime_span->description_string() . "\n" .
                                "Nemoguće je dvaput predbilježiti isto";
                            return $validation_result;
                        } else {
                            $validation_result["validated"] = true;
                        }
                    }
                }

            }

        }
        return $validation_result;
    }


    public static function checkAvailabilityBookings($p_selected_spans)
    {
        $validation_result = array();
        $validation_result["validated"] = false;

        $prereservations_unnecessary = false;
//
        $pre_reservations_status_label = "";
//
        $available_datetime_spans = array();
        $available_datetime_spans_description_labels = array();
        $available_datetime_spans_availability_labels = array();

        $full_datetime_spans = array();
        $full_datetime_spans_description_labels = array();
        $full_datetime_spans_availability_labels = array();

        for ($i = 0; $i < sizeof($p_selected_spans); $i++) {
            $selected_span = $p_selected_spans[$i];
            $date_time_courseId = explode(" ", $selected_span);
            $s_date = $date_time_courseId[0];
            $s_time = $date_time_courseId[1];
            $s_course_id = $date_time_courseId[2];
            list($s_hours, $s_minutes) = explode("-", $s_time);

            $datetime_span = DatetimeSpan::getByDatetimeAndCourse($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                $s_course_id);
            $training_course = TrainingCourse::getCourseById($s_course_id);

            $number_of_existing_reservations = Reservation::number_of_reservations_for_datetime_and_course($training_course->id, $datetime_span->id);
            if($number_of_existing_reservations < $training_course->capacity) {
                $available_datetime_spans[] = $selected_span;
                $available_datetime_spans_description_labels[] = $datetime_span->description_string();
                $available_datetime_spans_availability_labels[] = "" . $number_of_existing_reservations . "/" . $training_course->capacity;
                $prereservations_unnecessary = true;
                $pre_reservations_status_label .= sprintf("Više nije popunjen termin %s. Popunjenost %d/%d \n",
                    $datetime_span->description_string(), $number_of_existing_reservations, $training_course->capacity);
            } else {
                $full_datetime_spans[] = $selected_span;
                $full_datetime_spans_description_labels[] = $datetime_span->description_string();
                $full_datetime_spans_availability_labels[] = "" . $number_of_existing_reservations . "/" . $training_course->capacity;
            }
        }

        if(!$prereservations_unnecessary) {
            $validation_result["validated"] = true;
            $validation_result["reservations"] = false;
            $validation_result["available_datetime_spans"] = $available_datetime_spans;
            $validation_result["available_datetime_spans_description_labels"] = $available_datetime_spans_description_labels;
            $validation_result["available_datetime_spans_availibility_labels"] = $available_datetime_spans_availability_labels;
            $validation_result["full_datetime_spans"] = $full_datetime_spans;
            $validation_result["full_datetime_spans_description_labels"] = $full_datetime_spans_description_labels;
            $validation_result["full_datetime_spans_availibility_labels"] = $full_datetime_spans_availability_labels;
            return $validation_result;
        } else {
            $validation_result["validated"] = true;
            $validation_result["reservations"] = true;
            $validation_result["reservations_status_label"] = $pre_reservations_status_label;
            $validation_result["available_datetime_spans"] = $available_datetime_spans;
            $validation_result["available_datetime_spans_description_labels"] = $available_datetime_spans_description_labels;
            $validation_result["available_datetime_spans_availibility_labels"] = $available_datetime_spans_availability_labels;
            $validation_result["full_datetime_spans"] = $full_datetime_spans;
            $validation_result["full_datetime_spans_description_labels"] = $full_datetime_spans_description_labels;
            $validation_result["full_datetime_spans_availibility_labels"] = $full_datetime_spans_availability_labels;
            return $validation_result;
        }
    }


    public static function createNewSet($p_selected_spans, $user) {
        $dbh = DatabaseConnection::getInstance();

        try {
            $dbh->beginTransaction();

            for ($i = 0; $i < sizeof($p_selected_spans); $i++) {
                $selected_span = $p_selected_spans[$i];

                $date_time_courseId = explode(" ", $selected_span);
                $s_date = $date_time_courseId[0];
                $s_time = $date_time_courseId[1];
                $s_course_id = $date_time_courseId[2];
                list($s_hours, $s_minutes) = explode("-", $s_time);

                $datetime_span = DatetimeSpan::getByDatetimeAndCourse($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                    $s_course_id);
                $training_course = TrainingCourse::getCourseById($s_course_id);

                $sql = "INSERT INTO pre_reservation (user_id, datetime_span_id, training_course_id, training_type_id, created_at)
                  VALUES (:user_id, :datetime_span_id, :training_course_id, :training_type_id, :created_at)";
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);

                $stmt->bindParam(':user_id', $user->id, PDO::PARAM_INT);
                $stmt->bindParam(':datetime_span_id', $datetime_span->id, PDO::PARAM_INT);
                $stmt->bindParam(':training_course_id', $training_course->id, PDO::PARAM_INT);
                $stmt->bindValue(':training_type_id', 1, PDO::PARAM_INT); // no training type functional yet

                $stmt->execute();
            }
            $dbh->commit();
            return true;
        } catch(\Exception $e) {
            $dbh->rollback();
            return false;
        }



    }


    public static function alreadyExists($course, $datetime_span, $user)
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT COUNT(*) AS number FROM pre_reservation WHERE
          datetime_span_id = :datetime_span_id AND training_course_id = :training_course_id AND
          user_id = :user_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':datetime_span_id', $datetime_span, PDO::PARAM_INT);
        $stmt->bindParam(':training_course_id', $course, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row["number"] > 0) {
                return true;
            } else {
                return false;
            }
        }
        else {
            return null;
        }
    }


    public static function existsForUser($user_id)
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT COUNT(*) AS number FROM prereservation WHERE user_id = :id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row["number"] > 0) {
                return true;
            } else {
                return false;
            }
        }
        else {
            return null;
        }
    }



    public static function getByUser($user_id, $order_by = "datetime_span.datetime_span_start")
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT pre_reservation.* FROM pre_reservation JOIN datetime_span
          ON pre_reservation.datetime_span_id = datetime_span.id WHERE
          user_id = :user_id ORDER BY {$order_by}";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $booking = new Prereservation($row);
                $list[] = $booking;
            }
            return $list;
        }
        else {
            return array();
        }
    }



    public static function numberOfPrereservationsForDatetimeAndCourse($course, $datetime_span)
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT COUNT(*) AS number FROM pre_reservation WHERE
          datetime_span_id = :datetime_span_id AND training_course_id = :training_course_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':datetime_span_id', $datetime_span, PDO::PARAM_INT);
        $stmt->bindParam(':training_course_id', $course, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row["number"];
        }
        else {
            return null;
        }
    }


    public static function getByUserAndDatetimeSpan($user_id, $datetime_span_id)
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT * FROM pre_reservation WHERE
          datetime_span_id = :datetime_span_id AND user_id = :user_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':datetime_span_id', $datetime_span_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return new Prereservation($row);
        }
        else {
            return null;
        }
    }


    public function setFlagActivated($flag)
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "UPDATE pre_reservation set activated = :activated WHERE id = :id";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':activated', (int)$flag, PDO::PARAM_INT);
        $stmt->bindValue(':id', (int)$this->id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            return null;
        }
    }


}