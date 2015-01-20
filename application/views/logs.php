
 <div class="content">
		
    	<div class="title-content">
        	<h2>System Logs</h2>
        </div>
		<div style="float:right;margin: 16px;">
			<a href='<?php echo HTTP_PATH ."report/downloadCSV/$csvFile"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/download-icon.png' title='Download Reports'></a>
			<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
		</div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container">
		
			<div class="clear">&nbsp;</div>
			<form name="SMBi2" id="statusTable" action='<?php echo HTTP_PATH."report/logs" ?>' method="POST" style="margin: 0px 10px 10px 18px;"> 
	
		  <?php 
			//echo $val1;
			extract($POST);
			$opt = array(
						array('val'=>'rec_id',   		'label'=>'Rec ID'),
						array('val'=>'itemCode',  		'label'=>'Item Code'),
						array('val'=>'action',  		'label'=>'Action'),
						array('val'=>'rec_name',  		'label'=>'Record Name'),
						array('val'=>'module_name',  	'label'=>'Module'),
						array('val'=>'table_name',  	'label'=>'Table'),
						array('val'=>'ttime',  			'label'=>'Time'),
						array('val'=>'user_id',   		'label'=>'User'),
						array('val'=>'country_id',  	'label'=>'Country'));
			
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
			<input type='text' name='val1' value="<?php echo $val1; ?>" style='margin-right:10px;'>
			
			<?php
			echo "<select name='operator' style='margin-right:10px;width:150px;'>";
			foreach($operators as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($operator==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "From: <input type='text' name='DateFrom' value='$DateFrom'  id='datepicker'  style='width:85px;margin-right:10px;'>";
			echo "To: <input type='text' name='DateTo'     value='$DateTo'    id='datepicker2' style='width:85px;'>";
			
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
			
			<div class="cl"></div>	
			<input type='submit' name='Submit' value='Submit' style='margin-left: 84.3%;width:80px;margin-top: -76px;' class='Button_Under_Report'>
			<input type='submit' name='Reset' value='Reset'   style='margin-left: 5px;width:80px;margin-top: -76px;' class='Button_Under_Report'>
			
			<?php echo "<div class='fr' style='margin: -23px 6px 0px 0px;'> Record: ". $value .' of <b>' . $totrec ."</b>"; ?> 
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
			<label style="font-size:11px;color:#555;margin-top:-12px; margin-bottom:-10px;"><b>Note:</b> Date format should be <b>YYYY-MM-DD</b> (Ex. 2014-12-31)</label>
			<?php echo "<input type='hidden' name='order' id='order' value='$order'>"; 
				  echo "<input type='hidden' name='sort' id='sort' value='n'>";
			?>
			</form>
			<?php 
				echo $table;
			?>
			<div class="clear">&nbsp;</div>	
            </div>
        </div>
           
        <div class="clear"></div>
    </div>
<script>
  function sortBy(val)
  {
	document.getElementById('order').value = val;
	document.getElementById('sort').value  = 'y';
	$('#statusTable').submit();
  }

</script>
