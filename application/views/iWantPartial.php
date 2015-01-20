 <div class="content">
		
    	<div class="title-content">
        	<h2><span style="text-transform:lowercase;">i</span>WANT PARTIAL REPORT</span> </h2>
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
				<?php if($PUBLISH_CAMPAIGN & $_SESSION['countryID']==0){ ?><li><a href="#tabs-1" style="height: 8px;padding-top: 14px;"> All Items				</a></li> <?php } ?>
																		  <li><a href="#tabs-2" style="height: 8px;padding-top: 14px;"> Screening Committees </a></li>
				<?php if($PUBLISH_CAMPAIGN & $_SESSION['countryID']==0){ ?><li><a href="#tabs-3" style="height: 8px;padding-top: 14px;"> Campaign Description  </a></li> <?php } ?>
			  </ul>
			  <?php if($PUBLISH_CAMPAIGN & $_SESSION['countryID']==0){ ?>
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
									<td style='width:10px;text-align:center;' bgcolor='#d3d3d3'>   <b>No	 </b></td> 
									<td style='width:76px;text-align:center;' bgcolor='#d3d3d3'>   <b>Item Code  </b></td> 
									<td style='width:76px;text-align:center;' bgcolor='#d3d3d3'>   <b>USD Price  </b></td> 
									<td style='width:50px;text-align:center;' bgcolor='#d3d3d3'>   <b>Image  	 </b></td> 
									<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Item Name  </b></td> 
									<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Country    </b></td> 
									<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Date Released    </b></td> 
									<td style='width:50px;text-align:center;' bgcolor='#d3d3d3'>   <b>Want	 	 </b></td> 
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
						  <td <?php echo $c ?> style='text-align:left;'><?php echo $countryName ?> </td>
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
			  <?php } ?>
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
						<td style="width:154px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Country 	   	   </b></td> 
						<td style="width:96px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Email Sent  </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Status      </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Alter   	   </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Action      </b></td> 
					</tr>
					 <?php 
						$x = 0;
						$total = 0;
						//print_r($voters);
						foreach($voters as $v) { 
						extract($v);
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
						if($votingStatus=='invited') $votingStatus= votingStatus($campaignID,$voterID);
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
					  <td <?php echo $c ?> style='text-align:left;'>					<?php echo $countryName ?> 	</td>
					  <td <?php echo $c ?> style='text-align:center;'>					<?php echo $email_sent = ($email_sent=='y') ? 'Yes' : 'No'; ?> 	</td>	
					  <td <?php echo $c ?> style='text-align:center;'>					<?php echo $votingStatus ?> 				</td>
					  <td <?php echo $c ?> style='text-align:center;'>					<?php echo $addedAlter = ($addedAlter=='y') ? 'Yes' : 'No'; ?> 	</td>
					  <td <?php echo $c ?> style='text-align:left;'>
					  <a onclick="viewDialog('iWant',<?php echo $campaignID.','.$voterID ?>)" style='cursor:pointer;color:#FF575A;'> Details </a>	
					</td>
					
					</tr>
					<?php } 
					if(count($voters)==0) echo "<tr><td colspan='13'>Sorry there is no screening committees in your business unit for this campaign.</td></tr>" 
					?>
				</table>
			  </div>
			  <?php if($PUBLISH_CAMPAIGN & $_SESSION['countryID']==0){ ?>
			  <div id="tabs-3" style="height:530px;overflow-y: scroll;">
				<?php 
					echo "<strong style='color:#710002'> Number of screening committee per business unit: </strong>".$iWant_Rules_No_Committes_Ref[0]['num_commitee'] ."<br/>";
					echo "<hr class='dividerHR'>";
					$x=0;					
					echo "<table style='font-size:12px;'>
					<tr><td colspan='6' style='text-align:left'><b>Voting Rules</b></td></tr>
					<tr style='border-radius: 6px;font-size: 10px;'>
						<td style='width:0px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>No.  				</b></td> 
						<td style='width:155px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Item Type  				</b></td> 
						<td style='width:284px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Price Category  			</b></td> 
						<td style='width:471px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Voting Condition  		</b></td> 
						<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Current Items    		</b></td> 
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
							  <td  $c  style='text-align:center;'>	  <b>$current_num_items</b>  		</td> 
							  ";
						echo "</tr>";
						$TOTALcurrent_num_items+=$current_num_items;
					}
					echo "<tr class='alter'>  <td><b>Total</b></td><td colspan='1'></td> <td colspan='2'></td> <td><b>$TOTALcurrent_num_items</b></td></tr>";
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
					foreach($iWantCanvassingRulesXref as $r){
						extract($r);
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
						echo "<tr>
							  <td  $c  style='text-align:left;' colspan='2'>	  $x  		</td>
							  <td  $c  style='text-align:left;' colspan='2'>	  $fieldValue  		</td>
							  <td  $c  style='text-align:left;' colspan='1'>	  $price_rangeName  </td>
							  <td  $c  style='text-align:left;padding-left:20px;' colspan='3'>	  $cond1 $min_val $logical_operator $cond2  $max_val 					</td>
							  ";
						echo "</tr>";
					}
					echo "</table>";
				?>
			  </div>
			  <?php } ?>
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
			ajax('<?php echo HTTP_PATH ?>report/iWant_Voters/'+ cID +'/'+ itemID ,dID);
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
		url: '<?php echo HTTP_PATH ?>report/vote_items/'+ctype+'/'+cID+'/'+vID,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>
	