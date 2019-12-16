<?php

namespace App\Models;

use PDO;
use \Core\Token;
use \Core\Mail;
use \Core\View;


 class UserSignedIn extends \App\Models\User
{

    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
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

    public function deleteUser()
    {
      $user_role = true;

      $sql = 'DELETE FROM user
              WHERE id = :id';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

      return $stmt->execute();
    }

    public static function getAllUsers()
    {
        $sql = 'SELECT * FROM user';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function deleteUserByEmail($email)
    {

        $sql = 'DELETE FROM user
                WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function getAllUsersByEmail()
    {
        $sql = 'SELECT email FROM user
                WHERE user_role = 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function findUserByID($id)
    {
        $sql = 'SELECT * FROM user WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        return $user;
    }

    public static function findByID($id)
    {
        $sql = 'SELECT * FROM user WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function checkPassword($password, $password_hash)
    {
      if (password_verify($password, $password_hash)) {
          return true;
      }
      return false;
    }

    public function updateUserDetails($user,$id)
    {

        $date = date("Y-m-d", strtotime(str_replace('/', '-', $user->birthday)));

        $sql = 'UPDATE user
                SET name = :name,
                    surname = :surname,
                    birthday = :birthday,
                    self_phone = :self_phone,
                    description = :description
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':name', $user->name, PDO::PARAM_STR);
        $stmt->bindValue(':surname', $user->surname, PDO::PARAM_STR);
        $stmt->bindValue(':birthday', $date, PDO::PARAM_STR);
        $stmt->bindValue(':self_phone', $user->self_phone, PDO::PARAM_STR);
        $stmt->bindValue(':description', $user->description, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function emailExists($email, $ignore_id = null)
    {
        $user = static::findByEmail($email);

        if($user)
        {
            if($user->id != $ignore_id)
            {
                return true;
            }
        }
        return false;
    }

    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM user WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public function updateProfilePicture($profile_picture)
    {
        $sql = 'UPDATE user
                       SET profile_picture = :profile_picture
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $imagedata = fopen($profile_picture, 'rb');

        $stmt->bindValue(':profile_picture', $imagedata, PDO::PARAM_LOB);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function startPasswordReset()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->password_reset_token = $token->getToken();

        $expiry_timestamp = time() + (60 * 30);

        $sql = 'UPDATE user
                SET password_reset_hash = :hashed_token,
                    password_reset_expiry = :expires_at
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
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

    public function sendPasswordResetEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;

        $html = View::getTemplate('Password/reset_email.html',['url' => $url]);

        Mail::send($this->email, "Password reset", $html);
    }

    public function getProfilePicture()
    {
      $sql = "SELECT profile_picture FROM user WHERE id = :id";

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

      $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

      $stmt->execute();

      return $stmt->fetch();
    }

    public function enableAdmin($user_id)
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getToken();

        $sql = 'UPDATE user set user_role :hashed_token where id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_role', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getToken();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30; 

        $sql = 'INSERT INTO remembered_login (token_hash, user_id, expires_at)
                VALUES(:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

}
