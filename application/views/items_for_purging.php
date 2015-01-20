<div class="content" style="width: 112%;margin-left: -6%;">
	
	<div class="title-content">
		<h2> ITEMS FOR PURGING </h2>
	</div>
	
	<div style="float:right;margin: 16px;">
	
	</div>
		
		
	<div class="clear"></div>

	<div class="working_area" >
		<div class="container3" style="padding:10px;">
		<?php 
		 $countryID=NULL;
		 extract($POST);
		 $CI         = & get_instance();
		 $CI->load->database('default');
		 $CI->load->library('forms');
	
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
		   <form name="SMBi2" id="statusTable" action='<?php echo HTTP_PATH."itemDatabase/items_for_purging" ?>' method="POST"> 
		   
			<?php
				//echo "limit: ".$_SESSION['limit'];
				//MESSAGE ALERT
				if(isset($msg)){
					if(is_array($msg)){
					$CI =& get_instance();
					$CI->load->library('alert');
					echo $CI->alert->check($msg);
					}
				}
				?>
		  <?php 
			//echo $val1;
			$opt = array(
						array('val'=>'pcountry',   	 'label'=>'Country'),
						array('val'=>'itemCode',  	'label'=>'Item Code'),
						array('val'=>'itemName',  	'label'=>'Item Name'),
						array('val'=>'pstatus',  	'label'=>'Item Status'),
						array('val'=>'ptype',  		'label'=>'Item Type'),
						array('val'=>'full_name',    'label'=>'User'),
						array('val'=>'publish',   	 'label'=>'Publish'),
						array('val'=>'UnitPrice', 	 'label'=>'Local Price'),
						array('val'=>'USD_Price',  	 'label'=>'USD Price'),
						array('val'=>'dReleased',    'label'=>'Date Released'));
			$cond = array(
						array('val'=>'equal',  		'label'=>'Equal ='),
						array('val'=>'containing',  'label'=>'Containing'),
						array('val'=>'in',  		'label'=>'IN(...)'),
						array('val'=>'greaterThan', 'label'=>'Greater than or equal >='),
						array('val'=>'lessThan',    'label'=>'Less than or equal <='));
						
			$operators = array(
						array('val'=>' AND ',  'label'=>'AND'),
						array('val'=>' OR ',   'label'=>'OR'));
			
		echo "<div style='style=margin: 10px 10px 10px 10px;'>";	
			echo "<select name='opt1' style='margin-right:10px;'>";
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $l  = $o['label'];
			 $s  = ($opt1==$v OR $_SESSION['opt1']==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<select name='cond1' style='margin-right:10px;'>";
			foreach($cond as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($cond1==$v OR $_SESSION['cond1']==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			
			if(!isset($val1)) $val1='';
			if($_SESSION['val1']!='') $val1 = $_SESSION['val1'];
			?>
			<input type='text' name='val1' value="<?php echo $val1; ?>" style='margin-right:10px;'>
			
			<?php
			echo "<select name='operator' style='margin-right:12px;width:150px;'>";
			foreach($operators as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($operator==$v OR $_SESSION['operator']==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			if($_SESSION['DateFrom']!='') $DateFrom = $_SESSION['DateFrom'];
			if($_SESSION['DateTo']!='')   $DateTo   = $_SESSION['DateTo'];
			echo "Uploaded From: <input type='text' name='DateFrom' value='$DateFrom'  id='datepicker'  style='width:85px;margin-right:10px;'>";
			echo "Uploaded To: <input type='text' name='DateTo'     value='$DateTo'    id='datepicker2' style='width:85px;'>";
			
			echo "<div class='cl'></div>";
			
			echo "<select name='opt2' style='margin-right:10px;'>";
			foreach($opt as $o) 
			{ 
			 $v  = $o['val'];
			 $l  = $o['label'];
			 $s  = ($opt2==$v OR $_SESSION['opt2']==$v) ? 'selected' : '';
			 echo "<option value='$v' $s> $l </option>";   
			}
			echo "</select>";
			
			echo "<select name='cond2' style='margin-right:10px;'>";
			foreach($cond as $o) 
			{ 
			 $v  = $o['val']; 
			 $l  = $o['label'];
			 $s  = ($cond2==$v OR $_SESSION['cond2']==$v) ? 'selected' : '';
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
			<label style="font-size:11px;color:#555;margin-top:-37px;"><b>Note:</b> Date format should be <b>YYYY-MM-DD</b> (Ex. 2014-12-31)</label>
			<div style='font-weight:bold;'>
			 <?php echo $quarterStr; ?>
			 </div>
		  
		
		<?php echo "<input type='hidden' name='order' id='order' value='$order'>"; 
			  echo "<input type='hidden' name='sort' id='sort' value='n'>";
		?>
	    <div style='clear:both;height:10px;'></div>
		<?php 		
		if(!empty($table) & $_SESSION['super_admin']!='y'){
			if($DELETE OR $RESTORE) echo "<label style='margin: -16px 0 -10px 12px;'> <input type='checkbox' onclick='checkedAll()' style='vertical-align:top;color:#555555'> SELECT ALL </label>";
		
		if($DELETE){ ?>
		<p style="font-weight:bold;cursor:pointer;font-size:12px;margin:10px 0 5px 10px;width:180px;background:#370909;color:white;" onclick="deleteSelectedItem()" class="buttonWithRadius fl">  
			<img src="<?php echo HTTP_PATH ."img/delete.png"?>" style="margin-right:10px;">DELETE PERMANENTLY 
		</p>
		<p style="font-weight:bold;cursor:pointer;font-size:12px;margin:10px 0 5px 10px;width:270px;background:#370909;color:white;" onclick="saveToDiskMultipleItem()" class="buttonWithRadius fl">  
			<img src="<?php echo HTTP_PATH ."img/delete.png"?>" style="margin-right:10px;">SAVE TO DISK & DELETE PERMANENTLY  
		</p>
		<?php } if($RESTORE){ ?>
		<p style="font-weight:bold;cursor:pointer;font-size:12px;margin:10px 0 5px 10px;width:192px;background:#370909;color:white;" onclick="restoreSelectedItem()" class="buttonWithRadius fl">  
			<img src="<?php echo HTTP_PATH ."img/restore.png"?>" style="margin-right:10px;">RESTORE SELECTED ITEMS 
		</p>
		<?php } 
		} ?>
	    </div>
	    <?php 
			echo $table;
		?>
		<input type='hidden' name='msg' value='' id='toHiddenMsg'>
		<input type='hidden' name='singleVal' value='' id='singleVal'>
		<?php 		
		if(!empty($table) & $_SESSION['super_admin']!='y'){
			if($DELETE OR $RESTORE) echo "<label style='margin:2px 0 0 10px;'> </label>";
		if($DELETE){ ?>
		<p style="font-weight:bold;cursor:pointer;font-size:12px;margin:10px 0 5px 10px;width:180px;background:#370909;color:white;" onclick="deleteSelectedItem()" class="buttonWithRadius fl">  
			<img src="<?php echo HTTP_PATH ."img/delete.png"?>" style="margin-right:10px;">DELETE PERMANENTLY  
		</p>
		<p style="font-weight:bold;cursor:pointer;font-size:12px;margin:10px 0 5px 10px;width:270px;background:#370909;color:white;" onclick="saveToDiskMultipleItem()" class="buttonWithRadius fl">  
			<img src="<?php echo HTTP_PATH ."img/delete.png"?>" style="margin-right:10px;">SAVE TO DISK & DELETE PERMANENTLY 
		</p>
		<?php } if($RESTORE){ ?>
		<p style="font-weight:bold;cursor:pointer;font-size:12px;margin:10px 0 5px 10px;width:192px;background:#370909;color:white;" onclick="restoreSelectedItem()" class="buttonWithRadius fl">  
			<img src="<?php echo HTTP_PATH ."img/restore.png"?>" style="margin-right:10px;">RESTORE SELECTED ITEMS 
		</p>
		<?php } 
		} ?>
		</form>
		<div style='clear:both'></div>
		
		</div>
	</div>
	<div class="clear"></div>
</div>

<div id="dialog-form" title="Confirm Item Restore" style='display:none;'>
	<textarea name='notes' id='fromHiddenMsg'></textarea>
	<label class="grayLabel"> *Please add a note that will be send to Business Unit.</label>
	<input type="button" name="Submit" value="Submit" onclick="itmRestoreSubmit()">
	<input type="button" name="Cancel" value="Cancel" onclick="closeDialogForm()">
</div>

<script>
  function viewDialog()
  {
	$( "#dialog-form" ).dialog({modal: true,height: 200,
	width: 400});
  }
  
  function closeDialogForm()
  {
  $('#dialog-form').dialog('close');
  }
	
  function sortBy(val)
  {
	document.getElementById('order').value = val;
	document.getElementById('sort').value  = 'y';
	$('#statusTable').submit();
  }
  
  checked=false;
  function checkedAll (frm1) {
	document.getElementById('singleVal').value="";
	var aa= document.getElementById('statusTable');
	if (checked == false)
	{
	   checked = true
	}
	else
	{
	  checked = false
	}
	for (var i =0; i < aa.elements.length; i++) 
	{
		aa.elements[i].checked = checked;
	}
  }
  
  function clearSingleId()
  {
  document.getElementById('singleVal').value="";
  }
  
  function deleteOneItem(id)
  {	
	jConfirm("Permanently delete this item?","Alert",function(r){
		if(r){ 
		window.location = "<?php echo HTTP_PATH ?>itemDatabase/items_for_purgingActions/deleteOneItem/"+ id;
		}
	});
  }
  
  function saveToDiskOneItem(id)
  {
	jConfirm("Download it's resources & Permanently delete this item?","Alert",function(r){
		if(r){ 
			window.location = "<?php echo HTTP_PATH ?>delete_purge/saveToDisk/saveOneItem/"+ id; 
			setTimeout('window.location = "<?php echo HTTP_PATH.'itemDatabase/items_for_purging/item_has_been_save_and_downloaded' ?>";', 2000);   
		}
	});
  }
  
  function saveToDiskMultipleItem(id)
  {
	jConfirm("Download items resources & Permanently delete these items?","Alert",function(r){
		if(r){
		 document.getElementById("statusTable").action = "<?php echo HTTP_PATH ?>delete_purge/saveToDisk/saveMultipleItem";
		 document.getElementById("statusTable").submit();
		 setTimeout('window.location = "<?php echo HTTP_PATH.'itemDatabase/items_for_purging/items_has_been_save_and_downloaded' ?>";', 2000); 
		}
	});
  }
  
  
  function itmRestoreSubmit()
  {
   //SINGLE VAL
   var singleVal = document.getElementById('singleVal').value;
   if(singleVal!='')
   {
    document.getElementById("toHiddenMsg").value = document.getElementById("fromHiddenMsg").value;
    document.getElementById("statusTable").action = "<?php echo HTTP_PATH ?>itemDatabase/items_for_purgingActions/restoreOneItem";
	document.getElementById("statusTable").submit();
   }
   else
   {
    document.getElementById("toHiddenMsg").value = document.getElementById("fromHiddenMsg").value;
    document.getElementById("statusTable").action = "<?php echo HTTP_PATH ?>itemDatabase/items_for_purgingActions/restoreSelectedItem";
	document.getElementById("statusTable").submit();
   }
  }
  
  function restoreOneItem(id)
  {
    viewDialog();
	document.getElementById('singleVal').value = id;
  }
  
  function restoreSelectedItem()
  {
	viewDialog();
	/*
	jConfirm("Are you sure you want to restore these Items?","Alert",function(r){
		if(r){ 
		document.getElementById("statusTable").action = "<?php echo HTTP_PATH ?>itemDatabase/items_for_purgingActions/restoreSelectedItem";
		document.getElementById("statusTable").submit();
		}
	});*/
 }
 
 
  
  function deleteSelectedItem()
  {
	
	jConfirm("Are you sure you want to delete permanently these items?","Alert",function(r){
		if(r){ 
		document.getElementById("statusTable").action = "<?php echo HTTP_PATH ?>itemDatabase/items_for_purgingActions/deleteSelectedItem";
		document.getElementById("statusTable").submit();
		}
	});
  }
  
  
  
  
</script>

<style>
td { border-color:#AAA6A6; padding: 2px 0px 0px 1px; }
th { border-color:#AAA6A6 }
table { border-color:#AAA6A6 }
</style>

	

	

