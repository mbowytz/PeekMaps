<?php
/**
 * Database.php
 * 
 * The Database class is meant to simplify the task of accessing
 * information from the website's database.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 17, 2004
 */
include("constants.php");
      
class MySQLDB
{
   var $connection;         //The MySQL database connection
   var $num_members;        //Number of signed-up users
   /* Note: call getNumMembers() to access $num_members! */

   /* Class constructor */
   function MySQLDB(){
      /* Make connection to database */
      $this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
      mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
      
      /**
       * Only query database to find out number of members
       * when getNumMembers() is called for the first time,
       * until then, default value set.
       */
      $this->num_members = -1;
      
      if(TRACK_VISITORS){
         /* Calculate number of users at site */
         //$this->calcNumActiveUsers();
      
         /* Calculate number of guests at site */
         //$this->calcNumActiveGuests();
      }
   }

    function mylog($message){
        $q = "INSERT INTO tbllog VALUES (null, null, '".$message."')";
        return  mysql_query($q, $this->connection);    
   }
   
   /**
    * confirmUserPass - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given password is the same password in the database
    * for that user. If the user doesn't exist or if the
    * passwords don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserPass($username, $password){
   
        $this->mylog("***top of confirmUserPass Pwd:".$password);
   
      /* Add slashes if necessary (for query) */
      if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      }
        $this->mylog("***before SQL User:".$username);
      /* Verify that user is in database */
      $q = "SELECT password FROM ".TBL_USERS." WHERE username = '".$username."'";
      $result = mysql_query($q, $this->connection);
      if(!$result || (mysql_numrows($result) < 1)){
         $this->mylog("***return 1");
         return 1; //Indicates username failure
      }

      /* Retrieve password from result, strip slashes */
      $dbarray = mysql_fetch_array($result);
     // $dbarray['password'] = stripslashes($dbarray['password']);
      $password = stripslashes($password);
      
              $this->mylog("***".$password." and ".$dbarray['password']);
      
