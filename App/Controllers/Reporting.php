<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\UserSignedIn;
use \App\Models\UserNotSignedIn;
use \Core\Auth;
use \Core\Flash;
use \Core\Mail;


class Reporting extends \Core\Controller
{

    public function reportAction()
    {
      $this->requireLogin();

      View::renderTemplate('Admin/report.html');
    }

    public function sendticketAction()
    {

      $user = Auth::getUser();
      $body = $user->name.",".$_GET['body'];

      $admins = UserSignedIn::getAllUsersByEmail();

      for ($i = 0; $i <= sizeof($admins)-1; $i++)
      {
          Mail::send($admins[$i]['email'],$_GET['subject'],$body);
      }

      Flash::addMessage('Segnalazione inviata');

      $this->redirect('/send/ticket');
    }
}
