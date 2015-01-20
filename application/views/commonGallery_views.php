<div class="content">
	
	<div class="title-content">
		<h2> Number of Views from Common Gallery  </h2>
	</div>
	<div style="float:right;margin: 16px;">
		<a href='<?php echo HTTP_PATH ."report/downloadCSV/$csvFile"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/download-icon.png' title='Download Reports'></a>
		<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png' title='Help'></a>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
		 $CI         = & get_instance();
		 $CI->load->database('default');
		
		$Csql = "SELECT countryName FROM country WHERE id= $cID";
		$country = $CI->db->query($Csql);
		$country = $country->row();
		$cName = $country->countryName;
		
		$sql = "SELECT YEAR(dateAdded) as cyear FROM items GROUP BY YEAR(dateAdded)";
		$years = $CI->db->query($sql);
		 
		$action = HTTP_PATH.'users/iLikeCampaignRules/insert';
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		extract($POST);
		?>

		<div class='fl ' style='width:100%;margin-top:-12px;'>
		    <form name='BU_activeness' style="margin: 10px 10px 10px 10px;" action='<?php echo HTTP_PATH ."report/commonGallery_views/$tab" ?>' method='post'>
			<?php 
			$opt = array(
						array('val'=>'cName',   		'label'=>'Country'),
						searchParam($tab),
						array('val'=>'uploaded',  		'label'=>'Uploaded Items'),
						array('val'=>'num_views',  	'label'=>'Views'));
			
			$cond = array(
						array('val'=>'equal',  		'label'=>'Equal ='),
						array('val'=>'containing',  'label'=>'Containing'),
						array('val'=>'in',  		'label'=>'IN(...)'),
						array('val'=>'between',  	'label'=>'Between'),
						array('val'=>'greaterThan', 'label'=>'Greater than or equal >='),
						array('val'=>'lessThan',    'label'=>'Less than or equal <='));
						
			$operators = array(
						array('val'=>' AND ',  'label'=>'AND'),
						array('val'=>' OR ',   'label'=>'OR'));
			
			
			echo "<select name='opt1' style='margin-right:10px;width: 157px;'>";
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $l  = $o['label'];
			 $s  = ($opt1==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<select name='cond1' style='margin-right:10px;'>";
			foreach($cond as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($cond1==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			
			if(!isset($val1)) $val1='';
			?>
			<input type='text' name='val1' value="<?php echo $val1; ?>" style='margin-right:10px;width: 190px;'>
			
			<?php
			echo "<select name='operator' style='margin-right:10px;width:73px;'>";
			foreach($operators as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($operator==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "Published From: <input type='text' name='DateFrom' value='$DateFrom'  id='datepicker'  style='width:85px;margin-right:10px;'>";
			echo "Published To: <input type='text' name='DateTo'     value='$DateTo'    id='datepicker2' style='width:85px;'>";
			
			echo "<div class='cl'></div>";
			
			echo "<select name='opt2' style='margin-right:10px;width: 157px;'>";
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $l  = $o['label'];
			 $s  = ($opt2==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<select name='cond2' style='margin-right:10px;'>";
			foreach($cond as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($cond2==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			if(!isset($val2)) $val2='';
			?>
			<input type='text' name='val2' value="<?php echo $val2; ?>" style='margin-right:10px;width: 190px;'>	

			</div>
			<div class="cl"></div>
			<input type='submit' name='Submit' value='Submit' style='margin-left: 83.2%;width:80px;margin-top: -75px;' class='Button_Under_Report'>
			<input type='submit' name='Reset' value='Reset' style='margin-left: 5px;width:80px;margin-top: -75px;' class='Button_Under_Report'>
			<h2 class='fl form' style='width:50%;margin-right:10px;margin-top: -29px; margin-bottom:-3px; margin-left:10px;'> COUNTRY: <?php echo $cName ." | ". $quarterStr; ?></h2>
			<div style='clear:both;'></div>
		  
			</form>
			<?php 
			if($DateFrom=='' AND $DateTo==''){
			 $DateFrom="null";
			 $DateTo  ="null";
			}
			?>
			   <div style='clear:both;'></div>
			   	<table id="large" cellpadding="0" cellspacing="0" border=1 style="width:100%;font-size:13px;margin-bottom: 5px;" class="iLike_Result_Table tablesorter">
					<?php 
						$Tbl_rows = array(
						array('t'=>'POSM_STATUS',   'label'=>'POSM STATUS',   'link'=>HTTP_PATH.'report/commonGallery_views/POSM_STATUS' ),
						array('t'=>'POSM_TYPE',     'label'=>'POSM TYPE',     'link'=>HTTP_PATH.'report/commonGallery_views/POSM_TYPE'),
						array('t'=>'OUTLET_TYPE',   'label'=>'OUTLET TYPE',   'link'=>HTTP_PATH.'report/commonGallery_views/OUTLET_TYPE'),
						array('t'=>'PREMIUM_TYPE',  'label'=>'PREMIUM TYPE',  'link'=>HTTP_PATH.'report/commonGallery_views/PREMIUM_TYPE'),
						array('t'=>'MATERIAL_TYPE', 'label'=>'MATERIAL TYPE', 'link'=>HTTP_PATH.'report/commonGallery_views/MATERIAL_TYPE'),
						array('t'=>'BRAND_TYPE',    'label'=>'BRAND TYPE',    'link'=>HTTP_PATH.'report/commonGallery_views/BRAND_TYPE'));
						echo "<tr style='height: 45px;'>";
						foreach($Tbl_rows as $Tr){
						extract($Tr);
							$cl = ($tab==$t) ? '_active':''; 
							echo "<td class='tableTab$cl'><a href='$link' class='whitePls'> $label </a></td>";
						}echo "</tr>";
					?>
				
				<tr>
				<td colspan='6' style='background-color:#b99595'>
				<table id="large" cellpadding="0" cellspacing="0" border=1 style="width:98%;font-size:13px;margin-top: -2px;margin-left:12px;background-color:white;margin-bottom: 9px;" class="iLike_Result_Table tablesorter">
				<tbody>
				 <?php 
					$x = 0;	
					$y=1;
					$z=1;
					$sum_total=0;
					$ctr=1;
					$itemTypeCtr=0;
					//print_r($results);
					foreach($results as $r) {
					extract($r);
					echo "<tr style='height: 30px;'>
							<th class='darkTable' style='width:10px;text-align:center;color:white;padding:0px;border-top-style: hidden;' bgcolor='#370909'>   	 <b>No 		 		  </b></th> 
							<th class='darkTable' style='width:10px;text-align:center;color:white;padding:0px;border-top-style: hidden;' bgcolor='#370909'>   	 <b>Country 		 	  </b></th> 
							<th class='darkTable' style='width:10px;text-align:center;color:white;padding:0px;border-top-style: hidden;' bgcolor='#370909'>   	 <b>$table  			  </b></th> 
							<th class='darkTable' style='width:64px;text-align:center;color:white;padding:0px;border-top-style: hidden;' bgcolor='#370909'>     <b>Uploaded Items  	  </b></th> 
							<th class='darkTable' style='width:64px;text-align:center;color:white;padding:0px;border-top-style: hidden;' bgcolor='#370909'>     <b>Views    </b></th> 
							<th class='darkTable' style='width:64px;text-align:center;color:white;padding:0px;border-top-style: hidden;' bgcolor='#370909'>     <b>Action  	  		  </b></th> 
						</tr>";
					$sub_total_Upload 	 	= 0;
					$sub_total_myViews 		= 0;
					$sum_total_Upload		= 0;
					$sum_total_Published	= 0;
					$sum_myViews			= 0;
		
					$x = 0;	
					//print_r($rows);
					$f="";
					$c="";
					foreach($rows as $r)
					{extract($r);
					$c   = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					//SUB TOTAL & REST
	
					if($f!=$fldVal AND $x!=1 AND $sa==TRUE AND count($rows)>=2){
					echo "<tr>
						   <td   style='text-align:left;padding-left:30px;'>	<b>Sub Total</b></td>
						   <td   ></td>		  			  		  			  
						   <td   ></td>		  			  		  			  
						   <td   style='text-align:right;padding-right:20px;'><b>$sub_total_Upload</b></td>
						   ";
					if($ctr==1)
						echo "<td   style='text-align:right;padding-right:20px;'> <b>$sub_total_myViews </b> </td>";
					else
						echo "<td   style='text-align:right;padding-right:20px;'>  <b>$sub_total_myViews </b></td>";
					
					echo  "</tr>";
					$sub_total_Upload 	 	= 0;
					$sub_total_myViews 		= 0;
					$x=1;
					$ctr++;
					$itemTypeCtr++;
					}$f = $fldVal; $t= $table;
					
					$sub_total_Upload 		+= $Uploaded_Items;
					$sub_total_myViews 		+= $myViews;
					$sum_total_Upload       += $Uploaded_Items;
					$sum_myViews 			+= $myViews;
				 echo 
				 "<tr>
				  <td $c >												$x   			   </td>
				  <td $c style='text-align:left;padding-left:20px;'>	$Country_Name      </td>
				  <td $c style='text-align:left;padding-left:20px;'>	$fldVal     	   </td>
				  <td $c style='text-align:right;padding-right:20px;'>	$Uploaded_Items    </td>
				  <td $c style='text-align:right;padding-right:20px;'>	$myViews   </td>
				  <td $c style='text-align:center;padding-right:20px;'>	<a href='".HTTP_PATH."report/commonGallery_details/$cID/$fld/$fldID/$DateFrom/$DateTo'>Details</a> </td>
				</tr>";
				 }
				 if(count($rows)>=2 AND $sa==TRUE AND $itemTypeCtr>=2){
				 echo "<tr>
					   <td style='text-align:left;padding-left:30px;'>	<b>Sub Total</b></td>
					   <td></td>		  			  		  			  
					   <td> </td>		  			  		  			  
					   <td style='text-align:right;padding-right:20px;'><b>$sub_total_Upload</b></td>
					   <td style='text-align:right;padding-right:20px;'><b>$sub_total_myViews</b></td>
					   <td style='text-align:center;padding-right:20px;'></td>
					  </tr>";
				 }
				 if(!empty($rows)){
				 //DETECT IF SUPERADMIN
				 $ALL = ($sa==TRUE) ? 'ALL' : $_SESSION['countryID'];
				 echo "<tr>
					   <td class='alter' style='text-align:left;padding-left:30px;'>	<b>Overall Total</b></td>
					   <td class='alter'></td>		  			  		  			  
					   <td class='alter'></td>		  			  		  			  
					   <td class='alter' style='text-align:right;padding-right:20px;'><b>$sum_total_Upload</b></td>
					   <td class='alter' style='text-align:right;padding-right:20px;'><b>$sum_myViews</b></td>
					   <td class='alter' style='text-align:center;padding-right:20px;'></td>
					  </tr>";
				 } 
				}
				
				if(empty($rows)){
					echo "<tr>
							<td colspan='6'> No match found, please review your search parameters.  </td>
						  </tr>";
				}
				?>
				</tbody>	
				</table>
				</td>
				</tr>
				</table>
				<div style='clear:both'></div>
		</div>

		</div>
	</div>
	
	<div class="clear"></div>
</div>

<div id="dialog-form" title="LIST OF ITEMS" style='display:none;'>
	<div id="List_of_Items"></div>
</div>

<?php 
function searchParam($type)
{
$arr="";
switch($type)
{
case 'POSM_STATUS':
	$arr = array('val'=>'pstatus',  'label'=>'POSM Status');
break;
case 'POSM_TYPE':
	$arr = array('val'=>'ptype',  'label'=>'POSM Type');
break;
case 'OUTLET_TYPE':
	$arr = array('val'=>'poutlet_status', 'label'=>'Outlet Type');
break;
case 'PREMIUM_TYPE':
	$arr = array('val'=>'ppremium_type', 'label'=>'Premium Type');
break;
case 'MATERIAL_TYPE':
	$arr = array('val'=>'pmaterial', 'label'=>'Material Type');
break;
case 'BRAND_TYPE':
	$arr = array('val'=>'pbrand',  'label'=>'Brand Type');
break;
}
return $arr;
}
?>

<script>  
  //item_distribution_Preview($view='',$countryID='',$month='',$year='',$fld='',$fld_val='')
  function viewDialog(view,cID,mID,year,fld,fld_val)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/item_distribution_Preview/'+view+'/'+cID+'/'+mID+'/'+year+'/'+fld+'/'+fld_val,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>



	

	

