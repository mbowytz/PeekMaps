<?php 
include_once('../include/session.php'); 
$loc = array();
$username = $_GET['username'];
$loc = $session->fetchUserLocation($username);
print_r($loc);
$latitude = str_replace(".","",$loc['latitude']*1000000);
$longitude = str_replace(".","",$loc['longitude']*1000000);

?>
<html><head>
<style>
html,div {
  display:block;
}
body {
color:black;
font:0.8em arial,sans-serif;
margin:0;
padding:20px 0 17px;
overflow:hidden;
}
#map_canvas {
height:100%;
padding-bottom:20px;
left:0;
overflow:hidden;
position:absolute;
top:0;
width:100%;
}
#titlebar {
background-color:#343434;
background-image:url("./images/gradient.png");
background-repeat:repeat-x;
font-family:arial;
height:20px;
left:0;
position:absolute;
top:0;
width:100%;
z-index:777;
}
#footerbar {
background-color:#EFEFEF;
border-top:1px solid #FF850A;
bottom:0;
font-size:12px;
height:16px;
left:0;
position:absolute;
width:100%;
}
</style>
<script>
function mapSize() {
var mapWidth = 0, mapHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    mapWidth = window.innerWidth;
    mapHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    mapWidth = document.documentElement.clientWidth;
    mapHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    mapWidth = document.body.clientWidth;
    mapHeight = document.body.clientHeight;
  }  
  if (mapWidth > 500) mapWidth = 500;
  if (mapHeight > 500) mapHeight = 500;
  mapHeight -= 20;
document.getElementById('map').src = "http://www.google.com/mapdata?latitude_e6=<?php echo $latitude; ?>&longitude_e6=<?php echo $longitude; ?>&w="+mapWidth+"&h="+mapHeight+"&hl=en&zl=5&cc=us&tstyp=1";
}
</script>
</style>
</head>
<body onresize="mapSize();" onload="mapSize();">
<div id="titlebar">
<span id="lastupd" style="font-weight: bold; font-size: 10pt; color: rgb(239, 239, 239); font-family: arial,helvetica,sans-serif;">57 minutes ago</span>
<span style="right: 0px; float: right; position: absolute; top: 0px;"><img src="images/logo.png"> </span></div>
<div id="map_canvas"><img id="map"/></div>
<div id="footerbar"></div>
</body>
</html>