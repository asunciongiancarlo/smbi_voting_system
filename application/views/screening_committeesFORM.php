
	<div class="content">
    	<div class="title-content" style='width:100%;'>
        	<h2 class='fl'><?php 
					echo "MANAGE <span style='text-transform:lowercase;'>i</span>WANT COMMITEE </h2>";
				?>
			<h2  style='text-decoration:underline;cursor:pointer;font-size:14px;margin-top:0px;float:right;float: right;margin-right: 45px;'>
				<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
			</h2>
			
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
					$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id=".$_SESSION['countryID']." ORDER BY countryName ASC";
				}	
				$country = $CI->db->query($Csql);
				
				$departments = "SELECT department_name FROM voters_department ORDER BY department_name ASC";
				$departments = $CI->db->query($departments);
				
				//ACTION STATEMENT
				$action = HTTP_PATH ."iWantCampaign/screening_committees/insert";
				
				//ISSET ID
				$EDIT=TRUE;
				$vgCountryID="";
				if(isset($id))
				{
					$action = HTTP_PATH ."iWantCampaign/screening_committees/update/".$id;
					$sql   = $CI->db->query("SELECT countryID FROM voters_group WHERE id = $id LIMIT 0,1");
					$sql   = $sql->row();
					$vgCountryID    = $sql->countryID;
					if($vgCountryID!=$_SESSION['countryID']) $EDIT=FALSE;
				}
				
			?>
			<div class="container" style='width:98%;'>
			<?php 
				$CI =& get_instance();
				$CI->load->library('forms');
				$CI2 =& get_instance();
				$CI2->load->library('fv');

				echo $CI->forms->form_header('SMBi','vendorFORM',$action);	
			?> 
			<div class="clear"></div>
			<div class="fl" style="margin-top:20px;margin-bottom:10px;margin-left:20px;">
					<label style='margin: -17px 0px 3px -19px;'> Minimum No. of Screening Committees:  <b><?php echo $min_committees ?>/Business Unit</b></label>
					<div class="button-content1b" id='tab2'>
						<a href="#" onclick='showcommiteeDiv()'><h2>SCREENING COMMITEE</h2></a>
					</div>
					
					<div id='loading' class='fl'>
						<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> Checking email address...
					</div>
					
					<div id='loading2' class='fl' style='margin-top:20px;display:none;'>
						<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> Please wait, campaign is being save email notification will send on campaign duration...
					</div>
					
					<div id='loading3' class='fl' style='margin-top:20px;display:none;'>
						<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> <?php echo $loading3MSG ?>
					</div>
			</div>
			<div style='clear:both;'></div>
			<br/>			

				
			<div id='committeeDiv' style='display:block;margin-top:10px;'>
				<!---COMMITEE FORM-->
				
				<div style='clear:both;'></div>
				<?php if($_SESSION['super_admin']!='y'){ ?>
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
								<select name='yearOfBirth' id='yearOfBirth' style="width:96%;">
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
								 <select name='selcountry' id='selcountry' style="width:100%;font-size: 11px;" placeholder='country'> 
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
				<?php } ?>
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
										if($_SESSION['super_admin']!='y')
										echo "<img onclick='removeEmail(\"emailCtr".$emailCtr++."\")' style='margin: -10px 17px -20px 9px;padding-top: 4px;cursor:pointer' src='".HTTP_PATH."img/delete.png' title='Delete' class='fl'>  ";
										
								echo " </td>
									</tr>
								</table>
								</div>";
								
							} 
						}
					 ?>
				</div>
				<!--NOMINATION COMMITEE-->
			</div>
        </div>
        <div class="clear"></div>
    </div>
   <?php
	   if($_SESSION['super_admin']!='y'){
	   $field   = "<div class='fl'>";
	   $field  .= "<input name='btnsubmit2' type='button' onclick='countEmail()' class='nav-REMOTE-btn1 fl' value='SAVE COMMITEE GROUP' style='color:white;margin-top:20px;'> <br/><br/> ";
	   $field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:260px;margin-left:5px;'> *Save all screening committees</p>";
	   echo $field  .= "</div>";
	   }
	   echo "</form>";
	?>
	

	<script type="text/javascript">
	function countEmail()
	{
		msg='';
		var iIndex = $('.emails').length;
		if(iIndex <  <?php echo $min_committees ?>)
			msg += "There should be <?php echo $min_committees ?> person per Screening Committees' group.\n";
			
		if(msg!='')
		 jAlert(msg);
		else
		 $('#vendorFORM').submit();
	}
	
	function loadItems(type)
	{
		var val="0px";
		var w = window.innerWidth;
		
		if(type=='show'){
			if(w>2000)
				val = "405px";
			if(w<2000 & w>1300)
				val = "100px";
			if(w<2000 & w>1700)
				val = "376px";
			if(w<1300)
				val = "-76px";
			
			//alert('innerWidth: '+ w + 'left:'+val);
			//alert(val);
			document.getElementById("iWantItemsDiv").style.left = val;
		}else{
			document.getElementById("iWantItemsDiv").style.left = '-10000px';
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
		var country 	  = document.getElementById('selcountry').value;
		var email 	   	  = document.getElementById('committeeEmail').value;
		var department 	  = document.getElementById('departmentName').value;
		var birth_of_year = document.getElementById('yearOfBirth').value;
		var msg = '';
		var iIndex = $('.emails').length;
		var emails = $('.emails');
		
		//SAME SET OF VOTER'S COUNTRY IN 
		var jIndex 	  = $('.countryID').length;
		var countries = $('.countryID');
		var countryError=0;
		for(i=0;i<iIndex;i++)
		{
		   if(countries[i].value!=country)  countryError++;
		}
		if(countryError!=0) msg += "Screening Committee's group should have same set of country.\n";
		
		//if(iIndex ==  <?php echo $min_committees ?>)
		//	msg += "Please create another group their should be <?php echo $min_committees ?> person per Business Unit in screening committee's group. Thank you!\n";
	
		
		var countryIDs_ctr = $('.countryID').length;
		var countryIDs_val = $('.countryID');
		var ctr=0;
		for(x=0;x<countryIDs_ctr;x++)
		{
			if(countryIDs_val[x].value == country)
				ctr++;
		}
		
		for(i=0;i<iIndex;i++)
		{
		   if(emails[i].value==email)  msg += email +" Already in the list. \n";
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
		if(validateYear(birth_of_year)==true)  msg += 'Under age commitee must be 18 yrs old and above \n';
		
		//GET THE NUMBER OF TRs
		var numTr = (document.getElementById('emailsTable').getElementsByTagName('tr').length);
		
		if(msg=='')
		{ 
		  //IF VOTED
		   var a = $.ajax({
			url: '<?php echo HTTP_PATH ?>iWantCampaign/lastVoted/'+email,
			async: false
		   }).responseText; 
		   
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
	
	
	