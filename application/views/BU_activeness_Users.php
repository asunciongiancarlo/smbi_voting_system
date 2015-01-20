<div class="content">
	
	<div class="title-content">
		<h2>ACTIVENESS OF USERS INDEX</h2>
	</div>
	<div style="float:right;margin: 16px;">
		<a href='<?php echo HTTP_PATH ."report/downloadCSV/$csvFile"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/download-icon.png' title='Download Reports'></a>
		<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png' title='Help'></a>
	</div>
	
	<div class="clear"></div>

	<div class="working_area">
		<div class="container2">
		<?php
		 $countryID=NULL;
		 extract($POST);
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 
		 if($_SESSION['super_admin']=='y'  OR $_SESSION['countryID']==0){
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
		
		$DateFromInput = $DateFrom;
		$DateToInput   = $DateTo;
		
		$DateFromInput = ($DateFromInput=="null") ? "" : $DateFromInput;
		$DateToInput   = ($DateToInput=="null") ? "" : $DateToInput;
		//print_r($POST);
		?>

		
		  <form name="SMBi2" id="statusTable" style="margin: 10px 10px 10px 10px;" action='<?php echo HTTP_PATH."report/BU_activeness_Users" ?>' method="POST"> 
		  <?php 
			//echo "<input type='hidden' name='cID' value='$cID'>";
			//echo $val1;
			$opt = array(
						array('val'=>'pcountry',   			'label'=>'Country'),
						array('val'=>'full_name',   			'label'=>'Users'),
						array('val'=>'Uploaded_Items',  	'label'=>'Uploaded Items'),
						array('val'=>'Publish',  			'label'=>'Publish'),
						array('val'=>'Disapprove',  			'label'=>'Disapprove'),
						array('val'=>'Not_Yet_Publish',  	'label'=>'For Approval'),
						array('val'=>'AVG_Local_Price',  	'label'=>'AVG. Local Price'),
						array('val'=>'AVG_USD_Price',  		'label'=>'AVG. USD Price'));
			
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
			
			
			echo "<select name='opt1' style='margin-right:10px;width: 162px;'>";
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $l  = $o['label'];
			 $s  = ($opt1==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<select name='cond1' style='margin-right:10px;width: 200px;'>";
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
			echo "<select name='operator' style='margin-right:10px;width:67px;'>";
			foreach($operators as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($operator==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "Uploaded From: <input type='text' name='DateFrom' value='$DateFromInput'  id='datepicker'  style='width:85px;margin-right:10px;'>";
			echo "Uploaded To: <input type='text' name='DateTo'     value='$DateToInput'    id='datepicker2' style='width:85px;'>";
			
			echo "<div class='cl'></div>";
			
			echo "<select name='opt2' style='margin-right:10px;width: 162px;'>";
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $l  = $o['label'];
			 $s  = ($opt2==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<select name='cond2' style='margin-right:10px;width: 200px;'>";
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
			<input type='submit' name='Submit' value='Submit' style='margin-left: 28%;width:80px;' class='Button_Under_Report'>
			<input type='submit' name='Reset' value='Reset' style='margin-left: 5px;width:80px;' class='Button_Under_Report'>
			<div style='font-weight:bold;'>
			 <?php echo $quarterStr; ?>
			 </div>
		</form>  
		<label style="font-size:11px;color:#555; margin-left:10px;"><b>Note:</b> Date format should be <b>YYYY-MM-DD</b> (Ex. 2014-12-31)</label>

			   
			   <div style='clear:both'></div>
			   <?php 
				//print_r($reports);
				?>
				
				<table id="large" cellpadding="0" cellspacing="0" border=1 style="width:100%;font-size:13px;" class="iLike_Result_Table tablesorter">
				<thead>
				<tr>
					<th style="width:10px;text-align:center;color:white;padding:0px" bgcolor='#bb1d1d'>   						  		   				    <b>No 		 		  </b></th> 
					<th style="width:200px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Country'>   		    <b>Country   	 	  </b></th> 
					<th style="width:200px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Country'>   		    <b>User   	 	  	  </b></th> 
					<th style="width:130px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by User'>     		    <b>Uploaded Items  	  </b></th> 					
					<th style="width:150px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Uploaded Items'>     <b>For Approval  	 	 </b></th> 
					<th style="width:150px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Uploaded Items'>     <b>Disapprove  	  		 </b></th> 
					<th style="width:80px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Uploaded Items'>      <b>Publish  	  		  </b></th> 
					<th style="width:150px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Local Price'>        <b>AVG. Local Price    	</b></th> 
					<th style="width:150px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by USD Price'>     	    <b>AVG. USD Price  	  	</b></th> 
					<th style="width:150px;text-align:center;color:white;padding:0px;cursor:pointer;" bgcolor='#bb1d1d' title='Sort by Year'>      		    <b>Action	 	 	  	</b></th> 
				</tr>
				</thead>
				<tbody>
				 <?php 
					$x = 0;	
					$y=1;
					$z=1;
					$total=0;
					$sum_local=0;
					$sum_usd=0;
					$sum_approve=0;
					$sum_disapprove=0;
					$sum_not_publish=0;
					foreach($reports as $r) { 
					extract($r);
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$total += $num_items;
					$sum_local += $localPrice;
					$sum_usd   += $usPrice;
					$sum_approve += $publish_items;
					$sum_disapprove += $disapprove_items;
					$sum_not_publish += $not_publish;
					
				 ?>
				<tr>
				  <td>												<?php echo $x     ?> 		</td>
				  <td style='text-align:left;'>	<?php echo $pcountry ?> 		</td>
				  <td style='text-align:left;'>	<?php echo $full_name ?> 		</td>
				  <td style='text-align:center;'>					<?php echo $num_items  ?> 	</td>
				 
				  <td style='text-align:center;'>					<?php echo $not_publish ?> </td>
				  <td style='text-align:center;'>					<?php echo $disapprove_items ?> </td>
				   <td style='text-align:center;'>					<?php echo $publish_items ?> </td>
				  <td style='text-align:center;'>	<?php echo number_format($localPrice, 2, '.', '')  ?> 			</td>
				  <td style='text-align:center;'>	<?php echo number_format($usPrice, 2, '.', '')  	?> 			</td>
				  <td style='text-align:center;'>
					<a href='<?php echo HTTP_PATH."report/BU_activeness_Users_details/$cID/$userID/$DateFrom/$DateTo"; ?>' style='cursor:pointer;'> View Items
				 </td>
				</tr>
				 <?php } ?>
				</tbody>
				<?php 
					if(!$reports)
						echo "<tr><td colspan='10'>No items found, please check search parameters or current quarter that has been initially set.</td></tr>"
				?>
				
				<tr>
					<td> </td>
					<td> </td>
					<td> </td>
					<td> <b><?php echo number_format($total) 		 ?> </b></td>
				    
				    <td style='text-align:center;'> <b><?php echo $sum_not_publish;   ?> </b></td>
				    <td style='text-align:center;'> <b><?php echo $sum_disapprove;   ?> </b></td>
					<td style='text-align:center;'> <b><?php echo $sum_approve; ?> </b></td>
				    <td style='text-align:center;'> <b><?php echo number_format($sum_local, 2, '.', '') ?> </b></td>
				    <td style='text-align:center;'> <b><?php echo number_format($sum_usd, 2, '.', '')   ?> </b></td>
				    <td> </td>
				</tr>
				</table>
				
				<div style='clear:both'></div>



		</div>
	</div>
	
	<div class="clear"></div>
</div>

<div id="dialog-form" title="LIST OF ITEMS" style='display:none;'>
	<div id="List_of_Items"></div>
</div>

<script>
  function switchUsers()
  {
	var cID 		= document.getElementById('countryID');
	var countryID  = cID.options[cID.selectedIndex].value;
	
	//IF VOTED
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>generate_field/Users_Field/'+countryID,
		async: false
	}).responseText;
	
	document.getElementById('user_id').innerHTML = a;
  }
	
  function viewDialog(view,cID,mID,year,uID)
  {
	$( "#dialog-form" ).dialog({modal: true,height: 500,
      width: 950});
	  
	var a = $.ajax({
		url: '<?php echo HTTP_PATH ?>report/viewItems/'+view+'/'+cID+'/'+mID+'/'+year+'/'+uID,
		async: false
	}).responseText;
	
	document.getElementById('List_of_Items').innerHTML = a;
  }
</script>



	

	

