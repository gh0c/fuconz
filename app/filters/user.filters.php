<?php

use app\helpers\Configuration;
use app\helpers\Sessions;

$userAuthCheck = function($required) use ($app) {
    return function() use ($required, $app) {
        if (!$app->auth_user && $required)  {
            if($app->request->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(array(
                    "error" =>"Stranica dostupna samo prijavljenim članovima! U međuvremenu ste odjavljeni. " .
                        "Pokušajte ponovno pristupiti stranici i po potrebi se prijaviti!"
                ));
                exit();
            } else {
                $app->flash('statuses', "Stranica dostupna samo prijavljenim članovima!");
                $app->redirect($app->urlFor('user.login'));
            }

        }
        else if ($app->auth_user && !$required){
            if ($app->request->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(array(
                    "error" =>"Već ste prijavljeni. Osvježite stranicu i pokušajte ponovo."
                ));
                exit();

            } else {
                $app->flash('statuses', "Već ste prijavljeni!");
                $app->redirect($app->urlFor('user.profile.home'));
            }

        }
    };
};

$adminAuthCheck = function($required) use ($app) {
    return function() use ($required, $app) {
        if (!$app->auth_admin && $required)  {
            if ($app->request->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(array(
                    "error" =>"Stranica dostupna samo administratorima! U međuvremenu ste odjavljeni. Pokušajte ponovno"
                ));
                exit();

            } else {
                $app->flash('statuses', "Stranica dostupna samo administratorima!");
                $app->redirect($app->urlFor('admin.login'));
            }

        }
        else if ($app->auth_admin && !$required){
            if ($app->request->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(array(
                    "error" =>"Već ste prijavljeni. Osvježite stranicu i pokušajte ponovo."
                ));
                exit();

            } else {
                $app->flash('statuses', "Već ste prijavljeni!");
                $app->redirect($app->urlFor('admin.home'));

            }

        }
    };
};

$authenticated_user = function() use ($userAuthCheck) {
    return $userAuthCheck(true);
};

$guest_user = function() use ($userAuthCheck) {
    return $userAuthCheck(false);
};

$authenticated_admin = function() use ($adminAuthCheck) {
    return $adminAuthCheck(true);
};

$guest_admin = function() use ($adminAuthCheck) {
    return $adminAuthCheck(false);
};
?>