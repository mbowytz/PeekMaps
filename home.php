   <div class="grid_9" id="content-body">
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam id neque quis tellus vestibulum mollis. Duis aliquet, enim id dignissim ullamcorper, risus nibh venenatis odio, vel consequat velit dolor at tortor. Aliquam a nulla in nulla interdum suscipit ut et nisi. Praesent at libero vitae tellus molestie semper eu sed orci. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed risus leo, aliquam at ullamcorper ac, faucibus ut est. Quisque pulvinar lobortis sem, at eleifend quam vestibulum sed. Aenean non lorem at lectus porta accumsan id et justo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Etiam mattis sem ac nibh tempor id congue odio accumsan. Integer tincidunt ante vel odio faucibus accumsan. Nunc feugiat ante sed elit scelerisque ac ullamcorper nisi vestibulum.</p>
		<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse potenti. Vivamus porta lorem ac elit suscipit eget vestibulum sem viverra. Donec interdum, diam nec tincidunt consequat, nibh nisi condimentum velit, sed bibendum lacus arcu lobortis odio. Vivamus felis quam, tincidunt ac luctus ac, dignissim ut orci. Donec arcu ipsum, fermentum vel ullamcorper nec, venenatis mattis libero. In hac habitasse platea dictumst. Nulla facilisi. Aenean at sem arcu, vel venenatis diam. Nunc luctus odio vitae mi adipiscing nec ornare diam ullamcorper. Integer nisi enim, luctus vitae dignissim quis, fermentum sit amet nisi. Nulla sollicitudin gravida orci, at dapibus mi imperdiet id. Aenean eu arcu eget erat posuere molestie. Fusce pulvinar, lorem sit amet lacinia fringilla, tellus nulla tincidunt tortor, at blandit lectus nisl id justo.</p>
	</div>
    <div class="grid_3">
		<h2>Login</h2>
		<form id="content-body" name="login" action="process.php" method="POST"> 
			<ul id="login">
				<li>
				  <b>User Name</b>
				</li>
				<li>
				  <input type="text" size="20" name="user" maxlength="30" value="" /><br/>
				</li>
				<li>
				  <b>Password </b>
				</li>
				<li>
				  <input type="password" size="20" name="pass" maxlength="30" value="" />
				</li>  
				<li>
				  <b>Keep me logged in:&nbsp;<input id="user"  type="checkbox" name="remember" ></b>
				</li>
				<li >				  
				  <div class="btn btn_red"><a href="javascript:document.login.submit();">Login</a><span></span></div> 
				</li>
			 </ul>
			 <input  type="hidden" name="login" value="1">
		</form> 
		<br/>  
		<br/>		
		<h2>New User?</h2>
		<div id="content-body" style="padding:10px;">
			<div style="overflow: auto; margin-left: auto;margin-right: auto;width: 122px;">
				<div class="btn btn_red"><a href="index.php?action=register">Sign Up Here</a><span></span></div>
			</div>
		</div>
	</div>