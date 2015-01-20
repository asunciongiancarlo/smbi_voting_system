    <div style='background-color:white;'>
        <div class="content_vendor">
			<div class="button-content2" style='margin:5px' id='tab1'>
				<a href="#" onclick="showItemDescription('item_description')"><h2>ITEM FORM</h2></a>
			</div>
			<div class="button-content1" style='margin:5px' id='tab2'>
				<a href="#" onclick="showItemVendors('item_vendors')"><h2>VENDORS</h2></a>
			</div>
			
			<div class="clear" style='height:10px;'></div>
			<div class="red"> </div>
			
			<?php
				$CI =& get_instance();
				$CI2 =& get_instance();
				$CI2->load->library('fv');
				
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
				}
		
				//ACTION STATEMENT
				$action = HTTP_PATH ."itemDatabase/items/insert";
				
				//ISSET ID
				$Long_Description='';
				$brandID='';
				$POSMTypeID='';
				$POSMStatusID='';
				$OUTLETStatusID=0;
				$PremiumTypeID='';
				$MaterialTypeID='';
				$countryID='';
				$itemName='';
				$Short_Description='';
				$UnitPrice='';
				$MOQ='';
				$UOM='';
				$dateAdded='';
				$Fields0001='';
				$Fields0002='';
				$Fields0003='';
				$Fields0004='';
				$Fields0005='';
				$DateLastEdited=''; 
				$selectedVendor='';
				$publish='';
				$publish_other_country='';
				$plant_inventory 		= '';
				$supplier_stock_on_hand = '';
				$date_first_issue 		= '';
				$date_last_used 		= '';
				$activity_event_use 	= '';
				
	
				if(isset($id))
				{
					$action = HTTP_PATH ."itemDatabase/items/update/".$id;
					$sql = $CI->db->query("SELECT * FROM items WHERE id= $id");
					$sql = $sql->result_array();
					
					//ITEM DESCRIPTION
					extract($sql);
			
					$Long_Description 	= 	$sql[0]['Long_Description']; 
					$brandID	 		= 	$sql[0]['brandID']; 
					$POSMTypeID 		= 	$sql[0]['POSMTypeID']; 
					$POSMStatusID		=  	$sql[0]['POSMStatusID'];
					$OUTLETStatusID		=  	$sql[0]['OUTLETStatusID'];;
					$PremiumTypeID		=  	$sql[0]['PremiumTypeID'];;
					$MaterialTypeID		=  	$sql[0]['MaterialTypeID'];;
					$countryID			=  	$sql[0]['countryID'];;
					$itemName			=  	$sql[0]['itemName'];;
					$Short_Description	=  	$sql[0]['Short_Description'];;
					$UnitPrice			=  	$sql[0]['UnitPrice'];;
					$MOQ				=  	$sql[0]['MOQ'];;
					$UOM				= 	$sql[0]['UOM'];;
					$dateAdded			= 	$sql[0]['dateAdded'];;
					$Fields0001			= 	$sql[0]['Fields0001'];;
					$Fields0002			=  	$sql[0]['Fields0002'];;
					$Fields0003			= 	$sql[0]['Fields0003'];;
					$Fields0004			= 	$sql[0]['Fields0004'];;
					$Fields0005			= 	$sql[0]['Fields0005'];
					$DateLastEdited		= 	$sql[0]['DateLastEdited'];
					$publish			=   $sql[0]['publish'];
					$publish_other_country	=   $sql[0]['publish_other_country'];
					
					$plant_inventory 		= $sql[0]['plant_inventory'];
					$supplier_stock_on_hand = $sql[0]['supplier_stock_on_hand'];
					$date_first_issue 		= $sql[0]['date_first_issue'];
					$date_last_used 		= $sql[0]['date_last_used'];
					$activity_event_use 	= $sql[0]['activity_event_use'];
					
					//ITEM VENDORS
					$sql = $CI->db->query("SELECT vendorID as selectedVendorID FROM itemVendorsRef WHERE itemID= $id");
					$selectedVendor = $sql->result_array();
					
				}
				
				//POSM SWITCH
				if(isset($POSM_statusID))
					$POSMStatusID = $POSM_statusID;
				
				//SET DUPLICATE INTO
				if(isset($duplicate))
					$action = HTTP_PATH ."itemDatabase/items/insert";
			 
			?>
			
			
			<div class="container" style='margin-left: -25px;'>
			<div id="item_description" style="display:block;">
			
			 <table style='margin:0 auto' border='0' width='100%'>
			   <tr>
			     <td valign='top' width='30%'> 
				 <?php 
					/*POSM STATUS SWITCHING */
						$sql = $CI->db->query("SELECT POSM_Status.id AS sID, statusName FROM POSM_Status");
						$sql = $sql->result_array();
						$switcherAction = HTTP_PATH."itemDatabase/items/add";
						if(isset($id))
							$switcherAction = HTTP_PATH."itemDatabase/items/edit/".$id;
						if(isset($duplicate))
							$switcherAction = HTTP_PATH."itemDatabase/items/duplicate/".$id;
						
						echo "<form name='switcher' method='POST' action='$switcherAction' style='text-align:left;'>
								$csrf
								<h2 class='form'> ". $CI2->fv->label(5) ." </h2>
								<div class=''>
									<select name='sID' onchange='this.form.submit()' style='width:410px;'>";
									  $chk="";
									  foreach($sql as $status)
									  { 
										extract($status);
										if($POSMStatusID == $sID) $chk = 'selected';
										echo "<option value='$sID' $chk> $statusName </option>";
										$chk = '';
									  }
						echo    	"</select>
								</div>
							</form> <hr>"; 
							
					/*POSM STATUS SWITCHING */
					
					
					
					$CI =& get_instance();
					$CI->load->library('forms');						
					echo $CI->forms->form_header('SMBi','vendorFORM',$action);
					
					echo "<div class='fl' style='width:500px'>";
						if(isset($id))
						{
							$itemID = $id;
							$date = $DateLastEdited;
							$DateLastEdited = ($DateLastEdited!='0000-00-00') ?  date('M j, Y', strtotime($date)) : $DateLastEdited='-';
							
							echo "<div style='clear:both;margin:5px'> </div>";
							echo "	  <p style='font-size:12px;text-align:left;margin-bottom:10px;'> 
										<b>Date Added:</b> ".  date('M j, Y', strtotime($dateAdded)) ."<br/>
										<b>Last Update:</b> $DateLastEdited <br/>
									 </p>";
							
							
							echo "<div id='imageBar'>";	
								echo "<div class='fl' style='text-align:center;width:442px;'>
										<div style='height:199px;'>"; 	
											$item_img = isset($items_images[0]['image']) ? $items_images[0]['image'] : 'blank.png';
											echo "<img id='bigThumb' src='". HTTP_PATH.'img/items/'.$item_img ."' style='height:150px;'>";
								echo   "</div>";
								echo "</div>";
								
								echo "<div id='thumbnails' class='fl'>"; 
									$i=0; $j=0;
								
									foreach($items_images as $im)
									{
										$thumb = '"thumb'.$j++.'"';
										extract($im);
										//IMAGE HIDDEN FIELD
										if(isset($duplicate))
											echo "<input type='hidden' name='images[]' value='$image'>";
											
										echo "<img src='".HTTP_PATH."img/items/$image' id='thumb".$i++."' onclick='enlargeThumbnail(".$thumb.")' width='100' class='fl' style='z-index:-1;margin-right:10px;margin-bottom:5px;'>";
									}
								echo "</div>";
							echo "</div>";
						}
						
	
						
					echo "</div>";		
				 	
					echo "<div class='fl' style='width:250px'>";
						//HIDDEN ELEMENT
						echo "<input type='hidden' name='POSMStatusID' value='$POSMStatusID'>";
						
						if($CI2->fv->fieldChecker($POSMStatusID,12)=='y')
							echo $CI->forms->form_fields2('text','itemName',$itemName,$CI2->fv->label(11),$CI2->fv->v(11),'disabled');
						
						if($CI2->fv->fieldChecker($POSMStatusID,13)=='y')
							echo $CI->forms->form_fields2('text','Short_Description',$Short_Description,$CI2->fv->label(12),$CI2->fv->v(12));
						
						if($CI2->fv->fieldChecker($POSMStatusID,5)=='y')
							echo $CI->forms->select('POSMTypeID','POSM_Type','typeName',$CI2->fv->label(4),$POSMTypeID,$CI2->fv->v(4));
						
						if($CI2->fv->fieldChecker($POSMStatusID,10)=='y')	
							echo $CI->forms->select('MaterialTypeID','MATERIAL_Type','materialName',$CI2->fv->label(9),$MaterialTypeID,$CI2->fv->v(9));	
						
						if($CI2->fv->fieldChecker($POSMStatusID,2)=='y')
							echo $CI->forms->form_fields2('textarea','Long_Description',$Long_Description,$CI2->fv->label(1),$CI2->fv->v(1));
						
						
						if($CI2->fv->fieldChecker($POSMStatusID,4)=='y')
							echo $CI->forms->select('brandID','brands','brandName',$CI2->fv->label(3),$brandID,$CI2->fv->v(3));
							
						
						if($CI2->fv->fieldChecker($POSMStatusID,6)=='y')	
							echo $CI->forms->select('OUTLETStatusID','OUTLET_Status','statusName',$CI2->fv->label(6),$OUTLETStatusID,$CI2->fv->v(6));
							
						if($CI2->fv->fieldChecker($POSMStatusID,7)=='y')
							echo $CI->forms->form_fields2('select_premium','PremiumTypeID',$PremiumTypeID,$CI2->fv->label(7),$CI2->fv->v(7));
						
						//PUBLISH	
						echo "<input type='hidden' name='publish' value='y' id='publishInput'>";
						
						if($CI2->fv->fieldChecker($POSMStatusID,9)=='y')
							echo $CI->forms->form_fields2('publish','publish_other_country',$publish_other_country,$CI2->fv->label(31),$CI2->fv->v(31));
						
						
						//COUNTRY		
						if($_SESSION['super_admin']=='y'){
							echo $CI->forms->select('countryID','country','countryName',$CI2->fv->label(10),$countryID,$CI2->fv->v(10));
						}
						else{
							$countryID = $_SESSION['countryID'];
							echo "<input type='hidden' name='countryID' value='$countryID'>";
							echo $CI->forms->select('countryID','country','countryName',$CI2->fv->label(10),$countryID,$CI2->fv->v(10),'disabled');
						}
						
					
						
						if($CI2->fv->fieldChecker($POSMStatusID,14)=='y')
							echo $CI->forms->form_fields2('text','UnitPrice',$UnitPrice,$CI2->fv->label(13),$CI2->fv->v(13));
						
						if($CI2->fv->fieldChecker($POSMStatusID,15)=='y')	
							echo $CI->forms->form_fields2('text','MOQ',$MOQ,$CI2->fv->label(14),$CI2->fv->v(14));
						
						if($CI2->fv->fieldChecker($POSMStatusID,16)=='y')
							echo $CI->forms->form_fields2('text','UOM',$UOM,$CI2->fv->label(15),$CI2->fv->v(15));
						
						if($CI2->fv->fieldChecker($POSMStatusID,17)=='y')
							echo $CI->forms->form_fields2('text','Fields0001',$Fields0001,$CI2->fv->label(16),$CI2->fv->v(16));
						
						if($CI2->fv->fieldChecker($POSMStatusID,18)=='y')
							echo $CI->forms->form_fields2('text','Fields0002',$Fields0002,$CI2->fv->label(17),$CI2->fv->v(17));
						
						if($CI2->fv->fieldChecker($POSMStatusID,19)=='y')
							echo $CI->forms->form_fields2('text','Fields0003',$Fields0003,$CI2->fv->label(18),$CI2->fv->v(18));
						
						if($CI2->fv->fieldChecker($POSMStatusID,20)=='y')
							echo $CI->forms->form_fields2('text','Fields0004',$Fields0004,$CI2->fv->label(19),$CI2->fv->v(19));
						
						if($CI2->fv->fieldChecker($POSMStatusID,21)=='y')
							echo $CI->forms->form_fields2('text','Fields0005',$Fields0005,$CI2->fv->label(20),$CI2->fv->v(20));
						
						//ADDITIONAL INFO
						if($CI2->fv->fieldChecker($POSMStatusID,22)=='y')
							echo $CI->forms->form_fields2('text','plant_inventory',$plant_inventory,$CI2->fv->label(54),$CI2->fv->v(54));
						
						if($CI2->fv->fieldChecker($POSMStatusID,23)=='y')
							echo $CI->forms->form_fields2('text','supplier_stock_on_hand',$supplier_stock_on_hand,$CI2->fv->label(55),$CI2->fv->v(55));
						
						if($CI2->fv->fieldChecker($POSMStatusID,24)=='y')
							echo $CI->forms->form_fields2('text','date_first_issue',$date_first_issue,$CI2->fv->label(56),$CI2->fv->v(56));
							
						if($CI2->fv->fieldChecker($POSMStatusID,25)=='y')
							echo $CI->forms->form_fields2('text','date_last_used',$date_last_used,$CI2->fv->label(57),$CI2->fv->v(57));	
							
						if($CI2->fv->fieldChecker($POSMStatusID,26)=='y')
							echo $CI->forms->form_fields2('text','activity_event_use',$activity_event_use,$CI2->fv->label(58),$CI2->fv->v(58));
						
					echo "</div>"; ?>
					</td>
			   </tr>
			 </table>
				
					
			</div>
			
			<div class="clear"></div>
			
				<div id="item_vendors" style="display:none;width:95%;margin-left: 25px;">
				
					<?php
						//VENDORS
						$i=1;
						$j=1;
						
						foreach($vendors as $d){
						$checked="";
						extract($d);
						
							//CHECK IF SELECTED
							if($selectedVendor!=NULL)
							{
								foreach($selectedVendor as $sV)
								{
									extract($sV);
									if($selectedVendorID==$vID)
										$checked = "checked";
								}
							}
					?>
				
					<div class="drop-down" style="cursor:pointer;">
						<span class="ven"> <h2><input type="checkbox" <?php echo $checked ?> name="multipleVendors[]" value="<?php echo $vID ?>" style="vertical-align:text-top;margin-right:10px;">  <?php echo $company_name ?></h2></span>
						<span class="arrow" onclick="showCompany('<?php echo "info_company".$i++ ?>')"><img src="<?php echo HTTP_PATH ?>/img/arrow-down.jpg"  width="21" height="15" /></span>
						<div class="clear"></div>
					</div>
					
					<div class="info-company" style="display:none" id='info_company<?php echo $j++ ?>'>
						<table border="0">
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
								</td>							</tr>
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
							</tr>
						</table>
					</div>
				
				<?php } ?>
				
				</div>
				
			</div>
			
        </div>
		
        <div class="clear"></div>
    </div>
	
	<?php 
		echo "</form>";
	?>
	
	<script>
	
	function StopPar()
	{
		document.getElementById('publishInput').value = 'n';
		$('#vendorFORM').parsley().destroy();
	}
	
	
	
	function enlargeThumbnail(id)
	{
		var thumb = document.getElementById(id);
		var bigThumb = document.getElementById('bigThumb');
		
		bigThumb.src = thumb.src;
	
	}
	
	function deleteOneImg(id,itemID)
	{
		if(confirm("Are you sure you want to delete this thumbnail?"))
		{
			ajax('<?php echo HTTP_PATH ?>itemDatabase/deleteOneImg/'+id+'/'+itemID,'imageBar')
		}
	}
	
	
	function showItemDescription(id)
	{
		var x = document.getElementById(id);
		var y = document.getElementById('item_vendors');
		
		document.getElementById('tab1').className = 'button-content2';
		document.getElementById('tab2').className = 'button-content1';
		
		if(x.style.display == "none")
		{
			x.style.display="block";
			y.style.display="none";
		}
	}
	
	function showItemVendors(id)
	{
		var x = document.getElementById(id);
		var y = document.getElementById('item_description');
        
		document.getElementById('tab2').className = 'button-content2';
		document.getElementById('tab1').className = 'button-content1';
		
		
		if(x.style.display == "none")
		{
			x.style.display="block";
			y.style.display="none";
			
		}
		
	}
	
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

	</script>
	
	
	
	
	