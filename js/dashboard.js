    var cm_map;
    var cm_openInfowindow;
    var cm_mapMarkers = [];
    var cm_mapHTMLS = [];
    var lastlinkid;
     
    // Change these parameters to customize map
    //var param_wsId = "od6";
    //var param_ssKey = "o16162288751915453340.4402783830945175750";
    var param_useSidebar = true;
    var param_titleColumn = "title";
    var param_descriptionColumn = "description";
    var param_latColumn = "latitude";
    var param_lngColumn = "longitude";
    var param_rankColumn = "rank";
    var param_iconType = "green";
    var param_iconOverType = "orange";
     
    var param_shortLinkName = "shortlink_name"; 
    var param_shortLinkClicks = "clicks";
    
    /**
     * Loads map and calls function to load in worksheet data.
     */
    function cm_load() {  
      var myLatlng = new google.maps.LatLng(myLat,myLon); //40.907787,-79.359741);
      var myOptions = {
        zoom: 2,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      }
      cm_map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
     
      cm_getJSON();
      cm_getShortLinkJSON();
    }
     
    /**
     * Function called when marker on the map is clicked.
     * Opens an info window (bubble) above the marker.
     * @param {Number} markerNum Number of marker in global array
     */
    function cm_markerClicked(markerNum) {
      var infowindowOptions = {
        content: cm_mapHTMLS[markerNum]
      }
      var infowindow = new google.maps.InfoWindow(infowindowOptions);
      infowindow.open(cm_map, cm_mapMarkers[markerNum]);
      cm_setInfowindow(infowindow);
    }
    
    function cm_loadShortLinks(json) {
     //alert("shortlink Json.length:"+json.length);
     for (var i = 0; i < json.length; i++) {
        var entry = json[i];
        
        var shortlinkUL = document.createElement("ul");
        
        var shortlinkLI1 = document.createElement("li");
        var titleH2 = document.createElement("h2");
        var titletext = entry[param_shortLinkName];
        titleH2.appendChild(document.createTextNode(titletext));
        shortlinkLI1.appendChild(titleH2);
        
        var shortlinkLI2 = document.createElement("li");
        var bClickCount = document.createElement("b");
        var counttext = entry[param_shortLinkClicks]+" clicks";
        bClickCount.appendChild(document.createTextNode(counttext));
        shortlinkLI2.appendChild(bClickCount);
        
        var shortlinkLI3 = document.createElement("li");
        var statsLinkA = document.createElement("a");
        statsLinkA.setAttribute("href","#"); //"javascript:something()");
        statsLinkA.appendChild(document.createTextNode("View Stats"));
        shortlinkLI3.appendChild(statsLinkA);
        
        var shortlinkLI4 = document.createElement("li");
        var inputDeleteLink = document.createElement("input");
        inputDeleteLink.setAttribute("type","submit");
        inputDeleteLink.setAttribute("name","submit");
        inputDeleteLink.setAttribute("value","Delete Link");
        shortlinkLI4.appendChild(inputDeleteLink);
        
        //Put it all together now!
        shortlinkUL.appendChild(shortlinkLI1);
        shortlinkUL.appendChild(shortlinkLI2);
        shortlinkUL.appendChild(shortlinkLI3);
        shortlinkUL.appendChild(shortlinkLI4);
        //alert("titletext:"+titletext+" counttext:"+counttext);
        //stick on the page
        document.getElementById("shortlinks").appendChild(shortlinkUL);
     }
    
    }
    
    /** 
     * Called when JSON is loaded. Creates sidebar if param_sideBar is true.
     * Sorts rows if param_rankColumn is valid column. Iterates through worksheet rows, 
     * creating marker and sidebar entries for each row.
     * @param {JSON} json Worksheet feed
     */       
    function cm_loadMapJSON(json) {
      var usingRank = false;
     
      if(param_useSidebar == true) {
        var sidebarTD = document.createElement("li");
        sidebarTD.setAttribute("width","150");
        sidebarTD.setAttribute("valign","top");
        var sidebarDIV = document.createElement("div");
        sidebarDIV.id = "cm_sidebarDIV";
        //sidebarDIV.style.overflow = "auto";
        //sidebarDIV.style.height = "450px";
        //sidebarDIV.style.fontSize = "11px";
        //sidebarDIV.style.color = "#000000";
        sidebarTD.appendChild(sidebarDIV);
        document.getElementById("sidebar-inner").appendChild(sidebarTD);
      }
     
      var bounds = new google.maps.LatLngBounds();
       
      for (var i = 0; i < json.length; i++) {
        var entry = json[i];
                
        if(entry[param_latColumn]) {
          var lat = parseFloat(entry[param_latColumn]);
          var lng = parseFloat(entry[param_lngColumn]);
          var point = new google.maps.LatLng(lat,lng);
          
          var html = "<div style='font-size:12px'>";
          html += "<strong>" + entry[param_titleColumn] 
                  + "</strong>";
          var label = entry[param_titleColumn];          
          var rank = parseInt(entry[param_rankColumn]);          
          if(entry[param_descriptionColumn]) {
            html += "<br/>" + entry[param_descriptionColumn];
          }
          html += "</div>";
     
          // create the marker
          var marker = cm_createMarker(cm_map,point,label,html,rank);
          // cm_map.addOverlay(marker);
          cm_mapMarkers.push(marker);
          cm_mapHTMLS.push(html);
          bounds.extend(point);
        
          if(param_useSidebar == true) {          
            //markerA.style.color = "#000000";
            var markerA = document.createElement("div");
            markerA.setAttribute("id","peekfriend"+rank);
            var sidebarText = label;
            var markerA2 = document.createElement("a");
            markerA2.setAttribute("href","javascript:cm_markerClicked('" + i +"')");
            markerA2.appendChild(document.createTextNode(sidebarText));
            markerA.appendChild(markerA2);//document.createTextNode(sidebarText));
            sidebarDIV.appendChild(markerA);
            sidebarDIV.appendChild(document.createElement("br"));
          } 
        }
      }
     
      cm_map.fitBounds(bounds);
      cm_map.setCenter(bounds.getCenter());
    }
     
    function cm_setInfowindow(newInfowindow) {
      if (cm_openInfowindow != undefined) {

        cm_openInfowindow.close();
      }
      if (lastlinkid != undefined) {
        document.getElementById(lastlinkid).style.background="#ffffff";     
      }
      cm_openInfowindow = newInfowindow;
    }
     
    /**
     * Creates marker with ranked Icon or blank icon,
     * depending if rank is defined. Assigns onclick function.
     * @param {GLatLng} point Point to create marker at
     * @param {String} title Tooltip title to display for marker
     * @param {String} html HTML to display in InfoWindow
     * @param {Number} rank Number rank of marker, used in creating icon
     * @return {GMarker} Marker created
     */
    function cm_createMarker(map, latlng, title, html, rank) {
      var iconSize = new google.maps.Size(20, 34);
      var iconShadowSize = new google.maps.Size(37, 34);
      var iconHotSpotOffset = new google.maps.Point(9, 0); // Should this be (9, 34)?
      var iconPosition = new google.maps.Point(0, 0);
      var infoWindowAnchor = new google.maps.Point(9, 2);
      var infoShadowAnchor = new google.maps.Point(18, 25);
     
      var iconShadowUrl = "http://www.google.com/mapfiles/shadow50.png";
      var iconImageUrl;
      var iconImageOverUrl;
      var iconImageOutUrl;
     
      if(rank > 0 && rank < 100) {
        iconImageOutUrl = "http://gmaps-samples.googlecode.com/svn/trunk/" +
            "markers/" + param_iconType + "/marker" + rank + ".png";
        iconImageOverUrl = "http://gmaps-samples.googlecode.com/svn/trunk/" +
            "markers/" + param_iconOverType + "/marker" + rank + ".png";
        iconImageUrl = iconImageOutUrl;
      } else { 
        iconImageOutUrl = "http://gmaps-samples.googlecode.com/svn/trunk/" +
            "markers/" + param_iconType + "/blank.png";
        iconImageOverUrl = "http://gmaps-samples.googlecode.com/svn/trunk/" +
            "markers/" + param_iconOverType + "/blank.png";
        iconImageUrl = iconImageOutUrl;
      }
      
      
      var markerShadow =
          new google.maps.MarkerImage(iconShadowUrl, iconShadowSize,
                                      iconPosition, iconHotSpotOffset);
     
      var markerImage =
          new google.maps.MarkerImage(iconImageUrl, iconSize,
                                      iconPosition, iconHotSpotOffset);
     
      var markerImageOver =
          new google.maps.MarkerImage(iconImageOverUrl, iconSize,
                                      iconPosition, iconHotSpotOffset);
     
      var markerImageOut =
          new google.maps.MarkerImage(iconImageOutUrl, iconSize,
                                      iconPosition, iconHotSpotOffset);
     
      var markerOptions = {
        title: title,
        icon: markerImage,
        shadow: markerShadow,
        position: latlng,
        map: map
      }
     
      var marker = new google.maps.Marker(markerOptions);
      var linkid = "peekfriend"+rank;
      google.maps.event.addListener(marker, "click", function() {
        var infowindowOptions = {
          content: html
        }
        var infowindow = new google.maps.InfoWindow(infowindowOptions);
        cm_setInfowindow(infowindow);
        infowindow.open(map, marker);
        marker.setIcon(markerImageOut);
        //alert("linkid:"+linkid+", lastlinkid:"+lastlinkid);
        //document.getElementById(lastlinkid).style.background="#ffffff";
        document.getElementById(linkid).style.background="#ffff00";
        lastlinkid=linkid;
        
      });
      
      google.maps.event.addListener(marker, "mouseover", function() {
        marker.setIcon(markerImageOver);
      });
      google.maps.event.addListener(marker, "mouseout", function() {
        marker.setIcon(markerImageOut);
      });
     
      return marker;
    }
     
    function  cm_getShortLinkJSON() {
      // Retrieve the JSON feed.
      var script = document.createElement('script');

      var jsonSource = 'http://peeksocial.peeknet.net/peekmaps/db_json.php?name=shortlinks&sid='+sid+'&callback=cm_loadShortLinks';
      //alert("Src:"+jsonSource);
      script.setAttribute('src', jsonSource);
      script.setAttribute('id', 'jsonScript');
      script.setAttribute('type', 'text/javascript');
      document.documentElement.firstChild.appendChild(script);
    
    }
    /**
     * Creates a script tag in the page that loads in the 
     * JSON feed for the specified key/ID. 
     * Once loaded, it calls cm_loadMapJSON.
     */
    function cm_getJSON() {
     
      // Retrieve the JSON feed.
      var script = document.createElement('script');

      var jsonSource = 'http://peeksocial.peeknet.net/peekmaps/db_json.php?name=friends&sid='+sid+'&callback=cm_loadMapJSON';
      //alert("friendsource: "+jsonSource);
      script.setAttribute('src', jsonSource);
      script.setAttribute('id', 'jsonScript');
      script.setAttribute('type', 'text/javascript');
      document.documentElement.firstChild.appendChild(script);
    }
    
    setTimeout('cm_load()', 500); 
    
    var options = { 
        target:        '#output1',   // target element(s) to be updated with server response 
        //beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse  // post-submit callback 
 
        // other available options: 
        //url:       url         // override for form's 'action' attribute 
        //type:      type        // 'get' or 'post', override for form's 'method' attribute 
        //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
        //clearForm: true        // clear all form fields after successful submit 
        //resetForm: true        // reset the form after successful submit 
 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    }; 
 
    // bind form using 'ajaxForm' 
    $('#myForm').ajaxForm(options); 
    
     
