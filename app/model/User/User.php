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

class User
{
    public $id = null;
    public $username = null;
    public $first_name = null;
    public $last_name = null;
    public $email = null;
    public $sex = "";
    private $password = null;
    private $pass_salt = null;
//    private $pass_hash = null;
    public $avatar_ext = "";
    public $active = null;
    public $activated = null;
    public $deactivated = null;
    public $banned = null;
    public $used_credits = null;

    public $fb_id = null;
    public $use_fb_avatar = null;

    function __construct($input_data = array())
    {
        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];
        if ( isset( $input_data['username'] ) )
            $this->username = $input_data['username'];
        if ( isset( $input_data['first_name'] ) )
            $this->first_name = $input_data['first_name'];
        if ( isset( $input_data['last_name'] ) )
            $this->last_name = $input_data['last_name'];
        if ( isset( $input_data['email'] ) )
            $this->email = $input_data['email'] ;

        if( isset($input_data['fb_id']))
            $this->fb_id = $input_data["fb_id"];
        if( isset($input_data['use_fb_avatar']))
            $this->use_fb_avatar = $input_data["use_fb_avatar"];

        if ( isset( $input_data['avatar_format_ext'] ) )
            $this->avatar_ext = $input_data['avatar_format_ext'];
        if ( isset( $input_data['sex'] ) )
            $this->sex = $input_data['sex'];

        if ( isset( $input_data['active'] ) )
            $this->active = (int)$input_data['active'];
        if ( isset( $input_data['activated'] ) )
            $this->activated = (int)$input_data['activated'];
        if ( isset( $input_data['deactivated'] ) )
            $this->deactivated = (int)$input_data['deactivated'];
        if ( isset( $input_data['banned'] ) )
            $this->banned = (int)$input_data['banned'];


        if ( isset( $input_data['password'] ) )
            $this->password = $input_data['password'];
        if ( isset( $input_data['salt'] ) )
            $this->pass_salt = $input_data['salt'];

