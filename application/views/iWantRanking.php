    <div class="content">
		
    	<div class="fl title-content">
        	<h2><span style='text-transform:lowercase;'>i</span>Want Ranking</h2>
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
				$action = HTTP_PATH ."users/iWantRanking/insert";
				
				if(!$ADD){
					$action = "#";
				}
				
				//ISSET ID
				$minRank="";
				$maxRank="";
				$countryID="";
				if(isset($id))
				{
					$action = HTTP_PATH ."users/iWantRanking/update/".$id;
					$sql = $CI->db->query("SELECT * FROM iWantRanking WHERE id= $id");
					$sql = $sql->result_array();
					extract($sql);
					
					$minRank  = $sql[0]['minRank'];
					$maxRank  = $sql[0]['maxRank'];
					$countryID  = $sql[0]['countryID'];
				}
			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
			<?php
			if($ADD OR $EDIT){
				$CI =& get_instance();
				$CI->load->library('forms');
				echo $CI->forms->form_header('SMBi','statusFORM',$action);
				echo $CI->forms->form_fields('text','minRank',$minRank,'EDIT MINIMUM RANKING','r');
				echo $CI->forms->form_fields('text','maxRank',$maxRank,'EDIT MAX RANKING ','r');
				echo "<input type='hidden' name='countryID' value='$countryID'>";
				echo $CI->forms->form_submit('SMBi');
				echo "</form>";
			}
			?>
			

			<!-- STATUS TABLE  -->
			<form name="SMBi2" id="statusTable" action="<?php echo HTTP_PATH ?>users/iWantRanking/deleteSelectedItem" method="POST"> 
				<?php echo $csrf ?>
				<div class="clear"></div>
				<table cellpadding="0" cellspacing="0" style="width:100%;margin: 0px auto;">
				
					<tr style="border-radius: 6px;">
						<th style="width:200px;text-align:center;"> MINIMUM RANK </th> 
						<th style="width:10px;text-align:center;">  MAXIMUM RANK </th> 
						<th style="width:10px;text-align:center;">  COUNTRY 	 </th> 
						<th style="width:150px;text-align:center;"> ACTION 		 </th>   
					</tr>
					
					<?php 
						$x=0;
						foreach($iWantRanking as $s)
						{
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							extract($s);
							echo "<tr>";
								echo "<td $c >". $minRank ."</td>";
								echo "<td $c >". $maxRank ."</td>";
								echo "<td $c >". $countryName ."</td>";
								echo "<td $c style='text-align:center;'>"; 
								if($EDIT)
									echo "<a href='".HTTP_PATH."users/iWantRanking/edit/".$iWantRankingID."'>Edit</a>"; 
						
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
			jConfirm("Are you sure you want to delete this iWantRanking?","Alert",function(r){
				if(r) window.location = "<?php echo HTTP_PATH ?>users/iWantRanking/deleteOneItem/"+ id;
			});
		}
		
		function deleteSelectedItem()
		{
			jConfirm("Are you sure you want to delete Multiple iWantRanking?","Alert",function(r){
				if(r) document.getElementById("statusTable").submit();
			});
		}
		
		function submitiWantRanking()
		{
			document.getElementById("statusFORM").submit();
		}
		
		
		checked=false;
		function checkedAll (frm1) {
			var aa= document.getElementById('statusTable');
			if (checked == false)
			{
			   checked = true
			}
			else
			{
			  checked = false
			}
			for (var i =0; i < aa.elements.length; i++) 
			{
				aa.elements[i].checked = checked;
			}
		}
    

	</script>