<?php
global $CI;
$CI =& get_instance();
$CI->load->database('default');
?>
<div class="content">
	<div class="fl title-content">
		<h2> ROLES </h2>
	</div>
	<div style="float:right;margin: 16px;">
		<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
	</div>
		
	<div class="clear"></div>
	<div>

		<div  style="font-weight:bold; font-size:15px; padding: 30px 30px;" class="container2">

	<?php
	
		$action = HTTP_PATH ."users/roles/insert";
		$roleName = '';
		$userProfileID = 0;
		$roleID=0;
		
		
		if(isset($id))
		{
			$action = HTTP_PATH ."users/roles/update/".$id;
			$sql = $CI->db->query("SELECT * FROM roles WHERE id= $id");
			$sql = $sql->result_array();
			extract($sql);
			
			$roleID 		= $sql[0]['id'];
			$roleName = $sql[0]['roleName'];
			$selectedProfileID  = $sql[0]['user_profileID'];
		}
		
		echo "<input type='hidden' name='roleID' value='$roleID' id='roleID'>";
		
		
		
		//IF SET SELECTEDID
		if(isset($selectedProfileID))
		{
			$uf =  $selectedProfileID;
		}

			$CI->load->library('forms');
			echo $CI->forms->form_header('SMBi','roleName',$action);
			$sql = $CI->db->query("SELECT *, user_profile.id as upID FROM user_profile ORDER BY profile_name ASC");
			$user_profiles = $sql->result_array();
			
		
			echo $CI->forms->form_fields2('text','roleName',$roleName,'ROLE NAME','r');
			echo "<div class='clear'></div>";
		
			
			
			//MODULE CHECKER
			function moduleChecker($moduleID,$function,$roleID)
			{
				$CI =& get_instance();
				 
				$sql = $CI->db->query("SELECT * FROM roles_userProfilesRef 
									   WHERE roleID = $roleID AND
									   systemModID = $moduleID AND
									   function = '$function'");
				$roles = $sql->result_array();
				
				if($roles!=NULL)
				{
					return "checked";
				}
			}
		
	
			//CHECKBOX FOR USER PROFILES
			echo "<h2 class='form'> USER PROFILES </h2>";
			$mod=0; $mod2=0; $h=0; $h1=0;
			foreach($user_profiles as $up)
			{
				extract($up);
				$link = HTTP_PATH.'img/arrow_down_role.png';
	
				
				echo "<h2 id=h".$h++." style='font-size:15px;color:white;border:1px solid gray;width:860px;padding-left:10px;background:gray;cursor:pointer;' class='fl' onclick=\"viewModules('divMod".$mod++."','h".$h1++."')\"> 
						<img src='$link' style='border:1px solid gray;padding:2px;background:white;'> $profile_name 
					 </h2>";
				echo "<div class='clear'></div>";
				
				$sql = $CI->db->query("SELECT *, system_mod.id as moduleID  FROM user_profileRef 
									   LEFT JOIN system_mod 
									   ON system_mod.id = user_profileRef.system_modID
									   WHERE user_profileID = $id ORDER by moduleID ASC");
									   
										
									   
				$modules = $sql->result_array();
				
				//print_r($modules);
				echo "<div id='divMod".$mod2++."' style='border:1px solid gray;width:850px;padding:10px;display:none;margin-top:-10px;'>";
					foreach($modules as $m)
					{
						extract($m);
						echo $modname;
						echo "<br/>";
						$DELETE="DELETE";
						if($moduleID==18) $DELETE="PURGE";
						
						echo "<input name='permission[]' type='checkbox' value='$moduleID|ADD|$upID' ". moduleChecker($moduleID,'ADD',$roleID) ." style='vertical-align:baseline'> 		 <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'> ADD </span> 
							  <input name='permission[]' type='checkbox'  value='$moduleID|EDIT|$upID' ". moduleChecker($moduleID,'EDIT',$roleID) ." style='vertical-align:baseline'> 	 <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'> EDIT </span> 
							  <input name='permission[]' type='checkbox'  value='$moduleID|DELETE|$upID' ". moduleChecker($moduleID,'DELETE',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'> $DELETE </span> 
							  <input name='permission[]' type='checkbox'  value='$moduleID|REVIEW|$upID' ". moduleChecker($moduleID,'REVIEW',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'> REVIEW </span> ";
							  
							  if($moduleID==27 OR $moduleID==29){
								echo "<input name='permission[]' type='checkbox'  value='$moduleID|ALTER CAMPAIGN|$upID' ". moduleChecker($moduleID,'ALTER CAMPAIGN',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>ALTER CAMPAIGN </span>";
								
								if($moduleID==29){ 
									echo "<input name='permission[]' type='checkbox'  value='$moduleID|PUBLISH CAMPAIGN|$upID' ". moduleChecker($moduleID,'PUBLISH CAMPAIGN',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>PUBLISH CAMPAIGN </span>";
									echo "<br/><input name='permission[]' type='checkbox'  value='$moduleID|EDIT NOMINEES|$upID' ". moduleChecker($moduleID,'EDIT NOMINEES',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>ADD/EDIT NOMINEES (SEND EMAIL NOTIFICATION TO BUSINESS UNITS)</span>";
								}	
								if($moduleID==27) echo "<input name='permission[]' type='checkbox'  value='$moduleID|ALTER RESULTS|$upID' ". moduleChecker($moduleID,'ALTER RESULTS',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>ALTER RESULTS </span>";
								
								if($moduleID==27) echo "<br/><input name='permission[]' type='checkbox'  value='$moduleID|ALTER CANVASSING RULES|$upID' ". moduleChecker($moduleID,'ALTER CANVASSING RULES',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>ALTER CANVASSING RULES (RESET RULES) </span>";
								
								if($moduleID==29) echo "<br/><input name='permission[]' type='checkbox'  value='$moduleID|ALTER CANVASSING RULES|$upID' ". moduleChecker($moduleID,'ALTER CANVASSING RULES',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>ALTER CANVASSING RULES (RESET RULES) </span>";
							  }
							  if($moduleID==18){
								echo "<input name='permission[]' type='checkbox'  value='$moduleID|APPROVE|$upID' ". moduleChecker($moduleID,'APPROVE',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>PUBLISH ITEM</span>";
							  }
							  
							  if($moduleID==18 OR $moduleID==26)  
								echo "<input name='permission[]' type='checkbox'  value='$moduleID|NOTIFICATION|$upID' ". moduleChecker($moduleID,'NOTIFICATION',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>NOTIFICATION </span>"; 
							   if($moduleID==18){
							    echo "<input name='permission[]' type='checkbox'  value='$moduleID|PURGE APPROVAL|$upID' ". moduleChecker($moduleID,'ARCHIVE',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>PURGE APPROVAL</span>";
								echo "<br/><input name='permission[]' type='checkbox'  value='$moduleID|POPULAR|$upID' ". moduleChecker($moduleID,'POPULAR',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>POPULAR</span>";
								echo "<input name='permission[]' type='checkbox'  value='$moduleID|ARCHIVE|$upID' ". moduleChecker($moduleID,'ARCHIVE',$roleID) ." style='vertical-align:baseline'>  <span style='margin-left:5px;margin-right:10px;;padding-right:10px;'>ARCHIVE</span>";
							  }
							echo "<div class='clear' style='height:15px;'></div>";
							  
						echo "<br/>";
					}
					
				echo "</div>";
			}
	?>
	    </div>
		<?php  
			echo $CI->forms->buttons('GoPar','roleName');
			echo "</form>";
		?>
</div>

	 
	
	<script type="text/javascript">
	 	function StopPar()
		{
			$('#roleName').parsley().destroy();
		}
	
 	 function viewModules(box,h)
	 {
		if(document.getElementById(box).style.display == 'none')
		{
			document.getElementById(box).style.display = 'block';
			document.getElementById(h).style.background = '#a70001';
		}else
		{
			document.getElementById(box).style.display = 'none';
			document.getElementById(h).style.background = 'gray';
		}
		
	 }
		
	 function switchModule()
	 {
		var x = document.getElementById('switcher');
		var id = x.options[x.selectedIndex].value;
		var roleID = document.getElementById('roleID').value;
		
		ajax('<?php echo HTTP_PATH ?>users/module/'+id+'/'+roleID,'profiles')
	 }
	</script>