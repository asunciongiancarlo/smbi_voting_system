    <div class="content">
		
    	<div class="fl title-content">
        	<h2>POSM Status Fields</h2>
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
			
			if($ADD){
			?>
			<a href="<?php echo HTTP_PATH ."users/POSM_fields/add" ?>"> 
				<div class="sub-link">
				<ul>
					<li><img src="<?php echo HTTP_PATH ?>img/plus.png" width="31" height="31"> </li>
					<li><h5>Add<br/>POSM FIELDS</h5> </li>
				</ul>
				</div>	
			</a>
			<?php } ?>
			<br/>
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
	

			<!-- STATUS TABLE  -->
			
			 <div class="clear"></div>			
			<table cellpadding="0" cellspacing="0" style="width:100%;">
			
				<tr style="border-radius: 6px;">
					<th style="text-align:center;"> POSM STATUS  </th> 
					<th style="text-align:center;"> ACTION </th>   
				</tr>
				
				<?php 
					$x=0;
					foreach($POSM_fields_rec as $s)
					{
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
						extract($s);
						echo "<tr>";
							echo "<td $c style='text-align:center;'>". $statusName ."</td>";
							echo "<td $c style='text-align:center;'>";
							if($EDIT)
								echo "<a href='".HTTP_PATH."users/POSM_fields/edit/".$POSM_statusID."'>Edit</a> ";
							if($DELETE)
									echo "| <a style='cursor:pointer' onclick='deleteOneItem($POSM_statusID)'>Delete</a> </td>";
						echo "</tr>";
					}
				?>
			</table>
			<br/>
			
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>
	

	
	<script type="text/javascript">
		function deleteOneItem(id)
		{
			if(confirm("Are you sure you want to delete this item?"))
			{
				window.location = "<?php echo HTTP_PATH ?>users/POSM_fields/deleteOneItem/"+ id;
			}
		}
		
		function deleteSelectedItem()
		{
			if(confirm("Are you sure you want to delete multiple Brands?"))
			{
				document.getElementById("statusTable").submit();
			}
		}
		
		function submitbrands()
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