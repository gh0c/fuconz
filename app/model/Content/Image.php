<?php
namespace app\model\Content;

use \app\model\Database\DatabaseConnection;
use \app\helpers\Hash;
use \PDO;

class Image
{
    public $id = null;
    public $public_id = null;
    public $version = null;
    public $width = null;
    public $height = null;
    public $format = null;
    public $resource_type = null;
    public $created_at = null;
    public $type = null;
    public $etag = null;
    public $url = null;
    public $secure_url = null;
    public $orig_filename = null;
    public $path = null;
    public $moderated = null;
    public $hash = null;

    function __construct($input_data = array()) {
        if ( isset( $input_data['id'] ) )
            $this->id = (int) $input_data['id'];

        if ( isset( $input_data['version'] ) )
            $this->version = (int) $input_data['version'];
        if ( isset( $input_data['width'] ) )
            $this->width = (int) $input_data['width'];
        if ( isset( $input_data['height'] ) )
            $this->height = (int) $input_data['height'];

        if ( isset( $input_data['public_id'] ) )
            $this->public_id = $input_data['public_id'];

        if ( isset( $input_data['format'] ) )
            $this->format = $input_data['format'];
        if ( isset( $input_data['resource_type'] ) )
            $this->resource_type = $input_data['resource_type'];
        if ( isset( $input_data['created_at'] ) )
            $this->created_at = $input_data['created_at'] ;

        if ( isset( $input_data['type'] ) )
            $this->type = $input_data['type'];
        if ( isset( $input_data['etag'] ) )
            $this->etag = $input_data['etag'];
        if ( isset( $input_data['url'] ) )
            $this->url = $input_data['url'];
        if ( isset( $input_data['secure_url'] ) )
            $this->secure_url = $input_data['secure_url'];
        if ( isset( $input_data['orig_filename'] ) )
            $this->orig_filename = $input_data['orig_filename'];
        if ( isset( $input_data['path'] ) )
            $this->path = $input_data['path'];
        if ( isset( $input_data['moderated'] ) )
            $this->moderated = (int) $input_data['moderated'];

       $this->hash = (isset($input_data["hash"])) ? $input_data["hash"] : null;
    }


    public static function getImageById($id)    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM cl_image WHERE id = :id LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $img = new Image($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
        return $img;
    }


    public static function getImageByHash($hash)    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM cl_image WHERE hash = :hash LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':hash', $hash, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $img = new Image($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
        return $img;
    }



    public static function createNew($public_id, $version, $width, $height, $format, $url, $secure_url, $resource_type = null,
        $type = null, $etag = null, $orig_filename = null, $path = null, $moderated = null) {
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO cl_image (public_id, version, width, height, format, url, secure_url, resource_type,
            created_at, type, etag, orig_filename, path, moderated, hash)
            VALUES (:public_id, :v, :w, :h, :format, :url, :s_url, :resource_type, :created_at, :type, :etag,
            :orig_name, :path, :mod, :hash)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':created_at', date("Y-m-d H:i:s", time()), PDO::PARAM_STR);
        $stmt->bindParam(':public_id', $public_id, PDO::PARAM_STR);
        $stmt->bindParam(':v', $version, PDO::PARAM_INT);
        $stmt->bindParam(':w', $width, PDO::PARAM_INT);
        $stmt->bindParam(':h', $height, PDO::PARAM_INT);
        $stmt->bindParam(':format', $format, PDO::PARAM_STR);
        $stmt->bindParam(':url', $url, PDO::PARAM_STR);
        $stmt->bindParam(':s_url', $secure_url, PDO::PARAM_STR);
        $stmt->bindParam(':resource_type', $resource_type, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':etag', $etag, PDO::PARAM_STR);
        $stmt->bindParam(':orig_name', $orig_filename, PDO::PARAM_STR);
        $stmt->bindParam(':path', $path, PDO::PARAM_STR);
        $stmt->bindParam(':mod', $moderated, PDO::PARAM_STR);
        $stmt->bindValue(':hash', Hash::getMSG()->generateString(128), PDO::PARAM_STR);

        $status = array();
        try {
            $stmt->execute();
            $status["success"] = true;
            $img = Image::getImageById($dbh->lastInsertId("cl_image_id_seq"));
            return array($status, $img);
        } catch (\Exception $e) {
            $status["success"] = false;
            $status["err"] = $e->getMessage();
            return array($status, null);
        }

    }



    public function assignImageToEntity($entity_id, $entity_type, $flag = null)    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "INSERT INTO image_entity (image_id, entity_id, entity_type, flag)
            VALUES (:image_id, :entity_id, :entity_type, :flag)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':image_id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':entity_id', $entity_id, PDO::PARAM_INT);
        $stmt->bindParam(':entity_type', $entity_type, PDO::PARAM_STR);
        $stmt->bindParam(':flag', $flag, PDO::PARAM_STR);

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




    public static function getImageForEntityWithFlag($entity_id, $entity_type, $flag = "general")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "SELECT * FROM image_entity WHERE entity_type = :type AND entity_id = :id AND flag = :flag LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':type', $entity_type, PDO::PARAM_STR);
        $stmt->bindParam(':id', $entity_id, PDO::PARAM_INT);
        $stmt->bindParam(':flag', $flag, PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($img = Image::getImageById((int)$row["image_id"])) {
                return $img;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


    public static function deleteImagesForEntityWithFlag($entity_id, $entity_type, $flag = "general")
    {
        $dbh = DatabaseConnection::getInstance();
        $sql = "DELETE img.* FROM cl_image AS img JOIN image_entity ON img.id = image_entity.image_id
            WHERE entity_type = :type AND entity_id = :id AND flag = :flag";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':type', $entity_type, PDO::PARAM_STR);
        $stmt->bindParam(':id', $entity_id, PDO::PARAM_INT);
        $stmt->bindParam(':flag', $flag, PDO::PARAM_STR);

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

    public static function deleteAssociationsForEntityWithFlag($entity_id, $entity_type, $flag = "general")
    {
        $status = self::deleteImagesForEntityWithFlag($entity_id, $entity_type, $flag);

        $dbh = DatabaseConnection::getInstance();
        $sql = "DELETE FROM image_entity WHERE entity_type = :type AND entity_id = :id AND flag = :flag";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':type', $entity_type, PDO::PARAM_STR);
        $stmt->bindParam(':id', $entity_id, PDO::PARAM_INT);
        $stmt->bindParam(':flag', $flag, PDO::PARAM_STR);

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

}
