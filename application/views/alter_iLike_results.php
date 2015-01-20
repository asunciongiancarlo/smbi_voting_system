
 <div class="content">
		
    	<div class="title-content">
        	<h2>ALTER <span style="text-transform:lowercase;">i</span>LIKE RESULTS</span> </h2>
        </div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container">
			<table cellpadding="0" cellspacing="0" style="width:100%;margin: 0px auto;">
				<br/>
			   <tr style="border-radius: 6px;margin-top:10px;">
					<th style="width:200px;text-align:center;">  DATE ADDED 	</th> 
					<th style="width:600px;text-align:center;">  CAMPAIGN NAME 	</th> 
					<th style="width:200px;text-align:center;">  CREATED BY  	</th> 
					<th style="width:200px;text-align:center;">  FROM 			</th> 
					<th style="width:200px;text-align:center;">  TO				</th> 
					<th style="width:200px;text-align:center;">  STATUS 		</th> 
					<th style="width:315px;text-align:center;">  REMARKS 		</th> 
				</tr>
				<tr>
				  <td><?php echo $repHeader[0]['DateAdded']?> </td>
				  <td><?php echo $repHeader[0]['campaignName']?> </td>
				  <td><?php echo $repHeader[0]['full_name']?> </td>
				  <td><?php echo $repHeader[0]['DateFrom']?> </td>
				  <td><?php echo $repHeader[0]['DateTo']?> </td>
				  <td><?php echo $repHeader[0]['status'] == "on progress" ? "in progress" : $repHeader[0]['status'] ?> </td>
				  <td><?php echo $repHeader[0]['remarks']?> </td>
				</tr>
			</table>
			
			<form name="alter_iLike_Result" method="POST" action='<?php echo HTTP_PATH ."iLikeCampaign/save_new_iLike_items/$cID" ?>'>
			<input name="campaignName" value="<?php echo $repHeader[0]['campaignName']?>" type="hidden">
			<h2 class="fl resultLabel">iLike Results</h2>
			<h2 class="fr resultLabel2">All iLike Items</h2>
			<div class="clear"></div>
			
			<div id="iLikeResults" class="fl">
			<?php	
			//print_r($_SESSION['iLike_items']);
			if($_SESSION['iLike_items']){
				foreach($_SESSION['iLike_items'] as $d){
					extract($d);
					$img_path = HTTP_PATH."img/small/$item_image";
					$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$itemID";
					
					if($EDIT)
						$edit_shortCut="<a href='".HTTP_PATH."itemDatabase/items/edit/$itemID' target='_blank'> <img src='".HTTP_PATH."img/edit-item.png' style='margin-left:8px;cursor:pointer;'></a>";
						
					$w = w($item_image);
					$del_shortCut="<img onclick=\"delete_item($itemID,$cID,'$alter')\" src='".HTTP_PATH."img/delete-item.png' style='margin-left:8px;cursor:pointer;'>";
					$title_css="";
					if($alter=="no"){
						$color = "#4E4545";
					}else{
						$title_css="style='margin-left: 23px;'";
						$color = "#999999";
					}
				echo "
					  <div style='width:120px;height:180px;margin: 10px 5px 20px 10px;;background:white;' class='fl'>
						<p style='font-size:12px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;margin-bottom:-1px;background-color:$color;color:white;'> 
							<b $title_css> ". $POSM_TypeName ."</b> $del_shortCut
					   </p>
					   <p style='font-size:12px;text-align:center;border: 1px solid #ccc;margin-bottom:-1px;background-color:#bbbbbb;color:white;'> 
						<label style='margin-left:0px;font-size:11px;'> $extra_label </label> 
					  </p>
					   <input type='hidden' name='items[]' value='$itemID'>
						<div style='border: 1px solid #ccc;text-align:center;padding:5px;height:100px;overflow: hidden;'>
							<a href='$itemPreviewLink' target='_blank' class='itemLink'>
								<table>
								 <tr>
									<td class='gal-Icon-Container'><img class='gal-Icon-Img' src='$img_path' style='$w;margin-top:-65px;'></td>
								 </tr>	
								</table> 
							</a>
						</div>
						<p style='font-size:10px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;background:white;color: #555;margin-bottom:10px;' label='$itemName'> 
							<b> ". $itemName ."</b><br/>
							<b>Likes: $voteTot</b> $edit_shortCut
						</p>
						 <br/>
					 </div>";
				}
			}else{
				echo "No iLike Results";
			}
			?>	
			</div>
			
			<div id="iLikeAllItems" class="fl">
			<?php
		
			foreach($rep as $d){
				extract($d);
				if($EDIT)
					$edit_shortCut="<a href='".HTTP_PATH."itemDatabase/items/edit/$itemID' target='_blank'> <img src='".HTTP_PATH."img/edit-item.png' style='margin-left:8px;cursor:pointer;'></a>";
				
				$img_path = HTTP_PATH."img/small/$item_image";
				$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$itemID";
				$w = w($item_image);
			echo "
				  <div style='width:120px;height:180px;margin: 10px 5px 20px 10px;;background:white;' class='fl'>
					<p style='font-size:12px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;margin-bottom:-1px;background-color:#999999;color:white;'> 
						<b style='margin-left:18px;'> ". substr($ptype,0,-5) ."</b> <img onclick='addItem($itemID,$cID)' src='".HTTP_PATH."img/add-item.png' style='margin-left:21px;cursor:pointer;'>
				   </p>
				   <p style='font-size:12px;text-align:center;border: 1px solid #ccc;margin-bottom:-1px;background-color:#bbbbbb;color:white;'> 
						<label style='margin-left:0px;font-size:11px;'> $extra_label </label> 
				   </p>
				   <input type='hidden' name='items[]' value='$itemID'>
					<div style='border: 1px solid #ccc;text-align:center;padding:5px;height:100px;overflow: hidden;'>
						<a href='$itemPreviewLink' target='_blank' class='itemLink'>
							<table>
							 <tr>
								<td class='gal-Icon-Container'><img class='gal-Icon-Img' src='$img_path' style='$w;margin-top:-65px;'></td>
							 </tr>	
							</table> 
						</a>
					</div>
					<p style='font-size:10px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;background:white;color: #555;margin-bottom:10px;' label='$itemName'> 
						<b> ". cutStr($itemName) ."</b><br/>
						<b>Likes: $voteTot</b> $edit_shortCut
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
						*Save button will alter iLike Results.</p>";
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
		if(strlen($itemName)>=15)
			return substr($itemName,0,15)."..";
		else	
			return $itemName; 
	}
