
<div class="content">
	<style>
		@media (min-width: 1200px)
		.span4 {
		width: 272px;
		margin: 0 0 0 20px;
		}
	</style>
	<div class="title-content">
		<h2 class='fl'> E-CATALOG </h2>
		<a href='<?php echo HTTP_PATH.'eCatalog' ?>'>
		<p class='fl' style='text-align:right;margin-left:765px;margin-top:10px;color:white;'> 
			CMS 
		</p>
		</a>
	</div>
	<div class="clear"></div>

	<div class="working_area" >
		<div class="container2" style='background:white;'>
		
		<?php
			
		echo "<div class='e-cat-gallery'>";
		foreach($eCatalog as $eC)
		{
			extract($eC);
			$download_icon = HTTP_PATH.'/img/download.jpg';
			$cover_img 	   = HTTP_PATH.'/img/cover/'.$cover;
			
			echo "<div class='span4 e-catalogue'> 
					<a href='e-catalogue-gallery.html'>
						<img src='$download_icon' width='51' height='71' class='download-pdf'>
					</a>
					<center><img src='$cover_img' width='270' height='312'></center>  
					<p class='eCatalog_desc'> 
						<span>$title</span><br/> 
						<label class='short_desc'>
							$short_description 
						<label>
						<label class='cover_date'>$date </label>
					</p>
				</div>";
		}
		echo "</div>";
		?>
		
		
			<?php
				$CI =& get_instance();
				
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}
			?>
		
			<div class="clear"></div>
			<a href="<?php echo HTTP_PATH ."eCatalog/catalog/add" ?>"> 
			<div class="sub-link">
				<ul>
					<li> <img src="<?php echo HTTP_PATH ?>img/plus.png" width="31" height="31"> </li>
					<li> <h5>Add<br/>E-CATALOG</h5> </li>
				</ul>
			</div>
			</a>
			
			<table cellpadding="0" cellspacing="0" style="100%">
			
				<tr style="border-radius: 6px;">
					<th style="width:200px;text-align:center;"> COVER 			    	</th> 
					<th style="width:200px;text-align:center;"> TITLE 			    	</th> 
					<th style="width:200px;text-align:center;"> SHORT DESCRIPTION 		</th> 
					<th style="width:200px;text-align:center;"> DATE 			 		</th> 
					<th style="width:150px;text-align:center;"> ACTION 					</th>   
				</tr>
				
				<?php
			
					$x=0;
					print_r($eCatalog);
					
					foreach($eCatalog as $eC)
					{
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
						extract($eC);
						echo "<tr>";
							echo "<td $c > <img src='". HTTP_PATH."img/cover/".$cover  ."' </td>";
							echo "<td $c >". $title  					."</td>";
							echo "<td $c >". $short_description 		."</td>";
							echo "<td $c >". $date 						."</td>";
							echo "<td $c style='text-align:center;'>";
							if($REVIEW)
								echo "<a href='".HTTP_PATH."eCatalog/catalogPreview/view/".$id."'>View</a> |";  
							if($EDIT)
								echo "<a href='".HTTP_PATH."eCatalog/catalog/edit/".$id."'>Edit</a> |";  
							if($DELETE)
								echo "<a style='cursor:pointer' onclick='deleteOneItem($id)'>Delete</a> </td>";
						echo "</tr>";
					}
				?>
			</table>
            </div>
	</div>
</div>
	

	
<script>	
function deleteOneItem(id)
{
	if(confirm("Are you sure you want to delete this e-Catalog?"))
	{
		window.location = "<?php echo HTTP_PATH ?>eCatalog/catalog/deleteOneItem/"+ id;
	}
}
	
	
</script>

</div>
	<div class="clear" style="height:20px;"></div>
</div>

	