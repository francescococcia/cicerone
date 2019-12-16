<?php

namespace App\Models;

use PDO;
use \Core\Mail;
use \Core\View;

class Feedback extends \Core\Model
{

    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }


    public function releaseFeedback()
    {

      $sql = 'INSERT INTO feedback (rating, comment, user, partecipant)
              VALUES (:rating, :comment, :id_cicerone, :id_partecipant)';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':rating', $this->rating, PDO::PARAM_INT);
      $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);
      $stmt->bindValue(':id_cicerone', $this->id_cicerone, PDO::PARAM_INT);
      $stmt->bindValue(':id_partecipant', $this->id_partecipant, PDO::PARAM_INT);

      return $stmt->execute();
    }

    public static function getAllFeedbackCicerone($id_cicerone)
    {

      $sql = 'SELECT user.name as user_name, user.profile_picture as profile_picture, feedback.comment as comment, partecipant.user as id_globtrotter from user
            inner join partecipant on partecipant.user=user.id inner join feedback on partecipant.id=feedback.partecipant
            WHERE feedback.user = :id_cicerone';

         $db = static::getDB();
         $stmt = $db->prepare($sql);

         $stmt->bindValue(':id_cicerone', $id_cicerone, PDO::PARAM_INT);

         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $stmt->execute();

         return $stmt->fetchAll();
     }


     public static function getAll1starFeedback($id_cicerone)
     {

       $sql = 'SELECT COUNT(rating) as one_star from user
             inner join partecipant on partecipant.user=user.id inner join feedback on partecipant.id=feedback.partecipant
             WHERE feedback.user = :id_cicerone AND rating = 1';

          $db = static::getDB();
          $stmt = $db->prepare($sql);

          $stmt->bindValue(':id_cicerone', $id_cicerone, PDO::PARAM_INT);

          $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

          $stmt->execute();

          return $stmt->fetch();
      }

      public static function getAll2starFeedback($id_cicerone)
      {

        $sql = 'SELECT COUNT(rating) as two_star from user
              inner join partecipant on partecipant.user=user.id inner join feedback on partecipant.id=feedback.partecipant
              WHERE feedback.user = :id_cicerone AND rating = 2';

           $db = static::getDB();
           $stmt = $db->prepare($sql);

           $stmt->bindValue(':id_cicerone', $id_cicerone, PDO::PARAM_INT);

           $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

           $stmt->execute();

           return $stmt->fetch();
       }

       public static function getAll3starFeedback($id_cicerone)
       {

         $sql = 'SELECT COUNT(rating) as three_star from user
               inner join partecipant on partecipant.user=user.id inner join feedback on partecipant.id=feedback.partecipant
               WHERE feedback.user = :id_cicerone AND rating = 3';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id_cicerone', $id_cicerone, PDO::PARAM_INT);

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

            $stmt->execute();

            return $stmt->fetch();
        }

        public static function getAll4starFeedback($id_cicerone)
        {

          $sql = 'SELECT COUNT(rating) as four_star from user
                inner join partecipant on partecipant.user=user.id inner join feedback on partecipant.id=feedback.partecipant
                WHERE feedback.user = :id_cicerone AND rating = 4';

             $db = static::getDB();
             $stmt = $db->prepare($sql);

             $stmt->bindValue(':id_cicerone', $id_cicerone, PDO::PARAM_INT);

             $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

             $stmt->execute();

             return $stmt->fetch();
         }

         public static function getAll5starFeedback($id_cicerone)
         {

           $sql = 'SELECT COUNT(rating) as five_star from user
                 inner join partecipant on partecipant.user=user.id inner join feedback on partecipant.id=feedback.partecipant
                 WHERE feedback.user = :id_cicerone AND rating = 5';

              $db = static::getDB();
              $stmt = $db->prepare($sql);

              $stmt->bindValue(':id_cicerone', $id_cicerone, PDO::PARAM_INT);

              $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

              $stmt->execute();

              return $stmt->fetch();;
          }

          public static function getAvarageFeeedback($id_cicerone)
          {

            $sql = 'SELECT ROUND(AVG(rating),1) as avarage_rating, COUNT(rating) as n_feedback from user
                  inner join partecipant on partecipant.user=user.id inner join feedback on partecipant.id=feedback.partecipant
                  WHERE feedback.user = :id_cicerone';

               $db = static::getDB();
               $stmt = $db->prepare($sql);

               $stmt->bindValue(':id_cicerone', $id_cicerone, PDO::PARAM_INT);

               $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

               $stmt->execute();

               return $stmt->fetch();;
           }

     public static function checkFeedback($id_globtrotter)
     {

       $sql = 'SELECT user.name as user_name, user.profile_picture as profile_picture, feedback.comment as comment, feedback.user as id_cicerone from user
             inner join partecipant on partecipant.user=user.id inner join feedback on partecipant.id=feedback.partecipant
             WHERE partecipant.user = :id_globtrotter';

          $db = static::getDB();
          $stmt = $db->prepare($sql);

          $stmt->bindValue(':id_globtrotter', $id_globtrotter, PDO::PARAM_INT);

          $stmt->setFetchMode(PDO::FETCH_ASSOC);

          $stmt->execute();

          return $stmt->fetchAll();
      }

}
