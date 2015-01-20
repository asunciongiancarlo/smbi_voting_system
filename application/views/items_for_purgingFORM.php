<form name='itemFORM' method='POST' action='<?php echo HTTP_PATH ."itemDatabase/items_for_purgingFORM/update/$id" ?>'> 
 <div style='background-color:white;'>
        <div class="container form">
			<?php
			$CI =& get_instance();
			$CI->load->library('forms');
			//print_r($item);
			if(isset($item[0])){
				extract($item[0]);
			}	
			?>
			
			<div class="container">
			<div id="tabs" style="font-size:14px;width:96%;"> <!-- margin-left:-75px; -->
				  <ul>
					<li><a href="#tabs-1" style="height: 8px;padding-top: 14px;"> ITEM DESCRIPTION				</a></li>
					<?php 
					if($VENDORS_REVIEW){ ?>
					<li><a href="#tabs-2" style="height: 8px;padding-top: 14px;"> VENDORS INFORMATION 			</a></li>
					<?php } ?>
				  </ul>
				  <div id="tabs-1" style="padding-left:50px;">
					<!-- ITEM FORM -->
						<div id="item_description" style="display:block;margin-left:-25px;">
						
						<?php
						if($released_Date=='0000-00-00') $released_Date = 'Not yet release';
						echo "<label class='itemCode'>
								<strong>PRODUCT CODE: </strong> $itemCode <br/> 
								<strong>DATE:  		  </strong>(UPLOADED: $uploaded_Date /RELEASED: $released_Date)<br/> 
								<strong>CONTACT PERSON:  </strong> $fname<br/> 
							</label>";
						?>
						<div class="container form">
						 <div style="margin:-40px 20px">
						  <br/><br/>	  
						  <table width="100%" cellpadding="0" cellspacing="0" border="0" >
							 <td rowspan="10" width="50%" valign='top'>
							  <center>
								<?php
								
								if(isset($itemID))
								{
									$item_img = isset($items_images[0]['image']) ? $items_images[0]['image'] : 'blank.png';
									echo" <div style='margin:5px'> 
										<a href='JavaScript:imgs(-1)'><img style='float:left'   src='".HTTP_PATH."img/left.png'></a> 
										 <a href='JavaScript:zoom($itemID)'><img style='margin:0 auto' src='".HTTP_PATH."img/zoom.png'></a> 
										<a style='' href='JavaScript:imgs(1)'><img style='float:right' src='".HTTP_PATH."img/right.png'></a>
									</div> <hr>";
									echo "<div class='targetarea' style='position:relative;width: 100%;min-height:350px;overflow:hidden'>
											<a href='#'><img   id='zoom'  data-zoom-image='".HTTP_PATH."img/big/$item_img' src='".HTTP_PATH."img/small/$item_img'></a>
										   </div>";
										  
									echo "<hr style='width:450px;clear:both'>";
									
									
									echo "<div class='multizoom1 thumbs' id='imageBar' style='width:450px;'>";

											$i=0; $j=0;
											echo "<label style='margin-top:15px' class='fl' onclick='adjustLeft()'> <img src='".HTTP_PATH."img/left.png'> &nbsp;</label>";
											echo "<div class='fl sliderBox'>";
											
											echo "<div id='imageBox'>";
												echo "<ul id='imageSlider'  style='position:absolute;list-style:none;left:0'>";
													$imgTot = count($items_images);
													
														$border_color = ''; $imgIDs=array();
														foreach($items_images as $i=>$im)
														{
															extract($im);
															$imgIDS[] =$id;
															//SET GREEN BORDER IF DEFAULT
															if($defaultStatus == 1) $primaryIcon = 'check_icon.png';
															else $primaryIcon = 'check_icon_black.png';
															
															//IMAGE HIDDEN FIELD
															$imgT =  HTTP_PATH."img/thumb/$image";
															$imgS =  HTTP_PATH."img/small/$image";
															$imgB =  HTTP_PATH."img/big/$image";
															if(isset($duplicate))
															echo "<input type='hidden' name='images[]' value='$image'>";
														   
															  echo "<li class='fl' >
																		<div id='tf$id' style='margin:5px;margin-top: -2px;'>
																			 
																			<br/>
																			<a href='#' onclick='chImg($i)' id='imgs$id'  data-image='$imgS'  data-zoom-image='$imgB'><img   src='$imgT' alt='' style='border:3px solid gray;height:35px'/></a> <br>
																		
																		</div>
																	</li>";
															$border_color='';
														}
													echo "</ul>";
												echo "</div>";
										echo "</div>";
										echo "<label class='fl' style='margin-top:15px' onclick='adjustRight()'> &nbsp; <img src='".HTTP_PATH."img/right.png'> </label>";
									echo "</div>";
									
								}else {
									echo "<img src='".HTTP_PATH."img/small/blank.png' width='460' height='460' style='border: 1px solid #cccccc;'>";
								}

								echo "</div>";
								?>
							</center>
							</td>
							<td style="text-align:left;font-size: 14px;">
				
							 <div id="accordion" style="width:80%;">
								<h3 style='height: 11px;color:#710002;'><b>ITEM INFORMATION</b></h3>
								  <div>
									<?php
									//print_r($item[0]);
									if(isset($item[0])){
									
										$CI2 =& get_instance();
										$CI2->load->library('fv');
										
										echo "<strong>Views </strong>: $tot_views <br/>";
										
										echo "<strong> ". $CI2->fv->label(10) ." </strong>: $countryName <br/>";
										//ITEM NAME
										if($CI2->fv->fieldChecker($POSMStatusID,12)=='y')
											echo "<strong> ". $CI2->fv->label(11) ." </strong>: $itemName <br/>";
										
										//SHORT DESCRIPTION
										if($CI2->fv->fieldChecker($POSMStatusID,13)=='y')
											echo "<strong> ". $CI2->fv->label(12) ." </strong>:<label style='width:300px;'>$Short_Description </label>";
										
										//POSM TYPE/OUTLET STATUS
										if($CI2->fv->fieldChecker($POSMStatusID,5)=='y')
											echo "<strong> ". $CI2->fv->label(5) ." </strong>: $POSMStatusName <br/>";
										
										//ITEM TYPE
										if($CI2->fv->fieldChecker($POSMStatusID,5)=='y')
											echo "<strong> ". $CI2->fv->label(4) ." </strong>: $typeName <br/>";
										
										//OUTLET STATUS
										if($CI2->fv->fieldChecker($POSMStatusID,6)=='y' AND $OutletStatusName!="")	
											echo "<strong> ". $CI2->fv->label(6) ." </strong>: $OutletStatusName <br/>";
										
										//PREMIUM TYPE
										if($CI2->fv->fieldChecker($POSMStatusID,7)=='y' AND $premiumTypeName!="")
											echo "<strong> ". $CI2->fv->label(7) ." </strong>: $premiumTypeName <br/>";
										
										//MATERIAL
										if($CI2->fv->fieldChecker($POSMStatusID,10)=='y')
											echo "<strong> ". $CI2->fv->label(9) ." </strong>: $materialName <br/>";
										
										
										//BRAND
										if($CI2->fv->fieldChecker($POSMStatusID,4)=='y')
											echo "<strong> ". $CI2->fv->label(3) ." </strong>: $brandName <br/>";
											
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
										
										
										//UNIT PRICE
										if($CI2->fv->fieldChecker($POSMStatusID,14)=='y' OR
										   $CI2->fv->fieldChecker($POSMStatusID,28)=='y' OR
										   $CI2->fv->fieldChecker($POSMStatusID,29)=='y' OR
										   $CI2->fv->fieldChecker($POSMStatusID,14)=='y')
										   echo "<hr class='borderLine'>";
										
										if($CI2->fv->fieldChecker($POSMStatusID,14)=='y')
											echo "<strong> ". $CI2->fv->label(13) ." </strong>: $UnitPrice <br/>";
											
										//USD PRICE
										if($CI2->fv->fieldChecker($POSMStatusID,28)=='y')
											echo "<strong> ". $CI2->fv->label(79) ." </strong>: $USD_Price <br/>";
										
										//Estimated lead time
										if($CI2->fv->fieldChecker($POSMStatusID,29)=='y')
											echo "<strong> ". $CI2->fv->label(80) ." </strong>: $estimated_production_lead_time <br/>";
											
										//PRICE AVAILABILITY
										if($CI2->fv->fieldChecker($POSMStatusID,30)=='y')
											echo "<strong> ". $CI2->fv->label(81) ." </strong>: $price_validity <br/>";
										
									}else{
										echo "<strong style='margin-top:300px;'> Sorry, There is no information on this item. </strong>";
									}
								?>
									
								  </div>
							
							 </td>
							 </table>
							 
						  <br/><br/>
						 </div>
							
							 
						</div>
						</div>
					<!-- ITEM FORM -->
				  </div>
				  
				  <?php if($VENDORS_REVIEW){ ?>
				  <div id="tabs-2" style="height:530px;padding-left:50px;padding-top:10px;overflow-y:scroll;">
				  
				  <?php
						echo "<div style='font-size:12px'>";
								
							//MOQ
							if($CI2->fv->fieldChecker($POSMStatusID,15)=='y')
								echo "<strong> ". $CI2->fv->label(14) ." </strong>: $MOQ <br/>";
								
							//UOM
							if($CI2->fv->fieldChecker($POSMStatusID,16)=='y')
								echo "<strong> ". $CI2->fv->label(15) ." </strong>: $UOM <br/>";
								
							//Long_Description
							if($CI2->fv->fieldChecker($POSMStatusID,2)=='y')
								echo "<strong> ". $CI2->fv->label(1) ." </strong>: <label style='width:300px;'>$Long_Description </label>";
								
							//Country of Origin
							if($CI2->fv->fieldChecker($POSMStatusID,27)=='y')
								echo "<strong> ". $CI2->fv->label(77) ." </strong>: $country_name <br/>";
								
						echo "</div>";
						
						echo "<strong style='margin-top:5px;'> VENDORS: </strong>";
						if(!$vendors){
							echo "<br/><label style='margin-top:5px;'> Sorry, no vendor information. </label>";
						}
						
						//VENDORS
						$i=1;
						$j=1;	
						foreach($vendors as $d){
						extract($d);
					?>
				
					<div class="drop-down" style="background:#bb2123;height:28px;">
							<h2 style='width:10px;'>
								<b><label style="margin:0 0 0 0;color:white;float:left;width:600px;"><?php echo $company_name ?> </label></b>
							</h2>
							<img onclick="showCompany('<?php echo "info_company".$i++ ?>')" src="<?php echo HTTP_PATH ?>/img/arrow-down.jpg"  width="21" height="15" style="margin-top:6px;cursor:pointer;float:right;" />
					</div>
					<?php
						$display = "display:none;";
						if($j==1 OR $j==2 OR $j==3)
							$display = "display:block;"
					?>
					<div class="info-company" style="<?php echo $display ?>;font-size:12px;background-color: rgb(240, 248, 255);" id='info_company<?php echo $j++ ?>'>
						<table border="0" style='font-size:10px;'>
							<tr>
								<td style="width: 470px;" align="left">
									<span class="info-right">
										<h2 align="left">COMPANY NAME </h2><br/><br/>
										<p align="left"> <?php echo $company_name ?> </p>
									</span>
								</td>
								<td style="width: 470px;" align="left">
									<span class="info-right">
										<h2 align="left">CONTACT PERSON</h2><br/><br/>
										<p align="left"> <?php echo $fname ." ". $mname ." ".  $lname  ?> </p>
									</span>
								</td>
							</tr>
							<tr>
								<td style="width: 470px;" align="left">
									<span class="info-right">
										<h2 align="left">BILLING ADDRESS </h2><br/><br/>
										<p align="left"> <?php echo $billing_address ?> </p>
									</span>
								</td>
								<td style="width: 470px;" align="left">
									<span class="info-right">
										<h2 align="left">TELEPHONE </h2><br/><br/>
										<p align="left"> <?php echo $telephone ?> </p>
									</span>
								</td>							</tr>
							<tr>
								<td style="width: 470px;" align="left">
									<span class="info-right">
										<h2 align="left">EMAIL ADDRESS </h2><br/><br/>
										<p align="left"> <?php echo $telephone ?>  </p>
									</span>
								</td>
								<td style="width: 470px;" align="left">
									<span class="info-right">
										<h2 align="left">COUNTRY </h2><br/><br/>
										<p align="left"> <?php echo $countryName ?> </p>
									</span>
								</td>
							</tr>
							<tr>
								<td style="width: 470px;" align="left">
									<span class="info-right">
										<h2 align="left">POSTAL CODE </h2><br/><br/>
										<p align="left"> <?php echo $postal_code ?>  </p>
									</span>
								</td>
								<td style="width: 400px;" align="left">
									<span class="info-right">
										<h2 align="left">CITY CODE </h2><br/><br/>
										<p align="left"> <?php echo $city_state ?> </p>
									</span>
								</td>
							</tr>
						</table>
					</div>
					
					<?php } ?>
				  
				  </div>
				  <?php } ?>
			</div>

			</div>
           
  </div>

 </div>
	<?php
    if($RESTORE) echo $CI->forms->buttons('restore_to_item_db','itemFORM');
	if($DELETE) echo $CI->forms->buttons('permanent_delete','itemFORM');
	?>
