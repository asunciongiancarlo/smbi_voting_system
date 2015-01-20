<div class="content">
	
	<div class="title-content">
		<h2>iLike Campaign Rules</h2>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php
		 $countryID=0;
		 $fieldName="";
		 $fieldID=0;
		 $val="";
		
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 $POSMtype   = $CI->db->query("select * from  POSM_Type   ");
		 $POSMstatus = $CI->db->query("select * from POSM_Status  ");
		 $outlet     = $CI->db->query("select * from OUTLET_Status ");
		 
		 $brand      	  = $CI->db->query("select * from brands  ORDER BY brandName ASC");
		 $material_type   = $CI->db->query("select * from MATERIAL_Type ");
	
		 
		 if($_SESSION['super_admin']=='y')
			$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 ORDER BY countryName ASC";
		 else
			$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id=".$_SESSION['countryID']." ORDER BY countryName ASC";
			
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
		 
		
		$action = HTTP_PATH.'users/iLikeCampaignRules/insert';
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		
		if(isset($iLikeCampaignRulesID)){
			$sql = "SELECT * FROM iLikeCampaignRules WHERE iLikeCampaignRules.id = $iLikeCampaignRulesID";
			$sql = $CI->db->query($sql);
			$iLikeCampaignRules = $sql->row();
			
			$countryID = $iLikeCampaignRules->countryID;
			$fieldName = $iLikeCampaignRules->fieldName;
			$fieldID   = $iLikeCampaignRules->fieldID;
			$val 	   = $iLikeCampaignRules->val;
			$rel 	   = $iLikeCampaignRules->rel;
			
			$action = HTTP_PATH."users/iLikeCampaignRules/update/$iLikeCampaignRulesID";
		}
		
		$conditions = array(array('label'=>'Condition','value'=>''),
						    array('label'=>'>=','value'=>'>='),
							array('label'=>'<=','value'=>'<='),
							array('label'=>'==','value'=>'==')); 
		
		?>

<?php 
	$CI =& get_instance();
	$CI->load->library('forms');						
	echo $CI->forms->form_header('SMBi','iLikeCampaignRules',$action);
	$validation = "data-parsley-trigger='change' data-parsley-type='number'  placeholder='Integer'";