/* 
      $q = "INSERT INTO dummy VALUES (null, '".$password." and ".$dbarray['password']."')";
      $foo = mysql_query($q, $this->connection);
 */
      /* Validate that password is correct */
      if($password == $dbarray['password']){
          $this->mylog("***SUCCESS!");
         return 0; //Success! Username and password confirmed
      }
      else{
          $this->mylog("***OOPS!!");
         return 2; //Indicates password failure
      }
   }
   
   /**
    * confirmUserID - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given userid is the same userid in the database
    * for that user. If the user doesn't exist or if the
    * userids don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserID($username, $securityid){
      /* Add slashes if necessary (for query) */
      if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT securityid FROM ".TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      if(!$result || (mysql_numrows($result) < 1)){
         return 1; //Indicates username failure
      }

      /* Retrieve securityid from result, strip slashes */
      $dbarray = mysql_fetch_array($result);
      $dbarray['securityid'] = stripslashes($dbarray['securityid']);
      $securityid = stripslashes($securityid);

      /* Validate that securityid is correct */
      if($securityid == $dbarray['securityid']){
         return 0; //Success! Username and securityid confirmed
      }
      else{
         return 2; //Indicates securityid invalid
      }
   }
   
   /**
    * usernameTaken - Returns true if the username has
    * been taken by another user, false otherwise.
    */
   function usernameTaken($username){
      if(!get_magic_quotes_gpc()){
         $username = addslashes($username);
      }
      $q = "SELECT username FROM ".TBL_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      return (mysql_numrows($result) > 0);
   }
   
   /**
    * usernameBanned - Returns true if the username has
    * been banned by the administrator.
    */
   function usernameBanned($username){
      if(!get_magic_quotes_gpc()){
         $username = addslashes($username);
      }
      $q = "SELECT username FROM ".TBL_BANNED_USERS." WHERE username = '$username'";
      $result = mysql_query($q, $this->connection);
      return (mysql_numrows($result) > 0);
   }
  
   function userSubmittedXtifyAuth($username,$level) {
    $q = "UPDATE ".TBL_USERS." SET submitted_xtify_auth = ".$level." WHERE submitted_xtify_auth IS NOT NULL and username =  '$username'";
    return mysql_query($q, $this->connection); 
   }
 
   /**
    * addNewUser - Inserts the given (username, password, email)
    * info into the database. Appropriate user level is set.
    * Returns true on success, false otherwise.
    */
   function addNewUser($username, $password, $email, $tracking_level){
      $q = "INSERT INTO ".TBL_USERS." VALUES (null, 0, '".$username."', '".$password."', ' ', ' ', '".$email."', ".$tracking_level.", null)";
      if (mysql_query($q, $this->connection)) {
      
        $q = "INSERT INTO tbluserlocations VALUES ((SELECT userid FROM ".TBL_USERS." WHERE username = '".$username."' LIMIT 1),40.7142,-74.0064,NOW())";

        if (mysql_query($q, $this->connection)) 
            return true;
        else 
            return false;

      }
      else 
        return false;
      
   }
   
   function updUserLocation($user_email_address, $latitude, $longitude) {
        $q = "UPDATE tbluserlocations SET latitude = ".$latitude.", longitude = ".$longitude.", last_updated = NOW() ".
             "WHERE userid = (SELECT userid FROM ".TBL_USERS." WHERE email_address = '".$user_email_address."')";
        return mysql_query($q, $this->connection);
   }
   
   function JSONHandler($json_name, $securityid) {
  
    if ($json_name == "shortlinks") {
        //shortlink SQL
        $query = "SELECT s.shortlink_id, s.keyword as shortlink_name, s.clicks FROM ".TBL_SHORTLINKS." s, ".TBL_USERS." u ".
                 "WHERE u.userid = s.userid AND u.securityid = '".$securityid."'";
    }
    else if ($json_name == "friends") {
        //friend SQL
        /*
        $query = "SELECT u2.username AS title, u2.email_address AS description, loc.latitude, ".
             "loc.longitude, 1 AS rank FROM ".TBL_USERS." u, ".TBL_USERS." u2, ".TBL_FRIENDS." f, ".
             TBL_USERLOCATIONS." loc WHERE u.securityid = '".$securityid."' AND u.userid = f.userid AND f.friend_userid = u2.userid ".
             "AND loc.userid = f.friend_userid ".
             "UNION ".*/
         $query =     "SELECT 'test1' AS title, 'test@example.com' AS description, 40.6192 as latitude, -73.9898 as longitude, 1 as rank FROM DUAL ".
             "UNION ".
             "SELECT 'test2', 'test2@example.com', 41.7866, -87.7927, 1 FROM DUAL ".
             "UNION ".
             "SELECT 'test3', 'test3@example.com', 40.7613, -73.7295, 1 FROM DUAL ".
             "UNION ".
             "SELECT 'test4', 'test4@example.com', 34.0627, -117.741, 1 FROM DUAL";
             
    }
    
    /*$query = 'SELECT u.username AS title, u.email AS description, x.curr_latitude latitude, '.
             'x.curr_longitude longitude, u.id AS rank FROM users u, xtify_tbl x '.
             'WHERE u.id = x.user_id LIMIT 15';
      */       
    $res = mysql_query($query, $this->connection);
    
    if ($res) {
        // iterate over every row
        while ($row = mysql_fetch_assoc($res)) {
            // for every field in the result..
            for ($i=0; $i < mysql_num_fields($res); $i++) {
                $info = mysql_fetch_field($res, $i);
                
                if ($json_name == "shortlinks") {
                    $row[$info->shortlink_id] = $info->shortlink_id;
                    $row[$info->shortlink_name] = $info->shortlink_name;
                    $row[$info->clicks] = $info->clicks;                
                }
                else if ($json_name == "friends") {            
                    $row[$info->title] = $info->title;
                    $row[$info->description] = $info->description;
                    $row[$info->latitude] = $info->latitude;
                    $row[$info->longitude] = $info->longitude;
                    $row[$info->rank] = $info->rank;
                }
            }
            $rows[] = $row;
        }
    }
    
    return "(".json_encode($rows).")";
   }
   
   
   /**
    * addNewMap - Inserts a record into map_tbl that ties a map's ID
    * to the person it belongs to.  In the future, this table will hold
    * map preferences like map source and marker types.
    */
   function addNewMap($username){
     /*let's make something really random*/
     $mapkey = md5(uniqid(rand(), true));
       
     $q = "INSERT INTO map_tbl VALUES ((SELECT userid FROM ".TBL_USERS." WHERE username = '".$username."' LIMIT 1), '".$mapkey."')";           
       
      return mysql_query($q, $this->connection);
    }
   /**
    * updateUserField - Updates a field, specified by the field
    * parameter, in the user's row of the database.
    */
   function updateUserField($username, $field, $value){
      $q = "UPDATE ".TBL_USERS." SET ".$field." = '$value' WHERE username = '$username'";
      return mysql_query($q, $this->connection);
   }
   
   /**
    * getMapLocation - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
   function getMapLocation($map_uid){
      $q = "SELECT x.curr_latitude, x.curr_longitude, x.last_updated FROM xtify_tbl x, map_tbl m WHERE x.user_id = m.user_id AND m.map_unique_key = '".$map_uid."'";
      $result = mysql_query($q, $this->connection);
      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)){
         return NULL;
      }
      /* Return result array */
      $dbarray = mysql_fetch_array($result);
      $lat = $dbarray['curr_latitude'];
      $lng = $dbarray['curr_latitude'];
      $timestamp = $dbarray['last_updated'];
      
      return array ($lat, $lng, $timestamp);  
   }
   
   function xtifyEnabled($username){
      $q = "SELECT * FROM xtify_tbl WHERE user_id = (select id from users where username = '".$username."')";
       $result = mysql_query($q, $this->connection);
      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)){
        return false;
      }
      else {
        return true;
      }
   }

   function setLocation($latitude, $longitude, $username) {
     $q = "UPDATE tbluserlocations SET latitude = ".$latitude.", longitude = ".$longitude.", last_updated = NOW() ".
          "WHERE userid = (SELECT userid FROM tblusers WHERE username = '".$username."')";

     return mysql_query($q, $this->connection);   
   }
  
   /**
    * getUserInfo - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
   function getUserInfo($username){
      $q = "SELECT * FROM ".TBL_USERS." WHERE username = '".$username."'";
      $result = mysql_query($q, $this->connection);
      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)){
         return NULL;
      }
      /* Return result array */
      $dbarray = mysql_fetch_array($result);
      return $dbarray;
   }
   
   /**
    * getNumMembers - Returns the number of signed-up users
    * of the website, banned members not included. The first
    * time the function is called on page load, the database
    * is queried, on subsequent calls, the stored result
    * is returned. This is to improve efficiency, effectively
    * not querying the database when no call is made.
    */
   function getNumMembers(){
      if($this->num_members < 0){
         $q = "SELECT * FROM ".TBL_USERS;
         $result = mysql_query($q, $this->connection);
         $this->num_members = mysql_numrows($result);
      }
      return $this->num_members;
   }
   
   /**
    * calcNumActiveUsers - Finds out how many active users
    * are viewing site and sets class variable accordingly.
    
   function calcNumActiveUsers(){
      /* Calculate number of users at site 
      $q = "SELECT * FROM ".TBL_ACTIVE_USERS;
      $result = mysql_query($q, $this->connection);
      $this->num_active_users = mysql_numrows($result);
   }
   */
   /**
    * calcNumActiveGuests - Finds out how many active guests
    * are viewing site and sets class variable accordingly.
    
   function calcNumActiveGuests(){
      /* Calculate number of guests at site 
      $q = "SELECT * FROM ".TBL_ACTIVE_GUESTS;
      $result = mysql_query($q, $this->connection);
      $this->num_active_guests = mysql_numrows($result);
   }
   */
   /**
    * addActiveUser - Updates username's last active timestamp
    * in the database, and also adds him to the table of
    * active users, or updates timestamp if already there.
    
   function addActiveUser($username, $time){
      $q = "UPDATE ".TBL_USERS." SET created_date = '$time' WHERE username = '$username'";
      mysql_query($q, $this->connection);
      
      if(!TRACK_VISITORS) return;
      $q = "REPLACE INTO ".TBL_ACTIVE_USERS." VALUES ('$username', '$time')";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }
   */
   /* addActiveGuest - Adds guest to active guests table 
   function addActiveGuest($ip, $time){
      if(!TRACK_VISITORS) return;
      $q = "REPLACE INTO ".TBL_ACTIVE_GUESTS." VALUES ('$ip', '$time')";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }
   */
   /* These functions are self explanatory, no need for comments */
   
   /* removeActiveUser 
   function removeActiveUser($username){
      if(!TRACK_VISITORS) return;
      $q = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE username = '$username'";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }
   */
   /* removeActiveGuest 
   function removeActiveGuest($ip){
      if(!TRACK_VISITORS) return;
      $q = "DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE ip = '$ip'";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }
   */
   /* removeInactiveUsers 
   function removeInactiveUsers(){
      if(!TRACK_VISITORS) return;
      $timeout = time()-USER_TIMEOUT*60;
      $q = "DELETE FROM ".TBL_ACTIVE_USERS." WHERE timestamp < $timeout";
      mysql_query($q, $this->connection);
      $this->calcNumActiveUsers();
   }
*/
   /* removeInactiveGuests    function removeInactiveGuests(){
      if(!TRACK_VISITORS) return;
      $timeout = time()-GUEST_TIMEOUT*60;
      $q = "DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE timestamp < $timeout";
      mysql_query($q, $this->connection);
      $this->calcNumActiveGuests();
   }
   */

   function removeFoursquareAuth($username){
     $q = "DELETE FROM foursquare_oauth_tbl WHERE user_id = (SELECT id FROM users WHERE username = '".$username."')";
     return mysql_query($q, $this->connection);  
   }

   function shortLinkExists($shortlink_name){
     $q = "SELECT 1 FROM ".TBL_SHORTLINKS." WHERE keyword='".$shortlink_name."'";
     $result = mysql_query($q, $this->connection);
     return mysql_numrows($result);
   }

   function shortLinkIDExists($shortlink_id){
     $q = "SELECT 1 FROM shortlink_tbl WHERE shortlink_id=$shortlink_id";
     $result = mysql_query($q, $this->connection);
     if ($result == null) {
       mylog("shortLinkID does not exist.");
       return 0;
     }
     else {
       mylog("shortLinkID does exist.");
       return 1;
     }
     //return mysql_numrows( mysql_query($q, $this->connection));
   }
 
   function updShortLink($username, $shortlink_id, $enable_link, $shortlink_name) {
     $this->mylog("updShortLink:$username, $shortlink_id, $enable_link, $shortlink_name"); 
     if (!is_numeric($enable_link)) $enable = 0;
     else { 
        if ($enable_link > 0) $enable = 1;
        else $enable = 0;
     }

     //if ($link_name != null) { 
        $q = "UPDATE shortlink_tbl SET enabled_flag=$enable WHERE shortlink_id = $shortlink_id AND user_id = (SELECT userid FROM users WHERE username='$username' LIMIT 1)";
        return mysql_query($q, $this->connection);
     //}
     //else {
     //	return false;
     //}
   }

   function delShortLink($username, $shortlink_name) {
     $q = "DELETE FROM ".TBL_SHORTLINKS." WHERE keyword = '".$shortlink_name."'";
     $ret = mysql_query($q, $this->connection);

     $q = "DELETE FROM ".TBL_SHORTLINKS_LOG." WHERE keyword = '".$shortlink_name."'";
     $ret2 = mysql_query($q, $this->connection);
 
     return true;
   }
 
   function addShortLink($username, $shortlink_name, $user_ip) {
     $q = "INSERT INTO ".TBL_SHORTLINKS." VALUES (null, (SELECT userid FROM ".TBL_USERS." WHERE username = '".$username."'), '".$shortlink_name."',NOW(),'".$user_ip."',0)";
     return mysql_query($q, $this->connection); 
   }

   function userHasLocation($username){
    // $q = "SELECT 1 FROM xtify_tbl WHERE user_id = (SELECT id FROM users WHERE username = '".$username."') AND curr_latitude IS NOT NULL AND curr_longitude IS NOT NULL";
    // $result = mysql_query($q, $this->connection);
    // return mysql_numrows($result);
    return 1;
   }
   
   /* Function userHasFoursquare()*/
   
   function userHasFoursquare1($username){   
      $q = "SELECT 1 FROM users u, foursquare_oauth_tbl fot WHERE fot.user_id = u.id AND username = '".$username."'";  
      $result = mysql_query($q, $this->connection);
      return mysql_numrows($result);
   } 
   
   /**
    * query - Performs the given query on the database and
    * returns the result, which may be false, true or a
    * resource identifier.
    */
   function query($query){
      return mysql_query($query, $this->connection);
   }
  
   function addShortLinkCount($keyword) {
	$q = "UPDATE tblshortlinks SET clicks = clicks + 1 WHERE keyword = '".$keyword."'";
        return mysql_query($q, $this->connection);
   }
 
   function logShortLinkView($keyword, $referrer, $user_agent, $ip_address) {
	$q = "INSERT INTO tblshortlinkslog (keyword, referrer, user_agent, ip_address) VALUES ('".$keyword."', '".$referrer."','".$user_agent."','".$ip_address."')";
	return mysql_query($q, $this->connection);
   }

   function findMapLocation($keyword, $referrer, $user_agent, $ip_address){
        $q = "SELECT loc.latitude, loc.longitude, loc.last_updated ".
             "FROM tbluserlocations loc, ".TBL_USERS." u, tblshortlinks s ".
             "WHERE loc.userid = u.userid AND s.userid = u.userid ".
             "AND s.keyword = '".$keyword."'";
        
         $result = mysql_query($q, $this->connection);
      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)){
         return NULL;
      }
      /* Return result array */
      $dbarray = mysql_fetch_array($result);
      
      $lat = $dbarray['latitude'];
      $lng = $dbarray['longitude'];
      $timestamp = $dbarray['last_updated'];

      $ret = $this->addShortLinkCount($keyword);
      $ret = $this->logShortLinkView($keyword, $referrer, $user_agent, $ip_address); 
 
      return array ('latitude'=>$lat, 'longitude'=>$lng, 'timestamp'=>$timestamp);  
   }
};

/* Create database connection */
$database = new MySQLDB;

?>
