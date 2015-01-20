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

.itemsSummaryLabel
{
	display:block;
	padding:50px;
}

</style>
<![endif]-->
</head>

<body  data-spy="scroll" data-target=".bs-docs-sidebar" data-twttr-rendered="true">

<div class="container">
	<header style="width:100%;">
    	<div class="logo">
       		<img src="<?php echo HTTP_PATH?>img/logo.png" width="420" height="45">
        </div>
		<div class="logo" style='float:right;padding-top:20px;font-size:18px;color:white' >
       		Welcome: <b><?php echo $voterINFO[0]['fname'] .' ' . $voterINFO[0]['lname'] ?></b> 
        </div>
        <div class="clear"></div>
	</header>
	<div class="content-gal-common ilkeheader" style="position:relative">
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
<script>
   var iLikesCurRules ='<?php echo $curLevelCondition ?>';
   var df ='';
   var curVotes =<?php echo isset($curVotes)? $curVotes:0?>;
	
   function next()
	{
	 if(isComplete()) {
	 
	 jConfirm("You've completed voting for <?php echo $posmTypeList[$curIndexPOSM]['POSM_TypeName'] . " " .  $priceRange[$keyOfActive]['extra_label']?><br/>Please click Ok to proceed.","Message Confirmation",
		  function(r)
			{
			  if(r)window.location.href ='<?php echo isset($frwURL) ? $frwURL:"" ?>';
			});
	 }
	}
	
	function back()
	{
	 window.location.href ='<?php echo $prevLvlpath ?>';
	}
		
  function finish()
	   {
	   if(isComplete()) window.location.href ='<?php echo isset($finishURL) ? $finishURL:"" ?>'; 
	   }
	   
	 function submit()
	   {
	    jConfirm("Are you sure you want to submit your votes for iWant Campaign?<br/>Click Ok to proceed.","Message Confirmation",function(r) {if(r)goSubmit()});
	   
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
	ajax2("<?php echo HTTP_PATH . "gallery/vote/$cam_id/$email/" ?>"+id +"/"+vote+"",'totalVote');
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
       jQuery('#likeITEMS').effect('slide',{direction:'right'},'slow',function(){ajax2('<?php echo HTTP_PATH . "gallery/getSummary/$cam_id/$email"?>','likeITEMS');});
	   ;
	 }
   
   function isComplete()
    {
	 var list =$('.ptitle');
	 var isThereAnError = false;
	
	 for(i=0;i<list.length;i++)
	   {
	 	 if($('#'+list[i].id).attr('alt')==''){list[i].style.background='#d41819';isThereAnError=true; }
	   }
    
     var a = $.ajax({
		url: "<?php echo HTTP_PATH . "gallery/getCurVote/$curLevelID/$CID/$VID" ?>",
		async: false
	}).responseText;	 
	curVotes = a;
	
	 
	 var iLikesCurRules2 = iLikesCurRules.replace('likes',curVotes);
	 iLikesCurRules2 = iLikesCurRules2.replace('likes',curVotes);
	 iLikesCurRules2 = iLikesCurRules2.replace('AND','&&');
	 iLikesCurRules2 = iLikesCurRules2.replace('or','||');
 
	 iLikesCurRules3 = iLikesCurRules.replace('<=',' a maximum of ');
	 iLikesCurRules3 = iLikesCurRules3.replace('likes','');
	 iLikesCurRules3 = iLikesCurRules3.replace('>=',' a minimum of  ');
	 iLikesCurRules3 = iLikesCurRules3.replace('<',' More than ');
	 iLikesCurRules3 = iLikesCurRules3.replace('>',' Less than ');
	 iLikesCurRules3 = iLikesCurRules3.replace('==',' exactly  ');
	 iLikesCurRules3 = iLikesCurRules3.replace('=',' exactly ');
	 iLikesCurRules3 = iLikesCurRules3.replace('likes','');
	 iLikesCurRules3 = iLikesCurRules3.replace('AND','and');
     
	 var min_number_of_votes_MSG = "<?php echo $min_number_of_votes_MSG; ?>";
	 var max_number_of_votes_MSG = "<?php echo $max_number_of_votes_MSG; ?>";
	 
	 min_number_of_votes_MSG = min_number_of_votes_MSG.replace('XX',curVotes);
	 max_number_of_votes_MSG = max_number_of_votes_MSG.replace('XX',curVotes);
	 
	   if(isThereAnError){ 
	      jAlert('Please vote on all the items to proceed. Highlighted in red are the items that need your vote.');
	   }else if(eval(iLikesCurRules2)==false){
		   //IF MINIMUM DOESN'T MEET
		   if(<?php echo $min_number_of_votes ?> > curVotes){
			  jAlert(min_number_of_votes_MSG,'Message Alert');
		   }else if(curVotes > <?php echo $max_number_of_votes ?>){
			  $.alerts.okButton = 'View All Items';
			  $.alerts.cancelButton  = 'View WANTED Items';
			  jConfirm(max_number_of_votes_MSG,'Message Alert',function(r){	  
				if(!r){  window.location = '<?php echo $review_url ?>'; }
				<?php if($review_url_stat=='reload'){ echo "else{ window.location = '$review_url_no_extension' }"; } ?>
			   });
			   //RESET MESSAGE
			   $.alerts.okButton 	  = 'Ok';
			   $.alerts.cancelButton  = 'Cancel';
		   }else{
			  jAlert('<br/>Sorry you were not able to meet the rules. You have wanted  '+ curVotes  + ' items. You need to want ' + iLikesCurRules3  + ' items.','Alert');
		   }
	   }else{
	     return true; 
	   }
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
			//getCurVote("<?php echo HTTP_PATH . "gallery/getCurVote/$curLevelID/$CID/$VID" ?>");
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
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/jquery.alert.css">
<script src="<?php echo HTTP_PATH ?>js/jquery.alert.js"></script>
<script> 
vote(-1,'none');
<?php
  if($review!=true) 
   {
 ?>
     //CHECK IF NEEDS TO RELOAD
	 var list =$('.ptitle');
	 var voteCtr = 0;
	 for(i=0;i<list.length;i++)
     {
	 if($('#'+list[i].id).attr('alt')!='') voteCtr++;
     }
     if(voteCtr<list.length) jAlert('<?php echo " You are voting ". $posmTypeList[$curIndexPOSM]['POSM_TypeName'] . " ".$priceRange[$keyOfActive]['extra_label']."<br/>Please vote on all the items to proceed."   ?>','Current Window');
	 
<?php
  }
 ?> 
</script>
<style>
#popup_cancel, #popup_ok {
width: 150px;
}
</style>
</body>

</html>
