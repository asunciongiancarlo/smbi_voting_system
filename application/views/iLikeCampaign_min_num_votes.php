    <div class="content">
		
    	<div class="fl title-content">
        	<h2> iLike Minimum Number of Votes</h2>
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
				$min_num_votes="";
				if(isset($id))
				{
					$action = HTTP_PATH ."users/iLikeCampaign_min_num_votes/update/".$id;
					$sql = $CI->db->query("SELECT min_num_votes, countryName FROM 
										   iLikeCampaign_min_num_votes, country
										   WHERE iLikeCampaign_min_num_votes.id=$id AND 
										   iLikeCampaign_min_num_votes.countryID = country.id");
					$sql = $sql->result_array();
					extract($sql);
					
					$min_num_votes = $sql[0]['min_num_votes'];
					$countryName  = $sql[0]['countryName'];
				}
			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
			<?php 
			if(isset($id)){
				$CI =& get_instance();
				
				$CI->load->library('forms');
				echo $CI->forms->form_header('SMBi','statusFORM',$action);
				echo $CI->forms->form_fields('text','min_num_votes',$min_num_votes,'EDIT NUM OF COMMITEE','r');
				echo "<input type='hidden' name='countryName' value='$countryName'>";
				echo $CI->forms->form_submit('SMBi');
				echo "</form>";
			}
			?>
			

			<!-- STATUS TABLE  -->
			<div class="clear"></div>
			<table cellpadding="0" cellspacing="0" style="width:100%;">
			
				<tr style="border-radius: 6px;">
					<th style="width:30px;">				    No</th> 
					<th style="width:200px;text-align:center;"> Number of Commitees </th> 
					<th style="width:200px;text-align:center;"> COUNTRY </th> 
					<th style="width:150px;text-align:center;"> ACTION </th>   
				</tr>
				
				<?php 
					$x=0;
					foreach($iLikeCampaign_min_num_votes as $i)
					{
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
						extract($i);
						echo "<tr>";
							echo "<td $c > $x </td>";
							echo "<td $c >". $min_num_votes ."</td>";
							echo "<td $c > $countryName </td>";
							echo "<td $c style='text-align:center;'>";
							if($EDIT)
								echo "<a href='".HTTP_PATH."users/iLikeCampaign_min_num_votes/edit/".$cID."'>Edit</a>"; 
						echo "</tr>";
					}
				?>
			</table>
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>