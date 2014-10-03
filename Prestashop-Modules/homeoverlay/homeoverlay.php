<?php

if (!defined('_PS_VERSION_'))
  exit;

class homeoverlay extends Module
{

private $_html = '';

  function __construct()
  {
    $this->name = 'homeoverlay';
    $this->tab = '';
    $this->version = '1.0';
    $this->author = 'Tyler Cooper';
 
    parent::__construct();
    $this->displayName = $this->l('New Visitor Overlay');
    $this->description = $this->l('Creates page overlay to display information to first time visitors.');


  } //end of construct()

  // Registration of module to 'header'
  public function install() {
    if (
      parent::install() == false
      || $this->registerHook('header') == false
    )
      return false;
    return true;

  } //end of install()

  public function uninstall()
  {
    parent::uninstall();
  } //end of uninstall()



public function hookdisplayHeader($params)
{

  // If there is not a cookie, display overlay and set cookie.
	if (!$_COOKIE['firstTime'])
	{
		$value = true;
	    setcookie("firstTime", $value, '/');		

		$this->context->controller->addJS(($this->_path).'overlay.js');
	    return $this->display(__FILE__,'homeoverlay.tpl');	
	}


}



}
?>