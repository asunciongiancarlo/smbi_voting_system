    <div class="content">
		
    	<div class="fl title-content">
        	<h2>USER MANUAL</h2>
        </div>
		<div style="float:right;margin: 16px;">
			<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
		</div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container2">
			<?php
				$CI    =& get_instance();
				$action="";
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}
				
				//ISSET ID
				$module_name="";
				if(isset($id))
				{
					$action = HTTP_PATH ."users/user_manual/update/".$id;
					$sql = $CI->db->query("SELECT module_name FROM user_manual WHERE id= $id");
					$sql = $sql->result_array();
					extract($sql);
					
					$module_name = $sql[0]['module_name'];
				}
			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
			<?php 
			if(isset($id)){
				$CI =& get_instance();
				echo "<label><b>EDIT: $module_name's MANUAL </b></label>";
				$CI->load->library('forms');
				echo $CI->forms->form_header('SMBi','statusFORM',$action);
				echo "<input type='hidden' name='module_name' value='$module_name'>";
				echo "<div class='fl' style='font-size:10px;margin-top:-30px;'>";
				echo $CI->forms->form_fields2('file','userfile','','BRAND','o');
				echo "</div>";
				echo $CI->forms->form_submit('SMBi');
				echo "</form>";
			}
			?>
			

			<!-- STATUS TABLE  -->
			 <div class="clear"></div>
				<table cellpadding="0" cellspacing="0" style="width:100%;">
				
					<tr style="border-radius: 6px;">
						<th style="width:10px;text-align:center;">  NO 			</th> 
						<th style="text-align:left;"> MODULE 					</th> 
						<th style="width:150px;text-align:center;"> USER MANUAL </th>   
						<th style="width:150px;text-align:center;"> ACTION 		</th>   
					</tr>
					
					<?php 
						$x=0;
						foreach($status as $s)
						{
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							extract($s);
							
							$user_manual = ($user_manual=="") ? "default.pdf" : $user_manual;
							
							echo "<tr>";
								echo "<td $c > $x </td>";
								echo "<td $c style='text-align:left;'> $module_name </td>";
								echo "<td $c > <a href='".HTTP_PATH."files/user_manual/$user_manual' target='_new'>User Manual</a> </td>";
								echo "<td $c style='text-align:center;'>";
								if($EDIT)
									echo "<a href='".HTTP_PATH."users/user_manual/edit/".$id."'>Edit</a>"; 
							echo "</tr>";
						}
					?>
				</table>
			
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>