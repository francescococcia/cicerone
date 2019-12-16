<?php

namespace App\Models;

use PDO;
use \Core\Token;
use \Core\Mail;
use \Core\View;

abstract class User extends \Core\Model
{

    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
    
}
