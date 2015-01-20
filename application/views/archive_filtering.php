    <div class="content">
		
    	<div class="title-content">
        	<h2>ITEMS FILTERING</h2>
        </div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container2">
			<?php
				$CI =& get_instance();
				
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}
		
				//ISSET ID
				$countryVal="";
				$action = HTTP_PATH ."users/archive_filtering/update/".$archive_filtering[0]['id'];

			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
			<?php
		
			$dateFrom	= "";
			$dateTo		= "";
			$tyear  	= $archive_filtering[0]['tyear'];
			$tmonth 	= $archive_filtering[0]['tmonth'];
			$dateFrom 	= $archive_filtering[0]['dateFrom'];
			$dateTo 	= $archive_filtering[0]['dateTo'];
			$defaultRange 	= $archive_filtering[0]['defaultRange'];
			$defaultDate 	= $archive_filtering[0]['defaultDate'];
			
			if($EDIT){
				$CI =& get_instance();
				$CI->load->library('forms');
				echo $CI->forms->form_header('SMBi','statusFORM',$action);
				 echo "<table>";
					echo "<tr>
							<td> 
								<input type='radio' name='default' value='defaultRange' ".active($defaultRange)." > </td>
							<td>";
							 echo "<label style='font-weight:bold;float:left;text-align:left;color: #710002;font-size: 15px;margin-left: 10px;'>YEAR<label>";
							 echo "<select name='tyear' style='float:left'>";
							 for($i=0;$i<=100;$i++){
								$s = isset($tyear) ? ($tyear==$i ? "selected":""):""; 
								echo "<option value='$i' $s> $i </option>";
							 }	
							 echo  "</select>";
					echo "</td>";
		
					echo "<td>
							<div style='clear:both'></div>";
							 echo "<label style='font-weight:bold;float:left;text-align:left;color: #710002;font-size: 15px;margin-left: 10px;'>MONTH<label>";
							 echo "<select name='tmonth' style='float:left;margin-right:10px;'>";
							  for($i=0;$i<=12;$i++){
								$s = isset($tmonth) ? ($tmonth==$i ? "selected":""):""; 
								echo "<option value='$i' $s> $i </option>";
							 }	
							 echo  "</select>
						</td>";
				echo   "</tr>
						<tr>
							<td>  </td>
							<td>";
							//$CI =& get_instance();
							//$CI->load->library('forms');
							//echo $CI->forms->form_fields2('text_short','dateFrom',$dateFrom,'DATE FROM','DateFrom');
				echo 		"</td>";
				echo 		"<td>";
								//echo $CI->forms->form_fields2('text_short','dateTo',$dateTo,'DATE TO','DateTo');
				echo "</td>
					  </tr>
					  <tr>";
				echo 	"<td></td>
						 <td style='width:50%;'> ".  $CI->forms->form_submit('SMBi') ." </td>
						 <td></td>
					   </tr>";
				
				echo "</form>";
				echo "</table>";
			}
			?>			
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>	
	<br/>
	<?php 
		function active($x)
		{
			if($x==1)
				return 'checked';
			else
				return '';
			/*	
			$y = if($x==1) ? 'checked' : '';
			return $y;
			*/
		}
	?>

	
