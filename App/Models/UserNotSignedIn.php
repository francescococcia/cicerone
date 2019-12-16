<?php

namespace App\Models;

use PDO;
use \Core\Token;
use \Core\Mail;
use \Core\View;
use \App\Models\UserSignedIn;

 class UserNotSignedIn extends \App\Models\User
{

    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function save()
    {
        $this->validateSignup();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getToken();

            $date = date("Y-m-d", strtotime(str_replace('/', '-', $this->birthday)));

            $user_role = false;

            $sql = 'INSERT INTO user (name,surname, email, birthday, sex, self_phone, password_hash, activation_hash, user_role)
                    VALUES (:name, :surname, :email, :birthday, :sex, :self_phone, :password_hash, :activation_hash, :user_role)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':surname', $this->surname, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':birthday', $date, PDO::PARAM_STR);
            $stmt->bindValue(':sex', $this->sex, PDO::PARAM_STR);
            $stmt->bindValue(':self_phone', $this->self_phone, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);
            $stmt->bindValue(':user_role', $user_role, PDO::PARAM_BOOL);

            return $stmt->execute();
        }

        return false;
    }

    public function validateSignup()
    {
        $today = date('m/d/Y', time());
        $today_dt = strtotime($today);
        $birthday_dt = strtotime($this->birthday);
        $min = date("01/01/1900");
        $min_dt = strtotime($min);
        $minum_age = strtotime ( '-18 year', $today_dt);
        $minum_age_dt = date('m/d/Y', $minum_age);

        if ($this->name == '') {
            $this->errors[] = 'Name is required';
        }

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Invalid email';
        }
        if (UserSignedIn::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'email already taken';
        }

        if (strlen($this->password) < 6) {
            $this->errors[] = 'Please enter at least 6 characters for the password';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'Password needs at least one letter';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'Password needs at least one number';
        }
        if($this->password != $this->password_confirmation)
        {
          $this->errors[] = 'Password do not corrispond';
        }
        if(!preg_match('/^[0-9]{10}+$/', $this->self_phone))
        {
          $this->errors[] = 'The phone number is invalid';
        }
        if($birthday_dt > $today_dt or $birthday_dt < $min_dt)
        {
          $this->errors[] = 'The is an error in the date';
        }
        elseif ($birthday_dt > $minum_age)
        {
          $this->errors[] = 'You cannot be a minor';
        }
        if($this->sex != 'Uomo')
        {
          if($this->sex != 'Donna')
          {
            $this->errors[] = 'The sex is incorrect';
          }
        }
    }

    public static function authenticate($email, $password)
    {
        $user = UserSignedIn::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }
        return false;
    }

    public function findUserByEmail($email)
    {
        $sql = 'SELECT * FROM user WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        return $user;
    }

    public function resetPassword($password)
    {

        $this->password = $password;

        $this->validateSignup();

        if(empty($this->errors))
        {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE user
                    SET password_hash = :password_hash,
                        password_reset_hash = NULL,
                        password_reset_expiry = NULL
                    WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);


            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);


            return $stmt->execute();
        }
        return false;
    }

    public static function sendPasswordReset($email)
    {
        $user = UserSignedIn::findByEmail($email);

        if($user)
        {
            if($user->startPasswordReset())
            {
                $user->sendPasswordResetEmail();
            }
        }
    }

    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM user
                WHERE password_reset_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        if($user)
        {
            if(strtotime($user->password_reset_expiry) > time() )
            {
                return $user;
            }
        }
    }

    public function sendConviladationAccountEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/registration/activate/' . $this->activation_token;

        $html = View::getTemplate('Signup/accountActivation_email.html',['url' => $url]);

        Mail::send($this->email, "Account activation", $html);
    }

  public static function activate($token)
    {
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE user
                SET is_active = 1,
                    activation_hash = null
                WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->execute();
    }

    public static function getPublicUserProfile($id)
    {
      $user = UserSignedIn::findByID($id);

      if($user)
      {
        return $user;
      }
      return null;
    }

}
