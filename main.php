    </p>
    <style>
    
        #sidebar {
            border:1px solid black;
            overflow: auto; 
            width:240px;
            height:380px;
            left:0;
            float:left;
        }
        
        #sidebar ul {
            text-shadow:0 -1px 1px rgba(0,0,0,0.5);
            list-style:none outside none;
            padding-top:5px;
            padding-bottom:5px;
            margin-left:-25px;
            text-align:left;
            font-size:1em;
        }
        
        #sidebar ul li {
        
        }
    
    </style>
    <script type="text/javascript">
       var myLat = 40.00000;
       var myLon = -79.999343;
       var sid = "<?php echo $_SESSION['securityid']; ?>";
    </script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script> 
    <script src="./js/dashboard.js" type="text/javascript"></script>
    <script src="./js/jquery-1.4.2.min.js" type="text/javascript"></script> 
    <script src="./js/jquery.form.js" type="text/javascript"></script>
    
    <script src="./js/ui.core.js" type="text/javascript"></script> 
    <script src="./js/ui.tabs.js" type="text/javascript"></script> 
    
    <link rel="stylesheet" href="./css/ui.tabs.css" type="text/css" media="print, projection, screen"> 
    <script type="text/javascript"> 
    $(function() {
        //$('#rotate > ul').tabs({ fx: { opacity: 'toggle' } }); //.tabs('rotate', 2000);
         $('#rotate > ul').tabs();
    });
    </script>  

    <script>
    
    // prepare the form when the DOM is ready 
$(document).ready(function() { 
    var options = { 
        //target:        '#output1',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
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
}); 
 
// pre-submit callback 
function showRequest(formData, jqForm, options) { 
    // formData is an array; here we use $.param to convert it to a string to display it 
    // but the form plugin does this for you automatically when it submits the data 
    var queryString = $.param(formData); 
 
    // jqForm is a jQuery object encapsulating the form element.  To access the 
    // DOM element for the form do this: 
    // var formElement = jqForm[0]; 
 
    //alert('About to submit: \n\n' + queryString); 
 
    // here we could return false to prevent the form from being submitted; 
    // returning anything other than false will allow the form submit to continue 
    return true; 
} 
 
// post-submit callback 
function showResponse(responseText, statusText, xhr, $form)  { 
    // for normal html responses, the first argument to the success callback 
    // is the XMLHttpRequest object's responseText property 
 
    // if the ajaxForm method was passed an Options Object with the dataType 
    // property set to 'xml' then the first argument to the success callback 
    // is the XMLHttpRequest object's responseXML property 
 
    // if the ajaxForm method was passed an Options Object with the dataType 
    // property set to 'json' then the first argument to the success callback 
    // is the json data object returned by the server 
    //alert('Status:'+statusText);
    //alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + 
    //    '\n\nThe output div should have already been updated with the responseText.'); 

    /*
                <ul> 
                <li><h2>Link1</h2></li> 
                <li><b>15 clicks</b></li> 
                <li><a href="">View Stats</a></li>                
                <li><input type="submit" name="submit" value="Delete Link"> 
                </ul> 
        
        
            var markerA = document.createElement("div");
            markerA.setAttribute("id","peekfriend"+rank);
            var sidebarText = label;
            var markerA2 = document.createElement("a");
            markerA2.setAttribute("href","javascript:cm_markerClicked('" + i +"')");
            markerA2.appendChild(document.createTextNode(sidebarText));
            markerA.appendChild(markerA2);
            sidebarDIV.appendChild(markerA);
            sidebarDIV.appendChild(document.createElement("br"));*/
        
        //responseText = responseText.replace("&amp;","&").replace("&gt;", ">").replace("&lt;", "<").replace("&quot;","\"");

        //var resptxt = document.createTextNode(responseText);
        if (responseText.indexOf("Error") > -1) {
            /*var iTag = document.createElement("i");
            iTag.innerHtml = responseText;
            document.getElementById("shortlinks").appendChild(iTag);*/
            alert(responseText);
        }
        else {
            var ulTag = document.createElement("ul");
            ulTag.innerHTML = responseText;
            document.getElementById("shortlinks").appendChild(ulTag);
        }
        
} 
    
    
    </script>
    
	<div class="grid_12" id="content-body">
    <div id="logout" style="float:right;float: right; margin-top: 10px; margin-right:16px;">
        <a href="process.php?">Log Out</a>
    </div>
    <div class="clear"></div> 
    <div id="rotate" style="min-height:430px;  margin-top: -20px;"> 
            <ul> 
                <li><a href="#fragment-1"><span>My Dashboard</span></a></li> 
                <li><a href="#fragment-2"><span>My PeekMap</span></a></li> 
                <li><a href="#fragment-3"><span>Settings</span></a></li> 
                <li><a href="#fragment-4"><span><?php if ($session->logged_in) { echo "Logged In"; } else { echo "Not Logged In"; }?></span></a></li> 
                <li><a href="#fragment-5"><span><?php echo $_SESSION['username']; ?></span></a></li> 
            </ul> 
            <div id="fragment-1">
               <div class="grid_4" id="sidebar">
                 <ul id="sidebar-inner"> 
                 </ul>
               </div>
               <div  id="map_canvas" style="width:650px;height:380px;float:right;" >
               </div>
               <div class="clear"></div>  
            </div> 
            <div id="fragment-2"> 
               <style> 
                   #shortlink-new {
                       padding-bottom:5px;
                    }
                   #shortlink-new ul {
                        background:#DDDDDD;
                        list-style:none outside none;
                        font-size:1em;
                   }
                   
                   #shortlink-new ul li {
                    display:inline;
                   }
                   
                   #shortlinks 
                   {
                    overflow:auto;
                   }
                   
                   #shortlinks ul 
                   {                        
                        background:#ABABAB;
                        list-style:none outside none;
                        font-size:1em;
                        padding:10px;
                        color:#fff;
                        margin: 5px;
                   }
                   
                   #shortlinks ul li {
                    display:inline;                    
                   }                    
                   
                   #shortlinks ul a {
                    color:#fff;
                   }
                                      
                   #shortlinks ul li h2 {                    
                    font-size: 28px;
                    width:50%;
                    display:inline; 
                   }
               </style>  
                
                <div class="grid_10" id="shortlink-new"> 
                    <form id="myForm" name="newlink" action="newshortlink.php"> 
                      <ul> 
                        <li><label for="name">Short Link Name:</label></li> 
                        <li><input type="text" name="name" size="30" value=""></li> 
                        <li><input type="submit" name="submit" value="Create New Short Link"></li> 
                        <li><label class="error" for="name" id="output1">This field is required.</label> </li> 
                      </ul> 
                    </form> 
               </div> 
               <div class="clear"></div> 
             
               <div class="grid_10"  style="height:300px;" id="shortlinks"> 
               </div>            
            </div>
            <div id="fragment-3"> 
                <div class="clear"></div> 
                <div class="grid_4">
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                </div>
                <div class="grid_8">                
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                </div>                
                <div class="clear"></div> 
            </div> 
            <div id="fragment-4"> 
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
            </div> 
            <div id="fragment-5"> 
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
            </div> 
        </div> 
	</div>	
	<div class="clear"></div> 
