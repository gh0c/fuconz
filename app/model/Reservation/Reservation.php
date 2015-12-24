<?php

namespace app\model\Reservation;

use \app\helpers\Calendar;
use \app\helpers\Configuration;
use app\model\Database\DatabaseConnection;
use \app\model\User\User;
use \PDO;
use \PDOException;


class Reservation {

    public $id = null;

    public $type = "reservation";

    public $datetime_span = null;
    public $datetime_span_id = null;

    public $training_course_id = null;

    public $created_at = null;

    public $canceled = null;

    public $user = null;
    public $user_id = null;

    public $triggered = null;

    function __construct($input_data = array()) {

        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];
        if ( isset( $input_data['canceled'] ) )
            $this->canceled = (int) $input_data['canceled'];

        if ( isset( $input_data['training_course_id'] ) )
            $this->training_course_id = (int) $input_data['training_course_id'];
        if ( isset( $input_data['datetime_span_id'] ) ) {
            $this->datetime_span_id = (int) $input_data['datetime_span_id'];
            $this->datetime_span = DatetimeSpan::getById($this->datetime_span_id);
        }
        if ( isset( $input_data['user_id'] ) ) {
            $this->user_id = (int) $input_data['user_id'];
            $this->user = User::getUserById($this->user_id);
        }

        if ( isset( $input_data['triggered'] ) )
            $this->triggered = (int)$input_data['triggered'];

