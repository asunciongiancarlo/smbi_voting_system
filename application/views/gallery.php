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


<!--[if IE]>
<style>/* this style block is for IE */

.stat1 { font-size:11px; }
.stat { font-size:11px; }
.pre-item {
    display: block;
    width: 24%;
    height: 30px;
    margin-bottom: 1.8em;
    background-image: url('../img/processBG2.jpg');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    filter: progid:DXImageTransform.Microsoft.AlphaImageLoader( src='<?php echo HTTP_PATH ?>img/processBG2.jpg', sizingMethod='scale');
    -ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader( src='<?php echo HTTP_PATH ?>img/processBG2.jpg', sizingMethod='scale')";
}
 
 .ser-item {
    display: block;
    width: 25%;
    height: 30px;
    margin-bottom: 1.8em;
    background-image: url('../img/processBG2.jpg');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    filter: progid:DXImageTransform.Microsoft.AlphaImageLoader( src='<?php echo HTTP_PATH ?>img/processBG.jpg', sizingMethod='scale');
    -ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader( src='<?php echo HTTP_PATH ?>img/processBG.jpg', sizingMethod='scale')";
}
.done-item {
    display: block;
    width: 24%;
    height: 30px;
    margin-bottom: 1.8em;
    background-image: url('../img/processBG2.jpg');
    background-repeat: no-repeat;
    background-size: 100% 100%;
    filter: progid:DXImageTransform.Microsoft.AlphaImageLoader( src='<?php echo HTTP_PATH ?>img/processBG.jpg', sizingMethod='scale');
    -ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader( src='<?php echo HTTP_PATH ?>img/processBG.jpg', sizingMethod='scale')";
}

.pagination { *padding-top: 10px!important;  *padding-bottom: 10px!important;  *padding-left:15px!important; *padding-right:15px!important;}
.firstnum { *margin-right:-15px!important; }

</style>
<![endif]-->



</head>

<body data-spy="scroll" data-target=".bs-docs-sidebar" data-twttr-rendered="true">
<div class="container">
	<header>
    	<div class="logo">
       		<img src="<?php echo HTTP_PATH?>img/logo.png" width="420" height="45">
        </div>
		<div class="logo" style='float:right;padding-top:20px;font-size:18px;color:white' >
			<b><?php  if(isset($voterINFO[0])){ "Welcome:";  echo $voterINFO[0]['fname'] .' ' . $voterINFO[0]['lname']; } ?></b> 
        </div>
        <div class="clear"></div>
	</header>
	<div class="content-gal-common" style='position:relative'>
         <div class="title-content-ilike" style='margin-left:-19px;'>
		  
		   <?php if(isset($galTitle)) { ?>
        	<h3 class="voting-title" style='float:left;'><?php echo $galTitle ?></h3>
        	<div class="totalsum" style='float:right;font-weight:bold;'>
			   <span>Total Wants:</span>
			   <span id='totalVote'>00</span>
			   <a href="javascript:void(0)">
			     <span class="vs" onclick="showBox(this)">View Summary</span>
			   </a>
			 </div>
        	 
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
</div>
</body>
<script>
   var iLikesCurRules ='<?php echo $curLevelCondition?>';
   var df ='';
   var curVotes =<?php echo isset($curVotes)? $curVotes:0?>;
   function back()
      {	 
	   window.location.href ='<?php echo isset($backURL) ? $backURL:"" ?>';
      }
	
   function next()
		{
		 if(isComplete()) window.location.href ='<?php echo isset($frwURL) ? $frwURL:"" ?>';
		}
		
  function finish()
	   {
	   if(isComplete()) window.location.href ='<?php echo isset($finishURL) ? $finishURL:"" ?>'; 
	   }
	   
	 function submit()
	   {
	    jConfirm("Are you sure to Submit?","SMBi POSM",function(r) {if(r)goSubmit()});
	   
	   }
	function goSubmit()
       {
	     submitVote('<?php echo isset($submitURL) ? $submitURL:"" ?>'); 
	   }

  function reload()
  {
	window.location.href = '<?php echo isset($reloadURL) ? $reloadURL:"" ?>'; 
  }  
 
 function vote(id,vote,obj)
  {
         //$('.unlike-btn-liked').attr('disabled','disabled');
        // $('.like-btn-liked').attr('disabled','disabled');
		 var review =<?php echo isset($review) ? $review:"false" ?>;
	     ajax2("<?php echo HTTP_PATH . "gallery/vote/$cam_id/$email/" ?>"+id +"/"+vote+".html",'totalVote');
	     //ajax2("<?php echo HTTP_PATH . "gallery/vote/$cam_id/$email/" ?>"+id +"/"+vote+"/totonly.html",'totalVote');
	     
		  
  }
 
  function showBox(obj)
	 {
	   var pos = jQuery('.content-gal-common').offset();
       var r = pos.right; 	   
       var w= pos.width; 	   
       var t = pos.top;
       jQuery('#likeITEMS').css('top',0); 	   
       jQuery('#likeITEMS').css('right',0);
       jQuery('#likeITEMS').html('<br><center>Loading... Pls wait!<center>');
       jQuery('#likeITEMS').effect('slide',{direction:'right'},'slow',function(){ajax2('<?php echo HTTP_PATH . "gallery/getSummary/$cam_id/$email.html"?>','likeITEMS');});
	   ;
	 }
   
   function isComplete()
    {
	 var list =$('.ptitle');
	 var isThereAnError = false;
	
	 for(i=0;i<list.length;i++)
	   {
	 	 if($('#'+list[i].id).attr('alt')==''){list[i].style.color='red';isThereAnError=true; }
	   }
	 var iLikesCurRules2 = iLikesCurRules.replace('likes',curVotes);
	 iLikesCurRules2 = iLikesCurRules2.replace('likes',curVotes);
	 iLikesCurRules2 = iLikesCurRules2.replace('AND','&&');
	 iLikesCurRules2 = iLikesCurRules2.replace('or','||');
 
	   if(isThereAnError) 
	      jAlert('Please Vote on all items width red title');
	   else if(eval(iLikesCurRules2)==false)
	      jAlert('Rules did not meet! <br/>you  likes:' + curVotes +  "<br/>you must Vote "+ iLikesCurRules);
	   else
	     return true; 
	}
	HTTP_PATH = '<?php echo HTTP_PATH?>';

function ajax2(file,form)
{
	var xmlhttp2;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp2=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp2.onreadystatechange=function()
	  {
	  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
		{
		 	document.getElementById(form).innerHTML=xmlhttp2.responseText;
			getCurVote("<?php echo HTTP_PATH . "gallery/getCurVote/$curLevelID/$CID/$VID.html" ?>");
		}
	  }
	xmlhttp2.open("GET",file,true);
	xmlhttp2.send();
}	
</script>
<script src="<?php echo HTTP_PATH?>js/bootstrap.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script src="<?php echo HTTP_PATH?>js/jquery.effects.core.js" type="text/javascript"></script>
<script src="<?php echo HTTP_PATH?>js/jquery.effects.slide.js" type="text/javascript"></script>
<script src="<?php echo HTTP_PATH?>js/ajax.js"></script>
<link rel="stylesheet" href="<?php echo HTTP_PATH ?>css/jquery.alert.css">
<script src="<?php echo HTTP_PATH ?>js/jquery.alert.js"></script>
<script> vote(-1,'none')</script>
</html>
