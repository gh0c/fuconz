<?php
namespace app\model\Chat;

use \app\helpers\Hash;
use \app\model\User\User;
use \app\model\Database\DatabaseConnection;
use \PDO;

class ChatRoom
{

    public $id = null;
    public $created_at = null;

    public $last_activity = null;

    public $hash = null;

    public $name = null;
    public $symbolic_name = null;

    function __construct($input_data = array())
    {

        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];


        if ( isset( $input_data['hash'] ) )
            $this->hash = $input_data['hash'];

        if ( isset( $input_data['created_at'] ) )
            $this->created_at = $input_data['created_at'];
        if ( isset( $input_data['last_activity'] ) )
            $this->last_activity = $input_data['last_activity'];

        if ( isset( $input_data['name'] ) )
            $this->name = $input_data['name'];
        if ( isset( $input_data['symbolic_name'] ) )
            $this->symbolic_name = $input_data['symbolic_name'];

    }





    public static function getById($id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM chat_room WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $chat_room = new ChatRoom($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
        return $chat_room;
    }



    public static function getByHash($hash)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM chat_room WHERE hash = :hash LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':hash', $hash, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $chat_room = new ChatRoom($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
        return $chat_room;
    }



    public static function createNew($message_body, $sender, $receivers)
    {
        $dbh = DatabaseConnection::getInstance();
        $status = array();

        $sql = "INSERT INTO chat_room (
            name, symbolic_name, hash, created_at, last_activity)
            VALUES (
            :name, :sym_name, :hash, :created_at, :last_activity
            )";
        $stmt = $dbh->prepare($sql);

        try {
            $dbh->beginTransaction();

            $stmt->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
            $stmt->bindValue(':last_activity', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
            $name = "Chat korisnika: " . $sender->username;
            $sym_name = "" . $sender->id;
            foreach($receivers as $receiver) {
                $name .= ", " . $receiver->username;
                $sym_name .= "_" . $receiver->id;
            }
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':sym_name', $sym_name, PDO::PARAM_STR);
            $stmt->bindValue(':hash', Hash::getMSG()->generateString(63), PDO::PARAM_STR);

            $stmt->execute();

            $chat_room_id = $dbh->lastInsertId("chat_room_id_seq");


            // create new chat message
            $sql_create_msg = "INSERT INTO chat_message (
                sender_id, message_body, created_at, chat_room_id)
                VALUES (
                :sender_id, :msg_body, :created_at, :room_id
                )";

            $stmt_create_msg = $dbh->prepare($sql_create_msg);

            $stmt_create_msg->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
            $stmt_create_msg->bindParam(':room_id', $chat_room_id, PDO::PARAM_INT);
            $stmt_create_msg->bindParam(':sender_id', $sender->id, PDO::PARAM_INT);
            $stmt_create_msg->bindParam(':msg_body', $message_body, PDO::PARAM_STR);

            $stmt_create_msg->execute();

            $msg_id = $dbh->lastInsertId("chat_meesage_id_seq");


            $sql_create_association = "INSERT INTO chat_room_user (
                chat_room_id, user_id, added, user_have_unread_messages, last_read_message_id)
                VALUES (
                :room_id, :user_id, :added, :unread, :last_read_msg_id
                )";
            $stmt_create_association = $dbh->prepare($sql_create_association);
            $stmt_create_association->bindValue(':added', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
            $stmt_create_association->bindParam(':room_id', $chat_room_id, PDO::PARAM_INT);
            $stmt_create_association->bindParam(':user_id', $sender->id, PDO::PARAM_INT);
            $stmt_create_association->bindValue(':unread', 0, PDO::PARAM_INT);
            $stmt_create_association->bindParam(':last_read_msg_id', $msg_id, PDO::PARAM_INT);

            $stmt_create_association->execute();

            foreach($receivers as $receiver) {
                $sql_create_association = "INSERT INTO chat_room_user (
                chat_room_id, user_id, added, user_have_unread_messages, last_read_message_id)
                VALUES (
                :room_id, :user_id, :added, :unread, :last_read_msg_id
                )";
                $stmt_create_association = $dbh->prepare($sql_create_association);
                $stmt_create_association->bindValue(':added', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
                $stmt_create_association->bindParam(':room_id', $chat_room_id, PDO::PARAM_INT);
                $stmt_create_association->bindParam(':user_id', $receiver->id, PDO::PARAM_INT);
                $stmt_create_association->bindValue(':unread', 1, PDO::PARAM_INT);
                $stmt_create_association->bindValue(':last_read_msg_id', 0, PDO::PARAM_INT);

                $stmt_create_association->execute();
            }


            $dbh->commit();
            $status["success"] = true;
            $status["chat-room-id"] = $chat_room_id;

            return $status;

        } catch(\Exception $e) {
            $dbh->rollback();
            $status["success"] = false;
            $status["err"] = $e->getMessage();
            return $status;
        }
    }



    public static function getSingleByTwoUsers($user_a_id, $user_b_id)
    {
        $dbh = DatabaseConnection::getInstance();

        $sql = "SELECT * FROM chat_room WHERE symbolic_name IN (:name_a, :name_b) LIMIT 1";


        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':name_a', sprintf("%d_%d", $user_a_id, $user_b_id), PDO::PARAM_STR);
        $stmt->bindValue(':name_b', sprintf("%d_%d", $user_b_id, $user_a_id), PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $chat_room = new ChatRoom($stmt->fetch(PDO::FETCH_ASSOC));
            return $chat_room;
        } else {
            return null;
        }

    }



    public static function getByUser($id, $limit = 1000, $order_by = "chat_room.last_activity DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT chat_room.* FROM chat_room
            JOIN chat_room_user on chat_room.id = chat_room_user.chat_room_id
            WHERE chat_room_user.user_id = :user_id ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $entity = new ChatRoom($row);
                $list[] = $entity;
            }
            return $list;
        }
        else {
            return array();
        }
    }



    public function userHaveUnreadMessages($user_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT user_have_unread_messages FROM chat_room_user
            WHERE user_id = :user_id AND chat_room_id = :room_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':room_id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if((int)$row["user_have_unread_messages"] > 0) {
                return true;
            } else {
                return false;
            }
        }
        else {
            return null;
        }
    }

    public function usersUnread($user_id)
    {
        return ChatMessage::getUsersUnreadMessagesForChatRoom($this->id, $user_id);
    }



    public function lastMessage()
    {
        return ChatMessage::lastChatRoomMessage($this->id);
    }

    public static function numberOfHotChatrooms($user_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT SUM(user_have_unread_messages) AS number FROM chat_room_user
            WHERE user_id = :user_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row["number"];
        }
        else {
            return null;
        }
    }

    public function numberOfUsersUnreadMessages($user_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number
            FROM chat_message WHERE id > (
            SELECT last_read_message_id FROM chat_room_user
            WHERE user_id = :user_id AND chat_room_id = :chat_room_id)
            AND chat_room_id = :chat_room_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':chat_room_id', $this->id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row["number"];
        }
        else {
            return null;
        }
    }


    public function refreshUnreadStatusForUser($user_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE chat_room_user SET
            user_have_unread_messages = :unread,
            last_read_message_id = (
              SELECT MAX(id) FROM chat_message
              WHERE chat_room_id = :chat_room_id
            )
            WHERE chat_room_id = :chat_room_id
            AND user_id = :user_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':unread', 0, PDO::PARAM_INT);

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':chat_room_id', $this->id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $status["success"] = true;
            return $status;
        } catch (\Exception $e) {
            $status["success"] = false;
            $status["err"] = $e->getMessage();
            return $status;
        }
    }


    public function refreshUnreadStatusForUserWithMsg($user_id, $msg_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE chat_room_user SET
            user_have_unread_messages = :unread,
            last_read_message_id = :msg_id
            WHERE chat_room_id = :chat_room_id
            AND user_id = :user_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':unread', 0, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->bindParam(':msg_id', $msg_id, PDO::PARAM_INT);
        $stmt->bindParam(':chat_room_id', $this->id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $status["success"] = true;
            return $status;
        } catch (\Exception $e) {
            $status["success"] = false;
            $status["err"] = $e->getMessage();
            return $status;
        }
    }


    public static function getHotForUser($id, $limit = 1000, $order_by = "chat_room_user.last_read_message_id")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT chat_room.* FROM chat_room
            JOIN chat_room_user ON chat_room.id = chat_room_user.chat_room_id
            WHERE chat_room_user.user_id = :user_id AND
            chat_room_user.user_have_unread_messages = 1
            ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $entity = new ChatRoom($row);
                $list[] = $entity;
            }
            return $list;
        }
        else {
            return array();
        }
    }



    public function participantsWithoutMe($my_id)
    {
        $all_id_s = explode("_", $this->symbolic_name);
        $id_s = array_diff($all_id_s, array($my_id));

        $participants = array();
        foreach($id_s as $user_id) {
            $participants[] = User::getUserById($user_id);
        }
        return $participants;
    }


    public function addMessageTo($message_body, $sender)
    {
        $dbh = DatabaseConnection::getInstance();
        $status = array();

        $chat_room_id = $this->id;

        // create new chat message
        $sql_create_msg = "INSERT INTO chat_message (
            sender_id, message_body, created_at, chat_room_id)
            VALUES (
            :sender_id, :msg_body, :created_at, :room_id
            )";
        try {
            $dbh->beginTransaction();

            $stmt_create_msg = $dbh->prepare($sql_create_msg);

            $stmt_create_msg->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
            $stmt_create_msg->bindParam(':room_id', $chat_room_id, PDO::PARAM_INT);
            $stmt_create_msg->bindParam(':sender_id', $sender->id, PDO::PARAM_INT);
            $stmt_create_msg->bindParam(':msg_body', $message_body, PDO::PARAM_STR);

            $stmt_create_msg->execute();

            $msg_id = $dbh->lastInsertId("chat_message_id_seq");

            $sql_update_last_activity = "UPDATE chat_room
                SET last_activity = :last_activity
                WHERE id = :room_id";
            $stmt_update_last_activity = $dbh->prepare($sql_update_last_activity);

            $stmt_update_last_activity->bindValue(':last_activity', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
            $stmt_update_last_activity->bindParam(':room_id', $chat_room_id, PDO::PARAM_INT);

            $stmt_update_last_activity->execute();

            $sql_update_sender_association = "UPDATE chat_room_user SET
                user_have_unread_messages = :unread, last_read_message_id = :new_msg_id
                WHERE chat_room_id = :chat_room_id AND user_id = :sender_id";
            $stmt_update_sender_association = $dbh->prepare($sql_update_sender_association);
            $stmt_update_sender_association->bindParam(':chat_room_id', $chat_room_id, PDO::PARAM_INT);
            $stmt_update_sender_association->bindParam(':sender_id', $sender->id, PDO::PARAM_INT);
            $stmt_update_sender_association->bindValue(':unread', 0, PDO::PARAM_INT);
            $stmt_update_sender_association->bindParam(':new_msg_id', $msg_id, PDO::PARAM_INT);

            $stmt_update_sender_association->execute();


            $sql_update_receiver_associations = "UPDATE chat_room_user SET
                user_have_unread_messages = :unread
                WHERE chat_room_id = :chat_room_id AND user_id <> :sender_id";
            $stmt_update_receiver_associations = $dbh->prepare($sql_update_receiver_associations);
            $stmt_update_receiver_associations->bindParam(':chat_room_id', $chat_room_id, PDO::PARAM_INT);
            $stmt_update_receiver_associations->bindParam(':sender_id', $sender->id, PDO::PARAM_INT);
            $stmt_update_receiver_associations->bindValue(':unread', 1, PDO::PARAM_INT);

            $stmt_update_receiver_associations->execute();

            $dbh->commit();
            $status["success"] = true;
            $status["msg-id"] = $msg_id;
            return $status;

        } catch(\Exception $e) {
            $dbh->rollback();
            $status["success"] = false;
            $status["err"] = $e->getMessage();
            return $status;
        }
    }
}

?>