<div class="content">
	
	<div class="title-content">
		<h2>ITEM DIVISION</h2>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
		 $CI         = & get_instance();
		 $CI->load->database('default');
		
		$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id= $countryID";
		$country = $CI->db->query($Csql);
		$country = $country->row();
		
		
		if(!isset($cName))
			$cName = "All Country";
		else
			$cName = $country->countryName;
		
		$sql = "SELECT YEAR(dateAdded) as cyear FROM items GROUP BY YEAR(dateAdded)";
		$years = $CI->db->query($sql);
		 
		$action = HTTP_PATH.'users/iLikeCampaignRules/insert';
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		?>

		<div class='fl  searhPanel' style='width:100%;margin-top:-20px;'>
		      <form name='BU_activeness' action='<?php echo HTTP_PATH ."item_distribution_Preview/$view/$countryID/$month/$year/$fld/$fld_val"; ?>' method='post'>
			  
				  <h2 class='fl form' style='width:50%;margin-right:10px;'> COUNTRY: <?php echo $cName; ?></h2>
				  <?php if($vType=='gYear') echo "<h2 class='fl form' style='width:50%;margin-right:10px;'> YEAR: $cyear </h2>"; ?>
				  <div style='clear:both;'></div>
			  
			  </form>
			   <div style='clear:both;'></div>
			   				
				<table id="large" cellpadding="0" cellspacing="0" border=1 style="width:58%;font-size:13px;" class="iLike_Result_Table tablesorter">
				<tbody>
				 <?php 
					$x = 0;	
					$y=1;
					$z=1;
					$sum_total=0;
					//print_r($results);
					foreach($results as $r) {
					extract($r);
					echo "<tr>
							<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   						  		   				   <b>No 		 		  </b></th> 
							<th style='width:10px;text-align:left;color:white;padding-left:20px' bgcolor='#bb1d1d'>   						  		   			   <b>$table  			  </b></th> 
							<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' title='Sort by Uploaded Items'>     <b>Uploaded Items  	  </b></th> 
							<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' title='Sort by Uploaded Items'>     <b>Action  	  		  </b></th> 
						</tr>";
					
					$sub_total = 0;
					$x = 0;	
					//print_r($rows);
					foreach($rows as $r)
					{extract($r);
					$sub_total += $num_items; 
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$col = ($col=='') ? 'Uncatagorized' : $col;
					$sum_total += $num_items;
					
					$cID = ($countryID==0) ? 0 : $cID;
				 ?>
				<tr>
				  <td>												<?php echo $x     ?> 			</td>
				  <td style='text-align:left;padding-left:20px;'>	<?php echo $col     ?> 			</td>
				  <td style='text-align:center;'>
					<!--($view='',$countryID='',$month='',$year='',$fld='',$fld_val='') -->
					 <?php echo $num_items  ?> 
				  </td>
				  <?php if($previewType=='gCountry'){ ?>
					<td><a href="<?php echo HTTP_PATH.'report/item_distribution_Preview/'.$previewType.'/'.$cID.'/null/null/'.$fld.'/'.$fieldValue ?>" style='cursor:pointer;'> View Items  </a></td>
				  <?php }elseif($previewType=='gYear'){ ?>
					<td><a href="<?php echo HTTP_PATH.'report/item_distribution_Preview/'.$previewType.'/'.$cID.'/null/'.$year.'/'.$fld.'/'.$fieldValue ?>" style='cursor:pointer;'> View Items  </a></td>
				  <?php }elseif($previewType=='gMonth'){ ?>
					<td><a href="<?php echo HTTP_PATH.'report/item_distribution_Preview/'.$previewType.'/'.$cID.'/'.$mID.'/'.$year.'/'.$fld.'/'.$fieldValue ?>" style='cursor:pointer;'> View Items  </a></td>
				  <?php } ?>
				</tr>
				 <?php }
				 echo "<tr>
					  <td>	<b>Total</b>										</td>
					  <td style='text-align:left;padding-left:20px;'>			</td>
					  <td style='text-align:center;'>	
					  <b>"; ?>
				<?php
				echo "$sub_total
				
					  </b>		</td>			  			  		  			  
					  <td></td>
					</tr>";
				 } ?>
				</tbody>	
				</table>
				<br/>
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
  //item_distribution_Preview($view='',$countryID='',$month='',$year='',$fld='',$fld_val='')
  function viewDialog(view,cID,mID,year,fld,fld_val)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/item_distribution_Preview/'+view+'/'+cID+'/'+mID+'/'+year+'/'+fld+'/'+fld_val,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>



	

	

