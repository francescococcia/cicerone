<?php

namespace App\Models;

use PDO;
use \Core\Mail;
use \Core\View;

class Partecipant extends \Core\Model
{


    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public static function getGlobtrottersByExcursion($id_excursion)
    {

         $sql = 'SELECT user.name as user_name, user.surname as user_surname, partecipant.user as id_globtrotter, excursion.user as id_cicerone,
                  partecipant.excursion as id_excursion, partecipant.state as state, partecipant.id as id_partecipant, user.profile_picture as profile_picture,
                    user.self_phone as self_phone, partecipant.seats_booked as seats_booked
                   FROM partecipant inner join excursion on partecipant.excursion=excursion.id inner join user on partecipant.user=user.id
                   WHERE excursion.id = :id_excursion';



         $db = static::getDB();
         $stmt = $db->prepare($sql);

         $stmt->bindValue(':id_excursion', $id_excursion, PDO::PARAM_STR);

         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getGlobtrotters()
    {

         $sql = 'SELECT partecipant.user as id_globtrotter, partecipant.excursion as id_excursion from partecipant inner join excursion on excursion.id=partecipant.excursion';

         $db = static::getDB();
         $stmt = $db->prepare($sql);

         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function deleteAcceptedRequest($id_globetrotter,$id_excursion, $id_partecipant)
    {
        $seats_update = static::getSeatsBooked($id_partecipant)->seats_booked + Excursion::getSeatsAvaible($id_excursion)->seats;
        Excursion::changeSeatsAvaible($seats_update,$id_excursion);
        $sql = 'DELETE FROM partecipant
                WHERE partecipant.excursion = :id_excursion';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id_excursion', $id_excursion, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function deleteSentRequestGlobtrotter($id_globetrotter,$id_excursion, $id_partecipant)
    {
        $seats_update = static::getSeatsBooked($id_partecipant)->seats_booked + Excursion::getSeatsAvaible($id_excursion)->seats;
        Excursion::changeSeatsAvaible($seats_update,$id_excursion);
        $sql = 'DELETE FROM partecipant
                WHERE partecipant.user = :id_globetrotter';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id_globetrotter', $id_globetrotter, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function acceptRequestGlobetrotter($id_partecipant)
    {
            $sql = 'UPDATE partecipant
                    SET state ="Accettato"
                    WHERE id = :id_partecipant';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_partecipant', $id_partecipant, PDO::PARAM_INT);

            return $stmt->execute();
    }

    public static function getAllExcursionsGlobtrotterActive($id_globtrotter)
    {
      $date = date("Y-m-d");

      $sql = 'SELECT partecipant.user as id_globtrotter, excursion.user as id_cicerone,
               partecipant.excursion as id_excursion, partecipant.state as state, partecipant.id as id_partecipant, user.profile_picture as profile_picture,
               partecipant.user as id_globtrotter, partecipant.excursion as id_excursion, excursion.meeting_date as meeting_date, excursion.destination as destination,
               excursion.meeting_time as meeting_time, excursion.type as type, excursion.language as language, user.self_phone as self_phone,
               partecipant.seats_booked as seats_booked
                FROM partecipant inner join excursion on partecipant.excursion=excursion.id inner join user on partecipant.user=user.id
                WHERE partecipant.user = :id_globtrotter
                HAVING excursion.meeting_date > :today_date';

         $db = static::getDB();
         $stmt = $db->prepare($sql);

        $stmt->bindValue(':id_globtrotter', $id_globtrotter, PDO::PARAM_INT);
        $stmt->bindValue(':today_date', $date, PDO::PARAM_STR);

         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $stmt->execute();

         return $stmt->fetchAll();
     }

     public static function getAllExcursionsGlobtrotterActiveParsing($id_cicerone)
     {
       $date = date("Y-m-d");

       $sql = 'SELECT profile_picture from user
              WHERE id = :id_cicerone';

          $db = static::getDB();
          $stmt = $db->prepare($sql);

         $stmt->bindValue(':id_cicerone', $id_cicerone, PDO::PARAM_INT);

          $stmt->setFetchMode(PDO::FETCH_ASSOC);

          $stmt->execute();

          return $stmt->fetchAll();
      }

     public static function getGlobetrotter($id_globtrotter)
     {
       $date = date("Y-m-d");

       $sql = 'SELECT partecipant.user as globetrotter
                 FROM partecipant inner join excursion on partecipant.excursion=excursion.id inner join user on partecipant.user=user.id
                 WHERE partecipant.user = :id_globtrotter AND partecipant.state = "Accettato"';

          $db = static::getDB();
          $stmt = $db->prepare($sql);

         $stmt->bindValue(':id_globtrotter', $id_globtrotter, PDO::PARAM_INT);

          $stmt->setFetchMode(PDO::FETCH_ASSOC);

          $stmt->execute();

          return $stmt->fetchAll();
      }

      public static function getCicerone($id_cicerone)
      {
        $date = date("Y-m-d");

        $sql = 'SELECT excursion.user as cicerone
                  FROM user inner join excursion on user.id=excursion.user inner join partecipant on excursion.id = partecipant.excursion
                  WHERE excursion.user = :id_cicerone AND partecipant.state = "Accettato"';

           $db = static::getDB();
           $stmt = $db->prepare($sql);

          $stmt->bindValue(':id_cicerone', $id_cicerone, PDO::PARAM_INT);

           $stmt->setFetchMode(PDO::FETCH_ASSOC);

           $stmt->execute();

           return $stmt->fetchAll();
       }

     public static function getAllExcursionsGlobtrotterPassed($id_globtrotter)
     {
       $date = date("Y-m-d");

       $sql = 'SELECT partecipant.user as id_globtrotter, excursion.user as id_cicerone,
                partecipant.excursion as id_excursion, partecipant.state as state, partecipant.id as id_partecipant, user.profile_picture as profile_picture,
                partecipant.user as id_globtrotter, partecipant.excursion as id_excursion, excursion.meeting_date as meeting_date, excursion.destination as destination,
                excursion.meeting_time as meeting_time, excursion.type as type, excursion.language as language
                 FROM partecipant inner join excursion on partecipant.excursion=excursion.id inner join user on partecipant.user=user.id
                 WHERE partecipant.user = :id_globtrotter
                 HAVING excursion.meeting_date < :today_date';

          $db = static::getDB();
          $stmt = $db->prepare($sql);

         $stmt->bindValue(':id_globtrotter', $id_globtrotter, PDO::PARAM_INT);
         $stmt->bindValue(':today_date', $date, PDO::PARAM_STR);

          $stmt->setFetchMode(PDO::FETCH_ASSOC);

          $stmt->execute();

          return $stmt->fetchAll();
      }

      public static function getSeatsBooked($id_partecipant)
      {
          $sql = 'SELECT seats_booked FROM partecipant WHERE id = :id_partecipant';

          $db = static::getDB();
          $stmt = $db->prepare($sql);

          $stmt->bindValue(':id_partecipant', $id_partecipant, PDO::PARAM_STR);

          $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

          $stmt->execute();

          return $stmt->fetch();
      }


}
