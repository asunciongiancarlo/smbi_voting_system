<div class="content">
	
	<div class="f title-content">
		<h2>US Dollar - Price Range</h2>
	</div>
	<div style="float:right;margin: 16px;">
		
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
			$CI =& get_instance();
			$CI->load->database('default');	
			//MESSAGE ALERT
			if(isset($msg)){
				$CI->load->library('alert');
				echo $CI->alert->check($msg);
			}
		?>
		<table cellpadding="0" cellspacing="0" style="width:150%;" id="large">
		<?php
			$CI2 =& get_instance();
			$CI2->load->library('fv');
					
			$x=0; 
			$table='';
			$tName='';
			foreach($POSMTypes as $POSMType)
			{extract($POSMType);
				//HEADER
				if($typeName!=$tName){
				if($ADD) $img = "<a href='".HTTP_PATH."users/price_range/add/$POSM_TypeID' style='color:white;'><img src='".HTTP_PATH."img/add-item.png' style='margin-left:21px;cursor:pointer;'> Add Price Range </a>";
				echo "<tr>   
							<td colspan='9' style='text-align:left;background-color:#330404;color:white;' class='alter'><b>$typeName</b></td> 
							<td style='text-align:left;width: 153px;background-color:#330404;color:white;' class='alter'> $img </td>  
					 </tr>";
				echo "<tr style='border-radius: 6px;'>
						<th style='width:50px;text-align:center;font-size: 12px;padding: 0;'> 			ORDER 	     		 </th> 
						<th style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			LEVEL NAME 	     	 </th> 
						<th style='width:175px;text-align:center;font-size: 12px;padding: 0;'> 			EXTRA LABEL 	     </th> 
						<th style='text-align:center;font-size: 12px;padding: 0;'>						CAMPAIGN LABEL 		 </th> 
						<th style='width:103px;text-align:center;font-size: 12px;padding: 0;'> 			CONDITION 1 		 </th> 
						<th style='text-align:center;font-size: 12px;padding: 0;'> 						MIN. VALUE 		   	 </th> 
						<th style='text-align:center;font-size: 12px;padding: 0;'> 						LOGICAL OPERATOR     </th> 
						<th style='width:150px;text-align:center;font-size: 12px;padding: 0;'> 			CONDITION 2 		 </th>   
						<th style='text-align:center;font-size: 12px;padding: 0;'> 						MAX. VALUE 		 	 </th>   
						<th style='width: 141px;text-align:center;font-size: 12px;padding: 0;'> 		ACTION 		 	 	 </th>   
					</tr>";
				}
				//RANGES
				$sql = $CI->db->query("SELECT *,price_range.id as pID  FROM price_range WHERE POSMTypeID = $POSM_TypeID ORDER BY xOrder ASC");
				$price_ranges = $sql->result_array();
				foreach($price_ranges as $price_range)
				{
				extract($price_range);
				$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
				echo "<tr>";
					echo "<td $c style='text-align:center;'>". $xOrder 		   ."</td>";
					echo "<td $c style='text-align:left;'>".   $level_name	   ."</td>";
					echo "<td $c style='text-align:left;'>".   $extra_label	   ."</td>";
					echo "<td $c style='text-align:left;'>".   $campaign_label   ."</td>";
					echo "<td $c style='text-align:center;'>". $cond1			   ."</td>";
					echo "<td $c style='text-align:center;'>". $min_val		   ."</td>";
					echo "<td $c style='text-align:center;'>". $logical_operator ."</td>";
					echo "<td $c style='text-align:center;'>". $cond2 		   ."</td>";
					echo "<td $c style='text-align:center;'>". $max_val 		   ."</td>";
					echo "<td $c style='text-align:center;'>";
					if($EDIT)
						echo "<a href='".HTTP_PATH."users/price_range/edit/$pID'>Edit</a> | ";
					if($DELETE)	
						echo "<a style='cursor:pointer' onclick='deleteOneItem($pID)'>Delete</a> </td>";
				echo "</tr>";
				}
				$tName=$typeName;
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
		jConfirm("Are you sure you want to delete this iLike Voting Rule?","Alert",function(r){
			if(r) window.location = "<?php echo HTTP_PATH ?>users/price_range/deleteOneItem/"+ id;
		});
	
	}
</script>
	

	

