    <div style='background-color:white;'>
        <div class="container form">
		
			<?php
			 //print_r($item[0]);
			 if(isset($item[0])){
				extract($item[0]);
			 }	
			?>
			
			 
			<div class="container" style='margin-left: -25px;'>
			
			<!-- ITEM FORM -->
			<div id="item_description" style="display:block;">
			<div class="container form">
			 <div style="margin:0 20px">
              <br/><br/>	  
              <table width="100%" cellpadding="0" cellspacing="0" border="0" >
                <tr>
                 <td rowspan="10" width="50%" valign='top'>
				 
				
                  <center>
				
					<?php
					
					if($items_images)
					{ ?>
						
						<a href='JavaScript:zoom(<?php echo $itemID ?>)'><img style='margin:0 auto' src='<?php echo HTTP_PATH ?>img/zoom.png'></a>
						<div id="galleria">
						<?php
							foreach($items_images as $i=>$im)
							{
								extract($im);
								//IMAGE HIDDEN FIELD
								$imgT =  HTTP_PATH."img/thumb/$image";
								$imgS =  HTTP_PATH."img/small/$image";
								$imgB =  HTTP_PATH."img/big/$image";
								
								echo  "<a href='$imgB'>
										<img 
											src='$imgS',
											data-big='$imgB'
											data-title='$itemName'
											data-description='$Short_Description'
										>
									</a>";
							}
						?>	
						</a>
						</div>
						
						
					<?php	
					}else {
						echo "<span style='border: 1px solid #cccccc; background: #eee url(".HTTP_PATH."img/small/blank.png) center center no-repeat; height:350px;width:400px;display: block;' ></span>";
					}
					
					?>
                </center>
                </td>
                <td style='text-align:left;' width="50%" valign='top'>
				<?php
					if(isset($item[0])){
					
						$CI2 =& get_instance();
						$CI2->load->library('fv');
						
						echo "<strong> ". $CI2->fv->label(10) ." </strong>: $countryName <br/>";
						//ITEM NAME
						if($CI2->fv->fieldChecker($POSMStatusID,12)=='y')
							echo "<strong> ". $CI2->fv->label(11) ." </strong>: $itemName <br/>";
						
						//SHORT DESCRIPTION
						if($CI2->fv->fieldChecker($POSMStatusID,13)=='y')
							echo "<strong> ". $CI2->fv->label(12) ." </strong>: <br/>$Short_Description <br/>";
						
						//POSM TYPE/OUTLET STATUS
						if($CI2->fv->fieldChecker($POSMStatusID,5)=='y')
							echo "<strong> ". $CI2->fv->label(5) ." </strong>: $POSMStatusName <br/>";
						
						
						//ITEM TYPE
						if($CI2->fv->fieldChecker($POSMStatusID,5)=='y')
							echo "<strong> ". $CI2->fv->label(4) ." </strong>: $typeName <br/>";
						
						//OUTLET STATUS
						if($CI2->fv->fieldChecker($POSMStatusID,6)=='y')	
							echo "<strong> ". $CI2->fv->label(6) ." </strong>: $OutletStatusName <br/>";
						
						//MATERIAL
						if($CI2->fv->fieldChecker($POSMStatusID,10)=='y')
							echo "<strong> ". $CI2->fv->label(9) ." </strong>: $materialName <br/>";
						
						
						//BRAND
						if($CI2->fv->fieldChecker($POSMStatusID,4)=='y')
							echo "<strong> ". $CI2->fv->label(3) ." </strong>: $brandName <br/>";
						
						//PREMIUM TYPE
						if($CI2->fv->fieldChecker($POSMStatusID,7)=='y')
							echo "<strong> ". $CI2->fv->label(7) ." </strong>: $premiumTypeName <br/>";
						
						//MOQ
						if($CI2->fv->fieldChecker($POSMStatusID,15)=='y')
							echo "<strong> ". $CI2->fv->label(14) ." </strong>: $MOQ <br/>";
							
						//UNIT PRICE
						if($CI2->fv->fieldChecker($POSMStatusID,14)=='y')
							echo "<strong> ". $CI2->fv->label(13) ." </strong>: $UnitPrice <br/>";
							
						//UOM
						if($CI2->fv->fieldChecker($POSMStatusID,16)=='y')
							echo "<strong> ". $CI2->fv->label(15) ." </strong>: $UOM <br/>";
							
						//Long_Description
						if($CI2->fv->fieldChecker($POSMStatusID,2)=='y')
							echo "<strong> ". $CI2->fv->label(1) ." </strong>: $Long_Description <br/>";
							
						//FIELD001
						if($CI2->fv->fieldChecker($POSMStatusID,17)=='y')
							echo "<strong> ". $CI2->fv->label(16) ." </strong>: $Fields0001 <br/>";
						
						//FIELD002
						if($CI2->fv->fieldChecker($POSMStatusID,18)=='y')
							echo "<strong> ". $CI2->fv->label(17) ." </strong>: $Fields0002 <br/>";
							
						//FIELD003
						if($CI2->fv->fieldChecker($POSMStatusID,19)=='y')
							echo "<strong> ". $CI2->fv->label(18) ." </strong>: $Fields0003 <br/>";
							
						//FIELD004
						if($CI2->fv->fieldChecker($POSMStatusID,20)=='y')
							echo "<strong> ". $CI2->fv->label(19) ." </strong>: $Fields0004 <br/>";
							
						//FIELD005
						if($CI2->fv->fieldChecker($POSMStatusID,21)=='y')
							echo "<strong> ". $CI2->fv->label(20) ." </strong>: $Fields0005 <br/>";
					
					}else{
						echo "<strong style='margin-top:300px;'> Sorry, There is no information on this item. </strong>";
					}
				?>
                
				</td>
                 </tr>
            
				              
                </table>
					
                  
			 </div>
				
				 
			</div>

			</div>
			<!-- itemZoom -->
			<div class="popupBox" style='opacity:1;' id="dialog" >
			 <div class='close2' style='color:white;font-size:20px;margin-right:50px;float:left;padding:0;cursor:pointer'> 
				<img src="<?php echo HTTP_PATH ."img/close2.png"?>" style="width:90px;"> 
			 </div>
			 
			 <div style='clear:both'></div>
			 <iframe  src='' style='height:664px;border:0;width:90%;margin:0 auto' id='popUpFrame'> </iframe>
			 </div>	
			 <div id="mask2"> </div>
			<!-- itemZoom -->
			
		
			</div>
           
  </div>
