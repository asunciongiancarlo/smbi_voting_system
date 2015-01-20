    <div style='background-color:white;'>
        <div class="container form">
		
			<?php
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
					
					if(isset($itemID))
					{
						
						$item_img = isset($items_images[0]['image']) ? $items_images[0]['image'] : 'blank.png';
						echo" <div style='margin:5px'> 
						    <a href='JavaScript:imgs(-1)'><img style='float:left'   src='".HTTP_PATH."img/left.png'></a> 
							 <a href='JavaScript:zoom($itemID)'><img style='margin:0 auto' src='".HTTP_PATH."img/zoom.png'></a> 
                            <a style='' href='JavaScript:imgs(1)'><img style='float:right' src='".HTTP_PATH."img/right.png'></a>
							
															
					    </div> <hr >";
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
																 
																<br>
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
					
					?>
                </center>
                </td>
                <td style='text-align:left;vertical-align: top;padding-left:30px;'>
				<?php
				    //print_r($item[0]);
					if(isset($item[0])){
					
						$CI2 =& get_instance();
						$CI2->load->library('fv');
						
						//ITEM NAME
						echo "<strong> ". $CI2->fv->label(11) ." </strong>: $itemName <br/>";
						
						//SHORT DESCRIPTION
						echo "<strong> ". $CI2->fv->label(12) ." </strong>: <label style='width:300px;'>$Short_Description </label>";
						
						//LONG DESCRIPTION
						echo "<strong> ". $CI2->fv->label(1) ." </strong>: <label style='width:300px;'>$Long_Description </label>";
					
					}else{
						echo "<strong style='margin-top:300px;'> Sorry, There is no information on this item. </strong>";
					}
				?>
                
                 </td>
                 </tr>
                 </table>
					
                    <br/><br/>
			 </div>
				
				 
			</div>
			 
			 
			
			</div>
			<!-- ITEM FORM -->
		
		
			<div class="clear"></div>
			
				 
				
			</div>
           
  </div>
</div>
			
        

			
			
	
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

	top: 0;
	display: none;
	z-index: 99999;
	padding: 20px; 
}

strong{
	color:#710002;
}
 
</style>	
	
   		     <div class="popupBox" style='opacity:1;' id="dialog" >
			 <div class='close2' style='color:white;font-size:20px;margin-right:50px;float:left;padding:0;cursor:pointer'> <img src="<?php echo HTTP_PATH ."img/close2.png"?>" style="width:90px;">  </div>
			 <div style='clear:both'></div>
			 <iframe  src='' style='height:800px;border:0;width:90%;margin:0 auto' id='popUpFrame'> </iframe>
			 </div>	
			 <div id="mask2"> </div>
			
	
	 
	
	<script>
	
	 function zoom(id)
	  { 
		 var img  =  $('#zoom').attr('src');
		 img  =img.substring(img.lastIndexOf('/')+1,img.length);
		
		 //document.getElementById('popUpFrame').src = "<?php echo HTTP_PATH ?>gallery/itemZoomVoting/" +id +'/'+img;
		 
		 var formData = {itemID:id,imgSrc:img};
		 $.ajax({
		  url : "<?php echo HTTP_PATH ?>gallery/itemZoomVoting/",
		  type: "POST",
		  data : formData,
		  success: function(data, textStatus, jqXHR){
			document.getElementById('popUpFrame').src = "<?php echo HTTP_PATH ?>gallery/itemZoomVoting/";
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
	
	
	function StopPar()
	{
		document.getElementById('publishInput').value = 'n';
		$('#vendorFORM').parsley().destroy();
		//$('#vendorFORM').parsley('destroy');
	}
	
	
	
	function enlargeThumbnail(img)
	{
	
		document.getElementById('smallThum').src = "<?php echo HTTP_PATH?>img/small/" + img ;
	
	}
	
	function deleteOneImg(id,itemID)
	{
		if(confirm("Are you sure you want to delete this thumbnail?"))
		{
			
			var xmlhttp2;
			var file = '<?php echo HTTP_PATH ?>itemDatabase/deleteOneImg/'+id+'/'+itemID;
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
			 
					if(xmlhttp2.responseText==true)
						location.reload();
				}
			  }
			xmlhttp2.open("GET",file,true);
			xmlhttp2.send();
		}
	}
	
	function setDefaultImg(id,itemID)
	{
		if(confirm("Are you sure you want to set this as default Image for this product?"))
		{
			var xmlhttp2;
			var file = '<?php echo HTTP_PATH ?>itemDatabase/setDefaultImg/'+id+'/'+itemID;
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
			 
					if(xmlhttp2.responseText==true)
						location.reload();
				}
			  }
			xmlhttp2.open("GET",file,true);
			xmlhttp2.send();
		
		}
	}
	
	
	function showItemDescription(id)
	{
		var x = document.getElementById(id);
		var y = document.getElementById('item_vendors');
		
		document.getElementById('tab1').className = 'button-content2';
		document.getElementById('tab2').className = 'button-content1';
		
		if(x.style.display == "none")
		{
			x.style.display="block";
			y.style.display="none";
		}
	}
	
	function showItemVendors(id)
	{
		var x = document.getElementById(id);
		var y = document.getElementById('item_description');
        
		document.getElementById('tab2').className = 'button-content2';
		document.getElementById('tab1').className = 'button-content1';
		
		
		if(x.style.display == "none")
		{
			x.style.display="block";
			y.style.display="none";
		}
		
	}
	
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
	
	 
</script>
	
	
	