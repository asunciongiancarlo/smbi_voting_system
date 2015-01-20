<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>San Miguel International Beer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="San Miguel International Beer">
<!-- CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/custom2.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/custom.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/itemsDB.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/admin.css">

<script type="text/javascript" src="<?php echo HTTP_PATH ?>js/jquery-1.8.0.js"></script>
<script src="<?php echo HTTP_PATH ?>parsely/parsley_new.js"></script>

<link rel="stylesheet" href="<?php echo HTTP_PATH ?>datepicker/jquery_ui.css" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo HTTP_PATH ?>datepicker/jquery_ui.js"></script>
<script type="text/javascript" src="<?php echo HTTP_PATH ?>js/ajax.js"></script>
</head>

<body data-spy="scroll" data-target=".bs-docs-sidebar" data-twttr-rendered="true" id='campaignDiv'>
<div class="container" >
	<header>
    	<div class="logo">
       		<a href='<?php echo HTTP_PATH ?>'> <img src="<?php echo HTTP_PATH ?>img/logo.png" width="420" height="45"> </a>
        </div>
         <div class="nav-top">
        	<a onclick="cleariLikeSessionItems(this,'<?php echo HTTP_PATH ?>')" class="nav-btn" >Home</a>
            <a onclick="cleariLikeSessionItems(this,'<?php echo HTTP_PATH ?>users/personal_account/view/<?php echo $_SESSION['user_id']; ?>')" class="nav-btn" >My Account</a>
      	</div>
        <div class="clear"></div>
		<p class='acc_info'>
			Welcome <?php echo $_SESSION['full_name'];  
			if($_SESSION['super_admin']=='y') echo " (Super Admin)"; ?>! | <a href='<?php echo HTTP_PATH.'login/logout' ?>' style='color:white;'>Log-out</a>
			<br/> <?php echo $_SESSION['countryName']; ?>
		</p>
	</header>

        
    <div class="breadcrums" style='cursor:pointer;'>
    	<ul>
        	<li><a href="<?php echo HTTP_PATH ?>"> Home </a></li>
			<li><img src="<?php echo HTTP_PATH ?>img/arrow.png" width="3" height="5"></li>
			<li><a href="<?php echo HTTP_PATH."iWantCampaign/iWant/" ?>"> iWant Campaign </a></li>
     	</ul>
  	</div>
	
    
    <div class="clear"></div>
    
	<?php include($vfile); ?>
    
</div>
<br/><br/><br/>
</body>

<script src="<?php echo HTTP_PATH ?>js/bootstrap.min.js" type="text/javascript"></script>
<script>
	$('#DateFrom').datepicker({minDate: 0});
	$('#DateTo').datepicker({minDate: 0});
	
	$(function() {
		$( "#datepicker" ).datepicker();
		$( "#datepicker2" ).datepicker();
		$( "#datepicker3" ).datepicker();
		$( "#datepicker4" ).datepicker();
		$( "#datepicker4" ).datepicker();
		$( "#DateFrom" ).datepicker();
		$( "#DateTo" ).datepicker();
  });
</script>
<link rel="stylesheet" href="<?php echo HTTP_PATH ?>css/jquery.alert.css">
<script src="<?php echo HTTP_PATH ?>js/jquery.alert.js"></script>
</html>
