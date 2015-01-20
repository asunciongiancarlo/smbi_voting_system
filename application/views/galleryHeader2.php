<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>San Miguel International Beer</title>
<meta name="Viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="San Miguel International Beer">
<!-- CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH?>css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH?>css/custom2.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH?>css/custom.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH?>css/itemsDB.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH?>css/admin.css">
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!--[if IE]>
<style>/* this style block is for IE */


.h3Title {*width:70%;}
.items-fil{ *margin-top:-2px!important; }
.searhPanel { *margin-left:65px!important; *margin-top:2px!important; }

.pagination { *padding-top: 10px!important;  *padding-bottom: 10px!important;  *padding-left:15px!important; *padding-right:15px!important;}
.firstnum { *margin-right:-15px!important; }


</style>
<![endif]-->


<script type="text/javascript" src="<?php echo HTTP_PATH ?>js/jquery-1.8.0.js"></script>
<script type="text/javascript" src="<?php echo HTTP_PATH ?>js/galleria-1.3.3.min.js"></script>
<style>
#mask2 {
	position: absolute;
	left: 0;
	top: 0;
	z-index: 90000;
	background-color:black;
	display: none;
}

.popupBox {
	
	min-height: 900px;
	position: absolute;
	width:90%;
	left: 0;
	top: 0;
	display: none;
	z-index: 99999;
	padding: 20px; 
}
strong{
	color: #710002;
}
</style>

</head>

<body data-spy="scroll" data-target=".bs-docs-sidebar" data-twttr-rendered="true">
<div class="container">
	<header>
    	<div class="logo">
       		<a href='<?php echo HTTP_PATH ?>'><img src="<?php echo HTTP_PATH ?>img/logo.png" width="420" height="45"></a>
        </div>
		<div class="nav-top">
        	<a href="<?php echo HTTP_PATH ?>" class="nav-btn" >Home</a>
            <a href="<?php echo HTTP_PATH ?>users/personal_account/view/<?php echo $_SESSION['user_id']; ?>" class="nav-btn" >My Account</a>
      	</div>
        <div class="clear"></div>
		<p class='acc_info'>
			Welcome <?php echo $_SESSION['full_name'];  
			if($_SESSION['super_admin']=='y') echo " (Super Admin)"; ?>! | <a href='<?php echo HTTP_PATH.'login/logout' ?>' style='color:white;'>Log-out</a>
			<br/> <?php echo $_SESSION['countryName']; ?>
		</p>
	</header>

    <div class="clear"></div>
	
	<div class="breadcrums" style='text-align:left'>
		<ul>
			<li><a href="<?php echo HTTP_PATH ?>"> Home </a></li>
			<?php echo $breadCrumbs ?>
		</ul>
	</div>
	
	<div class="content-gal-common">
         <div class="title-content-ilike" style='width: 100%;'>
		   
		    <h2 style='float:left;color:#202020'></h2>
		   
		   <?php if(isset($galTitle)) { ?> 
        	<h3 style='float:left;'><?php echo $galTitle ?></h3>
			
		
			<div class="search_tools">
			<div style='width:109px;height:44px;margin-left:-125px'>
				
			</div>
				<?php 
					if(!isset($itemPreview) & $galTitle != 'eCatalogue')
						include('search_toolsGallery.php') 
				?>
			</div>
			
          <?php }  ?>
		</div>         
        <div class="clear"></div>
        <br/>
		<div id="Icontainer" style="padding-left:75px;">
			<?php include($vfile);   ?>
		</div>
		<br/>        
        <div class="clear"></div>  
    </div>
	<!-- item zoom -->
	<input type='hidden' id='hiddenImage' value=''>
	<!-- item zoom -->
    <br/>
<br/><br/><br/>

</div>
</body>


<script>
	function viewfilter()
	{

		var x = document.getElementById('viewfilter').style.display;
		if(x == 'none')
			document.getElementById('viewfilter').style.display = 'block';
		else
			document.getElementById('viewfilter').style.display = 'none';

	}

   function back()
        {
		 if(isComplete()) window.location.href ='<?php echo isset($backURL) ? $backURL:"" ?>';
		}
   function next()
        {
		 if(isComplete()) window.location.href ='<?php echo isset($frwURL) ? $frwURL:"" ?>';
		}
		
  function finish()
      {
	    if(isComplete()) window.location.href ='<?php echo isset($finishURL) ? $finishURL:"" ?>';
	  }
  function isComplete()
    {
	 var list =document.getElementsByClassName('ptitle');
	 var isThereAnError = false;
	 
	 for(i=0;i<list.length;i++)
	   {
	 	 if($('#'+list[i].id).attr('alt')==''){list[i].style.color='red';isThereAnError=true; }
			  
	   }
	   if(isThereAnError) 
	     alert('Please Vote on the all item, "red title"');
	   else
	     return true;
	      
	}
	 
	  
  function showBox(obj)
	 {
	   var pos = $(obj).offset();
       var l = pos.left; 	   
       var t = pos.top ;
       $('#searchPanel').css('top',t+50); 	   
       $('#searchPanel').css('left',l-240);
       $('#searchPanel').effect('slide',{direction:'right'},'slow');
 
        	   
	 }
</script>
<script src="<?php echo HTTP_PATH?>js/bootstrap.min.js" type="text/javascript"></script>

<script src="<?php echo HTTP_PATH?>js/jquery-1.8.3.min.js"></script>
<script src="<?php echo HTTP_PATH?>js/jquery.effects.core.js" type="text/javascript"></script>
<script src="<?php echo HTTP_PATH?>js/jquery.effects.slide.js" type="text/javascript"></script>
<script src="<?php echo HTTP_PATH?>js/ajax.js"></script>


 
<script>
	
	// Load the classic theme
    Galleria.loadTheme('../../../js/galleria.classic.js');

    // Initialize Galleria
    Galleria.run('#galleria');	
	
	Galleria.configure({
		debug: false // debug is now off for deployment
	});
	
	Galleria.ready(function() {
		this.bind('image', function(e) {
			var source = e.imageTarget.src; // src is the currently showing image source
			document.getElementById('hiddenImage').value = source;
			//alert(source);
		});
	});
</script>

<script type="text/javascript" src="<?php echo HTTP_PATH?>js/jquery.elevatezoom.js"></script>
<script>
	
	var ctr = 0;
	
	
	Galleria.ready(function() {
		this.bind('image', function(e) {
			var s = e.imageTarget; // src is the currently showing image source
			$(s).attr('id', 'zoom');
			$(s).attr('data-zoom-image', s.src);
			
			$(".zoomContainer").remove();
			//$("div").remove(".zoomContainer");
			$("#zoom").elevateZoom();
			$('.zoomWindow').css("background-image",s.src);
		});
	});
	

</script>



</html>
