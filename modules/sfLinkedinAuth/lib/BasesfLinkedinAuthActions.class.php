<?php

class BasesfLinkedinAuthActions extends sfActions
{
  public function executeSignin(sfWebRequest $request)
  {
    if ($request->getParameter('redirect')) {
      $this->getUser()->setFlash('redirect',$request->getParameter('redirect'));
    }

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

    $user_data = $linkedin->get('/people/~:(id,first-name,last-name,email-address)');


    if ($this->getUser()->getFlash('is_linkedin_connect')) {
      // Busca si la cuenta LinkedIn está asociada a otro usuario
      $otro_usuario = sfGuardUserTable::getInstance()->findOneByLinkedinUid($user_data['id']);
      if ($otro_usuario && $otro_usuario->getId() != $this->getUser()->getGuardUser()->getId()) {
        throw new Exception("Otro usuario tiene asignada esta cuenta LinkedIn");
      }

      $usuario = $this->getUser()->getGuardUser();
      $usuario->set(sfConfig::get('app_linkedin_guard_uid_column','linkedin_uid'),$user_data['id']);
      $usuario->set(sfConfig::get('app_linkedin_guard_token_column','linkedin_token'),$token);
      $usuario->save();

      $this->redirect(sfConfig::get('app_linkedin_after_connect_url','@homepage'));
    }


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

    if ($this->getUser()->hasFlash('redirect')) {
      return $this->redirect($this->getUser()->getFlash('redirect'));
    } else {
      $this->redirect(sfConfig::get('app_linkedin_after_signin_url','@homepage'));
    }

  }

  public function executeAccessDenied(sfWebRequest $request)
  {

  }

  public function executeConnect(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->isAuthenticated());

    $linkedin = new sfLinkedin($this->getContext()->getRouting()->generate('linkedin_signin_response',array(),true));

    $url = $linkedin->getLoginUrl(array(
      'r_fullprofile',
      'r_emailaddress',
      'r_contactinfo',
      ));

    $this->getUser()->setFlash('is_linkedin_connect',true);

    $this->redirect($url);

  }

}