<?php
/*                                                                                                                                                                                 
Template Name: LoginTemplate                                                                                                                                                              
*/
?>
<? 
$blogurl = get_option('siteurl');
$postlink = $_GET['redirect']; 
?>

<style type="text/css">
	/****** Login Stuff: REMOVE IF WE DONT USE THIS ******/

#login{
	margin: 0 auto;
	width: 268px;
	padding: 12px;
	font-family:Arial, Helvetica, sans-serif;
	padding: 16px 16px 40px 16px;
	font-weight: bold;
	background:#fff;
}

#login label{
	color: #ee2c52;
	padding:2px;
}

#login input{
	border:1px solid #bdbdbd;
	padding:2px;
}

#login a{
        text-decoration:none;
        color:#ccc;
        font-size:12px;
        padding-top:10px;
		padding: 0 5px;
}

#login a:hover{
        background-color:#505050;
        color:#f1f1f1;
}

#backtoblog {
        background-color:#686852;
        text-align:right;
        width:100%;
        color:#FFFFFF;
}

.forgetmenot{
	font-size:12px;
}

p.forgetmenot label{
	color:#f1f1f1;
}

#backtoblog a{
        color:#FFFFFF;
        font-size:small;
}

.submit{
        float:right;
        padding-right:10px;
}
</style>
 
<div id="login">

<form name="loginform" id="loginform" action="<?=$blogurl ?>/wp-login.php" method="post">
	<p>
		<label>Username<br />
		<input type="text" name="log" id="user_login" class="input" value="" size="20" tabindex="10" /></label>
	</p>
	<p>
		<label>Password<br />
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
 
	</p>
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" value="Log In" tabindex="100" />
		<input type="hidden" name="redirect_to" value="<?=$postlink ?>#respond" />
		<input type="hidden" name="testcookie" value="1" />
	</p>
	<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> Remember Me</label></p>	
</form>
 
<p>
<a href="<?=$blogurl ?>/wp-login.php?action=register" title="Register">Register</a>
<a href="<?=$blogurl ?>/wp-login.php?action=lostpassword" title="Password Lost and Found">Lost your password?</a>
</p>
</div>
  
<script type="text/javascript">
try{document.getElementById('user_login').focus();}catch(e){}
</script>