 <div class="content">
		
    	<div class="title-content">
        	<h2><span style="text-transform:lowercase;">i</span>LIKE REPORT</span> </h2>
        </div>
		<div style="float:right;margin: 16px;">
		 <a href='<?php echo HTTP_PATH ."report/downloadCSV/$csvFile"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/download-icon.png' title='Download Reports'></a>
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
			
			<div id="tabs" style="font-size:14px;">
			  <ul>
				<li><a href="#tabs-0" style="height: 8px;padding-top: 14px;"> Winning Items					</a></li>
				<li><a href="#tabs-1" style="height: 8px;padding-top: 14px;"> All Items					</a></li>
				<li><a href="#tabs-2" style="height: 8px;padding-top: 14px;"> Nomination Committees </a></li>
				<li><a href="#tabs-3" style="height: 8px;padding-top: 14px;"> Campaign Description  </a></li>
			  </ul>
			   <div id="tabs-0" style="height:530px;overflow-y: scroll;padding: 0;">
				<div style="margin:0px auto;">
				<?php 	
				$iType="";
				$pCategory="";
				$ctr=0;
				$CI =& get_instance();
				foreach($POSM_Type as $p_type)
				{ extract($p_type);
				  $CI->load->database('default');
				  //IDENTIFY THE NUMBER OF LEVELS PER ITEM TYPE
				  $levels = $CI->db->query("SELECT price_range.id as prID, extra_label as eLabel FROM price_range WHERE POSMTypeID = $pID ORDER BY id ASC");
				  $levels = $levels->result_array();
				  if($iType!=$typeName){ echo "<h5 style='clear:both;background:#900808;color:white;font-size:16px;text-align:left;padding:6px;'> $typeName  </h5>";  $ctr=0;}
				  //FIND ALL ITEMS  
				  foreach($levels as $l)
				  { extract($l);
				   
				  $sqlSTr="SELECT items.id as iID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName, extra_label,
							(SELECT totvote FROM iLikeResultRef WHERE itemID = iID AND campaignID = $cID) AS voteTot,
							POSM_Type.typeName as POSM_TypeName 
							FROM items
							LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
							LEFT JOIN price_range ON price_range.id = items.price_rangeID
							WHERE  items.id IN (SELECT itemID FROM iLikeResultRef WHERE campaignID = $cID) AND items.POSMTypeID = $pID AND items.price_rangeID = $prID
							ORDER BY voteTot DESC";
					$winningItems 	  = $this->db->query($sqlSTr);
					$winningItems 	  = $winningItems->result_array();
	
					if(count($winningItems)>0 AND $pCategory!=$eLabel) 
					echo "<div style='clear:both;background:#d3d3d3;color:black;font-size:12px;text-align:left;padding:6px;width:1091px;margin-left: 20px;'> ".$eLabel."</div>
						  <div style='border:1px solid #d3d3d3;min-height:200px;width: 1101px;;margin-top: -10px;margin-left: 20px;overflow: hidden;margin-bottom: 10px;'>";
					$x=1;
					foreach($winningItems as $w)
					{ extract($w);
					  $label = "<label style='position:absolute;top: 145px;left: 7px;color:#A39E9E;font-size:11px;'>".$x++.". </label>";
					  if($EDIT) $edit_shortCut="<a href='".HTTP_PATH."itemDatabase/items/edit/$iID' target='_blank'> <img src='".HTTP_PATH."img/edit-item.png' height='16' width='16' style='padding: 3px;position: absolute;border: 1px solid rgb(197, 188, 188);right: 2px;top: 2px;background: rgb(252, 252, 252);'></a>";
					  ?>
					  <div class="fl" style='margin:5px;height:161px;overflow:hidden;border:1px solid #f7cb60;padding: 0px 0px 4px 0px;width: 180px;margin:19px;position:relative;'>
							 <div style='text-align:right;'>
								<?php echo $edit_shortCut; ?>
							 </div>
							 <div class="clear"  style='border-bottom:1px solid #eeeeee;height:120px;text-align:center;width:184px;overflow:hidden'>
								<a href="<?php echo HTTP_PATH.'gallery/itemInfo2/'.$iID .'/iR/'.$cID ?>" target='_blank'> 
								 <img   src="<?php echo HTTP_PATH?>img/small/<?php echo $item_image ?>" style='height:100%'>
								</a>
								
							 </div>
							 <div style='text-align:center;'>
								<hr style='margin:-1px 0'>
								<a href="<?php echo HTTP_PATH.'gallery/itemInfo2/'.$iID .'/iR/'.$cID ?>" target='_blank'> 
								<p class='ptitle' alt='' title="<?php echo $itemName ?>" style='font-size:12px;margin-top:5px;color:black'>
									<b><?php 
										if(strlen($itemName)>=20){
											echo substr($itemName,0,20)."...";
										}else{	
											echo $itemName;
										}
										?>
									</b>
								</p>
								</a>
								<?php echo $label ?>
								<label style='color:#555555;font-size:11px;margin-top: -15px;'> Likes: <?php echo $voteTot ?> </label>
							 </div>
						</div>
					  <?php
					  $ctr++;
					}
					if(count($winningItems)>0 AND $pCategory!=$eLabel) echo "</div>";
					$pCategory  = $eLabel;
				  }
				  if($ctr==0) echo "<p style='margin-left: 16px;color: gray;font-size: 13px;'>Sorry there are no winning items from this item type.</p>";
				} ?>
				</div>
			  </div>
			  <div id="tabs-1" style="height:530px;overflow-y: scroll;">
					<div class="clear">&nbsp;</div>	
					<table cellpadding="0" cellspacing="0" border=1 style="width:100%;margin: 0px auto;margin-top: -20px;font-size: 12px;" class="iLike_Result_Table">
						
						 <?php 
							$x=0; 
							$total=0;
							$z=0; $y=0;
							$iType="";
							$pType="";
							foreach($rep as $r) {
							extract($r);
							//HEADER
							if($iType!=$ptype) 		 echo "<tr> <td colspan='9' style='background:#900808;color:white;font-size:16px;text-align:left;padding:10px;'>$ptype</td></tr>";
							if($pType!=$extra_label) echo "<tr> <td colspan='9' style='background:#AFAFAF;color:black;font-size:12px;text-align:left;padding: 5px 5px 5px 10px;'>$extra_label</td></tr>";
							if($iType!=$ptype) {
							echo  "<tr style='border-radius: 6px;'>
									<td style='width:10px;text-align:center;' bgcolor='#d3d3d3'>   <b>No 		 </b></td> 
									<td style='width:76px;text-align:center;' bgcolor='#d3d3d3'>   <b>Item Code  </b></td> 
									<td style='width:76px;text-align:center;' bgcolor='#d3d3d3'>   <b>USD Price  </b></td> 
									<td style='width:50px;text-align:center;' bgcolor='#d3d3d3'>   <b>Image  	 </b></td> 
									<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Item Name  </b></td> 
									<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Country    </b></td> 
									<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Date Released    </b></td> 
									<td style='width:50px;text-align:center;' bgcolor='#d3d3d3'>   <b>Likes	 	 </b></td> 
									<td style='width:50px;text-align:center;' bgcolor='#d3d3d3'>   <b>Action	 </b></td> 
								</tr>";
							}
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							$total += $voteTot;
							$itemName =  ($ptype!='-') ? "<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'> $itemName </a>" : "$itemName";
						 ?>
						<tr>
						  <td <?php echo $c ?> ><?php echo $x ?> 		 </td>
						  <td <?php echo $c ?> ><?php echo $iCode ?> </td>
						  <td <?php echo $c ?> ><?php echo $uPrice ?> </td>
						  <td <?php echo $c ?> ><img src='<?php echo HTTP_PATH ."img/thumb/".$item_image; ?>' style="width:30px;height:30px"> </td>
						  <td <?php echo $c ?> style='text-align:left;' > <?php echo $itemName ?> </td>
						  <td <?php echo $c ?> ><?php echo $countryName ?> </td>
						  <td <?php echo $c ?> ><?php echo $dReleased ?> </td>
						  <td <?php echo $c ?> ><?php echo $voteTot ?> </td>
						  
						  <td <?php echo $c ?> >
							<?php if($voteTot!=0) ?>
								<label onclick="showVoters('<?php echo 'v'.$z++ ?>',<?php echo $campaignID ?>,<?php echo $itemID?>)" title="Voters" style='cursor:pointer;color:#FF575A;'>
									Details
								</label> 
							
						  </td>
						   
						</tr>
						<tr>
							<td colspan='9'> 
								<div id='<?php echo 'v'.$y++ ?>' style='display:none;'> </div>
							</td>
						</tr>
						 <?php 
						 $iType = $ptype;
						 $pType = $extra_label;
						 } ?>
					</table>			
					<div class="clear">&nbsp;</div>	
			  </div>
			  <div id="tabs-2" style="height:530px;overflow-y: scroll;">
				<table cellpadding="0" cellspacing="0" border=1 style="width:100%;margin: 0px auto;font-size:12px;" class="iLike_Result_Table">
					<tr style="border-radius: 6px;">
						<td style="width:10px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>No 		   </b></td> 
						<td style="width:130px;text-align:center;color:white;" bgcolor='#bb1d1d'>   <b>First Name  </b></td> 
						<td style="width:130px;text-align:center;color:white;" bgcolor='#bb1d1d'>   <b>Last  Name  </b></td> 
						<td style="width:30px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Gender  	   </b></td> 
						<td style="width:300px;text-align:center;color:white;" bgcolor='#bb1d1d'>   <b>Email  	   </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Department  </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Year 	   </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Age 	   	   </b></td> 
						<td style="width:74px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Email Sent  </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Status      </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Alter   	   </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Action      </b></td> 
					</tr>
					 <?php 
						$x = 0;
						$total = 0;
						foreach($voters as $v) { 
						extract($v);
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
						if($votingStatus=='invited') $votingStatus= votingStatus($campaignID,$id);
					 ?>
					<tr>
					  <td <?php echo $c ?> >											<?php echo $x ?> 							</td>
					  <td <?php echo $c ?> style='text-align:left;padding-left:10px;'>	<?php echo $fname ?> 						</td>
					  <td <?php echo $c ?> style='text-align:left;padding-left:10px;'>	<?php echo $lname ?> 						</td>
					  <td <?php echo $c ?> style='text-align:left;padding-left:10px;'>	<?php echo $gender ?> 						</td>
					  <td <?php echo $c ?> style='text-align:left;'>					<?php echo $email ?> 						</td>
					  <td <?php echo $c ?> style='text-align:left;'>					<?php echo $department ?> 					</td>
					  <td <?php echo $c ?> style='text-align:center;'>					<?php echo $year_of_birth ?> 				</td>
					  <td <?php echo $c ?> style='text-align:center;'>					<?php echo (date('Y')-$year_of_birth) ?> 	</td>
					  <td <?php echo $c ?> style='text-align:center;'>					<?php echo $email_sent = ($email_sent=='y') ? 'Yes' : 'No'; ?> 	</td>	
					  <td <?php echo $c ?> style='text-align:center;'>					<?php echo $votingStatus ?> 				</td>
					  <td <?php echo $c ?> style='text-align:center;'>					<?php echo $addedAlter = ($addedAlter=='y') ? 'Yes' : 'No'; ?> 	</td>
					  <td <?php echo $c ?> style='text-align:left;'>
					  <a onclick="viewDialog('iLike',<?php echo $campaignID.','.$id ?>)" style='cursor:pointer;color:#FF575A;'> Details </a>	
					</td>
					
					</tr>
					 <?php } ?>
				</table>
			  </div>
			  <div id="tabs-3" style="height:530px;overflow-y: scroll;">
				<?php 
					echo "<strong style='color:#710002'> Campaign rules: </strong>";
					foreach($iLike_Rules_Ref as $iLRR)
					{ extract($iLRR);
					  echo $fieldValue ." ".$rel." ".$val."<br/>"; 
					}
					echo "<label style='color:#555;margin-top: -6px;font-size:10px;'>Note: 0 as value and no Relational Operator Means ALL item in Database</label>";
					echo "<hr class='dividerHR' style='height: 2px;'>";
					echo "<strong style='color:#710002'> Minimum number of nominees: </strong>".$iLike_Rules_No_Committes_Ref[0]['num_commitee'] ."<br/>";
					echo "<hr class='dividerHR'>";
					$x=0;					
					echo "<table style='font-size:12px;'>
					<tr><td colspan='6' style='text-align:left'><b>Voting Rules</b></td></tr>
					<tr style='border-radius: 6px;font-size: 10px;'>
						<td style='width:0px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>No.  				</b></td> 
						<td style='width:155px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Item Type  				</b></td> 
						<td style='width:284px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Price Category  			</b></td> 
						<td style='width:471px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Voting Condition  		</b></td> 
						<td style='width:200px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Min. No. of Likes   		</b></td> 
						<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Uploaded Items    		</b></td> 
					</tr>";
					$TOTALcurrent_num_items=0;
					foreach($VotingRules as $r){
						extract($r);
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
						echo "<tr>
							  <td  $c  style='text-align:left;'>	  $x  				</td>
							  <td  $c  style='text-align:left;'>	  $fieldValue  				</td>
							  <td  $c  style='text-align:left;'>	  $price_rangeName  		</td>
							  <td  $c  style='text-align:left;padding-left:20px;'>	  $cond1 $min_val $logical_operator $cond2 $max_val  </td>
							  <td  $c  style='text-align:center;'>	  $min_number_of_items  	</td> 
							  <td  $c  style='text-align:center;'>	  <b>$current_num_items</b>  		</td> 
							  ";
						echo "</tr>";
						$TOTALcurrent_num_items+=$current_num_items;
					}
					echo "<tr class='alter'>  <td><b>Total</b></td><td colspan='1'></td> <td colspan='3'></td> <td><b>$TOTALcurrent_num_items</b></td></tr>";
					echo "</table>";
					
					echo "<hr class='dividerHR' style='height: 2px;'>";
					echo "
					<table style='font-size:12px;width: 100%;'>
					<tr><td colspan='6' style='text-align:left'><b>Canvassing Rules</b></td></tr>	
					<tr style='border-radius: 6px;font-size: 10px;'>
						<td style='width:0px;text-align:left;color:white;' bgcolor='#bb1d1d' colspan='2'>   <b>No.  			</b></td> 
						<td style='width:76px;text-align:left;color:white;' bgcolor='#bb1d1d' colspan='2'>   <b>Item Type  			</b></td> 
						<td style='width:180px;text-align:left;color:white;' bgcolor='#bb1d1d' colspan='1'>   <b>Price Category  		</b></td> 
						<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d' colspan='3'>   <b>Canvassing Condition </b></td> 
					</tr>";
					$x=0;
					foreach($iLikeCanvassingRulesXref as $r){
						extract($r);
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
						echo "<tr>
							  <td  $c  style='text-align:left;' colspan='2'>	  $x  		</td>
							  <td  $c  style='text-align:left;' colspan='2'>	  $fieldValue  		</td>
							  <td  $c  style='text-align:left;' colspan='1'>	  $price_rangeName  </td>
							  <td  $c  style='text-align:center;' colspan='3'>	  $cond1 $min_val $logical_operator $cond2  $max_val 					</td>
							  ";
						echo "</tr>";
					}
					echo "</table>";
				?>
			  </div>
			</div>
           </div>
        <div class="clear"></div>
    </div>
    </div>
<?php
	function votingStatus($campaignID='',$voterID='')
	{
		$CI =& get_instance();
		$CI->load->library('forms');
		$sql = $CI->db->query("SELECT id FROM votexRef WHERE campaignID=$campaignID AND voterID=$voterID");
		$rows = $sql->result_array();
		if($rows)
			return "partial";
		else
			return "invited";
	}
?>
	
<script>
  $(function() {
	$( "#tabs" ).tabs();
  });
  
  	  function showVoters(dID,cID,itemID)
	  {
		if(document.getElementById(dID).style.display == "none"){
			ajax('<?php echo HTTP_PATH ?>report/iLike_Voters/'+ cID +'/'+ itemID ,dID);
			document.getElementById(dID).style.display = "block";
		}	
		else{ 
			document.getElementById(dID).style.display = "none";
		}
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
		url: 'http://smc.c3-interactive.com.ph/filexplorer',
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>
	