<?php 
 
 $CI         = & get_instance();
 $CI->load->database('default');
 $POSMtype   = $CI->db->query("select * from  POSM_Type   ");
 $POSMstatus = $CI->db->query("select * from POSM_Status  ");
 $outlet     = $CI->db->query("select * from OUTLET_Status ");
 
 $country    = $CI->db->query("select * from country ");
 $brand      = $CI->db->query("select * from brands ");
 $material_type   = $CI->db->query("select * from MATERIAL_Type ");
 $price      = $CI->db->query("select max(UnitPrice) as hp, min(UnitPrice) as mp from items ");
 $price      = $price->result_array();

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
	   $v = $o['id'];
	   $t = $o['premiumTypeName'];
	   $cid = $o['childID'];
	   
	   $optG =getPremium($cid,$selectedITEM); 
	   $s =  $selectedITEM == $v ? 'selected':"";
	   if($optG!='') $opt .= "<optgroup label='$t' >$optG</optgroup>";
	   else   	     $opt .= "<option value='$v' $s >$t</option>";
	  }
	 
            	
	return $opt; 
   }
   
  if(isset($post)) extract($post); 
 
?>

<div class="sideBar_search"> 
<form name='frmSearch' method='POST' action='<?php echo $searchAction ?>'> 
				<input name="txtsearch" value='<?php echo isset($txtsearch) ? $txtsearch:""  ?>' type="text" class="search-input" style="margin-top:6px;width:150px;">    
				<input name="btnsearch" value='' type="submit" class="search-btn" style="margin:7px 5px 0 0;">  	
			</div>
			<br/>
				<span style='color:white;'>Filter option:</span> 
				<div class='fl  searhPanel'>
					   <select name='selPOSMType' onchange="document.forms.frmSearch.submit()">
					      <option value='' > -Select POST Type- </option>    
					      <?php foreach( $POSMtype->result_array() as $o) 
						        { 
								 $v = $o['id'];
								 $t = $o['typeName'];
								 $s = isset($selPOSMType) ? ($selPOSMType==$v ? "selected":""):""; 
								 echo "<option value='$v' $s> $t </option>";   
								}  
						  ?>
					    </select>
     				    <select name='selPOSMStatus' onchange="document.forms.frmSearch.submit()">
					      <option value='' > -Select POSM Status- </option>    
					      <?php foreach( $POSMstatus->result_array() as $o) 
						        { 
								 $v = $o['id'];
								 $t = $o['statusName'];
								 $s = isset($selPOSMStatus) ? ($selPOSMStatus==$v ? "selected":""):""; 
								 echo "<option value='$v' $s > $t </option>";   
								}  
						  ?>
					    </select>
						 
						 <select name='selPremiumType' onchange="document.forms.frmSearch.submit()">
					      <option value='' > -Select Premium type- </option>    
					      <?php  
						    echo getPremium(0,$selPremiumType); 
						  ?>
					    </select>
						<select name='seloutlet' onchange="document.forms.frmSearch.submit()">
					      <option value='' > -Select POSM Outlet- </option>    
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
						<select name='selCountry'  onchange="document.forms.frmSearch.submit()">
					      <option value='' > -Select Country- </option>    
					      <?php foreach( $country->result_array() as $o) 
						        { 
								 $v = $o['id'];
								 $t = $o['countryName'];
								  $s = isset($selCountry) ? ($selCountry==$v ? "selected":""):""; 
								 echo "<option value='$v' $s > $t </option>";   
								}  
						  ?>
					    </select>
						<select name='selBrand'  onchange="document.forms.frmSearch.submit()">
					      <option value='' > -Select Brand- </option>    
					      <?php foreach( $brand->result_array() as $o) 
						        { 
								 $v = $o['id'];
								 $t = $o['brandName'];
								  $s = isset($selBrand) ? ($selBrand==$v ? "selected":""):""; 
								 echo "<option value='$v' $s > $t </option>";   
								}  
						  ?>
					    </select>
						<select name='selMaterial'  onchange="document.forms.frmSearch.submit()">
					      <option value='' > -Select Materials- </option>    
					      <?php foreach( $material_type->result_array() as $o) 
						        { 
								 $v = $o['id'];
								 $t = $o['materialName'];
								  $s = isset($selMaterial) ? ($selMaterial==$v ? "selected":""):""; 
								 echo "<option value='$v' $s > $t </option>";   
								}  
						  ?>
					    </select>
				
					     
				</div>
				<?php echo $csrf ?>
</form>