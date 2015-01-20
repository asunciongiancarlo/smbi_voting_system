
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
			
			 <form name="<?php echo HTTP_PATH ."users/archive_details/archive_catalogue/$id" ?>" method="POST">
				<div id="tabs" style="font-size:14px;">
				  <ul>
					<li><a href="#tabs-1" style="height: 8px;padding-top: 14px;"> eCatalogue		</a></li>
				  </ul>
				  <div id="tabs-1" style="height:677px;padding-left:50px;overflow-y: scroll;">				  
					<div style="margin:0px auto;">
					<?php
					$CI =& get_instance();
					//MESSAGE ALERT
					if(isset($msg)){
						$CI->load->library('alert');
						echo $CI->alert->check($msg);
					}
					
					if($Archive_e_catalog==TRUE){ ?>
						<input type='submit' name='button' value='Restore eCatalogue Items' class='clickMe fl' style="margin-right: 13px;" onclick="restore_eCatalog(<?php echo $aID ?>)"><br/>
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
					echo "</select></div>";
					
					if($eCatalogue_items){
						echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
							 <tr style='border-radius: 6px;'>
								<td style='width:10px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Select 		   		</b></td> 
								<td style='width:10px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		   			</b></td> 
								<td style='width:10px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Catalogue 		   	</b></td> 
								<td style='width:50px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Item Code 		   	</b></td> 
								<td style='width:30px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Image  	   			</b></td> 
								<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Item Name    			</b></td> 
								<td style='width:200px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Short Description  	</b></td> 
								<td style='width:50px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Date Added  	</b></td> 
							</tr>";
						 
							$x = 0;
							$total = 0;
							//print_r($eCatalogue_items);
							foreach($eCatalogue_items as $i) { 
							extract($i);
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							$gray = '';
							if($publish != 'y'){
								$gray =  "style='background:#AFAFAF'";
								$c = '';
							}
						 
							echo	"<tr>
									  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>	<input type='checkbox' name='eCat_IDs[]' value='$ec_itemsID'></td>
									  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>	$x 											  </td>
									  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>	$catalogue_title 							  </td>
									  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>	$itemCode 									  </td>
									  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>	
										<img src='".HTTP_PATH."img/galleryImg/$iImg' style='height:30px'>";
										if($gray!='')
											echo "<label style='color:red;font-size:10px;margin: 0;'> *Draft </label>";
							echo	 "</td>
									  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$itemName  									  </td>
									  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$Short_Description  						  </td>
									  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>	$tdate  						  </td>
									  ";
							echo	"</tr>";
							}
						echo"</table>";
					}
					?>
					
					</div>
					</div>
				</div>
			 </form>
			
			<div class="clear">&nbsp;</div>	
             			
			<div class="clear">&nbsp;</div>	
            </div>
        </div>
           
        <div class="clear"></div>
    </div>
	

	
	<script>
	  $(function() {
		$( "#tabs" ).tabs();
	  });
  </script>