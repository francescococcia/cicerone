<?php

namespace App\Controllers;

use \App\Models\UserSignedIn;
use \Core\Auth;
use \Core\Flash;


class Account extends \Core\Controller
{

    public function validateEmailAction()
    {
        $is_valid = ! UserSignedIn::emailExists($_GET['email']);

        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    public function validatePasswordAction()
    {
        $is_valid = UserSignedIn::checkPassword($_GET['old_password'], Auth::getUser()->password_hash);

        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    public function deleteUserAccountAction()
    {
        $user = Auth::getUser();

        if($user->deleteUser())
        {

          Flash::addMessage('L account è stato cancellato con successo');

          $this->redirect('/');
        }
    }
}
