<div class="content">
	
	<div class="title-content">
		<h2><span style='text-transform:lowercase;'>i</span>Want Campaign Rules</h2>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
			$CI =& get_instance();
					
			//MESSAGE ALERT
			if(isset($msg)){
				$CI->load->library('alert');
				echo $CI->alert->check($msg);
			}
		
		if($ADD){
		?>
		<a href="<?php echo HTTP_PATH ."users/iWantCampaignRules/add" ?>"> 
			<div class="sub-link">
				<ul>
					<li> <img src="<?php echo HTTP_PATH ?>img/plus.png" width="31" height="31"> </li>
					<li> <h5>Add<br/>Rule</h5> </li>
				</ul>
			</div>
		</a>
		<?php } ?>
		<table cellpadding="0" cellspacing="0" style="width:150%;">
		<tr style="border-radius: 6px;">
			<th style="width:150px;text-align:center;"> FIELD NAME 	     </th> 
			<th style="text-align:center;">				VALUE 		     </th> 
			<th style="width:150px;text-align:center;"> LOGICAL OPERATOR </th> 
			<th style="text-align:center;"> 			ITEMS 		   	 </th> 
			<th style="text-align:center;"> 			COUNTRY 		 </th> 
			<th style="text-align:center;"> 			ACTION 		   	 </th>   
		</tr>
		<?php
			$CI2 =& get_instance();
			$CI2->load->library('fv');
			
		
			$x=0; 
			$table='';
			foreach($iWantCampaignRules as $iCR)
			{
				$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
				extract($iCR);
				switch($fieldName){
					case("POSMTypeID"):
						$tableName	= $CI2->fv->label(4);;
						$fieldName  = 'typeName';
						$table 		= 'POSM_Type';
					break;
					case("POSMStatusID"):
						$tableName	= 'ITEM STATUS';
						$fieldName  = 'statusName';
						$table 		= 'POSM_Status';
					break;
					case("OUTLETStatusID"):
						$tableName	= $CI2->fv->label(6); ;
						$fieldName  = 'statusName';
						$table 		= 'OUTLET_Status';
					break;
					case("PremiumTypeID"):
						$tableName	= $CI2->fv->label(7);;
						$fieldName  = 'premiumTypeName';
						$table 		= 'premiumItemType';
					break;
					case("MaterialTypeID"):
						$tableName	= $CI2->fv->label(9);;
						$fieldName  = 'materialName';
						$table 		= 'MATERIAL_Type';
					break;
					case("brandID"):
						$tableName	= $CI2->fv->label(3);;
						$fieldName  = 'brandName';
						$table 		= 'brands';
					break;
				}
				
				$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 1");
				$row 		= $query->row();
				$name_Field = $row->$fieldName;
				
				echo "<tr>";
					echo "<td $c style='text-align:left;'>".$tableName."</td>";
					echo "<td $c style='text-align:left;'>". $name_Field ."</td>";
					echo "<td $c >". $rel ."</td>";
					echo "<td $c >". $val ."</td>";
					echo "<td $c >". $countryName ."</td>";
					echo "<td $c style='text-align:center;'>";
					if($DELETE)	
						echo "<a style='cursor:pointer' onclick='deleteOneItem($iWantCampaignRulesID)'>Delete</a> </td>";
				echo "</tr>";
			}
		?>
		</table>
		<br/>
		</div>
	</div>
	   
	<div class="clear"></div>
</div>

<script type="text/javascript">
	function deleteOneItem(id)
	{	
		jConfirm("Are you sure you want to delete this iWant Campaign Rule?","Alert",function(r){
			if(r) window.location = "<?php echo HTTP_PATH ?>users/iWantCampaignRules/deleteOneItem/"+ id;
		});
	}
</script>
	

	

