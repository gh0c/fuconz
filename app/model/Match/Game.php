<?php
namespace app\model\Match;

use app\helpers\Configuration as Cfg;
use app\model\Database\DatabaseConnection;
use \app\model\Messages\Message;
use \app\model\Reservation\DatetimeSpan;
use app\helpers\Hash;
use app\model\User\Player;
use app\model\User\User;
use \PDO;

class Game
{
    public $id = null;
    public $title = null;
    public $datetime_span = null;
    public $datetime_span_id = null;
    public $field = null;

    public $winner = null;
    public $res_team_one = null;
    public $res_team_two = null;

    public $players_team_one = null;
    public $players_team_two = null;

    public $player_ids_team_one = null;
    public $player_ids_team_two = null;

    public $created_at = null;

    public $after_extra_time = null;

    function __construct($input_data = array())
    {
        if ( isset( $input_data['id'] ) ) {
            $this->id = (int) $input_data['id'];
            $this->populatePlayers();
        }
        if ( isset( $input_data['title'] ) )
            $this->title = $input_data['title'];
        if ( isset( $input_data['field'] ) )
            $this->field = $input_data['field'];
        if ( isset( $input_data['created_at'] ) )
            $this->created_at = $input_data['created_at'];

        if ( isset( $input_data['datetime_span_id'] ) ) {
            $this->datetime_span_id = (int) $input_data['datetime_span_id'];
            $this->datetime_span = DatetimeSpan::getById($this->datetime_span_id);
        }

        if ( isset( $input_data['res_team_one'] ) )
            $this->res_team_one = (int)$input_data['res_team_one'];
        if ( isset( $input_data['res_team_two'] ) )
            $this->res_team_two = (int)$input_data['res_team_two'];
        if ( isset( $input_data['winner'] ) )
            $this->winner = (int)$input_data['winner'];
        if ( isset( $input_data['after_extra_time'] ) )
            $this->after_extra_time = (int)$input_data['after_extra_time'];
    }


    public function populate_attributes_from_input($data)
    {
        $this->__construct($data);
    }


    public function result()
    {
        return $this->res_team_one . ":" . $this->res_team_two;
    }

    public function resultFromWinnersPerspective()
    {
        if($this->winner == 2) {
            return $this->res_team_two . ":" . $this->res_team_one;
        }
        return $this->res_team_one . ":" . $this->res_team_two;
    }


    public function resultDescriptionString()
    {
        $description = "";
        if($this->winner == 1) {
            $description .= "Pobjeda ekipe 1";
            if($this->after_extra_time) {
                $description .= " poslije penala";
            }
            return $description;
        }
        if($this->winner == 2) {
            $description .= "Pobjeda ekipe 2";
            if($this->after_extra_time) {
                $description .= " poslije penala";
            }
            return $description;

        }
        if($this->winner == 0) {
            $description .= "Neriješeno";
            return $description;
        }
        return $description;
    }



