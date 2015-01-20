    <div class="content">
    	<div class="title-content" style='width:100%;'>
        	<h2 class='fl'><span style='text-transform:lowercase;'>i</span>LIKE CAMPAIGN </h2>
			
			<h2  style='text-decoration:underline;cursor:pointer;font-size:14px;margin-top:0px;float:right;float: right;margin-right: 45px;'>
				<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
			</h2>
			<h2  style='text-decoration:underline;cursor:pointer;font-size:14px;margin-top:0px;float:right;float: right;margin-right: 20px;' onclick='loadItems()'>ITEM LIST</h2>
			<h2  style='text-decoration:underline;cursor:pointer;font-size:14px;margin-top:0px;float:right;float: right;margin-right: 20px;' onclick='campaignRules()'>CAMPAIGN RULES</h2>
		</div>
		
        <div class="clear"></div>
        <div class="working_area">
			<?php
				$CI =& get_instance();
				//print_r($rules);
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}
		
				//ACTION STATEMENT
				$action = HTTP_PATH ."iLikeCampaign/votingCampaign/insert";
				
				//GET COUNTRY
				$sql = $CI->db->query("SELECT countryName FROM country WHERE id=". $_SESSION['countryID'] . " LIMIT 1");
				$row = $sql->row(); 
				$userCountryName = $row->countryName;
				
				$departments = "SELECT department_name FROM voters_department ORDER BY department_name ASC";
				$departments = $CI->db->query($departments);
				
				$campaignName   = date('Y-m-d')."_".$row->countryName ."_"."iLike Campaign" ; 
				$DateAdded 	    = ''; 
				$DatePublished 	= ''; 
				$DateFrom 		= ''; 
				$DateTo 		= ''; 
				$campaignID 	= 0;
				$status 		= 'new';
				
				//ISSET ID
				if(isset($id))
				{
					$action = HTTP_PATH ."iLikeCampaign/votingCampaign/update/".$id;
					$sql = $CI->db->query("SELECT * FROM campaign WHERE id= $id");
					$sql = $sql->result_array();
					extract($sql);
					
					$campaignName   	= $sql[0]['campaignName']; 
					$DateAdded 	    	= $sql[0]['DateAdded']; 
					$DatePublished 		= $sql[0]['DatePublished']; 
					$DateLastEdited 	= $sql[0]['DateLastEdited']; 
					$DateFrom 			= $sql[0]['DateFrom']; 
					$DateTo 			= $sql[0]['DateTo']; 
					$status 			= $sql[0]['status']; 
					$adminCreatorID 	= $sql[0]['adminCreatorID']; 
					$adminLastEditorID 	= $sql[0]['adminLastEditorID']; 
					$campaignID			= $id;
				}
				
				//REVOTE
				if(isset($revote))
					$action = HTTP_PATH ."iLikeCampaign/votingCampaign/insert";
				
				//ALTER
				if(isset($alter))
					$action = HTTP_PATH ."iLikeCampaign/alter/".$campaignID;
			?>
			<div class="container" style='padding:10px'>
			<?php 
				$CI =& get_instance();
				$CI->load->library('forms');
				$CI2 =& get_instance();
				$CI2->load->library('fv');

				echo $CI->forms->form_header('SMBi','vendorFORM',$action);
				echo '<div class="fl" style="width:21%; margin-right:86px;">
						<h2 class="form"> CAMPAIGN NAME </h2>';
				echo '<input name="campaignName" value="'.$campaignName.'" type="text" readonly data-parsley-trigger="change" data-parsley-required="true" placeholder="Required field" style="width: 130%;">';
						
				echo '</div>';
				echo "<input type='hidden' name='status' value='$status'>";
				echo $CI->forms->form_fields2('text_short','DateFrom',$DateFrom,$CI2->fv->label(44),'DateFrom');
				echo $CI->forms->form_fields2('text_short','DateTo',$DateTo,$CI2->fv->label(45),'DateTo');
				
				//REVOTE
				if(isset($revote)){
					echo "<input type='hidden' name='prevCampaignID' value='$campaignID'>";
				}
				
				//ILIKE RULES 
				generate_iLikeRules($rules,$voting_rules,$canvassing_rules,$min_committees);
				
				if(isset($id))
				{
				//CREATED BY
				$sql = $CI->db->query("SELECT full_name FROM admin_users WHERE id = $adminCreatorID LIMIT 1");
				$sql = $sql->row();
				$createdByName = isset($sql->full_name) ? $sql->full_name : '';
				
				//EDITED BY
				$sql = $CI->db->query("SELECT full_name FROM admin_users WHERE id = $adminLastEditorID LIMIT 1");
				$sql = $sql->row();
				$lastEditedByName = isset($sql->full_name) ? $sql->full_name : '';
				
				
				echo '<div class="fl" style="color:#bb4041;font-weight:bold;margin-left:3px;margin-top:25px;">
					  <table style="margin-top:-10px;">
						<tr>
							<td style="text-align:left;font-size:12px;">CREATED BY: 	   '.$createdByName 	.'</td>
							<td style="text-align:left;font-size:12px;">LAST EDITED BY:    '.$lastEditedByName	.'</td>
						<tr>
						<tr>
							<td style="text-align:left;font-size:12px;">DATE CREATED:  	   '.$DateAdded			.'</td> 	   	
							<td style="text-align:left;font-size:12px;">DATE LAST EDITED:  '.$DateLastEdited	.'</td> 		
						</tr>
					  </table>
					</div>';
					echo "<input name='status' value='$status' type='hidden'>";
				} 
				
			?> 
			<div class="clear"></div>
			<div class="fl" style="margin-top:20px;margin-bottom:10px;margin-left:20px;">
					<div class="button-content1b" id='tab2'>
						<a href="#" onclick='showcommiteeDiv()'><h2>NOMINATION COMMITTEE</h2></a>
					</div>
					
					<div id='loading' class='fl'>
						<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> Checking email address...
					</div>
					
					<div id='loading2' class='fl' style='margin-top:20px;display:none;'>
						<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> Please wait while the campaign is being save... 
					</div>
					
					<div id='loading3' class='fl' style='margin-top:20px;display:none;'>
						<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> Please wait while the campaign is being save. Email notification will be sent to those included in the list... 
					</div>
			</div>
			<div style='clear:both;'></div>
			<br/>
				<div id='loadItems' title='CAMPAIGN ITEMS: <?php echo count($items) ?>' style='display:none;'>			
				<!--ITEMS PAGE-->
				<?php
					function cutStr($itemName=''){
						if(strlen($itemName)>=15)
							return substr($itemName,0,11)."...";
						else	
							return $itemName; 
					}
					
					$x=0;
					//print_r($items);
					$iType=""; $pRange=""; 
					foreach($items as $d){
						extract($d);
						if($iType!=$POSM_TypeName) echo "<h5 style='clear:both;text-align: left;background: #bb1d1d;margin: 0;color: #fff;padding: 2px 10px;'> $POSM_TypeName  </h5>";
						if($pRange!=$extra_label) {
						$x=0;
						echo "<div style='clear:both;text-align: left;background: #d3d3d3;;margin: 0;color: #fff;padding: 2px 10px;color:black;width:96%;margin-left:6px;margin-top: 5px;font-size:13px;'> $extra_label  </div>";
						}
						//$itemID = $id;
						//GET FIRST ITEM IMAGE
						$sql = $CI->db->query("SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = $itemID LIMIT 0,1");
						$item_img = $sql->result_array();
						extract($item_img);
						$item_img = isset($item_img[0]['image']) ? $item_img[0]['image'] : 'blank.png';
						$img_path = HTTP_PATH."img/small/$item_img";
						$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$itemID"; 
						$POSM_TypeName  = isset($POSM_TypeName) ? $POSM_TypeName:"";	
						$x++;
					echo "<a href='$itemPreviewLink' target='_blank' class='itemLink'>
						  <div style='width:160px;height:150px;position:relative;margin-top:-8px;margin: 10px 10px 0px 12px;' class='fl'>
							<div style='border: 1px solid #f7cb60;text-align:center;padding:5px;height:100px;'>
								<img src='$img_path' class='itemIcon'>
							</div>
							
							<p style='font-size:12px;text-align:center;border: 1px solid #f7cb60;padding-bottom:1px;margin-top: -1px;' title='$itemName'> 
								<label style='width: 10px;height: 10px;position: absolute;font-size: 13px;padding: 1px;left: 6px;'>$x.</label> <b>". cutStr($itemName) ."</b> 
							</p>
						 </div>
						 </a>";
						$iType=$POSM_TypeName;
						$pRange=$extra_label;
					}
					if(count($items)==0)
						echo "<label>*Sorry there are no avaialble items for this campaign.</label>"
					?>
				<!--ITEMS PAGE-->
				</div>
				
			<div id='committeeDiv' style='display:block;padding-right:10px;margin-top:-43px;'>
				<!---COMMITEE FORM-->
				
				<div style='clear:both;'></div>
				<table border="0" style="display:block;padding-right:10px;margin-top:10px;">
					<tr>
						<td class="td-left" width='20%' style='padding:3px'>
							<h2 class="form"> FIRST NAME </h2>
							<div class="fill-up-box" style="width:95%;"> 
								<input name="committeeFname" style="width:98%;" type="text" class="fill-input" id='committeeFname'>    
							</div>
						</td>
						<td class="td-left" width='20%' style='padding:3px'>
							<h2 class="form"> LAST NAME </h2>
							<div class="fill-up-box" style="width:95%;"> 
								<input name="committeeLname" style="width:98%;" type="text" class="fill-input" id='committeeLname'>    
							</div>
						</td>
						<td class="td-left" width='10%' style='padding:3px'>
							<h2 class="form"> GENDER </h2>
							<div class="fill-up-box" > 
								 <select name='selgender' id='selgender' style="width:95%;font-size: 12px;" placeholder='gender'>   
								    <option value='Male'>Male</option>
								    <option value='Female'>Female</option>
								 </select>   
							</div>
						</td>
						<td class="td-left" width='15%'>
							<h2 class="form"> DEPT. </h2>
							<div class="fill-up-box" style="width:95%;">   
								<select name='departmentName' id='departmentName' style="width:100%;font-size: 11px;" placeholder='Department'> 
								 <?php 
									foreach($departments->result_array() as $d) 
									{ 
									 $v = $d['department_name'];
									 $t = $d['department_name'];
									 echo "<option value='$v'> $t </option>";   
									}  
								  ?>
								</select> 
							</div>
						</td>
						<td class="td-left" width='25%' style='padding:3px'>
							<h2 class="form"> EMAIL </h2>
							<div class="fill-up-box" style="width:100%;"> 
								<input name="committeeEmail" type="text"  style="width:98%;font-size:13px;" class="fill-input" id='committeeEmail'>    
							</div>
						</td>
						<td class="td-left" width='15%'>
							<h2 class="form"> BIRTH YEAR </h2>
							<div class="fill-up-box" style="margin-left: 5px;width:95%;"> 
								<select name='yearOfBirth' id='yearOfBirth' style="width:100%;font-size: 12px;">
								<?php
									$j=date('Y');
									for($i=date('Y');$i!=1899;$i--){
										echo "<option value='".$i."'>".$j--."</option>";
									}	
								?>
								</select>    
							</div>
						</td>
						<td style='padding-left:10px;text-align:left;padding-top:47px;padding:5px;vertical-align:center;'   width='15%'>
							<br/><span class="nav-REMOTE-btn1" style="padding:10px;color:white;cursor:pointer;margin-top:5px;" onclick='addEmail()'>ADD</span>
						</td>
					</tr>
				</table>
				<div style='margin-left:16px;margin-bottom:-15px;width:150%;'>
					<?php //echo $CI->forms->form_fields2('file','userfile','','UPLOAD .CSV FILE','r'); ?> 
				</div>	
				<br/>
				<!---COMMITEE FORM-->
			
			
				<!--NOMINATION COMMITEE-->
				<div class="clear"></div>
				<div id='nominationCommitteDiv'>
					<table id='emailsTable' cellpadding="0" cellspacing="0" style="width:100%;text-align:left;margin-top:10px;text-align:center;clear:both;">
					<tr>
						<th style="width:120px;text-align:center;"> F.NAME 				</th> 
						<th style="width:120px;text-align:center;"> L.NAME 				</th> 
						<th style="width:10px;text-align:center;">  GENDER 				</th> 
						<th style="width:30px;text-align:center;">  DEPT.  		        </th> 
						<th style="width:250px;text-align:center;"> EMAIL 				</th> 
						<th style="width:10px;text-align:center;">  YEAR 				</th> 
						<th style="width:45px;text-align:right;">  ACTION 				</th> 
					</tr>
					</table>
					<?php
						$emailCtr = 1;
						$x=0;
						if(isset($admin_users) AND !isset($alter)) {
							foreach($admin_users as $au)
							{
								$c = (($x++)%2) != 0 ? "" : "style='background:#f9ebeb;'"; 
								extract($au);
								echo "<div id='emailCtr".$emailCtr."' $c class='emailClass' style='width:100%'>
								 <table>
									<tr>
										<td style='padding: 0 0 0 15px;'> 
										   <input type='hidden' name='voterTypes[]' 	value='2'>
										   <input style='width:90%;margin-bottom: 0;' type='text' value='$fname' 	    	name='fnames[]'       readonly='readonly' > 
										</td>
										<td style='padding:3px;'><input type='text' style='width:114%;margin-bottom: 0;' value='$lname' 	    	name='lnames[]'       readonly='readonly' ></td>
										<td style='padding:3px;text-align: right;width: 117px;'>
											<input type='text' style='width:59%;margin-bottom: 0;' value='$gender' 	    	name='gender[]'       readonly='readonly' >
										</td>
										<td style='padding:3px;width: 145px;'><input type='text' style='width:92%;margin-bottom: 0;' value='$department'  	name='departments[]' readonly='readonly'> </td> 
										<td style='padding:3px;width: 253px;'><input type='text' style='width:116%;margin-bottom: 0;' value='$email_address'  		    class='emails'   name='emails[]' 	 readonly='readonly'> </td> 
										<td style='padding:3px;text-align: right;'><input type='text' style='width:51%;margin-bottom: 0;' value='$year_of_birth'  			name='years[]' 		 readonly='readonly' > </td> 
										<td style='padding:3px;text-align: center;'><img onclick='removeEmail(\"emailCtr".$emailCtr++."\")' style='margin: 0 0 0 5px;padding-top: 4px;cursor:pointer' src='".HTTP_PATH."img/delete.png' title='Delete' class='fl'>  ";
										if($status=="on progress" AND $votingStatus=='invited' AND isset($ALTER_CAMPAIGN)){
											echo "<img src='".HTTP_PATH."img/re-send-failed.png' onclick='resendEmail(\"$campaignID\",\"$voterID\")' class='fl' style='cursor:pointer;' title='Resend email'>";
										}elseif(!isset($ALTER_CAMPAIGN)){ 
											echo "<img src='".HTTP_PATH."img/re-send-failed.png' class='fl' style='cursor:pointer;' title='Resend email'>";
										}
										if($status=="on progress" AND $votingStatus=='done'){
											echo "<img src='".HTTP_PATH."img/re-send-ok.png' class='fl' title='Done' >";
										}
								echo " </td>
									</tr>
								</table>
								</div>";
							} 
						}else{
						echo "<table style='width: 100%;'>";
							foreach($admin_users as $au)
							{
								$c = (($x++)%2) == 0 ? "" : "style='background:#f9ebeb;text-align:left;'"; 
								extract($au);
								echo "<tr $c> 
										<td style='text-align: left;width: 15%;'>  $fname 	 	</td>
										<td style='text-align: left;width: 20%;'> $lname 	 	</td>
										<td style='text-align: center;width: 7%;'> $gender 	 	</td>
										<td style='text-align: center;width: 10%;'> $department 	</td>
										<td style='text-align: left;width: 32%;'> $email_address </td>
										<td style='text-align: right;width: 2%;'> $year_of_birth </td>
										<td> - </td>
										</tr>";
							} 
						echo "</table>";
						}
					 ?>
					
				</div>
				<!--NOMINATION COMMITEE-->
			</div>
        </div>
        <div class="clear"></div>
    </div>

	
   <?php 
	   if($status!='on progress' and $status!='done' and $_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0 AND !isset($alter)) 
		echo $CI->forms->buttons('StopPar','vendorFORM');
	   
	   if($status!='on progress' and $status!='done' and $_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0 OR isset($alter)){
		   $field  = "<div class='fl'>";
		   $field  .= "<input name='btnsubmit1' onclick='SavePublish()' type='button' class='nav-REMOTE-btn1 fl' value='Publish Campaign' style='color:white;margin-top:20px;'> <br/><br/> ";
		   $field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:260px;margin-left:5px;'> *OK - Save all changes and Publish Campaign</p>";
		   echo $field	.= "</div>";	
	   }
	   echo "</form>";
	?>
	
	<?php 
		function generate_iLikeRules($rules,$voting_rules,$canvassing_rules,$min_committees)
		{ 
			echo "<table cellpadding='0' cellspacing='0' border=1 style='width:614px;margin: 0px auto;font-size:12px; display:none;position:absolute;background-color:white;left:47%;' class='fr iLike_Result_Table' id='iLike_Rule_Table'>
					<tr><td colspan='6' style='text-align:left'><b>Campaign Rules</b>  <a onclick='campaignRules()' style='float: right;cursor: pointer;color: black;margin-right: 10px;'>(&#x2716; close)</a> </td></tr>
					<tr style='border-radius: 6px;font-size: 10px;'>
						<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d' colspan='2'>    <b>Field Name  </b></td> 
						<td style='width:30px;text-align:center;color:white;' bgcolor='#bb1d1d'  colspan='2'>    <b>Operator    </b></td>    
						<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d' colspan='2'>    <b>Value       </b></td> 
										  
					</tr>";
			$x = 0;
			$invalidRules=0;
			//print_r($rules);			
			foreach($rules as $r){
					extract($r);
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
				echo "<tr>
					  <td  $c  style='text-align:left;'   colspan='2'>	 $fieldValue  			</td>
					  <td  $c  style='text-align:center;' colspan='2'>	  $rel  					</td>
					  <td  $c  style='text-align:center;' colspan='2'>	  $val  					</td> ";
				echo "</tr>";
			}
			
			echo "<tr><td colspan='6' style='text-align:left'><b>Voting Rules</b></td></tr>
					<tr style='border-radius: 6px;font-size: 10px;'>
						<td style='width:179px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Item Type  				</b></td> 
						<td style='width:361px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Price Category  			</b></td> 
						<td style='width:548px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Voting Condition  		</b></td> 
						<td style='width:237px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Min. No. of Items   		</b></td> 
						<td style='width:160px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Current Items    		</b></td> 
						<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Status    				</b></td> 
					</tr>";
			//print_r($voting_rules);
			$TOTALcurrent_num_items=0;
			foreach($voting_rules as $r){
				extract($r);
				$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
				echo "<tr>
					  <td  $c  style='text-align:left;'>	  $fieldValue  				</td>
					  <td  $c  style='text-align:left;'>	  $price_rangeName  		</td>
					  <td  $c  style='text-align:center;'>	  $cond1 $min_val $logical_operator $cond2 $max_val  </td>
					  <td  $c  style='text-align:center;'>	  <b>$min_number_of_items</b>  	</td> 
					  <td  $c  style='text-align:center;'>	  $current_num_items  		</td> 
					  <td  $c  style='text-align:center;'>	  $stat  	</td> ";
				echo "</tr>";
				$TOTALcurrent_num_items+=$current_num_items;
				if($stat=='Not Good')
					$invalidRules++;
			}
			
			echo "<tr> <td colspan='1'><b>Total</b></td> <td colspan='3'></td> <td><b>$TOTALcurrent_num_items</b></td><td colspan='1'></td></tr>";
			
			echo "<tr><td colspan='6' style='text-align:left'><b>Canvassing Rules</b></td></tr>
					<tr style='border-radius: 6px;font-size: 10px;'>
						<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d' colspan='2'>   <b>Item Type  			</b></td> 
						<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d' colspan='1'>   <b>Price Category  		</b></td> 
						<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d' colspan='3'>   <b>Canvassing Condition </b></td> 
					</tr>";
			foreach($canvassing_rules as $r){
				extract($r);
				$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
				echo "<tr>
					  <td  $c  style='text-align:left;' colspan='2'>	  $fieldValue  		</td>
					  <td  $c  style='text-align:left;' colspan='1'>	  $price_rangeName  </td>
					  <td  $c  style='text-align:center;' colspan='3'>	  $cond1 $min_val $logical_operator $cond2  $max_val 					</td>
					  ";
				echo "</tr>";
			}
			
			echo "<tr>
					<td colspan='6' style='text-align:left;'>Minimum Number of Nominees: <b>$min_committees </b></td>
				</tr>";
			
			//SECRET ELEMENT
			echo "<input type='hidden' value='$invalidRules' name='invalidRules' id='invalidRules'>";
			
			if(!$rules)
				echo "<tr><td colspan='9'>Sorry no campaign rules found.</td></tr>";
			
			echo "</table>";
		}
	?>
	
	
	<script type="text/javascript">
	function resendEmail(campaignID,voterID)
	{
		jConfirm('Resend email notification?','Alert',function(r){
			if(r){ var stat = $.ajax({
					url: '<?php echo HTTP_PATH ?>iLikeCampaign/resend_Email/'+campaignID+'/'+voterID,
					async: false
				}).responseText;
			
			if(stat=='ok')
				jAlert('Email has been sent!', 'Success');
			else
				jAlert('Sorry email not send check campaign duration.', 'Alert');
				
			}
		});
	}

	
	$("#vendorFORM").bind("keypress", function (e) {
		if (e.keyCode == 13) {
			return false;
		}
	});
	
	function campaignRules()
	{
		if(document.getElementById('iLike_Rule_Table').style.display=='none')
			document.getElementById('iLike_Rule_Table').style.display='block';
		else
			document.getElementById('iLike_Rule_Table').style.display='none';
	}
	
	/*CONFIRM IF WANT TO SAVE*/
	function SavePublish()
	{
		//CHECK IF VALID DATE
		DateFrom 	 = document.getElementById('DateFrom').value;
		DateTo 	 	 = document.getElementById('DateTo').value;
		invalidRules = document.getElementById('invalidRules').value;
		
		var a = $.ajax({
			url: '<?php echo HTTP_PATH ?>iLikeCampaign/check_date/'+DateFrom+'/'+DateTo,
			async: false
		}).responseText;
		
		var committee_checker = "ok";
		var iIndex = $('.emails').length;
		<?php 
		if($status=="new" OR $status=="updated"){ ?>
		//CANVASSING RULES
		 if(<?php echo $min_number_of_committees ?>>iIndex){ committee_checker='not'; }
		<?php } ?>
		
		//alert(committee_checker);
		jConfirm('OK - Save all changes and Publish Campaign?','Alert',function(r){		
			if(r){
				if(a=='good' & committee_checker=='ok')
				{
					//HAS COMMITTEES
					<?php if(!isset($alter)){ ?>
					if(iIndex < <?php echo $min_committees ?>)
					{
						jAlert('Sorry cannot publish campaign minimum number of nomination committees doesn\'t meet.');
					}else{
						if(invalidRules>0){
							jAlert('Sorry cannot publish campaign Voting Rules has not been meet, Please check all items.');
						}else{
							document.getElementById('loading3').style.display = 'block';
							$("#vendorFORM").parsley("validate");
							document.getElementById("vendorFORM").action = "<?php echo HTTP_PATH.'iLikeCampaign/publishCampaign/'.$campaignID; ?>";
							$("#vendorFORM").submit();
						}
					}<?php 
					} else { ?>
						document.getElementById('loading2').style.display = 'block';
						$("#vendorFORM").parsley("validate");
						document.getElementById("vendorFORM").action = "<?php echo HTTP_PATH.'iLikeCampaign/alter/'.$campaignID; ?>";
						$("#vendorFORM").submit();
					<?php } ?>
				}
				else if(committee_checker=='not')
				{
				  jAlert('Sorry cannot be publish, canvassing rules did not meet.');
				}
				else{
					jAlert('Sorry cannot be publish, double check date parameters or check the campaign rules.');
				}
			}
		});
		
	}
	
	$('#mask').click(function(e)
	{
		$('#mask').hide();
		$('#itemsList').hide();
	});
	$('.close2').click(function(e)
	{
		$('#mask').hide();
		$('#itemsList').hide();
	});
   
	function loadItems()
	{
		$( "#loadItems" ).dialog({modal: true,height: 600,
		width: 950});
	}
 
	function cleariLikeSessionItems(obj,url)
	  {
		jConfirm('Are you sure you want to quit iLike Campaign Form?','Alert',function(r){
			if(r) window.location = url;
		});
	  }
 
	function StopPar()
	{
		//document.getElementById('publishInput').value = 'n';
		document.getElementById('loading2').style.display = 'block';
		$('#vendorFORM').parsley().destroy();
	}
 
	function lostFocus(obj)
	{
		obj.setAttribute('readonly');
	}
	
	function enable(obj)
	{
		obj.removeAttribute('readonly');
	}
	
	var origEmails='';
	function gotFocusEmail(obj) {origEmails=obj.value}
	function checkListEmail(obj)
    {
	     var iIndex = document.getElementsByClassName('emails').length;
		 var emails = document.getElementsByClassName('emails');
		 var iE=0;
		 for(i=0;i<iIndex;i++)
		  {
		    if(emails[i].value==obj.value)  iE++;
		  } 
		  if(iE>1) {jAlert( obj.value +" Already in the list. "); obj.value=origEmails; }
		 
    }
	
	function validateYear(birth_of_year)
	{
		var currentYear = <?php echo date('Y') ?>
		
		if((currentYear - birth_of_year)<=17)
			return true;
		else
			return false;
	}
	
	function validateStr(stringf)
	{
		if (/[^a-zA-Z0-9\- ]/.test( stringf ))
			return true;
		else
			return false;
	}
	
	function addEmail()
	{
		document.getElementById('loading').style.display = 'block';
		
		var fname 	   	  = document.getElementById('committeeFname').value;
		var lname 	   	  = document.getElementById('committeeLname').value;
		var gender 	   	  = document.getElementById('selgender').value;
		var email 	   	  = document.getElementById('committeeEmail').value;
		var department 	  = document.getElementById('departmentName').value;
		var birth_of_year = document.getElementById('yearOfBirth').value;
		var msg = '';
		var iIndex = $('.emails').length;
		var emails = $('.emails');
		
		//if(iIndex >= <?php echo $min_committees ?>)
		//	msg += "Sorry, Number of committes must be <?php echo $min_committees ?> only. \n";
		
		for(i=0;i<iIndex;i++)
		{
		   if(emails[i].value==email)  msg += email +" Already in the list. ";
		}
		
		if(fname=='') 			 	 	  msg += 'Enter first name \n';
		if(validateStr(fname)==true)      msg += 'First name can only contain alphanumeric characters and space\n';
		if(lname=='') 			 	      msg += 'Enter last name \n';
		if(validateStr(lname)==true)      msg += 'Last name can only contain alphanumeric characters and space\n';
		if(email=='') 			 	 	  msg += 'Enter email \n';
		if(department=='')  	 	 	  msg += 'Enter department \n';
		if(validateStr(department)==true) msg += 'Department can only contain alphanumeric characters, space and hypehns\n';
		if(birth_of_year=='') 	 		  msg += 'Enter Birth of year \n';
	    if(validateEmail(email)) 		  msg += 'Enter a valid email \n';
	
		//VALIDATE BIRTH YEAR
		if(validateYear(birth_of_year)==true)  msg += 'Under age committee must be 18 yrs old and above \n';
		
		//GET THE NUMBER OF TRs
		var numTr = (document.getElementById('emailsTable').getElementsByTagName('tr').length);
		
		if(msg=='')
		{ 
		  //IF VOTED
		   var a = $.ajax({
			url: '<?php echo HTTP_PATH ?>iLikeCampaign/lastVoted/'+email,
			async: false
		   }).responseText;
		   
		  if(a=='existing')
			msg += "User already voted, please wait another year";
				
			if(msg=="")
			{
				document.getElementById('loading').style.display = 'none';
				
				var my_email = $.ajax({
				url: '<?php echo HTTP_PATH ?>iLikeCampaign/generateEmailTD/'+ fname +'/'+ lname + '/'+ gender +'/'+email+'/'+department+'/'+birth_of_year+'/'+iIndex,
				async: false
			   }).responseText;
				
				var divKo = document.getElementById("nominationCommitteDiv");
				divKo.insertAdjacentHTML('beforeend',my_email);
				
				document.getElementById('committeeFname').value = '';
				document.getElementById('committeeLname').value = '';
				$('#selgender').prop('selectedIndex',0);
				document.getElementById('committeeEmail').value = '';
				$('#departmentName').prop('selectedIndex',0);
				$('#yearOfBirth').prop('selectedIndex',0);
			}else{
			   document.getElementById('loading').style.display = 'none';
			   jAlert(msg);
			}  
		}
		else 
		{ 
			document.getElementById('loading').style.display = 'none';
			jAlert(msg); 
		}	
	}
	
	
	function validateEmail(email)
	{
		var filter=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		var filter2=/--/i;
		 
		if(email=='') return true;
		if (filter2.test(email))
			return true;
		if (filter.test(email)) 
			return false;
		else
			return true;
	}
	
	
	function removeEmail(id)
	{
		jConfirm('Are you sure you want to remove this field?',"Alert",function(r){
			if(r){		
				var td_row = $("#"+id);
				td_row.remove();
				
				var TRlength =  $('.emailClass').length;
				var TRs 	 =  $('.emailClass');
				
				//alert(TRs);
				function outerHTML(node){
					return node.outerHTML || new XMLSerializer().serializeToString(node);
				}
				
				for (var index = 1; index < TRs.length; index++) {
					//alert(outerHTML(TRs[index]));
				}
				
				for(var i=1; i<TRlength; i++)
				{
					if(i%2==0)
					{ 	
						TRs[i].style.background = '#f9ebeb'; 
					}else{
						TRs[i].style.background = '#ffffff'; 
					}
				}
			}
		});
	}
	

	</script>	
	
	
	