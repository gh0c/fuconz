<?php

use app\helpers\Configuration;

// Session keys
Configuration::write('session.user_logged_in', 's_user_logged_in');
Configuration::write('session.logged_user', 's_logged_user');
Configuration::write('session.logged_user_email', 's_logged_user_email');
Configuration::write('session.logged_user_id', 's_logged_user_id');

Configuration::write('session.admin_logged_in', 's_admin_logged_in');
Configuration::write('session.logged_admin', 's_logged_admin');
Configuration::write('session.logged_admin_email', 's_logged_user_email');
Configuration::write('session.logged_admin_id', 's_logged_admin_id');

Configuration::write('session.status.error', 's_status_error');
Configuration::write('session.status.neutral', 's_status_neutral');
Configuration::write('session.status.success', 's_status_success');

Configuration::write('session.admin.status.error', 's_admin_status_error');
Configuration::write('session.admin.status.neutral', 's_admin_status_neutral');
Configuration::write('session.admin.status.success', 's_admin_status_success');

Configuration::write('session.csrf_token', 's_nsn_csrf_token');

// Cookies (rememberMe)

Configuration::write('cookie.user_remember_me', 'registered_user_rm');
Configuration::write('cookie.admin_remember_me', 'admin_rm');
Configuration::write('cookie.path', '/');
Configuration::write('cookie.user_remember_me_duration', 60*24*60*60);
Configuration::write('cookie.admin_remember_me_duration', 60*24*60*60);
// Paths

Configuration::write('path.user.avatar', 'public/images/registered-users');



Configuration::write('repeating.interval.none', 'none');
Configuration::write('repeating.interval.day', 'day');
Configuration::write('repeating.interval.week', 'week');
Configuration::write('repeating.interval.month.1', 'month.1');
Configuration::write('repeating.interval.month.2', 'month.2');


// Images subpaths

Configuration::write('path.images.full', 'full');
Configuration::write('path.images.t1', 'thumb1');
Configuration::write('path.images.t2', 'thumb2');
Configuration::write('path.images.t3', 'thumb3');

// Avatar

Configuration::write('avatar.minimum.width', 55);
Configuration::write('avatar.minimum.height', 55);

Configuration::write('user.avatar.t1.height', 55);
Configuration::write('user.avatar.t1.width', 55);

Configuration::write('user.avatar.t2.height', 170);
Configuration::write('user.avatar.t2.width', 170);

Configuration::write('user.avatar.full.width', 700);
Configuration::write('user.avatar.full.height', 650);


Configuration::write('jpeg.quality', 100);


// Database defaults
Configuration::write('db.sex.male', 'M');
Configuration::write('db.sex.female', 'F');



Configuration::write('password.reset.duration', 2*60*60);


\Cloudinary::config(array(
    "cloud_name" => "dqpjjihsv",
    "api_key" => "246759957442647",
    "api_secret" => "3jQQ4ITKsKslrlwNucryALiYuU0"
));