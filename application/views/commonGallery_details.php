<div class="content" style="width: 112%;margin-left: -6%;">
	
	<div class="title-content">
		<h2>Common Gallery Number of Views in Details</h2>
	</div>
	
	<div style="float:right;margin: 16px;">
		<a href='<?php echo HTTP_PATH ."report/downloadCSV/$csvFile"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/download-icon.png' title='Download Reports'></a>
		<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png' title='Help'></a>
	</div>
		
		
	<div class="clear"></div>

	<div class="working_area" style="min-height:816px;">
		<div class="container3" style="padding:10px;">
		<?php 
		 $countryID=NULL;
		 extract($POST);
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 $CI->load->library('forms');
		 $price_categories = $CI->db->query("SELECT DISTINCT(extra_label) as levels FROM price_range ORDER BY xORDER ASC");
		 $price_categories = $price_categories->result_array();
		 
		 if($_SESSION['super_admin']=='y' OR $_SESSION['countryID']==0){
			$Csql  = "SELECT country.id as cID, countryName FROM country WHERE id!= 0";
			$cnd = "";
			if(isset($countryID) AND $countryID!='all') 
				$cnd =  "WHERE admin_users.countryID = ".$countryID;
					
			$users = "SELECT user_id, admin_users.full_name as fname 
					  FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id $cnd	
					  GROUP BY admin_users.id";
		 }else{
			$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id=".$_SESSION['countryID'];
			if(isset($countryID)) 
				$cnd =  $countryID;
			else
				$cnd = $_SESSION['countryID'];
				
			$users = "SELECT user_id, admin_users.full_name as fname 
					  FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id 
					  WHERE items.countryID = ".$cnd."
					  GROUP BY admin_users.id";
		 }	
		 $country = $CI->db->query($Csql);
		 
		 $sql = "SELECT YEAR(dateAdded) as cyear FROM items GROUP BY YEAR(dateAdded)";
		 $years = $CI->db->query($sql);
		 
		 $users = $this->db->query($users);
		 $users = $users->result_array();
		 //print_r($users);
		 
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		extract($POST);
		
		//print_r($POST);
		?>
		 <form name="SMBi2" id="statusTable" action='<?php echo HTTP_PATH."report/commonGallery_details/$cID/$fieldName/$fieldVal/$DateFrom/$DateTo" ?>' method="POST"> 
	
		  <?php 
			//echo $val1;
			$opt = array(
						array('val'=>'num_views',   'label'=>'Number of Views'),
						array('val'=>'likes',  		'label'=>'Likes'),
						array('val'=>'wants',  		'label'=>'Wants'),
						array('val'=>'itemCode',  	'label'=>'Item Code'),
						array('val'=>'itemName',  	'label'=>'Item Name'),
						array('val'=>'pstatus',  	'label'=>'Item Status'),
						array('val'=>'ptype',  		'label'=>'Item Type'),
						array('val'=>'poutlet_status',  'label'=>'Outlet Type'),
						array('val'=>'ppremium_type',  'label'=>'Premium Type'),
						array('val'=>'pmaterial',  	 'label'=>'Material'),
						array('val'=>'pbrand',  	 'label'=>'Brand'),
						array('val'=>'full_name',    'label'=>'User'),
						array('val'=>'pcountry',   	 'label'=>'Country'),
						array('val'=>'publish',   	 'label'=>'Publish'),
						array('val'=>'UnitPrice', 	 'label'=>'Local Price'),
						array('val'=>'USD_Price',  	 'label'=>'USD Price'),
						array('val'=>'price_rangeName',  	 'label'=>'Price Category'),
						array('val'=>'dUploaded',    'label'=>'Date Uploaded'));
			
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
			
		echo "<div style='style=margin: 10px 10px 10px 10px;'>";	
			echo "<select name='opt1' style='margin-right:10px;' onchange='priceCategory1(this)'>";
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
			$disabled_select_priceCategory = "disabled";
			$display_select_priceCategory  = "display:none;";
			$disabled_text_priceCategory   = "";
			$display_text_priceCategory    = "";
			if(isset($opt1)){
			 if($opt1=="price_rangeName"){
				$disabled_select_priceCategory  = "";
				$display_select_priceCategory   = "";
				$disabled_text_priceCategory    = "disabled";
				$display_text_priceCategory     = "display:none;";
			 }
			}
			?>
			<input type='text' name='val1' id='text_priceCategory' value="<?php echo $val1 ?>"  style='<?php echo $display_text_priceCategory ?>;margin-right:10px;margin-left:4px;' <?php echo $disabled_text_priceCategory ?>>
			<?
			echo "<select name='val1' id='select_priceCategory' style='$display_select_priceCategory;margin-left: 4px;margin-right:10px;' $disabled_select_priceCategory>";
			foreach($price_categories as $price_categorie)
			{ extract($price_categorie);
			  $s = ($levels==$val1) ? "selected" : "";
			  echo "<option value='$levels' $s>$levels</option>";
			}
			$s = ("Uncategorized"==$val1) ? "selected" : "";
			echo "<option value='Uncategorized' $s>Uncategorized</option>";
			echo "</select>";
			?>
			
	
			<?php
			echo "<select name='operator' style='margin-right:10px;width:150px;' id='select_textCategory'>";
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
			
			echo "<select name='opt2' style='margin-right:10px;' onchange='priceCategory2(this)'>";
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
			$disabled_select_priceCategory = "disabled";
			$display_select_priceCategory  = "display:none;";
			$disabled_text_priceCategory   = "";
			$display_text_priceCategory    = "";
			if(isset($opt2)){
			 if($opt2=="price_rangeName"){
				$disabled_select_priceCategory  = "";
				$display_select_priceCategory   = "";
				$disabled_text_priceCategory    = "disabled";
				$display_text_priceCategory     = "display:none;";
			 }
			}
			?>
			<input type='text' name='val2' id='text_priceCategory2' value="<?php echo $val2 ?>"  style='<?php echo $display_text_priceCategory ?>;margin-right:10px;margin-left:4px;' <?php echo $disabled_text_priceCategory ?>>
			<?php 
			echo "<select name='val2' id='select_priceCategory2' style='$display_select_priceCategory;margin-left: 4px;margin-right:10px;' $disabled_select_priceCategory>";
			foreach($price_categories as $price_categorie)
			{ extract($price_categorie);
			  $s = ($levels==$val2) ? "selected" : "";
			  echo "<option value='$levels' $s>$levels</option>";
			}
			$s = ("Uncategorized"==$val2) ? "selected" : "";
			echo "<option value='Uncategorized' $s>Uncategorized</option>";
			echo "</select>";
			?>
		  <?php 
		   if($limit+20 <= $totrec) 
			  $value = ($limit+1) .'-'. ($limit+20);
			else
			  $value = ($limit+1) .'-'. ($totrec);
					
			?>
			<?php echo "<div class='fr' style='margin: -2px 28px 8px 0px;'> Record: ". $value .' of <b>' . $totrec ."</b>"; ?> 
			<select onchange="document.forms[0].submit()" name='selpage' style="font-size:12px;width:98px;"> 
			  <?php 
				
				for($x=0; $x <= $totrec-1;$x+=20)
				   {
					if($x+20 <= $totrec) 
					  $value = ($x+1) .'-'. ($x+20);
					else
					  $value = ($x+1) .'-'. ($totrec); 
					$sel = $limit == $x ? "selected":"";
					echo "<option $sel value='$x'> $value </option>";
				   }
				?>
			</select>
			</div>
			<div class="cl"></div>	
			<input type='submit' name='Submit' value='Submit' style='margin-left: 85%;width:80px;margin-top: -25px;' class='Button_Under_Report'>
			<input type='submit' name='Reset' value='Reset'   style='margin-left: 5px;width:80px;margin-top: -25px;' class='Button_Under_Report'>
			<label style="font-size:11px;color:#555;margin-top:-37px;"><b>Note:</b> Date format should be <b>YYYY-MM-DD</b> (Ex. 2014-12-31)
			<br/> Using between condition should have the following input format: <b>'input 1' AND 'input 2'</b>
			</label>
			<div style='font-weight:bold;'>
			  <?php echo "$country_name | $fldName | $quarterStr"; ?>
			 </div>
		  
		
		<?php echo "<input type='hidden' name='order' id='order' value='$order'>"; 
			  echo "<input type='hidden' name='sort' id='sort' value='n'>";
		?>
	    <div style='clear:both;height:10px;'></div>
	    </div>
	    <?php 
			echo $table;
		?>
		</form>
		<div style='clear:both'></div>
		
		</div>
	</div>
	<div class="clear"></div>
