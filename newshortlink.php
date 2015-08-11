<?php
    include("include/session.php");    
    $shortlink_name =  $_GET['name'];
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    if ($session->logged_in) {
      $retval =  $session->addNewShortLink($session->username, $shortlink_name, $user_ip);
    }
    if ($retval) {
        echo "<li><h2>".$shortlink_name."</h2></li>\n".
             "<li><b>0 clicks</b></li>\n". 
             "<li><a href=\"\">View Stats</a></li>\n".
             "<li><input type=\"submit\" name=\"submit\" value=\"Delete Link\">\n";
    }
    else {
        echo "Error: Short link ".$shortlink_name." already exists.";
    }
?>