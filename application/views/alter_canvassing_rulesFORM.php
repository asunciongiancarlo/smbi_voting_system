<style>
ul.parsley-errors-list {
	height:0px;
}
</style>
<?php 
		$CI  = & get_instance();
		$CI->load->database('default');
	
		$action = HTTP_PATH."iLikeCampaign/alter_canvassing_rules/update/$campaignID";
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		
		$iLikeCanvassingRules = $CI->db->query("SELECT typeName, i.fieldID as POSM_TypeID ,extra_label, i.price_rangeID as priceID , 
			   i.cond1 as condition1, i.min_val as minimum_value, i.logical_operator as logicalOperator, i.cond2 as condition2, i.max_val as maximum_value 
			   FROM iLikeCanvassingRulesXref as i 
			   LEFT JOIN price_range ON price_range.id = i.price_rangeID 
			   LEFT JOIN POSM_Type   ON POSM_Type.id   = i.fieldID 
			   WHERE campaignID = $campaignID ORDER BY typeName ASC, price_rangeID ASC");
		$iLikeCanvassingRules = $iLikeCanvassingRules->result_array();
		
		$conditions = array(array('label'=>'[Select]','value'=>''),
						    array('label'=>'>','value'=>'>'),
						    array('label'=>'<','value'=>'<'),
						    array('label'=>'>=','value'=>'>='),
							array('label'=>'<=','value'=>'<='),
							array('label'=>'==','value'=>'==')); 
							
		$relation = array(array('label'=>'[Select]','value'=>''),
						    array('label'=>'AND','value'=>'AND'),
						    array('label'=>'OR','value'=>'OR')); 
							
	$CI =& get_instance();
	$CI->load->library('forms');						
	echo $CI->forms->form_header('SMBi','AlterCanvassingRulesFORM',$action);
?>
<div class="content">
	
	<div class="title-content">
		<h2>Alter <span style='text-transform: lowercase;'>i</span>Like Canvassing Rules</h2>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
<?php 
	$validation = "data-parsley-trigger='change' data-parsley-required='true' data-parsley-type='number'  placeholder='Integer'";
?>
		<div class='fl  searhPanel' style='width:174%;margin-top:-20px;'>
			   <input name='campaignID' id='campaignID' value='<?php echo $campaignID ?>' type='hidden'>
			   <input name='countryID'  id='countryID'  value='<?php echo $countryID ?>' type='hidden'>
			   <div style='clear:both;'></div>
			   <div id='loading' class='fl' style='margin-top:20px;display:none;'>
					<img src='<?php echo HTTP_PATH.'img/loading.gif'?>'> Please wait campaign is now recanvassing & saving new setting...
				</div>
				<div style='clear:both;'></div>
			   <h2 class='fl form' style='width:121px;'>   ITEM TYPE  		 	 </h2>
			   <h2 class='fl form' style='width:200px;'>   LEVEL  		 	 	 </h2>
			   <h2 class='fl form' style='width:8%;'>    1st CONDITION		 </h2>
			   <h2 class='fl form' style='width:6%;'>    MIN. VALUE		 	 </h2>
			   <h2 class='fl form' style='width:9%;'>    LOGICAL CONDITION 	 </h2>
			   <h2 class='fl form' style='width:8%;'>    2nd CONDITION 		 </h2>
			   <h2 class='fl form' style='width:8%;'>    MAX VALUE 			 </h2>
			   <div style='clear: both;height:10px;' ></div>
			  <?php 
			  foreach($iLikeCanvassingRules as $iLikeCanvassingRule)
			  { extract($iLikeCanvassingRule);
			    echo "<label class='fl' style='width:121px;'><b>$typeName </b>	  <input type='hidden' name='fieldIDs[]' 	   value='$POSM_TypeID'> </label>";
			    echo "<label class='fl' style='width:200px;'><b>$extra_label</b>  <input type='hidden' name='price_rangeIDs[]' value='$priceID'>     </label>";
				echo "<select name='cond1s[]' id='rel' class='fl' style='width:7%;margin-right:20px;'>";
					  foreach($conditions as $c)
					  {	extract($c);
						$s = ($value==$condition1) ? 'selected':'';
						echo "<option value='$value' $s>$label</option>";
					  }
				echo "</select>";
				echo "<input type='text' name='min_vals[]' value='$minimum_value' class='fl' style='width:4%;margin-right:20px;'> ";
				echo "<select name='logical_operators[]' id='lrel' class='fl' style='width:8%;margin-right:20px;'>";
						  foreach($relation as $r)
						  {	extract($r);
							$s = ($value==$logicalOperator) ? 'selected':'';
							echo "<option value='$value' $s>$label</option>";
						  }
				echo "</select>";
				echo "<select name='cond2s[]' id='rel' class='fl' style='width:7%;margin-right:20px;'>";
					  foreach($conditions as $c)
					  {	extract($c);
						$s = ($value==$condition2) ? 'selected':'';
						echo "<option value='$value' $s>$label</option>";
					  }
				echo "</select>";
				echo "<input type='text' name='max_vals[]' value='$maximum_value' class='fl' style='width:4%;margin-right:20px;'> <div style='clear: both;' ></div>";
			  }
			  
			  ?>
			 <div style='clear:both'></div>
		</div>
			
		</div>
	</div>

	
	<div class="clear"></div>
</div>
<?php  
	   $field   = "<div class='fl'>";
	   $field  .= "<input name='btnsubmit2' type='submit' class='nav-REMOTE-btn1 fl' value='Save' style='color:white;margin-top:20px;'> <br/><br/> ";
	   $field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:170px;margin-left:5px;'> *Update canvassing rules</p>";
	   echo $field  .= "</div>";
	  echo "</form>";
	?>
	
<script>

$("#AlterCanvassingRulesFORM").bind("keypress", function (e) {
    if (e.keyCode == 13) {
        return false;
    }
});

function alterCanvassingRules(){
$("#AlterCanvassingRulesFORM").parsley("validate");
if($('#AlterCanvassingRulesFORM').parsley().isValid()){
	var rel_value = document.getElementById('rel').value;
    var val_value = document.getElementById('val').value;
    var val_canvassing_rules = document.getElementById('num_canvassing_rules').value;
    var val_campaignID = document.getElementById('campaignID').value;
	
<?php if(count($iLikeCanvassingRules)==1){ ?>
	var formData = {rel:rel_value,val:val_value,num_canvassing_rules:val_canvassing_rules,campaignID:val_campaignID};
	$.ajax({
	  url : "<?php echo HTTP_PATH ?>iLikeCampaign/alter_canvassing_rules/test",
	  type: "POST",
	  data : formData,
	  success: function(data, textStatus, jqXHR){
		if(data=='Ok'){
			jConfirm("Are you sure you want to apply this canvassing rules?","Alert",function(r){
				if(r){ 
					document.getElementById('AlterCanvassingRulesFORM').submit();
					document.getElementById('loading2').style.display = 'block';
				}
			});
		}else{
			jAlert(data, 'Alert Dialog');
		}
	  },error: function(jqXHR, textStatus, errorThrown)
	  {
		jAlert('Wrong Configuration!', 'Alert Dialog');
	  }});
<?php }else{ ?>
	var val_lrel = document.getElementById('lrel').value;
    var val_rel2 = document.getElementById('rel2').value;
    var val_value2 = document.getElementById('val2').value;
	
	var formData = {rel:rel_value,val:val_value,num_canvassing_rules:val_canvassing_rules,campaignID:val_campaignID,lrel:val_lrel,rel2:val_rel2,val2:val_value2};
	$.ajax({
	  url : "<?php echo HTTP_PATH ?>iLikeCampaign/alter_canvassing_rules/test",
	  type: "POST",
	  data : formData,
	  success: function(data, textStatus, jqXHR){
		if(data=='Ok'){
			jConfirm("Are you sure you want to apply this canvassing rules?","Alert",function(r){
				if(r){ 
					document.getElementById('AlterCanvassingRulesFORM').submit();
					document.getElementById('loading2').style.display = 'block';
				}
			});
		}else{
			jAlert(data, 'Alert Dialog');
		}
	  },error: function(jqXHR, textStatus, errorThrown)
	  {
		jAlert('Wrong Configuration!', 'Alert Dialog');
	  }});
<?php } ?>
}
}
</script>	
	

	

