    <div class="content">
		
    	<div class="fl title-content">
        	<h2>ITEM DATABASE: ITEM TYPE - OTHER FIELDS</h2>
        </div>
		<div style="float:right;margin: 16px;">
			<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
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
				$action = HTTP_PATH ."users/Item_Database_Type_Other_fields/insert";

			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
			<?php
			if($ADD OR $EDIT){
				$CI =& get_instance();
				$CI->load->library('forms');
				echo $CI->forms->form_header('SMBi','statusFORM',$action);
				 echo "<table style='width:100%'>";
					echo "<tr>
							<td>";
							 echo "<label style='font-weight:bold;float:left;text-align:left;'>POSM Type<label>";
							 echo "<select name='POSM_TypeID' style='float:left'>";
							  foreach($POSM_Status  as $key => $s)
							  {
								extract($s);
								echo "<option value='$id'>$typeName </option>"; 
							  }
							 echo  "</select>";
					echo "</td>";
		
					echo "<td>
							<div style='clear:both'></div>";
							 echo "<label style='font-weight:bold;float:left;text-align:left;'>FIELDS<label>";
							 echo "<select name='table_fieldsID' style='float:left;margin-right:10px;'>";
							  foreach($POSM_fields  as $key => $s)
							  {
								extract($s);
								echo "<option value='$id'>$fieldName </option>"; 
							  }
							 echo  "</select>
						</td>
						<td style='width:50%;'>";
						echo $CI->forms->form_submit('SMBi');
				echo "</td>
					</form>";
				echo "</table>";
			}
			?>
			<!-- STATUS TABLE  -->
			<form name="SMBi2" id="statusTable" action="<?php echo HTTP_PATH ?>itemDatabase/Country/deleteSelectedItem" method="POST"> 
				<?php echo $csrf ?>
				<div class="clear"></div>
				<table cellpadding="0" cellspacing="0" style="width:100%;margin: 0px auto;">
				
					<tr style="border-radius: 6px;">
						<th style="width:200px;text-align:center;"> Item Type </th> 
						<th style="width:200px;text-align:center;"> Database Field </th> 
						<th style="width:150px;text-align:center;"> ACTION </th>   
					</tr>
					
					<?php 
						//print_r($itemType_POSM_table_fields);
						$x=0;
						foreach($itemType_POSM_table_fields as $s)
						{
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							extract($s);
							echo "<tr>";
								 
								echo "<td $c >". $typeName ."</td>";
								echo "<td $c style='text-align:left;padding-left:100px;'>". $fieldName ."</td>";
								echo "<td $c style='text-align:center;'>"; 
								
								if($DELETE)	
									echo "<a style='cursor:pointer' onclick='deleteOneItem($iID)'>Delete</a> </td>";
							echo "</tr>";
						}
					?>
				</table>
			</form>
			
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>
	
	
	<br/>

	
	<script type="text/javascript">
		function deleteOneItem(id)
		{
			if(confirm("Are you sure you want to delete this Relation?"))
			{
				window.location = "<?php echo HTTP_PATH ?>users/Item_Database_Type_Other_fields/deleteOneItem/"+ id;
			}
		}
 

	</script>