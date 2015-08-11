<?php
include("database.php");
include("mailer.php");
include("form.php");
include("constants.php");

class Session
{
   var $username;     //Username given on sign-up
   var $userid;       //Random value generated on current login
   var $userlevel;    //The level to which the user pertains
   var $time;         //Time user was last active (page loaded)
   var $logged_in;    //True if user is logged in, false otherwise
   var $userinfo = array();  //The array holding all user info
   var $url;          //The page url current being viewed
   var $referrer;     //Last recorded site page viewedi
   var $submitted_xtify_auth;  //Did the user submit a request to Xtify to be location tracked?
   var $email;
   var $shortlinkname; //value that access the user's map from my.peekmaps.com/"something"
   /**
    * Note: referrer should really only be considered the actual
    * page referrer in process.php, any other time it may be
    * inaccurate.
    */

   /* Class constructor */
   function Session(){
      $this->time = time();
      $this->startSession();
   }

   /**
    * startSession - Performs all the actions necessary to 
    * initialize this session object. Tries to determine if the
    * the user has logged in already, and sets the variables 
    * accordingly. Also takes advantage of this page load to
    * update the active visitors tables.
    */
   function startSession(){
      global $database;  //The database connection
      session_start();   //Tell PHP to start the session

      /* Determine if user is logged in */
      $this->logged_in = $this->checkLogin();

      /**
       * Set guest value to users not logged in, and update
       * active guests table accordingly.
       */
      if(!$this->logged_in){
         $this->username = $_SESSION['username'] = GUEST_NAME;
         $this->userlevel = GUEST_LEVEL;
         //$database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
      }
      /* Update users last active timestamp 
      else{
         $database->addActiveUser($this->username, $this->time);
      }
      */
      /* Remove inactive visitors from database
      $database->removeInactiveUsers();
      $database->removeInactiveGuests();
       */
      /* Set referrer page */
      if(isset($_SESSION['url'])){
         $this->referrer = $_SESSION['url'];
      }else{
         $this->referrer = "/";
      }

      /* Set current url */
      $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
   }

