<div class="content">
		
	<div class="title-content fl">
		<h2> <span style='text-transform: lowercase;'>e</span>CATALOGUE ITEMS</h2>
	</div>
	
	<div style="float:right;margin: 16px;">
		<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
	</div>
	
	<div class="clear"></div>

	<div class="working_area" >
		<div class="container2 form" style='background:white;'>
			<?php
				$CI =& get_instance();
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}
			?>
		
			<div class="clear"></div>
			<?php 
			if($ADD){ ?>
			<a href="<?php echo HTTP_PATH ."eCatalog/items/add" ?>"> 
			<div class="sub-link">
				<ul>
					<li> <img src="<?php echo HTTP_PATH ?>img/plus.png" width="31" height="31"> </li>
					<li> <h5>Add<br/><span style='text-transform:lowercase;'>e</span>CATALOGUE ITEM</h5> </li>
				</ul>
			</div>
			</a>
			<?php } ?>
			
			<table cellpadding="0" cellspacing="0" style="100%">
			
				<tr style="border-radius: 6px;">
					<th style="width:200px;text-align:center;"> BRAND 			    				</th> 
					<th style="width:200px;text-align:center;"> TITLE 			    				</th> 
					<th style="width:200px;text-align:center;"> BRAND GUIDELINES 			   		</th> 
					<th style="width:200px;text-align:center;"> DATE UPDATED 			 			</th> 
					<th style="width:150px;text-align:center;"> ACTION 								</th>   
				</tr>
				
				<?php
					$x=0;
					$y=1;
					$z=1;
					//print_r($eCatalog);
					foreach($eCatalog as $eC)
					{
						$c = (($x++)%2) == 0 ? "class='alter3'" :  ""; 
						extract($eC);
						echo "<tr>";
							echo "<td $c > <a href='".HTTP_PATH."gallery/eCatalog/view/$id'> <img src='". HTTP_PATH."img/cover/".$cover  ."' width='100px;'> </a> </td>";
							echo "<td $c >". $title  					."</td>";
							echo "<td $c > <a href='".HTTP_PATH."files/brand_guidelines/$brand_guidelines' target='_blank'>Brand Guidelines </a></td>";
							echo "<td $c >". $tdate 						."</td>";
							echo "<td $c style='text-align:center;'>"; ?>
							<label onclick="showItems('<?php echo 'i'.$z++ ?>',<?php echo $id ?>)" title="View Items"> View Items </label>
					<?php 
							echo "</td>";
						echo "</tr>"; ?>
					<tr>
						<td colspan='5' style='padding: 0;'>
							<div id='i<?php echo $y++ ?>' style='display:none;height:151px;overflow-y:scroll;'>
						</td>
					</tr>
				   <?php } ?>
			</table>
            </div>
	</div>
</div>
	<script>	

	  function showItems(dID,cID)
	  {
		if(document.getElementById(dID).style.display == "none"){
			ajax('<?php echo HTTP_PATH ?>eCatalog/showItems/'+ cID ,dID);
			document.getElementById(dID).style.display = "block";
		}	
		else{ 
			document.getElementById(dID).style.display = "none";
		}
	  }

	
	function deleteOneItem(id)
	{
		jConfirm("Are you sure you want to delete this Item?","Alert",function(r){
			if(r) window.location = "<?php echo HTTP_PATH ?>eCatalog/items/deleteOneItem/"+ id;
		});
		
	}
	</script>
	
	</div>
        <div class="clear" style="height:20px;"></div>
    </div>

	