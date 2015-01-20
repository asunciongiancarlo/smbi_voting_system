 <div class="processBar" align="center" style="margin-bottom:10px;">
    <?php 
	 $width=(count($posmTypeList) / 100 )*100-1;
	foreach($posmTypeList as $p) { extract($p); ?>
		<div class="pre-item" style='width:<?php echo $width ?>%'> 
			<div class="bar">
			   <span class="spanText2"><span class="stat1"><?php echo $p['POSM_TypeName'] ?></span></span>
			</div>
		</div>

	   <?php } ?>
	   <!-- 
	     <div class="ser-item">
				<div class="bar"><span class="spanText"> <span class="stat">Service Item</span></span></div>
		</div>
		<div class="ser-item">
			<div class="bar"><span class="spanText"> <span class="stat">Service Item</span></span></div>
		</div>
	   
	   -->
		<div class="done-item">
			<!--<img class="stat-icon" src="<?php echo HTTP_PATH ?>img/check.png"/>-->
			<div class="bar"><span class="spanText fin"><span class="stat">Finish</span></span></div>
		</div>
  </div>
  <div id="Icontainer" class="gallery-container" style="width: 101%; margin-left: -4px;">
  <div class="row1">
  <?php
	//print_r($currentVote);
	$lastItem="" ;
	if(is_array($items)) { 
            foreach($items as $i) { extract($i);
			//if($lastItem!=$POSM_TypeName) echo "<h5 style='clear:both;text-align: left;background: #330404;margin: 0;color: #fff;padding: 10px 10px;'> $POSM_TypeName  </h5>";
  ?>
         <div class="like-gal fl gallery-item" >
				<div class="heading" style='text-align:center;height:25px;color:black;font-style:bold;'> <b><?php echo $POSM_TypeName; ?></b> </div>
				<div class="clear"></div>
				 <?php $w = w($itemImg); ?>
				 <a target='_blank' href="<?php echo HTTP_PATH ?>gallery/itemInfo/<?php echo $itemID?>.html">
				 <div class="clear" style='height:160px;text-align:center;width:210px;overflow:hidden'>
				     <table>
					 <tr>
						<td class='gal-Icon-Container'><img class='gal-Icon-Img' src="<?php echo HTTP_PATH?>img/galleryImg/<?php echo $itemImg ?>" style='<?php echo $w ?>'></td>
					 </tr>	
					</table> 
		         </div>
				 </a>
				 
			     <div style='text-align:center;'>
					<hr style='margin:2px 0'>
					<?php
					 $iLike     = getVote($itemID,'yes',$currentVote);
					 $iDontLike = getVote($itemID,'no',$currentVote);
					 $alt       = getVote($itemID,'no',$currentVote) .  getVote($itemID,'yes',$currentVote);
					?>
					<h4 class='ptitle' id='pt<?php echo $itemID ?>' alt='<?php echo  $alt ?>' title="<?php echo $itemName?>">
					<?php 
					if(strlen($itemName)>=20)
						echo substr($itemName,0,20)."...";
					else	
						echo $itemName;
					?>
					</h4>
					
			     <ul style='margin-top:5px'>
                	<li><input id='btnl<?php echo $itemID ?>'  onclick="vote(<?php echo $itemID ?>,'yes');jQuery('#pt<?php echo $itemID ?>').css('color','#330404');jQuery('#pt<?php echo $itemID ?>').attr('alt','1');jQuery('#btndl<?php echo $itemID ?>').removeClass('btnClick');jQuery(this).addClass('btnClick');"   type="button" class="like-btn-liked <?php echo $iLike ?>"></li>
                    <li><input id='btndl<?php echo $itemID ?>' onclick="vote(<?php echo $itemID ?>,'no');jQuery('#pt<?php echo $itemID ?>').css('color','#330404');jQuery('#pt<?php echo $itemID ?>').attr('alt','-1');jQuery('#btnl<?php echo $itemID ?>').removeClass('btnClick');jQuery(this).addClass('btnClick');"    type="button" class="unlike-btn-liked <?php echo $iDontLike ?> "></li>
                </ul>
				<div class="clear" style='height:5px'></div>
			  </div>
			</div>
         <?php $lastItem=$POSM_TypeName; } } ?>
		 <div class="clear"></div>
				<?php
				   //echo 'total '.$total; 	echo "<br/>";
				   //echo 'page '.$page; 		echo "<br/>";
				   $pageB =   $page-1; 
				   $pageF =   $page+1; 
				   $backURL= HTTP_PATH. "gallery/voting/$cam_id/$email/$pageB.html";
				   $frwURL= HTTP_PATH. "gallery/voting/$cam_id/$email/$pageF.html";
				   $finishURL= HTTP_PATH. "gallery/finish/$cam_id/$email.html";
				?>
				 <div style='margin:0 auto;width:100%'>
					<?php if($pageB>0) { ?> <a href="JavaScript:back()" class="back-btn" style='float:left'><span class="back-arr"><img src="<?php echo HTTP_PATH?>img/left-arrow.png" width="14" height="21"></span> Back</a> <?php } ?>
					<?php if($pageF<=ceil($total/20)) { ?> 
						 <a style='float:right' href="JavaScript:next()" class="next-btn">NEXT<span class="next-arr"><img src="<?php echo HTTP_PATH ?>img/right-arrow.png" width="14" height="21"></span></a><?php } 
					else { ?>
						  <a style='float:right' href="JavaScript:finish()" class="next-btn">Finish<span class="next-arr"><img src="<?php echo HTTP_PATH ?>img/right-arrow.png" width="14" height="21"></span></a>
					<?php } ?>
	            </div>			 
      </div>
        <!--Filters -->
     <div class="span4 right-panel" id='likeITEMS'   style='min-height:285px;position:absolute;display:none'>
        <h4>LIKED ITEMS (<a style='color:black' href="#" onclick="javascript:jQuery('#likeITEMS').hide('slide',{direction:'right'});">close</a>)</h4>
	    <div id='likeItems'></div>
     </div> 
	 
<?php 
	function w($img)
	{
		$w='';
		$HTTP_PATH = getcwd()."/img/galleryImg/$img";
		list($width, $height, $type, $attr) = getimagesize("$HTTP_PATH");
		if($width>$height)
			return $w='width:100%';
		else
			return $w;
	}  
	
	function getVote($id,$vote,$currentVote)
	  {

	    if(is_array($currentVote)==false) {return "";exit();}; 
		foreach($currentVote as $k=>$v)
		  {
		    if($v['pid']==$id and $vote==$v['vote']) 
			{ 
			  return "btnClick"; 
			  exit();
			}
		  }
		  return "";
	  }
?>
