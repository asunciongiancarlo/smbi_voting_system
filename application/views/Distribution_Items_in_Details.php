<div class="content">
	
	<div class="title-content">
		<h2>DISTRIBUTION OF PROMO ITEMS IN DETAILS</h2>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 
		extract($POST);
		$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id= $countryID";
			
		$country = $CI->db->query($Csql);
		 
		$sql = "SELECT YEAR(dateAdded) as cyear FROM items GROUP BY YEAR(dateAdded)";
		$years = $CI->db->query($sql);
		 
		 
		$action = HTTP_PATH.'users/iLikeCampaignRules/insert';
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		
		
		//print_r($POST);
		?>

		<div class='fl  searhPanel' style='width:174%;margin-top:-20px;'>
		
		      <form name='BU_activeness' action='<?php echo HTTP_PATH .'report/Distribution_Items_in_Details' ?>' method='post'>
			  
				  <h2 class='fl form' style='width:9%;margin-right:10px;'> COUNTRY		</h2>
				  <h2 class='fl form' style='width:13%;margin-right:10px;'>ATTRIBUTE	</h2>
				  <h2 class='fl form' style='width:9%;margin-right:10px;'> FROM			</h2>
				  <h2 class='fl form' style='width:9%;margin-right:10px;'> TO			</h2>
				  <h2 class='fl form' style='width:13%;margin-right:11px;'>YEARS		</h2>

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
				  
				  <select name='category' class='fl'  style='width:13%;margin-right:10px;'>  
					  <?php 
							$filters = array(
										array('fval'=>'POSMTypeID', 	'fname'=>'POSM TYPE'),
										array('fval'=>'POSMStatusID', 	'fname'=>'POSM STATUS'),
										array('fval'=>'OUTLETStatusID', 'fname'=>'SERVICE ITEM OUTLET TYPE'),
										array('fval'=>'PremiumTypeID',  'fname'=>'PREMIUM ITEM TYPE'),
										array('fval'=>'MaterialTypeID', 'fname'=>'MATERIAL TYPE'),
										array('fval'=>'brandID', 'fname'=>'BRAND')
										);
							foreach($filters as $f) 
							{ 
							 $y = $f['fval'];
							 $fname = $f['fname'];
							 $s = ($category==$y) ? 'selected' : '';
							 echo "<option value='$y' $s> $fname </option>";   
							}  
					  ?>
				  </select>
				  
				 <select name='fmonth' class='fl'  style='width:9%;margin-right:10px;'>  
					  <?php 
							$months = array(
										array('fmonth'=>1, 'month'=>'January'),
										array('fmonth'=>2, 'month'=>'February'),
										array('fmonth'=>3, 'month'=>'March'),
										array('fmonth'=>4, 'month'=>'April'),
										array('fmonth'=>5, 'month'=>'May'),
										array('fmonth'=>6, 'month'=>'June'),
										array('fmonth'=>7, 'month'=>'July'),
										array('fmonth'=>8, 'month'=>'Agust'),
										array('fmonth'=>9, 'month'=>'September'),
										array('fmonth'=>10, 'month'=>'October'),
										array('fmonth'=>11, 'month'=>'November'),
										array('fmonth'=>12, 'month'=>'December')
										);
							$s = ($fmonth=='all') ? 'selected' : '';
							echo "<option value='all' $s> All Months </option>";
							foreach($months as $m) 
							{ 
							 $c = $m['fmonth'];
							 $b = $m['month'];
							 $s = ($fmonth==$c) ? 'selected' : '';
							 echo "<option value='$c' $s> $b </option>";   
							}
					  ?>
				  </select>
				  <select name='tmonth' class='fl'  style='width:9%;margin-right:10px;'>  
					  <?php 
							$months = array(
										array('tmonth'=>1, 'month'=>'January'),
										array('tmonth'=>2, 'month'=>'February'),
										array('tmonth'=>3, 'month'=>'March'),
										array('tmonth'=>4, 'month'=>'April'),
										array('tmonth'=>5, 'month'=>'May'),
										array('tmonth'=>6, 'month'=>'June'),
										array('tmonth'=>7, 'month'=>'July'),
										array('tmonth'=>8, 'month'=>'Agust'),
										array('tmonth'=>9, 'month'=>'September'),
										array('tmonth'=>10, 'month'=>'October'),
										array('tmonth'=>11, 'month'=>'November'),
										array('tmonth'=>12, 'month'=>'December')
										);
							$s = ($tmonth=='all') ? 'selected' : '';
							echo "<option value='all' $s> All Months </option>";
							foreach($months as $m) 
							{ 
							 $c = $m['tmonth'];
							 $b = $m['month'];
							 $s = ($tmonth==$c) ? 'selected' : '';
							 echo "<option value='$c' $s> $b </option>";   
							}
					  ?>
				  </select>
				  
				  <select name='cyear' class='fl'  style='width:6%;margin-right:10px;'>  
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
					<th style="width:10px;text-align:center;color:white;padding:0px" bgcolor='#bb1d1d'>   						  		   				   <b><?php echo $category_label ?> </b></th> 
					<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Uploaded Items'>     <b>Uploaded Items  	  </b></th> 
					
					<th style="width:75px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Items'>     		   <b>AVG. Local Price 	  </b></th> 
					<th style="width:50px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Year'>      		   <b>AVG. USD Price  	  </b></th> 
					
					<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Target Items'>       <b>Month  	  		  </b></th> 
					<th style="width:75px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Items'>     		   <b>Year  	  	  	 </b></th> 
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
					$col = ($col=='') ? 'Uncatagorized' : $col;
					$total += $num_items;
					//DEFAULT
					$month = ($default==TRUE) ? '-' : $month;
					$sum_local += $localPrice;
					$sum_usd   += $usPrice;
				 ?>
				<tr>
				  <td>												<?php echo $x     ?> 			</td>
				  <td style='text-align:left;padding-left:20px;'>												<?php echo $col     ?> 			</td>
				  <td style='text-align:center;'>					<?php echo $num_items  ?> 		</td>			  
				  <td style='text-align:right;padding-right:40px;'>	<?php echo number_format($localPrice, 2, '.', '')  ?> 		</td>
				  <td style='text-align:right;padding-right:40px;'>	<?php echo number_format($usPrice, 2, '.', '')     ?> 		</td>			  
				  <td style='text-align:center;'>					<?php echo $month ?> 			</td>
				  <td style='text-align:center;'>					<?php echo $year ?> 			</td>
					
				  
				  <td style='text-align:center;'>					
				  <?php if($default==TRUE){ ?>
					<a onclick="viewDialog('default','<?php echo $category.'\','.$fieldValue.','.$cID.','.$mID.','.$year ?>)" style='cursor:pointer;'> Items </a> 
				  <?php }else{ ?>
					<a onclick="viewDialog('includeMonth','<?php echo $category.'\','.$fieldValue.','.$cID.','.$mID.','.$year ?>)" style='cursor:pointer;'> Items </a>
				  <?php } ?>
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
				    <td> <?php echo number_format($total) ?></td>
				    <td style='text-align:right;padding-right:40px;'> <b><?php echo number_format($sum_local, 2, '.', '') ?> </b></td>
				    <td style='text-align:right;padding-right:40px;'> <b><?php echo number_format($sum_usd, 2, '.', '')   ?> </b></td>
				    <td> <b> </b></td>
				    <td> <b>	
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
  function viewDialog(view,category,fieldValue,cID,mID,year)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/Distribution_Items_Preview/'+view+'/'+category+'/'+fieldValue+'/'+cID+'/'+mID+'/'+year,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>



	

	

