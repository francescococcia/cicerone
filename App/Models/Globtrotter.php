<?php

namespace App\Models;

use PDO;
use \Core\Token;
use \Core\Mail;
use \Core\View;

class Globtrotter extends \App\Models\UserSignedIn
{

  public function __construct($data = [])
  {
      foreach ($data as $key => $value) {
          $this->$key = $value;
      };
  }

  public function sendPartecipationRequest($excursion)
  {
      return Excursion::sendPartecipationRequest($excursion);
  }

  public function deleteSentRequestGlobtrotter($id_globetrotter,$id_excursion, $id_partecipant)
  {
      return Partecipant::deleteSentRequestGlobtrotter($id_globetrotter,$id_excursion, $id_partecipant);
  }
}
