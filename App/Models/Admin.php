<?php

namespace App\Models;

use PDO;
use \Core\Token;
use \Core\Mail;
use \Core\View;
use \App\Models\Excursion;
use \App\Models\UserSignedIn;

class Admin extends \App\Models\UserSignedIn
{

  public function __construct($data = [])
  {
      foreach ($data as $key => $value) {
          $this->$key = $value;
      };
  }

  public function handleExcursions($destination, $type)
  {
    return Excursion::getAllExcursionsBy($destination, $type);
  }

  public function handleUsersAccount($email)
  {
    return UserSignedIn::getAllUsersByEmail($email);
  }

  public function deleteExcursion($id_excursion)
  {
    return Excursion::removeExcursion($id_excursion);
  }

  public function deleteAccount($email)
  {
    return UserSignedIn::deleteUserByEmail($email);
  }

}
