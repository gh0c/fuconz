<?php
namespace app\model\Messages;

use \app\model\User\User;
use \app\model\Admin\Admin;
use \app\model\Reservation\Reservation;
use \app\model\Reservation\DatetimeSpan;
use \app\model\Reservation\TrainingCourse;
use \app\model\Reservation\Prereservation;



class Logger
{
    public static function logNewUserRegistration($user)
    {
        // Messages
        foreach(Admin::getAdmins() as $admin) {
            $msg = "<span>" . $user->ssbos("Registrirana je nova članica", "Registriran je novi član") . " {$user->username}</span><br>" .
                "<span>{$user->full_name()}</span><br>" .
                "<span>{$user->email}</span>";
            Message::createNew("admin", $admin->id, $msg);
        }


        // Action logs
        $msg = $user->ssbos("Registrirana je nova članica", "Registriran je novi član") . "{$user->username} ({$user->full_name()})";
        $receivers = array();
        $receivers[] = array("id" => $user->id, "type" => "user");
        ActionLog::createNew($receivers, $msg, "administration");


        // User logs
        UserLog::createNew($user->id, "Registrirani ste kao korisnik članskih stranica", "administration");
    }


    public static function logClassicUserLogin($user)
    {
        // Messages

        // Action logs

        // User logs
        UserLog::createNew($user->id, "Prijava na članske stranice", "access");
    }


    public static function logUserPasswordChange($user)
    {
        // Messages

        // Action logs

        // User logs
        UserLog::createNew($user->id, "Promjena lozinke", "password");
    }

    public static function logUserAvatarChange($user)
    {
        // Messages

        // Action logs

        // User logs
        UserLog::createNew($user->id, "Promjena avatara", "profile");
    }

    public static function logUserProfileDataChange($user)
    {
        // Messages

        // Action logs

        // User logs
        UserLog::createNew($user->id, "Promjena osobnih podataka", "profile");
    }


    public static function logClassicBooking($user, $selected_spans)
    {
        $exit_msg = "Uspješna rezervacija!\nRezervacija termina: \n";

        $admin_msg = "<span>{$user->username} je " . $user->ssbos("rezervirala", "rezervirao") . " termine:</span><br><ul>";
        $action_log_msg = "<span>{$user->username} ({$user->full_name()} " . $user->ssbos("rezervirala", "rezervirao") . " je termine" .
            " (" . (string)(sizeof($selected_spans)) . "):<br><ul>";
        $user_log_msg = "Rezervirali ste termine: <br><ul>";

        //action logs
        $receivers = array();
        $receivers[] = array("id" => $user->id, "type" => "user");
        $datetimes = array();


        for ($i = 0; $i < sizeof($selected_spans); $i++) {
            $selected_span = $selected_spans[$i];
            $date_time_courseId = explode(" ", $selected_span);
            $s_date = $date_time_courseId[0];
            $s_time = $date_time_courseId[1];
            $s_course_id = $date_time_courseId[2];
            list($s_hours, $s_minutes) = explode("-", $s_time);

            $datetime_span = DatetimeSpan::get_by_datetime_and_course($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                $s_course_id);
            $datetimes[] = array("id" => $datetime_span->id);

            $training_course = TrainingCourse::getCourseById($s_course_id);

            $admin_msg .= "<li>" . $datetime_span->description_string() . "<br>" . $training_course->title . "</li>";
            $exit_msg .=  $datetime_span->description_string() . "\n";
            $action_log_msg .= "<li>" . $datetime_span->description_string() . "</li>";
            $user_log_msg .= "<li>" . $datetime_span->description_string() . "</li>";

        }

        $admin_msg .= "</ul>";
        $action_log_msg .= "</ul>";
        $user_log_msg .= "</ul>";

        // Messages
        foreach(Admin::getAdmins() as $admin) {
            Message::createNew("admin", $admin->id, $admin_msg);
        }

        // Action logs
        ActionLog::createNew($receivers, $action_log_msg, "reservations", $datetimes);

        // User logs
        UserLog::createNew($user->id, $user_log_msg, "reservations");

        return $exit_msg;

    }


