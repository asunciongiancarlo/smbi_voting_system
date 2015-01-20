    <div class="content">
		
    	<div class="fl title-content">
        	<h2>Restore Points </h2>
        </div>
		<div style="float:right;margin: 16px;">
			
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
		
				//ACTION STATEMENT
				$action = HTTP_PATH ."itemDatabase/brands/insert";
				
				if($ADD & $_SESSION['super_admin']!='y'){
				$CI =& get_instance();
				$CI->load->library('forms');
				$action = HTTP_PATH ."itemDatabase/restore_points/insert";
				echo $CI->forms->form_header('SMBi','eCatalogFORM',$action);
				echo "<h2 style='color: #710002;font-size: 15px;margin: 0px;text-align: left;'> 
						Upload Restore Point  
					  </h2>";
				echo "<div style='margin-bottom:10px;'><input type='file' name='restorePoinFile' accept='application/zip'>";
				echo "<input type='submit' name='Submit' onclick='submitForm()'> 
					 <label style='font-size:10px;color:gray;'>Upload zip file only</label>
					 </div>";
				echo "</form>";
				echo"<div id='loading' class='fl' style='margin-top: -32px;margin-left: 100px;'>
						<img src='".HTTP_PATH."img/loading.gif'> Please wait file is being uploaded & check...
					</div>";
				}
				
			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
			<!-- STATUS TABLE  -->
			<form name="SMBi2" id="statusTable" action="<?php echo HTTP_PATH ?>itemDatabase/brands/deleteSelectedItem" method="POST"> 
				
				 <div class="clear"></div>
				<table cellpadding="0" cellspacing="0" style="width:100%;">
				
					<tr style="border-radius: 6px;">
						<th style="width:100px;text-align:center;"> No. 		</th> 
						<th style="width:100px;text-align:center;"> Country    	</th> 
						<th style="width:208px;text-align:center;"> File Name   </th> 
						<th style="width:100px;text-align:center;"> Uploader  	</th> 
						<th style="width:100px;text-align:center;"> Date  		</th> 
						<th style="width:150px;text-align:center;"> Action 		</th>   
					</tr>
					
					<?php 
						$x=0;
						//print_r($restore_points);
						foreach($restore_points as $r)
						{
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							extract($r);
							echo "<tr>";
								echo "<td $c >". $x 		  ."</td>";
								echo "<td $c style='text-align:left'>". $countryName ."</td>";
								echo "<td $c style='text-align:left'>". $name 		  ."</td>";
								echo "<td $c style='text-align:left'>". $full_name 		  ."</td>";
								echo "<td $c >". $tdate 		  ."</td>";
								echo "<td $c style='text-align:center;'>";
								if($DELETE & $_SESSION['super_admin']!='y')	
									echo "<a style='cursor:pointer' onclick='deleteOneItem($rID)'>Delete</a> | ";
								if($RESTORE & $_SESSION['super_admin']!='y')	
									echo "<a style='cursor:pointer' onclick='restoreOneItem($rID)'>Restore</a> | ";
								if($RESTORE & $_SESSION['super_admin']!='y')	
									echo "<a style='cursor:pointer' onclick='viewDialog($rID)'>Preview</a> ";
							echo "</td>
							</tr>";
						}
					?>
				</table>
			</form>

			</div>
           
        <div class="clear"></div>
    </div>
	
	
	<br/>
	<style>
	.working_area
	{
	margin-bottom: -25px!important;
	}
	</style>
	<div id="dialog-form" title="LIST OF ITEMS" style='display:none;'>
	<div id="List_of_Items"></div>
	
	<script type="text/javascript">
		function submitForm()
		{
		document.getElementById('loading').style.display = 'block';
		document.getElementById('eCatalogFORM').submit();
		}
		
		function viewDialog(id)
		{
		$( "#dialog-form" ).dialog({modal: true,height: 500,
		  width: 950});
		  
		var a = $.ajax({
			url: "<?php echo HTTP_PATH.'zip_preview/readFile/'?>"+id,
			async: false
		}).responseText;
		
		document.getElementById('List_of_Items').innerHTML = a;
		}
  
		function deleteOneItem(id)
		{
			jConfirm("Are you sure you want to delete this file?","Alert",function(r){
				if(r) window.location = "<?php echo HTTP_PATH ?>itemDatabase/restore_points/deleteOneItem/"+ id;
			});
		}
		
		function restoreOneItem(id)
		{
			jConfirm("Are you sure you want to restore this file?","Alert",function(r){
				if(r) window.location = "<?php echo HTTP_PATH ?>zip_restore/restore/"+ id;
			});
		}
	</script>	