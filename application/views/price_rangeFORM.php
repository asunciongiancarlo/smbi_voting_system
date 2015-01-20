<div class="content">
	<div class="title-content">
		<h2>US Dollar - Price Range</h2>
	</div>
	<div class="clear"></div>
	<div class="working_area">
		<div class="container2">
		<?php 
		 $POSMTypeID="";
		 $xOrder="";
		 $level_name="";
		 $campaign_label="";
		 $max_val="";
		 $min_val="";
		 $extra_label="";
		 
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 if(isset($post)) extract($post); 
		 $action = HTTP_PATH.'users/price_range/insert';
		 $CI2 =& get_instance();
		 $CI2->load->library('fv');
		 
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 if(isset($ParamPOSMTypeID)){ 
		 $POSMtype   = $CI->db->query("select * from  POSM_Type  WHERE id = $ParamPOSMTypeID");
		 $POSMTypeID = $ParamPOSMTypeID;
		 }
		 else{ 						 
		 $POSMtype   = $CI->db->query("select * from  POSM_Type");
		 }
		 
		if(isset($price_rangeID)){
			$sql   = "SELECT * FROM price_range WHERE price_range.id = $price_rangeID";
			$sql   = $CI->db->query($sql);
			$range = $sql->row();
			
			$POSMTypeID 	  = $range->POSMTypeID;
			$xOrder 		  = $range->xOrder;
			$level_name 	  = $range->level_name;
			$extra_label 	  = $range->extra_label;
			$campaign_label   = $range->campaign_label;
			$cond1 			  = $range->cond1;
			$min_val 		  = $range->min_val;
			$logical_operator = $range->logical_operator;
			$cond2   		  = $range->cond2;
			$max_val 		  = $range->max_val;
			$action = HTTP_PATH."users/price_range/update/$price_rangeID";
		}
		
		if(isset($POST)){ 
		extract($POST);
		$val = $value;
		$fieldID = $POST_FieldID;
		$rel = $POST_rel;
		}
		
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
	
	$validation = "data-parsley-required='true'";
?>

		<div class='fl  searhPanel' style='width:170%;margin-top:10px;'>
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
			  <h2 class='form fl' style='width:3%;margin-right:10px;'>ORDER: </h2>
			  <input type='text' name='xOrder' value="<?php echo $xOrder ?>" class='fl' style='width:2%;margin-right:10px;height:3%;'></input>
			  
			  <h2 class='form fl' style='width:6%;margin-right:10px;'>LEVEL NAME: </h2>
			  <input type='text' name='level_name' value="<?php echo $level_name ?>" class='fl' style='width:10%;margin-right:10px;height:3%;margin-left: -20px;'></input>
			  
			  <h2 class='form fl' style='width:6%;margin-right:10px;'>EXTRA LABEL: </h2>
			  <input type='text' name='extra_label' value="<?php echo $extra_label ?>" class='fl' style='width:10%;margin-right:10px;height:3%;margin-left: -20px;'></input>
			  
			  <h2 class='form fl' style='width:9%;margin-right:-12px;'>CAMPAIGN LABEL: </h2>
			  <input type='text' name='campaign_label' value="<?php echo $campaign_label ?>" class='fl' style='width:9%;margin-right:10px;height:3%;'></input>
			 
			   <div style='clear:both;'></div>
			   
			   <h2 class='fl form' style='width:10%;margin-right:10px;'> POSM TYPE</h2>
			   <h2 class='fl form' style='width:10%;margin-right:10px;'> CONDITION</h2>
			   <h2 class='fl form' style='width:11%;margin-right:-78px;'>MIN VALUE</h2>
			   <h2 class='fl form' style='width:9%;margin-right:17px;'>  LOGICAL OPERATOR</h2>
			   <h2 class='fl form' style='width:10%;margin-right:10px;'> CONDITION</h2>
			   <h2 class='fl form' style='width:20%;margin-right:20px;'> MAX VALUE</h2>
			   
			   <div style='clear:both'></div>
			   <select name='POSMTypeID' class='fl' style='width:10%;margin-right:10px;'>  
				  <?php foreach( $POSMtype->result_array() as $o) 
						{ 
						 $v = $o['id'];
						 $t = $o['typeName'];
						 $s = ($POSMTypeID==$v) ? "selected":"";
						 echo "<option value='$v' $s> $t </option>";   
						}  
				  ?>
				</select>
			   
			   <!-- CONDITION 1 -->
			   <select name='cond1' class='fl' style='width:9%;margin-right:26px;'>
					<?php 
						foreach($conditions as $c)
						{	extract($c);
							$s = ($value==$cond1) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						}
					?>
				</select>
				<input type='text' name='min_val' value="<?php echo $min_val ?>" class='fl' style='width:5%;margin-right:60px;height:3%;'></input>
				<select name='logical_operator' class='fl' style='width:8%;margin-right:34px;margin-left: -30px;'>
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
		</div>
	
		</div>
	</div>

	
	<div class="clear"></div>
</div>
<?php echo $csrf;
	  $CI =& get_instance();
	  $CI->load->library('forms');	
	  echo $CI->forms->buttons('GoPar','iLikeVotingRules');
	  echo "</form>";
	?>
	

	


	