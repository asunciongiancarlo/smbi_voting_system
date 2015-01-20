<div class="content">
	
	<div class="title-content">
		<h2>DISTRIBUTION OF PROMO ITEMS</h2>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 
		 if($_SESSION['super_admin']=='y')
			$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0";
		 else
			$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id=".$_SESSION['countryID'];
			
		 $country = $CI->db->query($Csql);
		 
		 $sql = "SELECT YEAR(dateAdded) as cyear FROM items GROUP BY YEAR(dateAdded)";
		 $years = $CI->db->query($sql);
		 
		$action = HTTP_PATH.'users/iLikeCampaignRules/insert';
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		extract($POST);
		
		//print_r($POST);
		?>

		<div class='fl  searhPanel' style='width:174%;margin-top:-20px;'>
		
		      <form name='BU_activeness' action='<?php echo HTTP_PATH .'report/Distribution_Items' ?>' method='post'>
			  
				  <h2 class='fl form' style='width:13%;margin-right:10px;'>COUNTRY		</h2>
				  <h2 class='fl form' style='width:13%;margin-right:11px;'>YEARS		</h2>

			      <div style='clear:both'></div>
				  
				  <select name='countryID' class='fl'  style='width:13%;margin-right:10px;'>  
					  <?php 
							if($_SESSION['super_admin']=='y'){
								$s = ($countryID=='all') ? 'selected' : '';
								echo "<option value='all' $s> All Countries </option>";
							}
							foreach($country->result_array() as $o) 
							{ 
							 $v = $o['cID'];
							 $t = $o['countryName'];
							 $s = ($countryID==$v) ? 'selected' : '';
							 echo "<option value='$v' $s> $t </option>";   
							}  
					  ?>
				  </select>
				 
				  
				  <select name='cyear' class='fl'  style='width:13%;margin-right:10px;'>  
					  <?php 
							$s = ($cyear=='all') ? 'selected' : '';
							echo "<option value='all' $s> All Years </option>";
							foreach($years->result_array() as $y) 
							{ 
							 $y = $y['cyear'];
							 $s = ($cyear==$y) ? 'selected' : '';
							 echo "<option value='$y' $s> $y </option>";   
							}  
					  ?>
				  </select>
				  
				  
				  <input type='submit' name='filter'>
			  
			  </form>
			   <div style='clear:both;'></div>
			   
			   
			   <div style='clear:both'></div>
			   <?php 
				//print_r($reports);
				?>
				
				<table id="large" cellpadding="0" cellspacing="0" border=1 style="width:62%;font-size:13px;" class="iLike_Result_Table tablesorter">
				<thead>
				<tr>
					<th style="width:10px;text-align:center;color:white;padding:0px" bgcolor='#bb1d1d'>   						  		   				   <b>No 		 		  </b></th> 
					<th style="width:19px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Country'>   		   <b>Country   	 	  </b></th>
					<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Target Items'>       <b>AVG. Local Price  	  </b></th> 
					<th style="width:75px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Items'>     		   <b>AVG. USD Price  	  	  </b></th> 
					<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Uploaded Items'>     <b>Uploaded Items  	  </b></th> 
					<th style="width:50px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Year'>      		   <b>Year	 	 		  </b></th> 
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
					$sum_local=0;
					$sum_usd=0;
					foreach($reports as $r) { 
					extract($r);
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$total += $num_items;
					$m = $month;
					$month = ($per_country==TRUE) ? '-' : $month;
					$sum_local += $localPrice;
					$sum_usd   += $usPrice;
				 ?>
				<tr>
				  <td>													<?php echo $x     ?> 							</td>
				     <td style='text-align:left;padding-left:50px;'>	<?php echo $cName ?> 						    </td>	
					 <td style='text-align:right;padding-right:40px;'>	<?php echo number_format($localPrice, 2, '.', '')  ?> 		</td>
					 <td style='text-align:right;padding-right:40px;'>	<?php echo number_format($usPrice, 2, '.', '')  	?> 		</td>
					 <td style='text-align:center;'>					<?php echo $num_items   ?> 						</td>
					 <td style='text-align:center;'>					<?php echo $year  		?> 						</td>
					 <td style='text-align:center;'>					
					<a onclick="viewDialog('per_countryAVG',<?php echo $cID.','.$mID.','.$year ?>)" style='cursor:pointer;'> Items </a> |  
					<a href='<?php echo HTTP_PATH."report/Distribution_Items_in_Details/$cID/POSMTypeID"; ?>' style='cursor:pointer;' target="_blank"> More </a>
				  </td>
				</tr>
				 <?php } ?>
				</tbody>
				<?php 
					if(!$reports)
						echo "<tr><td colspan='6'>No match found.</td></tr>"
				?>
				
				<tr>
					<td> <b>Total</b></td>
				    <td> </td>
				    <td style='text-align:right;padding-right:40px;'> <b><?php echo number_format($sum_local, 2, '.', '') ?> </b></td>
				    <td style='text-align:right;padding-right:40px;'> <b><?php echo number_format($sum_usd, 2, '.', '')   ?> </b></td>
				    <td> <b><?php echo $total ?>	</b></td>
				    <td></td>
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
  function viewDialog(view,cID,mID,year)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 970});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/viewItems/'+view+'/'+cID+'/'+mID+'/'+year,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>



	

	

