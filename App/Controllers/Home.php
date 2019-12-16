<?php

namespace App\Controllers;

use \Core\View;
use \Core\Mail;
use \Core\Auth;
use \App\Models\Excursion;
use \App\Models\Partecipant;
use \Core\Flash;


class Home extends \Core\Controller
{

    public function indexAction()
    {
        View::renderTemplate('Home/index.html');
    }


	public function findExcursionByAction()
  {
        View::renderTemplate('Excursion/searching.html');
  }

  public function addExcursionAction()
  {
    	$this->requireLogin();

      View::renderTemplate('Excursion/excursion_add.html');
  }

  public function resultSearchingExcursionsAction()
  {
    $excursion = new Excursion($_GET);
    $excursions = Excursion::getAllExcursionsBy($excursion->destination, $excursion->type, $excursion->meeting_date);
    $partecipants = Partecipant::getGlobtrotters();
    $i = 0;
    $j = 0;
    $globtrotters = [];

    foreach ($excursions as $excursion)
    {
      $excursions[$i]['profile_picture'] = base64_encode($excursion['profile_picture']);
      foreach($partecipants as $partecipant)
        {
          if($excursions[$i]['id_excursion'] == $partecipants[$j]['id_excursion'])
          {
            $globtrotters[$j] = $partecipants[$j]['id_globtrotter'];
            $excursions[$i]['id_globtrotter'] = $globtrotters;
          }
          $j++;
        }
        unset($globtrotters);
        $globtrotters = [];
        $j = 0;
        $i++;
    }
    View::renderTemplate('Excursion/excursions_found.html',[
        'excursions' => $excursions,
        'foo' => ""
    ]);

  }

    public function addAction()
    {
      $excursion = new Excursion($_POST);

      $user = Auth::getUser();

      if ($excursion->save($user)) {

          Flash::addMessage('Escursione inserita');

          $this->redirect('/');
      }
    }
}
