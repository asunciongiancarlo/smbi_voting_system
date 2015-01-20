<?php 
	$CI = & get_instance();
	$CI->load->database('default');
	$POSMtype   = $CI->db->query("select * from  POSM_Type");
	
	$users = $CI->db->query("SELECT user_id, admin_users.full_name as fname 
							 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id 	
							 WHERE admin_users.countryID = $countryID GROUP BY admin_users.id");
	$users = $users->result_array();
	
	$Csql = "SELECT country.id as cID, countryName FROM country WHERE id!= 0 AND id= $countryID";
	$country = $CI->db->query($Csql);
	$country = $country->row();
	
	
	if(!isset($cName))
		$cName = "All Country";
	else
		$cName = $country->countryName;
	
	$CI2 =& get_instance();
	$CI2->load->library('forms');
	
	extract($POST);
?>
<div class="content">
	
	<div class="fl title-content">
		<h2>List of Items</h2>
	</div>
	
	<div class="clear"></div>
	
	<div class="working_area">	
		<div class="container3" style="margin-top:-10px;padding:5px;">
		<label class='lbl_title' style='margin-top:10px;'> Country: <?php echo $cName ?> | Year: <?php echo $year ?> | Month: <?php echo $month_in_word ?></label>
		<form name="SMBi2" id="statusTable" action='<?php echo HTTP_PATH."report/BU_items/$typeView/$countryID/$month/$year/$user_id" ?>' method="POST"> 
	
		  <?php 
			//echo $val1;
			$opt = array(
						array('val'=>'cName',  'label'=>'Country'),
						array('val'=>'itemCode',  'label'=>'Item Code'),
						array('val'=>'itemName',  'label'=>'Item Name'),
						array('val'=>'itemType',  'label'=>'Item Type'),
						array('val'=>'user_id',   'label'=>'Users'),
						array('val'=>'publish',   'label'=>'Publish'),
						array('val'=>'UnitPrice', 'label'=>'Local Price'),
						array('val'=>'USD_Price',  'label'=>'USD Price'),
						array('val'=>'dateAdded', 'label'=>'Date Uploaded'),
						array('val'=>'dateReleased', 'label'=>'Date Released'));
			
			$cond = array(
						array('val'=>'equal',  		'label'=>'Equal ='),
						array('val'=>'containing',  'label'=>'Containing'),
						array('val'=>'greaterThan', 'label'=>'Greater than or equal >='),
						array('val'=>'lessThan',    'label'=>'Less than or equal <='));
						
			$operators = array(
						array('val'=>' AND ',  'label'=>'AND'),
						array('val'=>' OR ',   'label'=>'OR'));
			
			
			echo "<select name='opt1' style='margin-right:10px;'>";
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
			<input type='text' name='val1' value="<?php echo str_replace('"',"",$val1); ?>" style='margin-right:10px;'>
			
			<?php
			echo "<select name='operator' style='margin-right:10px;'>";
			foreach($operators as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($operator==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<div class='cl'></div>";
			
			echo "<select name='opt2' style='margin-right:10px;'>";
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
			<input type='text' name='val2' value="<?php echo $val2; ?>" style='margin-right:10px;'>
			<?php 
		   if($limit+20 <= $totrec) 
			  $value = ($limit+1) .'-'. ($limit+20);
			else
			  $value = ($limit+1) .'-'. ($totrec);
					
			?>
			Record: <?php echo $value .' of ' . $totrec ?> 
			<select onchange="document.forms[0].submit()" name='selpage' style="font-size:12px;width:93px;"> 
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
		  		
		  
		  
		  <input type='submit' name='Submit' value='Submit' style='margin-left: 10px;'>
		  <input type='submit' name='Reset' value='Reset' style='margin-left: 5px;'>
		</form>  
		<label style="font-size:11px;color:#555;"><b>Note:</b> Date format should be <b>YYYY-MM-DD</b> (Ex. 2014-12-31)</label>
		
		<?php 
			echo $table;
		?>
		</div>
		
	</div>
	   
	<div class="clear"></div>
</div>
		
<script type="text/javascript">

</script>

<style>
td { border-color:#AAA6A6 }
th { border-color:#AAA6A6 }
table { border-color:#AAA6A6 }


</style>