<?php

namespace App\Controllers;

use \Core\View;
use \Core\Mail;
use \Core\Auth;
use \App\Models\Globtrotter;
use \App\Models\Excursion;
use \App\Models\Partecipant;
use \App\Models\Cicerone;
use \App\Models\Feedback;
use \Core\Flash;


class RequestExcursion extends \Core\Controller
{

    public $getTo;

    public function sendRequestAction()
    {

      $excursion = new Excursion($_GET);

      $user = new Globtrotter();


      if(isset(Auth::getUser()->id))
      {
        if($_GET['id_globtrotter'] == Auth::getUser()->id)
        {
          if($user->sendPartecipationRequest($excursion) == true)
          {
            Flash::addMessage('Richiesta inoltrata');

            $this->redirect('/');
          }
          else {
            Flash::addMessage('Errore di invio richiesta');

            $this->redirect('/');
          }
        }
      }
      else {
        $this->redirect('/login');
      }
    }


    public function excursionsOfferedAction()
    {
      $excursions = Excursion::getAllExcursionsCicerone(Auth::getUser()->id);

      View::renderTemplate('Excursion/excursions_cicerone.html',[
          'excursions' => $excursions
      ]);
    }

    public function excursionsBookedAction()
    {
      $excursions = Partecipant::getAllExcursionsGlobtrotterActive(Auth::getUser()->id);

      $excursions2 = Partecipant::getAllExcursionsGlobtrotterPassed(Auth::getUser()->id);

      $foo = false;
      $feedbacks = Feedback::checkFeedback(Auth::getUser()->id);

      if(!empty($feedbacks))
      {
        $foo = true;
      }

      $i = 0;

      foreach($excursions as $excursion)
      {
        $excursions_parsed = Partecipant::getAllExcursionsGlobtrotterActiveParsing($excursions[$i]['id_cicerone']);
        $excursions[$i]['profile_picture'] = base64_encode($excursions_parsed[0]['profile_picture']);
        $i++;
      }

      $i = 0;

      foreach($excursions2 as $excursion)
      {
        $excursions_parsed2 = Partecipant::getAllExcursionsGlobtrotterActiveParsing($excursions2[$i]['id_cicerone']);
        $excursions2[$i]['profile_picture'] = base64_encode($excursions_parsed2[0]['profile_picture']);
        $i++;
      }

      View::renderTemplate('Excursion/excursions_offered.html',[
          'excursions2' => $excursions2,
          'excursions' => $excursions,
          'foo' => $foo,
          'feedbacks' => $feedbacks
      ]);
    }

    public function modifyExcursionAction()
    {
        $excursion = new Excursion($_POST);
        $user = new Cicerone();

        if($user->modifyExcursion($excursion))
        {
            Flash::addMessage('L escursione è stata modificata');

            $this->redirect('/excursion/offered');

        }
    }
    public function removeExcursionAction()
    {

        $excursion = new Excursion($_POST);
        $user = new Cicerone();

        if($user->removeExcursion($excursion->id_excursion))
        {
            Flash::addMessage('L escursione è stata rimossa');

            $this->redirect('/excursion/offered');

        }
    }

    public function showGlobtrottersAction()
    {
      $user = new Cicerone();

      $id_excursion = $this->route_params['globtrotters'];

      $excursions = $user->getGlobtrottersByExcursion($id_excursion);
      $i = 0;

      foreach($excursions as $excursion)
      {
        $excursions[$i]['profile_picture'] = base64_encode($excursion['profile_picture']);
        $i++;
      }

      View::renderTemplate('Globtrotter/show_globtrotters.html',[
          'excursions' => $excursions
      ]);
    }

    public function acceptRequestAction()
    {

      $user = new Cicerone();

      $user->acceptRequestGlobetrotter($_GET['id_partecipant']);

      Flash::addMessage('Richiesta accettata');

      $this->redirect('/excursion/offered/globtrotters/'.$_GET['id_excursion']);

    }

    public function deleteGlobtrotterAction()
    {

      $user = new Cicerone($_POST);

      $user->deleteAcceptedRequest($user->id_globtrotter,$user->id_excursion, $user->id_partecipant);

      Flash::addMessage('Hai rimosso la richiesta di partecipazione');

      $this->redirect('/excursion/booked');

    }

    public function IdeleteGlobtrotterAction()
    {

      $user = new Globtrotter($_POST);

      $user->deleteSentRequestGlobtrotter($user->id_globtrotter,$user->id_excursion, $user->id_partecipant);

      Flash::addMessage("Hai rimosso il globtrotter dall'escursione");

      $this->redirect('/excursion/offered');

    }

    public function displayAction()
    {

      View::renderTemplate('Excursion/excursions_offered.html');
    }

}
