<?php
use \app\model\User\User;
use app\helpers\Configuration as Cfg;
use \app\helpers\Hash;
use \app\model\Messages\Logger;
use \app\model\Content\Image;
use \app\model\Messages\Message;
use \app\model\Reservation\Booking;

$app->group('/clanovi', function () use ($app, $authenticated_user) {

    $app->group('/profil', $authenticated_user(), function () use ($app, $authenticated_user) {

        $app->get('/', $authenticated_user(), function () use ($app) {
            $app->render('user/profile/user.home.twig', array(
                'user' => $app->auth_user,
                'active_page' => 'user.profile',
                'active_item' => 'user.profile.home'));
        })->name('user.profile.home');

        $app->post('/load-messages', $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {

                $body = $app->request->getBody();
                $json_data_received = json_decode($body, true);

                $unread_messages = Message::getUnreadMessagesForReceiver($app->auth_user->id, "user");
                $app->render('user/profile/home/unread_messages.twig', array(
                    'user' => $app->auth_user,
                    'unread_messages' => $unread_messages
                ));
                exit();
            }
        })->name('user.profile.load.messages.post');


        $app->post('/load-reservations', $authenticated_user(), function() use ($app) {
            if($app->request->isAjax()) {

                $body = $app->request->getBody();
                $json_data_received = json_decode($body, true);

                list($b_allowed, $b_not_allowed, $b_ended) = Booking::getByUserLimitedSorted($app->auth_user->id, 5, 2, 1000, 5);

                $app->render('user/profile/home/hot_bookings.twig', array(
                    'user' => $app->auth_user,
                    'bookings_allowed' => $b_allowed,
                    'bookings_not_allowed' => $b_not_allowed,
                    'bookings_ended' => $b_ended
                ));
                exit();
            }
        })->name('user.profile.load.reservations.post');

        $app->get('/:action', $authenticated_user(), function ($action) use ($app) {
            if(in_array($action, array('avatar', 'ikona', 'promjena-lozinke', 'podaci'))) {
                $app->pass();
            }
            else {
                $app->flashNow('errors', "Nema tražene stranice!");
                $app->render('user/profile/user.action.twig', array(
                    'user' => $app->auth_user,
                    'action' => $action,
                    'active_page' => "user.profile",
                ));
            }
        })->name('user.action');


        $app->get('/promjena-lozinke', $authenticated_user(), function () use ($app) {
            $app->render('user/profile/user.password_change.twig', array(
                'active_page' => "user.profile",
                'active_item' => "user.profile.password-change"));
        })->name('user.profile.password-change');


        $app->post('/promjena-lozinke', $authenticated_user(), function () use ($app) {
            $p_password = $app->request->post('old-password');
            $p_password_new = $app->request->post('new-password');
            $p_password_new_repeated = $app->request->post('new-password-repeated');

            $validation_result = User::validatePasswordChange($p_password, $p_password_new,
                $p_password_new_repeated, $app->auth_user);

            if(!($validation_result["validated"])) {
                // Validation failed
                if(isset($validation_result["errors"])) {
                    $app->flash('errors', $validation_result["errors"]);
                }
                $app->redirect($app->urlFor('user.profile.password-change'));
            } else {
                // Validation of input data successful
                $status = $app->auth_user->updatePassword(Hash::password($p_password_new . $app->auth_user->getPasswordSalt()));
                if ($status["success"] == true) {
                    $app->flash('success', "Uspješna promjena lozinke.");
                    Logger::logUserPasswordChange($app->auth_user);
                    $app->redirect($app->urlFor('user.profile.home'));
                } else {
                    $app->flash('errors', "Greška kod unosa u bazu.\n" . $status["err"] . "\nPokušajte ponovno");
                    $app->redirect($app->urlFor('user.profile.password-change'));
                }
            }
        })->name('user.profile.password-change.post');




        $app->get('/podaci', $authenticated_user(), function () use ($app) {
            $app->render('user/profile/user.profile_data_change.twig', array(
                'active_page' => "user.profile",
                'active_item' => "user.profile.profile-data-change"));
        })->name('user.profile.profile-data-change');

        $app->post('/podaci', $authenticated_user(), function () use ($app) {
            $p_email = $app->request->post('email');
            $p_first_name = $app->request->post('first-name');
            $p_last_name = $app->request->post('last-name');
            $p_sex = $app->request->post('sex');

            $validation_result = User::validateProfileDataChange($p_email, $app->auth_user,
                $p_first_name, $p_last_name, $p_sex);

            if(!($validation_result["validated"])) {
                // valudation failed
                if(isset($validation_result["errors"])) {
                    $app->flash('errors',  $validation_result["errors"]);
                }
                $app->redirect($app->urlFor('user.profile.profile-data-change'));
            } else {
                // Validation of input data successful
                if ($app->auth_user->updateProfileData($p_email, $p_first_name, $p_last_name, $p_sex)) {
                    Logger::logUserProfileDataChange($app->auth_user);
                    $app->flash('success', "Uspješna promjena osobnih podataka!");
                    $app->redirect($app->urlFor('user.profile.home'));
                } else {
                    $app->flash('errors', "Greška kod unosa u bazu.\nPokušajte ponovno");
                    $app->redirect($app->urlFor('user.profile.profile-data-change'));
                }
            }
        })->name('user.profile.profile-data-change.post');




        $app->get('/avatar', $authenticated_user(), function () use ($app) {
            $app->render('user/profile/user.avatar_change.twig', array(
                'user' => $app->auth,
                'active_page' => "user.profile",
                'active_item' => "user.profile.avatar-change"
            ));

        })->name('user.profile.avatar-change');



        $app->post('/avatar', $authenticated_user(), function () use ($app) {
            $p_uploaded_img_hash = $app->request->post('uploaded-img-hash');
            $p_delete_avatar = $app->request->post('delete-avatar');
            $p_use_fb_avatar = $app->request->post('use-fb-avatar');
            $p_fb_id = $app->request->post('fb-id');
            if(isset($p_delete_avatar) && $p_delete_avatar === "on") {
                if($app->auth_user->deleteOldImages() &&
                    $app->auth_user->unsetFacebookAvatarUsage()) {
                    $app->flash('success', "Uspješno brisanje avatara.");
                    Logger::logUserAvatarDelete($app->auth_user);
                    $app->redirect($app->urlFor('user.profile.home'));
                } else {
                    $app->flash('errors', "Greška kod unosa u bazu (:1).\nPokušajte ponovno");
                    $app->redirect($app->urlFor('user.profile.avatar-change'));
                }

            } else {
                if(isset($p_use_fb_avatar) && $p_use_fb_avatar === "on") {
                    if(!isset($p_fb_id) || $p_fb_id === "") {
                        $app->flash('errors', "Odabrano je postavljanje avatara na FB profilnu sliku, ali nije unesen i Facebook identifikator!\nPokušajte ponovno");
                        $app->redirect($app->urlFor('user.profile.avatar-change'));
                    } else {
                        if($app->auth_user->updateFacebookAvatarUsage(1, trim($p_fb_id))) {
                            $app->flash('success', "Uspješna promjena avatara.\nKao avatar koristit će se profilna slika Facebook profila koji ste unijeli.\n" .
                                $app->auth_user->getFacebookAvatarURL());
                            Logger::logUserAvatarFacebookChange($app->auth_user);
                            if(!$app->auth_user->imageHosted($app->auth_user->facebookAvatarExists())) {
                                $app->flash("statuses", "Profilna slika za zadani Facebook identifikator nije pronađena!");
                            } else {
                                $app->flash("statuses", "Profilna slika za zadani Facebook identifikator je pronađena!");
                            }
                            $app->redirect($app->urlFor('user.profile.home'));
                        } else {
                            $app->flash('errors', "Greška kod unosa u bazu (:2) \n {$p_fb_id}.\nPokušajte ponovno");
                            $app->redirect($app->urlFor('user.profile.avatar-change'));
                        }
                    }

                } else {
                    $img = Image::getImageByHash($p_uploaded_img_hash);
                    if($img) {
                        // delete old images for user
                        $app->auth_user->deleteOldImages();
                        $status = $img->assignImageToEntity($app->auth_user->id, "user", "avatar");
                        if($status["success"]) {
                            $app->flash('success', "Uspješna promjena avatara.");
                            Logger::logUserAvatarChange($app->auth_user);
                            $app->redirect($app->urlFor('user.profile.home'));
                        } else {
                            $app->flash('errors', "Greška kod unosa u bazu.\n" . $status["err"] . "\nPokušajte ponovno");
                            $app->redirect($app->urlFor('user.profile.avatar-change'));
                        }
                    } else {
                        $app->flash('errors', "Greška kod dohvata uploadane slike.\nPokušajte ponovno");
                        $app->redirect($app->urlFor('user.profile.avatar-change'));
                    }
                }
            }


        })->name('user.profile.avatar-change.post');


        $app->post('/avatar-changed', $authenticated_user(), function () use ($app) {
            if($app->request->isAjax()) {
                $body = $app->request->getBody();
                $json_data_received = json_decode($body, true);

                $input_data = $json_data_received["img-data"];

                $p_version = (isset($input_data['version'])) ? (int)$input_data['version'] : null;
                $p_width = (isset($input_data['width'])) ? (int)$input_data['width'] : null;
                $p_height = (isset($input_data['height'])) ? (int)$input_data['height'] : null;

                $p_public_id = (isset($input_data['public_id'])) ? $input_data['public_id'] : null;

                $p_format = (isset($input_data['format'])) ? $input_data['format'] : null;
                $p_resource_type = (isset($input_data['resource_type'])) ? $input_data['resource_type'] : null;

                $p_type = (isset($input_data['type'])) ? $input_data['type'] : null;
                $p_etag = (isset($input_data['etag'])) ? $input_data['etag'] : null;
                $p_url = (isset($input_data['url'])) ? $input_data['url'] : null;
                $p_secure_url = (isset($input_data['secure_url'])) ? $input_data['secure_url'] : null;
                $p_orig_filename = (isset($input_data['original_filename'])) ?  $input_data['original_filename'] : null;
                $p_path = (isset($input_data['path'])) ? $input_data['path'] : null;
                $p_moderated = (isset($input_data['moderated'])) ? (int)$input_data['moderated'] : null;

                try {
                    list($status, $img) = Image::createNew($p_public_id, $p_version, $p_width, $p_height, $p_format, $p_url, $p_secure_url,
                        $p_resource_type, $p_type, $p_etag, $p_orig_filename, $p_path, $p_moderated);
                    if ($status["success"] == true && $img) {
                        header('Content-Type: application/json');
                        echo json_encode(array("hash" => $img->hash));
                    } else {
                        header('Content-Type: application/json');
                        if(isset($status["err"])) {
                            $error = "Greška (:1) - " . $status["err"];
                        } else {
                            $error = "Greška (:1.1) - Nepotpun dohvat slike.";
                        }
                        echo json_encode(array(
                            "error" => $error,
                            "img" => $img,
                            "sent_data" => $input_data
                        ));
                    }
                } catch(\Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        "error" =>"Greška (:2) - " . $e->getMessage() . "",
                        "sent_data" => $input_data
                    ));
                }
                exit();
            }


        })->name('user.profile.avatar-change.changed.post');




//        $app->get('/ikona', $authenticated(), function () use ($app) {
//            $app->render('user/user.avatar.thumb.twig', array(
//                'user' => $app->auth
//            ));
//
//        })->name('user.avatar.thumb');



//        $app->post('/ikona', $authenticated_user(), function () use ($app) {
//            $p_width_ratio = $app->request->post('width_ratio');
//            $p_height_ratio = $app->request->post('height_ratio');
//            $p_top = $app->request->post('top');
//            $p_left = $app->request->post('left');
//
//
//
//            if(!($p_width_ratio) || !($p_height_ratio) || !($p_top) || !($p_left)) {
//                $app->flash("errors", "Došlo je do pogreške. Molimo pokušajte ponovno.");
//                $app->response()->redirect($app->urlFor('user.avatar.thumb'));
//            }
//            else {
//                if(($p_width_ratio) === "" || ($p_height_ratio)  === ""
//                    || ($p_top)  === "" || ($p_left)  === "") {
//                    $app->flash("errors", "Došlo je do pogreške. Molimo pokušajte ponovno.");
//                    $app->response()->redirect($app->urlFor('user.avatar.thumb'));
//                }
//                else {
//                    $user = User::getUserById($app->auth->id);
//                    $user->checkAvatarDir();
//
//                    $width_ratio = floatval($p_width_ratio);
//                    $heigth_ratio = floatval($p_height_ratio);
//                    $top = floatval($p_top);
//                    $left = floatval($p_left);
//
//                    $main_img_dir = Cfg::read('path.user.avatar') . "/" . $user->id . "/";
//                    $full_img_dir = $main_img_dir . Cfg::read('path.images.full') . "/";
//                    $full_img_path = $full_img_dir . "avatar" . $user->avatar_ext;
//
//                    list ($full_img_w, $full_img_h) = ImagesHandler::imgDimensions($full_img_path);
//                    var_dump($full_img_w); echo "<br>";
//                    var_dump($full_img_h); echo "<br>";
//
//                    $final_w = $full_img_w/$width_ratio;
//                    $final_h = $full_img_h/$heigth_ratio;
//
//                    $resized_img_name = "avatar_resized";
//
//                    $resized_img_dir = $main_img_dir . Cfg::read('path.images.full') . "/";
//                    $resized_img_path = $resized_img_dir . $resized_img_name . $user->avatar_ext;
//                    ImagesHandler::saveResized($full_img_path, $resized_img_dir, $resized_img_name,
//                        array($final_w, $final_h), true);
//
//                    $thumbs_name = "avatar";
//                    $thumb1_dir = $main_img_dir . Cfg::read('path.images.t1') . "/";
//
//                    $thumb2_dir = $main_img_dir . Cfg::read('path.images.t2') . "/";
//                    $thumb2_path = $thumb2_dir . $thumbs_name . $user->avatar_ext;
//
//                    ImagesHandler::saveCropped($resized_img_path, $thumb2_dir, $thumbs_name, array(
//                        intval($left),intval($top),
//                        Cfg::read('user.avatar.t2.width'), Cfg::read('user.avatar.t2.height'))
//                    );
//
//                    ImagesHandler::saveResized($thumb2_path, $thumb1_dir , $thumbs_name,
//                        array(Cfg::read('user.avatar.t1.width'), Cfg::read('user.avatar.t1.height')));
//
//                    unlink($resized_img_path);
//
//
//                    $app->flash("success", "Uspješan odabir ikone na temelju avatara!");
//                    $app->flash("statuses", "Ukoliko i dalje vidite staru ikonu u izborniku dolje lijevo, pokušajte ručno osvježiti stranicu:\n" .
//                        "(F5 / CTRL + R / refresh / reload)\nVaš preglednik privremeno pohranjuje sadržaj ikone ".
//                        "prema adresi koja ostaje nepromijenjena i kada se ikona promijeni");
//                    $app->response()->redirect($app->urlFor('user.avatar.thumb'));
//
//                }
//
//            }
//
////            exit();
//        })->name('user.thumb_ul.post');


    });

});


?>
