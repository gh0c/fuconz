<?php

namespace app\model\Middleware;

use app\helpers\Text;
use Slim\Middleware;
use app\helpers\Configuration;
use app\helpers\Sessions;
use app\helpers\Auth;
use app\model\User\User;
use app\model\Admin\Admin;
use app\model\Content\Jukebox;


class BeforeMiddleware extends Middleware
{
    public function call()
    {
        $this->app->hook('slim.before', array($this, 'run'));
        $this->next->call();
    }

    public function run()
    {

        if (Sessions::get(Configuration::read('session.user_logged_in')))  {
            $this->app->auth_user = User::getUserById(Sessions::get(Configuration::read('session.logged_user_id')));
        }
        $this->checkUserRememberMe();

        $error_msgs = Sessions::get(Configuration::read('session.status.error'));
        $success_msgs = Sessions::get(Configuration::read('session.status.success'));
        $status_msgs = Sessions::get(Configuration::read('session.status.neutral'));

        Sessions::set(Configuration::read('session.status.error'), null);
        Sessions::set(Configuration::read('session.status.success'), null);
        Sessions::set(Configuration::read('session.status.neutral'), null);

        $this->app->view()->appendData(array(
            'user_error_msgs' => $error_msgs,
            'user_success_msgs' => $success_msgs,
            'user_status_msgs' => $status_msgs,
            'auth_user' => $this->app->auth_user));

        if(Sessions::get(Configuration::read('session.admin_logged_in'))) {
            $this->app->auth_admin = Admin::getAdminById(Sessions::get(Configuration::read('session.logged_admin_id')));
        }
        $this->checkAdminRememberMe();

        $admin_error_msgs = Sessions::get(Configuration::read('session.admin.status.error'));
        $admin_success_msgs = Sessions::get(Configuration::read('session.admin.status.success'));
        $admin_status_msgs = Sessions::get(Configuration::read('session.admin.status.neutral'));

        Sessions::set(Configuration::read('session.admin.status.error'), null);
        Sessions::set(Configuration::read('session.admin.status.success'), null);
        Sessions::set(Configuration::read('session.admin.status.neutral'), null);

        $this->app->view()->appendData(array(
            'admin_error_msgs' => $admin_error_msgs,
            'admin_success_msgs' => $admin_success_msgs,
            'admin_status_msgs' => $admin_status_msgs,
            'auth_admin' => $this->app->auth_admin));


        // jukebox with quotes and config class with static methods
        $this->app->view()->appendData(array(
            'jukebox' => new Jukebox($this->app->auth_user),
            'config' => new Configuration(),
            'text_help' => new Text()
        ));
    }

    protected function checkUserRememberMe() {
        if(isset($_COOKIE[Configuration::read('cookie.user_remember_me')]) &&
            !$this->app->auth_user) {

            $data = $_COOKIE[Configuration::read('cookie.user_remember_me')];
            $user = Auth::rememberMeCookieUserLogin($data);
            if($user) {
                $this->app->auth_user = $user;
            }
        }
    }

    protected function checkAdminRememberMe() {
        if(isset($_COOKIE[Configuration::read('cookie.admin_remember_me')]) &&
            !$this->app->auth_admin) {

            $data = $_COOKIE[Configuration::read('cookie.admin_remember_me')];
            $admin = Auth::rememberMeCookieAdminLogin($data);
            if($admin) {
                $this->app->auth_admin = $admin;
            }
        }
    }
}
