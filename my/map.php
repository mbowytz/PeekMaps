<?php 
include_once('../include/session.php'); 
$loc = array();
$peekmap = $_GET['peekmap'];
$referrer = $_SERVER['HTTP_USER_AGENT'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$loc = $session->fetchPeekMapLocation($peekmap, $referrer, $user_agent, $ip_address);
//print_r($loc);
$latitude = str_replace(".","",$loc['latitude']*1000000);
$longitude = str_replace(".","",$loc['longitude']*1000000);

?>
<html>
<head>
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
padding-top:3px;
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
#pointer {
position: absolute;
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
  if (mapWidth > 800) mapWidth = 800;
  if (mapHeight > 600) mapHeight = 600;
  mapHeight -= 20;
document.getElementById('map').src = "http://www.google.com/mapdata?latitude_e6=<?php echo $latitude; ?>&longitude_e6=<?php echo $longitude; ?>&w="+mapWidth+"&h="+mapHeight+"&hl=en&zl=5&cc=us&tstyp=1";
document.getElementById('pointer').style.top = mapHeight / 2 - 62;
document.getElementById('pointer').style.left = mapWidth / 2 - 65;
}
function init(){
  var hasTimedOut = false;
  var resizeHandler = function() {
    mapSize(); 
    return false;
  };
 
  window.onresize = function() {
    var time = 100;//resize timout in milliseconds 
    if (hasTimedOut !== false) {
      clearTimeout(hasTimedOut);
    }
    hasTimedOut = setTimeout(resizeHandler, time); 
  };
}
</script>
</style>
</head>
<body onLoad="mapSize();">
<div id="titlebar"><span id="lastupd" style="font-weight: bold; font-size: 10pt; color: rgb(239, 239, 239); font-family: arial,helvetica,sans-serif;">  57 minutes ago</span>
<span style="right: 0px; float: right; position: absolute; top: 0px;"><img src="images/logo.png"> </span></div>
<div id="map_canvas"><img id="map"/><img id="pointer" src="images/big-giant-hand.png"></div>
<div id="footerbar"></div>
<script>init();</script>
</body>
</html>
