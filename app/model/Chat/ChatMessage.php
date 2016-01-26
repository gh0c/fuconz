<?php
namespace app\model\Chat;

use \app\helpers\Calendar;
use \app\model\User\User;

use \app\model\Database\DatabaseConnection;
use \PDO;

use \app\helpers\Text;

class ChatMessage
{

    public $id = null;
    public $created_at = null;

    public $message_body = null;
    public $sender = null;
    public $sender_id = null;

    public $chat_room = null;
    public $chat_room_id = null;

    function __construct($input_data = array())
    {

        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];

        if ( isset( $input_data['sender_id'] ) ) {
            $this->sender_id = (int) $input_data['sender_id'];
            $this->sender = User::getUserById($this->sender_id);
        }
        if ( isset( $input_data['message_body'] ) ) {
            // raw string from DB to chat display

            $this->message_body = Text::toChatString($input_data['message_body']);
        }


        if ( isset( $input_data['created_at'] ) )
            $this->created_at = $input_data['created_at'];

        if ( isset( $input_data['chat_room_id'] ) ) {
            $this->chat_room_id = (int) $input_data['chat_room_id'];
            $this->chat_room = null; // ChatRoom::getById($this->chat_room_id);
        }
    }





    public static function getMessageById($msg_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM chat_message WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $msg_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $msg = new ChatMessage($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
        return $msg;
    }



    public static function getMessagesForSender($sender_id, $limit = 100000, $order_by = "created_at ASC, id ASC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM chat_message
            WHERE sender_id = :sender_id ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new ChatMessage($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }


    public static function getMessagesInRangeForSender($sender_id, $limit = 10000, $offset = 0, $order_by = "created_at ASC, id ASC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM chat_message WHERE sender_id = :sender_id ORDER BY {$order_by} LIMIT :limit OFFSET :off";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':off', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new ChatMessage($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }


    public static function getMessagesForChatRoom($chat_room_id, $limit = 100000, $order_by = "created_at ASC, id ASC ")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM chat_message
            WHERE chat_room_id = :chat_room_id ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':chat_room_id', $chat_room_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new ChatMessage($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }


    public static function getUsersUnreadMessagesForChatRoom($chat_room_id, $user_id, $limit = 100000, $order_by = "created_at ASC, id ASC ")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM chat_message
            WHERE chat_room_id = :chat_room_id
            AND id > (SELECT last_read_message_id FROM chat_room_user
            WHERE chat_room_id = :chat_room_id AND user_id = :user_id)
            ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':chat_room_id', $chat_room_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new ChatMessage($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }



    public static function getMessagesInRangeForChatRoom($chat_room_id, $limit = 10000, $offset = 0, $order_by = "created_at ASC, id ASC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM chat_message
            WHERE chat_room_id = :chat_room_id
            ORDER BY {$order_by} LIMIT :limit OFFSET :off";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':off', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':sender_id', $chat_room_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new ChatMessage($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }



    public static function lastChatRoomMessage($chat_room_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM chat_message
            WHERE chat_room_id = :chat_room_id ORDER BY id DESC LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':chat_room_id', $chat_room_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $msg = new ChatMessage($stmt->fetch(PDO::FETCH_ASSOC));
            return $msg;
        } else {
            return null;
        }
    }


    public static function numberOfMessagesForSender($sender_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number
            FROM chat_message WHERE sender_id = :sender_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);

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
        $sql = "SELECT COUNT(*) AS number FROM chat_message WHERE id = :id";
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
        $sql = "DELETE FROM chat_message WHERE id = :id LIMIT 1";
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

    public function datetimeIdString()
    {
        $time = strtotime($this->created_at);
        $id = sprintf("%s-%d", date("Y-m-d-h-i-s", $time), $this->id);
        return $id;
    }


}

?>