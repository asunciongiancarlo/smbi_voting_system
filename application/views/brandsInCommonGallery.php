    <div class="content">
		
    	<div class="fl title-content">
        	<h2>COMMON GALLERY - FEATURED BRANDS </h2>
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
				
				//ISSET ID
				$brandVal="";
				if(isset($id))
				{
					$action = HTTP_PATH ."itemDatabase/brands/update/".$id;
				}
			?>
		
			
			<br/>
			
				<!-- STATUS TABLE  -->
			<form name="SMBi2" id="statusTable" action="<?php echo HTTP_PATH ?>itemDatabase/brandsInCommonGallery/update" method="POST"> 
				<center>
				<?php 
					$CI =& get_instance();
					$CI->load->library('forms');
					echo "<div style='width:100%;height:63px;'>";
						echo $CI->forms->form_submit('SMBi');
					echo "</div>";
				?>
					<?php echo $csrf ?>
					 <div class="clear"></div>
					<table cellpadding="0" cellspacing="0" style="width:100%;">
					
						<tr style="border-radius:6px;">
							<th style='text-align:center;'> <input type="checkbox" onclick="checkedAll()" style="margin-right:10px;"> SELECT ALL</th> 
							<th style='text-align:center;'> BRAND NAME </th>   
						</tr>
						
						<?php 
							$x=0;
							foreach($status as $s)
							{
								$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
								extract($s);
								echo "<tr>";
									echo "<td $c > <input type='checkbox' name='checkBoxVar[]' ". brandChecker($id) ." value='$id' id='checkBoxVar'>  </td>";
									echo "<td $c style='text-align:left;padding-left:250px;'>". $brandName ."</td>";
								echo "</tr>";
							}
						?>
					</table>
					</center>
			</form>
			
            </div>
			
        </div>
           
        <div class="clear"></div>
    </div>
	
	
	<br/>
	<div style="text-align:center">
	
	
	<?php if($last>0){ ?>
		<ul class="pagination">
				<a href="<?php echo HTTP_PATH."itemDatabase/brands/page/1" ?>"><li class="page-btn" style="margin-left:3px;"> &laquo; FIRST </li></a> 
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
						$page_link = HTTP_PATH."itemDatabase/brands/page/".$i++;
						if($active_page==$page++)
						{
							$style="style='text-decoration:underline'";
						}
						echo  " <a href='$page_link' $style><li>". $page2++ ."</li></a> ";
						$l--;
					}
				?>
				<a href="<?php echo HTTP_PATH."itemDatabase/brands/page/".$last ?>"><li class="page-btn">LAST &raquo;</li></a>
		</ul>
	<?php } ?>
    </div>
	<?php 
	//MODULE CHECKER
		function brandChecker($id)
		{
			$CI =& get_instance();
			$sql = $CI->db->query("SELECT * FROM  commonGalleryBrands WHERE brandID = $id");
			$b = $sql->result_array();
			
			if($b!=NULL)
			{
				return "checked";
			}
		}
	?>
	
	<script type="text/javascript">

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
	