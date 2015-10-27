<?php

namespace app\model\Content;

use app\helpers\Configuration as Cfg;

class ImagesHandler
{

    public static function validate($file, $min_dimensions = null) {
        if ($error_msg = self::validateImgFormat($file)) {
            return $error_msg;
        }
        if (!$min_dimensions) {
            return null;
        }
        else {
            if($error_msg = self::validateDimensions($file, $min_dimensions[0], $min_dimensions[1])) {
                return $error_msg;
            }
            return null;
        }
    }

    public static function validateDimensions($file, $min_w, $min_h) {
        $tmp_name = trim( $file['tmp_name'] );

        // get the image width, height and mime type
        $image_proportions = getimagesize($tmp_name);


        if ($image_proportions[0] < $min_w ||
            $image_proportions[1] < $min_h) {
            return "Premale dimenzije uploadane slike: " .
                "{$image_proportions[0]}x{$image_proportions[1]}" .
                ". Očekivani minimum: {$min_w}x{$min_h}";
        }
        return false;
    }

    public static function validateImgFormat($file) {
        $file_type = $file['type'];
        if (!(preg_match('/^image\/p?jpeg$/i', $file_type) ||
            preg_match('/^image\/gif$/i', $file_type) ||
            preg_match('/^image\/(x-)?png$/i', $file_type) )) {
            return "Format uploadane datoteke nije ispravan: {$file_type} " .
                ". Dopušteno: image/jpeg, image/png, image/gif!";
        }
        else {

            $orig_ext = strtolower( strrchr( $file['name'], '.' ) );

            if (preg_match('/^.jp[e]?g$/i', $orig_ext) ||
                preg_match('/^.gif$/i', $orig_ext) ||
                preg_match('/^.png$/i', $orig_ext)) {

                return false;
            }
            else {
                return "Neočekivana ekstenzija tipa datoteke: {$orig_ext}" .
                    ". Dopušteno : .jpeg, .jpg, .png, .gif";
            }
        }
    }


    public static function imageExt($file) {
        $file_type = $file['type'];
        if (!(preg_match('/^image\/p?jpeg$/i', $file_type) ||
            preg_match('/^image\/gif$/i', $file_type) ||
            preg_match('/^image\/(x-)?png$/i', $file_type) )) {
            return null;
        }
        else {

            $orig_ext = strtolower( strrchr( $file['name'], '.' ) );

            if (preg_match('/^.jp[e]?g$/i', $orig_ext) ||
                preg_match('/^.gif$/i', $orig_ext) ||
                preg_match('/^.png$/i', $orig_ext)) {

                return $orig_ext;
            }
            else {
                return null;
            }
        }
    }

    public static function saveUploadedImg($image, $destination, $name) {
        $img_ext = strtolower( strrchr( $image['name'], '.' ) );

        $tmp_name = trim( $image['tmp_name'] );
        echo "<br>";
        var_dump($image);

        if ( is_uploaded_file ( $tmp_name ) ){

            try {
                if ( !( move_uploaded_file( $tmp_name, $destination . $name . $img_ext))) {
                    throw new \Exception("Error durig copying uploaded file.");
                }
            }
            catch (\Exception $ex) {
                throw new \Exception("Error durig copying uploaded file. -- \n" .
                    $ex->getMessage());
            }
            try {
                if ( !( chmod( $destination . "/" . $name . $img_ext, 0666 ) ) ){
                    throw new \Exception("Error durig writing uploaded file. No writing permissions.");
                }
            }
            catch (\Exception $ex) {
                throw new \Exception("Error durig writing uploaded file. -- \n" .
                    $ex->getMessage());
            }

        }
    }


    public static function saveResized($image_path, $destination, $name, $desired_dimensions,
                                       $allow_enlargement = false, $quality = 100, $crop = false) {
        $dest_w = $desired_dimensions[0];
        $dest_h = $desired_dimensions[1];

        $img_ext = strtolower( strrchr( $image_path, '.' ) );


        list($src_w, $src_h, $src_type) = getimagesize($image_path);
        if (!$crop) {

            if (!$allow_enlargement && $src_w <= $dest_w && $src_h <= $dest_h) {
                $final_w = $src_w;
                $final_h = $src_h;
            }
            else {
                $final_h = intval($src_h/$src_w * $dest_w);
                if($final_h > $dest_h) {
                    $final_h = $dest_h;
                    $final_w = $src_w/$src_h * $dest_h;
                }
                else {
                    $final_w = $dest_w;
                }
            }

            $thumb_templ = imagecreatetruecolor($final_w, $final_h);

            if($src_type == IMAGETYPE_GIF) {
                self::saveGIF($image_path, $thumb_templ, 0, 0, 0, 0, $final_w, $final_h,
                    $src_w, $src_h, $destination, $name, $img_ext);
            }
            else if($src_type == IMAGETYPE_JPEG) {
                self::saveJPEG($image_path, $thumb_templ, 0, 0, 0, 0, $final_w, $final_h,
                    $src_w, $src_h, $destination, $name, $img_ext, Cfg::read('jpeg.quality'));
            }
            else if($src_type == IMAGETYPE_PNG) {
                self::savePNG($image_path, $thumb_templ, 0, 0, 0, 0, $final_w, $final_h,
                    $src_w, $src_h, $destination, $name, $img_ext);
            }
        }
    }


