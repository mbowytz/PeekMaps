    <div class="grid_3">&nbsp;</div>
	<div class="grid_6">
        
		<style>
            h2 {
                color:black;
             }
        </style>
		<form id="content-body" style="padding-left:10px;padding-top:10px;" name="register" action="process.php" method="POST"> 
            <fieldset>
                <h2>First - Who are you?</h2>
                <ul id="login">
                    <li>
                      <b>Your Name</b>
                    </li>
                    <li>
                      <input type="text" size="30" name="username" maxlength="30" value="" /> <?php echo $form->error("username"); ?>
                    </li>
                    <li>&nbsp;</li>
                    <li>
                      <b>Your Peek's Primary Email Address</b>
                    </li>
                    <li>
                      <input type="text" size="30" name="email" maxlength="30" value="" /><?php echo $form->error("email"); ?>
                    </li>
                    <li>&nbsp;</li>
                    <li>
                      <b>Password </b>
                    </li>
                    <li>
                      <input type="password" size="30" name="password" maxlength="30" value="" /><?php echo $form->error("password"); ?>
                    </li>  		
                    <li>&nbsp;</li>				            
                </ul>
			 </fieldset>
             <fieldset>
                 <h2>Choose the level that you wish to be tracked at:</h2> <?php echo $form->error("tracklevel"); ?>
                 <ul id="login">
                    <li>
                      <input type="radio" name="tracklevel" value="1" /> Most Accurate Location Possible
                    </li>
                    <li>&nbsp;</li>
                    <li>
                      <input type="radio" name="tracklevel" value="2" /> Nearest Town/City
                    </li>
                    <li>&nbsp;</li>				
                    <li>				  
                      <div class="btn btn_red"><a href="#" onclick="javascript:document.register.submit();">Sign Up!</a><span></span></div> 
                    </li>
                    <input type="hidden" name="signup" value="1">
                </ul>
                <input  type="hidden" name="register" value="2">
             </fieldset>
		</form> 
		<br/>  		
	</div>
	<div class="grid_3">&nbsp;</div>