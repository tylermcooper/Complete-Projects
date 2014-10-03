<?php

if (!defined('_PS_VERSION_'))
  exit;

class extarget extends Module
{

private $_html = '';

  function __construct()
  {
    $this->name = 'extarget';
    $this->tab = '';
    $this->version = '1.0';
    $this->author = 'Tyler Cooper';
 
    parent::__construct();
    $this->displayName = $this->l('Exact Target');
    $this->description = $this->l('Outputs Order Information to files.');


  } //end of construct()

  public function install() {
    if (
      parent::install() == false
      || $this->registerHook('displayOrderConfirmation') == false
    )
      return false;
    return true;

  } //end of install()

  public function uninstall()
  {
    parent::uninstall();
  } //end of uninstall()


  public function displayOrderConfirmation()
  {
    return Hook::exec('displayOrderConfirmation', $params);
  }


}
?>