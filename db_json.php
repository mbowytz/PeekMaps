<?php

    include("./include/session.php");      
    include("./include/constants.php");

class FetchJSON
{
   /* Class constructor */
   function FetchJSON(){
      global $session;
      
      /* User submitted login form */
      if(!isset($_GET['callback']) || !isset($_GET['name']) || !isset($_GET['sid'])){
         $this->errormsg();
      }
      else {

           $json = $session->fetchJSONArray($_GET['name'], $_GET['sid']);
           print_r($_GET['callback'].$json); //callback is prepended for json-p
            
      }
    }
    
    function errormsg() {
        echo "Bad Parameters";       
    }
}
    
/* Initialize process */
$process = new FetchJSON;
?>