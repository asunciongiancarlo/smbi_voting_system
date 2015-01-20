<?php 
 
 
 $CI         = & get_instance();
 $CI->load->database('default');
 
 //ADMIN
 $cond = "";
 if($_SESSION['super_admin']=='y' OR $_SESSION['countryID']==0){
	$brand = "SELECT id, brandName FROM brands ORDER BY brandName ASC";
 }
 else{
	$cond = "WHERE id = ".$_SESSION['countryID'];
	$brand = "select * from brands where id in (select brandID from brandXref where countryID=".$_SESSION['countryID'].") ORDER BY brandName ASC";
 }
 
 
 if($galTitle=="Common Gallery")
	$brand = "select * from brands where id in 
				(select brandID from commonGalleryBrands) 
			  ORDER BY brandName ASC";
	
 
 if($galTitle=='Common Gallery')
	$cond = "WHERE id !=0";
 
 $POSMtype   = $CI->db->query("select *, POSM_Type.id as POSM_TypeID  from  POSM_Type ");
 $POSMstatus = $CI->db->query("select * from POSM_Status");
 $outlet     = $CI->db->query("select * from OUTLET_Status");
 
 $country    = $CI->db->query("select * from country $cond");
 $brand      = $CI->db->query($brand);
 $material_type   = $CI->db->query("select * from MATERIAL_Type ORDER BY materialName ASC");
 $price      = $CI->db->query("select max(UnitPrice) as hp, min(UnitPrice) as mp from items ");
 $price      = $price->result_array();

  function priceRange($id,$priceRangeID)
  {
	$CI         = & get_instance();
	$CI->load->database('default');
	$option="";
	$price_ranges   = $CI->db->query("select level_name, extra_label, price_range.id as pID 
									FROM price_range 
									WHERE POSMTypeID = $id
									ORDER BY id ASC, xOrder ASC");
	 //print_r($price_ranges->result_array);
	 foreach( $price_ranges->result_array() as $o) 
	{ 
	 $v = $o['pID'];
	 $t = $o['level_name']." ".$o['extra_label'];
	 $s = isset($priceRangeID) ? ($priceRangeID==$v ? "selected":""):""; 
	 echo "<option value='$v' $s> $t </option>";   
	}  
  }
 
 function getPremium($pid=0,$selectedITEM='') 
   {
    $CI         = & get_instance();
    $CI->load->database('default');
    $sql = "select * from premiumItemType as pt inner join premiumItemTypeRef as ptREF on pt.id = ptREF.childID where ptREF.parentID='$pid'";
    $premium   = $CI->db->query($sql);
    $premium   = $premium->result_array();
	$opt = "";
	
	foreach( $premium as $o)
	  {
	   $v   = $o['id'];
	   $t   = $o['premiumTypeName'];
	   $cid = $o['childID'];
	   
	   $optG = getPremium($cid,$selectedITEM); 
	   $s =  $selectedITEM == $v ? 'selected':"";
	   if($optG!='') $opt .= "<optgroup label='$t' >$optG</optgroup>";
	   else   	     $opt .= "<option value='$v' $s >$t</option>";
	  }
	 
            	
	return $opt; 
   }
   
  if(isset($post)) extract($post); 
 
  if(isset($txtsearch))
  {
	if($txtsearch=="null")
		$txtsearch="";
  }
  
  if(isset($priceFrom) OR isset($priceTo))
  {
	if($priceFrom=="null")
		$priceFrom="";
	if($priceTo=="null")
		$priceTo="";
  }
 
?>

<div class="sideBar_search" style='margin-left:-10px;margin-top: -45px;'> 
<form name='frmSearch' method='POST' action='<?php echo $searchAction ?>'> 
	<input type='hidden' name='active_page' id='active_page' value='<?php echo $active_page ?>'>
	<input name="txtsearch" value="<?php echo isset($txtsearch) ? str_replace('"',"",$txtsearch):'';  ?>" type="text" class="search-input">    
	<input name="btnsearch" value='' type="submit" class="search-btn" style="margin:7px 5px 0 0;" onclick="submitSearch()"> 	
	<div class="filter item-fil" style='height:28px; margin-top:5px;margin-bottom:1px;'  > 
		<img src='<?php echo HTTP_PATH.'img/gray.jpg'; ?>' onclick="viewfilter()" style="margin-top: -50px;">
	</div>
</div>
				<div class='fl  searhPanel' id='viewfilter' style='background:#340404;margin-top:8px;display:none;position:absolute;z-index: 2;'>
				<br/>
				   <select class='sel' name='selPOSMType' >
					      <option value='' > Select POSM Type </option>    
					      <?php foreach( $POSMtype->result_array() as $o) 
						        { 
								 $v = $o['id'];
								 $t = $o['typeName'];
								 $s = isset($selPOSMType) ? ($selPOSMType==$v ? "selected":""):""; 
								 echo "<option value='$v' $s> $t </option>";   
								}  
						  ?>
					    </select>
     				    <select class='sel' name='selPOSMStatus' >
					      <option value='' > Select POSM Status </option>    
					      <?php foreach( $POSMstatus->result_array() as $o) 
						        { 
								 $v = $o['id'];
								 $t = $o['statusName'];
								 $s = isset($selPOSMStatus) ? ($selPOSMStatus==$v ? "selected":""):""; 
								 echo "<option value='$v' $s > $t </option>";   
								}  
						  ?>
					    </select>
						 
						 <select class='sel' name='selPremiumType' >
					      <option value='' > Select Premium Item Type </option>    
					      <?php  
						    echo getPremium(0,$selPremiumType); 
						  ?>
					    </select>
						<select class='sel' name='seloutlet' >
					      <option value='' > Select Service Item Outlet Type </option>    
					      <?php foreach( $outlet->result_array() as $o) 
						        {
                                 $s = isset($selPOSMStatus) ? ($selPOSMStatus==$v ? "selected":""):"";								
								 $v = $o['id'];
								 $t = $o['statusName'];
								  $s = isset($seloutlet) ? ($seloutlet==$v ? "selected":""):""; 
								 echo "<option value='$v' $s > $t </option>";   
								}  
						  ?>
					    </select>
						<!--COUNTRY-->
						<select class='sel' name='selCountry'  >
					      <option value='' > Select Business Unit </option>    
					      <?php foreach( $country->result_array() as $o) 
						        { 
								 $v = $o['id'];
								 $t = $o['countryName'];
								  $s = isset($selCountry) ? ($selCountry==$v ? "selected":""):""; 
								 echo "<option value='$v' $s > $t </option>";   
								}  
						  ?>
					    </select>
						<!--COUNTRY-->
						
						<!---Brand--->
						<select class='sel' name='selBrand'  >
					      <option value='' > Select Brand </option>    
					      <?php foreach( $brand->result_array() as $o) 
						        { 
								 $v = $o['id'];
								 $t = $o['brandName'];
								  $s = isset($selBrand) ? ($selBrand==$v ? "selected":""):""; 
								 echo "<option value='$v' $s > $t </option>";   
								}  
						  ?>
					    </select>
						
						<!---Brand--->
						
						<select class='sel' name='selMaterial'  >
						  <option value='' > Select Material Type </option>    
						  <?php foreach( $material_type->result_array() as $o) 
								{ 
								 $v = $o['id'];
								 $t = $o['materialName'];
								  $s = isset($selMaterial) ? ($selMaterial==$v ? "selected":""):""; 
								 echo "<option value='$v' $s > $t </option>";   
								}  
						  ?>
						</select>
						
						<select class='sel' name='items_date'  >
						  <option value='' > Sort Items By Date </option>    
						  <?php
							$s = isset($items_date) ? ($items_date=='DESC' ? "selected":""):""; 
							echo "<option value='DESC' $s> Newest </option>";
							$s = isset($items_date) ? ($items_date=='ASC' ? "selected":""):""; 
							echo "<option value='ASC' $s> Oldest  </option>";
						  ?>
						</select>
						
						<select class='sel' name='nviews'  >
						  <option value='' > Select Number of Views </option>    
						  <?php
							$s = isset($nviews) ? ($nviews=='DESC' ? "selected":""):""; 
							echo "<option value='DESC' $s> Most Viewed</option>";
							$s = isset($nviews) ? ($nviews=='ASC' ? "selected":""):""; 
							echo "<option value='ASC' $s> Least Viewed</option>";
						  ?>
						</select>
						
					<table>
						<tr>
							<td style="text-align:left; padding:0px;" colspan="4"><label style='color:white;margin-right: 9px;margin-bottom: 3px;' class="fl"><b>Absolute Price Range</b></label></td>
						</tr>
						<tr>
								<td style="text-align:left; padding:0px;" colspan="5">
									<select class='sel' name='sort_by_price'  >
									  <option value='' > Sort Items by Price </option>    
									  <?php
										$s = isset($sort_by_price) ? ($sort_by_price=="UnitPrice-DESC" ? "selected":""):""; 
										echo "<option value='UnitPrice-DESC' $s> Local Price - Highest </option>";
										$s = isset($sort_by_price) ? ($sort_by_price=="UnitPrice-ASC" ? "selected":""):""; 
										echo "<option value='UnitPrice-ASC' $s> Local Price - Lowest  </option>";
										
										$s = isset($sort_by_price) ? ($sort_by_price=="USD_Price-DESC" ? "selected":""):""; 
										echo "<option value='USD_Price-DESC' $s> USD Price - Highest </option>";
										$s = isset($sort_by_price) ? ($sort_by_price=="USD_Price-ASC" ? "selected":""):""; 
										echo "<option value='USD_Price-ASC' $s> USD Price - Lowest  </option>";
									  ?>
									</select>
								</td>
							</tr>
						<tr>
						<tr>
							<td style="text-align:left; padding:0px;" colspan="4"><label style='color:white;margin-right: 9px;margin-bottom: 3px;' class="fl"><b>Price Category</b></label></td>
						</tr>
						<tr>
							<td style="text-align:left; padding:0px;" colspan="5">
								<select name="priceRangeID">
								<option value='' > Select Price Category </option> 
								<?php 
								if(isset($priceRangeID)){
									if($priceRangeID==0 AND $priceRangeID!='null') $s = "selected";
								}
								echo "<option value='0' $s> Uncategorized </option>";
								foreach($POSMtype->result_array as $POSMt){ 
								$k = $POSMt['POSM_TypeID'];
								$j = $POSMt['typeName'];
								echo "<optgroup label='$j'>";
								priceRange($POSMt['POSM_TypeID'],$priceRangeID);
								echo "</optgroup>";
								}?>
								</select>
							</td>
						</tr>
							<td class='dateParam' style='padding:0px;width: 90px;'>
								<?php $s = isset($priceRange) ? ($priceRange=='UnitPrice' ? "checked":""):"";  
									  echo "<input type='radio' name='priceRange'   value='UnitPrice' class='fl' $s >   <label style='color:white;' class='fl'> Local </label>";
								?>
							</td>
							<td class='dateParam' style='padding:0px;width: 70px;'>
								<?php $s = isset($priceRange) ? ($priceRange=='USD_Price' ? "checked":""):"";  
									  echo "<input type='radio' name='priceRange'   value='USD_Price' class='fl' $s >   <label style='color:white;' class='fl'> USD </label>";
								?>
							</td>
							<td class='dateParam' style='padding:0px;'>
								<?php $v = isset($priceFrom) ? $priceFrom :"";  
									  echo "<input type='text' name='priceFrom' value='$v' style='width:75%;background-color: #F6E2E2;border: 1px solid #583434;color:black;'>";
								?>
							</td>
							<td style='padding:0px;'>
								<label style='color:white;'>to</label>
							</td>
							<td class='dateParam' style='padding:0px;'>
								<?php $v = isset($priceTo) ? $priceTo :"";  
									  echo "<input type='text' name='priceTo' value='$v' style='width:75%;background-color: #F6E2E2;border: 1px solid #583434;color:black;'>";
								?>
							</td>
						</tr>
					</table>
			
					<table>
						<tr>
							<td style="text-align:left; padding:0px;" colspan="3"><label style='color:white;margin-right: 9px;margin-bottom: 3px;' class="fl"><b>Created Last</b></label></td>
						</tr>
						<tr>
							<td class='dateParam'>
							<select name='year' class='range'>
							<?php
								for($i=0;$i<=100;$i++)
								{	
									$s = isset($year) ? ($year==$i ? "selected":""):""; 
									if($i==0)
										echo "<option value='0' $s>Year</option>";
									else
										echo "<option value='$i' $s>$i</option>";
								}
							?>
							</select>
							</td>
							<td class='dateParam'>
							<select name='month' class='range'>
							<?php
								for($i=0;$i<=12;$i++)
								{	
									$s = isset($month) ? ($month==$i ? "selected":""):""; 
									if($i==0)
										echo "<option value='0' $s>Month</option>";
									else
										echo "<option value='$i' $s>$i</option>";
								}
							?>
							</select>
							</td>
							<td class='dateParam'>
								<input name="btnsearch" value='' type="button" class="search-btn" style="margin: -7px 0px 0 54px;padding-right: 36px;" onclick="submitSearch()"> 
							</td>
						</tr>
					</table>    
				</div>
				<?php echo $csrf ?>
</form>