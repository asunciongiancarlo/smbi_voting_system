<div class="content" style='padding-left:1px;'>
	<div class="title-content" style='width:100%;'>
		<h2 class="item-galla" style='float:left;'>ARCHIVED ITEMS</h2>
		
		<div style='float:right;margin-right:58px;'>
			<div class="fl search_tools items-searchtools">
				<div class="additem" >
					
				</div>
				<?php include('search_tools.php') ?>
			</div>
			<div style="float:right;margin: 10px -22px 20px 23px;" class="help">
				<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src="<?php echo HTTP_PATH ?>/img/help1.png"></a>
			</div>
		</div>
	</div>
	
	<div class="clear"></div>

	<div>

		<div  style="font-weight:bold; font-size:15px; padding-top:10px;" class="container">
			<?php 
				$CI =& get_instance();
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo "<br/>";
					echo "<div class='msgBar' style='width:100%'>". $CI->alert->check($msg) ."</div>";
				}
			?>
			<div class="row1">
			<form name="SMBi2" id="itemsForm" action="<?php echo HTTP_PATH ?>itemDatabase/tag_for_purging/items_for_archiving/purgeSelectedItems" method="POST" class="item-container" style="margin-left: 18px;"> 			
			<?php 		
				if(!empty($data) & $_SESSION['super_admin']!='y' &  ($RESTORE OR $DELETE)){
					echo "<label style='margin:-10px 0 0 10px;'> <input type='checkbox' onclick='checkedAll()' style='vertical-align:top;color:#555555'> SELECT ALL </label>";
			    if($RESTORE){
			?>
				<p style="font-weight:bold;cursor:pointer;font-size:12px;margin:10px 0 0 10px;width:194px;background:#370909;color:white;" onclick="restoreSelectedItem()" class="buttonWithRadius fl">  
					<img src="<?php echo HTTP_PATH ."img/restore.png"?>" style="margin-right:10px;">RESTORE SELECTED ITEMS 
				</p>
			<?php } if($DELETE){ ?>
				<p style="font-weight:bold;cursor:pointer;font-size:12px;margin:10px 0 0 10px;width:180px;background:#370909;color:white;" onclick="deleteSelectedItem()" class="buttonWithRadius fl">  
					<img src="<?php echo HTTP_PATH ."img/delete.png"?>" style="margin-right:10px;">PURGE SELECTED ITEMS 
				</p>
			<?php } 
				} ?>	
					
			<div style="clear:both;"></div>
			<?php	  
				echo $csrf;
					
					//NO SEARCH RESULTS
					if(empty($data)){
						include('no_search_result.php');
					}
					
					$CI =&get_instance();
					
					foreach($data as $i){	
					extract($i);
					$sql = $CI->db->query("SELECT image FROM items_images WHERE id = (SELECT ID FROM items_images WHERE itemID = $itemID AND defaultStatus=1 LIMIT 0,1) LIMIT 0,1");
					$item_img = $sql->result_array();
					extract($item_img);
					$item_img = isset($item_img[0]['image']) ? $item_img[0]['image'] : 'blank.png';
					$w = w($item_img);
					?>
					<div class="common-gal fl items-item" style="border:1px solid #999999;min-height:299px;overflow:hidden;">
						<?php $background = ($irrelevant=='y') ? "background:#575353;" : "background:#999999;";  
							  $title	  = ($irrelevant=='y') ? "title='Disapproved Item'" : '';
						?>
						<div class="common-gal-heading" style='text-align:center;height:25px;color:white;font-style:bold;margin-bottom:3px;<?php echo $background ?>' <?php echo $title ?>> 
							<b><?php echo $POSM_TypeName; ?></b>
							<?php if($publish!='y'){ echo "<br/><label style='font-size:11px;color:red;margin-top:-3px;'>*For Approval</label>"; } ?>
							<?php if($popular=='y'){ echo "<img title='Popular Item' src='".HTTP_PATH."img/set-popular.png' title='Popular Item' style='margin-left: 22px;position: absolute;'>"; } ?>
						</div>
						<div class="clear"></div>
							 
							 <div class="clear"  style='border-bottom:1px solid #eeeeee;height:160px;text-align:center;width:210px;overflow:hidden'>
								 <table>
								 <tr>
									<td class='gal-Icon-Container'>
										<a href="<?php echo HTTP_PATH.'gallery/itemInfo2/'.$itemID.'/i'?>">
											<img class='gal-Icon-Img img-items' src="<?php echo HTTP_PATH ?>img/galleryImg/<?php echo $item_img ?>" style='<?php echo $w ?>'>
										</a>
									</td>
								 </tr>	
								</table> 
							 </div>
						 
						 <div style='text-align:center;'>
							<hr style='margin:2px 0'>
							<a href="<?php echo HTTP_PATH.'gallery/itemInfo2/'.$itemID.'/i'?>">
								<h4 class='ptitle' style='margin-bottom:1px;font-size:14px;margin-top:5px;height:20px;' id='pt<?php echo $itemID ?>' alt='' title="<?php echo $itemName?>" style='font-size:15px;'>
									<?php 
									if(strlen($itemName)>=20)
										echo substr($itemName,0,20)."...";
									else	
										echo $itemName;
									?>
								</h4>
							</a>
							<label class='dateReleased' style='margin-top:-5px;font-size: 10px;color:gray;'>Views: <?php echo $iViews; ?></label>
						 </div>
						 <?php
						
						 echo "<label style='font-size:10px;text-align:center;margin-bottom:-15px;height:40px;'>" ;
						 if(campaign_items($itemID)!=TRUE){
								if($RESTORE & $_SESSION['super_admin']!='y')	
									echo "<a style='cursor:pointer' onclick='restoreOneItem($itemID)'>Restore</a> | ";
								if($DELETE & $_SESSION['super_admin']!='y')	
									echo "<a style='cursor:pointer' onclick='deleteOneItem($itemID)'>Purge</a> ";
						}else{
							echo "Campaign Item: Edit and Delete are not available.";
						}
						echo "</label>";
						
						 if($RESTORE & $_SESSION['super_admin']!='y' & campaign_items($itemID)!=TRUE){
							echo "<span style='text-align:left;margin:0 0 3px 3px;'>
									<input type='checkbox' name='selectedItems[]' value='$itemID' id='checkBoxVar' style='padding-bottom:3px;'> 
								 </span>";
						}
						?>
					</div>
				<?php }  ?>
			
			<div style='clear:both;'></div>	
			<?php 		
				if(!empty($data) & $_SESSION['super_admin']!='y' &  ($RESTORE OR $DELETE)){
			    if($RESTORE){
			?>
				<p style="font-weight:bold;cursor:pointer;font-size:12px;margin:10px 0 0 10px;width:194px;background:#370909;color:white;" onclick="restoreSelectedItem()" class="buttonWithRadius fl">  
					<img src="<?php echo HTTP_PATH ."img/restore.png"?>" style="margin-right:10px;">RESTORE SELECTED ITEMS 
				</p>
			<?php } if($DELETE){ ?>
				<p style="font-weight:bold;cursor:pointer;font-size:12px;margin:10px 0 0 10px;width:180px;background:#370909;color:white;" onclick="deleteSelectedItem()" class="buttonWithRadius fl">  
					<img src="<?php echo HTTP_PATH ."img/delete.png"?>" style="margin-right:10px;">PURGE SELECTED ITEMS 
				</p>
			<?php } 
				} ?>	
			<div style='clear:both;'></div>	
			</form>
			
			</div>
			<div style="text-align:center;font-weight:normal;margin-left:-75px;height: 17px;">
			<?php if($last>0){ 
				$url_append = $url;
			?>	
				<ul class="pagination" style="margin-left:40px;">
					<a href="<?php echo HTTP_PATH ."itemDatabase/redirect_link/$redirectTo/page/1/".$url_append ?>"><li class="page-btn firstnum" style="margin-right:3px;"> &laquo; FIRST </li></a> 
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
										
										$url1 = HTTP_PATH. "itemDatabase/redirect_link/$redirectTo/page/".$ctr++."/".$url_append; 
										 
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
									$url2 = HTTP_PATH. "itemDatabase/redirect_link/$redirectTo/page/".$page++."/".$url_append; 
									echo  "<a href='$url2' $style><li>". $page1++ ."</li></a>";
								 $i--;
								}
								
								/*ACTIVE PAGE*/
								$url3 = HTTP_PATH. "itemDatabase/redirect_link/$redirectTo/page/".$active_page."/".$url_append; 
								echo  "<a href='$url3' style='text-decoration:underline;margin-right:2px;'><li>". $active_page ."</li></a>";
						
								/*RIGHT SIDE*/
								$i=5;
								$page  = $active_page+1;
								$page1 = $active_page+1;
								$style="style='margin-right:2px;'";
								while($i!=0){
								  if(($active_page++)<$last){
									 $url4 = HTTP_PATH. "itemDatabase/redirect_link/$redirectTo/page/".$page++."/".$url_append; 
									 echo  "<a href='$url4' $style><li>". $page1++ ."</li></a>";
								  }
								 $i--;
								}
								
								if($active_page-5!=$last){
									echo "<a><li>...</li></a>";
								}
							}
					
							$url5 = HTTP_PATH. "itemDatabase/redirect_link/$redirectTo/page/$last/".$url_append; 
						?>
					<a href='<?php echo $url5 ?>'><li class="page-btn">LAST &raquo;</li></a>
				</ul>
				<label style="color:#370909;font-weight:bold; margin-left:74px; padding-bottom:20px;">
					ITEMS: 
					<?php	
					if($total_rec<=15 OR $active_p==1){
						echo  "1-". count($data) . " of $total_rec";
					}else{
						$x=0;
						$x = (($active_p * 15)-15);
						echo $x + 1 ."-". ($x + count($data)) ." of $total_rec";
					}
					?>
				</label>
				
			<?php } ?>
			</div>
			<?php
			 echo "<b style='font-size:12px;margin-left:15px;'>Archiving Rules:</b><ul class='featured_brands_ul' style='list-style: none;width:600px;'><li class='featured_brands_li'>$qualifications</li></ul>";
			?>

		</div>
	
	</div>
	   
	<div class="clear"></div>
