    <div class="content">
		
    	<div class="fl title-content">
        	<h2>ADMIN USERS</h2>
        </div>
        <div style="float:right;margin: 16px;">
			<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
		</div>
		
        <div class="clear"></div>
	
        <div class="working_area">
			
			<div class="container2">
			<?php 
				//MESSAGE ALERT
				if(isset($msg)){
					$CI =& get_instance();
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}	
			
			
			if($ADD){
			?>
			<a href="<?php echo HTTP_PATH ."users/admin_users/add" ?>"> 
				<div class="sub-link">
				<ul>
					<li><img src="<?php echo HTTP_PATH ?>img/plus.png" width="31" height="31"> </li>
					<li><h5>Add<br/>User</h5> </li>
				</ul>
				</div>	
			</a>
			<?php } ?>
			<?php //print_r($admin_users); ?>
			
			<!-- STATUS TABLE  -->
			
				<?php echo $csrf ?>
				<table id="large" cellspacing="0" class="tablesorter" style="width:100%;">
				<thead>
					<tr style="border-radius: 6px;cursor:pointer;">
						<th style="width:100px;text-align:center;" title='Sort by Country'> COUNTRY </th> 
						<th style="width:200px;" title='Sort by Department'> DEPARTMENT			   	</th> 
						<th style="width:200px;" title='Sort by Full name'> FULL NAME			   	    </th> 
						<th style="width:200px;" title='Sort by User Name'> USER NAME			   	    </th> 
						<th style="width:214px;text-align:center;"> ACTION  </th>   
					</tr>
				</thead>
				<tbody>
					<?php 
						$x=0;
						//print_r($admin_users);
						foreach($admin_users as $s)
						{
							//$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							$c="";
							extract($s);
							echo "<tr>";
								echo "<td $c style='text-align:left;'>". $countryName ."</td>";
								echo "<td $c style='text-align:left;'>". $department_name ."</td>";
								echo "<td $c style='text-align:left;'>". $full_name ."</td>";
								echo "<td $c style='text-align:left;'>". $uname ."</td>";
								
								
								echo "<td $c style='text-align:center;'>";
								if($EDIT)
									echo "<a href='".HTTP_PATH."users/admin_users/edit/".$adminID."'>Edit</a> |"; 
								if($DELETE)
									echo "<a style='cursor:pointer' onclick='deleteOneItem($adminID)'>Delete</a> | ";
								if($ADD)
									echo "<a href='".HTTP_PATH."users/admin_users/duplicate/".$adminID."'>Duplicate</a> |"; 
								if(history($adminID))
									echo " <a onclick='history($adminID)' style='cursor:pointer;'>History</a>";
							echo "</td>
								</tr>";
						}
					?>
				</tbody>
				</table>			
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>
	
	
	<br/>
	<div style="text-align:center">
	<?php 
		function history($adminID)
		{	$CI =& get_instance();
			$CI->load->library('alert');
			$sql = $CI->db->query("SELECT id FROM admin_usersRef WHERE admin_userID = $adminID");
			$row = $sql->result_array();
			if($row)
				return TRUE;
			else
				return FALSE;
		}
	?>
	
	<?php if($last>0){ ?>
		<ul class="pagination">
				<a href="<?php echo HTTP_PATH."users/admin_users/page/1" ?>"><li class="page-btn" style="margin-left:3px;"> &laquo; FIRST </li></a> 
				<?php 
					//PAGNINATION
					$i	   = 1; 
					$page  = 1;
					$page2 = 1;
					$l 	   = $last;
					
					while($l!=0)
					{
						
						//ACTIVE PAGE
						$style="";
						$page_link = HTTP_PATH."users/admin_users/page/".$i++;
						if($active_page==$page++)
						{
							$style="style='text-decoration:underline'";
						}
						echo  " <a href='$page_link' $style><li>". $page2++ ."</li></a> ";
						$l--;
					}
				?>
				<a href="<?php echo HTTP_PATH."users/admin_users/page/".$last ?>"><li class="page-btn">LAST &raquo;</li></a>
		</ul>
	<?php } ?>
    </div>
	
	<div id="dialog-form" title="All BU Items was transferred to: " style='display:none;'>
		<div id="myContent">
		</div>
	</div>
	
	
	<script type="text/javascript">
		function history(auID)
		{
			$( "#dialog-form" ).dialog({modal: true,height: 430,
			width: 900});
			
			var a = $.ajax({
				url: '<?php echo HTTP_PATH ?>users/history/'+auID,
				async: false
			}).responseText;
			
			document.getElementById( "myContent" ).innerHTML = a;
		}
		function deleteOneItem(id)
		{
			jConfirm("Are you sure you want to delete this User?","Alert",function(r){
				if(r) window.location = "<?php echo HTTP_PATH ?>users/admin_users/deleteOneItem/"+ id;
			});	
		}
	</script>
	
	