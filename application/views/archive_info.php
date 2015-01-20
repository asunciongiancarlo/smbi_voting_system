
 <div class="content">
		
    	<div class="title-content">
        	<h2>Archive Basic Info</span> </h2>
        </div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container">
			<?php 
			//print_r($archive_list);
			$aID = $archive_info[0]['id']; ?>
			<table cellpadding="0" cellspacing="0" style="width:100%;margin: 0px auto;" class="iLike_Result_Table">
				<br/>
			   <tr style="border-radius: 6px;margin-top:10px;">
					<td style="width:200px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;">  ARCHIVE NAME 		</td> 
					<td style="width:600px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;">  DATE FROM 			</td> 
					<td style="width:200px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;">  DATE TO  			</td> 
				</tr>
				<tr>
				  <td><?php echo str_replace('_',' ', $archive_info[0]['archive_name'])    	?> 	</td>
				  <td><?php echo date("M d, Y", strtotime($archive_info[0]['startDate']))	?> 	</td>
				  <td><?php echo date("M d, Y", strtotime($archive_info[0]['endDate']))		?> 	</td>
				</tr>
			</table>
			
				<div id="tabs" style="font-size:14px;">
				  <ul>
					<li><a href="#tabs-1" style="height: 8px;padding-top: 14px;"> eCatalogue		</a></li>
					<li><a href="#tabs-3" style="height: 8px;padding-top: 14px;"> Item Database 	</a></li>
					<li><a href="#tabs-2" style="height: 8px;padding-top: 14px;"> Campaigns 		</a></li>
				  </ul>
				  <div id="tabs-1" style="height:530px;padding-left:50px;overflow-y: scroll;">
					
					
					<div style="margin:0px auto;">
					<?php if($Archive_e_catalog==TRUE){ ?>
						<input type='button' name='button' value='Restore eCatalogue' class='clickMe' onclick="restore_eCatalog(<?php echo $aID ?>)"><br/>
					<?php } ?>
					<table cellpadding="0" cellspacing="0" style="100%">
						<tr style="border-radius: 6px;">
							<th style="width:10px;text-align:center;">  No 			    					</th> 
							<th style="width:200px;text-align:center;"> BRAND 			    				</th> 
							<th style="width:200px;text-align:center;"> TITLE 			    				</th> 
							<th style="width:200px;text-align:center;"> BRAND GUIDELINES 			   		</th> 
							<th style="width:200px;text-align:center;"> DATE UPDATED 			 			</th>   
							<th style="width:200px;text-align:center;"> ITEMS 			 					</th>   
						</tr>
						
						<?php
							$x=0;
							$y=1;
							$z=1;
							foreach($eCatalogue as $eC)
							{
								$c = (($x++)%2) == 0 ? "class='alter3'" :  ""; 
								extract($eC);
								echo "<tr>";
									echo "<td $c >". $x  																							  		."</td>";
									echo "<td $c >  <img src='". HTTP_PATH."img/cover/".$cover  ."' width='80px;'>   										  </td>";
									echo "<td $c >". $title  																								."</td>";
									echo "<td $c > <a href='".HTTP_PATH."files/brand_guidelines/$brand_guidelines' target='_blank'>Brand Guidelines </a>	  </td>";
									echo "<td $c >". date("M d, Y", strtotime($tdate)) ."</td>"; 
										echo "<td $c style='text-align:center;'>"; ?>
										<label onclick="showItems('<?php echo 'i'.$z++ ?>',<?php echo $id ?>)" title="View Items"> View Items </label>
									<?php 
											echo "</td>";
										echo "</tr>"; ?>
									<tr>
										<td colspan='6' style='padding: 0;'>
											<div id='i<?php echo $y++ ?>' style='display:none;height:151px;overflow-y:scroll;'></div>
										</td>
									</tr>
						   <?php } ?>
					</table>
					</div>
					</div>
				<div id="tabs-3" style="height:530px;overflow-y: scroll;">
					<?php if($Archive_items==TRUE){ ?>
						<input type='button' name='button' value='Restore Item Database' class='clickMe' onclick="restore_Item_Database(<?php echo $aID ?>)"><br/>
					<?php } ?>
					<table cellpadding="0" cellspacing="0" border=1 style="width:100%;margin: 0px auto;font-size:12px;" class="iLike_Result_Table">
						<tr style="border-radius: 6px;">
							<td style="width:10px;text-align:center;color:white;" bgcolor='#bb1d1d'>   <b>No 		 </b></td> 
							<td style="width:30px;text-align:center;color:white;" bgcolor='#bb1d1d'>   <b>Image  	 </b></td> 
							<td style="width:200px;text-align:center;color:white;" bgcolor='#bb1d1d'>  <b>Name  	 </b></td> 
							<td style="width:100px;text-align:center;color:white;" bgcolor='#bb1d1d'>  <b>Description</b></td> 
						</tr>
						 <?php 
							$x = 0;
							$total = 0;
							$y=1;
							$z=1;
							foreach($items as $i) { 
							extract($i);
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
						 ?>
						<tr>
						  <td <?php echo $c ?> >							<?php echo $x ?> 																			  </td>
						  <td <?php echo $c ?> style='text-align:center;' > <img src="<?php echo HTTP_PATH .'img/thumb/'.$item_image ?>" style="width:30px;height:30px">  </td>
						  <td <?php echo $c ?> style='text-align:left;padding-left:50px;'>	<?php echo $itemName ?> 													  </td>
						  <td <?php echo $c ?> style='text-align:left;padding-left:50px;'>	<?php echo $Short_Description ?> 											  </td>
						</tr>
						 <?php } ?>
					</table>
				  </div>
				  
				  <div id="tabs-2" style="height:530px;overflow-y: scroll;">
					<?php if($Archive_campaign==TRUE){ ?>
						<input type='button' name='button' value='Restore Campaigns' class='clickMe' onclick="restore_Campaigns(<?php echo $aID ?>)"><br/>
					<?php } ?>
					<table cellpadding="0" cellspacing="0" style="width:100%;margin: 0px auto;">
						<tr style="border-radius: 6px;">
							<th style="width:10px;text-align:center;">   No 			</th> 
							<th style="width:100px;text-align:center;">  DATE 			</th> 
							<th style="width:100px;text-align:center;">  TYPE 			</th> 
							<th style="width:340px;text-align:center;">  CAMPAIGN NAME 	</th> 
							<th style="width:100px;text-align:center;">  DATE FROM 		</th> 
							<th style="width:100px;text-align:center;">  DATE TO 		</th> 
							<th style="width:100px;text-align:center;">  STATUS 		</th> 
						</tr>
						<?php
							$x=0;
							foreach($campaigns as $campaign)
							{
								$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
								extract($campaign);
								$iSatus =trim(strtolower($status))=='on progress' ? "in progress": $status;
								echo "<tr>";
									echo "<td $c > $x </td>";
									echo "<td $c >". date("M d, Y", strtotime($DateAdded)) 	."</td>";
									echo "<td $c > $campaignType							  </td>";
									echo "<td $c style='text-align:left;padding-left:10px;'>". $campaignName 	."</td>";
									echo "<td $c >". $DateFrom 		."</td>";
									echo "<td $c >". $DateTo 		."</td>";
									echo "<td $c >"; 
											if($campaignType=='iLike')
												echo "<a href='".HTTP_PATH."users/iLikeReport/$id/$aID' target='_new'>". $iSatus 		."</a>"; 
											else
												echo "<a href='".HTTP_PATH."users/iWantReport/$id/$aID' target='_new'>". $iSatus 		."</a>";
								echo "</td>";
								echo "</tr>";
							}
						?>
					</table>
				  </div>
				
				</div>
			
			
			<div class="clear">&nbsp;</div>	
             			
			<div class="clear">&nbsp;</div>	
            </div>
        </div>
           
        <div class="clear"></div>
    </div>
	

	
	<script>
	  $(function() {
		$( "#tabs" ).tabs();
	  });
	  
	  function showItems(dID,cID)
	  {
		if(document.getElementById(dID).style.display == "none"){
			ajax('<?php echo HTTP_PATH ?>users/showItems/'+ cID ,dID);
			document.getElementById(dID).style.display = "block";
		}	
		else{ 
			document.getElementById(dID).style.display = "none";
		}
	  }
	  
	  function restore_eCatalog(aID)
	  {
		url = '<?php echo HTTP_PATH."restore/restore_eCatalogue/$aID" ?>'; 
		jConfirm("Are you sure you want to restore all of this eCatalogue?","Alert",function(r){
			if(r){
				window.open(url);
				location.reload();
			}
		});
	  }
	  
	   function restore_Item_Database(aID)
	  {
		url = '<?php echo HTTP_PATH."restore/restore_Item_Database/$aID" ?>'; 
		jConfirm("Are you sure you want to restore all of this items?","Alert",function(r){
			if(r){
				window.open(url);
				location.reload();
			}
		});
	  }
	  
	  function restore_Campaigns(aID)
	  {
		url = '<?php echo HTTP_PATH."restore/restore_Campaigns/$aID" ?>'; 
		jConfirm("Are you sure you want to restore all of this Campaigns?","Alert",function(r){
			if(r){
				window.open(url);
				window.location = '<?php echo HTTP_PATH.'users/archive_list'; ?>';
			}
		});
	  }
  </script>