<div class='itemWrapper'  style='width:100%'>
  <div id='faq'>
     <h2 onclick="$('#helpWrapper').toggle('slow')"> <a style='text-decoration:none;color:#bb4041;' href="javascript:void(0)">Legend <span style='font-size:11px'>click here to show or hide</span></a> </h2>
	 <hr>
	 <div id='helpWrapper'>
	   <table>
	    <tr>
	     <td class="td-to-hide"> <img src="<?php echo HTTP_PATH. "img/Information-icon.png"?>"> </td>
	     <td class="td-stay"> 
		    <strong> </strong>
			<?php
			 if($review!=true) 
		      {
			?> 
			<p>
			Please vote on all the items below. You may either click 
             <input value="Like"  style='cursor: pointer;font-size: 11px;height: 22px;padding: 0;'   type="button" class="like-btn" alt="">  
			 or
			 <input value="Not Now" style='cursor: pointer;font-size: 11px;height: 22px;padding: 0; '  type="button" class="like-btn" alt="">
			</p>
			<p>
            Click the
             <a href="JavaScript:javascript:void(0)" style='padding: 3px;font-size: 10px; margin-left:0px!important' class="back-btn">Next > </a>
			button once you are done with the current page.<br>  
			</p>
			<p>
            You can click 
            <a href="JavaScript:javascript:void(0)" style='padding: 3px;font-size: 10px; margin-left:0px!important' class="back-btn">< Back </a>
			to go back to the previous page.<br>
			<?php } else { ?> 
		    Please review the items you have voted. If you wish to change some items, <br>
			you can click the “Premium Item” or “Service Item” tabs in the header to go back to the voting page.
			<?php } ?> 
			</p>
			
         </td> 		   
	    </tr>
       </table>		
	  </p>

	 </div>
	 
  </div>
 <div class="processBar" align="center">
    <?php

	  $totalPOSMTYPE = count($posmTypeList);
	  $totalPRICERANGE = count($priceRange);
      $hasbeenTag=false;
	  $isLast = false;
	  $width=(100/(count($posmTypeList) +1));
	  
	  foreach($posmTypeList as $k=>$p) 
	  { 
	      extract($p);
          if($hasbeenTag==false or $review==true) 
		    {
 			 $classBar  = "pre-item";
		     $classIcon = "spanText2";
			}
			//var_dump($_SESSION['itemReview'.$encemail]);
	     if(isset($_SESSION['itemReview'.$encemail]))  $classIcon = "spanText2";	
		 $path =isset($_SESSION['itemReview'.$encemail])? HTTP_PATH. "gallery/voting/$cam_id/$email/1/$k/0":"#";
         //var_dump($path);  	   
	   ?>
		
		<a href='<?php echo $path ?>'>
		<div class="<?php echo  $classBar ?>" style='margin-left:0;width:<?php echo $width ?>%'> 
			<div class="bar">
			   <span class="<?php echo $classIcon ?>"><span class="stat1"><?php echo $p['POSM_TypeName'] ?></span></span>
			</div>
		</div>
        </a>
	   <?php 
	      if($items[0]['POSM_TypeName']== $p['POSM_TypeName']) 
			{
			  $classBar  = "ser-item";
			  $classIcon = "spanText";
			  $hasbeenTag=true;
			  if($k == count($posmTypeList)-1) { $isLast = true;}
			}
	     } 
		   if($review==true) 
		    {
 			 $classBar  = "pre-item";
		     $classIcon = "spanText2";
			}
		   if($_SESSION['itemReview'.$encemail])  $classIcon = "spanText2";	
		 ?>
		<a href="<?php echo $_SESSION['itemReview'.$encemail]==true ?  HTTP_PATH. "gallery/review/$cam_id/$email":"#"?>"> 
		<div class="<?php echo $classBar?>" style='width:<?php echo $width ?>%'>
			<div class="bar"><span class="<?php echo $classIcon ?> fin"><span class="stat">Review</span></span></div>
		</div>
		</a>
  </div>
 <div class='itemWrapper'  style='border:1px solid #710100;width:100%'>
 
 <div class='priceRange'>
    <ul>
	<?php
	 $keyOfActive ='';// $curLvl="";
	  foreach($priceRange as $k=> $pr)
	   {
	     extract($pr);
	     if($curLevelID == $id)    $keyOfActive = $k;
	   }
	  foreach($priceRange as $k=> $pr)
	   {
	    extract($pr);
		$curLvl =  $k<= $keyOfActive  ?   "activeLevel":""; $listyle="";
		if($k==0)		$listyle =  "style='background-position-x:70px'";
		if($k == count($priceRange)-1 )  $listyle= "style='background-position-x:-180px'";
		
	    echo "<li $listyle > <div class='prLevel $curLvl prLevel$k'> <a href=''>  ".($k+1)."</a></div><span> $extra_label  </span><p style='font-size:11px'> ($level_name)  </p>  </i> </li>";

	   }

	  if(isset($priceRange[$keyOfActive+1]['id'])==false){
  	     $nextLvlpath =  HTTP_PATH. "gallery/voting/$cam_id/$email/1/".($curIndexPOSM+1)."/0";
		 if($priceRange[$keyOfActive-1]['id']==false)
		 {
		 $prevLvlpath =  HTTP_PATH. "gallery/voting/$cam_id/$email/1/".($curIndexPOSM-1)."";
		 }else{
  	     $prevLvlpath =  HTTP_PATH. "gallery/voting/$cam_id/$email/1/".($curIndexPOSM)."/".($priceRange[$keyOfActive-1]['id'])."";;  
		 }
	  }else{
	     $nextLvlpath =  HTTP_PATH. "gallery/voting/$cam_id/$email/1/".($curIndexPOSM)."/".($priceRange[$keyOfActive+1]['id'])."";
		 
		 if($priceRange[($keyOfActive-1)]['id']==false)
          $prevLvlpath =  HTTP_PATH. "gallery/voting/$cam_id/$email/1/".($curIndexPOSM-1)."/".(($priceRange[($keyOfActive)]['id'])-1)."";
		 else{
		  $prevLvlpath =  HTTP_PATH. "gallery/voting/$cam_id/$email/1/".($curIndexPOSM)."/".($priceRange[($keyOfActive-1)]['id'])."";
		 }
		 
	     
	  }	
	?>
	</ul>
	 
  </div>
  <div id="Icontainer" class="gallery-container"  style="width: 101%; margin-left: -4px;">
 
  <div class="row1" style=''>

  <?php
	//print_r($items);
	$lastItem="";
	$lastPriceRange="";
	if(is_array($items)) { 
            foreach($items as $ki=> $i) { extract($i);
			if($lastItem!=$i['POSM_TypeName'] and $review==true ) echo "<h4 style='clear:both;text-align: left;background:lightgray;color:#000;padding:10px;margin: 0 0 0 5px;width: 97%;'> $POSM_TypeName  </h4>";
			if($lastPriceRange!=$i['label'] and $review==true ) 
			  echo "<fieldset style='margin:15px auto 15px auto ;border:1px solid gray;width:97%'>  <legend STYLE='margin: 10PX;FONT-WEIGHT:BOLD'>".$i['label'] ."</legend>";
   
					 $iLike     = getVote($itemID,'yes',$currentVote,$VOTER_ID,$CAMPAIGN_ID);
					 $iDontLike = getVote($itemID,'no',$currentVote,$VOTER_ID,$CAMPAIGN_ID);
					 $alt       = getVote($itemID,'no',$currentVote,$VOTER_ID,$CAMPAIGN_ID) .  getVote($itemID,'yes',$currentVote,$VOTER_ID,$CAMPAIGN_ID);
				?>
         <div class="like-gal fl gallery-item">
		       
				<div   alt='<?php echo $alt ?>' title="<?php echo $itemName?>" class="heading   ptitle" id='itmBox<?php echo $itemID ?>'  style='text-align:center;height:50px;color:black;font-style:bold;overflow:hidden;<?php echo $iLike=='btnClick'?"background-color:#f5bc33":""?>'> 
				  <?php 
					if(strlen($itemName)<29) echo "<p style='margin-top:16px;font-weight:bold;line-height:16px;'>".$itemName."</p>";
					else 					 echo "<p style='margin-top:2px;font-weight:bold; line-height:16px;'>".$itemName."</p>";
					
					?></p>
				</div>
				 
				<div class="clear"></div>
				 <?php $w = w($itemImg); ?>
				 
				 <div class="clear" style='height:170px;text-align:center;width:210px;overflow:hidden'>
				     <table>
					 <tr>
						<td class='gal-Icon-Container'><img title="<?php echo $itemName?>" class='gal-Icon-Img' src="<?php echo HTTP_PATH?>img/galleryImg/<?php echo $itemImg ?>" style='<?php echo $w ?>;margin-top: -7px;'></td>
					 </tr>	
					</table> 
		         </div>

			     <div style='text-align:center;'>
				 
 
				 <?php if($review!=true) {?>	
			     <ul style='margin-top:5px'>
                	<li><input value="Like" id='btnl<?php echo $itemID ?>'  onclick="vote(<?php echo $itemID ?>,'yes',this);jQuery('#itmBox<?php echo $itemID ?>').css('background-color','#f5bc33');jQuery('#itmBox<?php echo $itemID ?>').css('color','#330404');jQuery('#itmBox<?php echo $itemID ?>').attr('alt','1');jQuery('#btndl<?php echo $itemID ?>').removeClass('btnClick');jQuery(this).addClass('btnClick');"   type="button" class="like-btn <?php echo $iLike ?>" alt="<?php echo $iLike ?>"></li>
                    <li><input value="Not Now" id='btndl<?php echo $itemID ?>' onclick="vote(<?php echo $itemID ?>,'no',this);jQuery('#itmBox<?php echo $itemID ?>').css('background-color','#f8edd7');jQuery('#itmBox<?php echo $itemID ?>').css('color','#330404');jQuery('#itmBox<?php echo $itemID ?>').attr('alt','-1');jQuery('#btnl<?php echo $itemID ?>').removeClass('btnClick');jQuery(this).addClass('btnClick');"    type="button" class="like-btn <?php echo $iDontLike ?> " alt="<?php echo $iDontLike ?>"></li>
                </ul>
				<?php } ?>
				<div class="clear" style='height:5px'></div>
			    </div>
			</div>
         <?php 
	 
		 if($items[$ki]['label']!=$items[$ki+1]['label'] and $review==true ) 
			  echo "</fieldset>";
		 $lastItem=$i['POSM_TypeName']; 
		 $lastPriceRange=$i['label'];
		 } 
		 
		 } ?>
		 <div class="clear"></div>
				<?php

				   if( $curIndexPOSM ==  ($totalPOSMTYPE -1) and ($keyOfActive== $totalPRICERANGE-1)) $nextLvlpath	= HTTP_PATH.    "gallery/review/$cam_id/$email"; 
				   $frwURL    = $nextLvlpath;
				  
				   $backbtn 	=  ($curIndexPOSM>0 or $keyOfActive >0) ? true:false;   
				   $submitURL = HTTP_PATH .    "gallery/finish/$cam_id/$email";
				   $reloadURL = HTTP_PATH .   "gallery/review/$cam_id/$encemail";
				?>
				
				 <?php  if($review!=true) { ?>
				   <center>
					 <span>
					   <?php
						echo "<b> ITEMS:</b>  ". ($limit  + 1). "-$total "; 
					    echo " of $curPOSMName (".$priceRange[$keyOfActive]['extra_label'].") "; 
						 
						?>
					 </span>
				   </center>
				 <?php } ?>
				 <div style='margin:0 auto;width:100%'>
					<?php 
					//echo "pageF: ".$pageF;
					// echo  "$page<=".ceil($total/20);
				 
					?>
					
					<?php if($review!=true)  { ?> <a style='float:right' href="JavaScript:next()" class="next-btn">Next<span class="next-arr"><img src="<?php echo HTTP_PATH ?>img/right-arrow.png" width="14" height="21"></span></a><?php } ?> 
					<?php if($backbtn==true) { ?> <a style='float:left' href="JavaScript:back()" class="back-btn"><span class="back-arr"><img src="<?php echo HTTP_PATH ?>img/left-arrow.png" width="14" height="21"></span>Back</a><?php } ?> 
					<?php if($review==true)  {
                                $path="JavaScript:submit()";
						         ?>  
						 <a style='float:right;<?php echo $style?>' id='submitBTN' href="<?php echo $path ?>" class="next-btn">Submit<span class="next-arr"><img src="<?php echo HTTP_PATH ?>img/right-arrow.png" width="14" height="21"></span></a>
					<?php }  ?>
					 
				</div>			 
      </div>
        <!--Filters -->
	<div>	
	 <div class="span4 right-panel" id='likeITEMS'  style='min-height:285px;position:absolute;display:none;border:1px solid gray;'>
     <div id='likeItems'></div>
     </div> 
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
	
	function getVote($id,$vote,$currentVote,$VOTER_ID,$CAMPAIGN_ID)
	  {

	    //if(is_array($currentVote)==false) {return "";exit();};
		$CI  =& get_instance();
		$sql = $CI->db->query("SELECT id FROM votexRef WHERE campaignID=$CAMPAIGN_ID AND voterID=$VOTER_ID AND itemID= $id AND vote='$vote'");
		$row = $sql->result_array(); 
		
		if($row)
			return "btnClick";
		else
			 return "";
	  }
?>
 </div>
 </div>