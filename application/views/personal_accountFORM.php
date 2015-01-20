    <div class="content">
		
    	<div class="title-content">
        	<h2> My Account </h2>
        </div>
        
        <div class="clear"></div>
	
        <div class="working_area form">
		
			<?php
				$CI =& get_instance();
				
				
				//ACTION STATEMENT
				$action = HTTP_PATH ."users/admin_users/insert";
				
				//ISSET ID
				$roleID 		= ""; 
				$countryID 		= ""; 
				$password 		= ""; 
				$uname 			= ""; 
				$full_name 		= ""; 
				$email_address  = ""; 
				$Fields001 		= ""; 
				$Fields002 		= "";
				$Fields003 		= ""; 
				$Fields004 		= ""; 
				$Fields005 		= "";
				$Fields006 		= ""; 
				$admin_userID	= 0;
				$super_admin	= "";
				
				if(isset($POST))
				{
					extract($POST);
				}
				
				
				if(isset($id))
				{
					$action = HTTP_PATH ."users/personal_account/update/".$id;
					$sql = $CI->db->query("SELECT * FROM admin_users WHERE id = $id");
					$sql = $sql->result_array();
					extract($sql);
					
					$admin_userID  = $id;
					$countryID     = $sql[0]['countryID'];
					$password 	   = $sql[0]['password'];
					$uname 		   = $sql[0]['uname'];
					$full_name 	   = $sql[0]['full_name'];
					$email_address = $sql[0]['email_address'];
					$Fields001 	   = $sql[0]['Fields001'];
					$Fields002 	   = $sql[0]['Fields002'];
					$Fields003 	   = $sql[0]['Fields003'];
					$Fields004 	   = $sql[0]['Fields004'];
					$Fields005 	   = $sql[0]['Fields005'];
					$Fields006 	   = $sql[0]['Fields006'];
					$super_admin   = $sql[0]['super_admin'];
				}
			?>
			<div class="container2" style='width:30%;'>
			<?php 
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}
				
			
				$CI =& get_instance();
				$CI->load->library('forms');
				$CI2 =& get_instance();
				$CI2->load->library('fv');
				
				echo $CI->forms->form_header('SMBi','accountFORM',$action);
				
				$countryID = $_SESSION['countryID'];
				echo "<input type='hidden' name='countryID' value='$countryID'>";
				
				if($_SESSION['super_admin']!='y'){
					echo $CI->forms->select('countryID','country','countryName',$CI2->fv->label(10),$countryID,$CI2->fv->v(10),'disabled');
				}else{
					echo "<h2> COUNTRY NAME</h2>";
					echo "<select disabled='true' style='width:100%;float:left'>
							<option value=''> Multi-Country </option>
						  </select>";
				}
				if($CI2->fv->sh(33)=='y')	
					echo $CI->forms->form_fields2('text','uname',$uname,$CI2->fv->label(33),$CI2->fv->v(33),'disabled');
				
				if($admin_userID!=0){
					echo $CI->forms->form_fields2('password','oldpassword','','OLD PASSWORD','r');	
					echo $CI->forms->form_fields2('password','password','','NEW PASSWORD','password_1st_field');	
					echo $CI->forms->form_fields2('password','password','','CONFIRM PASSWORD','password_2nd_field');	
				}else{
					echo $CI->forms->form_fields2('password','password','',$CI2->fv->label(34),$CI2->fv->v(34));	
				}
				if($CI2->fv->sh(35)=='y')
					echo $CI->forms->form_fields2('text','fullname',$full_name,$CI2->fv->label(35),$CI2->fv->v(35),'disabled');
				if($CI2->fv->sh(36)=='y')
					echo $CI->forms->form_fields2('text','email_address',$email_address,$CI2->fv->label(36),$CI2->fv->v(36),'disabled');
				if($CI2->fv->sh(37)=='y')
					echo $CI->forms->form_fields2('text','Fields001',$Fields001,$CI2->fv->label(37),$CI2->fv->v(37),'disabled');
				if($CI2->fv->sh(38)=='y')
					echo $CI->forms->form_fields2('text','Fields002',$Fields002,$CI2->fv->label(38),$CI2->fv->v(39),'disabled');
				if($CI2->fv->sh(39)=='y')
					echo $CI->forms->form_fields2('text','Fields003',$Fields003,$CI2->fv->label(39),$CI2->fv->v(39),'disabled');
				if($CI2->fv->sh(40)=='y')
					echo $CI->forms->form_fields2('text','Fields004',$Fields004,$CI2->fv->label(40),$CI2->fv->v(40),'disabled');
				if($CI2->fv->sh(41)=='y')
					echo $CI->forms->form_fields2('text','Fields005',$Fields005,$CI2->fv->label(41),$CI2->fv->v(41),'disabled');
				if($CI2->fv->sh(42)=='y')
				echo $CI->forms->form_fields2('text','Fields006',$Fields006,$CI2->fv->label(42),$CI2->fv->v(42),'disabled');
			
			?>
            </div>
			
        </div>
		
        <div class="clear"></div>
    </div>
	
	<?php 
		echo $CI->forms->buttons('GoPar','accountFORM');
		echo "</form>";
	?>
	
	
	
	
	
	