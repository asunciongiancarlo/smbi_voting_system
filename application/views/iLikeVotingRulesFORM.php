<div class="content">
	<div class="title-content">
		<h2><span style='text-transform:lowercase;'>i</span>Like Voting Rules</h2>
	</div>
	<div class="clear"></div>
	<div class="working_area">
		<div class="container2">
		<?php 
		 $fieldName="";
		 $fieldID=0;
		 $val="";
		 $cond1="";
		 $min_val="";
		 $logical_operator="";
		 $cond2="";
		 $max_val="";
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 $POSMtype   = $CI->db->query("select * from  POSM_Type   ");
		 $POSMstatus = $CI->db->query("select * from  POSM_Status  ");
		 $outlet     = $CI->db->query("select * from  OUTLET_Status ");
		 
		 $brand           = $CI->db->query("select * from brands ");
		 $material_type   = $CI->db->query("select * from MATERIAL_Type ");
	     
		 if(isset($post)) extract($post); 
		
		$action = HTTP_PATH.'users/iLikeVotingRules/insert';
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		
		if(isset($iLikeVotingRulesID)){
			$sql = "SELECT * FROM iLikeVotingRules WHERE iLikeVotingRules.id = $iLikeVotingRulesID";
			$sql = $CI->db->query($sql);
			$iLikeVotingRules = $sql->row();
			
			$countryID = $iLikeVotingRules->countryID;
			$fieldName = $iLikeVotingRules->fieldName;
			$fieldID   = $iLikeVotingRules->fieldID;
			$POSMTypeID= $iLikeVotingRules->fieldID;
			$price_rangeID = $iLikeVotingRules->price_rangeID;
			$val 	   = $iLikeVotingRules->val;
			$rel 	   = $iLikeVotingRules->rel;
			$cond1 			  = $iLikeVotingRules->cond1;
			$min_val 		  = $iLikeVotingRules->min_val;
			$logical_operator = $iLikeVotingRules->logical_operator;
			$cond2   		  = $iLikeVotingRules->cond2;
			$max_val 		  = $iLikeVotingRules->max_val;
			$action = HTTP_PATH."users/iLikeVotingRules/update/$iLikeVotingRulesID";
		}
		
		 if(isset($POST)) extract($POST);
		 //SELECT PRICE CATEGORY
		 $price_category  = $CI->db->query("select * from price_range WHERE POSMTypeID= $POSMTypeID ORDER BY xOrder ASC");
		 
		  if($_SESSION['super_admin']=='y')
			$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0";
		 else
			$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id=".$_SESSION['countryID'];
			
		 $country = $CI->db->query($Csql);
		 		   
			
		$conditions = array(array('label'=>'Condition','value'=>''),
							array('label'=>'>','value'=>'>'),
						    array('label'=>'<','value'=>'<'),
						    array('label'=>'>=','value'=>'>='),
							array('label'=>'<=','value'=>'<='),
							array('label'=>'==','value'=>'=='));
		$relation = array(array('label'=>'Condition','value'=>''),
						    array('label'=>'AND','value'=>'AND'),
						    array('label'=>'OR','value'=>'OR')); 
		?>

<?php 
	$CI =& get_instance();
	$CI->load->library('forms');						
	echo $CI->forms->form_header('SMBi','iLikeVotingRules',$action);
	
	$validation = "data-parsley-trigger='change' data-parsley-type='number'  placeholder='Integer'";
