<div class="content">
	
	<div class="f title-content">
		<h2><span style='text-transform:lowercase;'>i</span>Want Voting Rules</h2>
	</div>
	<div style="float:right;margin: 16px;">
		<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2" style='width: 105%;'>
		<?php 
			$CI =& get_instance();
			//MESSAGE ALERT
			echo "<div style='width: 93%;'>";
			if(isset($msg)){
				$CI->load->library('alert');
				echo $CI->alert->check($msg);
			}
			echo "</div>";
		?>
		<table cellpadding="0" cellspacing="0" style="width:93%;margin-top: -25px;" id="large">
		<?php
			$CI2 =& get_instance();
			$CI2->load->library('fv');
			$CI->load->library('alert');
			
			$x=0; 
			$table='';
			$cName='';
			foreach($countries as $country)
			{extract($country);
				//HEADER
				if($countryName!=$cName){ 
				echo "<tr><td colspan='6' style='height: 10px;'> <td></tr>";
				echo "<tr>   
						<td colspan='8' class='country-name'  style='text-align:left;background-color:#330404;color:white;' class='alter'><b>Country: $countryName</b></td> 
						<td class='country-name'  style='text-align:left;width: 153px;background-color:#330404;color:white;' class='alter'> </td>  
					 </tr>";
				foreach($POSM_Type as $p_type)
				{ extract($p_type); 
				   $CI->load->database('default');
				  //IDENTIFY THE NUMBER OF LEVELS PER ITEM TYPE
				  $sql1 = $CI->db->query("SELECT COUNT(id) as num_of_level FROM price_range WHERE POSMTypeID = $pID");
				  $sql1 = $sql1->row();
				  
				  //CHECK IF ALL VOTING RULES ARE COMPLETE
				  $sql = $CI->db->query("SELECT COUNT(id) as num_of_levels_present FROM iWantVotingRules WHERE fieldID = $pID AND countryID = $cID");
				  $sql = $sql->row();
				 
				  $img = "";
				  if($ADD AND ($sql1->num_of_level!=$sql->num_of_levels_present)) 
				  $img = "<a href='".HTTP_PATH."users/iWantVotingRules/add/$cID/$pID' class='add-voting2'  style='color:white;'>
							<img src='".HTTP_PATH."img/add-item.png' style='margin-left:21px;cursor:pointer;'> Add Voting Rules 
						</a>";
				  echo "<tr>    
						<td class='Itype'  colspan='8' style='text-align:left;background-color:#A5A5A5;color:white;' class='alter'><b>Item Type: $typeName</b></td> 
						<td class='Itype'  style='text-align:left;width: 172px;background-color:#A5A5A5;color:white;' class='alter'> $img </td>  
					 </tr>";
				  //SELECT VOTING RULES
				 
				  $sql = $CI->db->query("SELECT i.id as iWantVotingRulesID, xOrder, level_name, campaign_label, rel, val,  
										i.cond1 AS icond1, i.min_val as imin_val, i.logical_operator as ilogical_operator, i.cond2 as icond2, i.max_val as imax_val
										FROM iWantVotingRules as i 
										LEFT JOIN price_range ON price_range.id = i.price_rangeID
										WHERE countryID = $cID AND fieldID = $pID ORDER BY xOrder ASC");
				  $sql = $sql->result_array();
				  echo "<tr class='row-title'  style='border-radius: 6px;'>
						<th class='row-title'  style='width:50px;text-align:center;font-size: 12px;padding: 0;'> 			ORDER 	     		 </th> 
						<th class='row-title'  style='text-align:center;font-size: 12px;padding: 0;'>						CAMPAIGN LABEL 		 </th> 
						<th class='row-title'  style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			LEVEL NAME 	     	 </th> 
						<th class='row-title'  style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			CONDITION 1 	     </th> 
						<th class='row-title'  style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			MIN. VALUE 	         </th> 
						<th class='row-title'  style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			LOGICAL OPERATOR 	 </th> 
						<th class='row-title'  style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			CONDITION 2 	     </th> 
						<th class='row-title'  style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			MAX. VALUE 	     	 </th> 
						<th  class='row-title' style='width: 141px;text-align:center;font-size: 12px;padding: 0;'> 		ACTION 		 	 	 </th>   
					</tr>";
				  foreach($sql as $s)
				  { extract($s);
					$c = (($x++)%2) != 0 ? "class='alter alter-2'" :  ""; 
				    echo "<tr>";
					echo "<td $c style='text-align:left;'>".   $xOrder."</td>";
					echo "<td $c style='text-align:center;'>". $campaign_label ."</td>";
					echo "<td $c style='text-align:center;'>". $level_name ."</td>";
					
					echo "<td $c >". $icond1 ."</td>";
					echo "<td $c >". $imin_val ."</td>";
					echo "<td $c >". $ilogical_operator ."</td>";
					echo "<td $c >". $icond2 ."</td>";
					echo "<td $c >". $imax_val ."</td>";
					echo "<td $c style='text-align:center;'>";
					if($EDIT)
						echo "<a href='".HTTP_PATH."users/iWantVotingRules/edit/$iWantVotingRulesID'>Edit</a> | ";
					if($DELETE)	
						echo "<a style='cursor:pointer' onclick='deleteOneItem($iWantVotingRulesID)'>Delete</a> </td>";
					echo "</tr>";
				  }
				}
		
				}
				$tName=$typeName;
			}
		?>
		</table>
		</div>
	</div>
  
	<div class="clear"></div>
</div>
	<div style="text-align:center">
	<?php if($last>0){ ?>
		<ul class="pagination">
		<a href="<?php echo HTTP_PATH."users/iWantVotingRules/page/1" ?>"><li class="page-btn" style="margin-left:3px;"> &laquo; FIRST </li></a> 
		<?php 
			//PAGNINATION
			$i	   = 1; 
			$page  = 1;
			$page2 = 1;
			$l 	   = $last;
			
			while($l!=0)
			{
				
				//ACTIVE PAGE
				$style="";
				$page_link = HTTP_PATH."users/iWantVotingRules/page/".$i++;
				if($active_page==$page++)
				{
					$style="style='text-decoration:underline'";
				}
				echo  " <a href='$page_link' $style><li>". $page2++ ."</li></a> ";
				$l--;
			}
		?>
		<a href="<?php echo HTTP_PATH."users/iWantVotingRules/page/".$last ?>"><li class="page-btn">LAST &raquo;</li></a>
		</ul>
	<?php } ?>
    </div> 

<script type="text/javascript">
	function deleteOneItem(id)
	{
		jConfirm("Are you sure you want to delete this iWant Voting Rule?","Alert",function(r){
			if(r) window.location = "<?php echo HTTP_PATH ?>users/iWantVotingRules/deleteOneItem/"+ id;
		});
	
	}
</script>
	

	

