<?php

namespace App\Models;

use PDO;
use \Core\Mail;
use \Core\View;
/**
 * User Excursion
 *
 * PHP version 7.0
 */
class Excursion extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save($user)
    {

      $date = date("Y-m-d", strtotime($this->meeting_date));

      $sql = 'INSERT INTO excursion (destination, meeting_address, type, language, meeting_date,
                                     meeting_time, price, seats, description, user)
              VALUES (:destination, :meeting_address, :type, :language, :meeting_date, :meeting_time,
              :price, :seats, :description, :user)';

      $db = static::getDB();
      $stmt = $db->prepare($sql);

      $stmt->bindValue(':destination', $this->destination, PDO::PARAM_STR);
      $stmt->bindValue(':meeting_address', $this->meeting_address, PDO::PARAM_STR);
      $stmt->bindValue(':type', $this->type, PDO::PARAM_STR);
      $stmt->bindValue(':language', $this->language, PDO::PARAM_STR);
      $stmt->bindValue(':meeting_date', $date, PDO::PARAM_STR);
      $stmt->bindValue(':meeting_time', $this->meeting_time, PDO::PARAM_STR);
      $stmt->bindValue(':price', $this->price, PDO::PARAM_STR);
      $stmt->bindValue(':seats', $this->seats, PDO::PARAM_INT);
      $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);

      $stmt->bindValue(':user', $user->id, PDO::PARAM_INT);

      return $stmt->execute();
    }

    public static function getAllExcursionsBy($destination, $type, $meeting_date)
     {

       $date = date("Y-m-d");

       if(empty($destination))
       {
         $destination = null;
       }
       if(empty($type))
       {
         $type = null;
       }
       if(empty($meeting_date))
       {
         $date = date("Y-m-d");
       }
       else {
         $date = date("Y-m-d", strtotime($meeting_date));
         $today = date("Y-m-d");
         if($today > $date)
         {
           $date = $today;
         }
       }

         $sql = 'SELECT user.name as user_name, user.surname as user_surname, excursion.destination, excursion.type, excursion.user as cicerone_id,
                       excursion.meeting_time, excursion.meeting_date, excursion.meeting_address, excursion.price, excursion.description,
                       excursion.id as id_excursion, user.profile_picture, user.id as id_globtrotter, excursion.language as language,
                       excursion.seats as seats
                 FROM excursion inner join user on excursion.user=user.id
                 WHERE (excursion.destination = :destination or :destination is null)
                 AND (type = :type or :type is null)
                 HAVING excursion.meeting_date >= :meeting_date order by excursion.meeting_date';


         $db = static::getDB();
         $stmt = $db->prepare($sql);
         $stmt->bindValue(':destination', $destination, PDO::PARAM_STR);
         $stmt->bindValue(':type', $type, PDO::PARAM_STR);
         $stmt->bindValue(':meeting_date', $date, PDO::PARAM_STR);

         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $stmt->execute();

         return $stmt->fetchAll();
     }

     public static function getSeatsAvaible($id_excursion)
     {
         $sql = 'SELECT seats FROM excursion WHERE id = :id_excursion';

         $db = static::getDB();
         $stmt = $db->prepare($sql);

         $stmt->bindValue(':id_excursion', $id_excursion, PDO::PARAM_STR);

         $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

         $stmt->execute();

         return $stmt->fetch();
     }

     public static function changeSeatsAvaible($seats_changed,$id_excursion)
     {
         $sql = 'UPDATE excursion
                 SET seats = :seats
                 WHERE id = :id_excursion';

         $db = static::getDB();
         $stmt = $db->prepare($sql);

         $db = static::getDB();
         $stmt = $db->prepare($sql);

         $stmt->bindValue(':seats', $seats_changed, PDO::PARAM_INT);
         $stmt->bindValue(':id_excursion', $id_excursion, PDO::PARAM_INT);

         return $stmt->execute();
     }

      public static function modifyExcursion($excursion)
      {
              $date = date("Y-m-d", strtotime($excursion->meeting_date));


              $sql = 'UPDATE excursion
                      SET meeting_date = :meeting_date,
                          meeting_time = :meeting_time,
                          meeting_address = :meeting_address,
                          price = :price,
                          seats = :seats,
                          description = :description
                      WHERE id = :id_excursion';

              $db = static::getDB();
              $stmt = $db->prepare($sql);

              $stmt->bindValue(':meeting_date', $date, PDO::PARAM_STR);
              $stmt->bindValue(':meeting_time', $excursion->meeting_time, PDO::PARAM_STR);
              $stmt->bindValue(':meeting_address', $excursion->meeting_address, PDO::PARAM_STR);
              $stmt->bindValue(':price', $excursion->price, PDO::PARAM_STR);
              $stmt->bindValue(':description', $excursion->description, PDO::PARAM_STR);
              $stmt->bindValue(':seats', $excursion->seats, PDO::PARAM_INT);
              $stmt->bindValue(':id_excursion', $excursion->id_excursion, PDO::PARAM_INT);

              return $stmt->execute();
      }

      public static function removeExcursion($id_excursion)
      {

            $sql = 'DELETE FROM excursion
                    WHERE id = :id_excursion';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id_excursion', $id_excursion, PDO::PARAM_INT);

            return $stmt->execute();
      }

      public static function getAllExcursionsGlobtrotter($id_excursion)
      {

          $sql = 'SELECT user.name as user_name, user.surname as user_surname, partecipant.user as id_globtrotter, excursion.user as id_cicerone,
                   partecipant.excursion as id_excursion, partecipant.state as state, partecipant.id as id_partecipant, user.profile_picture as profile_picture,
                   partecipant.user as id_globtrotter, partecipant.id as id_partecipant
                    FROM partecipant inner join excursion on partecipant.excursion=excursion.id inner join user on partecipant.user=user.id';



          $db = static::getDB();
          $stmt = $db->prepare($sql);

          $stmt->bindValue(':id_excursion', $id_excursion, PDO::PARAM_STR);

          $stmt->setFetchMode(PDO::FETCH_ASSOC);

          $stmt->execute();

         return $stmt->fetchAll();

       }

       public static function getAllExcursionsCicerone($id_cicerone)
       {
            $sql = 'SELECT excursion.destination, excursion.type, excursion.user as cicerone_id,
                          excursion.meeting_time, excursion.meeting_date, excursion.meeting_address, excursion.price, excursion.description,
                          excursion.id as id_excursion, excursion.language as language, excursion.seats as seats
                    FROM excursion inner join user on excursion.user=user.id
                    WHERE excursion.user = :id_user';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id_user', $id_cicerone, PDO::PARAM_INT);

            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $stmt->execute();

            return $stmt->fetchAll();
        }

       public static function sendPartecipationRequest($excursion)
       {
             $excusion_change_seats = static::getSeatsAvaible($excursion->id_excursion);
             $seats_avaible = $excusion_change_seats->seats;
             $seats_diff = $seats_avaible - $excursion->seats_booked;
             if($seats_diff >= 0)
             {
                 static::changeSeatsAvaible($seats_diff, $excursion->id_excursion);

                 $sql = 'INSERT INTO partecipant (excursion,user, seats_booked)
                         VALUES (:excursion, :user, :seats)';

                 $db = static::getDB();
                 $stmt = $db->prepare($sql);

                 $stmt->bindValue(':excursion', $excursion->id_excursion, PDO::PARAM_INT);
                 $stmt->bindValue(':seats', $excursion->seats_booked, PDO::PARAM_INT);
                 $stmt->bindValue(':user', $excursion->id_globtrotter, PDO::PARAM_INT);

                 return $stmt->execute();
             }
             return false;
       }


}