?>

		<div class='fl  searhPanel' style='width:170%;margin-top:-20px;'>
		       <?php
			    $CI =& get_instance();
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo "<div style='width:58%;margin:10px 0 -10px 0;'>";
					 echo $CI->alert->check($msg);
					echo "</div>";
				}
			  ?>
			  <h2 class='fl form' style='width:25%;margin-right:10px;'>COUNTRY		    </h2>
			  <h2 class='fl form' style='width:20%;margin-right:20px;'>ITEM TYPE 		</h2>
			  <h2 class='fl form' style='width:45%;margin-right:60px;'>			   		</h2>
			    <div style='clear:both;'></div>
			  <select name='countryID' style='width:25%;margin-right:10px;' disabled>
				  <?php foreach($country->result_array() as $o) 
						{ 
						 $v = $o['cID'];
						 $t = $o['countryName'];
						 if($countryID==$v OR $country_ID==$v) $s = "selected"; else  $s ="";
						 echo "<option value='$v' $s> $t </option>";   
						}  
				  ?>
				</select>
				<input type='hidden' name='countryID' value='<?php echo $countryID?>'>
			   <?php 
			   //ADD VOTING RULES
			   if(isset($countryID)) echo "<input type='hidden' name='countryID' value='$countryID'>";
			   ?>
			   <!-- POSMTypeID -->
			   <?php if(!isset($iLikeVotingRulesID) OR (isset($iLikeVotingRulesID) AND $fieldName=='POSMTypeID')){ ?>
			   <select name='POSMTypeID' class='fl' style='width:20%;margin-right:10px;' disabled>
				  <option value='' > <?php echo $CI2->fv->label(4); ?> </option>    
				  <?php foreach( $POSMtype->result_array() as $o) 
						{ 
						 $v = $o['id'];
						 $t = $o['typeName'];
						 $s = ($fieldID==$v OR $POSMTypeID==$v) ? "selected":"";
						 echo "<option value='$v' $s> $t </option>";   
						}  
				  ?>
				</select>
				
				<?php } ?>
			   <div style='clear:both;'></div>
			
				<div style='clear:both'></div>
				<h2 class='fl form' style='width:25%;margin-right:10px;'>LEVEL		   </h2>
			    <div style='clear:both'></div>
				<select name='price_rangeID' class='fl' style='width:25%;margin-right:10px;'>
				<option value='' > <?php echo $CI2->fv->label(86); ?> </option>    
				  <?php foreach($price_category->result_array() as $p) 
						{ 
						 $v = $p['id'];
						 
						 if($p['logical_operator']!="") 
						  $t = $p['campaign_label'].": ".$p['level_name']." (USD ".$p['cond1']." ".$p['min_val']." ".$p['logical_operator']." USD  ".$p['cond2']." ".$p['max_val'].")";
						 else					  
						  $t = $p['campaign_label'].": ".$p['level_name']." (USD ".$p['cond1']." ".$p['min_val']." ".$p['logical_operator']."   ".$p['cond2']." ".$p['max_val'].")";
						 
						 $s = ($v==$price_rangeID OR $v==$POST_price_rangeID) ? "selected":"";
						 echo "<option value='$v' $s> $t </option>";   
						}  
				  ?>
				</select>
				<?php 
			    //ADD VOTING RULES
			    if(isset($POSMTypeID)) echo "<input type='hidden' name='POSMTypeID' value='$POSMTypeID'>";
			    ?>
				<div style='clear:both;'></div>
			    <h2 class='fl form' style='width:11%;margin-right:10px;'> CONDITION 1</h2>
			    <h2 class='fl form' style='width:8%;margin-right:0px;'>MIN VALUE	 </h2>
			    <h2 class='fl form' style='width:12%;margin-right:17px;'>  LOGICAL OPERATOR</h2>
			    <h2 class='fl form' style='width:10%;margin-right:10px;'> CONDITION 2</h2>
			    <h2 class='fl form' style='width:10%;margin-right:20px;'> MAX VALUE</h2>
				<div style='clear:both;'></div>
			   <!-- CONDITION 1 -->
			   <select name='cond1' class='fl' style='width:10%;margin-right:26px;'>
					<?php 
						foreach($conditions as $c)
						{	extract($c);
							$s = ($value==$cond1) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						}
					?>
				</select>
				<input type='text' name='min_val' value="<?php echo $min_val ?>" class='fl' style='width:6%;margin-right:60px;height:3%;'></input>
				<select name='logical_operator' class='fl' style='width:11%;margin-right:34px;margin-left: -30px;'>
					<?php 
						foreach($relation as $r)
						{	extract($r);
							$s = ($value==$logical_operator) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						}
					?>  
				</select>
				<select name='cond2' class='fl' style='width:9%;margin-right:27px;'>
					<?php 
						foreach($conditions as $c)
						{	extract($c);
							$s = ($value==$cond2) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						}
					?>
				</select>
				<input type='text' name='max_val' value="<?php echo $max_val ?>" class='fl' style='width:5%;margin-right:60px;height:3%;'></input>
				
				<div style='clear:both'></div>
				<label style="padding-bottom:10px; padding-top:5px;">To use percentage voting rules please input a value in float format: Ex. <b>0.10, 0.15, 0.5</b></label>
				<?php
				if(isset($POST) AND !isset($iLikeVotingRulesID)){ 
				extract($POST);
				$val 	 = "";
				$fieldID = "";
				$rel     = "";
				} 
				?>
				<div style='clear:both'></div>
		</div>
	
		</div>
	</div>

	
	<div class="clear"></div>
</div>
<?php echo $csrf;
	  $CI =& get_instance();
	  $CI->load->library('forms');	
	  echo $CI->forms->buttons('GoPar','iLikeCampaignRules');
	  echo "</form>";
	?>
	

	


	