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
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH?>css/itemsDB.css">
<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->




</head>

<body data-spy="scroll" data-target=".bs-docs-sidebar" data-twttr-rendered="true">
<div class="container">
	<header>
    	<div class="logo">
       		<img src="<?php echo HTTP_PATH?>img/logo.png" width="420" height="45">
        </div>
        <div class="clear"></div>
	</header>

    <div class="clear"><br/></div>
	<div class="content-gal-common">
         <div class="title-content-ilike" style='width: 100%;'>
		   
		    <h2 style='float:left; color:#202020'><?php echo $title_header ?></h2>
			<h2 style='float:right'><a href=""><img border=0 src='<?php echo HTTP_PATH?>img/filterIcon2.jpg'></a></h2>
		   
		   <?php if(isset($galTitle)) { ?>
        	<h3 style='float:left;width:400px'><?php echo $galTitle?></h3>
        	<h3 style='float:right;'><a href="javascript:void(0)"><img onclick='showBox(this)' border=0 src='<?php echo HTTP_PATH?>img/filterIcon.jpg'></a></h3>
          <?php }  ?>
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
</div>
</body>
<script>
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
	function vote(id,vote)
	  {
	   ajax2("<?php echo HTTP_PATH . "gallery/vote/$cam_id/$email/" ?>"+id +"/"+vote+".html",'likeItems');
	  }
	  
  function showBox(obj)
	 {
	   var pos = jQuery(obj).offset();
       var l = pos.left; 	   
       var t = pos.top;
       jQuery('#likeITEMS').css('top',t-28); 	   
       jQuery('#likeITEMS').css('left',l-250);
       jQuery('#likeITEMS').effect('slide',{direction:'right'},'slow');
 
        	   
	 }
</script>
<script src="<?php echo HTTP_PATH?>js/bootstrap.min.js" type="text/javascript"></script>

<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script src="<?php echo HTTP_PATH?>js/jquery.effects.core.js" type="text/javascript"></script>
<script src="<?php echo HTTP_PATH?>js/jquery.effects.slide.js" type="text/javascript"></script>
<script src="<?php echo HTTP_PATH?>js/ajax.js"></script>
<script type="text/javascript" src="<?php echo HTTP_PATH?>js/multizoom.js"> </script>
<script>
 jQuery(document).ready(function($){

	$('#multizoom1').addimagezoom({ // multi-zoom: options same as for previous Featured Image Zoomer's addimagezoom unless noted as '- new'
		descArea: '#description', // description selector (optional - but required if descriptions are used) - new
		speed: 1500, // duration of fade in for new zoomable images (in milliseconds, optional) - new
		descpos: false, // if set to true - description position follows image position at a set distance, defaults to false (optional) - new
		zoomrange: [3, 10],
		magnifiersize: [400,300],
		magnifierpos: 'right',
		cursorshadecolor: '#fdffd5',
		cursorshade: true //<-- No comma after last option!
	});


	 
})
</script>

</html>
