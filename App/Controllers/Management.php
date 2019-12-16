<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Admin;
use \Core\Auth;
use \Core\Flash;


class Management extends \Core\Controller
{


     public function accountsAction()
     {
       $this->requireAdmin();

       if(!empty($_POST))
       {
         $admin = new Admin($_POST);
         $users = $admin->handleUsersAccount($admin->email);

       }
       else
       {
          $admin = new Admin();
          $users = $admin->handleUsersAccount(null);
       }

         View::renderTemplate('Admin/accounts.html',[
           "users" => $users
         ]);
     }

     public function excursionsAction()
     {
         $this->requireAdmin();

         if(!empty($_POST))
         {
           $admin = new Admin($_POST);
           $excursions = $admin->handleExcursions($admin->destination,$admin->type);
         }
         else
         {
            $admin = new Admin();
            $excursions = $admin->handleExcursions(null,null);
         }

         View::renderTemplate('Admin/excursions.html',[
           'excursions' => $excursions
         ]);
     }

    public function deleteUserAccountAction()
    {
      $admin = new Admin($_POST);

      if($admin->deleteAccount($_POST['email']))
      {
        Flash::addMessage("L'account è stato rimosso");
      }

      $this->redirect('/management/accounts');
    }

    public function deleteExcursionAction()
    {
       $admin = new Admin($_POST);


       if($admin->deleteExcursion($_POST['id_excursion']))
       {
         Flash::addMessage("L'escursione è stata rimossa");
       }

       $this->redirect('/management/excursions');
    }

}