   /**
    * checkLogin - Checks if the user has already previously
    * logged in, and a session with the user has already been
    * established. Also checks to see if user has been remembered.
    * If so, the database is queried to make sure of the user's 
    * authenticity. Returns true if the user has logged in.
    */
   function checkLogin(){
      global $database;  //The database connection
      /* Check if user has been remembered */
      if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])){
         $this->username = $_SESSION['username'] = $_COOKIE['cookname'];
         $this->securityid   = $_SESSION['securityid']   = $_COOKIE['cookid'];
      }

      /* Username and securityid have been set and not guest */
      if(isset($_SESSION['username']) && isset($_SESSION['securityid']) &&
         $_SESSION['username'] != GUEST_NAME){
         /* Confirm that username and securityid are valid */
         if($database->confirmUserID($_SESSION['username'], $_SESSION['securityid']) != 0){
            /* Variables are incorrect, user not logged in */
            unset($_SESSION['username']);
            unset($_SESSION['securityid']);
            return false;
         }

         /* User is logged in, set class variables */
         $this->userinfo  = $database->getUserInfo($_SESSION['username']);
         $this->username  = $this->userinfo['username'];
         $this->securityid    = $this->userinfo['securityid'];
         $this->userlevel = $this->userinfo['userlevel'];
         $this->email     = $this->userinfo['email'];
         //$this->submitted_xtify_auth = $this->userinfo['submitted_xtify_auth'];
         $this->shortlinkname = $this->userinfo['shortlinkname'];
         return true;
      }
      /* User not logged in */
      else{
         return false;
      }
   }

   /**
    * login - The user has submitted his username and password
    * through the login form, this function checks the authenticity
    * of that information in the database and creates the session.
    * Effectively logging in the user if all goes well.
    */
   function login($subuser, $subpass, $subremember){
      global $database, $form;  //The database and form object
      $database->mylog("top of login");
      /* Username error checking */
      $field = "user";  //Use field name for username
      if(!$subuser || strlen($subuser = trim($subuser)) == 0){
         $form->setError($field, "* Username not entered");
      }
      else{
         /* Check if username is not alphanumeric */
         if(!eregi("^([0-9a-z])*$", $subuser)){
            $form->setError($field, "* Username not alphanumeric");
         }
      }
      
      $database->mylog("passed username validation");
      
      /* Password error checking */
      $field = "pass";  //Use field name for password
      if(!$subpass){
         $form->setError($field, "* Password not entered");
      }

      /* Return if form errors exist */
      if($form->num_errors > 0){
         return false;
      }

      $database->mylog("num_error = 0");
      $database->mylog("Password:".$subpass);
      /* Checks that username is in database and password is correct */
      $subuser = stripslashes($subuser);
      $result = $database->confirmUserPass($subuser, md5($subpass));

      $database->mylog("After confirmUserPass:".$result);
      
      /* if $result == 0 then Success! */

      /* Check error codes */
      if($result == 1){
         $field = "user";
         $form->setError($field, "* Username not found");
      }
      else if($result == 2){
         $field = "pass";
         $form->setError($field, "* Invalid password");
      }
  
      /* Return if form errors exist */
      if($form->num_errors > 0){
         return false;
      }

      $database->mylog("(again) num_errors = 0");
      
      /* Username and password correct, register session variables */
      $this->userinfo  = $database->getUserInfo($subuser);
      $this->username  = $_SESSION['username'] = $this->userinfo['username'];
      $this->securityid    = $_SESSION['securityid']   = $this->generateRandID();
      $this->userlevel = $this->userinfo['userlevel'];
      
      $database->mylog("After the This stuff");
      
      /* Insert securityid into database and update active users table */
      $database->updateUserField($this->username, "securityid", $this->securityid);
      //$database->addActiveUser($this->username, $this->time);
      //$database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

      $database->mylog("After updateUserField");
      
      /**
       * This is the cool part: the user has requested that we remember that
       * he's logged in, so we set two cookies. One to hold his username,
       * and one to hold his random value securityid. It expires by the time
       * specified in constants.php. Now, next time he comes to our site, we will
       * log him in automatically, but only if he didn't log out before he left.
       */
      if($subremember){
         setcookie("cookname", $this->username, time()+COOKIE_EXPIRE, COOKIE_PATH);
         setcookie("cookid",   $this->securityid,   time()+COOKIE_EXPIRE, COOKIE_PATH);
      }
      $database->mylog("RETURN TRUE!! :-)");
      /* Login completed successfully */
      return true;
   }

   /**
    * logout - Gets called when the user wants to be logged out of the
    * website. It deletes any cookies that were stored on the users
    * computer as a result of him wanting to be remembered, and also
    * unsets session variables and demotes his user level to guest.
    */
   function logout(){
      global $database;  //The database connection
      /**
       * Delete cookies - the time must be in the past,
       * so just negate what you added when creating the
       * cookie.
       */
      if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])){
         setcookie("cookname", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
         setcookie("cookid",   "", time()-COOKIE_EXPIRE, COOKIE_PATH);
      }

      /* Unset PHP session variables */
      unset($_SESSION['username']);
      unset($_SESSION['userid']);

      /* Reflect fact that user has logged out */
      $this->logged_in = false;
      
      /**
       * Remove from active users table and add to
       * active guests tables.
       */
      //$database->removeActiveUser($this->username);
      //$database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
      
      /* Set user level to guest */
      $this->username  = GUEST_NAME;
      $this->userlevel = GUEST_LEVEL;
   }

   /**
    * register - Gets called when the user has just submitted the
    * registration form. Determines if there were any errors with
    * the entry fields, if so, it records the errors and returns
    * 1. If no errors were found, it registers the new user and
    * returns 0. Returns 2 if registration failed.
    */
   function register($subuser, $subpass, $subemail, $tracklevel){
      global $database, $form, $mailer;  //The database, form and mailer object
      
      /* Username error checking */
      $field = "username";  //Use field name for username
      if(!$subuser || strlen($subuser = trim($subuser)) == 0){
         $form->setError($field, "* Username not entered");
      }
      else{
         /* Spruce up username, check length */
         $subuser = stripslashes($subuser);
         if(strlen($subuser) < 5){
            $form->setError($field, "* Username below 5 characters");
         }
         else if(strlen($subuser) > 30){
            $form->setError($field, "* Username above 30 characters");
         }
         /* Check if username is not alphanumeric */
         else if(!eregi("^([0-9a-z])+$", $subuser)){
            $form->setError($field, "* Username not alphanumeric");
         }
         /* Check if username is reserved */
         else if(strcasecmp($subuser, GUEST_NAME) == 0){
            $form->setError($field, "* Username reserved word");
         }
         /* Check if username is already in use */
         else if($database->usernameTaken($subuser)){
            $form->setError($field, "* Username already in use");
         }         
      }

      /* Password error checking */
      $field = "password";  //Use field name for password
      if(!$subpass){
         $form->setError($field, "* Password not entered");
      }
      else{
         /* Spruce up password and check length*/
         $subpass = stripslashes($subpass);
         if(strlen($subpass) < 4){
            $form->setError($field, "* Password too short");
         }
         /* Check if password is not alphanumeric */
         else if(!eregi("^([0-9a-z])+$", ($subpass = trim($subpass)))){
            $form->setError($field, "* Password not alphanumeric");
         }
         /**
          * Note: I trimmed the password only after I checked the length
          * because if you fill the password field up with spaces
          * it looks like a lot more characters than 4, so it looks
          * kind of stupid to report "password too short".
          */
      }
      
      /* Email error checking */
      $field = "email";  //Use field name for email
      if(!$subemail || strlen($subemail = trim($subemail)) == 0){
         $form->setError($field, "* Email not entered");
      }
      else{
         /* Check if valid email address */
         $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                 ."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                 ."\.([a-z]{2,}){1}$";
         if(!eregi($regex,$subemail)){
            $form->setError($field, "* Email invalid");
         }
         $subemail = stripslashes($subemail);
      }

      /* Tracking level error checking */
      $field = "tracklevel";  //Use field name for trackinglevel
      if(!$tracklevel || strlen($tracklevel = trim($tracklevel)) == 0){
         $form->setError($field, "* Tracking Level not selected");
      }
      else{
         //Sneaky hackers! Trying to set tracking level to something wierd!
         if($tracklevel != '1' && $tracklevel != '2') {
            $form->setError($field, "* Tracking Level is invalid");
         }
      }     
      
      /* Errors exist, have user correct them */
      if($form->num_errors > 0){
         return 1;  //Errors with form
      }
      /* No errors, add the new account to the */
      else{
         if($database->addNewUser($subuser, md5($subpass), $subemail,  $tracklevel)){        
            
            //if ($database->addNewMap($subuser)) {         
               //if(EMAIL_WELCOME){
               //$mailer->sendWelcome($subuser,$subemail,$subpass);
               //}
              $retval = $this->login($subuser, $subpass, 0);
              
              if ($retval) $database->mylog("The Retval is True");
              else $database->mylog("The Retval is False!");
              
              return 0;  //New user added succesfully
            //}
            //else {
            //  return 2; //Registration attempt failed
            //            //...TODO:say failed to make map upon fail.
            //}  
         }else{
            return 2;  //Registration attempt failed for some other reason (i.e. DB problem)
         }
      }
   }
   
   function setUserLocation($username, $lat,$lon) {
     global $database, $form;  //The database and form object
     
      $retval = $database->setLocation($lat, $lon, $username);
      if ($retval != 1) {       
        return false;  //Errors with form 
      }
      else {
        return true; //success!!
      }
    /*}*/
  }
  function fetchJSONArray($json_name,$securityid) {
    global $database;
    return $database->JSONHandler($json_name, $securityid);    
    
  }
  
  function removeFSAuth($username) {
    global $database, $form;
    
    $database->removeFoursquareAuth($username);
    return true;
  }