</form>	
<style>
.content-gal-common{
height: 760px!important;
}
#mask2 {
	position: absolute;
	left: 0;
	top: 0;
	/*z-index: 90000;*/
	background-color:black;
	display: none;
}

.popupBox {
	
	min-height: 900px;
	position: absolute;
	width:90%;
	margin-left:-12px;
	top: 0;
	left:9%;
	display: none;
	z-index: 99999;
	padding: 20px; 
}

strong{
	color:#710002;
}
 
</style>	
	
   		     <div class="popupBox" style='opacity:1;' id="dialog" >
			 <div class='close2' style='color:white;font-size:20px;margin-right:50px;padding:0;cursor:pointer'> <img src="<?php echo HTTP_PATH ."img/close2.png"?>" style="width:90px;">  </div>
			 <div style='clear:both'></div>
				<iframe src='' style='height:800px; border:0; width:90% ;margin:0 auto' id='popUpFrame'> </iframe>
			 </div>	
			 <div id="mask2"> </div>
			
	
	 
	
	<script>
	function sendComment()
	{	
		if(document.getElementById('message').value == '' || document.getElementById('message').value == ' ')
		{
			jAlert('Cannot send inquiry with blank message', 'Warning');
		}else{
			document.getElementById('loading').style.display = 'block';
			$.ajax({
			type: "post",
			url:  "<?php echo HTTP_PATH .'itemDatabase/iTem_inquiry' ?>",
			data: $('form').serialize(),
			success: function () {
			  document.getElementById('loading').style.display = 'none';
			  document.getElementById('message').value = '';
			  jAlert('Comment has been sent!', 'Message');
			}
		  });
		}	  
	}

	
	
	$(function() {
    $( "#accordion" ).accordion({
      heightStyle: "content"
    });
  });
	
	/*VENORS*/
	function showCompany(info_company)
	{
		var x = document.getElementById(info_company);		
		if(x.style.display == "none")
		{
			x.style.display = "block";
		}else
		{
			x.style.display = "none";
		}
	}
	/*VENORS*/
	
	
	 function zoom(id)
	 { 
		 var img  =  $('#zoom').attr('src');
		 img  =img.substring(img.lastIndexOf('/')+1,img.length);
		
		var formData = {itemID:id,imgSrc:img};
		$.ajax({
		  url : "<?php echo HTTP_PATH ?>gallery/itemzoom/",
		  type: "POST",
		  data : formData,
		  success: function(data, textStatus, jqXHR){
			document.getElementById('popUpFrame').src = "<?php echo HTTP_PATH ?>gallery/itemzoom/";
		  },error: function(jqXHR, textStatus, errorThrown)
		  {
            alert("failed");
		  }});
		 
		 var maskHeight = $(document).height();
		 var maskWidth = $(window).width();
		 $('#mask2').css({'width':maskWidth,'height':maskHeight});	
		 $('#mask2').fadeIn(1500);	
		 $('#mask2').fadeTo("slow",0.8);
		 var winH = $(window).height();
		 var winW = $(window).width();
		 $('#dialog').css('top',50);
		 /*$('#dialog').css('left', winW/2-$('#dialog').width()/2);*/
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
	
	
	var adjusVal = 0;
	var curImgIndex = 0;
	var TotIMG = <?php echo count($imgIDS) ?>;
	
	var imgIDS = new Array(<?php   foreach($imgIDS as $k=> $id) { if($k<count($imgIDS)-1) echo "$id,"; else echo "$id" ; }?>);
	 
	var SliderWidth = $('#imageSlider li').length ;
	SliderWidth = SliderWidth * 60;
	var cl = 0  ;
	 var imgSmallWidht = $('#zoom').css('width');
	 var imgSmallheight = $('#zoom').css('height');
	
	  if(imgSmallheight > imgSmallWidht) $('#zoom').css('height',400);
	  if(imgSmallheight < imgSmallWidht) $('#zoom').css('width',450);
	function imgs(index)
	  {
	    if((index+curImgIndex) < TotIMG && (index+curImgIndex) >=0)
		  {  
		    if((curImgIndex +1) % 5 == 0 && index==1 )
			 { cl = cl-300;$('#imageSlider').animate({left:cl},500);}
			if((curImgIndex +1) % 5 == 0 && index==-1  )
			 {cl = cl+300;$('#imageSlider').animate({left:cl},500);}
			 
			$('#imgs'+imgIDS[curImgIndex] + ' img').css('border','3px solid gray');
			curImgIndex = index+curImgIndex;
		    $('#imgs'+imgIDS[curImgIndex] + ' img').css('border','3px solid red');
		    $('#imgs'+imgIDS[curImgIndex]).trigger('click');
			var lpos = $('#imageSlider').offset();
		    var lpos = lpos.left;
		  
			
			
		   //alert(imgIDS[curImgIndex]);
		  }
		 
		 
	  }
	  
	 function chImg(i)
	  {
	   $('#imgs'+imgIDS[curImgIndex] + ' img').css('border','3px solid gray');
	   curImgIndex = i;
	   $('#imgs'+imgIDS[curImgIndex] + ' img').css('border','3px solid red');
	  }
	function adjustLeft()
	{	
		var ctr=0;
		var lpos = $('#imageSlider').offset();
		var lpos = lpos.left;
		//alert(SliderWidth); 
		if(cl-300>(SliderWidth*-1)) {cl = cl-300; $('#imageSlider').animate({left:cl},500); }
		
	}
	
	function adjustRight()
	{	
		var lpos = $('#imageSlider').offset();
		var lpos = lpos.left;
		if(cl+300<=0) {cl = cl+300; $('#imageSlider').animate({left:cl},500); }
	
	}
	
	
	 
</script>
	
	
	