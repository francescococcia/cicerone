<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\UserNotSignedIn;
use \Config\Config;
use \Core\Auth;
use \Core\Flash;
use \Core\Mail;


class Password extends \Core\Controller
{
    public function forgot_passwordAction()
    {
    	$this->isUserLoggedInAlready();

        View::renderTemplate('Password/forgot_password.html');
    }

    public function requestResetAction()
    {
    	UserNotSignedIn::sendPasswordReset($_POST['email']);

    	Flash::addMessage('Email inviata con successo');

    	$this->redirect('/login/forgot');
    }

    public function resetAction()
    {
    	$token = $this->route_params['token'];

    	$user = $this->getUserOrExit($token);

    	View::renderTemplate('Password/reset_password.html',[
    			'token' => $token
    	]);
    }

    public function resetPasswordAction()
    {
    	$token = $_POST['token'];

    	$user = $this->getUserOrExit($token);
      $user->password_confirmation = $_POST['password_confirmation'];
      $user->password = $_POST['password'];

    	if($user->password == $user->password_confirmation)
    	{
	    	if($user->resetPassword($user->password))
	    	{
	    		Flash::addMessage('La password è stata cambiata con successo');

	    		$this->redirect('/login');
	    	}
	    	else
	    	{
	    		View::renderTemplate('Password/forgot_password.html',[
	    			'token' => $token,
	    			'user' => $user
	    		]);
	    	}
    	}
    	else
    	{
    		Flash::addMessage('I campi della password non corrispondono', Flash::getWarningMessage());

    		$this->redirect('/password/reset/'.$token);
    	}
    }

    protected function getUserOrExit($token)
    {
    	$user = UserNotSignedIn::findByPasswordReset($token);

    	if($user)
    	{
    		return $user;
    	}
    	else
    	{
    		Flash::addMessage('La sessione è scaduta, richiedi l\'invio dell\'email', Flash::getWarningMessage());

    		View::renderTemplate('Password/forgot_password.html');

    		exit;

    	}
    }
}
