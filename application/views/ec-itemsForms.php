<?php 
	$CI =& get_instance();
	$CI->load->library('forms');
	$CI2 =& get_instance();
	$CI2->load->library('fv');
	$CI->load->database('default');
?>	
	<div  style='background-color:white;'>
        <div class="form">
		  <div class='fl'>
			<?php if($EDIT){ ?>
			<div class="button-content2" style='margin:5px;cursor:pointer;' id='tab1'>
				<h2 onclick="showItemDescription('item_description')">EC-ITEM FORM</h2>
			</div>
			<?php } if($VENDORS_EDIT){  ?>
			<div class="button-content1" style='margin:5px;cursor:pointer;' id='tab2'>
				<h2 onclick="showItemVendors('item_vendors')">VENDORS</h2>	
			</div>
			<?php } ?>
		  </div>	
		  
		  <div style="float:right;margin: 16px;">
			<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help2.png'></a>
		  </div>
			
			<div class="clear" style='height:10px;'></div>
			<div class="red"> </div>
			
			<?php
				
				//MESSAGE ALERT
				if(isset($msg)){
					$CI->load->library('alert');
					echo "<div class='msgBar' style='width:60%'>". $CI->alert->check($msg) ."</div>";
				}
		
				//ACTION STATEMENT
				$action = HTTP_PATH ."eCatalog/items/insert";
				//ISSET ID
				$imgTot=0;
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
				$USD_Price='';
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
				$country_of_origin 		= '';
				$eCatalogID				= 0;
				$estimated_production_lead_time		= '';
				$price_validity		= '';
			
				if(isset($id))
				{
					$itemID = $id; 
					$action = HTTP_PATH ."eCatalog/items/update/".$id;
					$sql = $CI->db->query("SELECT * FROM ec_items WHERE id= $id");
					$sql = $sql->result_array();
					
					//ITEM DESCRIPTION
					extract($sql);
					
					$itemCode 				= 	$sql[0]['itemCode']; 
					$Long_Description 		= 	$sql[0]['Long_Description']; 
					$brandID	 			= 	$sql[0]['brandID']; 
					$POSMTypeID 			= 	$sql[0]['POSMTypeID']; 
					$POSMStatusID			=  	$sql[0]['POSMStatusID'];
					$OUTLETStatusID			=  	$sql[0]['OUTLETStatusID'];;
					$PremiumTypeID			=  	$sql[0]['PremiumTypeID'];;
					$MaterialTypeID			=  	$sql[0]['MaterialTypeID'];;
					$countryID				=  	$sql[0]['countryID'];;
					$itemName				=  	$sql[0]['itemName'];;
					$Short_Description	    =  	$sql[0]['Short_Description'];;
					$UnitPrice			    =  	$sql[0]['UnitPrice'];;
					$USD_Price			    =  	$sql[0]['USD_Price'];;
					$MOQ				    =  	$sql[0]['MOQ'];;
					$UOM				    = 	$sql[0]['UOM'];;
					$country_of_origin 		=   $sql[0]['country_of_origin'];
					$dateAdded			    = 	$sql[0]['dateAdded'];;
					$Fields0001			    = 	$sql[0]['Fields0001'];;
					$Fields0002			    =  	$sql[0]['Fields0002'];;
					$Fields0003			    = 	$sql[0]['Fields0003'];;
					$Fields0004			    = 	$sql[0]['Fields0004'];;
					$Fields0005			    = 	$sql[0]['Fields0005'];
					$estimated_production_lead_time = $sql[0]['estimated_production_lead_time'];
					$price_validity 		= 	$sql[0]['price_validity'];
					$DateLastEdited		    = 	$sql[0]['DateLastEdited'];
					$publish			    =   $sql[0]['publish'];
					$publish_other_country	=   $sql[0]['publish_other_country'];
					$eCatalogID				=   $sql[0]['ecID'];
					
					$plant_inventory 		= $sql[0]['plant_inventory'];
					$supplier_stock_on_hand = $sql[0]['supplier_stock_on_hand'];
					$date_first_issue 		= $sql[0]['date_first_issue'];
					$date_last_used 		= $sql[0]['date_last_used'];
					$activity_event_use 	= $sql[0]['activity_event_use'];
					
					$dateAdded 				= $sql[0]['dateAdded'];
					$dateReleased 			= $sql[0]['dateReleased'];
					$user_id 				= $sql[0]['user_id'];
					
					//ITEM VENDORS
					$sql = $CI->db->query("SELECT vendorID as selectedVendorID FROM ecitemVendorsRef WHERE itemID= $id");
					$selectedVendor = $sql->result_array();
					
					//UPLOADED BY
					$sql 	  = $CI->db->query("SELECT full_name FROM admin_users WHERE id = $user_id LIMIT 0,1");
					$uploadBy = $sql->row();
				}
				
				//POSM SWITCH
				if(isset($POSM_statusID))
					$POSMStatusID = $POSM_statusID;
				
				//SET DUPLICATE INTO
				if(isset($duplicate))
					$action = HTTP_PATH ."eCatalog/items/insert";
			 
			?>
			
			
			<div class="container" style="margin-top:25px;">
			
			<!-- ITEM FORM -->
			<?php if($EDIT){ ?>
			<div id="item_description" style="display:block;">
			<div class="container">
			 <div style="margin:0 20px;">
              
			  <?php echo $CI->forms->form_header('SMBi','itemFORM',$action);?>
              <table width="100%" cellpadding="0" cellspacing="0" border="0" >
                <tr>
                <td rowspan="7" width="50%" valign='top'>
                  <center>
					<?php 
					if(isset($id))
					{
						//ITEM CODE
						if($dateReleased=='0000-00-00') $dateReleased = 'Not yet release';
						echo "<label class='itemCode'>
								<strong>PRODUCT CODE: </strong> $itemCode <br/> 
								<strong>DATE:  		  </strong>(RELEASED: $dateReleased /UPLOADED: $dateAdded)<br/> 
								<strong>UPLOADED BY:  </strong> ". $uploadBy->full_name ."<br/> 
							</label>";
					
						
						$itemID = $id;
						$itemID1 = $id;

						$item_img = isset($items_images[0]['image']) ? $items_images[0]['image'] : 'blank.png';						
						echo" <div style='margin:5px'> 
						         <a href='JavaScript:imgs(-1)'><img style='float:left'   src='".HTTP_PATH."img/left.png'></a> 
							     <a href='JavaScript:zoom($itemID)'><img style='margin:0 auto' src='".HTTP_PATH."img/zoom.png'></a> 
                                 <a style='' href='JavaScript:imgs(1)'><img style='float:right' src='".HTTP_PATH."img/right.png'></a>
							  </div> <hr/>";
						
						 echo "<div class='targetarea' style='position:relative;width: 100%;min-height:300px;overflow:hidden'>
								 <a href='#'><img style=''  id='zoom' data-zoom-image='".HTTP_PATH."img/big/$item_img' src='".HTTP_PATH."img/small/$item_img'></a>
					 		   </div>";
							  
						echo "<hr style='width:450px;clear:both'>";
						
						
						echo "<div class='multizoom1 thumbs' id='imageBar' style='width:450px;'>";

								$i=0; $j=0;
								echo "<label style='margin-top:15px' class='fl' onclick='adjustLeft()'> <img src='".HTTP_PATH."img/left.png' '> &nbsp;</label>";
								echo "<div class='fl' style='width:380px;margin-top:-10px;max-height:80px;height:80px;overflow:hidden;border:1px solid rgb(196, 196, 196);position:relative;'>";
								
								
								echo "<div id='imageBox'>";
									echo "<ul id='imageSlider'  style='position:absolute;list-style:none;left:0'>";
										$imgTot = count($items_images);
										
											$border_color = ''; $imgIDs=array();
											foreach($items_images as $i=>$im)
											{
												extract($im);
												$imgIDS[] = $id;
												//SET GREEN BORDER IF DEFAULT
												if($defaultStatus == 1) $primaryIcon = 'check_icon.png';
												else $primaryIcon = 'check_icon_black.png';
												
												//IMAGE HIDDEN FIELD
												$imgT =  HTTP_PATH."img/thumb/$image";
												$imgS =  HTTP_PATH."img/small/$image";
												$imgB =  HTTP_PATH."img/big/$image";
												if(isset($duplicate))
												echo "<input type='hidden' name='images[]' value='$image'>";
											   
												  echo "<li class='fl' >
															<div id='tf$id' style='margin:5px;margin-top: -2px;'>
																<img   title='delete image' src='".HTTP_PATH."img/Delete.png' width='14' height='14' style='margin:5px;z-index:1;'  onclick='deleteOneImg($id,$itemID)'>
																<br>
																<a href='#' onclick='chImg($i)'   id='imgs$id'  data-image='$imgS'  data-zoom-image='$imgB'><img   src='$imgT' alt='' style='border:3px solid gray;height:35px'/></a> <br>
																<img title='Make Primary Image' src='".HTTP_PATH."img/$primaryIcon' width='14' height='14' style='margin:5px;z-index:1;'  onclick='setDefaultImg($id,$itemID)'>
															</div>
														</li>";
												$border_color='';
											}
										echo "</ul>";
									echo "</div>";
							echo "</div>";
							echo "<label class='fl' style='margin-top:15px' onclick='adjustRight()'> &nbsp; <img src='".HTTP_PATH."img/right.png' '> </label>";
						echo "</div>";
						
					}else {
						echo "<span style='border: 1px solid #cccccc; background: #eee url(".HTTP_PATH."img/small/blank.png) center center no-repeat; height:350px;width:98%;display: block;' ></span>";
					}
					
					?>
                  </center>
                </td>
                <td>
                
                </td>
                </tr>
                        <tr>
                            <td width="50%">
                                <?php 
								echo $CI->forms->form_header('SMBi','itemFORM',$action);
								//HIDDEN ELEMENT
								echo "<input type='hidden' name='POSMStatusID' value='$POSMStatusID'>";
								
								?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php 
									if($CI2->fv->ecItemField_Checker(12)=='y')
										echo $CI->forms->form_fields2('text','itemName',$itemName,$CI2->fv->label(67),$CI2->fv->v(11));
								?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php 
									if($CI2->fv->ecItemField_Checker(13)=='y')
										echo $CI->forms->form_fields2('textarea','Short_Description',$Short_Description,$CI2->fv->label(50),$CI2->fv->v(12));
								?>
                            </td>
                        </tr>                    
            
						<tr>
                            <td>
								<?php 
									if($CI2->fv->ecItemField_Checker(5)=='y'){
									/*POSM TYPE */
								    $sql = $CI->db->query("SELECT POSM_Type.id AS pID, typeName FROM POSM_Type");
								    $sql = $sql->result_array();
										echo "<h2> ". $CI2->fv->label(62) ." </h2>
												<select name='POSMTypeID' id='pID' onchange='switchType(this)' data-parsley-trigger='change' data-parsley-required='true'>";
												$chk="";
												echo "<option value=''> [SELECT] </option>";
												foreach($sql as $status)
												{ 
													extract($status);
													if($POSMTypeID == $pID) $chk = "selected";
													echo "<option value='$pID' $chk> $typeName </option>";
													$chk="";
												}
										echo    "</select>"; 
									/*POSM TYPE */
									}
								?>
                            </td>
                        </tr>                    
                        <tr>
                            <td>
								<div id='POSM_LINK'>
									<?php
									if($POSMTypeID){
										$sql = "SELECT table_fieldsID FROM ecitemType_POSM_table_fields WHERE POSM_TypeID = $POSMTypeID LIMIT 0,1";
										$row = $CI->db->query($sql);
										$row = $row->row();
										
										switch($row->table_fieldsID){
											case 5:
												echo $CI->forms->select('OUTLETStatusID','OUTLET_Status','statusName',$CI2->fv->label(63),$OUTLETStatusID,$CI2->fv->v(6));
											break;
											case 7:
												echo $CI->forms->form_fields2('select_premium','PremiumTypeID',$PremiumTypeID,$CI2->fv->label(64),$CI2->fv->v(7));
											break;
										}
									}
									?>
								</div>
                            </td>
						</tr>
						<tr>
                            <td>
                            	<?php
									//PUBLISH	
									echo "<input type='hidden' name='publish' value='y' id='publishInput'>";
									
									if($CI2->fv->ecItemField_Checker(10)=='y')	
										echo $CI->forms->select('MaterialTypeID','MATERIAL_Type','materialName',$CI2->fv->label(66),$MaterialTypeID,$CI2->fv->v(9));	
								?>
                            </td>
                        </tr> 
						<tr>
                            <td>
                            	<?php 
									if($CI2->fv->ecItemField_Checker(3)=='y')
									$v = '';
									$v = isset($id) ? 'o' : $CI2->fv->v(2);
									echo $CI->forms->form_fields2('multiple_files','files[]','',$CI2->fv->label(60),$v);	
								?>
                            </td>
							<td>
                            	<?php 
								    //e_Catalog Unique Title BRAND
									if($CI2->fv->ecItemField_Checker(4)=='y')
										echo $CI->forms->select('ecID','e_catalog','title',$CI2->fv->label(61),$eCatalogID,$CI2->fv->v(3));
								?>
                            </td>
						</tr>
                        
                        <tr>
							<td style="vertical-align:top">
                            	<?php
								$f1 = $CI2->fv->ecItemField_Checker(17);
								$f2 = $CI2->fv->ecItemField_Checker(18);
								$f3 = $CI2->fv->ecItemField_Checker(19);
								$f4 = $CI2->fv->ecItemField_Checker(20);
								$f5 = $CI2->fv->ecItemField_Checker(21);
								
						
								if($f1=='y' OR  $f2=='y' OR $f3=='y' OR $f4=='y' OR $f5=='y')
								{
								echo "<h2>EXTRA FIELDS</h2>";
								echo "<div class='extraFieldDiv'>";
								echo "<table class='extraFieldTable'>
									  <tr>
											<td>";
											if($CI2->fv->ecItemField_Checker(17)=='y')
												echo $CI->forms->form_fields2('text','Fields0001',$Fields0001,$CI2->fv->label(72),$CI2->fv->v(16));
									  echo "</td>
											<td>";
											if($CI2->fv->ecItemField_Checker(18)=='y')
												echo $CI->forms->form_fields2('text','Fields0002',$Fields0002,$CI2->fv->label(73),$CI2->fv->v(17));
									   echo "</td>
										 </tr>
										 <tr>
											<td>";	
											if($CI2->fv->ecItemField_Checker(19)=='y')
												echo $CI->forms->form_fields2('text','Fields0003',$Fields0003,$CI2->fv->label(74),$CI2->fv->v(18));
									  echo "</td>
											<td>";	
											if($CI2->fv->ecItemField_Checker(20)=='y')
												echo $CI->forms->form_fields2('text','Fields0004',$Fields0004,$CI2->fv->label(75),$CI2->fv->v(19));
									  echo "</td>
											</tr>
											<tr>
											<td>";	
										if($CI2->fv->ecItemField_Checker(21)=='y')
											echo $CI->forms->form_fields2('text','Fields0005',$Fields0005,$CI2->fv->label(76),$CI2->fv->v(20));
									 echo "</td>";
								echo "</tr>";
								echo "</table>
								</div>";
								}
					
								?>
                            </td>
                            <td></td>
                        </tr>                                        
                    </table>
				</div>
				
				
			
			<?php
			if(isset($id)){
			$date = $DateLastEdited;
			$DateLastEdited = ($DateLastEdited!='0000-00-00') ?  date('M j, Y', strtotime($date)) : $DateLastEdited='-';
			
			echo "<div style='clear:both;margin:5px'> </div>";
			echo "	  <p style='font-size:12px;text-align:left;margin-bottom:10px;margin-left:30px;'> 
						<b>Date Added:</b> ".  date('M j, Y', strtotime($dateAdded)) ."<br/>
						<b>Last Update:</b> $DateLastEdited <br/>
					 </p><br/>";
			}
			?>
			
			</div>
			</div>
			<?php } ?>
			<!-- ITEM FORM -->
		
		
			<!-- VENDOR FORM -->
			<?php if($VENDORS_EDIT){ 	
					if(!$EDIT & $VENDORS_EDIT){
					$switcherAction = HTTP_PATH."eCatalog/items/edit/".$itemID;
					echo $CI->forms->form_header('SMBi','itemFORM',$action);
					//PUBLISH	
					echo "<input type='hidden' name='publish' value='y' id='publishInput'>";
					echo "<strong style='margin-left: 35px;'> ITEM NAME: $itemName</strong>";
					}
			?>	
			<div class="clear"></div>
				<div id="item_vendors" style="display:none;margin: 10px 20px;}">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" >
						<tr>
							<td width='50%'>
                            	<?php 
									
									
									if($CI2->fv->ecItemField_Checker(14)=='y')
										echo $CI->forms->form_fields2('text','UnitPrice',$UnitPrice,$CI2->fv->label(69),$CI2->fv->v(13));
								?>
                            </td>
                            <td width='50%'>
                            	<?php
									if($CI2->fv->ecItemField_Checker(15)=='y')	
										echo $CI->forms->form_fields2('text','MOQ',$MOQ,$CI2->fv->label(70),$CI2->fv->v(14));
								?>
                            </td>
                        </tr>                    
						<tr>
                            <td>
                            	<?php
									//USD
									if($CI2->fv->fieldChecker($POSMStatusID,28)=='y')
										echo $CI->forms->form_fields2('text','USD_Price',$USD_Price,$CI2->fv->label(82),$CI2->fv->v(82));
								?>
                            </td>
							<td>
                            	<?php 	
									//lead time
									if($CI2->fv->fieldChecker($POSMStatusID,29)=='y')
										echo $CI->forms->form_fields2('text','estimated_production_lead_time',$estimated_production_lead_time,$CI2->fv->label(83),$CI2->fv->v(83));	
								?>
                            </td>
                        </tr> 
                        <tr>
                            <td>
                            	<?php 
									if($CI2->fv->ecItemField_Checker(16)=='y')
										echo $CI->forms->form_fields2('text','UOM',$UOM,$CI2->fv->label(71),$CI2->fv->v(15));
								?>
                            </td>
							<td>
                            	<?php if($CI2->fv->ecItemField_Checker(27)=='y')
										echo $CI->forms->select('country_of_origin','countries','country_name',$CI2->fv->label(78),$country_of_origin,$CI2->fv->v(78));
								?>
                            </td>
                        </tr>
						<tr>
							<td  style="vertical-align:top">
								<?php if($CI2->fv->ecItemField_Checker(2)=='y')
									echo $CI->forms->form_fields2('textarea','Long_Description',$Long_Description,$CI2->fv->label(1),$CI2->fv->v(1));
								?>
							</td>	
							<td  style="vertical-align:top">
								<?php 
									//PRICE VALIDITY
									if($CI2->fv->fieldChecker($POSMStatusID,30)=='y')
										echo $CI->forms->form_fields2('text','price_validity',$price_validity,$CI2->fv->label(84),$CI2->fv->v(84));
								?>
							</td> 	
						</tr>	
					</table>
					<div style="height:20px;"></div>
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
					
					<div class="drop-down">
							<h2 style='width:10px;'>
								<input type="checkbox" <?php echo $checked ?> name="multipleVendors[]" value="<?php echo $vID ?>" style="vertical-align:middle;margin-right:10px;margin-top:-10px;"> 
								<b><label style="margin-left: 40px;color:white;float:left;margin-top:-34px;width:600px;"><?php echo $company_name ?> </label></b>
							</h2>
							<img onclick="showCompany('<?php echo "info_company".$i++ ?>')" src="<?php echo HTTP_PATH ?>/img/arrow-down.jpg"  width="21" height="15" style="margin-top:-30px;cursor:pointer;float:right;" />
					</div>
					
					<div class="info-company" style="display:none;font-size:12px; background-color: rgb(240, 248, 255);" id='info_company<?php echo $j++ ?>'>
						<table border="0" style="margin-left:30px;">
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
										<h2 align="left">POSTAL CODE </h2><br/><br/>
										<p align="left"> <?php echo $postal_code ?>  </p>
									</span>
								</td>
							</tr>
							<tr>
								<td style="width: 470px;" align="left">
									<span class="info-right">
										<h2 align="left">CITY CODE </h2><br/><br/>
										<p align="left"> <?php echo $city_state ?> </p>
									</span>
								</td>
								<td style="width: 400px;" align="left">
									
								</td>
							</tr>
						</table>
					</div>
					<div style="clear:both;margin-bottom:1px;"></div>
				<?php } ?>
				
				</div>
			<?php } ?>	
			</div>
			
        </div>
		
        <div class="clear"></div>
		<br/>
    </div>

	<?php if(isset($imgIDS)){ ?>
	 <div class="popupBox" style='opacity:1;' id="dialog" >
	 <div class='close2' style='color:white;font-size:20px;margin-right:50px;float:left;padding:0;cursor:pointer'> 
		<img src="<?php echo HTTP_PATH ."img/close2.png"?>" style="width:90px;"> 
	 </div>
	 
	 <div style='clear:both'></div>
	 <iframe  src='' style='height:800px; border:0; width:90%; margin:0 auto' id='popUpFrame'> </iframe>
	 </div>	
	 <div id="mask2"> </div>
	 <?php } ?>
	
	<?php 
		echo $CI->forms->buttons('GoPar','itemFORM');
		echo "</form>";
	?>
	
	<script>
	<?php if(isset($imgIDS)){ ?>
	 
	 function zoom(id)
	 { 
		 var img  =  $('#zoom').attr('src');
		 img  =img.substring(img.lastIndexOf('/')+1,img.length);
		
		 //document.getElementById('popUpFrame').src = "<?php echo HTTP_PATH ?>gallery/itemzoom2/" +id +'/'+img;
		 
		  var formData = {itemID:id,imgSrc:img};
		 $.ajax({
		  url : "<?php echo HTTP_PATH ?>gallery/itemzoom2/",
		  type: "POST",
		  data : formData,
		  success: function(data, textStatus, jqXHR){
			document.getElementById('popUpFrame').src = "<?php echo HTTP_PATH ?>gallery/itemzoom2/";
		  },error: function(jqXHR, textStatus, errorThrown)
		  {
            alert("failed");
		  }});
		 
		 var maskHeight = $(document).height();
		 var maskWidth = $(window).width();
		 $('#mask2').css({'width':maskWidth,'height':maskHeight});	
		 $('#mask2').fadeIn(1500);	
		 $('#mask2').fadeTo("slow",0.8);
		 var winH = $(window).height();
		 var winW = $(window).width();
		 $('#dialog').css('top',50);
		/* $('#dialog').css('left', winW/2-$('#dialog').width()/2);*/
		 $('#dialog').fadeIn(3000);
         $('#dialog').css('opacity',1);  		 
	  }

  
	$('.close2').click(function (e) {
		e.preventDefault();
		$('#mask2').hide();
		$('#dialog').hide();
	});	
	
	//if mask is clicked
	$('#mask2').click(function () {
		$(this).hide();
		$('#dialog').hide();
	});	
	<?php } ?>
	</script>
	
	<?php if(isset($imgIDS)){ ?>
	<script>
	var adjusVal = 0;
	var curImgIndex = 0;
	var TotIMG = <?php echo count($imgIDS) ?>;
	
	var imgIDS = new Array(<?php   foreach($imgIDS as $k=> $id) { if($k<count($imgIDS)-1) echo "$id,"; else echo "$id" ; }?>);
	 
	var SliderWidth = $('#imageSlider li').length ;
	SliderWidth = SliderWidth * 60;
	var cl = 0  ;
	var imgSmallWidht = $('#zoom').css('width');
	var imgSmallheight = $('#zoom').css('height');
	 if(imgSmallheight > imgSmallWidht) $('#zoom').css('height',500);
	 if(imgSmallheight < imgSmallWidht) $('#zoom').css('width',450);
	function imgs(index)
	  {
	    if((index+curImgIndex) < TotIMG && (index+curImgIndex) >=0)
		  {  
		    if((curImgIndex +1) % 5 == 0 && index==1 )
			 { cl = cl-300;$('#imageSlider').animate({left:cl},500);}
			if((curImgIndex +1) % 5 == 0 && index==-1  )
			 {cl = cl+300;$('#imageSlider').animate({left:cl},500);}
			 
			$('#imgs'+imgIDS[curImgIndex] + ' img').css('border','3px solid gray');
			curImgIndex = index+curImgIndex;
		    $('#imgs'+imgIDS[curImgIndex] + ' img').css('border','3px solid red');
		    $('#imgs'+imgIDS[curImgIndex]).trigger('click');
			var lpos = $('#imageSlider').offset();
		    var lpos = lpos.left;
		   //alert(imgIDS[curImgIndex]);
		  }
	  }
	  
	</script>  
	<?php } ?>
	
	<script>  
	  
	  function switchType(sel)
	  {
		var POSMTypeID = sel.options[sel.selectedIndex].value;
		
		//IF VOTED
		var a = $.ajax({
			url: '<?php echo HTTP_PATH ?>generate_field/ecPOSMType_FIELD/'+POSMTypeID,
			async: false
		}).responseText;
		
		document.getElementById('POSM_LINK').innerHTML = a;
	  }
	  
	 function chImg(i)
	  {
	   $('#imgs'+imgIDS[curImgIndex] + ' img').css('border','3px solid gray');
	   curImgIndex = i;
	   $('#imgs'+imgIDS[curImgIndex] + ' img').css('border','3px solid red');
	  }
	  
	function adjustLeft()
	{	
		var ctr=0;
		var lpos = $('#imageSlider').offset();
		var lpos = lpos.left;
		//alert(SliderWidth); 
		if(cl-300>(SliderWidth*-1)) {cl = cl-300; $('#imageSlider').animate({left:cl},500); }
		
	}
	
	function adjustRight()
	{	
		var lpos = $('#imageSlider').offset();
		var lpos = lpos.left;
		if(cl+300<=0) {cl = cl+300; $('#imageSlider').animate({left:cl},500); }
	
	}
	
	
	
	function StopPar()
	{
		document.getElementById('publishInput').value = 'n';
		$('#itemFORM').parsley().destroy();
	}
	
	
	function enlargeThumbnail(img)
	{
	
		document.getElementById('smallThum').src = "<?php echo HTTP_PATH?>img/small/" + img ;
	
	}
	
	function deleteOneImg(id,itemID)
	{
		jConfirm("Delete this picture?\n Form will reload and any unsave information will be lost.","Alert",function(r){
			if(r){
				var xmlhttp2;
				var file = '<?php echo HTTP_PATH ?>eCatalog/deleteOneImg/'+id+'/'+itemID;
				if (window.XMLHttpRequest)
				  {// code for IE7+, Firefox, Chrome, Opera, Safari
				  xmlhttp2=new XMLHttpRequest();
				  }
				else
				  {// code for IE6, IE5
				  xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
				  }
				xmlhttp2.onreadystatechange=function()
				  {
				  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
					{
				 
						if(xmlhttp2.responseText==true)
							location.reload();
					}
				  }
				xmlhttp2.open("GET",file,true);
				xmlhttp2.send();
			}
		});
	}
	
	function setDefaultImg(id,itemID)
	{
		jConfirm("Set as primary picture?\n Form will reload and any unsave information will be lost.","Alert",function(r){
				if(r){
					var xmlhttp2;
					var file = '<?php echo HTTP_PATH ?>eCatalog/setDefaultImg/'+id+'/'+itemID;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp2=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp2.onreadystatechange=function()
					  {
					  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
						{
					 
							if(xmlhttp2.responseText==true)
								location.reload();
						}
					  }
					xmlhttp2.open("GET",file,true);
					xmlhttp2.send();
				}
			}
		);
		
	}
	
	
	<?php if($EDIT & $VENDORS_EDIT){?>
	
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
	<?php }elseif(!$EDIT & $VENDORS_EDIT){ ?>
		
	
	document.getElementById('item_vendors').style.display = "block";
	document.getElementById('tab2').className = "button-content2";

	
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
	<?php } ?>
</script>
<script src="<?php echo HTTP_PATH?>js/jquery.elevateZoom-3.0.8.min.js"></script>
<script>
    $("#zoom").elevateZoom({ensSize:30,gallery:'imageSlider', cursor: 'pointer', galleryActiveClass: 'active', loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif'}); 
</script>	

<style>
#mask2 {
	position: absolute;
	left: 0;
	top: 0;
	/*z-index: 90000;*/
	background-color:black;
	display: none;
}

.popupBox {
	
	min-height: 900px;
	position: absolute;
	width:90%;
	left: 9%;
	top: 0;
	display: none;
	z-index: 99999;
	padding: 20px; 
	margin-left:-12px;
}
</style>
	
	
	