?>
	
<script>
function addItem(iID,cID)
{
	jConfirm("Add this item to iLike Item Results?","Alert",function(r){
		if(r){ 
			 var add_item = $.ajax({
				url: "<?php echo HTTP_PATH . "iLikeCampaign/add_item/" ?>"+ iID +"/"+ cID,
				async: false
			}).responseText;
			 
			if(add_item=='ok'){
				 var a = $.ajax({
					url: '<?php echo HTTP_PATH ?>iLikeCampaign/view_items/' + <?php echo $cID ?>,
					async: false
				}).responseText;
		
				document.getElementById('iLikeResults').innerHTML = a;
			}else{
				jAlert('Sorry item already exist. Please choose another item.', 'Alert Dialog');
			}
		}
		//alert(iID);
	});
}

function delete_item(iID,cID,alt)
{
	jConfirm("Remove this item from iLike Item Results?","Alert",function(r){
		if(r){  
			 var delete_item = $.ajax({
				url: "<?php echo HTTP_PATH . "iLikeCampaign/delete_item/" ?>"+ iID +"/"+cID +"/"+alt,
				async: false
			}).responseText;
			
			if(delete_item=='ok'){
				 var a = $.ajax({
					url: '<?php echo HTTP_PATH ?>iLikeCampaign/view_items/' + <?php echo $cID ?>,
					async: false
				}).responseText;
		
				document.getElementById('iLikeResults').innerHTML = a;
			}
		}
	});
}
</script>

<div id="dialog-form" title="LIST OF ITEMS" style='display:none;'>
	<div id="List_of_Items"></div>
</div>

<script>  
  function viewDialog(ctype,cID,vID)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/vote_items/'+ctype+'/'+cID+'/'+vID,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>