    public static function saveCropped($orig_img_path, $destination, $name, $rectangle) {
        $left = $rectangle[0];
        $top = $rectangle[1];
        $width = $rectangle[2];
        $height = $rectangle[3];


        $img_ext = strtolower( strrchr( $orig_img_path, '.' ) );
        list($src_w, $src_h, $src_type) = getimagesize($orig_img_path);
        $thumb_templ = imagecreatetruecolor($width, $height);

        if($src_type == IMAGETYPE_GIF) {
            self::saveCroppedGIF($orig_img_path, $thumb_templ, $destination, $name,
                $left, $top, $width, $height, $img_ext);
        }
        else if($src_type == IMAGETYPE_JPEG) {
            self::saveCroppedJPEG($orig_img_path, $thumb_templ, $destination, $name,
                $left, $top, $width, $height, $img_ext, Cfg::read('jpeg.quality'));
        }
        else if($src_type == IMAGETYPE_PNG) {
            self::saveCroppedPNG($orig_img_path, $thumb_templ, $destination, $name,
                $left, $top, $width, $height, $img_ext);
        }


    }


    private static function saveJPEG($image_path, $thumb_templ, $d_x, $d_y, $s_x, $s_y,
        $dest_w, $dest_h, $src_w, $src_h, $destination_dir, $name, $ext, $quality) {
        $image_content = imagecreatefromjpeg($image_path);
        imagecopyresampled($thumb_templ, $image_content, $d_x, $d_y, $s_x, $s_y,
            $dest_w, $dest_h, $src_w, $src_h);
        imagejpeg($thumb_templ, $destination_dir . $name . $ext, $quality);
        imagedestroy($thumb_templ);
    }

    private static function saveCroppedJPEG($orig_img_path, $thumb_templ, $destination, $name,
        $left, $top, $width, $height, $img_ext,  $quality = 100) {
        $image_content = imagecreatefromjpeg($orig_img_path);
        imagecopy($thumb_templ, $image_content, 0, 0, $left, $top, $width, $height);
        imagejpeg($thumb_templ, $destination . $name . $img_ext, $quality);
        imagedestroy($thumb_templ);
    }


    private static function saveGIF($image_path, $thumb_templ, $d_x, $d_y, $s_x, $s_y,
                                     $dest_w, $dest_h, $src_w, $src_h, $destination_dir, $name, $ext) {

        $image_content = imagecreatefromgif($image_path);
        imagecopyresampled($thumb_templ, $image_content, $d_x, $d_y, $s_x, $s_y,
            $dest_w, $dest_h, $src_w, $src_h);
        imagegif($thumb_templ, $destination_dir . $name . $ext);
        imagedestroy($thumb_templ);
    }

    private static function saveCroppedGIF($orig_img_path, $thumb_templ, $destination, $name,
                                           $left, $top, $width, $height, $img_ext) {

        $image_content = imagecreatefromgif($orig_img_path);
        imagecopy($thumb_templ, $image_content, 0, 0, $left, $top, $width, $height);
        imagegif($thumb_templ, $destination . $name . $img_ext);
        imagedestroy($thumb_templ);
    }


    private static function savePNG($image_path, $thumb_templ, $d_x, $d_y, $s_x, $s_y,
                                    $dest_w, $dest_h, $src_w, $src_h, $destination_dir, $name, $img_ext) {
        $image_content = imagecreatefrompng($image_path);
        imagealphablending($thumb_templ, false);
        imagesavealpha($thumb_templ, true);
        imagealphablending($thumb_templ, true);
        imagecopyresampled($thumb_templ, $image_content, $d_x, $d_y, $s_x, $s_y,
            $dest_w, $dest_h, $src_w, $src_h);
        imagepng($thumb_templ, $destination_dir . $name . $img_ext);
        imagedestroy($thumb_templ);
    }

    private static function saveCroppedPNG($orig_img_path, $thumb_templ, $destination, $name,
                                           $left, $top, $width, $height, $img_ext) {
        $image_content = imagecreatefrompng($orig_img_path);
        imagealphablending($thumb_templ, false);
        imagesavealpha($thumb_templ, true);
        imagealphablending($thumb_templ, true);
        imagecopy($thumb_templ, $image_content, 0, 0, $left, $top, $width, $height);
        imagepng($thumb_templ, $destination . $name . $img_ext);
        imagedestroy($thumb_templ);
    }


    public static function imgDimensions($image_path) {
        list($src_w, $src_h, $src_type) = getimagesize($image_path);
        return array($src_w, $src_h);
    }
}
?>