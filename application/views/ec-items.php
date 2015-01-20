      <div class="content">
		
    	<div class="title-content">
        	<h2> <span style='text-transform:lowercase;'>e</span>CATALOG ITEMS</h2>
        </div>
        
        <div class="clear"></div>
	
        <div class="working_area">
		     <div class="container" style='padding:20px;'>	
			   <?php
				//MESSAGE ALERT
				print_r($msg);
				$CI =& get_instance();
				if(isset($msg)){
					$CI->load->library('alert');
					echo "<div class='msgBar' style='width:96%'>". $CI->alert->check($msg) ."</div>";
				}
			    
				
			    extract($eCatalog[0]);
				$eCatalogID = $id;
			   ?>
			   <table style='border:1px;text-align:left;width:43%;'> 
			     <tr> 
				    <th>TITLE</th>
				    <td><?php echo $title ?></td>
			     </tr>
				  <tr> 
				    <th>PUBLISH</th>
				    <td><?php echo $publish=='y' ? "Yes":"No" ?></td>
			     </tr>
				 <tr> 
				    <th>COVER</th>
				    <td><img src='<?php echo HTTP_PATH?>img/cover/<?php echo $cover?>' width='200'></td>
			     </tr>
				 <tr> 
				    <th>BRAND GUIDELINES</th>
				    <td><a href='<?php echo HTTP_PATH.'files/brand_guidelines/'.$brand_guidelines ?>' target='_blank'>Brand Guidelines </a></td>
			     </tr>
				 
			   </table>
			   <hr style='width:95%'>
			   <a href="<?php echo HTTP_PATH?>eCatalog/items/<?php echo $ecID?>/add.html">
				   <div class="sub-link" style='width:95%'>
					<ul>
						<li> <img src="<?php echo HTTP_PATH?>img/plus.png" width="31" height="31"> </li>
						<li> <h5>Add <br/>Items</h5> </li>
					</ul>
				   </div>
			   </a>
			  <table cellpadding="0" cellspacing="0" style="width:97%; font-size:12px;">
				<tr>
					<th style="text-align:center;width:100px;">IMAGE</th>
					<th style="text-align:center;width:100px;">NAME</th>
					<th style="text-align:center;width:100px;">DESCRIPTION</th>
					<th style="text-align:center;width:150px;">CATEGORY</th>
					<th style="text-align:center;width:100px;">ACTION</th>
				</tr>
				<?php 
		       $CI =& get_instance();
				
				$x=0; 
				//print_r($ecItems);
				foreach($ecItems as $k=> $dd){
					//GET THE FIRST IMAGE
					//var_dump($dd);
					
					extract($dd); 
					//echo "SELECT image FROM ecitems_images WHERE   itemID = $itemID  LIMIT 1";					
					$sql = $CI->db->query("SELECT image FROM ecitems_images WHERE   itemID = $itemID  AND defaultStatus = 1 LIMIT 1");
					$item_img = $sql->result_array();
					extract($item_img);
					$item_img = isset($item_img[0]['image']) ? $item_img[0]['image'] : 'blank.png';
					
					$c = (($x++)%2) == 0 ? "class='alter'" : ""; 
					
					$gray = '';
					if($ec_itemsPublish != 'y'){
						$gray =  "style='background:#e3e3e3'";
						$c = '';
					}
					?>
					<tr <?php echo $gray ?>>
						<td <?php echo $c ?>>
							<div class="clear ctn" style="border-bottom:1px solid #eeeeee;height:160px;text-align:center;width:210px;overflow:hidden">
								 <img src="<?php echo HTTP_PATH."img/galleryImg/".$item_img ?>"> 
							 </div>
				
							<?php if($ec_itemsPublish != 'y')
									echo "<p style='color:red;font-size:10px;text-align:left;'> *Warning save as draft. </p>"
							?>
						</td>
						<td <?php echo $c ?>> <b><?php echo $itemName ?></b> </td>
						<td <?php echo $c ?>> <?php echo $Short_Description ?> </td>
						<td style="text-align:left;" <?php echo $c ?>> 
							<span style="color:#bb4041;"> Brand Name:		</span> <?php echo $e_catalogBrand   ?> 	<br/> 
							<span style="color:#bb4041;"> POSM Type:		</span> <?php echo $typeName 		 ?> 	<br/> 
							<span style="color:#bb4041;"> Outlet Type:		</span> <?php echo $OutletStatusName ?> 	<br/>  
							<span style="color:#bb4041;"> Premium Type Name:</span> <?php echo $premiumTypeName  ?> 	<br/>  
							<span style="color:#bb4041;"> Meterial Type:	</span> <?php echo $materialName 	 ?> 	<br/>  
						</td>
						 
						<?php echo "<td $c style='text-align:center;'>";
									//if($EDIT)	
										echo "<a href='".HTTP_PATH."eCatalog/items/$ecID/edit/".$itemID.".html'>Edit</a> |";
									//if($DELETE)	
										echo "<a style='cursor:pointer' onclick='deleteOneItem($eCatalogID,$itemID)'>Delete</a> 
								</td>"; 
						?>
					</tr>
			<?php 
				//$gray = '';
				} ?>
			</table>  
             </div>
		</div>	
        </div>
		
        <div class="clear"></div>
	

	<script type="text/javascript">
		function deleteOneItem(eCatalogID,itemID)
		{
			jConfirm("Are you sure you want to delete this Item?","Alert",function(r){
				if(r)
					window.location = "<?php echo HTTP_PATH ?>eCatalog/items/"+ eCatalogID +"/deleteOneItem/"+ itemID;
			} );
			
		}
	</script>	