    <div class="content">
		
    	<div class="fl title-content">
        	<h2>Countries</h2>
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
				$action = HTTP_PATH ."itemDatabase/Country/insert";
				
				if(!$ADD){
					$action = "#";
				}
				
				//ISSET ID
				$countryVal="";
				$countryCode="";
				$time_zone="";
				if(isset($id))
				{
					$action = HTTP_PATH ."itemDatabase/Country/update/".$id;
					$sql = $CI->db->query("SELECT * FROM country WHERE id= $id");
					$sql = $sql->result_array();
					extract($sql);
					
					$countryVal  = $sql[0]['countryName'];
					$countryCode = $sql[0]['countryCode'];
					$time_zone = $sql[0]['time_zone'];
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
				echo $CI->forms->form_fields('text','country',$countryVal,'ADD COUNTRY NAME','r');
				echo $CI->forms->form_fields('text','countryCode',$countryCode,'ADD COUNTRY CODE','r');
				echo $CI->forms->form_fields('text','time_zone',$time_zone,'ADD TIME ZONE','r');
				echo "<div style='width:19%;float:left; margin-top:21px;'>";
				echo $CI->forms->form_submit('SMBi');
				echo "</div>";
				echo "</form>";
			}
			?>
			

			<!-- STATUS TABLE  -->
			<form name="SMBi2" id="statusTable" action="<?php echo HTTP_PATH ?>itemDatabase/Country/deleteSelectedItem" method="POST"> 
				<?php echo $csrf ?>
				<div class="clear"></div>
				<table cellpadding="0" cellspacing="0" style="width:100%;margin: 0px auto;">
				
					<tr style="border-radius: 6px;">
						<th style="width:100px;"> <input type="checkbox" onclick="checkedAll()" style="margin-right:10px;"> SELECT ALL</th> 
						<th style="width:64px;text-align:center;">  COUNTRY NAME </th> 
						<th style="width:10px;text-align:center;">  COUNTRY CODE </th> 
						<th style="width:10px;text-align:center;">  TIME ZONE    </th> 
						<th style="width:150px;text-align:center;"> ACTION 		 </th>   
					</tr>
					
					<?php 
						$x=0;
						foreach($status as $s)
						{
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							extract($s);
							echo "<tr>";
								echo "<td $c > <input type='checkbox' name='checkBoxVar[]' value='$id' id='checkBoxVar'>  </td>";
								echo "<td $c style='text-align:left;'>". $countryName ."</td>";
								echo "<td $c >". $countryCode ."</td>";
								echo "<td $c style='text-align:left;padding-left:55px;'>". $time_zone ."</td>";
								echo "<td $c style='text-align:center;'>"; 
								if($EDIT)
									echo "<a href='".HTTP_PATH."itemDatabase/Country/edit/".$id."'>Edit</a> |"; 
								if($DELETE)	
									echo "<a style='cursor:pointer' onclick='deleteOneItem($id)'>Delete</a> </td>";
							echo "</tr>";
						}
					?>
				</table>
			</form>
			<?php if($DELETE){ ?>
				<p style="font-weight:bold;cursor:pointer;" onclick="deleteSelectedItem()"> DELETE SELECTED ITEMS </p>
			<?php } ?>
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>
	
	
	<br/>
	<div style="text-align:center">
	
	
	<?php if($last>0){ ?>
		<ul class="pagination">
				<a href="<?php echo HTTP_PATH."itemDatabase/Country/page/1" ?>"><li class="page-btn" style="margin-left:3px;"> &laquo; FIRST </li></a> 
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
						$page_link = HTTP_PATH."itemDatabase/Country/page/".$i++;
						if($active_page==$page++)
						{
							$style="style='text-decoration:underline'";
						}
						echo  " <a href='$page_link' $style><li>". $page2++ ."</li></a> ";
						$l--;
					}
				?>
				<a href="<?php echo HTTP_PATH."itemDatabase/Country/page/".$last ?>"><li class="page-btn">LAST &raquo;</li></a>
		</ul>
	<?php } ?>
    </div>
	
	<script type="text/javascript">
		function deleteOneItem(id)
		{
			jConfirm("Are you sure you want to delete this Country?","Alert",function(r){
				if(r) window.location = "<?php echo HTTP_PATH ?>itemDatabase/Country/deleteOneItem/"+ id;
			});
		}
		
		function deleteSelectedItem()
		{
			jConfirm("Are you sure you want to delete Multiple Country?","Alert",function(r){
				if(r) document.getElementById("statusTable").submit();
			});
		}
		
		function submitcountry()
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