<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>San Miguel International</title>

  <link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/loginstyle.css" />
  
</head>
<?php 
	$action=HTTP_PATH."login/authenticate";
?> 
<body class="login_bground">

	
	
	<div class="login_head" >
	<center>
	<div style='margin-top:-20px;width:555px;color: black;font-family: calibri;'>
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
			<img src="<?php echo HTTP_PATH ?>img/smb.png">
		</a>
	</div>
	
	
	<center>
	<?php include($vfile); ?>
	</center>
	
	
</body>
</html>

