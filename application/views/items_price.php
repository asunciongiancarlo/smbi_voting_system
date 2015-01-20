<?php 
	$CI = & get_instance();
	$CI->load->database('default');
	$POSMtype   = $CI->db->query("select * from  POSM_Type");
	
	$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id= $countryID";
	$country = $CI->db->query($Csql);
	$country = $country->row();
	
	$CI2 =& get_instance();
	$CI2->load->library('forms');
	
	extract($POST);
?>
<div class="content">
	
	<div class="fl title-content">
		<h2>List of Items</h2>
	</div>
	
	<div class="clear"></div>
	
	<div class="working_area">	
		<div class="container2" style="margin-top:-10px;">
		<label class='lbl_title'> Country: <?php echo $country->countryName; ?> | Year: <?php echo $year ?> | Month: <?php echo $month ?></label>
		<form name="SMBi2" id="statusTable" action='<?php echo HTTP_PATH."report/items_price/$typeView/$countryID/$month/$year" ?>' method="POST"> 
		  <h2 class='fl form' style='width:9%;'>  Item Code			</h2>
		  <h2 class='fl form' style='width:17%;margin: 0 0 0 59px;'>Name				</h2>
		  <h2 class='fl form' style='width:11%;'>   Type				</h2>
		  <h2 class='fl form' style='width:7%;margin-right:2px;'>   LP From:			</h2>
		  <h2 class='fl form' style='width:10%;margin-right:2px;'>   <input type='text' name='localPriceFrom' value='<?php if(isset($localPriceFrom)) echo $localPriceFrom; ?>'></h2>
		  <h2 class='fl form' style='width:8%;margin-right:2px;'>   USD From:			</h2>
		  <h2 class='fl form' style='width:7%;margin-right:2px;'>   <input type='text' name='USDFrom' value='<?php if(isset($USDFrom)) echo $USDFrom; ?>'></h2>
		  	  
		  <h2 class='fl form' style='width:5%;'>   From: </h2>	
		  <h2 class='fl form' style='width:10%;'>   <input class='fl' type='text' name='DateFrom' value='<?php if(isset($DateFrom)) echo $DateFrom; ?>' id='datepicker'> </h2>	  
		 <h2 class='fl form' style='width:9%;margin-bottom:0px;margin-left:4px;'> Publish: </h2>	
		 
		  <div style='clear:both'></div>
		  <input type='text' name='itemCode' value='<?php if(isset($itemCode)) echo $itemCode; ?>' class='fl'  style='width:11%;'> 
		  <input type='text' name='itemName' value='<?php if(isset($itemName)) echo $itemName;  ?>' class='fl'  style='width:16%;margin: 0 0 0 18px;'> 
		  
		  <select class='sel fl' name='selPOSMType' style='width:12%;'>
		  <option value='' > Select Type </option>    
		  <?php foreach( $POSMtype->result_array() as $o) 
				{ 
				 $v = $o['id'];
				 $t = $o['typeName'];
				 $s = isset($selPOSMType) ? ($selPOSMType==$v ? "selected":""):""; 
				 echo "<option value='$v' $s> $t </option>";   
				}  
		  ?>
		  </select>
		  
		  <h2 class='fl form' style='width:6%;'> LP  To: </h2>	
		  <h2 class='fl form' style='width:10%;'>   <input class='fl' type='text' name='localPriceTo' value='<?php if(isset($localPriceTo)) echo $localPriceTo; ?>'> </h2>
		  
		   <h2 class='fl form' style='width:9%;'> USD  To: </h2>	
		  <h2 class='fl form' style='width:7%;'>   <input class='fl' type='text' name='USDTo' value='<?php if(isset($USDTo)) echo $USDTo; ?>'> </h2>
		  
		  		   
		  <h2 class='fl form' style='width:5%;'>   To: </h2>	
		  <h2 class='fl form' style='width:10%;'>   <input class='fl' type='text' name='DateTo' value='<?php if(isset($DateTo)) echo $DateTo; ?>' id='datepicker2'> </h2>
		  
		   
		   <select class='fl' name='publish'  style='width:7%;margin-left:4px;'>  
		  <?php 
			$opt = array(
						array('val'=>'',    'yn'=>'All'),
						array('val'=>'y',   'yn'=>'Yes'),
						array('val'=>'n',   'yn'=>'No'));
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $yn = $o['yn'];
			 $s = ($publish==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $yn </option>";   
			}
		  ?>
		  </select>
		  <div class='cl' style='margin-top:10px;'></div>
		  <input type='submit' name='Submit' value='Submit' style='margin: 4px 0 5px 949px;'>
		</form>  
		
		<?php 
			echo $table;
		?>
		</div>
		
	</div>
	   
	<div class="clear"></div>
</div>
		
<script type="text/javascript">

</script>