<?php
namespace app\model\Messages;

use \app\helpers\Calendar;
use \app\helpers\Configuration;
use app\model\Database\DatabaseConnection;
use \PDO;
use \PDOException;

class UserLog
{

    public $id = null;
    public $created_at = null;

    public $message_body = null;
    public $receiver = null;
    public $receiver_id = null;

    public $flag = "general";


    function __construct($input_data = array())
    {

        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];
        if ( isset( $input_data['flag'] ) )
            $this->flag = $input_data['flag'];

        if ( isset( $input_data['receiver_id'] ) )
            $this->receiver_id = (int) $input_data['receiver_id'];
        if ( isset( $input_data['message_body'] ) )
            $this->message_body = $input_data['message_body'];

        if ( isset( $input_data['created_at'] ) )
            $this->created_at = $input_data['created_at'];

    }





    public static function getUserLogsLogsForReceiver($receiver_id, $limit = 1000000, $order_by = "created_at DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM user_log WHERE receiver_id = :receiver_id ORDER BY :order LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':order', $order_by, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new UserLog($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return null;
        }
    }


    public static function getUserLogsWithFlagForReceiver($receiver_id, $flag = "general", $limit = 1000000, $order_by = "created_at DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM user_log WHERE receiver_id = :receiver_id AND flag = :flag AND has_been_read = 0 ORDER BY :order LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':order', $order_by, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':flag', $flag, PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new UserLog($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return null;
        }
    }


    public static function numberOfUserLogsForReceiver($receiver_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number FROM user_log WHERE receiver_id = :receiver_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row["number"];
        }
        else {
            return null;
        }
    }





    public static function createNew($receiver_id, $message_body, $flag = "general")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO user_log (receiver_id, flag, created_at, message_body)
            VALUES (:receiver_id, :flag, :created_at, :message_body)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
        $stmt->bindParam(':flag', $flag, PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
        $stmt->bindParam(':message_body', $message_body, PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }


}

?>