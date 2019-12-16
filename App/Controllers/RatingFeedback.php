<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Feedback;
use \Core\Auth;
use \Core\Flash;
use \Core\Mail;

class RatingFeedback extends \Core\Controller
{

    public function ratingAction()
    {
      $this->requireLogin();

      $rating = new Feedback($_POST);

      View::renderTemplate('feedback/rating.html',[
        'rating' => $rating
      ]);
    }

    public function showRatingAction()
    {
        $id_cicerone = $this->route_params['public'];

        $feedbacks = Feedback::getAllFeedbackCicerone($id_cicerone);

        $one_star = Feedback::getAll1starFeedback($id_cicerone);

        $two_star = Feedback::getAll2starFeedback($id_cicerone);

        $three_star = Feedback::getAll3starFeedback($id_cicerone);

        $four_star = Feedback::getAll4starFeedback($id_cicerone);

        $five_star = Feedback::getAll5starFeedback($id_cicerone);

        $avarage_rating = Feedback::getAvarageFeeedback($id_cicerone);

        $i = 0;

        foreach($feedbacks as $feedback)
        {
          $feedbacks[$i]['profile_picture'] = base64_encode($feedback['profile_picture']);
          $i++;
        }

        View::renderTemplate('Feedback/viewfeedback.html',[
          'feedbacks' => $feedbacks,
          'one_star' => $one_star->one_star,
          'two_star' => $two_star->two_star,
          'three_star' => $three_star->three_star,
          'four_star' => $four_star->four_star,
          'five_star' => $five_star->five_star,
          'avarage_rating' => $avarage_rating->avarage_rating,
          'n_feedback' => $avarage_rating->n_feedback,
        ]);

    }

    public function ratingCiceroneAction()
    {
        $rating = new Feedback($_POST);

        if($rating->releaseFeedback())
        {
          Flash::addMessage('Grazie per aver lasciato il feedback');

          $this->redirect('/excursion/booked');
        }
    }

}
