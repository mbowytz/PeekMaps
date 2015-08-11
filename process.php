<?php

    include("include/session.php");
    include("include/constants.php");
    
class Process
{
   /* Class constructor */
   function Process(){
      global $session;
      
      /* User submitted login form */
      if(isset($_POST['login'])){
         $this->procLogin();
      }
      /* Process a New User Signup */
      elseif(isset($_POST['signup'])){
         $this->procRegister();
      }
      elseif(isset($_POST['logout'])){
	 $this->procLogout();
      }
      else if($session->logged_in){
         $this->procLogout();
      }

    }

   /**
    * procLogout - Simply attempts to log the user out of the system
    * given that there is no logout form to process.
    */
   function procLogout(){
      global $session;
      $retval = $session->logout();
      header("Location: index.php");
   }

   /**
    * procLogin - Processes the user submitted login form, if errors
    * are found, the user is redirected to correct the information,
    * if not, the user is effectively logged in to the system.
    */
   function procLogin(){
      global $session, $form;
      /* Login attempt */
      $retval = $session->login($_POST['user'], $_POST['pass'], isset($_POST['remember']));

      /* Login successful */
      if(!$retval){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
      }
      header("Location: index.php");
   }

    
    function procRegister(){
      global $session, $form;
 
      /* Registration attempt */
      $retval = $session->register(strtolower($_POST['username']), $_POST['password'],strtolower( $_POST['email']),$_POST['tracklevel']);

      /* Registration Successful */
      if($retval == 0){
         $_SESSION['reguname'] = $_POST['username'];
         $_SESSION['regsuccess'] = true;
         $session->login(strtolower($_POST['username']), $_POST['password'], 0);
         header("Location: index.php?action=main");
         
      }
      /* Error found with form */
      else if($retval == 1){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: index.php?action=register&retval=1");
      }
      /* Registration attempt failed for an "other" reason*/
      else if($retval == 2){
         $_SESSION['reguname'] = $_POST['username'];
         $_SESSION['regsuccess'] = false;
         header("Location: index.php?action=register&retval=2");
      }
      //header("Location: index.php?action=register");
      
   }
   
  
};


/* Initialize process */
$process = new Process;

?>
