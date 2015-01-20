<?php 
			foreach($items as $i){
			extract($i);
			?>
			
			<div class="common-gal fl items-item" style='margin:20px;'>
				<div class="common-gal-heading" style='background-color:#bb1d1d'> 
					<h4 class='ptitle' id='pt<?php echo $itemID ?>' alt='' title="<?php echo $title?>" style='color:white;text-align:center;padding-top:2px;'><?php echo $title?></h4>
				</div> 
				 <div class="clear" style='border-bottom:1px solid #eeeeee;height:160px;text-align:center;width:210px;overflow:hidden;margin-top:-10px'>
					<?php $w = w($cover); ?>
					
					  <table>
						<tr>
							<td class='gal-Icon-Container'><a href="<?php echo HTTP_PATH?>gallery/eCatalog/view/<?php echo $id?>"><img class='gal-Icon-Img' src="<?php echo HTTP_PATH?>img/cover/<?php echo $cover ?>" style='<?php echo $w ?>'></a></td>
						</tr>	
					  </table> 
					
				 </div>
				 <div style='text-align:center;'>
					<p style='color:#555;height:20px;font-size:12px;margin:0px;'><?php echo "<a href='".HTTP_PATH."files/brand_guidelines/$brand_guidelines' target='_blank'>Brand Guidelines </a>"; ?></p>
					<p style='color:#555;height:20px;font-size:10px;'>Date Updated: <?php echo $tdate ?></p>
				 </div>
			</div>
			
<?php } ?>
<div style='clear:both'></div>

<?php if($EDIT OR $_SESSION['super_admin']=='y'){  ?>
<a href='<?php echo HTTP_PATH ?>eCatalog'><p style="font-weight:bold;cursor:pointer;font-size:14px;margin:10px 0 0 20px;"> MANAGE eCATALOGUE </p> </a>
<?php } ?>

<?php 
	function w($img)
	{
		$w='';
		$HTTP_PATH = getcwd()."/img/cover/$img";
		list($width, $height, $type, $attr) = getimagesize("$HTTP_PATH");
		if($width>$height)
			return $w='width:100%';
		else
			return $w;
	}
?>