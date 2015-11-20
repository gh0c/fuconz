<?php
namespace app\model\Messages;

use \app\helpers\Calendar;
use \app\helpers\Configuration;
use app\model\Database\DatabaseConnection;
use \PDO;
use \PDOException;

class ActionLog
{

    public $id = null;
    public $created_at = null;

    public $message_body = null;
    public $receiver = null;
    public $receiver_type = null;
    public $receiver_id = null;

    public $flag = "general";

    public $list_of_receivers = null;

    public $datetime_span_id = null;

    function __construct($input_data = array())
    {

        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];
        if ( isset( $input_data['flag'] ) )
            $this->flag = $input_data['flag'];

        if ( isset( $input_data['receiver_type'] ) )
            $this->receiver_type = $input_data['receiver_type'];
        if ( isset( $input_data['receiver_id'] ) )
            $this->receiver_id = (int) $input_data['receiver_id'];
        if ( isset( $input_data['message_body'] ) )
            $this->message_body = $input_data['message_body'];

        if ( isset( $input_data['created_at'] ) )
            $this->created_at = $input_data['created_at'];
        if ( isset( $input_data['datetime_span_id'] ) )
            $this->datetime_span_id = (int) $input_data['datetime_span_id'];
        if ( isset( $input_data['list_of_receivers'] ) )
            $this->list_of_receivers = $input_data['list_of_receivers'];
    }





    public static function getActionLogById($log_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM action_log WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $log_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $log = new ActionLog($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
        return $log;
    }




    public static function getActionLogsForReceiver($receiver_id, $receiver_type, $limit = 1000000, $order_by = "created_at DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT action_log.* FROM action_log JOIN action_log_receiver
            ON action_log.id = action_log_receiver.action_log_id
            WHERE receiver_id = :receiver_id AND receiver_type = :receiver_type ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_type', $receiver_type, PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new ActionLog($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }



    public static function getActionLogsWithFlagForReceiver($receiver_id, $receiver_type, $flag = 'general', $limit = 1000000, $order_by = "created_at DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT action_log.* FROM action_log JOIN action_log_receiver
            ON action_log.id = action_log_receiver.action_log_id
            WHERE receiver_id = :receiver_id AND receiver_type = :receiver_type AND flag = :flag ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':flag', $flag, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_type', $receiver_type, PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new ActionLog($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }




    public static function numberOfActionLogsForReceiver($receiver_id, $receiver_type)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number FROM action_log JOIN action_log_receiver
            ON action_log.id = action_log_receiver.action_log_id
            WHERE receiver_id = :receiver_id AND receiver_type = :receiver_type";
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


    public static function getActionLogsForDatetimeSpanForReceiver($receiver_id, $receiver_type, $datetime_span_id, $limit = 1000000, $order_by = "created_at DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT action_log.* FROM action_log JOIN action_log_receiver
            ON action_log.id = action_log_receiver.action_log_id JOIN action_log_datetime_span
            ON action_log.id = action_log_datetime_span.action_log_id
            WHERE receiver_id = :receiver_id AND receiver_type = :receiver_type AND datetime_span_id = :datetime_span_id ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':datetime_span_id', $datetime_span_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_type', $receiver_type, PDO::PARAM_STR);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new ActionLog($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }



    public static function getActionLogsForDatetimeSpan($datetime_span_id, $limit = 1000000, $order_by = "created_at DESC")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM action_log JOIN action_log_receiver
            ON action_log.id = action_log_receiver.action_log_id JOIN action_log_datetime_span
            ON action_log.id = action_log_datetime_span.action_log_id
            WHERE datetime_span_id = :datetime_span_id ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':datetime_span_id', $datetime_span_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $msg = new ActionLog($row);
                $list[] = $msg;
            }
            return $list;
        }
        else {
            return array();
        }
    }

    public static function numberOfActionLogsForDatetimeSpan($datetime_span_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number FROM action_log JOIN action_log_datetime_span
            ON action_log.id = action_log_datetime_span.action_log_id
            WHERE datetime_span_id = :datetime_span_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':datetime_span_id', $datetime_span_id, PDO::PARAM_INT);


        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row["number"];
        }
        else {
            return null;
        }
    }


    public static function actionLogWithIdExists($id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number FROM action_log WHERE id = :id";
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



    public static function createNew($receivers, $message_body, $flag = "general", $datetimes = array()) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO action_log (created_at, message_body, flag)
            VALUES (:created_at, :message_body, :flag)";
        $stmt = $dbh->prepare($sql);

        try {
            $dbh->beginTransaction();
            $stmt->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
            $stmt->bindParam(':flag', $flag, PDO::PARAM_STR);
            $stmt->bindParam(':message_body', $message_body, PDO::PARAM_STR);

            try {
                $stmt->execute();
                $action_log_id = $dbh->lastInsertId();
                foreach($receivers as $receiver) {
                    $sql2 = "INSERT INTO action_log_receiver (action_log_id, receiver_type, receiver_id)
                        VALUES (:id, :receiver_type, :receiver_id)";
                    $stmt = $dbh->prepare($sql2);
                    $stmt->bindValue(':id', (int)$action_log_id, PDO::PARAM_INT);
                    $stmt->bindParam(':receiver_id', $receiver["id"], PDO::PARAM_INT);
                    $stmt->bindParam(':receiver_type', $receiver["type"], PDO::PARAM_STR);
                    $stmt->execute();
                }
                foreach($datetimes as $span) {
                    $sql2 = "INSERT INTO action_log_datetime_span (action_log_id, datetime_span_id)
                        VALUES (:id, :span_id)";
                    $stmt = $dbh->prepare($sql2);
                    $stmt->bindValue(':id', (int)$action_log_id, PDO::PARAM_INT);
                    $stmt->bindParam(':span_id', $span["id"], PDO::PARAM_INT);
                    $stmt->execute();
                }
                $dbh->commit();
                $status["success"] = true;
                return $status;

            }catch(\Exception $e) {
                $dbh->rollback();
                $status["success"] = false;
                $status["err"] = $e->getMessage();
                return $status;
            }

        } catch(\Exception $e) {
            $dbh->rollback();
            $status["success"] = false;
            $status["err"] = $e->getMessage();
            return $status;
        }

    }


    public function delete() {
        $dbh = DatabaseConnection::getInstance();
        $sql = "DELETE FROM action_log WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        try {
            $dbh->beginTransaction();

            $stmt->execute();

            $sql = "DELETE FROM action_log_receiver WHERE action_log_id = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            $stmt->execute();
            $sql = "DELETE FROM action_log_datetime_span WHERE action_log_id = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            return true;

        } catch (\Exception $e) {
            $dbh->rollBack();
            return false;
        }



    }




}

?>