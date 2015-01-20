<div class="content">
	
	<div class="title-content">
		<h2><span style='text-transform:lowercase'>e</span>Catalogue Items Monthly Report</h2>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 
		 
		$action = HTTP_PATH.'users/iLikeCampaignRules/insert';
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		extract($POST);
		
		//print_r($POST);
		?>

		<div class='fl  searhPanel' style='width:100%!important;margin-top:0px; margin-bottom:20px;'>
		      <form name='BU_activeness' action='<?php echo HTTP_PATH ."report/eCatalogue_per_Month/$ecID/$year" ?>' method='post'>
			  
				  <h2 class='fl form' style='width:25%;margin-right:11px;'>CATALOGUE: <?php echo $reports[0]['eTitle']; ?>			</h2> 
				  <h2 class='fl form' style='width:13%;margin-right:11px;'>YEAR: <?php echo $year; ?>								</h2>
			      <div style='clear:both'></div>
				  <h2 class='fl form' style='width:9%;margin-right:11px;'>FROM			</h2> 
				  <h2 class='fl form' style='width:13%;margin-right:11px;'>TO			</h2> 

			      <div style='clear:both'></div>
				 
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
				  <input type='submit' name='filter'>
				  <input type='hidden' name='cyear' value='<?php echo $cyear ?>'>
			  </form>
			   <div style='clear:both;'></div>
			   
			   
			   <div style='clear:both'></div>
			   <?php 
				//print_r($reports);
				?>
				
				<table id="large" cellpadding="0" cellspacing="0" border=1 style="width:99%;font-size:13px;" class="iLike_Result_Table tablesorter">
				<thead>
				<tr>
					<th style="width:10px;text-align:center;color:white;padding:0px" bgcolor='#bb1d1d'>   						  		   				   <b>No 		 		  </b></th>  
					<th style="width:50px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Year'>      		   <b>Month	 	 		  </b></th> 
					<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Uploaded Items'>     <b>Uploaded Items  	  </b></th> 
					<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Local Price'>        <b>AVG. Local Price    </b></th> 
					<th style="width:75px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by USD Price'>     	   <b>AVG. USD Price  	  </b></th> 
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
					$total += $eCatalogueItems;
					$sum_local += $avgUnitPrice;
					$sum_usd   += $avgUSDPrice;
				 ?>
				<tr>
				  <td>												<?php echo $x     ?> 		</td>
				  <td style='text-align:center;'>					<?php echo $month  ?> 		</td>			  
					 <td style='text-align:center;'>				<?php echo $eCatalogueItems ?> 	</td>
					<td style='text-align:right;padding-right:40px;'>	<?php echo number_format($avgUnitPrice, 2, '.', '')  ?> 			</td>
				  <td style='text-align:right;padding-right:40px;'>		<?php echo number_format($avgUSDPrice, 2, '.', '')  	?> 			</td>
				  <td style='text-align:center;'>	
					<a href='<?php echo HTTP_PATH."report/eCatalogue_items/gMonth/$ecID/$year/$mID"; ?>' style='cursor:pointer;'> View Items </a> |
					<a href='<?php echo HTTP_PATH."report/eCatalogue_item_division/gMonth/$ecID/$year/$mID"; ?>' style='cursor:pointer;'> Distribution </a> 
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
					<td style='text-align:right;padding-right:40px;'> <b><?php echo number_format($sum_local, 2, '.', '') ?> </b></td>
				    <td style='text-align:right;padding-right:40px;'> <b><?php echo number_format($sum_usd, 2, '.', '')   ?> </b></td>
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
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/viewItems/'+view+'/'+cID+'/'+mID+'/'+year,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>



	

	

