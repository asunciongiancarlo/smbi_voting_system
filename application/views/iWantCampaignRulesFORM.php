<div class="content">
	
	<div class="title-content">
		<h2><span style='text-transform:lowercase;'>i</span>Want Campaign Rules</h2>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 $POSMtype   = $CI->db->query("select * from  POSM_Type   ");
		 $POSMstatus = $CI->db->query("select * from POSM_Status  ");
		 $outlet     = $CI->db->query("select * from OUTLET_Status ");
		 
		 $brand      = $CI->db->query("select * from brands ");
		 $material_type   = $CI->db->query("select * from MATERIAL_Type ");
		 
		 if($_SESSION['super_admin']=='y')
			$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0";
		 else
			$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id=".$_SESSION['countryID'];
			
		 $country = $CI->db->query($Csql);
		 

		 function getPremium($pid=0,$selectedITEM='') 
		   {
			$CI         = & get_instance();
			$CI->load->database('default');
			$sql = "select * from premiumItemType as pt inner join premiumItemTypeRef as ptREF on pt.id = ptREF.childID where ptREF.parentID='$pid'";
			$premium   = $CI->db->query($sql);
			$premium   = $premium->result_array();
			$opt = "";
			
			foreach( $premium as $o)
			  {
			   $v = $o['id'];
			   $t = $o['premiumTypeName'];
			   $cid = $o['childID'];
			   
			   $optG =getPremium($cid,$selectedITEM); 
			   $s =  $selectedITEM == $v ? 'selected':"";
			   if($optG!='') $opt .= "<optgroup label='$t' >$optG</optgroup>";
			   else   	     $opt .= "<option value='$v' $s >$t</option>";
			  }
			return $opt; 
		   }
		   
		  if(isset($post)) extract($post); 
		 
		
		$action = HTTP_PATH.'users/iWantCampaignRules/insert';
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		
		?>

<?php 
	$CI =& get_instance();
	$CI->load->library('forms');						
	echo $CI->forms->form_header('SMBi','iWantCampaignRules',$action);
	
	$validation = "data-parsley-trigger='change' data-parsley-type='number'  placeholder='Integer'";