</div>
<br/>

<?php
	function campaign_items($itemID='')
	{
		$CI =& get_instance();
		$sql = $CI->db->query("SELECT campaignItemsXref.itemID FROM campaignItemsXref 
				LEFT JOIN campaign ON campaign.id = campaignItemsXref.campaignID 
				WHERE campaign.status = 'on progress' AND campaignItemsXref.itemID = $itemID");
		$sql = $sql->result_array();
		if(count($sql)>0)
			return TRUE;
		else
			return FALSE;
	}
	
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
	
	<script type="text/javascript">
	   	function SetAsPopularItems()
		{
			jConfirm("Set these as popular items?","Alert",function(r){
				if(r){
					$("#itemsForm").attr('action','<?php echo HTTP_PATH ."itemDatabase/SetAsPopularItems" ?>');
					document.getElementById("itemsForm").submit();
				}
			});
		}
		
		function submitSearch()
		{
			document.getElementById('active_page').value = 1;
			document.forms.frmSearch.submit();
		}
		
		function active_page(page)
		{
			document.getElementById('active_page').value = page;
			document.forms.frmSearch.submit();
		}
	
		function deleteOneItem(id)
		{
			jConfirm("Are you sure you want to submit this item for purging?","Alert",function(r){
				if(r) window.location = "<?php echo HTTP_PATH ?>itemDatabase/tag_for_purging/items_for_archiving/purgeOneItem/"+ id;
			});
		}
		
		
		function viewfilter()
		{
			var x = document.getElementById('viewfilter').style.display;
			if(x == 'none')
				document.getElementById('viewfilter').style.display = 'block';
			else
				document.getElementById('viewfilter').style.display = 'none';

		}
	
		
		function deleteSelectedItem()
		{
			jConfirm("Are you sure you want to submit these Items for purging?","Alert",function(r){
				if(r) document.getElementById("itemsForm").submit();
			});
		}
		
		function restoreSelectedItem()
		{
			jConfirm("Are you sure you want to restore these items to Item Database?","Alert",function(r){
				if(r){ 
				document.getElementById("itemsForm").action = "<?php echo HTTP_PATH .'itemDatabase/restoreFromArchiving/restoreSelectedItem'?>"
				document.getElementById("itemsForm").submit();
				}
			});
		}
		
		function restoreOneItem(id)
		{
			jConfirm("Are you sure you want to restore this items?","Alert",function(r){
				if(r) window.location = "<?php echo HTTP_PATH ?>itemDatabase/restoreFromArchiving/restoreOneItem/"+ id;
			});
		}
		
		
		function submitbrands()
		{
			document.getElementById("statusFORM").submit();
		}
		
		
		checked=false;
		function checkedAll (frm1) {
			var aa= document.getElementById('itemsForm');
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