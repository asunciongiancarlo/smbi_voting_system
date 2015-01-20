<div class="content">
	
	<div class="title-content">
		<h2><?php echo $ViewLabel; ?></h2>
	</div>
	
	<div style="float:right;margin: 16px;">
		<a href='<?php echo HTTP_PATH ."report/downloadCSV/$csvFile"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/download-icon.png' title='Download Reports'></a>
		<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
	</div>
		
	<div class="clear"></div>

	<div class="working_area">
		<div class="container3" style="padding:10px;">
		<?php 
		 $countryID=NULL;
		 extract($POST);
		 $CI         = & get_instance();
		 $CI->load->database('default');
		extract($POST);
		 $price_categories = $CI->db->query("SELECT DISTINCT(extra_label) as levels FROM price_range ORDER BY xORDER ASC");
		 $price_categories = $price_categories->result_array();
		 
		//print_r($POST);
		?>
		  <form name="SMBi2" id="statusTable" style="margin: 10px 10px 10px 10px;" action='<?php echo HTTP_PATH."report/campaign_items_summary/$ViewType" ?>' method="POST">
		  <?php 
			//echo $val1;
			$opt = array(
						array('val'=>'itemCode',   		'label'=>'Item Code'),
						array('val'=>'itemName',   		'label'=>'Item Name'),
						array('val'=>'ptype',  		    'label'=>'Item Type'),
						array('val'=>'extra_label',  	 'label'=>'Price Category'),
						array('val'=>'country_Name',  	'label'=>'Country'),
						array('val'=>'campaign_Name',  	'label'=>'Campaign Name'),
						array('val'=>'totalVote',      	'label'=>'Vote'));
			
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
			 if($opt1=="extra_label"){
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
			echo "</select>";
			?>
			
			<?php
			
			echo "<select name='operator' style='margin-right:10px;width:75px;'>";
			foreach($operators as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($operator==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "Date From: <input type='text' name='DateFrom' value='$DateFrom'  id='datepicker'  style='width:85px;margin-right:10px;'>";
			echo "Date To: <input type='text' name='DateTo'     value='$DateTo'    id='datepicker2' style='width:85px;'>";	
			
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
			 if($opt2=="extra_label"){
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
			echo "</select>";
			?>
			<?php 
		
		   if($limit+20 <= $totrec) 
			  $value = ($limit+1) .'-'. ($limit+20);
			else
			  $value = ($limit+1) .'-'. ($totrec);
					
			?>
			 
			
			<input type='submit' name='Submit' value='Submit' style='margin-left:84.6%;width:80px; margin-top: -79px;' class='Button_Under_Report'>
			<input type='submit' name='Reset' value='Reset' style='margin-left: 5px;width:80px; margin-top: -79px;' class='Button_Under_Report'>
		    <?php echo "<input type='hidden' name='order' id='order' value='$order'>"; 
				  echo "<input type='hidden' name='sort' id='sort' value='n'>";
			?>
			
			<?php echo "<div class='fr' style='margin: -23px 6px 0px 0px;z-index:1;'> Record: ". $value .' of <b>' . $totrec ."</b>"; ?> 
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
		</form>  
		<label style="font-size:11px;color:#555;margin-top: -20px; margin-left:10px;"><b>Note:</b> Date format should be <b>YYYY-MM-DD</b> (Ex. 2014-12-31)</label>
		
	    <div style='clear:both;'></div>
	   
	    <?php 
			echo $table;
		?>
		
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
	if(id.value=='extra_label'){
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
	if(id.value=='extra_label'){
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
			url: '<?php echo HTTP_PATH ?>report/Summay_iWant_Voters/'+ itemID,
			async: false
		}).responseText;
	}	
	document.getElementById('List_of_Items').innerHTML = a;
  }
 </script>



<style>
td { border-color:#AAA6A6 }
th { border-color:#AAA6A6 }
table { border-color:#AAA6A6 }
</style>

	

	

