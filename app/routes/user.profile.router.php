<?php
use \app\model\User\RegisteredUser;
use app\helpers\Configuration as Cfg;
use \app\helpers\Hash;

$app->group('/clanovi', function () use ($app, $authenticated_user) {

    $app->group('/profil', $authenticated_user(), function () use ($app, $authenticated_user) {

        $app->get('/', $authenticated_user(), function () use ($app) {
            $app->render('user/user.profile.twig', array(
                'user' => $app->auth_user,
                'active_page' => 'user.profile',
                'active_item' => 'user.profile.home'));
        })->name('user.profile.home');


        $app->get('/:action', $authenticated_user(), function ($action) use ($app) {
            if(in_array($action, array('avatar', 'ikona', 'promjena-lozinke'))) {
                $app->pass();
            }
            else
            {
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

            $validation_result = RegisteredUser::validatePasswordChange($p_password, $p_password_new,
                $p_password_new_repeated, $app->auth_user);

            if(!($validation_result["validated"])) {
                // Validation failed
                if(isset($validation_result["errors"])) {
                    $app->flash('errors', $validation_result["errors"]);
                }
                $app->redirect($app->urlFor('user.profile.password_change'));
            } else {
                // Validation of input data successful

                if ($app->auth_user->updatePassword(Hash::password($p_password_new . $app->auth_user->getPasswordSalt()))) {
                    $app->flash('success', "Uspješna promjena lozinke.");
                    $app->redirect($app->urlFor('user.profile'));
                } else {
                    $app->flash('errors', "Greška kod unosa u bazu.\nPokušajte ponovno");
                    $app->redirect($app->urlFor('user.profile.password_change'));
                }
            }
        })->name('user.profile.password-change.post');


//        $app->get('/avatar', $authenticated_user(), function () use ($app) {
//            $app->render('user/user.avatar.twig', array(
//                'user' => $app->auth
//            ));
//
//        })->name('user.avatar');

//        $app->post('/avatar', $authenticated_user(), function () use ($app) {
//            $p_avatar_file = $_FILES['avatar_file'];
//            $p_delete_old = $app->request->post('delete_avatar');
//
//            if(isset($p_avatar_file) && $p_avatar_file["error"] == UPLOAD_ERR_OK) {
//                $error_status = ImagesHandler::validate($p_avatar_file, array(
//                    Cfg::read('avatar.minimum.width'),
//                    Cfg::read('avatar.minimum.height')
//                ));
//
//                if($error_status) {
//                    if(!isset($p_delete_old) || $p_delete_old != "on") {
//                        $app->flash("errors","Nije odabrano brisanje starog avatara " .
//                            "niti je odabrana ispravna slika za upload novog. \n{$error_status}");
//                        $app->response()->redirect($app->urlFor('user.avatar'));
//                    }
//                    else {
//                        $user = RegisteredUser::getUserById($app->auth->id);
//                        $user->deleteAvatarDir();
//                        $user->updateExtension();
//                        $app->flash("statuses", "Stari avatar je izbrisan. \n" .
//                            "$error_status");
//                        $app->response()->redirect($app->urlFor('user.avatar'));
//                    }
//                }
//                else {
//                    $user = RegisteredUser::getUserById($app->auth->id);
//                    $user->deleteAvatarDir();
//
//                    $user->checkAvatarDir();
//                    $img_ext = ImagesHandler::imageExt($p_avatar_file);
//                    $user->avatar_ext = $img_ext;
//                    $user->updateExtension();
//
//                    $main_img_dir = Cfg::read('path.user.avatar') . "/" . $user->id . "/";
//                    $full_img_dir = $main_img_dir . Cfg::read('path.images.full') . "/";
//
//                    ImagesHandler::saveUploadedImg($p_avatar_file, $full_img_dir, "temp");
//                    $full_img_path = $full_img_dir . "temp" . $img_ext;
//
//                    ImagesHandler::saveResized($full_img_path, $full_img_dir, "avatar",
//                        array(Cfg::read('user.avatar.full.width'),
//                            Cfg::read('user.avatar.full.height')));
//
//                    $t2_img_dir = $main_img_dir . Cfg::read('path.images.t2') . "/";
//                    ImagesHandler::saveResized($full_img_path, $t2_img_dir, "avatar",
//                        array(Cfg::read('user.avatar.t2.width'),
//                            Cfg::read('user.avatar.t2.height')));
//
//                    $t1_img_dir = $main_img_dir . Cfg::read('path.images.t1') . "/";
//                    ImagesHandler::saveResized($full_img_path, $t1_img_dir, "avatar",
//                        array(Cfg::read('user.avatar.t1.width'),
//                            Cfg::read('user.avatar.t1.height')));
//
//                    unlink($full_img_path);
//                    $app->flash('success', "Avatar je uspješno pohranjen! \n" .
//                        "Skalirajte i pozicionirajte sliku kako bi odgovarala dimenzijama ikone" .
//                        " koju želite.");
//                    $app->response()->redirect($app->urlFor('user.avatar.thumb'));
//
//
//
//                }
//            }
//            else if(!isset($p_delete_old) || $p_delete_old != "on") {
//                $app->flash("errors",
//                    "Nije odabrano brisanje starog avatara niti je odabrana slika za upload novog.");
//                $app->response()->redirect($app->urlFor('user.avatar'));
//            }
//            else {
//                $user = RegisteredUser::getUserById($app->auth->id);
//                $user->deleteAvatarDir();
//                $user->updateExtension();
//                $app->flash("success", "Uspješno izbrisan avatar!");
//                $app->response()->redirect($app->urlFor('user.avatar'));
//            }
//
//        })->name('user.avatar_ul.post');



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
//                    $user = RegisteredUser::getUserById($app->auth->id);
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