    public static function logBookingWithPrereservation($user, $selected_available_spans, $selected_full_spans)
    {
        if(sizeof($selected_available_spans) > 0 && sizeof($selected_full_spans) > 0) {
            // both reservation(s) and prereservation(s)
            $exit_msg = "Uspješna rezervacija i predbilježba termina!\nRezervacija termina: \n";

            $admin_msg = "<span>{$user->username} je " . $user->ssbos("rezervirala", "rezervirao") . " termine:</span><br><ul>";
            $action_log_msg = "<span>{$user->username} ({$user->full_name()} " . $user->ssbos("rezervirala", "rezervirao") . " je termine" .
                " (" . (string)(sizeof($selected_available_spans)) . "):<br><ul>";
            $user_log_msg = "Rezervirali ste termine: <br><ul>";

            //action logs
            $receivers = array();
            $receivers[] = array("id" => $user->id, "type" => "user");
            $datetimes = array();


            for ($i = 0; $i < sizeof($selected_available_spans); $i++) {
                $selected_span = $selected_available_spans[$i];
                $date_time_courseId = explode(" ", $selected_span);
                $s_date = $date_time_courseId[0];
                $s_time = $date_time_courseId[1];
                $s_course_id = $date_time_courseId[2];
                list($s_hours, $s_minutes) = explode("-", $s_time);

                $datetime_span = DatetimeSpan::get_by_datetime_and_course($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                    $s_course_id);
                $datetimes[] = array("id" => $datetime_span->id);

                $training_course = TrainingCourse::getCourseById($s_course_id);

                $admin_msg .= "<li>" . $datetime_span->description_string() . "<br>" . $training_course->title . "</li>";
                $exit_msg .=  $datetime_span->description_string() . "\n";
                $action_log_msg .= "<li>" . $datetime_span->description_string() . "</li>";
                $user_log_msg .= "<li>" . $datetime_span->description_string() . "</li>";

            }

            $admin_msg .= "</ul>";
            $action_log_msg .= "</ul>";
            $user_log_msg .= "</ul>";

            $exit_msg .= "\nPredbilježba termina: \n";

            $admin_msg .= "<span>{$user->username} se " . $user->ssbos("predbilježila", "predbilježio") . " za termine:</span><br><ul>";
            $action_log_msg .= "<span>{$user->username} ({$user->full_name()} " . $user->ssbos("predbilježila", "predbilježio") . " se za termine" .
                " (" . (string)(sizeof($selected_full_spans)) . "):<br><ul>";
            $user_log_msg .= "Predbilježili ste se za termine: <br><ul>";



            for ($i = 0; $i < sizeof($selected_full_spans); $i++) {
                $selected_span = $selected_full_spans[$i];
                $date_time_courseId = explode(" ", $selected_span);
                $s_date = $date_time_courseId[0];
                $s_time = $date_time_courseId[1];
                $s_course_id = $date_time_courseId[2];
                list($s_hours, $s_minutes) = explode("-", $s_time);

                $datetime_span = DatetimeSpan::get_by_datetime_and_course($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                    $s_course_id);
                $datetimes[] = array("id" => $datetime_span->id);

                $training_course = TrainingCourse::getCourseById($s_course_id);

                $admin_msg .= "<li>" . $datetime_span->description_string() . "<br>" . $training_course->title . "</li>";
                $exit_msg .=  $datetime_span->description_string() . "\n";
                $action_log_msg .= "<li>" . $datetime_span->description_string() . "</li>";
                $user_log_msg .= "<li>" . $datetime_span->description_string() . "</li>";

            }

            $admin_msg .= "</ul>";
            $action_log_msg .= "</ul>";
            $user_log_msg .= "</ul>";


            // Messages
            foreach(Admin::getAdmins() as $admin) {
                Message::createNew("admin", $admin->id, $admin_msg);
            }

            // Action logs
            ActionLog::createNew($receivers, $action_log_msg, "reservations", $datetimes);

            // User logs
            UserLog::createNew($user->id, $user_log_msg, "reservations");

            return $exit_msg;

        } else if(sizeof($selected_available_spans) > 0 && !(sizeof($selected_full_spans) > 0)) {

            // only reservations (Shouldn't happen, logClassicBooking())
            self::logClassicBooking($user, $selected_available_spans);

        } else if (!(sizeof($selected_available_spans) > 0) && sizeof($selected_full_spans) > 0) {

            // only prereservations
            $exit_msg = "Uspješna predbilježba!\nPredbilježba termina: \n";

            $admin_msg = "<span>{$user->username} se " . $user->ssbos("predbilježila", "predbilježio") . " za termine:</span><br><ul>";
            $action_log_msg = "<span>{$user->username} ({$user->full_name()} " . $user->ssbos("predbilježila", "predbilježio") . " se za termine" .
                " (" . (string)(sizeof($selected_full_spans)) . "):<br><ul>";
            $user_log_msg = "Predbilježili ste se za termine: <br><ul>";

            //action logs
            $receivers = array();
            $receivers[] = array("id" => $user->id, "type" => "user");
            $datetimes = array();


            for ($i = 0; $i < sizeof($selected_full_spans); $i++) {
                $selected_span = $selected_full_spans[$i];
                $date_time_courseId = explode(" ", $selected_span);
                $s_date = $date_time_courseId[0];
                $s_time = $date_time_courseId[1];
                $s_course_id = $date_time_courseId[2];
                list($s_hours, $s_minutes) = explode("-", $s_time);

                $datetime_span = DatetimeSpan::get_by_datetime_and_course($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                    $s_course_id);
                $datetimes[] = array("id" => $datetime_span->id);

                $training_course = TrainingCourse::getCourseById($s_course_id);

                $admin_msg .= "<li>" . $datetime_span->description_string() . "<br>" . $training_course->title . "</li>";
                $exit_msg .=  $datetime_span->description_string() . "\n";
                $action_log_msg .= "<li>" . $datetime_span->description_string() . "</li>";
                $user_log_msg .= "<li>" . $datetime_span->description_string() . "</li>";

            }

            $admin_msg .= "</ul>";
            $action_log_msg .= "</ul>";
            $user_log_msg .= "</ul>";

            // Messages
            foreach(Admin::getAdmins() as $admin) {
                Message::createNew("admin", $admin->id, $admin_msg);
            }

            // Action logs
            ActionLog::createNew($receivers, $action_log_msg, "reservations", $datetimes);

            // User logs
            UserLog::createNew($user->id, $user_log_msg, "reservations");

            return $exit_msg;
        } else {
            $exit_msg = "Zabilježena je nepoznata akcija. Molim provjeri u kalendaru jesu li rezervacije uspjele!";
            return $exit_msg;
        }


    }
}


?>