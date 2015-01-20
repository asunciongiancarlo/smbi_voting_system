    <div class="content">
		
    	<div class="fl title-content">
        	<h2>POSM Fields</h2>
        </div>
		<div style="float:right;margin: 16px;">
			<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
		</div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container2" style="padding: 30px 30px;">
			<?php
				$CI =& get_instance();
				
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}
		
				//ACTION STATEMENT
				$action = HTTP_PATH ."users/POSM_fields/insert";
				$POSM_statusID=0;
				
				if(isset($id))
				{
					$action = HTTP_PATH ."users/POSM_fields/update/".$id;
					$sql = $CI->db->query("SELECT * FROM POSM_Status WHERE id= $id");
					$sql = $sql->result_array();
					extract($sql);
					
					$POSM_statusID = $sql[0]['id'];
				}
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
					echo "<div style='width:97%'>";
					echo $CI->forms->select('POSM_statusID','POSM_Status','statusName',$CI2->fv->label(5),$POSM_statusID,$CI2->fv->v(5),'');
					echo "</div>";
					//echo "<input type='hidden' name='POSM_statusID' value='$POSM_statusID'>";
					echo '<div class="clear"></div>';
					echo '<h2 class="form"> ITEM FIELDS </h2>';
					$CI =& get_instance();
					$CI->load->library('forms');
					
					//print_r($POSM_fields);
					echo "<table style='width: 52%;'>";
					$x=0;
					foreach($POSM_fields as $P)
					{
						extract($P);
						$c = (($x++)%2) == 0 ? "class='alter alter-2'" :  ""; 
						echo "<tr>";
							echo "<td $c style='text-align:left;'><input ". fieldChecker($POSM_statusID,$id) ." type='checkbox' name='fields[]' value='$id' id='checkBoxVar'> $fieldName  </td>";
							echo "<td $c style='text-align:left;'>". $CI->forms->validation_rules2('validations[]',$POSM_statusID,$id,validation_chckr($POSM_statusID,$id)) ."</td>";
						echo "</tr>";
					}
					echo "</table>";
					
					function fieldChecker($POSM_statusID,$POSM_FieldID)
					{
						$CI =& get_instance();
						$sql = $CI->db->query("SELECT * FROM POSM_status_fields WHERE POSM_statusID = $POSM_statusID AND POSM_FieldID = $POSM_FieldID");
						$sql = $sql->result_array();
						
						if($sql!= NULL)
						{
							return "checked";
						}
					}
					
					function validation_chckr($POSM_statusID,$POSM_FieldID)
					{
						$CI =& get_instance();
						$sql = $CI->db->query("SELECT validation FROM POSM_status_fields WHERE POSM_statusID = $POSM_statusID AND POSM_FieldID = $POSM_FieldID LIMIT 0,1");
						$sql = $sql->row();
						
						if($sql) return $sql->validation;
						else 	 return "";
					}
				?>
			

			
            </div>
			
        </div>
		 
  
    </div>
	<?php 
		 echo $CI->forms->buttons('GoPar','statusFORM');
			echo "</form>";
        ?>  
	
	<br/>
	<div style="text-align:center">
	
