<div class="content">
	
	<div class="title-content">
		<h2>Campaign Summary</h2>
	</div>
	<div style="float:right;margin: 16px;">
		
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
		
		 $price_categories = $CI->db->query("SELECT DISTINCT(extra_label) as levels FROM price_range ORDER BY xORDER ASC");
		 $price_categories = $price_categories->result_array();
		
		$sql = "SELECT YEAR(dateAdded) as cyear FROM items GROUP BY YEAR(dateAdded)";
		$years = $CI->db->query($sql);
		 
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		extract($POST);
		?>

		<div class='fl ' style='width:100%;margin-top:-12px;'>
		    <form name='BU_activeness' style="margin: 10px 10px 10px 10px;" action='<?php echo HTTP_PATH ."report2/campaign_summary" ?>' method='post'>
			<?php 
			$opt = array(
						array('val'=>'countryName',   	'label'=>'Country'),
						array('val'=>'campaignName',   	'label'=>'Country'),
						array('val'=>'extra_label',     'label'=>'Price Category'),
						array('val'=>'uploaded_items',  'label'=>'Uploaded Items'),
						array('val'=>'winning_items',  	'label'=>'Winning Items')
						);
			
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
			
			
			echo "<select name='opt1' style='margin-right:10px;width: 170px;' onchange='priceCategory1(this)'>";
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $l  = $o['label'];
			 $s  = ($opt1==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<select name='cond1' style='margin-right:10px;width:207px;'>";
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
			echo "</select>";
			?>
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
			
			echo "Uploaded From: <input type='text' name='DateFrom' value='$DateFrom'  id='datepicker'  style='width:85px;margin-right:10px;'>";
			echo "Uploaded To: <input type='text' name='DateTo'     value='$DateTo'    id='datepicker2' style='width:85px;'>";
			
			echo "<div class='cl'></div>";
			
			echo "<select name='opt2' style='margin-right:10px;width: 170px;' onchange='priceCategory2(this)'>";
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $l  = $o['label'];
			 $s  = ($opt2==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<select name='cond2' style='margin-right:10px;width:207px;'>";
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
			</div>
			<div class="cl"></div>
			<input type='submit' name='Submit' value='Submit' style='margin-left: 83.2%;width:80px;margin-top: -92px;' class='Button_Under_Report'>
			<input type='submit' name='Reset' value='Reset' style='margin-left: 5px;width:80px;margin-top: -92px;' class='Button_Under_Report'>
			<div style='clear:both;'></div>
		  
			</form>
			<?php 
			if($DateFrom=='' AND $DateTo==''){
			 $DateFrom="null";
			 $DateTo  ="null";
			}
			?>
			<div style='clear:both;'></div>
			<?php echo $table; ?>  
			<div style='clear:both'></div>
		</div>

		</div>
	</div>
	
	<div class="clear"></div>
</div>

<div id="dialog-form" title="LIST OF ITEMS" style='display:none;'>
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



	

	

