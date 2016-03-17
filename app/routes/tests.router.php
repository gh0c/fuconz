<?php

use app\helpers\Auth;
use app\helpers\Hash;
use app\model\Admin\Admin;

$app->group('/tests', function () use ($app) {
    $app->get('/1', function () use ($app) {
        echo "<pre>Test:<br>";
        $testovi = array(
            "2211Gh0C",
            "alfabetagamaevokuracvama",
            "alfabetagama",
            "čobankovićjebemtimater",
            "2211ghoc",
            "gh0c2211hrx",
            "gogo2211",
            "gh0c.hrx",
            "ajmosvipurgerimoji",
            "ajmosvipurgerimojiovunoćzapjevajmo",
            "2211.Gh0C",
            "gh0c2211hrx",
            "gh0c2211",
            "čobankovićjebemtimater",
            "ghoc.hrx",
            "gh0c2211",
            "gh0c?hrx!22-11.",
            "gh0c?hrx!2211",
            ""
        );
        $p_username = "Gh0C";
        $salt = "alfabetagamaevokuracvama";
        echo "Expected Hashed :<br>" . '$2y$10$HXuzDs.pVdM65igXuNBDMepxCbYcWeeFEL0seRS7AXGlEIP4KTrbC' . "<br><br>";
        $admin = Admin::getAdminByUsername($p_username);
        foreach($testovi as $p_password) {
            echo "Pass: " . $p_password . "<br>";
            $r_h = Hash::password($p_password . $salt);
            echo "Res: " . $r_h . "<br>";
            $success = Hash::passwordCheck($p_password . $admin->getPasswordSalt(), $admin->getPassword());

            if($success) {
                echo "YA -------------------------";
            } else {
                echo "NAY";
            }
            echo "<br><br>";
        }

    });

    $app->get('/2', function () use ($app) {
        $curl_connection =
            curl_init('http://http://sportnet.20minuta.hr/');
    });

});