<?php 
	$CI = & get_instance();
	$CI->load->database('default');
	$POSMtype   = $CI->db->query("select * from  POSM_Type");

	
	$CI2 =& get_instance();
	$CI2->load->library('forms');
	
	extract($POST); 
	if($DateFrom=='' AND $DateTo==''){
		$DateFrom="null";
		$DateTo  ="null";
	}
?>	
<div class="content" style="width: 112%;margin-left: -6%;">
	
	<div class="fl title-content">
		<h2><span style='text-transform:lowercase;'>e</span>Catalogue Report in Details</h2>
	</div>
	
	<div style="float:right;margin: 16px;">
		<a href='<?php echo HTTP_PATH ."report/downloadCSV/$csvFile"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/download-icon.png' title='Download Reports'></a>
		<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png' title='Help'></a>
	</div>
	
	<div class="clear"></div>
	
	<div class="working_area">	
		<div class="container3" style="margin-top:0px;padding:5px;">
		<label class='lbl_title' style='margin-top:10px; margin-left:80px;'> Catalogue: <?php echo $ecTitle; ?> </label>
		 <form name="SMBi2" id="statusTable" style="margin: 10px 50px 10px 80px;" action='<?php echo HTTP_PATH."report/eCatalogue_report_details/$ecID/$DateFrom/$DateTo" ?>' method="POST"> 
	
		  <?php 
			//echo $val1;
			$opt = array(
						array('val'=>'num_views',   'label'=>'Number of Views'),
						array('val'=>'itemCode',  	'label'=>'Item Code'),
						array('val'=>'itemName',  	'label'=>'Item Name'),
						array('val'=>'ptype',  		'label'=>'Item Type'),
						array('val'=>'poutlet_status','label'=>'Outlet Type'),
						array('val'=>'ppremium_type', 'label'=>'Premium Type'),
						array('val'=>'pmaterial',  	 'label'=>'Material'),
						array('val'=>'full_name',    'label'=>'User'),
						array('val'=>'publish',   	 'label'=>'Publish'),
						array('val'=>'UnitPrice', 	 'label'=>'Local Price'),
						array('val'=>'USD_Price',  	 'label'=>'USD Price'),
						array('val'=>'dUploaded', 'label'=>'Date Uploaded'));
			
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
			
			
			echo "<select name='opt1' style='margin-right:10px;width: 160px;'>";
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $l  = $o['label'];
			 $s  = ($opt1==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<select name='cond1' style='margin-right:10px;width: 204px;'>";
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
			<input type='text' name='val1' value="<?php echo $val1; ?>" style='margin-right:10px;width: 200px;'>
			
			<?php
			echo "<select name='operator' style='margin-right:10px;width:120px;'>";
			foreach($operators as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($operator==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			if($DateFrom=='null' AND $DateTo=='null'){
				$DateFrom="";
				$DateTo  ="";
			}
			echo "Published From: <input type='text' name='DateFrom' value='$DateFrom'  id='datepicker'  style='width:85px;margin-right:10px;'>";
			echo "Published To: <input type='text' name='DateTo'     value='$DateTo'    id='datepicker2' style='width:85px;'>";
			
			echo "<div class='cl'></div>";
			
			echo "<select name='opt2' style='margin-right:10px;width: 160px;'>";
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $l  = $o['label'];
			 $s  = ($opt2==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<select name='cond2' style='margin-right:10px;width: 204px;'>";
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
			<input type='text' name='val2' value="<?php echo $val2; ?>" style='margin-right:10px;width: 200px;'>	
		  <?php 
		   if($limit+20 <= $totrec) 
			  $value = ($limit+1) .'-'. ($limit+20);
			else
			  $value = ($limit+1) .'-'. ($totrec);
					
			?>
			<?php echo "<div class='fr' style='margin: -2px 39px 8px 0px;'> Record: ". $value .' of <b>' . $totrec ."</b>"; ?> 
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
			<input type='submit' name='Submit' value='Submit' style='margin-left: 82.3%;width:80px;margin-top: -16px;' class='Button_Under_Report'>
			<input type='submit' name='Reset' value='Reset'   style='margin-left: 5px;width:80px;margin-top: -16px;' class='Button_Under_Report'>
			<label style="font-size:11px;color:#555;margin-top:-25px; margin-left:5px;"><b>Note:</b> Date format should be <b>YYYY-MM-DD</b> (Ex. 2014-12-31)</label>
			<div style='font-weight: bold;margin-left: 3px;margin-bottom: -11px;'>
			 <?php echo $quarterStr; ?>
			</div>
			<div style='font-weight:bold;'>
			 </div>
			 <?php echo "<input type='hidden' name='order' id='order' value='$order'>"; 
			  echo "<input type='hidden' name='sort' id='sort' value='n'>";
			 ?>
			 </form>
		<?php 
			echo $table;
		?>
		</div>
		
	</div>
	   
	<div class="clear"></div>
</div>
		
<div id="dialog-form" title="Logs" style='display:none;'>
	<div id="List_of_Items"></div>
</div>

<style>
td { border-color:#AAA6A6; padding: 2px 0px 0px 1px; }
th { border-color:#AAA6A6 }
table { border-color:#AAA6A6 }
</style>
<script>
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
</script>