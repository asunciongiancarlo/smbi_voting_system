	<div class="content">
    	<div class="title-content" style='width:100%;'>
        	<h2 class='fl'><?php 
				if($PUBLISH_CAMPAIGN) 
					echo "<span style='text-transform:lowercase;'>i</span>WANT CAMPAIGN </h2>";
				else
					echo "EDIT <span style='text-transform:lowercase;'>i</span>WANT COMMITEE </h2>";
				?>
			<h2  style='text-decoration:underline;cursor:pointer;font-size:14px;margin-top:0px;float:right;float: right;margin-right: 45px;'>
				<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
			</h2>
			<?php if($PUBLISH_CAMPAIGN){ ?>
			<h2 style='text-decoration:underline;cursor:pointer;font-size:14px;margin-top:0px;float:right;float: right;margin-right: 20px;' onclick="loadItems('show')">ALL ITEMS</h2>
			<h2  style='text-decoration:underline;cursor:pointer;font-size:14px;margin-top:0px;float:right;float: right;margin-right: 20px;' onclick="campaignRules()">CAMPAIGN RULES</h2>
			<?php } ?>
			
        </div>
        <div class="clear"></div>
        <div class="working_area">
			<?php
				$CI =& get_instance();
				
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}
				
				//COUNTRIES
				if($_SESSION['countryID']==0){
					$Csql = "SELECT country.id as cID, countryName FROM country ORDER BY cID ASC";
				}else{
					$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id=".$_SESSION['countryID'];
				}	
				$country = $CI->db->query($Csql);
				
				$departments = "SELECT department_name FROM voters_department ORDER BY department_name ASC";
				$departments = $CI->db->query($departments);
				
				//ACTION STATEMENT
				$action = HTTP_PATH ."iWantCampaign/iWant/insert";
				
				//GET COUNTRY
				$sql = $CI->db->query("SELECT COUNT(id) as iwant_ctr FROM campaign WHERE campaignType='iWant'");
				$row = $sql->row(); 
				$iwant_code = iWant_code(($row->iwant_ctr)+1);
				
				
				$campaignName   = date('Y-m-d')."_iWant Campaign_$iwant_code" ; 
				$DateAdded 	    = ''; 
				$DatePublished 	= ''; 
				$DateFrom 		= ''; 
				$DateTo 		= ''; 
				$campaignID 	= 0;
				$status 		= 'new';
				$BU_Notified 	= 'n';
				$loading3MSG	= 'Please wait while the campaign is being save. Email notification will be sent to Business Units...';
				
				//ISSET ID
				if(isset($id))
				{
					$action = HTTP_PATH ."iWantCampaign/iWant/update/".$id;
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
					$BU_Notified 		= $sql[0]['BU_Notified']; 
					$campaignID			= $id;
					$loading3MSG	= 'Please wait while the campaign is being save...';
				}
				
				//REVOTE
				if(isset($revote))
					$action = HTTP_PATH ."iWantCampaign/iWant/insert";
				
				//ALTER
				if(isset($alter))
					$action = HTTP_PATH ."iWantCampaign/alter/".$campaignID;
			?>
			<div class="container" style='width:98%;'>
			<?php 
				$CI =& get_instance();
				$CI->load->library('forms');
				$CI2 =& get_instance();
				$CI2->load->library('fv');

				echo $CI->forms->form_header('SMBi','vendorFORM',$action);
				if($PUBLISH_CAMPAIGN) {
				echo '<div class="fl" style="width:21%; margin-right:86px;">
						<h2 class="form"> CAMPAIGN NAME </h2>';
				echo '<input name="campaignName" value="'.$campaignName.'" type="text" readonly data-parsley-trigger="change" data-parsley-required="true" placeholder="Required field" style="width: 130%;">';
				echo '</div>';
				}		
				
				//VOTING RULES
				generate_iLikeRules($voting_rules);
				
				echo "<input type='hidden' name='status' value='$status'>";
				
			    if($PUBLISH_CAMPAIGN){
				echo $CI->forms->form_fields2('text_short','DateFrom',$DateFrom,$CI2->fv->label(44),'DateFrom','');
				echo $CI->forms->form_fields2('text_short','DateTo',$DateTo,$CI2->fv->label(45),'DateTo','');
				}
				
				//REVOTE
				if(isset($revote)){
					echo "<input type='hidden' name='prevCampaignID' value='$campaignID'>";
				}
				
				//iLikeCampaignID
				if(isset($iLikeID)){
					echo "<input type='hidden' name='iLikeID' value='$iLikeID'>";
				}
				
				
				if(isset($id) AND $PUBLISH_CAMPAIGN)
				{
				//CREATED BY
				$sql = $CI->db->query("SELECT full_name FROM admin_users WHERE id = $adminCreatorID LIMIT 1");
				$sql = $sql->row();
				$createdByName = isset($sql->full_name) ? $sql->full_name : '';
				
				//EDITED BY
				$sql = $CI->db->query("SELECT full_name FROM admin_users WHERE id = $adminLastEditorID LIMIT 1");
				$sql = $sql->row();
				$lastEditedByName = isset($sql->full_name) ? $sql->full_name : '';
				}
			?>	
			<div style='clear:both;'></div>
			<div class="fl" style="margin-top:20px;margin-bottom:10px;margin-left:20px;">
				<label style='margin: -17px 0px 3px -19px;'> Minimum No. of Screening Committees: <b><?php echo $min_committees ?>/Business Unit</b></label>
				<div class="button-content1b" id='tab2'>
					<a href="#" onclick='showcommiteeDiv()'><h2>SCREENING COMMITEE</h2></a>
				</div>
			</div>
			<?php	
				if(isset($id) AND $PUBLISH_CAMPAIGN)
				{
				echo '<div class="fl" style="color:#bb4041;font-weight:bold;margin-left:21px;margin-top:29px;">
					  <table style="margin-top:-29px;">
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
			<div style='clear:both;'></div>
				<div id='loading' class='fl'>
						<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> Checking email address...
				</div>
				
				<div id='loading2' class='fl' style='margin-top:0px;display:none;'>
					<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> Please wait while the campaign is being save. Email notification will be sent to those included in the list ...
				</div>
				
				<div id='loading3' class='fl' style='margin-top:0px;display:none;'>
					<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> <?php echo $loading3MSG ?>
				</div>		
				<!--ITEMS PAGE-->
				<?php 
				if($PUBLISH_CAMPAIGN){ 
					previewItems($items,$CI,$EDIT,$rep,$edit_items); } ?>
				<!--ITEMS PAGE-->
				
			<div id='committeeDiv' style='display:block;margin-top:10px;'>
				<!---COMMITEE FORM-->
				
				<div style='clear:both;'></div>
				<table border="0" style="margin-top:-35px;margin-bottom:22px;width:100%">
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
								<input name="committeeEmail" type="text"  style="width:98%;font-size:12px;" class="fill-input" id='committeeEmail'>    
							</div>
						</td>
						<td class="td-left" width='15%'>
							<h2 class="form"> BIRTH YEAR </h2>
							<div class="fill-up-box" style="width:95%;"> 
								<select name='yearOfBirth' id='yearOfBirth' style="width:96%;font-size: 12px;">
								<?php
									$j=date('Y');
									for($i=date('Y');$i!=1899;$i--){
										echo "<option value='".$i."'>".$j--."</option>";
									}	
								?>
								</select>    
							</div>
						</td>
						<td class="td-left" width='30%' style='padding:3px'>
							<h2 class="form"> COUNTRY </h2>
							<div class="fill-up-box" style="margin-left: -3px;width:113%;">
								 <select name='selcountry' id='selcountry' style="width:100%;font-size: 12px;" placeholder='country'> 
								 <?php 
									foreach($country->result_array() as $o) 
									{ 
									 $v = $o['cID'];
									 $t = $o['countryName'];
									 echo "<option value='$v'> $t </option>";   
									}  
								  ?>
								  </select> 								   
							</div>
						</td>
						<td style='padding-left:10px;text-align:left;padding-top:47px;padding:5px;vertical-align:center;'   width='15%'>
							<br/><span class="nav-REMOTE-btn1" style="padding:10px;color:white;cursor:pointer;margin-top:5px;margin-left: 20px;" onclick='addEmail()'>ADD</span>
						</td>
					</tr>
				</table>
				<div style='margin-left:16px;margin-bottom:-15px;width:150%;'>
					
				</div>	
				<!---COMMITEE FORM-->
			
				<!--NOMINATION COMMITEE-->
				<div class="clear"></div>
				<div id='nominationCommitteDiv'>
					<table id='emailsTable' cellpadding="0" cellspacing="0" style="width:100%;text-align:left;margin-top:10px;text-align:center;background-color:white;">
					<tr>
						<th style="width:120px;text-align:center;"> F.NAME 				</th> 
						<th style="width:10px;text-align:center;"> L.NAME 				</th> 
						<th style="width:98px;text-align:right;">  GENDER 				</th> 
						<th style="width:30px;text-align:right;">  DEPT.  		        </th> 
						<th style="width:250px;text-align:center;"> EMAIL 				</th> 
						<th style="width:58px;text-align:right;">  YEAR 				</th> 
						<th style="width:10px;text-align:right;">  COUNTRY 			</th> 
						<th style="width:45px;text-align:right;">  ACTION 				</th> 
					</tr>
					</table>
					<?php
						$emailCtr = 1;
						$x=0;
						if(isset($admin_users) AND !isset($alter)) {
							foreach($admin_users as $au)
							{
								$c = (($x++)%2) == 0 ? "" : "style='background:#f9ebeb;'"; 
								extract($au);
								echo "<div id='emailCtr".$emailCtr."' $c class='emailClass' style='width:100%'>
								 <table>
									<tr>
										<td style='padding: 0 0 0 15px;width: 103px;'> 
										   <input type='hidden' name='voterTypes[]' 	value='2'>
										   <input style='width:90%;margin-bottom: 0;' type='text' value='$fname' 	    	name='fnames[]'       readonly='readonly'> 
										</td>
										<td style='padding:3px;'><input type='text' style='width:114%;margin-bottom: 0;' value='$lname' 	    	name='lnames[]'       readonly='readonly'></td>
										<td style='padding:3px;text-align: right;width: 117px;'>
											<input type='text' style='width:59%;margin-bottom: 0;' value='$vGender' 	    	name='genders[]'       readonly='readonly'>
										</td>
										<td style='padding:3px;width: 145px;'><input type='text' style='width:92%;margin-bottom: 0;' value='$department'  	name='departments[]' readonly='readonly'> </td> 
										<td style='padding:3px;width: 253px;'><input type='text' style='width:110%;margin-bottom: 0;' value='$email_address'  		    class='emails'   name='emails[]' 	 readonly='readonly'  onclick='gotFocusEmail(this)' onBlur='checkListEmail(this);'> </td> 
										<td style='padding:3px;text-align: right;'><input type='text' style='width:51%;margin-bottom: 0;' value='$year_of_birth'  			name='years[]' 		 readonly='readonly'> </td> 
										<td style='padding:3px;'><input type='text' style='width:89px;margin-bottom: 0;'value='$countryName' 	 name='countryName[]' 		  	 readonly='readonly'> 
										<input type='hidden' name='countryIDs[]' value='$countryID' class='countryID'></td>  
										<td style='padding:3px;text-align: center;'>";
										if($status!='on progress' AND $status!='failed')
											echo "<img onclick='removeEmail(\"emailCtr".$emailCtr++."\")' style='margin: -10px 17px -20px 9px;padding-top: 4px;cursor:pointer' src='".HTTP_PATH."img/delete.png' title='Delete' class='fl'>  ";
										if($status=="on progress" AND $votingStatus=='invited' AND isset($ALTER_CAMPAIGN)){
											echo "<img src='".HTTP_PATH."img/re-send-failed.png' onclick='resendEmail(\"$campaignID\",\"$voterID\")' class='fr' style='cursor:pointer;' title='Resend email'>";
										}elseif(!isset($ALTER_CAMPAIGN)){ 
											echo "<img src='".HTTP_PATH."img/re-send-failed.png' class='fr' style='cursor:pointer;' title='Resend email'>";
										}
										if($status=="on progress" AND $votingStatus=='done'){
											echo "<img src='".HTTP_PATH."img/re-send-ok.png' class='fr' title='Done'>";
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
								echo"<tr $c> 
										<td style='text-align: left;width: 15%;'>  $fname 	 	</td>
										<td style='text-align: left;width: 20%;'> $lname 	 	</td>
										<td style='text-align: left;width: 7%;'> $vGender 	 	</td>
										<td style='text-align: left;width: 10%;'> $department 	</td>
										<td style='text-align: left;width: 32%;'> $email_address </td>
										<td style='text-align: right;width: 2%;'> $year_of_birth </td>
										<td style='text-align: left;width:  25%;'> $countryName </td>
										<td style='width: 12%;'> - </td>
										</tr>
									";
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

	   if(($status=='on progress' AND $_SESSION['super_admin']!='y') AND (isset($alter) AND $PUBLISH_CAMPAIGN)){
		   $field   = "<div class='fl'>";
		   $field  .= "<input name='btnsubmit2' onclick='SavePublish()' type='button' class='nav-REMOTE-btn1 fl' value='Publish Campaign' style='color:white;margin-top:20px;'> <br/><br/> ";
		   $field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:260px;margin-left:5px;'> *OK - Save all changes and Publish Campaign</p>";
		   echo $field  .= "</div>";
	   } 
   
	   if($status!='on progress' and $status!='done' and $status!='failed' and $_SESSION['super_admin']!='y' AND !isset($alter)){ 
		if($PUBLISH_CAMPAIGN){
			//echo $CI->forms->buttons('StopPar','vendorFORM');
		}
		if(!$PUBLISH_CAMPAIGN){
			echo $CI->forms->buttons('Save_iWant','vendorFORM');
		}else{
			$field   = "<div class='fl'>";
			$field  .= "<input name='Save_and_Notify' onclick='Save_Notify()' type='submit' class='nav-REMOTE-btn1 fl' value='Save as Draft' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:250px;margin-left:5px;'> *This will save all campaign information.</p>";
			echo $field  .= "</div>";
		}
	   }
	   
	   if(($status!='on progress' and $status!='done' and $_SESSION['super_admin']!='y') AND (isset($alter) OR $PUBLISH_CAMPAIGN)){
		   $field   = "<div class='fl'>";
		   $field  .= "<input name='btnsubmit2' onclick='SavePublish()' type='button' class='nav-REMOTE-btn1 fl' value='Publish Campaign' style='color:white;margin-top:20px;'> <br/><br/> ";
		   $field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:260px;margin-left:5px;'> *OK - Save all changes and Publish Campaign</p>";
		   echo $field  .= "</div>";
	   }   
	   echo "</form>";
	?>
	
	<?php function previewItems($items,$CI,$EDIT,$rep,$edit_items){ ?>
	
	<div id='iWantItemsDiv' style='display:none;'>	
		<label class='fl'><b><div id="iWant_items_ctr">Campaign Items: <?php echo count($_SESSION['iWant_items']) ?></div></b></label>
		<label class='fr' onclick="loadItems('hide')"><b>X Close</b></label>
		<div style='clear: both;'></div>
		
		<div style='/*background:#f9ebeb;*/'>
		<!--ITEMS PAGE-->
		<div class="container">
			<h2 class="fl resultLabel">iWant Items</h2>
			<h2 class="fr resultLabel2">Division Marketing Items</h2>
			
			<div id="iLikeResults" class="fl" style="margin-left:2px;">
			<?php	
			//print_r($_SESSION['iWant_items']);
			$lastItem="";
			$ctr=0;
			$iType="";
			$pRange="";
			foreach($_SESSION['iWant_items'] as $d){
				extract($d);
				$img_path = HTTP_PATH."img/small/$item_image";
				$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$itemID";
				
				//$w = w($item_image);
				$del_shortCut="";
				if($edit_items)$del_shortCut="<img onclick=\"delete_item($itemID)\" src='".HTTP_PATH."img/delete-item.png' style='margin-left:8px;cursor:pointer;'>";
				
				$title_css="";
			if($lastItem!=$countryName) echo "<h5 style='clear:both;text-align: left;background: #330404;margin-top: 0;color: #fff;padding: 2px 10px;margin-bottom: -5px;height: 27px;'> $countryName  </h5>";	
			$ctr++;
			echo "
				  <div style='width:120px;height:173px;margin: 10px 5px 24px 10px;;background:white;' class='fl'>
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
						<b style='padding-right:10px;'> ". $itemName." </b>  
					</p>
					 <br/>
				 </div>";
			$lastItem=$countryName;
			$iType   =$POSM_TypeName;
			}
			
			if(!$_SESSION['iWant_items']) echo "No items for iWant.";
			?>	
			</div>
			
			<div id="iLikeAllItems" class="fl">
			<?php
			$ctr=0; 
			$iType="";
			echo "<h5 style='clear:both;text-align: left;background: #330404;margin-top: 0;color: #fff;padding: 2px 10px;margin-bottom: -5px;height: 27px;'> Multi-Country  </h5>";
			foreach($rep as $d){
				extract($d);
				$img_path = HTTP_PATH."img/small/$item_image";
				$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$item_ID";
				$add_shortCut="";
				if($edit_items)
				   $add_shortCut="<img onclick='addItem($item_ID)' src='".HTTP_PATH."img/add-item.png' style='margin-left:21px;cursor:pointer;'>";
			$ctr++;	
			echo "
				  <div style='width:120px;height:173px;margin: 10px 5px 20px 10px;;background:white;' class='fl'>
					<p style='font-size:12px;text-align:center;padding-bottom:3px;margin-bottom:-1px;background-color:#757575;color:white;'> 
						<b style='color:#330404;'>$ctr. </b><b>  ". substr($POSM_TypeName,0,-5) ." </b>  $add_shortCut
				   </p>
				   <p style='font-size:12px;text-align:center;padding-bottom:3px;margin-bottom:-1px;background-color:#999999;color:white;'> 
					$extra_label 
				   </p>
				   <input type='hidden' name='items[]' value='$item_ID'>
					<div style='border: 1px solid #ccc;text-align:center;padding:5px;height:100px;overflow: hidden;'>
						<a href='$itemPreviewLink' target='_blank' class='itemLink'>
							<table>
							 <tr>
								<td class='gal-Icon-Container'><img class='gal-Icon-Img' src='$img_path' style='width:100%;margin-top:-65px;'></td>
							 </tr>	
							</table> 
						</a>
					</div>
					<p style='font-size:10px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;background:white;color: #555;margin-bottom:10px;' label='$itemName'> 
						<b> ". cutStr($itemName) ."</b>
					</p>
					 <br/>
				 </div>";
			$iType=$POSM_TypeName;
			}
			?>	
			</div>
        </div>
		   
		<!--ITEMS PAGE-->
		</div>
	</div>
	<?php } ?>
	
	<?php 
	function iWant_code($iwant_ctr)
	{
		$iwant_code="";
		$coding = "%04d";
		if($iwant_ctr>9999)
			$coding = "%0".strlen($iwant_ctr)."d";
		
		return $iwant_code = sprintf($coding, $iwant_ctr);
	}
	
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
	
	
	<?php
	function generate_iLikeRules($voting_rules)
	{ 
	echo "<div style='display:none;position:absolute;margin-left: 451px;margin-top:18px;' id='iWant_Rules_Table'>	
			$voting_rules 
		</div>";
	}
	?>
	
	<script type="text/javascript">
	function campaignRules()
	{
		if(document.getElementById('iWant_Rules_Table').style.display=='none')
			document.getElementById('iWant_Rules_Table').style.display='block';
		else
			document.getElementById('iWant_Rules_Table').style.display='none';
	}
	
	function resendEmail(campaignID,voterID)
	{
		jConfirm('Resend email notification?','Alert',function(r){
			if(r){ var stat = $.ajax({
					url: '<?php echo HTTP_PATH ?>iWantCampaign/resend_Email/'+campaignID+'/'+voterID,
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
	
	function addItem(iID)
	{
		jConfirm("Add this item to iWant Campaign?","Alert",function(r){
			
			if(r){
			 var add_item = $.ajax({
				url: "<?php echo HTTP_PATH . "iWantCampaign/add_div_item/" ?>"+ iID,
				async: false
			}).responseText;
			 
			if(add_item=='ok'){
				 var a = $.ajax({
					url: '<?php echo HTTP_PATH ?>iWantCampaign/view_div_items/',
					async: false
				}).responseText;
		
			     var num = $.ajax({
					url: '<?php echo HTTP_PATH ?>iWantCampaign/count_div_item',
					async: false
				}).responseText;
				
				var votingRules = $.ajax({
					url: '<?php echo HTTP_PATH ?>iWantCampaign/iWantVotingRules/refresh',
					async: false
				}).responseText;
				
				document.getElementById('iLikeResults').innerHTML = a;
				document.getElementById('iWant_items_ctr').innerHTML = num;
				document.getElementById('iWant_Rules_Table').innerHTML = votingRules;
		
			}else{
				jAlert('Sorry item already exist. Please choose another item.', 'Alert Dialog');
			}
			
			}
			//alert(iID);
		});
	}

	function delete_item(iID)
	{
		jConfirm("Remove this item from iWant Campaign?","Alert",function(r){
			if(r){ 
			 var delete_item = $.ajax({
				url: "<?php echo HTTP_PATH . "iWantCampaign/delete_div_item/" ?>"+ iID ,
				async: false
			}).responseText;
			
			if(delete_item=='ok'){
				 var a = $.ajax({
					url: '<?php echo HTTP_PATH ?>iWantCampaign/view_div_items/',
					async: false
				}).responseText;
				
				var num = $.ajax({
					url: '<?php echo HTTP_PATH ?>iWantCampaign/count_div_item',
					async: false
				}).responseText;
				
				var votingRules = $.ajax({
					url: '<?php echo HTTP_PATH ?>iWantCampaign/iWantVotingRules/refresh',
					async: false
				}).responseText;
				
				document.getElementById('iLikeResults').innerHTML = a;
				document.getElementById('iWant_items_ctr').innerHTML = num;
				document.getElementById('iWant_Rules_Table').innerHTML = votingRules;
			}
			}
		});
	}
	</script>
	
	<script type="text/javascript">
	function loadItems(type)
	{
		var val="0px";
		var h = window.innerHeight;
		
		if(type=='show'){
			if(h>=900)
				val = "-241px";
			else
				val = "-99px";	
			//alert('innerWidth: '+ w + 'left:'+val);
			//alert(val);*/
			
			document.getElementById("iWantItemsDiv").style.display = 'block';
			document.getElementById("iWantItemsDiv").style.marginTop = val;
		}else{
			document.getElementById("iWantItemsDiv").style.display = 'none';
		}
	}
	//var w = window.innerWidth;
	//alert(w);
	
	function Save_Notify(){
		if($("#vendorFORM").parsley("validate")){
			if($('#vendorFORM').parsley().isValid()){
				document.getElementById('loading3').style.display = 'block';
			}
		}
	}
	
	$(function() {
		$( "#accordion" ).accordion({collapsible: true,active: false,});
	});
	
	/*REMOVE ITEM*/
	function removeThisItem(id)
	{
		//alert(id);
		jConfirm('Are you sure you want to remove this item from iWant Campaign?.','Alert',function(r){		
			if(r) document.getElementById(id).remove();
		});
	}
	/*REMOVE ITEM*/
	
	
	/*CONFIRM IF WANT TO SAVE*/
	function SavePublish()
	{	
		//CHECK IF VALID DATE
		DateFrom = document.getElementById('DateFrom').value;
		DateTo 	 = document.getElementById('DateTo').value;
		invalidRules = document.getElementById('invalidRules').value;
		
		var a = $.ajax({
			url: '<?php echo HTTP_PATH ?>iLikeCampaign/check_date/'+DateFrom+'/'+DateTo,
			async: false
		}).responseText;
		
		var countriesInIwantCampaign = $.ajax({
			url: '<?php echo HTTP_PATH ?>iWantCampaign/howManyCountryThereIniWantItems',
			async: false
		}).responseText;
		
		var committee_checker = "ok";
		//HAS COMMITTEES
		var iIndex = $('.emails').length;
		<?php 
		if($status=="new" OR $status=="updated"){ ?>
		//CHECK CANVASSING RULES
		var committee_checker = $.ajax({
			url: '<?php echo HTTP_PATH ?>iWantCampaign/comittees_vs_canvassing_rules/'+iIndex,
			async: false
		}).responseText;
		<?php } ?>
		
		jConfirm('OK - Save all changes and Publish Campaign?','Alert',function(r){		
			if(r){
				if(a=='good' & committee_checker=='ok')
				{	
					//HAS COMMITTEES
					<?php if(!isset($alter)){ ?>
					var invalid_rules = $('#').value;
					if(iIndex<=0 || iIndex < <?php echo $min_committees ?> || invalidRules!=0 || iIndex < countriesInIwantCampaign){
						jAlert('Warning: Number of screening committees doesn\'t meet it should be atleast '+countriesInIwantCampaign+' or a problem on campaign rules has been occured please check. Thank you!\n');
					}else{
						document.getElementById('loading2').style.display = 'block';
						$("#vendorFORM").parsley("validate");
						document.getElementById("vendorFORM").action = "<?php echo HTTP_PATH.'iWantCampaign/publishCampaign/'.$campaignID; ?>";
						$("#vendorFORM").submit();
					}<?php 
					} else { ?>
						document.getElementById('loading2').style.display = 'block';
						$("#vendorFORM").parsley("validate");
						document.getElementById("vendorFORM").action = "<?php echo HTTP_PATH.'iWantCampaign/alter/'.$campaignID; ?>";
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
   
 
	function cleariLikeSessionItems(obj,url)
	{
		jConfirm('Are you sure you want to quit iWant Campaign Form?','Alert',function(r){
			if(r) window.location = url;
		});
	}
 
	function StopPar()
	{
		//document.getElementById('publishInput').value = 'n';
		document.getElementById('loading3').style.display = 'block';
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
	     var iIndex = $('.emails').length;
		 var emails = $('.emails');
		 var iE=0;
		 for(i=0;i<iIndex;i++)
		  {
		    if(emails[i].value==obj.value)  iE++;
		  } 
		  if(iE>1) {jAlert( obj.value +"Email address is already in the list."); obj.value=origEmails; }
		 
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
		var country 	  = document.getElementById('selcountry').value;
		var email 	   	  = document.getElementById('committeeEmail').value;
		var department 	  = document.getElementById('departmentName').value;
		var birth_of_year = document.getElementById('yearOfBirth').value;
		var msg = '';
		var iIndex = $('.emails').length;
		var emails = $('.emails');
		
		/*CHECK HOW MANY NOMINEES IN THE DATABASE*/
		var a = $.ajax({
			url: '<?php echo HTTP_PATH ?>iWantCampaign/lastVoted/'+email,
			async: false
		}).responseText; 
		
		var countryIDs_ctr = $('.countryID').length;
		var countryIDs_val = $('.countryID');
		var ctr=0;
		for(x=0;x<countryIDs_ctr;x++)
		{
			if(countryIDs_val[x].value == country)
				ctr++;
		}
	
		//if(ctr >=  <?php echo $min_committees ?>)
		//	msg += "Sorry, Number of committes must be <?php echo $min_committees ?> per BU only. \n";
		
		for(i=0;i<iIndex;i++)
		{
		   if(emails[i].value==email)  msg += email +"Email address is already in the list.";
		}
		
		if(fname=='') 			 		  msg += 'Enter first name \n';
		if(validateStr(fname)==true)      msg += 'First name can only contain alphanumeric characters and space\n';
		if(lname=='') 			 		  msg += 'Enter last name \n';
		if(validateStr(lname)==true)      msg += 'Last name can only contain alphanumeric characters and space\n';
		if(email=='') 			 		  msg += 'Enter email \n';
		if(department=='')  	 		  msg += 'Enter department \n';
		if(validateStr(department)==true) msg += 'Department can only contain alphanumeric characters, space and hypehns\n';
		if(birth_of_year=='') 	 		  msg += 'Enter Birth of year \n';
	    if(validateEmail(email)) 		  msg += 'Enter a valid email \n';
	    if(country=='') 		  		  msg += 'Enter a country \n';
	
		//VALIDATE BIRTH YEAR
		if(validateYear(birth_of_year)==true)  msg += 'Under age committee must be 18 yrs old and above \n';
		
		//GET THE NUMBER OF TRs
		var numTr = (document.getElementById('emailsTable').getElementsByTagName('tr').length);
		
		
		if(msg=='')
		{ 		   
		  if(a=='existing')
			msg += "User already voted, please wait another year";
			
		 if(msg=="")
		 {
			
			var my_email = $.ajax({
				url: '<?php echo HTTP_PATH ?>iWantCampaign/generateEmailTD/'+ fname +'/'+ lname + '/'+ gender +'/'+email+'/'+department+'/'+birth_of_year+'/'+country+'/'+iIndex,
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
			$('#selcountry').prop('selectedIndex',0);
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
		document.getElementById('loading').style.display = 'none';
		
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
				
				var TRlength = document.getElementById('emailsTable').getElementsByTagName('tr').length;
				var TRs 	 =  document.getElementById('emailsTable').getElementsByTagName('tr');
				
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
	
	
	