        if ( isset( $input_data['used_credits'] ) )
            $this->used_credits = $input_data['used_credits'];
    }


    public function populate_attributes_from_input($data)
    {
        $this->__construct($data);
    }



    public function getPassword() {
        return $this->password;
    }


    public function getPasswordSalt() {
        return $this->pass_salt;
    }


    public function activated() {
        if (!$this->activated || $this->activated == 0) {
            return false;
        } else {
            return true;
        }
    }


    public function full_name()  {
        return ($this->first_name . " " . $this->last_name);
    }


    public static function getUsers($limit = 1000000, $order_by = "username ASC") {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM users ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user = new User($row);
                $list[] = $user;
            }
            return $list;
        }
        else {
            return array();
        }
    }


    public static function getActiveUsers($limit = 1000000, $order_by = "username ASC") {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM users WHERE active = 1 ORDER BY {$order_by} LIMIT :limit";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $list = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user = new User($row);
                $list[] = $user;
            }
            return $list;
        }
        else {
            return array();
        }

    }

    public static function getUserById($user_id)
    {
        if ($user_data = self::getUserDataById($user_id)) {
            return new User($user_data);
        } else {
            return null;
        }
    }


    protected static function getUserDataById($user_id)
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    public static function getUserByEmail($email){
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM users WHERE UPPER(email) = UPPER(:email) LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $user = new User($stmt->fetch(PDO::FETCH_ASSOC));
            return $user;
        }
        else {
            return null;
        }
    }


    public static function getUserByUsername($username){
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM users WHERE UPPER(username) = UPPER(:username) LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $user = new User($stmt->fetch(PDO::FETCH_ASSOC));
            return $user;
        }
        else {
            return null;
        }
    }



    public function delete() {
        $dbh = DatabaseConnection::getInstance();
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            return null;
        }
    }


    public function updateRememberMeCredentials($identifier, $token) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE users SET remember_me_id = :remember_me_id, remember_me_token = :token WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':remember_me_id', $identifier, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();
    }



    public function removeRememberMeCredentials() {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE users SET remember_me_id = :remember_me_id, remember_me_token = :token WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':remember_me_id', null, PDO::PARAM_INT);
        $stmt->bindValue(':token', null, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();
    }


    public static function existsWithCredentials($identifier) {

        $dbh = DatabaseConnection::getInstance();
        $stmt = $dbh->prepare('SELECT * FROM users WHERE remember_me_id = :remember_me_id LIMIT 1');
        $stmt->bindParam(':remember_me_id', $identifier, PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return new User($stmt->fetch());
        } else {
            return null;
        }
    }


    public function credentialsMatch($hashedToken)
    {
        $dbh = DatabaseConnection::getInstance();
        $stmt = $dbh->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $result = $stmt->fetch();
            $token = $result["remember_me_token"];
            if(Hash::hashCheck($hashedToken, $token))
            {
                return true;
            }
            else {
                return false;
            }
        } else {
            return false;
        }
    }



    // AVATAR ---
    public function hasAvatar($type = "avatar") {
        if ($this->facebookAvatarExists() || $this->customAvatarExists($type)) {
            return true;
        } else {
            return false;
        }
    }

    public function getAvatarURL($type = "avatar", $options = array()) {
        if($url = $this->facebookAvatarExists($options)) {
            if($this->imageHosted($url)) {
                return $url;
            } else {
                return null;
            }
        } else if ($url = $this->customAvatarExists($type, $options)) {
            if($this->imageHosted($url)) {
                return $url;
        } else {
                return null;
            }
        } else {
            return null;
        }
    }






    //
    // (1)
    // FACEBOOK AVATAR
    //
    public function facebookAvatarExists($options = array()) {
        if($this->use_fb_avatar && $this->fb_id) {
            $new_merged_options = array_merge($options, array("type" => "facebook"));
            return cloudinary_url($this->fb_id . ".jpg", $new_merged_options);
        } else {
            return null;
        }
    }
    public function getFacebookAvatarURL($options = array())
    {
        if($this->use_fb_avatar && $this->fb_id) {
            $new_merged_options = array_merge($options, array("type" => "facebook"));
            $url = (string)cloudinary_url($this->fb_id . ".jpg", $new_merged_options);
            if (($url)) {
                return $url;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }




    //
    // (1)
    // CUSTOM AVATAR
    //

    public function customAvatarExists($type = "avatar", $options = array())
    {
        if($img = $this->customAvatarAssociationExists($type)) {
            if(isset($img->url)) {
                $new_merged_options = array_merge($options, array("version" => $img->version));

                $url = (string)cloudinary_url($this->getImagePath($type), $new_merged_options);
                if (($url)) {
                    return $url;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


    public function getCustomAvatarURL($type = "avatar", $options = array())
    {
        if($img = $this->customAvatarAssociationExists($type)) {
            $new_merged_options = array_merge($options, array("version" => $img->version));

            return (string)cloudinary_url($this->getImagePath($type), $new_merged_options);
        } else {
            return null;
        }
    }

    public function getCustomAvatarImageTag($type = "avatar", $options = array())
    {
        if($img = $this->customAvatarAssociationExists($type)) {
            $new_merged_options = array_merge($options, array("version" => $img->version));

            echo cl_image_tag($this->getImagePath($type), $new_merged_options);
        } else {
            return null;
        }
    }

    public function returnCustomAvatarImageTag($type = "avatar", $options = array())
    {
        if($img = $this->customAvatarAssociationExists($type)) {
            $new_merged_options = array_merge($options, array("version" => $img->version));

            return cl_image_tag($this->getImagePath($type), $new_merged_options);
        } else {
            return null;
        }
    }


    public function customAvatarAssociationExists($type = "avatar")
    {
        if($img = Image::getImageForEntityWithFlag($this->id, "user", $type)) {
            return $img;
        } else {
            return null;
        }
    }


    public function deleteImage($type = "avatar")
    {
        $api = new \Cloudinary\Api();
        $api->delete_resources(array($this->getImagePath($type)));
    }


    public function getImagePath($type = "avatar")
    {
        return Cfg::read("cl.images.path") . "/user/" . $this->username . "/" . $type . "/img";
    }


    public function deleteOldImages()
    {
//        $this->deleteImage("avatar");
        $status = Image::deleteAssociationsForEntityWithFlag($this->id, "user", "avatar");
    }




    public function imageHosted($url) {
        if (@getimagesize($url)) {
            return true;
        } else {
            return false;
        }
    }


    public function updatePassword($hashed_pass) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE users SET password = :new_pass WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':new_pass', $hashed_pass, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

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


    public function updateFacebookAvatarUsage($use_fb_avatar, $fb_id) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE users SET use_fb_avatar = :use, fb_id = :fb_id WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':fb_id', $fb_id, PDO::PARAM_STR);
        $stmt->bindParam(':use', $use_fb_avatar, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $this->populate_attributes_from_input(array("use_fb_avatar"=>$use_fb_avatar, "fb_id" => $fb_id));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function unsetFacebookAvatarUsage() {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE users SET use_fb_avatar = :use WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':use', 0, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function createNew($username, $email, $password, $first_name = null, $last_name = null, $sex = null) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO users (email, username, first_name, last_name, password, salt, sex, registered_at)
            VALUES (:email, :username, :first_name, :last_name, :password, :salt, :sex, :registered_at)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':registered_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
        $stmt->bindParam(':sex', $sex, PDO::PARAM_STR);
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $salt = Hash::getMSG()->generateString(128);
        $stmt->bindParam(':salt', $salt, PDO::PARAM_STR);
        $stmt->bindValue(':password', Hash::password($password . $salt), PDO::PARAM_STR);

        try {
            $stmt->execute();
            $status["success"] = true;
            return User::getUserById($dbh->lastInsertId());
        } catch (\Exception $e) {
            $status["success"] = false;
            $status["err"] = $e->getMessage();
            return $status;
        }

    }



    public  function updateProfileData($email, $first_name = null, $last_name = null, $sex = null) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "UPDATE users SET email = :email, first_name = :first_name, last_name = :last_name, sex = :sex WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':sex', $sex, PDO::PARAM_STR);
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }


    public function unreadMessagesExists()
    {
        if(Message::numberOfUnreadForReceiver($this->id, "user") > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function numberOfUnreadMessages()
    {
        return Message::numberOfUnreadForReceiver($this->id, "user");
    }

    public function numberOfMessages()
    {
        return Message::numberOfMessagesForReceiver($this->id, "user");
    }


    public static function validateNew($p_username, $p_email, $p_password, $p_password_repeated,
                                       $first_name = null, $last_name = null, $sex = null) {
        $validation_result = array();
        $validation_result["validated"] = false;
        if(!isset($p_email) || $p_email === "" || !isset($p_username) || $p_username === ""
            || !isset($p_password) || $p_password === "" || !isset($p_password_repeated) || $p_password_repeated === "") {

            $validation_result["errors"] = "Nedostaju podaci za registraciju! \n" .
                "Unos e-mail adrese, korisničkog imena i lozinke su obavezni za prijavu.";
            return $validation_result;
        } elseif(!preg_match('/^(\w|\s|[^\x00-\x7F]|-)+$/', $p_username) ) {
            $validation_result["errors"] = "Dopušteni znakovi korisničkog imena su slova, brojke i crtica!";
            return $validation_result;
        }elseif (!($p_password === $p_password_repeated)) {
            $validation_result["errors"] = "Lozinka i njena potvrda se ne poklapaju!";
            return $validation_result;
        } else {
            if (self::getUserByUsername($p_username)) {
                $validation_result["errors"] = "Već postoji korisnik {$p_username}.\n ".
                    "Odaberite drugo korisničko ime i pokušajte ponovo.";
                return $validation_result;
            } elseif (self::getUserByEmail($p_email)) {
                $validation_result["errors"] = "E-mail na koji se registrirate mora biti jedinstven. \n".
                    "Već postoji korisnik sa e-mail adresom {$p_email}.";
                return $validation_result;
            } else {
                $validation_result["validated"] = true;
            }
        }
        return $validation_result;
    }


    public static function validateProfileDataChange($p_email, $user, $first_name = null, $last_name = null, $sex = null) {
        $validation_result = array();
        $validation_result["validated"] = false;
        if(!isset($p_email) || $p_email === "" ) {
            $validation_result["errors"] = "Prilikom promjene osobnih podataka obavezan je unos e-mail adrese!";
            return $validation_result;
        } else {
            if ($existing_user = self::getUserByEmail($p_email)) {
                if($existing_user->id != $user->id) {
                    $validation_result["errors"] = "E-mail na koji se registrirate mora biti jedinstven. \n".
                        "Već postoji korisnik sa e-mail adresom {$p_email}.";
                    return $validation_result;
                } else {
                    $validation_result["validated"] = true;
                }
            } else {
                $validation_result["validated"] = true;
            }
        }
        return $validation_result;
    }


    public static function validatePasswordChange($p_password, $p_password_new, $p_password_new_repeated, $user) {
        $validation_result = array();
        $validation_result["validated"] = false;
        if(!isset($p_password) || $p_password === "" ||
            !isset($p_password_new) || $p_password_new === "" ||
            !isset($p_password_new_repeated) || $p_password_new_repeated === "") {

            $validation_result["errors"] = "Nedostaju podaci za registraciju! \n" .
                "Unos e-mail adrese, korisničkog imena i lozinke su obavezni za prijavu.";
            return $validation_result;
        } elseif (!($p_password_new === $p_password_new_repeated)) {
            $validation_result["errors"] = "Lozinka i njena potvrda se ne poklapaju!";
            return $validation_result;
        } else {
            if(Hash::passwordCheck($p_password . $user->getPasswordSalt(), $user->getPassword())) {
                $validation_result["validated"] = true;
            } else {
                $validation_result["errors"] = "Neispravna aktualna lozinka.\nPokušajte ponovo.";
                return $validation_result;
            }

        }
        return $validation_result;
    }


    public static function validateUserLogin($p_username, $p_password)
    {

        $validation_result = array();
        $validation_result["validated"] = false;

        if(!isset($p_username) || $p_username === "" || !isset($p_password) || $p_password === "")
        {
            $validation_result["errors"] = "Nedostaju podaci za prijavu! \n" .
                "Unos korisničkog imena i lozinke su obavezni za prijavu.";
            return $validation_result;
        }
        $user = self::getUserByUsername($p_username);
        if (!$user) {
            $validation_result["errors"] = "Neispravo korisničko ime i/ili lozinka.";
            return $validation_result;
        }
        else {
            $success = Hash::passwordCheck($p_password . $user->getPasswordSalt(), $user->getPassword());

            if($success) {

                if(!$user->activated()) {
                    $validation_result["errors"] = "Korisnik još nije aktiviran.";
                    return $validation_result;
                } else {
                    $validation_result["validated"] = true;
                    $validation_result["user"] = $user;
                }
            } else {
                $validation_result["errors"] = "Neispravo korisničko ime i/ili lozinka.";
                return $validation_result;
            }
        }
        return $validation_result;
    }



    public function activePassResetRequestExists() {
        return UserPassReset::activePassResetRequestExistsForUser($this);
    }

    public function createNewPasswordReset() {
        return UserPassReset::createNew($this);
    }

    public static function getPassResetByUserHash($hash) {
        return UserPassReset::getByUserHash($hash);
    }

    public function deletePasswordResetRequest() {
        UserPassReset::deleteForUser($this);
    }


    public function ssbos($string1, $string2) {
        //Select string based on user's sex
        if ($this->sex === Cfg::read('db.sex.female')) {
            return $string1;
        }
        return $string2;
    }

}
?>