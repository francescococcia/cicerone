<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\UserNotSignedIn;
use \App\Models\Cicerone;
use \Config\Config;
use \Core\Auth;
use \Core\Flash;


class Signup extends \Core\Controller
{


    public function registrationAction()
    {
        $this->isUserLoggedInAlready();

        View::renderTemplate('Signup/registration.html');
    }

    public function createAction()
    {
        $user = new UserNotSignedIn($_POST);

        if ($user->save()) {

            $user->sendConviladationAccountEmail();

            Flash::addMessage('Account registrato con successo, convalidalo tramite il link inviato nell\'email');

            $this->redirect('/login');

        } else {

           View::renderTemplate('Signup/registration.html');
        }
    }


    public function accountActivationAction()
    {
        UserNotSignedIn::activate($this->route_params['token']);

        Flash::addMessage('Account attivato con successo');

        $this->redirect('/login');
    }

    public function testAction()
    {
      View::renderTemplate('Signup/rating.html');
    }
}
