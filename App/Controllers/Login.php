<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\UserNotSignedIn;
use \Config\Config;
use \Core\Auth;
use \Core\Flash;
use \Core\Mail;

class Login extends \Core\Controller
{


    public function loginAction()
    {
        $this->isUserLoggedInAlready();

        View::renderTemplate('Login/login.html');
    }


    public function createAction()
    {
        $user = UserNotSignedIn::authenticate($_POST['email'], $_POST['password']);

        $remember_me = isset($_POST['remember_me']);

        if ($user)
        {
            if($user->is_active == 1)
            {
                Auth::login($user, $remember_me);

                Flash::addMessage('Login effettuato');

                $this->redirect('/');
            }
            else
            {
                Flash::addMessage('L\'account non è stato attivato, controlla la tua email', Flash::getWarningMessage());

                View::renderTemplate('login/login.html', [
                    'email' => $_POST['email'],
                    'remember_me' => $remember_me
                ]);
            }

        } else {

            Flash::addMessage('Controlla le tue credenziali e riprova', Flash::getWarningMessage());

            View::renderTemplate('login/login.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
            ]);


        }
    }

	public function loggatoAction()
    {
        View::renderTemplate('home/loggato.html');
    }

    public function adminAction()
    {
        $users = User::getAllUsers();

        View::renderTemplate('home/admin.html', [
        'users' => $users
        ]);
    }


    public function destroyAction()
    {
        
        Auth::logout();

        $this->redirect('/login/showLogoutMessage');

    }

    public function showLogoutMessageAction()
    {
      Flash::addMessage('Logout effettuato');

      $this->redirect('/');
    }
}
