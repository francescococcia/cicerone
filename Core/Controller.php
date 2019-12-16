<?php

namespace Core;

use \Core\Auth;
use \Config\Config;


abstract class Controller
{

    protected $route_params = [];

    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }


    protected function before()
    {
    }

    protected function after()
    {
    }

    public function redirect($url = "")
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . Config::ROOT_DIR . $url, true, 303);
        exit;
    }

    public function requireLogin()
    {
        if(!Auth::getUser())
        {
            Auth::rememberRequestedPage();

            $this->redirect('/login');
        }
    }

    public function requireAdmin()
    {
        if(Auth::getUser()->user_role == false)
        {
            $this->redirect('/');

            exit;
        }
    }

    public function isUserLoggedInAlready()
    {
        if(Auth::getUser())
        {
            $this->redirect("/");
            exit;
        }
    }
}