?>
		<div class='fl  searhPanel' style='width:174%;margin-top:-20px;'>
			    <h2 class='form' style='width:25%;margin-right:10px;'>COUNTRY</h2>
				  <select name='countryID' style='width:25%;margin-right:10px;'>
					  <?php foreach($country->result_array() as $o) 
							{ 
							 $v = $o['cID'];
							 $t = $o['countryName'];
							 echo "<option value='$v'> $t </option>";   
							}  
					  ?>
					</select>
				   <div style='clear:both;'></div>
			   
			   <h2 class='fl form' style='width:25%;margin-right:10px;'>FIELD NAME</h2>
			   <h2 class='fl form' style='width:20%;margin-right:20px;'>LOGICAL CONDITION</h2>
			   <h2 class='fl form' style='width:45%;margin-right:60px;'>VALUE</h2>
			   <div style='clear:both'></div>
			   <!-- POSMTypeID -->
			   <select name='POSMTypeID' class='fl' style='width:25%;margin-right:10px;'>
				  <option value='' > <?php echo $CI2->fv->label(4); ?> </option>    
				  <?php foreach( $POSMtype->result_array() as $o) 
						{ 
						 $v = $o['id'];
						 $t = $o['typeName'];
						 $s = isset($selPOSMType) ? ($selPOSMType==$v ? "selected":""):""; 
						 echo "<option value='$v' $s> $t </option>";   
						}  
				  ?>
				</select>
				<select name='POSMTypeCond' class='fl' style='width:20%;margin-right:20px;'>
					<option value=''   > Condition </option>  
					<option value='>'  > > 		   </option>  
					<option value='<'  > <		   </option>  
					<option value='>=' > >= 	   </option>  
					<option value='<=' > <= 	   </option>  
					<option value='==' > == 	   </option>  
				</select>
				
				<input type='text' name='POSMTypeVal' class='fl' style='width:10%;margin-right:60px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both;'></div>
				
				 <!-- POSMStatusID -->
				<select name='POSMStatusID' class='fl' style='width:25%;margin-right:10px;'>
				  <option value=''> ITEM STATUS </option>    
				  <?php foreach( $POSMstatus->result_array() as $o) 
						{ 
						 $v = $o['id'];
						 $t = $o['statusName'];
						 $s = isset($selPOSMStatus) ? ($selPOSMStatus==$v ? "selected":""):""; 
						 echo "<option value='$v' $s > $t </option>";   
						}  
				  ?>
				</select>
				<select  name='POSMStatusCond' class='fl' style='width:20%;margin-right:20px;'>
					<option value=''   > Condition </option>  
					<option value='>'  > > 		   </option>  
					<option value='<'  > <		   </option>  
					<option value='>=' > >= 	   </option>  
					<option value='<=' > <= 	   </option>  
					<option value='==' > == 	   </option>   
				</select>
				<input type='text' name='POSMStatusVal' class='fl' style='width:10%;margin-right:10px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both'></div>
				
				
				<!-- OUTLETsTATUSID -->
				 <select name='OUTLETStatusID' class='fl' style='width:25%;margin-right:10px;'>
				  <option value='' > <?php echo $CI2->fv->label(6); ?> </option>    
				  <?php foreach( $outlet->result_array() as $o) 
						{
						 $s = isset($selPOSMStatus) ? ($selPOSMStatus==$v ? "selected":""):"";								
						 $v = $o['id'];
						 $t = $o['statusName'];
						  $s = isset($seloutlet) ? ($seloutlet==$v ? "selected":""):""; 
						 echo "<option value='$v' $s > $t </option>";   
						}  
				  ?>
				</select>
				<select class='fl' name='OUTLETStatusCond' class='fl' style='width:20%;margin-right:20px;'>
					<option value=''   > Condition </option>  
					<option value='>'  > > 		   </option>  
					<option value='<'  > <		   </option>  
					<option value='>=' > >= 	   </option>  
					<option value='<=' > <= 	   </option>  
					<option value='==' > == 	   </option>   
				</select>
				<input type='text' name='OUTLETStatusVal' class='fl' style='width:10%;margin-right:10px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both'></div>
				
				
				<!-- PremiumTypeID -->
				 <select name='PremiumTypeID' class='fl' style='width:25%;margin-right:10px;'>
				  <option value='' > <?php echo $CI2->fv->label(7); ?> </option>    
				  <?php  
					echo getPremium(0,$selPremiumType); 
				  ?>
				</select>
				<select name='PremiumTypeCond' class='fl' style='width:20%;margin-right:20px;'>
					<option value=''   > Condition </option>  
					<option value='>'  > > 		   </option>  
					<option value='<'  > <		   </option>  
					<option value='>=' > >= 	   </option>  
					<option value='<=' > <= 	   </option>  
					<option value='==' > == 	   </option>   
				</select>
				<input type='text' name='PremiumTypeVal' class='fl' style='width:10%;margin-right:10px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both'></div>
				
				
				<!-- MaterialTypeID -->
				<select name='MaterialTypeID'  class='fl' style='width:25%;margin-right:10px;'>
				  <option value='' > <?php echo $CI2->fv->label(9); ?> </option>    
				  <?php foreach( $material_type->result_array() as $o) 
						{ 
						 $v = $o['id'];
						 $t = $o['materialName'];
						  $s = isset($selMaterial) ? ($selMaterial==$v ? "selected":""):""; 
						 echo "<option value='$v' $s > $t </option>";   
						}  
				  ?>
				</select>
				<select class='fl' name='MaterialTypeCond' style='width:20%;margin-right:20px;'>
					<option value=''   > Condition </option>  
					<option value='>'  > > 		   </option>  
					<option value='<'  > <		   </option>  
					<option value='>=' > >= 	   </option>  
					<option value='<=' > <= 	   </option>  
					<option value='==' > == 	   </option>   
				</select>
				<input type='text' name='MaterialTypeVal' class='fl' style='width:10%;margin-right:10px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both'></div>
				
				
				<!-- brandID -->
				<select name='brandID' class='fl' style='width:25%;margin-right:10px;'>
				  <option value='' > <?php echo $CI2->fv->label(3); ?> </option>    
				  <?php foreach( $brand->result_array() as $o) 
						{ 
						 $v = $o['id'];
						 $t = $o['brandName'];
						  $s = isset($selBrand) ? ($selBrand==$v ? "selected":""):""; 
						 echo "<option value='$v' $s > $t </option>";   
						}  
				  ?>
				</select>
				<select name='brandCond' class='fl' style='width:20%;margin-right:20px;'>
					<option value='' >  Condition </option>  
					<option value='>' > > </option>  
					<option value='<' > < </option>  
					<option value='>=' > >= </option>  
					<option value='<=' > <= </option>  
					<option value='==' > == </option>  
				</select>
				<input type='text' name='brandVal' class='fl' style='width:10%;margin-right:10px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both'></div>
		</div>
				


		</div>
	</div>

	
	<div class="clear"></div>
</div>
<?php echo $csrf; 
	  echo $CI->forms->buttons('GoPar','iWantCampaignRules');
	  echo "</form>";
	?>
	

	

