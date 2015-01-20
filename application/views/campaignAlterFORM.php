    <div class="content">
    	<div class="title-content" style='width:100%;'>
        	<h2 class='fl'><span style='text-transform:lowercase;'>i</span>LIKE CAMPAIGN </h2>
			<h2  style='text-decoration:underline;cursor:pointer;font-size:14px;margin-top:0px;float:right;float: right;margin-right: 45px;' onclick='loadItems()'>ITEM LIST</h2>
			<h2  style='text-decoration:underline;cursor:pointer;font-size:14px;margin-top:0px;float:right;float: right;margin-right: 45px;' onclick='campaignRules()'>CAMPAIGN RULES</h2>
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
		
				//ACTION STATEMENT
				$action = HTTP_PATH ."iLikeCampaign/votingCampaign/insert";
				
				//GET COUNTRY
				$sql = $CI->db->query("SELECT countryName FROM country WHERE id=". $_SESSION['countryID'] . " LIMIT 1");
				$row = $sql->row(); 
				$userCountryName = $row->countryName;
				
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
					$action = HTTP_PATH ."iLikeCampaign/votingCampaign/$id/update.html";
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
				generate_iLikeRules($rules,$min_committees);
				
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
						<a href="#" onclick='showcommiteeDiv()'><h2>NOMINATION COMMITEE</h2></a>
					</div>
					
					<div id='loading' class='fl'>
						<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> Checking email address...
					</div>
			</div>
			<div style='clear:both;'></div>
			<br/>
				<div id='itemsList' style='margin-top:-241px;display:none;z-index:90001;position:absolute;width:71%;margin-left: 77px;'>	
				<div style='color:white;background-color:#bb1d1d;border:1px solid #bb1d1d;padding-left:10px;'> CAMPAIGN ITEMS: <?php echo count($items) ?> 
					<span class='close2' style='float:right'>X close  </span> 
				</div>				
				<!--ITEMS PAGE-->
				<div id='Menu_Item' style='border:1px solid #bb1d1d; height:360px; overflow-y:scroll;margin:0px auto;background-color:white;padding-left:18px;padding-bottom:10px;'>
				<?php
					function cutStr($itemName=''){
						if(strlen($itemName)>=20)
							return substr($itemName,0,17)."...";
						else	
							return $itemName; 
					}
					
					$x=0;
					 
					foreach($items as $d){
						extract($d);
						$itemID = $id;
						//GET FIRST ITEM IMAGE
						$sql = $CI->db->query("SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = $itemID LIMIT 0,1");
						$item_img = $sql->result_array();
						extract($item_img);
						$item_img = isset($item_img[0]['image']) ? $item_img[0]['image'] : 'blank.png';
						$img_path = HTTP_PATH."img/small/$item_img";
						$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$itemID"; 
					 $POSM_TypeName  = isset($POSM_TypeName) ? $POSM_TypeName:"";	
					echo "<a href='$itemPreviewLink' target='_blank' class='itemLink'>
						  <div style='width:160px;height:150px;margin:10px;' class='fl'>
							<p style='font-size:13px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;margin-bottom:-1px;background-color:#999999;color:white;'> 
								<b> ". $POSM_TypeName ."</b> 
						   </p>
							<div style='border: 1px solid #ccc;text-align:center;padding:5px;height:100px;'>
								<img src='$img_path' class='itemIcon'>
							</div>
							
							<p style='font-size:13px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;'> 
								<b> ". cutStr($itemName) ."</b> 
							</p>
						 </div>
						 </a>";
					} ?>
					<div class='clear'> </div>
				</div>
				<!--ITEMS PAGE-->
				</div>
				<div id='mask' style='position: absolute;left: 0;top: 0;z-index:90000;background-color: #000;'></div>
			
			<div id='committeeDiv' style='display:block;padding-right:10px;margin-top:-43px;'>
				<!---COMMITEE FORM-->
				
				<div style='clear:both;'></div>
				<table border="0" style="margin-top:-20px;margin-bottom:22px;width:100%">
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
								 <select name='selgender' id='selgender' style="width:95%;" placeholder='gender'>   
								    <option value='Male'>Male</option>
								    <option value='Female'>Female</option>
								 </select>   
							</div>
						</td>
						<td class="td-left" width='10%'>
							<h2 class="form"> DEPT. </h2>
							<div class="fill-up-box" style="width:95%;"> 
								<input name="departmentName" style="width:98%;" type="text" class="fill-input" id='departmentName'>    
							</div>
						</td>
						<td class="td-left" width='30%' style='padding:3px'>
							<h2 class="form"> EMAIL </h2>
							<div class="fill-up-box" style="width:100%;"> 
								<input name="committeeEmail" type="text"  style="width:98%;" class="fill-input" id='committeeEmail'>    
							</div>
						</td>
						<td class="td-left" width='15%'>
							<h2 class="form"> BIRTH YEAR </h2>
							<div class="fill-up-box" style="margin-left: 5px;width:95%;"> 
								<select name='yearOfBirth' id='yearOfBirth' style="width:100%;">
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
				<!---COMMITEE FORM-->
			
			
				<!--NOMINATION COMMITEE-->
				<div class="clear"></div>
				<div id='nominationCommitteDiv'>
					<table id='emailsTable' cellpadding="0" cellspacing="0" style="width:100%;text-align:left;margin-top:10px;text-align:center;">
					<tr>
						<th style="width:120px;text-align:center;"> F.NAME 				</th> 
						<th style="width:120px;text-align:center;"> L.NAME 				</th> 
						<th style="width:10px;text-align:center;">  GENDER 				</th> 
						<th style="width:30px;text-align:center;">  DEPT.  		        </th> 
						<th style="width:250px;text-align:center;"> EMAIL 				</th> 
						<th style="width:10px;text-align:center;">  YEAR 				</th> 
						<th style="width:45px;text-align:center;">  ACTION 				</th> 
					</tr>
					<?php
						$emailCtr = 1;
						$x=0;
						if(isset($admin_users)) {
						foreach($admin_users as $au)
						{
							$c = (($x++)%2) == 0 ? "" : "style='background:#f9ebeb;'"; 
							extract($au);
							echo "<tr id='emailCtr".$emailCtr."' $c> 
									<td> 
										<input type='hidden' name='voterTypes[]' 	value='2'>
										<input type='text' value='$fname' 	    	    name='fnames[]'      readonly='readonly'  onclick='enable(this)' onBlur='lostFocus(this)'> 
									</td>
									<td> <input type='text' value='$lname'  		    name='lnames[]'       readonly='readonly'  onclick='enable(this)' onBlur='lostFocus(this)'> </td> 
									<td> <input type='text' value='$gender'  		    name='gender[]'      readonly='readonly'  onclick='enable(this)' onBlur='lostFocus(this)'> </td> 
									<td> <input type='text' value='$department'  		name='departments[]' readonly='readonly'  onclick='enable(this)' onBlur='lostFocus(this)'> </td> 
									<td> <input type='text' value='$email_address'      class='emails'	name='emails[]' 	 readonly='readonly'  onclick='enable(this);gotFocusEmail(this)' onBlur='lostFocus(this);checkListEmail(this);'> </td>  
									<td> <input type='text' value='$year_of_birth'  	name='years[]'       readonly='readonly'  onclick='enable(this)' onBlur='lostFocus(this)'> </td> 
									<td> <label onclick='removeEmail(\"emailCtr".$emailCtr++."\")'>Del</label> </td>
								 </tr>";
						} }
					 ?>
					</table>
				</div>
				<!--NOMINATION COMMITEE-->
			</div>
        </div>
        <div class="clear"></div>
    </div>
   <?php 
	   
		   $field   = "<div class='fl'>";
		   $field  .= "<input name='btnsubmit2' onclick='SavePublish()' type='button' class='nav-REMOTE-btn1 fl' value='Save' style='color:white;margin-top:20px;'> <br/><br/> ";
		   $field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:260px;margin-left:5px;'> *OK - Save and Publish Campaign <br> Cancel - Save and Publish later.</p>";
		   echo $field  .= "</div>";
	   
	   
	   echo "</form>";
	?>
	
	<?php 
		function generate_iLikeRules($rules,$min_committees)
		{ 
			echo "<table cellpadding='0' cellspacing='0' border=1 style='width:300px;margin: 0px auto;font-size:12px; display:none;position:absolute;background-color:white;left:55%;' class='fr iLike_Result_Table' id='iLike_Rule_Table'>
				<tr style='border-radius: 6px;'>
					<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Field Name  </b></td> 
					<td style='width:30px;text-align:center;color:white;' bgcolor='#bb1d1d'>    <b>Operator   </b></td>    
					<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Value   </b></td> 
					 	   			  
				</tr>";
			$x = 0;
			$invalidRules=0;
			//print_r($rules);			
			foreach($rules as $r){
					extract($r);
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
				echo "<tr>
	 
					  <td  $c  style='text-align:left;'>	 $fieldName  			</td>
					  <td  $c  style='text-align:center;'>	  $rel  					</td>
					  <td  $c  style='text-align:center;'>	  $val  					</td> ";
					  
				 
				 
				echo "</tr>";
			}
			
			echo "<tr>
					<td colspan='6' style='text-align:left;'>Minimum Number of Committees: <b>$min_committees </b></td>
				</tr>";
			
			//SECRET ELEMENT
			echo "<input type='hidden' value='$invalidRules' name='invalidRules' id='invalidRules'>";
			
			if(!$rules)
				echo "<tr><td colspan='6'>Sorry no campaign rules found.</td></tr>";
			
			echo "</table>";
		}
	?>
	
	
	<script type="text/javascript">
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

		
		jConfirm('OK - Save and Publish Campaign. \nCancel - Save and Publish later.','Alert',function(r){		
			if(r){
				if(a=='good')
				{
					//HAS COMMITTEES
					var iIndex = document.getElementsByClassName('emails').length;
					
					if(iIndex < <?php echo $min_committees ?>)
					{
						jAlert('Sorry cannot publish campaign number of nomination committees doesn\'t meet.');
					}else{
						if(invalidRules>0){
							jAlert('Sorry cannot publish campaign there is an error in Campaign Rules.');
						}else{
							$("#vendorFORM").parsley("validate");
							document.getElementById("vendorFORM").action = "<?php echo HTTP_PATH.'iLikeCampaign/publishCampaign/'.$campaignID; ?>";
							$("#vendorFORM").submit();
						}
					}
				}else{
					jAlert('Sorry cannot be save, double check date parameters or fix the campaign rules.');
				}
			}else{
				if(a=='good'){
					$("#vendorFORM").parsley("validate");
					$("#vendorFORM").submit();
				}else{
					jAlert('Sorry cannot be save, double check date parameters or fix the campaign rules.');
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
		var maskHeight = $(document).height();
		var maskWidth  = $(document).width();
		
		$('#mask').css({'width':maskWidth});
		$('#mask').css({'height':maskHeight});
		$('#mask').fadeIn(1500);
		$('#mask').fadeTo('slow',0.7);
		
		var winH = $(window).height();
		var winW = $(window).width();
		
		$('#Menu_Item').css({'top':120});
		$('#Menu_Item').css({'left':winW/2-$('#Menu_Item').width()/2});
		$('#Menu_Item').fadeIn(1500);
		$('#itemsList').css({'display':'block'});
	}
 
	function cleariLikeSessionItems(obj,url)
	  {
		jConfirm('Are you sure you want to quit? Campaign haven\'t save.','Alert',function(r){
			if(r) window.location = url;
		});
	  }
 
	function StopPar()
	{
		//document.getElementById('publishInput').value = 'n';
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
		var iIndex = document.getElementsByClassName('emails').length;
		var emails = document.getElementsByClassName('emails');
	
		if(iIndex < <?php echo $min_committees ?>)
			msg += "Sorry, Number of committes must be at least 3. . \n";
		
		for(i=0;i<iIndex;i++)
		{
		   if(emails[i].value==email)  msg += email +" Already in the list. ";
		}
		
		if(fname=='') 			 msg += 'Enter first name \n';
		if(lname=='') 			 msg += 'Enter last name \n';
		if(email=='') 			 msg += 'Enter email \n';
		if(department=='')  	 msg += 'Enter department \n';
		if(birth_of_year=='') 	 msg += 'Enter Birth of year \n';
	    if(validateEmail(email)) msg += 'Enter a valid email \n';
	
		//VALIDATE BIRTH YEAR
		if(validateYear(birth_of_year)==true)  msg += 'Under age commitee must be 18 yrs old and above \n';
		
		//GET THE NUMBER OF TRs
		var numTr = (document.getElementById('emailsTable').getElementsByTagName('tr').length);
		
		if(msg=='')
		{ 
			//CHECK WHEN THE LAST VOTED			
			var file = '<?php echo HTTP_PATH ?>mail_checker/m/'+email;
			
			var xmlhttp;
			if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
			  xmlhttp=new XMLHttpRequest();
			}
			else
			{// code for IE6, IE5
			  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			xmlhttp.onreadystatechange=function()
			{
			  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			  {
				  //ACTIVE EMAIL
				  if(xmlhttp.responseText!='	valid')
					msg += "The email account that you entered does not exist. Please try double-checking the recipient\'s email address for typos or unnecessary spaces.";
				  
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
					ajaxAppend('<?php echo HTTP_PATH ?>iLikeCampaign/generateEmailTD/'+ fname +'/'+ lname + '/'+ gender +'/'+email+'/'+department+'/'+birth_of_year+'/'+numTr,'emailsTable');
					document.getElementById('committeeFname').value = '';
					document.getElementById('committeeLname').value = '';
					$('#selgender').prop('selectedIndex',0);
					document.getElementById('committeeEmail').value = '';
					document.getElementById('departmentName').value = '';
					$('#yearOfBirth').prop('selectedIndex',0);
				 }else{
				   document.getElementById('loading').style.display = 'none';
				   jAlert(msg);
				 }  
				 
				 
			  }
			}
			xmlhttp.open("GET",file,true);
			xmlhttp.send();
			
			
			
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
				document.getElementById(id).remove();
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