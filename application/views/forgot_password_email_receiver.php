    <div class="content">
		
    	<div class="fl title-content">
        	<h2>Forgot Password: Email Recipient</h2>
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
				$action = HTTP_PATH ."users/forgot_password_email_receiver/insert";
				
				if(!$ADD){
					$action = '#';
				}
				
				//ISSET ID
				$full_name="";
				$email_address="";
				$main_default="";
				if(isset($id))
				{
					$action = HTTP_PATH ."users/forgot_password_email_receiver/update/".$id;
					$sql = $CI->db->query("SELECT full_name, email_address, main_default FROM forgot_password_email_receiver WHERE id= $id");
					$sql = $sql->result_array();
					extract($sql);
					
					$full_name 	   = $sql[0]['full_name'];
					$email_address = $sql[0]['email_address'];
					$main_default 	   = $sql[0]['main_default'];
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
				echo $CI->forms->form_fields('text','full_name',$full_name,'ADD FULL NAME','r');
				echo $CI->forms->form_fields('text','email_address',$email_address,'ADD EMAIL ADDRESS','r');
				echo $CI->forms->form_fields('text','main_default',$main_default,'DEFAULT','r');
				echo "<div style='width: 216px;float:left;margin-top: 22px;margin-right: -129px;' class='fl'>";
					echo $CI->forms->form_submit('SMBi');
				echo "</div>";
				echo "</form>";
			}
			?>
			

			<!-- STATUS TABLE  -->
			<form name="SMBi2" id="statusTable" action="<?php echo HTTP_PATH ?>users/forgot_password_email_receiver/deleteSelectedItem" method="POST"> 
				<?php echo $csrf ?>
				 <div class="clear"></div>
				 <p style='color:#555;'>*Their should be 1 default email address from the list. Email will be directly sent to him, others will receive a copy.</p>
				<table cellpadding="0" cellspacing="0" style="width:100%;">
				
					<tr style="border-radius: 6px;">
						<th style="width:10px;text-align:center;"> No 		</th> 
						<th style="width:200px;text-align:center;"> FULL NAME 		</th> 
						<th style="width:200px;text-align:center;"> EMAIL ADDRESS 	</th> 
						<th style="width:100px;text-align:center;"> DEFAULT 	</th> 
						<th style="width:150px;text-align:center;"> ACTION </th>   
					</tr>
					
					<?php 
						$x=0;
						foreach($status as $s)
						{
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							extract($s);
							echo "<tr>";
								echo "<td $c >". $x ."</td>";
								echo "<td $c >". $full_name ."</td>";
								echo "<td $c >". $email_address ."</td>";
								echo "<td $c >". $main_default ."</td>";
								echo "<td $c style='text-align:center;'>";
								if($EDIT)
									echo "<a href='".HTTP_PATH."users/forgot_password_email_receiver/edit/".$id."'>Edit</a> |"; 
								if($DELETE)	
									echo "<a style='cursor:pointer' onclick='deleteOneItem($id)'>Delete</a> </td>";
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
	<div style="text-align:center">
	
	
	<?php if($last>0){ ?>
		<ul class="pagination">
				<a href="<?php echo HTTP_PATH."users/forgot_password_email_receiver/page/1" ?>"><li class="page-btn" style="margin-left:3px;"> &laquo; FIRST </li></a> 
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
						$page_link = HTTP_PATH."users/forgot_password_email_receiver/page/".$i++;
						if($active_page==$page++)
						{
							$style="style='text-decoration:underline'";
						}
						echo  " <a href='$page_link' $style><li>". $page2++ ."</li></a> ";
						$l--;
					}
				?>
				<a href="<?php echo HTTP_PATH."users/forgot_password_email_receiver/page/".$last ?>"><li class="page-btn">LAST &raquo;</li></a>
		</ul>
	<?php } ?>
    </div>
	
	<script type="text/javascript">
		function deleteOneItem(id)
		{
			jConfirm("Are you sure you want to delete this email recipient?","Alert",function(r){
				if(r) window.location = "<?php echo HTTP_PATH ?>users/forgot_password_email_receiver/deleteOneItem/"+ id;
			});
		}
		
		
		function submitforgot_password_email_receiver()
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
