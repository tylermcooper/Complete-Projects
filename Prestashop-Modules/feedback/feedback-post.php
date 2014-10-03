<?php 
require_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/feedback.php');
  
  // Prepare data
	$email = trim($_POST["email"]);
	$name = trim($_POST["name"]);
	$generic = trim($_POST["generic"]);	
	$question1 = trim($_POST["question1"]);
	$question2 = trim($_POST["question2"]);
	$question3 = trim($_POST["question3"]);
	$question4 = trim($_POST["question4"]);
	$question5 = trim($_POST["question5"]);

  // Insert data into feedback table
	Db::getInstance()->insert('feedback', array(

      'email'	   => PSQL($email),
      'name'	   => PSQL($name),
      'generic'		=> PSQL($generic),
      'question1'	   => PSQL($question1),
      'question2'	   => PSQL($question2),
      'question3'	   => PSQL($question3),
      'question4'	   => PSQL($question4),
      'question5'	   => PSQL($question5),
    ));

?>