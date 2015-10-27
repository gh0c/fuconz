<?php
namespace app\helpers;

use app\helpers\Configuration as Cfg;
use PHPMailer;

class Mailer
{
    private static $mail;

    public static function getMailer()
    {

        if (!self::$mail)
        {
            self::$mail = new PHPMailer();
            self::$mail->isSMTP();
            self::$mail->CharSet = 'UTF-8';

            self::$mail->SMTPAuth = true;
            self::$mail->isHTML(true);

            self::$mail->Host = Cfg::read('mail.host');
            self::$mail->Username = Cfg::read('mail.username');
            self::$mail->Password = Cfg::read('mail.password');
            self::$mail->Port = Cfg::read('mail.port');
        }

        return self::$mail;
    }

    public static function passwordRecovery($input_data) {

        $url = $input_data['url'];
        $pass_reset = $input_data['pass.reset'];
        $user = $input_data['user'];

        $mailer = self::getMailer();
        $email_body = '<h1>Reset lozinke</h1>' .
            '<p>Za pristup stranici za aktivaciju nove lozinke ' .
            'kliknite na link koji slijedi </br></p>' .
            '<a href = "' . $url . '">Reset lozinke</a></br>' .
            '<p>Link će biti aktivan do ' . date("H:i j.n.Y.", $pass_reset->resetValidUntil()) .
            '</br></p>' .
            'Ukoliko niste poslali zahtjev za reaktivaciju lozinke, molimo da zanemarite i izbrišete ovu poruku.<br>' .
            "<a href = 'http://pilateszagreb.eu/'>Pure pilates studio, Zagreb </a><br>";

        // Change this crap
        $mailer->addAddress('ghoc.hrx@gmail.com', 'Goran Hrzenjak');
        $mailer->Subject = "Reset zaboravljene lozinke";
        $mailer->Body = $email_body;

        $mailer->From = "From@localhost.com";
        $mailer->FromName = "Gogo testa rossa";

        $sent_successfully = $mailer->send();

        return $sent_successfully;
    }

}

