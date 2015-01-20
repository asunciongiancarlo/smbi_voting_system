    <div class="content">
		
    	<div class="fl title-content">
        	<h2><span style='text-transform:lowercase;'>e</span>Catalogue Item Fields</h2>
        </div>
		<div style="float:right;margin: 16px;">
			<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
		</div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container2" style="padding: 30px 50px;">
			<?php
				$CI =& get_instance();
				
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}
				
				$action = HTTP_PATH ."users/ec_item_fields/update/";		
			?>
			
				<!-- STATUS FORM  -->
				<div id="errorContainer">
				</div>
				<?php 
					$CI =& get_instance();
					$CI->load->library('forms');
					$CI2 =& get_instance();
					$CI2->load->library('fv');
					
					echo $CI->forms->form_header('SMBi','statusFORM',$action);		
					echo '<div class="clear"></div>';
					echo '<h2 class="form"> ITEM FIELDS </h2><br/>';
					
					foreach($ec_item_fields as $P)
					{
						extract($P);
						echo "<div class='fl'>";
							echo "<input ". fieldChecker($id) ." type='checkbox' name='fields[]' value='$id' id='checkBoxVar'> $fieldName";
						echo "</div>";
						echo "<br/>";
						echo "<br/>";
					}
					

					function fieldChecker($id)
					{
						$CI =& get_instance();
						$sql = $CI->db->query("SELECT * FROM ec_item_fields WHERE id = $id AND active = 1");
						$sql = $sql->result_array();
						
						if($sql!= NULL)
						{
							return "checked";
						}
					}
				?>
					
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>
	<?php 
	 echo $CI->forms->buttons('GoPar','statusFORM');
		echo "</form>";
	?>  
	
	<br/>
	<div style="text-align:center">
	
