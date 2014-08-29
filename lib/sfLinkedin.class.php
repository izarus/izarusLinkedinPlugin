<?php

class sfLinkedin
{
  protected $linkedin = null;
  protected $api_key = null;
  protected $api_secret = null;
  protected $token = null;
  protected $token_expires = null;

  public function __construct($callback_url)
  {
    $this->api_key = sfConfig::get('app_linkedin_api_key');
    $this->api_secret = sfConfig::get('app_linkedin_api_secret');

    $this->linkedin = new LinkedIn(array(
      'api_key' => $this->api_key,
      'api_secret' => $this->api_secret,
      'callback_url' => $callback_url,
      ));
  }

  public function getLoginUrl(array $scope = array(), $state = null){
    return $this->linkedin->getLoginUrl($scope, $state);
  }

  public function getAccessToken($authorization_code = null)
  {
    return $this->linkedin->getAccessToken($authorization_code);
  }

  public function post($endpoint, array $payload = array())
  {
   return $this->linkedin->post($endpoint, $payload);
  }

  public function get($endpoint, array $payload = array())
  {
    return $this->linkedin->get($endpoint, $payload);
  }

  public function put($endpoint, array $payload = array())
  {
    return $this->fetch($endpoint, $payload);
  }

}