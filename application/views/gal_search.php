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
<form name='frmSearch' method='POST'  action='<?php echo $searchAction ?>'> 
	
			<br/>
				<span style='color:white;'>Filter option:</span> 
				<div class='fl  searhPanel'>
					   <select name='selPOSMType' >
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
     				    <select name='selPOSMStatus'>
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
						 
						 <select name='selPremiumType' >
					      <option value='' > -Select Premium type- </option>    
					      <?php  
						    echo getPremium(0,$selPremiumType); 
						  ?>
					    </select>
						<select name='seloutlet' >
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
						<select name='selCountry'  >
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
						<select name='selBrand'  >
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
						<select name='selMaterial'  >
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
						<select name='selAge'  >
					      <option value='' > - Item Age - </option>    
					      <?php for($age=0;$age<=50;$age+=2) 
						        { 
								  $s = isset($selAge) ? ($selAge=="$age-".($age+3) ? "selected":""):"";  
								 echo "<option $s value='$age-".($age+3)."'> $age-".($age+3)."</option>";   
								}  
						  ?>
					    </select>
						<select name='selPrice'  >
					      <option value='' > - Item Price - </option>    
					      <?php 
						  $min = ceil($price[0]['mp']);
						  $max = $price[0]['hp'];
						  for($min;$min<=$max;$min+=10) 
						        { 
								  $s = isset($selPrice) ? ($selPrice=="$min-".($min+3) ? "selected":""):"";  
								 echo "<option $s value='$min-".($min+3)."'> $min-".($min+11)."</option>";   
								}  
						  ?>
					    </select>
					     
				</div>
				<?php echo $csrf ?>
				<div class=""> 

				<input name="btnsearch" class='nav-REMOTE-btn1'     type="submit"   style="margin-top:6px;width:290px;background:#a70001">    
				 	
               </div>
</form>