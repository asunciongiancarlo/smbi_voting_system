
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
				<li><a href="#tabs-1" style="height: 8px;padding-top: 14px;"> iLike Results					</a></li>
				<li><a href="#tabs-2" style="height: 8px;padding-top: 14px;"> All Items					</a></li>
				<li><a href="#tabs-3" style="height: 8px;padding-top: 14px;"> Nomination Committees </a></li>
				<li><a href="#tabs-4" style="height: 8px;padding-top: 14px;"> Campaign Description  </a></li>
			  </ul>
			   <div id="tabs-1" style="height:530px;padding-left:50px;overflow-y: scroll;">
					<div style="margin:0px auto;">
					<?php 	
					//print_r($repHeader);
					//print_r($topItems);
					$lastItem="";
					$CI =& get_instance();
					foreach($topItems as $i){
					extract($i);
					if($EDIT)
					$edit_shortCut="<a href='".HTTP_PATH."itemDatabase/items/edit/$iID' target='_blank'> <img src='".HTTP_PATH."img/edit-item.png' height='16' width='16'></a>";
					
					if($lastItem!=$POSM_TypeName) echo "<h5 style='clear:both;text-align: left;background: #330404;margin: 0;color: #fff;padding: 2px 10px;'> $POSM_TypeName  </h5>";
					?>
					
						<div class="fl" style='margin:5px;height:218px;overflow:hidden;border:1px solid gray;'>
							<div style='text-align:center;height:23px;background:#bb1d1d;color:black;font-style:bold;'> <b style='color:white;'><?php echo $POSM_TypeName ?></b> <?php echo $edit_shortCut ?> </div>
							<div style='text-align:center;height:23px;background:#bb1d1d;color:black;font-style:bold;'> <b style='color:white;'><?php echo $extra_label ?></b> <?php echo $edit_shortCut ?> </div>
							<div class="clear"></div>
							 
							 <div class="clear"  style='border-bottom:1px solid #eeeeee;height:120px;text-align:center;width:195px;overflow:hidden'>
								<a href="<?php echo HTTP_PATH.'gallery/itemInfo2/'.$iID .'/iR/'.$repHeader[0]['cID'] ?>" target='_blank'> 
								 <img   src="<?php echo HTTP_PATH?>img/small/<?php echo $item_image ?>" style='height:100%'>
								</a>
							 </div>
							 <div style='text-align:center;'>
								<hr style='margin:2px 0'>
								<a href="<?php echo HTTP_PATH.'gallery/itemInfo2/'.$iID .'/iR/'.$repHeader[0]['cID'] ?>" target='_blank'> 
								<p class='ptitle' alt='' title="<?php echo $itemName ?>" style='font-size:14px;margin-top:5px;color:#bb4041'>
									<b><?php 
										if(strlen($itemName)>=20)
											echo substr($itemName,0,20)."...";
										else	
											echo $itemName;
										?>
									</b>
								</p>
								</a>
								<label style='color:#555555;font-size:11px;margin-top: -15px;'> Likes: <?php echo $voteTot ?> </label>
								
							 </div>
						</div>
					</a>
					<?php 
					$lastItem=$POSM_TypeName; 
					} ?>
					</div>
				  </div>
			 <div id="tabs-2" style="height:530px;padding-left:50px;overflow-y: scroll;">
			 <?php 
			 //print_r($rep);
			 ?>
					<div class="clear">&nbsp;</div>	
					<table cellpadding="0" cellspacing="0" border=1 style="width:100%;margin: 0px auto;margin-top: -20px;font-size: 13px;" class="iLike_Result_Table">
						<tr style="border-radius: 6px;">
							<td style="width:10px;text-align:center;" bgcolor='#999999'>   <b>No 		 </b></td> 
							<td style="width:30px;text-align:center;" bgcolor='#999999'>   <b>Item Type  </b></td> 
							<td style="width:30px;text-align:center;" bgcolor='#999999'>   <b>Item Code  </b></td> 
							<td style="width:50px;text-align:center;" bgcolor='#999999'>   <b>Image  	 </b></td> 
							<td style="width:150px;text-align:center;" bgcolor='#999999'>  <b>Item Name  </b></td> 
							<td style="width:50px;text-align:center;" bgcolor='#999999'>   <b>Likes	 	 </b></td> 
							<td style="width:50px;text-align:center;" bgcolor='#999999'>   <b>Action	 </b></td> 
						</tr>
						 <?php 
							$x=0;
							$total=0;
							$z=0; $y=0;
							$premium=0;
							$service=0;
							foreach($rep as $r) {
							extract($r);
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							$total += $voteTot;
							$itemName =  ($r['ptype']!='-') ? "<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'> $itemName </a>" : "$itemName";
							
							//PREMIUM
							if($r['ptype']=="Premium Item"){ $premium++;
							}else{ $service++; }
						 ?>
						<tr>
						  <td <?php echo $c ?> ><?php echo $x ?> 		 </td>
						  <td <?php echo $c ?> ><?php echo $r['ptype'] ?> </td>
						  <td <?php echo $c ?> ><?php echo $r['iCode']?> </td>
						  <td <?php echo $c ?> ><img src='<?php echo HTTP_PATH ."img/thumb/".$item_image; ?>' style="width:30px;height:30px"> </td>
						  <td <?php echo $c ?> style='text-align:left;' > 
							 <?php echo $itemName ?> 
						</td>
						  <td <?php echo $c ?> ><?php echo $voteTot ?> </td>
						  
						  <td <?php echo $c ?> >
							<?php if($voteTot!=0) ?>
								<label onclick="showVoters('<?php echo 'v'.$z++ ?>',<?php echo $campaignID ?>,<?php echo $itemID?>)" title="Voters" style='cursor:pointer;color:#FF575A;'>
									Details
								</label> 
							
						  </td>
						   
						</tr>
						<tr>
							<td colspan='7'> 
								<div id='<?php echo 'v'.$y++ ?>' style='display:none;'> </div>
							</td>
						</tr>
						</tr>
						 <?php } ?>
						 <tr>
							<td colspan='5' style='text-align:left;font-weight:bold;'>Total </td>
							<td style='font-weight:bold;'><?php echo $total; ?> </td>
							<td style='font-weight:bold;'> </td>
						 </tr>
					</table>			
					<div class="clear">&nbsp;</div>	
			  </div>
			  <div id="tabs-3" style="height:530px;padding-left:50px;overflow-y: scroll;">
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
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Status   </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Alter   	   </b></td> 
						<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Action      </b></td> 
					</tr>
					 <?php 
						$x = 0;
						//print_r($voters);
						$total = 0;
						foreach($voters as $v) { 
						extract($v);
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
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
						<?php if($votingStatus!='done'){ ?>
								<a onclick="viewDialog('iLike',<?php echo $campaignID.','.$id ?>)" style='cursor:pointer;color:#FF575A;'> Details </a>
						<?php }else{ ?>
								<a onclick="viewDialog('iLike',<?php echo $campaignID.','.$id ?>)" style='cursor:pointer;color:#FF575A;'> Details </a>	
						<?php } ?> 				
					</td>
					
					</tr>
					 <?php } ?>
				</table>
			  </div>
			  <div id="tabs-4" style="height:530px;padding-left:50px;overflow-y: scroll;">
				<?php 
					echo "<strong style='color:#710002'> Campaign rules: </strong>";
					foreach($iLike_Rules_Ref as $iLRR)
					{ extract($iLRR);
					  echo $fieldValue ." ".$rel." ".$val."<br/>"; 
					}
					echo "<label style='color:#555;margin-top: -6px;font-size:10px;'>Note: 0 as value and no Relational Operator Means ALL item in Database</label>";
					echo "<hr class='dividerHR'>";
					echo "<strong style='color:#710002'> Minimum number of nominees: </strong>".$iLike_Rules_No_Committes_Ref[0]['num_commitee'] ."<br/>";
					echo "<hr class='dividerHR'>";
					
					echo "<strong style='color:#710002'> Premium Items: </strong>".$premium."<br/>";
					echo "<strong style='color:#710002'> Service Items: </strong>".$service."<br/>";
					echo "<strong style='color:#710002'> Total Items: </strong> ". ($service+$premium) ."<br/>";
					echo "<hr class='dividerHR'>";
					echo "<strong style='color:#710002'> Voting rules: </strong><br/>";
					$x=0;
					$ctr = count($iLikeVotingRulesRef);
					foreach($iLikeVotingRulesRef as $i)
					{ extract($i);
					  $x++;
					  $br = "<br/>";
					  if($ctr==4 AND ($x==1 OR $x==3))
					  $br = "AND <br/>";
					  
					  echo "$table  : $fieldName  $relation  $value $br";
					}
					echo "<hr class='dividerHR'>";
					echo "<strong style='color:#710002'> Canvassing rules: </strong>"; 
					foreach($iLikeCanvassingRulesXref as $i)
					{ extract($i);
					  if(strpos($actual_input,'.')){
					    echo "Number of Likes per Item: $lrel $rel ". $actual_input*100 ."% (<b>$val</b>) <br/>";
					  }else{
					   echo "Number of Likes per Item: ". $lrel ." ". $rel ." ". $val ."<br/>";
					  }
					  
					}
					
				?>
			  </div>
			</div>
           </div>
        <div class="clear"></div>
    </div>
    </div>

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
		url: '<?php echo HTTP_PATH ?>report/vote_items/'+ctype+'/'+cID+'/'+vID,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>