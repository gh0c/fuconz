<?php
namespace app\model\User;

use app\helpers\Configuration as Cfg;
use app\model\Database\DatabaseConnection;
use app\helpers\Hash;
use app\helpers\General;
use \PDO;
use \PDOException;


class UserPassReset
{
    public $reset_hash = "";
    public $user_id = null;
    public $reset_sent_on = null;
    public $security = null;

    function __construct($input_data = array()) {
        if(isset($input_data["user_id"]))
            $this->user_id = (int)$input_data["user_id"];
        if(isset($input_data["reset_hash"]))
            $this->reset_hash = $input_data["reset_hash"];
        if(isset($input_data["reset_sent_on"])) {
            $date_string = explode( '-', $input_data['reset_sent_on'] );
            if ( count($date_string) == 3 ) {
                $this->reset_sent_on = strtotime($input_data["reset_sent_on"]);
            }
            else {
                $this->reset_sent_on = $input_data["reset_sent_on"];
            }
        }

        if(isset($input_data["security"]))
            $this->security = $input_data["security"];
    }

    public function resetValidUntil() {
        return $this->reset_sent_on + Cfg::read('password.reset.duration');
    }

    public static function createNew($user) {

        $time_now = time();
        $reset_hash = Hash::getMSG()->generateString(128);
        $security = Hash::hash($user->email);
        $pass_reset = new UserPassReset(array(
            'user_id' => $user->id,
            'reset_hash' => $reset_hash,
            'reset_sent_on' => $time_now,
            'security' => $security));
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO user_pass_reset (user_id, reset_hash, reset_sent_on, security) VALUES " .
            "(:user_id, :reset_hash, :reset_sent_on, :security)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':reset_sent_on', date("Y-m-d H:i:s", $time_now), PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user->id, PDO::PARAM_INT);
        $stmt->bindValue(':reset_hash', Hash::hash($reset_hash), PDO::PARAM_STR);
        $stmt->bindParam(':security', $security, PDO::PARAM_STR);

        $stmt->execute();

        return $pass_reset;
    }


    public static function activePassResetRequestExistsForUser($user) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM user_pass_reset WHERE user_id = :user_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $user->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $result = $stmt->fetch();
            $sent_on = strtotime($result["reset_sent_on"]);
            if (time() < $sent_on + Cfg::read('password.reset.duration')) {
                return new UserPassReset(array(
                    'user_id' => $user->id,
                    'reset_hash' => $result["reset_hash"],
                    'reset_sent_on' => $sent_on,
                    'security' =>$result['security']));
            }
            else {
                $sql2 = "DELETE FROM user_pass_reset WHERE user_id = :user_id";
                $stmt = $dbh->prepare($sql2);
                $stmt->bindParam(':user_id', $user->id, PDO::PARAM_INT);
                $stmt->execute();

                return null;
            }
        }
        else {
            return null;
        }

    }


    public static function getByUserHash($hash) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM user_pass_reset WHERE security = :security";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':security', $hash, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $result = $stmt->fetch();
            return new UserPassReset($result);
        }
        else {
            return null;
        }
    }

    public static function deleteForUser($user){
        $dbh = DatabaseConnection::getInstance();
        $sql = "DELETE FROM user_pass_reset WHERE user_id = :user_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $user->id, PDO::PARAM_INT);
        $stmt->execute();


    }
}