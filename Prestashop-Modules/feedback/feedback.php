<?php

if (!defined('_PS_VERSION_'))
  exit;

class feedback extends Module
{

private $_html = '';

  function __construct()
  {
    $this->name = 'feedback';
    $this->tab = '';
    $this->version = '1.0';
    $this->author = 'Tyler Cooper';
 
    parent::__construct();
    $this->displayName = $this->l('Feedback');
    $this->description = $this->l('Feedback form.');


  } //end of construct()

  public function install() {



    if (
      parent::install() == false
      || $this->registerHook('header') == false
      || !$this->createTable()
      || !$this->_createTab()
      )
      return false;
    return true;



  } //end of install()

private function _createTab()
{
    /* define data array for the tab  */
    $data = array(
                  'id_tab' => '', 
                  'id_parent' => 7, 
                  'class_name' => 'AdminFeedback', 
                  'module' => 'feedback', 
                  'position' => 1, 'active' => 1 
                 );

    /* Insert the data to the tab table*/
    $res = Db::getInstance()->insert('tab', $data);

    //Get last insert id from db which will be the new tab id
    $id_tab = Db::getInstance()->Insert_ID();

   //Define tab multi language data
    $data_lang = array(
                     'id_tab' => $id_tab, 
                     'id_lang' => Configuration::get('PS_LANG_DEFAULT'),
                     'name' => 'News'
                     );

    // Now insert the tab lang data
    $res &= Db::getInstance()->insert('tab_lang', $data_lang);

    return true;

} /* End of createTab*/

public function createTable() {

  $sql= "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."feedback`(
  `id_feedback` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `name` VARCHAR(256) NOT NULL,
  `generic` VARCHAR(600) NOT NULL,  
  `question1` VARCHAR(600) NOT NULL,
  `question2` VARCHAR(600) NOT NULL, 
  `question3` VARCHAR(600) NOT NULL, 
  `question4` VARCHAR(600) NOT NULL, 
  `question5` VARCHAR(600) NOT NULL,  
  `email` VARCHAR(600) NOT NULL)";

  $result =  Db::getInstance()->Execute($sql);
  
  return $result;
}


  public function uninstall()
  {
    parent::uninstall();
    $sql = "drop table "._DB_PREFIX_."feedback";
    Db::getInstance()->Execute($sql);
  } //end of uninstall()



public function hookdisplayHeader($params)
{

  $form = '
      <br/><br/><br/>
      <form>
          <div class="feedback-info">
          <label for="email">Email :</label>
          <input class="inputs" type="email" name ="email" id="email" placeholder="optional"/><br/>
          <label for="name">Name :</label>
          <input class="inputs" type="text" name="name" id="name" placeholder="optional"/><br/>
          <label for="generic">Comment :</label><br/>
          <textarea class="inputs" name="generic" id="generic"></textarea><br/>
          </div>

          <div class="feedback-text">
            <p>
              Nothing is more important to us here at CGFlags.com than our customers.  That\'s why when we want to improve <i>YOUR</i> experience, we ask for <i>YOUR</i> feedback.  
            </p><br/>


            <p>
              On behalf of everyone at CGFlags.com, thank you for your continued business and support.  Please use this simple form to submit any comments about your experience using CGFlags.com.  In addition to your comments, click "Survey" below to further assist us with a quick 5 question survey.
            </p><br/>

          </div>

          <div class="clearfix"></div>
          
          <div id="survey-section">

          <hr/>
          <label for="question1">Could you quickly and easily find what you were looking for? </label>
          <textarea class="inputs" name="question1" id="question1"></textarea><hr/><br/>
          
          <label for="question2">Did our website do what you expected it to do? </label>
          <textarea class="inputs" name="question2" id="question2"></textarea><hr/><br/>
          
          <label for="question3">Did you feel that you needed to click too many times to complete typical tasks?</label>
          <textarea class="inputs" name="question3" id="question3"></textarea><hr/><br/>
          
          <label for="question4">Did our website respond quickly?</label>
          <textarea class="inputs" name="question4" id="question4"></textarea><hr/><br/>
          
          <label for="question5">If any, which sections of the site did you find frustrating?</label>
          <textarea class="inputs" name="question5" id="question5"></textarea><hr/><br/>


          </div>

          <div class="input-wrapper">
          <div id="error_message"></div>
          <div id="success_message"></div>
          <div id="div1"></div>
          <input id="submit" type="button" value ="Submit" onclick="PostData()" />
          <input id="survey" type="button" value ="Full Survey" />
          <div class="clearfix"></div>
          </div>

      </form>

  ';


  $this->context->smarty->assign('form', $form);
  return $this->display(__FILE__,'feedback.tpl');	


}



}
?>