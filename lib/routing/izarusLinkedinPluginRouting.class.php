<?php

class izarusLinkedinPluginRouting
{
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $event->getSubject()->prependRoute('linkedin_signin', new sfRoute('/linkedin/login', array('module' => 'sfLinkedinAuth', 'action' => 'signin')));
    $event->getSubject()->prependRoute('linkedin_signin_response', new sfRoute('/linkedin/response', array('module' => 'sfLinkedinAuth', 'action' => 'signinResponse')));
    $event->getSubject()->prependRoute('linkedin_access_denied', new sfRoute('/linkedin/access-denied', array('module' => 'sfLinkedinAuth', 'action' => 'accessDenied')));
  }
}

