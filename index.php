<?php
   include("./include/session.php");   
   require('header.php');
   /*******************************************************/
   $action = $_GET['action'];
   /****************
   if (strtolower($action) == "about") {
      require ('about.php');
   }
   else if (strtolower($action) == "contact") {
      require ('contact.php');
   }
   else if (strtolower($action) == "support") {
      require ('support.php');
   }
   else if (strtolower($action) == "privacy") {
      require ('privacy.php');
   }
   else if (strtolower($action) == "register") {
      require ('register.php');
   }
   else if (strtolower($action) == "forgotpass") {
      require ('forgotpass.php');
   }
   else if (strtolower($action) == "userinfo" && $session->logged_in) {
      require ('userinfo.php');
   }
   else if (strtolower($action) == "useredit" && $session->logged_in) {
      require ('useredit.php');
   }
   ***************/
	
	if (strtolower($action) == "about") {
		require('about.php');		
	}
	else if (strtolower($action) == "contact") {
		require('support.php');		
	}		
	else if (strtolower($action) == "support") {
		require('support.php');		
	}	
	else if (strtolower($action) == "register") {
		require('signup.php');		
	}
	else if (strtolower($action) == "main") {
		require('main.php');		
	}
	else {
        if ( $session->logged_in )
            require('main.php');
        else 
            require('home.php');
	}
   /*******************************************************/
   require('footer.php');
?>