<div>
 <?php 
	//NO SEARCH RESULTS
	//print_r($_POST);
	if(empty($items)){
		include('no_search_result.php');
	}
	
	function convertDate($type='',$dateTime='')
	{
	 $CI =& get_instance();
	 $CI->load->database('default');
	 $utc_date = DateTime::createFromFormat(
		"Y-m-d H:i:s",
		"$dateTime",
		new DateTimeZone('UTC')
	  );
	  
	  if($dateTime=="0000-00-00 00:00:00"){ return "0000-00-00"; die(); }
	  
	  $new_date_format = clone $utc_date; // we don't want PHP's default pass object by reference here
	  //SELECT TIME ZONE FROM THE DB
	  $sql = $CI->db->query("SELECT time_zone FROM country WHERE id = ". $_SESSION['countryID']." LIMIT 0,1");
	  $sql = $sql->row();
	  $new_date_format->setTimeZone(new DateTimeZone($sql->time_zone));
	  
	  if($type=='date') return $new_date_format->format('Y-m-d');
	  else 				return $new_date_format->format('H:i:s');
	}
	
	$CI =& get_instance();	
	foreach($items as $i){
	extract($i);
	$sql = $CI->db->query("SELECT image FROM ecitems_images WHERE id = (SELECT ID FROM ecitems_images WHERE itemID = $itemID AND defaultStatus = 1) LIMIT 1");
	$item_img = $sql->result_array();
	extract($item_img);
	$item_img = isset($item_img[0]['image']) ? $item_img[0]['image'] : 'blank.png';
	
	$CI =& get_instance();
	$sql = $CI->db->query("SELECT typeName FROM POSM_Type WHERE id=$POSMTypeID LIMIT 0,1");
	$row = $sql->row();

	$dateReleased = convertDate('date',"$dateReleased 00:00:00");
	?>
	
		<div class="common-gal fl items-item" style='height:240px;overflow:hidden;'>
			<div class="common-gal-heading" style='text-align:center;height:25px;background:#999999;color:white;font-style:bold;'> <b><?php echo $row->typeName;?></b> </div>
			<div class="clear"></div>
			 <?php $w = w($item_img); ?>
			 <div class="clear"  style='border-bottom:1px solid #eeeeee;height:160px;text-align:center;width:210px;overflow:hidden'>
				<table>
				 <tr>
					<td class='gal-Icon-Container'>
					<a href="<?php echo HTTP_PATH.'gallery/itemInfoECatalog/'.$ecID.'/'.$itemID; ?>"> 
						<img class='gal-Icon-Img' src="<?php echo HTTP_PATH?>img/galleryImg/<?php echo $item_img ?>" style='<?php echo $w ?>'>
					</a>
					</td>
				</tr>	
				</table> 
			 </div>
			 <div style='text-align:center;'>
				<hr style='margin:2px 0'>
				<h4 class='ptitle' id='pt<?php echo $itemID ?>' alt='' title="<?php echo $itemName?>" style='font-size:15px;'><?php echo $itemName?></h4>
				<label class='dateReleased' style='margin-top:-5px;'>Date Published: <?php echo date("M d, Y", strtotime($dateReleased));?><label>
					<label class='dateReleased' style='margin-top:-5px;font-size: 10px;color:gray;'>Views: <?php echo $iViews; ?></label>
			 </div>
		</div>
	
	 <?php } ?>
</div>
<div style='clear:both'></div>

<?php if(($EDIT OR $_SESSION['super_admin']=='y') AND $items!=NULL){  ?>
<a href="<?php echo HTTP_PATH .'eCatalog/items/'.$ecID ?>"><p style="font-weight:bold;cursor:pointer;font-size:14px;margin:10px 0 0 10px;"> MANAGE eCATALOGUE ITEMS </p> </a>
<?php } ?>

			<div style="text-align:center;font-weight:normal;margin-left:-75px;">
			<?php if($last>0){ 
				$url_append = $url;
			?>	
				<ul class="pagination" style="margin-left:40px;">
					<a href="<?php echo HTTP_PATH ."gallery/redirect_link_ecat/$ecID/page/1/".$url_append ?>"><li class="page-btn firstnum" style="margin-right:3px;"> &laquo; FIRST </li></a> 
						<?php 
							//PAGNINATION
							$active_p = $active_page;
							if($active_page<=9){
								//PAGNINATION
								$page  = 1;
								$page2 = 1;
								$l 	   = 10;
								$ctr   = 1;
								
								while($l!=0)
								{
									//ACTIVE PAGE
									$style="style='margin-right:2px;'";
									if($active_page==$page++)
										$style="style='text-decoration:underline;margin-right:2px;'";
									
									if($page-1<=$last){
										
										$url1 = HTTP_PATH. "gallery/redirect_link_ecat/$ecID/page/".$ctr++."/".$url_append; 
										 
										echo  "<a href='$url1' $style><li>". $page2++ ."</li></a>";
									}
									$l--;
								}
								
								if($last>9)
									echo "<a><li>...</li></a>";
								
							}elseif($active_page>=10){
								
								/*LEFT SIDE*/
								$i=5;
								$page  = $active_page-5;
								$page1 = $active_page-5;
								$style="style='margin-right:2px;'";
								while($i!=0){
									$url2 = HTTP_PATH. "gallery/redirect_link_ecat/$ecID/page/".$page++."/".$url_append; 
									echo  "<a href='$url2' $style><li>". $page1++ ."</li></a>";
								 $i--;
								}
								
								/*ACTIVE PAGE*/
								$url3 = HTTP_PATH. "gallery/redirect_link_ecat/$ecID/page/".$active_page."/".$url_append; 
								echo  "<a href='$url3' style='text-decoration:underline;margin-right:2px;'><li>". $active_page ."</li></a>";
						
								/*RIGHT SIDE*/
								$i=5;
								$page  = $active_page+1;
								$page1 = $active_page+1;
								$style="style='margin-right:2px;'";
								while($i!=0){
								  if(($active_page++)<$last){
									 $url4 = HTTP_PATH. "gallery/redirect_link_ecat/$ecID/page/".$page++."/".$url_append; 
									 echo  "<a href='$url4' $style><li>". $page1++ ."</li></a>";
								  }
								 $i--;
								}
								
								if($active_page-5!=$last){
									echo "<a><li>...</li></a>";
								}
							}
					
							$url5 = HTTP_PATH. "gallery/redirect_link_ecat/$ecID/page/$last/".$url_append; 
						?>
					<a href='<?php echo $url5 ?>'><li class="page-btn">LAST &raquo;</li></a>
				</ul>
				<label style="color:#370909;font-weight:bold; margin-left:74px; padding-bottom:20px;">
					ITEMS: 
					<?php	
					if($total_rec<=15 OR $active_p==1){
						echo  "1-". count($items) . " of $total_rec";
					}else{
						$x=0;
						$x = (($active_p * 15)-15);
						echo $x + 1 ."-". ($x + count($items)) ." of $total_rec";
					}
					?>
				</label>
				
			<?php } ?>
		</div>
<?php 
	function w($img)
	{
		$w='';
		$HTTP_PATH = getcwd()."/img/galleryImg/$img";
		list($width, $height, $type, $attr) = getimagesize("$HTTP_PATH");
		if($width>$height)
			return $w='width:100%';
		else
			return $w;
	}
?>


<!--[if IE]>
<style>/* this style block is for IE */

.pagination { *padding-top: 10px!important;  *padding-bottom: 10px!important;  *padding-left:15px!important; *padding-right:15px!important;}
.firstnum { *margin-right:-15px!important; }


</style>
<![endif]-->