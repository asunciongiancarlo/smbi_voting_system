    <div class="content">
		
    	<div class="title-content fl">
        	<h2> VENDORS </h2>
        </div>
		
		<div style="float:right;margin: 16px;">
			<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
		</div>
        
        <div class="clear"></div>
	
        <div class="working_area">
		<div class="content_vendor">
		
        <div class="clear"></div>
	
		<br/>
		
		<?php 
		$CI =& get_instance();
		//MESSAGE ALERT
		if(isset($msg)){
			$CI->load->library('alert');
			echo $CI->alert->check($msg);
		}
		?>
		
		<?php if($ADD){ ?>
			<div class="sub-link">
				<ul>
					<li><a href="<?php echo HTTP_PATH ."itemDatabase/vendors/add" ?>"> <img src="<?php echo HTTP_PATH ?>img/plus.png" width="31" height="31"> </a></li>
					<li><a href="<?php echo HTTP_PATH ."itemDatabase/vendors/add" ?>"> <h5>Add<br/>Vendor</h5> </a></li>
				</ul>
			</div>	
		<?php
			}
			$i=1;
			$j=1;			
			foreach($data as $d){
			extract($d);
		?>
	
		<div class="drop-down" style="cursor:pointer;" onclick="showCompany('<?php echo "info_company".$i++ ?>')">
			<span class="ven"><h2><?php echo $company_name ?></h2></span>
			<span class="arrow"><img src="<?php echo HTTP_PATH ?>/img/arrow-down.jpg"  width="21" height="15" /></span>
			<div class="clear"></div>
		</div>
		
		<div class="info-company" style="display:none" id='info_company<?php echo $j++ ?>'>
			<table border="0" style="background:#f0f8ff">
				<tr>
					<td style="width: 470px;" align="left">
						<span class="info-right">
							<h2 align="left">COMPANY NAME </h2><br/><br/>
							<p align="left"> <?php echo $company_name ?> </p>
						</span>
					</td>
					<td style="width: 470px;" align="left">
						<span class="info-right">
							<h2 align="left">CONTACT PERSON</h2><br/><br/>
							<p align="left"> <?php echo $fname ." ". $mname ." ".  $lname  ?> </p>
						</span>
					</td>
				</tr>
				<tr>
					<td style="width: 470px;" align="left">
						<span class="info-right">
							<h2 align="left">BILLING ADDRESS </h2><br/><br/>
							<p align="left"> <?php echo $billing_address ?> </p>
						</span>
					</td>
					<td style="width: 470px;" align="left">
						<span class="info-right">
							<h2 align="left">TELEPHONE </h2><br/><br/>
							<p align="left"> <?php echo $telephone ?> </p>
						</span>
					</td>
				</tr>
				<tr>
					<td style="width: 470px;" align="left">
						<span class="info-right">
							<h2 align="left">EMAIL ADDRESS </h2><br/><br/>
							<p align="left"> <?php echo $telephone ?>  </p>
						</span>
					</td>
					<td style="width: 470px;" align="left">
						<span class="info-right">
							<h2 align="left">COUNTRY </h2><br/><br/>
							<p align="left"> <?php echo $countryName ?> </p>
						</span>
					</td>
					<td>
					</td>
					
					<?php if($ADD){ ?>
					<td>
						<div class="button-content4" style="cursor:pointer;">
							<a href=""><a href="<?php echo HTTP_PATH ."itemDatabase/vendors/duplicate/".$vID ?>"><h2>DUPLICATE</h2></a></a>
						</div>
					</td>
					<?php } ?>
				</tr>
				<tr>
					<td style="width: 470px;" align="left">
						<span class="info-right">
							<h2 align="left">POSTAL CODE </h2><br/><br/>
							<p align="left"> <?php echo $postal_code ?>  </p>
						</span>
					</td>
					<td style="width: 400px;" align="left">
						<span class="info-right">
							<h2 align="left">CITY CODE </h2><br/><br/>
							<p align="left"> <?php echo $city_state ?> </p>
						</span>
					</td>
					<?php if($EDIT){ ?>
					<td>
						<div class="button-content4" style="cursor:pointer;">
							<a href=""><a href="<?php echo HTTP_PATH ."itemDatabase/vendors/edit/".$vID ?>"><h2>EDIT</h2></a></a>
						</div>
					</td>
					<?php } if($DELETE){ ?>
					<td>
						<div class="button-content4" onclick="deleteOneItem('<?php echo $vID ?>')" style="cursor:pointer;">
							<h2>DELETE</h2>
						</div>
					</td>
					<?php } ?>
				</tr>
			</table>
			
		</div>
		
		<?php } ?>
		
    </div>
	
	<script>
	function showCompany(info_company)
	{
		var x = document.getElementById(info_company);
		
		if(x.style.display == "none")
		{
			x.style.display = "block";
		}else
		{
			x.style.display = "none";
		}
	}
	
	function deleteOneItem(id)
	{
		jConfirm("Are you sure you want to delete this Vendor?","Alert",function(r){
			if(r) window.location = "<?php echo HTTP_PATH ?>itemDatabase/vendors/deleteOneItem/"+ id;
		});
	}
		
		
	</script>
	
	</div>
        <div class="clear" style="height:20px;"></div>
    </div>
	
	<div style="text-align:center;margin-top:20px;">
	
	
	<?php if($last>0){ ?>
		<ul class="pagination">
				<a href="<?php echo HTTP_PATH."itemDatabase/vendors/page/1" ?>"><li class="page-btn" style="margin-right:2px;"> &laquo; FIRST </li></a> 
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
						$page_link = HTTP_PATH."itemDatabase/vendors/page/".$i++;
						if($active_page==$page++)
						{
							$style="style='text-decoration:underline'";
						}
						echo  " <a href='$page_link' $style><li>". $page2++ ."</li></a> ";
						$l--;
					}
				?>
				<a href="<?php echo HTTP_PATH."itemDatabase/vendors/page/".$last ?>"><li class="page-btn">LAST &raquo;</li></a>
		</ul>
	<?php } ?>
    </div>
	