?>
		<div class='fl  searhPanel' style='width:174%;margin-top:-20px;'>
		      <h2 class='form' style='width:25%;margin-right:10px;'>COUNTRY</h2>
			  <select name='countryID' style='width:25%;margin-right:10px;' onchange='switchBrand(this)' data-parsley-trigger="change" data-parsley-required="true" placeholder="Required field">
				  <option value=''>SELECT</option>
				  <?php foreach($country->result_array() as $o) 
						{ 
						 $v = $o['cID'];
						 $t = $o['countryName'];
						 $s = ($countryID==$v) ? "selected" : "";
						 echo "<option value='$v' $s> $t </option>";   
						}  
				  ?>
				</select>
			   <div style='clear:both;height: 10px;'></div>
			   
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
						 $s = ($fieldID==$v) ? "selected":"";
						 echo "<option value='$v' $s> $t </option>";   
						}  
				  ?>
				</select>
				<select name='POSMTypeCond' class='fl' style='width:20%;margin-right:20px;'>
					<?php 
						foreach($conditions as $c)
						{	extract($c);
							$s = ($value==$rel) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						}
					?>
				</select>
				
				<input type='text' name='POSMTypeVal' value="<?php echo $val ?>" class='fl' style='width:10%;margin-right:60px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both;'></div>
				
				 <!-- POSMStatusID -->
				<select name='POSMStatusID' class='fl' style='width:25%;margin-right:10px;'>
				  <option value=''> ITEM STATUS </option>    
				  <?php foreach( $POSMstatus->result_array() as $o) 
						{ 
						 $v = $o['id'];
						 $t = $o['statusName'];
						 $s = ($fieldID==$v) ? "selected":"";
						 echo "<option value='$v' $s > $t </option>";   
						}  
				  ?>
				</select>
				<select  name='POSMStatusCond' class='fl' style='width:20%;margin-right:20px;'>
					<?php 
						foreach($conditions as $c)
						{	extract($c);
							$s = ($value==$rel) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						}
					?> 
				</select>
				<input type='text' name='POSMStatusVal' value="<?php echo $val ?>" class='fl' style='width:10%;margin-right:10px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both'></div>
				
				
				<!-- OUTLETsTATUSID -->
				 <select name='OUTLETStatusID' class='fl' style='width:25%;margin-right:10px;'>
				  <option value='' > <?php echo $CI2->fv->label(6); ?> </option>    
				  <?php foreach( $outlet->result_array() as $o) 
						{							
						 $v = $o['id'];
						 $t = $o['statusName'];
						 $s = ($fieldID==$v) ? "selected":"";
						 echo "<option value='$v' $s > $t </option>";   
						}  
				  ?>
				</select>
				<select class='fl' name='OUTLETStatusCond' class='fl' style='width:20%;margin-right:20px;'>
					<?php 
						foreach($conditions as $c)
						{	extract($c);
							$s = ($value==$rel) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						}
					?> 
				</select>
				<input type='text' name='OUTLETStatusVal' value="<?php echo $val ?>" class='fl' style='width:10%;margin-right:10px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both'></div>
				
				
				<!-- PremiumTypeID -->
				 <select name='PremiumTypeID' class='fl' style='width:25%;margin-right:10px;'>
				  <option value='' > <?php echo $CI2->fv->label(7); ?> </option>    
				  <?php  
					echo getPremium(0,$selPremiumType); 
				  ?>
				</select>
				<select name='PremiumTypeCond' class='fl' style='width:20%;margin-right:20px;'>
					<?php 
						foreach($conditions as $c)
						{	extract($c);
							$s = ($value==$rel) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						}
					?>  
				</select>
				<input type='text' name='PremiumTypeVal' value="<?php echo $val ?>" class='fl' style='width:10%;margin-right:10px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both'></div>
				
				
				<!-- MaterialTypeID -->
				<select name='MaterialTypeID'  class='fl' style='width:25%;margin-right:10px;'>
				  <option value='' > <?php echo $CI2->fv->label(9); ?> </option>    
				  <?php foreach( $material_type->result_array() as $o) 
						{ 
						 $v = $o['id'];
						 $t = $o['materialName'];
						 $s = ($fieldID==$v) ? "selected":"";
						 echo "<option value='$v' $s > $t </option>";   
						}  
				  ?>
				</select>
				<select class='fl' name='MaterialTypeCond' style='width:20%;margin-right:20px;'>
					<?php 
						foreach($conditions as $c)
						{	extract($c);
							$s = ($value==$rel) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						}
					?>  
				</select>
				<input type='text' name='MaterialTypeVal' value="<?php echo $val ?>" class='fl' style='width:10%;margin-right:10px;height:3%;' <?php echo $validation; ?>></input>
				<div style='clear:both'></div>
				
				
				<!-- brandID --> 
				<div id='BrandDiv'>
				<select name='brandID' class='fl' style='width:25%;margin-right:10px;'>
				  <option value='' > <?php echo $CI2->fv->label(3); ?> </option>    
				  <?php foreach( $brand->result_array() as $o) 
						{ 
						 $v = $o['id'];
						 $t = $o['brandName'];
						 $s = ($fieldID==$v) ? "selected":"";
						 echo "<option value='$v' $s > $t </option>";   
						}  
				  ?>
				</select>
				</div>
				
				<select name='brandCond' class='fl' style='width:20%;margin-right:20px;'>
					<?php 
						foreach($conditions as $c)
						{	extract($c);
							$s = ($value==$rel) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						}
					?> 
				</select>
				
				<input type='text' name='brandVal' value="<?php echo $val ?>" class='fl' style='width:10%;margin-right:10px;height:3%;' <?php echo $validation; ?>></input>
		
				<div style='clear:both'></div>
		</div>
	
		<p>0 as value and no Relational Operator Means ALL item in Database </p>
		</div>
	</div>

	
	<div class="clear"></div>
</div>
<?php echo $csrf; 
	  echo $CI->forms->buttons('GoPar','iLikeCampaignRules');
	  echo "</form>";
	?>
	<style>
	ul.parsley-errors-list {
	width: 200px!important;
	height: 0px!important;
	}
	</style>
	
	<script  type="text/javascript">  
	  
	  function switchBrand(sel)
	  {
		var countryID = sel.options[sel.selectedIndex].value;
		var a = $.ajax({
			url: '<?php echo HTTP_PATH ?>generate_field/Brand_per_BU/'+countryID,
			async: false
		}).responseText;
		
		document.getElementById('BrandDiv').innerHTML = a;
	  }
	 </script>
	

	

