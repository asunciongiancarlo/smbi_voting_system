    <div class="content">
		
    	<div class="fl title-content">
        	<h2>iWant Minumum Number of Nominees</h2>
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
		
				//ACTION STATEMENT
				$action = "";
				
				//ISSET ID
				$num_commitee="";
				if(isset($id))
				{
					$action = HTTP_PATH ."users/iWant_min_number_commitees/update/".$id;
					$sql = $CI->db->query("SELECT num_commitee, countryName FROM 
										   iWantCampaignNumber_of_commitees, country
										   WHERE iWantCampaignNumber_of_commitees.id=$id AND 
										   iWantCampaignNumber_of_commitees.countryID = country.id");
					$sql = $sql->result_array();
					extract($sql);
					
					$num_commitee = $sql[0]['num_commitee'];
					$countryName  = $sql[0]['countryName'];
				}
				if(isset($POST)) extract($POST);
			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
			<?php 
			if(isset($id)){
				$CI =& get_instance();
				
				$CI->load->library('forms');
				echo $CI->forms->form_header('SMBi','statusFORM',$action);
				echo $CI->forms->form_fields('text','num_commitee',$num_commitee,'EDIT NUM OF COMMITEE','r');
				echo "<input type='hidden' name='countryName' value='$countryName'>";
				
				echo "<div style='margin-top: 20px;float:left; width:40%;'>";
					echo $CI->forms->form_submit('SMBi');
				echo "</div>";
				echo "</form>";
			}
			?>
			

			<!-- STATUS TABLE  -->
			<div class="clear"></div>
			<table cellpadding="0" cellspacing="0" style="width:100%;">
			
				<tr style="border-radius: 6px;">
					<th style="width:30px;">				    No</th> 
					<th style="width:200px;text-align:center;"> NUMBER OF NOMINEES </th> 
					<th style="width:200px;text-align:center;"> COUNTRY </th> 
					<th style="width:150px;text-align:center;"> ACTION </th>   
				</tr>
				
				<?php 
					$x=0;
					foreach($iWantCampaignNumber_of_commitees as $i)
					{
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
						extract($i);
						echo "<tr>";
							echo "<td $c > $x </td>";
							echo "<td $c >". $num_commitee ."</td>";
							echo "<td $c > $countryName </td>";
							echo "<td $c style='text-align:center;'>";
							if($EDIT)
								echo "<a href='".HTTP_PATH."users/iWant_min_number_commitees/edit/".$cID."'>Edit</a>"; 
						echo "</tr>";
					}
				?>
			</table>
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>