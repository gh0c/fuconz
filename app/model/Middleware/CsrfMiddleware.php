<?php

namespace app\model\Middleware;

use Slim\Middleware;
use app\helpers\Configuration;
use app\helpers\Sessions;
use app\helpers\Hash;
use app\model\User\RegisteredUser;


class CsrfMiddleware extends Middleware
{
    protected $key;

    public function call()
    {
        $this->key = Configuration::read('session.csrf_token');
        $this->app->hook('slim.before', array($this, 'check'));
        $this->next->call();
    }

    public function check() {
        if(!Sessions::get($this->key)) {
            Sessions::set($this->key, Hash::hash(
                Hash::getMSG()->generateString(128)
            ));
        }
        $token = Sessions::get($this->key);

        if(in_array($this->app->request()->getMethod(), array('POST', 'PUT', 'DELETE'))) {
            $submittedToken = $this->app->request()->post($this->key) ?: '';
            if(!Hash::hashCheck($token, $submittedToken)) {
                throw new \Exception('CSRF token mismatch');
            }
        }

        $this->app->view()->appendData( array(
            'csrf_key' => $this->key,
            'csrf_token' => $token
        ));
    }


}