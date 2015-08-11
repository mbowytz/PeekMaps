    <div class="grid_4">&nbsp;</div>
	<div class="grid_4">
        
		
		<form id="content-body" style="padding-left:10px;padding-top:10px;" name="step1" action="index.php?action=step2" method="POST"> 
            <fieldset>
            <h2>First - Who are you?</h2>
                <ul id="login">
				<li>
				  <b>Your Name</b>
				</li>
				<li>
				  <input type="text" size="30" name="username" maxlength="30" value="" /><br/>
				</li>
				<li>&nbsp;</li>
				<li>
				  <b>Your Peek's Primary Email Address</b>
				</li>
				<li>
				  <input type="text" size="30" name="username" maxlength="30" value="" /><br/>
				</li>
				<li>&nbsp;</li>
				<li>
				  <b>Password </b>
				</li>
				<li>
				  <input type="password" size="30" name="password" maxlength="30" value="" />
				</li>  		
				<li>&nbsp;</li>				
				<li style="float:right;padding-right:40px;" >				  
				  <div class="btn btn_red" ><a href="#" onclick="javascript:document.step1.submit();">Next</a><span></span></div> 
				</li>
			 </ul>
			 </fieldset>
             <fieldset>
             <h2>Choose the level that you wish to be tracked at:</h2>
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
				  <div class="btn btn_red"><a href="#" onclick="javascript:document.step2.submit();">You're Done - Click to Continue!</a><span></span></div> 
				</li>
			</ul>
			<input  type="hidden" name="register" value="2">
             </fieldset>
		</form> 
		<br/>  		
	</div>
	<div class="grid_4">&nbsp;</div>