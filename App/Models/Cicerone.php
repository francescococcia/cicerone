<?php

namespace App\Models;

use PDO;
use \Core\Token;
use \Core\Mail;
use \Core\View;
use \App\Models\Excursion;
use \App\Models\Partecipant;
/**
 * User model
 *
 * PHP version 7.0
 */
class Cicerone extends \App\Models\UserSignedIn
{

  public function __construct($data = [])
  {
      foreach ($data as $key => $value) {
          $this->$key = $value;
      };
  }

  public function getGlobtrottersByExcursion($id_excursion)
  {
    return Partecipant::getGlobtrottersByExcursion($id_excursion);
  }

  public function modifyExcursion($excursion)
  {
    return Excursion::modifyExcursion($excursion);
  }

  public function removeExcursion($id_excursion)
  {

    return Excursion::removeExcursion($id_excursion);
  }

  public function acceptRequestGlobetrotter($id_partecipant)
  {

    return Partecipant::acceptRequestGlobetrotter($id_partecipant);
  }

  public function deleteAcceptedRequest($id_globetrotter,$id_excursion, $id_partecipant)
  {

      return Partecipant::deleteAcceptedRequest($id_globetrotter,$id_excursion, $id_partecipant);
  }

}
