<div>
 <?php 
			$CI =& get_instance();
			foreach($items as $i){
			
			extract($i);
			$sql = $CI->db->query("SELECT image FROM items_images WHERE id = (SELECT MAX(ID) FROM items_images WHERE itemID = $itemID) LIMIT 1");
			$item_img = $sql->result_array();
			extract($item_img);
			$item_img = isset($item_img[0]['image']) ? $item_img[0]['image'] : 'blank.png';
			?>
			<div class="common-gal fl" style='margin:5px;'>
				<div class="common-gal-heading">  </div>
				<div class="clear"></div>
				 
				 <div class="clear" style='text-align:center;width:210px;overflow:hidden'>
				     <img src="<?php echo HTTP_PATH?>img/small/<?php echo $item_img ?>" style='height:160px'></center>
		         </div>
			     <div style='text-align:center;'>
					<a href="<?php echo HTTP_PATH.'gallery/itemInfo/'.$itemID ?>"> Item Info</a>
					<hr style='margin:2px 0'>
					<h4 class='ptitle' id='pt<?php echo $itemID ?>' alt='' title="<?php echo $itemName?>"><?php echo $itemName?></h4>
					<p style='color:gray;height:20px;'><?php echo $brandName ?></p>
			     </div>
			</div>
			
	 <?php } ?>
</div>

<div class="clear"></div>
<div style="text-align:center;">
	
<?php if($last>0){ ?>
		<ul class="pagination">
				<a href="<?php echo HTTP_PATH."eCatalog/preview/page/1" ?>"><li class="page-btn" style="margin-left:3px;"> &laquo; FIRST </li></a> 
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
						$page_link = HTTP_PATH."eCatalog/preview/page/".$i++;
						if($active_page==$page++)
						{
							$style="style='text-decoration:underline'";
						}
						echo  " <a href='$page_link' $style><li>". $page2++ ."</li></a> ";
						$l--;
					}
				?>
				<a href="<?php echo HTTP_PATH."eCatalog/preview/page/".$last ?>"><li class="page-btn">LAST &raquo;</li></a>
		</ul>
	<?php } ?>
</div>



