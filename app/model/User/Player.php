<?php
namespace app\model\User;

use app\helpers\Configuration as Cfg;
use app\model\Content\Image;
use app\model\Database\DatabaseConnection;
use \app\model\Messages\Message;
use app\helpers\Hash;
use app\helpers\General;
use \PDO;
use \PDOException;

class Player extends User
{
    public $has_avatar = false;
    public $avatar_img_tag = null;
    public $avatar_img_url = "";

    function __construct($input_data = array())
    {
        parent::__construct($input_data);

        $this->has_avatar = ($this->hasAvatar("avatar"))? true : false;

        if($this->has_avatar) {
//            $this->avatar_img_tag = $this->returnClImageTag("avatar", array("width" => 50, "height" => 50, "crop" => "fill"));
            $this->avatar_img_url = $this->getAvatarURL("avatar", array("width" => 50, "height" => 50, "crop" => "fill"));
        }

    }


    public static function getPlayerById($user_id)
    {
        if ($user_data = self::getUserDataById($user_id)) {
            return new Player($user_data);
        } else {
            return null;
        }
    }





}
?>