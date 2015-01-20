
 <div class="content">
		
    	<div class="title-content">
        	<h2>Division Marketing Items </h2>
        </div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container">
			
			<form name="alter_iLike_Result" method="POST" action='<?php echo HTTP_PATH ."iLikeCampaign/save_new_div_items" ?>'>
			<h2 class="fl resultLabel">Filtered Items</h2>
			<h2 class="fr resultLabel2">Division Marketing Items (*Not yet won)</h2>
			<div class="clear"></div>
			
			<div id="iLikeResults" class="fl">
			<?php	
			//print_r($_SESSION['iLike_items']);
			$ctr=0;
			foreach($_SESSION['Division_Marketing_items'] as $d){
				extract($d);
				$img_path = HTTP_PATH."img/small/$item_image";
				$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$itemID";
				
				$w = w($item_image);
				$del_shortCut="<img onclick=\"delete_item($itemID)\" src='".HTTP_PATH."img/delete-item.png' style='margin-left:8px;cursor:pointer;'>";
				$title_css="";
				$ctr++;
				echo "<div style='width:120px;height:173px;margin: 10px 5px 24px 10px;;background:white;' class='fl'>
					<p style='font-size:12px;text-align:center;padding-bottom:3px;margin-bottom:-1px;background-color:#757575;color:white;'> 
						<b style='color:#330404;'>$ctr. </b><b>  $POSM_TypeName </b>  $del_shortCut
				   </p>
				   <p style='font-size:12px;text-align:center;padding-bottom:3px;margin-bottom:-1px;background-color:#999999;color:white;'> 
					$extra_label 
				   </p>
				   <input type='hidden' name='items[]' value='$itemID'>
					<div style='border: 1px solid #ccc;text-align:center;padding:5px;height:110px;overflow: hidden;background-color: white;'>
						<a href='$itemPreviewLink' target='_blank' class='itemLink'>
							<table>
							 <tr>
								<td class='gal-Icon-Container'><img class='gal-Icon-Img' src='$img_path' style='width:100%;margin-top:-65px;'></td>
							 </tr>	
							</table> 
						</a>
					</div>
					<p style='font-size:10px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;background:white;color: #555;margin-bottom:10px;height: 14px;' label='$itemName'> 
						<b style='padding-right:10px;'> ". cutStr($itemName) ." </b>  
					</p>
					 <br/>
				 </div>";
			}
			if(!$_SESSION['Division_Marketing_items']) echo "No items selected.";
			?>	
			</div>
			
			<div id="iLikeAllItems" class="fl">
			<?php
			$ctr=0;
			foreach($rep as $d){
				extract($d);
				$img_path = HTTP_PATH."img/small/$item_image";
				$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$itemID";
				$w = w($item_image);
			$ctr++;	
			echo "<div style='width:120px;height:173px;margin: 10px 5px 24px 10px;;background:white;' class='fl'>
					<p style='font-size:12px;text-align:center;padding-bottom:3px;margin-bottom:-1px;background-color:#757575;color:white;'> 
						<b style='color:#330404;'>$ctr. </b><b>  ".substr($POSM_TypeName,0,-5)." </b>  <img onclick='addItem($itemID)' src='".HTTP_PATH."img/add-item.png' style='margin-left:21px;cursor:pointer;'>
				   </p>
				   <p style='font-size:12px;text-align:center;padding-bottom:3px;margin-bottom:-1px;background-color:#999999;color:white;'> 
					$extra_label 
				   </p>
				   <input type='hidden' name='items[]' value='$itemID'>
					<div style='border: 1px solid #ccc;text-align:center;padding:5px;height:110px;overflow: hidden;background-color: white;'>
						<a href='$itemPreviewLink' target='_blank' class='itemLink'>
							<table>
							 <tr>
								<td class='gal-Icon-Container'><img class='gal-Icon-Img' src='$img_path' style='width:100%;margin-top:-65px;'></td>
							 </tr>	
							</table> 
						</a>
					</div>
					<p style='font-size:10px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;background:white;color: #555;margin-bottom:10px;height: 14px;' label='$itemName'> 
						<b style='padding-right:10px;'> ". cutStr($itemName) ." </b>  
					</p>
					 <br/>
				 </div>";	
			}
			?>	
			</div>
			
			<?php			
			$CI =& get_instance();
			$CI->load->library('forms');
			
			if($ALTER_RESULTS){
			echo "<div style='margin-left:20px'>";
				echo $CI->forms->buttons('save','SMBi'); 
				echo "<p style='clear: both;color:gray;margin-top:20px;font-size:12px;width:300px;margin-left:5px;'> 
						*Save button will save all changes.</p>";
			echo "</div>";
			}	
			?>
			</form>
           </div>
		   
        <div class="clear"></div>
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
	
	function cutStr($itemName=''){
		if(strlen($itemName)>=20)
			return substr($itemName,0,20)."..";
		else	
			return $itemName; 
	}
?>
	
<script>
function addItem(iID)
{
	jConfirm("Add this item to Filtered Items?","Alert",function(r){
		 
		 var add_item = $.ajax({
			url: "<?php echo HTTP_PATH . "iLikeCampaign/add_div_item/" ?>"+ iID,
			async: false
		}).responseText;
		 
		if(add_item=='ok'){
			 var a = $.ajax({
				url: '<?php echo HTTP_PATH ?>iLikeCampaign/view_div_items/',
				async: false
			}).responseText;
	
			document.getElementById('iLikeResults').innerHTML = a;
		}else{
			jAlert('Sorry item already exist. Please choose another item.', 'Alert Dialog');
		}
		//alert(iID);
	});
}

function delete_item(iID)
{
	jConfirm("Remove this item from Filtered Items?","Alert",function(r){
		 
		 var delete_item = $.ajax({
			url: "<?php echo HTTP_PATH . "iLikeCampaign/delete_div_item/" ?>"+ iID ,
			async: false
		}).responseText;
		
		if(delete_item=='ok'){
			 var a = $.ajax({
				url: '<?php echo HTTP_PATH ?>iLikeCampaign/view_div_items/',
				async: false
			}).responseText;
	
			document.getElementById('iLikeResults').innerHTML = a;
		}
	});
}
</script>