        if ( isset( $input_data['created_at'] ) )
            $this->created_at = $input_data['created_at'];

    }


    public function menuDisplayString()
    {
        if($this->canceled == 1) {
            return "Otkazana rezervacija";
        } else if ($this->canceled == 2) {
            return "Potvrđena rezervacija";
        } else {
            return "Rezervacija zasad nepoznatog statusa";
        }
    }

    public function myMenuDisplayStringHistory()
    {
        if($this->canceled == 1) {
            return "Imali ste otkazanu rezervaciju";
        } else if ($this->canceled == 2) {
            return "Imali ste potvrđenu rezervaciju";
        } else {
            return "Imali ste rezervaciju nepoznatog statusa";
        }
    }


    public static function getById($id)
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT * FROM reservation WHERE id = :id LIMIT 1";

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $reservation = new Reservation($stmt->fetch(PDO::FETCH_ASSOC));
                return $reservation;
            }
            else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }


    public function delete() {
        $dbh = DatabaseConnection::getInstance();
        $sql = "DELETE FROM reservation WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            return null;
        }
    }


    public static function createNew($course, $datetime_span, $user, $training_type = 1, $triggered = 0)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO reservation (user_id, datetime_span_id, training_course_id, training_type_id, created_at, triggered)
            VALUES (:user_id, :datetime_span_id, :training_course_id, :training_type_id, :created_at, :triggered)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);

        $stmt->bindParam(':user_id', $user, PDO::PARAM_INT);
        $stmt->bindParam(':datetime_span_id', $datetime_span, PDO::PARAM_INT);
        $stmt->bindParam(':training_course_id', $course, PDO::PARAM_INT);
        $stmt->bindParam(':training_type_id', $training_type, PDO::PARAM_INT);
        $stmt->bindParam(':triggered', $triggered, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            return null;
        }
    }


    public static function validateNewSetOfReservations($p_selected_spans, $user) {
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
                        $validation_result["errors"] = "Terminu koji ste odabrali: " . $s_date . " " . $s_time . " je prošlo vrijeme za rezervaciju:\n" .
                            "Ponovite rezervaciju!";
                        return $validation_result;

                    } else {
                        if(self::already_exists($training_course->id, $datetime_span->id, $user->id)) {
                            $validation_result["errors"] = "Već imate identičnu rezervaciju: " . $datetime_span->descriptionString() . "\n" .
                                "Nemoguće je dvaput rezervirati isto";
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

        $prereservations_necessary = false;
//
        $pre_reservations_status_label = "";
//
        $available_datetime_spans = array();
        $available_datetime_spans_description_labels = array();
        $available_datetime_spans_description_labels_midi = array();

        $available_datetime_spans_availability_labels = array();

        $full_datetime_spans = array();
        $full_datetime_spans_description_labels = array();
        $full_datetime_spans_description_labels_midi = array();
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

            $number_of_existing_reservations = self::numberOfReservationsForDatetimeAndCourse($datetime_span->id, $training_course->id);
            if($number_of_existing_reservations < $training_course->capacity) {
                $available_datetime_spans[] = $selected_span;
                $available_datetime_spans_description_labels[] = $datetime_span->descriptionString();
                $available_datetime_spans_description_labels_midi[] = $datetime_span->descriptionStringMid();

                $available_datetime_spans_availability_labels[] = "" . $number_of_existing_reservations . "/" . $training_course->capacity;
            } else {
                $full_datetime_spans[] = $selected_span;
                $full_datetime_spans_description_labels[] = $datetime_span->descriptionString();
                $full_datetime_spans_description_labels_midi[] = $datetime_span->descriptionStringMid();
                $full_datetime_spans_availability_labels[] = "" . $number_of_existing_reservations . "/" . $training_course->capacity;
                $prereservations_necessary = true;
                $pre_reservations_status_label .= sprintf("Popunjen je termin %s. - Popunjenost: %d/%d \n",
                    $datetime_span->descriptionString(), $number_of_existing_reservations, $training_course->capacity);
            }
        }

        if(!$prereservations_necessary) {
            $validation_result["validated"] = true;
            $validation_result["prereservations"] = false;
            $validation_result["available_datetime_spans"] = $available_datetime_spans;
            $validation_result["available_datetime_spans_description_labels"] = $available_datetime_spans_description_labels;
            $validation_result["available_datetime_spans_description_labels_midi"] = $available_datetime_spans_description_labels_midi;
            $validation_result["available_datetime_spans_availibility_labels"] = $available_datetime_spans_availability_labels;
            $validation_result["full_datetime_spans"] = $full_datetime_spans;
            $validation_result["full_datetime_spans_description_labels"] = $full_datetime_spans_description_labels;
            $validation_result["full_datetime_spans_description_labels_midi"] = $full_datetime_spans_description_labels_midi;
            $validation_result["full_datetime_spans_availibility_labels"] = $full_datetime_spans_availability_labels;
            return $validation_result;
        } else {
            $validation_result["validated"] = true;
            $validation_result["prereservations"] = true;
            $validation_result["prereservations_status_label"] = $pre_reservations_status_label;
            $validation_result["available_datetime_spans"] = $available_datetime_spans;
            $validation_result["available_datetime_spans_description_labels"] = $available_datetime_spans_description_labels;
            $validation_result["available_datetime_spans_description_labels_midi"] = $available_datetime_spans_description_labels_midi;
            $validation_result["available_datetime_spans_availibility_labels"] = $available_datetime_spans_availability_labels;
            $validation_result["full_datetime_spans"] = $full_datetime_spans;
            $validation_result["full_datetime_spans_description_labels"] = $full_datetime_spans_description_labels;
            $validation_result["full_datetime_spans_description_labels_midi"] = $full_datetime_spans_description_labels_midi;
            $validation_result["full_datetime_spans_availibility_labels"] = $full_datetime_spans_availability_labels;
            return $validation_result;
        }
    }


    public static function createNewSet($p_selected_spans, $user)
    {
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

                $sql = "INSERT INTO reservation (user_id, datetime_span_id, training_course_id, training_type_id, created_at)
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


    public static function already_exists($course, $datetime_span, $user) {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT COUNT(*) AS number FROM reservation WHERE
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

        $sql = "SELECT COUNT(*) AS number FROM reservation WHERE user_id = :id";
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


    public static function numberOfReservationsForDatetimeAndCourse($datetime_span, $course) {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT COUNT(*) AS number FROM reservation WHERE
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

        $sql = "SELECT * FROM reservation WHERE
          datetime_span_id = :datetime_span_id AND user_id = :user_id";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':datetime_span_id', (int)$datetime_span_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return new Reservation($row);
        }
        else {
            return null;
        }
    }



    public static function getByDatetimeSpan($datetime_span_id, $order_by = "created_at ASC")
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT * FROM reservation WHERE
          datetime_span_id = :datetime_span_id ORDER BY {$order_by}";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':datetime_span_id', (int)$datetime_span_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $booking = new Reservation($row);
                $list[] = $booking;
            }
            return $list;
        }
        else {
            return array();
        }
    }



    public static function getNumberByDatetimeSpan($datetime_span_id)
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT COUNT(*) AS number FROM reservation WHERE
          datetime_span_id = :datetime_span_id";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':datetime_span_id', (int)$datetime_span_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row["number"];
        }
        else {
            return null;
        }
    }


    public static function getByUser($user_id, $order_by = "datetime_span.datetime_span_start")
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT reservation.* FROM reservation JOIN datetime_span
          ON reservation.datetime_span_id = datetime_span.id WHERE
          user_id = :user_id ORDER BY {$order_by}";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $booking = new Reservation($row);
                $list[] = $booking;
            }
            return $list;
        }
        else {
            return array();
        }
    }


    public static function getByUserLimited($user_id, $limit = 1000, $order_by = "datetime_span.datetime_span_start")
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT reservation.* FROM reservation JOIN datetime_span
          ON reservation.datetime_span_id = datetime_span.id WHERE
          user_id = :user_id ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $booking = new Reservation($row);
                $list[] = $booking;
            }
            return $list;
        }
        else {
            return array();
        }
    }


    public function setFlagCanceled($flag)
    {
        $this->canceled = (int)$flag;
        $dbh = DatabaseConnection::getInstance();

        $sql = "UPDATE reservation set canceled = :canceled WHERE id = :id";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':canceled', (int)$flag, PDO::PARAM_INT);
        $stmt->bindValue(':id', (int)$this->id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            return null;
        }
    }


}