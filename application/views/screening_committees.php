    <div class="content">
		
    	<div class="fl title-content">
        	<h2>MANAGE SCREENING COMMITTEES</h2>
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
				$action = HTTP_PATH ."itemDatabase/brands/insert";
			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>	
			<?php if($_SESSION['super_admin']!='y'){ ?>
			<a href='<?php echo HTTP_PATH ?>iWantCampaign/screening_committees/add' style='margin-top:20px;'>
				<div class="sub-link">
					<ul>
						<li><img src="<?php echo HTTP_PATH ?>/img/plus.png" width="31" height="31"></li>
						<li> <h5>ADD Screening<br> Group</h5></li>
					</ul>
				</div>
			</a>
			<?php } ?>
			<!-- STATUS TABLE  -->
			<form name="SMBi2" id="statusTable" action="<?php echo HTTP_PATH ?>itemDatabase/brands/deleteSelectedItem" method="POST"> 
				<?php echo $csrf ?>
				 <div class="clear"></div>
				<table cellpadding="0" cellspacing="0" style="width:100%;">
				
					<tr style="border-radius: 6px;">
						<th style="width:50px;">No</th> 
						<th style="width:240px;text-align:center;"> GROUP NAME </th> 
						<th style="width:200px;text-align:center;"> COUNTRY </th> 
						<th style="width:150px;text-align:center;"> USER </th>   
						<th style="width:150px;text-align:center;"> DATE CREATED </th>   
						<th style="width:150px;text-align:center;"> ACTION </th>   
					</tr>
					
					<?php 
						$x=0;
						//print_r($voters_group);
						foreach($voters_group as $s)
						{
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							extract($s);
							echo "<tr>";
								echo "<td $c > $x  			 </td>";
								echo "<td $c style='text-align:left;'> $group_name   </td>";
								echo "<td $c style='text-align:left;padding-left: 40px;'> $countryName  </td>";
								echo "<td $c style='text-align:left;padding-left: 10px;'> $full_name  		 </td>";
								echo "<td $c > $tdate  		 </td>";
								echo "<td $c style='text-align:center;'>";
								if($EDIT_NOMINEES)
									echo "<a href='".HTTP_PATH."iWantCampaign/screening_committees/edit/".$vgID."'>Edit</a> | "; 
								if($EDIT_NOMINEES)	
									echo "<a style='cursor:pointer' onclick='deleteOneItem($vgID)'>Delete</a> </td>";
							echo "</tr>";
						}
					?>
				</table>
			</form>
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>

	
	<script type="text/javascript">
	function deleteOneItem(id)
	{
		jConfirm("Are you sure you want to delete this set of Screening committees?","Alert",function(r){
			if(r) window.location = "<?php echo HTTP_PATH ?>iWantCampaign/screening_committees/deleteOneItem/"+ id;
		});
	}
	</script>