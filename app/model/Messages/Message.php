<?php
namespace app\model\Messages;

use \app\helpers\Calendar;
use \app\helpers\Configuration;
use app\model\Database\DatabaseConnection;
use \PDO;
use \PDOException;

class Message
{

    public $id = null;
    public $created_at = null;

    public $message_body = null;
    public $receiver = null;
    public $receiver_type = null;
    public $receiver_id = null;

    public $has_been_read = 0;


    function __construct($input_data = array())
    {

        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];
        if ( isset( $input_data['has_been_read'] ) )
            $this->has_been_read = (int) $input_data['has_been_read'];

        if ( isset( $input_data['receiver_type'] ) )
            $this->receiver_type = $input_data['receiver_type'];
        if ( isset( $input_data['receiver_id'] ) )
            $this->receiver_id = (int) $input_data['receiver_id'];
        if ( isset( $input_data['message_body'] ) )
            $this->message_body = $input_data['message_body'];

        if ( isset( $input_data['created_at'] ) )
            $this->created_at = $input_data['created_at'];

    }





    public static function getMessageById($msg_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM message WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $msg_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $msg = new Message($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
        return $msg;
    }




    public static function getMessages($limit = 1000000, $order_by = "created_at DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM message ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new Message($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }


    public static function getMessagesForReceiver($receiver_id, $receiver_type, $limit = 100000, $order_by = "created_at DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM message WHERE receiver_id = :receiver_id AND receiver_type = :receiver_type ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_type', $receiver_type, PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new Message($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }


    public static function getMessagesInRangeForReceiver($receiver_id, $receiver_type, $limit = 10000, $offset = 0, $order_by = "created_at DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM message WHERE receiver_id = :receiver_id AND receiver_type = :receiver_type ORDER BY {$order_by} LIMIT :limit OFFSET :off";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':off', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_type', $receiver_type, PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new Message($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }


    public static function getUnreadMessagesForReceiver($receiver_id, $receiver_type, $limit = 1000000, $order_by = "created_at DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM message WHERE receiver_id = :receiver_id AND receiver_type = :receiver_type AND has_been_read = 0 ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_type', $receiver_type, PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new Message($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }


    public static function setHasBeenReadForListForReceiver($receiver_id, $receiver_type, $has_been_read)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE message SET has_been_read = :has_been_read WHERE receiver_id = :receiver_id AND receiver_type = :receiver_type";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':has_been_read', $has_been_read, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_type', $receiver_type, PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);

        $stmt->execute();

    }



    public function setHasBeenRead($has_been_read)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE message SET has_been_read = :has_been_read WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':has_been_read', $has_been_read, PDO::PARAM_INT);

        $stmt->execute();
    }


    public static function numberOfUnreadForReceiver($receiver_id, $receiver_type)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number FROM message WHERE receiver_id = :receiver_id AND receiver_type = :receiver_type AND has_been_read = 0";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':receiver_type', $receiver_type, PDO::PARAM_STR);
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


    public static function numberOfMessagesForReceiver($receiver_id, $receiver_type)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number FROM message WHERE receiver_id = :receiver_id AND receiver_type = :receiver_type";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':receiver_type', $receiver_type, PDO::PARAM_STR);
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


    public static function messageWithIdExists($id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number FROM message WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return ((int)$row["number"] > 0)? true : false;
        }
        else {
            return false;
        }
    }



    public static function createNew($receiver_type, $receiver_id, $message_body) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO message (receiver_id, receiver_type, created_at, message_body)
            VALUES (:receiver_id, :receiver_type, :created_at, :message_body)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
        $stmt->bindParam(':receiver_type', $receiver_type, PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
        $stmt->bindParam(':message_body', $message_body, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            return null;
        }
    }


    public function delete() {
        $dbh = DatabaseConnection::getInstance();
        $sql = "DELETE FROM message WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            return null;
        }
    }



    public function timeLabel()
    {
        $time = strtotime($this->created_at);
        return sprintf("%s %s %s", date("j.", $time), Calendar::cro_month_label_genitive(date("n", $time)), date("Y. H:i", $time));
    }




}

?>