/*
  function doShortLink($username, $shortlink_id, $enable_link, $delete_flag, $shortlink_name) { 
    global $database, $form;
    if ($database->shortLinkExists($shortlink_name) == 1) {
      if ($delete_flag == "true") {
        deleteShortLink($username, $shortlink_id);
      }
      else {
        $database->updShortLink($username, $shortlink_id, $enable_link, $link_name);
      }
    } 
    else {
	addNewShortLink($username, $shortlink_name);
      }
    return true;
    
  }
 */

  function deleteShortLink($username, $shortlink_id) {
    global $database, $form;
    return $database->delShortLink($username, $shortlink_id);
  }

  function addNewShortLink($username, $shortlink_name, $user_ip) {
    global $database, $form;
    $database->mylog("top of addNewShortLink");
    if (!$database->shortLinkExists($shortlink_name)) {
      $database->mylog("shortLinkExists == 0");
      $database->addShortLink($username, $shortlink_name, $user_ip);
      $database->mylog("AFTER addShortLink.");
      return true;
    }
    else {
      $database->mylog("shortLinkExists [".$shortlink_name."] Usr:".$username);
      return false;
    }
  }
 
  function doShortLink($username, $shortlink_id, $enable_link, $delete_flag, $shortlink_name, $user_ip) {
    global $database, $form;

    $field = "shortlinkname";

    $database->mylog("TOP:doShortLink $username, $shortlink_id, $enable_link, $delete_flag, $shortlink_name");
    if ($database->shortLinkIDExists($shortlink_id) == 1) {
      if ($delete_flag == "true") {
        $this->deleteShortLink($username, $shortlink_id);
      }
      else {
        $database->mylog("doShortLink - going to call updShortLink");

        if ($enable_link == "true") {
	  $enable_link = 1;
        }
        else {
	  $enable_link = 0;
        }

        $database->updShortLink($username, $shortlink_id, $enable_link, $shortlink_name);
        $database->mylog("doShortLink - after updShortLink");
      }
    }
    else {
        if ($database->shortLinkExists($shortlink_name) == 0) {
          $this->addNewShortLink($username, $shortlink_name, $user_ip);
        }
        else {
          $form->setError($field, "* PeekMap Name is not available.");
        }
      }
    return true;

  }
 
  /**
   * set flag on users table (users.submitted_xtify_auth)
   * indicating that the user submitted an auth request to
   * Xtify.  This function is called from index.php upon
   * callback from Xtify.
   **/
  
  function setUserSubmittedXtifyAuth($username,$level) {
    global $database;
    $database->userSubmittedXtifyAuth($username,$level);
    return true;
  } 
   /**
    * editAccount - Attempts to edit the user's account information
    * including the password, which it first makes sure is correct
    * if entered, if so and the new password is in the right
    * format, the change is made. All other fields are changed
    * automatically.
    */
   function editAccount($subcurpass, $subnewpass, $subemail){
      global $database, $form;  //The database and form object
      /* New password entered */
      if($subnewpass){
         /* Current Password error checking */
         $field = "curpass";  //Use field name for current password
         if(!$subcurpass){
            $form->setError($field, "* Current Password not entered");
         }
         else{
            /* Check if password too short or is not alphanumeric */
            $subcurpass = stripslashes($subcurpass);
            if(strlen($subcurpass) < 4 ||
               !eregi("^([0-9a-z])+$", ($subcurpass = trim($subcurpass)))){
               $form->setError($field, "* Current Password incorrect");
            }
            /* Password entered is incorrect */
            if($database->confirmUserPass($this->username,md5($subcurpass)) != 0){
               $form->setError($field, "* Current Password incorrect");
            }
         }
         
         /* New Password error checking */
         $field = "newpass";  //Use field name for new password
         /* Spruce up password and check length*/
         $subpass = stripslashes($subnewpass);
         if(strlen($subnewpass) < 4){
            $form->setError($field, "* New Password too short");
         }
         /* Check if password is not alphanumeric */
         else if(!eregi("^([0-9a-z])+$", ($subnewpass = trim($subnewpass)))){
            $form->setError($field, "* New Password not alphanumeric");
         }
      }
      /* Change password attempted */
      else if($subcurpass){
         /* New Password error reporting */
         $field = "newpass";  //Use field name for new password
         $form->setError($field, "* New Password not entered");
      }
      
      /* Email error checking */
      $field = "email";  //Use field name for email
      if($subemail && strlen($subemail = trim($subemail)) > 0){
         /* Check if valid email address */
         $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                 ."@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                 ."\.([a-z]{2,}){1}$";
         if(!eregi($regex,$subemail)){
            $form->setError($field, "* Email invalid");
         }
         $subemail = stripslashes($subemail);
      }
      
      /* Errors exist, have user correct them */
      if($form->num_errors > 0){
         return false;  //Errors with form
      }
      
      /* Update password since there were no errors */
      if($subcurpass && $subnewpass){
         $database->updateUserField($this->username,"password",md5($subnewpass));
      }
      
      /* Change Email */
      if($subemail){
         $database->updateUserField($this->username,"email",$subemail);
      }
      
      /* Success! */
      return true;
   }
   
   /**
    * isAdmin - Returns true if currently logged in user is
    * an administrator, false otherwise.
    */
   function isAdmin(){
      return ($this->userlevel == ADMIN_LEVEL ||
              $this->username  == ADMIN_NAME);
   }
   
   /**
    * generateRandID - Generates a string made up of randomized
    * letters (lower and upper case) and digits and returns
    * the md5 hash of it to be used as a userid.
    */
   function generateRandID(){
      return md5($this->generateRandStr(16));
   }
   
   /**
    * generateRandStr - Generates a string made up of randomized
    * letters (lower and upper case) and digits, the length
    * is a specified parameter.
    */
   function generateRandStr($length){
      $randstr = "";
      for($i=0; $i<$length; $i++){
         $randnum = mt_rand(0,61);
         if($randnum < 10){
            $randstr .= chr($randnum+48);
         }else if($randnum < 36){
            $randstr .= chr($randnum+55);
         }else{
            $randstr .= chr($randnum+61);
         }
      }
      return $randstr;
   }
   
   function fetchPeekMapLocation($keyword, $referrer, $user_agent, $ip_address){
       global $database;
       $location = array();
       $location[] = $database->findMapLocation($keyword, $referrer, $user_agent, $ip_address);
       return $location[0];
   }
   
   function updateUserLocation($email_address, $latitude, $longitude) {
       global $database;
       return $database->updUserLocation($email_address, $latitude, $longitude);
   }
   
};


/**
 * Initialize session object - This must be initialized before
 * the form object because the form uses session variables,
 * which cannot be accessed unless the session has started.
 */
$session = new Session;

/* Initialize form object */
$form = new Form;

?>
