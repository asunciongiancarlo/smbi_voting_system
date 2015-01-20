<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>San Miguel International Beer</title>
<meta name="Viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="San Miguel International Beer">
<!-- CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH?>css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH?>css/custom.css">
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->


</head>

<body data-spy="scroll" data-target=".bs-docs-sidebar" data-twttr-rendered="true">
<div class="container">
	<header>
    	<div class="logo">
       		<a href='<?php echo HTTP_PATH ?>'><img src="<?php echo HTTP_PATH ?>img/logo.png" width="420" height="45"></a>
        </div>
        
        <div class="clear"></div>
	</header>
        
    
    
    <div class="clear"><br/></div>
    	<div class="content-gal-common">
    	<div class="title-content-ilike" style='width: 100%;'>
        	<h2 style='float:left; color:#202020'>Common Gallery</h2>
        	<h2 style='float:right'><a href=""><img border=0 src='<?php echo HTTP_PATH?>img/filterIcon2.jpg'></a></h2>
        </div>        
        <div class="clear"></div>
        <br/>
      <!-- -->        
        <?php include($vfile);   ?>
		<br/>        
        <div class="clear"></div>  
    </div>
    <br/>

<br/><br/><br/>

<div id='searchPanel' style='display:none;position:absolute'></div>
</div>

</body>
<script src="<?php echo HTTP_PATH?>js/bootstrap.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script src="<?php echo HTTP_PATH?>js/ajax.js"></script>

</html>
