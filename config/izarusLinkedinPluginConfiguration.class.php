<?php

class izarusLinkedinPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('routing.load_configuration', array('izarusLinkedinPluginRouting', 'listenToRoutingLoadConfigurationEvent'));
  }
}