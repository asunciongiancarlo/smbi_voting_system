<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>San Miguel International</title>

  <link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/loginstyle.css" />
  <!--[if IE]>
<style>/* this style block is for IE */



.login_foot{*margin-top:30px!important; }



</style>
<![endif]-->
  
  
</head>
<?php 
	$action=HTTP_PATH."login/authenticate";
?> 
<body class="login_bground">

	
	
	<div class="login_head" >
	<center>
	<div style='margin-top:-20px;width:555px;font-size: 14px;color:white; font-family: calibri;'>
		<?php 
		$CI =& get_instance();
		$CI2 =& get_instance();
		$CI2->load->library('alert');
		//MESSAGE ALERT
		if(isset($msg)){
			$CI->load->library('alert');
			echo $CI->alert->check($msg);
		}
		?>
	</div>
	</center>
		<a href="<?php echo HTTP_PATH ?>">
			<img src="<?php echo HTTP_PATH ?>img/smb.png" style="border:0px;">
		</a>
	</div>
	
	
	<center>
	<div id="login_body">
		
	<?php 
	if(preg_match('/(?i)msie [2-7]/',$_SERVER['HTTP_USER_AGENT']))
	{
		echo "<div style='height:170px;width:500px;font-size: 14px;color:white; font-family: calibri;padding-top:150px;'>
		Incompatible browser detected, please update your browser or 
		download Google Chrome or Firefox Fox for maximum system compatibility.<br/> Thank You! </div>";
	}else{
	?>
	<div class="login_font">
		SIGN IN TO YOUR ACCOUNT
	</div>
	<form  name="ycform"  method="POST" action="<?php echo $action ?>">
		<div class="login_smbi">
			<img src="<?php echo HTTP_PATH ?>img/mail.png" class="email_image_pos" style="float:left; margin: 16px 14px 0px 10px;">
			<input name="txtusername" type="text" style="height: 25px; color: white; font-size: 16px; font-family: calibri;  float:left; margin: -22px 7px 0px 59px;width: 72%;" class="login_right"/>
		</div>
		<label style="font-size: 12px;color:white; font-family: calibri;">User Name</label>
		
		<div style='clear:both;'></div>
		
		<div class="login_smbi">
			<img src="<?php echo HTTP_PATH ?>img/password.png" class="image_pos" style="float:left; margin: 16px 14px 0px 10px;">
			<input name="txtpassword" type="password" style="height: 25px; color: white; font-size: 16px; font-family: calibri;  float:left; margin: -29px 7px 0px 59px;width: 72%;" class="login_right"/>
		</div>
		<label style="font-size: 12px;color:white; font-family: calibri;">Password</label>
		<div style='clear:both;'></div>
		<br/>
		<input type="submit" value="SIGN IN" style='width:175px;height:50px;background:red;border:0;color:white;font-size:17px;'/>
		<br/>
		<?php  
			echo $csrf = "<input type='hidden' name='".$CI2->security->get_csrf_token_name()."' value='".$CI2->security->get_csrf_hash()."'>";
		?>
	</form>
	<?php 
	echo "<a href='".HTTP_PATH."forgot_password' style='color:white;font-family: calibri;font-size: 12px;text-decoration:none;margin-top:5px;'> Forgot Password?</a>";
	} ?>
	
	<div class="login_foot" style=' margin-top:39px;'>
		<img src="<?php echo HTTP_PATH ?>img/foot.png">
	</div>
	</div>
	</center>
	
	
</body>
</html>

