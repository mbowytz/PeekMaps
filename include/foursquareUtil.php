<?php
    require_once('foursquareOAuth.php');
    include_once("constants.php");
    
    function get_foursquare_friend_xml($foursquare_id, $oauth_access_token, $oauth_access_token_secret ) {

    $to = new FoursquareOAuth(FOURSQUARE_CONSUMER_KEY, FOURSQUARE_CONSUMER_SECRET, $oauth_access_token, $oauth_access_token_secret );
    
    $content = $to->OAuthRequest('http://api.foursquare.com/v1/checkins', array(), 'GET');      

  if (is_null($content)) {
    echo "get_foursquare_friend_xml >>> No friends for foursquare id ".$foursquare_id."\n";
  }
  else {
  
  $result = array();
  $ret = array();
  $ii=0;
  
  if($xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA)) {
    while (!is_null($xml->checkin[$ii]->display)){
          $result['latitude']   = $xml->checkin[$ii]->venue->geolat;
          $result['longitude']  = $xml->checkin[$ii]->venue->geolong;
          $result['photo']      = $xml->checkin[$ii]->user->photo;
          $result['display']    = $xml->checkin[$ii]->display;
          $result['shout']      = $xml->checkin[$ii]->shout;
          $result['created']    = $xml->checkin[$ii]->created;
     
        if (!is_null($result['latitude'] )) $ret[$ii] = $result;
        $ii++;
        }
    }
         
    if ($ii > 0) {
      $xml_file = FOURSQUARE_XML_FILE_PATH.$foursquare_id.".xml";
     
      $doc = new DOMDocument();
      $doc->formatOutput = true;

      $r = $doc->createElement( "friendcheckins" );
      $doc->appendChild( $r );

      foreach( $ret as $checkins )
      {
      $b = $doc->createElement( "checkin" );

      $display = $doc->createElement( "display" );
      $display->appendChild(
      $doc->createTextNode( $checkins['display'] )
      );
      $b->appendChild( $display );

      $shout = $doc->createElement( "shout" );
      $shout->appendChild(
      $doc->createTextNode( $checkins['shout'] )
      );
      $b->appendChild( $shout );

      $created = $doc->createElement( "created" );
      $created->appendChild(
      $doc->createTextNode( $checkins['created'] )
      );
      $b->appendChild( $created );
      
      $photo = $doc->createElement( "photo" );
      $photo->appendChild(
      $doc->createTextNode( $checkins['photo'] )
      );
      $b->appendChild( $photo );
      
      $latitude = $doc->createElement( "latitude" );
      $latitude->appendChild(
      $doc->createTextNode( $checkins['latitude'] )
      );
      $b->appendChild( $latitude );
      
      $longitude = $doc->createElement( "longitude" );
      $longitude->appendChild(
      $doc->createTextNode( $checkins['longitude'] )
      );
      $b->appendChild( $longitude );

      $r->appendChild( $b );
      }
      $doc->save($xml_file);
    }
  }

}

function get_foursquare_xml($rss_value) {

    $url = "http://feeds.foursquare.com/history/".$rss_value.".kml";
 // $url = "http://feeds.playfoursquare.com/history/026d45564de36937b02de38a01095620.kml";
  $result = array();
  
  $count = 0;
  if($xml = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA)) {
        $result["name"]   = $xml->xpath("/kml/Folder/Placemark/name");
        $result["description"]    = $xml->xpath("/kml/Folder/Placemark/description");
        $result["updated"]    = $xml->xpath("/kml/Folder/Placemark/updated");
        $result["coordinates"] = $xml->xpath("/kml/Folder/Placemark/Point/coordinates");

        foreach($result as $key => $attribute) {
          $i=0;
          foreach($attribute as $element) {
            $ret[$i][$key] = (string)$element;
            $i++;
            }

        }
        $count++;
    }
    
    if ($count > 0) {
    
      $xml_file = FOURSQUARE_XML_FILE_PATH.$rss_value.".xml";
     
      $doc = new DOMDocument();
      $doc->formatOutput = true;

      $r = $doc->createElement( "checkins" );
      $doc->appendChild( $r );

      if (count($ret) > 0) {

      foreach( $ret as $checkins )
      {
        $b = $doc->createElement( "checkin" );

        $name = $doc->createElement( "name" );
        $name->appendChild(
        $doc->createTextNode( $checkins['name'] )
        );
        $b->appendChild( $name );

        $desc = $doc->createElement( "description" );
        $desc->appendChild(
        $doc->createTextNode( $checkins['description'] )
        );
        $b->appendChild( $desc );

        $upd = $doc->createElement( "updated" );
        $upd->appendChild(
        $doc->createTextNode( $checkins['updated'] )
        );
        $b->appendChild( $upd );
        
        list($lon, $lat,$dummy) = split(",",$checkins['coordinates'],3);
        
        $latitude = $doc->createElement( "latitude" );
        $latitude->appendChild(
        $doc->createTextNode( $lat )
        );
        $b->appendChild( $latitude );
        
        $longitude = $doc->createElement( "longitude" );
        $longitude->appendChild(
        $doc->createTextNode( $lon )
        );
        $b->appendChild( $longitude );
        
        $r->appendChild( $b );
      }
      }
      $doc->save($xml_file);
      
    }
    else {
      echo "get_foursquare_xml >>> No checkins! Rss Value: ".$rss_value."\n";
    }

}

?>
