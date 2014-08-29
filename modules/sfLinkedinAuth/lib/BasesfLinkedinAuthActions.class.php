<?php

class BasesfLinkedinAuthActions extends sfActions
{
  public function executeSignin(sfWebRequest $request)
  {
    $linkedin = new sfLinkedin($this->getContext()->getRouting()->generate('linkedin_signin_response',array(),true));

    $url = $linkedin->getLoginUrl(array(
      'r_fullprofile',
      'r_emailaddress',
      'r_contactinfo',
      ));

    $this->redirect($url);

  }

  public function executeSigninResponse(sfWebRequest $request)
  {
    if ($request->getParameter('error') && $request->getParameter('error')=='access_denied'){
      $this->redirect('@linkedin_access_denied');
    }

    $linkedin = new sfLinkedin($this->getContext()->getRouting()->generate('linkedin_signin_response',array(),true));

    $token = $linkedin->getAccessToken($request->getParameter('code'));

    $user_data = $linkedin->get('/people/~:(id,first-name,last-name,email-address)')

    // Busca usuario ya logeado previo con linkedin
    $usuario = sfGuardUserTable::getInstance()->findOneByEmailAddress($user_data['emailAddress']);
    if ($usuario) {
      $usuario->set(sfConfig::get('app_linkedin_guard_uid_column','linkedin_uid'),$user_data['id']);
      $usuario->set(sfConfig::get('app_linkedin_guard_token_column','linkedin_token'),$token);
      $usuario->save();
    } else {
      $usuario = new sfGuardUser();
      $usuario->setFirstName($user_data['firstName']);
      $usuario->setLastName($user_data['lastName']);
      $usuario->setEmailAddress($user_data['emailAddress']);
      $usuario->set(sfConfig::get('app_linkedin_guard_uid_column','linkedin_uid'),$user_data['id']);
      $usuario->set(sfConfig::get('app_linkedin_guard_token_column','linkedin_token'),$token);
      $usuario->save();
    }

    $this->getUser()->signIn($usuario);
    $this->redirect(sfConfig::get('app_linkedin_after_signin_url','@homepage'));

  }

  public function executeAccessDenied(sfWebRequest $request)
  {

  }

}