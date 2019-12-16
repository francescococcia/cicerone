<?php

namespace App\Controllers;

use \Core\View;
use \Core\Mail;
use \Core\Auth;
use \App\Models\Excursion;
use \App\Models\Partecipant;
use \App\Models\UserSignedIn;
use \App\Models\UserNotSignedIn;
use \App\Models\Feedback;
use \Core\Flash;

class Profile extends \Core\Controller
{
    public function dashboardAction()
    {
        $this->requireLogin();

        $one_star = Feedback::getAll1starFeedback(Auth::getUser()->id);

        $two_star = Feedback::getAll2starFeedback(Auth::getUser()->id);

        $three_star = Feedback::getAll3starFeedback(Auth::getUser()->id);

        $four_star = Feedback::getAll4starFeedback(Auth::getUser()->id);

        $five_star = Feedback::getAll5starFeedback(Auth::getUser()->id);

        View::renderTemplate('Profile/dashboard.html',[
          'one_star' => $one_star->one_star,
          'two_star' => $two_star->two_star,
          'three_star' => $three_star->three_star,
          'four_star' => $four_star->four_star,
          'five_star' => $five_star->five_star
        ]);

    }

    public function updatePersonalDataAction()
    {
        $this->requireLogin();

        $user = new UserSignedIn($_POST);

        if($user->updateUserDetails($user,Auth::getUser()->id))
        {
          Flash::addMessage('Dati aggiornati');

          $this->redirect('/dashboard');
        }
    }

    public function updateProfilePicture()
    {
        $this->requireLogin();


        if(Auth::getUser()->updateProfilePicture($_FILES['profile_picture']['tmp_name']))
        {

          $this->redirect('/dashboard');
        }
    }

    public function updatePasswordAction()
    {
        $this->requireLogin();

        $current_user = Auth::getUser();

        $current_user->password_confirmation = $_POST['password_confirmation'];

        if (password_verify($_POST['old_password'], $current_user->password_hash))
        {

            if($current_user->resetPassword($_POST['password']))
            {
              Flash::addMessage('Dati aggiornati');

              $this->redirect('/dashboard');

            }
            else {

              Flash::addMessage('Dati non aggiornati', Flash::getWarningMessage());

              $this->redirect('/dashboard');
            }
          }
        else
        {
          Flash::addMessage('Dati non aggiornati', Flash::getWarningMessage());

          $this->redirect('/dashboard');
        }
    }

    public static function getProfilePicture()
    {
        $current_user = Auth::getUser();

        if(isset($current_user->profile_picture))
        {
          return base64_encode($current_user->profile_picture);
        }
        return false;
    }

    public function publicUserAction()
    {

      $id = $this->route_params['public'];

      $user = UserNotSignedIn::getPublicUserProfile($id);

      $one_star = Feedback::getAll1starFeedback($id);

      $two_star = Feedback::getAll2starFeedback($id);

      $three_star = Feedback::getAll3starFeedback($id);

      $four_star = Feedback::getAll4starFeedback($id);

      $five_star = Feedback::getAll5starFeedback($id);

      $avarage_rating = Feedback::getAvarageFeeedback($id);

      $tel = false;

      if(isset(Auth::getUser()->id))
      {
        $globetrotter = Partecipant::getGlobetrotter(Auth::getUser()->id);
        $cicerone = Partecipant::getCicerone($id);

        $globetrotter2 = Partecipant::getGlobetrotter($id);
        $cicerone2 = Partecipant::getCicerone(Auth::getUser()->id);

        if(($globetrotter and $cicerone) or ($globetrotter2 and $cicerone2))
        {
          $tel = true;
        }
        elseif($id == Auth::getUser()->id)
        {
          $tel = true;
        }
      }
      $birth_date = $user->birthday;
      $age= date("Y") - date("Y", strtotime($birth_date));

      $user->profile_picture = base64_encode($user->profile_picture);


      View::renderTemplate('Profile/public_user.html',[
          'user' => $user,
          'age' => $age,
          'tel' => $tel,
          'id_cicerone' => $id,
          'one_star' => $one_star->one_star,
          'two_star' => $two_star->two_star,
          'three_star' => $three_star->three_star,
          'four_star' => $four_star->four_star,
          'five_star' => $five_star->five_star,
          'avarage_rating' => $avarage_rating->avarage_rating,
          'n_feedback' => $avarage_rating->n_feedback
      ]);
    }
}
