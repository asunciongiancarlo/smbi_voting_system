<div class="content" style="width: 112%;margin-left: -6%;">
	
	<div class="title-content">
		<h2>Voters Summary</h2>
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
		//print_r($POST);
		?>
		  <form name="SMBi2" id="statusTable" style="margin: 10px 10px 10px 10px;" action='<?php echo HTTP_PATH."report/voters_summary" ?>' method="POST"> 
		  <input type="checkbox" name="iLike" value="iLike" class="fl" style="margin-right:5px;" <?php if(isset($iLike)) echo "checked"; ?> > <label class="fl" style="margin-right:20px;"><b>iLike Campaign</b></label>
		  <input type="checkbox" name="iWant" value="iWant" class="fl" style="margin-right:5px;" <?php if(isset($iWant)) echo "checked"; ?> > <label class="fl"><b>iWant Campaign</b></label>
		  <div style="clear:both;"> </div>
		  <?php 
			//echo $val1;
			$opt = array(
						array('val'=>'fname',   		'label'=>'First Name'),
						array('val'=>'lname',   		'label'=>'Last Name'),
						array('val'=>'gender',  		'label'=>'Gender'),
						array('val'=>'email',  		    'label'=>'Email'),
						array('val'=>'department',      'label'=>'Department'),
						array('val'=>'year_of_birth',   'label'=>'Year of Birth'),
						array('val'=>'age',   		    'label'=>'Age'),
						array('val'=>'campaignName',    'label'=>'Campaign Name'),
						array('val'=>'countryName',     'label'=>'Country'));
			
			$cond = array(
						array('val'=>'equal',  		'label'=>'Equal ='),
						array('val'=>'containing',  'label'=>'Containing'),
						array('val'=>'in',  		'label'=>'IN(...)'),
						array('val'=>'between',  		'label'=>'Between'),
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
			echo "<select name='operator' style='margin-right:10px;'>";
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
		
		  		
		  
		  <input type='submit' name='Submit' value='Submit' style='width:80px; margin-left: 31.2%;' class='Button_Under_Report'>
		  <input type='submit' name='Reset' value='Reset' style='margin-left: 5px;width:80px;' class='Button_Under_Report'>
		  <?php echo "<input type='hidden' name='order' id='order' value='$order'>"; 
			  echo "<input type='hidden' name='sort' id='sort' value='n'>";
			?>
			
			<?php echo "<div class='fr' style='margin: 5px 6px 0px 0px;'> Record: ". $value .' of <b>' . $totrec ."</b>"; ?> 
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
		<label style="font-size:11px;color:#555; margin-top: 17px;"><b>Note:</b> Date format should be <b>YYYY-MM-DD</b> (Ex. 2014-12-31)</label>
		</form> 
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
  function sortBy(val)
  {
	document.getElementById('order').value = val;
	document.getElementById('sort').value  = 'y';
	$('#statusTable').submit();
  }
   function viewDialog(ctype,cID,vID)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/vote_items/'+ctype+'/'+cID+'/'+vID,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>

<style>
td { border-color:#AAA6A6 }
th { border-color:#AAA6A6 }
table { border-color:#AAA6A6 }


</style>

	

	

