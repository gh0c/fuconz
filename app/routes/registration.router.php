<?php


use \app\model\User\User;
use \app\model\Messages\Logger;


$app->get('/clanovi/registracija', $guest_user(), function () use ($app) {
    $app->render('authentication/registration.twig', array(
    ));
})->name('user.registration');



$app->post('/clanovi/registracija', $guest_user(), function() use ($app) {
    $p_email = $app->request->post('email');
    $p_username = $app->request->post('username');
    $p_password = $app->request->post('new-password');
    $p_password_repeated = $app->request->post('new-password-repeated');
    $p_first_name = $app->request->post('first-name');
    $p_last_name = $app->request->post('last-name');
    $p_sex = $app->request->post('sex');

    $validation_result = User::validateNew($p_username, $p_email, $p_password, $p_password_repeated,
        $p_first_name, $p_last_name, $p_sex);

    if(!($validation_result["validated"])) {
        // valudation failed
        if(isset($validation_result["errors"])) {
            $app->flash('errors',  $validation_result["errors"]);
        }
        $app->redirect($app->urlFor('user.registration'));
    } else {
        // Validation of input data successful
        list($status, $new_user) = User::createNew($p_username, $p_email, $p_password, $p_first_name, $p_last_name, $p_sex);
        if ($status["success"] == true && $new_user) {
            Logger::logNewUserRegistration($new_user);
            $app->flash('success', "Uspješna registracija!");
            $app->flash('statuses', "Sada se možete prijaviti koristeći unesene podatke.");
            $app->redirect($app->urlFor('user.login'));
        } else {
            $app->flash('errors', "Greška kod unosa u bazu.\n" . $status["err"] . "\nPokušajte ponovno");
            $app->redirect($app->urlFor('user.registration'));
        }
    }

})->name('user.registration.post');





