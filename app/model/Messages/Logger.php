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
            $msg = "<span>" . $user->ssbos("Registrirana je nova članica", "Registriran je novi član") .
                " </span><span class = 'underline'>{$user->username}</span><br>" .
                "<span>{$user->full_name()}</span><br>" .
                "<span>{$user->email}</span>";
            Message::createNew("admin", $admin->id, $msg);
        }
        $msg = "<span class = 'title'>" . $user->ssbos("Dobrodošla", "Dobrodošao") . " " . $user->username . "!</span><br>" .
            "<span>Hvala na registraciji! <i class = 'fa fa-fw fa-thumbs-o-up'></i></span><br><br>" .
            "<span>Nadam se da će ove poruke poslužiti za neke osnovne obavijesti, " .
            "a možda implementiram i neku mogućnost jednostavne međusobne komunikacije...</span><br>".
            "<div class='std-item sender'>".
                "<div class = 'msg-sender-icon-holder std-icon-holder'>" .
                    "<div class = 'msg-avatar-cont std-icon-cont'>".
                        "<div class = 'thumbnail sender msg-icon std-icon no-avatar'>" .
                            "<div class='pic-cont std-pic-cont'>".
                                "<span class='v-align-helper'></span><img src='public/graphics/admin.jpg'/>".
                            "</div>".
                        "</div>".
                    "</div>".
                "</div>" .
                "<div class = 'sender-label'><span>Admin Gh0C</span></div>".
            "</div>";

        Message::createNew("user", $user->id, $msg);



        // Action logs
        $msg = "<span>" . $user->ssbos("Registrirana je nova članica", "Registriran je novi član") .
            "</span><span class = 'underline'>{$user->username}</span><span> ({$user->full_name()})</span>";
        $receivers = array();
        $receivers[] = array("id" => $user->id, "type" => "user");
        ActionLog::createNew($receivers, $msg, "administration");


        // User logs
        UserLog::createNew($user->id, "<span>Registrirani ste kao korisnik članskih stranica</span>", "administration");
    }


    public static function logAdminBroadcastMessage($msg)
    {
        //append admin signature
        $msg .= "<br>".
            "<div class='std-item sender'>".
            "<div class = 'msg-sender-icon-holder std-icon-holder'>" .
            "<div class = 'msg-avatar-cont std-icon-cont'>".
            "<div class = 'thumbnail sender msg-icon std-icon no-avatar'>" .
            "<div class='pic-cont std-pic-cont'>".
            "<span class='v-align-helper'></span><img src='public/graphics/admin.jpg'/>".
            "</div>".
            "</div>".
            "</div>".
            "</div>" .
            "<div class = 'sender-label'><span>Admin Gh0C</span></div>".
            "</div>";
        $all_users = User::getUsers();
        foreach($all_users as $user) {
            Message::createNew("user", $user->id, $msg);
        }

        return $msg;

    }



    public static function logClassicUserLogin($user)
    {
        // Messages

        // Action logs

        // User logs
        UserLog::createNew($user->id, "<span>Prijava na članske stranice</span>", "access");
    }


    public static function logUserPasswordChange($user)
    {
        // Messages

        // Action logs

        // User logs
        UserLog::createNew($user->id, "<span>Promjena lozinke</span>", "password");
    }

    public static function logUserAvatarChange($user)
    {
        // Messages

        // Action logs

        // User logs
        UserLog::createNew($user->id, "<span>Promjena avatara</span>", "profile");
    }

    public static function logUserAvatarDelete($user)
    {
        // Messages

        // Action logs

        // User logs
        UserLog::createNew($user->id, "<span>Brisanje avatara</span>", "profile");
    }

    public static function logUserAvatarFacebookChange($user)
    {
        // Messages

        // Action logs

        // User logs
        UserLog::createNew($user->id, "<span>Postavljanje avatara na Facebook profilnu sliku</span>", "profile");
    }

    public static function logUserProfileDataChange($user)
    {
        // Messages

        // Action logs

        // User logs
        UserLog::createNew($user->id, "<span>Promjena osobnih podataka</span>", "profile");
    }


    public static function logClassicBooking($user, $selected_spans)
    {
        $exit_msg = "Uspješna rezervacija!\nRezervacija termina: \n";

        $admin_msg = "<span class = 'underline'>{$user->username}</span><span> je " . $user->ssbos("rezervirala", "rezervirao") . " termine:</span><br><ul>";
        $action_log_msg = "<span class = 'underline'>{$user->username}</span><span> ({$user->full_name()}) je " . $user->ssbos("rezervirala", "rezervirao") . " termine" .
            " (" . (string)(sizeof($selected_spans)) . "):</span><br><ul>";
        $user_log_msg = "<span>Rezervirali ste termine: </span><br><ul>";

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

            $datetime_span = DatetimeSpan::getByDatetimeAndCourse($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                $s_course_id);
            $datetimes[] = array("id" => $datetime_span->id);

            $training_course = TrainingCourse::getCourseById($s_course_id);

            $admin_msg .= "<li><span>" . $datetime_span->descriptionString() . "<br>" . $training_course->title . "</span></li>";
            $exit_msg .=  $datetime_span->descriptionString() . "\n";
            $action_log_msg .= "<li><span>" . $datetime_span->descriptionString() . "</span></li>";
            $user_log_msg .= "<li><span>" . $datetime_span->descriptionString() . "</span></li>";

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

            $admin_msg = "<span class = 'underline'>{$user->username}</span><span> je " . $user->ssbos("rezervirala", "rezervirao") . " termine:</span><br><ul>";
            $action_log_msg = "<span class = 'underline'>{$user->username}</span><span> ({$user->full_name()}) je " . $user->ssbos("rezervirala", "rezervirao") . " termine" .
                " (" . (string)(sizeof($selected_available_spans)) . "):</span><br><ul>";
            $user_log_msg = "<span>Rezervirali ste termine: </span><br><ul>";

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

                $datetime_span = DatetimeSpan::getByDatetimeAndCourse($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                    $s_course_id);
                $datetimes[] = array("id" => $datetime_span->id);

                $training_course = TrainingCourse::getCourseById($s_course_id);

                $admin_msg .= "<li><span>" . $datetime_span->descriptionString() . "<br>" . $training_course->title . "</span></li>";
                $exit_msg .=  $datetime_span->descriptionString() . "\n";
                $action_log_msg .= "<li><span>" . $datetime_span->descriptionString() . "</span></li>";
                $user_log_msg .= "<li><span>" . $datetime_span->descriptionString() . "</span></li>";

            }

            $admin_msg .= "</ul>";
            $action_log_msg .= "</ul>";
            $user_log_msg .= "</ul>";

            $exit_msg .= "\nPredbilježba termina: \n";

            $admin_msg .= "<span class = 'underline'>{$user->username}</span><span> se " . $user->ssbos("predbilježila", "predbilježio") . " za termine:</span><br><ul>";
            $action_log_msg .= "<span class = 'underline'>{$user->username}</span><span> ({$user->full_name()}) se " . $user->ssbos("predbilježila", "predbilježio") . " za termine" .
                " (" . (string)(sizeof($selected_full_spans)) . "):</span><br><ul>";
            $user_log_msg .= "<span>Predbilježili ste se za termine: </span><br><ul>";



            for ($i = 0; $i < sizeof($selected_full_spans); $i++) {
                $selected_span = $selected_full_spans[$i];
                $date_time_courseId = explode(" ", $selected_span);
                $s_date = $date_time_courseId[0];
                $s_time = $date_time_courseId[1];
                $s_course_id = $date_time_courseId[2];
                list($s_hours, $s_minutes) = explode("-", $s_time);

                $datetime_span = DatetimeSpan::getByDatetimeAndCourse($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                    $s_course_id);
                $datetimes[] = array("id" => $datetime_span->id);

                $training_course = TrainingCourse::getCourseById($s_course_id);

                $admin_msg .= "<li><span>" . $datetime_span->descriptionString() . "<br>" . $training_course->title . "</span></li>";
                $exit_msg .=  $datetime_span->descriptionString() . "\n";
                $action_log_msg .= "<li><span>" . $datetime_span->descriptionString() . "</span></li>";
                $user_log_msg .= "<li><span>" . $datetime_span->descriptionString() . "</span></li>";

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

            $admin_msg = "<span class = 'underline'>{$user->username}</span><span> se " . $user->ssbos("predbilježila", "predbilježio") . " za termine:</span><br><ul>";
            $action_log_msg = "<span class = 'underline'>{$user->username}</span><span> ({$user->full_name()}) se " . $user->ssbos("predbilježila", "predbilježio") . " za termine" .
                " (" . (string)(sizeof($selected_full_spans)) . "):</span><br><ul>";
            $user_log_msg = "<span>Predbilježili ste se za termine: </span><br><ul>";

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

                $datetime_span = DatetimeSpan::getByDatetimeAndCourse($s_date . " " . $s_hours . ":" . $s_minutes . ":00",
                    $s_course_id);
                $datetimes[] = array("id" => $datetime_span->id);

                $training_course = TrainingCourse::getCourseById($s_course_id);

                $admin_msg .= "<li><span>" . $datetime_span->descriptionString() . "<br>" . $training_course->title . "</span></li>";
                $exit_msg .=  $datetime_span->descriptionString() . "\n";
                $action_log_msg .= "<li><span>" . $datetime_span->descriptionString() . "</span></li>";
                $user_log_msg .= "<li><span>" . $datetime_span->descriptionString() . "</span></li>";

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




    public static function logSpanCancelation($span, $reservations)
    {
        // Messages
        $msg = "<span>Termin " . $span->descriptionString() . " je otkazan! </span><br>" .
            "<span>Nedovoljan broj prijava: " . count($reservations) .
            " (minimalno: " . $span->training_course->min_reservations . ", kapacitet: " . $span->training_course->capacity . ") </span><br>";
        if (count($reservations)>0)
        {
            $msg .= '<span class = "underline"">Rezervacije: </span><br><ul>';
            foreach($reservations as $reservation)
            {
                $msg .= "<li><span class = 'underline'>" . $reservation->user->username . "</span><span> (rezervirano: " .
                    date("j.n.Y. G:i", strtotime($reservation->created_at)) . ")</span></li>";

                // user message
                $user_msg = "<span>Termin " . $span->descriptionString() . "<br>(" . $span->training_course->title .
                    ")<br>je otkazan zbog nedovoljno prijava! (" . count($reservations) . ", potrebno: " .
                    $span->training_course->min_reservations . ")</span><br>";
                $user_msg .= "<span>Tvoja rezervacija za taj termin je automatski poništena!</span>";

                Message::createNew("user", $reservation->user->id, $user_msg);
            }
            $msg .= "</ul>";
        }

        foreach(Admin::getAdmins() as $admin) {
            Message::createNew("admin", $admin->id, $msg);
        }


        // Action logs

        $receivers = array();
        $datetimes = array();
        $datetimes[] = array("id" => $span->id);
        $msg = "<span>Termin " . $span->descriptionString() . " je otkazan! </span><br>" .
            "<span>Nedovoljan broj prijava: " . count($reservations) .
            " (minimalno: " . $span->training_course->min_reservations . ", kapacitet: " . $span->training_course->capacity . ") <span><br>" .
            "<span>Otkazanih rezervacija: " . count($reservations) . "</span>";

        foreach($reservations as $reservation)
        {
            // User Log
            $user_msg = "<span>Zbog nedovoljnog broja prijava, otkazan je termin " .
                "za koji ste imali rezervaciju: </span><br><ul><li><span>" . $span->descriptionString() . "</span></li></ul>";
            UserLog::createNew($reservation->user->id, $user_msg, "reservations");
            $receivers[] = array("id" => $reservation->user->id, "type" => "user");
        }
        ActionLog::createNew($receivers, $msg, "reservations", $datetimes);
    }


    public static function logSpanConfirmation($span, $reservations, $pre_reservations)
    {
        // Messages
        $msg = "<span>Termin " . $span->descriptionString() . " je potvrđen! </span><br>" .
            "<span>Dovoljan broj prijava: " . count($reservations) .
            " (minimalno: " . $span->training_course->min_reservations . ", kapacitet: " . $span->training_course->capacity . ") </span><br>";
        $user_msg = "<span>Potvrda održavanja termina: <br>" . $span->descriptionString() . "</span><br>" .
            "<span class = 'underline'>Prijavljeni članovi: </span><br>";
        if (count($reservations)>0)
        {
            $msg .= '<span class = "underline">Prijave: </span><br><ul>';
            foreach($reservations as $reservation)
            {
                $msg .= "<li><span class = 'underline'>" . $reservation->user->username . "</span><span> (rezervirano: " .
                    date("j.n.Y. G:i", strtotime($reservation->created_at)) . ")</span></li>";

                // user message
                $user_msg .=
                    "<div class='std-item active-booking-user'>".
                    "<div class = 'active-booking-user-icon-holder std-icon-holder'>" .
                    "<div class = 'msg-avatar-cont std-icon-cont'>".
                    "<div class = 'thumbnail active-booking-user msg-icon std-icon no-avatar'>" .
                    "<div class='pic-cont std-pic-cont'>";
                $avatar_url = $reservation->user->getAvatarURL('avatar', array("width" => 34, "height" => 34, "crop" => "fill"));
                if($avatar_url) {
                    $user_msg .= "<span class='v-align-helper'></span><img src='" . $avatar_url ."'/>";
                } else {
                    $user_msg .= "<i class = 'fa icon fa-fw fa-male'></i>";
                }
                $user_msg .= "</div>".
                    "</div></div></div>" .
                    "<div class = 'active-booking-user-label'><span>" . $reservation->user->username . "</span></div>".
                    "</div>";
            }
            $msg .= "</ul>";
        }
        if (count($pre_reservations) > 0) {
            $msg .= '<span class = "underline">Otkazane predbilježbe: </span><br><ul>';
            foreach($pre_reservations as $pre_reservation) {
                $msg .= "<li><span class = 'underline'>" . $pre_reservation->user->username . "</span><span> (predbilježeno: " .
                    date("j.n.Y. G:i", strtotime($reservation->created_at)) . ")</span></li>";            }
        }
        foreach(Admin::getAdmins() as $admin) {
            Message::createNew("admin", $admin->id, $msg);
        }
        foreach($reservations as $reservation) {
            Message::createNew("user", $reservation->user->id, $user_msg);
        }
        $canceled_pre_resrevation_msg = "<span>Otkazivanje predbilježbe za termin:  <br>".
            $span->descriptionString() . "<br>" .
            "Broj postojećih prijava (" . count($reservations) . ") ostao je jednak broju slobodnih mjesta na terminu (" .
            $span->training_course->capacity . ")</span><br>".
            "<span>Tvoja prebilježba zato nije aktivirana.</span>";
        foreach($pre_reservations as $pre_reservation) {
            Message::createNew("user", $pre_reservation->user->id, $canceled_pre_resrevation_msg);
        }



        // Action logs
        $receivers = array();
        $datetimes = array();
        $datetimes[] = array("id" => $span->id);
        $msg = "<span>Termin " . $span->descriptionString() . " je potvrđen! </span><br>" .
            "<span>Dovoljan broj prijava: " . count($reservations) .
            " (minimalno: " . $span->training_course->min_reservations . ", kapacitet: " . $span->training_course->capacity . ") <span><br>" .
            "<span>Potvrđenih rezervacija: " . count($reservations) . "</span><br>" .
            "<span>Otkazanih predbilježbi: " . count($pre_reservations) . "</span>";

        foreach($reservations as $reservation)
        {
            // User Log
            $user_msg = "<span>Zbog dovoljnog broja prijava, potvrđen je termin " .
                "za koji ste imali rezervaciju: </span><br><ul><li><span>" . $span->descriptionString() . "</span></li></ul>";
            UserLog::createNew($reservation->user->id, $user_msg, "reservations");
            $receivers[] = array("id" => $reservation->user->id, "type" => "user");
        }
        foreach($pre_reservations as $pre_reservation)
        {
            // User Log
            $user_msg = "<span>Zbog dovoljnog broja prijava, potvrđen je termin " .
                "za koji ste imali predbilježbu: </span><br><ul><li><span>" . $span->descriptionString() . "</span></li></ul>" .
                "<span>Nije došlo do otkazivanja prijava pa je Vaša predbilježba otpala!</span>";
            UserLog::createNew($pre_reservation->user->id, $user_msg, "reservations");
            $receivers[] = array("id" => $pre_reservation->user->id, "type" => "user");
        }
        ActionLog::createNew($receivers, $msg, "reservations", $datetimes);
    }




    public static function logPrereservationCancelation($user, $prereservation)
    {

        $admin_msg = "<span class = 'underline'>{$user->username}</span><span> je " . $user->ssbos("otkazala", "otkazao") . " predbilježbu za termin:</span><br><ul>";
        $action_log_msg = "<span class = 'underline'>{$user->username}</span><span> ({$user->full_name()}) je " . $user->ssbos("otkazala", "otkazao") .
            " predbilježbu za termin</span><br><ul>";
        $user_log_msg = "<span>Otkazali ste predbilježbu za termin: </span><br><ul>";

        //action logs
        $receivers = array();
        $receivers[] = array("id" => $user->id, "type" => "user");
        $datetimes = array();
        $datetimes[] = array("id" => $prereservation->datetime_span->id);
        $admin_msg .= "<li><span>" . $prereservation->datetime_span->descriptionString() . "<br>" . $prereservation->datetime_span->training_course->title . "</span></li>";
        $action_log_msg .= "<li><span>" . $prereservation->datetime_span->descriptionString() . "</span></li>";
        $user_log_msg .= "<li><span>" . $prereservation->datetime_span->descriptionString() . "</span></li>";

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
    }


    public static function logClassicReservationCancelation($user, $reservation)
    {

        $admin_msg = "<span class = 'underline'>{$user->username}</span><span> je " . $user->ssbos("otkazala", "otkazao") . " rezervaciju za termin:</span><br><ul>";
        $action_log_msg = "<span class = 'underline'>{$user->username}</span><span> ({$user->full_name()}) je " . $user->ssbos("otkazala", "otkazao") .
            " rezervaciju za termin</span><br><ul>";
        $user_log_msg = "<span>Otkazali ste prijavu za termin: </span><br><ul>";

        //action logs
        $receivers = array();
        $receivers[] = array("id" => $user->id, "type" => "user");
        $datetimes = array();
        $datetimes[] = array("id" => $reservation->datetime_span->id);
        $admin_msg .= "<li><span>" . $reservation->datetime_span->descriptionString() . "<br>" . $reservation->datetime_span->training_course->title . "</span></li>";
        $action_log_msg .= "<li><span>" . $reservation->datetime_span->descriptionString() . "</span></li>";
        $user_log_msg .= "<li><span>" . $reservation->datetime_span->descriptionString() . "</span></li>";

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
    }



    public static function logReservationCancelationWithTriggeredPrereservationActivation($user, $reservation, $pre_reservation)
    {
        $admin_msg = "<span class = 'underline'>{$user->username}</span><span> je " . $user->ssbos("otkazala", "otkazao") . " rezervaciju za termin:</span><br><ul>";
        $action_log_msg = "<span class = 'underline'>{$user->username}</span><span> ({$user->full_name()}) je " . $user->ssbos("otkazala", "otkazao") .
            " rezervaciju za termin</span><br><ul>";
        $user_log_msg = "<span>Otkazali ste prijavu za termin: </span><br><ul>";
        $new_user_msg = "<span>Aktivirana je tvoja predbilježba za termin! <br>" .
            "Zbog otkazivanja korisničke rezervacije oslobodilo se mjesto i tvoja predbilježba postaje aktivna rezervacija. <br>" .
            "Termin: " . $pre_reservation->datetime_span->descriptionString() . "</span><br>";
        $new_user_log_msg = "<span>Aktivacija predbilježbe za termin: </span><br><ul>";

        //action logs
        $receivers = array();
        $receivers[] = array("id" => $user->id, "type" => "user");
        $receivers[] = array("id" => $pre_reservation->user->id, "type" => "user");

        $datetimes = array();
        $datetimes[] = array("id" => $reservation->datetime_span->id);
        $admin_msg .= "<li><span>" . $reservation->datetime_span->descriptionString() . "<br>" . $reservation->datetime_span->training_course->title . "</span></li>";
        $action_log_msg .= "<li><span>" . $reservation->datetime_span->descriptionString() . "</span></li>";
        $user_log_msg .= "<li><span>" . $reservation->datetime_span->descriptionString() . "</span></li>";
        $new_user_log_msg .= "<li><span>" . $reservation->datetime_span->descriptionString() . "</span></li>";

        $admin_msg .= "</ul>" .
            "<span><i class = 'fa fa-fw fa-exclamation'></i> Nakon otkazivanja članske rezervacije " .
            "došlo je do automatske rezervacije zbog postojanja predbilježbe!</span><br><br>" .
            "<span class = 'underline'>" . $pre_reservation->user->username . "</span><span> je " . $pre_reservation->user->ssbos("imala", "imao") .
            " neaktiviranu predbilježbu prvu po prioritetu za isti taj termin.</span>";
        $action_log_msg .= "</ul>" .
            "<span>Nakon otkazivanja članske rezervacije " .
            "došlo je do automatske rezervacije zbog postojanja predbilježbe!</span><br>" .
            "<span class = 'underline'>" . $pre_reservation->user->username . "</span><span> ({$pre_reservation->user->full_name()}) je " . $pre_reservation->user->ssbos("imala", "imao") .
            " neaktiviranu predbilježbu prvu po prioritetu za isti taj termin koja postaje nova rezervacija.</span>";
        $user_log_msg .= "</ul>";
        $new_user_log_msg .= "</ul>";

        // Messages
        foreach(Admin::getAdmins() as $admin) {
            Message::createNew("admin", $admin->id, $admin_msg);
        }
        Message::createNew("user", $pre_reservation->user->id, $new_user_msg);

        // Action logs
        ActionLog::createNew($receivers, $action_log_msg, "reservations", $datetimes);

        // User logs
        UserLog::createNew($user->id, $user_log_msg, "reservations");
        UserLog::createNew($pre_reservation->user->id, $new_user_log_msg, "reservations");
    }
}


?>