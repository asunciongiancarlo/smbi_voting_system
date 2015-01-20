    <div class="content">
		
    	<div class="title-content">
        	<h2>Voter Type</h2>
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
				$action = HTTP_PATH ."iLikeCampaign/voterType/insert";
				
				//ISSET ID
				$voterTypeVal="";
				if(isset($id))
				{
					$action = HTTP_PATH ."iLikeCampaign/voterType/update/".$id;
					$sql = $CI->db->query("SELECT voterTypeName FROM voterType WHERE id= $id");
					$sql = $sql->result_array();
					extract($sql);
					
					$voterTypeVal = $sql[0]['voterTypeName'];
				}
			?>
			
			<!-- STATUS FORM  -->
			<div id="errorContainer">
			</div>
			<?php 
				$CI =& get_instance();
				$CI->load->library('forms');
				echo $CI->forms->form_header('SMBi','voterFORM',$action);
				echo $CI->forms->form_fields('text','voterTypeName',$voterTypeVal,'VOTER TYPE NAME','r');
				echo $CI->forms->form_submit('SMBi');
				echo "</form>";
			?>
			

			<!-- STATUS TABLE  -->
			<form name="SMBi2" id="statusTable" action="<?php echo HTTP_PATH ?>iLikeCampaign/voterType/deleteSelectedItem" method="POST"> 
				<?php echo $csrf ?>
				<table cellpadding="0" cellspacing="0" style="width:100%;margin: 0px auto;">
				
					<tr style="border-radius: 6px;">
						<th style="width:100px;padding-left:50px;"> <input type="checkbox" onclick="checkedAll()" style="margin-right:10px;"> SELECT ALL</th> 
						<th style="width:200px;text-align:center;">  ACCOUNT TYPE </th> 
						<th style="width:150px;text-align:center;">  ACTION </th>   
					</tr>
					
					<?php 
						$x=0;
						foreach($status as $s)
						{
							$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
							extract($s);
							echo "<tr>";
								echo "<td $c style='padding-left:100px;'> <input type='checkbox' name='checkBoxVar[]' value='$id' id='checkBoxVar'>  </td>";
								echo "<td $c >". $voterTypeName ."</td>";
								echo "<td $c style='text-align:center;'><a href='".HTTP_PATH."iLikeCampaign/voterType/edit/".$id."'>Edit</a> | <a style='cursor:pointer' onclick='deleteOneItem($id)'>Delete</a> </td>";
							echo "</tr>";
						}
					?>
				</table>
			</form>
			<p style="font-weight:bold;cursor:pointer;" onclick="deleteSelectedItem()"> DELETE SELECTED ITEMS </p>
			
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>
	
	
	<br/>
	<div style="text-align:center">
	
	
	<?php if($last>0){ ?>
		<ul class="pagination">
				<a href="<?php echo HTTP_PATH."iLikeCampaign/voterType/page/1" ?>"><li class="page-btn" style="margin-left:3px;"> &laquo; FIRST </li></a> 
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
						$page_link = HTTP_PATH."iLikeCampaign/voterType/page/".$i++;
						if($active_page==$page++)
						{
							$style="style='text-decoration:underline'";
						}
						echo  " <a href='$page_link' $style><li>". $page2++ ."</li></a> ";
						$l--;
					}
				?>
				<a href="<?php echo HTTP_PATH."iLikeCampaign/voterType/page/".$last ?>"><li class="page-btn">LAST &raquo;</li></a>
		</ul>
	<?php } ?>
    </div>
	
	<script type="text/javascript">
		function deleteOneItem(id)
		{
			if(confirm("Are you sure you want to delete this Voter Type?"))
			{
				window.location = "<?php echo HTTP_PATH ?>iLikeCampaign/voterType/deleteOneItem/"+ id;
			}
		}
		
		function deleteSelectedItem()
		{
			if(confirm("Are you sure you want to delete multiple Voter Types?"))
			{
				document.getElementById("statusTable").submit();
			}
		}
		
		function submitvoterType()
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