</div>

<script>
	<?php if(isset($items_images)){ ?>
	 
	 function zoom(id)
	 { 
		 var img  =  document.getElementById('hiddenImage').value;
		 img  = img.substring(img.lastIndexOf('/')+1,img.length);
		
		 document.getElementById('popUpFrame').src = "<?php echo HTTP_PATH ?>gallery/itemzoom/" +id +'/'+img;
		 var maskHeight = $(document).height();
		 var maskWidth = $(window).width();
		 $('#mask2').css({'width':maskWidth,'height':maskHeight});	
		 $('#mask2').fadeIn(1500);	
		 $('#mask2').fadeTo("slow",0.8);
		 var winH = $(window).height();
		 var winW = $(window).width();
		 $('#dialog').css('top',50);
		 $('#dialog').css('left', winW/2-$('#dialog').width()/2);
		 $('#dialog').fadeIn(3000);
         $('#dialog').css('opacity',1);  		 
	  }

  
	$('.close2').click(function (e) {
		e.preventDefault();
		$('#mask2').hide();
		$('#dialog').hide();
	});	
	
	//if mask is clicked
	$('#mask2').click(function () {
		$(this).hide();
		$('#dialog').hide();
	});	
	<?php } ?>
	</script>	
	

			
	
	 
	

	
	
	