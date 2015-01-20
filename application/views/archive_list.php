    <div class="content">
		
    	<div class="title-content">
        	<h2>Archive List</h2>
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
			
				//ISSET ID
				$brandVal="";
				if(isset($id))
				{
					$action = HTTP_PATH ."itemDatabase/brands/update/".$id;
					$sql = $CI->db->query("SELECT brandName FROM brands WHERE id= $id");
					$sql = $sql->result_array();
					extract($sql);
					
					$brandVal = $sql[0]['brandName'];
				}
			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
			<!-- STATUS TABLE  -->
			<form name="SMBi2" id="statusTable" action="<?php echo HTTP_PATH ?>itemDatabase/brands/deleteSelectedItem" method="POST"> 
				<?php echo $csrf ?>
				 <div class="clear"></div>
				<table cellpadding="0" cellspacing="0" style="width:100%;">
				
					<tr style="border-radius: 6px;">
						<th style="width:10px;text-align:center;background-color: rgb(175, 168, 168);">  No.    		 	</th> 
						<th style="width:110px;text-align:center;background-color: rgb(175, 168, 168);"> Date Added    		</th> 
						<th style="width:500px;text-align:center;background-color: rgb(175, 168, 168);"> Archive Name 		</th>   
						<th style="width:150px;text-align:center;background-color: rgb(175, 168, 168);"> Date From 	 		</th>
						<th style="width:150px;text-align:center;background-color: rgb(175, 168, 168);"> Date To 	 		</th>
						<th style="width:264px;text-align:center;background-color: rgb(175, 168, 168);"> Action 	 	 	</th>
					</tr>
					
					<?php 
						$x=0;
						foreach($archive_list as $s)
						{
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							extract($s);
							echo "<tr>";
								echo "<td $c >". $x 								 	."</td>";
								echo "<td $c >".  date("M d, Y", strtotime($dateAdded))	."</td>";
								echo "<td $c >". str_replace('_',' ',$archive_name)  	."</td>";
								echo "<td $c >".  date("M d, Y", strtotime($startDate))	."</td>";
								echo "<td $c >".  date("M d, Y", strtotime($endDate))	."</td>";
								echo "<td $c style='text-align:center;'>";
									echo "<a href='".HTTP_PATH."users/archive_details/archive_catalogue/".$id."'> Catalogue </a> | "; 
									echo "<a href='".HTTP_PATH."users/archive_details/archive_item_db/".$id."'> Item Database </a> | "; 
									echo "<a href='".HTTP_PATH."downloadZipFile/db/".$id."' target='_new'> Download Resources </a>"; 
									echo "</td>";
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
	<script>	

	  function showItems(dID,cID)
	  {
		if(document.getElementById(dID).style.display == "none"){
			ajax('<?php echo HTTP_PATH ?>users/showItems/'+ cID ,dID);
			document.getElementById(dID).style.display = "block";
		}	
		else{ 
			document.getElementById(dID).style.display = "none";
		}
	  }
	</script>