</div>

<div id="dialog-form" title="Logs" style='display:none;'>
	<div id="List_of_Items"></div>
</div>


<script>
  function priceCategory1(id)
  {
	if(id.value=='price_rangeName'){
	document.getElementById('select_priceCategory').disabled 		= false;
	document.getElementById('select_priceCategory').style.display 	= '';
	
	document.getElementById('text_priceCategory').style.display 	= 'none';
	document.getElementById('text_priceCategory').disabled			= true;
	}else{
	document.getElementById('select_priceCategory').disabled 		= true;
	document.getElementById('select_priceCategory').style.display 	= 'none';
	
	document.getElementById('text_priceCategory').style.display 	= '';
	document.getElementById('text_priceCategory').disabled 			= false;
	}
  }
  
  function priceCategory2(id)
  {
	if(id.value=='price_rangeName'){
	document.getElementById('select_priceCategory2').disabled 		= false;
	document.getElementById('select_priceCategory2').style.display 	= '';
	
	document.getElementById('text_priceCategory2').style.display 	= 'none';
	document.getElementById('text_priceCategory2').disabled			= true;
	}else{
	document.getElementById('select_priceCategory2').disabled 		= true;
	document.getElementById('select_priceCategory2').style.display 	= 'none';
	
	document.getElementById('text_priceCategory2').style.display 	= '';
	document.getElementById('text_priceCategory2').disabled 			= false;
	}
  }
  
  function sortBy(val)
  {
	document.getElementById('order').value = val;
	document.getElementById('sort').value  = 'y';
	$('#statusTable').submit();
  }

  function viewDialog(tbl,itemID)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/hits/'+tbl+'/'+itemID,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
  
   function showVoters(campaignType,itemID)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 400,
	width: 950});
  
	if(campaignType=='iLike'){
		var a = $.ajax({
			url: '<?php echo HTTP_PATH ?>report/Summay_iLike_Voters/'+ itemID,
			async: false
		}).responseText;
	
	}else{
		var a = $.ajax({
			url: '<?php echo HTTP_PATH ?>report/Summay_iWant_Voters2/'+ itemID,
			async: false
		}).responseText;
	}	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>

<style>
td { border-color:#AAA6A6; padding: 2px 0px 0px 1px; }
th { border-color:#AAA6A6 }
table { border-color:#AAA6A6 }
</style>

	

	

