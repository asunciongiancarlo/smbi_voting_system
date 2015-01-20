<div class="content">
	
	<div class="title-content">
		<h2><span style="text-transform:lowercase">e</span>CATALOGUE REPORT</h2>
	</div>
	
	<div style="float:right;margin: 16px;">
		<a href='<?php echo HTTP_PATH ."report/downloadCSV/$csvFile"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/download-icon.png' title='Download Reports'></a>
		<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
	</div>
		
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php 
		extract($POST);
		
		//print_r($POST);
		?>

		<div class='fl' style='width:100%;margin-top:5px 0px 5px 0px;'>   
	   <div style='clear:both'></div>
		<form name='BU_activeness' style="margin: 10px 10px 10px 10px;" action='<?php echo HTTP_PATH ."report/eCatalogue_index" ?>' method='post'>
			<?php 
			$opt = array(
						array('val'=>'ecName',   		'label'=>'Catalogue'),
						array('val'=>'uploaded',  		'label'=>'Uploaded Items'),
						array('val'=>'avg_local',  		'label'=>'Avg. Local Price'),
						array('val'=>'avg_usd',  		'label'=>'Avg. USD Price')
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
			
			
			echo "<select name='opt1' style='margin-right:10px;width: 150px;'>";
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
			
			echo "<select name='opt2' style='margin-right:10px;width: 150px;'>";
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

			</div>
			<div class="cl"></div>
			<div style='font-weight: bold;margin-left: 12px;margin-bottom: -20px;'>
			 <?php echo $quarterStr; ?>
			 </div>
			<input type='submit' name='Submit' value='Submit' style='margin-left: 84.2%;width:80px;margin-top: -75px;' class='Button_Under_Report'>
			<input type='submit' name='Reset' value='Reset' style='margin-left: 5px;width:80px;margin-top: -75px;' class='Button_Under_Report'>
			<div style='clear:both;'></div>
		  
		</form>
		
		
		<?php 
		if($DateFrom=='' AND $DateTo==''){
			$DateFrom="null";
			$DateTo  ="null";
		}
		?>
		
		<table id="large" cellpadding="0" cellspacing="0" border=1 style="width:100%;font-size:13px; margin-bottom: 20px; border-color: #888888;" class="iLike_Result_Table tablesorter">
		<thead>
		<tr>
			<th style="width:10px;text-align:center;color:white;padding:0px" bgcolor='#bb1d1d'>   						  		   				   <b>No 		 		  </b></th> 
			<th style="width:19px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Country'>   		   <b>Cover   	 	  	  </b></th>
			<th style="width:19px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Country'>   		   <b>Name   	 	  	  </b></th> 
			<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Uploaded Items'>     <b>Uploaded Items  	  </b></th> 
			<th style="width:64px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Local Price'>        <b>AVG. Local Price    </b></th> 
			<th style="width:75px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by USD Price'>     	   <b>AVG. USD Price  	  </b></th> 
			<th style="width:50px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Year'>      		   <b>Action	 	 	  </b></th> 
		</tr>
		</thead>
		<tbody>
		 <?php 
			$x = 0;	
			$y=1;
			$z=1;
			$total=0;
			$total_target=0;
			$diff=0;
			$sum_local=0;
			$sum_usd=0;
			//print_r($reports);
			foreach($reports as $r) { 
			extract($r);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
			$total += $eCatalogueItems;
			$sum_local += $avgUnitPrice;
			$sum_usd   += $avgUSDPrice;
		 ?>
		<tr>
		  <td>												<?php echo $x     ?> 		</td>
		  <td style='text-align:center;'>					<img src='<?php echo HTTP_PATH."img/cover/".$cover ?>' width='100px;'> 					</td>
		  <td style='text-align:left;padding-left:50px;'>	<?php echo $title ?> 		</td>				  
		  <td style='text-align:center;'>					<?php echo $eCatalogueItems ?> 					</td>
		  <td style='text-align:right;padding-right:40px;'>	<?php echo number_format($avgUnitPrice, 2, '.', '')  ?> 			</td>
		  <td style='text-align:right;padding-right:40px;'>	<?php echo number_format($avgUSDPrice, 2, '.', '')  	?> 			</td>
		  <td style='text-align:center;'>					
			<a href='<?php echo HTTP_PATH."report/eCatalogue_report_details/$eID/$DateFrom/$DateTo"; ?>' style='cursor:pointer;'> View Items </a> 
		  </td>
		</tr>
		 <?php } ?>
		</tbody>
		<?php 
			if(!$reports)
				echo "<tr><td colspan='7'>No match found, please check search parameters.</td></tr>"
		?>
		
		<tr>
			<td> <b>Total</b></td>
			<td> </td>
			<td> </td>
			<td> <b><?php echo number_format($total) 		 ?> </b></td>
			<td style='text-align:right;padding-right:40px;'> <b><?php echo number_format($sum_local, 2, '.', '') ?> </b></td>
			<td style='text-align:right;padding-right:40px;'> <b><?php echo number_format($sum_usd, 2, '.', '')   ?> </b></td>
			
			<td> </td>
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

<script>  
  function viewDialog(view,cID,mID,year)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/viewItems/'+view+'/'+cID+'/'+mID+'/'+year,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>

<style>
td { border-color: #888888; }
th { border-color: #888888; }	

	
</style>
