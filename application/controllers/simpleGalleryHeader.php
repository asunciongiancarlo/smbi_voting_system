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
<script type="text/javascript" src="<?php echo HTTP_PATH ?>js/jquery-1.8.0.js"></script>
</head>

<body data-spy="scroll" data-target=".bs-docs-sidebar" data-twttr-rendered="true">
<div class="container">
	<header>
    	<div class="logo">
       		<a href='<?php echo HTTP_PATH ?>'><img src="<?php echo HTTP_PATH ?>img/logo.png" width="420" height="45"></a>
        </div>
	</header>

    <div class="clear" style='height:30px;'></div>
	

	
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
					{
						if(isset($eCatalogItems))
							include('search_toolsECGallery.php');
						else 
							include('search_toolsGallery.php'); 
					}
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
    <br/>
<br/><br/><br/>

</div>
</body>


<script>
	function submitSearch()
	{
		document.getElementById('active_page').value = 1;
		document.forms.frmSearch.submit();
	}

	function active_page(page)
	{
		document.getElementById('active_page').value = page;
		document.forms.frmSearch.submit();
	}

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
<script src="<?php echo HTTP_PATH?>js/jquery.elevatezoom.js"></script>
<script>
    $("#zoom").elevateZoom({gallery:'imageBar', cursor: 'pointer', galleryActiveClass: 'active', imageCrossfade: true, loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif'}); 
</script>
 
</html>