    public static function getGames($limit = 1000000, $order_by = "datetime_span.datetime_span_start ASC") {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT game.* FROM game JOIN datetime_span ON game.datetime_span_id = datetime_span.id
            ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $entity = new Game($row);
                $list[] = $entity;
            }
            return $list;
        }
        else {
            return array();
        }
    }

    public static function getById($id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM game WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return new Game($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    public static function getGamesByPlayer($player_id, $limit = 1000000, $order_by = "datetime_span.datetime_span_start ASC") {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT game.* FROM game JOIN datetime_span on game.datetime_span_id = datetime_span.id
            JOIN player_game ON game.id = player_game.game_id
            WHERE player_game.user_id = :user_id ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $player_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $entity = new Game($row);
                $list[] = $entity;
            }
            return $list;
        }
        else {
            return array();
        }
    }

    public static function getGamesByDatetimeSpan($span_id, $limit = 1000000, $order_by = "datetime_span.datetime_span_start ASC") {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT game.* FROM game JOIN datetime_span on game.datetime_span_id = datetime_span.id
            WHERE game.datetime_span_id = :span_id ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':span_id', $span_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $entity = new Game($row);
                $list[] = $entity;
            }
            return $list;
        }
        else {
            return array();
        }
    }

    public static function numberOfPlayerAppearances($player_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number FROM game JOIN player_game ON game.id = player_game.game_id
            WHERE player_game.user_id = :user_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $player_id, PDO::PARAM_INT);

        $stmt->execute();
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row["number"];
        }
        else {
            return null;
        }
    }



    public static function playerTotalResultsRatio($player_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT SUM(res_for) AS number_for, SUM(res_against) AS number_against from (
          SELECT SUM(game.res_team_one) AS res_for, SUM(game.res_team_two) AS res_against FROM game JOIN player_game ON game.id = player_game.game_id
            WHERE player_game.user_id = :user_id AND player_game.team = 1
            UNION ALL
          SELECT SUM(game.res_team_two) AS res_for, SUM(game.res_team_one) AS resagainst FROM game JOIN player_game ON game.id = player_game.game_id
            WHERE player_game.user_id = :user_id AND player_game.team = 2
          ) a";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $player_id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return array((int)$row["number_for"], (int)$row["number_against"]);
        }
        else {
            return null;
        }
    }


    public static function numberOfPlayerWins($player_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT COUNT(*) AS number FROM game JOIN player_game ON game.id = player_game.game_id
            WHERE player_game.user_id = :user_id AND player_game.team = game.winner";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user_id', $player_id, PDO::PARAM_INT);

        $stmt->execute();
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row["number"];
        }
        else {
            return null;
        }
    }



    public function populatePlayers()
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT player_game.* FROM game JOIN player_game ON game.id = player_game.game_id
            WHERE player_game.game_id = :game_id ORDER BY player_game.player_order ASC";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':game_id', $this->id, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $players_one = array();
            $player_ids_one = array();

            $players_two = array();
            $player_ids_two = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if((int)$row["team"] == 1) {
                    $player_ids_one[] = (int)$row["user_id"];
                    $players_one[] = Player::getPlayerById($row["user_id"]);
                } elseif ((int)$row["team"] == 2) {
                    $player_ids_two[] = (int)$row["user_id"];
                    $players_two[] = Player::getPlayerById($row["user_id"]);
                }
            }
            $this->players_team_one = $players_one;
            $this->players_team_two = $players_two;
            $this->player_ids_team_one = $player_ids_one;
            $this->player_ids_team_two = $player_ids_two;

        }
        else {
            return null;
        }
    }



    public function playersStatus($player_id)
    {
        if (in_array($player_id, $this->player_ids_team_one)) {
            if ($this->winner == 1) {
                return 1; // player in winning team
            } else if ($this->winner == 2) {
                return 2; // player in defeated team
            } else if ($this->winner == 0) {
                return 3; // draw
            } else {
                return 4;
            }
        } else if (in_array($player_id, $this->player_ids_team_two)) {
            if ($this->winner == 1) {
                return 2; // player in defeated team
            } else if ($this->winner == 2) {
                return 1; // player in winning team
            } else if ($this->winner == 0) {
                return 3; // draw
            } else {
                return 4;
            }
        } else {
            return 4;
        }
    }

    public function resultFromPlayersPerspective($player_id)
    {
        if (in_array($player_id, $this->player_ids_team_one)) {
            return sprintf("%d:%d", $this->res_team_one, $this->res_team_two);
        } else if (in_array($player_id, $this->player_ids_team_two)) {
            return sprintf("%d:%d", $this->res_team_two, $this->res_team_one);
        } else {
            return sprintf("%d:%d", $this->res_team_one, $this->res_team_two);
        }
    }


    public function resultDescriptionStringFromPlayersPerspective($player_id)
    {
        $description = "";
        $players_status = $this->playersStatus($player_id);
        $result_from_players_perspective = $this->resultFromPlayersPerspective($player_id);
        if($players_status == 1) {
            $description .= "Pobjeda " . $result_from_players_perspective;
        } else if($players_status == 2) {
            $description .= "Poraz " . $result_from_players_perspective;
        } else if($players_status == 3) {
            $description .= "Neriješeno " . $result_from_players_perspective;
        }

        if($this->after_extra_time) {
            $description .= " (Poslije penala)";
        }
        return $description;
    }


    public function playersStatusLabel($player_id)
    {
        $status = $this->playersStatus($player_id);
        if($status == 1) {
            return "won";
        }
        if($status == 2) {
            return "lost";
        }
        if($status == 3) {
            return "drawn";
        }
        return "no-status";
    }






    public function delete() {
        $dbh = DatabaseConnection::getInstance();
        $sql = "DELETE FROM game WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $dbh->beginTransaction();

        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            $sql = "DELETE FROM player_game WHERE game_id = :game_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':game_id', $this->id, PDO::PARAM_INT);
            try {
                $stmt->execute();
                $dbh->commit();

                return true;
            } catch (\Exception $e) {
                $dbh->rollBack();
                return null;
            }
        } catch (\Exception $e) {
            $dbh->rollBack();
            return null;
        }
    }



    public static function createNew($title, $res_team_one, $res_team_two, $winner, $datetime_span_id, $players, $field = "", $after_et = null) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO game (title, datetime_span_id, field, res_team_one, res_team_two, winner, created_at, after_extra_time)
            VALUES (:title, :span_id, :field, :res_one, :res_two, :winner, :created_at, :after_et)";
        $stmt = $dbh->prepare($sql);

        try {
            $dbh->beginTransaction();
            $stmt->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':field', $field, PDO::PARAM_STR);
            $stmt->bindParam(':res_one', $res_team_one, PDO::PARAM_INT);
            $stmt->bindParam(':res_two', $res_team_two, PDO::PARAM_INT);
            $stmt->bindParam(':winner', $winner, PDO::PARAM_INT);
            $stmt->bindParam(':span_id', $datetime_span_id, PDO::PARAM_INT);
            $stmt->bindParam(':after_et', $after_et, PDO::PARAM_INT);

            try {
                $stmt->execute();

                $game_id = $dbh->lastInsertId("game_id_seq");
                $players_team_one = $players["team-one"];
                $players_team_two = $players["team-two"];

                $player_order = 0;
                foreach($players_team_one as $player_one) {
                    $player_order += 1;
                    $sql2 = "INSERT INTO player_game (game_id, user_id, player_order, team)
                        VALUES (:game_id, :user_id, :player_order, :team)";
                    $stmt = $dbh->prepare($sql2);

                    $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
                    $stmt->bindValue(':user_id', (int)$player_one, PDO::PARAM_INT);
                    $stmt->bindParam(':player_order', $player_order , PDO::PARAM_INT);
                    $stmt->bindValue(':team', 1, PDO::PARAM_INT);
                    $stmt->execute();
                }

                $player_order = 0;
                foreach($players_team_two as $player_two) {
                    $player_order += 1;
                    $sql2 = "INSERT INTO player_game (game_id, user_id, player_order, team)
                        VALUES (:game_id, :user_id, :player_order, :team)";
                    $stmt = $dbh->prepare($sql2);
                    $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
                    $stmt->bindValue(':user_id', (int)$player_two, PDO::PARAM_INT);
                    $stmt->bindParam(':player_order', $player_order , PDO::PARAM_INT);
                    $stmt->bindValue(':team', 2, PDO::PARAM_INT);
                    $stmt->execute();
                }

                $dbh->commit();
                $status["success"] = true;
                return $status;
            } catch (\Exception $e) {
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


    public function edit($title, $res_team_one, $res_team_two, $winner, $datetime_span_id, $players, $field = "", $after_et = null) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE game SET title = :title, datetime_span_id = :span_id, field = :field, res_team_one = :res_one,
            res_team_two = :res_two, winner = :winner, after_extra_time = :after_et WHERE id = :id";
        $stmt = $dbh->prepare($sql);

        try {
            $dbh->beginTransaction();
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':field', $field, PDO::PARAM_STR);
            $stmt->bindParam(':res_one', $res_team_one, PDO::PARAM_INT);
            $stmt->bindParam(':res_two', $res_team_two, PDO::PARAM_INT);
            $stmt->bindParam(':winner', $winner, PDO::PARAM_INT);
            $stmt->bindParam(':span_id', $datetime_span_id, PDO::PARAM_INT);
            $stmt->bindParam(':after_et', $after_et, PDO::PARAM_INT);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            try {
                $stmt->execute();

                $sql = "DELETE FROM player_game WHERE game_id = :id";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

                $stmt->execute();

                $players_team_one = $players["team-one"];
                $players_team_two = $players["team-two"];

                $player_order = 0;
                foreach($players_team_one as $player_one) {
                    $player_order += 1;
                    $sql2 = "INSERT INTO player_game (game_id, user_id, player_order, team)
                        VALUES (:game_id, :user_id, :player_order, :team)";
                    $stmt = $dbh->prepare($sql2);

                    $stmt->bindParam(':game_id', $this->id, PDO::PARAM_INT);
                    $stmt->bindValue(':user_id', (int)$player_one, PDO::PARAM_INT);
                    $stmt->bindParam(':player_order', $player_order , PDO::PARAM_INT);
                    $stmt->bindValue(':team', 1, PDO::PARAM_INT);
                    $stmt->execute();
                }

                $player_order = 0;
                foreach($players_team_two as $player_two) {
                    $player_order += 1;
                    $sql2 = "INSERT INTO player_game (game_id, user_id, player_order, team)
                        VALUES (:game_id, :user_id, :player_order, :team)";
                    $stmt = $dbh->prepare($sql2);
                    $stmt->bindParam(':game_id', $this->id, PDO::PARAM_INT);
                    $stmt->bindValue(':user_id', (int)$player_two, PDO::PARAM_INT);
                    $stmt->bindParam(':player_order', $player_order , PDO::PARAM_INT);
                    $stmt->bindValue(':team', 2, PDO::PARAM_INT);
                    $stmt->execute();
                }

                $dbh->commit();
                $status["success"] = true;
                return $status;
            } catch (\Exception $e) {
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



    public static function validateNew($p_title, $p_datetime_span_id, $p_winner, $p_res_team_one, $p_res_team_two,
                                       $p_players_team_one, $p_players_team_two, $p_field = null) {
        $validation_result = array();
        $validation_result["validated"] = false;

        if(!isset($p_title) || $p_title === "") {

            $validation_result["errors"] = "Nedostaju podaci za unos! \n" .
                "Unos naziva utakmice je obavezan.";
            return $validation_result;
        }elseif (!isset($p_winner) || !is_numeric($p_winner) || !in_array($p_winner, array(0,1,2)) ||
            !isset($p_res_team_one) || !is_numeric($p_res_team_one) || $p_res_team_one <= 0  ||
            !isset($p_res_team_two) || !is_numeric($p_res_team_two) || $p_res_team_two <= 0){
            $validation_result["errors"] = "Nedostaju podaci o rezultatu. \n" .
                "Rezultati moraju biti nenegativan cijeli broj, a ishod broj iz skupa [1,2,0]";
            return $validation_result;
        } elseif ( ($p_res_team_one > $p_res_team_two && $p_winner != 1) ||
            ($p_res_team_one < $p_res_team_two && $p_winner != 2) ||
            ($p_res_team_one == $p_res_team_two && $p_winner != 0)) {
            $validation_result["errors"] = "Nepodudaranje rezultata: {$p_res_team_one}:{$p_res_team_two} i odabranog ishoda: [{$p_winner}]";
            return $validation_result;

        }
        $span = DatetimeSpan::getById($p_datetime_span_id);
        if(!$span) {
            $validation_result["errors"] = "Ne postoji termin sa identifikatorom {$p_datetime_span_id}";
            return $validation_result;
        }
        if (!isset($p_players_team_one) || count($p_players_team_one) < 1 || !isset($p_players_team_two) || count($p_players_team_two) < 1) {
            $validation_result["errors"] = "Ekipa mora imati neke igrače...";
            return $validation_result;
        }
        foreach ($p_players_team_one as $player_one) {
            if(!User::getUserById((int)$player_one)) {
                $validation_result["errors"] = "Ne postoji igrač ekipe 1 sa identifikatorom {$player_one}";
                return $validation_result;
            }
            if (in_array($player_one, $p_players_team_two)) {
                $validation_result["errors"] = "Igrač ekipe 1 sa identifikatorom {$player_one} nalazi se i u ekipi 2";
                return $validation_result;
            }
        }
        foreach ($p_players_team_two as $player_two) {
            if(!User::getUserById((int)$player_two)) {
                $validation_result["errors"] = "Ne postoji igrač ekipe 2 sa identifikatorom {$player_two}";
                return $validation_result;
            }
            if (in_array($player_two, $p_players_team_one)) {
                $validation_result["errors"] = "Igrač ekipe 2 sa identifikatorom {$player_two} nalazi se i u ekipi 1";
                return $validation_result;
            }
        }
        $validation_result["validated"] = true;
        return $validation_result;
    }


    public static function validateEdit($p_title, $p_datetime_span_id, $p_winner, $p_res_team_one, $p_res_team_two,
                                       $p_players_team_one, $p_players_team_two, $p_field = null) {
        $validation_result = array();
        $validation_result["validated"] = false;

        if(!isset($p_title) || $p_title === "") {
            $validation_result["errors"] = "Nedostaju podaci za unos! \n" .
                "Unos naziva utakmice je obavezan.";
            return $validation_result;
        }elseif (!isset($p_winner) || !is_numeric($p_winner) || !in_array($p_winner, array(0,1,2)) ||
            !isset($p_res_team_one) || !is_numeric($p_res_team_one) || $p_res_team_one <= 0  ||
            !isset($p_res_team_two) || !is_numeric($p_res_team_two) || $p_res_team_two <= 0){
            $validation_result["errors"] = "Nedostaju podaci o rezultatu. \n" .
                "Rezultati moraju biti nenegativan cijeli broj, a ishod broj iz skupa [1,2,0]";
            return $validation_result;
        } elseif ( ($p_res_team_one > $p_res_team_two && $p_winner != 1) ||
            ($p_res_team_one < $p_res_team_two && $p_winner != 2) ||
            ($p_res_team_one == $p_res_team_two && $p_winner != 0)) {
            $validation_result["errors"] = "Nepodudaranje rezultata: {$p_res_team_one}:{$p_res_team_two} i odabranog ishoda: [{$p_winner}]";
            return $validation_result;

        }
        $span = DatetimeSpan::getById($p_datetime_span_id);
        if(!$span) {
            $validation_result["errors"] = "Ne postoji termin sa identifikatorom {$p_datetime_span_id}";
            return $validation_result;
        }
        if (!isset($p_players_team_one) || count($p_players_team_one) < 1 || !isset($p_players_team_two) || count($p_players_team_two) < 1) {
            $validation_result["errors"] = "Ekipa mora imati neke igrače...";
            return $validation_result;
        }
        foreach ($p_players_team_one as $player_one) {
            if(!User::getUserById((int)$player_one)) {
                $validation_result["errors"] = "Ne postoji igrač ekipe 1 sa identifikatorom {$player_one}";
                return $validation_result;
            }
            if (in_array($player_one, $p_players_team_two)) {
                $validation_result["errors"] = "Igrač ekipe 1 sa identifikatorom {$player_one} nalazi se i u ekipi 2";
                return $validation_result;
            }
        }
        foreach ($p_players_team_two as $player_two) {
            if(!User::getUserById((int)$player_two)) {
                $validation_result["errors"] = "Ne postoji igrač ekipe 2 sa identifikatorom {$player_two}";
                return $validation_result;
            }
            if (in_array($player_two, $p_players_team_one)) {
                $validation_result["errors"] = "Igrač ekipe 2 sa identifikatorom {$player_two} nalazi se i u ekipi 1";
                return $validation_result;
            }
        }
        $validation_result["validated"] = true;
        return $validation_result;
    }


}
?>