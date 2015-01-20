<div class="content">
	
	<div class="title-content">
		<h2>ACTIVENESS OF USERS per YEAR</h2>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 
		
		$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id= $countryID";
			
		$country = $CI->db->query($Csql);
		 
		$sql = "SELECT YEAR(dateAdded) as cyear FROM items GROUP BY YEAR(dateAdded)";
		$years = $CI->db->query($sql);
		 
		$users = $CI->db->query("SELECT user_id, admin_users.full_name as fname 
								 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id 	
								 WHERE admin_users.id = $user_id LIMIT 0,1");
		$users = $users->result_array();
		 
		 
		$action = HTTP_PATH.'users/iLikeCampaignRules/insert';
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		extract($POST);
		
		//print_r($POST);
		?>

		<div class='fl  searhPanel' style='width:174%;margin-top:-20px;'>
		
		      <form name='BU_activeness' action='<?php echo HTTP_PATH .'report/BU_activeness_Users_in_Details' ?>' method='post'>
			  
				  <h2 class='fl form' style='width:9%;margin-right:10px;'>COUNTRY		</h2>
				  <h2 class='fl form' style='width:13%;margin-right:10px;'>USER			</h2>
				 
			      <div style='clear:both'></div>
				  
				  <select name='countryID' class='fl'  style='width:9%;margin-right:10px;'>  
					  <?php 
							foreach($country->result_array() as $o) 
							{ 
							 $v = $o['cID'];
							 $t = $o['countryName'];
							 $s = ($countryID==$v) ? 'selected' : '';
							 echo "<option value='$v' $s> $t </option>";   
							}  
					  ?>
				  </select>
				  <select name='user_id' class='fl'  style='width:13%;margin-right:10px;'>  
					  <?php 
							foreach($users as $u) 
							{ 
							 $fname = $u['fname'];
							 $uID   = $u['user_id'];
							 $s 	= ($user_id==$uID) ? 'selected' : '';
							 echo "<option value='$uID' $s> $fname </option>";   
							}  
					  ?>
				  </select>				  
			  </form>
			   <div style='clear:both;'></div>
			   
			   
			   <div style='clear:both'></div>
			   <?php 
				//print_r($reports);
				?>
				
				<table id="large" cellpadding="0" cellspacing="0" border=1 style="width:58%;font-size:13px;" class="iLike_Result_Table tablesorter">
				<thead>
				<tr>
					<th style="width:10px;text-align:center;color:white;padding:0px" bgcolor='#bb1d1d'>   						  		   				   <b>No 		 		  </b></th>  
					<th style="width:50px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Year'>      		   <b>Year	 	 		  </b></th> 
					<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Uploaded Items'>     <b>Uploaded Items  	  </b></th> 
					<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Target Items'>       <b>Target   Items  	  </b></th> 
					<th style="width:75px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Items'>     		   <b>Difference  	  	  </b></th> 
					<th style="width:50px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Year'>      		   <b>Action	 	 	  </b></th> 
				</tr>
				</thead>
				<tbody>
				 <?php 
					$x = 0;	
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$diff=0;
					foreach($reports as $r) { 
					extract($r);
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$total += $num_items;
					$m = $month;					
				 ?>
				<tr>
				  <td>												<?php echo $x     ?> 		</td>
				  <td style='text-align:center;'>					<?php echo $year  ?> 		</td>			  
					 <td style='text-align:center;'>				<?php echo $num_items ?> 	</td>
					 <td style='text-align:center;'>				<?php echo number_format($target * 12); 	$total_target += $target * 12;  ?> </td>
					 <td style='text-align:center;'>					
						<?php $diff += $num_items - ($target * 12);
							  $s = ($num_items-($target * 12)<=0) ? "style='color:red; font-weight:bold;'" : "style='color:green; font-weight:bold;'";
							  echo "<label $s>".number_format($num_items-($target * 12)) ."</label>";    
						?> 
					</td>
				  
				  <td style='text-align:center;'>					
					<a href='<?php echo HTTP_PATH."report/BU_items/BU_activeness_Users_per_Year/$cID/null/$year/$uID"; ?>' style='cursor:pointer;'> View Items 	</a> |
					<a href='<?php echo HTTP_PATH."report/BU_activeness_Users_per_Month/$uID/$cID/$year"; ?>' style='cursor:pointer;'> More </a>
				  </td>
				</tr>
				 <?php } ?>
				</tbody>
				<?php 
					if(!$reports)
						echo "<tr><td colspan='8'>No match found.</td></tr>"
				?>
				
				<tr>
					<td> <b>Total</b></td>
				    <td> </td>
				    <td> <b><?php echo number_format($total) 		 ?> </b></td>
				    <td> <b><?php echo number_format($total_target) ?> </b></td>
				    <td> <b>
						<?php 
							$s = ($diff<=0) ? "style='color:red; font-weight:bold;'" : "style='color:green; font-weight:bold;'";
							echo "<label $s>". number_format($diff) ."</label>";    
						?>	
					</b></td>
					<td> </td>
				</tr>
				</table>
				
				<div style='clear:both'></div>
		</div>
				


		</div>
	</div>
	
	<div class="clear"></div>
</div>

<div id="dialog-form" title="LIST OF ITEMS" style='display:none;'>
	<div id="List_of_Items"></div>
</div>

<script>  
  function viewDialog(view,cID,mID,year,uID)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/viewItems/'+view+'/'+cID+'/'+mID+'/'+year+'/'+uID,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>



	

	

