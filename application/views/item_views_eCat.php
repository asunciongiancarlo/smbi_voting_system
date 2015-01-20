<div class="content">
	
	<div class="title-content">
		<h2>NUMBER OF VIEWS PER ITEM</h2>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
		 $countryID=NULL;
		 extract($POST);
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 		 
		$sql = "SELECT YEAR(dateAdded) as cyear FROM ec_items WHERE ec_items.publish = 'y' GROUP BY YEAR(dateAdded)";
		$years = $CI->db->query($sql);
	
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		extract($POST);
		
		//print_r($POST);
		?>

		<div class='fl  searhPanel' style='width:174%;margin-top:-20px;'>
		
		      <form name='BU_activeness' action='<?php echo HTTP_PATH .'report/item_views_eCat' ?>' method='post'>
			 
				  <h2 class='fl form' style='width:9%;margin-right:10px;'> FROM			</h2>
				  <h2 class='fl form' style='width:9%;margin-right:10px;'> TO			</h2>
				  <h2 class='fl form' style='width:45%;margin-right:60px;'>YEAR			</h2>
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
					<th style="width:10px;text-align:center;color:white;padding:0px" bgcolor='#bb1d1d'>   						  		   				   <b>No 		 		    </b></th> 
					<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by User'>     		   <b>Code  	  			</b></th> 
					<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by User'>     		   <b>Image  	  			</b></th>
					<th style="width:50px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Month'>     		   <b>Item Name  	 		</b></th> 
					<th style="width:50px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Month'>     		   <b>Views  	 			</b></th> 
					<th style="width:50px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Year'>      		   <b>Released	 	 		</b></th> 
				</tr>
				</thead>
				<tbody>
				 <?php 
					$x = 0;	
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($reports as $r) { 
					extract($r);
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$total += $num_views;
				 ?>
				<tr>
				  <td>												<?php echo $x     ?> 			</td>
				  <td style='text-align:left;padding-left:10px;'>	<?php echo $itemCode ?> 		</td>
				  <td style='text-align:center;'>					<img src="<?php echo HTTP_PATH.'img/thumb/'.$item_image ?>" style="width:30px;height:30px;"> 					</td>
				  <td style='text-align:left;padding-left:30px;'>	<?php echo "<a href='".HTTP_PATH."gallery/itemInfoECatalog/$ecID/$itemID' target='_blank'>". $itemName ."</a>"; ?> 			</td>
				  <td style='text-align:center;'>					
						<a onclick="viewDialog('eCatalogue',<?php echo $itemID ?>)" style='cursor:pointer;'> <?php echo $num_views ?> </a>
				  </td>
				  <td style='text-align:left;padding-left:50px;'>	<?php echo date("M d, Y", strtotime($dateReleased))  ?> 				</td>
				</tr>
				 <?php } ?>
				</tbody>
				<?php 
					if(!$reports)
						echo "<tr><td colspan='8'>No match found.</td></tr>"
				?>
				<tr>
				  <td> <b> Total </b></td>
				  <td> </td>
				  <td> </td>
				  <td> </td>
				  <td> <b> <?php echo number_format($total) ?></b></td>
				  <td> </td>
				</tr>
				</table>
				
				<div style='clear:both'></div>
		</div>
				


		</div>
	</div>
	
	<div class="clear"></div>
</div>

<div id="dialog-form" title="Logs" style='display:none;'>
	<div id="List_of_Items"></div>
</div>

<script>
	
  function viewDialog(tbl,itemID)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/hits/'+tbl+'/'+itemID,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>



	

	

