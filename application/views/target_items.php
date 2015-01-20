    <div class="content">
		
    	<div class="fl title-content">
        	<h2>Target Items per Month</h2>
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
				$ti="";
				$action = HTTP_PATH."users/target_items/insert";
				$target_val="";
				$countryName="";
				$country_ID="";
				if(isset($id))
				{
					$action = HTTP_PATH ."users/target_items/update/".$id;
					$sql = $CI->db->query("SELECT *, target_items.id as iID FROM target_items, country WHERE country.id = target_items.countryID AND target_items.id = $id");
					$sql = $sql->result_array();
					extract($sql);
					
					$country_ID     = $sql[0]['countryID'];
					$target_val    = $sql[0]['target'];
					$countryName    = $sql[0]['countryName'];
				}
			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
			<?php
			$CI         = & get_instance();
			$CI->load->database('default');
			$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0";
			$country = $CI->db->query($Csql);
			
			$CI->load->library('forms');
			echo $CI->forms->form_header('SMBi','statusFORM',$action);
			?>
			 <h2 class='fl form' style='width:25%;margin-right:10px;'>COUNTRY</h2>
			 <h2 class='fl form' style='width:25%;margin-right:10px;'>TARGET ITEMS</h2>
			 <div style='clear:both'></div>
			  <select name='countryID' style='width:25%;margin-right:10px;' class='fl'>
				  <?php foreach($country->result_array() as $o) 
						{ 
						 $v = $o['cID'];
						 $t = $o['countryName'];
						 $s = ($country_ID==$v) ? "selected" : "";
						 echo "<option value='$v' $s> $t </option>";   
						}  
				  ?>
				</select>
				<input name="target" value="<?php echo $target_val ?>" class="fl">
			    <input type="hidden" name="countryName" value="<?php echo $countryName ?>">
			<?php
				echo "<div style='width:30%;margin-left:20px;' class='fl'>";
				echo "<input type='submit' name='Submit'>";
				echo "</div>";
				echo "</form>";
			?>
			

			<!-- STATUS TABLE  -->
			<form name="SMBi2" id="statusTable" action="<?php echo HTTP_PATH ?>itemDatabase/brands/deleteSelectedItem" method="POST"> 
				<?php echo $csrf ?>
				 <div class="clear"></div>
				<table cellpadding="0" cellspacing="0" style="width:100%;">
				
					<tr style="border-radius: 6px;">
						<th style="width:200px;text-align:center;"> COUNTRY </th> 
						<th style="width:150px;text-align:center;"> ITEMS   </th>   
						<th style="width:150px;text-align:center;"> ACTION   </th>   
					</tr>
					
					<?php 
						$x=0;
						foreach($target as $s)
						{
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							extract($s);
							echo "<tr>";
								echo "<td $c >". $countryName ."</td>";
								echo "<td $c >". $target ."</td>";
								echo "<td $c style='text-align:center;'>";
								if($EDIT)
									echo "<a href='".HTTP_PATH."users/target_items/edit/".$iID."'>Edit</a>"; 
								if($DELETE)	
									echo " | <a style='cursor:pointer' onclick='deleteOneItem($iID)'>Delete</a> </td>";	
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
			jConfirm("Are you sure you want to delete this Nomination Committee?","Alert",function(r){
				if(r) window.location = "<?php echo HTTP_PATH ?>users/target_items/deleteOneItem/"+ id;
			});
		}

	</script>
