
 <div class="content">
		
    	<div class="title-content">
        	<h2>Archive Items</span> </h2>
        </div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container">
			<?php 
			//print_r($archive_list);
			$aID = $archive_info[0]['id']; ?>
			<table cellpadding="0" cellspacing="0" style="width:100%;margin: 0px auto;" class="iLike_Result_Table">
				<br/>
			   <tr style="border-radius: 6px;margin-top:10px;">
					<td style="width:200px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;">  ARCHIVE NAME 		</td> 
					<td style="width:600px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;">  DATE FROM 			</td> 
					<td style="width:200px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;">  DATE TO  			</td> 
				</tr>
				<tr>
				  <td><?php echo str_replace('_',' ', $archive_info[0]['archive_name'])    	?> 	</td>
				  <td><?php echo date("M d, Y", strtotime($archive_info[0]['startDate']))	?> 	</td>
				  <td><?php echo date("M d, Y", strtotime($archive_info[0]['endDate']))		?> 	</td>
				</tr>
			</table>
			
			<form name="<?php echo HTTP_PATH ."users/archive_details/archive_item_db/$id" ?>" method="POST">
				<div id="tabs" style="font-size:14px;">
				  <ul>
					<li><a href="#tabs-3" style="height: 8px;padding-top: 14px;"> Item Database 	</a></li>
				  </ul>
				<div id="tabs-3" style="height:800px;overflow-y: scroll;">
					<?php 
					$CI =& get_instance();
					//MESSAGE ALERT
					if(isset($msg)){
						$CI->load->library('alert');
						echo $CI->alert->check($msg);
					}
					
					if($Archive_items==TRUE){ ?>
						<input type='submit' name='button' value='Restore Items' class='clickMe fl' style="margin-right: 13px;" onclick="restore_Item_Database(<?php echo $aID ?>)"><br/>
					<?php }
					$g=0;
					if($limit+20 <= $totrec){
					  $g = $limit;
					  $value = ($limit+1) .'-'. ($limit+20);
					}else{
					  $g = $totrec;
					  $value = ($limit+1) .'-'. ($totrec);
					}
					?>
					<div style="margin-top:-23px;margin-left:10px;">
					Record: <?php echo $value .' of ' . $totrec ?> 
					<select onchange="document.forms[0].submit()" name='selpage' style="font-size:12px;width:130px;"> 
					  <?php 
						
						for($x=0; $x <= $totrec-1;$x+=20)
						   {
							if($x+20 <= $totrec) 
							  $value = ($x+1) .'-'. ($x+20);
							else
							  $value = ($x+1) .'-'. ($totrec); 
							$sel = $limit == $x ? "selected":"";
							echo "<option $sel value='$x'> $value </option>";
						   }
						?>
					</select>
					</div>
					
					<table cellpadding="0" cellspacing="0" border=1 style="width:100%;margin: 0px auto;font-size:12px;" class="iLike_Result_Table">
						<tr style="border-radius: 6px;">
							
							<td style="width:10px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Select 		  </b></td> 
							<td style="width:10px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>No 		  </b></td> 
							<td style="width:100px;text-align:center;color:white;" bgcolor='#bb1d1d'>   <b>Item Code  </b></td> 
							<td style="width:100px;text-align:center;color:white;" bgcolor='#bb1d1d'>   <b>Country    </b></td> 
							<td style="width:50px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Image  	  </b></td> 
							<td style="width:100px;text-align:center;color:white;" bgcolor='#bb1d1d'>   <b>Name  	  </b></td> 
							<td style="width:100px;text-align:center;color:white;" bgcolor='#bb1d1d'>   <b>Short Description</b></td> 
							<td style="width:20px;text-align:center;color:white;" bgcolor='#bb1d1d'>    <b>Publish    </b></td> 
						</tr>
						 <?php 
							$total = 0;
							$y=1;
							$z=1;
							//print_r($items);
							foreach($items as $i) { 
							extract($i);
							$c = (($g++)%2) == 0 ? "class='alter'" :  "";
							$publish = ($publish=='y' ? 'Yes':'No');
						 ?>
						<tr>
						  <td <?php echo $c ?> >							<?php echo "<input type='checkbox' name='itemDB_IDs[]' value='$itemDB_ID'>" 		 ?> 																			  </td>
						  <td <?php echo $c ?> >							<?php echo $g 		 ?> 																			  </td>
						  <td <?php echo $c ?> >							<?php echo $itemCode ?> 																			  </td>
						  <td <?php echo $c ?> >							<?php echo $countryName ?> 																			  </td>
						  <td <?php echo $c ?> style='text-align:center;' > <img src="<?php echo HTTP_PATH .'img/galleryImg/'.$item_image ?>" style="height:30px">  </td>
						  <td <?php echo $c ?> style='text-align:left;padding-left:50px;'>	<?php echo $itemName ?> 													  </td>
						  <td <?php echo $c ?> style='text-align:left;'>	<?php echo $Short_Description ?> 											  </td>
						  <td <?php echo $c ?>>	<?php echo $publish ?> 											  </td>
						</tr>
						 <?php } ?>
					</table>
				  </div>
				
				</div>
			</form>
            </div>
        </div>
           
        <div class="clear"></div>
    </div>
	

	
	<script>
	  $(function() {
		$( "#tabs" ).tabs();
	  });
	 
  </script>